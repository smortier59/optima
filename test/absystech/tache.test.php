<?
class tache_test extends ATF_PHPUnit_Framework_TestCase {

	/* @author Quentin JANON <qjanon@absystech.fr> */
	public function setUp() {
		$this->initUser();
	}

	/* @author Quentin JANON <qjanon@absystech.fr> */
	public function tearDown() {
		ATF::db()->rollback_transaction(true);
	}

	/* @author Quentin JANON <qjanon@absystech.fr> *//* @author Quentin JANON <qjanon@absystech.fr> */
	public function testSelect_all() {
		ATF::tache()->truncate();
		$tache = array(
			array("etat"=>"fini","tache"=>"tache","horaire_debut"=>"2050-01-01","horaire_fin"=>"2050-01-02","id_user"=>ATF::$usr->getId())
			,array("tache"=>"tache","horaire_debut"=>"2050-01-01","horaire_fin"=>"2050-01-02","id_user"=>ATF::$usr->getId())
		);

		foreach ($tache as $k=>$i) {
			$tache[$k]['id_tache'] = $this->obj->i($i);
		}

		$this->obj->q->setCount();
		$r=$this->obj->select_all();
		$liste_tache = $r['data'];
		$this->assertFalse(empty($liste_tache),"Aucune donnée à afficher");
		// test des addFields ajoutés
		$this->assertTrue(array_key_exists('horaire_fin',$liste_tache[0]),"la colonne horaire fin n'existe pas");
		$this->assertTrue(array_key_exists('tache.etat',$liste_tache[0]),"la colonne etat n'existe pas");
		$this->assertTrue(array_key_exists('tache.type',$liste_tache[0]),"la colonne type n'existe pas");
	
		//$this->assertTrue($liste_tache[0]['allowValid'],"Erreur, on devrait pouvoir modifier cette tache");
		//$this->assertFalse($liste_tache[1]['allowValid'],"Erreur, on ne devrait pas pouvoir modifier cette tache");
	
	}
	/* @author Quentin JANON <qjanon@absystech.fr> */
	public function testUpdate_complete(){
		$this->initUserOnly(false);
		$id_tache=$this->obj->i(array('id_user'=>$this->id_user,'tache'=>'lol','horaire_debut'=>'2010-07-26 10:10:00','horaire_fin'=>'2010-07-26 12:10:00'));

		$this->obj->update_complete(array('id_tache'=>$id_tache,'complete'=>40));
		$this->assertEquals($this->obj->select($id_tache,'complete'),40,"Méthode update_complete non fonctionnel");
	}
	/* @author Quentin JANON <qjanon@absystech.fr> */
	public function testInsert(){
		ATF::$msg->getNotices(); // Vider des notices existantes
		
		//pour tester l'ajout des taches_user
		$this->initUserOnly(false);
		$id_user2=ATF::user()->i(array(
			"login"=>"tutul lol"
			,"password"=>"tu"
			,"civilite"=>"M"
			,"prenom"=>"class:".get_class($this)
			,"nom"=>"unitaire lol"
			,"pole"=>"dev"
			,"id_agence"=>$this->id_agence
			,"email"=>"debug@absystech.fr"
			,'custom'=>serialize(array('preference'=>array('langue'=>'fr')))
		));
		
		//pour tester l'ajout de la societe
		$id_soc=ATF::societe()->i(array('societe'=>'soc lol'));
		$id_suivi=ATF::suivi()->i(array("id_user"=>$this->id_user,"id_societe"=>$id_soc,"texte"=>"MDR"));
		
		$infos=array('tache'=>array("id_suivi"=>$id_suivi,"tache"=>"tac lol","horaire_fin"=>"2010-07-26 10:10:00"));
		$infos['dest']=$this->id_user.",".$id_user2;
		
		$id_tache=$this->obj->insert($infos);
		
		$this->assertTrue(is_numeric($id_tache),"N'a pas retourné l'id_tache ou cette dernière ne s'est pas créée");
		//vérification de ce qui a été inséré
		$donnees=$this->obj->select($id_tache);
		$this->assertEquals($id_soc,$donnees['id_societe'],'La société n a pas ete attribuee');
		$this->assertEquals($this->id_user,$donnees['id_user'],'Le créateur de la tache n a pas ete cree');
		ATF::tache_user()->q->reset()->addCondition('id_tache',$id_tache);
		$tac_user=ATF::tache_user()->select_all();
		$this->assertTrue(count($tac_user)==2,"Tous les tache_user n'ont pas ete insere");
		//redirection
		$ref=ATF::$cr->getCrefresh('main');
		$this->assertEquals($id_suivi,$ref['vars']['requests']['suivi']['id_suivi'],'La redirection sur suivi n est pas correcte');
		//notice
		$notices=ATF::$msg->getNotices();
		$this->assertTrue(is_array($notices),'La notice n a pas ete executee');
		$this->assertEquals(ATF::$usr->trans("email_envoye","tache"),$notices[0]['msg'],'Le contenu de la notice n est pas bonne');
		
		ATF::$cr->reset();
		//test si il n'y a pas de suivi et qu'il n'y a pas de mail disponible
		$id_user3=ATF::user()->i(array(
			"login"=>"tutul lol 2"
			,"password"=>"tu"
			,"civilite"=>"M"
			,"prenom"=>"class:".get_class($this)
			,"nom"=>"unitaire lol 2"
		));
		$id_tache2=$this->obj->insert(array('tache'=>array("id_user"=>$id_user3,"tache"=>"tac lol 2","horaire_fin"=>"2010-07-26 10:10:00")));
		
		$this->assertTrue(is_numeric($id_tache2),"N'a pas retourné l'id_tache 2 ou cette dernière ne s'est pas créée");
		//un notice devrait s'afficher concernant les emails
		$notices=ATF::$msg->getNotices();
		$this->assertTrue(is_array($notices),'La notice n a pas ete executee');
		$this->assertEquals($notices[0]['msg'],"Aucune adresse mail disponible",'Le contenu de la notice n est pas bonne');
		$ref2=ATF::$cr->getCrefresh('main');
		$this->assertEquals($id_tache2,$ref2['vars']['requests']['tache']['id_tache'],'La redirection sur tache n est pas correcte');
		
		//gestion d'erreur du dest
		$infos=array('tache'=>array("id_suivi"=>$id_suivi,"tache"=>"tac lol2","horaire_fin"=>"2010-07-26 10:10:00"));
		$infos['dest']="lol";
		
		try{
			$this->obj->insert($infos);
			$this->assertTrue(false,"Une erreur aurait du être générée");
		}catch(errorATF $e){
			//c'est bon
		}
	}
	/* @author Quentin JANON <qjanon@absystech.fr> */
	public function testUpdate(){
		$this->initUserOnly(false);
		//création d'une tache a modifier
		$id_user2=ATF::user()->i(array(
			"login"=>"tutul lol 2"
			,"password"=>"tu"
			,"civilite"=>"M"
			,"prenom"=>"class:".get_class($this)
			,"nom"=>"unitaire lol 2"
		));
		$id_user3=ATF::user()->i(array(
			"login"=>"tutul lol 3"
			,"password"=>"tu"
			,"civilite"=>"M"
			,"prenom"=>"class:".get_class($this)
			,"nom"=>"unitaire lol 3"
		));
		$id_tache=$this->obj->insert(array('tache'=>array("tache"=>"tac lol","horaire_fin"=>"2010-07-26 10:10:00"),"dest"=>$this->id_user.",".$id_user2));
		//notice
		$notices=ATF::$msg->getNotices();
		$this->assertTrue(is_array($notices),'La notice n a pas ete executee');
		$this->assertEquals(ATF::$usr->trans("email_envoye","tache"),$notices[0]['msg'],'Le contenu de la notice n est pas bonne');
		
		$infos=array('tache'=>array('id_tache'=>$id_tache,'tache'=>"tache lol"),'dest'=>$id_user2.",".$id_user3);
		$this->obj->update($infos);
		$donnees=$this->obj->select($id_tache);

		$this->assertEquals("tache lol",$donnees['tache'],"Le nom de la tache n'a pas ete modifie");
		$this->assertTrue(count($donnees['dest'])==2,'Le nombre de destinataire n est pas correct');
		$dest=array_flip($donnees['dest']);
		$this->assertTrue((isset($dest[$id_user2]) && isset($dest[$id_user3])),'Les destinataires ne sont pas corrects');
		$ref=ATF::$cr->getCrefresh('main');
		$this->assertEquals($id_tache,$ref['vars']['requests']['tache']['id_tache'],'La redirection sur tache n est pas correcte');
		
		
		//redirection sur suivi
		ATF::$cr->reset();
		$id_soc=ATF::societe()->i(array('societe'=>'soc lol'));
		$id_suivi=ATF::suivi()->i(array("id_user"=>$this->id_user,"id_societe"=>$id_soc,"texte"=>"MDR"));
		$infos2=array('tache'=>array('id_tache'=>$id_tache,'id_suivi'=>$id_suivi));
		$this->obj->update($infos2);
		
		$donnees2=$this->obj->select($id_tache);
		$this->assertEquals($id_suivi,$donnees2['id_suivi'],"Le suivi n'a pas ete modifie");
		$ref=ATF::$cr->getCrefresh('main');
		$this->assertEquals($id_suivi,$ref['vars']['requests']['suivi']['id_suivi'],'La redirection sur suivi n est pas correcte');
		
		//gestion d'erreur du dest
		$infos=array('tache'=>array('id_tache'=>$id_tache,'tache'=>"tache lol2"),'dest'=>"lol");
		
		try{
			$this->obj->update($infos);
			$this->assertTrue(false,"Une erreur aurait du être générée");
		}catch(errorATF $e){
			//c'est bon
		}
	}
	
