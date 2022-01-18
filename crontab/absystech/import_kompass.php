<?
define("__BYPASS__",true);
$_SERVER["argv"][1] = "absystech";
include(dirname(__FILE__)."/../../global.inc.php");

ATF::define("tracabilite",false);

ATF::$usr->set('id_user',72);
ATF::$usr->set('id_agence',1);

/*
$q= "SELECT * FROM TABLE_160";
$data = ATF::db()->sql2array($q);

$i =1;
foreach ($data as $key => $value) {
	if($key != 0){
		$soc = array();

		$soc["societe"] = $value["COL 1"];
		$soc["siret"] = $value["COL 2"];
		$soc["reference_tva"] = $value["COL 3"];
		$soc["nom_commercial"] = $value["COL 5"];
		$soc["adresse"] = $value["COL 6"];
		$soc["adresse_2"] = $value["COL 7"];
		$soc["cp"] = $value["COL 8"];
		$soc["ville"] = $value["COL 9"];
		$soc["tel"] = str_replace("+33", "03", $value["COL 12"]);
		$soc["fax"] = $value["COL 13"];
		$soc["email"] = $value["COL 14"];
		$soc["web"] = $value["COL 15"];
		$soc["nb_employe"] = $value["COL 17"];
		$soc["naf"] = $value["COL 18"];
		$soc["relation"] = "suspect";
		$soc["id_commercial"] = 72; //Jacques

		ATF::societe()->q->reset()->where("siret", $soc["siret"],"OR","test")
								  ->where("societe", $soc["societe"],"OR","test");

		if(! ATF::societe()->select_row()){
			try{
				ATF::db()->begin_transaction();
				ATF::societe()->i($soc);
				$i++;
			}catch(errorATF $e){
				echo $e->getMessage()."\n\n";
				ATF::db()->rollback_transaction();
			}
			ATF::db()->commit_transaction();

		}else{
			echo  $soc["societe"]." existe\n";
		}
	}
}*/
ATF::societe()->q->reset()->whereIsNull("ref");
foreach (ATF::societe()->select_all() as $key => $value) {
	$soc['ref']=ATF::societe()->create_ref($s);
	$soc["divers_5"]=substr(md5(time()),0,4); // Mot de passe hotline
	$soc["mdp_client"]=util::generateRandWord(9);
	$soc["mdp_absystech"]=util::generateRandWord(9);
	$soc["id_societe"] = $value["id_societe"];

	ATF::societe()->u($soc);
}

