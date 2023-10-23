<?
/** Classe api_lixxbail
* @package Optima
* @subpackage Cleodis
*/
class api_lixxbail extends classes_optima {
    public $log_file = "api_lixxbail";
    public $id_refinanceur_lixxbail = 12;

    /**
	* Constructeur
	*/
	function __construct() {
		parent::__construct();
		$this->table = "creditsafe";
	}

    function guidv4($data = null) {
        // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
        $data = $data ?? random_bytes(16);
        assert(strlen($data) == 16);

        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        // Output the 36 character UUID.
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * Recupere les constantes necessaires à l'API LIXXBAIL
     * Si elles n'existe pas, on retourne une erreur ATF
     * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
     *
     * @return array basic64, baseurl
     * @throws ErrorATF si une constante est manquante
     */
    public function checkAndGetConstante(){
        $basic64 = ATF::constante()->getConstante("__API_LIXXBAIL_BASICBASE64__");
        $baseurl = ATF::constante()->getConstante("__API_LIXXBAIL_BASEURL__");

        if (!ATF::constante()->select($basic64, "valeur")) throw new errorATF("Constante basic64 Lixxbail manquante");
        if (!ATF::constante()->select($baseurl, "valeur")) throw new errorATF("Constante baseurl Lixxbail manquante");

        return array(
            "basic64" => ATF::constante()->select($basic64, "valeur"),
            "baseurl" => ATF::constante()->select($baseurl, "valeur")
        );
    }

    /**
     * Genere et execute un appel CURL
     *
     * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
     *
     * @param  string $url
     * @param  string $token
     * @param  string $method (GET / POST)
     * @param  array $params
     * @return array response
     */
    public function curlCall($url, $token, $method='GET', $params = null, $headers = null){

        log::logger("-- URL : ".$url , $this->log_file);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        if ($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge($headers, ['Content-Type: application/json', 'Authorization: Bearer '.$token]));
        } else {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json', 'Authorization: Bearer '.$token) );
        }


        if($method === 'GET'){
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        }

        if($method === 'POST'){
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }



        $response = curl_exec($ch);
        $response = json_decode($response);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        log::logger("RETOUR -->" , $this->log_file);
        log::logger($response , $this->log_file);

        if ($http_status == 200 || $http_status == 201) {
            $response = json_decode($response);
            return $response;
        } else {
            if (gettype($response->error_description) === "string") throw new errorATF("error:".str_replace(":","",$response->error_description)."|code:".$response->error,$http_status);
            if (gettype($response->error_description) === "object") {
                $string_error = "";
                foreach($response->error_description as $k => $v) {
                    $string_error .= $v[0]." ".$k."\n";
                }
                throw new errorATF("error:".$string_error."|code:".$response->error,$http_status);
            }



        }
    }


