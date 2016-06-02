<?php
/**
* Le portail d'objets a plusieurs intérêts : 
* 		- Permettre l'inclusion (include_once) et l'instanciation ( new xxx() ) uniquement au besoin et non le faire pour toutes les tables de la base à chaque page quelque soit la perspective utilisateur et si on va réellement s'en servir
*		- Permettre de centraliser les appels aux instances d'objets DDP (database driven programming) de manère plus optimisée qu'en stockant dans $GLOBALS (cf. ATF 4)
*		- Conserver les objets utilisé en cache pour ne pas instancier à nouveau les objets à chaque besoin
*		- Permettre l'accès à de multiples bases de données simplement, et ne s'y connecter qu'en cas de réelle nécessités
* @date 2008-01-01
* @package ATF
* @version 5
* @author Yann GAUTHERON <ygautheron@absystech.fr>
*/ 
class gate {
	protected static $singletons = array(); // Objets singletons stockés en un tableau statique
	protected static $db = array(); // Accès aux bases de données singletons 
	protected static $db_idents = array(
		/* Informations de connections aux bases de données :
		array(
			"database"=>
			,"login"=>
			,"password"=>
			,"host"=>
			,"port"=>
			,"type"=>
		),
		array(...
		*/
	);
	protected static $mysqlDefaultUser = NULL;
	protected static $mysqlDefaultPassword = NULL;
	protected static $mysqlDefaultHost = "localhost";
	protected static $mysqlDefaultPort = 3306;
	
	/* Gestion des bases de données bien cloisonnées mais regroupées par ce préfixe de base de données */
	protected static $schema = NULL;
	protected static $main_database = NULL;
	
	/* Selon la base demandée, on peut forcer d'utiliser une connection particulière avec son identifiant. 
	* @example array("extranet_v3_absystech"=>"fluor") 
	*/
	public static $dbIdentMapping = array();
	
	/* Classe mère et par défaut */
	protected static $motherClassName = "classes";
	
	/* Accès aux variables d'environnement via ATF */
	protected static $env;
	
	/**
    * Réinitialisation, pour réaccrochage des variables d'environnement
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    */ 
    public static function resetEnv() {
//log::logger("resetEnv","jgwiazdowski");
		self::$env = NULL;
	}
	
	/**
    * Retourne l'objet des variables d'environnement
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    * @return array
    */ 
    public static function getEnv() {
		return self::$env;
	}
		
