<?
/**
* TU sur le module contact d'optima
*/
class contact_test extends ATF_PHPUnit_Framework_TestCase {
	/** 
	* Méthode pré-test, exécutée avant chaque test unitaire
	* besoin d'un user pour les traduction
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	*/
	public function setUp() {
		$this->initUser();
	}
	
	/** 
	* Méthode post-test, exécutée après chaque test unitaire
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	*/
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}
			
//	* 
//	* Test unitaire sur le constructeur
//	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
//	
	public function test_contact_constructeur(){
		new contact();
	}
//	* 
//	* Test unitaire sur la méthode insert
//	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
//	
	public function test_select_data(){
		$order = "date";
		$r = $this->obj->select_data($order);
		$this->assertNotNull(count($r),"Aucun retour ?");

	}
	
//	* 
//	* Test unitaire sur la méthode insert
//	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
//	
	public function test_saCustom(){
		$this->obj->q->setCount();
		$r = $this->obj->saCustom();
		$r = $r['data'];
		$this->assertArrayHasKey("completer",$r[0],"Il manque le champ 'completer'");
		$this->assertArrayHasKey("contact.etat",$r[0],"Il manque le champ 'contact.etat'");
		$this->assertTrue($r[0]['allowSuivi'],"Le allowSuivi n'est pas a TRUE, pourtant les doits sont censé être là.");

	}
	
//	* 
//	* Test unitaire sur la méthode insert
//	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
//	
	public function test_insert(){
		//Insertion d'un contact
		$contact=array('contact'=>array('nom'=>'nom','prenom'=>'prenom'));
		$id=$this->obj->insert($contact,$this->s);
		//Test de la requête
		$this->assertNotNull($id,"Le contact n'a pu être inséré");
		//On recherche le contact
		$contact=$this->obj->select($id);
		//print_r($contact);
		//Test de la requête
		$this->assertNotNull($contact,"Le contact n'a pu être retouvé ! Le select ne marche pas !");
		//Test de la casse du nom
		$this->assertEquals($contact['nom'],'NOM',"Le nom n'est pas en majuscule ! strtoupper n'a pas du fonctionner...");
		//Test de la casse du prénom
		$this->assertEquals($contact['prenom'],'Prenom',"La première lettre du prénom n'est pas en majuscule ! ucfirst n'a pas du fonctionner...");
		//Test du propriétaire
		$this->assertEquals($contact['id_owner'],$this->s["user"]->getID(),"Le contact propriétaire n'a pas été trouvé !");
	}
	
	
//	* 
//	* Test unitaire méthode applique_css
//	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
//	
	public function test_applique_css(){
		//Test de société inactive
		$contact=array("contact.etat"=>"inactif");
		$retour=$this->obj->applique_css($contact);
		$this->assertEquals($retour,"grise",'La couleur CSS ne correspond pas !');
		
		//Test de société active
		$contact=array("contact.etat"=>"actif");
		$retour=$this->obj->applique_css($contact);
		$this->assertNull($retour,'La couleur CSS ne correspond pas !');

		//Test de société inactive en base
		ATF::contact()->update(array("id_contact"=>$this->id_contact,"etat"=>"inactif"));
		$contact=array("contact.id_contact"=>$this->id_contact);
		$retour=$this->obj->applique_css($contact);
		$this->assertEquals($retour,"grise",'La couleur CSS ne correspond pas !');
		
		//Test de société active en base
		ATF::contact()->u(array("id_contact"=>$this->id_contact,"gsm"=>"000000"));
		ATF::contact()->update(array("id_contact"=>$this->id_contact,"etat"=>"actif"));
		$contact=array("contact.id_contact"=>$this->id_contact);
		$retour=$this->obj->applique_css($contact);
		$this->assertNull($retour,'La couleur CSS ne correspond pas !');		
	}
	
//	* 
//	* Test unitaire méthode getMail
//	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
//	
	public function test_getMail(){
		$contact=array("id_contact"=>$this->id_contact);
		$retour=$this->obj->getMail($contact);
		$this->assertEquals($retour["email"],"debug@absystech.fr",'Le mail ne correspond pas !');
	}
	
