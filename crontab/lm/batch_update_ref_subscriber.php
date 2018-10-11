<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "lm";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);


try{
	ATF::begin_transaction();

	$mandates = array( "18100013 - SLM18100006" , "18100011 - SLM18100007" , "18100008 - SLM18100005" , "18100004 - SLM18100003" , "18090007 - SLM18090004" , "18080027 - SLM16090003" , "18080011 - " , "18080010 - " , "18080007 - " , "18080005 - " , "18080004 - " , "18080002 - " , "18070096 - " , "18070088 - SLM16110002" , "18070020 - " , "18070018 - " , "18070015 - " , "18060038 - " , "18060033 - " , "18060025 - " , "18060020 - SLM18040014" , "18060016 - SLM18010015" , "18060015 - SLM18040013" , "18060014 - SLM18040013" , "18060013 - SLM18040013" , "18040018 - SLM18040014" , "18040016 - SLM18040009" , "18040014 - SLM18040003" , "18040008 - SLM18040003" , "18040007 - SLM18040005" , "18040006 - SLM18040003" , "18040005 - SLM18040004" , "18030016 - SLM18030009" , "18030011 - SLM18030006" , "18030008 - SLM18010024" , "18020020 - SLM17120009" , "18020008 - SLM18020004" , "18020004 - SLM18010025" , "18010020 - SLM18010014" , "SLM18010008" , "SLM17120004" , "SLM17120007" , "SLM17120003" , "SLM17110017" , "SLM17110012" , "SLM17010004" , "SLM17110009" , "SLM17080001" , "SLM17060019" , "SLM17060014" , "17060036 - SLM17060009" , "17060032 - SLM17060008" , "17060012 - SLM17060004" , "17060003 - SLM17060002" , "17050008 - SLM17050005" , "SLM17040010" , "SLM17040011" , "SLM17040009" , "SLM17040006" , "SLM17040004" , "SLM17040003" , "SLM17040002" , "SLM17040001" , "SLM17030016" , "SLM17030015" , "SLM17030014" , "SLM17030012" , "SLM17030011" , "SLM17030009" , "SLM17030008" , "SLM17030003" , "SLM17030006" , "SLM17030005" , "SLM17030002" , "SLM17020015" , "SLM17020014" , "SLM17020004" , "SLM17020003" , "SLM17010010" , "SLM17010008" , "SLM17010004" , "SLM17010001" , "SLM16120011" , "SLM16120010" , "SLM16120008" , "SLM16120004" , "SLM16120003" , "SLM16120002" , "SLM16120001" , "SLM16110021" , "SLM16110020" , "SLM16110016" , "SLM16110015" , "SLM16110014" , "SLM16110010" , "SLM16110009" , "SLM16090008" , "SLM16110003" , "SLM16110002" , "SLM16100016" , "SLM16100014" , "SLM16100010" , "SLM16100005" , "SLM16100001" , "SLM16090007" , "SLM16090004");


	foreach($mandates as $k=>$v){

		if (strpos($v, '- ') !== false) {
			$exp = explode("-", $v);

			ATF::affaire()->q->reset()->where("affaire.ref", $exp[0]);
			$aff = ATF::affaire()->select_row();

			//ATF::affaire()->u(array("id_affaire" => $aff["affaire.id_affaire"], "subscriber_reference"=> $v));
			log::logger(array("id_affaire" => $aff["affaire.id_affaire"], "subscriber_reference"=> $v) , "mfleurquin");
		}else{
			//On met à jour toute les affaires du code client
			ATF::societe()->q->reset()->where("societe.ref", $v);
			$soc = ATF::societe()->select_row();

			ATF::affaire()->q->reset()->where("affaire.id_societe", $soc["id_societe"]);
			$affs = ATF::affaire()->sa();

			foreach ($affs as $key => $value) {
				//ATF::affaire()->u(array("id_affaire" => $value["id_affaire"], "subscriber_reference"=> $v));
				log::logger(array("id_affaire" => $value["id_affaire"], "subscriber_reference"=> $v) , "mfleurquin");
			}
		}


	}

	ATF::commit_transaction();
}catch(errorATF $e){
	echo $e->getMessage();
	ATF::rollback_transaction();
}

?>