	/**
    * Appel à une classe
	* @param string $name | Note : la valeur de $name est sensible à la casse.
	* @param array $arguments
	*	Mode classique :
	*		0 : Forcer une instance de telle classe (absystech pour forcer la classe du projet absystech par exemple, voir les Tests unitaires)
	*		1 : Appeler cette classe sur cette base de données
	*	@todo Mode associatif :
	*		0 : array
	*			class : Forcer une instance de telle classe
	*			db : Appeler cette classe sur cette base de données
	*			namespace : Faire appel à ce namespace en particulier
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    */ 
    public static function &__callStatic($name, $arguments=NULL) {
//log::logger("CallClass=".$name,"ygautheron");
		if (empty($name) || preg_match("/,/",$name)) {
			return;
		} elseif ($name==="usr" || $name===ATF::$usrClass) {
			return ATF::$usr;
		} else {
			// Portail de variables d'environnement
			//if (isset($arguments[0])) {
				switch($name) {
					case "_s":
					case "_g":
					case "_p":
					case "_r":
					case "_f":
					case "_e":
					case "_c":	
					case "_srv":
						if (!self::$env) {
//log::logger("INIT ENV => ERROR","ygautheron");
							self::$env = new env();
						}
						if (isset($arguments[1])) {
							return self::$env->set($name,$arguments[0],$arguments[1]);
						} else {
							return self::$env->get($name,$arguments[0]);
						}
				}
			//}
			
//log::logger('__callStatic namespace '.__NAMESPACE__,'ygautheron');
//if (strpos($name,"user")!==false) {
//	log::logger('__callStatic exists '.$name." => ".class_exists($name),'ygautheron');
//}
			
//			$include_path = __INCLUDE_PATH__;
//			if (file_exists($include_path.ATF::$project)) {
//				$include_path .= ATF::$project."/";
//			}
			
			//Pdf
			if($name=="html2pdf"){
				$obj="html2pdf";
				if(class_exists("html2pdf_".ATF::$codename)){
					$obj="html2pdf_".ATF::$codename;
				}
				if(!ATF::$singletons[$obj]){
					ATF::$singletons[$obj] = new $obj();
				}
				return self::getSingleton($obj);
			}
//log::logger("    ".$name." => ".ATF::$singletons[$name]." || ".$arguments." && ".$arguments[0]." && ".($arguments[0] && !(ATF::$singletons[$name] instanceof $arguments[0]))." [".ATF::$codename."[".$_SESSION["user"]->website_codename,"ygautheron");
/*if (ATF::$singletons[$name]) {
log::logger('__callStatic test get_class ('.$name.' / '.get_class(ATF::$singletons[$name]).')','ygautheron');
}*/
			if (!(ATF::$singletons[$name] = ATF::getSingleton($name)) || $arguments && $arguments[0] && !(ATF::$singletons[$name] instanceof $arguments[0]) || get_class(ATF::$singletons[$name])=='__PHP_Incomplete_Class') {
//				try {
					$table_exists = ATF::db()->table_or_view_exists($name);
//				} catch (errorATF $e) {
//					$table_exists = false;
//				}
//log::logger($include_path.$arguments[0]."/".$name.'.class.php','ygautheron');
				if (ATF::$schema && $arguments && $arguments[0] && file_exists(__INCLUDE_PATH__.$arguments[0]."/".$name.'.class.php')) { 
					// On écrase ce singleton $name par un objet $arguments[0]."_".$arguments[1] 
					// dont la classe se trouve définie dans /includes/$arguments[0]/$name.class.php
					$class = $name."_".$arguments[0];
//log::logger('__callStatic 0 new '.$class.'()','ygautheron');
//					if (!class_exists($class)) {
//						require_once __INCLUDE_PATH__.$arguments[0]."/".$name.'.class.php';
//					}
					if ($table_exists===true) {
						ATF::$singletons[$name] = new $class($name);
//					} elseif (!class_exists($class) && class_exists($name)) { // Uniquement à cause de la classe pdf qui n'est pas de la forme "pdf_absystech"
//						ATF::$singletons[$name] = new $name();
					} else {
						ATF::$singletons[$name] = new $class();
					}
				} elseif ($arguments && $arguments[0] && class_exists($arguments[0])) { 
					// On écrase ce singleton $name par un objet $arguments[0]
//log::logger('__callStatic 1 new '.$arguments[0].'()','ygautheron');
					ATF::$singletons[$name] = new $arguments[0]($name);  
				} elseif (ATF::$codename && class_exists($name."_".ATF::$codename)) {	 // La classe spécifique  existe, parfait
//log::logger('__callStatic 21 new '.$name.'()','ygautheron');
					$name_ = $name."_".ATF::$codename;
					ATF::$singletons[$name] = new $name_;  
				} elseif (class_exists($name)) { 	// La classe existe, parfait
//log::logger('__callStatic 22 new '.$name.'()','ygautheron');
					ATF::$singletons[$name] = new $name;  
				}  elseif (ATF::db() && $table_exists===true) {
//log::logger('__callStatic 3 new classes('.$name.')','ygautheron');
					ATF::$singletons[$name] = new ATF::$motherClassName($name);
				}  elseif (ATF::module() && method_exists(ATF::module(),'isAbstract') && !ATF::module()->isAbstract($name)) {//Si ce n'est pas une classe abstraite alors c'est une erreur
//log::logger('__callStatic false ('.$name.')','ygautheron');
					//$e = new errorATF("1 ".__CLASS__." not found : ".$name,402);
					//$e->setLog();
					return false;
				}
				
				ATF::setSingleton($name); // Sauvegarde dans la session
				
				if (isset($arguments[1]) && ATF::$db_idents[$arguments[1]]) {
					ATF::$singletons[$name]->setDB($arguments[1]);
				}
			} else {
//log::logger('__callStatic '.ATF::$singletons[$name]->table,'ygautheron');
//log::logger('__callStatic OK ALREADY THERE ('.$name.' / '.get_class(ATF::$singletons[$name]).')','ygautheron');
			}
			
			return ATF::$singletons[$name];
		}
    }
	
