<?
class formation_facture_fournisseur_test extends ATF_PHPUnit_Framework_TestCase {
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
	}
	
	/* Méthode post-test, exécute après chaque test unitaire */
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}

	/*
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test_construct(){
		new formation_facture_fournisseur();
	}

	/*
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test_insert(){
		$infos = array("id_formation_devis" => $this->id_devis_formation , 
					   "id_societe" => $this->id_societe ,
					   "id_contact" => $this->id_contact ,
					   "id_fournisseur" => 1606 ,
					   "montant"=> 500,
					   "thematique"=> "Thematique",
					   "ref"=> "ref TU",
					   "date"=>"2015-03-18",
					   "commentaire"=> "commentaire");
		
		$return = ATF::formation_bon_de_commande_fournisseur()->insert(array( "formation_bon_de_commande_fournisseur" => $infos));
		$this->assertNotNull($return,'Formation_commande non créé 1 !');


		$infos = array("id_formation_devis"=> $this->id_formation_devis,
					   "id_formation_bon_de_commande_fournisseur"=> $return,
					   "id_societe"=> $this->id_societe,
					   "ref"=>"ref",
					   "prix"=> 500,
					   "numero_dossier"=>"Reference OPCA",
					   "tva"=>1.2,
					   "date"=>date("Y-m-d"));
		$this->id_formation_facture_fournisseur = ATF::formation_facture_fournisseur()->insert(array( "formation_facture_fournisseur" => $infos));

		$this->assertNotNull($this->id_formation_facture_fournisseur , "Un probleme dans l'insertion");
	}

	/*
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test_default_value(){
		$infos = array("id_formation_devis" => $this->id_devis_formation , 
					   "id_societe" => $this->id_societe ,
					   "id_contact" => $this->id_contact ,
					   "id_fournisseur" => 1606 ,
					   "montant"=> 500,
					   "date"=>"2015-03-18",
					   "thematique"=> "Thematique",
					   "ref"=> "ref TU",
					   "commentaire"=> "commentaire");
		
		$this->id_formation_bon_de_commande_fournisseur = ATF::formation_bon_de_commande_fournisseur()->insert(array( "formation_bon_de_commande_fournisseur" => $infos));



		ATF::_r('id_formation_bon_de_commande_fournisseur',$this->id_formation_bon_de_commande_fournisseur);

		$this->assertEquals($this->id_formation_bon_de_commande_fournisseur, $this->obj->default_value("id_formation_bon_de_commande_fournisseur"), "default_value ne renvoie pas la bonne valeur !");
		$this->assertEquals(date("Y-m-d"), $this->obj->default_value("date"), "default_value 2 ne renvoie pas la bonne valeur !");
		$this->assertEquals(1.2, $this->obj->default_value("tva"), "default_value 2.2 ne renvoie pas la bonne valeur !");
		
		$this->assertEquals("", $this->obj->default_value("etat"), "default_value 3 ne renvoie pas la bonne valeur !");

		$this->assertEquals(1606, $this->obj->default_value("id_societe"), "default_value 2.3 ne renvoie pas la bonne valeur !");

	}

	/*
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function default_valueExactitude(){
		$fmfe=new formation_commande_fournisseur_exactitude();

		$this->assertEquals(17, $fmfe->default_value("id_user"), "default_value exactitude ne renvoie pas la bonne valeur !");
	}
}
?>