<?
if($_SERVER["REMOTE_ADDR"] == "109.190.125.45" || preg_match('/192\.168\.0\.[0-9]{1,3}/', $_SERVER["REMOTE_ADDR"]) || $_SERVER["REMOTE_ADDR"] == "82.247.208.221"){
	try{
		include(dirname(__FILE__)."/../global.inc.php");

		ATF::$codename = "absystech";

		if(isset($_GET["code"]) && $_GET["code"] == "att"){
			ATF::$codename = "att";
		}
		
		ATF::db()->select_db("extranet_v3_".ATF::$codename);
		ATF::$usr = new usr("13");
		ATF::_s("user",ATF::$usr);
		

		ATF::getEnv()->commitSession();
		
		ATF::$html->displayWithAnalyzer("rotator.tpl.htm"); 

	}catch(error $e){
		$e->setError();
	}
}

?>
