<?
class bon_de_commande_cleodis_test extends ATF_PHPUnit_Framework_TestCase {

	/** Méthode pré-test, exécute avant chaque test unitaire
	* besoin d'un user pour les traduction
	*/
	public function setUp() {
		$this->initUser();
//		echo "(".ATF::db()->numberTransaction()."|";
	}

	/** Méthode post-test, exécute après chaque test unitaire*/
	public function tearDown(){
//		echo ATF::db()->numberTransaction().")";
		ATF::db()->rollback_transaction(true);
	}

	public function insertBdc(){

		$this->id_societe=1397;

		$this->devis["devis"]=array(
								 "id_societe" => $this->id_societe
								,"id_filiale" => 246
								,"date" => date("Y-m-d")
								,"id_contact" => 1401
								,"devis" => "Tu Bdc"
								,"type_contrat" => "lld"
								,"validite" => date("Y-m-d",strtotime(date("Y-m-d")."- 15 day"))
								,"id_opportunite" =>NULL
								,"tva" => "1.196"
								,"prix" => "14 000.00"
								,"prix_achat" => "4 641.00"
								,"marge" => "66.85"
								,"marge_absolue" => "9 359.00"
        );

		$this->devis["values_devis"] = array(
             "loyer" => '[{"loyer__dot__loyer":"1000","loyer__dot__duree":"14","loyer__dot__assurance":"","loyer__dot__frais_de_gestion":"","loyer__dot__frequence_loyer":"mois","loyer__dot__loyer_total":14000}]'
            ,"produits" => '[{"devis_ligne__dot__produit":"Optiplex GX520 TFT 19","devis_ligne__dot__quantite":"1","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"DEL-WRK-OPTGX520-19","devis_ligne__dot__prix_achat":"10","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"DELL|#ref=164a1c62808dc1a3af6f7d99051db73b","devis_ligne__dot__visibilite_prix":"visible","devis_ligne__dot__id_produit_fk":"9","devis_ligne__dot__id_fournisseur_fk":"1351"},{"devis_ligne__dot__produit":"XSERIES 226","devis_ligne__dot__quantite":"1","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"O2-SRV-226-001","devis_ligne__dot__prix_achat":"3113.00","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"AUDIOPTIC TRADE SERVICES|#ref=c0529cb381c6dcf43fc554b910ce02e9","devis_ligne__dot__visibilite_prix":"visible","devis_ligne__dot__id_produit_fk":"5","devis_ligne__dot__id_fournisseur_fk":"1358"},{"devis_ligne__dot__produit":"Optiplex GX520 TFT 17","devis_ligne__dot__quantite":"2","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"DEL-WRK-OPTGX520-17","devis_ligne__dot__prix_achat":"759.00","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"DELL|#ref=164a1c62808dc1a3af6f7d99051db73b","devis_ligne__dot__visibilite_prix":"visible","devis_ligne__dot__id_produit_fk":"8","devis_ligne__dot__id_fournisseur_fk":"1351"}]'
        );

		$this->id_devis=classes::decryptId(ATF::devis()->insert($this->devis));

		$this->devis_select=ATF::devis()->select($this->id_devis);
		ATF::devis_ligne()->q->reset()->addCondition("id_devis",$this->id_devis);
		$this->devis_ligne=ATF::devis_ligne()->sa();

		$this->commande["commande"]=array(
								 "commande" => $this->devis_select["devis"]
								,"type" => "prelevement"
								,"id_societe" => $this->id_societe
								,"date" => date("Y-m-d")
								,"id_affaire" => $this->devis_select["id_affaire"]
								,"clause_logicielle" => "non"
								,"prix" => "14 000.00"
								,"prix_achat" =>"4 641.00"
								,"marge" => "66.85"
								,"marge_absolue" => "9 359.00"
								,"id_devis" => $this->devis_select["id_devis"]
        );

		$this->commande["values_commande"] = array(
			 "loyer" => '[{"loyer__dot__loyer":"1000.00","loyer__dot__duree":"14","loyer__dot__assurance":null,"loyer__dot__frais_de_gestion":null,"loyer__dot__frequence_loyer":"mois","loyer__dot__loyer_total":14000}]'
			,"produits" => '[{"commande_ligne__dot__produit":"Optiplex GX520 TFT 19","commande_ligne__dot__serial":"BBPZB2J","commande_ligne__dot__quantite":"1","commande_ligne__dot__ref":"DEL-WRK-OPTGX520-19","commande_ligne__dot__id_fournisseur":"DELL","commande_ligne__dot__id_fournisseur_fk":"1351","commande_ligne__dot__prix_achat":"10.00","commande_ligne__dot__id_produit":"Optiplex GX520 TFT 19","commande_ligne__dot__id_produit_fk":"9","commande_ligne__dot__id_commande_ligne":"'.$devis_ligne[0]["id_devis_ligne"].'"},{"commande_ligne__dot__produit":"XSERIES 226","commande_ligne__dot__serial":"BBPZB2J2","commande_ligne__dot__quantite":"1","commande_ligne__dot__ref":"O2-SRV-226-001","commande_ligne__dot__id_fournisseur":"AUDIOPTIC TRADE SERVICES","commande_ligne__dot__id_fournisseur_fk":"1358","commande_ligne__dot__prix_achat":"3113.00","commande_ligne__dot__id_produit":"XSERIES 226","commande_ligne__dot__id_produit_fk":"5","commande_ligne__dot__id_commande_ligne":"'.$devis_ligne[1]["id_devis_ligne"].'"},{"commande_ligne__dot__produit":"Optiplex GX520 TFT 17","commande_ligne__dot__serial":"BBPZB2J3","commande_ligne__dot__quantite":"2","commande_ligne__dot__ref":"DEL-WRK-OPTGX520-17","commande_ligne__dot__id_fournisseur":"DELL","commande_ligne__dot__id_fournisseur_fk":"1351","commande_ligne__dot__prix_achat":"759.00","commande_ligne__dot__id_produit":"Optiplex GX520 TFT 17","commande_ligne__dot__id_produit_fk":"8","commande_ligne__dot__id_commande_ligne":"'.$devis_ligne[2]["id_devis_ligne"].'"}]'
		);

		$this->id_commande=classes::decryptId(ATF::commande()->insert($this->commande));
        ATF::$msg->getNotices();
		$this->commande_select=ATF::commande()->select($this->id_commande);
		ATF::commande_ligne()->q->reset()->addCondition("id_commande",$this->id_commande);
		$this->commande_ligne=ATF::commande_ligne()->sa();
	}

