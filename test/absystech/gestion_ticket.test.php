<?
/**
* Module gestion_ticket - gère les transactions de la faturation
*/
class gestion_ticket_test extends ATF_PHPUnit_Framework_TestCase {
	protected function setUp() {
		ATF::db()->begin_transaction(true);
	}
	
	protected function tearDown() {
		ATF::db()->rollback_transaction(true);
	}
	
	/* @author Jérémie Gwiazdowski <jgw@absystech.fr> */
	public function test_add_ticket_std(){
		$this->initUser(false);
		$infos=array(
			"credits"=>10,
			"libelle"=>"Test add_ticket",
			"id_societe"=>$this->id_societe
		);
		
		$id_gestion_ticket=$this->obj->add_ticket($infos);
		$this->assertNotNull($id_gestion_ticket,"Impossible d'ajouter un ticket !");
		
		$gestion_ticket=$this->obj->select($id_gestion_ticket);
		
		$this->assertEquals(1,$gestion_ticket["operation"],"assert 1");
		$this->assertEquals("ajout",$gestion_ticket["type"],"assert 2");
		$this->assertEquals("Test add_ticket",$gestion_ticket["libelle"],"assert 3");
		$this->assertNotNull($gestion_ticket["date"],"assert 4");
		$this->assertEquals(10,$gestion_ticket["nbre_tickets"],"assert 5");
		$this->assertEquals(10.00,$gestion_ticket["solde"],"assert 6");
		//print_r($gestion_ticket);
		
		$notices=ATF::$msg->getNotices();
		$this->assertTrue(is_array($notices),"assert 7");
		$this->assertEquals(1,count($notices),"assert 8");
		
		$infos=array(
			"credits"=>25.25,
			"libelle"=>"Test add_ticket",
			"id_societe"=>$this->id_societe,
			"label_" => "FLI14040003"
		);
		
		$cadre_refreshed=array();
		$id_gestion_ticket=$this->obj->add_ticket($infos,$this->s,NULL,$cadre_refreshed);
		$this->assertNotNull($id_gestion_ticket,"Impossible d'ajouter un ticket !");
		
		$gestion_ticket=$this->obj->select($id_gestion_ticket);
		
		$this->assertEquals(2,$gestion_ticket["operation"],"assert 9");
		$this->assertEquals("ajout",$gestion_ticket["type"],"assert 10");
		$this->assertEquals("Test add_ticket",$gestion_ticket["libelle"],"assert 11");
		$this->assertNotNull($gestion_ticket["date"],"assert 12");
		$this->assertEquals(25.25,$gestion_ticket["nbre_tickets"],"assert 13");
		$this->assertEquals(35.25,$gestion_ticket["solde"],"assert 14");
		//print_r($gestion_ticket);
		
		$notices=ATF::$msg->getNotices();
		$this->assertTrue(is_array($notices),"assert 15");
		$this->assertEquals(1,count($notices),"assert 16");
		
		//Test refresh
		$cr=ATF::$cr->getCrefresh();
		$this->assertTrue(is_array($cr["main"]),"assert 17");





	}
	
	/* @author Jérémie Gwiazdowski <jgw@absystech.fr> */
	public function test_add_ticket_gestion_ticket_not_numeric(){
		$this->initUser(false);
		$infos=array(
			"credits"=>"hop",
			"libelle"=>"Test add_ticket",
			"id_societe"=>$this->id_societe
		);
				
		$error=false;
		try{
			$this->obj->add_ticket($infos);
		}catch(errorATF $e){
			$error=true;
		}
		
		$this->assertTrue($error,"L'erreur n'est pas declenchee");
	}
	
	/* @author Jérémie Gwiazdowski <jgw@absystech.fr> */
	public function test_add_ticket_credits_negatifs(){
		$this->initUser(false);
		$infos=array(
			"credits"=>-10,
			"libelle"=>"Test add_ticket",
			"id_societe"=>$this->id_societe
		);
		
		$error=false;
		try{
			$this->obj->add_ticket($infos);
		}catch(errorATF $e){
			$error=true;
		}
		
		$this->assertTrue($error,"L'erreur n'est pas declenchee");
	}
	
	/* @author Jérémie Gwiazdowski <jgw@absystech.fr> */
	public function test_add_ticket_libell_null(){
		$this->initUser(false);
		$infos=array(
			"credits"=>10,
			"id_societe"=>$this->id_societe
		);
		
		$error=false;
		try{
			$this->obj->add_ticket($infos);
		}catch(errorATF $e){
			$error=true;
		}
		
		$this->assertTrue($error,"L'erreur n'est pas declenchee");
	}
	
