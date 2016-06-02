<?php
/**
* Classe de configuration ATF
* @date 2008-01-01
* @package ATF
* @version 5
* @author Yann GAUTHERON <ygautheron@absystech.fr>
*/

class ATF extends gate {
	/**
	* Version du package
	* @var string
	*/
	public static $version = "5.7.1";

	/**
	* Objet de rendu des templates
	* @var smarty
	*/
	public static $html;

	/**
	* Objets des bases de données
	* @var array
	*/
	//public static $db = array();

	/**
	* Thème extjs par défaut
	* @var string
	*/
	public static $extJStheme = "gray";

	/**
	* Expiration de la session en secondes
	* @var int
	*/
	private static $sessionActivityLimit = 1800;

	/**
	* Nom de la session à forcer
	* @var int
	*/
	private static $sessionName = NULL;

	/**
	* ID de la session à forcer
	* @var int
	*/
	private static $sessionId = NULL;

	/**
	* Debug request flag
	* Only fatal errors : $_REQUEST[$flag_debug]==1
	* All errors : $_REQUEST[$flag_debug]==2
	* @var string
	*/
	private static $flag_debug = "debug";

	/**
	* Sous domaine du site web utilisé pour définir le langage (Exemple : http://en.adsolaris.eu/
	* @var string
	*/
	private static $subdomain = NULL;

	/**
	* Phpinfo request flag
	* Show phpinfo if $_REQUEST[$flag_phpinfo]==1
	* @var string
	*/
	private static $flag_phpinfo = "nfo";

	/**
	* Ancien Système d'erreur ??
	* Errors
	* @todo Voir si c'est encore utilisé !
	* @var array
	*/
	private static $errors = array(
		"gate" => array(
			1 => "Class not found"
			,2 => "No database definition found (Please use ATF::define_db() to define at least one database connection descriptor)"
			,3 => "Invalid database descriptor (need database name and database type)"
		)
	);

	/**
	* Debug mode activé ou non, à TRUE les erreurs fatales sont affichées
	* @var bool
	*/
	public static $debug;

	/**
	* Classes amenées à être stockées dans la session
	* @var mixed
	*/
//	private static $session_classes = NULL;

	/**
	* Langue par défaut
	* @var string
	*/
	public static $default_language = "fr";

	/**
	* Autoload pager
	* @var bool
	*/
	public static $autoload_pager = true;

	/**
	* Nom du projet, variable utilisée pour chercher les répertoires d'include et templates correspondants
	* @var string
	*/
	public static $project = NULL;

	/**
	* Ldap host
	* @var Adrsse de connexion au serveur ldap
	*/
	private static $ldapHost;

	/**
	* Ldap DN
	* @var Domaine de connexion au serveur ldap
	*/
	private static $ldapDN;

	/**
	* Ldap Password
	* @var Password de connexion au serveur ldap
	*/
	private static $ldapPassword;

	/**
	* Ldap
	* @var Objet ldap
	*/
	private static $ldap;

	/**
	*  Application en cours
	* @var string
	*/
	public static $codename = NULL;

    /**
    *  Application en cours
    * @var string
    */
    public static $codenameForTraduction = NULL;

	/**
	* Singleton analyzer
	* @var analyzer
	*/
	public static $analyzer;

	/**
	* Tracabilité activée ou non
	* @var bool
	*/
	public static $tracabilite = true;

	/**
	* Contrôleur de privileges
	* @var mixed
	*/
	public static $controller = NULL;

	/**
	* Classe contrôleur par défaut
	* @var string
	*/
	private static $controllerClass = "controller";

	/**
	* Classe mère ATF
	* @var string
	*/
	/*private static $motherClassName = "classes"; */

	/**
	* Classe pdf par défaut
	* @var string
	*/
	public static $pdf;

	/**
	* Singleton messager
	* @var msg
	*/
	public static $msg;

	/**
	* User courant
	* @var mixed
	*/
	public static $usr = NULL;

	/**
	* Objet de l'utilisateur loggué
	* @var string
	*/
	public static $usrClass = "usr";

	/**
	* Singleton user (table des utilisateurs)
	* @var string
	*/
	public static $userClass = "user";

	/**
	* Localisation des constantes dans la base de données
	* @var array
	*/
	public static $default_constants = array(NULL,"constante","constante","valeur");

