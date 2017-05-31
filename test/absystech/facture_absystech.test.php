<?
class facture_absystech_test extends ATF_PHPUnit_Framework_TestCase {
	//@author  Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  
	function setUp() {
		$this->initUser();

		//Contact
		$contact["nom"]="Tu_devis";
		$this->id_contact=ATF::contact()->insert($contact);

		//Devis
		$this->devis["devis"]["id_contact"]=$this->id_contact;
		$this->devis["devis"]['resume']='Tu_devis';
		$this->devis["devis"]["date"]=date('Y-m-d');
		$this->devis["devis"]['id_societe']=$this->id_societe;
		$this->devis["devis"]['validite']=date('Y-m-d');
		$this->devis["devis"]['prix']="200";
		$this->devis["devis"]['frais_de_port']="50";
		$this->devis["devis"]['prix_achat']="50";
		
		//Devis_ligne
		$this->devis["values_devis"]=array("produits"=>'[{
			"devis_ligne__dot__ref":"TU",
			"devis_ligne__dot__produit":"Tu_devis",
			"devis_ligne__dot__quantite":"15",
			"devis_ligne__dot__poids":"10",
			"devis_ligne__dot__prix":"10",
			"devis_ligne__dot__prix_achat":"10",
			"devis_ligne__dot__id_fournisseur":"1",
			"devis_ligne__dot__id_compte_absystech":"1",
			"devis_ligne__dot__marge":97.14,
			"devis_ligne__dot__id_fournisseur_fk":"1"
		}]');

		//Insertion
		$this->id_devis = ATF::devis()->insert($this->devis,$this->s);
		$this->id_affaire = ATF::devis()->select($this->id_devis,"id_affaire");
	
		//Commande
		$this->commande["commande"]=$this->devis["devis"];
		$this->commande["commande"]["id_affaire"]=$this->id_affaire;
		$this->commande["commande"]["id_devis"]=$this->id_devis;
		
