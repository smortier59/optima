<?php
/**
* Le contrôleur centralise les accès et vérifications des privilèges utilisateur
*
* @date 2009-01-07
* @package ATF
* @version 5
* @author Yann GAUTHERON <ygautheron@absystech.fr>
*/
class controller {
	/**
	* L'utilisateur associé ?
	* @var user
	*/
	protected $user;

	/**
	* Par défaut l'accès est autorisé, si faux le controleur ATF a refusé un accès
	* @var bool
	*/
	protected $granted = true;

	/**
	* Indication de perte de session !
	* C'est le check_request_access qui catch l'erreur du check pour paramètrer cette variable.
	* Il n'est pas possible de remonter l'erreur car le traitement est différé sur le denied_reason
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @var bool
	*/
	protected $lostSession = false;

	/**
	* Constructeur
	* @param user $user L'utilisateur de la session
	*/
	public function __construct(&$user) {
		$this->user=$user;
	}

	/**
	* Vérifie les droits selon le contexte
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param array $request ($_REQUEST habituellement attendu)
	* @param array $post ($_POST habituellement attendu)
	* @param array $s ($_SESSION habituellement attendu)
	*/
	public function check_request_access(&$request,&$post,&$s) {
		try {
			// Gestion des namespace
			//patch en attendant une modification des liens en .div qui mettent par exemple : devis,updateSelectAll dans $request["table"]
			if (isset($request["table"]) && (!preg_match('`,`',$request["table"]) || (preg_match('`,`',$request["table"]) && ($__m = explode(",",$request["table"])) && ($__c = $__m[0])))) {
				if($__c)$request["table"]=$__c;
			//if (isset($request["table"])) {
				$namespace = strstr($request["table"], "\\", true);
				if ($namespace && $request["id_".$request["table"]]) {
					$request["__real_table"] = str_replace($namespace."\\","",$request["table"]);
					$request["id_".$request["__real_table"]] = $request["id_".$request["table"]];
					unset($request["id_".$request["table"]]);
				}
			}

			return $this->check($request,$post,$s);
		}catch(errorLogin $e){
			$this->lostSession = true;
			$this->granted = false;
		}catch(RedirectionException $e){
			throw $e;
		}catch(errorATF $e){
			$this->granted = false;
		}
	}

	/**
	* Retourne le résultat du check du controlleur
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param array $request ($_REQUEST habituellement attendu)
	* @param array $post ($_POST habituellement attendu)
	* @param array $s ($_SESSION habituellement attendu)
	*/
	public function isGranted(&$request=NULL,&$post=NULL,&$s=NULL) {
		// Retour du résultat courant de check
		return $this->granted;
	}

	/**
	* Positionne le controller à granted false ou granted true
	* @param bool $value
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	*/
	public function setGranted($value){
		if($value){
			$this->granted = true;
		}else{
			$this->granted = false;
		}
	}

	/**
	* Vérifie les droits selon le contexte
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param array $request ($_REQUEST habituellement attendu)
	* @param array $post ($_POST habituellement attendu)
	* @param array $s ($_SESSION habituellement attendu)
	*/
	public function check(&$request,&$post,&$s) {
		return $this->check_event($request["table"],$request["event"],$s);
	}

