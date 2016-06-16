<?
class copieur_facture_test extends ATF_PHPUnit_Framework_TestCase {
	
	/*@author Quentin JANON <qjanon@absystech.fr> */ 
	function setUp() {
		$this->initUser();

		//copieur_contrat
		$this->copieur_contrat["copieur_contrat"]["date"]=date('Y-m-d');
		$this->copieur_contrat["copieur_contrat"]['id_societe']=$this->id_societe;
		$this->copieur_contrat["copieur_contrat"]['duree']=36;
		
		//copieur_contrat_ligne
		$this->copieur_contrat["values_copieur_contrat"]=array("produits"=>'[{"copieur_contrat_ligne__dot__type":"fhdfghdf","copieur_contrat_ligne__dot__designation":"sdfghndhyfnsn sfnbsg","copieur_contrat_ligne__dot__quantite":"","copieur_contrat_ligne__dot__prix_achatNB":"0.00500","copieur_contrat_ligne__dot__prix_achatC":"0.50000","copieur_contrat_ligne__dot__prixNB":"0.00700","copieur_contrat_ligne__dot__prixC":"0.70000"},{"copieur_contrat_ligne__dot__type":"jgvjg","copieur_contrat_ligne__dot__designation":"jgvjgv","copieur_contrat_ligne__dot__quantite":"1","copieur_contrat_ligne__dot__prix_achatNB":"0.20000","copieur_contrat_ligne__dot__prix_achatC":"0.30000","copieur_contrat_ligne__dot__prixNB":"0.40000","copieur_contrat_ligne__dot__prixC":"0.50000"}]');

		//Insertion
		$this->id_copieur_contrat = ATF::copieur_contrat()->insert($this->copieur_contrat,$this->s);
		$this->id_affaire_cout_page = ATF::copieur_contrat()->select($this->id_copieur_contrat,"id_affaire_cout_page");
		
		//copieur_facture
		$this->copieur_facture["copieur_facture"]["date"]=date('Y-m-d');
		$this->copieur_facture["copieur_facture"]['id_termes']=1;
		$this->copieur_facture["copieur_facture"]['id_societe']=$this->id_societe;
		$this->copieur_facture["copieur_facture"]['tva']=1.8;
		$this->copieur_facture["copieur_facture"]['id_affaire_cout_page']=$this->id_affaire_cout_page;
		$this->copieur_facture["copieur_facture"]['releve_compteurNB']=3600;
		$this->copieur_facture["copieur_facture"]['releve_compteurC']=3611;
		
		//copieur_facture_ligne
		$this->copieur_facture["values_copieur_facture"]=array("produits"=>'[{"copieur_facture_ligne__dot__type":"fhdfghdf","copieur_facture_ligne__dot__designation":"sdfghndhyfnsn sfnbsg","copieur_facture_ligne__dot__quantite":"","copieur_facture_ligne__dot__prix_achatNB":"0.00500","copieur_facture_ligne__dot__prix_achatC":"0.50000","copieur_facture_ligne__dot__prixNB":"0.00700","copieur_facture_ligne__dot__prixC":"0.70000"},{"copieur_facture_ligne__dot__type":"jgvjg","copieur_facture_ligne__dot__designation":"jgvjgv","copieur_facture_ligne__dot__quantite":"1","copieur_facture_ligne__dot__prix_achatNB":"0.20000","copieur_facture_ligne__dot__prix_achatC":"0.30000","copieur_facture_ligne__dot__prixNB":"0.40000","copieur_facture_ligne__dot__prixC":"0.50000"}]');

		//Insertion
		$this->id_copieur_facture = ATF::copieur_facture()->insert($this->copieur_facture,$this->s);
	}
	
	/*@author Quentin JANON <qjanon@absystech.fr> */ 
	function tearDown(){
		ATF::db()->rollback_transaction(true);
		//Flush des notices
		ATF::$msg->getNotices();
	}


