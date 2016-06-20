<?
/**
* @package Optima
*/
class tache extends classes_optima {
	function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		$this->quick_insert = array('tache'=>'tache');
		
		$this->colonnes['fields_column']  = array(
			'tache.tache'
			,'tache.id_societe'=>array("width"=>200)
			,'tache.concernes'=>array("custom"=>true,"nosort"=>true,"width"=>200)
			,'tache.complete'=>array("width"=>100,"renderer"=>"progress")
			,'horaire_fin'=>array("width"=>80,"renderer"=>"duree")
			,'fichier_joint'=>array("width"=>50,"custom"=>true,"nosort"=>true,"type"=>"file","align"=>"center")
			,'actions'=>array("width"=>50,"custom"=>true,"nosort"=>true,"renderer"=>"actionsTaches")
			,'relance'=>array("width"=>50,"custom"=>true,"nosort"=>true,"renderer"=>"relanceTache")
		);
		$this->colonnes['primary']  = array(
            'tache',
            'id_societe',
            'horaire_fin',
            'concernes'=>array("custom"=>true)
        ); 
		/*pour page accueil de type nebula*/
		$this->colonnes["retard"] = array("id_societe","tache","retard"=>array("custom"=>true),"validation"=>array("custom"=>true));
		$this->colonnes["declare"] = array("id_societe","id_user","tache","horaire_debut","restant"=>array("custom"=>true),"etat","validation"=>array("custom"=>true));
		
		//$this->jointure[] = querier::jointure($this->table,"id_societe","societe","id_societe");
		$this->stats_types = array("user","users");
		$this->complete = array('0'=>'0%','20'=>'20%','40'=>'40%','60'=>'60%','80'=>'80%');

		//IMPORTANT, complète le tableau de colonnes avec les infos MYSQL des colonnes
		$this->fieldstructure();		
		
		$this->colonnes['bloquees']['insert'] =  array('id_suivi','id_aboutisseur','etat','id_user','horaire_debut','type','lieu','description','date_validation','complete');	
		$this->colonnes['bloquees']['update'] =  array('id_suivi','id_aboutisseur','etat','id_user','horaire_debut','type','lieu','description','date_validation','complete');	
		$this->colonnes['bloquees']['filtre'] =  array("donnee"=>array('tache.concernes'),"table"=>array('tache_user'=>1));
		$this->files["fichier_joint"] = array("multiUpload"=>true);

		$this->foreign_key["aboutisseur"] = "user";
		
		// [JOINTURE SUPPLEMENTAIRE DE TABLE USER] Correspondance de Alias vers Table
		//$this->foreign_key["concernes"] = "userConcernes";
		