	/**
	* Actions dans le cas où aucun comportement de la méthode est demandée
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param array $request ($_REQUEST habituellement attendu)
	* @param array $get ($_GET habituellement attendu)
	* @param array $post ($_POST habituellement attendu)
	* @param array $s ($_SESSION habituellement attendu)
	* @param array $files ($_FILES habituellement attendu)
	* @return bool true si tout s'est bien passé
	*/
	public function defaut(&$request,&$get,&$post,&$s,&$files=NULL) {

		// API REST OAUTH2
		if (api::process($_SERVER['REQUEST_METHOD'],$_SERVER['REDIRECT_URL'],$request,$post,$files)) {
			return false;
		}

		if($_COOKIE['l'] && $_COOKIE['s'] && $_COOKIE['p'] && $_COOKIE['optima']['societe'] && ATF::$usr->logged!==true){
			$infos = array('login'=>$_COOKIE['l'],
							'password'=>$_COOKIE['p'],
							'seed'=>$_COOKIE['s'],
							'schema'=>$_COOKIE['optima']['societe'],
							'store'=>true
			);
				//Vérification des données se trouvant dans le cookie
//			ATF::applyCodename();
			ATF::$usr->login($infos);
		}
		if (isset($request["table"])) {
			//Gestion des name_space
			if(strpos($request["table"],"___")){
				$class = explode("___",$request["table"]);
				$request["table"]=$class[0]."\\".$class[1];
			}
			//Vérification des cookie en vue d'une auto-connexion
			//Si on appel un module sans être loggé => page de login
			if($get["method"]!=="css" && $get["method"]!=="js") {
				if(isset(ATF::$usr->logged) && ATF::$usr->logged===true) {
					ATF::$html->assign("current_class",ATF::{$request["table"]}());
				}
			}
		}
		if (isset($get["method"])) {
			switch ($get["method"]) {
				case "dialog":
					$this->default_dialog($request,$get,$post,$s,$files);
					return false;
				case "div":
					/* Rafraichissement de div en ajax */
					$this->default_div($request,$get,$post,$s,$files);
					return false;
				case "ajax":
					/* Execution des scripts AJAX retournant du JSON */
					$this->default_ajax($request,$get,$post,$s,$files);
					return false;
				case "asterisk":
					// Execution des webservcies AGI. Les URL de type "http://dev.optima.absystech.net/asterisk/getAgentConcerned" appelées par Asterisk
					//ATF::makeHeaders("plain/text");
					if ($post["server"] && $get["schema"]) {
						$get["table"]="asterisk,webservice";
						$post["server"] = unserialize($post["server"]);
						$post["display"]=true;
						$this->default_exec($request,$get,$post,$s,$files);
					}
					return false;
				case "facture":
					if ($post["action"] && $get["schema"]) {
						$get["table"]="facture,webservice";
						$this->default_exec($request,$get,$post,$s,$files);
					}
					return false;
				case "js":
					if(ATF::$html->template_exists($get["table"].".tpl.js")){
						ATF::makeHeaders("text/javascript");
						ATF::$html->display($get["table"].".tpl.js");
					}
					return false;
				case "css":
					if(ATF::$html->template_exists($get["table"].".tpl.css")){
						ATF::makeHeaders("text/css");
						ATF::$html->display($get["table"].".tpl.css");
					}
					return false;
				case "temp":
					$request["temp"] = true;
				case "dl":
				case "kml":
					// Téléchargement de fichiers
					if (ATF::getClass($get["table"])){
						ATF::getClass($get["table"])->dl($request,$s);
					}
					return false;
				//Image !
				case "chart":
					if (ATF::getClass($get["table"])){
						echo ATF::getClass($get["table"])->chart($request,$s);
					}
					return false;
				case "png":
					if (ATF::getClass($get["table"])){
						ATF::makeHeaders("image/png");
						echo ATF::getClass($get["table"])->img($request);
					}
					return false;
				case "jpg":
					if (ATF::getClass($get["table"])) {
						ATF::makeHeaders("image/jpeg");
						echo ATF::getClass($get["table"])->img($request);
					}
					return false;
				case "gif":
					if (ATF::getClass($get["table"])){
						ATF::makeHeaders("image/gif");
						echo ATF::getClass($get["table"])->img($request);
					}
					return false;
				case "all_type":
					// Afficher ouvre un fichier de n'importe quel type (extension) stocké dans /temp/
					if (ATF::getClass($get["table"])){
						echo ATF::getClass($get["table"])->readfile($request);
					}
					return false;
				case "tpl":
					// Accès direct au template
					ATF::makeHeaders("text/html",false,0,false);
					ATF::$html->array_assign($post);
					ATF::$html->display($get["tpl"].".tpl.htm");
					return false;
// A mettre dans le controller spécifique ?
//				case "history":
//					/* On retourne en arrière dans l'historique */
//					if (ATF::$usr->navLastHistory()) {
//						header("Location: ".ATF::$usr->navLastHistory());
//						ATF::$usr->navRemoveLastHistory();
//						break;
//					}
				case "pdf":
					//ATF::unsetSingleton('pdf');
					ATF::pdf()->generic($get['function'],$get['id'],$get['params']);
					return false;
				case "html2pdf":
					if($get["dl"]){
						ATF::html2pdf()->setOutput("download");
					}else{
						ATF::html2pdf()->setOutput("pdf");
					}
					ATF::html2pdf()->generic($get["function"],$get["id"],$get["date"]);
					return false;
				case "xml":
				case "rss":
					$c=ATF::getClass($get["table"]);
					if ($c && method_exists($c,$get["method"])){
						echo $c->{$get["method"]}($request);
					}
					return false;
			}
		}
		return true;
	}

