<?
/**
 * @testdox Etat des affaires
 */
class loyer_prolongation_test extends ATF_PHPUnit_Framework_TestCase {
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
		$c = new loyer_prolongation();
		$this->assertTrue($c instanceOf loyer_prolongation, "L'objet loyer_prolongation n'est pas de bon type");
	}

	public function test_select_all(){
		$this->obj->q->reset()->where("id_affaire", 12388);

		$res = $this->obj->select_all();

		$this->assertEquals(count($res), 2, "select all 1");
		$this->assertEquals($res[0]["id_affaire"], 12388, "select all 2");
		$this->assertEquals($res[1]["id_affaire"], 12388, "select all 3");
	}

	public function test_prixTotal(){
		$res = $this->obj->prixTotal(12388);
		$this->assertEquals($res , "5233.0", "Prix total error");
	}

	public function test_dureeTotal(){

		$frequence = array("mois"=>17,"trimestre"=>51, "semestre"=>102, "an"=>204);

		foreach ($frequence as $key => $value) {
			$this->obj->q->reset()->where("id_affaire", 12388);
			$res = $this->obj->select_all();

			foreach ($res as $k => $v) {
				ATF::loyer_prolongation()->u(array("id_loyer_prolongation"=>$v["id_loyer_prolongation"], "frequence_loyer"=> $key));
			}
			$res = $this->obj->dureeTotal(12388);
			$this->assertEquals($res , $value, "Duree totale ".$key." error");
		}

	}

};