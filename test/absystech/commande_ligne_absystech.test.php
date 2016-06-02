<?
class commande_ligne_absystech_test extends ATF_PHPUnit_Framework_TestCase {
	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	/*besoin d'un user pour les traduction*/
	function setUp() {
		$this->initUser();

		$contact["nom"]="Tu_devis";
		$this->id_contact=ATF::contact()->insert($contact);
		$this->devis["devis"]["id_contact"]=$this->id_contact;
		$this->devis["devis"]['resume']='Tu_devis';
		$this->devis["devis"]['id_societe']=1;
		$this->devis["devis"]['validite']=date('Y-m-d');
		$this->devis["devis"]['prix']="200";
		$this->devis["devis"]['frais_de_port']="50";
		$this->devis["devis"]['prix_achat']="50";
		$this->devis["devis"]['date']=date("Y-m-d");
		$this->devis["values_devis"]=array("produits"=>'[{"devis_ligne__dot__ref":"TU","devis_ligne__dot__produit":"Tu_devis","devis_ligne__dot__quantite":"15","devis_ligne__dot__poids":"10","devis_ligne__dot__prix":"10","devis_ligne__dot__prix_achat":"1","devis_ligne__dot__id_fournisseur":"1","devis_ligne__dot__id_compte_absystech_fk":"1","devis_ligne__dot__marge":97.14,"devis_ligne__dot__id_fournisseur_fk":"1"}]');

		$this->id_devis = ATF::devis()->insert($this->devis,$this->s);
		$this->id_affaire = ATF::devis()->select($this->id_devis,"id_affaire");
	
		//Commande
		$commande["commande"]=$this->devis["devis"];
		$commande["commande"]["id_affaire"]=$this->id_affaire;
		$commande["commande"]["id_devis"]=$this->id_devis;
		
		//Commande_ligne
		$commande["values_commande"]=array("produits"=>'[{"commande_ligne__dot__ref":"TU","commande_ligne__dot__produit":"Tu_commande","commande_ligne__dot__quantite":"15","commande_ligne__dot__prix":"10","commande_ligne__dot__prix_achat":"1","commande_ligne__dot__id_fournisseur":"1","commande_ligne__dot__id_compte_absystech_fk":"1","commande_ligne__dot__marge":97.14,"commande_ligne__dot__id_fournisseur_fk":"1"}]');

		//Insertion
		unset($commande["commande"]["id_contact"]);
		unset($commande["commande"]["validite"]);
		$this->id_commande = ATF::commande()->insert($commande,$this->s);
		$this->commande=$commande;
		$this->commande_ligne = $this->obj->select_special("id_commande",classes::decryptId($this->id_commande));

		//Facture
		$facture["facture"]=$this->commande["commande"];
		$facture["facture"]["tva"]=1.2;
		$facture["facture"]["id_affaire"]=$this->id_affaire;
		
		//Facture_ligne
		$facture["values_facture"]=array("produits"=>'[{"facture_ligne__dot__ref":"TU","facture_ligne__dot__produit":"Tu_facture","facture_ligne__dot__quantite":"15","facture_ligne__dot__prix":"10","facture_ligne__dot__prix_achat":"10","facture_ligne__dot__id_fournisseur":"1","facture_ligne__dot__serial":"777","facture_ligne__dot__id_compte_absystech_fk":"1","facture_ligne__dot__marge":97.14,"facture_ligne__dot__id_fournisseur_fk":"1"}]');

		//Insertion
		unset($facture["facture"]["resume"]);
		unset($facture["facture"]["prix_achat"]);
		unset($facture["facture"]["id_devis"]);
		$this->facture=$facture;
		$this->id_facture = ATF::facture()->insert($facture,$this->s);

	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function tearDown(){
		ATF::db()->rollback_transaction(true);
	}
	
	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function testSelect_all(){
		$fields=array($this->obj->table.".ref",$this->obj->table.".produit",$this->obj->table.".quantite",$this->obj->table.".poids",$this->obj->table.".prix",$this->obj->table.".prix_achat",$this->obj->table.".id_fournisseur",$this->obj->table.".id_compte_absystech");
		$this->obj->q->reset()->where("id_commande",classes::decryptId($this->id_commande))->setView(array("order"=>$fields));
		$select_all=$this->obj->select_all(false,false,false,true);
		$this->assertEquals($select_all['count'],1,"1 select_all renvoie le bon count");
		$this->assertEquals($select_all['data'][0]["ref"],"TU","2 select_all renvoie le bon data");

		$this->obj->q->reset()->where("id_commande","aaaaaaaaaa")->setView(array("order"=>$fields));
		$select_all=$this->obj->select_all(false,false,false,true);
		$this->assertEquals($select_all['count'],0,"3 select_all renvoie le bon count");
		$this->assertNull($select_all['data'][0],array(),"4 select_all renvoie le bon data");
	
	}
	
	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function testToFactureLigne(){
		$fields=array(
			"facture_ligne.ref"
			, "facture_ligne.produit"
			, "facture_ligne.quantite"
			, "facture_ligne.prix"
			, "facture_ligne.id_fournisseur"
			, "facture_ligne.id_compte_absystech"
			, "facture_ligne.serial"
		);
		$q=ATF::_s("pager")->getAndPrepare("factureProduitsUpdate");
		$q->reset()->where("id_commande",classes::decryptId($this->id_commande))->setView(array("order"=>$fields))->setCount();
		$this->obj->setQuerier($q);
		$toFactureLigne=$this->obj->toFactureLigne();
		$toFactureLigne_attendu=array(
										"data"=>array(
														"0"=>array(
																	"facture_ligne.id_produit"=>"",
																	"facture_ligne.produit"=>$this->commande_ligne[0]["produit"],
																	"facture_ligne.quantite"=>$this->commande_ligne[0]["quantite"],
																	"facture_ligne.ref"=>$this->commande_ligne[0]["ref"],
																	"facture_ligne.prix"=>$this->commande_ligne[0]["prix"],
																	"facture_ligne.id_fournisseur"=>"AbsysTech",
																	"facture_ligne.id_compte_absystech"=>"",
																	"facture_ligne.id_produit_fk"=>"",
																	"facture_ligne.id_fournisseur_fk"=>"1",
																	"facture_ligne.id_compte_absystech_fk"=>"",
																	"facture_ligne.id_facture_ligne"=>$this->commande_ligne[0]["id_commande_ligne"],
																	"facture_ligne.serial"=>$this->commande_ligne[0]["serial"],
																	"facture_ligne.marge"=>90,
																	"facture_ligne.marge_absolue"=>135
																	)
														),
										"count"=>"1"
										);


		$this->assertEquals($toFactureLigne_attendu["data"][0]["facture_ligne.produit"],$toFactureLigne["data"][0]["facture_ligne.produit"],"tofactureLigne ne renvoi pas le bon produit");
		$this->assertEquals($toFactureLigne_attendu["data"][0]["facture_ligne.quantite"],$toFactureLigne["data"][0]["facture_ligne.quantite"],"tofactureLigne ne renvoi pas la bonne quantite");
		$this->assertEquals($toFactureLigne_attendu["data"][0]["facture_ligne.ref"],$toFactureLigne["data"][0]["facture_ligne.ref"],"tofactureLigne ne renvoi pas le bon ref");
		$this->assertEquals($toFactureLigne_attendu["data"][0]["facture_ligne.poids"],$toFactureLigne["data"][0]["facture_ligne.poids"],"tofactureLigne ne renvoi pas le bon poids");
		$this->assertEquals($toFactureLigne_attendu["data"][0]["facture_ligne.prix"],$toFactureLigne["data"][0]["facture_ligne.prix"],"tofactureLigne ne renvoi pas le bon prix");
		$this->assertEquals($toFactureLigne_attendu["data"][0]["facture_ligne.id_fournisseur"],$toFactureLigne["data"][0]["facture_ligne.id_fournisseur"],"tofactureLigne ne renvoi pas le bon id_fournisseur");
		$this->assertEquals($toFactureLigne_attendu["data"][0]["facture_ligne.marge"],$toFactureLigne["data"][0]["facture_ligne.marge"],"tofactureLigne ne renvoi pas la bonne marge");
		$this->assertEquals($toFactureLigne_attendu["data"][0]["facture_ligne.marge_absolue"],$toFactureLigne["data"][0]["facture_ligne.marge_absolue"],"tofactureLigne ne renvoi pas la bonne marge_absolue");
	}
	
	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function testselectOnlyNotYetOrderedQuantities(){
		$fields=array(
			"commande_ligne.ref"
			, "commande_ligne.produit"
			, "commande_ligne.quantite"
		);
		$q=ATF::_s("pager")->getAndPrepare("testselectOnlyNotYetOrderedQuantities");
		$q->reset()->where("id_commande",classes::decryptId($this->id_commande))->setView(array("order"=>$fields))->setCount();
		$this->obj->setQuerier($q);
		$post = array(
			"pager"=>"testselectOnlyNotYetOrderedQuantities"
			,"id_commande"=>"1538"
			,"id_fournisseur"=>"636"
		);
							
		$selectOnlyNotYetOrderedQuantities=$this->obj->selectOnlyNotYetOrderedQuantities($post);
		$this->assertEquals("090129-141838-14-AT",$selectOnlyNotYetOrderedQuantities[0]["bon_de_commande_ligne.serial"],"selectOnlyNotYetOrderedQuantities ne renvoi pas les bonnes infos");
	}
	
	
	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function testselectOnlyNotYetOrderedQuantities2(){
		log::logger("-------------------------------------------------------------------" , "mfleurquin");

		$fields=array(
			"commande_ligne.ref"
			, "commande_ligne.produit"
			, "commande_ligne.quantite"
		);
		$q=ATF::_s("pager")->getAndPrepare("testselectOnlyNotYetOrderedQuantities");
		$q->reset()->where("id_commande",classes::decryptId($this->id_commande))->setView(array("order"=>$fields))->setCount();
		$this->obj->setQuerier($q);
		$post = array(
			"pager"=>"testselectOnlyNotYetOrderedQuantities"
			,"id_commande"=>$this->id_commande
			,"id_fournisseur"=>"1"
		);
		
		
		$bdc["bon_de_commande"] = $this->commande["commande"];
		unset($bdc["bon_de_commande"]["id_devis"]);
		$bdc["bon_de_commande"]["ref"]="REF_bon_de_commande";
		$bdc["bon_de_commande"]["id_commande"] = $this->id_commande;
		$bdc["bon_de_commande"]["prix"] = 150;
		$bdc["bon_de_commande"]["id_fournisseur"] = "1";
		$bdc["bon_de_commande"]["id_fournisseurFinal"] = "1";
		 
		$bdc["values_bon_de_commande"] = array("produits"=>'[{"bon_de_commande_ligne__dot__ref":"TU",
															"bon_de_commande_ligne__dot__produit":"Tu_commande",
															"bon_de_commande_ligne__dot__quantite":"5",
															"bon_de_commande_ligne__dot__prix":"10",
															"bon_de_commande_ligne__dot__prix_achat":"1",
															"bon_de_commande_ligne__dot__id_fournisseur":"1",
															"bon_de_commande_ligne__dot__id_compte_absystech_fk":"1",
															"bon_de_commande_ligne__dot__marge":97.14,
															"bon_de_commande_ligne__dot__id_fournisseur_fk":"1"}]');
				
		$id_bdc = ATF::bon_de_commande()->insert($bdc);
							
		$selectOnlyNotYetOrderedQuantities=$this->obj->selectOnlyNotYetOrderedQuantities($post);		
		//$this->assertEquals("090129-141838-14-AT",$selectOnlyNotYetOrderedQuantities[0]["bon_de_commande_ligne.serial"],"selectOnlyNotYetOrderedQuantities ne renvoi pas les bonnes infos");
	}
	

