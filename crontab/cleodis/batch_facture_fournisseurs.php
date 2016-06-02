<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);

$dataPath = __DATA_PATH__."cleodis/";

$i = "CREATE TABLE IF NOT EXISTS `export_ff` (`ref` varchar(19) DEFAULT NULL,  `type` varchar(11) DEFAULT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8";
ATF::db()->sql2array($i);

$i2 = "INSERT INTO `export_ff` (`ref`, `type`) VALUES ('1530108', 'achat'),('FC 9 158', 'maintenance'),('FC 9 159', 'maintenance'),('FC 9 160', 'maintenance'),('FC 9 161', 'maintenance'),('FCT01131', 'maintenance'),('TS 15040', 'achat'),('TS 15043', 'achat'),('1500381', 'achat'),('92285101', 'achat'),('92288087', 'achat'),('FA0035496', 'achat'),('5535518', 'achat'),('F1500150', 'maintenance'),('F1500151', 'maintenance'),('F1500152', 'maintenance'),('1500636', 'achat'),('F1501161', 'achat'),('1500926', 'achat'),('1500850', 'achat'),('1500923', 'achat'),('1500942', 'achat'),('1500940', 'achat'),('FCO150103085', 'achat'),('TS 15056', 'achat'),('FA114627', 'achat'),('92294476', 'achat'),('92300945', 'achat'),('92301196', 'achat'),('92300901', 'achat'),('FCO150101421', 'achat'),('FC 9 247', 'maintenance'),('FC 9 250', 'maintenance'),('FC 9 251', 'maintenance'),('FC 9 252', 'maintenance'),('FC 9 253', 'maintenance'),('FC 9 240', 'maintenance'),('FC 9 241', 'maintenance'),('FC 9 242', 'maintenance'),('FC 9 243', 'maintenance'),('FC 9 244', 'maintenance'),('FC 9 245', 'maintenance'),('FC 9 246', 'maintenance'),('92309434', 'achat'),('92307131', 'achat'),('1501464', 'achat'),('1501525', 'achat'),('F15020696', 'achat'),('TS 15092', 'achat'),('FA0036615', 'achat'),('FA0037200', 'achat'),('1501829', 'achat'),('1501830', 'achat'),('25294', 'achat'),('25293', 'achat'),('30050111', 'achat'),('1501757', 'achat'),('FCO150202940', 'achat'),('FCO150202739', 'achat'),('92318167', 'achat'),('FA103487', 'achat'),('FA103488', 'achat'),('FC 9 438', 'maintenance'),('FC 9 439', 'maintenance'),('FC 9 440', 'maintenance'),('FC 9 441', 'maintenance'),('FC 9 442', 'maintenance'),('FC 9 444', 'maintenance'),('FC 9 445', 'maintenance'),('11000977', 'cout_copie'),('TS 15096', 'achat'),('B5030022', 'maintenance'),('FA8047', 'achat'),('FA8054', 'achat'),('FA00305', 'achat'),('TS 15154', 'achat'),('92332287', 'achat'),('92332294', 'achat'),('FCA1B110713', 'achat'),('25442', 'achat'),('FCO150301222', 'achat'),('FLI15020012', 'achat'),('FA103527', 'achat'),('FA103526', 'achat'),('1502749', 'achat'),('FLI15020032', 'achat'),('FLI150020033', 'achat'),('fc81501079', 'achat'),('FC_1501080', 'achat'),('FA0036604', 'achat'),('0362014', 'achat'),('11001154', 'cout_copie'),('FC 9 157', 'maintenance'),('FLI15020031', 'achat'),('FLI15030065', 'achat'),('92297824', 'achat'),('530047', 'achat'),('530048', 'achat'),('FC 9 050', 'maintenance'),('FC 9 633', 'maintenance'),('FC 9 632', 'maintenance'),('FC 9 631', 'maintenance'),('FC 9 630', 'maintenance'),('AFLI15030009', 'achat'),('FA8064', 'achat'),('FC 9 635', 'maintenance'),('530052', 'achat'),('530053', 'achat'),('TS 15169', 'achat'),('15 0308', 'achat'),('1503119', 'achat'),('B5040031', 'maintenance'),('1537772', 'achat'),('1537766', 'achat'),('92343028', 'achat'),('92343392', 'achat'),('92340314', 'achat'),('FLI15040001', 'achat'),('FLI1503004', 'achat'),('FLI15020030', 'achat'),('651107289', 'achat'),('4519543583', 'achat'),('T2015R3760000001237', 'achat'),('540020', 'achat'),('540021', 'achat'),('540018', 'achat'),('540019', 'achat'),('F1502048-C02501', 'achat'),('540025', 'achat'),('540026', 'achat'),('540029', 'achat'),('540030', 'achat'),('F1521520', 'maintenance'),('F1521518', 'maintenance'),('1503555', 'achat'),('FCT01188', 'maintenance'),('92351563', 'achat'),('92353115', 'achat'),('92354235', 'achat'),('92354231', 'achat'),('92354473', 'achat'),('92354396', 'achat'),('FC_1504051', 'achat'),('TS 15149', 'achat'),('92339804', 'achat'),('92286999', 'achat'),('FA103548', 'achat'),('150172', 'achat'),('TS 15168', 'achat'),('TS 15204', 'achat'),('TS 15218', 'achat'),('TS 15221', 'achat'),('651852674', 'achat'),('15 0414', 'achat'),('15 0413', 'achat'),('FCU150027', 'achat'),('FA8203', 'achat'),('FA0146', 'achat'),('VEN0001445147', 'achat'),('F15051910', 'achat'),('B5050018', 'maintenance'),('15419', 'achat'),('FC_1504077', 'achat'),('1', 'achat'),('20150127', 'achat'),('FA000011426', 'achat'),('92361003', 'achat'),('10877', 'achat'),('15LP1972', 'achat'),('1504317', 'achat'),('FA8345', 'achat'),('15LP2034', 'achat'),('M31T-1505-50027', 'achat'),('FLI15030067', 'achat'),('F1521519', 'maintenance'),('F1505028', 'achat'),('25785', 'achat'),('TS 15262', 'achat'),('FA000011469', 'achat'),('92374455', 'achat'),('FC 20 023 659', 'achat'),('FCO150400891', 'achat'),('FCO150400890', 'achat'),('FCO150400094', 'achat'),('32027695', 'achat'),('25816', 'achat'),('92362469', 'achat'),('FA000011427', 'achat'),('FCU150619', 'achat'),('FAC000002836', 'achat'),('TS 15274', 'achat'),('1540899', 'achat'),('FA8358', 'achat'),('FLI15050041', 'achat'),('FLI15040070', 'achat'),('FA1505-6469', 'achat'),('F0409037', 'achat'),('92377084', 'achat'),('92385130', 'achat'),('92385349', 'achat'),('15 0512', 'achat'),('15 0511', 'achat'),('15 0510', 'achat'),('VEN0001460773', 'achat'),('FA00308', 'achat'),('15LP2410', 'achat'),('15LP2411', 'achat'),('15LP2412', 'achat'),('B5060023', 'maintenance'),('FC_1505098', 'achat'),('FA10688', 'achat'),('FC93584', 'achat'),('FCO150600253', 'achat'),('FCO150600056', 'achat'),('1541217', 'achat'),('FCO150600947', 'achat'),('FA0041781', 'achat'),('TS 15308', 'achat'),('06089', 'achat'),('FA8491', 'achat'),('1506008', 'achat'),('FA8489', 'achat'),('FA8487', 'achat'),('FC 10 026', 'maintenance'),('FC 9 833', 'maintenance'),('FC 9 837', 'maintenance'),('FC 9 838', 'maintenance'),('FC 10 020', 'maintenance'),('FC 10 021', 'maintenance'),('FC 10 022', 'maintenance'),('FC 10 023', 'maintenance'),('FC 10 024', 'maintenance'),('FC 10 025', 'maintenance'),('FC 9 834', 'maintenance'),('FC 9 835', 'maintenance'),('FC 9 836', 'maintenance'),('FCO150601342', 'achat'),('FA0042186', 'achat'),('FLI15040061', 'achat'),('FCO150402697', 'achat'),('FCO150401365', 'achat'),('FLI15050017', 'achat'),('TS 15275', 'achat'),('29', 'achat'),('25942', 'achat'),('1002666', 'achat')";
ATF::db()->sql2array($i2);


