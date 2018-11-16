<?
class devis_cleodis_test extends ATF_PHPUnit_Framework_TestCase {
	protected static $devis = 'a:9:{s:9:"extAction";s:5:"devis";s:9:"extMethod";s:6:"insert";s:7:"preview";s:4:"true";s:11:"label_devis";a:5:{s:10:"id_filiale";s:7:"CLEODIS";s:14:"id_opportunite";s:8:"Aucun(e)";s:10:"id_societe";s:7:"FINORPA";s:10:"id_contact";s:16:"M Philippe MOONS";s:10:"AR_societe";s:7:"FINORPA";}s:5:"devis";a:21:{s:10:"id_filiale";s:3:"246";s:5:"devis";s:2:"TU";s:3:"tva";s:5:"1.196";s:11:"date_accord";s:10:"08-02-2011";s:14:"id_opportunite";s:0:"";s:10:"id_societe";i:5391;s:12:"type_contrat";s:3:"lld";s:8:"validite";s:10:"23-02-2011";s:10:"id_contact";s:4:"5753";s:6:"loyers";s:4:"0.00";s:23:"frais_de_gestion_unique";s:4:"0.00";s:16:"assurance_unique";s:4:"0.00";s:10:"AR_societe";s:0:"";s:5:"marge";s:5:"99.96";s:13:"marge_absolue";s:8:"8 021.00";s:4:"prix";s:8:"8 024.00";s:10:"prix_achat";s:4:"3.00";s:5:"email";s:17:"pmoons@finorpa.fr";s:10:"emailTexte";s:4:"<br>";s:10:"emailCopie";s:24:"jerome.loison@cleodis.fr";s:13:"filestoattach";a:1:{s:13:"fichier_joint";s:0:"";}}s:7:"avenant";s:0:"";s:2:"AR";s:0:"";s:5:"loyer";a:1:{s:15:"frequence_loyer";s:1:"m";}s:12:"values_devis";a:2:{s:5:"loyer";s:185:"[{"loyer__dot__loyer":"233","loyer__dot__duree":"34","loyer__dot__assurance":"2","loyer__dot__frais_de_gestion":"1","loyer__dot__frequence_loyer":"mois","loyer__dot__loyer_total":8024}]";s:8:"produits";s:415:"[{"devis_ligne__dot__produit":"Zywall 5 - dispositif de sécurité","devis_ligne__dot__quantite":"3","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"ZYX-FW","devis_ligne__dot__prix_achat":"1","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"<span class=\"searchSelectionFound\">D</span>JP SERVICE","devis_ligne__dot__id_produit_fk":"1175","devis_ligne__dot__id_fournisseur_fk":"1583"}]";}}';

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


