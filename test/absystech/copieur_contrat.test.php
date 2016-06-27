<?
class copieur_contrat_test extends ATF_PHPUnit_Framework_TestCase {
	
	/*@author Quentin JANON <qjanon@absystech.fr> */ 
	function setUp() {
		$this->initUser();
	}
	
	/*@author Quentin JANON <qjanon@absystech.fr> */ 
	function tearDown(){
		ATF::db()->rollback_transaction(true);
		//Flush des notices
		ATF::$msg->getNotices();
	}
	
	/*@author Quentin JANON <qjanon@absystech.fr>  */
	function testInsertSansLigne(){

		$copieur_contrat["copieur_contrat"]["id_societe"]=$this->id_societe;
		$copieur_contrat["copieur_contrat"]["date"]=date("Y-m-d");
		$copieur_contrat["copieur_contrat"]['duree']=36;

		try {
			$this->id = $this->obj->insert($copieur_contrat,$this->s);
		} catch (errorATF $e) {
			$err = $e->getCode();
		}

		$this->assertEquals(50,$err,"ERREUR 1 NON ATTRAPPEE (pas de lignes)");
	}
	
	/*@author Quentin JANON <qjanon@absystech.fr>  */
	function testInsert(){

		$copieur_contrat["copieur_contrat"]["id_societe"]=$this->id_societe;
		$copieur_contrat["copieur_contrat"]["date"]=date("Y-m-d");
		$copieur_contrat["copieur_contrat"]['duree']=36;
		$copieur_contrat["values_copieur_contrat"]=array("produits"=>'[{"copieur_contrat_ligne__dot__type":"fhdfghdf","copieur_contrat_ligne__dot__designation":"sdfghndhyfnsn sfnbsg","copieur_contrat_ligne__dot__quantite":"","copieur_contrat_ligne__dot__prix_achatNB":"0.00500","copieur_contrat_ligne__dot__prix_achatC":"0.50000","copieur_contrat_ligne__dot__prixNB":"0.00700","copieur_contrat_ligne__dot__prixC":"0.70000"},{"copieur_contrat_ligne__dot__type":"jgvjg","copieur_contrat_ligne__dot__designation":"jgvjgv","copieur_contrat_ligne__dot__quantite":"1","copieur_contrat_ligne__dot__prix_achatNB":"0.20000","copieur_contrat_ligne__dot__prix_achatC":"0.30000","copieur_contrat_ligne__dot__prixNB":"0.40000","copieur_contrat_ligne__dot__prixC":"0.50000"}]');

		$a = array();
		$id = $this->obj->decryptId($this->obj->insert($copieur_contrat,$this->s,false,$a));
		$el = $this->obj->select($id);

		$this->assertNotNull($el['id_affaire_cout_page'],"Il manque l'id_affaire_cout_page !");
		$this->assertNotNull($el['id_user'],"Il manque l'id_user !");

		$l = ATF::copieur_contrat_ligne()->ss("id_copieur_contrat",$id);
		$this->assertEquals(2,count($l),"Il n'y a pas le bon nombre de ligne");

	}

	/*@author Quentin JANON <qjanon@absystech.fr>  */
	function testInsertPreview(){

		$copieur_contrat["copieur_contrat"]["id_societe"]=$this->id_societe;
		$copieur_contrat["copieur_contrat"]["date"]=date("Y-m-d");
		$copieur_contrat["copieur_contrat"]['duree']=36;
		$copieur_contrat["values_copieur_contrat"]=array("produits"=>'[{"copieur_contrat_ligne__dot__type":"fhdfghdf","copieur_contrat_ligne__dot__designation":"sdfghndhyfnsn sfnbsg","copieur_contrat_ligne__dot__quantite":"","copieur_contrat_ligne__dot__prix_achatNB":"0.00500","copieur_contrat_ligne__dot__prix_achatC":"0.50000","copieur_contrat_ligne__dot__prixNB":"0.00700","copieur_contrat_ligne__dot__prixC":"0.70000"},{"copieur_contrat_ligne__dot__type":"jgvjg","copieur_contrat_ligne__dot__designation":"jgvjgv","copieur_contrat_ligne__dot__quantite":"1","copieur_contrat_ligne__dot__prix_achatNB":"0.20000","copieur_contrat_ligne__dot__prix_achatC":"0.30000","copieur_contrat_ligne__dot__prixNB":"0.40000","copieur_contrat_ligne__dot__prixC":"0.50000"}]');
		$copieur_contrat["preview"] = true;
		$a = array();
		$id = $this->obj->decryptId($this->obj->insert($copieur_contrat,$this->s,false,$a));

	}


