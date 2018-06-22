<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "lm";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);



try{
	ATF::begin_transaction();


	$parc = array(
		array( "70515620" , "Alarme maison sans fil EVOLOGY Zen série limitée :" , "73120085/d880392adb7c" , "attente_location" , "actif"),
		array( "70515620" , "Alarme maison sans fil EVOLOGY Zen série limitée :" , "73220139/d880392ad87d" , "attente_location" , "actif"),
		array( "70515620" , "Alarme maison sans fil EVOLOGY Zen série limitée :" , "83320036/d8803929e8ed" , "attente_location" , "actif"),
		array( "79354835" , "Badge mains-libres désactivant automatiquement l'alarme" , "BU3001018B4F" , "attente_location" , "actif"),
		array( "79354835" , "Badge mains-libres désactivant automatiquement l'alarme" , "BU300101479B" , "attente_location" , "actif"),
		array( "79355346" , "Caméra intérieure connectée HD, Somfy Protect" , "2059A0EDCD54" , "attente_location" , "actif"),
		array( "79355346" , "Caméra intérieure connectée HD, Somfy Protect" , "B0411D145A41" , "attente_location" , "actif"),
		array( "69008114" , "Clavier à touches intérieur" , "FO30050E9F17" , "attente_location" , "actif"),
		array( "69008114" , "Clavier à touches intérieur" , "FO30050EA246" , "attente_location" , "actif"),
		array( "79354821" , "Détecteur de mouvements compatible animaux" , "BU2003000F70" , "attente_location" , "actif"),
		array( "79354821" , "Détecteur de mouvements compatible animaux" , "BU2003000934" , "attente_location" , "actif"),
		array( "79354821" , "Détecteur de mouvements compatible animaux" , "BU2003000919" , "attente_location" , "actif"),
		array( "79354821" , "Détecteur de mouvements compatible animaux" , "BU200300057B" , "attente_location" , "actif"),
		array( "79354821" , "Détecteur de mouvements compatible animaux" , "BU20030014FF" , "attente_location" , "actif"),
		array( "68658415" , "Détecteur de mouvements intérieur compatible animaux" , "FO2004433M1" , "attente_location" , "actif"),
		array( "68658415" , "Détecteur de mouvements intérieur compatible animaux" , "FO2004433M2" , "attente_location" , "actif"),
		array( "68658415" , "Détecteur de mouvements intérieur compatible animaux" , "FO2004433M3" , "attente_location" , "actif"),
		array( "70201551" , "Détecteur de vibrations et d'ouvertures IntelliTAG anti-intrusion" , "FO200600CD8D1507" , "attente_location" , "actif"),
		array( "70201551" , "Détecteur de vibrations et d'ouvertures IntelliTAG anti-intrusion" , "FO200600FDB31603" , "attente_location" , "actif"),
		array( "70201551" , "Détecteur de vibrations et d'ouvertures IntelliTAG anti-intrusion" , "FO200600348A1606" , "attente_location" , "actif"),
		array( "70201551" , "Détecteur de vibrations et d'ouvertures IntelliTAG anti-intrusion" , "FO200600343E1606" , "attente_location" , "actif"),
		array( "70201551" , "Détecteur de vibrations et d'ouvertures IntelliTAG anti-intrusion" , "FO200600CC0B1507" , "attente_location" , "actif"),
		array( "70201551" , "Détecteur de vibrations et d'ouvertures IntelliTAG anti-intrusion" , "FO200600CAFE1507" , "attente_location" , "actif"),
		array( "70201551" , "Détecteur de vibrations et d'ouvertures IntelliTAG anti-intrusion" , "FO200600FC9A1603" , "attente_location" , "actif"),
		array( "70201551" , "Détecteur de vibrations et d'ouvertures IntelliTAG anti-intrusion" , "FO200600FDAB1603" , "attente_location" , "actif"),
		array( "70201551" , "Détecteur de vibrations et d'ouvertures IntelliTAG anti-intrusion" , "FO200600CA0B1507" , "attente_location" , "actif"),
		array( "70201551" , "Détecteur de vibrations et d'ouvertures IntelliTAG anti-intrusion" , "FO200600CA091507" , "attente_location" , "actif"),
		array( "70201551" , "Détecteur de vibrations et d'ouvertures IntelliTAG anti-intrusion" , "FO200600D1B81508" , "attente_location" , "actif"),
		array( "70201551" , "Détecteur de vibrations et d'ouvertures IntelliTAG anti-intrusion" , "FO20060107D7" , "attente_location" , "actif"),
		array( "70201551" , "Détecteur de vibrations et d'ouvertures IntelliTAG anti-intrusion" , "FO200601084A1609" , "attente_location" , "actif"),
		array( "70201551" , "Détecteur de vibrations et d'ouvertures IntelliTAG anti-intrusion" , "FO20060104ED1607" , "attente_location" , "actif"),
		array( "70201551" , "Détecteur de vibrations et d'ouvertures IntelliTAG anti-intrusion" , "FO200600F1431601" , "attente_location" , "actif"),
		array( "70201551" , "Détecteur de vibrations et d'ouvertures IntelliTAG anti-intrusion" , "FO20060102DA1607" , "attente_location" , "actif"),
		array( "70201551" , "Détecteur de vibrations et d'ouvertures IntelliTAG anti-intrusion" , "FO20060108751609" , "attente_location" , "actif"),
		array( "70201551" , "Détecteur de vibrations et d'ouvertures IntelliTAG anti-intrusion" , "FO200601052E1608" , "attente_location" , "actif"),
		array( "70201551" , "Détecteur de vibrations et d'ouvertures IntelliTAG anti-intrusion" , "FO20060041B81408" , "attente_location" , "actif"),
		array( "70201551" , "Détecteur de vibrations et d'ouvertures IntelliTAG anti-intrusion" , "F020060102301607" , "attente_location" , "actif"),
		array( "79354814" , "IntelliTAG, détecteur anti-intrusion pour portes et fenêtres" , "BU2001011F29" , "attente_location" , "actif"),
		array( "79354814" , "IntelliTAG, détecteur anti-intrusion pour portes et fenêtres" , "BU2001011406" , "attente_location" , "actif"),
		array( "79354814" , "IntelliTAG, détecteur anti-intrusion pour portes et fenêtres" , "BU200101140E" , "attente_location" , "actif"),
		array( "79354814" , "IntelliTAG, détecteur anti-intrusion pour portes et fenêtres" , "BU2001011F26" , "attente_location" , "actif"),
		array( "79354814" , "IntelliTAG, détecteur anti-intrusion pour portes et fenêtres" , "BU2001014DCB" , "attente_location" , "actif"),
		array( "00000001" , "Sirène extérieure" , "Reprise0001" , "attente_location" , "actif"),
		array( "00000001" , "Sirène extérieure" , "Reprise0002" , "attente_location" , "actif"),
		array( "79490586" , "Sirène extérieure 112dB sans fil" , "BU4008002B49" , "attente_location" , "actif"),
		array( "80123661" , "Alarme maison sans fil connectée compatible animaux Starter pack SOMFY protect" , "BU0101003C93" , "attente_location" , "actif"),
		array( "80123661" , "Alarme maison sans fil connectée compatible animaux Starter pack SOMFY protect" , "BU010100403A" , "attente_location" , "actif"),
		array( "68624906" , "Télécommande 4 boutons" , "FO30041508CR20321" , "attente_location" , "actif"),
		array( "68624906" , "Télécommande 4 boutons" , "FO30041605CR20322" , "attente_location" , "actif"),
		array( "68624906" , "Télécommande 4 boutons" , "FO30041508CR20323" , "attente_location" , "actif")
	);

	foreach($parc as $key=>$value){

		ATF::produit()->q->reset()->where("produit.ref_lm", $value[0]);
		$produit = ATF::produit()->select_row();

		ATF::parc()->i(array(
							"id_produit"=> $produit["produit.id_produit"],
							"ref"=>$value[0],
							"libelle"=>$value[1],
							"serial" => $value[2],
							"etat"=> "attente_location"
						)
					  );
	}

	ATF::commit_transaction();
}catch(errorATF $e){
	echo $e->getMessage();
	ATF::rollback_transaction();
}

?>