	/**
	* Traitements de la méthode dialog
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param array $request ($_REQUEST habituellement attendu)
	* @param array $get ($_GET habituellement attendu)
	* @param array $post ($_POST habituellement attendu)
	* @param array $s ($_SESSION habituellement attendu)
	* @param array $files ($_FILES habituellement attendu)
	*/
	public function default_dialog(&$request,&$get,&$post,&$s,&$files=NULL) {
		/* Appel d'une boite de dialogue contextuelle en HTML
		*		string $_POST[template] : calque HTML à afficher
		*/
		ATF::$html->array_assign($request); // On assigne toutes les variables du tableau POST comme variable normale dans l'objet smarty
		ATF::$html->display($get["table"].".tpl.dialog");
	}

	/**
	* Traitements de la méthode ajax
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param array $request ($_REQUEST habituellement attendu)
	* @param array $get ($_GET habituellement attendu)
	* @param array $post ($_POST habituellement attendu)
	* @param array $s ($_SESSION habituellement attendu)
	* @param array $files ($_FILES habituellement attendu)
	*/
	public function default_ajax(&$request,&$get,&$post,&$s,&$files=NULL) {
		//log::logger("AJAX","jgwiazdowski");
		if ($this->isGranted($request,$post,$s)) {

			try{
				switch ($get["event"]) {
					case "usr,login":
						ATF::$cr->block("top");
						try{
							ATF::$json->add("result",ATF::$usr->login($post)); // Exception du login
						}catch(errorATF $e){
							//Société erronée
							$e->setLog();
						}
						// Redirection automatique
						if ($post["url"]) {
							ATF::$json->add("location",__MANUAL_WEB_PATH__.($post["url"]));
						}
						// Donne la référence de session
						if ($post["sess"]) {
							ATF::$json->add("sess",session_id());
						}
//						// Donne infos nécessaires à l'application mobile : les onglets mobiles stockés en préférence
//						if ($post["mobile"]) {
//							ATF::$json->add("tabs",array("Hotlines","Societes","Geolocalisation"));
//						}
						break;

					case "error":
						throw new errorAjax($post["message"],$post["name"],NULL,$post["fileName"],$post["lineNumber"],$post["jsStack"],$post["url"]);

					case "tpl2div":
						/* Rafraichissement AJAX d'un div */
						$this->default_div($request,$get,$post,$s,$files);
						return false;

					default:

						/* Traitements ou rafraichissements demandés en appelant une méthode de classe PHP directement par leurs noms
						*		Principe d'appels de traitements par méthodes Objets via AJAX
						*		string $post[__m] : Méthode de la classe appelée, Exemple : "user,delete" pointera la méthode user::delete($post,$_SESSION)
						*		string $post[fallback] : Nécessaire en général lorsqu'on poste des fichiers (post standard, non ajax), redirigera vers cette URL
						*
						*		string $post[div] : calque HTML à mettre à jour en retour
						*		string $post[template] : template SMARTY à utiliser pour remplir le calque, s'il n'est pas défini la variable $post[div] est utilisée
						*		string $post[nocr] : Evite de rafraichir le cadre refreshed
						*/
						// Traitement particulier demandé
						if($post["nocr"]){
							$cadre_refreshed = NULL;
							unset($post["nocr"]);
						}else{
							$cadre_refreshed = array();
						}
						$get["table"] = $get["event"];

						//Foreach pas beau (@todo)
						if ($json = $this->default_exec($request,$get,$post,$s,$files,$cadre_refreshed)) {
							foreach($json as $index=>$j){
								ATF::$json->add($index,$j);
							}
						} else {
							return false;

						}
						//Ajout du cadre_refreshed dans l'objet (mode hybride)
						if(is_array($cadre_refreshed)){
							foreach($cadre_refreshed as $div=>$html){
								ATF::$cr->addHtml($div,$html);
							}
						}

						// On met tous les POST dans l'objet Smarty
						ATF::$html->array_assign($post);
						if($post["div"]) {
							// Refresh du div demandé avec le template demandé
							if (!$post["template"]) {
								$post["template"] = $post["div"];
							}
							ATF::$cr->add($post["div"],$post["template"].".tpl.htm");
						}
						// Temps de génération de la page
//						if (is_array($cadre_refreshed)) {
//							ATF::$cr->add("generationTime","generationTime.tpl.htm",array("generationTime"=>"true"));
//						}
						break;
				}
			}catch(errorAjax $e){
				ATF::errorProcess($e,true);
				ATF::$cr->reset()->resetDefault();
			}catch(errorSQL $e){
				ATF::errorProcess($e,true);
				ATF::$cr->reset()->resetDefault();
			}catch(errorATF $e){
				$e->setError();
				ATF::$cr->reset()->resetDefault();
			}catch(Exception $e){
				ATF::errorProcess($e,true);
				ATF::$cr->reset()->resetDefault();
			}
		}else{
			$this->setDeniedReason();
		}//end valid_ajax
		if (is_array($post["extTpl"])) {
			// Demande de refresh extJS
			foreach($post["extTpl"] as $div => $tpl){
				$tpl.=".tpl.js";
				if (ATF::$html->template_exists($tpl)) {
					$post["extTpl"][$div] = ATF::$html->fetch($tpl);
				} else {
					throw new errorATF("Template not found (".$tpl.")");
				}
			}
			ATF::$json->build(NULL,ATF::$msg);
			ATF::$json->add("extTpl",$post["extTpl"]);
			ATF::$json->add("success",true);
		} else {
			//Contruction du json (génération via le cadre refresh qui fait tout !)
			ATF::$cr->generate();
		}
		//Sauvegarde de la session
		ATF::getEnv()->commitSession();

		//Renvoie du json au navigateur
		echo ATF::$json->send();
	}

