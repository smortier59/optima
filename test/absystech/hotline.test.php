<?
/**
* @todo Tu des notices
* @todo Tu du contenu des mails
* @todo Tu des stats !!!
*/
class hotline_test extends ATF_PHPUnit_Framework_TestCase {
	/**
	* Hotline de test
	* @var int
	*/
	private $id_hotline;
		
	protected function setUp() {
        ATF::db()->begin_transaction();
        ATF::hotline()->q->reset()->where("hotline.date","2015-01-01 00:00:00","AND",false,"<");
        if($hotlines = ATF::hotline()->select_all()){
            foreach ($hotlines as $key => $value) {
                ATF::hotline()->d($value["id_hotline"]);
            }
        }
        ATF::db()->commit_transaction();


		ATF::initialize();
		$this->initUser();
		//ATF::db()->begin_transaction(true);
	}
	
	protected function tearDown() {
		ATF::db()->rollback_transaction(true);
		//Flush des notices
		ATF::$msg->getNotices();
	}

	//Initialisation d'un jeu de test holtine
	//@author Jérémie Gwiazdowski <jgw@absystech.fr>	
	private function initHotline($id_user="id_user"){
		$this->date_terminee=date("Y-m-d h:i:s",strtotime("+3 Day"));
		$hotline = array(
			"hotline"=>"HotlineTuTest"
			,"id_societe"=>$this->id_societe
			,"detail"=>"HotlineTuTest"
			,"date_debut"=>date("Y-m-d")
			,"id_contact"=>$this->id_contact
			,"id_user"=>$this->$id_user
			,"visible"=>"oui"
			,"pole_concerne"=>"dev"
			,"date_terminee"=>$this->date_terminee
			,"estimation"=>"01:00:00"
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

	// Initialisation d'un jeu de tests d'affaire
	// @author Jérémie Gwiazdowski <jgw@absystech.fr>
	private function initAffaire(){
		$this->devis[ATF::devis()->table]["id_contact"]=$this->id_contact;
		$this->devis[ATF::devis()->table]['resume']='Tu_devis';
		$this->devis[ATF::devis()->table]['id_societe']=1;
		$this->devis[ATF::devis()->table]['validite']=date('Y-m-d');
		$this->devis[ATF::devis()->table]['prix']="200";
		$this->devis[ATF::devis()->table]['frais_de_port']="50";
		$this->devis[ATF::devis()->table]['prix_achat']="50";
		$this->devis["devis"]["date"]=date('Y-m-d');
		$this->devis["values_devis"]=array("produits"=>'[{"devis_ligne__dot__ref":"TU","devis_ligne__dot__produit":"Tu_devis","devis_ligne__dot__quantite":"15","devis_ligne__dot__poids":"10","devis_ligne__dot__prix":"10","devis_ligne__dot__prix_achat":"10","devis_ligne__dot__id_fournisseur":"1","devis_ligne__dot__id_compte_absystech":"1","devis_ligne__dot__marge":97.14,"devis_ligne__dot__id_fournisseur_fk":"1"}]');
		$this->id_devis = ATF::devis()->insert($this->devis,$this->s);
		$this->id_affaire = ATF::devis()->select($this->id_devis,"id_affaire");
	}
	
	// Initialisation d'un utilisateur bis utilisé pour les mails hotline
	// @author Jérémie Gwiazdowski <jgw@absystech.fr>
	private function initAltUser(){
		//Création d'un utilisateur
		$user=array(
			"login"=>"altTutul"
			,"password"=>"tu"
			,"civilite"=>"M"
			,"prenom"=>"class:"//.get_class($this)
			,"nom"=>"unitaire"
			,"pole"=>"dev"
			,"id_agence"=>$this->id_agence
			,"id_profil"=>$this->id_profil
			,"email"=>"tu@absystech.fr"
			,"custom"=>serialize(array("preference"=>array("langue"=>"fr")))
			,"date_connection"=>date("Y-m-d H:i:s")
		);
		$this->alt_id_user = ATF::user()->i($user);
	}

    
     // @author Morgan FLEURQUIN <mfleuerquin@absystech.fr>
    public function test_getCreditUtilises(){
        $this->initUser(false);
        $this->initHotline();
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
        $this->assertEquals($this->obj->getCreditUtilises($this->id_hotline),0.25,"Le nombre de credit n'est pas .... bon 1!");

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
        $this->assertEquals($this->obj->getCreditUtilises($this->id_hotline),0.50,"Le nombre de credit n'est pas .... bon 2!");

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
        $this->assertEquals($this->obj->getCreditUtilises($this->id_hotline),0.75,"Le nombre de credit n'est pas .... bon 3!");


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
        $this->assertEquals($this->obj->getCreditUtilises($this->id_hotline),1,"Le nombre de credit n'est pas .... bon 4!");

        if (!$this->id_hotline) $this->initHotline();
        $hotline_interaction['id_hotline']=$this->id_hotline;
        $hotline_interaction['date']=date('Y-m-d H:i:s');
        $hotline_interaction['duree_presta']='00:50';
        $hotline_interaction['heure_debut_presta']='14:00';
        $hotline_interaction['heure_fin_presta']='14:50';
        $hotline_interaction['credit_presta']=0.77;
        $hotline_interaction['no_test_credit']=true;
        $hotline_interaction['etat']='fixing';
        $hotline_interaction['detail']='detail01';
        $hotline_interaction['visible'] ='oui';
        $hotline_interaction['hotline_interaction']=$hotline_interaction;
        $this->id_hotline_interaction = ATF::hotline_interaction()->insert($hotline_interaction,$this->s);
        $this->assertEquals($this->obj->getCreditUtilises($this->id_hotline),2,"Le nombre de credit n'est pas .... bon 5!");

    }

     public function test_getSecond(){
        $this->assertEquals(3725,$this->obj->getSecond("01:02:05"),"GetSecond incorrect");
    }

    public function test_getModeFacturation(){
        $this->assertEquals(true,$this->obj->getModeFacturation(array("id_hotline"=>12888)),"getModeFacturation 1 incorrect");
        $this->assertEquals(false,$this->obj->getModeFacturation(array("id_hotline"=>12990)),"getModeFacturation 2 incorrect");
    }

    public function test_getJoursOuvres(){
        $this->assertEquals(7,$this->obj->getJoursOuvres("2016-01-01","2016-01-11"),"GetSecond incorrect");
    }

    public function test_getTauxHorraire(){
        //Taux horaire avec facture
        $this->assertEquals(650.00 , $this->obj->getTauxHorraire(8723) , "GetTauxHorraire avec facture incorrect");
        
        //Taux horaire sans facture
        $this->assertEquals(27.92 , $this->obj->getTauxHorraire(8535) , "GetTauxHorraire sans facture incorrect");
    }
    

    public function test_requetebyUserParMois(){
        $res1 = $this->obj->requetebyUserParMois(-1,array("2015-12","2015-12"));
        
        /*$this->assertEquals(43.75 , $res1["dataset"]["oui"]["set"][58]["value"]  , "Erreur 1");
        $this->assertEquals(4.00 , $res1["dataset"]["non"]["set"][58]["value"]  , "Erreur 2");
        $this->assertEquals(21.00 , $res1["dataset"]["conges"]["set"][58]["value"]  , "Erreur 3");*/

        $res2 = $this->obj->requetebyUserParMois("2015-11",array("2015-11","2015-11"));
       /* log::logger($res2 , "mfleurquin");
        $this->assertEquals(34.20 , $res2["dataset"]["oui"]["set"][55]["value"]  , "Erreur 4");
        $this->assertEquals(0.00 ,  $res2["dataset"]["non"]["set"][55]["value"]  , "Erreur 5");
        $this->assertEquals(0.00 ,  $res2["dataset"]["conges"]["set"][55]["value"]  , "Erreur 6");*/
    }





    public function test_requetebyUser7joursGlissants(){
        $this->initUser(false);
        $this->initHotline();
        $this->initInteractions();
        $retour = $this->obj->requetebyUser7joursGlissants($this->id_user);
        $this->assertEquals(1.00,$retour["dataset"]["temps_passe"]["set"][date("Y-m-d")]["value"],"requetebyUser7joursGlissants incorrect");
        $this->assertEquals("Tps passé : 1.00 H",$retour["dataset"]["temps_passe"]["set"][date("Y-m-d")]["titre"],"requetebyUser7joursGlissants incorrect");
        $this->assertEquals(1.00,$retour["dataset"]["temps"]["set"][date("Y-m-d")]["value"],"requetebyUser7joursGlissants incorrect");
        $this->assertEquals("Tps facturé : 1.00 H",$retour["dataset"]["temps"]["set"][date("Y-m-d")]["titre"],"requetebyUser7joursGlissants incorrect");
    }




    
    //@author Yann GAUTHERON <ygautheron@absystech.fr>  
    //@author Yann GAUTHERON <ygautheron@absystech.fr>  
    public function test_getRecentForMobile(){
        ATF::$usr->last_activity = "2008-01-01 00:00:00";
        ATF::$usr->set('pole','dev');
        $r = $this->obj->rpcGetRecentForMobile(array("countUnseenOnly"=>true));
        $this->assertGreaterThan(630,$r);
        
        ATF::$usr->last_activity = "2016-01-01 00:00:00";
        
        $this->obj->q->reset();
        $r = $this->obj->rpcGetRecentForMobile(array("countUnseenOnly"=>false,"limit"=>100));
        $this->assertEquals(100,count($r),"Mauvais nombre de hotline retournée");
        /*$this->assertEquals('a:1:{i:0;a:30:{s:15:"hotline.hotline";s:69:"Créer une alerte qui vérifie, à chaque import de données Saturne.";s:21:"hotline.pole_concerne";s:3:"dev";s:18:"hotline.id_societe";s:7:"Alerteo";s:16:"intervenant_user";s:2:"35";s:19:"intervenant_contact";s:0:"";s:10:"id_hotline";s:3:"449";s:5:"temps";s:4:"6.50";s:9:"duree_dep";s:4:"0.00";s:12:"duree_presta";s:4:"6.50";s:21:"hotline.id_affaire_fk";s:0:"";s:15:"hotline.urgence";s:6:"detail";s:12:"hotline.etat";s:5:"payee";s:15:"hotline.visible";s:3:"oui";s:16:"hotline.wait_mep";s:3:"non";s:18:"hotline.avancement";s:0:"";s:18:"hotline.estimation";s:0:"";s:21:"hotline.date_terminee";s:0:"";s:15:"hotline.id_user";s:20:"Mathieu TRIBOUILLARD";s:12:"temps_estime";s:0:"";s:11:"temps_total";s:4:"6.35";s:16:"hotline.priorite";s:0:"";s:21:"date_last_interaction";s:19:"2010-04-06 12:19:46";s:21:"hotline.id_societe_fk";s:2:"18";s:18:"hotline.id_user_fk";s:2:"35";s:18:"hotline.id_hotline";s:3:"449";s:11:"intervenant";s:20:"Mathieu TRIBOUILLARD";s:7:"contact";s:0:"";s:9:"humanDate";s:10:"=> Non vus";s:5:"heure";s:5:"12h19";s:16:"indexSectionDate";s:0:"";}}'
            ,serialize(array($r[0])),"Erreur 1");*/
    }

   
    

    public function test_setbillingModeNewAbsystech() {
        $this->initUser(false);
        $this->initHotline();
        $h = $this->obj->select($this->id_hotline);       

        ATF::user()->u(array("id_user"=>$h["id_user"], "email"=>"test@absystech.fr"));

        $infos = array("id_hotline"=>$this->id_hotline,"charge"=>"rd","type_requete"=>"charge_absystech","send_mail"=>true);

        $r = $this->obj->setbillingModeNew($infos);

        $this->assertTrue($r,"Erreur  de retour");

        $h = $this->obj->select($this->id_hotline);

        $this->assertEquals("non",$h['facturation_ticket'],"Erreur dans le facturation_ticket setté");
        $this->assertEquals("fixing",$h['etat'],"Erreur dans le etat setté");
        $this->assertNull($h['ok_facturation'],"Erreur dans le ok_facturation setté");
        $this->assertNull($h['id_affaire'],"Erreur dans le id_affaire setté");


    }   


    public function test_setbillingModeNewClient() {
        $this->initUser(false);
        $this->initHotline();       

        $h = $this->obj->select($this->id_hotline); 
        ATF::user()->u(array("id_user"=>$h["id_user"], "email"=>"test@absystech.fr"));

        $infos = array("id_hotline"=>$this->id_hotline,"charge"=>"rd","type_requete"=>"charge_client","relance"=>true,"refresh"=>true);

        $r = $this->obj->setbillingModeNew($infos);

        $this->assertTrue($r,"Erreur  de retour");

        $h = $this->obj->select($this->id_hotline);

        $this->assertEquals("oui",$h['facturation_ticket'],"Erreur dans le facturation_ticket setté");
        $this->assertEquals("wait",$h['etat'],"Erreur dans le etat setté");
        $this->assertNull($h['ok_facturation'],"Erreur dans le ok_facturation setté");
        $this->assertNull($h['id_affaire'],"Erreur dans le id_affaire setté");
    }



    
    // @author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_select_all_test1(){
        ATF::db()->truncate("hotline");
        $this->initUser(false);
        $this->initHotline();
        $this->initInteractions();

        $id_user2=ATF::user()->insert(array(
            "login"=>"toto",
            "password"=>"titi",
            "prenom"=>"prenom",
            "nom"=>"nom"
        ));
        //Nouvelle hotline estimation 07:00 - user 2
        $id_hotline_4=$this->obj->insert(
            array("charge"=>"intervention"
                 ,"hotline"=>"test"
                 ,"detail"=>"detail"
                 ,"pole_concerne"=>"dev"
                 ,"id_societe"=>$this->id_societe
                 ,"id_contact"=>$this->id_contact
                 ,"type_requete"=>"charge_client"
                 ,"estimation"=>"07:00"
                 ,"id_user"=>$id_user2));
        $this->assertNotNull($id_hotline_4,"La requête ne se crée pas... - assert 1");

        //Nouvelle hotline estimation 25:00 - //temps passe 01:00
        $id_hotline_3=$this->obj->insert(
            array("charge"=>"intervention"
                 ,"hotline"=>"test"
                 ,"detail"=>"detail"
                 ,"pole_concerne"=>"dev"
                 ,"id_societe"=>$this->id_societe
                 ,"id_contact"=>$this->id_contact
                 ,"type_requete"=>"charge_client"
                 ,"estimation"=>"25:00"));
        $this->assertNotNull($id_hotline_3,"La requête ne se crée pas... - assert 1");

        
        //Nouvelle hotline estimation 25:00 / temps passe 26:00
        $id_hotline_2=$this->obj->insert(
            array("charge"=>"intervention"
                 ,"hotline"=>"test"
                 ,"detail"=>"detail"
                 ,"pole_concerne"=>"dev"
                 ,"id_societe"=>$this->id_societe
                 ,"id_contact"=>$this->id_contact
                 ,"type_requete"=>"charge_client"
                 ,"estimation"=>"25:00"));
        $this->assertNotNull($id_hotline_2,"La requête ne se crée pas... - assert 2");
        
        $hotline_interaction["id_hotline"] =$id_hotline_2;
        $hotline_interaction["date"] ="2015-12-02 12:00:00";
        $hotline_interaction['duree_presta']='30:00';
        $hotline_interaction['heure_debut_presta']='14:00';
        $hotline_interaction['heure_fin_presta']='14:30';
        $hotline_interaction['credit_presta']="0.50"; 
        $hotline_interaction['no_test_credit']=true;
        $hotline_interaction["etat"] ="fixing";
        $hotline_interaction["detail"] ="detail";
        $hotline_interaction["visible"] ="oui";
        $hotline_interaction["hotline_interaction"] = $hotline_interaction;
        $this->id_hotline_interaction_ = ATF::hotline_interaction()->insert($hotline_interaction,$this->s);     
                
        //Nouvelle hotline estimation 01:00 - temps passe 0:00
        $id_hotline_1=$this->obj->insert(
            array("charge"=>"intervention"
                 ,"hotline"=>"test"
                 ,"detail"=>"detail"
                 ,"urgence"=>"detail"
                 ,"pole_concerne"=>"dev"
                 ,"id_societe"=>$this->id_societe
                 ,"id_contact"=>$this->id_contact
                 ,"type_requete"=>"charge_client"
                 ,"estimation"=>"01:00"));
        $this->assertNotNull($id_hotline_1,"La requête ne se crée pas... - assert 3");

        $this->obj->q->reset('limit')->setLimit(10);
        $data=$this->obj->select_data();

        //Premier enregistrement
        $record=$data["data"][0];
        $this->assertTrue(is_array($record),"record 1.0");
        $this->assertEquals($record["id_hotline"],$id_hotline_1,"record 1.1");
        $this->assertEquals($record["duree_presta"],"0.00","record 1.2");
        $this->assertEquals($record["hotline.urgence"],"detail","record 1.3");
        $this->assertEquals($record["hotline.etat"],"wait","record 1.4");
        $this->assertEquals($record["hotline.wait_mep"],"non","record 1.5");
        $this->assertEquals($record["hotline.estimation"],"01:00:00","record 1.7");
        $this->assertEquals($record["temps_estime"],"1.00","record 1.8");
        $this->assertEquals($record["temps_total"],"0.00","record 1.9");
        $this->assertEquals($record["hotline.priorite"],20,"record 1.10");
        $this->assertEquals($record["dead_line"],ATF::$usr->date_trans(date("Y-m-d",strtotime("now")),false),"record 1.11");
        $this->assertEquals($record["urgent"],"now","record 1.12");
        
        //Deuxième enregistrement
        $record=$data["data"][1];
        $this->assertTrue(is_array($record),"record 2.0");
        $this->assertEquals($record["id_hotline"],$id_hotline_2,"record 2.1");
        $this->assertEquals($record["duree_presta"],"30.00","record 2.2");
        $this->assertEquals($record["hotline.urgence"],"detail","record 2.3");
        $this->assertEquals($record["hotline.etat"],"wait","record 2.4");
        $this->assertEquals($record["hotline.wait_mep"],"non","record 2.5");
        $this->assertEquals($record["hotline.estimation"],"25:00:00","record 2.7");
        $this->assertEquals($record["temps_estime"],"25.00","record 2.8");
        //$this->assertEquals($record["temps_total"],"26.00","record 2.9");
        $this->assertEquals($record["hotline.priorite"],20,"record 2.10");
        //$this->assertEquals($record["dead_line"],ATF::$usr->date_trans(date("Y-m-d",strtotime("now")),false),"record 2.11");
        //$this->assertEquals($record["urgent"],"outdated","record 2.12");
        
        //Troisième enregistrement
        $record=$data["data"][2];
        $this->assertTrue(is_array($record),"record 3.0");
        $this->assertEquals($record["id_hotline"],$id_hotline_3,"record 3.1");
        $this->assertEquals($record["duree_presta"],"0.00","record 3.2");
        $this->assertEquals($record["hotline.urgence"],"detail","record 3.3");
        $this->assertEquals($record["hotline.etat"],"wait","record 3.4");
        $this->assertEquals($record["hotline.wait_mep"],"non","record 3.5");
        $this->assertEquals($record["hotline.estimation"],"25:00:00","record 3.7");
        $this->assertEquals($record["temps_estime"],"25.00","record 3.8");
        $this->assertEquals($record["temps_total"],"0.00","record 3.9");
        $this->assertEquals($record["hotline.priorite"],20,"record 3.10");
        //$this->assertEquals($record["dead_line"],ATF::$usr->date_trans(date("Y-m-d",strtotime("+3 day")),false),"record 3.11");
        $this->assertNull($record["urgent"],"record 3.12");

        //Quatième enregistrement
        $record=$data["data"][3];
        $this->assertTrue(is_array($record),"record 4.0");
        $this->assertEquals($record["id_hotline"],$id_hotline_4,"record 4.1");
        $this->assertEquals($record["duree_presta"],"0.00","record 4.2");
        $this->assertEquals($record["hotline.urgence"],"detail","record 4.3");
        $this->assertEquals($record["hotline.etat"],"wait","record 4.4");
        $this->assertEquals($record["hotline.wait_mep"],"non","record 4.5");
        $this->assertEquals($record["hotline.estimation"],"07:00:00","record 4.7");
        $this->assertEquals($record["temps_estime"],"7.00","record 4.8");
        $this->assertEquals($record["temps_total"],"0.00","record 4.9");
        $this->assertEquals($record["hotline.priorite"],20,"record 4.10");
        //$this->assertEquals($record["dead_line"],ATF::$usr->date_trans(date("Y-m-d",strtotime("+1 day")),false),"record 4.11");
        //$this->assertEquals($record["dead_line"],"-","record 4.11");
        $this->assertNull($record["urgent"],"record 4.12");
        
        //Cinquième enregistrement
        $record=$data["data"][4];
        $this->assertTrue(is_array($record),"record 5.0");
        $this->assertEquals($record["id_hotline"],$this->id_hotline,"record 5.1");
        $this->assertEquals($record["duree_presta"],"2.50","record 5.2");
        $this->assertEquals($record["hotline.urgence"],"detail","record 5.3");
        $this->assertEquals($record["hotline.etat"],"fixing","record 5.4");
        $this->assertEquals($record["hotline.wait_mep"],"non","record 5.5");
        $this->assertEquals($record["hotline.estimation"],"01:00:00","record 5.7");
        $this->assertEquals($record["temps_estime"],"1.00","record 5.8");
        //$this->assertEquals($record["temps_total"],"2.50","record 5.9");
        $this->assertEquals($record["hotline.priorite"],20,"record 5.10");
        //$this->assertEquals($record["dead_line"],ATF::$usr->date_trans(date("Y-m-d",strtotime("now")),false),"record 5.11");
       //$this->assertEquals($record["dead_line"],"-","record 5.11");
        
        
        //Flush des notices
        ATF::$msg->getNotices();
    }
    
    public function test_select_all_test2(){
        ATF::db()->truncate("hotline");
        $this->initUser(false);
        $this->initHotline();
        $this->initInteractions();
        
        //Hotline de test
        for($i=0;$i<2;$i++){
            $id_hotline=$this->obj->insert(
                array("charge"=>"intervention"
                     ,"hotline"=>"test"
                     ,"detail"=>"detail"
                     ,"urgence"=>"detail"
                     ,"pole_concerne"=>"dev"
                     ,"id_societe"=>$this->id_societe
                     ,"id_contact"=>$this->id_contact
                     ,"type_requete"=>"charge_client"));
            $this->assertNotNull($id_hotline,"La requête ne se crée pas... - assert ".$i);
        }
        
        //Forçage du nombre d'enregistrements par page (en cas de pb)
        $this->obj->q->setLimit(2);
         
        //$data=$this->obj->select_data(false,"desc",1);
        $this->obj->q->setPage(1);
        $this->obj->q->addOrder($this->obj->q->table.".id_".$this->obj->table,"desc");
        $data=$this->obj->select_data();
            
        //Premier enregistrement
        $record=$data["data"][0];
        $id=$id_hotline-2;
        $this->assertTrue(is_array($record),"record 1.0");
        $this->assertEquals($record["id_hotline"],$id,"record 1.1");
        //$this->assertEquals($record["temps"],"2.25","record 1.2");
        $this->assertEquals($record["hotline.urgence"],"detail","record 1.3");
        $this->assertEquals($record["hotline.etat"],"fixing","record 1.4");
        $this->assertEquals($record["hotline.wait_mep"],"non","record 1.5");        
        $this->assertEquals($record["hotline.estimation"],"01:00:00","record 1.7");
        $this->assertEquals($record["temps_estime"],"1.00","record 1.8");
        //$this->assertEquals($record["temps_total"],"2.50","record 1.9");
        $this->assertEquals($record["hotline.priorite"],20,"record 1.10");
        $this->assertEquals($record["dead_line"],"-","record 1.11");
        //$this->assertNull($record["urgent"],"record 1.12");
        
        //Flush des notices
        ATF::$msg->getNotices();
    }

    // @author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_select_all_test3(){
        ATF::db()->truncate("hotline");
        $this->initUser(false);

        //Nouvelle hotline estimation 25:00 - temps passe 26:00
        $id_hotline_3=$this->obj->insert(
            array("charge"=>"intervention"
                 ,"hotline"=>"test"
                 ,"urgence"=>"detail"
                 ,"detail"=>"detail"
                 ,"pole_concerne"=>"dev"
                 ,"id_societe"=>$this->id_societe
                 ,"id_contact"=>$this->id_contact
                 ,"type_requete"=>"charge_client"
                 ,"estimation"=>"25:00"));
        $this->assertNotNull($id_hotline_3,"La requête ne se crée pas... - assert 1");
        
        $hotline_interaction["id_hotline"] =$id_hotline_3;
        $hotline_interaction["date"] ="2009-12-02 12:00:00";
        $hotline_interaction['duree_presta']='30:00';
        $hotline_interaction['heure_debut_presta']='14:00';
        $hotline_interaction['heure_fin_presta']='14:30';
        $hotline_interaction['credit_presta']="0.50";        
        $hotline_interaction['no_test_credit']=true;
        $hotline_interaction["etat"] ="fixing";
        $hotline_interaction["detail"] ="detail";
        $hotline_interaction["visible"] ="oui";
        $hotline_interaction["hotline_interaction"] = $hotline_interaction;
        $this->id_hotline_interaction_ = ATF::hotline_interaction()->insert($hotline_interaction,$this->s);
        
        //Nouvelle hotline estimation 25:00 / temps passe 26:00
        $id_hotline_2=$this->obj->insert(
            array("charge"=>"intervention"
                 ,"hotline"=>"test"
                 ,"detail"=>"detail"
                 ,"urgence"=>"detail"
                 ,"pole_concerne"=>"dev"
                 ,"id_societe"=>$this->id_societe
                 ,"id_contact"=>$this->id_contact
                 ,"type_requete"=>"charge_client"
                 ,"estimation"=>"25:00"));
        $this->assertNotNull($id_hotline_2,"La requête ne se crée pas... - assert 2");
        
        $hotline_interaction["id_hotline"] =$id_hotline_2;
        $hotline_interaction["date"] ="2009-12-02 12:00:00";
        $hotline_interaction['duree_presta']='01:00';
        $hotline_interaction['heure_debut_presta']='14:00';
        $hotline_interaction['heure_fin_presta']='15:00';
        $hotline_interaction['credit_presta']=1;        
        $hotline_interaction["etat"] ="fixing";
        $hotline_interaction["detail"] ="detail";
        $hotline_interaction["visible"] ="oui";
        $hotline_interaction["hotline_interaction"] = $hotline_interaction;
        $this->id_hotline_interaction_ = ATF::hotline_interaction()->insert($hotline_interaction,$this->s);
        
        $this->obj->q->reset('limit')->setLimit(10);
        $data=$this->obj->select_data();    
            

        //Premier enregistrement
        $record=$data["data"][0];
        $this->assertTrue(is_array($record),"record 1.0");
        $this->assertEquals($record["id_hotline"],$id_hotline_2,"record 1.1");
        $this->assertEquals($record["duree_presta"],"01.00","record 1.2");
        $this->assertEquals($record["hotline.urgence"],"detail","record 1.3");
        $this->assertEquals($record["hotline.etat"],"wait","record 1.4");
        $this->assertEquals($record["hotline.wait_mep"],"non","record 1.5");
        $this->assertEquals($record["hotline.avancement"],0,"record 1.6");
        $this->assertEquals($record["hotline.estimation"],"25:00:00","record 1.7");
        $this->assertEquals($record["temps_estime"],"25.00","record 1.8");
        //$this->assertEquals($record["temps_total"],"01.00","record 1.9");
        $this->assertEquals($record["hotline.priorite"],20,"record 1.10");
        //$this->assertEquals($record["dead_line"],ATF::$usr->date_trans(date("Y-m-d",strtotime("now")),false),"record 1.11");
        $this->assertNull($record["urgent"],"record 2.12");
        
        //Deuxième enregistrement
        $record=$data["data"][1];
        $this->assertTrue(is_array($record),"record 2.0");
        $this->assertEquals($record["id_hotline"],$id_hotline_3,"record 2.1");
        $this->assertEquals($record["duree_presta"],"30.00","record 2.2");
        $this->assertEquals($record["hotline.urgence"],"detail","record 2.3");
        $this->assertEquals($record["hotline.etat"],"wait","record 2.4");
        $this->assertEquals($record["hotline.wait_mep"],"non","record 2.5");
        $this->assertEquals($record["hotline.avancement"],0,"record 2.6");
        $this->assertEquals($record["hotline.estimation"],"25:00:00","record 2.7");
        $this->assertEquals($record["temps_estime"],"25.00","record 2.8");
        //$this->assertEquals($record["temps_total"],"26.00","record 2.9");
        $this->assertEquals($record["hotline.priorite"],20,"record 2.10");
        //$this->assertEquals($record["dead_line"],ATF::$usr->date_trans(date("Y-m-d",strtotime("now")),false),"record 2.11");
        //$this->assertEquals($record["urgent"],"outdated","record 2.12");
        
        //Flush des notices
        ATF::$msg->getNotices();
    }
    
