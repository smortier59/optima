<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "bdomplus";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);


ATF::affaire()->q->reset()->whereIsNull("id_parent")->whereIsNotNull("id_fille");
$affaires_initiales = ATF::affaire()->select_all();


foreach ($affaires_initiales as $key => $value) {
    // On recupere le retour contrat du contrat
    ATF::commande()->q->reset()->where("commande.id_affaire", $value["affaire.id_affaire"]);
    $commande = ATF::commande()->select_row();

    $retour_contrat = $commande["commande.retour_contrat"];




    $fille = ATF::affaire()->select($value["affaire.id_affaire"] , "id_fille");

    if($retour_contrat && $fille) {
        update_recursif($fille, $retour_contrat);
    }
}


function update_recursif ($affaire_fille, $retour_contrat) {

    ATF::commande()->q->reset()->where('commande.id_affaire', $affaire_fille);
    $commande = ATF::commande()->select_row();

    if($commande["commande.retour_contrat"]) {
        $retour_contrat = $commande["commande.retour_contrat"];
    } else {
        ATF::commande()->u(array("id_commande"=> $commande["commande.id_commande"], "retour_contrat" => $retour_contrat));
    }

    $fille = ATF::affaire()->select($affaire_fille, "id_fille");

    if ($fille && $retour_contrat) {
        update_recursif($fille, $retour_contrat);
    }
}