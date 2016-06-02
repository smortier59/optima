<?php
/**
* Classe user_ATF
* @package ATF
* Cet objet permet La manipulation de l'utilisateur connecté, le login de l'application
* C'est un objet central de l'application
*/

class user extends classes_optima {
	/**
	* Colonnes SELECT ALL
	* @var mixed
	*/
	public $colonnes;

	/**
	* Nom des champs
	* @var mixed
	*/
	public $field_nom;

	/**
	* Constructeur
	* @todo Trouver plus propre pour le try catch de filedstructure...
	*/
	public function __construct($table_or_id=NULL) {
		//Appel du constructeur de classes
		parent::__construct($table_or_id);

		//Définition de la table
		$this->table = __CLASS__;

		/*Gestion des colonnes (??DEPRECATED ATF4 ??)*/
		$this->colonnes['fields_column'] = array(
			 'user.login'
			 ,'user.email'=>array("renderer"=>"email")
			 ,'user.ville'
			 ,'user.id_profil'=>array("width"=>150,"align"=>"center")
			 ,'user.date_activity'=>array("width"=>100,"align"=>"center")
		 );//Colonnes SELECT ALL
		$this->colonnes['preference'] = array('civilite','prenom','nom','adresse','adresse_2','adresse_3','ville','gsm','email','id_pays');

		$this->colonnes["primary"] = array(
			"nom"
			,'prenom'
			,'login'
			,'password'
			,'id_societe'
			,'id_agence'
			,'etat'
			,'id_profil'
			,"id_superieur"
		);

		$this->colonnes['panel']['coordonnees'] = array(
			"adresse"
			,'gsm'
			,"email"
		);

		$this->colonnes['panel']['telephonie'] = array(
			'id_phone'
			//,'ast_server'
		);

		//IMPORTANT, complète le tableau de colonnes avec les infos MYSQL des colonnes
		try {
			$this->fieldstructure();	/*		log::logger($this->colonnes['fields_column'],'qjanon');	*///		$this->tables_de_jointure = array("domaine");
		} catch (errorATF $e) {
			// Si pas de table user, impossible de se logger, mais on va créer un "user::$logged=false"
		}
		$this->panels['coordonnees'] = array("visible"=>true);
		$this->panels['telephonie'] = array("visible"=>true);

		$this->color["etat"]["inactif"] = "#AAAAAA";

		$this->colonnes['bloquees']['insert'] =  array('date','etat','date_connection','date_activity','custom');
		$this->colonnes['bloquees']['update'] =  array('date','date_connection','date_activity','custom');
		$this->colonnes['bloquees']['select'] =  array('password','custom',"adresse_2","adresse_3","ville","cp","id_pays","civilite","prenom");
		$this->colonnes['bloquees']['export'] =  array('password','custom');

		$this->autocomplete = array(
			"field"=>array("user.prenom","user.nom")
			,"show"=>array("user.civilite"," ","user.prenom"," ","user.nom")
			,"popup"=>array("user.nom","user.prenom","societe.societe")
		);
		$this->field_nom = "%prenom% %nom%";
		$this->foreign_key["id_superieur"] = "user";

		$this->addPrivilege("saveOuvertureToolbar");
		$this->addPrivilege("preferenceAccueil","update");
		$this->addPrivilege("createShorcutContainer");
		$this->addPrivilege("AJAXgetFilters");
		$this->no_update_all = false; // Pouvoir modifier massivement
		//$this->formExt=true;
		$this->helpMeURL = "http://wiki.optima.absystech.net/index.php/Gestion_des_utilisateurs";
	}