    // @author Jérémie Gwiazdowski <jgw@absystech.fr>
    // Cas deadline non estimé et non remplie 
    public function test_select_all_test4(){
        ATF::db()->truncate("hotline");
        $this->initUser(false);
        
        $id_hotline=$this->obj->insert(
            array("charge"=>"intervention"
                 ,"hotline"=>"test"
                 ,"detail"=>"detail"
                 ,"urgence"=>"detail"
                 ,"pole_concerne"=>"dev"
                 ,"id_societe"=>$this->id_societe
                 ,"id_contact"=>$this->id_contact
                 ,"type_requete"=>"charge_client"));
        $this->assertNotNull($id_hotline,"La requête ne se crée pas... - assert 1");
        
        $data=$this->obj->select_data();

        //Premier enregistrement
        $record=$data["data"][0];
        $this->assertTrue(is_array($record),"record 1.0");
        $this->assertEquals($record["id_hotline"],$id_hotline,"record 1.1");
        $this->assertEquals($record["temps"],"0.00","record 1.2");
        $this->assertEquals($record["hotline.urgence"],"detail","record 1.3");
        $this->assertEquals($record["hotline.etat"],"wait","record 1.4");
        $this->assertEquals($record["hotline.wait_mep"],"non","record 1.5");
        $this->assertEquals($record["hotline.avancement"],0,"record 1.6");
        $this->assertNull($record["hotline.estimation"],"record 1.7");
        $this->assertNull($record["temps_estime"],"record 1.8");
        $this->assertEquals($record["temps_total"],"0.00","record 1.9");
        $this->assertEquals($record["hotline.priorite"],20,"record 1.10");
        $this->assertEquals($record["dead_line"],"-","record 1.11");
        $this->assertNull($record["urgent"],"record 1.12");
        
        //Flush des notices
        ATF::$msg->getNotices();
    }  




    // @author Caroline MOREL <cmorel@absystech.fr>
    public function createDocument($name, $format){
        

        $this->initHotline();
        
        // Créer un fichier IMG/PDF/AUTRE
        $this->filepath = $this->obj->filepath($this->id_hotline,"fichier_joint");


        touch($this->filepath);
        $zip = new ZipArchive();
        $res = $zip->open($this->filepath);
        $this->assertTrue($res,"Pas de fichier zip 1 !");
        $this->assertFileExists($this->filepath,"Pas de fichier zip !");

        $path = dirname($this->filepath);

        $this->filename = $this->filepath.$format;
        touch($this->filename);
        $this->assertFileExists($this->filename,"Pas de fichier ".$format." !");
        $this->assertTrue($zip->addFile($this->filename),"Addfile dans le ZIP foireux");
        $zip->close();

        unlink($this->filename);
        return $this->obj->dynamicPicture($this->id_hotline);
    }

    public function createDocumentPDF($name, $format){
        $this->initHotline();
        

        // Créer un fichier PDF
        $this->filepath = $this->obj->filepath($this->id_hotline,"fichier_joint");
        touch($this->filepath);
        $this->assertFileExists($this->filepath,"Pas de fichier zip !");
        
        $dirname = dirname($this->filepath);

        $this->file2zip = dirname(__FILE__)."/HotlinePJ.pdf";

        //touch($this->file2zip);
        $this->assertFileExists($this->file2zip,"Pas de fichier ".$this->file2zip." !");

        $URL = __MANUAL_WEB_PATH__."hotline-".$this->id_hotline."-previewPDF0"."-200-50.png";
        $URLDL = __MANUAL_WEB_PATH__."hotline-select-"."dldoc0-".$this->id_hotline."-fichier_joint.dl";

        // Zipper le fichier
        $zip = new ZipArchive();
        $zip->open($this->filepath);
        $zip->addFile($this->file2zip);
        $zip->close();

        return $this->obj->dynamicPicture($this->id_hotline);
    }

    public function test_dynamicPictureFalse() {
        $this->assertFalse($this->obj->dynamicPicture(),"Aucun paramètre en entrée, doit retourner FALSE");
    }

    public function test_dynamicPicturejpg() {
        $hotline = $this->createDocument("image",".jpg");

        $random = strpos($hotline[0][URL],"?");
        $randomHD = strpos($hotline[0][URLHD],"?");
        $extension = substr(".jpg",1);

        // Vérifier la fonction
        $this->assertEquals($this->filename,$hotline[0][name],"Pas le même fichier !");
        $this->assertEquals("0 B",$hotline[0][size],"Pas la même taille de fichier !");
        $this->assertEquals($extension,$hotline[0][type],"Pas le même type de fichier !");
        $this->assertEquals(__MANUAL_WEB_PATH__."hotline-".$this->id_hotline."-image0"."-200-50.jpg",substr($hotline[0][URL], 0, $random),"Pas la même URL !");
        $this->assertEquals(__MANUAL_WEB_PATH__."hotline-".$this->id_hotline."-image0"."-800-600.jpg",substr($hotline[0][URLHD], 0, $randomHD),"Pas la même URL pour l'agrandissement !");

        // Supprimer les fichiers créés
        exec("rm ".dirname($this->filepath)."/".$this->id_hotline.".*");
    }

    public function test_dynamicPicturepng() {
        $hotline = $this->createDocument("image",".png");

        $random = strpos($hotline[0][URL],"?");
        $randomHD = strpos($hotline[0][URLHD],"?");
        $extension = substr(".png",1);

        // Vérifier la fonction
        $this->assertEquals($this->filename,$hotline[0][name],"Pas le même fichier !");
        $this->assertEquals("0 B",$hotline[0][size],"Pas la même taille de fichier !");
        $this->assertEquals($extension,$hotline[0][type],"Pas le même type de fichier !");
        $this->assertEquals(__MANUAL_WEB_PATH__."hotline-".$this->id_hotline."-image0"."-200-50.png",substr($hotline[0][URL], 0, $random),"Pas la même URL !");
        $this->assertEquals(__MANUAL_WEB_PATH__."hotline-".$this->id_hotline."-image0"."-800-600.png",substr($hotline[0][URLHD], 0, $randomHD),"Pas la même URL pour l'agrandissement !");

        // Supprimer les fichiers créés
        exec("rm ".dirname($this->filepath)."/".$this->id_hotline.".*");
    }

    public function test_dynamicPicturegif() {
        $hotline = $this->createDocument("image",".gif");

        $random = strpos($hotline[0][URL],"?");
        $randomHD = strpos($hotline[0][URLHD],"?");
        $extension = substr(".gif",1);

        // Vérifier la fonction
        $this->assertEquals($this->filename,$hotline[0][name],"Pas le même fichier !");
        $this->assertEquals("0 B",$hotline[0][size],"Pas la même taille de fichier !");
        $this->assertEquals($extension,$hotline[0][type],"Pas le même type de fichier !");
        $this->assertEquals(__MANUAL_WEB_PATH__."hotline-".$this->id_hotline."-image0"."-200-50.gif",substr($hotline[0][URL], 0, $random),"Pas la même URL !");
        $this->assertEquals(__MANUAL_WEB_PATH__."hotline-".$this->id_hotline."-image0"."-800-600.gif",substr($hotline[0][URLHD], 0, $randomHD),"Pas la même URL pour l'agrandissement !");

        // Supprimer les fichiers créés
        exec("rm ".dirname($this->filepath)."/".$this->id_hotline.".*");
    }
  
    public function test_dynamicPicturepdf() {
        $hotline = $this->createDocumentPDF("dldoc",".pdf");
        
        $extension = substr(".pdf",1);

        // Vérifier la fonction
        $this->assertEquals($this->file2zip,$hotline[0][name],"Pas le même fichier !");
        $this->assertEquals("111.74 KB",$hotline[0][size],"Pas la même taille de fichier !");
        $this->assertEquals($extension,$hotline[0][type],"Pas le même type de fichier !");
        $this->assertEquals(__MANUAL_WEB_PATH__."hotline-".$this->id_hotline."-previewPDF0"."-200-50.png",$hotline[0][URL],"Pas la même URL pour la prévisualisation !");
        $this->assertEquals(__MANUAL_WEB_PATH__."hotline-select-"."dldoc0-".$this->id_hotline."-pdf.dl",$hotline[0][URLDL],"Pas la même URL pour le téléchargement !");
        
        exec("rm ".dirname($this->filepath)."/".$this->id_hotline.".*");
    }

    public function test_dynamicPictureindd() {
        $hotline = $this->createDocument("dldoc",".indd");

        $extension = substr(".indd",1);

        // Vérifier la fonction
        $this->assertEquals($this->filename,$hotline[0][name],"Pas le même fichier !");
        $this->assertEquals("0 B",$hotline[0][size],"Pas la même taille de fichier !");
        $this->assertEquals($extension,$hotline[0][type],"Pas le même type de fichier !");
        $this->assertEquals(ATF::$staticserver.'images/icones/indesign.png',$hotline[0][URL],"Pas la même URL !");
        $this->assertEquals(__MANUAL_WEB_PATH__."hotline-select-"."dldoc0-".$this->id_hotline."-indd.dl",$hotline[0][URLDL],"Pas la même URL pour le téléchargement !");

        // Supprimer les fichiers créés
        exec("rm ".dirname($this->filepath)."/".$this->id_hotline.".*");
    }

    public function test_dynamicPicturepsd() {
        $hotline = $this->createDocument("dldoc",".psd");

        $extension = substr(".psd",1);

        // Vérifier la fonction
        $this->assertEquals($this->filename,$hotline[0][name],"Pas le même fichier !");
        $this->assertEquals("0 B",$hotline[0][size],"Pas la même taille de fichier !");
        $this->assertEquals($extension,$hotline[0][type],"Pas le même type de fichier !");
        $this->assertEquals(ATF::$staticserver.'images/icones/photoshop.png',$hotline[0][URL],"Pas la même URL !");
        $this->assertEquals(__MANUAL_WEB_PATH__."hotline-select-"."dldoc0-".$this->id_hotline."-psd.dl",$hotline[0][URLDL],"Pas la même URL pour le téléchargement !");

        // Supprimer les fichiers créés
        exec("rm ".dirname($this->filepath)."/".$this->id_hotline.".*");
    }

    public function test_dynamicPicturetiff() {
        $hotline = $this->createDocument("dldoc",".tiff");

        $extension = substr(".tiff",1);

        // Vérifier la fonction
        $this->assertEquals($this->filename,$hotline[0][name],"Pas le même fichier !");
        $this->assertEquals("0 B",$hotline[0][size],"Pas la même taille de fichier !");
        $this->assertEquals($extension,$hotline[0][type],"Pas le même type de fichier !");
        $this->assertEquals(ATF::$staticserver.'images/icones/photoshop.png',$hotline[0][URL],"Pas la même URL !");
        $this->assertEquals(__MANUAL_WEB_PATH__."hotline-select-"."dldoc0-".$this->id_hotline."-tiff.dl",$hotline[0][URLDL],"Pas la même URL pour le téléchargement !");

        // Supprimer les fichiers créés
        exec("rm ".dirname($this->filepath)."/".$this->id_hotline.".*");
    }

