<?php
define("__BYPASS__",true);
// $_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../../global.inc.php");
ATF::define("tracabilite",false);

$bmnPath = realpath("../../../bmn");

// Commande SFTP pour se connecter au serveur SFTP de prod de slimpay
// shell_exec("sftp -oPubkeyAcceptedKeyTypes=ssh-rsa -oHostKeyAlgorithms=ssh-rsa,ssh-dss -oKexAlgorithms=diffie-hellman-group1-sha1,diffie-hellman-group-exchange-sha1 -o Port=6321 -o IdentityFile=id_rsa bymycar01@slimprod.slimpay.net");

// Commande SFTP pour se connecter au serveur SFTP de prod de slimpay et récupérer tous les fichiers dans le dossier `out/`
// shell_exec("sftp -oPubkeyAcceptedKeyTypes=ssh-rsa -oHostKeyAlgorithms=ssh-rsa,ssh-dss -oKexAlgorithms=diffie-hellman-group1-sha1,diffie-hellman-group-exchange-sha1 -o Port=6321 -o IdentityFile=id_rsa bymycar01@slimprod.slimpay.net:out/* /home/www/optima/core/bmn");

// Commande SFTP provisoire connecté au SFTP de dev
shell_exec("sshpass -p recette sftp -P 1110 bymycar01@172.10.10.62:out/* /home/www/optima/core/bmn");

ATF::slimpay()->updateAllBankMobilityEntites($BmnPath);
?>