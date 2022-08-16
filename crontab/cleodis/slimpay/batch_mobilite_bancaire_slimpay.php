<?php
define("__BYPASS__",true);
// $_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../../global.inc.php");
ATF::define("tracabilite",false);
$logFile = 'slimpay.log';

$bmnPath = realpath("../../../bmn");
$dateNow = date("Ymd");
$bmnName = "BMN-".$dateNow."-??????????????";

// Commande SFTP pour se connecter au serveur SFTP de prod de slimpay
// exec("sftp -oPubkeyAcceptedKeyTypes=ssh-rsa -oHostKeyAlgorithms=ssh-rsa,ssh-dss -oKexAlgorithms=diffie-hellman-group1-sha1,diffie-hellman-group-exchange-sha1 -o Port=6321 -o IdentityFile=id_rsa bymycar01@slimprod.slimpay.net", $output);

if ($_SERVER["argv"][2] === "dev") {
  // Commande SFTP provisoire connecté au SFTP de dev
  exec("sshpass -p recette sftp -P 1110 bymycar01@172.10.10.62:out/{$bmnName} /home/www/optima/core/bmn", $output);
} else {
  // Commande SFTP pour se connecter au serveur SFTP de prod de slimpay et récupérer le dernier fichier BMN dans le dossier `out/`
  exec("sftp -oPubkeyAcceptedKeyTypes=ssh-rsa -oHostKeyAlgorithms=ssh-rsa,ssh-dss -oKexAlgorithms=diffie-hellman-group1-sha1,diffie-hellman-group-exchange-sha1 -o Port=6321 -o IdentityFile=id_rsa bymycar01@slimprod.slimpay.net:out/{$bmnName} /home/www/optima/core/bmn", $output);
}

// Vérification de la présence de fichier BMN sur le sftp
if (count($output)) {

  ATF::slimpay()->updateAllBankMobilityEntites($bmnPath);

  // Suppression du fichier csv après avoir été traité
  $files = glob($bmnPath."/*");
  foreach($files as $file) {
    if(is_file($file)) {
      unlink($file);
    }
  }
  log::logger("Bank mobility updated !", $logFile);
} else {
  log::logger("No new BMN available !", $logFile);
}
?>