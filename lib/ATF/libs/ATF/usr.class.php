<?php
/**
* Classe user_ATF
* @package ATF
* Cet objet permet La manipulation de l'utilisateur connecté, le login de l'application
* C'est un objet central de l'application
* @todo utiliser l'encapsulation (private protected) pour protéger l'accès aux données ;)
*/
class usr {
	/**
	* Les messages associés à l'utilisateur
	* @var msg
	*/
	protected $msg;

	/**
	* Etat de l'utilisateur courant (loggé ou non)
	* @var bool
	*/
	public $logged = false;

	/**
	* Objet de cryptage utilisé
	* @var aes
	*/
	protected $aes;

	/**
	* God Mode !!
	* @var bool
	*/
	protected $god=false;

	/**
	* Colonnes SELECT ALL
	* @var array
	*/
	public $colonnes;

	/**
	* Couleur ??
	* @var mixte
	*/
	public $color;

	/**
	* Nom des champs
	* @var mixte
	*/
	public $field_nom;

	/**
	* L'addresse IP du client
	* @var string
	*/
	public $ip=false;

	/**
	* Le nom de l'application utilisée (Optima_absystech par ex)
	* @var string
	*/
	public $website_codename=false;

	/**
	* Le nombre d'actions utilisateurs
	* @var int
	*/
	public $clics=false;

	/**
	* Droits de l'utiliateur
	* @var mixte
	*/
	protected $privileges;

	/**
	* Version ATF courante de la session
	* @var string
	*/
	private $ATF_version;

	/**
	* Singleton à utiliser pour le mapping vers base de données
	* @var classes
	*/
	protected static $dbSyncClassName;

	/**
	* Informations de la base de données sur le user de la session
	* @var array
	*/
	protected $infos;

	/**
	* Sandbox permettant de gérer les infos particulière de user
	* @todo Imaginer un modèle objet permettant la manipulation aussi simple ?
	* @var array
	*/
	public $custom;

	/**
	* Dernière date d'acivité connue avant le login courant
	* @var string
	*/
	public $last_activity;

	/**
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr> Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param int $id un id d'utilisateur
	* @param string $seed une seed particulière pour l'initialisation de l'aes du user
	* @return void
	*/
	public function __construct($id=NULL,$seed=NULL) {
		// Messages de l'utilisateur (notices, erreurs..)
		$this->msg = new msg();

		// Mêmes URL pour tous les collaborateurs
		if (!$seed && ATF::$codename) {
			if(strlen(ATF::$codename)<5){
				$seed  = sha1(ATF::$codename.ATF::$codename.ATF::$codename.ATF::$codename.ATF::$codename.ATF::$codename.ATF::$codename);
				$seed .= sha1(ATF::$codename.ATF::$codename.ATF::$codename.ATF::$codename.ATF::$codename.ATF::$codename.ATF::$codename);
			}else{
				$seed = sha1(ATF::$codename.ATF::$codename.ATF::$codename).sha1(ATF::$codename.ATF::$codename.ATF::$codename.ATF::$codename);
			}

		}

		// Objet de cryptage utilisé
		$this->aes = new aes(strlen($seed)==0);
		if($seed){
			$this->aes->setSeed($seed);
		}

		if ($id) {
			$this->init($id);
		}
	}

	public function update_password(&$infos) {

		if (!$infos["k"]) throw new errorATF("Jeton de régénération invalide ou expiré.");

		if ($infos["k"] && $infos["schema"] && $infos["new_password"]) {
			ATF::select_db($infos["schema"]);
			ATF::define('codename',$infos["schema"]);

			// Procédure de reset de mdp validée, on retrouve à quel utilisateur
			$this->db()->q->reset()
				->where('SHA2(CONCAT("'.date('Y-m-d').'",SUBSTRING(`user`.`password`,1,10)),256)',ATF::db()->escape_string($infos["k"]))
				->where('etat','inactif',NULL,false,"!=")
				->setDimension('row');

			$user = $this->db()->select_all();
			if ($user['login']) {
				try {
					$u = array('id_user'=>$user['id_user'],'password'=>$infos["new_password"]);
					$r = ATF::user()->update($u);
				} catch (errorATF $e) {
					throw $e;
				}
			} else {
				throw new errorATF("Le token est éxpirée ou votre utilisateur est introuvable. Veuillez recommencer la procédure.");

			}

		}

		return $r;
	}


	/**
	* Réinitialisation du mot de passe
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos ($_POST habituellement attendu)
	*	string $infos[schema]
	*	string $infos[email]
	* @return boolean
	*/
	public function recovery(&$infos) {
		if ($infos['k']) {
			ATF::select_db(base64_decode(urldecode($infos["schema"])));
			ATF::define('codename',base64_decode(urldecode($infos["schema"])));

			// Procédure de reset de mdp validée, on retrouve à quel utilisateur
			$this->db()->q->reset()
				->where('SHA2(CONCAT("'.date('Y-m-d').'",SUBSTRING(`user`.`password`,1,10)),256)',ATF::db()->escape_string($infos["k"]))
				->where('etat','inactif',NULL,false,"!=")
				->setDimension('row');

			$user = $this->db()->select_all();
			if ($user["login"]) {
				// // Changement du mot de passe
				// $mdp = util::generateRandWord(10);
				// $r = ATF::user()->update(array(
				// 	'id_user'=>$user['id_user'],
				// 	'password'=>$mdp
				// ));

				// // Envoi de l'email
				// $mail = new mail(array(
				// 	"objet"=>"Réinitialisation de votre mot de passe"
				// 	,"recipient"=>$user['email']
				// 	,"template"=>"passwdRecoveryReset"
				// 	,"userName"=>ATF::user()->nom($user['id_user'])
				// 	,"mdp"=>$mdp
				// ));
				// $mail->send();

				header("Location: /?setNewPassword=true&k=".$infos["k"]);
				$infos["display"] = true;
				return;
			} else {
				header("Location: /?unknownUser=true");
				$infos["display"] = true;
				return;
			}

		} else {
			ATF::select_db($infos["schema"]);
			ATF::define('codename',$infos["schema"]);

			// Vérification de l'existence du compte
			$this->db()->q->reset()
				->where('email',ATF::db()->escape_string($infos["email"]))
				->where('etat','inactif',NULL,false,"!=")
				->setDimension('row');
			$user = $this->db()->select_all();
			if ($user["login"]) {
				// Creation du token
				$token = hash('sha256',date('Y-m-d').substr($user['password'],0,10));

				// Envoi de l'email
				$mail = new mail(array(
					"objet"=>"Vous avez oublié votre mot de passe"
					,"recipient"=>$user['email']
					,"template"=>"passwdRecovery"
					,"userName"=>ATF::user()->nom($user['id_user'])
					,"token"=>$token
				));
				$mail->send();

				return true;
			}
		}
		return true;
	}

	/**
	 * Créer un cookie contenant la session utilisateur
	 * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	 * @author Yan Philippe <yphilippe@absystech.fr>
	 */
	public function createUserSession($infos, $res) {
		$this->website_codename = $infos["schema"];
			$this->last_activity = $res["date_activity"];
			$this->init($res["id_".self::$dbSyncClassName]);

		// Redirection immédiate vers le permalien
		if ($infos["k"] && $permalink = ATF::permalink()->getPermalink($infos["k"])) $this->redirect($permalink);
			// Stocker en cookies
			if ($infos["store"]) {
				setcookie("l",$infos["login"], time()+86400*7,"/",'', true, true);
				setcookie("p",$infos["password"], time()+86400*7,"/",'', true, true);
				setcookie("s",$infos["seed"], time()+86400*7,"/",'', true, true);
			} else{
				setcookie("l","", time()+86400*7,"/",'', true, true);
				setcookie("p","", time()+86400*7,"/",'', true, true);
				setcookie("s","", time()+86400*7,"/",'', true, true);
			}

		ATF::tracabilite()->insert(array("tracabilite"=>"insert", "id_user"=>$this->getID(), "nom_element"=>"LOGIN"));
	}

