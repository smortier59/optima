<?
/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
class tache_cleodis_test extends ATF_PHPUnit_Framework_TestCase {

	public function setUp() {
		$this->initUser();
	}
	
	public function tearDown() {
		ATF::db()->rollback_transaction(true);
	}
	
	public function testInsert(){
		ATF::$msg->getNotices();
		
		$infos=array('tache'=>array("id_suivi"=>$id_suivi,"tache"=>"tac lol","horaire_debut"=>date("Y-m-d"),"horaire_fin"=>date("Y-m-d")));
		$id_tache=$this->obj->insert($infos);
		$this->assertTrue(is_numeric($id_tache),"N'a pas retourné l'id_tache ou cette dernière ne s'est pas créée");
		
		//redirection
		$ref=ATF::$cr->getCrefresh('main');
		$this->assertEquals($id_tache,$ref['vars']['requests']['tache']['id_tache'],'La redirection sur suivi n est pas correcte');
		
		//notice
		$notices=ATF::$msg->getNotices();
		$this->assertTrue(is_array($notices),'La notice n a pas ete executee');
		$this->assertEquals(ATF::$usr->trans("email_envoye","tache"),$notices[0]['msg'],'Le contenu de la notice n est pas bonne');


		$infos=array('tache'=>array("id_suivi"=>$id_suivi,"tache"=>"tac lol","horaire_debut"=>date("Y-m-d"),"horaire_fin"=>date("Y-m-d") ,"type_tache"=> "demande_comite"), "dest" =>"blabla");
		try{
			$id_tache=$this->obj->insert($infos);
		} catch (errorATF $e) {
			$error = $e->getMessage();
		}
		
		
		$infos=array("id_suivi"=>1,'tache'=>array("id_suivi"=>1,"tache"=>"tac lol","horaire_debut"=>date("Y-m-d"),"horaire_fin"=>date("Y-m-d") ,"type_tache"=> "demande_comite"));
		
		$this->assertTrue(is_numeric($id_tache),"N'a pas retourné l'id_tache ou cette dernière ne s'est pas créée");

		$infos=array('tache'=>array("id_suivi"=>$id_suivi,"tache"=>"tac lol","horaire_debut"=>date("Y-m-d"),"horaire_fin"=>date("Y-m-d"),"type_tache"=> "demande_comite", "decision_comite"=>"accord_portage", "id_societe"=>246), "dest" => array("0"=>"35","1"=>"16"));
		$id_tache=$this->obj->insert($infos);
		$this->assertTrue(is_numeric($id_tache),"N'a pas retourné l'id_tache ou cette dernière ne s'est pas créée");

		$infos=array('tache'=>array("id_suivi"=>$id_suivi,"tache"=>"tac lol","horaire_debut"=>date("Y-m-d"),"horaire_fin"=>date("Y-m-d"),"type_tache"=> "demande_comite", "decision_comite"=>"refus_comite", "id_societe"=>246), "dest" => array("0"=>"19"));
		$id_tache=$this->obj->insert($infos);
		$this->assertTrue(is_numeric($id_tache),"N'a pas retourné l'id_tache ou cette dernière ne s'est pas créée");

	}
	
	public function testValid(){
		$this->initUserOnly(false);
		$id_tache=$this->obj->insert(array('tache'=>array("tache"=>"tac lol","horaire_fin"=>"2010-07-26 10:10:00")));
		$this->assertTrue($this->obj->valid(array('id_tache'=>$id_tache)),"L'email a bien ete envoye");
		$this->assertEquals(100,$this->obj->select($id_tache,'complete'),"La tache n'a pas ete mise a jour");
		//notice
		$notices=ATF::$msg->getNotices();
		$this->assertTrue(is_array($notices),'La notice n a pas ete executee');
		$this->assertEquals(ATF::$usr->trans("email_envoye","tache"),$notices[0]['msg'],'Le contenu de la notice n est pas bonne');



		$infos=array('tache'=>array("id_suivi"=>$id_suivi,"tache"=>"tac lol","horaire_debut"=>date("Y-m-d"),"horaire_fin"=>date("Y-m-d"),"type_tache"=> "demande_comite", "decision_comite"=>"attente_retour", "id_societe"=>246), "dest" => array("0"=>ATF::$usr->getID(),"1"=>"16"));
		$id_tache=$this->obj->insert($infos);
		$this->obj->valid(array('id_tache'=>$id_tache, "comboDisplay"=> "decision user 1"));
		$this->assertEquals("1",$this->obj->select($id_tache , "validation_1"));
		$this->assertEquals("decision user 1",$this->obj->select($id_tache , "decision_1"));

		ATF::$usr->set('id_user',16);
		$this->obj->valid(array('id_tache'=>$id_tache, "comboDisplay"=> "decision user 2"));
		$this->assertEquals(100,$this->obj->select($id_tache,'complete'),"La tache n'a pas ete mise a jour");
		$this->assertEquals("1",$this->obj->select($id_tache , "validation_2"));
		$this->assertEquals("decision user 2",$this->obj->select($id_tache , "decision_2"));
		//notice
		$notices=ATF::$msg->getNotices();
	}	

