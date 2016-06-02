<?
/** Classe facture
* @package Optima
* @subpackage MANALA
*/
require_once dirname(__FILE__)."/../facture.class.php";
class facture_manala extends facture {
	/**
	* Mail de facture
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
	private $facture_mail;

	/**
	* Mail de copy de facture
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
	private $facture_copy_mail;

	/**
	* Mail actuel
	* @var mixed
	*/
	private $current_mail=NULL;
	
	/**	* Constructeur par défaut
	*/ 
	public function __construct() { 
		parent::__construct();
		$this->table = "facture";
		$this->colonnes['fields_column'] = array(	
			'facture.ref'=>array("width"=>100,"align"=>"center")
			,'facture.id_societe'
			,'facture.num_commande'
			,'facture.num_fournisseur'
			,'facture.date'=>array("width"=>100,"align"=>"center")
			,'facture.etat'=>array("width"=>30,"renderer"=>"etat")
			,'facture.prix'=>array("aggregate"=>array("min","avg","max","sum"),"align"=>"right","renderer"=>"money","width"=>80)
			,'fichier_joint'=>array("custom"=>true,"nosort"=>true,"type"=>"file","align"=>"center","width"=>50)
			//,'actions'=>array("custom"=>true,"nosort"=>true,"align"=>"center","width"=>100,"renderer"=>"actionsFacture")
		);	

		$this->colonnes['primary'] = array(	 
			"id_societe"
			,"num_commande"			
			,"num_fournisseur"			
			,"date"			
			,"tva"
            ,'missions'=>array("custom"=>true)	
            ,'etat'
		);		

		$this->colonnes['panel']['detail'] = array(
			"designation"=>array("xtype"=>"textarea")
			,"prix_unitaire"					
		);

		
		// Propriété des panels
		$this->panels['primary'] = array("visible"=>true,'nbCols'=>2);
		$this->panels['detail'] = array("visible"=>true,'nbCols'=>2,"collapsible"=>false);


		// Champs masqués
		$this->colonnes['bloquees']['insert'] =  array('ref','id_user','prix','etat');	
		$this->colonnes['bloquees']['cloner'] =  array('ref','id_user','prix');	
		$this->colonnes['bloquees']['update'] =  array('ref','id_user','prix','prix_unitaire','missions');	
		
		$this->onglets = array("facture_mission");

		//IMPORTANT, complte le tableau de colonnes avec les infos MYSQL des colonnes
		$this->fieldstructure();	

		$this->files["fichier_joint"] = array("type"=>"pdf","preview"=>true,"quickMail"=>true);
		$this->field_nom = "ref";
		
		

	}
	
	/** 
	* Surcharge de l'insert afin d'insérer les lignes de factures et modifier l'état de l'affaire sur l'insert d'une facture
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	*/
	public function insert($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
 		$preview=$infos["preview"];

 		$missions = $infos['missions'];

		$this->infoCollapse($infos);
		unset($infos['id_mission']);
		if(!$infos["id_societe"]){
			throw new error("Vous devez spécifier la société (Entité)",167);
		}
		
		if(!$missions || empty($missions)){
			throw new error("Vous devez spécifier au moins une mission",168);
		}

		$infos["prix_unitaire"]=util::stringToNumber($infos["prix_unitaire"]);
		$infos["id_user"] = ATF::$usr->getID();
		$infos["ref"] = ATF::facture()->getRef($infos["date"]);

		
		ATF::db($this->db)->begin_transaction();
		// Calcul du prix total TTC		
		$infos["prix"] = $infos["prix_unitaire"]*$infos['tva'];
		//Facture
		$last_id=parent::insert($infos,$s,NULL,$var=NULL,NULL,true);
		
		foreach ($missions as $k=>$i) {
			$toI = array("id_mission"=>$i,"id_facture"=>$this->decryptId($last_id));
			ATF::facture_mission()->i($toI);
			$toU = array("id_mission"=>$i,"etat"=>"attente_paiement");
			ATF::mission()->u($toU);
		}
		
		if($preview){
			$this->move_files($last_id,$s,true,$infos["filestoattach"]); // Génération du PDF de preview
			ATF::db($this->db)->rollback_transaction();
			return $this->cryptId($last_id);
		}else{
			$this->move_files($last_id,$s,false,$infos["filestoattach"]); // Génération du PDF avec les lignes dans la base	
			ATF::db($this->db)->commit_transaction();
		}
		$this->redirection("select",$last_id);

		return $this->cryptId($last_id);
	}