	/* @author Jérémie Gwiazdowski <jgw@absystech.fr> */
	public function test_remove_ticket(){
		$this->initUser(false);
		//Ajout de 10 tickets
		$infos=array(
			"credits"=>10,
			"libelle"=>"Test add_ticket",
			"id_societe"=>$this->id_societe
		);
		
		$id_gestion_ticket=$this->obj->add_ticket($infos);
		$this->assertNotNull($id_gestion_ticket,"Impossible d'ajouter un ticket !");
		
		$gestion_ticket=$this->obj->select($id_gestion_ticket);
		
		$this->assertEquals(1,$gestion_ticket["operation"],"assert 1");
		$this->assertEquals("ajout",$gestion_ticket["type"],"assert 2");
		$this->assertEquals("Test add_ticket",$gestion_ticket["libelle"],"assert 3");
		$this->assertNotNull($gestion_ticket["date"],"assert 4");
		$this->assertEquals(10,$gestion_ticket["nbre_tickets"],"assert 5");
		$this->assertEquals(10.00,$gestion_ticket["solde"],"assert 6");
		//print_r($gestion_ticket);
		
		$notices=ATF::$msg->getNotices();
		$this->assertTrue(is_array($notices),"assert 7");
		$this->assertEquals(1,count($notices),"assert 8");
		
		//Création d'une hotline
		$hotline = array(
			"hotline"=>"HotlineTuTest"
			,"id_societe"=>$this->s["user"]->get("id_societe")
			,"detail"=>"HotlineTuTest"
			,"date_debut"=>date('Y-m-d')
			,"id_contact"=>$this->id_contact
			,"id_user"=>$this->id_user
			,"visible"=>"oui"
			,"urgence"=>'detail'
			,"pole_concerne"=>"dev"
			,'charge'=>"intervention" //précisé pour éviter les problèmes avec la méthode "setbillingmode"
		);
		$this->id_hotline = ATF::hotline()->insert($hotline);
		
		//Création des interactions
		$hotline_interaction['id_hotline'] =$this->id_hotline;
		$hotline_interaction['date'] =date('Y-m-d H:i:s');
		$hotline_interaction['duree_presta'] ='00:15';
		$hotline_interaction['credit_presta'] =0.25;
		$hotline_interaction['credit_dep'] =0.00;
		$hotline_interaction['heure_debut_presta'] ='14:00';
		$hotline_interaction['heure_fin_presta'] ='14:15';
		$hotline_interaction['etat'] ='fixing';
		$hotline_interaction['detail'] ='detail01';
		$hotline_interaction['visible'] ='oui';
		$hotline_interaction['hotline_interaction'] =$hotline_interaction;
		$this->id_hotline_interaction = ATF::hotline_interaction()->insert($hotline_interaction,$this->s);
		
		$hotline_interaction['id_hotline'] =$this->id_hotline;
		$hotline_interaction['date'] =date('Y-m-d H:i:s');
		$hotline_interaction['duree_presta'] ='00:15';
		$hotline_interaction['credit_presta'] =0.25;
		$hotline_interaction['credit_dep'] =0.00;
		$hotline_interaction['heure_debut_presta'] ='15:00';
		$hotline_interaction['heure_fin_presta'] ='15:15';
		$hotline_interaction['etat'] ='fixing';
		$hotline_interaction['detail'] ='detail02';
		$hotline_interaction['visible'] ='oui';
		$hotline_interaction['hotline_interaction'] =$hotline_interaction;
		$this->id_hotline_interaction_ = ATF::hotline_interaction()->insert($hotline_interaction,$this->s);

		//Suprresion des tickets		
		$infos=array(
			"id_societe"=>$this->id_societe,
			"id_hotline"=>$this->id_hotline
		);
		
		$nb_tickets=$this->obj->remove_ticket($infos);
		
		$this->assertEquals(0.5,$nb_tickets,"nb tickets retires incorrect");
		$gestion_ticket=$this->obj->select($id_gestion_ticket+1);
		//print_r($gestion_ticket);
		
		$this->assertEquals(2,$gestion_ticket["operation"],"assert 9");
		$this->assertEquals("retrait",$gestion_ticket["type"],"assert 10");
		$this->assertNotNull($gestion_ticket["date"],"assert 12");
		$this->assertEquals(-0.5,$gestion_ticket["nbre_tickets"],"assert 13");
		$this->assertEquals(9.5,$gestion_ticket["solde"],"assert 14");
		
		//Flush des notices
		ATF::$msg->getNotices();
	}

