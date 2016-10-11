<?
class formation_commande_fournisseur_test extends ATF_PHPUnit_Framework_TestCase {

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
		new formation_commande_fournisseur();
	}
	
	/*
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test_insert(){
		$infos = array("id_formation_devis" => $this->id_devis_formation , "id_formation_commande" => $this->id_commande_formation ,"objectif" => "L'objectif de la formation", "id_user"=>97 );
		$return = $this->obj->insert(array("preview" => true, "formation_commande_fournisseur" => $infos));
		$this->assertNotNull($return,'Formation_commande non créé 1 !');

		$formation_commande_fournisseur = $this->obj->insert(array("formation_commande_fournisseur" => $infos));
		$this->assertNotNull($formation_commande_fournisseur,'formation_commande_fournisseur non créé 2 !');

	}

	/*
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test_default_value(){
		ATF::_r('id_formation_commande',$this->id_commande_formation);

		$this->assertEquals($this->id_devis_formation , $this->obj->default_value("id_formation_devis") , "ID devis formation incorrect");
		$this->assertEquals($this->id_commande_formation , $this->obj->default_value("id_formation_commande") , "id_formation_commande incorrect");
		$this->assertEquals(97 , $this->obj->default_value("id_user") , "id_user incorrect");
		$this->assertEquals(date("Y-m-d") , $this->obj->default_value("date_envoi") , "date_envoi incorrect");
		$this->assertEquals("" , $this->obj->default_value("objectif") , "objectif incorrect");
	}
}