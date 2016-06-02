<?
/**
* @package Optima
*/
class produit extends classes_optima {
	
	function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		
		$this->colonnes['primary']=array('ref','produit');
		
		$this->autocomplete = array(
			"field"=>array("produit","ref")
			,"show"=>array("produit","ref")
			,"popup"=>array("produit","ref")
		);

		
		$this->fieldstructure();
		$this->addPrivilege("toCommandeLigne","insert");
	}
	
	function select($id,$field=NULL){
		if($id["lang"] && is_array($id)){
			$devis_ligne = ATF::devis_ligne()->select($id["lang"]);
			//On récupère toutes les infos sauf la quantité
			unset($devis_ligne["quantite"]);
			return $devis_ligne;
		}else{
			return parent::select($id,$field);
		}
	}

};
?>