	/*@author Quentin JANON <qjanon@absystech.fr> */
	function testUpdate(){
		$copieur_contrat["copieur_contrat"]["id_societe"]=$this->id_societe;
		$copieur_contrat["copieur_contrat"]["date"]=date("Y-m-d");
		$copieur_contrat["copieur_contrat"]['duree']=36;
		$copieur_contrat["values_copieur_contrat"]=array("produits"=>'[{"copieur_contrat_ligne__dot__type":"fhdfghdf","copieur_contrat_ligne__dot__designation":"sdfghndhyfnsn sfnbsg","copieur_contrat_ligne__dot__quantite":"","copieur_contrat_ligne__dot__prix_achatNB":"0.00500","copieur_contrat_ligne__dot__prix_achatC":"0.50000","copieur_contrat_ligne__dot__prixNB":"0.00700","copieur_contrat_ligne__dot__prixC":"0.70000"},{"copieur_contrat_ligne__dot__type":"jgvjg","copieur_contrat_ligne__dot__designation":"jgvjgv","copieur_contrat_ligne__dot__quantite":"1","copieur_contrat_ligne__dot__prix_achatNB":"0.20000","copieur_contrat_ligne__dot__prix_achatC":"0.30000","copieur_contrat_ligne__dot__prixNB":"0.40000","copieur_contrat_ligne__dot__prixC":"0.50000"}]');

		$id = $this->obj->decryptId($this->obj->insert($copieur_contrat,$this->s));

		$copieur_contrat['copieur_contrat']['id_copieur_contrat'] = $id;
		$copieur_contrat["copieur_contrat"]['duree']=32;
		$copieur_contrat["values_copieur_contrat"]=array("produits"=>'[{"copieur_contrat_ligne__dot__type":"fhdfghdf","copieur_contrat_ligne__dot__designation":"sdfghndhyfnsn sfnbsg","copieur_contrat_ligne__dot__quantite":"","copieur_contrat_ligne__dot__prix_achatNB":"0.00500","copieur_contrat_ligne__dot__prix_achatC":"0.50000","copieur_contrat_ligne__dot__prixNB":"0.00700","copieur_contrat_ligne__dot__prixC":"0.70000"}]');

		$this->obj->update($copieur_contrat);
		$el = $this->obj->select($id);

		$this->assertEquals(32,$el['duree'],"Durée non modifié !");
		$l = ATF::copieur_contrat_ligne()->ss("id_copieur_contrat",$id);
		$this->assertEquals(1,count($l),"Il n'y a pas le bon nombre de ligne");
	}

		/*@author Quentin JANON <qjanon@absystech.fr> */
	function testUpdatePreview(){
		$copieur_contrat["copieur_contrat"]["id_societe"]=$this->id_societe;
		$copieur_contrat["copieur_contrat"]["date"]=date("Y-m-d");
		$copieur_contrat["copieur_contrat"]['duree']=36;
		$copieur_contrat["values_copieur_contrat"]=array("produits"=>'[{"copieur_contrat_ligne__dot__type":"fhdfghdf","copieur_contrat_ligne__dot__designation":"sdfghndhyfnsn sfnbsg","copieur_contrat_ligne__dot__quantite":"","copieur_contrat_ligne__dot__prix_achatNB":"0.00500","copieur_contrat_ligne__dot__prix_achatC":"0.50000","copieur_contrat_ligne__dot__prixNB":"0.00700","copieur_contrat_ligne__dot__prixC":"0.70000"},{"copieur_contrat_ligne__dot__type":"jgvjg","copieur_contrat_ligne__dot__designation":"jgvjgv","copieur_contrat_ligne__dot__quantite":"1","copieur_contrat_ligne__dot__prix_achatNB":"0.20000","copieur_contrat_ligne__dot__prix_achatC":"0.30000","copieur_contrat_ligne__dot__prixNB":"0.40000","copieur_contrat_ligne__dot__prixC":"0.50000"}]');

		$id = $this->obj->decryptId($this->obj->insert($copieur_contrat,$this->s));

		$copieur_contrat['copieur_contrat']['id_copieur_contrat'] = $id;
		$copieur_contrat["copieur_contrat"]['duree']=32;
		$copieur_contrat["values_copieur_contrat"]=array("produits"=>'[{"copieur_contrat_ligne__dot__type":"fhdfghdf","copieur_contrat_ligne__dot__designation":"sdfghndhyfnsn sfnbsg","copieur_contrat_ligne__dot__quantite":"","copieur_contrat_ligne__dot__prix_achatNB":"0.00500","copieur_contrat_ligne__dot__prix_achatC":"0.50000","copieur_contrat_ligne__dot__prixNB":"0.00700","copieur_contrat_ligne__dot__prixC":"0.70000"}]');
		$copieur_contrat["preview"] = true;

		$this->obj->update($copieur_contrat);

	}

