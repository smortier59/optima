<?php
//declare(ticks = 1);
////pcntl_signal(SIGTERM, "signal_handler");
//pcntl_signal(SIGINT, "signal_handler");
////pcntl_signal(SIGKILL, "signal_handler");
//
//function signal_handler($signal) {
//	echo $signal;	
//	switch($signal) {
//		case SIGTERM:
//			print "Caught SIGTERM\n";
//			exit;
//		case SIGKILL:
//			print "Caught SIGKILL\n";
//			exit;
//		case SIGINT:
//			print "Caught SIGINT\n";
//			exit;
//	}
//}
//
class socketer {
	private static $host = "localhost";
	private static $port = 16969;
	private static $max_clients = 100;
	
	/* Les instructions sont délimitées par ceci */
	private static $delimiter = "\t"; // Ex: \t pour Flash
	
	/* Tableau d'association inverse (mac vers key_socket) */
	private $mac2key = array();
	private $observers = array(); // Observateur web
	
	function __construct($bind_address=NULL,$port=NULL,$max_clients=NULL) {
		if ($bind_address) self::$bind_address = $bind_address;
		if ($port) self::$port = $port;
		if ($max_clients) self::$max_clients = $max_clients;
		
		// Set the ip and port we will listen on
		$this->log("AbsysTech Server-side Socket Gateway");
		$this->log("Initializing...");
		
		// Array that will hold client information
		$this->clients = array();
		
		// Create a TCP Stream socket
		$this->sock = socket_create(AF_INET, SOCK_STREAM, 0);
		
		// Bind the socket to an address/port
		$this->log("Binding to address ".self::$host.":".self::$port."...");
		while (!socket_bind($this->sock, self::$host, self::$port));
		$this->log("Socket opened on ".self::$host.":".self::$port);
		$this->log("Waiting for clients...");
		
		// Start listening for connections
		socket_listen($this->sock);
		
		// Loop continuously
		while (true) {
			// Setup clients listen socket for reading
			$this->read[0] = $this->sock;
			for ($i = 0; $i < self::$max_clients+1; $i++) {
				if (isset($this->client[$i]) && $this->client[$i]['sock']  != NULL) {
					$this->read[$i + 1] = $this->client[$i]['sock'];
				}
			}
			$write = NULL;
			$except = NULL;
			$tv_sec = NULL;
			
			// Set up a blocking call to socket_select()
			$this->ready = socket_select($this->read,$write,$except,$tv_sec);
			
			/* if a new connection is being made add it to the client array */
			$this->create_client();
			
			/* Reconnection à la base MySQL au cas où la connection a sautée */
			$this->ping();
		
			/* Executer le processus "step" pour chaque donnée reçue */
			$this->step();
		}
	}
	
	function __destruct() {
		foreach ($this->client as $k => $c) {
			$this->kill_client($k);	
		}
		if ($this->handle) {
			fclose($this->handle);
		}
		if ($this->sock) {
			socket_close($this->sock);
		}
	}
	
	/**
	 * Création d'une nouvelle connection
	 */
	function create_client() {
		if (in_array($this->sock, $this->read)) {
			for ($i = 0; $i < self::$max_clients+1; $i++) {
				if (!$this->client[$i]) {
					if ($this->client[$i]['sock'] = socket_accept($this->sock)) {
						$this->client[$i]['date'] = time();
						socket_getpeername ($this->client[$i]['sock'], $this->client[$i]['address'] , $this->client[$i]['port'] );
						$this->log("Ouverture de socket ".$this->client[$i]['sock']."/".$this->client[$i]['address'].":".$this->client[$i]['port']);
						$this->client[$i]['address_reduite'] = explode(".",$this->client[$i]['address']);
						$this->client[$i]['address_reduite'] = dechex($this->client[$i]['address_reduite'][0]).dechex($this->client[$i]['address_reduite'][1]).dechex($this->client[$i]['address_reduite'][2]).dechex($this->client[$i]['address_reduite'][3]);					
								
						/* Trop de clients */
						if (count($this->client) > self::$max_clients) {					
							$this->log("Too many clients (".$this->client[$i]['address'].")");
							$this->kill_client($i);
						}
					}
					break;
				}
			}
			if (--$this->ready <= 0)
				return;
		}	
	}
	
