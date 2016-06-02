<?php
/**
* Moteur d'erreurs Basé sur le moteur d'Exception du language
* @date 2009-03-20
* @package ATF
* @version 5
* @author  Jérémie Gwiazdowski <jgw@absystech.fr>
* @author  Yann GAUTHERON <ygautheron@absystech.fr>
*/
class errorATF extends Exception {
	/**
	* Utilisateur localisé
	* @var user
	*/
	private $user;

	/**
	* La collection de messages
	* @var array
	* @deprecated
	*/
	//private $messages=array();

	/**
	* Message avancé
	* @var string
	*/
	private $advanced_message;

	/**
	* Pile spécifique
	* @var mixed
	*/
	private $stack;

	/**
	* Création d'une erreur ATF
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param string $message Un message personnalisé de description de l'erreur
	* @param int $code Un code d'erreur
	* @param error $previous l'erreur précédente
	* @param string $file Le nom du fichier si on désire personnalisé (utilisé par le handler de error.inc.php)
	* @param string $line Le numéro de la ligne si on désire personnalisé (utilisé par le handler de error.inc.php)
	* @param array $stack Une pile d'exécution
	*/
	public function __construct($message,$code=__PROGRAM_ERROR__,$previous=NULL,$file=false,$line=false,$stack=NULL){
		//$message .= nl2br($this->getTraceAsString());
		if (is_array($message)) {
			$this->advanced_message = $message;
			$message = "generic message : ".json_encode($message);
		}
		parent::__construct($message,$code?$code:1000,$previous);
		// Nom du fichier
		if($file){
			$this->file=$file;
		}
		// Numéro de ligne
		if($line){
			$this->line=$line;
		}
		// Stack spécifique
		$this->stack=$stack;
	}

	/**
	* Génère l'exception dans le moteur de message
	* @author  Jérémie Gwiazdowski <jgw@absystech.fr>
	* @return boolean true si le message a été corectement ajouté
	*/
	public function setMessage(){
		if(!ATF::$msg) return false;
		if ($this->advanced_message) {
			ATF::$msg->addError($this->advanced_message,$this->getCode());
		} else {
			ATF::$msg->addError(
				array(
					"text"=>$this->getMessage()
					,"params"=>array("title"=>$this->getErrName())
				)
				, $this->getCode()
			);//.' - ligne '.$this->getLine().' - fichier '.$this->getFile(),$this->getCode());
		}
		return true;
	}

	/**
	* Sauvegarde l'exception dans un fichier de journalisation
	* @author  Jérémie Gwiazdowski <jgw@absystech.fr>
	* @return boolean true si l'insertion s'est bien passée
	*/
	public function setLog(){
		$s = 'codename-'.ATF::$codename;
		if (ATF::getEnv() && ATF::getEnv()->isEnv("_s")) {
			$s .= chr(124).'user-'.(ATF::_s("user") ? ATF::_s("user")->getLogin() : NULL);
		}
		$s .= chr(124).'message-'.$this->getMessage()
			.chr(124).'code-'.$this->getCode()
			.chr(124).'file-'.$this->getFile()
			.chr(124).'line-'.$this->getLine()
			.chr(124).'trace-'.$this->getStack();
		log::logger($s,'error.log',false,true);
		return true;
	}

	/**
	* Indique l'erreur en base si l'argument inDb est à vrai et l'ajoute au moteur de message
	* @author  Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param boolean $inDb vrai si on désire conserver une trace de l'erreur en base
	* @retunr boolean true si l'erreur a été notifiée correctement
	*/
	public function setError($setLog=true){
		$this->setMessage();
		if($setLog){
			$this->setLog();
		}
		return true;
	}

	/**
	* Retourne le code d'erreur, mais cette méthode peut être surchargée
	* @author  Yann GAUTHERON <ygautheron@absystech.fr>
	* @return int
	*/
	public function getErrno(){
		return $this->getCode();
	}

	/**
	* Retourne le nom de l'erreur (erreur application,...)
	* @author  Jérémie Gwiazdowski <jgw@absystech.fr>
	* @return string
	*/
	public function getErrName(){
		return "Erreur";
	}

	/**
	* Donne la pile spéfifique si dispo
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @return string
	*/
	public function getStack(){
		if($this->stack){
			return print_r($this->stack,true);
		}else{
			return $this->getTraceAsString();
		}
	}
};

/**
* Erreurs SQL
* @date 2010-01-29
* @package ATF
* @version 5
* @author  Jérémie Gwiazdowski <jgw@absystech.fr>
*/
class errorSQL extends errorATF{
	/**
	* Numéro d'erreur SQL
	* @var int
	*/
	private $errno = 0;