    /*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
    public function test_updateDate(){
        $this->insertBdc();

        $bon_de_commande["bon_de_commande"]=array(
                                                     "id_societe" => $this->id_societe
                                                    ,"id_commande" => $this->id_commande
                                                    ,"id_fournisseur" => 1351
                                                    ,"id_affaire" => $this->devis_select["id_affaire"]
                                                    ,"bon_de_commande" => $this->devis_select["devis"]
                                                    ,"id_contact" => 5333
                                                    ,"prix" => "10.00"
                                                    ,"tva" =>"1.196"
                                                    ,"etat" => "envoyee"
                                                    ,"payee" => "non"
                                                    ,"date" => date("Y-m-d")
                                                    ,"destinataire" => "AXXES"
                                                    ,"adresse" => "26 rue de La Vilette - Part Dieu"
                                                    ,"adresse_2" => $this->devis_select["id_devis"]
                                                    ,"adresse_3" => $this->devis_select["id_devis"]
                                                    ,"cp" => "69003"
                                                    ,"ville" => "LYON"
                                                    ,"id_pays" => "FR"
                                                    ,"id_fournisseur_intermediaire" => NULL
                                                    ,"livraison_destinataire" => NULL
                                                    ,"livraison_adresse" => NULL
                                                    ,"livraison_cp" => NULL
                                                    ,"livraison_ville" => NULL
                                                    ,"email" => "debug@absystech.fr"
                                                    ,"emailTexte" => "TU<br>"
                                                    ,"emailCopie" => "debug@absystech.fr"
                                                    ,"filestoattach" =>  array("fichier_joint" =>NULL )
                                                );

        $bon_de_commande["commandes"]="xnode-".$this->id_commande.",".$this->commande_ligne[1]["id_commande_ligne"].",".$this->commande_ligne[2]["id_commande_ligne"];

        $refresh = array();
        $id_bon_de_commande=classes::decryptId(ATF::bon_de_commande()->insert($bon_de_commande,$this->s,NULL,$refresh));


        $this->obj->updateDate(array("value"=>date("Y-m-d"), "key"=>"date_livraison_estime",  "id_bon_de_commande"=> $id_bon_de_commande ));

        $this->obj->updateDate(array("value"=>date("Y-m-d"), "key"=>"date_installation_prevue",  "id_bon_de_commande"=> $id_bon_de_commande ));

        $this->obj->updateDate(array("value"=>date("Y-m-d"), "key"=>"date_livraison_prevue",  "id_bon_de_commande"=> $id_bon_de_commande ));


        ATF::$msg->getNotices();

    }



    /*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
    public function test_insert(){

        $this->insertBdc();

        $bon_de_commande["bon_de_commande"]=array(
                                 "id_societe" => $this->id_societe
                                ,"id_commande" => $this->id_commande
                                ,"id_fournisseur" => 1351
                                ,"id_affaire" => $this->devis_select["id_affaire"]
                                ,"bon_de_commande" => $this->devis_select["devis"]
                                ,"id_contact" => 5333
                                ,"prix" => "10.00"
                                ,"tva" =>"1.196"
                                ,"etat" => "envoyee"
                                ,"payee" => "non"
                                ,"date" => date("Y-m-d")
                                ,"destinataire" => "AXXES"
                                ,"adresse" => "26 rue de La Vilette - Part Dieu"
                                ,"adresse_2" => $this->devis_select["id_devis"]
                                ,"adresse_3" => $this->devis_select["id_devis"]
                                ,"cp" => "69003"
                                ,"ville" => "LYON"
                                ,"id_pays" => "FR"
                                ,"id_fournisseur_intermediaire" => NULL
                                ,"livraison_destinataire" => NULL
                                ,"livraison_adresse" => NULL
                                ,"livraison_cp" => NULL
                                ,"livraison_ville" => NULL
                                ,"email" => "debug@absystech.fr"
                                ,"emailTexte" => "TU<br>"
                                ,"emailCopie" => "debug@absystech.fr"
                                ,"filestoattach" =>  array(
                                        "fichier_joint" =>NULL
                                    )
        );

        $bon_de_commande["panel_courriel-checkbox"]="on";

        try {
            classes::decryptId(ATF::bon_de_commande()->insert($bon_de_commande));
        } catch (errorATF $e) {
            $error = $e->getCode();
        }
        $this->assertEquals(875,$error,'Erreur bon de commande sans commandes');

        $bon_de_commande["commandes"]="xnode-".$this->id_commande.",".$this->commande_ligne[0]["id_commande_ligne"]."";

        $refresh = array();
        $id_bon_de_commande=classes::decryptId(ATF::bon_de_commande()->insert($bon_de_commande,$this->s,NULL,$refresh));
        $this->assertNotNull($id_bon_de_commande,"Le bon de commande n'a pas été créé");

        $bon_de_commande_select=ATF::bon_de_commande()->select($id_bon_de_commande);
        $this->assertEquals("FDELL09-".$this->commande_select["ref"]."-1",$bon_de_commande_select["ref"],"La ref du bon de commande n'a pas été créé");
        ATF::bon_de_commande_ligne()->q->reset()->addCondition("id_bon_de_commande",$id_bon_de_commande);
        $bon_de_commande_ligne=ATF::bon_de_commande_ligne()->sa();

        $this->assertEquals(array(
                                    "id_bon_de_commande_ligne"=>$bon_de_commande_ligne[0]["id_bon_de_commande_ligne"],
                                    "id_bon_de_commande"=>$id_bon_de_commande,
                                    "ref"=>"DEL-WRK-OPTGX520-19",
                                    "produit"=>"Optiplex GX520 TFT 19",
                                    "quantite"=>1,
                                    "prix"=>"10.000",
                                    "id_commande_ligne"=>$this->commande_ligne[0]["id_commande_ligne"],
                                    "prix_ttc" => "0.00"
                                )
            ,$bon_de_commande_ligne[0]
            ,"Erreur sur la création d'une ligne de commande"
        );

        ATF::facture_non_parvenue()->q->reset()->addCondition("id_bon_de_commande",$id_bon_de_commande);
        $facture_non_parvenue=ATF::facture_non_parvenue()->sa();

        $this->assertEquals(array(
                                    "id_facture_non_parvenue"=>$facture_non_parvenue[0]["id_facture_non_parvenue"],
                                    "ref"=>"FDELL09-".$this->commande_select["ref"]."-1-FNP",
                                    "id_facture_fournisseur"=>NULL,
                                    "prix"=>"10.00",
                                    "tva"=>"1.196",
                                    "etat"=>"impayee",
                                    "id_affaire"=>$this->devis_select["id_affaire"],
                                    "id_bon_de_commande"=>$id_bon_de_commande,
                                    "date"=>$facture_non_parvenue[0]["date"],
                                    'facturation_terminee' => 'non',
                                    'prix_ht'=> "0.0000"
                                )
            ,$facture_non_parvenue[0]
            ,"Erreur sur la création d'une facture parvenue"
        );

        $this->obj->d($id_bon_de_commande);
        $bon_de_commande["preview"]=true;
        $bon_de_commande["panel_courriel-checkbox"]=false;
        $id_bon_de_commande2=classes::decryptId(ATF::bon_de_commande()->insert($bon_de_commande,$this->s,NULL,$refresh));
        $this->assertNotNull($id_bon_de_commande2,"Le bon de commande n'a pas été créé en preview");

        $bon_de_commande["preview"]=true;
        $bon_de_commande["bon_de_commande"]["id_fournisseur"]=246;
        $bon_de_commande["bon_de_commande"]["prix_cleodis"]=1000;
        $bon_de_commande["bon_de_commande"]["prix"]=200;
        $id_bon_de_commande3=classes::decryptId(ATF::bon_de_commande()->insert($bon_de_commande,$this->s,NULL,$refresh));

        ATF::facture_non_parvenue()->q->reset()->addCondition("id_bon_de_commande",$id_bon_de_commande3);
        $facture_non_parvenue=ATF::facture_non_parvenue()->sa();
        $this->assertEquals(array(
                "id_facture_non_parvenue"=>$facture_non_parvenue[0]["id_facture_non_parvenue"],
                "ref"=>"FCLEO-".$this->commande_select["ref"]."-1-FNP",
                "id_facture_fournisseur"=>NULL,
                "prix"=>"1000.00",
                "tva"=>"1.196",
                "etat"=>"impayee",
                "id_affaire"=>$this->devis_select["id_affaire"],
                "id_bon_de_commande"=>$id_bon_de_commande3,
                "date"=>$facture_non_parvenue[0]["date"],
                'facturation_terminee' => 'non',
                'prix_ht'=> "0.0000"
            )
            ,$facture_non_parvenue[0]
            ,"Erreur sur la création d'une facture parvenue du BDC CLEODIS"
        );

    }


    /*@author Morgan FLEURUQUIN <mfleurquin@absystech.fr>  */
    public function test_createAllBDC(){
        $this->insertBdc();

        $id_affaire = ATF::commande()->select($this->id_commande, "id_affaire");

        ATF::bon_de_commande()->createAllBDC(array("id_commande"=>$this->id_commande));

        ATF::bon_de_commande()->q->reset()->where("id_affaire", $id_affaire);
        $ret = ATF::bon_de_commande()->sa();

        $this->assertEquals(count($ret), 2, "Il doit y avoir 2 BDC sur l'affaire normalement !");
        $this->assertEquals($ret[0]["prix"]," 1528.00", "Prix du BDC 1 incorrect");
        $this->assertEquals($ret[0]["ref"],"FDELL09-".$this->commande_select["ref"]."-1", "Ref du BDC 1 incorrect");
        $this->assertEquals($ret[0]["id_fournisseur"]," 1351", "Fournisseur du BDC 1 incorrect");


        $this->assertEquals($ret[1]["prix"], "3113.00", "Prix du BDC 2 incorrect");
        $this->assertEquals($ret[1]["ref"],"FAUDIO-".$this->commande_select["ref"]."-1", "Ref du BDC 2 incorrect");
        $this->assertEquals($ret[1]["id_fournisseur"]," 1358", "Fournisseur du BDC 2 incorrect");

    }


