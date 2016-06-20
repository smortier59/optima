<?
/**
* Classe de test sur le module societe_cleodis
*/
class parc_cleodis_test extends ATF_PHPUnit_Framework_TestCase {
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
	
	public function testUpdateExistenz(){
		$id_affaire=ATF::affaire()->decryptId(ATF::affaire()->i(array("etat"=>"commande","date"=>date("Y-m-d"),"ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu","nature"=>"affaire")));
		$id_devis=ATF::devis()->decryptId(ATF::devis()->i(array("ref"=>"refTu","id_user"=>$this->id_user,"id_societe"=>$this->id_societe,"id_filiale"=>246,"prix"=>600,"date"=>date("Y-m-d"),"devis"=>"AffaireTu","type_contrat"=>"lld","date_accord"=>date("Y-m-d"),"etat"=>"gagne","id_contact"=>$this->id_contact,"id_affaire"=>$id_affaire,"tva"=>"1.196","loyer_unique"=>"non","prix_achat"=>0,"validite"=>date("Y-m-d"))));
		$id_devis_ligne=ATF::devis_ligne()->i(array("type"=>"portable","id_devis"=>$id_devis,"id_produit"=>6,"ref"=>"GPAC-GPS","produit"=>"Ordinateur TOM TOM GO500","quantite"=>2,"id_fournisseur"=>5385,"prix_achat"=>"0","visible"=>"oui","visibilite_prix"=>"visible"));
		$id_commande=ATF::commande()->decryptId(ATF::commande()->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"commande"=>"AffaireTu","prix_achat"=>0,"prix"=>600,"date"=>date("Y-m-d"),"id_devis"=>$id_devis,"etat"=>"mis_loyer","id_user"=>$this->id_user,"tva"=>"1.196","clause_logicielle"=>"non","date_debut"=>date("Y-m-d"),"type"=>"prelevement","id_affaire"=>$id_affaire)));

		$id_parc1=$this->obj->i(array(
								"id_societe"=>$this->id_societe,
								"id_affaire"=>$id_affaire,
								"ref"=>"GPAC-GPS",
								"libelle"=>"Ordinateur TOM TOM GO500",
								"serial"=>"00000000000",
								"etat"=>"loue",
								"existence"=>"inactif"
							)
					);

		$id_parc2=$this->obj->i(array(
								"id_societe"=>$this->id_societe,
								"id_affaire"=>$id_affaire,
								"ref"=>"GPAC-GPS1",
								"libelle"=>"Ordinateur TOM TOM GO5001",
								"serial"=>"111111111111",
								"etat"=>"broke",
								"date_inactif"=>"2010-01-01",
								"existence"=>"actif"
							)
					);
		
		$affaire = new affaire_cleodis($id_affaire);
		$commande = new commande_cleodis($id_commande);	
		$this->obj->updateExistenz($commande,$affaire);

		$parc1=$this->obj->select($id_parc1);
		$this->assertEquals("actif",$parc1["existence"],'Le parc n est pas passé en actif');
		$this->assertNull($parc1["date_inactif"],'1 la date inactif n est pas passée en Null');

		$parc2=$this->obj->select($id_parc2);
		$this->assertEquals("inactif",$parc2["existence"],'2 Le parc broke doit passé en inactif');

		ATF::commande()->u(array("id_commande"=>$id_commande,"etat"=>"non_loyer"));
		$commande = new commande_cleodis($id_commande);	
		$this->obj->updateExistenz($commande,$affaire);

		$parc1=$this->obj->select($id_parc1);
		$this->assertEquals("inactif",$parc1["existence"],'3 Le parc n est pas passé en inactif');

		$parc2=$this->obj->select($id_parc2);
		$this->assertEquals("inactif",$parc2["existence"],'4 Le parc n est pas passé en inactif');

		//Avenant
		$id_affaire2=ATF::affaire()->decryptId(ATF::affaire()->i(array("etat"=>"commande","date"=>date("Y-m-d"),"ref"=>"refTuAv","id_societe"=>$this->id_societe,"affaire"=>"AffaireTuAv","nature"=>"avenant","id_parent"=>$id_affaire)));
		$id_commande2=ATF::commande()->decryptId(ATF::commande()->i(array("ref"=>"refTuAv","id_societe"=>$this->id_societe,"commande"=>"AffaireTuAv","prix_achat"=>0,"prix"=>600,"date"=>date("Y-m-d"),"id_devis"=>NULL,"etat"=>"non_loyer","id_user"=>$this->id_user,"tva"=>"1.196","clause_logicielle"=>"non","date_debut"=>date("Y-m-d"),"type"=>"prelevement","id_affaire"=>$id_affaire2)));
		ATF::commande()->u(array("id_commande"=>$id_commande,"etat"=>"mis_loyer"));

		$this->obj->u(array("id_parc"=>$id_parc1,"etat"=>"loue","existence"=>"inactif","date_inactif"=>date("Y-m-d"),"date"=>date("Y-m-d",strtotime(date("Y-m-d")."- 1 day"))));
		$this->obj->u(array("id_parc"=>$id_parc2,"etat"=>"loue","existence"=>"inactif","date_inactif"=>date("Y-m-d"),"date"=>date("Y-m-d",strtotime(date("Y-m-d")."- 1 day"))));
		
		$id_parc3=$this->obj->i(array(
								"id_societe"=>$this->id_societe,
								"id_affaire"=>$id_affaire2,
								"ref"=>"GPAC-GPS",
								"libelle"=>"Ordinateur TOM TOM GO500",
								"serial"=>"00000000000",
								"etat"=>"broke",
								"existence"=>"actif",
								"provenance"=>$id_affaire,
								"date"=>date("Y-m-d")
							)
					);

		$id_parc4=$this->obj->i(array(
								"id_societe"=>$this->id_societe,
								"id_affaire"=>$id_affaire2,
								"ref"=>"GPAC-GPS1",
								"libelle"=>"Ordinateur TOM TOM GO5001",
								"serial"=>"111111111111",
								"etat"=>"broke",
								"existence"=>"actif",
								"provenance"=>$id_affaire,
								"date"=>date("Y-m-d")
							)
					);

		
		$affaireAv = new affaire_cleodis($id_affaire2);
		$commandeAv = new commande_cleodis($id_commande2);	
		$affaire_parente = $affaireAv->getParentAvenant();
		$commande_parent = new commande_cleodis($id_commande);	

		$this->obj->updateExistenz($commandeAv,$affaireAv,$affaire_parente);

		$parc1=$this->obj->select($id_parc1);
		$this->assertEquals("actif",$parc1["existence"],'Le parc1 avenant n est pas passé en actif');
		$this->assertNull($parc1["date_inactif"],'la date parc1 avenant inactif n est pas passée en Null');

		$parc2=$this->obj->select($id_parc2);
		$this->assertEquals("actif",$parc2["existence"],'Le parc2 avenant n est pas passé en actif');
		$this->assertNull($parc2["date_inactif"],'la date parc2 avenant inactif n est pas passée en Null');

		$parc3=$this->obj->select($id_parc3);
		$this->assertEquals("inactif",$parc3["existence"],'Le parc3 avenant n est pas passé en inactif');
		$this->assertNull($parc3["date_inactif"],'la date parc3 avenant inactif n est pas activé');

		$parc4=$this->obj->select($id_parc4);
		$this->assertEquals("inactif",$parc4["existence"],'Le parc4 avenant n est pas passé en inactif');
		$this->assertNull($parc4["date_inactif"],'la date parc4 avenant inactif n est pas activé');

		ATF::commande()->u(array("id_commande"=>$id_commande2,"etat"=>"mis_loyer"));
		
		$commandeAv = new commande_cleodis($id_commande2);	
		$this->obj->updateExistenz($commandeAv,$affaireAv,$affaire_parente);

		$parc1=$this->obj->select($id_parc1);
		$this->assertEquals("inactif",$parc1["existence"],'Le parc1 avenant n est pas passé en inactif');
		$this->assertEquals($commandeAv->get("date_debut"),$parc1["date_inactif"],'la date_inactif parc1 avenant inactif n est pas bonne');

		$parc2=$this->obj->select($id_parc2);
		$this->assertEquals("inactif",$parc2["existence"],'Le parc2 avenant n est pas passé en inactif');
		$this->assertEquals($commandeAv->get("date_debut"),$parc2["date_inactif"],'la date_inactif parc2 avenant inactif n est pas bonne');

		$parc3=$this->obj->select($id_parc3);
		$this->assertEquals("actif",$parc3["existence"],'Le parc parc3 avenant n est pas passé en actif');
		$this->assertNull($parc3["date_inactif"],'la date parc3 avenant inactif n est pas passée en Null');

		$parc4=$this->obj->select($id_parc4);
		$this->assertEquals("actif",$parc4["existence"],'Le parc parc4 avenant n est pas passé en actif');
		$this->assertNull($parc4["date_inactif"],'la date parc4 avenant inactif n est pas passée en Null');
		
		//AR
		ATF::affaire()->u(array("id_affaire"=>$id_affaire,"nature"=>"affaire","id_fille"=>$id_affaire2,"id_parent"=>NULL));
		ATF::affaire()->u(array("id_affaire"=>$id_affaire2,"nature"=>"AR","id_parent"=>NULL));
		ATF::commande()->u(array("id_commande"=>$id_commande,"etat"=>"mis_loyer"));
		ATF::commande()->u(array("id_commande"=>$id_commande2,"etat"=>"non_loyer"));

		$this->obj->u(array("id_parc"=>$id_parc3,"etat"=>"reloue"));
		$id_parc5=$this->obj->i(array(
								"id_societe"=>$this->id_societe,
								"id_affaire"=>$id_affaire,
								"ref"=>"GPAC-GPS",
								"libelle"=>"Ordinateur TOM TOM GO500",
								"serial"=>"00000000000",
								"etat"=>"broke",
								"existence"=>"actif",
								"date"=>date("Y-m-d")
							)
					);

		$this->obj->d($id_parc4);

		$affaireAR = new affaire_cleodis($id_affaire2);
		$commandeAR = new commande_cleodis($id_commande2);	
		$ap = $affaireAR->getParentAR();
		foreach ($ap as $a) {
			$affaires_parentes[] = new affaire_cleodis($a["id_affaire"]);
		}

		$this->obj->updateExistenz($commandeAR,$affaireAR,NULL,$affaires_parentes);

		$parc1=$this->obj->select($id_parc1);
		$this->assertEquals("actif",$parc1["existence"],'Le parc1 AR n est pas passé en actif');
		$this->assertNull($parc1["date_inactif"],'la date_inactif parc1 AR inactif n est pas NULL');

		$parc2=$this->obj->select($id_parc2);
		$this->assertEquals("actif",$parc2["existence"],'Le parc2 avenant n est pas passé en actif');
		$this->assertNull($parc2["date_inactif"],'la date_inactif parc2 AR inactif n est pas NULL');

		$parc3=$this->obj->select($id_parc3);
		$this->assertEquals("inactif",$parc3["existence"],'Le parc3 AR n est pas passé en inactif');
		$this->assertNull($parc2["date_inactif"],'la date_inactif parc3 AR inactif n est pas NULL');

		$parc5=$this->obj->select($id_parc5);
		$this->assertEquals("inactif",$parc5["existence"],'Le parc5 AR n est pas passé en inactif');
		$this->assertNull($parc5["date_inactif"],'la date_inactif parc5 AR inactif n est pas NULL');


		ATF::commande()->u(array("id_commande"=>$id_commande,"etat"=>"AR"));
		ATF::commande()->u(array("id_commande"=>$id_commande2,"etat"=>"mis_loyer"));

		$commandeAR = new commande_cleodis($id_commande2);	
		$this->obj->updateExistenz($commandeAR,$affaireAR,NULL,$affaires_parentes);

		$parc1=$this->obj->select($id_parc1);
		$this->assertEquals("inactif",$parc1["existence"],'Le parc1 AR n est pas passé en inactif');
		$this->assertEquals($parc1["date_inactif"],$commandeAR->get("date_debut"),'la date_inactif parc1 AR inactif n est pas bonne');

		$parc2=$this->obj->select($id_parc2);
		$this->assertEquals("inactif",$parc2["existence"],'Le parc2 AR n est pas passé en actif');
		$this->assertEquals($parc2["date_inactif"],$commandeAR->get("date_debut"),'la date_inactif parc2 AR inactif n est pas bonne');

		$parc3=$this->obj->select($id_parc3);
		$this->assertEquals("actif",$parc3["existence"],'Le parc3 AR n est pas passé en actif');
		$this->assertNull($parc3["date_inactif"],'la date_inactif parc3 AR inactif n est pas NULL');