	/**
	* Surcharge de la méthode insert dans le cas d'une attribution de profil
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed=NULL){
		$this->infoCollapse($infos);
		if ($infos["password"]) {
			try {
				ATF::$usr->sanitize_password($infos["password"]);
			} catch (Exception $e) {
				if (get_class($e) == "errorATF") {
					throw $e;
				}
			}
		}

		//traitement à effecteur seulement si le profil attribué est différent de celui courant pour des questions d'optimisation
		if(is_array($infos) && isset($infos['id_profil']) && ATF::$usr->get('id_profil')!=$infos['id_profil']){
			$this->verif_profil($infos['id_profil']);
		}
		$return = parent::insert($infos,$s,$files,$cadre_refreshed);
		if($return){


			//Envoi du mail suite à la création d'un nouveau user
			$mail = new mail(array( "recipient"=> "dev@absystech.fr",
									"objet"=>"[".ATF::$codename."] Ajout d'un nouvel utilisateur",
									"template"=>"newUser",
									"message"=>"Un nouvel utilisateur viens d'etre ajouté sur Optima.<br />Username : ".$infos["login"],
									"from"=>"dev@absystech.fr"));
			$mail->send();
		}
		return $return;
	}

	/**
	* Surcharge de la méthode update dans le cas d'un changement de profil
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function update($infos,&$s,$files=NULL,&$cadre_refreshed=NULL){
		$this->infoCollapse($infos);

		if ($infos["password"]) {
			try {
				ATF::$usr->sanitize_password($infos["password"]);
			} catch (errorATF $e) {
				throw $e;
			}
		}


		//traitement à effecteur seulement si le profil a changer pour des question d'optimisation
		$profil=$this->select($this->decryptId($infos['id_user']),'id_profil');
		if(is_array($infos) && isset($infos['id_profil']) && $infos['id_profil']!=$profil){
			$this->verif_profil($infos['id_profil']);
		}
		return parent::update($infos,$s,$files,$cadre_refreshed);
	}

	/**
	* Check si la personne logué a un profil qui contient tous les droits sur les modules, que le profil a attribuer
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param $int $id_profil
	*/
	public function verif_profil($id_profil){
		if(!ATF::$usr->isGod()) { // Bypass pour l'utilisateur god
			ATF::profil_privilege()->q->reset()
								->addField("DISTINCT(module.module)","nom")
								->setStrict()
								->addCondition('profil_privilege.id_module','pp_submit.id_module',NULL,'id_module',"=",false,false,true)
								->addCondition('pp_submit.id_profil',ATF::$usr->get('id_profil'),NULL,'id_profil')
								->addSuperCondition("id_module,id_profil","AND","A")
								->addJointure('profil_privilege','id_privilege','profil_privilege','id_privilege','pp_submit',NULL,NULL,'A')
								->addJointure('profil_privilege','id_module','module','id_module')
								->addCondition('profil_privilege.id_profil',ATF::profil()->decryptId($id_profil))
								->addCondition('pp_submit.id_profil_privilege',NULL,NULL,false,"IS NULL");
			$result = ATF::profil_privilege()->select_all();
			// echappement des modules qui n'ont pas besoin de droit
			// Il faudra ptet a terme regarder le CONTROLLED BY et jouer avec ça.
			foreach ($result as $k=>$i) {
				if ($i['nom']=="accueil") unset($result[$k]);
				if ($i['nom']=="preference") unset($result[$k]);
			}
			// Si le profil que l'on veut attribuer a des droits que celui actuel n'a pas, alors on ne peut pas le changer
			if(count($result)>0){
				foreach($result as $key=>$item){
					$nom.=($nom?",":"").ATF::$usr->trans($item['nom'],'module');
				}
				throw new errorATF(ATF::$usr->trans('not_change','user')." (".$nom.")",387);
			}
		}
	}

	/**
	* Retourne uniquement les informations nécessaires à la création du menu déroulant de la hotline
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @return array
	*/
	public function options_transfert() {
		$this->q->reset()
			->addField($this->table.".id_".$this->table)
			->addCondition('user.etat','normal','AND')
			->addCondition('user.pole',NULL,'AND',false,'IS NOT NULL');
		if ($data = parent::select_all()) {
			foreach($data as $key => $item) {
				$return[$item[$this->table.".id_".$this->table."_fk"]] = $item[$this->table.".id_".$this->table];
			}
		}
		return $return;
	}

	/**
	* Teste si l'utilisateur a le droit de faire l'évênement, et retourne un booleen
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $module Le module cible du test de privilege
	* @param string $event
	* @param array $s ($_SESSION habituellement attendu)
	* @return boolean
	*/
	public function eventPrivilege(&$event) {
		switch ($event) {
			case "setPreferences":
			case "updateCustom":
			case "preference":
				return true; // On peut toujours le faire
		}
		return parent::eventPrivilege($event); // Par défaut
	}

