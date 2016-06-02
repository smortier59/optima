<?
class affaire_cout_page_test extends ATF_PHPUnit_Framework_TestCase {

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
		
		//copieur_contrat
		$this->copieur_facture["copieur_facture"]["date"]=date('Y-m-d');
		$this->copieur_facture["copieur_facture"]['id_termes']=1;
		$this->copieur_facture["copieur_facture"]['id_societe']=$this->id_societe;
		$this->copieur_facture["copieur_facture"]['tva']=1.2;
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

	
	/*@author Quentin JANON <qjanon@absystech.fr> */ 
	function test_saFilter(){
		// FIltrage général par profil
		ATF::$usr->set('id_profil',11);
		ATF::affaire_cout_page()->select_all();
		$this->assertEquals(array("filtreGeneral"=>"affaire_cout_page.id_commercial = '".ATF::$usr->getID()."'"),ATF::affaire_cout_page()->q->getWhere(),"Filtrage general sur affaire_cout_page.id_commercial invailde");
	}

	/*@author Quentin JANON <qjanon@absystech.fr> */ 
	function testUpdate_termes(){
		$affaire_cout_page=$this->obj->select($this->id_affaire_cout_page);
		$this->assertEquals("",$affaire_cout_page["id_termes"],"Il ne devrait pas y avoir de termes");

		$affaire_cout_page["id_termes"]=1;
		$update_termes=$this->obj->update_termes($affaire_cout_page);
		$this->assertEquals(array(
			0=>array(
				"msg"=>"Modification de l'enregistrement 'Contrat de maintenance coût/page' effectuée avec succès.",
				"title"=>"Succès !",
				"timer"=>""
			)
		),ATF::$msg->getNotices(),"1 La notice de modification fonctionne bien");
		$affaire_cout_page=$this->obj->select($this->id_affaire_cout_page);
		$this->assertEquals("1",$affaire_cout_page["id_termes"],"update_termes ne fonctionne pas");
		$this->assertTrue($update_termes,"update_termes ne renvoie pas true");

		$update_termes=$this->obj->update_termes($affaire_cout_page);
		$this->assertFalse($update_termes,"update_termes ne renvoie pas false");
	}


	/*@author Quentin JANON <qjanon@absystech.fr> */ 
	function testCan_delete(){
	
		try {
			$can_delete=$this->obj->can_delete($this->id_affaire_cout_page);
		} catch (error $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(892,$error,"can_delete ne doit pas pouvoir suprimer une affaire_cout_page s'il y a une facture ET un contrat");


		ATF::copieur_facture()->d($this->id_copieur_facture);
		try {
			$can_delete=$this->obj->can_delete($this->id_affaire_cout_page);
		} catch (error $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(892,$error,"can_delete ne doit pas pouvoir suprimer une affaire s'il y a un contrat");

		ATF::copieur_contrat()->d($this->id_commande);
		$can_delete=$this->obj->can_delete($this->id_affaire_cout_page);
		$this->assertTrue($can_delete,"can_delete doit pouvoir suprimer une affaire s'iln'y a pas de devis ni de commande ni de facture");
	}


	/*@author Quentin JANON <qjanon@absystech.fr> */ 
	function test_can_update(){
		$u = array("id_affaire_cout_page"=>$this->id_affaire_cout_page,"etat"=>"commande");
		try {
			$can_update=$this->obj->can_update($this->id_affaire_cout_page,$u);
		} catch (error $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(892,$error,"ne doit pas pouvoir modifier une affaire_cout_page");

		$u = array("id_affaire_cout_page"=>$this->id_affaire_cout_page,"id_societe"=>$this->id_societe);
		$this->assertTrue($this->obj->can_update($this->id_affaire_cout_page,$u),"Doit pouvoir suprimer une affaire si c'est un id_societe");

		$u = array("id_affaire_cout_page"=>$this->id_affaire_cout_page,"id_termes"=>1);
		$this->assertTrue($this->obj->can_update($this->id_affaire_cout_page,$u),"Doit pouvoir suprimer une affaire si c'est un id_termes");
	}

	/*@author Quentin JANON <qjanon@absystech.fr> */ 
	function test_setForecast(){
		$u = array("id_affaire_cout_page"=>$this->id_affaire_cout_page,"forecast"=>"180");
		$erreur = false;
		try {
			$this->obj->setForecast($u);
		} catch (error $e) {
			$erreur = true;
		}
		$this->assertTrue($erreur,"Erreur non remontée");
		$this->assertEquals(6512,$e->getErrno(),"Mauvais code d'erreur");
				
		$u = array("id_affaire_cout_page"=>$this->id_affaire_cout_page,"forecast"=>"80");
		$this->obj->setForecast($u);
		$this->assertEquals("80",$this->obj->select($this->id_affaire_cout_page,"forecast"),"Erreur le forecast n'est pas a 80...");
		
	}
	
};