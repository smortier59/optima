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
		$currentDate = new DateTime();
		for ($i=0; $i < 30; $i++) { 
			$date = new DateTime();
			$date->sub(new DateInterval('P'.$i.'D'));

			$id_etat = ATF::print_etat()->insert(array(
				"date"=> date_format($date,'Y-m-d H:i:s'),
				'name'=>"Cartouche Test noir ".$i,
				'color'=>'black',
				'id_stock'=> $this->id_stock,
				'current' => 2500-($i*5),
				'max' => 3000,
				'type'=> 'toner'
			)); 
		}
		
		$get = array('id_stock' => $this->id_stock,"graph"=> "true");
		$ret =ATF::print_etat()->_GET($get);
		// point le plus ancien en premier
		$this->assertEquals(date_format($date,'Y-m-d H:i:s'),$ret[0]["date"],"Probleme de date retour print etat");
		$this->assertEquals("black",$ret[0]["color"],"Probleme de color retour print etat");
		$this->assertEquals("Cartouche Test noir 29",$ret[0]["name"],"Probleme de retour max");

		$this->assertEquals(date_format($currentDate,'Y-m-d H:i:s'),$ret[29]["date"],"Probleme de date retour print etat");
		$this->assertEquals("black",$ret[29]["color"],"Probleme de color retour print etat");
		$this->assertEquals("Cartouche Test noir 0",$ret[29]["name"],"Probleme de retour max");		
	}
	
	public function test_GET(){
		$this->environnement_test();	
		$date = new DateTime();

		ATF::print_etat()->insert(array(
			"date"=> date_format($date,'Y-m-d H:i:s'),
			'name'=>"Cartouche Test noir",
			'color'=>'black',
			'id_stock'=> $this->id_stock,
			'current' => 20000,
			'max' => 33000,
			'type'=> 'toner'
		));
		ATF::print_etat()->insert(array(
			"date"=> date_format($date,'Y-m-d H:i:s'),
			'name'=>"Cartouche Test jaune",
			'color'=>'yellow',
			'id_stock'=> $this->id_stock,
			'current' => 2500,
			'max' => 3000,
			'type'=> 'toner'
		));
		ATF::print_etat()->insert(array(
			"date"=> date_format($date,'Y-m-d H:i:s'),
			'name'=>"Cartouche Test magenta",
			'color'=>'magenta',
			'id_stock'=> $this->id_stock,
			'current' => 500,
			'max' => 5000,
			'type'=> 'toner'
		));
		$ret =ATF::print_etat()->_GET();

		$this->assertEquals("Cartouche Test noir",$ret[0]["name"],"Probleme de name retour print consommable");
		$this->assertEquals("20000",$ret[0]["current"],"Probleme de current retour print consommable");
		$this->assertEquals("black",$ret[0]["color"],"Probleme de code color");

		$this->assertEquals("Cartouche Test jaune",$ret[1]["name"],"Probleme de name retour print consommable");
		$this->assertEquals("2500",$ret[1]["current"],"Probleme de current retour print consommable");
		$this->assertEquals("yellow",$ret[1]["color"],"Probleme de code color");

		$this->assertEquals("Cartouche Test magenta",$ret[2]["name"],"Probleme de name retour print consommable");
		$this->assertEquals("500",$ret[2]["current"],"Probleme de current retour print consommable");
		$this->assertEquals("magenta",$ret[2]["color"],"Probleme de code color");
	}

	public function test_POST(){
		$this->environnement_test();	
		$date = new DateTime("2017-05-02");
		$post = array(
			'id_stock'=> $this->id_stock,
			'etat' => json_encode( array(
				"toners"=> array(array(
					"date"=> date_format($date,'Y-m-d H:i:s'),
					'name'=>"Cartouche Test post",
					'color'=>'black',
					'current' => 200,
					'max' => 8000,
				)),
				'copies' => array(
					"mono"=> 654321,
					'color'=> 123456
				)

			))
		);

		$ret =ATF::print_etat()->_POST('',$post);
		$this->assertTrue($ret['result'],"probleme d'insertion via POST");
		$etat = ATF::print_etat()->select_all();

		$this->assertEquals($this->id_stock,$etat[0]['id_stock'],'Probleme id_stock insérée');
		$this->assertEquals('color',$etat[0]['name'],'Probleme name insérée');
		$this->assertEquals('copie_couleur',$etat[0]['type'],'Probleme type inséré');

		$this->assertEquals($this->id_stock,$etat[1]['id_stock'],'Probleme id_stock insérée');
		$this->assertEquals('mono',$etat[1]['name'],'Probleme name insérée');
		$this->assertEquals('copie_noir',$etat[1]['type'],'Probleme type inséré');


		$this->assertEquals($this->id_stock,$etat[2]['id_stock'],'Probleme id_stock insérée');
		$this->assertEquals('Cartouche Test post',$etat[2]['name'],'Probleme name insérée');
		$this->assertEquals('toner',$etat[2]['type'],'Probleme type inséré');
	}
	public function test_POSTError(){
		$this->environnement_test();	
		try {
			// id_stock is missing 
		$post = array(
			'id_stock'=> '',
			'etat' => json_encode( array(
				"toners"=> array(array(
					"date"=> date_format($date,'Y-m-d H:i:s'),
					'name'=>"Cartouche Test post",
					'color'=>'black',
					'current' => 200,
					'max' => 8000,
				)),
				'copies' => array(
					"mono"=> 654321,
					'color'=> 123456
				)

			))
		);
			ATF::print_etat()->_POST(false,$post);
		} catch (errorATF $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(500,$error,'problème sur post lorsqu il y a un champ manquant');

	}

}
?>