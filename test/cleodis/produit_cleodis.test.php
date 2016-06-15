<?
class produit_cleodis_test extends ATF_PHPUnit_Framework_TestCase {
	
	/** Méthode pré-test, exécute avant chaque test unitaire
	* besoin d'un user pour les traduction
	*/
	public function setUp() {
		$this->begin_transaction(true);
	}
	
	/** Méthode post-test, exécute après chaque test unitaire*/
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}

	/** @test Test du constructeur produit.  */
	public function test_constructeur(){
		$this->_produit = new produit_cleodis();	
	}


	public function test_speed_insert(){
		$produit=array("ref"=>"refTu",
					   "produit"=>"produitTu",
						"prix_achat"=>"100.00",
						"id_fabriquant"=>20,
						"id_sous_categorie"=>16,
						"id_fournisseur"=>1358,
						);
						
		$speed_insert=$this->obj->speed_insert($produit);
		$this->assertEquals(
							array(
								"nom"=>"produitTu"
								,"id"=>$speed_insert["id"]
								,"data"=>array(
											"id_produit"=>$speed_insert["id"],
											"ref"=>"refTu",
											"produit"=>"produitTu",
											"prix_achat"=>"100.00",
											"id_fabriquant"=>"20",
											"id_sous_categorie"=>"16",
											"type"=>"fixe",
											"id_fournisseur"=>"1358",
											"code"=>NULL,
											"obsolete"=>"non",
											"id_produit_dd"=>NULL,
											"id_produit_dotpitch"=>NULL,
											"id_produit_format"=>NULL,
											"id_produit_garantie_uc"=>NULL,
											"id_produit_garantie_ecran"=>NULL,
											"id_produit_garantie_imprimante"=>NULL,
											"id_produit_lan"=>NULL,
											"id_produit_lecteur"=>NULL,
											"id_produit_OS"=>NULL,
											"id_produit_puissance"=>NULL,
											"id_produit_ram"=>NULL,
											"id_produit_technique"=>NULL,
											"id_produit_type"=>NULL,
											"id_produit_typeecran"=>NULL,
											"id_produit_viewable"=>NULL,
											"id_processeur"=>NULL,
											"etat"=>"actif",
											"commentaire"=>NULL,
											"fournisseur"=>"AUDIOPTIC TRADE SERVICES",
											"type_offre"=>NULL,
											"description"=>NULL,
											"loyer"=>NULL,
											"duree"=>NULL,
											"loyer1"=>NULL,
											"duree1"=>NULL,
											"duree2"=>NULL,
											"loyer2"=>NULL,
											"visible_sur_site"=>'non',
											"id_produit_env"=>NULL,
											"id_produit_besoins"=>NULL,
											"id_produit_tel_produit"=>NULL,
											"id_produit_tel_type"=>NULL,
											"avis_expert"=>NULL,
											"services"=>NULL, 
											"url"=>"produittu"
										)
							),
							$speed_insert,
							'speed_insert ne renvoi pas les bonnes infos'
							);
	}

	public function test_speed_insert_template(){
		$infos=array("id"=>"devis[produits]",
					   "id_produit"=>"8",
						"parent_class"=>"devis_ligne"
						);	
						
		$speed_insert_template=$this->obj->speed_insert_template($infos);
		$this->assertNotNull($speed_insert_template,"speed_insert_template ne retourne rien");
		$this->assertEquals(array("id"=>"devis[produits]",
					   "id_produit"=>"8",
						"parent_class"=>"devis_ligne",
						"display"=>true
						),$infos,"speed_insert_template ne retourne rien");

	}

	public function test_insert(){
		$produit=array("ref"=>"O2-SRV-226-001",
					   "produit"=>"produitTu",
						"prix_achat"=>"100.00",
						"id_fabriquant"=>20,
						"id_sous_categorie"=>16,
						"id_fournisseur"=>1358,
						);	
						
		try {
			$insert=$this->obj->insert($produit);
		} catch (errorATF $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(987,$error,'Erreur ref déjà utilisé');
						
		$produit=array("ref"=>"refTu",
					   "produit"=>"produitTu",
						"prix_achat"=>"100.00",
						"id_fabriquant"=>20,
						"id_sous_categorie"=>16,
						"id_fournisseur"=>1358
						);	
		$produit=$this->obj->insert($produit);
		$this->assertNotNull($produit,"Produit non inséré");	

	}


	public function test_setInfos(){
		$produit=array("ref"=>"refTu",
					   "produit"=>"produitTu",
						"prix_achat"=>"100.00",
						"id_fabriquant"=>20,
						"id_sous_categorie"=>16,
						"id_fournisseur"=>1358
						);	
		$produit=$this->obj->insert($produit);

		$this->obj->setInfos(array("id_produit"=>$produit,
								   "field"=>"produit",
								   "produit"=>"produitTu setInfos"
								  ));
		$this->assertEquals("produitTu setInfos",$this->obj->select($produit, "produit"),'Erreur setInfos');

		ATF::$msg->getNotices();
	}

	public function test_EtatUpdate(){
		$produit=array("ref"=>"refTu",
					   "produit"=>"produitTu",
						"prix_achat"=>"100.00",
						"id_fabriquant"=>20,
						"id_sous_categorie"=>16,
						"id_fournisseur"=>1358
						);	
		$produit=$this->obj->insert($produit);

		$this->obj->EtatUpdate(array("id_produit"=>$produit,
								   "field"=>"visible_sur_site",
								   "visible_sur_site"=>"oui"
								  ));
		$this->assertEquals("oui",$this->obj->select($produit, "visible_sur_site"),'Erreur EtatUpdate');

		ATF::$msg->getNotices();
	}

	public function test_autocomplete(){
		$produit=array(
						"start"=>0,
					   "limit"=>0,
					   "query"=>"ABSTEL-PRESTA-AUT"
						);	
						
		$autocomplete=$this->obj->autocomplete($produit);
		$this->assertEquals("Configuration Easy2Call 50 – Basic",$autocomplete[0][0],"autocomplete ne renvoi pas le bon prodit");
		$this->assertEquals("sans_objet",$autocomplete[0][1],"autocomplete ne renvoi pas le bon type");
		$this->assertEquals('<span class="searchSelectionFound">ABSTEL-PRESTA-AUT</span>',$autocomplete[0][2],"autocomplete ne renvoi pas le bon label");
		$this->assertEquals("ABSYSTECH TELECOM",$autocomplete[0][3],"autocomplete ne renvoi pas le bon fournisseur");
		$this->assertEquals("450.00",$autocomplete[0][4],"autocomplete ne renvoi pas le bon prix");
		$this->assertEquals("ABSYST. TEL",$autocomplete[0][6],"autocomplete ne renvoi pas le bon nom fournisseur");

		$this->assertEquals("Configuration Easy2Call 50 – Basic",$autocomplete[0]["raw_0"],"autocomplete ne renvoi pas le bon prodit");
		$this->assertEquals("sans_objet",$autocomplete[0]["raw_1"],"autocomplete ne renvoi pas le bon type");
		$this->assertEquals('ABSTEL-PRESTA-AUT',$autocomplete[0]["raw_2"],"autocomplete ne renvoi pas le bon label");
		$this->assertEquals("ABSYSTECH TELECOM",$autocomplete[0]["raw_3"],"autocomplete ne renvoi pas le bon fournisseur");
		$this->assertEquals("450.00",$autocomplete[0]["raw_4"],"autocomplete ne renvoi pas le bon prix");
		$this->assertEquals("ABSYST. TEL",$autocomplete[0]["raw_6"],"autocomplete ne renvoi pas le bon nom fournisseur");
	}

	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
	public function test_getDataFromIcecatEcran() {
		$r = $this->obj->getDataFromIcecat("DELL","10646015");



		$this->assertNotNull($r['produit'],"Il devrait y avoir un retour pour l'entrée 'produit'");
		$this->assertEquals("LG 24EB23PM-B LED display",$r['produit'],"Le produit n'a pas le bon libellé, aurait-il changer ?");

		$this->assertNotNull($r['type_ecran'],"Il devrait y avoir un retour pour l'entrée 'type_ecran'");
		$this->assertEquals("LED",$r['type_ecran'],"Le type_ecran n'a pas le bon libellé, aurait-il changer ?");

		$this->assertNotNull($r['taille_ecran'],"Il devrait y avoir un retour pour l'entrée 'taille_ecran'");
		$this->assertEquals("60,96 cm (24\")",$r['taille_ecran'],"Le taille_ecran n'a pas le bon libellé, aurait-il changer ?");

	}

	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
	public function test_getDataFromIcecatImprimante() {
		$r = $this->obj->getDataFromIcecat("Xerox","560V_F");

		$this->assertNotNull($r['produit'],"Il devrait y avoir un retour pour l'entrée 'produit'");
		$this->assertEquals("Xerox Color 560V/F",$r['produit'],"Le produit n'a pas le bon libellé, aurait-il changer ?");

		$this->assertNotNull($r['mem'],"Il devrait y avoir un retour pour l'entrée 'mem'");
		$this->assertEquals("2048 Mo",$r['mem'],"Le mem n'a pas le bon libellé, aurait-il changer ?");

		$this->assertNotNull($r['type_ecran'],"Il devrait y avoir un retour pour l'entrée 'type_ecran'");
		$this->assertEquals("LCD",$r['type_ecran'],"Le type_ecran n'a pas le bon libellé, aurait-il changer ?");

		$this->assertNotNull($r['taille_ecran'],"Il devrait y avoir un retour pour l'entrée 'taille_ecran'");
		$this->assertEquals("26,4 cm (10.4\")",$r['taille_ecran'],"Le taille_ecran n'a pas le bon libellé, aurait-il changer ?");

		$this->assertNotNull($r['tech_impression'],"Il devrait y avoir un retour pour l'entrée 'tech_impression'");
		$this->assertEquals("Laser",$r['tech_impression'],"Le tech_impression n'a pas le bon libellé, aurait-il changer ?");
		
		$this->assertNotNull($r['format_impression'],"Il devrait y avoir un retour pour l'entrée 'format_impression'");
		$this->assertEquals("A3, A4, A5",$r['format_impression'],"Le format_impression n'a pas le bon libellé, aurait-il changer ?");
	}

	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
	public function test_getDataFromIcecatOrdi() {
		$r = $this->obj->getDataFromIcecat("Samsung","XE303C12-A01US");

		$this->assertNotNull($r['produit'],"Il devrait y avoir un retour pour l'entrée 'produit'");
		$this->assertEquals("Samsung XE series XE303C12",$r['produit'],"Le produit n'a pas le bon libellé, aurait-il changer ?");

		//$this->assertNotNull($r['proc_puissance'],"Il devrait y avoir un retour pour l'entrée 'proc_puissance'");
		//$this->assertEquals("2 x 1,7 GHz",$r['proc_puissance'],"Le proc_puissance n'a pas le bon libellé, aurait-il changer ?");

		$this->assertNotNull($r['mem'],"Il devrait y avoir un retour pour l'entrée 'mem'");
		$this->assertEquals("2 Go",$r['mem'],"Le mem n'a pas le bon libellé, aurait-il changer ?");

		$this->assertNotNull($r['lecteur'],"Il devrait y avoir un retour pour l'entrée 'lecteur'");
		$this->assertEquals("Non",$r['lecteur'],"Le lecteur n'a pas le bon libellé, aurait-il changer ?");

		$this->assertNotNull($r['os'],"Il devrait y avoir un retour pour l'entrée 'os'");
		$this->assertEquals("Chrome OS",$r['os'],"Le os n'a pas le bon libellé, aurait-il changer ?");

		$this->assertNotNull($r['taille_ecran'],"Il devrait y avoir un retour pour l'entrée 'taille_ecran'");
		$this->assertEquals("29,5 cm (11.6\")",$r['taille_ecran'],"Le taille_ecran n'a pas le bon libellé, aurait-il changer ?");
	}

	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
	public function test_getDataFromIcecatThrow() {

		$erreur = false;
		try {
			$r = $this->obj->getDataFromIcecat("Saxxxmsundfgg","XE303C12-Asdfgdfs01US");
		} catch (errorATF $e) {
			$erreur = true;
		}

		log::logger($r , "mfleurquin");

		$this->assertTrue($erreur,"L'erreur ne remonte pas ?");
		$this->assertEquals(910,$e->getCode(),"L'erreur ne remonte pas ?");

	}

	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
	public function test_getInfosFromICECATThrow() {

		$erreur = false;
		try {
			$r = $this->obj->getInfosFromICECAT();
		} catch (errorATF $e) {
			$erreur = true;
		}

		$this->assertTrue($erreur,"L'erreur ne remonte pas ?");


	}

	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
	public function test_getInfosFromICECAT() {

		ATF::setSingleton("produit", new mock_produit_cleodis());
		$r = $this->obj->getInfosFromICECAT(array("ref"=>"toto","id_fabriquant"=>1));

		$this->assertEquals("Disque dur",$r['produit'],"Erreur de remonté du produit");
		
		$this->assertEquals("Disque dur",$r['dd']['libelle'],"Erreur de remonté du libelle dd");
		$this->assertNotNull($r['dd']['id_produit_dd'],"ID dd doit être présent");

		$this->assertEquals("Disque dur",$r['typeecran']['libelle'],"Erreur de remonté du libelle typeecran");
		$this->assertNotNull($r['typeecran']['id_produit_typeecran'],"ID typeecran doit être présent");

		$this->assertEquals("Disque dur",$r['tailleecran']['libelle'],"Erreur de remonté du libelle tailleecran");
		$this->assertNotNull($r['tailleecran']['id_produit_viewable'],"ID tailleecran doit être présent");

		$this->assertEquals("Disque dur",$r['proc_modele']['libelle'],"Erreur de remonté du libelle proc_modele");
		$this->assertNotNull($r['proc_modele']['id_processeur'],"ID proc_modele doit être présent");

		$this->assertEquals("Disque dur",$r['proc_puissance']['libelle'],"Erreur de remonté du libelle proc_puissance");
		$this->assertNotNull($r['proc_puissance']['id_produit_puissance'],"ID proc_puissance doit être présent");

		$this->assertEquals("Disque dur",$r['mem']['libelle'],"Erreur de remonté du libelle mem");
		$this->assertNotNull($r['mem']['id_produit_ram'],"ID mem doit être présent");

		$this->assertEquals("Disque dur",$r['lecteur']['libelle'],"Erreur de remonté du libelle lecteur");
		$this->assertNotNull($r['lecteur']['id_produit_lecteur'],"ID lecteur doit être présent");

		$this->assertEquals("Disque dur",$r['reseau']['libelle'],"Erreur de remonté du libelle reseau");
		$this->assertNotNull($r['reseau']['id_produit_lan'],"ID reseau doit être présent");

		$this->assertEquals("Disque dur",$r['os']['libelle'],"Erreur de remonté du libelle os");
		$this->assertNotNull($r['os']['id_produit_OS'],"ID os doit être présent");

		$this->assertEquals("Disque dur",$r['tech_impression']['libelle'],"Erreur de remonté du libelle tech_impression");
		$this->assertNotNull($r['tech_impression']['id_produit_technique'],"ID tech_impression doit être présent");

		$this->assertEquals("Disque dur",$r['format_impression']['libelle'],"Erreur de remonté du libelle format_impression");
		$this->assertNotNull($r['format_impression']['id_produit_format'],"ID format_impression doit être présent");
		
	}

	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
	public function test_dealWith() {
		$r = $this->obj->dealWith('produit_dd',"Fucking SSD DD Zorian");

		$this->assertEquals("Fucking SSD DD Zorian",$r['libelle'],"Erreur de remonté du libellé format_impression");
		$this->assertNotNull($r['id_produit_dd'],"Id_produit_dd doit être présent");

		$r2 = $this->obj->dealWith('produit_dd',"Fucking SSD DD Zorian");

		$this->assertEquals($r['id_produit_dd'],$r2['id_produit_dd'],"Erreur de récupération d'un ID existant !");
	}


	/**
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test_update() {
		$produit=array("ref"=>"refTu",
					   "produit"=>"produitTu",
						"prix_achat"=>"100.00",
						"id_fabriquant"=>20,
						"id_sous_categorie"=>16,
						"id_fournisseur"=>1358,
						);	
		$produit=$this->obj->insert($produit);
		$this->assertNotNull($produit,"Produit non inséré");	


		$this->obj->update(array("id_produit" => $produit , "produit"=> "ProduitUpdate"));

		$this->assertEquals("ProduitUpdate" , $this->obj->select($produit , "produit") , "Le produit ne s'est pas updaté !");
	}


	/* @author Morgan FLEURQUIN <mfleurquin@absystech.fr> */
	public function test_construct(){
		ATF::db()->select_db("extranet_v3_exactitude");
		$c = new produit_exactitude();	
		
		$this->assertTrue($c instanceOf produit_exactitude, "L'objet produit_exactitude n'est pas de bon type");
		
		ATF::db()->select_db("extranet_v3_cleodis");	
	}



	// @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	private function beginTransaction($codename){		
		ATF::db()->select_db("extranet_v3_".$codename);
    	ATF::$codename = $codename;
    	ATF::db()->begin_transaction(true);		
	}

	// @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	private function RollBackTransaction($codename){	
		ATF::db()->rollback_transaction(true);
        ATF::$codename = "cleodis";
        ATF::db()->select_db("extranet_v3_cleodis");
	}

	/** @test Test du constructeur produit.  */
	public function test_constructeurCap(){
		$this->beginTransaction("cap");

		$c = new produit_cap();	

		$this->RollBackTransaction("cleodis");

		$this->assertTrue($c instanceOf produit_cap, "L'objet produit_cap n'est pas de bon type");	
	}


	/** @test Test du constructeur produit.  */
	public function test_InsertCap(){
		$this->beginTransaction("cap");

		$infos = array("ref"=>"Test produit CAP",
					   "produit"=>"Produit CAP",
					   "prix_achat"=>500,
					   "prix"=>800
					  );

		$c = new produit_cap();	
		$id = $c->insert($infos);

		$prod = $c->select($id);

		$this->RollBackTransaction("cleodis");

		$this->assertEquals("produit-cap", $prod["url"], "Insert Error");
	}

	
	public function test_autocompleteCap(){
		$this->beginTransaction("cap");

		$infos = array("ref"=>"Test produit CAP",
					   "produit"=>"Produit CAP",
					   "prix_achat"=>500,
					   "prix"=>800
					  );

		$c = new produit_cap();	
		$id= $c->insert($infos);

		$prod = $c->autocomplete(array(
			"limit"=>10
			,"start"=>0
			,"condition_field"=>"produit.produit"
			,"condition_value"=>"Produit CAP"
		));		

		$this->RollBackTransaction("cleodis");

		$res = array(0=> "Produit CAP",
					 1=> "Test produit CAP",
					 2=> "500.00",
					 3=> $id,
					 4=> "",
					 "raw_0"=> "Produit CAP",
					 "raw_1"=> "Test produit CAP",
					 "raw_2"=> "500.00",
					 "raw_3"=> $id,
					 "raw_4"=> ""	);

		$this->assertEquals($res, $prod[0], "Error autocomplete");
	}

	public function test_autocompleteexactitude(){
		$this->beginTransaction("exactitude");

		$infos = array("ref"=>"Test produit exactitude",
					   "produit"=>"Produit exactitude",
					   "prix_achat"=>500,
					   "prix"=>800
					  );

		$c = new produit_exactitude();	
		$id= $c->insert($infos);

		$prod = $c->autocomplete(array(
			"limit"=>10
			,"start"=>0
			,"condition_field"=>"produit.produit"
			,"condition_value"=>"Produit exactitude"
		));		

		$this->RollBackTransaction("cleodis");

		$res = array(0=> "Produit exactitude",
					 1=> "Test produit exactitude",
					 2=> "500.00",
					 3=> "800.00",
					 4=> $id,
					 5=>"",
					 "raw_0"=> "Produit exactitude",
					 "raw_1"=> "Test produit exactitude",
					 "raw_2"=> "500.00",
					 "raw_3"=> "800.00",
					 "raw_4"=> $id,
					 "raw_5"=>""	);

		$this->assertEquals($res, $prod[0], "Error autocomplete");
	}






	/** @test Test du constructeur produit.  */
	public function test_InsertExactitude(){
		$this->beginTransaction("exactitude");

		$infos = array("ref"=>"Test produit exactitude",
					   "produit"=>"Produit exactitude",
					   "prix_achat"=>500,
					   "prix"=>800
					  );

		$c = new produit_exactitude();	
		$id = $c->insert($infos);

		$prod = $c->select($id);

		$this->RollBackTransaction("cleodis");

		$this->assertEquals("produit-exactitude", $prod["url"], "Insert Error");
	}



	public function test_speed_insert_exactitude(){
		$this->beginTransaction("exactitude");

		$c = new produit_exactitude();	

		$produit=array("ref"=>"refTu",
					   "produit"=>"produitTu",
					   "prix_achat"=>"100.00",
					   "prix"=>800						
					  );
						
		$speed_insert=$c->speed_insert($produit);

		$this->RollBackTransaction("cleodis");

		$this->assertEquals(
							array(
								"nom"=>"produitTu"
								,"id"=>$speed_insert["id"]
								,"data"=>array(
											"id_produit"=>$speed_insert["id"],
											"ref"=>"refTu",
											"produit"=>"produitTu",
											"prix_achat"=>"100.00",
											"prix"=>"800.00",											
											"commentaire"=>NULL,
											"type_offre"=>NULL,
											"description"=>NULL,																					
											"visible_sur_site"=>'non',											
											"avis_expert"=>NULL,											
											"url"=>"produittu"
										)
							),
							$speed_insert,
							'speed_insert ne renvoi pas les bonnes infos'
							);
	}

	public function test_speed_insert_cap(){
		$this->beginTransaction("cap");

		$c = new produit_cap();	

		$produit=array("ref"=>"refTu",
					   "produit"=>"produitTu",
					   "prix_achat"=>"100.00",
					   "prix"=>800						
					  );
						
		$speed_insert=$c->speed_insert($produit);

		$this->RollBackTransaction("cleodis");

		$this->assertEquals(
							array(
								"nom"=>"produitTu"
								,"id"=>$speed_insert["id"]
								,"data"=>array(
											"id_produit"=>$speed_insert["id"],
											"ref"=>"refTu",
											"produit"=>"produitTu",
											"prix_achat"=>"100.00",
											"prix"=>"800.00",											
											"commentaire"=>NULL,
											"type_offre"=>NULL,
											"description"=>NULL,																					
											"visible_sur_site"=>'non',											
											"avis_expert"=>NULL,											
											"url"=>"produittu"
										)
							),
							$speed_insert,
							'speed_insert ne renvoi pas les bonnes infos'
							);
	}

	public function test_speed_insert_template_cap(){
		$this->beginTransaction("cap");

		
		$infos = array("ref"=>"Test produit cap",
					   "produit"=>"Produit cap",
					   "prix_achat"=>500,
					   "prix"=>800
					  );

		$c = new produit_cap();	
		$id = $c->insert($infos);

		$infos=array("id"=>"devis[produits]",
					   "id_produit"=>$id,
						"parent_class"=>"devis_ligne"
						);	
						
		$speed_insert_template=$c->speed_insert_template($infos);
		
		$this->RollBackTransaction("cleodis");

		$this->assertNotNull($speed_insert_template,"speed_insert_template ne retourne rien");
		$this->assertEquals(array("id"=>"devis[produits]",
					   "id_produit"=>$id,
						"parent_class"=>"devis_ligne",
						"display"=>true
						),$infos,"speed_insert_template ne retourne rien");

	}


	public function test_speed_insert_template_exactitude(){
		$this->beginTransaction("exactitude");

		
		$infos = array("ref"=>"Test produit exactitude",
					   "produit"=>"Produit exactitude",
					   "prix_achat"=>500,
					   "prix"=>800
					  );

		$c = new produit_exactitude();	
		$id = $c->insert($infos);

		$infos=array("id"=>"devis[produits]",
					   "id_produit"=>$id,
						"parent_class"=>"devis_ligne"
						);	
						
		$speed_insert_template=$c->speed_insert_template($infos);
		
		$this->RollBackTransaction("cleodis");

		$this->assertNotNull($speed_insert_template,"speed_insert_template ne retourne rien");
		$this->assertEquals(array("id"=>"devis[produits]",
					   "id_produit"=>$id,
						"parent_class"=>"devis_ligne",
						"display"=>true
						),$infos,"speed_insert_template ne retourne rien");

	}