    public function test_dynamicPictureai() {
        $hotline = $this->createDocument("dldoc",".ai");

        $extension = substr(".ai",1);

        // Vérifier la fonction
        $this->assertEquals($this->filename,$hotline[0][name],"Pas le même fichier !");
        $this->assertEquals("0 B",$hotline[0][size],"Pas la même taille de fichier !");
        $this->assertEquals($extension,$hotline[0][type],"Pas le même type de fichier !");
        $this->assertEquals(ATF::$staticserver.'images/icones/illustrator.png',$hotline[0][URL],"Pas la même URL !");
        $this->assertEquals(__MANUAL_WEB_PATH__."hotline-select-"."dldoc0-".$this->id_hotline."-ai.dl",$hotline[0][URLDL],"Pas la même URL pour le téléchargement !");

        // Supprimer les fichiers créés
        exec("rm ".dirname($this->filepath)."/".$this->id_hotline.".*");
    }

    public function test_dynamicPicturedoc() {
        $hotline = $this->createDocument("dldoc",".doc");

        $extension = substr(".doc",1);

        // Vérifier la fonction
        $this->assertEquals($this->filename,$hotline[0][name],"Pas le même fichier !");
        $this->assertEquals("0 B",$hotline[0][size],"Pas la même taille de fichier !");
        $this->assertEquals($extension,$hotline[0][type],"Pas le même type de fichier !");
        $this->assertEquals(ATF::$staticserver.'images/icones/word.png',$hotline[0][URL],"Pas la même URL !");
        $this->assertEquals(__MANUAL_WEB_PATH__."hotline-select-"."dldoc0-".$this->id_hotline."-doc.dl",$hotline[0][URLDL],"Pas la même URL pour le téléchargement !");

        // Supprimer les fichiers créés
        exec("rm ".dirname($this->filepath)."/".$this->id_hotline.".*");
    }

    public function test_dynamicPicturedocx() {
        $hotline = $this->createDocument("dldoc",".docx");

        $extension = substr(".docx",1);

        // Vérifier la fonction
        $this->assertEquals($this->filename,$hotline[0][name],"Pas le même fichier !");
        $this->assertEquals("0 B",$hotline[0][size],"Pas la même taille de fichier !");
        $this->assertEquals($extension,$hotline[0][type],"Pas le même type de fichier !");
        $this->assertEquals(ATF::$staticserver.'images/icones/word.png',$hotline[0][URL],"Pas la même URL !");
        $this->assertEquals(__MANUAL_WEB_PATH__."hotline-select-"."dldoc0-".$this->id_hotline."-docx.dl",$hotline[0][URLDL],"Pas la même URL pour le téléchargement !");

        // Supprimer les fichiers créés
        exec("rm ".dirname($this->filepath)."/".$this->id_hotline.".*");
    }

    public function test_dynamicPictureodt() {
        $hotline = $this->createDocument("dldoc",".odt");

        $extension = substr(".odt",1);

        // Vérifier la fonction
        $this->assertEquals($this->filename,$hotline[0][name],"Pas le même fichier !");
        $this->assertEquals("0 B",$hotline[0][size],"Pas la même taille de fichier !");
        $this->assertEquals($extension,$hotline[0][type],"Pas le même type de fichier !");
        $this->assertEquals(ATF::$staticserver.'images/icones/word.png',$hotline[0][URL],"Pas la même URL !");
        $this->assertEquals(__MANUAL_WEB_PATH__."hotline-select-"."dldoc0-".$this->id_hotline."-odt.dl",$hotline[0][URLDL],"Pas la même URL pour le téléchargement !");

        // Supprimer les fichiers créés
        exec("rm ".dirname($this->filepath)."/".$this->id_hotline.".*");
    }

    public function test_dynamicPicturexls() {
        $hotline = $this->createDocument("dldoc",".xls");

        $extension = substr(".xls",1);

        // Vérifier la fonction
        $this->assertEquals($this->filename,$hotline[0][name],"Pas le même fichier !");
        $this->assertEquals("0 B",$hotline[0][size],"Pas la même taille de fichier !");
        $this->assertEquals($extension,$hotline[0][type],"Pas le même type de fichier !");
        $this->assertEquals(ATF::$staticserver.'images/icones/xls2.png',$hotline[0][URL],"Pas la même URL !");
        $this->assertEquals(__MANUAL_WEB_PATH__."hotline-select-"."dldoc0-".$this->id_hotline."-xls.dl",$hotline[0][URLDL],"Pas la même URL pour le téléchargement !");

        // Supprimer les fichiers créés
        exec("rm ".dirname($this->filepath)."/".$this->id_hotline.".*");
    }

    public function test_dynamicPictureods() {
        $hotline = $this->createDocument("dldoc",".ods");

        $extension = substr(".ods",1);

        // Vérifier la fonction
        $this->assertEquals($this->filename,$hotline[0][name],"Pas le même fichier !");
        $this->assertEquals("0 B",$hotline[0][size],"Pas la même taille de fichier !");
        $this->assertEquals($extension,$hotline[0][type],"Pas le même type de fichier !");
        $this->assertEquals(ATF::$staticserver.'images/icones/xls2.png',$hotline[0][URL],"Pas la même URL !");
        $this->assertEquals(__MANUAL_WEB_PATH__."hotline-select-"."dldoc0-".$this->id_hotline."-ods.dl",$hotline[0][URLDL],"Pas la même URL pour le téléchargement !");

        // Supprimer les fichiers créés
        exec("rm ".dirname($this->filepath)."/".$this->id_hotline.".*");
    }

    public function test_dynamicPictureppt() {
        $hotline = $this->createDocument("dldoc",".ppt");

        $extension = substr(".ppt",1);

        // Vérifier la fonction
        $this->assertEquals($this->filename,$hotline[0][name],"Pas le même fichier !");
        $this->assertEquals("0 B",$hotline[0][size],"Pas la même taille de fichier !");
        $this->assertEquals($extension,$hotline[0][type],"Pas le même type de fichier !");
        $this->assertEquals(ATF::$staticserver.'images/icones/ppt.png',$hotline[0][URL],"Pas la même URL !");
        $this->assertEquals(__MANUAL_WEB_PATH__."hotline-select-"."dldoc0-".$this->id_hotline."-ppt.dl",$hotline[0][URLDL],"Pas la même URL pour le téléchargement !");

        // Supprimer les fichiers créés
        exec("rm ".dirname($this->filepath)."/".$this->id_hotline.".*");
    }

    public function test_dynamicPicturepptx() {
        $hotline = $this->createDocument("dldoc",".pptx");

        $extension = substr(".pptx",1);

        // Vérifier la fonction
        $this->assertEquals($this->filename,$hotline[0][name],"Pas le même fichier !");
        $this->assertEquals("0 B",$hotline[0][size],"Pas la même taille de fichier !");
        $this->assertEquals($extension,$hotline[0][type],"Pas le même type de fichier !");
        $this->assertEquals(ATF::$staticserver.'images/icones/ppt.png',$hotline[0][URL],"Pas la même URL !");
        $this->assertEquals(__MANUAL_WEB_PATH__."hotline-select-"."dldoc0-".$this->id_hotline."-pptx.dl",$hotline[0][URLDL],"Pas la même URL pour le téléchargement !");

        // Supprimer les fichiers créés
        exec("rm ".dirname($this->filepath)."/".$this->id_hotline.".*");
    }

    public function test_dynamicPicturetxt() {
        $hotline = $this->createDocument("dldoc",".txt");

        $extension = substr(".txt",1);

        // Vérifier la fonction
        $this->assertEquals($this->filename,$hotline[0][name],"Pas le même fichier !");
        $this->assertEquals("0 B",$hotline[0][size],"Pas la même taille de fichier !");
        $this->assertEquals($extension,$hotline[0][type],"Pas le même type de fichier !");
        $this->assertEquals(ATF::$staticserver.'images/icones/txt.png',$hotline[0][URL],"Pas la même URL !");
        $this->assertEquals(__MANUAL_WEB_PATH__."hotline-select-"."dldoc0-".$this->id_hotline."-txt.dl",$hotline[0][URLDL],"Pas la même URL pour le téléchargement !");

        // Supprimer les fichiers créés
        exec("rm ".dirname($this->filepath)."/".$this->id_hotline.".*");
    }


    // @author Nicolas BERTEMONT <nbertemont@absystech.fr> 
    public function test_nom() {
        $this->initUser(false);
        $this->initHotline();
        
        //return nom
        $this->assertEquals("HotlineTuTest",$this->obj->nom($this->id_hotline),"Le nom récupéré n'est pas correcte");
        
        //avec memory_optimisation_select
        ATF::setSingleton("hotline", new hotlineTU());
        $nom=ATF::hotline()->nom($this->id_hotline);
        //une deuxieme fois pour tester le cache
        $nom2=ATF::hotline()->nom($this->id_hotline);
        ATF::unsetSingleton("hotline");
        
        $this->assertEquals("HotlineTuTest",$nom,"1/ Le nom récupéré en utilisant 'memory_optimisation_select'  n'est pas correcte");
        $this->assertEquals("HotlineTuTest",$nom2,"2/ Le nom récupéré en utilisant le cache n'est pas correcte");
        
        //notice dû a l'utilisation de l'insert, donc on flush sans avoir besoin de regarder le contenu (car normalement deja tester dans insert)
        ATF::$msg->getNotices();
    }
    


    // @author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_getTotalTime(){
        $this->initUser(false);
        $this->initHotline();
        $this->initInteractions();
        $time=$this->obj->getTotalTime($this->id_hotline, "presta");
        $this->assertEquals($time,"02H30","Le temps renvoyé est pas .... bon !");

        $time=$this->obj->getTotalTime($this->id_hotline, "dep");
        $this->assertEquals($time,"00H00","Le temps deplacement renvoyé est pas .... bon !");
        
    }
 
    
    // @author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_getBillingTime(){
        $this->initUser(false);
        $this->initHotline();
        $this->initInteractions();
        $time=$this->obj->getBillingTime($this->id_hotline);
        $this->assertEquals($time,0.00,"Le temps renvoyé est pas .... bon !");
    }



