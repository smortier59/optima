<?php
/** fonctions utilisées par Asio
* @package asio
* @author Jérémie Gwiazdowski <jgw@absystech.fr>
*/

/** Fonction cliente de Asio
* @param ressource $ressource la ressource socket
* @param object $crypt l'objet de cryptage
* @param string $server_name le nom du serveur demandé
* @param $daemon_asio boolean true si on est dans le daemon (pas de log affiché)
* @param $log_asio object_fichier le fichier dans lequel on enregistre le log

*/
function doClient($ressource,$crypt,$server_name,$daemon_asio,$log_asio){
	if($ressource){
		//Lecture de la commande
		$command=readCommand($ressource,$crypt,$server_name,$daemon_asio,$log_asio);
		//Exécution de la commande
		$retour=execCommand($command,$server_name,$daemon_asio,$log_asio);
		sendCommand($ressource,$command,$retour,$crypt,$server_name,$daemon_asio,$log_asio);
	}else{
		exec_log("Echec de connexion au client : la ressource est invalide\n",$daemon_asio,$log_asio);
	}
}

/** Fonction de lecture et d'analyse de la commande
* @param ressource $ressource la ressource socket
* @param object $crypt l'objet de cryptage
* @param string $server_name le nom du serveur demandé
* @param $daemon_asio boolean true si on est dans le daemon (pas de log affiché)
* @param $log_asio object_fichier le fichier dans lequel on enregistre le log
*/
function readCommand($ressource,$crypt,$server_name,$daemon_asio,$log_asio){
	//**********************
	//Lecture de la commande
	//**********************
	$compteur=0;
	$message='';
	do{
		$compteur++;
		$buffer=socket_read($ressource,2048,PHP_BINARY_READ);
		//echo "buffer : ".$buffer."\n";
		$message.=$crypt->decrypte($buffer);
		//echo "message : ".$message."\n";
		//Arret IMPREVU DE LA LECTURE
		if(substr($message,0,1)!=chr(2)){//Première lecture : On doit avoir le flag de début de lecture
			exec_log("[ERREUR] Flag de début de commande incorrect ! (".ord(substr($message,0,1)).")\n",$daemon_asio,$log_asio);
			return false;
		}
		if($buffer==false){//Buffer vide
			exec_log("[ERREUR] Le tampon de lecture est vide !\n",$daemon_asio,$log_asio);
			return false;
		}
		if($compteur>100){
			exec_log("[ERREUR] Le message envoyé est trop important (MAX:204800 octets)\n",$daemon_asio,$log_asio);
			return false;
		}
		//fin du message
		$end=substr($message,strlen($message)-1,1);
	}while($end!=chr(3));
	
	//On supprime les marqueurs de début et de fin
	$message=substr($message,1,strlen($message)-2);
	//echo 'json:'.$message."\n";
	//On décode le json
	$command=json_decode($message,true);
	//print_r($command);
	
	//Validation de la commande
	if(!isset($command['command'])){
		exec_log("--[ERREUR] Commande non trouvée, reçu:".$message."\n",$daemon_asio,$log_asio);
		return false;
	}elseif(!isset($command['type'])){
		exec_log("--[ERREUR] Type non trouvé, reçu:".$message."\n",$daemon_asio,$log_asio);
		return false;
	}
	exec_log("--Commande : ".$command['command']."\n",$daemon_asio,$log_asio);

	//Analyse de la commande
	$server=$command['server'];
	$user=$command["user"];
	//$command='';
	$i=0;

	//Affichage de l'analyse de la commande
	exec_log("--serveur demandé : ".$server."\n",$daemon_asio,$log_asio);
	//exec_log("--serveur configuré : ".$server_name."\n",$daemon_asio,$log_asio);
	exec_log("--utilisateur demandé : ".$user."\n",$daemon_asio,$log_asio);
	//exec_log("--commande demandée : ".$command['command']."\n",$daemon_asio,$log_asio);
	exec_log("--type demandé : ".$command['type']."\n",$daemon_asio,$log_asio);
	
	//Test du serveur
	if($server!=$server_name){
		exec_log("--Le serveur n'est pas valide, ".$server_name." est demandé\n",$daemon_asio,$log_asio);
		return false;
	}
	
	//Test de droit
	//A écrire
	return $command;
}

