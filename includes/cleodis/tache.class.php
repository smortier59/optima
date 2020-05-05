<?
/** Classe affaire
* @package Optima
* @subpackage Cléodis
*/
require_once dirname(__FILE__)."/../tache.class.php";
class tache_cleodis extends tache {
	function __construct() {
		$this->table = "tache";
		parent::__construct();
		$this->colonnes['fields_column']["actions"] = array("width"=>50,"custom"=>true,"nosort"=>true,"renderer"=>"actionsTachesCleodis");


		$this->colonnes['fields_column']["tache.type_tache"];
		$this->colonnes['fields_column']["tache.decision_comite"];

		$this->colonnes['panel']["infos_tache"] =	array("type_tache" => array("custom"=>true) ,
										          		  "decision_comite"=> array("custom"=>true)
										);
		$this->panels['infos_tache'] = array('nbCols'=>2,'visible'=>true);
		$this->fieldstructure();

		$this->foreign_key['id_user_valid_1'] = "user";
		$this->foreign_key['id_user_valid_2'] = "user";


		$this->colonnes['bloquees']['insert'] =
		$this->colonnes['bloquees']['cloner'] =
		$this->colonnes['bloquees']['update'] = array('id_user_valid_1','id_user_valid_2','validation_1','validation_2');
	}

