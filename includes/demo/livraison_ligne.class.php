<?
/**
* Classe Livraison Ligne
* @author MOUAD EL HIZABRI
* @package Optima
* @subpackage Absystech
**/
class livraison_ligne_absystech extends classes_optima {
	/**
	* Constructeur livraison ligne
	* @author MOUAD EL HIZABRI
	*/
	public function __construct() {
		parent::__construct();
		$this->table = "livraison_ligne"; 
		$this->colonnes['fields_column'] = array(
			'livraison_ligne.id_livraison'
			,'livraison_ligne.id_stock'
			,'livraison_ligne.etat'=>array("width"=>40,"renderer"=>"etat")
		);	
        $this->fieldstructure();		
	}
	
    /**
	* @author MOUAD EL HIZABRI
	* Vérifie si les lignes de livraison sont en etat en_cours_de_livraison
	* @param int identifiant de la livraison
	* return int nombre de lignes en etat en_cours_de_livraison
	*/
	public function delivery_status_verification($id_livraison){
		$this->q
		     ->reset()
			 ->where('id_livraison',$id_livraison)
			 ->where('etat','en_cours_de_livraison');
			 
		$les_lignes=$this->select_all();
		return count($les_lignes);
	}
	
};
class livraison_ligne_att extends livraison_ligne_absystech { };
class livraison_ligne_wapp6 extends livraison_ligne_absystech { };
class livraison_ligne_demo extends livraison_ligne_absystech { };
?>