		/*@author Quentin JANON <qjanon@absystech.fr> */
	function testUpdateSansLigne(){
		$copieur_contrat["copieur_contrat"]["id_societe"]=$this->id_societe;
		$copieur_contrat["copieur_contrat"]["date"]=date("Y-m-d");
		$copieur_contrat["copieur_contrat"]['duree']=36;
		$copieur_contrat["values_copieur_contrat"]=array("produits"=>'[{"copieur_contrat_ligne__dot__type":"fhdfghdf","copieur_contrat_ligne__dot__designation":"sdfghndhyfnsn sfnbsg","copieur_contrat_ligne__dot__quantite":"","copieur_contrat_ligne__dot__prix_achatNB":"0.00500","copieur_contrat_ligne__dot__prix_achatC":"0.50000","copieur_contrat_ligne__dot__prixNB":"0.00700","copieur_contrat_ligne__dot__prixC":"0.70000"},{"copieur_contrat_ligne__dot__type":"jgvjg","copieur_contrat_ligne__dot__designation":"jgvjgv","copieur_contrat_ligne__dot__quantite":"1","copieur_contrat_ligne__dot__prix_achatNB":"0.20000","copieur_contrat_ligne__dot__prix_achatC":"0.30000","copieur_contrat_ligne__dot__prixNB":"0.40000","copieur_contrat_ligne__dot__prixC":"0.50000"}]');

		$id = $this->obj->decryptId($this->obj->insert($copieur_contrat,$this->s));

		$copieur_contrat['copieur_contrat']['id_copieur_contrat'] = $id;
		$copieur_contrat["copieur_contrat"]['duree']=32;
		unset($copieur_contrat["values_copieur_contrat"]);

		try {
			$this->obj->update($copieur_contrat);
		} catch (errorATF $e) {
			$err = $e->getCode();
		}

		$this->assertEquals(50,$err,"ERREUR  NON ATTRAPPEE (pas de lignes)");

	}

		/*@author Quentin JANON <qjanon@absystech.fr> */
	function test_uploadFileFromSA() {
		$copieur_contrat["copieur_contrat"]["id_societe"]=$this->id_societe;
		$copieur_contrat["copieur_contrat"]["date"]=date("Y-m-d");
		$copieur_contrat["copieur_contrat"]['duree']=36;
		$copieur_contrat["values_copieur_contrat"]=array("produits"=>'[{"copieur_contrat_ligne__dot__type":"fhdfghdf","copieur_contrat_ligne__dot__designation":"sdfghndhyfnsn sfnbsg","copieur_contrat_ligne__dot__quantite":"","copieur_contrat_ligne__dot__prix_achatNB":"0.00500","copieur_contrat_ligne__dot__prix_achatC":"0.50000","copieur_contrat_ligne__dot__prixNB":"0.00700","copieur_contrat_ligne__dot__prixC":"0.70000"},{"copieur_contrat_ligne__dot__type":"jgvjg","copieur_contrat_ligne__dot__designation":"jgvjgv","copieur_contrat_ligne__dot__quantite":"1","copieur_contrat_ligne__dot__prix_achatNB":"0.20000","copieur_contrat_ligne__dot__prix_achatC":"0.30000","copieur_contrat_ligne__dot__prixNB":"0.40000","copieur_contrat_ligne__dot__prixC":"0.50000"}]');

		$idC = $this->obj->insert($copieur_contrat,$this->s);
		$id = $this->obj->decryptId($idC);


		$p = array(
		    "id" => $idC,
		    "extAction" => "copieur_contrat",
		    "extMethod" => "uploadFileFromSA",
		);


		$this->obj->uploadFileFromSA($p,$p,array("toto"=>"toto"));

		$el = $this->obj->select($id);
		$affaire = ATF::affaire_cout_page()->select($el['id_affaire_cout_page']);
		$this->assertEquals("accepte",$el['etat'], "Problème dans l'état du contrat");
		$this->assertEquals("commande",$affaire['etat'], "Problème dans l'état del'affaire");
		$this->assertEquals(100,$affaire['forecast'], "Problème dans l'forecast del'affaire");

	}
	
