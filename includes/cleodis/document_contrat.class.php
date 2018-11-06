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

		$this->files["fichier_joint"] = array("type"=>"pdf","no_generate"=>true);

		$this->fieldstructure();
	}


	/**
	* On ne doit avoir que les document de contrat actifs
	* @author Morgan Fleurquin <mfleurquin@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @return une nouvelle fenêtre
	*/
	/*public function select_all($order_by="document_contrat.document_contrat",$asc='asc',$page=false,$count=false){
		$this->q->where("document_contrat.etat",'actif');
		return parent::select_all($order_by,$asc,$page,$count);
	}*/

};
?>