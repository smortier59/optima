<?

class relance_test extends ATF_PHPUnit_Framework_TestCase {
	
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 24-05-2011
	*/ 
	public function setUp() {
		$this->begin_transaction(true);
		$this->obj = ATF::relance();
		$this->obj->truncate();
		
		$this->facture["ref"]="FLITUTUTUTU";
		$this->facture["tva"]="20.0";
		$this->facture["date"]="2010-01-01";
		$this->facture["id_societe"]=ATF::$usr->get('id_societe');
		$this->facture["type_facture"]="facture";
		$this->facture["date_previsionnelle"]="2010-01-31";
		
		$this->facture["id_facture"] = ATF::facture()->i($this->facture);
		
	}
	
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 24-05-2011
	*/ 
	public function tearDown() {
		ATF::db()->rollback_transaction(true);
		//Flush des notices
		ATF::$msg->getNotices();
	}

	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 24-05-2011
	*/ 
	public function test_generateSansContact() {
		$this->assertFalse($this->obj->generate(),"Rien en entrée, doit retourner FALSE");
		$erreur = false;
		try {
			$erreur = false;
			$param = array('id_facture'=>$this->facture["id_facture"]);
			$r = $this->obj->generate($param);
		} catch (error $e) {
			$erreur = true;
		}
		$this->assertTrue($erreur,"Erreur non catché :/ ");
		$this->assertEquals(167,$e->getCode(),"Mauvais code d'erreur en retour");
		
	}

	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 24-05-2011
	*/ 
	public function test_generateSansEmailDeContact() {
		$contact["nom"]="Tu_devis";
		$contact["id_societe"]=ATF::$usr->get('id_societe');
		$this->id_contact=ATF::contact()->i($contact);

		$societe = array(
			"id_societe"=>ATF::$usr->get('id_societe')
			,"id_contact_facturation"=>$this->id_contact
		);
		ATF::societe()->u($societe);
		
		
		try {
			$erreur = false;
			$param = array('id_facture'=>$this->facture["id_facture"]);
			$r = $this->obj->generate($param);
		} catch (error $e) {
			$erreur = true;
		}
		$this->assertTrue($erreur,"Erreur non catché :/ ");
		$this->assertEquals(166,$e->getCode(),"Mauvais code d'erreur en retour");
		
	}
	
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 24-05-2011
	*/  
	public function test_generatePremiereRelance() {
		$contact["nom"]="Tu_devis";
		$contact["id_societe"]=ATF::$usr->get('id_societe');
		$contact["email"]="tu@absystech.fr";
		$this->id_contact=ATF::contact()->i($contact);

		$societe = array(
			"id_societe"=>ATF::$usr->get('id_societe')
			,"id_contact_facturation"=>$this->id_contact
		);
		ATF::societe()->u($societe);
		
		// fichier de facture
		$p = ATF::facture()->filepath($this->facture["id_facture"],"fichier_joint");
		util::file_put_contents($p,"YALLAH OHE");
		
		$param = array('id_facture'=>$this->facture["id_facture"]);
		$param["filestoattach"]["relance"] = true;
		$r = $this->obj->generate($param);
		$el = $this->obj->select($r);
		$this->assertNotNull($el,"Rien en retour, il devrait y avoir l'ID");
		$this->assertNotNull($el['date_1'],"Rien en date_1.");
		$this->assertNull($el['date_2'],"Une date en date_2?!.");
		$this->assertNull($el['date_demeurre'],"Une date en date_demeurre?!.");
		$this->assertNull($el['date_injonction'],"Une date en date_injonction?!.");
		$path = $this->obj->filepath($this->facture["id_facture"],"relance",true); 
		$this->assertFileExists($path,"Le fichier PDF n'a pas été créer :/");
		util::rm($path);
	}
	
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 24-05-2011
	*/  
	public function test_generateDeuxiemeRelance() {
		$contact["nom"]="Tu_devis";
		$contact["id_societe"]=ATF::$usr->get('id_societe');
		$contact["email"]="tu@absystech.fr";
		$this->id_contact=ATF::contact()->i($contact);

		$societe = array(
			"id_societe"=>ATF::$usr->get('id_societe')
			,"id_contact_facturation"=>$this->id_contact
		);
		ATF::societe()->u($societe);
		
		$param = array('id_facture'=>$this->facture["id_facture"]);
		$param["filestoattach"]["relance"] = true;
		$r = $this->obj->generate($param);
		$r = $this->obj->generate($param);
		$el = $this->obj->select($r);
		$this->assertNotNull($el,"Rien en retour, il devrait y avoir l'ID");
		$this->assertNotNull($el['date_1'],"Rien en date_1.");
		$this->assertNotNull($el['date_2'],"Rien en date_2?!.");
		$this->assertNull($el['date_demeurre'],"Une date en date_demeurre?!.");
		$this->assertNull($el['date_injonction'],"Une date en date_injonction?!.");
		$path = $this->obj->filepath($this->facture["id_facture"],"relance",true); 
		$this->assertFileExists($path,"Le fichier PDF n'a pas été créer :/");
		util::rm($path);
	}
	
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 24-05-2011
	*/  
	public function test_generateMiseEnDemeure() {
		$contact["nom"]="Tu_devis";
		$contact["id_societe"]=ATF::$usr->get('id_societe');
		$contact["email"]="tu@absystech.fr";
		$this->id_contact=ATF::contact()->i($contact);

		$societe = array(
			"id_societe"=>ATF::$usr->get('id_societe')
			,"id_contact_facturation"=>$this->id_contact
		);
		ATF::societe()->u($societe);
		
		$param = array('id_facture'=>$this->facture["id_facture"]);
		$param["filestoattach"]["relance"] = true;
		$this->obj->generate($param);
		$this->obj->generate($param);
		$r = $this->obj->generate($param);
		$el = $this->obj->select($r);
		$this->assertNotNull($el,"Rien en retour, il devrait y avoir l'ID");
		$this->assertNotNull($el['date_1'],"Rien en date_1.");
		$this->assertNotNull($el['date_2'],"Rien en date_2?!.");
		$this->assertNotNull($el['date_demeurre'],"Rien en date_demeurre?!.");
		$this->assertNull($el['date_injonction'],"Une date en date_injonction?!.");
		$path = $this->obj->filepath($this->facture["id_facture"],"relance",true); 
		$this->assertFileExists($path,"Le fichier PDF n'a pas été créer :/");
		util::rm($path);
	}
	
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 24-05-2011
	*/  
	public function test_generateInjonction() {
		$contact["nom"]="Tu_devis";
		$contact["id_societe"]=ATF::$usr->get('id_societe');
		$contact["email"]="tu@absystech.fr";
		$this->id_contact=ATF::contact()->i($contact);

		$societe = array(
			"id_societe"=>ATF::$usr->get('id_societe')
			,"id_contact_facturation"=>$this->id_contact
		);
		ATF::societe()->u($societe);
		
		$param = array('id_facture'=>$this->facture["id_facture"]);
		$param["filestoattach"]["relance"] = true;
		$this->obj->generate($param);
		$this->obj->generate($param);
		$this->obj->generate($param);
		$r = $this->obj->generate($param);
		$el = $this->obj->select($r);
		$this->assertNotNull($el,"Rien en retour, il devrait y avoir l'ID");
		$this->assertNotNull($el['date_1'],"Rien en date_1.");
		$this->assertNotNull($el['date_2'],"Rien en date_2?!.");
		$this->assertNotNull($el['date_demeurre'],"Rien en date_demeurre?!.");
		$this->assertNotNull($el['date_injonction'],"Rien en date_injonction?!.");
		$path = $this->obj->filepath($this->facture["id_facture"],"relance",true); 
		$this->assertFileExists($path,"Le fichier PDF n'a pas été créer :/");
		util::rm($path);
	}
	
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 24-05-2011
	*/  
	public function test_generateCycleTermine() {
		$contact["nom"]="Tu_devis";
		$contact["id_societe"]=ATF::$usr->get('id_societe');
		$contact["email"]="tu@absystech.fr";
		$this->id_contact=ATF::contact()->i($contact);

		$societe = array(
			"id_societe"=>ATF::$usr->get('id_societe')
			,"id_contact_facturation"=>$this->id_contact
		);
		ATF::societe()->u($societe);
		
		$param = array('id_facture'=>$this->facture["id_facture"]);
		$param["filestoattach"]["relance"] = true;
		$this->obj->generate($param);
		$this->obj->generate($param);
		$this->obj->generate($param);
		$this->obj->generate($param);
		
		try {
			$erreur = false;
			$r = $this->obj->generate($param);
		} catch (error $e) {
			$erreur = true;
		}
		$this->assertTrue($erreur,"Erreur non catchée");
		$this->assertEquals(168,$e->getCode(),"Code d'Erreur mauvais");

	}
	
};
?>