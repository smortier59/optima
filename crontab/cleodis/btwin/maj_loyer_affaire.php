<?php

    define("__BYPASS__",true);
    $_SERVER["argv"][1] = "cleodis";
    include(dirname(__FILE__)."/../../../global.inc.php");
    ATF::define("tracabilite",false);
    error_reporting(E_ALL);
    $frsDecathlon = 20622; // PROD
    $frsDecathlon = 20622; // RCT
    $frsDecathlon = 20622; // DEV



    ATF::db()->begin_transaction();
    try {
        $q = "SELECT
            affaire.`id_affaire`,
            affaire.`ref`,
            affaire.affaire,
            COUNT(devis_ligne.id_devis_ligne) as nbLignes,
            loyer.duree,
            loyer.loyer
        FROM `affaire`
        LEFT JOIN loyer ON loyer.id_affaire = affaire.id_affaire
        LEFT JOIN devis ON devis.id_affaire = affaire.id_affaire
        LEFT JOIN devis_ligne ON devis_ligne.id_devis=devis.id_devis
        WHERE site_associe = 'btwin' AND affaire.etat != 'perdue'
        GROUP BY affaire.id_affaire
        ORDER BY COUNT(devis_ligne.id_devis_ligne) DESC";

        $list = ATF::db()->sql2array($q);

        foreach ($list as $k=>$i) {
            if ($i['nbLignes'] == 1) {
                $affaireUneLigne++;
                echo "\nAffaire ".$i['id_affaire']." - ".$i['ref']." - ".$i['affaire']." ==> UNE SEULE LIGNE !";
                continue;
            }
            if ($i['nbLignes'] > 2) {
                $affairePlusDeDeuxLigne++;
                echo "\nAffaire ".$i['id_affaire']." - ".$i['ref']." - ".$i['affaire']." ==> PLUS DE DEUX LIGNES !";
                continue;
            }
            $affaireTraitee++;
            echo "\nTraitement de l'affaire ".$i['id_affaire']." - ".$i['ref']." - ".$i['affaire']." - ".$i['nbLignes']." lignes identifiées";

            // On travail sur le devis et ses lignes
            ATF::devis()->q->reset()->where('devis.id_affaire',$i['id_affaire']);
            $d = ATF::devis()->select_all();

            if (!$d) {
                echo "\nPas de devis pour l'affaire ".$i['id_affaire']."\n";
                continue;
            }
            if (count($d) > 1) throw new errorATF("Plus d'un devis pour l'affaire ".$i['id_affaire']);
            $devis = $d[0];

            ATF::devis_ligne()->q->reset()->where("id_devis",$devis['id_devis'])->where("id_fournisseur", $frsDecathlon)->where('id_fournisseur',29109);

            $ld = ATF::devis_ligne()->sa();
            if (!$ld) {
                echo "\nPAS DE ligne de devis avec le fournisseur Decathlon SA pour l'devis ".$devis['id_devis']."\n";
                continue;
            }
            if (count($ld) > 1) throw new errorATF("Plus d'une ligne de devis avec le fournisseur Decathlon SA pour l'devis ".$devis['id_devis']);
            $lignes_devis = $ld[0];
            $lignes_devis['loyer'] = $i['loyer'];
            $lignes_devis['duree'] = $i['duree'];

            print_r($lignes_devis);
            ATF::devis_ligne()->u($lignes_devis);

            // On travail sur le commande et ses lignes
            ATF::commande()->q->reset()->where('commande.id_affaire',$i['id_affaire']);
            ATF::commande()->q->setToString();
            echo "\n\nREQUETE 2".ATF::commande()->select_all();
            ATF::commande()->q->unsetToString();
            $c = ATF::commande()->select_all();
            if (!$c) {
                echo "\nPas de commande pour l'affaire ".$i['id_affaire']."\n";
                continue;
            }
            if (count($c) > 1) throw new errorATF("Plus d'une commande pour l'affaire ".$i['id_affaire']);
            $commande = $c[0];

            ATF::commande_ligne()->q->reset()->where("id_commande",$commande['commande.id_commande'])->where("id_fournisseur", $frsDecathlon);
            $lc = ATF::commande_ligne()->sa();
            if (!$lc) {
                echo "\nPAS DE ligne de commande avec le fournisseur Decathlon SA pour l'commande ".$commande['commande.id_commande']."\n";
                continue;
            }
            if (count($lc) > 1) throw new errorATF("Plus d'une ligne de commande avec le fournisseur Decathlon SA pour l'commande ".$commande['commande.id_commande']);
            $lignes_commande = $lc[0];
            print_r($lignes_commande);

            $lignes_commande['loyer'] = $i['loyer'];
            $lignes_commande['duree'] = $i['duree'];

            ATF::commande_ligne()->u($lignes_commande);

        }

    } catch (errorATF $e) {
        ATF::db()->rollback_transaction();
        throw $e;
    }
    ATF::db()->commit_transaction();

    echo "\nAffaire avec une seule ligne : ".$affaireUneLigne;
    echo "\nAffaire avec plus de deux lignes : ".$affairePlusDeDeuxLigne;
    echo "\nAffaire traitée : ".$affaireTraitee;
    echo "\nNombre Affaire total : ".count($list);