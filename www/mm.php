<?php
$_SERVER["argv"][1] = "absystech";
include(dirname(__FILE__)."/../global.inc.php");

function toTime($h){
 $m=round(($h-floor($h))*60);
 return floor($h).'h'.str_pad($m, 2, "0", STR_PAD_LEFT);
}
function getWorkingDays($startDate,$endDate,$holidays){
    $endDate = strtotime($endDate);
    $startDate = strtotime($startDate);
    $days = ($endDate - $startDate) / 86400 + 1;
    $no_full_weeks = floor($days / 7);
    $no_remaining_days = fmod($days, 7);
    $the_first_day_of_week = date("N", $startDate);
    $the_last_day_of_week = date("N", $endDate);
    if ($the_first_day_of_week <= $the_last_day_of_week) {
        if ($the_first_day_of_week <= 6 && 6 <= $the_last_day_of_week) $no_remaining_days--;
        if ($the_first_day_of_week <= 7 && 7 <= $the_last_day_of_week) $no_remaining_days--;
    } else {
        if ($the_first_day_of_week == 7) {
            $no_remaining_days--;
            if ($the_last_day_of_week == 6)
                $no_remaining_days--;
        } else
            $no_remaining_days -= 2;
    }
   $workingDays = $no_full_weeks * 5;
    if ($no_remaining_days > 0 )
      $workingDays += $no_remaining_days;
    foreach($holidays as $holiday){
        $time_stamp=strtotime($holiday);
        if ($startDate <= $time_stamp && $time_stamp <= $endDate && date("N",$time_stamp) != 6 && date("N",$time_stamp) != 7)
            $workingDays--;
    }
    return $workingDays;
}
$holidays=array('2019-01-01','2019-04-22','2019-05-01','2019-05-08','2019-05-30','2019-06-10','2019-07-14','2019-08-15','2019-11-01','2019-11-11','2019-12-25','2020-01-01','2020-04-13','2020-05-01','2020-05-08','2020-05-21','2020-06-01',
'2020-07-14','2020-08-15','2020-11-01','2020-11-11','2020-12-25','2021-01-01','2021-04-05','2021-05-01','2021-05-08','2021-05-13','2021-05-24','2021-07-14','2021-08-15','2021-11-01','2021-11-11','2021-12-25','2022-01-01','2022-04-18','20
22-05-01','2022-05-08','2022-05-26','2022-06-06','2022-07-14','2022-08-15','2022-11-01','2022-11-11','2022-12-25','2023-01-01','2023-04-10','2023-05-01','2023-05-08','2023-05-18','2023-05-29','2023-07-14','2023-08-15','2023-11-01','2023-
11-11','2023-12-25','2024-01-01','2024-04-01','2024-05-01','2024-05-08','2024-05-09','2024-05-20','2024-07-14','2024-08-15','2024-11-01','2024-11-11','2024-12-25','2025-01-01','2025-04-21','2025-05-01','2025-05-08','2025-05-29','2025-06-
09','2025-07-14','2025-08-15','2025-11-01','2025-11-11','2025-12-25','2026-01-01','2026-04-06','2026-05-01','2026-05-08','2026-05-14','2026-05-25','2026-07-14','2026-08-15','2026-11-01','2026-11-11','2026-12-25','2027-01-01','2027-03-29'
,'2027-05-01','2027-05-06','2027-05-08','2027-05-17','2027-07-14','2027-08-15','2027-11-01','2027-11-11','2027-12-25','2028-01-01','2028-04-17','2028-05-01','2028-05-08','2028-05-25','2028-06-05','2028-07-14','2028-08-15','2028-11-01','2
028-11-11','2028-12-25','2029-01-01','2029-04-02','2029-05-01','2029-05-08','2029-05-10','2029-05-21','2029-07-14','2029-08-15','2029-11-01','2029-11-11','2029-12-25','2030-01-01','2030-04-22','2030-05-01','2030-05-08','2030-05-30','2030
-06-10','2030-07-14','2030-08-15','2030-11-01','2030-11-11','2030-12-25');
$nbWorkingDays = getWorkingDays(date("Y-m-01"),date("Y-m-d"),$holidays);

function mb_str_pad(  $input,  $pad_length,  $pad_string=" ",  $pad_style=STR_PAD_RIGHT,  $encoding="UTF-8"){
    return str_pad(      $input,      strlen($input)-mb_strlen($input,$encoding)+$pad_length,      $pad_string,      $pad_style);
}
$o=array();

switch ($_POST["command"]){
case "/podium":
$infos = ATF::hotline()->_requetebyUserParMois(array("moment"=>$_POST["text"]?$_POST["text"]:"now"),array());

//print_r($infos);die;

ATF::define_db("db","optima_att");
foreach ($infos["att"]["dataset"]["oui"]["set"] as $k => $v) {
if (!$k) continue;
 $infos["att"]["categories"]["category"][$k]["label"] = ATF::user()->select($k,'prenom').' '.ATF::user()->select($k,'nom');
 $oui[$infos["att"]["categories"]["category"][$k]["label"]]+=$v["value"];
 $temps_partiel[$infos["att"]["categories"]["category"][$k]["label"]]=(float)ATF::user()->select($k,'temps_partiel');
 $total[$infos["att"]["categories"]["category"][$k]["label"]]+=($v["value"]+$infos["att"]["dataset"]["non"]["set"][$k]["value"]+$infos["att"]["dataset"]["conges"]["set"][$k]["value"])*(2-$temps_partiel[$infos["att"]["categories"]["catego
ry"][$k]["label"]]);
 $data[$infos["att"]["categories"]["category"][$k]["label"]]["ATT"]+=($v["value"]+$infos["att"]["dataset"]["non"]["set"][$k]["value"]+$infos["att"]["dataset"]["conges"]["set"][$k]["value"]);
 $data[$infos["att"]["categories"]["category"][$k]["label"]]["temps_partiel"]=$temps_partiel[$infos["att"]["categories"]["category"][$k]["label"]];
 $data[$infos["att"]["categories"]["category"][$k]["label"]]["oui"]+=$v["value"];
 $data[$infos["att"]["categories"]["category"][$k]["label"]]["non"]+=$infos["att"]["dataset"]["non"]["set"][$k]["value"];
 $data[$infos["att"]["categories"]["category"][$k]["label"]]["conges"]+=$infos["att"]["dataset"]["conges"]["set"][$k]["value"];
 $data[$infos["att"]["categories"]["category"][$k]["label"]]["ranking"]=$k!=1;
}

ATF::define_db("db","optima_absystech");
foreach ($infos["at"]["dataset"]["oui"]["set"] as $k => $v) {
 if (!$k) continue;
 $infos["at"]["categories"]["category"][$k]["label"] = ATF::user()->select($k,'prenom').' '.ATF::user()->select($k,'nom');
 $oui[$infos["at"]["categories"]["category"][$k]["label"]]+=$v["value"];
 $temps_partiel[$infos["at"]["categories"]["category"][$k]["label"]]=(float)ATF::user()->select($k,'temps_partiel');
 $total[$infos["at"]["categories"]["category"][$k]["label"]]+=($v["value"]+$infos["at"]["dataset"]["non"]["set"][$k]["value"]+$infos["at"]["dataset"]["conges"]["set"][$k]["value"])*(2-$temps_partiel[$infos["at"]["categories"]["category"]
[$k]["label"]]);
 $data[$infos["at"]["categories"]["category"][$k]["label"]]["AT"]+=($v["value"]+$infos["at"]["dataset"]["non"]["set"][$k]["value"]+$infos["at"]["dataset"]["conges"]["set"][$k]["value"]);
 $data[$infos["at"]["categories"]["category"][$k]["label"]]["temps_partiel"]=$temps_partiel[$infos["at"]["categories"]["category"][$k]["label"]];
 $data[$infos["at"]["categories"]["category"][$k]["label"]]["oui"]+=$v["value"];
 $data[$infos["at"]["categories"]["category"][$k]["label"]]["non"]+=$infos["at"]["dataset"]["non"]["set"][$k]["value"];
 $data[$infos["at"]["categories"]["category"][$k]["label"]]["conges"]+=$infos["at"]["dataset"]["conges"]["set"][$k]["value"];
 $data[$infos["at"]["categories"]["category"][$k]["label"]]["ranking"]=$k!=1;
}
array_multisort($total, SORT_DESC, $data);

$place=1;

$objectif_set = $objectif2_set = false;
$objectif =  7*$nbWorkingDays; // heures
$objectif2 = 7*$nbWorkingDays*.8; // heures
foreach ($data as $k => $v) {
$key++;
$tot = $v["oui"]+$v["non"]+$v["conges"];
if (!$_POST["text"] && $tot<$objectif && !$objectif_set) { $objectif_set=true;
 $users[]="||↑ sont surpuissants ↑|".toTime($objectif)."|||||Objectif temps plein|";
}
if (!$_POST["text"] && $tot<$objectif2 && !$objectif2_set) { $objectif2_set=true;
 $users[]="||↓ sont en retard ↓|".toTime($objectif2)."|||||Limite 80% de production|";
}
$icon="";
if ($v["ranking"])
switch($place) {
case 1: $icon=":trophy::1st_place_medal:";break;
case 2: $icon=":2nd_place_medal:";break;
case 3: $icon=":3rd_place_medal:";break;
}
$users[] = "|".($v["ranking"] ? $place.($place==1?'er':'ème') : "")."|".
($icon?$icon." ":($key==count($data) ? ":snail: " : "")).trim($k)."|".
"**".toTime($tot)."**"."|".
toTime($v["oui"])."|".
toTime($v["non"])."|".
($v["conges"] ? toTime($v["conges"]) : "")."|".
($v["ATT"] ? toTime($v["ATT"]) : "")."|".
($v["temps_partiel"]<1?' ('.toTime($tot).' @ '.($v["temps_partiel"]*100).'% ≈ '.toTime($total[$k]).' @ 100%)':'')."|";
if ($v["ranking"]) $place++;
$_["tot"]+=$tot;
$_["oui"]+=$v["oui"];
$_["non"]+=$v["non"];
$_["conges"]+=$v["conges"];
$_["ATT"]+=$v["ATT"];
}
$users[]="|||".toTime($_["tot"])."|".toTime($_["oui"])."|".toTime($_["non"])."|".toTime($_["conges"])."|".toTime($_["ATT"])."||";

$o["response_type"] = "in_channel";
$o["text"] =
//"```\n".
"".//---#### Classement instantané du pointage de **".($_POST["text"]?$_POST["text"]:"ce mois ci (".date("Y-m").")")."** indépendamment du type de prestation :
"|Pointage|".($_POST["text"]?$_POST["text"]:"ce mois ci (".date("Y-m").")")."|Total|Fact.|Maint.|Congé|dont ATT||\n".
"|:-|-:|-:|-:|-:|-:|-:|:-|\n".
implode("\n",$users).
""
//"\n```"
;
//$o["text"] = "hop:".json_encode($_POST);//"Classement instantané du pointage ce mois ci indépendamment du type de prestation :\n".implode("\n",$users);
$o["username"] = ucfirst(substr($_POST["command"],1))." (".$_POST["user_name"].")";
//$o["icon_url"] = "

break;
}
header('Content-type: application/json');
echo json_encode($o);
