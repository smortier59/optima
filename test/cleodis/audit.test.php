<?
class audit_test extends ATF_PHPUnit_Framework_TestCase {

	// @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	private function beginTransaction($codename){
		ATF::db()->select_db("optima_".$codename);
    	ATF::$codename = $codename;
    	ATF::db()->begin_transaction(true);
	}

	// @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	private function RollBackTransaction($codename){
		ATF::db()->rollback_transaction(true);
        ATF::$codename = "cleodis";
        ATF::db()->select_db("optima_cleodis");
	}


	// @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	public function test_construct(){
		$this->beginTransaction("cap");
		$c = new audit();

		$this->RollBackTransaction("cleodis");

		$this->assertTrue($c instanceOf audit, "L'objet audit n'est pas de bon type");

	}

	// @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	public function test_insert_preview(){
		$this->beginTransaction("cap");
		$c = new audit();

		$data = array("ref"=> "123456",
					  "id_user"=> 1,
					  "type"=>"gestion_poste",
					  "id_societe"=>9969,
					  "date"=>"2015-10-26",
					  "contact"=>array(9509,9510,9511)
					 );

		$id = $c->insert(array("audit"=>$data,"preview"=>true, "tu"=>true));

		$this->RollBackTransaction("cleodis");
		$this->assertNotNull($id, "Erreur insert");


	}



	// @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	public function test_update(){
		$this->beginTransaction("cap");
		$c = new audit();

		$data = array("ref"=> "ReftestAudit",
					  "id_user"=> 1,
					  "type"=>"gestion_poste",
					  "id_societe"=>9969,
					  "date"=>"2015-10-26",
					  "contact"=>array(9509,9510,9511)
					 );

		$id = $c->insert(array("audit"=>$data, "tu"=>true));

		$data["id_audit"] = $id;
		$data["ref"] = "RefAudit2";

		$id = $c->update(array("audit"=>$data, "tu"=>true));

		$this->RollBackTransaction("cleodis");
		$this->assertNotNull($id, "Erreur insert");


	}


	// @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	public function test_update_preview(){
		$this->beginTransaction("cap");
		$c = new audit();

		$data = array("ref"=> "ReftestAudit",
					  "id_user"=> 1,
					  "type"=>"gestion_poste",
					  "id_societe"=>9969,
					  "date"=>"2015-10-26",
					  "contact"=>array(9509,9510,9511)
					 );

		$id = $c->insert(array("audit"=>$data,  "tu"=>true));

		$data["id_audit"] = $id;
		$data["ref"] = "RefAudit2";

		$id = $c->update(array("audit"=>$data,"preview"=>true, "tu"=>true));

		$this->RollBackTransaction("cleodis");
		$this->assertNotNull($id, "Erreur insert");


	}

	// @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	/*public function test_can_delete(){
		$this->beginTransaction("cap");

		$c = new audit();

		$this->assertEquals(false, $c->can_delete(1),"Can delete incorrect");

		$this->RollBackTransaction("cleodis");
	}*/

	// @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	public function test_can_update(){
		$this->beginTransaction("cap");
		$c = new audit();

		$data = array("ref"=> "123456",
					  "id_user"=> 1,
					  "type"=>"gestion_poste",
					  "id_societe"=>9969,
					  "date"=>"2015-10-26"
					 );

		$id = $c->insert(array("audit"=>$data, "tu"=>true));

		$retour = $c->can_update($id);


		$c->u(array("id_audit"=>$id, "etat"=>"perdu"));

		try{
			$c->can_update($id);
		} catch (errorATF $e) {
			$error = $e->getMessage();
		}
		$this->RollBackTransaction("cleodis");

		$this->assertEquals(true, $retour, "Erreur can update");
		$this->assertEquals("Impossible de modifier/supprimer ce Audit car il n'est plus en 'En attente'",$error,"Erreur can update 2");


	}

	// @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	public function test_getRef(){
		$this->beginTransaction("cap");
		$c = new audit();

		$data = array("ref"=> "12345",
					  "id_user"=> 1,
					  "type"=>"gestion_poste",
					  "id_societe"=>9969,
					  "date"=>"2015-10-26"
					 );

		$id = $c->insert(array("audit"=>$data, "tu"=>true));
		$ref = $c->getRef("2015-10-26");

		$c->u(array("id_audit"=>$id, "ref"=>"1510001"));
		$ref1 = $c->getRef("2015-10-26");

		$c->u(array("id_audit"=>$id, "ref"=>"1510010"));
		$ref2 = $c->getRef("2015-10-26");


		$c->u(array("id_audit"=>$id, "ref"=>"1510100"));
		$ref3 = $c->getRef("2015-10-26");



		$this->RollBackTransaction("cleodis");

		$this->assertEquals("1510001",$ref, "GetRef 1 incorrect");
		$this->assertEquals("1510002",$ref1, "GetRef 2 incorrect");
		$this->assertEquals("1510011",$ref2, "GetRef 3 incorrect");
		$this->assertEquals("1510101",$ref3, "GetRef 4 incorrect");
	}

	// @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	public function test_perdu(){
		$this->beginTransaction("cap");
		$c = new audit();

		$data = array("ref"=> "12345",
					  "id_user"=> 1,
					  "type"=>"gestion_poste",
					  "id_societe"=>9969,
					  "date"=>"2015-10-26"
					 );

		$id = $c->insert(array("audit"=>$data, "tu"=>true));

		$c->perdu(array("id_audit"=>$id));

		$audit = $c->select($id);

		$c->u(array("id_audit"=>$id, "etat"=>"signe"));

		try{
			$c->perdu(array("id_audit"=>$id));
		}catch(errorATF $e){
			$erreur = $e->getMessage();
		}

		ATF::$msg->getNotices();

		$this->RollBackTransaction("cleodis");

		$this->assertEquals("perdu",$audit["etat"], "Audit pas passé en perdu !");
		$this->assertEquals("Impossible de passer un audit gagnée en 'perdu'",$erreur, "Pas d'erreur ??");
	}



};