	/**
	* Traitements de la méthode div
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param array $request ($_REQUEST habituellement attendu)
	* @param array $get ($_GET habituellement attendu)
	* @param array $post ($_POST habituellement attendu)
	* @param array $s ($_SESSION habituellement attendu)
	* @param array $files ($_FILES habituellement attendu)
	* @return array JSON, qu'il faudra convertir en string JSON
	*/
	public function default_exec(&$request,&$get,&$post,&$s,&$files=NULL,&$cadre_refreshed=NULL) {

		// Traitement particulier demandé
		if ($get["table"] && ($__m = explode(",",$get["table"])) && ($__c = $__m[0]) && ($__m = $__m[1])) {
			if ((ATF::$__c() instanceof classes || ATF::$__c() instanceof $__c) && method_exists(ATF::$__c(),$__m)) {
				ATF::$html->assign('current_class',ATF::$__c());
				//dans le cas d'une url de type classe,methode.ajax,arg1=toto&arg2=tutu, on met les arguments
				// dans la variable $post pour les récupérer dans la méthode appelée
				unset($get['event'],$get['method'],$get['table']);
				foreach($get as $cle=>$item){
					$post[$cle]=$item;
				}
				if ($post["display"]) { // Si c'est le client qui demande uniquement d'afficher le résultat brut de cette méthode
					echo ATF::$__c()->$__m($post,$s,$files);
					return false;
				} else {
					$json["result"] = ATF::$__c()->$__m($post,$s,$files,$cadre_refreshed);
					ATF::$html->assign("json",$json);
				}
				// Si c'est la méthode qui demande à ne retourner que l'affichage de son résultat
				if ($post["display"]===true) {
					echo $json["result"];
					return false;
				}
			}
		}
		return $json;
	}

