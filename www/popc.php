<?
try{
	include(dirname(__FILE__)."/../global.inc.php");

	//Première connexion
	if ($_GET["tel"] && $_SESSION["user"]->logged) {
		$tel = trim($_GET["tel"]);
		$tel = str_replace("(","",$tel);
		$tel = str_replace(")","",$tel);
		$tel = str_replace("-","",$tel);
		$tel = ATF::db()->real_escape_string($tel);
		ATF::contact()->q->reset()
			->addField('contact.id_societe','id')
			->from("contact","id_societe","societe","id_societe")
			->orWhere("contact.tel",$tel,"tel")
			->orWhere("contact.gsm",$tel,"tel")
			->orWhere("societe.tel",$tel,"tel")
			->setLimit(1)
			->setDimension('cell');
		if ($id_societe = ATF::contact()->select_all()) {
			$id_societe= ATF::societe()->cryptId($id_societe);
			header("Location: societe-select-".$id_societe.".html");
			die;
		}
	}
}catch(errorATF $e){
	$e->setError();
}
?>