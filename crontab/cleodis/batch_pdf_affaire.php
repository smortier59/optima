<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);

$dataPath = __DATA_PATH__."cleodis/";


$ff = ATF::facture_fournisseur()->select_all();



foreach ($ff as $key => $value) {

	$path = ATF::facture_fournisseur()->filepath($value["id_facture_fournisseur"], "fichier_joint");
 	if (file_exists($path)) {
 		$class = ATF::getClass("facture_fournisseur");

		$id_pdf_affaire = ATF::pdf_affaire()->insert(array("id_affaire"=>$value["id_affaire"], "provenance"=>ATF::$usr->trans($class->name(), "module")." ref : ".$value['ref']));
		
		copy(ATF::facture_fournisseur()->filepath($value["id_facture_fournisseur"],"fichier_joint"), ATF::pdf_affaire()->filepath($id_pdf_affaire,"fichier_joint2"));
	}

}

?>