	/*@author Quentin JANON <qjanon@absystech.fr>  */
	public function test_default_value_withAFFAIRE() {
		ATF::_r('id_affaire_cout_page',$this->id_affaire_cout_page);

		$this->assertEquals("devis",$this->obj->default_value('etat'),"Mauvaise default value pour l'état");
		$this->assertEquals($this->id_societe,$this->obj->default_value('id_societe'),"Mauvaise default value pour l'id_societe");
		$this->assertNull($this->obj->default_value('id_termes'),"Mauvaise default value pour l'id_termes");
		$this->assertEquals(20,$this->obj->default_value('forecast'),"Mauvaise default value pour l'forecast");

		$aff = array("id_affaire_cout_page"=>$this->id_affaire_cout_page,"id_termes"=>1);
		ATF::affaire_cout_page()->u($aff);
		$this->assertEquals(1,$this->obj->default_value('id_termes'),"Mauvaise default value pour l'id_termes 2");
		$aff = array("id_affaire_cout_page"=>$this->id_affaire_cout_page,"id_termes"=>NULL);
		ATF::affaire_cout_page()->u($aff);
	}

	/*@author Quentin JANON <qjanon@absystech.fr> */
	public function test_default_value_withCONTRAT() {
		ATF::_r('id_copieur_contrat',$this->id_copieur_contrat);

		$this->assertEquals(date("Y-m-d"),$this->obj->default_value('date'),"Mauvaise default value pour l'date");
		$this->assertEquals(ATF::$usr->get("email"),$this->obj->default_value('emailCopie'),"Mauvaise default value pour l'emailCopie");
		$this->assertFalse($this->obj->default_value('email'),"Mauvaise default value pour l'email");
		$soc = array('id_societe'=>$this->id_societe,"id_contact_facturation"=>$this->id_contact);
		ATF::societe()->u($soc);
		$this->assertEquals("debug@absystech.fr",$this->obj->default_value('email'),"Mauvaise default value pour l'email 2");
		$this->assertNull($this->obj->default_value('id_termes'),"Mauvaise default value pour l'id_termes");
		$this->assertEquals(1.2,$this->obj->default_value('tva'),"Mauvaise default value pour l'tva");

	} 

	/*@author Quentin JANON <qjanon@absystech.fr>  */
	public function test_default_value_withFACTURE() {
		ATF::_r('id_copieur_facture',$this->id_copieur_facture);

		$this->assertEquals("impayee",$this->obj->default_value('etat'),"Mauvaise default value pour l'état");
		$this->assertEquals(date('Y-m-d',strtotime(date("Y-m-d")." + 30 day")),$this->obj->default_value('date_previsionnelle'),"Mauvaise default value pour l'date_previsionnelle");
		$this->assertNull($this->obj->default_value('id_termes'),"Mauvaise default value pour l'id_termes");
		$this->assertEquals(1.8,$this->obj->default_value('tva'),"Mauvaise default value pour l'tva");
	}

	/*@author Quentin JANON <qjanon@absystech.fr>  */
	public function test_insertTHROW() {
		//copieur_contrat
		$cf["copieur_facture"]["date"]=date('Y-m-d');
		$cf["copieur_facture"]['tva']=1.8;
		
		//Insertion
		$erreur = false;
		try {
			ATF::copieur_facture()->insert($cf,$this->s);		
		} catch (errorATF $e) {
			$erreur = true;
		}
		$this->assertTrue($erreur,"L'erreur sur l'id_societe n'est pas remonté");
		

		$cf["copieur_facture"]['id_societe']=$this->id_societe;
		$soc = array("etat"=>"inactif",'id_societe'=>$this->id_societe);
		ATF::societe()->u($soc);
		//Insertion
		$erreur = false;
		try {
			ATF::copieur_facture()->insert($cf,$this->s);		
		} catch (errorATF $e) {
			$erreur = true;
		}
		$this->assertTrue($erreur,"L'erreur sur l'id_societe FERME ! n'est pas remonté");
		$soc = array("etat"=>"actif",'id_societe'=>$this->id_societe);
		ATF::societe()->u($soc);
		
		
		//Insertion
		$erreur = false;
		try {
			ATF::copieur_facture()->insert($cf,$this->s);		
		} catch (errorATF $e) {
			$erreur = true;
		}
		$this->assertTrue($erreur,"L'erreur sur la présence des relevé n'est pas remontée.");
		$cf["copieur_facture"]['releve_compteurNB']=3600;
		$cf["copieur_facture"]['releve_compteurC']=3611;
		
		//Insertion
		$erreur = false;
		try {
			ATF::copieur_facture()->insert($cf,$this->s);		
		} catch (errorATF $e) {
			$erreur = true;
		}
		$this->assertTrue($erreur,"L'erreur sur la présence de l'id affaire cout page n'est pas remontée.");
		$cf["copieur_facture"]['id_affaire_cout_page']=$this->id_affaire_cout_page;


		//Insertion
		$erreur = false;
		try {
			ATF::copieur_facture()->insert($cf,$this->s);		
		} catch (errorATF $e) {
			$erreur = true;
		}
		$this->assertTrue($erreur,"L'erreur sur la présence de l'id terme n'est pas remontée.");
		$cf["copieur_facture"]['id_termes']=1;


		//copieur_facture_ligne
		//$cf["values_copieur_facture"]=array("produits"=>'[{"copieur_facture_ligne__dot__type":"fhdfghdf","copieur_facture_ligne__dot__designation":"sdfghndhyfnsn sfnbsg","copieur_facture_ligne__dot__quantite":"","copieur_facture_ligne__dot__prix_achatNB":"0.00500","copieur_facture_ligne__dot__prix_achatC":"0.50000","copieur_facture_ligne__dot__prixNB":"0.00700","copieur_facture_ligne__dot__prixC":"0.70000"},{"copieur_facture_ligne__dot__type":"jgvjg","copieur_facture_ligne__dot__designation":"jgvjgv","copieur_facture_ligne__dot__quantite":"1","copieur_facture_ligne__dot__prix_achatNB":"0.20000","copieur_facture_ligne__dot__prix_achatC":"0.30000","copieur_facture_ligne__dot__prixNB":"0.40000","copieur_facture_ligne__dot__prixC":"0.50000"}]');



	}

