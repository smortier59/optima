<?
/**
* TU sur les news */
class news_test extends ATF_PHPUnit_Framework_TestCase {

	public function setUp() {
		$this->initUser();
	}
	
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}
			
	// @author Cyril Charlier <ccharlier@absystech.fr>
	public function testGetConseils() {
		$ret = ATF::news()->GetConseils();
		$this->assertEquals("conseil de test",substr(trim($ret),-strlen("conseil de test")), "Mauvais conseil retourné");
	}

};
?>