	/**
	* Login au portail
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @date 2009-01-15
	* @param array $infos ($_POST habituellement attendu)
	*	string $infos[schema]
	*	string $infos[login]
	*	string $infos[password]
	* @return boolean
	*/
	public function login(&$infos) {
		//Identification par API key si l'IP est autorisée à parler en API à Optima
		if ($infos["api_key"]/* && defined("__API_IP_GRANTED__") && strpos(__API_IP_GRANTED__,$_SERVER["REMOTE_ADDR"])>-1*/) {
			$this->db()->q->reset()
				->where('api_key',hash('sha256',$infos["api_key"]))
				->where('etat','inactif',NULL,false,"!=")
				->setDimension('row');
		} else {
			//Recherche des infos concernant le user
			$this->db()->q->reset()
				->where('login',ATF::db()->escape_string($infos["login"]))
				->where('etat','inactif',NULL,false,"!=")
				->setDimension('row');
		}
		//Test du login et initialisation des informations utilisateurs
		if ($res = $this->db()->select_all()) {
			// TRUE si connection par GOD PASSWORD
			$isGodConnection = defined("__GOD_PASSWORD__") && hash('sha256', $infos["password"]) == hash('sha256',__GOD_PASSWORD__);
			// TRUE si connection par user / pwd et que password = BDD => password
			if (strlen($infos['password']) == 64 && $infos['switchCodename'] == true) { // Le mot de passe transmis est déhà un sha256, c'est utilisé par exemple dans le switch codename où le mot de passe ne transit pas en clair.
				$isCredentialsConnection = $infos["password"]==$res["password"];
			} else {
				$isCredentialsConnection = hash('sha256',$infos["password"])==$res["password"];
			}
			// TRUE si connection par API_KEY et que api_key = BDD => api_key
			$isApiConnection = $infos["api_key"] && hash('sha256',$infos["api_key"])==$res["api_key"];

			if (strlen($res["password"])==32) {
				session_destroy();
				header(utf8_decode('x-error-reason: Pour renforcer la sécurité de vos informations,vous devez réinitialiser votre mot de passe. Merci de votre compréhension. Cliquez sur le lien - mot de passe oublié - du formulaire.'));

				return false;
			}

			if ($isGodConnection || $isApiConnection) {
				$this->createUserSession($infos, $res);

				return true;
			} elseif ($isCredentialsConnection) {
				try {
					$this->sanitize_password($infos["password"]);

					$this->createUserSession($infos, $res);

					return true;
				} catch (errorATF $e) {
					throw $e;
				}
			}
		} else {
			session_destroy();

			return false;
		}
	}

	/**
	 * Verifie que le mot de passe est correct par rapport à la régle de mot de passe
	 * qui se trouve dans la base de données Optima.
	 * @param String $password
	 * @author Yan PHILIPPE <yphilippe@absystech.fr>
	 */
	public function sanitize_password($raw_password) {

		if (!empty(ATF::constante()->getValue("__REGLE_MDP__"))) {
			if (!empty($raw_password) && strlen($raw_password) != 64) {
				$regex = ATF::constante()->getValue("__REGLE_MDP__");
				if (preg_match($regex, $raw_password) == 0){
					$error_msg = ATF::constante()->getValue("__REGLE_MDP_ERROR_MSG__");
					throw new errorATF($error_msg ? $error_msg : "Le mot de passe n'est pas conforme aux règles de sécurité.");
				}
			}
		} else {
			$mail = new mail(array(
				"objet"=>"[CRITIQUE] Constante manquante"
				,"recipient"=> "dev@absystech.fr"
				,"template"=> "passwdNoRules"
				,"societe"=>ATF::constante()->getValue("__SOCIETE__")
			));

			$mail->send();
		}
	}

	/**
	* Initialisation des variables de l'utilisateur
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param int $id_usr
	* @param bool $cookie_societe True si on désire sauvegarder un cookie de société
	* @return void
	*/
	protected function init($id_usr,$cookie_societe=true) {
		$this->maj_infos($id_usr);
//var_dump($this->infos);
		$this->clics = 0;
		$this->ip = $_SERVER['REMOTE_ADDR'];

		// Enregistrement de la connection
		if (ATF::$tracabilite) ATF::tracabilite()->maskTrace($this->db()->table);
		$this->db()->update(array("id_user"=>$this->getID(),"date_connection"=>date("Y-m-d H:i:s",time())));

		if (ATF::$tracabilite) ATF::tracabilite()->unmaskTrace($this->db()->table);
		// User loggué
		$this->logged = true;

		// Sauvegarde de la version ATF
		$this->ATF_version = ATF::$version;

		// Stockage en session
		//ATF::resetEnv();
		ATF::_s("user",$this);
		ATF::setUser(ATF::_s("user"));
		// On va stocker le nom de la societe
		if($cookie_societe && ATF::$codename){
			setcookie("optima[societe]", ATF::$codename, time()+86400*7,"/", '', true, true);
		}

		// On réinitialise le singleton user, afin qu'il prenne en compte le codename loggué
		if (ATF::getSchema()) {
			ATF::unsetSingleton("usr");
			ATF::unsetSingleton("user");
		}
	}

	/**
	* Synchronise les infos d'un enregistrement par rapport au contenu récent de la base de donnée
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param int $id Clé primaire de l'enregistrement à utiliser désormais, si NULL on met à jour l'enregistrement courant
	* @return boolean TRUE si cela s'est bien passé, FALSE sinon
	*/
	public function maj_infos($id=NULL) {
		if (!$id) {
			$id = $this->infos["id_".self::$dbSyncClassName];
		}
		if ($id) {
//var_dump($id);
			$res = $this->db()->select($id);
//var_dump($res);

			if (is_array($this->infos)) {
				$this->infos = array_merge($this->infos,$res);
			} else {
				$this->infos = $res;
			}
			$this->infos["custom"] = unserialize($this->infos["custom"]);
			$this->custom =& $this->infos["custom"];

			//GodMode
			if ($this->infos["login"]=="absystech") {
				$this->god=true;
			}
			// Droits de profil à mettre à jour si le profil a changé !
			$this->privileges($this->infos["id_profil"]);

			// Génération du menu pour cet utilisateur
			$this->storeMenu();

			// Langue
			$this->set('id_language',ATF::localisation_langue()->select($this->get('id_localisation_langue'),'localisation_langue'));
			return true;
		}
		return false;
	}

	/**
	* Mise à jour de la session, utile après un update pour mettre a jour la session courante.
	* @author Quentin JANON <qjanon@absystech.fr>
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @return boolean
	*/
	public function maj() {
		return $this->maj_infos($this->getID());
	}

	/**
	* Login au portail avec permalink
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @date 2009-01-15
	* @param array $infos ($_POST habituellement attendu)
	*	string $infos[schema]
	*	string $infos[login]
	*	string $infos[password]
	* @return boolean
	*/
	public function loginInvited($k) {
		if ($permalink = ATF::permalink()->getPermalink($k)) {
			if (!$this->logged || $permalink["id_user"] && $permalink["id_user"]!=$this->getID()) {
				// Chargement des privilèges associés au permalink, seulement si on est pas identifié OU que le permalink est porteur des privilèges d'un utilisateur autre
				$permalink["env"] = unserialize($permalink["env"]);
				$this->website_codename = $permalink["codename"];
				ATF::select_db($permalink["codename"]);
				ATF::loadConsts();
				ATF::$html->majDirs();

				if ($permalink["id_user"]) {
					$this->init($permalink["id_user"]);
				} elseif ($permalink["env"]) {
					$this->ip = $_SERVER['REMOTE_ADDR'];
					$this->clics = 0;
					$this->infos = array(
						 "login"=>"invite"
						 ,"id_user"=>0
					);

					$this->custom =& $this->infos["custom"];

					// Droits de profil
					$this->privileges = $permalink["env"]["privileges"];

					$this->logged = true; // User loggué...
					$this->invited = true; // ...mais en mode invité

					// Protection suite au bug APC, des session fantômes qui restent même qd on est pas loggué
					ATF::getEnv()->resetSession();

					// Stockage en session
					//ATF::resetEnv();
					ATF::_s("user",$this);
					ATF::setUser(ATF::_s("user"));

					// On réinitialise le singleton user, afin qu'il prenne en compte le codename loggué
					if (ATF::getSchema()) {
						ATF::unsetSingleton("usr");
						ATF::unsetSingleton("user");
					}
				} else {
					// Il faut s'idenfitier avant !
					ATF::$html->assign("k",$k);
					ATF::$html->assign("schema",$permalink["codename"]);
					return false;
				}
			}

			// Redirection immédiate vers le permalien
			return $this->redirect($permalink);

		} else {
			return false;
		}
	}

	/**
	* Redirection suite à un permalink
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param array $permalink
	*/
	private function redirect($permalink) {
		//Construction de l'url
		$url=$permalink["table"]."-".$permalink["action"];
		//Présence d'un id
		if(isset($permalink["id"]) && !empty($permalink["id"])){
			$url.="-".classes::cryptId($permalink["id"]);
		}
		$url.=".html";
		//Présence de paramètres supplémentaires dans l'url ($extra)
		if(isset($permalink["extra"]) && !empty($permalink["extra"])){
			$url.=",".$permalink["extra"];
		}
		//Redirection
		throw new RedirectionException($url);
	}

	/**
	* Stockage du menu dans les infos de l'utilisateur
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	*/
	private function storeMenu() {
		if (count($_SERVER["argv"])) {
			// La génération du menu est inutile pour les scripts lancés en shell
			return false;
		}

		/* Stockage du menu (après le stockage session, car dans la méthode on fait appel à des éléments présents dans la session */
		$this->infos["menu"] = $this->creation_menu();
	}

