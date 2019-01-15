<?
/**
 * @testdox Etat des affaires
 */
class affaire_etat_cleodis_test extends ATF_PHPUnit_Framework_TestCase {
	/** Méthode pré-test, exécute avant chaque test unitaire
	* besoin d'un user pour les traduction
	*/
	public function setUp() {
		ATF::db()->begin_transaction(true);
	}

	/* Méthode post-test, exécute après chaque test unitaire */
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}


	/** Test du constructeur
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @testdox Constructeur
	*/
	public function test__construct(){
		$c = new affaire_etat();
		$this->assertTrue($c instanceOf affaire_etat, "L'objet affaire_etat n'est pas de bon type");
	}

	/**
	* @testdox Méthode _GET
	*/
	public function test__GET(){
		$this->obj = ATF::affaire_etat();

		$res = ATF::affaire_etat()->_GET(array("id_affaire"=>"8657b31577e56fcff8664b356ff1d2c2"));

		$this->assertEquals("reception_demande", $res[0]["etat"], "Retour GET incorrect 1");
		$this->assertEquals("2017-10-18 17:15:15", $res[0]["date"], "Retour GET incorrect 2");
		$this->assertEquals("21546", $res[0]["id_affaire"], "Retour GET incorrect 3");

	}

	/**
	* @testdox Méthode _POST
	*/
	public function test__POST(){
		$this->obj = ATF::affaire_etat();

		$res = ATF::affaire_etat()->_POST(array(),array("id_affaire"=>"8657b31577e56fcff8664b356ff1d2c2","etat"=>"reception_pj"));
		$this->assertEquals(true,$res, "POST incorrect");

		$res = ATF::affaire_etat()->_GET(array("id_affaire"=>"8657b31577e56fcff8664b356ff1d2c2"));

		$this->assertEquals("reception_pj", $res[0]["etat"], "Retour GET incorrect 1");
		$this->assertEquals("21546", $res[0]["id_affaire"], "Retour GET incorrect 3");


	}

};