	/**
    * Sauver le singleton dans la session
	* @param string $name
	* @param classes $class
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
    */ 
    public static function setSingleton($name,$class=NULL) {
		if ($class) {
			ATF::$singletons[$name] = $class;
		}
		
		//ajout du name!='pdf' pour ne pas stocker les pdf dans la session
		if (session_id() !== "" && $name!="pdf") { // Seulement si la session a démarré
//log::logger("set > ".$name." => ".get_class(ATF::$singletons[$name]),"ygautheron");		

			ATF::_s("ATF,singletons,".$name,ATF::$singletons[$name]);
		}
    }
	
	
	/**
    * Récupérer le singleton dans la session
	* Maj : Utilisation de env pour accèder aux singletons stockés dans la session
	* @param string $name
    * @author Yann GAUTHERON <ygautheron@absystech.fr> Jérémie GWIAZDOWSKI <jgw@absystech.fr>
    */ 
    protected static function &getSingleton($name) {
		if( empty(ATF::$singletons[$name])
			&& session_id() !== "" // Seulement si la session a démarré
//			&& ATF::_s("ATF")
//			&& ATF::_s("ATF,singletons")
			&& ($c = ATF::_s("ATF,singletons,".$name))
			) {
			return $c;
		} elseif(isset(ATF::$singletons[$name])) {
			return ATF::$singletons[$name];
		}
    }
	
	/**
    * Alias de __callStatic
	* @param string $name
	* @param array $arguments
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    */ 
    public static function getClass($name) {
//log::logger(ATF::__callStatic($name)->table);
		return ATF::__callStatic($name);
    }
	
	/**
    * Supprime un singleton, permet de revenir à une classe principale plutôt qu'une classe spécifique d'un schema
	* @param string $name
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    */ 
    public static function unsetSingleton($name) {
		if (ATF::$singletons[$name]) {
        	unset(ATF::$singletons[$name]);
		}
		if( ATF::_s("ATF")
			&& ATF::_s("ATF,singletons")
			&& ATF::_s("ATF,singletons,".$name)
			) {
			ATF::_s("ATF,singletons,".$name,false);
		}
    }
	
	/**
    * Supprime tous les singletons de ATF::$singletons et de la session
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    */ 
    public static function resetSingletons() {
		foreach (ATF::$singletons as $name => $s) {
			self::unsetSingleton($name);
		}
    }
	
	/**
    * Supprime un identificateur de base de données
	* @param string $ident
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    */ 
    public static function unsetDBIdent($ident) {
		if (isset(ATF::$db_idents[$ident]) || ATF::$db_idents[$ident]===NULL) {
        	unset(ATF::$db_idents[$ident]);
		}
		if (isset(ATF::$db[$ident]) || ATF::$db[$ident]===NULL) {
        	unset(ATF::$db[$ident]);
		}		
    }
	
	/**
    * Retourne un identificateur de base de données
	* @param string $ident
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    */ 
    public static function &getDBIdent($ident) {
		return ATF::$db_idents[$ident];
    }
	
	/**
    * Défini un identificateur de base de données à partir d'un tableau
	* @param string $ident
	* @param array $infos Informations d'identifications
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    */ 
    public static function setDBIdent($ident,$infos) {
		ATF::$db_idents[$ident] = $infos;
		ATF::db($ident,true); // Forcer la reconnection
    }
	
	/**
    * Appel à une base de données, se reconnecte automatiquement si la connection est perdue
	* @param string $name, si NULL on retourne le premier objet de base de données initialisé
	* @param boolean $reconnect, si TRUE on force la reconnection
    * @author Yann GAUTHERON <ygautheron@absystech.fr> 
    */ 
	public static function &db($name=NULL,$reconnect=false) {
		if (empty(ATF::$db[$name]) || $reconnect) { // Aucune base de donnée connectée et disponible avec ce nom ?
			if ($name===NULL) {
				if (empty(ATF::$db_idents)) {
					throw new errorSQL('db ATF::$db_idents is empty !',59);
				} else {
					reset(ATF::$db_idents);
					$d = current(ATF::$db_idents); // Sinon on prend le premier descripteur qu'on trouve
				}
				// Utilisation d'un schema
				if (empty($d["database"]) && ATF::$schema) {
					$d["database"] = ATF::$main_database;
				}
			} else {
				$d = ATF::$db_idents[$name];
			}

			if (empty($d)) {
				throw new errorSQL("db definition empty (".$name.")!",58);
			}
			if ($d["database"] && class_exists($d["type"])) { // La définition de connection est-elle définie ?
				if (!$d["login"]) {
					$d["login"]=ATF::$mysqlDefaultUser;
					$d["password"]=ATF::$mysqlDefaultPassword;
				}
			
				//Connexion à la base
				ATF::$db[$d["name"]] = new $d["type"]($d["host"],$d["login"],$d["password"],$d["database"],$d["port"]);
				//Gestion d'erreur sur la connexion
				if(ATF::$db[$d["name"]]->connect_error) {
					// Société inexistante sélectionnée
					if(ATF::_g("event")=="usr,login"){
						throw new errorLogin();
					}else{
						throw new errorSQL(ATF::$db[$d["name"]]->connect_error,ATF::$db[$d["name"]]->connect_errno);
					}
				}
				
				//Setting du charset
				ATF::$db[$d["name"]]->setCharset($d["charset"]);
				//Show tables
				//ATF::$db[$d["name"]]->all_tables();
				
				if ($name===NULL) { // Référence comme objet de base défini par défaut
					ATF::$db[NULL] =& ATF::$db[$d["name"]];
				}
			}  else {
				throw new errorSQL("db '".$d["type"]."' not found (name=".$name.")",57);
			}
		}
		return ATF::$db[$name];
    }
	
//	/**
//    * Crée une seconde connexion à la base de données, afin d'effectuer des requêtes en dehors d'une transaction déjà en cours
//	* @param string $name, si NULL on retourne le premier objet de base de données initialisé
//    * @author Yann GAUTHERON <ygautheron@absystech.fr> 
//    */ 
//	public static function &cloneDB($name=NULL) {
//		if (isset(ATF::$db_idents[$name]) && is_array(ATF::$db_idents[$name])) {
//			$d = ATF::$db_idents[$name];
//			return new $d["type"]($d["host"],$d["login"],$d["password"],$d["database"],$d["port"]);
//		}
//	}
	
