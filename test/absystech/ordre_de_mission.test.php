<?
/**
* Tu module ordre de mission
*/
class ordre_de_mission_test extends ATF_PHPUnit_Framework_TestCase {
	/**
	* Hotline de test
	* @var int
	*/
	private $id_hotline;
		
	protected function setUp() {
		$this->initUser();
	}
	
	protected function tearDown() {
		ATF::db()->rollback_transaction(true);
		//Flush des notices
		ATF::$msg->getNotices();
	}
	
	/** 
	* Initialisation d'un jeu de test holtine
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	*/
	private function initHotline(){
		$hotline = array(
			"hotline"=>"HotlineTuTest"
			,"id_societe"=>$this->s["user"]->get("id_societe")
			,"detail"=>"HotlineTuTest"
			,"date_debut"=>date('Y-m-d')
			,"id_contact"=>$this->id_contact
			,"id_user"=>$this->id_user
			,"pole_concerne"=>"dev"
			,"urgence"=>'detail'
			,'charge'=>"intervention" //précisé pour éviter les problèmes avec la méthode "setbillingmode"
		);
		$this->id_hotline = ATF::hotline()->insert($hotline);
	}
	
	/* @author Jérémie Gwiazdowski <jgw@absystech.fr>, Nicolas BERTEMONT <nbertemont@absystech.fr> */
	public function test_insert(){
		$this->initHotline();
		//Création d'un odm
		$infos=array("id_hotline"=>$this->id_hotline
					,"id_user"=>$this->id_user
					,"id_societe"=>$this->id_societe
					,"date"=>date("Y-m-d h:i:s")
					,"ordre_de_mission"=>"TU odm"
					,"adresse"=>"TU adr"
					,"cp"=>"12345"
					,"ville"=>"tu");
		
		//Astuce de merde pour générer le pdf !
		$infos['filestoattach']=array("fichier_joint");
		
		//--Transaction d'encadrement
		ATF::db()->begin_transaction();
		$id_odm=$this->obj->insert($infos);
				
		//On génère les fichiers de la queue (et notamment le fichier joint au mail ;))
		//Puis le mail
		//Sans METTRE A JOUR LA BASE DE DONNEES
		$queue=ATF::db()->getQueue();
		$queue->generateMoveFile();
		$queue->generateSendEmail();
		$mail=$this->obj->getMail();
		$mail=$mail->sent;
		$this->assertTrue(isset($mail[0]));
		
		ATF::db()->rollback_transaction();
		//--Fin Transaction d'encadrement
	}
	
	/* @author Jérémie Gwiazdowski <jgw@absystech.fr> */
	public function test_default_value(){
		$this->initHotline();
		ATF::_r("id_hotline",$this->id_hotline);
		
		$value=$this->obj->default_value("adresse");
		$this->assertEquals($value,"139 rue des arts");
		
		$value=$this->obj->default_value("adresse_2");
		$this->assertNull($value);
		
		$value=$this->obj->default_value("adresse_3");
		$this->assertNull($value);
		
		$value=$this->obj->default_value("cp");
		$this->assertEquals($value,"59100");
		
		$value=$this->obj->default_value("ville");
		$this->assertEquals($value,"Roubaix");
		
		$value=$this->obj->default_value("id_pays");
		$this->assertEquals($value,"FR");
		
		$value=$this->obj->default_value("id_user");
		$this->assertEquals($value,$this->id_user);
		
		$value=$this->obj->default_value("ordre_de_mission");
		$this->assertEquals($value,"HotlineTuTest");
		
		$value=$this->obj->default_value("date");
		$this->assertNull($value);
	}
		
};
?>