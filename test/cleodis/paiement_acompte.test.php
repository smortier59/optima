<?
class paiement_acompte_test extends ATF_PHPUnit_Framework_TestCase {
	
	// @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	private function beginTransaction($codename){		
		ATF::db()->select_db("extranet_v3_".$codename);
    	ATF::$codename = $codename;
    	ATF::db()->begin_transaction(true);		
	}

	// @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	private function RollBackTransaction($codename){	
		ATF::db()->rollback_transaction(true);
        ATF::$codename = "cleodis";
        ATF::db()->select_db("extranet_v3_cleodis");
	}	

};