// @author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_getEstimatedTime(){
        $this->initUser(false);
        $this->initHotline();
        $this->initInteractions();
        $time=$this->obj->getEstimatedTime($this->id_hotline);
        $this->assertEquals($time,1,"Le temps renvoyé est pas .... bon !");
    }

    
    // @author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_takeRequest() {
        $this->initUser(false);
        $this->initHotline();
        ATF::$msg->getNotices();//Flush des notices 
        
        $this->initAltUser();       
        $this->obj->takeRequest(
            array(
                "id_hotline"=>$this->id_hotline
                ,"send_mail"=>true
                ,"id_user"=>$this->alt_id_user));
        $hotline=$this->obj->select($this->id_hotline);
        $this->assertEquals($hotline["etat"],"fixing","Le passage en prise en charge ne marche pas !! - assert 1");
        
        
        //Test notices
        $notices=ATF::$msg->getNotices();
        $this->assertTrue(is_array($notices),"assert 4");
        $this->assertEquals(3,count($notices),"assert 5");
    }
  
    // @author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_getFormBillingMode() {
        $this->initUser(false);
        $this->initHotline("alt_id_user");
        //Flush notices
        ATF::$msg->getNotices();

        //Charge absystech
        $infos=array("id_hotline"=>$this->id_hotline);
        $this->obj->getFormBillingMode($infos);
        
        $cr=ATF::$cr->getCrefresh();
        $this->assertTrue(is_array($cr["main"]),"assert 1");
        $this->assertTrue(is_array($cr["mainScript"]),"assert 2");
        
        //Charge client
        $this->obj->setBillingMode(array("id_hotline"=>$this->id_hotline
                                        ,"type_requete"=>"charge_client"
                                        ,"refresh"=>true
                                        ,"send_mail"=>true
                                        ,"charge"=>"intervention"));
        $infos=array("id_hotline"=>$this->id_hotline);
        ATF::$msg->getNotices();//Flush notices
        $this->obj->getFormBillingMode($infos);
        
        $cr=ATF::$cr->getCrefresh();
        $this->assertTrue(is_array($cr["main"]),"assert 3");
        $this->assertTrue(is_array($cr["mainScript"]),"assert 4");
        
        //Sur une affaire
        $this->initAffaire();
        $this->obj->setBillingMode(array("id_hotline"=>$this->id_hotline
                                        ,"type_requete"=>"affaire"
                                        ,"refresh"=>true
                                        ,"send_mail"=>true
                                        ,"id_affaire"=>$this->id_affaire
                                        ,"charge"=>"intervention"));
        $infos=array("id_hotline"=>$this->id_hotline);
        ATF::$msg->getNotices();//Flush notices
        $this->obj->getFormBillingMode($infos);
        
        $cr=ATF::$cr->getCrefresh();
        $this->assertTrue(is_array($cr["main"]),"assert 5");
        $this->assertTrue(is_array($cr["mainScript"]),"assert 6");
    }


    // @author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_boostBilling() {
        $this->initUser(false);
        //Requête intervention
        $id_hotline=$this->obj->insert(
            array("charge"=>"intervention"
                 ,"hotline"=>"test"
                 ,"detail"=>"detail"
                 ,"pole_concerne"=>"dev"
                 ,"id_societe"=>$this->id_societe
                 ,"id_contact"=>$this->id_contact
                 ,"type_requete"=>"charge_client"));
        $this->assertNotNull($id_hotline,"La requête ne se crée pas... - assert 1");
        $hotline=$this->obj->select($id_hotline);
        ATF::$msg->getNotices();//Flush des notices 
        
        //Tests Hotline
        $this->assertEquals($hotline["facturation_ticket"],"oui","assert 2");
        $this->assertNull($hotline["ok_facturation"],"assert 3");
        $this->assertEquals($hotline["charge"],"intervention","assert 4");
        $this->assertEquals($hotline["hotline"],"test","assert 5");
        //print_r($hotline["detail"]);die();
        $this->assertEquals($hotline["detail"],"Requête mise en ligne par class: unitaire\n\ndetail","assert 6");
        $this->assertEquals($hotline["id_societe"],$this->id_societe,"assert 7");
        $this->assertEquals($hotline["id_contact"],$this->id_contact,"assert 8");
        $this->assertEquals($hotline["pole_concerne"],"dev","assert 9");
        $this->assertNull($hotline["indice_satisfaction"],"assert 10");
        $this->assertEquals($hotline["etat"],"wait","assert 11");
        
        //Boost Billing
        $this->obj->boostBilling($this->obj->select($hotline["id_hotline"]));
               
        //Test notices
        $notices=ATF::$msg->getNotices();
        $this->assertTrue(is_array($notices),"assert 13");
        $this->assertEquals(2,count($notices),"assert 14");
    }
    
    // @author Jérémie Gwiazdowski <jgw@absystech.fr>, Nicolas BERTEMONT <nbertemont@absystech.fr>
    public function test_setBillingModeChargeAT() {
        $this->initUser(false);
        $this->initAltUser();
        $this->initHotline("alt_id_user");
        $this->initInteractions();
        ATF::$msg->getNotices();//Flush des notices 
        
        //Charge AbsysTech
        $this->obj->setBillingMode(array("id_hotline"=>$this->id_hotline
                                        ,"type_requete"=>"charge_absystech"
                                        ,"refresh"=>true
                                        ,"send_mail"=>true
                                        ,"charge"=>"intervention"));
        $hotline=$this->obj->select($this->id_hotline);
        
        //Tests Hotline
        $this->assertEquals($hotline["facturation_ticket"],"non","assert 2");
        $this->assertNull($hotline["ok_facturation"],"assert 3");
        $this->assertEquals($hotline["charge"],"intervention","assert 4");
        $this->assertEquals($hotline["hotline"],"HotlineTuTest","assert 5");
        //print_r($hotline["detail"]);die();
        $this->assertEquals($hotline["detail"],"Requête mise en ligne par class: unitaire\n\nHotlineTuTest","assert 6");
        $this->assertEquals($hotline["id_societe"],$this->id_societe,"assert 7");
        $this->assertEquals($hotline["id_contact"],$this->id_contact,"assert 8");
        $this->assertEquals($hotline["pole_concerne"],"dev","assert 9");
        $this->assertNull($hotline["indice_satisfaction"],"assert 10");
        $this->assertEquals($hotline["etat"],"fixing","assert 11");
        $cr=ATF::$cr->getCrefresh();
        $this->assertEquals($cr["main"]["template"],"generic-select.tpl.htm","assert 12");
        $this->assertNull($hotline["id_affaire"],"assert 13");
        
        //Test notices
        $notices=ATF::$msg->getNotices();
        $this->assertTrue(is_array($notices),"assert 14");
        $this->assertEquals(2,count($notices),"assert 15");
    }
    
    // @author Jérémie Gwiazdowski <jgw@absystech.fr>, Nicolas BERTEMONT <nbertemont@absystech.fr>
    public function test_setBillingModeChargeClient() {
        $this->initUser(false);
        $this->initHotline("alt_id_user");
        $this->initInteractions();
        ATF::$msg->getNotices();//Flush des notices 
        
        //Charge AbsysTech
        $this->obj->setBillingMode(array("id_hotline"=>$this->id_hotline
                                        ,"type_requete"=>"charge_client"
                                        ,"refresh"=>true
                                        ,"send_mail"=>true
                                        ,"charge"=>"intervention"));
        $hotline=$this->obj->select($this->id_hotline);
        
        //Tests Hotline
        $this->assertEquals($hotline["facturation_ticket"],"oui","assert 2");
        $this->assertNull($hotline["ok_facturation"],"assert 3");
        $this->assertEquals($hotline["charge"],"intervention","assert 4");
        $this->assertEquals($hotline["hotline"],"HotlineTuTest","assert 5");
        //print_r($hotline["detail"]);die();
        $this->assertEquals($hotline["detail"],"Requête mise en ligne par class: unitaire\n\nHotlineTuTest","assert 6");
        $this->assertEquals($hotline["id_societe"],$this->id_societe,"assert 7");
        $this->assertEquals($hotline["id_contact"],$this->id_contact,"assert 8");
        $this->assertEquals($hotline["pole_concerne"],"dev","assert 9");
        $this->assertNull($hotline["indice_satisfaction"],"assert 10");
        $this->assertEquals($hotline["etat"],"wait","assert 11");
        $cr=ATF::$cr->getCrefresh();
        $this->assertEquals($cr["main"]["template"],"generic-select.tpl.htm","assert 12");
        $this->assertNull($hotline["id_affaire"],"assert 13");
            
        //Test notices
        $notices=ATF::$msg->getNotices();
        $this->assertTrue(is_array($notices),"assert 14");
        $this->assertEquals(2,count($notices),"assert 15");
    }
    
    // @author Jérémie Gwiazdowski <jgw@absystech.fr>, Nicolas BERTEMONT <nbertemont@absystech.fr>
    public function test_setBillingModeRelanceChargeClient() {
        $this->initUser(false);
        $this->initHotline("alt_id_user");
        $this->initInteractions();
        ATF::$msg->getNotices();//Flush des notices 
        
        //Charge AbsysTech
        $this->obj->setBillingMode(array("id_hotline"=>$this->id_hotline
                                        ,"type_requete"=>"charge_client"
                                        ,"refresh"=>true
                                        ,"send_mail"=>true
                                        ,"relance"=>true
                                        ,"charge"=>"intervention"));
        $hotline=$this->obj->select($this->id_hotline);
        
        //Tests Hotline
        $this->assertEquals($hotline["facturation_ticket"],"oui","assert 2");
        $this->assertNull($hotline["ok_facturation"],"assert 3");
        $this->assertEquals($hotline["charge"],"intervention","assert 4");
        $this->assertEquals($hotline["hotline"],"HotlineTuTest","assert 5");
        //print_r($hotline["detail"]);die();
        $this->assertEquals($hotline["detail"],"Requête mise en ligne par class: unitaire\n\nHotlineTuTest","assert 6");
        $this->assertEquals($hotline["id_societe"],$this->id_societe,"assert 7");
        $this->assertEquals($hotline["id_contact"],$this->id_contact,"assert 8");
        $this->assertEquals($hotline["pole_concerne"],"dev","assert 9");
        $this->assertNull($hotline["indice_satisfaction"],"assert 10");
        $this->assertEquals($hotline["etat"],"wait","assert 11");
        $cr=ATF::$cr->getCrefresh();
        $this->assertEquals($cr["main"]["template"],"generic-select.tpl.htm","assert 12");
        $this->assertNull($hotline["id_affaire"],"assert 13");

        //Test notices
        $notices=ATF::$msg->getNotices();
        $this->assertTrue(is_array($notices),"assert 14");
        $this->assertEquals(2,count($notices),"assert 15");
    }
    
    // @author Jérémie Gwiazdowski <jgw@absystech.fr>, Nicolas BERTEMONT <nbertemont@absystech.fr>
    public function test_setBillingModeAffaire() {
        $this->initUser(false);
        $this->initAltUser();
        $this->initHotline("alt_id_user");
        $this->initInteractions();
        $this->initAffaire();
        ATF::$msg->getNotices();//Flush des notices 
        
        //Charge AbsysTech
        $this->obj->setBillingMode(array("id_hotline"=>$this->id_hotline
                                        ,"type_requete"=>"affaire"
                                        ,"refresh"=>true
                                        ,"send_mail"=>true
                                        ,"id_affaire"=>$this->id_affaire
                                        ,"charge"=>"intervention"));
        $hotline=$this->obj->select($this->id_hotline);
        
        //Tests Hotline
        $this->assertEquals($hotline["facturation_ticket"],"non","assert 2");
        $this->assertNull($hotline["ok_facturation"],"assert 3");
        $this->assertEquals($hotline["charge"],"intervention","assert 4");
        $this->assertEquals($hotline["hotline"],"HotlineTuTest","assert 5");
        //print_r($hotline["detail"]);die();
        $this->assertEquals($hotline["detail"],"Requête mise en ligne par class: unitaire\n\nHotlineTuTest","assert 6");
        $this->assertEquals($hotline["id_societe"],$this->id_societe,"assert 7");
        $this->assertEquals($hotline["id_contact"],$this->id_contact,"assert 8");
        $this->assertEquals($hotline["pole_concerne"],"dev","assert 9");
        $this->assertNull($hotline["indice_satisfaction"],"assert 10");
        $this->assertEquals($hotline["etat"],"fixing","assert 11");
        $cr=ATF::$cr->getCrefresh();
        $this->assertEquals($cr["main"]["template"],"generic-select.tpl.htm","assert 12");
        $this->assertEquals($hotline["id_affaire"],$this->id_affaire,"assert 13");
                       
        //Test notices
        $notices=ATF::$msg->getNotices();
        $this->assertTrue(is_array($notices),"assert 14");
        $this->assertEquals(2,count($notices),"assert 15");
    }
    
    // @author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_resolveRequest() {
        $this->initUser(false);
        $this->initAltUser();
        $this->initHotline("alt_id_user");
        $this->initInteractions();
        ATF::$msg->getNotices();//Flush des notices 
                
        
        $this->obj->resolveRequest(
            array("id_hotline"=>$this->id_hotline
                 ,"send_mail"=>true));
        $hotline=$this->obj->select($this->id_hotline);
        $this->assertEquals($hotline["etat"],"done","La requête ne se passe pas en etat résolu !! - assert 1");
 
        //Test notices
        $notices=ATF::$msg->getNotices();
        $this->assertTrue(is_array($notices),"assert 3");
        $this->assertEquals(2,count($notices),"assert 4");
    }   
    
    // @author Jérémie Gwiazdowski <jgw@absystech.fr>, Nicolas BERTEMONT <nbertemont@absystech.fr>
    public function test_resolveRequestWOdm() {
        $this->initUser(false);
        $this->initHotline();
        $this->initInteractions();
        ATF::$msg->getNotices();//Flush des notices
        //Création d'un odm
        $infos=array("id_hotline"=>$this->id_hotline
                    ,"id_user"=>$this->id_user
                    ,"id_societe"=>$this->id_societe
                    ,"date"=>date("Y-m-d h:i:s")
                    ,"ordre_de_mission"=>"TU odm"
                    ,"adresse"=>"TU adr"
                    ,"cp"=>"12345"
                    ,"ville"=>"tu");
        
        //Astuce de merde pour générer le pdf !
        $infos['filestoattach']=array("fichier_joint");
        
        $id_odm=ATF::ordre_de_mission()->insert($infos);
        $error=false;
        try{
            $this->obj->resolveRequest(array("id_hotline"=>$this->id_hotline));
        }catch(error $e){
            $error=true;
        }
        $this->assertTrue($error,"assert 1");
                
        //Test notices
        $notices=ATF::$msg->getNotices();
        $this->assertTrue(is_array($notices),"assert 3");
        $this->assertEquals(0,count($notices),"assert 4");
    }   


    // @author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_insert_free() {
        $this->initUser(false);
        
        ATF::societe()->u(array("id_societe" => $this->id_societe , "etat" => "inactif"));
        try{
            $id_hotline=$this->obj->insert(
                array("charge"=>"intervention"
                     ,"hotline"=>"test"
                     ,"detail"=>"detail"
                     ,"pole_concerne"=>"dev"
                     ,"id_societe"=>$this->id_societe
                     ,'priorite'=>1
                     ,"id_contact"=>$this->id_contact
                     ,"send_mail"=>true
                     ,"filestoattach"=>array("fichier_joint"=>true)
                     ,"visible"=>"oui"));           
        }catch(error $e){
            $erreur = $e->getMessage();
        }
        
        $this->assertEquals("Impossible d'ajouter une requête car la société est inactive" , $erreur , "Possible d'inserer une requete sur societe incative?");  
        
        ATF::societe()->u(array("id_societe" => $this->id_societe , "etat" => "actif"));
        //Requête intervention
        $id_hotline=$this->obj->insert(
            array("charge"=>"intervention"
                 ,"hotline"=>"test"
                 ,"detail"=>"detail"
                 ,"pole_concerne"=>"dev"
                 ,"id_societe"=>$this->id_societe
                 ,'priorite'=>10
                 ,"id_contact"=>$this->id_contact
                 ,"send_mail"=>true
                 ,"filestoattach"=>array("fichier_joint"=>true)
                 ,"visible"=>"oui"));
        $this->assertNotNull($id_hotline,"La requête ne se crée pas... - assert 1");
        $hotline=$this->obj->select($id_hotline);
        $this->assertEquals("genant",$hotline['urgence'],"La requête n'a pas la bonne urgence");
        

        //Tests Hotline
        $this->assertNull($hotline["facturation_ticket"],"assert 4");
        $this->assertNull($hotline["ok_facturation"],"assert 5");
        $this->assertEquals($hotline["charge"],"intervention","assert 6");
        $this->assertEquals($hotline["hotline"],"test","assert 7");
        //print_r($hotline["detail"]);die();
        $this->assertEquals($hotline["detail"],"Requête mise en ligne par class: unitaire\n\ndetail","assert 8");
        $this->assertEquals($hotline["id_societe"],$this->id_societe,"assert 9");
        $this->assertEquals($hotline["id_contact"],$this->id_contact,"assert 10");
        $this->assertEquals($hotline["pole_concerne"],"dev","assert 11");
        $this->assertNull($hotline["indice_satisfaction"],"assert 12");
        $this->assertEquals($hotline["etat"],"free","assert 13");
        
        //Test notices
        $notices=ATF::$msg->getNotices();
        $this->assertTrue(is_array($notices),"assert 14");
        $this->assertEquals(2,count($notices),"nombre de notices incorrect");
    }
    
    // @author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_insert_intervention_charge_client() {
        $this->initUser(false);
        //Requête intervention
        $id_hotline=$this->obj->insert(
            array("charge"=>"intervention"
                 ,"hotline"=>"test"
                 ,"detail"=>"detail"
                 ,"pole_concerne"=>"dev"
                 ,"id_societe"=>$this->id_societe
                 ,'priorite'=>16
                 ,"id_contact"=>$this->id_contact
                 ,"type_requete"=>"charge_client"
                 ,"send_mail"=>true
                 ,"visible"=>"oui"));
        $this->assertNotNull($id_hotline,"La requête ne se crée pas... - assert 1");
        $hotline=$this->obj->select($id_hotline);
        $this->assertEquals("bloquant",$hotline['urgence'],"La requête n'a pas la bonne urgence");
   
                        
        //Tests Hotline
        $this->assertEquals($hotline["facturation_ticket"],"oui","assert 4");
        $this->assertNull($hotline["ok_facturation"],"assert 5");
        $this->assertEquals($hotline["charge"],"intervention","assert 6");
        $this->assertEquals($hotline["hotline"],"test","assert 7");
        //print_r($hotline["detail"]);die();
        $this->assertEquals($hotline["detail"],"Requête mise en ligne par class: unitaire\n\ndetail","assert 8");
        $this->assertEquals($hotline["id_societe"],$this->id_societe,"assert 9");
        $this->assertEquals($hotline["id_contact"],$this->id_contact,"assert 10");
        $this->assertEquals($hotline["pole_concerne"],"dev","assert 11");
        $this->assertNull($hotline["indice_satisfaction"],"assert 12");
        $this->assertEquals($hotline["etat"],"wait","assert 13");
        
        //Test notices
        $notices=ATF::$msg->getNotices();
        $this->assertTrue(is_array($notices),"assert 14");
        $this->assertEquals(4,count($notices),"nombre de notices incorrect");
    }
    
    // @author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_insert_intervention_charge_client_w_user() {
        $this->initUser(false);
        $this->initAltUser();
        //Requête intervention
        $id_hotline=$this->obj->insert(
            array("charge"=>"intervention"
                 ,"hotline"=>"test"
                 ,"detail"=>"detail"
                 ,"pole_concerne"=>"dev"                 
                 ,"id_societe"=>$this->id_societe
                 ,"id_contact"=>$this->id_contact
                 ,"type_requete"=>"charge_client"
                 ,"id_user"=>$this->alt_id_user
                 ,"send_mail"=>true
                 ,"visible"=>"oui"));
        $this->assertNotNull($id_hotline,"La requête ne se crée pas...");
        $hotline=$this->obj->select($id_hotline);
    
        
        //Tests hotline
        $this->assertEquals($hotline["facturation_ticket"],"oui","assert 1");
        $this->assertNull($hotline["ok_facturation"],"assert 2");
        $this->assertEquals($hotline["charge"],"intervention","assert 3");
        $this->assertEquals($hotline["hotline"],"test","assert 4");
        //print_r($hotline["detail"]);die();
        $this->assertEquals($hotline["detail"],"Requête mise en ligne par class: unitaire\n\ndetail");
        $this->assertEquals($hotline["id_societe"],$this->id_societe,"assert 5");
        $this->assertEquals($hotline["id_contact"],$this->id_contact,"assert 6");
        $this->assertNull($hotline["indice_satisfaction"],"assert 7");
        $this->assertEquals($hotline["etat"],"wait","assert 8");
        $this->assertEquals($hotline["pole_concerne"],"dev","assert 9");
        $this->assertEquals($hotline["id_user"],$this->alt_id_user,"assert 10");
        
        //Test notices
        $notices=ATF::$msg->getNotices();
        $this->assertTrue(is_array($notices),"assert 11");
        $this->assertEquals(4,count($notices),"assert 12");
    }

    // @author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_insert_intervention_charge_at() {
        $this->initUser(false);
        //Requête intervention
        $id_hotline=$this->obj->insert(
            array("charge"=>"intervention"
                 ,"hotline"=>"test"
                 ,"detail"=>"detail"
                 ,"pole_concerne"=>"telecom"
                 ,"id_societe"=>$this->id_societe
                 ,"id_contact"=>$this->id_contact
                 ,"type_requete"=>"charge_absystech"
                 ,"send_mail"=>true
                 ,"visible"=>"oui"));
        $this->assertNotNull($id_hotline,"La requête ne se crée pas...");
        $hotline=$this->obj->select($id_hotline);
 
        
        //Tests Hotline
        $this->assertEquals($hotline["facturation_ticket"],"non","assert 1");
        $this->assertNull($hotline["ok_facturation"],"assert 2");
        $this->assertEquals($hotline["charge"],"intervention","assert 3");
        $this->assertEquals($hotline["hotline"],"test","assert 4");
        //print_r($hotline["detail"]);die();
        $this->assertEquals($hotline["detail"],"Requête mise en ligne par class: unitaire\n\ndetail","assert 5");
        $this->assertEquals($hotline["id_societe"],$this->id_societe,"assert 6");
        $this->assertEquals($hotline["id_contact"],$this->id_contact,"assert 7");
        $this->assertNull($hotline["indice_satisfaction"],"assert 8");
        $this->assertEquals($hotline["pole_concerne"],"telecom","assert 9");
        $this->assertEquals($hotline["etat"],"free","assert 10");
        
        //Test notices
        $notices=ATF::$msg->getNotices();
        $this->assertTrue(is_array($notices),"assert 11");
        $this->assertEquals(4,count($notices),"assert 12");
    }

    // @author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_insert_intervention_charge_at_w_user() {
        $this->initUser(false);
        $this->initAltUser();
        //Requête intervention
        $id_hotline=$this->obj->insert(
            array("charge"=>"intervention"
                 ,"hotline"=>"test"
                 ,"detail"=>"detail"
                 ,"pole_concerne"=>"system"
                 ,"id_societe"=>$this->id_societe
                 ,"id_contact"=>$this->id_contact
                 ,"type_requete"=>"charge_absystech"
                 ,"id_user"=>$this->alt_id_user
                 ,"send_mail"=>true
                 ,"visible"=>"oui"));
        $this->assertNotNull($id_hotline,"La requête ne se crée pas...");
        $hotline=$this->obj->select($id_hotline);
            
        //Tests hotline
        $this->assertEquals($hotline["facturation_ticket"],"non","assert 1");
        $this->assertNull($hotline["ok_facturation"],"assert 2");
        $this->assertEquals($hotline["charge"],"intervention","assert 3");
        $this->assertEquals($hotline["hotline"],"test","assert 4");
        //print_r($hotline["detail"]);die();
        $this->assertEquals($hotline["detail"],"Requête mise en ligne par class: unitaire\n\ndetail","assert 5");
        $this->assertEquals($hotline["id_societe"],$this->id_societe,"assert 6");
        $this->assertEquals($hotline["id_contact"],$this->id_contact,"assert 7");
        $this->assertNull($hotline["indice_satisfaction"],"assert 8");
        $this->assertEquals($hotline["etat"],"fixing","assert 9");
        $this->assertEquals($hotline["pole_concerne"],"system","assert 10");
        $this->assertEquals($hotline["id_user"],$this->alt_id_user,"assert 11");
        
        //Test notices
        $notices=ATF::$msg->getNotices();
        $this->assertTrue(is_array($notices),"assert 12");
        $this->assertEquals(4,count($notices),"assert 13");
    }
    
    // @author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_insert_intervention_affaire() {
        $this->initUser(false);
        $this->initAltUser();
        $this->initAffaire();
        //Requête intervention
        $id_hotline=$this->obj->insert(
            array("charge"=>"intervention"
                 ,"hotline"=>"test"
                 ,"detail"=>"detail"
                 ,"pole_concerne"=>"dev"
                 ,"id_societe"=>$this->id_societe
                 ,"id_contact"=>$this->id_contact
                 ,"type_requete"=>"affaire"
                 ,"id_affaire"=>$this->id_affaire
                 ,"send_mail"=>true
                 ,"visible"=>"oui"));
        $this->assertNotNull($id_hotline,"La requête ne se crée pas...");
        $hotline=$this->obj->select($id_hotline);
          
        //Tests hotline
        $this->assertEquals($hotline["facturation_ticket"],"non","assert 1");
        $this->assertNull($hotline["ok_facturation"],"assert 2");
        $this->assertEquals($hotline["charge"],"intervention","assert 3");
        $this->assertEquals($hotline["hotline"],"test","assert 4");
        //print_r($hotline["detail"]);die();
        $this->assertEquals($hotline["detail"],"Requête mise en ligne par class: unitaire\n\ndetail","assert 5");
        $this->assertEquals($hotline["id_societe"],$this->id_societe,"assert 6");
        $this->assertEquals($hotline["id_contact"],$this->id_contact,"assert 7");
        $this->assertNull($hotline["indice_satisfaction"],"assert 8");
        $this->assertEquals($hotline["etat"],"free","assert 9");
        $this->assertEquals($hotline["id_affaire"],$this->id_affaire,"assert 10");
        
        //Test notices
        $notices=ATF::$msg->getNotices();
        $this->assertTrue(is_array($notices),"assert 11");
        $this->assertEquals(4,count($notices),"assert 12");
    }

    // @author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_insert_intervention_affaire_w_user() {
        $this->initUser(false);
        $this->initAltUser();
        $this->initAffaire();
        //Requête intervention
        $id_hotline=$this->obj->insert(
            array("charge"=>"intervention"
                 ,"hotline"=>"test"
                 ,"detail"=>"detail"
                 ,"pole_concerne"=>"dev"
                 ,"id_societe"=>$this->id_societe
                 ,"id_contact"=>$this->id_contact
                 ,"type_requete"=>"affaire"
                 ,"id_user"=>$this->alt_id_user
                 ,"id_affaire"=>$this->id_affaire
                 ,"send_mail"=>true
                 ,"visible"=>"oui"));
        $this->assertNotNull($id_hotline,"La requête ne se crée pas...");
        $hotline=$this->obj->select($id_hotline);
        
        //Tests hotline
        $this->assertEquals($hotline["facturation_ticket"],"non","assert 1");
        $this->assertNull($hotline["ok_facturation"],"assert 2");
        $this->assertEquals($hotline["charge"],"intervention","assert 3");
        $this->assertEquals($hotline["hotline"],"test","assert 4");
        //print_r($hotline["detail"]);die();
        $this->assertEquals($hotline["detail"],"Requête mise en ligne par class: unitaire\n\ndetail","assert 5");
        $this->assertEquals($hotline["id_societe"],$this->id_societe,"assert 6");
        $this->assertEquals($hotline["id_contact"],$this->id_contact,"assert 7");
        $this->assertNull($hotline["indice_satisfaction"],"assert 8");
        $this->assertEquals($hotline["etat"],"fixing","assert 9");
        $this->assertEquals($hotline["pole_concerne"],"dev","assert 10");
        $this->assertEquals($hotline["id_user"],$this->alt_id_user,"assert 11");
        $this->assertEquals($hotline["id_affaire"],$this->id_affaire,"assert 12");
        
        //Test notices
        $notices=ATF::$msg->getNotices();
        $this->assertTrue(is_array($notices),"assert 13");
        $this->assertEquals(4,count($notices),"assert 14");
    }

    // @author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_insert_rd_charge_at() {
        $this->initUser(false);
        $this->initAltUser();
        //Requête intervention
        $id_hotline=$this->obj->insert(
            array("charge"=>"rd"
                 ,"hotline"=>"test"
                 ,"detail"=>"detail"
                 ,"pole_concerne"=>"dev"
                 ,"id_societe"=>$this->id_societe
                 ,"id_contact"=>$this->id_contact
                 ,"type_requete"=>"charge_absystech"
                 ,"send_mail"=>true));
        $this->assertNotNull($id_hotline,"La requête ne se crée pas...");
        $hotline=$this->obj->select($id_hotline);
           
        //Tests hotline
        $this->assertEquals($hotline["facturation_ticket"],"non","assert 1");
        $this->assertNull($hotline["ok_facturation"],"assert 2");
        $this->assertEquals($hotline["charge"],"rd","assert 3");
        $this->assertEquals($hotline["hotline"],"test","assert 4");
        //print_r($hotline["detail"]);die();
        $this->assertEquals($hotline["detail"],"Requête mise en ligne par class: unitaire\n\ndetail","assert 5");
        $this->assertEquals($hotline["id_societe"],$this->id_societe,"assert 6");
        $this->assertEquals($hotline["id_contact"],$this->id_contact,"assert 7");
        $this->assertEquals($hotline["pole_concerne"],"dev","assert 8");
        $this->assertNull($hotline["indice_satisfaction"],"assert 9");
        $this->assertEquals($hotline["etat"],"free","assert 10");
        
        //Test notices
        $notices=ATF::$msg->getNotices();
        $this->assertTrue(is_array($notices),"assert 11");
        $this->assertEquals(4,count($notices),"assert 12");
    }
    
    // @author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_insert_rd_charge_at_w_user() {
        $this->initUser(false);
        $this->initAltUser();
        //Requête intervention
        $id_hotline=$this->obj->insert(
            array("charge"=>"rd"
                 ,"hotline"=>"test"
                 ,"detail"=>"detail"
                 ,"pole_concerne"=>"dev"
                 ,"id_societe"=>$this->id_societe
                 ,"id_contact"=>$this->id_contact
                 ,"type_requete"=>"charge_absystech"
                 ,"id_user"=>$this->alt_id_user
                 ,"send_mail"=>true));
        $this->assertNotNull($id_hotline,"La requête ne se crée pas...");
        $hotline=$this->obj->select($id_hotline);
        
        //Tests hotline
        $this->assertEquals($hotline["facturation_ticket"],"non","assert 1");
        $this->assertNull($hotline["ok_facturation"],"assert 2");
        $this->assertEquals($hotline["charge"],"rd","assert 3");
        $this->assertEquals($hotline["hotline"],"test","assert 4");
        //print_r($hotline["detail"]);die();
        $this->assertEquals($hotline["detail"],"Requête mise en ligne par class: unitaire\n\ndetail","assert 5");
        $this->assertEquals($hotline["id_societe"],$this->id_societe,"assert 6");
        $this->assertEquals($hotline["id_contact"],$this->id_contact,"assert 7");
        $this->assertNull($hotline["indice_satisfaction"],"assert 8");
        $this->assertEquals($hotline["pole_concerne"],"dev","assert 9");
        $this->assertEquals($hotline["etat"],"fixing","assert 10");
        $this->assertEquals($hotline["id_user"],$this->alt_id_user,"assert 11");
        
        //Test notices
        $notices=ATF::$msg->getNotices();
        $this->assertTrue(is_array($notices),"assert 12");
        $this->assertEquals(4,count($notices),"assert 13");
    }

    // @author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_insert_maintenance() {
        $this->initUser(false);
        $this->initAltUser();
        $this->initAffaire();
        //Requête intervention
        $id_hotline=$this->obj->insert(
            array("charge"=>"maintenance"
                 ,"hotline"=>"test"
                 ,"detail"=>"detail"
                 ,"urgence"=>"detail"
                 ,"pole_concerne"=>"dev"
                 ,"id_societe"=>$this->id_societe
                 ,"id_contact"=>$this->id_contact
                 ,"type_requete"=>"affaire"
                 ,"id_affaire"=>$this->id_affaire
                 ,"send_mail"=>true));
        $this->assertNotNull($id_hotline,"La requête ne se crée pas...");
        $hotline=$this->obj->select($id_hotline);
        $this->assertEquals("detail",$hotline['urgence'],"La requête n'a pas la bonne urgence");
        
        //Tests hotline
        $this->assertEquals($hotline["facturation_ticket"],"non","assert 1");
        $this->assertNull($hotline["ok_facturation"],"assert 2");
        $this->assertEquals($hotline["charge"],"maintenance","assert 3");
        $this->assertEquals($hotline["hotline"],"test","assert 4");
        //print_r($hotline["detail"]);die();
        $this->assertEquals($hotline["detail"],"Requête mise en ligne par class: unitaire\n\ndetail","assert 5");
        $this->assertEquals($hotline["id_societe"],$this->id_societe,"assert 6");
        $this->assertEquals($hotline["id_contact"],$this->id_contact,"assert 7");
        $this->assertNull($hotline["indice_satisfaction"],"assert 8");
        $this->assertEquals($hotline["etat"],"free","assert 9");
        $this->assertEquals($hotline["id_affaire"],$this->id_affaire,"assert 10");
        
        //Test notices
        $notices=ATF::$msg->getNotices();
        $this->assertTrue(is_array($notices),"assert 11");
        $this->assertEquals(4,count($notices),"assert 12");
    }

    // @author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_insert_maintenance_w_user() {
        $this->initUser(false);
        $this->initAltUser();
        $this->initAffaire();
        //Requête intervention
        $id_hotline=$this->obj->insert(
            array("charge"=>"maintenance"
                 ,"hotline"=>"test"
                 ,"detail"=>"detail"
                 ,"priorite"=>11
                 ,"pole_concerne"=>"dev"
                 ,"id_societe"=>$this->id_societe
                 ,"id_contact"=>$this->id_contact
                 ,"type_requete"=>"affaire"
                 ,"id_affaire"=>$this->id_affaire
                 ,"id_user"=>$this->alt_id_user
                 ,"send_mail"=>true));
        $this->assertNotNull($id_hotline,"La requête ne se crée pas...");
        $hotline=$this->obj->select($id_hotline);
        $this->assertEquals("genant",$hotline['urgence'],"La requête n'a pas la bonne urgence");
        
        //Tests hotline
        $this->assertEquals($hotline["facturation_ticket"],"non","assert 1");
        $this->assertNull($hotline["ok_facturation"],"assert 2");
        $this->assertEquals($hotline["charge"],"maintenance","assert 3");
        $this->assertEquals($hotline["hotline"],"test","assert 4");
        //print_r($hotline["detail"]);die();
        $this->assertEquals($hotline["detail"],"Requête mise en ligne par class: unitaire\n\ndetail","assert 5");
        $this->assertEquals($hotline["id_societe"],$this->id_societe,"assert 6");
        $this->assertEquals($hotline["id_contact"],$this->id_contact,"assert 7");
        $this->assertNull($hotline["indice_satisfaction"],"assert 8");
        $this->assertEquals($hotline["etat"],"fixing","assert 9");
        $this->assertEquals($hotline["id_affaire"],$this->id_affaire,"assert 10");
        $this->assertEquals($hotline["id_user"],$this->alt_id_user,"assert 11");
        $this->assertEquals($hotline["priorite"],11,"assert 12");
        
        //Test notices
        $notices=ATF::$msg->getNotices();
        $this->assertTrue(is_array($notices),"assert 13");
        $this->assertEquals(4,count($notices),"assert 14");
    }

    // @author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_insert_mono_interaction() {
        $this->initUser(false);
        $this->initAltUser();
        $this->initAffaire();
        //Requête intervention
        $id_hotline=$this->obj->insert(
            array("charge"=>"rd"
                 ,"hotline"=>"test"
                 ,"detail"=>"detail"
                 ,"priorite"=>11
                 ,"pole_concerne"=>"dev"
                 ,"id_societe"=>$this->id_societe
                 ,"id_contact"=>$this->id_contact
                 ,"type_requete"=>"charge_absystech"
                 ,"mono_interaction"=>true
                 ,"temps_mono_interaction"=>"1:00"
                 ,"send_mail"=>true));
        $this->assertNotNull($id_hotline,"La requête ne se crée pas...");
        $hotline=$this->obj->select($id_hotline);
        $this->assertEquals("genant",$hotline['urgence'],"La requête n'a pas la bonne urgence");
        
        //Tests hotline
        $this->assertEquals($hotline["facturation_ticket"],"non","assert 1");
        $this->assertNull($hotline["ok_facturation"],"assert 2");
        $this->assertEquals($hotline["charge"],"rd","assert 3");
        $this->assertEquals($hotline["hotline"],"test","assert 4");
        //print_r($hotline["detail"]);die();
        $this->assertEquals($hotline["detail"],"Requête mise en ligne par class: unitaire\n\ndetail","assert 5");
        $this->assertEquals($hotline["id_societe"],$this->id_societe,"assert 6");
        $this->assertEquals($hotline["id_contact"],$this->id_contact,"assert 7");
        $this->assertNull($hotline["indice_satisfaction"],"assert 8");
        $this->assertEquals($hotline["etat"],"free","assert 9");
        $this->assertEquals($hotline["id_user"],ATF::$usr->getId(),"assert 11");
        $this->assertEquals($hotline["priorite"],11,"assert 12");
        
        //Tests des interactions
        ATF::hotline_interaction()->q->reset()
            ->addCondition("id_hotline",$id_hotline);
        $inters=ATF::hotline_interaction()->sa();
        
        $this->assertEquals($inters[0]["temps"],"00:00:00","assert 13");
        $this->assertEquals($inters[0]["temps_passe"],"00:00:00","assert 14");
        $this->assertEquals($inters[0]["detail"],"Requête créée par class: unitaire","assert 15");
        $this->assertEquals($inters[0]["id_user"],ATF::$usr->getId(),"assert 16");
        $this->assertEquals($inters[0]["visible"],"non","assert 17");
        
        $this->assertEquals($inters[1]["detail"],"detail","assert 20");
        $this->assertEquals($inters[1]["id_user"],ATF::$usr->getId(),"assert 21");
        $this->assertEquals($inters[1]["visible"],"oui","assert 22");
        
        //Test notices
        $notices=ATF::$msg->getNotices();
        $this->assertTrue(is_array($notices),"assert 23");
        $this->assertEquals(4,count($notices),"assert 24");
    }

    // @author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_insert_mono_interaction_w_billing() {
        $this->initUser(false);
        $this->initAltUser();
        $this->initAffaire();
        //Requête intervention
        $id_hotline=$this->obj->insert(
            array("charge"=>"intervention"
                 ,"hotline"=>"test"
                 ,"detail"=>"detail"
                 ,"priorite"=>11
                 ,"pole_concerne"=>"dev"
                 ,"id_societe"=>$this->id_societe
                 ,"id_contact"=>$this->id_contact
                 ,"type_requete"=>"charge_client"
                 ,"mono_interaction"=>true
                 ,"temps_mono_interaction"=>"1:00"
                 ,"send_mail"=>true));
        $this->assertNotNull($id_hotline,"La requête ne se crée pas...");
        $hotline=$this->obj->select($id_hotline);
        $this->assertEquals("genant",$hotline['urgence'],"La requête n'a pas la bonne urgence");
        
        //Tests hotline
        $this->assertEquals($hotline["facturation_ticket"],"oui","assert 1");
        $this->assertNull($hotline["ok_facturation"],"assert 2");
        $this->assertEquals($hotline["charge"],"intervention","assert 3");
        $this->assertEquals($hotline["hotline"],"test","assert 4");
        //print_r($hotline["detail"]);die();
        $this->assertEquals($hotline["detail"],"Requête mise en ligne par class: unitaire\n\ndetail","assert 5");
        $this->assertEquals($hotline["id_societe"],$this->id_societe,"assert 6");
        $this->assertEquals($hotline["id_contact"],$this->id_contact,"assert 7");
        $this->assertNull($hotline["indice_satisfaction"],"assert 8");
        $this->assertEquals($hotline["etat"],"wait","assert 9");
        $this->assertEquals($hotline["id_user"],ATF::$usr->getId(),"assert 11");
        $this->assertEquals($hotline["priorite"],11,"assert 12");
        
        //Tests des interactions
        ATF::hotline_interaction()->q->reset()
            ->addCondition("id_hotline",$id_hotline);
        $inters=ATF::hotline_interaction()->sa();
        
        $this->assertEquals($inters[0]["detail"],"Requête créée par class: unitaire","assert 15");
        $this->assertEquals($inters[0]["id_user"],ATF::$usr->getId(),"assert 16");
        $this->assertEquals($inters[0]["visible"],"non","assert 17");        
        $this->assertEquals($inters[1]["detail"],"detail","assert 20");
        $this->assertEquals($inters[1]["id_user"],ATF::$usr->getId(),"assert 21");
        $this->assertEquals($inters[1]["visible"],"oui","assert 22");
        
        //Test notices
        $notices=ATF::$msg->getNotices();
        $this->assertTrue(is_array($notices),"assert 23");
        $this->assertEquals(4,count($notices),"assert 24");
    }
    
    // @author Jérémie Gwiazdowski <jgw@absystech.fr>
    /*public function test_insert_mono_interaction_w_error() {
        $this->initUser(false);
        $this->initAltUser();
        $this->initAffaire();
        $error=false;
        try{
            //Requête intervention
            $id_hotline=$this->obj->insert(
                array("charge"=>"intervention"
                     ,"hotline"=>"test"
                     ,"detail"=>"detail"
                     ,"priorite"=>11
                     ,"pole_concerne"=>"dev"
                     ,"id_societe"=>$this->id_societe
                     ,"id_contact"=>$this->id_contact
                     ,"type_requete"=>"charge_client"
                     ,"mono_interaction"=>true
                     ,"estimation"=>"00:00"
                     ,"send_mail"=>true));
        }catch(error $e){
            $error=true;
            ATF::db()->rollback_transaction();
        }
        $this->assertTrue($error,"assert 1");
        //Test notices
        $notices=ATF::$msg->getNotices();
        $this->assertTrue(is_array($notices),"assert 2");
        $this->assertEquals(3,count($notices),"assert 3");
    }*/
    
    // @author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_update(){
        $this->initUser(false);
        $this->initHotline();
        ATF::$msg->getNotices();//Flush des notices
        $this->obj->update(array("id_hotline"=>$this->id_hotline,"hotline"=>"test","detail"=>"hop","send_mail"=>true));
        $hotline=$this->obj->select($this->id_hotline);
        $this->assertEquals($hotline["hotline"],"test","Le changement de titre n'a pas opéré !!");
        $this->assertEquals($hotline["detail"],"hop","Le changement de contenu de la requête n'a pas opéré !!");
        
        //Test notices
        $notices=ATF::$msg->getNotices();
        $this->assertTrue(is_array($notices));
        $this->assertEquals(0,count($notices));
    }
    
    // @author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_cancelRequest() {
        $this->initUser(false);
        $this->initHotline("alt_id_user");
        ATF::$msg->getNotices();//Flush des notices
        $this->obj->cancelRequest(array("id_hotline"=>$this->id_hotline,"send_mail"=>true));
        $hotline=$this->obj->select($this->id_hotline);
        $this->assertEquals($hotline["etat"],"annulee","La requête n'est pas passée en été annulée !!");
        
        //Test notices
        $notices=ATF::$msg->getNotices();
        $this->assertTrue(is_array($notices));
        $this->assertEquals(2,count($notices));
    }
   
    // @author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_fixingRequest() {
        $this->initUser(false);


        $this->initHotline();
        ATF::$msg->getNotices();//Flush des notices
        //Passage de la requête en wait
        $this->obj->update(array("id_hotline"=>$this->id_hotline,"etat"=>"wait"));
        //Passage en fixing
        $this->obj->fixingRequest(array("id_hotline"=>$this->id_hotline));
        $hotline=$this->obj->select($this->id_hotline);
        $this->assertEquals($hotline["etat"],"fixing","La requête n'est pas passée en état fixing !!");
        
        //Test notices
        $notices=ATF::$msg->getNotices();
        $this->assertTrue(is_array($notices));
        $this->assertEquals(1,count($notices));
    }

    // @author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_setWait(){
        $this->initUser(false);
        $this->initHotline();
        ATF::$msg->getNotices();//Flush des notices
        //Passage de la requête en fixing
        $this->obj->update(array("id_hotline"=>$this->id_hotline,"etat"=>"fixing","facturation_ticket"=>"non"));
        //Passage en wait
        $this->obj->setWait(array("id_hotline"=>$this->id_hotline));
        $hotline=$this->obj->select($this->id_hotline);
        $this->assertEquals($hotline["etat"],"wait","La requête n'est pas passée en état wait !!");
        
        //Test notices
        $notices=ATF::$msg->getNotices();
        $this->assertTrue(is_array($notices));
        $this->assertEquals(1,count($notices));
    }

    // @author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_default_value(){
        $this->initUserOnly(false);
        //Test avec un user qui appartient à plusieurs pôles
        //Maj du user
        ATF::user()->update(array("id_user"=>$this->id_user,"pole"=>"system,telecom"));
        $value=$this->obj->default_value("pole_concerne");
        $this->assertEquals($value,"system","Le pôle n'est pas le bon !!"); 

        $value=$this->obj->default_value("estimation");
        $this->assertEquals($value,"00:00","L'estimation n'est pas le bonne !!");
        
        //Default value parent
        $value=$this->obj->default_value("urgence");
        $this->assertNull($value);  
    }   
    
    // @author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_setWaitMep() {
        $this->initUser(false);
        $this->initHotline();
        ATF::$msg->getNotices();//Flush des notices
        $this->obj->setWaitMep(array(
            "id_hotline"=>$this->id_hotline
            ,"send_mail"=>true));
        $hotline=$this->obj->select($this->id_hotline);
        $this->assertEquals($hotline["wait_mep"],"oui","La requête n'est pas en mode wait mep!!");  
        
        //Test notices
        $notices=ATF::$msg->getNotices();
        $this->assertTrue(is_array($notices));
        $this->assertEquals(2,count($notices));
    }

    // @author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_setMep() {
        $this->initUser(false);
        $this->initHotline();
        
        $this->obj->setWaitMep(array("id_hotline"=>$this->id_hotline));
        
        ATF::$msg->getNotices();//Flush des notices
        
        $this->obj->setMep(array(
            "id_hotline"=>$this->id_hotline
            ,"send_mail"=>true));
        $hotline=$this->obj->select($this->id_hotline);
        $this->assertEquals($hotline["wait_mep"],"non","La requête n'est pas repassé en wait mep non !!");
               
        //Test notices
        $notices=ATF::$msg->getNotices();
        $this->assertTrue(is_array($notices));
        $this->assertEquals(2,count($notices));
    }   
    
    // @author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_cancelMep() {
        $this->initUser(false);
        $this->initHotline();
        
        $this->obj->setWaitMep(array("id_hotline"=>$this->id_hotline));
        
        ATF::$msg->getNotices();//Flush des notices
        
        $this->obj->cancelMep(array(
            "id_hotline"=>$this->id_hotline
            ,"send_mail"=>true));
        $hotline=$this->obj->select($this->id_hotline);
        $this->assertEquals($hotline["wait_mep"],"non","La requête n'est pas repassé en wait mep non !!");

        
        //Test notices
        $notices=ATF::$msg->getNotices();
        $this->assertTrue(is_array($notices));
        $this->assertEquals(1,count($notices));
    }   

    
    // @author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_createPortailHotlineURL(){
        $this->initUser(false);
        $this->initHotline();
        $url=$this->obj->createPortailHotlineURL("login","pwd",$this->id_hotline,"851");
        $this->assertEquals(strlen($url)>76,true,"L'url n'est pas créée !!");
    }

    // @author Jérémie Gwiazdowski <jgw@absystech.fr>, Yann GAUTHERON <ygautheron@absystech.fr>
    public function test_getSimplePriorite(){
        //Etat free
        $prio=$this->obj->getSimplePriorite("free",0);
        $this->assertEquals($prio["progressBar"],"progressRed");
        //Done
        $prio=$this->obj->getSimplePriorite("done",0);
        $this->assertEquals($prio["progressBar"],"progressGrey");
        //Payee
        $prio=$this->obj->getSimplePriorite("payee",0);
        $this->assertEquals($prio["progressBar"],"progressGrey");
        //détail
        $prio=$this->obj->getSimplePriorite("fixing",5);
        $this->assertEquals($prio["progressBar"],"progressGreen");
        //bloquant
        $prio=$this->obj->getSimplePriorite("fixing",10);
        $this->assertEquals($prio["progressBar"],"progressOrange");
        //urgent
        $prio=$this->obj->getSimplePriorite("fixing",15);
        $this->assertEquals($prio["progressBar"],"progressRed");
        //wait_MEP = oui
        $prio=$this->obj->getSimplePriorite("fixing",15,"oui");
        $this->assertEquals($prio["progressBar"],"progressGreen");
    }   

    // @author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_setPriorite($infos){
        $this->initUser(false);
        $this->initHotline();
        ATF::$msg->getNotices();//Flush des notices
        
        //Cas classique
        $prio=$this->obj->setPriorite(array("id_hotline"=>$this->id_hotline,"priorite"=>17));
        $hotline=$this->obj->select($this->id_hotline);
        $this->assertEquals($hotline["priorite"],17);   
        
        //Test notices
        $notices=ATF::$msg->getNotices();
        $this->assertTrue(is_array($notices));
        $this->assertEquals(1,count($notices));
        
        //Range invalide
        $error=false;
        try{
            $prio=$this->obj->setPriorite(array("id_hotline"=>$this->id_hotline,"priorite"=>50));
        }catch(error $e){
            $error=$e->getMessage();
        }
        $this->assertEquals($error,"invalid_range");    
        //Priorité inchangé normalement
        $hotline=$this->obj->select($this->id_hotline);
        $this->assertEquals($hotline["priorite"],17);   
        
        //Test notices
        $notices=ATF::$msg->getNotices();
        $this->assertTrue(is_array($notices));
        $this->assertEquals(0,count($notices));
        
        //Avec refresh de la fiche hotline
        $prio=$this->obj->setPriorite(array("id_hotline"=>$this->id_hotline,"priorite"=>18,"hotline_select"=>true));
        $hotline=$this->obj->select($this->id_hotline);
        $this->assertEquals($hotline["priorite"],18);   
        
        //Test notices
        $notices=ATF::$msg->getNotices();
        $this->assertTrue(is_array($notices));
        $this->assertEquals(1,count($notices));
        
        //Test crefresh
        $cr=ATF::$cr->getCrefresh();
        $this->assertEquals("generic-select.tpl.htm",$cr["main"]["template"]);
    }

    // @author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_setPrioriteByUrgence(){
        $this->initUser(false);
        $this->initHotline();
        //Priorité détail
        $prio=$this->obj->setPrioriteByUrgence($this->id_hotline,"detail");
        $hotline=$this->obj->select($this->id_hotline);
        $this->assertEquals($hotline["priorite"],5);    
        //Priorité Génant
        $prio=$this->obj->setPrioriteByUrgence($this->id_hotline,"genant");
        $hotline=$this->obj->select($this->id_hotline);
        $this->assertEquals($hotline["priorite"],10);
        //Priorité Bloquant
        $prio=$this->obj->setPrioriteByUrgence($this->id_hotline,"bloquant");
        $hotline=$this->obj->select($this->id_hotline);
        $this->assertEquals($hotline["priorite"],15);
    }


    // @author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_getAvancement(){
        //Etats classiques
        $perc=$this->obj->getAvancement("free",0);
        $this->assertEquals($perc,0);
        $perc=$this->obj->getAvancement("done",0);

        $this->assertEquals($perc,90);
        $perc=$this->obj->getAvancement("payee",0);
        $this->assertEquals($perc,100);
        $perc=$this->obj->getAvancement("annulee",0);
        $this->assertEquals($perc,100);
        
        //Etat particulier
        $perc=$this->obj->getAvancement("fixing",60);
        $this->assertEquals($perc,56);
        $perc=$this->obj->getAvancement("fixing",50,false);
        $this->assertEquals($perc,50);
        
        //Etat pourri
        $perc=$this->obj->getAvancement("pourri",60);
        $this->assertFalse($perc);
    }
    
    // @author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_setAvancement(){
        $this->initUser(false);
        $this->initHotline();
        ATF::$msg->getNotices();//Flush des notices
        
        $this->obj->setAvancement($this->id_hotline,40);
        $hotline=$this->obj->select($this->id_hotline);
        $this->assertEquals($hotline["avancement"],40);
        
        //Range invalide
        $error=false;
        try{
            $this->obj->setAvancement($this->id_hotline,400);
        }catch(error $e){
            $error=$e->getMessage();
        }
        $this->assertEquals($error,"invalid_range");    

        //Normalement l'avancement n'a pas changé !!
        $hotline=$this->obj->select($this->id_hotline);
        $this->assertEquals($hotline["avancement"],40);
    }


    // ---------------Stats---------------------------
    // @author Nicolas BERTEMONT <nbertemont@absystech.fr>
    public function test_stats(){
        //------------- top 10 -------------
        //insertion de 11 societes avec 11 soldes négatifs pour que seules 10 en reviennent
        $id_soc1=ATF::societe()->i(array('societe'=>'soc lol 1'));
        $id_soc2=ATF::societe()->i(array('societe'=>'soc lol 2'));
        $id_soc3=ATF::societe()->i(array('societe'=>'soc lol 3'));
        $id_soc4=ATF::societe()->i(array('societe'=>'soc lol 4'));
        $id_soc5=ATF::societe()->i(array('societe'=>'soc lol 5'));
        $id_soc6=ATF::societe()->i(array('societe'=>'soc lol 6'));
        $id_soc7=ATF::societe()->i(array('societe'=>'soc lol 7'));
        $id_soc8=ATF::societe()->i(array('societe'=>'soc lol 8'));
        $id_soc9=ATF::societe()->i(array('societe'=>'soc lol 9'));
        $id_soc10=ATF::societe()->i(array('societe'=>'soc lol 10'));
        $id_soc11=ATF::societe()->i(array('societe'=>'soc lol 11'));

        ATF::gestion_ticket()->multi_insert(array(0=>array("operation"=>1,"id_societe"=>$id_soc1,"type"=>"ajout","solde"=>"-1000")
                                                ,1=>array("operation"=>1,"id_societe"=>$id_soc2,"type"=>"ajout","solde"=>"-2000")
                                                ,2=>array("operation"=>1,"id_societe"=>$id_soc3,"type"=>"ajout","solde"=>"-3000")
                                                ,3=>array("operation"=>1,"id_societe"=>$id_soc4,"type"=>"ajout","solde"=>"-4000")
                                                ,4=>array("operation"=>1,"id_societe"=>$id_soc5,"type"=>"ajout","solde"=>"-5000")
                                                ,5=>array("operation"=>1,"id_societe"=>$id_soc6,"type"=>"ajout","solde"=>"-6000")
                                                ,6=>array("operation"=>1,"id_societe"=>$id_soc7,"type"=>"ajout","solde"=>"-7000")
                                                ,7=>array("operation"=>1,"id_societe"=>$id_soc8,"type"=>"ajout","solde"=>"-8000")
                                                ,8=>array("operation"=>1,"id_societe"=>$id_soc9,"type"=>"ajout","solde"=>"-9000")
                                                ,9=>array("operation"=>1,"id_societe"=>$id_soc10,"type"=>"ajout","solde"=>"-10000")
                                                ,10=>array("operation"=>1,"id_societe"=>$id_soc11,"type"=>"ajout","solde"=>"-11000")));

        $top10=$this->obj->stats(false,"top10negatif");

        //check de la méthode ajoutDonnees
        $this->assertEquals(1,count($top10['dataset']),'Problème de récupération des données à afficher');
        $this->assertEquals(15,count($top10['categories']['category']),'Nombre des données à afficher incorrecte');
        //check de la méthode paramGraphe
        $this->assertTrue(is_array($top10['params']),'Problème de récupération des params');
        //le nombre de valeur dans les set de data doit être égal au nombre de catégorie
        $this->assertEquals(count($top10['categories']['category']),count($top10['dataset']["solde"]['set']),"Le nombre de données à afficher en abscisse n'est pas correct");

        //test du contenu
        $this->assertEquals("-11000",$top10['dataset']['solde']['set'][$id_soc11]['value'],"Le solde inscrit est incorrecte");
        $this->assertEquals("soc lol 5",$top10['dataset']['solde']['set'][$id_soc5]['titre'],"Le nom inscrit est incorrecte");
        $this->assertEquals("societe-select-".ATF::societe()->cryptID($id_soc2).".html",$top10['dataset']['solde']['set'][$id_soc2]['link'],"Le lien inscrit est incorrecte");
        //$this->assertFalse(isset($top10['dataset']['solde']['set'][$id_soc1]),"Cette societe ne devrait pas etre présente dans la liste");

        //------------- avec widget -------------
        //insertion de user et de ticket pour check les données renvoyées
        //pour tester sans être préoccupé par le contenu actuel de la table, je supprime tout
        ATF::hotline()->q->reset()->addConditionNotNull("id_hotline");
        ATF::hotline()->delete();
        $id_user=ATF::user()->i(array("login"=>"lol","password"=>"lol","prenom"=>"lol","nom"=>"mdr"));
        $id_user2=ATF::user()->i(array("login"=>"lol2","password"=>"lol","prenom"=>"ptdr","nom"=>"exptdr"));
        $this->obj->multi_insert(array(0=>array("id_societe"=>$id_soc1,"hotline"=>"hot lol 1","id_user"=>$id_user,"urgence"=>"detail", "pole_concerne"=> "dev")
                                       ,1=>array("id_societe"=>$id_soc1,"hotline"=>"hot lol 2","id_user"=>$id_user,"urgence"=>"detail", "pole_concerne"=> "dev")
                                       ,2=>array("id_societe"=>$id_soc1,"hotline"=>"hot lol 3","id_user"=>$id_user,"urgence"=>"genant", "pole_concerne"=> "system")
                                       ,3=>array("id_societe"=>$id_soc1,"hotline"=>"hot lol 4","id_user"=>$id_user2,"urgence"=>"detail", "pole_concerne"=> "system")
                                       ,4=>array("id_societe"=>$id_soc1,"hotline"=>"hot lol 5","id_user"=>$id_user2,"urgence"=>"bloquant", "pole_concerne"=> "telecom")));
        //pour le user ?
        $this->obj->i(array("id_societe"=>$id_soc1,"hotline"=>"hot lol 5","urgence"=>"bloquant"));

        $widget=$this->obj->stats(true);

        $this->assertEquals("lm",$widget['categories']['category'][0]["label"],"widget / Les initiales du premier user sont incorrectes");
        $this->assertEquals("?",$widget['categories']['category'][2]["label"],"widget / Le nom du troisieme user est incorrect");
        $this->assertEquals(3,count($widget['categories']['category']),"widget / Le nombre d'utilisateur est incorrect");
        $this->assertEquals(3,count($widget['dataset']),"widget / Le nombre de dataset est incorrect");
        $this->assertEquals(count($widget['categories']['category']),count($widget['dataset']["bloquant"]['set']),"widget / Le nombre de données à afficher en abscisse n'est pas correct");
        $this->assertEquals("Bloquant",$widget['dataset']["bloquant"]['params']['seriesname'],"widget / Le seriesname est incorrect");
        $this->assertEquals(2,$widget['dataset']["detail"]['set'][$id_user]['value'],"widget / le nombre de req en detail est incorrect");
        $this->assertEquals("Bloquant : 0",$widget['dataset']["bloquant"]['set'][$id_user]['titre'],"widget / le titre indiqué est incorrect");
        $this->assertEquals(urlencode("hotline.html,stats=1&label=".$id_user2),$widget['dataset']["bloquant"]['set'][$id_user2]['link'],"widget / le lien est incorrect");

        //------------- basique ------------
        $basique=$this->obj->stats();

        $this->assertEquals("lol mdr",$basique['categories']['category'][0]["label"],"basique / Le premier user est incorrect");
        $this->assertEquals(3,count($basique['categories']['category']),"basique / Le nombre d'utilisateur est incorrect");
        $this->assertEquals(3,count($basique['dataset']),"basique / Le nombre de dataset est incorrect");
        $this->assertEquals(count($basique['categories']['category']),count($basique['dataset']["bloquant"]['set']),"basique / Le nombre de données à afficher en abscisse n'est pas correct");
        $this->assertEquals("Bloquant",$basique['dataset']["bloquant"]['params']['seriesname'],"basique / Le seriesname est incorrect");
        $this->assertEquals(2,$basique['dataset']["detail"]['set'][$id_user]['value'],"basique / le nombre de req en detail est incorrect");
        $this->assertEquals("Bloquant : 0",$basique['dataset']["bloquant"]['set'][$id_user]['titre'],"basique / le titre indiqué est incorrect");
        $this->assertEquals(urlencode("hotline.html,stats=1&label=".$id_user2),$basique['dataset']["bloquant"]['set'][$id_user2]['link'],"basique / le lien est incorrect");

        //------------ Camembert Part des requetes
        $part = $this->obj->stats(true , "partTicket");

        $this->assertEquals("Part des requêtes hotline", $part['params']['caption'] ,"Part ticket, le titre n'est pas bon");
        $this->assertEquals(40, $part['dataset']["dev"]['pourcentage'] ,"pourcentage dev incorrect");

        $this->obj->multi_insert(array(0=>array("id_societe"=>$id_soc1,"hotline"=>"hot lol 1","date_debut"=>date("Y-m-01" , strtotime("-1 month")) ,"id_user"=>$id_user,"urgence"=>"detail", "pole_concerne"=> "dev","wait_mep"=>"oui")
                                      ,1=>array("id_societe"=>$id_soc1,"hotline"=>"hot lol 2","id_user"=>$id_user,"date_debut"=>date("Y-m-01" , strtotime("-1 month")), "urgence"=>"detail", "pole_concerne"=> "dev","wait_mep"=>"oui")));
        $waitmep = $this->obj->stats(true , "waitmep");

        $this->assertEquals("Charge actuelle en attente de MEP", $waitmep['params']['caption'] ,"Wait MEP, le titre n'est pas bon");
        $this->assertEquals(2, $waitmep['dataset']["detail"]["set"][$id_user]["value"] ,"nb requete en attente de MEP incorrect");

        $part2 = $this->obj->stats(true , "requetebyUser", $id_user);
        $this->assertEquals("Part des requêtes hotline <br /> pour lol", $part2['params']['caption'] ,"Part ticketbyUser, le titre n'est pas bon"); 
    }

    public function test_statNbCloture(){
        $this->obj->i(array("id_societe"=>971,"etat"=>"done", "date_debut"=> date("Y-m-d" , strtotime("-2 day")), "hotline"=>"hot lol 1","id_user"=>1,"urgence"=>"detail","date"=>date("Y-m-d H:i:s"), "pole_concerne"=> "dev","wait_mep"=>"oui"));
        $this->obj->i(array("id_societe"=>1,"etat"=>"done", "date_debut"=> date("Y-m-d" , strtotime("-2 day")), "hotline"=>"hot lol 2","id_user"=>1,"urgence"=>"detail","date"=>date("Y-m-d H:i:s"), "pole_concerne"=> "dev","wait_mep"=>"oui"));
        $this->obj->i(array("id_societe"=>1,"etat"=>"done", "date_debut"=> date("Y-m-d" , strtotime("-2 day")), "hotline"=>"hot lol 2","id_user"=>3,"urgence"=>"detail","date"=>date("Y-m-d H:i:s"), "pole_concerne"=> "dev","wait_mep"=>"oui"));
        $stat_cloture=$this->obj->statNbCloture();
        
        $this->assertNotNull($stat_cloture["dataset"]['dif']['set']['GAUTHERON']['value'],"statNbCloture");
        $this->assertNotNull($stat_cloture["dataset"]['dif']['set']['GAUTHERON']['value2'],"statNbCloture2");
    
    }

    // @author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_statsFiltrage(){
        $this->initUserOnly(false);
        
        ATF::env()->set("_r","label",ATF::$usr->getID());

        $id_ancien_filtre=$this->obj->statsFiltrage();
        
        $this->assertTrue(is_numeric($id_ancien_filtre),'1/ Le filtre n a pas ete cree');
        $filtre=ATF::filtre_optima()->select($id_ancien_filtre);
        $this->assertEquals($filtre['id_module'],ATF::module()->from_nom('hotline'),'1/ Le filtre ne possede pas les bonnes informations');
        $this->assertEquals($filtre['options'],'a:3:{s:4:"name";s:15:"Filtre de stats";s:4:"mode";s:3:"AND";s:10:"conditions";a:4:{i:0;a:3:{s:5:"field";s:15:"hotline.id_user";s:7:"operand";s:4:"LIKE";s:5:"value";s:15:"class: unitaire";}i:1;a:3:{s:5:"field";s:12:"hotline.etat";s:7:"operand";s:2:"!=";s:5:"value";s:5:"payee";}i:2;a:3:{s:5:"field";s:12:"hotline.etat";s:7:"operand";s:2:"!=";s:5:"value";s:7:"annulee";}i:3;a:3:{s:5:"field";s:12:"hotline.etat";s:7:"operand";s:2:"!=";s:5:"value";s:4:"done";}}}','Le options du filtre n est pas correct');

        //test avec le filtre existant et des donnees différentes
        ATF::env()->set("_r","label","");
        
        $id_filter_key=$this->obj->statsFiltrage();
        
        $this->assertEquals($id_filter_key,$id_ancien_filtre,'Le filtre n a pas ete modifie mais ajoute ou inexistant');
        $filtre_modif=ATF::filtre_optima()->select($id_filter_key);
        $this->assertEquals($filtre_modif['options'],'a:3:{s:4:"name";s:15:"Filtre de stats";s:4:"mode";s:3:"AND";s:10:"conditions";a:1:{i:0;a:3:{s:5:"field";s:12:"hotline.etat";s:7:"operand";s:1:"=";s:5:"value";s:4:"free";}}}','Le options du filtre modifie n est pas correct');  
    
    }
    
    // ---------------Crontab----------------------------
        
    // @author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_traitement_facturation_non_facture(){
        //Création de la requête
        ATF::db()->truncate("hotline");
        $this->initUser(false);
        $hotline = array(
            "hotline"=>"HotlineTuTest"
            ,"id_societe"=>$this->id_societe
            ,"detail"=>"HotlineTuTest"
            ,"date_debut"=>date("Y-m-d")
            ,"id_contact"=>$this->id_contact
            ,"id_user"=>$this->id_user
            ,"visible"=>"oui"
            ,"urgence"=>"detail"
            ,"pole_concerne"=>"dev"
            ,"type_requete"=>"charge_absystech"
        );
        $this->id_hotline = ATF::hotline()->insert($hotline);
        //Prise en charge
        $this->obj->takeRequest(
            array(
                "id_hotline"=>$this->id_hotline
                ,"id_user"=>$this->id_user)
        );
        //Insertion d'une interaction
        ATF::hotline_interaction()->insert(
            array(
                "id_hotline"=>$this->id_hotline
                ,"date"=>date("Y-m-d")
                ,"duree_presta"=>"1:00"
                ,'credit_presta'=>1
                ,'heure_debut_presta'=>date("H:i")
                ,'heure_fin_presta'=>date("H:i")
                ,"id_user"=>$this->id_user
                ,"detail"=>"Test"
        )); 
        //Résolution de la requête
        $this->obj->resolveRequest(
            array(
                "id_hotline"=>$this->id_hotline
                ,"id_user"=>$this->id_user)
        );
        //Validation par le client
        $this->obj->update(array("id_hotline"=>$this->id_hotline,"indice_satisfaction"=>1));
        
        ob_start();
        $this->obj->traitement_facturation($this->s);
        ob_end_clean();
        $hotline=$this->obj->select($this->id_hotline);
        
        $this->assertEquals("payee",$hotline["etat"],"assert 1");
        $this->assertEquals(0,$hotline["priorite"],"assert 2");
        
        ATF::$msg->getNotices();//Flush des notices
    }

    // @author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_traitement_facturation_facture(){
        //Création de la requête
        ATF::db()->truncate("hotline");
        ATF::db()->truncate("gestion_ticket");
        $this->initUser(false);
        $hotline = array(
            "hotline"=>"HotlineTuTest"
            ,"id_societe"=>$this->id_societe
            ,"detail"=>"HotlineTuTest"
            ,"date_debut"=>date("Y-m-d")
            ,"id_contact"=>$this->id_contact
            ,"id_user"=>$this->id_user
            ,"visible"=>"oui"
            ,"urgence"=>"detail"
            ,"pole_concerne"=>"dev"
            ,"type_requete"=>"charge_client"
        );
        $this->id_hotline = ATF::hotline()->insert($hotline);
        //Prise en charge
        $this->obj->takeRequest(
            array(
                "id_hotline"=>$this->id_hotline
                ,"id_user"=>$this->id_user)
        );
        //Validation par le client
        $this->obj->update(array("id_hotline"=>$this->id_hotline,"ok_facturation"=>"oui"));
        //Insertion d'une interaction
        ATF::hotline_interaction()->insert(
            array(
                "id_hotline"=>$this->id_hotline
                ,"date"=>date("Y-m-d")
                ,"duree_presta"=>"1:00"
                ,'credit_presta'=>1
                ,'heure_debut_presta'=>date("H:i")
                ,'heure_fin_presta'=>date("H:i")
                ,"id_user"=>$this->id_user
                ,"detail"=>"Test"
        )); 
        //Résolution de la requête
        $this->obj->resolveRequest(
            array(
                "id_hotline"=>$this->id_hotline
                ,"id_user"=>$this->id_user)
        );
        //Validation par le client
        $this->obj->update(array("id_hotline"=>$this->id_hotline,"indice_satisfaction"=>1));
        
        ob_start();
        $this->obj->traitement_facturation($this->s);
        ob_end_clean();
        $hotline=$this->obj->select($this->id_hotline);
        
        $this->assertEquals("payee",$hotline["etat"],"assert 1");
        $this->assertEquals(0,$hotline["priorite"],"assert 2");
        
        //Test sur la transaction
        ATF::gestion_ticket()->q->reset();
        $transactions=ATF::gestion_ticket()->sa();
        if(!is_array($transactions) || !$transactions[0]) throw new Exception("Pb sur Gestion Ticket !");
        $transaction=$transactions[0];
        
        $this->assertEquals(1,$transaction["operation"],"assert 3");
        $this->assertEquals($this->id_societe,$transaction["id_societe"],"assert 4");
        $this->assertEquals("retrait",$transaction["type"],"assert 5");
        $this->assertEquals($this->id_hotline,$transaction["id_hotline"],"assert 6");
        $this->assertEquals(-1.00,$transaction["nbre_tickets"],"assert 7");
        $this->assertEquals(-1.00,$transaction["solde"],"assert 8");
        
        ATF::$msg->getNotices();//Flush des notices
    }

    // @author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_upgradePriorite_std(){
        //Création de la requête
        //ATF::db()->truncate("hotline");
        $this->initUser(false);
        $hotline = array(
            "hotline"=>"HotlineTuTest"
            ,"id_societe"=>$this->id_societe
            ,"detail"=>"HotlineTuTest"
            ,"date_debut"=>date('Y-m-d')
            ,"id_contact"=>$this->id_contact
            ,"id_user"=>$this->id_user
            ,"visible"=>"oui"
            ,"urgence"=>'detail'
            ,"pole_concerne"=>"dev"
            ,'charge'=>"intervention"
        );
        $this->id_hotline = ATF::hotline()->insert($hotline);
        $this->obj->takeRequest(
            array(
                "id_hotline"=>$this->id_hotline
                ,"id_user"=>$this->id_user)
        );  

        ob_start();
        //$this->obj->upgradePriorite();
        ob_end_clean();
        //$this->assertEquals(6,ATF::hotline()->select($this->id_hotline,"priorite"),"assert 1");
        
        ATF::$msg->getNotices();//Flush des notices
    }
    
    // @author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_upgradePriorite_no_fixing(){
        //Création de la requête
        //ATF::db()->truncate("hotline");
        $this->initUser(false);
        $hotline = array(
            "hotline"=>"HotlineTuTest"
            ,"id_societe"=>$this->id_societe
            ,"detail"=>"HotlineTuTest"
            ,"date_debut"=>date('Y-m-d')
            ,"id_contact"=>$this->id_contact
            ,"id_user"=>$this->id_user
            ,"visible"=>"oui"
            ,"urgence"=>'detail'
            ,"pole_concerne"=>"dev"
            ,'charge'=>"intervention"
        );
        $this->id_hotline = ATF::hotline()->insert($hotline);

        ob_start();
        $this->obj->upgradePriorite();
        ob_end_clean();
        $this->assertEquals(20,ATF::hotline()->select($this->id_hotline,"priorite"),"assert 1");
        
        ATF::$msg->getNotices();//Flush des notices
    }
    
    // @author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_upgradePriorite_p_zero(){
        //Création de la requête
        //ATF::db()->truncate("hotline");
        $this->initUser(false);
        $hotline = array(
            "hotline"=>"HotlineTuTest"
            ,"id_societe"=>$this->id_societe
            ,"detail"=>"HotlineTuTest"
            ,"date_debut"=>date('Y-m-d')
            ,"id_contact"=>$this->id_contact
            ,"id_user"=>$this->id_user
            ,"visible"=>"oui"
            ,"urgence"=>'detail'
            ,"pole_concerne"=>"dev"
            ,'charge'=>"intervention"
        );
        $this->id_hotline = ATF::hotline()->insert($hotline);
        $this->obj->takeRequest(
            array(
                "id_hotline"=>$this->id_hotline
                ,"id_user"=>$this->id_user)
        );
        $this->obj->update(array("id_hotline"=>$this->id_hotline,"priorite"=>0));   

        ob_start();
        $this->obj->upgradePriorite();
        ob_end_clean();
        $this->assertEquals(0,ATF::hotline()->select($this->id_hotline,"priorite"),"assert 1");
        
        ATF::$msg->getNotices();//Flush des notices
    }
    
    // @author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_upgradePriorite_p_twenty(){
        //Création de la requête
        //ATF::db()->truncate("hotline");
        $this->initUser(false);
        $hotline = array(
            "hotline"=>"HotlineTuTest"
            ,"id_societe"=>$this->id_societe
            ,"detail"=>"HotlineTuTest"
            ,"date_debut"=>date('Y-m-d')
            ,"id_contact"=>$this->id_contact
            ,"id_user"=>$this->id_user
            ,"visible"=>"oui"           
            ,"pole_concerne"=>"dev"
            ,'charge'=>"intervention"
        );
        $this->id_hotline = ATF::hotline()->insert($hotline);
        $this->obj->takeRequest(
            array(
                "id_hotline"=>$this->id_hotline
                ,"id_user"=>$this->id_user)
        );  
        $this->obj->update(array("id_hotline"=>$this->id_hotline,"priorite"=>20));  

        ob_start();
        $this->obj->upgradePriorite();
        ob_end_clean();
        $this->assertEquals(20,ATF::hotline()->select($this->id_hotline,"priorite"),"assert 1");
        
        ATF::$msg->getNotices();//Flush des notices
    }
    
    // @author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_upgradePriorite_w_error(){
        //Création de la requête
        //ATF::db()->truncate("hotline");
        $this->initUser(false);
        $hotline = array(
            "hotline"=>"HotlineTuTest"
            ,"id_societe"=>$this->id_societe
            ,"detail"=>"HotlineTuTest"
            ,"date_debut"=>date('Y-m-d')
            ,"id_contact"=>$this->id_contact
            ,"id_user"=>$this->id_user
            ,"visible"=>"oui"           
            ,"pole_concerne"=>"dev"
            ,'charge'=>"intervention"
        );
        $this->id_hotline = ATF::hotline()->insert($hotline);
        
        //Prise en charge de la requête
        $this->obj->takeRequest(
            array(
                "id_hotline"=>$this->id_hotline
                ,"id_user"=>$this->id_user)
        );  
        
        //Maj de la priorité
        $this->obj->update(array("id_hotline"=>$this->id_hotline,"priorite"=>15));  

        //Test de upgrade priorité
        $this->obj=new hotlineTUbis();
        ob_start();
        $retour=$this->obj->upgradePriorite();
        ob_end_clean();
        $this->assertFalse($retour,"assert 1");
        
        //Flush des notices
        $msg=ATF::$msg->getNotices();
        $this->assertEquals(4,count($msg),"assert 2");


    }
    
    // ---------------Divers----------------------------
    
    // @author Nicolas BERTEMONT <nbertemont@absystech.fr>
    public function test_selectForDashBoard(){
        $this->initUserOnly(false);
        //suppression de toutes les interactions pour avoir des valeurs stables
        ATF::hotline_interaction()->q->reset()->addConditionNotNull("id_hotline_interaction");
        ATF::hotline_interaction()->delete();
        $id_societe=ATF::societe()->i(array('societe'=>'soc lol'));
        $id_hotline=$this->obj->i(array("id_societe"=>$id_societe,"hotline"=>"hot lol","etat"=>"fixing","id_user"=>$this->id_user));
        
        $result=$this->obj->selectForDashBoard(array("limit"=>10,"start"=>0));
        $this->assertEquals(1,ATF::$json->get("totalCount"),"Le nombre de ticket retourné est incorrecte");
        $this->assertEquals($id_hotline,$result[0]['id'],"Le ticket retourné est incorrecte");
    }
    
    // @author Nicolas BERTEMONT <nbertemont@absystech.fr>
    public function test_getTempsTotalNonResolu(){
        //suppression de toutes les interactions pour avoir des valeurs stables
        ATF::hotline_interaction()->q->reset()->addConditionNotNull("id_hotline_interaction");
        ATF::hotline_interaction()->delete();
        $id_societe=ATF::societe()->i(array('societe'=>'soc lol'));

        $id_hotline=$this->obj->i(array("id_societe"=>$id_societe,"hotline"=>"hot lol"));
        ATF::hotline_interaction()->i(array("id_hotline"=>$id_hotline,"temps"=>"07:00:00"));
        $this->assertEquals("1",$this->obj->getTempsTotalNonResolu(),"1/ Le temps retourné est incorrect");
        ATF::hotline_interaction()->i(array("id_hotline"=>$id_hotline,"temps"=>"14:00:00"));
        $this->assertEquals("3",$this->obj->getTempsTotalNonResolu(),"2/ Le temps retourné est incorrect");
    }
    
    // @author Jérémie Gwiazdowski <jgw@absystech.fr>
    public function test_alreadyBilled(){
        $this->initUser(false);
        $this->initHotline();
        ATF::$msg->getNotices();//Flush des notices
        
        $this->obj->update(array("id_hotline"=>$this->id_hotline,"facturation_ticket"=>"oui","etat"=>"annulee"));
        $this->assertTrue($this->obj->alreadyBilled($this->id_hotline));
        
        $this->obj->update(array("id_hotline"=>$this->id_hotline,"facturation_ticket"=>"oui","etat"=>"payee"));
        $this->assertTrue($this->obj->alreadyBilled($this->id_hotline));
        
        $this->obj->update(array("id_hotline"=>$this->id_hotline,"facturation_ticket"=>"non","etat"=>"payee"));
        $this->assertFalse($this->obj->alreadyBilled($this->id_hotline));
        
        $this->obj->update(array("id_hotline"=>$this->id_hotline,"facturation_ticket"=>"oui","etat"=>"fixing"));
        $this->assertFalse($this->obj->alreadyBilled($this->id_hotline));
    }
    
    // @author Nicolas BERTEMONT <nbertemont@absystech.fr>
    public function test_ListeHotline(){
        //réinitialisation pour éviter les conflits de données
        ATF::db()->truncate("hotline");
        
        //creation des hotlines de tests
        $this->initUserOnly(false);

        $id_societe=ATF::societe()->i(array('societe'=>'soc lol'));

        $id_hotline1=$this->obj->i(array("id_societe"=>$id_societe,"hotline"=>"hot lol 1","id_user"=>$this->id_user,"priorite"=>12));
        $id_hotline2=$this->obj->i(array("id_societe"=>$id_societe,"hotline"=>"hot lol 2","priorite"=>12));
        $id_hotline3=$this->obj->i(array("id_societe"=>$id_societe,"hotline"=>"hot lol 3","priorite"=>11));
        $id_hotline4=$this->obj->i(array("id_societe"=>$id_societe,"hotline"=>"hot lol 4","priorite"=>5));
        
        $liste=$this->obj->listeHotline(array('priorite'=>12));
        $this->assertEquals(5,count($liste[0]),"Il manque des priorités à afficher");
        $this->assertEquals(0,$liste[0]['10'],"Le nombre de priorité 10 est incorrecte");
        $this->assertEquals(1,$liste[0]['11'],"Le nombre de priorité 11 est incorrecte");
        $this->assertEquals(2,$liste[0]['12'],"Le nombre de priorité 12 est incorrecte");
        $this->assertEquals(0,$liste[0]['13'],"Le nombre de priorité 13 est incorrecte");
        $this->assertEquals(0,$liste[0]['14'],"Le nombre de priorité 14 est incorrecte");
        
        $liste_user=$this->obj->listeHotline(array('priorite'=>12,"user"=>$this->id_user));
        $this->assertEquals(5,count($liste_user[0]),"2/ Il manque des priorités à afficher");
        $this->assertEquals(0,$liste_user[0]['10'],"2/ Le nombre de priorité 10 est incorrecte");
        $this->assertEquals(0,$liste_user[0]['11'],"2/ Le nombre de priorité 11 est incorrecte");
        $this->assertEquals(1,$liste_user[0]['12'],"2/ Le nombre de priorité 12 est incorrecte");
        $this->assertEquals(0,$liste_user[0]['13'],"2/ Le nombre de priorité 13 est incorrecte");
        $this->assertEquals(0,$liste_user[0]['14'],"2/ Le nombre de priorité 14 est incorrecte");
        
    }

     //@author Yann GAUTHERON <ygautheron@absystech.fr>  
    public function test_getInteractionsForMobile(){
        $this->initUser(false);
        $this->initHotline();
        ATF::hotline_interaction()->q->reset()->where("id_hotline",$this->id_hotline);
        $this->id_hotline_interaction = ATF::hotline_interaction()->select_row();
        $r = $this->obj->rpcGetInteractionsForMobile(array('id'=>$this->id_hotline));
        $this->assertEquals($this->id_hotline,$r[0]["id_hotline"],"retour 1 incorrect");
        $this->assertEquals($this->id_hotline_interaction["id_hotline_interaction"],$r[0]["id_hotline_interaction"],"retour 2 incorrect");
        $this->assertNull($this->obj->rpcGetInteractionsForMobile(),"Devrait retourner NULL");
    }




       //@author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    public function test_statCleodis(){
        $date = $this->obj->getSemestre(12,2015);
        $titre = array("Garantie" ,"Facture" , "CM" );  
        $result = array(
            "Garantie" => array(1 => array("semestre" => "2015-01-01 au 2015-06-30", "duree" => 240 , "dureeH" => 4.00),
                                2 => array("semestre" => "2015-07-01 au 2015-12-31", "duree" => 780 , "dureeH" => 13.00)),
            "Facture" => array( 1 => array("semestre" => "2015-01-01 au 2015-06-30", "duree" => 19296 , "dureeH" => 321.60),
                                2 => array("semestre" => "2015-07-01 au 2015-12-31", "duree" => 11205 , "dureeH" => 186.75)),
            "CM" => array(      1 => array("semestre" => "2015-01-01 au 2015-06-30", "duree" => 3516 , "dureeH" => 58.60),
                                2=> array("semestre" =>  "2015-07-01 au 2015-12-31", "duree" => 1116 , "dureeH" => 18.60))
        );
                        
                       
        $result["titre"]= "Stats CLEODIS";
        $result["categories"]= $titre;
        $result["semestres"] = $date;
        
        $res = $this->obj->stats(true, "statCleodis" , array("month" => 12 , "year" => 2015));
        
        $this->assertEquals($result, $res , "1 -Error StatCleodis");
        $this->assertEquals($result["Garantie"][1], $res["Garantie"][1] , "2 - Error StatCleodis");
        $this->assertEquals($result["Garantie"][2]["semestre"], $res["Garantie"][2]["semestre"] , "3 - Error StatCleodis");
        
        $this->assertEquals($result["Facture"][1], $res["Facture"][1] , "5 - Error StatCleodis");
        $this->assertEquals($result["Facture"][2]["semestre"], $res["Facture"][2]["semestre"] , "6 - Error StatCleodis");
        
        $this->assertEquals($result["CM"][1], $res["CM"][1] , "8 - Error StatCleodis");
        $this->assertEquals($result["CM"][2]["semestre"], $res["CM"][2]["semestre"] , "9 - Error StatCleodis");
    }

    //@author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    public function test_getSemestre(){
        $mois = 2;
        $annee = date("Y");
        $annee2 = $annee-1;
        $res = $this->obj->getSemestre($mois,$annee);
        $doitAvoir = array( 0 => array(
                                "debut" => $annee2."-01-01",
                                "fin" => $annee2."-06-30"
                                      ),
                            1 => array(
                                "debut" => $annee2."-07-01",
                                "fin" => $annee2."-12-31"
                                      ),
                            2 => array(
                                "debut" => $annee."-01-01",
                                "fin" => $annee."-06-30"
                                      ),
                            "semestre" => array(
                                        0 => "S1 ".$annee2,
                                        1 => "S2 ".$annee2,
                                        2 => "S1 ".$annee
                                    ));
        $this->assertEquals($doitAvoir , $res , "1 - getSemestre ne renvoi pas les bonnes dates");  
        
        $mois = 9;
        $res = $this->obj->getSemestre($mois,$annee);
        $doitAvoir = array( 0 => array(
                                "debut" => $annee2."-07-01",
                                "fin" => $annee2."-12-31"
                                ),
                            1 => array(
                                "debut" => $annee."-01-01",
                                "fin" => $annee."-06-30"
                                      ),
                            2 => array(
                                "debut" => $annee."-07-01",
                                "fin" => $annee."-12-31"
                                      ),
                            "semestre" => array(
                                        0 => "S2 ".$annee2,
                                        1 => "S1 ".$annee,
                                        2 => "S2 ".$annee
                                    ));
        $this->assertEquals($doitAvoir , $res , "2 - getSemestre ne renvoi pas les bonnes dates");
    } 








    //@author Nicolas BERTEMONT <nbertemont@absystech.fr>
    public function test_statsTps(){
        $stat_tps_moyen=$this->obj->statsTps();

        $this->assertTrue(count($stat_tps_moyen['categories']['category'])>0,"Problème de récupération des catégories");
        $this->assertEquals("2015 January",$stat_tps_moyen['categories']['category']["2015 January"]["label"],"Les labels ont changé ?");
        $this->assertEquals("3.44",$stat_tps_moyen['dataset']['tps_moyen']['set']['2015 January']['value'],"Valeur incorrecte");
        $this->assertEquals("2015 February : 2.48",$stat_tps_moyen['dataset']['tps_moyen']['set']['2015 February']['titre'],"Titre incorrecte");

        $stat_par_user=$this->obj->statsTps(false,true);
        $this->assertEquals("2015 January",$stat_par_user['categories']['category']["2015 January"]["label"],"par user / Les labels ont changé ?");
        $this->assertEquals("2.06",$stat_par_user['dataset']['FLEURQUIN']['set']['2015 February']['value'],"par user / Valeur incorrecte");
        $this->assertEquals("FLEURQUIN : 2.06",$stat_par_user['dataset']['FLEURQUIN']['set']['2015 February']['titre'],"par user / Titre incorrecte");

        $stat_wid_user_clos=$this->obj->statsTps(true,true,true);        
        $this->assertEquals("YG",$stat_wid_user_clos['categories']['category']["GAUTHERON"]["label"],"wid_user_clos / Les labels ont changé ?");
        
        $this->assertGreaterThan(0,$stat_wid_user_clos['dataset']['dif']['set']['GAUTHERON']['value'],"wid_user_clos / Valeur incorrecte"); 
        
        $this->obj->i(array("id_societe"=>971,"etat"=>"done", "date_debut"=> date("Y-m-d" , strtotime("-2 day")), "hotline"=>"hot lol 1","id_user"=>1,"urgence"=>"detail","date"=>date("Y-m-d H:i:s"), "pole_concerne"=> "dev","wait_mep"=>"oui"));
        $stat_wid_user_nbre_clos=$this->obj->statsTps(true,true,true,true);
        
        
        
        $this->obj->q->reset()
                ->addField('count(*)','dif')
                ->setStrict()
                ->addJointure("hotline","id_user","user","id_user")
                ->addCondition("hotline.id_societe","1","AND",false,"!=")
                ->addCondition("hotline.etat","done")
                ->addCondition("hotline.etat","payee")
                ->addCondition("hotline.id_societe","1154","AND",false,"!=")
                ->addCondition("DATE_ADD(hotline.date, INTERVAL 30 DAY)","'".date("Y-m-d 00:00:00")."'",NULL,false,">=",false,false,true)
                ->addCondition("hotline.id_user",1);
        $result2=$this->obj->select_cell();
        $this->assertEquals($result2, $stat_wid_user_nbre_clos["dataset"]['dif']['set']['GAUTHERON']['value'],"wid_user_nbre_clos / Valeur incorrecte");
        $this->assertEquals("GAUTHERON : ".$result2,$stat_wid_user_nbre_clos["dataset"]['dif']['set']['GAUTHERON']['titre'],"wid_user_nbre_clos / Titre incorrecte");
    

    }   

    // @author Nicolas BERTEMONT <nbertemont@absystech.fr>
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
    
    // @author Nicolas BERTEMONT <nbertemont@absystech.fr>
    public function test_getUserActif(){
        $users=$this->obj->getUserActif();
        $this->assertTrue(count($users)>0,"La méthode ne renvoie pas les données");
        $this->assertEquals(1,$users[12],"La méthode ne renvoie pas les bonnes données");
        //on regarde qu'il a également pris en compte les users inactifs
        $this->assertFalse(isset($users[28]),"La méthode renvoie les users inactifs");
    }
    
    // @author Nicolas BERTEMONT <nbertemont@absystech.fr> 
    public function test_saExport(){
        $this->obj->q->reset()->where("hotline.etat","free");
        $res = $this->obj->saExport();
    }

    public function test_getTimeFactureCalcule(){

        $res = $this->obj->getTimeFactureCalcule(array("hotline.id_affaire_fk"=>9124));
        $this->assertEquals('0j 4h38', $res , "Erreur getTimeFactureCalcule");

    }


    //@author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    public function test_checkMailBox(){                        
        $mail = 'optima-hotline@absystech.fr';
        $host = "zimbra.absystech.net"; 
        $port = 143;
        $password = "az78qs45";         
        ATF::setSingleton("imap", new mockObjectCurlClassesHotline());
        try{
            $this->obj->checkMailBox("toto", $host, $port, $password);
        }catch (error $e) {
            $eMsg = $e->getMessage();
        }
        $this->assertEquals($eMsg, "error", "L'erreur catch n'est pas bonne !!");       
        $this->assertEquals(true,$this->obj->checkMailBox($mail, $host, $port, $password), "Erreur lors de la lecture du mail !!");
        
        ATF::hotline_interaction()->q->reset()->setLimit(1);
        $res = ATF::hotline_interaction()->select_row();
        $attendu = "toto dans un tu \n \n  <---- Pour répondre par email, écrire au-dessus de cette ligne ----> tototototoot";  
    
        $this->assertEquals($res["detail"],$attendu, "Erreur lors du detail de l'inter !!");    
        
        //Test notices
        $notices=ATF::$msg->getNotices();
        $this->assertTrue(is_array($notices),"assert 1");
        $this->assertEquals(4,count($notices),"assert 2");              
        ATF::unsetSingleton("imap");
    }

    public function test_checkMailBox2(){                  
        $mail = 'optima-hotline@absystech.fr';
        $host = "zimbra.absystech.net"; 
        $port = 143;
        $password = "az78qs45";         
        ATF::setSingleton("imap", new mockObjectCurlClassesHotline2()); 
        $this->assertEquals(true,$this->obj->checkMailBox($mail, $host, $port, $password), "Erreur lors de la lecture du mail !!");    
           
        ATF::hotline_interaction()->q->reset()->setLimit(1);
        $res = ATF::hotline_interaction()->select_row();
        $attendu = "toto dans un tu \n \n  <---- Pour répondre par email, écrire au-dessus de cette ligne ----> tototototoot";  
        
        $this->assertEquals($res["detail"],$attendu, "Erreur lors du detail de l'inter !!");    
        $this->assertEquals($res["id_user"],55, "Erreur de user de l'inter !!");
      
        //Test notices
        $notices=ATF::$msg->getNotices();
        $this->assertTrue(is_array($notices),"assert 1");
        $this->assertEquals(5,count($notices),"assert 2");              
        ATF::unsetSingleton("imap");
    }

    public function test_checkMailBox3(){                  
        $mail = 'optima-hotline@absystech.fr';
        $host = "zimbra.absystech.net"; 
        $port = 143;
        $password = "az78qs45";         
        ATF::setSingleton("imap", new mockObjectCurlClassesHotline3()); 
        $this->assertEquals(true,$this->obj->checkMailBox($mail, $host, $port, $password), "Erreur lors de la lecture du mail !!");    
           
       
        //Test notices
        $notices=ATF::$msg->getNotices();                     
        ATF::unsetSingleton("imap");
    }





    public function test_checkMailBox4(){                  
        $mail = 'optima-hotline@absystech.fr';
        $host = "zimbra.absystech.net"; 
        $port = 143;
        $password = "az78qs45";                 
        ATF::setSingleton("imap", new mockObjectCurlClassesHotline4()); 
        $this->assertEquals(true,$this->obj->checkMailBox($mail, $host, $port, $password), "Erreur lors de la lecture du mail !!");    
        
        ATF::hotline_interaction()->q->reset()->setLimit(1);
        $res = ATF::hotline_interaction()->select_row();          
        
        $this->assertEquals($res["detail"],"Requête créée par class: unitaire", "Erreur lors du detail de l'inter !!");

        //Test notices
        $notices=ATF::$msg->getNotices();             
        ATF::unsetSingleton("imap");        
    }

    public function test_setbillingModeNewAffaire() {
        $this->initUser(false);
        $this->initHotline();
        $this->initAffaire();
        $infos = array("id_hotline"=>$this->id_hotline,"charge"=>"rd","type_requete"=>"affaire","id_affaire"=>$this->id_affaire);

        $r = $this->obj->setbillingModeNew($infos);

        $this->assertTrue($r,"Erreur  de retour");

        $h = $this->obj->select($this->id_hotline);

        $this->assertEquals("non",$h['facturation_ticket'],"Erreur dans le facturation_ticket setté");
        $this->assertEquals("fixing",$h['etat'],"Erreur dans le etat setté");
        $this->assertNull($h['ok_facturation'],"Erreur dans le ok_facturation setté");
        $this->assertEquals($this->id_affaire,$h['id_affaire'],"Erreur dans le id_affaire setté");


    }

    public function test_getBillingModeMaintenance() {
        $this->initUser(false);
        $this->initHotline();
        $this->initAffaire();
        $infos = array("id_hotline"=>$this->id_hotline,"charge"=>"maintenance","type_requete"=>"charge_absystech","id_affaire"=>$this->id_affaire);

        $this->obj->setbillingModeNew($infos);
        $r = $this->obj->getbillingMode($infos,true);

        $this->assertEquals("Contrat de maintenance ou Garantie Tu_devis",$r,"Erreur  de retour");


    }    
    public function test_getBillingModeInter() {
        $this->initUser(false);
        $this->initHotline();
        $this->initAffaire();
        $infos = array("id_hotline"=>$this->id_hotline,"charge"=>"intervention","type_requete"=>"charge_client","facturation_ticket"=>"oui");

        $this->obj->setbillingModeNew($infos);
        $r = $this->obj->getbillingMode($this->id_hotline,true);

        $this->assertEquals("Intervention Facturé(e) au ticket",$r,"Erreur  de retour 1");

        $infos = array("id_hotline"=>$this->id_hotline,"charge"=>"intervention","type_requete"=>"charge_absystech","facturation_ticket"=>"non");

        $this->obj->setbillingModeNew($infos);
        $r = $this->obj->getbillingMode($this->id_hotline,true);

        $this->assertEquals("Intervention Non facturé",$r,"Erreur  de retour 2");


    }   
   
 
    public function test_dl() {
        //init
        $this->initUser(false);
        $this->initHotline();
        $t = $this->obj->filepath($this->id_hotline,"dldoc");
        file_put_contents($t,"YALLAH !");

        //lancer l'initialisation de la récupération du fichier
        ob_start();
        
        $this->obj->dl(array('field'=>'dldoc','id_hotline'=>$this->id_hotline,"type"=>"txt"));
        $contenu=ob_get_clean();
        //récupération des infos
        $this->assertTrue(strlen($contenu)>0,'Récupération du pdf du devis créé échoué');

        ob_start();
        $this->obj->dl(array('field'=>'dldfgdsfgdoc','id_hotline'=>$this->id_hotline));
        $contenu=ob_get_clean();
    }
 
    public function test_isThereMEPTicket() {
        $q = "UPDATE hotline SET wait_mep='non'";
        ATF::db()->query($q);
        $this->assertEquals("false",$this->obj->isThereMEPTicket(),"Il ne devrait pas y avoir de ticket a mettre en production");

        $q = "UPDATE hotline SET wait_mep='oui'";
        ATF::db()->query($q);

        $this->assertTrue($this->obj->isThereMEPTicket(),"Il ne devrait  y avoir QUE des tickets a mettre en production");
    }
    
    public function test_getMEPTicket() {

        $q = "UPDATE hotline SET wait_mep='oui'";
        ATF::db()->query($q);

        $this->obj->q->reset()->setCountOnly();
        $this->assertEquals($this->obj->sa(),count($this->obj->getMEPTicket()),"Il n'y a pas le même nombre de ticket a mettre en production que le nobre total de ticket.");
    }

    public function test_massValidMEP() {
        $q = "UPDATE hotline SET wait_mep='non'";
        ATF::db()->query($q);
        $this->obj->q->reset()->setLimit(3);

        foreach ($this->obj->sa() as $k=>$i) {
            $infos['th'][$i['id_hotline']] = true;
        }
        
        $this->obj->massValidMEP($infos);


        foreach ($this->obj->sa() as $k=>$i) {
            $this->assertEquals("non",$i['wait_mep'],"Les tickets ne sont pas passés en mise en production");
        }
    }

    public function test_acceptationAutomatique(){        
        $this->initUser(false);
        $this->initHotline("alt_id_user");
        $this->initInteractions();        
        $this->obj->setBillingMode(array("id_hotline"=>$this->id_hotline
                                        ,"id_user"=>$this->alt_id_user
                                        ,"type_requete"=>"charge_client"
                                        ,"refresh"=>true
                                        ,"send_mail"=>true
                                        ,"charge"=>"intervention"
                                        ,"date"=>"2015-01-01 09:42:10"));
        $this->obj->u(array("id_hotline"=>$this->id_hotline, "date"=>"2015-01-01 09:42:10"));

        $hotline=$this->obj->select($this->id_hotline);
        
        ATF::hotline_interaction()->q->reset()->where("id_hotline",$this->id_hotline)->where("detail", 'Choix de la facturation "Charge Client"%',"AND", false, "LIKE")->addOrder("date", "desc");
        $facturation = ATF::hotline_interaction()->select_row();

        ATF::hotline_interaction()->u(array("id_hotline_interaction"=>$facturation["id_hotline_interaction"] , "date"=>"2015-01-01 09:42:10"));     
     
        $this->obj->acceptationAutomatique();

        $this->assertEquals("oui",$this->obj->select($this->id_hotline, "ok_facturation"),"La hotline ne s'est pas acceptée??");
        
        ATF::$msg->getNotices();//Flush des notices 
    }
   

    

};


