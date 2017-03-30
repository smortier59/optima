<?
/**
* Classe suivi
* Cet objet permet de gérer les suivis ! (sisi c'est vrai !)
* @package Optima
*/
class suivi extends classes_optima {
	/**
	* Constructeur
	*/
	public function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		$this->colonnes["fields_column"] = array(
			'suivi.id_user'
			,'suivi.id_societe'
			,'suivi.date'=>array("width"=>100,"align"=>"center")
			,'suivi.texte'
			,'suivi.intervenant_client'=>array("custom"=>true,"nosort"=>true)
			,'suivi.intervenant_societe'=>array("custom"=>true,"nosort"=>true)
			,'suivi.notifie'=>array("custom"=>true,"nosort"=>true)
			,'fichier_joint'=>array("width"=>50,"custom"=>true,"nosort"=>true,"type"=>"file")
		);
		$this->colonnes['primary'] = array(
			"id_societe"
			,"type"
			,"texte"=>array("xtype"=>"textarea","height"=>300)
			,"date"
			,"id_affaire"
		);

		$this->colonnes['panel']['intervenants'] = array(
			"suivi_contact"=>array("custom"=>true)
			,"suivi_societe"=>array("custom"=>true)
			,"suivi_notifie"=>array("custom"=>true)
		);
		$this->stats_types = array("user","users");


		$this->colonnes["speed_insert"] = array(
			'id_societe'
			,'type'
			,'date'
			,'texte'=>array("xtype"=>"textarea","height"=>150)
			,"suivi_contact"=>array("custom"=>true)
			,"suivi_societe"=>array("custom"=>true)
			,"suivi_notifie"=>array("custom"=>true)
		);

		$this->fieldstructure();
		$this->panels['intervenants'] = array("visible"=>true,"nbCols"=>3);
		$this->panels['primary'] = array("nbCols"=>1);

		$this->colonnes['bloquees']['select'] =  array('id_user','type');
		$this->colonnes['bloquees']['insert'] =  array('id_user');
		$this->colonnes['bloquees']['update'] =  array('id_user');
		$this->colonnes['bloquees']['filtre'] =  array("donnee"=>array('suivi_contact','suivi_societe','suivi_notifie'),"table"=>array('suivi_contact'=>1,'suivi_societe'=>1,'suivi_notifie'=>1));

		$this->addPrivilege("rpcGetRecentForMobile");
		$this->addPrivilege("suiviSpeedInsertForWebmail","insert");
		$this->field_nom = "texte";