	/**
	 * Processus pour chacune des données reçue
	 */
	function step() {
		// If a client is trying to write - handle it now
		foreach($this->client as $key => $item) {
			if (in_array($this->client[$key]['sock'] , $this->read)) {
				$input = socket_read($this->client[$key]['sock'] , 2048);
				$this->log("www --> PHP (".$this->client[$key]['sock'].") [".strlen($input)." octets]",2);
				if ($input==="" || $input===false) {
					/* Zero length string meaning disconnected */
					$this->log("[DECONNECTION] (".$this->client[$key]['sock']."/".$this->client[$key]['address'].":".$this->client[$key]['port'].($input===false ? ", ".socket_strerror(socket_last_error()) : NULL).")",2);
					$this->kill_client($key);

				} elseif (strpos($input,self::$delimiter)===false) {
					/* Information complète supérieure à la taille du paquet */
					$this->log("[BUFFERIZED] (".$this->client[$key]['sock'].") [".strlen($input)." octets] ".$input,2);
					$this->client[$key]['buffer'] .= $input;
					
				} else {
					/* Traitement de la ou les informations reçues */
					
					/* Il peut y avoir plusieurs instructions, on éclate par le délimiteur */
					$inputs = explode(self::$delimiter,$input);
					$this->log(count($inputs)." statements"); //  (".implode(",",$inputs).")
					
					/* Le buffer appartient à la prochaine instruction (première case du tableau) */
					if (isset($this->client[$key]['buffer'])) {
						$inputs[0] = $this->client[$key]['buffer'].$inputs[0];
						unset($this->client[$key]['buffer']);
					}
					
					/* Si la dernière case du tableau ne contient rien, alors l'avant dernière n'est pas une instruction complète, il faut la placer en buffer */
					$this->client[$key]['buffer'] = array_pop($inputs);
					if ($this->client[$key]['buffer']) {
						$this->log("[BUFFERIZED] (".$this->client[$key]['sock'].") [".strlen($this->client[$key]['buffer'])." octets] ".$this->client[$key]['buffer'],2);
					}
					
					foreach ($inputs as $input) {
						$this->log("[STATEMENT PROCESS] [".strlen($input)." octets] ".$input,2);
						
						$json = json_decode($input,true);
						
						/* Initialisation */
						if (isset($json["codename"]) && !$this->client[$key]["codename"]) {
							if ($this->clientById[$json["codename"]][$json["id_user"]]) {
								// On supprime le zombi, ce nouveau socket est prioritaire
								$this->kill_client($this->clientById[$json["codename"]][$json["id_user"]]);
							} 
							$this->client[$key]["codename"] = $json["codename"];
							$this->client[$key]["id_user"] = $json["id_user"];
							$this->clientById[$json["codename"]][$json["id_user"]] = $key;
						}
						
						/* Récupérer la liste des correspondants en ligne du même codename */
						$json["online"] = $this->getOnlineClients($key);
						
						// Première commande observe d'un navigateur
						if($json["connect"]) {
							unset($json["connect"]);
							$this->send($key,$json);
							//$json["msg"] = "online";
							//$json["me"] = true;
						}					
						
						/* Demande de laliste uniquement */
						if (isset($json["getList"])) {
							$this->send($key,$json);
							unset($json["getList"]);
						}
						
						/* Message public */
						elseif(isset($json["msg"])) {
							foreach ($json["online"] as $k => $client) {
								if ($this->client[$k]["id_user"]!=$this->client[$key]["id_user"] // Ne pas envoyer à soi-même
									&& (!$json["id_user_recipient"] || $this->client[$k]["id_user"]==$json["id_user_recipient"])) { // N'envoyer un message privé qu'au destinataire
									$this->send($k,$json);
								}
							}
						}
					}
				}
			} elseif ($this->client[$key]['date']+86400 < time()) {
				// Close the socket if timeout
				$this->log("TIMEOUT ! Close socket :".$this->client[$key]['address']." ".date("Y-m-d H:i:s",$this->client[$key]['date']));
				$this->kill_client($key);
			}
		}
	}
	
	/**
	 * Envoyer un message à Flash
	 * @param integer $key_client Client à qui envoyer le message
	 * @param array $output Infos du message
	 * @param boolean $close_connection Fermer la connection après cet ultime message
	 */
	function send($key_client,$output,$close_connection=false) {
		$this->log("PHP --> www (".$this->client[$key_client]['sock'].") ".$output,2);
		socket_write($this->client[$key_client]['sock'],json_encode($output)/*.self::$delimiter*/);
		if ($close_connection) {
			$this->kill_client($key_client);
		}
	}
	
	/**
	 * Envoyer la liste des clients en ligne
	 * @param integer $key_client Client courant
	 * @return array
	 */
	function getOnlineClients($key_client) {
		foreach ($this->client as $k => $client) {
			// Seulement les utilisateurs de son codename
			if ($this->client[$k]["codename"]==$this->client[$key_client]["codename"]) {
/* Cette protection 'in_array' ne devrait pâs avoir lieue... mais il semblerait que des fois j'ai des doublons... */
				if (!in_array($this->client[$k]["id_user"],$toSend)) {
					$toSend[$k]=$this->client[$k]["id_user"];
				}
			}
		}
		return $toSend;
	}

	/**
	 * Fermer une connection
	 * @param integer $key_client Client à déconnecter
	 */
	function kill_client($key_client) {
		unset($this->clientById[$this->client[$key_client]['codename']][$this->client[$key_client]['id_user']]);
		socket_close($this->client[$key_client]['sock']);
		$this->log("Socket closed :".$this->client[$key_client]['address']);
		unset($this->client[$key_client]);
	}
	
	/**
	 * Retourne une chaîne XML par rapport à un Array PHP
	 * @param string $node Nom du node XML
	 * @param array $attributes Attributs XML
	 * @return string 
	 */
	function array2xml($node, $attributes) {
		foreach ($attributes as $key => $item) {
			$items .= " ".$key."='".urlencode($item)."'";
		}
		return "<".$node.$items." />";
	}
	
	/**
	 * Fonction de log
	 * @param string $s Message à stocker
	 * @param integer $level Niveau de debug
	 * @return string 
	 */
	function log($s,$level=0) {
		//if (__DEBUG__<$level) return;
		if (is_array($s)) {
			ob_start();
			print_r($s);
			$s = ob_get_contents();
			ob_end_clean();
		}
		echo date("Y-m-d H:i:s",time())." ".$s."\n";
		//fwrite($this->handle,date("YmdHis",time())." ".$s."\n");
	}
	
	/**
	 * Reconnection à la base au cas où la connection a sautée
	 */
	function ping() {
		if (!ATF::db()->ping()) {
			$this->log(ATF::db()->error);
			ATF::db(NULL,true);
			$this->log("Reconnexion à MySQL !");
		}
	}
}
?>