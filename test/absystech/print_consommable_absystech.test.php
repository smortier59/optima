<?
class print_consommable_absystech_test extends ATF_PHPUnit_Framework_TestCase {
	// @author Cyril Charlier <ccharlier@absystech.fr>
	private $ref_stock;

	public function setUp(){
		ATF::db()->begin_transaction(true);

	}
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
		ATF::$msg->getNotices();

	}
	private function environnement_test(){
		ATF::db()->truncate("print_consommable");
		$stock =ATF::stock()->insert(array(
			"libelle" =>"print stock test TU",
			"ref"=>'AZE123654',
			'adresse_mac'=>"ABZNE22",
			'quantite'=> 1
		));
		$this->ref_stock = 'AZE123654';
	}
	public function test_table(){
		$this->assertEquals("print_consommable",$this->obj->name(),"Probleme de nom de classe print_consommable");
	}
	public function test_GETWithIdPrintConsommable(){
		$this->environnement_test();
		$id_conso = ATF::print_consommable()->i(array(
			"designation"=>"Test noir",
			'duree'=>"12500",
			'prix'=>12.5,
			'ref_stock'=> $this->ref_stock,
			'couleur'=> 'noir'
		));

		$get = array( 'id_print_consommable' => $id_conso );
		$ret =ATF::print_consommable()->_GET($get);
		$this->assertEquals("Test noir",$ret["designation"],"Probleme de designation retour print consommable");
		$this->assertEquals("12500",$ret["duree"],"Probleme de duree retour print consommable");
		$this->assertEquals("noir",$ret["couleur"],"Probleme de code couleur");


	}
	public function test_GET(){
		$this->environnement_test();	
		ATF::print_consommable()->insert(array(
			"designation"=>"Test noir",
			'duree'=>"12500",
			'prix'=>12.5,
			'ref_stock'=> $this->ref_stock,
			'couleur'=> 'noir'
		));
		ATF::print_consommable()->insert(array(
			"designation"=>"Test magenta",
			'duree'=>"18500",
			'prix'=>1298.5,
			'ref_stock'=> $this->ref_stock,
			'couleur'=> 'magenta'
		));
		ATF::print_consommable()->insert(array(
			"designation"=>"Test yellow",
			'duree'=>"18600",
			'prix'=>198.35,
			'ref_stock'=> $this->ref_stock,
			'couleur'=> 'jaune'
		));
		$ret =ATF::print_consommable()->_GET();
		$this->assertEquals("Test noir",$ret[0]["designation"],"Probleme de designation retour print consommable");
		$this->assertEquals("12500",$ret[0]["duree"],"Probleme de duree retour print consommable");
		$this->assertEquals("noir",$ret[0]["couleur"],"Probleme de code couleur");

		$this->assertEquals("Test magenta",$ret[1]["designation"],"Probleme de designation retour print consommable");
		$this->assertEquals("18500",$ret[1]["duree"],"Probleme de duree retour print consommable");
		$this->assertEquals("magenta",$ret[1]["couleur"],"Probleme de code couleur");

		$this->assertEquals("Test yellow",$ret[2]["designation"],"Probleme de designation retour print consommable");
		$this->assertEquals("18600",$ret[2]["duree"],"Probleme de duree retour print consommable");
		$this->assertEquals("jaune",$ret[2]["couleur"],"Probleme de code couleur");
	}
	public function test_GETByRefStock(){
		$this->environnement_test();	
		
		ATF::stock()->insert(array(
			"libelle" =>"print stock test 2",
			"ref"=>'789789789',
			'adresse_mac'=>"15GH65IORJ",
			'quantite'=> 1
		));
		ATF::print_consommable()->insert(array(
			"designation"=>"Test noir",
			'duree'=>"12500",
			'prix'=>12.5,
			'ref_stock'=> $this->ref_stock,
			'couleur'=> 'noir'
		));
		ATF::print_consommable()->insert(array(
			"designation"=>"Test magenta",
			'duree'=>"18500",
			'prix'=>1298.5,
			'ref_stock'=> $this->ref_stock,
			'couleur'=> 'magenta'
		));
		ATF::print_consommable()->insert(array(
			"designation"=>"Test yellow",
			'duree'=>"18600",
			'prix'=>198.35,
			'ref_stock'=> '789789789',
			'couleur'=> 'jaune'
		));
		$get = array('ref_stock' => $this->ref_stock);
		$ret =ATF::print_consommable()->_GET($get);
		$this->assertEquals("Test noir",$ret[0]["designation"],"Probleme de designation retour print consommable");
		$this->assertEquals("12500",$ret[0]["duree"],"Probleme de duree retour print consommable");
		$this->assertEquals("noir",$ret[0]["couleur"],"Probleme de code couleur");

		$this->assertEquals("Test magenta",$ret[1]["designation"],"Probleme de designation retour print consommable");
		$this->assertEquals("18500",$ret[1]["duree"],"Probleme de duree retour print consommable");
		$this->assertEquals("magenta",$ret[1]["couleur"],"Probleme de code couleur");
	}
	
	public function test_POST(){
		$this->environnement_test();	
		$date = new DateTime("2017-05-02");
		$post = array(
			"designation"=>"Test noir",
			'duree'=>"12500",
			'prix'=>12.5,
			'ref_stock'=> $this->ref_stock,
			'couleur'=> 'noir'
		);

		$ret =ATF::print_consommable()->_POST('',$post);
		$this->assertTrue($ret['result'],"probleme d'insertion via POST");
		$consommable = ATF::print_consommable()->select_all();
		$this->assertEquals($consommable['data'][0]['id_print_consommable'],$ret['id_print_consommable'],'Probleme consommable inséré');
	}
	public function test_POSTError(){
		$this->environnement_test();	
		try {
			$post = array(
				"designation"=>"",
				'duree'=>"12500",
				'prix'=>12.5,
				'ref_stock'=> $this->ref_stock,
				'couleur'=> 'noir'
			);
			ATF::print_consommable()->_POST(false,$post);
		} catch (errorATF $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(500,$error,'problème sur post lorsqu il y a un champ manquant');

	}
	public function test_PUTError(){
		$this->environnement_test();
		$post = array(
				"designation"=>"",
				'duree'=>"12500",
				'prix'=>12.5,
				'ref_stock'=> $this->ref_stock,
				'couleur'=> 'noir'
			);
		try {
			ATF::print_consommable()->_PUT('',$post);
		}catch (errorATF $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(500,$error,'problème sur put lorsqu il n y a pas l\'id_print_consommable');

	}
	public function test_PUT(){
		$this->environnement_test();
		$id_print_consommable = ATF::print_consommable()->i(array(
			"designation"=>"Test noir",
			'duree'=>"12500",
			'prix'=>12.5,
			'ref_stock'=> $this->ref_stock,
			'couleur'=> 'noir'
		));
		$post = array(
			'designation'=>"Test only",
			'id_print_consommable'=>$id_print_consommable
		);
		$ret =ATF::print_consommable()->_PUT('',$post);
		$consommable = ATF::print_consommable()->select_all();

		$this->assertTrue($ret['result'],'doit retourner true');
		$this->assertEquals('Test only',$consommable[0]['designation'],'doit renvoyer la nouvelle designation');


	}
	public function test_DELETE(){
		$this->environnement_test();
		$id_print_consommable = ATF::print_consommable()->insert(array(
			"designation"=>"Test noir",
			'duree'=>"12500",
			'prix'=>12.5,
			'ref_stock'=> $this->ref_stock,
			'couleur'=> 'noir'
		));
		$get = array('id'=>$id_print_consommable);
		$ret =ATF::print_consommable()->_DELETE($get);
		$this->assertTrue($ret['result'],'doit retourner true');
	}
	public function test_DELETE_Error(){
		$this->environnement_test();
		try {
			$id_print_consommable = ATF::print_consommable()->insert(array(
				"designation"=>"Test noir",
				'duree'=>"12500",
				'prix'=>12.5,
				'ref_stock'=> $this->ref_stock,
				'couleur'=> 'noir'
			));
			ATF::print_consommable()->_DELETE('');
        }catch (errorATF $e) {
            $error = $e->getCode();
        }
        $this->assertEquals(1000,$error,'id manquant !');
	}
}
?>