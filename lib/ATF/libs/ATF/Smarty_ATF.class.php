<?php
/**
* Classe Smarty_ATF
* Cet objet permet hérite de la classe Smarty, ajoute certaines fonctionalités utiles dans le framework ATF
*
* @date 2009-11-01
* @package ATF
* @version 5
* @author Yann GAUTHERON <ygautheron@absystech.fr>
*/ 

require_once __SMARTY_PATH__.'Smarty.class.php';

class Smarty_ATF extends Smarty {
	/**
	* Objet User utilisé pour la traduction dans les templates
	* @access private
	* @var object
	*/
	private $user;
	
	/**
	* Constructeur
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param user $user Rendre la localisation pour ce user
	*/	
	public function __construct($user=NULL) {
		parent::__construct();
		//Test pour vérifier que l'on pourra compiler les templates_c et donner une indication sur le problème
		if(!is_writable(__ABSOLUTE_PATH__.'templates_c/')){
			print_r("Problème de droit d'écriture sur ".__ABSOLUTE_PATH__.'templates_c/');
			throw new errorATF("Problème de droit d'écriture sur ".__ABSOLUTE_PATH__.'templates_c/');
		}
		
		$this->compile_dir = __ABSOLUTE_PATH__.'templates_c/';

		$this->majDirs();
		$this->setUser($user?$user:ATF::$usr);
	}

	function __toString() {
		return "";
	}
	
	/**
	* Défini l'utilisateur destinataire de l'évaluation du templates et notamment par rapport à la langue retournée
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param user $user
	*/	
	public function setUser($user) {
		$this->user=$user;
		$this->clearAssign("locUser");
		$this->assign("locUser",$user);
	}
	
	/**
	* Met à jour les chemins des plugins et templates
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	*/	
	public function majDirs() {
		$this->template_dir = array();
		if (ATF::$templates_dir) {
			foreach (explode(",",ATF::$templates_dir) as $t) {
				if (is_dir(__ABSOLUTE_PATH__.'templates/'.$t."/")) {
					$this->addTemplateDir(__ABSOLUTE_PATH__.'templates/'.$t.'/');
				}
			}
		}
		if (is_dir(__ABSOLUTE_PATH__.'templates/'.ATF::$codename."/")) {
			$this->addTemplateDir(__ABSOLUTE_PATH__.'templates/'.ATF::$codename.'/');
		}
		$this->addTemplateDir(__ABSOLUTE_PATH__.'templates/');
		$this->addTemplateDir(__ATF_PATH__.'templates/');

		if (is_dir(__LIBS_PATH__.'smarty_plugins/')) {
			$this->addPluginsDir(__LIBS_PATH__.'smarty_plugins/');
		}
		$this->addPluginsDir(__ATF_PATH__.'libs/smarty_plugins/');
		$this->addPluginsDir(__ATF_PATH__.'libs/ATF/smarty_plugins/');
		$this->addPluginsDir(__ATF_PATH__.'libs/Smarty/plugins/');
	}
	
	/**
	* Renvoie l'utilisateur associé
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @return user
	*/	
	public function getUser() {
		return $this->user;
	}
	
	/**
	* Assignation de chaque éléments d'un array 
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	*/	
	public function array_assign($a) {
		if(is_array($a)){
			foreach($a as $k => $i) {
				$this->assign($k,$i);
			}
		}
	}
	
	/**
	* Retourne un template avec l'analyzer
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $tpl Template appelé
	* @return string
	*/	
	public function fetchWithAnalyzer($tpl) {
		ATF::$analyzer->flag("fetch");
		$s = $this->fetch($tpl);
		ATF::$analyzer->end("fetch");
		return $s;
	}
	
	/**
	* Génère l'analyzer de Smarty qui peut être affiché dans le template copyright
	* @return string La chaine de caractère d'information sur la génération des templates.
	*/
	public function getSmartyAnalyzer(){
		$time_total= round(ATF::$analyzer->total(),3);
		$gen = $time_total." s.";
//		if (isset(ATF::$analyzer->flags["SQL"]["occurence"])) {
//			$gen .= " | ".ATF::$analyzer->flags["SQL"]["occurence"]."xQ "
//				.round(ATF::$analyzer->flags["SQL"]["total"],3)." s. "
//				.round(100*ATF::$analyzer->flags["SQL"]["total"]/$time_total)."%";
//		}
		if (isset(ATF::$analyzer->flags["fetch"])) {
			$gen .= " | ".((ATF::$analyzer->flags["fetch"]["occurence"])?ATF::$analyzer->flags["fetch"]["occurence"]:"1")."xT "
				.round(ATF::$analyzer->flags["fetch"]["total"],3)." s. "
				.round(100*ATF::$analyzer->flags["fetch"]["total"]/$time_total)."%";
		}
		return $gen;
	}
	
	/**
	* Affiche un template avec l'analyzer	
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $tpl Template appelé
	*/	
	public function displayWithAnalyzer($tpl) {
		$s =& $this->fetchWithAnalyzer($tpl);
		
		ATF::db()->makeHeaders();
		
		//$s = str_replace("[ATF:GENERATION_TIME]",$this->getSmartyAnalyzer(),$s);
		echo $s;
		return true;
	}
	
	/**
	* Retourne la date de dernière modif d'un fichier template	
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $tpl Template appelé
	*/	
	public function mtime($tpl) {
		foreach ($this->template_dir as $path) {
			if (file_exists($path.$tpl)) {
				$return = filemtime($path.$tpl);
				break;
			}
		}
		return $return;
	}
	
	/**
	* Template_exists optimisé
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $tpl
	*/
	public function template_exists($tpl){
		if (ATF::isTestUnitaire()) {
			return parent::templateExists($tpl);
		} else {
			$tpl_exists=ATF::_s("tpl_exists");
			
			if(!is_array($tpl_exists)){
				ATF::_s("tpl_exists",array("init"=>true));
			}
			
			if(isset($tpl_exists[$tpl])){
				return $tpl_exists[$tpl];
			}else{
				$tmp=parent::templateExists($tpl);
				$tpl_exists[$tpl]=$tmp;
				ATF::_s("tpl_exists",$tpl_exists);
				return $tmp;
			}
		}
	}
	
	/**
	* Ajoute un répertoire de template
	* @param string $dir Le répertoire en chemin absolu
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	*/
	public function addTplDir($dir){
		$this->template_dir[]=$dir;
	}
}
?>