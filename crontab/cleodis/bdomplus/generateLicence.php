<?php
define("__BYPASS__",true);
$typeLicence = $_SERVER["argv"][1];
$number = $_SERVER["argv"][2];

$_SERVER["argv"][1] = "bdomplus";

include(dirname(__FILE__)."/../../../global.inc.php");
ATF::define("tracabilite",false);

echo "Type de licence => ".$typeLicence."\n";
echo "Nb de licence à générer => ".$number."\n";

$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
$charactersLength = strlen($characters);

if($typeLicence == 1 || $typeLicence == 2){

	for($nb=0;$nb<$number;$nb++){
		$licence = "";
		for($i=0;$i<29;$i++){
			if($i == 5 || $i == 11 || $i == 17 || $i == 23){
				$licence .= "-";
			} else{
				$licence .= $characters[rand(0, $charactersLength - 1)];
			}
		}

		$to_insert = array("part_1" => substr($licence, 0, -4),
							"part_2" => substr($licence, -4),
							"id_licence_type" => $typeLicence
						);

		ATF::licence()->i($to_insert);
	}
}

if($typeLicence == 3 || $typeLicence == 4){
	for($nb=0;$nb<$number;$nb++){
		$licence = "";
		for($i=0;$i<26;$i++){
			$licence .= $characters[rand(0, $charactersLength - 1)];
		}
		$to_insert = array("part_1" => substr($licence, 0, -4),
							"part_2" => substr($licence, -4),
							"id_licence_type" => $typeLicence
						);

		ATF::licence()->i($to_insert);
	}
}

/*
licence Office 2-2-2-2-5car
Norton 25 car*/