<?
/** Classe espace_client_conseiller
* Permet de faire des appels aux bacls Ecc, afin d'avoir des informations sur des comptes
* @package Optima
* @subpackage Cléodis
*/
class espace_client_conseiller {

    /**
     * Retourne l'url du back client
     * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
     * @return url
     */
    public function getUrlBack() {
        ATF::constante()->q->reset()->where("constante", "__URL_ESPACE_CLIENT_BACK__");
        $url_back_espace_client = ATF::constante()->select_row();
        if (!$url_back_espace_client) {
            throw new errorATF("Il n'y a pas de constante __URL_ESPACE_CLIENT_BACK__ pour l'url de l'espace client");
        }
        return $url_back_espace_client["valeur"];
    }

    /**
     * Retourne l'url du front client
     * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
     * @return url
     */
    public function getUrlFront() {
        ATF::constante()->q->reset()->where("constante", "__URL_ESPACE_CLIENT__");
        $url_front_espace_client = ATF::constante()->select_row();
        if(!$url_front_espace_client) {
            throw new errorATF("Il n'y a pas de constante __URL_ESPACE_CLIENT__ pour l'url de l'espace client");
        }
        return $url_front_espace_client["valeur"];
    }



    /**
     * Recupere l'id d'application par rapport à un nom de domaine
     * @author Morgan Fleurquin <mfleurquin@absystech.fr>
     * @param url du back à appeler $url_back_espace_client
     * @param url du front pour recuperer un id application $url_front_espace_client
     * @return object Info de l'application
     */
    public function getApplicationByDomain($url_back_espace_client, $url_front_espace_client) {
        $curl = curl_init();
        $host = str_replace("http://", "", $url_back_espace_client);
        $host = str_replace("https://", "", $host);

        echo "Récupération de l'application ID, appel CURL : ".$url_back_espace_client . '/application/domain?domain=' . $url_front_espace_client."\n";

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url_back_espace_client.'/application/domain?domain=' . $url_front_espace_client,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'host: '. $host
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    /**
     * Interroge le back, afin de savoir si un compte existe pour
     * @author Morgan Fleurquin <mfleurquin@absystech.fr>
     * @param  string $url_back_espace_client
     * @param string $applicationId
     * @param array $clients
     *          [
     *              id_societe, --> ID du client
     *              nom,        --> Nom du client
     *              prenom,     --> Prenom du client
     *              email,      --> Email du client (email à tester)
     *              ref,        --> Ref affaire
     *              affaire     --> ID affaire
     *          ]
     * @return array clients avec en plus un champs existe qui permet de saboir si un compte existe ou non
     */
    public function checkAccountsExiste($url_back_espace_client, $applicationId, $clients) {
        $curl = curl_init();

        echo "Check si le client à un compte : " . $url_back_espace_client . "/account/existAccountForOptima";

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url_back_espace_client.'/account/existAccountForOptima',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_VERBOSE => false,
            CURLOPT_POSTFIELDS => array('applicationId' => $applicationId, 'clients' => json_encode($clients)),
        ));

        $response = curl_exec($curl);
        if($response === false) {
            $error = curl_error($curl);
            log::logger("Error ---> ", "espace_client_conseiller_error");
            log::logger($error, "espace_client_conseiller_error");
        }


        curl_close($curl);
        return $response;


    }
}