	/**
	* Met à jour les variables de custom
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>, Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @date 2009-01-15
	* @param array $infos ($_POST habituellement attendu)
	*	string $infos[variable1]
	*	string $infos[variable2]
	*	...
	* peut être nul dans le cas d'une sauvegarde d'ouverture d'onglet/panel et ajout/suppression d'onglet
	* @return boolean
	*/
	public function updateCustom($infos) {
		return ATF::$usr->updateCustom($infos);
	}

	/**
	* Donne la liste des utilisateurs pour une utilisation dans un select HTML
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param boolean $inactif Vrai si on désire avoir les utilisateurs inactifs
	* @return array la liste des utilisateurs
	*/
	public function html_options($inactif=false){
		$this->q->reset()->addField("user.id_user");
		if(!$inactif){
			$this->q->addCondition("etat","normal");
		}
		$data=$this->sa();
		$tmp=array();
		foreach($data as $user){
			$tmp[$this->cryptId($user["user.id_user_fk"])]=$user["user.id_user"];
		}
		return $tmp;
	}

	/**
	* Surcharge de preference pour mise à jour du custom
	* @author QUENTIN JANON <qjanon@absystech.fr>
	*/
	public function preferenceAccueil($infos,&$s,$files=NULL,&$cadre_refreshed=NULL){
		ATF::$usr->preferenceAccueil($infos);
	}

	/* Autocomplete sur les utilisateur qui ont leur état actif
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos ($_POST habituellement attendu)
	*	string $infos[recherche]
	* @param boolean $reset VRAI si on reset lme querier, FAUX si on a initialisé qqch de précis avant...
	* @return string HTML de retour
	*/
	public function autocomplete($infos,$reset=true) {
		if ($reset) {
			$this->q->reset();
		}
		$this->q
			->addOrder("user.nom","asc")
			->where("user.etat","normal")
			->where("user.login","absystech","OR",false,"!=");
		return parent::autocomplete($infos,false);
	}

	/**
	* Permet d'appeler le getFilters en ajax
	* @author QJ <qjanon@absystech.fr>
	*/
	public function AJAXgetFilters($params,&$s) {
		$result = ATF::$usr->getFilters($params['tableTo']);
		foreach ($result as $k=>$i) {
			if (is_array($i)) {
				foreach ($i as $k_=>$i_) {
					$return[str_replace("public_","",$k_)] = $i_;
				}
			}
		}
		ATF::$json->add("totalCount",count($return));
		return $return;
	}

	/**
	* Surcharge de preference pour mise à jour du custom
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function setPreferences($infos){
		return ATF::$usr->setPreferences($infos);
	}

	/**
	* Récupérer l'id_user a partir du login
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $login
	* @return int
	*/
	public function getIDFromLogin($login){
		$this->q->reset()->setStrict()->where('login',$login)->addField('id_user');
		return $this->select_cell();
	}

	/**
	* Récupérer l'id_user a partir de l'email
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $email
	* @return int
	*/
	public function getIDFromEmail($email){
		if ($email) {
			$this->q->reset()->setStrict()->where('email',$email)->addField('id_user');
			return $this->select_cell();
		}
	}

	/**
	* Donne les préférences actuelles de l'utilisateur
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @return array les préférences
	*/
	public function getPreferences(){
		//patch pour absystech en attente de jérém si il faut l'appliquer ailleurs
		if(ATF::$codename=="absystech"){
			//Téléphones de l'utilisateur
			ATF::phone()->q->reset()->addField("id_phone,phone")->where("id_user",ATF::$usr->getID());
			$phones=ATF::phone()->sa();
			$this->preference["phone"]["phone"]["data"]=array();
			foreach($phones as $phone){
				$this->preference["phone"]["phone"]["data"][$phone["id_phone"]]=$phone["phone"];
			}
			$this->preference["phone"]["phone"]["default"]=ATF::$usr->get("id_phone");
		}
		return $this->preference;
	}

