<?
/** Classe courrier_information_pack
* @package Optima
* @subpackage Cléodis
*/
class courrier_information_pack extends classes_optima {
	function __construct($table_or_id=NULL) {
		$this->table ="courrier_information_pack";
		parent::__construct($table_or_id);

		$this->colonnes['fields_column'] = array(
			 'courrier_information_pack.courrier_information_pack'
			,'courrier_information_pack.template_mail_courrier'
			,'fichier_joint'=>array("custom"=>true,"nosort"=>true,"type"=>"file","align"=>"center","renderer"=>"uploadFile")
		);
		$this->files["fichier_joint"] = array("type"=>"pdf","no_generate"=>true);

		$this->fieldstructure();
	}
};
?>