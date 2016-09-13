<?

/** 
* Script qui permet de faire une mise ajour massive des contact via un CSV généré par nos soins avec toutes les colonnes
* @author Quentin JANON <qjanon@absystech.fr>

Array
(
    [0] => id_contact
    [1] => date
    [2] => civilite
    [3] => nom
    [4] => prenom
    [5] => etat
    [6] => id_societe
    [7] => id_owner
    [8] => private
    [9] => adresse
    [10] => adresse_2
    [11] => adresse_3
    [12] => cp
    [13] => ville
    [14] => id_pays
    [15] => tel
    [16] => gsm
    [17] => fax
    [18] => email
    [19] => fonction
    [20] => departement
    [21] => anniversaire
    [22] => loisir
    [23] => langue
    [24] => assistant
    [25] => assistant_tel
    [26] => tel_autres
    [27] => adresse_autres
    [28] => forecast
    [29] => description
    [30] => cle_externe
    [31] => divers_1
    [32] => disponibilite
)

*/
define("__BYPASS__",true);
$_SERVER["argv"][1] = "alerteo";
include(dirname(__FILE__)."/../../global.inc.php");

$fichier = "contact-02-05-12.csv";
$fic = fopen($fichier, 'rb');

$entete = fgetcsv($fic);
$entete = explode(";",$entete[0]);
if (count($entete)!=33) die('structure non conforme'.count($entete));
$ct=0;
while ($ligne = fgetcsv($fic)) {
	
	$ligne = explode(";",$ligne[0]);
	
	foreach ($ligne as $k=>$i) {
		if ($i=='NULL') continue;
		if ($entete[$k]=='date') continue;

		$u[$entete[$k]] = $i;

	}
	if ($u['id_contact']) {
		try {
			ATF::contact()->u($u);
			echo "Contact : ".$u['id_contact']." MAJ\n";
		} catch (errorATF $e) {
			echo "Contact : ".$u['id_contact']." ERREUR\n";
			$ct++;
			$errors[] = $e;
			//throw $e;
		}
		unset($u);
	}
}

echo "ERREUR : ".$ct."\n";
?>