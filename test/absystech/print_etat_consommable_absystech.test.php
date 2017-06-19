<?
class print_etat_consommable_absystech_test extends ATF_PHPUnit_Framework_TestCase {
	// @author Cyril Charlier <ccharlier@absystech.fr>
	private $id_stock;
	private $id_print_consommable;

	public function setUp(){
		ATF::db()->begin_transaction(true);

	}
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
		ATF::$msg->getNotices();

	}
	private function environnement_test(){
		ATF::db()->truncate("print_etat_consommable");
		$this->id_stock =ATF::stock()->insert(array(
			"libelle" =>"print stock test TU",
			"ref"=>'AZE123654',
			'adresse_mac'=>"ABZNE22",
			'quantite'=> 1
		));
		$this->id_print_consommable= ATF::print_consommable()->insert(array(
			"designation" =>"TU consommable test",
			"ref_stock"=>'AZE123654',
			'duree'=>"15",
			'prix'=> 123.00,
			'couleur'=>'autre'
		));
	}
	public function test_table(){
		$this->assertEquals("print_etat_consommable",$this->obj->name(),"Probleme de nom de classe print_etat");
	}
	
	public function test_GETWithIdStock(){
		$this->environnement_test();
		$date = new DateTime();
		$id_stock2 =ATF::stock()->insert(array(
			"libelle" =>"print stock test TU 2",
			"ref"=>'9856TUDH0235',
			'adresse_mac'=>"956LDAP3",
			'quantite'=> 1
		));
		ATF::print_etat_consommable()->i(array(
			"date"=> date_format($date,'Y-M-d H:i:s'),
			'id_stock'=> $this->id_stock,
			'id_print_consommable'=> $this->id_print_consommable
		));
		ATF::print_etat_consommable()->i(array(
			"date"=> date_format($date,'Y-M-d H:i:s'),
			'id_stock'=> $id_stock2,
			'id_print_consommable'=> $this->id_print_consommable
		));
		
		$get = array('id_stock' => $this->id_stock);
		$ret =ATF::print_etat_consommable()->_GET($get);
		$this->assertEquals(1,count($ret),"Probleme du nombre de retour get print etat consommable");
		$this->assertEquals("TU consommable test",$ret[0]["designation"],"Probleme de retour designation print etat consommable");
		$this->assertEquals(15,$ret[0]["duree"],"Probleme de retour duree print etat consommable");
		$this->assertEquals("autre",$ret[0]["couleur"],"Probleme de retour designation print etat consommable");

	}
	public function test_POSTError(){
		$this->environnement_test();	
		try {
			$post = array(
				'id_stock'=> '',
				'id_print_consommable'=>''		
			);
            ATF::print_etat_consommable()->_POST('',$post);
        } catch (errorATF $e) {
            $error = $e->getCode();
        }
        $this->assertEquals(500,$error,'problème sur post lorsqu il n y a pas de field');

	}
	public function test_POST(){
		$this->environnement_test();
		$date = new DateTime();
		$post = array(
			'id_stock'=> $this->id_stock,
			'date'=> date_format($date,'Y-M-d H:i:s'),	
			'id_print_consommable'=>$this->id_print_consommable,

		);
        $res = ATF::print_etat_consommable()->_POST('',$post);
		$etat_consommables = ATF::print_etat_consommable()->_GET();
		$this->assertEquals($res['id_print_etat_consommable'],$etat_consommables[0]['id_print_etat_consommable'],"l'id ne correspond pas ");
	}
}
?>