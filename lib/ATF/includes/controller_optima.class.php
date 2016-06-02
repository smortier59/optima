<?php
/**
* Contrôleur spécifique pour Optima
* @author Yann GAUTHERON <ygautheron@absystech.fr>
* @package ATF
*/
class controller_optima extends controller {
	/**
	* Constructeur
	* @param user $user L'utilisateur de la session
	*/
	public function __construct(&$user) {
		parent::__construct($user);
	}

	/**
	* Vérifie les droits selon le contexte
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>, Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array $request ($_REQUEST habituellement attendu)
	* @param array $post ($_POST habituellement attendu)
	* @param array $s ($_SESSION habituellement attendu)
	* @todo vérifier que le if ($module!="accueil") { est légitime
	*/
	public function check(&$request,&$post,&$s) {
		//Partie extjs
		if ($request["extAction"]) {
			$request["event"] = $request["extAction"].",".$request["extMethod"];
			ATF::_g('event',$request["event"]);
			ATF::$json->add("type","rpc");
			ATF::$json->add("action",$request["extAction"]);
			ATF::$json->add("method",$request["extMethod"]);
			ATF::$json->add("success",true);
		}
		// Méthode par défaut
		if (!$request["method"]) {
			$request["method"] = "html";
		}
		//Core check !
		if (!(isset($request["event"]) && ATF::$usr->droitException($request["event"])) && !(isset($request["k"]) && $this->user->loginInvited($request["k"])) && $request["event"]!="error") {
			switch ($request["method"]) {
				case "rss":
				case "asterisk":
					return true;
					break;
				case "tpl":
				case "html":
				case "ajax":
				case "div":
				case "dl":
				case "temp":
					//Recherche du module
					$module = $request["module"];
					if (!$module) {
						$module = $request["table"];
					}

					// Event
					// $request["event"] => Orientation de la vue finale
					// $checked_event => Privilège testé
					if (strpos($request["event"],",")!==false) {
						$module = explode(",",$request["event"]);
						$checked_event = $module[1];
						$module = $module[0];
						$obj=ATF::getClass($module);
						if($obj instanceof classes){
							$request["event"] = $obj->getDefaultRedirection($checked_event);
						} else {
							$request["event"]="select";
						}
					}
					// Mode invité
					if ($this->user->get("logged") && !$this->user->getID() && !$request["event"] && !$checked_event) {
						$this->user->logout();
						throw new errorATF($this->user->trans("invite_only"));
					}

					// Template (tpl2div)
					if (!$module && strpos($request["template"],"-")!==false) {
						$module = explode("-",$request["template"]);
						$module = $module[0];
						$request["event"] = "select";
					}

					// Le privilege testé doit toujours exister, sinon il est égal à l'event de la vue appelée
					if (!$checked_event) {
						 $checked_event = $request["event"];
					}

					//evenement spécifique pour la recherche globale pour éviter au gens de faire une recherche alors qu'ils sont déconnectés
					if($checked_event=="global_search"){
						if(!ATF::$usr->isLogged()){
							throw new errorLogin();
						}
					}

					if($module!="accueil" && $module!="upload" && $module!="creditsafe"){
						// Module forcé
						$real_module = $module;

						// Event forcé
						$real_event = $module;

						// Test du privilège
						$droit = $this->user->privilege($module,$checked_event,NULL,$real_module,$real_event,$s);

						// Si un module forcé, on force "table" et "module" dans $request
						if ($real_module != $module) {
							$request["table"] = $request["module"] = $real_module;
						}

						if ($droit===false) {
							if(!ATF::$usr->isLogged()){
								throw new errorLogin();
							}elseif($checked_event){
								$this->denied_reason = array("checked_event"=>$checked_event, "module"=>$module, "real_module"=>$real_module);
								throw new errorATF($this->user->trans("error_403_".$checked_event));
							}else{
								//throw new errorATF($this->user->trans("error_403"));
								throw new errorATF("error_403");
							}
						}
					}
			}
		}
	}

	/**
	* Actions dans le cas où aucun comportement de la méthode est demandée
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param array $request ($_REQUEST habituellement attendu)
	* @param array $get ($_GET habituellement attendu)
	* @param array $post ($_POST habituellement attendu)
	* @param array $s ($_SESSION habituellement attendu)
	* @param array $files ($_FILES habituellement attendu)
	*/
	public function defaut(&$request,&$get,&$post,&$s,&$files=NULL) {
		$retour=parent::defaut($request,$get,$post,$s,$files);
		// Si utilisateur non loggué, et qu'on est pas sur la page d'accueil, redirection vers accueil
		if (!(!ATF::$usr->droitException($get["event"]) && (!isset(ATF::$usr->logged) || !ATF::$usr->logged) && count($get)>0)){
			if($get['url']){
				header("Location: /".(defined("__ROOT_PATH__") ? __ROOT_PATH__ : NULL)."?".base64_decode($get['url']));
			}
		}
		return $retour;
	}

}
?>
