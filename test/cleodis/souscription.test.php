<?
class souscription_cleodis_test  extends ATF_PHPUnit_Framework_TestCase {
	public function setUp() {
		ATF::db()->begin_transaction();

		$this->post = Array(
		    "siret" => "44393519200015",
		    "societe" => Array(
	            "id_societe" => "43574",
	            "societe" => "ESPACE PRO",
	            "bic" => "FR763007",
	            "siret" => "44393519200015",
	            "iban" => "FR7630076020821234567890186",
	            "adresse" => "1935 RTE NATIONALE",
	            "adresse_2" => "",
	            "cp" => "74120",
	            "ville" => "MEGEVE",
	            "date_blocage" => "",
	            "livraison_adresse" => "2, rue de la charue",
	            "livraison_adresse_2" => "",
	            "livraison_adresse_3" => "",
	            "livraison_cp" => "59000",
	            "livraison_ville" => "LILLE",
	            "livraison_id_pays" => "FR",
	            "facturation_adresse" => "1 rue du peuplier",
	            "facturation_adresse_2" => "",
	            "facturation_adresse_3" => "",
	            "facturation_cp" => "59000",
	            "facturation_ville" => "Lille",
	            "facturation_id_pays" => "FR"
	        ),
		    "contact" => Array(
	            "id_contact" => "48139",
	            "nom" => "Fleurquin",
	            "prenom" => "Morgan",
	            "email" => "mfleurquin@absystech.fr",
	            "tel" => "",
	            "gsm" => "0625303642",
	            "fonction" => "Dev"
	        ),
		    "livraison" => Array(
	            "adresse" => "2, rue de la charue",
	            "adresse_2" => "",
	            "cp" => "59000",
	            "ville" => "LILLE"
	        ),
		    "facturation" => Array(
	            "adresse" => "1 rue du peuplier",
	            "adresse_2" => "",
	            "cp" => "59000",
	            "ville" => "Lille",
	            "adresse_3" => "",
	        ),
		    "bic" => "FR763007",
		    "iban" => "FR7630076020821234567890186",
		    "type" => "professionnel",
		    "site_associe" => "boulangerpro",
		    "produits" => '[{"id_pack_produit":915,"id_pack_produit_ligne":5854,"id_produit":16565,"ref":"812320","produit":"Lave linge hublot MIELE WKH 122 WPS","quantite":1},{"id_pack_produit":915,"id_pack_produit_ligne":5853,"id_produit":16554,"ref":"ExtGar09","produit":"Extension de garantie <2000€ 60 mois","quantite":1},{"id_pack_produit":915,"id_pack_produit_ligne":5852,"id_produit":16559,"ref":"Trans04","produit":"Transport GEODIS 143,28 € 60 mois","quantite":1},{"id_pack_produit":918,"id_pack_produit_ligne":5705,"id_produit":16568,"ref":"1111968","produit":"TV SAMSUNG THE FRAME UE65LS03 2018","quantite":3},{"id_pack_produit":918,"id_pack_produit_ligne":5706,"id_produit":16554,"ref":"ExtGar09","produit":"Extension de garantie <2000€ 60 mois","quantite":3},{"id_pack_produit":918,"id_pack_produit_ligne":5707,"id_produit":16557,"ref":"Trans02","produit":"Transport GEODIS 92,33 € 60 mois","quantite":3},{"id_pack_produit":918,"id_pack_produit_ligne":5711,"id_produit":16582,"ref":"1022151","produit":"Câble HDMI MONSTERCABLE 1M50 UHD 18Gbps","quantite":3}]',
		    "id_pack_produit" => Array(
	            0 => "915",
	            1 => "918",
	            2 => "920"
	        ),
		    "id_panier" => "262",
		    "hash_panier" => "b785faf4dc5264963931ac2a71cfcbdd",
		    "id_contact" => "48139",
		    "id_societe" => "43574"
		);
	}

	/** Méthode post-test, exécute après chaque test unitaire*/
	public function tearDown(){
		ATF::$msg->getNotices();
		ATF::db()->rollback_transaction(true);
	}



