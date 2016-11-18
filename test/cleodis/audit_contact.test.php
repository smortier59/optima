<?
class audit_contact_test extends ATF_PHPUnit_Framework_TestCase {
	
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


	// @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	public function test_construct(){
		$this->beginTransaction("cap");
		$c = new audit_contact();		
		
		$this->RollBackTransaction("cleodis");

		$this->assertTrue($c instanceOf audit_contact, "L'objet audit_contact n'est pas de bon type");
			
	}

}