	public function test_select_all() {
		$copieur_contrat1["copieur_contrat"]["id_societe"]=$this->id_societe;
		$copieur_contrat1["copieur_contrat"]["date"]=date("Y-m-d");
		$copieur_contrat1["copieur_contrat"]['duree']=36;
		$copieur_contrat1["values_copieur_contrat"]=array("produits"=>'[{"copieur_contrat_ligne__dot__type":"fhdfghdf","copieur_contrat_ligne__dot__designation":"sdfghndhyfnsn sfnbsg","copieur_contrat_ligne__dot__quantite":"","copieur_contrat_ligne__dot__prix_achatNB":"0.00500","copieur_contrat_ligne__dot__prix_achatC":"0.50000","copieur_contrat_ligne__dot__prixNB":"0.00700","copieur_contrat_ligne__dot__prixC":"0.70000"},{"copieur_contrat_ligne__dot__type":"jgvjg","copieur_contrat_ligne__dot__designation":"jgvjgv","copieur_contrat_ligne__dot__quantite":"1","copieur_contrat_ligne__dot__prix_achatNB":"0.20000","copieur_contrat_ligne__dot__prix_achatC":"0.30000","copieur_contrat_ligne__dot__prixNB":"0.40000","copieur_contrat_ligne__dot__prixC":"0.50000"}]');

		$copieur_contrat2["copieur_contrat"]["id_societe"]=$this->id_societe;
		$copieur_contrat2["copieur_contrat"]["date"]=date("Y-m-d");
		$copieur_contrat2["copieur_contrat"]['duree']=36;
		$copieur_contrat2["copieur_contrat"]['etat']="accepte";
		$copieur_contrat2["values_copieur_contrat"]=array("produits"=>'[{"copieur_contrat_ligne__dot__type":"fhdfghdf","copieur_contrat_ligne__dot__designation":"sdfghndhyfnsn sfnbsg","copieur_contrat_ligne__dot__quantite":"","copieur_contrat_ligne__dot__prix_achatNB":"0.00500","copieur_contrat_ligne__dot__prix_achatC":"0.50000","copieur_contrat_ligne__dot__prixNB":"0.00700","copieur_contrat_ligne__dot__prixC":"0.70000"},{"copieur_contrat_ligne__dot__type":"jgvjg","copieur_contrat_ligne__dot__designation":"jgvjgv","copieur_contrat_ligne__dot__quantite":"1","copieur_contrat_ligne__dot__prix_achatNB":"0.20000","copieur_contrat_ligne__dot__prix_achatC":"0.30000","copieur_contrat_ligne__dot__prixNB":"0.40000","copieur_contrat_ligne__dot__prixC":"0.50000"}]');

		$idC = $this->obj->insert($copieur_contrat1,$this->s);
		$idC2 = $this->obj->insert($copieur_contrat2,$this->s);
		$id = $this->obj->decryptId($idC);
		$id2 = $this->obj->decryptId($idC2);

		$this->obj->q->addField("copieur_contrat.etat")->where('id_copieur_contrat',$id)->where('id_copieur_contrat',$id2)->setCount();
		$r = $this->obj->select_all();
		$this->assertArrayHasKey("allowFacture",$r['data'][0],"Il n'y a pas l'entrée allowFacture 1");
		$this->assertTrue($r['data'][0]['allowFacture'],"Devrait être a TRUE");
		$this->assertArrayHasKey("allowFacture",$r['data'][1],"Il n'y a pas l'entrée allowFacture 2");
		$this->assertFalse($r['data'][1]['allowFacture'],"Devrait être a FALSE");
	}