	/* @author Nicolas BERTEMONT <nbertemont@absystech.fr> */ 
	public function testStats(){

		$stat_normale=$this->obj->stats(false,false,false,1);

		//check de la méthode ajoutDonnees
		$this->assertTrue(count($stat_normale['dataset'])>0,'Problème de récupération des données à afficher');
		//check de la méthode paramGraphe
		$this->assertTrue(is_array($stat_normale['params']),'Problème de récupération des params');
		//le nombre de valeur dans les set de data doit être égal au nombre de catégorie
		$this->assertTrue(isset($stat_normale['dataset'][2007]),'Problème de récupération des dataset');
		$this->assertEquals(2010,$stat_normale['dataset'][2010]['params']['seriesname'],'Problème de params dans les données');
		$this->assertEquals(count($stat_normale['categories']['category']),count($stat_normale['dataset'][2010]['set']),"Le nombre de données à afficher en abscisse n'est pas correct");

		//test du contenu
		$this->assertEquals("Mars",$stat_normale['categories']['category']['2']['label'],"Le nom des catégories n'est pas correct");
		$this->assertEquals("106",$stat_normale['dataset'][2015]['set']["03"]['value'],'Les valeurs ne sont pas correctes');
		
		$c_gt=new gt_tu();
		$stat_widget=$c_gt->stats(false,false,true,1,10,2011);

		//check de la méthode ajoutDonnees
		$this->assertTrue(count($stat_widget['dataset'])>0,'stat_widget / Problème de récupération des données à afficher');
		//check de la méthode paramGraphe
		$this->assertTrue(is_array($stat_widget['params']),'stat_widget / Problème de récupération des params');
		//le nombre de valeur dans les set de data doit être égal au nombre de catégorie
		$this->assertTrue(isset($stat_widget['dataset']["AbsysTech"]),'stat_widget / Problème de récupération des dataset');
		$this->assertEquals("AbsysTech",$stat_widget['dataset']["AbsysTech"]['params']['seriesname'],'stat_widget / Problème de params dans les données');
		$this->assertEquals(count($stat_widget['categories']['category']),count($stat_widget['dataset']["AbsysTech"]['set']),"stat_widget / Le nombre de données à afficher en abscisse n'est pas correct");

		$cles=array_keys($stat_widget['categories']['category']);
		$cles_val=array_keys($stat_widget['dataset']["AbsysTech"]['set']);
		$this->assertEquals(10,$cles[11],"La dernière clé ne corresponds pas au mois courant");
		$this->assertEquals(10,$cles_val[11],"La dernière clé de valeur ne corresponds pas au mois courant");

		//sans passage de l'id societe
		$stat_widget_ss_soc=$this->obj->stats(false,false,true);
		$this->assertTrue(count($stat_widget_ss_soc['dataset'])>0,'stat_widget_ss_soc / Problème de récupération des données à afficher');
		$this->assertTrue(isset($stat_widget_ss_soc['dataset']["societes"]),'stat_widget_ss_soc / Problème de récupération des dataset');
		$this->assertTrue(count($stat_widget_ss_soc['dataset']["societes"]['set'])>0,'stat_widget_ss_soc / set / Problème de récupération des données à afficher');
	}
	
	/* @author Nicolas BERTEMONT <nbertemont@absystech.fr> */ 
	public function testOptions_soc(){
		$this->obj->truncate();
		$this->obj->i(array("operation"=>"1","id_societe"=>1,"type"=>"retrait","date"=>date("Y-m-d H:i:s")));
		$this->obj->i(array("operation"=>"1","id_societe"=>584,"type"=>"retrait","date"=>date("Y-m-d H:i:s")));
		
		$recup_soc=$this->obj->options_soc();
		$this->assertEquals(2,count($recup_soc),"Le nombre de société récupéré est incorrecte");
		$this->assertEquals("AbsysTech",$recup_soc[1],"La société récupérée est incorrecte (Absystech)");
		$this->assertEquals("GEMP",$recup_soc[584],"La société récupérée est incorrecte (GEMP)");
	}
};


class gt_tu extends gestion_ticket {
	public function initGraphe(&$graph,$intitule,$month=NULL){	
		parent::initGraphe($graph,$intitule,10);
	}
}
?>