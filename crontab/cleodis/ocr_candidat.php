<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "exactitude";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);


ATF::candidat()->q->reset()->whereIsNull("ocrcv");

$candidats = ATF::candidat()->select_all();

if($candidats){
	foreach ($candidats as $key => $value) {
		$pathPDF = ATF::candidat()->filepath($value["id_candidat"],"cv");

		if(file_exists($pathPDF)){
			//$cmd = "gs -q -dNODISPLAY -dSAFER -dDELAYBIND -dWRITESYSTEMDICT -dSIMPLE -c save -f ps2ascii.ps ".$pathPDF." -c quit";
		
			$cmd = "pdftotext -nopgbrk ".$pathPDF;
			$result = `$cmd`;

			$texte = file_get_contents($pathPDF.'.txt', FILE_USE_INCLUDE_PATH);

			ATF::candidat()->u(array("id_candidat"=>$value["id_candidat"], "ocrcv"=>$texte));
		}else{
			echo "Pas de pdf pour le candidat ".$value["id_candidat"]."\n";
		}

		
	}
}
?>