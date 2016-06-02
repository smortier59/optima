<?
/** 
* Crontab qui permet de gérer les envois de mail, et les création de tâche pour les notes de frais.
* @author Quentin JANON <qjanon@absystech.fr>
* @date 22-11-2010
*/
define("__BYPASS__",true);
include("../global.inc.php");

// Séléction des note de frais de ce mois ci
$d = mktime(0,0,0,date('m'),ATF::note_de_frais()->getJourLimit()-1,date('Y'));	
$name = ATF::note_de_frais()->getNom($d);
//$name = "Avril 2011";
//echo $name."\n";

$contentBegin = "Plusieurs note de frais doivent être validées pour ".$name.", en voici la liste : \n";

ATF::note_de_frais()->q->reset()->where("note_de_frais",$name);
foreach (ATF::note_de_frais()->sa() as $k=>$i) {
//	echo "Note de frais de : ".ATF::user()->nom($i['id_user'])."\n";
	$content[ATF::user()->select($i['id_user'],'id_superieur')] = "\n".ATF::user()->nom($i['id_user'])."\n";
	
}

$contentEnd = "\nPour vous occuper de ces note de frais, rendez vous sur Optima dans le module 'Note de Frais'.";

foreach ($content as $k=>$i) {
	$infos = array(
		'tache'=>array(
			"id_user"=>13
			,"tache"=>$contentBegin.$i.$contentEnd
			,"id_societe"=>ATF::user()->select($k,'id_societe')
			,"horaire_debut"=>date("Y-m-d H:i:s")
			,"horaire_fin"=>date("Y-m-d H:i:s",mktime(date('H'),date('i'),date('s'),date('m'),25,date('Y')))
		)
		,"dest"=>$k
	);
	ATF::tache()->insert($infos);

	$mail = new mail(array( 
		"recipient"=>"frais@absystech.fr", 
		"objet"=>"[Note de frais] ".$name." a valider",
		"body"=>$contentBegin.$i.$contentEnd
	));
	$mail->send();
}


?>