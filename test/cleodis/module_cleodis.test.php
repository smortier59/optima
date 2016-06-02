<?
class module_cleodis_test extends ATF_PHPUnit_Framework_TestCase {
	
	public function setUp() {
		$this->initUser();
	}

	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}

	/* @author Nicolas BERTEMONT <nbertemont@absystech.fr> */
	public function test_enfants(){
		$cm=new module_midas();
		$this->assertEquals('1b41c4f7097eff80a86249d952a7f25d',md5(serialize($cm->enfants(52))),"Des modules d'affaire sont visibles mais ne l'était pas avant");
	}
	
	/* @author Nicolas BERTEMONT <nbertemont@absystech.fr> */
	public function test_skin_from_nom(){
		$cm=new module_midas();
		$this->assertEquals('yellow',$cm->skin_from_nom("lol"),"Skin incorrecte");
	}

};
?>