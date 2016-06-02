<?	
/** Classe bon_de_pret_ligne
* @package Optima
* @subpackage Absystech
*/
class bon_de_pret_ligne extends classes_optima {
	function __construct() {
		parent::__construct(); 
		$this->table = __CLASS__;
		$this->colonnes['fields_column'] = array( 
			 'bon_de_pret_ligne.ref'
			,'bon_de_pret_ligne.id_stock'
			,'bon_de_pret_ligne.ref'=>array("width"=>100,"align"=>"center")
			,'bon_de_pret_ligne.serial'
			,'bon_de_pret_ligne.serialAT'
		);

		$this->fieldstructure();
	}
};
?>