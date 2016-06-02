<?
class paiement_acompte_test extends ATF_PHPUnit_Framework_TestCase {
	
	// @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	private function beginTransaction($codename){		
		ATF::db()->select_db("extranet_v3_".$codename);
    	ATF::$codename = $codename;
    	ATF::db()->begin_transaction(true);		
	}

	// @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	private function RollBackTransaction($codename){	
		ATF::db()->rollback_transaction(true);
        ATF::$codename = "cleodis";
        ATF::db()->select_db("extranet_v3_cleodis");
	}


	// @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	public function test_construct(){
		$this->beginTransaction("exactitude");
		$c = new paiement_acompte();		
		
		$this->RollBackTransaction("cleodis");

		$this->assertTrue($c instanceOf paiement_acompte, "L'objet paiement_acompte n'est pas de bon type");
			
	}


	public function test_autocomplete(){
		$this->beginTransaction("exactitude");
		$c = new paiement_acompte();		

		$res = $c->autocomplete();
		$this->RollBackTransaction("cleodis");

		$data = array(0 => "6f491be0831c8bf2555239d4ed9d5db4",
					  1 => "1 000 € HT d'acompte avant démarrage de la mission",
					  2 => "",
					  "raw_0" => "10",
					  "raw_1" => "1 000 € HT d'acompte avant démarrage de la mission",
					  "raw_2" => "");

		$this->assertEquals($data[1],$res[0][1], "error autocomplete");


	}

};