<?
/**
* Classe Visite
*
*
* @date 2009-10-31
* @package inventaire
* @version 1.0.0
* @author QJ <qjanon@absystech.fr>
* @author Yann GAUTHERON <ygautheron@absystech.fr>
*
*/ 
class visite extends classes_optima {
	/**
	* Constructeur
	*/
	public function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		
		$this->colonnes['fields_column'] = array(
			'visite.visite'
			,'visite.id_gep_projet'
			,'visite.date'
			,'visite.date_visite'
			,'nbReponses'=>array("custom"=>true,"nosort"=>true)
			,'nbPhotos'=>array("custom"=>true,"nosort"=>true)
			,'coutTotal'=>array("custom"=>true,"nosort"=>true)
			,'etat' 
//			,'pdf'=>array("custom"=>true,"nosort"=>true)
		);

		$this->colonnes['primary'] = array(
			 "id_gep_projet"
			,'visite'
			,'reference_site'
			,'adresse'
			,'cp'
			,'ville'
			,'date_visite'
			,'personne_accompagnante'
			,'fonction'
			,'tel_pa'
			,'tel_ig'
			,'etat'
		);

		$this->colonnes['bloquees']['select'] = 	array(	
			"visite"
		);
		$this->colonnes['bloquees']['insert'] = 	array(	
			"date"
		);
		$this->colonnes['bloquees']['update'] = 	array(	
			"date"
		);
		
		$this->fieldstructure();
		
		$this->privilege_egal["formulaire"]="update";
		$this->quick_action['select'][] = 'formulaire';
		
		$this->files = array("photo"=>array("type"=>"png","convert_from"=>array("jpg","png","gif"),"select"=>true));
		$this->gmap = true;
		$this->onglets = array('ged');
		
		// Si il s'agit des profils clients, on unset l'onglet des synthese visite.
		