	/**
	* Traitements de la méthode div
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param array $request ($_REQUEST habituellement attendu)
	* @param array $get ($_GET habituellement attendu)
	* @param array $post ($_POST habituellement attendu)
	* 		Mise a jour d'un div a partir d'un template
	* 		string $_POST[div] : calque HTML à mettre à jour en retour
	* 		string $_POST[template] : template SMARTY à utiliser pour remplir le calque, s'il n'est pas défini la variable $_POST[div] est utilisée
	* 		string $_POST[page] : Numéro de page appelée
	* 		string $_POST[order] : Ordre de tri appelé : un champ défini dans la requête, ou plusieurs séparés par virgules
	* 		string $_POST[fields] : Champs de retour, lorsqu'on n'a pas besoin de tous les champs (optimisation), séparés par virgules
	* 		string $_POST[recherche] : Mots clés recherchés, la recherche s'effectue sur tous les champs définis dans $_POST[fields]
	* 		string $_POST[no_top] : savoir si on raffraîchit ou non le top
	*
	* 		Principe d'appels de traitements par méthodes Objets via AJAX
	* 		string $_POST[__m] : Méthode de la classe appelée, Exemple : "user,delete" pointera la méthode user::delete($_POST,$_SESSION)
	* @param array $s ($_SESSION habituellement attendu)
	* @param array $files ($_FILES habituellement attendu)
	* @return string l'affichage
	*/
	public function default_div(&$request,&$get,&$post,&$s,&$files=NULL) {
		if ($this->isGranted($request,$post,$s)) {
			try{
				switch ($get["event"]) {
					case "cloner":
					case "delete":
					case "insert":
					case "update":
					case "select":
						$func = "can_".$get["event"];
						$obj = ATF::getClass($get["table"]);
						if($obj && method_exists($obj,$func) && !$obj->$func($request["id_".$get["table"]])){
							ATF::$cr->block("top");
							throw new errorATF(loc::mt(
									ATF::$usr->trans("probleme_".$func,$get["table"])
									,array(
										"table"=>ATF::$usr->trans($get["table"],"module")
										,"function"=>ATF::$usr->trans($get["event"])
									)
								)
							);
						}
						if($get["event"]=="select"){
							//On fait la requête du select ici pour éviter les erreurs coté template et donc la difficulté de gérer les 404
							$class=ATF::getClass($get["table"]);
							if(is_a($class,"classes")){
								//Recherche du select
								if($request[$class->table]["id_".$class->name()]){
									$id=$request[$class->table]["id_".$class->name()];
								}else{
									$id=$request["id_".$class->name()];
								}

								//Test de validité de l'id
								$request[$class->table]=$class->select($id,NULL,$get["seed"]);

								//Check de cohérence
								if(!is_array($request[$class->table])){
									$post["template"]="error";
								}
							}
						}
					default:
						/* Div et template par défaut si aucun div renseigné */
						if (!$post["template"] && !$post["pager"]) {
							$post["template"] = "generic"; // Template par défaut
						}
						if (!$post["div"]) {
							$post["div"] = "main"; // div par défaut
						}

						ATF::$html->assign("requests",$request);

						ATF::$html->array_assign($post); // On assigne toutes les variables du tableau POST comme variable normale dans l'objet smarty

						// Traitement particulier demandé DEPRECATED
						$cadre_refreshed = array();

						//Foreach pas beau (@todo)
						if ($json = $this->default_exec($request,$get,$post,$s,$files,$cadre_refreshed)) {
							foreach($json as $index=>$j){
								ATF::$json->add($index,$j);
							}
						}

						//Ajout du cadre_refreshed dans l'objet (mode hybride)
						if(is_array($cadre_refreshed)){
							foreach($cadre_refreshed as $div=>$html){
								ATF::$cr->addHtml($div,$html);
							}
						}

						//Rafraîchissement du template désiré
						if ($post["template"] && $post["div"]) {
							ATF::$cr->add($post["div"],$post["template"].".tpl.htm");
						}

						// Js spécifique d'un module
						if(ATF::$html->template_exists($get["table"].".tpl.js")){
							ATF::$cr->add("main-script",$get["table"].".tpl.js");
						}

						// Temps de génération de la page
//						if (is_array($cadre_refreshed) && !$post["nognt"]) {
//							ATF::$cr->add("generationTime","generationTime.tpl.htm",array("generationTime"=>"true"));
//						}

						// Blocage du top
						if($post["notop"]){
							ATF::$cr->block("top");
						}

						// Mise à jour du title
						ATF::$cr->add("title","title",array("current_class"=>ATF::getClass($get["table"])));
				}
			}catch(errorSQL $e){
				ATF::errorProcess($e,true);
				ATF::$cr->reset()->resetDefault();
			}catch(errorATF $e){
				$e->setError();
				ATF::$cr->reset()->resetDefault();
			}catch(Exception $e){
				ATF::errorProcess($e,true);
				ATF::$cr->reset()->resetDefault();
			}
		}else{
			$this->setDeniedReason();
		}

		//Contruction du json (génération via le cadre refresh qui fait tout !)
		ATF::$cr->generate();

		//Sauvegarde de la session
		ATF::getEnv()->commitSession();

		echo ATF::$json->send();
	}

