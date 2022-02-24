<?php

define("__BYPASS__",true);
include(dirname(__FILE__)."/../../global.inc.php");

ATF::societe()->q->reset()->whereIsNull("ref");

$soc = ATF::societe()->sa();

foreach($soc as $societe) {

    $ref=ATF::societe()->create_ref_prefix();
    $ref .= 'LI'.date('ym', strtotime($societe["date"]));

    $max=ATF::societe()->get_max_ref($ref);
    if($max<10){
        $ref.='000'.$max;
    }elseif($max<100){
        $ref.='00'.$max;
    }elseif($max<1000){
        $ref.='0'.$max;
    }elseif($max<10000){
        $ref.=$max;
    }

    ATF::societe()->u(array("id_societe" => $societe["id_societe"], "ref" => $ref));

}

