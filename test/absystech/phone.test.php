<?
/**
* Module phone ! Permet de gérer les téléphones des utilisateurs (click2call)
*/
class phone_test extends ATF_PHPUnit_Framework_TestCase {		
	protected function setUp() {
		ATF::initialize();
		//$this->initUser();
		ATF::db()->begin_transaction(true);
	}
	
	protected function tearDown() {
		ATF::db()->rollback_transaction(true);
	}
	
	/* @author Jérémie Gwiazdowski <jgw@absystech.fr> */
	public function test_insert(){
		$this->initUser(false);
		
		//Création d'un PABX
		$this->id_asterisk=ATF::asterisk()->insert(array(
			"asterisk"=>"serveur de test"
			,"host"=>"localhost"
			,"url_webservice"=>"https://asterisk.absystech.netttt/webservices/index.php"
			,"login"=>"test"
			,"password"=>"test"
			));
		$this->assertNotNull($this->id_asterisk,"pb creation serveur");
		
		//Création d'un téléphone
		$infos=array(
			"phone"=>"test",
			"sip"=>45,
			"id_user"=>$this->id_user,
			"id_asterisk"=>$this->id_asterisk
			);
		$id_phone=$this->obj->insert($infos);
		
		$this->assertNotNull($this->id_asterisk,"pb creation telephone");
		$phone=$this->obj->select($id_phone);
		$this->assertEquals("test",$phone["phone"],"assert 1");
		$this->assertEquals(45,$phone["sip"],"assert 2");
		$this->assertEquals($this->id_user,$phone["id_user"],"assert 3");
		$this->assertEquals($this->id_asterisk,$phone["id_asterisk"],"assert 4");
	}
	
	/* @author Jérémie Gwiazdowski <jgw@absystech.fr> */
	public function test_insert_speed_insert(){
		$this->initUser(false);
		
		//Création d'un PABX
		$this->id_asterisk=ATF::asterisk()->insert(array(
			"asterisk"=>"serveur de test"
			,"host"=>"localhost"
			,"url_webservice"=>"https://asterisk.absystech.netttt/webservices/index.php"
			,"login"=>"test"
			,"password"=>"test"
			));
		$this->assertNotNull($this->id_asterisk,"pb creation serveur");
		
		//Création d'un téléphone
		$infos=array(
			"phone"=>"test",
			"sip"=>45,
			"id_user"=>$this->id_user,
			"id_asterisk"=>$this->id_asterisk,
			"notpl2div"=>true
			);
		$id_phone=$this->obj->insert($infos);
		
		$this->assertNotNull($this->id_asterisk,"pb creation telephone");
		$phone=$this->obj->select($id_phone);
		$this->assertEquals("test",$phone["phone"],"assert 1");
		$this->assertEquals(45,$phone["sip"],"assert 2");
		$this->assertEquals($this->id_user,$phone["id_user"],"assert 3");
		$this->assertEquals($this->id_asterisk,$phone["id_asterisk"],"assert 4");
	}
};

?>