	/**
	* Ceci est le mail d'expéditeur par défaut intégré à chaque mail envoyé
	* @var string
	*/
	public static $mailfrom = "ATF noreply <ne.pas@repondre.merci>";

	/**
	* Interception des emails, souvent utile aux dev
	* @var string
	*/
	public static $mailcopy;

	/**
	* Interception des emails, souvent utile aux dev.
	* Par sécurité si le flag DEV est a true et que mailinterceptor n'est pas définie, cette variable est initialisée a dev@absysetch.fr au premier mail envoyé
	* @var string
	*/
	public static $mailinterceptor;

	/**
	* Mailbox de debug
	* @var string
	*/
	public static $debugMailbox = "debug@absystech.net";

	/**
	* Préfixe utilisé dans l'objeet des mails
	* @var string
	*/
	public static $mailprefix;

	/**
	* Num du thread
	* Permet d'avoir le numéro de séquence d'une exécution PHP, le numéro est initialisé plus bas via la fonction time
	* @var int
	*/
	public static $id_thread;

	/**
	* Adresse du serveur statique pour les js, css, images et flash
	* @var string
	*/
	public static $staticserver;

	/**
	* Div à rafraichir à chaque appel Ajax
	* @var array
	*/
	public static $div_refresh=array("top");

	/**
	* Objet Cadre_refreshed
	* @var cr
	*/
	public static $cr;

	/**
	* Objet Json
	* @var json
	*/
	public static $json;

	/**
	* Tableau d'objets de transactions d'autocommit
	* @var array
	*/
	public static $queue = array();

	/**
	* Mode test unitaires
	* @var boolean
	*/
	public static $testsUnitaires = false;

	/* Chemins d'inclusion, par priorité */
	private static $path = array(
		"include"=>array()
		,"template"=>array()
	);

	/**
	* Objet Mobile
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @var mobile
	*/
	public static $mobile;

	/**
	* Flag permettant la création automatique des nouvelles traductions possible dans les tables de localisation
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @var autoAddNewTranslations
	*/
	public static $autoAddNewTranslations = false;

	/**
	* Répertoire de templates principal et prioritaire
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @var templates_dir
	*/
	public static $templates_dir;

	/**
	* Taille max d'upload autorisé sur le ce serveur
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @var string
	*/
	public static $maxFileSize = 0;

	/**
    * Initialisation du framework
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    */
	public static function initialize() {
		/* Id Thread */
		ATF::$id_thread=microtime();
		/* Analyzer */
		self::setAnalyzer();

		/* Debug mode */
		self::setDebugMode(ATF::$debug,ATF::$flag_debug,ATF::$flag_phpinfo);

		/* Classes système */
		include_once "error.class.php";

		/* Interfaces (non gérés par l'autoload)*/
		include_once "db.interface.php";
		include_once "rssInterface.interface.php";

		/* Initialisation du Moteur d'erreur en production
		   En dev c'est la directive "auto_prepend_file" qui doit être utilisée afin de gérer les parses error
		   Il suffit de mettre dans le .htacess la ligne suivante :
		   php_value auto_prepend_file "/home/www/optima/core/libs/ATF/error.inc.php"
		*/
		include_once "error.inc.php";

		/* Démarrage de la session */
		self::resetEnv();
		self::$env = new env();

		self::applyCodename();
		self::activityLimitation();
		self::resetSingletons();

		/* Création de l'utilisateur */
		ATF::createUser();

		/* Application du user courant à ATF (utilisé pour le cryptage AES) */
		ATF::setUser(ATF::_s("user"));

		/* Singleton des messages
		* @author Yann GAUTHERON <ygautheron@absystech.fr>
		* @todo pas très joli... le mieux serait de faire toujours des new errorATF($s,....);
		* pour avoir toujours la session en paramètres, cette méthode serait aussi meilleure
		* pour tester les erreurs en tests unitaires, mais pour simplifier les
		* nombreux "throw new errorATF()" je prend sur moi et bind par référence l'objet message du user de la session !
		*/
		ATF::$msg =& ATF::$usr->getMsg();

		// Choix de lalangue
		ATF::setLanguage();

		ATF::$html = new Smarty_ATF;

		ATF::$mobile = new mobile;

		// Met à jour la date d'activité du user, s'il est identifiable
		ATF::$usr->activity();

		// Récupération des CI
		//include_once __INCLUDE_PATH__.ATF::$codename."/ci.inc.php";

		// Json
		ATF::$json=new json();

		// crefresh
		ATF::$cr=new crefresh(ATF::$html,ATF::$json,ATF::$msg,ATF::$div_refresh);

		// Exécution
		return self::execController();
	}