	public function test_default_value() {
		$copieur_contrat["copieur_contrat"]["id_societe"]=$this->id_societe;
		$copieur_contrat["copieur_contrat"]["date"]=date("Y-m-d");
		$copieur_contrat["copieur_contrat"]['duree']=36;
		$copieur_contrat["values_copieur_contrat"]=array("produits"=>'[{"copieur_contrat_ligne__dot__type":"fhdfghdf","copieur_contrat_ligne__dot__designation":"sdfghndhyfnsn sfnbsg","copieur_contrat_ligne__dot__quantite":"","copieur_contrat_ligne__dot__prix_achatNB":"0.00500","copieur_contrat_ligne__dot__prix_achatC":"0.50000","copieur_contrat_ligne__dot__prixNB":"0.00700","copieur_contrat_ligne__dot__prixC":"0.70000"},{"copieur_contrat_ligne__dot__type":"jgvjg","copieur_contrat_ligne__dot__designation":"jgvjgv","copieur_contrat_ligne__dot__quantite":"1","copieur_contrat_ligne__dot__prix_achatNB":"0.20000","copieur_contrat_ligne__dot__prix_achatC":"0.30000","copieur_contrat_ligne__dot__prixNB":"0.40000","copieur_contrat_ligne__dot__prixC":"0.50000"}]');

		$idC = $this->obj->insert($copieur_contrat,$this->s);
		$id = $this->obj->decryptId($idC);
		$el = $this->obj->select($id);


		$r = $this->obj->default_value("id_societe");

		$this->assertNull($r,"Mauvais retour d'id_societe");

		ATF::_r('id_copieur_contrat',$idC);
		$r = $this->obj->default_value("id_societe");
		$this->assertEquals($this->id_societe,$r,"Mauvais retour d'id_societe 2");

		ATF::_r('id_copieur_contrat',false);
		ATF::_r('id_affaire_cout_page',$el['id_affaire_cout_page']);
		$r = $this->obj->default_value("id_societe");
		$this->assertEquals($this->id_societe,$r,"Mauvais retour d'id_societe 3");


	}

	public function test_default_value_releve_initiaux() {
		$r = $this->obj->default_value("releve_initial_C");
		$this->assertEquals(0,$r,"Mauvais retour d'releve_initial_C 2");
		$r = $this->obj->default_value("releve_initial_NB");
		$this->assertEquals(0,$r,"Mauvais retour d'releve_initial_NB 2");

		$copieur_contrat["copieur_contrat"]["id_societe"]=$this->id_societe;
		$copieur_contrat["copieur_contrat"]["date"]=date("Y-m-d");
		$copieur_contrat["copieur_contrat"]['duree']=36;
		$copieur_contrat["copieur_contrat"]['releve_initial_C']=64;
		$copieur_contrat["copieur_contrat"]['releve_initial_NB']=67;
		$copieur_contrat["values_copieur_contrat"]=array("produits"=>'[{"copieur_contrat_ligne__dot__type":"fhdfghdf","copieur_contrat_ligne__dot__designation":"sdfghndhyfnsn sfnbsg","copieur_contrat_ligne__dot__quantite":"","copieur_contrat_ligne__dot__prix_achatNB":"0.00500","copieur_contrat_ligne__dot__prix_achatC":"0.50000","copieur_contrat_ligne__dot__prixNB":"0.00700","copieur_contrat_ligne__dot__prixC":"0.70000"},{"copieur_contrat_ligne__dot__type":"jgvjg","copieur_contrat_ligne__dot__designation":"jgvjgv","copieur_contrat_ligne__dot__quantite":"1","copieur_contrat_ligne__dot__prix_achatNB":"0.20000","copieur_contrat_ligne__dot__prix_achatC":"0.30000","copieur_contrat_ligne__dot__prixNB":"0.40000","copieur_contrat_ligne__dot__prixC":"0.50000"}]');

		$idC = $this->obj->insert($copieur_contrat,$this->s);
		$id = $this->obj->decryptId($idC);
		$el = $this->obj->select($id);


		$r = $this->obj->default_value("id_societe");

		$this->assertNull($r,"Mauvais retour d'id_societe");

		ATF::_r('id_copieur_contrat',$idC);
		$r = $this->obj->default_value("releve_initial_C");
		$this->assertEquals(64,$r,"Mauvais retour d'releve_initial_C 2");
		$r = $this->obj->default_value("releve_initial_NB");
		$this->assertEquals(67,$r,"Mauvais retour d'releve_initial_NB 2");

		ATF::_r('id_copieur_contrat',false);
		ATF::_r('id_affaire_cout_page',$el['id_affaire_cout_page']);
		$r = $this->obj->default_value("releve_initial_C");
		$this->assertEquals(64,$r,"Mauvais retour d'releve_initial_C 3");
		$r = $this->obj->default_value("releve_initial_NB");
		$this->assertEquals(67,$r,"Mauvais retour d'releve_initial_NB 3");


	}


};
?>