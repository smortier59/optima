<?php
include_once dirname(__FILE__)."/../libs/ATF/ATF.inc.php";
if ($_REQUEST["sess"]) {
	ATF::define("sessionId",$_REQUEST["sess"]);
}

try{
	include(dirname(__FILE__)."/../global.inc.php");
	switch ($_REQUEST["commande"]) {
			case "marge":
				die(ATF::affaire()->getMargeTotaleDepuisDebutAnnee());

			case "pipe":
				die(ATF::devis()->getTotalPipePondere());

			case "activity":
				$query = "SELECT
				SUM(IF(a.website_codename!='absystech',1,0)) as client,
				COUNT(*) total
				FROM optima.activity a
				WHERE a.activity>DATE_ADD(NOW(),INTERVAL -5 MINUTE)";
				$result = ATF::db()->sql2array($query);
				die($result[0]["client"]."/".$result[0]["total"]);

			// Hotlines récentes
			case "h":
				if (ATF::$usr->getID()) {
					$data=ATF::hotline()->getRecentForMobile();
					die(json_encode($data));
				}
				break;

			// Hotline interactions
			case "hi":
				if (ATF::$usr->getID()) {
					if ($_REQUEST["id"]) {
						ATF::hotline()->q->reset();
						$data=ATF::hotline()->getInteractionsForMobile($_REQUEST["id"]);
					}
					die(json_encode($data));
				}
				break;

			// Société à proximité
			case "proximity":
				if (ATF::$usr->getID()) {	
					$_REQUEST["nw"] = explode(",",$_REQUEST["nw"]);
					$_REQUEST["se"] = explode(",",$_REQUEST["se"]);
					$x = ($_REQUEST["nw"][0]+$_REQUEST["se"][0])/2;
					$y = ($_REQUEST["nw"][1]+$_REQUEST["se"][1])/2;
					$data = ATF::societe()->getProximiteFromXY($x,$y);
					die(json_encode($data));
				}
				break;

			// Sociétés
			case "s":
				if (ATF::$usr->getID()) {
					ATF::societe()->q->reset()->addField("societe")->addField("id_societe");
					$data=ATF::societe()->select_all();
					foreach ($data as $k => $i) {
						$societe = substr(ucfirst(util::removeAccents(trim($i["societe"]))),0,1);
						if (!$societe) {
							$societe="-";
						}
						$data[$k]["indexAlpha"]=$societe;
					}
					die(json_encode($data));
				}
				break;
	}
	
	die(json_encode(array("nosession"=>true)));
}catch(errorATF $e){
	log::logger("ERROR : ".$e->getMessage(),'qjanon');
//      $e->setError();
}
?>