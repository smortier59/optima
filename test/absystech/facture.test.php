<?
class facture_test extends ATF_PHPUnit_Framework_TestCase {
	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function setUp() {
		$this->initUser();
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function tearDown(){
		$this->rollback_transaction();
	}
	
	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function testConstruct(){
		$facture=new facture();
		$this->assertNotNull($facture,"Le constructeur de facture ne fonctionne pas");
	}
};
?>