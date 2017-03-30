<?
/**
* Classe de test sur le module societe_cleodis
*/
class facture_cleodis_test extends ATF_PHPUnit_Framework_TestCase {
	public function setUp() {
		$this->initUser();
		
		$this->id_soc=ATF::societe()->i(array("societe"=>"soc lol2"));
		$this->id_aff=ATF::affaire()->i(array("ref"=>"ref lol1","id_societe"=>$this->id_soc,"affaire"=>"aff lol"));
		$this->loyer = ATF::loyer()->i(array("id_affaire"=> $this->id_aff, "loyer"=>50, "duree"=> 3, "frequence_loyer"=>"mois"));
		
		$this->id_aff2=ATF::affaire()->i(array("ref"=>"ref lol2","id_societe"=>$this->id_soc,"affaire"=>"aff lol2"));
		$this->loyer2 = ATF::loyer()->i(array("id_affaire"=> $this->id_aff2, "loyer"=>50, "duree"=> 12, "frequence_loyer"=>"trimestre"));
		
		$this->id_aff3=ATF::affaire()->i(array("ref"=>"ref lol3","id_societe"=>$this->id_soc,"affaire"=>"aff lol3"));
		$this->loyer3 = ATF::loyer()->i(array("id_affaire"=> $this->id_aff3, "loyer"=>50, "duree"=> 3, "frequence_loyer"=>"an"));
		$this->refi = ATF::demande_refi()->i(array("date" => date("Y-m-d"), "id_refinanceur"=>1, "id_affaire"=> $this->id_aff3, "id_contact"=>81,"id_societe"=> $this->id_soc, "description"=> "toto", "etat"=> "valide"));
		
		$this->id_fac=ATF::facture()->i(array("ref"=>"ref lol1","id_societe"=>$this->id_soc,"prix"=>45,"date"=>date("Y-m-d"),"tva"=>"19.6","id_affaire"=>$this->id_aff));
		$this->id_fac2=ATF::facture()->i(array("ref"=>"ref lol2","id_societe"=>$this->id_soc,"prix"=>45,"date"=>date("Y-m-d"),"tva"=>"19.6","id_affaire"=>$this->id_aff2));
		$this->id_fac3=ATF::facture()->i(array("ref"=>"ref lol3","id_societe"=>$this->id_soc,"prix"=>45,"date"=>date("Y-m-d"),"tva"=>"19.6","id_affaire"=>$this->id_aff3));
				
		ATF::facturation()->i(array("id_affaire" => $this->id_aff,"montant"=> 10, "frais_de_gestion"=> 10,"id_societe"=>$this->id_soc, "id_facture" => $this->id_fac, "date_periode_debut" => "2012-01-01", "date_periode_fin"=> date("Y-m-d")));
		ATF::facturation()->i(array("id_affaire" => $this->id_aff2,"montant"=> 10, "frais_de_gestion"=> 10,"id_societe"=>$this->id_soc, "id_facture" => $this->id_fac2, "date_periode_debut" => "2012-01-01", "date_periode_fin"=> date("Y-m-d")));
		ATF::facturation()->i(array("id_affaire" => $this->id_aff3,"montant"=> 10, "frais_de_gestion"=> 10,"id_societe"=>$this->id_soc, "id_facture" => $this->id_fac2, "date_periode_debut" => "2012-01-01", "date_periode_fin"=> date("Y-m-d")));
	}
	
	public function tearDown() {
		ATF::$msg->getNotices();
		ATF::db()->rollback_transaction(true);
	}

