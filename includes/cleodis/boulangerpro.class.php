<?
require __ABSOLUTE_PATH__.'includes/cleodis/boulangerpro/ApiBoulangerProV2.php';
class boulangerpro extends classes_optima {
  public function __construct() {
    $this->logFile = "boulangerpro";

    $this->api = new ApiBoulangerProV2(__API_BOULANGER_CLIENT__,__API_BOULANGER_SECRET__,__API_BOULANGER_HOST__);
  }

  public function _boulangerMajPrix($get, $post) {
    $this->logFile = "batch-majPrixCatalogueProduit-".date("Ymd-His");
    // $this->logFile = "qjanon";
    try {

      ATF::societe()->q->reset()->where("societe", "BOULANGER PRO", "AND", false, "LIKE");
      $id_fournisseur = ATF::societe()->select_cell();

      log::logger("-----------------------------------------------------------------",$this->logFile);
      log::logger("==========DEBUT DU BATCH==========",$this->logFile);

      ATF::db()->begin_transaction(true);
      try {

        ATF::produit()->q->reset()->where('id_fournisseur', $id_fournisseur)->where('etat','actif');

        ATF::produit()->q->where('ref',842769, "OR", "ref_produit", "LIKE"); // Produit id 21330 - Lave linge hublot BOSCH EX WAN28150FF
        // Produits demandés par Benjamin sur skype le 9/5/19 à 17h18
        ATF::produit()->q->where('ref',1016843, "OR", "ref_produit", "LIKE"); 
        ATF::produit()->q->where('ref',1119534, "OR", "ref_produit", "LIKE"); 
        ATF::produit()->q->where('ref',"BPLivrMeSGEODI100a125EUR60mois", "OR", "ref_produit", "LIKE"); 
        ATF::produit()->q->where('ref',"Frais60", "OR", "ref_produit", "LIKE"); 
        ATF::produit()->q->where('ref',1119576, "OR", "ref_produit", "LIKE"); 
        ATF::produit()->q->where('ref',1114199, "OR", "ref_produit", "LIKE"); 
        ATF::produit()->q->where('ref',"BPLivrMeSGEODI50a75EUR60mois", "OR", "ref_produit", "LIKE"); 
        ATF::produit()->q->where('ref',1021609, "OR", "ref_produit", "LIKE"); 
        ATF::produit()->q->where('ref',264570, "OR", "ref_produit", "LIKE"); 
        ATF::produit()->q->where('ref',1047180, "OR", "ref_produit", "LIKE"); 

        // Produits demandés par Vincent /Benjamin, mail du mardi 21/05/19 à 15h50
        ATF::produit()->q->where('ref',280814, "OR", "ref_produit", "LIKE"); 
        ATF::produit()->q->where('ref',146031, "OR", "ref_produit", "LIKE"); 
        ATF::produit()->q->where('ref',151577, "OR", "ref_produit", "LIKE"); 
        ATF::produit()->q->where('ref',1022585, "OR", "ref_produit", "LIKE"); 

        $catalogueBoulProActif = ATF::produit()->sa();

        // echo "\n".count($catalogueBoulProActif). " produits à traiter";
        log::logger(count($catalogueBoulProActif). " produits à traiter",$this->logFile);


        foreach ($catalogueBoulProActif as $k=>$produit) {
          log::logger("-- Produit ref ".$produit['ref']." - ".$produit['produit'],$this->logFile);
          
          $prix_livraison = 0;          
          $r = self::APIBoulPROlivraison($produit['ref']);

          log::logger("---- Appel boulpro API livraison",$this->logFile);
          log::logger($r,$this->logFile);
          if (!$r) {
            log::logger("Introuvable chez Boulanger PRO : AUCUNE REPONSE",$this->logFile);
            continue;
          } else if ($r['error_code']) {
            log::logger("/!\/!\/!\ Erreur chez Boulanger PRO : ".$r['error_code']." - ".$r['message'],$this->logFile);
            continue;
          } else {
            // Nous n'avons qu'un seul retour par produit, donc on depop simplement le premier élément (CF CdC 201904_Modif_API_V02.docx - trello : https://trello.com/c/dOIRqL4Z)
            $l = array_shift($r);
            $prix_livraison = $l['price'];
            log::logger("Prix de livraison = ".$prix_livraison,$this->logFile);
            self::manageProduitLivraisonChanges($produit, $l, $packDesactive, $produitDesactive, $this->logFile);


          }

          log::logger("\n---- Appel boulpro API price",$this->logFile);
          $r = self::APIBoulPROprice($produit['ref'],$this->logFile);

          if (!$r) {
            log::logger("Produit ref ".$produit['ref']." - ".$produit['produit']." - introuvable chez Boulanger PRO : AUCUNE REPONSE",$this->logFile);
          } else if ($r['error_code']) {
            log::logger("Produit ref ".$produit['ref']." - ".$produit['produit']." - erreur chez Boulanger PRO : ".$r['error_code']." - ".$r['message'],$this->logFile);
          } else {
            $p = $r[0];
            $prix_avec_taxe = round($p['price_tax_excl'],2)+round($p['ecotax'],2)+round($p['ecomob'],2);
            $prix_final_calcule = $prix_avec_taxe + $prix_livraison;

            log::logger("Produit ref ".$produit['ref']." - ".$produit['produit']." - trouvé chez Boulanger PRO ! ",$this->logFile);
            log::logger("Prix boulpro : ".$prix_final_calcule." VS Prix cléodis : ".$produit['prix_achat'],$this->logFile);
            log::logger("Taxe eco boulpro : ".round($p['ecotax'],2)." VS Taxe eco cléodis : ".round($produit['taxe_ecotaxe'],2),$this->logFile);
            log::logger("Taxe eco MOB boulpro : ".round($p['ecomob'],2)." VS Taxe eco MOB cléodis : ".round($produit['taxe_ecomob'],2),$this->logFile);
            // Mise a jour des taxes du produit
            // On sauve les old pour l'export excel
            $produit["old_prix_achat"] = $produit["prix_achat"];
            $produit["old_taxe_ecotaxe"] = $produit["taxe_ecotaxe"];
            $produit["old_taxe_ecomob"] = $produit["taxe_ecomob"];
            $produit["prix_achat"] = $prix_final_calcule;
            $produit["taxe_ecotaxe"] = $p['ecotax'];
            $produit["taxe_ecomob"] = $p['ecomob'];


            /* CALCUL DU LOYER */
            $prix_total = $prix_final_calcule;
            log::logger("------- Calcul du prix 1 : ".$prix_total,$this->logFile);
            $taux_annuel = self::getTaux($prix_total);
            log::logger("------- Taux annuel : ".$taux_annuel,$this->logFile);
            $loyer = $this->vpm($taux_annuel / 12, $produit['duree'], -$prix_total, 0, 1);
            log::logger("----- Loyer calculé : ".$loyer,$this->logFile);

            $produit["old_loyer"] = $produit["loyer"];
            $produit["loyer"] = $loyer;

            self::manageProduitPrixChanges($produit, $p, $packDesactive, $produitDesactive, $this->logFile);
          
          }


          ATF::pack_produit_ligne()->q->reset()
            ->where('id_produit', $produit['id_produit'])
            ->where('principal', 'oui');
          $produit_principal_ligne_de_pack = ATF::pack_produit_ligne()->select_cell();
          $countPP = count($produit_principal_ligne_de_pack);
          log::logger("Le produit est principal dans ".$countPP." packs",$this->logFile);
          if ($countPP) {
            log::logger("---- Appel boulpro API service",$this->logFile);
            $r = self::APIBoulPROService($produit['ref'],$this->logFile);
            $s = $r[0]['services'][0];

            if (count($r) != 1) {
              foreach ($produit_principal_ligne_de_pack as $ligne_de_pack_a_desactiver) {
                ATF::pack_produit()->u(array("id_pack_produit"=>$ligne_de_pack_a_desactiver['id_pack_produit'],"etat"=>"inactif"));
                $packDesactive[] = array(
                  'id' => $ligne_de_pack_a_desactiver['id_pack_produit'],
                  'raison' => "Désactivation cause Garantie"
                );
                log::logger("Pack associé n°".$ligne_de_pack_a_desactiver['id_pack_produit']." désactivé cause Garantie",$this->logFile);
              }
              
            } else {
              $ref_garantie = $s["reference"];
              log::logger("Produit ".$produit['produit']." (".$produit['ref'].") : Mise à jour de la ref garantie : ".$ref_garantie,$this->logFile);
              ATF::produit()->u(array("id_produit"=>$produit['id_produit'], "ref_garantie"=>$ref_garantie));

              $r = ATF::pack_produit()->getIdPackFromProduit($produit['id_produit'], 'actif');
              if (!count($r)) log::logger("Aucun packs associé ACTIF, donc on traite pas le reste.",$this->logFile);
              foreach (explode(",", $r) as $id_pack) {
                if (!$id_pack) continue;

                if (!$ref_garantie) {
                    // On désactive le pack associé
                    log::logger("Pack associé n°".$id_pack." désactivé cause Aucune Garantie chez Boul PRO",$this->logFile);
                    ATF::pack_produit()->u(array("id_pack_produit"=>$id_pack,"etat"=>"inactif"));
                    $packDesactive[] = array(
                      'id' => $id_pack,
                      'raison' => "Désactivation cause Garantie"
                    );
                } else {
                  ATF::pack_produit_ligne()->q->reset()
                    ->from('pack_produit_ligne','id_produit','produit','id_produit')
                    ->where('pack_produit_ligne.id_pack_produit', $id_pack)
                    ->where('produit.ref',$ref_garantie)
                    ->setLimit(1);
                  $r = ATF::pack_produit_ligne()->select_row();

                  if ($r) {
                    log::logger("Garantie trouvée ! Rien ne se passe",$this->logFile);
                  } else {
                    // On désactive le pack associé
                    log::logger("Pack associé n°".$id_pack." désactivé cause Garantie non trouvée",$this->logFile);
                    ATF::pack_produit()->u(array("id_pack_produit"=>$id_pack,"etat"=>"inactif"));
                    $packDesactive[] = array(
                      'id' => $id_pack,
                      'raison' => "Désactivation cause Garantie"
                    );
                  }

                }


              }

            }
          }
        }

        // Contrôle de COHERENCE !
        if ($produit['duree']*$produit['loyer'] < $produit['prix_achat']) {
          log::logger("Contrôle de cohérence : Si la durée * loyer < Prix d’achat HT+livraison",$this->logFile);
          log::logger(($produit['duree']*$produit['loyer'])." < ".$produit['prix_achat'],$this->logFile);
          $r = ATF::pack_produit()->getIdPackFromProduit($produit['id_produit']);
          foreach (explode(",", $r) as $id_pack) {
            if (!$id_pack) continue;
            log::logger("Pack associé n°".$id_pack." désactivé cause Garantie non trouvée",$this->logFile);
            ATF::pack_produit()->u(array("id_pack_produit"=>$id_pack,"etat"=>"inactif"));
            $packDesactive[] = array(
              'id' => $id_pack,
              'raison' => "Désactivation cause problème calcul loyer"
            );
          }          
        }



      } catch (errorATF $e) {
        ATF::db()->rollback_transaction(true);
        log::logger("ERREUR DE TRAITEMENT",$this->logFile);
        log::logger($e->getMessage(),$this->logFile);
        throw $e;
      }
      ATF::db()->commit_transaction(true);

      // echo "\n========== FIN  DU  BATCH ==========";
      // echo "\nPacks désactivésn\n";
      // print_r($packDesactive);
      // echo "\nProduits désactivés";
      // print_r($produitDesactive);
      $sendmail = false;
      $infos_mail["from"] = "Support AbsysTech <no-reply@absystech.net>";
      $infos_mail["objet"] = "[BOULANGER PRO] Batch prix - packs et produits désactivés";
      $infos_mail["recipient"] = "boulangerpro@absystech.fr,benjamin.tronquit@cleodis.com,jerome.loison@cleodis.com";
      // $infos_mail["recipient"] = "qjanon@absystech.fr";

      $infos_mail['body'] = '';
      $fpack = __TEMP_PATH__."packs_desactives.csv";
      @unlink($fpack);
      log::logger($fpack,$this->logFile);
      log::logger($packDesactive,$this->logFile);
      if (!empty($packDesactive)) {
        $filepack= fopen($fpack, "w+");
        $sendmail = true;
        foreach ($packDesactive as $k=>$pack) {
          ATF::pack_produit()->q->reset()->addAllFields('pack_produit')->where("pack_produit.id_pack_produit",$pack['id'])->setLimit(1);
          $p = ATF::pack_produit()->select_row();
          $p['raison'] = $pack['raison'];

          if ($k == 0) {
            $entetes = array_map(function ($el) { return str_replace('pack_produit.','', ATF::$usr->trans($el)); }, array_keys($p));
            fputcsv($filepack, $entetes);
            fputs("\n");
          }

          fputcsv($filepack, $p);
          fputs("\n");
        }
        fclose($filepack);
      }


      $fproduit = __TEMP_PATH__."produits_desactives.csv";
      log::logger($fproduit,$this->logFile);
      @unlink($fproduit);
      if (!empty($produitDesactive)) {
        $fileproduit= fopen($fproduit, "w+");
        $sendmail = true;
        foreach ($produitDesactive as $line) {
          ATF::produit()->q->reset()->addAllFields('produit')->where("produit.id_produit",$line['id'])->setLimit(1);
          $p = ATF::produit()->select_row();
          $p['raison'] = $line['raison'];

          if ($k == 0) {
            $entetes = array_map(function ($el) { return str_replace('pack_produit.','', ATF::$usr->trans($el)); }, array_keys($p));
            fputcsv($fileproduit, $entetes);
            fputs("\n");
          }

          fputcsv($fileproduit, $p);
          fputs("\n");
        }
        fclose($fileproduit);
      }
      log::logger(count($packDesactive)." packs désactivés",$this->logFile);
      log::logger(count($produitDesactive)." produits désactivés",$this->logFile);
      log::logger("========== FIN  DU  BATCH ==========\n",$this->logFile);
      $sendmail = true;
      if ($sendmail) {
        $mail = new mail($infos_mail);
        if (file_exists($fpack)) {
          $mail->addFile($fpack, "Packs désactivés.csv");
          // unlink($fpack);
        }
        if (file_exists($fproduit)) {
          $mail->addFile($fproduit, "Produits désactivés.csv");
          // unlink($fproduit);
        }
        $logFilePath = __ABSOLUTE_PATH__.'log/'.$this->logFile;
        if (file_exists($logFilePath)) {
          $mail->addFile($logFilePath, "Log du script ".$this->logFile.".txt");
          //unlink($fproduit);
        }
        $mail->send();
      }


    } catch (errorATF $e) {
      log::logger("ERREUR DE TRAITEMENT DU MAIL",$this->logFile);
      log::logger($e->getMessage(),$this->logFile);
      throw $e;
    }

    return true;
  }

