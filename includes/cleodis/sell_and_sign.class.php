
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
			"contractorTo_id" => $post["dataSellAndSign"]["contractorTo_id"]
		));
		return true;

	}

	public function getJToken() {
		ATF::constante()->q->reset()->where("constante","__J_TOKEN_SELL_AND_SIGN__");
		$constante = ATF::constante()->select_row();
		return $constante["valeur"];
	}

	public function getContract($contract_id) {
		$url = "https://cloud.sellandsign.com/calinda/hub/selling/model/contract/read?action=getContract&contract_id=" . $contract_id;

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array(
			  'j_token: ' . $this->getJToken()
			),
		  ));

		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        $response = json_decode($response);
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($http_status === 200) {
           return $response;
        }else{
			$error = curl_error($curl);
			throw new errorATF($error);
		}
	}


	public function getSignedContract($contract_id, $id_commande) {
		$url = "https://cloud.sellandsign.com/calinda/hub/selling/do?m=getSignedContract&contract_id=" . $contract_id;
		$j_token = "";


		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_HTTPHEADER => array(
				'j_token: ' . $this->getJToken()
			),
		));
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

		$response = curl_exec($curl);
		$error = curl_error($curl);

		curl_close($curl);

		if ( $error != "" ) {
            return false;
        }

		return $response;

	}

};

class sell_and_sign_cleodisbe extends sell_and_sign { };
class sell_and_sign_citrenting extends sell_and_sign { };
class sell_and_sign_cap extends sell_and_sign { };
class sell_and_sign_solo extends sell_and_sign { };
class sell_and_sign_arrow extends sell_and_sign { };

class sell_and_sign_bdomplus extends sell_and_sign { };
class sell_and_sign_bdom extends sell_and_sign { };
class sell_and_sign_boulanger extends sell_and_sign { };