<?
class courrier_information_pack_test extends ATF_PHPUnit_Framework_TestCase {

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
		$c = new courrier_information_pack();
		$this->assertTrue($c instanceOf courrier_information_pack, "L'objet courrier_information_pack n'est pas de bon type");

	}

}