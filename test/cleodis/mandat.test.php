<?
class mandat_test extends ATF_PHPUnit_Framework_TestCase {
	
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
		$this->beginTransaction("cap");
		$c = new mandat_cap();		
		
		$this->RollBackTransaction("cleodis");

		$this->assertTrue($c instanceOf mandat, "L'objet mandat n'est pas de bon type");
			
	}


	public function test_default_value(){
		$this->beginTransaction("cap");
		$c = new audit();	
		$m = new mandat_cap();

		$data = array("ref"=> "123456",
					  "id_user"=> 1,
					  "type"=>"gestion_poste",
					  "id_societe"=>9969,
					  "date"=>"2015-10-26"
					 );

		$id_audit = $c->insert(array("audit"=>$data, "tu"=>true));

		$audit = $c->select($id_audit);


		ATF::_r("id_audit", $id_audit);

		$dv1 = $m->default_value("ref");
		$dv2 = $m->default_value("id_societe");
		$dv3 = $m->default_value("id_affaire");
		$dv4 = $m->default_value("date");
		$dv5 = $m->default_value("date_envoi");

		$this->RollBackTransaction("cleodis");

		$this->assertEquals("123456", $dv1 , "default_value 1 erreur");
		$this->assertEquals(9969, $dv2 , "default_value 2 erreur");
		$this->assertEquals($audit["id_affaire"], $dv3 , "default_value 3 erreur");
		$this->assertEquals(date("Y-m-d"), $dv4 , "default_value 4 erreur");
		$this->assertEquals(NULL, $dv5 , "default_value 5 erreur");

	}
	
	
	public function test_insert(){
		$this->beginTransaction("cap");
		$c = new audit();	
		$m = new mandat_cap();

		$data = array("ref"=> "123456",
					  "id_user"=> 1,
					  "type"=>"gestion_poste",
					  "id_societe"=>9969,
					  "date"=>"2015-10-26"
					 );

		$id_audit = $c->insert(array("audit"=>$data, "tu"=>true));

		$audit = $c->select($id_audit);



		$mandat = array("ref"=>"123456",
						"id_societe"=>9969,
						"id_affaire"=>$audit["id_affaire"],
						"date"=>date("Y-m-d"),
						"id_audit"=>$id_audit,
						"indemnite_retard"=>300,
						"contact"=>array(9509,9510)
					   );
		
		$id_mandat = $m->insert(array("mandat"=>$mandat, "preview"=>true, "tu"=>true));

		$this->RollBackTransaction("cleodis");

		
	}


	public function test_update(){
		$this->beginTransaction("cap");
		$c = new audit();	
		$m = new mandat_cap();
	

		$data = array("ref"=> "123456",
					  "id_user"=> 1,
					  "type"=>"gestion_poste",
					  "id_societe"=>9969,
					  "date"=>"2015-10-26"
					 );

		$id_audit = $c->insert(array("audit"=>$data, "tu"=>true));

		$audit = $c->select($id_audit);




		$mandat = array("ref"=>"123456",
						"id_societe"=>9969,
						"id_affaire"=>$audit["id_affaire"],
						"date"=>date("Y-m-d"),
						"id_audit"=>$id_audit,
						"indemnite_retard"=>300,
						"contact"=>array(9509,9510)
					   );

		$id_mandat = $m->insert(array("mandat"=>$mandat, "tu"=>true));

		$mandat["id_mandat"]=$id_mandat;
		$mandat["indemnite_retard"]=500;

		$id_mandat = $m->update(array("mandat"=>$mandat,"preview"=>true, "tu"=>true));
		$id_mandat = $m->update(array("mandat"=>$mandat, "tu"=>true));

		$mandat = $m->select($id_mandat);

		$this->RollBackTransaction("cleodis");

		$this->assertEquals(500, $mandat["indemnite_retard"], "Update Incorrect !");
	}


	public function test_updateDate(){
		$this->beginTransaction("cap");
		$c = new audit();	
		$m = new mandat_cap();

		$data = array("ref"=> "123456",
					  "id_user"=> 1,
					  "type"=>"gestion_poste",
					  "id_societe"=>9969,
					  "date"=>"2015-10-26"
					 );

		$id_audit = $c->insert(array("audit"=>$data, "tu"=>true));

		$audit = $c->select($id_audit);

		$mandat = array("ref"=>"123456",
						"id_societe"=>9969,
						"id_affaire"=>$audit["id_affaire"],
						"date"=>date("Y-m-d"),
						"id_audit"=>$id_audit,
						"indemnite_retard"=>300
					   );

		$id_mandat = $m->insert(array("mandat"=>$mandat, "tu"=>true));
		
		ATF::societe()->u(array("id_societe"=> 9969, "relation"=>"prospect"));

		$d = array("id_mandat"=>$id_mandat,
				   "key"=> "date_retour",
				   "value"=> date("Y-m-d")
				  );

		$m->updateDate($d);

		$notices = ATF::$msg->getNotices();

		$this->RollBackTransaction("cleodis");

		$this->assertEquals(1, count($notices), "Erreur de notices incorrect");
	}


	
};