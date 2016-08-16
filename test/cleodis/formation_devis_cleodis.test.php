<?
class formation_devis_cleodis_test extends ATF_PHPUnit_Framework_TestCase {

	/*
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function environnement(){		
			$this->environnementFormation();
	}


	/** Méthode pré-test, exécute avant chaque test unitaire
	* besoin d'un user pour les traduction
	*/
	public function setUp() {
		ATF::db()->begin_transaction(true);
		$this->environnement();
	}
	
	/* Méthode post-test, exécute après chaque test unitaire */
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}

	/*
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test_construct(){
		new formation_devis_cleodis();
	}

	/*
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test_default_value(){
		$this->assertEquals(date("Y-m-d"), $this->obj->default_value("date"), "default_value ne renvoie pas la bonne valeur !");
		$this->assertEquals("", $this->obj->default_value("thematique"), "default_value 2 ne renvoie pas la bonne valeur !");

		$this->assertEquals(date("Y-m-d", strtotime("+15 day")) , $this->obj->default_value("date_validite"), "default_value 3 ne renvoie pas la bonne valeur !");
	
		$fde = new formation_devis_exactitude();
		$this->assertEquals("", $fde->default_value("thematique"), "default_value exactitude 2 ne renvoie pas la bonne valeur !");
	}

	/*
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test_insert(){
		$infos = array(  "numero_dossier" => "123456789"
						,"thematique" => "La thématique de la formation"
						,"date" => date("Y-m-d")						
						,"nb_heure" => "20"
						,"prix" => "50"
						,"id_owner"=>97
						,"id_contact"=>81
						,"id_societe" => $this->id_societe
						,"contact" => NULL
						,"type"=>"normal"
						,"opca"=>array(246,253)
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
																				 "formation_devis_fournisseur__dot__type"=>"lieu_formation",
																				 "formation_devis_fournisseur__dot__montant"=>3000),
																				array("formation_devis_fournisseur__dot__id_societe_fk"=>"246",
																					  "formation_devis_fournisseur__dot__type"=>"apporteur_affaire",
																					  "formation_devis_fournisseur__dot__montant"=>3500)																				
																				)
																		)
					);

		try {
			$this->obj->insert(array("preview" => true, "formation_devis" => $infos, "values_formation_devis" => $dates));
		} catch (errorATF $e) {
			$error = $e->getMessage();
		}
		$this->assertEquals("Il faut au moins 1 participant à la formation" , $error , "Message d'erreur incorrect !");


		$infos["contact"] = array($this->id_contact1, $this->id_contact2, $this->id_contact3);
		$return = $this->obj->insert(array("preview" => true, "formation_devis" => $infos, "values_formation_devis" => $dates));
		$this->assertNotNull($return,'Devis non créé 1 !');

		$id_devis_formation = $this->obj->insert(array("formation_devis" => $infos, "values_formation_devis" => $dates));
		$this->assertNotNull($id_devis_formation,'Devis non créé 2 !');
	}

	/*
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test_perdu(){
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
																				 "formation_devis_fournisseur__dot__type"=>"lieu_formation",
																				 "formation_devis_fournisseur__dot__montant"=>3000),
																				array("formation_devis_fournisseur__dot__id_societe_fk"=>"246",
																					  "formation_devis_fournisseur__dot__type"=>"apporteur_affaire",
																					  "formation_devis_fournisseur__dot__montant"=>3500)																				
																				)
																		)
					);
		$id_devis_formation = $this->obj->insert(array("formation_devis" => $infos, "values_formation_devis" => $dates));

		$this->obj->perdu(array("id_formation_devis"=> $id_devis_formation));
		$this->assertEquals("perdu" , $this->obj->select($id_devis_formation , "etat") , "Le devis n'est pas passé en perdu !");


		$this->obj->u(array("id_formation_devis" => $id_devis_formation, "etat" => "gagne"));
		try {
			$this->obj->perdu(array("id_formation_devis"=> $id_devis_formation));
		} catch (errorATF $e) {
			$error = $e->getMessage();
		}
		$this->assertEquals("Impossible de passer un devis gagné en 'perdu'" , $error , "Message d'erreur incorrect !");

		ATF::$msg->getNotices();

	}	

	/*
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test_getCommandeFournisseur(){
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
		$id_devis_formation = $this->obj->insert(array("formation_devis" => $infos , "values_formation_devis" => $dates));


		$this->assertEquals(NULL , $this->obj->getCommandeFournisseur($id_devis_formation), "Il n'y a pas encore de commande fournisseur dessus !");

		$this->assertEquals(NULL , $this->obj->getCommandeFournisseur(), "Doit renvoyer NULL si on ne passe pas de param !");

	}


	/*
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test_update(){
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
		$id_devis_formation = $this->obj->insert(array("formation_devis" => $infos , "values_formation_devis" => $dates));


		$infos["id_formation_devis"] =  $id_devis_formation;
		$infos["contact"] = NULL;
		try {
			$this->obj->update(array("preview" => true, "formation_devis" => $infos));
		} catch (errorATF $e) {
			$error = $e->getMessage();
		}
		$this->assertEquals("Il faut au moins 1 participant à la formation" , $error , "Message d'erreur incorrect !");

		$infos = array("id_formation_devis"=> $id_devis_formation, "contact" => array($this->id_contact1, $this->id_contact2, $this->id_contact3), "numero_dossier" => "TEST" ,"opca"=>array(246,253) );
		
		$this->obj->u(array("id_formation_devis"=> $id_devis_formation, "etat"=>"perdu"));
		try {
			$this->obj->update(array("preview" => true, "formation_devis" => $infos , "values_formation_devis" => $dates));
		} catch (errorATF $e) {
			$error = $e->getMessage();
		}
		$this->assertEquals("Impossible de modifier un devis qui n'est plus en attente !" , $error , "Message d'erreur 2 incorrect !");
		
		$this->obj->u(array("id_formation_devis"=> $id_devis_formation, "etat"=>"attente"));

		
		
		$this->obj->update(array("preview" => true, "formation_devis" => $infos, "values_formation_devis" => $dates));
		$this->assertEquals("TEST" , $this->obj->select($id_devis_formation , "numero_dossier"),'Devis non updaté 1 !');

		$this->obj->update(array("formation_devis" => $infos, "values_formation_devis" => $dates));
		$this->assertEquals("TEST" , $this->obj->select($id_devis_formation , "numero_dossier"),'Devis non updaté 2 !');
	}

	/*
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test_can_update(){
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
		$id_devis_formation = $this->obj->insert(array("formation_devis" => $infos , "values_formation_devis" => $dates));

		$this->obj->u(array("id_formation_devis"=> $id_devis_formation , "etat"=>"gagne"));

		try {
			$this->obj->can_update($id_devis_formation);
		} catch (errorATF $e) {
			$error = $e->getMessage();
		}
		$this->assertEquals("Impossible de modifier/supprimer ce Devis formation car il n'est plus en 'En attente'" , $error , "Message d'erreur 2 incorrect !");
	}


	/*@author Morgan FLEURQUIN <mfleurquin@absystech.fr>  */
	public function test_getRef(){
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
		$id_devis_formation = $this->obj->insert(array("formation_devis" => $infos, "values_formation_devis" => $dates));

		$this->assertEquals("FCL20000001",	$this->obj->getRef(date("Y-m-d", strtotime("2000-01-01"))), 	"getRef pas 0");	



		$this->obj->u(array("id_formation_devis"=> $id_devis_formation, "numero_dossier"=> "FCL20000020" ));
		$this->assertEquals("FCL20000021",	$this->obj->getRef(date("Y-m-d", strtotime("2000-01-01"))), 	"getRef pas 10");

		$this->obj->u(array("id_formation_devis"=> $id_devis_formation, "numero_dossier"=> "FCL20000200" ));
		$this->assertEquals("FCL20000201",	$this->obj->getRef(date("Y-m-d", strtotime("2000-01-01"))), 	"getRef pas 100");

		$this->obj->u(array("id_formation_devis"=> $id_devis_formation, "numero_dossier"=> "FCL20002000" ));
		$this->assertEquals("FCL20002001",	$this->obj->getRef(date("Y-m-d", strtotime("2000-01-01"))), 	"getRef pas 1000");
	}




	/*@author Morgan FLEURQUIN <mfleurquin@absystech.fr>  */
	public function test_getMontantFournisseur(){
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
																					 "formation_devis_fournisseur__dot__type"=>"lieu_formation",
																					 "formation_devis_fournisseur__dot__montant"=>3000),
																					array("formation_devis_fournisseur__dot__id_societe_fk"=>"246",
																						  "formation_devis_fournisseur__dot__type"=>"apporteur_affaire",
																						  "formation_devis_fournisseur__dot__montant"=>3500)																				
																					)
																			)
						);
			$id_devis_formation = $this->obj->insert(array("formation_devis" => $infos, "values_formation_devis" => $dates));



			$this->assertEquals(3500 , $this->obj->getMontantFournisseur(array("id_formation_devis"=> $id_devis_formation, "id_fournisseur"=> 246)), "Le montant est incohérent !");
			$this->assertEquals(0.00 , $this->obj->getMontantFournisseur(array("id_formation_devis"=> $id_devis_formation, "id_fournisseur"=> 50)), "Le montant 2 est incohérent !");
	}

	/*@author Morgan FLEURQUIN <mfleurquin@absystech.fr>  */
	public function test_getMontantForFacture(){
		$infos = array(  "numero_dossier" => "123456789"
							,"thematique" => "La thématique de la formation"
							,"date" => date("Y-m-d")						
							,"nb_heure" => "20"
							,"prix" => "50"
							,"acompte"=>200
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
																					 "formation_devis_fournisseur__dot__type"=>"lieu_formation",
																					 "formation_devis_fournisseur__dot__montant"=>3000),
																					array("formation_devis_fournisseur__dot__id_societe_fk"=>"246",
																						  "formation_devis_fournisseur__dot__type"=>"apporteur_affaire",
																						  "formation_devis_fournisseur__dot__montant"=>3500)																				
																					)
																			)
						);
			$id_devis_formation = $this->obj->insert(array("formation_devis" => $infos, "values_formation_devis" => $dates));

			$this->assertEquals("3000.00" , $this->obj->getMontantForFacture(array("id_formation_devis"=> $id_devis_formation, "type"=> "normale")), "Le montant est incohérent !");
			$this->assertEquals(200 , $this->obj->getMontantForFacture(array("id_formation_devis"=> $id_devis_formation, "type"=> "acompte")), "Le montant 2 est incohérent !");
			$this->assertEquals(0.00 , $this->obj->getMontantForFacture(array("id_formation_devis"=> $id_devis_formation, "type"=> "rien")), "Le montant 3 est incohérent !");
	


	}

	/*@author Morgan FLEURQUIN <mfleurquin@absystech.fr>  */
	public function test_getPriseEnCharge(){
		$infos = array(  "numero_dossier" => "123456789"
							,"thematique" => "La thématique de la formation"
							,"date" => date("Y-m-d")						
							,"nb_heure" => "20"
							,"prix" => "50"
							,"acompte"=>200
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
																					 "formation_devis_fournisseur__dot__type"=>"lieu_formation",
																					 "formation_devis_fournisseur__dot__montant"=>3000),
																					array("formation_devis_fournisseur__dot__id_societe_fk"=>"246",
																						  "formation_devis_fournisseur__dot__type"=>"apporteur_affaire",
																						  "formation_devis_fournisseur__dot__montant"=>3500)																				
																					)
																			)
						);
			$id_devis_formation = $this->obj->insert(array("formation_devis" => $infos, "values_formation_devis" => $dates));

			$this->assertEquals(false , $this->obj->getPriseEnCharge($id_devis_formation), "Erreur 1 !");
			

			ATF::formation_priseEnCharge()->insert(array("id_formation_devis"=>$id_devis_formation,
												"opca"=>6491,
												"etat"=>"attente_element",
												"montant_demande"=>3500,
												"date_envoi"=>date("Y-m-d")
											));

			$this->assertEquals(true , $this->obj->getPriseEnCharge($id_devis_formation), "Erreur 2 !");
			


	}

	public function test_opcaIn(){
		$this->assertEquals(true,$this->obj->opcaIn("1234|1548", array("id_societe"=>1234)), "Erreur OpcaIn 1");
		$this->assertEquals(false,$this->obj->opcaIn("1234|1548",  array("id_societe"=>4321)), "Erreur OpcaIn 2");

	}
}