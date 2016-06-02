<?php
/** Classe importer
* @package ATF
*/
class importer extends classes_optima {
	/*---------------------------*/
	/*      Constructeurs        */
	/*---------------------------*/	
	public function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
        $this->colonnes['fields_column'] = array(
            'importer.id_user'
            ,'importer.importer'
            ,'importer.id_module'
            ,'importer.date'=>array("width"=>100,"align"=>"center")
            ,'importer.etat'=>array("width"=>30,"align"=>"center","renderer"=>"etat")
        );
        
        $this->colonnes['primary'] = array(
            'id_user'
            ,'importer'
            ,'id_module'=>array('id'=>'id_module')
            ,'separateur'
            ,'date'
            ,'options'
        );
		
        $this->colonnes['bloquees']['insert'] =  array('id_user','nb','date','mapping',"etat","date_import","extension",'complement','erreur','filename','lignes_inserer','lignes_ignore','lignes_update');
        $this->colonnes['bloquees']['select'] =  array('importer','mapping','separateur','complement','option','erreur','etat','filename','lignes_inserer','lignes_ignore','lignes_update');
		$this->colonnes['bloquees']['filtre']['donnee'] =  array('mapping','complement');
		$this->colonnes['bloquees']['filtre']['colonne'] =  array('mapping','complement');
		
        $this->colonnes['panel']['champs_sup'] = array(
            "champs_sup"=>array("custom"=>true)
        );
        $this->panels["champs_sup"] = array("visible"=>true,"collapsible"=>true,"hideOnSelect"=>true);
        
		$this->colonnes['panel']['statistique'] = array(
            "nb",
            "date_import",
		);
		$this->panels["statistique"] = array();
		$this->panels["primary"] = array("nbCols"=>2);
		
		$this->no_update=true;