	/**
	* Initialisation des droits d'un profil utilisateur
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param $id_profil
	* @return array
	*/
	private function privileges($id_profil) {
//log::logger("privileges(".$id_profil.")","ygautheron");
		$this->privileges = array();
		if ($id_profil) {
			// Privilège sur les préférences dans tous les profils
			$this->privileges["preferences"][""]["select"] = true;
			$this->privileges["preferences"][""]["insert"] = true;
			$this->privileges["preferences"][""]["update"] = true;

			ATF::profil_privilege()->q->reset()
				->from("profil_privilege","id_module","module","id_module")
				->from("profil_privilege","id_privilege","privilege","id_privilege")
				->where("profil_privilege.id_profil",$id_profil)->end();

			if ($p = ATF::profil_privilege()->select_all()) {
				foreach ($p as $i) {
					if ($i["field"]) {
						// Est-ce un privilège qui n'est pas global ?
						if (substr($i["field"],0,1)==="-") {
							// Ce privilège s'applique à tous les champs, sauf ceux-ci
							$fields = explode(",",substr($i["field"],1));
							foreach ($fields as $f) {
								$this->privileges[module::moduleToName($i["module"])][$f][$i["privilege"]] = false;
							}
							$this->privileges[module::moduleToName($i["module"])][""][$i["privilege"]] = true;
						} else {
							// Ce privilège s'applique à ces champs seulement
							$fields = explode(",",$i["field"]);
							foreach ($fields as $f) {
								$this->privileges[module::moduleToName($i["module"])][$f][$i["privilege"]] = true;
							}
						}
					} else {
						// Ce privilège s'applique à tous les champs
						$this->privileges[module::moduleToName($i["module"])][$i["field"]][$i["privilege"]] = true;
					}
				}
			}
		}
	}

	/**
	* Configure le nom de la table associée à l'objet utilisateur
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param $dbSyncClassName
	*/
	public static function setDbSyncClassName($dbSyncClassName) {
		self::$dbSyncClassName = $dbSyncClassName;
	}

//	/**
//	* Retourne les arguments de création
//	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
//	* @return array
//	*/
//	private function __sleep() {
//		return array(self::$dbSyncClassName);
//	}

	/**
	* Retourne le singleton dédié
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @return &msg
	*/
	public function db() {
//log::logger("usr::db() => ".self::$dbSyncClassName,ygautheron);
		return ATF::getClass(self::$dbSyncClassName);
	}

	/**
	* Met à jour l'enregistrement de l'utilisateur loggué
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @return &msg
	*/
	public function update($infos) {
		if ($this->getID()) {
			$infos["id_user"] = $this->getID();
			return $this->db()->update($infos);
		} else {
			return false;
		}
	}

	/**
	* Retourne l'objet message de l'utilisateur courant par référence (pointeur)
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @return &msg
	*/
	public function &getMsg() {
		return $this->msg;
	}

	/**
	* Initialise un ATF::$usr si on vient de telescope
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	*/
	private function initFromTelescope() {
		if (!$this->infos["id_".self::$dbSyncClassName] && api::getToken()) {
            $headers = apache_request_headers();
            if ($headers["x-optima-id-user"]) {
            	$this->init($headers["x-optima-id-user"]);

            	// Je me présente, je suis ce contact :
            	if ($headers["x-optima-id-contact"]) {
					$contact = ATF::contact()->select($headers["x-optima-id-contact"]);
					ATF::$usr->set('contact', $contact);
				}

            } elseif (!$headers["x-optima-public"]) {
            	throw new Exception("telescope_optima_subsession_lost", 500);
            }
		}
	}

	/**
	* Retourne la clé primaire de l'utilisateur dans la base de données
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @todo Pourrait être géré avec un option clé de nom différent (table != user par exemple)
	* @return $id_user
	*/
	public function getID() {
		$this->initFromTelescope();
		return $this->infos["id_".self::$dbSyncClassName];
	}

	/**
	* Retourne le login de l'utilisateur
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param $no_init Ne pas initialiser telescope
	* @return string
	*/
	public function getLogin($no_init=false) {
		if (!$no_init) {
			$this->initFromTelescope();
		}
		return $this->infos["login"];
	}

	/**
	* Retourne les infos de l'utilisateur
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @return array
	*/
	public function getInfos() {
		$this->initFromTelescope();
		return $this->infos;
	}

	/**
	* Retourne une valeur des infos diverses
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $field
	* @return mixed
	*/
	public function get() {
		$this->initFromTelescope();
		$nfo = $this->infos;

		for ($i=0;$i<func_num_args();$i++) {
			$nfo = $nfo[func_get_arg($i)];
		}
		return $nfo;
	}

	/**
	* Renseigne une valeur sur une info diverse
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $field
	* @return void
	*/
	public function set($field,$val) {
		$this->initFromTelescope();
		$this->infos[$field]=$val;
	}

	/**
	* Traduction avec la langue de l'utilisateur
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param straing|array $word
	* @param prefix $prefix
	* @param prefix $suffix
	* @param boolean $strict
	* @return string
	*/
	public function trans($word,$prefix=NULL,$suffix=NULL,$strict=false,$suffixInPrefix=NULL) {
		$mot_traduit=loc::ation($word,$prefix,$suffix,$strict,$this->infos["id_language"],$suffixInPrefix);
		if (ATF::$autoAddNewTranslations) {
			ATF::localisation_traduction()->insertDefaultTrans($mot_traduit,$word,$prefix,$suffix,$strict,$this->infos["id_language"],$suffixInPrefix);
		}
		return $mot_traduit;
	}

	/**
	* Traduction avec la langue de l'utilisateur
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param straing|array $word
	* @param prefix $prefix
	* @param prefix $suffix
	* @param boolean $strict
	* @return string
	*/
	public function transFromSet($words,$prefix=NULL,$suffix=NULL,$strict=false,$suffixInPrefix=NULL) {
		$word = explode(",",$words);
		$wt = array();
		foreach ($word as $w) {
			$wt[] = $this->trans($w,$prefix,$suffix,$strict,$suffixInPrefix);
		}
		return implode(", ",$wt);
	}

	/**
	* Renvoi une date formatée dans la langue de l'utilisateur
	* @author Quentin JANON <qjanon@absystech.fr>
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $date date brute du type "2009-07-21"
	* @param boolean $year paramètre permettant de gérer l'affichage de l'année dans le résultat $year=='force' permet de forcer l'affichage de la date
	* @param boolean $concat paramètre permettant de gérer l'affichage de la date en format réduit ou pas dans le résultat
	* @param boolean $allowSmartContraction TRUE permet de retourner des traduction intelligentes du style "Hier" ou "Il y a 2h"
	* @example date_trans("2009-07-21 17:18:30") renvoi Mardi 21 Juillet 17:18 ou Mar. 21 Juil. 17:18 (si $concat=true)
	* @return string La date formatté dans la langue de l'utilisateur
	*/
	public function date_trans($date, $year=true, $concat=false,$allowSmartContraction=false) {
		if (strlen($date)<=7) {
			$temp = getdate(strtotime($date."-01"));
			$return = ucfirst(loc::ation($temp['month'],false,false,false,$this->infos["id_language"]));
			if ($year) {
				$return .= " ".$temp['year'];
			}
		} else {
			if (strlen($date)>10) {
				$time=true;
			}
			$temp = getdate(strtotime($date));
			$nom_jour=loc::ation($temp['weekday'],false,false,false,$this->infos["id_language"]);

			// Traduction intélligente
			if ($allowSmartContraction) {
				if ($smartDate = $this->smartDateFormat($date)) {
					return $smartDate;
				}
			}

			$mois=loc::ation($temp['month'],false,false,false,$this->infos["id_language"]);

			if(ATF::$usr->custom['user']['date_format']){
				if (($year===true && $temp['year']!=date("Y",time())) || $year==='force'){
					$date_format=ATF::$usr->custom['user']['date_format'];
				}else{
					$date_format=preg_replace("(\/Y|Y\-)","",ATF::$usr->custom['user']['date_format']);
				}
				switch(ATF::$usr->custom['user']['date_format']){
					case "d/m/Y":
					case "m/d/Y":
					case "Y-m-d":
						$return=date($date_format,strtotime($date));
						break;

					case "complet":
						$return=$this->formeDate($concat,$temp,$year,$mois,$nom_jour);
						break;

					case "texte":
						$return=$this->formeDate($concat,$temp,$year,$mois);
						break;

					case "d-m-Ymin":
						$return=date("d",strtotime($date))."-".mb_strtolower(substr($mois,0,4),"UTF-8")."-".substr(date("Y",strtotime($date)),2,4);
						break;
				}
			}else{
				$return=$this->formeDate($concat,$temp,$year,$mois,$nom_jour);
			}
			if ($time && ATF::$usr->custom['user']['heure']!='non') {
				if ($temp['hours'] < 10) $temp['hours'] = "0".$temp['hours'];
				if ($temp['minutes'] < 10) $temp['minutes'] = "0".$temp['minutes'];
				$return .= " à ".$temp['hours']."h".$temp['minutes'];
			}

		}

		if ($temp['year'] == '1970') return null;
		else return($return);

	}

