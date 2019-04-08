<?
/**
* Classe de test des mails hotline
* @author Jérémie Gwiazdowski <jgw@absystech.fr>
* ATTENTION : Il est normal que la classe ne soit pas testée au complet. En effet les tests de mails sont testés dans les méthodes métiers de hotline et hotline_interaction
*/
class hotline_mail_test extends ATF_PHPUnit_Framework_TestCase {
	/**
	* Hotline de test
	* @var int
	*/
	private $id_hotline;
		
	protected function setUp() {
		$this->initUser();
	}
	
	protected function tearDown() {
        ATF::$msg->getNotices();
		ATF::db()->rollback_transaction(true);
	}
	
	/** 
	* Initialisation d'un jeu de test holtine
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	private function initHotline(){
		$hotline = array(
			"hotline"=>"HotlineTuTest"
			,"id_societe"=>2
			,"detail"=>"HotlineTuTest"
			,"date_debut"=>date('Y-m-d')
			,"id_contact"=>$this->id_contact
			,"id_user"=>$this->id_user
			,"urgence"=>'detail'
			,"pole_concerne"=>"dev"
			,'charge'=>"intervention" //précisé pour éviter les problèmes avec la méthode "setbillingmode"
		);
		$this->id_hotline = ATF::hotline()->insert($hotline);
	}
	
	// Initialisation d'un jeu de tests d'interactions
    // @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    private function initInteractions(){
        if (!$this->id_hotline) $this->initHotline();
        $hotline_interaction['id_hotline']=$this->id_hotline;
        $hotline_interaction['date']=date('Y-m-d H:i:s');
        $hotline_interaction['duree_presta']='00:15';
        $hotline_interaction['heure_debut_presta']='14:45';
        $hotline_interaction['heure_fin_presta']='15:00';
        $hotline_interaction['credit_presta']=0.25;
        $hotline_interaction['no_test_credit']=true;
        $hotline_interaction['etat']='fixing';
        $hotline_interaction['detail']='detail01';
        $hotline_interaction['visible'] ='oui';
        $hotline_interaction['hotline_interaction']=$hotline_interaction;
        $this->id_hotline_interaction = ATF::hotline_interaction()->insert($hotline_interaction,$this->s);
        
        $hotline_interaction['id_hotline']=$this->id_hotline;
        $hotline_interaction['date']=date('Y-m-d H:i:s');
        $hotline_interaction['duree_presta']='00:45';
        $hotline_interaction['heure_debut_presta']='14:45';
        $hotline_interaction['heure_fin_presta']='15:30';
        $hotline_interaction['credit_presta']=0.75;
        $hotline_interaction['etat']='fixing';
        $hotline_interaction['detail']='detail02';
        $hotline_interaction['visible'] ='oui';
        $hotline_interaction['hotline_interaction']=$hotline_interaction;
        $this->id_hotline_interaction_= ATF::hotline_interaction()->insert($hotline_interaction,$this->s);

        $hotline_interaction['id_hotline']=$this->id_hotline;
        $hotline_interaction['date']="2009-12-01 12:00:00";
        $hotline_interaction['duree_presta']='00:45';
        $hotline_interaction['heure_debut_presta']='14:45';
        $hotline_interaction['heure_fin_presta']='15:30';
        $hotline_interaction['credit_presta']=0.75;
        $hotline_interaction['etat']='fixing';
        $hotline_interaction['detail']='detail02';
        $hotline_interaction['visible'] ='oui';
        $hotline_interaction['hotline_interaction']=$hotline_interaction;
        $this->id_hotline_interaction_=ATF::hotline_interaction()->insert($hotline_interaction,$this->s);

        $hotline_interaction['id_hotline']=$this->id_hotline;
        $hotline_interaction['date']="2009-12-02 12:00:00";
        $hotline_interaction['duree_presta']='00:45';
        $hotline_interaction['heure_debut_presta']='14:45';
        $hotline_interaction['heure_fin_presta']='15:30';
        $hotline_interaction['credit_presta']=0.75;
        $hotline_interaction['etat']='fixing';
        $hotline_interaction['detail']='detail02';
        $hotline_interaction['visible'] ='oui';
        $hotline_interaction['hotline_interaction']=$hotline_interaction;
        $this->id_hotline_interaction_=ATF::hotline_interaction()->insert($hotline_interaction,$this->s);
    }
	
    /* @author Quentin JANON <qjanon@absystech.fr> */
    public function test_createMailInsertERROR(){
        $erreur = true;
        try{
            $this->obj->createMailInsert();  
        } catch (errorATF $e) {
            $erreur = true;
        }
        $this->assertTrue($erreur,"Erreur non declenche");
    }
    
    /* @author Quentin JANON <qjanon@absystech.fr> */
    public function test_createMailInsertERROR2(){
        $this->initHotline();
        //Modif du mail pour éviter l'erreur "same_mail"
        ATF::user()->update(array("id_user"=>$this->id_user,"email"=>"tu@absystech.net","pole"=>""));
        
        $this->obj->createMailInsert($this->id_hotline);
 
        $erreur = true;
        try{
            $this->obj->createMailInsert();  
        } catch (errorATF $e) {
            $erreur = true;
        }
        $this->assertTrue($erreur,"Erreur non declenche");
    }


	/* ---------------Mails----------------------------*/
	
	/* @author Jérémie Gwiazdowski <jgw@absystech.fr> */
	public function test_createMailBilling(){
		$this->initHotline();
		$this->initInteractions();
		
		//Modif du mail pour éviter l'erreur "same_mail"
		ATF::user()->update(array("id_user"=>$this->id_user,"email"=>"tu@absystech.net"));
		
		$this->obj->createMailBilling($this->id_hotline);
		$this->obj->sendMail();
		
		//Tests Mail
		//Génération de la bonne partie de la queue
		$queue=ATF::db()->getQueue();
		$queue->generateMoveFile();
		$queue->generateSendEmail();
		
		$mails=$this->obj->getCurrentMail();
		$mails=$mails->sent;
		$this->assertTrue(isset($mails[0]));
		//$this->assertEquals(isset($mails[0]));
		//print_r(md5($mails[0]["body"]));
		//Flush des notices
		$nb = ATF::$msg->getNotices();
	}
	
	/* @author Jérémie Gwiazdowski <jgw@absystech.fr> */
	public function test_createMailInteractionMultiMails(){
		$this->initHotline();
		$this->initInteractions();
		
		//Modif du mail pour éviter l'erreur "same_mail"
		ATF::user()->update(array("id_user"=>$this->id_user,"email"=>"tu@absystech.net"));
		
		$this->obj->createMailInteraction($this->id_hotline,$this->id_hotline_interaction,false,"1,2");
		
		 $this->obj->sendMail();
		
		//Tests Mail
		//Génération de la bonne partie de la queue
		$queue=ATF::db()->getQueue();
		$queue->generateMoveFile();
		$queue->generateSendEmail();
		
		$mails=$this->obj->getCurrentMail();
		$mails=$mails->sent;
		$this->assertTrue(isset($mails[0]));
		//$this->assertEquals(isset($mails[0]));
		//print_r(md5($mails[0]["body"]));
		//Flush des notices
		$nb = ATF::$msg->getNotices();
	}

	/* @author Morgan Fleurquin <mfleurquin@absystech.fr>
	public function test_createMailInteractionInternal(){
		ATF::$usr->set("id_user" , NULL);
		$id_hotline_interaction = 57162;
		$id_hotline = 9806;
		$to = "tu@absystech.net";
		$this->obj->createMailInteractionInternal($to,$id_hotline,$id_hotline_interaction);

	} */
	
    
    
};
?>