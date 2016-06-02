<?
class formation_bon_de_commande_fournisseur_test extends ATF_PHPUnit_Framework_TestCase {
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
		$this->id_commande_formation = ATF::formation_commande()->i(array(	"id_formation_devis" => $this->id_devis_formation, "ref"=>"FCL20150001", "date" => date("Y-m-d") ));

		$infos = array("id_formation_devis" => $this->id_devis_formation , "id_formation_commande" => $this->id_commande_formation ,"objectif" => "L'objectif de la formation", "id_user"=>97 );
		
		$this->id_formation_commande_fournisseur = ATF::formation_commande_fournisseur()->insert(array("formation_commande_fournisseur" => $infos));

	}
	
	/* Méthode post-test, exécute après chaque test unitaire */
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}

	/*
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test_construct(){
		new formation_bon_de_commande_fournisseur();
	}

	/*
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test_default_value(){
		ATF::_r('id_formation_devis',$this->id_devis_formation);

		$this->assertEquals($this->id_societe, $this->obj->default_value("id_societe"), "default_value ne renvoie pas la bonne valeur !");
		$this->assertEquals($this->id_devis_formation, $this->obj->default_value("id_formation_devis"), "default_value 2 ne renvoie pas la bonne valeur !");
		$this->assertEquals("La thématique de la formation", $this->obj->default_value("thematique"), "default_value 3 ne renvoie pas la bonne valeur !");


		$this->assertEquals("", $this->obj->default_value("montant"), "default_value 3 ne renvoie pas la bonne valeur !");
	}
	
	/*
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test_insert(){
		$infos =  array("id_formation_devis" => $this->id_devis_formation , 
					   "id_societe" => $this->id_societe ,
					   "id_contact" => $this->id_contact ,
					   "id_fournisseur" => 1606 ,
					   "montant"=> 500,
					   "thematique"=> "Thematique",
					   "ref"=> "ref TU",
					   "date"=>"2015-03-18",
					   "commentaire"=> "commentaire");
		
		$return = $this->obj->insert(array( "formation_bon_de_commande_fournisseur" => $infos));
		$this->assertNotNull($return,'Formation_commande non créé 1 !');

		$return = $this->obj->insert(array( "preview"=> true, "formation_bon_de_commande_fournisseur" => $infos));
		$this->assertNotNull($return,'Formation_commande non créé 2 !');

		

	}


	/*
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test_update(){
		$infos =  array("id_formation_devis" => $this->id_devis_formation , 
					   "id_societe" => $this->id_societe ,
					   "id_contact" => $this->id_contact ,
					   "id_fournisseur" => 1606 ,
					   "montant"=> 500,
					   "thematique"=> "Thematique",
					   "ref"=> "ref TU",
					   "date"=>"2015-03-18",
					   "commentaire"=> "commentaire");
		
		$id_formation_bon_de_commande_fournisseur = $this->obj->insert(array( "formation_bon_de_commande_fournisseur" => $infos));
		$this->assertNotNull($id_formation_bon_de_commande_fournisseur,'Formation_commande non créé 1 !');


		$infos =  array("id_formation_bon_de_commande_fournisseur" => $id_formation_bon_de_commande_fournisseur , 
					   "commentaire"=> "Commentaire modifié");
		
		$return = $this->obj->update(array( "formation_bon_de_commande_fournisseur" => $infos));
		$this->assertEquals("Commentaire modifié", $this->obj->select($id_formation_bon_de_commande_fournisseur , "commentaire"), "Commentaire non modifié !");


		$infos =  array("id_formation_bon_de_commande_fournisseur" => $id_formation_bon_de_commande_fournisseur , 
					   "commentaire"=> "Commentaire modifié 2");
		$return = $this->obj->update(array("preview"=>true, "formation_bon_de_commande_fournisseur" => $infos));
		$this->assertEquals("Commentaire modifié 2", $this->obj->select($id_formation_bon_de_commande_fournisseur , "commentaire"), "Commentaire 2 non modifié !");
	}


	/*
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test_getRef(){
		$infos =  array("id_formation_devis" => $this->id_devis_formation , 
					   "id_societe" => $this->id_societe ,
					   "id_contact" => $this->id_contact ,
					   "id_fournisseur" => 1606 ,
					   "montant"=> 500,
					   "date"=>"2015-03-18",
					   "thematique"=> "Thematique",
					   "ref"=> "FCL20140001",
					   "commentaire"=> "commentaire");
		
		$id_formation_bon_de_commande_fournisseur = $this->obj->insert(array( "formation_bon_de_commande_fournisseur" => $infos));
		$this->assertNotNull($id_formation_bon_de_commande_fournisseur,'Formation_commande non créé 1 !');

		$this->obj->u(array("id_formation_bon_de_commande_fournisseur"=> $id_formation_bon_de_commande_fournisseur, "ref"=>"FCL20140001"));
		$this->assertEquals("FCL20140002", $this->obj->getRef("2014-01-01") , "REF 1 incorrecte");

		$this->obj->u(array("id_formation_bon_de_commande_fournisseur"=> $id_formation_bon_de_commande_fournisseur, "ref"=>"FCL20140011"));
		$this->assertEquals("FCL20140012", $this->obj->getRef("2014-01-01") , "REF 1-2 incorrecte");

		$this->obj->u(array("id_formation_bon_de_commande_fournisseur"=> $id_formation_bon_de_commande_fournisseur, "ref"=>"FCL20141001"));
		$this->assertEquals("FCL20141002", $this->obj->getRef("2014-01-01") , "REF 2 incorrecte");

		$this->obj->u(array("id_formation_bon_de_commande_fournisseur"=> $id_formation_bon_de_commande_fournisseur, "ref"=>"FCL20140998"));
		$this->assertEquals("FCL20140999", $this->obj->getRef("2014-01-01") , "REF 3 incorrecte");

		$this->assertEquals("FCL20130001", $this->obj->getRef("2013-01-01") , "REF 4 incorrecte");

	}

	/*
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test_select_all(){
		$infos =  array("id_formation_devis" => $this->id_devis_formation , 
					   "id_societe" => $this->id_societe ,
					   "id_contact" => $this->id_contact ,
					   "id_fournisseur" => 1606 ,
					   "montant"=> 500,
					   "thematique"=> "Thematique",
					   "ref"=> "ref TU",
					   "date"=>"2015-03-18",
					   "commentaire"=> "commentaire");
		
		$return = $this->obj->insert(array("formation_bon_de_commande_fournisseur" => $infos));

		$this->obj->q->reset()->addCondition("formation_bon_de_commande_fournisseur.id_formation_devis", $this->id_devis_formation)->setCount();
        $r = $this->obj->select_all();  
		$this->assertEquals("1", $r["data"][0]["factureFournisseurAllow"] , "select_all error");
	}


}
?>