    /**
     * Authentification à l'API Credit Safe
     * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
     *
     * @return token
     * @throws ErrorATF si probleme de recuperation de token
     *         ErrorATF si probleme de constante
     */
    public function authenticate(){

        log::logger("-- Récuperation des constantes", $this->log_file);
        $constantes = $this->checkAndGetConstante();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $constantes["baseurl"].'token');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded', 'Authorization: Basic '.$constantes['basic64'] ));
        $response = curl_exec($ch);
        $response = json_decode($response);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_status === 200) {
            return array(
                "access_token" => $response->access_token,
                "baseurl" => $constantes["baseurl"]
            );
        }else{
            log::logger("ERROR Authenticate" , $this->log_file);
            log::logger($response , $this->log_file);
            if ($response->details) {
                throw new errorATF("API LIXXBAIL ERROR Authenticate : ".$response->message." ".$response->details);
            } else {
                throw new errorATF("API LIXXBAIL ERROR Authenticate : ".$response->message);
            }

        }

    }

    public function _newConsumerFundingRequest($infos) {
        log::logger("NEW CONSUMER FUNDING REQUEST", "mfleurquin");
        $data = json_decode(base64_decode($infos["data"]), true);
        try {
            ATF::affaire()->q->reset()->where("affaire.ref", $data["leasing_information"]["consumer_funding_reference_id"]);
            $a = ATF::affaire()->select_row();
            $affaire = ATF::affaire()->select($a["affaire.id_affaire"]);

            if ($affaire) {
                $this->createLixxBailComite($affaire, $data);

                log::logger("-- Récuperation du token" , $this->log_file);
                $dataAuth = $this->authenticate();

                $access_token = $dataAuth["access_token"];
                $baseurl = $dataAuth["baseurl"];
                $url = $baseurl.'mylixxnet_leasing_fundings/v1/consumer_funding_requests';

                $postData = [
                    "partner" => [
                        "id" => [
                            "registration_number" => "45307981600055",
                            "registration_scheme" => "SIRET"
                        ],
                        "email_address" => "jerome.loison@cleodis.com"
                    ],
                    "customer" => [
                        "id" => [
                            "registration_number" => $data["customer"]["siret"],
                            "registration_scheme" => "SIRET"
                        ]
                    ],
                    "main_asset" => [
                        "categorization" => [
                            "type_code" => $data["main_asset"]["categorization"]["type_code"],
                            "reference_code" => $data["main_asset"]["categorization"]["reference_code"],
                            "brand_code" => $data["main_asset"]["categorization"]["brand_code"]
                        ],
                        "condition_code" => $data["main_asset"]["condition_code"],
                        "destination_country" => [
                            "code" => $data["main_asset"]["destination_country"]["code"],
                            "label" => $data["main_asset"]["destination_country"]["label"]
                        ],
                        "quantity" => $data["main_asset"]["quantity"],
                        "currency_code" => "EUR",
                        "destination_town_name" => $data["main_asset"]["destination_town_name"],
                        "has_breakdown_insurance" => ($data["main_asset"]["has_breakdown_insurance"] == 'non') ? false : true,
                        "financial_loss_insurance_type" => null,
                        "provider" => [
                            "registration_number" => $data["main_asset"]["provider"]["registration_number"],
                            "registration_scheme" => "SIRET"
                        ]
                    ],
                    "leasing_information" => [
                        "consumer_funding_reference_id" => $data["leasing_information"]["consumer_funding_reference_id"],
                        "consumer_request_date_time" => $data["leasing_information"]["consumer_request_date_time"],
                        "financial_data" => [
                            "rate_scale_code" => $data["leasing_information"]["financial_data"]["rate_scale_code"],
                            "payment_term_code" => $data["leasing_information"]["financial_data"]["payment_term_code"],
                            "payment_type_code" => $data["leasing_information"]["financial_data"]["payment_type_code"],
                            "payment_period" => intval($data["leasing_information"]["financial_data"]["payment_period"], 10),
                            "reimbursement_periodicity_code" => $data["leasing_information"]["financial_data"]["reimbursement_periodicity_code"],
                            "number_of_reimbursement_periodicities" => intval($data["leasing_information"]["financial_data"]["number_of_reimbursement_periodicities"],10),
                            "currency_code" => "EUR",
                            "rent_amount" => $data["leasing_information"]["financial_data"]["rent_amount"],
                        ]
                    ],
                    "final_update_flag" => false
                ];
                if ($data["main_asset"]["condition_code"] === 'OCCA') $postData["main_asset"]["date_of_first_use"] = $data["main_asset"]["date_of_first_use"];

                log::logger("-- Envoi de la demande ".$url , $this->log_file);
                log::logger("Data --> " , $this->log_file);
                log::logger($postData , $this->log_file);
                $uuid = $this->guidv4();
                log::logger("correlationId --> " . $uuid, $this->log_file);
                $headers = [ 'Correlationid: '.$uuid ];


                try {
                    $res = $this->curlCall($url, $access_token, 'POST', json_encode($postData), $headers);
                    ATF::affaire()->u(["id_affaire"=> $a["affaire.id_affaire"], "demande_lixxbail" => 'oui']);
                    if (isset($res["acknowledgment_message"])) return array("success"=>true ,"result"=>$res["acknowledgment_message"]);
                    return array("success"=>true ,"result"=>"ok");

                } catch (errorATF $e) {
                    throw $e;
                }
            } else {
                throw new errorATF("AFFAIRE_NOT_FOUND", 404);
            }
        } catch (errorATF $e) {
            throw $e;
        }
    }

    function createLixxBailComite($affaire) {

        $societe = ATF::societe()->select($affaire["id_societe"]);
        //On crée le comité
        $comite = array  (
            "id_societe" => $societe["id_societe"],
            "id_affaire" =>  $affaire["id_affaire"],
            "id_contact" => ATF::societe()->select($societe["id_societe"], "id_contact_signataire"),
            "etat"=> "en_attente",
            "decisionComite"=>'',
            "activite" => $societe["activite"],
            "reponse" => null,
            "validite_accord" => null,
            "id_refinanceur" => $this->id_refinanceur_lixxbail,
            "loyer_actualise" => ATF::affaire()->getCompteTLoyerActualise($affaire),
            "date_creation" => $societe["date_creation"],
            "date_compte" => $societe["lastaccountdate"],
            "capitaux_propres" => $societe["capitaux_propres"],
            "note" => $societe["cs_score"],
            "dettes_financieres" => $societe["dettes_financieres"],
            "limite" => $societe["cs_avis_credit"],
            "ca" => $societe["ca"],
            "capital_social" => $societe["capital_social"],
            "resultat_exploitation" => $societe["resultat_exploitation"],
            "date" => date("d-m-Y"),
            "description" => "Comité LixxBail",
            "suivi_notifie"=>array(0=>"")
        );
        return ATF::comite()->insert(array("comite"=>$comite));

    }
}