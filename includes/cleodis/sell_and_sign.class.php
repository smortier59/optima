
<?
/**
* Sell and Sign
* @package Optima
*/
class sell_and_sign extends classes_optima {
	/**
	* Constructeur
	*/
	public function __construct() {
		parent::__construct();
		$this->controlled_by = 'affaire';
		$this->table = "sell_and_sign";
		$this->colonnes['primary'] = array(
			"sell_and_sign"
			,"date"
			,"id_affaire"
			,"dataCustomer_number"
			,"contractor_id"
			,"contract_id"
			,"document_id"
			,"contractorTo_id"
			,"bundle_id"
		);
		$this->fieldstructure();
		$this->files["dossier_preuve"] = array("type"=>"zip","preview"=>false,"no_upload"=>false,"no_generate"=>true);
	}	
};

class sell_and_sign_cleodisbe extends sell_and_sign { };
class sell_and_sign_cap extends sell_and_sign { };

class sell_and_sign_bdomplus extends sell_and_sign { };
class sell_and_sign_bdom extends sell_and_sign { };
class sell_and_sign_boulanger extends sell_and_sign { };