
<?php

class rdv_test extends ATF_PHPUnit_Framework_TestCase {
	
	/** Méthode pré-test, exécutée avant chaque test unitaire
	* besoin d'un user pour les traduction
	*/
	public function setUp() {
		ATF::db()->begin_transaction(true);
		$adherent =  array("adherent" => Array(
            "date_entree" => "03-07-2013" ,
            "id_orientation" => 2,
            "id_site_accueil" => 3,
            "id_pole_accueil" => 1,
            "civilite" => "Mr",
            "prenom" =>"jojo",
            "nom" => "toto",
            "sexe" => "M",
            "nationalite" =>"francaise",
            "nationalite2" =>NULL,
            "fixe" =>NULL,
            "mobile" =>NULL ,
            "nom_jeune_fille" => NULL,
            "date_naissance" => "03-07-2011",
            "ville_naissance" => "Lille",
            "pays_naissance" => "FRANCE" ,
            "tranche_age" => "NC" ,
            "personne_a_charge" => 0 ,
            "situation_familiale" => "celibataire" ,
            "adresse_perso" => "Rue de lille",
            "adresse_perso_2" => NULL,
            "mail" => NULL,
            "caf" => NULL,
            "mutuelle" => "non" ,
            "nom_mutuelle" => NULL,
            "habitation" => "locataire",
            "surface_habitable" => "0" ,
            "cp" => "59000",
            "id_zonegeo" => "2",
            "securite_sociale" => NULL,
            "assurance" => non ,
            "nom_assurance" => NULL,
            "cmu" => non ,
            "profession" => "chomeur",
            "qualif_pro" => "aucune",
            "tel_employeur" => NULL,
            "cp_employeur" => NULL,
            "ville_employeur" => NULL,
            "csp" => "sans_activite",
            "niveau" => NULL,
            "fax" => NULL,
            "ce" => NULL,
            "adresse_ce" => NULL ,
            "demandeur_emploi" => non ,
            "employeur" => NULL ,
            "adresse_employeur" => NULL  
        ),
        "values_adherent" => json_encode(Array(0 => array("adherent_enfant__dot__prenom"=>"ddd",
            			  "adherent_enfant__dot__nom"=>"hhh",
            			  "adherent_enfant__dot__date_naissance"=>"2013-07-05T00:00:00",
            			  "adherent_enfant__dot__note"=>"dd")
						))
		);
        ATF::adherent()->insert($adherent);		
		ATF::adherent()->q->reset()->addOrder("id_adherent", "desc")->setLimit(1);
		$this->adherent = ATF::adherent()->select_row();
		
	}
	
	/** Méthode post-test, exécutée après chaque test unitaire
	*/
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}

	/*--------------------------------------------------------------*/
	/*                   Tests unitaires                            */
	/*--------------------------------------------------------------*/
	
	/** Test du constructeur
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/	
	public function test__construct(){
		$c = new rdv();	
		$this->assertTrue($c instanceOf rdv, "L'objet rdv n'est pas de bon type");
	}	
	
	public function test_rdv_imminent(){
			$this->obj->insert(array("id_adherent"=>$this->adherent["id_adherent"],
									 "date_contact"=> date("Y-m-d"),
									 "type_contact"=> "telephonique",
									 "date_rdv" => date("Y-m-d 01:00:00"),
									 "type_rdv" => "accompagnement",
									 "id_pole_accueil" => 1							 
			));
			
			$rdv = $this->obj->rdv_imminent(date("Y-m-d"));
			$result = array("id_adherent" => $this->adherent["id_adherent"],
				            "date_rdv" => date("Y-m-d 01:00:00"),
				            "type_rdv" => "accompagnement",
				            "date" => date("Y-m-d"),
				            "adherent" => "Mr Jojo TOTO"
							);
						
			$this->assertEquals($result["id_adherent"], $rdv[0]["id_adherent"] , "1 - Erreur rdv imminent");
			$this->assertEquals($result["type_rdv"], $rdv[0]["type_rdv"] , "2 - Erreur rdv imminent");
			$this->assertEquals($result["date_rdv"], $rdv[0]["date_rdv"] , "3 - Erreur rdv imminent");
			$this->assertEquals($result["adherent"], $rdv[0]["adherent"] , "4 - Erreur rdv imminent");
	}

	/** Test du select_all
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test_select_all(){
		$sa = $this->obj->select_all();	

		$this->assertEquals(ATF::adherent()->select($sa[0]["id_adherent"] ,"num_dossier"), $sa[0]["num_dossier"] , "Error" );
	}
}
?>