	/*@author Quentin JANON <qjanon@absystech.fr>  */
	public function test_insertWithoutPredenteFactureTHROW() {
		$this->obj->delete($this->id_copieur_facture);
		//copieur_contrat
		$cf["copieur_facture"]["date"]=date('Y-m-d');
		$cf["copieur_facture"]['tva']=1.8;
		$cf["copieur_facture"]['id_societe']=$this->id_societe;
		$cf["copieur_facture"]['releve_compteurNB']=10;
		$cf["copieur_facture"]['releve_compteurC']=20;
		$cf["copieur_facture"]['id_affaire_cout_page']=$this->id_affaire_cout_page;
		$cf["copieur_facture"]['id_termes']=1;

		$aff = array(
			"id_affaire_cout_page"=>$this->id_affaire_cout_page,
			"releve_initial_C"=>50,
			"releve_initial_NB"=>50
		);
		ATF::affaire_cout_page()->u($aff);

		//Insertion
		$erreur = false;
		try {
			ATF::copieur_facture()->insert($cf,$this->s);		
		} catch (errorATF $e) {
			$erreur = true;
		}
		$this->assertTrue($erreur,"L'erreur sur la différence entre les relevé COULEUR n'est pas remontée.");

		$aff = array(
			"id_affaire_cout_page"=>$this->id_affaire_cout_page,
			"releve_initial_C"=>5,
			"releve_initial_NB"=>50
		);
		ATF::affaire_cout_page()->u($aff);

		//Insertion
		$erreur = false;
		try {
			ATF::copieur_facture()->insert($cf,$this->s);		
		} catch (errorATF $e) {
			$erreur = true;
		}
		$this->assertTrue($erreur,"L'erreur sur la différence entre les relevé N&B n'est pas remontée.");


	}

