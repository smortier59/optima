<?php

class adherent_test extends ATF_PHPUnit_Framework_TestCase {
	
	/** Méthode pré-test, exécutée avant chaque test unitaire
	* besoin d'un user pour les traduction
	*/
	public function setUp() {
		ATF::db()->begin_transaction(true);
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
		$c = new adherent();	
		$this->assertTrue($c instanceOf adherent, "L'objet adherent n'est pas de bon type");
	}	
	
	
	
	/** Test de insert
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/	
	public function test__insert(){
			
		$adherent =  array("adherent" => Array(
            "date_entree" => "03-07-".date("Y") ,
            "id_orientation" => 2,
            "id_site_accueil" => 3,
            "id_pole_accueil" => 1,
            "civilite" => "Mr",
            "prenom" =>"",
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
        "values_adherent" => array("enfants" =>json_encode(Array(0 => array("adherent_enfant__dot__prenom"=>"ddd",
								            			  "adherent_enfant__dot__nom"=>"hhh",
								            			  "adherent_enfant__dot__date_naissance"=>"2013-07-05T00:00:00",
								            			  "adherent_enfant__dot__note"=>"dd")
						)))
		);
		
		try{
			$this->obj->insert($adherent);		
		}catch (errorATF $e) {
			$error = $e->getMessage();
		}
		$this->assertEquals("Le champs Prénom n'est pas renseigné !",$error,"Insert adherent sans prenom ne doit pas se faire !!");
	
	
	
		$adherent =  array("adherent" => Array(
            "date_entree" => "03-07-".date("Y") ,
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
        "values_adherent" => array("enfants" => json_encode(Array(array("adherent_enfant__dot__prenom"=>"ddd",
								            			  "adherent_enfant__dot__nom"=>"",
								            			  "adherent_enfant__dot__date_naissance"=>"2013-07-05T00:00:00",
								            			  "adherent_enfant__dot__note"=>"dd")
						)))
		);
		
		$this->obj->insert($adherent);		
		ATF::adherent()->q->reset()->addOrder("id_adherent", "desc")->setLimit(1);
		$last_adherent = ATF::adherent()->select_row();		
		$this->assertEquals("TOTO", $last_adherent["nom"] , "Nom adherent Incorect ");
		$this->assertEquals("Jojo", $last_adherent["prenom"] , "Prenom adherent Incorect ");
		//Enfants sans prenom
		ATF::adherent_enfant()->q->reset()->where("id_adherent" ,$last_adherent["id_adherent"]);
		$enfants = ATF::adherent_enfant()->select_all();
		$this->assertEquals("Ddd", $enfants[0]["prenom"] , "Prenom adherent_enfant Incorect ");
		$this->assertEquals("TOTO", $enfants[0]["nom"] , "Nom adherent_enfant Incorect ");
		
		
		$adherent["values_adherent"]["enfants"] = json_encode(Array(array("adherent_enfant__dot__prenom"=>"",
										            			    "adherent_enfant__dot__nom"=>"",
										            			    "adherent_enfant__dot__date_naissance"=>"2013-07-05T00:00:00",
										            			    "adherent_enfant__dot__note"=>"dd")));
				
		try {
			$this->obj->insert($adherent);	
		} catch (errorATF $e) {
			$error = $e->getMessage();
		}
		$this->assertEquals("Les enfants doivent avoir un prénom et une date de naissance !",$error,"Insert avec enfant sans nom ne doit pas se faire !!");
      }

	/** Test de l'update
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test_update(){
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
        "values_adherent" => array("enfants" =>json_encode(Array(array("adherent_enfant__dot__prenom"=>"ddd",
											            			  "adherent_enfant__dot__nom"=>"hhh",
											            			  "adherent_enfant__dot__date_naissance"=>"2013-07-05T00:00:00",
											            			  "adherent_enfant__dot__note"=>"dd"),
									            			  array("adherent_enfant__dot__prenom"=>"aaa",
											            			"adherent_enfant__dot__nom"=>"bbb",
											            			"adherent_enfant__dot__date_naissance"=>"2013-07-05T00:00:00",
											            			"adherent_enfant__dot__note"=>"fff")
															))
								 )
		);
        $this->obj->insert($adherent);		
		ATF::adherent()->q->reset()->addOrder("id_adherent", "desc")->setLimit(1);
		$last_adherent = ATF::adherent()->select_row();
		
		$adherent["adherent"]["id_adherent"] = $last_adherent["id_adherent"];
		$adherent["adherent"]["nom"] = "tata";		
		
        $this->obj->update($adherent);
        
        $adherent = ATF::adherent()->select($last_adherent["id_adherent"]);
		$this->assertEquals("TATA", $adherent["nom"] , "Nom adherent Incorect ");
		
		ATF::adherent_enfant()->q->reset()->where("id_adherent" ,$last_adherent["id_adherent"]);
		$enfants = ATF::adherent_enfant()->select_all();
		$this->assertEquals(2 , count($enfants) , "Pas le bon nombre d'enfants");
		
		
	}
	
	/** 
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test_last(){
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
		$this->obj->insert($adherent);
		ATF::adherent()->q->reset()->where("nom", "TOTO", "AND")->where("prenom", "Jojo");
		$id = ATF::adherent()->select_all();
		$id = $id[0]["id_adherent"];
		
		$this->assertEquals($id , $this->obj->last(), "Last ne renvoi pas le bon adherent");
		
	}

	/** Test du value default
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test_default_value(){
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
            "date_naissance" => "03-07-1980",
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
        "values_adherent" => array("enfants" => json_encode(Array(array("adherent_enfant__dot__prenom"=>"ddd",
								            			  "adherent_enfant__dot__nom"=>"hhh",
								            			  "adherent_enfant__dot__date_naissance"=>"2013-07-05T00:00:00",
								            			  "adherent_enfant__dot__note"=>"dd")
						)))
		);
		
		
		$this->obj->insert($adherent);
		
		ATF::adherent()->q->reset()->where("prenom", "Jojo")->where("nom", "TOTO");
		$id = ATF::adherent()->select_row();
		
		
		
		
		ATF::_r('id_adherent' , ATF::adherent()->cryptId($id["id_adherent"]));	
		$this->assertEquals((date('Y')-1980)." ans" , $this->obj->default_value("age") , "1 - Default value incorrecte");
		
		ATF::_r('id', $id["id_adherent"]);
		$this->obj->default_value("ville_naissance");
		
		
	}


      /** Test du value default
      * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
      */
      public function test_getRatio(){
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
              "values_adherent" => array("enfants" =>json_encode(Array(array("adherent_enfant__dot__prenom"=>"ddd",
                                                                                "adherent_enfant__dot__nom"=>"hhh",
                                                                                "adherent_enfant__dot__date_naissance"=>"2013-07-05T00:00:00",
                                                                                "adherent_enfant__dot__note"=>"dd"),
                                                                          array("adherent_enfant__dot__prenom"=>"aaa",
                                                                              "adherent_enfant__dot__nom"=>"bbb",
                                                                              "adherent_enfant__dot__date_naissance"=>"2013-07-05T00:00:00",
                                                                              "adherent_enfant__dot__note"=>"fff")
                                                                              ))
                                                       )
            );
            $this->obj->insert($adherent);          
            ATF::adherent()->q->reset()->addOrder("id_adherent", "desc")->setLimit(1);
            $last_adherent = ATF::adherent()->select_row();

            ATF::adherent_charge()->q->reset()->where("id_adherent",$last_adherent["id_adherent"]);
            $charge =   ATF::adherent_charge()->select_row();    


            $charges["electricite"] = 100;
            $charges["gaz"] = 50;
            $charges["eau"] = 70;
            $charges["electricite_conjoint"] = 150;
            $charges["gaz_conjoint"] = 20;
            $charges["eau_conjoint"] = 200;
            $charges["id_adherent_charge"] = $charge["adherent_charge.id_adherent_charge"];
            ATF::adherent_charge()->u($charges);

            ATF::adherent_ressource()->q->reset()->where("id_adherent" , $last_adherent["id_adherent"]);
            $ressource = ATF::adherent_ressource()->select_row();
            $ressources["salaire"] = 3000;
            $ressources["id_adherent_ressource"] = $ressource["adherent_ressource.id_adherent_ressource"];;
            ATF::adherent_ressource()->u($ressources);

            $this->assertEquals("0.197" , $this->obj->getRatio($last_adherent["id_adherent"]) , "1 -getRatio incorrect");

      }
		
}
?>