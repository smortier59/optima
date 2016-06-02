<?php
/** Classe privilege : Gestion des droits sur ATF5
* @package ATF
*/
class module_privilege extends classes_optima {
	
	/*---------------------------*/
	/*      Constructeurs        */
	/*---------------------------*/	
	public function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		$this->colonnes['fields_column'] = array('module_privilege.id_module','module_privilege.id_privilege');
		$this->fieldstructure();
	}
	
};
?>