	/*@author Quentin JANON <qjanon@absystech.fr>  */
	public function test_insertEmailTHROW() {

		//copieur_contrat
		$cf["copieur_facture"]["date"]=date('Y-m-d');
		$cf["copieur_facture"]['tva']=1.8;
		$cf["copieur_facture"]['id_societe']=$this->id_societe;
		$cf["copieur_facture"]['releve_compteurNB']=36500;
		$cf["copieur_facture"]['releve_compteurC']=20000;
		$cf["copieur_facture"]['id_affaire_cout_page']=$this->id_affaire_cout_page;
		$cf["copieur_facture"]['id_termes']=1;
		$cf["copieur_facture"]['emailTexte']="hop hop hop";
		$cf["values_copieur_facture"]=array("produits"=>'[{"copieur_facture_ligne__dot__type":"fhdfghdf","copieur_facture_ligne__dot__designation":"sdfghndhyfnsn sfnbsg","copieur_facture_ligne__dot__quantite":"","copieur_facture_ligne__dot__prix_achatNB":"0.00500","copieur_facture_ligne__dot__prix_achatC":"0.50000","copieur_facture_ligne__dot__prixNB":"0.00700","copieur_facture_ligne__dot__prixC":"0.70000"},{"copieur_facture_ligne__dot__type":"jgvjg","copieur_facture_ligne__dot__designation":"jgvjgv","copieur_facture_ligne__dot__quantite":"1","copieur_facture_ligne__dot__prix_achatNB":"0.20000","copieur_facture_ligne__dot__prix_achatC":"0.30000","copieur_facture_ligne__dot__prixNB":"0.40000","copieur_facture_ligne__dot__prixC":"0.50000"}]');

		//Insertion
		$erreur = false;
		try {
			ATF::copieur_facture()->insert($cf,$this->s);		
		} catch (errorATF $e) {
			$erreur = true;
		}
		$this->assertTrue($erreur,"L'erreur sur l'email des params n'est pas remontée.");
		$this->assertEquals(166,$e->getCode(),"Mauvais code erreur");
	}

	/*@author Quentin JANON <qjanon@absystech.fr>  */
	public function test_insertEmailTHROW2() {

		$soc = array("id_contact_facturation"=>$this->id_contact,"id_societe"=>$this->id_societe);
		ATF::societe()->u($soc);

		$con = array("id_contact"=>$this->id_contact,"email"=>NULL);
		ATF::contact()->u($con);
		//copieur_contrat
		$cf["copieur_facture"]["date"]=date('Y-m-d');
		$cf["copieur_facture"]['tva']=1.8;
		$cf["copieur_facture"]['id_societe']=$this->id_societe;
		$cf["copieur_facture"]['releve_compteurNB']=36500;
		$cf["copieur_facture"]['releve_compteurC']=20000;
		$cf["copieur_facture"]['id_affaire_cout_page']=$this->id_affaire_cout_page;
		$cf["copieur_facture"]['id_termes']=1;
		$cf["copieur_facture"]['emailTexte']="hop hop hop";
		$cf["values_copieur_facture"]=array("produits"=>'[{"copieur_facture_ligne__dot__type":"fhdfghdf","copieur_facture_ligne__dot__designation":"sdfghndhyfnsn sfnbsg","copieur_facture_ligne__dot__quantite":"","copieur_facture_ligne__dot__prix_achatNB":"0.00500","copieur_facture_ligne__dot__prix_achatC":"0.50000","copieur_facture_ligne__dot__prixNB":"0.00700","copieur_facture_ligne__dot__prixC":"0.70000"},{"copieur_facture_ligne__dot__type":"jgvjg","copieur_facture_ligne__dot__designation":"jgvjgv","copieur_facture_ligne__dot__quantite":"1","copieur_facture_ligne__dot__prix_achatNB":"0.20000","copieur_facture_ligne__dot__prix_achatC":"0.30000","copieur_facture_ligne__dot__prixNB":"0.40000","copieur_facture_ligne__dot__prixC":"0.50000"}]');

		//Insertion
		$erreur = false;
		try {
			ATF::copieur_facture()->insert($cf,$this->s);		
		} catch (errorATF $e) {
			$erreur = true;
		}
		$this->assertTrue($erreur,"L'erreur sur l'email de contact de facturation n'est pas remontée.");
		$this->assertEquals(166,$e->getCode(),"Mauvais code erreur");

	}

