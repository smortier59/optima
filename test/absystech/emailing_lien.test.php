<?
include_once "emailing.test.php";

class emailing_lien_test extends emailing_test {	
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 17-11-2010
	*/ 
	function setUp() {
		parent::setUp("emailing_lien");
 		$this->obj = ATF::emailing_lien();		
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
	public function test_default_value() {
		$r = $this->obj->default_value("url");
		$this->assertEquals("http://",$r,"Erreur de default value 1");
		$this->assertNull($this->obj->default_value("phoque"),"Erreur de default value 2");
	}

	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 07-12-2010
	*/ 
	public function test_iFromPlugin() {
		try {		
			$this->assertFalse($this->obj->iFromPlugin(),"Insertion sans aucunes infos ? Strange.");
		} catch (error $e) {
			$this->assertNotNull($e->getMessage(),"Le throw ne s'est pas déclenché, couille dans le paté !");
		}
		$this->assertFalse($this->obj->iFromPlugin(array("emailing_lien"=>array())),"Insertion sans aucunes infos ? Strange.");
		
		$r =$this->obj->iFromPlugin(array("emailing_lien"=>array("emailing_lien"=>"www.absystech.net/")));
		$this->assertNotNull($r['id'],"Erreur d'insertion, il n'y a pas d'id en retour");
		$this->assertEquals("http://www.absystech.net/",$r['lib'],"Erreur de retour, il n'y a pas de lib en retour");
	}

	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 07-12-2010
	*/ 
	public function test_autocomplete() {
		$infos['condition_value'] = "lienTU";
		$r =$this->obj->autocomplete($infos);
		$this->assertEquals(4,count($r),"Pas le bon nombre de retour");
		$this->assertNotEquals(32,strlen($r[0]['id']),"Erreur, l'ID ne doit pas être crypté en retour");
	}



};
?>