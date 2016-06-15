<?
class asterisk_test extends ATF_PHPUnit_Framework_TestCase {	
	public function setUp() {			
		ATF::initialize();
		//Création d'un serveur et d'un téléphone
		$this->initUser();
		ATF::societe()->u(array("id_societe" => $this->id_societe,"tel"=>"1234567890"	));
	}

	public function tearDown(){
		ATF::db()->rollback_transaction();		
	}

	// @author Yann GAUTHERON <ygautheron@absystech.fr>
	public function test_originate(){
		ATF::asterisk()->update(array(
			"id_asterisk"=>1,
			"url_webservice"=>"http://dev.optima.absystech.net/TU_asterisk." // devient TU_asterisk.originate
		));
		$result = $this->obj->originate(5,123);
		$this->assertTrue($result,"Appel non lance");
	}
	
	// @author Yann GAUTHERON <ygautheron@absystech.fr>
	public function test_webservice(){
		ATF::db()->begin_transaction(true);
		$post = array(
			"server"=>array(
				"script.php",
				"0320579307",
				"getCallerName"
			)
		);
		try {
			$this->assertEquals('SET VARIABLE getCallerName "SAFER Flandres Artois (DIERCKENS Olivier)"'."\n",$this->obj->webservice($post),"Retour variable incorrect");
			
			$post["server"][1]="/tmp/touched";
			$post["server"][2]="insertHotline";
            $post["server"][3]="04060047";
			$files = array(array("tmp_name"=>"/tmp/touched"));
			$this->assertEquals('SET VARIABLE insertHotline ""'."\n",$this->obj->webservice($post,$s,$files),"Retour variable 2 incorrect");
            $n = ATF::$msg->getNotices();
			$this->assertEquals(1, count($n), "Nombre de notices incorrect");
		} catch (Exception $e) {
            log::logger("ERREUR DANS LES TU BORDEL","asterisk.log"); 
            log::logger($e->getMessage(),"asterisk.log"); 
		    log::logger($e->getTraceAsString(),"asterisk.log"); 
        }
		ATF::db()->rollback_transaction(true);
	}
	
	// @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	public function test_checkHotlineFromId(){
		$this->assertEquals("nok", $this->obj->checkHotlineFromId(100000000) , "La requete 100000000 existe ??");
		$this->assertEquals("ferme", $this->obj->checkHotlineFromId(11265) , "La requete 8526 n'est pas terminée ??");
		
