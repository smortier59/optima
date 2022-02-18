<?php


class loyer_kilometrage extends classes_optima{

    public function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		$this->controlled_by = 'affaire';
		$this->colonnes["fields_column"] = array(
			 'loyer_kilometrage.loyer'
            ,"loyer_kilometrage.kilometrage"
			,"loyer_kilometrage.id_affaire"

		);
		$this->fieldstructure();
	}

}