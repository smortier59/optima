<?
define("__BYPASS__",true);
if (!$_SERVER['argv'][1]) {
	$_SERVER['argv'][1]="absystech";
}	
include(dirname(__FILE__)."/../global.inc.php");

require_once __DIR__."/../libs/ATF/libs/vendor/autoload.php";
$pdf = new FPDI();

//$pageCount = $pdf->setSourceFile(__DATA_PATH__."/lm/commande/1.contratA4");
//$tplCon = $pdf->importPage(1, '/MediaBox');

$pageCount = $pdf->setSourceFile(__DIR__."/../signature1.4.pdf");
$tplSig = $pdf->importPage(1, '/MediaBox');

$pdf->addPage();
$pdf->useTemplate($tplSig, 10, 10, 90);
//$pdf->useTemplate($tplCon, 10, 10, 90);

$pdf->Output();