	/**
	* Méthode spéciale par défaut "saCustom"
	* Appel la méthode de classe particulière à utiliser,  si $method =flase on utilise select_all
	* Utilisation dans generic_select_all
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $s : la session
	* @param string $method : methode de classe particulière à utiliser
	* @return array Résultat de la requête
	*/
	public function select_data(&$s,$method=false){
		if (!$method) {
			$method="saCustom";
		}
		return parent::select_data($s,$method);
	}

	/** Filtre le user absystech
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/
	public function saCustom() {
		$this->q->where("user.login","absystech","OR",false,"!=");
		$this->q->where("user.login","absystech","OR",false,"!=");
		return $this->select_all();
	}

	/** Login user REST
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/
	public function _login($get,$post) {
		$schema = $post["s"];
		$login = $post["u"];
		$password = $post["p"];
		$api_key = $post["k"];

		$data = array(
			"schema"=>$schema,
			"login"=>$login,
			"password"=>$password,
			"api_key"=>$api_key
		);

		if (ATF::$usr->login($data)) {
			$session = ATF::$usr->getInfos();
			$session["codename"] = ATF::$usr->website_codename;

			if (ATF::getClass("telescope")) {
				ATF::telescope()->q->reset()->where("codename", ATF::$usr->website_codename);
				$session["telescope"] = ATF::telescope()->select_row();
			}


			$session["societe"] = ATF::constante()->getValue('__SOCIETE__');
			$session["privileges"] = ATF::$usr->getPrivileges();
			$session["custom"] = ATF::$usr->getCustom();
			unset($session["menu"],$session["password"]);
			header('X-Optima-Session-Name: '.session_name());
			header('X-Optima-Session-Id: '.session_id());
			header('X-Optima-Societe: '.ATF::$usr->website_codename);
			ATF::resetSingletons();
			return $session;
		} else {
			throw new Exception("Credentials refused", 500);
		}
	}

	/**
	 * Methode de login pour le portail partenaire via un user partenaire authentifié par user.api_key et le contact authentifié par login et mot de passe sur la table contact
	 * @param  [array] $get
	 * @param  [array] $post
	 * @return [array] $session
	 */
	public function _loginPartenaire($get,$post) {
		// Authentification sur utilisateur partenaire
		$session = $this->_login($get,$post);

		// Authentification du contact
		if ($contact = ATF::contact()->login($post)) {

			$contact = array_intersect_key($contact,array_flip(array('id_contact','id_societe','nom','prenom','email','etat','lead','id_filiale','login'))); // Ne conserver que ces champs
			$contact["id_user"] = $session["id_user"];

			// Ajout des infos nécessaires sur l'entité
			$societe = ATF::societe()->select($contact["id_societe"]);
			$contact["societe"] = array_intersect_key($societe,array_flip(array('id_societe','societe','revendeur','id_filiale'))); // Ne conserver que ces champs

			$contact["societe"]['logo'] = file_exists(ATF::societe()->filepath($societe["id_filiale"],"logo")) ? true : false;

			ATF::$usr->set('contact', $contact);
		}

		return $contact;
	}


/* PARTIE DES FONCTIONS POUR TELESCOPE*/


	/** Fonction qui génère les résultat pour les champs d'auto complétion utilisateur
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
	public function _ac($get,$post) {
		$length = 25;
		$start = 0;

		$this->q->reset();

		// On ajoute les champs utiles pour l'autocomplete
		$this->q->addField("id_user")->addField("nom")->addField("prenom")->addField("pole");

		if ($get['q']) {
			$this->q->setSearch($get["q"]);
		}

		// Speciale clauses where
		if ($get['pole']) {
			$this->q->where("pole",$get['pole']);
		} else {
			$this->q->whereIsNotNull("pole");
		}

		// Clause globale
		$this->q->where("etat","normal");

		// CLause Order
		$this->q->addOrder("prenom");

		$this->q->setLimit($length,$start)->setPage($start/$length);


		$return = $this->select_all();
		if ($get['sortByPole']) {
			foreach ($return as $k=>$i) {
				$r[$i['pole']][] = $i;
			}
			return $r;
		} else {
			return $return;
		}
	}
	/**
	* Permet de récupérer la liste des utilisateurs pour telescope
	* @package Telescope
	* @author Charlier Cyril <ccharlier@absystech.fr>
	* @param $get array Argument obligatoire mais inutilisé ici.
	* @param $post array Argument obligatoire mais inutilisé ici.
	* @return array un tableau avec les données
	*/