	//test sur infos_dest deja fait avec select (voir test sur update)
	
	//test sur select deja fait avec un champs et sans, voir testUpdate_complete et testUpdate
		/* @author Quentin JANON <qjanon@absystech.fr> */
	public function testValid(){
		ATF::$msg->getNotices();
		$this->initUserOnly(false);


		$id_tache=$this->obj->insert(array('tache'=>array("tache"=>"tac lol","horaire_fin"=>"2010-07-26 10:10:00")));
		$this->assertEquals($id_tache,$this->obj->valid(array('id_tache'=>$id_tache)),"Le retour n'est pas bon");
		$this->assertEquals(100,$this->obj->select($id_tache,'complete'),"La tache n'a pas ete mise a jour");
		//notice
		$notices=ATF::$msg->getNotices();
		$this->assertTrue(is_array($notices),'La notice n a pas ete executee');
		$this->assertEquals(ATF::$usr->trans("email_envoye","tache"),$notices[0]['msg'],'Le contenu de la notice n est pas bonne');
	}	


	/* @author Morgan FLEURQUIN <mfleurquin@absystech.fr> */
	public function testValidPeriodique(){
		$this->initUserOnly(false);
		$t = new tache_absystech();
		$id_tache=$t->insert(array('tache'=>array("tache"=>"tac hebdomadaire","horaire_fin"=>"2010-07-26 10:10:00", "periodique"=>"hebdomadaire")));
		$this->assertEquals($id_tache,$t->valid(array('id_tache'=>$id_tache)),"Le retour n'est pas bon");
		$this->assertEquals(100,$t->select($id_tache,'complete'),"La tache n'a pas ete mise a jour");
		$this->obj->q->reset()->addAllFields("tache")->where("tache", "tac hebdomadaire","AND")->where("tache.etat", "en_cours");
		$tache = $this->obj->select_all();	
		$this->assertEquals(date('Y-m-d', strtotime("+7 day"))  , date('Y-m-d', strtotime($tache[0]["tache.horaire_debut"]))  ,"La nouvelle tache Hebdo est incorrecte");
		//notice
		$notices=ATF::$msg->getNotices();
		$this->assertTrue(is_array($notices),'La notice n a pas ete executee');
		$this->assertEquals(ATF::$usr->trans("email_envoye","tache"),$notices[0]['msg'],'Le contenu de la notice n est pas bonne');
	


		
		$id_tache=$t->insert(array('tache'=>array("tache"=>"tac mensuel","horaire_fin"=>"2010-07-26 10:10:00", "periodique"=>"mensuel")));
		$this->assertEquals($id_tache,$t->valid(array('id_tache'=>$id_tache)),"Le retour n'est pas bon");
		$this->assertEquals(100,$t->select($id_tache,'complete'),"La tache n'a pas ete mise a jour");
		$this->obj->q->reset()->addAllFields("tache")->where("tache", "tac mensuel","AND")->where("tache.etat", "en_cours");
		$tache = $this->obj->select_all();
		$this->assertEquals(date('Y-m-d', strtotime("+1 month"))  , date('Y-m-d', strtotime($tache[0]["tache.horaire_debut"]))  ,"La nouvelle tache mensuel est incorrecte");
		//notice
		$notices=ATF::$msg->getNotices();
		$this->assertTrue(is_array($notices),'La notice n a pas ete executee');
		$this->assertEquals(ATF::$usr->trans("email_envoye","tache"),$notices[0]['msg'],'Le contenu de la notice n est pas bonne');


		
		$id_tache=$t->insert(array('tache'=>array("tache"=>"tac trimestriel","horaire_fin"=>"2010-07-26 10:10:00", "periodique"=>"trimestriel")));
		$this->assertEquals($id_tache,$t->valid(array('id_tache'=>$id_tache)),"Le retour n'est pas bon");
		$this->assertEquals(100,$t->select($id_tache,'complete'),"La tache n'a pas ete mise a jour");
		$this->obj->q->reset()->addAllFields("tache")->where("tache", "tac trimestriel","AND")->where("tache.etat", "en_cours");
		$tache = $this->obj->select_all();		
		$this->assertEquals(date('Y-m-d', strtotime("+3 month"))  , date('Y-m-d', strtotime($tache[0]["tache.horaire_debut"]))  ,"La nouvelle tache trimestriel est incorrecte");
		//notice
		$notices=ATF::$msg->getNotices();
		$this->assertTrue(is_array($notices),'La notice n a pas ete executee');
		$this->assertEquals(ATF::$usr->trans("email_envoye","tache"),$notices[0]['msg'],'Le contenu de la notice n est pas bonne');


		
		$id_tache=$t->insert(array('tache'=>array("tache"=>"tac annuel","horaire_fin"=>"2010-07-26 10:10:00", "periodique"=>"annuel")));
		$this->assertEquals($id_tache,$t->valid(array('id_tache'=>$id_tache)),"Le retour n'est pas bon");
		$this->assertEquals(100,$t->select($id_tache,'complete'),"La tache n'a pas ete mise a jour");
		$this->obj->q->reset()->addAllFields("tache")->where("tache", "tac annuel","AND")->where("tache.etat", "en_cours");
		$tache = $this->obj->select_all();		
		$this->assertEquals(date('Y-m-d', strtotime("+1 year"))  , date('Y-m-d', strtotime($tache[0]["tache.horaire_debut"]))  ,"La nouvelle tache annuel est incorrecte");
		//notice
		$notices=ATF::$msg->getNotices();
		$this->assertTrue(is_array($notices),'La notice n a pas ete executee');
		$this->assertEquals(ATF::$usr->trans("email_envoye","tache"),$notices[0]['msg'],'Le contenu de la notice n est pas bonne');

	}	


