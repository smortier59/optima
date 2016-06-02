<?
class affaire_candidat_test extends ATF_PHPUnit_Framework_TestCase {
	
	public function setUp() {
		$this->initUser();
	}
	
	/** Méthode post-test, exécute après chaque test unitaire*/
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}

	/* @author Morgan FLEURQUIN <mfleurquin@absystech.fr> */
	public function test_construct(){
		ATF::db()->select_db("extranet_v3_exactitude");
		$c = new affaire_candidat();	
		
		$this->assertTrue($c instanceOf affaire_candidat, "L'objet affaire_candidat n'est pas de bon type");
		
		ATF::db()->select_db("extranet_v3_cleodis");	
	}


	/* @author Morgan FLEURQUIN <mfleurquin@absystech.fr> */
	public function test_EtatUpdate(){
		ATF::db()->select_db("extranet_v3_exactitude");

		$id_candidat = ATF::candidat()->i(array("nom"=>"Nom", "prenom"=>"prenom", "niveau_diplome"=>"bac +2"));
		$id_affaire = ATF::affaire()->i(array("ref"=>"ref Affaire", "affaire"=> "Test affaire" , "id_societe"=>246));

		$id_affaire_candidat = ATF::affaire_candidat()->i(array("id_affaire"=> $id_affaire, "id_candidat"=>$id_candidat, "id_societe"=>246));

		$this->obj->EtatUpdate(array("id_affaire_candidat"=>$id_affaire_candidat, "field"=> "evalue", "evalue"=>"oui"));

		$this->assertEquals("oui", $this->obj->select($id_affaire_candidat , "evalue"), "Update error");

		ATF::$msg->getNotices();

		ATF::db()->select_db("extranet_v3_cleodis");	
	}	

	public function test_default_value(){
		ATF::db()->select_db("extranet_v3_exactitude");

		$id_candidat = ATF::candidat()->i(array("nom"=>"Nom", "prenom"=>"prenom", "niveau_diplome"=>"bac +2"));
		$id_affaire = ATF::affaire()->i(array("ref"=>"ref Affaire", "affaire"=> "Test affaire" , "id_societe"=>246));

		$id_affaire_candidat = ATF::affaire_candidat()->i(array("id_affaire"=> $id_affaire, "id_candidat"=>$id_candidat, "id_societe"=>246));

		$this->assertEquals(NULL, $this->obj->default_value("id_societe"), "default_value incorrect");

		ATF::_r("id_affaire", $id_affaire);

		$this->assertEquals(246, $this->obj->default_value("id_societe"), "default_value incorrect 1");

		$this->assertEquals(NULL, $this->obj->default_value("commentaire"), "default_value incorrect 2");

		ATF::db()->select_db("extranet_v3_cleodis");	
	}	



	
};