    /*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
    public function insertFacture($relance = false){

        $this->id_affaire=ATF::affaire()->decryptId(ATF::affaire()->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu")));
        
        $this->id_commande=ATF::commande()->decryptId(ATF::commande()->i(array("ref"=>"Ref tu","id_societe"=>$this->id_societe,"id_user"=>$this->id_user,"tva"=>"1,196","id_affaire"=>$this->id_affaire)));
        
        $this->id_demande_refi=ATF::demande_refi()->decryptId(ATF::demande_refi()->i(array(
                                                                                            "date"=>date("Y-m-d"),
                                                                                            "id_contact"=>$this->id_contact,
                                                                                            "id_refinanceur"=>4,
                                                                                            "id_affaire"=>$this->id_affaire,
                                                                                            "id_societe"=>$this->id_societe,
                                                                                            "description"=>"Tu description",
                                                                                            "loyer_actualise"=>50,
                                                                                            "etat"=>"valide"
                                                                                            )
                                                                                        )
                                                                                    );

        $this->id_facture=ATF::facture()->decryptId(ATF::facture()->i(array(
                                                                        "ref"=>"refTU",
                                                                        "type_facture"=>"facture",
                                                                        "id_societe"=>$this->id_societe,
                                                                        "prix"=>120,
                                                                        "date"=>date("Y-m-d",strtotime(date("Y-m-01")." 1 month")),
                                                                        "date_previsionnelle"=>date("Y-m-d",strtotime(date("Y-m-01")." 2 month")),
                                                                        "date_paiement"=>date("Y-m-d",strtotime(date("Y-m-01")." + 3 month")),
                                                                        "date_relance"=>date("Y-m-d",strtotime(date("Y-m-01")." + 4 month")),
                                                                        "date_periode_debut"=>date("Y-m-01"),
                                                                        "date_periode_fin"=>date("Y-m-d",strtotime(date("Y-m-01")." + 1 month")),
                                                                        "tva"=>"1.196",
                                                                        "id_user"=>$this->id_user,
                                                                        "id_commande"=>$this->id_commande,
                                                                        "id_affaire"=>$this->id_affaire,
                                                                        "id_demande_refi"=>$this->id_demande_refi
                                                                        )));

        $this->id_facture_ligne1=ATF::facture_ligne()->decryptId(ATF::facture_ligne()->i(array(
                                                                                            "id_facture"=>$this->id_facture,
                                                                                            "produit"=>"produitTU",
                                                                                            "quantite"=>1,
                                                                                            )));
        
        $this->id_facture_ligne2=ATF::facture_ligne()->decryptId(ATF::facture_ligne()->i(array(
                                                                                            "id_facture"=>$this->id_facture,
                                                                                            "produit"=>"produitTU2",
                                                                                            "quantite"=>2,
                                                                                            )));
		
		
        $this->id_facturation=ATF::facturation()->decryptId(ATF::facturation()->i(array(
                                                                                        "id_affaire"=>$this->id_affaire,
                                                                                        "id_societe"=>$this->id_societe,
                                                                                        "montant"=>100,
                                                                                        "frais_de_gestion"=>10,
                                                                                        "assurance"=>10,
                                                                                        "date_periode_debut"=>date("Y-m-d",strtotime(date("Y-m-01")." 1 month")),
                                                                                        "type"=>"contrat",
                                                                                        "date_periode_fin"=>date("Y-m-d",strtotime(date("Y-m-01")." + 2 month")),
                                                                                        "id_facture"=>$this->id_facture,
                                                                                        "envoye"=>"oui"
                                                                                        )
                                                                                    )
                                                                                );
        
		 $this->id_facturation2=ATF::facturation()->decryptId(ATF::facturation()->i(array(
                                                                                        "id_affaire"=>$this->id_affaire,
                                                                                        "id_societe"=>$this->id_societe,
                                                                                        "montant"=>100,
                                                                                        "frais_de_gestion"=>10,
                                                                                        "assurance"=>10,
                                                                                        "date_periode_debut"=>date("Y-m-d",strtotime(date("Y-m-01")." +2 month")),
                                                                                        "type"=>"contrat",
                                                                                        "date_periode_fin"=>date("Y-m-d",strtotime(date("Y-m-01")." + 3 month")),
                                                                                        "id_facture"=>NULL,
                                                                                        "envoye"=>"oui"
                                                                                        )
                                                                                    )
                                                                                );
		
        if ($relance) {
            $this->relance1 = array(
                "id_societe"=>$this->id_societe
                ,"id_contact"=>$this->id_contact
                ,"type"=>"premiere"
            );
            $this->relance1['id'] = ATF::relance()->i($this->relance1);
            $this->relance2 = array(
                "id_societe"=>$this->id_societe
                ,"id_contact"=>$this->id_contact
                ,"type"=>"seconde"
            );
            $this->relance2['id'] = ATF::relance()->i($this->relance2);
            $this->relance3 = array(
                "id_societe"=>$this->id_societe
                ,"id_contact"=>$this->id_contact
                ,"type"=>"mise_en_demeure"
            );
            $this->relance3['id'] = ATF::relance()->i($this->relance3);
            
            ATF::relance_facture()->i(array("id_facture"=>$this->id_facture,"id_relance"=>$this->relance1['id']));
            ATF::relance_facture()->i(array("id_facture"=>$this->id_facture,"id_relance"=>$this->relance2['id']));
            ATF::relance_facture()->i(array("id_facture"=>$this->id_facture,"id_relance"=>$this->relance3['id']));
        }else{
        	$this->id_facture2=ATF::facture()->decryptId(ATF::facture()->i(array(
                                                                        "ref"=>"refTU2",
                                                                        "type_facture"=>"facture",
                                                                        "id_societe"=>$this->id_societe,
                                                                        "prix"=>120,
                                                                        "date"=>date("Y-m-d",strtotime(date("Y-m-01")." 3 month")),
                                                                        "date_previsionnelle"=>date("Y-m-d",strtotime(date("Y-m-01")." 4 month")),
                                                                        "date_paiement"=>date("Y-m-d",strtotime(date("Y-m-01")." + 5 month")),
                                                                        "date_relance"=>date("Y-m-d",strtotime(date("Y-m-01")." + 6 month")),
                                                                        "date_periode_debut"=>date("Y-m-01"),
                                                                        "date_periode_fin"=>date("Y-m-d",strtotime(date("Y-m-01")." + 1 month")),
                                                                        "tva"=>"1.196",
                                                                        "id_user"=>$this->id_user,
                                                                        "id_commande"=>$this->id_commande,
                                                                        "id_affaire"=>$this->id_affaire,
                                                                        "id_demande_refi"=>$this->id_demande_refi
                                                                        )));

       	 $this->id_facture_ligne3=ATF::facture_ligne()->decryptId(ATF::facture_ligne()->i(array(
                                                                                            "id_facture"=>$this->id_facture2,
                                                                                            "produit"=>"produitTU",
                                                                                            "quantite"=>1,
                                                                                            )));
        
       	 $this->id_facture_ligne4=ATF::facture_ligne()->decryptId(ATF::facture_ligne()->i(array(
                                                                                            "id_facture"=>$this->id_facture2,
                                                                                            "produit"=>"produitTU2",
                                                                                            "quantite"=>2,
                                                                                            )));		
		
        }
    }


  
    //@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  
    public function test_delete(){
        $this->insertFacture();
        $delete_commande=array("id"=>array(0=>$this->id_facture));
        $this->obj->delete($delete_commande);

        $this->assertNull(ATF::facturation()->select($this->id_facture),"La facture doit être supprimée");
        $facturation=ATF::facturation()->select($this->id_facturation);
        $this->assertEquals("non",$facturation['envoye'],"La facturation ne doit pas être envoyée");
        $this->assertNull($facturation['id_facture'],"La facture ne doit pas être envoyée");

        $id_affaire=ATF::affaire()->decryptId(ATF::affaire()->i(array("ref"=>"refTuVente","id_societe"=>$this->id_societe,"affaire"=>"AffaireTuVente","nature"=>"vente")));

        $id_commande=ATF::commande()->decryptId(ATF::commande()->i(array("ref"=>"refTUVente","id_societe"=>$this->id_societe,"id_user"=>$this->id_user,"tva"=>"1,196","id_affaire"=>$id_affaire,"etat"=>"mis_loyer")));

        $id_facture=ATF::facture()->decryptId(ATF::facture()->i(array(
                                                                        "ref"=>"refTUVente",
                                                                        "id_societe"=>$this->id_societe,
                                                                        "date"=>date("Y-m-d",strtotime(date("Y-m-01")." 1 month")),
                                                                        "prix"=>2000,
                                                                        "tva"=>"1.196",
                                                                        "id_affaire"=>$id_affaire,
                                                                        )));

        $delete_commande=array("id"=>array(0=>$id_facture));
        $this->obj->delete($delete_commande);

        $this->assertNull(ATF::facturation()->select($id_facture),"La facture doit être supprimée");
        $this->assertEquals("non_loyer",ATF::commande()->select($id_commande,"etat"),"La facturation ne doit pas être envoyée");
    }
    
    //@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> 
    public function test_insert(){
        $this->insertFacture();
        
        $produits=array(
                        0=>array(
                                "facture_ligne__dot__produit"=>"ZywallVis 5 - dispositif de sécurité",
                                "facture_ligne__dot__quantite"=>"3",
                                "facture_ligne__dot__ref"=>"ZYX-FW",
                                "facture_ligne__dot__id_fournisseur"=>"DJP SERVICE",
                                "facture_ligne__dot__id_fournisseur_fk"=>"1583",
                                "facture_ligne__dot__prix_achat"=>"1.00",
                                "facture_ligne__dot__id_produit"=>"ZywallVis 5 - dispositif de sécurité",
                                "facture_ligne__dot__id_produit_fk"=>"1175",
                                "facture_ligne__dot__id_facture_ligne"=>"aaaaaaa"
                                )
        );
        
        $facture["values_facture"]["produits"]=json_encode($produits);

        $produits_repris=array(
                        0=>array(
                        "facture_ligne__dot__produit"=>"ZywallVis 51 - dispositif de sécurité",
                        "facture_ligne__dot__quantite"=>"1",
                        "facture_ligne__dot__ref"=>"ZYX-FW1",
                        "facture_ligne__dot__id_fournisseur"=>"DJP SERVICE1",
                        "facture_ligne__dot__id_fournisseur_fk"=>"1583",
                        "facture_ligne__dot__prix_achat"=>"1.00",
                        "facture_ligne__dot__id_produit"=>"ZywallVis 51 - dispositif de sécurité",
                        "facture_ligne__dot__id_produit_fk"=>"1175",
                        "facture_ligne__dot__id_facture_ligne"=>"bbbbbbbbb"
                        )
        );
        $facture["values_facture"]["produits_repris"]=json_encode($produits_repris);

        $produits_non_visible=array(
                        0=>array(
                        "facture_ligne__dot__produit"=>"ZywallVis 52 - dispositif de sécurité",
                        "facture_ligne__dot__quantite"=>"2",
                        "facture_ligne__dot__ref"=>"ZYX-FW2",
                        "facture_ligne__dot__id_fournisseur"=>"DJP SERVICE2",
                        "facture_ligne__dot__id_fournisseur_fk"=>"1583",
                        "facture_ligne__dot__prix_achat"=>"1.00",
                        "facture_ligne__dot__id_produit"=>"ZywallVis 52 - dispositif de sécurité",
                        "facture_ligne__dot__id_produit_fk"=>"1175",
                        "facture_ligne__dot__id_facture_ligne"=>"cccccccc"
                        )
        );
        $facture["values_facture"]["produits_non_visible"]=json_encode($produits_non_visible);

        unset($facture["preview"]);
        $facture["panel_courriel-checkbox"]="on";
        $facture["facture"]["type_facture"]="facture";
        $facture["facture"]["email"]="tu@absystech.fr";
        $facture["facture"]["emailCopie"]="tucopie@absystech.fr";
        $facture["facture"]["emailTexte"]="texte tu mail";
        $facture["facture"]["id_commande"]=$this->id_commande;
        $facture["facture"]["id_societe"]=$this->id_societe;
        $facture["facture"]["date"]=date("Y-m-01");
        $facture["facture"]["prix"]="100";
        $refresh = array();

        //Déjà facturée
        ATF::facturation()->u(array("id_facturation"=>$this->id_facturation,"id_facture"=>$this->id_facture));
        try {
             $id_facture = $this->obj->insert($facture);
        } catch (errorATF $e) {
            $error = $e->getCode();
        }
        $this->assertEquals(349,$error,'Il existe déjà  une facturation pour cette période');
        ATF::facturation()->u(array("id_facturation"=>$this->id_facturation,"id_facture"=>NULL));

        //Sans prix
        unset($facture["facture"]["prix"]);
        try {
             $id_facture = $this->obj->insert($facture);
        } catch (errorATF $e) {
            $error = $e->getCode();
        }
        $this->assertEquals(351,$error,'Il faut un prix pour la facture');
        $facture["facture"]["prix"]="100";

        $id_facture = $this->obj->insert($facture,$this->s,NULL,$refresh);
        
        
        //Avec date 
		$date_debut = ATF::facturation()->select($this->id_facturation , "date_periode_debut");
		$date_debut = explode("-", $date_debut);
		$date_fin = ATF::facturation()->select($this->id_facturation , "date_periode_fin");
		$date_fin = explode("-", $date_fin);
		
		
		$facture["facture"]["date_periode_debut"]= $date_debut[2]."-".$date_debut[1]."-".$date_debut[0];
        $facture["facture"]["date_periode_fin"]=$date_fin[2]."-".$date_fin[1]."-".$date_fin[0];
		try {
             $id_facture = $this->obj->insert($facture);
        } catch (errorATF $e) {
            $error = $e->getCode();
        }
        $this->assertEquals(349,$error,'Il existe déjà  une facturation pour cette période');
		
        $this->assertEquals(array(
                                    "id_facture"=>$id_facture,
                                    "type_facture"=>"facture",
                                    "ref"=>"refTu-1",
                                    "id_societe"=>$this->id_societe,
                                    "prix"=>"100.00",
                                    "etat"=>"impayee",
                                    "date"=>date("Y-m-01"),
                                    "date_previsionnelle"=>NULL,
                                    "date_paiement"=>NULL,
                                    "date_relance"=>date('Y-m-d',strtotime(date("Y-m-d")." + 1 month")),
                                    "date_periode_debut"=>date('Y-m-d',strtotime(date("Y-m-01")." + 1 month")),
                                    "date_periode_fin"=>date('Y-m-d',strtotime(date("Y-m-01")." + 2 month")),
                                    "tva"=>"1.2",
                                    "id_user"=>$this->id_user,
                                    "id_commande"=>$this->id_commande,
                                    "id_affaire"=>$this->id_affaire,
                                    "mode_paiement"=>NULL,
                                    "id_demande_refi"=>NULL,
                                    "id_refinanceur"=>NULL,
                                    "envoye_mail"=>"tu@absystech.fr",
                                    "rejet"=>"non_rejet",                               
                                    "commentaire"=> NULL,
                                    "type_libre"=>NULL ,
                                    "redevance"=> "oui",
                                    'date_rejet' => NULL,
                                    'date_regularisation' => NULL                      
                                    )
                            ,$this->obj->select($id_facture)
                            ,"La facture s'est mal inséré");

        ATF::facture_ligne()->q->reset()->addCondition("id_facture",$id_facture);
        $facture_ligne=ATF::facture_ligne()->sa();
        $this->assertEquals(array(
                                0=>array(
                                        "id_facture_ligne"=>$facture_ligne[0]["id_facture_ligne"],
                                        "id_facture"=>$id_facture,
                                        "id_produit"=>1175,
                                        "ref"=>"ZYX-FW",
                                        "produit"=>"ZywallVis 5 - dispositif de sécurité",
                                        "quantite"=>3,
                                        "id_fournisseur"=>1583,
                                        "prix_achat"=>"1.00",
                                        "serial"=>NULL,
                                        "code"=>NULL,
                                        "id_affaire_provenance"=>NULL,
                                        "visible"=>"oui",
                                        "afficher"=>"oui"
                                        ),
                                1=>array(
                                        "id_facture_ligne"=>$facture_ligne[1]["id_facture_ligne"],
                                        "id_facture"=>$id_facture,
                                        "id_produit"=>1175,
                                        "ref"=>"ZYX-FW1",
                                        "produit"=>"ZywallVis 51 - dispositif de sécurité",
                                        "quantite"=>1,
                                        "id_fournisseur"=>1583,
                                        "prix_achat"=>"1.00",
                                        "serial"=>NULL,
                                        "code"=>NULL,
                                        "id_affaire_provenance"=>NULL,
                                        "visible"=>"oui",
                                        "afficher"=>"oui"
                                        ),
                                2=>array(
                                        "id_facture_ligne"=>$facture_ligne[2]["id_facture_ligne"],
                                        "id_facture"=>$id_facture,
                                        "id_produit"=>1175,
                                        "ref"=>"ZYX-FW2",
                                        "produit"=>"ZywallVis 52 - dispositif de sécurité",
                                        "quantite"=>2,
                                        "id_fournisseur"=>1583,
                                        "prix_achat"=>"1.00",
                                        "serial"=>NULL,
                                        "code"=>NULL,
                                        "id_affaire_provenance"=>NULL,
                                        "visible"=>"non",
                                        "afficher"=>"oui"
                                        )
                            )
                            ,$facture_ligne
                            ,"Les facture_ligne s'est mal inséré");

        $this->assertEquals("facture"
                            ,ATF::affaire()->select($this->id_affaire,"etat")
                            ,"l'affaire est bien passé en facturé");
            

        $this->assertEquals("oui"
                            ,ATF::facturation()->select($this->id_facturation,"envoye")
                            ,"la facturation est bien envoyée");

        $this->assertEquals($id_facture
                            ,ATF::facturation()->select($this->id_facturation,"id_facture")
                            ,"la facturation est bien facturée");
		
		
		//Facture avec date 
		$date_debut = ATF::facturation()->select($this->id_facturation2 , "date_periode_debut");
		$date_debut = explode("-", $date_debut);
		$date_fin = ATF::facturation()->select($this->id_facturation2 , "date_periode_fin");
		$date_fin = explode("-", $date_fin);
		
		
		$facture["facture"]["date_periode_debut"]= $date_debut[2]."-".$date_debut[1]."-".$date_debut[0];
        $facture["facture"]["date_periode_fin"]=$date_fin[2]."-".$date_fin[1]."-".$date_fin[0];
		
        $id_facture = $this->obj->insert($facture);
		
		$this->assertEquals($id_facture, ATF::facturation()->select($this->id_facturation2 , "id_facture"),'ERREUR la facture dans l echeancier incorrecte');
		
		
        //REFI
        ATF::facturation()->u(array("id_facturation"=>$this->id_facturation,"id_facture"=>NULL));
        $this->id_demande_refi=ATF::demande_refi()->decryptId(ATF::demande_refi()->i(array(
                                                                                            "date"=>date("Y-m-d"),
                                                                                            "id_contact"=>$this->id_contact,
                                                                                            "id_refinanceur"=>1,
                                                                                            "id_affaire"=>$this->id_affaire,
                                                                                            "id_societe"=>$this->id_societe,
                                                                                            "description"=>"Tu description",
                                                                                            "loyer_actualise"=>50,
                                                                                            "etat"=>"valide"
                                                                                            )
                                                                                        )
                                                                                    );
        $facture["facture"]["type_facture"]="refi";
        $facture["preview"]=true;
        unset($facture["panel_courriel-checkbox"]);

        try {
             $id_facture = $this->obj->insert($facture);
        } catch (errorATF $e) {
            $error = $e->getCode();
        }
        $this->assertEquals(347,$error,'Il n y a pas de demande de refinancement valide pour cette affaire');

        $facture["facture"]["id_demande_refi"]=$this->id_demande_refi;

        $id_facture = $this->obj->insert($facture,$this->s,NULL,$refresh);
        
        $this->assertEquals(array(
                                    "id_facture"=>$this->obj->decryptId($id_facture),
                                    "type_facture"=>"refi",
                                    "ref"=>"refTu-1-RE",
                                    "id_societe"=>$this->id_societe,
                                    "prix"=>"50.00",
                                    "etat"=>"impayee",
                                    "date"=>date("Y-m-01"),
                                    "date_previsionnelle"=>NULL,
                                    "date_paiement"=>NULL,
                                    "date_relance"=>date('Y-m-d',strtotime(date("Y-m-d")." + 1 month")),
                                    "date_periode_debut"=>NULL,
                                    "date_periode_fin"=>NULL,
                                    "tva"=>"1.200",
                                    "id_user"=>$this->id_user,
                                    "id_commande"=>$this->id_commande,
                                    "id_affaire"=>$this->id_affaire,
                                    "mode_paiement"=>NULL,
                                    "id_demande_refi"=>$this->id_demande_refi,
                                    "id_refinanceur"=>"1",
                                    "envoye_mail"=>NULL,
                                    "rejet"=>"non_rejet",                               
                                    "commentaire"=> NULL,
                                    "type_libre"=>NULL  ,
                                    "redevance"=> "oui" ,
                                    'date_rejet' => NULL,
                                    'date_regularisation' => NULL                                     
                                    )
                            ,$this->obj->select($id_facture)
                            ,"La facture REFI s'est mal inséré");

        //LIBRE
        ATF::affaire()->u(array("id_affaire"=>$this->id_affaire,"nature"=>"vente"));
        $facture["facture"]["type_facture"]="libre";        
        $facture["facture"]["prix_libre"]="150.00";
        $facture["facture"]["date_periode_debut_libre"]=date('Y-m-d',strtotime(date("Y-m-5")." + 1 month"));
        $facture["facture"]["date_periode_fin_libre"]=date('Y-m-d',strtotime(date("Y-m-5")." + 2 month"));
        
        try {
             $id_facture = $this->obj->insert($facture,$this->s,NULL,$refresh);
        } catch (errorATF $e) {
            $error = $e->getCode();
        }
        $this->assertEquals(351,$error,'Il n y a pas de type libre pour cette facture libre');
        
        $facture["facture"]["type_libre"]="retard";
        $id_facture = $this->obj->insert($facture,$this->s,NULL,$refresh);

        $this->assertEquals("vente"
                            ,ATF::commande()->select($this->id_commande,"etat")
                            ,"la commande est passée en vente");


        $this->assertEquals(array(
                                    "id_facture"=>$this->obj->decryptId($id_facture),
                                    "type_facture"=>"libre",
                                    "ref"=>"refTu-1-LI",
                                    "id_societe"=>$this->id_societe,
                                    "prix"=>"150.00",
                                    "etat"=>"impayee",
                                    "date"=>date("Y-m-01"),
                                    "date_previsionnelle"=>NULL,
                                    "date_paiement"=>NULL,
                                    "date_relance"=>date('Y-m-d',strtotime(date("Y-m-d")." + 1 month")),
                                    "date_periode_debut"=>date('Y-m-d',strtotime(date("Y-m-05")." + 1 month")),
                                    "date_periode_fin"=>date('Y-m-d',strtotime(date("Y-m-05")." + 2 month")),
                                    "tva"=>"1.000",
                                    "id_user"=>$this->id_user,
                                    "id_commande"=>$this->id_commande,
                                    "id_affaire"=>$this->id_affaire,
                                    "mode_paiement"=>NULL,
                                    "id_demande_refi"=>NULL,
                                    "id_refinanceur"=>NULL,
                                    "envoye_mail"=>NULL,
                                    "rejet"=>"non_rejet",                               
                                    "commentaire"=>NULL,
                                    "type_libre"=>"retard"   ,
                                    "redevance"=> "oui"  ,
                                    'date_rejet' => NULL,
                                    'date_regularisation' => NULL                                  
                                    )
                            ,$this->obj->select($id_facture)
                            ,"La facture LIBRE s'est mal inséré");



        $facture["facture"]["type_facture"]="midas";
        $facture["preview"]=true;
        $facture["facture"]["prix_midas"] = 500;
        $facture["facture"]["periode_midas"] = "La periode MIDAS";

        $id_facture = $this->obj->insert($facture);

        $this->assertEquals(500,ATF::facture()->select($id_facture,"prix"),"la facture Midas c'est mal inseré?");
        $this->assertEquals("La periode MIDAS",ATF::facture()->select($id_facture,"commentaire"),"la facture Midas c'est mal inseré 2?");
       


    }


    //@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> 
    function test_getLignes(){
        $this->insertFacture();
        $facture=new facture_cleodis($this->id_facture);
        $lignes1=$facture->getLignes();
        ATF::facture_ligne()->q->reset()->where("id_facture",$this->id_facture);
        $lignes2= ATF::facture_ligne()->sa();

        $this->assertEquals($lignes2,$lignes1,"getLignes ne renvoi pas les bonnes lignes");
    }
    
    function test_can_delete(){
        $this->insertFacture();
        
        $this->assertTrue($this->obj->can_delete($this->id_facture),"On doit pouvoir supprimer une facture impayée");
        
        ATF::facture()->u(array("id_facture"=>$this->id_facture,"etat"=>"payee"));
        try {
             $this->obj->can_delete($this->id_facture);
        } catch (errorATF $e) {
            $error = $e->getCode();
        }
        $this->assertEquals(879,$error,"On ne doit pas pouvoir supprimer une facture payée");

    }

    function test_can_update(){
        $this->insertFacture();
        
        try {
             $this->obj->can_update($this->id_facture);
        } catch (errorATF $e) {
            $error = $e->getCode();
        }
        $this->assertEquals(878,$error,"On ne doit pas pouvoir modifier une facture payée");
        
    }


    //@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
    //@author Morgan FLEURQUIN <mfleurquin@absystech.fr>  
    function test_updateDate(){
        $this->insertFacture();

        $mod["id_facture"]=$this->id_facture;
        $mod["key"]="date_paiement";
        $mod["value"]=date("Y-m-d");
        
        $this->obj->updateDate($mod);
        $facture=ATF::facture()->select($this->id_facture);
        $this->assertEquals(date("Y-m-d"),$facture["date_paiement"],"La date_paiement ne se modifie pas");
        $this->assertEquals(array(0=>(array("msg"=>loc::mt(ATF::$usr->trans("notice_update_success_date"),array("record"=>$this->obj->nom($this->id_facture),"date"=>"date_paiement")),
                                  "title"=>ATF::$usr->trans("notice_success_title"),
                                  "timer"=>""))),
                            ATF::$msg->getNotices(),"1 La notice de updateDate ne se fait pas");

        $mod["value"]=NULL;
        $this->obj->updateDate($mod);
        $facture=ATF::facture()->select($this->id_facture);
        $this->assertNull($facture["date_paiement"],"La date_paiement ne se supprime pas");
        $this->assertEquals(array(0=>(array("msg"=>loc::mt(ATF::$usr->trans("notice_update_success_date"),array("record"=>$this->obj->nom($this->id_facture),"date"=>"date_paiement")),
                                  "title"=>ATF::$usr->trans("notice_success_title"),
                                  "timer"=>""))),
                            ATF::$msg->getNotices(),"2 La notice de updateDate ne se fait pas");


        $mod["id_facture"]=$this->id_facture;
        $mod["key"]="date_paiement";
        $mod["value"]=date("Y-m-d");
        
        $this->obj->updateDate($mod);
        $facture=ATF::facture()->select($this->id_facture);
        $this->assertEquals(date("Y-m-d"),$facture["date_paiement"],"La date_paiement ne se modifie pas");
        $this->assertEquals(array(0=>(array("msg"=>loc::mt(ATF::$usr->trans("notice_update_success_date"),array("record"=>$this->obj->nom($this->id_facture),"date"=>"date_paiement")),
                                  "title"=>ATF::$usr->trans("notice_success_title"),
                                  "timer"=>""))),
                            ATF::$msg->getNotices(),"1 La notice de updateDate ne se fait pas");


        $mod["id_facture"]=$this->id_facture;
        $mod["key"]="date_rejet";
        $mod["value"]=date("Y-m-d");
        
        $this->obj->updateDate($mod);
        $facture=ATF::facture()->select($this->id_facture);
        $this->assertEquals(date("Y-m-d"),$facture["date_rejet"],"La date_rejet ne se modifie pas");
        $this->assertEquals(array(0=>(array("msg"=>loc::mt(ATF::$usr->trans("notice_update_success_date"),array("record"=>$this->obj->nom($this->id_facture),"date"=>"date_rejet")),
                                  "title"=>ATF::$usr->trans("notice_success_title"),
                                  "timer"=>""))),
                            ATF::$msg->getNotices(),"3 La notice de updateDate ne se fait pas");



        $mod["id_facture"]=$this->id_facture;
        $mod["key"]="date_rejet";
        $mod["value"]=date("Y-m-20");
        
        try{
             $this->obj->updateDate($mod);
        } catch (errorATF $e) {
            $error = $e->getMessage();
        }
        $this->assertEquals("Impossible de modifier une date de rejet car elle est déja renseignée",$error,"On ne doit pas pouvoir mettre a jour une date rejet deja renseignée");     
       
        


    }

    //@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  
    function test_updateEnumRejet(){
        $this->insertFacture();

        $mod["id_facture"]=$this->id_facture;
        $mod["key"]="rejet";
        $mod["value"]="contestation_debiteur";
        
        $this->obj->updateEnumRejet($mod);
        $facture=ATF::facture()->select($this->id_facture);
        $this->assertEquals("contestation_debiteur",$facture["rejet"],"Le rejet ne se modifie pas");
        $this->assertEquals(array(0=>(array("msg"=>loc::mt(ATF::$usr->trans("notice_update_success_date"),array("record"=>$this->obj->nom($this->id_facture),"date"=>"rejet")),
                                  "title"=>ATF::$usr->trans("notice_success_title"),
                                  "timer"=>""))),
                            ATF::$msg->getNotices(),"1 La notice de updateEnumRejet ne se fait pas");

        $this->obj->u(array("id_facture"=>$this->id_facture,"etat"=>"payee"));

        $this->obj->updateDate($mod);
        try {
        $this->obj->updateEnumRejet($mod);
        } catch (errorATF $e) {
            $error = $e->getCode();
        }
        $this->assertEquals(877,$error,"On ne doit pas pouvoir updateEnumRejet une facture payée");

    }


	function test_updateEnumRejet2(){
        $this->insertFacture();

        $mod["id_facture"]=$this->id_facture2;
        $mod["key"]="rejet";
        $mod["value"]="contestation_debiteur";
        ATF::commande()->u(array("id_commande"=>$this->id_commande , "etat"=>"prolongation"));
        $this->obj->updateEnumRejet($mod);       
        $this->assertEquals("prolongation_contentieux" , ATF::commande()->select($this->id_commande, "etat"), "UpdateEnum 2 - 1 L'etat n'a pas changé");
		
		ATF::$msg->getNotices();
	}
	
	function test_updateEnumRejet21(){	
		$this->insertFacture();				
		$mod["id_facture"]=$this->id_facture2;
        $mod["key"]="rejet";
        $mod["value"]="non_rejet";
		
        ATF::commande()->u(array("id_commande"=>$this->id_commande , "etat"=>"mis_loyer_contentieux"));
		$this->obj->updateEnumRejet($mod);       
        $this->assertEquals("mis_loyer" , ATF::commande()->select($this->id_commande, "etat"), "UpdateEnum 2 - 1 L'etat n'a pas changé");
	}
	function test_updateEnumRejet22(){
		$this->insertFacture();				
		$mod["id_facture"]=$this->id_facture2;
        $mod["key"]="rejet";
        $mod["value"]="non_rejet";	
		
		ATF::commande()->u(array("id_commande"=>$this->id_commande , "etat"=>"restitution_contentieux"));
		$this->obj->updateEnumRejet($mod);       
        $this->assertEquals("restitution" , ATF::commande()->select($this->id_commande, "etat"), "UpdateEnum 2 - 2 L'etat n'a pas changé");
	}
	function test_updateEnumRejet23(){
		$this->insertFacture();				
		$mod["id_facture"]=$this->id_facture2;
        $mod["key"]="rejet";
        $mod["value"]="non_rejet";
			
		ATF::commande()->u(array("id_commande"=>$this->id_commande , "etat"=>"prolongation_contentieux"));
		$this->obj->updateEnumRejet($mod);       
        $this->assertEquals("prolongation" , ATF::commande()->select($this->id_commande, "etat"), "UpdateEnum 2 - 3 L'etat n'a pas changé");		
    }

    function test_updateDate3(){
        $this->insertFacture();
        $mod["id_facture"]=$this->id_facture2;
        $mod["key"]="date_regularisation";
        $mod["value"]="2014-01-01";
        ATF::commande()->u(array("id_commande"=>$this->id_commande , "etat"=>"prolongation_contentieux"));
        $this->obj->updateDate($mod);       
        $this->assertEquals("prolongation" , ATF::commande()->select($this->id_commande, "etat"), "UpdateDate3 3 - 1 L'etat n'a pas changé");
        
        ATF::$msg->getNotices();
    }

    //@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> 
    function testIs_past(){
        $this->assertTrue($this->obj->is_past(date("Y-m-d",strtotime(date('Y-m-d')."-1 day"))),"Erreur sur is_past quand la date n'est pas passée");
        $this->assertFalse($this->obj->is_past(date("Y-m-d",strtotime(date('Y-m-d')."+1 day"))),"Erreur sur is_past quand la date est passée");
    }

    
    // @author Nicolas BERTEMONT <nbertemont@absystech.fr> 
    public function testAjoutTitre(){
        $lol = new objet_excel();
        $sheets=array("refi"=>$lol);
        
        $this->obj->ajoutTitre($sheets);
        
        $this->assertEquals("Type",$sheets['refi']->A1,"La valeur de la cellule A1 est incorrecte");
        $this->assertTrue($sheets['refi']->sheet->size,"Autosize n'a pas été fait");
    }
    
    // @author Nicolas BERTEMONT <nbertemont@absystech.fr> 
    public function testAjoutDonnees(){

        //pour refi
        $obj_refi = new objet_excel();
        $sheets=array("auto"=>$obj_refi);
        
        $id_societe=ATF::societe()->i(array("societe"=>"TU"));
        $id_affaire=ATF::affaire()->i(array("ref"=>"REFTu","id_societe"=>$id_societe,"affaire"=>"ATU")); 
        //$id_devis=ATF::devis()->i(array("ref"=>"ref test", "type_contrat"=>"presta","id_affaire"=>$id_affaire ,"id_societe"=>$id_societe,"id_user"=>$this->id_user,"devis"=>"devis test","id_contact"=>5753 ,"tva"=>19.6,"validite"=>"2016-01-01","prix"=>100));
              
        $infos=array(0=>array('facture.id_affaire_fk'=>$id_affaire,'facture.type_facture'=>'refi','facture.prix'=>'100'));
        
        $this->obj->ajoutDonnees($sheets,$infos);

        $this->assertEquals("G",$sheets['auto']->A2,"La cellule A2 est mal remplie");       
        $this->assertEquals("01011970",$sheets['auto']->B2,"La cellule B2 est mal remplie");    
        $this->assertEquals("VEN",$sheets['auto']->C2,"La cellule C2 est mal remplie"); 
        $this->assertEquals("411000",$sheets['auto']->D2,"La cellule D2 est mal remplie");  
        //$this->assertNull($sheets['auto']->E2,"La cellule E2 est mal remplie"); 
        $this->assertEquals("D",$sheets['auto']->F2,"La cellule F2 est mal remplie");   
        $this->assertEquals("0",$sheets['auto']->G2,"La cellule G2 est mal remplies 1");    
        $this->assertEquals("FREFTu-/",$sheets['auto']->H2,"La cellule H2 est mal remplie");    
        $this->assertEquals("F70010001",$sheets['auto']->I2,"La cellule I2 est mal remplie");   
        $this->assertNull($sheets['auto']->J2,"La cellule J2 est mal remplie"); 

        $this->assertEquals("G",$sheets['auto']->A3,"La cellule A3 est mal remplie");       
        $this->assertEquals("01011970",$sheets['auto']->B3,"La cellule B3 est mal remplie");    
        $this->assertEquals("VEN",$sheets['auto']->C3,"La cellule C3 est mal remplie"); 
        $this->assertEquals("707110",$sheets['auto']->D3,"La cellule D3 est mal remplie");  
        $this->assertNull($sheets['auto']->E3,"La cellule E3 est mal remplie"); 
        $this->assertEquals("C",$sheets['auto']->F3,"La cellule F3 est mal remplie");   
        $this->assertEquals("100",$sheets['auto']->G3,"La cellule G3 est mal remplies 1");  
        $this->assertEquals("FREFTu-/",$sheets['auto']->H3,"La cellule H3 est mal remplie");    
        $this->assertEquals("F70010001",$sheets['auto']->I3,"La cellule I3 est mal remplie");   
        $this->assertNull($sheets['auto']->J3,"La cellule J3 est mal remplie"); 

        $this->assertEquals("A1",$sheets['auto']->A4,"La cellule A4 est mal remplie");      
        $this->assertEquals("01011970",$sheets['auto']->B4,"La cellule B4 est mal remplie");    
        $this->assertEquals("VEN",$sheets['auto']->C4,"La cellule C4 est mal remplie"); 
        $this->assertEquals("707110",$sheets['auto']->D4,"La cellule D4 est mal remplie");  
        $this->assertNull($sheets['auto']->E4,"La cellule E4 est mal remplie"); 
        $this->assertEquals("C",$sheets['auto']->F4,"La cellule F4 est mal remplie");   
        $this->assertEquals("100",$sheets['auto']->G4,"La cellule G4 est mal remplies");    
        $this->assertEquals("FREFTu-/",$sheets['auto']->H4,"La cellule H4 est mal remplie");    
        $this->assertEquals("F70010001",$sheets['auto']->I4,"La cellule I4 est mal remplie");   
        $this->assertEquals("20REFTu00",$sheets['auto']->J4,"La cellule J4 est mal remplie");   

        $this->assertEquals("G",$sheets['auto']->A5,"La cellule A5 est mal remplie");       
        $this->assertEquals("01011970",$sheets['auto']->B5,"La cellule B5 est mal remplie");    
        $this->assertEquals("VEN",$sheets['auto']->C5,"La cellule C5 est mal remplie"); 
        $this->assertEquals("445710",$sheets['auto']->D5,"La cellule D5 est mal remplie 1");    
        $this->assertNull($sheets['auto']->E5,"La cellule E5 est mal remplie"); 
        $this->assertEquals("C",$sheets['auto']->F5,"La cellule F5 est mal remplie");   
        $this->assertEquals("100",$sheets['auto']->G5,"La cellule G5 est mal remplies");    
        $this->assertEquals("FREFTu-/",$sheets['auto']->H5,"La cellule H5 est mal remplie");    
        $this->assertEquals("F70010001",$sheets['auto']->I5,"La cellule I5 est mal remplie");   
        $this->assertNull($sheets['auto']->J5,"La cellule J5 est mal remplie");

        //pour auto
        $obj_auto = new objet_excel();
        $sheets=array("auto"=>$obj_auto);      
        $infos=array(0=>array('facture.id_affaire_fk'=>$id_affaire,'facture.type_facture'=>'auto','facture.prix'=>'100'));
        
        //ATF::devis()->u(array("id_devis"=> $id_devis, "type_contrat"=>"lld"));
       
        $this->obj->ajoutDonnees($sheets,$infos);

        $this->assertEquals("G",$sheets['auto']->A2,"La cellule A2 est mal remplie auto");       
        $this->assertEquals("01011970",$sheets['auto']->B2,"La cellule B2 est mal remplie auto");    
        $this->assertEquals("VEN",$sheets['auto']->C2,"La cellule C2 est mal remplie auto"); 
        $this->assertEquals("411000",$sheets['auto']->D2,"La cellule D2 est mal remplie auto");  
        $this->assertNull($sheets['auto']->E2,"La cellule E2 est mal remplie auto"); 
        $this->assertEquals("D",$sheets['auto']->F2,"La cellule F2 est mal remplie auto");   
        $this->assertEquals("0",$sheets['auto']->G2,"La cellule G2 est mal remplies  auto");    
        $this->assertEquals("-",$sheets['auto']->H2,"La cellule H2 est mal remplie auto");   
        $this->assertEquals("F70010001",$sheets['auto']->I2,"La cellule I2 est mal remplie auto");   
        $this->assertNull($sheets['auto']->J2,"La cellule J2 est mal remplie auto"); 

        $this->assertEquals("G",$sheets['auto']->A3,"La cellule A3 est mal remplie auto");       
        $this->assertEquals("01011970",$sheets['auto']->B3,"La cellule B3 est mal remplie auto");    
        $this->assertEquals("VEN",$sheets['auto']->C3,"La cellule C3 est mal remplie auto"); 
        $this->assertEquals("706400",$sheets['auto']->D3,"La cellule D3 est mal remplie auto");  
        $this->assertNull($sheets['auto']->E3,"La cellule E3 est mal remplie auto"); 
        $this->assertEquals("C",$sheets['auto']->F3,"La cellule F3 est mal remplie auto");   
        $this->assertEquals("100",$sheets['auto']->G3,"La cellule G3 est mal remplies 2 auto");  
        $this->assertEquals("-",$sheets['auto']->H3,"La cellule H3 est mal remplie auto");   
        $this->assertEquals("F70010001",$sheets['auto']->I3,"La cellule I3 est mal remplie auto");   
        $this->assertNull($sheets['auto']->J3,"La cellule J3 est mal remplie auto"); 

        $this->assertEquals("A1",$sheets['auto']->A4,"La cellule A4 est mal remplie auto");      
        $this->assertEquals("01011970",$sheets['auto']->B4,"La cellule B4 est mal remplie auto");    
        $this->assertEquals("VEN",$sheets['auto']->C4,"La cellule C4 est mal remplie auto"); 
        $this->assertEquals("706400",$sheets['auto']->D4,"La cellule D4 est mal remplie auto");  
        $this->assertNull($sheets['auto']->E4,"La cellule E4 est mal remplie auto"); 
        $this->assertEquals("C",$sheets['auto']->F4,"La cellule F4 est mal remplie auto");   
        $this->assertEquals("100",$sheets['auto']->G4,"La cellule G4 est mal remplies auto");    
        $this->assertEquals("-",$sheets['auto']->H4,"La cellule H4 est mal remplie auto");   
        $this->assertEquals("F70010001",$sheets['auto']->I4,"La cellule I4 est mal remplie auto");   
        $this->assertEquals("20REFTu00",$sheets['auto']->J4,"La cellule J4 est mal remplie auto");   

        $this->assertEquals("G",$sheets['auto']->A5,"La cellule A5 est mal remplie auto");       
        $this->assertEquals("01011970",$sheets['auto']->B5,"La cellule B5 est mal remplie auto");    
        $this->assertEquals("VEN",$sheets['auto']->C5,"La cellule C5 est mal remplie auto"); 
        $this->assertEquals("445710",$sheets['auto']->D5,"La cellule D5 est mal remplie 2 auto");    
        $this->assertNull($sheets['auto']->E5,"La cellule E5 est mal remplie auto auto"); 
        $this->assertEquals("C",$sheets['auto']->F5,"La cellule F5 est mal remplie auto");   
        $this->assertEquals("100",$sheets['auto']->G5,"La cellule G5 est mal remplies auto");    
        $this->assertEquals("-",$sheets['auto']->H5,"La cellule H5 est mal remplie auto");   
        $this->assertEquals("F70010001",$sheets['auto']->I5,"La cellule I5 est mal remplie auto");   
        $this->assertNull($sheets['auto']->J5,"La cellule J5 est mal remplie auto"); 

    
        //pour CLEODIS
        $obj_auto = new objet_excel();
        $sheets=array("auto"=>$obj_auto);
        $id_refinanceur=ATF::refinanceur()->i(array("refinanceur"=>"CLEODIS","code"=>"123","code_refi"=>"123"));
        $id_contact=ATF::contact()->i(array("nom"=>"TU"));
        $id_demande_refi=ATF::demande_refi()->i(array("id_contact"=>$id_contact,"date"=>date("Y-m-d"),"id_refinanceur"=>$id_refinanceur,"id_affaire"=>$id_affaire,"id_societe"=>$id_societe,"description"=>"TU","etat"=>"valide"));
        $infos=array(0=>array('facture.id_affaire_fk'=>$id_affaire,'facture.type_facture'=>'facture','facture.prix'=>'100'));
        
        $this->obj->ajoutDonnees($sheets,$infos);
        $this->assertEquals("G",$sheets['auto']->A2,"La cellule A2 est mal remplie CLEODIS");       
        $this->assertEquals("01011970",$sheets['auto']->B2,"La cellule B2 est mal remplie CLEODIS");    
        $this->assertEquals("VEN",$sheets['auto']->C2,"La cellule C2 est mal remplie CLEODIS"); 
        $this->assertEquals("411000",$sheets['auto']->D2,"La cellule D2 est mal remplie CLEODIS");  
        $this->assertEquals("D",$sheets['auto']->F2,"La cellule F2 est mal remplie CLEODIS");   
        $this->assertEquals("0",$sheets['auto']->G2,"La cellule G2 est mal remplies 3 CLEODIS");    
        $this->assertEquals("-",$sheets['auto']->H2,"La cellule H2 est mal remplie CLEODIS");   
        $this->assertEquals("F70010001",$sheets['auto']->I2,"La cellule I2 est mal remplie CLEODIS");   
        $this->assertNull($sheets['auto']->J2,"La cellule J2 est mal remplie CLEODIS"); 

        $this->assertEquals("G",$sheets['auto']->A3,"La cellule A3 est mal remplie CLEODIS");       
        $this->assertEquals("01011970",$sheets['auto']->B3,"La cellule B3 est mal remplie CLEODIS");    
        $this->assertEquals("VEN",$sheets['auto']->C3,"La cellule C3 est mal remplie CLEODIS"); 
        $this->assertEquals("706200",$sheets['auto']->D3,"La cellule D3 est mal remplie CLEODIS");  
        $this->assertNull($sheets['auto']->E3,"La cellule E3 est mal remplie CLEODIS"); 
        $this->assertEquals("C",$sheets['auto']->F3,"La cellule F3 est mal remplie CLEODIS");   
        $this->assertEquals("100",$sheets['auto']->G3,"La cellule G3 est mal remplies 3 CLEODIS");  
        $this->assertEquals("-",$sheets['auto']->H3,"La cellule H3 est mal remplie CLEODIS");   
        $this->assertEquals("F70010001",$sheets['auto']->I3,"La cellule I3 est mal remplie CLEODIS");   
        $this->assertNull($sheets['auto']->J3,"La cellule J3 est mal remplie CLEODIS"); 

        $this->assertEquals("A1",$sheets['auto']->A4,"La cellule A4 est mal remplie CLEODIS");      
        $this->assertEquals("01011970",$sheets['auto']->B4,"La cellule B4 est mal remplie CLEODIS");    
        $this->assertEquals("VEN",$sheets['auto']->C4,"La cellule C4 est mal remplie CLEODIS"); 
        $this->assertEquals("706200",$sheets['auto']->D4,"La cellule D4 est mal remplie CLEODIS");  
        $this->assertNull($sheets['auto']->E4,"La cellule E4 est mal remplie CLEODIS"); 
        $this->assertEquals("C",$sheets['auto']->F4,"La cellule F4 est mal remplie CLEODIS");   
        $this->assertEquals("100",$sheets['auto']->G4,"La cellule G4 est mal remplies CLEODIS");    
        $this->assertEquals("-",$sheets['auto']->H4,"La cellule H4 est mal remplie CLEODIS");   
        $this->assertEquals("F70010001",$sheets['auto']->I4,"La cellule I4 est mal remplie CLEODIS");   
        $this->assertEquals("20REFTu00",$sheets['auto']->J4,"La cellule J4 est mal remplie CLEODIS");   

        $this->assertEquals("G",$sheets['auto']->A5,"La cellule A5 est mal remplie CLEODIS");       
        $this->assertEquals("01011970",$sheets['auto']->B5,"La cellule B5 est mal remplie CLEODIS");    
        $this->assertEquals("VEN",$sheets['auto']->C5,"La cellule C5 est mal remplie CLEODIS"); 
        $this->assertEquals("445712",$sheets['auto']->D5,"La cellule D5 est mal remplie 3 CLEODIS");    
        $this->assertNull($sheets['auto']->E5,"La cellule E5 est mal remplie CLEODIS"); 
        $this->assertEquals("C",$sheets['auto']->F5,"La cellule F5 est mal remplie CLEODIS");   
        $this->assertEquals("100",$sheets['auto']->G5,"La cellule G5 est mal remplies CLEODIS");    
        $this->assertEquals("-",$sheets['auto']->H5,"La cellule H5 est mal remplie CLEODIS");   
        $this->assertEquals("F70010001",$sheets['auto']->I5,"La cellule I5 est mal remplie CLEODIS");   
        $this->assertNull($sheets['auto']->J5,"La cellule J5 est mal remplie CLEODIS"); 

        //pour BMF
        ATF::refinanceur()->u(array("id_refinanceur"=>$id_refinanceur,"refinanceur"=>"BMF"));
        $this->obj->ajoutDonnees($sheets,$infos);

        $this->assertEquals("G",$sheets['auto']->A2,"La cellule A2 est mal remplie BMF");       
        $this->assertEquals("01011970",$sheets['auto']->B2,"La cellule B2 est mal remplie BMF");    
        $this->assertEquals("VEN",$sheets['auto']->C2,"La cellule C2 est mal remplie BMF"); 
        $this->assertEquals("467000",$sheets['auto']->D2,"La cellule D2 est mal remplie BMF");  
        $this->assertEquals("B",$sheets['auto']->E2,"La cellule E2 est mal remplie BMF");   
        $this->assertEquals("D",$sheets['auto']->F2,"La cellule F2 est mal remplie BMF");   
        $this->assertEquals("0",$sheets['auto']->G2,"La cellule G2 est mal remplies 4 BMF");    
        $this->assertEquals("-",$sheets['auto']->H2,"La cellule H2 est mal remplie BMF");   
        $this->assertEquals("F70010001",$sheets['auto']->I2,"La cellule I2 est mal remplie BMF");   
        $this->assertNull($sheets['auto']->J2,"La cellule J2 est mal remplie BMF"); 

        $this->assertEquals("G",$sheets['auto']->A3,"La cellule A3 est mal remplie BMF");       
        $this->assertEquals("01011970",$sheets['auto']->B3,"La cellule B3 est mal remplie BMF");    
        $this->assertEquals("VEN",$sheets['auto']->C3,"La cellule C3 est mal remplie BMF"); 
        $this->assertEquals("706500",$sheets['auto']->D3,"La cellule D3 est mal remplie BMF");  
        $this->assertNull($sheets['auto']->E3,"La cellule E3 est mal remplie BMF"); 
        $this->assertEquals("C",$sheets['auto']->F3,"La cellule F3 est mal remplie BMF");   
        $this->assertEquals("100",$sheets['auto']->G3,"La cellule G3 est mal remplies 4 BMF");  
        $this->assertEquals("-",$sheets['auto']->H3,"La cellule H3 est mal remplie BMF");   
        $this->assertEquals("F70010001",$sheets['auto']->I3,"La cellule I3 est mal remplie BMF");   
        $this->assertNull($sheets['auto']->J3,"La cellule J3 est mal remplie BMF"); 

        $this->assertEquals("A1",$sheets['auto']->A4,"La cellule A4 est mal remplie BMF");      
        $this->assertEquals("01011970",$sheets['auto']->B4,"La cellule B4 est mal remplie BMF");    
        $this->assertEquals("VEN",$sheets['auto']->C4,"La cellule C4 est mal remplie BMF"); 
        $this->assertEquals("706500",$sheets['auto']->D4,"La cellule D4 est mal remplie BMF");  
        $this->assertNull($sheets['auto']->E4,"La cellule E4 est mal remplie BMF"); 
        $this->assertEquals("C",$sheets['auto']->F4,"La cellule F4 est mal remplie BMF");   
        $this->assertEquals("100",$sheets['auto']->G4,"La cellule G4 est mal remplies BMF");    
        $this->assertEquals("-",$sheets['auto']->H4,"La cellule H4 est mal remplie BMF");   
        $this->assertEquals("F70010001",$sheets['auto']->I4,"La cellule I4 est mal remplie BMF");   
        $this->assertEquals("20REFTu00",$sheets['auto']->J4,"La cellule J4 est mal remplie BMF");   

        $this->assertEquals("G",$sheets['auto']->A5,"La cellule A5 est mal remplie BMF");       
        $this->assertEquals("01011970",$sheets['auto']->B5,"La cellule B5 est mal remplie BMF");    
        $this->assertEquals("VEN",$sheets['auto']->C5,"La cellule C5 est mal remplie BMF"); 
        $this->assertEquals("445712",$sheets['auto']->D5,"La cellule D5 est mal remplie 4 BMF");    
        $this->assertNull($sheets['auto']->E5,"La cellule E5 est mal remplie BMF"); 
        $this->assertEquals("C",$sheets['auto']->F5,"La cellule F5 est mal remplie BMF");   
        $this->assertEquals("100",$sheets['auto']->G5,"La cellule G5 est mal remplies BMF");    
        $this->assertEquals("-",$sheets['auto']->H5,"La cellule H5 est mal remplie BMF");   
        $this->assertEquals("F70010001",$sheets['auto']->I5,"La cellule I5 est mal remplie BMF");   
        $this->assertNull($sheets['auto']->J5,"La cellule J5 est mal remplie BMF"); 

        //pour CLEOFI
        ATF::refinanceur()->u(array("id_refinanceur"=>$id_refinanceur,"refinanceur"=>"CLEOFI"));
        $this->obj->ajoutDonnees($sheets,$infos);

        $this->assertEquals("G",$sheets['auto']->A2,"La cellule A2 est mal remplie CLEOFI");       
        $this->assertEquals("01011970",$sheets['auto']->B2,"La cellule B2 est mal remplie CLEOFI");    
        $this->assertEquals("VEN",$sheets['auto']->C2,"La cellule C2 est mal remplie CLEOFI"); 
        $this->assertEquals("411000",$sheets['auto']->D2,"La cellule D2 est mal remplie CLEOFI");  
        $this->assertNull($sheets['auto']->E2,"La cellule E2 est mal remplie CLEOFI"); 
        $this->assertEquals("D",$sheets['auto']->F2,"La cellule F2 est mal remplie CLEOFI");   
        $this->assertEquals("0",$sheets['auto']->G2,"La cellule G2 est mal remplies 5 CLEOFI");    
        $this->assertEquals("-",$sheets['auto']->H2,"La cellule H2 est mal remplie CLEOFI");   
        $this->assertEquals("F70010001",$sheets['auto']->I2,"La cellule I2 est mal remplie CLEOFI");   
        $this->assertNull($sheets['auto']->J2,"La cellule J2 est mal remplie CLEOFI"); 

        $this->assertEquals("G",$sheets['auto']->A3,"La cellule A3 est mal remplie CLEOFI");       
        $this->assertEquals("01011970",$sheets['auto']->B3,"La cellule B3 est mal remplie CLEOFI");    
        $this->assertEquals("VEN",$sheets['auto']->C3,"La cellule C3 est mal remplie CLEOFI"); 
        $this->assertEquals("706230",$sheets['auto']->D3,"La cellule D3 est mal remplie CLEOFI");  
        $this->assertNull($sheets['auto']->E3,"La cellule E3 est mal remplie CLEOFI"); 
        $this->assertEquals("C",$sheets['auto']->F3,"La cellule F3 est mal remplie CLEOFI");   
        $this->assertEquals("100",$sheets['auto']->G3,"La cellule G3 est mal remplies 5 CLEOFI");    
        $this->assertEquals("-",$sheets['auto']->H3,"La cellule H3 est mal remplie CLEOFI");   
        $this->assertEquals("F70010001",$sheets['auto']->I3,"La cellule I3 est mal remplie CLEOFI");   
        $this->assertNull($sheets['auto']->J3,"La cellule J3 est mal remplie CLEOFI"); 

        $this->assertEquals("A1",$sheets['auto']->A4,"La cellule A4 est mal remplie CLEOFI");      
        $this->assertEquals("01011970",$sheets['auto']->B4,"La cellule B4 est mal remplie CLEOFI");    
        $this->assertEquals("VEN",$sheets['auto']->C4,"La cellule C4 est mal remplie CLEOFI"); 
        $this->assertEquals("706400",$sheets['auto']->D4,"La cellule D4 est mal remplie CLEOFI");  
        $this->assertNull($sheets['auto']->E4,"La cellule E4 est mal remplie CLEOFI"); 
        $this->assertEquals("C",$sheets['auto']->F4,"La cellule F4 est mal remplie CLEOFI");   
        $this->assertEquals("100",$sheets['auto']->G4,"La cellule G4 est mal remplies CLEOFI");    
        $this->assertEquals("-",$sheets['auto']->H4,"La cellule H4 est mal remplie CLEOFI");   
        $this->assertEquals("F70010001",$sheets['auto']->I4,"La cellule I4 est mal remplie CLEOFI");   
        $this->assertEquals("20REFTu00",$sheets['auto']->J4,"La cellule J4 est mal remplie CLEOFI");   

        $this->assertEquals("G",$sheets['auto']->A5,"La cellule A5 est mal remplie CLEOFI");       
        $this->assertEquals("01011970",$sheets['auto']->B5,"La cellule B5 est mal remplie CLEOFI");    
        $this->assertEquals("VEN",$sheets['auto']->C5,"La cellule C5 est mal remplie CLEOFI"); 
        $this->assertEquals("445712",$sheets['auto']->D5,"La cellule D5 est mal remplie 5 CLEOFI");    
        $this->assertNull($sheets['auto']->E5,"La cellule E5 est mal remplie CLEOFI"); 
        $this->assertEquals("C",$sheets['auto']->F5,"La cellule F5 est mal remplie CLEOFI");   
        $this->assertEquals("100",$sheets['auto']->G5,"La cellule G5 est mal remplies CLEOFI");    
        $this->assertEquals("-",$sheets['auto']->H5,"La cellule H5 est mal remplie CLEOFI");   
        $this->assertEquals("F70010001",$sheets['auto']->I5,"La cellule I5 est mal remplie CLEOFI");   
        $this->assertNull($sheets['auto']->J5,"La cellule J5 est mal remplie CLEOFI"); 

        
        //pour vente
        $obj_vente = new objet_excel();
        $sheets=array("auto"=>$obj_vente);
        ATF::affaire()->u(array("id_affaire"=>$id_affaire,"nature"=>"vente"));      
        ATF::refinanceur()->u(array("id_refinanceur"=>$id_refinanceur,"refinanceur"=>"TU"));
        $infos=array(0=>array('facture.id_affaire_fk'=>$id_affaire,'facture.type_facture'=>'facture','facture.id_commande_fk'=>$id_commande,'facture.prix'=>'100'));
        
        $this->obj->ajoutDonnees($sheets,$infos);
        $this->assertEquals("G",$sheets['auto']->A2,"La cellule A2 est mal remplie vente");       
        $this->assertEquals("01011970",$sheets['auto']->B2,"La cellule B2 est mal remplie vente");    
        $this->assertEquals("VEN",$sheets['auto']->C2,"La cellule C2 est mal remplie vente"); 
        $this->assertEquals("411000",$sheets['auto']->D2,"La cellule D2 est mal remplie vente");  
        $this->assertNull($sheets['auto']->E2,"La cellule E2 est mal remplie vente"); 
        $this->assertEquals("D",$sheets['auto']->F2,"La cellule F2 est mal remplie vente");   
        $this->assertEquals("0",$sheets['auto']->G2,"La cellule G2 est mal remplie ventes 6");    
        $this->assertEquals("-",$sheets['auto']->H2,"La cellule H2 est mal remplie vente");   
        $this->assertEquals("F70010001",$sheets['auto']->I2,"La cellule I2 est mal remplie vente");   
        $this->assertNull($sheets['auto']->J2,"La cellule J2 est mal remplie vente"); 

        $this->assertEquals("G",$sheets['auto']->A3,"La cellule A3 est mal remplie vente");       
        $this->assertEquals("01011970",$sheets['auto']->B3,"La cellule B3 est mal remplie vente");    
        $this->assertEquals("VEN",$sheets['auto']->C3,"La cellule C3 est mal remplie vente"); 
        $this->assertEquals("707110",$sheets['auto']->D3,"La cellule D3 est mal remplie vente");  
        $this->assertNull($sheets['auto']->E3,"La cellule E3 est mal remplie vente"); 
        $this->assertEquals("C",$sheets['auto']->F3,"La cellule F3 est mal remplie vente");   
        $this->assertEquals("100",$sheets['auto']->G3,"La cellule G3 est mal remplie ventes 6");  
        $this->assertEquals("-",$sheets['auto']->H3,"La cellule H3 est mal remplie vente");   
        $this->assertEquals("F70010001",$sheets['auto']->I3,"La cellule I3 est mal remplie vente");   
        $this->assertNull($sheets['auto']->J3,"La cellule J3 est mal remplie vente"); 

        $this->assertEquals("A1",$sheets['auto']->A4,"La cellule A4 est mal remplie vente");      
        $this->assertEquals("01011970",$sheets['auto']->B4,"La cellule B4 est mal remplie vente");    
        $this->assertEquals("VEN",$sheets['auto']->C4,"La cellule C4 est mal remplie vente"); 
        $this->assertEquals("707110",$sheets['auto']->D4,"La cellule D4 est mal remplie vente");  
        $this->assertNull($sheets['auto']->E4,"La cellule E4 est mal remplie vente"); 
        $this->assertEquals("C",$sheets['auto']->F4,"La cellule F4 est mal remplie vente");   
        $this->assertEquals("100",$sheets['auto']->G4,"La cellule G4 est mal remplie ventes");    
        $this->assertEquals("-",$sheets['auto']->H4,"La cellule H4 est mal remplie vente");   
        $this->assertEquals("F70010001",$sheets['auto']->I4,"La cellule I4 est mal remplie vente");   
        $this->assertEquals("20REFTu00",$sheets['auto']->J4,"La cellule J4 est mal remplie vente");   

        $this->assertEquals("G",$sheets['auto']->A5,"La cellule A5 est mal remplie vente");       
        $this->assertEquals("01011970",$sheets['auto']->B5,"La cellule B5 est mal remplie vente");    
        $this->assertEquals("VEN",$sheets['auto']->C5,"La cellule C5 est mal remplie vente"); 
        $this->assertEquals("445710",$sheets['auto']->D5,"La cellule D5 est mal remplie vente vente6");    
        $this->assertNull($sheets['auto']->E5,"La cellule E5 est mal remplie vente"); 
        $this->assertEquals("C",$sheets['auto']->F5,"La cellule F5 est mal remplie vente");   
        $this->assertEquals("100",$sheets['auto']->G5,"La cellule G5 est mal remplie ventes");    
        $this->assertEquals("-",$sheets['auto']->H5,"La cellule H5 est mal remplie vente");   
        $this->assertEquals("F70010001",$sheets['auto']->I5,"La cellule I5 est mal remplie vente");   
        $this->assertNull($sheets['auto']->J5,"La cellule J5 est mal remplie vente"); 

        //pour pro
        ATF::affaire()->u(array("id_affaire"=>$id_affaire,"nature"=>"affaire"));    
        $id_commande=ATF::commande()->i(array("ref"=>"TU","date_debut"=>date('Y-m-d',strtotime("-1 month")),"date_evolution"=>date('Y-m-d',strtotime("-1 day")),"id_societe"=>$id_societe,"id_user"=>ATF::$usr->getId(),"tva"=>"19.6"));
        $obj_pro = new objet_excel();
        $sheets=array("auto"=>$obj_pro);
        $infos=array(0=>array('facture.id_affaire_fk'=>$id_affaire,'facture.type_facture'=>'facture','facture.date_periode_debut'=>date('Y-m-d'),'facture.id_commande_fk'=>$id_commande,'facture.prix'=>'100'));
        
        $this->obj->ajoutDonnees($sheets,$infos);

        $this->assertEquals("G",$sheets['auto']->A2,"La cellule A2 est mal remplie pro");       
        $this->assertEquals("01011970",$sheets['auto']->B2,"La cellule B2 est mal remplie pro");    
        $this->assertEquals("VEN",$sheets['auto']->C2,"La cellule C2 est mal remplie pro"); 
        $this->assertEquals("411000",$sheets['auto']->D2,"La cellule D2 est mal remplie pro");  
        $this->assertNull($sheets['auto']->E2,"La cellule E2 est mal remplie pro"); 
        $this->assertEquals("D",$sheets['auto']->F2,"La cellule F2 est mal remplie pro");   
        $this->assertEquals("0",$sheets['auto']->G2,"La cellule G2 est mal remplie pros 7");    
        $this->assertEquals("-",$sheets['auto']->H2,"La cellule H2 est mal remplie pro");   
        $this->assertEquals("F70010001",$sheets['auto']->I2,"La cellule I2 est mal remplie pro");   
        $this->assertNull($sheets['auto']->J2,"La cellule J2 est mal remplie pro"); 

        $this->assertEquals("G",$sheets['auto']->A3,"La cellule A3 est mal remplie pro");       
        $this->assertEquals("01011970",$sheets['auto']->B3,"La cellule B3 est mal remplie pro");    
        $this->assertEquals("VEN",$sheets['auto']->C3,"La cellule C3 est mal remplie pro"); 
        $this->assertEquals("706230",$sheets['auto']->D3,"La cellule D3 est mal remplie pro");  
        $this->assertNull($sheets['auto']->E3,"La cellule E3 est mal remplie pro"); 
        $this->assertEquals("C",$sheets['auto']->F3,"La cellule F3 est mal remplie pro");   
        $this->assertEquals("100",$sheets['auto']->G3,"La cellule G3 est mal remplie pros 7");  
        $this->assertEquals("-",$sheets['auto']->H3,"La cellule H3 est mal remplie pro");   
        $this->assertEquals("F70010001",$sheets['auto']->I3,"La cellule I3 est mal remplie pro");   
        $this->assertNull($sheets['auto']->J3,"La cellule J3 est mal remplie pro"); 

        $this->assertEquals("A1",$sheets['auto']->A4,"La cellule A4 est mal remplie pro");      
        $this->assertEquals("01011970",$sheets['auto']->B4,"La cellule B4 est mal remplie pro");    
        $this->assertEquals("VEN",$sheets['auto']->C4,"La cellule C4 est mal remplie pro"); 
        $this->assertEquals("706230",$sheets['auto']->D4,"La cellule D4 est mal remplie pro");  
        $this->assertNull($sheets['auto']->E4,"La cellule E4 est mal remplie pro"); 
        $this->assertEquals("C",$sheets['auto']->F4,"La cellule F4 est mal remplie pro");   
        $this->assertEquals("100",$sheets['auto']->G4,"La cellule G4 est mal remplie pros");    
        $this->assertEquals("-",$sheets['auto']->H4,"La cellule H4 est mal remplie pro");   
        $this->assertEquals("F70010001",$sheets['auto']->I4,"La cellule I4 est mal remplie pro");   
        $this->assertEquals("20REFTu00",$sheets['auto']->J4,"La cellule J4 est mal remplie pro");   

        $this->assertEquals("G",$sheets['auto']->A5,"La cellule A5 est mal remplie pro");       
        $this->assertEquals("01011970",$sheets['auto']->B5,"La cellule B5 est mal remplie pro");    
        $this->assertEquals("VEN",$sheets['auto']->C5,"La cellule C5 est mal remplie pro"); 
        $this->assertEquals("445713",$sheets['auto']->D5,"La cellule D5 est mal remplie pro vente7");    
        $this->assertNull($sheets['auto']->E5,"La cellule E5 est mal remplie pro"); 
        $this->assertEquals("C",$sheets['auto']->F5,"La cellule F5 est mal remplie pro");   
        $this->assertEquals("100",$sheets['auto']->G5,"La cellule G5 est mal remplie pros");    
        $this->assertEquals("-",$sheets['auto']->H5,"La cellule H5 est mal remplie pro");   
        $this->assertEquals("F70010001",$sheets['auto']->I5,"La cellule I5 est mal remplie pro");   
        $this->assertNull($sheets['auto']->J5,"La cellule J5 est mal remplie pro"); 



        //pour mad
        ATF::affaire()->u(array("id_affaire"=>$id_affaire,"nature"=>"affaire"));    
        ATF::commande()->u(array("id_commande"=>$id_commande,"date_debut"=>date('Y-m-d',strtotime("+1 day")),"date_evolution"=>date('Y-m-d',strtotime("+1 month"))));
        $obj_mad = new objet_excel();
        $sheets=array("auto"=>$obj_mad);
        $infos=array(0=>array('facture.id_affaire_fk'=>$id_affaire,'facture.type_facture'=>'facture','facture.date_periode_debut'=>date('Y-m-d'),'facture.id_commande_fk'=>$id_commande,'facture.prix'=>'100'));
        
        $this->obj->ajoutDonnees($sheets,$infos);

        $this->assertEquals("G",$sheets['auto']->A2,"La cellule A2 est mal remplie mad");       
        $this->assertEquals("01011970",$sheets['auto']->B2,"La cellule B2 est mal remplie mad");    
        $this->assertEquals("VEN",$sheets['auto']->C2,"La cellule C2 est mal remplie mad"); 
        $this->assertEquals("411000",$sheets['auto']->D2,"La cellule D2 est mal remplie mad");  
        $this->assertNull($sheets['auto']->E2,"La cellule E2 est mal remplie mad"); 
        $this->assertEquals("D",$sheets['auto']->F2,"La cellule F2 est mal remplie mad");   
        $this->assertEquals("0",$sheets['auto']->G2,"La cellule G2 est mal remplie mads 8");    
        $this->assertEquals("-",$sheets['auto']->H2,"La cellule H2 est mal remplie mad");   
        $this->assertEquals("F70010001",$sheets['auto']->I2,"La cellule I2 est mal remplie mad");   
        $this->assertNull($sheets['auto']->J2,"La cellule J2 est mal remplie mad"); 

        $this->assertEquals("G",$sheets['auto']->A3,"La cellule A3 est mal remplie mad");       
        $this->assertEquals("01011970",$sheets['auto']->B3,"La cellule B3 est mal remplie mad");    
        $this->assertEquals("VEN",$sheets['auto']->C3,"La cellule C3 est mal remplie mad"); 
        $this->assertEquals("706300",$sheets['auto']->D3,"La cellule D3 est mal remplie mad");  
        $this->assertNull($sheets['auto']->E3,"La cellule E3 est mal remplie mad"); 
        $this->assertEquals("C",$sheets['auto']->F3,"La cellule F3 est mal remplie mad");   
        $this->assertEquals("100",$sheets['auto']->G3,"La cellule G3 est mal remplie mads 8");  
        $this->assertEquals("-",$sheets['auto']->H3,"La cellule H3 est mal remplie mad");   
        $this->assertEquals("F70010001",$sheets['auto']->I3,"La cellule I3 est mal remplie mad");   
        $this->assertNull($sheets['auto']->J3,"La cellule J3 est mal remplie mad"); 

        $this->assertEquals("A1",$sheets['auto']->A4,"La cellule A4 est mal remplie mad");      
        $this->assertEquals("01011970",$sheets['auto']->B4,"La cellule B4 est mal remplie mad");    
        $this->assertEquals("VEN",$sheets['auto']->C4,"La cellule C4 est mal remplie mad"); 
        $this->assertEquals("706300",$sheets['auto']->D4,"La cellule D4 est mal remplie mad");  
        $this->assertNull($sheets['auto']->E4,"La cellule E4 est mal remplie mad"); 
        $this->assertEquals("C",$sheets['auto']->F4,"La cellule F4 est mal remplie mad");   
        $this->assertEquals("100",$sheets['auto']->G4,"La cellule G4 est mal remplie mads");    
        $this->assertEquals("-",$sheets['auto']->H4,"La cellule H4 est mal remplie mad");   
        $this->assertEquals("F70010001",$sheets['auto']->I4,"La cellule I4 est mal remplie mad");   
        $this->assertEquals("20REFTu00",$sheets['auto']->J4,"La cellule J4 est mal remplie mad");   

        $this->assertEquals("G",$sheets['auto']->A5,"La cellule A5 est mal remplie mad");       
        $this->assertEquals("01011970",$sheets['auto']->B5,"La cellule B5 est mal remplie mad");    
        $this->assertEquals("VEN",$sheets['auto']->C5,"La cellule C5 est mal remplie mad"); 
        $this->assertEquals("445715",$sheets['auto']->D5,"La cellule D5 est mal remplie mad 8");    
        $this->assertNull($sheets['auto']->E5,"La cellule E5 est mal remplie mad"); 
        $this->assertEquals("C",$sheets['auto']->F5,"La cellule F5 est mal remplie mad");   
        $this->assertEquals("100",$sheets['auto']->G5,"La cellule G5 est mal remplie mads");    
        $this->assertEquals("-",$sheets['auto']->H5,"La cellule H5 est mal remplie mad");   
        $this->assertEquals("F70010001",$sheets['auto']->I5,"La cellule I5 est mal remplie mad");   
        $this->assertNull($sheets['auto']->J5,"La cellule J5 est mal remplie mad"); 

        //pour avoir
        $obj_avoir = new objet_excel();
        $sheets=array("auto"=>$obj_avoir);
        $infos=array(0=>array('facture.id_affaire_fk'=>$id_affaire,'facture.prix'=>'-100'));
        
        $this->obj->ajoutDonnees($sheets,$infos);

        $this->assertEquals("G",$sheets['auto']->A2,"La cellule A2 est mal remplie avoir");       
        $this->assertEquals("01011970",$sheets['auto']->B2,"La cellule B2 est mal remplie avoir");    
        $this->assertEquals("VEN",$sheets['auto']->C2,"La cellule C2 est mal remplie avoir"); 
        $this->assertEquals("411000",$sheets['auto']->D2,"La cellule D2 est mal remplie avoir");  
        $this->assertNull($sheets['auto']->E2,"La cellule E2 est mal remplie avoir"); 
        $this->assertEquals("C",$sheets['auto']->F2,"La cellule F2 est mal remplie avoir");   
        $this->assertEquals("0",$sheets['auto']->G2,"La cellule G2 est mal remplie avoirs 9");    
        $this->assertEquals("-",$sheets['auto']->H2,"La cellule H2 est mal remplie avoir");   
        $this->assertEquals("F70010001",$sheets['auto']->I2,"La cellule I2 est mal remplie avoir");   
        $this->assertNull($sheets['auto']->J2,"La cellule J2 est mal remplie avoir"); 

        $this->assertEquals("G",$sheets['auto']->A3,"La cellule A3 est mal remplie avoir");       
        $this->assertEquals("01011970",$sheets['auto']->B3,"La cellule B3 est mal remplie avoir");    
        $this->assertEquals("VEN",$sheets['auto']->C3,"La cellule C3 est mal remplie avoir"); 
        $this->assertEquals("706400",$sheets['auto']->D3,"La cellule D3 est mal remplie avoir");  
        $this->assertNull($sheets['auto']->E3,"La cellule E3 est mal remplie avoir"); 
        $this->assertEquals("D",$sheets['auto']->F3,"La cellule F3 est mal remplie avoir");   
        $this->assertEquals("100",$sheets['auto']->G3,"La cellule G3 est mal remplie avoirs 9");  
        $this->assertEquals("-",$sheets['auto']->H3,"La cellule H3 est mal remplie avoir");   
        $this->assertEquals("F70010001",$sheets['auto']->I3,"La cellule I3 est mal remplie avoir");   
        $this->assertNull($sheets['auto']->J3,"La cellule J3 est mal remplie avoir"); 

        $this->assertEquals("A1",$sheets['auto']->A4,"La cellule A4 est mal remplie avoir");      
        $this->assertEquals("01011970",$sheets['auto']->B4,"La cellule B4 est mal remplie avoir");    
        $this->assertEquals("VEN",$sheets['auto']->C4,"La cellule C4 est mal remplie avoir"); 
        $this->assertEquals("706400",$sheets['auto']->D4,"La cellule D4 est mal remplie avoir");  
        $this->assertNull($sheets['auto']->E4,"La cellule E4 est mal remplie avoir"); 
        $this->assertEquals("D",$sheets['auto']->F4,"La cellule F4 est mal remplie avoir");   
        $this->assertEquals("100",$sheets['auto']->G4,"La cellule G4 est mal remplie avoirs");    
        $this->assertEquals("-",$sheets['auto']->H4,"La cellule H4 est mal remplie avoir");   
        $this->assertEquals("F70010001",$sheets['auto']->I4,"La cellule I4 est mal remplie avoir");   
        $this->assertEquals("20REFTu00",$sheets['auto']->J4,"La cellule J4 est mal remplie avoir");   

        $this->assertEquals("G",$sheets['auto']->A5,"La cellule A5 est mal remplie avoir");       
        $this->assertEquals("01011970",$sheets['auto']->B5,"La cellule B5 est mal remplie avoir");    
        $this->assertEquals("VEN",$sheets['auto']->C5,"La cellule C5 est mal remplie avoir"); 
        $this->assertEquals("445710",$sheets['auto']->D5,"La cellule D5 est mal remplie avoir 9");    
        $this->assertNull($sheets['auto']->E5,"La cellule E5 est mal remplie avoir"); 
        $this->assertEquals("D",$sheets['auto']->F5,"La cellule F5 est mal remplie avoir");   
        $this->assertEquals("100",$sheets['auto']->G5,"La cellule G5 est mal remplie avoirs");    
        $this->assertEquals("-",$sheets['auto']->H5,"La cellule H5 est mal remplie avoir");   
        $this->assertEquals("F70010001",$sheets['auto']->I5,"La cellule I5 est mal remplie avoir");   
        $this->assertNull($sheets['auto']->J5,"La cellule J5 est mal remplie avoir"); 

        //pour avenant
        $obj_avoir = new objet_excel();
        $sheets=array("auto"=>$obj_avoir);
        ATF::affaire()->u(array("id_affaire"=>$id_affaire,"nature"=>"avenant"));        
        $infos=array(0=>array('facture.id_affaire_fk'=>$id_affaire,'facture.prix'=>'-100'));
        
        $this->obj->ajoutDonnees($sheets,$infos);

        $this->assertEquals("G",$sheets['auto']->A2,"La cellule A2 est mal remplie avenant");       
        $this->assertEquals("01011970",$sheets['auto']->B2,"La cellule B2 est mal remplie avenant");    
        $this->assertEquals("VEN",$sheets['auto']->C2,"La cellule C2 est mal remplie avenant"); 
        $this->assertEquals("411000",$sheets['auto']->D2,"La cellule D2 est mal remplie avenant");  
        $this->assertNull($sheets['auto']->E2,"La cellule E2 est mal remplie avenant"); 
        $this->assertEquals("C",$sheets['auto']->F2,"La cellule F2 est mal remplie avenant");   
        $this->assertEquals("0",$sheets['auto']->G2,"La cellule G2 est mal remplie avenants 9");    
        $this->assertEquals("-",$sheets['auto']->H2,"La cellule H2 est mal remplie avenant");   
        $this->assertEquals("F70010001",$sheets['auto']->I2,"La cellule I2 est mal remplie avenant");   
        $this->assertNull($sheets['auto']->J2,"La cellule J2 est mal remplie avenant"); 

        $this->assertEquals("G",$sheets['auto']->A3,"La cellule A3 est mal remplie avenant");       
        $this->assertEquals("01011970",$sheets['auto']->B3,"La cellule B3 est mal remplie avenant");    
        $this->assertEquals("VEN",$sheets['auto']->C3,"La cellule C3 est mal remplie avenant"); 
        $this->assertEquals("706400",$sheets['auto']->D3,"La cellule D3 est mal remplie avenant");  
        $this->assertNull($sheets['auto']->E3,"La cellule E3 est mal remplie avenant"); 
        $this->assertEquals("D",$sheets['auto']->F3,"La cellule F3 est mal remplie avenant");   
        $this->assertEquals("100",$sheets['auto']->G3,"La cellule G3 est mal remplie avenants 9");  
        $this->assertEquals("-",$sheets['auto']->H3,"La cellule H3 est mal remplie avenant");   
        $this->assertEquals("F70010001",$sheets['auto']->I3,"La cellule I3 est mal remplie avenant");   
        $this->assertNull($sheets['auto']->J3,"La cellule J3 est mal remplie avenant"); 

        $this->assertEquals("A1",$sheets['auto']->A4,"La cellule A4 est mal remplie avenant");      
        $this->assertEquals("01011970",$sheets['auto']->B4,"La cellule B4 est mal remplie avenant");    
        $this->assertEquals("VEN",$sheets['auto']->C4,"La cellule C4 est mal remplie avenant"); 
        $this->assertEquals("706400",$sheets['auto']->D4,"La cellule D4 est mal remplie avenant");  
        $this->assertNull($sheets['auto']->E4,"La cellule E4 est mal remplie avenant"); 
        $this->assertEquals("D",$sheets['auto']->F4,"La cellule F4 est mal remplie avenant");   
        $this->assertEquals("100",$sheets['auto']->G4,"La cellule G4 est mal remplie avenants");    
        $this->assertEquals("-",$sheets['auto']->H4,"La cellule H4 est mal remplie avenant");   
        $this->assertEquals("F70010001",$sheets['auto']->I4,"La cellule I4 est mal remplie avenant");   
        $this->assertEquals("20REFTuAV",$sheets['auto']->J4,"La cellule J4 est mal remplie avenant");   

        $this->assertEquals("G",$sheets['auto']->A5,"La cellule A5 est mal remplie avenant");       
        $this->assertEquals("01011970",$sheets['auto']->B5,"La cellule B5 est mal remplie avenant");    
        $this->assertEquals("VEN",$sheets['auto']->C5,"La cellule C5 est mal remplie avenant"); 
        $this->assertEquals("445710",$sheets['auto']->D5,"La cellule D5 est mal remplie avenant 9");    
        $this->assertNull($sheets['auto']->E5,"La cellule E5 est mal remplie avenant"); 
        $this->assertEquals("D",$sheets['auto']->F5,"La cellule F5 est mal remplie avenant");   
        $this->assertEquals("100",$sheets['auto']->G5,"La cellule G5 est mal remplie avenants");    
        $this->assertEquals("-",$sheets['auto']->H5,"La cellule H5 est mal remplie avenant");   
        $this->assertEquals("F70010001",$sheets['auto']->I5,"La cellule I5 est mal remplie avenant");   
        $this->assertNull($sheets['auto']->J5,"La cellule J5 est mal remplie avenant"); 

        //pour increment
        $obj_avoir = new objet_excel();
        $sheets=array("auto"=>$obj_avoir);
        ATF::affaire()->u(array("id_affaire"=>$id_affaire,"nature"=>"avenant"));        
        for ($i=1;$i<=1000;$i++){
            if($i==1 || $i==10 || $i==100 || $i==1000){
                $infos[$i]=array('facture.id_affaire_fk'=>$id_affaire,'facture.prix'=>'-100');
            }else{
                $infos[$i]=NULL;
            }
        }
        
        $this->obj->ajoutDonnees($sheets,$infos);

        $this->assertEquals("F70010001",$sheets['auto']->I2,"La cellule I2 est mal remplie");   
        $this->assertEquals("F70010011",$sheets['auto']->I10,"La cellule I10 est mal remplie"); 
        $this->assertEquals("F70010101",$sheets['auto']->I14,"La cellule I14 est mal remplie"); 
        $this->assertEquals("F70011001",$sheets['auto']->I18,"La cellule I18 est mal remplie"); 

    }

	public function testAjoutDonneesRejet(){
		//pour refi
        $obj_refi = new objet_excel();
        $sheets=array("auto"=>$obj_refi);
        
        $id_societe=ATF::societe()->i(array("societe"=>"TU"));
        $id_affaire=ATF::affaire()->i(array("ref"=>"REFTu","id_societe"=>$id_societe,"affaire"=>"ATU"));        
        $infos=array(0=>array('facture.id_affaire_fk'=>$id_affaire,'facture.type_facture'=>'refi','facture.prix'=>'100'));
        $infos["rejet"] = "ok";
        $this->obj->ajoutDonnees($sheets,$infos);
		
        $this->assertEquals("G",$sheets['auto']->A2,"La cellule A2 est mal remplie refi");       
        $this->assertEquals("01011970",$sheets['auto']->B2,"La cellule B2 est mal remplie refi");    
        $this->assertEquals("VEN",$sheets['auto']->C2,"La cellule C2 est mal remplie refi"); 
        $this->assertEquals("771000",$sheets['auto']->D2,"La cellule D2 est mal remplie refi");  
        $this->assertNull($sheets['auto']->E2,"La cellule E2 est mal remplie refi"); 
        $this->assertEquals("C",$sheets['auto']->F2,"La cellule F2 est mal remplie refi");   
        		
        //pour auto
        $obj_auto = new objet_excel();
        $sheets=array("auto"=>$obj_auto);
        
        $infos=array(0=>array('facture.id_affaire_fk'=>$id_affaire,'facture.type_facture'=>'auto','facture.prix'=>'100'));
        $infos["rejet"] = "ok";
        $this->obj->ajoutDonnees($sheets,$infos);
				
        $this->assertEquals("G",$sheets['auto']->A2,"La cellule A2 est mal remplie auto"); 
		$this->assertEquals("771000",$sheets['auto']->D2,"La cellule D2 est mal remplie auto"); 		
		$this->assertEquals("A1",$sheets['auto']->A3,"La cellule A3 est mal remplie auto"); 
		$this->assertEquals("771000",$sheets['auto']->D3,"La cellule D6 est mal remplie auto");
		$this->assertEquals("G",$sheets['auto']->A4,"La cellule A4 est mal remplie auto"); 
		$this->assertEquals("445710",$sheets['auto']->D4,"La cellule D4 est mal remplie auto");
		
		    
        //pour CLEODIS
        $obj_auto = new objet_excel();
        $sheets=array("auto"=>$obj_auto);
        $id_refinanceur=ATF::refinanceur()->i(array("refinanceur"=>"CLEODIS","code"=>"123","code_refi"=>"123"));
        $id_contact=ATF::contact()->i(array("nom"=>"TU"));
        $id_demande_refi=ATF::demande_refi()->i(array("id_contact"=>$id_contact,"date"=>date("Y-m-d"),"id_refinanceur"=>$id_refinanceur,"id_affaire"=>$id_affaire,"id_societe"=>$id_societe,"description"=>"TU","etat"=>"valide"));
        $infos=array(0=>array('facture.id_affaire_fk'=>$id_affaire,'facture.type_facture'=>'facture','facture.prix'=>'100'));
         $infos["rejet"] = "ok";
        $this->obj->ajoutDonnees($sheets,$infos,true);
        
        $this->assertEquals("G",$sheets['auto']->A2,"La cellule A2 est mal remplie cleodis"); 
		$this->assertEquals("771000",$sheets['auto']->D2,"La cellule D2 est mal remplie cleodis"); 		
		$this->assertEquals("A1",$sheets['auto']->A3,"La cellule A3 est mal remplie cleodis"); 
		$this->assertEquals("771000",$sheets['auto']->D3,"La cellule D6 est mal remplie cleodis");
		$this->assertEquals("G",$sheets['auto']->A4,"La cellule A4 est mal remplie cleodis"); 
		$this->assertEquals("445712",$sheets['auto']->D4,"La cellule D4 est mal remplie cleodis");
        

        //pour BMF
        ATF::refinanceur()->u(array("id_refinanceur"=>$id_refinanceur,"refinanceur"=>"BMF"));
        $infos["rejet"] = "ok";
        $this->obj->ajoutDonnees($sheets,$infos);
       
        $this->assertEquals("G",$sheets['auto']->A2,"La cellule A2 est mal remplie BMF"); 
        $this->assertEquals("A1",$sheets['auto']->A3,"La cellule A3 est mal remplie BMF"); 
        $this->assertEquals("G",$sheets['auto']->A4,"La cellule A4 est mal remplie BMF"); 
		
		$this->assertEquals("771000",$sheets['auto']->D2,"La cellule D2 est mal remplie BMF"); 
        $this->assertEquals("771000",$sheets['auto']->D3,"La cellule D3 est mal remplie BMF"); 
        $this->assertEquals("445712",$sheets['auto']->D4,"La cellule D4 est mal remplie BMF");
		
		
        //pour CLEOFI
        ATF::refinanceur()->u(array("id_refinanceur"=>$id_refinanceur,"refinanceur"=>"CLEOFI"));
        $infos["rejet"] = "ok";
        $this->obj->ajoutDonnees($sheets,$infos);
		
       	$this->assertEquals("G",$sheets['auto']->A2,"La cellule A2 est mal remplie CLEOFI"); 
        $this->assertEquals("A1",$sheets['auto']->A3,"La cellule A3 est mal remplie CLEOFI"); 
        $this->assertEquals("G",$sheets['auto']->A4,"La cellule A4 est mal remplie CLEOFI"); 
		
		$this->assertEquals("706230",$sheets['auto']->D2,"La cellule D2 est mal remplie CLEOFI"); 
        $this->assertEquals("771000",$sheets['auto']->D3,"La cellule D3 est mal remplie CLEOFI"); 
        $this->assertEquals("445712",$sheets['auto']->D4,"La cellule D4 est mal remplie CLEOFI");
        
        //pour vente
        $obj_vente = new objet_excel();
        $sheets=array("auto"=>$obj_vente);
        ATF::affaire()->u(array("id_affaire"=>$id_affaire,"nature"=>"vente"));      
        ATF::refinanceur()->u(array("id_refinanceur"=>$id_refinanceur,"refinanceur"=>"TU"));
        $infos=array(0=>array('facture.id_affaire_fk'=>$id_affaire,'facture.type_facture'=>'facture','facture.id_commande_fk'=>$id_commande,'facture.prix'=>'100'));
        $infos["rejet"] = "ok";
        $this->obj->ajoutDonnees($sheets,$infos);
      
		$this->assertEquals("G",$sheets['auto']->A2,"La cellule A2 est mal remplie vente"); 
        $this->assertEquals("A1",$sheets['auto']->A3,"La cellule A3 est mal remplie vente"); 
        $this->assertEquals("G",$sheets['auto']->A4,"La cellule A4 est mal remplie vente"); 
		
		$this->assertEquals("771000",$sheets['auto']->D2,"La cellule D2 est mal remplie vente"); 
        $this->assertEquals("771000",$sheets['auto']->D3,"La cellule D3 est mal remplie vente"); 
        $this->assertEquals("445710",$sheets['auto']->D4,"La cellule D4 est mal remplie vente");
		
		
        //pour pro
        ATF::affaire()->u(array("id_affaire"=>$id_affaire,"nature"=>"affaire"));    
        $id_commande=ATF::commande()->i(array("ref"=>"TU","date_debut"=>date('Y-m-d',strtotime("-1 month")),"date_evolution"=>date('Y-m-d',strtotime("-1 day")),"id_societe"=>$id_societe,"id_user"=>ATF::$usr->getId(),"tva"=>"19.6"));
        $obj_pro = new objet_excel();
        $sheets=array("auto"=>$obj_pro);
        $infos=array(0=>array('facture.id_affaire_fk'=>$id_affaire,'facture.type_facture'=>'facture','facture.date_periode_debut'=>date('Y-m-d'),'facture.id_commande_fk'=>$id_commande,'facture.prix'=>'100'));
        $infos["rejet"] = "ok";
        $this->obj->ajoutDonnees($sheets,$infos);
       	$this->assertEquals("G",$sheets['auto']->A2,"La cellule A2 est mal remplie pro"); 
        $this->assertEquals("A1",$sheets['auto']->A3,"La cellule A3 est mal remplie pro"); 
        $this->assertEquals("G",$sheets['auto']->A4,"La cellule A4 est mal remplie pro"); 
		
		$this->assertEquals("771000",$sheets['auto']->D2,"La cellule D2 est mal remplie pro"); 
        $this->assertEquals("771000",$sheets['auto']->D3,"La cellule D3 est mal remplie pro"); 
        $this->assertEquals("445713",$sheets['auto']->D4,"La cellule D4 est mal remplie pro");


        //pour mad
        ATF::affaire()->u(array("id_affaire"=>$id_affaire,"nature"=>"affaire"));    
        ATF::commande()->u(array("id_commande"=>$id_commande,"date_debut"=>date('Y-m-d',strtotime("+1 day")),"date_evolution"=>date('Y-m-d',strtotime("+1 month"))));
        $obj_mad = new objet_excel();
        $sheets=array("auto"=>$obj_mad);
        $infos=array(0=>array('facture.id_affaire_fk'=>$id_affaire,'facture.type_facture'=>'facture','facture.date_periode_debut'=>date('Y-m-d'),'facture.id_commande_fk'=>$id_commande,'facture.prix'=>'100'));
        $infos["rejet"] = "ok";
        $this->obj->ajoutDonnees($sheets,$infos);
				
		$this->assertEquals("G",$sheets['auto']->A2,"La cellule A2 est mal remplie mad"); 
        $this->assertEquals("A1",$sheets['auto']->A3,"La cellule A3 est mal remplie mad"); 
        $this->assertEquals("G",$sheets['auto']->A4,"La cellule A4 est mal remplie mad"); 
		
		$this->assertEquals("771000",$sheets['auto']->D2,"La cellule D2 est mal remplie mad"); 
        $this->assertEquals("771000",$sheets['auto']->D3,"La cellule D3 est mal remplie mad"); 
        $this->assertEquals("445715",$sheets['auto']->D4,"La cellule D4 est mal remplie mad");
		
       
        //pour avoir
        $obj_avoir = new objet_excel();
        $sheets=array("auto"=>$obj_avoir);
        $infos=array(0=>array('facture.id_affaire_fk'=>$id_affaire,'facture.prix'=>'-100'));
         $infos["rejet"] = "ok";
        $this->obj->ajoutDonnees($sheets,$infos);
		$this->assertEquals("G",$sheets['auto']->A2,"La cellule A2 est mal remplie avoir"); 
        $this->assertEquals("A1",$sheets['auto']->A3,"La cellule A3 est mal remplie avoir"); 
        $this->assertEquals("G",$sheets['auto']->A4,"La cellule A4 est mal remplie avoir"); 
		
		$this->assertEquals("771000",$sheets['auto']->D2,"La cellule D2 est mal remplie avoir"); 
        $this->assertEquals("771000",$sheets['auto']->D3,"La cellule D3 est mal remplie avoir"); 
        $this->assertEquals("445710",$sheets['auto']->D4,"La cellule D4 est mal remplie avoir");

        //pour avenant
        $obj_avoir = new objet_excel();
        $sheets=array("auto"=>$obj_avoir);
        ATF::affaire()->u(array("id_affaire"=>$id_affaire,"nature"=>"avenant"));        
        $infos=array(0=>array('facture.id_affaire_fk'=>$id_affaire,'facture.prix'=>'-100'));
        $infos["rejet"] = "ok";
        $this->obj->ajoutDonnees($sheets,$infos);
 		$this->assertEquals("G",$sheets['auto']->A2,"La cellule A2 est mal remplie avenant"); 
        $this->assertEquals("A1",$sheets['auto']->A3,"La cellule A3 est mal remplie avenant"); 
        $this->assertEquals("G",$sheets['auto']->A4,"La cellule A4 est mal remplie avenant");
		$this->assertEquals("771000",$sheets['auto']->D2,"La cellule D2 est mal remplie avenant"); 
        $this->assertEquals("771000",$sheets['auto']->D3,"La cellule D3 est mal remplie avenant"); 
        $this->assertEquals("445710",$sheets['auto']->D4,"La cellule D4 est mal remplie avenant");
        

        //pour increment
        $obj_avoir = new objet_excel();
        $sheets=array("auto"=>$obj_avoir);
        ATF::affaire()->u(array("id_affaire"=>$id_affaire,"nature"=>"avenant"));        
        for ($i=1;$i<=1000;$i++){
            if($i==1 || $i==10 || $i==100 || $i==1000){
                $infos[$i]=array('facture.id_affaire_fk'=>$id_affaire,'facture.prix'=>'-100');
            }else{
                $infos[$i]=NULL;
            }
        }   
		$infos["rejet"] = "ok";     
        $this->obj->ajoutDonnees($sheets,$infos);		
		$this->assertEquals("F70010003",$sheets['auto']->I6,"La cellule I6 est mal remplie");
		$this->assertEquals("F70010012",$sheets['auto']->I8,"La cellule I8 est mal remplie"); 
		
		
		//pour prolongation refi par CLEOFI
        ATF::affaire()->u(array("id_affaire"=>$id_affaire,"nature"=>"affaire"));   
		ATF::refinanceur()->u(array("id_refinanceur"=>$id_refinanceur,"refinanceur"=>"CLEOFI")); 
        $id_commande=ATF::commande()->i(array("ref"=>"TU22","date_debut"=>date('Y-m-d',strtotime("-2 month")),"date_evolution"=>date('Y-m-d',strtotime("-1 month")),"id_societe"=>$id_societe,"id_user"=>ATF::$usr->getId(),"tva"=>"19.6"));
        $obj_pro = new objet_excel();
        $sheets=array("auto"=>$obj_pro);
        $infos=array(0=>array('facture.id_affaire_fk'=>$id_affaire,'facture.type_facture'=>'facture','facture.date_periode_debut'=>date('Y-m-d'),'facture.id_commande_fk'=>$id_commande,'facture.prix'=>'100'));
        $infos["rejet"] = "ok";
        $this->obj->ajoutDonnees($sheets,$infos);
       	$this->assertEquals("G",$sheets['auto']->A2,"La cellule A2 est mal remplie prolongation refi par CLEOFI"); 
        $this->assertEquals("A1",$sheets['auto']->A3,"La cellule A3 est mal remplie prolongation refi par CLEOFI"); 
        $this->assertEquals("G",$sheets['auto']->A4,"La cellule A4 est mal remplie prolongation refi par CLEOFI"); 
		
		$this->assertEquals("706230",$sheets['auto']->D2,"La cellule D2 est mal remplie prolongation refi par CLEOFI"); 
        $this->assertEquals("706230",$sheets['auto']->D3,"La cellule D3 est mal remplie prolongation refi par CLEOFI"); 
        $this->assertEquals("445713",$sheets['auto']->D4,"La cellule D4 est mal remplie prolongation refi par CLEOFI");
        
		ATF::affaire()->u(array("id_affaire"=>$id_affaire,"nature"=>"avenant"));   
		ATF::refinanceur()->u(array("id_refinanceur"=>$id_refinanceur,"refinanceur"=>"CLEOFI")); 
        $id_commande=ATF::commande()->i(array("ref"=>"TU23","date_debut"=>date('Y-m-d',strtotime("-3 month")),"date_evolution"=>date('Y-m-d',strtotime("-2 month")),"id_societe"=>$id_societe,"id_user"=>ATF::$usr->getId(),"tva"=>"19.6"));
        $obj_pro2 = new objet_excel();
        $sheets=array("auto"=>$obj_pro2);
        $infos=array(0=>array('facture.id_affaire_fk'=>$id_affaire,'facture.type_facture'=>'facture','facture.date_periode_debut'=>date('Y-m-d'),'facture.id_commande_fk'=>$id_commande,'facture.prix'=>'100'));
        $infos["rejet"] = "ok";
        $this->obj->ajoutDonnees($sheets,$infos);
       	$this->assertEquals("G",$sheets['auto']->A2,"La cellule A2 est mal remplie prolongation refi par CLEOFI 2"); 
        $this->assertEquals("A1",$sheets['auto']->A3,"La cellule A3 est mal remplie prolongation refi par CLEOFI 2"); 
        $this->assertEquals("G",$sheets['auto']->A4,"La cellule A4 est mal remplie prolongation refi par CLEOFI 2"); 
		
		$this->assertEquals("706230",$sheets['auto']->D2,"La cellule D2 est mal remplie prolongation refi par CLEOFI 2"); 
        $this->assertEquals("20REFTuAV",$sheets['auto']->J3,"La cellule D3 est mal remplie prolongation refi par CLEOFI 2"); 
        $this->assertEquals("445713",$sheets['auto']->D4,"La cellule D4 est mal remplie prolongation refi par CLEOFI 2");
		
		
		$obj_pro = new objet_excel();
        $sheets=array("auto"=>$obj_pro);
        $infos=array(0=>array('facture.id_affaire_fk'=>"0705048",
        					  'facture.type_facture'=>'facture',
        					  'facture.date_periode_debut'=>"2010-01-01",
        					  'facture.prix'=>'100',
							  "facture.tva "=> "1.196",
							  "facture.id_societe_fk" => "372",
					          "facture.id_affaire_fk" => "599",
					          "commande.etat_fk" => "1450",
					          "facture.id_facture_fk" => "14362",
					          "facture.id_user_fk" => 21));
        $infos["rejet"] = "ok";	
        $this->obj->ajoutDonnees($sheets,$infos);
		
		$obj_pro = new objet_excel();
        $sheets=array("auto"=>$obj_pro);
        $infos=array(0=>array('facture.id_affaire_fk'=>"0705048",
        					  'facture.type_facture'=>'facture',
        					  'facture.date_periode_debut'=>"2010-01-01",
        					  'facture.prix'=>'100',
							  "facture.tva "=> "1.196",
							  "facture.id_societe_fk" => "372",
					          "facture.id_affaire_fk" => "599",
					          "commande.etat_fk" => "1450",
					          "facture.id_facture_fk" => "14362",
					          "facture.id_user_fk" => 21));
        unset($infos["rejet"]);	
        $this->obj->ajoutDonnees($sheets,$infos);		
	}

    // @author Nicolas BERTEMONT <nbertemont@absystech.fr> 
    public function test_export_xls_special(){
        //lancer l'initialisation de la récupération du fichier
        ob_start();
        $infos=array("toto"=>"lol");
        $this->obj->export_xls_special($infos);
        
        //récupération des infos
        $fichier=ob_get_contents();

        //suppression des éléments (dans tampon)
        ob_end_clean();

        //vérification des informations
        $chem=__TEMP_PATH__.ATF::$codename."/lol.csv";
        $chem2=__TEMP_PATH__.ATF::$codename."/lol2.xls";
        file_put_contents($chem2,$fichier);
        file_put_contents($chem,`xls2csv -d utf-8 $chem2`);

        if ($f = fopen ($chem,"r")) {
            while ($data = fgetcsv($f, 0, ";")) {           
                //noms des colonnes
                if ($data[0]=='JOURNAL,"DATE","DEVISE","COMPTE GENERAL","TIERS","LIBELLE","REF","DEBIT","CREDIT"'){
                    $informations++;
                }
            }
            fclose($f);
        }
        unlink($chem);
        unlink($chem2);
        
        $this->assertNull($informations,"Le nombre d'onglet dans le fichier est incorrect");
        
    }

    // @author Nicolas BERTEMONT <nbertemont@absystech.fr> 
    public function test_export_special(){
        $this->initUserOnly(false);
        $id_soc=ATF::societe()->i(array("societe"=>"soc lol"));
        $id_aff=ATF::affaire()->insert(array("ref"=>"ref lol","id_societe"=>$id_soc,"affaire"=>"aff lol"));
        $id_fac=ATF::facture()->i(array("ref"=>"ref lol","id_societe"=>$id_soc,"prix"=>45,"date"=>date("Y-m-d"),"tva"=>"19.6","id_affaire"=>$id_aff));
        

        $q=ATF::_s("pager")->create("gsa_lol_lol");
        $q->reset()->addField("ref")->setStrict()->addCondition("id_facture",$id_fac);
        $c_fact=new fact();
        $c_fact->export_special(array('onglet'=>"gsa_lol_lol"));    
        if($c_fact->message){
            $this->assertTrue(false,$c_fact->message);
        }
		$c_fact->export_special2(array('onglet'=>"gsa_lol_lol"));
    }
    
    //@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  
    public function test_getRef(){
        $this->assertEquals("0611033-1-AP",
                            $this->obj->getRef(89,"ap"),
                            "getRef ap");   

        $this->assertEquals("1105027-2-RE",
                            $this->obj->getRef(5030,"refi"),
                            "getRef refi"); 

        $this->assertEquals("0611002-4-LI",
                            $this->obj->getRef(57,"libre"),
                            "getRef libre");    

        $this->assertEquals("0607004-7",
                            $this->obj->getRef(33),
                            "getRef");  

        $this->assertEquals("0809055AVT-14",
                            $this->obj->getRef(2260),
                            "getRef avt");  

    }
    
    //@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  
    public function test_defaultValues(){
        $this->insertFacture();
        
        ATF::facturation()->u(array("id_facturation"=>$this->id_facturation,"id_facture"=>NULL));
        
        ATF::societe()->u(array("id_societe"=>$this->id_societe,"id_contact_facturation"=>$this->id_contact));
        
        ATF::$usr->maj_infos(16);
        
        $this->assertNull(ATF::facture()->default_value("email"),'valeur email avant remplissage du request');
        $this->assertEquals(ATF::$usr->get("email"),ATF::facture()->default_value("emailCopie"),'email marge_absolue avant remplissage du request');
        $this->assertNull(ATF::facture()->default_value("emailTexte"),'valeur emailTexte');
        $this->assertEquals(ATF::$usr->get('id_user'),ATF::facture()->default_value("id_user"),'id_user marge_absolue avant remplissage du request');
        $this->assertEquals(date("Y-m-d"),ATF::facture()->default_value("date"),'valeur date avant remplissage du request');
        $this->assertEquals(date("Y-m-d"),ATF::facture()->default_value("date_previsionnelle"),'valeur date_previsionnelle avant remplissage du request');
        $this->assertNull(ATF::facture()->default_value("id_affaire"),'valeur id_affaire');
        $this->assertNull(ATF::facture()->default_value("id_commande"),'valeur id_commande');
        $this->assertNull(ATF::facture()->default_value("id_societe"),'valeur id_societe');
        $this->assertNull(ATF::facture()->default_value("date_periode_debut"),'valeur id_affaire');
        $this->assertNull(ATF::facture()->default_value("date_periode_fin"),'valeur date_periode_debut');
        $this->assertNull(ATF::facture()->default_value("prix"),'valeur prix');
        $this->assertNull(ATF::facture()->default_value("prix_refi"),'valeur prix_refi');
        $this->assertNull(ATF::facture()->default_value("mode_paiement"),'valeur mode_paiement');

        ATF::_r('id_commande',$this->id_commande);
        $this->assertEquals("debug@absystech.fr",ATF::facture()->default_value("email"),'valeur email commande');
        $this->assertEquals(ATF::$usr->get("email"),ATF::facture()->default_value("emailCopie"),'email commande');
        $this->assertEquals(ATF::facture()->majMail($this->id_societe),ATF::facture()->default_value("emailTexte"),'valeur emailTexte commande');
        $this->assertEquals($this->id_user,ATF::facture()->default_value("id_user"),'id_user commande');
        $this->assertEquals(date("Y-m-d"),ATF::facture()->default_value("date"),'valeur date commande');
        $this->assertEquals(date("Y-m-d",strtotime(date("Y-m-01")." 1 month")),ATF::facture()->default_value("date_previsionnelle"),'valeur date_previsionnelle commande');
        $this->assertEquals($this->id_affaire,ATF::facture()->default_value("id_affaire"),'valeur id_affaire commande');
        $this->assertEquals($this->id_commande,ATF::facture()->default_value("id_commande"),'valeur id_commande commande');
        $this->assertEquals($this->id_societe,ATF::facture()->default_value("id_societe"),'valeur id_societe commande');
        $this->assertEquals(date("Y-m-d",strtotime(date("Y-m-01")." 1 month")),ATF::facture()->default_value("date_periode_debut"),'valeur id_affaire commande');
        $this->assertEquals(date("Y-m-d",strtotime(date("Y-m-01")." 2 month")),ATF::facture()->default_value("date_periode_fin"),'valeur date_periode_debut commande');
        $this->assertEquals("120",ATF::facture()->default_value("prix"),'valeur prix commande');
        $this->assertEquals(50,ATF::facture()->default_value("prix_refi"),'valeur prix_refi commande');
        ATF::affaire()->u(array("id_affaire"=>$this->id_affaire,"nature"=>"vente"));
        $this->assertEquals("cheque",ATF::facture()->default_value("mode_paiement"),'valeur mode_paiement1 commande');
        ATF::affaire()->u(array("id_affaire"=>$this->id_affaire,"nature"=>"affaire"));
        $this->assertNull(ATF::facture()->default_value("mode_paiement"),'valeur mode_paiement2 commande');
        
        ATF::_r('id_facture',$this->id_facture);
        $this->assertEquals("debug@absystech.fr",ATF::facture()->default_value("email"),'valeur email facture');
        $this->assertEquals(ATF::$usr->get("email"),ATF::facture()->default_value("emailCopie"),'email facture');
        $this->assertEquals(ATF::facture()->majMail($this->id_societe),ATF::facture()->default_value("emailTexte"),'valeur emailTexte facture');
        $this->assertEquals($this->id_user,ATF::facture()->default_value("id_user"),'id_user facture');
        $this->assertEquals(date("Y-m-d"),ATF::facture()->default_value("date"),'valeur date facture');
        $this->assertEquals(date("Y-m-d",strtotime(date("Y-m-01")." 1 month")),ATF::facture()->default_value("date_previsionnelle"),'valeur date_previsionnelle facture');
        $this->assertEquals($this->id_affaire,ATF::facture()->default_value("id_affaire"),'valeur id_affaire facture');
        $this->assertEquals($this->id_commande,ATF::facture()->default_value("id_commande"),'valeur id_commande facture');
        $this->assertEquals($this->id_societe,ATF::facture()->default_value("id_societe"),'valeur id_societe commande');
        $this->assertEquals(date("Y-m-d",strtotime(date("Y-m-01")." 1 month")),ATF::facture()->default_value("date_periode_debut"),'valeur id_affaire facture');
        $this->assertEquals(date("Y-m-d",strtotime(date("Y-m-01")." 2 month")),ATF::facture()->default_value("date_periode_fin"),'valeur date_periode_debut facture');
        $this->assertEquals("120",ATF::facture()->default_value("prix"),'valeur prix facture');
        $this->assertEquals(50,ATF::facture()->default_value("prix_refi"),'valeur prix_refi facture');
        ATF::affaire()->u(array("id_affaire"=>$this->id_affaire,"nature"=>"vente"));
        $this->assertEquals("cheque",ATF::facture()->default_value("mode_paiement"),'valeur mode_paiement1 facture');
        ATF::affaire()->u(array("id_affaire"=>$this->id_affaire,"nature"=>"affaire"));
        $this->assertNull(ATF::facture()->default_value("mode_paiement"),'valeur mode_paiement2 facture');
        ATF::affaire()->u(array("id_affaire"=>$this->id_affaire,"date_previsionnelle"=>2));
        $this->assertEquals(date("Y-m-d",strtotime(date("Y-m-01")." 1 month + 2 day")),ATF::facture()->default_value("date_previsionnelle"),'valeur date_previsionnelle facture');

        $id_affaire=ATF::affaire()->decryptId(ATF::affaire()->i(array("ref"=>"refTuVente","id_societe"=>$this->id_societe,"affaire"=>"AffaireTuVente","nature"=>"vente")));

        $id_facture=ATF::facture()->decryptId(ATF::facture()->i(array(
                                                                        "ref"=>"refTUVente",
                                                                        "id_societe"=>$this->id_societe,
                                                                        "date"=>date("Y-m-d",strtotime(date("Y-m-01")." 1 month")),
                                                                        "prix"=>2000,
                                                                        "tva"=>"1.196",
                                                                        "id_affaire"=>$id_affaire,
                                                                        )));

        ATF::_r('id_facture',$id_facture);
        $this->assertEquals("2000",ATF::facture()->default_value("prix"),'valeur prix vente');

    }

	//@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  
    //@author Quentin JANON <qjanon@absystech.fr>  
		public function test_select_all(){
		$this->insertFacture(true);
		ATF::societe()->u(array("id_societe"=>$this->id_societe,"code_client"=>"CODETU"));
		$this->obj->q->reset()->setCount()->addCondition("id_facture",$this->id_facture);	
        
        $attendu = array(
            array(
                "societe.code_client"=>"CODETU",
                "societe.code_client_fk"=>$this->id_societe,
                "facture.id_facture"=>$this->id_facture,
                "numRelance" => false,
                "allowRelance" => true,
                'id_relance_premiere' => ATF::relance()->cryptId($this->relance1['id']),
                'id_autreFacture' => null,
                'ref_autreFacture' => null,
                'id_relance_seconde' => ATF::relance()->cryptId($this->relance2['id']),
                'id_relance_mise_en_demeure' => ATF::relance()->cryptId($this->relance3['id'])
                
            )
        );
        $r = $this->obj->select_all();
		$this->assertEquals($attendu,$r['data'],'Problème dans les colonnes/valeurs retournées');
	}

 
    //@author Morgan Fleurquin <mfleurquin@absystech.fr>
    public function test_export_autoportes(){
        $this->initUserOnly(false);
        $q=ATF::_s("pager")->create("gsa_lol_lol");
        $q->addField("ref")->setStrict()->addCondition("id_facture",0);
        $c_fact=new fact();
        ob_start();
        $c_fact->export_autoportes(array('onglet'=>"gsa_lol_lol")); 
        ob_end_clean(); 
        if($c_fact->message){
            $this->assertTrue(false,$c_fact->message);
        }
		
		ob_start();
        $c_fact->export_autoportes(array('onglet'=>"gsa_lol_lol", "refi"=>true)); 
        ob_end_clean(); 
        if($c_fact->message){
            $this->assertTrue(false,$c_fact->message);
        }
     }

	//@author Morgan Fleurquin <mfleurquin@absystech.fr>
    public function test_export_autoportes2(){
        $this->initUserOnly(false);
        $q=ATF::_s("pager")->create("gsa_facture_facture");
        $q->addField("ref")->setStrict()->addCondition("id_facture",0);
        $c_fact=new fact();
		ob_start();
		try{
			$c_fact->export_autoportes(array('onglet'=>"gsa_facture_facture"));
		}catch (errorATF $e) {
			$erreur = $e->getMessage();
		}		
		ob_end_clean(); 		   
		$this->assertEquals($erreur , "Il faut générer les fichier Excell à partir d'un filtre personnalisé" , "L'erreur doit se déclencher si on n'est pas sur un filtre perso");
    }
      		
    //@author Morgan Fleurquin <mfleurquin@absystech.fr>
    //@author Yann GAUTHERON <ygautheron@absystech.fr>
    public function test_export_xls_autoportes(){
        ob_start();  
		
		// Simulation d'un loyer annuel
		$query = "UPDATE loyer SET frequence_loyer='an' WHERE id_loyer=15145";
		ATF::db()->query($query);
		
		//AFFAIRE -> 1110019 (pas de refi), 
		//			 1107026(CLEOFI + maj + mois), 
		//			 1206055(CLEODIS + T + maj)
		//					(A)			
		$i=0;
		foreach (array(68176,65416,60117,53799,64608,10032) as $i => $id) {
			$infos[$i] = ATF::facture()->select($id);          

			$infos[$i]["facture.id_affaire_fk"] = $infos[$i]["id_affaire"];
			$infos[$i]["facture.id_societe_fk"] = $infos[$i]["id_societe"];
			$infos[$i]["facture.id_commande_fk"] = $infos[$i]["id_commande"];
			$infos[$i]["commande.etat_fk"] = $infos[$i]["id_commande"];
			$infos[$i]["facture.id_demande_refi_fk"] = $infos[$i]["id_demande_refi"];
			$infos[$i]["facture.id_refinanceur_fk"] =$infos[$i]["id_refinanceur"];	
			ATF::commande()->u(array("id_commande"=> $infos[$i]["id_commande"] , "date_evolution" => date('Y-m-01',strtotime("+1 year"))));
		}
		
        $this->obj->export_xls_autoportes($infos,true);        
        ob_end_clean();     
		
        if($c_fact->message){
            $this->assertTrue(false,$c_fact->message);
        }       
    }

	//@author Morgan Fleurquin <mfleurquin@absystech.fr>
    public function test_export_xls_autoportes2(){        
         ob_start();  
		
		// Simulation d'un loyer annuel
		$query = "UPDATE loyer SET frequence_loyer='an' WHERE id_loyer=15145";
		ATF::db()->query($query);
		
		//AFFAIRE -> 1110019 (pas de refi), 
		//			 1107026(CLEOFI + maj + mois), 
		//			 1206055(CLEODIS + T + maj)
		//					(A)			
		$i=0;
		foreach (array(68176,65416,60117,53799,64608,10032,2) as $i => $id) {
			$infos[$i] = ATF::facture()->select($id);
			$infos[$i]["facture.id_affaire_fk"] = $infos[$i]["id_affaire"];
			$infos[$i]["facture.id_societe_fk"] = $infos[$i]["id_societe"];
			$infos[$i]["facture.id_commande_fk"] = $infos[$i]["id_commande"];
			$infos[$i]["commande.etat_fk"] = $infos[$i]["id_commande"];
			$infos[$i]["facture.id_demande_refi_fk"] = $infos[$i]["id_demande_refi"];
			$infos[$i]["facture.id_refinanceur_fk"] =$infos[$i]["id_refinanceur"];
			if($id != 2){
				ATF::commande()->u(array("id_commande"=> $infos[$i]["id_commande"] , "date_evolution" => date('Y-m-01',strtotime("+1 year"))));
			}
					
		}		
        $this->obj->export_xls_autoportes($infos);        
        ob_end_clean();     
		
        if($c_fact->message){
            $this->assertTrue(false,$c_fact->message);
        }       
    }	
    //@author Quentin JANON <qjanon@absystech.fr>  
    public function test_getAllForRelance(){
        $this->insertFacture(true);
        
        // Insertion de quelques factures en plus... qui reviendront lors de l'appel de la fonction 
        $facture = ATF::facture()->i(array(
            "ref"=>"refTU3",
            "type_facture"=>"facture",
            "id_societe"=>$this->id_societe,
            "prix"=>1200,
            "date"=>date("Y-m-d",strtotime(date("Y-m-01")." 1 month")),
            "date_previsionnelle"=>date("Y-m-d",strtotime(date("Y-m-01")." 2 month")),
            "date_paiement"=>date("Y-m-d",strtotime(date("Y-m-01")." + 3 month")),
            "date_relance"=>date("Y-m-d",strtotime(date("Y-m-01")." + 4 month")),
            "date_periode_debut"=>date("Y-m-01"),
            "date_periode_fin"=>date("Y-m-d",strtotime(date("Y-m-01")." + 1 month")),
            "tva"=>"1.196",
            "id_user"=>$this->id_user,
            "id_commande"=>$this->id_commande,
            "id_affaire"=>$this->id_affaire,
            "id_demande_refi"=>$this->id_demande_refi
         ));
         
         $infos["id_facture"] = $this->id_facture;
         $r = $this->obj->getAllForRelance($infos);
         $this->assertEquals(1,count($r),"Il devrait y avoir une seul facture dans le retour");
         $this->assertEquals("refTU3",$r[0]['reference'],"Problème de REF dans le retour");
         $this->assertEquals($facture,$r[0]['id'],"Problème d'ID dans le retour");
    }
    
    
   public function test_contientFactureRejetee(){
   		 $this->insertFacture();
   		 
		$mod["id_facture"]=$this->id_facture;
        $mod["key"]="rejet";
        $mod["value"]="contestation_debiteur";        
        $this->obj->updateEnumRejet($mod);        
		$mod["id_facture"]=$this->id_facture2;
        $mod["key"]="rejet";
        $mod["value"]="contestation_debiteur";        
        $this->obj->updateEnumRejet($mod);
		
		$this->assertEquals(1 , $this->obj->contientFactureRejetee($this->id_commande , ATF::facture()->cryptId($this->id_facture)), "Damned");
	
   } 


	public function test_libreToNormale(){
		$id_aff=ATF::affaire()->i(array("ref"=>"ref lol test","id_societe"=>$this->id_soc,"affaire"=>"aff lol test"));
		$id_facturation = ATF::facturation()->i(array("id_affaire" => $id_aff,"montant"=> 10, "frais_de_gestion"=> 10,"id_societe"=>$this->id_soc, "id_facture" => NULL, "date_periode_debut" => "2012-01-01", "date_periode_fin"=> "2012-02-01"));
		$factureLibre = ATF::facture()->i(array("ref"=>"ref lol test","id_societe"=>$this->id_soc,"prix"=>45,"date"=>"2011-12-01","tva"=>"19.6","id_affaire"=>$id_aff, "type_facture" => "libre" , "type_libre" => "contentieux", "date_periode_debut" => "2012-01-01" , "date_periode_fin" => "2012-02-01"));
		
		 try {
             $this->obj->libreToNormale(array("id_facture" => $this->obj->cryptId($factureLibre)));
        } catch (errorATF $e) {
            $error = $e->getMessage();
        }
        $this->assertEquals("Il n'est pas possible de passer une facture libre contentieux en facture normale",$error,"LibreToNormale 1");
		
		ATF::facture()->u(array("id_facture" => $factureLibre , "type_libre" => "normale", "date_periode_debut" => "2013-01-01" , "date_periode_fin" => "2013-02-01"));
        $this->obj->libreToNormale(array("id_facture" => $this->obj->cryptId($factureLibre)));
		ATF::facturation()->q->reset()->where("id_facture" , $factureLibre);
        $facturation = ATF::facturation()->select_row();

		$this->assertEquals("2013-01-01" , $facturation["date_periode_debut"] ,"LibreToNormale 2.1");
        $this->assertEquals("2013-02-01" , $facturation["date_periode_fin"] ,"LibreToNormale 2.2");
         $this->assertEquals("45.00" , $facturation["montant"] ,"LibreToNormale 2.3");
		
		ATF::facturation()->u(array("id_facturation" => $id_facturation , "id_facture" => $factureLibre));
		ATF::facture()->u(array("id_facture" => $factureLibre , "date_periode_debut" => "2012-01-01" , "date_periode_fin" => "2012-02-01"));
		try {
             $this->obj->libreToNormale(array("id_facture" => $this->obj->cryptId($factureLibre)));
        } catch (errorATF $e) {
            $error = $e->getMessage();
        }
		$this->assertEquals("Il y a déja une facture pour la période du 2012-01-01 au 2012-02-01",$error,"LibreToNormale 3");
		
		ATF::facturation()->u(array("id_facturation" => $id_facturation , "id_facture" => NULL));
		$this->obj->libreToNormale(array("id_facture" => $this->obj->cryptId($factureLibre)));
		
		$this->assertEquals("facture",ATF::facture()->select($factureLibre , "type_facture"),"LibreToNormale 4");
		$this->assertEquals($factureLibre,ATF::facturation()->select($id_facturation , "id_facture"),"LibreToNormale 5");
		
		ATF::suivi()->q->reset()->where("id_affaire" , $id_aff);
		$suivis = ATF::suivi()->select_row();
		$this->assertNotNull($suivis,'Pas de suivis ???');
	}


    public function test_export_xls_cegid(){
        ATF::facture()->q->reset()->setStrict()->where("facture.date","2014-12-12","AND",false,">=")->where("facture.date","2014-12-20","AND",false,"<=");
        $infos = ATF::facture()->sa();

        foreach ($infos as $key => $value) {
            $infos[$key]["facture.id_affaire_fk"] = $value["id_affaire"];
            $infos[$key]["facture.id_facture_fk"] = $value["id_facture"];
            $infos[$key]["facture.id_commande_fk"] = $value["id_commande"];
            $infos[$key]["facture.id_societe_fk"] = $value["id_societe"];
        }        
        ob_start();
        $this->obj->export_xls_cegid($infos , true);
         //récupération des infos
        $fichier=ob_get_contents();     
        ob_end_clean();

        $this->assertNotNull($fichier, "L'export ne s'est pas bien passé??");
    }

    //@author Morgan Fleurquin <mfleurquin@absystech.fr>
    public function test_eexport_cegid(){        
        ATF::facture()->q->reset()->setStrict()->where("facture.date","2014-12-12","AND",false,">=")->where("facture.date","2014-12-20","AND",false,"<=");
        $infos = ATF::facture()->sa();

        foreach ($infos as $key => $value) {
            $infos[$key]["facture.id_affaire_fk"] = $value["id_affaire"];
            $infos[$key]["facture.id_facture_fk"] = $value["id_facture"];
            $infos[$key]["facture.id_commande_fk"] = $value["id_commande"];
            $infos[$key]["facture.id_societe_fk"] = $value["id_societe"];
        }        
        ob_start();
        $this->obj->export_cegid(array('onglet'=>"gsa_facture_facture" , "tu"=>true));
         //récupération des infos
        $fichier=ob_get_contents();     
        ob_end_clean();

        $this->assertNotNull($fichier, "L'export ne s'est pas bien passé??");    
    }


    // @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    private function beginTransaction($codename){       
        ATF::db()->select_db("extranet_v3_".$codename);
        ATF::$codename = $codename;
        ATF::db()->begin_transaction(true);     
    }

    // @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    private function RollBackTransaction($codename){    
        ATF::db()->rollback_transaction(true);
        ATF::$codename = "cleodis";
        ATF::db()->select_db("extranet_v3_cleodis");
    }


};

class objet_excel {	
	public function __construct(){
		$this->sheet=new objet_sheet();
	}
	public function write($col,$valeur) {
		$this->$col=$valeur;
	}
}
class objet_sheet {
	public function getColumnDimension($col){
		return $this;
	}
	public function setAutoSize($bool){
		$this->size=true;
	}
}
class fact extends facture_cleodis {
	public function export_xls_special($infos){
		if(count($infos)!=1){
			$this->message="Ne renvoie pas le bon nombre d'information";
		}elseif(count($infos[0])<15){
			$this->message="N'a pas ajouté tous les champs";
		}
	}
	
}


?>