	/**
	* Retourne une date intelligemment formatée si dans un passé proche (Il y a 4mn, il y a 2h, Mardi...)
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $date date brute du type "2009-07-21"
	* @param timestamp $baseDate date de référence pour calculer la proximité
	* @return string La date formatté dans la langue de l'utilisateur
	*/
	public function smartDateFormat($date, $baseDate=NULL) {
		if (strlen($date)>10) {
			$time=true;
		}
		if (!$baseDate) {
			 $baseDate = time();
		}
		if (date("Y-m-d",strtotime($date))===date("Y-m-d",$baseDate)) {
			// Aujourd'hui
			if ($time && ATF::$usr->custom['user']['heure']!='non') {
				$nbSecondsAgo = $baseDate-strtotime($date);
				$nbMinutesAgo = floor($nbSecondsAgo/60);
				if ($nbMinutesAgo==0) {
					// Il y a 17 s
					$smartDate = $nbSecondsAgo."s";
				} elseif ($nbMinutesAgo<=60) {
					// Il y a 43 mn
					$smartDate = $nbMinutesAgo."mn";
				} elseif ($nbMinutesAgo<=120) {
					// Plus récent que 2h
					$smartDate = floor($nbMinutesAgo/60)."h".($nbMinutesAgo % 60)."mn";
				} else {
					// Il y a quelques heures
					$smartDate = ceil($nbMinutesAgo/60)."h";
				}
				$smartDate = loc::mt(ATF::$usr->trans("il_y_a_x_heure"),array("heure"=>$smartDate));

			} else {
				$smartDate = $this->trans("aujourd_hui");
			}
		} elseif (date("Y-m-d",strtotime($date)+86400)===date("Y-m-d",$baseDate)) {
			// Hier
			$smartDate = $this->trans("hier");
		} elseif (date("Y-m-d",strtotime($date)+86400*2)===date("Y-m-d",$baseDate)) {
			// Avant-hier
			$smartDate = $this->trans("avant_hier");
		} elseif (date("W",strtotime($date))===date("W",$baseDate) && date("Y",strtotime($date))===date("Y",$baseDate)) {
			// Même semaine
			$temp = getdate(strtotime($date));
			$smartDate=ucfirst(loc::ation($temp['weekday'],false,false,false,$this->infos["id_language"]));
		}
		return $smartDate;
	}

	/* Permet de construire l'affichage de la date
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function formeDate($concat,$temp,$year,$mois,$nom_jour=NULL){
		if ($concat) {
			$return = substr(ucfirst($nom_jour),0).($nom_jour?" ":"").$temp['mday']." ".mb_strtolower(substr($mois,0),"UTF-8").(strlen($mois)>4?"":NULL);
		} else {
			$return = substr(ucfirst($nom_jour),0,3).($nom_jour?". ":"").$temp['mday']." ".mb_strtolower(substr($mois,0,4),"UTF-8").(strlen($mois)>4?".":NULL);
		}
		if (($year===true && $temp['year']!=date("Y",time())) || $year==='force')	$return .= " ".$temp['year'];
		return $return;
	}


	/**
	* Retourne l'objet de cryptage
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	*/
	public function getAES(){
		return $this->aes;
	}

	/**
	* méthode récursive retournant un tableau contenant la structure du menu à afficher
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param int $id_module : pour retrouver les enfants du module
	* @return array menu
	*/
	public function creation_menu($id_module=NULL,$visible=1){
		$modules=ATF::module()->enfants($id_module,$visible);
		$menu=array();
		//Parcours des modules principaux
		foreach($modules as $module){
			//Recherche du nb d'enfants => prédicat sur les enfants
			$a_des_enfants=ATF::module()->nb_enfants($module["id_module"],$visible);
			//Test du privilège sur le module (select il me semble)
			$name = module::moduleToName($module["module"]);
			if($this->privilege($name,"select")){
				//Il y a des enfants ! Mais attention le test de droit n'est pas encore fait
				if($a_des_enfants){
					//Recherche des sous-enfants (avec test de droits)
					$tmp=$this->creation_menu($module["id_module"],$visible);
					//La liste des enfants est vide => on n'a aucun droits sur les sous-enfants
					if(count($tmp)==0){
						$a_des_enfants=false;
					//Ajout du module et de ses sous-enfants
					}else{
						array_push($menu,array(
							"module"=>$module["module"]
							,"icone"=>module::iconePath($module["module"])
							,"nb_enfants"=>count($tmp)
							,"enfants"=>$tmp
							,"traduction"=>$this->trans($module["module"],"module")
							,"abreviation"=>$this->trans($module["module"],"abreviation",NULL,true)
							,"visible"=>$module["visible"])
							);
					}
				}
				//Il n'y a pas d'enfant ou on n'a pas les droits sur les sous-enfants
				if(!$a_des_enfants){
					//Cas particulier : module stats
					if($module["module"]=="stats" && ATF::getClass("stats")){
						$menuStats=ATF::stats()->createMenu();
						array_push($menu,array(
							"module"=>$module["module"]
							,"icone"=>module::iconePath($module["module"])
							,"traduction"=>$this->trans($module["module"],"module")
							,"visible"=>$module["visible"]
							,"enfants"=>$menuStats
							,"nb_enfants"=>count($menuStats))
						);
					}else{
						array_push($menu,array(
							"module"=>$module["module"]
							,"icone"=>module::iconePath($module["module"])
							,"traduction"=>$this->trans($module["module"],"module")
							,"visible"=>$module["visible"])
						);
					}
				}
			}
		}
		//Nombre de catégories principales
		if(!$id_module){
			$this->infos["menu_length"] = count($menu);
		}
		return $menu;
	}

	/**
	* méthode récursive retournant un tableau contenant la structure du menu à afficher
	* ou la structure complète de l'arborescence des modules
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $id_module : pour retrouver les enfants du module
	* @return array menu
	*/
	public function structureModule($id_module=NULL,$visible=1){
		$parent=ATF::module()->enfants($id_module,$visible);
		foreach($parent as $cle_parent=>$valeur_parent){
			$a_des_enfants=ATF::module()->nb_enfants($valeur_parent['id_module'],$visible);
			if($this->privilege(module::moduleToName($valeur_parent['module']),'select')){
				if($a_des_enfants){
					$menu[$valeur_parent['id_module']]=array(
						"module"=>$valeur_parent['module']
						,"icone"=>module::iconePath($valeur_parent['module'])
						,"enfants"=>$this->structureModule($valeur_parent['id_module'],$visible)
						,"traduction"=>$this->trans($valeur_parent['module'],'module')
						,"abreviation"=>$this->trans($valeur_parent['module'],'abreviation',NULL,true)
						,"visible"=>$valeur_parent['visible']
					);
				}else{
					$menu[$valeur_parent['id_module']]=array(
						"module"=>$valeur_parent['module']
						,"icone"=>module::iconePath($valeur_parent['module'])
						,"traduction"=>$this->trans($valeur_parent['module'],'module')
						,"visible"=>$valeur_parent['visible']
					);
				}
			}
		}
		return $menu;
	}

	/** Retourne les privilèges du user
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function getPrivileges(){
		$this->initFromTelescope();
		return $this->privileges;
	}

	/**
	* Prédicat retournant VRAI si l'utilisateur a le privilege demandé, FAUX sinon
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $module
	* @param string $privilege
	* @param string $field
	* @param string $real_module Modifié par le module qui doit "bypassé" les $_REQUEST[table] ou $_REQUEST[module] par exemple (actions_by)
	* @return boolean
	*/
	public function privilege($module,$privilege,$field=NULL,&$real_module=NULL) {
		$this->initFromTelescope();

		if($this->isGod()===true){
			return true;
		}

		if (!$privilege) { // Par défaut le privilege est la sélection => "select"
			$privilege = "select";
		}

		// Est-ce qu'un privilège événementiel a été prévu ?
		if ($module/* && is_string($module)*/) {
			$c = ATF::getClass($module);
			if ($c instanceof classes) {
				if (($specific = $c->eventPrivilege($privilege))!==NULL) {
					return $specific;
				}
			}

			// Si ce privilège est sous la responsabilité d'un autre privilege (par exemple 'clone' est sous la responsabilité de 'insert'
			if (isset($c->privilege_egal) && isset($c->privilege_egal[$privilege])) {
				$privilege = $c->privilege_egal[$privilege];
			}

			// Si cette action sur ce module est régie par un autre, on modifie le module appelé
			if (isset($c->actions_by) && isset($c->actions_by[$privilege])) {
				$module = $c->actions_by[$privilege];
				if ($real_module) {
					$real_module = $module; // L'action doit être
				}
			}

			// Si un autre module est responsable de celui-ci, on cherche les droits du module responsable
			if (isset($c->controlled_by)) {
				$module = $c->controlled_by;
			}

			// Module public
			if (isset($c->public) && $c->public) {
				return true;
			}

			// Droits sur champ spécifique
			if ($field && isset($this->privileges[$module][$field][$privilege])) {
				// Retourne la valeur du privilege pour ce champ, sinon la valeur
				return $this->privileges[$module][$field][$privilege];
			}
		}

		// Privilege global
		if (isset($this->privileges[$module][""][$privilege])) {
			return true;
		} else {
			return false;
		}
	}

