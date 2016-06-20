<?
class commande_absystech_test extends ATF_PHPUnit_Framework_TestCase {
	private $commande;
	
	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	/*besoin d'un user pour les traduction*/
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

		$this->id_devis = ATF::devis()->insert($this->devis,$this->s);
		$this->id_affaire = ATF::devis()->select($this->id_devis,"id_affaire");
	
		//Commande
		$commande[$this->obj->table]=$this->devis["devis"];
		$commande[$this->obj->table]["id_affaire"]=$this->id_affaire;
		$commande[$this->obj->table]["id_devis"]=$this->id_devis;
		$commande[$this->obj->table]["date"]=date("Y-m-d");
		
		//Commande_ligne
		$commande["values_commande"]=array("produits"=>'[{"commande_ligne__dot__ref":"TU","commande_ligne__dot__produit":"Tu_commande","commande_ligne__dot__quantite":"15","commande_ligne__dot__prix":"10","commande_ligne__dot__prix_achat":"10","commande_ligne__dot__id_fournisseur":"1","commande_ligne__dot__id_compte_absystech":"1","commande_ligne__dot__marge":97.14,"commande_ligne__dot__id_fournisseur_fk":"1"}]');

		//Insertion
		unset($commande[$this->obj->table]["id_contact"]);
		unset($commande[$this->obj->table]["validite"]);
		$this->commande=$commande;
		$this->id_commande = $this->obj->insert($commande,$this->s);

	}
	
	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function tearDown(){
		ATF::db()->rollback_transaction(true);
	}


	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function testInsert(){
		//Sans societe
		unset($commande[$this->obj->table]["id_societe"]);
		// Erreur 1
		try {
			$id_commande = $this->obj->insert($commande,$this->s);
		} catch (errorATF $e) {
			$erreur_trouvee1 = $e->getCode();			
		}
		$this->assertEquals(105,$erreur_trouvee1,"ERREUR 1 NON ATTRAPPEE (id_societe non insere)");
		//Sans affaire
		$commande[$this->obj->table]["id_societe"]=1;
		$commande[$this->obj->table]["id_affaire"]="A";
		$commande[$this->obj->table]["date"]=date("Y-m-d");

		// Erreur 2
		try {
			$id_commande = $this->obj->insert($commande,$this->s);
		} catch (errorATF $e) {
			$erreur_trouvee2 = $e->getCode();
			ATF::db($this->db)->rollback_transaction();
		}
		$this->assertEquals(12,$erreur_trouvee2,"ERREUR 2 NON ATTRAPPEE (id_affaire non insere)");

		$commande[$this->obj->table]["id_affaire"]=$this->id_affaire;
		$commande[$this->obj->table]["code_commande_client"]="Code Cli TU";

		//Insertion
		$id_commande = $this->obj->insert($commande,$this->s);
		$this->assertNotNull($id_commande,"Insertion de la commande incorrecte");

		//Etat de l'affaire
		$id_affaire = $this->obj->select($id_commande,"id_affaire");
		$affaire=ATF::affaire()->select($id_affaire);
		$this->assertEquals($affaire["etat"],"commande","Erreur sur l'etat de l'affaire");
		//Code commande client de l'affaire
		$this->assertEquals($affaire["code_commande_client"],"Code Cli TU","Erreur sur le commande client de l'affaire");

		//Etat du devis
		$devis=ATF::devis()->select($this->id_devis);
		$this->assertEquals($devis["etat"],"gagne","Erreur sur l'etat de l'affaire");

		$commande[$this->obj->table]=$this->obj->select($id_commande);
		$this->obj->delete($id_commande);
		unset($commande[$this->obj->table]["id_commande"]);
		unset($commande[$this->obj->table]["id_affaire"]);
		unset($commande[$this->obj->table]["resume"]);
		
		// Erreur 4
		try {
			$id_commande = $this->obj->insert($commande,$this->s);
		} catch (errorATF $e) {
			$erreur_trouvee3 = $e->getCode();
			ATF::db($this->db)->rollback_transaction();
		}
		
		$this->assertEquals(12,$erreur_trouvee3,"ERREUR 3 NON ATTRAPPEE (resume non insere)");
		$commande[$this->obj->table]["resume"]="Tu_commande2";
		$commande["values_commande"]=array("produits"=>'[{"commande_ligne__dot__ref":"TU","commande_ligne__dot__produit":"Tu_commande","commande_ligne.prix":"10","commande_ligne__dot__prix_achat":"10","commande_ligne__dot__id_fournisseur":"1","commande_ligne__dot__id_compte_absystech":"1","commande_ligne__dot__marge":97.14,"commande_ligne__dot__id_fournisseur_fk":"1"}]');
		try {
			$id_commande = $this->obj->insert($commande,$this->s);
		} catch (errorATF $e) {
			$erreur_trouvee3 = $e->getMessage();
			
		}
		
		$commande[$this->obj->table]["resume"]="Tu_commande2";		
		$commande["values_commande"]=array("produits"=>'[{"commande_ligne__dot__ref":"TU","commande_ligne__dot__produit":"Tu_commande","commande_ligne__dot__quantite":"","commande_ligne__dot__prix":"20","commande_ligne__dot__prix_achat":"10","commande_ligne__dot__id_fournisseur":"1","commande_ligne__dot__id_compte_absystech":"1","commande_ligne__dot__marge":97.14,"commande_ligne__dot__id_fournisseur_fk":"1"}]');
			
		//Insertion
		unset($commande[$this->obj->table]["date"]);
		$id_commande = $this->obj->insert($commande,$this->s);
		$this->assertNotNull($id_commande,"Insertion de la commande incorrecte");
		$this->assertEquals(date("Y-m-d"),$this->obj->select($id_commande,"date"),"Date commande incorrecte");
		
		//Etat de l'affaire
		$id_affaire = $this->obj->select($id_commande,"id_affaire");
		$affaire=ATF::affaire()->select($id_affaire);
		$this->assertEquals($affaire["etat"],"commande","Erreur sur l'etat de l'affaire");
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function testCan_update(){
		$this->assertTrue($this->obj->can_update($this->id_commande),"1 Can_update ne fonctionne pas");
		
		$infos["id_commande"]=$this->id_commande;
		$this->obj->annulee($infos,$this->s);
		ATF::$msg->getNotices();
		ATF::$usr->set("id_profil",4);
		try {
			$this->obj->can_update($this->id_commande);
		} catch (errorATF $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(892,$error,"2 Can_update ne fonctionne pas");
	}

	
	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function testCan_delete(){
		$this->assertTrue($this->obj->can_delete($this->id_commande),"1 can_delete ne fonctionne pas");
		
		$infos["id_commande"]=$this->id_commande;
		$infos["etat"]="facturee";
		$this->obj->u($infos,$this->s);
		ATF::$msg->getNotices();

		try {
			$this->obj->can_delete($this->id_commande);
		} catch (errorATF $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(893,$error,"2 can_delete ne fonctionne pas");
	}
	
	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function testAnnulee(){
		$this->obj->u(array("id_commande"=>$this->id_commande,"etat"=>"facturee"));
		$this->assertFalse($this->obj->annulee(array("id_commande"=>$this->id_commande)),"1 La méthode annule ne doit pas permettre d'annuler une commande facturee");

		$this->obj->u(array("id_commande"=>$this->id_commande,"etat"=>"en_cours"));
		$this->assertTrue($this->obj->annulee(array("id_commande"=>$this->id_commande)),"2 La méthode annule doit permettre d'annuler une commande en cours");
	
		$this->assertEquals(array(0=>array("msg"=>"La commande '".$this->obj->select($this->id_commande,'ref')."' a bien été annulée","title"=>"Succès !","timer"=>"")),ATF::$msg->getNotices(),"3 La notice d'annulation fonctionne bien");
		
		$commande = $this->obj->select($this->id_commande);
		$this->assertEquals("annulee",$commande["etat"],"4 La commande doit passer en annule si elle est en cours");
	}  

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	/*@author Quentin JANON <qjanon@absystech.fr> */ 
	function testDelete(){
		
		$commande[$this->obj->table]=$this->obj->select($this->id_commande);

		//Test try catch
		$commande[$this->obj->table]["id_commande"]="A";
		try {
			$this->obj->delete($commande[$this->obj->table]["id_commande"]);
		} catch (errorATF $e) {
			$erreur_trouvee = $e->getCode();
			ATF::db($this->db)->rollback_transaction();
		}
		
		$this->assertEquals("893",$erreur_trouvee,"la commande ne doit pas être inséré si il n'y a pas de commande");
		$commande[$this->obj->table]["id_commande"]=$this->id_commande;

		//Suppression
		$this->obj->delete($this->id_commande);
		$this->assertNull($this->obj->select($this->id_commande),"Suppression de la commande incorrecte");

		//Etat de l'affaire
		$affaire=ATF::affaire()->select($this->id_affaire);
		$this->assertEquals($affaire["etat"],"devis","Erreur sur l'etat de l'affaire");

		//Etat du devis
		$devis=ATF::devis()->select($this->id_devis);
		$this->assertEquals($devis["etat"],"attente","Erreur sur l'etat du devis");

		//SANS AFFAIRE
		$commande=$this->commande;
		unset($commande[$this->obj->table]["id_devis"]);
		unset($commande[$this->obj->table]["id_affaire"]);

		$id_commande=$this->obj->insert($commande,$this->s);
		$commande[$this->obj->table]=$this->obj->select($id_commande);

		$affaire=ATF::affaire()->select($commande[$this->obj->table]["id_affaire"]);
		$this->assertNotNull($affaire,"Insertion de la commande incorrecte");

		$this->obj->delete($commande[$this->obj->table]["id_commande"]);
		$affaire=ATF::affaire()->select($commande[$this->obj->table]["id_affaire"]);
		$this->assertNull($affaire,"Suppression de la commande incorrecte");
	
		//DEVIS TAB
		$commande[$this->obj->table]=$this->commande[$this->obj->table];
		unset($commande[$this->obj->table]["id_devis"]);
		unset($commande[$this->obj->table]["id_affaire"]);
		$id_commande=$this->obj->insert($commande,$this->s);

		$commande[$this->obj->table]=$this->obj->select($id_commande);

		$commande1[$this->obj->table]=$this->commande[$this->obj->table];
		unset($commande1[$this->obj->table]["id_devis"]);
		unset($commande1[$this->obj->table]["id_affaire"]);
		$commande1["values_commande"] = $commande["values_commande"];

		$id_commande1=$this->obj->insert($commande1,$this->s);

		$commande1[$this->obj->table]=$this->obj->select($id_commande1);
		

		$affaire=ATF::affaire()->select($commande[$this->obj->table]["id_affaire"]);
		$this->assertNotNull($affaire,"Insertion de tabcommande incorrecte");

		$affaire=ATF::affaire()->select($commande1[$this->obj->table]["id_affaire"]);
		$this->assertNotNull($affaire,"Insertion de tabcommande1 incorrecte");

		$ids["id"] = array(0=>$commande[$this->obj->table]["id_commande"],1=>$commande1[$this->obj->table]["id_commande"]);
		$ids["strict"]=1;
		$this->obj->delete($ids);
		
		$affaire=ATF::affaire()->select($commande[$this->obj->table]["id_affaire"]);
		$this->assertNull($affaire,"Suppression de commande incorrecte");

		$affaire=ATF::affaire()->select($commande1[$this->obj->table]["id_affaire"]);
		$this->assertNull($affaire,"Suppression de commande1 incorrecte");
		

	}
	

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function testUpdate(){
		ATF::commande_ligne()->q->reset()->addCondition("id_commande",$this->obj->decryptId($this->id_commande));
		$commande_ligne=ATF::commande_ligne()->select_all();
		$commande=$this->commande;
		$commande["commande"]["id_commande"]=$this->id_commande;
		$commande[$this->obj->table]["code_commande_client"]="Code Cli TU";
		$commande["values_commande"]=array("produits"=>'[{"commande_ligne__dot__id_commande_ligne":"'.$commande_ligne[0]["id_commande_ligne"].'","commande_ligne__dot__ref":"'.$commande_ligne[0]["ref"].'","commande_ligne__dot__produit":"'.$commande_ligne[0]["produit"].'","commande_ligne__dot__quantite":"15","commande_ligne__dot__prix":"10","commande_ligne__dot__prix_achat":"10","commande_ligne__dot__id_fournisseur":"1","commande_ligne__dot__id_compte_absystech":"1","commande_ligne__dot__marge":97.14,"commande_ligne__dot__id_fournisseur_fk":"1"}]');
		
		
		//Modification
		$this->obj->update($commande,$this->s);
		ATF::commande_ligne()->q->reset()->addCondition("id_commande",$this->obj->decryptId($this->id_commande));
		$commande_ligne=ATF::commande_ligne()->select_all();
		$this->assertEquals("TU",$commande_ligne[0]["ref"],"Update ne renvoie pas le bon enregistrement");
		$this->assertEquals("15",$commande_ligne[0]["quantite"],"Update ne met pas bien à jour la quantité");
		$this->assertEquals("10.00",$commande_ligne[0]["prix"],"Update ne met pas bien à jour le prix");
		
		$id_affaire = $this->obj->select($this->id_commande,"id_affaire");
		$affaire=ATF::affaire()->select($id_affaire);
		//Code commande client de l'affaire
		$this->assertEquals($affaire["code_commande_client"],"Code Cli TU","Erreur sur le commande client de l'affaire");
		
		$commande["commande"]["prix"] = 0;
		$commande["commande"]["frais_de_port"] = 0;
		$commande["values_commande"]=array("produits"=>'[{"commande_ligne__dot__id_commande_ligne":"'.$commande_ligne[0]["id_commande_ligne"].'","commande_ligne__dot__ref":"'.$commande_ligne[0]["ref"].'","commande_ligne__dot__produit":"'.$commande_ligne[0]["produit"].'","commande_ligne__dot__quantite":"","commande_ligne__dot__prix":"10","commande_ligne__dot__prix_achat":"10","commande_ligne__dot__id_fournisseur":"1","commande_ligne__dot__id_compte_absystech":"1","commande_ligne__dot__marge":97.14,"commande_ligne__dot__id_fournisseur_fk":"1"}]');
		$this->obj->update($commande,$this->s);
		
	}
	
	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function testSelect(){
		$commande=$this->obj->select($this->id_commande);
		$this->assertNotEquals("",$commande["date"],"select ne prend pas la date");

		ATF::_r('event',"cloner");
		$commande_cloner=$this->obj->select($this->id_commande);
		$this->assertEquals("",$commande_cloner["date"],"select cloner prend la date");
	}
	
	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function testDefault_value(){

		ATF::_r('id_commande',$this->id_commande);

		$id_societe=$this->obj->default_value("id_societe");
		$this->assertEquals($this->commande["commande"]["id_societe"],$id_societe,"default_value commande ne renvoie pas le bon 'id_societe'");

		$resume=$this->obj->default_value("resume");
		$this->assertEquals($this->commande["commande"]["resume"],$resume,"default_value commande ne renvoie pas le bon 'resume'");

		$prix=$this->obj->default_value("prix");
		$this->assertEquals($this->commande["commande"]["prix"],$prix,"default_value commande ne renvoie pas le bon 'prix'");

		$frais_de_port=$this->obj->default_value("frais_de_port");
		$this->assertEquals($this->commande["commande"]["frais_de_port"],$frais_de_port,"default_value commande ne renvoie pas le bon 'frais_de_port'");

		$prix_achat=$this->obj->default_value("prix_achat");
		$this->assertEquals($this->commande["commande"]["prix_achat"],$prix_achat,"default_value commande ne renvoie pas le bon 'prix_achat'");

		$sous_total=$this->obj->default_value("sous_total");
		$this->assertEquals(($this->commande["commande"]["prix"]-$this->commande["commande"]["frais_de_port"]),$sous_total,"default_value commande ne renvoie pas le bon 'sous_total'");

		$marge=$this->obj->default_value("marge");
		$this->assertEquals((round(((($this->commande["commande"]["prix"]-$this->commande["commande"]["frais_de_port"])-$this->commande["commande"]["prix_achat"])/($this->commande["commande"]["prix"]-$this->commande["commande"]["frais_de_port"]))*100,2)."%"),$marge,"default_value commande ne renvoie pas la bonnne 'marge'");


		ATF::_r('id_devis',$this->id_devis);

		$id_societe=$this->obj->default_value("id_societe");
		$this->assertEquals($this->devis["devis"]["id_societe"],$id_societe,"default_value ne renvoie pas le bon 'id_societe'");

		$resume=$this->obj->default_value("resume");
		$this->assertEquals($this->devis["devis"]["resume"],$resume,"default_value ne renvoie pas le bon 'resume'");

		$prix=$this->obj->default_value("prix");
		$this->assertEquals($this->devis["devis"]["prix"],$prix,"default_value ne renvoie pas le bon 'prix'");

		$frais_de_port=$this->obj->default_value("frais_de_port");
		$this->assertEquals($this->devis["devis"]["frais_de_port"],$frais_de_port,"default_value ne renvoie pas le bon 'frais_de_port'");

		$prix_achat=$this->obj->default_value("prix_achat");
		$this->assertEquals($this->devis["devis"]["prix_achat"],$prix_achat,"default_value ne renvoie pas le bon 'prix_achat'");

		$sous_total=$this->obj->default_value("sous_total");
		$this->assertEquals(($this->devis["devis"]["prix"]-$this->devis["devis"]["frais_de_port"]),$sous_total,"default_value ne renvoie pas le bon 'sous_total'");

		$marge=$this->obj->default_value("marge");
		$this->assertEquals((round(((($this->devis["devis"]["prix"]-$this->devis["devis"]["frais_de_port"])-$this->devis["devis"]["prix_achat"])/($this->devis["devis"]["prix"]-$this->devis["devis"]["frais_de_port"]))*100,2)."%"),$marge,"default_value devis ne renvoie pas la bonnne 'marge'");

		$marge_absolue=$this->obj->default_value("marge_absolue");
		$this->assertEquals(($this->devis["devis"]["prix"]-$this->devis["devis"]["frais_de_port"]-$this->devis["devis"]["prix_achat"]),$marge_absolue,"default_value ne renvoie pas le bon 'marge_absolue'");

		$default_value=$this->obj->default_value("default_value");
		$this->assertNull($default_value,"default_value ne renvoie pas une bonne valeur pour le field default_value");
	}

	/*@author Yann GAUTHERON <ygautheron@absystech.fr> */ 
	function testFiltrageParProfilApporteurAffaire(){
		ATF::$usr->set('id_profil',11);
		ATF::$usr->set('id_user',1);
		$this->obj->sa();
		$this->assertEquals("affaire.id_commercial = '1'",$this->obj->q->getWhere('affaire.id_commercial'));
	}
		
	/*@author Quentin JANON <qjanon@absystech.fr> */ 
	function test_select_all(){
		$this->obj->q->reset()->setCount();
		
		$r = $this->obj->select_all();
		$r = $r['data'];
		$this->assertArrayHasKey("allowCF",$r[0],"Il manque une des valeurs (allowCF) utiles aux boutons EXTJS");
		$this->assertArrayHasKey("allowLivraison",$r[0],"Il manque une des valeurs (allowLivraison) utiles aux boutons EXTJS");
		$this->assertArrayHasKey("allowFacture",$r[0],"Il manque une des valeurs (allowFacture) utiles aux boutons EXTJS");
		$this->assertArrayHasKey("allowCancel",$r[0],"Il manque une des valeurs (allowCancel) utiles aux boutons EXTJS");
		$this->assertArrayHasKey("allowCheckFacture",$r[0],"Il manque une des valeurs (allowCheckFacture) utiles aux boutons EXTJS");
		$this->assertArrayHasKey("totalFacture",$r[0],"Il manque une des valeurs (totalFacture) utiles aux boutons EXTJS");
		$this->assertArrayHasKey("id_affaire",$r[0],"Il manque une des valeurs (id_affaire) utiles aux boutons EXTJS");
		
		$this->assertTrue($r[0]["allowCF"],"Le allowCF devrait être a TRUE");
		$this->assertTrue($r[0]["allowLivraison"],"Le allowLivraison devrait être a TRUE");
		$this->assertTrue($r[0]["allowFacture"],"Le allowFacture devrait être a TRUE");
		$this->assertTrue($r[0]["allowCancel"],"Le allowCancel devrait être a TRUE");
		$this->assertTrue($r[0]["allowCheckFacture"],"Le allowCheckFacture devrait être a TRUE");
		
		$r[0]["etat"] = "annulee";
		unset($r[0]["allowCF"],$r[0]["allowLivraison"],$r[0]["allowFacture"],$r[0]["allowCancel"],$r[0]["allowCheckFacture"],$r[0]["totalFacture"]);
		$this->obj->u($r[0]);

		ATF::$usr->set('id_profil',2);

		$this->obj->q->reset()->setCount()->where('id_commande',$r[0]['id_commande']);
		$r = $this->obj->select_all();
		$r = $r['data'];

		$this->assertFalse($r[0]["allowCF"],"Le allowCF devrait être a FALSE");
		$this->assertFalse($r[0]["allowLivraison"],"Le allowLivraison devrait être a FALSE");
		$this->assertFalse($r[0]["allowFacture"],"Le allowFacture devrait être a FALSE");
		$this->assertFalse($r[0]["allowCancel"],"Le allowCancel devrait être a FALSE");
		$this->assertFalse($r[0]["allowCheckFacture"],"Le allowCheckFacture devrait être a FALSE");
	}
		
	/*@author Quentin JANON <qjanon@absystech.fr> */ 
	function test_setInfos(){
		$this->obj->q->reset()->setCount();
		
		$r = $this->obj->select_all();
		$r = $r['data'];

		$params = array('id_commande'=>$r[0]['id_commande'],'etat'=>'facturee');
		$this->obj->setInfos($params);


		$r = $this->obj->select($r[0]['id_commande']);

		$this->assertEquals("facturee",$r["etat"],"L'état ne s'est pas modifié !");

	}



};
?>