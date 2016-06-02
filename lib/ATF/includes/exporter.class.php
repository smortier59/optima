<?php
/** Classe exporter
* @package ATF
*/
class exporter extends classes_optima {
	/*---------------------------*/
	/*      Attributs            */
	/*---------------------------*/
	
	/*---------------------------*/
	/*      Constructeurs        */
	/*---------------------------*/	
	public function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		$this->colonnes["fields_column"] = array(	'exporter.exporter'
													,'exporter.date'
													,'exporter.id_user'
													,'exporter.id_module'
													,'exporter.filtre'
													,'exporter.filtre_fields'
												);
		$this->fieldstructure();
	}
	
	/*---------------------------*/
	/*      Mthodes             */
	/*---------------------------*/	
};
?>