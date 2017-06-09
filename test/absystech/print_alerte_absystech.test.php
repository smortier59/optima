<?
class print_alerte_absystech_test extends ATF_PHPUnit_Framework_TestCase {
	// @author Cyril Charlier <ccharlier@absystech.fr>
	private $id_stock;

	public function setUp(){
		ATF::db()->begin_transaction(true);

	}
	private function environnement_test(){
		ATF::db()->truncate("print_alerte");
		$stock =ATF::stock()->insert(array(
			"libelle" =>"print stock test TU",
	    	'adresse_mac'=>"ABZNE22",
	    	'quantite'=> 1
    	));
    	$this->id_stock = $stock;
	}
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
		ATF::$msg->getNotices();

	}
	public function test_table(){
		$this->assertEquals("print_alerte",$this->obj->name(),"Probleme de nom de classe print_alerte");
	}

	public function test_GETWithIdStock(){
		$this->environnement_test();
		ATF::print_alerte()->insert(array(
			"id_stock" =>$this->id_stock,
		    "code"=>"1.2.1.2.1.2.1.2",
	    	'message'=>"Erreur test message 1"
    	));
    	$date = new DateTime("2017-05-02");
    	ATF::print_alerte()->insert(array(
    		"id_stock" =>$this->id_stock,
		    "code"=>"1.2.1.2.1.2.1.5",
	    	'message'=>"Erreur test message 2",
    		'date'=> date_format($date,'Y-M-d H:i:s')
    	));
    	$get = array(
    		'id_stock'=>$this->id_stock,
    		'date_cloture'=>'NULL'
    		);
    	$ret =ATF::print_alerte()->_GET($get);
		$this->assertEquals("Erreur test message 1",$ret[0]["message"],"Probleme de message retour alerte");
		$this->assertEquals("1.2.1.2.1.2.1.2",$ret[0]["code"],"Probleme de code retour alerte");

		$this->assertEquals("Erreur test message 2",$ret[1]["message"],"Probleme de nom retour alerte 2");
		$this->assertEquals("1.2.1.2.1.2.1.5",$ret[1]["code"],"Probleme de code retour alerte 2");


	}
	public function test_GET(){
		$this->environnement_test();	
		$id_stock =ATF::stock()->insert(array(
			"libelle" =>"print stock test TU",
	    	'adresse_mac'=>"ABZNE23",
	    	'quantite'=> 1
    	));
		ATF::print_alerte()->insert(array(
			"id_stock" =>$this->id_stock,
		    "code"=>"1.2.1.2.1.2.1.2",
	    	'message'=>"Erreur test message 1"
    	));
    	$date = new DateTime("2017-05-02");
    	ATF::print_alerte()->insert(array(
    		"id_stock" =>$id_stock,
		    "code"=>"1.2.1.2.1.2.1.5",
	    	'message'=>"Erreur test message 2",
    		'date'=> date_format($date,'Y-M-d H:i:s')
    	));
    	$ret =ATF::print_alerte()->_GET($get);
		$this->assertEquals("Erreur test message 1",$ret[0]["message"],"Probleme de message retour alerte");
		$this->assertEquals("1.2.1.2.1.2.1.2",$ret[0]["code"],"Probleme de code retour alerte");

		$this->assertEquals("Erreur test message 2",$ret[1]["message"],"Probleme de nom retour alerte 2");
		$this->assertEquals("1.2.1.2.1.2.1.5",$ret[1]["code"],"Probleme de code retour alerte 2");
	}
	public function test_POST(){
		$this->environnement_test();	
		$date = new DateTime("2017-05-02");
		$post = array(
			'alerts'=> json_encode(
				array(
					array(
					'code'=>"1.2.1.2.1.2.1.5",
					'message'=>"Erreur test post",
					)
				)
			),
			'id_stock'=> $this->id_stock,
			'date'=>date_format($date,'Y-m-d H:i:s')
	   	);

    	$ret =ATF::print_alerte()->_POST('',$post);
		$this->assertTrue($ret['result'],"probleme d'insertion via POST");
		$alertes = ATF::print_alerte()->select_all();
		$this->assertEquals('Erreur test post',$alertes['data'][0]['message'],'Probleme alerte insérée');
		$this->assertEquals('2017-05-02 00:00:00',$alertes['data'][0]['date'],'Probleme alerte date insérée');
	}
	public function test_POSTError(){
		$this->environnement_test();	
		try {
			$post = array(
				'alerts'=> json_encode(
					array(
						array(
							'code'=>"",
							'message'=>"",
						)
					)
				),
				'id_stock'=> '',
				'date'=>''		
			);
            ATF::print_alerte()->_POST('',$post);
        } catch (errorATF $e) {
            $error = $e->getCode();
        }
        $this->assertEquals(500,$error,'problème sur post lorsqu il n y a pas de field');

	}
	public function test_PUTError(){
		$this->environnement_test();
		$id_alerte = ATF::print_alerte()->insert(array(
			"id_stock" =>$this->id_stock,
		    "code"=>"1.2.1.2.1.2.1.2",
	    	'message'=>"Erreur test message 1"
    	));	
    	ATF::print_alerte()->insert(array(
    		"id_stock" =>$this->id_stock,
		    "code"=>"1.2.1.2.1.2.1.5",
	    	'message'=>"Erreur test message 2",
    		'date'=> date_format($date,'Y-M-d H:i:s')
    	));
		$date = new DateTime("2017-05-02");
		$post = array(
			array(
				'code'=>"1.2.3.1.2.3.1",
				'message'=>"error Test"
			)
		);
		try {
    		ATF::print_alerte()->_PUT('',$post);
        }catch (errorATF $e) {
            $error = $e->getCode();
        }
        $this->assertEquals(500,$error,'problème sur put lorsqu il n y a pas l\'id_print_alerte');

	}
	public function test_PUT(){
		$this->environnement_test();
		$id_alerte = ATF::print_alerte()->insert(array(
			"id_stock" =>$this->id_stock,
		    "code"=>"1.2.1.2.1.2.1.2",
	    	'message'=>"Erreur test message 1"
    	));	
		$date = new DateTime("2017-05-02");
		$post = array(
			'code'=>"1.2.3.1.2.3.1",
			'message'=>"error Test",
			'id_print_alerte'=>$id_alerte
		);
    	$ret =ATF::print_alerte()->_PUT('',$post);
        $this->assertTrue($ret['result'],'doit retourner true');
        //ATF::$msg->getNotices();


	}
}
?>