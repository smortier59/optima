<?php
/**
* Messagerie instantanée
* @package ATF
* @version ATF 5 - 2010-10-21
* @author Yann GAUTHERON <ygautheron@absystech.fr>
* @copyright Copyright (c) 2003-2011, AbsysTech
*/ 
class im extends classes_optima {
	/* Variables de définition de socket
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @var sock
	*/
	private $sock;
	
	/* Tableau des lecture sur socket
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @var sock
	*/
	private $r;
	
	/* Tableau des écritures sur socket
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @var sock
	*/
	private $w;
	
	/* Tableau des exceptions sur socket
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @var sock
	*/
	private $e;
	
	/**
	* Adresse d'hôte du démon
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @var string
	*/
	private static $host = "localhost";
	
	/**
	* Port du démon
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @var string
	*/
	private static $port = 16969;

	/**
	 * Constructeur
	 */
	public function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
	}
	
	public function connect() {
		if ($this->sock) {
			return true;
		}
		$this->sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		socket_set_nonblock($this->sock);
		socket_connect($this->sock,self::$host, self::$port);
		socket_set_block($this->sock);
		switch(socket_select($this->r = array($this->sock), $this->w = array($this->sock), $e = array($this->sock), NULL)){
			case 2:
				ATF::$msg->addWarning("Connexion impossible au serveur.","Messagerie instantanée");
				return false;

			case 1:
				return true;
					
			case 0:
				ATF::$msg->addWarning("Le serveur met trop de temps à répondre.","Messagerie instantanée");
				return false;
		}
	}
	
	/**
	* Définir un attribut
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $host
	* @param int $port
	*/	
	function set($host,$port) {
		self::$host = $host;
		self::$port = $port;
	}
	
	/**
	* Se déconnecter
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	*/	
	public function deconnect() {
		socket_close($this->sock);
		$this->sock = NULL;
	}
	
	/**
	* Envoyer un message
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	*/	
	public function send($infos) {
		ATF::getEnv()->commitSession();
		ATF::$cr->block("top");
		if ($this->connect()) {
			$infos["msg"] = urldecode($infos["msg"]);
			$data = array(
				"msg"=>$infos["msg"]
				,"id_user"=>ATF::$usr->getID()
				,"codename"=>ATF::$codename
			);	
			
			// Interprétation d'une syntaxe spéciale
			if (preg_match("/\/to=(.[^\/]*)\/(.*)/",$infos["msg"],$matches)) { // Envoyer message privé 
				if ($id_user = ATF::user()->getIDFromLogin($matches[1])) {
					$data["msg"] = $matches[2];
					$data["id_user_recipient"] = $id_user;
				}
			}
			
			// Sauvegarde du message dans la base
			$this->insert(array(
				"id_user"=>$data["id_user"]
				,"im"=>$data["msg"]
				,"id_user_recipient"=>$data["id_user_recipient"]
			));
			
			// Envoi du message instantané
			$output = json_encode($data)."\t";
			//socket_set_option($this->sock,SOL_SOCKET,SO_RCVTIMEO,array('sec'=>5,'usec'=>0));
			socket_write($this->sock,$output);
			//ATF::$msg->addNotice("Message '".$infos["msg"]."' dispatched.");
			$this->deconnect();
			return $infos;
		}
	}
	
	/**
	* Récupère la liste des utilisateurs en ligne
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	*/	
	public function getList($infos) {
		ATF::getEnv()->commitSession();
		ATF::$cr->block("top");
		if ($this->connect()) {
			$infos["msg"] = urldecode($infos["msg"]);
			$data = array(
				"id_user"=>ATF::$usr->getID()
				,"codename"=>ATF::$codename
				,"getList"=>true
			);	
			
			// Envoi de la demande
			$output = json_encode($data)."\t";
			socket_write($this->sock,$output);
			
			// Récupération de la liste
			$input = socket_read($this->sock , 2048);
			
			$this->deconnect();
			return $this->parseInput($input);
		}
	}
	
	/** 
	* Observe si un message est reçu
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return string le json
	*/
	public function observe($infos) {
		ATF::$cr->block("top");
		if ($this->connect()) {
			// Je me présente
			$data = array(
				"id_user"=>ATF::$usr->getID()
				,"codename"=>ATF::$codename
				,"location"=>urldecode($infos["location"])
			);	
			if ($infos["connect"]) {
				// Premier observe d'une page web du navigateur (Celui tout de suite après un rafraichissement de page F5 par exemples)
				$data["connect"]=true;
			}
			$output = json_encode($data)."\t";
			socket_write($this->sock,$output);

			ATF::getEnv()->commitSession();
			socket_set_option($this->sock,SOL_SOCKET,SO_RCVTIMEO,array('sec'=>60,'usec'=>0));
//			$timeout = 5;
//			$start = time();
//			do {
//				$bytes = socket_recv($this->sock, $input, 2048/*, MSG_WAITALL*/);
//			} while (!$bytes && (time() - $start < $timeout));
			if ($input = socket_read($this->sock , 2048)) {
				$this->deconnect();
				return $this->parseInput($input);
			} else {
				// Si aucun message, on se rabat quand même sur la liste des connectés
				return $this->getList($infos);	
			}
		}
	}
	
	/** 
	* Traite un résultat envoyé par le démon, retourne TRUE si données invalides
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $input Paquet en provenance du démon
	* @return array | boolean
	*/
	public function parseInput($input) {
		if ($input) {
			$infos = json_decode($input,true);
			if ($infos["codename"]==ATF::$codename) {
				// On complète les infos du message : expéditeur
				if ($infos["id_user"]) {
					$infos["user"] = ATF::user()->nom($infos["id_user"]);
					$infos["id_user"] = classes::cryptId($infos["id_user"]);
				}
				
				// Utilisateur destinataire du mesage privé
				if ($infos["id_user_recipient"]) {
					$infos["id_user_recipient_login"] = ATF::user()->select($infos["id_user_recipient"],"login");
				}
				
				// Utilisateur courant
				$infos["current_id_user"] = classes::cryptId(ATF::$usr->getID());
				
				// Liste des connectés
				unset($infos["codename"]);
				if ($infos["online"]) {
					$o = $infos["online"];
					$infos["online"] = array();
					foreach ($o as $u) {
						$infos["online"][]=array(
							"id_user"=>classes::cryptId($u)
							,"user"=>ATF::user()->nom($u)
							,"login"=>ATF::user()->select($u,"login")
						);
					}
				}

				return array("im"=>$infos);
			} else {
				return true;
			}
		} else {
			return true;
		}
	}
	
	/** 
	* Retouren les derniers messages
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return array
	*/
	public function getLast() {
		$this->q->reset()->setLimit(42);
		$data = $this->select_all();
		krsort($data);
		return $data;
	}

	/**
	 * Filtrage d'information général
	 * @author Yann GAUTHERON <ygautheron@absystech.fr>
	 */
	protected function saFilter(){
		// Ne pas voir les messages privés des autres
		$this->q
			->whereIsNull("id_user_recipient","OR","filtrePV",true)
			->orWhere("id_user_recipient",ATF::$usr->getID(),"filtrePV","=")
			->orWhere("id_user",ATF::$usr->getID(),"filtrePV","=");
	}
};
?>