	/**
	* Méthode de sortie de l'application
	*/
	public function logout() {
		setcookie("l","", 0,"/", '', true, true);
		setcookie("p","", 0,"/", '', true, true);
		setcookie("s","", 0,"/", '', true, true);
		setcookie("optima[societe]","", 0,"/", '', true, true);
		ATF::getEnv()->resetSession();
	}
	/**
	* Retourne le contenu de custom dans la base de données
	* @author Cyril CHARLIER <ccharlier@absystech.fr>
	* @return $custom
	*/
	public function getCustom() {
		$this->initFromTelescope();
		return $this->custom;
	}
	/**
	* Met à jour les variables de custom
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>, Nicolas BERTEMONT <nbertemont@absystech.fr> , Cyril CHARLIER <ccharlier@absystech.fr>
	* @date 2009-01-15
	* @param array $infos ($_POST habituellement attendu)
	*	string $infos[variable1]
	*	string $infos[variable2]
	*	...
	* peut être nul dans le cas d'une sauvegarde d'ouverture d'onglet/panel et ajout/suppression d'onglet
	* @return boolean
	*/
	public function updateCustom($infos=NULL) {
		if (!$this->getID()) {
			return;
		}

		/* si on change la vue */
		if(isset($infos['vue_custom'])){
			//check de la case
			if($infos['vue_custom']=="true"){
				$this->custom["columns"][$infos['table']]["vue_custom"]="oui";
			}else{
				unset($this->custom["columns"][$infos['table']]["vue_custom"]);
			}
		/* dans le cas où l'on souhaite réinitialiser les colonnes */
		}elseif(isset($infos['delete'])){
//			if($infos['type']=="sort"){
//				unset($this->custom["columns"][$infos['table']]["sort"]);
//			}else
			if($this->custom["columns"][$infos['table']]["vue_custom"]){
				//suppression de la vue custom
				unset($this->custom["columns"][$infos['table']]);
			}elseif($infos['filter_key']){
				//suppression de la vue du filtre
				$filterKey = str_replace("public_","",$infos['filter_key']);
				$options = unserialize(ATF::filtre_optima()->select($filterKey,'options'));
				unset($options['view']);
				ATF::filtre_optima()->update(array('id_filtre_optima'=>$filterKey,'options'=>serialize($options)));
			}
		}else{
			if(is_array($infos)){
				foreach ($infos as $custom_key => $custom_value) {
					switch ($custom_key) { // Protection pour éviter qu'on y mette n'importe quoi (hack)
						case "columns": // Mise à jour des infos des colonnes pour ce module
							//permet de checker les colonnes si ce sont celles par défaut ou non (si oui alors on ne polue pas le custom
							foreach ($custom_value as $table => $options) {
								//si il y a eu des changements, on les sauvegarde
								if($this->aChangeOuNon($table,$options)){
									if($infos['filter_key'] && !$this->custom["columns"][$table]['vue_custom']){
										//sauvegarder dans la vue du filtre si il y en a un de sélectionné
										$filterKey = str_replace("public_","",$infos['filter_key']);
										$options_filtre=unserialize(ATF::filtre_optima()->select($filterKey,'options'));
										$options_filtre['view']=$options;
										ATF::filtre_optima()->update(array('id_filtre_optima'=>$filterKey,'options'=>serialize($options_filtre)));
									}else{
										//ou sauvegarder dans le custom
										$this->custom[$custom_key][$table] = $options;
										$this->custom[$custom_key][$table]["vue_custom"]="oui";
									}
								}
							}
							break;
						case "params":
							//permet de checker les colonnes si ce sont celles par défaut ou non (si oui alors on ne polue pas le custom
							foreach ($custom_value as $table => $options) {
								//si il y a eu des changements, on les sauvegarde
									$this->custom[$custom_key][$table] = $options;
							}
							break;
						case "filters": // Mise à jour des infos des filtres pour ce module
							foreach ($custom_value as $table => $filters) {
								foreach ($filters as $key => $options) {
									/* Créer ou mettre à jour en tant que filtre public */
									$id_filtre = str_replace("public_","",$key);
									if ($options) {
										if (isset($infos["public"])) {
											$array["type"]="public";
										}else{
											$array["type"]="prive";
										}
										switch ($infos["evenement"]) {
											case "update":
												$array["id_filtre_optima"]=$id_filtre;
											case "insert":
												$array["filtre_optima"]=$options["name"];
												$array["id_module"]=ATF::module()->from_nom($table);
												$array["id_user"]=$this->getID();
												$array["options"]=serialize($options);
												$id_filtre_insert=ATF::filtre_optima()->{$infos["evenement"]}($array);
												break;
										}
									} else {
										//suppression du filtre
//										if(ATF::filtre_optima()->delete($id_filtre)){
//											foreach(ATF::_s("pager")->q as $div=>$querier){
//												if($querier->filter_key==$key){
//													ATF::_s("pager")->unsetQuerier($div);
//													break;
//												}
//											}
//										}
									}

									if($infos["public"]=="insert"){
										$infos['filter_key']="public_".$id_filtre;
									}elseif($infos["evenement"]=="insert"){
										$infos['filter_key']=$id_filtre_insert;
									}else{
										$infos['filter_key']=$key;
									}
								}
							}
							break;
					}
				}
			}
		}
		ATF::tracabilite()->maskTrace($this->db()->table);
		$this->db()->update(array("id_user"=>$this->getID(),"custom"=>serialize($this->infos["custom"])));
		ATF::tracabilite()->unmaskTrace($this->db()->table);
		ATF::$cr->block('generationTime');
		//ATF::getEnv()->commitSession();
		return $infos;
	}

	/**
	* Retourne la liste des filtres de l'utilisateur pour cette table
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>, Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @deprecated N'est utilisé que pour les select all non EXTJS, et donc pour les filtres qui ne sont pas en BDD.
	* @notes IMPORTANT : est encore utile pour inventaire GEMP
	* @return array
	*/
	public function getFilters($table) {
		$id_module = ATF::module()->from_nom($table);

		// Filtres privés
		ATF::filtre_optima()->q->reset()->addCondition("id_module",$id_module)->addCondition("id_user",$this->getID())->addCondition('type','prive');
		if($filtres_prives=ATF::filtre_optima()->select_all()){
			foreach ($filtres_prives as $key => $item) {
				$filters[$this->trans("filtre_perso")][$item["id_filtre_optima"]]=$item["filtre_optima"];
			}
		}

		// Filtres publics
		ATF::filtre_optima()->q->reset()->addCondition("id_module",$id_module)->addCondition('type','public');
		if($filtres_publics=ATF::filtre_optima()->select_all()){
			foreach ($filtres_publics as $key => $item) {
				$filters[$this->trans("filtre_public")]["public_".$item["id_filtre_optima"]]=$item["filtre_optima"];
			}
		}

		return $filters;
	}

	/**
	* Retourne l'identifiant du filtre par défaut pour cer identifiant de requêteur
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param string $div
	* @return string
	* @todo déplacer les méthodes et creer une classe spécifique + changer le nom filtre_defaut par un nom plus approprié
	*/
	public function getDefaultFilter($div,$k="filter_key") {
//log::logger("getDefaultFilter",ygautheron);
//log::logger($this->infos["custom"]["filtersDefaults"][$div],ygautheron);
		//avec le nouveau système de select_all extjs, chaque onglet a son propre div, mais on se sert du parent pour la sauvegarde
		$div=preg_replace("`_[(0-9)]*$`","",$div);

		if ($div) {
			ATF::filtre_defaut()->q->reset()
				->where("id_user",$this->getID())
				->where("`div`",$div)
				->setDimension("cell")
				->addField('`'.$k.'`')
				->setStrict();
			$result = ATF::filtre_defaut()->select_all();
			if ($k=="order") {
				$result = unserialize($result);
			}
		}
		return $result;
	}

	/**
	* Enregistre l'identifiant du filtre par défaut pour cer identifiant de requêteur
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param string $div
	* @return int
	*/
	public function setDefaultFilter($div,$div_parent,$class) {
		if ($div) {
			$q = ATF::_s("pager")->create($div);

			//puisque l'on applique plus les filtres, mais qu'ils sont sous forme d'onglet, cette condition n'est plus
			if($q->getFilterKey() || $q->getOrder() || $q->getPage() || ($q->getLimit() && $q->getLimit()!=__RECORD_BY_PAGE__)){
				// Sauvegarde seulement si il y a des données
				ATF::tracabilite()->maskTrace($this->db()->table);

				$infos=array(
					"id_user"=>$this->getID()
					,"div"=>$div_parent
					,"filter_key"=>$q->getFilterKey()
					,"order"=>$q->getOrder()?serialize($q->getOrder()):NULL
					,"page"=>$q->getPage()
					,"limit"=>$q->getLimit()
				);

				ATF::tracabilite()->unmaskTrace($this->db()->table);
			}
		}
		return $id_filtre_defaut;
	}

	/** On détecte si les champs affichés sont ceux par défaut ou non
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param string table : nom de la table courante
	*/
	public function IsCustomColumns($table,$id_filtre=NULL){
		//si il y a un filtre, on check si la view a été sauvegardé et si elle corresponds à la vue par défaut
		if(isset($this->custom["columns"][$table]) && $this->custom["columns"][$table]['vue_custom']){
			$colonnes=$this->custom["columns"][$table];
		}elseif($id_filtre){
			$options=unserialize(ATF::filtre_optima()->select(str_replace("public_","",$id_filtre),'options'));
			$colonnes=$options['view'];
		}

		if($colonnes){
			//comparaison des deux tableaux, en prenant compte que les colonnes peuvent ne pas être les meme, mais également que seul l'ordre ait pu changé
			return $this->aChangeOuNon($table,$colonnes);
		}else{
			return false;
		}
	}

	/** Récupère les éventuelles colonnes d'un filtre
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param integer id_filtre : clé du filtre éventuellement sélectionné
	*/
	public function getColumnsFilter($id_filtre){
		$options=unserialize(ATF::filtre_optima()->select(str_replace("public_","",$id_filtre),'options'));
		return $options['view']['order'];
	}

