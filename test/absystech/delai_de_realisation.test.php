<?
/*@author Antoine MAITRE <amaitre@absystech.fr> */ 
class delai_de_realisation_test extends ATF_PHPUnit_Framework_TestCase {
	public function setUp() {
		ATF::db()->begin_transaction(true);
	}
	
	public function tearDown() {
		ATF::db()->rollback_transaction(true);
	}
	
	/*@author Antoine MAITRE <amaitre@absystech.fr> */ 
	function test_autocomplete(){
		$autocomplete=$this->obj->autocomplete();
		$this->assertEquals("1 semaine",$autocomplete[0]["raw_1"],"Méthode update_complete non fonctionnel");
	}
};
?>