//@author Morgan FLEURQUIN <mfleurquin@absystech.fr>
class mockObjectCurlClassesHotline extends imap {
    public function init($host, $port, $mail, $password){
        if ($mail !=="optima-hotline@absystech.fr") {
            $this->error = "error";
        } else {
            $this->error = NULL;    
        }
    }   
        
    public function imap_fetch_overview($type){
        $user=array(
            "login"=>"altTutul"
            ,"password" => "toto"
            ,"id_societe" => 1
            ,"prenom" => "toto"
            ,"nom" => "tata"
            ,"email" => "toto@absystech.fr"         
        );
        $this->id_user = ATF::user()->insert($user);
        
        $contact=array(
            "nom"=>"altTutul"
            ,"prenom" => "toto"
            ,"id_societe" => 1
            ,"email" => "toto@absystech.fr"         
        );
        $this->id_contact = ATF::contact()->insert($contact);

        $hotline = array(
            "hotline"=>"HotlineTuTest"
            ,"id_societe"=>1
            ,"detail"=>"HotlineTuTest"
            ,"date_debut"=>date('Y-m-d')
            ,"id_contact"=>$this->id_contact
            ,"id_user"=>$this->id_user
            ,"visible"=>"oui"
            ,"pole_concerne"=>"dev"
            ,"urgence"=>'detail'
            ,'charge'=>"intervention" 
        );
        $this->id_hotline = ATF::hotline()->insert($hotline);
        
        $tmp = array(0=>new stdClass());
        $tmp[0]->uid = 0;
        $tmp[0]->subject = "Un sujet de mail";
        $tmp[0]->date = "2009-12-10 12:00:00";
        $tmp[0]->to = "Hotline AbsysTech <optima-hotline-".ATF::$codename."-".ATF::hotline()->cryptId($this->id_hotline)."-".ATF::contact()->cryptId($this->id_contact)."@absystech-speedmail.com>";;
        
        return $tmp;
    }
    