        $this->files["fichier"]=array("type"=>"csv","collapsed"=>false);
		$this->files["rapport_de_simulation"]=array("type"=>"xls","collapsed"=>true);
		$this->fieldstructure();
		$this->addPrivilege("chargerFichier","insert");
        $this->addPrivilege("recupDonnees","insert");
        $this->addPrivilege("restart","insert");
        $this->addPrivilege("cancel","insert");
	}
	
	/** Permet de charger le contenu du fichier pour afficher un échantillon
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array infos : contient toutes les données du formulaires
	*/
	public function chargerFichier($infos,&$s,$files=NULL,&$cadre_refreshed){
		// Ici il y avait 'fichier' au lieu de '$infos['key']', ce qui est un peu con vu qu'on passe le nom du champ en tant que key
		$path=$this->filepath(ATF::$usr->getID(),($infos['key']?$infos['key']:'fichier'),true);
		// Trouver ce que ce truc test, chemin complet du fichier ? nom réel du fichier ? etc etc...
		if ($infos['importer']['filestoattach']['fichier']) {
			$fn = $infos['importer']['filestoattach']['fichier'];
		} elseif ($files[$infos['key']]['name']) {
			$fn = $files[$infos['key']]['name'];
		}
		
		$path_parts = pathinfo($fn);
		if($path_parts['extension']=="xls"){
			//on ecrase le ficher temp pour avoir un fichier csv
			file_put_contents($path,`xls2csv -d utf-8 $path`);
		}
		
		if ($f = fopen ($path,"r")) {
			while ($data = fgetcsv($f, 0, $infos['importer']['separateur'])) {	
				if (!current($data)) continue;
				if (count($data)==1) continue;				
				$informations["nb"]++;	
				//contenus
				foreach ($data as $key =>$item) {
					//condition pour recup les titres
					if ($informations["nb"]==1 && $item) {
						$informations["csv"][$key]["title"] = $item;
					//condition pour recup le contenu	
					}  elseif ($item && count($informations["csv"][$key]["samples"])<3) {
						$informations["csv"][$key]["samples"][] = $item;
					}
				}
			}
		}
		foreach ($informations["csv"] as $k => $i) {
			$informations["csv"][$k]["samples"] = implode(", ",$i["samples"])."...";
		}
		ATF::$html->assign("donnees",$infos);
		ATF::$html->assign("filename",$fn);

		ATF::$html->assign("mapping",$informations["csv"]);
		ATF::$html->assign("nom_module",$infos[$this->table]['id_module']);	
		$class = ATF::getClass($infos[$this->table]['id_module']);
		if ($infos['IdNotFiltered']) {
			$cols = $class->table_structure();
		} else {
			$cols = $class->table_structure("id_".$infos[$this->table]['id_module']);
		}
		ATF::$html->assign("colonnes",$cols);	
	}
	
	/** Surcharge de l'insert pour vérifier que le mapping est effectué et pour dispatcher les valeurs à insérer
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed) {
		$insert['importer']=array(
            'id_user'=>ATF::$usr->getId()	
            ,'importer'=>$infos['importer']['importer']
            ,'separateur'=>$infos['importer']['separateur']
            ,'id_module'=>ATF::module()->from_nom($infos['importer']['id_module'])
            ,'mapping'=>serialize($infos['liaison'])
            ,'filename'=>$infos['nom_fichier']
            ,'options'=>$infos['importer']['options']
            ,'filestoattach'=>array('fichier'=>$infos['nom_fichier'])
        );
        
        
		//traitement de champs_sup, pour retirer les préfixes, decrypter les ids éventuels ...
		if($infos['champs_sup']){
			$this->checkChampsSup($infos['champs_sup']);
			$insert['importer']['complement']=serialize($infos['champs_sup']);
		}

		//check du mapping : il faut qu'il y ait tous les champs obligatoires (sans valeur par défaut) de sélectionner pour valider l'import
		$check=$this->checkMapping($infos['liaison'],$infos['importer']['id_module'],$infos['champs_sup']);
//log::logger($insert,ygautheron);
//log::logger($files,ygautheron);
		if($check===true){			
			$id_element=parent::insert($insert,$s,$files);
			
			$path=$this->filepath($id_element,"fichier");
			$taille=explode("/",`du -bs $path`);
			$size=trim($taille[0]);

			//si le fichier (csv) a une taille inférieur à 100Ko, on l'upload de suite
			if($size<100000 || $infos["preview"]){
				$this->q->reset()->where("id_importer",$id_element);
				$retour=$this->importMassif($infos["preview"]);
				if($retour===true || $retour==2){
					ATF::$msg->addNotice("L'import a été enregistré et appliqué");
				}else{
					throw new errorATF($retour);
				}
			}else{
				ATF::$msg->addNotice("L'import a été enregistré et sera appliqué dans quelques minutes");
			}

			if(is_numeric($infos['champs_sup']['id_societe'])){
				ATF::societe()->redirection('select',ATF::societe()->cryptId($infos['champs_sup']['id_societe']));
			}elseif($infos['importer']['id_module']=="emailing_contact" && is_numeric($infos['champs_sup']['id_emailing_source'])){
				ATF::emailing_source()->redirection('select',ATF::emailing_source()->cryptId($infos['champs_sup']['id_emailing_source']));
			}else{
				$this->redirection("select_all");
			}
			return $id_element;
		}else{
			throw new errorATF("Attention, tous les champs obligatoires du module sélectionné n'ont pas été choisi dans le mapping : ".$check);
		}
	}

	/** Permet de vérifier que les champs obligatoires du module sélectionnés ont été sélectionné au niveau du mapping
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array mapping : tableau du mapping fait par le user
	* @param string module : nom du module sélectionné
	* @return boolean : vrai si les champs obligatoires sont sélectionnés, faux dans le cas contraire
	*/
	public function checkMapping($mapping,$module,$complement=NULL){
		$mapping_inverse=array_flip($mapping);

		//suppression du prefixe du nom du module sur le mapping
		$this->supPrefixe($mapping_inverse);

		foreach(ATF::getClass($module)->desc as $champs=>$infos){
			//check des types du champs
			if($infos['Null']=="NO" && !$infos['Default'] && $infos['Default']!='0' && $champs!="id_".$module){
				//si il fait parti du mapping, c'est bon, sinon on doit envoyer une erreur
				//sauf dans le cas où l'on provient d'un formulaire, dans lequel on a rempli les infos obligatoires
				if(!isset($mapping_inverse[$champs]) && (!$complement || ($complement && !isset($complement[$champs])))){
					$return[]=ATF::$usr->trans($champs,$module);
				}
			}
		}
		$return=implode(",",$return);
		return $return?$return:true;
	}

    /** Execute la mise à jour ou insertion en conservant un rapport livrable en Excel
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    **/
    function insertAndReport($mod,$row_data,$preview,&$worksheetATF,$row_erreur){
        return $this->processAndReport($mod,$row_data,$preview,$worksheetATF,$row_erreur,"INSERT");
    }
    function updateAndReport($mod,$row_data,$preview,&$worksheetATF,$row_erreur){
        return $this->processAndReport($mod,$row_data,$preview,$worksheetATF,$row_erreur,"UPDATE");
    }
    function deleteAndReport($mod,$row_data,$preview,&$worksheetATF,$row_erreur){
        return $this->processAndReport($mod,$row_data,$preview,$worksheetATF,$row_erreur,"DELETE");
    }
    function processAndReport($mod,$row_data,$preview,&$worksheetATF,$row_erreur,$action){
        $method = strtolower($action);
        $pk = $row_data["id_".$mod]; // Si on l'a (pour le delete)

        if ($preview) {
            $col = 65;
            foreach ($row_data as $k=>$i) {
                if ($k == "id_".$mod) continue;
                $worksheetATF->write(chr($col).$this->report_row, $i);
                $col++;
            }

            if ($row_erreur) {
                $worksheetATF->write(chr($col).$this->report_row, $action." ERROR (".$row_erreur.")");
            } else {
                // On tente de mettre à jour
                try {
                    ATF::getClass($mod)->$method($method=="delete" ? $row_data["id_".$mod] : $row_data);
                    $worksheetATF->write(chr($col).$this->report_row, $action." OK");
                } catch (Exception $e) {
                    $worksheetATF->write(chr($col).$this->report_row, $action." ERROR (".$e->getMessage().")");
                }
            }

            $this->report_row++;
        } else {
            if ($row_erreur) {
                throw new errorATF($row_erreur);
            }

            ATF::getClass($mod)->$method($method=="delete" ? $pk : $row_data);
        }
    }

    /** Organise le tableau d'import avec les enfants d'abord, en vue de prévenir la suppression de parents avant d'enfants
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    **/
    public function enfantAvantParents($mod,&$data){
        $keys = array_flip(ATF::getClass($mod)->foreign_key);
        $cle_parent = $keys[$mod];

        // Détermination du niveau arborescent
        $niveaux = array();
        foreach($data as $k => $row) {
            $niveau = 0;
            $id = $row[$cle_parent];
            while ($id = ATF::getClass($mod)->select($id,$cle_parent)) {
                $niveau++;
            }
            $niveaux[] = $niveau;
        }

        array_multisort($niveaux, SORT_DESC, $data);
    }

	/** Permet l'importation des imports en attente (un par un)
    * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
    * @author Quentin JANON <qjanon@absystech.fr>
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    **/
	public function importMassif($preview){
		ATF::db($this->db)->begin_transaction();
		$this->q->reset()->where("etat","en_attente")->setLimit(1)->setDimension("row_arro");
		$infos=$this->select_all();

		if($infos){
            $complement=unserialize($infos['complement']);

			try{
                $mod = ATF::module()->select($infos['id_module'],'module');
    			$liaison=unserialize($infos['mapping']);
                foreach ($liaison as $k=>$i) {
                    if (!$i) {
                        unset($liaison[$k]);
                        continue;  
                    }
                    $liaison[$k] = str_replace($mod.".","",$i);
                }

                if ($infos['options']=="delete") {
                    // En mode delete, on supprime tout sauf ce qu'il y a dans le fichier fourni
                    ATF::getClass($mod)->q->reset();
                    if($infos['complement']){
                        $complement=unserialize($infos['complement']);
                        // On recherche l'element existant avec les même clé étrangère supplémentaire.
                        foreach($complement as $champs=>$valeur){
                            ATF::getClass($mod)->q->where($champs,$valeur);
                        }
                    }
                    if ($resultat = ATF::getClass($mod)->select_all()) {
                        foreach ($resultat as $item) {
                            $toDelete[$item["id_".$mod]]=$item;
                        }
                    }
                }

                if ($preview) {
                    // Génération du fichier excel de rapport
                    require_once __ABSOLUTE_PATH__."libs/ATF/libs/PHPExcel/Classes/PHPExcel.php";
                    require_once __ABSOLUTE_PATH__."libs/ATF/libs/PHPExcel/Classes/PHPExcel/Writer/Excel5.php";
                    $fname = $this->filepath(ATF::$usr->getID(),'rapport_de_simulation',true);
                    $workbook = new PHPExcel;
                    $worksheetATF = new PHPEXCEL_ATF($workbook,0);
                    $worksheetATF->sheet->setTitle('Données');

                    //$style_titre1 = new excel_style();
                    //$style_titre1->setWrap()->alignement('center')->setSize(13)->setBorder("thin")->setBold();

                    $col = 64;
                    foreach ($liaison as $k=>$i) {
                        $col++;
                        $worksheetATF->write(chr($col).'1', $i/*, $style_titre1->getStyle()*/);
                    }
                    if($infos['complement']){
                        $complement=unserialize($infos['complement']);
                        // On recherche l'element existant avec les même clé étrangère supplémentaire.
                        foreach($complement as $champs=>$valeur){
                            $col++;
                            $worksheetATF->write(chr($col).'1', $champs);
                        }
                    }
                    $this->report_row = 2;
                    //$worksheetATF->sheet->getColumnDimension('A')->setWidth("10");
                }

    			$path=$this->filepath($infos['id_importer'],"fichier");
                $totalLigne = count(file($path));
    			$row=0;
    			if ($f = fopen ($path,"r")) {
    				while ($data = fgetcsv($f, 0, $infos['separateur'])) {
    				    $row++;
    					if (count($data)==1 || $row == 1){
                            $totalLigne--;
                            continue;
                        }
                        $row_erreur = "";

                        foreach ($data as $k=>$i) {

                            if (!$liaison[$k]) {
                            	unset($data[$k]);
                            	continue;
                            }

							if (method_exists(ATF::getClass($mod),'getImportMapping')) {
								if ($data[$k] && ($realField = ATF::getClass($mod)->getImportMapping($liaison[$k]))) {
                                    if (is_numeric($data[$k])) {
                                        $row_erreur .= $mod.".id_".$mod." == ".$data[$k];
                                        ATF::getClass($mod)->q->reset()->where($mod.".id_".$mod,$data[$k])->addField($mod.".id_".$mod)->SetStrict();
                                    } else {
                                        ATF::getClass($mod)->q->reset()->where($mod.'.'.$realField,$data[$k])->addField($mod.".id_".$mod)->SetStrict();
                                    }
                                    if($result = ATF::getClass($mod)->select_cell()){
                                        $data[$k] = $result;
                                    } else {
                                        $row_erreur .= "La clé choisie '".$data[$k]."' pour ".$liaison[$k]." (clé primaire réelle ou ".$realField." attendu) n'existe pas. ";
                                    }
                                }

							} elseif(preg_match("`id_`",$liaison[$k])){
                                if ($table=$this->fk_from($liaison[$k])) {
                                    $class = ATF::getClass($table);
                                    $champ_unique = $class->champs_unique ? $class->champs_unique : "id_".$table;
                                    $class->q->reset()->where($table.'.'.$champ_unique,$data[$k])->addField($table.".id_".$table)->SetStrict();

                                    // Ajout du complément d'information si besoin et si la colonne existe dans le module $table
                                    if($complement){
                                        foreach($complement as $champs => $valeur){
                                            if (in_array($class->desc,$champs)) {
                                                $class->q->where($table.'.'.$champs,$valeur);
                                            }
                                        }
                                    }

                                    if($result=$class->select_cell()){
                                        $data[$k] = $result;
                                    } else {
                                        $row_erreur .= "La clé choisie '".$data[$k]."' pour ".$liaison[$k]." (".$champ_unique." attendu) n'existe pas. ";
                                    }
                                }
                            }
                        }

                        $row_data = array_combine($liaison,$data);

                        // Ajout du complément d'information si besoin
                        if($complement){
                            foreach($complement as $champs => $valeur){
                                if(!$row_data[$champs]){
                                    $row_data[$champs]=$valeur;
                                }
                            }
                        }

                        if (ATF::getClass($mod)->champs_unique && !$row_data[ATF::getClass($mod)->champs_unique]) {
                            throw new errorATF("Il manque les données pour le champ obligatoire : ".$mod." > '".ATF::getClass($mod)->champs_unique."' sur la ligne ".$row." de votre fichier. (".print_r($row_data,true).")",1474);
                        }

                        if ($infos['options']=="delete") {
                           // En mode delete, on supprime tout sauf ce qu'il y a dans le fichier fourni
                            ATF::getClass($mod)->q->reset()->where(ATF::getClass($mod)->champs_unique,$row_data[ATF::getClass($mod)->champs_unique]);
                            if($infos['complement']){
                                $complement=unserialize($infos['complement']);
                                // On recherche l'element existant avec les même clé étrangère supplémentaire.
                                foreach($complement as $champs=>$valeur){
                                    ATF::getClass($mod)->q->where($champs,$valeur);
                                }
                            }
                            if ($line = ATF::getClass($mod)->select_row()) {
                                if (isset($toDelete[$line["id_".$mod]])) { // Si on a fourni la ligne, on l'enlève de ce qui est prévu d'être supprimé !
                                    unset($toDelete[$line["id_".$mod]]);
                                }
                            }
                        } elseif ($infos['options']=="update") {
                            if ($dedoublonnement[$row_data[ATF::getClass($mod)->champs_unique]]) {
                                $row_erreur .= "La clé unique '".$row_data[ATF::getClass($mod)->champs_unique]."' a déjà été sollicitée dans ce fichier. ";
                            }
                            ATF::getClass($mod)->q->reset()->where(ATF::getClass($mod)->champs_unique,$row_data[ATF::getClass($mod)->champs_unique]);
                            if($infos['complement']){
                                $complement=unserialize($infos['complement']);
                                // On recherche l'element existant avec les même clé étrangère supplémentaire.
                                foreach($complement as $champs=>$valeur){
                                    ATF::getClass($mod)->q->where($champs,$valeur);
                                }
                            }
                            if ($line = ATF::getClass($mod)->select_row()) {
                                $row_data["id_".$mod] = $line["id_".$mod];
                                $this->updateAndReport($mod,$row_data,$preview,$worksheetATF,$row_erreur);
                               // Peut être qu'l faudra prévoir d'échaper les champs qui n'ont pas de valeur pour éviter de les écraser avec une valeur NULL
                            } else {
                                $this->insertAndReport($mod,$row_data,$preview,$worksheetATF,$row_erreur);
                            }
                            $dedoublonnement[$row_data[ATF::getClass($mod)->champs_unique]]=true;
                        } else {
                            if (ATF::getClass($mod)->champs_unique) {
                                // S'il y a un champ unique on est obligé d'insérer au fur et à mesure pour convertir la clé unique en clé primaire...
                                $this->insertAndReport($mod,$row_data,$preview,$worksheetATF,$row_erreur);
                            } else {
                                $toInsert[] = $row_data;
                            }
                        }
    				}
    			}

				if($preview) {
                    if ($toInsert){
                        foreach ($toInsert as $row_data) {
                            $this->insertAndReport($mod,$row_data,$preview,$worksheetATF);
                        }
                    }
                    if ($toDelete){
                        if (in_array($mod,ATF::getClass($mod)->foreign_key)) {
                            $this->enfantAvantParents($mod,$toDelete);
                        }
                        foreach ($toDelete as $row_data) {
                            $this->deleteAndReport($mod,$row_data,$preview,$worksheetATF);
                        }
                    }

                } else {
				    if ($infos['options']=="ignore") {
                        if ($toInsert) {
                            $r = ATF::getClass($mod)->multi_insert($toInsert,false,$infos['options']);
                        }
                    } elseif ($infos['options']=="update") {
                        if (!empty($toInsert)) {
                            $r = ATF::getClass($mod)->multi_insert($toInsert,false,"ignore"); // Peu importe qu'on mette le ignore ici, normallement les lignes a update sont identifié plus haut dans le traitement.
                        }
                    } elseif ($infos['options']=="delete") {
                        foreach ($toDelete as $row_data) {
                            $this->deleteAndReport($mod,$row_data,$preview,$worksheetATF);
                        }
                    }
				}

				$u = array(
				    'id_importer'=>$infos['id_importer'],
                    'etat'=>'fini',
                    'lignes_inserer'=>$r['Records']-$r['Duplicates'],
                    'lignes_ignore'=>$r['Duplicates'],
                    'lignes_update'=>$r['Updated'],
				    'nb'=>$totalLigne,
				    "date_import"=>date("Y-m-d H:i:s")
                );

				$this->update($u);

                if ($preview) {
                    $writer = new PHPExcel_Writer_Excel5($workbook);
                    $writer->save($fname);
                    PHPExcel_Calculation::getInstance()->__destruct();
                    ATF::db($this->db)->rollback_transaction();
                    ATF::getClass($mod)->d($infos['id_importer']);
                    return true;
                } else {
                    ATF::db($this->db)->commit_transaction();
                }

                return true;
			}catch(errorATF $e){
				$this->update(array('id_importer'=>$infos['id_importer'],'etat'=>'probleme',"date_import"=>date("Y-m-d H:i:s"),"erreur"=>$e->getMessage()));
				return $e->getMessage();
			}

		}else{
			return 2;
		}
	}

	/** Permet de récupérer les données du formulaire d'où l'on pourait provenir avant d'être sur celui d'import
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function recupDonnees($infos,&$s,$files=NULL,&$cadre_refreshed){
		$provenance=$infos['provenance'];

		ATF::$provenance()->infoCollapse($infos);

		//suppression des valeurs nulles
		foreach($infos as $key=>$item){
			if($item){
				$donnees[$key]=$item;
			}
		}
		
		ATF::$cr->add("main","generic-update_ext.tpl.htm",array("table"=>"importer","event"=>"insert",'provenance'=>$provenance,"champs_complementaire"=>$donnees));
		ATF::$cr->setUrl("importer.html,event=insert");
		
		return true;
	}
	
	/** Permet de supprimer les préfixes des clés du tableau
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array $tableau : tableau dont on modifie les clés
	*/
	public function supPrefixe(&$tableau){
		$tab=$tableau;
		foreach($tab as $champs=>$value){
			unset($tab[$champs]);
			$new_champs=explode('.',$champs);
			//si il n'y a pas de préfix, c'est qu'il s'agit d'un custom, donc on ne sauvegarde pas
			if($new_champs[1]){
				$tab[$new_champs[1]]=$value;
			}
		}
		$tableau=$tab;
	}
	
	/** traitement de champs_sup, pour retirer les préfixes, decrypter les ids éventuels ...
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array $donnees
	*/
	public function checkChampsSup(&$donnees){
		//suppression du prefixe
		$this->supPrefixe($donnees);
		
		//decrypter les ids éventuels ...
		foreach($donnees as $champs=>$valeur){
			if(substr($champs,0,3)=="id_"){
				$classe=substr($champs,3);
				if($classe=="owner" || $classe=="superieur"){
					$classe="user";
				}elseif($classe=="filiale"){
					$classe="societe";
				}

				$valeur=ATF::$classe()->decryptId($valeur);
			}
			$donnees[$champs]=$valeur;
		}
	}
    
    /** Renvoi les liaisons formatté pour le XTemplate qui les affiches
    * @author Quentin JANON <qjanon@absystech.fr>
    * @param string $serialize
    */
    public function getLiaisonForXTemplate($serialize) {
        $val = unserialize($serialize);
        foreach ($val as $k=>$i) {
            if (ATF::getClass($this->fk_from($k)) && $i) {
                $key_class=ATF::getClass($this->fk_from($k));
                //$r[] = array("name"=>$k,"val"=>$key_class->nom($i),"table"=>$key_class->name(),"id"=>$i);
                $r .= '{ name:"'.$k.'",val:"'.$key_class->nom($i).'",tableName:"'.ATF::$usr->trans($key_class->name(),'module').'",table:"'.$key_class->name().'",id:"'.$i.'",link:"'.$key_class->name().'-select-'.$key_class->cryptId($i).'.html" },';
            }
        }
        return "[".substr($r,0,-1)."]";
    }   
    
    /** Renvoi le mapping formatté pour le XTemplate qui les affiches
    * @author Quentin JANON <qjanon@absystech.fr>
    * @param string $serialize
    */
    public function getMappingForXTemplate($serialize) {
        $val = unserialize($serialize);
        foreach ($val as $k=>$i) {
            $r .= '{ colXLS:"'.$k.'",col:"'.$i.'" },';
        }
        return "[".substr($r,0,-1)."]";
    }    
    
    /** Remet un import en état 'en_cours'
    * @author Quentin JANON <qjanon@absystech.fr>
    */
    public function restart($infos,&$s,$files=NULL,&$cadre_refreshed) {
        if ($infos['id_importer']) {
            $u = array("id_importer"=>$this->decryptId($infos['id_importer']),"etat"=>"en_attente");
            parent::update($u);
            //$infos['display'] = true;
            ATF::$msg->addNotice("L'import a été remis en attente, il sera éxecuté dans quelques instants.");
            $this->redirection("select",$infos['id_importer']);
            return true;
        }
    }

    /** Annule un import
    * @author Quentin JANON <qjanon@absystech.fr>
    */
    public function cancel($infos,&$s,$files=NULL,&$cadre_refreshed) {
        if ($infos['id_importer']) {
            $u = array("id_importer"=>$this->decryptId($infos['id_importer']),"etat"=>"annule");
            parent::update($u);
            //$infos['display'] = true;
            ATF::$msg->addNotice("L'import a été annulé avec succès");
            $this->redirection("select",$infos['id_importer']);
            return true;
        }
    }
};
?>