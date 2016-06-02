<?
/** Client-Serveur TCP/IP
* Cette version comporte des méthodes pour créer un serveur multi-threading
* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
* @package réseaux
* @version 2
*/
class Network{
	/*---------Attributs-----------*/
	private $socket;//La socket utilisée
	private $socketRessource;//La ressource liée à la socket
	private $address;//L'adresse d'écoute
	private $port;//le port
	private $logPath;//Le path du log
	private $serveur;//Si true appli serveur, si false appli cliente
	
	/*---------Constructeurs------------*/
	
	/** Crée un nouvel objet "Network" qui sera soit un client ou un serveur
	* @param address string l'adresse IP du serveur
	* @param port int le port à utiliser
	* @param logPath string l'emplacement du fichier de log
	* @param serveur boolean true si l'appli est un serveur, si false c'est une appli cliente
	* @param auto boolean si true lance l'initialisation de l'application (client ou serveur)
	*/
	public function __construct($address,$port,$logPath,$serveur=false,$auto=true){
		$this->address=$address;
		$this->port=$port;
		$this->logPath=$logPath;
		$this->serveur=$serveur;
		if($auto){
			if($serveur){
				$this->serveur();
			}else{
				$this->client();
			}
		}
	}
	
	/*---------Méthodes------------*/
	
	/** Ajoute une ligne de journalisation
	* @param string $message le message à journalisé
	* @return boolean true si l'écriture a réussie
	*/
	private function addLog($message){
		//Si le fichier de log est de 100 Ko on le flush
		if(filesize($this->logPath)>100000){
			$descripteur=fopen($this->logPath,'r');
		}else{
			$descripteur=fopen($this->logPath,'a');}
		//Erreur si l'ouverture a échouée
		if(empty($descripteur)){ return false;}
		//Création du message
		$begin_message=(($this->serveur)?'Serveur':'Client')." - ".date('H:i:s - j/m/Y')." : ";
		$message=$begin_message.$message."\n";
		//Ecriture des données
		fwrite($descripteur,$message,strlen($message));
		//Fermeture du fichier
		fclose($descripteur);
		return true;
	}
	
	/** Création de la socket
	* @return true si la création a réussie
	*/
	public function createSocket(){
		if(($this->socket=socket_create(AF_INET,SOCK_STREAM,SOL_TCP)) === false){
			$this->addLog('La création de socket a échoué');
			$this->addLog(socket_strerror(socket_last_error()));
			return false;
		}else{
			$this->addLog('La création de socket a réussi !');
			//socket_set_nonblock($this->socket);
			return true;
		}
	}
	
	/** "Bind" de la socket
	* @return true si la création a réussie
	*/
	public function bindSocket(){
		if(socket_bind($this->socket,$this->address,$this->port) === false){
			$this->addLog('Le bind de la socket a échoué');
			$this->addLog(socket_strerror(socket_last_error($this->socket)));
			return false;
		}else{
			$this->addLog('Le bind de la socket a réussi !');
			return true;
		}
	}
	
	/** Ecoute de la socket 
	* @return true si la création a réussie
	*/
	public function listenSocket(){
		if(socket_listen($this->socket,1) === false){
			$this->addLog('L\'écoute de la socket a échoué');
			$this->addLog(socket_strerror(socket_last_error($this->socket)));
			return false;
		}else{
			$this->addLog('Le serveur est en attente d\'une connexion !');
			//socket_set_nonblock($this->socket);
			return true;
		}
	}
	
	/** Attente de connexion 
	* @param boolean $multithread evite l'abondance de log dans le cas d'un serveur multithread
	* @return ressource La ressource socket false si cela a échouée
	*/
	public function acceptSocket($multithread=false){
		if(($this->socketRessource=socket_accept($this->socket)) === false){
			if(!$multithread){
				$this->addLog('L\'attente de connexion a échoué');
				$this->addLog(socket_strerror(socket_last_error($this->socket)));}
			return false;
		}else{
			if(!$multithread){
				$this->addLog('Le serveur a accepté la connexion !');}
			return $this->socketRessource;
		}
	}
	
