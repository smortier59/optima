<?php
class boulangerpro {

    public function __construct() {

    }

    public function _majPrix () {
        require __ABSOLUTE_PATH__.'includes/cleodis/boulangerpro/ApiBoulangerProV2.php';

        if (__DEV__) {
          $id_fournisseur = 28973;
          $host = "https://test.api.boulanger.pro/v2/";
          $customerKey = "CLEODISTEST";
          $secretKey = "yK7qcGnFRKntDRcVSm6fRxPV5hPPPwtg";
        } else { 
          die('on est pas en dev');
          $id_fournisseur = false;
          $host = "https://api.boulanger.pro/v2/";
          $customerKey = "CLEODISTEST";
          $secretKey = "yK7qcGnFRKntDRcVSm6fRxPV5hPPPwtg";
        }


        $api = new ApiBoulangerProV2($customerKey,$secretKey,$host);
        echo "\n========== DEBUT DU BATCH ==========";
        log::logger("-----------------------------------------------------------------","batch-majPrixCatalogueProduit");
        log::logger("==========DEBUT DU BATCH==========","batch-majPrixCatalogueProduit");

        ATF::db()->begin_transaction(true);
        try {

          ATF::produit()->q->reset()
            ->where('site_associe', 'boulangerpro')
            ->where('etat', 'actif')
            ->where('id_fournisseur', $id_fournisseur); 

          $catalogueBoulProActif = ATF::produit()->sa();

          echo "\n".count($catalogueBoulProActif). " produits à traiter";
          log::logger(count($catalogueBoulProActif). " produits à traiter","batch-majPrixCatalogueProduit");


          foreach ($catalogueBoulProActif as $k=>$produit) {
            $response = $api->get('price/'.$produit['ref']);

            $r = $response->getContent();

            if ($r['error_code']) {
              echo "\n>Produit ref ".$produit['ref']." - ".$produit['produit']." - introuvable chez Boulanger PRO : ".$r['error_code']." - ".$r['message'];
              log::logger("Produit ref ".$produit['ref']." - ".$produit['produit']." - introuvable chez Boulanger PRO : ".$r['error_code']." - ".$r['message'],"batch-majPrixCatalogueProduit");
            } else {
              echo "\n>Produit ref ".$produit['ref']." - ".$produit['produit']." - trouvé chez Boulanger PRO ! Prix boulpro : ".$p['price_tax_excl']." VS Prix cléodis : ".$produit['prix_achat'];
              log::logger("Produit ref ".$produit['ref']." - ".$produit['produit']." - trouvé chez Boulanger PRO ! Prix boulpro : ".$p['price_tax_excl']." VS Prix cléodis : ".$produit['prix_achat'],"batch-majPrixCatalogueProduit");
              $p = $r[0];
              if ($produit['prix_achat'] != $p['price_tax_excl']) {
                echo "\n ----- Prix modifié pour ce produit";
                log::logger("----- Prix modifié pour ce produit","batch-majPrixCatalogueProduit");

                // MAJ nouveau prix sur le produit
                ATF::produit()->u(array("id_produit"=>$produit['id_produit'],"new_prix"=>$p['price_tax_excl']));

                // Produit inclus, on va désactiver tous les packs associés
                if ($produit['max'] == $produit['min'] && $produit['max'] == $produit['defaut']) {
                  echo "\n ----- Produit inclus - on désactive le pack, quantité min ".$produit['min'].", max ".$produit['max'].", defaut ".$produit['defaut'];
                  log::logger("----- Produit inclus - on désactive le pack, quantité min ".$produit['min'].", max ".$produit['max'].", defaut ".$produit['defaut'],"batch-majPrixCatalogueProduit");
                  $packs = ATF::produit()->getPacks($produit['id_produit']);

                  foreach ($packs as $pack) {
                    echo "\n ----- Désactivation pack associé : ".$pack['id_pack_produit'];
                    log::logger("----- Désactivation pack associé : ".$pack['id_pack_produit'],"batch-majPrixCatalogueProduit");

                    ATF::pack_produit()->u(array("id_pack_produit"=>$pack['id_pack_produit'],"etat"=>"inactif"));
                    $packDesactive[] = $pack['id_pack_produit'];
                  }
                } else {
                  // Produit non inclus, on va désactiver uniquement le produit
                  echo "\n ----- On désactive le produit car il est non inclus";
                  log::logger("----- On désactive le produit car il est non inclus","batch-majPrixCatalogueProduit");
                  ATF::produit()->u(array("id_produit"=>$produit['id_produit'],"etat"=>"inactif"));
                  $produitDesactive[] = $produit['id_produit'];
                }
              } else {
                echo "\n ----- Prix inchangé pour ce produit, on ne traite pas";
                log::logger("----- Prix inchangé pour ce produit, on ne traite pas","batch-majPrixCatalogueProduit");
              }
            }

          }


        } catch (errorATF $e) {
          ATF::db()->rollback_transaction(true);
          throw $e;
        }
        ATF::db()->commit_transaction(true);

        echo "\n========== FIN  DU  BATCH ==========";
        echo "\nPacks désactivésn\n";
        print_r($packDesactive);
        echo "\nProduits désactivés";
        print_r($produitDesactive);

        $sendmail = false;
        $infos_mail["from"] = "Support AbsysTech <no-reply@absystech.fr>";
        $infos_mail["objet"] = "[BOULMANGER PRO] Batch prix - packs et produits désactivés";
        $infos_mail["recipient"] = "qjanon@absystech.fr";

        $infos_mail['body'] = '';

        if (!empty($packDesactive)) {
          $sendmail = true;
          $infos_mail['body'] .= "Packs désactivés : ".implode($packDesactive, ",");
        }

        if (!empty($produitDesactive)) {
          $sendmail = true;
          $infos_mail['body'] .= "Produits désactivés : ".implode($produitDesactive, ",");
        }


        if ($sendmail) {
          $mail = new mail($infos_mail);
          $mail->send();    
        }
        log::logger("Packs désactivésn","batch-majPrixCatalogueProduit");
        log::logger($packDesactive,"batch-majPrixCatalogueProduit");
        log::logger("Produits désactivés","batch-majPrixCatalogueProduit");
        log::logger($produitDesactive,"batch-majPrixCatalogueProduit");
        log::logger("========== FIN  DU  BATCH ==========\n","batch-majPrixCatalogueProduit");

    }