	/**
	* Témoigne de l'activité de chaque utilisateur (de chaque clic)
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @return boolean TRUE si cela s'est bien passé, FALSE sinon
	*/
	public function activity() {
		if ($this->getID()) {
			$this->clics++;
			$temp=NULL;
			//Mise à jour de l'activité dans la table user
			ATF::tracabilite()->maskTrace($this->db()->table);
//			$this->db()->update(array("id_user"=>$this->getID(),"date_activity"=>date("Y-m-d H:i:s",time())),$temp,NULL,$tmp,true);
			ATF::tracabilite()->unmaskTrace($this->db()->table);

			if (ATF::getSchema()) {
				//Mise à jout de l'activité sur la table activity
				ATF::activity(NULL,"main")->setActivity();
			}
			return true;
		} else {
			//@todo stats sur les permalink ?
			return false;
		}
	}

	/**
    * Update des preferences d'un user (nico : modif pour optimisation et ajout des mails de suivi)
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos
	*					array infos[user]
	*					array infos[preference]
	*					array infos[suivi]
    * @return boolean true
    */
	public function preference($infos){
		// Champ custom
		$this->custom['user']['show_data_day'] = $infos['user']['show_data_day']; // afficher le contenu par rapport à la date du jour
		$this->custom['user']['tronquer'] = $infos['user']['tronquer']; // Tronquer les mots dans les select_all
		$this->custom['user']['show_all'] = $infos['user']['show_all']; // afficher le contenu de tous les panels
		$this->custom['suivi']['mail'] = $infos['suivi']['mail']; // Envoi ou non les emails de suivi
		$this->custom['suivi']['mail_digest'] = $infos['suivi']['mail_digest']; // Envoi ou non les emails de suivi
		$this->custom['user']['date_format'] = $infos['user']['date_format'];
		$this->custom['user']['heure'] = $infos['user']['heure'];
		$this->custom['user']['date_format']=$infos['user']['date_format'];
		$this->custom['user']['datetime']=$infos['user']['datetime'];

		//Messagerie
		$this->custom['messagerie']['host'] = $infos['messagerie']['host'];
		$this->custom['messagerie']['username'] = $infos['messagerie']['username'];
		$this->custom['messagerie']['password'] = $infos['messagerie']['password'];
		$this->custom['messagerie']['port'] = $infos['messagerie']['port'];
		$this->custom['messagerie']['folder'] = $infos['messagerie']['folder'];
		$this->custom['messagerie']['tls'] = $infos['messagerie']['tls'];

		//calendrier
		$this->custom['calendrier']['host'] = $infos['calendrier']['host'];
		$this->custom['calendrier']['username'] = $infos['calendrier']['username'];
		$this->custom['calendrier']['password'] = $infos['calendrier']['password'];
		$this->custom['calendrier']['calendar_partage'] = $infos['calendrier']['calendar_partage'];
		$this->custom['calendrier']['calendar_default'] = $infos['calendrier']['calendar_default'];
		$this->custom['calendrier']['calendar_name'] = $infos['calendrier']['calendar_name'];

		//widgets graph
		$this->custom['widgets']['devis_signe'] = $infos['widgets']['devis_signe'];
		$this->custom['widgets']['ca_previsionnel'] = $infos['widgets']['ca_previsionnel'];
		$this->custom['widgets']['marge'] = $infos['widgets']['marge'];
		$this->custom['widgets']['resteAPayer'] = $infos['widgets']['resteAPayer'];
		$this->custom['widgets']['facture_top10negatif'] = $infos['widgets']['facture_top10negatif'];
		$this->custom['widgets']['suvis'] = $infos['widgets']['suvis'];
		$this->custom['widgets']['hotline_interaction'] = $infos['widgets']['hotline_interaction'];
		$this->custom['widgets']['hotline'] = $infos['widgets']['hotline'];
		$this->custom['widgets']['tpsPriseCharge'] = $infos['widgets']['tpsPriseCharge'];
		$this->custom['widgets']['tpsCloture'] = $infos['widgets']['tpsCloture'];
		$this->custom['widgets']['nbreCloture'] = $infos['widgets']['nbreCloture'];
		$this->custom['widgets']['hotline_top10negatif'] = $infos['widgets']['hotline_top10negatif'];
		$this->custom['widgets']['hotline_waitmep'] = $infos['widgets']['hotline_waitmep'];
		$this->custom['widgets']['hotline_partTicket'] = $infos['widgets']['hotline_partTicket'];
		$this->custom['widgets']['hotline_requetebyUser'] = $infos['widgets']['hotline_requetebyUser'];
		$this->custom['widgets']['devis_ca'] = $infos['widgets']['devis_ca'];
		$this->custom['widgets']['devis_prix'] = $infos['widgets']['devis_prix'];

		$infos["user"]["custom"] = serialize($this->custom);
		ATF::zimbra()->user_zid($infos['calendrier']);
		// Mot de passe
		if(($infos["user"]['password'] || $infos["user"]['password_again']) && $infos["user"]['password'] != $infos["user"]['password_again']){
			throw new error ($this->trans('wrong_pwd','preference'),536);
		}
		if (!$infos["user"]['password']) {
			unset($infos["user"]['password']);
		}
		unset($infos["user"]['heure'],$infos["user"]['date_format'],$infos["user"]['password_again'],$infos['user']['show_data_day'],$infos["user"]['tronquer'],$infos['user']['show_all']);

		// Téléphone SIP
		if(ATF::$codename=="absystech" || ATF::$codename=="att"){
			$infos["user"]["id_phone"]=$infos["phone"]["phone"];
		}
		$maj=$this->update($infos["user"]);
		$this->maj();

		return $maj;
	}

	/**
	* Renvoie l'intitulé du jour (L pour lundi, M pour mardi,...)
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @author Q.JANON <qjanon@absystech.fr>
	* @date creation : 14/01/2008 - modification : 08/09/2009
	* @param $date la date
	* @return string l'intitulé du jour
	*/
	public function abbreviate_day($date) {
		$date = getdate(strtotime($date));
		return ucfirst(substr($this->trans($date['weekday']),0,1));
	}

	/**
	* Affiche le template de changement de mdp
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	*/
	public function newPwdTpl($infos,&$s,$files=NULL,$cadre_refreshed=NULL){
		ATF::$cr->add("sendNewPwd","newPassword");
		ATF::$cr->block("top");
		ATF::$cr->block("generationTime");
	}

	/**
	* Change le mot de passe d'un user, et lui envoi le nouveau par mail
	* @author QJ <qjanon@absystech.fr>
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @date creation : 11/12/09
	* @param $infos l'email de l'utilisateur
	*/
	public function newPwd($infos){
		$this->db()->q->reset()
			->addField('id_user')
			->addField('email')
			->addCondition("email",$infos['email'])
			->setDimension('row');

		if ($user = ATF::user()->select_all()) {
			$mdp = util::generateRandWord(10);
			$user['password'] = $mdp;
			unset($user['user.id_user']);
			$r = ATF::user()->update($user);

			/* MAIL */
			$mail = new mail(array(
				"objet"=>$this->trans("modifyPassword")
				,"recipient"=>$user['email']
				,"template"=>"newPwd"
				,"mdp"=>$mdp
				,"userName"=>ATF::user()->nom($user['id_user'])
			));
			$mail->send();
			ATF::$cr->add("sendNewPwd","newPassword.tpl.htm",array("mailSend"=>true));
		} else {
			ATF::$cr->add("sendNewPwd","newPassword.tpl.htm",array("mailNotSend"=>true));
		}
	}

	/**
	* Conserver la session
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	*/
	public function keepOnline($infos,&$s,$files=NULL,&$cadre_refreshed=NULL){
		ATF::$cr->block("generationTime");
		ATF::$cr->block("top");
		return true;
	}

	/**
	* Indique si c'est l'utilisateur god
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	*/
	public function isGod(){
		return $this->god;
	}

	/**
	* Indique si une nouvelle version d'ATF existe
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	*/
	public function isNewATF(){
		if(isset($this->ATF_version)){
			return $this->ATF_version!=ATF::$version;
		}else{
			return false;
		}
	}

	/**
	* Indique l'utilisateur est loggé
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	*/
	public function isLogged(){
		return $this->logged;
	}

	/** Permet d'ajouter la liste des champs du module choisi dans la liste déjà présente
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param string $table : table courante
	* @param string $nom_module : nom du module à joindre
	* @param integer filter_key : identifiant du filtre (null si création)
	* @param boolean $afficher_tout : afficher tous les modules dans la liste ou cacher le module qu'on va lier
	*/
	public function ajoutRecupListeChamps($table,$nom_module,$filter_key=NULL,$afficher_tout=false){
		$ajout=ATF::getClass($nom_module)->table_structure();
		$ajout=$this->trans($ajout,$nom_module);
		$ajout=$this->tri_ident($ajout,true);
		$this->infos["filtre"][$table][$nom_module]=$ajout;
		return $this->recupListeChamps($table,$filter_key,$nom_module,$afficher_tout);
	}

