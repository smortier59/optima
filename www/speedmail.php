<?
if ($_GET["societe"]) $_SERVER["argv"][1] = base64_decode($_GET["societe"]);
include(dirname(__FILE__)."/../global.inc.php");

if (ATF::_g("societe")) {
    ATF::define_db("db","extranet_v3_".base64_decode(ATF::_g("societe")));
    ATF::$codename = base64_decode(ATF::_g("societe"));
}

if (strlen(ATF::_g("unregister"))==32) { // DESINSCRIPTION
	$id_contact = ATF::emailing_contact()->fromMD5(ATF::_g("unregister"));
	ATF::$html->assign("email",ATF::emailing_contact()->select($id_contact,'email'));
	if (ATF::emailing_contact()->select($id_contact,'opt_in')=="non") {
		ATF::$html->assign("alreadyUnregister",true);
	}
	if (ATF::_g('confirm')) {
		ATF::emailing_contact()->unregister($_GET["unregister"]);
		ATF::$html->assign("confirm",true);
	} else {
		ATF::$html->assign("societe",ATF::_g("societe"));
		ATF::$html->assign("unregister",ATF::_g("unregister"));
	}
	ATF::$html->display("speedmail_unregister.tpl.htm");
	die();
} elseif (ATF::_g("idel")) { // TRACKING D'UN LIEN
	$id_lien = ATF::emailing_lien()->fromMD5(ATF::_g("idel"));
	$lien = ATF::emailing_lien()->select($id_lien);
	if (ATF::_g("ideje")) {
		$id_email = ATF::emailing_job_email()->fromMD5(ATF::_g("ideje"));
		$email = ATF::emailing_job_email()->select($id_email);
	}

	if ($email) {
		//Insertion dans la table rtacking des infos (ip, host, etc...)
		$tracking = array(
			"id_emailing_job_email"=>$email["id_emailing_job_email"]
			,"id_emailing_lien"=>$lien["id_emailing_lien"]
			,"ip"=>$_SERVER["REMOTE_ADDR"]
			,"host"=>gethostbyaddr($_SERVER["REMOTE_ADDR"])
		);

		ATF::emailing_tracking()->insert($tracking);
		
		ATF::emailing_job_email()->increase($email["id_emailing_job_email"],"tracking");
		ATF::emailing_job_email()->update(array("id_emailing_job_email"=>$email["id_emailing_job_email"],"last_tracking"=>date("Y-m-d H:i:s",time())));
		
		ATF::emailing_liste_contact()->increase($email["id_emailing_liste_contact"],"tracking");
		ATF::emailing_liste_contact()->update(array("id_emailing_liste_contact"=>$email["id_emailing_liste_contact"],"last_tracking"=>date("Y-m-d H:i:s",time())));
		
		$elc = ATF::emailing_liste_contact()->select($email["id_emailing_liste_contact"]);
		ATF::emailing_contact()->increase($elc["id_emailing_contact"],"tracking");
		ATF::emailing_contact()->update(array("id_emailing_contact"=>$elc["id_emailing_contact"],"last_tracking"=>date("Y-m-d H:i:s",time())));
		
		ATF::emailing_liste()->increase($elc["id_emailing_liste"],"tracking");
		ATF::emailing_liste()->update(array("id_emailing_liste"=>$elc["id_emailing_liste"],"last_tracking"=>date("Y-m-d H:i:s",time())));
		
	}
	header("Location: ".$lien["url"]);
	die();	
	
} elseif (($id_projet = ATF::_g("id_emailing_projet")) && ATF::_g("preview")) { // PREVIEW SUR LA PAGE SELECT DE PROJET
	$projet = ATF::emailing_projet()->select($id_projet);

	//URL de désinscription
	$projet["unregister"]= "#";
	//URL de visualisation du mail dans une page HTML 
	$projet["viewer"]= "#";
	ATF::$html->assign("projet",$projet);
	ATF::$html->display("speedmail.tpl.htm");
	die();
} elseif (ATF::_g("id") && $id_projet = ATF::emailing_projet()->fromMD5(ATF::_g("id"))) { // VOIR LE MAIL EN HTML

	$projet = ATF::emailing_projet()->select($id_projet);
	$id_emailing_job = ATF::emailing_job_email()->fromMD5(ATF::_g("ideje"),"id_emailing_job");

	// Incrémentation de la visu en ligne 
	ATF::emailing_job()->increase($id_emailing_job,"nb_visu_online");
	
	$id_emailing_liste_contact = ATF::emailing_job_email()->fromMD5(ATF::_g("ideje"),"id_emailing_liste_contact");
	ATF::emailing_projet()->apply_links($projet["corps"],$id_emailing_job,$id_emailing_liste_contact);
	
	$id_emailing_contact = ATF::emailing_contact()->fromMD5(ATF::_g("idec"),"id_emailing_contact");
	$recipient = ATF::emailing_contact()->select($id_emailing_contact,"email");
	if (ATF::_g("societe")=="pvr") {
		$frequence = ATF::emailing_contact()->select($id_emailing_contact,"frequence");
	}
	ATF::emailing_projet()->filters($projet["corps"],$id_emailing_contact);
	
	if (ATF::_g("societe")=="pvr") {
		// Récupération des vidéos
		$ls = date("Y-m-d H:i:s",ATF::_g('last_sollicitation'));
		$v = ATF::emailing_projet()->getVideos($recipient ,$ls);
		ATF::$html->assign("frequence",$frequence);
		ATF::$html->assign("videos",$v);
	}


	ATF::$html->assign("projet",$projet);
	ATF::$html->assign("recipient",$recipient);
	ATF::$html->assign("inline",true);
	ATF::$html->display("speedmail.tpl.htm");
	die();
} else {
	header("Location: http://www.absystech.fr/");
	die();
}
?>