	/* @author Quentin JANON <qjanon@absystech.fr> */
	public function testEnvoyer_mail(){
		$this->initUserOnly(false);
		$id_user2=ATF::user()->i(array(
			"login"=>"tutul lol 2"
			,"password"=>"tu"
			,"civilite"=>"M"
			,"prenom"=>"class:".get_class($this)
			,"nom"=>"unitaire lol 2"
		));
		$id_user3=ATF::user()->i(array(
			"login"=>"tutul lol 3"
			,"password"=>"tu"
			,"civilite"=>"M"
			,"prenom"=>"class:".get_class($this)
			,"nom"=>"unitaire lol 3"
			,"email"=>"debug@absystech.fr"
		));
		$id_tache=$this->obj->insert(array('tache'=>array("tache"=>"tac lol",'id_aboutisseur'=>$id_user3,"horaire_fin"=>"2010-07-26 10:10:00"),"dest"=>$this->id_user.",".$id_user2));
		$this->assertTrue($this->obj->envoyer_mail($id_tache),'1/ Probleme d envoi de mail');
		//notice
		$notices=ATF::$msg->getNotices();
		$this->assertTrue(is_array($notices),'La notice n a pas ete executee');
		$this->assertEquals(ATF::$usr->trans("email_envoye","tache"),$notices[0]['msg'],'Le contenu de la notice n est pas bonne');
		
		//changement d'aboutisseur
		$this->obj->u(array('id_tache'=>$id_tache,'id_aboutisseur'=>$id_user2));	
		$this->assertTrue($this->obj->envoyer_mail($id_tache),'2/ Probleme d envoi de mail');
	}
/* @author Quentin JANON <qjanon@absystech.fr> */
	public function testrelance(){
		$this->initUserOnly(false);
		$id_user2=ATF::user()->i(array(
			"login"=>"tutul lol 2"
			,"password"=>"tu"
			,"civilite"=>"M"
			,"prenom"=>"class:".get_class($this)
			,"nom"=>"unitaire lol 2"
		));
		$id_user3=ATF::user()->i(array(
			"login"=>"tutul lol 3"
			,"password"=>"tu"
			,"civilite"=>"M"
			,"prenom"=>"class:".get_class($this)
			,"nom"=>"unitaire lol 3"
			,"email"=>"debug@absystech.fr"
		));
		$id_tache=$this->obj->insert(array('tache'=>array("tache"=>"tac lol",'id_aboutisseur'=>$id_user3,"horaire_fin"=>"2010-07-26 10:10:00"),"dest"=>$this->id_user.",".$id_user2));
		$this->assertTrue($this->obj->relance(array("id_tache"=>$id_tache)),'1/ Probleme d envoi de mail');
		//notice
		$notices=ATF::$msg->getNotices();
		$this->assertTrue(is_array($notices),'La notice n a pas ete executee');
		$this->assertEquals(ATF::$usr->trans("email_envoye","tache"),$notices[0]['msg'],'Le contenu de la notice n est pas bonne');
	}