/*		$parc5=$this->obj->select($id_parc5);
		$this->assertEquals("actif",$parc5["existence"],'Le parc5 AR n est pas passé en actif');
		$this->assertNull($parc5["date_inactif"],'la date_inactif parc5 AR inactif n est pas NULL');
*/
		//Arrêter
		ATF::commande()->u(array("id_commande"=>$id_commande,"etat"=>"arreter"));
		$affaire = new affaire_cleodis($id_affaire);
		$commande = new commande_cleodis($id_commande);	

		$this->obj->u(array("id_parc"=>$id_parc1,"etat"=>"loue","existence"=>"actif","date_inactif"=>NULL));
		$this->obj->u(array("id_parc"=>$id_parc2,"etat"=>"loue","existence"=>"actif","date_inactif"=>NULL));

		$this->obj->updateExistenz($commande,$affaire);

		$parc1=$this->obj->select($id_parc1);
		$this->assertEquals("inactif",$parc1["existence"],'Le parc1 Arrêter n est pas passé en inactif');
		$this->assertEquals($parc1["date_inactif"],date("Y-m-d"),'la date_inactif Arrêter inactif n est pas bonne');

		$parc2=$this->obj->select($id_parc2);
		$this->assertEquals("inactif",$parc2["existence"],'Le parc2 Arrêter n est pas passé en actif');
		$this->assertEquals($parc2["date_inactif"],date("Y-m-d"),'la date_inactif parc2 Arrêter inactif n est pas bonne');

