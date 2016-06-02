<?
/**
* Classe de test sur le module societe_cleodis
*/
class prolongation_test extends ATF_PHPUnit_Framework_TestCase {
	/** Méthode pré-test, exécute avant chaque test unitaire
	* besoin d'un user pour les traduction
	*/
	public function setUp() {
		$this->initUser();
	}
	
	/** Méthode post-test, exécute après chaque test unitaire*/
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}
	
	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
	public function test_defaultValues(){

		$id_affaire=ATF::affaire()->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu"));
		$id_commande=ATF::commande()->i(array("ref"=>"Ref tu","id_societe"=>$this->id_societe,"id_user"=>$this->id_user,"tva"=>"1,196","id_affaire"=>$id_affaire,"date_evolution"=>"2010-01-01"));

		ATF::_r('id_commande',$id_commande);
		$this->assertEquals($this->id_societe,$this->obj->default_value("id_societe"),'valeur id_societe');
		$this->assertEquals($id_affaire,$this->obj->default_value("id_affaire"),'valeur id_affaire');
		$this->assertEquals(4,$this->obj->default_value("id_refinanceur"),'valeur id_refinanceur');
		$this->assertEquals("Ref tu",$this->obj->default_value("ref"),'valeur ref');
		$this->assertEquals("2010-01-02",$this->obj->default_value("date_debut"),'valeur date_debut');

		$this->assertNull($this->obj->default_value("default_value"),'valeur default_value');
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
	public function test_existProlongation(){
		$id_affaire=ATF::affaire()->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu"));
		$this->assertFalse($this->obj->existProlongation($id_affaire),"Il n'y a pas de prolongation pour cette affaire");
		
		$id_prolongation=$this->obj->i(array("id_affaire"=>$id_affaire,"ref"=>"prolongTu","id_societe"=>$this->id_societe));
		$this->assertTrue($this->obj->existProlongation($id_affaire),"Il y a une prolongation pour cette affaire");
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
	public function test_getByaffaire(){
		$id_affaire=ATF::affaire()->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu"));
		$this->assertNull($this->obj->getByaffaire($id_affaire),"Il n'y a pas de prolongation pour cette affaire");
		
		$id_prolongation=$this->obj->i(array("id_affaire"=>$id_affaire,"ref"=>"prolongTu","id_societe"=>$this->id_societe));
		$this->assertEquals($this->obj->select($id_prolongation),$this->obj->getByaffaire($id_affaire),"Il y a une prolongation pour cette affaire");
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
	public function testInsert(){
		$id_affaire=ATF::affaire()->decryptId(ATF::affaire()->i(array("etat"=>"commande","date"=>date("Y-m-d"),"ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu","nature"=>"affaire")));
		$id_devis=ATF::devis()->decryptId(ATF::devis()->i(array("ref"=>"refTu","id_user"=>$this->id_user,"id_societe"=>$this->id_societe,"id_filiale"=>246,"prix"=>600,"date"=>date("Y-m-d"),"devis"=>"AffaireTu","type_contrat"=>"lld","date_accord"=>date("Y-m-d"),"etat"=>"gagne","id_contact"=>$this->id_contact,"id_affaire"=>$id_affaire,"tva"=>"1.196","loyer_unique"=>"non","prix_achat"=>0,"validite"=>date("Y-m-d"))));
		$id_devis_ligne=ATF::devis_ligne()->i(array("type"=>"portable","id_devis"=>$id_devis,"id_produit"=>6,"ref"=>"GPAC-GPS","produit"=>"Ordinateur TOM TOM GO500","quantite"=>2,"id_fournisseur"=>5385,"prix_achat"=>"0","visible"=>"oui","visibilite_prix"=>"visible"));
		$id_commande=ATF::commande()->decryptId(ATF::commande()->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"commande"=>"AffaireTu","prix_achat"=>0,"prix"=>600,"date"=>date("Y-m-d"),"id_devis"=>$id_devis,"etat"=>"mis_loyer","id_user"=>$this->id_user,"tva"=>"1.196","clause_logicielle"=>"non","date_debut"=>date("Y-m-d"),"type"=>"prelevement","id_affaire"=>$id_affaire)));
		$id_commande_ligne=ATF::commande_ligne()->i(array("id_commande"=>$id_commande,"id_produit"=>6,"ref"=>"GPAC-GPS","produit"=>"Ordinateur TOM TOM GO500","quantite"=>2,"id_fournisseur"=>5385,"prix_achat"=>"0","visible"=>"oui"));

		$prolongation["prolongation"]=array(
												"id_affaire"=>$id_affaire,
												"id_societe"=>$this->id_societe,
												"id_refinanceur"=>4,
												"prix"=>845,
												"filestoattach"=>array(
																		"facturation"=>NULL,
																		),
												"id_commande"=>$id_commande,
												"date_arret"=>date("Y-m-d")
											);

		try {
			$id_prolongation=$this->obj->decryptId($this->obj->insert($prolongation));
		} catch (error $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(875,$error,'La prolongation n aurait pas du s insérer car il n y a pas de date de fin');

		ATF::commande()->u(array("id_commande"=>$id_commande,"date_evolution"=>date("Y-m-d",strtotime(date("Y-m-01")." + 6 month"))));

		try {
			$id_prolongation=$this->obj->decryptId($this->obj->insert($prolongation));
		} catch (error $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(877,$error,'La prolongation n aurait pas du s insérer car il n y a pas de loyer');

		$prolongation["values_prolongation"]=array("loyer_prolongation"=>'[{"loyer_prolongation__dot__loyer":"100","loyer_prolongation__dot__duree":"6","loyer_prolongation__dot__assurance":"10","loyer_prolongation__dot__frais_de_gestion":"10","loyer_prolongation__dot__frequence_loyer":"mois","loyer_prolongation__dot__loyer_total":600},{"loyer_prolongation__dot__loyer":"25","loyer_prolongation__dot__duree":"2","loyer_prolongation__dot__assurance":"","loyer_prolongation__dot__frais_de_gestion":"","loyer_prolongation__dot__frequence_loyer":"trimestre","loyer_prolongation__dot__loyer_total":100},{"loyer_prolongation__dot__loyer":"25","loyer_prolongation__dot__duree":"1","loyer_prolongation__dot__assurance":"","loyer_prolongation__dot__frais_de_gestion":"","loyer_prolongation__dot__frequence_loyer":"an","loyer_prolongation__dot__loyer_total":25},{"loyer_prolongation__dot__loyer":"","loyer_prolongation__dot__duree":"","loyer_prolongation__dot__assurance":"","loyer_prolongation__dot__frais_de_gestion":"","loyer_prolongation__dot__frequence_loyer":"","loyer_prolongation__dot__loyer_total":0}]');

		try {
			$id_prolongation=$this->obj->decryptId($this->obj->insert($prolongation));
		} catch (error $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(876,$error,'La prolongation n aurait pas du s insérer car il n y a pas de frequence');
		$this->obj->q->reset()->addOrder("id_prolongation","DESC")->setDimension("row");
		$prolog_delete=$this->obj->sa();
		$this->obj->d($prolog_delete["id_prolongation"]);

		$prolongation["values_prolongation"]=array("loyer_prolongation"=>'[{"loyer_prolongation__dot__loyer":"100","loyer_prolongation__dot__duree":"6","loyer_prolongation__dot__assurance":"10","loyer_prolongation__dot__frais_de_gestion":"10","loyer_prolongation__dot__frequence_loyer":"mois","loyer_prolongation__dot__loyer_total":600},{"loyer_prolongation__dot__loyer":"25","loyer_prolongation__dot__duree":"2","loyer_prolongation__dot__assurance":"","loyer_prolongation__dot__frais_de_gestion":"","loyer_prolongation__dot__frequence_loyer":"trimestre","loyer_prolongation__dot__loyer_total":100},{"loyer_prolongation__dot__loyer":"25","loyer_prolongation__dot__duree":"1","loyer_prolongation__dot__assurance":"","loyer_prolongation__dot__frais_de_gestion":"","loyer_prolongation__dot__frequence_loyer":"an","loyer_prolongation__dot__loyer_total":25},{"loyer_prolongation__dot__loyer":"25","loyer_prolongation__dot__duree":"2","loyer_prolongation__dot__assurance":"","loyer_prolongation__dot__frais_de_gestion":"","loyer_prolongation__dot__frequence_loyer":"semestre","loyer_prolongation__dot__loyer_total":25}]');
		try {
			$id_prolongation=$this->obj->decryptId($this->obj->insert($prolongation));
		} catch (error $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(880,$error,'La prolongation n aurait pas du s insérer date inférieur');
		$this->obj->q->reset()->addOrder("id_prolongation","DESC")->setDimension("row");
		$prolog_delete=$this->obj->sa();
		$this->obj->d($prolog_delete["id_prolongation"]);

		$refresh = array();
		
		$prolongation["prolongation"]=array(
												"id_affaire"=>$id_affaire,
												"id_societe"=>$this->id_societe,
												"id_refinanceur"=>4,
												"prix"=>845,
												//"filestoattach"=>array(
												//							"facturation"=>NULL,
												//						),
												"id_commande"=>$id_commande,
												"date_arret"=>date("Y-m-d",strtotime(date("Y-m-d")." +5 year"))
											);
	
		$id_prolongation=$this->obj->decryptId($this->obj->insert($prolongation,$this->s,NULL,$refresh));
		$prolog_select=$this->obj->select($id_prolongation);
		ATF::loyer_prolongation()->q->reset()->addCondition("id_prolongation",$id_prolongation);
		$loyer_prolongation=ATF::loyer_prolongation()->sa();
		$this->assertEquals(array(
									"id_prolongation"=>$id_prolongation,
									"id_affaire"=>$id_affaire,
									"ref"=>"refTu",
									"id_refinanceur"=>4,
									"date_debut"=>date("Y-m-d",strtotime(date("Y-m-02")." + 6 month")),
									"date_fin"=>date("Y-m-d",strtotime(date("Y-m-01")." + 6 month + 3 year")),
									"id_societe"=>$this->id_societe,
									"date_arret"=>date("Y-m-d",strtotime(date("Y-m-d")." +5 year"))
									)
							,$prolog_select,'La prolongation n est pas bonne');

		ATF::loyer_prolongation()->q->reset()->addCondition("id_prolongation",$id_prolongation);
		$loyer_prolongation=ATF::loyer_prolongation()->sa();
		$this->assertEquals(array(
									"id_loyer_prolongation"=>$loyer_prolongation[0]["id_loyer_prolongation"],
									"id_affaire"=>$id_affaire,
									"id_prolongation"=>$id_prolongation,
									"loyer"=>"100.00",
									"duree"=>"6",
									"assurance"=>"10.00",
									"frais_de_gestion"=>"10.00",
									"frequence_loyer"=>"mois",
									"date_debut"=>date("Y-m-d",strtotime(date("Y-m-02")." + 6 month")),
									"date_fin"=>date("Y-m-d",strtotime(date("Y-m-01")." + 1 year"))
									)
							,$loyer_prolongation[0],'Le loyer_prolongation n est pas bon 0');

		$this->assertEquals(array(
									"id_loyer_prolongation"=>$loyer_prolongation[1]["id_loyer_prolongation"],
									"id_affaire"=>$id_affaire,
									"id_prolongation"=>$id_prolongation,
									"loyer"=>"25.00",
									"duree"=>"2",
									"assurance"=>NULL,
									"frais_de_gestion"=>NULL,
									"frequence_loyer"=>"trimestre",
									"date_debut"=>date("Y-m-d",strtotime(date("Y-m-02")." + 1 year")),
									"date_fin"=>date("Y-m-d",strtotime(date("Y-m-01")." + 6 month + 1 year"))
									)
							,$loyer_prolongation[1],'Le loyer_prolongation n est pas bon 1');
		

		$this->assertEquals(array(
									"id_loyer_prolongation"=>$loyer_prolongation[2]["id_loyer_prolongation"],
									"id_affaire"=>$id_affaire,
									"id_prolongation"=>$id_prolongation,
									"loyer"=>"25.00",
									"duree"=>"1",
									"assurance"=>NULL,
									"frais_de_gestion"=>NULL,
									"frequence_loyer"=>"an",
									"date_debut"=>date("Y-m-d",strtotime(date("Y-m-02")." + 6 month + 1 year")),
									"date_fin"=>date("Y-m-d",strtotime(date("Y-m-01")." + 6 month + 2 year"))
									)
							,$loyer_prolongation[2],'Le loyer_prolongation n est pas bon 2');

		$this->assertEquals(array(
									"id_loyer_prolongation"=>$loyer_prolongation[3]["id_loyer_prolongation"],
									"id_affaire"=>$id_affaire,
									"id_prolongation"=>$id_prolongation,
									"loyer"=>"25.00",
									"duree"=>"2",
									"assurance"=>NULL,
									"frais_de_gestion"=>NULL,
									"frequence_loyer"=>"semestre",
									"date_debut"=>date("Y-m-d",strtotime(date("Y-m-02")." + 6 month + 2 year")),
									"date_fin"=>date("Y-m-d",strtotime(date("Y-m-01")." + 6 month + 3 year"))
									)
							,$loyer_prolongation[3],'Le loyer_prolongation n est pas bon 3');

		
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
	public function testUpdateDate(){
		
	}

	
	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
	public function testUpdateFinProlongation(){
		$id_affaire=ATF::affaire()->decryptId(ATF::affaire()->i(array("etat"=>"commande","date"=>date("Y-m-d"),"ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu","nature"=>"affaire")));
		$id_prolongation=ATF::prolongation()->i(array("id_affaire"=>$id_affaire,"ref"=>"prolongTu","id_societe"=>$this->id_societe,"date_debut"=>date("2011-01-01"),"date_fin"=>"2012-09-30"));
		$id_loyer_prolongation1=ATF::loyer_prolongation()->i(array("id_affaire"=>$id_affaire,"id_prolongation"=>$id_prolongation,"loyer"=>"100","duree"=>3,"frequence_loyer"=>"mois","date_debut"=>"2011-01-01","date_fin"=>"2011-03-31"));
		$id_loyer_prolongation2=ATF::loyer_prolongation()->i(array("id_affaire"=>$id_affaire,"id_prolongation"=>$id_prolongation,"loyer"=>"50","duree"=>2,"frequence_loyer"=>"trimestre","date_debut"=>"2011-04-01","date_fin"=>"2011-09-30"));

		$id_facture = ATF::facture()->i(
			array(
				"id_affaire"=>$id_affaire
				,"id_societe"=>$this->id_societe
				,"tva"=>"1.196"
				,"id_user"=>$this->id_user
				,"date"=>"2010-01-01"
				,"type_facture"=>"facture"
				,"ref"=>"ref" //champs obligatoire
			)
		);

		$id_facturation1=ATF::facturation()->i(array("id_affaire"=>$id_affaire,"id_societe"=>$this->id_societe,"id_facture"=>NULL,"montant"=>100,"date_periode_debut"=>"2011-01-01","date_periode_fin"=>"2011-01-31","envoye"=>"oui"));
		$id_facturation2=ATF::facturation()->i(array("id_affaire"=>$id_affaire,"id_societe"=>$this->id_societe,"id_facture"=>NULL,"montant"=>100,"date_periode_debut"=>"2011-02-01","date_periode_fin"=>"2011-02-28","envoye"=>"oui"));
		$id_facturation3=ATF::facturation()->i(array("id_affaire"=>$id_affaire,"id_societe"=>$this->id_societe,"id_facture"=>NULL,"montant"=>100,"date_periode_debut"=>"2011-04-01","date_periode_fin"=>"2011-06-30","envoye"=>"oui"));
		$id_facturation4=ATF::facturation()->i(array("id_affaire"=>$id_affaire,"id_societe"=>$this->id_societe,"id_facture"=>NULL,"montant"=>100,"date_periode_debut"=>"2011-07-01","date_periode_fin"=>"2011-09-30","envoye"=>"oui"));
		$id_facturation5=ATF::facturation()->i(array("id_affaire"=>$id_affaire,"id_societe"=>$this->id_societe,"id_facture"=>$id_facture,"montant"=>100,"date_periode_debut"=>"2011-10-01","date_periode_fin"=>"2011-12-31","envoye"=>"oui"));

		$infos["id_prolongation"]=$id_prolongation;
		$infos["key"]="date_arret";
		$infos["value"]="01-01-2010";
		
		try {
			$updateDate=$this->obj->updateDate($infos);
		} catch (error $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(879,$error,'La date insérée est inférieure à la date de prolongation');
		
		$infos["value"]="31-12-2011";
		try {
			$updateDate=$this->obj->updateDate($infos);
		} catch (error $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(878,$error,'On ne peut pas supprimer une facture');

		ATF::facturation()->u(array("id_facturation"=>$id_facturation5,"id_facture"=>NULL));
		$this->assertTrue($this->obj->updateDate($infos),'Problème updateDate 1');
					
		$this->assertNull(ATF::facturation()->select($id_facturation5),'La facturation ne c est pas bien supprimée');
		$this->assertEquals("2011-12-31",$this->obj->select($id_prolongation,"date_arret"),'La prolongation ne prend pas la date arrêt');

		$infos["value"]=NULL;	
		$this->assertTrue($this->obj->updateDate($infos),'Problème updateDate 2');
		$this->assertNull($this->obj->select($id_prolongation,"date_arret"),'La date arrêt ne se supprime pas');

		$infos["key"]="date_fin";
		$this->assertTrue($this->obj->updateDate($infos),'Problème updateDate 3');

		$notices = ATF::$msg->getNotices();
		$this->assertEquals(count($notices),1,'Le nombre de notices est incorrect');
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
	public function testDelete(){
		$id_affaire=ATF::affaire()->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu"));
		$id_prolongation=$this->obj->i(array("id_affaire"=>$id_affaire,"ref"=>"prolongTu","id_societe"=>$this->id_societe));
		
		$infos["id"][]=$id_prolongation;
		$this->obj->delete($infos);
		$this->assertNull($this->obj->select($id_prolongation),'La prolongation ne c est pas bien supprimée');
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
	public function testUnsetDate(){
		$id_affaire=ATF::affaire()->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu"));
		$id_prolongation=$this->obj->i(array("id_affaire"=>$id_affaire,"ref"=>"prolongTu","id_societe"=>$this->id_societe));
	
		$id_loyer_prolongation1=ATF::loyer_prolongation()->i(array("id_affaire"=>$id_affaire,"id_prolongation"=>$id_prolongation,"loyer"=>"100","duree"=>12,"frequence_loyer"=>"mois","date_debut"=>"2010-01-01","date_fin"=>"2010-12-31"));
		$id_loyer_prolongation2=ATF::loyer_prolongation()->i(array("id_affaire"=>$id_affaire,"id_prolongation"=>$id_prolongation,"loyer"=>"100","duree"=>6,"frequence_loyer"=>"mois","date_debut"=>"2011-01-01","date_fin"=>"2011-07-01"));
		
		$this->obj->unsetDate($id_affaire);
		
		$loyer_prolongation1=ATF::loyer_prolongation()->select($id_loyer_prolongation1);
		$this->assertNull($loyer_prolongation1["date_debut"],'unset n a pas supprimée la date_debut');
		$this->assertNull($loyer_prolongation1["date_fin"],'unset n a pas supprimée la date_fin');

		$loyer_prolongation2=ATF::loyer_prolongation()->select($id_loyer_prolongation2);
		$this->assertNull($loyer_prolongation2["date_debut"],'unset n a pas supprimée la date_debut');
		$this->assertNull($loyer_prolongation2["date_fin"],'unset n a pas supprimée la date_fin');
		
	}


	 /* @author NMorgan FLEURQUIN <mfleurquin@absystech.fr> 
    * @date 08/12/2015
    */
    public function test_uploadFileFromSA() {

    	$id_affaire=ATF::affaire()->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu"));
		$id_prolongation=$this->obj->i(array("id_affaire"=>$id_affaire,"ref"=>"prolongTu","id_societe"=>$this->id_societe));
	
        


        $infos = array(
            "extAction"=>"prolongation"
        );
        $this->assertFalse($this->obj->uploadFileFromSA($infos),"Erreur, pas d'id en entrée, renvoi FALSE");        
        $infos = array(
            "id"=>$id_prolongation
        );
        $this->assertFalse($this->obj->uploadFileFromSA($infos),"Erreur, pas de class en entrée, renvoi FALSE");

        $infos['extAction'] = "prolongation";
        $infos['field'] = "tu";
        $this->assertFalse($this->obj->uploadFileFromSA($infos),"Erreur, pas de files en entrée, renvoi FALSE");
        
        $file = __ABSOLUTE_PATH__."test/cleodis/pdf_exemple.pdf";
        $files = array(
            "tu"=> array(
                "name"=>"pdf_exemple"
                ,"type"=>"application/pdf"
                ,"tmp_name"=>$file
                ,"error"=>0
                ,"size"=>filesize($file)
            )
        );
        if(!file_exists(__ABSOLUTE_PATH__."../temp/testsuite/prolongation/"))util::mkdir(__ABSOLUTE_PATH__."../temp/testsuite/prolongation/");
        if(!file_exists(__ABSOLUTE_PATH__."../temp/testsuite/pdf_affaire/"))util::mkdir(__ABSOLUTE_PATH__."../temp/testsuite/pdf_affaire/");
        
        $r = $this->obj->uploadFileFromSA($infos,ATF::_s(),$files);
        $this->assertEquals('{"success":true}',$r,"Erreur dans le retour de l'upload");
        $f = __ABSOLUTE_PATH__."../data/testsuite/prolongation/".$id_prolongation.".tu";
        $this->assertTrue(file_exists($f),"Erreur : le fichier n'est pas là !");
        unlink($f);

        ATF::pdf_affaire()->q->reset()->where("id_affaire",$id_affaire);
        $pdf_affaire = ATF::pdf_affaire()->select_all();

        $f = __ABSOLUTE_PATH__."../data/testsuite/pdf_affaire/".$pdf_affaire[0]["id_pdf_affaire"].".fichier_joint";
        $this->assertTrue(file_exists($f),"Erreur : le fichier pdf_affaire n'est pas là !");
        unlink($f);

    }

};
?>