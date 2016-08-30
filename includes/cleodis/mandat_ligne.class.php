<?	
/** Classe mandat_ligne
* @package Optima
* @subpackage Cleodis
*/
class mandat_ligne extends classes_optima {
	function __construct() {
		parent::__construct(); 
		$this->controlled_by = "mandat";
		$this->colonnes['fields_column'] = array( 
			 'mandat_ligne.id_mandat'
			,'mandat_ligne.texte'
			,'mandat_ligne.valeur'
			,'mandat_ligne.type'
			,'mandat_ligne.ligne_titre'
			,'mandat_ligne.mandat_type'
		);
				
		$this->colonnes['bloquees']['insert'] = array('id_mandat_ligne','id_mandat');
		
		$this->fieldstructure();
		
		$this->foreign_key['id_mandat'] =  "mandat";
		$this->no_insert=true;
		$this->no_update=true;
		$this->no_delete=true;
	}
};
?>