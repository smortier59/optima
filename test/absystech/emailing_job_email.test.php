<?
include_once "emailing.test.php";

class emailing_job_email_test extends emailing_test {	
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 17-11-2010
	*/ 
	function setUp() {
		parent::setUp("emailing_projet,emailing_source,emailing_contact,emailing_liste,emailing_liste_contact,emailing_job,emailing_job_email");
 		$this->obj = ATF::emailing_job_email();		
	}
	
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 17-11-2010
	*/ 
	function tearDown() {
		ATF::db()->rollback_transaction(true);
	}

	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 17-11-2010
	*/ 
	function test_updateError() {
		$this->assertFalse($this->obj->updateError(),"Ah ouais ? Seriously ?");
		
		$d = array(
			"id_emailing_job"=>md5($this->ej['id_emailing_job'])
			,"id_emailing_liste_contact"=>md5($this->ecl[0]['id_emailing_liste_contact'])
			,"permanent_failure"=>"5.0.0"
			,"persistent_failure"=>"4.0.0"
			,"success"=>"2.0.0"
			,"erreur_brute"=>"ERREUR BRUT POUR TEST TU"
		);

		$this->assertEquals(1,$this->obj->updateError($d),"L'enregistrement n'a pas été modifié");
		$this->assertEquals($d['permanent_failure'],$this->obj->select($this->eje[0]['id_emailing_job_email'],"permanent_failure"),"La permanent failure ne s'est pas modifié");
		$this->assertEquals($d['persistent_failure'],$this->obj->select($this->eje[0]['id_emailing_job_email'],"persistent_failure"),"La persistent_failure ne s'est pas modifié");
		$this->assertEquals($d['success'],$this->obj->select($this->eje[0]['id_emailing_job_email'],"success"),"La success ne s'est pas modifié");
		$this->assertEquals($d['erreur_brute'],$this->obj->select($this->eje[0]['id_emailing_job_email'],"erreur_brute"),"La erreur_brute ne s'est pas modifié");
	}
	
	public function test_select_all() {
		$this->obj->q->reset();
		$r = $this->obj->select_all();
		$this->assertEquals(4,count($r),"Erreur dans le nombre de retour");
	}
	
};
?>