	/** 
	* Surcharge de l'insert afin d'insérer les lignes de factures et modifier l'état de l'affaire sur l'insert d'une facture
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	*/
	public function update($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		$infos_ligne = json_decode($infos["values_".$this->table]["produits"],true);
 		$preview=$infos["preview"];
		$this->infoCollapse($infos);
		/*Formatage des numériques*/
		$infos["prix_unitaire"]=util::stringToNumber($infos["prix_unitaire"]);
		$infos["id_user"] = ATF::$usr->getID();

 
		ATF::db($this->db)->begin_transaction();
		//*****************************Transaction********************************
	
		//Facture			
		parent::update($infos,$s);

		//***************************************************************************************
				
		if($preview){
			$this->move_files($infos["id_facture"],$s,true,$infos["filestoattach"]); // Génération du PDF de preview
			ATF::db($this->db)->rollback_transaction();
			return $this->cryptId($infos["id_facture"]);
		}else{
			$this->move_files($infos["id_facture"],$s,false,$infos["filestoattach"]); // Génération du PDF avec les lignes dans la base

			ATF::db($this->db)->commit_transaction();
		}

		$this->redirection("select",$infos["id_facture"]);
		
		return true;
	}

	
	/** 
	* Surcharge de delete afin de supprimer les lignes de commande et modifier l'état de l'affaire et du devis
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param int $infos le ou les identificateurs de l'élément que l'on désire inséré
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	*/
	public function delete($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL) {
		if (is_numeric($infos) || is_string($infos)) {
						
			$id=$this->decryptId($infos);
			$facture=$this->select($id);
			
			ATF::db($this->db)->begin_transaction();
			//*****************************Transaction********************************
				
				//Commande
				ATF::commande_facture()->q->reset()->addCondition("id_facture",$id)->end();
				if($tab_commande=ATF::commande_facture()->select_all()){
					//Pour toutes les commandes liées à la facture
					foreach($tab_commande as $key=>$item){
						$commande["commande"]["id_commande"]=$item["id_commande"];
						$commande["commande"]["etat"]="en_cours";
						ATF::commande()->u($commande,$s);
					}
				}

				//Facture
				parent::delete($id,$s);
				
				//On recupere les factures précédente pour récuperer la date de fin la plus proche
				$this->q->reset()->where("facture.id_affaire", $facture["id_affaire"])->addField("facture.date_fin_periode");
				$factures = $this->select_all();
				
				//Si pas d'autre facture la date de fin de période devient NULL
				
				if(is_array($factures)){
					$dateFin = "";
					foreach($factures as $k=>$v){
						if($v["facture.date_fin_periode"]){
							$nbJours = ATF::facture_absystech()->date_diff($dateFin, $v["facture.date_fin_periode"]);	
							 if(($dateFin === "") || ($dateFin === NULL)){
								$dateFin = $v["facture.date_fin_periode"];
							}
							else{		
								if($nbJours > 1){
									$dateFin = $v["facture.date_fin_periode"];
								}																							
							}
						}
					}							
					ATF::affaire()->u(array("id_affaire" => $facture["id_affaire"], "date_fin_maintenance" => $dateFin));	
				}else{
					ATF::affaire()->u(array("id_affaire" => $facture["id_affaire"], "date_fin_maintenance" => NULL));					
				}
				
		
				//Affaire
				$this->q->reset()->addCondition("id_affaire",$facture["id_affaire"])->SetCount()->end();
				$autre_affaire=parent::sa();

				//S'il n'y a pas d'autres factures pour cette affaire
				if($autre_affaire["count"]==0){
					$affaire["id_affaire"]=$facture["id_affaire"];
					ATF::devis()->q->reset()->addCondition("id_affaire",$facture["id_affaire"])->SetCount()->end();
					$devis = ATF::devis()->sa();
					ATF::commande()->q->reset()->addCondition("id_affaire",$facture["id_affaire"])->SetCount()->end();
					$commande=ATF::commande()->sa();

					//S'il y a au moins une commande pour cette affaire
					if($commande["count"]>0){
						$affaire["etat"]="commande";
						ATF::affaire()->u($affaire,$s);
					//S'il y a au moins un devis 
					}elseif($devis["count"]>0){
						$affaire["etat"]="devis";	
						$affaire["forecast"]="20";
						ATF::affaire()->u($affaire,$s);
					//Sinon on peut tout supprimer
					}else{
						ATF::affaire()->delete($affaire["id_affaire"],$s);
						unset($facture["id_affaire"]);
					}					
				}	
				
				ATF::db($this->db)->commit_transaction();
				//*****************************************************************************

				if($facture["id_affaire"]){
					ATF::affaire()->redirection("select",$facture["id_affaire"]);
				}else{
					$this->redirection("select_all",NULL,"facture.html");
				}
		} elseif (is_array($infos) && $infos) {
            foreach($infos["id"] as $key=>$item){
                $this->delete($item,$s,$files,$cadre_refreshed);
            }
        }
		
		return true;
	}
	

	
	/** 
	* Retourne false car suppression de facture impossible sauf si etat impayee ou pas derniere du mois
	* @author Quentin JANON <qjanon@absystech.fr>
	* @return boolean 
	*/
	public function can_delete($id){
		if($this->select($id,"etat")=="impayee"){			 

			return true;
		}
		else{
			throw new error("Il est impossible de supprimer cette facture car elle est payée",892);
		}
	}
	



