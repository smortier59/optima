<?
class affaire_candidat_test extends ATF_PHPUnit_Framework_TestCase {
	
	public function setUp() {
		$this->initUser();
	}
	
	/** Méthode post-test, exécute après chaque test unitaire*/
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}
	
};