<?
class pointage_horaire_test extends ATF_PHPUnit_Framework_TestCase {
	

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
	*/	
	public function test__construct(){
		$c = new pointage_horaire();	
		$this->assertTrue($c instanceOf pointage_horaire, "L'objet pointage_horaire n'est pas de bon type");
	}
};