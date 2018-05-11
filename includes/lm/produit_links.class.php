<?	
/** Classe produit_links
* @package Optima
* @subpackage Cleodis
*/
class produit_links extends classes_optima {
	function __construct() {
		parent::__construct();
		$this->controlled_by = 'produit';
		$this->table = __CLASS__;
		$this->colonnes['fields_column'] = array( 
			'produit_links.id_produit'
			,'produit_links.id_produit_cible'
			,'produit_links.etat'
		);

		$this->colonnes['primary'] = array(
			"id_produit"
			,'id_produit_cible'
			,'etat'
		);
		
		$this->colonnes['bloquees']['insert'] =  array('id_produit');
		$this->colonnes['ligne'] =  array( 	
			'produit_links.id_produit'
			,'produit_links.id_produit_cible'
			,'produit_links.etat'
		);
		
		$this->field_nom = "%id_produit% %id_produit_cible% %etat%";
		$this->foreign_key['id_produit_cible'] =  "produit";

		$this->fieldstructure();	
		$this->selectAllExtjs=true; 
	}
}