/**
	* @author Quentin JANON <qjanon@absystech.fr>
	
	public function test_getDataFromIcecatImprimante() {
		$r = $this->obj->getDataFromIcecat("Xerox","560V_F");

		$this->assertNotNull($r['produit'],"Il devrait y avoir un retour pour l'entrée 'produit'");
		$this->assertEquals("Xerox Color 560V/F",$r['produit'],"Le produit n'a pas le bon libellé, aurait-il changer ?");

		$this->assertNotNull($r['mem'],"Il devrait y avoir un retour pour l'entrée 'mem'");
		$this->assertEquals("2048 Mo",$r['mem'],"Le mem n'a pas le bon libellé, aurait-il changer ?");

		$this->assertNotNull($r['type_ecran'],"Il devrait y avoir un retour pour l'entrée 'type_ecran'");
		$this->assertEquals("LCD",$r['type_ecran'],"Le type_ecran n'a pas le bon libellé, aurait-il changer ?");

		$this->assertNotNull($r['taille_ecran'],"Il devrait y avoir un retour pour l'entrée 'taille_ecran'");
		$this->assertEquals("26,4 cm (10.4\")",$r['taille_ecran'],"Le taille_ecran n'a pas le bon libellé, aurait-il changer ?");

		$this->assertNotNull($r['tech_impression'],"Il devrait y avoir un retour pour l'entrée 'tech_impression'");
		$this->assertEquals("Laser",$r['tech_impression'],"Le tech_impression n'a pas le bon libellé, aurait-il changer ?");
		
		$this->assertNotNull($r['format_impression'],"Il devrait y avoir un retour pour l'entrée 'format_impression'");
		$this->assertEquals("A3, A4, A5",$r['format_impression'],"Le format_impression n'a pas le bon libellé, aurait-il changer ?");
	}

	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	
	public function test_getDataFromIcecatOrdi() {
		$r = $this->obj->getDataFromIcecat("Samsung","XE303C12-A01US");

		$this->assertNotNull($r['produit'],"Il devrait y avoir un retour pour l'entrée 'produit'");
		$this->assertEquals("Samsung XE series XE303C12",$r['produit'],"Le produit n'a pas le bon libellé, aurait-il changer ?");

		//$this->assertNotNull($r['proc_puissance'],"Il devrait y avoir un retour pour l'entrée 'proc_puissance'");
		//$this->assertEquals("2 x 1,7 GHz",$r['proc_puissance'],"Le proc_puissance n'a pas le bon libellé, aurait-il changer ?");

		$this->assertNotNull($r['mem'],"Il devrait y avoir un retour pour l'entrée 'mem'");
		$this->assertEquals("2 Go",$r['mem'],"Le mem n'a pas le bon libellé, aurait-il changer ?");

		$this->assertNotNull($r['lecteur'],"Il devrait y avoir un retour pour l'entrée 'lecteur'");
		$this->assertEquals("Non",$r['lecteur'],"Le lecteur n'a pas le bon libellé, aurait-il changer ?");

		$this->assertNotNull($r['os'],"Il devrait y avoir un retour pour l'entrée 'os'");
		$this->assertEquals("Chrome OS",$r['os'],"Le os n'a pas le bon libellé, aurait-il changer ?");

		$this->assertNotNull($r['taille_ecran'],"Il devrait y avoir un retour pour l'entrée 'taille_ecran'");
		$this->assertEquals("30 cm (12\")",$r['taille_ecran'],"Le taille_ecran n'a pas le bon libellé, aurait-il changer ?");
	}

	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	
	public function test_getDataFromIcecatThrow() {

		$erreur = false;
		try {
			$r = $this->obj->getDataFromIcecat("Saxxxmsundfgg","XE303C12-Asdfgdfs01US");
		} catch (errorATF $e) {
			$erreur = true;
		}

		$this->assertTrue($erreur,"L'erreur ne remonte pas ?");
		$this->assertEquals(910,$e->getCode(),"L'erreur ne remonte pas ?");

	}

	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	
	public function test_getInfosFromICECATThrow() {

		$erreur = false;
		try {
			$r = $this->obj->getInfosFromICECAT();
		} catch (errorATF $e) {
			$erreur = true;
		}

		$this->assertTrue($erreur,"L'erreur ne remonte pas ?");


	}

	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	
	public function test_getInfosFromICECAT() {

		ATF::setSingleton("produit", new mock_produit_cleodis());
		$r = $this->obj->getInfosFromICECAT(array("ref"=>"toto","id_fabriquant"=>1));

		$this->assertEquals("Disque dur",$r['produit'],"Erreur de remonté du produit");
		/*
		$this->assertEquals("Disque dur",$r['dd']['libelle'],"Erreur de remonté du libelle dd");
		$this->assertNotNull($r['dd']['id_produit_dd'],"ID dd doit être présent");

		$this->assertEquals("Disque dur",$r['typeecran']['libelle'],"Erreur de remonté du libelle typeecran");
		$this->assertNotNull($r['typeecran']['id_produit_typeecran'],"ID typeecran doit être présent");

		$this->assertEquals("Disque dur",$r['tailleecran']['libelle'],"Erreur de remonté du libelle tailleecran");
		$this->assertNotNull($r['tailleecran']['id_produit_viewable'],"ID tailleecran doit être présent");

		$this->assertEquals("Disque dur",$r['proc_modele']['libelle'],"Erreur de remonté du libelle proc_modele");
		$this->assertNotNull($r['proc_modele']['id_processeur'],"ID proc_modele doit être présent");

		$this->assertEquals("Disque dur",$r['proc_puissance']['libelle'],"Erreur de remonté du libelle proc_puissance");
		$this->assertNotNull($r['proc_puissance']['id_produit_puissance'],"ID proc_puissance doit être présent");

		$this->assertEquals("Disque dur",$r['mem']['libelle'],"Erreur de remonté du libelle mem");
		$this->assertNotNull($r['mem']['id_produit_ram'],"ID mem doit être présent");

		$this->assertEquals("Disque dur",$r['lecteur']['libelle'],"Erreur de remonté du libelle lecteur");
		$this->assertNotNull($r['lecteur']['id_produit_lecteur'],"ID lecteur doit être présent");

		$this->assertEquals("Disque dur",$r['reseau']['libelle'],"Erreur de remonté du libelle reseau");
		$this->assertNotNull($r['reseau']['id_produit_lan'],"ID reseau doit être présent");

		$this->assertEquals("Disque dur",$r['os']['libelle'],"Erreur de remonté du libelle os");
		$this->assertNotNull($r['os']['id_produit_OS'],"ID os doit être présent");

		$this->assertEquals("Disque dur",$r['tech_impression']['libelle'],"Erreur de remonté du libelle tech_impression");
		$this->assertNotNull($r['tech_impression']['id_produit_technique'],"ID tech_impression doit être présent");

		$this->assertEquals("Disque dur",$r['format_impression']['libelle'],"Erreur de remonté du libelle format_impression");
		$this->assertNotNull($r['format_impression']['id_produit_format'],"ID format_impression doit être présent");
		

	}

	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	
	public function test_dealWith() {
		$r = $this->obj->dealWith('produit_dd',"Fucking SSD DD Zorian");

		$this->assertEquals("Fucking SSD DD Zorian",$r['libelle'],"Erreur de remonté du libellé format_impression");
		$this->assertNotNull($r['id_produit_dd'],"Id_produit_dd doit être présent");

		$r2 = $this->obj->dealWith('produit_dd',"Fucking SSD DD Zorian");

		$this->assertEquals($r['id_produit_dd'],$r2['id_produit_dd'],"Erreur de récupération d'un ID existant !");
	}
*/


};


class mock_produit_cleodis extends produit_cleodis {

	public function getDataFromIcecat() {
		$r = array(
			"produit"=>"Disque dur",
			"dd"=>"Disque dur",
			"type_ecran"=>"Disque dur",
			"taille_ecran"=>"Disque dur",
			"proc_modele"=>"Disque dur",
			"proc_puissance"=>"Disque dur",
			"mem"=>"Disque dur",
			"lecteur"=>"Disque dur",
			"reseau"=>"Disque dur",
			"os"=>"Disque dur",
			"tech_impression"=>"Disque dur",
			"format_impression"=>"Disque dur",
		);
		return $r;
	}
};
?>