<?
class commande_ligne_test extends ATF_PHPUnit_Framework_TestCase {
	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	/*besoin d'un user pour les traduction*/
	function setUp() {
		$this->initUser();
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function tearDown(){
		ATF::db()->rollback_transaction(true);
	}
	
	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function testConstruct(){
		$commande_ligne=new commande_ligne();
		$this->assertEquals("%commande.ref% > %commande_ligne.produit%",$commande_ligne->field_nom,"Commande_ligne n'a pas le bon field_nom");
		$this->assertEquals("commande",$commande_ligne->controlled_by,"Commande_ligne n'a pas le bon field_nom");
	}

	

};
?>