	/**
	* Chargement des constantes
	* Effet de bord : Affichage du résultat du controller sur la sortie d'affichage
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @return string|bool Le résultat de l'éxécution ou un booléen
	*/
	public static function loadConsts(){
		if (ATF::$default_constants && !empty(ATF::$codename)) {
			ATF::loadConstants(ATF::$default_constants[0],ATF::$default_constants[1],ATF::$default_constants[2],ATF::$default_constants[3]);
		}
	}

	/**
	* Exécute le controller du projet
	* Effet de bord : Affichage du résultat du controller sur la sortie d'affichage
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @return string|bool Le résultat de l'éxécution ou un booléen
	*/
	private static function execController(){
		ATF::$controller = new ATF::$controllerClass(ATF::$usr);
		ATF::$controller->check_request_access($_REQUEST,$_POST,$_SESSION);
		return ATF::$controller->defaut($_REQUEST,$_GET,$_POST,$_SESSION,$_FILES);
	}

	/**
    * Ajout d'un chemin de recherche de fichier include, ou template
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    * @param string $type include|template
    * @param string $path Chemin
    */
	public static function addPath($type,$path) {
		if (isset(ATF::$path[$type])) {
			self::$path[$type][]=$path;
		}
	}

	/**
    * Ajout d'un chemin de recherche de fichier include, ou template
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    * @param string $type include|template
    * @param string $path Chemin
    */
	public static function getPaths($type) {
		return self::$path[$type];
	}

	/**
    * Application de l'Analyzer
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    */
	public static function setAnalyzer() {
		ATF::$analyzer = new analyzer();
		ATF::$analyzer->start();
		ATF::$analyzer->flag("ATF init");
	}

	/**
    * Définitions de configuration
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    * @param string $name
    * @param mixed $value
    */
	public static function define($name,$value) {
		switch ($name) {
//			case "session_classes":
//				ATF::${$name} = explode(",",$value);
//				break;

			default:
				ATF::${$name} = $value;
		}
	}