	public function _GET($get,$post) {
		// Gestion du tri
		if (!$get['tri']) $get['tri'] = "nom";
		if (!$get['trid']) $get['trid'] = "desc";

		// Gestion du limit
		if (!$get['limit']) $get['limit'] = 30;

		// Gestion de la page
		if (!$get['page']) $get['page'] = 0;

		$colsData = array(
			"user.id_user",
			"user.login",
			"user.civilite",
			"user.nom",
			"user.prenom",
			"user.gsm",
			"user.email",
			"user.newsletter",
			"user.adresse",
			"user.cp",
			"user.ville",
			"user.etat",
			"user.adresse",
			"pays.pays",
			"user.custom",
			"user.id_societe",
			"societe.societe",
			"user.id_agence",
			"agence.agence",
			"user.id_superieur",
			"user.pole",
			"user.id_pays",
			// Charlier Cyril <ccharlier@absystech.fr>
			// error SQL avec id_
			// impossible de recuperer l'id_phone
			//"phone.id_phone",
			"profil.profil",
			"user.id_profil",
		);
		$this->q->reset();
		if($get["search"]){
			header("ts-search-term: ".$get['search']);
			//$this->q->setSearch($get['search']);
			$this->q->orWhere('user.nom','%'.$get['search'].'%','search',"LIKE")
			->orWhere('user.prenom','%'.$get['search'].'%','search',"LIKE")
			->orWhere('user.gsm','%'.$get['search'].'%','search',"LIKE")
			->orWhere('user.email','%'.$get['search'].'%','search',"LIKE");
		}


		if ($get['id_user']) {
			$this->q->where("user.id_user",$get['id_user'])->setLimit(1);

		} else {
			// gestion des filtres
			if ($get['filters']['actif'] == "on") {
				$this->q->where("user.etat","normal");
			}
			$this->q->setLimit($get['limit']);
		}
		$this->q->addField($colsData)
				->from("user","id_pays","pays","id_pays")
				->addJointure("user","id_societe","societe","id_societe")
				->addJointure("user","id_agence","agence","id_agence")
				->addJointure("user","id_profil","profil","id_profil")
				->setCount();
		$data = $this->select_all('user.'.$get['tri'],$get['trid'],$get['page'],true);

		foreach ($data["data"] as $k=>$lines) {
			foreach ($lines as $k_=>$val) {
				if (strpos($k_,".")) {
					$tmp = explode(".",$k_);
					$data['data'][$k][$tmp[1]] = $val;
					unset($data['data'][$k][$k_]);
				}
			}
		}
		// si l'on recupère un seul user, on renvoie directement la premiere ligne du tableau
		if($get['id_user']){
			$return = $data['data'][0];
			if ($return['custom'] != null)
				$return['custom']= unserialize($return['custom']);
		}else{
			header("ts-total-row: ".$data['count']);
			header("ts-max-page: ".ceil($data['count']/$get['limit']));
			header("ts-active-page: ".$get['page']);
			$return = $data['data'];
		}
		 return $return;
	}

	// Fonction a déprécier
	/** Fonction qui récupère les données des utilisateurs pour "mon profil" dans telescope
	* @author cyril CHARLIER <ccharlier@absystech.fr>
	*/
	public function _getInfos($get){
		$this->q->reset()
			->setDimension('row')
			->addField("login")
			->addField("nom")
			->addField("prenom")
			->addField("civilite")
			->addField("gsm")
			->addField("adresse")
			->addField("cp")
			->addField("ville")
			->addField("etat")
			->addField("newsletter")
			->addField("id_pays")
			->addField("email")
			->addField("custom");

		$this->q->where("id_user",ATF::$usr->getID())->setLimit(1);
		$data = $this->select_all();
		$data['custom'] = unserialize($data['custom']);
		return $data;

	}

	/**
	* Permet de modifier un client depuis telescope
	* @author cyril CHARLIER <ccharlier@absystech.fr>
	* @param $get array Argument obligatoire mais inutilisé ici.
	* @param $post array Contient les données envoyé en POST par le formulaire.
	* @return boolean|integer Renvoi l'id de l'enregitrement inséré ou false si une erreur est survenu.
	*/