	/**
    * Surcharge d'insertion pour la redirection
    * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @return int id_tache
    */
	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$no_mail=false){

		if($infos["tache"]["id_suivi"] && !$infos["tache"]["id_affaire"]) $infos["tache"]["id_affaire"] = ATF::suivi()->select($infos["tache"]["id_suivi"] , "id_affaire");

		if($infos["tache"]["type_tache"] == "demande_comite"){
			if(isset($infos['dest'])) {
			    $liste_destinataire= is_array($infos['dest'])?$infos['dest']:explode(",",$infos['dest']);
	        }
			$this->infoCollapse($infos);

			if(!$infos['horaire_debut'])$infos['horaire_debut']=date("Y-m-d H:i:s");
			if(!$infos["id_user"])$infos["id_user"]=ATF::$usr->getID();
			//si un suivi est précisé et que la socité ne l'est pas, on prends l'id_societe du suivi pour le rattacher a la tache
			if($infos['id_suivi'] && !$infos['id_societe'])$infos['id_societe']=ATF::suivi()->select($infos['id_suivi'],'id_societe');
			$infos['id_societe']=ATF::societe()->decryptId($infos['id_societe']);

			ATF::db($this->db)->begin_transaction();

			$infos['id_'.$this->table]=parent::insert($infos,$s);
			//on met le créateur de la tâche dans les mails
			if ($emailUser = ATF::user()->select($infos["id_user"],'email')) {
				$liste_email[$infos["id_user"]]=$emailUser;
			}
			//on relie les destinataires à la tâche
			foreach($liste_destinataire as $key=>$id_user){
				$id_util=ATF::user()->decryptId($id_user);
				if(!$liste_email[$id_util]){
					$email=ATF::user()->select($id_util,'email');
					if($email){
						$liste_email[$id_util]=$email;
					}
				}
				$tab_dest[]=array('id_tache'=>$infos['id_'.$this->table],'id_user'=>$id_util);
			}

			//ajout des concernés
			if($tab_dest){
				try{
					ATF::tache_user()->multi_insert($tab_dest);
				} catch(errorATF $e) {
					ATF::db($this->db)->rollback_transaction();
					$e->setError();
					throw new errorATF('Erreur Insert');
				}
				$tache["id_tache"] = $infos['id_'.$this->table];
				for($i=0;$i<2;$i++){
					if($tab_dest[$i]){
						$int = $i+1;
						$tache["id_user_valid_$int"] = $tab_dest[$i]["id_user"];
					}
				}
				parent::u($tache);
			}

			//dans le cas où l'on a un tache.class dans un autre projet qui appel cette méthode
			if(!$no_mail){
				$entete = "Demande comité";
				if($infos["decision_comite"]){
					switch ($infos["decision_comite"]) {
						case 'accord_portage':  $entete = "Accord portage";
						case 'accord_reserve_cession':  $entete = "Accord sous réserve de cession";
						case 'accord_cession_portage':  $entete = "Accord cession ou portage";
						case 'attente_retour':  $entete = "Attente retour comité";
						case 'refus_comite':  $entete = "Refus comité";
					}
				}

				//envoi des mails aux concernés (si il y a au moins le mail du
				if(count($liste_email)>1 || $liste_email[ATF::$usr->getID()]){
					$mail = new mail(array( "recipient"=>implode(',',$liste_email),
								"optima_url"=>ATF::permalink()->getURL($this->createPermalink($infos['id_'.$this->table])),
								"objet"=>"[".$entete."]Nouvelle tâche de la part de ".ATF::user()->nom(ATF::$usr->getID()),
								"template"=>"tache_insert",
								"donnees"=>$infos,
								"from"=>ATF::$usr->get('email')));
					if($mail->send()){
						if($infos["decision_comite"]){
							$suivi = array(
								 "id_user"=>ATF::$usr->get('id_user')
								,"id_societe"=>$infos['id_societe']
								,"id_affaire"=>$infos['id_affaire']
								,"type_suivi"=>'Refinancement'
								,"texte"=>"La decision du comité viens d'être rendue : ".$entete.")\n par ".ATF::$usr->getNom()
								,'public'=>'oui'
								,'id_contact'=>NULL
								,'suivi_societe'=>array(0=>ATF::$usr->getID())
							);
							ATF::suivi()->insert($suivi);
						}
						ATF::$msg->addNotice(ATF::$usr->trans("email_envoye",$this->table));
					}
				}else{	ATF::$msg->addNotice("Aucune adresse mail disponible");	}
			}
			ATF::db($this->db)->commit_transaction();
			if($infos['id_suivi']){	ATF::suivi()->redirection("select",$infos['id_suivi']);	}
			else{	$this->redirection("select",$infos['id_tache']);	}
			return $infos['id_'.$this->table];
		}else{
			$infos['id_tache'] = parent::insert($infos,$s,$files,$cadre_refreshed,$no_mail);
			//$this->redirection("select",$infos['id_tache']);
			return $infos['id_tache'];
		}
	}



	/**
    * Valide une tache
    * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param array $infos pour la validation
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
    */
	public function valid($infos,&$s,$files=NULL,&$cadre_refreshed){
		// on met a jour les infos de la base avec entre autre la date de validation et le nom du user qui a validé
		//envoi d'un mail à tous les concernés si mise à jour effectuée
		$tache = $this->select($infos["id_tache"]);
		$termine = true;

		if($tache["type_tache"] == "demande_comite"){
			$data["id_tache"] = $infos["id_tache"];

			if($tache["id_user_valid_1"] == ATF::$usr->getID()){
				$data["validation_1"] = 1;
				$data["decision_1"] = $infos["comboDisplay"];
			}

			if($tache["id_user_valid_2"] == ATF::$usr->getID()){
				$data["validation_2"] = 1;
				$data["decision_2"] = $infos["comboDisplay"];
			}

			parent::u($data);

			$suivi = array(
					 "id_user"=>ATF::$usr->get('id_user')
					,"id_societe"=>$tache['id_societe']
					,"id_affaire"=>$tache['id_affaire']
					,"type_suivi"=>'Refinancement'
					,"texte"=>"La decision du comité viens d'être rendue : ".$infos["comboDisplay"].")\n par ".ATF::$usr->getNom()
					,'public'=>'oui'
					,'id_contact'=>NULL
					,'suivi_societe'=>array(0=>ATF::$usr->getID())
				);
			ATF::suivi()->insert($suivi);

			$tache = $this->select($infos["id_tache"]);


			if($tache["id_user_valid_1"] && !$tache["validation_1"]) $termine = false;
			if($tache["id_user_valid_2"] && !$tache["validation_2"]) $termine = false;

		}


		if($termine){
			if(parent::u(array("etat"=>"fini",
							"id_aboutisseur"=>ATF::$usr->getID(),
							"date_validation"=>date("Y-m-d H:i"),
							"complete"=>100,
							"id_tache"=>$infos["id_tache"])
						)
			){
				if ($email_envoye=$this->envoyer_mail($infos["id_tache"],"tache_valid")) {
					ATF::$msg->addNotice(ATF::$usr->trans("email_envoye"));
				}
				$return = $email_envoye;
			}

		}
		return $return;
	}



	/*
	 * Annule une tâche
 	 * @author Quentin JANON <qjanon@absystech.fr>
 	 * @param id
	 * @return TRUE si vrai, sinon FALSE
	 */
	function cancel($infos) {
		if (!$infos['id']) return false;
		$d = array("id_tache"=>$this->decryptId($infos['id']),"etat"=>"annule");
		return parent::u($d);
	}


	/** Selectionne les taches correspondant à une date et au user connecté
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param date $date
	*/
	public function tache_imminente($date,$nbJour=5){
		if (!$date) $date = date("Y-m-d h:i:s");
		$this->q->reset()
				->addField("tache.tache","tache")
				->addField("societe.id_societe","id_societe")
				->addField("societe.societe","societe")
				->addField("DATE_FORMAT(tache.horaire_fin,'%Y-%m-%d')","date")
				->addField("CONCAT_WS(' ',user.civilite,user.prenom,user.nom)","createur")
				->addJointure('tache','id_tache','tache_user','id_tache')
				->addJointure('tache','id_user','user','id_user')
				->addJointure("tache","id_societe","societe","id_societe")
				->addCondition('tache.horaire_fin',$date,NULL,"sup",'>=')
				->addCondition('tache.horaire_fin',date("Y-m-d h:i:s",strtotime('+'.$nbJour.' days',strtotime($date))),NULL,"inf",'<')
				->addCondition('tache.etat','en_cours')
				->addCondition('tache_user.id_user',ATF::$usr->getID())
				->addOrder("date","asc");

		foreach(parent::select_all() as $key=>$item){
			$lignes[$item['date']][]=$item;
		}
		return $lignes;
	}


	public function update($infos,&$s,$files=NULL,&$cadre_refreshed=NULL){
		if($infos["tache"]["type_tache"] == "demande_comite"){

			if(isset($infos['dest'])) {
			    $liste_destinataire= is_array($infos['dest'])?$infos['dest']:explode(",",$infos['dest']);
	        }
			//on relie les destinataires à la tâche
			foreach($liste_destinataire as $key=>$id_user){
				$id_util=ATF::user()->decryptId($id_user);
				if(!$liste_email[$id_util]){
					$email=ATF::user()->select($id_util,'email');
					if($email){
						$liste_email[$id_util]=$email;
					}
				}
			}

			$entete = "Demande comité";
			if($infos["tache"]["decision_comite"]){
					switch ($infos["decision_comite"]) {
						case 'accord_portage':  $entete = "Accord portage";
						case 'accord_reserve_cession':  $entete = "Accord sous réserve de cession";
						case 'accord_cession_portage':  $entete = "Accord cession ou portage";
						case 'attente_retour':  $entete = "Attente retour comité";
						case 'refus_comite':  $entete = "Refus comité";

					}


				$suivi = array(
					 "id_user"=>ATF::$usr->get('id_user')
					,"id_societe"=>$infos["tache"]['id_societe']
					,"id_affaire"=>$infos["tache"]['id_affaire']
					,"type_suivi"=>'Refinancement'
					,"texte"=>"Decision comité rendue : ".$entete."\n par ".ATF::$usr->getNom()
					,'public'=>'oui'
					,'id_contact'=>NULL
					,'suivi_societe'=>array(0=>ATF::$usr->getID())
				);
				ATF::suivi()->insert($suivi);

				if(count($liste_email)>=1 || $liste_email[ATF::$usr->getID()]){

					$mail = new mail(array( "recipient"=>implode(',',$liste_email),
								"optima_url"=>ATF::permalink()->getURL($this->createPermalink($infos["tache"]['id_'.$this->table])),
								"objet"=>"[".$entete."]Mise à jour d'une tâche de la part de ".ATF::user()->nom(ATF::$usr->getID()),
								"template"=>"tache_insert",
								"donnees"=>$infos["tache"],
								"from"=>ATF::$usr->get('email')));

					if($mail->send()){
						ATF::$msg->addNotice(ATF::$usr->trans("email_envoye",$this->table));
					}
				}else{ ATF::$msg->addNotice("Aucune adresse mail disponible"); }
			}
			parent::update($infos,$s);
		}else{
			parent::update($infos,$s);
		}
	}


	/**
    * On surcharge le select_all pour permettre le tri sur certains champs et de pouvoir les préfixer, et de filtrer les informations sur ce que l'on souhaite voir
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @return liste des tâches filtrées
    */
	public function select_all() {
		$this->q->addField("type_tache");
		return parent::select_all();
	}


};

class tache_cleodisbe extends tache_cleodis { };
class tache_cap extends tache_cleodis { };

class tache_bdomplus extends tache_cleodis { };

class tache_boulanger extends tache_cleodis { };

class tache_assets extends tache_cleodis { };