<?php

class adherent_charge_test extends ATF_PHPUnit_Framework_TestCase {
	
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
		$c = new adherent_charge();	
		$this->assertTrue($c instanceOf adherent_charge, "L'objet adherent_charge n'est pas de bon type");
	}	
	
	/** Test de update
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test_update(){
				
		ATF::adherent_charge()->q->reset()->where("id_adherent", $this->adherent["id_adherent"]);
		$charge = 	ATF::adherent_charge()->select_row();		
		
		$charge["id_adherent_charge"] = $charge["adherent_charge.id_adherent_charge"];
		unset($charge["adherent_charge.id_adherent_charge"]);
		$charge["electricite"]	= 300;
		$charge["electricite_conjoint"] = 200;
		$charge["electricite_charge"] = 100;
		$this->obj->update(array("adherent_charge" =>$charge));
		$charge = ATF::adherent_charge()->select($this->adherent["id_adherent"]);
		
		$this->assertEquals("300.00", $charge["electricite"] , "Montant Electricite Incorrect");
		$this->assertEquals("200.00", $charge["electricite_conjoint"] , "Montant Electricite conjoint Incorrect");
		$this->assertEquals("100.00", $charge["electricite_charge"] , "Montant Electricite charge Incorrect");
					
	}
	
	/** Test du select_all
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test_select_all(){
		ATF::adherent_charge()->q->reset()->where("id_adherent", $this->adherent["id_adherent"]);
		$charge = 	ATF::adherent_charge()->select_row();		
		
		$charge["id_adherent_charge"] = $charge["adherent_charge.id_adherent_charge"];
		unset($charge["adherent_charge.id_adherent_charge"]);
		$charge["amende"]	= 100;
		$charge["electricite"]	= 300;
		$charge["electricite_conjoint"] = 200;
		$charge["electricite_charge"] = 100;
		$this->obj->update(array("adherent_charge" =>$charge));
		$charge = ATF::adherent_charge()->select($this->adherent["id_adherent"]);			
		
		$this->obj->q->reset()->where("id_adherent_charge",$charge["id_adherent_charge"])->end();
		$r = $this->obj->select_all();
		
		$this->assertEquals("300.00", $r[0]["total_mensuel"] , "total_mensuel Incorrect");
		$this->assertEquals("200.00", $r[0]["total_mensuel_conjoint"] , "total_mensuel_conjoint Incorrect");
		$this->assertEquals("200.00", $r[0]["total_impaye"] , "total_impaye Incorrect");
		$this->assertEquals("3600.00", $r[0]["total_annuel"] , "total_annuel Incorrect");
		$this->assertEquals("2400.00", $r[0]["total_conjoint"] , "total_conjoint Incorrect");
		
	}
	
	/** Test du selectCalcule
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test_selectCalcule(){
		ATF::adherent_charge()->q->reset()->where("id_adherent", $this->adherent["id_adherent"]);
		$charge = 	ATF::adherent_charge()->select_row();		
		
		$charge["id_adherent_charge"] = $charge["adherent_charge.id_adherent_charge"];
		unset($charge["adherent_charge.id_adherent_charge"]);
		$charge["amende"]	= 100;
		$charge["electricite"]	= 300;
		$charge["electricite_conjoint"] = 200;
		$charge["electricite_charge"] = 100;
		$this->obj->update(array("adherent_charge" =>$charge));
		$charge = ATF::adherent_charge()->select($this->adherent["id_adherent"]);			
		
		
		$r = $this->obj->selectCalcule($this->adherent["id_adherent"]);
		
		$this->assertEquals("300.00", $r[0]["total_mensuel"] , "total_mensuel Incorrect");
		$this->assertEquals("200.00", $r[0]["total_mensuel_conjoint"] , "total_mensuel_conjoint Incorrect");
		$this->assertEquals("200.00", $r[0]["total_impaye"] , "total_impaye Incorrect");
		$this->assertEquals("3600.00", $r[0]["total_annuel"] , "total_annuel Incorrect");
		$this->assertEquals("2400.00", $r[0]["total_conjoint"] , "total_conjoint Incorrect");	
	}

	public  function test_getAdherent_charge(){
		ATF::adherent_charge()->q->reset()->where("id_adherent", $this->adherent["id_adherent"]);
		$charge = 	ATF::adherent_charge()->select_row();		
		
		$charge["id_adherent_charge"] = $charge["adherent_charge.id_adherent_charge"];	
				
		$this->assertEquals($charge["id_adherent_charge"] , $this->obj->getAdherent_charge($this->adherent["id_adherent"]), "Pas le bon Id");
	}

	public function test_default_value(){
		ATF::adherent_charge()->q->reset()->where("id_adherent", $this->adherent["id_adherent"]);
		$charge = 	ATF::adherent_charge()->select_row();		
		
		$charge["id_adherent_charge"] = $charge["adherent_charge.id_adherent_charge"];
		unset($charge["adherent_charge.id_adherent_charge"]);
		$charge["amende"]	= 3000;
		$charge["remb_pret_immo"] = 200;
		$charge["electricite_conjoint"] = 500;
		$this->obj->update(array("adherent_charge" =>$charge));		
		
		ATF::_r("id" ,"");
		ATF::_r("id_adherent_charge" , "");
			
		
		ATF::_r("id_adherent_charge" ,$charge["id_adherent_charge"]);
		$this->assertEquals(200 , $this->obj->default_value("total_mensuel") , "1 - Default value incorrect");		
		$this->assertEquals(500 , $this->obj->default_value("total_mensuel_conjoint") , "2 - Default value incorrect");
		$this->obj->default_value("amende");
		
		ATF::_r("id" ,$charge["id_adherent_charge"]);
		$this->assertEquals(200*12 , $this->obj->default_value("total_annuel") , "3 - Default value incorrect");		
		$this->assertEquals(500*12 , $this->obj->default_value("total_conjoint") , "4 - Default value incorrect");
		$this->assertEquals(3000 , $this->obj->default_value("total_impaye") , "5 - Default value incorrect");
	}

}
?>