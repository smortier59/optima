<?
/* @author Nicolas BERTEMONT <nbertemont@absystech.fr> */
class suivi_test extends ATF_PHPUnit_Framework_TestCase {
	public function setUp(){
		ATF::db()->begin_transaction(true);
	}
	
	public function tearDown(){
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
	
	//méthode pour créer le suivi bidon avec tous les éléments nécessaire
	public function ajoutSuivi(){
		$this->initUserOnly(false);
		
		$this->creerSocCon();
		//création d'utilisateur
		$this->id_user=ATF::user()->i(array("login"=>"log lol","password"=>"lol","civilite"=>"M","prenom"=>"lol","nom"=>"lol","email"=>"lol@lol.fr"));
		$this->assertTrue(is_numeric($this->id_user),"Le user ne s'est pas créé");
		$this->id_user2=ATF::user()->i(array("login"=>"log lol2","password"=>"lol2","civilite"=>"M","prenom"=>"lol2","nom"=>"lol2","email"=>"lol2@lol.fr"));
		$this->assertTrue(is_numeric($this->id_user2),"Le user2 ne s'est pas créé");
		
		//insertion d'un suivi
		$infos=array('suivi'=>array('texte'=>'TU suivi','id_societe'=>$this->id_societe
									,"suivi_contact"=>$this->id_contact
									,"suivi_societe"=>$this->id_user.",".$this->id_user2
									,"suivi_notifie"=>$this->id_user,
									"objet"=>["Suivi Contact"]));
		$cr="lol";
		$this->id_suivi=$this->obj->insert($infos,$s,false,$cr);
		//pour éviter les problèmes dans le cas où quelqu'un touche au querier
		$this->assertTrue(is_numeric($this->id_suivi),"Insertion d'un suivi échoué");
		//notice
		$notices=ATF::$msg->getNotices();
		$this->assertTrue(is_array($notices),'La notice n a pas ete executee');
		$this->assertEquals(ATF::$usr->trans("email_envoye","suivi"),$notices[0]['msg'],'Le contenu de la notice n est pas bonne');
	}
	
	public function testInsert(){
		$this->ajoutSuivi();
		
		//test du select et de l'insert
		//récupération des informations
		$donnees=$this->obj->select($this->id_suivi);

		$this->assertEquals("TU suivi",$donnees['texte'],"Le suivi ne contient pas les bonnes informations");	
		$this->assertEquals($this->id_contact,$donnees['suivi_contact'][0],"Le contact n'est pas correct ou n'a pas été récupéré");
		$this->assertEquals($this->id_user,$donnees['suivi_societe'][0],"Le user 1 n'est pas correct ou n'a pas été récupéré");
		$this->assertEquals($this->id_user2,$donnees['suivi_societe'][1],"Le user 2 n'est pas correct ou n'a pas été récupéré");
		$this->assertEquals($this->id_user,$donnees['suivi_notifie'][0],"Le user notifie n'est pas correct ou n'a pas été récupéré");
		
		//cas où le notifie n'a pas de mail
		ATF::user()->u(array('id_user'=>$this->id_user2,"email"=>NULL));
		$this->assertNull(ATF::user()->select($this->id_user2,'email'),"L'email du user 2 n'a pas été modifié");
		
		$id_affaire=ATF::affaire()->i(array("id_societe"=>$this->id_societe,"affaire"=>"TU default value","date"=>date("Y-m-d")));
		$infos2=array('suivi'=>array('texte'=>'TU suivi'
									,'id_contact'=>1
									,'id_societe'=>$this->id_societe
									,'id_affaire'=>$id_affaire
									,"suivi_contact"=>$this->id_contact
									,"suivi_societe"=>$this->id_user
									,"suivi_notifie"=>$this->id_user2
									,"no_redirect"=>true));
		$this->obj->insert($infos2);	
		$warning=ATF::$msg->getWarnings();
		$this->assertTrue(is_array($warning),'La notice n a pas ete executee');
		$this->assertEquals("Aucune adresse mail disponible pour lol2 lol2",$warning[0]['msg'],'Le contenu du warning n est pas bon');		
	}
	
	public function testInsertRedirect(){
		$this->ajoutSuivi();
		
		//test du select et de l'insert
		//récupération des informations
		$donnees=$this->obj->select($this->id_suivi);

		$this->assertEquals("TU suivi",$donnees['texte'],"Le suivi ne contient pas les bonnes informations");	
		$this->assertEquals($this->id_contact,$donnees['suivi_contact'][0],"Le contact n'est pas correct ou n'a pas été récupéré");
		$this->assertEquals($this->id_user,$donnees['suivi_societe'][0],"Le user 1 n'est pas correct ou n'a pas été récupéré");
		$this->assertEquals($this->id_user2,$donnees['suivi_societe'][1],"Le user 2 n'est pas correct ou n'a pas été récupéré");
		$this->assertEquals($this->id_user,$donnees['suivi_notifie'][0],"Le user notifie n'est pas correct ou n'a pas été récupéré");
		
		//cas où le notifie n'a pas de mail
		ATF::user()->u(array('id_user'=>$this->id_user2,"email"=>NULL));
		$this->assertNull(ATF::user()->select($this->id_user2,'email'),"L'email du user 2 n'a pas été modifié");
		
		$infos2=array('suivi'=>array('texte'=>'TU suivi'
									,'id_contact'=>1
									,'id_societe'=>$this->id_societe
									,"suivi_contact"=>$this->id_contact
									,"suivi_societe"=>$this->id_user
									,"suivi_notifie"=>$this->id_user2
									,"__redirect"=>"societe"));
		$s = $cr = $f = array();
		$this->obj->insert($infos2,$s,$f,$cr);	
		$warning=ATF::$msg->getWarnings();
		$this->assertTrue(is_array($warning),'La notice n a pas ete executee');
		$this->assertEquals("Aucune adresse mail disponible pour lol2 lol2",$warning[0]['msg'],'Le contenu du warning n est pas bon');		
	}
	
	public function testInsertRedirectSociete(){
		$this->ajoutSuivi();
		
		//test du select et de l'insert
		//récupération des informations
		$donnees=$this->obj->select($this->id_suivi);

		$this->assertEquals("TU suivi",$donnees['texte'],"Le suivi ne contient pas les bonnes informations");	
		$this->assertEquals($this->id_contact,$donnees['suivi_contact'][0],"Le contact n'est pas correct ou n'a pas été récupéré");
		$this->assertEquals($this->id_user,$donnees['suivi_societe'][0],"Le user 1 n'est pas correct ou n'a pas été récupéré");
		$this->assertEquals($this->id_user2,$donnees['suivi_societe'][1],"Le user 2 n'est pas correct ou n'a pas été récupéré");
		$this->assertEquals($this->id_user,$donnees['suivi_notifie'][0],"Le user notifie n'est pas correct ou n'a pas été récupéré");
		
		//cas où le notifie n'a pas de mail
		ATF::user()->u(array('id_user'=>$this->id_user2,"email"=>NULL));
		$this->assertNull(ATF::user()->select($this->id_user2,'email'),"L'email du user 2 n'a pas été modifié");
		
		$infos2=array('suivi'=>array('texte'=>'TU suivi'
									,'id_contact'=>1
									,'id_societe'=>$this->id_societe
									,"suivi_societe"=>$this->id_user
									,"suivi_notifie"=>$this->id_user2));
		$s = $cr = $f = array();
		$this->obj->insert($infos2,$s,$f,$cr);	

		$warning=ATF::$msg->getWarnings();
		$this->assertTrue(is_array($warning),'La notice n a pas ete executee');
		$this->assertEquals("Aucune adresse mail disponible pour lol2 lol2",$warning[0]['msg'],'Le contenu du warning n est pas bon');		
	}
	
	//suite du test du select (en plus de ce qu'il y a dans l'insert)
	public function testSelect(){
		$this->ajoutSuivi();
		$this->assertEquals('TU suivi',$this->obj->select($this->id_suivi,'texte'),"1/ Select avec un champs en paramètre non fonctionnel");
		$this->assertEquals($this->id_societe,$this->obj->select($this->id_suivi,'id_societe'),"2/ Select avec un champs en paramètre non fonctionnel");
	}
	
	public function testUpdate(){
		$this->ajoutSuivi();
		
		//création d'un contact
		$id_new_contact=ATF::contact()->i(array("nom"=>"tu lol2","email"=>"clol2@lol.fr"));
		$this->assertTrue(is_numeric($id_new_contact),"Le contact 2 ne s'est pas créé");
		
		$infos=array('suivi'=>array('id_suivi'=>$this->id_suivi,'texte'=>'TU suivi modif'
									,"suivi_contact"=>$id_new_contact
									,"suivi_societe"=>$this->id_user
									,"suivi_notifie"=>$this->id_user2));
		$this->obj->update($infos);
		$suivi_maj=$this->obj->select($this->id_suivi);
		$this->assertEquals('TU suivi modif',$suivi_maj['texte'],"Méthode update non fonctionnel");
		$this->assertEquals($id_new_contact,$suivi_maj['suivi_contact'][0],"Le contact n'est pas correct ou n'a pas été récupéré");
		$this->assertTrue(count($suivi_maj['suivi_societe'])==1,"Le user2 n'a pas été supprimé des suivi_societe");
		$this->assertEquals($this->id_user,$suivi_maj['suivi_societe'][0],"Le user n'est pas correct ou n'a pas été récupéré");
		$this->assertEquals($this->id_user2,$suivi_maj['suivi_notifie'][0],"Le user2 notifie n'est pas correct ou n'a pas été récupéré");
	
		//cas ou il n'y a pas d'email
		ATF::user()->u(array('id_user'=>$this->id_user2,"email"=>NULL));
		$this->assertNull(ATF::user()->select($this->id_user2,'email'),"L'email du user 2 n'a pas été modifié");
		
		$this->obj->update($infos);	
		$warning=ATF::$msg->getWarnings();
		$this->assertTrue(is_array($warning),'La notice n a pas ete executee');
		$this->assertEquals("Aucune adresse mail disponible pour lol2 lol2",$warning[0]['msg'],'Le contenu du warning n est pas bon');		
	}

	public function testSelect_all(){
		$this->ajoutSuivi();
		
		$this->obj->q->reset()->addCondition("suivi.id_suivi",$this->id_suivi)->addField("texte");
		$donnees=$this->obj->select_all();
		$this->assertEquals('TU suivi',$donnees[0]['texte'],"Le suivi récupéré n'est pas le bon");
		$this->assertEquals("tu lol",trim($donnees[0]['suivi.intervenant_client']),"Le contact récupéré n'est pas correct");
	}
	
	public function testDernierSuivi(){
		$this->ajoutSuivi();
		$donnees=$this->obj->dernierSuivi($this->id_societe);
		$this->assertEquals($this->id_suivi,$donnees['id_suivi'],"Le suivi récupéré n'est pas le dernier");
		$this->assertEquals('TU suivi',$donnees['texte'],"Les données du dernier suivi ne sont pas correctes");
	}

	public function testDefault_value(){
		$this->assertNull($this->obj->default_value("type"),"default_value n'a pas renvoyé null");
		$this->initUserOnly(false);
		$this->creerSocCon();

		$this->assertNull($this->obj->default_value("id_societe"),"La méthode ne renvoie pas la bonne valeur");
		$this->assertNull($this->obj->default_value("id_contact"),"La méthode ne renvoie pas la bonne valeur");

		ATF::contact()->u(array("id_contact"=>$this->id_contact,"id_owner"=>ATF::$usr->get("id_user")));
		$this->assertEquals($this->id_contact,$this->obj->default_value("id_contact"),"La méthode ne renvoie pas la bonne valeur");

		ATF::env()->set("_r","suivi_contact_id_contact",$this->id_contact);
		$this->assertEquals($this->id_societe,$this->obj->default_value("id_societe"),"La méthode ne renvoie pas la bonne valeur");
		$this->assertEquals($this->id_contact,$this->obj->default_value("id_contact"),"La méthode ne renvoie pas la bonne valeur");

		$id_affaire=ATF::affaire()->i(array("id_societe"=>$this->id_societe,"affaire"=>"TU default value","date"=>date("Y-m-d")));
		ATF::env()->set("_r","id_affaire",$id_affaire);
		$this->assertEquals($this->id_societe,$this->obj->default_value("id_societe"),"La méthode ne renvoie pas la bonne valeur");
		
		ATF::env()->set("_r","id_contact",$this->id_contact);
		$this->assertEquals(ATF::contact()->select($this->id_contact,"id_societe"),$this->obj->default_value("id_societe"),"La méthode ne renvoie pas la bonne valeur");
		$this->assertEquals($this->id_contact,$this->obj->default_value("id_contact"),"La méthode ne renvoie pas la bonne valeur");
	}
	
	public function testAutocompleteConditions(){
		//sans id_contact
		$c_contact=new contact();
		$donnees=$this->obj->autocompleteConditions($c_contact,(array)($infos),"nom","lol");
		$this->assertTrue(count($donnees['condition_field'])==1,"Le nombre de données 'field' n'est pas correcte");
		$this->assertEquals("contact.nom",$donnees['condition_field'][0],"Le champs 'field' n'est pas formaté correctement");
		$this->assertTrue(count($donnees['condition_value'])==1,"Le nombre de données 'value' n'est pas correcte");
		$this->assertEquals("lol",$donnees['condition_value'][0],"Le champs 'value' n'est pas formaté correctement");
		
		//avec id_contact
		$this->creerSocCon();
		$donnees2=$this->obj->autocompleteConditions($c_contact,array('suivi_contact_id_contact'=>$this->id_contact),"nom","lol");
		$this->assertTrue(count($donnees2['condition_field'])==2,"Le nombre de données2 'field' n'est pas correcte");
		$this->assertEquals("contact.id_societe",$donnees2['condition_field'][0],"Le 1e champs 'field' n'est pas formaté correctement");
		$this->assertEquals("contact.nom",$donnees2['condition_field'][1],"Le 2e champs 'field' n'est pas formaté correctement");
		$this->assertTrue(count($donnees2['condition_value'])==2,"Le nombre de données2 'value' n'est pas correcte");
		$this->assertEquals($this->id_societe,$donnees2['condition_value'][0],"Le 1e champs 'value' n'est pas formaté correctement");	
		$this->assertEquals("lol",$donnees2['condition_value'][1],"Le 2e champs 'value' n'est pas formaté correctement");	
	}
	
	public function testGetRecentForMobile(){
		//suppression de tous les suivis pour éviter les problèmes
		$this->obj->q->reset()->addConditionNotNull("id_suivi");
		$this->obj->delete();
		
		$this->ajoutSuivi();
		
		//le count
		//-10sec : correspondant au délai pour créer le suivi
		ATF::$usr->last_activity=date("Y-m-d H:i:s",time()-10);
		$this->assertEquals(1,$this->obj->rpcGetRecentForMobile(array("countUnseenOnly"=>true,"limit"=>1)),"Le nombre de suivi retourné n'est pas correcte");
		
		//les données
		$donnees=$this->obj->rpcGetRecentForMobile();
		$this->assertEquals(1,count($donnees),"Le nombre de données n'est pas correcte");
		$this->assertEquals("soc lol",$donnees[0]['societe'],"Le nom de la société n'est pas correcte");
		$this->assertEquals("TU suivi",$donnees[0]['texte'],"Le nom du suivi n'est pas correcte");
	}

	//@author Quentin JANON <qjanon@absystech.fr>
	public function test_suiviSpeedInsertForWebmail() {
		ATF::unsetSingleton("messagerie");
		ATF::setSingleton("messagerie", new mockObjectForMessagerie());
		ATF::contact()->i(array("nom"=>"TU","email"=>"tu@absystech.net","id_societe"=>ATF::$usr->get('id_societe')));
		$r = $this->obj->suiviSpeedInsertForWebmail($infos);
		
		$this->assertNotNull(strpos($r,',value: "email"'),"1/ L'infos n'est pas a la bonne position");
		$this->assertNotNull(strpos($r,",value: '01-01-2025'"),"2/ L'infos n'est pas a la bonne position");
		$this->assertNotNull(strpos($r,",value:'01-01-2025'+' '+'09:00'"),"3/ L'infos n'est pas a la bonne position");

		ATF::contact()->i(array("nom"=>"TU2","email"=>"tu@absystech.net","id_societe"=>ATF::$usr->get('id_societe')));
		ATF::_r("mime",true);
		$r = $this->obj->suiviSpeedInsertForWebmail($infos);
	}

	//@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	public function test_digest() {
		// On vire les derniers suivis par sécurité en cas de récup de base récente
		$q = "DELETE FROM suivi ORDER BY id_suivi DESC LIMIT 10";
		ATF::db()->query($q);
		
		$id_user1=ATF::user()->i(array("login"=>"TULOG1","password"=>"TUPWD1","civilite"=>"M","prenom"=>"TU1","nom"=>"TU1","email"=>"tu@absystech.net"));
		$id_user2=ATF::user()->i(array("login"=>"TULOG2","password"=>"TUPWD2","civilite"=>"M","prenom"=>"TU2","nom"=>"TU2","email"=>"tu@absystech.net"));
		$id_user3=ATF::user()->i(array("login"=>"TULOG3","password"=>"TUPWD3","civilite"=>"M","prenom"=>"TU3","nom"=>"TU3","email"=>"tu@absystech.net"));

		$tab["preferences"]=array("suivi.mail_digest"=>"non");

		$tab["suivi"]["mail_digest"]="non";
		ATF::user()->u(array("id_user"=>$id_user3,"custom"=>serialize($tab)));

		$id_societe=ATF::societe()->i(array("societe"=>"TU"));
		$id_affaire1=ATF::affaire()->i(array("id_societe"=>$id_societe,"affaire"=>"TU 1","date"=>date("Y-m-d")));
		$id_affaire2=ATF::affaire()->i(array("id_societe"=>$id_societe,"affaire"=>"TU 2","date"=>date("Y-m-d")));
		
		$id_suivi1=$this->obj->i(array("id_user"=>$id_user1,"id_societe"=>$id_societe,"id_affaire"=>$id_affaire1,"texte"=>"suivi 1"));	
		ATF::suivi_notifie()->i(array("id_suivi"=>$id_suivi1,"id_user"=>$id_user1));
		ATF::suivi_notifie()->i(array("id_suivi"=>$id_suivi1,"id_user"=>$id_user2));
		
		$id_suivi2=$this->obj->i(array("id_user"=>$id_user1,"id_societe"=>$id_societe,"id_affaire"=>$id_affaire2,"texte"=>"suivi 2"));	
		ATF::suivi_notifie()->i(array("id_suivi"=>$id_suivi2,"id_user"=>$id_user2));
		ATF::suivi_notifie()->i(array("id_suivi"=>$id_suivi2,"id_user"=>$id_user3));
		
		$tab=$this->obj->digest();
		$this->assertEquals(array(
									0=>array(
												"id_suivi"=>$id_suivi1,
												"id_user"=>$id_user1,
												"id_societe"=>$id_societe,
												"id_affaire"=>$id_affaire1,
												"type"=>"tel",
												"date"=>$tab[0]["date"],
												"texte"=>"suivi 1",
											),
									1=>array(
												"id_suivi"=>$id_suivi1,
												"id_user"=>$id_user1,
												"id_societe"=>$id_societe,
												"id_affaire"=>$id_affaire1,
												"type"=>"tel",
												"date"=>$tab[1]["date"],
												"texte"=>"suivi 1",
											),
									2=>array(
												"id_suivi"=>$id_suivi2,
												"id_user"=>$id_user1,
												"id_societe"=>$id_societe,
												"id_affaire"=>$id_affaire2,
												"type"=>"tel",
												"date"=>$tab[2]["date"],
												"texte"=>"suivi 2",
											),
								)
							,$tab
							,"problème digest");
	}

};

class mockObjectForMessagerie extends messagerie {
	public function select($id) {
		return array(
			"from"=>"tu@absystech.net"
			,"date"=>"2025-01-01 09:00:00"
		);
	}
	
	public function getBody($infos) {
		if (ATF::_r("mime")) {
			return array("mime_type"=>"text");
		}
	}
}
?>