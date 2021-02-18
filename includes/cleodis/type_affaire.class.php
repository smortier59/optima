<?
/** Classe affaire_etat
* @package Optima
* @subpackage Cléodis
*/

class type_affaire extends classes_optima {
	public function __construct() {
		parent::__construct();

    //table type_affaire et ajout de logo
		$this->table = "type_affaire";
		$this->colonnes["fields_column"] = array(
            "type_affaire"
            ,"libelle_pdf"
            ,'logo'=> array("custom"=>true,"nosort"=>true,"type"=>"file","align"=>"center","width"=>70,"renderer"=>"uploadFile")
    );

    $this->colonnes['primary'] = array(
      "type_affaire",
      "libelle_pdf"
    );

    $this->fieldstructure();

    $this->files["logo"] = array("type"=>"jpg","no_upload"=>false,"no_generate"=>true);

  }

}