	/**
    * Défini les informations de connection à une base de données
	* @param string $name
	* @param string $database
	* @param string $login
	* @param string $password default ""
	* @param string $host default "localhost"
	* @param int $port default 3306
	* @param string $type default "mysql"
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    */ 
	public static function define_db($name,$database,$login=false,$password=false,$host=false,$port=false,$type="mysql",$charset="utf8") {

		if ($login===false && ATF::$mysqlDefaultUser) {
			$login=ATF::$mysqlDefaultUser;
		}
		if ($password===false && ATF::$mysqlDefaultPassword) {
			$password=ATF::$mysqlDefaultPassword;
		}
		if ($host===false && ATF::$mysqlDefaultHost) {
			$host=ATF::$mysqlDefaultHost;
		}
		if ($port===false && ATF::$mysqlDefaultPort) {
			$port=ATF::$mysqlDefaultPort;
		}
		if (isset(ATF::$db_idents[$name]) && ATF::$db_idents[$name]) { // L'indicatif existe déjà, on met à jour
			if ($database && $database !== ATF::$db_idents[$name]['database']) {
				ATF::$db_idents[$name]['database'] = $database;
				if (isset(ATF::$db[$name])) {
					ATF::$db[$name]->select_db($database);
				}
			}
			if ($login && $login !== ATF::$db_idents[$name]['login']) {
				ATF::$db_idents[$name]['login'] = $login;
			}
			if ($password && $password !== ATF::$db_idents[$name]['password']) {
				ATF::$db_idents[$name]['password'] = $password;
			}
			if ($host && $host !== ATF::$db_idents[$name]['host']) {
				ATF::$db_idents[$name]['host'] = $host;
			}
			if ($port && $port !== ATF::$db_idents[$name]['port']) {
				ATF::$db_idents[$name]['port'] = $port;
			}
			if ($type && $type !== ATF::$db_idents[$name]['type']) {
				ATF::$db_idents[$name]['type'] = $type;
			}
			if ($charset && $charset !== ATF::$db_idents[$name]['charset']) {
				ATF::$db_idents[$name]['charset'] = $charset;
			}
		} else { // Création de l'indicatif
			ATF::$db_idents[$name] = array(
				"database"=>$database
				,"login"=>$login
				,"password"=>$password
				,"host"=>$host
				,"port"=>$port
				,"type"=>$type
				,"name"=>$name
				,"charset"=>$charset
			);
			
			// Si on est en mode schema, on enregistre d'indicatif de la base principale

			if (ATF::$schema && ATF::$main_database && !isset(ATF::$db_idents["main"])) {
				ATF::$db_idents["main"] = array(
					"database"=>ATF::$main_database
					,"login"=>$login
					,"password"=>$password
					,"host"=>$host
					,"port"=>$port
					,"type"=>$type
					,"name"=>"main"
					,"charset"=>$charset
				);
			}
		}
	}
	
	/**
	* Donne le niveau de schema de l'application
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @date 30/03/2009
	* @return boolean true si le mode schema est activé
	*/
	public static function getSchema(){
		return ATF::$schema;
	}
};
?>