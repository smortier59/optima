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
            ,"etat"
    );

    $this->colonnes['primary'] = array(
      "type_affaire",
      "libelle_pdf",
      "etat"
    );

    $this->fieldstructure();

    $this->files["logo"] = array("type"=>"jpg","no_upload"=>false,"no_generate"=>true);

  }

  /**
	* Surcharge de la méthode autocomplete pour faire apparaître que les document de contrat actifs
	* @author Morgan Fleurquin <mfleurquin@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @return une nouvelle fenêtre
	*/
	function autocomplete($infos) {
		// Récupérer les produits
		$this->q->reset()->where("type_affaire.etat",'actif')
				->addOrder("type_affaire.type_affaire", 'ASC');
		return parent::autocomplete($infos,false);
	}

}