	/** Ecriture sur la socket
	* @param string $message le message à envoyer
	* @param boolean $serveur true si c'est le serveur qui écrit
	* @return true si l'écriture a réussie
	*/
	public function writeSocket($message,$serveur=false){
		if($serveur && !$this->socketRessource){
			$this->addLog('Ecriture impossible, la Ressource est invalide. La connexion a du se couper.');
			return false;
		}else{
			if(socket_write((($serveur)?$this->socketRessource:$this->socket),$message,strlen($message)) === false){
				$this->addLog('L\'écriture sur la socket a échoué');
				$this->addLog(socket_strerror(socket_last_error(($serveur)?$this->socketRessource:$this->socket)));
				return false;
			}else{
				$this->addLog('L\'écriture sur la socket a réussi, le message est :'.$message);
				return true;
			}
		}
	}
	
	/** Lecture sur la socket
	* @param boolean $serveur true si c'est le serveur qui lit
	* @param $taille la taille du buffer de lecture
	* @return $string le message lu, false si la lecture à echouée
	*/
	public function readSocket($serveur=false,$taille=2048,$type=PHP_NORMAL_READ){
		if($serveur && !$this->socketRessource){
			$this->addLog('Ecriture impossible, la Ressource est invalide. La connexion a du se couper.');
			return false;
		}else{
			if(($buffer=socket_read((($serveur)?$this->socketRessource:$this->socket),$taille,$type)) === false){
				$this->addLog('La lecture sur la socket a échoué');
				$this->addLog(socket_strerror(socket_last_error((($serveur)?$this->socketRessource:$this->socket))));
				return false;
			}else{
				$this->addLog('La lecture sur la socket a réussi, le message lu est :'.$buffer);
				return $buffer;
			}
		}
	}
	
	/** Connexion de la socket
	* @return true si la connexion à réussi
	*/
	public function connectSocket(){
		if(socket_connect($this->socket,$this->address,$this->port) === false){
			$this->addLog('La connexion à la socket a échoué');
			$this->addLog(socket_strerror(socket_last_error($this->socket)));
			return false;
		}else{
			$this->addLog('La connexion à la socket a réussi');
			return true;
		}
	}
	
	/** Création client
	* @return boolean true si la création a réussi
	*/
	public function client(){
		//Création de la socket
		if(!$this->createSocket()){return false;}
		//Connexion au serveur
		if(!$this->connectSocket()){return false;}
		return true;
	}
	
	/** Création serveur
	* @return boolean true si la création a réussi
	*/
	public function serveur($multithread=false){
		//Création de la socket
		$this->createSocket();
		$this->bindSocket();
		//Mise en écoute
		$this->listenSocket();
		//Mode non bloquant
		if($multithread){
			socket_set_nonblock($this->socket);
		}
		//Attente d'une connexion
		//$this->acceptSocket();
		return true;
	}
	
	/** Ferme la socket
	* @param boolean $serveur vrai si c'est le serveur 
	*/
	public function closeSocket($serveur=false){
		//Fermeture de la ressource serveur
		if($serveur){
			$this->closeRessourceSocket();
		}
		//Fermeture de la socket
		socket_close($this->socket);
		$this->addLog('Fermeture de la socket');
	}
	
	/** Ferme la ressource socket
	*/
	public function closeRessourceSocket(){
		//Fermeture de la ressource serveur
		socket_close($this->socketRessource);
		$this->addLog('Fermeture de la Ressource socket');
	}
	
	/** Installe un nouveau gestionnaire de signaux
	* Cette méthode doit s'exécuter au début du script
	*/
	public function setSigHandler(){
		//declare(ticks = 1); Ligne à mettre en entête de fichier
		function sig_handler($signo){
			global $stop;
			$stop=false;
			switch ($signo) {
				case SIGINT:
					$stop=true;
					//exit(2);
					break;
				case SIGTERM:
					$stop=true;
					break;
				case SIGCHLD:
					pcntl_waitpid(-1, $status); 
				default:
			}
		}
		pcntl_signal(SIGTERM,"sig_handler");
		pcntl_signal(SIGINT,"sig_handler");
		pcntl_signal(SIGCHLD,"sig_handler");
	}
	
	/** Exécute un client (non non on ne le tue pas ;) )
	* Cette fonction crée un processus fils
	* @param ressource la ressource socket utilisée
	* @param function le coeur du programme client
	*/
	function execClient($ressource,$fonction){
		//On forke le client
		$pid = pcntl_fork();
		
		switch($pid){
			case 0:
				//Exécution de la fonction cliente
				$fonction;
				socket_close($ressource);
				exit(0);
				break;
			case -1:
				//Erreur
				$this->addLog('Creation du processus fils impossible !\n');
				exit(1);
				break;
			default :
				socket_close($ressource);
				pcntl_wait($pid);
				break;
		}
}
};
?>