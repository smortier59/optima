<?php
/* Classe de gestion API
* @author Yann GAUTHERON <ygautheron@absystech.fr>
*/
class api {
    const VERSION = 7;
    const EXPIRATION_TIME = 600; // secondes
    private static $token; // Infos du token identifié

    /**
    * Authentification
    * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
    * @param array $data
    */
    public static function access_token($post) {
        if ($post["grant_type"]=="client_credentials") {
            // On vérifie le login/pass API, retourne un access_token lié a ce compte
            if ($access_token = self::login($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW'])) {
                // On génère un access_token
                $data = array(
                    "access_token" => $access_token,
                    "expires_in" => self::EXPIRATION_TIME,
                    "token_type" => 'Bearer'
                );
                return $data;
            }
        }
    }

    /**
    * Login via token oAuth
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    * @param string $client_id
    * @param string $client_secret
    * @return string $token_access
    */
    public static function login($client_id,$client_secret) {
        if ($client_id && $client_id==__API_CLIENT_ID__ && $client_secret && $client_secret==__API_CLIENT_SECRET__) {
            do {
            	// Anti-doublons éventuels (hyper rarissime mais bon... on sait jamais)
	            $access_token = sha1(__ABSOLUTE_PATH__.microtime().mt_rand(0,100000));
            } while (file::cached($access_token));
            if (file::cached($access_token,"access_token",self::EXPIRATION_TIME)) {
                return $access_token;
            }
        } else {
            throw new Exception("Credentials not valid", 500);
        }
    }

    /**
    * Vérifie l'access_token
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    * @param string $access_token
    * @return string $id_user
    */
    public static function checkAccessToken($post) {
        $headers = apache_request_headers();
        if ($token = str_replace("Bearer ","",$headers["Authorization"])) {
            if ($token = file::cached($token)) {
                return $token;
            }
        }

        return self::checkAndGetTokenInfos();
    }

    /**
    * Envoi une réponse HTTP avec un code de retour et un résultat en JSON
    * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
    * @param array $data
    */
    private static function send($data,$code=200) {
        if      ($code==200) header("HTTP/1.0 200");// No Response");
        elseif  ($code>=400) {
            // Si c'est un 500, alors on redirige vers la meme URL avec un # pour passer par l'affichage d'une erreur en HTML
            log::logger($data,"error");
            header("HTTP/1.0 ".$code);
            header("X-Error-Code: ".$code);
            header("X-Error-Reason: ".$data);
            $data = false;
        }
        header("Content-type: application/json; charset=utf-8");
        if ($code !== 200) {
            throw new errorATF($data, $code);
        } else {
            echo json_encode($data);
        }

    }

    /**
    * Retourne les infos du token identifié sinon déclenche une exception
    * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
    * @return array
    */
    public static function checkAndGetTokenInfos() {
        if (!self::$token) {
            throw new Exception("A valid access token is required for this job.", 500);
        }
        return self::$token;
    }

    /**
    * Retourne les infos du token identifié
    * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
    * @return mixed
    */
    public static function getToken() {
        return self::$token;
    }

    /**
    * Traite les requêtes RESTful
    * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
    * @param string $http_method GET, POST, PUT, DELETE...
    * @param string $path
    * @param array $get
    * @param array $post
    * @return boolean
    */
    public static function process($http_method,$path,$get,$post,$files) {
        $path = explode("/",$path);
        array_shift($path); // host/
        $api = array_shift($path); // api/
        $version = array_shift($path); // v3/
        if($post["schema"]) {
            ATF::define('codename',$post["schema"]);
            ATF::select_db($_POST["schema"]);
            ATF::applyCodename();

        }
        if ($api=="api") {
        	if ($version=="v".self::VERSION) {
	            $module = array_shift($path);

	            $x = array_shift($path);
	            if (is_numeric($x) || strlen($x) === 32) {
	                $get["id"] = $x;
	            } elseif ($x) {
	                $function = $x;
	            }

	            if (!$function) {
	                $function = $http_method;
	            }
	            $function = "_".$function;
	            try {
	                switch ($module) {
	                    case "access_token":
	                        if ($data = self::access_token($post)) {
	                            self::send($data);
	                        } else {
	                            self::send('Access token denied',500);
	                        }
	                        break;

	                    default:
	                        self::$token = self::checkAccessToken();
                            $class = ATF::getClass($module);
	                        if ($class) {
	                            if (method_exists($class,$function)) {
                                    $data = $class->$function($get,$post,$files);
                                    self::send($data);
	                            } else {
	                                self::send("Process not found ".$module."/".$function." (".ATF::$codename.")",500);
	                            }
	                        } else {
	                            self::send("Scope or module not found ".$module." (".ATF::$codename.")",500);
	                        }
	                }
	            } catch (Exception $e) {
	                // Retour normalement pris en charge par la méthode qui spécifie l'erreur elle-même
	                self::send($e->getMessage(),$e->getCode());
	            }
	        } else {
	            self::send("This API version disabled",500);
	        }

	        return true; // On traite une API
	    }

        return false;
    }

    /**
    * Envoie en UDP un paquet au serveur NodeJS Telecope
    * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
    * @param mixed $data
    */
    public static function sendUDP($data) {
    	$msg = json_encode($data);
		$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
		socket_sendto($sock, $msg, strlen($msg), MSG_EOF, __API_TELESCOPE_HOST__, __API_TELESCOPE_PORT__);
    }
}
