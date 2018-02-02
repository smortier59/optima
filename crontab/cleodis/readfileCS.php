<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);

$response = file_get_contents("/home/optima/core/log/creditsafe.xml");


$xml = simplexml_load_string($response);

$bi = $xml->xmlresponse->body->company->baseinformation;
$s = $xml->xmlresponse->body->company->summary;
$b =  $xml->xmlresponse->body->company->balancesynthesis;


