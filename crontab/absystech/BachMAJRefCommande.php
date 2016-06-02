<?
define("__BYPASS__",true);
include(dirname(__FILE__)."/../../global.inc.php");
/*
ATF::commande_ligne()->q->reset()->addField("COUNT(*)","nb_doublon")
								 ->addField("commande_ligne.ref")
								 ->addField("commande_ligne.id_commande")
								 ->addGroup("commande_ligne.ref")
								 ->addGroup("commande_ligne.id_commande")
								 //->where("id_compte_absystech",1,"AND",false,"<>")
								 ->addHavingCondition("nb_doublon",1,"OR",false,">")
								 ->addOrder("nb_doublon" , "DESC");
$res = ATF::commande_ligne()->select_all();
log::logger($res , "mfleurquin");
foreach ($res as $key => $value) {
	if($value["commande_ligne.ref"]){
		ATF::commande_ligne()->q->reset()->where("ref", $value["commande_ligne.ref"])
								     ->where("id_commande" , $value["commande_ligne.id_commande_fk"]);
		$result = ATF::commande_ligne()->select_all();
		$i = 1;
		foreach ($result as $k => $v) {
			ATF::commande_ligne()->u(array("id_commande_ligne"=> $v["id_commande_ligne"] , "ref" =>$v["ref"]."-".$i));
			log::logger("Modif de la ligne commande_ligne ".$v["id_commande_ligne"]."  AVANT : ".$v["ref"]."  APRES : ".$v["ref"]."-".$i , "mfleurquin");
			$i++;	
		}
	}else{
		log::logger("REF NULLE sur la commande ".$value["commande_ligne.id_commande_fk"] , "mfleurquin");			
	}	
}
*/


$q = "SELECT *, COUNT(*) as nb_doubon  FROM devis_ligne GROUP BY ref,id_devis HAVING nb_doubon > 1";

$res = ATF::db()->sql2array($q);	  

//$res = ATF::devis_ligne()->select_all();
log::logger($res , "mfleurquin");

foreach ($res as $key => $value) {
	if($value["ref"]){
		ATF::devis_ligne()->q->reset()->where("ref", $value["ref"])
								     ->where("id_devis" , $value["id_devis"]);
		$result = ATF::devis_ligne()->select_all();
		$i = 1;
		foreach ($result as $k => $v) {
			ATF::devis_ligne()->u(array("id_devis_ligne"=> $v["id_devis_ligne"] , "ref" =>$v["ref"]."-".$i));
			log::logger("Modif de la ligne devis_ligne ".$v["id_devis_ligne"]."  AVANT : ".$v["ref"]."  APRES : ".$v["ref"]."-".$i , "mfleurquin");
			$i++;	
		}
	}else{
		log::logger("REF NULLE sur le devis ".ATF::devis_ligne()->select($value["devis_ligne.id_devis_ligne"] , "id_devis") , "mfleurquin");			
	}	
}




?>