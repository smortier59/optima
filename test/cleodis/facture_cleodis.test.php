<?
/**
* Classe de test sur le module societe_cleodis
*/
class facture_cleodis_test extends ATF_PHPUnit_Framework_TestCase {


    public function initDevis_Commande(){
        $this->devis_1 = array (
                          'extAction' => 'devis',
                          'extMethod' => 'insert',
                          'preview' => 'true',
                          'label_devis' =>
                          array (
                            'id_filiale' => 'CLEODIS',
                            'id_opportunite' => 'Aucun(e)',
                            'id_societe' => 'FINORPA',
                            'id_contact' => 'M Philippe MOONS',
                            'AR_societe' => 'FINORPA',
                          ),
                          'devis' =>
                          array (
                            'id_filiale' => '246',
                            'devis' => 'TU',
                            'tva' => '1.200',
                            'date_accord' => '08-02-2011',
                            'id_opportunite' => '',
                            'id_societe' => 5391,
                            'type_contrat' => 'lld',
                            'validite' => '23-02-2011',
                            'id_contact' => '5753',
                            'loyers' => '0.00',
                            'frais_de_gestion_unique' => '0.00',
                            'assurance_unique' => '0.00',
                            'AR_societe' => '',
                            'marge' => '99.96',
                            'marge_absolue' => '8 021.00',
                            'prix' => '8 024.00',
                            'prix_achat' => '3.00',
                            'email' => 'pmoons@finorpa.fr',
                            'emailTexte' => '<br>',
                            'emailCopie' => 'jerome.loison@cleodis.fr',
                            'filestoattach' =>
                            array (
                              'fichier_joint' => '',
                            ),
                          ),
                          'avenant' => '',
                          'AR' => '',
                          'loyer' =>
                          array (
                            'frequence_loyer' => 'm',
                          ),
                          'values_devis' =>
                          array (
                            'loyer' => '[{"loyer__dot__loyer":"233","loyer__dot__duree":"34","loyer__dot__assurance":"2","loyer__dot__frais_de_gestion":"1","loyer__dot__frequence_loyer":"mois","loyer__dot__loyer_total":8024}]',
                            'produits' => '[{"devis_ligne__dot__produit":"Zywall 5 - dispositif de sécurité","devis_ligne__dot__quantite":"3","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"ZYX-FW","devis_ligne__dot__prix_achat":"1","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"<span class=\"searchSelectionFound\">D</span>JP SERVICE","devis_ligne__dot__id_produit_fk":"1175","devis_ligne__dot__id_fournisseur_fk":"1583"}]',
                          ),
                        );

        $this->devis_2 = array (
                          'extAction' => 'devis',
                          'extMethod' => 'insert',
                          'preview' => 'true',
                          'label_devis' =>
                          array (
                            'id_filiale' => 'CLEODIS',
                            'id_opportunite' => 'Aucun(e)',
                            'id_societe' => 'FINORPA',
                            'id_contact' => 'M Philippe MOONS',
                            'AR_societe' => 'FINORPA',
                          ),
                          'devis' =>
                          array (
                            'id_filiale' => '246',
                            'devis' => 'TU',
                            'tva' => '1.200',
                            'date_accord' => '08-02-2011',
                            'id_opportunite' => '',
                            'id_societe' => 5391,
                            'type_contrat' => 'lld',
                            'validite' => '23-02-2011',
                            'id_contact' => '5753',
                            'loyers' => '0.00',
                            'frais_de_gestion_unique' => '0.00',
                            'assurance_unique' => '0.00',
                            'AR_societe' => '',
                            'marge' => '99.96',
                            'marge_absolue' => '8 021.00',
                            'prix' => '8 024.00',
                            'prix_achat' => '3.00',
                            'email' => 'pmoons@finorpa.fr',
                            'emailTexte' => '<br>',
                            'emailCopie' => 'jerome.loison@cleodis.fr',
                            'filestoattach' =>
                            array (
                              'fichier_joint' => '',
                            ),
                          ),
                          'avenant' => '',
                          'AR' => '',
                          'loyer' =>
                          array (
                            'frequence_loyer' => 'm',
                          ),
                          'values_devis' =>
                          array (
                            'loyer' => '[{"loyer__dot__loyer":"233","loyer__dot__duree":"34","loyer__dot__assurance":"2","loyer__dot__frais_de_gestion":"1","loyer__dot__frequence_loyer":"trimestre","loyer__dot__loyer_total":8024}]',
                            'produits' => '[{"devis_ligne__dot__produit":"Zywall 5 - dispositif de sécurité","devis_ligne__dot__quantite":"3","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"ZYX-FW","devis_ligne__dot__prix_achat":"1","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"<span class=\"searchSelectionFound\">D</span>JP SERVICE","devis_ligne__dot__id_produit_fk":"1175","devis_ligne__dot__id_fournisseur_fk":"1583"}]',
                          ),
                        );

        $this->devis_3 = array (
                          'extAction' => 'devis',
                          'extMethod' => 'insert',
                          'preview' => 'true',
                          'label_devis' =>
                          array (
                            'id_filiale' => 'CLEODIS',
                            'id_opportunite' => 'Aucun(e)',
                            'id_societe' => 'FINORPA',
                            'id_contact' => 'M Philippe MOONS',
                            'AR_societe' => 'FINORPA',
                          ),
                          'devis' =>
                          array (
                            'id_filiale' => '246',
                            'devis' => 'TU',
                            'tva' => '1.200',
                            'date_accord' => '08-02-2011',
                            'id_opportunite' => '',
                            'id_societe' => 5391,
                            'type_contrat' => 'lld',
                            'validite' => '23-02-2011',
                            'id_contact' => '5753',
                            'loyers' => '0.00',
                            'frais_de_gestion_unique' => '0.00',
                            'assurance_unique' => '0.00',
                            'AR_societe' => '',
                            'marge' => '99.96',
                            'marge_absolue' => '8 021.00',
                            'prix' => '8 024.00',
                            'prix_achat' => '3.00',
                            'email' => 'pmoons@finorpa.fr',
                            'emailTexte' => '<br>',
                            'emailCopie' => 'jerome.loison@cleodis.fr',
                            'filestoattach' =>
                            array (
                              'fichier_joint' => '',
                            ),
                          ),
                          'avenant' => '',
                          'AR' => '',
                          'loyer' =>
                          array (
                            'frequence_loyer' => 'm',
                          ),
                          'values_devis' =>
                          array (
                            'loyer' => '[{"loyer__dot__loyer":"233","loyer__dot__duree":"34","loyer__dot__assurance":"2","loyer__dot__frais_de_gestion":"1","loyer__dot__frequence_loyer":"semestre","loyer__dot__loyer_total":8024}]',
                            'produits' => '[{"devis_ligne__dot__produit":"Zywall 5 - dispositif de sécurité","devis_ligne__dot__quantite":"3","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"ZYX-FW","devis_ligne__dot__prix_achat":"1","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"<span class=\"searchSelectionFound\">D</span>JP SERVICE","devis_ligne__dot__id_produit_fk":"1175","devis_ligne__dot__id_fournisseur_fk":"1583"}]',
                          ),
                        );

        $this->devis_4 = array (
                          'extAction' => 'devis',
                          'extMethod' => 'insert',
                          'preview' => 'true',
                          'label_devis' =>
                          array (
                            'id_filiale' => 'CLEODIS',
                            'id_opportunite' => 'Aucun(e)',
                            'id_societe' => 'FINORPA',
                            'id_contact' => 'M Philippe MOONS',
                            'AR_societe' => 'FINORPA',
                          ),
                          'devis' =>
                          array (
                            'id_filiale' => '246',
                            'devis' => 'TU',
                            'tva' => '1.200',
                            'date_accord' => '08-02-2011',
                            'id_opportunite' => '',
                            'id_societe' => 5391,
                            'type_contrat' => 'lld',
                            'validite' => '23-02-2011',
                            'id_contact' => '5753',
                            'loyers' => '0.00',
                            'frais_de_gestion_unique' => '0.00',
                            'assurance_unique' => '0.00',
                            'AR_societe' => '',
                            'marge' => '99.96',
                            'marge_absolue' => '8 021.00',
                            'prix' => '8 024.00',
                            'prix_achat' => '3.00',
                            'email' => 'pmoons@finorpa.fr',
                            'emailTexte' => '<br>',
                            'emailCopie' => 'jerome.loison@cleodis.fr',
                            'filestoattach' =>
                            array (
                              'fichier_joint' => '',
                            ),
                          ),
                          'avenant' => '',
                          'AR' => '',
                          'loyer' =>
                          array (
                            'frequence_loyer' => 'an',
                          ),
                          'values_devis' =>
                          array (
                            'loyer' => '[{"loyer__dot__loyer":"233","loyer__dot__duree":"34","loyer__dot__assurance":"2","loyer__dot__frais_de_gestion":"1","loyer__dot__frequence_loyer":"an","loyer__dot__loyer_total":8024}]',
                            'produits' => '[{"devis_ligne__dot__produit":"Zywall 5 - dispositif de sécurité","devis_ligne__dot__quantite":"3","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"ZYX-FW","devis_ligne__dot__prix_achat":"1","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"<span class=\"searchSelectionFound\">D</span>JP SERVICE","devis_ligne__dot__id_produit_fk":"1175","devis_ligne__dot__id_fournisseur_fk":"1583"}]',
                          ),
                        );



        $this->commande_1 = array (
                                  'extAction' => 'commande',
                                  'extMethod' => 'insert',
                                  'preview' => 'true',
                                  'commande' =>
                                  array (
                                    'commande' => 'TU',
                                    'type' => 'prelevement',
                                    'id_societe' => '5391',
                                    'date' => '10-05-2011',
                                    'id_affaire' => '6002',
                                    'clause_logicielle' => 'non',
                                    'prix' => '8 024.00',
                                    'prix_achat' => '3.00',
                                    'marge' => '99.96',
                                    'marge_absolue' => '8 021.00',
                                    'email' => 'pmoons@finorpa.fr',
                                    'emailTexte' => '',
                                    'emailCopie' => 'jerome.loison@cleodis.fr',
                                    'id_devis' => '5929',
                                    '__redirect' => 'devis',
                                  ),
                                  'values_commande' =>
                                  array (
                                    'loyer' => '[{"loyer__dot__loyer":"233.00","loyer__dot__duree":"34","loyer__dot__assurance":"2.00","loyer__dot__frais_de_gestion":"1.00","loyer__dot__frequence_loyer":"mois","loyer__dot__loyer_total":8024}]',
                                    'produits_repris' => '',
                                    'produits' => '[{"commande_ligne__dot__produit":"Zywall 5 - dispositif de sécurité","commande_ligne__dot__quantite":"3","commande_ligne__dot__ref":"ZYX-FW","commande_ligne__dot__id_fournisseur":"DJP SERVICE","commande_ligne__dot__id_fournisseur_fk":"1583","commande_ligne__dot__prix_achat":"1.00","commande_ligne__dot__id_produit":"Zywall 5 - dispositif de sécurité","commande_ligne__dot__id_produit_fk":"1175"}]',
                                    'produits_non_visible' => '',
                                  ),
                                );

        $this->commande_2 = array (
                                  'extAction' => 'commande',
                                  'extMethod' => 'insert',
                                  'preview' => 'true',
                                  'commande' =>
                                  array (
                                    'commande' => 'TU',
                                    'type' => 'prelevement',
                                    'id_societe' => '5391',
                                    'date' => '10-05-2011',
                                    'id_affaire' => '6002',
                                    'clause_logicielle' => 'non',
                                    'prix' => '8 024.00',
                                    'prix_achat' => '3.00',
                                    'marge' => '99.96',
                                    'marge_absolue' => '8 021.00',
                                    'email' => 'pmoons@finorpa.fr',
                                    'emailTexte' => '',
                                    'emailCopie' => 'jerome.loison@cleodis.fr',
                                    'id_devis' => '5929',
                                    '__redirect' => 'devis',
                                  ),
                                  'values_commande' =>
                                  array (
                                    'loyer' => '[{"loyer__dot__loyer":"233.00","loyer__dot__duree":"34","loyer__dot__assurance":"2.00","loyer__dot__frais_de_gestion":"1.00","loyer__dot__frequence_loyer":"trimestre","loyer__dot__loyer_total":8024}]',
                                    'produits_repris' => '',
                                    'produits' => '[{"commande_ligne__dot__produit":"Zywall 5 - dispositif de sécurité","commande_ligne__dot__quantite":"3","commande_ligne__dot__ref":"ZYX-FW","commande_ligne__dot__id_fournisseur":"DJP SERVICE","commande_ligne__dot__id_fournisseur_fk":"1583","commande_ligne__dot__prix_achat":"1.00","commande_ligne__dot__id_produit":"Zywall 5 - dispositif de sécurité","commande_ligne__dot__id_produit_fk":"1175"}]',
                                    'produits_non_visible' => '',
                                  ),
                                );

        $this->commande_3 = array (
                                  'extAction' => 'commande',
                                  'extMethod' => 'insert',
                                  'preview' => 'true',
                                  'commande' =>
                                  array (
                                    'commande' => 'TU',
                                    'type' => 'prelevement',
                                    'id_societe' => '5391',
                                    'date' => '10-05-2011',
                                    'id_affaire' => '6002',
                                    'clause_logicielle' => 'non',
                                    'prix' => '8 024.00',
                                    'prix_achat' => '3.00',
                                    'marge' => '99.96',
                                    'marge_absolue' => '8 021.00',
                                    'email' => 'pmoons@finorpa.fr',
                                    'emailTexte' => '',
                                    'emailCopie' => 'jerome.loison@cleodis.fr',
                                    'id_devis' => '5929',
                                    '__redirect' => 'devis',
                                  ),
                                  'values_commande' =>
                                  array (
                                    'loyer' => '[{"loyer__dot__loyer":"233.00","loyer__dot__duree":"34","loyer__dot__assurance":"2.00","loyer__dot__frais_de_gestion":"1.00","loyer__dot__frequence_loyer":"semestre","loyer__dot__loyer_total":8024}]',
                                    'produits_repris' => '',
                                    'produits' => '[{"commande_ligne__dot__produit":"Zywall 5 - dispositif de sécurité","commande_ligne__dot__quantite":"3","commande_ligne__dot__ref":"ZYX-FW","commande_ligne__dot__id_fournisseur":"DJP SERVICE","commande_ligne__dot__id_fournisseur_fk":"1583","commande_ligne__dot__prix_achat":"1.00","commande_ligne__dot__id_produit":"Zywall 5 - dispositif de sécurité","commande_ligne__dot__id_produit_fk":"1175"}]',
                                    'produits_non_visible' => '',
                                  ),
                                );

        $this->commande_4 = array (
                                  'extAction' => 'commande',
                                  'extMethod' => 'insert',
                                  'preview' => 'true',
                                  'commande' =>
                                  array (
                                    'commande' => 'TU',
                                    'type' => 'prelevement',
                                    'id_societe' => '5391',
                                    'date' => '10-05-2011',
                                    'id_affaire' => '6002',
                                    'clause_logicielle' => 'non',
                                    'prix' => '8 024.00',
                                    'prix_achat' => '3.00',
                                    'marge' => '99.96',
                                    'marge_absolue' => '8 021.00',
                                    'email' => 'pmoons@finorpa.fr',
                                    'emailTexte' => '',
                                    'emailCopie' => 'jerome.loison@cleodis.fr',
                                    'id_devis' => '5929',
                                    '__redirect' => 'devis',
                                  ),
                                  'values_commande' =>
                                  array (
                                    'loyer' => '[{"loyer__dot__loyer":"233.00","loyer__dot__duree":"34","loyer__dot__assurance":"2.00","loyer__dot__frais_de_gestion":"1.00","loyer__dot__frequence_loyer":"an","loyer__dot__loyer_total":8024}]',
                                    'produits_repris' => '',
                                    'produits' => '[{"commande_ligne__dot__produit":"Zywall 5 - dispositif de sécurité","commande_ligne__dot__quantite":"3","commande_ligne__dot__ref":"ZYX-FW","commande_ligne__dot__id_fournisseur":"DJP SERVICE","commande_ligne__dot__id_fournisseur_fk":"1583","commande_ligne__dot__prix_achat":"1.00","commande_ligne__dot__id_produit":"Zywall 5 - dispositif de sécurité","commande_ligne__dot__id_produit_fk":"1175"}]',
                                    'produits_non_visible' => '',
                                  ),
                                );


    }


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







    public function test_createFactureProrata(){
        $this->initDevis_Commande();

        //Facture ProRata en mois
        $devis1 = ATF::devis()->insert($this->devis_1);
        $devis1 = ATF::devis()->decryptId($devis1);
        $affaire1 = ATF::devis()->select($devis1, "id_affaire");
        $this->commande_1["commande"]["id_devis"] = $devis1;
        $commande1 = ATF::commande()->insert($this->commande_1);
        $commande1 = ATF::commande()->decryptId($commande1)

        //Facture ProRata en trimestre



        //Facture ProRata en semestre


        //Facture ProRata en année


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