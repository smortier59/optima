<?
class devis_absystech_test extends ATF_PHPUnit_Framework_TestCase {

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */
	function setUp() {
		$this->initUser();

		$contact["nom"]="Tu_devis";
		$this->id_contact=ATF::contact()->insert($contact);
		$this->devis["devis"]["id_contact"]=$this->id_contact;
		$this->devis["devis"]["date"]=date("Y-m-d");
		$this->devis["devis"]['resume']='Tu_devis';
		$this->devis["devis"]['id_societe']=1;
		$this->devis["devis"]['validite']=date('Y-m-d');
		$this->devis["devis"]['prix']="200";
		$this->devis["devis"]['frais_de_port']="50";
		$this->devis["devis"]['prix_achat']="50";
		$this->devis["values_devis"]=array("produits"=>'[{"devis_ligne__dot__ref":"TU","devis_ligne__dot__produit":"Tu_devis","devis_ligne__dot__quantite":"15","devis_ligne__dot__poids":"10","devis_ligne__dot__prix":"10","devis_ligne__dot__prix_achat":"10","devis_ligne__dot__id_fournisseur":"1","devis_ligne__dot__id_compte_absystech":"1","devis_ligne__dot__marge":97.14,"devis_ligne__dot__id_fournisseur_fk":"1"}]');

		$this->id_devis = $this->obj->insert($this->devis,$this->s);
		$this->id_affaire = $this->obj->select($this->id_devis,"id_affaire");
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */
	function tearDown(){
		ATF::db()->rollback_transaction(true);
		//Flush des notices
		ATF::$msg->getNotices();
	}


	// @author Yann GAUTHERON <ygautheron@absystech.fr>
	public function testAutocompleteConditions(){
		$this->assertEquals(
			array("condition_field"=>array("contact.id_societe","contact.truc"),"condition_value"=>array(3,2)),
			$this->obj->autocompleteConditions(ATF::contact(),array("devis"=>array("id_societe"=>3)),"truc",2),
			"Le couple condition_field/value est incorrect"
		);
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */
	function testInsert(){
		$devis["devis"]["date"]=date('Y-m-d');
		$devis["devis"]["id_contact"]="A";
		$devis["devis"]['resume']='Tu_devis';
		$devis["devis"]['id_societe']="A";
		$devis["devis"]['validite']=date('Y-m-d');
		$devis["devis"]['filestoattach']["fichier_joint"]="true";
		$devis['preview']=true;
		$devis['emailTexte']["mail"]="TU";

		$devis["devis"]['frais_de_port']="50";
		$devis["devis"]['prix_achat']="50";


		// Erreur 1
		try {
			$id_devis = $this->obj->insert($devis,$this->s);
		} catch (errorATF $e) {
			$erreur_trouvee1 = $e->getCode();
		}

		$this->assertEquals(602,$erreur_trouvee1,"ERREUR 1 NON ATTRAPPEE (contact non insere)");

		$devis["devis"]["id_contact"]=$this->id_contact;


		// Erreur 2
		try {
			$id_devis = $this->obj->insert($devis,$this->s);
		} catch (errorATF $e) {
			$erreur_trouvee3 = $e->getCode();
		}
		$this->assertEquals(600,$erreur_trouvee3,"ERREUR 2 NON ATTRAPPEE (entite non insere)");
		$devis["devis"]['id_societe']=1;
		$devis["label_devis"]["id_politesse_post"]="Veuillez agréer, Mademoiselle, l'expression de nos sentiments les meilleurs.";

		// Erreur 3
		try {
			$id_devis = $this->obj->insert($devis,$this->s);
		} catch (errorATF $e) {
			$erreur_trouvee2 = $e->getCode();
		}
		$this->assertEquals(600,$erreur_trouvee2,"ERREUR 3 NON ATTRAPPEE (ligne non insere)");

		$devis["values_devis"]=array("produits"=>'[{"devis_ligne__dot__ref":"Tu_devis","devis_ligne__dot__produit":"Tu_devis","devis_ligne__dot__quantite":"15","devis_ligne__dot__poids":"10","devis_ligne__dot__prix":"10","devis_ligne__dot__prix_achat":"10","devis_ligne__dot__id_fournisseur":"JPG|#ref=30c6b9cea3f7ad21798dec631e8150b2","devis_ligne__dot__id_compte_absystech":"VENTES DE MARCHANDISES|#ref=ad7d6cf6f3b6a19470595a1066fe7b47","devis_ligne__dot__marge":97.14,"devis_ligne__dot__id_fournisseur_fk":"9","devis_ligne__dot__id_compte_absystech_fk":"9"},{"devis_ligne__dot__ref":"Tu_devis1","devis_ligne__dot__produit":"Tu_devis","devis_ligne__dot__quantite":"","devis_ligne__dot__poids":"10","devis_ligne__dot__prix":"20","devis_ligne__dot__prix_achat":"10","devis_ligne__dot__id_fournisseur":"1","devis_ligne__dot__id_compte_absystech":"9","devis_ligne__dot__marge":97.14,"devis_ligne__dot__id_fournisseur_fk":"1"}]');
		$devis["label_devis"]["id_politesse_post"]="Veuillez agréer, Monsieur, l'expression de nos sentiments les meilleurs.";


		// Erreur 4
		$erreur_trouvee4 = false;
		try {
			$id_devis = $this->obj->insert($devis,$this->s);
		} catch (errorATF $e) {
			echo "\nERROR => ".$e->getMessage()."\n";
			$erreur_trouvee4 = true;
		}
		$this->assertFalse($erreur_trouvee4,"ERREUR 4 NON ATTRAPPEE (le devis aurait du s'insere)");

		// Erreur 5
		$id_devis = $this->obj->insert($devis,$this->s);
		$file_exist = file_get_contents($this->obj->filepath($this->id_user,"fichier_joint",true));
		$this->assertNotNull($file_exist,"ERREUR 5 NON ATTRAPPEE (Le preview n'a pas été fait)");

		ATF::societe()->u(array("id_societe" => 1, "etat"=> "inactif"));
		try {
			$id_devis = $this->obj->insert($devis,$this->s);
		} catch (errorATF $e) {
			$erreur_trouvee1 = $e->getMessage();
		}
		$this->assertEquals("Impossible d'ajouter un devis car la société est inactive",$erreur_trouvee1,"ERREUR 1 NON ATTRAPPEE (societe inactive)");
		ATF::societe()->u(array("id_societe" => 1, "etat"=> "actif"));

		// Erreur 7
		$devis["devis"]["email"]="mtribouillard@absystech.fr";
		$devis["devis"]["emailTexte"]="Un mail pour la forme";
		$id_devis = $this->obj->insert($devis,$this->s);
		$this->assertNotNull($id_devis,"1 Problème sur l'insert");
		$this->assertEquals("mtribouillard@absystech.fr",$this->obj->select($id_devis,"mail"),"1 L'insert n'a pas enregistré le mail");
		$this->assertEquals("Un mail pour la forme",$this->obj->select($id_devis,"mail_text"),"1 L'insert n'a pas enregistré le mail_text");
		//Flush des notices
		ATF::$msg->getNotices();

		ATF::contact()->u(array("id_contact"=>$this->id_contact,"email"=>"mtribouillard@absystech.fr"));
		$devis["devis"]["email"]="";
		$devis["devis"]["emailTexte"]="";
		$id_devis = $this->obj->insert($devis,$this->s);
		$this->assertNotNull($id_devis,"2 Problème sur l'insert");
		//Flush des notices
		ATF::$msg->getNotices();

		//Affaire
		$id_affaire = $this->obj->select($id_devis,"id_affaire");
		$affaire=ATF::affaire()->select($id_affaire);
		$this->assertEquals(20,$affaire['forecast'],"L'affaire ne prend pas le bon forecast");
		$this->assertEquals("",$affaire2['id_termes'],"L'affaire ne prend pas le bon terme");

		//Devis
		$devis_select=$this->obj->select($id_devis);
		$this->assertEquals("attente",$devis_select['etat'],"Le devis ne prend pas le bon etat attente");
		$this->assertEquals("200",$devis_select['prix'],"Le devis ne prend pas le bon prix");
		$this->assertEquals("50",$devis_select['frais_de_port'],"Le devis ne prend pas le bon frais_de_port");
		$this->assertEquals("50",$devis_select['prix_achat'],"Le devis ne prend pas le bon prix_achat");
		$this->assertEquals("A",$devis_select['revision'],"Le devis ne prend pas le bon revision");

		//Devis ligne
		ATF::devis_ligne()->q->reset()->addCondition("id_devis",$this->obj->decryptId($id_devis))->setDimension("row")->end();
		$devis_ligne=ATF::devis_ligne()->select_all();
		$this->assertEquals("Tu_devis",$devis_ligne['ref'],"Le devis_ligne ne prend pas le bon ref");
		$this->assertEquals("15",$devis_ligne['quantite'],"Le devis_ligne ne prend pas le bon quantite");
		$this->assertEquals("10",$devis_ligne['prix'],"Le devis_ligne ne prend pas le bon prix");
		$this->assertEquals("10",$devis_ligne['poids'],"Le devis_ligne ne prend pas le bon poids");
		$this->assertEquals("10",$devis_ligne['prix_achat'],"Le devis_ligne ne prend pas le bon prix_achat");
		$this->assertEquals("9",$devis_ligne['id_compte_absystech'],"Le devis_ligne ne prend pas le bon id_compte_absystech");

		//Devis bloque avec affaire
		ATF::profil()->u(array("id_profil"=>ATF::$usr->get("id_profil"),"seuil"=>1));
		$devis["devis"]["id_affaire"]=$id_affaire;
		$devis["label_devis"]["id_termes"]= "30% à la commande, le solde à réception de facture";
		$cadre_refresh=array();
		$this->assertEquals(array(),ATF::$msg->getNotices(),"La notice devrait être vide");
		unset($devis['preview']);
		$id_devis = $this->obj->insert($devis,$this->s,NULL,$cadre_refresh);
		$notice = ATF::$msg->getNotices();
		$a = array(
				0=>array(
					//"msg"=>"En tant que Associé vous n'avez pas les droits de faire un devis supérieur à 1 €. Si vous pensez qu'il s'agit d'une erreur, contactez votre supérieur hiérarchique pour qu'il vérifie les droits de votre profil utilisateur.",
					"msg" =>"error_403_devis_seuil",
					"title"=>"Droits d'accès requis pour cette opération ! ",
					"timer"=>"",
					"type"=>"success"
				)
				,1 => array(
					"msg" => "Veuillez prévenir votre supérieur pour débloquer le devis."
					,"title" => "Droits d'accès requis pour cette opération ! "
					,"timer" => "",
					"type"=>"success"
				)

		);

		$this->assertEquals($a,$notice,"1 La notice de devis bloqué ne se fait pas");
//		ATF::profil()->u(array("id_profil"=>ATF::$usr->get("id_profil"),"seuil"=>""));
		$devis_select1=$this->obj->select($id_devis);
		$this->assertEquals("bloque",$devis_select1['etat'],"Le devis ne prend pas le bon etat bloque");

		$affaire2=ATF::affaire()->select($id_affaire);
		$this->assertEquals(2,$affaire2['id_termes'],"L'affaire ne prend pas le bon terme");

		ATF::$usr->set("id_superieur",1);
		$id_devis = $this->obj->insert($devis,$this->s,NULL,$cadre_refresh);

		$this->assertEquals(array(
									0=>array(
										//"msg"=>"En tant que Associé vous n'avez pas les droits de faire un devis supérieur à 1 €. Si vous pensez qu'il s'agit d'une erreur, contactez votre supérieur hiérarchique pour qu'il vérifie les droits de votre profil utilisateur."
										"msg" =>"error_403_devis_seuil"
										,"title"=>"Droits d'accès requis pour cette opération ! "
										,"timer"=>""
										,"type"=>"success"
										)
							),ATF::$msg->getNotices(),"2 La notice de devis bloqué ne se fait pas");

		ATF::user()->u(array("id_user"=>ATF::$usr->get("id_superieur"),"email"=>NULL));
		$id_devis = $this->obj->insert($devis,$this->s,NULL,$cadre_refresh);
		$this->assertEquals(array(
									0=>array(
										//"msg"=>"En tant que Associé vous n'avez pas les droits de faire un devis supérieur à 1 €. Si vous pensez qu'il s'agit d'une erreur, contactez votre supérieur hiérarchique pour qu'il vérifie les droits de votre profil utilisateur."
										'msg' => 'error_403_devis_seuil'
										,"title"=>"Droits d'accès requis pour cette opération ! "
										,"timer"=>""
										,'type'=>"success"
										)
									,1 => array(
										"msg" => "Veuillez prévenir votre supérieur pour débloquer le devis."
										,"title" => "Droits d'accès requis pour cette opération ! "
										,"timer" => ""
										,"type"=>"success"
									)
							),ATF::$msg->getNotices(),"3 La notice de devis bloqué ne se fait pas");



		$id_devis = $this->obj->insert($this->devis,$this->s,NULL,$cadre_refresh);

	}

	/* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */
	function testDelete(){
		$this->assertNotNull($this->obj->select($this->id_devis),"Le devis n'a pas été bien inséré");
		$this->assertNotNull(ATF::affaire()->select($this->id_affaire),"L'affaire n'a pas été bien insérée");

		$this->obj->delete($this->id_devis);
		$this->assertNull($this->obj->select($this->id_devis),"Le devis n'a pas été bien supprimée");
		$this->assertNull(ATF::affaire()->select($this->id_affaire),"L'affaire n'a pas été bien supprimée");

		$devis=$this->devis;
		$devis["devis"]["id_devis"]=$this->obj->insert($this->devis,$this->s);
		$devis["devis"]["id_affaire"]=$this->obj->select($devis["devis"]["id_devis"],"id_affaire");

		$id["id"]["1"] = $this->obj->update($devis,$this->s);
		$id["id"]["2"] = $this->obj->insert($this->devis,$this->s);

		$devis0=$this->obj->select($devis["devis"]["id_devis"]);
		$this->assertNotNull($devis0,"0 Le devis n'a pas été bien inséré");
		$this->assertNull($devis0["etat"],"0 Le devis n'a pas le bon état");
		$this->assertEquals("A",$devis0["revision"],"0 Le devis n'a pas la bonne révision");

		$devis1=$this->obj->select($id["id"]["1"]);
		$this->assertNotNull($devis1,"1 Le devis n'a pas été bien inséré");
		$this->assertEquals("attente",$devis1["etat"],"1 Le devis n'a pas le bon état");
		$this->assertEquals("B",$devis1["revision"],"1 Le devis n'a pas la bonne révision");

		$devis2=$this->obj->select($id["id"]["2"]);
		$this->assertNotNull($devis2,"2 Le devis n'a pas été bien inséré");
		$this->assertEquals("attente",$devis2["etat"],"2 Le devis n'a pas le bon état");
		$this->assertEquals("A",$devis2["revision"],"2 Le devis n'a pas la bonne révision");

		$this->obj->delete($id);
		$this->assertNull($this->obj->select($id["id"]["1"]),"1 Le devis n'a pas été bien supprimée");
		$this->assertNull($this->obj->select($id["id"]["2"]),"2 Le devis n'a pas été bien supprimée");

		$devis0=$this->obj->select($devis["devis"]["id_devis"]);
		$this->assertNotNull($devis0,"0 Le devis n'aurait pas du être supprimé");
		$this->assertEquals("attente",$devis0["etat"],"0 Le devis n'a pas le bon état");

	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */
	function testCan_delete(){
		$this->assertTrue($this->obj->can_delete($this->id_devis),"1 La méthode can_delete ne fonctionne pas");

		//Commande
		$commande["commande"]=$this->devis["devis"];
		$commande["commande"]["id_affaire"]=$this->id_affaire;
		$commande["commande"]["id_devis"]=$this->id_devis;

		//Commande_ligne
		$commande["values_commande"]=array("produits"=>'[{"commande_ligne__dot__ref":"TU","commande_ligne__dot__produit":"Tu_commande","commande_ligne__dot__quantite":"15","commande_ligne__dot__prix":"10","commande_ligne__dot__prix_achat":"10","commande_ligne__dot__id_fournisseur":"1","commande_ligne__dot__id_compte_absystech":"1","commande_ligne__dot__marge":97.14,"commande_ligne__dot__id_fournisseur_fk":"1"}]');

		//Insertion
		unset($commande["commande"]["id_contact"]);
		unset($commande["commande"]["validite"]);
		$id_commande = ATF::commande()->insert($commande,$this->s);


		try {
			$this->obj->can_delete($this->id_devis);
		} catch (errorATF $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(892,$error,"2 La méthode can_delete ne fonctionne pas");

	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */
	function testCan_update(){
		$this->assertTrue($this->obj->can_update($this->id_devis),"1 La méthode can_update ne fonctionne pas");

		//Commande
		$commande["commande"]=$this->devis["devis"];
		$commande["commande"]["id_affaire"]=$this->id_affaire;
		$commande["commande"]["id_devis"]=$this->id_devis;

		//Commande_ligne
		$commande["values_commande"]=array("produits"=>'[{"commande_ligne__dot__ref":"TU","commande_ligne__dot__produit":"Tu_commande","commande_ligne__dot__quantite":"15","commande_ligne__dot__prix":"10","commande_ligne__dot__prix_achat":"10","commande_ligne__dot__id_fournisseur":"1","commande_ligne__dot__id_compte_absystech":"1","commande_ligne__dot__marge":97.14,"commande_ligne__dot__id_fournisseur_fk":"1"}]');

		//Insertion
		unset($commande["commande"]["id_contact"]);
		unset($commande["commande"]["validite"]);

		$id_commande = ATF::commande()->insert($commande,$this->s);

		try {
			$this->obj->can_update($this->id_devis);
		} catch (errorATF $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(892,$error,"2 La méthode can_update ne fonctionne pas");

		$this->assertFalse($this->obj->can_update("aaaaa"),"3 La méthode can_update ne renvoie pas false lorsque le devis n'existe pas");

		ATF::societe()->u(array("id_societe" => 1, "etat"=> "inactif"));
		try {
			$this->obj->can_update($this->id_devis);
		} catch (errorATF $e) {
			$error = $e->getMessage();
		}
		$this->assertEquals("Impossible de modifier un devis car la société est inactive",$error,"3 Ne renvoi pas l'erreur sur la societe fermée !");

	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */
	function testPerdu(){
		//Devis
		$infos["id_devis"] = $this->id_devis;

		//Commande
		$commande["commande"]=$this->devis["devis"];
		$commande["commande"]["id_affaire"]=$this->id_affaire;
		$commande["commande"]["id_devis"]=$this->id_devis;

		//Commande_ligne
		$commande["values_commande"]=array("produits"=>'[{"commande_ligne__dot__ref":"TU","commande_ligne__dot__produit":"Tu_commande","commande_ligne__dot__quantite":"15","commande_ligne__dot__prix":"10","commande_ligne__dot__prix_achat":"10","commande_ligne__dot__id_fournisseur":"1","commande_ligne__dot__id_compte_absystech":"1","commande_ligne__dot__marge":97.14,"commande_ligne__dot__id_fournisseur_fk":"1"}]');

		//Insertion
		unset($commande["commande"]["id_contact"]);
		unset($commande["commande"]["validite"]);
		$id_commande = ATF::commande()->insert($commande,$this->s);

		$devis = $this->obj->select($this->id_devis);
		$this->assertEquals("gagne",$devis["etat"],"1 Le devis ne prend pas le bon etat 'gagne'");

		$this->assertFalse($this->obj->perdu(array("id_devis"=>$this->id_devis)),"2 La méthode perdu ne renvoie pas false");
		$devis = $this->obj->select($this->id_devis);
		$this->assertEquals("gagne",$devis["etat"],"3 Le devis ne doit pas passer en 'perdu' s'il est en état 'gagné'");

		ATF::commande()->delete($id_commande,$this->s);
		$devis = $this->obj->select($this->id_devis);
		$this->assertEquals("attente",$devis["etat"],"4 Le devis ne prend pas le bon etat 'attente'");


    	$notices = array(array(
            'msg' => 'Suppression de l\'enregistrement \'CSO17030001\' avec succès.',
            'title' => 'Succès !',
            'timer' => '',
            'type' => 'success')
        );


		$this->assertEquals($notices,ATF::$msg->getNotices(),"5 La notice devrait être vide");
		$this->obj->perdu($infos,$this->s);
		//$this->assertEquals(array(0=>array("msg"=>"Le devis '".$this->obj->select($this->id_devis,"ref")."' a bien été passé en 'Perdu'","title"=>"Succès !","timer"=>"")),ATF::$msg->getNotices(),"6 La notice en état perdu ne se fait pas");

		$this->assertEquals(array(0=>array("msg"=>"notice_devis_perdu","title"=>"Succès !","timer"=>"","type"=>"success")),ATF::$msg->getNotices(),"6 La notice en état perdu ne se fait pas");

		$devis = $this->obj->select($this->id_devis);
		$this->assertEquals("perdu",$devis["etat"],"7 Le devis doit passer en 'perdu' s'il est en état 'attente'");
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */
	/*@author Quentin JANON <qjanon@absystech.fr> */
	function testUnlock(){
		$this->obj->u(array(
			"id_devis"=>$this->id_devis
			,"etat"=>"bloque"
			,"mail"=>"qjanon@absystech.fr"
			,"mail_text"=>"Mail pour le devis bloque"
		));
		$this->assertEquals("bloque",$this->obj->select($this->id_devis,"etat"),"L'etat bloque ne s'est pas bien mis");

		ATF::user()->u(array("id_user"=>$this->id_user,"email"=>""));

		try {
			$this->obj->unlock(array("id_devis"=>$this->id_devis),$this->s);
		} catch (errorATF $e) {
			$erreur_trouvee1 = $e->getCode();
		}

		$this->assertEquals("12",$erreur_trouvee1,"Le devis ne doit pas etre validé s'il n'y a pas de mail");

		ATF::user()->u(array("id_user"=>$this->id_user,"email"=>"mtribouillard@absystech.fr"));

		$this->assertEquals(array(),ATF::$msg->getNotices(),"La notice devrait être vide");
		$this->obj->unlock(array("id_devis"=>$this->id_devis),$this->s);
		$r = array(
			0=>array(
				"msg"=>"A cette adresse mail : qjanon@absystech.fr"
				,"title"=>"Email envoyé"
				,'timer' => null
		        ,'type' => 'success'
			)
			,1=>array(
				"msg"=>"Ce devis est maintenant validé."
				,"title"=>"Succès !"
				,'timer' => null
		        ,'type' => 'success'

			)
		);
		$this->assertEquals($r,ATF::$msg->getNotices(),"6 La notice en état valide ne se fait pas");
		$this->assertEquals("attente",$this->obj->select($this->id_devis,"etat"),"L'etat attente ne s'est pas bien mis");



	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */
	function testUpdate(){
		$devis_update=$this->devis;
		$devis_update["devis"]["id_affaire"]=$this->id_affaire;
		$devis_update["devis"]["id_"."devis"]=$this->id_devis;
		$devis_update["preview"]=true;
		$id_devis=$this->obj->update($devis_update,$this->s);
		$this->obj->q->reset()->addCondition("id_affaire",$this->id_affaire)->end();
		$devis_update=$this->obj->select_all();
		$this->assertEquals("B",$devis_update[0]["revision"],"L'update ne change pas la révision");
		$this->assertEquals($this->id_affaire,$devis_update[0]["id_affaire"],"L'update ne change pas la révision");

		$devis_update=$this->devis;
		$devis_update["devis"]["id_affaire"]=$this->id_affaire;
		$devis_update["devis"]["id_"."devis"]=$id_devis;
		$devis_update["preview"]=false;
		$this->obj->update($devis_update,$this->s);
		$this->obj->q->reset()->addCondition("id_affaire",$this->id_affaire)->end();
		$devis_update=$this->obj->select_all();
		$this->assertEquals("C",$devis_update[0]["revision"],"L'update ne change pas la révision");
		$this->assertEquals($this->id_affaire,$devis_update[0]["id_affaire"],"L'update ne change pas la révision");
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */
	function testSelect_all(){
		$devis_update=$this->devis;
		$devis_update["devis"]["id_affaire"]=$this->id_affaire;
		$devis_update["devis"]["id_"."devis"]=$this->id_devis;
		$this->obj->update($devis_update,$this->s);
		$this->obj->q->reset()->addCondition("id_affaire",$this->id_affaire)->end();
		$devis_update=$this->obj->select_all();
		$this->assertEquals("B",$devis_update[0]["revision"],"1 Le sous-requêtage de select_all ne prend pas la révision");
		$this->assertNull($devis_update[1]["revision"],"2 Le sous-requêtage de select_all ne prend pas la révision");

		$this->obj->q->reset()->setCount();
		$r = $this->obj->select_all();
		$r = $r['data'];

		$this->assertArrayHasKey("allowCmd",$r[0],"Il manque une des valeurs (allowCmd) utiles aux boutons EXTJS");
		$this->assertArrayHasKey("allowUnlockDevis",$r[0],"Il manque une des valeurs (allowUnlockDevis) utiles aux boutons EXTJS");
		$this->assertArrayHasKey("allowCancel",$r[0],"Il manque une des valeurs (allowCancel) utiles aux boutons EXTJS");

		$this->assertFalse($r[0]["allowCmd"],"Le allowCmd doit être a FALSE");
		$this->assertFalse($r[0]["allowUnlockDevis"],"Le allowUnlockDevis doit être a FALSE");
		$this->assertFalse($r[0]["allowCancel"],"Le allowCancel doit être a FALSE");

		$this->obj->q->reset()->setCount()->addField("devis.etat");
		$r = $this->obj->select_all();
		$r = $r['data'];

		$this->assertTrue($r[0]["allowCmd"],"Le allowCmd doit être a TRUE");
		$this->assertFalse($r[0]["allowUnlockDevis"],"Le allowUnlockDevis doit être a FALSE");
		$this->assertTrue($r[0]["allowCancel"],"Le allowCancel doit être a TRUE");

		$r[0]['etat'] = 'bloque';
		$r[0]['id_devis'] = $r[0]['devis.id_devis'];
		unset($r[0]['allowCmd'],$r[0]['allowUnlockDevis'],$r[0]['allowCancel'],$r[0]['devis.id_devis'],$r[0]['devis.etat'],$r[0]['fichier_joint']);
		ATF::devis()->u($r[0]);

		$this->obj->q->reset()->setCount()->addField("devis.etat");
		$r = $this->obj->select_all();
		$r = $r['data'];

		$this->assertFalse($r[0]["allowCmd"],"Le allowCmd doit être a FALSE");
		$this->assertTrue($r[0]["allowUnlockDevis"],"Le allowUnlockDevis doit être a TRUE");
		$this->assertTrue($r[0]["allowCancel"],"Le allowCancel doit être a TRUE");
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */
	function testtoutesRevisions(){
		$devis_update=$this->devis;
		$devis_update["devis"]["id_affaire"]=$this->id_affaire;
		$devis_update["devis"]["id_"."devis"]=$this->id_devis;
		$this->obj->update($devis_update,$this->s);
		$this->obj->q->reset()->addCondition("id_affaire",$this->id_affaire)->end();
		$devis_update=$this->obj->toutesRevisions();
		$this->assertEquals("B",$devis_update[0]["revision"],"1 toutesRevisions ne renvoie pas tous les devis");
		$this->assertEquals("A",$devis_update[1]["revision"],"2 toutesRevisions ne renvoie pas tous les devis");
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */
	function testStats(){
		/* nombre de taches */
		$graph=$this->obj->stats();
		$this->assertTrue(is_array($graph),"Méthode stats non fonctionnel");
		$this->assertTrue(isset($graph['params']),"Méthode stats non fonctionnel (niveau params)");
		$this->assertTrue(isset($graph['categories']['category']),"Méthode stats non fonctionnel (niveau categories)");
		$this->assertTrue(isset($graph['dataset']),"Méthode stats non fonctionnel (niveau dataset)");

		/* nombre de taches créé pour un user */
		$graph=$this->obj->stats(false,'user');
		$this->assertTrue(is_array($graph),"user Méthode stats non fonctionnel");
		$this->assertTrue(isset($graph['params']),"Méthode stats non fonctionnel (user niveau params)");
		$this->assertTrue(isset($graph['categories']['category']),"Méthode stats non fonctionnel (user niveau categories)");
		$this->assertTrue(isset($graph['dataset']),"Méthode stats non fonctionnel (user niveau dataset)");

		/* nombre de taches créé pour tous les users */
		$graph=$this->obj->stats(false,'users');
		$this->assertTrue(is_array($graph),"users Méthode stats non fonctionnel");
		$this->assertTrue(isset($graph['params']),"users Méthode stats non fonctionnel (niveau params)");
		$this->assertTrue(isset($graph['categories']['category']),"users Méthode stats non fonctionnel (niveau categories)");
		$this->assertTrue(isset($graph['dataset']),"users Méthode stats non fonctionnel (niveau dataset)");

		/* nombre de taches créé pour tous les pipe */
		$graph=$this->obj->stats(false,'pipe');
		$this->assertTrue(is_array($graph),"pipe Méthode stats non fonctionnel");
		$this->assertTrue(isset($graph['params']),"pipe Méthode stats non fonctionnel (niveau params)");
		$this->assertTrue(isset($graph['categories']['category']),"pipe Méthode stats non fonctionnel (niveau categories)");
		$this->assertTrue(isset($graph['dataset']),"pipe Méthode stats non fonctionnel (niveau dataset)");
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */
	function testGetTotalPipe(){
		$this->obj->q->reset()
			->addCondition("devis.etat","attente")
			->addCondition("devis.etat","bloque")
			->setDimension('cell')
			->addField("SUM(`devis`.`prix`)","nb");
		$getTotalPipe_sa=$this->obj->sa();
		$getTotalPipe=$this->obj->getTotalPipe();
		$this->assertEquals($getTotalPipe_sa,$getTotalPipe,"1 getTotalPipe ne renvoie pas tous le bon montant sans forcast");

		$this->obj->q->reset()
			->addCondition("devis.etat","attente")
			->addCondition("devis.etat","bloque")
			->setDimension('cell')
			->addJointure("devis","id_affaire","affaire","id_affaire")
			->addField("SUM(`devis`.`prix`*`affaire`.`forecast`/100)","nb");
		$getTotalPipe_sa=$this->obj->sa();
		$getTotalPipe=$this->obj->getTotalPipe(true);
		$this->assertEquals($getTotalPipe_sa,$getTotalPipe_sa,"2 getTotalPipe ne renvoie pas tous le bon montant avec forcast");
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */
	function testGetTotalPipePondere(){
		$this->obj->q->reset()
			->addCondition("devis.etat","attente")
			->addCondition("devis.etat","bloque")
			->setDimension('cell')
			->addJointure("devis","id_affaire","affaire","id_affaire")
			->addField("SUM(`devis`.`prix`*`affaire`.`forecast`/100)","nb");
		$getTotalPipe_sa=$this->obj->sa();
		$getTotalPipePondere=$this->obj->getTotalPipePondere();
		$this->assertEquals($getTotalPipe_sa,$getTotalPipePondere,"2 getTotalPipePondere ne renvoie pas tous le bon montant");
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */
	function testAnnule(){
		$this->obj->u(array("id_devis"=>$this->id_devis,"etat"=>"gagne"));
		$this->assertFalse($this->obj->annule(array("id_devis"=>$this->id_devis)),"1 La méthode annule ne doit pas permettre d'annuler un devis gagne");

		$this->obj->u(array("id_devis"=>$this->id_devis,"etat"=>"attente"));
		$this->devis["devis"]['id_devis']=$this->id_devis;

		$last_id=$this->obj->update($this->devis,$this->s);
		$this->assertEquals(array(),ATF::$msg->getNotices(),"La notice devrait être vide");
		$this->assertTrue($this->obj->annule(array("id_devis"=>$last_id)),"2 La méthode annule doit permettre de passer la révision précédente en ''");
		$this->assertEquals(array(0=>array("msg"=>"notice_devis_annule","title"=>"Succès !",'timer' => null,'type' => 'success')),ATF::$msg->getNotices(),"3 La notice en état annulé ne se fait pas");

		$devis = $this->obj->select($last_id);
		$this->assertEquals("annule",$devis["etat"],"4 Le devis doit passer en 'annule' s'il est en état 'attente'");

		$devis = $this->obj->select($this->id_devis);
		$this->assertNull($devis["etat"],"5 Le devis doit repasser en 'attente' si sa révision a été annulé");

		$this->assertEquals(array(),ATF::$msg->getNotices(),"La notice devrait être vide");
		$this->assertTrue($this->obj->annule(array("id_devis"=>$this->id_devis)),"6 La méthode annule doit permettre d'annuler un devis attente");
		$this->assertEquals(array(0=>array("msg"=>"notice_devis_annule","title"=>"Succès !",'timer' => null,'type' => 'success')),ATF::$msg->getNotices(),"7 La notice en état annulé ne se fait pas");

		$devis = $this->obj->select($this->id_devis);
		$this->assertEquals("annule",$devis["etat"],"8 Le devis doit passer en 'annule' s'il est en état 'attente'");
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
	function testDefault_value(){

		$id_termes=$this->obj->default_value("id_termes");
		$this->assertEquals("",$id_termes,"1 default_value ne renvoie pas le bon 'id_termes' sans devis");

		$sous_total=$this->obj->default_value("sous_total");
		$this->assertEquals(0,$sous_total,"default_value ne renvoie pas le bon 'sous_total' sans devis");

		$marge=$this->obj->default_value("marge");
		$this->assertEquals(0,$marge,"default_value ne renvoie pas le bon 'marge' sans devis");

		$marge_absolue=$this->obj->default_value("marge_absolue");
		$this->assertEquals(0,$marge_absolue,"default_value ne renvoie pas le bon 'marge_absolue' sans devis");

		$email=$this->obj->default_value("email");
		$this->assertEquals("",$email,"default_value ne renvoie pas le bon 'email' sans devis");

		ATF::_r('id_societe',$this->id_societe);
		ATF::societe()->update(array("id_societe"=>$this->id_societe,"id_termes"=>1));
		$id_termes=$this->obj->default_value("id_termes");
		$this->assertEquals(1,$id_termes,"2 default_value ne renvoie pas le bon 'id_termes'");

		ATF::_r('id_devis',$this->id_devis);
		$id_termes=$this->obj->default_value("id_termes");
		$this->assertEquals("",$id_termes,"3 default_value ne renvoie pas le bon 'id_termes'");

		ATF::contact()->update(array("id_contact"=>$this->id_contact,"email"=>"debugemail@absystech.fr"));
		$email=$this->obj->default_value("email");
		$this->assertEquals("debugemail@absystech.fr",$email,"default_value ne renvoie pas le bon 'email'");

		$emailCopie=$this->obj->default_value("emailCopie");
		$this->assertEquals("debug@absystech.fr",$emailCopie,"default_value ne renvoie pas le bon 'emailCopie'");

		$sous_total=$this->obj->default_value("sous_total");
		$this->assertEquals("150",$sous_total,"default_value ne renvoie pas le bon 'sous_total'");
		$marge=$this->obj->default_value("marge");
		$this->assertEquals("75%",$marge,"default_value ne renvoie pas le bon 'marge'");

		$marge_absolue=$this->obj->default_value("marge_absolue");
		$this->assertEquals("100",$marge_absolue,"default_value ne renvoie pas le bon 'marge'");

		$default_value=$this->obj->default_value("default_value");
		$this->assertNull($default_value,"default_value ne renvoie pas une bonne valeur pour le field default_value");

		ATF::_r('id',$this->id_devis);
		$sous_total=$this->obj->default_value("sous_total");
		$this->assertEquals("150",$sous_total,"sous_total ne renvoie pas le bon 'sous_total avec id'");

		$objet=$this->obj->default_value("objet");
		$this->assertEquals("Devis ref : ".$this->obj->select($this->id_devis,"ref"),$objet,"objet ne renvoie pas le bon 'objet'");

		$texte=$this->obj->default_value("texte");
		$this->assertNotNull($texte,"objet ne renvoie pas le bon 'texte'");

		$fichier_joint=$this->obj->default_value("fichier_joint");
		$this->assertTrue($fichier_joint,"objet ne renvoie pas le bon 'fichier_joint'");
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */
	function testSelect(){
		$devis=$this->obj->select($this->id_devis);
		$this->assertNotEquals("",$devis["date"],"select ne prend pas la date");
		$this->assertNotEquals("",$devis["validite"],"select ne prend pas la validité");

		ATF::_r('event',"cloner");
		$devis_cloner=$this->obj->select($this->id_devis);
		$this->assertEquals("",$devis_cloner["date"],"select cloner prend la date");
		$this->assertEquals("",$devis_cloner["validite"],"select cloner prend la validité");
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */
	function testCloner(){
		$devis["devis"]=$this->obj->select($this->id_devis);
		$devis["devis"]["revision"]="B";
		$devis["devis"]["etat"]="gagne";
		$devis["devis"]["cause_perdu"]="tu";
		$devis["values_devis"]=array("produits"=>'[{"devis_ligne__dot__ref":"TU","devis_ligne__dot__produit":"Tu_devis","devis_ligne__dot__quantite":"15","devis_ligne__dot__poids":"10","devis_ligne__dot__prix":"10","devis_ligne__dot__prix_achat":"10","devis_ligne__dot__id_fournisseur":"1","devis_ligne__dot__id_compte_absystech":"1","devis_ligne__dot__marge":97.14,"devis_ligne__dot__id_fournisseur_fk":"1"}]');

		$id_devis=$this->obj->cloner($devis,$this->s);
		$devis_cloner=$this->obj->select($id_devis);

		$this->assertNotEquals($devis["devis"]["ref"],$devis_cloner["ref"],"cloner ne met pas à jour la ref");
		$this->assertNotEquals($devis["devis"]["revision"],$devis_cloner["revision"],"cloner ne met pas à jour la revision");
		$this->assertNotEquals($devis["devis"]["etat"],$devis_cloner["etat"],"cloner ne met pas à jour la etat");
		$this->assertNotEquals($devis["devis"]["cause_perdu"],$devis_cloner["cause_perdu"],"cloner ne met pas à jour la cause_perdu");
		$this->assertNotEquals($devis["devis"]["id_affaire"],$devis_cloner["id_affaire"],"cloner ne met pas à jour la id_affaire");
		$this->assertNotEquals($devis["devis"]["id_devis"],$devis_cloner["id_devis"],"cloner ne met pas à jour la id_devis");

	}

	/*@author Yann GAUTHERON <ygautheron@absystech.fr> */
	function testFiltrageParProfilApporteurAffaire(){
		ATF::$usr->set('id_profil',11);
		ATF::$usr->set('id_user',1);
		$this->obj->sa();
		$this->assertEquals("devis.id_user = '1'",$this->obj->q->getWhere('devis.id_user'));
	}

	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 22-08-2011
	*/
	function test_annulation_perdu(){
		$this->assertFalse($this->obj->annulation($params),"Aucun paramètre en entrée, doit retourner FALSE");

		$params = array("action"=>"perdu","id"=>$this->id_devis,"raison"=>"C'est la loose");

		$this->assertTrue($this->obj->annulation($params),"Doit retourner TRUE si succés");
		$this->assertEquals("perdu",$this->obj->select($this->id_devis,'etat'),"Mauvais état après la perte...");
		$this->assertEquals("C'est la loose",$this->obj->select($this->id_devis,'cause_perdu'),"Mauvaise cause_perdu après la perte...");
		$this->assertEquals(1,count(ATF::$msg->getNotices()),"Il n'y a pas le bon nombre de notice");
		$this->assertEquals("perdue",ATF::affaire()->select($this->id_affaire,'etat'),"Affaire Mauvais état après la perte...");
		$this->assertEquals("0",ATF::affaire()->select($this->id_affaire,'forecast'),"Affaire Mauvais forecast après la perte...");
	}

	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 22-08-2011
	*/
	function test_annulation_annule(){
		$params = array("action"=>"annule","id"=>$this->id_devis,"raison"=>"C'est la loose pour l'annulation");

		$this->assertTrue($this->obj->annulation($params),"Doit retourner TRUE si succés");
		$this->assertEquals("annule",$this->obj->select($this->id_devis,'etat'),"Mauvais état après l'annulation...");
		$this->assertEquals("C'est la loose pour l'annulation",$this->obj->select($this->id_devis,'cause_perdu'),"Mauvaise cause_perdu après l'annulation...");
		$this->assertEquals(1,count(ATF::$msg->getNotices()),"Il n'y a pas le bon nombre de notice");
		$this->assertEquals("perdue",ATF::affaire()->select($this->id_affaire,'etat'),"Affaire Mauvais état après la perte...");
		$this->assertEquals("0",ATF::affaire()->select($this->id_affaire,'forecast'),"Affaire Mauvais forecast après la perte...");
	}

	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 22-08-2011
	*/
	function test_annulation_replace(){
		ATF::devis()->q->setLimit(1)->addField('id_devis')->setDimension('cell');
		$id_devis = ATF::devis()->sa();

		$params = array("action"=>"replace","id"=>$this->id_devis,"id_devis"=>$id_devis,"raison"=>"C'est la loose pour le remplacement");

		$this->assertEquals(1,$this->obj->annulation($params),"Doit retourner TRUE si succés");
		$this->assertEquals("remplace",$this->obj->select($this->id_devis,'etat'),"Mauvais état après le remplacement...");
		$this->assertEquals($id_devis,$this->obj->select($this->id_devis,'id_remplacant'),"Mauvais état après le remplacement...");
		$this->assertEquals("C'est la loose pour le remplacement",$this->obj->select($this->id_devis,'cause_perdu'),"Mauvaise cause_perdu après le remplacement...");
		$this->assertEquals("perdue",ATF::affaire()->select($this->id_affaire,'etat'),"Affaire Mauvais état après la perte...");
		$this->assertEquals("0",ATF::affaire()->select($this->id_affaire,'forecast'),"Affaire Mauvais forecast après la perte...");
	}

	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 22-08-2011
	*/
	function test_annulation_default(){
		$params = array("action"=>"repgfsdfgsrgserlace","id"=>$this->id_devis);

		$this->assertFalse($this->obj->annulation($params),"Doit retourner FALSE si ca passe par le défault");
	}

	/*@author Quentin JANON <qjanon@absystech.fr> */
	function test_getRefMail(){
		$devis=$this->obj->select($this->id_devis);
		$this->assertEquals($devis["ref"]."-".$devis["revision"],$this->obj->getRefMail($this->id_devis),"La ref retourné n'est pas correct");
	}


	/*@author Quentin JANON <qjanon@absystech.fr> */
	function test_sendMailDevis_sansTextMail(){
		$this->assertFalse($this->obj->sendMailDevis($this->id_devis,""),"Erreur de mailText inexistant ne retourne pas false");
	}

	/*@author Quentin JANON <qjanon@absystech.fr> */
	function test_sendMailDevis_sansMail(){
		$this->obj->u(array(
			"id_devis"=>$this->id_devis
			,"mail_text"=>"uogiugv guvi gvuogjvjhbiuh ugtfvhyfcvuhj "
		));
		$this->begin_transaction(true);
		$erreur = false;
		try {
			$this->obj->sendMailDevis($this->id_devis);
		} catch (errorATF $e) {
			$erreur = true;
		}

		$this->assertTrue($erreur,"Erreur de mail inexistant non remontée");
		$this->assertEquals(1054,$e->getCode(),"Mauvais code d'Erreur de mail inexistant");
	}

	/*@author Quentin JANON <qjanon@absystech.fr> */
	function test_sendMailDevis_zipOpenError(){
		$this->obj->u(array(
			"id_devis"=>$this->id_devis
			,"mail_text"=>"uogiugv guvi gvuogjvjhbiuh ugtfvhyfcvuhj "
			,"mail"=>"qjanon@absystech.fr"
		));

		$p = $this->obj->filepath($this->obj->decryptId($this->id_devis),"documentAnnexes");
		ATF::util()->file_put_contents($p,"outougvogu");
		$this->assertFileExists($p,"Fichier annexe non crée");

		$this->begin_transaction(true);
		$erreur = false;
		try {
			$this->obj->sendMailDevis($this->id_devis);
		} catch (errorATF $e) {
			$erreur = true;
		}

		$this->assertTrue($erreur,"Erreur de zip non ouvert non remontée");
		$this->assertEquals(501,$e->getCode(),"Mauvais code d'Erreur de problème de zip");
	}

	/*@author Quentin JANON <qjanon@absystech.fr> */
	function test_sendMailDevis_zipSuccess(){
		$this->obj->u(array(
			"id_devis"=>$this->id_devis
			,"mail_text"=>"uogiugv guvi gvuogjvjhbiuh ugtfvhyfcvuhj "
			,"mail"=>"qjanon@absystech.fr"
			,"mail_copy"=>"qjanon@absystech.fr"
		));

		$p = $this->obj->filepath($this->obj->decryptId($this->id_devis),"documentAnnexes");
		ATF::util()->file_put_contents($p,"");
		$zip = new ZipArchive;
        $src = array(
			"/home/optima/core/test/absystech/commande.test.php"
			,"/home/optima/core/test/absystech/devis.test.php"
		);
        if ($zip->open($p) === true) {
            foreach ($src as $item)
                if (file_exists($item)) {
                    $zip->addFile($item);
				}
            $zip->close();
        }

		$this->assertFileExists($p,"Fichier annexe non crée");

		$this->obj->sendMailDevis($this->id_devis);

		$r = array(
			0 => array(
				"msg" => "A cette adresse mail : qjanon@absystech.fr"
				,"title" => "Email envoyé"
				,'timer' => null,
				'type' => 'success'
			)
    		,1 => array(
				"msg" => "A cette adresse mail : qjanon@absystech.fr"
				,"title" => "Email envoyé en copie"
				,'timer' => null,
				'type' => 'success'
			)
		);

		$this->assertEquals($r,ATF::$msg->getNotices(),"La notice de success n'est pas là");
	}

	// @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	// @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	// @author Yann GAUTHERON <ygautheron@absystech.fr>
	function testRecentForMobile(){
		$getRecentForMobile=$this->obj->getRecentForMobile();
		// NB : je laisse à Mathieu le soin de faire un test plus approprié en créant des éléments de test
		if($getRecentForMobile){
			$this->assertTrue($getRecentForMobile[count($getRecentForMobile)-1]["date"]>date("Y-m-d H:i:s",time()-86400*30),"getRecentForMobile renvoi des devis antérieur à 30 jours !");
		}else{
			$this->assertTrue(false,"Le dernier devis date de plus de 30 jours ?");
		}
	}
};
?>