		//Commande_ligne
		$this->commande["values_commande"]=array("produits"=>'[{
			"commande_ligne__dot__ref":"TU",
			"commande_ligne__dot__produit":"Tu_commande",
			"commande_ligne__dot__quantite":"15",
			"commande_ligne__dot__prix":"10",
			"commande_ligne__dot__prix_achat":"10",
			"commande_ligne__dot__id_fournisseur":"1",
			"commande_ligne__dot__id_compte_absystech":"1",
			"commande_ligne__dot__marge":97.14,
			"commande_ligne__dot__id_fournisseur_fk":"1"
		}]');

		//Insertion
		unset($this->commande["commande"]["id_contact"],$this->commande["commande"]["validite"]);
		$this->id_commande = ATF::commande()->insert($this->commande,$this->s);

		//Facture
		$this->facture["facture"]=$this->commande["commande"];
		$this->facture["facture"]["date"]="2010-01-01";
		$this->facture["facture"]["id_affaire"]=$this->id_affaire;
		$this->facture["facture"]["mode"]="facture";
		$this->facture["facture"]["id_termes"]=2;
		$this->facture["facture"]["tva"]=1.2;
		
		//Facture_ligne
		$this->facture["values_facture"]=array("produits"=>'[{
			"facture_ligne__dot__ref":"TU",
			"facture_ligne__dot__produit":"Tu_facture",
			"facture_ligne__dot__quantite":"15",
			"facture_ligne__dot__prix":"20",
			"facture_ligne__dot__prix_achat":"10",
			"facture_ligne__dot__id_fournisseur":"1",
			"facture_ligne__dot__serial":"777",
			"facture_ligne__dot__id_compte_absystech":"1",
			"facture_ligne__dot__marge":97.14,
			"facture_ligne__dot__id_fournisseur_fk":"1"
		}]');

		//Insertion
		unset($this->facture["facture"]["resume"],$this->facture["facture"]["prix_achat"],$this->facture["facture"]["id_devis"]);
		$this->id_facture = $this->obj->insert($this->facture,$this->s);
	}

	//@author  Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> 
	function tearDown(){
		$this->rollback_transaction();
	}

	// @author Yann GAUTHERON <ygautheron@absystech.fr>
	public function testAutocompleteConditions(){
		$this->assertEquals(
			array("condition_field"=>array("facture.id_societe","affaire.truc"),"condition_value"=>array(3,2)),
			$this->obj->autocompleteConditions(ATF::affaire(),array("facture"=>array("id_societe"=>3)),"truc",2),
			"Le couple condition_field/value est incorrect"
		);
	}

	//@author  Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	public function testUpdate(){

		$facture["facture"]=$this->commande["commande"];
		$facture["facture"]["date"]="2010-01-03";
		$facture["facture"]["id_facture"]=$this->id_facture;
		
		//Facture_ligne
		$facture["values_facture"]=array("produits"=>'[
			{
				"facture_ligne__dot__ref":"TU1",
				"facture_ligne__dot__produit":"Tu_facture1",
				"facture_ligne__dot__quantite":"5",
				"facture_ligne__dot__prix":"25",
				"facture_ligne__dot__prix_achat":"15",
				"facture_ligne__dot__id_fournisseur":"2",
				"facture_ligne__dot__serial":"666",
				"facture_ligne__dot__id_compte_absystech_fk":"2",
				"facture_ligne__dot__id_compte_absystech":"2",
				"facture_ligne__dot__marge":97.14,
				"facture_ligne__dot__id_fournisseur_fk":"2",
				"facture_ligne__dot__id_facture":"'.$this->id_facture.'"
			}
		]');

		unset($facture["facture"]["resume"],$facture["facture"]["prix_achat"],$facture["facture"]["id_devis"]);
		$facture["preview"]=true;
		$this->obj->update($facture,$this->s);

		$facture1=$this->obj->select($this->id_facture);

		$this->assertEquals(ATF::facture()->select($this->id_facture,'prix'),$facture1['prix'],"L'update est rollback, il ne devrait donc pas y avoir de modification du prix.");
		$this->assertEquals("2010-01-03",$facture1["date"],"2 Problème sur la date de la facture lors de l'update");

		ATF::facture_ligne()->q->reset()->addCondition("id_facture",$this->obj->decryptId($this->id_facture));

		$facture_ligne1=ATF::facture_ligne()->select_all();
		
		$this->assertEquals("TU1",$facture_ligne1[0]["ref"],"3 Problème sur la ref de la facture_ligne lors de l'update");
		$this->assertEquals("Tu_facture1",$facture_ligne1[0]["produit"],"4 Problème sur le produit de la facture_ligne lors de l'update");
		$this->assertEquals("5.0",$facture_ligne1[0]["quantite"],"5 Problème sur la quantite de la facture_ligne lors de l'update");
		$this->assertEquals("25.00",$facture_ligne1[0]["prix"],"5 Problème sur la quantite de la facture_ligne lors de l'update");
		$this->assertEquals("666",$facture_ligne1[0]["serial"],"6 Problème sur le serial de la facture_ligne lors de l'update");
		$this->assertEquals("2",$facture_ligne1[0]["id_compte_absystech"],"7 Problème sur le id_compte_absystech de la facture_ligne lors de l'update");
		$this->assertEquals("2",$facture_ligne1[0]["id_fournisseur"],"8 Problème sur le id_fournisseur de la facture_ligne lors de l'update");
		$this->assertEquals("15.00",$facture_ligne1[0]["prix_achat"],"9 Problème sur le prix_achat de la facture_ligne lors de l'update");

		$facture["preview"]=false;
		$facture["facture"]["emailTexte"]="TU facture1";

		//Sans mail
		try {
			$this->obj->update($facture,$this->s);
		} catch (errorATF $e) {
			$erreur_trouvee1 = $e->getCode();
		}

		$this->assertEquals(166,$erreur_trouvee1,"ERREUR 1 NON ATTRAPPEE (il n'y a pas d'email pour ce contact)");
		ATF::societe()->update(array("id_societe"=>ATF::societe()->decryptId($this->id_societe),"id_contact_facturation"=>ATF::contact()->decryptId($this->id_contact)));
		ATF::contact()->update(array("id_contact"=>ATF::contact()->decryptId($this->id_contact),"email"=>"tu@absystech.net"));

		$this->obj->update($facture,$this->s);

		$facture["facture"]["email"]="tu@absystech.net";
		$facture["facture"]["emailCopie"]="tu@absystech.net";
	
		//Facture_ligne
		$facture["values_facture"]=array("produits"=>'[{
			"facture_ligne__dot__ref":"TU1",
			"facture_ligne__dot__produit":"Tu_facture1",
			"facture_ligne__dot__quantite":"10",
			"facture_ligne__dot__prix":"25",
			"facture_ligne__dot__prix_achat":"15",
			"facture_ligne__dot__id_fournisseur":"2",
			"facture_ligne__dot__serial":"666",
			"facture_ligne__dot__id_compte_absystech":"2",
			"facture_ligne__dot__marge":97.14,
			"facture_ligne__dot__id_fournisseur_fk":"1",
			"facture_ligne__dot__id_facture":"'.$this->id_facture.'"
		}]');

		$this->obj->update($facture,$this->s);

		$facture2=$this->obj->select($this->id_facture);
		$this->assertEquals("300.0",$facture2["prix"],"11 Problème sur le prix de la facture lors de l'update");

		ATF::facture_ligne()->q->reset()->addCondition("id_facture",$this->obj->decryptId($this->id_facture));

		$facture_ligne2=ATF::facture_ligne()->select_all();
		$this->assertEquals("10.0",$facture_ligne2[0]["quantite"],"10 Problème sur la quantite de la quantite lors de l'update");

		// PARTIE PERIODIQUE !
		
		$facture["facture"]["periodicite"] = "mensuelle";
		try {
			$this->obj->update($facture,$this->s);
		} catch (errorATF $e) {
			$erreur_trouvee1 = $e->getCode();
		}
		$this->assertEquals(175,$erreur_trouvee1,"ERREUR 2 NON ATTRAPPEE (il n'y a pas de date de debut de periode)");
		
		$facture["facture"]["date_debut_periode"] = "2013-01-01";
		$facture["facture"]["id_termes"] = NULL;
		$facture["facture"]["type_facture"] = "facture";
		
		try {
			$erreur_trouvee1 = null;
			$this->obj->update($facture,$this->s);
		} catch (errorATF $e) {
			$erreur_trouvee1 = $e->getCode();
		}
		$this->assertEquals(167,$erreur_trouvee1,"ERREUR 2 NON ATTRAPPEE (il n'y a pas de terme)");
		$facture["facture"]["id_termes"] = 2;
		
		$this->obj->update($facture,$this->s);
		$facture2=$this->obj->select($this->id_facture);		
		$this->assertEquals("2013-01-01",$facture2["date_debut_periode"],"1 Problème sur date_debut_periode de la facture lors de l'update");
		
		
		$facture["facture"]["periodicite"] = "trimestrielle";
		$facture["facture"]["date_debut_periode"] = "2013-01-01";
		$this->obj->update($facture,$this->s);
		$facture2=$this->obj->select($this->id_facture);		
		$this->assertEquals("2013-01-01",$facture2["date_debut_periode"],"2 Problème sur date_debut_periode de la facture lors de l'update");
		
		$facture["facture"]["periodicite"] = "trimestrielle";
		$facture["facture"]["date_debut_periode"] = "2013-04-01";
		$this->obj->update($facture,$this->s);
		$facture2=$this->obj->select($this->id_facture);		
		$this->assertEquals("trimestrielle",$facture2["periodicite"],"3 Problème sur date_debut_periode de la facture lors de l'update");
		
		$facture["facture"]["periodicite"] = "trimestrielle";
		$facture["facture"]["date_debut_periode"] = "2013-09-01";
		$this->obj->update($facture,$this->s);
		$facture2=$this->obj->select($this->id_facture);		
		$this->assertEquals("2013-09-01",$facture2["date_debut_periode"],"4 Problème sur date_debut_periode de la facture lors de l'update");
		
		$facture["facture"]["periodicite"] = "trimestrielle";
		$facture["facture"]["date_debut_periode"] = "2013-10-01";
		$this->obj->update($facture,$this->s);
		$facture2=$this->obj->select($this->id_facture);		
		$this->assertEquals("2013-10-01",$facture2["date_debut_periode"],"5 Problème sur date_debut_periode de la facture lors de l'update");
		
		$facture["facture"]["periodicite"] = "annuelle";
		$facture["facture"]["date_debut_periode"] = "2013-01-01";
		$this->obj->update($facture,$this->s);
		$facture2=$this->obj->select($this->id_facture);		
		$this->assertEquals("2013-01-01",$facture2["date_debut_periode"],"6 Problème sur date_debut_periode de la facture lors de l'update");
		$this->assertEquals("annuelle",$facture2["periodicite"],"6 Problème sur periodicite de la facture lors de l'update");
		
		
		$facture["facture"]["periodicite"] = "semestrielle";
		$facture["facture"]["date_debut_periode"] = "2013-02-01";
		$this->obj->update($facture,$this->s);
		$facture2=$this->obj->select($this->id_facture);		
		$this->assertEquals("2013-02-01",$facture2["date_debut_periode"],"7 Problème sur date_debut_periode de la facture lors de l'update");
		$this->assertEquals("semestrielle",$facture2["periodicite"],"7 Problème sur periodicite de la facture lors de l'update");
		
		
		$facture["facture"]["date_debut_periode"] = "2013-08-01";
		$this->obj->update($facture,$this->s);
		$facture2=$this->obj->select($this->id_facture);		
		$this->assertEquals("2013-08-01",$facture2["date_debut_periode"],"8 Problème sur date_debut_periode de la facture lors de l'update");
		$this->assertEquals("semestrielle",$facture2["periodicite"],"8 Problème sur periodicite de la facture lors de l'update");

		//$infos= array("date" => "2013-01-01", "periodicite" => "trimestrielle", "date_debut_periode" => "2013-01-01", "id_facture" => $this->id_facture);
		//$this->obj->update($infos,$this->s);
	}


	//@author  Quentin JANON <qjanon@absystech.fr> */ 
	public function testUpdateAvoir(){

		$facture["facture"]=$this->commande["commande"];
		$facture["facture"]["date"]="2010-01-03";
		$facture["facture"]["id_facture"]=$this->id_facture;
		$facture["facture"]["mode"]="avoir";

		//Facture_ligne
		$facture["values_facture"]=array("produits"=>'[
			{
				"facture_ligne__dot__ref":"TU1",
				"facture_ligne__dot__produit":"Tu_facture1",
				"facture_ligne__dot__quantite":"5",
				"facture_ligne__dot__prix":"25",
				"facture_ligne__dot__prix_achat":"15",
				"facture_ligne__dot__id_fournisseur":"2",
				"facture_ligne__dot__serial":"666",
				"facture_ligne__dot__id_compte_absystech_fk":"2",
				"facture_ligne__dot__id_compte_absystech":"2",
				"facture_ligne__dot__marge":97.14,
				"facture_ligne__dot__id_fournisseur_fk":"2",
				"facture_ligne__dot__id_facture":"'.$this->id_facture.'"
			}
		]');

		unset($facture["facture"]["resume"],$facture["facture"]["prix_achat"],$facture["facture"]["id_devis"]);
		$err = false;
		try {
			$this->obj->update($facture,$this->s);
		} catch (errorATF $e) {
			$err = true;
		}

		$this->assertTrue($err,"Pas de facture parente donc une erreur.");

		$facture["facture"]["id_facture_parente"]=$this->id_facture;

		$this->obj->update($facture,$this->s);

		$f=$this->obj->select($this->id_facture);

		$this->assertLessThan(0,$f['prix'],"Le prix devrait être négatif car c'est un avoir.");
		$this->assertEquals("avoir",$f['type_facture'],"Le type_facture devrait être avoir.");



	}

	//@author  Quentin JANON <qjanon@absystech.fr> */
	public function testUpdateFactor(){

		$facture["facture"]=$this->commande["commande"];
		$facture["facture"]["date"]="2010-01-03";
		$facture["facture"]["id_facture"]=$this->id_facture;
		$facture["facture"]["mode"]="factor";

		//Facture_ligne
		$facture["values_facture"]=array("produits"=>'[
			{
				"facture_ligne__dot__ref":"TU1",
				"facture_ligne__dot__produit":"Tu_facture1",
				"facture_ligne__dot__quantite":"5",
				"facture_ligne__dot__prix":"25",
				"facture_ligne__dot__prix_achat":"15",
				"facture_ligne__dot__id_fournisseur":"2",
				"facture_ligne__dot__serial":"666",
				"facture_ligne__dot__id_compte_absystech_fk":"2",
				"facture_ligne__dot__id_compte_absystech":"2",
				"facture_ligne__dot__marge":97.14,
				"facture_ligne__dot__id_fournisseur_fk":"2",
				"facture_ligne__dot__id_facture":"'.$this->id_facture.'"
			}
		]');

		unset($facture["facture"]["resume"],$facture["facture"]["prix_achat"],$facture["facture"]["id_devis"]);
		$err = false;
		try {
			$societe = array("id_societe"=>$this->id_societe,"rib_affacturage"=>"","iban_affacturage"=>"","bic_affacturage"=>"");
			ATF::societe()->u($societe);
			$this->obj->update($facture,$this->s);
		} catch (errorATF $e) {
			$err = true;
		}

		$this->assertTrue($err,"Pas de RIB,IBAN,BIC sur la société donc une erreur.");

		$societe = array("id_societe"=>$this->id_societe,"rib_affacturage"=>"test","iban_affacturage"=>"test","bic_affacturage"=>"test");
		ATF::societe()->u($societe);
		$this->obj->update($facture,$this->s);

		$f=$this->obj->select($this->id_facture);

		$this->assertEquals("factor",$f['type_facture'],"Le type_facture devrait être factor.");



	}

	//@author  Quentin JANON <qjanon@absystech.fr> */
	public function test_can_update() {
		$this->obj->q->reset()->where("type_facture","facture");
		$r = $this->obj->select_row();
		$this->assertTrue($this->obj->can_update($r['facture.id_facture']),"Peut etre modifié si c'est un type facture");

		$this->obj->q->reset()->where("type_facture","avoir");
		$r = $this->obj->select_row();
		$this->assertTrue($this->obj->can_update($r['facture.id_facture']),"Peut etre modifié si c'est un type avoir");

		$this->obj->q->reset()->where("type_facture","acompte");
		$r = $this->obj->select_row();
		$err1=false;
		try {
			$this->obj->can_update($r['facture.id_facture']);
		} catch (errorATF $e) {
			$err1 = true;
		}
		$this->assertTrue($err1,"Peut etre modifié si c'est un type acompte");

		$this->obj->q->reset()->where("type_facture","solde");
		$r = $this->obj->select_row();
		$err2=false;
		try {
			$this->assertFalse($this->obj->can_update($r['facture.id_facture']),"Peut etre modifié si c'est un type solde");
		} catch (errorATF $e) {
			$err2 = true;
		}
		$this->assertTrue($err2,"Peut etre modifié si c'est un type solde");


	}

	// @author Yann GAUTHERON <ygautheron@absystech.fr>
	function testInsertErreurs(){

		//Facture sans commande
		$facture=$this->obj->select($this->id_facture);
		$this->assertEquals("impayee",$facture["etat"],"1 Problème sur l'état de la facture lors de l'insertion de la facture");
		$this->assertEquals("350.00",$facture["prix"],"2 Problème sur le prix de la facture lors de l'insertion de la facture");
		$this->assertEquals("facture",$facture["type_facture"],"3 Problème sur le type de facture lors de l'insertion de la facture");
		ATF::commande_facture()->q->reset()->addCondition("id_facture",$this->obj->decryptId($this->id_facture));
		$this->assertNull(ATF::commande_facture()->select_all(),"3 Insert a inséré une commande_facture alors qu'elle n'aurait pas dû");
		ATF::commande_facture()->q->reset()->addCondition("id_commande",ATF::commande()->decryptId($this->id_commande));
		$this->assertNull(ATF::commande_facture()->select_all(),"4 Insert a inséré une commande_facture alors qu'elle n'aurait pas dû");


		//Facture acompte avec commande
		$facture2=$this->facture;
		$facture2["facture"]["id_commande"]=$this->id_commande;
		$facture2["facture"]["acompte_pourcent"]=25;
		$facture2["facture"]["tva"]=3;
		$facture2["facture"]["dematerialisation"]=3;
		$facture2["preview"]=true;
		ATF::$usr->set('id_profil',2);
		$id_facture2=$this->obj->insert($facture2,$this->s);
		ATF::$usr->set('id_profil',1);
		$this->assertEquals(array(
									0=>array(
										"msg"=>"Seul le profil Associé permet de modifier la TVA. La TVA de cette facture sera donc de 1.2",
										"title"=>"Droits d'accès requis pour cette opération ! ",
										"timer"=>""
										)
							),ATF::$msg->getNotices(),"La notice de TVA ne se fait pas");

		$facture2_select=$this->obj->select($id_facture2);
		ATF::commande_facture()->q->reset()->addCondition("id_facture",$this->obj->decryptId($id_facture2));
		$this->assertNotNull(ATF::commande_facture()->select_all(),"5 Insert n'a pas inséré de commande_facture alors qu'elle n'aurait pas dû");
		ATF::commande_facture()->q->reset()->addCondition("id_commande",ATF::commande()->decryptId($this->id_commande));
		$this->assertNotNull(ATF::commande_facture()->select_all(),"6 Insert n'a pas inséré de commande_facture alors qu'elle n'aurait pas dû");
		$this->assertEquals("50.00",$facture2_select["frais_de_port"],"7 Les frais de port ne doivent pas bouger ici");
		$this->assertEquals("87.5",$facture2_select["prix"],"8 Problème sur les prix de port d'un accompte : 0.25*350");
		$this->assertEquals("acompte",$facture2_select["type_facture"],"9 Problème sur le type facture de port d'un accompte");

		//Facture solde finale
		$facture3=$this->facture;
		$facture3["facture"]["id_commande"]=$this->id_commande;
		$facture3["facture"]["finale"]=true;
		$facture3["facture"]["emailTexte"]="TU facture";
		$facture3["facture"]["email"]="tu@absystech.net";
		$facture3["facture"]["emailCopie"]="tu@absystech.net";

		$id_facture3=$this->obj->insert($facture3,$this->s);
		$facture3_select=$this->obj->select($id_facture3);
		$this->assertEquals("50.00",$facture3_select["frais_de_port"],"10 Les frais de port ne doivent pas bouger ici");
		$this->assertEquals("262.50",$facture3_select["prix"],"11 Problème sur les prix de port d'un solde : 350-87.5(montant accompte deja versé)");
		$this->assertEquals("solde",$facture3_select["type_facture"],"12 Problème sur le type facture de port d'un solde");

		//Facture avoir sans affaire
		$facture4=$this->facture;
		unset($facture4["facture"]["id_affaire"]);
		$facture4["facture"]["mode"]="avoir";		
		$facture4["facture"]["emailTexte"]="TU facture";
		//Pas de lignes
		try {
			unset($facture4["values_facture"]);
			$this->obj->insert($facture4,$this->s);
		} catch (errorATF $e) {
			$erreur_trouveelignes = $e->getCode();
		}
		$this->assertEquals(161,$erreur_trouveelignes,"ERREUR lignes NON ATTRAPPEE (il n'y a pas de lignes)");
		$facture4["values_facture"]=array("produits"=>'[{"facture_ligne__dot__ref":"TU","facture_ligne__dot__produit":"Tu_facture","facture_ligne__dot__quantite":"","facture_ligne__dot__prix":"20","facture_ligne__dot__prix_achat":"10","facture_ligne__dot__id_fournisseur":"1","facture_ligne__dot__serial":"777","facture_ligne__dot__id_compte_absystech":"1","facture_ligne__dot__marge":97.14,"facture_ligne__dot__id_fournisseur_fk":"1"}]');

		//Pas de société
		try {
			$facture4["facture"]["id_societe"]=NULL;
			$this->obj->insert($facture4,$this->s);
		} catch (errorATF $e) {
			$erreur_trouvee1 = $e->getCode();
		}
		$this->assertEquals(167,$erreur_trouvee1,"ERREUR 1 NON ATTRAPPEE (il n'y a pas de société)");
		$facture4["facture"]["id_societe"]=$this->id_societe;

		//Pas d'affaire
		try {
			$this->obj->insert($facture4,$this->s);
		} catch (errorATF $e) {
			$erreur_trouvee1 = $e->getCode();
		}
		$this->assertEquals(160,$erreur_trouvee1,"ERREUR 1 NON ATTRAPPEE (il n'y a pas d'affaire)");

		//Pas de libellé affaire devis
		$facture4["facture"]["affaire_sans_devis"]=true;
		try {
			$this->obj->insert($facture4,$this->s);
		} catch (errorATF $e) {
			$erreur_trouveeSansDevis = $e->getCode();
		}
		$this->assertEquals(162,$erreur_trouveeSansDevis,"ERREUR pas de libelle affaire dans devis NON ATTRAPPEE");
		unset($facture4["facture"]["affaire_sans_devis"]);

		$facture4["facture"]["id_affaire"]=$this->id_affaire;
		//Avoir sans facture parente
		try {
			$this->obj->insert($facture4,$this->s);
		} catch (errorATF $e) {
			$erreur_trouvee1 = $e->getCode();
		}
		$this->assertEquals(170,$erreur_trouvee1,"ERREUR 1 NON ATTRAPPEE (il n'y a pas de facture parente pour l'avoir)");
		$facture4["facture"]["id_facture_parente"]=1;

		ATF::contact()->update(array("id_contact"=>ATF::contact()->decryptId($this->id_contact),"email"=>"tu@absystech.net"));
		ATF::societe()->update(array("id_societe"=>ATF::societe()->decryptId($this->id_societe),"id_contact_facturation"=>ATF::contact()->decryptId($this->id_contact)));
		unset($facture4["facture"]["id_affaire"]);
		$facture4["facture"]["affaire_sans_devis"]=true;
		$facture4["facture"]["affaire_sans_devis_libelle"]="Libellé";
		$id=$this->obj->insert($facture4,$this->s);
		$facture= ATF::facture()->select($id);
		$this->assertEquals($facture["date"],$facture4["facture"]["date"],"facture non cree avec les bonnes infos");
	}

	
	// @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	// @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	// @author Yann GAUTHERON <ygautheron@absystech.fr>
	function testInsert(){

		//Facture sans commande
		$facture=$this->obj->select($this->id_facture);
		$this->assertEquals("impayee",$facture["etat"],"1 Problème sur l'état de la facture lors de l'insertion de la facture");
		$this->assertEquals("350.00",$facture["prix"],"2 Problème sur le prix de la facture lors de l'insertion de la facture");
		$this->assertEquals("facture",$facture["type_facture"],"3 Problème sur le type de facture lors de l'insertion de la facture");
		ATF::commande_facture()->q->reset()->addCondition("id_facture",$this->obj->decryptId($this->id_facture));
		$this->assertNull(ATF::commande_facture()->select_all(),"3 Insert a inséré une commande_facture alors qu'elle n'aurait pas dû");
		ATF::commande_facture()->q->reset()->addCondition("id_commande",ATF::commande()->decryptId($this->id_commande));
		$this->assertNull(ATF::commande_facture()->select_all(),"4 Insert a inséré une commande_facture alors qu'elle n'aurait pas dû");


		//Facture acompte avec commande
		$facture2=$this->facture;
		$facture2["facture"]["id_commande"]=$this->id_commande;
		$facture2["facture"]["acompte_pourcent"]=25;
		$facture2["facture"]["tva"]=3;
		$facture2["facture"]["dematerialisation"]=3;
		$facture2["preview"]=true;
		ATF::$usr->set('id_profil',2);
		$id_facture2=$this->obj->insert($facture2,$this->s);
		ATF::$usr->set('id_profil',1);
		$this->assertEquals(array(
									0=>array(
										"msg"=>"Seul le profil Associé permet de modifier la TVA. La TVA de cette facture sera donc de 1.2",
										"title"=>"Droits d'accès requis pour cette opération ! ",
										"timer"=>""
										)
							),ATF::$msg->getNotices(),"La notice de TVA ne se fait pas");

		$facture2_select=$this->obj->select($id_facture2);
		ATF::commande_facture()->q->reset()->addCondition("id_facture",$this->obj->decryptId($id_facture2));
		$this->assertNotNull(ATF::commande_facture()->select_all(),"5 Insert n'a pas inséré de commande_facture alors qu'elle n'aurait pas dû");
		ATF::commande_facture()->q->reset()->addCondition("id_commande",ATF::commande()->decryptId($this->id_commande));
		$this->assertNotNull(ATF::commande_facture()->select_all(),"6 Insert n'a pas inséré de commande_facture alors qu'elle n'aurait pas dû");
		$this->assertEquals("50.00",$facture2_select["frais_de_port"],"7 Les frais de port ne doivent pas bouger ici");
		$this->assertEquals("87.5",$facture2_select["prix"],"8 Problème sur les prix de port d'un accompte : 0.25*350");
		$this->assertEquals("acompte",$facture2_select["type_facture"],"9 Problème sur le type facture de port d'un accompte");

		
		//Facture solde finale
		$facture3=$this->facture;
		$facture3["facture"]["id_commande"]=$this->id_commande;
		$facture3["facture"]["finale"]=true;
		$facture3["facture"]["emailTexte"]="TU facture";
		$facture3["facture"]["email"]="tu@absystech.net";
		$facture3["facture"]["emailCopie"]="tu@absystech.net";

		$id_facture3=$this->obj->insert($facture3,$this->s);
		$facture3_select=$this->obj->select($id_facture3);
		$this->assertEquals("50.00",$facture3_select["frais_de_port"],"10 Les frais de port ne doivent pas bouger ici");
		$this->assertEquals("262.50",$facture3_select["prix"],"11 Problème sur les prix de port d'un solde : 350-87.5(montant accompte deja versé)");
		$this->assertEquals("solde",$facture3_select["type_facture"],"12 Problème sur le type facture de port d'un solde");

		
		//Facture avoir sans affaire
		$facture4=$this->facture;
		unset($facture4["facture"]["id_affaire"]);
		$facture4["facture"]["mode"]="avoir";		
		$facture4["facture"]["emailTexte"]="TU facture";
		$facture4["values_facture"]=array("produits"=>'[{"facture_ligne__dot__ref":"TU","facture_ligne__dot__produit":"Tu_facture","facture_ligne__dot__quantite":"","facture_ligne__dot__prix":"20","facture_ligne__dot__prix_achat":"10","facture_ligne__dot__id_fournisseur":"1","facture_ligne__dot__serial":"777","facture_ligne__dot__id_compte_absystech":"1","facture_ligne__dot__marge":97.14,"facture_ligne__dot__id_fournisseur_fk":"1"}]');
		$facture4["facture"]["id_societe"]=NULL;
		$facture4["facture"]["id_societe"]=$this->id_societe;
		$facture4["facture"]["id_affaire"]=$this->id_affaire;
		//Avoir sans facture parente
		try {
			$this->obj->insert($facture4,$this->s);
		} catch (errorATF $e) {
			$erreur_trouvee1 = $e->getCode();
		}
		$this->assertEquals(170,$erreur_trouvee1,"ERREUR 1 NON ATTRAPPEE (il n'y a pas de facture parente pour l'avoir)");
		$facture4["facture"]["id_facture_parente"]=1;

		//Sans mail
		try {
			$this->obj->insert($facture4,$this->s);
		} catch (errorATF $e) {
			$erreur_trouvee1 = $e->getCode();
		}
		$this->assertEquals(166,$erreur_trouvee1,"ERREUR 1 NON ATTRAPPEE (il n'y a pas d'email pour ce contact)");

		ATF::societe()->update(array("id_societe"=>ATF::societe()->decryptId($this->id_societe),"id_contact_facturation"=>ATF::contact()->decryptId($this->id_contact)));


		//Sans mail 2
		try {
			$this->obj->insert($facture4,$this->s);
		} catch (errorATF $e) {
			$erreur_trouvee11 = $e->getCode();
		}
		$this->assertEquals(166,$erreur_trouvee11,"ERREUR 11 NON ATTRAPPEE (il n'y a pas d'email pour ce contact)");

		//Avec mail
		ATF::contact()->update(array("id_contact"=>ATF::contact()->decryptId($this->id_contact),"email"=>"tu@absystech.net"));
		
		ATF::societe()->u(array("id_societe" => $this->id_societe, "etat"=> "inactif"));
		try {
			$this->obj->insert($facture4,$this->s);
		} catch (errorATF $e) {
			$erreur_trouvee1 = $e->getMessage();
		}
		$this->assertEquals("Impossible d'ajouter une facture sur une entité fermée",$erreur_trouvee1,"ERREUR 1 NON ATTRAPPEE (societe inactive)");
		ATF::societe()->u(array("id_societe" => $this->id_societe, "etat"=> "actif"));
		
		// Société Belge
		ATF::societe()->u(array('id_societe'=>$this->id_societe,'reference_tva'=>"BE66666666"));

		$facture['frais_de_port'] = 0;
		$facture['prix'] = 50;	
		$id_facture4=$this->obj->insert($facture4,$this->s);
		$facture4_select=$this->obj->select($id_facture4);
		$affaire2=ATF::affaire()->select($facture4_select["id_affaire"]);
		$this->assertEquals("-50.00",$facture4_select["prix"],"13 Problème sur les prix de port d'un solde");
		$this->assertEquals("avoir",$facture4_select["type_facture"],"14 Problème sur le type facture de port d'un solde");
		$this->assertEquals(ATF::affaire()->decryptId($this->id_affaire),$facture4_select["id_affaire"],"14 Problème sur l'id_affaire facture de port d'un solde");

		//Facture factor
		$facture5=$this->facture;
		$facture5["facture"]["mode"]="factor";

		//Sans les champs obligatoires
		try {
			ATF::societe()->update(array("id_societe"=>ATF::societe()->decryptId($this->id_societe),"rib_affacturage"=>"","iban_affacturage"=>"","bic_affacturage"=>""));
			$this->obj->insert($facture5,$this->s);
		} catch (errorATF $e) {
			$erreur_trouvee2 = $e->getCode();
		}
		$this->assertEquals(167,$erreur_trouvee2,"ERREUR 2 NON ATTRAPPEE (Il manque l'une de ces informations pour la société : RIB, IBAN, BIC)");

		ATF::societe()->update(array("id_societe"=>ATF::societe()->decryptId($this->id_societe),"rib_affacturage"=>"aaaaaa","iban_affacturage"=>"aaaaaa","bic_affacturage"=>"aaaaaa"));

		$id_facture5=$this->obj->insert($facture5,$this->s);
		$facture5_select=$this->obj->select($id_facture5);
		$this->assertEquals("factor",$facture5_select["type_facture"],"14 Problème sur le type facture factor");
		
		//Facture 6 avec periodicité
		$facture6=$this->facture;
		$facture6["facture"]["periodicite"]="mensuelle";
		$facture6["facture"]["date_debut_periode"]="2010-01-15";
		
		$id_facture6=$this->obj->insert($facture6,$this->s);		
		ATF::facture_ligne()->q->reset()->addCondition('id_facture',$id_facture6)->end();
		$infos_facture_produit = ATF::facture_ligne()->select_all();

		$facture6["facture"]["periodicite"]="mensuelle";
		$facture6["facture"]["date_debut_periode"]="2010-01-15";
		$facture6["facture"]["date_fin_periode"]="2010-01-18";		
		$id_facture6=$this->obj->insert($facture6,$this->s);
		
		$facture6=$this->facture;
		$facture6["facture"]["periodicite"]="trimestrielle";
		$facture6["facture"]["date_debut_periode"]="2010-01-15";
		
		$id_facture6=$this->obj->insert($facture6,$this->s);		
		ATF::facture_ligne()->q->reset()->addCondition('id_facture',$id_facture6)->end();
		$infos_facture_produit = ATF::facture_ligne()->select_all();
		
		$facture6=$this->facture;
		$facture6["facture"]["periodicite"]="trimestrielle";
		$facture6["facture"]["date_debut_periode"]="2010-04-15";
		
		$id_facture6=$this->obj->insert($facture6,$this->s);		
		ATF::facture_ligne()->q->reset()->addCondition('id_facture',$id_facture6)->end();
		$infos_facture_produit = ATF::facture_ligne()->select_all();

		$facture6["facture"]["date_fin_periode"]="2010-05-18";		
		$id_facture6=$this->obj->insert($facture6,$this->s);

		$facture6=$this->facture;
		$facture6["facture"]["periodicite"]="trimestrielle";
		$facture6["facture"]["date_debut_periode"]="2010-07-15";
		
		$id_facture6=$this->obj->insert($facture6,$this->s);		
		ATF::facture_ligne()->q->reset()->addCondition('id_facture',$id_facture6)->end();
		$infos_facture_produit = ATF::facture_ligne()->select_all();
		
		$facture6=$this->facture;
		$facture6["facture"]["periodicite"]="trimestrielle";
		$facture6["facture"]["date_debut_periode"]="2010-10-15";
		
		$id_facture6=$this->obj->insert($facture6,$this->s);		
		ATF::facture_ligne()->q->reset()->addCondition('id_facture',$id_facture6)->end();
		$infos_facture_produit = ATF::facture_ligne()->select_all();
		
		$facture6=$this->facture;
		ATF::affaire()->u(array("id_affaire" => $facture6["facture"]["id_affaire"], "date_fin_maintenance" => "2010-01-15"));
		$facture6["facture"]["periodicite"]="annuelle";
		$facture6["facture"]["date_debut_periode"]="2010-01-15";
		
		$id_facture6=$this->obj->insert($facture6,$this->s);		
		ATF::facture_ligne()->q->reset()->addCondition('id_facture',$id_facture6)->end();
		$infos_facture_produit = ATF::facture_ligne()->select_all();

		$facture6["facture"]["periodicite"]="annuelle";
		$facture6["facture"]["date_debut_periode"]="2010-01-15";
		$facture6["facture"]["date_fin_periode"]="2010-11-15";		
		$id_facture6=$this->obj->insert($facture6,$this->s);

		$facture7=$this->facture;
		$facture7["facture"]["periodicite"]="annuelle";
		$facture7["facture"]["date_debut_periode"]="";
		try {
			$erreur_trouvee = NULL;
			$id_facture7=$this->obj->insert($facture7,$this->s);
		} catch (errorATF $e) {		
			$erreur_trouvee = $e->getCode();
		}		
		$this->assertNull($id_facture7);	
		$this->assertEquals(175,$erreur_trouvee,"ERREUR 3 NON ATTRAPPEE (Il manque la date de début de periode");
		
		$facture8=$this->facture;
		$facture8["facture"]["periodicite"]="semestrielle";
		$facture8["facture"]["date_debut_periode"]="2015-01-15";		
		$facture8["facture"]["id_termes"] = NULL;
		$facture8["facture"]["type_facture"] = "facture";
		try {
			$erreur_trouvee = NULL;
			$id_facture8=$this->obj->insert($facture8,$this->s);
		} catch (errorATF $e) {		
			$erreur_trouvee = $e->getCode();
		}	
		$this->assertEquals(167,$erreur_trouvee,"ERREUR  NON ATTRAPPEE (Il manque les termes");
		$facture8["facture"]["id_termes"] = 2;
		try {
			$erreur_trouvee = NULL;
			$id_facture8=$this->obj->insert($facture8,$this->s);
		} catch (errorATF $e) {		
			$erreur_trouvee = $e->getCode();
		}
		$facture8["facture"]["date_debut_periode"]="2015-08-15";
		try {
			$erreur_trouvee = NULL;
			$id_facture8=$this->obj->insert($facture8,$this->s);
		} catch (errorATF $e) {		
			$erreur_trouvee = $e->getCode();
		}
		$facture8["facture"]["date_fin_periode"]="2015-02-15";
		$id_facture8=$this->obj->insert($facture8,$this->s);	
		
		
		
	}


	//@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> 
	function testDelete(){
		//Suppression multiple
		$facture0=$this->facture;
		$ids["id"][1]=$this->obj->insert($facture0,$this->s);
		$ids["strict"]=1;
		$this->obj->delete($ids,$this->s);
		$this->assertNull($this->obj->select($ids["id"][1]),"01 La Suppression multiple ne fonctionne pas");

		
		$facture6=$this->facture;
		$facture6["facture"]["periodicite"]="trimestrielle";
		$facture6["facture"]["date_debut_periode"]="2010-01-15";		
		$id_facture6=$this->obj->insert($facture6,$this->s);		
		
		
		$facture7=$this->facture;
		$facture7["facture"]["periodicite"]="mensuelle";
		$facture7["facture"]["date_debut_periode"]="2010-01-15";
		$id_facture7=$this->obj->insert($facture7,$this->s);
		
		$facture8=$this->facture;
		$facture8["facture"]["periodicite"]="annuelle";
		$facture8["facture"]["date_debut_periode"]="2010-01-15";
		$id_facture8=$this->obj->insert($facture8,$this->s);
		
		$facture9=$this->facture;	
		$id_facture9=$this->obj->insert($facture9,$this->s);
		
		$this->obj->delete($id_facture9,$this->s);
		$this->obj->delete($id_facture8,$this->s);
		$this->obj->delete($id_facture7,$this->s);
		$this->obj->delete($id_facture6,$this->s);

		//Facture simple (avec commande et devis)
	    $this->obj->delete($this->id_facture,$this->s);
		$this->assertNull($this->obj->select($this->id_facture),"1 La facture ne s'est pas bien supprimée");
		
		//Facture avec commande lié
		$facture=$this->facture;
		$facture["facture"]["id_commande"]=$this->id_commande;
		$facture["facture"]["finale"]=true;
		$id_facture=$this->obj->insert($facture,$this->s);
		$this->assertEquals("facturee",ATF::commande()->select($this->id_commande,"etat"),"2 La commande ne passe pas en facturée lorsqu'il y a une facture");
		$this->assertNotNull($this->obj->select($id_facture),"3 La facture ne s'est pas bien créé");
		$this->obj->delete($id_facture,$this->s);
		$this->assertNull($this->obj->select($id_facture),"4 La facture ne s'est pas bien supprimée lorsqu'il y a une commande liée");
		$this->assertEquals("en_cours",ATF::commande()->select($this->id_commande,"etat"),"5 La commande ne passe pas en en cours lorsque l'on supprime la facture");
		$this->assertEquals("commande",ATF::affaire()->select($facture["facture"]["id_affaire"],"etat"),"6 La suppression de l'affaire n'a pas permis de passer l'affaire en commande");


		//Facture sans commande dans l'affaire
		$facture2=$this->facture;
		ATF::commande()->d($this->id_commande);
		$id_facture2=$this->obj->insert($facture2,$this->s);
		$facture_select2=$this->obj->select($id_facture2);	
		$this->obj->delete($id_facture2,$this->s);
		$this->assertNull($this->obj->select($id_facture2),"7 La facture ne s'est pas bien supprimée lorsqu'il n'y a pas de commande dans l'affaire");
		$this->assertEquals("devis",ATF::affaire()->select($facture["facture"]["id_affaire"],"etat"),"8 La suppression de l'affaire n'a pas permis de passer l'affaire en devis");

		//Facture sans devis dans l'affaire
		$facture3=$this->facture;
		ATF::devis()->d($this->id_devis);
		$id_facture3=$this->obj->insert($facture3,$this->s);
		$facture_select3=$this->obj->select($id_facture3);	
		$this->obj->delete($id_facture3,$this->s);
		$this->assertNull($this->obj->select($id_facture3),"9 La facture ne s'est pas bien supprimée lorsqu'il n'y a pas de commande ni devis dans l'affaire");
		$this->assertNull(ATF::affaire()->select($facture_select3["id_affaire"]),"6 La suppression de facture n'a pas généré de suppression d'affaire");
				
	}

	//@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	function testIs_past(){
		$this->assertTrue($this->obj->is_past(date("Y-m-d",strtotime(date('Y-m-d')."-1 day"))),"Erreur sur is_past quand la date n'est pas passée");
		$this->assertFalse($this->obj->is_past(date("Y-m-d",strtotime(date('Y-m-d')."+1 day"))),"Erreur sur is_past quand la date est passée");
	}
		
	//@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> 
	function testSelect_all(){
		ATF::$msg->getNotices();
		$this->obj->q->reset()->addCondition("id_affaire",$this->id_affaire)->end();
		$this->obj->q->setCount();
		$r = $this->obj->select_all();
		$facture = $r['data'];

		$this->assertEquals("420.00",$facture[0]["prix_ttc"],"1 select_all ne renvoi pas le bon ttc pour une facture impayée");
		$this->assertEquals("420.00",$facture[0]["solde"],"2 select_all ne renvoi pas le bon solde pour une facture impayée");

		$facture_paiement["facture_paiement"]["id_facture"] = $this->id_facture;
		$facture_paiement["facture_paiement"]["montant"] = "240.00";
		$facture_paiement["facture_paiement"]["mode_paiement"] = "cheque";
		$facture_paiement["facture_paiement"]["date"] = date('Y-m-d');

		$id_facture_paiement = ATF::facture_paiement()->insert($facture_paiement);
		$n = ATF::$msg->getNotices();
/*
		$this->assertEquals(array(
									0=>array(
										"msg"=>"La facture '".$facture[0]["facture.ref"]."' est passée en payée.","title"=>"Succès !","timer"=>""
										)
							),$n,"3 La notice de facture payée ne se fait pas");*/
		$this->obj->q->reset()->addCondition("id_affaire",$this->id_affaire)->setCount()->end();
		$r = $this->obj->select_all();
		$facture = $r['data'];

		$this->assertEquals("420.00",$facture[0]["prix_ttc"],"4 select_all ne renvoi pas le bon ttc pour une facture payée");
		$this->assertEquals("180.00",$facture[0]["solde"],"5 select_all ne renvoi pas le bon solde pour une facture payée");
	}

	//@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> 
	function testSelect_all_allowSolde(){
		ATF::$msg->getNotices();
		$this->obj->q->reset()->addCondition("id_affaire",$this->id_affaire)->end();
		$this->obj->q->setCount();
		$r = $this->obj->select_all();
		$facture = $r['data'];

		$this->assertEquals("420.00",$facture[0]["prix_ttc"],"1 select_all ne renvoi pas le bon ttc pour une facture impayée");
		$this->assertEquals("420.00",$facture[0]["solde"],"2 select_all ne renvoi pas le bon solde pour une facture impayée");

		$facture_paiement["facture_paiement"]["id_facture"] = $this->id_facture;
		$facture_paiement["facture_paiement"]["montant"] = "420.00";
		$facture_paiement["facture_paiement"]["mode_paiement"] = "cheque";
		$facture_paiement["facture_paiement"]["date"] = date('Y-m-d');

		$id_facture_paiement = ATF::facture_paiement()->insert($facture_paiement);
		$n = ATF::$msg->getNotices();

		$this->obj->q->reset()->addCondition("id_affaire",$this->id_affaire)->setCount()->end();
		$r = $this->obj->select_all();
		$facture = $r['data'];
		$this->assertEquals("420.00",$facture[0]["prix_ttc"],"4 select_all ne renvoi pas le bon ttc pour une facture payée");
		$this->assertEquals("0.00",$facture[0]["solde"],"5 select_all ne renvoi pas le bon solde pour une facture payée");
	}
	
	function test_select_all_interet_et_relance_4() {
		$u = array('id_facture'=>$this->id_facture,'date_previsionnelle'=>"2000-01-01");
		$this->obj->u($u);
		ATF::relance()->truncate();
		$relance = array("id_facture"=>$this->id_facture,"date_1"=>date('Y-m-d'),"date_2"=>date('Y-m-d'),"date_demeurre"=>date('Y-m-d'));
		ATF::relance()->insert($relance);
		
		$this->obj->q->reset()->addCondition("id_affaire",$this->id_affaire)->end();
		$this->obj->q->setCount();
		$r = $this->obj->select_all();
		$facture = $r['data'];


		$this->assertFalse($facture[0]["allowPDFRelance"],"Il manque le allowPDFRelance a FALSE");
	}
	
	//@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  
	function testStats(){
		//nombre de taches 
		unset(ATF::stats()->liste_annees[$this->obj->table]);
		ATF::stats()->liste_annees[$this->obj->table][2011]=1;
		$graph=$this->obj->stats();
		$this->assertTrue(is_array($graph),"Méthode stats non fonctionnel");
		$this->assertTrue(isset($graph['params']),"Méthode stats non fonctionnel (niveau params)");
		$this->assertTrue(isset($graph['categories']['category']),"Méthode stats non fonctionnel (niveau categories)");
		$this->assertTrue(isset($graph['dataset']),"Méthode stats non fonctionnel (niveau dataset)");
		
		// nombre de taches créé pour un user 
		$graph=$this->obj->stats(false,'resteAPayer');
		$this->assertTrue(is_array($graph),"user Méthode stats non fonctionnel 2");
		$this->assertTrue(isset($graph['params']),"Méthode stats non fonctionnel (user niveau params) 2");
		$this->assertTrue(isset($graph['categories']['category']),"Méthode stats non fonctionnel (user niveau categories) 2");
		$this->assertTrue(isset($graph['dataset']),"Méthode stats non fonctionnel (user niveau dataset) 2");
		
		
		//nombre de taches créé pour un user 
		$graph=$this->obj->stats(false,'top10negatif');
		$this->assertTrue(is_array($graph),"user Méthode stats non fonctionnel 3");
		$this->assertTrue(isset($graph['params']),"Méthode stats non fonctionnel (user niveau params) 3");
		$this->assertTrue(isset($graph['categories']['category']),"Méthode stats non fonctionnel (user niveau categories) 3");
		$this->assertTrue(isset($graph['dataset']),"Méthode stats non fonctionnel (user niveau dataset) 3");
		
	}
	
	//@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	function testStats_CA(){ 
		$stats_CA=$this->obj->stats_CA(array("2005"=>true));
		$this->assertEquals("12",count($stats_CA),"1 stats_CA ne renvoi pas 12 stats pour une année");
		$this->assertEquals("2005",$stats_CA["11"]["year"],"2 stats_CA ne renvoi pas la bonne année");
		$this->assertEquals("1",$stats_CA["11"]["month"],"3 stats_CA ne renvoi pas la bonne année");
		$this->assertEquals("18492.30",$stats_CA["11"]["nb"],"4 stats_CA ne renvoi pas la bonne année");
		$this->assertEquals("348",$stats_CA["11"]["facture.id_facture"],"5 stats_CA ne renvoi pas la bonne année");
	}
	
	//@author  Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> 
	function testFacture_by_commande(){
		$facture_by_commande_pas_de_commande=$this->obj->facture_by_commande(ATF::commande()->decryptId($this->id_commande));
		$this->assertFalse($facture_by_commande_pas_de_commande,"0 Facture_by_commande renvoi un prix alors qu'il n'y en a pas");

		$facture1=$this->facture;
		$facture1["facture"]["id_commande"]=$this->id_commande;
		$this->obj->insert($facture1,$this->s);

		$facture2=$this->facture;
		$facture2["facture"]["id_commande"]=$this->id_commande;
		$facture2["facture"]["acompte_pourcent"]=25;
		$this->obj->insert($facture2,$this->s);
		$facture_by_commande=$this->obj->facture_by_commande(ATF::commande()->decryptId($this->id_commande));
		$facture_by_commande_acompte=$this->obj->facture_by_commande(ATF::commande()->decryptId($this->id_commande),true);

		$this->assertEquals("437.5",$facture_by_commande["prix"],"1 Facture_by_commande ne renvoi pas le bon prix");

		$this->assertEquals("87.5",$facture_by_commande_acompte["prix"],"3 Facture_by_commande avec acompte ne renvoi pas le bon prix");
	}
	//@author  Quentin JANON <qjanon@absystech.fr> 
	function testTotal_by_commande(){
		$total=$this->obj->total_by_commande(ATF::commande()->decryptId($this->id_commande));
		$this->assertEquals("0.00",$total,"1 Total_by_commande ne renvoi pas le bon prix");

		$facture1=$this->facture;
		$facture1["facture"]["id_commande"]=$this->id_commande;
		$this->obj->insert($facture1,$this->s);

		$facture2=$this->facture;
		$facture2["facture"]["id_commande"]=$this->id_commande;
		$facture2["facture"]["acompte_pourcent"]=25;
		$this->obj->insert($facture2,$this->s);
		$total=$this->obj->total_by_commande(ATF::commande()->decryptId($this->id_commande));

		$this->assertEquals("437.5",$total,"2 Total_by_commande ne renvoi pas le bon prix");
	}
		
	//@author  Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> 
	function testCan_delete(){
		try{
			$this->obj->can_delete($this->id_facture);
		}catch(errorATF $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(893,$error,"2 can_delete permet de supprimer une facture qui n'est pas la derniere");
		
		$this->obj->u(array("id_facture"=>$this->id_facture,"etat"=>"payee"));

		try {
			$this->obj->can_delete($this->id_facture);
		} catch (errorATF $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(892,$error,"2 can_delete permet de supprimer une facture payee");
		
		
	}
		
	//@author  Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>, Nicolas BERTEMONT <nbertemont@absystech.fr> 
	function testGetTotalImpayees(){
		$getTotalImpayees1=$this->obj->getTotalImpayees(date("Y",time())-1);
		$this->assertTrue($getTotalImpayees1>0,"1 getTotalImpayees ne renvoie rien");

		$getTotalImpayees2=$this->obj->getTotalImpayees(date("Y",time())-1,true);
		$this->assertTrue($getTotalImpayees2>0,"2 getTotalImpayees (avant) ne renvoie rien");

		$c_facture_att=new facture_att();
		
		$getTotalImpayees1=$c_facture_att->getTotalImpayees(date("Y",time())-1);
		$this->assertTrue($getTotalImpayees1>0,"att / 1 getTotalImpayees ne renvoie rien");

		$getTotalImpayees2=$c_facture_att->getTotalImpayees(date("Y",time())-1,true);
		$this->assertTrue($getTotalImpayees2>0,"att / 2 getTotalImpayees (avant) ne renvoie rien");

	}
	
	//@author Yann GAUTHERON <ygautheron@absystech.fr>
	function testwebservice(){		
		$this->assertNull($this->obj->webservice(array("action"=>"generate"),$session));
		$this->assertTrue($this->obj->webservice(array("action"=>"generate","id"=>$this->id_facture),$session));
		$this->assertEquals(1,count(ATF::$msg->getNotices()),"Nombre de notices incorrect");
		$this->assertEquals(1,count(ATF::$msg->getWarnings()),"Nombre de warnings incorrect");
	}

	//@author  Quentin JANON <qjanon@absystech.fr> 
	function testGeneratePDF(){
		$facture=$this->obj->select($this->id_facture);
		$facture['id'] = $facture['id_facture'];
		$pathAttendu = $this->obj->filepath($facture["id_facture"],"fichier_joint",true);
		util::rm($pathAttendu);
		
		$this->obj->generatePDF($facture,$this->s);

		$this->assertFileExists($pathAttendu,"Le fichier ne s'est pas généré");
		
		$notice = array(
			array(
				"msg"=>ATF::$usr->trans("facture_regenere_avec_succes",$this->obj->table),
				"title"=>"",
				"timer"=>""
			)
		);
		$this->assertEquals($notice,ATF::$msg->getNotices(),"La notice ne se fait pas");
		
		$warning = array(
			array(
				"msg"=>ATF::$usr->trans("email_sauvegarde_old_facture_doesnt_exist",$this->obj->table),
				"title"=>"",
				"timer"=>""
			)
		);
		$this->assertEquals($warning,ATF::$msg->getWarnings(),"La warning ne se fait pas");
	}
		
	//@author  Quentin JANON <qjanon@absystech.fr> 
	function testGeneratePDF2(){
		$facture=$this->obj->select($this->id_facture);
		$facture['id'] = $facture['id_facture'];
		$pathAttendu = $this->obj->filepath($facture["id_facture"],"fichier_joint",true);
		util::rm($pathAttendu);
		
		$this->obj->generatePDF($facture,$this->s,true);
		//Flush des notices
		ATF::$msg->getNotices();
		ATF::$msg->getWarnings();
		$this->obj->generatePDF($facture,$this->s,true);

		$this->assertFileExists($pathAttendu,"Le fichier ne s'est pas généré");
		
		$nReturned = ATF::$msg->getNotices();
		
		$notice = array(
			"msg"=>ATF::$usr->trans("facture_regenere_avec_succes",$this->obj->table),
			"title"=>"",
			"timer"=>""
		);
		$this->assertEquals($notice,$nReturned[1],"La notice ne se fait pas");
		
		$notice = array(
			"msg"=>ATF::$usr->trans("email_sauvegarde_old_facture_send",$this->obj->table),
			"title"=>"",
			"timer"=>""
		);
		$this->assertEquals($notice,$nReturned[0],"La notice 2 ne se fait pas");
	}
	
	//@author  Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> 
	function testGetTVA(){
		$this->assertEquals(1,$this->obj->getTVA(),"getTVA sans paramètre devrait retourner 1");
		$this->assertEquals(1.2,$this->obj->getTVA($this->id_societe),"getTVA  devrait retourner 1.2");
	}
	
	//@author  Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> 
	function testDefault_valueDate(){
		$date=$this->obj->default_value("date");
		$this->assertEquals(date("Y-m-d"),$date,"default_value ne renvoie pas la bonne date");
	}
	
	//@author  Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  
	function testDefault_value(){

		$mode=$this->obj->default_value("mode");
		$this->assertEquals("facture",$mode,"default_value ne renvoie pas le bon 'mode'");

		$acompte_pourcent=$this->obj->default_value("acompte_pourcent");
		$this->assertEquals(100,$acompte_pourcent,"default_value ne renvoie pas le bon 'acompte_pourcent'");

		$emailCopie=$this->obj->default_value("emailCopie");
		$this->assertEquals("debug@absystech.fr",$emailCopie,"default_value ne renvoie pas le bon 'emailCopie'");

		$email=$this->obj->default_value("email");
		$this->assertFalse($email,"default_value ne renvoie pas le bon 'email' 1");

		ATF::_r('id_societe',$this->id_societe);

		$id_societe=$this->obj->default_value("id_societe");
		$this->assertEquals($this->id_societe,$id_societe,"default_value ne renvoie pas le bon 'id_societe'");

		$tva=$this->obj->default_value("tva");
		$this->assertEquals(1.2,$tva,"default_value ne renvoie pas le bon 'tva'");

		$default_value=$this->obj->default_value("default_value");
		$this->assertNull($default_value,"default_value ne renvoie pas une bonne valeur pour le field default_value");


		ATF::_r('id_affaire',$this->id_affaire);

		$email=$this->obj->default_value("email");
		$this->assertFalse($email,"default_value ne renvoie pas le bon 'email' 2");
		
		ATF::societe()->update(array("id_societe"=>ATF::societe()->decryptId($this->id_societe),"id_contact_facturation"=>ATF::contact()->decryptId($this->id_contact)));

		ATF::contact()->update(array("id_contact"=>ATF::contact()->decryptId($this->id_contact),"email"=>"tu@absystech.net"));

		$email=$this->obj->default_value("email");
		$this->assertEquals("tu@absystech.net",$email,"default_value ne renvoie pas le bon 'email' 3");

		$id_societe=$this->obj->default_value("id_societe");
		$this->assertEquals($this->id_societe,$id_societe,"default_value ne renvoie pas le bon 'id_societe'");

		$tva=$this->obj->default_value("tva");
		$this->assertEquals(1.2,$tva,"default_value ne renvoie pas le bon 'tva'");

		$default_value=$this->obj->default_value("default_value");
		$this->assertNull($default_value,"default_value ne renvoie pas une bonne valeur pour le field default_value");

		ATF::_r('id_commande',$this->id_commande);

		$prix=$this->obj->default_value("prix");
		$this->assertEquals(200,$prix,"default_value ne renvoie pas le bon 'prix' (commande)");

		$frais_de_port=$this->obj->default_value("frais_de_port");
		$this->assertEquals($this->commande["commande"]["frais_de_port"],$frais_de_port,"default_value ne renvoie pas le bon 'frais_de_port' (commande)");
		$prix_achat=$this->obj->default_value("prix_achat");
		$this->assertEquals($this->commande["commande"]["prix_achat"],$prix_achat,"default_value ne renvoie pas le bon 'prix_achat' (commande)");

		$sous_total=$this->obj->default_value("sous_total");
		$this->assertEquals((200-$this->commande["commande"]["frais_de_port"]),$sous_total,"default_value ne renvoie pas le bon 'sous_total' (commande)");

		$marge=$this->obj->default_value("marge");
		$this->assertEquals((round(((200-$this->commande["commande"]["prix_achat"])/200)*100,2)."%"),$marge,"default_value ne renvoie pas le bon 'marge' (commande)");

		$marge_absolue=$this->obj->default_value("marge_absolue");
		$this->assertEquals((200-$this->commande["commande"]["frais_de_port"]-$this->commande["commande"]["prix_achat"]),$marge_absolue,"default_value ne renvoie pas le bon 'marge_absolue' (commande)");

		$id_affaire=$this->obj->default_value("id_affaire");
		$this->assertEquals($this->commande["commande"]["id_affaire"],$id_affaire,"default_value ne renvoie pas le bon 'id_affaire' (commande)");


		ATF::_r('id_facture',$this->id_facture);

		$prix=$this->obj->default_value("prix");
		$this->assertEquals(350,$prix,"default_value ne renvoie pas le bon 'prix' (facture)");

		$frais_de_port=$this->obj->default_value("frais_de_port");
		$this->assertEquals($this->facture["facture"]["frais_de_port"],$frais_de_port,"default_value ne renvoie pas le bon 'frais_de_port' (facture)");

		ATF::facture_ligne()->q->reset()
							->addCondition("id_facture",ATF::facture_ligne()->decryptId($this->id_facture))
							->addField("SUM(`prix_achat` * `quantite`)","prix_achat")
							->setStrict()
							->setDimension('cell');

		$prix_achat=ATF::facture_ligne()->select_all();

		$prix_achat_find=$this->obj->default_value("prix_achat");
		$this->assertEquals($prix_achat,$prix_achat_find,"default_value ne renvoie pas le bon 'prix_achat' (facture)");

		$sous_total=$this->obj->default_value("sous_total");
		$this->assertEquals((350-$this->facture["facture"]["frais_de_port"]),$sous_total,"default_value ne renvoie pas le bon 'sous_total' (facture)");

		$marge=$this->obj->default_value("marge");
		$this->assertEquals((round(((350-$prix_achat)/350)*100,2)."%"),$marge,"default_value ne renvoie pas le bon 'marge' (facture)");

		$email=$this->obj->default_value("email");
		$this->assertEquals("tu@absystech.net",$email,"default_value ne renvoie pas le bon 'email' (facture)");

		$id_affaire=$this->obj->default_value("id_affaire");
		$this->assertEquals($this->facture["facture"]["id_affaire"],$id_affaire,"default_value ne renvoie pas le bon 'id_affaire' (facture)");
		
		
		ATF::societe()->u(array("id_societe" => $id_societe , "id_termes" => 10));
		$affaire = ATF::affaire()->i(array("etat"=> "facture", "date"=>date("Y-m-d"), "id_societe" => $id_societe, "affaire"=> "Tu Affaire" , "id_termes" => 8));
		
		ATF::_r('id_affaire',$affaire);
		$id_termes=$this->obj->default_value("id_termes");
		$this->assertEquals(8,$id_termes,"default_value ne renvoie pas le bon 'id_termes'");
		
		ATF::_r('id_affaire',false);
		ATF::_r('id_societe',$id_societe);
		$id_termes=$this->obj->default_value("id_termes");
		$this->assertEquals(10,$id_termes,"default_value ne renvoie pas le bon 'id_termes'");
		
		ATF::_r('id_societe',false);
		$id_termes=$this->obj->default_value("id_termes");
		$this->assertEquals("",$id_termes,"default_value ne renvoie pas le bon 'id_termes'");
	}

	//@author  Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> 
	function testSelect(){
		$this->facture["facture"]["date_previsionnelle"]="2010-01-01";
		$this->facture["facture"]["date_effective"]="2010-01-01";
		$this->facture["facture"]["date_relance"]="2010-01-01";
		$id_facture=$facture=$this->obj->insert($this->facture,$this->s);


		$facture=$this->obj->select($id_facture);
		$this->assertNotEquals("",$facture["date"],"select ne prend pas la date");
		$this->assertNotEquals("",$facture["date_previsionnelle"],"select ne prend pas la date_previsionnelle");
		$this->assertNotEquals("",$facture["date_effective"],"select ne prend pas la date_effective");
		$this->assertNotEquals("",$facture["date_relance"],"select ne prend pas la date_relance");

		ATF::_r('event',"cloner");
		$facture_cloner=$this->obj->select($this->id_facture);
		$this->assertEquals("",$facture_cloner["date"],"select cloner prend la date");
		$this->assertEquals("",$facture_cloner["date_previsionnelle"],"select cloner prend la date_previsionnelle");
		$this->assertEquals("",$facture_cloner["date_effective"],"select cloner prend la date_effective");
		$this->assertEquals("",$facture_cloner["date_relance"],"select cloner prend la date_relance");
	}
	
	// @author Nicolas BERTEMONT <nbertemont@absystech.fr> 
	public function test_saFilter(){
		$this->obj->q->reset();
		ATF::$usr->set("id_profil",11);	
		$this->obj->select_all();
		$this->assertEquals(array("affaire.id_commercial"=>"affaire.id_commercial = '".ATF::$usr->getID()."'"),$this->obj->q->getWhere(),"Filtrage general sur facture invailde");
	}


	//@author  Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> 
	public function testRapprochementFacture(){
		$id_facture = ATF::facture()->i(
			array(
				"id_affaire"=>$this->id_affaire
				,"id_societe"=>$this->id_societe
				,"tva"=>"1"
				,"prix"=>"350"
				,"id_user"=>ATF::$usr->getID()
				,"date"=>"2050-08-01 09:00:00"
				,"type_facture"=>"facture"
				,"ref"=>"FSO10010002"
				,"date_previsionnelle"=>"2050-08-31 09:00:00"
			)	
		);

		$societe1 = array(
			"societe"=>"societe TEST TU 1",
			"id_filiale"=>$this->id_societe
		);
		$id_societe1 = ATF::societe()->decryptId(ATF::societe()->i($societe1));
		$id_facture1 = ATF::facture()->i(
			array(
				"id_affaire"=>$this->id_affaire
				,"id_societe"=>$id_societe1
				,"tva"=>"1"
				,"prix"=>"350"
				,"id_user"=>ATF::$usr->getID()
				,"date"=>"2050-08-01 09:00:00"
				,"type_facture"=>"facture"
				,"ref"=>"FSO10010003"
				,"date_previsionnelle"=>"2050-08-31 09:00:00"
			)	
		);

		$societe2 = array(
			"societe"=>"societe TEST TU 2",
			"id_filiale"=>$id_societe1
		);
		$id_societe2 = ATF::societe()->i($societe2);
		$id_facture2 = ATF::facture()->i(
			array(
				"id_affaire"=>$this->id_affaire
				,"id_societe"=>$id_societe2
				,"tva"=>"1"
				,"prix"=>"350"
				,"id_user"=>ATF::$usr->getID()
				,"date"=>"2050-08-01 09:00:00"
				,"type_facture"=>"facture"
				,"ref"=>"FSO10010004"
				,"date_previsionnelle"=>"2050-08-31 09:00:00"
			)	
		);

		$q=$this->obj->rapprochementFacture($this->id_societe,351);	
		$this->obj->q=$q;
		$factureRapprochementFacture=$this->obj->sa();

		$this->assertEquals($this->obj->select($this->id_facture),$factureRapprochementFacture[0],"factureRapprochementFacture 1 ne retourne pas les bonnes factures + 1");
		$this->assertEquals($this->obj->select($id_facture),$factureRapprochementFacture[1],"factureRapprochementFacture 2 ne retourne pas les bonnes factures + 1");
		$this->assertEquals($this->obj->select($id_facture1),$factureRapprochementFacture[2],"factureRapprochementFacture 3 ne retourne pas les bonnes factures + 1");
		$this->assertEquals($this->obj->select($id_facture2),$factureRapprochementFacture[3],"factureRapprochementFacture 4 ne retourne pas les bonnes factures + 1");

		$q=$this->obj->rapprochementFacture($this->id_societe,349);	
		$this->obj->q=$q;
		$factureRapprochementFacture1=$this->obj->sa();
		$this->assertEquals($factureRapprochementFacture,$factureRapprochementFacture1,"factureRapprochementFacture1 ne retourne pas les bonnes factures - 1");

		$q=$this->obj->rapprochementFacture($id_societe1,349);	
		$this->obj->q=$q;
		$factureRapprochementFacture2=$this->obj->sa();
		$this->assertEquals($factureRapprochementFacture,$factureRapprochementFacture2,"factureRapprochementFacture2 ne retourne pas les bonnes factures - 1");

		$q=$this->obj->rapprochementFacture($id_societe2,349);	
		$this->obj->q=$q;
		$factureRapprochementFacture3=$this->obj->sa();
		$this->assertEquals($factureRapprochementFacture,$factureRapprochementFacture3,"factureRapprochementFacture3 ne retourne pas les bonnes factures - 1");

	}
	
	//@author  Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> 
	public function testRapprochementFactureSA(){
		$id_facture1 = ATF::facture()->i(
			array(
				"id_affaire"=>$this->id_affaire
				,"id_societe"=>$this->id_societe
				,"tva"=>"1"
				,"prix"=>"100"
				,"id_user"=>ATF::$usr->getID()
				,"date"=>"2050-08-01 09:00:00"
				,"type_facture"=>"facture"
				,"ref"=>"FSO10010002"
				,"date_previsionnelle"=>"2050-08-31 09:00:00"
			)	
		);

		$id_facture2 = ATF::facture()->i(
			array(
				"id_affaire"=>$this->id_affaire
				,"id_societe"=>$this->id_societe
				,"tva"=>"1"
				,"prix"=>"200"
				,"id_user"=>ATF::$usr->getID()
				,"date"=>"2050-08-01 09:00:00"
				,"type_facture"=>"facture"
				,"ref"=>"FSO10010003"
				,"date_previsionnelle"=>"2050-08-31 09:00:00"
			)	
		);

		$id_facture3 = ATF::facture()->i(
			array(
				"id_affaire"=>$this->id_affaire
				,"id_societe"=>$this->id_societe
				,"tva"=>"1"
				,"prix"=>"150"
				,"id_user"=>ATF::$usr->getID()
				,"date"=>"2050-08-01 09:00:00"
				,"type_facture"=>"facture"
				,"ref"=>"FSO10010004"
				,"date_previsionnelle"=>"2050-08-31 09:00:00"
			)	
		);

		$id_facture4 = ATF::facture()->i(
			array(
				"id_affaire"=>$this->id_affaire
				,"id_societe"=>$this->id_societe
				,"tva"=>"1"
				,"prix"=>"150"
				,"id_user"=>ATF::$usr->getID()
				,"date"=>"2050-08-01 09:00:00"
				,"type_facture"=>"facture"
				,"ref"=>"FSO10010005"
				,"date_previsionnelle"=>"2050-08-31 09:00:00"
			)	
		);

		
		$q=$this->obj->rapprochementFacture($this->id_societe,301);	
		$this->obj->q=$q;
		$factureRapprochementFacture=$this->obj->rapprochementFactureSA();

		$this->assertEquals(array("ref"=>"FSO10010004 FSO10010005","prix"=>"150.00 + 150.00 = 300","facture.id_facture"=>$this->obj->decryptId($id_facture3).$this->obj->decryptId($id_facture4)),$factureRapprochementFacture["data"][1],"factureRapprochementFacture ne retourne pas les bonnes factures 3");
		$this->assertEquals(array("ref"=>"FSO10010002 FSO10010003","prix"=>"100.00 + 200.00 = 300","facture.id_facture"=>$this->obj->decryptId($id_facture1).$this->obj->decryptId($id_facture2)),$factureRapprochementFacture["data"][0],"factureRapprochementFacture ne retourne pas les bonnes factures 2");
	}

	//@author Yann GAUTHERON <ygautheron@absystech.fr> 
	public function testincremanteFacture(){
		$this->assertFalse($this->obj->incremanteFacture());
	}

	public function test_lettre_change(){
		$infos = array("facture" => $this->id_facture.",2");
		try {
			$this->obj->lettre_change($infos);
		} catch (errorATF $e) {
			$erreur_trouvee1 = $e->getMessage();
		}
		$this->assertEquals($erreur_trouvee1,"Il faut inserer un RIB pour la societe", "Erreur 1 non trouvée");
		ATF::societe()->u(array("id_societe" => $this->id_societe , "rib" => "30003011100002068845277"));
		
		try {
			$this->obj->lettre_change($infos);
		} catch (errorATF $e) {
			$erreur_trouvee1 = $e->getMessage();
		}
		$this->assertEquals($erreur_trouvee1,"Il faut inserer une banque pour la societe", "Erreur 2 non trouvée");
		ATF::societe()->u(array("id_societe" => $this->id_societe , "banque" => "societe generale lille nationale"));
		
		$this->assertEquals(true, $this->obj->lettre_change($infos) , "Lettre change erreur 3");
		
		
		$this->assertEquals(false,$this->obj->lettre_change(array("facture" => "")), "Erreur 4 non trouvée");
		
		
		try {
			$this->obj->lettre_change(array("facture" => "2"));
		} catch (errorATF $e) {
			$erreur_trouvee1 = $e->getMessage();
		}
		$this->assertEquals($erreur_trouvee1,"Toutes les factures sont déja payées", "Erreur 5 non trouvée");
		
		
		
	}

	
	public function test_autocompleteFactureAvoirDispo() {

		//Facture
		$facture["facture"]=$this->commande["commande"];
		$facture["facture"]["date"]="2010-01-02";
		$facture["facture"]["id_affaire"]=$this->id_affaire;
		$facture["facture"]["mode"]="avoir"; 
		$facture["facture"]["id_termes"]=2;
		$facture["facture"]["id_facture_parente"]=$this->id_facture;
		$facture["facture"]["tva"]=1.2;
		
		//Facture_ligne
		$facture["values_facture"]=array("produits"=>'[{
			"facture_ligne__dot__ref":"TU",
			"facture_ligne__dot__produit":"Tu_facture",
			"facture_ligne__dot__quantite":"15",
			"facture_ligne__dot__prix":"20",
			"facture_ligne__dot__prix_achat":"10",
			"facture_ligne__dot__id_fournisseur":"1",
			"facture_ligne__dot__serial":"777",
			"facture_ligne__dot__id_compte_absystech":"1",
			"facture_ligne__dot__marge":97.14,
			"facture_ligne__dot__id_fournisseur_fk":"1"
		}]');

		//Insertion
		unset($facture["facture"]["resume"],$facture["facture"]["prix_achat"],$facture["facture"]["id_devis"]);
		$id_avoir = $this->obj->insert($facture,$this->s);

		$params = array('id_facture'=>$this->id_facture);
		$r = $this->obj->autocompleteFactureAvoirDispo($params);

		$this->assertGreaterThan(1,count($r),"Il y a moins d'un résultat !");
		$this->assertEquals($id_avoir,$r[0][0],"Ce n'est pas la bonne facture qui remonte. !");

	}

};
?>