$q = "SELECT * FROM export_ff";
$data = ATF::db()->sql2array($q);

foreach ($data as $key => $value) {
	ATF::facture_fournisseur()->q->reset()->where("ref",$value["ref"]);
	$ff = ATF::facture_fournisseur()->select_row();	
	ATF::facture_fournisseur()->u(array("id_facture_fournisseur"=>$ff["id_facture_fournisseur"] , "type"=>$value["type"]));
}

$d = "DROP TABLE `export_ff`";
ATF::db()->sql2array($d);

$u = "UPDATE `facture_fournisseur` SET `numero_cegid`=NULL,`deja_exporte`=NULL";
ATF::db()->sql2array($u);


ATF::facture_fournisseur()->q->reset()
							->addJointure("facture_fournisseur", "id_affaire", "affaire","id_affaire" )
						    ->addJointure("affaire", "id_affaire", "commande","id_affaire" )
							->where("facture_fournisseur.date", "2015-01-01", "AND", false, ">=")
							->where("facture_fournisseur.type", "achat", "AND")
						    ->whereIsNotNull("commande.date_debut")
						    ->addOrder("commande.date_debut");
$facture_fournisseurs = ATF::facture_fournisseur()->sa();


$num = 4000;
foreach ($facture_fournisseurs as $key => $value) {
	ATF::facture_fournisseur()->u(array('id_facture_fournisseur' => $value["id_facture_fournisseur"], 
										'numero_cegid'=>$num));
	$num++;

}


?>