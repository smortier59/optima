<?
class facture_non_parvenue_test extends ATF_PHPUnit_Framework_TestCase {
	public function setUp() {
		$this->initUser();
	}
	
	/** Méthode post-test, exécute après chaque test unitaire*/
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}


	public function test_EtatUpdate(){
		$this->obj->EtatUpdate(array("id_facture_non_parvenue"=>3,"field"=>"facturation_terminee","facturation_terminee"=>"oui"));
		ATF::$msg->getNotices();
		$this->assertEquals("oui",$this->obj->select(3, "facturation_terminee"), "Etat Update incorrect");
	}

};