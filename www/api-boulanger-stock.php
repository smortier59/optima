<?php
  error_reporting(E_ALL);

  include(dirname(__FILE__)."/../global.inc.php");

  ATF::$codename = "cleodis";
  if ($_GET['code'] != "V1WhoJULNkngC3uA9NUi") die('Accès restreint');

  require __ABSOLUTE_PATH__.'includes/cleodis/boulangerpro/ApiBoulangerProV2.php';

  if (!defined("__API_BOULANGER_CLIENT__")) die('PAS DE CONFIG 1');
  if (!defined("__API_BOULANGER_SECRET__")) die('PAS DE CONFIG 2');
  if (!defined("__API_BOULANGER_HOST__")) die('PAS DE CONFIG 3');

  ATF::societe()->q->reset()->where("societe", "BOULANGER PRO", "AND", false, "LIKE");
  $frs = ATF::societe()->select_row();
  $id_frs = $frs['id_societe'];
  echo "Société : ".$frs['societe']." (siret: ".$frs['siret'].") / ID = ".$id_frs."<br><br>";


  ATF::produit()->q->reset()->where('id_fournisseur', $id_frs);

  $catalogueBoulProActif = ATF::produit()->sa();

  if ($catalogueBoulProActif) {

    foreach ($catalogueBoulProActif as $k=>$produit) {
      echo "==================================================<br>";
      $api = new ApiBoulangerProV2(__API_BOULANGER_CLIENT__,__API_BOULANGER_SECRET__,__API_BOULANGER_HOST__);

      $response = $api->get('quantity', [ 'reference' => $produit['ref'] ]);

      $content = $response->getContent();
      $content["reference"] = $produit['ref'];
      $code = $response->getCode();
      echo "Réponse boulanger PRO<br>";
      echo "Code : ".$code."<br>";
      print_r($content);
      echo "<br>";
    }
  } else {
    echo "Pas de produit identifié...<br>";
  }
?>