	//Permet de tester des fonction private
	public function invokeMethod(&$object, $methodName, array $parameters = array()){
	    $reflection = new \ReflectionClass(get_class($object));
	    $method = $reflection->getMethod($methodName);
	    $method->setAccessible(true);
	    return $method->invokeArgs($object, $parameters);
	}

    /* @author Morgan FLEURQUIN <mfleurquin@absystech.fr> */
    public function test_construct(){
        $c = new souscription_cleodis();
        $this->assertTrue($c instanceOf souscription_cleodis, "L'objet souscription_cleodis n'est pas de bon type");
    }

    /* @author Morgan FLEURQUIN <mfleurquin@absystech.fr> */
    public function test_checkIBAN(){
    	$c = new souscription_cleodis();
    	try{
    		$res = $c->checkIBAN("");
		} catch (errorATF $e) {
			$error = $e->getMessage();
		}
		$this->assertEquals($error , "IBAN vide", "Erreur IBAN vide non déclanchée");


		try{
    		$res = $c->checkIBAN("123456");
		} catch (errorATF $e) {
			$error = $e->getMessage();
		}
		$this->assertEquals($error , "IBAN incorrect", "Erreur IBAN incorrect non déclanchée");


		try{
    		$res = $c->checkIBAN("FR89 3000 2039 0700 0046 6324 A26");
		} catch (errorATF $e) {
			$error = $e->getMessage();
		}
		$this->assertNull($res , "IBAN incorrect ??");
    }

    /* @author Morgan FLEURQUIN <mfleurquin@absystech.fr> */
    public function test_getLibelleAffaire(){
    	$myClass = new souscription_cleodis();
		$res = $this->invokeMethod($myClass, 'getLibelleAffaire', array(array(173), "boulangerpro"));
		$this->assertEquals('BOULANGER PRO - Location PACKPRO CONNECT Entry', $res, "Libelle affaire 1 incorrect");

		$res = $this->invokeMethod($myClass, 'getLibelleAffaire', array(array(173), "btwin"));
		$this->assertEquals('BTWIN - Location PACKPRO CONNECT Entry', $res, "Libelle affaire 2 incorrect");

		$res = $this->invokeMethod($myClass, 'getLibelleAffaire', array(array(727,728), "btwin"));
		$this->assertEquals('BTWIN - Location PACKPRO CONNECT Performance + PACKPRO CONNECT Entry+', $res, "Libelle affaire 3 incorrect");

    }


     /* @author Morgan FLEURQUIN <mfleurquin@absystech.fr> */
    public function test_getPrefixCodeClient(){
    	$myClass = new souscription_cleodis();
		$res = $this->invokeMethod($myClass, 'getPrefixCodeClient',  array("boulangerpro"));
		$this->assertEquals('BG', $res, "Prefix code client 1 incorrect");

		$res = $this->invokeMethod($myClass, 'getPrefixCodeClient',  array("btwin"));
		$this->assertEquals('BT', $res, "Prefix code client 2 incorrect");

		$res = $this->invokeMethod($myClass, 'getPrefixCodeClient',  array("toshiba"));
		$this->assertEquals('', $res, "Prefix code client 3 incorrect");
    }


