<?
/**
* Classe de test sur le module societe_cleodis
*/
class facturation_test extends ATF_PHPUnit_Framework_TestCase {
	/** Méthode pré-test, exécute avant chaque test unitaire
	* besoin d'un user pour les traduction
	*/
	public function setUp() {
		$this->initUser();
	}

	/** Méthode post-test, exécute après chaque test unitaire*/
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}


  	// @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	// @author Yann GAUTHERON <ygautheron@absystech.fr>
	function testUpdate_facturations2(){
		$id_affaire=ATF::affaire()->decryptId(ATF::affaire()->i(array("etat"=>"commande","date"=>date("Y-m-d"),"ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu","nature"=>"affaire")));
		$id_loyer1=ATF::loyer()->decryptId(ATF::loyer()->i(array("id_affaire"=>$id_affaire,"loyer"=>200,"duree"=>3,"assurance"=>20,"frais_de_gestion"=>2,"frequence_loyer"=>"mois")));
		$id_loyer2=ATF::loyer()->decryptId(ATF::loyer()->i(array("id_affaire"=>$id_affaire,"loyer"=>100,"duree"=>1,"assurance"=>10,"frais_de_gestion"=>1,"frequence_loyer"=>"trimestre")));
		$id_loyer3=ATF::loyer()->decryptId(ATF::loyer()->i(array("id_affaire"=>$id_affaire,"loyer"=>50,"duree"=>1,"assurance"=>5,"frais_de_gestion"=>0,"frequence_loyer"=>"an")));

		$id_devis=ATF::devis()->decryptId(ATF::devis()->i(array("ref"=>"refTu","id_user"=>$this->id_user,"id_societe"=>$this->id_societe,"id_filiale"=>246,"prix"=>600,"date"=>date("Y-m-d"),"devis"=>"AffaireTu","type_contrat"=>"lld","date_accord"=>date("Y-m-d"),"etat"=>"gagne","id_contact"=>$this->id_contact,"id_affaire"=>$id_affaire,"tva"=>"1.196","loyer_unique"=>"non","prix_achat"=>0,"validite"=>date("Y-m-d"))));
		$id_devis_ligne=ATF::devis_ligne()->i(array("type"=>"portable","id_devis"=>$id_devis,"id_produit"=>6,"ref"=>"GPAC-GPS","produit"=>"Ordinateur TOM TOM GO500","quantite"=>2,"id_fournisseur"=>5385,"prix_achat"=>"0","visible"=>"oui","visibilite_prix"=>"visible"));
		$id_commande=ATF::commande()->decryptId(ATF::commande()->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"commande"=>"AffaireTu","prix_achat"=>0,"prix"=>600,"date"=>date("Y-m-d"),"id_devis"=>$id_devis,"etat"=>"mis_loyer","id_user"=>$this->id_user,"tva"=>"1.196","clause_logicielle"=>"non","date_debut"=>date("2010-01-01"),"date_evolution"=>date("2010-12-31"),"type"=>"prelevement","id_affaire"=>$id_affaire)));
		$id_commande_ligne1=ATF::commande_ligne()->i(array("id_commande"=>$id_commande,"id_produit"=>6,"ref"=>"GPAC-GPS","produit"=>"Ordinateur TOM TOM GO500","quantite"=>2,"id_fournisseur"=>5385,"prix_achat"=>"0","visible"=>"oui"));
		$id_commande_ligne2=ATF::commande_ligne()->i(array("id_commande"=>$id_commande,"id_produit"=>6,"ref"=>"GPAC-GPS1","produit"=>"Ordinateur TOM TOM GO5001","quantite"=>2,"id_fournisseur"=>5385,"prix_achat"=>"0","visible"=>"oui"));

		$id_affaire_parente=ATF::affaire()->decryptId(ATF::affaire()->i(array("etat"=>"commande","date"=>date("Y-m-d"),"ref"=>"refTuParenre","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu","nature"=>"affaire","id_fille"=>$id_affaire)));
		$id_loyer1=ATF::loyer()->decryptId(ATF::loyer()->i(array("id_affaire"=>$id_affaire_parente,"loyer"=>200,"duree"=>3,"assurance"=>20,"frais_de_gestion"=>2,"frequence_loyer"=>"mois")));
		$id_loyer2=ATF::loyer()->decryptId(ATF::loyer()->i(array("id_affaire"=>$id_affaire_parente,"loyer"=>100,"duree"=>1,"assurance"=>10,"frais_de_gestion"=>1,"frequence_loyer"=>"trimestre")));
		$id_loyer3=ATF::loyer()->decryptId(ATF::loyer()->i(array("id_affaire"=>$id_affaire_parente,"loyer"=>50,"duree"=>1,"assurance"=>5,"frais_de_gestion"=>0,"frequence_loyer"=>"an")));

		$id_devis_parent=ATF::devis()->decryptId(ATF::devis()->i(array("ref"=>"refTuParenre","id_user"=>$this->id_user,"id_societe"=>$this->id_societe,"id_filiale"=>246,"prix"=>600,"date"=>date("Y-m-d"),"devis"=>"AffaireTu","type_contrat"=>"lld","date_accord"=>date("Y-m-d"),"etat"=>"gagne","id_contact"=>$this->id_contact,"id_affaire"=>$id_affaire_parente,"tva"=>"1.196","loyer_unique"=>"non","prix_achat"=>0,"validite"=>date("Y-m-d"))));
		$id_devis_parent_ligne=ATF::devis_ligne()->i(array("type"=>"portable","id_devis"=>$id_devis_parent,"id_produit"=>6,"ref"=>"GPAC-GPS","produit"=>"Ordinateur TOM TOM GO500","quantite"=>2,"id_fournisseur"=>5385,"prix_achat"=>"0","visible"=>"oui","visibilite_prix"=>"visible"));
		$id_commande_parent=ATF::commande()->decryptId(ATF::commande()->i(array("ref"=>"refTuParenre","id_societe"=>$this->id_societe,"commande"=>"AffaireTu","prix_achat"=>0,"prix"=>600,"date"=>date("Y-m-d"),"id_devis"=>$id_devis_parent,"etat"=>"mis_loyer","id_user"=>$this->id_user,"tva"=>"1.196","clause_logicielle"=>"non","date_debut"=>NULL,"date_evolution"=>date("2009-12-31"),"type"=>"prelevement","id_affaire"=>$id_affaire_parente)));
		$id_commande_parent_ligne1=ATF::commande_ligne()->i(array("id_commande"=>$id_commande_parent,"id_produit"=>6,"ref"=>"GPAC-GPS","produit"=>"Ordinateur TOM TOM GO500","quantite"=>2,"id_fournisseur"=>5385,"prix_achat"=>"0","visible"=>"oui"));
		$id_commande_parent_ligne2=ATF::commande_ligne()->i(array("id_commande"=>$id_commande_parent,"id_produit"=>6,"ref"=>"GPAC-GPS1","produit"=>"Ordinateur TOM TOM GO5001","quantite"=>2,"id_fournisseur"=>5385,"prix_achat"=>"0","visible"=>"oui"));

		$id_facturation=$this->obj->i(array("id_affaire"=>$id_affaire_parente,"id_societe"=>$this->id_societe,"id_facture"=>NULL,"montant"=>100,"date_periode_debut"=>"2009-04-01","date_periode_fin"=>"2009-06-30","envoye"=>"oui"));

		ATF::affaire()->decryptId(ATF::affaire()->u(array("id_affaire"=>$id_affaire,"nature"=>"AR")));

		$affaire=new affaire_cleodis($id_affaire);
		$affaire_parent=new affaire_cleodis($id_affaire_parente);
		$this->obj->update_facturations($affaire_parent,$affaire);
		$this->assertFalse($this->obj->update_facturations($affaire_parent,$affaire),'update_facturations doit renvoyer false car la commande parent n a pas de date debut');


		ATF::commande()->u(array("id_commande"=>$id_commande_parent,"date_debut"=>"2009-01-01"));
		$id_facture_check_avoir = ATF::facture()->i(array(
			"id_affaire"=>$id_affaire_parente
			,"date"=>"2009-01-01"
			,"id_societe"=>ATF::affaire()->select($id_affaire_parente,"id_societe")
			,"ref"=>"TU----"
			,"tva"=>1.196
			,"date_periode_debut"=>"2009-01-01"
			,"date_periode_fin"=>"2010-04-01"
			,"type_facture"=>"facture"
		));
		try {
			$this->obj->update_facturations($affaire_parent,$affaire);
		} catch (errorATF $e) {
			$errno = $e->getCode();
		}
		$this->assertEquals(878,$errno,"Erreur non retrouvée");

		$id_facture_avoir = ATF::facture()->i(array(
			"id_affaire"=>$id_affaire_parente
			,"date"=>"2009-01-01"
			,"id_societe"=>ATF::affaire()->select($id_affaire_parente,"id_societe")
			,"ref"=>"TU----AV"
			,"tva"=>1.196
			,"prix"=>-666
			,"date_periode_debut"=>"2009-01-01"
			,"date_periode_fin"=>"2010-04-01"
			,"type_facture"=>"libre"
		));
		$this->assertTrue($this->obj->update_facturations($affaire_parent,$affaire),'update_facturations doit renvoyer true car la commande parent a une date debut 2');

		$infos_facturation = array(
			"id_affaire"=>$id_affaire_parente
			,"id_societe"=>$this->id_societe
			,"id_facture"=>$id_facture_check_avoir
			,"montant"=>100
			,"date_periode_debut"=>"2009-01-01"
			,"date_periode_fin"=>"2009-01-31"
			,"envoye"=>"oui"
		);
		$id_facturation_mois_dernier=$this->obj->i($infos_facturation);
		$commande = $affaire->getCommande();
		$commande->set("date_debut","2008-01-01");
		$this->assertTrue($this->obj->update_facturations($affaire_parent,$affaire),'update_facturations doit renvoyer true car la commande parent a une date debut 3');

		// Avec facture associee a facturation
		ATF::facture()->d($id_facture_avoir);
		try {
			$this->obj->update_facturations($affaire_parent,$affaire);
		} catch (errorATF $e) {
			$errno = $e->getCode();
		}
		$this->assertEquals(879,$errno,"Erreur non retrouvée");
	}

	// @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	function testUpdate_facturations(){
		$id_affaire=ATF::affaire()->decryptId(ATF::affaire()->i(array("etat"=>"commande","date"=>date("Y-m-d"),"ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu","nature"=>"affaire")));
		$id_loyer1=ATF::loyer()->decryptId(ATF::loyer()->i(array("id_affaire"=>$id_affaire,"loyer"=>200,"duree"=>3,"assurance"=>20,"frais_de_gestion"=>2,"frequence_loyer"=>"mois")));
		$id_loyer2=ATF::loyer()->decryptId(ATF::loyer()->i(array("id_affaire"=>$id_affaire,"loyer"=>100,"duree"=>1,"assurance"=>10,"frais_de_gestion"=>1,"frequence_loyer"=>"trimestre")));
		$id_loyer3=ATF::loyer()->decryptId(ATF::loyer()->i(array("id_affaire"=>$id_affaire,"loyer"=>50,"duree"=>1,"assurance"=>5,"frais_de_gestion"=>0,"frequence_loyer"=>"an")));

		$id_devis=ATF::devis()->decryptId(ATF::devis()->i(array("ref"=>"refTu","id_user"=>$this->id_user,"id_societe"=>$this->id_societe,"id_filiale"=>246,"prix"=>600,"date"=>date("Y-m-d"),"devis"=>"AffaireTu","type_contrat"=>"lld","date_accord"=>date("Y-m-d"),"etat"=>"gagne","id_contact"=>$this->id_contact,"id_affaire"=>$id_affaire,"tva"=>"1.196","loyer_unique"=>"non","prix_achat"=>0,"validite"=>date("Y-m-d"))));
		$id_devis_ligne=ATF::devis_ligne()->i(array("type"=>"portable","id_devis"=>$id_devis,"id_produit"=>6,"ref"=>"GPAC-GPS","produit"=>"Ordinateur TOM TOM GO500","quantite"=>2,"id_fournisseur"=>5385,"prix_achat"=>"0","visible"=>"oui","visibilite_prix"=>"visible"));
		$id_commande=ATF::commande()->decryptId(ATF::commande()->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"commande"=>"AffaireTu","prix_achat"=>0,"prix"=>600,"date"=>date("Y-m-d"),"id_devis"=>$id_devis,"etat"=>"mis_loyer","id_user"=>$this->id_user,"tva"=>"1.196","clause_logicielle"=>"non","date_debut"=>date("2010-01-01"),"date_evolution"=>date("2010-12-31"),"type"=>"prelevement","id_affaire"=>$id_affaire)));
		$id_commande_ligne1=ATF::commande_ligne()->i(array("id_commande"=>$id_commande,"id_produit"=>6,"ref"=>"GPAC-GPS","produit"=>"Ordinateur TOM TOM GO500","quantite"=>2,"id_fournisseur"=>5385,"prix_achat"=>"0","visible"=>"oui"));
		$id_commande_ligne2=ATF::commande_ligne()->i(array("id_commande"=>$id_commande,"id_produit"=>6,"ref"=>"GPAC-GPS1","produit"=>"Ordinateur TOM TOM GO5001","quantite"=>2,"id_fournisseur"=>5385,"prix_achat"=>"0","visible"=>"oui"));

		$id_affaire_parente=ATF::affaire()->decryptId(ATF::affaire()->i(array("etat"=>"commande","date"=>date("Y-m-d"),"ref"=>"refTuParenre","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu","nature"=>"affaire","id_fille"=>$id_affaire)));
		$id_loyer1=ATF::loyer()->decryptId(ATF::loyer()->i(array("id_affaire"=>$id_affaire_parente,"loyer"=>200,"duree"=>3,"assurance"=>20,"frais_de_gestion"=>2,"frequence_loyer"=>"mois")));
		$id_loyer2=ATF::loyer()->decryptId(ATF::loyer()->i(array("id_affaire"=>$id_affaire_parente,"loyer"=>100,"duree"=>1,"assurance"=>10,"frais_de_gestion"=>1,"frequence_loyer"=>"trimestre")));
		$id_loyer3=ATF::loyer()->decryptId(ATF::loyer()->i(array("id_affaire"=>$id_affaire_parente,"loyer"=>50,"duree"=>1,"assurance"=>5,"frais_de_gestion"=>0,"frequence_loyer"=>"an")));

		$id_devis_parent=ATF::devis()->decryptId(ATF::devis()->i(array("ref"=>"refTuParenre","id_user"=>$this->id_user,"id_societe"=>$this->id_societe,"id_filiale"=>246,"prix"=>600,"date"=>date("Y-m-d"),"devis"=>"AffaireTu","type_contrat"=>"lld","date_accord"=>date("Y-m-d"),"etat"=>"gagne","id_contact"=>$this->id_contact,"id_affaire"=>$id_affaire_parente,"tva"=>"1.196","loyer_unique"=>"non","prix_achat"=>0,"validite"=>date("Y-m-d"))));
		$id_devis_parent_ligne=ATF::devis_ligne()->i(array("type"=>"portable","id_devis"=>$id_devis_parent,"id_produit"=>6,"ref"=>"GPAC-GPS","produit"=>"Ordinateur TOM TOM GO500","quantite"=>2,"id_fournisseur"=>5385,"prix_achat"=>"0","visible"=>"oui","visibilite_prix"=>"visible"));
		$id_commande_parent=ATF::commande()->decryptId(ATF::commande()->i(array("ref"=>"refTuParenre","id_societe"=>$this->id_societe,"commande"=>"AffaireTu","prix_achat"=>0,"prix"=>600,"date"=>date("Y-m-d"),"id_devis"=>$id_devis_parent,"etat"=>"mis_loyer","id_user"=>$this->id_user,"tva"=>"1.196","clause_logicielle"=>"non","date_debut"=>NULL,"date_evolution"=>date("2009-12-31"),"type"=>"prelevement","id_affaire"=>$id_affaire_parente)));
		$id_commande_parent_ligne1=ATF::commande_ligne()->i(array("id_commande"=>$id_commande_parent,"id_produit"=>6,"ref"=>"GPAC-GPS","produit"=>"Ordinateur TOM TOM GO500","quantite"=>2,"id_fournisseur"=>5385,"prix_achat"=>"0","visible"=>"oui"));
		$id_commande_parent_ligne2=ATF::commande_ligne()->i(array("id_commande"=>$id_commande_parent,"id_produit"=>6,"ref"=>"GPAC-GPS1","produit"=>"Ordinateur TOM TOM GO5001","quantite"=>2,"id_fournisseur"=>5385,"prix_achat"=>"0","visible"=>"oui"));

		$id_facturation=$this->obj->i(array("id_affaire"=>$id_affaire_parente,"id_societe"=>$this->id_societe,"id_facture"=>NULL,"montant"=>100,"date_periode_debut"=>"2009-04-01","date_periode_fin"=>"2009-06-30","envoye"=>"oui"));

		ATF::affaire()->decryptId(ATF::affaire()->u(array("id_affaire"=>$id_affaire,"nature"=>"AR")));

		$affaire=new affaire_cleodis($id_affaire);
		$affaire_parent=new affaire_cleodis($id_affaire_parente);

		$this->obj->update_facturations($affaire_parent,$affaire);
		$this->assertFalse($this->obj->update_facturations($affaire_parent,$affaire),'update_facturations doit renvoyer false car la commande parent n a pas de date debut');

		ATF::commande()->u(array("id_commande"=>$id_commande_parent,"date_debut"=>date("2009-01-01")));
		$this->assertTrue($this->obj->update_facturations($affaire_parent,$affaire),'update_facturations doit renvoyer true car la commande parent a une date debut');

		$this->obj->q->reset()->addCondition("id_affaire",$id_affaire_parente)->addOrder("date_periode_debut");
		$facturations_parente=$this->obj->sa();
		$this->assertEquals(array(
								0=>array(
										"id_facturation"=>$facturations_parente[0]["id_facturation"],
										"id_affaire"=>$id_affaire_parente,
										"id_societe"=>$this->id_societe,
										"id_facture"=>NULL,
										"montant"=>"200.00",
										"frais_de_gestion"=>"2.00",
										"assurance"=>"20.00",
										"date_periode_debut"=>"2009-01-01",
										"type"=>"contrat",
										"envoye"=>"non",
										"date_periode_fin"=>"2009-01-31"
								),
								1=>array(
										"id_facturation"=>$facturations_parente[1]["id_facturation"],
										"id_affaire"=>$id_affaire_parente,
										"id_societe"=>$this->id_societe,
										"id_facture"=>NULL,
										"montant"=>"200.00",
										"frais_de_gestion"=>"2.00",
										"assurance"=>"20.00",
										"date_periode_debut"=>"2009-02-01",
										"type"=>"contrat",
										"envoye"=>"non",
										"date_periode_fin"=>"2009-02-28"
								),
								2=>array(
										"id_facturation"=>$facturations_parente[2]["id_facturation"],
										"id_affaire"=>$id_affaire_parente,
										"id_societe"=>$this->id_societe,
										"id_facture"=>NULL,
										"montant"=>"200.00",
										"frais_de_gestion"=>"2.00",
										"assurance"=>"20.00",
										"date_periode_debut"=>"2009-03-01",
										"type"=>"contrat",
										"envoye"=>"non",
										"date_periode_fin"=>"2009-03-31"
								),
								3=>array(
										"id_facturation"=>$facturations_parente[3]["id_facturation"],
										"id_affaire"=>$id_affaire_parente,
										"id_societe"=>$this->id_societe,
										"id_facture"=>NULL,
										"montant"=>"100.00",
										"frais_de_gestion"=>"0.00",
										"assurance"=>"0.00",
										"date_periode_debut"=>"2009-04-01",
										"type"=>"contrat",
										"envoye"=>"oui",
										"date_periode_fin"=>"2009-06-30"
								)
							)
							,$facturations_parente,'insert_facturation ne créé pas les bonnes facturations parent ');


		ATF::devis()->decryptId(ATF::devis()->u(array("id_devis"=>$id_devis_parent,"loyer_unique"=>"oui")));
		$this->assertTrue($this->obj->update_facturations($affaire_parent,$affaire),'update_facturations doit renvoyer true car la commande parent a une date debut');

		ATF::loyer()->u(array("id_loyer"=>$id_loyer1,"frequence_loyer"=>"trimestre"));
		$this->assertTrue($this->obj->update_facturations($affaire_parent,$affaire),'update_facturations doit renvoyer true car la commande parent a une date debut');

		ATF::loyer()->u(array("id_loyer"=>$id_loyer1,"frequence_loyer"=>"an"));
		$this->assertTrue($this->obj->update_facturations($affaire_parent,$affaire),'update_facturations doit renvoyer true car la commande parent a une date debut');

	}

	//@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	public function testDelete_special(){
		$id_affaire=ATF::affaire()->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu"));

		$id_facture = ATF::facture()->i(
			array(
				"id_affaire"=>$id_affaire
				,"id_societe"=>$this->id_societe
				,"tva"=>"1.196"
				,"id_user"=>$this->id_user
				,"date"=>"2010-01-01"
				,"type_facture"=>"facture"
				,"ref"=>"ref" //champs obligatoire
			)
		);

		$id_facturation=$this->obj->i(array("id_affaire"=>$id_affaire,"id_societe"=>$this->id_societe,"id_facture"=>$id_facture,"montant"=>100,"date_periode_debut"=>"2010-01-01","date_periode_fin"=>"2010-02-01","envoye"=>"oui"));
		try {
			$this->obj->delete_special($id_affaire,"contrat");
		} catch (errorATF $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(878,$error,'Impossible de supprimer une facturation envoyée');

		$this->obj->u(array("id_facturation"=>$id_facturation,"envoye"=>"non"));
		try {
			$this->obj->delete_special($id_affaire,"contrat");
		} catch (errorATF $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(879,$error,'Impossible de supprimer une facturation qui a une facture');

		$this->obj->u(array("id_facturation"=>$id_facturation,"id_facture"=>NULL));

//		$target = $this->obj->filepath($id_facturation,"facturation");
//		util::file_put_contents($target,"tuFacturation");

		ATF::db($this->db)->begin_transaction();
		$this->assertTrue($this->obj->delete_special($id_affaire,"contrat"),"delete_special devrait renvoyer true");
		ATF::db($this->db)->rollback_transaction();

		$this->assertNull($this->obj->select($id_facturation),'la facturation ne s est pas supprimée');

	}

	//@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	public function testPeriode_facturation(){

		$id_affaire=ATF::affaire()->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu"));
		$id_facturation=$this->obj->i(array("id_affaire"=>$id_affaire,"id_societe"=>$this->id_societe,"id_facture"=>$id_facture,"montant"=>100,"date_periode_debut"=>date("Y-m-d"),"date_periode_fin"=>date("Y-m-d",strtotime(date("Y-m-01")." + 1 month")),"envoye"=>"oui"));

		$this->assertEquals($this->obj->select($id_facturation),$this->obj->periode_facturation($id_affaire),'Periode_facturation ne renvoi pas la bonne facturation');

		$this->assertEquals($this->obj->select($id_facturation),$this->obj->periode_facturation($id_affaire,true),'Periode_facturation ne renvoi pas la bonne facturation');
	}

	//@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	public function testgetResteAPayer(){

		$id_affaire=ATF::affaire()->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu"));
		$id_facture = ATF::facture()->i(
			array(
				"id_affaire"=>$id_affaire
				,"id_societe"=>$this->id_societe
				,"tva"=>"1.196"
				,"id_user"=>$this->id_user
				,"date"=>"2010-01-01"
				,"type_facture"=>"facture"
				,"ref"=>"ref" //champs obligatoire
			)
		);
		$id_facturation=$this->obj->i(array(
											"id_affaire"=>$id_affaire,
											"id_societe"=>$this->id_societe,
											"id_facture"=>$id_facture,
											"montant"=>300,
											"date_periode_debut"=>date("Y-m-d"),
											"date_periode_fin"=>date("Y-m-d",strtotime(date("Y-m-01")." + 1 month - 1 day")),
											"envoye"=>"oui"
											)
										);

		$id_facturation=$this->obj->i(array(
											"id_affaire"=>$id_affaire,
											"id_societe"=>$this->id_societe,
											"id_facture"=>NULL,
											"montant"=>25,
											"date_periode_debut"=>date("Y-m-d",strtotime(date("Y-m-01")." + 1 month")),
											"date_periode_fin"=>date("Y-m-d",strtotime(date("Y-m-01")." + 2 month - 1 day")),
											"envoye"=>"non"
											)
										);


		$id_facturation=$this->obj->i(array(
											"id_affaire"=>$id_affaire,
											"id_societe"=>$this->id_societe,
											"id_facture"=>NULL,
											"montant"=>25,
											"date_periode_debut"=>date("Y-m-d",strtotime(date("Y-m-01")." + 2 month")),
											"date_periode_fin"=>date("Y-m-d",strtotime(date("Y-m-01")." + 3 month - 1 day")),
											"envoye"=>"non"
											)
										);
		$this->assertEquals(50,$this->obj->getResteAPayer($id_affaire),'getResteAPayer ne renvoi pas le bon montant');
	}


	//@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	public function testMontant_total(){

		$id_affaire=ATF::affaire()->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu"));
		$commande=array(
						"ref"=>"affaire",
						"id_societe"=>$this->id_societe,
						"id_user"=>$this->id_user,
						"tva"=>"1.196",
						"id_affaire"=>$id_affaire
						);
		$id_commande=ATF::commande()->i($commande);

		$id_facturation1=$this->obj->i(array("id_affaire"=>$id_affaire,"id_societe"=>$this->id_societe,"id_facture"=>$id_facture,"montant"=>100,"frais_de_gestion"=>10,"assurance"=>1,"date_periode_debut"=>date("Y-m-d"),"date_periode_fin"=>date("Y-m-d",strtotime(date("Y-m-01")." + 1 month - 1 day")),"envoye"=>"oui"));
		$id_facturation2=$this->obj->i(array("id_affaire"=>$id_affaire,"id_societe"=>$this->id_societe,"id_facture"=>$id_facture,"montant"=>200,"frais_de_gestion"=>20,"assurance"=>2,"date_periode_debut"=>date("Y-m-d",strtotime(date("Y-m-01")." - 1 month")),"date_periode_fin"=>date("Y-m-d",strtotime(date("Y-m-01")." - 1 day")),"envoye"=>"oui"));


		$this->assertEquals(
							array(
									"total_ht"=>"300.00",
									"total_assurance"=>"3.00",
									"total_frais_de_gestion"=>"30.00",
									"facturation.id_facturation"=>$id_facturation1,
									"loyer"=>"300.00",
									"tva"=>"65.27",
									"total"=>"398.27"
							)
							,$this->obj->montant_total($id_affaire,"contrat"),'Montant_total ne renvoi pas les bons totaux');
	}


	//@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	public function testInsert_facturation_prolongation(){

		$id_affaire=ATF::affaire()->decryptId(ATF::affaire()->i(array("etat"=>"commande","date"=>date("Y-m-d"),"ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu","nature"=>"affaire")));
		$id_devis=ATF::devis()->decryptId(ATF::devis()->i(array("ref"=>"refTu","id_user"=>$this->id_user,"id_societe"=>$this->id_societe,"id_filiale"=>246,"prix"=>600,"date"=>date("Y-m-d"),"devis"=>"AffaireTu","type_contrat"=>"lld","date_accord"=>date("Y-m-d"),"etat"=>"gagne","id_contact"=>$this->id_contact,"id_affaire"=>$id_affaire,"tva"=>"1.196","loyer_unique"=>"non","prix_achat"=>0,"validite"=>date("Y-m-d"))));
		$id_devis_ligne=ATF::devis_ligne()->i(array("type"=>"portable","id_devis"=>$id_devis,"id_produit"=>6,"ref"=>"GPAC-GPS","produit"=>"Ordinateur TOM TOM GO500","quantite"=>2,"id_fournisseur"=>5385,"prix_achat"=>"0","visible"=>"oui","visibilite_prix"=>"visible"));
		$id_commande=ATF::commande()->decryptId(ATF::commande()->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"commande"=>"AffaireTu","prix_achat"=>0,"prix"=>600,"date"=>date("Y-m-d"),"id_devis"=>$id_devis,"etat"=>"mis_loyer","id_user"=>$this->id_user,"tva"=>"1.196","clause_logicielle"=>"non","date_debut"=>date("2010-01-01"),"date_evolution"=>date("2010-12-31"),"type"=>"prelevement","id_affaire"=>$id_affaire)));
		$id_commande_ligne1=ATF::commande_ligne()->i(array("id_commande"=>$id_commande,"id_produit"=>6,"ref"=>"GPAC-GPS","produit"=>"Ordinateur TOM TOM GO500","quantite"=>2,"id_fournisseur"=>5385,"prix_achat"=>"0","visible"=>"oui"));
		$id_commande_ligne2=ATF::commande_ligne()->i(array("id_commande"=>$id_commande,"id_produit"=>6,"ref"=>"GPAC-GPS1","produit"=>"Ordinateur TOM TOM GO5001","quantite"=>2,"id_fournisseur"=>5385,"prix_achat"=>"0","visible"=>"oui"));

		$id_prolongation=ATF::prolongation()->i(array("id_affaire"=>$id_affaire,"ref"=>"prolongTu","id_societe"=>$this->id_societe,"date_debut"=>date("2011-01-01"),"date_fin"=>"2012-09-30"));

		$id_loyer_prolongation1=ATF::loyer_prolongation()->i(array("id_affaire"=>$id_affaire,"id_prolongation"=>$id_prolongation,"loyer"=>"100","duree"=>3,"frequence_loyer"=>"mois","date_debut"=>"2011-01-01","date_fin"=>"2011-03-31"));
		$id_loyer_prolongation2=ATF::loyer_prolongation()->i(array("id_affaire"=>$id_affaire,"id_prolongation"=>$id_prolongation,"loyer"=>"50","duree"=>2,"frequence_loyer"=>"trimestre","date_debut"=>"2011-04-01","date_fin"=>"2011-09-30"));
		$id_loyer_prolongation3=ATF::loyer_prolongation()->i(array("id_affaire"=>$id_affaire,"id_prolongation"=>$id_prolongation,"loyer"=>"25","duree"=>1,"frequence_loyer"=>"an","date_debut"=>"2011-10-01","date_fin"=>"2012-09-30"));

		$commande = new commande_cleodis($id_commande);
		$this->assertTrue($this->obj->insert_facturation_prolongation($commande),"insert_facturation_prolongation devrait renvoyer true");
		$this->assertEquals(
							array(
									"id_prolongation"=>$id_prolongation,
									"id_affaire"=>$id_affaire,
									"ref"=>"prolongTu",
									"id_refinanceur"=>NULL,
									"date_debut"=>"2011-01-01",
									"date_fin"=>"2012-09-30",
									"id_societe"=>$this->id_societe,
									"date_arret"=>NULL
							)
							,ATF::prolongation()->select($id_prolongation),'insert_facturation_prolongation ne met pas à jour les dates de prolongation');

		$this->assertEquals(
							array(
									"id_loyer_prolongation"=>$id_loyer_prolongation1,
									"id_affaire"=>$id_affaire,
									"id_prolongation"=>$id_prolongation,
									"loyer"=>"100.00",
									"duree"=>3,
									"assurance"=>NULL,
									"frais_de_gestion"=>NULL,
									"frequence_loyer"=>"mois",
									"date_debut"=>"2011-01-01",
									"date_fin"=>"2011-03-31",
									'serenite' => '0.00',
									'maintenance' => '0.00',
									'hotline' => '0.00',
									'supervision' => '0.00',
									'support' => '0.00'
							)
							,ATF::loyer_prolongation()->select($id_loyer_prolongation1),'insert_facturation_prolongation ne met pas à jour les dates de loyer_prolongation1');

		$this->assertEquals(
							array(
									"id_loyer_prolongation"=>$id_loyer_prolongation2,
									"id_affaire"=>$id_affaire,
									"id_prolongation"=>$id_prolongation,
									"loyer"=>"50.00",
									"duree"=>2,
									"assurance"=>NULL,
									"frais_de_gestion"=>NULL,
									"frequence_loyer"=>"trimestre",
									"date_debut"=>"2011-04-01",
									"date_fin"=>"2011-09-30",
									'serenite' => '0.00',
									'maintenance' => '0.00',
									'hotline' => '0.00',
									'supervision' => '0.00',
									'support' => '0.00'
							)
							,ATF::loyer_prolongation()->select($id_loyer_prolongation2),'insert_facturation_prolongation ne met pas à jour les dates de loyer_prolongation2');

		$this->assertEquals(
							array(
									"id_loyer_prolongation"=>$id_loyer_prolongation3,
									"id_affaire"=>$id_affaire,
									"id_prolongation"=>$id_prolongation,
									"loyer"=>"25.00",
									"duree"=>1,
									"assurance"=>NULL,
									"frais_de_gestion"=>NULL,
									"frequence_loyer"=>"an",
									"date_debut"=>"2011-10-01",
									"date_fin"=>"2012-09-30",
									'serenite' => '0.00',
									'maintenance' => '0.00',
									'hotline' => '0.00',
									'supervision' => '0.00',
									'support' => '0.00'
							)
							,ATF::loyer_prolongation()->select($id_loyer_prolongation3),'insert_facturation_prolongation ne met pas à jour les dates de loyer_prolongation3');


		$this->obj->q->reset()->addCondition("id_affaire",$id_affaire);
		$facturations=$this->obj->sa();
		$this->assertEquals(array(
								0=>array(
										"id_facturation"=>$facturations[0]["id_facturation"],
										"id_affaire"=>$id_affaire,
										"id_societe"=>$this->id_societe,
										"id_facture"=>NULL,
										"montant"=>"100.00",
										"frais_de_gestion"=>"0.00",
										"assurance"=>NULL,
										"date_periode_debut"=>"2011-01-01",
										"type"=>"prolongation",
										"envoye"=>"non",
										"date_periode_fin"=>"2011-01-31"
								),
								1=>array(
										"id_facturation"=>$facturations[1]["id_facturation"],
										"id_affaire"=>$id_affaire,
										"id_societe"=>$this->id_societe,
										"id_facture"=>NULL,
										"montant"=>"100.00",
										"frais_de_gestion"=>"0.00",
										"assurance"=>NULL,
										"date_periode_debut"=>"2011-02-01",
										"type"=>"prolongation",
										"envoye"=>"non",
										"date_periode_fin"=>"2011-02-28"
								),
								2=>array(
										"id_facturation"=>$facturations[2]["id_facturation"],
										"id_affaire"=>$id_affaire,
										"id_societe"=>$this->id_societe,
										"id_facture"=>NULL,
										"montant"=>"100.00",
										"frais_de_gestion"=>"0.00",
										"assurance"=>NULL,
										"date_periode_debut"=>"2011-03-01",
										"type"=>"prolongation",
										"envoye"=>"non",
										"date_periode_fin"=>"2011-03-31"
								),
								3=>array(
										"id_facturation"=>$facturations[3]["id_facturation"],
										"id_affaire"=>$id_affaire,
										"id_societe"=>$this->id_societe,
										"id_facture"=>NULL,
										"montant"=>"50.00",
										"frais_de_gestion"=>"0.00",
										"assurance"=>NULL,
										"date_periode_debut"=>"2011-04-01",
										"type"=>"prolongation",
										"envoye"=>"non",
										"date_periode_fin"=>"2011-06-30"
								),
								4=>array(
										"id_facturation"=>$facturations[4]["id_facturation"],
										"id_affaire"=>$id_affaire,
										"id_societe"=>$this->id_societe,
										"id_facture"=>NULL,
										"montant"=>"50.00",
										"frais_de_gestion"=>"0.00",
										"assurance"=>NULL,
										"date_periode_debut"=>"2011-07-01",
										"type"=>"prolongation",
										"envoye"=>"non",
										"date_periode_fin"=>"2011-09-30"
								),
								5=>array(
										"id_facturation"=>$facturations[5]["id_facturation"],
										"id_affaire"=>$id_affaire,
										"id_societe"=>$this->id_societe,
										"id_facture"=>NULL,
										"montant"=>"25.00",
										"frais_de_gestion"=>"0.00",
										"assurance"=>NULL,
										"date_periode_debut"=>"2011-10-01",
										"type"=>"prolongation",
										"envoye"=>"non",
										"date_periode_fin"=>"2012-09-30"
								)
							)
							,$this->obj->sa(),'insert_facturation_prolongation ne créé pas les bonnes facturations');

	}

	//@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	public function testInsert_facturations(){

		$id_affaire=ATF::affaire()->decryptId(ATF::affaire()->i(array("etat"=>"commande","date"=>date("Y-m-d"),"ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu","nature"=>"affaire")));
		$id_loyer1=ATF::loyer()->decryptId(ATF::loyer()->i(array("id_affaire"=>$id_affaire,"loyer"=>200,"duree"=>3,"assurance"=>20,"frais_de_gestion"=>2,"frequence_loyer"=>"mois")));
		$id_loyer2=ATF::loyer()->decryptId(ATF::loyer()->i(array("id_affaire"=>$id_affaire,"loyer"=>100,"duree"=>1,"assurance"=>10,"frais_de_gestion"=>1,"frequence_loyer"=>"trimestre")));
		$id_loyer3=ATF::loyer()->decryptId(ATF::loyer()->i(array("id_affaire"=>$id_affaire,"loyer"=>50,"duree"=>1,"assurance"=>5,"frais_de_gestion"=>0,"frequence_loyer"=>"an")));
		$id_loyer4=ATF::loyer()->decryptId(ATF::loyer()->i(array("id_affaire"=>$id_affaire,"loyer"=>50,"duree"=>1,"assurance"=>5,"frais_de_gestion"=>0,"frequence_loyer"=>"jour")));


		$id_devis=ATF::devis()->decryptId(ATF::devis()->i(array("ref"=>"refTu","id_user"=>$this->id_user,"id_societe"=>$this->id_societe,"id_filiale"=>246,"prix"=>600,"date"=>date("Y-m-d"),"devis"=>"AffaireTu","type_contrat"=>"lld","date_accord"=>date("Y-m-d"),"etat"=>"gagne","id_contact"=>$this->id_contact,"id_affaire"=>$id_affaire,"tva"=>"1.196","loyer_unique"=>"non","prix_achat"=>0,"validite"=>date("Y-m-d"))));
		$id_devis_ligne=ATF::devis_ligne()->i(array("type"=>"portable","id_devis"=>$id_devis,"id_produit"=>6,"ref"=>"GPAC-GPS","produit"=>"Ordinateur TOM TOM GO500","quantite"=>2,"id_fournisseur"=>5385,"prix_achat"=>"0","visible"=>"oui","visibilite_prix"=>"visible"));
		$id_commande=ATF::commande()->decryptId(ATF::commande()->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"commande"=>"AffaireTu","prix_achat"=>0,"prix"=>600,"date"=>date("Y-m-d"),"id_devis"=>$id_devis,"etat"=>"mis_loyer","id_user"=>$this->id_user,"tva"=>"1.196","clause_logicielle"=>"non","date_debut"=>date("2010-01-01"),"date_evolution"=>date("2010-12-31"),"type"=>"prelevement","id_affaire"=>$id_affaire)));
		$id_commande_ligne1=ATF::commande_ligne()->i(array("id_commande"=>$id_commande,"id_produit"=>6,"ref"=>"GPAC-GPS","produit"=>"Ordinateur TOM TOM GO500","quantite"=>2,"id_fournisseur"=>5385,"prix_achat"=>"0","visible"=>"oui"));
		$id_commande_ligne2=ATF::commande_ligne()->i(array("id_commande"=>$id_commande,"id_produit"=>6,"ref"=>"GPAC-GPS1","produit"=>"Ordinateur TOM TOM GO5001","quantite"=>2,"id_fournisseur"=>5385,"prix_achat"=>"0","visible"=>"oui"));

		$commande = new commande_cleodis($id_commande);
		$devis = new devis($id_devis);
		$affaire=new affaire_cleodis($id_affaire);

		$this->assertTrue($this->obj->insert_facturations($commande,$affaire,false,$devis,"contrat"),"insert_facturation devrait renvoyer true" );
		$this->obj->q->reset()->addCondition("id_affaire",$id_affaire);
		$facturations=$this->obj->sa();
		$this->assertEquals(array(
								0=>array(
										"id_facturation"=>$facturations[0]["id_facturation"],
										"id_affaire"=>$id_affaire,
										"id_societe"=>$this->id_societe,
										"id_facture"=>NULL,
										"montant"=>"200.00",
										"frais_de_gestion"=>"2.00",
										"assurance"=>"20.00",
										"date_periode_debut"=>"2010-01-01",
										"type"=>"contrat",
										"envoye"=>"non",
										"date_periode_fin"=>"2010-01-31"


								),
								1=>array(
										"id_facturation"=>$facturations[1]["id_facturation"],
										"id_affaire"=>$id_affaire,
										"id_societe"=>$this->id_societe,
										"id_facture"=>NULL,
										"montant"=>"200.00",
										"frais_de_gestion"=>"2.00",
										"assurance"=>"20.00",
										"date_periode_debut"=>"2010-02-01",
										"type"=>"contrat",
										"envoye"=>"non",
										"date_periode_fin"=>"2010-02-28"

								),
								2=>array(
										"id_facturation"=>$facturations[2]["id_facturation"],
										"id_affaire"=>$id_affaire,
										"id_societe"=>$this->id_societe,
										"id_facture"=>NULL,
										"montant"=>"200.00",
										"frais_de_gestion"=>"2.00",
										"assurance"=>"20.00",
										"date_periode_debut"=>"2010-03-01",
										"type"=>"contrat",
										"envoye"=>"non",
										"date_periode_fin"=>"2010-03-31"

								),
								3=>array(
										"id_facturation"=>$facturations[3]["id_facturation"],
										"id_affaire"=>$id_affaire,
										"id_societe"=>$this->id_societe,
										"id_facture"=>NULL,
										"montant"=>"100.00",
										"frais_de_gestion"=>"1.00",
										"assurance"=>"10.00",
										"date_periode_debut"=>"2010-04-01",
										"type"=>"contrat",
										"envoye"=>"non",
										"date_periode_fin"=>"2010-06-30"

								),
								4=>array(
										"id_facturation"=>$facturations[4]["id_facturation"],
										"id_affaire"=>$id_affaire,
										"id_societe"=>$this->id_societe,
										"id_facture"=>NULL,
										"montant"=>"50.00",
										"frais_de_gestion"=>"0.00",
										"assurance"=>"5.00",
										"date_periode_debut"=>"2010-07-01",
										"type"=>"contrat",
										"envoye"=>"non",
										"date_periode_fin"=>"2011-06-30"

								),
								5=>array(
										"id_facturation"=>$facturations[5]["id_facturation"],
										"id_affaire"=>$id_affaire,
										"id_societe"=>$this->id_societe,
										"id_facture"=>NULL,
										"montant"=>"50.00",
										"frais_de_gestion"=>"0.00",
										"assurance"=>"5.00",
										"date_periode_debut"=>"2011-07-01",
										"type"=>"contrat",
										"envoye"=>"non",
										"date_periode_fin"=>"2011-07-02"

								)
							)
							,$facturations,'insert_facturation ne créé pas les bonnes facturations');

		//////////////////AR
		$id_affaire_parente=ATF::affaire()->decryptId(ATF::affaire()->i(array("etat"=>"commande","date"=>date("Y-m-d"),"ref"=>"refTuParenre","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu","nature"=>"affaire","id_fille"=>$id_affaire)));
		$id_loyer1=ATF::loyer()->decryptId(ATF::loyer()->i(array("id_affaire"=>$id_affaire_parente,"loyer"=>200,"duree"=>3,"assurance"=>20,"frais_de_gestion"=>2,"frequence_loyer"=>"mois")));
		$id_loyer2=ATF::loyer()->decryptId(ATF::loyer()->i(array("id_affaire"=>$id_affaire_parente,"loyer"=>100,"duree"=>1,"assurance"=>10,"frais_de_gestion"=>1,"frequence_loyer"=>"trimestre")));
		$id_loyer3=ATF::loyer()->decryptId(ATF::loyer()->i(array("id_affaire"=>$id_affaire_parente,"loyer"=>50,"duree"=>1,"assurance"=>5,"frais_de_gestion"=>0,"frequence_loyer"=>"an")));

		$id_devis_parent=ATF::devis()->decryptId(ATF::devis()->i(array("ref"=>"refTuParenre","id_user"=>$this->id_user,"id_societe"=>$this->id_societe,"id_filiale"=>246,"prix"=>600,"date"=>date("Y-m-d"),"devis"=>"AffaireTu","type_contrat"=>"lld","date_accord"=>date("Y-m-d"),"etat"=>"gagne","id_contact"=>$this->id_contact,"id_affaire"=>$id_affaire_parente,"tva"=>"1.196","loyer_unique"=>"non","prix_achat"=>0,"validite"=>date("Y-m-d"))));
		$id_devis_parent_ligne=ATF::devis_ligne()->i(array("type"=>"portable","id_devis"=>$id_devis_parent,"id_produit"=>6,"ref"=>"GPAC-GPS","produit"=>"Ordinateur TOM TOM GO500","quantite"=>2,"id_fournisseur"=>5385,"prix_achat"=>"0","visible"=>"oui","visibilite_prix"=>"visible"));
		$id_commande_parent=ATF::commande()->decryptId(ATF::commande()->i(array("ref"=>"refTuParenre","id_societe"=>$this->id_societe,"commande"=>"AffaireTu","prix_achat"=>0,"prix"=>600,"date"=>date("Y-m-d"),"id_devis"=>$id_devis_parent,"etat"=>"mis_loyer","id_user"=>$this->id_user,"tva"=>"1.196","clause_logicielle"=>"non","date_debut"=>date("2009-01-01"),"date_evolution"=>date("2009-12-31"),"type"=>"prelevement","id_affaire"=>$id_affaire_parente)));
		$id_commande_parent_ligne1=ATF::commande_ligne()->i(array("id_commande"=>$id_commande_parent,"id_produit"=>6,"ref"=>"GPAC-GPS","produit"=>"Ordinateur TOM TOM GO500","quantite"=>2,"id_fournisseur"=>5385,"prix_achat"=>"0","visible"=>"oui"));
		$id_commande_parent_ligne2=ATF::commande_ligne()->i(array("id_commande"=>$id_commande_parent,"id_produit"=>6,"ref"=>"GPAC-GPS1","produit"=>"Ordinateur TOM TOM GO5001","quantite"=>2,"id_fournisseur"=>5385,"prix_achat"=>"0","visible"=>"oui"));

		ATF::affaire()->decryptId(ATF::affaire()->u(array("id_affaire"=>$id_affaire,"nature"=>"AR")));

		$commande = new commande_cleodis($id_commande);
		$devis = new devis_cleodis($id_devis);
		$affaire=new affaire_cleodis($id_affaire);

		if ($ap = $affaire->getParentAR()) {
			// Parfois l'affaire a plusieurs parents car elle annule et remplace plusieurs autres affaires
			foreach ($ap as $a) {
				$affaires_parentes[] = new affaire_cleodis($a["id_affaire"]);
			}
		}

		$this->assertTrue($this->obj->insert_facturations($commande,$affaire,$affaires_parentes,$devis,"contrat"),"insert_facturation devrait renvoyer true" );
		$this->obj->q->reset()->addCondition("id_affaire",$id_affaire_parente);
		$facturations_parente=$this->obj->sa();
		$this->assertEquals(array(
								0=>array(
										"id_facturation"=>$facturations_parente[0]["id_facturation"],
										"id_affaire"=>$id_affaire_parente,
										"id_societe"=>$this->id_societe,
										"id_facture"=>NULL,
										"montant"=>"200.00",
										"frais_de_gestion"=>"2.00",
										"assurance"=>"20.00",
										"date_periode_debut"=>"2009-01-01",
										"type"=>"contrat",
										"envoye"=>"non",
										"date_periode_fin"=>"2009-01-31"
								),
								1=>array(
										"id_facturation"=>$facturations_parente[1]["id_facturation"],
										"id_affaire"=>$id_affaire_parente,
										"id_societe"=>$this->id_societe,
										"id_facture"=>NULL,
										"montant"=>"200.00",
										"frais_de_gestion"=>"2.00",
										"assurance"=>"20.00",
										"date_periode_debut"=>"2009-02-01",
										"type"=>"contrat",
										"envoye"=>"non",
										"date_periode_fin"=>"2009-02-28"
								),
								2=>array(
										"id_facturation"=>$facturations_parente[2]["id_facturation"],
										"id_affaire"=>$id_affaire_parente,
										"id_societe"=>$this->id_societe,
										"id_facture"=>NULL,
										"montant"=>"200.00",
										"frais_de_gestion"=>"2.00",
										"assurance"=>"20.00",
										"date_periode_debut"=>"2009-03-01",
										"type"=>"contrat",
										"envoye"=>"non",
										"date_periode_fin"=>"2009-03-31"
								),
								3=>array(
										"id_facturation"=>$facturations_parente[3]["id_facturation"],
										"id_affaire"=>$id_affaire_parente,
										"id_societe"=>$this->id_societe,
										"id_facture"=>NULL,
										"montant"=>"100.00",
										"frais_de_gestion"=>"1.00",
										"assurance"=>"10.00",
										"date_periode_debut"=>"2009-04-01",
										"type"=>"contrat",
										"envoye"=>"non",
										"date_periode_fin"=>"2009-06-30"
								)
							)
							,$facturations_parente,'insert_facturation ne créé pas les bonnes facturations parent 2');


		//////////////////Avenant
		$id_affaire_avenant=ATF::affaire()->decryptId(ATF::affaire()->i(array("etat"=>"commande","date"=>date("Y-m-d"),"ref"=>"refTuAvenant","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu","nature"=>"avenant","id_parent"=>$id_affaire)));
		$id_loyer1=ATF::loyer()->decryptId(ATF::loyer()->i(array("id_affaire"=>$id_affaire_avenant,"loyer"=>200,"duree"=>3,"assurance"=>20,"frais_de_gestion"=>2,"frequence_loyer"=>"mois")));
		$id_loyer2=ATF::loyer()->decryptId(ATF::loyer()->i(array("id_affaire"=>$id_affaire_avenant,"loyer"=>100,"duree"=>1,"assurance"=>10,"frais_de_gestion"=>1,"frequence_loyer"=>"trimestre")));
		$id_loyer3=ATF::loyer()->decryptId(ATF::loyer()->i(array("id_affaire"=>$id_affaire_avenant,"loyer"=>50,"duree"=>1,"assurance"=>5,"frais_de_gestion"=>0,"frequence_loyer"=>"an")));

		$id_devis_avenant=ATF::devis()->decryptId(ATF::devis()->i(array("ref"=>"refTuAvenant","id_user"=>$this->id_user,"id_societe"=>$this->id_societe,"id_filiale"=>246,"prix"=>600,"date"=>date("Y-m-d"),"devis"=>"AffaireTu","type_contrat"=>"lld","date_accord"=>date("Y-m-d"),"etat"=>"gagne","id_contact"=>$this->id_contact,"id_affaire"=>$id_affaire_avenant,"tva"=>"1.196","loyer_unique"=>"oui","prix_achat"=>0,"validite"=>date("Y-m-d"))));
		$id_devis_avenant_ligne=ATF::devis_ligne()->i(array("type"=>"portable","id_devis"=>$id_devis_avenant,"id_produit"=>6,"ref"=>"GPAC-GPS","produit"=>"Ordinateur TOM TOM GO500","quantite"=>2,"id_fournisseur"=>5385,"prix_achat"=>"0","visible"=>"oui","visibilite_prix"=>"visible"));
		$id_commande_avenant=ATF::commande()->decryptId(ATF::commande()->i(array("ref"=>"refTuAvenant","id_societe"=>$this->id_societe,"commande"=>"AffaireTu","prix_achat"=>0,"prix"=>600,"date"=>date("Y-m-d"),"id_devis"=>$id_devis_avenant,"etat"=>"mis_loyer","id_user"=>$this->id_user,"tva"=>"1.196","clause_logicielle"=>"non","date_debut"=>date("2009-01-01"),"date_evolution"=>date("2009-12-31"),"type"=>"prelevement","id_affaire"=>$id_affaire_avenant)));
		$id_commande_avenant_ligne1=ATF::commande_ligne()->i(array("id_commande"=>$id_commande_avenant,"id_produit"=>6,"ref"=>"GPAC-GPS","produit"=>"Ordinateur TOM TOM GO500","quantite"=>2,"id_fournisseur"=>5385,"prix_achat"=>"0","visible"=>"oui"));
		$id_commande_avenant_ligne2=ATF::commande_ligne()->i(array("id_commande"=>$id_commande_avenant,"id_produit"=>6,"ref"=>"GPAC-GPS1","produit"=>"Ordinateur TOM TOM GO5001","quantite"=>2,"id_fournisseur"=>5385,"prix_achat"=>"0","visible"=>"oui"));

		ATF::affaire()->decryptId(ATF::affaire()->u(array("id_affaire"=>$id_affaire,"nature"=>"AR")));

		$commande_avenant = new commande_cleodis($id_commande_avenant);
		$devis_avenant = new devis_cleodis($id_devis_avenant);
		$affaire_avenant=new affaire_cleodis($id_affaire_avenant);


		$this->assertTrue($this->obj->insert_facturations($commande_avenant,$affaire_avenant,false,$devis_avenant,"contrat"),"insert_facturation avenant devrait renvoyer true" );
		$this->obj->q->reset()->addCondition("id_affaire",$id_affaire_avenant);
		$facturations_avenant=$this->obj->sa();
		$this->assertEquals(array(
								0=>array(
										"id_facturation"=>$facturations_avenant[0]["id_facturation"],
										"id_affaire"=>$id_affaire_avenant,
										"id_societe"=>$this->id_societe,
										"id_facture"=>NULL,
										"montant"=>"200.00",
										"frais_de_gestion"=>"2.00",
										"assurance"=>"20.00",
										"date_periode_debut"=>"2009-01-01",
										"type"=>"contrat",
										"envoye"=>"non",
										"date_periode_fin"=>"2009-12-31"
								)
							)
							,$facturations_avenant,'insert_facturation ne créé pas les bonnes facturations avenant');


	}


	//@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	public function testInsert_facturation(){

		$date_debut_periode = date("Y-m-d",mktime(0,0,0,date("m"),01,date("Y")));
		$date_debut_periode=date("Y-m-d",strtotime($date_debut_periode."+1 month"));
		$date_fin_periode=date("Y-m-d",strtotime($date_debut_periode."+1 month"));

		$id_affaire=ATF::affaire()->decryptId(ATF::affaire()->i(array("etat"=>"commande","date"=>date("Y-m-d"),"ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu","nature"=>"affaire")));
		$id_loyer1=ATF::loyer()->decryptId(ATF::loyer()->i(array("id_affaire"=>$id_affaire,"loyer"=>200,"duree"=>3,"assurance"=>20,"frais_de_gestion"=>2,"frequence_loyer"=>"mois")));
		$id_loyer2=ATF::loyer()->decryptId(ATF::loyer()->i(array("id_affaire"=>$id_affaire,"loyer"=>100,"duree"=>1,"assurance"=>10,"frais_de_gestion"=>1,"frequence_loyer"=>"trimestre")));
		$id_loyer3=ATF::loyer()->decryptId(ATF::loyer()->i(array("id_affaire"=>$id_affaire,"loyer"=>50,"duree"=>1,"assurance"=>5,"frais_de_gestion"=>0,"frequence_loyer"=>"an")));

		$id_devis=ATF::devis()->decryptId(ATF::devis()->i(array("ref"=>"refTu","id_user"=>$this->id_user,"id_societe"=>$this->id_societe,"id_filiale"=>246,"prix"=>600,"date"=>date("Y-m-d"),"devis"=>"AffaireTu","type_contrat"=>"lld","date_accord"=>date("Y-m-d"),"etat"=>"gagne","id_contact"=>$this->id_contact,"id_affaire"=>$id_affaire,"tva"=>"1.196","loyer_unique"=>"non","prix_achat"=>0,"validite"=>date("Y-m-d"))));
		$id_devis_ligne=ATF::devis_ligne()->i(array("type"=>"portable","id_devis"=>$id_devis,"id_produit"=>6,"ref"=>"GPAC-GPS","produit"=>"Ordinateur TOM TOM GO500","quantite"=>2,"id_fournisseur"=>5385,"prix_achat"=>"0","visible"=>"oui","visibilite_prix"=>"visible"));
		$id_commande=ATF::commande()->decryptId(ATF::commande()->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"commande"=>"AffaireTu","prix_achat"=>0,"prix"=>600,"date"=>date("Y-m-d"),"id_devis"=>$id_devis,"etat"=>"arreter","id_user"=>$this->id_user,"tva"=>"1.196","clause_logicielle"=>"non","date_debut"=>date("2010-01-01"),"date_evolution"=>date("2010-12-31"),"type"=>"prelevement","id_affaire"=>$id_affaire)));
		$id_commande_ligne2=ATF::commande_ligne()->i(array("id_commande"=>$id_commande,"id_produit"=>6,"ref"=>"GPAC-GPS1","produit"=>"Ordinateur TOM TOM GO5001","quantite"=>2,"id_fournisseur"=>5385,"prix_achat"=>"0","visible"=>"oui"));

		$commande = new commande_cleodis($id_commande);
		$devis = new devis($id_devis);
		$affaire=new affaire_cleodis($id_affaire);

