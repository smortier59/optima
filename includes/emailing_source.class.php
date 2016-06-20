<?
/** 
* Classe emailing_source, gère les source pour les contacts
* @package Optima
* @author Quentin JANON <qjanon@absystech.fr>
*/
class emailing_source extends emailing {
	function __construct() { // PHP5
		parent::__construct();
		$this->table = __CLASS__;
		$this->colonnes['fields_column'] = array(
			'emailing_source.emailing_source'
			,'emailing_source.date'=>array("width"=>100,"align"=>"center")
			,'nbContacts'=>array("width"=>120,"custom"=>true,"aggregate"=>array("min","avg","max","sum"),"align"=>"center","type"=>"decimal")
		);
		
		$this->fieldstructure();
		$this->onglets = array('emailing_contact'=>array('opened'=>true));
//		$this->addPrivilege("iFromPlugin","insert");
		$this->addPrivilege("majListContact","update");
		$this->helpMeURL = "http://wiki.optima.absystech.net/index.php/Source_de_contact";
	}
	
	/**
	* Surcharge du select-All
	*/
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){
		$this->q
			->addJointure("emailing_source","id_emailing_source","emailing_contact","id_emailing_source")
			->addField("COUNT(emailing_contact.id_emailing_contact)","nbContacts")
			->addGroup("emailing_source.id_emailing_source");
		$return = parent::select_all($order_by,$asc,$page,$count);
		return $return;
	}
	
	/** 
	* Met a jour les contacts d'une source !
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
	public function majListContact($infos,&$s,$files=NULL,&$cadre_refreshed) {
		//Initialisation des variables
		$numUpdateOK = $numUpdateNOK = $numInsertOK = $numInsertNOK = $numEnrTraite = 0;
		
		
		// Verification de la présence du fichier temporaire
		$path=ATF::importer()->filepath(ATF::$usr->getID(),"fichier",true);
		if (!file_exists($path)) {
			throw new errorATF(ATF::$usr->trans("fichier_temp_manquant",$this->table),1000);
		} else {
			$r = `xls2csv -d utf-8 $path 2>/dev/null`;
			$path = str_replace(".xls",".csv",$path);
			//on ecrase le ficher temp pour avoir un fichier csv
			file_put_contents($path,$r);
		}
		
		// Récupération des données du fichier temporaire
		$f = fopen($path,"r");
		
		// Vérification des colonnes
		$cols = fgetcsv($f, 1, ",");
		$keysID = array_search("id_emailing_contact",$cols);
		$tableCols = ATF::emailing_contact()->table_structure(false,false,true);
		
		foreach ($cols as $k=>$i) {
		    if (!$i) continue;
			if (ereg("id_",$i) || ereg("_fk",$i)) continue;
			if (!in_array($i,$tableCols)) {
				if ($champsInconnu!="") $champsInconnu .= ", ";
                $i = str_replace("–","-",$i);
                $i = str_replace("\n","",$i);
                
				$champsInconnu .= $i;
			}
		}

		if (!$champsInconnu) {
			$lineCompteur = 1;
			while (($data = fgetcsv($f, 10000, ",")) !== FALSE) {
				
				$lineCompteur++;
				unset($enr);
				foreach ($data as $k=>$i) {
					if ($i && $i!="") {
						//Préparation du tableau pour l'insert ou l'update
                        $i = str_replace("–","-",$i);
                        $i = str_replace("\n","",$i);
                        $enr[$cols[$k]] = (string)$i;
					}
				}
				// Si pas de valeur, c'est une ligne vide, donc on la saute.
				if (!$enr) continue;
				$numEnrTraite++;
				// Update ou insert ? basé sur l'id_contact qui est dans la cellule 10 du tableau
				if (ATF::emailing_contact()->select($data[$keysID])) {
					// Traitement de l'update
					try {
						$r = ATF::emailing_contact()->u($enr);
						$numUpdateOK+=$r;
					} catch (errorATF $e) {
						$msg = $e->getMessage();
						if (ereg("generic message : ",$msg)) {
							$tmp = json_decode(str_replace("generic message : ","",$msg),true);
							$msg = $tmp['text'];
						}
                        $erreurs[$e->getErrno()." - ".$msg] .= $lineCompteur.", ";
                        
						$numUpdateNOK++;
					}
				} else {
					// Traitement de l'insert
					try {
						$r = ATF::emailing_contact()->i($enr);
						$numInsert++;
					} catch (errorATF $e) {
						$msg = $e->getMessage();
                        
						if (ereg("generic message : ",$msg)) {
							$tmp = json_decode(str_replace("generic message : ","",$msg),true);
							$msg = $tmp['text'];
						}

                        if ($e->getErrno()==1062) {
                            $erreurs[$e->getErrno()." - Enregistrement(s) déjà existant(s)"] .= $lineCompteur.", ";
                        } else {
                            $erreurs[$e->getErrno()." - ".$msg] .= $lineCompteur.", ";
                        }
						$numInsertNOK++;
					}
				}
			}
		}
		fclose($handle);		
		
        foreach ($erreurs as $err=>$lignes) {
            $tampon .= "<hr>Erreur ".$err." pour les lignes : ".$lignes;
        }
        
		ATF::$html->assign("numEnrTraite",$numEnrTraite);
		ATF::$html->assign("numUpdateOK",$numUpdateOK);
		ATF::$html->assign("numUpdateNOK",$numUpdateNOK);
		ATF::$html->assign("numInsertOK",$numInsertOK);
		ATF::$html->assign("numInsertNOK",$numInsertNOK);
		ATF::$html->assign("erreurs",addslashes($tampon));
		ATF::$html->assign("champsInconnu",$champsInconnu);
	}
	
};
?>