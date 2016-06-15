<?
/** Import société / Prospect
* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
*/
define("__BYPASS__",true);
$_SERVER["argv"][1] = "exquado";
include(dirname(__FILE__)."/../../global.inc.php");

$i = "SELECT * FROM `Client_import`;";
$liste = ATF::db()->sql2array($i);

$i = 0;
$siege = NULL;

foreach ($liste as $k => $v) {

	if(is_numeric($v["Code"])){
		$siege = NULL;
	}

	$id_societe = ATF::societe()->i(array("ref"=>$v["Code"], 
										 "societe"=>$v["Nom"], 
										 "cp"=>$v["C.P."], 
										 "ville"=>$v["Ville"], 
										 "tel"=>$v["Téléphone"], 
										 "fax"=>$v["Fax"], 
										 "liens"=>$v["Catég"],
										 "relation"=>"client",
										 "id_filiale"=>$siege
									   )
								 );
	
	if(is_numeric($v["Code"])){
		$siege = $id_societe;
	}

	if($v["Contact"]){

		ATF::contact()->i(array("id_societe"=>$id_societe,
							   "nom"=>$v["Contact"], 
							   "tel"=>$v["Tél. Contact"]
							  ));						  
	}
	
}


?>