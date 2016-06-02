<?
class candidat_cleodis_test extends ATF_PHPUnit_Framework_TestCase {
	/** Méthode pré-test, exécutée avant chaque test unitaire
	* besoin d'un user pour les traduction
	*/
	public function setUp() {
		ATF::db()->begin_transaction(true);
	}
	
	/** Méthode post-test, exécutée après chaque test unitaire
	*/
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}

	/*--------------------------------------------------------------*/
	/*                   Tests unitaires                            */
	/*--------------------------------------------------------------*/
	
	/** Test du constructeur
	* @author Morgan Fleurquin <mfleurquin@absystech.fr>
	*/
	public function test_candidat_constructeur(){
		new candidat_exactitude();
	}	

	/** Test du constructeur
	* @author Morgan Fleurquin <mfleurquin@absystech.fr>
	*/
	public function test_getAffaires(){
		ATF::db()->select_db("extranet_v3_exactitude");
		
		$c = new candidat_exactitude();

		$this->assertNotNull($c->getAffaires(), "Pas d'affaire en cours sur exactitude ??");

		ATF::db()->select_db("extranet_v3_cleodis");

		
	}	

	
	
};
?>