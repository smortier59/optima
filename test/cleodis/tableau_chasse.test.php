<?
class tableau_chasse_test extends ATF_PHPUnit_Framework_TestCase {

	public function setUp() {
		ATF::db()->begin_transaction(true);
	}
	
	/** Méthode post-test, exécute après chaque test unitaire*/
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}

	/* @author Morgan FLEURQUIN <mfleurquin@absystech.fr> */
	public function test_construct(){
		ATF::db()->select_db("extranet_v3_exactitude");
		$c = new tableau_chasse();	
		
		$this->assertTrue($c instanceOf tableau_chasse, "L'objet tableau_chasse n'est pas de bon type");
		
		ATF::db()->select_db("extranet_v3_cleodis");	
	}

	public function test_interesseUpdate(){
		ATF::db()->select_db("extranet_v3_exactitude");
		$c = new tableau_chasse();	
		
		$tc = $c->insert(array("magasin"=> "magasin TU", "adresse"=> "adresse TU", "id_affaire"=>13240));

		$c->interesseUpdate(array("id_tableau_chasse"=>$tc, "interesse"=>"oui"));

		$this->assertEquals("oui", $c->select($tc , "interesse") , "MAJ interesseUpdate incorrect");

		ATF::$msg->getNotices();

		ATF::db()->select_db("extranet_v3_cleodis");	
	}	


	public function test_setInfos(){
		ATF::db()->select_db("extranet_v3_exactitude");
		$c = new tableau_chasse();	
		
		$tc = $c->insert(array("magasin"=> "magasin TU", "adresse"=> "adresse TU", "id_affaire"=>13240));

		$c->setInfos(array("id_tableau_chasse"=>$tc, "field"=>"tel" , "tel"=>"0606060606"));

		$this->assertEquals("0606060606", $c->select($tc , "tel") , "MAJ setInfos incorrect");
		
		ATF::$msg->getNotices();

		ATF::db()->select_db("extranet_v3_cleodis");	
	}


}
?>