	 /* @author NMorgan FLEURQUIN <mfleurquin@absystech.fr>
    * @date 08/12/2015
    */
    public function test_uploadFileFromSA() {

    	$devis = unserialize(self::$devis);
		$devis["preview"]=false;
		$devis["devis"]["id_opportunite"]=10;
		$devis["devis"]["RIB"]="30027 17536 00013420801 37";
		$devis["panel_courriel-checkbox"]="on";
		$devis["devis"]["email"]="tu@absystech.net";
		$devis["devis"]["emailCopie"]="tucopie@absystech.fr";
		$devis["devis"]["emailTexte"]="texte tu mail";
		$devis["values_devis"]["produits_non_visible"]='[{"devis_ligne__dot__produit":"Zywall 5 - dispositif de sécurité","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"ZYX-FW","devis_ligne__dot__prix_achat":"1","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"<span class=\"searchSelectionFound\">D</span>JP SERVICE","devis_ligne__dot__id_produit_fk":"1175","devis_ligne__dot__id_fournisseur_fk":"1583"}]';
		$refresh = array();
		$id_devis=classes::decryptId(ATF::devis()->insert($devis,$this->s,NULL,$refresh));
		$id_affaire = ATF::devis()->select($id_devis,'id_affaire');




        $infos = array(
            "extAction"=>"devis"
        );
        $this->assertFalse($this->obj->uploadFileFromSA($infos),"Erreur, pas d'id en entrée, renvoi FALSE");
        $infos = array(
            "id"=>$id_devis
        );
        $this->assertFalse($this->obj->uploadFileFromSA($infos),"Erreur, pas de class en entrée, renvoi FALSE");

        $infos['extAction'] = "devis";
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
        if(!file_exists(__ABSOLUTE_PATH__."../temp/testsuite/devis/"))util::mkdir(__ABSOLUTE_PATH__."../temp/testsuite/devis/");
        if(!file_exists(__ABSOLUTE_PATH__."../temp/testsuite/pdf_affaire/"))util::mkdir(__ABSOLUTE_PATH__."../temp/testsuite/pdf_affaire/");

        $r = $this->obj->uploadFileFromSA($infos,ATF::_s(),$files);
        $this->assertEquals('{"success":true}',$r,"Erreur dans le retour de l'upload");
        $f = __ABSOLUTE_PATH__."../data/testsuite/devis/".$id_devis.".tu";
        $this->assertTrue(file_exists($f),"Erreur : le fichier n'est pas là !");
        unlink($f);

        ATF::pdf_affaire()->q->reset()->where("id_affaire",$id_affaire);
        $pdf_affaire = ATF::pdf_affaire()->select_all();

        $f = __ABSOLUTE_PATH__."../data/testsuite/pdf_affaire/".$pdf_affaire[0]["id_pdf_affaire"].".fichier_joint";
        $this->assertTrue(file_exists($f),"Erreur : le fichier pdf_affaire n'est pas là !");
        unlink($f);

    }


	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
	public function test_insert(){
		$devis = unserialize(self::$devis);
		$devis["preview"]=false;
		$devis["devis"]["id_opportunite"]=10;
		$devis["devis"]["RIB"]="30027 17536 00013420801 37";
		$devis["panel_courriel-checkbox"]="on";
		$devis["devis"]["email"]="tu@absystech.net";
		$devis["devis"]["emailCopie"]="tucopie@absystech.fr";
		$devis["devis"]["emailTexte"]="texte tu mail";

		$loyer=$devis["values_devis"]["loyer"];
		unset($devis["values_devis"]["loyer"]);
		//Sans loyer
		try {
			classes::decryptId(ATF::devis()->insert($devis,$this->s));
		} catch (errorATF $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(875,$error,'Erreur devis sans loyer non declenchee');

		//Sans frequence
		$devis["values_devis"]["loyer"]='[{"loyer__dot__loyer":"233","loyer__dot__duree":"34","loyer__dot__assurance":"2","loyer__dot__frais_de_gestion":"1","loyer__dot__frequence_loyer":"","loyer__dot__loyer_total":8024}]';
		try {
			classes::decryptId(ATF::devis()->insert($devis,$this->s));
		} catch (errorATF $e) {
			$error1 = $e->getCode();
		}
		$this->assertEquals(876,$error1,'Erreur devis sans frequence non declenchee');

		//Sans produit
		$devis["values_devis"]["loyer"]=$loyer;
		$produits=$devis["values_devis"]["produits"];
		unset($devis["values_devis"]["produits"]);
		try {
			classes::decryptId(ATF::devis()->insert($devis,$this->s));
		} catch (errorATF $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(877,$error,'Erreur devis sans produit non declenchee');

		$devis["values_devis"]["produits"]=$produits;
		$devis["values_devis"]["produits_non_visible"]='[{"devis_ligne__dot__produit":"Zywall 5 - dispositif de sécurité","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"ZYX-FW","devis_ligne__dot__prix_achat":"1","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"","devis_ligne__dot__id_produit_fk":"1175","devis_ligne__dot__id_fournisseur_fk":""}]';
		try {
			classes::decryptId(ATF::devis()->insert($devis,$this->s));
		} catch (errorATF $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(882,$error,'Erreur produit sans fournisseur non declenchee');

		$devis["values_devis"]["produits_non_visible"]='[{"devis_ligne__dot__produit":"Zywall 5 - dispositif de sécurité","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"ZYX-FW","devis_ligne__dot__prix_achat":"1","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"<span class=\"searchSelectionFound\">D</span>JP SERVICE","devis_ligne__dot__id_produit_fk":"1175","devis_ligne__dot__id_fournisseur_fk":"1583"}]';
		$refresh = array();
		$id_devis=classes::decryptId(ATF::devis()->insert($devis,$this->s,NULL,$refresh));

		$file_exist = file_get_contents($this->obj->filepath($this->id_user,"fichier_joint",true));
		$this->assertNotNull($file_exist,"Le fichier ne c est pas créé");

		$id_affaire = ATF::devis()->select($id_devis,'id_affaire');
		$affaire=ATF::affaire()->select($id_affaire);

		$this->assertEquals(array("id_affaire"=>$id_affaire,"etat"=>"devis",
									"ref"=>"7001004","reference_refinanceur"=>NULL,"date"=>NULL,
									"id_societe"=>5391,"id_filiale"=>246,"affaire"=>"TU","taux_refi"=>NULL,
									"taux_refi_reel"=>NULL,"apporteur"=>NULL,"assurance_fixe"=>NULL,"assurance_portable"=>NULL,
									"total_depense"=>NULL,"total_recette"=>NULL,"valeur_residuelle"=>NULL,"data"=>NULL,"forecast"=>"20",
									"id_parent"=>NULL,"id_fille"=>NULL,"date_installation_prevu"=>"2008-10-01","date_installation_reel"=>NULL,
									"date_livraison_prevu"=>"2008-09-06","date_garantie"=>NULL,"nature"=>"affaire",
									"RIB"=>"30027 17536 00013420801 37","BIC"=>NULL,"IBAN"=>NULL,"nom_banque"=>NULL,"ville_banque"=>NULL,"date_previsionnelle"=>"0", "RUM" => NULL, 'date_ouverture' => NULL , "date_recettage_cablage"=> NULL,'type_affaire' => 'normal'
								 )
			,$affaire
			,"L'affaire ne renvoie pas les bonnes infos"
		);

		ATF::devis_ligne()->q->reset()->addCondition("id_devis",$id_devis);
		$devis_ligne = ATF::devis_ligne()->sa();
		$this->assertEquals(array("id_devis_ligne"=>$devis_ligne[0]["id_devis_ligne"],"type"=>"fixe","id_devis"=>$id_devis,"id_produit"=>1175,"ref"=>"ZYX-FW","produit"=>"Zywall 5 - dispositif de sécurité","quantite"=>3,"id_fournisseur"=>1583,"prix_achat"=>"1.00","code"=>NULL,"id_affaire_provenance"=>NULL,"serial"=>NULL,"visible"=>"oui","visibilite_prix"=>"visible","neuf"=>"oui",'date_achat' => null, 'ref_simag' => null,'commentaire' => null,'options' => 'non')
			,$devis_ligne[0]
			,"L'affaire ne renvoie pas les bonnes lignes de devis"
		);
		$this->assertEquals(array("id_devis_ligne"=>$devis_ligne[1]["id_devis_ligne"],"type"=>"fixe","id_devis"=>$id_devis,"id_produit"=>1175,"ref"=>"ZYX-FW","produit"=>"Zywall 5 - dispositif de sécurité","quantite"=>"0","id_fournisseur"=>1583,"prix_achat"=>"1.00","code"=>NULL,"id_affaire_provenance"=>NULL,"serial"=>NULL,"visible"=>"non","visibilite_prix"=>"visible","neuf"=>"oui",'date_achat' => null, 'ref_simag' => null,'commentaire' => null,'options' => 'non')
			,$devis_ligne[1]
			,"L'affaire ne renvoie pas les bonnes lignes de devis"
		);

		ATF::loyer()->q->reset()->addCondition("id_affaire",$id_affaire);
		$loyer = ATF::loyer()->sa();
		$this->assertEquals(array("id_loyer"=>$loyer[0]["id_loyer"],"id_affaire"=>$id_affaire,"loyer"=>"233.00","duree"=>34,"assurance"=>"2.00","frais_de_gestion"=>"1.00","frequence_loyer"=>"mois",'avec_option' => 'non')
			,$loyer[0]
			,"L'affaire ne renvoie pas les bons loyers"
		);
	}



	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
	// Annule et remplace
	public function test_insertAR(){
		$devis = unserialize(self::$devis);
		unset($devis["preview"]);
		$devis["panel_AR-checkbox"] = "on";
		$devis["devis"]["RIB"]="30027 17536 00013420801 37";

		$devis["values_devis"]["produits_repris"] = '[{"devis_ligne__dot__produit":"Optiplex GX520 TFT 17 DVD 48X","devis_ligne__dot__quantite":1,"devis_ligne__dot__type":"sans_objet","devis_ligne__dot__ref":"OptiGX520 17 DVD 48X-1","devis_ligne__dot__prix_achat":"100","devis_ligne__dot__id_produit":"Optiplex GX520 TFT 17 DVD 48X","devis_ligne__dot__id_fournisseur":"DELL","devis_ligne__dot__visibilite_prix":"invisible","devis_ligne__dot__serial":"5X7ZB2J","devis_ligne__dot__id_produit_fk":"5893","devis_ligne__dot__id_parc":"17","devis_ligne__dot__id_affaire_provenance":"26","devis_ligne__dot__id_fournisseur_fk":"1351"},{"devis_ligne__dot__produit":"LATITUDE D520","devis_ligne__dot__quantite":1,"devis_ligne__dot__type":"sans_objet","devis_ligne__dot__ref":"DELLLAT-1","devis_ligne__dot__prix_achat":"50","devis_ligne__dot__id_produit":"LATITUDE D520","devis_ligne__dot__id_fournisseur":"CBASE","devis_ligne__dot__visibilite_prix":"invisible","devis_ligne__dot__serial":"BBPZB2J","devis_ligne__dot__id_produit_fk":"6066","devis_ligne__dot__id_parc":"6995","devis_ligne__dot__id_affaire_provenance":"43","devis_ligne__dot__id_fournisseur_fk":"1349"}]';
		try {
			classes::decryptId(ATF::devis()->insert($devis,$this->s));
		} catch (errorATF $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(879,$error,'Erreur parc_sans_AR non declenchee AR');

		$devis["AR"] = "affaire_26,affaire_43";

		$id_devis = classes::decryptId(ATF::devis()->insert($devis,$this->s));
		$this->assertNotNull($id_devis,'Devis non créé');

		$id_affaire = ATF::devis()->select($id_devis,'id_affaire');
		$affaire=ATF::affaire()->select($id_affaire);
		$this->assertEquals("AR",$affaire["nature"]
			,"L'affaire ne prend pas la nature AR"
		);
		$this->assertEquals(NULL,$affaire["id_parent"]
			,"L'affaire qui AR ne doit pas avoir de id_parent"
		);
		ATF::parc()->q->reset()->addCondition("id_affaire",$id_affaire);
		$parcs=ATF::parc()->sa();

		$this->assertEquals(array("id_parc"=>$parcs[0]["id_parc"],"id_societe"=>$affaire["id_societe"],"id_produit"=>"5893","id_affaire"=>$id_affaire,"ref"=>"OptiGX520 17 DVD 48X","libelle"=>"Optiplex GX520 TFT 17 DVD 48X","divers"=>NULL,"serial"=>"5X7ZB2J","etat"=>"reloue","code"=>"23W001280206DVRO1XPP323T173#####|Office 2003 PME - Lecteur DVD 48X","date"=>$parcs[0]["date"],"date_inactif"=>date("Y-m-d"),"date_garantie"=>"2009-11-01","provenance"=>"26","existence"=>"inactif",'date_achat' => null)
			,$parcs[0]
			,"Le 1er parc ne renvoie pas les bons infos"
		);

		$this->assertEquals(array("id_parc"=>$parcs[1]["id_parc"],"id_societe"=>$affaire["id_societe"],"id_produit"=>"6066","id_affaire"=>$id_affaire,"ref"=>"DELLLAT","libelle"=>"LATITUDE D520","divers"=>NULL,"serial"=>"BBPZB2J","etat"=>"reloue","code"=>"23P007161202CDRW#XPP323T153#####|Microsoft Office 2003 Small","date"=>$parcs[1]["date"],"date_inactif"=>date("Y-m-d"),"date_garantie"=>"2009-11-01","provenance"=>"43","existence"=>"inactif",'date_achat' => null)
			,$parcs[1]
			,"Le 2ème parc ne renvoie pas les bons infos"
		);

		$a = new affaire_cleodis($id_affaire);
		$ap = $a->getParentAR($id_affaire); // Méthode pour avenant
		foreach ($ap as $a) {
			$affaires_parentes[] = new affaire_cleodis($a["id_affaire"]);
		}
		$this->assertEquals(array(
				array("id_affaire"=>26,"ref"=>"0607001")
				,array("id_affaire"=>43,"ref"=>"0610001")
			)
			,$ap
			,"Les affaires parentes ne sont pas celles attendues !"
		);
		// Tests avec mauvais parc
		$error = NULL;
		try {
			$devis["AR"] = "affaire_4358,parc_23242,affaire_4358,parc_7421,parc_7423";
			classes::decryptId(ATF::devis()->insert($devis,$this->s));
		} catch (errorATF $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(891,$error,'Erreur parc_checked_sans_affaire non declenchee AR');
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
	// Annule et remplace
	public function test_insertAvenant(){
		$devis = unserialize(self::$devis);
		$devis["panel_avenant_lignes-checkbox"] = "on";
		$devis["devis"]["RIB"]="30027 17536 00013420801 37";
				$devis["values_devis"]["produits_repris"] = '[{"devis_ligne__dot__produit":"Optiplex GX520 TFT 17 DVD 48X","devis_ligne__dot__quantite":1,"devis_ligne__dot__type":"sans_objet","devis_ligne__dot__ref":"OptiGX520 17 DVD 48X-1","devis_ligne__dot__prix_achat":"100","devis_ligne__dot__id_produit":"Optiplex GX520 TFT 17 DVD 48X","devis_ligne__dot__id_fournisseur":"DELL","devis_ligne__dot__visibilite_prix":"invisible","devis_ligne__dot__serial":"5X7ZB2J","devis_ligne__dot__id_produit_fk":"5893","devis_ligne__dot__id_parc":"17","devis_ligne__dot__id_affaire_provenance":"26","devis_ligne__dot__id_fournisseur_fk":"1351"}]';
		try {
			classes::decryptId(ATF::devis()->insert($devis,$this->s));
		} catch (errorATF $e) {
			$error1 = $e->getCode();
		}
		$this->assertEquals(879,$error1,'Erreur parc_sans_avenant non declenchee avenant');

		$devis["avenant"] = "affaire_26,affaire_43";
		try {
			classes::decryptId(ATF::devis()->insert($devis,$this->s));
		} catch (errorATF $e) {
			$error2 = $e->getCode();
		}
		$this->assertEquals(878,$error2,'Erreur parc_sans_avenant non declenchee avenant');

		$devis["avenant"] = "affaire_27";

		try {
			classes::decryptId(ATF::devis()->insert($devis,$this->s));
		} catch (errorATF $e) {
			$error2 = $e->getCode();
		}
		$this->assertEquals(891,$error2,'Erreur "parc n appertenant pas a l affaire" non declenchee avenant');

		$devis["avenant"] = "affaire_26";
		$id_devis = classes::decryptId(ATF::devis()->insert($devis,$this->s));
		$this->assertNotNull($id_devis,'Devis non créé');
		$id_affaire = ATF::devis()->select($id_devis,'id_affaire');
		$affaire=ATF::affaire()->select($id_affaire);
		$this->assertEquals("avenant",$affaire["nature"]
			,"L'affaire ne prend pas la nature avenant"
		);
		$this->assertEquals(26,$affaire["id_parent"]
			,"L'affaire avenant doit avoir un id_parent"
		);
		ATF::parc()->q->reset()->addCondition("id_affaire",$id_affaire);
		$parcs=ATF::parc()->sa();
		$this->assertEquals(array("id_parc"=>$parcs[0]["id_parc"],"id_societe"=>$affaire["id_societe"],"id_produit"=>"5893","id_affaire"=>$id_affaire,"ref"=>"OptiGX520 17 DVD 48X","libelle"=>"Optiplex GX520 TFT 17 DVD 48X","divers"=>NULL,"serial"=>"5X7ZB2J","etat"=>"broke","code"=>"23W001280206DVRO1XPP323T173#####|Office 2003 PME - Lecteur DVD 48X","date"=>$parcs[0]["date"],"date_inactif"=>date("Y-m-d"),"date_garantie"=>"2009-11-01","provenance"=>"26","existence"=>"inactif",'date_achat' => null)
			,$parcs[0]
			,"Le 1er parc ne renvoie pas les bons infos"
		);


		$devis["values_devis"]["produits_repris"] = '[{"devis_ligne__dot__produit":"LATITUDE D520","devis_ligne__dot__quantite":1,"devis_ligne__dot__type":"sans_objet","devis_ligne__dot__ref":"DELLLAT-1","devis_ligne__dot__prix_achat":"50","devis_ligne__dot__id_produit":"LATITUDE D520","devis_ligne__dot__id_fournisseur":"CBASE","devis_ligne__dot__visibilite_prix":"invisible","devis_ligne__dot__serial":"BBPZB2J","devis_ligne__dot__id_produit_fk":"6066","devis_ligne__dot__id_parc":"6995","devis_ligne__dot__id_affaire_provenance":"43","devis_ligne__dot__id_fournisseur_fk":"1349"}]';
		$devis["avenant"] = "affaire_43";
		$devis["devis"]["loyer_unique"]="oui";
		$devis["devis"]["loyers"]=1000;
		$devis["devis"]["frais_de_gestion_unique"]=10;
		$devis["devis"]["assurance_unique"]=10;
		$devis["values_devis"]["loyer"] = '[{"loyer__dot__loyer":"1000.00","loyer__dot__duree":"25","loyer__dot__assurance":"10.00","loyer__dot__frais_de_gestion":"10.00","loyer__dot__frequence_loyer":"mois","loyer__dot__loyer_total":25000}]';

		unset($devis["panel_avenant_lignes-checkbox"]);
		try {
			classes::decryptId(ATF::devis()->insert($devis,$this->s));
		} catch (errorATF $e) {
			$error3 = $e->getCode();
		}
		$this->assertEquals(881,$error3,'Erreur "Un loyer unique est forcément un avenant" non declenchee avenant');


		$devis["panel_avenant_lignes-checkbox"] = "on";
		$id_devis = classes::decryptId(ATF::devis()->insert($devis,$this->s));
		$id_affaire = ATF::devis()->select($id_devis,'id_affaire');
		ATF::loyer()->q->reset()->addCondition("id_affaire",$id_affaire);
		$loyers=ATF::loyer()->sa();
		$this->assertEquals(1
			,count($loyers)
			,"Pour un loyer unique il ne peut y avoir qu'un loyer"
		);
		$this->assertEquals(array("id_loyer"=>$loyers[0]["id_loyer"],"id_affaire"=>$id_affaire,"loyer"=>"1000.00","duree"=>1,"assurance"=>"10.00","frais_de_gestion"=>"10.00","frequence_loyer"=>"mois",'avec_option' => 'non')
			,$loyers[0]
			,"Le loyer ne renvoi pas les bonnes infos"
		);

	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
	// VENTE
	public function test_insertVente(){
		$devis = unserialize(self::$devis);
		$devis["preview"]=false;
		$devis["panel_courriel-checkbox"]="on";
		unset($devis["devis"]["email"]);
		$devis["devis"]["emailCopie"]="tucopie@absystech.fr";
		$devis["devis"]["emailTexte"]="texte tu mail";
		$devis["values_devis"]["produits_repris"] = '[{"devis_ligne__dot__produit":"Optiplex GX520 TFT 17 DVD 48X","devis_ligne__dot__quantite":1,"devis_ligne__dot__type":"sans_objet","devis_ligne__dot__ref":"OptiGX520 17 DVD 48X-1","devis_ligne__dot__prix_achat":"100","devis_ligne__dot__id_produit":"Optiplex GX520 TFT 17 DVD 48X","devis_ligne__dot__id_fournisseur":"DELL","devis_ligne__dot__visibilite_prix":"invisible","devis_ligne__dot__serial":"5X7ZB2J","devis_ligne__dot__id_produit_fk":"5893","devis_ligne__dot__id_parc":"17","devis_ligne__dot__id_affaire_provenance":"26","devis_ligne__dot__id_fournisseur_fk":"1351"}]';
		$devis["devis"]["type_contrat"] = "vente";
		$devis["devis"]["RIB"]="30027 17536 00013420801 37";
		$devis["panel_vente-checkbox"] = "on";

		$devis["devis"]["id_societe"] = 256;
		$devis["vente"] = "affaire_14035";

		try {
			classes::decryptId(ATF::devis()->insert($devis,$this->s));
		} catch (errorATF $e) {
			$error2 = $e->getCode();
		}
		$this->assertEquals(880,$error2,'Erreur "vente sans prix" non declenchee vente');

		$devis["devis"]["prix_vente"]=3000;

		try {
			classes::decryptId(ATF::devis()->insert($devis,$this->s));
		} catch (errorATF $e) {
			$error3 = $e->getCode();
		}
		$this->assertEquals(891,$error3,'Erreur "parc_checked_sans_affaire" non declenchee vente');

		$devis["vente"] = "affaire_26";

		ATF::contact()->u(array("id_contact"=>$devis["devis"]["id_contact"],"email"=>"tu@absystech.net"));

		$id_devis = classes::decryptId(ATF::devis()->insert($devis,$this->s));
		$this->assertNotNull($id_devis,'Devis non créé');
		$id_affaire = ATF::devis()->select($id_devis,'id_affaire');
		$affaire=ATF::affaire()->select($id_affaire);
		$this->assertEquals("vente",$affaire["nature"]
			,"L'affaire ne prend pas la nature vente"
		);
		ATF::parc()->q->reset()->addCondition("id_affaire",$id_affaire);
		$parcs=ATF::parc()->sa();
		$this->assertEquals(array("id_parc"=>$parcs[0]["id_parc"],"id_societe"=>$affaire["id_societe"],"id_produit"=>"5893","id_affaire"=>$id_affaire,"ref"=>"OptiGX520 17 DVD 48X","libelle"=>"Optiplex GX520 TFT 17 DVD 48X","divers"=>NULL,"serial"=>"5X7ZB2J","etat"=>"vendu","code"=>"23W001280206DVRO1XPP323T173#####|Office 2003 PME - Lecteur DVD 48X","date"=>$parcs[0]["date"],"date_inactif"=>date("Y-m-d"),"date_garantie"=>"2009-11-01","provenance"=>"26","existence"=>"inactif",'date_achat' => null)
			,$parcs[0]
			,"Le 1er parc ne renvoie pas les bons infos"
		);

		ATF::loyer()->q->reset()->addCondition("id_affaire",$id_affaire);
		$loyers=ATF::loyer()->sa();
		$this->assertEquals(1
			,count($loyers)
			,"Pour une vente il ne peut y avoir qu'un loyer"
		);
		$this->assertEquals(array("id_loyer"=>$loyers[0]["id_loyer"],"id_affaire"=>$id_affaire,"loyer"=>"3000.00","duree"=>1,"assurance"=>NULL,"frais_de_gestion"=>NULL,"frequence_loyer"=>"mois",'avec_option' => 'non')
			,$loyers[0]
			,"Le loyer ne renvoi pas les bonnes infos"
		);

	}

	/*@author Yann GAUTHERON <ygautheron@absystech.fr>  */
	public function test_getDateFinPrevue(){

		$id_affaire=ATF::affaire()->decryptId(ATF::affaire()->i(array("etat"=>"commande","date"=>date("Y-m-d"),"ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu","nature"=>"affaire")));
		$id_devis=ATF::devis()->decryptId(ATF::devis()->i(array("ref"=>"refTu","id_user"=>$this->id_user,"id_societe"=>$this->id_societe,"id_filiale"=>246,"prix"=>600,"date"=>date("Y-m-d"),"devis"=>"AffaireTu","type_contrat"=>"lld","date_accord"=>date("Y-m-d"),"etat"=>"gagne","id_contact"=>$this->id_contact,"id_affaire"=>$id_affaire,"tva"=>"1.196","loyer_unique"=>"non","prix_achat"=>0,"validite"=>date("Y-m-d"))));

		$d = new devis_cleodis($id_devis);
		$this->assertEquals("1990-01-01",$d->getDateFinPrevue("1990-01-01"),'Devis non créé en mode preview');




		$devis = unserialize(self::$devis);

		// Insertion
		unset($devis["preview"]);
		$id_devis = classes::decryptId(ATF::devis()->insert($devis,$this->s));
		$this->assertNotNull($id_devis,'Devis non créé en mode preview');

		$d = new devis_cleodis($id_devis);
		$this->assertEquals("1992-10-31",$d->getDateFinPrevue("1990-01-01"),'Devis non créé en mode preview');
	}

	/*@author Yann GAUTHERON <ygautheron@absystech.fr>  */
	public function test_canUpdateDelete(){
		// Création d'une affaire
		$devis = unserialize(self::$devis);
		$id_devis = ATF::devis()->insert($devis,$this->s);
		$this->assertNotNull($id_devis,'Devis non créé');

		$d = new devis_cleodis($id_devis);
		$d->set("etat","gagne");
		$this->assertFalse(ATF::devis()->can_delete($id_devis),'Can delete ne retourne pas false');

		try {
			ATF::devis()->can_update($id_devis);
		} catch (errorATF $e) {
			$error = $e->getCode();
		}

		$this->assertEquals(892,$error,'Can update ne doit pas se faire si etat gagne');

		$d->set("etat","attente");
		$this->assertTrue(ATF::devis()->can_update($id_devis),'Can update ne retourne pas true lorsque le devis est en attente');
	}


	/*@author Yann GAUTHERON <ygautheron@absystech.fr>  */
	public function test_defaultValues(){
		ATF::$usr->maj_infos(16);

		$this->assertEquals("",ATF::devis()->default_value("emailTexte"),'valeur emailTexte avant remplissage du request');
		$this->assertEquals(0,ATF::devis()->default_value("marge_absolue"),'valeur marge_absolue avant remplissage du request');
		$this->assertEquals(0,ATF::devis()->default_value("marge"),'valeur marge avant remplissage du request');
		$this->assertEquals(date("Y-m-d",strtotime("+15 day")),ATF::devis()->default_value("validite"),'valeur validite avant remplissage du request');
		$this->assertEquals(date("Y-m-d"),ATF::devis()->default_value("date"),'valeur date');
		$this->assertEquals("lld",ATF::devis()->default_value("type_contrat"),'valeur type_contrat avant remplissage du request');
		$this->assertEquals("1.20",ATF::devis()->default_value("tva"),'valeur tva avant remplissage du request');
		$this->assertEquals("jerome.loison@cleodis.com",ATF::devis()->default_value("emailCopie"),'valeur emailCopie avant remplissage du request');
		$this->assertEquals("246",ATF::devis()->default_value("id_filiale"),'valeur id_filiale avant remplissage du request');
		$this->assertEquals("",ATF::devis()->default_value("email"),'valeur email avant remplissage du request');
		$this->assertEquals(0,ATF::devis()->default_value("prix"),'valeur prix');
		$this->assertEquals(0,ATF::devis()->default_value("prix_achat"),'valeur prix_achat');
		$this->assertEquals("",ATF::devis()->default_value("RIB"),'valeur RIB');
		$this->assertEquals("",ATF::devis()->default_value("BIC"),'valeur BIC');
		$this->assertEquals("",ATF::devis()->default_value("IBAN"),'valeur IBAN');
		$this->assertEquals("",ATF::devis()->default_value("nom_banque"),'valeur nom_banque');
		$this->assertEquals("",ATF::devis()->default_value("ville_banque"),'valeur ville_banque');
		ATF::_r('id_societe',5391);
		ATF::societe()->u(array("id_societe"=>5391,"RIB"=>"TURIB","BIC"=>"TUBIC","IBAN"=>"TUIBAN","nom_banque"=>"TUbanque","ville_banque"=>"TUville_banque"));
		$this->assertEquals("TURIB",ATF::devis()->default_value("RIB"),'valeur RIB');
		$this->assertEquals("TUBIC",ATF::devis()->default_value("BIC"),'valeur BIC');
		$this->assertEquals("TUIBAN",ATF::devis()->default_value("IBAN"),'valeur IBAN');
		$this->assertEquals("TUbanque",ATF::devis()->default_value("nom_banque"),'valeur banque');
		$this->assertEquals("TUville_banque",ATF::devis()->default_value("ville_banque"),'valeur ville_banque');

		ATF::$codename = "cleodisbe";
		$this->assertEquals("4225",ATF::devis()->default_value("id_filiale"),'valeur id_filiale avant remplissage du request cleodis be');
		ATF::$codename = "cleodis";

		$devis = unserialize(self::$devis);
		$id_devis = classes::decryptId(ATF::devis()->insert($devis,$this->s));

		ATF::_r('id_devis',$id_devis);
		$devis = ATF::devis()->select($id_devis);
		$this->assertEquals(ATF::devis()->majMail($devis["id_societe"]),ATF::devis()->default_value("emailTexte"),'valeur emailTexte');
		$this->assertEquals("8021",ATF::devis()->default_value("marge_absolue"),'valeur marge_absolue');
		$this->assertEquals("99.96%",ATF::devis()->default_value("marge"),'valeur marge');
		$this->assertEquals("pmoons@finorpa.fr",ATF::devis()->default_value("email"),'pmoons@finorpa.fr');
		$this->assertEquals("jerome.loison@cleodis.com",ATF::devis()->default_value("emailCopie"),'valeur emailCopie');
		$this->assertEquals("8024",ATF::devis()->default_value("prix"),'valeur prix');
		$this->assertEquals("3.00",ATF::devis()->default_value("prix_achat"),'valeur prix_achat');
		$this->assertEquals("",ATF::devis()->default_value("RIB"),'valeur RIB');
		$this->assertEquals("",ATF::devis()->default_value("BIC"),'valeur BIC');
		$this->assertEquals("",ATF::devis()->default_value("IBAN"),'valeur IBAN');
		$this->assertEquals("",ATF::devis()->default_value("nom_banque"),'valeur banque');
		$this->assertEquals("",ATF::devis()->default_value("ville_banque"),'valeur ville_banque');
		$this->assertNull(ATF::devis()->default_value("prix_vente"),'valeur prix_vente');

		ATF::societe()->update(array("id_societe"=>$devis["id_societe"],"tva"=>6.66));
		ATF::_r('id_societe',$devis["id_societe"]);
		ATF::_r('id_devis',false);
		$this->assertEquals("6.66",ATF::devis()->default_value("tva"),"La valeur de la TVA n'est pas celle enregistré dans la société.");

		$devis = unserialize(self::$devis);
		$devis["values_devis"]["produits_repris"] = '[{"devis_ligne__dot__produit":"Optiplex GX520 TFT 17 DVD 48X","devis_ligne__dot__quantite":1,"devis_ligne__dot__type":"sans_objet","devis_ligne__dot__ref":"OptiGX520 17 DVD 48X-1","devis_ligne__dot__prix_achat":"100","devis_ligne__dot__id_produit":"Optiplex GX520 TFT 17 DVD 48X","devis_ligne__dot__id_fournisseur":"DELL","devis_ligne__dot__visibilite_prix":"invisible","devis_ligne__dot__serial":"5X7ZB2J","devis_ligne__dot__id_produit_fk":"5893","devis_ligne__dot__id_parc":"17","devis_ligne__dot__id_affaire_provenance":"26","devis_ligne__dot__id_fournisseur_fk":"1351"}]';
		$devis["devis"]["type_contrat"] = "vente";
		$devis["panel_vente-checkbox"] = "on";
		$devis["devis"]["prix_vente"]=3000;
		$devis["devis"]["prix"]=3000;
		$devis["vente"] = "affaire_26";
		$id_devis = classes::decryptId(ATF::devis()->insert($devis,$this->s));
		ATF::_r('id_devis',$id_devis);

		$this->assertEquals("3000.00",ATF::devis()->default_value("prix_vente"),'valeur prix_vente');


	}


	function testGetFournisseurs(){
		$devis = unserialize(self::$devis);
		$id_devis = classes::decryptId(ATF::devis()->insert($devis,$this->s));
		$this->assertEquals("DJP SERVICE INFORMATIQUE",$this->obj->getFournisseurs($id_devis,true),'getFournisseurs avec array renvoi une mauvaise valeur');
		$this->assertEquals(array(array("id_fournisseur"=>1583)),$this->obj->getFournisseurs($id_devis),'getFournisseurs avec string renvoi une mauvaise valeur');
	}

	function testPerdu(){
		$devis = unserialize(self::$devis);
		$id_devis = classes::decryptId(ATF::devis()->insert($devis,$this->s));
		$this->assertTrue($this->obj->perdu(array("id_devis"=>$id_devis)),'Problème quand on passe un devis en perdu');

		ATF::societe()->u(array("id_societe"=>$this->id_societe, "relation"=>"prospect"));

		$comite=array("id_societe"=>$this->id_societe,"id_contact"=>$devis["devis"]["id_contact"] ,"id_affaire"=>$this->obj->select($id_devis,"id_affaire"), "id_refinanceur"=>1,"date"=>date("Y-m-d"),"description"=>"description");
		$id_comite=ATF::comite()->i($comite);

		$this->assertEquals("perdu",$this->obj->select($id_devis,"etat"),'Perdu ne passe pas le devis en perdu');

		$affaire=ATF::affaire()->select(ATF::devis()->select($id_devis,'id_affaire'));
		$this->assertEquals("perdue",$affaire["etat"],'Perdu ne passe pas l affaire en perdu');
		$this->assertEquals(0,$affaire["forecast"],'Perdu ne change pas le forecast de l affaire');
		$this->assertEquals(array(0=>array("msg"=>"notice_devis_perdu","title"=>"Succès !","timer"=>""),1=>array("msg"=>"Email envoyé au(x) notifié(s)","title"=>"","timer"=>"")),ATF::$msg->getNotices(),'Perdu ne renvoi pas le bon getNotices');

		$this->obj->u(array("id_devis"=>$id_devis,"etat"=>"gagne"));
		try {
			$this->obj->perdu(array("id_devis"=>$id_devis));
		} catch (errorATF $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(899,$error,'un devis gagne ne peut etre perdu');

		$parent=array("ref"=>"fille","id_societe"=>$this->id_societe,"affaire"=>"fille");
		$id_parent=ATF::affaire()->i($parent);
		ATF::affaire()->u(array("id_affaire"=>ATF::affaire()->decryptId($id_parent),"id_fille"=>ATF::devis()->select($id_devis,'id_affaire')));

		$this->obj->u(array("id_devis"=>$id_devis,"etat"=>"attente"));

		$this->obj->perdu(array("id_devis"=>$id_devis));
		$this->assertEquals(array(0=>array("msg"=>"notice_devis_perdu","title"=>"Succès !","timer"=>""),1=>array("msg"=>"Email envoyé au(x) notifié(s)","title"=>"","timer"=>"")),ATF::$msg->getNotices(),'Perdu ne renvoi pas le bon getNotices');

		$this->assertNull(ATF::affaire()->select(ATF::affaire()->decryptId($id_parent),"id_fille"),"Une affaire perdue ne doit être la fille de personne");
	}

	function test_contactByDevis(){
		$devis = unserialize(self::$devis);
		$id_devis = classes::decryptId(ATF::devis()->insert($devis,$this->s));
		$id_contact=$this->obj->contactByDevis($this->obj->select($id_devis,"id_affaire"));
		$this->assertEquals(5753,$id_contact,'ContactByDevis ne renvoi pas le bon contact');

		$this->assertFalse($this->obj->contactByDevis(),'ContactByDevis doit renvoyer false s il n y a pas de id');
	}

	function testGetLignes(){
		$devis = unserialize(self::$devis);
		$devis["panel_AR-checkbox"] = "on";
		$devis["values_devis"]["produits_repris"] = '[{"devis_ligne__dot__produit":"Optiplex GX520 TFT 17 DVD 48X","devis_ligne__dot__quantite":1,"devis_ligne__dot__type":"sans_objet","devis_ligne__dot__ref":"OptiGX520 17 DVD 48X-1","devis_ligne__dot__prix_achat":"100","devis_ligne__dot__id_produit":"Optiplex GX520 TFT 17 DVD 48X","devis_ligne__dot__id_fournisseur":"DELL","devis_ligne__dot__serial":"5X7ZB2J","devis_ligne__dot__id_produit_fk":"5893","devis_ligne__dot__id_parc":"17","devis_ligne__dot__id_affaire_provenance":"26","devis_ligne__dot__id_fournisseur_fk":"1351","devis_ligne__dot__visibilite_prix":"invisible","devis_ligne__dot__visible":"oui"},{"devis_ligne__dot__produit":"LATITUDE D520","devis_ligne__dot__quantite":1,"devis_ligne__dot__type":"sans_objet","devis_ligne__dot__ref":"DELLLAT-1","devis_ligne__dot__prix_achat":"50","devis_ligne__dot__id_produit":"LATITUDE D520","devis_ligne__dot__id_fournisseur":"CBASE","devis_ligne__dot__serial":"BBPZB2J","devis_ligne__dot__id_produit_fk":"6066","devis_ligne__dot__id_parc":"6995","devis_ligne__dot__id_affaire_provenance":"43","devis_ligne__dot__id_fournisseur_fk":"1349","devis_ligne__dot__visibilite_prix":"invisible","devis_ligne__dot__visible":"non"}]';
		unset($devis["values_devis"]["produits"]);
		$devis["values_devis"]["produits"]='[{"devis_ligne__dot__produit":"Zywall 5 - dispositif de sécurité","devis_ligne__dot__quantite":"3","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"ZYX-FW","devis_ligne__dot__prix_achat":"1","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"<span class=\"searchSelectionFound\">D</span>JP SERVICE","devis_ligne__dot__id_produit_fk":"1175","devis_ligne__dot__id_fournisseur_fk":"1583","devis_ligne__dot__visibilite_prix":"invisible","devis_ligne__dot__visible":"oui"},{"devis_ligne__dot__produit":"Zywall 5 - dispositif de sécurité","devis_ligne__dot__quantite":"1","devis_ligne__dot__type":"portable","devis_ligne__dot__ref":"ZYX-FWvis","devis_ligne__dot__prix_achat":"1","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"<span class=\"searchSelectionFound\">D</span>JP SERVICE","devis_ligne__dot__id_produit_fk":"1175","devis_ligne__dot__id_fournisseur_fk":"1583","devis_ligne__dot__visibilite_prix":"visible","devis_ligne__dot__visible":"non"}]';
		$devis["AR"] = "affaire_26,affaire_43";
		$id_devis = classes::decryptId(ATF::devis()->insert($devis,$this->s));
		$affaire = new affaire_cleodis(ATF::devis()->select($id_devis,'id_affaire'));
		$devis = $affaire->getDevis();

		$visible=$devis->getLignes("visible");
		$this->assertEquals(1,count($visible),'GetLignes ne renvoi pas le bon nombre de lignes visibles');
		$this->assertEquals("ZYX-FW",$visible[0]["ref"],'GetLignes ne renvoi pas les bonnes lignes visibles');

		$invisible=$devis->getLignes("invisible");
		$this->assertEquals(1,count($invisible),'GetLignes ne renvoi pas le bon nombre de lignes invisible');
		$this->assertEquals("ZYX-FWvis",$invisible[0]["ref"],'GetLignes ne renvoi pas les bonnes lignes invisible');

		$reprise=$devis->getLignes("reprise");
		$this->assertEquals(2,count($reprise),'GetLignes ne renvoi pas le bon nombre de lignes reprise');
		$this->assertEquals("OptiGX520 17 DVD 48X-1",$reprise[0]["ref"],'GetLignes ne renvoi pas les bonnes lignes reprise');
		$this->assertEquals("DELLLAT-1",$reprise[1]["ref"],'GetLignes ne renvoi pas les bonnes lignes reprise');
	}

	function testUpdate(){

		$devis = unserialize(self::$devis);
		$devis["devis"]["id_opportunite"]=10;
		$id_devis = ATF::devis()->insert($devis,$this->s);
		$devis["devis"]["id_devis"]=$id_devis;
		$devis["devis"]["devis"]="DEVIS REVISE";
		$devis["devis"]["filestoattach"]["fichier_joint"]="true";

		$suivi=array("id_societe"=>$this->id_societe,"texte"=>"tu devis update","id_affaire"=>$this->obj->select($id_devis,"id_affaire"),"type_suivi"=>"Devis");
		$id_suivi=ATF::suivi()->i($suivi);
		$tache=array("id_societe"=>$this->id_societe,"tache"=>"tu devis update","horaire_debut"=>"2000-01-01 00:00:00","horaire_fin"=>"2000-01-01 00:00:00","id_affaire"=>$this->obj->select($id_devis,"id_affaire"));
		$id_tache=ATF::tache()->i($tache);

		$comite=array("id_societe"=>$this->id_societe,"id_contact"=>$devis["devis"]["id_contact"] ,"id_affaire"=>$this->obj->select($id_devis,"id_affaire"), "id_refinanceur"=>1,"date"=>date("Y-m-d"),"description"=>"description");
		$id_comite=ATF::comite()->i($comite);

		$demande_refi=array("id_societe"=>$this->id_societe,"id_contact"=>$devis["devis"]["id_contact"] ,"id_affaire"=>$this->obj->select($id_devis,"id_affaire"), "id_refinanceur"=>1,"date"=>date("Y-m-d"),"description"=>"description");
		$id_demande_refi=ATF::demande_refi()->i($demande_refi);


		$parent=array("ref"=>"parent","id_societe"=>$this->id_societe,"affaire"=>"parent");
		$id_parent=ATF::affaire()->i($parent);
		ATF::affaire()->u(array("id_affaire"=>$id_parent,"id_fille"=>ATF::devis()->select($id_devis,'id_affaire')));

		$parentAVT=array("ref"=>"parentAVT","id_societe"=>$this->id_societe,"affaire"=>"parentAVT");
		$id_parentAVT=ATF::affaire()->decryptId(ATF::affaire()->i($parentAVT));
		ATF::affaire()->u(array("id_affaire"=>$this->obj->select($id_devis,"id_affaire"),"id_parent"=>$id_parentAVT));

		$id_parc=ATF::parc()->i(array("libelle"=>"tu parc","serial"=>"TUparc","etat"=>"reloue","id_affaire"=>ATF::devis()->select($id_devis,"id_affaire")));
		$id_parcParent1=ATF::parc()->i(array("libelle"=>"tu parc","serial"=>"TUparc","etat"=>"loue","id_affaire"=>$id_parent,"existence"=>"inactif"));
		$id_parcParent2=ATF::parc()->i(array("libelle"=>"tu parc","serial"=>"TUparc","etat"=>"broke","id_affaire"=>$id_parent,"existence"=>"inactif"));
		$id_parcParentAVT=ATF::parc()->i(array("libelle"=>"tu parc","serial"=>"TUparc","etat"=>"loue","id_affaire"=>$id_parentAVT,"existence"=>"inactif"));

		$id_devis = ATF::devis()->update($devis);
		$this->assertEquals("DEVIS REVISE",ATF::devis()->select($id_devis,"devis"),'Update ne se fait pas bien');
		$this->assertEquals(ATF::suivi()->select($id_suivi,"id_affaire"),ATF::devis()->select($id_devis,"id_affaire"),'Suivi supprimer sur update devis');
		$this->assertEquals(ATF::tache()->select($id_tache,"id_affaire"),ATF::devis()->select($id_devis,"id_affaire"),'Tache supprimer sur update devis');

		$this->assertNull(ATF::parc()->select($id_parc),"le parc de l'affaire modifiée doit être supprimé");
		$this->assertNull(ATF::parc()->select($id_parcParent1),"le parc parent de l'affaire modifiée doit être supprimé");
		$this->assertNull(ATF::parc()->select($id_parcParent2),"le parc broke parent de l'affaire modifiée doit être supprimé");
		$this->assertNull(ATF::parc()->select($id_parcParentAVT),"le parc parent de l'affaire modifiée doit être supprimé");

		$devis["devis"]["id_devis"]=$id_devis;
		$devis["preview"]=false;
		$refresh = array();
		$devis["devis"]["devis"]="DEVIS REVISE pre";
		$id_devis = ATF::devis()->update($devis,$this->s,NULL,$refresh);
		$this->assertEquals("DEVIS REVISE pre",ATF::devis()->select($id_devis,"devis"),'Update ne se fait pas bien en pre');

	}

	/* @author Nicolas BERTEMONT <nbertemont@absystech.fr> */
	public function test_devMidas(){
		$dm=new devis_midas();
		$this->assertEquals('a:6:{s:9:"devis.ref";a:4:{s:4:"type";s:4:"text";s:5:"xtype";s:9:"textfield";s:9:"maxlength";s:2:"16";s:7:"default";N;}s:16:"devis.id_societe";a:4:{s:4:"type";s:3:"int";s:5:"xtype";s:11:"numberfield";s:9:"maxlength";s:1:"8";s:7:"default";N;}s:16:"devis.id_affaire";a:5:{s:4:"type";s:3:"int";s:5:"xtype";s:11:"numberfield";s:9:"maxlength";s:1:"8";s:7:"default";N;s:4:"null";b:1;}s:11:"devis.devis";a:4:{s:4:"type";s:4:"text";s:5:"xtype";s:9:"textfield";s:9:"maxlength";s:3:"255";s:7:"default";N;}s:10:"devis.etat";a:7:{s:4:"type";s:4:"enum";s:5:"xtype";s:5:"combo";s:4:"data";a:3:{i:0;s:5:"gagne";i:1;s:7:"attente";i:2;s:5:"perdu";}s:7:"default";s:7:"attente";s:4:"null";b:1;s:8:"renderer";s:4:"etat";s:5:"width";i:30;}s:10:"devis.date";a:4:{s:4:"type";s:4:"date";s:5:"xtype";s:9:"datefield";s:7:"default";N;s:4:"null";b:1;}}',serialize($dm->colonnes['fields_column']),"Le constructeur de la classe midas a changé");

	}

	public function test_select_all(){
		$c=new devis_midas();
		$c->select_all();
		$this->assertEquals("eba995fa9a2c29172094f839996689f2",md5($c->q->lastSQL),"Les conditions de filtrage ont changé ?");
	}


	public function test_export_devis_loyer(){
        ATF::devis()->q->reset()->setStrict()->where("devis.date","2015-05-01","AND",false,">=")->where("devis.date","2015-06-01","AND",false,"<=");
        $infos = ATF::devis()->sa();
        foreach ($infos as $key => $value) {
           	$infos[$key]["devis.id_affaire_fk"] = $value["id_affaire"];
        }
        ob_start();
        $this->obj->export_devis_loyer($infos , "true");
         //récupération des infos
        $fichier=ob_get_contents();
        ob_end_clean();

        $this->assertNotNull($fichier, "L'export ne s'est pas bien passé??");
	}

	public function test_export_devis_loyer2(){

		$this->obj->q =ATF::_s("pager")->create("gsa_lol_lol");
       	$this->obj->q->reset()->setStrict()->where("devis.date","2015-05-01","AND",false,">=")->where("devis.date","2015-06-01","AND",false,"<=")
       							->addField("devis.id_affaire", "devis.id_affaire_fk");

        ob_start();
        $this->obj->export_devis_loyer(array('onglet'=>"gsa_lol_lol") , "false", "false");
         //récupération des infos
        $fichier=ob_get_contents();
        ob_end_clean();

        $this->assertNotNull($fichier, "L'export ne s'est pas bien passé??");
	}


	public function test_devis_gagne_stats(){
		ATF::stats()->liste_annees["devis"] = array("2015"=>1);

		$res = $this->obj->devis_gagne_stats(true,"reseau",true,2014,1);

		$this->assertEquals(16 ,$res["dataset"]["reel"]["01"]["value"] , "Count 2013 incorrect?");
		$this->assertEquals(9 ,$res["dataset"]["reel"]["12"]["value"] , "Count 2014 incorrect?");

		ATF::stats()->liste_annees["devis"] = array("2014"=>1);
		$res = $this->obj->devis_gagne_stats(true,"autre",false,2014,3);

		ATF::stats()->liste_annees["devis"] = array("2015"=>1);
		$res = $this->obj->devis_gagne_stats(true,"autre",true,2015,3);


	}


	private function beginTransaction($codename, $begin, $commit){
		if($begin){
			ATF::db()->select_db("extranet_v3_".$codename);
	    	ATF::$codename = $codename;
	    	ATF::db()->begin_transaction(true);
		}

		if($commit){
			ATF::db()->rollback_transaction(true);
	        ATF::$codename = "cleodis";
	        ATF::db()->select_db("extranet_v3_cleodis");
		}

	}
};
?>
