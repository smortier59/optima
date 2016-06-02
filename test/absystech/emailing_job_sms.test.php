<?
include_once "emailing.test.php";

class emailing_job_sms_test extends emailing_test {	
	/**
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @date 24-10-2014
	*/ 
	function setUp() {
		ATF::db()->begin_transaction(true);
		//parent::setUp("emailing_projet,emailing_source,emailing_contact,emailing_liste,emailing_liste_contact,emailing_job,emailing_job_sms");
 		//$this->obj = ATF::emailing_job_sms();		
	}
	
	/**
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @date 24-10-2014
	*/ 
	function tearDown() {
		ATF::db()->rollback_transaction(true);
	}

	function test_construct() {
		$d = new emailing_job_sms();
	}

	/**
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @date 24-10-2014
	*/ 
	function test_updateError() {
		$this->assertFalse($this->obj->updateError(),"Ah ouais ? Seriously ?");
		
		$d = array(
			"id_emailing_job"=>md5(106)
			,"id_emailing_liste_contact"=>md5(75218)
			,"failure"=>"5.0.0"
			,"success"=>"400"
			,"erreur_brute"=>"ERREUR BRUT POUR TEST TU"
		);

		$this->assertEquals(1,$this->obj->updateError($d),"L'enregistrement n'a pas été modifié");
		$this->assertEquals($d['failure'],$this->obj->select(14,"failure"),"La failure ne s'est pas modifié");
		$this->assertEquals($d['success'],$this->obj->select(14,"success"),"La success ne s'est pas modifié");
		$this->assertEquals($d['erreur_brute'],$this->obj->select(14,"erreur_brute"),"La erreur_brute ne s'est pas modifié");
	}
	
	/**
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @date 24-10-2014
	*/ 
	public function test_select_all() {
		$this->obj->q->reset();
		$r = $this->obj->select_all();
		$this->assertEquals(1,count($r),"Erreur dans le nombre de retour");
	}
	
};
?>