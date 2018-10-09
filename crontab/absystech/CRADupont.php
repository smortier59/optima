<?
/** Sors un CRA pour Dupont restauration
* @author Quentin JANON <qjanon@absystech.fr>
*/
define("__BYPASS__",true);
$_SERVER["argv"][1] = "absystech";
include(dirname(__FILE__)."/../../global.inc.php");

// Récupération des tickets ouvert de Dupont
ATF::hotline()->q->reset()->where('id_societe',1387)->where('etat','fixing');
$ths = ATF::hotline()->sa();
$lignes = array();
echo "\n---- ".count($ths)." tickets hotline trouvé\n";


foreach ($ths as $k=>$th) {
  echo "Ticket #".$th['id_hotline']." - ".$th['hotline']."\n";
  ATF::hotline_interaction()->q->reset()
                            ->where("id_hotline",$th['id_hotline'])
                            ->where('id_user',12/* Quentin */)
                            ->where('id_user',63/* Anthony */)
                            ->where('id_user',55/* Morgan */)
                            ->where('visible','oui');

  $th_is = ATF::hotline_interaction()->sa();
  echo "\n-------- ".count($th_is)." interactions trouvées\n";
  foreach ($th_is as $k=>$th_i) {
    echo "    Interaction #".$th_i['id_hotline_interaction']."\n";

    if (preg_match("/\*{3}SUR SITE\*{3}/",$th_i['detail']) || preg_match("/\*{3} SUR SITE \*{3}/",$th_i['detail'])) {
      $place = "Dupont Restauration";
      $th_i['detail'] = str_replace("***SUR SITE***","",$th_i['detail']);
      $th_i['detail'] = str_replace("*** SUR SITE ***","",$th_i['detail']);
    } else if (preg_match("/\*{3} ABSYSTECH \*{3}/",$th_i['detail']) || preg_match("/\*{3}ABSYSTECH\*{3}/",$th_i['detail'])) {
      $place = "Absystech";
      $th_i['detail'] = str_replace("***ABSYSTECH***","",$th_i['detail']);
      $th_i['detail'] = str_replace("*** ABSYSTECH ***","",$th_i['detail']);
    } else {
      echo "\nAUCUNE INDICATION DE LOCATION\n";
    }

    $lignes[] = array(
      date("d-m-Y",strtotime($th_i['date'])),
      ATF::user()->nom($th_i['id_user']),
      $th['hotline'],
      $place,
      strip_tags(str_replace("<br>","\n\r",$th_i['detail']),"<br>"),
      str_replace(".",",",$th_i['credit_presta'])
    );

  }

}
$file = fopen("/home/qjanon/CRA_dupont.csv", "w+");
fputs($file, "Date,Qui,Projet,Site,Description,Temps(heure)\n");

foreach ($lignes as $line) {
  fputcsv($file, $line);
  fputs("\n");
}
fclose($file);