	/**
    * Retourne la valeur par défaut spécifique aux données passées en paramètres
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param string $field
	* @return string
    */   	
	public function default_value($field){
		if(ATF::_r('id_mission')){
			$infos=ATF::mission()->select(ATF::_r('id_mission'));
		}

		switch ($field) {
			case "id_societe":
				return $infos['id_societe'];
			case "tva":
				return 1.200;
			case "date":
				return date("Y-m-d");
			case "prix":
				return $infos["prix"];
			case "prix_unitaire":
				return $prix_achat;
			case "emailCopie":
				return ATF::$usr->get("email");
			case "email":
				if($id_societe){
					if($id_contact_facturation=ATF::societe()->select($id_societe,"id_contact_facturation")){
						return ATF::contact()->select($id_contact_facturation,"email");
					}else{
						return false;
					}
				}else{
					return false;
				}

			default:
				return parent::default_value($field);
		}
	}
	
	/**
    * Retourne la ref d'une facture
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param int $id_parent
	* @return string ref
    */
	function getRef($date,$class){
		if (!$date) {
			throw new error(ATF::$usr->trans("impossible_de_generer_la_ref_sans_date"),321);	
		}	

		$prefix=strtoupper(date("y",strtotime($date)));

		$this->q->reset()
					   ->addCondition("ref",$prefix."%","AND",false,"LIKE")
					   ->addField('SUBSTRING(`ref`,3)+1',"max_ref")
					   ->addOrder('ref',"DESC")
					   ->setDimension("row")
					   ->setLimit(1);

		$nb=$this->sa();
		if($nb["max_ref"]){
			if($nb["max_ref"]<10){
				$suffix="000".$nb["max_ref"];
			}elseif($nb["max_ref"]<100){
				$suffix="00".$nb["max_ref"];
			}elseif($nb["max_ref"]<1000){
				$suffix="0".$nb["max_ref"];
			}else{
				$suffix=$nb["max_ref"];
			}
		}else{
			$suffix="0001";
		}
		return $prefix.$suffix;
	}

		
};

?>