    /*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
    public function test_bdcByAffaire(){

        $this->insertBdc();

        //1er BDC
        $bon_de_commande["bon_de_commande"]=array(
                                 "id_societe" => $this->id_societe
                                ,"id_commande" => $this->id_commande
                                ,"id_fournisseur" => 1351
                                ,"id_affaire" => $this->devis_select["id_affaire"]
                                ,"bon_de_commande" => $this->devis_select["devis"]
                                ,"id_contact" => 5333
                                ,"prix" => "10.00"
                                ,"tva" =>"1.196"
                                ,"etat" => "envoyee"
                                ,"payee" => "non"
                                ,"date" => date("Y-m-d")
                                ,"destinataire" => "AXXES"
                                ,"adresse" => "26 rue de La Vilette - Part Dieu"
                                ,"adresse_2" => $this->devis_select["id_devis"]
                                ,"adresse_3" => $this->devis_select["id_devis"]
                                ,"cp" => "69003"
                                ,"ville" => "LYON"
                                ,"id_pays" => "FR"
                                ,"id_fournisseur_intermediaire" => NULL
                                ,"livraison_destinataire" => NULL
                                ,"livraison_adresse" => NULL
                                ,"livraison_cp" => NULL
                                ,"livraison_ville" => NULL
                                ,"email" => "debug@absystech.fr"
                                ,"emailTexte" => "TU<br>"
                                ,"emailCopie" => "debug@absystech.fr"
                                ,"filestoattach" =>  array(
                                        "fichier_joint" =>NULL
                                    )
        );

        $bon_de_commande["commandes"]="xnode-".$this->id_commande.",".$this->commande_ligne[0]["id_commande_ligne"]."";

        $refresh = array();
        $id_bon_de_commande=classes::decryptId(ATF::bon_de_commande()->insert($bon_de_commande,$this->s,NULL,$refresh));

        $this->assertFalse($this->obj->bdcByAffaire($this->id_commande),"1 bdcByAffaire renvoie True alors qu'il reste encore des Bdc !!!");

        //2er BDC
        $bon_de_commande["bon_de_commande"]=array(
                                 "id_societe" => $this->id_societe
                                ,"id_commande" => $this->id_commande
                                ,"id_fournisseur" => 1351
                                ,"id_affaire" => $this->devis_select["id_affaire"]
                                ,"bon_de_commande" => $this->devis_select["devis"]
                                ,"id_contact" => 5333
                                ,"prix" => "1 518.00"
                                ,"tva" =>"1.196"
                                ,"etat" => "envoyee"
                                ,"payee" => "non"
                                ,"date" => date("Y-m-d")
                                ,"destinataire" => "AXXES"
                                ,"adresse" => "26 rue de La Vilette - Part Dieu"
                                ,"adresse_2" => $this->devis_select["id_devis"]
                                ,"adresse_3" => $this->devis_select["id_devis"]
                                ,"cp" => "69003"
                                ,"ville" => "LYON"
                                ,"id_pays" => "FR"
                                ,"id_fournisseur_intermediaire" => NULL
                                ,"livraison_destinataire" => NULL
                                ,"livraison_adresse" => NULL
                                ,"livraison_cp" => NULL
                                ,"livraison_ville" => NULL
                                ,"email" => "debug@absystech.fr"
                                ,"emailTexte" => "TU<br>"
                                ,"emailCopie" => "debug@absystech.fr"
                                ,"filestoattach" =>  array(
                                        "fichier_joint" =>NULL
                                    )
        );

        $bon_de_commande["commandes"]="xnode-".$this->id_commande.",".$this->commande_ligne[1]["id_commande_ligne"]."";

        $refresh = array();
        $id_bon_de_commande=classes::decryptId(ATF::bon_de_commande()->insert($bon_de_commande,$this->s,NULL,$refresh));

        $this->assertFalse($this->obj->bdcByAffaire($this->id_commande),"2 bdcByAffaire renvoie True alors qu'il reste encore des Bdc !!!");

        //3er BDC
        $bon_de_commande["bon_de_commande"]=array(
                                 "id_societe" => $this->id_societe
                                ,"id_commande" => $this->id_commande
                                ,"id_fournisseur" => 1358
                                ,"id_affaire" => $this->devis_select["id_affaire"]
                                ,"bon_de_commande" => $this->devis_select["devis"]
                                ,"id_contact" => 4365
                                ,"prix" => " 3 113.00"
                                ,"tva" =>"1.196"
                                ,"etat" => "envoyee"
                                ,"payee" => "non"
                                ,"date" => date("Y-m-d")
                                ,"destinataire" => "AXXES"
                                ,"adresse" => "26 rue de La Vilette - Part Dieu"
                                ,"adresse_2" => $this->devis_select["id_devis"]
                                ,"adresse_3" => $this->devis_select["id_devis"]
                                ,"cp" => "69003"
                                ,"ville" => "LYON"
                                ,"id_pays" => "FR"
                                ,"id_fournisseur_intermediaire" => NULL
                                ,"livraison_destinataire" => NULL
                                ,"livraison_adresse" => NULL
                                ,"livraison_cp" => NULL
                                ,"livraison_ville" => NULL
                                ,"email" => "debug@absystech.fr"
                                ,"emailTexte" => "TU<br>"
                                ,"emailCopie" => "debug@absystech.fr"
                                ,"filestoattach" =>  array(
                                        "fichier_joint" =>NULL
                                    )
        );

        $bon_de_commande["commandes"]="xnode-".$this->id_commande.",".$this->commande_ligne[2]["id_commande_ligne"]."";

        $refresh = array();
        $id_bon_de_commande=classes::decryptId(ATF::bon_de_commande()->insert($bon_de_commande,$this->s,NULL,$refresh));

        $this->assertTrue($this->obj->bdcByAffaire($this->id_commande),"3 bdcByAffaire renvoie False alors qu'il n'y a plus des Bdc !!!");

    }

    /*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
    public function test_getRef(){
        $this->insertBdc();

        ATF::societe()->u(array("id_societe"=>253, "code_fournisseur"=>NULL));

        try {
            $this->obj->getRef($this->devis_select["id_affaire"],253);
        } catch (errorATF $e) {
            $error = $e->getCode();
        }
        $this->assertEquals(880,$error,'Erreur Fournisseur sans code fournisseur');

        ATF::societe()->u(array("id_societe"=>253, "code_fournisseur"=>"FCOLBE"));


        $this->assertEquals("FDELL09-".$this->devis_select["ref"]."-1",$this->obj->getRef($this->devis_select["id_affaire"],1351),'Erreur sur la ref bon de commande si 1er ref');

        $bon_de_commande["bon_de_commande"]=array(
                                 "id_societe" => $this->id_societe
                                ,"id_commande" => $this->id_commande
                                ,"id_fournisseur" => 1351
                                ,"id_affaire" => $this->devis_select["id_affaire"]
                                ,"bon_de_commande" => $this->devis_select["devis"]
                                ,"id_contact" => 5333
                                ,"prix" => "10.00"
                                ,"tva" =>"1.196"
                                ,"etat" => "envoyee"
                                ,"payee" => "non"
                                ,"date" => date("Y-m-d")
                                ,"destinataire" => "AXXES"
                                ,"adresse" => "26 rue de La Vilette - Part Dieu"
                                ,"adresse_2" => $this->devis_select["id_devis"]
                                ,"adresse_3" => $this->devis_select["id_devis"]
                                ,"cp" => "69003"
                                ,"ville" => "LYON"
                                ,"id_pays" => "FR"
                                ,"id_fournisseur_intermediaire" => NULL
                                ,"livraison_destinataire" => NULL
                                ,"livraison_adresse" => NULL
                                ,"livraison_cp" => NULL
                                ,"livraison_ville" => NULL
                                ,"email" => "debug@absystech.fr"
                                ,"emailTexte" => "TU<br>"
                                ,"emailCopie" => "debug@absystech.fr"
                                ,"filestoattach" =>  array(
                                        "fichier_joint" =>NULL
                                    )
        );

        $bon_de_commande["commandes"]="xnode-".$this->id_commande.",".$this->commande_ligne[0]["id_commande_ligne"]."";

        $refresh = array();
        $id_bon_de_commande=classes::decryptId(ATF::bon_de_commande()->insert($bon_de_commande,$this->s,NULL,$refresh));
        $this->assertEquals("FDELL09-".$this->devis_select["ref"]."-2",$this->obj->getRef($this->devis_select["id_affaire"],1351),'Erreur sur la ref bon de commande si 2er ref');
    }

    /*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
    public function test_select_all(){
        $this->insertBdc();

        $this->obj->q->reset()->addCondition("bon_de_commande.id_affaire",$this->devis_select["id_affaire"])->setCount();
        $bon_de_commande["bon_de_commande"]=array(
                                 "id_societe" => $this->id_societe
                                ,"id_commande" => $this->id_commande
                                ,"id_fournisseur" => 1351
                                ,"id_affaire" => $this->devis_select["id_affaire"]
                                ,"bon_de_commande" => $this->devis_select["devis"]
                                ,"id_contact" => 5333
                                ,"prix" => "10.00"
                                ,"tva" =>"1.196"
                                ,"etat" => "envoyee"
                                ,"payee" => "non"
                                ,"date" => date("Y-m-d")
                                ,"destinataire" => "AXXES"
                                ,"adresse" => "26 rue de La Vilette - Part Dieu"
                                ,"adresse_2" => $this->devis_select["id_devis"]
                                ,"adresse_3" => $this->devis_select["id_devis"]
                                ,"cp" => "69003"
                                ,"ville" => "LYON"
                                ,"id_pays" => "FR"
                                ,"id_fournisseur_intermediaire" => NULL
                                ,"livraison_destinataire" => NULL
                                ,"livraison_adresse" => NULL
                                ,"livraison_cp" => NULL
                                ,"livraison_ville" => NULL
                                ,"email" => "debug@absystech.fr"
                                ,"emailTexte" => "TU<br>"
                                ,"emailCopie" => "debug@absystech.fr"
                                ,"filestoattach" =>  array(
                                        "fichier_joint" =>NULL
                                    )
        );

        $bon_de_commande["commandes"]="xnode-".$this->id_commande.",".$this->commande_ligne[0]["id_commande_ligne"]."";

        $refresh = array();
        $id_bon_de_commande1=classes::decryptId(ATF::bon_de_commande()->insert($bon_de_commande,$this->s,NULL,$refresh));
        ATF::bon_de_commande_ligne()->q->reset()->addCondition("id_bon_de_commande",$id_bon_de_commande1);
        $bon_de_commande_ligne=ATF::bon_de_commande_ligne()->sa();
        ATF::commande_ligne()->u(array("id_commande_ligne"=>$bon_de_commande_ligne[0]["id_commande_ligne"],"serial"=>"11"));

        $this->obj->q->reset()->addCondition("bon_de_commande.id_affaire",$this->devis_select["id_affaire"])->setCount();

        //2er BDC
        $bon_de_commande["bon_de_commande"]=array(
                                 "id_societe" => $this->id_societe
                                ,"id_commande" => $this->id_commande
                                ,"id_fournisseur" => 1351
                                ,"id_affaire" => $this->devis_select["id_affaire"]
                                ,"bon_de_commande" => $this->devis_select["devis"]
                                ,"id_contact" => 5333
                                ,"prix" => "1 518.00"
                                ,"tva" =>"1.196"
                                ,"etat" => "envoyee"
                                ,"payee" => "non"
                                ,"date" => date("Y-m-d")
                                ,"destinataire" => "AXXES"
                                ,"adresse" => "26 rue de La Vilette - Part Dieu"
                                ,"adresse_2" => $this->devis_select["id_devis"]
                                ,"adresse_3" => $this->devis_select["id_devis"]
                                ,"cp" => "69003"
                                ,"ville" => "LYON"
                                ,"id_pays" => "FR"
                                ,"id_fournisseur_intermediaire" => NULL
                                ,"livraison_destinataire" => NULL
                                ,"livraison_adresse" => NULL
                                ,"livraison_cp" => NULL
                                ,"livraison_ville" => NULL
                                ,"email" => "debug@absystech.fr"
                                ,"emailTexte" => "TU<br>"
                                ,"emailCopie" => "debug@absystech.fr"
                                ,"filestoattach" =>  array(
                                        "fichier_joint" =>NULL
                                    )
        );

        $bon_de_commande["commandes"]="xnode-".$this->id_commande.",".$this->commande_ligne[1]["id_commande_ligne"]."";

        $refresh = array();
        $id_bon_de_commande2=classes::decryptId(ATF::bon_de_commande()->insert($bon_de_commande,$this->s,NULL,$refresh));
        ATF::bon_de_commande_ligne()->q->reset()->addCondition("id_bon_de_commande",$id_bon_de_commande2);
        $bon_de_commande_ligne=ATF::bon_de_commande_ligne()->sa();
        ATF::commande_ligne()->u(array("id_commande_ligne"=>$bon_de_commande_ligne[0]["id_commande_ligne"],"serial"=>"22"));

        //3er BDC
        $bon_de_commande["bon_de_commande"]=array(
                                 "id_societe" => $this->id_societe
                                ,"id_commande" => $this->id_commande
                                ,"id_fournisseur" => 1358
                                ,"id_affaire" => $this->devis_select["id_affaire"]
                                ,"bon_de_commande" => $this->devis_select["devis"]
                                ,"id_contact" => 4365
                                ,"prix" => " 3 113.00"
                                ,"tva" =>"1.196"
                                ,"etat" => "envoyee"
                                ,"payee" => "non"
                                ,"date" => date("Y-m-d")
                                ,"destinataire" => "AXXES"
                                ,"adresse" => "26 rue de La Vilette - Part Dieu"
                                ,"adresse_2" => $this->devis_select["id_devis"]
                                ,"adresse_3" => $this->devis_select["id_devis"]
                                ,"cp" => "69003"
                                ,"ville" => "LYON"
                                ,"id_pays" => "FR"
                                ,"id_fournisseur_intermediaire" => NULL
                                ,"livraison_destinataire" => NULL
                                ,"livraison_adresse" => NULL
                                ,"livraison_cp" => NULL
                                ,"livraison_ville" => NULL
                                ,"email" => "debug@absystech.fr"
                                ,"emailTexte" => "TU<br>"
                                ,"emailCopie" => "debug@absystech.fr"
                                ,"filestoattach" =>  array(
                                        "fichier_joint" =>NULL
                                    )
        );

        $bon_de_commande["commandes"]="xnode-".$this->id_commande.",".$this->commande_ligne[2]["id_commande_ligne"]."";

        $refresh = array();
        $id_bon_de_commande3=classes::decryptId(ATF::bon_de_commande()->insert($bon_de_commande,$this->s,NULL,$refresh));
        ATF::bon_de_commande_ligne()->q->reset()->addCondition("id_bon_de_commande",$id_bon_de_commande3);
        $bon_de_commande_ligne=ATF::bon_de_commande_ligne()->sa();
        ATF::commande_ligne()->u(array("id_commande_ligne"=>$bon_de_commande_ligne[0]["id_commande_ligne"],"serial"=>"33"));

        $this->obj->q->reset()->addCondition("bon_de_commande.id_affaire",$this->devis_select["id_affaire"])->setCount();

        $sa=$this->obj->select_all();

        $this->assertEquals("3723.14800",$sa["data"][0]["solde"]
                            ,'1 Erreur sur le sa solde');

        $this->assertEquals($id_bon_de_commande3,$sa["data"][0]["bon_de_commande.id_bon_de_commande"]
                            ,'1 Erreur sur le sa id_bon_de_commande');

        $this->assertEquals(true,$sa["data"][0]["factureFournisseurAllow"]
                            ,'1 Erreur sur le sa factureFournisseurAllow');

        $this->assertEquals(true,$sa["data"][0]["parcInsertionAllow"]
                            ,'1 Erreur sur le sa parcInsertionAllow');


        $this->assertEquals("1815.52800",$sa["data"][1]["solde"]
                            ,'2 Erreur sur le sa solde');

        $this->assertEquals($id_bon_de_commande2,$sa["data"][1]["bon_de_commande.id_bon_de_commande"]
                            ,'2 Erreur sur le sa id_bon_de_commande');

        $this->assertEquals(true,$sa["data"][1]["factureFournisseurAllow"]
                            ,'2 Erreur sur le sa factureFournisseurAllow');


        $this->assertEquals("11.96000",$sa["data"][2]["solde"]
                            ,'3 Erreur sur le sa solde');

        $this->assertEquals($id_bon_de_commande1,$sa["data"][2]["bon_de_commande.id_bon_de_commande"]
                            ,'3 Erreur sur le sa id_bon_de_commande');

        $this->assertEquals(true,$sa["data"][2]["factureFournisseurAllow"]
                            ,'3 Erreur sur le sa factureFournisseurAllow');



        ATF::commande_ligne()->u(array("id_commande_ligne"=>$bon_de_commande_ligne[0]["id_commande_ligne"],"serial"=>"33 44"));
        $sa=$this->obj->select_all();

        $this->assertEquals("3723.14800",$sa["data"][0]["solde"]
                            ,'11 Erreur sur le sa solde');

        $this->assertEquals($id_bon_de_commande3,$sa["data"][0]["bon_de_commande.id_bon_de_commande"]
                            ,'11 Erreur sur le sa id_bon_de_commande');

        $this->assertEquals(true,$sa["data"][0]["factureFournisseurAllow"]
                            ,'11 Erreur sur le sa factureFournisseurAllow');


        $this->assertEquals("1815.52800",$sa["data"][1]["solde"]
                            ,'22 Erreur sur le sa solde');

        $this->assertEquals($id_bon_de_commande2,$sa["data"][1]["bon_de_commande.id_bon_de_commande"]
                            ,'22 Erreur sur le sa id_bon_de_commande');

        $this->assertEquals(true,$sa["data"][1]["factureFournisseurAllow"]
                            ,'22 Erreur sur le sa factureFournisseurAllow');


        $this->assertEquals("11.96000",$sa["data"][2]["solde"]
                            ,'33 Erreur sur le sa solde');

        $this->assertEquals($id_bon_de_commande1,$sa["data"][2]["bon_de_commande.id_bon_de_commande"]
                            ,'33 Erreur sur le sa id_bon_de_commande');

        $this->assertEquals(true,$sa["data"][2]["factureFournisseurAllow"]
                            ,'33 Erreur sur le sa factureFournisseurAllow');
    }

    /*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
    public function test_can_delete(){
        $this->insertBdc();

        ATF::facture_ligne()->q->reset()->where("id_affaire_provenance","6211");
        $allSQL2 = ATF::facture_ligne()->sa();
        foreach ($allSQL2 as $key => $value) { ATF::facture_ligne()->d($value["id_facture_ligne"]); }

        $bon_de_commande["bon_de_commande"]=array(
                                 "id_societe" => $this->id_societe
                                ,"id_commande" => $this->id_commande
                                ,"id_fournisseur" => 1351
                                ,"id_affaire" => $this->devis_select["id_affaire"]
                                ,"bon_de_commande" => $this->devis_select["devis"]
                                ,"id_contact" => 5333
                                ,"prix" => "10.00"
                                ,"tva" =>"1.196"
                                ,"etat" => "envoyee"
                                ,"payee" => "non"
                                ,"date" => date("Y-m-d")
                                ,"destinataire" => "AXXES"
                                ,"adresse" => "26 rue de La Vilette - Part Dieu"
                                ,"adresse_2" => $this->devis_select["id_devis"]
                                ,"adresse_3" => $this->devis_select["id_devis"]
                                ,"cp" => "69003"
                                ,"ville" => "LYON"
                                ,"id_pays" => "FR"
                                ,"id_fournisseur_intermediaire" => NULL
                                ,"livraison_destinataire" => NULL
                                ,"livraison_adresse" => NULL
                                ,"livraison_cp" => NULL
                                ,"livraison_ville" => NULL
                                ,"email" => "debug@absystech.fr"
                                ,"emailTexte" => "TU<br>"
                                ,"emailCopie" => "debug@absystech.fr"
                                ,"filestoattach" =>  array(
                                        "fichier_joint" =>NULL
                                    )
        );
        $bon_de_commande["commandes"]="xnode-".$this->id_commande.",".$this->commande_ligne[0]["id_commande_ligne"]."";

        // On simule une affaire enfant
        $query = "UPDATE affaire SET id_parent=".$this->devis_select["id_affaire"]." WHERE id_affaire=26";
        ATF::db()->query($query);

        $refresh = array();
        $id_bon_de_commande=classes::decryptId(ATF::bon_de_commande()->insert($bon_de_commande,$this->s,NULL,$refresh));
        ATF::bon_de_commande()->can_delete($id_bon_de_commande);
        try{
            $this->assertTrue(ATF::bon_de_commande()->can_delete($id_bon_de_commande),'1 Probleme sur le can_delete');
        }catch (errorATF $e) {
            $error = $e->getCode();
        }
        $this->assertTrue(ATF::bon_de_commande()->can_update($id_bon_de_commande),'1 Probleme sur le can_update');

        ATF::facture_fournisseur()->i(array("ref"=>"reftu","id_fournisseur"=>1351,"prix"=>"10.00","tva"=>"1.196","etat"=>"impayee","id_affaire"=>$this->devis_select["id_affaire"],"id_bon_de_commande"=>$id_bon_de_commande,"date_echeance"=>date("Y-m-d"), "date"=> date("Y-m-d"), "type"=>"achat"));

        try {
            ATF::bon_de_commande()->can_delete($id_bon_de_commande);
        } catch (errorATF $e) {
            $error = $e->getCode();
        }
        $this->assertEquals(884,$error,'2 Probleme sur le can_delete car facture_fournisseur');

        try {
            ATF::bon_de_commande()->can_update($id_bon_de_commande);
        } catch (errorATF $e) {
            $error = $e->getCode();
        }
        $this->assertEquals(884,$error,'2 Probleme sur le can_update car facture_fournisseur');

        // Test avec affaire parente
        ATF::affaire()->q->reset()->whereIsNotNull("id_parent")->setLimit(1)->addField("affaire.id_parent","id_parent")->addField("affaire.id_affaire","id_affaire")->setStrict();
        $a = ATF::affaire()->select_row();
        $bon_de_commande["bon_de_commande"]["id_affaire"]=$a["id_parent"];
        ATF::affaire()->u(array("id_affaire"=>$a["id_affaire"],"nature"=>"AR"));
        $id_bon_de_commande=classes::decryptId(ATF::bon_de_commande()->insert($bon_de_commande,$this->s,NULL,$refresh));

        foreach (array(6211,6989,6990,7779,7782,7784) as $i) { // Ces affaires sont censé ne pas être présente dans les facture lignes dans le champs 'id_affaire_provenance'
            ATF::facturation()->q->reset()->where("id_affaire",$i);
            ATF::facturation()->d();
            ATF::facture()->q->reset()->where("id_affaire",$i);
            ATF::facture()->d();
            ATF::affaire()->d($i);
        }
        try{
            $this->assertTrue(ATF::bon_de_commande()->can_delete($id_bon_de_commande));
        }catch (errorATF $e) {
            $error = $e->getCode();
        }
    }

    /*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
    public function test_autocompleteConditions(){
        $this->insertBdc();

        $bon_de_commande["bon_de_commande"]=array(
                                 "id_societe" => $this->id_societe
                                ,"id_commande" => $this->id_commande
                                ,"id_fournisseur" => 1351
                                ,"id_affaire" => $this->devis_select["id_affaire"]
                                ,"bon_de_commande" => $this->devis_select["devis"]
                                ,"id_contact" => 5333
                                ,"prix" => "10.00"
                                ,"tva" =>"1.196"
                                ,"etat" => "envoyee"
                                ,"payee" => "non"
                                ,"date" => date("Y-m-d")
                                ,"destinataire" => "AXXES"
                                ,"adresse" => "26 rue de La Vilette - Part Dieu"
                                ,"adresse_2" => $this->devis_select["id_devis"]
                                ,"adresse_3" => $this->devis_select["id_devis"]
                                ,"cp" => "69003"
                                ,"ville" => "LYON"
                                ,"id_pays" => "FR"
                                ,"id_fournisseur_intermediaire" => NULL
                                ,"livraison_destinataire" => NULL
                                ,"livraison_adresse" => NULL
                                ,"livraison_cp" => NULL
                                ,"livraison_ville" => NULL
                                ,"email" => "debug@absystech.fr"
                                ,"emailTexte" => "TU<br>"
                                ,"emailCopie" => "debug@absystech.fr"
                                ,"filestoattach" =>  array(
                                        "fichier_joint" =>NULL
                                    )
        );

        $bon_de_commande["commandes"]="xnode-".$this->id_commande.",".$this->commande_ligne[0]["id_commande_ligne"]."";

        $refresh = array();
        $id_bon_de_commande=classes::decryptId(ATF::bon_de_commande()->insert($bon_de_commande,$this->s,NULL,$refresh));

        $infos["id_commande"]=$id_bon_de_commande;
        $autocompleteConditions=$this->obj->autocompleteConditions(ATF::bon_de_commande(),$infos,"id_fournisseur",1351,"id_fournisseur");
        $this->assertEquals(
            array("condition_field"=>array("commande_ligne.id_commande","bon_de_commande.id_fournisseur"),"condition_value"=>array($id_bon_de_commande,1351)),
            $autocompleteConditions,
            "Le couple condition_field/value est incorrect"
        );
    }


    /*@author Yann GAUTHERON <ygautheron@absystech.fr>  */
    public function test_defaultValues(){

        $this->insertBdc();
        ATF::_r('id_commande',$this->commande_select["id_commande"]);
        $this->assertEquals(ATF::bon_de_commande()->majMail($this->commande_select["id_societe"]),ATF::bon_de_commande()->default_value("emailTexte"),'valeur emailTexte');
        $this->assertEquals($this->commande_select["id_affaire"],ATF::bon_de_commande()->default_value("id_affaire"),'valeur id_affaire');
        $this->assertEquals("Tu Bdc - S00088",ATF::bon_de_commande()->default_value("bon_de_commande"),'valeur commande');
        $this->assertEquals($this->commande_select["tva"],ATF::bon_de_commande()->default_value("tva"),'valeur tva');
        $this->assertEquals(date("Y-m-d"),ATF::bon_de_commande()->default_value("date"),'valeur date');
        $this->assertEquals($this->commande_select["id_societe"],ATF::bon_de_commande()->default_value("id_societe"),'valeur id_societe');
        $this->assertEquals(ATF::societe()->select($this->commande_select["id_societe"],"adresse"),ATF::bon_de_commande()->default_value("adresse"),'valeur adresse');
        $this->assertEquals(ATF::societe()->select($this->commande_select["id_societe"],"adresse_2"),ATF::bon_de_commande()->default_value("adresse_2"),'valeur adresse_2');
        $this->assertEquals(ATF::societe()->select($this->commande_select["id_societe"],"adresse_3"),ATF::bon_de_commande()->default_value("adresse_3"),'valeur adresse_3');
        $this->assertEquals(ATF::societe()->select($this->commande_select["id_societe"],"ville"),ATF::bon_de_commande()->default_value("ville"),'valeur ville"');
        $this->assertEquals(ATF::societe()->select($this->commande_select["id_societe"],"cp"),ATF::bon_de_commande()->default_value("cp"),'valeur cp');
        $this->assertEquals(ATF::societe()->select($this->commande_select["id_societe"],"id_pays"),ATF::bon_de_commande()->default_value("id_pays"),'valeur id_pays');
        $this->assertEquals(ATF::societe()->nom($this->commande_select["id_societe"],"id_pays"),ATF::bon_de_commande()->default_value("destinataire"),'valeur destinataire');
        $this->assertEquals("debug@absystech.fr",ATF::bon_de_commande()->default_value("emailCopie"),'valeur emailCopie');

        $this->assertNull(ATF::bon_de_commande()->default_value("default_value"),'valeur default_value');
    }

