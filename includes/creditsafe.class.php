<?
/**
* Classe credit Safe Connect - API Credit Safe Connect 1.3
* @package Optima
*/
class creditsafe {

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


        $return['societe'] = $companySummary->businessName;
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
		$return['cp'] = $basicInfo->contactAddress->postalCode;
		$return['ville'] = $basicInfo->contactAddress->city;
        $return['id_pays'] = $basicInfo->contactAddress->country;




		$return['capital'] =  $data->report->shareCapitalStructure->nominalShareCapital->value;

		$return['cs_score'] = $creditScore->currentCreditRating->providerValue->value;
		$return['cs_avis_credit'] = $creditScore->currentCreditRating->creditLimit->value;

        $latestRatingChangeDate = explode("-",explode('T',$creditScore->latestRatingChangeDate)[0]);
        $return['lastScoreDate'] = $latestRatingChangeDate[2]."/".$latestRatingChangeDate[1]."/".$latestRatingChangeDate[0];
        if ($return['lastScoreDate'] === "//") $return['lastScoreDate'] = null;

        $lastaccountdate = explode("-",explode('T',$data->report->localFinancialStatements[0]->yearEndDate)[0]);
        $return['lastaccountdate'] = $lastaccountdate[2]."/".$lastaccountdate[1]."/".$lastaccountdate[0];
        if ($return['lastaccountdate'] === "//") $return['lastaccountdate'] = null;


        $financialStatement = $data->report->financialStatements[0];

		$return['receivables'] = number_format($financialStatement->balanceSheet->totalReceivables, 0, ",", " ");
		$return['securitieandcash'] = number_format($financialStatement->balanceSheet->cash , 0, ",", " ");		// Produits d'exploitation

		$return["capital_social"] = number_format($data->report->shareCapitalStructure->nominalShareCapital->value , 0, ",", " ");
		$return["capitaux_propres"] = number_format($financialStatement->balanceSheet->totalShareholdersEquity , 0, ",", " ");
		$return["dettes_financieres"] = number_format($data->report->localFinancialStatements[0]->liabilities->financialLiabilities , 0, ",", " ");

        $return['netturnover'] =  number_format($data->report->localFinancialStatements[0]->profitAndLoss->netTurnover , 0, ",", " ");
		$return['operatingincome'] =  number_format($data->report->localFinancialStatements[0]->profitAndLoss->salesOfGoods , 0, ",", " ");
        $return['operationgprofitless'] = number_format($data->report->localFinancialStatements[0]->profitAndLoss->operatingProfit , 0, ",", " ");
		$return['financialincome'] = number_format($data->report->localFinancialStatements[0]->profitAndLoss->financialIncome , 0, ",", " ");
		$return['financialcharges'] = number_format($data->report->localFinancialStatements[0]->profitAndLoss->financialCharges , 0, ",", " ");

        $return['ca'] = $return['netturnover'];
        $return['resultat_exploitation'] = $return['operationgprofitless'];


		// ETAT
		switch ($companySummary->companyStatus->CompanyStatus->status) {
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

}