//		$this->assertTrue($this->obj->insert_facturations($commande,$affaire,false,$devis,"contrat"),"insert_facturation devrait renvoyer true" );

		$this->assertFalse($this->obj->insert_facturation($commande,$affaire),"L'insertion de facturation ne devrait pas se faire car l'affaire est arrêté");
		ATF::commande()->u(array("id_commande"=>$id_commande,"etat"=>"mis_loyer"));

		$commande = new commande_cleodis($id_commande);
		$id=$this->obj->insert_facturation($commande,$affaire);
		$this->assertEquals(array(
										"id_facturation"=>$id,
										"id_affaire"=>$id_affaire,
										"id_societe"=>$this->id_societe,
										"id_facture"=>NULL,
										"montant"=>"50.00",
										"frais_de_gestion"=>"0.00",
										"assurance"=>"5.00",
										"date_periode_debut"=>$date_debut_periode,
										"type"=>"prolongation",
										"envoye"=>"non",
										"date_periode_fin"=>date("Y-m-d",strtotime($date_debut_periode." + 1 year - 1 day"))
								)
								,$this->obj->select($id)
								,"L'insertion de la facturation ne fonctionne pas fréquence an");
		$this->obj->d($id);

		ATF::loyer()->u(array("id_loyer"=>$id_loyer3,"frequence_loyer"=>"mois"));
		$id=$this->obj->insert_facturation($commande,$affaire);
		$this->assertEquals(array(
										"id_facturation"=>$id,
										"id_affaire"=>$id_affaire,
										"id_societe"=>$this->id_societe,
										"id_facture"=>NULL,
										"montant"=>"50.00",
										"frais_de_gestion"=>"0.00",
										"assurance"=>"5.00",
										"date_periode_debut"=>$date_debut_periode,
										"type"=>"prolongation",
										"envoye"=>"non",
										"date_periode_fin"=>date("Y-m-d",strtotime($date_debut_periode." + 1 month - 1 day"))
								)
								,$this->obj->select($id)
								,"L'insertion de la facturation ne fonctionne pas fréquence mois");
		$this->obj->d($id);

		ATF::loyer()->u(array("id_loyer"=>$id_loyer3,"frequence_loyer"=>"trimestre"));
		$id=$this->obj->insert_facturation($commande,$affaire);
		$this->assertEquals(array(
										"id_facturation"=>$id,
										"id_affaire"=>$id_affaire,
										"id_societe"=>$this->id_societe,
										"id_facture"=>NULL,
										"montant"=>"50.00",
										"frais_de_gestion"=>"0.00",
										"assurance"=>"5.00",
										"date_periode_debut"=>$date_debut_periode,
										"type"=>"prolongation",
										"envoye"=>"non",
										"date_periode_fin"=>date("Y-m-d",strtotime($date_debut_periode." + 3 month - 1 day"))
								)
								,$this->obj->select($id)
								,"L'insertion de la facturation ne fonctionne pas fréquence trimestre");
		$this->obj->d($id);

		$id_prolongation=ATF::prolongation()->i(array("id_affaire"=>$id_affaire,"ref"=>"prolongTu","id_societe"=>$this->id_societe,"date_debut"=>date("2011-01-01"),"date_fin"=>"2012-09-30","date_arret"=>date("2011-01-01")));
		$id_loyer_prolongation1=ATF::loyer_prolongation()->i(array("id_affaire"=>$id_affaire,"id_prolongation"=>$id_prolongation,"loyer"=>"100","duree"=>3,"frequence_loyer"=>"mois","date_debut"=>"2011-01-01","date_fin"=>"2011-03-31"));
		$id_loyer_prolongation2=ATF::loyer_prolongation()->i(array("id_affaire"=>$id_affaire,"id_prolongation"=>$id_prolongation,"loyer"=>"50","duree"=>2,"frequence_loyer"=>"trimestre","date_debut"=>"2011-04-01","date_fin"=>"2011-09-30"));
		$id_loyer_prolongation3=ATF::loyer_prolongation()->i(array("id_affaire"=>$id_affaire,"id_prolongation"=>$id_prolongation,"loyer"=>"25","duree"=>1,"frequence_loyer"=>"an","date_debut"=>"2011-10-01","date_fin"=>"2012-09-30"));

		$this->assertFalse($this->obj->insert_facturation($commande,$affaire),"L'insertion de facturation ne devrait pas se faire car le dernier loyer est arrêté");

		ATF::prolongation()->u(array("id_prolongation"=>$id_prolongation,"date_arret"=>NULL));

		$id_facturation=$this->obj->insert_facturation($commande,$affaire);
		$this->assertEquals(array(
										"id_facturation"=>$id_facturation,
										"id_affaire"=>$id_affaire,
										"id_societe"=>$this->id_societe,
										"id_facture"=>NULL,
										"montant"=>"25.00",
										"frais_de_gestion"=>"0.00",
										"assurance"=>NULL,
										"date_periode_debut"=>$date_debut_periode,
										"type"=>"prolongation",
										"envoye"=>"non",
										"date_periode_fin"=>date("Y-m-d",strtotime($date_debut_periode." + 1 year - 1 day"))
								)
								,$this->obj->select($id_facturation)
								,"L'insertion de la facturation ne fonctionne pas fréquence trimestre");
		$this->obj->d($id_facturation);
		}

	public function test_insertfacturation2(){

		$date_debut_periode = date("Y-m-d",mktime(0,0,0,date("m"),01,date("Y")));
		$date_debut_periode=date("Y-m-d",strtotime($date_debut_periode."+1 month"));
		$date_fin_periode=date("Y-m-d",strtotime($date_debut_periode."+1 month"));

		$id_affaire=ATF::affaire()->decryptId(ATF::affaire()->i(array("etat"=>"commande","date"=>date("Y-m-d"),"ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu","nature"=>"affaire")));
		$id_loyer1=ATF::loyer()->decryptId(ATF::loyer()->i(array("id_affaire"=>$id_affaire,"loyer"=>200,"duree"=>3,"assurance"=>20,"frais_de_gestion"=>2,"frequence_loyer"=>"mois")));
		$id_loyer2=ATF::loyer()->decryptId(ATF::loyer()->i(array("id_affaire"=>$id_affaire,"loyer"=>100,"duree"=>1,"assurance"=>10,"frais_de_gestion"=>1,"frequence_loyer"=>"trimestre")));
		$id_loyer3=ATF::loyer()->decryptId(ATF::loyer()->i(array("id_affaire"=>$id_affaire,"loyer"=>50,"duree"=>1,"assurance"=>5,"frais_de_gestion"=>0,"frequence_loyer"=>"an")));

		$id_devis=ATF::devis()->decryptId(ATF::devis()->i(array("ref"=>"refTu","id_user"=>$this->id_user,"id_societe"=>$this->id_societe,"id_filiale"=>246,"prix"=>600,"date"=>date("Y-m-d"),"devis"=>"AffaireTu","type_contrat"=>"lld","date_accord"=>date("Y-m-d"),"etat"=>"gagne","id_contact"=>$this->id_contact,"id_affaire"=>$id_affaire,"tva"=>"1.196","loyer_unique"=>"non","prix_achat"=>0,"validite"=>date("Y-m-d"))));
		$id_devis_ligne=ATF::devis_ligne()->i(array("type"=>"portable","id_devis"=>$id_devis,"id_produit"=>6,"ref"=>"GPAC-GPS","produit"=>"Ordinateur TOM TOM GO500","quantite"=>2,"id_fournisseur"=>5385,"prix_achat"=>"0","visible"=>"oui","visibilite_prix"=>"visible"));
		$id_commande=ATF::commande()->decryptId(ATF::commande()->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"commande"=>"AffaireTu","prix_achat"=>0,"prix"=>600,"date"=>date("Y-m-d"),"id_devis"=>$id_devis,"etat"=>"arreter","id_user"=>$this->id_user,"tva"=>"1.196","clause_logicielle"=>"non","date_debut"=>date("2010-01-01"),"date_evolution"=>date("2010-12-31"),"type"=>"prelevement","id_affaire"=>$id_affaire)));
		$id_commande_ligne1=ATF::commande_ligne()->i(array("id_commande"=>$id_commande,"id_produit"=>6,"ref"=>"GPAC-GPS","produit"=>"Ordinateur TOM TOM GO500","quantite"=>2,"id_fournisseur"=>5385,"prix_achat"=>"0","visible"=>"oui"));
		$id_commande_ligne2=ATF::commande_ligne()->i(array("id_commande"=>$id_commande,"id_produit"=>6,"ref"=>"GPAC-GPS1","produit"=>"Ordinateur TOM TOM GO5001","quantite"=>2,"id_fournisseur"=>5385,"prix_achat"=>"0","visible"=>"oui"));

		$commande = new commande_cleodis($id_commande);
		$devis = new devis($id_devis);
		$affaire=new affaire_cleodis($id_affaire);

		//******************************* RESTITUTION *****************************************************
		//Anuelle
		ATF::loyer()->u(array("id_loyer"=>$id_loyer3,"frequence_loyer"=>"an"));
		ATF::commande()->u(array("id_commande"=>$id_commande,"etat"=>"restitution" , "date_prevision_restitution" => date("Y-m-d",strtotime($date_debut_periode." - 2 month"))));
		$commande = new commande_cleodis($id_commande);
		$id_facturation=$this->obj->insert_facturation($commande,$affaire);
		$this->assertEquals(array(	"id_facturation"=>$id_facturation,
									"id_affaire"=>$id_affaire,
									"id_societe"=>$this->id_societe,
									"id_facture"=>NULL,
									"montant"=>"50.00",
									"frais_de_gestion"=>"0.00",
									"assurance"=>"5.00",
									"date_periode_debut"=>date("Y-m-d",strtotime($date_debut_periode." - 2 month")),
									"type"=>"prolongation",
									"envoye"=>"non",
									"date_periode_fin"=>date("Y-m-d",strtotime($date_debut_periode." + 1 year - 1 day - 2 month")),
									'serenite' => '0.00',
									'maintenance' => '0.00',
									'hotline' => '0.00',
									'supervision' => '0.00',
									'support' => '0.00'
								)
								,$this->obj->select($id_facturation)
								,"L'insertion de la facturation ne fonctionne pas fréquence annuelle, restitution");
		//Trimestre
		$this->obj->d($id_facturation);
		ATF::loyer()->u(array("id_loyer"=>$id_loyer3,"frequence_loyer"=>"trimestre"));
		$commande = new commande_cleodis($id_commande);
		$id_facturation=$this->obj->insert_facturation($commande,$affaire);

		$this->assertEquals(array(	"id_facturation"=>$id_facturation,
									"id_affaire"=>$id_affaire,
									"id_societe"=>$this->id_societe,
									"id_facture"=>NULL,
									"montant"=>"50.00",
									"frais_de_gestion"=>"0.00",
									"assurance"=>5.00,
									"date_periode_debut"=>date("Y-m-d",strtotime($date_debut_periode." -2 month ")),
									"type"=>"prolongation",
									"envoye"=>"non",
									"date_periode_fin"=>date("Y-m-d",strtotime($date_debut_periode." + 1 month - 1 day")),
									'serenite' => '0.00',
									'maintenance' => '0.00',
									'hotline' => '0.00',
									'supervision' => '0.00',
									'support' => '0.00'
								)
								,$this->obj->select($id_facturation)
								,"L'insertion de la facturation ne fonctionne pas fréquence trimestre, restitution");

		//Mensuelle
		$this->obj->d($id_facturation);
		ATF::loyer()->u(array("id_loyer"=>$id_loyer3,"frequence_loyer"=>"mois"));
		$commande = new commande_cleodis($id_commande);
		$id_facturation=$this->obj->insert_facturation($commande,$affaire);

		$this->assertEquals(array(	"id_facturation"=>$id_facturation,
									"id_affaire"=>$id_affaire,
									"id_societe"=>$this->id_societe,
									"id_facture"=>NULL,
									"montant"=>"50.00",
									"frais_de_gestion"=>"0.00",
									"assurance"=>5.00,
									"date_periode_debut"=>date("Y-m-d",strtotime($date_debut_periode." -2 month ")),
									"type"=>"prolongation",
									"envoye"=>"non",
									"date_periode_fin"=>date("Y-m-d",strtotime($date_debut_periode." -1 month -1 day")),
									'serenite' => '0.00',
									'maintenance' => '0.00',
									'hotline' => '0.00',
									'supervision' => '0.00',
									'support' => '0.00'
								)
								,$this->obj->select($id_facturation)
								,"L'insertion de la facturation ne fonctionne pas fréquence mois, restitution");


		ATF::facturation()->d($id_facturation);

		$id_prolongation=ATF::prolongation()->i(array("id_affaire"=>$affaire->get("id_affaire"),"ref"=>"prolongTu","id_societe"=>$this->id_societe,"date_debut"=>date("2011-01-01"),"date_fin"=>"2012-09-30"));

		$id_facturation=$this->obj->insert_facturation($commande,$affaire);
		$this->assertEquals(array(
										"id_facturation"=>$id_facturation,
										"id_affaire"=>$id_affaire,
										"id_societe"=>$this->id_societe,
										"id_facture"=>NULL,
										"montant"=>"50.00",
										"frais_de_gestion"=>"0.00",
										"assurance"=>"5.00",
										"date_periode_debut"=>date("Y-m-d",strtotime($date_debut_periode."-2 month")),
										"type"=>"prolongation",
										"envoye"=>"non",
										"date_periode_fin"=>date("Y-m-d",strtotime($date_debut_periode." - 1 day -1 month"))
								)
								,$this->obj->select($id_facturation)
								,"L'insertion de la facturation ne fonctionne pas fréquence trimestre");
	}

	// @author Yann GAUTHERON <ygautheron@absystech.fr>
	function testInsert_facture1(){
		$this->Insert_facture();
	}

	// @author Yann GAUTHERON <ygautheron@absystech.fr>
	function testInsert_facture2(){
		$this->Insert_facture(6);
	}

	// @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	// @author Yann GAUTHERON <ygautheron@absystech.fr>
	function Insert_facture($date_previsionnelle=NULL){
		$affaire=array(
						"ref"=>"affaire",
						"id_societe"=>$this->id_societe,
						"affaire"=>"insert_facture"
						);
		if ($date_previsionnelle) {
			$affaire["date_previsionnelle"]=$date_previsionnelle;
		}
		$id_affaire=ATF::affaire()->i($affaire);
		$affaire=ATF::affaire()->select($id_affaire);

		$facturation=array(
							"id_affaire"=>$id_affaire,
							"id_societe"=>$this->id_societe,
							"montant"=>"0",
							"date_periode_debut"=>date("Y-m-d",strtotime(date("Y-m-01")." + 1 month")),
							"date_periode_fin"=>date("Y-m-d",strtotime(date("Y-m-01")." + 2 month - 1 day"))
							);
		$id_facturation=ATF::facturation()->i($facturation);
		$facturation=ATF::facturation()->select($id_facturation);

		$this->assertFalse($this->obj->insert_facture($affaire,$facturation),'La facture ne doit pas se faire, il n y a pas de commande');

		$commande=array(
						"ref"=>"affaire",
						"id_societe"=>$this->id_societe,
						"id_user"=>$this->id_user,
						"tva"=>"1.196",
						"id_affaire"=>$id_affaire,
						"date_debut"=>date("Y-m-d",strtotime(date("Y-m-01")." - 1 year")),
						"date_evolution"=>date("Y-m-d",strtotime(date("Y-m-01")." + 2 year"))
						);
		$id_commande=ATF::commande()->i($commande);

		$commande_ligne=array(
						"id_commande"=>$id_commande,
						"id_produit"=>5,
						"produit"=>"produit tu",
						"quantite"=>1
						);
		ATF::commande_ligne()->i($commande_ligne);

		$this->assertEquals($this->obj->insert_facture($affaire,$facturation),"montant_zero",'La facture doit être de montant 0');

		ATF::facturation()->u(array("id_facturation"=>$id_facturation,"montant"=>"100"));
		$facturation=ATF::facturation()->select($id_facturation);

		$id_facture=$this->obj->insert_facture($affaire,$facturation);
		$facture=ATF::facture()->select($id_facture);
		ATF::facture_ligne()->q->reset()
							   ->addCondition("id_facture",$id_facture);
		$facture_ligne=ATF::facture_ligne()->sa();

		$this->assertEquals(array(

									"id_facture"=>(string)$id_facture,
									"type_facture"=>"facture",
									"ref"=>"affaire-1",
									"id_societe"=>$this->id_societe,
									"prix"=>"100.00",
									"etat"=>"impayee",
									"date"=>date("Y-m-d",strtotime(date("Y-m-01")." + 1 month")),
									"date_previsionnelle"=>date("Y-m-d",strtotime(date("Y-m-01")." + 1 month".($date_previsionnelle ? " +".$date_previsionnelle." day" : ""))),
									"date_paiement"=>date("Y-m-d",strtotime(date("Y-m-01")." + 1 month".($date_previsionnelle ? " +".$date_previsionnelle." day" : ""))),
									"date_relance"=>NULL,
									"date_periode_debut"=>date("Y-m-d",strtotime(date("Y-m-01")." + 1 month")),
									"date_periode_fin"=>date("Y-m-d",strtotime(date("Y-m-01")." + 2 month - 1 day")),
									"tva"=>"1.200",
									"id_user"=>$this->id_user,
									"id_commande"=>(string)$id_commande,
									"id_affaire"=>(string)$id_affaire,
									"mode_paiement"=>"prelevement",
									"id_demande_refi"=>NULL,
									"id_refinanceur"=>NULL,
									"envoye_mail"=>NULL,
									"rejet"=>"non_rejet",
									"commentaire"=>NULL,
									"type_libre"=>NULL,
									'redevance' => 'oui',
									'date_rejet' => NULL,
									'date_regularisation' => NULL
							)
							,$facture,'insert_facture ne créé pas la bonne facture');


		$this->assertEquals(array(
								0=>array(
										"id_facture_ligne"=>$facture_ligne[0]["id_facture_ligne"],
										"id_facture"=>$id_facture,
										"id_produit"=>5,
										"ref"=>NULL,
										"produit"=>"produit tu",
										"quantite"=>"1",
										"id_fournisseur"=>NULL,
										"prix_achat"=>NULL,
										"serial"=>NULL,
										"code"=>NULL,
										"id_affaire_provenance"=>NULL,
										"visible"=>"oui",
										"afficher"=>"oui"
								)
							)
							,$facture_ligne,'insert_facturation ne créé pas la bonne facture_ligne');



		$this->assertFalse($this->obj->insert_facture($affaire,$facturation),'La facture ne doit pas se faire car il existe deja une facture');

	}

	// @author Yann GAUTHERON <ygautheron@absystech.fr>
	public function test_facturationMensuelleContrat1(){

		$id_societe=ATF::societe()->i(array("societe"=>"TU","code_client"=>"TU"));
		$id_affaire=ATF::affaire()->i(array("ref"=>"REFTu","id_societe"=>$id_societe,"affaire"=>"ATU"));
		$id_commande=ATF::commande()->i(array(
												"ref"=>"Ref tu",
												"id_societe"=>$id_societe,
												"id_user"=>$this->id_user,
												"tva"=>"1,196",
												"id_affaire"=>$id_affaire,
												"date_debut"=>date("Y-m-d",strtotime(date("Y-m-01")."-1 day")),
												"date_evolution"=>date("Y-m-d",strtotime(date("Y-m-01")."+1 day"))
											)
										);

		$id_facturation=ATF::facturation()->i(array(
													"id_affaire"=>$id_affaire,
													"id_societe"=>$id_societe,
													"montant"=>"100",
													"date_periode_debut"=>date("Y-m-d",strtotime(date("Y-m-01")." + 1 month")),
													"date_periode_fin"=>date("Y-m-d",strtotime(date("Y-m-01")." + 2 month"))
													)
												);


		$this->obj->facturationMensuelle(true);
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture1=ATF::facture()->sa();
		$this->assertNotNull($facture1,"Problème car une facture type ne passe pas");
		ATF::facture()->d($facture1["id_facture"]);

		//Mauvaise date 1
		ATF::facturation()->u(array(
										"id_facturation"=>$id_facturation,
										"envoye"=>"non",
										"date_periode_debut"=>date("Y-m-d",strtotime(date("Y-m-01")." + 2 month"))
									)
								);
		$this->obj->facturationMensuelle(true);
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture2=ATF::facture()->sa();
		$this->assertNull($facture2,"Problème car une facture avec mauvaise date debut passe pas");

		//Mauvaise date 2
		ATF::facturation()->u(array(
										"id_facturation"=>$id_facturation,
										"date_periode_debut"=>date("Y-m-d",strtotime(date("Y-m-01")." - 2 month"))
									)
								);
		$this->obj->facturationMensuelle(true);
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture3=ATF::facture()->sa();
		$this->assertNull($facture3,"Problème car une facture avec mauvaise date debut passe pas");

		//Ré-initialisation 1
		ATF::facturation()->u(array(
										"id_facturation"=>$id_facturation,
										"envoye"=>"non",
										"date_periode_debut"=>date("Y-m-d",strtotime(date("Y-m-01")." + 1 month"))
									)
								);
		$this->obj->facturationMensuelle(true);
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture4=ATF::facture()->sa();
		$this->assertNotNull($facture4,"Problème car une facture type ne passe pas 1");
		ATF::facture()->d($facture4["id_facture"]);

		//Envoye==oui
		ATF::facturation()->u(array(
										"id_facturation"=>$id_facturation,
										"envoye"=>"oui"
									)
								);
		$this->obj->facturationMensuelle(true);
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture5=ATF::facture()->sa();
		$this->assertNull($facture5,"Problème car une facturation envoye ne doit plus l'etre");

		//Ré-initialisation 2
		ATF::facturation()->u(array(
										"id_facturation"=>$id_facturation,
										"envoye"=>"non"
									)
								);
		$this->obj->facturationMensuelle(true);
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture6=ATF::facture()->sa();
		$this->assertNotNull($facture6,"Problème car une facture type ne passe pas 2");
		ATF::facture()->d($facture6["id_facture"]);

	}

	// @author Yann GAUTHERON <ygautheron@absystech.fr>
	public function test_facturationMensuelleContrat1b(){

		$id_societe=ATF::societe()->i(array("societe"=>"TU","code_client"=>"TU"));
		$id_affaire=ATF::affaire()->i(array("ref"=>"REFTu","id_societe"=>$id_societe,"affaire"=>"ATU"));
		$id_commande=ATF::commande()->i(array(
												"ref"=>"Ref tu",
												"id_societe"=>$id_societe,
												"id_user"=>$this->id_user,
												"tva"=>"1,196",
												"id_affaire"=>$id_affaire,
												"date_debut"=>date("Y-m-d",strtotime(date("Y-m-01")."-1 day")),
												"date_evolution"=>date("Y-m-d",strtotime(date("Y-m-01")."+1 day"))
											)
										);

		$id_facturation=ATF::facturation()->i(array(
													"id_affaire"=>$id_affaire,
													"id_societe"=>$id_societe,
													"montant"=>"100",
													"date_periode_debut"=>date("Y-m-d",strtotime(date("Y-m-01")." + 1 month")),
													"date_periode_fin"=>date("Y-m-d",strtotime(date("Y-m-01")." + 2 month"))
													)
												);



		//id_facture
		$id_facture1 = ATF::facture()->i(
			array(
				"id_affaire"=>$id_affaire
				,"id_societe"=>$id_societe
				,"tva"=>"1.196"
				,"id_user"=>$this->id_user
				,"date"=>"2010-01-01"
				,"type_facture"=>"facture"
				,"ref"=>"ref" //champs obligatoire
			)
		);
		ATF::facturation()->u(array(
										"id_facturation"=>$id_facturation,
										"id_facture"=>$id_facture1
									)
								);
		$this->obj->facturationMensuelle(true);
		ATF::facture()->d($id_facture1);
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture7=ATF::facture()->sa();
		$this->assertNull($facture7,"Problème car un on ne peut facturer une echéance de facturation qui a déjà été facturée");

		//Ré-initialisation 3
		ATF::facturation()->u(array(
										"id_facturation"=>$id_facturation,
										"id_facture"=>NULL
									)
								);
		$this->obj->facturationMensuelle(true);
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture8=ATF::facture()->sa();
		$this->assertNotNull($facture8,"Problème car une facture type ne passe pas 3");
		ATF::facture()->d($facture8["id_facture"]);

		//etat==perdu
		ATF::affaire()->u(array(
										"id_affaire"=>$id_affaire,
										"etat"=>"perdue"
									)
								);
		$this->obj->facturationMensuelle(true);
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture9=ATF::facture()->sa();
		$this->assertNull($facture9,"Problème car une facturation envoye ne doit pas sortir si affaire arreter");

		//Ré-initialisation 4
		ATF::affaire()->u(array(
										"id_affaire"=>$id_affaire,
										"etat"=>"devis"
									)
								);
		$this->obj->facturationMensuelle(true);
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture10=ATF::facture()->sa();
		$this->assertNotNull($facture10,"Problème car une facture type ne passe pas 4");
		ATF::facture()->d($facture10["id_facture"]);

		//etat==arreter
		ATF::commande()->u(array(
										"id_commande"=>$id_commande,
										"etat"=>"arreter"
									)
								);
		$this->obj->facturationMensuelle(true);
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture11=ATF::facture()->sa();
		$this->assertNull($facture11,"Problème car une facturation envoye ne doit pas sortir si commande arreter");


		//etat==AR
		ATF::commande()->u(array(
										"id_commande"=>$id_commande,
										"etat"=>"AR"
									)
								);
		$this->obj->facturationMensuelle(true);
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture12=ATF::facture()->sa();
		$this->assertNull($facture12,"Problème car une facturation envoye ne doit pas sortir si commande AR");

		//Ré-initialisation 5
		ATF::commande()->u(array(
										"id_commande"=>$id_commande,
										"etat"=>"mis_loyer"
									)
								);
		$this->obj->facturationMensuelle(true);
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture13=ATF::facture()->sa();
		$this->assertNotNull($facture13,"Problème car une facture type ne passe pas 5");
		ATF::facture()->d($facture13["id_facture"]);

		//nature==vente
		ATF::affaire()->u(array(
										"id_affaire"=>$id_affaire,
										"nature"=>"vente"
									)
								);
		$this->obj->facturationMensuelle(true);
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture14=ATF::facture()->sa();
		$this->assertNull($facture14,"Problème car une facturation envoye ne doit pas sortir si nature vente");

		//Ré-initialisation 6
		ATF::affaire()->u(array(
										"id_affaire"=>$id_affaire,
										"nature"=>"affaire"
									)
								);
		$re=$this->obj->facturationMensuelle(true);
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture15=ATF::facture()->sa();
		$this->assertNotNull($facture15,"Problème car une facture type ne passe pas 6");
		ATF::facture()->d($facture15["id_facture"]);
	}

	// @author Yann GAUTHERON <ygautheron@absystech.fr>
	public function test_facturationMensuelleContrat2(){

		$id_societe=ATF::societe()->i(array("societe"=>"TU","code_client"=>"TU"));
		$id_affaire=ATF::affaire()->i(array("ref"=>"REFTu","id_societe"=>$id_societe,"affaire"=>"ATU"));
		$id_commande=ATF::commande()->i(array(
												"ref"=>"Ref tu",
												"id_societe"=>$id_societe,
												"id_user"=>$this->id_user,
												"tva"=>"1,196",
												"id_affaire"=>$id_affaire,
												"date_debut"=>date("Y-m-d",strtotime(date("Y-m-01")."-1 day")),
												"date_evolution"=>date("Y-m-d",strtotime(date("Y-m-01")."+1 day"))
											)
										);

		$id_facturation=ATF::facturation()->i(array(
													"id_affaire"=>$id_affaire,
													"id_societe"=>$id_societe,
													"montant"=>"100",
													"date_periode_debut"=>date("Y-m-d",strtotime(date("Y-m-01")." + 1 month")),
													"date_periode_fin"=>date("Y-m-d",strtotime(date("Y-m-01")." + 2 month"))
													)
												);

		//Facture Refi
		$id_refinanceur=ATF::refinanceur()->i(array("refinanceur"=>"TU","code"=>"123","code_refi"=>"123"));
		$id_facture2 = ATF::facture()->i(
			array(
				"id_affaire"=>$id_affaire
				,"id_societe"=>$id_societe
				,"tva"=>"1.196"
				,"id_user"=>$this->id_user
				,"date"=>"2010-10-01"
				,"type_facture"=>"refi"
				,"id_refinanceur"=>$id_refinanceur
				,"ref"=>"ref" //champs obligatoire
			)
		);

		//Normal
		$this->obj->facturationMensuelle(true);
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture16=ATF::facture()->sa();
		$this->assertNull($facture16,"Problème car une facturation envoye ne doit pas sortir si facture refi");

		//Avec refinanceur REFACTURATION
		ATF::refinanceur()->u(array("id_refinanceur"=>$id_refinanceur,"code_refi"=>"REFACTURATION"));
		$this->obj->facturationMensuelle(true);
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture17=ATF::facture()->sa();
		$this->assertNotNull($facture17,"Problème car une facture refi avec un refinanceur FACTURATION doit être facturée");
		ATF::facture()->d($facture17["id_facture"]);
		ATF::refinanceur()->u(array("id_refinanceur"=>$id_refinanceur,"code_refi"=>"123"));

		//Avec demande de refi
		//Demande refi session > date
		$id_demande_refi=ATF::demande_refi()->i(array("id_contact"=>$this->id_contact,"date"=>date("Y-m-d"),"id_refinanceur"=>$id_refinanceur,"id_affaire"=>$id_affaire,"id_societe"=>$id_societe,"description"=>"TU","etat"=>"valide","date_cession"=>date("Y-m-d",strtotime("- 1 year"))));
		$this->obj->facturationMensuelle(true);
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture18=ATF::facture()->sa();
		$this->assertNull($facture18,"Problème car une facture refi avec une date de session inférieur à aujourd'hui ne doit pas se faire facturer");

		//Demande refi session < date
		ATF::demande_refi()->u(array("id_demande_refi"=>$id_demande_refi,"date_cession"=>date("Y-m-d",strtotime("+ 1 year"))));
		$this->obj->facturationMensuelle(true);
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture19=ATF::facture()->sa();
		$this->assertNotNull($facture19,"Problème car une facture refi avec une date de session supérieur à aujourd'hui doit  se faire facturer");
		ATF::facture()->d($facture19["id_facture"]);

		ATF::demande_refi()->u(array("id_demande_refi"=>$id_demande_refi,"etat"=>"en_attente"));
		ATF::demande_refi()->d($id_demande_refi);
		ATF::facture()->d($id_facture2);
		ATF::refinanceur()->d($id_refinanceur);

		$return1=$this->obj->facturationMensuelle(true);
		$this->assertEquals("pc",$return1["non_envoye"]["contratclient_non_envoyeSociete"]["TU".$id_facturation]["cause"],"La facturation ne doit pas êter envoyée car il n'y a pas de contact");
		$this->assertEquals(array(),$return1["facturer"],"Il n'y a pas de facture envoye 1");
		$this->assertNull($return1["non_envoye"]["prolongationclientSociete"],"Il n'y a pas de prolongation 1");
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture20=ATF::facture()->sa();
		ATF::facture()->d($facture20["id_facture"]);

		ATF::societe()->u(array("id_societe"=>$id_societe,"id_contact_facturation"=>$this->id_contact));
		ATF::contact()->u(array("id_contact"=>$this->id_contact,"email"=>NULL , "id_societe" => $id_societe));
		$return2=$this->obj->facturationMensuelle(true);
		$this->assertEquals("an",$return2["non_envoye"]["contratclient_non_envoyeSociete"]["TU".$id_facturation]["cause"],"La facturation ne doit pas êter envoyée car il n'y a pas de contact avec mail");
		$this->assertEquals(array(),$return2["facturer"],"Il n'y a pas de facture envoye 2");
		$this->assertNull($return2["non_envoye"]["prolongationclientSociete"],"Il n'y a pas de prolongation 2");
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture21=ATF::facture()->sa();
		ATF::facture()->d($facture21["id_facture"]);

		ATF::facture()->u(array("id_facture" => $id_facture2 , "type_facture" => "libre"));
		ATF::contact()->u(array("id_contact"=>$this->id_contact,"email"=>"bidon@absystech.fr"));
		$return3=$this->obj->facturationMensuelle(true);
		$facturation=$this->obj->select($id_facturation);
		$facturation["ltrimsociete"]="TU";
		$facturation["ltrimcode_client"]="TU";
		$facturation["email"]="bidon@absystech.fr";

		$this->assertEquals($facturation,$return3["facturer"]["contratclientSociete"]["TU".$id_facturation],"La facturation doit être envoyée");
		$this->assertEquals(array(),$return3["non_envoye"],"Il n'y a pas de facture non_envoye 1");
		$this->assertNull($return3["facturer"]["prolongationclientSociete"],"Il n'y a pas de prolongation 3");
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture22=ATF::facture()->sa();
		ATF::facture()->d($facture22["id_facture"]);

		$this->obj->u(array("id_facturation"=>$id_facturation,"type"=>"prolongation","envoye"=>'non',"id_facture"=>NULL));
		$return4=$this->obj->facturationMensuelle(true);
		$facturation=$this->obj->select($id_facturation);
		$facturation["ltrimsociete"]="TU";
		$facturation["ltrimcode_client"]="TU";
		$facturation["email"]="bidon@absystech.fr";
		$this->assertEquals(NULL,$return4["facturer"]["prolongationclientSociete"]["TU".$id_facturation],"La prolongation ne doit pas être envoyée");
		//$this->assertEquals(array(),$return4["non_envoye"],"Il n'y a pas de facture non_envoye 2");
		$this->assertNull($return4["facturer"]["contratclientSociete"],"Il n'y a pas de facture");
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture23=ATF::facture()->sa();

		$this->obj->u(array("id_facturation"=>$id_facturation,"type"=>"contrat","envoye"=>'non',"id_facture"=>NULL));
		$return5=$this->obj->facturationMensuelle(true);
		$this->assertEquals("pi",$return5["non_envoye"]["contratclient_non_envoyeSociete"]["TU".$id_facturation]["cause"],"La facturation ne doit pas êter envoyée car il existe déjà une facture pour cette date");
		$this->assertEquals(array(),$return5["facturer"],"Il n'y a pas de facture envoye 3");
		$this->assertNull($return5["facturer"]["prolongationclientSociete"],"Il n'y a pas de prolongation 4");
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture24=ATF::facture()->sa();
		ATF::facture()->d($facture23["id_facture"]);

		$this->obj->u(array("id_facturation"=>$id_facturation,"type"=>"contrat","envoye"=>'non',"id_facture"=>NULL,"montant"=>"0.00"));
		$return6=$this->obj->facturationMensuelle(true);
		$this->assertEquals(array(),$return6["non_envoye"],"Il n'y a pas de facture non envoye car montant 0");
		$this->assertEquals(array(),$return6["facturer"],"Il n'y a pas de facture car montant 0");

	}

	// @author Yann GAUTHERON <ygautheron@absystech.fr>
	public function test_facturationMensuelleProlongation(){

		$id_societe=ATF::societe()->i(array("societe"=>"TU","code_client"=>"TU"));
		$id_affaire=ATF::affaire()->i(array("ref"=>"REFTu","id_societe"=>$id_societe,"affaire"=>"ATU"));
		$id_loyer=ATF::loyer()->i(array("id_affaire"=>$id_affaire,"loyer"=>100,"duree"=>10));
		$id_commande=ATF::commande()->i(array(
												"ref"=>"Ref tu",
												"id_societe"=>$id_societe,
												"id_user"=>$this->id_user,
												"tva"=>"1,196",
												"id_affaire"=>$id_affaire,
												"date_debut"=>date("Y-m-d",strtotime(date("Y-m-01")."-2 year")),
												"date_evolution"=>date("Y-m-d",strtotime(date("Y-m-01")."-1 year"))
											)
										);



		$return1=$this->obj->facturationMensuelle(true);
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture1=ATF::facture()->sa();
		$this->assertNotNull($facture1,"Problème car une facture type ne passe pas");
		ATF::facture()->d($facture1["id_facture"]);

		//etat==perdu
		ATF::affaire()->u(array(
										"id_affaire"=>$id_affaire,
										"etat"=>"perdue"
									)
								);
		$this->obj->facturationMensuelle(true);
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture9=ATF::facture()->sa();
		$this->assertNull($facture9,"Problème car une facturation envoye ne doit pas sortir si affaire arreter");

		//etat==arreter
		ATF::commande()->u(array(
										"id_commande"=>$id_commande,
										"etat"=>"arreter"
									)
								);
		$this->obj->facturationMensuelle(true);
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture11=ATF::facture()->sa();
		$this->assertNull($facture11,"Problème car une facturation envoye ne doit pas sortir si commande arreter");


		//etat==AR
		ATF::commande()->u(array(
										"id_commande"=>$id_commande,
										"etat"=>"AR"
									)
								);
		$this->obj->facturationMensuelle(true);
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture12=ATF::facture()->sa();
		$this->assertNull($facture12,"Problème car une facturation envoye ne doit pas sortir si commande AR");

		//nature==vente
		ATF::affaire()->u(array(
										"id_affaire"=>$id_affaire,
										"nature"=>"vente"
									)
								);
		$this->obj->facturationMensuelle(true);
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture14=ATF::facture()->sa();
		$this->assertNull($facture14,"Problème car une facturation envoye ne doit pas sortir si nature vente");

		ATF::commande()->u(array(
										"id_commande"=>$id_commande,
										"etat"=>"mis_loyer"
									)
								);
		ATF::affaire()->u(array(
										"id_affaire"=>$id_affaire,
										"nature"=>"affaire",
										"etat"=>"devis"
									)
								);

		$return1=$this->obj->facturationMensuelle(true);
		ATF::facturation()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addOrder("id_facturation");
		$facturation=ATF::facturation()->sa();
		ATF::facturation()->d($facturation["id_facturation"]);

		$this->assertEquals("pc",$return1["non_envoye"]["prolongationclient_non_envoyeSociete"]["TU".$facturation["id_facturation"]]["cause"],"La facturation ne doit pas êter envoyée car il n'y a pas de contact");
		$this->assertEquals(array(),$return1["facturer"],"Il n'y a pas de facture envoye 1");
		$this->assertNull($return1["facturer"]["contratclient_non_envoyeSociete"],"Il n'y a pas de contrat 1");
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture20=ATF::facture()->sa();
		ATF::facture()->d($facture20["id_facture"]);
		ATF::facturation()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addOrder("id_facturation");
		$facturation=ATF::facturation()->sa();
		ATF::facturation()->d($facturation["id_facturation"]);
		ATF::societe()->u(array("id_societe"=>$id_societe,"id_contact_facturation"=>$this->id_contact));
		ATF::contact()->u(array("id_contact"=>$this->id_contact,"email"=>NULL));

		$return2=$this->obj->facturationMensuelle(true);
		ATF::facturation()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addOrder("id_facturation");
		$facturation=ATF::facturation()->sa();
		$this->assertEquals("pc",$return2["non_envoye"]["prolongationclient_non_envoyeSociete"]["TU".$facturation["id_facturation"]]["cause"],"La facturation ne doit pas êter envoyée car il n'y a pas de contact avec mail");
		$this->assertEquals(array(),$return2["facturer"],"Il n'y a pas de facture envoye 2");
		$this->assertNull($return2["facturer"]["contratclient_non_envoyeSociete"],"Il n'y a pas de contrat 2");
		ATF::facturation()->d($facturation["id_facturation"]);
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture21=ATF::facture()->sa();
		ATF::facture()->d($facture21["id_facture"]);

		ATF::contact()->u(array("id_contact"=>$this->id_contact,"email"=>"bidon@absystech.fr"));
		$return3=$this->obj->facturationMensuelle(true);
		$this->assertNull($return3["facturer"]["contratclientSociete"],"Il n'y a pas de prolongation 3");
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		ATF::facturation()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addOrder("id_facturation");
		$facturation=ATF::facturation()->sa();
		$facturation["ltrimsociete"]="TU";
		$facturation["ltrimcode_client"]="TU";
		$facturation["email"]="bidon@absystech.fr";
		//$this->assertEquals($facturation,$return3["facturer"]["prolongationclientSociete"]["TU".$facturation["id_facturation"]],"La facturation doit être envoyée");
		//$this->assertEquals(array(),$return3["non_envoye"],"Il n'y a pas de facture non_envoye 4");
		ATF::facturation()->d($facturation["id_facturation"]);
		$facture22=ATF::facture()->sa();
		ATF::facture()->d($facture22["id_facture"]);

		ATF::loyer()->u(array("id_loyer"=>$id_loyer,"loyer"=>"0"));
		$return6=$this->obj->facturationMensuelle(true);
		$this->assertEquals(array(),$return6["non_envoye"],"Il n'y a pas de facture non envoye car montant 0");
		$this->assertEquals(array(),$return6["facturer"],"Il n'y a pas de facture car montant 0");

		ATF::loyer()->u(array("id_loyer"=>$id_loyer,"loyer"=>"100"));
		$return6=$this->obj->facturationMensuelle(true);
	}

	/*public function test_facturationMensuelleRestitutionDansContrat(){

		$id_societe=ATF::societe()->i(array("societe"=>"TU","code_client"=>"TU"));
		$id_affaire=ATF::affaire()->i(array("ref"=>"REFTu","id_societe"=>$id_societe,"affaire"=>"ATU"));
		$id_commande=ATF::commande()->i(array(
												"ref"=>"Ref tu",
												"id_societe"=>$id_societe,
												"id_user"=>$this->id_user,
												"tva"=>"1,196",
												"id_affaire"=>$id_affaire,
												"etat"=> "restitution",
												"date_debut"=>date("Y-m-d",strtotime(date("Y-m-01")."-1 day")),
												"date_evolution"=>date("Y-m-d",strtotime(date("Y-m-01")."+1 day")),
												"date_prevision_restitution"=>date("Y-m-d",strtotime(date("Y-m-01")."+2 day"))
											)
										);

		$id_facturation=ATF::facturation()->i(array(
													"id_affaire"=>$id_affaire,
													"id_societe"=>$id_societe,
													"montant"=>"100",
													"date_periode_debut"=>date("Y-m-d",strtotime(date("Y-m-01")." + 1 month")),
													"date_periode_fin"=>date("Y-m-d",strtotime(date("Y-m-01")." + 2 month"))
													)
												);


		$this->obj->facturationMensuelle(true);
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture1=ATF::facture()->sa();
		//$this->assertNotNull($facture1,"Problème car une facture type ne passe pas");
		ATF::facture()->d($facture1["id_facture"]);

		//Mauvaise date 1
		ATF::facturation()->u(array(
										"id_facturation"=>$id_facturation,
										"envoye"=>"non",
										"date_periode_debut"=>date("Y-m-d",strtotime(date("Y-m-01")." + 2 month"))
									)
								);
		$this->obj->facturationMensuelle(true);
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture2=ATF::facture()->sa();
		$this->assertNull($facture2,"Problème car une facture avec mauvaise date debut passe pas");

		//Mauvaise date 2
		ATF::facturation()->u(array(
										"id_facturation"=>$id_facturation,
										"date_periode_debut"=>date("Y-m-d",strtotime(date("Y-m-01")." - 2 month"))
									)
								);
		$this->obj->facturationMensuelle(true);
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture3=ATF::facture()->sa();
		$this->assertNull($facture3,"Problème car une facture avec mauvaise date debut passe pas");

		//Ré-initialisation 1
		ATF::facturation()->u(array(
										"id_facturation"=>$id_facturation,
										"envoye"=>"non",
										"date_periode_debut"=>date("Y-m-d",strtotime(date("Y-m-01")." + 1 month"))
									)
								);
		$this->obj->facturationMensuelle(true);
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture4=ATF::facture()->sa();
		//$this->assertNotNull($facture4,"Problème car une facture type ne passe pas 1");
		ATF::facture()->d($facture4["id_facture"]);

		//Envoye==oui
		ATF::facturation()->u(array(
										"id_facturation"=>$id_facturation,
										"envoye"=>"oui"
									)
								);
		$this->obj->facturationMensuelle(true);
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture5=ATF::facture()->sa();
		//$this->assertNull($facture5,"Problème car une facturation envoye ne doit plus l'etre");

		//Ré-initialisation 2
		ATF::facturation()->u(array(
										"id_facturation"=>$id_facturation,
										"envoye"=>"non"
									)
								);
		$this->obj->facturationMensuelle(true);
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture6=ATF::facture()->sa();
		//$this->assertNotNull($facture6,"Problème car une facture type ne passe pas 2");
		ATF::facture()->d($facture6["id_facture"]);



	/*
		$id_societe=ATF::societe()->i(array("societe"=>"TU","code_client"=>"TU"));
		$id_affaire=ATF::affaire()->i(array("ref"=>"REFTu","id_societe"=>$id_societe,"affaire"=>"ATU"));
		$id_commande=ATF::commande()->i(array(
												"ref"=>"Ref tu",
												"etat"=>"restitution",
												"id_societe"=>$id_societe,
												"id_user"=>$this->id_user,
												"tva"=>"1,196",
												"id_affaire"=>$id_affaire,
												"date_debut"=>date("Y-m-d",strtotime(date("Y-m-01")."-1 day")),
												"date_evolution"=>date("Y-m-d",strtotime(date("Y-m-01")."+1 day"))
											)
										);

		$id_facturation=ATF::facturation()->i(array(
													"id_affaire"=>$id_affaire,
													"id_societe"=>$id_societe,
													"montant"=>"100",
													"date_periode_debut"=>date("Y-m-d",strtotime(date("Y-m-01")." + 1 month")),
													"date_periode_fin"=>date("Y-m-d",strtotime(date("Y-m-01")." + 2 month"))
													)
												);

		//Facture Refi
		$id_refinanceur=ATF::refinanceur()->i(array("refinanceur"=>"TU","code"=>"123","code_refi"=>"123"));
		$id_facture2 = ATF::facture()->i(
			array(
				"id_affaire"=>$id_affaire
				,"id_societe"=>$id_societe
				,"tva"=>"1.196"
				,"id_user"=>$this->id_user
				,"date"=>"2010-10-01"
				,"type_facture"=>"refi"
				,"id_refinanceur"=>$id_refinanceur
				,"ref"=>"ref" //champs obligatoire
			)
		);

		//Normal
		$id_demande_refi=ATF::demande_refi()->i(array("id_contact"=>$this->id_contact,"date"=>date("Y-m-d"),"id_refinanceur"=>$id_refinanceur,"id_affaire"=>$id_affaire,"id_societe"=>$id_societe,"description"=>"TU","etat"=>"valide","date_cession"=>date("Y-m-d",strtotime("- 1 year"))));
		$this->obj->facturationMensuelle(true);
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture16=ATF::facture()->sa();
		//$this->assertNull($facture16,"Problème car une facturation envoye ne doit pas sortir si facture refi");

		//Avec refinanceur REFACTURATION
		ATF::refinanceur()->u(array("id_refinanceur"=>$id_refinanceur,"code_refi"=>"REFACTURATION"));
		$this->obj->facturationMensuelleRestitution(true);
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture17=ATF::facture()->sa();
		//$this->assertNotNull($facture17,"Problème car une facture refi avec un refinanceur FACTURATION doit être facturée");
		ATF::facture()->d($facture17["id_facture"]);
		ATF::refinanceur()->u(array("id_refinanceur"=>$id_refinanceur,"code_refi"=>"123"));

		//Avec demande de refi
		//Demande refi session < date
		ATF::demande_refi()->u(array("id_demande_refi"=>$id_demande_refi,"date_cession"=>date("Y-m-d",strtotime("+ 1 year"))));
		$this->obj->facturationMensuelleRestitution(true);
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture19=ATF::facture()->sa();
		//$this->assertNotNull($facture19,"Problème car une facture refi avec une date de session supérieur à aujourd'hui doit  se faire facturer");
		ATF::facture()->d($facture19["id_facture"]);

		ATF::demande_refi()->u(array("id_demande_refi"=>$id_demande_refi,"etat"=>"en_attente"));
		ATF::demande_refi()->d($id_demande_refi);
		ATF::facture()->d($id_facture2);
		ATF::refinanceur()->d($id_refinanceur);

		ATF::societe()->u(array("id_societe"=>$id_societe,"id_contact_facturation"=>$this->id_contact));
		ATF::contact()->u(array("id_contact"=>$this->id_contact,"email"=>NULL , "id_societe" => 1854));
		$return1=$this->obj->facturationMensuelleRestitution(true);
		//$this->assertEquals("pc",$return1["non_envoye"]["contratclient_non_envoyeSociete"]["TU".$id_facturation]["cause"],"La facturation ne doit pas êter envoyée car il n'y a pas de contact");
		//$this->assertEquals(array(),$return1["facturer"],"Il n'y a pas de facture envoye 1");
		//$this->assertNull($return1["non_envoye"]["prolongationclientSociete"],"Il n'y a pas de prolongation 1");
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture20=ATF::facture()->sa();
		ATF::facture()->d($facture20["id_facture"]);

		ATF::societe()->u(array("id_societe"=>$id_societe,"id_contact_facturation"=>$this->id_contact));
		ATF::contact()->u(array("id_contact"=>$this->id_contact,"email"=>NULL , "id_societe" => $id_societe));
		$return2=$this->obj->facturationMensuelleRestitution(true);
		//$this->assertEquals("an",$return2["non_envoye"]["contratclient_non_envoyeSociete"]["TU".$id_facturation]["cause"],"La facturation ne doit pas êter envoyée car il n'y a pas de contact avec mail");
		//$this->assertEquals(array(),$return2["facturer"],"Il n'y a pas de facture envoye 2");
		//$this->assertNull($return2["non_envoye"]["prolongationclientSociete"],"Il n'y a pas de prolongation 2");
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture21=ATF::facture()->sa();
		ATF::facture()->d($facture21["id_facture"]);

		ATF::facture()->u(array("id_facture" => $id_facture2 , "type_facture" => "libre"));
		ATF::contact()->u(array("id_contact"=>$this->id_contact,"email"=>"bidon@absystech.fr"));
		$return3=$this->obj->facturationMensuelleRestitution(true);
		$facturation=$this->obj->select($id_facturation);
		$facturation["ltrimsociete"]="TU";
		$facturation["ltrimcode_client"]="TU";
		$facturation["email"]="bidon@absystech.fr";

		//$this->assertEquals($facturation,$return3["facturer"]["contratclientSociete"]["TU".$id_facturation],"La facturation doit être envoyée");
		//$this->assertEquals(array(),$return3["non_envoye"],"Il n'y a pas de facture non_envoye 1");
		//$this->assertNull($return3["facturer"]["prolongationclientSociete"],"Il n'y a pas de prolongation 3");
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture22=ATF::facture()->sa();
		ATF::facture()->d($facture22["id_facture"]);

		$this->obj->u(array("id_facturation"=>$id_facturation,"type"=>"prolongation","envoye"=>'non',"id_facture"=>NULL));
		$return4=$this->obj->facturationMensuelleRestitution(true);
		$facturation=$this->obj->select($id_facturation);
		$facturation["ltrimsociete"]="TU";
		$facturation["ltrimcode_client"]="TU";
		$facturation["email"]="bidon@absystech.fr";
		//$this->assertEquals($facturation,$return4["facturer"]["prolongationclientSociete"]["TU".$id_facturation],"La prolongation doit être envoyée");
		//$this->assertEquals(array(),$return4["non_envoye"],"Il n'y a pas de facture non_envoye 2");
		//$this->assertNull($return4["facturer"]["contratclientSociete"],"Il n'y a pas de facture");
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture23=ATF::facture()->sa();

		$this->obj->u(array("id_facturation"=>$id_facturation,"type"=>"contrat","envoye"=>'non',"id_facture"=>NULL));
		$return5=$this->obj->facturationMensuelleRestitution(true);
		$this->assertEquals("pi",$return5["non_envoye"]["contratclient_non_envoyeSociete"]["TU".$id_facturation]["cause"],"La facturation ne doit pas êter envoyée car il existe déjà une facture pour cette date");
		$this->assertEquals(array(),$return5["facturer"],"Il n'y a pas de facture envoye 3");
		$this->assertNull($return5["facturer"]["prolongationclientSociete"],"Il n'y a pas de prolongation 4");
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture24=ATF::facture()->sa();
		ATF::facture()->d($facture23["id_facture"]);

		$this->obj->u(array("id_facturation"=>$id_facturation,"type"=>"contrat","envoye"=>'non',"id_facture"=>NULL,"montant"=>"0.00"));
		$return6=$this->obj->facturationMensuelleRestitution(true);
		//$this->assertEquals(array(),$return6["non_envoye"],"Il n'y a pas de facture non envoye car montant 0");
		//$this->assertEquals(array(),$return6["facturer"],"Il n'y a pas de facture car montant 0");

	}*/

	public function test_facturationMensuelleRestitution(){

		$id_societe=ATF::societe()->i(array("societe"=>"TU","code_client"=>"TU"));
		$id_affaire=ATF::affaire()->i(array("ref"=>"REFTu","id_societe"=>$id_societe,"affaire"=>"ATU","nature"=>"affaire","etat"=>"devis"));
		$id_loyer=ATF::loyer()->i(array("id_affaire"=>$id_affaire,"loyer"=>100,"duree"=>10));
		$id_commande=ATF::commande()->i(array(
												"ref"=>"Ref tu",
												"etat"=>"restitution",
												"id_societe"=>$id_societe,
												"id_user"=>$this->id_user,
												"tva"=>"1,196",
												"id_affaire"=>$id_affaire,
												"date_debut"=>date("Y-m-d",strtotime(date("Y-m-01")."-2 year")),
												"date_evolution"=>date("Y-m-d",strtotime(date("Y-m-01")."-1 year")),
												"date_prevision_restitution" =>date("Y-m-d",strtotime(date("Y-m-01")."-6 month"))
											)
										);
		$id_facturation=ATF::facturation()->i(array(
													"id_affaire"=>$id_affaire,
													"id_societe"=>$id_societe,
													"montant"=>"100",
													"date_periode_debut"=>date("Y-m-01",strtotime(date("Y-m-01")." -2 month")),
													"date_periode_fin"=>date("Y-m-01",strtotime(date("Y-m-01")." -1 month -1day"))
													)
												);

		$return6=$this->obj->facturationMensuelleRestitution(true);

		ATF::facturation()->q->reset()->where("id_facture", $return6["facture_prolongation"]["prolongationCode"]["TU"]);
		$res = ATF::facturation()->select_row();
		$id_facturation = $res["id_facturation"];
		$this->assertEquals("pc",$return6["non_envoye"]["prolongationclient_non_envoyeSociete"]["TU".$id_facturation]["cause"],"1 - La facturation ne doit pas êter envoyée car il n'y a pas de contact");


		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture20=ATF::facture()->sa();
		ATF::facture()->d($facture20["id_facture"]);
		ATF::facturation()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addOrder("id_facturation");
		$facturation=ATF::facturation()->sa();
		ATF::facturation()->d($facturation["id_facturation"]);
		ATF::societe()->u(array("id_societe"=>$id_societe,"id_contact_facturation"=>$this->id_contact));
		ATF::contact()->u(array("id_contact"=>$this->id_contact,"email"=>NULL,"id_societe" => $id_societe, "nom" => "Toto"));


		$return7=$this->obj->facturationMensuelleRestitution(true);

		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture20=ATF::facture()->sa();
		ATF::facture()->d($facture20["id_facture"]);
		ATF::facturation()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addOrder("id_facturation");
		$facturation=ATF::facturation()->sa();
		ATF::facturation()->d($facturation["id_facturation"]);

		ATF::societe()->u(array("id_societe"=>$id_societe,"id_contact_facturation"=>$this->id_contact));
		ATF::contact()->u(array("id_contact"=>$this->id_contact,"email"=>"tu@absystech.net","id_societe" => $id_societe, "nom" => "Toto"));
		$this->obj->u(array("id_facturation"=>$id_facturation,"type"=>"prolongation","envoye"=>'non',"id_facture"=>NULL));
		$return8=$this->obj->facturationMensuelleRestitution(true);

		ATF::contact()->u(array("id_contact"=>$this->id_contact,"email"=>NULL,"id_societe" => $id_societe, "nom" => "Toto"));
		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture20=ATF::facture()->sa();
		ATF::facture()->d($facture20["id_facture"]);
		ATF::facturation()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addOrder("id_facturation");
		$facturation=ATF::facturation()->sa();
		ATF::facturation()->d($facturation["id_facturation"]);
		$return11=$this->obj->facturationMensuelleRestitution(true);

		ATF::facture()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addCondition("type_facture","facture");
		$facture20=ATF::facture()->sa();
		ATF::facture()->d($facture20["id_facture"]);
		ATF::facturation()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row")->addOrder("id_facturation");
		$facturation=ATF::facturation()->sa();
		ATF::facturation()->d($facturation["id_facturation"]);
		ATF::loyer()->u(array("id_loyer"=>$id_loyer,"loyer"=>"0"));

		$return9=$this->obj->facturationMensuelleRestitution(true);



	}

}
?>