<?
include_once "emailing.test.php";

class emailing_source_test extends emailing_test {	
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 17-11-2010
	*/ 
	function setUp() {
		
		parent::setUp("emailing_source");
 		$this->obj = ATF::emailing_source();		
		
	}
	
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 29-09-2010
	*/ 
	function tearDown() {
		ATF::db()->rollback_transaction(true);
	}

	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 29-09-2010
	*/ 
	public function test_select_all() {
		$this->obj->q->reset();
		$r = $this->obj->select_all();
		$this->assertArrayHasKey('nbContacts',$r[0],"Erreur il n'y a plus le nombre de contacts dans le retour.");
	}
	
    /**
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 13-06-2012
    */ 
    public function test_majListContact() {
        $this->assertNotNull($this->obj->select(15),"La source 15 n'existe plus, et c'est sur celle là qu'on fait la mise a jour pour le test");    
        ATF::setSingleton("importer",new importer_ES());
        
        $this->obj->majListContact();
        $r = ATF::$html->getTemplateVars();
        
        $this->assertEquals(18,$r["numEnrTraite"],"Mauvais retour de numEnrTraite");
        $this->assertEquals(0,$r["numUpdateOK"],"Mauvais retour de numUpdateOK");
        $this->assertEquals(1,$r["numUpdateNOK"],"Mauvais retour de numUpdateNOK");
        $this->assertEquals(0,$r["numInsertOK"],"Mauvais retour de numInsertOK");
        $this->assertEquals(4,$r["numInsertNOK"],"Mauvais retour de numInsertNOK");
        $this->assertNotNull($r["erreurs"],"Mauvais retour de erreurs");
        $this->assertEquals("",$r["champsInconnu"],"Mauvais retour de champsInconnu");
        
    }

    /**
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 27-08-2013
    */ 
    public function test_majListContactErreurChamps() {
        $this->assertNotNull($this->obj->select(15),"La source 15 n'existe plus, et c'est sur celle là qu'on fait la mise a jour pour le test");    
        ATF::setSingleton("importer",new importer_ES2());
        
        $this->obj->majListContact();
        $r = ATF::$html->getTemplateVars();
        
        $this->assertEquals("kiugutfuyhol",$r["champsInconnu"],"Mauvais retour de champsInconnu");
        
    }

	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 13-06-2012
	*/ 
	public function test_majListContactSansFichier() {
		rename(__ABSOLUTE_PATH__."test/absystech/emailing_source-test_majListContact.xls",__ABSOLUTE_PATH__."test/absystech/emailing_source-test_majListContactRENAME.xls");
		ATF::setSingleton("importer",new importer_ES());
		$erreur = false;
		try {
			$this->obj->majListContact();
		} catch (error $e) {
			$erreur = true;
			$code = $e->getCode();
		}
		$this->assertTrue($erreur,"Il manque l'erreur de fichier manquant");
		$this->assertEquals(1000,$code,"Le code d'erreur est mauvais");
		
		rename(__ABSOLUTE_PATH__."test/absystech/emailing_source-test_majListContactRENAME.xls",__ABSOLUTE_PATH__."test/absystech/emailing_source-test_majListContact.xls");
		
		
	}

};

class importer_ES extends importer {
    public function filepath() {
        return  __ABSOLUTE_PATH__."test/absystech/emailing_source-test_majListContact.xls";
    }
}
class importer_ES2 extends importer {
    public function filepath() {
        return  __ABSOLUTE_PATH__."test/absystech/emailing_source-test_majListContact2.xls";
    }
}
?>