<?
class scanner_test extends ATF_PHPUnit_Framework_TestCase {

	// @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	private function beginTransaction($codename){
    	ATF::db()->begin_transaction(true);
	}

	// @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	private function RollBackTransaction($codename){
		ATF::db()->rollback_transaction(true);
	}


	// @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	public function test_construct(){
		$c = new scanner();
		$this->assertTrue($c instanceOf scanner, "L'objet scanner n'est pas de bon type");

	}

}