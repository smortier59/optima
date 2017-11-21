<?

/**

Borne DECT = Borne DECT RFP L35IP

Fournisseur : Immotic
Compte AT : Vente de marchandise
Référence : L35IP

Fixe = MITEL Aastra 6867i

Fournisseur : Immotic
Compte AT : Vente de marchandise
Référence : 6867i

DECT = MITEL Aastra 142d

Fournisseur : Immotic
Compte AT : Vente de marchandise
Référence : A142D

* Script qui remet en stock tous les produit issu d'un fichier CSV
* @author Quentin JANON <qjanon@absystech.fr>
*/
define("__BYPASS__",true);
$_SERVER["argv"][1] = "att";
include(dirname(__FILE__)."/../../global.inc.php");

$id_commercial = 24; // Sebastien MORTIER
$compte_absystech = 1; // VENTE DE MARCHANDISE
$fournisseur = 1497; // IMMOTIQUE DISTRIBUTION
$logFile = "batch-import-saint-maclou"; // FICHIER DE LOG
$resume = "Installation téléphonique"; // Nom dud evis et de l'affaire
$id_terme = 1; // A reception de facture
$id_maison_mere = 1320; // A reception de facture

$fichier = "saintmaclou.csv";

if (!file_exists($fichier)) echo "FICHIER INTROUVABLE\n";

$fic = fopen($fichier, 'rb');

$entete = fgetcsv($fic, 0, ";");

$ctSociete = 0;
$ctContact = 0;
$ctAffaire = 0;
$ctDevis = 0;
$ctDevisLigne = 0;

ATF::db()->begin_transaction(true);
log::logger("===================== DEBUT DE SCRIPT =====================",$logFile);
try {
    while ($ligne = fgetcsv($fic, 0, ";")) {
        log::logger("========= LIGNE FROM CSV",$logFile);
        log::logger($ligne,$logFile);
        $emailDefault = "rm".$ligne[1]."@saint-maclou.fr";

        $societe = array(
            "societe"=> "Saint Maclou ".$ligne[0],
            "code_groupe"=> $ligne[1],
            "adresse"=> $ligne[2],
            "adresse_2"=> $ligne[3],
            "adresse_3"=> $ligne[4],
            "cp"=> $ligne[5],
            "ville"=> $ligne[6],
            "tel"=> $ligne[7],
            "fax"=> $ligne[8],
            "email"=> $emailDefault,
            "id_commercial"=>$id_commercial,
            "id_filiale"=>$id_maison_mere,
            "cle_externe"=>"TSM-".$ligne[1]
        );

        log::logger("========= SOCIETE",$logFile);
        log::logger($societe,$logFile);
        $id_societe = ATF::societe()->i($societe);
        log::logger("ID SOCIETE = ".$id_societe,$logFile);
        $ctSociete++;

        $contact = array(
            "id_societe"=>$id_societe,
            "nom"=>"Magasin",
            "prenom"=>"Responsable",
            "fonction"=>"Responsable du magasin",
            "tel"=>$ligne[7],
            "gsm"=>"",
            "fax"=>$ligne[8],
            "email"=>$emailDefault,
            "login"=>"rm".$ligne[1],
            "pwd"=>"fock"
        );

        log::logger("========= CONTACT",$logFile);
        log::logger($contact,$logFile);
        $id_contact = ATF::contact()->i($contact);
        log::logger("ID CONTACT = ".$id_contact,$logFile);
        $ctContact++;

        $affaire = array(
            "id_societe"=>$id_societe,
            "etat"=>"devis",
            "affaire"=>$resume,
            "date"=>date("Y-m-d"),
            "id_termes"=>$id_terme,
            "id_commercial"=>$id_commercial,
            "suivi_ec"=>true
        );

        log::logger("========= AFFAIRE",$logFile);
        log::logger($affaire,$logFile);
        $id_affaire = ATF::affaire()->i($affaire);
        log::logger("ID AFFAIRE = ".$id_affaire,$logFile);
        $ctAffaire++;

        $devis = array(
            "resume"=>$resume,
            "ref"=>ATF::affaire()->getRef(date("Y-m-d"),"devis","LILLE"),
            "id_affaire"=>$id_affaire,
            "id_societe"=>$id_societe,
            "id_contact"=>$id_contact,
            "id_user"=>$id_commercial,
            "type_devis"=>"normal",
            "tva"=>"1.200",
            "date"=>date("Y-m-d"),
            "validite"=>date("Y-m-d",strtotime("+4 months"))
        );
        log::logger("========= DEVIS",$logFile);
        log::logger($devis,$logFile);
        $id_devis = ATF::devis()->i($devis);
        log::logger("ID DEVIS = ".$id_devis,$logFile);
        $ctDevis++;

        $ligne1 = array(
            "id_devis"=>$id_devis,
            "produit"=>"Borne DECT RFP L35IP",
            "ref"=>"L35IP",
            "id_compte_absystech"=>$compte_absystech,
            "id_fournisseur"=>$fournisseur,
            "quantite"=>$ligne[9]
        );
        log::logger("========= LIGNE BORNE DECT",$logFile);
        log::logger($ligne1,$logFile);
        $id_devis_ligne = ATF::devis_ligne()->i($ligne1);
        log::logger("ID LIGNE = ".$id_devis_ligne,$logFile);
        $ctDevisLigne++;

        $ligne2 = array(
            "id_devis"=>$id_devis,
            "produit"=>"MITEL Aastra 6867i",
            "ref"=>"6867i",
            "id_compte_absystech"=>$compte_absystech,
            "id_fournisseur"=>$fournisseur,
            "quantite"=>$ligne[10]
        );
        log::logger("========= LIGNE FIXE",$logFile);
        log::logger($ligne2,$logFile);
        $id_devis_ligne = ATF::devis_ligne()->i($ligne2);
        log::logger("ID LIGNE = ".$id_devis_ligne,$logFile);
        $ctDevisLigne++;

        $ligne3 = array(
            "id_devis"=>$id_devis,
            "produit"=>"MITEL Aastra 142d",
            "ref"=>"A142D",
            "id_compte_absystech"=>$compte_absystech,
            "id_fournisseur"=>$fournisseur,
            "quantite"=>$ligne[11]
        );
        log::logger("========= LIGNE DECT",$logFile);
        log::logger($ligne3,$logFile);
        $id_devis_ligne = ATF::devis_ligne()->i($ligne3);
        log::logger("ID LIGNE = ".$id_devis_ligne,$logFile);
        $ctDevisLigne++;

    }
} catch (errorATF $e) {
    ATF::db()->rollback_transaction(true);
    throw $e;
}
ATF::db()->commit_transaction(true);


echo "DONE !\n";
echo "Sociétés insérées : ".$ctSociete."\n";
echo "Contacts insérés : ".$ctContact."\n";
echo "Affaires insérées : ".$ctAffaire."\n";
echo "Devis insérés : ".$ctDevis."\n";
echo "Devis Ligne insérées : ".$ctDevisLigne."\n";
echo "=====================================\n";
