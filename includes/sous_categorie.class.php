<?
/**
* @package Optima
*/
class sous_categorie extends classes_optima{

	function __construct(){ //PHP5
		parent::__construct();
		$this->table=__CLASS__;
		$this->colonnes['fields_column']=array('sous_categorie.sous_categorie');
		$this->colonnes['primary']=array('id_categorie','sous_categorie');
		
		$this->fieldstructure();
		
	}

};
?>