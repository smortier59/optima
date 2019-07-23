<?
/* @author Nicolas BERTEMONT <nbertemont@absystech.fr> */
class conge_absystech_test extends ATF_PHPUnit_Framework_TestCase {
	public function setUp(){
		ATF::db()->begin_transaction(true);
		$this->initUserOnly(false);
		//modification du user pour passer dans la partie email
		ATF::user()->u(array('id_user'=>$this->id_user,"id_superieur"=>23));
		//insertion d'un congé
		$infos=array('conge'=>array('conge'=>'TU conge','date_debut'=>date("Y-m-d"),'date_fin'=>date("Y-m-d")));
		$this->id_conge=$this->obj->insert($infos);
		$this->assertTrue(is_numeric($this->id_conge),"Insertion d'un congé échoué");
		$conge=$this->obj->select($this->id_conge);
		$this->assertEquals($this->id_user,$conge["id_user"],"Insert du user non fonctionnel");
		$this->assertEquals('TU conge',$conge['conge'],"Contenu du congé récupéré incorrect");
	}

	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}

	public function testConstruct(){
		$c_conge=new conge_absystech();
		$this->assertTrue(isset($c_conge->colonnes['fields_column']),"Il n'y a plus de fields_column dans conge");
		$this->assertTrue(isset($c_conge->colonnes['primary']),"Il n'y a plus de primary dans conge");
		$this->assertTrue(isset($c_conge->colonnes['bloquees']),"Il n'y a plus de colonnes bloquées sur conge");
	}

	public function testInsert(){
		//test de la gestion d'erreur dans le cas où on se trompe dans les dates
		$infos_erreur=array('conge'=>array('conge'=>'TU conge','date_debut'=>date("Y-m-d"),'date_fin'=>date("Y-m-d",strtotime("-1day")),"periode"=>"autre"));
		try{
			$this->obj->insert($infos_erreur);
			$this->assertTrue(false,'Aurait dû générer une erreur');
		}catch(errorATF $e){
			$this->assertEquals(ATF::$usr->trans("fin_inf_deb","conge"),$e->getMessage(),"L'erreur retournée n'est pas celle attendue");
		}
	}

	public function testUpdate(){
		$this->obj->update(array("conge"=>array("id_conge"=>$this->id_conge,'date_fin'=>date("Y-m-d",strtotime("+1day")),'periode'=>"autre")));
		$conge=$this->obj->select($this->id_conge);
		$this->assertEquals(date("Y-m-d",strtotime("+1day")),$conge['date_fin'],"La maj du congé n'a pas été appliquée");

		//test de la gestion d'erreur dans le cas où on se trompe dans les dates
		try{
			$this->obj->update(array("conge"=>array("id_conge"=>$this->id_conge,'date_debut'=>date("Y-m-d"),'date_fin'=>date("Y-m-d",strtotime("-1day")),'periode'=>"autre")));
			$this->assertTrue(false,'Aurait dû générer une erreur');
		}catch(errorATF $e){
			$this->assertEquals(ATF::$usr->trans("fin_inf_deb","conge"),$e->getMessage(),"L'erreur retournée n'est pas celle attendue");
		}
	}

	public function testValidation(){
		$retour=$this->obj->validation(array("id_conge"=>$this->id_conge,"etat"=>"ok"));
		$this->assertTrue($retour,"Problème d'envoi de mail");
		$infos = $this->obj->select($this->id_conge);
		$this->obj->delete_zimbra_conge($infos);
	}

	/*public function testStoreIcal(){
		//le but ici n'est pas de tester curl, donc on regarde juste si on récupère les notices, et que l'on passe aux bons endroits
		//case autre
		ATF::setSingleton("curl", new mockObjectCurl());
		$this->obj->storeIcal(array('mdp'=>base64_encode("lol"),"id_conge"=>$this->id_conge));
		$notices_autre=ATF::$msg->getNotices();
		ATF::unsetSingleton("curl");
		$this->assertTrue(is_array($notices_autre),"La notice 'autre' n a pas ete executee");
		$this->assertEquals($notices_autre[0]['msg'],ATF::$usr->trans("ajout_agenda","conge"),"Le contenu de la notice 'autre' n est pas bonne");
		//case am
		ATF::setSingleton("curl", new mockObjectCurlTrue());
		$this->obj->u(array('id_conge'=>$this->id_conge,'periode'=>'am'));
		$this->obj->storeIcal(array('mdp'=>base64_encode("lol"),"id_conge"=>$this->id_conge));
		$notices_am=ATF::$msg->getNotices();
		ATF::unsetSingleton("curl");
		$this->assertTrue(is_array($notices_am),"La notice 'am' n a pas ete executee");
		$this->assertEquals($notices_am[0]['msg'],ATF::$usr->trans("mdp_incorrect","conge"),"Le contenu de la notice 'am' n est pas bonne");

		//case pm
		ATF::setSingleton("curl", new mockObjectCurl());
		$this->obj->u(array('id_conge'=>$this->id_conge,'periode'=>'pm'));
		$this->obj->storeIcal(array('mdp'=>base64_encode("lol"),"id_conge"=>$this->id_conge));
		$notices_pm=ATF::$msg->getNotices();
		ATF::unsetSingleton("curl");
		$this->assertTrue(is_array($notices_pm),"La notice 'pm' n a pas ete executee");
		$this->assertEquals($notices_pm[0]['msg'],ATF::$usr->trans("ajout_agenda","conge"),"Le contenu de la notice 'pm' n est pas bonne");

		//case jour
		ATF::setSingleton("curl", new mockObjectCurl());
		$this->obj->u(array('id_conge'=>$this->id_conge,'periode'=>'jour'));
		$this->obj->storeIcal(array('mdp'=>base64_encode("lol"),"id_conge"=>$this->id_conge));
		$notices_jour=ATF::$msg->getNotices();
		ATF::unsetSingleton("curl");
		$this->assertTrue(is_array($notices_jour),"La notice 'jour' n a pas ete executee");
		$this->assertEquals($notices_jour[0]['msg'],ATF::$usr->trans("ajout_agenda","conge"),"Le contenu de la notice 'jour' n est pas bonne");
	}*/

	/* @author Yann GAUTHERON <ygautheron@absystech.fr>, Nicolas BERTEMONT <nbertemont@absystech.fr> */
	public function testSelectAll(){
		$this->obj->truncate();
		$time = strtotime("2001-06-01");
		$infos=array(
			'conge'=>array(
				'conge'=>'TU conge + 2 semaines'
				,'type'=>'paye'
				,'periode'=>'autre'
				,'etat'=>'ok'
				,'date_debut'=>date("Y-m-d",$time)
				,'date_fin'=>date("Y-m-d",$time+86400*17)
			)
		);
		$this->obj->insert($infos);

		$infos=array(
			'conge'=>array(
				'conge'=>'TU conge 1 jour'
				,'type'=>'paye'
				,'periode'=>'jour'
				,'etat'=>'en_cours'
				,'date_debut'=>date("Y-m-d",$time)
				,'date_fin'=>date("Y-m-d",$time)
			)
		);
		$this->obj->insert($infos);

		// On met tutul en supérieur hiérarchique de lui même
		ATF::user()->u(array("id_user"=>ATF::$usr->getId(),"id_superieur"=>ATF::$usr->getId()));

		$this->obj->q->reset()
			->addField("conge.etat")
			->orWhere("date_debut",date("Y-m-d",$time));
		$this->obj->q->setCount();
		$r = $this->obj->select_all();
		$r = $r['data'];
		unset($r[0]["conge.id_conge"],$r[1]["conge.id_conge"]);

		$this->assertTrue($r[0]["allowValid"],"Le allowValid devrait être a TRUE sur l'enregistrement 0.");
		$this->assertTrue($r[0]["allowRefus"],"Le allowValid devrait être a TRUE sur l'enregistrement 0.");
		$this->assertFalse($r[1]["allowValid"],"Le allowValid devrait être a FALSE sur l'enregistrement 1.");
		$this->assertFalse($r[1]["allowRefus"],"Le allowValid devrait être a FALSE sur l'enregistrement 1.");

		$this->assertEquals('1.0',$r[0]["duree"],"Conges 1 non corrects");
		$this->assertEquals('0.0',$r[0]["dureeCetteAnnee"],"Conges 2 non corrects");
		//+2 car on tombera toujours sur 2 weekend, et on bosse le samedi (donc 2 samedis à ajouter)
		$this->assertEquals('12.0',$r[1]["duree"],"Conges 3 non corrects");
		$this->assertEquals('0.0',$r[1]["dureeCetteAnnee"],"Conges 4 non corrects");

		$infos=array(
			'conge'=>array(
				'conge'=>'TU conge 1 jour à annuler'
				,'type'=>'paye'
				,'date_debut'=>date("Y-m-d")
				,'date_fin'=>date("Y-m-d")
			)
		);
		$id_conge=$this->obj->insert($infos);

		$this->obj->q->reset()->addCondition("id_conge",$id_conge)->setCount();
		$recup_conge = $this->obj->select_all();
		$this->assertTrue($recup_conge['data'][0]["allowCancel"],"Ce congé devrait pouvoir être annulé");

		$infos=array(
			'conge'=>array(
				'conge'=>'TU conge 1 jour à ne pas annuler'
				,'type'=>'paye'
				,'date_debut'=>date("Y-m-d",strtotime("-1 month"))
				,'date_fin'=>date("Y-m-d",strtotime("-1 month"))
				,'id_user'=>1
			)
		);
		$id_conge=$this->obj->insert($infos);

		$this->obj->q->reset()->addCondition("id_conge",$id_conge)->setCount();
		$recup_conge = $this->obj->select_all();
		$this->assertFalse(isset($recup_conge['data'][0]["allowCancel"]),"Ce congé ne devrait pas pouvoir être annulé");
	}

	/* @author Nicolas BERTEMONT <nbertemont@absystech.fr> */
	public function testCan_delete(){
		$infos=array(
			'conge'=>array(
				'conge'=>'TU conge 1 jour à annuler'
				,'type'=>'paye'
				,'date_debut'=>date("Y-m-d",strtotime("+1 month"))
				,'date_fin'=>date("Y-m-d",strtotime("+1 month"))
			)
		);
		$id_conge=$this->obj->insert($infos);

		$this->assertTrue($this->obj->can_delete($id_conge),"Ce congé devrait pouvoir être supprimé");

		$infos=array(
			'conge'=>array(
				'conge'=>'TU conge 1 jour à ne pas annuler'
				,'type'=>'paye'
				,'date_debut'=>date("Y-m-d",strtotime("-1 month"))
				,'date_fin'=>date("Y-m-d",strtotime("-1 month"))
				,'id_user'=>1
			)
		);
		$id_conge=$this->obj->insert($infos);

		try{
			$this->obj->can_delete($id_conge);
			$this->assertFalse(true,"Ce congé ne devrait pas pouvoir être supprimé");
		}catch(errorATF $e){
			//c'est good
		}
	}

	/* @author Nicolas BERTEMONT <nbertemont@absystech.fr> */
	public function testAnnulation(){

		//demande d'annulation
		$this->assertTrue($this->obj->annulation(array("id_conge"=>$this->id_conge,"raison"=>"lol")),"La demande d annulation n a pas fonctionné");
		$this->assertEquals("-> Raison de l'annulation : lol",$this->obj->select($this->id_conge,"raison"),"La raison de la demande d annulation n a pas été sauvegardée");
		$mess=ATF::$msg->getNotices();
		$this->assertEquals("Email envoyé",$mess[0]["msg"],"Notice incorrecte");

		//annulation
		$this->assertTrue($this->obj->annulation(array("id_conge"=>$this->id_conge,"etat"=>"annule")),"L annulation n a pas fonctionné");
		$this->assertEquals("annule",$this->obj->select($this->id_conge,"etat"),"L'etat n a pas été sauvegardée");
		$mess=ATF::$msg->getNotices();
		$this->assertEquals("Email envoyé",$mess[0]["msg"],"Notice incorrecte");
		$notices_autre=ATF::$msg->getWarnings();
	}

	/* @author Antoine MAITRE <amaitre@absystech.fr>
	public function testConge_zimbra() {
		$pref = array();
		$pref["calendrier.host"] = '192.168.0.151';
		$pref['calendrier.username'] = 'debug@absystech.fr';
		$pref['calendrier.password'] = 'jesuis1TU';
		$pref['calendrier.calendar_name'] = 'Calendar';
		$pref['calendrier.calendar_partage'] = 'debug@absystech.fr';
		ATF::preferences()->update($pref);
		$this->assertFalse($this->obj->conge_zimbra(NULL), "Le retour pour NULL devrait être False");
		$infos = $this->obj->select($this->id_conge);
		ATF::setSingleton("curl", new mockObjectCurlVal());
		$this->assertTrue($this->obj->conge_zimbra($infos), "Le retour devrait être True pour autre");
		$infos = $this->obj->select($this->id_conge);
		$this->obj->delete_zimbra_conge($infos);
		$infos['periode'] = 'am';
		$this->assertTrue($this->obj->conge_zimbra($infos), "Le retour devrait être True pour am");
		$infos = $this->obj->select($this->id_conge);
		$this->obj->delete_zimbra_conge($infos);
		$infos['periode'] = 'pm';
		$this->assertTrue($this->obj->conge_zimbra($infos), "Le retour devrait être True pour pm");
		$infos = $this->obj->select($this->id_conge);
		ATF::unsetSingleton("curl");
		$this->obj->delete_zimbra_conge($infos);
		ATF::$msg->getWarnings();
		ATF::$msg->getNotices();
		unlink('/tmp/tmp.ics');
	}

	/* @author Antoine MAITRE <amaitre@absystech.fr>
	public function testDelete_zimbra_conge() {
		$pref = array();
		$pref["calendrier.host"] = '192.168.0.151';
		$pref['calendrier.username'] = 'debug@absystech.fr';
		$pref['calendrier.calendar_name'] = 'Calendar';
		$pref['calendrier.calendar_partage'] = 'debug@absystech.fr';
		ATF::preferences()->update($pref);
		$this->assertFalse($this->obj->delete_zimbra_conge(), "Le retour pour NULL devrait être False");
		$infos = array("id_user"=>ATF::user()->getIDFromLogin('tutul'), "zid"=>"100-101");
		ATF::setSingleton("curl", new mockObjectCurlDel());
		$this->assertTrue($this->obj->delete_zimbra_conge($infos), "Le retour devrait être True");
		$pref['calendrier.password'] = 'jesuis1TU';
		$this->assertTrue($this->obj->delete_zimbra_conge($infos), "Le retour devrait être True");
		ATF::setSingleton("curl", new mockObjectCurlDel());
		ATF::$msg->getWarnings();
		ATF::$msg->getNotices();
		unlink('/tmp/tmp.ics');
	}
	*/

	public function test_CongesDispo(){
		$date = date("Y-m-d",strtotime("+7day"));

		$this->obj->update(array("conge"=>array("id_conge"=>$this->id_conge,'date_fin'=>$date,'periode'=>"autre")));
		$this->obj->validation(array("id_conge"=>$this->id_conge,"etat"=>"ok"));
		ATF::$msg->getWarnings();
		ATF::$msg->getNotices();

		$this->assertEquals(6, $this->obj->CongesDispo(array("id_conge" =>$this->id_conge, "id_user"=>$this->id_user)), "Conges Dispo retour incorrect");
	}
};

class mockObjectCurl extends curl {
	public function curlInit($data){
		$this->ch = 'lol';
	}
	public function curlSetopt($param1,$param2){
		//on ne fait rien, il s'agit de paramétrage
	}
	public function curlExec(){
		return false;
	}
};
class mockObjectCurlTrue extends curl {
	public function curlInit($data){
		$this->ch = 'lol';
	}
	public function curlSetopt($param1,$param2){
		//on ne fait rien, il s'agit de paramétrage
	}
	public function curlExec(){
		return true;
	}
};
class mockObjectCurlVal extends curl {
	public function curlInit($data){
		$this->ch = 'lol';
	}
	public function curlSetopt($param1,$param2){
		//on ne fait rien, il s'agit de paramétrage
	}
	public function curlExec(){
		return 'invId="100-101"';
	}
};
class mockObjectCurlDel extends curl {
	public function curlInit($data){
		$this->ch = 'lol';
	}
	public function curlSetopt($param1,$param2){
		//on ne fait rien, il s'agit de paramétrage
	}
	public function curlExec(){
		return 'invId="100-101"';
	}
};
?>