	public function testCancel(){
		$this->initUserOnly(false);
		$id_tache=$this->obj->insert(array('tache'=>array("tache"=>"tac lol","horaire_fin"=>"2010-07-26 10:10:00")));
		$this->assertFalse($this->obj->cancel(),'Devrait retourné false car pas d id passé en param');
		$this->obj->cancel(array('id'=>$id_tache));
		$this->assertEquals('annule',$this->obj->select($id_tache,'etat'),'Le champs n a pas ete mis a jour');
		//notice
		$notices=ATF::$msg->getNotices();
		$this->assertTrue(is_array($notices),'La notice n a pas ete executee');
		$this->assertEquals(ATF::$usr->trans("email_envoye","tache"),$notices[0]['msg'],'Le contenu de la notice n est pas bonne');
	}
	
	public function testTache_imminente(){
		$this->initUserOnly(false);
		$id_tache=$this->obj->insert(array('tache'=>array("tache"=>"tac lol","horaire_fin"=>date('Y-m-d',strtotime('-4 days'))." 10:10:00")));
		$id_tache2=$this->obj->insert(array('tache'=>array("tache"=>"tac lol2","horaire_fin"=>date('Y-m-d',strtotime('+3 days'))." 10:10:00")));
		$id_tache3=$this->obj->insert(array('tache'=>array("tache"=>"tac lol3","horaire_fin"=>date('Y-m-d',strtotime('+4 days'))." 10:10:00")));
		$id_tache4=$this->obj->insert(array('tache'=>array("tache"=>"tac lol4","horaire_fin"=>date('Y-m-d',strtotime('+4 days'))." 12:10:00")));
		$id_tache5=$this->obj->insert(array('tache'=>array("tache"=>"tac lol5","horaire_fin"=>date('Y-m-d',strtotime('+6 days'))." 10:10:00")));
		//notice
		$notices=ATF::$msg->getNotices();
		$this->assertTrue(is_array($notices),'La notice n a pas ete executee');
		$this->assertEquals(ATF::$usr->trans("email_envoye","tache"),$notices[0]['msg'],'Le contenu de la notice n est pas bonne');
		
		//on se met en concerne
		ATF::tache_user()->multi_insert(array(0=>array('id_tache'=>$id_tache,'id_user'=>$this->id_user)
											,1=>array('id_tache'=>$id_tache2,'id_user'=>$this->id_user)
											,2=>array('id_tache'=>$id_tache3,'id_user'=>$this->id_user)
											,3=>array('id_tache'=>$id_tache4,'id_user'=>$this->id_user)
											,4=>array('id_tache'=>$id_tache5,'id_user'=>$this->id_user)));
		
		$lignes=$this->obj->tache_imminente();
		$this->assertTrue(is_array($lignes),'La méthode n a pas renvoye les taches');
		$this->assertFalse(isset($lignes[date('Y-m-d',strtotime('+5 days'))]),'La méthode a renvoye une tache qui ne devrait pas');
		$this->assertEquals(1,count($lignes[date('Y-m-d',strtotime('+3 days'))]),'1/ Le nombre de tache le '.date('Y-m-d',strtotime('+3 days'))." n est pas correct");
		$this->assertEquals(2,count($lignes[date('Y-m-d',strtotime('+4 days'))]),'2/ Le nombre de tache le '.date('Y-m-d',strtotime('+4 days'))." n est pas correct");
		$this->assertEquals('tac lol2',$lignes[date('Y-m-d',strtotime('+3 days'))][0]['tache'],'Le contenu de la ligne est incorrect');
	}
	
	// @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	public function testUpdate(){
		
		$id_tache=$this->obj->insert(array('tache'=>array("tache"=>"tac lol","horaire_fin"=>"2010-07-26 10:10:00"),"dest"=>"16,35"));
		//notice
		$notices=ATF::$msg->getNotices();
		$this->assertTrue(is_array($notices),'La notice n a pas ete executee');
		$this->assertEquals(ATF::$usr->trans("email_envoye","tache"),$notices[0]['msg'],'Le contenu de la notice n est pas bonne');
		
		$infos=array('tache'=>array('id_tache'=>$id_tache,'tache'=>"tache lol"),'dest'=>"16,35");
		$this->obj->update($infos);
		$donnees=$this->obj->select($id_tache);

		$this->assertEquals("tache lol",$donnees['tache'],"Le nom de la tache n'a pas ete modifie");
		$this->assertTrue(count($donnees['dest'])==2,'Le nombre de destinataire n est pas correct');
		
		
		$infos=array('tache'=>array("id_tache"=>$id_tache,"tache"=>"tac lol","horaire_debut"=>date("Y-m-d"),"horaire_fin"=>date("Y-m-d"),"type_tache"=> "demande_comite", "decision_comite"=>"accord_portage", "id_societe"=>246), "dest" => array("0"=>"35","1"=>"16"));
		$this->obj->update($infos);
		//$this->assertTrue(is_numeric($id_tache),"N'a pas retourné l'id_tache ou cette dernière ne s'est pas créée");

		$infos=array('tache'=>array("id_tache"=>$id_tache,"tache"=>"tac lol","horaire_debut"=>date("Y-m-d"),"horaire_fin"=>date("Y-m-d"),"type_tache"=> "demande_comite", "decision_comite"=>"refus_comite", "id_societe"=>246), "dest" => array("0"=>"35","1"=>"16"));
		$this->obj->update($infos);
		//$this->assertTrue(is_numeric($id_tache),"N'a pas retourné l'id_tache ou cette dernière ne s'est pas créée");
		
		$notices=ATF::$msg->getNotices();
		
	}

	// @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	public function testSelectall(){
		ATF::tache()->q->reset()->where("tache.id_tache", 8509);
		$res = ATF::tache()->select_all();

		$this->assertEquals("appel", $res[0]["type_tache"], "Type tache incorrect??");
	}
	
};
?>