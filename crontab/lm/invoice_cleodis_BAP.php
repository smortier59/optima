<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "lm";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);


ATF::facture_fournisseur()->q->reset()
						  ->addAllFields("facture_fournisseur")
						  ->whereIsNull("DATE_EXPORT_BAP","AND")
						  ->where("facture_fournisseur.date",date("Y-m-d"),"AND",false,"<=");

$infos = array();
if($factures = ATF::facture_fournisseur()->sa()){
	$file = ATF::facture_fournisseur()->export_bap($infos,$factures);
	// fichier de test envoyé
	$path = '/tmp/';
	file_put_contents($path.$file['filename'], $file['content']);
	// infos concernant l'envoi sur un FTP
$cmd = "sftp -i ".__DIR__."/id_dsa_adeo -P 2222 lmab_cleodis@ftptransfer-prod.adeoservices.com << EOF
put ".$path.$file['filename']." IN/
EOF";
$result = `$cmd`;

	if($result){
		echo "Le fichier ".$file['filename']." a été chargé avec succès\n";
		// Si le fichier a été chargé , envoyer un mail avec le nom du fichier
		$infos_mail["from"] = "Support AbsysTech <no-reply@absystech.fr>";
		$infos_mail["objet"] = "Export Comptable";
		$infos_mail["recipient"] = 'mfleurquin@absystech.net';
		$infos_mail["template"] = "invoice_cleodis_file_updated";
		$info_mail["html"] = true;

	   	$mail = new mail($infos_mail);
	   	$mail->addFile($path.$file['filename'],$file['filename'],true);
	   	$mail->send();
	   	// supprime le fichier une fois l'avoir envoyé & mis en PJ
	   	unlink($path.$filename);
	} else {
		echo "Il y a eu un problème lors du chargement du fichier $file\n";
	}

	// Fermeture de la connexion
	ftp_close($conn_id);
}