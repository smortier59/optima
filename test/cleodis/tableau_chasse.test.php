<?
class tableau_chasse_test extends ATF_PHPUnit_Framework_TestCase {

	public function setUp() {
		ATF::db()->begin_transaction(true);
	}
	
	/** Méthode post-test, exécute après chaque test unitaire*/
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}

}
?>