<?
class hotline_interaction_test extends ATF_PHPUnit_Framework_TestCase {
	/**
	* Hotline de test
	* @var int
	*/
	private $id_hotline;
	
	protected function setUp() {
		ATF::initialize();
		$this->initUser();	
	}
	
	protected function tearDown() {
		ATF::db()->rollback_transaction(true);
		//Flush des notices
		ATF::$msg->getNotices();
	}
	
 
	// Initialisation d'un jeu de test holtine
	// @author Jérémie Gwiazdowski <jgw@absystech.fr>
	private function initHotline(){
		$hotline = array(
			"hotline"=>"HotlineTuTest"
			,"id_societe"=>$this->s["user"]->get("id_societe")
			,"detail"=>"HotlineTuTest"
			,"date_debut"=>date('Y-m-d')
			,"id_contact"=>$this->id_contact
			,"id_user"=>$this->id_user
			,"visible"=>"oui"
			,"pole_concerne"=>"dev"
			,"urgence"=>'detail'
			,'charge'=>"intervention" //précisé pour éviter les problèmes avec la méthode "setbillingmode"
		);
		$this->id_hotline = ATF::hotline()->insert($hotline);
	}
	
	
	// Initialisation d'un jeu de tests d'interactions
	// @author Jérémie Gwiazdowski <jgw@absystech.fr>
	private function initInteractions(){
		if (!$this->id_hotline) $this->initHotline();
		$hotline_interaction['id_hotline']=$this->id_hotline;
		$hotline_interaction['date']=date('Y-m-d H:i:s');
		$hotline_interaction['duree_presta']='00:15';
		$hotline_interaction['heure_debut_presta']='14:45';
		$hotline_interaction['heure_fin_presta']='15:00';
		$hotline_interaction['credit_presta']=0.00;	
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
	
	//@author Jérémie Gwiazdowski <jgw@absystech.fr>
	private function initAltUser(){
		//Création d'un utilisateur
		$user=array(
			"login"=>"altTutul"
			,"password"=>"tu"
			,"civilite"=>"M"
			,"prenom"=>"class:"//.get_class($this)
			,"nom"=>"unitaire"
			,"pole"=>"system"
			,"id_agence"=>$this->id_agence
			,"id_profil"=>$this->id_profil
			,"email"=>"tu@absystech.fr"
			,"custom"=>serialize(array("preference"=>array("langue"=>"fr")))
			,"date_connection"=>date("Y-m-d H:i:s")
		);
		$this->alt_id_user = ATF::user()->i($user);
	}
	
	
	//@author Morgan Fleurquin <mfleurquin@absystech.fr>
	public function test_insert_error(){
		$this->initHotline();
		ATF::$msg->getNotices();//Flush des notices	
		
		$user=array(
			"login"=>"altTutul"
			,"password" => "toto"
			,"id_societe" => 1
			,"prenom" => "toto"
			,"nom" => "tata"
			,"email" => "toto@absystech.fr"			
		);
		$id_user = ATF::user()->insert($user);
		
		//Création du jeu de données
		$hotline_interaction['id_hotline']=$this->id_hotline;
		$hotline_interaction['date']="2009-12-10 12:00:00";		
		$hotline_interaction['etat']='fixing';		
		$hotline_interaction['visible'] ='oui';
		$hotline_interaction['hotline_interaction']=$hotline_interaction;
		$hotline_interaction['send_mail']=true;
		$hotline_interaction['id_user']= $id_user;	
		$hotline_interaction['actifNotify'] = "1,12";			
		
		try{ $id_inter=$this->obj->insert(array("hotline_interaction"=>array()),$this->s);
		}catch(error $e){ $errorMessage = $e->getMessage(); }
		$this->assertEquals("Aucunes informations transmises, veuillez recommencez le traitement.",$errorMessage,"assert 1");
		

		try{ $id_inter=$this->obj->insert(array("hotline_interaction"=>$hotline_interaction),$this->s);
		}catch(error $e){ $errorMessage = $e->getMessage(); }
		$this->assertEquals("Vous devez préciser les actions effectuées sur la requête.",$errorMessage,"assert 2");

		$hotline_interaction['detail']='Test detail';
		$hotline_interaction['duree_presta']='00:00';		
		try{ $id_inter=$this->obj->insert(array("hotline_interaction"=>$hotline_interaction),$this->s);
		}catch(error $e){ $errorMessage = $e->getMessage(); }
		$this->assertEquals("duree_presta_non_renseigne",$errorMessage,"assert 3");
		
		$hotline_interaction['duree_presta']='00:45';
		$hotline_interaction['heure_fin_presta']='14:45';
		$hotline_interaction['heure_debut_presta']='15:30';		
		try{ $id_inter=$this->obj->insert(array("hotline_interaction"=>$hotline_interaction),$this->s);
		}catch(error $e){ $errorMessage = $e->getMessage(); }
		$this->assertEquals("L'heure du début de la prestation est supérieure à l'heure de fin !",$errorMessage,"assert 4");


		$hotline_interaction['heure_depart_dep']='15:00';		
		$hotline_interaction['heure_debut_presta']='14:45';
		$hotline_interaction['heure_fin_presta']='15:30';	
		$hotline_interaction['heure_arrive_dep']='15:30';	

		unset($hotline_interaction["hotline_interaction"]);	

		try{ $id_inter=$this->obj->insert(array("hotline_interaction"=>$hotline_interaction),$this->s);
		}catch(error $e){ $errorMessage = $e->getMessage(); }
		$this->assertEquals("L'heure de début de mission est superieure à l'heure de début de prestation !",$errorMessage,"assert 5");


		$hotline_interaction['heure_depart_dep']='14:45';		
		$hotline_interaction['heure_debut_presta']='14:45';
		$hotline_interaction['heure_fin_presta']='15:30';	
		$hotline_interaction['heure_arrive_dep']='15:00';	

		unset($hotline_interaction["hotline_interaction"]);	

		try{ $id_inter=$this->obj->insert(array("hotline_interaction"=>$hotline_interaction),$this->s);
		}catch(error $e){ $errorMessage = $e->getMessage(); }
		$this->assertEquals("L'heure de fin de mission est inferieure à l'heure de fin de prestation !",$errorMessage,"assert 6");


		$hotline_interaction['heure_depart_dep']='14:45';		
		$hotline_interaction['heure_debut_presta']='14:45';
		$hotline_interaction['heure_fin_presta']='15:30';	
		$hotline_interaction['heure_arrive_dep']='15:30';	
		$hotline_interaction['duree_pause']='01:30';

		unset($hotline_interaction["hotline_interaction"]);	

		try{ $id_inter=$this->obj->insert(array("hotline_interaction"=>$hotline_interaction),$this->s);
		}catch(error $e){ $errorMessage = $e->getMessage(); }
		$this->assertEquals("La durée de pause est superieure à la durée de prestation !",$errorMessage,"assert 7");




	}


	//@author Quentin JANON <qjanon@absystech.fr>
	public function test_delete() {
		$this->initInteractions();
		$odm = array(
			"ordre_de_mission"=>"Yallah TU"
			,"id_societe"=>$this->id_societe
			,"id_contact"=>$this->id_contact
			,"date"=>date("Y-m-d")
			,"id_user"=>ATF::$usr->getId()
			,"adresse"=>"12 rue du louvre"
			,"ville"=>"LILLE"
			,"etat"=>"termine"
			,"cp"=>"59000"
		);
		
		$id_odm = ATF::ordre_de_mission()->i($odm);
		
		$it = array("id_hotline_interaction"=>$this->id_hotline_interaction, "id_ordre_de_mission"=>$id_odm);
		$this->obj->u($it);
		
		$it = array("id"=>array($this->id_hotline_interaction));
		$this->obj->delete($it);
		
		$r = ATF::ordre_de_mission()->select($id_odm,"etat");
		$this->assertEquals("en_cours",$r,"L'ordre de mission n'est pas revenu en état en cours suite a la suppression de l'interaction.");
		
	}

	
	//@author Jérémie Gwiazdowski <jgw@absystech.fr>
	public function test_insert() {
		$this->initHotline();
		ATF::$msg->getNotices();//Flush des notices	
		
		$user=array(
			"login"=>"altTutul"
			,"password" => "toto"
			,"id_societe" => 1
			,"prenom" => "toto"
			,"nom" => "tata"
			,"email" => "toto@absystech.fr"			
		);
		$id_user = ATF::user()->insert($user);
		
		//Création du jeu de données
		$hotline_interaction['id_hotline']=$this->id_hotline;
		$hotline_interaction['date']="2009-12-10 12:00:00";
		
		$hotline_interaction['credit_presta']='0.75';
		$hotline_interaction['duree_presta']='00:45';
		$hotline_interaction['heure_depart_dep']='14:45';
		$hotline_interaction['heure_debut_presta']='14:45';
		$hotline_interaction['heure_fin_presta']='15:30';		
		$hotline_interaction['heure_arrive_dep']='16:30';
		$hotline_interaction['duree_dep']='01:00';
		$hotline_interaction['credit_dep']='0.75';	
		$hotline_interaction['duree_pause']='00:00';
		$hotline_interaction['etat']='fixing';
		$hotline_interaction['detail']='detail02';
		$hotline_interaction['visible'] ='oui';
		$hotline_interaction['avancement']=50;
		$hotline_interaction['hotline_interaction']=$hotline_interaction;
		$hotline_interaction['send_mail']=true;
		$hotline_interaction['id_user']= $id_user;	
		$hotline_interaction['actifNotify'] = "1,12";
		$hotline_interaction['nature']='interaction';

		//Insertion de l'inter
		$id_inter=$this->obj->insert(array("hotline_interaction"=>$hotline_interaction),$this->s);
				
		$inter=$this->obj->select($id_inter);
		//Vérification des données
		$this->assertEquals($inter["id_hotline"],$this->id_hotline,"assert 1");
		$this->assertEquals($inter["date"],"2009-12-10 12:00:00","assert 2");
		$this->assertEquals($inter["detail"],"detail02","assert 3");
		$this->assertEquals($inter["duree_presta"],"00:45:00","assert 4");
		$this->assertEquals($inter["visible"],"oui","assert 5");
		$this->assertNull($inter["id_contact"],"assert 7");
		

		ATF::societe()->u(array("id_societe"=>ATF::hotline()->select($this->id_hotline , "id_societe"), "forfait_dep"=>0.00));

		ATF::hotline()->u(array("id_hotline"=>$this->id_hotline , "facturation_ticket"=>"oui"));
		
		try{
			$id_inter=$this->obj->insert(array("hotline_interaction"=>$hotline_interaction),$this->s);
		}catch(error $e){ $errorMessage = $e->getMessage(); }
		
		$this->assertEquals("Merci de saisir votre justification !",$errorMessage,"assert 8");

		$hotline_interaction['champ_alerte'] = "La justification !";
		$id_inter=$this->obj->insert(array("hotline_interaction"=>$hotline_interaction),$this->s);


		//Test notices
		$notices=ATF::$msg->getNotices();
		$this->assertTrue(is_array($notices),"assert 9");
		$this->assertEquals(5,count($notices),"assert 10");
	}

	public function test_insertOtherUser() {
        $this->initAltUser();
        $this->initHotline();
        ATF::$msg->getNotices();//Flush des notices 
        
        $h = array(
            "id_hotline"=>$this->id_hotline
            ,"etat"=>"fixing"
        );
        ATF::hotline()->u($h);
        
        //Changement d'utilisateur
        $new_usr=new usr($this->alt_id_user);
        ATF::setUser($new_usr);
        //Création du jeu de données
        $hotline_interaction['id_hotline']=$this->id_hotline;
        $hotline_interaction['date']="2009-12-10 12:00:00";
        $hotline_interaction['no_test_credit']=true;
		$hotline_interaction['duree_presta']='00:45';
		$hotline_interaction['heure_debut_presta']='14:45';
		$hotline_interaction['heure_fin_presta']='15:30';
        $hotline_interaction['etat']='fixing';
        $hotline_interaction['detail']='detail02';
        $hotline_interaction['visible'] ='oui';
        $hotline_interaction['avancement']=50;
        $hotline_interaction['hotline_interaction']=$hotline_interaction;
        $hotline_interaction['send_mail']=true;
        $hotline_interaction['mep_mail']=true;
        //Insertion de l'inter
        $id_inter=$this->obj->insert(array("hotline_interaction"=>$hotline_interaction),$this->s);
        $inter=$this->obj->select($id_inter);
        //Vérification des données
        $this->assertEquals($inter["id_hotline"],$this->id_hotline,"assert 1");
        $this->assertEquals($inter["date"],"2009-12-10 12:00:00","assert 2");
        $this->assertEquals($inter["detail"],"detail02","assert 3");
        $this->assertEquals($inter["duree_presta"],"00:45:00","assert 4");
        $this->assertEquals($inter["visible"],"oui","assert 5");
        $this->assertEquals($inter["id_user"],$this->alt_id_user,"assert 6");
        $this->assertNull($inter["id_contact"],"assert 7");
        
        // Test sur la mise en prod
        $this->assertEquals("oui",ATF::hotline()->select($this->id_hotline,"wait_mep"),"assert 9");
        
        //Test notices
        $notices=ATF::$msg->getNotices();
        $warnings=ATF::$msg->getWarnings();

        $this->assertTrue(is_array($notices),"assert 9");
        $this->assertEquals(2,count($notices),"assert 10");
        $this->assertTrue(is_array($warnings),"assert 11");
        $this->assertEquals(1,count($warnings),"assert 12");
    }


    //@author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_insertUserTransfert() {
        $this->initAltUser();
        $this->initHotline();
        ATF::$msg->getNotices();//Flush des notices 
        
        //Création du jeu de données
        $hotline_interaction['id_hotline']=$this->id_hotline;
        $hotline_interaction['date']="2009-12-10 12:00:00";
        $hotline_interaction['no_test_credit']=true;
		$hotline_interaction['duree_presta']='00:45';
		$hotline_interaction['heure_debut_presta']='14:45';
		$hotline_interaction['heure_fin_presta']='15:30';
        $hotline_interaction['etat']='fixing';
        $hotline_interaction['detail']='detail02';
        $hotline_interaction['hotline_interaction']=$hotline_interaction;
        $hotline_interaction['transfert']=$this->alt_id_user;
        $hotline_interaction['send_mail']=true;
        //Insertion de l'inter
        $id_inter=$this->obj->insert(array("hotline_interaction"=>$hotline_interaction),$this->s);
        $inter=$this->obj->select($id_inter);
        //Vérification des données
        $this->assertEquals($inter["id_hotline"],$this->id_hotline,"assert 1");
        $this->assertEquals($inter["date"],"2009-12-10 12:00:00","assert 2");
        $this->assertEquals($inter["detail"],"Requête transférée par  à class: unitaire<br />detail02","assert 3");
        $this->assertEquals($inter["duree_presta"],"00:45:00","assert 4");
        $this->assertEquals($inter["id_user"],$this->id_user,"assert 5");
        $this->assertNull($inter["id_contact"],"assert 6");
        
        //Test du changement de pôle
        $this->assertEquals(ATF::hotline()->select($this->id_hotline,"pole_concerne"),"system","assert 7");
        $this->assertEquals(ATF::hotline()->select($this->id_hotline,"id_user"),$this->alt_id_user,"assert 8");
        

        //Test notices
        $notices=ATF::$msg->getNotices();
        $this->assertTrue(is_array($notices),"assert 12");
        $this->assertEquals(3,count($notices),"assert 13");
    }
	

