<?
class formation_priseEnCharge_test extends ATF_PHPUnit_Framework_TestCase {

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
		ATF::db()->rollback_transaction(true);
	}

	/*
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test_construct(){
		new formation_priseEnCharge();
	}

	/*
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test_default_value(){
		$this->assertEquals(date("Y-m-d") , $this->obj->default_value("date_envoi") , "default_value 1 error!");
		$this->assertEquals("" , $this->obj->default_value("ref") , "default_value 2 error!");
	}

	public function test_delete(){
		$id1 = ATF::formation_priseEnCharge()->i(array("id_formation_devis"=>$this->id_devis_formation,"ref"=>"test1","montant_demande"=>2000, "opca"=>6491, "date_envoi"=>date("Y-m-d")));
		$id2 = ATF::formation_priseEnCharge()->i(array("id_formation_devis"=>$this->id_devis_formation,"ref"=>"test2","montant_demande"=>2000, "opca"=>6491, "date_envoi"=>date("Y-m-d")));

		$this->obj->delete(array("id"=>array($id1,$id2)));

		ATF::formation_priseEnCharge()->q->reset()->where("id_formation_devis",$this->id_devis_formation);

		$this->assertNull(ATF::formation_priseEnCharge()->select_all(), "Probleme de delete ?");
	}	
}