	/** Récupère les champs des modules joints au module courant
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param string $table_parente : dans le cas ou c'est un insert de filtre, besoin d'avoir le nom de la table parent pour recup colonne
	* @param integer $id_filtre : identifiant du filtre (null si création)
	* @param string $module_ajoute : module à joindre (null si c'est juste une récupération de l'actuel)
	* @param boolean $afficher_tout : afficher tous les modules dans la liste ou cacher le module qu'on va lier
	*/
	public function recupListeChamps($table_parente,$id_filtre=NULL,$module_ajoute=NULL,$afficher_tout=false){
		if($table_parente){
			$tableau=ATF::getClass($table_parente)->recup_colonnes('donnee');
		}

		//si il s'agit d'un filtre existant, on récupère les jointures éventuelles
		if($id_filtre){
			$options=unserialize(ATF::filtre_optima()->select($id_filtre,'options'));
			$jointures=$options['jointures'];

			foreach($jointures as $items){
				if($afficher_tout || $items['nom_module']!=$module_ajoute){
					$ajout=ATF::getClass($items['nom_module'])->table_structure();
					$ajout=$this->trans($ajout,$items['nom_module']);
					$ajout=$this->tri_ident($ajout,true);
					$tableau[$this->trans($items['nom_module'],'module')]=$ajout;
				}else{
					//pour éviter d'avoir la table en question dans la liste des tables permettant la jointure
					break;
				}
			}
		}

		//si plusieurs jointures ont été ajoutées mais pas enregistrées
		if($jointure_sup=$this->infos["filtre"][$table_parente]){
			foreach($jointure_sup as $nom=>$donnees){
				if($afficher_tout || $nom!=$module_ajoute){
					$tableau[$this->trans($nom,'module')]=$donnees;
				}else{
					break;
				}
			}
		}

		//pour différencier les champs avec id
		$tableau=$this->tri_ident($tableau);

		return $tableau;
	}

	/** Permet de trier toutes les lignes par ordre croissant et identifier les clés primaires de manière récursive
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array tableau : tableau a trié
	* @param boolean asort : trie le tableau suivant les clés ou suivant les valeurs
	*/
	public function tri_ident($tableau,$asort=false){
		foreach($tableau as $key=>$item){
			if(is_array($item)){
				$tableau[$key]=$this->tri_ident($item,true);
			}else{
				$nom=explode('.',$key);
				if($key==($nom[0].'.id_'.$nom[0]) && !strpos($item,"(".$this->trans('identifiant').")"))$tableau[$key]=$item.' ('.$this->trans('identifiant').')';
			}
		}

		if($asort)asort($tableau);
		else ksort($tableau);

		return $tableau;
	}

	/** Permet de retirer de la liste les champs du module enlevé / ou de réinitialiser toutes les données présentes mais non utilisés dans usr
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param string $table : le nom du module sur lequel on se situe
	* @param string $module_sup : le nom du module à retirer (NULL si on reinitialise tout)
	*/
	public function unsetListeChamps($table,$module_sup=NULL){
		if($module_sup){
			unset($this->infos['filtre'][$table][$module_sup]);
		}else{
			unset($this->infos['filtre'][$table]);
		}
	}

