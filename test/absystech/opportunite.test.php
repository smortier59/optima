<?
class opportunite_test extends ATF_PHPUnit_Framework_TestCase {
	// @author Cyril Charlier <ccharlier@absystech.fr>
	private $societe;
	private $opportunite;
	private $user;
	private $commercial;

	public function setUp(){
		ATF::db()->begin_transaction(true);
		$this->environnement_test();

	}
	private function environnement_test(){

		ATF::db()->truncate("opportunite");

		$this->commercial = ATF::user()->i(array("login" =>"userTestComOp",
			'password'=>"az78qs45",
			'nom'=>'userComm',
			'civilite'=>'m',
			'prenom'=>'prenom'
		));
		$this->societe = ATF::societe()->i(array("societe"=>"Societe Test",
			'id_commercial'=>$this->commercial
		));
		$this->user =ATF::user()->i(array("login" =>"userTest",
			'password'=>"az78qs45",
			'nom'=>'userTest',
			'civilite'=>'m',
			'prenom'=>'prenom',
			"id_societe"=> $this->societe
		));
		$this->opportunite = ATF::opportunite()->i(array("opportunite" =>"Test opportunite",
			'id_user'=>$this->user,
			'etat'=>'en_cours',
			"id_societe"=> $this->societe,
			"id_owner"=> $this->commercial
		));

	}
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
		ATF::$msg->getNotices();

	}
	public function test_table(){
		$this->assertEquals("opportunite",$this->obj->name(),"Probleme de nom de classe opportunite");
	}
	public function testSelectAll(){
		$opportunite2 = ATF::opportunite()->i(array("opportunite" =>"Test opportunite2",
			'id_user'=>$this->user,
			'etat'=>'en_cours',
			"id_societe"=> $this->societe,
			"id_owner"=> $this->commercial
		));
		$ret =ATF::opportunite()->select_all();
		$this->assertEquals(0,$ret[0]['nb_suivi'],"Probleme de nombre de suivi");
		$this->assertEquals((String) $opportunite2,$ret[0]['opportunite.id_opportunite'],"Probleme id retourné");


		$this->assertEquals(0,$ret[1]['nb_suivi'],"Probleme de nombre de suivi");
		$this->assertEquals((String) $this->opportunite,$ret[1]['opportunite.id_opportunite'],"Probleme id retourné");
	}
	public function testAutoIncrement(){
		$opportunite1 = ATF::opportunite()->i(array("opportunite" =>"Test testAutoIncrement",
			'id_user'=>$this->user,
			'etat'=>'en_cours',
			"id_societe"=> $this->societe,
			"id_owner"=> $this->commercial
		));

		$opportunite2 = ATF::opportunite()->i(array("opportunite" =>"opportunite testAutoIncrement",
			'id_user'=>$this->user,
			'etat'=>'en_cours',
			"id_societe"=> $this->societe,
			"id_owner"=> $this->commercial
		));
		$get = array('q'=>"testAutoIncrement");
		//$debug = new ReflectionClass('opportunite_absystech');
		//$test = $debug->getMethod("_ac");
		$ret = ATF::opportunite()->_ac($get);

		$this->assertEquals($opportunite2,$ret[0]['id_opportunite'],"Probleme id opportunite retourne");
		$this->assertEquals("opportunite testAutoIncrement",$ret[0]['opportunite'],"Probleme opportunite retourné");
		$this->assertEquals(0,$ret[0]['nb_suivi'],"Probleme de nombre de suivi");

		$this->assertEquals($opportunite1,$ret[1]['id_opportunite'],"Probleme id opportunite retourne");
		$this->assertEquals(0,$ret[1]['nb_suivi'],"Probleme de nombre de suivi");
		$this->assertEquals("Test testAutoIncrement",$ret[1]['opportunite'],"Probleme opportunite retourné");
	}
	
	public function testAutoIncrementWithIdSociete(){
		ATF::opportunite()->i(array("opportunite" =>"Test testAutoIncrement",
			'id_user'=>$this->user,
			'etat'=>'en_cours',
			"id_societe"=> $this->societe,
			"id_owner"=> $this->commercial
		));
		$societe = ATF::societe()->i(array("societe"=>"Other Societe Test",
			'id_commercial'=>$this->commercial
		));
		$opportunite = ATF::opportunite()->i(array("opportunite" =>"opportunite testAutoIncrement",
			'id_user'=>$this->user,
			'etat'=>'en_cours',
			"id_societe"=> $societe,
			"id_owner"=> $this->commercial
		));
		$get = array("id_societe"=>$societe);
		$ret = ATF::opportunite()->_ac($get);
		$this->assertEquals($opportunite,$ret[0]['id_opportunite'],"Probleme id opportunite retourne");
		$this->assertEquals(0,$ret[0]['nb_suivi'],"Probleme de nombre de suivi");
		$this->assertEquals("opportunite testAutoIncrement",$ret[0]['opportunite'],"Probleme opportunite retourné");
	}

	/*public function testToAffaire(){
		// Methode a tester.
		ATF::suivi()->i(array(
			'id_user'=>$this->user,
			"id_societe"=> $this->societe,
			"id_opportunite"=> $this->opportunite,
			'texte'=>'test suivi'
		));
		$get = array("id_opportunite"=>$this->opportunite);
		$ret = ATF::opportunite()->toAffaire($get);
		$get = array("id_affaire"=>$ret);
		$getAffaire = ATF::affaire()->_GET();


		//$this->assertEquals($ret,ATF::affaire()->decryptId($getAffaire[0]['id_affaire_fk']),"Probleme id affaire retourne".print_r($getAffaire,true));
		$this->assertEquals("Test opportunite",$getAffaire[0]['affaire'],"Probleme d'affaire retourné");
		$this->assertEquals($this->societe,$getAffaire[0]['societe.societe_fk'],"Probleme société retourné");	
	
	}*/

}
?>