<?
/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
class termes_test extends ATF_PHPUnit_Framework_TestCase {
	public function setUp() {
		ATF::db()->begin_transaction(true);
	}
	
	public function tearDown() {
		ATF::db()->rollback_transaction(true);
	}
	
	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function test_autocomplete(){
		$autocomplete=$this->obj->autocomplete();
		 ATF::termes()->q->reset()->setLimit(2000)
										   ->addOrder("termes")
										   ->setPage(0);
		$termes = ATF::termes()->select_all();
		$this->assertEquals($termes[count($termes)-1]["termes"],$autocomplete[count($termes)-1]["raw_1"],"Méthode update_complete non fonctionnel");
	}
};
?>