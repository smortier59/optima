<?
class produit_env_test extends ATF_PHPUnit_Framework_TestCase {
	
	/** Méthode pré-test, exécute avant chaque test unitaire
	* besoin d'un user pour les traduction
	*/
	public function setUp() {
		$this->begin_transaction(true);
	}
	
	/** Méthode post-test, exécute après chaque test unitaire*/
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}

	/** @test Test du constructeur produit.  */
	public function test_constructeur(){
		$this->_produit_env = new produit_env();	
	}

	/** @test Test du constructeur produit.  */
	public function test_autocomplete(){
		ATF::_s('preselected_produit_env',array(array("id_produit_env"=>1)));
		$this->assertNotNull($this->obj->autocomplete(array()),"autocomplete ne retourne rien");
	}

	public function test_setInfos(){
		ATF::produit_env()->setInfos(array("id_produit_env"=>1, "field"=>"ordre", "ordre"=>3));
		$this->assertEquals(3, ATF::produit_env()->select(1,"ordre"), "SetInfos incorrect");

		ATF::$msg->getNotices();
	}
}
?>