<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "lm";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);


ATF::facture_fournisseur()->q->reset()->addAllFields("facture_fournisseur")
						  ->whereIsNull("DATE_EXPORT_FACT","AND")
						  ->where("facture_fournisseur.date",date("Y-m-d"),"AND",false,"<=");

$infos = array();
if($factures = ATF::facture_fournisseur()->sa()){
	$file = ATF::facture_fournisseur()->export_ap($infos,$factures);
	log::logger($file,'ccharlier');
	// fichier de test envoyé
	$path = '/tmp/';
	file_put_contents($path.$file['filename'], $file['content']);
	// infos concernant l'envoi sur un FTP
	$ftp_server = __FTP_SERVER__;

	$ftp_conn = ftp_connect($ftp_server) or die("Could not connect to $ftp_server");

	$ftp_username = __FTP_USERNAME__;
	$ftp_userpass = __FTP_USERPASS__;
	$login_res = ftp_login($ftp_conn, $ftp_username, $ftp_userpass);

	if(ftp_put($ftp_conn,'/web/test/'.$file['filename'], $path.$file['filename'], FTP_ASCII)){
		echo "Le fichier ".$file['filename']." a été chargé avec succès\n";
		// Si le fichier a été chargé , envoyer un mail avec le nom du fichier
		$infos_mail["from"] = "Support AbsysTech <no-reply@absystech.fr>";
		$infos_mail["objet"] = "Export Comptable";
		$infos_mail["recipient"] = 'ccharlier@absystech.net';
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