	public function _PUT($get,$post){
		$input = file_get_contents('php://input');
	    if (!empty($input)) parse_str($input,$post);

		$return = array();
		array_filter($post);
	    // update
        try {
        	// si password est vide on l'unset afin d'update l'user
        	if(!trim($post["password"]))
        		unset($post["password"]);
        	$post["pole"] =implode(",", $post["pole"]);
        	$post["etat"]= ($post["etat"] == "on")?'normal':'inactif';
        	$post["newsletter"]= ($post["newsletter"] == "on")?'oui':'non';
    		$this->update($post);

		} catch (errorATF $e) {
  			throw new errorATF($e->getMessage(),500);
		}
    	$return['result'] = true;
    	$return['id_user'] = $post['id_user'];
       	// Récupération des notices créés
        $return['notices'] = ATF::$msg->getNotices();

        return $return;
	}
	/**
	* Permet d'ajouter un utilisateur
	* @author cyril CHARLIER <ccharlier@absystech.fr>
	* @param $get array Argument obligatoire mais inutilisé ici.
	* @param $post array Contient les données envoyé en POST par le formulaire.
	* @return boolean|integer Renvoi l'id de l'enregitrement inséré ou false si une erreur est survenu.
	*/
	public function _POST($get,$post){
		$input = file_get_contents('php://input');
	    if (!empty($input)) parse_str($input,$post);
		array_filter($post);

		$return = array();
        try {
        	// si password est vide on l'unset afin d'update l'user
        	$post["etat"]= ($post["etat"] == "on")?'normal':'inactif';
        	$post["newsletter"]= ($post["newsletter"] == "on")?'oui':'non';
        	if(!trim($post["password"]))
        		unset($post["password"]);
        	$post["pole"] =implode(",", $post["pole"]);
        	$result = $this->insert($post);

		} catch (errorATF $e) {
  			throw new errorATF($e->getMessage(),500);
		}
        $return['result'] = true;
        $return['id_user'] = $result;
        return $return;
	}


	/**
	* Permet de modifier les paramètres d'un utilisateur depuis telescope
	* @author cyril CHARLIER <ccharlier@absystech.fr>
	* @param $get array Argument obligatoire mais inutilisé ici.
	* @param $post array Contient les données envoyé en POST par le formulaire.
	* @return boolean|integer Renvoi l'id de l'enregitrement inséré ou false si une erreur est survenu.
	* @return String
	*/

	public function _PutParam($get,$post){
	    //log::logger(__FUNCTION__,'ccharlier');
		$input = file_get_contents('php://input');
	    if (!empty($input)) parse_str($input,$post);
		$return = array();
        if (!$post) throw new Exception("POST_DATA_MISSING",500);
    	// Si on fait un update de l'user
    	else {
	        // Check des champs obligatoire
			if (!$post['custom']) throw new errorATF(ATF::$usr->trans('preferences_missing','user'));
	        // Récuperation du custom dans la db
	        $this->q->reset()
					->setDimension('cell')
					->addField("custom")
					->where("id_user",ATF::$usr->getID())->setLimit(1);

			// on unserialize le custom dans la db
			$dbCustom = unserialize($this->select_all());
			// merge des 2 tableaux
			$dbCustom["params"] = array_merge($dbCustom["params"], $post["custom"]);
			$this->updateCustom($dbCustom);

        	$return['id_user'] = ATF::$usr->getID();
        	$return['params'] = $dbCustom["params"];
    	}
           	// Récupération des notices créés
        $return['notices'] = ATF::$msg->getNotices();

        return $return;
	}

	/** Fonction qui récupère les parametres dans telescope
	* @author cyril CHARLIER <ccharlier@absystech.fr>
	*/
	public function _getParams($get){
		$this->q->reset()
			->setDimension('cell')
			->addField("custom");

		$this->q->where("id_user",ATF::$usr->getID())->setLimit(1);
		$data = $this->select_all();

		$data['custom'] = unserialize($data['custom']);
		return $data;

	}
}
