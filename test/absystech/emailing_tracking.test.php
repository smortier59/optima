<?
include_once "emailing.test.php";

class emailing_tracking_test extends emailing_test {

	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 17-11-2010
	*/ 
	function setUp() {
		parent::setUp("emailing_projet,emailing_source,emailing_liste,emailing_lien,emailing_job,emailing_job_email,emailing_tracking");
 		$this->obj = ATF::emailing_tracking();
	}
	
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 06-12-2010
	*/ 
	function tearDown() {
		ATF::db()->rollback_transaction(true);
	}

	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 12-04-2011
	*/ 
	function test_select_all() {
		$this->obj->q->reset()->setCount();
		$r = $this->obj->select_all();
		$r = $r['data'];
		$this->assertEquals(5,count($r),"Erreur, le nombre de retour est incorrect : ".print_r($r,true));
	}

	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 12-04-2011
	*/ 
	function test_select_all2() {
		$this->obj->q->reset()->setCount();
		ATF::_r("id",$this->ej['id_emailing_job']);
		ATF::_r("parent_name",'emailing_job');
		$r = $this->obj->select_all();
		$r = $r['data'];
		$this->assertEquals(5,count($r),"Erreur, le nombre de retour est incorrect");
	}

	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 12-04-2011
	*/ 
	function test_select_all3() {
		$this->obj->q->reset()->setCount();
		ATF::_r("pager","pager_".$this->ej['id_emailing_job']);
		$r = $this->obj->select_all();
		$r = $r['data'];
		$this->assertEquals(5,count($r),"Erreur, le nombre de retour est incorrect");
	}

	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 12-04-2011
	*/ 
	function test_select_all4() {
		$this->obj->q->reset()->setCount();
		ATF::_r("id_emailing_job",$this->ej['id_emailing_job']);
		$r = $this->obj->select_all();
		$r = $r['data'];
		$this->assertEquals(5,count($r),"Erreur, le nombre de retour est incorrect");
	}

	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 02-05-2014
	*/ 
	function test_tauxPenetration() {
		$r = $this->obj->tauxPenetration($this->ej['id_emailing_job']);
		$this->assertEquals(100,$r,"1 Erreur, le tauxPenetration est incorrect");
		$r = $this->obj->tauxPenetration($this->ej['id_emailing_job'],false);
		$this->assertEquals(4,$r,"2 Erreur, le tauxPenetration est incorrect");
	}



};
?>