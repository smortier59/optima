<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "lm";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);



$export_opteven = ATF::affaire()->export_opteven(false);


$path = '/tmp/';
$filename = "LeroyMerlin" . date("Ymd") . ".csv";
file_put_contents($path.$filename, $export_opteven);



//Envoi à OPTEVEN via SFTP
$cmd = 'lftp -p 222 sftp://leroymerlin2018:KOcSoDLmfKe0vS4ehIFo@46.218.94.135 -e "put -O put/ '.$path.$filename.'; bye"';
`$cmd`;

//Si on est le 1er du mois, on envoi le fichier par mail également à Benjamin et Estelle
//if(date("d") === "01"){
    $infos_mail["from"] = "Support AbsysTech <no-reply@absystech.fr>";
	$infos_mail["objet"] = "Fichier envoyé à Opteven le ".date("d/m/Y", strtotime("-1 day"));
	$infos_mail["recipient"] = 'benjamin.tronquit@cleodis.com; estelle.tampigny@leroymerlin.fr';
	//$infos_mail["recipient"] = 'mfleurquin@absystech.fr';
	$infos_mail["template"] = "flux_opteven";
	$info_mail["html"] = true;

   	$mail = new mail($infos_mail);
   	$mail->addFile($path.$filename,$filename,true);
   	$mail->send();
//}

// supprime le fichier une fois l'avoir envoyé & mis en PJ
unlink($path.$filename);

