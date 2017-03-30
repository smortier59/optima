<?
/** Sors un CRA pour Dupont restauration
* @author Quentin JANON <qjanon@absystech.fr>
*/
define("__BYPASS__",true);
$_SERVER["argv"][1] = "absystech";
include(dirname(__FILE__)."/../../global.inc.php");

ATF::societe()->q->reset()
              ->addField('societe.id_societe','id')
              ->addField('societe.societe','soc')
              ->addField('GROUP_CONCAT(DISTINCT(devis_ligne.id_compte_absystech))','dms')
              ->from('societe','id_societe','devis','id_societe')
              ->from('devis','id_devis','devis_ligne','id_devis')
              ->addGroup('societe.id_societe');
              // ->where('societe.id_societe',3);
// ATF::societe()->q->setToString();
$soc = ATF::societe()->sa();
// ATF::societe()->q->unsetToString();

try {
  foreach ($soc as $k=>$s) {
    $domaines = ','.$s['dms'].',';
    echo "SOCIETE ".$s['soc']." DOMAINE ".$domaines."\n";

    if (strpos($domaines,',1,') !== false) { // 1 VENTES DE MARCHANDISES : Hardware 2
      ATF::societe_domaine()->q->reset()->where('id_societe',$s['id'])->where('id_domaine',2)->setCountOnly();
      $f = ATF::societe_domaine()->sa();
      echo "VENTES DE MARCHANDISES : Hardware 2 = ".$f."\n\n";
      if (!$f) ATF::societe_domaine()->i(array("id_societe"=>$s['id'], 'id_domaine'=>2));
    }
    if (strpos($domaines,',2,') !== false) { // 2 VENTE DE DOMAINES : Web service4
      ATF::societe_domaine()->q->reset()->where('id_societe',$s['id'])->where('id_domaine',4)->setCountOnly();
      $f = ATF::societe_domaine()->sa();
      echo "VENTE DE DOMAINES : Web service4 = ".$f."\n\n";
      if (!$f) ATF::societe_domaine()->i(array("id_societe"=>$s['id'], 'id_domaine'=>4));
    }
    if (strpos($domaines,',3,') !== false) { // 3 VENTE ABONNEMENT TELEPHONIQUE : telecom 5
      ATF::societe_domaine()->q->reset()->where('id_societe',$s['id'])->where('id_domaine',5)->setCountOnly();
      $f = ATF::societe_domaine()->sa();
      echo "VENTE ABONNEMENT TELEPHONIQUE : telecom 5 = ".$f."\n\n";
      if (!$f) ATF::societe_domaine()->i(array("id_societe"=>$s['id'], 'id_domaine'=>5));
    }
    if (strpos($domaines,',5,') !== false) { // 5 PRESTATIONS DE DEVELOPPEMENT : Web agency 3
      ATF::societe_domaine()->q->reset()->where('id_societe',$s['id'])->where('id_domaine',3)->setCountOnly();
      $f = ATF::societe_domaine()->sa();
      echo "PRESTATIONS DE DEVELOPPEMENT : Web agency 3 = ".$f."\n\n";
      if (!$f) ATF::societe_domaine()->i(array("id_societe"=>$s['id'], 'id_domaine'=>3));
    }
    if (strpos($domaines,',6,') !== false) { // 6 LOCATION HEBERGEMENT : web service 4
      ATF::societe_domaine()->q->reset()->where('id_societe',$s['id'])->where('id_domaine',4)->setCountOnly();
      $f = ATF::societe_domaine()->sa();
      echo "LOCATION HEBERGEMENT : web service 4 = ".$f."\n\n";
      if (!$f) ATF::societe_domaine()->i(array("id_societe"=>$s['id'], 'id_domaine'=>4));
    }
    if (strpos($domaines,',7,') !== false) { // 7 SUPPORT : support 1
      ATF::societe_domaine()->q->reset()->where('id_societe',$s['id'])->where('id_domaine',1)->setCountOnly();
      $f = ATF::societe_domaine()->sa();
      echo "SUPPORT : support 1 = ".$f."\n\n";
      if (!$f) ATF::societe_domaine()->i(array("id_societe"=>$s['id'], 'id_domaine'=>1));
    }
    if (strpos($domaines,',13,') !== false) { // 13 VENTES MARCHANDISES - ABO TACITE : hardware 2
      ATF::societe_domaine()->q->reset()->where('id_societe',$s['id'])->where('id_domaine',2)->setCountOnly();
      $f = ATF::societe_domaine()->sa();
      echo "VENTES MARCHANDISES - ABO TACITE : hardware 2 = ".$f."\n\n";
      if (!$f) ATF::societe_domaine()->i(array("id_societe"=>$s['id'], 'id_domaine'=>2));
    }
    if (strpos($domaines,',14,') !== false) { // 14 VENTES MARCHANDISES - ABO RENOUV : hardware 2
      ATF::societe_domaine()->q->reset()->where('id_societe',$s['id'])->where('id_domaine',2)->setCountOnly();
      $f = ATF::societe_domaine()->sa();
      echo "VENTES MARCHANDISES - ABO RENOUV : hardware 2 = ".$f."\n\n";
      if (!$f) ATF::societe_domaine()->i(array("id_societe"=>$s['id'], 'id_domaine'=>2));
    }
  }
} catch (errorSQL $e) {

  echo "\n\n CODE = ".$e->getCode()."\n\n";
  throw $e;
}


// 2 VENTE DE DOMAINES : Web service4
// 4 PRESTATIONS
// 8 FRAIS DE PORT
// 9 INCONNUE
// 10 COUT COPIE
// 11 PRESTATIONS - ABO TACITE
// 12 PRESTATIONS - ABO RENOUV
