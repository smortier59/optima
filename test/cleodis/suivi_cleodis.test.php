<?
class suivi_cleodis_test extends ATF_PHPUnit_Framework_TestCase {
	public function setUp() {
		ATF::db()->begin_transaction(true);
	}
	
	/** Méthode post-test, exécute après chaque test unitaire*/
	
	public function tearDown(){
		ATF::$msg->getNotices();
		ATF::db()->rollback_transaction(true);
	}

	//méthode pour créer un environnement de test
	public function creerSocCon(){
		//creation d'une societe
		$this->id_societe=ATF::societe()->i(array("societe"=>"soc lol"));
		$this->assertTrue(is_numeric($this->id_societe),"La société ne s'est pas créée");
		//création d'un contact
		$this->id_contact=ATF::contact()->i(array("nom"=>"tu lol","email"=>"clol@lol.fr",'id_societe'=>$this->id_societe));
		$this->assertTrue(is_numeric($this->id_contact),"Le contact ne s'est pas créé");
	}

	public function testInsert(){
		$this->initUserOnly(false);		
		$this->creerSocCon();

		$this->id_user=ATF::user()->i(array("login"=>"log lol","password"=>"lol","civilite"=>"M","prenom"=>"lol","nom"=>"lol","email"=>"lol@lol.fr"));
		$this->assertTrue(is_numeric($this->id_user),"Le user ne s'est pas créé");

		//insertion d'un suivi
		$infos=array('suivi'=>array("id_societe" => $this->id_societe,
									"date" => "07-03-2014 09:52",
									"type_suivi" => "Autre",
									"texte" => "text de test du suivi",
									"type" => "appel"
			));
		$cr="test";
		$this->id_suivi=$this->obj->insert($infos,$s,false,$cr);
		//pour éviter les problèmes dans le cas où quelqu'un touche au querier
		$this->assertTrue(is_numeric($this->id_suivi),"Insertion d'un suivi échoué"); 


		$infos=array("id_societe" => $this->id_societe,
									"date" => "07-03-2014 09:52",
									"type_suivi" => "Autre",
									"texte" => "text de test du suivi",
									"type" => "appel"
			);
		$cr="test";
		$this->id_suivi=$this->obj->insert($infos,$s,false,$cr);
		//pour éviter les problèmes dans le cas où quelqu'un touche au querier
		$this->assertTrue(is_numeric($this->id_suivi),"Insertion d'un suivi échoué"); 
	}

	public function test_update(){		
		$this->initUserOnly(false);		
		$this->creerSocCon();

		$this->id_user=ATF::user()->i(array("login"=>"log lol","password"=>"lol","civilite"=>"M","prenom"=>"lol","nom"=>"lol","email"=>"lol@lol.fr"));
		$this->assertTrue(is_numeric($this->id_user),"Le user ne s'est pas créé");

		//insertion d'un suivi
		$infos=array('suivi'=>array("id_societe" => $this->id_societe,
									"date" => "07-03-2014 09:52",
									"type_suivi" => "Autre",
									"texte" => "text de test du suivi",
									"type" => "appel"
			));
		$cr="test";
		$this->id_suivi=$this->obj->insert($infos,$s,false,$cr);
		//pour éviter les problèmes dans le cas où quelqu'un touche au querier
		$this->assertTrue(is_numeric($this->id_suivi),"Insertion d'un suivi échoué"); 

		$data = array("id_suivi"=> $this->id_suivi, "texte"=>"Le texte du suivi modifié");

		$this->obj->update($data);

		$this->assertEquals("Le texte du suivi modifié" , $this->obj->select($this->id_suivi , "texte"), "Le texte ne s'est pas mis à jour !");
	}


	// @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	public function test_stats_special(){
		$annee = 2014;
		$id_user = 94;
		$this->obj->stats_filtre = array("suivi_type");
		$this->obj->suivi_type = array("note" => 1,
									    "fichier" => 0,
									    "RDV" => 1,
									    "appel" => 0,
									    "courrier" => 1
									);

		$res = $this->obj->stats_special($annee,$id_user,"");
		$this->assertEquals(185, $res["dataset"][94]["set"]["12"]["value"] , "Retour stat 1 incorrect");
		$this->assertEquals(192, $res["dataset"][94]["set"]["11"]["value"] , "Retour stat 2 incorrect");
		$this->assertEquals("Margaux Favier : 185", $res["dataset"][94]["set"]["12"]["titre"] , "Retour stat 3 incorrect");

	}

	// @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	public function test_changeUser(){
		$this->obj->liste_user = array("16"=>1);
        $this->assertTrue(is_array($this->obj->liste_user) && count($this->obj->liste_user)>0,"1/ Problème sur l'initialisation du liste_user");    
        $this->assertEquals(1,$this->obj->liste_user[16],"2/ Problème sur l'initialisation du liste_user"); 
        $this->obj->changeUser(array('tabuser'=>array(0=>16)));
        $this->assertTrue(is_array($this->obj->liste_user) && count($this->obj->liste_user)>0,"1/ Problème sur la modification du liste_user");     
        $this->assertEquals(0,$this->obj->liste_user[12],"2/ Problème sur l'initialisation du liste_user");    
    }
    
    // @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    public function test_getUserActif(){
        $users=$this->obj->getUserActif();
        $this->assertTrue(count($users)>0,"La méthode ne renvoie pas les données");
        $this->assertEquals("M Jérôme LOISON",$users[16],"La méthode ne renvoie pas les bonnes données");
        //on regarde qu'il a également pris en compte les users inactifs
        $this->assertFalse(isset($users[28]),"La méthode renvoie les users inactifs");
    }

    // @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    public function test_get_annee(){
        $annees=$this->obj->get_annee();
        $this->assertTrue(count($annees)>0,"La méthode ne renvoie pas les données");

        $this->assertEquals(2015,$annees[2015],"La méthode ne renvoie pas les bonnes données");
        $this->assertEquals(2014,$annees[2014],"La méthode ne renvoie pas les bonnes données 2");
    }

    // @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    public function test_modifEtat(){
        $this->obj->suivi_type = array("note" => 1,
									    "fichier" => 1,
									    "RDV" => 1,
									    "appel" => 1,
									    "courrier" => 1
									);

        $this->obj->modifEtat("suivi_type","fichier",false);
        $this->assertEquals(0 , $this->obj->suivi_type["fichier"] , "modifEtat erreur");        
    }





}