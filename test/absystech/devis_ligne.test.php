<?
class devis_ligne_test extends ATF_PHPUnit_Framework_TestCase {
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
		$devis_ligne=new devis_ligne();
		$this->assertEquals($devis_ligne->table,"devis_ligne","Le constructeur ne renvoi pas la bonne table");
		$this->assertEquals($devis_ligne->controlled_by,"devis","Le constructeur ne renvoi pas le bon controlled_by");
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function testSelect_all(){
		$this->obj->q->andWhere("id_devis","100","id","<");
		$this->assertEquals($this->obj->select_all(false,"desc"),$this->obj->select_all(false,"asc"),"select_all ne fonctionne pas");
	}
};