  /**
   * Gère le traitement après avoir appelé l'API de prix, et applique les changements
   * @param  Array $produit           Tableau associatif avec les infos du produit
   * @param  Array $l                 Retour de l'API de prix
   * @param  Array &$packDesactive    Pointeur vers le tableau contenant les packs désactivés
   * @param  Array &$produitDesactive Pointeur vers le tableau contenant les produits désactivés
   */
  private function manageProduitPrixChanges ($produit, $p, &$packDesactive, &$produitDesactive) {
      $p = [
        "id_produit"=>$produit['id_produit'],
        "prix_achat"=>$p['price_tax_excl']+$p['ecotax']+$p['ecomob'],
        "taxe_ecotaxe"=>$p['ecotax'],
        "taxe_ecomob"=>$p['ecomob'],
        "loyer"=>$produit['loyer']
      ];
      log::logger("Maj du produit",$this->logFile);
      log::logger($p,$this->logFile);
      // MAJ nouveau prix sur le produit
      ATF::produit()->u($p);

  }

  /**
   * Gère le traitement après avoir appelé l'API de livraison, et applique les changements
   * @param  Array $produit           Tableau associatif avec les infos du produit
   * @param  Array $l                 Retour de l'API de livraison
   * @param  Array &$packDesactive    Pointeur vers le tableau contenant les packs désactivés
   * @param  Array &$produitDesactive Pointeur vers le tableau contenant les produits désactivés
   */
  private function manageProduitLivraisonChanges($produit, $l, &$packDesactive, &$produitDesactive) {

    // On cherche le livreur dans les fabriquant
    $q = ATF::fabriquant()->q->reset()->where("fabriquant", $l['name'], "OR", false, "LIKE");
    $fabriquant = ATF::fabriquant()->select_row();

    if (!$fabriquant) {
      log::logger("Livreur/Fabriquant ".$l['name']." non retrouvé ",$this->logFile);
      $produitDesactive[] = array(
        'id' => $produit['id_produit'],
        'raison' => "Désactivation cause livraison"
      );     
    }

    if ($fabriquant) {
      log::logger("Fabriquant trouvé depuis le retour boulpro : ".$fabriquant['fabriquant']." (".$fabriquant['id_fabriquant'].")",$this->logFile);

      $packsDuProduit = ATF::pack_produit()->getIdPackFromProduit($produit['id_produit'], "actif", true);

      if (!count($packsDuProduit)) {
        log::logger("AUCUN Packs liés où le produit est principal : On ne fait rien",$this->logFile);
      } else {      
        log::logger("Packs liés où le produit est principal : ".$packsDuProduit,$this->logFile);
        foreach (explode(",", $packsDuProduit) as $id_pack) {
          $produits = ATF::pack_produit()->getProduitFromPack($id_pack, 'actif');
          if (!count($produits)) {
            log::logger("Aucun Produit actif pour le pack",$this->logFile);
          } else {
            log::logger("Produits actif du pack ".$id_pack.": ".count($produits),$this->logFile);
            $fabriquantFound = false;
            foreach ($produits as $p) {
              log::logger("Compare fabriquant product with boulpro ".$p['produit']." (".$p['id_produit']."): ".$p['id_fabriquant']." == ".$fabriquant['id_fabriquant'],$this->logFile);
              if ($p['id_fabriquant'] == $fabriquant['id_fabriquant']) {
                $fabriquantFound = true;
              }
            }

            if ($fabriquantFound) {
              log::logger("Rien à faire, on a retrouvé le fabriquant dans un des produits du pack",$this->logFile);
            } else {
              log::logger("Pack associé n°".$id_pack." désactivé cause livreur qui ne match pas",$this->logFile);
              ATF::pack_produit()->u(array("id_pack_produit"=>$id_pack,"etat"=>"inactif"));
              $packDesactive[] = array(
                'id' => $id_pack,
                'raison' => "Désactivation cause Livraison"
              );            
            }

          }

        }            
      }

      log::logger("Mise à jour du produit avec les nouvelles infos de fabriquant",$this->logFile);
      // MAJ nouveau prix sur le produit
      ATF::produit()->u(array(
        "id_produit"=>$produit['id_produit'],
        "livreur"=>$fabriquant['id_fabriquant'],
        "frais_livraison"=>$l['price']
      ));

    }
  }