	/**
	* Contruction d'une nouvelle erreur SQL avec un code d'erreur particulier
	* @author  Jérémie Gwiazdowski <jgw@absystech.fr>
	*/
	public function __construct($message,$code=NULL,$previous=NULL,$file=false,$line=false){
		parent::__construct($message,1000+__SQL_ERROR__,$previous,$file,$line);
		$this->errno = $code;
	}

	/**
	* Retourne l'erreur SQL
	* @author  Jérémie Gwiazdowski <jgw@absystech.fr>
	* @return int
	*/
	public function getErrno(){
		return $this->errno;
	}

	/**
	* Retourne le nom de l'erreur (erreur application,...)
	* @author  Jérémie Gwiazdowski <jgw@absystech.fr>
	* @return string
	*/
	public function getErrName(){
		return "Erreur SQL";
	}
};

/**
* Objet Erreur de login (et notamment lors de la perte de session)
*/
class errorLogin extends errorATF{
	/**
	* Création d'une erreur Login
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param string $message Un message personnalisé de description de l'erreur
	* @param int $code Un code d'erreur
	* @param error $previous l'erreur précédente
	* @param string file Le nom du fichier si on désire personnalisé (utilisé par le handler de error.inc.php)
	* @param string line Le numéro de la ligne si on désire personnalisé (utilisé par le handler de error.inc.php)
	*/
	public function __construct($message,$code=__LOGIN_ERROR__,$previous=NULL,$file=false,$line=false){
		parent::__construct($message,$code,$previous,$file,$line);
	}

	/**
	* Retourne le nom de l'erreur (erreur application,...)
	* @author  Jérémie Gwiazdowski <jgw@absystech.fr>
	* @return string
	*/
	public function getErrName(){
		return "Erreur de Session";
	}
}

/**
* Objet Erreur Ajax : Remontée d'erreur Ajax
*/
class errorAjax extends errorATF{
	/**
	* JsStack
	* @var string
	*/
	private $jsStack;

	/**
	* Url courante
	* @var string
	*/
	private $url;

	/**
	* Nom de l'erreur
	* @var string
	*/
	private $name;

	/**
	* Création d'une erreur Ajax
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param string $message Un message personnalisé de description de l'erreur
	* @param int $code Un code d'erreur
	* @param error $previous l'erreur précédente
	* @param string file Le nom du fichier si on désire personnalisé (utilisé par le handler de error.inc.php)
	* @param string line Le numéro de la ligne si on désire personnalisé (utilisé par le handler de error.inc.php)
	* @param string jsStack la pile Javascript
	* @param string url L'url ajax appelée au moment de l'incident
	*/
	public function __construct($message,$name=NULL,$previous=NULL,$file=false,$line=false,$jsStack=NULL,$url=NULL){
		$this->jsStack=$jsStack;
		$this->url=$url;
		$this->name=$name;
		parent::__construct($message,__AJAX_ERROR__,$previous,$file,$line);
	}

	/**
	* Retourne le nom de l'erreur (erreur application,...)
	* @author  Jérémie Gwiazdowski <jgw@absystech.fr>
	* @return string
	*/
	public function getErrName(){
		return "Erreur de javascript";
	}

	/**
	* Retourne la pile Js
	* @author  Jérémie Gwiazdowski <jgw@absystech.fr>
	* @return string
	*/
	public function getJsStack(){
		return $this->jsStack;
	}

	/**
	* Retourne l'url courante
	* @author  Jérémie Gwiazdowski <jgw@absystech.fr>
	* @return string
	*/
	public function getUrl(){
		return $this->url;
	}

	/**
	* Retourne le nom de l'erreur javascript
	* @author  Jérémie Gwiazdowski <jgw@absystech.fr>
	* @return string
	*/
	public function getName(){
		return $this->name;
	}
}

/**
* Exception de redirection (permalink)
*/
class RedirectionException extends errorATF{
	/**
	* L'url de redirection
	* @var string
	*/
	private $url;

	/**
	* Création d'une RedirectionException
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param string $url L'url de redirection
	*/
	public function __construct($url){
		$this->url=$url;
		parent::__construct("RedirectionException",__REDIRECTION_EXCEPTION__,NULL);
	}

	/**
	* Retourne l'url de redirection
	* @author  Jérémie Gwiazdowski <jgw@absystech.fr>
	* @return string
	*/
	public function getRedirectionUrl(){
		return $this->url;
	}
};
?>
