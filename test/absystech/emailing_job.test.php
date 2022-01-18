<?
include_once "emailing.test.php";

class emailing_job_test extends emailing_test {

	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 17-11-2010
	*/ 
	function setUp() {
		parent::setUp("emailing_projet,emailing_source,emailing_contact,emailing_liste,emailing_job,emailing_job_email");
 		$this->obj = ATF::emailing_job();
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
	* @date 06-12-2010
	*/
	function test_select_all() {

		$this->ej2 = array("emailing_job"=>"Job TU 2","id_emailing_projet"=>$this->ep['id_emailing_projet'],"id_emailing_liste"=>$this->el['id_emailing_liste'],"depart"=>date("Y-m-d",strtotime("-3 day")),"nbMailToSend"=>123);
		$this->ej2['id_emailing_job'] = ATF::emailing_job()->i($this->ej2);

		$this->obj->q->setCount();
		$this->obj->q->addField("emailing_job.id_emailing_liste","emailing_job.id_emailing_liste_fk");
		$r = $this->obj->select_all();

		foreach ($r['data'] as $k=>$i) {
			$this->assertArrayHasKey("nbSent",$i,"Il manque le nbSent dans le retour");
			$this->assertArrayHasKey("nbToSend",$i,"Il manque le nbToSend dans le retour");
			$this->assertArrayHasKey("nbClic",$i,"Il manque le nbClic dans le retour");
			$this->assertArrayHasKey("tauxClic",$i,"Il manque le tauxClic dans le retour");
			$this->assertArrayHasKey("tauxRetour",$i,"Il manque le tauxRetour dans le retour");
			$this->assertArrayHasKey("nbRetour",$i,"Il manque le nbRetour dans le retour");
			$this->assertArrayHasKey("tauxPenetration",$i,"Il manque le tauxPenetration dans le retour");
		}
	}
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 06-12-2010
	*/ 
	function test_select() {
		$r = $this->obj->select($this->ej['id_emailing_job']);

		$this->assertEquals($this->ej['id_emailing_job'],$r['id_emailing_job'],"Ce n'est pas le bon ID en retour");
		$this->assertArrayHasKey('nbSent',$r,"Rien en nbSent");
		$this->assertNotNull('nbClic',$r,"Rien en nbClic");
		$this->assertNotNull('tauxClic',$r,"Rien en tauxClic");
		$this->assertNotNull('nbRetour',$r,"Rien en nbRetour");
		$this->assertNotNull('tauxRetour',$r,"Rien en tauxRetour");
		$this->assertNotNull('tauxPenetration',$r,"Rien en tauxPenetration");

		$r = $this->obj->select($this->ej['id_emailing_job'],"emailing_job");
		$this->assertEquals("Job TU 1",$r,"Erreur sur la selection d'un seul field");
	}

	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 06-12-2010
	*/ 
	function test_insert() {
		$today  = mktime(date('H'), date('i'), date('s'), date("m")  , date("d"), date("Y"));
		$beforeToday = mktime(date('H'), date('i'), date("s")-5000, date("m")-1, date("d"),   date("Y"));

		$jobToday = array(
			"emailing_job"=>"TEST INSERT TU JOB"
			,"id_emailing_projet"=>$this->ep['id_emailing_projet']
			,"id_emailing_liste"=>$this->el['id_emailing_liste']
			,"depart"=>date("Y-m-d H:i:s",$today)
		);
		ATF::define("testsUnitaires",false);
		try {
			$r = $this->obj->insert($jobToday);
		} catch (errorATF $e) {
			ATF::define("testsUnitaires",true);
			throw $e;
		}
		ATF::define("testsUnitaires",true);
		$this->assertNotNull($r,"Erreur de création du Job Today");
		
		$jobBeforeToday = array(
			"emailing_job"=>"TEST INSERT TU JOB"
			,"id_emailing_projet"=>$this->ep['id_emailing_projet']
			,"id_emailing_liste"=>$this->el['id_emailing_liste']
			,"depart"=>date("Y-m-d H:i:s",$beforeToday)
		);

		try {
			$this->obj->insert($jobBeforeToday);
		} catch (errorATF $e) {
			$this->assertNotNull($e->getMessage(),"Le throw ne s'est pas déclenché, couille dans le paté !");
		}
	}

	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 06-12-2010
	*/
	function test_getCLS() {
		$this->assertEquals(12,$this->obj->getCLS(),"Erreur : La variable idConstanteLastSpeedmail a été modifié");
	}
 
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 06-12-2010
	*/
	function test_toSent() {
		$this->obj->majEtatSending();
		$r = $this->obj->toSent(true);
		$this->assertEquals(4,$r,"Erreur : Le nombre de mails a envoyé n'est pas bon (4!=".$r.", il y a peut être un Speedmail en cours d'envoi a l'heure ou les TU sont éxécuté.");
	}

	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 06-12-2010
	*/
	function test_majEtatSending() {
		$r = $this->obj->majEtatSending();
		$this->assertEquals(1,$r,"Erreur : il ne devrait y avoir qu'un job d'impacter");
	}
 
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 06-12-2010
	*/ 
	function test_majEtatSent() {
		$r = $this->obj->majEtatSent();
		$this->assertFalse($r,"Erreur : Pas d'email a envoyer, pas de retour");
		
		$this->obj->majEtatSending();
		$this->obj->send(150,true);
		$this->assertEquals("sent",$this->obj->select($this->ej['id_emailing_job'],'etat'),"Erreur : Il devrait y avoir un seul projet a passer a l'état sent");
	}

	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 06-12-2010
	*/
	function test_send() {		
		// Pour tester l'erreur des noms de domaine
		$this->ec[0]['email'] = "oiujkbk@iufgerlgv.losiughkh.liushdvku.com";
		ATF::emailing_contact()->u($this->ec[0]);
	
		$this->obj->majEtatSending();
		$this->obj->send(150,true);
		// Etat du projet : SENT
		$this->assertEquals("sent",$this->obj->select($this->ej['id_emailing_job'],'etat'),"Erreur : Il devrait y avoir un seul projet a passer a l'état sent");
		// Sollicitation contact
		$this->assertEquals(1,ATF::emailing_contact()->select($this->ec[5]['id_emailing_contact'],'sollicitation'),"Erreur : Il devrait y avoir une sollicitation sur le contact 5");
		$this->assertEquals(1,ATF::emailing_contact()->select($this->ec[6]['id_emailing_contact'],'sollicitation'),"Erreur : Il devrait y avoir une sollicitation sur le contact 6");

		// Sollicitation liste 
		$this->assertEquals(4,ATF::emailing_liste()->select($this->el['id_emailing_liste'],'sollicitation'),"Erreur : Il devrait y avoir 4 sollicitations sur la liste");
		//Sollicitation liste_contact
		ATF::emailing_liste_contact()->q->reset()
			->where("id_emailing_liste",$this->el['id_emailing_liste'])
			->where("id_emailing_contact",$this->ec[5]['id_emailing_contact'])
			->setDimension('row');

		$this->assertEquals(1,ATF::emailing_contact()->sa(),"Erreur : Il devrait y avoir une sollicitation sur le liste_contact 5");
		ATF::emailing_liste_contact()->q->reset()
			->where("id_emailing_liste",$this->el['id_emailing_liste'])
			->where("id_emailing_contact",$this->ec[6]['id_emailing_contact'])
			->setDimension('row');
		$this->assertEquals(1,ATF::emailing_contact()->sa(),"Erreur : Il devrait y avoir une sollicitation sur le liste_contact 6");
		
		// Test sur les Emailing job email
		ATF::emailing_job_email()->q->reset()->where("id_emailing_job",$this->ej['id_emailing_job']);
		$sa = ATF::emailing_job_email()->sa();

		$this->assertEquals(8,count($sa),"Erreur sur le nombre demailing_job_email créer");
//		$this->assertEquals("ERROR (#3) : Domaine invalide iufgerlgv.losiughkh.liushdvku.com",$sa[0]['erreur_brute'],"Le nom de domaine doit faire une erreur et la il n'y en a pas !");
	}
 
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 06-12-2010
	*/
	public function test_sendTRYCATCH() {		
		// Pour tester l'erreur des noms de domaine
		$this->ec[4]['email'] = "Phoque !";
		ATF::emailing_contact()->q->reset()->Where("id_emailing_contact",$this->ec[4]['id_emailing_contact'])->addValues($this->ec[4]);
		ATF::db()->update(ATF::emailing_contact());
		$this->obj->majEtatSending();
		$this->obj->send(150,true);

		ATF::emailing_liste_contact()->q->reset()
			->addField('id_emailing_liste_contact')
			->where("id_emailing_liste",$this->el['id_emailing_liste'])
			->where("id_emailing_contact",$this->ec[4]['id_emailing_contact'])
			->setDimension('cell');
		ATF::emailing_job_email()->q->reset()->where("id_emailing_job",$this->ej['id_emailing_job'])->where("id_emailing_liste_contact",ATF::emailing_liste_contact()->sa())->setDimension('row');
		$sa = ATF::emailing_job_email()->sa();

		$this->assertEquals('oui',$sa['retour'],"Erreur On devrait avoir un retour a oui sur ce contact");
		$this->assertEquals("ERROR (#1002) : L'email n'est pas valide Phoque !",$sa['erreur_brute'],"Erreur On devrait avoir un retour a oui sur ce contact");
	}
 

	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 06-12-2010
	*/ 
	public function test_bouncesAnalyse() {	
		$a = array("5.1.1");	
		$r = $this->obj->bouncesAnalyse($a);
		$this->assertEquals("5.1.1",$r[5]["5.1.1"],"1 - L'erreur de type 5 n'a pas été retournée.");
		$this->assertEquals(array(),$r[2],"1 - L'erreur de type 2 n'est pas vide.");
		$this->assertEquals(array(),$r[4],"1 - L'erreur de type 4 n'est pas vide.");

		$a = array("4.1.1");	
		$r = $this->obj->bouncesAnalyse($a);
		$this->assertEquals("4.1.1",$r[4]["4.1.1"],"2 - L'erreur de type 4 n'a pas été retournée.");
		$this->assertEquals(array(),$r[2],"2 - L'erreur de type 2 n'est pas vide.");
		$this->assertEquals(array(),$r[5],"2 - L'erreur de type 5 n'est pas vide.");

		$a = array("2.1.1");	
		$r = $this->obj->bouncesAnalyse($a);
		$this->assertEquals("2.1.1",$r[2]["2.1.1"],"3 - L'erreur de type 2 n'a pas été retournée.");
		$this->assertEquals(array(),$r[5],"3 - L'erreur de type 5 n'est pas vide.");
		$this->assertEquals(array(),$r[4],"3 - L'erreur de type 4 n'est pas vide.");

		$a = array("7.8.9");	
		$r = $this->obj->bouncesAnalyse($a);
		$this->assertEquals(array(),$r[5],"4 - L'erreur de type 5 n'est pas vide.");
		$this->assertEquals(array(),$r[4],"4 - L'erreur de type 4 n'est pas vide.");
		$this->assertEquals(array(),$r[2],"4 - L'erreur de type 4 n'est pas vide.");
	}
	
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 06-12-2010
	*/
	public function test_check_retour() {	
		ATF::unsetSingleton("imap");
		ATF::setSingleton("imap", new mockObjectForIMAP("mail.absystech.net",143,"postmaster@absystech-speedmail.com","lkjoiu987"));
		ATF::imap()->num = 5;
		ATF::imap()->ej = $this->eje[0]['id_emailing_job'];
		ATF::imap()->ecl = $this->eje[0]['id_emailing_liste_contact'];
		$this->obj->check_retour();
		ATF::emailing_job_email()->q->reset()
			->addField("permanent_failure")
			->addField("retour")
			->Where("MD5(id_emailing_job)",md5($this->eje[0]['id_emailing_job']))
			->Where("MD5(emailing_liste_contact.id_emailing_liste_contact)",md5($this->eje[0]['id_emailing_liste_contact']));
		$r = ATF::emailing_job_email()->select_all();
		$this->assertEquals("5.2.3",$r[0]['permanent_failure'],"Erreur de permanent_failure pour le 5.2.3");
		$this->assertEquals("oui",$r[0]['retour'],"Erreur de retour pour le 5.2.3");
		$this->assertEquals("non",ATF::emailing_contact()->select($this->ec[0]['id_emailing_contact'],'opt_in'),"Erreur de désincription pour le 5.2.3");
		
		ATF::imap()->num = 4;
		ATF::imap()->ej = $this->eje[1]['id_emailing_job'];
		ATF::imap()->ecl = $this->eje[1]['id_emailing_liste_contact'];
		$this->obj->check_retour();
		ATF::emailing_job_email()->q->reset()
			->addField("permanent_failure")
			->addField("persistent_failure")
			->addField("retour")
			->Where("MD5(id_emailing_job)",md5($this->eje[1]['id_emailing_job']))
			->Where("MD5(emailing_liste_contact.id_emailing_liste_contact)",md5($this->eje[1]['id_emailing_liste_contact']));
		$r = ATF::emailing_job_email()->select_all();
		$this->assertNull($r[0]['permanent_failure'],"Erreur de permanent_failure pour le 4.2.2");
		$this->assertEquals("4.2.2",$r[0]['persistent_failure'],"Erreur de persistant failure pour le 4.2.2");
		$this->assertEquals("oui",$r[0]['retour'],"Erreur de retour pour le 2.2.2");
		
		ATF::imap()->num = 2;
		ATF::imap()->ej = $this->eje[2]['id_emailing_job'];
		ATF::imap()->ecl = $this->eje[2]['id_emailing_liste_contact'];
		$this->obj->check_retour();
		ATF::emailing_job_email()->q->reset()
			->addField("permanent_failure")
			->addField("persistent_failure")
			->addField("success")
			->addField("retour")
			->Where("MD5(id_emailing_job)",md5($this->eje[2]['id_emailing_job']))
			->Where("MD5(emailing_liste_contact.id_emailing_liste_contact)",md5($this->eje[2]['id_emailing_liste_contact']));
		$r = ATF::emailing_job_email()->select_all();

		$this->assertNull($r[0]['permanent_failure'],"Erreur de permanent_failure pour le 2.2.2");
		$this->assertNull($r[0]['persistent_failure'],"Erreur de persistent_failure pour le 2.2.2");
		$this->assertEquals("2.2.2",$r[0]['success'],"Erreur de success pour le 2.2.2");
		$this->assertEquals("non",$r[0]['retour'],"Erreur de retour pour le 2.2.2");
		
		unset(ATF::imap()->num);
		ATF::imap()->ej = $this->eje[3]['id_emailing_job'];
		ATF::imap()->ecl = $this->eje[3]['id_emailing_liste_contact'];
		$this->obj->check_retour();
		
	} 

	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 07-02-2012
	*/
	public function test_estimationPrix(){
		$infos = array("idEL"=>$this->el['id_emailing_liste']);
		$r = $this->obj->estimationPrix($infos);
		$this->assertEquals(0.16,$r,"Le tarif a dû changer :/");
		
	}
	
};

class mockObjectForIMAP extends imap {
	public function imap_fetch_overview(){
		$return = array(
			array(
				"subject" => "=?utf-8?Q?Subject_TU_=31?="
				,"from" => "TU Expediteur <tu@absystech.net>"
				,"to" =>"tu1@absystech-speedmail.com"
				,"date" => "Mon, 4 Apr 2011 09:14:15 +0200 (CEST)"
				,"message_id" => "<1170c999570fefd4d26b56c65bac137b@dev.optima.absystech.net>"
				,"size" => 3914
				,"uid" => 149534
				,"msgno" => 11
				,"recent" => 0
				,"flagged" => 0
				,"answered" => 0
				,"deleted" => 0
				,"seen" => 0
				,"draft" => 0
				,"udate" => 1301901240
			),
			array(
				"subject" => "=?utf-8?Q?Subject_TU_=31?="
				,"from" => "TU Expediteur <tu@absystech.net>"
				,"to" =>"No-reply <tu8-speedmail@absystech-speedmail.com>"
				,"date" => "Mon, 4 Apr 2011 09:14:15 +0200 (CEST)"
				,"message_id" => "<1170c999570fefd4d26b56c65bac137b@dev.optima.absystech.net>"
				,"size" => 3914
				,"uid" => 149534
				,"msgno" => 11
				,"recent" => 0
				,"flagged" => 0
				,"answered" => 0
				,"deleted" => 0
				,"seen" => 0
				,"draft" => 0
				,"udate" => 1301901240
			),
			array(
				"subject" => "=?utf-8?Q?Subject_TU_=31?="
				,"from" => "TU Expediteur <tu@absystech.net>"
				,"to" =>"-speedmail@absystech-speedmail.com"
				,"date" => "Mon, 4 Apr 2011 09:14:15 +0200 (CEST)"
				,"message_id" => "<1170c999570fefd4d26b56c65bac137b@dev.optima.absystech.net>"
				,"size" => 3914
				,"uid" => 149534
				,"msgno" => 11
				,"recent" => 0
				,"flagged" => 0
				,"answered" => 0
				,"deleted" => 0
				,"seen" => 0
				,"draft" => 0
				,"udate" => 1301901240
			),
			array(
				"subject" => "=?utf-8?Q?Subject_TU_=31?="
				,"from" => "TU Expediteur <tu@absystech.net>"
				,"to" =>md5($this->ej)."-".md5($this->ecl)."-".base64_encode(ATF::$codename)."-speedmail@absystech-speedmail.com"
				,"date" => "Mon, 4 Apr 2011 09:14:15 +0200 (CEST)"
				,"message_id" => "<1170c999570fefd4d26b56c65bac137b@dev.optima.absystech.net>"
				,"size" => 3914
				,"uid" => 149534
				,"msgno" => 11
				,"recent" => 0
				,"flagged" => 0
				,"answered" => 0
				,"deleted" => 0
				,"seen" => 0
				,"draft" => 0
				,"udate" => 1301901240
			)
		);
		foreach ($return as $i) {
			$result[] = (object)$i;
		}
		return $result;
	}
	public function deleteMail(){
		//on ne fait rien, il s'agit de paramétrage
	}
	public function returnBodyStr(){
		switch ($this->num) {
			case 5:
				return "--- Below 5.2.3";
			break;
			case 4:
				return "--- Below 4.2.2";
			break;
			case 2:
				return "--- Below 2.2.2";
			break;
		}
	}
};


?>