//	* 
//	* Test unitaire méthode autocomplete
//	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
//	
	public function test_autocomplete(){
		$infos=array("condition_field"=>"contact.id_societe","condition_value"=>ATF::user()->cryptId($this->id_societe));
		$retour=$this->obj->autocomplete($infos);
		$this->assertTrue(is_array($retour[0]));
		$this->assertEquals($retour[0][1],"M contact test unitaire");
		$this->assertEquals($retour[0][2],"TestTU");
		$this->assertEquals($retour[0]["raw_1"],"M contact test unitaire");
		$this->assertEquals($retour[0]["raw_2"],"TestTU");
	}	
	
	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	public function test_autocompleteAvecMail(){
		$infos=array("condition_field"=>"contact.id_societe","condition_value"=>ATF::user()->cryptId($this->id_societe));
		$retour=$this->obj->autocompleteAvecMail($infos);
		$this->assertTrue(is_array($retour[0]));
		$this->assertEquals($retour[0][0],"debug@absystech.fr","autocompleteAvecMail ne renvoie pas le bon mail");
		$this->assertEquals($retour[0][2],"M contact test unitaire","autocompleteAvecMail ne renvoie pas le bon nom");
		$this->assertEquals($retour[0][3],"TestTU","autocompleteAvecMail ne renvoie pas le bon test");
		$this->assertEquals($retour[0]["raw_2"],"M contact test unitaire","autocompleteAvecMail ne renvoie pas le bon nom");
		$this->assertEquals($retour[0]["raw_3"],"TestTU","autocompleteAvecMail ne renvoie pas le bon test");
	}	

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	public function test_delete(){
		try {
			 $this->obj->can_delete($id_commande);
		} catch (error $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(879,$error,'can_delete ne doit pas laisser supprimer un contact');
	}	

	/*
		@author Quentin JANON <qjanon@absystech.fr> 
	*/ 
	public function testExport_vcard(){
		$societe = array(
			"societe"=>"societe TEST TU"
		);
		$societe['id_societe'] = ATF::societe()->i($societe);
		
		$contact = array(
			"nom"=>"BALOU"
			,"id_societe"=>$societe['id_societe']
		);
		$contact['id_contact'] = ATF::contact()->i($contact);

		ob_start();
		$this->obj->export_vcard(array('id'=>$contact['id_contact']));
		//récupération des infos
		$r=ob_get_contents();
		//suppression des éléments (dans tampon)
		ob_end_clean();
		
		$this->assertNotNull($r,"Erreur, aucun retour :/");
		
	}

	/*
		@author Quentin JANON <qjanon@absystech.fr> 
	*/ 
	public function test_createVcard(){
		$societe = array(
			"societe"=>"societe TEST TU"
		);
		$societe['id_societe'] = ATF::societe()->i($societe);
		
		$contact = array(
			"nom"=>"BALOU"
			,"id_societe"=>$societe['id_societe']
		);
		$contact['id_contact'] = ATF::contact()->i($contact);
		
		$r = $this->obj->createVcard($contact['id_contact']);
		
		$this->assertEquals("/home/optima/core/../temp/testsuite/contact/".$contact['id_contact'].".vcard",$r,"Le retour qui est le filepath n'est pas correct...");
		$this->assertFileExists($r,"Le fichcier vcard ne s'est pas créer...");

		$r = $this->obj->createVcard($contact['id_contact'],true,false);
		$this->assertEquals("0d8a379cd3db0e5a26a8e03915cdffbe",md5(file_get_contents($r)),"Le contenu de la VCARD est vraqué ??! WTF !");
	}

	/*
		@author Quentin JANON <qjanon@absystech.fr> 
	*/ 
	public function test_vcardToQRcode(){
		$societe = array(
			"societe"=>"societe TEST TU"
		);
		$societe['id_societe'] = ATF::societe()->i($societe);
		
		$contact = array(
			"nom"=>"BALOU"
			,"id_societe"=>$societe['id_societe']
		);
		$contact['id_contact'] = ATF::contact()->i($contact);
		
		$r = $this->obj->vcardToQRcode($contact['id_contact'],false);
		$this->assertEquals("contact-".$this->obj->cryptId($contact['id_contact'])."-qrcode-150.png",substr($r,0,-19),"Erreur de retour de l'URL de la vignette du qrcode");
		$fp = $this->obj->filepath($contact['id_contact'],"qrcode");
		$this->assertFileExists($fp,"Le fichier n'est pas sur le serveur");
	}
	
	public function test_getContactFromSociete() {
		$r = $this->obj->getContactFromSociete(1);
		foreach ($r as $k=>$i) {
			$this->assertEquals(1,$this->obj->select($i["raw_0"],"id_societe"),"Mauvais id société pour le contact : ".$i[1]);
			$this->assertEquals("actif",$i[6],"L'etat n'est pas actif, ne devrait pas remonter.");
		}
	}


};
?>