	/*@author Quentin JANON <qjanon@absystech.fr>  */
	public function test_insertEmail() {

		//copieur_contrat
		$cf["copieur_facture"]["date"]=date('Y-m-d');
		$cf["copieur_facture"]['tva']=1.8;
		$cf["copieur_facture"]['id_societe']=$this->id_societe;
		$cf["copieur_facture"]['releve_compteurNB']=36500;
		$cf["copieur_facture"]['releve_compteurC']=20000;
		$cf["copieur_facture"]['id_affaire_cout_page']=$this->id_affaire_cout_page;
		$cf["copieur_facture"]['id_termes']=1;
		$cf["copieur_facture"]['email']="qjanon@absystech.fr";
		$cf["copieur_facture"]['emailTexte']="hop hop hop";
		$cf["values_copieur_facture"]=array("produits"=>'[{"copieur_facture_ligne__dot__type":"fhdfghdf","copieur_facture_ligne__dot__designation":"sdfghndhyfnsn sfnbsg","copieur_facture_ligne__dot__quantite":"","copieur_facture_ligne__dot__prix_achatNB":"0.00500","copieur_facture_ligne__dot__prix_achatC":"0.50000","copieur_facture_ligne__dot__prixNB":"0.00700","copieur_facture_ligne__dot__prixC":"0.70000"},{"copieur_facture_ligne__dot__type":"jgvjg","copieur_facture_ligne__dot__designation":"jgvjgv","copieur_facture_ligne__dot__quantite":"1","copieur_facture_ligne__dot__prix_achatNB":"0.20000","copieur_facture_ligne__dot__prix_achatC":"0.30000","copieur_facture_ligne__dot__prixNB":"0.40000","copieur_facture_ligne__dot__prixC":"0.50000"}]');

		//Insertion
		ATF::copieur_facture()->insert($cf,$this->s);		



	}


	/*@author Quentin JANON <qjanon@absystech.fr>  */
	public function test_insertWithPredenteFactureTHROW() {

		//copieur_contrat
		$cf["copieur_facture"]["date"]=date('Y-m-d');
		$cf["copieur_facture"]['tva']=1.8;
		$cf["copieur_facture"]['id_societe']=$this->id_societe;
		$cf["copieur_facture"]['releve_compteurNB']=10;
		$cf["copieur_facture"]['releve_compteurC']=20;
		$cf["copieur_facture"]['id_affaire_cout_page']=$this->id_affaire_cout_page;
		$cf["copieur_facture"]['id_termes']=1;

		//Insertion
		$erreur = false;
		try {
			ATF::copieur_facture()->insert($cf,$this->s);		
		} catch (errorATF $e) {
			$erreur = true;
		}
		$this->assertTrue($erreur,"L'erreur sur la différence entre les relevé N&B n'est pas remontée.");
		$cf["copieur_facture"]['releve_compteurNB']=10000;

		//Insertion
		$erreur = false;
		try {
			ATF::copieur_facture()->insert($cf,$this->s);		
		} catch (errorATF $e) {
			$erreur = true;
		}
		$this->assertTrue($erreur,"L'erreur sur la différence entre les relevé COULEUR n'est pas remontée.");


	}



	/*@author Quentin JANON <qjanon@absystech.fr>  */
	public function test_insertWithPredenteFacture() {

		//copieur_contrat
		$cf["copieur_facture"]["date"]=date('Y-m-d');
		$cf["copieur_facture"]['tva']=1.8;
		$cf["copieur_facture"]['id_societe']=$this->id_societe;
		$cf["copieur_facture"]['releve_compteurNB']=10000;
		$cf["copieur_facture"]['releve_compteurC']=10000;
		$cf["copieur_facture"]['id_affaire_cout_page']=$this->id_affaire_cout_page;
		$cf["copieur_facture"]['id_termes']=1;
		$cf["copieur_facture"]['prix']=655.20;
		$cf["copieur_facture"]['id_copieur_contrat']=$this->id_copieur_contrat;
		$cf["copieur_facture"]['prix_ttc']=10000;
		$cf["values_copieur_facture"]=array("produits"=>'[{"copieur_facture_ligne__dot__type":"fhdfghdf","copieur_facture_ligne__dot__designation":"sdfghndhyfnsn sfnbsg","copieur_facture_ligne__dot__quantite":"","copieur_facture_ligne__dot__prix_achatNB":"0.00500","copieur_facture_ligne__dot__prix_achatC":"0.50000","copieur_facture_ligne__dot__prixNB":"0.00700","copieur_facture_ligne__dot__prixC":"0.70000"},{"copieur_facture_ligne__dot__type":"jgvjg","copieur_facture_ligne__dot__designation":"jgvjgv","copieur_facture_ligne__dot__quantite":"1","copieur_facture_ligne__dot__prix_achatNB":"0.20000","copieur_facture_ligne__dot__prix_achatC":"0.30000","copieur_facture_ligne__dot__prixNB":"0.40000","copieur_facture_ligne__dot__prixC":"0.50000"}]');
		$cf["preview"]=true;

		$id = $this->obj->insert($cf,$this->s);		

		$el = $this->obj->select($id);

		$this->assertEquals(10271.60,$el['prix'],"Le prix calculé n'est pas le bon !");
		$this->assertEquals(18488.88,$el['prix_ttc'],"Le prix_ttc calculé n'est pas le bon !");

		ATF::copieur_facture_ligne()->q->reset()->where("id_copieur_facture",$this->obj->decryptId($id));
		$lignes = ATF::copieur_facture_ligne()->sa();
		$this->assertEquals(2,count($lignes),"Pas le bon nombre de ligne");

		$this->assertEquals("accepte",ATF::copieur_contrat()->select($this->id_copieur_contrat,"etat"),"Erreur dans l'état du contrat");
		$this->assertEquals("facture",ATF::affaire_cout_page()->select($this->id_affaire_cout_page,"etat"),"Erreur dans l'état de l'affaire");
	}