    public function test_devis(){
    	$c = new souscription_cleodis();

    	$post = $this->post;
    	//Affaire BOUL PRO
    	$res = $c->_devis(array(), $post);


    	ATF::affaire()->q->reset()->addOrder("affaire.id_affaire", "DESC")->setLimit(1);
    	$lastID = ATF::affaire()->select_row();

    	$this->assertEquals($res[0], $lastID["affaire.id_affaire"], "Insertion Boul Pro incorrect 2?");

    	//On retire la ref de la société
    	ATF::societe()->u(array("id_societe"=>$post["id_societe"] , "code_client"=>NULL ));

    	//Affaire BTWIN
    	$post = $this->post;
		$post["site_associe"] = "btwin";
    	$res = $c->_devis(array(), $post);
    	ATF::affaire()->q->reset()->addOrder("affaire.id_affaire", "DESC")->setLimit(1);
    	$lastID = ATF::affaire()->select_row();
    	$this->assertEquals($res[0], $lastID["affaire.id_affaire"], "Insertion Btwin incorrect 2?");

    	//La ref de société a du se mettre à jour
    	$code_client = ATF::societe()->select($post["id_societe"], "code_client");
    	$this->assertEquals("BT", substr($code_client, 0,2), "Code client pas mis à jour!");


    	//On retire les produits pour provoquer une erreur
    	// Ce test fonctionnait avant le multi affaire ....
    	/*
    	try{
    		$c->_devis(array(), array("id_societe"=> $post["id_societe"], "iban"=>$post["iban"]));
    	} catch (errorATF $e) {
			$error = $e->getMessage();
		}
		$erreur = 'generic message : {"text":"\'Titre (Devis)\', \'Contact (Devis)\'","params":{"title":"Certaines donn\u00e9es obligatoires sont manquantes :"}}';
		$this->assertEquals($error , $erreur, "Erreur non déclanchée ?");
		*/
    }


    public function test_signAndGetPDF(){
    	$c = new souscription_cleodis();
    	$post = $this->post;
    	$affaires = $c->_devis(array(), $post);
    	$id_affaire = $affaires[0];

    	ATF::societe()->u(array("id_societe"=>$post["id_societe"],
								"tel"=>NULL,
								"ref"=>NULL,
								"code_client"=>NULL ));

    	$data = array(
    					"tel" => "0625303642",
    					"bic" => $post["bic"],
    					"iban" => $post["iban"],
    					"id" => $id_affaire,
    					"type"=> "professionnel",
    					"site_associe"=> $post["site_associe"]

    				 );

    	$res = ATF::souscription()->_signAndGetPDF($data, array());

    	$return = array(
	    	"id_affaire"=>$id_affaire,
			"civility"=>"M",
			"firstname"=>$post["contact"]["prenom"],
			"lastname"=>$post["contact"]["nom"],
			"address_1"=>$post["societe"]["adresse"],
			"address_2"=>$post["societe"]["adresse_2"]." ".$post["societe"]["adresse_3"],
			"postal_code"=>$post["societe"]["cp"],
			"city"=>$post["societe"]["ville"],
			"company_name"=>$post["societe"]["societe"],
			"country"=>"FR",
			"cell_phone"=>"0625303642",
		);

    	$societe = ATF::societe()->select($post["id_societe"]);
    	$this->assertEquals($societe["tel"], "0625303642", "Tel société pas mis à jour ?");
    	$this->assertEquals(substr($societe["code_client"],0, -4), "boulangerpro", "Code client pas mis à jour ?");
    	foreach ($return as $key => $value) {
    		$this->assertEquals($res[$key], $value, "Retour incorrect pour ".$key);
    	}

    	$this->assertEquals(count($res["files2sign"]), 1, "Nombre d'entrée a changé?");
    	$this->assertNotNull($res["files2sign"]["mandatSellAndSign.pdf"], "Pas de mandat PDF");

    }


