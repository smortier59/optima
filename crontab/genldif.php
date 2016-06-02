<?
define("__BYPASS__",true);
include("../global.inc.php");

foreach (ATF::contact()->select_all() as $i) {
//	$i = array_map("utf8_decode",$i);	
//	$societe = array_map("utf8_decode",ATF::societe()->select($i["id_societe"]));


	$societe = ATF::societe()->select($i["id_societe"]);
	$i = array_merge($societe,$i);
	if ($i["nom"]) {
		$cn = ($i["prenom"]?$i["prenom"]." ":NULL).$i["nom"];
		$s .= "dn: cn=".$cn.",ou=".ATF::$codename.",dc=absystech,dc=optima\n";
		$s .= "objectClass: inetOrgPerson\n";
		$s .= "objectClass: organizationalPerson\n";
		$s .= "objectClass: top\n";
		if ($i["prenom"]) $s .= "givenName: ".$i["prenom"]."\n";
		if ($i["nom"]) $s .= "sn: ".$i["nom"]."\n";
		$s .= "cn: ".$cn."\n";
		if ($i["adresse"]) $s .= "street: ".$i["adresse"]."\n";
		if ($i["societe"]) $s .= "o: ".$i["societe"]."\n";
		if ($i["ville"]) $s .= "l: ".$i["ville"]."\n";
		if ($i["cp"]) $s .= "postalCode: ".$i["cp"]."\n";
		if ($i["tel"]) $s .= "telephoneNumber: ".$i["tel"]."\n";
		if ($i["gsm"]) $s .= "mobile: ".$i["gsm"]."\n";
		if ($i["fax"]) $s .= "facsimileTelephoneNumber: ".$i["fax"]."\n";
		if ($i["email"]) $s .= "mail: ".$i["email"]."\n";
		$s .= "\n";
	}
}

echo "version: 1\n\n";
echo $s;
?>
