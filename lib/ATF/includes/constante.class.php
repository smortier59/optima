<?php
/** 
* Classe constante : permet de gérer les constantes de l'application
* @package ATF
*/
class constante extends classes_optima {
	/**
	* Constructeur constante
	*/
	public function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		$this->colonnes["fields_column"] = array(	
			'constante.constante'
			,'constante.valeur'
		);
		//Redirection spécifique
		$this->defaultRedirect["insert"]="select_all";
		$this->defaultRedirect["update"]="select_all";
		$this->defaultRedirect["cloner"]="select_all";
		
		$this->fieldstructure();
	}
	
	/**
	* Fonction qui renvoie l'id dune constante
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $constante
	* @return array $id
	*/	
	public function getID($constante){
		$this->q->reset()
				->addField("id_constante")
			 	->addCondition("constante.constante",$constante)
    			->setStrict()
				->setDimension('cell');
		return $this->select_all();
	}
	public function getConstante($constante){ return $this->getID($constante); }
	
	/**
	* Fonction qui renvoie lla valeur dune constante
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $constante
//	* @param boolean $forceNewDBConnection Permet de forcer la création d'un autre connexion (CETTE METHODE EST COMPLETEMENT DECORELLEE D'UNE EVENTUELLE TRANSACTION SUR LA BASE DE DONNEES GENERALE)
	* @return mixed value
	*/	
	public function getValue($constante,$forceNewDBConnection=false){
//		if ($forceNewDBConnection) {
//			// Creer une nouvelle connexion
//			if ($db2 = ATF::cloneDB("db")) {
//				if($db2->connect_error) {
//					// Société inexistante sélectionnée
//					throw new errorSQL($db2->connect_error,$db2->connect_errno);
//				}
//				$query = "SELECT `valeur` FROM `".$this->table."` WHERE `constante`='".$db2->real_escape_string($constante)."'";
//				$return = $db2->ffc($query);
//				$db2->close();
//				return $return;		
//			} else {
//				throw new errorATF("Erreur de connexion à la seconde base (getValue) !");
//			}
//		} else {
			// Utiliser la connexion courante
			$this->q->reset()
					->addField("valeur")
					->addCondition("constante.constante",$constante)
					->setStrict()
					->setDimension('cell');
			return $this->select_all();
//		}
	}
	
	/**
	* Fonction qui renvoie lla valeur dune constante
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $constante
	* @param string $value
//	* @param boolean $forceNewDBConnection Permet de forcer la création d'un autre connexion (CETTE METHODE EST COMPLETEMENT DECORELLEE D'UNE EVENTUELLE TRANSACTION SUR LA BASE DE DONNEES GENERALE)
	* @return int
	*/	
	public function setValue($constante,$value,$forceNewDBConnection=false){
//		if ($forceNewDBConnection) {
//			// Creer une nouvelle connexion
//			if ($db2 = ATF::cloneDB("db")) {
//				if($db2->connect_error) {
//					// Société inexistante sélectionnée
//					throw new errorSQL($db2->connect_error,$db2->connect_errno);
//				}
//				$query = "UPDATE `".$this->table."` SET `valeur`='".$db2->real_escape_string($value)."' WHERE `id_constante`=".$this->getID($constante);
//				$return = $db2->query($query);
//				$db2->close();
//				return $return;
//			} else {
//				throw new errorATF("Erreur de connexion à la seconde base (setValue) !");
//			}
//		} else {
			// Utiliser la connexion courante			
			$id_constante = $this->getID($constante);
			if (!$id_constante) {
				$id_constante = $this->i(array("constante"=>$constante));
			}
			$infos = array(
				"id_constante"=>$this->getID($constante)
				,"valeur"=>$value
			);
			return $this->u($infos);
//		}
	}
};
?>