    /*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
    public function test_delete(){

        $this->insertBdc();

        //1er BDC
        $bon_de_commande["bon_de_commande"]=array(
                                 "id_societe" => $this->id_societe
                                ,"id_commande" => $this->id_commande
                                ,"id_fournisseur" => 1351
                                ,"id_affaire" => $this->devis_select["id_affaire"]
                                ,"bon_de_commande" => $this->devis_select["devis"]
                                ,"id_contact" => 5333
                                ,"prix" => "10.00"
                                ,"tva" =>"1.196"
                                ,"etat" => "envoyee"
                                ,"payee" => "non"
                                ,"date" => date("Y-m-d")
                                ,"destinataire" => "AXXES"
                                ,"adresse" => "26 rue de La Vilette - Part Dieu"
                                ,"adresse_2" => $this->devis_select["id_devis"]
                                ,"adresse_3" => $this->devis_select["id_devis"]
                                ,"cp" => "69003"
                                ,"ville" => "LYON"
                                ,"id_pays" => "FR"
                                ,"id_fournisseur_intermediaire" => NULL
                                ,"livraison_destinataire" => NULL
                                ,"livraison_adresse" => NULL
                                ,"livraison_cp" => NULL
                                ,"livraison_ville" => NULL
                                ,"email" => "debug@absystech.fr"
                                ,"emailTexte" => "TU<br>"
                                ,"emailCopie" => "debug@absystech.fr"
                                ,"filestoattach" =>  array("fichier_joint" =>NULL)
        );

        $bon_de_commande["commandes"]="xnode-".$this->id_commande.",".$this->commande_ligne[1]["id_commande_ligne"].",".$this->commande_ligne[2]["id_commande_ligne"];

        $refresh = array();
        $id_bon_de_commande=classes::decryptId(ATF::bon_de_commande()->insert($bon_de_commande,$this->s,NULL,$refresh));

        $id_parc1=ATF::parc()->i(array("id_affaire"=>$this->devis_select["id_affaire"],"libelle"=>"parc tu1","serial"=>"BBPZB2J2","etat"=>"loue"));
        $id_parc2=ATF::parc()->i(array("id_affaire"=>$this->devis_select["id_affaire"],"libelle"=>"parc tu2","serial"=>"BBPZB2J3","etat"=>"loue","provenance"=>$this->devis_select["id_affaire"]));


        ATF::facture_non_parvenue()->q->reset()->addCondition("id_bon_de_commande",$id_bon_de_commande);
        $facture_non_parvenue=ATF::facture_non_parvenue()->sa();

        $this->assertNotNull($facture_non_parvenue,'les facture_non_parvenue ne s insèrent pas');

        try{
            $this->obj->delete(array("id"=>array(0=>$id_bon_de_commande)));

             $this->assertNull($this->obj->select($id_bon_de_commande),'le bon_de_commande ne se delete pas');
        }catch(errorATF $e){
            $e->setError();
        }

        ATF::facture_non_parvenue()->q->reset()->addCondition("id_bon_de_commande",$id_bon_de_commande);
        $facture_non_parvenue=ATF::facture_non_parvenue()->sa();

        $this->assertNull($facture_non_parvenue,'les facture_non_parvenue ne se deletent pas');

        ATF::parc()->q->reset()->addCondition("serial","BBPZB2J2");
        $parc1=ATF::parc()->sa();
        $this->assertNull($parc1,'le parc ne se supprime pas');

        ATF::parc()->q->reset()->addCondition("serial","BBPZB2J3");
        $parc2=ATF::parc()->sa();
        $this->assertNotNull($parc2,'le parc ne devrait pas se supprimer');

    }


    public function test_export_cegid(){
        $this->insertBdc();

        $id_affaire = ATF::commande()->select($this->id_commande, "id_affaire");
        ATF::commande()->updateDate(array("id_commande"=>$this->id_commande, "key"=>"date_debut", "value"=> date("Y-m-01")));

        ATF::bon_de_commande()->createAllBDC(array("id_commande"=>$this->id_commande));
        ATF::bon_de_commande()->q->reset()->where("bon_de_commande.id_affaire", $id_affaire);

        ob_start();
        ATF::bon_de_commande()->export_cegid(array("tu"=> true));
        //récupération des infos
        $fichier=ob_get_contents();
        //suppression des éléments (dans tampon)
        ob_end_clean();

        //vérification des informations

        $chem2=__TEMP_PATH__.ATF::$codename."/lol2.xls";
        file_put_contents($chem2,$fichier);

        //Use whatever path to an Excel file you need.
        $inputFileName = $chem2;

        try {
            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFileName);
        } catch (Exception $e) {
            die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' .
            $e->getMessage());
        }

        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        for ($row = 1; $row <= $highestRow; $row++) {
            $r = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, false);
            $rowData[] = $r[$row-1];
        }
        unlink($chem);
        unlink($chem2);

        $this->assertEquals(count($rowData), 9, "Ligne manquante");

        $header = array("TYPE", "DATE", "JOURNAL", "GENERAL", "AUXILIAIRE", "SENS", "MONTANT", "LIBELLE", "REFERENCE INTERNE" , "AXE1");
        $this->assertEquals($rowData[0], $header, "Header incorrect");

        $this->assertEquals($rowData[1][0], "G", "Cellule 1-0 incorrecte");
        $this->assertEquals($rowData[1][1], " ".date("01mY"), "Cellule 1-1 incorrecte");
        $this->assertEquals($rowData[1][2], "ACH", "Cellule 1-2 incorrecte");
        $this->assertEquals($rowData[1][3], "607110", "Cellule 1-3 incorrecte");
        $this->assertEquals($rowData[1][4], "", "Cellule 1-4 incorrecte");
        $this->assertEquals($rowData[1][5], "D", "Cellule 1-5 incorrecte");
        $this->assertEquals($rowData[1][6], "1528", "Cellule 1-6 incorrecte");
        $this->assertEquals($rowData[1][7], "", "Cellule 1-7 incorrecte");
        $this->assertEquals($rowData[1][8], ATF::affaire()->select($id_affaire, "ref")." ", "Cellule 1-8 incorrecte");
        $this->assertEquals($rowData[1][9], "", "Cellule 1-9 incorrecte");

        $this->assertEquals($rowData[2][0], "A1", "Cellule 2-0 incorrecte");
        $this->assertEquals($rowData[2][1], " ".date("01mY"), "Cellule 2-1 incorrecte");
        $this->assertEquals($rowData[2][2], "ACH", "Cellule 2-2 incorrecte");
        $this->assertEquals($rowData[2][3], "607110", "Cellule 2-3 incorrecte");
        $this->assertEquals($rowData[2][4], "", "Cellule 2-4 incorrecte");
        $this->assertEquals($rowData[2][5], "D", "Cellule 2-5 incorrecte");
        $this->assertEquals($rowData[2][6], "1528", "Cellule 2-6 incorrecte");
        $this->assertEquals($rowData[2][7], "", "Cellule 2-7 incorrecte");
        $this->assertEquals($rowData[2][8], ATF::affaire()->select($id_affaire, "ref")." ", "Cellule 2-8 incorrecte");
        $this->assertEquals($rowData[2][9], "20".ATF::affaire()->select($id_affaire, "ref").ATF::societe()->select($this->id_societe, "code_client")."00", "Cellule 2-9 incorrecte");

        $this->assertEquals($rowData[3][0], "G", "Cellule 3-0 incorrecte");
        $this->assertEquals($rowData[3][1], " ".date("01mY"), "Cellule 3-1 incorrecte");
        $this->assertEquals($rowData[3][2], "ACH", "Cellule 3-2 incorrecte");
        $this->assertEquals($rowData[3][3], "445860", "Cellule 3-3 incorrecte");
        $this->assertEquals($rowData[3][4], "", "Cellule 3-4 incorrecte");
        $this->assertEquals($rowData[3][5], "D", "Cellule 3-5 incorrecte");
        $this->assertEquals($rowData[3][6], "305.6", "Cellule 3-6 incorrecte");
        $this->assertEquals($rowData[3][7], "", "Cellule 3-7 incorrecte");
        $this->assertEquals($rowData[3][8], ATF::affaire()->select($id_affaire, "ref")." ", "Cellule 3-8 incorrecte");
        $this->assertEquals($rowData[3][9], "", "Cellule 3-9 incorrecte");

        $this->assertEquals($rowData[4][0], "G", "Cellule 4-0 incorrecte");
        $this->assertEquals($rowData[4][1], " ".date("01mY"), "Cellule 4-1 incorrecte");
        $this->assertEquals($rowData[4][2], "ACH", "Cellule 4-2 incorrecte");
        $this->assertEquals($rowData[4][3], "408100", "Cellule 4-3 incorrecte");
        $this->assertEquals($rowData[4][4], "", "Cellule 4-4 incorrecte");
        $this->assertEquals($rowData[4][5], "C", "Cellule 4-5 incorrecte");
        $this->assertEquals($rowData[4][6], "1833.6", "Cellule 4-6 incorrecte");
        $this->assertEquals($rowData[4][7], "", "Cellule 4-7 incorrecte");
        $this->assertEquals($rowData[4][8], ATF::affaire()->select($id_affaire, "ref")." ", "Cellule 4-8 incorrecte");
        $this->assertEquals($rowData[4][9], "", "Cellule 4-9 incorrecte");
    }

    public function test_export_cegid2(){
        // ========================================================================================================
        //                              Création d'un refinancement par CLEODIS
        //                              Avec etat de l'affaire en Avenant
        // ========================================================================================================
        $this->insertBdc();

        $id_affaire = ATF::commande()->select($this->id_commande, "id_affaire");
        ATF::commande()->updateDate(array("id_commande"=>$this->id_commande, "key"=>"date_debut", "value"=> date("Y-m-01")));

        ATF::bon_de_commande()->createAllBDC(array("id_commande"=>$this->id_commande));
        ATF::bon_de_commande()->q->reset()->where("bon_de_commande.id_affaire", $id_affaire);

        ATF::affaire()->u(array("id_affaire"=>$id_affaire, "nature"=>"avenant"));
        ATF::demande_refi()->insert(array(  "date"=>date("Y-m-d"),
                                            "id_contact"=>$this->id_contact,
                                            "id_refinanceur"=>4,
                                            "id_affaire"=>$id_affaire,
                                            "id_societe"=>$this->id_societe,
                                            "description"=>"Tu description",
                                            "etat"=>"valide"));

        ATF::bon_de_commande()->q->reset()->where("bon_de_commande.id_affaire", $id_affaire);


        ob_start();
        ATF::bon_de_commande()->export_cegid(array("tu"=> true));
        //récupération des infos
        $fichier=ob_get_contents();
        //suppression des éléments (dans tampon)
        ob_end_clean();
        //vérification des informations
        $chem2=__TEMP_PATH__.ATF::$codename."/lol2.xls";
        file_put_contents($chem2,$fichier);

        //Use whatever path to an Excel file you need.
        $inputFileName = $chem2;

        try {
            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFileName);
        } catch (Exception $e) {
            die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' .
            $e->getMessage());
        }

        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $rowData = array();

        for ($row = 1; $row <= $highestRow; $row++) {
            $r = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, false);
            $rowData[] = $r[$row-1];
        }
        unlink($chem);
        unlink($chem2);

        $this->assertEquals(count($rowData), 7, "Ligne manquante");

        $header = array("TYPE", "DATE", "JOURNAL", "GENERAL", "AUXILIAIRE", "SENS", "MONTANT", "LIBELLE", "REFERENCE INTERNE" , "AXE1");
        $this->assertEquals($rowData[0], $header, "Header incorrect");

        $this->assertEquals($rowData[1][0], "G", "Cellule 1-0 incorrecte");
        $this->assertEquals($rowData[1][1], " ".date("01mY"), "Cellule 1-1 incorrecte");
        $this->assertEquals($rowData[1][2], "ACH", "Cellule 1-2 incorrecte");
        $this->assertEquals($rowData[1][3], "218310", "Cellule 1-3 incorrecte");
        $this->assertEquals($rowData[1][4], "", "Cellule 1-4 incorrecte");
        $this->assertEquals($rowData[1][5], "D", "Cellule 1-5 incorrecte");
        $this->assertEquals($rowData[1][6], "1528", "Cellule 1-6 incorrecte");
        $this->assertEquals($rowData[1][7], "", "Cellule 1-7 incorrecte");
        $this->assertEquals($rowData[1][8], ATF::affaire()->select($id_affaire, "ref")." ", "Cellule 1-8 incorrecte");
        $this->assertEquals($rowData[1][9], "", "Cellule 1-9 incorrecte");

        $this->assertEquals($rowData[2][0], "G", "Cellule 2-0 incorrecte");
        $this->assertEquals($rowData[2][1], " ".date("01mY"), "Cellule 2-1 incorrecte");
        $this->assertEquals($rowData[2][2], "ACH", "Cellule 2-2 incorrecte");
        $this->assertEquals($rowData[2][3], "445860", "Cellule 2-3 incorrecte");
        $this->assertEquals($rowData[2][4], "", "Cellule 2-4 incorrecte");
        $this->assertEquals($rowData[2][5], "D", "Cellule 2-5 incorrecte");
        $this->assertEquals($rowData[2][6], "305.6", "Cellule 2-6 incorrecte");
        $this->assertEquals($rowData[2][7], "", "Cellule 2-7 incorrecte");
        $this->assertEquals($rowData[2][8], ATF::affaire()->select($id_affaire, "ref")." ", "Cellule 2-8 incorrecte");
        $this->assertEquals($rowData[2][9], "", "Cellule 2-9 incorrecte");

        $this->assertEquals($rowData[3][0], "G", "Cellule 3-0 incorrecte");
        $this->assertEquals($rowData[3][1], " ".date("01mY"), "Cellule 3-1 incorrecte");
        $this->assertEquals($rowData[3][2], "ACH", "Cellule 3-2 incorrecte");
        $this->assertEquals($rowData[3][3], "408100", "Cellule 3-3 incorrecte");
        $this->assertEquals($rowData[3][4], "", "Cellule 3-4 incorrecte");
        $this->assertEquals($rowData[3][5], "C", "Cellule 3-5 incorrecte");
        $this->assertEquals($rowData[3][6], "1833.6", "Cellule 3-6 incorrecte");
        $this->assertEquals($rowData[3][7], "", "Cellule 3-7 incorrecte");
        $this->assertEquals($rowData[3][8], ATF::affaire()->select($id_affaire, "ref")." ", "Cellule 3-8 incorrecte");
        $this->assertEquals($rowData[3][9], "", "Cellule 3-9 incorrecte");
    }

    public function test_export_servantissimmo(){
        // ========================================================================================================
        //                              Création d'un refinancement par CLEODIS
        //                              Avec etat de l'affaire en Avenant
        // ========================================================================================================
        $id_affaires = array();

        //Loyer Mensuel
        $this->insertBdc();
        $id_affaire = ATF::commande()->select($this->id_commande, "id_affaire");
        $id_affaires[] = $id_affaire;

        ATF::bon_de_commande()->createAllBDC(array("id_commande"=>$this->id_commande));

        ATF::commande()->updateDate(array("id_commande"=>$this->id_commande, "key"=>"date_debut", "value"=> date("Y-m-01")));

        ATF::affaire()->u(array("id_affaire"=>$id_affaire, "nature"=>"avenant"));
        ATF::demande_refi()->insert(array(  "date"=>date("Y-m-d"),
                                            "id_contact"=>$this->id_contact,
                                            "id_refinanceur"=>4,
                                            "id_affaire"=>$id_affaire,
                                            "id_societe"=>$this->id_societe,
                                            "description"=>"Tu description",
                                            "etat"=>"valide"));
        ATF::affaire()->u(array("id_affaire"=>$id_affaire, "nature"=>"avenant"));

        $frequence = array("trimestre", "semestre", "an");

        foreach ($frequence as $kf => $vf) {
            $this->insertBdc();
            $id_affaire = ATF::commande()->select($this->id_commande, "id_affaire");
            $id_affaires[] = $id_affaire;

            ATF::loyer()->q->reset()->where("id_affaire", $id_affaire);
            foreach (ATF::loyer()->sa() as $key => $value) {
                ATF::loyer()->u(array("id_loyer"=>$value["id_loyer"], "frequence_loyer"=> $vf));
            }

            ATF::bon_de_commande()->createAllBDC(array("id_commande"=>$this->id_commande));

            ATF::commande()->updateDate(array("id_commande"=>$this->id_commande, "key"=>"date_debut", "value"=> date("Y-m-01")));
            ATF::demande_refi()->insert(array(  "date"=>date("Y-m-d"),
                                                "id_contact"=>$this->id_contact,
                                                "id_refinanceur"=>4,
                                                "id_affaire"=>$id_affaire,
                                                "id_societe"=>$this->id_societe,
                                                "description"=>"Tu description",
                                                "etat"=>"valide"));
        }

        ATF::bon_de_commande()->q->reset();
        foreach ($id_affaires as $key => $value) {
            ATF::bon_de_commande()->q->where("bon_de_commande.id_affaire", $value, "OR");
        }


        ob_start();
        ATF::bon_de_commande()->export_servantissimmo(array("tu"=> true, "force"=>true));
        //récupération des infos
        $fichier=ob_get_contents();
        //suppression des éléments (dans tampon)
        ob_end_clean();
        //vérification des informations
        $chem2=__TEMP_PATH__.ATF::$codename."/lol2.xls";
        file_put_contents($chem2,$fichier);

        //Use whatever path to an Excel file you need.
        $inputFileName = $chem2;

        try {
            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFileName);
        } catch (Exception $e) {
            die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' .
            $e->getMessage());
        }

        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $rowData = array();

        for ($row = 1; $row <= $highestRow; $row++) {
            $r = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, false);
            $rowData[] = $r[$row-1];
        }
        unlink($chem);
        unlink($chem2);

        $this->assertEquals(count($rowData), 9, "Ligne manquante");

        $header = array('Compte','Date d\'entréee','Date de mise en service','Date de début d\'amortissement comptable','Date de début d\'amortissement fiscal','Référence','Libelle','Prix unitaire','Montant HT','Quantité','Montant TVA','Taux de TVA','Prorata','Montant TTC','Type de sortie','Date de sortie','Base comptable (=montant HT)','Méthode comptable','Durée comptable','Base fiscale','Méthode fiscale','Durée fiscale','Nature du bien','Type d\'entrée','Niveau de réalité','Totalcumulantérieur','Totalcumulantérieur fiscal','Critère 1','Réference 2','Compte fourn');
        $this->assertEquals($rowData[0], $header, "Header incorrect");

        $this->assertEquals($rowData[1][27], "20".substr(ATF::affaire()->select($id_affaires[0], "ref"),0 , 7).ATF::societe()->select($this->id_societe, "code_client")."AV", "Cellule 1-27 incorrecte");

        log::logger($rowData[8], "mfleurquin");

        $this->assertEquals($rowData[8][0], "218310", "Cellule 8-0 incorrecte");
        $this->assertEquals($rowData[8][1], date("d/m/Y"), "Cellule 8-1 incorrecte");
        $this->assertEquals($rowData[8][2], date("d/m/Y"), "Cellule 8-2 incorrecte");
        $this->assertEquals($rowData[8][3], date("d/m/Y"), "Cellule 8-3 incorrecte");
        $this->assertEquals($rowData[8][4], date("d/m/Y"), "Cellule 8-4 incorrecte");
        $this->assertEquals($rowData[8][5], "", "Cellule 8-5 incorrecte");
        $this->assertEquals($rowData[8][6], "AXXES ".ATF::affaire()->select($id_affaire, "ref")."-".ATF::societe()->select($this->id_societe, "code_client"), "Cellule 8-6 incorrecte");
        $this->assertEquals($rowData[8][7], "", "Cellule 8-7 incorrecte");
        $this->assertEquals($rowData[8][8], "3113", "Cellule 8-8 incorrecte");
        $this->assertEquals($rowData[8][9], "1", "Cellule 8-9 incorrecte");
        $this->assertEquals($rowData[8][10], "622.6", "Cellule 8-10 incorrecte");
        $this->assertEquals($rowData[8][11], "20", "Cellule 8-11 incorrecte");
        $this->assertEquals($rowData[8][12], "100", "Cellule 8-12 incorrecte");
        $this->assertEquals($rowData[8][13], "3735.6", "Cellule 8-13 incorrecte");
        $this->assertEquals($rowData[8][14], "00 ", "Cellule 8-14 incorrecte");
        $this->assertEquals($rowData[8][15], "30/12/2099", "Cellule 8-15 incorrecte");
        $this->assertEquals($rowData[8][16], "3113", "Cellule 8-16 incorrecte");
        $this->assertEquals($rowData[8][17], "01 ", "Cellule 8-17 incorrecte");
        $this->assertEquals($rowData[8][18], "14", "Cellule 8-18 incorrecte");
        $this->assertEquals($rowData[8][19], "3113", "Cellule 8-19 incorrecte");
        $this->assertEquals($rowData[8][20], "01 ", "Cellule 8-20 incorrecte");
        $this->assertEquals($rowData[8][21], "14", "Cellule 8-21 incorrecte");
        $this->assertEquals($rowData[8][22], "01 ", "Cellule 8-22 incorrecte");
        $this->assertEquals($rowData[8][23], "01 ", "Cellule 8-23 incorrecte");
        $this->assertEquals($rowData[8][24], "09 ", "Cellule 8-24 incorrecte");
        $this->assertEquals($rowData[8][25], "09 ", "Cellule 8-25 incorrecte");
        $this->assertEquals($rowData[8][26], "0 ", "Cellule 8-26 incorrecte");
        $this->assertEquals($rowData[8][27], "20".ATF::affaire()->select($id_affaire, "ref").ATF::societe()->select($this->id_societe, "code_client")."00", "Cellule 8-27 incorrecte");
        $this->assertEquals($rowData[8][28], "", "Cellule 8-28 incorrecte");
        $this->assertEquals($rowData[8][29], "AUDIOPTIC TRADE SERVICES", "Cellule 8-29 incorrecte");

    }

    /* @author Nicolas BERTEMONT <nbertemont@absystech.fr> */
    public function test_bdcMidas(){
        $cm=new bon_de_commande_midas();
        $this->assertEquals('a:7:{s:19:"bon_de_commande.ref";a:4:{s:4:"type";s:4:"text";s:5:"xtype";s:9:"textfield";s:9:"maxlength";s:2:"32";s:7:"default";N;}s:30:"bon_de_commande.id_fournisseur";a:4:{s:4:"type";s:3:"int";s:5:"xtype";s:11:"numberfield";s:9:"maxlength";s:1:"8";s:7:"default";N;}s:31:"bon_de_commande.bon_de_commande";a:4:{s:4:"type";s:4:"text";s:5:"xtype";s:9:"textfield";s:9:"maxlength";s:3:"256";s:7:"default";N;}s:20:"bon_de_commande.etat";a:6:{s:4:"type";s:4:"enum";s:5:"xtype";s:5:"combo";s:4:"data";a:6:{i:0;s:7:"envoyee";i:1;s:8:"terminee";i:2;s:3:"fnp";i:3;s:5:"stock";i:4;s:8:"a_regler";i:5;s:3:"fae";}s:7:"default";s:7:"envoyee";s:8:"renderer";s:4:"etat";s:5:"width";i:30;}s:20:"bon_de_commande.prix";a:5:{s:4:"type";s:7:"decimal";s:5:"xtype";s:11:"numberfield";s:7:"default";N;s:9:"aggregate";a:4:{i:0;s:3:"min";i:1;s:3:"avg";i:2;s:3:"max";i:3;s:3:"sum";}s:8:"renderer";s:5:"money";}s:8:"solde_ht";a:6:{s:6:"custom";b:1;s:9:"aggregate";a:4:{i:0;s:3:"min";i:1;s:3:"avg";i:2;s:3:"max";i:3;s:3:"sum";}s:5:"align";s:5:"right";s:6:"suffix";s:3:"€";s:4:"type";s:7:"decimal";s:8:"renderer";s:5:"money";}s:13:"fichier_joint";a:5:{s:6:"custom";b:1;s:6:"nosort";b:1;s:4:"type";s:4:"file";s:5:"align";s:6:"center";s:5:"width";i:50;}}',serialize($cm->colonnes['fields_column']),"Le constructeur de la classe midas a changé");
    }