/** Fonction d'envoie du retour de la commande
* @param ressource $ressource la ressource socket
* @param array $command l'objet command
* @param string $retour le message de retour
* @param object $crypt l'objet de cryptage
* @param string $server_name le nom du serveur demandé
* @param $daemon_asio boolean true si on est dans le daemon (pas de log affiché)
* @param $log_asio object_fichier le fichier dans lequel on enregistre le log
*/
function sendCommand($ressource,$command,$retour,$crypt,$server_name,$daemon_asio,$log_asio){
	//**********************
	//Envoie du résultat au client
	//**********************
	//Construction du message de retour
	$command=array('command'=>$command['command']
	      ,'type'=>$command['type']
	      ,'server'=>$command['server']
	      ,'user'=>$command["user"]
	      ,'return'=>$retour
	);
	$message=chr(2).json_encode($command).chr(3);;
	//Cryptage du message
	$message=$crypt->crypte($message);
	socket_write($ressource,$message,strlen($message));
	exec_log("--Envoi du message de retour, ".$retour."\n",$daemon_asio,$log_asio);
}

/** Fonction d'éxécution de la commande
* @param string $command le nom de la commande
* @param string $server_name le nom du serveur demandé
* @param $daemon_asio boolean true si on est dans le daemon (pas de log affiché)
* @param $log_asio object_fichier le fichier dans lequel on enregistre le log
*/
function execInternCommand($command,$server_name,$daemon_asio,$log_asio){
	//Recherche des arguments
	$args=array();
	$i=1;
	while(isset($command['arg'.$i])){
		array_push($args,$command['arg'.$i]);
		$i++;
	}
	
	//Exécution de la commande
	$command=trim($command['command']);
	if(!function_exists($command)){
		exec_log("--[ERREUR] La fonction interne '".$command."' n'existe pas !\n",$daemon_asio,$log_asio);
		return false;
	}else{
		return $command($args);}
}

/** Fonction d'éxécution de la commande
* @param string $command le nom de la commande
* @param string $server_name le nom du serveur demandé
* @param $daemon_asio boolean true si on est dans le daemon (pas de log affiché)
* @param $log_asio object_fichier le fichier dans lequel on enregistre le log
*/
function execExternCommand($command,$server_name,$daemon_asio,$log_asio){
	//Construction de la commande
	$command_string=escapeshellcmd($command['command']).' ';
	$i=1;
	while(isset($command['arg'.$i])){
		$command_string.=escapeshellarg($command['arg'.$i]).' ';
		$i++;
	}
	exec_log("--Exécution commande externe : '".$command_string."'\n",$daemon_asio,$log_asio);
	/*Création d'un process qui exécutera la commande*/
	//Descripteur du processus
	$descriptorspec = array(
	0 => array("pipe", "r"),  // // stdin est un pipe où le processus va lire
	1 => array("pipe", "w"),  // stdout est un pipe où le processus va écrire
	2 => array("file", "/tmp/error-output.txt", "a") // stderr est un fichier
	);
	
	//Path d'éxécution du processus
	$cwd = '/';
	//Environnement
	$env = NULL;
	//Commande utilisée
	$cmd=__ABSOLUTE_PATH__.'/serveur-asio/commands/'.$command_string;

	//Lancement du process
	$process = proc_open($cmd, $descriptorspec, $pipes, $cwd, $env);
	//Fermeture du pipe d'écriture
	fclose($pipes[0]);
	//echo $command;
	//Lecture de la sortie du process
	if($command_string=="service-control 'asterisk' 'start' "){
		$retour="Démarrage d'Asterisk effectué [patch asio]\n";
	}else{
		$retour=stream_get_contents($pipes[1]);
	}
	//Fermeture du pipe de lecture
	fclose($pipes[1]);
	
	//fermeture du process
	proc_close($process);
	
	//Renvoie du résultat du process
	return substr($retour,0,strlen($retour)-1);
}

/** Fonction d'éxécution de la commande
* @param string $command le nom de la commande
* @param string $server_name le nom du serveur demandé
* @param $daemon_asio boolean true si on est dans le daemon (pas de log affiché)
* @param $log_asio object_fichier le fichier dans lequel on enregistre le log
*/
function execCommand($command,$server_name,$daemon_asio,$log_asio){
	switch($command['type']){
		case 'intern':
			return execInternCommand($command,$server_name,$daemon_asio,$log_asio);
			break;
		case 'extern':
			return execExternCommand($command,$server_name,$daemon_asio,$log_asio);
			break;
	}
}

/**Routine de gestion de log
* @param string $log le log à enregister
* @param $daemon_asio boolean true si on est dans le daemon (pas de log affiché)
* @param $log_asio object_fichier le fichier dans lequel on enregistre le log
*/
function exec_log($log,$daemon_asio,$log_asio=false){
	if($daemon_asio){
		$log_asio->write($log);
	}else{
		echo $log;
	}
}
?>