		$this->addPrivilege("valid","update");
		$this->addPrivilege("postpone","update");
		$this->addPrivilege("cancel","update");
		$this->addPrivilege("giveUp","update");
		$this->addPrivilege("update_complete","update");
		$this->addPrivilege("liste_tache");
		$this->addPrivilege("tachesImminentes");
		$this->addPrivilege("tacheLate");
		$this->addPrivilege("relance");

	}
	
	/**
    * On surcharge le select_all pour permettre le tri sur certains champs et de pouvoir les préfixer, et de filtrer les informations sur ce que l'on souhaite voir
    * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @return liste des tâches filtrées
    */ 
	public function select_all() {
		$this->q	->addField("DATEDIFF(tache.horaire_fin,DATE(NOW()))","horaire_fin")
					->addField("tache.etat")
					->addField("tache.type")
					->addField("GROUP_CONCAT(CONCAT_WS('',`userconcernes`.`prenom`,' ',`userconcernes`.`nom`) SEPARATOR ',')","tache.concernes")
					->addJointure("tache","id_tache","tache_user","id_tache")
					->addJointure("tache_user","id_user","user","id_user","userconcernes") // [JOINTURE SUPPLEMENTAIRE DE TABLE USER]
					// Ces deux jointures sont celle qui sont générés automatiquement par le querier, je les force car j'ai un problème  avec le querier couplé au filtre.
					// Pour le filotre mes tâches, on utilise le champs user.nom, sauf que la jointure automatique qui se fait via le querier prend l'alias user__id_user donc 
					// il ne s'y retouve pas. Je force donc les deux jointures automatiques et je force l'alias de la jointure user sur USER.
					->addJointure("tache","id_societe","societe","id_societe","societe__id_societe") // [JOINTURE SUPPLEMENTAIRE DE TABLE SOCIETE]
					->addJointure("tache","id_user","user","id_user","user") // [JOINTURE SUPPLEMENTAIRE DE TABLE USER]
					->addGroup("tache.id_tache");
		$return = parent::select_all();
		
		foreach ($return['data'] as $k=>$i) {
			if ($i['tache.etat'] != "fini") {
				$return['data'][$k]['allowValid'] = true;
			} else {
				$return['data'][$k]['allowValid'] = false;
			}
		}
		return $return;
	}
	
			
	/**
    * Sert à la mise à jour du champs complété lors d'un changement sur l'accueil
    * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array $infos contient les informations nécessaires à la modification (id_tache et complete)
    */ 
	public function update_complete($infos){
		parent::update($infos);
	}
	
	/**
    * Méthode d'insertion
    * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @return int id_tache
    */ 	
	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$no_mail=false){
		if(isset($infos['dest'])) {
		    $liste_destinataire= is_array($infos['dest'])?$infos['dest']:explode(",",$infos['dest']);
        }
		$this->infoCollapse($infos);

		if($infos["no_redirect"]){
			unset($infos["no_redirect"]);
			$no_redirect = true;
		}

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
		}
		
		//dans le cas où l'on a un tache.class dans un autre projet qui appel cette méthode
		if(!$no_mail){
			//envoi des mails aux concernés (si il y a au moins le mail du 
			if(count($liste_email)>1 || $liste_email[ATF::$usr->getID()]){
				$mail = new mail(array( "recipient"=>implode(',',$liste_email), 
							"optima_url"=>ATF::permalink()->getURL($this->createPermalink($infos['id_'.$this->table])),
							"objet"=>"Nouvelle tâche de la part de ".ATF::user()->nom(ATF::$usr->getID()),
							"template"=>"tache_insert",
							"donnees"=>$infos,
							"from"=>ATF::$usr->get('email')));
				if($mail->send()){
					ATF::$msg->addNotice(ATF::$usr->trans("email_envoye",$this->table));
				}
			}else{
				//si il n'y a pas d'email on l'affiche
				ATF::$msg->addNotice("Aucune adresse mail disponible");
			}
		}
		
		ATF::db($this->db)->commit_transaction();

		if (!$no_redirect) {
			if($infos['id_suivi']){
				ATF::suivi()->redirection("select",$infos['id_suivi']);
			}else{
				$this->redirection("select",$infos['id_tache']);
			}
		}
		return $infos['id_'.$this->table];
	}
	
	/**
    * Méthode de mise à jour
    * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array $infos Simple dimension des champs à modifier, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @return int id_tache
    */ 	
	public function update($infos,&$s,$files=NULL,&$cadre_refreshed=NULL){
		if(isset($infos['dest'])) {
		    $liste_destinataire= is_array($infos['dest'])?$infos['dest']:explode(",",$infos['dest']);
        }
		$this->infoCollapse($infos);
		$infos["id_".$this->table] = $this->decryptId($infos["id_".$this->table]); 
				
		ATF::db($this->db)->begin_transaction();
				
		parent::update($infos,$s);
		//on regarde si les concernés ont été changés
		if($liste_destinataire){
			ATF::tache_user()->q->reset()->addCondition("id_tache",$infos['id_'.$this->table]);
			$anciens_concernes=ATF::tache_user()->select_all('tache_user.id_user','asc');
	
			foreach($liste_destinataire as $cle=>$id_user){
				foreach($anciens_concernes as $key=>$item){
					if($item["id_user"]==$id_user){
						unset($liste_destinataire[$cle]);
						unset($anciens_concernes[$key]);
						break;
					}
				}
			}
			//ajout des nouveaux concernés
			if($liste_destinataire){
				foreach($liste_destinataire as $key=>$id_user){
					$ajout[]=array('id_tache'=>$infos['id_'.$this->table],'id_user'=>$id_user);
				}
				try{
					ATF::tache_user()->multi_insert($ajout);
				} catch(errorATF $e) {
					ATF::db($this->db)->rollback_transaction();
					$e->setError();
					throw new errorATF('Erreur Insert');
				}
			}
			//suppression de ceux qui ont été déselectionnés
			if($anciens_concernes){
				ATF::tache_user()->q->reset();
				foreach($anciens_concernes as $key=>$item){
					ATF::tache_user()->q->addCondition("id_tache",$item['id_tache'])->addCondition("id_user",$item["id_user"]);
				}
				ATF::tache_user()->delete();
			}
		}
		
		ATF::db($this->db)->commit_transaction();
		
		if($infos['id_suivi']){
			ATF::suivi()->redirection("select",$infos["id_suivi"]);
		}else{
			$this->redirection("select_all");
		}
		
		return $infos['id_'.$this->table];
	}
		
	/**
    * Renvoi les users concernés par la tâche
    * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param int id_tache la tâche en question
	* @return array listes des destinataires : array(0=>array(id_user=>?),1=>...)
    */ 	
	public function infos_dest($id_tache) {
		ATF::tache_user()->q->reset()->addCondition("id_tache",$this->decryptId($id_tache));
		return ATF::tache_user()->select_all('tache_user.id_user','asc');
	}	
	
	/**
    * Récupère les informations de la tâches ou juste le champs éventuellement précisé
    * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param int id la tâche en question
	* @param string field le champs à retourner
	* @return array listes des informations concernant la tâche / le champs en question
    */ 
	public function select($id,$field=NULL) {
		if ($field) {
			return parent::select($id,$field);
		}
		$infos = parent::select($id);
		foreach($this->infos_dest($id) as $key=>$item){
			$infos["dest"][$key]=$item["id_user"];
		}
		return $infos;
	}
	
	/**
    * Valide une tache
    * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array $infos pour la validation
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
    */
	public function valid($infos,&$s,$files=NULL,&$cadre_refreshed){
		if (!$infos['id_tache']) return false;
		
		$infos['etat'] = "fini";
		$infos['complete'] = 100;
		$infos['date_validation'] = date("Y-m-d H:i");
		$infos['id_aboutisseur'] = ATF::$usr->getID();
		
		if (parent::update($infos)) {
			if ($email_envoye=$this->envoyer_mail($infos["id_tache"],"tache_valid")) {
				ATF::$msg->addNotice(ATF::$usr->trans("email_envoye"));
			}
		}		
		return $infos['id_tache'];
	}

	/**
    * Relance une tache
    * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $infos pour la validation
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
    */
	public function relance($infos,&$s,$files=NULL,&$cadre_refreshed){
		if ($email_envoye=$this->envoyer_mail($infos["id_tache"],"tache_relance")) {
			ATF::$msg->addNotice(ATF::$usr->trans("email_envoye"));
		}
		return $email_envoye;
	}
	
	/**
    * Envoi d'un mail aux personnes concernées par la tâche
    * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array &$s La session
	* @param int $id_tache la tâche en question
    */
	public function envoyer_mail($id_tache,$type="tache_valid") {
		$infos_tache = $this->select($id_tache);
		
		//création de la liste des emails des concernés avec celui de l'aboutisseur si il ne fait pas parti des concernés
		$liste_destinataire=$this->infos_dest($id_tache);
		foreach($liste_destinataire as $key=>$item){
			if($infos_tache['id_aboutisseur']==$item["id_user"]){
				$est_concerne=true;
			}
			$email=ATF::user()->select($item["id_user"],'email');
			if($email){
				$liste_email.=($liste_email?",":"").$email;
			}
		}
		
		if(!$est_concerne && $infos_tache['id_aboutisseur']){
			$email_abou=ATF::user()->select($infos_tache['id_aboutisseur'],'email');
			if($email_abou){
				$liste_email.=($liste_email?",":"").$email_abou;
			}
		}

		$mail = new mail(array( "recipient"=>"$liste_email", 
								"objet"=>ATF::$usr->trans($type,'mail'),
								"template"=>$type,
								"tache"=>$infos_tache,
								"from"=>"Optima <no-reply@absystech.fr>"));
		return $mail->send();
	}
				
	/** méthode permettant de faire les graphes des différents modules, dans statistique
	* @author DEV <dev@absystech.fr>, Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array &$s La session
	* @param bool $stats
	* @param bool $type
	*/
	public function stats($stats=false,$type=false) {
		$this->q->reset();
		//on récupère la liste des années que l'on ne souhaite pas voir afficher sur les graphes
		//on les incorpore ensuite sur les requêtes adéquates
		/*foreach(ATF::stats()->liste_annees[$this->table] as $key_list=>$item_list){
			if($item_list)$this->q->addCondition("YEAR(`horaire_fin`)",$key_list);
		}*/
		ATF::stats()->conditionYear(ATF::stats()->liste_annees[$this->table],$this->q,"`horaire_fin`",$type);
		
		switch ($type) {
			case "user":
				$this->q->addField("YEAR(`horaire_fin`)","year")
						->addField("MONTH(`horaire_fin`)","month")
						->addField("COUNT(*)","nb")
						->addCondition("id_user",ATF::$usr->getID())
						->addGroup("year")->addGroup("month");
				$stats['DATA'] = parent::select_all();
				
				$this->q->reset("field,group");
				$this->q->addField("DISTINCT YEAR(`horaire_fin`)","year");
				$stats['YEARS'] =parent::select_all();

				return parent::stats($stats,$type);

			case "users":
				$this->q->reset();
				$this->q->addField("CONCAT(`user`.`prenom`,' ',`user`.`nom`)","label")
						->addField("user.id_user","year")
						->addField("DATE_FORMAT(`".$this->table."`.`horaire_fin`,'%Y')","y")
						->addField("DATE_FORMAT(`".$this->table."`.`horaire_fin`,'%m')","month")
						->addField("COUNT(*)","nb")
						->addJointure("tache","id_user","user","id_user")
						->addCondition("TO_DAYS(NOW())-TO_DAYS(`".$this->table."`.`horaire_fin`)","365",NULL,"sub_date","<",false,false,true)
						->addGroup("year")->addGroup("month");
				$stats['DATA'] = parent::select_all();
				
				$this->q->reset("field,group,where");
				$this->q->addField("DISTINCT ".$this->table.".`id_user`","years");
				$stats['YEARS'] = parent::select_all();

				return parent::stats($stats,$type);
				
			default:
				$this->q->addField("YEAR(`horaire_fin`)","year")
						->addField("MONTH(`horaire_fin`)","month")
						->addField("COUNT(*)","nb");
				$this->q->addGroup("year")->addGroup("month");
				$stats['DATA'] = parent::select_all();
				
				$this->q->reset("field,group");
				$this->q->addField("DISTINCT YEAR(`horaire_fin`)","years");
				$stats['YEARS'] = parent::select_all();
				
				return parent::stats($stats,$type);
		}
	}
	
	/** Récupère le nombre de tâche dans un intervalle de +/- 2 jours par rapport à la date envoyée
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function liste_tache($infos){
		$this->q->reset()->addField("count(*)","nbr_tache")
						->addField("DATE_FORMAT(horaire_fin,'%d-%m-%Y')","dates")
						->addCondition("DATEDIFF('".date("Y-m-d",strtotime($infos['date']))."',horaire_fin)",2,NULL,"sou_date","<=",false,false,true)
						->addCondition("DATEDIFF('".date("Y-m-d",strtotime($infos['date']))."',horaire_fin)",-2,NULL,"sup_date",">=",false,false,true)
						->addCondition('tache.id_user',ATF::$usr->getID())
						->addCondition("etat",'en_cours')
						->addGroup('dates')
						->addOrder('dates','asc');
	
		$liste_date=array(
							ATF::$usr->date_trans(date('d-m-Y',strtotime('-2 days '.$infos['date'])))=>0
							,ATF::$usr->date_trans(date('d-m-Y',strtotime('-1 day '.$infos['date'])))=>0
							,ATF::$usr->date_trans(date('d-m-Y',strtotime($infos['date'])))=>0
							,ATF::$usr->date_trans(date('d-m-Y',strtotime('+1 day '.$infos['date'])))=>0
							,ATF::$usr->date_trans(date('d-m-Y',strtotime('+2 days '.$infos['date'])))=>0
		);	

		foreach(parent::select_all() as $key=>$item){
			$liste_date[ATF::$usr->date_trans($item['dates'])]=$item['nbr_tache'];
		}

		$liste[]=$liste_date;

		return $liste;
	}
	
	/*
	 * Annule une tâche
 	 * @author Quentin JANON <qjanon@absystech.fr>
 	 * @param id
	 * @return TRUE si vrai, sinon FALSE
	 */	
	function cancel($infos) {
		if (!$infos['id_tache']) return false;
		$d = array("id_tache"=>$this->decryptId($infos['id_tache']),"etat"=>"annule");
		return parent::update($d);
	}
	
	/*
	 * Abandonner une tache
 	 * @author Quentin JANON <qjanon@absystech.fr>
 	 * @param id
	 * @return TRUE si vrai, sinon FALSE
	 */	
	function giveUp($infos) {
		if (!$infos['id_tache']) return false;
		$dest = self::infos_dest($infos['id_tache']);
		if (count($dest)===1) {
			throw new errorATF("Vous êtes le seul sur cette tâche, impossible de vous retirer, veuillez l'annulé",402);
		}
		
		ATF::tache_user()->q->reset()->addField('id_tache_user')->where("id_tache",$infos['id_tache'])->where('id_user',ATF::$usr->getId());
		$id = ATF::tache_user()->select_cell();
		if (ATF::tache_user()->delete($id)) {
			// envoyer le mail aux user concerné.
			foreach ($dest as $k=>$i) {
				if ($i['id_user'] == ATF::$usr->getId()) continue;
				$email = ATF::user()->select($i['id_user'],'email');
				$liste_email.=($liste_email?",":"").$email;
			}
			$mail = new mail(array(
				"optima_url"=>ATF::permalink()->getURL($this->createPermalink($infos['id_'.$this->table])),
				"recipient"=>$liste_email, 
				"objet"=>ATF::user()->nom(ATF::$usr->getID())."a abandonner la tâche n°".$infos['id_'.$this->table],
				"template"=>"tache_giveup",
				"giveup_user"=>ATF::user()->nom(ATF::$usr->getId()),
				"tache"=>self::select($infos['id_tache']),
				"from"=>ATF::$usr->get('email')));
			if($mail->send()){
				ATF::$msg->addNotice(ATF::$usr->trans("tache_plus_assigne",$this->table));
				ATF::$msg->addNotice(ATF::$usr->trans("email_envoye",$this->table));
			}
		}
		return true;
	}
	
	/*
	 * Renvoi les tâche en retard pour un utilisateur
 	 * @author Quentin JANON <qjanon@absystech.fr>
 	 * @param id
	 * @return TRUE si vrai, sinon FALSE
	 */	
	function tacheLate($param) {
		// Après cette ligne, on débloque la session par nous n'y écrirons plus
		ATF::getEnv()->commitSession();

		if (!$param['date'] || $param['date']=="undefined") $param['date'] = date("Y-m-d");
		$param['date'] = date("Y-m-d",strtotime($param['date']));
		$date = $param['date'];
		// Tâche Late
		$this->q->reset() 
			->addField("tache.id_tache","id_tache")
			->addField("tache.tache","tache")
			->addField("societe.id_societe","id_societe")
			->addField("societe.societe","societe")
			->addField("tache.etat","etat")
			->addField("tache.priorite","urgence")
			->addField("DATE_FORMAT(tache.horaire_fin,'%Y-%m-%d')","date")
			->addField("CONCAT_WS(' ',user.civilite,user.prenom,user.nom)","createur")
			->addField("DATEDIFF('".$date."',horaire_fin)","tpsRetard")
			->addJointure('tache','id_tache','tache_user','id_tache')
			->addJointure('tache','id_user','user','id_user')
			->addJointure("tache","id_societe","societe","id_societe")
			->addCondition("tache.etat",'en_cours')
			->addCondition('tache_user.id_user',ATF::$usr->getID())
			->addCondition('tache.horaire_fin',$date,NULL,"sup",'<')
			->setCount()
			->addOrder("tpsRetard","desc");
			
		$lignes = parent::select_all();
		foreach ($lignes['data'] as $k_=>$i_) {
			$lignes['data'][$k_]['urgenceFlag'] = self::getFlagPath($i_['urgence']);
			$lignes['data'][$k_]['id_tache_crypted'] = self::cryptId($i_['id_tache']);
			$lignes['data'][$k_]['id_societe_crypted'] = ATF::societe()->cryptId($i_['id_societe']);
			$concernes = self::infos_dest($i_['id_tache']);
			$lignes['data'][$k_]['nbConcerne'] = count($concernes);
			foreach ($concernes as $vals) {
				$u = ATF::user()->select($vals['id_user']);
				$lignes['data'][$k_]['concerne'] .= substr($u['prenom'],0,1).".".$u['nom'].", ";
			}
		}
		return $lignes;
	}
	
	
	/** Selectionne les taches correspondant à une date et au user connecté 
	* SPECIAL POUR APPEL EN AJAX
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param date $date
	*/
	public function tachesImminentes($param){
		// Après cette ligne, on débloque la session par nous n'y écrirons plus
		ATF::getEnv()->commitSession();

		if (!$param['date'] || $param['date']=="undefined") $param['date'] = date("Y-m-d H:i:s");
		$param['date'] = date("Y-m-d H:i:s",strtotime($param['date']));
		$param['nbJour'] = ATF::$usr->get('custom','dashBoard','tache','nbJour');
		if (!$param['nbJour']) $param['nbJour'] = 5;
		$nbJour = $param['nbJour'];
		
		// Tâche 
		for ($i=0; $i<$nbJour; $i++) {
			$d = strtotime('+'.$i.' days',strtotime($param['date']));
			if (date("N",$d)==6 || date("N",$d)==7) {
				$nbJour++;
				continue;
			} 
			$dateTmp = date("Y-m-d",$d);
			$this->q->reset()
				->addField("tache.id_tache","id_tache")
				->addField("tache.tache","tache")
				->addField("societe.id_societe","id_societe")
				->addField("societe.societe","societe")
				->addField("tache.etat","etat")
				->addField("tache.priorite","urgence")
				->addField("DATE_FORMAT(tache.horaire_fin,'%Y-%m-%d')","date_fin")
				->addField("CONCAT_WS(' ',user.civilite,user.prenom,user.nom)","createur")
				->addJointure('tache','id_tache','tache_user','id_tache')
				->addJointure('tache','id_user','user','id_user')
				->addJointure("tache","id_societe","societe","id_societe")
				->addCondition('tache.horaire_fin',$dateTmp."%",NULL,NULL,'LIKE')
				->addCondition('tache_user.id_user',ATF::$usr->getID())
				->setCount()
				->addOrder("date_fin","asc");
			
			$r = parent::select_all();
			foreach ($r['data'] as $k_=>$i_) {
				$r['data'][$k_]['urgenceFlag'] = self::getFlagPath($i_['urgence']);
				$r['data'][$k_]['id_tache_crypted'] = self::cryptId($i_['id_tache']);
				$r['data'][$k_]['id_societe_crypted'] = ATF::societe()->cryptId($i_['id_societe']);
				$concernes = self::infos_dest($i_['id_tache']);
				$r['data'][$k_]['nbConcerne'] = count($concernes);
				foreach ($concernes as $vals) {
					$u = ATF::user()->select($vals['id_user']);
					$r['data'][$k_]['concerne'] .= substr($u['prenom'],0,1).".".$u['nom'].", ";
				}
			}
			
			$r["libelle"] = ATF::$usr->date_trans($dateTmp,true,true);
			$lignes['count'] += $r['count'];

			$lignes['lignes'][] = $r;

		}
		return $lignes;
	}
	
	
	/**
	* Permet de séléctionner les enregistrements qui ont été créer depuis la dernière connection de l'utilisateur
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param bool c retourne le compte si TRUE sinon retourne les data
	*/
	function getSinceLastConnection($c=false) {
		$this->q->reset();
		$this->q->addCondition("date",ATF::$usr->get('date_connection'),"OR",NULL,">=");
		$this->q->addJointure('tache','id_tache','tache_user','id_tache');
		$this->q->addCondition('tache_user.id_user',ATF::$usr->getID());
		if ($c) $this->q->setCountOnly();
		
		return $this->sa(); 
	}
	
	/**
    * Retourne la valeur par défaut spécifique aux données passées en paramètres
    * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param string $field
	* @return string
    */
	public function default_value($field){
		if(ATF::_r('id_suivi')){
			$suivi=ATF::suivi()->select(ATF::_r('id_suivi'));
			switch ($field) {
				case "id_societe":
					return $suivi["id_societe"];
					break;
				case "id_affaire":
					return $suivi["id_affaire"];
					break;
				case "tache":
					return $suivi["texte"];
					break;
			}
		}
		return parent::default_value($field);
	}	 

	/**
    * Postpone une tâche de X jours
    * @author Quentin JANON <qjanon@absystech.fr>
	* @param array $infos $_POST
	* @return boolean
    */
	public function postpone($infos){
		if (($infos['postponeValue'] || $infos['postponeValue2']) && $infos['id_tache']) {
			
			if ($infos['postponeValue']) {
				$horaire_fin = $this->select($infos['id_tache'],"horaire_fin");
				$ref = strtotime($horaire_fin);
				
				// On recupere le jour de l'horaire de fin, on lui ajoute les X jours en numérique, si ça dépasse 6 alors on ajout 2 au postponevalue.
				$j = date("N",$ref)+$infos['postponeValue'];
				if ($j>=6) $infos['postponeValue']+=2;
				
				$ref2 = strtotime("+".$infos['postponeValue']." days",$ref);
				$tache['horaire_fin'] = date("Y-m-d H:i:s",$ref2);
			} else {
				$tache['horaire_fin'] = date("Y-m-d H:i:s",strtotime($infos['postponeValue2']));
			}
			$tache['id_tache'] = $infos['id_tache'];
			if ($this->u($tache)) {
				if ($infos['postponeValue']) {
					ATF::$msg->addNotice(ATF::$usr->trans("tache_postpone")." ".$infos['postponeValue']." ".ATF::$usr->trans("jours"));
				} else {
					ATF::$msg->addNotice(ATF::$usr->trans("tache_postpone2")." ".$infos['postponeValue2']);
				}
			}
			return true;
		}
		return false;
	}	 

	public function getFlagPath($urgence) {
		$r = ""; 
		switch ($urgence) {
			case "petite":
				$r = '<img src="'.ATF::$staticserver.'/images/icones/flags/blue.png">&nbsp;';
			break;
			case "moyenne":
				$r = '<img src="'.ATF::$staticserver.'/images/icones/flags/orange.png">&nbsp;';
			break;
			case "grande":
				$r = '<img src="'.ATF::$staticserver.'/images/icones/flags/red.png">&nbsp;';
			break;
		}
		return $r;
	}



		
	
};
?>