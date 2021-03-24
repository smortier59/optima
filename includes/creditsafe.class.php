<?
/**
* Classe credit Safe Connect - API Credit Safe Connect 1.3
* @package Optima
*/
class creditsafe {

    public function curlCall($url, $token, $method='GET', $params = null){

        log::logger("-- URL : ".$url , "creditSafe");
        log::logger("-- token : ".$token , "creditSafe");

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
            log::logger($response , "creditSafe");
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
            log::logger($dataAuth , "creditSafe");

            $token = $dataAuth["token"];
            $baseurl = $dataAuth["baseurl"];

            $pays = ATF::constante()->getConstante("__API_CREDIT_PAYS_RECHERCHE__");
            if (!$pays) $pays = 'FR';

            $url = $baseurl.'/companies?countries=FR&regNo=33525999000034';
            log::logger("-- Recherche de la societe ".$url , "creditSafe");
            $res = $this->curlCall($url, $token);
            log::logger($res , "creditSafe");


            if ($res->totalSize >= 1){
                $idCreditSafe = $res->companies[0]->id;

                $url = $baseurl.'/companies/'.$idCreditSafe;
                log::logger("-- Récuperation du report de la societe ".$url , "creditSafe");
                $societeData = $this->curlCall($url, $token);
                log::logger($societeData , "creditSafe");
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

}