	function constructCommande(){
		
		$this->id_commande=ATF::commande()->i(array(
												"date"=>date("Y-m-d"),
												"ref"=>"TU Commande",
												"id_societe"=>$this->id_societe,
												"tva"=>"1.2"
											)
										);
		
		$this->id_commande_ligne1=$this->obj->i(array(
														"id_commande"=>$this->id_commande,
														"produit"=>"produit TU",
														"quantite"=>2,
														"prix"=>10,
														"prix_achat"=>5)
												);

		$this->id_commande_ligne2=$this->obj->i(array(
														"id_commande"=>$this->id_commande,
														"produit"=>"produit TU",
														"quantite"=>2,
														"prix"=>20,
														"prix_achat"=>10)
												);

	}
	
	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	/*function testInsert(){
		$this->constructCommande();
		
		$commande_ligne=array(
								"id_commande"=>$this->id_commande,
								"produit"=>"produit TU",
								"quantite"=>2,
								"prix"=>30,
								"prix_achat"=>15
							);		
		
		$this->assertNotNull($this->obj->insert($commande_ligne),"Commande_ligne ne s'insère pas");
		$commande=ATF::commande()->select($this->id_commande);
		$this->assertEquals(120,$commande["prix"],"le prix de la commande ne s'est pas mis à jour sur insert");
		$this->assertEquals(60,$commande["prix_achat"],"le prix achat de la commande ne s'est pas mis à jour sur insert");
	}*/

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	/*function testDelete(){
		$this->constructCommande();

		$this->assertTrue($this->obj->delete(array("id"=>array("0"=>$this->id_commande_ligne1))),"Commande_ligne ne se supprime pas");
		$commande=ATF::commande()->select($this->id_commande);
		$this->assertEquals(40,$commande["prix"],"le prix de la commande ne s'est pas mis à jour sur delete");
		$this->assertEquals(20,$commande["prix_achat"],"le prix achat de la commande ne s'est pas mis à jour sur delete");
	}*/

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	/*function testUpdate(){
		$this->constructCommande();
		
		$this->assertEquals(1,$this->obj->update(array("id_commande_ligne"=>$this->id_commande_ligne1,"id_commande"=>$this->id_commande,"quantite"=>3,"prix"=>6,"prix_achat"=>3)),"Commande_ligne ne se modifie pas");
		$commande=ATF::commande()->select($this->id_commande);
		$this->assertEquals(58,$commande["prix"],"le prix de la commande ne s'est pas mis à jour sur update");
		$this->assertEquals(29,$commande["prix_achat"],"le prix achat de la commande ne s'est pas mis à jour sur update");
	}*/