    public function imap_delete($uid) {
    }
    public function imap_expunge() {
    }
    
    public function returnBodyStr($uid,$section=1){
        $message = "toto dans un tu \n \n  <---- Pour répondre par email, écrire au-dessus de cette ligne ----> tototototoot";          
        return utf8_encode($message);
    }
    
    public function returnBody($uid,$section=1){
        $message = "toto dans un tu \n \n  <---- Pour répondre par email, écrire au-dessus de cette ligne ----> tototototoot";          
        return $message;
    }
};

class mockObjectCurlClassesHotline2 extends imap {
    public function init($host, $port, $mail, $password){
        if ($mail !=="optima-hotline@absystech.fr") {
            $this->error = "error";
        } else {
            $this->error = NULL;    
        }
    }   
        
    public function imap_fetch_overview($type){
        $user=array(
            "login"=>"altTutul"
            ,"password" => "toto"
            ,"id_societe" => 1
            ,"prenom" => "toto"
            ,"nom" => "tata"
            ,"email" => "toto@absystech.fr"         
        );
        $this->id_user = ATF::user()->insert($user);
        
        $contact=array(
            "nom"=>"altTutul"
            ,"prenom" => "toto"
            ,"id_societe" => 1
            ,"email" => "toto@absystech.fr"         
        );
        $this->id_contact = ATF::contact()->insert($contact);

        $hotline = array(
            "hotline"=>"HotlineTuTest"
            ,"id_societe"=>1
            ,"detail"=>"HotlineTuTest"
            ,"date_debut"=>date('Y-m-d')
            ,"id_contact"=>$this->id_contact
            ,"id_user"=>$this->id_user
            ,"visible"=>"oui"
            ,"pole_concerne"=>"dev"
            ,"urgence"=>'detail'
            ,'charge'=>"intervention" 
        );
        $this->id_hotline = ATF::hotline()->insert($hotline);
        
        $tmp = array(0=>new stdClass());
        $tmp[0]->uid = 0;
        $tmp[0]->subject = "Un sujet de mail";
        $tmp[0]->date = "2009-12-10 12:00:00";
        $tmp[0]->to = "Hotline AbsysTech <optima-hotline-".ATF::$codename."-".ATF::hotline()->cryptId($this->id_hotline)."-".ATF::contact()->cryptId($this->id_contact)."@absystech-speedmail.com>";;
        $tmp[0]->from = "<mfleurquin@absystech.fr>";
        
        return $tmp;
    }
    