		$this->addPrivilege("changeEtat","update"); 
		$this->addPrivilege("exportCout");
		$this->addPrivilege("autocompleteVisiteGe");
		$this->addPrivilege("autocompleteVisiteAcc");
		$this->addPrivilege("exportCarnetSante");
		$this->addPrivilege("exportSyntheseDiagAcc");
		$this->addPrivilege("exportCarnetSanteRoubaix");
		$this->addPrivilege("exportFiche");
	}
	
	/**
    * Listing avec droits particuliers
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @todo Remplacer les paramètres de select_all par un &$s
    */ 
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){
//		if (!ATF::$usr->get("id_agence")) {
//			// Sinon restriction par societe
//			$this->q->addJointure("visite","id_gep_projet","gep_projet","id_gep_projet");
//			$this->q->addCondition("gep_projet.id_societe",ATF::$usr->get("id_societe"));
//		}
		return parent::select_all($order_by,$asc,$page,$count);	
	}
	
	/**
    * Retourne vrai si le rapport du projet est accessibilité
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $id id_visite
    */ 
	public function estRapportAccessibilite($id){
		return ATF::gep_projet()->select($this->select($id,'id_gep_projet'),'rapport')=="accessibilite";
	}
	
	/**
    * Met à jour la date de la visite
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $id id_visite
    */ 
	public function majDate($id){
		return $this->update(array("id_visite"=>$id,"date"=>date("Y-m-d H:i:s")));
	}
	
	/** 
	* Ajout de champs custom
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $infos l'identificateur de l'élément que l'on désire exporter
	* @param array &$s La session
	*/
	public function export_vue($infos,&$s){
		$this->q->reset();
		$this->setQuerier($s["pager"]->create($infos['onglet'])); // Recuperer le querier actuel
		$this->q->setLimit(-1);
		if($infos = $this->select_all()) {
			foreach ($infos["data"] as $k => $i) {
				$infos["data"][$k]["nbReponses"]=ATF::vi_pa()->getNbReponses($i["visite.id_visite"]);
				$infos["data"][$k]["nbPhotos"]=ATF::vi_pa()->getNbReponses($i["visite.id_visite"],'photo');
				$infos["data"][$k]["coutTotal"]=$this->coutTotal($i["visite.id_visite"],true);
			}
		}
		$this->export($infos['data'],$s);
	}
	
	/** 
	* Ajout de champs custom
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $infos l'identificateur de l'élément que l'on désire exporter
	* @param array &$s La session
	*/
	public function export_total($infos,&$s){
		$this->setQuerier($s["pager"]->create($infos['onglet']));
		//on retiens le where dans le cas d'un onglet pour filtrer les donnéees
		$this->q->addAllFields($this->table)->setLimit(-1)->unsetCount();
		if($infos = parent::select_all()) {
			foreach ($infos["data"] as $k => $i) {
				$infos["data"][$k]["nbReponses"]=ATF::vi_pa()->getNbReponses($i["visite.id_visite"]);
				$infos["data"][$k]["nbPhotos"]=ATF::vi_pa()->getNbReponses($i["visite.id_visite"],'photo');
				$infos["data"][$k]["coutTotal"]=$this->coutTotal($i["visite.id_visite"],true);
			}
		}
		$this->export($infos,$s);
	}
	
	/**  Sortir un excel qui regroupe les coûts par élément de référence
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array $infos : contient le nom de l'onglet, la liste des visites éventuellement cochées
	*/
	public function exportCout($infos,&$s){
		//on récupère toutes les visites sur l'onglet
		$this->setQuerier($s["pager"]->create($infos['onglet']));
		$this->q->addAllFields($this->table)->setLimit(-1)->unsetCount();
		
		//sauf si on en coche
		if($infos['id']){
			foreach($infos['id'] as $cle=>$valeur){
				$this->q->addCondition('id_visite',$this->decryptId($valeur));
			}
		}
		
		$listeVisites = parent::select_all();

		foreach($listeVisites as $key=>$item){
			//on récupère les desordres (seuls éléments qui ont des coûts)
			foreach(ATF::vi_pa()->getDesordres($item['visite.id_gep_projet_fk'],$item['visite.id_visite_fk']) as $k=>$i){
				$info_ref=ATF::pa()->getReferenceElement($i['id_pa']);
				
				if($info_ref['id_pa']){
					$current = ATF::pa()->select($i['id_pa']);
					$currentVIPA = ATF::vi_pa()->isAnswered($item['visite.id_visite_fk'],$current['id_attr'],$current['id_pa'],$current['id_pa'],$i['id_vi_pa_multi']);
					
					if (ATF::pa()->hasCout($current['id_pa']) || ATF::pa()->getRegle($current['id_pa']) && $currentVIPA['id_vi_pa']) {
						$cout=ATF::vi_pa()->getCost($currentVIPA['id_vi_pa']);
						if($cout){	
							$tableau[$item['visite.id_visite_fk']][$info_ref['id_pa']]+=$cout;
							$col[$info_ref['id_pa']]=0;
							if(!$noms[$info_ref['id_pa']])$noms[$info_ref['id_pa']]=ATF::pa()->getLibelle($info_ref);
							$cout=0;
						}
					}
				}
			}
		}		

		$this->expo($tableau,$col,$noms);		
	}
	
	/** fonction qui va permettre de télécharger des fichiers
	* @author : Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array $infos liste de couts
	* @param array col liste des colonnes
	* @param array noms liste des noms des éléments de référence
	*/
	public function expo($infos,$col,$noms){
		require_once __ABSOLUTE_PATH__."libs/ATF/libs/php_writeexcel/class.writeexcel_workbook.inc.php";
		require_once __ABSOLUTE_PATH__."libs/ATF/libs/php_writeexcel/class.writeexcel_worksheet.inc.php";
		
		$fname = tempnam(__TEMPORARY_PATH__, __TEMPLATE__.ATF::$usr->getID());
		$workbook = new writeexcel_workbook($fname);
		$worksheet =& $workbook->addworksheet('Export Optima');

		if ($infos) {
			// mise en place des noms de champs
			$row_data=array();
			$row_data[]="";
			foreach ($col as $key => $item) {
				$row_data[] =$noms[$key];
			}
			$format = $workbook->addFormat(array('bg_color'=>'green'));
			$worksheet->write_row("A1", array_map("utf8_decode",$row_data),$format);
			$row++;

			//et ensuite les données
			foreach ($infos as $k => $i) {
				$row++;
				$row_data=array();
				$row_data[]=ATF::visite()->select($k,'visite');
				foreach ($col as $k_ => $i_) {
					$row_data[]=($i[$k_]?$i[$k_]:'-');
					if($i[$k_]){
						$col[$k_]+=$i[$k_];
					}
				}
				$worksheet->write_row("A".$row, array_map("utf8_decode",$row_data));
			}
			
			//total
			$row++;
			$row_data=array();
			$row_data[]="Total";
			foreach ($col as $k_ => $i_) {
				$row_data[]=($i_?$i_:'-');
			}
			$worksheet->write_row("A".$row, array_map("utf8_decode",$row_data));
		}			

		$workbook->close();
		header("Content-Type: application/x-msexcel; name=".str_replace(" ","_",ATF::$usr->trans($this->table,"module")).".xls");
		header("Content-Disposition: attachment; filename=".str_replace(" ","_",ATF::$usr->trans($this->table,"module")).".xls");
		header("Cache-Control: private");
		$fh=fopen($fname, "rb");
		fpassthru($fh);
		unlink($fname);
		die();
	}
	
	/**
    * Coût 
	* @author Yann GAUTHERON <ygautheron@absystech.fr>, Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param int $id id_visite
	* @param boolean $force Forcer le calul, meme si trop gourmand
	* @param int Cout total
    */ 
	public function coutTotal($id_visite,$force=false) {
		$visite = $this->select($id_visite);
		
		//pour le projet 11.204, on récupère le cout différemment
		if($visite["id_gep_projet"]==89){
			$cout=$this->recupTotal($visite);
		}else{
			$cout="(cf.Excel)";
			switch (ATF::gep_projet()->select($visite["id_gep_projet"],"rapport")) {
				case "accessibilite":
					if ($force) {
						$cout=0;
						foreach (ATF::vi_pa()->selectAccessibiliteBatiments($id_visite) as $batiment) {
							foreach (ATF::pa()->selectAccessibiliteElementsReferences($visite["id_gep_projet"]) as $element) {
								foreach (ATF::vi_pa()->selectAccessibiliteElements($id_visite,$element["id_pa"],$batiment["id_vi_pa_multi"]) as $elementOccurence) {
									$constats = ATF::vi_pa()->getConstatsAccessibilite($id_visite,$element["id_pa"],$elementOccurence["id_vi_pa_multi"]);
									$cout += ATF::vi_pa()->getConstatsAccessibiliteTotal($constats,NULL);
								}
							}
						}
					}
					break;
					
				case "gros_entretien":
					$cout=0;
					foreach (ATF::vi_pa()->getDesordres($visite["id_gep_projet"],$id_visite) as $c=>$o) {	
						$current = ATF::pa()->select($o['id_pa']);
						//try{
							$currentVIPA = ATF::vi_pa()->isAnswered($id_visite,$current['id_attr'],$current['id_pa'],$current['id_pa'],$o['id_vi_pa_multi']);
						//} catch(errorATF $e) { }
						
						//Récupération du coût associé s'il existe
						if (ATF::pa()->hasCout($current['id_pa']) || ATF::pa()->getRegle($current['id_pa']) && $currentVIPA['id_vi_pa']) {
							$cout += ATF::vi_pa()->getCost($currentVIPA['id_vi_pa']);
						}
					}
					break;
				
				default:
					
			}
		}
		return $cout;
	}
	
	/** Récupération du cout total (somme des prévisions affichés dans l'onglet grille de l'export PGR)
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function recupTotal($visite,$tab_cout=false){
		$elements=ATF::pa()->isPA(4300,$visite['id_gep_projet']);
		$enfants = ATF::pa()->selectChilds($elements['id_pa']);
		if (!$enfants) {
			// Enfants dans les ATTR
			$enfants = ATF::attr()->selectChilds($elements['id_attr']);
		}
		if($enfants){
			$tab_ligne=$nombre_ligne;
			
			$ordre_etat=0;
			foreach($enfants as $key=>$el_enf){
				//on ne prends que les parties qui possèdent des enfants
				if($el_enf['id_pa']){
					$enfants2 = ATF::pa()->selectChilds($el_enf['id_pa']);
					if (!$enfants2) {
						// Enfants dans les ATTR
						$enfants2 = ATF::attr()->selectChilds($el_enf['id_attr']);
					}

					foreach ($enfants2 as $k=>$i) {
						if($i['id_pa']){
							$enfants3 = ATF::pa()->selectChilds($i['id_pa']);
							if (!$enfants3) {
								// Enfants dans les ATTR
								$enfants3 = ATF::attr()->selectChilds($i['id_attr']);
							}

							foreach ($enfants3 as $ke=>$it) {
								//pour la partie grille
								if($it['id_attr']=="4309"){
									if($it['id_pa']){
										$multi = ATF::vi_pa()->getDistinct($visite['id_visite'],$it['id_attr'],$it['id_pa']);		
										$enfants4 = ATF::pa()->selectChilds($it['id_pa']);
										if (!$enfants4) {
											$enfants4 = ATF::attr()->selectChilds($it['id_attr']);
										}
										foreach($multi as $cle_multi=>$item_multi){
											foreach ($enfants4 as $k4=>$i4) {
												$i4['id_vi_pa_multi']=$item_multi['id_vi_pa_multi'];
												if($i4['id_attr']=="4316"){
													if($i4['id_pa']){
														$vi_pa = ATF::vi_pa()->getEnumReponse($i4,$visite['id_visite']);
														$reponse = ATF::pa()->getLibelle($vi_pa);
														$getcout=ATF::vi_pa()->isAnswered($visite['id_visite'],$it['id_attr'],$it['id_pa'],$it['id_pa'],$i4['id_vi_pa_multi']);
														if($tab_cout){
															$cout[$reponse]+=ATF::vi_pa()->getCost($getcout['id_vi_pa'],false);
														}else{
															if($reponse!=2022)$cout+=ATF::vi_pa()->getCost($getcout['id_vi_pa'],false);
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}	
		}
		return $cout;
	}
	
	/**
    * Changement de l'état d'une visite (en_cours, fini ou à relire) 
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param array $infos deux champs : id_visite et etat (en_cours, a_relire ou fini)
    */ 
	public function changeEtat($infos){
		$this->infoCollapse($infos);
		//decryptage de L'id !
		$id_visite=$this->decryptId($infos["id_visite"]);
		//Maj en bdd
		$this->update(array("id_visite"=>$id_visite,"etat"=>$infos["etat"]));
		//Refresh de la fiche
		ATF::$cr->block("top");
		ATF::$cr->block("generationTime");
		$this->redirection("select",$id_visite);
		ATF::$cr->addVar("main",array("id_visite"=>$id_visite));
	}
	
	/** 
	* Surcharge pour modifier les societes des ged de la visite
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function update($infos,&$s,$files=NULL,&$cadre_refreshed){
		$this->infoCollapse($infos);
		$this->q->reset()
				->addField('visite.id_gep_projet','projet')	
				->addField('gep_projet.id_societe','societe')
				->setStrict()
				->addCondition('id_visite',$this->decryptId($infos['id_visite']))
				->addJointure('visite','id_gep_projet','gep_projet','id_gep_projet')
				->setDimension('row_arro');
				
		$InfoGepProjetAncien=parent::select_all();

		$return=parent::update($infos,$s,$files,$cadre_refreshed);

		//on regarde si le projet a changé
		return $return;
	}
	
	/**
	* Donne le type de rapport de la visiste
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param int $id_visite
	*/
	public function getRapport($id_visite){
		$this->q->reset()
			->addField("gep_projet.rapport")
			->addJointure("gep_projet","id_gep_projet","visite","id_visite")
			->setDimension("cell");
		return $this->sa(); 
	}
	
	/**
	* Donne l'avancement en fonction du type de rapport et de la visite
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param int $id_visite
	* @return float le pourcentage p de la visite avec 0<=p<=1
	*/
	public function getAvancement($id_visite){
		$id_visite=$this->decryptId($id_visite);
		$etat=$this->select($id_visite,"etat");
		$rapport=$this->getRapport($id_visite);
		$nbVisistes=ATF::vi_pa()->getNbReponses($id_visite);
		if($etat=="en_cours"){
			switch($rapport){
				case "gros_entretien":
					if($nbVisistes<=100){
						return 0.25;
					}elseif($nbVisistes<=200){
						return 0.50;
					}elseif($nbVisistes<=300){
						return 0.75;
					}else{
						return 1;
					}
					break;
				case "accessibilite":
					if($nbVisistes<=100){
						return 0.25;
					}elseif($nbVisistes<=200){
						return 0.50;
					}elseif($nbVisistes<=300){
						return 0.75;
					}else{
						return 1;
					}
					break;
				case "energie":
					if($nbVisistes<=100){
						return 0.25;
					}elseif($nbVisistes<=200){
						return 0.50;
					}elseif($nbVisistes<=300){
						return 0.75;
					}else{
						return 1;
					}
					break;
			}
		}else{
			return 1;
		}
	}
	
	/**
    * Renvoi la liste complète des bâtiments du projet
    * @author QJ <qjanon@absystech.fr>
    * @param int $id Id du projet
    * @param int $idBat ID de l'attr BATIMENT : 48 par défaut
    * @return array liste des bâtiments et leur infos vi_pa
    */   
	function getAllBatiment($id,$idBat=48,$field="id_gep_projet",$id_attr_parent=false) {
		if (!$id) return false;
		/* SQL
			SELECT * FROM (
				SELECT `vi_pa`.* FROM `visite` 
				LEFT JOIN `vi_pa` ON vi_pa.id_visite=visite.id_visite
				WHERE `visite`.`id_gep_projet`=55 AND `id_attr`=48
				ORDER BY `date` DESC
			) AS q2
			GROUP BY id_vi_pa_multi		*/
		$this->q->reset()
					->addField("vi_pa.id_visite","id_visite")
					->addField("vi_pa.id_vi_pa","id_vi_pa")
					->addField("vi_pa.id_pa","id_pa")
					->addField("vi_pa.date","date")
					->addField("vi_pa.id_attr","id_attr")
					->addField("vi_pa.id_ppa","id_ppa")
					->addField("vi_pa.reponse","reponse")
					->addField("vi_pa.id_vi_pa_multi","id_vi_pa_multi")
					->setRefTable('visite',false)
					->setAlias('V')
					->addJointure("V","id_visite","vi_pa","id_visite")
					->addCondition($field,$this->decryptId($id))
					->addCondition("id_attr",$idBat)
					->addOrder("vi_pa.date","desc")
					->setStrict()
					->setToString();
		$subQuery = ATF::db($this->db)->select_all($this);
		$this->q->reset()->addGroup("id_vi_pa_multi")->setSubQuery($subQuery,'q1');
		if ($id_attr_parent) {
			$r = ATF::db($this->db)->select_all($this);
			foreach ($r as $k=>$i) {
				if (!$i['id_pa']) continue;
				$pa_parent = ATF::pa()->select(ATF::pa()->select($i['id_pa'],'id_parent'));
				if ($pa_parent['id_attr']==$id_attr_parent) {
					$return[] = $i;
				}
			}
		} else {
			$return = ATF::db($this->db)->select_all($this);
		}
		return $return;
	}	
	
	/** Autocomplete des visites de gros entretien sur formulaire d'import de synthese visite
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function autocompleteVisiteGe($infos,$reset=true){
		if ($reset) {
			$this->q->reset();
		}
		$this->q->addJointure('visite','id_gep_projet','gep_projet','id_gep_projet')->where("gep_projet.rapport","gros_entretien");
		return parent::autocomplete($infos,false);
	}
	
	/** Autocomplete des visites d'accessibilité et gros entretien sur formulaire d'import de synthese visite
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function autocompleteVisiteAcc($infos,$reset=true){
		if ($reset) {
			$this->q->reset();
		}
		$this->q->addJointure('visite','id_gep_projet','gep_projet','id_gep_projet')/*->where("gep_projet.rapport","accessibilite")*/;
		return parent::autocomplete($infos,false);
	}
	
	/** Export des carnets de santé, avec un onglet par bâtiment
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function exportCarnetSante($infos){
		require_once __ABSOLUTE_PATH__."libs/ATF/libs/php_writeexcel/class.writeexcel_workbook.inc.php";
		require_once __ABSOLUTE_PATH__."libs/ATF/libs/php_writeexcel/class.writeexcel_worksheet.inc.php";

		$fname = tempnam(__TEMPORARY_PATH__, __TEMPLATE__.ATF::$usr->getID());

		$workbook = new writeexcel_workbook($fname);
		ATF::export_cs()->ongletCS($workbook,$infos['id_visite']);

		$workbook->close();
		
		$visite=$this->select($infos['id_visite'],'visite');
		header("Content-Type: application/x-msexcel; name=".str_replace(" ","_",$visite)."-carnet_de_sante-".date("YmdHis").".xls");
		header("Content-Disposition: attachment; filename=".str_replace(" ","_",$visite)."-carnet_de_sante-".date("YmdHis").".xls");
		header("Cache-Control: private");
		$fh=fopen($fname, "rb");
		fpassthru($fh);
		unlink($fname);

	}
	
	/** Permet l'export du fichier de désordres constatés
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array $infos : contient les id_visite de ge acc et nrj
	*/
	public function exportSyntheseDiagAcc($infos){
		session_write_close();
	
		require_once __ABSOLUTE_PATH__."libs/ATF/libs/php_writeexcel/class.writeexcel_workbook.inc.php";
		require_once __ABSOLUTE_PATH__."libs/ATF/libs/php_writeexcel/class.writeexcel_worksheet.inc.php";
		
		$fname = tempnam(__TEMPORARY_PATH__, __TEMPLATE__.ATF::$usr->getID());

		$workbook = new writeexcel_workbook($fname);
		
		//récupération des informations concernant la visite
		$donnees=$this->select($infos['id_visite']);

		/********* Premier onglet *********/
		$worksheet =& $workbook->addworksheet(utf8_decode('Synthèse du diagnostic'));
		//en mode paysage et réglage de la zone d'impression
		$worksheet->set_landscape();
		$worksheet->set_margins(0.2);
		$worksheet->set_print_scale(48);
		ATF::export_diagnostic()->ongletDiag($workbook,$worksheet,$donnees);
		/**********************************/
		
		$workbook->close();
		
		header("Content-Type: application/x-msexcel; name=".str_replace(" ","_",$donnees['visite'])."-synthèse_diagnostic_acc.xls");
		header("Content-Disposition: attachment; filename=".str_replace(" ","_",$donnees['visite'])."-synthèse_diagnostic_acc.xls");;
		header("Cache-Control: private");
		$fh=fopen($fname, "rb");
		fpassthru($fh);
		unlink($fname);
	}
	
	/** Permet l'export du fichier de carnet de santé du projet Roubaix
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array $infos : contient les id_visite de ge acc et nrj
	*/
	public function exportCarnetSanteRoubaix($infos){
		session_write_close();
	
		require_once __ABSOLUTE_PATH__."libs/ATF/libs/php_writeexcel/class.writeexcel_workbook.inc.php";
		require_once __ABSOLUTE_PATH__."libs/ATF/libs/php_writeexcel/class.writeexcel_worksheet.inc.php";
		
		$fname = tempnam(__TEMPORARY_PATH__, __TEMPLATE__.ATF::$usr->getID());

		$workbook = new writeexcel_workbook($fname);
		
		//récupération des informations concernant la visite
		$donnees=$this->select($infos['id_visite']);

		ATF::export_cs_roubaix()->ongletCS($workbook,$infos['id_visite']);
		
		$workbook->close();
		
		header("Content-Type: application/x-msexcel; name=".str_replace(" ","_",$donnees['visite'])."-cs_roubaix.xls");
		header("Content-Disposition: attachment; filename=".str_replace(" ","_",$donnees['visite'])."-cs_roubaix.xls");;
		header("Cache-Control: private");
		$fh=fopen($fname, "rb");
		fpassthru($fh);
		unlink($fname);
	}
	
	/** 
	* Vérifie la possibilité de supprimer un élément
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 15-04-2011
	* @return boolean
	*/
	public function can_delete($id,$infos=false){
		$visite = $this->select($id);
		if ($visite['id_gep_projet']==70 && ATF::$usr->get('id_profil')!=6) {
			throw new errorATF(ATF::$usr->trans("impossible_supprimer_sur_ce_projet",$this->table),8802);
		}
		if ($visite['id_gep_projet']==71 && ATF::$usr->get('id_profil')!=6) {
			throw new errorATF(ATF::$usr->trans("impossible_supprimer_sur_ce_projet",$this->table),8804);
		}
		return true;
	}	
	
		
	public function exportFiche($infos){
		session_write_close();

		require_once __ABSOLUTE_PATH__."libs/ATF/libs/PHPExcel/Classes/PHPExcel.php";
		require_once __ABSOLUTE_PATH__."libs/ATF/libs/PHPExcel/Classes/PHPExcel/Writer/Excel5.php";

		$fname = tempnam(__TEMPORARY_PATH__, __TEMPLATE__.ATF::$usr->getID());

		/*$objet = PHPExcel_IOFactory::createReader('Excel5');

		$workbook = $objet->load(__ABSOLUTE_PATH__."www/excel/export_fiche.xls");*/

		try{
			$workbook = new PHPExcel;

			ATF::export_fiche()->export($workbook,$infos['id_visite']);
	
			$workbook->setActiveSheetIndex(0);
	
			$writer = new PHPExcel_Writer_Excel5($workbook);
	
			$writer->save($fname);
	
			$visite=$this->select($infos['id_visite']);
			
			if ($visite['id_gep_projet']==89) {
//				$matriceAbbr = array(
//					"INVESTIPIERRE"=>"SCPI	INV",
//					"ACCIMO PIERRE"=>"SCPI ACP",
//					"IMMOBILIERE PRIVEE France PIERRE"=>"SCPI IPFP",
//					"IMMOBILIERE PRIVéE France PIERRE"=>"SCPI IPFP",
//					"IMMOBILIèRE PRIVeE France PIERRE"=>"SCPI IPFP",
//					"IMMOBILIèRE PRIVéE France PIERRE"=>"SCPI IPFP",
//					"CAPIFORCE PIERRE"=>"SCPI CP",
//					"SOPRORENTE"=>"SCPI SOP",
//					"PIERRE SELECTION"=>"SCPI PS"
//				);
				$answer = ATF::vi_pa()->isAnswered($visite['id_visite'],4357,152550,152550,NULL,NULL,true);
				$fn = $answer." - ".$visite['adresse']." ".$visite['ville']." - PGR 2011 - Visite";
//				if ($matriceAbbr[utf8_decode($answer)]) {
//					echo 'ABBR = '.$matriceAbbr[$answer];
//				}

			} else {
				$fn = $visite['visite']."-fiche-".date("YmdHis");
			}
			
			header('Content-type: application/vnd.ms-excel');
			header('Content-Disposition:inline;filename='.$fn.'.xls ');
			header("Cache-Control: private");
			$fh=fopen($fname, "rb");
			fpassthru($fh);
			unlink($fname);
		
		}catch(errorATF $e){
			print_r($e->getMessage()."<br /><br />".utf8_decode("Signalez cette erreur à votre administrateur de projet (bien préciser la visite concernée)"));
			die();
		}
	}

};
?>