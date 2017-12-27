<?
class produit_fournisseur_loyer_test extends ATF_PHPUnit_Framework_TestCase {

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
		$this->_produit_fournisseur_loyer = new produit_fournisseur_loyer();
	}

	public function test_select_all(){
		$ret = $this->obj->select_all();

		$this->assertNotEmpty($res, "Retour Select all vide ??");
	}
}
?>