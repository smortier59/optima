<?
/**
* Classe suivi
* Cet objet permet de gérer les suivis ! (sisi c'est vrai !)
* @package Optima
*/
class suivi_notifie extends classes_optima {
	/**
	* Constructeur
	*/
	public function __construct() { 
		parent::__construct();
		$this->table = __CLASS__;
	}
	
	/**
	 * Retourne le champ par défaut pour le libelle
	 * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	 * @param int $id
	 * @return string
	 */
	public function nom($id){
		if (!$id) return;
		$id = $this->decryptId($id); // On sait jamais s'il s'agit d'un md5
		$return=$this->select($id);
		return ATF::suivi()->nom($return["id_suivi"])." ".ATF::user()->nom($return["id_user"]);
	}
	
};

?>