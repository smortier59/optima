<?php
/**
* Moteur de messages. Permet d'enregistrer (méthode add)
* et de renvoyer des messages (méthode getAll) à l'interface utilisateur
* @date 2009-03-20
* @package ATF
* @version 5
* @author Jérémie Gwiazdowski <jgw@absystech.fr>
* @author Yann GAUTHERON <ygautheron@absystech.fr>
*/
class msg {
	/**
	* Messages notices
	* @var array
	*/
	private $notices = array();
	/**
	* Messages warnings
	* @var array
	*/
	private $warnings = array();

	/**
	* Messages erreurs
	* @var array
	*/
	private $errors = array();

	/**
	* Ajoute un nouveau message d'alertes : erreurs non bloquante
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $message Message
	* @param string $title Titre
	* @param string $time Temps durant lequel cela reste affiché
	* @return boolean true si l'ajout s'est bien passé
	*/
	public function addWarning($message,$title=NULL,$timer=NULL){
		//Ajout du message dans le tableau
		$this->warnings[] = array('msg'=>$message,'title'=>$title,'timer'=>$timer, 'type'=>'warning'); // Un array pour le futur éventuel où on aurait plusieurs options possibles pour une Notice
		return true;
	}

	/**
	* Renvoie tous les alertes
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param boolean $flush si flush est vrai alors on vide la collection
	* @return array un tableau a deux dimensions contenant les types ($tab[$indice]['type']) et les messages ($tab[$indice]['msg'])
	*/
	public function getWarnings($flush=true){
		$tmp = $this->warnings;
		if ($flush) {
			$this->warnings = array();
		}
		return $tmp;
	}

	/**
	* Ajoute un nouveau message d'information
	* @param string $message Message
	* @param string $title Titre
	* @param string $time Temps durant lequel cela reste affiché
	* @return boolean true si l'ajout s'est bien passé
	*/
	public function addNotice($message,$title=NULL,$timer=NULL){
		//Ajout du message dans le tableau
		$this->notices[] = array('msg'=>$message,'title'=>$title,'timer'=>$timer, 'type'=>'success'); // Un array pour le futur éventuel où on aurait plusieurs options possibles pour une Notice
		return true;
	}

	/**
	* Renvoie tous les messages d'info
	* @param boolean $flush si flush est vrai alors on vide la collection
	* @return array un tableau a deux dimensions contenant les types ($tab[$indice]['type']) et les messages ($tab[$indice]['msg'])
	*/
	public function getNotices($flush=true){
		$tmp = $this->notices;
		if ($flush) {
			$this->notices = array();
		}
		return $tmp;
	}

	/**
	* Renvoie tous les messages d'info et de warning
	* @param boolean $flush si flush est vrai alors on vide la collection
	* @return array un tableau a deux dimensions contenant les types ($tab[$indice]['type']) et les messages ($tab[$indice]['msg'])
	*/
	public function getAllNotices($flush=true){
		$n = $this->getNotices($flush);
		$w = $this->getWarnings($flush);

		return array_merge($n,$w);
	}

	/**
	* Ajoute un nouveau message
	* @param string $message l'intitulé du message qui sera affiché
	* @param int $type le type du message (__ERROR_GENERIC__, __ERROR_SQL__, __ERROR_APPLICATION__)
	* @return boolean true si l'ajout s'est bien passé
	*/
	public function addError($message,$type=__ERROR_GENERIC__){
		//Ajout du message dans le tableau
		$this->errors[] = array('type'=>$type,'msg'=>$message);
		return true;
	}

	/**
	* Renvoie tous les messages d'erreur
	* @param boolean $flush si flush est vrai alors on vide la collection
	* @return array un tableau a deux dimensions contenant les types ($tab[$indice]['type']) et les messages ($tab[$indice]['msg'])
	*/
	public function getErrors($flush=true){
		$tmp = $this->errors;
		if ($flush) {
			$this->errors = array();
		}
		return $tmp;
	}
};
?>