    public function imap_delete($uid) {
    }
    public function imap_expunge() {
    }
    
    public function returnBodyStr($uid,$section=1){
        $message = "toto dans un tu \n \n  <---- Pour répondre par email, écrire au-dessus de cette ligne ----> tototototoot";          
        return utf8_encode($message);
    }
    
    public function returnBody($uid,$section=1){
        $message = "toto dans un tu \n \n  <---- Pour répondre par email, écrire au-dessus de cette ligne ----> tototototoot";          
        return $message;
    }


};

class mockObjectCurlClassesHotline3 extends imap {
    public function init($host, $port, $mail, $password){
        if ($mail !=="optima-hotline@absystech.fr") {
            $this->error = "error";
        } else {
            $this->error = NULL;    
        }
    }   
        
    public function imap_fetch_overview($type){
        $user=array(
            "login"=>"altTutul"
            ,"password" => "toto"
            ,"id_societe" => 1
            ,"prenom" => "toto"
            ,"nom" => "tata"
            ,"email" => "toto@absystech.fr"         
        );
        $this->id_user = ATF::user()->insert($user);
        
        $contact=array(
            "nom"=>"altTutul"
            ,"prenom" => "toto"
            ,"id_societe" => 1
            ,"email" => "toto@absystech.fr"         
        );
        $this->id_contact = ATF::contact()->insert($contact);

        $hotline = array(
            "hotline"=>"HotlineTuTest"
            ,"id_societe"=>1
            ,"detail"=>"HotlineTuTest"
            ,"date_debut"=>date('Y-m-d')
            ,"id_contact"=>$this->id_contact
            ,"id_user"=>$this->id_user
            ,"visible"=>"oui"
            ,"etat"=>"payee"
            ,"pole_concerne"=>"dev"
            ,"urgence"=>'detail'
            ,'charge'=>"intervention" 
        );
        $this->id_hotline = ATF::hotline()->insert($hotline);
        
        $tmp = array(0=>new stdClass());
        $tmp[0]->uid = 0;
        $tmp[0]->subject = "Un sujet de mail";
        $tmp[0]->date = "2009-12-10 12:00:00";
        $tmp[0]->to = "Hotline AbsysTech <optima-hotline-".ATF::$codename."-".ATF::hotline()->cryptId($this->id_hotline)."-".ATF::contact()->cryptId($this->id_contact)."@absystech-speedmail.com>";;
        $tmp[0]->from = "<mfleurquin@absystech.fr>";
        
        return $tmp;
    }
    