		$this->files["fichier_joint"] = array("multiUpload"=>true);
		$this->onglets = array('tache');
		$this->formExt=true;
	}

	/**
    * Méthode d'insertion
    * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @return int id_suivi
    */
	public function insert($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL){
		if($infos["suivi"]["id_affaire"]){
			$liste["id_affaire"] = ATF::affaire()->decryptId($infos["suivi"]["id_affaire"]);
		}


		if($infos["suivi"]["no_redirect"]){
			unset($infos["suivi"]["no_redirect"]);
			$no_redirect = true;
		}


		$attente_reponse = NULL;
		if(isset($infos["attente_reponse"]) && $infos["attente_reponse"] == "oui") $attente_reponse = true;

		/*Pour passer des champs supplémentaires dans l'email*/
		$champsComplementaire=$infos["champsComplementaire"];
		unset($infos["champsComplementaire"]);
		$objet = $infos["objet"];
		unset($infos["objet"]);
		$liste['suivi_contact'];
		$this->infoCollapse($infos);

		$link = NULL;
		if(isset($infos["permalink"])){
			$link = $infos["permalink"];
			unset($infos["permalink"]);
		}


		$liste['suivi_contact']=$infos['suivi_contact'];
		$liste['suivi_societe']=$infos['suivi_societe'];
		$liste['suivi_notifie']=$infos['suivi_notifie'];
		$liste['objet'] = $infos["objet"];
		unset($infos['suivi_contact'],$infos['suivi_societe'],$infos['suivi_notifie'],$infos['id_tache'],$infos["objet"]);
		if (!$infos['id_user']) $infos["id_user"]=ATF::$usr->getID();

		if ($infos["id_contact"] && !$this->getColonne("id_contact")) {
			$id_contact = $infos["id_contact"];
			unset($infos["id_contact"]);
		}

		ATF::db($this->db)->begin_transaction();

		$infos['id_'.$this->table]=parent::insert($infos,$s,$files);

		//if(!$link){	$link = ATF::permalink()->getURL($this->createPermalink($infos['id_'.$this->table]));	}

		//pour chaque personnes concernées (notifiés, intervenant_societe, intervenant_client)
		$array=array('suivi_contact'=>'id_contact','suivi_societe'=>'id_user','suivi_notifie'=>'id_user');

		foreach($array as $nom_table=>$interesse){
			//on relie les personnes au suivi
			if (!is_array($liste[$nom_table])) {
				$liste[$nom_table]=explode(',',$liste[$nom_table]);
			}
			$noms_notifies = array();
			foreach($liste[$nom_table] as $cle_inter_cli=>$id_user){
				if ($id_user = $this->decryptId($id_user)) {
					$tab_insert[]=array('id_suivi'=>$infos['id_'.$this->table],$interesse=>$id_user);
					//si il s'agit des notifiés on retient les id pour leur envoyer un mail, sauf si il s'agit du créateur
					if($nom_table=="suivi_notifie"/* && $id_user!=ATF::$usr->getID()*/){
						$email = ATF::user()->select($id_user,'email');
//						$custom=unserialize(ATF::user()->select($id_user,'custom'));
//						if($custom['suivi']['mail']!="non"){
						$liste_email.=($liste_email?",":"").$email;
						if (!$noms_notifies[$id_user] && !$email) {
							// Pour message d'erreur éventuel
							$noms_notifies[$id_user] = ATF::user()->nom($id_user);
						}
//						}
					}
				}
			}
			if($tab_insert){
				if(ATF::getClass($nom_table)->multi_insert($tab_insert)){
					if($nom_table=="suivi_notifie"){
						if($liste_email){
							//on envoi un mail pour chaque notifié
							if($objet == ""){
								$objet = "Nouveau suivi de la part de ".ATF::user()->nom(ATF::$usr->getID());
							}


							$mail = new mail(array(
										"optima_url"=>$link,
										"recipient"=>$liste_email,
										"objet"=>$objet,
										"template"=>"suivi",
										"id_user"=>ATF::$usr->getID(),
										"id_affaire"=>$infos['id_affaire'],
										"id_suivi"=>$infos['id_'.$this->table],
										"champsComplementaire"=>$champsComplementaire,
										"attente_reponse"=>$attente_reponse,
										"from"=>ATF::$usr->get('email')));


							if($mail->send()){
								ATF::$msg->addNotice(ATF::$usr->trans("email_envoye",$this->table));
							}
						}else{
							//si il n'y a pas d'email on l'affiche
							if ($noms_notifies) {
								ATF::$msg->addWarning("Aucune adresse mail disponible pour ".implode(", ",$noms_notifies));
							}
						}
					}
				}
			}
			$tab_insert=array();
		}
		if(!$no_redirect){
			if($infos['__redirect'] && $infos['id_'.$infos['__redirect']]){
				ATF::getClass($infos['__redirect'])->redirection("select",$infos['id_'.$infos['__redirect']]);
			}elseif(count($liste['suivi_contact'])==1 && $liste['suivi_contact'][0]){
				ATF::contact()->redirection("select",$liste['suivi_contact'][0]);
			} else {
				ATF::societe()->redirection("select",$infos['id_societe']);
			}
		}

		ATF::db($this->db)->commit_transaction();


		return $infos['id_'.$this->table];
	}

	/**
    * Récupère les informations du suivi ou juste le champs éventuellement précisé
    * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param int id le suivi en question
	* @param string field le champs à retourner
	* @return array listes des informations concernant le suivi / le champs en question
    */
	public function select($id,$field=NULL) {
		if ($field) {
			return parent::select($id,$field);
		}
		$infos = parent::select($id);

		// récupération des contacts
		if(strlen($id)==32)$id=ATF::suivi()->decryptId($id);
		//pour chaque personnes concernées (notifiés, intervenant_societe, intervenant_client)
		$array=array('suivi_contact'=>'id_contact','suivi_societe'=>'id_user','suivi_notifie'=>'id_user');

		foreach($array as $nom_table=>$interesse){
			foreach(ATF::getClass($nom_table)->select_special('id_suivi',$id,'id_suivi','asc') as $key=>$item){
				$infos[$nom_table][$key]=$item[$interesse];
			}
		}

		return $infos;
	}

	/**
    * Méthode de mise à jour
    * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @return int id_suivi
    */
	public function update($infos,&$s,$files=NULL,&$cadre_refreshed){
		$objet = $infos["objet"];

		$attente_reponse = NULL;
		if(isset($infos["attente_reponse"]) && $infos["attente_reponse"] == "oui") $attente_reponse = true;

		$infos = $infos[$this->table];

		$liste['suivi_contact']=$infos['suivi_contact'];
		$liste['suivi_societe']=$infos['suivi_societe'];
		$liste['suivi_notifie']=$infos['suivi_notifie'];
		unset($infos['suivi_contact'],$infos['suivi_societe'],$infos['suivi_notifie']);




		ATF::db($this->db)->begin_transaction();

		parent::update($infos,$s);

		$infos['id_'.$this->table]=ATF::suivi()->decryptId($infos['id_'.$this->table]);
		//pour chaque personnes concernées (notifiés, intervenant_societe, intervenant_client)
		$array=array('suivi_contact'=>'id_contact','suivi_societe'=>'id_user','suivi_notifie'=>'id_user');

		foreach($array as $nom_table=>$interesse){
			//on supprime tous les destinataires du suivi avant modification
			$class=ATF::getClass($nom_table);
			$class->q->addCondition("id_suivi",$infos['id_'.$this->table]);
			$class->delete();
			$class->q->reset();

			//on relie les personnes au suivi
            if (!is_array($liste[$nom_table])) {
                $liste[$nom_table]=explode(',',$liste[$nom_table]);
            }
            $noms_notifies = array();
			foreach ($liste[$nom_table] as $cle_inter_cli=>$id_user) {
				if ($id_user=$this->decryptId($id_user)) {
					$tab_insert[]=array('id_suivi'=>$infos['id_'.$this->table],$interesse=>$id_user);
					if($nom_table=="suivi_notifie"/* && $id_user!=ATF::$usr->getID()*/){
						$email = ATF::user()->select($id_user,'email');
						$liste_email.=($liste_email?",":"").$email;
						if (!$noms_notifies[$id_user] && !$email) {
							$noms_notifies[$id_user] = ATF::user()->nom($id_user);
						}
					}

				}
			}
			if ($tab_insert) {
				if(ATF::getClass($nom_table)->multi_insert($tab_insert)){
					if($nom_table=="suivi_notifie"){
						if($liste_email){
							//on envoi un mail pour chaque notifié
							if($objet == ""){
								$objet = "Modification suivi de ".ATF::user()->nom(ATF::$usr->getID());
							}
							$mail = new mail(array(
										"optima_url"=>ATF::permalink()->getURL($this->createPermalink($infos['id_'.$this->table])),
										"recipient"=>$liste_email,
										"objet"=>$objet,
										"template"=>"suivi",
										"id_user"=>ATF::$usr->getID(),
										"id_affaire"=>$infos['id_affaire'],
										"id_suivi"=>$infos['id_'.$this->table],
										"attente_reponse"=>$attente_reponse,
										"from"=>ATF::$usr->get('email')));
							if($mail->send()){
								ATF::$msg->addNotice(ATF::$usr->trans("email_envoye",$this->table));
							}
						}else{
							//si il n'y a pas d'email on l'affiche
							if ($noms_notifies) {
								ATF::$msg->addWarning("Aucune adresse mail disponible pour ".implode(", ",$noms_notifies));
							}
						}
					}
				}
			}
			$tab_insert=array();
		}

		ATF::db($this->db)->commit_transaction();

		$this->redirection("select",$infos['id_'.$this->table]);

		return $infos['id_'.$this->table];
	}


	/**
    * Jointure avec la table de liaison pour avoir les contacts
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    */
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false,$parent=false) {
		$this->q
			->addJointure("suivi","id_suivi","suivi_contact","id_suivi")
			->addJointure("suivi_contact","id_contact","contact","id_contact","contact_cont")
			->addField("GROUP_CONCAT(DISTINCT(CONCAT_WS('',SUBSTRING(`contact_cont`.`prenom`,1,1),' ',`contact_cont`.`nom`)) SEPARATOR ',')","suivi.intervenant_client")

			->addJointure("suivi","id_suivi","suivi_societe","id_suivi")
			->addJointure("suivi_societe","id_user","user","id_user","user_soc")
			->addField("GROUP_CONCAT(DISTINCT(CONCAT_WS('',SUBSTRING(`user_soc`.`prenom`,1,1),' ',`user_soc`.`nom`)) SEPARATOR ',')","suivi.intervenant_societe")

			->addJointure("suivi","id_suivi","suivi_notifie","id_suivi")
			->addJointure("suivi_notifie","id_user","user","id_user","user_not")
			->addField("GROUP_CONCAT(DISTINCT(CONCAT_WS('',SUBSTRING(`user_not`.`prenom`,1,1),' ',`user_not`.`nom`)) SEPARATOR ',')","suivi.notifie")

			->addGroup("suivi.id_suivi");

		return parent::select_all($order_by,$asc,$page,$count);
	}

	/**
    * Renvoi le dernier suivi fait pour une société ou pour ses contacts.
    * @author QJ <qjanon@absystech.fr>
    */
	public function dernierSuivi($id) {
		$this->q->reset()
			->addCondition("id_societe",$id)
			->addOrder("date","desc")
			->setDimension("row")
		;
		return parent::select_all();
	}

	/**
    * Retourne la valeur par défaut spécifique aux données passées en paramètres
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $field
	* @return string
    */
	public function default_value($field){
		switch ($field) {
			case "id_societe":
				if(ATF::_r('id_contact')){
					return ATF::contact()->select(ATF::_r('id_contact'),"id_societe");
				}elseif(ATF::_r('id_affaire')){
					return ATF::affaire()->select(ATF::_r('id_affaire'),"id_societe");
				}elseif (ATF::_r('suivi_contact_id_contact')) {
					return ATF::contact()->select(ATF::_r('suivi_contact_id_contact'),"id_societe");
				} //Je commente car il y a un problème pour afficher les intervenants de la société...
				break;
			case "id_contact":
				if(ATF::_r('id_contact')){
					return ATF::_r('id_contact');
				}elseif(ATF::_r('suivi_contact_id_contact')){
					return ATF::_r('suivi_contact_id_contact');
				}else{
					ATF::contact()->q->reset()
									 ->addCondition("id_owner",ATF::$usr->get("id_user"))
									 ->setDimension("row");
					if($contact=ATF::contact()->sa()){
						return $contact["id_contact"];
					}
				}
				break;
		}
		return parent::default_value($field);
	}

	/**
	* Pour les autocomplete, retourne une conditions au format URL   arg1=2&arg2=3...
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $class Classe des enregistrements affichés dans l'autocomplète
	* @param array $infos ($requests habituellement attendu)
	*	int $infos[id_affaire]
	*	int $infos[id_societe]
	* @param string $condition_field
	* @param string $condition_value
	* @return array Conditions de filtrage
	*/
	public function autocompleteConditions(classes_optima $class,$infos,$condition_field=NULL,$condition_value=NULL) {
		$this->infoCollapse($infos);
		switch ($class->table) {
			case "contact":
				if ($infos['suivi_contact_id_contact']) {
					$conditions["condition_field"][] = $class->table.".id_societe";
					$conditions["condition_value"][] = ATF::contact()->select($infos['suivi_contact_id_contact'],'id_societe');
				}
				break;
		}
		return array_merge_recursive((array)($conditions),parent::autocompleteConditions($class,$infos,$condition_field,$condition_value));
	}

	/**
	* Méthode ajax pour appeler les hotlines
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return array
	*/
	public function rpcGetRecentForMobile($infos){
		ATF::$cr->block("top");
		return $this->getRecentForMobile($infos["countUnseenOnly"]);
	}

	/**
	* Retourne les récents ayant eu lieues depuis la dernière activité
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return array
	*/
	public function getRecentForMobile($countUnseenOnly=false){
		$this->q->reset()
			->addField("suivi.id_societe")
			->addField("suivi.date","date")
			->addField("suivi.texte","texte")

			// Les 7 derniers jours
			->andWhere("suivi.date",date("Y-m-d H:i:s",time()-86400*7),"date",">")
			//->andHaving("suivi.notifie","%".ATF::user()->nom(ATF::$usr->getID())."%","notif","LIKE")
			;
		if ($countUnseenOnly) {
			$this->q->setCountOnly()
				->andWhere("suivi.date",ATF::$usr->last_activity,"date",">");

			return $this->select_all();
		} else {
			$return = $this->select_all();
			foreach ($return as $k=>$i) {
				$return[$k]["contact"] = $return[$k]["suivi.intervenant_client"];
				$return[$k]["intervenant_societe"] = $return[$k]["suivi.intervenant_societe"];
				$return[$k]["societe"] = $return[$k]["suivi.id_societe"];
				$return[$k]["notifie"] = $return[$k]["suivi.notifie"];
				unset($return[$k]["suivi.intervenant_client"],$return[$k]["suivi.intervenant_societe"],$return[$k]["suivi.id_societe"],$return[$k]["suivi.notifie"]);
				$return[$k]["indexSectionDate"] = date("y-m-d",strtotime($return[$k]["date"]));
				$return[$k]["humanDate"] = ATF::$usr->date_trans($return[$k]["date"],true,false,true);

				if ($return[$k]["date"]>ATF::$usr->last_activity) {
					$return[$k]["date"] = "=> ".ATF::$usr->trans("unseen");
				}
			}
			return util::cleanForMobile($return);
		}
	}

	/**
    * Méthode qui renvoi les informations nécessaire pour générer la fenêtre EXTJS contenant le formulaire de transcription d'un email en suivi.
    * @author Quentin JANON <qjanon@absystech.fr>
	* @param array $infos Simple dimension des champs utiles
	* @return string/HTML Résultat du template en HTML
    */
	public function suiviSpeedInsertForWebmail(&$infos) {
		$msg = ATF::messagerie()->select($infos['id']);
		// Récupération du contact et de sa société
		$contact = ATF::contact()->ss("email",$msg['from']);
		// Ne fonctionne que si on ne trouve bien qu'un seul contact dans la BDD, sinon l'utilisateur doit saisir lui même a qui correspond le mail
		if (count($contact)==1) {
			$contact = $contact[0];
		} else {
			unset($contact);
		}
		// Permet de récupérer le mime pour traiter le message de façons différentes
		$msg['mime'] = true;

		$body = ATF::messagerie()->getBody($msg,true);
		if ($body['mime_type']=="text") {
			$content = $body['content'];
		} else {
			$content = str_replace("\r","",str_replace("\n","",strip_tags($body['content'])));
			//$content = html_entity_decode($content);
		}


		$infos['requests']['suivi'] = array(
			"id_user"=>ATF::$usr->getId()
			,"id_societe"=>$contact['id_societe']
			,"type"=>"email"
			,"date"=>$msg['date']
			,"texte"=>$content
			,"suivi_contact"=>array($contact['id_contact'])
		);
		ATF::$html->array_assign($infos);
		ATF::$html->assign("table",$this->table);
		ATF::$html->assign("current_class",$this);

		return ATF::$html->fetch("suivi-speed_insert_forWebMail.tpl.js");

	}

	public function digest($infos) {
		$date=date("Y-m-d H:i:s",strtotime('-1 day',strtotime(date("Y-m-d H:i:s"))));
		$this->q->reset()->addCondition("date",$date,false,false,">=")
						 ->addOrder("id_user");

		$suivi=$this->sa();
		foreach($suivi as $item){
			ATF::suivi_notifie()->q->reset()->addCondition("id_suivi",$item["id_suivi"]);
			$suivi_notifie=ATF::suivi_notifie()->sa();
			foreach($suivi_notifie as $i){
				$suiviByUser[$i["id_user"]][$item["id_suivi"]]=$item;
			}
		}

		foreach($suiviByUser as $key => $item){

			unset($infos);
			$preferences=ATF::preferences()->getCustom(false,$key);
			if($preferences["preferences"]["suivi.mail_digest"]!="non"){
				foreach($item as $i){
					$infos_g[]=$i;
					$infos[]=$i;
				}

				$mail = new mail(array(
										"recipient"=>ATF::user()->select($key,"email"),
										"objet"=>"Synthèse quotidienne des suivis à votre intention",
										"template"=>"suivi_digest",
										"infoSuivi"=>$infos,
										"from"=>"Optima ".ucfirst(ATF::$codename)
										)
									);

				$mail->send();
			}
		}

		return $infos_g;
	}


	/*
 	* Fonctions pour telescope
 	*/
	/**
  * Méthode _GET pour récupérer la liste des suivis
  * @package telescope
  * @author Cyril CHARLIER <ccharlier@absystech.fr>
	* @param $get array Paramètre de filtrage, de tri, de pagination, etc...
	* @param $post array Argument obligatoire mais inutilisé ici.
	* @return array un tableau avec les données
	**/

	public function _GET($get,$post){
		// Gestion du tri
		if (!$get['tri']) $get['tri'] = "societe.id_suivi";
		if (!$get['trid']) $get['trid'] = "desc";

		// Gestion du limit
		if (!$get['limit']) $get['limit'] = 30;

		// Gestion de la page
		if (!$get['page']) $get['page'] = 0;

		$colsData = array(
			"societe.societe"=>array(),
			"societe.id_societe"=>array(),
			"user.id_user"=>array(),
			"user.civilite"=>array(),
			"user.nom"=>array(),
			"user.prenom"=>array(),
			"suivi.date"=>array(),
			"suivi.type"=>array(),
			"affaire.affaire"=>array(),
			"texte"=>array(),
			"suivi.id_suivi"=>array(),
			"suivi.id_opportunite"=>array(),
			"suivi.intervenant_client"=> array('custom'=>true),
			"suivi.intervenant_societe"=> array('custom'=>true),
			"suivi.temps_passe"=> array(),
			"suivi.ponderation"=> array(),
		);

		$this->q->reset();

		if($get["search"]){
			header("ts-search-term: ".$get['search']);
			$this->q->setSearch($get["search"]);
		}

		$this->q->addField($colsData);

		$this->q->from("suivi","id_user","user","id_user");
		$this->q->from("suivi","id_societe","societe","id_societe")
						->from("suivi",'id_affaire','affaire','id_affaire')
					->addJointure("suivi_notifie","id_user","user","id_user","user_not")
					->addJointure("suivi_contact","id_contact","contact","id_contact","suivi_client")
					->addJointure("suivi_societe","id_user","contact","id_user","suivi_societe")
					->addField("GROUP_CONCAT(DISTINCT(CONCAT_WS('',`user_not`.`prenom` ,' ',`user_not`.`nom`)) SEPARATOR ',')","suivi.notifie")
					->addField("GROUP_CONCAT(DISTINCT(`user_not`.`id_user`) SEPARATOR ',')","suivi.notifie_id")
					->addField("GROUP_CONCAT(DISTINCT(`suivi_client`.`id_contact`) SEPARATOR ',')","suivi.suivi_client")
					->addField("GROUP_CONCAT(DISTINCT(`suivi_societe`.`id_user`) SEPARATOR ',')","suivi.suivi_societe");

    $this->q->setLimit($get['limit'])->setCount();

		if ($get['id']) {
			$this->q->where("suivi.id_suivi",$get['id'])->setCount(false)->setDimension('row');
	    $data = $this->select_all();
	    foreach ($data as $k=>$lines) {
					if (strpos($k,".")) {
						$tmp = explode(".",$k);
						$data[$tmp[1]] = $lines;
						unset($data[$k]);
				}
			}
			$return = $data;
		} else {
			$data = $this->select_all($get['tri'],$get['trid'],$get['page'],true);

			// Envoi des headers
			header("ts-total-row: ".$data['count']);
			header("ts-max-page: ".ceil($data['count']/$get['limit']));
			header("ts-active-page: ".$get['page']);
			foreach ($data["data"] as $k=>$lines) {
				foreach ($lines as $k_=>$val) {
					if (strpos($k_,".")) {
						$tmp = explode(".",$k_);
						$data['data'][$k][$tmp[1]] = $val;
						unset($data['data'][$k][$k_]);
					}
				}
			}
	    $return = $data['data'];
		}


		return $return;
	}


    /**
  * Fonction _POST pour telescope
  * @package Telescope
  * @author Charlier Cyril <ccharlier@absystech.fr>
  * @param $get array.
  * @param $post array Argument obligatoire.
  * @return integer
  */
	public function _POST($get,$post){
    $input = file_get_contents('php://input');
    if (!empty($input)) parse_str($input,$post);
    $suivi=array(
    	'suivi'=>array(
    		'id_societe'=> $post['id_societe'],
    		'type'=>$post['type'],
    		'texte'=>$post['texte'],
    		'date'=>$post['date'],
    		'id_affaire'=> $post['id_affaire'],
    		"suivi_contact"=>$post['suivi_contact'],
    		"suivi_societe"=>$post['suivi_societe'],
    		"suivi_notifie"=>$post['suivi_notifie'],
    		"id_opportunite"=>$post['id_opportunite'],
    		'ponderation'=>$post['ponderation'],
    		'temps_passe'=>$post['temps_passe']
    	)
    );

    $return['id_suivi'] =$this->insert($suivi);
    return $return;
  }

    /**
  * Fonction _PUT pour telescope
  * @package Telescope
  * @author Charlier Cyril <ccharlier@absystech.fr>
  * @param $get array.
  * @param $post array Argument obligatoire.
  * @return boolean | integer
  */
  public function _PUT($get,$post){
    $input = file_get_contents('php://input');
    if (!empty($input)) parse_str($input,$post);
    $suivi=array(
    	'suivi'=>array(
    		'id_suivi'=> $post['id_suivi'],
    		'id_societe'=> $post['id_societe'],
    		'type'=>$post['type'],
    		'texte'=>$post['texte'],
    		'date'=>$post['date'],
    		'id_affaire'=> $post['id_affaire'],
    		"suivi_contact"=>$post['suivi_contact'],
    		"suivi_societe"=>$post['suivi_societe'],
    		"suivi_notifie"=>$post['suivi_notifie'],
    		"id_opportunite"=>$post['id_opportunite'],
    		'ponderation'=>$post['ponderation'],
    		'temps_passe'=>$post['temps_passe']
    	)
    );

    $this->update($suivi);
    $return['id_suivi'] = $post['id_suivi'];
    return $return;
  }

};
?>