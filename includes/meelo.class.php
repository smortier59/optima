<?
/** Classe Meelo
* @package Optima
*/
class meelo extends classes_optima {
    public $logFile = "meelo";

    function __construct() {
		parent::__construct();
		$this->table = "meelo";
    }

    /**
     * Genere et execute un appel CURL
     * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
     *
     * @param  string $url
     * @param  string $token
     * @param  string $method (GET / POST)
     * @param  array $params
     * @return array response
     */
    public function curlCall($url, $token, $method='GET', $params = null){

        log::logger("-- URL : ".$url , $this->logFile);
        log::logger("-- TOKEN : ".$token , $this->logFile);
        log::logger("-- METHOD : ".$method , $this->logFile);
        log::logger($params , $this->logFile);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json', 'Authorization: Bearer '.$token) );

        if($method === 'GET') curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        if ($method === 'POST' && $params) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));

        $response = curl_exec($ch);
        $response = json_decode($response);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_status === 200) {
           return $response;
        }else{
            log::logger($response , $this->logFile);
            if ($response->details) {
                throw new errorATF("MEELO ERROR : ".$response->message." ".$response->details);
            } else {
                throw new errorATF("MEELO ERROR : ".$response->message);
            }
        }
    }

    /**
     * Recupere les constantes necessaires à l'API MEELO
     * Si elles n'existe pas, on retourne une erreur ATF
     * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
     *
     * @return array username, password, baseurl
     * @throws ErrorATF si une constante est manquante
     */
    public function checkAndGetConstante(){
        $token = ATF::constante()->getConstante("__API_MEELO_TOKEN__");
        $checkCompanyUrl = ATF::constante()->getConstante("__API_MEELO_CHECK_COMPANY_BASEURL__");
        $scoringUrl = ATF::constante()->getConstante("__API_MEELO_SCORING_BASEURL__");

        if (!ATF::constante()->select($token, "valeur")) throw new errorATF("Constante API_MEELO_TOKEN manquante");
        if (!ATF::constante()->select($checkCompanyUrl, "valeur")) throw new errorATF("Constante API_MEELO_CHECK_COMPANY_URL manquante");
        if (!ATF::constante()->select($scoringUrl, "valeur")) throw new errorATF("Constante API_MEELO_SCORING_BASEURL manquante");

        return array(
            "token" => ATF::constante()->select($token, "valeur"),
            "checkCompanyUrl" => ATF::constante()->select($checkCompanyUrl, "valeur"),
            "scoringUrl" => ATF::constante()->select($scoringUrl, "valeur")
        );
    }

    function companyDetails($checkCompanyUrl, $token, $registrationNumber, $pays) {
        $data = [
            "registrationNumber" => $registrationNumber,
            "coverage" => "FULL",
            "depth" => 1,
            "country" => $pays
        ];
        if ($pays !== "FR") unset($data["coverage"]);

        $url = $checkCompanyUrl."/v3/company-details";
        log::logger("-- Création de la demande company-details" , $this->logFile);
        return $this->curlCall($url, $token, 'POST', $data);
    }

    function companyDetailTask($checkCompanyUrl, $token, $id, $try=0) {
        $url = $checkCompanyUrl.'/v3/task/'.$id;
        log::logger("-- Récuperation du report de la societe ".$url , $this->logFile);

        $report = $this->curlCall($url, $token);
        log::logger($report, $this->logFile);
        switch ($report->status) {
            case 'FAILED':
                throw new errorATF("MEELO ERROR : ".$report->statusReason);
            break;

            case 'IN_PROGRESS':
                if ($try < 5) {
                    log::logger("On retry", $this->logFile);
                    sleep(2);
                    return self::companyDetailTask($checkCompanyUrl, $token, $id, $try++);
                } else {
                    return null;
                }
            break;

            case 'SUCCESS':
                return $report->result;
            break;
            default:
                log::logger("STAUT NON GERE ". $report->status, "meelo");
            break;
        }
    }

    /**
     * Authentification à l'API MEELO
     * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>

     * @param  String $siret
     * @return Array
     * @throws ErrorATF si probleme de recuperation de token
     *         ErrorATF si probleme de constante
     */
    public function getInfosCompanyByRegistrationNumber($registrationNumber, $pays=null) {
        try {
            $constantes = $this->checkAndGetConstante();
            $token = $constantes["token"];
            $checkCompanyUrl = $constantes["checkCompanyUrl"];

            if (!$pays) {
                $pays = ATF::constante()->getConstante("__API_CREDIT_PAYS_RECHERCHE__");
                if (!$pays) {
                    $pays = 'FR';
                } else {
                    $pays = ATF::constante()->select($pays, "valeur");
                }
            }
            $res = $this->companyDetails($checkCompanyUrl, $token, $registrationNumber, $pays);
            log::logger($res , $this->logFile);

            if ($res && $res->id) {
               return self::companyDetailTask($checkCompanyUrl, $token, $res->id);
            } else {
                throw new errorATF("MEELO ERROR : Demande failed");
            }
        } catch (errorATF $e) {
            log::logger("Error 1" , $this->logFile);
            throw $e;
        }
    }


    public function getScoring($registrationNumber, $pays=null) {
        log::logger("-- GET SCORING" , $this->logFile);
        try {
            $constantes = $this->checkAndGetConstante();
            $token = $constantes["token"];
            $scoringUrl = $constantes["scoringUrl"];

            if (!$pays) {
                $pays = ATF::constante()->getConstante("__API_CREDIT_PAYS_RECHERCHE__");
                if (!$pays) {
                    $pays = 'FR';
                } else {
                    $pays = ATF::constante()->select($pays, "valeur");
                }
            }

            // Generation du journey ID
            $journeyID = $this->getJourneyId($scoringUrl, $token);

            // Creation de la demande
            $res = $this->scoreTask($scoringUrl, $token, $journeyID, $registrationNumber, $pays);

            // Recuperation des infos scoring
            if ($res && $res->id) {
               return self::scoreResultTask($scoringUrl, $token, $res->id);
            } else {
                throw new errorATF("MEELO ERROR : Demande failed");
            }
        } catch (errorATF $e) {
            log::logger("Error 1" , $this->logFile);
            throw $e;
        }
    }


    function getJourneyId($url, $token) {
        log::logger("-- GET JOURNEY ID" , $this->logFile);
        $url = $url."/journey-id";
        log::logger("-- Création Journey ID" , $this->logFile);
        return $this->curlCall($url, $token, 'GET');
    }

    function scoreTask($url, $token, $journeyID, $registrationNumber, $pays) {
        log::logger("-- POST SCORE TASK" , $this->logFile);
        $data = [
            "requestOptions" => [ "requestId" => "CREDIT_SAFE_SCORE"],
            "companyProfile" => [
                "address" => [
                  "country" => $pays
                ],
                "registrationNumber" => $registrationNumber
            ],
            "additionalParameters" => [],
            "rulesOptions" => []
        ];

        $url = $url."/v2/task/score?journeyId=".$journeyID;
        log::logger("-- Création de la demande score" , $this->logFile);
        return $this->curlCall($url, $token, 'POST', $data);
    }

    function scoreResultTask($scoringURL, $token, $id, $try=0) {
        log::logger("-- POST SCORE RESULT" , $this->logFile);
        $url = $scoringURL.'/v2/task/'.$id;
        log::logger("-- Récuperation du report de score de la societe ".$url , $this->logFile);

        $report = $this->curlCall($url, $token);
        log::logger($report, $this->logFile);
        switch ($report->status) {
            case 'FAILED':
                throw new errorATF("MEELO ERROR : ".$report->statusReason);
            break;

            case 'IN_PROGRESS':
                if ($try < 5) {
                    log::logger("On retry", $this->logFile);
                    sleep(2);
                    return self::scoreResultTask($scoringURL, $token, $id, $try++);
                } else {
                    return null;
                }
            break;

            case 'SUCCESS':
                return $report->result;
            break;
            default:
                log::logger("STAUT NON GERE ". $report->status, "meelo");
            break;
        }
    }
};

?>