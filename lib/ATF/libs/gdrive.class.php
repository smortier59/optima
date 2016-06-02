<?php
define('__GPLUS_REFRESH_TOKEN__','1/ueZFsnNitXvQc_soVfT9T3d-bmu47-Aafvhf6XylkhoMEudVrK5jSpoR30zcRFq6');
define('__GPLUS_CLIENTID__','85491629806-gnsur86e2m0us51o2bjetuhqlc6imici.apps.googleusercontent.com');
define('__GPLUS_CLIENTSECRET__','4PHj7LIcLdUs2jyRlmvg-IcU');
define('__GPLUS_REDIRECTURI__','urn:ietf:wg:oauth:2.0:oob');
class gdrive {
	var $client;
	var $service;
	var $accessToken;
	var $last_ping;
	var $optimaObj;

	public static $fakeFilepath = '/home/www/TF87121620_20140331FAC003404_F4714028_OC.pdf';

	function __construct($authCode,$table) {
		$this->optimaObj = ATF::getClass($table);
		if (!method_exists($this->optimaObj,'getNextToOCR')) {
			die("Objet Optime incorrect, il faut une méthode getNextToOCR");
		}


		$authObj = $this->checkRefreshToken();


		try {

			require_once dirname(__FILE__).'/google-api-php-client/Google_Client.php';
			require_once dirname(__FILE__).'/google-api-php-client/contrib/Google_DriveService.php';


			// Sinon il faut récupérer un refresh_token !
			//$apiConfig['oauth2_approval_prompt'] = 'auto';

			$this->client = new Google_Client();

			// Get your credentials from the console
			// Login : optima@reseller.absystech.fr
			// Pass : az...
			$this->client->setClientId(__GPLUS_CLIENTID__);
			$this->client->setClientSecret(__GPLUS_CLIENTSECRET__);
			$this->client->setRedirectUri(__GPLUS_REDIRECTURI__);
			$this->client->setScopes(array('https://www.googleapis.com/auth/drive'));

			$this->service = new Google_DriveService($this->client);


			if ($authObj->access_token) {
				// Refresh token accepté !
				$this->accessToken = $authObj;
				log::logger("Refresh token accepted !!",'ocr');
				//print_r($this->accessToken);

				//$this->client->setAccessToken($json_response);
				$this->client->refreshToken(__GPLUS_REFRESH_TOKEN__);

			} else {
				// Exchange authorization code for access token
				$this->accessToken = $this->client->authenticate($authCode);
				log::logger("Refresh token returned :",'ocr');
				print_r($this->accessToken);
				log::logger("Configurer la lib avec ce refresh token !",'ocr');
				die;
			}

		} catch (Google_AuthException $e) {
			log::logger($e->getMessage(),'ocr');
			log::logger("Auth impossible, utiliser (avec le compte optima@reseller.absystech.fr / az...) :",'ocr');
			log::logger($this->client->createAuthUrl(),'ocr');
			die;
		}
	}

	/* Vérifie en cURL si le refreshçtoken retourne bien un access_token
	 * @author YG
	 */
	function checkRefreshToken() {
		// On teste avec un refresh_token sensé ne jamais expirer !
		$client_id = __GPLUS_CLIENTID__;
		$client_secret = __GPLUS_CLIENTSECRET__;
		$redirect_uri = __GPLUS_REDIRECTURI__;

		$oauth2token_url = "https://accounts.google.com/o/oauth2/token";
		$clienttoken_post = array(
			"refresh_token" => __GPLUS_REFRESH_TOKEN__,
			"client_id" => __GPLUS_CLIENTID__,
			"client_secret" => __GPLUS_CLIENTSECRET__,
			"redirect_uri" => __GPLUS_REDIRECTURI__,
			"grant_type" => "refresh_token"
		);

		$curl = curl_init($oauth2token_url);

		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $clienttoken_post);
		//curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		$json_response = curl_exec($curl);
		curl_close($curl);

		return json_decode($json_response);
	}

	/* Vérifie s'il y a des fichiers à convertir et les converti
	 * @author YG
	 * @param $delay Délai d'attente en secondes avant de ping pour ne pas perdre la session GDrive
	 */
	function watcher($delay=120) {
		$this->last_ping = time();
		$counter = 0;
		while (($counter++) < 300) {
			try {
				// Récupérer les fichiers à convertir
				if ($files = $this->optimaObj->getNextToOCR()) {
					foreach ($files as $id) {
						$filepath = $this->optimaObj->filepath($id,'fichier_joint');
						log::logger($filepath,'ocr');

						$txt = $this->ocr($filepath);
						if (!$txt) $txt = "error";
	//log::logger($txt,'ocr');

						$this->optimaObj->update(array(
							"id_".$this->optimaObj->table=>$id,
							"ocr"=>$txt
						));

					}

				}

				if (method_exists($this->optimaObj,'getNextAfterOCR')) {
					$this->optimaObj->getNextAfterOCR();
				}

				// Si la session arrive a expiration, on envoie un fichier factice
				/*if (time() - $this->last_ping > $delay) {
	//log::logger("anti-idle ".self::$fakeFilepath,'ocr');

					// Méthode refresh_token
					$sessionToken = json_decode($this->accessToken);
					log::logger("refresh_token is : ".$sessionToken->refresh_token,'ocr');
					$this->client->refreshToken($sessionToken->refresh_token);
					$this->last_ping = time();

					// Méthode OCR factice
					//$this->ocr(self::$fakeFilepath);
				}*/

			} catch (Exception $e) {
				log::logger($e->getMessage(),'ocr');
			}

			sleep(1);
		}
	}

	/* Converti un fichier PDF en TXT via l'OCR de Google Drive
	 * @author YG
	 * @param string $filepath Chemin vers le fichier PDF à convertir
	 * @return string Texte reconnu
	 */
	function ocr($filepath) {
		// Créer le fichier
		$file = new Google_DriveFile();
		$file->setTitle('File '.md5(time()));
		$file->setDescription('Document '.md5(time()));
		$file->setMimeType('application/pdf');

		$data = file_get_contents($filepath);

		// OCR sur Drive
		$createdFile = $this->service->files->insert($file, array(
			'data' => $data,
			'mimeType' => 'application/pdf',
			'ocr' => true
		));
		log::logger("File created, fetching TXT...",'ocr');

		// Récupérer le fichier TXT
		$cmd = 'curl -H "Authorization: Bearer '.$this->accessToken->access_token.'" "'.$createdFile["exportLinks"]["text/plain"].'" --silent';
		$txt = `$cmd`;
		log::logger("TXT found :\n\n".$txt."\n\n",'ocr');

		// Suppression sur le Drive
		$this->service->files->delete($createdFile["id"]);
		log::logger("File deleted on Google Drive.",'ocr');

		$this->last_ping = time();

		return $txt;
	}
}

$table = $_SERVER["argv"][2];
$authCode = $_SERVER["argv"][3];
$gdrive = new gdrive($authCode,$table);
$gdrive->watcher();