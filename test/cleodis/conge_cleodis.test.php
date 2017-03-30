<?
/* @author Nicolas BERTEMONT <nbertemont@absystech.fr> */
class conge_cleodis_test extends ATF_PHPUnit_Framework_TestCase {
	public function setUp(){
		ATF::db()->begin_transaction(true);
	}
	
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}
	
	public function test_estSuperieur(){
		//28 : clandry
		//35 : frandoux
		//17 : cloison
		//16 : jloison
		$this->obj->truncate();
		$this->assertTrue($this->obj->estSuperieur(17,17),"id_user==id_superieur : devrait renvoyer true");
		
		//supérieur du supérieur peut valider
		$this->assertTrue($this->obj->estSuperieur(17,28),"id_user==id_sup : devrait renvoyer true");
		
		//création du congé pour le supérieur
		//tant que cloison n'est pas en congé, jloison ne peut valider les demandes
		$this->assertFalse($this->obj->estSuperieur(16,17),"cloison n'étant pas en congé, jloison ne peut le remplacer");
		
		$this->obj->i(array("date_debut"=>date("Y-m-d"),"date_fin"=>date("Y-m-d",strtotime("+1 day")),"id_user"=>17));
		$this->assertTrue($this->obj->estSuperieur(16,17),"cloison étant en congé, jloison peut le remplacer");
	}
	
	public function test_selectAll(){
		$this->obj->truncate();
		$this->initUserOnly(false);
		$id_conge=$this->obj->i(array("date_debut"=>date("Y-m-d"),"date_fin"=>date("Y-m-d",strtotime("+1 day")),"id_user"=>28));
		
		$this->obj->q->setCount();
		$retour1=$this->obj->select_all();
		
		$this->assertFalse($retour1['data'][0]['allowValid'],"1 - Ne devrait pas avoir la possibilité de valider");
		$this->assertFalse($retour1['data'][0]['allowRefus'],"1 - Ne devrait pas avoir la possibilité de refuser");
	
		//------------------------------
		$this->obj->u(array("id_conge"=>$id_conge,"id_user"=>16));
		
		ATF::$usr->set("id_user","16");

		$this->obj->q->setCount();
		$retour2=$this->obj->select_all();		
		
		$this->assertFalse($retour2['data'][0]['allowValid'],"2 - Ne devrait pas avoir la possibilité de valider");

		$this->obj->u(array("id_conge"=>$id_conge,"id_user"=>28, "etat"=> "attente_jerome"));
		$this->obj->q->setCount();
		$retour2=$this->obj->select_all();
		
		$this->assertTrue($retour2['data'][0]['allowValid'],"3 - Devrait avoir la possibilité de valider");
		$this->assertTrue($retour2['data'][0]['allowRefus'],"3 - Devrait avoir la possibilité de refuser");

		$this->obj->u(array("id_conge"=>$id_conge,"id_user"=>28, "etat"=> "attente_christophe"));
		$this->obj->q->setCount();
		$retour2=$this->obj->select_all();
		
		$this->assertFalse($retour2['data'][0]['allowValid'],"4 - Devrait avoir la possibilité de valider");
		$this->assertFalse($retour2['data'][0]['allowRefus'],"4 - Devrait avoir la possibilité de refuser");
	}
	
	public function test_validation(){
		$id_conge=$this->obj->i(array("date_debut"=>date("Y-m-d"),"date_fin"=>date("Y-m-d",strtotime("+1 day")),"id_user"=>28));
		$this->assertTrue($this->obj->validation(array("id_conge"=>$id_conge,"etat"=>"ok")),"Le mail aurait du être envoyé");
		$notice=ATF::$msg->getNotices();
		$this->assertEquals(ATF::$usr->trans("email_envoye"),$notice[0]["msg"],"Le contenu de la notice est incorrecte");
		
		$this->initUserOnly(false);
		//13 absystech
		ATF::$usr->set("id_user","17");
		$this->assertTrue($this->obj->validation(array("id_conge"=>$id_conge,"etat"=>"ok")),"Le mail aurait du être envoyé");
		$notice=ATF::$msg->getNotices();
		$this->assertEquals(ATF::$usr->trans("email_envoye"),$notice[0]["msg"],"Le contenu de la notice est incorrecte");
	}
		
	public function testInsert(){
		$this->obj->truncate();
		$this->initUserOnly(false);
		try{
			$this->obj->insert(array("conge"=>array("date_debut"=>"2012-02-02","date_fin"=>"2012-02-01",'periode'=>"autre")));
			$this->assertTrue(false,"Une erreur aurait du se produire");
		}catch(errorATF $e){
			$this->assertEquals(ATF::$usr->trans("fin_inf_deb","conge"),$e->getMessage(),"Le message d'erreur est incorrecte");
		}
		
		//le supérieur n'a pas de supérieur et il est en congé
		ATF::setSingleton("user",new user_tu_conge_cleodis());
		ATF::conge()->i(array("date_debut"=>date("Y-m-d"),"date_fin"=>date("Y-m-d",strtotime("+1 day")),"id_user"=>17));
		$id_conge2=$this->obj->insert(array("conge"=>array("date_debut"=>date("Y-m-d"),"date_fin"=>date("Y-m-d",strtotime("+1 day")),"id_user"=>35,'periode'=>"autre")));
		$id_sup_recup2=ATF::user()->sel['email'];
		ATF::unsetSingleton("user");
		$this->assertTrue(is_numeric($id_conge2),"2/ Le congé aurait du être créé");
		$this->assertEquals(16,$id_sup_recup2,"Le supérieur 2 pris en compte est faux");
		
		//le supérieur 1 est en congé mais que personne ne possède son profil et son supérieur => alors on laisse la demande à ce dernier
		ATF::setSingleton("user",new user_tu_conge_cleodis());
		ATF::conge()->i(array("date_debut"=>date("Y-m-d"),"date_fin"=>date("Y-m-d",strtotime("+1 day")),"id_user"=>35)); // Mettre fred en congé
		$id_conge=$this->obj->insert(array("conge"=>array("date_debut"=>date("Y-m-d"),"date_fin"=>date("Y-m-d",strtotime("+1 day")),"id_user"=>21,'periode'=>"autre"))); // Mettre severine en congé, fred est sa supérieure
		$id_sup_recup=ATF::user()->sel['email'];
		
		$this->assertTrue(is_numeric($id_conge),"Le congé aurait du être créé");	
		$this->assertEquals(17,$id_sup_recup,"Le supérieur pris en compte est faux");
		
		// On simule l'inactivité de tous sauf severine
		$query = "UPDATE user SET etat=IF(id_user=21,'normal','inactif')";
		ATF::db()->query($query);
		
		$id_conge=$this->obj->insert(array("conge"=>array("date_debut"=>date("Y-m-d"),"date_fin"=>date("Y-m-d",strtotime("+1 day")),"id_user"=>21,'periode'=>"autre"))); // Mettre severine en congé, fred est sa supérieure
		$id_sup_recup=ATF::user()->sel['email'];
		$this->assertTrue(is_numeric($id_conge),"Le congé aurait du être créé");	
		$this->assertEquals(17,$id_sup_recup,"Le supérieur pris en compte est faux");
	}
};

class user_tu_conge_cleodis extends user{
	public function __construct() {
		$this->table = "user";
		return parent::__construct();	
	}
	public function select($id,$champs){
		$this->sel[$champs]=$id;
		return parent::select($id,$champs);
	}
}

?>