	/**
	* Retourne les widgets de l'utilisateur
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function getWidgets(){
		$preferences = ATF::preferences()->getCustom();

		$w = array();

		if($preferences["widgets"]["ca_previsionnel"] !== "non"){
			//$w[$preferences["widgets"]["ca_previsionnel"]] = array('module'=>'devis','type'=>'pipe');
			$w[0] = array('module'=>'devis','type'=>'pipe','id_profil'=>array(1,9/*Assistante AT*/,5/*Assistante ATT*/,15/*Commercial*/));
		}
		if($preferences["widgets"]["marge"] !== "non"){
			//$w[$preferences["widgets"]["marge"]] = array('module'=>'affaire','type'=>'marge');
			$w[1] = array('module'=>'affaire','type'=>'marge','id_profil'=>array(1));
		}

		if($preferences["widgets"]["facture_top10negatif"] !== "non"){
			//$w[$preferences["widgets"]["facture_top10negatif"]] = array('module'=>'facture','type'=>'top10negatif') ;
			$w[2] = array('module'=>'facture','type'=>'top10negatif','id_profil'=>array(1,9/*Assistante AT*/,5/*Assistante ATT*/,15/*Commercial*/));
		}

		if($preferences["widgets"]["resteAPayer"] !== "non"){
			//$w[$preferences["widgets"]["resteAPayer"]] = array('module'=>'facture','type'=>'resteAPayer');
			$w[3] = array('module'=>'facture','type'=>'resteAPayer','id_profil'=>array(1,9/*Assistante AT*/,5/*Assistante ATT*/,15/*Commercial*/));
		}

		if($preferences["widgets"]["suivis"] !== "non"){
			//$w[$preferences["widgets"]["suivis"]] = array('module'=>'suivi');
			$w[4] = array('module'=>'suivi');
		}
		if($preferences["widgets"]["hotline_interaction"] !== "non"){
			//$w[$preferences["widgets"]["hotline_interaction"]] = array('module'=>'hotline_interaction');
			$w[5] = array('module'=>'hotline_interaction');
		}
		if($preferences["widgets"]["gestion_ticket"] !== "non"){
			//$w[$preferences["widgets"]["gestion_ticket"]] = array('module'=>'gestion_ticket');
			$w[6] = array('module'=>'gestion_ticket');
		}
		if($preferences["widgets"]["hotline"] !== "non"){
			//$w[$preferences["widgets"]["hotline"]] = array('module'=>'hotline');
			$w[7] = array('module'=>'hotline');
		}

		if($preferences["widgets"]["requetebyUserParMois"] !== "non"){
			$w[8] = array('module'=>'hotline','type'=>'requetebyUserParMois2');
		}

		if($preferences["widgets"]["requetebyUserParMois"] !== "non"){
			$w[9] = array('module'=>'hotline','type'=>'requetebyUserParMois');
		}

		if($preferences["widgets"]["requetebyUser7joursGlissants"] !== "non"){
			$w[10] = array('module'=>'hotline','type'=>'requetebyUser7joursGlissants');
		}

		if($preferences["widgets"]["graph_tarif_horaire"] !== "non"){
			$w[11] = array('module'=>'hotline','type'=>'graph_tarif_horaire','id_profil'=>array(1));
		}



		if($preferences["widgets"]["tpsPriseCharge"] !== "non"){
			//$w[$preferences["widgets"]["tpsPriseCharge"]] = array('module'=>'hotline','type'=>'tpsPriseCharge');
			$w[21] = array('module'=>'hotline','type'=>'tpsPriseCharge');
		}
		if($preferences["widgets"]["hotline_top10negatif"] !== "non"){
			//$w[$preferences["widgets"]["hotline_top10negatif"]] = array('module'=>'hotline','type'=>'top10negatif');
			$w[22] = array('module'=>'hotline','type'=>'top10negatif','id_profil'=>array(1,9/*Assistante AT*/,5/*Assistante ATT*/,15/*Commercial*/));
		}
		if($preferences["widgets"]["tpsCloture"] !== "non"){
			$w[23] = array('module'=>'hotline','type'=>'tpsCloture');
		}
		if($preferences["widgets"]["nbreCloture"] !== "non"){
			$w[24] = array('module'=>'hotline','type'=>'nbreCloture');
		}
		if($preferences["widgets"]["partTicket"] !== "non"){
			$w[25] = array('module'=>'hotline','type'=>'partTicket');
		}


		if($preferences["widgets"]["waitmep"] !== "non"){
			$w[16] = array('module'=>'hotline','type'=>'waitmep');
		}

		if($preferences["widgets"]["requetebyUser"] !== "non"){
			$w[17] = array('module'=>'hotline','type'=>'requetebyUser');
		}

		if(ATF::$codename=="absystech"){
			if($preferences["widgets"]["statCleodis"] !== "non"){
				$w[18] = array('module'=>'hotline','type'=>'statCleodis', 'id_profil'=>array(1,10)); //Associe et Développeur
			}
		}


		if($preferences["widgets"]["devis_signe"] !== "non"){
			$w[19] = array('module'=>'devis','type'=>'devis_signe');
		}

		if($preferences["widgets"]["devis_prix"] !== "non"){
			$w[20] = array('module'=>'devis','type'=>'devis_prix');
		}

		if($preferences["widgets"]["devis_ca"] !== "non"){
			$w[21] = array('module'=>'devis','type'=>'devis_ca');
		}



		//On remet dans l'ordre selon l'offset
		ksort($w);
		foreach ($w as $k => $i) {
			if (is_array($i["id_profil"]) && !in_array($this->get('id_profil'),$i["id_profil"])
				|| !$this->privilege($i['module'],'select')) {
				unset($w[$k]);
			}
		}
		return $w;
	}

	/** Créer une instance, un nouvel objet USR
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $id
	*/
	public static function newInstance($id){
		return new ATF::$usrClass($id);
	}

	/**
    * Update des preferences du tableau de bord de l'accueil pour un utilisateur
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param array $infos
    */
	public function preferenceAccueil($infos){
		if ($infos['extAction'] && $infos['extMethod']) {
			unset($infos['extAction'],$infos['extMethod']);
		}
		if ($this->custom['dashBoard']) {
			unset($this->custom['dashBoard']);
		}
		foreach ($infos as $module=>$params) {
			if ($params['activate']) {
				$this->custom['dashBoard'][$module] = $params;
			}
		}
		$this->updateCustom(); //!SANS PARAMETRES
	}

	/** Vérifie si il y a eu un changement entre les listes de champs
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param string $table : table courante
	* @param array $options : tableau contenant la vue (position des champs, align, prefix, suffix)
	*/
	public function aChangeOuNon($table,$options){
		//comparaison des deux tableaux, en prenant compte que les colonnes peuvent ne pas être les meme, mais également que seul l'ordre ait pu changé
		$ref=0;
		$col_base=array_flip(ATF::getClass($table)->colonnes_simples());
		//vérifie l'ordre des champs
		foreach($options['align'] as $champs=>$align){
			if($col_base[$champs]==$ref) {
				$ref++;
			} else {
				$ref--;
			}
		}

		//si les champs ne sont pas identiques avant et après on sauvegarde de toute façon
		if($ref!=count($col_base))return true;


		//vérifie si on a modifié l'alignement
		foreach($options['align'] as $cham=>$ali){
			if((ATF::getClass($table)->view['align'][$cham] && ATF::getClass($table)->view['align'][$cham]!=$ali)
				|| (!ATF::getClass($table)->view['align'][$cham] && $ali!="left") ){
				return true;
			}
		}

		//si on a ajouté / enlevé un prefix
		foreach($options['prefix'] as $cham_pre=>$valeur_pre){
			if((ATF::getClass($table)->view['prefix'][$cham_pre] && ATF::getClass($table)->view['prefix'][$cham_pre]!=$valeur_pre)
				|| (!ATF::getClass($table)->view['prefix'][$cham_pre] && $valeur_pre) ){
				return true;
			}
		}

		//si on a ajouté / enlevé un suffix
		foreach($options['suffix'] as $cham_su=>$valeur_su){
			if((ATF::getClass($table)->view['suffix'][$cham_su] && ATF::getClass($table)->view['suffix'][$cham_su]!=$valeur_su)
				|| (!ATF::getClass($table)->view['suffix'][$cham_su] && $valeur_su) ){
				return true;
			}
		}
		return false;
	}

	/**
	* Retourne le nom complet de l'utilisateur (civilite, prenom et nom)
	* @author Quentin JANON <qjanon@absystech.fr>
	* @return string
	*/
	public function getNom() {
		$r = "";
		if ($this->infos["civilite"]) {
			$r .= $this->infos["civilite"].". ";
		}
		if ($this->infos["prenom"]) {
			$r .= $this->infos["prenom"]." ";
		}
		if ($this->infos["nom"]) {
			$r .= $this->infos["nom"];
		}
		return $r;
	}

	/**
	* Retourne la langue choisie par défaut selon des éléments externes tels que la HOST, ou un SOUS DOMAINE ...
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return string
	*/
	public function getDefaultLanguage() {
		return $this->get("id_language");
	}

	/**
	* Surcharge de preference pour mise à jour du custom
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function setPreferences($infos){
		return $this->preference($infos);
	}

	/**
	* Réinitialisation des données utilisées par les filtres, pour éviter les blocages de l'affichage du select_all en cas de conflit (avec vue, structure base etc)
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param [IN] string $table Nom de la table courante
	* @parem [IN] integer $id_filtre identifiant du filtre (par défaut) concerné
	* @param [IN/OUT] array &$div	Nom de référence de l'élement div du DOM, ce nom sera utilisé comme préfixe dans la plupart des div contenus à l'intérieur
	* @param [IN/OUT] classes &$parent_class Classe parente d'un point de vue encapsulation de DIV côté DOM (par exempole une fiche select SOCIETE appelle des onglets enfants CONTACT, AFFAIRE... pour CONTACT le parent_class sera un objet SOCIETE)
	* @param [OUT] querier &$q Requêteur qui doit être récupéré dans le PAGER
	* @param [OUT] array &$view Vue récupérée de la session dans le champ custom du user, sinon la vue par défaut définie dans l'objet de la classe courante
	* @param [OUT] array &$url_extra Ces arguments supplémentaire sseront ajoutés aux URL d'ajout et d'insert...
	* @param [OUT] array &$extra Ces arguments seront ajoutés aux mises à jour de listings
	* @param [IN] array $fk Les clés étrangères à utiliser pour filtrer les enregistrements
	* @param [IN] array $function Fonction à exécuter (en général par defaut select_all)
	* @param [IN/OUT] array &$s La session
	* @param [IN] boolean $reinit permet de savoir si cet appel provient d'une réinitialisation (auquel cas, sécurité mis en place contre le bouclage)
	* @return array $data les données trouvées
	*/
	public function reinitFiltre($table,$id_filtre,$div,&$parent_class,&$q,&$view,&$url_extra,&$extra,$fk,$function,&$s,$no_limit=false){
		//l'un pour réinitialiser la vue
		//$this->updateCustom(array('delete'=>true,'table'=>$table));


		//l'autre pour réinitialiser le filtre
		//$this->updateCustom(array("filters"=>array($table=>array($id_filtre=>0))));
		ATF::filtre_user()->removeFilter(array("module"=>$table,"id_filtre_optima"=>str_replace("public_","",$id_filtre)));

		//si ce filtre est celui par défaut on le supprime de cette table
//		ATF::filtre_defaut()->q->reset()->setDimension('cell')
//										->addField("id_filtre_defaut")
//										->setStrict()
//										->addCondition("filtre_defaut.div",$div)
//										->addCondition("filter_key",$id_filtre)
//										->addCondition("id_user",$this->getID());
//		if($id_filtre_defaut = ATF::filtre_defaut()->select_all()){
//			ATF::filtre_defaut()->delete($id_filtre_defaut);
//		}

		ATF::$msg->addWarning("Le filtre et la vue ne sont plus accordés, ils ont donc été réinitialisés");

		//on réinitialise le querier
		ATF::getClass($table)->q->reset();

		//on réinitialise le post pour éviter de stocker le filtre par défaut
		$post =& ATF::_p();
		if(isset($post["filter_key"])){
			ATF::_p("filter_key",0);
		}

		//dernier parametre (reinit) a true, pour éviter le bouclage
		return ATF::getClass($table)->genericSelectAll($div,$parent_class,$q,$view,$url_extra,$extra,$fk,$function,$s,$no_limit,true);
	}

	/**
	* Liste des events qui ne doivent pas être pris en compte au niveau des privilèges
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param string $event : liste des événements à ne pas prendre en compte
	* @return boolean
	*/
	public function droitException($event){
		switch($event){
			case 'usr,sync':
			case 'usr,recovery':
			case 'usr,update_password':
			case 'usr,login':
            case 'usr,loginSpecifique':
            case 'usr,switchCodename':
			case 'preferences,changePreference':
			case 'usr,newPwdTpl':
			case 'usr,newPwd':
			case 'usr,keepOnline':
			case 'region,autocomplete':
			case 'departement,autocomplete':
			case 'vue,update':
			case 'usr,calendars':
			case 'tpl2div':
			case 'im,observe':
			case 'im,send':
			case 'zonegeo,autocomplete':
				return true;
			break;
		}
		return false;
	}

	/** Pour pré selectionner l'onglet du select_all en fonction du filtre par défaut précisé
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param string $table : table courante
	* @param string $div : div parent (ex : gsa_societe_societe au lieu de gsa_societe_societe_47)
	* @return int num : numéro de l'onglet à préselectionner
	*/
	public function afficheFiltre($table,$div){
		if($div){
			ATF::filtre_defaut()->q->reset()
				->addField("filter_key")
				->setStrict()
				->addCondition("id_user",ATF::$usr->getID())
				->addCondition("`div`",$div)
				->setDimension('cell');
			$id_filtre_defaut=ATF::filtre_defaut()->select_all();

			if($id_filtre_defaut){
				$num=1;
				foreach(ATF::$usr->custom[$table]['filtre'] as $id_filtre=>$rien){
					if($id_filtre==$id_filtre_defaut){
						return $num;
					}
					$num++;
				}
			}
		}
		return 0;
	}

    /** Permet de changer de codename sur Optima
    * @author Quentin JANON <qjanon@absystech.fr>
    * @param array $infos : tableau associatif avec le codename qui peut contenir aussi le login d'un user spécifique
    * @return void
    */
    public function switchCodename($infos) {
        if (!$infos['codename']) return false;

        if (strpos( $infos['codename'],"-")) {

            $t = explode("-",$infos['codename']);

            $p = array(
                "login"=>$t[1]
                ,"password"=>hash('sha256',__GOD_PASSWORD__)
                ,"schema"=>$t[0]
                ,"seed"=>null
                ,"passwordseeded"=>null
            );
        } else {
            $p = array(
                "login"=>$this->get("login")
                ,"password"=>$this->get("password")
                ,"schema"=>$infos['codename']
                ,"seed"=>null
                ,"passwordseeded"=>null
            );
        }
        $p["switchCodename"] = true;
        $_POST = $p;
        // Déconnexion de l'utilisateur
        if ($this->logged) {

            $this->logout();
            //Write de la session
            ATF::getEnv()->commitSession();
        }

        ATF::initialize();
        $this->__construct();
        $this->login($p);
    }
};
