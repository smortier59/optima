<?	
/**
* @package Optima
*/
class copieur_facture_ligne extends classes_optima {	
	function __construct() {
		parent::__construct(); 
		$this->table = __CLASS__;
		$this->controlled_by = "facture";
		$this->colonnes['fields_column'] = array(
			'copieur_facture_ligne.type'=>array("width"=>100,"align"=>"center")
			,'copieur_facture_ligne.designation'
			,'copieur_facture_ligne.quantite'=>array("width"=>50,"align"=>"center")
			,'copieur_facture_ligne.prix_achatNB'=>array("width"=>100,"align"=>"right")
			,'copieur_facture_ligne.prix_achatC'=>array("width"=>100,"align"=>"right")
			,'copieur_facture_ligne.prixNB'=>array("width"=>100,"align"=>"right")
			,'copieur_facture_ligne.prixC'=>array("width"=>100,"align"=>"right")
		);
		
		$this->fieldstructure();
	}


};
?>