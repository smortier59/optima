<?php
/**
* La classe d'accès aux variables d'environnement ATF
* Par cette classe, tous les appels aux variables d'environnement sont maitrisés, y compris (surtout) leur modification
*
* @date 2010-01-23
* @package ATF
* @version 5
* @author Yann GAUTHERON <ygautheron@absystech.fr>
*/
class env {
	/**
	* Les variables d'environnement
	* @var array
	*/
	private $envMap = array();

	/**
	* La session courante
	*/
	private $session=array();

	/**
	* Lors de la création de cet objet, on attache toutes les variables d'environnement PHP
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/
	public function __construct(){
//log::logger("construct_env","jgwiazdowski");
		// Taille maximale d'upload
		ATF::define("maxFileSize",min(str_replace("M","",ini_get("upload_max_filesize")),str_replace("M","",ini_get("post_max_size") )));

		//$this->envMap["_s"] =& $this->session;
		$this->envMap["_g"] =& $_GET;
		$this->envMap["_p"] =& $_POST;
		$this->envMap["_r"] =& $_REQUEST;
		$this->envMap["_f"] =& $_FILES;
		$this->envMap["_e"] =& $_ENV;
		$this->envMap["_c"] =& $_COOKIE;
		$this->envMap["_srv"] =& $_SERVER;
		$this->startSession();
		$this->envMap["_s"] =& $_SESSION;
	}

	/**
	* Démarre la session
	*/
	public function startSession($name=NULL,$id=NULL){
		if (!$name) {
			$name = ATF::getDefined("sessionName");
		}
		if (!$id) {
			$id = ATF::getDefined("sessionId");

			// Passage de l'id_session en POST[ATFsess] ou GET[ATFsess]
			if (!$id && $this->envMap["_r"]["ATFsess"]) {
				$id = $this->envMap["_r"]["ATFsess"];
				ATF::define("sessionId",$id);
			}
		}
		if ($name) {
			session_name($name);
		}
		if ($id) {
			session_id($id);
		}
		session_set_cookie_params(86400*7,'/',$_SERVER['HTTP_HOST'],true,true);
		session_start();
//log::logger("[".md5(ATF::$id_thread)."] startSession compte pager(".count($_SESSION["pager"]->q).")","ygautheron");
//log::logger(sizeof($_SESSION["user"]),ygautheron);
//log::logger($_SESSION["user"],ygautheron);
		$this->session=$_SESSION;
		//log::logger($this->session,"jgwiazdowski");
// YG: Endromir le principe d'acceleration concurrente
//		session_commit();
	}

	/**
	* Réinitialise une session
	*/
	public function resetSession(){
//log::logger("[".md5(ATF::$id_thread)."] resetSession","ygautheron");
//		$this->session=array();
/* NB : tout mis en commentaire sauf "$this->session=array()" et $_SESSION=array();, car problème pour se connecter/déconnecter
		//Destroy*/
		$_SESSION=array();
		session_destroy();
		session_set_cookie_params(86400*7,'/',$_SERVER['HTTP_HOST'],true,true);
		session_start();
		self::__construct();
		/*session_destroy();
		//Nouvel identifiant
		session_regenerate_id();
		//Fermeture de la session
		session_commit();*/
	}

	/**
	* Ecrit la session
	*/
	public function commitSession(){

// YG: Endromir le principe d'acceleration concurrente
//		session_start();


//		$_SESSION=$this->session;
//log::logger("[".md5(ATF::$id_thread)."] commitSession compte pager(".count($_SESSION["pager"]->q).")","ygautheron");
		session_commit();
	}

	/**
	* Prédicat, retourne VRAI si la variable concernant un environnement maitrisé
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $var
	* @return boolean
	*/
	public function isEnv($var){
		return isset($this->envMap[$var]);
	}

	/**
	* Retourne la valeur de la variable contenue dans la variable d'environnement $var
	* @author Yann GAUTHERON <ygautheron@absystech.fr> Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param string $var
	* @param string $key
	* @return mixed
	*/
	public function get($var,$key=NULL) {
		if($var=="_s" && $key=="pager" && !$this->getDimension($this->envMap[$var],$key) ){
			return $this->setDimension($this->envMap[$var],$key,new pager());
		}
//		if($var=="_s"){
//			log::logger("[get]","jgwiazdowski");
//			log::logger("getSession=".$key,"jgwiazdowski");
//		}
		if(!isset($this->envMap[$var])) return false;
		if ($key) {
			$retour=$this->getDimension($this->envMap[$var],$key);
		} else {
			$retour=$this->envMap[$var];
		}
//		if($var=="_s"){
//			log::logger("[/get]","jgwiazdowski");
//		}
		return $retour;
	}

	/**
	* Met à jour, et retourne la valeur de la variable contenue dans la variable d'environnement $var
	* @author Yann GAUTHERON <ygautheron@absystech.fr> Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param string $var
	* @param mixed $key
	* @param mixed $value si FALSE on supprime la variable
	* @return mixed
	*/
	public function set($var,$key,$value) {
//		if($var=="_s"){
//			log::logger("[set]","jgwiazdowski");
//			log::logger("setSession=".$key,"jgwiazdowski");
//		}
		if ($value===false && $this->getDimension($this->envMap[$var],$key)){
			$this->unsetDimension($this->envMap[$var],$key);
			$retour=NULL;
		} else {
			$retour=$this->setDimension($this->envMap[$var],$key,$value);
		}
//		if($var=="_s"){
//			log::logger("[/set]","jgwiazdowski");
//		}
		return $retour;
	}

	/**
	* Renvoie la sous dimension désirée d'un élément
	* Cette méthode est récursive et permet d'obtenir la sous dimension d'une variable d'environnement en séparant chaque dimension par une virgule
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param mixed $var le tableau à plusieurs dimensions
	* @param string $search la valeur que l'on recherche
	* @return mixed la valeur recherchée
	*/
	private function getDimension($var,$search){
		//Présence d'une dimension
		if($pos=strpos($search,",")){
			//echo $pos."|";
			$key=substr($search,0,$pos);
			//echo $key."|";
			$search=substr($search,$pos+1);
			//echo $search;
			return $this->getDimension($var[$key],$search);
		//Condition d'arrêt
		}else{
			return $var[$search];
		}
	}

	/**
	* Set la variable à la dimension désirée
	* Attention Cette méthode est récursive
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param mixed $var le tableau à plusieurs dimensions
	* @param string $search la valeur que l'on recherche
	* @param bool $unset true si on désire unsete
	* @return mixed la valeur insérée ($value tout simplement !)
	*/
	private function setDimension(&$var,$search,$value){
		//Présence d'une dimension
		if($pos=strpos($search,",")){
			//echo $pos."|";
			$key=substr($search,0,$pos);
			//echo $key."|";
			$search=substr($search,$pos+1);
			//echo $search;
			return $this->setDimension($var[$key],$search,$value);
		//Condition d'arrêt
		}else{
			$var[$search]=$value;
			return $value;
		}
	}

	/**
	* Unset la variable à la dimension désirée
	* Attention Cette méthode est récursive
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param mixed $var le tableau à plusieurs dimensions
	* @param string $search la valeur que l'on recherche
	*/
	private function unsetDimension(&$var,$search){
		//Présence d'une dimension
		if($pos=strpos($search,",")){
			//echo $pos."|";
			$key=substr($search,0,$pos);
			//echo $key."|";
			$search=substr($search,$pos+1);
			//echo $search;
			return $this->unsetDimension($var[$key],$search);
		//Condition d'arrêt
		}else{
			unset($var[$search]);
		}
	}
};
?>