    /* @author Nicolas BERTEMONT <nbertemont@absystech.fr> */
    public function test_select_allMidas(){
        $c=new bon_de_commande_midas();
        $c->select_all();
        $this->assertEquals("697c071ae10821abce3ca0bf4a6a1cd4",md5($c->q->lastSQL),"Les conditions de filtrage ont changé ?");
    }

    /* @author NMorgan FLEURQUIN <mfleurquin@absystech.fr> */
    public function test_construcCleodisBE(){
        $c=new bon_de_commande_cleodisbe();
       $this->assertTrue($c instanceOf bon_de_commande_cleodisbe, "L'objet bon_de_commande_cleodisbe n'est pas de bon type");
    }

    /* @author NMorgan FLEURQUIN <mfleurquin@absystech.fr> */
    public function test_construccap(){
        $c=new bon_de_commande_cap();
       $this->assertTrue($c instanceOf bon_de_commande_cap, "L'objet bon_de_commande_cap n'est pas de bon type");
    }


    /* @author NMorgan FLEURQUIN <mfleurquin@absystech.fr>
    * @date 08/12/2015
    */
    public function test_uploadFileFromSA() {
        $this->insertBdc();

        //1er BDC
        $bon_de_commande["bon_de_commande"]=array(
                                "id_societe" => $this->id_societe
                                ,"id_commande" => $this->id_commande
                                ,"id_fournisseur" => 1351
                                ,"id_affaire" => $this->devis_select["id_affaire"]
                                ,"bon_de_commande" => $this->devis_select["devis"]
                                ,"id_contact" => 5333
                                ,"prix" => "10.00"
                                ,"tva" =>"1.196"
                                ,"etat" => "envoyee"
                                ,"payee" => "non"
                                ,"date" => date("Y-m-d")
                                ,"destinataire" => "AXXES"
                                ,"adresse" => "26 rue de La Vilette - Part Dieu"
                                ,"adresse_2" => $this->devis_select["id_devis"]
                                ,"adresse_3" => $this->devis_select["id_devis"]
                                ,"cp" => "69003"
                                ,"ville" => "LYON"
                                ,"id_pays" => "FR"
                                ,"id_fournisseur_intermediaire" => NULL
                                ,"livraison_destinataire" => NULL
                                ,"livraison_adresse" => NULL
                                ,"livraison_cp" => NULL
                                ,"livraison_ville" => NULL
                                ,"email" => "debug@absystech.fr"
                                ,"emailTexte" => "TU<br>"
                                ,"emailCopie" => "debug@absystech.fr"
                                ,"filestoattach" =>  array("fichier_joint" =>NULL)
        );

        $bon_de_commande["commandes"]="xnode-".$this->id_commande.",".$this->commande_ligne[1]["id_commande_ligne"].",".$this->commande_ligne[2]["id_commande_ligne"];

        $refresh = array();
        $id_bon_de_commande=classes::decryptId(ATF::bon_de_commande()->insert($bon_de_commande,$this->s,NULL,$refresh));


        $infos = array(
            "extAction"=>"bon_de_commande"
        );
        $this->assertFalse($this->obj->uploadFileFromSA($infos),"Erreur, pas d'id en entrée, renvoi FALSE");
        $infos = array(
            "id"=>$id_bon_de_commande
        );
        $this->assertFalse($this->obj->uploadFileFromSA($infos),"Erreur, pas de class en entrée, renvoi FALSE");

        $infos['extAction'] = "bon_de_commande";
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
        if(!file_exists(__ABSOLUTE_PATH__."../temp/testsuite/bon_de_commande/"))util::mkdir(__ABSOLUTE_PATH__."../temp/testsuite/bon_de_commande/");
        if(!file_exists(__ABSOLUTE_PATH__."../temp/testsuite/pdf_affaire/"))util::mkdir(__ABSOLUTE_PATH__."../temp/testsuite/pdf_affaire/");

        $r = $this->obj->uploadFileFromSA($infos,ATF::_s(),$files);
        $this->assertEquals('{"success":true}',$r,"Erreur dans le retour de l'upload");
        $f = __ABSOLUTE_PATH__."../data/testsuite/bon_de_commande/".$id_bon_de_commande.".tu";
        $this->assertTrue(file_exists($f),"Erreur : le fichier n'est pas là !");
        unlink($f);


    }



};
?>