    public function imap_delete($uid) {
    }
    public function imap_expunge() {
    }
    
    public function returnBodyStr($uid,$section=1){
        $message = "toto dans un tu \n \n  <---- Pour répondre par email, écrire au-dessus de cette ligne ----> tototototoot";          
        return utf8_encode($message);
    }
    
    public function returnBody($uid,$section=1){
        $message = "toto dans un tu \n \n  <---- Pour répondre par email, écrire au-dessus de cette ligne ----> tototototoot";          
        return $message;
    }


};

//@author Morgan FLEURQUIN <mfleurquin@absystech.fr>
class mockObjectCurlClassesHotline4 extends imap {
    public function init($host, $port, $mail, $password){
        if ($mail !=="optima-hotline@absystech.fr") {
            $this->error = "error";
        } else {
            $this->error = NULL;    
        }
    }   
        
    public function imap_fetch_overview($type){
        $user=array(
            "login"=>"altTutul"
            ,"password" => "toto"
            ,"id_societe" => 1
            ,"prenom" => "toto"
            ,"nom" => "tata"
            ,"email" => "toto@absystech.fr"         
        );
        $this->id_user = ATF::user()->insert($user);
        
        $contact=array(
            "nom"=>"altTutul"
            ,"prenom" => "toto"
            ,"id_societe" => 1
            ,"email" => "toto@absystech.fr"         
        );
        $this->id_contact = ATF::contact()->insert($contact);

        $hotline = array(
            "hotline"=>"HotlineTuTest"
            ,"id_societe"=>1
            ,"detail"=>"HotlineTuTest"
            ,"date_debut"=>date('Y-m-d')
            ,"id_contact"=>$this->id_contact
            ,"id_user"=>$this->id_user
            ,"visible"=>"oui"
            ,"pole_concerne"=>"dev"
            ,"urgence"=>'detail'
            ,'charge'=>"intervention"            
        );
        $this->id_hotline = ATF::hotline()->insert($hotline);
        ATF::hotline()->u(array("id_hotline" => $this->id_hotline, "etat"=>"payee"));

        $tmp = array(0=>new stdClass());
        $tmp[0]->uid = 0;
        $tmp[0]->subject = "Un sujet de mail";
        $tmp[0]->date = "2009-12-10 12:00:00";
        $tmp[0]->to = "Hotline AbsysTech <optima-hotline-".ATF::$codename."-".ATF::hotline()->cryptId($this->id_hotline)."-".ATF::contact()->cryptId($this->id_contact)."@absystech-speedmail.com>";;
        $tmp[0]->from = "debug@absysTech.fr";
        return $tmp;
    }
    
    public function imap_delete($uid) {
    }
    public function imap_expunge() {
    }
    
    public function returnBodyStr($uid,$section=1){
        $message = "toto dans un tu \n \n  <---- Pour répondre par email, écrire au-dessus de cette ligne ----> tototototoot";          
        return utf8_encode($message);
    }
    
    public function returnBody($uid,$section=1){
        $message = "toto dans un tu \n \n  <---- Pour répondre par email, écrire au-dessus de cette ligne ----> tototototoot";          
        return $message;
    }




};


class hotlineTU extends hotline {
	public function __construct() {
		parent::__construct();
		$this->memory_optimisation_select=true;
	}
}

class hotlineTUbis extends hotline {	
	public function update($infos){
		echo "Exception";
		throw new errorATF();
	}
}

