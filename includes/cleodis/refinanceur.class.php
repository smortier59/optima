<?
/** Classe refinanceur
* @package Optima
* @subpackage Cléodis
*/
class refinanceur extends classes_optima {
	
	private $ids = array(
		"KBC"=>array(2,5)
		,"BNP"=>array(3,8)
	);
	
	function __construct() {
		parent::__construct();
		$this->table = "refinanceur"; 

		$this->colonnes['fields_column'] = array(
			'refinanceur','adresse','ville','capital','email'
		);
		
		$this->fieldstructure();
	}
	
	/**
    * Est ce que le refinanceur est KBC ?
	* @param int $id
	* @return bool
    */   	
	public function isKBC($id) {
		if (in_array($id,$this->ids['KBC'])) {
			return true;	
		} else {
			return false;	
		}
	}
	
	/**
    * Est ce que le refinanceur est BNP ?
	* @param int $id
	* @return bool
    */   	
	public function isBNP($id) {
		if (in_array($id,$this->ids['BNP'])) {
			return true;	
		} else {
			return false;	
		}
	}
};
