<?
/** Crontab hebdo pour relancer les clients donc requetes en attente
* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
*/
define("__BYPASS__",true);
include(dirname(__FILE__)."/../../global.inc.php");

ATF::hotline()->q->reset()->where("etat" , "wait")
						  ->where("visible" , "oui");
$wait = ATF::hotline()->select_all();

foreach ($wait as $key => $value) {
	$contact = ATF::contact()->select($value["id_contact"]);	
	if($contact["email"]){	
		//Construction du mail		
		$infos_mail['contact']= $contact["civilite"]." ".$contact["nom"]." ".$contact["prenom"];
		$infos_mail['id_hotline']=$value["id_hotline"];
		$infos_mail['hotline']=$value["hotline"];
		$infos_mail["from"] = "Support AbsysTech <no-reply@absystech.fr>";
		$infos_mail["objet"] = "Un ticket est en attente d'une action de votre part";
		$infos_mail["recipient"] = $contact['email'];
		$infos_mail["template"] = "waitHotlineMail";
		$info_mail["html"] = true;
		
		//Envoi du mail
		$mail = new mail($infos_mail);
		$mail->send();		
	}		
}

?>