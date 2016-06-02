<?php
/** fonctions internes
* @package asio
* @author Jérémie Gwiazdowski <jgw@absystech.fr>
*/

/** Obtention d'un fichier de configuration
* @param array $args le tableau d'argument, $args['0] doit contenir le path du fichier
*/
function editfile_get($args){
	$fichier=new Fichier($args[0]);
	//echo "je passe\n";
	$retour=$fichier->read();
	//echo 'je lis :'.$retour."\n";
	return $retour;
}

/** sauvegarde du fichier de configuration
* @param array $args le tableau d'argument, $args[0] doit contenir le path du fichier, $args[1] le contenu
*/
function editfile_save($args){
	//Sauvegarde de l'ancien fichier de configuration
	$old=editfile_get($args);
	touch($args[0].'.save-by-asio');
	$fichier=new Fichier($args[0].'.save-by-asio');
	$fichier->write($old,false);
	
	//Remplacement du fichier actuel
	$fichier=new Fichier($args[0]);
	$retour=$fichier->write($args[1],false);
	return $retour;
}

/*function exec_extern_php($args){
	$cmd=__ABSOLUTE_PATH__.'/serveur-asio/commands/'.$args[0];
	return system(escapeshellcmd($cmd));
}*/
?>