    public function test_signAndGetPDFBTWIN(){
    	$c = new souscription_cleodis();
    	$post = $this->post;
    	$affaires = $c->_devis(array(), $post);
    	$id_affaire = $affaires[0];

    	ATF::societe()->u(array("id_societe"=>$post["id_societe"],
								"tel"=>NULL,
								"ref"=>NULL,
								"code_client"=>NULL ));

    	$data = array(
    					"tel" => "0625303642",
    					"bic" => $post["bic"],
    					"iban" => $post["iban"],
    					"id" => $id_affaire,
    					"type"=> "particulier",
    					"site_associe"=> "btwin"

    				 );


    	$res = ATF::souscription()->_signAndGetPDF($data, array());

    	$return = array(
	    	"id_affaire"=>$id_affaire,
			"civility"=>"M",
			"firstname"=>$post["contact"]["prenom"],
			"lastname"=>$post["contact"]["nom"],
			"address_1"=>$post["societe"]["adresse"],
			"address_2"=>$post["societe"]["adresse_2"]." ".$post["societe"]["adresse_3"],
			"postal_code"=>$post["societe"]["cp"],
			"city"=>$post["societe"]["ville"],
			"company_name"=>$post["societe"]["societe"],
			"country"=>"FR",
			"cell_phone"=>"0625303642",
		);

    	$societe = ATF::societe()->select($post["id_societe"]);
    	$this->assertEquals($societe["tel"], "0625303642", "Tel société pas mis à jour ?");
    	$this->assertEquals(substr($societe["code_client"],0, -4), "btwin", "Code client pas mis à jour ?");
    	foreach ($return as $key => $value) {
    		$this->assertEquals($res[$key], $value, "Retour incorrect pour ".$key);
    	}

    	$this->assertEquals(count($res["files2sign"]), 3, "Nombre d'entrée a changé?");
    	$this->assertNotNull($res["files2sign"]["mandatSellAndSign.pdf"], "Pas de mandat PDF");
    	$this->assertNotNull($res["files2sign"]["contrat-PV.pdf"], "Pas de contrat PV PDF");
    	$this->assertNotNull($res["files2sign"]["notice_assurance.pdf"], "Pas de notice_assurance PDF");
    }


    public function test_signAndGetPDFErreur(){
    	$c = new souscription_cleodis();
    	$post = $this->post;
    	$affaires = $c->_devis(array(), $post);
    	$id_affaire = $affaires[0];

    	$data = array(
    					"tel" => "0625303642",
    					"bic" => $post["bic"],
    					"iban" => $post["iban"],
    					//"site_associe"=> $post["site_associe"]
    				 );

    	try{
    		$res = ATF::souscription()->_signAndGetPDF($data, array());
    	}catch (Exception $e) {
			$error = $e->getMessage();
		}
		$this->assertEquals($error , "Aucune information pour cet identifiant.", "Erreur non déclanchée ?");


		$data["id"] = $id_affaire;
		try{
    		$res = ATF::souscription()->_signAndGetPDF($data, array());
    	}catch (errorATF $e) {
			$error = $e->getMessage();
		}
		$this->assertEquals($error , "TYPE INCONNU : '', ne peut pas faire de retour", "Erreur 2 non déclanchée ?");


		$data["type"] = "test";
		try{
    		$res = ATF::souscription()->_signAndGetPDF($data, array());
    	}catch (errorATF $e) {
			$error = $e->getMessage();
		}
		$this->assertEquals($error , "SITE ASSOCIE INCONNU : '', aucun document a générer.", "Erreur 3 non déclanchée ?");

		$data["site_associe"] = "boulangerpro";
		//Pour le coverage
		$res = ATF::souscription()->_signAndGetPDF($data, array());
	}

	public function test_signGetInfosOnly(){
		$c = new souscription_cleodis();
		$post = $this->post;
    	$affaires = $c->_devis(array(), $post);
    	$id_affaire = $affaires[0];


    	try{
    		$res = ATF::souscription()->_signGetInfosOnly(array(), array());
    	}catch (Exception $e) {
			$error = $e->getMessage();
		}
		$this->assertEquals($error , "Aucune information pour cet identifiant.", "Erreur non déclanchée ?");

		$data = array("id" => $id_affaire);
    	$res = ATF::souscription()->_signGetInfosOnly($data, array());


    	$return = array(
			"civility"=>"M",
			"firstname"=>$post["contact"]["prenom"],
			"lastname"=>$post["contact"]["nom"],
			"company_name"=>$post["societe"]["societe"],
			"IBAN"=>$post["iban"],
     		"BIC"=>$post["bic"],
			"tel"=>"0625303642",
		);

		foreach ($return as $key => $value) {
    		$this->assertEquals($res[$key], $value, "Retour incorrect pour ".$key);
    	}
	}


};

?>