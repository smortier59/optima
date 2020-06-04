
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

		$this->colonnes['fields_column'] = array(
			"sell_and_sign.sell_and_sign"
			,"sell_and_sign.date"
			,"sell_and_sign.id_affaire"
			,"sell_and_sign.dataCustomer_number"
			,"sell_and_sign.contractor_id"
			,"sell_and_sign.contract_id"
			,"sell_and_sign.document_id"
			,"sell_and_sign.contractorTo_id"
			,"sell_and_sign.bundle_id"

		);



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
		$this->files["certificat_preuve"] = array("type"=>"zip","preview"=>false,"no_upload"=>false,"no_generate"=>true);
	}


	public function _dataSellAndSign($get, $post){

		$this->insert(array(
			"sell_and_sign" => $post["dataSellAndSign"]["sell_and_sign"],
			"id_affaire" => ATF::affaire()->decryptId($post["id"]),
			"dataCustomer_number" => $post["dataSellAndSign"]["dataCustomer_number"],
			"contractor_id" =>  $post["dataSellAndSign"]["contractor_id"],
			"contract_id" => $post["dataSellAndSign"]["contract_id"],
			"document_id" => $post["dataSellAndSign"]["document_id"],
			"contractorTo_id" => $post["dataSellAndSign"]["contractorTo_id"],
			"bundle_id" => 1
		));



		return true;

	}
};

class sell_and_sign_cleodisbe extends sell_and_sign { };
class sell_and_sign_cap extends sell_and_sign { };

class sell_and_sign_bdomplus extends sell_and_sign { };
class sell_and_sign_bdom extends sell_and_sign { };
class sell_and_sign_boulanger extends sell_and_sign { };