	//@author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_insertPoleTransfert() {
        $this->initHotline();
        ATF::$msg->getNotices();//Flush des notices 
        
        //Création du jeu de données
        $hotline_interaction['id_hotline']=$this->id_hotline;
        $hotline_interaction['date']="2009-12-10 12:00:00";
        $hotline_interaction['no_test_credit']=true;
		$hotline_interaction['duree_presta']='00:45';
		$hotline_interaction['heure_debut_presta']='14:45';
		$hotline_interaction['heure_fin_presta']='15:30';
        $hotline_interaction['etat']='fixing';
        $hotline_interaction['detail']='detail02';
        $hotline_interaction['hotline_interaction']=$hotline_interaction;
        $hotline_interaction['transfert_pole']="dev";
        $hotline_interaction['send_mail']=true;
        //Insertion de l'inter
        $id_inter=$this->obj->insert(array("hotline_interaction"=>$hotline_interaction),$this->s);
        $inter=$this->obj->select($id_inter);
        //Vérification des données
        $this->assertEquals($inter["id_hotline"],$this->id_hotline,"assert 1");
        $this->assertEquals($inter["date"],"2009-12-10 12:00:00","assert 2");
        $this->assertEquals($inter["detail"],"Requête transférée par  au pôle dev<br />detail02","assert 3");
        $this->assertEquals($inter["duree_presta"],"00:45:00","assert 4");
        $this->assertEquals($inter["id_user"],$this->id_user,"assert 5");
        $this->assertNull($inter["id_contact"],"assert 6");
        
