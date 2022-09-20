<?
/**
* Classe credit Safe Connect - API Credit Safe Connect 1.3
* @package Optima
*/
class creditsafe extends classes_optima {
	/**
	* Constructeur
	*/
	function __construct() {
		parent::__construct();
		$this->table = "creditsafe";
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
    public function curlCall($url, $token, $method='GET', $params = null){

        log::logger("-- URL : ".$url , "creditSafe");

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json', 'Authorization: Bearer '.$token) );

        if($method === 'GET'){
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        }
        $response = curl_exec($ch);
        $response = json_decode($response);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_status === 200) {
           return $response;
        }else{
            log::logger($response , "creditSafe");
            if ($response->details) {
                throw new errorATF("CREDIT SAFE ERROR : ".$response->message." ".$response->details);
            } else {
                throw new errorATF("CREDIT SAFE ERROR : ".$response->message);
            }
        }
    }


    /**
     * Recupere les constantes necessaires à l'API CREDIT SAFE
     * Si elles n'existe pas, on retourne une erreur ATF
     * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
     *
     * @return array username, password, baseurl
     * @throws ErrorATF si une constante est manquante
     */
    public function checkAndGetConstante(){
        $username = ATF::constante()->getConstante("__API_CREDIT_SAFE_USERNAME__");
        $password = ATF::constante()->getConstante("__API_CREDIT_SAFE_PASSWORD__");
        $baseurl = ATF::constante()->getConstante("__API_CREDIT_SAFE_BASEURL__");

        if (!ATF::constante()->select($username, "valeur")) throw new errorATF("Constante username CREDIT SAFE manquante");
        if (!ATF::constante()->select($password, "valeur")) throw new errorATF("Constante password CREDIT SAFE manquante");
        if (!ATF::constante()->select($baseurl, "valeur")) throw new errorATF("Constante baseurl CREDIT SAFE manquante");

        return array(
            "username" => ATF::constante()->select($username, "valeur"),
            "password" => ATF::constante()->select($password, "valeur"),
            "baseurl" => ATF::constante()->select($baseurl, "valeur")
        );
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

        log::logger("-- Récuperation des constantes", "creditSafe");
        $constantes = $this->checkAndGetConstante();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $constantes["baseurl"].'/authenticate');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{"username": "'.$constantes["username"].'", "password": "'.$constantes["password"].'" }');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json' ) );
        $response = curl_exec($ch);
        $response = json_decode($response);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_status === 200) {
            return array(
                "token" => $response->token,
                "baseurl" => $constantes["baseurl"]
            );
        }else{
            log::logger("ERROR Authenticate" , "creditSafe");
            if ($response->details) {
                throw new errorATF("CREDIT SAFE ERROR Authenticate : ".$response->message." ".$response->details);
            } else {
                throw new errorATF("CREDIT SAFE ERROR Authenticate : ".$response->message);
            }

        }

    }

    /**
     * Authentification à l'API Credit Safe
     * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>

     * @param  String $siret
     * @return Array
     * @throws ErrorATF si probleme de recuperation de token
     *         ErrorATF si probleme de constante
     */
    public function getInfosCompanyBySiret($siret){

        try {
            log::logger("-- Récuperation du token" , "creditSafe");
            $dataAuth = $this->authenticate();

            $token = $dataAuth["token"];
            $baseurl = $dataAuth["baseurl"];

            $pays = ATF::constante()->getConstante("__API_CREDIT_PAYS_RECHERCHE__");
            if (!$pays) {  $pays = 'FR'; }
            else {  $pays = ATF::constante()->select($pays, "valeur"); }


            $url = $baseurl.'/companies?countries='.$pays.'&regNo='.$siret;
            log::logger("-- Recherche de la societe ".$url , "creditSafe");
            $res = $this->curlCall($url, $token);
            log::logger($res , "creditSafe");

            if ($res->totalSize >= 1){
                $idCreditSafe = $res->companies[0]->id;

                $url = $baseurl.'/companies/'.$idCreditSafe.'?language=fr';
                log::logger("-- Récuperation du report de la societe ".$url , "creditSafe");
                $societeData = $this->curlCall($url, $token);
                log::logger($societeData , "creditSafe");
                return $this->cleanDataForOptima($societeData);
            }else{
                log::logger("Error 2" , "creditSafe");
                log::logger("Aucune société trouvée pour ".$siret." dans les pays suivants ".$pays , "creditSafe");
                throw new errorATF("Aucune société trouvée pour ".$siret." dans les pays suivants ".$pays);
            }
        } catch (errorATF $e) {
            log::logger("Error 1" , "creditSafe");
            throw $e;
        }

    }

    /**
     * Transforme l'objet recuperé de CS pour le transformer et formatter avec les data necessaires pour OPTIMA
     * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
     *
     * @param  Object $data
     * @return Array $return
     */
    private function cleanDataForOptima($data) {
        $return = array();

        $directors = $data->report->directors->currentDirectors;
        foreach ($directors as $key => $value) {
            $director = $value->name;
            if ($value->gender){
                if ($value->gender == "Male") $director = str_replace('M ', '', $director);
                if ($value->gender == "Female") $director = str_replace('Mme ', '', $director);
            }

            $explodeNom = explode(" ", $director);
            $nom = $explodeNom[0];
            $prenom = str_replace($nom.' ', '', $director);

			$return['gerant'][] = array("nom"=>$nom,
								        "prenom"=>$prenom,
								        "fonction"=>$value->positions[0]->positionName);

		}

        $companySummary = $data->report->companySummary;
        $companyIdentification = $data->report->companyIdentification;
        $basicInfo = $data->report->companyIdentification->basicInformation;
        $creditScore = $data->report->creditScore;


        $return['societe'] = $basicInfo->registeredCompanyName;
        $return['nom_commercial'] = $basicInfo->businessName;

		$return['siret'] = $companySummary->companyRegistrationNumber;
		$return['siren'] = substr($return['siret'],0,9);
        $return['reference_tva'] = $companyIdentification->basicInformation->vatRegistrationNumber;
        $return['naf'] = $companyIdentification->basicInformation->principalActivity->code;
		$return['activite'] = $companyIdentification->basicInformation->principalActivity->description;
		$return['structure'] = $companyIdentification->basicInformation->legalForm->description;
        $return['ville_rcs'] = $companyIdentification->basicInformation->commercialCourt;


        $dateparsee = explode('T',$basicInfo->companyRegistrationDate);
		$return['date_creation'] = date("Y-m-d",strtotime($dateparsee[0]));

		// $return['tel'] = str_replace("/","",(string)$company->BasicInformation->ContactTelephoneNumber);
		$return['adresse'] = $basicInfo->contactAddress->street;
        $return['tel'] = str_replace(" ","",$data->report->contactInformation->mainAddress->telephone);
        $return['fax'] = str_replace(" ", "", $data->report->additionalInformation->misc->faxNumber);
		$return['cp'] = $basicInfo->contactAddress->postalCode;
		$return['ville'] = $basicInfo->contactAddress->city;
        $return['id_pays'] = $basicInfo->contactAddress->country;


		$return['capital'] =  $data->report->shareCapitalStructure->nominalShareCapital->value;

        $return['cs_score'] = intval($creditScore->currentCreditRating->providerValue->value);

		$return['cs_avis_credit'] = $creditScore->currentCreditRating->creditLimit->value;

        $latestRatingChangeDate = explode("-",explode('T',$creditScore->latestRatingChangeDate)[0]);
        $return['lastScoreDate'] = $latestRatingChangeDate[2]."/".$latestRatingChangeDate[1]."/".$latestRatingChangeDate[0];
        if ($return['lastScoreDate'] === "//") $return['lastScoreDate'] = null;

        $lastaccountdate = explode("-",explode('T',$data->report->localFinancialStatements[0]->yearEndDate)[0]);
        $return['lastaccountdate'] = $lastaccountdate[2]."/".$lastaccountdate[1]."/".$lastaccountdate[0];
        if ($return['lastaccountdate'] === "//") $return['lastaccountdate'] = null;


        $localfinancialStatement = $data->report->localFinancialStatements[0];

		$return['receivables'] = number_format($localfinancialStatement->assets->receivables, 0, ",", " ");
		$return['securitieandcash'] = number_format($localfinancialStatement->assets->securitiesAndCash , 0, ",", " ");		// Produits d'exploitation

		$return["capital_social"] = number_format($data->report->shareCapitalStructure->nominalShareCapital->value , 0, ",", " ");
		$return["capitaux_propres"] = number_format($data->report->financialStatement[0]->balanceSheet->totalShareholdersEquity , 0, ",", " ");
		$return["dettes_financieres"] = number_format($data->report->localFinancialStatements[0]->liabilities->financialLiabilities , 0, ",", " ");

        $return['netturnover'] =  number_format($data->report->localFinancialStatements[0]->profitAndLoss->netTurnover , 0, ",", " ");
		$return['operatingincome'] =  number_format($data->report->localFinancialStatements[0]->profitAndLoss->salesOfGoods , 0, ",", " ");
        $return['operationgprofitless'] = number_format($data->report->localFinancialStatements[0]->profitAndLoss->operatingProfit , 0, ",", " ");
		$return['financialincome'] = number_format($data->report->localFinancialStatements[0]->profitAndLoss->financialIncome , 0, ",", " ");
		$return['financialcharges'] = number_format($data->report->localFinancialStatements[0]->profitAndLoss->financialCharges , 0, ",", " ");

        $return['ca'] = number_format($data->report->localFinancialStatements[0]->profitAndLoss->netTurnover , 0, ",", "");
        $return['resultat_exploitation'] = $return['operationgprofitless'];


		// ETAT
		switch ($companySummary->companyStatus->status) {
			case '':
				$return['etat'] = "supprime";
			break;
			case 'Inactive':
            case 'NonActive':
				$return['etat'] = "inactif";
			break;
			case 'Active':
				$return['etat'] = "actif";
			break;
		}
        return $return;

    }

    public function _getSolde() {
        try {
            $res = $this->getSolde(false);

            $return = [];
            foreach($res as $key => $value) {
                $return[] = [
                    "title" => $value["serie"],
                    "restant" => $value["restant"],
                    "utilise" => $value["utilise"],
                    "date" => $value["date"] ,
                    "heure" => $value["heure"]
                ];
            }


            return $return;
        } catch (errorATF $e) {
            throw $e;
        }

    }

    /**
     * Recupere les infos de soldes de Credit Safe
     * Stocke les data dans un fichier JSON
     *
     * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
     * @return void
     */
    public function getSolde($call_curl = true) {
        try {
            $folder_stat = dirname(__FILE__)."/../creditSafe/";
            $fileData = "soldeCS-".ATF::$codename.".json";
            if ( $call_curl ) {
                log::logger("-- Récuperation du token" , "creditSafe");
                $dataAuth = $this->authenticate();

                $token = $dataAuth["token"];
                $baseurl = $dataAuth["baseurl"];

                $pays = ATF::constante()->getConstante("__API_CREDIT_PAYS_RECHERCHE__");
                if (!$pays) {
                    $pays = 'FR';
                } else {
                    $pays = ATF::constante()->select($pays, "valeur");
                }


                $url = $baseurl.'/access';
                log::logger("-- Recherche des infos de solde ".$url , "creditSafe");
                $res = $this->curlCall($url, $token);


                if (!file_exists($folder_stat)) {
                    mkdir($folder_stat, 0755, true);
                }
                $seuil = 200;

                $constante_seuil = ATF::constante()->getConstante("__SEUIL_ALERTE_CREDIT_SAFE__");
                if($constante_seuil){
                    $seuil = ATF::constante()->select($constante_seuil, "valeur");
                    log::logger("Constante __SEUIL_ALERTE_CREDIT_SAFE__ trouvée, on prend ce seuiil : ".$seuil , "creditSafe");
                }else{
                    log::logger("Il n'y a pas de constante __SEUIL_ALERTE_CREDIT_SAFE__ trouvée, on prend le seuil par défaut : ".$seuil , "creditSafe");
                }

                if( ($res->countryAccess->creditsafeConnectOnlineReports[0]->paid - $res->countryAccess->creditsafeConnectOnlineReports[0]->used) < $seuil){
                    $send_email = true;

                    // On lit le fichier déja créé pour voir si on a déja envoyé un mail depuis 48h
                    if(file_exists($folder_stat.$fileData)){
                        $infos = json_decode(file_get_contents($folder_stat.$fileData));

                        log::logger("Dernier envoi du mail d'alerte : " . $infos->dernier_envoi_mail_alerte, "creditSafe");

                        if ($infos->dernier_envoi_mail_alerte
                            && date("YmdHi", strtotime($infos->dernier_envoi_mail_alerte)) > date("YmdHi", strtotime("-2 days"))
                        ){
                            log::logger("Envoi d'un mail il y a moins de 2 jours, on ne renvoi pas le mail d'alerte" , "creditSafe");
                            $send_email = false;
                        }
                    }

                    // Si pas de champs ou date envoi du precedent mail > 48h on envoi le mail d'avertissement
                    if ($send_email){
                        $data["dernier_envoi_mail_alerte"] = date("d-m-Y H:i");



                        $mail = new mail(
                            array(
                                "recipient"=>"jerome.loison@cleodis.com",
								"objet"=>"Solde Ticket Credit Safe critique",
								"template"=>"empty",
                                "texte" => "Votre solde de crédit ticket Credit Safe a atteint un seuil critique ".
                                            (
                                                $res->countryAccess->creditsafeConnectOnlineReports[0]->paid
                                                - $res->countryAccess->creditsafeConnectOnlineReports[0]->used
                                            )
                                            ." crédits restant.",
                                "html"=>true,
								"from"=>"noreply@cleodis.com"
                            )
                        );
		                $mail->send();
                        log::logger("Seuil crédit restant atteint, et envoi du mail", "creditSafe");
                    }


                }

                $data["date_interogation"] = date("d-m-Y H:i");
                $data["data"] = $res->countryAccess->creditsafeConnectOnlineReports;




                file_put_contents($folder_stat.$fileData, json_encode($data, JSON_PRETTY_PRINT));
            } else {
                $data = json_decode(file_get_contents($folder_stat.$fileData));

                foreach ($data->data as $key => $dataCountry) {
                    $return[$key]["title"] = "Solde Crédit Safe ".$dataCountry->countryName." <br /> au ".date("d/m/Y à H:i", strtotime($data->date_interogation));
                    $return[$key]["serie"] = $dataCountry->countryName;
                    $return[$key]["restant"] = $dataCountry->paid - $dataCountry->used;
                    $return[$key]["utilise"] = $dataCountry->used;
                    $return[$key]["date"] = date("Y-m-d", strtotime($data->date_interogation));
                    $return[$key]["heure"] = date("H:i", strtotime($data->date_interogation));
                }

                return $return;
            }

        } catch (errorATF $e) {
            log::logger("Error 1" , "creditSafe");
            throw $e;
        }
    }

}