	/*@author Quentin JANON <qjanon@absystech.fr>  */
	public function test_insertWithoutPredenteFacture() {

		$this->obj->delete($this->id_copieur_facture);
		$aff = array(
			"id_affaire_cout_page"=>$this->id_affaire_cout_page,
			"releve_initial_C"=>1000,
			"releve_initial_NB"=>1000
		);
		ATF::affaire_cout_page()->u($aff);

		//copieur_contrat
		$cf["copieur_facture"]["date"]=date('Y-m-d');
		$cf["copieur_facture"]['finale']=true;
		$cf["copieur_facture"]['id_societe']=$this->id_societe;
		$cf["copieur_facture"]['releve_compteurNB']=10000;
		$cf["copieur_facture"]['releve_compteurC']=10000;
		$cf["copieur_facture"]['id_affaire_cout_page']=$this->id_affaire_cout_page;
		$cf["copieur_facture"]['id_termes']=1;
		$cf["copieur_facture"]['prix']=655.20;
		$cf["copieur_facture"]['id_copieur_contrat']=$this->id_copieur_contrat;
		$cf["copieur_facture"]['prix_ttc']=10000;
		$cf["values_copieur_facture"]=array("produits"=>'[{"copieur_facture_ligne__dot__type":"fhdfghdf","copieur_facture_ligne__dot__designation":"sdfghndhyfnsn sfnbsg","copieur_facture_ligne__dot__quantite":"","copieur_facture_ligne__dot__prix_achatNB":"0.00500","copieur_facture_ligne__dot__prix_achatC":"0.50000","copieur_facture_ligne__dot__prixNB":"0.00700","copieur_facture_ligne__dot__prixC":"0.70000"},{"copieur_facture_ligne__dot__type":"jgvjg","copieur_facture_ligne__dot__designation":"jgvjgv","copieur_facture_ligne__dot__quantite":"1","copieur_facture_ligne__dot__prix_achatNB":"0.20000","copieur_facture_ligne__dot__prix_achatC":"0.30000","copieur_facture_ligne__dot__prixNB":"0.40000","copieur_facture_ligne__dot__prixC":"0.50000"}]');
		$cf["copieur_facture"]["emailTexte"]="Bonjour !";
		$cf["copieur_facture"]["email"]="qjanon@absystech.fr";
		$cf["copieur_facture"]["emailCopie"]="ygautheron@absystech.fr";
		$cf["copieur_facture"]["tva"]=3;

		ATF::$usr->set('id_profil',2);

		$id = $this->obj->insert($cf,$this->s);		
		ATF::$usr->set('id_profil',1);

		$this->assertEquals(array(
									0=>array(
										"msg"=>"Seul le profil Associé permet de modifier la TVA. La TVA de cette facture sera donc de 1.2",
										"title"=>"Droits d'accès requis pour cette opération ! ",
										"timer"=>""
										)
							),ATF::$msg->getNotices(),"La notice de TVA ne se fait pas");
		$el = $this->obj->select($id);

		$this->assertEquals(14463.00,$el['prix'],"Le prix calculé n'est pas le bon !");
		$this->assertEquals(17355.60,$el['prix_ttc'],"Le prix_ttc calculé n'est pas le bon !");

		$lignes = ATF::copieur_facture_ligne()->ss("id_copieur_facture",$this->obj->decryptId($id));
		$this->assertEquals(2,count($lignes),"Pas le bon nombre de ligne");

		$this->assertEquals("fini",ATF::copieur_contrat()->select($this->id_copieur_contrat,"etat"),"Erreur dans l'état du contrat");
		$this->assertEquals("terminee",ATF::affaire_cout_page()->select($this->id_affaire_cout_page,"etat"),"Erreur dans l'état de l'affaire");
	}