        //Verif de la prise en charge de la hotline
        $this->assertNull(ATF::hotline()->select($this->id_hotline,"id_user"),"assert 7");
   
        
        //Test notices
        $notices=ATF::$msg->getNotices();
        $this->assertTrue(is_array($notices),"assert 9");
        $this->assertEquals(3,count($notices),"assert 10");
    }


    //@author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_insertWait() {
        $this->initHotline();
        ATF::$msg->getNotices();//Flush des notices 
        ATF::hotline()->update(array("id_hotline"=>$this->id_hotline
                                    ,"facturation_ticket"=>"oui"
                                    ,"ok_facturation"=>"non"
                                    ,"etat"=>"fixing"));
        
        //Création du jeu de données
        $hotline_interaction['id_hotline']=$this->id_hotline;
        $hotline_interaction['date']="2009-12-10 12:00:00";
        $hotline_interaction['no_test_credit']=true;
		$hotline_interaction['duree_presta']='00:45';
		$hotline_interaction['heure_debut_presta']='14:45';
		$hotline_interaction['heure_fin_presta']='15:30';
        $hotline_interaction['etat_wait']=true;
        $hotline_interaction['detail']='detail02';
        $hotline_interaction['hotline_interaction']=$hotline_interaction;
        $hotline_interaction['send_mail']=true;
        //Insertion de l'inter
        $id_inter=$this->obj->insert(array("hotline_interaction"=>$hotline_interaction),$this->s);
        $inter=$this->obj->select($id_inter);
        //Vérification des données
        $this->assertEquals($inter["id_hotline"],$this->id_hotline,"assert 1");
        $this->assertEquals($inter["date"],"2009-12-10 12:00:00","assert 2");
        $this->assertEquals($inter["detail"],"detail02","assert 3");
        $this->assertEquals($inter["duree_presta"],"00:45:00","assert 4");
        $this->assertEquals($inter["id_user"],$this->id_user,"assert 5");
        $this->assertNull($inter["id_contact"],"assert 6");
        
        $this->assertEquals("wait",ATF::hotline()->select($this->id_hotline,"etat"),"assert 7");
        

        
        //Test notices
        $notices=ATF::$msg->getNotices();
        $this->assertTrue(is_array($notices),"assert 9");
        $this->assertEquals(1,count($notices),"assert 10");
    }

     //@author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_insertAutoFixing() {
        $this->initHotline();
        ATF::$msg->getNotices();//Flush des notices 
        ATF::hotline()->update(array("id_hotline"=>$this->id_hotline
                                    //,"facturation_ticket"=>"oui"
                                    //,"ok_facturation"=>"non"
                                    ,"etat"=>"fixing"));
        
        //Création du jeu de données
        $hotline_interaction['id_hotline']=$this->id_hotline;
        $hotline_interaction['date']="2009-12-10 12:00:00";
        $hotline_interaction['no_test_credit']=true;
		$hotline_interaction['duree_presta']='00:45';
		$hotline_interaction['heure_debut_presta']='14:45';
		$hotline_interaction['heure_fin_presta']='15:30';
        //$hotline_interaction['etat_wait']=true;
        $hotline_interaction['detail']='detail02';
        $hotline_interaction['hotline_interaction']=$hotline_interaction;
        $hotline_interaction['send_mail']=true;
        //Insertion de l'inter
        $id_inter=$this->obj->insert(array("hotline_interaction"=>$hotline_interaction),$this->s);
        $inter=$this->obj->select($id_inter);
        //Vérification des données
        $this->assertEquals($inter["id_hotline"],$this->id_hotline,"assert 1");
        $this->assertEquals($inter["date"],"2009-12-10 12:00:00","assert 2");
        $this->assertEquals($inter["detail"],"detail02","assert 3");
        $this->assertEquals($inter["duree_presta"],"00:45:00","assert 4");
        $this->assertEquals($inter["id_user"],$this->id_user,"assert 5");
        $this->assertNull($inter["id_contact"],"assert 6");
        
        $this->assertEquals("fixing",ATF::hotline()->select($this->id_hotline,"etat"),"assert 7");
        
        
        //Test notices
        $notices=ATF::$msg->getNotices();
        $this->assertTrue(is_array($notices),"assert 9");
        $this->assertEquals(1,count($notices),"assert 10");
    }


    //@author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_insertClose() {
        $this->initHotline();
        ATF::$msg->getNotices();//Flush des notices 
        ATF::hotline()->update(array("id_hotline"=>$this->id_hotline,"facturation_ticket"=>"non"));
        
        //Création du jeu de données
        $hotline_interaction['id_hotline']=$this->id_hotline;
        $hotline_interaction['date']="2009-12-10 12:00:00";
        $hotline_interaction['no_test_credit']=true;
		$hotline_interaction['duree_presta']='00:45';
		$hotline_interaction['heure_debut_presta']='14:45';
		$hotline_interaction['heure_fin_presta']='15:30';
        $hotline_interaction['detail']='detail02';
        $hotline_interaction['hotline_interaction']=$hotline_interaction;
        $hotline_interaction['send_mail']=true;
        //Insertion de l'inter
        $id_inter=$this->obj->insert(array("hotline_interaction"=>$hotline_interaction),$this->s);
        $inter=$this->obj->select($id_inter);
        //Vérification des données
        $this->assertEquals($inter["id_hotline"],$this->id_hotline,"assert 1");
        $this->assertEquals($inter["date"],"2009-12-10 12:00:00","assert 2");
        $this->assertEquals($inter["detail"],"detail02","assert 3");
        $this->assertEquals($inter["duree_presta"],"00:45:00","assert 5");
        $this->assertEquals($inter["id_user"],$this->id_user,"assert 6");
        $this->assertNull($inter["id_contact"],"assert 7");
        
        ATF::hotline()->update(array("id_hotline"=>$this->id_hotline,"etat"=>"done"));
        
        $this->assertEquals("done",ATF::hotline()->select($this->id_hotline,"etat"),"assert 8");
        

        
        //Test notices
        $notices=ATF::$msg->getNotices();
        $this->assertTrue(is_array($notices),"assert 9");
        $this->assertEquals(1,count($notices),"assert 10");
    }


    //@author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_insertWaitWHotlineBilling() {
        $this->initHotline();
        ATF::hotline()->update(array("id_hotline"=>$this->id_hotline,"facturation_ticket"=>"oui"));
        ATF::$msg->getNotices();//Flush des notices 
        
        //Création du jeu de données
        $hotline_interaction['id_hotline']=$this->id_hotline;
        $hotline_interaction['date']="2009-12-10 12:00:00";
        $hotline_interaction['no_test_credit']=true;
		$hotline_interaction['duree_presta']='00:45';
		$hotline_interaction['heure_debut_presta']='14:45';
		$hotline_interaction['heure_fin_presta']='15:30';
        //$hotline_interaction['etat_wait']=true;
        $hotline_interaction['detail']='detail02';
        $hotline_interaction['hotline_interaction']=$hotline_interaction;
        $hotline_interaction['send_mail']=true;
        //Insertion de l'inter
        $id_inter=$this->obj->insert(array("hotline_interaction"=>$hotline_interaction),$this->s);
        $inter=$this->obj->select($id_inter);
        //Vérification des données
        $this->assertEquals($inter["id_hotline"],$this->id_hotline,"assert 1");
        $this->assertEquals($inter["date"],"2009-12-10 12:00:00","assert 2");
        $this->assertEquals($inter["detail"],"detail02","assert 3");
        $this->assertEquals($inter["duree_presta"],"00:45:00","assert 4");
        $this->assertEquals($inter["id_user"],$this->id_user,"assert 5");
        $this->assertNull($inter["id_contact"],"assert 6");
        
        $this->assertEquals("wait",ATF::hotline()->select($this->id_hotline,"etat"),"assert 7");
             
        //Test notices
        $notices=ATF::$msg->getNotices();
        $this->assertTrue(is_array($notices),"assert 8");
        $this->assertEquals(1,count($notices),"assert 9");
    }   


     //@author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_insertWpjointe() {
        $this->initHotline();
        $this->initAltUser();
        ATF::$msg->getNotices();//Flush des notices 
        
        //Modif du mail pour éviter l'erreur "same_mail"
        ATF::user()->update(array("id_user"=>$this->id_user,"email"=>"tu@absystech.fr"));
        
        //Création du jeu de données
        $hotline_interaction['id_hotline']=$this->id_hotline;
        $hotline_interaction['date']="2009-12-10 12:00:00";
        $hotline_interaction['no_test_credit']=true;
		$hotline_interaction['duree_presta']='00:45';
		$hotline_interaction['heure_debut_presta']='14:45';
		$hotline_interaction['heure_fin_presta']='15:30';
        //$hotline_interaction['etat_wait']=true;
        $hotline_interaction['detail']='detail02';
        $hotline_interaction['visible']='oui';
        $hotline_interaction['hotline_interaction']=$hotline_interaction;
        $hotline_interaction['id_user']=$this->alt_id_user;
        $hotline_interaction['send_mail']=true;
        
        //Pièce jointe
        $hotline_interaction['filestoattach']["fichier_joint"]="fichier_joint"; 

        //Insertion de l'inter
        $id_inter=$this->obj->insert(array("hotline_interaction"=>$hotline_interaction),$this->s);
        $inter=$this->obj->select($id_inter);
        //Vérification des données
        $this->assertEquals($inter["id_hotline"],$this->id_hotline,"assert 1");
        $this->assertEquals($inter["date"],"2009-12-10 12:00:00","assert 2");
        $this->assertEquals($inter["detail"],"detail02","assert 3");
        $this->assertEquals($inter["duree_presta"],"00:45:00","assert 4");
        $this->assertEquals($inter["id_user"],$this->alt_id_user,"assert 5");
        $this->assertNull($inter["id_contact"],"assert 6");
        
        
        //Test notices
        $notices=ATF::$msg->getNotices();
        $this->assertTrue(is_array($notices),"assert 8");
        $this->assertEquals(3,count($notices),"assert 9");
    }


    //@author Jérémie Gwiazdowski <jgw@absystech.fr>, Nicolas BERTEMONT <nbertemont@absystech.fr>
    public function test_default_value(){
        $this->assertEquals($this->obj->default_value("id_user"),ATF::$usr->getID(),"assert 1");
        $this->assertEquals($this->obj->default_value("detail"),"","assert 2");
        ATF::_r("id_ordre_de_mission",1);
        $this->assertEquals($this->obj->default_value("detail"),ATF::$usr->trans("hotline_odm_detail"),"assert 3");
        $this->assertEquals($this->obj->default_value("visible"),"","assert 4");
        $this->initHotline();
        $r=array("id_hotline"=>$this->obj->cryptId($this->id_hotline));
        $this->assertEquals($this->obj->default_value("visible",$this->s,$r),"oui","assert 5");
        $this->assertEquals($this->obj->default_value("id_contact"),"","assert 6");
        ATF::_r("id_hotline",$this->id_hotline);
        $this->assertEquals("HotlineTuTest",$this->obj->default_value("rappel-hotline"),"assert 7");
        $this->assertEquals(nl2br("Requête mise en ligne par class: unitaire\n\nHotlineTuTest"),$this->obj->default_value("rappelDetail-hotline"),"assert 8");          
    }  

     //@author Nicolas BERTEMONT <nbertemont@absystech.fr>
    public function test_listingEtat(){
        $this->assertEquals(array('free'=>1,'fixing'=>1,'wait'=>1,'done'=>1,'payee'=>1,'annulee'=>1),$this->obj->listingEtat(),"Le retour des états est incorrecte");
    }

      

    //@author Jérémie Gwiazdowski <jgw@absystech.fr>, Nicolas BERTEMONT <nbertemont@absystech.fr>
    public function test_updateInteraction(){
        $this->initHotline();
        $this->initInteractions();
        $this->obj->update(array("id_hotline_interaction"=>$this->id_hotline_interaction
                                ,"id_hotline"=>$this->id_hotline
                                ,"duree_presta"=>"00:15:00"
                                ,"heure_debut_presta"=>"14:00"
                                ,"heure_fin_presta"=>"14:45"
                                ,"credit_presta"=>0.00
                                ,"champ_alerte"=>"Une raison bidon"
                                ,"detail"=>"Test"
                                ,"date"=>date('Y-m-d H:i:s')));
                                
        $inter=$this->obj->select($this->id_hotline_interaction);
        $this->assertEquals($inter["duree_presta"],"00:15:00","assert 1");
        $this->assertEquals($inter["detail"],"Test","assert 3");
        $this->assertEquals($inter["id_hotline"],$this->id_hotline,"assert 4");
    }

    // ---------------Stats----------------------------
    //@author Nicolas BERTEMONT <nbertemont@absystech.fr>
    public function creationElementTestStat(){
        //création d'une hotline bidon
        $id_hotline=ATF::hotline()->i(array("id_societe"=>$this->id_societe,"hotline"=>"hot lol","pole_concerne"=>"dev","facturation_ticket"=>"non"));
        $this->assertTrue(is_numeric($id_hotline),"La hotline de test ne s'est pas créée");
        //création d'une hotline_interaction bidon à une année ultérieure
        $id_hotline_interaction=$this->obj->i(array("id_hotline"=>$id_hotline,"detail"=>"mdr","date"=>date("Y-m-d H:i:s",strtotime("+1 year"))));
        $this->assertTrue(is_numeric($id_hotline_interaction),"L'interaction de test ne s'est pas créée");
        
        return array('id_societe'=>$this->id_societe,'id_hotline'=>$id_hotline,'id_hotline_interaction'=>$id_hotline_interaction);
    }
    
    //@author Nicolas BERTEMONT <nbertemont@absystech.fr>
    public function test_get_years(){
        $elements=$this->creationElementTestStat();
        $years=$this->obj->get_years($elements['id_societe']);
        $this->assertTrue(isset($years[date("Y",strtotime("+1 year"))]),"Le option renvoyé n'est pas bien structuré ou ne renvoi pas les bonnes valeurs");
        $this->assertEquals(date("Y",strtotime("+1 year")),$years[date("Y",strtotime("+1 year"))],"Le option renvoyé n'est pas bien structuré");
    }


    //@author Nicolas BERTEMONT <nbertemont@absystech.fr>
    public function test_societe_options(){
        $elements=$this->creationElementTestStat();
        $options=$this->obj->societe_options(date("Y",strtotime("+1 year")));
        $this->assertEquals("TestTU",$options[$elements['id_societe']],"Le option renvoyé n'est pas correct");
    }

    
    //@author Nicolas BERTEMONT <nbertemont@absystech.fr>
    public function test_stats_special(){
        $stats=$this->obj->stats_special(2010,1,23);
        //check de la méthode ajoutDonnees
        $this->assertEquals(1,count($stats['dataset']),'Problème de récupération des données à afficher');
        //check de la méthode paramGraphe
        $this->assertTrue(is_array($stats['params']),'Problème de récupération des params');
        //le nombre de valeur dans les set de data doit être égal au nombre de catégorie
        $this->assertEquals("Nicolas BERTEMONT",$stats['dataset'][23]['params']['seriesname'],'Problème de params dans les données');
        $this->assertEquals(count($stats['categories']['category']),count($stats['dataset'][23]['set']),"Le nombre de données à afficher en abscisse n'est pas correct");

        //test du contenu
        $this->assertEquals("Sept",$stats['categories']['category']['09']['label'],"Le nom des catégories n'est pas correct");
        $this->assertEquals("Temps (heures)",$stats['params']['yaxisname'],"Le nom des légendes n'est pas correct");
        $this->assertEquals(20.39,$stats['dataset'][23]['set']['02']['value'],'1/ Les valeurs ne sont pas correctes');
        $this->assertEquals("Nicolas BERTEMONT : 84.05",$stats['dataset'][23]['set']['03']['titre'],'2/ Les valeurs ne sont pas correctes');
        $this->assertEquals("hotline_interaction.html%2Cstats%3D1%26annee%3D2010%26mois%3D11%26societe%3D1%26user%3D23%26groupe%3D%26serie%3DNicolas+BERTEMONT",$stats['dataset'][23]['set']['11']['link'],'3/ Les valeurs ne sont pas correctes');
        
        //test en version regroupement par societe et sans préciser de société
        
        $stats_grpe=$this->obj->stats_special(2010,NULL,23,"soc");
        //check de la méthode ajoutDonnees
        $this->assertTrue(count($stats_grpe['dataset'])>1,'2/ Problème de récupération des données à afficher');
        //check de la méthode paramGraphe
        $this->assertTrue(is_array($stats_grpe['params']),'2/ Problème de récupération des params');
        //le nombre de valeur dans les set de data doit être égal au nombre de catégorie
        $this->assertEquals("AbsysTech",$stats_grpe['dataset'][1]['params']['seriesname'],'2/ Problème de params dans les données');
        $this->assertEquals(count($stats_grpe['categories']['category']),count($stats_grpe['dataset'][584]['set']),"2/ Le nombre de données à afficher en abscisse n'est pas correct");

        //test du contenu
        $this->assertEquals("Sept",$stats_grpe['categories']['category']['09']['label'],"Le nom des catégories n'est pas correct");
        $this->assertEquals("Temps (heures)",$stats_grpe['params']['yaxisname'],"Le nom des légendes n'est pas correct");
        $this->assertEquals(20.39,$stats_grpe['dataset'][1]['set']['02']['value'],'4/ Les valeurs ne sont pas correctes');
        $this->assertEquals("00.45",$stats_grpe['dataset'][584]['set']['04']['value'],'5/ Les valeurs ne sont pas correctes');
        $this->assertEquals("Ginger CEBTP : 01.16",$stats_grpe['dataset'][829]['set']['01']['titre'],'6/ Les valeurs ne sont pas correctes');
        $this->assertEquals("hotline_interaction.html%2Cstats%3D1%26annee%3D2010%26mois%3D08%26societe%3D%26user%3D23%26groupe%3Dsoc%26serie%3DGEMP",$stats_grpe['dataset'][584]['set']['08']['link'],'7/ Les valeurs ne sont pas correctes');
    }

    
    
    
    //@author Nicolas BERTEMONT <nbertemont@absystech.fr>
    public function test_stats(){
    
        //pour pouvoir tester, je supprime tous les enregistrements, et en insère de nouveaux
        $this->obj->q->reset()->addConditionNotNull("id_hotline_interaction");
        $this->obj->delete();
        ///création d'une hotline bidon
        $id_hotline=ATF::hotline()->i(array("id_societe"=>$this->id_societe,"hotline"=>"hot lol","pole_concerne"=>"dev","facturation_ticket"=>"oui"));
        $this->assertTrue(is_numeric($id_hotline),"1/ La hotline de test ne s'est pas créée");
        //création d'une hotline_interaction bidon à une année ultérieure
        $id_hotline_interaction=$this->obj->i(array("id_hotline"=>$id_hotline,"detail"=>"mdr",'id_user'=>23,"temps"=>"00:10:00","temps_passe"=>"00:15:00","date"=>date("Y-m-d H:i:s",strtotime("+1 year"))));
        $this->assertTrue(is_numeric($id_hotline_interaction),"1/ L'interaction de test ne s'est pas créée");
        ///création d'une hotline bidon
        $id_hotline2=ATF::hotline()->i(array("id_societe"=>$this->id_societe,"hotline"=>"hot lol2","pole_concerne"=>"dev","facturation_ticket"=>"non"));
        $this->assertTrue(is_numeric($id_hotline2),"2/ La hotline de test ne s'est pas créée");
        //création d'une hotline_interaction bidon à une année ultérieure
        $id_hotline_interaction2=$this->obj->i(array("id_hotline"=>$id_hotline2,"detail"=>"mdr2",'id_user'=>23,"temps"=>"00:10:00","temps_passe"=>"00:15:00","date"=>date("Y-m-d H:i:s",strtotime("+1 year"))));
        $this->assertTrue(is_numeric($id_hotline_interaction2),"2/ L'interaction de test ne s'est pas créée");
        $stats=$this->obj->stats();

        //check de la méthode ajoutDonnees
        $this->assertEquals(2,count($stats['dataset']),'Problème de récupération des données à afficher');
        //check de la méthode paramGraphe
        $this->assertTrue(is_array($stats['params']),'Problème de récupération des params');
        //le nombre de valeur dans les set de data doit être égal au nombre de catégorie
        $this->assertTrue(isset($stats['dataset']['tps_charge_client']) && isset($stats['dataset']['tps_charge_absystech']),'Problème de récupération des dataset');
        $this->assertEquals("A la charge du client",$stats['dataset']['tps_charge_client']['params']['seriesname'],'Problème de params dans les données');
        $this->assertEquals(count($stats['categories']['category']),count($stats['dataset']["tps_charge_client"]['set']),"Le nombre de données à afficher en abscisse n'est pas correct");

        //test du contenu
        $this->assertEquals("Nicolas BERTEMONT",$stats['categories']['category']['23']['label'],"Le nom des catégories n'est pas correct");
        $this->assertEquals("Temps (heures)",$stats['params']['yaxisname'],"Le nom des légendes n'est pas correct");
        $this->assertEquals(00.20,$stats['dataset']["tps_charge_absystech"]['set'][23]['value'],'1/ Les valeurs ne sont pas correctes');
        $this->assertEquals("A la charge du client : 00h10",$stats['dataset']["tps_charge_client"]['set'][23]['titre'],'2/ Les valeurs ne sont pas correctes');
        $this->assertEquals("hotline_interaction.html%2C%26user%3D23%26stats%3D2",$stats['dataset']["tps_charge_absystech"]['set'][23]['link'],'3/ Les valeurs ne sont pas correctes');

    }

    //@author Nicolas BERTEMONT <nbertemont@absystech.fr>
    public function test_stats30(){
        //pour pouvoir tester, je supprime tous les enregistrements, et en insère de nouveaux
        $this->obj->q->reset()->addConditionNotNull("id_hotline_interaction");
        $this->obj->delete();
        ///création d'une hotline bidon
        $id_hotline=ATF::hotline()->i(array("id_societe"=>$this->id_societe,"hotline"=>"hot lol","pole_concerne"=>"dev","facturation_ticket"=>"oui"));
        $this->assertTrue(is_numeric($id_hotline),"1/ La hotline de test ne s'est pas créée");
        //création d'une hotline_interaction bidon à une année ultérieure
        $id_hotline_interaction=$this->obj->i(array("id_hotline"=>$id_hotline,"detail"=>"mdr",'id_user'=>23,"temps"=>"00:15:00","temps_passe"=>"00:20:00","date"=>date("Y-m-d H:i:s",strtotime("-2 week"))));
        $this->assertTrue(is_numeric($id_hotline_interaction),"1/ L'interaction de test ne s'est pas créée");
        ///création d'une hotline bidon
        $id_hotline2=ATF::hotline()->i(array("id_societe"=>$this->id_societe,"hotline"=>"hot lol2","pole_concerne"=>"dev","facturation_ticket"=>"non"));
        $this->assertTrue(is_numeric($id_hotline2),"2/ La hotline de test ne s'est pas créée");
        //création d'une hotline_interaction bidon à une année ultérieure
        $id_hotline_interaction2=$this->obj->i(array("id_hotline"=>$id_hotline2,"detail"=>"mdr2",'id_user'=>23,"temps"=>"00:05:00","temps_passe"=>"00:10:00","date"=>date("Y-m-d H:i:s",strtotime("-1 week"))));
        $this->assertTrue(is_numeric($id_hotline_interaction2),"2/ L'interaction de test ne s'est pas créée");
        
        $stats=$this->obj->stats30();
        
        //check de la méthode ajoutDonnees
        $this->assertEquals(3,count($stats['dataset']),'Problème de récupération des données à afficher');
        //check de la méthode paramGraphe
        $this->assertTrue(is_array($stats['params']),'Problème de récupération des params');
        //le nombre de valeur dans les set de data doit être égal au nombre de catégorie
        $this->assertTrue(isset($stats['dataset']['tps_charge_client']) && isset($stats['dataset']['tps_charge_absystech']),'Problème de récupération des dataset');
        $this->assertEquals("A la charge du client",$stats['dataset']['tps_charge_client']['params']['seriesname'],'Problème de params dans les données');
        $this->assertEquals(count($stats['categories']['category']),count($stats['dataset']["tps_charge_client"]['set']),"Le nombre de données à afficher en abscisse n'est pas correct");

        //test du contenu
        $this->assertEquals(date('W',strtotime("-1 week")),$stats['categories']['category'][date('W',strtotime("-1 week"))]['label'],"Le nom des catégories n'est pas correct");
        $this->assertEquals("Temps (heures)",$stats['params']['yaxisname'],"Le nom des légendes n'est pas correct");
        $this->assertEquals("00.00",$stats['dataset']["tps_charge_absystech"]['set'][date('W',strtotime("-2 week"))]['value'],'1/ Les valeurs ne sont pas correctes');
        $this->assertEquals("Du ".date("d/m/Y",strtotime("-1 week"))." au ".date("d/m/Y",strtotime("-1 week"))." : 00h10",$stats['dataset']["tps_charge_absystech"]['set'][date('W',strtotime("-1 week"))]['titre'],'2/ Les valeurs ne sont pas correctes');
        $this->assertEquals("00.15",$stats['dataset']["tps_charge_client"]['set'][date('W',strtotime("-2 week"))]['value'],'3/ Les valeurs ne sont pas correctes');
        $this->assertEquals("Du ".date("d/m/Y",strtotime("-1 week"))." au ".date("d/m/Y",strtotime("-1 week"))." : 00h00",$stats['dataset']["tps_charge_client"]['set'][date('W',strtotime("-1 week"))]['titre'],'4/ Les valeurs ne sont pas correctes');
    
        // -------- widget ---------
        
        //pour pouvoir tester, je supprime tous les enregistrements, et en insère de nouveaux
        $this->obj->q->reset()->addConditionNotNull("id_hotline_interaction");
        $this->obj->delete();
        ///création d'une hotline bidon
        $id_hotline=ATF::hotline()->i(array("id_societe"=>$this->id_societe,"hotline"=>"hot lol","pole_concerne"=>"dev","facturation_ticket"=>"oui"));
        $this->assertTrue(is_numeric($id_hotline),"1/ La hotline de test ne s'est pas créée");
        //création d'une hotline_interaction bidon à une année ultérieure
        $id_hotline_interaction=$this->obj->i(array("id_hotline"=>$id_hotline,"detail"=>"mdr",'id_user'=>23,"temps"=>"00:15:00","temps_passe"=>"00:20:00","date"=>date("Y-m-d H:i:s",strtotime("-11 week",strtotime("2010-12-27")))));
        $this->assertTrue(is_numeric($id_hotline_interaction),"1/ L'interaction de test ne s'est pas créée");
        ///création d'une hotline bidon
        $id_hotline2=ATF::hotline()->i(array("id_societe"=>$this->id_societe,"hotline"=>"hot lol2","pole_concerne"=>"dev","facturation_ticket"=>"non"));
        $this->assertTrue(is_numeric($id_hotline2),"2/ La hotline de test ne s'est pas créée");
        //création d'une hotline_interaction bidon à une année ultérieure
        $id_hotline_interaction2=$this->obj->i(array("id_hotline"=>$id_hotline2,"detail"=>"mdr2",'id_user'=>23,"temps"=>"00:05:00","temps_passe"=>"00:10:00","date"=>date("Y-m-d H:i:s",strtotime("-10 week",strtotime("2010-12-27")))));
        $this->assertTrue(is_numeric($id_hotline_interaction2),"2/ L'interaction de test ne s'est pas créée");
    
        $stats_widget=$this->obj->stats30("2010-12-27",true);

        $this->assertEquals(3,count($stats_widget['dataset']),'2/ Problème de récupération des données à afficher');
        $this->assertEquals(1,count($stats_widget['dataset']["tps_charge_absystech"]['set']),'3/ Problème de récupération des données à afficher');
        $this->assertEquals(00.10,$stats_widget['dataset']["tps_charge_absystech"]['set'][42]['value'],'5/ Les valeurs ne sont pas correctes');
        $this->assertEquals("Du 18/10/2010 au 18/10/2010 : 00h00",$stats_widget['dataset']["tps_charge_client"]['set'][42]['titre'],'6/ Les valeurs ne sont pas correctes');

    }
    
    //@author Nicolas BERTEMONT <nbertemont@absystech.fr>
    public function test_statsChargeParUser(){
        $stats=$this->obj->statsChargeParUser("2010-12-31");

        //check de la méthode ajoutDonnees
        $this->assertTrue(count($stats['dataset'])>0,'Problème de récupération des données à afficher');
        //check de la méthode paramGraphe
        $this->assertTrue(is_array($stats['params']),'Problème de récupération des params');
        //le nombre de valeur dans les set de data doit être égal au nombre de catégorie
        $this->assertTrue(isset($stats['dataset']['23']),'Problème de récupération des dataset');
        $this->assertEquals("Nicolas BERTEMONT",$stats['dataset']['23']['params']['seriesname'],'Problème de params dans les données');
        $this->assertEquals(count($stats['categories']['category']),count($stats['dataset']["23"]['set']),"Le nombre de données à afficher en abscisse n'est pas correct");

        //test du contenu
        $this->assertEquals(50,$stats['categories']['category']['50']['label'],"Le nom des catégories n'est pas correct");
        $this->assertEquals("Temps (heures)",$stats['params']['yaxisname'],"Le nom des légendes n'est pas correct");
        $this->assertEquals(36.30,$stats['dataset']["12"]['set']["46"]['value'],'1/ Les valeurs ne sont pas correctes');
        $this->assertEquals("Nicolas BERTEMONT (Du 18/10/2010 au 22/10/2010) : 35h02",$stats['dataset']["23"]['set']["42"]['titre'],'2/ Les valeurs ne sont pas correctes');
        $this->assertEquals(19.30,$stats['dataset']["35"]['set']["44"]['value'],'3/ Les valeurs ne sont pas correctes');
        $this->assertEquals("hotline_interaction.html%2Cstats%3D4%26user%3D35%26date_debut%3D2010-11-02%26date_fin%3D2010-11-05",$stats['dataset']["35"]['set']["44"]['link'],'4/ Les valeurs ne sont pas correctes');

    }
    
    //@author Nicolas BERTEMONT <nbertemont@absystech.fr>
    public function test_statsProduction(){
        //juste pour quelques users pour éviter les problèmes d'état (actif/inactif) des users
        $this->obj->liste_user=array(1=>1,12=>1,23=>1);
        
        $stats=$this->obj->statsProduction("2010");

        //check de la méthode ajoutDonnees
        $this->assertTrue(count($stats['dataset'])>0,'Problème de récupération des données à afficher');
        //check de la méthode paramGraphe
        $this->assertTrue(is_array($stats['params']),'Problème de récupération des params');
        //le nombre de valeur dans les set de data doit être égal au nombre de catégorie
        $this->assertTrue(isset($stats['dataset']['tps_facture']),'Problème de récupération des dataset');
        $this->assertEquals("Non produit",$stats['dataset']['tps_non_produit']['params']['seriesname'],'Problème de params dans les données');
        $this->assertEquals(count($stats['categories']['category']),count($stats['dataset']["tps_non_facture"]['set']),"Le nombre de données à afficher en abscisse n'est pas correct");

        //test du contenu
        $this->assertEquals("Mars",$stats['categories']['category']['03']['label'],"Le nom des catégories n'est pas correct");
        $this->assertEquals("Taux (".urlencode("%").")",$stats['params']['yaxisname'],"Le nom des légendes n'est pas correct");
        $this->assertEquals(33,$stats['dataset']["tps_non_facture"]['set']["05"]['value'],'1/ Les valeurs ne sont pas correctes');
        $this->assertEquals("Non produit : 31%25 (124h48)",$stats['dataset']["tps_non_produit"]['set']["02"]['titre'],'2/ Les valeurs ne sont pas correctes');
        $this->assertEquals(9,$stats['dataset']["tps_conge"]['set']["12"]['value'],'3/ Les valeurs ne sont pas correctes');
    }

    //@author Nicolas BERTEMONT <nbertemont@absystech.fr>
    public function test_statsFiltrage(){
        $this->initUserOnly(false);
        
        //********** case 1 **********
        
        //case 1 : on précise société et utilisateur
        ATF::env()->set("_r","annee","2010");
        ATF::env()->set("_r","mois","12");
        ATF::env()->set("_r","stats","1");
        ATF::env()->set("_r","societe","1");
        ATF::env()->set("_r","user","23");
        ATF::env()->set("_r","groupe","");
        ATF::env()->set("_r","serie","Nicolas BERTEMONT");

        $id_filtre_cas1=$this->obj->statsFiltrage();
        
        $this->assertTrue(is_numeric($id_filtre_cas1),'1/ Le filtre n a pas ete cree');
        $filtre_cas1=ATF::filtre_optima()->select($id_filtre_cas1);
        $this->assertEquals(ATF::module()->from_nom('hotline_interaction'),$filtre_cas1['id_module'],'1/ Le filtre ne possede pas les bonnes informations');
        $this->assertEquals('a:5:{s:4:"name";s:15:"Filtre de stats";s:4:"mode";s:3:"AND";s:10:"conditions";a:5:{i:0;a:3:{s:5:"field";s:24:"hotline_interaction.date";s:7:"operand";s:5:"LIKE%";s:5:"value";s:7:"2010-12";}i:1;a:3:{s:5:"field";s:27:"hotline_interaction.id_user";s:7:"operand";s:4:"LIKE";s:5:"value";s:17:"Nicolas BERTEMONT";}i:2;a:3:{s:5:"field";s:18:"hotline.id_societe";s:7:"operand";s:4:"LIKE";s:5:"value";s:9:"AbsysTech";}i:3;a:3:{s:5:"field";s:21:"hotline.pole_concerne";s:7:"operand";s:2:"!=";s:5:"value";s:6:"system";}i:4;a:3:{s:5:"field";s:21:"hotline.pole_concerne";s:7:"operand";s:2:"!=";s:5:"value";s:7:"telecom";}}s:10:"choix_join";s:4:"left";s:9:"jointures";a:1:{i:0;a:3:{s:10:"nom_module";s:7:"hotline";s:6:"module";s:18:"hotline.id_hotline";s:12:"liste_champs";s:30:"hotline_interaction.id_hotline";}}}',$filtre_cas1['options'],'Le options du filtre n est pas correct');
        $this->assertEquals(ATF::$usr->getID(),$filtre_cas1['id_user'],'2/ Le filtre ne possede pas les bonnes informations');
    
        //case 1 : on ne précise pas de société
        ATF::env()->set("_r","annee","2010");
        ATF::env()->set("_r","mois","12");
        ATF::env()->set("_r","stats","1");
        ATF::env()->set("_r","societe","");
        ATF::env()->set("_r","user","23");
        ATF::env()->set("_r","groupe","");
        ATF::env()->set("_r","serie","Nicolas BERTEMONT");

        $id_filtre_cas12=$this->obj->statsFiltrage();

        $filtre_cas12=ATF::filtre_optima()->select($id_filtre_cas12);
        $this->assertEquals('a:5:{s:4:"name";s:15:"Filtre de stats";s:4:"mode";s:3:"AND";s:10:"conditions";a:5:{i:0;a:3:{s:5:"field";s:24:"hotline_interaction.date";s:7:"operand";s:5:"LIKE%";s:5:"value";s:7:"2010-12";}i:1;a:3:{s:5:"field";s:27:"hotline_interaction.id_user";s:7:"operand";s:4:"LIKE";s:5:"value";s:17:"Nicolas BERTEMONT";}i:3;a:3:{s:5:"field";s:21:"hotline.pole_concerne";s:7:"operand";s:2:"!=";s:5:"value";s:6:"system";}i:4;a:3:{s:5:"field";s:21:"hotline.pole_concerne";s:7:"operand";s:2:"!=";s:5:"value";s:7:"telecom";}i:5;a:3:{s:5:"field";s:18:"hotline.id_societe";s:7:"operand";s:4:"LIKE";s:5:"value";s:17:"Nicolas BERTEMONT";}}s:10:"choix_join";s:4:"left";s:9:"jointures";a:1:{i:0;a:3:{s:10:"nom_module";s:7:"hotline";s:6:"module";s:18:"hotline.id_hotline";s:12:"liste_champs";s:30:"hotline_interaction.id_hotline";}}}',$filtre_cas12['options'],'Le options du filtre n est pas correct (case 1, premier if)');
    
        //case 1 : si on ne précise pas de user
        ATF::env()->set("_r","annee","2010");
        ATF::env()->set("_r","mois","12");
        ATF::env()->set("_r","stats","1");
        ATF::env()->set("_r","societe","1");
        ATF::env()->set("_r","user","");
        ATF::env()->set("_r","groupe","util");
        ATF::env()->set("_r","serie","Nicolas BERTEMONT");
        
        $id_filtre_cas13=$this->obj->statsFiltrage();

        $filtre_cas13=ATF::filtre_optima()->select($id_filtre_cas13);
        $this->assertEquals('a:5:{s:4:"name";s:15:"Filtre de stats";s:4:"mode";s:3:"AND";s:10:"conditions";a:5:{i:0;a:3:{s:5:"field";s:24:"hotline_interaction.date";s:7:"operand";s:5:"LIKE%";s:5:"value";s:7:"2010-12";}i:2;a:3:{s:5:"field";s:18:"hotline.id_societe";s:7:"operand";s:4:"LIKE";s:5:"value";s:9:"AbsysTech";}i:3;a:3:{s:5:"field";s:21:"hotline.pole_concerne";s:7:"operand";s:2:"!=";s:5:"value";s:6:"system";}i:4;a:3:{s:5:"field";s:21:"hotline.pole_concerne";s:7:"operand";s:2:"!=";s:5:"value";s:7:"telecom";}i:5;a:3:{s:5:"field";s:27:"hotline_interaction.id_user";s:7:"operand";s:4:"LIKE";s:5:"value";s:17:"Nicolas BERTEMONT";}}s:10:"choix_join";s:4:"left";s:9:"jointures";a:1:{i:0;a:3:{s:10:"nom_module";s:7:"hotline";s:6:"module";s:18:"hotline.id_hotline";s:12:"liste_champs";s:30:"hotline_interaction.id_hotline";}}}',$filtre_cas13['options'],'Le options du filtre n est pas correct (case 1, elseif)');
    
        //********** case 2 **********
        ATF::env()->set("_r","stats","2");
        ATF::env()->set("_r","user","23");
        
        $id_filtre_cas2=$this->obj->statsFiltrage();

        $filtre_cas2=ATF::filtre_optima()->select($id_filtre_cas2);
        $this->assertEquals('a:3:{s:4:"name";s:15:"Filtre de stats";s:4:"mode";s:3:"AND";s:10:"conditions";a:2:{i:0;a:3:{s:5:"field";s:24:"hotline_interaction.date";s:7:"operand";s:2:">=";s:5:"value";s:10:"'.date("Y-m-d",strtotime(date("Y-m-d")." -60 days")).'";}i:1;a:3:{s:5:"field";s:27:"hotline_interaction.id_user";s:7:"operand";s:4:"LIKE";s:5:"value";s:17:"Nicolas BERTEMONT";}}}',$filtre_cas2['options'],'Le options du filtre n est pas correct (case 2)');
    
        //********** case 3 **********
        //tps_charge_absystech
        ATF::env()->set("_r","stats","3");
        ATF::env()->set("_r","charge","tps_charge_absystech");  
        ATF::env()->set("_r","date_debut","2010-12-13");
        ATF::env()->set("_r","date_fin","2010-12-17");
        
        $id_filtre_cas31=$this->obj->statsFiltrage();
        
        $filtre_cas31=ATF::filtre_optima()->select($id_filtre_cas31);
        $this->assertEquals('a:5:{s:4:"name";s:15:"Filtre de stats";s:4:"mode";s:3:"AND";s:10:"choix_join";s:4:"left";s:9:"jointures";a:1:{i:0;a:3:{s:10:"nom_module";s:7:"hotline";s:6:"module";s:18:"hotline.id_hotline";s:12:"liste_champs";s:30:"hotline_interaction.id_hotline";}}s:10:"conditions";a:3:{i:0;a:3:{s:5:"field";s:24:"hotline_interaction.date";s:7:"operand";s:2:">=";s:5:"value";s:10:"2010-12-13";}i:1;a:3:{s:5:"field";s:24:"hotline_interaction.date";s:7:"operand";s:2:"<=";s:5:"value";s:19:"2010-12-17 23:59:59";}i:2;a:3:{s:5:"field";s:26:"hotline.facturation_ticket";s:7:"operand";s:1:"=";s:5:"value";s:3:"non";}}}',$filtre_cas31['options'],'Le options du filtre n est pas correct (case 3 tps_charge_absystech)');

        //tps_charge_client
        ATF::env()->set("_r","stats","3");
        ATF::env()->set("_r","charge","tps_charge_client"); 
        ATF::env()->set("_r","date_debut","2010-12-06");
        ATF::env()->set("_r","date_fin","2010-12-10");
        
        $id_filtre_cas32=$this->obj->statsFiltrage();
        
        $filtre_cas32=ATF::filtre_optima()->select($id_filtre_cas32);
        $this->assertEquals('a:5:{s:4:"name";s:15:"Filtre de stats";s:4:"mode";s:3:"AND";s:10:"choix_join";s:4:"left";s:9:"jointures";a:1:{i:0;a:3:{s:10:"nom_module";s:7:"hotline";s:6:"module";s:18:"hotline.id_hotline";s:12:"liste_champs";s:30:"hotline_interaction.id_hotline";}}s:10:"conditions";a:3:{i:0;a:3:{s:5:"field";s:24:"hotline_interaction.date";s:7:"operand";s:2:">=";s:5:"value";s:10:"2010-12-06";}i:1;a:3:{s:5:"field";s:24:"hotline_interaction.date";s:7:"operand";s:2:"<=";s:5:"value";s:19:"2010-12-10 23:59:59";}i:2;a:3:{s:5:"field";s:26:"hotline.facturation_ticket";s:7:"operand";s:1:"=";s:5:"value";s:3:"oui";}}}',$filtre_cas32['options'],'Le options du filtre n est pas correct (case 3 tps_charge_client)');
    
        //********* case 4 **********
        ATF::env()->set("_r","stats","4");
        ATF::env()->set("_r","user","23");  
        ATF::env()->set("_r","date_debut","2010-12-28");
        ATF::env()->set("_r","date_fin","2010-12-31");
        
        $id_filtre_cas4=$this->obj->statsFiltrage();
        
        $filtre_cas4=ATF::filtre_optima()->select($id_filtre_cas4);
        $this->assertEquals('a:3:{s:4:"name";s:15:"Filtre de stats";s:4:"mode";s:3:"AND";s:10:"conditions";a:3:{i:0;a:3:{s:5:"field";s:24:"hotline_interaction.date";s:7:"operand";s:2:">=";s:5:"value";s:10:"2010-12-28";}i:1;a:3:{s:5:"field";s:24:"hotline_interaction.date";s:7:"operand";s:2:"<=";s:5:"value";s:19:"2010-12-31 23:59:59";}i:2;a:3:{s:5:"field";s:27:"hotline_interaction.id_user";s:7:"operand";s:4:"LIKE";s:5:"value";s:17:"Nicolas BERTEMONT";}}}',$filtre_cas4['options'],'Le options du filtre n est pas correct (case 4)');
    
        //recheck des valeurs supplémentaires du filtre pour vérif supplémentaire
        $this->assertEquals(ATF::module()->from_nom('hotline_interaction'),$filtre_cas4['id_module'],'3/ Le filtre ne possede pas les bonnes informations');
        $this->assertEquals(ATF::$usr->getID(),$filtre_cas4['id_user'],'4/ Le filtre ne possede pas les bonnes informations');
    }

    //@author Yann GAUTHERON <ygautheron@absystech.fr>
    public function test_getRecentForMobile(){
        //insertion de données bidons pour que le test fonctionne
        $id_soc=ATF::societe()->i(array("societe"=>"soc lol"));
        $id_hot=ATF::hotline()->i(array("id_societe"=>$id_soc,"hotline"=>"hot lol"));
        ATF::hotline_interaction()->i(array("id_hotline"=>$id_hot,"temps"=>"01:00:00","id_user"=>$this->id_user));
        
        ATF::$usr->last_activity = date("Y-m-d H:i:s",time()-86400*365);
        $this->assertGreaterThan(0,count($this->obj->getRecentForMobile()),"Aucune interaction dans la base ?");
        $this->assertGreaterThan(0,$this->obj->getRecentForMobile(true),"Compte incorrect, Aucune interaction dans la base ?");
    }

    //@author Nicolas BERTEMONT <nbertemont@absystech.fr>
    public function test_recupTpsConge(){
        $this->obj->recupTpsConge($tps_conge,23,2010);
        $this->assertTrue(is_array($tps_conge),"1/ La structure n'est pas correcte");
        $this->assertEquals(70,$tps_conge['07'],"2/ Ne récupère pas les bonnes valeurs");
        $this->assertEquals(24.5,$tps_conge['12'],"3/ Ne récupère pas les bonnes valeurs");
        $this->assertFalse(isset($tps_conge['01']),"4/ Ne récupère pas les bonnes valeurs");
        
        //création de conge pour entrer dans tous les cas de figure
        ATF::conge()->multi_insert(array(
            0=>array('date_debut'=>'2009-12-28','date_fin'=>'2010-01-05',"id_user"=>23,'etat'=>'ok')
            ,1=>array('date_debut'=>'2010-12-28','date_fin'=>'2011-01-05',"id_user"=>23,'etat'=>'ok')
            ,2=>array('date_debut'=>'2010-09-28','date_fin'=>'2010-10-05',"id_user"=>23,'etat'=>'ok')
        ));

        $this->obj->recupTpsConge($tps_conge2,23,2010);
        $this->assertTrue(is_array($tps_conge2),"5/ La structure n'est pas correcte");
        $this->assertTrue(isset($tps_conge2['02']),"6/ Ne récupère pas les bonnes valeurs");
        $this->assertEquals(7,$tps_conge2['02'],"6bis/ Ne récupère pas les bonnes valeurs");
        $this->assertEquals(52.5,$tps_conge2['12'],"7/ Ne récupère pas les bonnes valeurs");
        $this->assertEquals(70,$tps_conge2['07'],"8/ Ne récupère pas les bonnes valeurs");
    }

    //@author Nicolas BERTEMONT <nbertemont@absystech.fr>
    public function test_TpsNonProduit(){
        $tnp=$this->obj->TpsNonProduit(2010);
        $this->assertEquals(161,$tnp["03"],"1/ Ne renvoie pas le bon nombre d'heure");
        $this->assertEquals(140,$tnp["02"],"2/ Ne renvoie pas le bon nombre d'heure");
        $tnp2=$this->obj->TpsNonProduit(2012);
        $this->assertEquals(147,$tnp2["02"],"3/ Ne renvoie pas le bon nombre d'heure");
    }

    //@author Nicolas BERTEMONT <nbertemont@absystech.fr>
    public function test_nb_jours(){
        $this->assertEquals(31,$this->obj->nb_jours(12,2010),"1/ Le nombre de jour est incorrect");
        $this->assertEquals(31,$this->obj->nb_jours(1,2010),"2/ Le nombre de jour est incorrect");
        $this->assertEquals(30,$this->obj->nb_jours(4,2010),"3/ Le nombre de jour est incorrect");
        $this->assertEquals(28,$this->obj->nb_jours(2,2010),"4/ Le nombre de jour est incorrect");
        $this->assertEquals(29,$this->obj->nb_jours(2,2012),"5/ Le nombre de jour est incorrect");
    }
    
    //@author Nicolas BERTEMONT <nbertemont@absystech.fr>
    public function test_is_bissextile(){
        $this->assertTrue($this->obj->is_bissextile("2012"),"Ce n'est pas une année bissextile");
        $this->assertFalse($this->obj->is_bissextile("2011"),"C'est une année bissextile");
    }
    
    //@author Nicolas BERTEMONT <nbertemont@absystech.fr>
    public function test_AddTime(){
        $this->assertEquals("19.50",$this->obj->AddTime("10.25","9.25"),"1/AddTime ne renvoie pas la bonne valeur");
        $this->assertEquals("511.10",$this->obj->AddTime("500.35","10.35"),"2/AddTime ne renvoie pas la bonne valeur");
    }
    
    //@author Nicolas BERTEMONT <nbertemont@absystech.fr>
    public function test_SubTime(){
        $this->assertEquals("1.00",$this->obj->SubTime("10.25","9.25"),"1/SubTime ne renvoie pas la bonne valeur");
        $this->assertEquals("489.35",$this->obj->SubTime("500.10","10.35"),"2/SubTime ne renvoie pas la bonne valeur");
    }
    
    //@author Nicolas BERTEMONT <nbertemont@absystech.fr>
    public function test_modifEtat(){
        $this->assertEquals(serialize(array('dev'=>1,'system'=>0,'telecom'=>0)),serialize($this->obj->pole_concerne),"L'initialisation du pole_concerné a changé");
        $this->obj->modifEtat("pole_concerne","system","true");
        $this->assertEquals(serialize(array('dev'=>1,'system'=>1,'telecom'=>0)),serialize($this->obj->pole_concerne),"La modification du pole_concerné a échoué");

        $this->assertEquals(serialize(array('oui'=>1,'non'=>1)),serialize($this->obj->facturation_ticket),"L'initialisation du facturation_ticket a changé");
        $this->obj->modifEtat("facturation_ticket","oui","false");
        $this->assertEquals(serialize(array('oui'=>0,'non'=>1)),serialize($this->obj->facturation_ticket),"La modification du facturation_ticket a échoué");
    }
    
    //@author Nicolas BERTEMONT <nbertemont@absystech.fr>
    public function test_get_user(){
        $users=$this->obj->get_user();
        $this->assertTrue(count($users)>0,"La méthode ne renvoie pas les données");
        $this->assertEquals("M Yann-Gaël GAUTHERON",$users[1],"La méthode ne renvoie pas les bonnes données");
        //on regarde qu'il a également pris en compte les users inactifs
        //$this->assertEquals("Mlle Fanny DECLERCK",$users[28],"La méthode ne renvoie pas les users inactifs");
    }
    
    //@author Nicolas BERTEMONT <nbertemont@absystech.fr>
    public function test_changeUser(){
        $this->assertTrue(is_array($this->obj->liste_user) && count($this->obj->liste_user)>0,"1/ Problème sur l'initialisation du liste_user");    
        $this->assertEquals(1,$this->obj->liste_user[12],"2/ Problème sur l'initialisation du liste_user"); 
        $this->assertEquals(1,$this->obj->liste_user[1],"3/ Problème sur l'initialisation du liste_user");  
        $this->obj->changeUser(array('tabuser'=>array(0=>12)));
        $this->assertTrue(is_array($this->obj->liste_user) && count($this->obj->liste_user)>0,"1/ Problème sur la modification du liste_user"); 
        $this->assertEquals(1,$this->obj->liste_user[12],"2/ Problème sur la modification du liste_user");  
        $this->assertEquals(0,$this->obj->liste_user[1],"3/ Problème sur la modification du liste_user");   
        $this->assertTrue(count(array_flip($this->obj->liste_user))==2,"4/ Problème sur la modification du liste_user");
        $cr=ATF::$cr->getCrefresh();
        $this->assertEquals("stats_menu.tpl.htm",$cr['main']['template'],"Le template n'est pas relié correctement");
    }
    
    //@author Nicolas BERTEMONT <nbertemont@absystech.fr>
    public function test_getUserActif(){
        $users=$this->obj->getUserActif();
        $this->assertTrue(count($users)>0,"La méthode ne renvoie pas les données");
        $this->assertEquals(1,$users[12],"La méthode ne renvoie pas les bonnes données");
        //on regarde qu'il a également pris en compte les users inactifs
        $this->assertFalse(isset($users[28]),"La méthode renvoie les users inactifs");
    }
    
    // @author Morgan FLEURQUIN <mfleurquin@absystech.fr> 
    public function test_isIntervenant(){
        $user=array(
            "login"=>"altTutul"
            ,"password" => "toto"
            ,"id_societe" => 1
            ,"prenom" => "toto"
            ,"nom" => "tata"
            ,"email" => "toto@absystech.fr"         
        );
        $id_user = ATF::user()->insert($user);

        $hotline = array(
            "hotline"=>"HotlineTuTest"
            ,"id_societe"=>1
            ,"detail"=>"HotlineTuTest"
            ,"date_debut"=>date('Y-m-d')
            ,"id_contact"=>41
            ,"id_user"=>$id_user
            ,"visible"=>"oui"
            ,"pole_concerne"=>"dev"
            ,"urgence"=>'detail'
            ,'charge'=>"intervention" //précisé pour éviter les problèmes avec la méthode "setbillingmode"
        );
        $id_hotline = ATF::hotline()->insert($hotline);
        
        $this->assertEquals(false,$this->obj->isIntervenant($id_user, $id_hotline),"1 - La méthode isIntervenant ne renvoie pas la bonne valeur");

        $interaction = array(
            "id_hotline" => $id_hotline
            ,"id_user" => $id_user
            ,"date"=>"2009-12-10 12:00:00"
            ,"duree_presta"=>'00:45'
            ,"heure_debut_presta"=>"14:00"
            ,"heure_fin_presta"=>"14:45"
            ,"credit_presta"=>0.00
            ,"no_test_credit"=>true
            ,"etat_wait"=>true
            ,"detail"=>'detail02'
            ,"actifNotify" => ""            
        );
        ATF::hotline_interaction()->insert($interaction,$this->s);
        $this->assertEquals(true,$this->obj->isIntervenant($id_user, $id_hotline),"2 -La méthode isIntervenant ne renvoie pas la bonne valeur");
        
    }
    
    // @author Morgan FLEURQUIN <mfleurquin@absystech.fr> 
    public function test_select_data(){
        $res = $this->obj->select_data($this->s,false);
        $this->assertTrue($res["count"]>0,'Count à 0 ???');
    }
  
};
?>