  /**
   * Récupération des informations de livraison via l'API de boulangerPro
   * @param String $ref Référence du produit
   * @author Quentin JANON <qjanon@absystech.fr>
   */
  public function APIBoulPROlivraison($ref, $logFile = NULL) {
    $params = array(
      'delivery' => [ "country" => "FR" ],
      'products' => [ [ "reference" => $ref, "quantity" => 1 ] ]
    );
    $response = $this->api->get('carriers', $params);
    $r = $response->getContent();
    if ($logFile) {
      log::logger("---- REPONSE LIVRAISON",$this->logFile);
      log::logger($r,$this->logFile);
    }
    return $r;

  }

  /**
   * Récupération des informations tarifaires d’un produit via l'API de boulangerPro
   * @param String $ref Référence du produit
   * @author Quentin JANON <qjanon@absystech.fr>
   */
  public function APIBoulPROprice($ref, $logFile = NULL) {

    $response = $this->api->get('price/'.$ref);

    $r = $response->getContent();    

    if ($logFile) {
      log::logger("---- REPONSE PRICE",$this->logFile);
      log::logger($r,$this->logFile);
    }

    return $r;

  }

  /**
   * Récupération des services reliés à un produit via l'API de boulangerPro
   * @param String $ref Référence du produit
   * @author Quentin JANON <qjanon@absystech.fr>
   */
  public function APIBoulPROservice($ref, $logFile = NULL) {

    $response = $this->api->get('services/'.$ref);

    $r = $response->getContent();    

    if ($logFile) {
      log::logger("API Get services/".$ref,$this->logFile);
      log::logger("---- REPONSE SERVICES",$this->logFile);
      log::logger($r,$this->logFile);
    }
    return $r;

  }

  /**
   * Calcul financier, formule afin de déterminer les loyers
   */
  public function vpm($taux, $npm, $va, $vc = 0, $type = 0){
    if(!is_numeric($taux) || !is_numeric($npm) || !is_numeric($va) || !is_numeric($vc) || !is_numeric($type)) return false;

    if($type > 1|| $type < 0) return false;

    $tauxAct = pow(1 + $taux, -$npm);
    if((1 - $tauxAct) == 0) return 0;

    $vpm = ( ($va + $vc * $tauxAct) * $taux / (1 - $tauxAct) ) / (1 + $taux * $type);

    return -$vpm;
  }

  /**
   * Calcul le taux selon la règle émise par Benjamin
   * @param  float $prix Prix
   * @return float       Valeur tu taux
   */
  public function getTaux($prix){
    if ($prix < 500) $taux = 12/100;
    else if ($prix < 1500) $taux = 9.5/100;
    else if ($prix < 3000) $taux = 9/100;
    else $taux = 8/100;

    return $taux;
  }
}
?>