	/**
	* Check event par défaut des évenements spécifiques
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $module
	* @param string $event
	* @param array $s ($_SESSION habituellement attendu)
	*/
	public function check_event(&$module,&$event,&$s) {
		return NULL;
	}

	/**
	* Donne le nom du module utilisé dans le générique (generic.tpl.htm)
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param array $request les fameux requests php
	* @param classes $current_class la classe courante
	* @return string Le nom du module
	*/
	public function getTable($request,$current_class){
		if($request["table"]){
			return $request["table"];
		}elseif($current_class instanceof classes){
			return $current_class->name();
		}else{
			throw new errorATF("current_class_or_table_not_exist");
		}
	}

	/**
	* Donne l'évènement associé par défault (celui spécifié dans le request ou celui du constructeur)
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param array $request les fameux requests php
	* @param string $event le nom de l'évènement passé en paramètre à smarty
	* @return string Le nom de l'évènement
	*/
	public function getEvent($request,$table_name){
		if($request['event']){
			return $request['event'];
		}else{
			return "";
		}
	}

	/**
	* Donne le user associé au controller
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @return mixed l'objet user
	*/
	public function &getUser(){
		return $this->user;
	}

	/**
	* Mise en place de l'interdiction d'accès après un contrôle de droits négatif (isGranted)
	* Dans les cas ou isGranted==false on set le deniedReason
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	*/
	private function setDeniedReason(){
		if($this->lostSession){
			ATF::$json->add("nosession",true);
		}else{
			ATF::$json->add("denied",true);
			ATF::$json->add("denied_reason",ATF::$controller->denied_reason);
		}
		$err=new errorATF(ATF::$usr->trans("denied_reason"));
		$err->setError();
		ATF::$cr->reset()->resetDefault();
	}

	/**
	* Indique que la session est perdue (méthode de merde pour les Tu...)
	* Impossible de passer dans le code via controller uniquement Il faut faire un enfant controller (contoller Optima)
	* @param bool $value
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	*/
	public function setLostSession($value){
		$this->lostSession=$value;
	}
}
?>