<?
class licence_type extends classes_optima {

	public function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		$this->colonnes["fields_column"] = array(
			'licence_type.licence_type'
		);
		$this->fieldstructure();
	}



};
class licence_type_midas extends licence_type { };
class licence_type_cleodisbe extends licence_type { };
class licence_type_cap extends licence_type { };
class licence_type_bdomplus extends licence_type { };
class licence_type_bdom extends licence_type { };
class licence_type_boulanger extends licence_type { };