	/**
	* @author Morgan Fleurquin <mfleurquin@absystech.fr>
	*/
	function test_can_update(){
		try{
			$this->obj->can_update();
		}catch (error $e) {
			$error = $e->getMessage();
		}			
		$this->assertEquals($error , "Pour modifier une ligne de commande, il faut modifier dans la commande !!!" , "Pas de message d'erreur?? On ne peux pas update une ligne facture sans passer dans la facture normalement !!");
	}
	
	/**
	* @author Morgan Fleurquin <mfleurquin@absystech.fr>
	*/
	function test_can_insert(){
		try{
			$this->obj->can_insert();
		}catch (error $e) {
			$error = $e->getMessage();
		}			
		$this->assertEquals($error , "Pour inserer une ligne de commande, il faut modifier dans la commande !!!" , "Pas de message d'erreur?? On ne peux pas update une ligne facture sans passer dans la facture normalement !!");
	}
	
	/**
	* @author Morgan Fleurquin <mfleurquin@absystech.fr>
	*/
	/*function test_can_delete(){
		try{
			$this->obj->can_delete();
		}catch (error $e) {
			$error = $e->getMessage();
		}			
		$this->assertEquals($error , "Pour supprimer une ligne de commande, il faut modifier dans la commande !!!" , "Pas de message d'erreur?? On ne peux pas update une ligne facture sans passer dans la facture normalement !!");
	}*/
	
	
	
	
};
?>