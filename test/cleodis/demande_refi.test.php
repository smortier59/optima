<?
class demande_refi_test extends ATF_PHPUnit_Framework_TestCase {

	/** Méthode pré-test, exécute avant chaque test unitaire
	* besoin d'un user pour les traduction
	*/
	public function setUp() {
		$this->initUser();
//		echo "(".ATF::db()->numberTransaction()."|";
	}

	/** Méthode post-test, exécute après chaque test unitaire*/
	public function tearDown(){
//		echo ATF::db()->numberTransaction().")";
		ATF::db()->rollback_transaction(true);
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
	public function test_updateDateValidite(){

		$id_affaire=ATF::affaire()->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu"));
		$id_demande_refi=$this->obj->i(array("date"=>date("Y-m-d"),"id_contact"=>$this->id_contact,"id_refinanceur"=>1,"id_affaire"=>$id_affaire,"id_societe"=>$this->id_societe,"description"=>"Tu description"));

		$infos["value"]="undefined";
		$infos["key"]="validite_accord";
		$infos["id_demande_refi"]="aa";

		$this->assertFalse($this->obj->updateDate($infos),"updateDateValidite devrait renvoyer false puisqu'il y a pas d'id_demande_refi");
		$this->obj->updateDate($infos);

		$infos["id_demande_refi"]=$id_demande_refi;
		$infos["value"]=date("Y-m-d");

		$this->obj->updateDate($infos);

		$this->assertEquals(array(
								array(
										"msg" => "Modification de 'validite_accord' de l'enregistrement 'Tu description' effectuée avec succès.",
										"title" => "Succès !",
										"timer" => null,
										"type" => "success"
										)
							),
							ATF::$msg->getNotices(),
							"Les notices ne sont pas cohérentes !");

		$this->assertEquals("valide",
							$this->obj->select($id_demande_refi,"etat"),
							"La demande refi ne passe pas en valide !");

		unset($infos["value"]);
		$this->obj->updateDate($infos);

		$this->assertEquals(array(
								array(
										"msg" => "Modification de 'validite_accord' de l'enregistrement 'Tu description' effectuée avec succès.",
										"title" => "Succès !",
										"timer" => null,
										"type" => "success"
										)
							),
							ATF::$msg->getNotices(),
							"Les notices ne sont pas cohérentes !");

		$this->assertEquals("accepte",
							$this->obj->select($id_demande_refi,"etat"),
							"La demande refi ne passe pas en accepte !");
	}


	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
	public function test_updateDateCession(){

		$id_affaire=ATF::affaire()->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu"));
		$id_commande=ATF::commande()->i(array("ref"=>"Ref tu","id_societe"=>$this->id_societe,"id_user"=>$this->id_user,"tva"=>"1,196","id_affaire"=>$id_affaire));
		$id_loyer =ATF::loyer()->i(
										array(
												"id_affaire"=>$id_affaire,
												"loyer"=>"200",
												"duree"=>10,
												"assurance"=>"2",
												"frais_de_gestion"=>1,
												"frequence_loyer"=>"mois"
											)
										);
		$id_demande_refi=$this->obj->i(array("date"=>date("Y-m-d"),"id_contact"=>$this->id_contact,"id_refinanceur"=>1,"id_affaire"=>$id_affaire,"id_societe"=>$this->id_societe,"description"=>"Tu description"));

		$infos["key"]="date_cession";
		$infos["id_demande_refi"]=$id_demande_refi;
		$infos["value"]=date("Y-m-d");


		try {
			$this->obj->updateDate($infos);
		} catch (errorATF $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(875,$error,"Impossible d'insérer date de cession car il n'y a pas de date de fin de contrat");

		ATF::commande()->u(array("id_commande"=>$id_commande,"date_evolution"=>date("Y-m-d",strtotime(date("Y-m-d")." - 6 month"))));

		try {
			$this->obj->updateDate($infos);
		} catch (errorATF $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(876,$error,'La date de cession date est inférieur à la date de début de contrat');

		ATF::commande()->u(array("id_commande"=>$id_commande,"date_evolution"=>date("Y-m-d",strtotime(date("Y-m-d")." + 6 month + 2 years + 3 days"))));
		$this->obj->updateDate($infos);

		$this->assertEquals(array(
								array(
										"msg" => "Modification de 'date_cession' de l'enregistrement 'Tu description' effectuée avec succès.",
										"title" => "Succès !",
										"timer" => null,
										"type" => "success"
										)
							),
							ATF::$msg->getNotices(),
							"Les notices ne sont pas cohérentes !");

		$this->assertEquals("31 mois(s)",
							$this->obj->select($id_demande_refi,"duree_refinancement"),
							"La demande refi ne renvoi pas le bon duree_refinancement !");

		$this->assertEquals(date("Y-m-d"),
							$this->obj->select($id_demande_refi,"date_cession"),
							"La demande refi ne renvoi pas le bon date_cession !");


		ATF::loyer()->u(array("id_loyer"=>$id_loyer,"loyer"=>"200","frequence_loyer"=>"trimestre"));

		$this->obj->updateDate($infos);

		$this->assertEquals(array(
								array(
										"msg" => "Modification de 'date_cession' de l'enregistrement 'Tu description' effectuée avec succès.",
										"title" => "Succès !",
										"timer" => null,
										"type" => "success"
										)
							),
							ATF::$msg->getNotices(),
							"Les notices ne sont pas cohérentes !");

		$this->assertEquals("11 trimestre(s)",
							$this->obj->select($id_demande_refi,"duree_refinancement"),
							"La demande refi ne renvoi pas le bon duree_refinancement !");

		$this->assertEquals(date("Y-m-d"),
							$this->obj->select($id_demande_refi,"date_cession"),
							"La demande refi ne renvoi pas le bon date_cession !");



		ATF::loyer()->u(array("id_loyer"=>$id_loyer,"loyer"=>"200","frequence_loyer"=>"semestre"));

		$this->obj->updateDate($infos);

		$this->assertEquals(array(
								array(
										"msg" => "Modification de 'date_cession' de l'enregistrement 'Tu description' effectuée avec succès.",
										"title" => "Succès !",
										"timer" => null,
										"type" => "success"
										)
							),
							ATF::$msg->getNotices(),
							"Les notices ne sont pas cohérentes !");

		$this->assertEquals("6 semestre(s)",
							$this->obj->select($id_demande_refi,"duree_refinancement"),
							"La demande refi ne renvoi pas le bon duree_refinancement !");



		ATF::loyer()->u(array("id_loyer"=>$id_loyer,"loyer"=>"200","frequence_loyer"=>"an"));

		$this->obj->updateDate($infos);

		$this->assertEquals(array(
								array(
										"msg" => "Modification de 'date_cession' de l'enregistrement 'Tu description' effectuée avec succès.",
										"title" => "Succès !",
										"timer" => null,
										"type" => "success"
										)
							),
							ATF::$msg->getNotices(),
							"Les notices ne sont pas cohérentes !");

		$this->assertEquals("3 an(s)",
							$this->obj->select($id_demande_refi,"duree_refinancement"),
							"La demande refi ne renvoi pas le bon duree_refinancement !");

		$this->assertEquals(date("Y-m-d"),
							$this->obj->select($id_demande_refi,"date_cession"),
							"La demande refi ne renvoi pas le bon date_cession !");


		unset($infos["value"]);
		$this->obj->updateDate($infos);

		$this->assertEquals(array(
								array(
										"msg" => "Modification de 'date_cession' de l'enregistrement 'Tu description' effectuée avec succès.",
										"title" => "Succès !",
										"timer" => null,
										"type" => "success"
										)
							),
							ATF::$msg->getNotices(),
							"Les notices ne sont pas cohérentes !");

		$this->assertNull(
							$this->obj->select($id_demande_refi,"duree_refinancement"),
							"La demande refi ne renvoi pas le bon duree_refinancement !");

		$this->assertNull(
							$this->obj->select($id_demande_refi,"date_cession"),
							"La demande refi ne renvoi pas le bon date_cession !");
	}




	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
	public function test_existDemandeRefi(){

		$id_affaire=ATF::affaire()->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu"));

		$this->assertFalse($this->obj->existDemandeRefi($id_affaire),
							"existDemandeRefi ne devrait rien retourner car il n'y a pas d'affaire");

		$id_demande_refi=$this->obj->i(array("date"=>date("Y-m-d"),"id_contact"=>$this->id_contact,"id_refinanceur"=>1,"id_affaire"=>$id_affaire,"id_societe"=>$this->id_societe,"description"=>"Tu description"));

		$this->assertFalse($this->obj->existDemandeRefi($id_affaire),
							"existDemandeRefi ne devrait rien retourner car il n'est pas valide");

		$this->obj->u(array("id_demande_refi"=>$id_demande_refi,"etat"=>"valide"));

		$existDemandeRefi=$this->obj->existDemandeRefi($id_affaire);
		$this->assertEquals($id_demande_refi,
							$existDemandeRefi[0]["id_demande_refi"],
							"existDemandeRefi devrait retourner true car il est valide");

	}

//	@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
//
	public function test_defaultValues(){

		$this->devis["devis"]=array(
								 "id_societe" => $this->id_societe
								,"id_filiale" => 246
								,"date" => date("Y-m-d")
								,"id_contact" => $this->id_contact
								,"devis" => "Tu demande_refi"
								,"type_contrat" => "lld"
								,"validite" => date("Y-m-d",strtotime(date("Y-m-d")."- 15 day"))
								,"id_opportunite" =>NULL
								,"tva" => "1.196"
								,"prix" => "14 000.00"
								,"prix_achat" => "4 641.00"
								,"marge" => "66.85"
								,"marge_absolue" => "9 359.00"
        );

		$this->devis["values_devis"] = array(
             "loyer" => '[{"loyer__dot__loyer":"1000","loyer__dot__duree":"14","loyer__dot__assurance":"","loyer__dot__frais_de_gestion":"","loyer__dot__frequence_loyer":"mois","loyer__dot__loyer_total":14000}]'
            ,"produits" => '[{"devis_ligne__dot__produit":"Optiplex GX520 TFT 19","devis_ligne__dot__quantite":"1","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"DEL-WRK-OPTGX520-19","devis_ligne__dot__prix_achat":"10","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"DELL|#ref=164a1c62808dc1a3af6f7d99051db73b","devis_ligne__dot__visibilite_prix":"visible","devis_ligne__dot__id_produit_fk":"9","devis_ligne__dot__id_fournisseur_fk":"1351"},{"devis_ligne__dot__produit":"XSERIES 226","devis_ligne__dot__quantite":"1","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"O2-SRV-226-001","devis_ligne__dot__prix_achat":"3113.00","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"AUDIOPTIC TRADE SERVICES|#ref=c0529cb381c6dcf43fc554b910ce02e9","devis_ligne__dot__visibilite_prix":"visible","devis_ligne__dot__id_produit_fk":"5","devis_ligne__dot__id_fournisseur_fk":"1358"},{"devis_ligne__dot__produit":"Optiplex GX520 TFT 17","devis_ligne__dot__quantite":"2","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"DEL-WRK-OPTGX520-17","devis_ligne__dot__prix_achat":"759.00","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"DELL|#ref=164a1c62808dc1a3af6f7d99051db73b","devis_ligne__dot__visibilite_prix":"visible","devis_ligne__dot__id_produit_fk":"8","devis_ligne__dot__id_fournisseur_fk":"1351"}]'
        );

		$this->id_devis=classes::decryptId(ATF::devis()->insert($this->devis));
		$id_affaire=ATF::devis()->select($this->id_devis,"id_affaire");

		$id_comite=$this->obj->i(array("date"=>date("Y-m-d"),"id_contact"=>$this->id_contact,"id_refinanceur"=>1,"id_affaire"=>$id_affaire,"id_societe"=>$this->id_societe,"description"=>"Tu description"));


		$this->assertNull($this->obj->default_value("id_societe"),'valeur societe');
		$this->assertNull($this->obj->default_value("id_contact"),'valeur id_contact');
		$this->assertNull($this->obj->default_value("prix"),'valeur prix');
		$this->assertNull($this->obj->default_value("description"),'valeur description');
		$this->assertNull($this->obj->default_value("loyer_actualise"),'valeur loyer_actualise');


		ATF::_r('id_affaire',$id_affaire);
		$affaire = ATF::affaire()->select($id_affaire);
		$this->assertEquals($this->id_societe,$this->obj->default_value("id_societe"),'valeur societe');
		$this->assertEquals(ATF::$usr->get('id_user'),$this->obj->default_value("id_user"),'valeur id_user');
		$this->assertEquals(date("d-m-Y"),$this->obj->default_value("date"),'valeur date');
		$this->assertEquals($this->id_contact,$this->obj->default_value("id_contact"),'valeur id_contact');
		$this->assertEquals(14000,$this->obj->default_value("prix"),'valeur prix');
		$this->assertEquals("Tu demande_refi",$this->obj->default_value("description"),'valeur description');
		$this->assertEquals(ATF::affaire()->getCompteTLoyerActualise($affaire),$this->obj->default_value("loyer_actualise"),'valeur loyer_actualise');
		$this->assertEquals(NULL,$this->obj->default_value("id_refinanceur"),'valeur refinanceur');
		$this->assertEquals("NC",$this->obj->default_value("score"),'valeur score');
		$this->assertEquals("NC",$this->obj->default_value("avis_credit"),'valeur avis_credit');


		$id_demande_refi=$this->obj->decryptId($this->obj->insert(array("date"=>date("Y-m-d"),"id_contact"=>$this->id_contact,"id_refinanceur"=>1,"id_affaire"=>$id_affaire,"id_societe"=>$this->id_societe,"description"=>"Tu description"),$this->s,NULL,$refresh));


		$this->assertEquals("NC",$this->obj->default_value("score"),'valeur score');

		ATF::_r('id_affaire', '');
		ATF::_r('id_demande_refi',$id_demande_refi);
		$this->assertEquals("NC",$this->obj->default_value("avis_credit"),'valeur avis_credit');
	}

//	@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
//
	public function test_can_delete(){

		$id_affaire=ATF::affaire()->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu"));
		$id_demande_refi=$this->obj->decryptId($this->obj->i(array("date"=>date("Y-m-d"),"id_contact"=>$this->id_contact,"id_refinanceur"=>1,"id_affaire"=>$id_affaire,"id_societe"=>$this->id_societe,"description"=>"Tu description")));
		$this->assertTrue($this->obj->can_delete($id_demande_refi),'On doit pouvoir supprimer une demande_refi qui n est pas valide');
		$this->assertTrue($this->obj->can_update($id_demande_refi),'On doit pouvoir modifier une demande_refi qui n est pas valide');

		$id_facture=ATF::facture()->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"prix"=>1000,"date"=>date("Y-m-d"),"tva"=>"19.6","id_affaire"=>$id_affaire,"id_demande_refi"=>$id_demande_refi));
		try {
			$this->obj->can_delete($id_demande_refi);
		} catch (errorATF $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(878,$error,'On ne doit pas pouvoir supprimer une demande_refi qui a une facture');
		try {
			$this->obj->can_update($id_demande_refi);
		} catch (errorATF $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(878,$error,'On ne doit pas pouvoir modifier une demande_refi qui a une facture');


		ATF::facture()->d($id_facture);
		$this->obj->u(array("id_demande_refi"=>$id_demande_refi,"etat"=>"valide"));
		try {
			$this->obj->can_delete($id_demande_refi);
		} catch (errorATF $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(877,$error,'On ne doit pas pouvoir supprimer une demande_refi qui est valide');
		try {
			$this->obj->can_update($id_demande_refi);
		} catch (errorATF $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(877,$error,'On ne doit pas pouvoir moodifier une demande_refi qui est valide');

	}


	//	@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	public function test_insert(){
		$this->devis["devis"]=array(
								 "id_societe" => $this->id_societe
								,"id_filiale" => 246
								,"date" => date("Y-m-d")
								,"id_contact" => $this->id_contact
								,"devis" => "Tu demande_refi"
								,"type_contrat" => "lld"
								,"validite" => date("Y-m-d",strtotime(date("Y-m-d")."- 15 day"))
								,"id_opportunite" =>NULL
								,"tva" => "1.196"
								,"prix" => "14 000.00"
								,"prix_achat" => "4 641.00"
								,"marge" => "66.85"
								,"marge_absolue" => "9 359.00"
        );

		$this->devis["values_devis"] = array(
             "loyer" => '[{"loyer__dot__loyer":"1000","loyer__dot__duree":"14","loyer__dot__assurance":"","loyer__dot__frais_de_gestion":"","loyer__dot__frequence_loyer":"mois","loyer__dot__loyer_total":14000}]'
            ,"produits" => '[{"devis_ligne__dot__produit":"Optiplex GX520 TFT 19","devis_ligne__dot__quantite":"1","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"DEL-WRK-OPTGX520-19","devis_ligne__dot__prix_achat":"10","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"DELL|#ref=164a1c62808dc1a3af6f7d99051db73b","devis_ligne__dot__visibilite_prix":"visible","devis_ligne__dot__id_produit_fk":"9","devis_ligne__dot__id_fournisseur_fk":"1351"},{"devis_ligne__dot__produit":"XSERIES 226","devis_ligne__dot__quantite":"1","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"O2-SRV-226-001","devis_ligne__dot__prix_achat":"3113.00","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"AUDIOPTIC TRADE SERVICES|#ref=c0529cb381c6dcf43fc554b910ce02e9","devis_ligne__dot__visibilite_prix":"visible","devis_ligne__dot__id_produit_fk":"5","devis_ligne__dot__id_fournisseur_fk":"1358"},{"devis_ligne__dot__produit":"Optiplex GX520 TFT 17","devis_ligne__dot__quantite":"2","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"DEL-WRK-OPTGX520-17","devis_ligne__dot__prix_achat":"759.00","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"DELL|#ref=164a1c62808dc1a3af6f7d99051db73b","devis_ligne__dot__visibilite_prix":"visible","devis_ligne__dot__id_produit_fk":"8","devis_ligne__dot__id_fournisseur_fk":"1351"}]'
        );

		$refresh = array();
		$this->id_devis=classes::decryptId(ATF::devis()->insert($this->devis));
		$id_affaire=ATF::devis()->select($this->id_devis,"id_affaire");
		$id_demande_refi=$this->obj->decryptId($this->obj->insert(array("date"=>date("Y-m-d"),"id_contact"=>$this->id_contact,"id_refinanceur"=>1,"id_affaire"=>$id_affaire,"id_societe"=>$this->id_societe,"description"=>"Tu description"),$this->s,NULL,$refresh));
		$this->assertTrue(is_array($this->obj->select($id_demande_refi)),'la demande_refi ne c est pas bien inséré');
		$this->assertEquals("demande_refi",ATF::affaire()->select($id_affaire,"etat"),'l etat de l affaire n a pas changé');

		$infos["demande_refi"]["date"]=date("Y-m-d");
		$infos["demande_refi"]["id_contact"]=$this->id_contact;
		$infos["demande_refi"]["id_refinanceur"]=1;
		$infos["demande_refi"]["id_affaire"]=$id_affaire;
		$infos["demande_refi"]["id_societe"]=$this->id_societe;
		$infos["demande_refi"]["description"]="Tu description";
		$infos["demande_refi"]["etat"]="passage_comite";

		$id_demande_refi=$this->obj->decryptId($this->obj->insert($infos,$this->s,NULL,$refresh));
		$this->assertTrue(is_array($this->obj->select($id_demande_refi)),'la demande_refi ne c est pas bien inséré');
		$this->assertEquals("demande_refi",ATF::affaire()->select($id_affaire,"etat"),'l etat de l affaire n a pas changé');

		$infos["demande_refi"]["etat"]="accepte";
		$infos["preview"]=true;
		$id_demande_refi=$this->obj->decryptId($this->obj->insert($infos,$this->s,NULL,$refresh));
		$this->assertTrue(is_array($this->obj->select($id_demande_refi)),'la demande_refi ne c est pas bien inséré');
		$this->assertEquals("demande_refi",ATF::affaire()->select($id_affaire,"etat"),'l etat de l affaire n a pas changé');

		$infos["preview"]=false;
		$id_demande_refi=$this->obj->decryptId($this->obj->insert($infos,$this->s,NULL,$refresh));
		$this->assertTrue(is_array($this->obj->select($id_demande_refi)),'la demande_refi ne c est pas bien inséré');
		$this->assertEquals("demande_refi",ATF::affaire()->select($id_affaire,"etat"),'l etat de l affaire n a pas changé');

		ATF::$msg->getNotices();


	}

	/* @author Nicolas BERTEMONT <nbertemont@absystech.fr> */
	public function testId_refinanceur(){
		$id_soc=ATF::societe()->i(array("societe"=>"soc lol"));
		$id_aff=ATF::affaire()->i(array("ref"=>"ref lol","id_societe"=>$id_soc,"affaire"=>"aff lol"));
		$id_refinanceur=ATF::refinanceur()->i(array("refinanceur"=>"CLEODIS","code"=>"123","code_refi"=>"123"));
		$id_contact=ATF::contact()->i(array("nom"=>"nom lol"));
		$id_demande_refi=ATF::demande_refi()->i(array("id_contact"=>$id_contact,"date"=>date("Y-m-d"),"id_refinanceur"=>$id_refinanceur,"id_affaire"=>$id_aff,"id_societe"=>$id_soc,"description"=>"des lol","etat"=>"valide"));

		$this->assertEquals($id_refinanceur,$this->obj->id_refinanceur($id_aff),"L identifiant refinanceur retourné est incorrect");
	}


	//	@author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	public function test_update(){
		$this->devis["devis"]=array(
								 "id_societe" => $this->id_societe
								,"id_filiale" => 246
								,"date" => date("Y-m-d")
								,"id_contact" => $this->id_contact
								,"devis" => "Tu demande_refi"
								,"type_contrat" => "lld"
								,"validite" => date("Y-m-d",strtotime(date("Y-m-d")."- 15 day"))
								,"id_opportunite" =>NULL
								,"tva" => "1.196"
								,"prix" => "14 000.00"
								,"prix_achat" => "4 641.00"
								,"marge" => "66.85"
								,"marge_absolue" => "9 359.00"
        );

		$this->devis["values_devis"] = array(
             "loyer" => '[{"loyer__dot__loyer":"1000","loyer__dot__duree":"14","loyer__dot__assurance":"","loyer__dot__frais_de_gestion":"","loyer__dot__frequence_loyer":"mois","loyer__dot__loyer_total":14000}]'
            ,"produits" => '[{"devis_ligne__dot__produit":"Optiplex GX520 TFT 19","devis_ligne__dot__quantite":"1","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"DEL-WRK-OPTGX520-19","devis_ligne__dot__prix_achat":"10","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"DELL|#ref=164a1c62808dc1a3af6f7d99051db73b","devis_ligne__dot__visibilite_prix":"visible","devis_ligne__dot__id_produit_fk":"9","devis_ligne__dot__id_fournisseur_fk":"1351"},{"devis_ligne__dot__produit":"XSERIES 226","devis_ligne__dot__quantite":"1","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"O2-SRV-226-001","devis_ligne__dot__prix_achat":"3113.00","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"AUDIOPTIC TRADE SERVICES|#ref=c0529cb381c6dcf43fc554b910ce02e9","devis_ligne__dot__visibilite_prix":"visible","devis_ligne__dot__id_produit_fk":"5","devis_ligne__dot__id_fournisseur_fk":"1358"},{"devis_ligne__dot__produit":"Optiplex GX520 TFT 17","devis_ligne__dot__quantite":"2","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"DEL-WRK-OPTGX520-17","devis_ligne__dot__prix_achat":"759.00","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"DELL|#ref=164a1c62808dc1a3af6f7d99051db73b","devis_ligne__dot__visibilite_prix":"visible","devis_ligne__dot__id_produit_fk":"8","devis_ligne__dot__id_fournisseur_fk":"1351"}]'
        );

		$refresh = array();
		$this->id_devis=classes::decryptId(ATF::devis()->insert($this->devis));
		$id_affaire=ATF::devis()->select($this->id_devis,"id_affaire");
		$id_demande_refi=$this->obj->decryptId($this->obj->insert(array("date"=>date("Y-m-d"),"id_contact"=>$this->id_contact,"id_refinanceur"=>1,"id_affaire"=>$id_affaire,"id_societe"=>$this->id_societe,"description"=>"Tu description"),$this->s,NULL,$refresh));
		$this->assertTrue(is_array($this->obj->select($id_demande_refi)),'la demande_refi ne s est pas bien inséré');
		$this->assertEquals("demande_refi",ATF::affaire()->select($id_affaire,"etat"),'l etat de l affaire n a pas changé');

		$infos["demande_refi"]["date"]=date("Y-m-d");
		$infos["demande_refi"]["id_contact"]=$this->id_contact;
		$infos["demande_refi"]["id_refinanceur"]=1;
		$infos["demande_refi"]["id_affaire"]=$id_affaire;
		$infos["demande_refi"]["id_societe"]=$this->id_societe;
		$infos["demande_refi"]["description"]="Tu description";
		$id_demande_refi=$this->obj->decryptId($this->obj->insert($infos,$this->s,NULL,$refresh));
		$this->assertTrue(is_array($this->obj->select($id_demande_refi)),'la demande_refi ne s est pas bien inséré');

		$infos["demande_refi"]["id_demande_refi"]=$id_demande_refi;
		$infos["demande_refi"]["etat"]="passage_comite";
		$infos["preview"]=true;
		$this->obj->decryptId($this->obj->update($infos,$this->s,NULL,$refresh));


		unset($infos["preview"]);
		$infos["demande_refi"]["suivi_notifie"] = array(0=>35);
		$this->obj->decryptId($this->obj->update($infos,$this->s,NULL,$refresh));
		$etat = ATF::demande_refi()->select($id_demande_refi, "etat");
		$this->assertEquals("passage_comite",$etat, "Etat ne s'est pas mis à jour !");


		$infos["demande_refi"]["etat"]="accepte";
		$this->obj->decryptId($this->obj->update($infos,$this->s,NULL,$refresh));
		$etat = ATF::demande_refi()->select($id_demande_refi, "etat");
		$this->assertEquals("accepte",$etat, "Etat ne s'est pas mis à jour !");

		ATF::$msg->getNotices();
	}


	 /* @author NMorgan FLEURQUIN <mfleurquin@absystech.fr>
    * @date 08/12/2015
    */
    public function test_uploadFileFromSA() {
        $this->devis["devis"]=array(
								 "id_societe" => $this->id_societe
								,"id_filiale" => 246
								,"date" => date("Y-m-d")
								,"id_contact" => $this->id_contact
								,"devis" => "Tu demande_refi"
								,"type_contrat" => "lld"
								,"validite" => date("Y-m-d",strtotime(date("Y-m-d")."- 15 day"))
								,"id_opportunite" =>NULL
								,"tva" => "1.196"
								,"prix" => "14 000.00"
								,"prix_achat" => "4 641.00"
								,"marge" => "66.85"
								,"marge_absolue" => "9 359.00"
        );

		$this->devis["values_devis"] = array(
             "loyer" => '[{"loyer__dot__loyer":"1000","loyer__dot__duree":"14","loyer__dot__assurance":"","loyer__dot__frais_de_gestion":"","loyer__dot__frequence_loyer":"mois","loyer__dot__loyer_total":14000}]'
            ,"produits" => '[{"devis_ligne__dot__produit":"Optiplex GX520 TFT 19","devis_ligne__dot__quantite":"1","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"DEL-WRK-OPTGX520-19","devis_ligne__dot__prix_achat":"10","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"DELL|#ref=164a1c62808dc1a3af6f7d99051db73b","devis_ligne__dot__visibilite_prix":"visible","devis_ligne__dot__id_produit_fk":"9","devis_ligne__dot__id_fournisseur_fk":"1351"},{"devis_ligne__dot__produit":"XSERIES 226","devis_ligne__dot__quantite":"1","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"O2-SRV-226-001","devis_ligne__dot__prix_achat":"3113.00","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"AUDIOPTIC TRADE SERVICES|#ref=c0529cb381c6dcf43fc554b910ce02e9","devis_ligne__dot__visibilite_prix":"visible","devis_ligne__dot__id_produit_fk":"5","devis_ligne__dot__id_fournisseur_fk":"1358"},{"devis_ligne__dot__produit":"Optiplex GX520 TFT 17","devis_ligne__dot__quantite":"2","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"DEL-WRK-OPTGX520-17","devis_ligne__dot__prix_achat":"759.00","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"DELL|#ref=164a1c62808dc1a3af6f7d99051db73b","devis_ligne__dot__visibilite_prix":"visible","devis_ligne__dot__id_produit_fk":"8","devis_ligne__dot__id_fournisseur_fk":"1351"}]'
        );

		$refresh = array();
		$this->id_devis=classes::decryptId(ATF::devis()->insert($this->devis));
		$id_affaire=ATF::devis()->select($this->id_devis,"id_affaire");
		$id_demande_refi=$this->obj->decryptId($this->obj->insert(array("date"=>date("Y-m-d"),"id_contact"=>$this->id_contact,"id_refinanceur"=>1,"id_affaire"=>$id_affaire,"id_societe"=>$this->id_societe,"description"=>"Tu description"),$this->s,NULL,$refresh));
		$this->assertTrue(is_array($this->obj->select($id_demande_refi)),'la demande_refi ne c est pas bien inséré');
		$this->assertEquals("demande_refi",ATF::affaire()->select($id_affaire,"etat"),'l etat de l affaire n a pas changé');

		$infos["demande_refi"]["date"]=date("Y-m-d");
		$infos["demande_refi"]["id_contact"]=$this->id_contact;
		$infos["demande_refi"]["id_refinanceur"]=1;
		$infos["demande_refi"]["id_affaire"]=$id_affaire;
		$infos["demande_refi"]["id_societe"]=$this->id_societe;
		$infos["demande_refi"]["description"]="Tu description";
		$infos["demande_refi"]["etat"]="passage_comite";

		$this->id_demande_refi=$this->obj->decryptId($this->obj->insert($infos,$this->s,NULL,$refresh));

        $infos = array(
            "extAction"=>"demande_refi"
        );
        $this->assertFalse($this->obj->uploadFileFromSA($infos),"Erreur, pas d'id en entrée, renvoi FALSE");
        $infos = array(
            "id"=>$this->id_demande_refi
        );
        $this->assertFalse($this->obj->uploadFileFromSA($infos),"Erreur, pas de class en entrée, renvoi FALSE");

        $infos['extAction'] = "demande_refi";
        $infos['field'] = "tu";
        $this->assertFalse($this->obj->uploadFileFromSA($infos),"Erreur, pas de files en entrée, renvoi FALSE");

        $file = __ABSOLUTE_PATH__."test/cleodis/pdf_exemple.pdf";
        $files = array(
            "tu"=> array(
                "name"=>"pdf_exemple"
                ,"type"=>"application/pdf"
                ,"tmp_name"=>$file
                ,"error"=>0
                ,"size"=>filesize($file)
            )
        );
        if(!file_exists(__ABSOLUTE_PATH__."../temp/testsuite/demande_refi/"))util::mkdir(__ABSOLUTE_PATH__."../temp/testsuite/demande_refi/");
        if(!file_exists(__ABSOLUTE_PATH__."../temp/testsuite/pdf_affaire/"))util::mkdir(__ABSOLUTE_PATH__."../temp/testsuite/pdf_affaire/");

        $r = $this->obj->uploadFileFromSA($infos,ATF::_s(),$files);
        $this->assertEquals('{"success":true}',$r,"Erreur dans le retour de l'upload");
        $f = __ABSOLUTE_PATH__."../data/testsuite/demande_refi/".$this->id_demande_refi.".tu";
        $this->assertTrue(file_exists($f),"Erreur : le fichier n'est pas là !");
        unlink($f);

        ATF::pdf_affaire()->q->reset()->where("id_affaire",$id_affaire);
        $pdf_affaire = ATF::pdf_affaire()->select_all();

        $f = __ABSOLUTE_PATH__."../data/testsuite/pdf_affaire/".$pdf_affaire[0]["id_pdf_affaire"].".fichier_joint";
        $this->assertTrue(file_exists($f),"Erreur : le fichier pdf_affaire n'est pas là !");
        unlink($f);

    }

};
?>