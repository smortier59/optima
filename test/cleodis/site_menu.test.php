<?
class site_menu_test extends ATF_PHPUnit_Framework_TestCase {

	public function setUp() {
		ATF::db()->begin_transaction();
	}

	/** Méthode post-test, exécute après chaque test unitaire*/
	public function tearDown(){
		ATF::$msg->getNotices();
		ATF::db()->rollback_transaction(true);
	}


	// @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	public function test_construct(){
		$c = new site_menu();

		$this->assertTrue($c instanceOf site_menu, "L'objet site_menu n'est pas de bon type");
	}


	public function test_GET(){
		$retour = ATF::site_menu_cleodis()->_GET(array("site_associe"=>"portail_toshiba", "rubrique"=>"FAQ"), array());

		$this->assertEquals(count($retour), 7, "Count retour site menu Toshiba incorrect?");
		$this->assertEquals($retour["titre"], "Je peux dénoncer le contrat dès le début afin de garantir une date de fin ?", "Retour site menu Toshiba 0 incorrect?");
	}

};