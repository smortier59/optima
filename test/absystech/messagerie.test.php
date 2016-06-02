<?

class messagerie_test extends ATF_PHPUnit_Framework_TestCase {
	
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	*/ 
	function setUp() {
		$this->begin_transaction(true);
 		$this->obj = ATF::messagerie();
		
		$this->obj->truncate();

		$this->msg[0] = array(
			'subject'=>"Sujet MSG 1 TU",
			'from'=>"from MSG 1 TU",
			'to'=>"to MSG 1 TU",
			'date'=>date('Y-m-d'),
			'message_id'=>1,
			'size'=>666,
			'uid'=>1,
			'msgno'=>1,
			'recent'=>1,
			'flagged'=>0,
			'answered'=>0,
			'deleted'=>0,
			'seen'=>0,
			'draft'=>0,
			'udate'=>date('Y-m-d'),
			'id_user'=>ATF::$usr->getId()
		);
		$this->msg[0]['id_messagerie'] = $this->obj->i($this->msg[0]);
		
	}
	
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	*/ 
	function tearDown() {
		ATF::db()->rollback_transaction(true);
	}

	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	*/ 
	public function test_select_all() {
		$r = $this->obj->select_all();

		$this->assertArrayHasKey("msgno",$r[0],"La clé msgno n'est pas présente");
		$this->assertArrayHasKey("messagerie.attachmentsRealName",$r[0],"La clé messagerie.attachmentsRealName n'est pas présente");
		$this->assertEquals(1,count($r),"1 seul message présent dans la BDD, le compte n'est pas bon");
	}

	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	*/ 
	public function test_isSync() {
		$this->assertTrue($this->obj->isSync(ATF::$usr->getId(),1),"Message déjà synchronisé pourtant");
		$this->assertFalse($this->obj->isSync(ATF::$usr->getId(),2),"Message non synchronisé pourtant");

	}

	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	*/ 
	public function test_getLasitUID() {
		$this->assertEquals(1,$this->obj->getLasitUID(),"Last UID mauvais");
	}

};
?>