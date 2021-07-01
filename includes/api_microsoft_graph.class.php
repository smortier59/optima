<?
/**
* Classe credit Safe Connect - API Credit Safe Connect 1.3
* @package Optima
*/
class api_microsoft_graph extends classes_optima {

    /**
     * Recupere les constantes necessaires à l'API MICROSOFT
     * Si elles n'existe pas, on retourne une erreur ATF
     * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
     *
     * @return array username, password, baseurl
     * @throws ErrorATF si une constante est manquante
     */
    public function checkAndGetConstante(){

        $client_id = ATF::constante()->getConstante("__MS_GRAPH_CLIENT_ID__");
        $client_secret = ATF::constante()->getConstante("__MS_GRAPH_CLIENT_SECRET__");
        $tenant_id = ATF::constante()->getConstante("__MS_GRAPH_TENANT_ID__");

        if (!ATF::constante()->select($client_id, "valeur")) throw new errorATF("Constante CLIENT ID MICROSOFT GRAPH manquante");
        if (!ATF::constante()->select($client_secret, "valeur")) throw new errorATF("Constante CLIENT_SECRET MICROSOFT GRAPH manquante");
        if (!ATF::constante()->select($tenant_id, "valeur")) throw new errorATF("Constante TENANT ID MICROSOFT GRAPH manquante");

        return array(
            "client_id" => ATF::constante()->select($client_id, "valeur"),
            "client_secret" => ATF::constante()->select($client_secret, "valeur"),
            "tenant_id" => ATF::constante()->select($tenant_id, "valeur")
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
    public function auth() {

        $constante = $this->checkAndGetConstante();

        /*$tenantID = "92435d98-99f6-449b-b313-528fba7ad851";
        $clientID = "d1f6e0a0-a23c-4611-91b7-a9eff8385414";
        $clientSecret = "8SFGwR4K.k7qfB~l.T.5ItUBj44Uic02~5";*/


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://login.microsoftonline.com/".$constante["tenant_id"]."/oauth2/v2.0/token");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, '');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_POSTFIELDS, "client_id=".$constante["client_id"]."&scope=https%3A%2F%2Fgraph.microsoft.com%2F.default&client_secret=".$constante["client_secret"]."&grant_type=client_credentials");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/x-www-form-urlencoded') );



        $response = curl_exec($ch);
        $response = json_decode($response);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_status === 200) {
            return array(
                "token_type" => $response->token_type,
                "access_token" => $response->access_token
            );
        }else{
            log::logger("ERROR Authenticate" , "api_microsoft");
            if ($response->details) {
                throw new errorATF("MICROSOFT ERROR Authenticate : ".$response->message." ".$response->details);
            } else {
                throw new errorATF("MICROSOFT ERROR Authenticate : ".$response->message);
            }
        }
    }

    public function _getEventWeek($get) {

        if(!$get["email"]) throw new errorATF("Email non présent dans la session");

        try {
            $auth = $this->auth();

            $user = $this->getUserByEmail($auth, $get["email"]);
            log::logger($user , "mfleurquin");
            log::logger($user->id , "mfleurquin");

            $events = $this->getUserCalendar($auth, $user->id);


            // $events = json_decode(file_get_contents(__ABSOLUTE_PATH__."www/events.json"), true);

            $arrayDate = $res = array();

            $tz_from = new DateTimeZone('UTC');
            $tz_to = new DateTimeZone('Europe/Paris');

            foreach ($events->value as $k => $event) {
                if (!$event->isCancelled) {
                    $orig_deb = new DateTime($event->start->dateTime, $tz_from);
                    $orig_fin = new DateTime($event->end->dateTime, $tz_from);

                    $debut = $orig_deb->setTimezone($tz_to);
                    $fin = $orig_fin->setTimezone($tz_to);

                    $arrayDate[] = $debut->format('Y-m-d');

                    $participants = array();

                    if ($event->sensitivity !== "private") {
                        foreach ($event->attendees as $key => $value) {
                            $exp = explode(" ", $value->emailAddress->name);
                            $initiales = "";
                            foreach ($exp as $k => $v) {
                                $initiales .= ucfirst(substr($v, 0, 1));
                            }

                            $participants[] = array(
                                "nom" => $value->emailAddress->name,
                                "initiales" => $initiales,
                                "createur" => ($event->organizer->emailAddress->name === $value->emailAddress->name) ? true : false,
                            );
                        }
                    }



                    $res[] = array(
                        "titre" => ($event->sensitivity !== "private") ? $event->subject : "Rendez-vous privé",
                        "debut_jour" => $debut->format('Y-m-d'),
                        "fin_jour" => $fin->format('Y-m-d'),
                        "debut_heure" => $debut->format('H:i'),
                        "fin_heure" => $fin->format('H:i'),
                        "allDay" => $event->isAllDay,
                        "adresse" => ($event->sensitivity !== "private") ? $event->location->displayName : '',
                        "importance" => $event->importance,
                        "sensitivity" => $event->sensitivity,
                        "participants" => $participants
                    );
                }
            }

            $arrayDate = array_unique($arrayDate);
            sort($arrayDate);

            array_multisort(
                array_column($res, 'debut_jour'),  SORT_ASC,
                array_column($res, 'debut_heure'),  SORT_ASC,
                $res
            );

            return array("events"=>$res, "days"=>$arrayDate);
        } catch (errorATF $e) {
            throw new errorATF($e->getMessage(), 500);
        }
    }

    /**
     * Recupère les informations d'un utilisateur à partir de son email
     *
     * @param [type] $token
     * @param [type] $email
     * @return Object
     */
    static function getUserByEmail($token, $email) {
        $url = "https://graph.microsoft.com/v1.0/users/" . $email;
        try {
            return self::curl_get($token, $url);
        } catch (errorATF $e) {
            throw $e;
        }
    }

    /**
     * Recupère les events pour les 7 prochains jours du calendrier d'un user à partir de son ID
     *
     * @param [type] $token
     * @param [type] $email
     * @return Object
     */
    static function getUserCalendar($token, $id_user) {
        $url = "https://graph.microsoft.com/v1.0/users/" . $id_user. "/calendar/calendarview?startdatetime=".date("Y-m-d")."&enddatetime=".date("Y-m-d", strtotime("+7 days"));
        try {
            return self::curl_get($token, $url);
        } catch (errorATF $e) {
            throw $e;
        }
    }

    /**
     * Execute une requete CURL GET
     *
     * @param [type] $token
     * @param [type] $url
     * @return Object
     */
    static function curl_get($token, $url) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, '');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Authorization: Bearer ' . $token["access_token"]) );


        $response = curl_exec($ch);
        $response = json_decode($response);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_status === 200) {
            return $response;
        }else{
            log::logger("ERROR getUserByEmail" , "api_microsoft");
            if ($response->error) {
                throw new errorATF("MICROSOFT ERROR getUserByEmail : ".$response->error->code." : ".$response->error->message);
            } else {
                throw new errorATF("MICROSOFT ERROR getUserByEmail : ".$response->error->message);
            }
        }
    }

}