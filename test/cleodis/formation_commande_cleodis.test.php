<?
class formation_commande_cleodis_test extends ATF_PHPUnit_Framework_TestCase {

	/** Méthode pré-test, exécute avant chaque test unitaire
	* besoin d'un user pour les traduction
	*/
	public function setUp() {
		ATF::db()->begin_transaction(true);	
		$this->environnementFormation();

		$infos = array(  "numero_dossier" => "123456789"
						,"thematique" => "La thématique de la formation"
						,"date" => date("Y-m-d")
						,"nb_heure" => "20"
						,"prix" => "50"
						,"id_owner"=>97
						,"id_contact"=>81
						,"id_societe" => $this->id_societe
						,"contact" => array($this->id_contact1, $this->id_contact2, $this->id_contact3)
						,"type"=>"normal"
				 );
		$dates = array("formation_devis_ligne" => json_encode(array(array("formation_devis_ligne__dot__date"=>"2015-01-31T00:00:00",
																				"formation_devis_ligne__dot__date_deb_matin"=> "8h",
																				"formation_devis_ligne__dot__date_fin_matin"=> "12h30",
																				"formation_devis_ligne__dot__date_deb_am"=> "14h",
																				"formation_devis_ligne__dot__date_fin_am"=> "18h"),
																		  array("formation_devis_ligne__dot__date"=>"2015-01-29T00:00:00",
																		  		"formation_devis_ligne__dot__date_deb_matin"=> "8h",
																				"formation_devis_ligne__dot__date_fin_matin"=> "12h30")
																		)
																),
						"formation_devis_fournisseur" => json_encode(array(array("formation_devis_fournisseur__dot__id_societe_fk"=>"1606",
																				 "formation_devis_fournisseur__dot__type"=>"lieu_formation"),
																				array("formation_devis_fournisseur__dot__id_societe_fk"=>"246",
																					  "formation_devis_fournisseur__dot__type"=>"apporteur_affaire")																				
																				)
																		)
					);
		$this->id_devis_formation = ATF::formation_devis()->insert(array("formation_devis" => $infos , "values_formation_devis" => $dates));
	}
	
	/* Méthode post-test, exécute après chaque test unitaire */
	public function tearDown(){
		ATF::$msg->getNotices();
		ATF::db()->rollback_transaction(true);
	}

	/*
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test_construct(){
		new formation_commande_cleodis();
	}

	/*
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test_default_value(){
		$this->assertEquals(date("Y-m-d"), $this->obj->default_value("date"), "default_value ne renvoie pas la bonne valeur !");
		$this->assertEquals("", $this->obj->default_value("formateur"), "default_value 2 ne renvoie pas la bonne valeur !");
	}

	/*
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test_insert(){
		$infos = array( "id_formation_devis" => $this->id_devis_formation					   
					   ,"date" => date("Y-m-d")
					  );
		$return = $this->obj->insert(array("preview" => true, "formation_commande" => $infos));
		$this->assertNotNull($return,'Formation_commande non créé 1 !');

		$id_commande_formation = $this->obj->insert(array("formation_commande" => $infos));
		$this->assertNotNull($id_commande_formation,'Formation_commande non créé 2 !');

	}

	/*
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test_delete(){
		$infos = array( "id_formation_devis" => $this->id_devis_formation					   
					   ,"date" => date("Y-m-d")
					  );
		$return = $this->obj->insert(array("preview" => true, "formation_commande" => $infos));
		$this->assertNotNull($return,'Formation_commande non créé 1 !');

		$id_commande_formation = $this->obj->insert(array("formation_commande" => $infos));

		$this->obj->delete($id_commande_formation);

		$this->assertEquals(NULL, $this->obj->select($id_commande_formation), "La commande n'est passupprimé");
		$this->assertEquals("attente", ATF::formation_devis()->select($this->id_devis_formation , "etat"), "Le devis n'est pas passé en attente");


		$infos = array( "id_formation_devis" => $this->id_devis_formation					   
					   ,"date" => date("Y-m-d")
					  );		
		$id_commande_formation = $this->obj->insert(array("formation_commande" => $infos));
		$id_commande_formation2 = $this->obj->insert(array("formation_commande" => $infos));

		$this->obj->delete(array($id_commande_formation, $id_commande_formation2));
		$this->assertEquals(NULL, $this->obj->select($id_commande_formation), "La commande 1 n'est pas supprimé");
		$this->assertEquals(NULL, $this->obj->select($id_commande_formation), "La commande 2 n'est pas supprimé");

	}


	/*
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test_select_all(){
		$infos = array( "id_formation_devis" => $this->id_devis_formation					   
					   ,"date" => date("Y-m-d")
					  );	
		$id_commande_formation = $this->obj->insert(array("formation_commande" => $infos));	
		
		
		$this->obj->q->reset()->addCondition("id_formation_devis", $this->id_devis_formation)->setCount();
        $r = $this->obj->select_all();        
		$this->assertEquals(NULL, $r["data"][0]["factureAllow"], "Select all error");

		$infos =  array("id_formation_devis" => $this->id_devis_formation , 
					   "id_societe" => $this->id_societe ,
					   "id_contact" => $this->id_contact ,
					   "id_fournisseur" => 1606 ,
					   "montant"=> 500,
					   "thematique"=> "Thematique",
					   "ref"=> "ref TU",
					   "date"=>"2015-03-18",
					   "commentaire"=> "commentaire");		
		$return = ATF::formation_bon_de_commande_fournisseur()->insert(array( "formation_bon_de_commande_fournisseur" => $infos));
		$this->obj->q->reset()->addCondition("id_formation_devis", $this->id_devis_formation)->setCount();
        $r = $this->obj->select_all();  
		$this->assertEquals(1, $r["data"][0]["factureAllow"], "Select all 2  error");
	}

}