/*		$parc5=$this->obj->select($id_parc5);
		$this->assertEquals("actif",$parc5["existence"],'Le parc5 AR n est pas passé en actif');
		$this->assertNull($parc5["date_inactif"],'la date_inactif parc5 AR inactif n est pas NULL');
 * */
	}
	
	
	public function testSelect_all(){
		$id_affaire=ATF::affaire()->decryptId(ATF::affaire()->i(array("etat"=>"commande","date"=>date("Y-m-d"),"ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu","nature"=>"affaire")));

		$id_parc1=$this->obj->i(array(
								"id_societe"=>$this->id_societe,
								"id_affaire"=>$id_affaire,
								"ref"=>"GPAC-GPS",
								"libelle"=>"Ordinateur TOM TOM GO500",
								"serial"=>"00000000000",
								"etat"=>"loue",
								"existence"=>"inactif"
							)
					);

		$id_parc2=$this->obj->i(array(
								"id_societe"=>$this->id_societe,
								"id_affaire"=>$id_affaire,
								"ref"=>"GPAC-GPS1",
								"libelle"=>"Ordinateur TOM TOM GO5001",
								"serial"=>"111111111111",
								"etat"=>"loue",
								"date_inactif"=>"2010-01-01",
								"existence"=>"actif"
							)
					);
		
		$this->obj->q->reset()->addCondition("affaire.id_affaire",$id_affaire);
		$select_all=$this->obj->select_all();
		$this->assertEquals($id_parc2,$select_all[0]["id_parc"],'Select_all ne renvoi pas les bons parc 1');
		$this->assertEquals($id_parc1,$select_all[1]["id_parc"],'Select_all ne renvoi pas les bons parc 2');
		
	}

	public function testInsert(){
		
		$devis["devis"]=array(
								 "id_societe" => $this->id_societe
								,"id_filiale" => 246
								,"date" => date("Y-m-d")
								,"id_contact" => 1401
								,"devis" => "Tu serials"
								,"type_contrat" => "lld"
								,"validite" => date("Y-m-d",strtotime(date("Y-m-d")."- 15 day"))
								,"id_opportunite" =>NULL
								,"tva" => "1.196"
								,"prix" => "14 000.00"
								,"prix_achat" => "4 641.00"
								,"marge" => "66.85"
								,"marge_absolue" => "9 359.00"
        );
		
		$devis["values_devis"] = array(
             "loyer" => '[{"loyer__dot__loyer":"1000","loyer__dot__duree":"14","loyer__dot__assurance":"","loyer__dot__frais_de_gestion":"","loyer__dot__frequence_loyer":"mois","loyer__dot__loyer_total":14000}]'
            ,"produits" => '[{"devis_ligne__dot__produit":"Optiplex GX520 TFT 19","devis_ligne__dot__quantite":"1","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"DEL-WRK-OPTGX520-19","devis_ligne__dot__prix_achat":"10","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"DELL|#ref=164a1c62808dc1a3af6f7d99051db73b","devis_ligne__dot__visibilite_prix":"visible","devis_ligne__dot__id_produit_fk":"9","devis_ligne__dot__id_fournisseur_fk":"1351"},{"devis_ligne__dot__produit":"XSERIES 226","devis_ligne__dot__quantite":"1","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"O2-SRV-226-001","devis_ligne__dot__prix_achat":"3113.00","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"AUDIOPTIC TRADE SERVICES|#ref=c0529cb381c6dcf43fc554b910ce02e9","devis_ligne__dot__visibilite_prix":"visible","devis_ligne__dot__id_produit_fk":"5","devis_ligne__dot__id_fournisseur_fk":"1358"},{"devis_ligne__dot__produit":"Optiplex GX520 TFT 17","devis_ligne__dot__quantite":"2","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"DEL-WRK-OPTGX520-17","devis_ligne__dot__prix_achat":"759.00","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"DELL|#ref=164a1c62808dc1a3af6f7d99051db73b","devis_ligne__dot__visibilite_prix":"visible","devis_ligne__dot__id_produit_fk":"8","devis_ligne__dot__id_fournisseur_fk":"1351"}]'
        );
		
		$id_devis=classes::decryptId(ATF::devis()->insert($devis));
		
		$devis_select=ATF::devis()->select($id_devis);
		ATF::devis_ligne()->q->reset()->addCondition("id_devis",$id_devis);
		$devis_ligne=ATF::devis_ligne()->sa();
		
		$commande["commande"]=array(
								 "commande" => $devis_select["devis"]
								,"type" => "prelevement"
								,"id_societe" => $this->id_societe
								,"date" => date("Y-m-d")
								,"id_affaire" => $devis_select["id_affaire"]
								,"clause_logicielle" => "non"
								,"prix" => "14 000.00"
								,"prix_achat" =>"4 641.00"
								,"marge" => "66.85"
								,"marge_absolue" => "9 359.00"
								,"id_devis" => $devis_select["id_devis"]
        );
		
		$commande["values_commande"] = array(
			 "loyer" => '[{"loyer__dot__loyer":"1000.00","loyer__dot__duree":"14","loyer__dot__assurance":null,"loyer__dot__frais_de_gestion":null,"loyer__dot__frequence_loyer":"mois","loyer__dot__loyer_total":14000}]'
			,"produits" => '[{"commande_ligne__dot__produit":"Optiplex GX520 TFT 19","commande_ligne__dot__quantite":"1","commande_ligne__dot__ref":"DEL-WRK-OPTGX520-19","commande_ligne__dot__id_fournisseur":"DELL","commande_ligne__dot__id_fournisseur_fk":"1351","commande_ligne__dot__prix_achat":"10.00","commande_ligne__dot__id_produit":"Optiplex GX520 TFT 19","commande_ligne__dot__id_produit_fk":"9","commande_ligne__dot__id_commande_ligne":"'.$devis_ligne[0]["id_devis_ligne"].'"},{"commande_ligne__dot__produit":"XSERIES 226","commande_ligne__dot__quantite":"1","commande_ligne__dot__ref":"O2-SRV-226-001","commande_ligne__dot__id_fournisseur":"AUDIOPTIC TRADE SERVICES","commande_ligne__dot__id_fournisseur_fk":"1358","commande_ligne__dot__prix_achat":"3113.00","commande_ligne__dot__id_produit":"XSERIES 226","commande_ligne__dot__id_produit_fk":"5","commande_ligne__dot__id_commande_ligne":"'.$devis_ligne[1]["id_devis_ligne"].'"},{"commande_ligne__dot__produit":"Optiplex GX520 TFT 17","commande_ligne__dot__quantite":"2","commande_ligne__dot__ref":"DEL-WRK-OPTGX520-17","commande_ligne__dot__id_fournisseur":"DELL","commande_ligne__dot__id_fournisseur_fk":"1351","commande_ligne__dot__prix_achat":"759.00","commande_ligne__dot__id_produit":"Optiplex GX520 TFT 17","commande_ligne__dot__id_produit_fk":"8","commande_ligne__dot__id_commande_ligne":"'.$devis_ligne[2]["id_devis_ligne"].'"}]'
		);
		
		$id_commande=classes::decryptId(ATF::commande()->insert($commande));
		$commande_select=ATF::commande()->select($id_commande);
		ATF::commande_ligne()->q->reset()->addCondition("id_commande",$id_commande);
		$commande_ligne=ATF::commande_ligne()->sa();
		
		$bon_de_commande["bon_de_commande"]=array(
								 "id_societe" => $this->id_societe
								,"id_commande" => $id_commande
								,"id_fournisseur" => 1351
								,"id_affaire" => $devis_select["id_affaire"]
								,"bon_de_commande" => $devis_select["devis"]
								,"id_contact" => $this->id_contact
								,"prix" => "10.00"
								,"tva" =>"1.196"
								,"etat" => "envoyee"
								,"payee" => "non"
								,"date" => date("Y-m-d")
								,"destinataire" => "AXXES"
								,"adresse" => "26 rue de La Vilette - Part Dieu"
								,"adresse_2" => $devis_select["id_devis"]
								,"adresse_3" => $devis_select["id_devis"]
								,"cp" => "69003"
								,"ville" => "LYON"
								,"id_pays" => "FR"
								,"id_fournisseur_intermediaire" => NULL
								,"livraison_destinataire" => NULL
								,"livraison_adresse" => NULL
								,"livraison_cp" => NULL
								,"livraison_ville" => NULL
								,"email" => "debug@absystech.fr"
								,"emailTexte" => "TU<br>"
								,"emailCopie" => "debug@absystech.fr"
								,"filestoattach" =>  array(
										"fichier_joint" =>NULL
									)
        );
		
		$bon_de_commande["commandes"]="xnode-".$id_commande.",".$commande_ligne[0]["id_commande_ligne"].",".$commande_ligne[1]["id_commande_ligne"];
		$id_bon_de_commande=classes::decryptId(ATF::bon_de_commande()->insert($bon_de_commande));
		ATF::bon_de_commande_ligne()->q->reset()->addCondition("id_bon_de_commande",$id_bon_de_commande);
		$bon_de_commande_ligne=ATF::bon_de_commande_ligne()->sa();

		$infos["parc"]=array("id_bon_de_commande"=>$id_bon_de_commande);

		try {
			classes::decryptId($this->obj->insert($infos));
		} catch (errorATF $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(877,$error,'Erreur insertion de parc sans parc !');

		$infos["values_parc"]["produits"]=json_encode(array
											(0=>array(
														"parc__dot__produit"=>"Optiplex GX520 TFT 19",
														"parc__dot__quantite"=>1,
														"parc__dot__ref"=>"DEL-WRK-OPTGX520-19",
														"parc__dot__prix"=>"10.00",
														"parc__dot__serial"=>"aaaaaa",
														"parc__dot__id_parc"=>$bon_de_commande_ligne[0]["id_bon_de_commande_ligne"]
												)
											,1=>array(
														"parc__dot__produit"=>"XSERIES 226",
														"parc__dot__quantite"=>1,
														"parc__dot__ref"=>"O2-SRV-226-001",
														"parc__dot__prix"=>"3113.00",
														"parc__dot__serial"=>NULL,
														"parc__dot__id_parc"=>$bon_de_commande_ligne[1]["id_bon_de_commande_ligne"]
												)
											)
										);
		
		try {
			classes::decryptId($this->obj->insert($infos));
		} catch (errorATF $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(880,$error,'Erreur affaire sans garantie !');
		ATF::parc()->q->reset()->addCondition("id_affaire",$devis_select["id_affaire"]);
		$parcs=ATF::parc()->sa();
		foreach($parcs as $key=>$item){
			ATF::parc()->d($item["id_parc"]);
		}

		ATF::affaire()->u(array("id_affaire"=>$devis_select["id_affaire"],"date_garantie"=>"2010-01-01"));

		try {
			classes::decryptId($this->obj->insert($infos));
		} catch (errorATF $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(883,$error,'Erreur parc sans serial !');
		ATF::parc()->q->reset()->addCondition("id_affaire",$devis_select["id_affaire"]);
		$parcs=ATF::parc()->sa();
		foreach($parcs as $key=>$item){
			ATF::parc()->d($item["id_parc"]);
		}

		$infos["values_parc"]["produits"]=json_encode(array
											(0=>array(
														"parc__dot__produit"=>"Optiplex GX520 TFT 19",
														"parc__dot__quantite"=>1,
														"parc__dot__ref"=>"DEL-WRK-OPTGX520-19",
														"parc__dot__prix"=>"10.00",
														"parc__dot__serial"=>"aaaaaa",
														"parc__dot__id_parc"=>$bon_de_commande_ligne[0]["id_bon_de_commande_ligne"]
												)
											,1=>array(
														"parc__dot__produit"=>"XSERIES 226",
														"parc__dot__quantite"=>1,
														"parc__dot__ref"=>"O2-SRV-226-001",
														"parc__dot__prix"=>"3113.00",
														"parc__dot__serial"=>"aaaaaa",
														"parc__dot__id_parc"=>$bon_de_commande_ligne[1]["id_bon_de_commande_ligne"]
												)
											)
										);

		try {
			classes::decryptId($this->obj->insert($infos));
		} catch (errorATF $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(881,$error,'Erreur serial déjà utilisé !');
		ATF::parc()->q->reset()->addCondition("id_affaire",$devis_select["id_affaire"]);
		$parcs=ATF::parc()->sa();
		foreach($parcs as $key=>$item){
			ATF::parc()->d($item["id_parc"]);
		}

		$infos["values_parc"]["produits"]=json_encode(array
											(0=>array(
														"parc__dot__produit"=>"Optiplex GX520 TFT 19",
														"parc__dot__quantite"=>1,
														"parc__dot__ref"=>"DEL-WRK-OPTGX520-19",
														"parc__dot__prix"=>"10.00",
														"parc__dot__serial"=>"aaaaaaaa",
														"parc__dot__id_parc"=>$bon_de_commande_ligne[0]["id_bon_de_commande_ligne"]
												)
											,1=>array(
														"parc__dot__produit"=>"XSERIES 226",
														"parc__dot__quantite"=>1,
														"parc__dot__ref"=>"O2-SRV-226-001",
														"parc__dot__prix"=>"3113.00",
														"parc__dot__serial"=>"bbbbbbb",
														"parc__dot__id_parc"=>$bon_de_commande_ligne[1]["id_bon_de_commande_ligne"]
												)
											)
										);

		$this->assertTrue($this->obj->insert($infos),'Erreur insertion parc !');
		ATF::parc()->q->reset()->addCondition("id_affaire",$devis_select["id_affaire"]);
		$parcs=ATF::parc()->sa();

		$this->assertEquals(array
								(0=>array(
									"id_parc"=>$parcs[0]["id_parc"],
									"id_societe"=>$this->id_societe,
									"id_produit"=>9,
									"id_affaire"=>$devis_select["id_affaire"],
									"ref"=>"DEL-WRK-OPTGX520-19",
									"libelle"=>"Optiplex GX520 TFT 19",
									"divers"=>NULL,
									"serial"=>"aaaaaaaa",
									"etat"=>"loue",
									"code"=>NULL,
									"date"=>$parcs[0]["date"],
									"date_inactif"=>NULL,
									"date_garantie"=>"2010-01-01",
									"provenance"=>NULL,
									"existence"=>"actif",
									"date_achat"=> NULL
									)
								,1=>array(
									"id_parc"=>$parcs[1]["id_parc"],
									"id_societe"=>$this->id_societe,
									"id_produit"=>5,
									"id_affaire"=>$devis_select["id_affaire"],
									"ref"=>"O2-SRV-226-001",
									"libelle"=>"XSERIES 226",
									"divers"=>NULL,
									"serial"=>"bbbbbbb",
									"etat"=>"loue",
									"code"=>NULL,
									"date"=>$parcs[1]["date"],
									"date_inactif"=>NULL,
									"date_garantie"=>"2010-01-01",
									"provenance"=>NULL,
									"existence"=>"actif",
									"date_achat"=> NULL
									)
								)
							,$parcs,'Erreur serial déjà utilisé !');
		ATF::parc()->q->reset()->addCondition("id_affaire",$devis_select["id_affaire"]);
		$parcs=ATF::parc()->sa();
		foreach($parcs as $key=>$item){
			ATF::parc()->d($item["id_parc"]);
		}

		ATF::affaire()->u(array("id_affaire"=>$devis_select["id_affaire"],"nature"=>"vente"));
		$infos["values_parc"]["produits"]=json_encode(array
											(0=>array(
														"parc__dot__produit"=>"Optiplex GX520 TFT 19",
														"parc__dot__quantite"=>1,
														"parc__dot__ref"=>"DEL-WRK-OPTGX520-19",
														"parc__dot__prix"=>"10.00",
														"parc__dot__serial"=>"ccccc",
														"parc__dot__id_parc"=>$bon_de_commande_ligne[0]["id_bon_de_commande_ligne"]
												)
											,1=>array(
														"parc__dot__produit"=>"XSERIES 226",
														"parc__dot__quantite"=>1,
														"parc__dot__ref"=>"O2-SRV-226-001",
														"parc__dot__prix"=>"3113.00",
														"parc__dot__serial"=>"dddddddddd",
														"parc__dot__id_parc"=>$bon_de_commande_ligne[1]["id_bon_de_commande_ligne"]
												)
											)
										);

		$this->assertTrue($this->obj->insert($infos),'Erreur insertion parc !');
		ATF::parc()->q->reset()->addCondition("id_affaire",$devis_select["id_affaire"]);
		$parcs=ATF::parc()->sa();

		$this->assertEquals(array
								(0=>array(
									"id_parc"=>$parcs[0]["id_parc"],
									"id_societe"=>$this->id_societe,
									"id_produit"=>9,
									"id_affaire"=>$devis_select["id_affaire"],
									"ref"=>"DEL-WRK-OPTGX520-19",
									"libelle"=>"Optiplex GX520 TFT 19",
									"divers"=>NULL,
									"serial"=>"ccccc",
									"etat"=>"vendu",
									"code"=>NULL,
									"date"=>$parcs[0]["date"],
									"date_inactif"=>NULL,
									"date_garantie"=>"2010-01-01",
									"provenance"=>NULL,
									"existence"=>"actif",
									"date_achat"=> NULL
									)
								,1=>array(
									"id_parc"=>$parcs[1]["id_parc"],
									"id_societe"=>$this->id_societe,
									"id_produit"=>5,
									"id_affaire"=>$devis_select["id_affaire"],
									"ref"=>"O2-SRV-226-001",
									"libelle"=>"XSERIES 226",
									"divers"=>NULL,
									"serial"=>"dddddddddd",
									"etat"=>"vendu",
									"code"=>NULL,
									"date"=>$parcs[1]["date"],
									"date_inactif"=>NULL,
									"date_garantie"=>"2010-01-01",
									"provenance"=>NULL,
									"existence"=>"actif",
									"date_achat"=> NULL
									)
								)
							,$parcs,'Erreur serial déjà utilisé !');
		
	}

	public function testParcByBdc(){
		$id_affaire=ATF::affaire()->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu"));
		$id_commande=ATF::commande()->i(array("ref"=>"Ref tu","id_societe"=>$this->id_societe,"id_user"=>$this->id_user,"tva"=>"1,196","id_affaire"=>$id_affaire));
		
		$id_commande_ligne1=ATF::commande_ligne()->i(array("id_commande"=>$id_commande,"id_produit"=>5,"produit"=>"produit a","quantite"=>1));
		$id_commande_ligne2=ATF::commande_ligne()->i(array("id_commande"=>$id_commande,"id_produit"=>5,"produit"=>"produit b","quantite"=>1));
		
		$bon_de_commande["bon_de_commande"]=array(
								 "id_societe" => $this->id_societe
								,"ref" => "aaa"
								,"id_commande" => $id_commande
								,"id_fournisseur" => 1351
								,"id_affaire" => $id_affaire
								,"bon_de_commande" => "AffaireTu"
								,"tva" =>"1.196"
								,"date" => date("Y-m-d")
								,"destinataire" => "AXXES"
								,"adresse" => "26 rue de La Vilette - Part Dieu"
								,"cp" => "69003"
								,"ville" => "LYON"
								,"id_user" => $this->id_user
        );
		
		$id_bon_de_commande=classes::decryptId(ATF::bon_de_commande()->i($bon_de_commande));
		
		$bon_de_commande_ligne1=array(
										"id_bon_de_commande"=>$id_bon_de_commande,
										"produit"=>"produit a",
										"id_commande_ligne"=>$id_commande_ligne1,
										"quantite"=>1
									);
		
		$id_bon_de_commande_ligne1=ATF::bon_de_commande_ligne()->i($bon_de_commande_ligne1);

		$bon_de_commande_ligne2=array(
										"id_bon_de_commande"=>$id_bon_de_commande,
										"produit"=>"produit b",
										"id_commande_ligne"=>$id_commande_ligne2,
										"quantite"=>1
									);
		
		$id_bon_de_commande_ligne2=ATF::bon_de_commande_ligne()->i($bon_de_commande_ligne2);
		
		$this->assertTrue($this->obj->parcByBdc($id_bon_de_commande),'ParcByBdc renvoi faux alors qu il y a des parcs');
	
		ATF::commande_ligne()->u(array("id_commande_ligne"=>$id_commande_ligne1,"serial"=>"aa"));
		$this->assertTrue($this->obj->parcByBdc($id_bon_de_commande),'parcByBdc renvoi faux alors qu il y a des parcs');

		ATF::commande_ligne()->u(array("id_commande_ligne"=>$id_commande_ligne2,"serial"=>"bbb"));
		$this->assertFalse($this->obj->parcByBdc($id_bon_de_commande),'parcByBdc renvoi tru alors qu il n y a plus des parcs');
	}
	
	public function testParcByAffaire(){
		$id_affaire=ATF::affaire()->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu"));
		$id_commande=ATF::commande()->i(array("ref"=>"Ref tu","id_societe"=>$this->id_societe,"id_user"=>$this->id_user,"tva"=>"1,196","id_affaire"=>$id_affaire));
		
		$id_commande_ligne1=ATF::commande_ligne()->i(array("id_commande"=>$id_commande,"id_produit"=>5,"produit"=>"produit a","quantite"=>1));
		$id_commande_ligne2=ATF::commande_ligne()->i(array("id_commande"=>$id_commande,"id_produit"=>5,"produit"=>"produit b","quantite"=>1));
		
		$bon_de_commande["bon_de_commande"]=array(
								 "id_societe" => $this->id_societe
								,"ref" => "aaa"
								,"id_commande" => $id_commande
								,"id_fournisseur" => 1351
								,"id_affaire" => $id_affaire
								,"bon_de_commande" => "AffaireTu"
								,"tva" =>"1.196"
								,"date" => date("Y-m-d")
								,"destinataire" => "AXXES"
								,"adresse" => "26 rue de La Vilette - Part Dieu"
								,"cp" => "69003"
								,"ville" => "LYON"
								,"id_user" => $this->id_user
        );
		
		$id_bon_de_commande=classes::decryptId(ATF::bon_de_commande()->i($bon_de_commande));
		
		$bon_de_commande_ligne1=array(
										"id_bon_de_commande"=>$id_bon_de_commande,
										"produit"=>"produit a",
										"id_commande_ligne"=>$id_commande_ligne1,
										"quantite"=>1
									);
		
		$id_bon_de_commande_ligne1=ATF::bon_de_commande_ligne()->i($bon_de_commande_ligne1);

		$bon_de_commande_ligne2=array(
										"id_bon_de_commande"=>$id_bon_de_commande,
										"produit"=>"produit b",
										"id_commande_ligne"=>$id_commande_ligne2,
										"quantite"=>1
									);
		
		$id_bon_de_commande_ligne2=ATF::bon_de_commande_ligne()->i($bon_de_commande_ligne2);
		
		$this->assertTrue($this->obj->parcByAffaire($id_affaire),'ParcByAffaire renvoi faux alors qu il y a des parcs');
	
		ATF::commande_ligne()->u(array("id_commande_ligne"=>$id_commande_ligne1,"serial"=>"aa"));
		$this->assertTrue($this->obj->parcByAffaire($id_affaire),'ParcByAffaire renvoi faux alors qu il y a des parcs');

		ATF::commande_ligne()->u(array("id_commande_ligne"=>$id_commande_ligne2,"serial"=>"bbb"));
		$this->assertFalse($this->obj->parcByAffaire($id_affaire),'ParcByAffaire renvoi true alors qu il n y a plus des parcs');
	}

	/* @author Nicolas BERTEMONT <nbertemont@absystech.fr> */
	public function test_constructMidas(){
		$cm=new parc_midas();
		$this->assertEquals('a:6:{s:8:"parc.ref";a:5:{s:4:"type";s:4:"text";s:5:"xtype";s:9:"textfield";s:9:"maxlength";s:2:"32";s:7:"default";N;s:4:"null";b:1;}s:12:"parc.libelle";a:4:{s:4:"type";s:4:"text";s:5:"xtype";s:9:"textfield";s:9:"maxlength";s:3:"128";s:7:"default";N;}s:15:"parc.id_societe";a:5:{s:4:"type";s:3:"int";s:5:"xtype";s:11:"numberfield";s:9:"maxlength";s:1:"8";s:7:"default";N;s:4:"null";b:1;}s:18:"parc.date_garantie";a:5:{s:4:"type";s:4:"date";s:5:"xtype";s:9:"datefield";s:7:"default";N;s:4:"null";b:1;s:10:"updateDate";b:1;}s:11:"parc.serial";a:4:{s:4:"type";s:4:"text";s:5:"xtype";s:9:"textfield";s:9:"maxlength";s:2:"64";s:7:"default";N;}s:9:"parc.etat";a:4:{s:4:"type";s:4:"enum";s:5:"xtype";s:5:"combo";s:4:"data";a:5:{i:0;s:5:"broke";i:1;s:4:"loue";i:2;s:6:"reloue";i:3;s:4:"vole";i:4;s:5:"vendu";}s:7:"default";N;}}',serialize($cm->colonnes['fields_column']),"Le constructeur de la classe midas a changé");
	}
	
	/* @author Nicolas BERTEMONT <nbertemont@absystech.fr> */
	public function test_selectAllFranchCoursInfoMidas(){
		$cm=new parc_midas();
		$cm->selectAllFranchCoursInfo();
		$this->assertEquals("SELECT  `affaire`.* , `commande`.* , `societe`.* , `produit`.* , `parc`.*  FROM `parc`  LEFT JOIN affaire ON (`parc`.`id_affaire`=`affaire`.`id_affaire`)  LEFT JOIN commande ON (`affaire`.`id_affaire`=`commande`.`id_affaire`)  LEFT JOIN societe ON (`parc`.`id_societe`=`societe`.`id_societe`)  LEFT JOIN produit ON (`parc`.`id_produit`=`produit`.`id_produit`)  WHERE (libelle LIKE '%HP%' OR libelle LIKE '%NEC%' OR libelle LIKE '%Brother%') AND (commande.etat = 'prolongation' OR commande.etat = 'mis_loyer') AND (societe.id_filiale IS NULL) AND (societe.code_client LIKE 'M%') AND (societe.divers_3 = 'Midas') AND (parc.existence = 'actif') ORDER BY `existence` ASC, `parc`.`id_parc` desc",$cm->q->lastSQL,"La requête n'est pas bonne");
	}
	
	/* @author Nicolas BERTEMONT <nbertemont@absystech.fr> */
	public function test_selectAllSucCoursInfoMidas(){
		$cm=new parc_midas();
		$cm->selectAllSucCoursInfo();
		$this->assertEquals("SELECT  `affaire`.* , `commande`.* , `societe`.* , `produit`.* , `parc`.*  FROM `parc`  LEFT JOIN affaire ON (`parc`.`id_affaire`=`affaire`.`id_affaire`)  LEFT JOIN commande ON (`affaire`.`id_affaire`=`commande`.`id_affaire`)  LEFT JOIN societe ON (`parc`.`id_societe`=`societe`.`id_societe`)  LEFT JOIN produit ON (`parc`.`id_produit`=`produit`.`id_produit`)  WHERE (libelle LIKE '%HP%' OR libelle LIKE '%NEC%' OR libelle LIKE '%Brother%') AND (commande.etat = 'prolongation' OR commande.etat = 'mis_loyer') AND (societe.id_filiale IS NOT NULL) AND (societe.code_client LIKE 'M%') AND (societe.divers_3 = 'Midas') AND (parc.existence = 'actif') ORDER BY `existence` ASC, `parc`.`id_parc` desc",$cm->q->lastSQL,"La requête n'est pas bonne");
	}
	
	//@author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	public function test_getParcActifFromSerial(){
		$id_parc = ATF::parc()->i(array("id_societe"=> $this->id_societe , "id_produit"=>5, "id_affaire"=>$commande["commande"]["id_affaire"] , "ref"=>"Une ref", "libelle"=>"libelle", "serial"=>"toto", "etat"=>"loue" , "existence"=>"actif"));
		$this->assertEquals($id_parc,$this->obj->getParcActifFromSerial("toto") , "Ne retourne pas le materiel actif avec le serial");
		
		$this->assertEquals(true,$this->obj->parcSerialIsActif("toto") , "2 - Ne retourne pas le materiel actif avec le serial");
		
		
		ATF::parc()->u(array("id_parc" => $id_parc, "existence"=>"inactif" ));
		$this->assertEquals(null,$this->obj->getParcActifFromSerial("toto") , "3 - Ne retourne pas le materiel actif avec le serial");
		
		
	
	}

	public function test_updateDate(){
		$id_affaire=ATF::affaire()->decryptId(ATF::affaire()->i(array("etat"=>"commande","date"=>date("Y-m-d"),"ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu","nature"=>"affaire")));
		$id_devis=ATF::devis()->decryptId(ATF::devis()->i(array("ref"=>"refTu","id_user"=>$this->id_user,"id_societe"=>$this->id_societe,"id_filiale"=>246,"prix"=>600,"date"=>date("Y-m-d"),"devis"=>"AffaireTu","type_contrat"=>"lld","date_accord"=>date("Y-m-d"),"etat"=>"gagne","id_contact"=>$this->id_contact,"id_affaire"=>$id_affaire,"tva"=>"1.196","loyer_unique"=>"non","prix_achat"=>0,"validite"=>date("Y-m-d"))));
		$id_devis_ligne=ATF::devis_ligne()->i(array("type"=>"portable","id_devis"=>$id_devis,"id_produit"=>6,"ref"=>"GPAC-GPS","produit"=>"Ordinateur TOM TOM GO500","quantite"=>2,"id_fournisseur"=>5385,"prix_achat"=>"0","visible"=>"oui","visibilite_prix"=>"visible"));
		$id_commande=ATF::commande()->decryptId(ATF::commande()->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"commande"=>"AffaireTu","prix_achat"=>0,"prix"=>600,"date"=>date("Y-m-d"),"id_devis"=>$id_devis,"etat"=>"mis_loyer","id_user"=>$this->id_user,"tva"=>"1.196","clause_logicielle"=>"non","date_debut"=>date("Y-m-d"),"type"=>"prelevement","id_affaire"=>$id_affaire)));
		$loyer = ATF::loyer()->i(array("id_affaire" => $id_affaire, "loyer" => "300" , "duree" => 26, "frequence_loyer" => "mois"));

		$id_parc1=$this->obj->i(array(
								"id_societe"=>$this->id_societe,
								"id_affaire"=>$id_affaire,
								"ref"=>"GPAC-GPS",
								"libelle"=>"Ordinateur TOM TOM GO500",
								"serial"=>"00000000000",
								"etat"=>"loue",
								"existence"=>"actif"
							)
					);

		$this->obj->updateDate(array("id_parc" => $id_parc1, "value" => date("Y-m-d") , "key" => "date_achat"));
		$res = $this->obj->select($id_parc1);
		$this->assertEquals(date("Y-m-d") , $res["date_achat"] , "Update 1 Error");
		$this->assertEquals(date('Y-m-d', strtotime("+26 month")) , $res["date_garantie"] , "Update 2 Error");


		ATF::loyer()->u(array("id_loyer" => $loyer, "frequence_loyer" => "trimestre"));
		$this->obj->updateDate(array("id_parc" => $id_parc1, "value" => date("Y-m-d") , "key" => "date_achat"));
		$res = $this->obj->select($id_parc1);
		$this->assertEquals(date("Y-m-d") , $res["date_achat"] , "Update 1 Error");
		$this->assertEquals(date('Y-m-d', strtotime("+78 month")) , $res["date_garantie"] , "Update 2 Error");


		ATF::loyer()->u(array("id_loyer" => $loyer, "frequence_loyer" => "an"));
		$this->obj->updateDate(array("id_parc" => $id_parc1, "value" => date("Y-m-d") , "key" => "date_achat"));
		$res = $this->obj->select($id_parc1);
		$this->assertEquals(date("Y-m-d") , $res["date_achat"] , "Update 1 Error");
		$this->assertEquals(date('Y-m-d', strtotime("+26 year")) , $res["date_garantie"] , "Update 2 Error");


		ATF::loyer()->u(array("id_loyer" => $loyer, "frequence_loyer" => "semestre", "duree"=> "2"));
		$this->obj->updateDate(array("id_parc" => $id_parc1, "value" => date("Y-m-d") , "key" => "date_achat"));
		$res = $this->obj->select($id_parc1);
		$this->assertEquals(date("Y-m-d") , $res["date_achat"] , "Update 4 Error");
		$this->assertEquals(date('Y-m-d', strtotime("+12 month")) , $res["date_garantie"] , "Update 5 Error");

		ATF::$msg->getNotices();
	}
	
};
?>