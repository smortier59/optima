<?
class facture_paiement_test extends ATF_PHPUnit_Framework_TestCase {
	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function setUp() {
		$this->initUser();


		//Devis
		$this->devis["devis"]["id_contact"]=$this->id_contact;
		$this->devis["devis"]['resume']='Tu_devis';
		$this->devis["devis"]['id_societe']=$this->id_societe;
		$this->devis["devis"]['validite']=date('Y-m-d');
		$this->devis["devis"]['prix']="200";
		$this->devis["devis"]['frais_de_port']="50";
		$this->devis["devis"]['prix_achat']="50";
		$this->devis["devis"]["date"]=date('Y-m-d');
		
		//Devis_ligne
		$this->devis["values_devis"]=array("produits"=>'[{"devis_ligne__dot__ref":"TU","devis_ligne__dot__produit":"Tu_devis","devis_ligne__dot__quantite":"15","devis_ligne__dot__poids":"10","devis_ligne__dot__prix":"10","devis_ligne__dot__prix_achat":"10","devis_ligne__dot__id_fournisseur":"1","devis_ligne__dot__id_compte_absystech":"1","devis_ligne__dot__marge":97.14,"devis_ligne__dot__id_fournisseur_fk":"1"}]');

		//Insertion
		$this->id_devis = ATF::devis()->insert($this->devis,$this->s);
		$this->id_affaire = ATF::devis()->select($this->id_devis,"id_affaire");
	
		//Commande
		$this->commande["commande"]=$this->devis["devis"];
		$this->commande["commande"]["id_affaire"]=$this->id_affaire;
		$this->commande["commande"]["id_devis"]=$this->id_devis;
		
		//Commande_ligne
		$this->commande["values_commande"]=array("produits"=>'[{"commande_ligne__dot__ref":"TU","commande_ligne__dot__produit":"Tu_commande","commande_ligne__dot__quantite":"15","commande_ligne__dot__prix":"10","commande_ligne__dot__prix_achat":"10","commande_ligne__dot__id_fournisseur":"1","commande_ligne__dot__id_compte_absystech":"1","commande_ligne__dot__marge":97.14,"commande_ligne__dot__id_fournisseur_fk":"1"}]');

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
		$this->facture["values_facture"]=array("produits"=>'[{"facture_ligne__dot__ref":"TU","facture_ligne__dot__produit":"Tu_facture","facture_ligne__dot__quantite":"15","facture_ligne__dot__prix":"10","facture_ligne__dot__prix_achat":"10","facture_ligne__dot__id_fournisseur":"1","facture_ligne__dot__serial":"777","facture_ligne__dot__id_compte_absystech":"1","facture_ligne__dot__marge":97.14,"facture_ligne__dot__id_fournisseur_fk":"1"}]');

		//Insertion
		unset($this->facture["facture"]["resume"],$this->facture["facture"]["prix_achat"],$this->facture["facture"]["id_devis"]);
		$this->id_facture = ATF::facture()->insert($this->facture,$this->s);
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function tearDown(){
		$this->rollback_transaction();
	}


	/*@author Quentin JANON <qjanon@absystech.fr> */ 
	function testInsertAvoir(){

		//Facture
		$avoir["facture"]=$this->commande["commande"];
		$avoir["facture"]["date"]="2010-01-02";
		$avoir["facture"]["id_affaire"]=$this->id_affaire;
		$avoir["facture"]["mode"]="avoir";
		$avoir["facture"]["id_termes"]=2;

		$avoir["facture"]["id_facture_parente"]=$this->id_facture;
		$avoir["facture"]["tva"]=1.2;
		
		//Facture_ligne
		$avoir["values_facture"]=array("produits"=>'[{"facture_ligne__dot__ref":"TU","facture_ligne__dot__produit":"Tu_facture","facture_ligne__dot__quantite":"15","facture_ligne__dot__prix":"10","facture_ligne__dot__prix_achat":"10","facture_ligne__dot__id_fournisseur":"1","facture_ligne__dot__serial":"777","facture_ligne__dot__id_compte_absystech":"1","facture_ligne__dot__marge":97.14,"facture_ligne__dot__id_fournisseur_fk":"1"}]');
		//Insertion
		unset($avoir["facture"]["resume"],$avoir["facture"]["prix_achat"],$avoir["facture"]["id_devis"]);
		$id = ATF::facture()->insert($avoir,$this->s);

		$this->obj->insert(array("id_facture"=>$this->id_facture,"id_facture_avoir"=>$id,"mode_paiement"=>"avoir","date"=>date("Y-m-d")));
		$facture=ATF::facture()->select($this->id_facture);
		$this->assertEquals("payee",$facture["etat"],"Problème sur l'insert de paiement l'état na passe pas à 'payée'");
		
		$avoir = ATF::facture()->select($id); 
		$this->assertEquals("payee",$avoir['etat'],"L'avoir n'est pas en etat payee");
		// mode de paiement lettrage
		$this->obj->q->reset()->where('facture_paiement.id_facture',$avoir['id_facture']);
		$p = $this->obj->select_row();
		$this->assertEquals("lettrage",$p['facture_paiement.mode_paiement'],"L'mode_paiement n'est pas LETTRAGE");

		$notices = ATF::$msg->getNotices();
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function testInsert(){
		$this->obj->insert(array("id_facture"=>$this->id_facture,"montant"=>20,"date"=>date("Y-m-d")));
		$facture=ATF::facture()->select($this->id_facture);
		$this->assertEquals("impayee",$facture["etat"],"Problème sur l'insert de paiement l'état passe à 'payée' alors qu'il est impayé");
		
		$id_commande_facture=ATF::commande_facture()->insert(array("id_facture"=>$this->id_facture,"id_commande"=>$this->id_commande));
		$id = $this->obj->insert(array("id_facture"=>$this->id_facture,"montant"=>299,"date"=>date("Y-m-d")));

		$facture=ATF::facture()->select($this->id_facture);

		$this->assertEquals("payee",$facture["etat"],"Problème sur l'insert de paiement l'état reste à 'impayée' alors qu'il est payé");
		$this->assertNotNull($facture["date_effective"],"Problème sur l'insert de paiement la date effective ne se met pas");

		$affaire=ATF::affaire()->select($this->id_affaire);
		$this->assertEquals("terminee",$affaire["etat"],"L'affaire ne passe pas en terminée");

		$notices = ATF::$msg->getNotices();
		/*$this->assertEquals(array(
									0=>array(
										"msg"=>"La facture '".ATF::facture()->nom($this->id_facture)."' est passée en payée.","title"=>"Succès !","timer"=>""
									),
									1=>array(
										"msg"=>"L'affaire '".ATF::affaire()->nom($this->id_affaire)."' est passée en terminée.","title"=>"Succès !","timer"=>""
									)
							),$notices,"les notices de facture");
*/

	}



	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function testUpdateInteret(){
		ATF::facture()->u(array("id_facture"=>$this->id_facture,"date_previsionnelle"=>date("Y-m-d")));
		$id=$this->obj->insert(array("id_facture"=>$this->id_facture,"montant"=>ATF::facture()->select($this->id_facture,"prix")*ATF::facture()->select($this->id_facture,"tva"),"date"=>date("Y-m-d",strtotime(date("Y-m-d")." + 4 year"))));
		$facture_paiement=$this->obj->select($id);
		$this->assertEquals("86.08",$facture_paiement["montant_interet"],"le montant intérêt n'est pas bon");
		
		$id2=$this->obj->insert(array("id_facture"=>$this->id_facture,"montant"=>10,"date"=>date("Y-m-d",strtotime(date("Y-m-d")))));
		$facture_paiement=$this->obj->select($id);
		$path = $this->obj->filepath($id2,"factureInteret");
		$this->assertFalse(file_exists($path),"le montant intérêt n'est pas bon");
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function testDelete(){
		ATF::$msg->getNotices();
		$this->assertFalse($this->obj->delete(),"facture paiement ne devriat pas se deleter");

		$this->obj->insert(array("id_facture"=>$this->id_facture,"montant"=>20,"date"=>date("Y-m-d")));
		$fp = array("id_facture"=>$this->id_facture,"montant"=>ATF::facture()->select($this->id_facture,"prix")*ATF::facture()->select($this->id_facture,"tva"),"date"=>date("Y-m-d"));
		$id_facture_paiement=$this->obj->insert($fp);
		/*$this->assertEquals(array(
									0=>array(
										"msg"=>"La facture '".ATF::facture()->nom($this->id_facture)."' est passée en payée.","title"=>"Succès !","timer"=>""
										)
							),ATF::$msg->getNotices(),"3 La notice de facture payée ne se fait pas");*/
		$id=array(
					  "id"=>(
							 array(
										0=>$id_facture_paiement
									)
							)
					);
		$this->obj->delete($id);

		$facture=ATF::facture()->select($this->id_facture);
		$this->assertEquals("impayee",$facture["etat"],"Problème sur le delete de paiement l'état reste à 'impayée' alors qu'il est payé");
		$this->assertNull($facture["date_effective"],"Problème sur le delete de paiement la date effective ne se met pas");

		$affaire=ATF::affaire()->select($this->id_affaire);
		$this->assertEquals("facture",$affaire["etat"],"L'affaire ne passe pas en facture");
	}


	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function testFacturePerte(){
		$id_facture_paiement=$this->obj->insert(array("id_facture"=>$this->id_facture,"montant"=>20,"mode_paiement"=>"perte","date"=>date("Y-m-d")));
		$facture=ATF::facture()->select($this->id_facture);
		$this->assertEquals("perte",$this->obj->select($id_facture_paiement,'mode_paiement'),"Problème sur le mode_paiement de la facture paiement Perte");
		$this->assertEquals("perte",$facture["etat"],"Problème sur l'etat de la facture Perte");
	}


	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function testUpdate(){
		$id_commande_facture=ATF::commande_facture()->insert(array("id_facture"=>$this->id_facture,"id_commande"=>$this->id_commande));
		$id_facture_paiement=$this->obj->insert(array("id_facture"=>$this->id_facture,"montant"=>299,"date"=>date("Y-m-d")));

		$this->obj->update(array("id_facture_paiement"=>$id_facture_paiement,"montant"=>10,"id_facture"=>$this->id_facture));
		/*$this->assertEquals(array(
									0=>array(
										"msg"=>"La facture '".ATF::facture()->nom($this->id_facture)."' est passée en payée.","title"=>"Succès !","timer"=>""
										),
									1=>array(
										"msg"=>"L'affaire '".ATF::affaire()->nom($this->id_affaire)."' est passée en terminée.","title"=>"Succès !","timer"=>""
										)
							),ATF::$msg->getNotices(),"les notices de facture");*/
		$facture=ATF::facture()->select($this->id_facture);
		$this->assertEquals("impayee",$facture["etat"],"1 Problème sur l'update de paiement l'état reste à 'impayée' alors qu'il est payé");

		$affaire=ATF::affaire()->select($this->id_affaire);
		$this->assertEquals("facture",$affaire["etat"],"L'affaire ne passe pas en facture");

		$this->obj->update(array("id_facture_paiement"=>$id_facture_paiement,"montant"=>299,"id_facture"=>$this->id_facture));
		/*$this->assertEquals(array(
									0=>array(
										"msg"=>"La facture '".ATF::facture()->nom($this->id_facture)."' est passée en payée.","title"=>"Succès !","timer"=>""
										),
									1=>array(
										"msg"=>"L'affaire '".ATF::affaire()->nom($this->id_affaire)."' est passée en terminée.","title"=>"Succès !","timer"=>""
										)
							),ATF::$msg->getNotices(),"les notices de facture");
		*/
		$facture=ATF::facture()->select($this->id_facture);
		$this->assertEquals("payee",$facture["etat"],"2 Problème sur l'update de paiement l'état reste à 'impayée' alors qu'il est payé");

		$affaire=ATF::affaire()->select($this->id_affaire);
		$this->assertEquals("terminee",$affaire["etat"],"Problème sur l'update de paiement l'état de l'affaire ne passe pas en terminé");
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function test_select_all(){
		$id_facture_paiement=$this->obj->insert(array("id_facture"=>$this->id_facture,"montant"=>10,"date"=>date("Y-m-d")));
		$this->obj->q->reset()->addCondition("facture_paiement.id_facture",ATF::facture()->decryptId($this->id_facture))->setCount();
		$facture_paiement_sa=$this->obj->select_all();
		
		ATF::facture()->u(array("id_facture"=>$this->id_facture,"date_previsionnelle"=>date("Y-m-d")));
		$this->obj->update(array("id_facture_paiement"=>$id_facture_paiement,"montant"=>200,"id_facture"=>ATF::facture()->decryptId($this->id_facture),"date"=>date("Y-m-d",strtotime(date("Y-m-d")." + 4 year"))));
		$this->obj->q->reset()->addCondition("facture_paiement.id_facture",ATF::facture()->decryptId($this->id_facture))->setCount();
		$facture_paiement_sa=$this->obj->select_all();
		$this->assertTrue($facture_paiement_sa["data"][0]["allowFactureInteret"],"Problème sur le select_all qui devrait renvoyer allowFactureInteret true");
	}

	//@author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	function test_getRefForScanner(){
		$return = $this->obj->getRefForScanner(1 , NULL);
		$this->assertEquals($return,"Transfert vers paiement d'un montant de 1500.00 de la facture FLI07070023","getRefForScanner incorrect !");
	}

	function test_factureTerminee(){
		ATF::$msg->getNotices();
		$this->obj->factureTerminee(1166 , NULL);
		$notices = ATF::$msg->getNotices();

		$not = array(array(
            "msg" => "notice_update_facture_payee",
            "title" => "Succès !",
            "timer" => "",
            "type"=>"success"
        ));
        $this->assertEquals($not , $notices , "Erreur de notices");		

	}

};
?>