		/* @author Quentin JANON <qjanon@absystech.fr> */
	public function testStats(){
		$this->initUserOnly(false);
		ATF::stats()->initialisation();
		$this->obj->insert(array('tache'=>array("tache"=>"tac lol","horaire_fin"=>date("Y")."-07-26 10:10:00")));
		$this->obj->insert(array('tache'=>array("tache"=>"tac lol2","horaire_fin"=>"2008-07-26 12:10:00")));
		//notice
		$notices=ATF::$msg->getNotices();
		$this->assertTrue(is_array($notices),'La notice n a pas ete executee');
		$this->assertEquals(ATF::$usr->trans("email_envoye","tache"),$notices[0]['msg'],'Le contenu de la notice n est pas bonne');
		
		// nombre de taches
		$graph=$this->obj->stats();
		$this->assertTrue(is_array($graph),"1/ Méthode stats non fonctionnel");
		$this->assertTrue(isset($graph['params']),"1/ Méthode stats non fonctionnel (niveau params)");
		$this->assertTrue(isset($graph['categories']['category']),"1/ Méthode stats non fonctionnel (niveau categories)");
		$this->assertTrue(isset($graph['dataset']),"1/ Méthode stats non fonctionnel (niveau dataset)");
		$this->assertTrue(isset($graph['dataset'][date("Y")]),"1-1/ Méthode stats non fonctionnel (niveau dataset donnee)");
		$this->assertTrue($graph['dataset'][date("Y")]['set']['07']['value']>0,"1-2/ Méthode stats non fonctionnel (niveau dataset donnee)");
		
		// nombre de taches créé pour un user
		// en rajoutant l'annee 2008
		ATF::stats()->liste_annees['tache']['2008']=1;
		$graph2=$this->obj->stats(false,'user');		
		$this->assertTrue(is_array($graph2),"2/ Méthode stats non fonctionnel");
		$this->assertTrue(isset($graph2['params']),"2/ Méthode stats non fonctionnel (niveau params)");
		$this->assertTrue(isset($graph2['categories']['category']),"2/ Méthode stats non fonctionnel (niveau categories)");
		$this->assertTrue(isset($graph2['dataset']),"2/ Méthode stats non fonctionnel (niveau dataset)");
		$this->assertTrue((count($graph2['dataset'])==2),"2-1/ Méthode stats non fonctionnel (niveau dataset nombre donnee)");
		$this->assertEquals(1,$graph2['dataset'][date("Y")]['set']['07']['value'],"2-2/ Méthode stats non fonctionnel (niveau dataset donnee)");
		
		// nombre de taches créé pour tous les users
		$graph3=$this->obj->stats(false,'users');
		$this->assertTrue(is_array($graph3),"3/ Méthode stats non fonctionnel");
		$this->assertTrue(isset($graph3['params']),"3/ Méthode stats non fonctionnel (niveau params)");
		$this->assertTrue(isset($graph3['categories']['category']),"3/ Méthode stats non fonctionnel (niveau categories)");
		$this->assertTrue(isset($graph3['dataset']),"3/ Méthode stats non fonctionnel (niveau dataset)");
		$this->assertEquals(1,$graph3['dataset'][ATF::$usr->get('prenom')." ".ATF::$usr->get('nom')]['set']['07']['value'],"3/ Méthode stats non fonctionnel (niveau dataset donnee)");
		
	}
/* @author Quentin JANON <qjanon@absystech.fr> */
	public function testListe_tache(){
		$this->initUserOnly(false);
		$this->obj->insert(array('tache'=>array("tache"=>"tac lol","horaire_fin"=>"2010-07-26 10:10:00")));
		$this->obj->insert(array('tache'=>array("tache"=>"tac lol2","horaire_fin"=>"2010-07-24 12:10:00")));
		$this->obj->insert(array('tache'=>array("tache"=>"tac lol3","horaire_fin"=>"2010-07-27 15:10:00")));
		$this->obj->insert(array('tache'=>array("tache"=>"tac lol4","horaire_fin"=>"2010-07-27 17:10:00")));
		$this->obj->insert(array('tache'=>array("tache"=>"tac lol5","horaire_fin"=>"2010-07-23 19:10:00")));
		
		$liste=$this->obj->liste_tache(array('date'=>'2010-07-26'));
		$this->assertTrue((count($liste[0])==5),'Le nombre de date retourné n est pas correct');
		$this->assertEquals(1,$liste[0][ATF::$usr->date_trans('2010-07-24')],'Le nombre de tache au 24 n est pas correct');
		$this->assertEquals(0,$liste[0][ATF::$usr->date_trans('2010-07-25')],'Le nombre de tache au 25 n est pas correct');
		$this->assertEquals(1,$liste[0][ATF::$usr->date_trans('2010-07-26')],'Le nombre de tache au 26 n est pas correct');
		$this->assertEquals(2,$liste[0][ATF::$usr->date_trans('2010-07-27')],'Le nombre de tache au 27 n est pas correct');
		$this->assertEquals(0,$liste[0][ATF::$usr->date_trans('2010-07-28')],'Le nombre de tache au 28 n est pas correct');
		
		//notice
		$notices=ATF::$msg->getNotices();
		$this->assertTrue(is_array($notices),'La notice n a pas ete executee');
		$this->assertEquals(ATF::$usr->trans("email_envoye","tache"),$notices[0]['msg'],'Le contenu de la notice n est pas bonne');
	}
/* @author Quentin JANON <qjanon@absystech.fr> */
	public function testCancel(){
		$this->initUserOnly(false);
		$id_tache=$this->obj->insert(array('tache'=>array("tache"=>"tac lol","horaire_fin"=>"2010-07-26 10:10:00")));
		$this->assertFalse($this->obj->cancel(),'Devrait retourné false car pas d id passé en param');
		$this->obj->cancel(array('id_tache'=>$id_tache));
		$this->assertEquals('annule',$this->obj->select($id_tache,'etat'),'Le champs n a pas ete mis a jour');
		//notice
		$notices=ATF::$msg->getNotices();
		$this->assertTrue(is_array($notices),'La notice n a pas ete executee');
		$this->assertEquals(ATF::$usr->trans("email_envoye","tache"),$notices[0]['msg'],'Le contenu de la notice n est pas bonne');
	}
/* @author Quentin JANON <qjanon@absystech.fr> */
	public function testTacheLate(){
		$this->initUserOnly(false);
		ATF::tache()->truncate();
		$id_tache=$this->obj->insert(array('tache'=>array("tache"=>"tache TU 1","horaire_fin"=>"2030-01-01 10:10:00"),"dest"=>ATF::$usr->getID()));
		$this->obj->insert(array('tache'=>array("tache"=>"tache TU 2","horaire_fin"=>"2030-01-02 10:10:00"),"dest"=>ATF::$usr->getID()));
		$this->obj->insert(array('tache'=>array("tache"=>"tache TU 3","horaire_fin"=>"2030-01-03 10:10:00"),"dest"=>ATF::$usr->getID()));
		//notice
		$notices=ATF::$msg->getNotices(); 
		
		$retour=$this->obj->tacheLate(array('date'=>"2030-01-03"));

		$this->assertEquals(2,$retour['count'],'Ne renvoie pas le nombre de tache adéquat');
		$this->assertEquals($id_tache,$retour['data'][0]['id_tache'],'L id de la tache n est pas correct');
		$this->assertEquals(2,$retour['data'][0]['tpsRetard'],'Le temps restant est mal calculé');
	}

/* @author Quentin JANON <qjanon@absystech.fr> */
	public function testTachesImminentes(){
		$this->initUserOnly(false);
		$id_tache=$this->obj->insert(array('tache'=>array("tache"=>"tac lol","horaire_fin"=>date('Y-m-d',strtotime('-4 days'))." 10:10:00"),"dest"=>ATF::$usr->getID()));
		$id_tache2=$this->obj->insert(array('tache'=>array("tache"=>"tac lol2","horaire_fin"=>date('Y-m-d',strtotime('+3 days'))." 10:10:00"),"dest"=>ATF::$usr->getID()));
		$id_tache3=$this->obj->insert(array('tache'=>array("tache"=>"tac lol3","horaire_fin"=>date('Y-m-d',strtotime('+4 days'))." 10:10:00"),"dest"=>ATF::$usr->getID()));
		$id_tache4=$this->obj->insert(array('tache'=>array("tache"=>"tac lol4","horaire_fin"=>date('Y-m-d',strtotime('+4 days'))." 12:10:00"),"dest"=>ATF::$usr->getID()));
		$id_tache5=$this->obj->insert(array('tache'=>array("tache"=>"tac lol5","horaire_fin"=>date('Y-m-d',strtotime('+6 days'))." 10:10:00"),"dest"=>ATF::$usr->getID()));
		//notice
		$notices=ATF::$msg->getNotices();
		
		
		$retour=$this->obj->tachesImminentes(array('date'=>date('Y-m-d'),'nbJours'=>5));
		$this->assertTrue(is_array($retour),'Le type du retour n est pas correct');

		$this->assertNotNull($retour['count'],'Le nombre de donnee retourne est null');
		
		foreach ($retour['data'] as $k=>$i) {
			$this->assertArrayHasKey("data",$i,"Manque les datas");
			$this->assertArrayHasKey("count",$i,"Manque le count");
			$this->assertArrayHasKey("libelle",$i,"Manque le libelle");
		}
	}
	/* @author Quentin JANON <qjanon@absystech.fr> */
	public function testGetSinceLastConnection(){
		$this->initUserOnly(false);
		
		//creation d'une tache bidon
		$id_tache=$this->obj->i(array("id_user"=>$this->id_user,"tache"=>"lol","date"=>date("Y-m-d H:i:s",strtotime("+1 hour")),"horaire_debut"=>date("Y-m-d H:i:s"),"horaire_fin"=>date("Y-m-d H:i:s")));
		$this->assertTrue(is_numeric($id_tache),"La tache ne s'est pas créée");
		$id_tache_user=ATF::tache_user()->i(array("id_tache"=>$id_tache,"id_user"=>$this->id_user));
		$this->assertTrue(is_numeric($id_tache_user),"Le tache_user ne s'est pas créé");
		
		$this->assertEquals(1,$this->obj->getSinceLastConnection(true),"Ne retourne pas le bon nombre de tache");
		
		$retour=$this->obj->getSinceLastConnection();
		$this->assertEquals("lol",$retour[0]["tache"],"N'a pas retournée la bonne tache");
	}
	/* @author Quentin JANON <qjanon@absystech.fr> */
	public function testDefault_value(){

		$id_user=ATF::user()->i(array("login"=>"TULOG1","password"=>"TUPWD1","civilite"=>"M","prenom"=>"TU1","nom"=>"TU1","email"=>"tu@absystech.net"));
		$id_societe=ATF::societe()->i(array("societe"=>"TU"));
		$id_affaire=ATF::affaire()->i(array("id_societe"=>$id_societe,"affaire"=>"TU 1","date"=>date("Y-m-d")));
		$id_suivi=ATF::suivi()->i(array("id_user"=>$id_user,"id_societe"=>$id_societe,"id_affaire"=>$id_affaire,"texte"=>"suivi 1"));	
		
		$this->assertNull($this->obj->default_value("test"),"default_value n'a pas renvoyé null");

		ATF::env()->set("_r","id_suivi",$id_suivi);

		$this->assertEquals($id_contact,$this->obj->default_value("id_contact"),"La méthode ne renvoie pas la bonne valeur id_contact");
		$this->assertEquals($id_affaire,$this->obj->default_value("id_affaire"),"La méthode ne renvoie pas la bonne valeur id_affaire");
		$this->assertEquals("suivi 1",$this->obj->default_value("tache"),"La méthode ne renvoie pas la bonne valeur tache");
		$this->assertEquals($id_societe,$this->obj->default_value("id_societe"),"La méthode ne renvoie pas la bonne valeur id_societe");
	}
/* @author Quentin JANON <qjanon@absystech.fr> */
	public function test_postpone1() {
		$this->assertFalse($this->obj->postpone(),"Rien en entrée == FALSE !");
		$dateT = date("Y-m-d H:i:s");
		$id_tache = $this->obj->i(array("id_user"=>$this->id_user,"tache"=>"Tâche de TU","date"=>$dateT,"horaire_debut"=>date("Y-m-d H:i:s"),"horaire_fin"=>$dateT));
		
		$d = array("postponeValue"=>1,"id_tache"=>$id_tache);
		$jour = date("N");
		$this->obj->postpone($d);
		$newJour = date("Y-m-d H:i:s",strtotime($dateT." +".($jour==5?"3":"1")." days"));
		$this->assertEquals($newJour,ATF::tache()->select($id_tache,"horaire_fin"),"La date n'a pas bien été reporté");

		$notices=ATF::$msg->getNotices();
		$this->assertTrue(is_array($notices),'La notice n a pas ete executee');
		$this->assertEquals(ATF::$usr->trans("tache_postpone")." ".($jour==5?$d['postponeValue']+2:$d['postponeValue'])." ".ATF::$usr->trans("jours"),$notices[0]['msg'],'Le contenu de la notice n est pas bonne');

	}
/* @author Quentin JANON <qjanon@absystech.fr> */
	public function test_postpone2() {
		$dateT = date("Y-m-d H:i:s");
		$id_tache = $this->obj->i(array("id_user"=>$this->id_user,"tache"=>"Tâche de TU","date"=>$dateT,"horaire_debut"=>date("Y-m-d H:i:s"),"horaire_fin"=>$dateT));
		
		$d = array("postponeValue2"=>"01-01-2030","id_tache"=>$id_tache);
		$this->obj->postpone($d);
		
		$newJour = date("Y-m-d H:i:s",strtotime($dateT." +".($jour==5?"3":"1")." days"));
		$this->assertEquals("2030-01-01 00:00:00",ATF::tache()->select($id_tache,"horaire_fin"),"La date n'a pas bien été reporté");

		$notices=ATF::$msg->getNotices();
		$this->assertTrue(is_array($notices),'La notice n a pas ete executee');
		$this->assertEquals(ATF::$usr->trans("tache_postpone2")." ".$d['postponeValue2'],$notices[0]['msg'],'Le contenu de la notice n est pas bonne');

	}
/* @author Quentin JANON <qjanon@absystech.fr> */
	public function test_getFlagPath() {
		$this->assertEquals('<img src="'.ATF::$staticserver.'/images/icones/flags/blue.png">&nbsp;',$this->obj->getFlagPath("petite"),"Mauvais flag pour la petite priorité");
		$this->assertEquals('<img src="'.ATF::$staticserver.'/images/icones/flags/orange.png">&nbsp;',$this->obj->getFlagPath("moyenne"),"Mauvais flag pour la moyenne priorité");
		$this->assertEquals('<img src="'.ATF::$staticserver.'/images/icones/flags/red.png">&nbsp;',$this->obj->getFlagPath("grande"),"Mauvais flag pour la grande priorité");
		$this->assertEquals("",$this->obj->getFlagPath("extreme"),"Mauvais flag pour la priorité inexistante");
	}
	/* @author Quentin JANON <qjanon@absystech.fr> */
	public function test_giveup() {
		ATF::$msg->getNotices();
		$this->assertFalse($this->obj->giveUp(),"Rien en entrée == FALSE !");
		
		$infos['id_tache'] =$this->obj->i(array("id_user"=>$this->id_user,"tache"=>"lol","date"=>date("Y-m-d H:i:s",strtotime("+1 hour")),"horaire_debut"=>date("Y-m-d H:i:s"),"horaire_fin"=>date("Y-m-d H:i:s")));
		$this->assertTrue(is_numeric($infos['id_tache'] ),"La tache ne s'est pas créée");
		$id_tache_user=ATF::tache_user()->i(array("id_tache"=>$infos['id_tache'] ,"id_user"=>$this->id_user));
		$this->assertTrue(is_numeric($id_tache_user),"Le tache_user ne s'est pas créé");
		$id_tache_user2=ATF::tache_user()->i(array("id_tache"=>$infos['id_tache'] ,"id_user"=>1));
		$this->assertTrue(is_numeric($id_tache_user2),"Le tache_user2 ne s'est pas créé");
		$id_tache_user3=ATF::tache_user()->i(array("id_tache"=>$infos['id_tache'] ,"id_user"=>3));
		$this->assertTrue(is_numeric($id_tache_user3),"Le tache_user3 ne s'est pas créé");
		$this->assertEquals(3,count($this->obj->infos_dest($infos['id_tache'])),"Le compte de dest n'est pas le bon avant GIVEUP");
		
		$this->obj->giveUp($infos);
		
		$dest = $this->obj->infos_dest($infos['id_tache']);
		$this->assertEquals(2,count($this->obj->infos_dest($infos['id_tache'])),"Le compte de dest n'est pas le bon apres GIVEUP");

		ATF::tache_user()->delete($id_tache_user2);
		$erreur = false;
		try {
			$this->obj->giveUp($infos);
		} catch (errorATF $e) {
			$erreur = true;
			$c = $e->getCode();
		}
		$this->assertTrue($erreur,"Pas d'erreur alors qu'il ne reste qu'un dest ?");
		$this->assertEquals(402,$c,"Mauvais code d'erreur");

		$notices=ATF::$msg->getNotices();
		$this->assertEquals(4,count($notices),"Erreur dans le nombre de notices");
	}
}