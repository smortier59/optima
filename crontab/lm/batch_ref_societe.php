<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "lm";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);


$q = "SELECT id_societe, ref, date  FROM `societe` WHERE `ref` IS NULL";
$client_sans_ref = ATF::db()->sql2array($q);


foreach ($client_sans_ref as $key => $value){

	$ref = "SLI".date("Ym", strtotime($value["date"]));

	$max=ATF::societe()->get_max_ref($ref);
	if($max<10){
		$ref.='000'.$max;
	}elseif($max<100){
		$ref.='00'.$max;
	}elseif($max<1000){
		$ref.='0'.$max;
	}elseif($max<10000){
		$ref.=$max;
	}else{
		throw new errorATF(ATF::$usr->trans('ref_too_high'),80853);
	}

	echo $value["id_societe"]." ---> ".$ref;

}
