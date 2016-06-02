<?
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../global.inc.php");

$sans_objet=array(
			"ACCESSOIRE",
			"ANALYSEUR GAZ",
			"CONSOMMABLE",
			"FIREWALL",
			"GARANTIE",
			"LIGNE",
			"LOGICIEL",
			"PRESTATION",
			"RESEAU",
			"VIEWER"
			);

print_r("\n ---------------------SANS OBJET------------------------------- \n ");

foreach($sans_objet as $key=>$item){
	$query="SELECT *
			FROM `produit`
			WHERE `produit` LIKE '%".$item."%'";
	
	$produit=ATF::db()->sql2array($query);
	$nb=0;
	foreach($produit as $k=>$i){
		ATF::produit()->u(array("id_produit"=>$i["id_produit"],"type"=>"sans_objet"));
		$nb++;
	}
	print_r("\n".$nb."  ".$item);
}


$fixe=array(
			"AUDIOMETRE",
			"AUTOCOM",
			"AUTOREFRACTO",
			"BAIE",
			"BALANCE",
			"BLADE",
			"BORNE",
			"CENTREUR",
			"CHR",
			"CLIENT LEGER",
			"COMPRES",
			"COPIEUR",
			"DECODEUR",
			"DISQUE DUR",
			"ECRAN",
			"ELT ACTIF",
			"ENCEINTE",
			"EQUILIBREUSE",
			"FAX",
			"HOME CINEMA",
			"IMPRIMANTE",
			"LECTEUR",
			"LIBRAIRIE",
			"MEULEUSE",
			"MULTIFONCTION",
			"ONDULEUR",
			"PC",
			"PHOTOCOPIEUR",
			"POINTEUSE",
			"PONT",
			"POSTE",
			"PROJECT",
			"ROUTEUR",
			"SAUVEGARDE",
			"SCANNER",
			"SERVEUR",
			"STANDARD",
			"STOCKAGE",
			"TELECOPIEUR",
			"TERMINAL",
			"TRACEUR",
			"TV",
			"UC",
			"VIDEOPROJECTEUR",
			"WORKSTATION"
			);

print_r("\n ---------------------FIXE------------------------------- \n ");
		
foreach($fixe as $key=>$item){
	$query="SELECT *
			FROM `produit`
			WHERE `produit` LIKE '%".$item."%'";
	
	$produit=ATF::db()->sql2array($query);
	$nb=0;
	foreach($produit as $k=>$i){
		ATF::produit()->u(array("id_produit"=>$i["id_produit"],"type"=>"fixe"));
		$nb++;
	}
	print_r("\n".$nb."  ".$item);
}

$portable=array(
			"APPAREIL PHOTO NUMERIQUE",
			"CAMESCOPE",
			"DEMONTE-PNEUS",
			"PHOTO AP",
			"POCKET PC",
			"PORTABLE"
			);

		
print_r("\n ---------------------PORTABLE------------------------------- \n ");
foreach($portable as $key=>$item){
	$query="SELECT *
			FROM `produit`
			WHERE `produit` LIKE '%".$item."%'";
	
	$produit=ATF::db()->sql2array($query);
	$nb=0;
	foreach($produit as $k=>$i){
		ATF::produit()->u(array("id_produit"=>$i["id_produit"],"type"=>"portable"));
		$nb++;
	}
	print_r("\n".$nb."  ".$item);
}
?>