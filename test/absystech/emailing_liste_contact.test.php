<?
include_once "emailing.test.php";

class emailing_liste_contact_test extends emailing_test {	
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 08-12-2010
	*/ 
	function setUp() {
		parent::setUp("emailing_source,emailing_contact,emailing_liste");
 		$this->obj = ATF::emailing_liste_contact();		
	}
	
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 08-12-2010
	*/ 
	function tearDown() {
		ATF::db()->rollback_transaction(true);
	}

	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 08-12-2010
	*/ 
	public function test_nbMail() {
		$r = $this->obj->nbMail($this->el['id_emailing_liste']);
		$this->assertEquals(8,$r);
	}

};
?>