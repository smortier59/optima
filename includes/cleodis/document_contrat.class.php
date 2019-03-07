<?
/** Classe document_contrat
* @package Optima
* @subpackage Cléodis
*/
class document_contrat extends classes_optima {
	function __construct($table_or_id=NULL) {
		$this->table ="document_contrat";
		parent::__construct($table_or_id);

		$this->colonnes['fields_column'] = array(
			 'document_contrat.document_contrat'
			,'document_contrat.type_signature'
			,'document_contrat.etat'
			,'fichier_joint'=>array("custom"=>true,"nosort"=>true,"type"=>"file","align"=>"center","renderer"=>"uploadFile")
		);

		$this->files["fichier_joint"] = array("type"=>"pdf","no_generate"=>true,"obligatoire"=>true);

		$this->fieldstructure();
	}


	/**
	* Surcharge de la méthode autocomplete pour faire apparaître que les document de contrat actifs
	* @author Morgan Fleurquin <mfleurquin@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @return une nouvelle fenêtre
	*/
	function autocomplete($infos) {
		// Récupérer les produits
		$this->q->reset()->where("document_contrat.etat",'actif')
				->addOrder("document_contrat.document_contrat", 'ASC');
		return parent::autocomplete($infos,false);
	}

};
?>