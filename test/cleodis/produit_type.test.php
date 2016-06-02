<?
class produit_type_test extends ATF_PHPUnit_Framework_TestCase {
	
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
		$this->_produit_type = new produit_type();	
	}

	/** @test Test du constructeur produit.  */
	public function test_autocomplete(){
		ATF::_s('preselected_produit_type',array(array("id_produit_type"=>1)));
		$this->assertNotNull($this->obj->autocomplete(array()),"autocomplete ne retourne rien");
	}
}
?>