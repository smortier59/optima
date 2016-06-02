<?
/**
* Classe de test sur le module societe_frais_port
*/
class societe_frais_port_test extends ATF_PHPUnit_Framework_TestCase {
	/** Méthode pré-test, exécute avant chaque test unitaire
	* besoin d'un user pour les traduction
	*/
	public function setUp() {
		$this->initUser();
		$this->obj = new societe_frais_port();
	}
	
	/** Méthode post-test, exécute après chaque test unitaire
	*/
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}
	
	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function testFrais_port(){
		$frais_port=$this->obj->frais_port(array("poids"=>10));
		$this->assertEquals("35.00",$frais_port,'1 Frais port ne renvoie pas le bon prix');

		$frais_port=$this->obj->frais_port(array("poids"=>1000));
		$this->assertEquals("0.00",$frais_port,'2 Frais port ne renvoie pas le bon prix');
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function test_getQuickTips(){
		$infos = array();
		$r=$this->obj->getQuickTips($infos);
		$ex = "<ul><li>De 0.00Kg a 60.00Kg : <b>35.00€</b></li><li>De 60.00Kg a 100.00Kg : <b>44.00€</b></li><li>De 100.00Kg a 200.00Kg : <b>80.00€</b></li><li>De 200.00Kg a 300.00Kg : <b>140.00€</b></li><li>De 300.00Kg a 400.00Kg : <b>185.00€</b></li></ul>";
		$this->assertEquals($ex,$r,"Le retour n'est pas correcte, les frais de port ont été modifiés ou alors ya une merdouille");
		$this->assertTrue($infos['display'],"Le display n'est pas là");
		 
	}

				
};
?>