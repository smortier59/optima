<?

/** 
* Crontab qui permet de gérer les envois de mail pour les abonnements
* @author Quentin JANON <qjanon@absystech.fr>
* @date 05-03-2014
*/
define("__BYPASS__",true);

$frequence = $_SERVER["argv"][1];
if (!$frequence) die("Impossible de lancer le script sans avoir la fréquence !");
$_SERVER["argv"][1] = "pvr";
include(dirname(__FILE__)."/../../global.inc.php");

$libFrequenceEJ = ATF::emailing_contact()->getTradFrequence($frequence);
$libFrequenceEL = ATF::emailing_contact()->getTradFrequence($frequence,"f");

echo "============================================\n";
echo "Date ".date("d-m-Y H:i:s")."\n";
echo "Lancement de la crontab ".$libFrequenceEJ."\n";

// Récupération de tous les abonnés avec leur abonnement en fonction de la fréquence
ATF::emailing_contact()->q->reset()->where('frequence',$frequence)->where('opt_in','oui');
$r = ATF::emailing_contact()->select_all();

echo "Traitement des ".count($r)." contacts inscrits \n";

// Création de la liste de diffusion
$id_liste = ATF::emailing_liste()->insert(array("emailing_liste"=>"Liste ".$libFrequenceEL));

// Pour chaque abonnés récupérer plus haut, on en vérifie l'existence et on le créer si besoin
foreach ($r as $k=>$ec) {
	$i = array("id_emailing_contact"=>$ec['id_emailing_contact'],"id_emailing_liste"=>$id_liste);
	ATF::emailing_liste_contact()->i($i);
}

// Création du job
$job = array("emailing_job"=>"Job ".$libFrequenceEJ,"id_emailing_projet"=>1,"id_emailing_liste"=>$id_liste,"depart"=>date("Y-m-d H:i:s"));
ATF::emailing_job()->insert($job);

echo "Date ".date("d-m-Y H:i:s")."\n";
echo "Fin de la crontab ".$libFrequenceEJ."\n";
echo "============================================\n";