	/**
    * Applique le degré de debug
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    */
	private static function setDebugMode($debug,$flag_debug,$flag_phpinfo) {
		error_reporting(0); // Par défaut on ne doit rien voir comme erreur (Config de PRODUCTION !)
		if ($debug===true) {
			error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED);

			// Niveau d'affichage d'erreurs
			switch ($_REQUEST[$flag_debug]) {
				case 2:
					error_reporting(E_ALL);
					break;

				default:
			}

			// Affichage des infos PHP
			if (isset($_REQUEST[$flag_phpinfo]) && $_REQUEST[$flag_phpinfo]==1)
				self::phpinfo();
		}
	}

	/**
    * Affiche le PHPInfo
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    */
	private static function phpinfo() {
		phpinfo();
		throw new Exception('phpinfo');
	}

	/**
    * Limitation de la session en indépendance de la durée du cookie !
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    */
	private static function activityLimitation() {
		 if (ATF::_s("date_activity") && (time() - ATF::_s("date_activity") > self::$sessionActivityLimit)) {
			// session started more than 30 minates ago
			self::$env->resetSession();
		}
		ATF::_s("date_activity",time());
	}

	/**
    * Appliquele langage
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    */
	private static function setLanguage() {
		if (!ATF::$usr->logged) {
			if (ATF::$subdomain && loc::exists(ATF::$subdomain)) { // Ok !
				ATF::$usr->set("id_language",ATF::$subdomain);
			} elseif ($id_langue=ATF::$usr->getDefaultLanguage()) {
				ATF::$usr->set("id_language",$id_langue);
			} elseif (isset($_SERVER["HTTP_ACCEPT_LANGUAGE"]) && loc::exists(substr($_SERVER["HTTP_ACCEPT_LANGUAGE"],0,2))) {
				ATF::$usr->set("id_language",substr($_SERVER["HTTP_ACCEPT_LANGUAGE"],0,2));
			} elseif (loc::exists(ATF::$default_language)) {
				ATF::$usr->set("id_language",ATF::$default_language);
			} else {
				echo "No Language file ".$language_path.ATF::$default_language.".inc.php found !";
			}
		}
	}

	/**
    * Définition des schémas, plusieurs méthodes pour l'identification de la base de données à utiliser selon le cas
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    */
	public static function applyCodename() {
		if (ATF::$schema!==NULL) {
			if (isset($_POST["s"]) && isset($_POST["u"]) && isset($_POST["p"])) { // Pour l'API telescope !
				/* Formulaire de login */
				ATF::select_db($_POST["s"]);
				//define("__SECURE_SESSION__",true);
				self::$env->resetSession();

			} elseif (isset($_POST["schema"]) && isset($_POST["login"]) && isset($_POST["password"])) {
				/* Formulaire de login */
				ATF::select_db($_POST["schema"]);
				//define("__SECURE_SESSION__",true);
				self::$env->resetSession();

			} elseif (isset($_SERVER["CODENAME"])) {
				require_once __DIR__."/testsuite.class.php";
//log::logger("shell","ygautheron");
				/* Execution d'un script shell */
				ATF::select_db($_SERVER["CODENAME"]);
				//define("__SECURE_SESSION__",true);
				self::$env->resetSession();

			} elseif (isset($_SERVER["argv"][1]) && !ATF::$testsUnitaires) {
//log::logger("shell","ygautheron");
				/* Execution d'un script shell */
				ATF::select_db($_SERVER["argv"][1]);
				//define("__SECURE_SESSION__",true);
				self::$env->resetSession();

			} elseif (isset($_GET["schema"])) {
//log::logger("url","ygautheron");
				/* Login direct via URL */
				ATF::select_db(base64_decode(urldecode($_GET["schema"])));
				//define("__SECURE_SESSION__",true);
				self::$env->resetSession();

			} elseif (function_exists("apache_request_headers") && ($headers = apache_request_headers()) && isset($headers["x-optima-schema"])) {
//log::logger("url","ygautheron");
				/* Login direct via URL */
				ATF::select_db($headers["x-optima-schema"]);
				//define("__SECURE_SESSION__",true);
				self::$env->resetSession();

			} elseif (ATF::_s("user")->logged===true && ATF::_s("user")->website_codename) {
//log::logger("session","ygautheron");
				/* Session en cours... */
				ATF::select_db(ATF::_s("user")->website_codename);

			} elseif (isset($_COOKIE['optima']['societe']) ) {
//log::logger("shell","ygautheron");
				/* Execution d'un script shell */
				ATF::select_db($_COOKIE['optima']['societe']);
				//define("__SECURE_SESSION__",true);
				self::$env->resetSession();

			} else {
//log::logger("secured","ygautheron");
				//define("__SECURE_SESSION__",true);
				self::$env->resetSession();
			}
		}

		/* Chargement des constantes de la base de données */
		ATF::loadConsts();

		if (!isset(ATF::$codename) && isset(ATF::_s("user")->website_codename)) {
			ATF::define('codename',ATF::_s("user")->website_codename);
		}

		// Application du codename a l'email d'interception
		if (strpos(ATF::$mailinterceptor,"{codename}")>-1) {
			ATF::define('mailinterceptor',str_replace("{codename}",ATF::$codename,ATF::$mailinterceptor));
		}

	}

	/**
    * Création de l'utilisateur
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    */
	private static function createUser() {
		/* Création de l'utilisateur */
		if (class_exists(ATF::$usrClass)) {
			$c = ATF::$usrClass;
			$c::setDbSyncClassName(ATF::$userClass);
			if (!(ATF::_s("user") instanceof ATF::$usrClass)) {
//log::logger('pas de session',ygautheron);
				ATF::_s("user",new ATF::$usrClass());
			}
		} else {
			throw new errorATF("core class '".ATF::$usrClass."' not found !");
		}
	}

	/**
	* Choisir la base de données en fonction du login (mode schema, plusieurs bases derrière l'application)
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return &user un pointeur sur l'objet user
	*/
	public static function select_db($db){
		if (isset(ATF::$dbIdentMapping[ATF::$schema.$db])) {

			// Serveur particulier selon la base de données
			$id = ATF::getDBIdent(ATF::$dbIdentMapping[ATF::$schema.$db]);
			$id["name"]="db";
			if (!$id["database"]) {
				$id["database"]=ATF::$schema.$db;
			}
			ATF::setDBIdent($id["name"],$id);
		} else {
			ATF::define_db("db",ATF::$schema.$db,ATF::$mysqlDefaultUser,ATF::$mysqlDefaultPassword,ATF::$mysqlDefaultHost,ATF::$mysqlDefaultPort);
		}
		ATF::define('codename',$db);
	}

	/**
    * Charges toutes les constantes de la table passée en paramètre en constante PHP
	* @param string $database_name Bas ed edonnée à solliciter
	* @param string $table Table à solliciter
	* @param string $field_constant_name Champ contenant le nom de la constante à utiliser
	* @param string $field_value Champ contenant la valeur à utiliser
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    */
	static function loadConstants($database_name=NULL,$table="constante",$field_constant_name="constante",$field_value="valeur") {
		/* Import des variables globales */
		if ($db = ATF::db($database_name)) {
			if ($constantes = $db->sql2array("SELECT * FROM `".$table."`")) {
				foreach ($constantes as $key => $item) {
					if (!defined($item[$field_constant_name])) {
						define($item[$field_constant_name],$item[$field_value]);
					}
				}
			}
		}
	}

	/**
	* Donne le niveau de Debug de l'application
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @date 30/03/2009
	* @return boolean true si le mode debug est activé
	*/
	public static function getDebug(){
		return ATF::$debug;
	}

	/**
	* Retourne le mtime de ce fichier
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/
	public static function getLastUpdate(){
		return filemtime(__ATF_PATH__."libs/ATF/ATF.class.php");
	}

	/**
	* Définit le user courant de ATF
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param user l'objet utilisateur
	*/
	public static function setUser(&$usr){
		ATF::$usr=$usr;
	}

	/**
	* Retourne l'utilisateur courant de ATF
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @return &user un pointeur sur l'objet user
	*/
	public static function &getUser(){
		return ATF::$usr;
	}

	/**
	* Gère les erreurs de l'application en fin de traitement : envoie de mail si mode production, retour d'une modal box en dev,etc,...
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param Exception $Exception L'exception à traiter
	* @param boolean $ajax true si on se trouve en mode ajax
	*/
	public static function errorProcess($Exception,$ajax=false){
		/*Setting de l'exception en objet error si ce n'est pas le cas*/
		if(!($Exception instanceof error) && !($Exception instanceof errorSQL)){
			$Exception=new errorATF($Exception->getMessage(),$Exception->getCode(),NULL,$Exception->getFile(),$Exception->getLine(),$Exception->getTraceAsString());
		}

		//Gestion des erreurs Ajax
		if($Exception instanceof errorAjax){
			if(ATF::getDebug()){
				$Exception->setError();
			}else{
				$body="Infos Error :\n\tUrl:";
				$body.=$Exception->getUrl();
				$body.="\n\tName:";
				$body.=$Exception->getName();
				$body.="\n\tMessage:";
				$body.=$Exception->getMessage();
				$body.="\n\tCode:";
				$body.=$Exception->getCode();
				$body.="\n\tLine:";
				$body.=$Exception->getLine();
				$body.="\n\tFile:";
				$body.=$Exception->getFile();
				$body.="\n\tJsStack:";
				$body.=$Exception->getJsStack();
				$body.="\n\tTrace:";
				$body.=$Exception->getStack();

				if (ATF::_s("user")) {
					$body .= "\n\nInfos User :\n".log::array2string(ATF::$usr->getInfos());
				}

				// Envoi du mail
				$mail = new mail(array(
						"objet"=>"[IMPORTANT] ATF AJAX CORE PANIC - ".ATF::$project." ".ATF::$codename
						,"body"=>$body
						,"from"=>"ATF 5 <atf@absystech.fr>"));
				//$mail->send(self::$debugMailbox,true);
			}
			return;
		}

		//Enregistrement de l'erreur
		$Exception->setError();

		//Traitement de l'erreur
		if (ATF::getDebug()) {
			// Mode Dev
			if(!$ajax){
				//header("Refresh: 5;");
				echo "<b>ATF Core Panic ! - ATF Exception :</b>\n";
				if (ATF::$msg) {
					print_r(ATF::$msg->getErrors());
				} else {
					echo $Exception->getMessage()."\n".$Exception->getFile()."\n".$Exception->getLine()."\n".$Exception->getStack();
				}
			}
		} else {
			//Mode production
			if (ATF::$msg) {
				$body = log::array2string(ATF::$msg->getErrors());
			} else {
				$body = "PANIC : No ATF::$msg";
			}
			$body .= "\n\nGET\n".log::array2string($_GET);
			$body .= "\n\nPOST\n".log::array2string($_POST);
			$body .= "\n\tTrace:\n".$Exception->getMessage()."\n".$Exception->getFile()."\n".$Exception->getLine()."\n".$Exception->getStack();
			if ($Exception instanceof errorSQL) {
				$body .= "\n\nSQL\nDB= // ".log::array2string(ATF::db()->report())." // ".ATF::db()->getDatabase()." // ".log::array2string(ATF::db());
			}
			if (ATF::_s("user")) {
				$body .= "\n\n".log::array2string(ATF::$usr->getInfos());
			}

			// Envoi du mail
			$mail = new mail(array(
					"objet"=>"[IMPORTANT] ATF CORE PANIC - ".ATF::$project." ".ATF::$codename
					,"body"=>$body
					,"from"=>"ATF 5 <atf@absystech.fr>"));
			//$mail->send(self::$debugMailbox,true);

			if($ajax){
				//Setting de l'erreur utilisateur (le mail fush les erreurs pour ne revnoyer qu'une erreur basique à l'utilisateur
				if ($Exception instanceof errorSQL) {
					$Exception->setError();
				}else{
					$err=new errorATF(ATF::$usr->trans("error_ajax"));
					$err->setError();
				}
			} else {
				log::logger($Exception->getMessage()."\n".$Exception->getFile()."\n".$Exception->getLine()."\n".$Exception->getStack(), "error");
				header("Refresh: 5; url=/");
				echo "<b>Erreur irrécupérable... Vous allez être redirigé vers la page d'accueil.</b>\n";
			}
		}
	}

	/**
	* Construit les headers de la requête serveur (éléments renvoyés au navigateur)
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param $type string Le type de l'élément (exemple : text/css)
	* @param $cache boolean true si on désire mettre en place le système de cache
	* @param $expires int temps en secondes qui indique à quel moment le cache expire (même principe que les cookies)
	* @param $cache_private boolean paramètre qui indique que l'élément doit être enregistré uniquement par un cache privé (navigateur) et surtout pas par un proxy !
	*/
	public static function makeHeaders($type,$cache=true,$expires=86400,$cache_private=true){
		header("Content-type: ".$type."; charset=UTF-8");
		//header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
		if($cache){
			header("Expires: ".gmdate("D, d M Y H:i:s",time()+$expires)." GMT");
		}
		if($cache_private){
			header("Pragma: cache");
			header("Cache-Control: private");
		}
	}

	/**
    * Prédicat retournant VRAI si on est en mode test unitaire
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    */
	public static function isTestUnitaire() {
		return self::$testsUnitaires;
	}

	/**
    * Renvoie la constantes ATF demandée en paramètre
	* @param string $name
    * @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
    */
	public static function getDefined($name) {
		return self::${$name};
	}

	/**
    * Retourne le singleton ldap, ou le crée s'il n'existe pas
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $action insert|update|delete
	* @param array $infos
    */
	public static function ldap($action,$infos) {
		// Pas de définition Ldap => FALSE
		if (!self::ldapDefined()) {
			return false;
		}

		// Création de l'objet $ldap s'il n'existe pas + connexion
		if (!self::$ldap) {
			self::$ldap = new ldap(self::$ldapHost,self::$codename,self::$ldapDN,self::$ldapPassword);
		}

		// Execution de l'ordre
		if(ATF::db()->isTransaction()){
			ATF::db()->getQueue()->ldap($action,$infos);
		} else {
			self::$ldap->$action($infos);
		}

		return true;
	}

	/*
    * Prédicat retournant VRAI si ldap est activé
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    */
	public static function ldapDefined() {
		return !empty(self::$ldapHost);
	}
};
