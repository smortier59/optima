<?	
/** Classe produit_link
* @package Optima
* @subpackage Cleodis
*/
class produit_link extends classes_optima {
	function __construct() {
		parent::__construct();
		$this->controlled_by = 'produit';
		$this->table = __CLASS__;
		$this->colonnes['fields_column'] = array( 
			,'produit_link.id_produit'
			,'produit_link.id_produit_cible'
			,'produit_link.etat'
		);

		$this->colonnes['primary'] = array(
			,"id_produit"
			,'id_produit_cible'
			,'etat'
		);
		
		$this->colonnes['bloquees']['insert'] =  array('id_produit');
		$this->colonnes['ligne'] =  array( 	
			'produit_link.id_produit'
			,'produit_link.id_produit_cible'
			,'produit_link.etat'
		);
		
		$this->field_nom = "%id_produit% %id_produit_cible% %etat%";
		$this->foreign_key['id_produit_cible'] =  "produit";

		$this->fieldstructure();	
		$this->selectAllExtjs=true; 
	}
}