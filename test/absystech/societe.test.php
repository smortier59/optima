<?
/**
* Classe de test sur le module societe
*/
class societe_test extends ATF_PHPUnit_Framework_TestCase {
	/** Méthode pré-test, exécute avant chaque test unitaire
	* besoin d'un user pour les traduction
	*/
	public function setUp() {
		$this->initUser();
	}
	
	/** Méthode post-test, exécute après chaque test unitaire
	*/
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}


				
	/** 
	* Test unitaire d'insertion d'une societe
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	*/
	public function test_insert(){
		//Une erreur doit se déclencher
		try {
			$id=$this->obj->insert(array(),$this->s);
		} catch (errorATF $e) {
			$erreur_trouvee1 = $e->getCode();
		}
		$this->assertEquals(12,$erreur_trouvee1,"L'erreur de la societe non créée n'a pas été catchée !");
		
		//On insère une société débitrice
		$id=$this->obj->insert(array(
					'societe'=>array(
						'societe'=>'Testouille2'
						,'relation'=>'suspect')
		),$this->s);
		
		//Test du retour de la méthode
		$this->assertNotNull($id,'La requte ne renvoit rien... (pas de socit cr ??)');
				
		$societe=$this->obj->select($id);
		
		//Test de la prcence de la référence
		$this->assertNotNull($societe['ref'],'La rfrence de la societe ne se cre pas');
		
		//Test de la construction de la référence
		$this->assertEquals(substr($societe['ref'],0,7),'SSO'.date('ym'),'La rfrence ne se formate pas comme il faut ! (SXXAAMMXXXX)');
		
		//Test de l'idowner
		$this->assertNotNull($societe['id_owner'],'Le propritaire (id_owner) ne se rattache pas...');
		
		//Test de l'idowner du user choisit
		$this->assertEquals($societe['id_owner'],$this->s["user"]->get("id_user"),'Le propritaire (id_owner) n\'est pas le mme que le crateur...');
		
	}
		
	/** 
	* Test unitaire méthode get_max_ref
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	*/
	public function test_get_max_ref(){
		
		//Excution de la méthode métier
		$result=$this->obj->get_max_ref('SSO'.date('ym'));
		
		//Test du retour de la méthode
		$this->assertNotNull($result,"La rfrence n'est pas retourne...");
		
		//Test de la validité du résultat
		$this->assertTrue($result>=1,"La rfrence max n'est pas valide !");
	}
	
	/** 
	* Test unitaire méthode create_ref
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @author Mathieu Tribouillard <mtribouillard@absystech.fr>
	* @depends test_get_max_ref
	*/
	public function test_create_ref(){
		//log::logger($societe['ref'],'jgwiazdowski');
		//Exécution de la méthode métier
		$result=$this->obj->create_ref($this->s);
		
		//Test du retour de la mthode
		$this->assertNotNull($result,"La rfrence n'est pas retourne...");
		
		//Test de la validité du résultat
		$this->assertEquals(substr($result,0,7),'SSO'.date('ym'),"La rfrence n'est pas correcte !");
		
		//Insertion et test du suivant
		$id=$this->obj->insert(array(
					'societe'=>array(
						'societe'=>'Testouille2'
						,'relation'=>'suspect')
		),$this->s);
		
		//Excution de la méthode métier
		$result=$this->obj->create_ref($this->s);
		
		//Test du retour de la méthode
		$this->assertNotNull($result,"La rfrence suivante n'est pas retourne...");
		
		//Test de la validité du résultat
		$this->assertEquals(substr($result,0,7),'SSO'.date('ym'),"La rfrence n'est pas correcte !");

		$this->obj->q->reset()
			->addCondition('id_societe',$id);
		$societe=$this->obj->sa();

		$this->obj->u(array("id_societe"=>$id,"ref"=>substr($societe[0]["ref"],0,7)."0098"));
		$result=$this->obj->create_ref($this->s);
		$this->assertEquals("0099",substr($result,-4),"1 La rfrence n'est pas correcte !");

		$this->obj->u(array("id_societe"=>$id,"ref"=>substr($societe[0]["ref"],0,7)."0998"));
		$result=$this->obj->create_ref($this->s);
		$this->assertEquals("0999",substr($result,-4),"2 La rfrence n'est pas correcte !");

		$this->obj->u(array("id_societe"=>$id,"ref"=>substr($societe[0]["ref"],0,7)."9998"));
		$result=$this->obj->create_ref($this->s);
		$this->assertEquals("9999",substr($result,-4),"3 La rfrence n'est pas correcte !");

		$this->obj->u(array("id_societe"=>$id,"ref"=>substr($societe[0]["ref"],0,7)."9999"));
		// Erreur 1
		try {
			$result=$this->obj->create_ref($this->s);
		} catch (errorATF $e) {
			$erreur_trouvee1 = $e->getCode();
		}
		$this->assertEquals(80853,$erreur_trouvee1,"4 La rfrence n'est pas correcte !");

		ATF::$usr->set("id_agence",NULL);
		// Erreur 2
		try {
			$result=$this->obj->create_ref($this->s);
		} catch (errorATF $e) {
			$erreur_trouvee2 = $e->getCode();
		}
		$this->assertEquals(80846,$erreur_trouvee2,"5 La rfrence n'est pas correcte !");
	}
		
	/** 
	* Test unitaire méthode applique_css
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	*/
	public function test_applique_css(){
		//Test de socité inactive
		$societe=array("societe.etat"=>"inactif");
		$retour=$this->obj->applique_css($societe);
		$this->assertEquals($retour,"grise",'La couleur CSS ne correspond pas !');
		
		//Test de société active
		$societe=array("societe.etat"=>"actif");
		$retour=$this->obj->applique_css($societe);
		$this->assertNull($retour,'La couleur CSS ne correspond pas !');
        
		//Test de société inactive en base
		ATF::societe()->update(array("id_societe"=>$this->id_societe,"etat"=>"inactif"));
		$societe=array("societe.id_societe"=>$this->id_societe);
		$retour=$this->obj->applique_css($societe);
		$this->assertEquals($retour,"grise",'La couleur CSS ne correspond pas !');
		
		//Test de société active en base
		ATF::societe()->update(array("id_societe"=>$this->id_societe,"etat"=>"actif"));
		$societe=array("societe.id_societe"=>$this->id_societe);
		$retour=$this->obj->applique_css($societe);
		$this->assertNull($retour,'La couleur CSS ne correspond pas !');		
	}
	
	/** 
	* Test unitaire méthode update spécifique
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	*/
	public function test_update(){
		//test de la présence des coordonnées GPS
		$societe=$this->obj->select($this->id_societe);
		$this->assertNotNull($societe["latitude"],"La latitude n'est pas prsente !");
		$this->assertNotNull($societe["longitude"],"La longitude n'est pas prsente !");
		
		//test coordonnée GPS
		$this->obj->update(array("societe"=>array("id_societe"=>$this->id_societe,"adresse"=>"hop")));
		$societe=$this->obj->select($this->id_societe);
		$this->assertNull($societe["latitude"],"[changement adresse] La latitude n'est pas nulle !");
		$this->assertNull($societe["longitude"],"[changement adresse] La longitude n'est pas nulle !");
		
		$this->obj->update(array("societe"=>array("id_societe"=>$this->id_societe,"cp"=>"hop")));
		$societe=$this->obj->select($this->id_societe);
		$this->assertNull($societe["latitude"],"[changement cp] La latitude n'est pas nulle !");
		$this->assertNull($societe["longitude"],"[changement cp] La longitude n'est pas nulle !");
		
		$this->obj->update(array("societe"=>array("id_societe"=>$this->id_societe,"ville"=>"hop")));
		$societe=$this->obj->select($this->id_societe);
		$this->assertNull($societe["latitude"],"[changement ville] La latitude n'est pas nulle !");
		$this->assertNull($societe["longitude"],"[changement ville] La longitude n'est pas nulle !");
		
		//Test contact inactif
		$this->obj->update(array("societe"=>array("id_societe"=>$this->id_societe,"etat"=>"inactif")));
		$contact=ATF::contact()->select($this->id_contact);
		$this->assertEquals($contact["etat"],"inactif","Le contact ne passe pas en inactif !");		
	}

	/** 
	* @author Mathieu Tribouillard <mtribouillard@absystech.fr>
	*/
	public function test_autocompleteFournisseurs(){
		$autocompleteFournisseurs=$this->obj->autocompleteFournisseurs(array("condition_value"=>$this->id_societe,"condition_field"=>"id_societe"));
		$this->assertNull($autocompleteFournisseurs[0][0],"autocompleteFournisseurs ne renvoie pas la bonne valeur sans fournisseur");
		$this->obj->update(array("id_societe"=>$this->id_societe,"fournisseur"=>"oui"));
		$autocompleteFournisseurs=$this->obj->autocompleteFournisseurs(array("condition_value"=>$this->id_societe,"condition_field"=>"id_societe"));
		$this->assertEquals("TestTU",$autocompleteFournisseurs[0][1],"autocompleteAvecTermes ne renvoie pas la bonne valeur avec fournisseur");
	}

	/* @author Nicolas BERTEMONT <nbertemont@absystech.fr> */
	public function test_feuille(){
		$id_parent=ATF::societe()->i(array('societe'=>'soc parent'));
		$id_enfant=ATF::societe()->i(array('societe'=>'soc enfant','id_filiale'=>$id_parent));
		
		//test du parent
		$feuille_parent=$this->obj->feuille(array("id_societe"=>$id_parent,"societe"=>"soc parent","expanded"=>false));
		$this->assertEquals('a:6:{s:4:"text";s:10:"soc parent";s:2:"id";i:'.$id_parent.';s:4:"leaf";b:0;s:4:"href";s:52:"societe-select-'.$this->obj->cryptId($id_parent).'.html";s:3:"cls";s:6:"folder";s:8:"expanded";b:0;}',serialize($feuille_parent),"1/ Le contenu est incorrect");
		
		//test de l'enfant
		$feuille_enfant=$this->obj->feuille(array("id_societe"=>$id_enfant,"societe"=>"soc enfant"));
		$this->assertEquals('a:6:{s:4:"text";s:10:"soc enfant";s:2:"id";i:'.$id_enfant.';s:4:"leaf";b:1;s:4:"href";s:52:"societe-select-'.$this->obj->cryptId($id_enfant).'.html";s:3:"cls";s:4:"file";s:8:"expanded";N;}',serialize($feuille_enfant),"2/ Le contenu est incorrect");
	}
	
	/* @author Nicolas BERTEMONT <nbertemont@absystech.fr> */
	public function test_societeOriginelle(){
		$id_parent=ATF::societe()->i(array('societe'=>'soc parent'));
		$id_enfant=ATF::societe()->i(array('societe'=>'soc enfant','id_filiale'=>$id_parent));
		$id_pt_enfant=ATF::societe()->i(array('societe'=>'soc pt enfant','id_filiale'=>$id_enfant));

		$socOri=$this->obj->societeOriginelle($id_parent,$id_parent);
		$this->assertEquals('a:6:{s:4:"text";s:10:"soc parent";s:2:"id";s:4:"'.$id_parent.'";s:4:"leaf";b:0;s:4:"href";s:52:"societe-select-'.$this->obj->cryptId($id_parent).'.html";s:3:"cls";s:6:"folder";s:8:"expanded";N;}',serialize($socOri[0]),"1/ Le contenu est incorrect");
		
		$socOri2=$this->obj->societeOriginelle($id_enfant,$id_enfant);
		$this->assertEquals('a:6:{s:4:"text";s:10:"soc parent";s:2:"id";s:4:"'.$id_parent.'";s:4:"leaf";b:0;s:4:"href";s:52:"societe-select-'.$this->obj->cryptId($id_parent).'.html";s:3:"cls";s:6:"folder";s:8:"expanded";b:1;}',serialize($socOri2[0]),"2/ Le contenu est incorrect");

		$socOri3=$this->obj->societeOriginelle($id_pt_enfant,$id_pt_enfant);
		$this->assertEquals('a:6:{s:4:"text";s:10:"soc parent";s:2:"id";s:4:"'.$id_parent.'";s:4:"leaf";b:0;s:4:"href";s:52:"societe-select-'.$this->obj->cryptId($id_parent).'.html";s:3:"cls";s:6:"folder";s:8:"expanded";b:1;}',serialize($socOri3[0]),"3/ Le contenu est incorrect");
	}
	
	/* @author Nicolas BERTEMONT <nbertemont@absystech.fr> */
	public function test_estEnfant(){
		$id_parent=ATF::societe()->i(array('societe'=>'soc parent'));
		$id_enfant=ATF::societe()->i(array('societe'=>'soc enfant','id_filiale'=>$id_parent));
		$id_pt_enfant=ATF::societe()->i(array('societe'=>'soc pt enfant','id_filiale'=>$id_enfant));
		
		$this->assertTrue($this->obj->estEnfant($id_parent,$id_enfant),"id_enfant est normalement enfant de id_parent");
		$this->assertTrue($this->obj->estEnfant($id_parent,$id_pt_enfant),"id_pt_enfant est normalement enfant de id_parent");
		$this->assertFalse($this->obj->estEnfant($id_enfant,$id_parent),"id_parent n'est pas enfant de id_enfant");
	}
	
	/* @author Nicolas BERTEMONT <nbertemont@absystech.fr> */
	public function test_branch(){
		$id_parent=ATF::societe()->i(array('societe'=>'soc parent'));
		$id_enfant=ATF::societe()->i(array('societe'=>'soc enfant','id_filiale'=>$id_parent));
		$id_pt_enfant=ATF::societe()->i(array('societe'=>'soc pt enfant','id_filiale'=>$id_enfant));
		$id_pt_enfant2=ATF::societe()->i(array('societe'=>'soc pt enfant2','id_filiale'=>$id_enfant));
		
		$branche=$this->obj->branch(array("node"=>"source","valeur"=>$id_pt_enfant));
		$this->assertEquals('[{"text":"soc parent","id":"'.$id_parent.'","leaf":false,"href":"societe-select-'.$this->obj->cryptId($id_parent).'.html","cls":"folder","expanded":true}]',$branche,"1/ Le json envoyé n'est pas correcte");

		$branche2=$this->obj->branch(array("node"=>$id_enfant,"valeur"=>$id_parent));
		$this->assertEquals('[{"text":"soc pt enfant","id":"'.$id_pt_enfant.'","leaf":true,"href":"societe-select-'.$this->obj->cryptId($id_pt_enfant).'.html","cls":"file","expanded":null},{"text":"soc pt enfant2","id":"'.$id_pt_enfant2.'","leaf":true,"href":"societe-select-'.$this->obj->cryptId($id_pt_enfant2).'.html","cls":"file","expanded":null}]',$branche2,"2/ Le json envoyé n'est pas correcte");
		
	}
	
	/* @author Yann GAUTHERON <ygautheron@absystech.fr> */
	public function test_getGroup(){
		$this->assertFalse($this->obj->getGroup());
	}

	//@author Morgan FLEURQUIN <mfleurquin@absystech.fr 
	public function test_sendMails(){
		
		$infos = array("societe" => "1_2"
					  ,"email" => "debug@absystech.fr"
					  ,"message" => "TU send mail societe"
					  );
		ATF::societe()->sendMails($infos);	
		
		$infos["previsualiser"] = "true";	
		ATF::societe()->sendMails($infos);	
	}

	//@author Quentin JANON <qjanon@absystech.fr 
	public function test_getInfosFromCREDITSAFE(){
		
		$infos = array("siret" => "44480406600033");
		$r = $this->obj->getInfosFromCREDITSAFE($infos);	
		

		$this->assertEquals("ABSYSTECH",$r['societe'],"Le nom de société n'est pas bon. Il a changé sur Credit Safe ?");
		$this->assertEquals("FR",$r['id_pays'],"Le id_pays de société n'est pas bon. Il a changé sur Credit Safe ?");
		$this->assertEquals("139 RUE DES ARTS",$r['adresse'],"Le adresse de société n'est pas bon. Il a changé sur Credit Safe ?");
		$this->assertEquals("",$r['adresse_2'],"Le adresse_2 de société n'est pas bon. Il a changé sur Credit Safe ?");
		$this->assertEquals("59100",$r['cp'],"Le cp de société n'est pas bon. Il a changé sur Credit Safe ?");
		$this->assertEquals("ROUBAIX",$r['ville'],"Le ville de société n'est pas bon. Il a changé sur Credit Safe ?");
		//$this->assertEquals("03 20 28 48 68",$r['tel'],"Le tel de société n'est pas bon. Il a changé sur Credit Safe ?");
		//$this->assertEquals("03 20 28 48 69",$r['fax'],"Le fax de société n'est pas bon. Il a changé sur Credit Safe ?");
		$this->assertEquals("6202A",$r['naf'],"Le naf de société n'est pas bon. Il a changé sur Credit Safe ?");
		$this->assertEquals("Conseil en systèmes et logiciels informatiques",$r['activite'],"Le activite de société n'est pas bon. Il a changé sur Credit Safe ?");
		$this->assertEquals("SARL",$r['structure'],"Le structure de société n'est pas bon. Il a changé sur Credit Safe ?");
		$this->assertEquals(20000,$r['capital'],"Le capital de société n'est pas bon. Il a changé sur Credit Safe ?");
		$this->assertEquals("2003-01-01",$r['date_creation'],"Le date_creation de société n'est pas bon. Il a changé sur Credit Safe ?");
		$this->assertEquals("FR88444804066",$r['reference_tva'],"Le reference_tva de société n'est pas bon. Il a changé sur Credit Safe ?");
		$this->assertEquals("10 à 19 salariés",$r['nb_employe'],"Le nb_employe de société n'est pas bon. Il a changé sur Credit Safe ?");

	}


};
?>