		$id = ATF::hotline()->i(array("id_societe" => 1, "hotline"=> "Hotline tu" , "pole_concerne" => "dev"));
		$this->assertEquals("Hotline tu", $this->obj->checkHotlineFromId($id) , "La requete 8526 n'est pas terminée ??");
	}
	
	// @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	public function test_getCodenameFromHotline(){
		$this->assertEquals("att", $this->obj->getCodenameFromHotline(8526) , "La requete est ouverte chez AT??");
		
		ATF::hotline()->q->reset()->where("etat" , "fixing")->setLimit(1);
		$hotline = ATF::hotline()->select_row();		
		$this->assertEquals("absystech", $this->obj->getCodenameFromHotline($hotline["id_hotline"]) , "La requete est ouverte chez AT??");
	}
	
	// @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	public function test_getSIPFromHotline(){
		$this->assertEquals("nok", $this->obj->getSIPFromHotline(100000000) , "La requete 100000000 est dispo??");
		
		$id = ATF::hotline()->i(array("id_societe" => 1, "hotline"=> "Hotline tu" , "pole_concerne" => "dev"));
		$this->assertEquals("nouser", $this->obj->getSIPFromHotline($id) , "La requete a un utilisateur??");
		
		ATF::hotline()->u(array("id_hotline" => $id , "id_user" => 13));
		$this->assertEquals("nophone", $this->obj->getSIPFromHotline($id) , "L'utilisateur 13  a un id_phone??");
		
		ATF::hotline()->u(array("id_hotline" => $id , "id_user" => 29));
		$this->assertEquals("nosip", $this->obj->getSIPFromHotline($id) , "L'utilisateur 29 a un id_phone avec phone??");
		
		ATF::hotline()->u(array("id_hotline" => $id , "id_user" => 1));
		$this->assertEquals(41, $this->obj->getSIPFromHotline($id) , "L'utilisateur 1 n'a pas un id_phone avec phone??");
	}
	
	// @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	public function test_getContactFromCallerId(){
		$this->assertEquals(2360, $this->obj->getContactFromCallerId("0383909095" , true) , "Mme Garcia a changé de numéro ?? id_contact 2360");
	}
	
	// @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	public function test_getSocieteFromCallerId(){
		$this->assertEquals(1154, $this->obj->getSocieteFromCallerId("0320509902" , true) , "1 - Absystech Telecom a changé de numéro ??");		
		$this->assertEquals(array("id_societe" => $this->id_societe , "societe" => "TestTU" , "societe.id_societe"=>$this->id_societe), $this->obj->getSocieteFromCallerId("1234567890") , "2 - Absystech Telecom a changé de numéro ??");
		
	}
	
	// @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	public function test_NumSansUser(){
		$this->assertEquals("TestTU" , $this->obj->getCallerName("1234567890") , "2 - Absystech Telecom a changé de numéro ??");
	}
	
	// @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	// @author Yann GAUTHERON <ygautheron@absystech.fr>
	public function test_getCreditFromRef(){
		$this->assertEquals("nok", ATF::asterisk()->getCreditFromRef("PLOP99999999"), "Devrait retourner nok car pas de société avec cette ref !");			
		$this->assertEquals("0.00", ATF::asterisk()->getCreditFromRef("04060047"));
		$this->assertEquals(1, count(ATF::$msg->getNotices()), "Nombre de notices incorrect");
		
		/*ATF::db()->select_db("extranet_v3_att");
		try{
			$this->assertEquals("0.00", ATF::asterisk()->getCreditFromRef("09120011"));
		}catch(errorATF $e){
			echo $e->getTraceAsString();
		}		
		ATF::db()->select_db("extranet_v3_absystech");
		$this->assertEquals(1, count(ATF::$msg->getNotices()), "Nombre de notices incorrect");*/
	}
	
	// @author Yann GAUTHERON <ygautheron@absystech.fr>
	public function test_callCancelled(){
		$this->assertNull(ATF::asterisk()->callCancelled("PLOP99999999"));			
		$this->assertEquals(0, count(ATF::$msg->getNotices()), "Nombre de notices incorrect");
		
		$this->assertNull(ATF::asterisk()->callCancelled("04060047"));
		$this->assertEquals(1, count(ATF::$msg->getNotices()), "Nombre de notices incorrect");
		
		/*ATF::db()->select_db("extranet_v3_att");
		try{
			$this->assertNull(ATF::asterisk()->callCancelled("09120011"));
		}catch(errorATF $e){
			echo $e->getTraceAsString();
		}		
		ATF::db()->select_db("extranet_v3_absystech");
		$this->assertEquals(1, count(ATF::$msg->getNotices()), "Nombre de notices incorrect");*/
	}
	
	// @author Yann GAUTHERON <ygautheron@absystech.fr>
	public function test_insertHotline(){
		$files = array();
		$id_hotline = ATF::asterisk()->insertHotline("09120011",$files,"dev");
		$this->assertEquals("SLI09120011",ATF::societe()->select(ATF::hotline()->select($id_hotline,"id_societe"),"ref"));	
		
		$this->assertEquals("ok",ATF::asterisk()->insertInteraction($id_hotline,$files));	
		$this->assertEquals(3, count(ATF::$msg->getNotices()), "Nombre de notices incorrect");
		
		// Ajouter check des fichiers si nécessaire...	
	}
}