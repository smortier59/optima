<?php

class zonegeo_test extends ATF_PHPUnit_Framework_TestCase {
	
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
		$c = new zonegeo();	
		$this->assertTrue($c instanceOf zonegeo, "L'objet zonegeo n'est pas de bon type");
	}	
	
	public function test_select_all(){
            ATF::_r("pager" ,"gsa_zonegeo");
		$res = $this->obj->select_all();
		$this->assertEquals("Roubaix" , $res[0]["zonegeo.id_zonegeo"] , "Roubaix n'est plus 1er au nombre de personne ??");
	}
	
}
?>