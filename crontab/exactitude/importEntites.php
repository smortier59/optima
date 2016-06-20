<?
define("__BYPASS__",true);
$_SERVER["argv"][1] = "exactitude";
include(dirname(__FILE__)."/../../global.inc.php");


$fichier = dirname(__FILE__)."/importEntites.csv";

//$path = $pathNOK;
echo "\n\n******************************************************************\n";
echo "START AT : ".date("d-m-Y H:i:s")." - Filename = ".$fichier."\n\n";

$fic = fopen($fichier, 'rb');


$entete = fgetcsv($fic);
$entete = explode(",",$entete[0]);

$ct=0;
while ($l = fgetcsv($fic)) {
  $l = array_map(function($val) { return str_replace("_x000D_","",$val); }, $l);

  $toInsert = array(
    "naf"=>substr(0,5,$l[0]),
    "societe"=>$l[1],
    "nom_commercial"=>$l[2],
    "adresse"=>$l[3],
    "cp"=>substr(0,5,str_replace(" ","",$l[4])),
    "ville"=>$l[5],
    "tel"=>str_replace(" ","",$l[6]),
    "activite"=>$l[7],
    "relation"=>$l[8],
  );

  unset($suivis);
  if ($l[9]) {

    $tmp = explode("_x000D_",$l[9]);
    foreach ($tmp as $k=>$i) {
      if (!$i) continue;
      preg_match("`([0-9]{2}/[0-9]{2}/[0-9]{2})(.*)`",$i,$m);

      $d = date("Y-m-d",strtotime($m[1]));
      if ($d && $d!="1970-01-01") {
        $suivis[] = array("texte"=>$m[2],"date"=>$d,"id_user"=>1);
      }
    }

  }

  try {
    $id_societe = ATF::societe()->i($toInsert);
    foreach ($suivis as $k=>$i) {
      $i['id_societe'] = $id_societe;
      ATF::suivi()->i($i);
    }
    echo "DONE !\n";

  } catch (errorATF $e) {
    if ($e->getErrno()==1062) continue;
    print_r($toInsert);
    die("ERREUR  => ".$e->getErrno()." - ".$e->getMessage()."\n\n");
  }
}
echo "\n\nEND AT : ".date("d-m-Y H:i:s");
echo "\n\n******************************************************************\n";
