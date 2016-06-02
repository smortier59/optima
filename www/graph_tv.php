<?
try{
	include(dirname(__FILE__)."/../global.inc.php");

	ATF::$html->displayWithAnalyzer("graph_tv.tpl.htm"); 

}catch(error $e){
	$e->setError();
}
?>