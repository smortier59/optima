<?
class print_etat_absystech_test extends ATF_PHPUnit_Framework_TestCase {
	// @author Cyril Charlier <ccharlier@absystech.fr>
	private $id_stock;

	public function setUp(){
		ATF::db()->begin_transaction(true);

	}
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
		ATF::$msg->getNotices();

	}
	private function environnement_test(){
		ATF::db()->truncate("print_etat");
		$this->id_stock =ATF::stock()->insert(array(
			"libelle" =>"print stock test TU",
			"ref"=>'AZE123654',
			'adresse_mac'=>"ABZNE22",
			'quantite'=> 1
		));
	}
	public function test_table(){
		$this->assertEquals("print_etat",$this->obj->name(),"Probleme de nom de classe print_etat");
	}
	public function test_GETWithIdStock(){
		$this->environnement_test();
		$date = new DateTime("2017-05-02");
		$id_etat = ATF::print_etat()->insert(array(
			"date"=> date_format($date,'Y-M-d H:i:s'),
			'name'=>"Cartouche Test noir",
			'color'=>'black',
			'id_stock'=> $this->id_stock,
			'current' => 2500,
			'max' => 3000,
			'type'=> 'toner'
		)); 
		$get = array('id_stock' => $this->id_stock);
		$ret =ATF::print_etat()->_GET($get);
		$this->assertEquals("Cartouche Test noir",$ret[0]["name"],"Probleme de name retour print etat");
		$this->assertEquals("black",$ret[0]["color"],"Probleme de duree retour print etat");
		$this->assertEquals(3000,$ret[0]["max"],"Probleme de retour max");
	}
	public function test_GETForGraph(){
		$this->environnement_test();
		for ($i=0; $i < 30; $i++) { 
			$date = new DateTime("2017-08-"+$i);
			$id_etat = ATF::print_etat()->insert(array(
				"date"=> date_format($date,'Y-M-d H:i:s'),
				'name'=>"Cartouche Test noir"+$i,
				'color'=>'black',
				'id_stock'=> $this->id_stock,
				'current' => 2500-($i*5),
				'max' => 3000,
				'type'=> 'toner'
			)); 
		}
		
		$get = array('id_stock' => $this->id_stock,"graph"=> "true");
		$ret =ATF::print_etat()->_GET($get);
		log::logger($ret,'ccharlier');
		//$this->assertEquals("Cartouche Test noir",$ret[0]["name"],"Probleme de name retour print etat");
		//$this->assertEquals("black",$ret[0]["color"],"Probleme de duree retour print etat");
		//$this->assertEquals(3000,$ret[0]["max"],"Probleme de retour max");
	}
	/*
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

		$ret =ATF::print_etat()->_POST('',$post);
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
	*/
}
?>