	/*@author Quentin JANON <qjanon@absystech.fr>  */
	public function test_updateDateThrow() {
		$params = array(
			"id_copieur_facture"=>$this->id_copieur_facture,
			"value"=>"2014-01-01",
			"key"=>"date_effective"
		);

		$erreur = false;
		try {
			$this->obj->updateDate($params);
		} catch (errorATF $e) {
			$erreur = true;
		}
		$this->assertTrue($erreur,"Erreur non attrapée !");
	}

	/*@author Quentin JANON <qjanon@absystech.fr>  */
	public function test_updateDate() {
		$d = date("Y-m-d",strtotime(date("Y-m-d")." +1 day"));
		$params = array(
			"id_copieur_facture"=>$this->id_copieur_facture,
			"value"=>$d,
			"key"=>"date_effective"
		);


		$this->obj->updateDate($params);

		$this->assertEquals($d,$this->obj->select($this->id_copieur_facture,'date_effective'),"Problème dans le set de la date effective.");

		$params = array(
			"id_copieur_facture"=>$this->id_copieur_facture,
			"value"=>$d,
			"key"=>"date"
		);	

		$this->obj->updateDate($params);

		$this->assertEquals($d,$this->obj->select($this->id_copieur_facture,'date'),"Problème dans le set de la date .");



	}


	/*@author Quentin JANON <qjanon@absystech.fr>  */
	public function test_select_all() {
		

		$r = $this->obj->select_all();
		$this->assertArrayHasKey("retard",$r[0],"Il manque le retard");

	}


	/*@author Quentin JANON <qjanon@absystech.fr>  */
	public function test_getLastFacture() {
		$r = $this->obj->getLastFacture($this->id_affaire_cout_page);
		$this->assertEquals($this->obj->decryptId($this->id_copieur_facture),$r['id_copieur_facture'],"Erreur dans la récupération de la facture.");

		//copieur_facture
		$copieur_facture["copieur_facture"]["date"]=date("Y-m-d",strtotime(date("Y-m-d")." +1 day"));
		$copieur_facture["copieur_facture"]['id_termes']=1;
		$copieur_facture["copieur_facture"]['id_societe']=$this->id_societe;
		$copieur_facture["copieur_facture"]['tva']=1.8;
		$copieur_facture["copieur_facture"]['id_affaire_cout_page']=$this->id_affaire_cout_page;
		$copieur_facture["copieur_facture"]['releve_compteurNB']=36200;
		$copieur_facture["copieur_facture"]['releve_compteurC']=36211;
		
		//copieur_facture_ligne
		$copieur_facture["values_copieur_facture"]=array("produits"=>'[{"copieur_facture_ligne__dot__type":"fhdfghdf","copieur_facture_ligne__dot__designation":"sdfghndhyfnsn sfnbsg","copieur_facture_ligne__dot__quantite":"","copieur_facture_ligne__dot__prix_achatNB":"0.00500","copieur_facture_ligne__dot__prix_achatC":"0.50000","copieur_facture_ligne__dot__prixNB":"0.00700","copieur_facture_ligne__dot__prixC":"0.70000"},{"copieur_facture_ligne__dot__type":"jgvjg","copieur_facture_ligne__dot__designation":"jgvjgv","copieur_facture_ligne__dot__quantite":"1","copieur_facture_ligne__dot__prix_achatNB":"0.20000","copieur_facture_ligne__dot__prix_achatC":"0.30000","copieur_facture_ligne__dot__prixNB":"0.40000","copieur_facture_ligne__dot__prixC":"0.50000"}]');

		//Insertion
		$id_copieur_facture2 = ATF::copieur_facture()->insert($copieur_facture);
		$r = $this->obj->getLastFacture($this->id_affaire_cout_page,100000000);
		$this->assertEquals($this->obj->decryptId($id_copieur_facture2),$r['id_copieur_facture'],"Erreur dans la récupération de la facture 2.");

	}

};
?>