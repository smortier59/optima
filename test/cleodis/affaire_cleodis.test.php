<?
class affaire_cleodis_test extends ATF_PHPUnit_Framework_TestCase {
	protected static $devis = 'a:9:{s:9:"extAction";s:5:"devis";s:9:"extMethod";s:6:"insert";s:7:"preview";s:4:"true";s:11:"label_devis";a:5:{s:10:"id_filiale";s:7:"CLEODIS";s:14:"id_opportunite";s:8:"Aucun(e)";s:10:"id_societe";s:7:"FINORPA";s:10:"id_contact";s:16:"M Philippe MOONS";s:10:"AR_societe";s:7:"FINORPA";}s:5:"devis";a:21:{s:10:"id_filiale";s:3:"246";s:5:"devis";s:2:"TU";s:3:"tva";s:5:"1.196";s:11:"date_accord";s:10:"08-02-2011";s:14:"id_opportunite";s:0:"";s:10:"id_societe";i:5391;s:12:"type_contrat";s:3:"lld";s:8:"validite";s:10:"23-02-2011";s:10:"id_contact";s:4:"5753";s:6:"loyers";s:4:"0.00";s:23:"frais_de_gestion_unique";s:4:"0.00";s:16:"assurance_unique";s:4:"0.00";s:10:"AR_societe";s:0:"";s:5:"marge";s:5:"99.96";s:13:"marge_absolue";s:8:"8 021.00";s:4:"prix";s:8:"8 024.00";s:10:"prix_achat";s:4:"3.00";s:5:"email";s:17:"pmoons@finorpa.fr";s:10:"emailTexte";s:4:"<br>";s:10:"emailCopie";s:24:"jerome.loison@cleodis.fr";s:13:"filestoattach";a:1:{s:13:"fichier_joint";s:0:"";}}s:7:"avenant";s:0:"";s:2:"AR";s:0:"";s:5:"loyer";a:1:{s:15:"frequence_loyer";s:1:"m";}s:12:"values_devis";a:2:{s:5:"loyer";s:369:"[{"loyer__dot__loyer":"233","loyer__dot__duree":"34","loyer__dot__assurance":"2","loyer__dot__frais_de_gestion":"1","loyer__dot__frequence_loyer":"mois","loyer__dot__loyer_total":8024},{"loyer__dot__loyer":666,"loyer__dot__duree":6,"loyer__dot__assurance":"2","loyer__dot__frais_de_gestion":"1","loyer__dot__frequence_loyer":"trimestre","loyer__dot__loyer_total":8024}]";s:8:"produits";s:415:"[{"devis_ligne__dot__produit":"Zywall 5 - dispositif de sécurité","devis_ligne__dot__quantite":"3","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"ZYX-FW","devis_ligne__dot__prix_achat":"1","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"<span class=\"searchSelectionFound\">D</span>JP SERVICE","devis_ligne__dot__id_produit_fk":"1175","devis_ligne__dot__id_fournisseur_fk":"1583"}]";}}';

	protected static $commande = 'a:7:{s:9:"extAction";s:8:"commande";s:9:"extMethod";s:6:"insert";s:7:"preview";s:4:"true";s:14:"label_commande";a:1:{s:10:"id_societe";s:16:"FLAT LEASE GROUP";}s:8:"commande";a:16:{s:10:"id_societe";s:4:"5392";s:4:"date";s:16:"08-02-2011 12:40";s:8:"commande";s:9:"dfgqdgqfg";s:4:"type";s:11:"prelevement";s:17:"clause_logicielle";s:3:"non";s:11:"retour_prel";s:0:"";s:13:"mise_en_place";s:0:"";s:9:"retour_pv";s:0:"";s:14:"retour_contrat";s:0:"";s:5:"marge";s:6:"100.00";s:13:"marge_absolue";s:4:"3.00";s:4:"prix";s:4:"3.00";s:10:"prix_achat";s:4:"0.00";s:8:"id_devis";s:4:"4901";s:10:"__redirect";s:5:"devis";s:10:"id_affaire";s:4:"5049";}s:12:"datecommande";a:1:{s:4:"date";s:5:"12:40";}s:15:"values_commande";a:2:{s:5:"loyer";s:185:"[{"loyer__dot__loyer":"1.00","loyer__dot__duree":"1","loyer__dot__assurance":"1.00","loyer__dot__frais_de_gestion":"1.00","loyer__dot__frequence_loyer":"t","loyer__dot__loyer_total":3}]";s:8:"produits";s:453:"[{"commande_ligne__dot__produit":"Thinkpad Z61M 9450 Core Duo T2500","commande_ligne__dot__quantite":"1.0","commande_ligne__dot__ref":"LEN-POR-THINK","commande_ligne__dot__id_fournisseur":"A2I CONCEPT","commande_ligne__dot__prix_achat":"1532.00","commande_ligne__dot__id_produit":"Thinkpad Z61M 9450 Core Duo T2500","commande_ligne__dot__id_fournisseur_fk":1366,"commande_ligne__dot__id_produit_fk":314,"commande_ligne__dot__id_commande_ligne":"56176"}]";}}';

	protected static $demande_refi = 'a:5:{s:9:"extAction";s:12:"demande_refi";s:9:"extMethod";s:6:"insert";s:12:"demande_refi";a:21:{s:10:"id_societe";s:4:"1397";s:14:"id_refinanceur";s:32:"8ae7985025c5ac77a845b8f923e5186a";s:10:"id_affaire";s:4:"4918";s:10:"id_contact";s:4:"1401";s:4:"taux";s:0:"";s:20:"pourcentage_materiel";s:1:"0";s:16:"frais_de_gestion";s:0:"";s:17:"valeur_residuelle";s:4:"0.00";s:20:"pourcentage_logiciel";s:1:"0";s:11:"coefficient";s:0:"";s:4:"prix";s:10:"136 500.00";s:7:"encours";s:0:"";s:4:"date";s:10:"28-04-2011";s:7:"reponse";s:0:"";s:15:"validite_accord";s:0:"";s:11:"description";s:22:"test ar et non visible";s:15:"marque_materiel";s:0:"";s:12:"observations";s:0:"";s:4:"etat";s:10:"en_attente";s:13:"filestoattach";a:1:{s:3:"pdf";s:0:"";}s:10:"__redirect";s:7:"affaire";}s:18:"label_demande_refi";a:2:{s:14:"id_refinanceur";s:15:"KBC BAIL France";s:10:"id_contact";s:19:"M Philippe LASSAUCE";}s:19:"values_demande_refi";a:1:{s:5:"loyer";s:193:"[{"loyer__dot__loyer":"3500.00","loyer__dot__duree":"39","loyer__dot__assurance":null,"loyer__dot__frais_de_gestion":null,"loyer__dot__frequence_loyer":"mois","loyer__dot__loyer_total":136500}]";}}';

	protected static $facture = 'a:4:{s:9:"extAction";s:7:"facture";s:9:"extMethod";s:6:"insert";s:7:"facture";a:18:{s:10:"id_societe";s:4:"1397";s:12:"type_facture";s:5:"libre";s:19:"date_previsionnelle";s:10:"28-04-2011";s:10:"id_affaire";s:4:"5079";s:13:"mode_paiement";s:11:"prelevement";s:11:"id_commande";s:32:"ca0b9e5927cbeffe146cefba13f9a34b";s:4:"date";s:10:"28-04-2011";s:15:"id_demande_refi";s:0:"";s:14:"id_refinanceur";s:0:"";s:18:"date_periode_debut";s:10:"19-04-2011";s:16:"date_periode_fin";s:10:"18-05-2011";s:4:"prix";s:8:"3 500.00";s:5:"email";s:0:"";s:10:"emailTexte";s:0:"";s:10:"emailCopie";s:24:"jerome.loison@cleodis.fr";s:13:"filestoattach";a:1:{s:13:"fichier_joint";s:0:"";}s:10:"__redirect";s:8:"commande";s:10:"prix_libre";i:1000;}s:14:"values_facture";a:4:{s:5:"loyer";s:160:"[{"loyer__dot__loyer":"3500.00","loyer__dot__duree":"39","loyer__dot__assurance":null,"loyer__dot__frais_de_gestion":null,"loyer__dot__frequence_loyer":"mois"}]";s:15:"produits_repris";s:1958:"[{"facture_ligne__dot__produit":"Thinkpad Z61M 9450 Core Duo T2500","facture_ligne__dot__quantite":"1","facture_ligne__dot__ref":"LEN-POR-THINK","facture_ligne__dot__id_fournisseur":"LIXXBAIL","facture_ligne__dot__prix_achat":null,"facture_ligne__dot__id_produit":"Thinkpad Z61M 9450 Core Duo T2500","facture_ligne__dot__serial":"05S00088PORT50002","facture_ligne__dot__id_fournisseur_fk":246,"facture_ligne__dot__id_produit_fk":15},{"facture_ligne__dot__produit":"Thinkpad Z61M 9450 Core Duo T2500","facture_ligne__dot__quantite":"1","facture_ligne__dot__ref":"LEN-POR-THINK","facture_ligne__dot__id_fournisseur":"LIXXBAIL","facture_ligne__dot__prix_achat":null,"facture_ligne__dot__id_produit":"Thinkpad Z61M 9450 Core Duo T2500","facture_ligne__dot__serial":"05S00088PORT50001","facture_ligne__dot__id_fournisseur_fk":246,"facture_ligne__dot__id_produit_fk":15},{"facture_ligne__dot__produit":"UC","facture_ligne__dot__quantite":"1","facture_ligne__dot__ref":"LEN-UC","facture_ligne__dot__id_fournisseur":"LIXXBAIL","facture_ligne__dot__prix_achat":null,"facture_ligne__dot__id_produit":"UC","facture_ligne__dot__serial":"05S00088STAT02","facture_ligne__dot__id_fournisseur_fk":246,"facture_ligne__dot__id_produit_fk":15},{"facture_ligne__dot__produit":"UC","facture_ligne__dot__quantite":"1","facture_ligne__dot__ref":"LEN-UC","facture_ligne__dot__id_fournisseur":"LIXXBAIL","facture_ligne__dot__prix_achat":null,"facture_ligne__dot__id_produit":"UC","facture_ligne__dot__serial":"05S00088STAT01","facture_ligne__dot__id_fournisseur_fk":246,"facture_ligne__dot__id_produit_fk":15},{"facture_ligne__dot__produit":"XW4305","facture_ligne__dot__quantite":"1","facture_ligne__dot__ref":"HP-WRK-XW4305","facture_ligne__dot__id_fournisseur":"LIXXBAIL","facture_ligne__dot__prix_achat":"30000.00","facture_ligne__dot__id_produit":"XW4305","facture_ligne__dot__serial":"SCFV01","facture_ligne__dot__id_fournisseur_fk":246,"facture_ligne__dot__id_produit_fk":15}]";s:8:"produits";s:419:"[{"facture_ligne__dot__produit":"Serveur PROLIANT ML 350 G7","facture_ligne__dot__quantite":"1","facture_ligne__dot__ref":"HP-SRV-PROL-9-1","facture_ligne__dot__id_fournisseur":"INMAC WSTORE","facture_ligne__dot__prix_achat":"35000.00","facture_ligne__dot__id_produit":"Serveur PROLIANT ML 350 G7","facture_ligne__dot__serial":"SRV01","facture_ligne__dot__id_fournisseur_fk":246,"facture_ligne__dot__id_produit_fk":15}]";s:20:"produits_non_visible";s:385:"[{"facture_ligne__dot__produit":"frais divers","facture_ligne__dot__quantite":"1","facture_ligne__dot__ref":"FRAIS_DIVERS","facture_ligne__dot__id_fournisseur":"LIXXBAIL","facture_ligne__dot__prix_achat":"100.00","facture_ligne__dot__id_produit":"frais divers","facture_ligne__dot__serial":"TEST2011","facture_ligne__dot__id_fournisseur_fk":246,"facture_ligne__dot__id_produit_fk":15}]";}}';

	/** Méthode pré-test, exécute avant chaque test unitaire
	* besoin d'un user pour les traduction
	*/
	public function setUp() {
		ATF::db()->begin_transaction(true);
	}

	/* Méthode post-test, exécute après chaque test unitaire */
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}


	/** Test du constructeur
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test__constructCap(){
		$c = new affaire_cap();
		$this->assertTrue($c instanceOf affaire_cap, "L'objet affaire_cap n'est pas de bon type");
	}

	/* @author Yann GAUTHERON <ygautheron@absystech.fr> */
	public function test_getCompteT(){
		$this->obj = ATF::affaire();
		$devis = unserialize(self::$devis);
		ATF::$usr = new usr(16);
		$id_devis = classes::decryptId(ATF::devis()->insert($devis,$this->s));
		$this->assertNotNull($id_devis,'Devis non créé en mode preview');

		$this->assertNull($this->obj->getCompteT($infos),"Sans affaire, le compte en T doit etre nul");

		$infos["taux"] = 13;
		$infos["id_affaire"] = ATF::devis()->select($id_devis,"id_affaire");

		$this->assertNotNull($this->obj->getCompteT($infos),"Devrait retourner du HTML (1)");

		$this->assertEquals($infos["id_affaire"],ATF::$html->getTemplateVars('id_affaire'),"id_affaire non trouvé");
		$affaire = ATF::$html->getTemplateVars('affaire');

		$this->assertTrue($affaire instanceof affaire_cleodis,"Objet affaire_cleodis incorrect");
		$this->assertEquals(3,ATF::$html->getTemplateVars('lignesTotal'),"Total de lignes non correct");
		$this->assertEquals(0,ATF::$html->getTemplateVars('facturesTotal'),"Total de factures non correct");
		$this->assertEquals(13,ATF::$html->getTemplateVars('taux'),"Taux incorrect");
		$this->assertEquals(0,ATF::$html->getTemplateVars('vr'),"Valeur résiduelle incorrecte");
		$loyer = json_decode(ATF::$html->getTemplateVars('loyerData'),true);
		unset($loyer[0]["loyer.id_loyer"],$loyer[1]["loyer.id_loyer"]);
		$this->assertEquals('[{"loyer":"233.00","duree":"34","assurance":"2.00","frais_de_gestion":"1.00","frequence_loyer":"mois","pv":9327.37},{"loyer":"666.00","duree":"6","assurance":"2.00","frais_de_gestion":"1.00","frequence_loyer":"trimestre","pv":3711.08}]',json_encode($loyer),"Loyers incorrects");
		$this->assertNull(ATF::$html->getTemplateVars('resteAFacturer'),"Total restant à facturer incorrect");
		$this->assertEquals(9324.37,ATF::$html->getTemplateVars('marge'),"Marge incorrecte");
		$this->assertEquals(99.97,ATF::$html->getTemplateVars('margePourcent'),"% Marge incorrecte");
		$this->assertEquals($infos["type"],ATF::$html->getTemplateVars('type'),"Type de compte en T incorrect");
		$this->assertEquals(13,$this->obj->select($infos["id_affaire"],"taux_refi"),"Taux de refinancement incorrect");
		$this->assertTrue($infos["display"],"display valeur incrrecte");
		$this->assertEquals(3,ATF::$html->getTemplateVars('depensesTotal'),"Dépense totale incorrecte");

		// En manager
		$infos["type"] = "manager";

		// Avec une commande
		$commande = unserialize(self::$commande);
		$commande["commande"]["id_devis"]=$id_devis;
		$commande["commande"]["id_affaire"]=$infos["id_affaire"];
		$id_commande = ATF::commande()->insert($commande,$this->s);
		$this->assertNotNull($id_commande,'Commande non créé');

		$commande = $affaire->getCommande();
		$date_debut = array(
			"value" => date("Y-m-d",strtotime(date("Y-m-01")."- 3 year - 4 month"))
			,"key" => "date_debut"
			,"id_commande" => $id_commande
		);
		$commande->updateDate($date_debut);

		// Avec une demande de refi
		$demande_refi = unserialize(self::$demande_refi);
		$demande_refi["demande_refi"]["id_affaire"]=$infos["id_affaire"];
		$demande_refi["demande_refi"]["id_refinanceur"]=2;
		$demande_refi["demande_refi"]["etat"]='valide';
		$demande_refi["demande_refi"]["valeur_residuelle"]=15;
		$id_demande_refi = ATF::demande_refi()->insert($demande_refi,$this->s);
		$this->assertNotNull($id_demande_refi,'Demande refi non créé');

		$this->assertNotNull($this->obj->getCompteT($infos),"Devrait retourner du HTML (2)");
		$this->assertEquals($infos["type"],ATF::$html->getTemplateVars('type'),"Manager : Type de compte en T incorrect");
		$this->assertEquals(15,ATF::$html->getTemplateVars('vr'),"Valeur résiduelle incorrecte");
		$this->assertEquals(0,ATF::$html->getTemplateVars('taux'),"Manager : Taux réel incorrect");
		$this->assertEquals(11918,ATF::$html->getTemplateVars('resteAFacturer'),"Manager : Total restant à facturer incorrect");
		$this->assertEquals(10521,ATF::$html->getTemplateVars('marge'),"Manager : Marge incorrecte");
		$this->assertEquals(87.29,ATF::$html->getTemplateVars('margePourcent'),"Manager : % Marge incorrecte");

		// Avec une facture
		$facture = unserialize(self::$facture);
		$facture["facture"]["type_facture"]="libre";
		$facture["facture"]["type_libre"]="normale";
		$facture["facture"]["prix_libre"]=1000;
		$facture["facture"]["id_commande"]=$id_commande;
		$facture["facture"]["id_affaire"]=$infos["id_affaire"];
		$id_facture = ATF::facture()->insert($facture,$this->s);
		unset($infos["taux"]);
		unset($infos["type"]);
		$this->assertNotNull($this->obj->getCompteT($infos),"Devrait retourner du HTML (3)");
		$this->assertEquals(12053,$this->obj->getCompteTLoyerActualise($infos),"Loyer incorrect");

		// Avec facture non parvenue négative
		$facture_non_parvenue = array(
			"ref"=>"ref FNP tu"
			,"prix"=>-55000
			,"tva"=>1.196
			,"id_affaire"=>$infos["id_affaire"]
		);
		$id_facture = ATF::facture_non_parvenue()->insert($facture_non_parvenue,$this->s);
		$this->assertNotNull($this->obj->getCompteT($infos),"Devrait retourner du HTML (4)");

		// Contrat cédé
		/*$infos["date_cession"]=date("Y-m-d",strtotime(date("Y-m-01")."- 2 month"));
		if((date("m") === "01") ||(date("m") === "06")){
			$this->assertEquals(3137.0,$this->obj->getCompteTLoyerActualise($infos),"Loyer actuel incorrect");
		}else{
			$this->assertEquals(2914.0,$this->obj->getCompteTLoyerActualise($infos),"Loyer actuel incorrect");
		}*/


		ATF::$msg->getNotices();
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
	public function test_select_all(){
		ATF::db()->rollback_transaction(true);
		$this->initUser();

		$id_affaire=$this->obj->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu"));
		$id_affaire2=$this->obj->i(array("ref"=>"refTuAR","id_societe"=>$this->id_societe,"affaire"=>"AffaireTuAR","id_fille"=>$id_affaire));
		$id_affaire3=$this->obj->i(array("ref"=>"refTuAvenant","id_societe"=>$this->id_societe,"affaire"=>"AffaireTuAvenant","id_parent"=>$id_affaire,"nature"=>"avenant"));
		$this->obj->q->reset()->addCondition("affaire.id_affaire",$id_affaire)->setCount()->addAllFields("affaire")->setStrict();
		$affaire=$this->obj->select_all();
		$this->assertEquals("",$affaire["data"][0]["parentes"],
							"Il ne devrait pas y avoir de parent !");


		$this->obj->u(array("id_affaire"=>$id_affaire,"nature"=>"AR"));
		$this->obj->q->reset()->addCondition("affaire.id_affaire",$id_affaire)->setCount()->addAllFields("affaire")->setStrict();
		$affaire2=$this->obj->select_all();
		$this->assertEquals('<a href="#affaire-select-'.$this->obj->cryptId($id_affaire2).'.html">refTuAR</a>, ',
							$affaire2["data"][0]["parentes"],
							"Les parentes ne sont pas bonnes si AR!");

		$this->obj->q->reset()->addCondition("affaire.id_affaire",$id_affaire3)->setCount()->addAllFields("affaire")->setStrict();
		$affaire3=$this->obj->select_all();
		$this->assertEquals('<a href="#affaire-select-'.$this->obj->cryptId($id_affaire).'.html">refTu</a>, ',
							$affaire3["data"][0]["parentes"],
							"Les parentes ne sont pas bonnes si avenant!");
	}


	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
	public function test_mailContact(){
		ATF::db()->rollback_transaction(true);
		$this->initUser();

		$id_societe=1397;

		$devis["devis"]=array(
								 "id_societe" => $id_societe
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

		$devis["values_devis"] = array(
             "loyer" => '[{"loyer__dot__loyer":"1000","loyer__dot__duree":"14","loyer__dot__assurance":"","loyer__dot__frais_de_gestion":"","loyer__dot__frequence_loyer":"mois","loyer__dot__loyer_total":14000}]'
            ,"produits" => '[{"devis_ligne__dot__produit":"Optiplex GX520 TFT 19","devis_ligne__dot__quantite":"1","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"DEL-WRK-OPTGX520-19","devis_ligne__dot__prix_achat":"10","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"DELL|#ref=164a1c62808dc1a3af6f7d99051db73b","devis_ligne__dot__visibilite_prix":"visible","devis_ligne__dot__id_produit_fk":"9","devis_ligne__dot__id_fournisseur_fk":"1351"},{"devis_ligne__dot__produit":"XSERIES 226","devis_ligne__dot__quantite":"1","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"O2-SRV-226-001","devis_ligne__dot__prix_achat":"3113.00","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"AUDIOPTIC TRADE SERVICES|#ref=c0529cb381c6dcf43fc554b910ce02e9","devis_ligne__dot__visibilite_prix":"visible","devis_ligne__dot__id_produit_fk":"5","devis_ligne__dot__id_fournisseur_fk":"1358"},{"devis_ligne__dot__produit":"Optiplex GX520 TFT 17","devis_ligne__dot__quantite":"2","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"DEL-WRK-OPTGX520-17","devis_ligne__dot__prix_achat":"759.00","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"DELL|#ref=164a1c62808dc1a3af6f7d99051db73b","devis_ligne__dot__visibilite_prix":"visible","devis_ligne__dot__id_produit_fk":"8","devis_ligne__dot__id_fournisseur_fk":"1351"}]'
        );

		$id_devis=classes::decryptId(ATF::devis()->insert($devis));

		$devis_select=ATF::devis()->select($id_devis);
		ATF::devis_ligne()->q->reset()->addCondition("id_devis",$id_devis);
		$devis_ligne=ATF::devis_ligne()->sa();

		$commande["commande"]=array(
								 "commande" => $devis_select["devis"]
								,"type" => "prelevement"
								,"id_societe" => $id_societe
								,"date" => date("Y-m-d")
								,"id_affaire" => $devis_select["id_affaire"]
								,"clause_logicielle" => "non"
								,"prix" => "14 000.00"
								,"prix_achat" =>"4 641.00"
								,"marge" => "66.85"
								,"marge_absolue" => "9 359.00"
								,"id_devis" => $devis_select["id_devis"]
        );

		$commande["values_commande"] = array(
			 "loyer" => '[{"loyer__dot__loyer":"1000.00","loyer__dot__duree":"14","loyer__dot__assurance":null,"loyer__dot__frais_de_gestion":null,"loyer__dot__frequence_loyer":"mois","loyer__dot__loyer_total":14000}]'
			,"produits" => '[{"commande_ligne__dot__produit":"Optiplex GX520 TFT 19","commande_ligne__dot__quantite":"1","commande_ligne__dot__ref":"DEL-WRK-OPTGX520-19","commande_ligne__dot__id_fournisseur":"DELL","commande_ligne__dot__id_fournisseur_fk":"1351","commande_ligne__dot__prix_achat":"10.00","commande_ligne__dot__id_produit":"Optiplex GX520 TFT 19","commande_ligne__dot__id_produit_fk":"9","commande_ligne__dot__id_commande_ligne":"'.$devis_ligne[0]["id_devis_ligne"].'"},{"commande_ligne__dot__produit":"XSERIES 226","commande_ligne__dot__quantite":"1","commande_ligne__dot__ref":"O2-SRV-226-001","commande_ligne__dot__id_fournisseur":"AUDIOPTIC TRADE SERVICES","commande_ligne__dot__id_fournisseur_fk":"1358","commande_ligne__dot__prix_achat":"3113.00","commande_ligne__dot__id_produit":"XSERIES 226","commande_ligne__dot__id_produit_fk":"5","commande_ligne__dot__id_commande_ligne":"'.$devis_ligne[1]["id_devis_ligne"].'"},{"commande_ligne__dot__produit":"Optiplex GX520 TFT 17","commande_ligne__dot__quantite":"2","commande_ligne__dot__ref":"DEL-WRK-OPTGX520-17","commande_ligne__dot__id_fournisseur":"DELL","commande_ligne__dot__id_fournisseur_fk":"1351","commande_ligne__dot__prix_achat":"759.00","commande_ligne__dot__id_produit":"Optiplex GX520 TFT 17","commande_ligne__dot__id_produit_fk":"8","commande_ligne__dot__id_commande_ligne":"'.$devis_ligne[2]["id_devis_ligne"].'"}]'
		);

		$id_commande=classes::decryptId(ATF::commande()->insert($commande));
		$commande_select=ATF::commande()->select($id_commande);
		ATF::commande_ligne()->q->reset()->addCondition("id_commande",$id_commande);
		$commande_ligne=ATF::commande_ligne()->sa();

		$bon_de_commande["bon_de_commande"]=array(
								 "id_societe" => $id_societe
								,"id_commande" => $id_commande
								,"id_fournisseur" => 1351
								,"id_affaire" => $devis_select["id_affaire"]
								,"bon_de_commande" => $devis_select["devis"]
								,"id_contact" => $this->id_contact
								,"prix" => "10.00"
								,"tva" =>"1.196"
								,"etat" => "envoyee"
								,"payee" => "non"
								,"date" => date("Y-m-d")
								,"destinataire" => "AXXES"
								,"adresse" => "26 rue de La Vilette - Part Dieu"
								,"adresse_2" => $devis_select["id_devis"]
								,"adresse_3" => $devis_select["id_devis"]
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

		$bon_de_commande["commandes"]="xnode-".$id_commande.",".$commande_ligne[0]["id_commande_ligne"]."";

		$refresh = array();
		$id_bon_de_commande=classes::decryptId(ATF::bon_de_commande()->insert($bon_de_commande,$this->s,NULL,$refresh));


		$email["email"]="debug@absystech.fr";
		$email["emailCopie"]="debug@absystech.fr";
		$path=array("CommandeFournisseur"=>"fichier_joint");
		$this->assertTrue($this->obj->mailContact($email,$id_bon_de_commande,"bon_de_commande",$path),"problème sur mailContact avec mail");

		unset($email["email"]);
		$email["id_contact"]=$this->id_contact;
		$email["objet"]="TU";
		$id_user=ATF::$usr->get("id_user");
		ATF::$usr->set("id_user","");
		$this->assertTrue($this->obj->mailContact($email,$id_bon_de_commande,"bon_de_commande",$path),"problème sur mailContact avec contact");
		ATF::$usr->set("id_user",$id_user);

		ATF::contact()->u(array("id_contact"=>$this->id_contact,"email"=>NULL));
		try {
			$this->begin_transaction(true);
			$this->obj->mailContact($email,$id_bon_de_commande,"bon_de_commande",$path);
		} catch (errorATF $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(349,$error,'problème sur mailContact contact sans mail');

		ATF::bon_de_commande()->u(array("id_bon_de_commande"=>$id_bon_de_commande,"id_contact"=>NULL));
		try {
			$this->begin_transaction(true);
			$this->obj->mailContact($email,$id_bon_de_commande,"bon_de_commande",$path);
		} catch (errorATF $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(350,$error,'problème sur mailContact contact sans mail et sans contact');

	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
	public function test_formateInsertUpdate(){
		ATF::db()->rollback_transaction(true);
		$this->initUser();

		$id_opportunite=ATF::opportunite()->i(array(
													"opportunite"=>"Tu Opportunite",
													"id_user"=>$this->id_user,
													"id_societe"=>$this->id_societe,
													"id_contact"=>$this->id_contact,
													"id_target"=>$this->id_user,
													"date_installation_prevu"=>date("Y-m-d",strtotime(date("Y-m-d")." - 4 month")),
													"date_installation_reel"=>date("Y-m-d",strtotime(date("Y-m-d")." - 3 month")),
													"date_livraison_prevu"=>date("Y-m-d",strtotime(date("Y-m-d")." - 2 month"))
													)
												);

		//Classique

		$infos["id_societe"]=$this->id_societe;
		$infos["nature"]="classique";
		$infos["devis"]="affaire tu";
		$infos["RIB"]="RIB TU";
		$infos["BIC"]="BIC TU";
		$infos["IBAN"]="IBAN TU";
		$infos["nom_banque"]="banque TU";
		$infos["ville_banque"]="ville_banque TU";
		$infos["date"]=date("Y-m-d");
		$infos["id_opportunite"]=$id_opportunite;

		$affaireFormateClassique=$this->obj->formateInsertUpdate($infos);
		$this->assertEquals(array(
									"id_societe"=>$this->id_societe,
									"nature"=>"classique",
									"affaire"=>"affaire tu",
									"RIB"=>"RIB TU",
									"BIC"=>"BIC TU",
									"IBAN"=>"IBAN TU",
									"nom_banque"=>"banque TU",
									"ville_banque"=>"ville_banque TU",
									"date"=>date("Y-m-d"),
									"date_installation_prevu"=>date("Y-m-d 00:00:00",strtotime(date("Y-m-d")." - 4 month")),
									"date_installation_reel"=>date("Y-m-d 00:00:00",strtotime(date("Y-m-d")." - 3 month")),
									"date_livraison_prevu"=>date("Y-m-d 00:00:00",strtotime(date("Y-m-d")." - 2 month")),
									"id_parent"=>NULL,
									"date_garantie"=>NULL,
									"ref"=>$this->obj->getRef(date("Y-m-d"))
								),
							$affaireFormateClassique,
							"Le formateInsertUpdate ne renvoie pas le bon tab classique");


		//Avenant

		$infosAvenant["id_societe"]=$this->id_societe;
		$infosAvenant["nature"]="avenant";
		$infosAvenant["devis"]="affaire tu";
		$infosAvenant["RIB"]="RIB TU";
		$infosAvenant["BIC"]="BIC TU";
		$infosAvenant["IBAN"]="IBAN TU";
		$infosAvenant["nom_banque"]="banque TU";
		$infosAvenant["ville_banque"]="ville_banque TU";
		$infosAvenant["date"]=date("Y-m-d");
		$infosAvenant["id_opportunite"]=$id_opportunite;
		$infosAvenant["id_parent"]=26;

		$affaireFormateAvenant=$this->obj->formateInsertUpdate($infosAvenant);
		$this->assertEquals(array(
									"id_societe"=>$this->id_societe,
									"nature"=>"avenant",
									"affaire"=>"affaire tu",
									"RIB"=>"RIB TU",
									"BIC"=>"BIC TU",
									"IBAN"=>"IBAN TU",
									"nom_banque"=>"banque TU",
									"ville_banque"=>"ville_banque TU",
									"date"=>date("Y-m-d"),
									"date_installation_prevu"=>date("Y-m-d 00:00:00",strtotime(date("Y-m-d")." - 4 month")),
									"date_installation_reel"=>date("Y-m-d 00:00:00",strtotime(date("Y-m-d")." - 3 month")),
									"date_livraison_prevu"=>date("Y-m-d 00:00:00",strtotime(date("Y-m-d")." - 2 month")),
									"id_parent"=>NULL,
									"date_garantie"=>"2009-11-01",
									"ref"=>$this->obj->getRefAvenant(26),
									"id_parent"=>26
								),
							$affaireFormateAvenant,
							"Le formateInsertUpdate ne renvoie pas le bon tab avenant");


		//Vente

		$infosVente["id_societe"]=$this->id_societe;
		$infosVente["nature"]="vente";
		$infosVente["devis"]="affaire tu";
		$infosVente["RIB"]="RIB TU";
		$infosVente["BIC"]="BIC TU";
		$infosVente["IBAN"]="IBAN TU";
		$infosVente["nom_banque"]="banque TU";
		$infosVente["ville_banque"]="ville_banque TU";
		$infosVente["date"]=date("Y-m-d");
		$infosVente["id_opportunite"]=$id_opportunite;
		$infosVente["id_parent"]=26;

		$affaireFormateVente=$this->obj->formateInsertUpdate($infosVente);
		$this->assertEquals(array(
									"id_societe"=>$this->id_societe,
									"nature"=>"vente",
									"affaire"=>"affaire tu",
									"RIB"=>"RIB TU",
									"BIC"=>"BIC TU",
									"IBAN"=>"IBAN TU",
									"nom_banque"=>"banque TU",
									"ville_banque"=>"ville_banque TU",
									"date"=>date("Y-m-d"),
									"date_installation_prevu"=>date("Y-m-d 00:00:00",strtotime(date("Y-m-d")." - 4 month")),
									"date_installation_reel"=>date("Y-m-d 00:00:00",strtotime(date("Y-m-d")." - 3 month")),
									"date_livraison_prevu"=>date("Y-m-d 00:00:00",strtotime(date("Y-m-d")." - 2 month")),
									"id_parent"=>NULL,
									"date_garantie"=>NULL,
									"ref"=>$this->obj->getRef(date("Y-m-d")),
									"id_parent"=>26
								),
							$affaireFormateVente,
							"Le formateInsertUpdate ne renvoie pas le bon tab vente");

	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
	public function test_getRefAvenant(){
		$this->assertEquals("0607001AVT1",
							$this->obj->getRefAvenant(26),
							"getRefAvenant marche qu'en y a qu'un");

		$this->assertEquals("0612019AVT4",
							$this->obj->getRefAvenant(164),
							"getRefAvenant marche qu'en y a qu'un");
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
	public function test_getRef(){
		$this->assertEquals("0001001",
							$this->obj->getRef("2000-01-01"),
							"getRef pas 0");

		$this->assertEquals("0609006",
							$this->obj->getRef("2006-09-01"),
							"getRef pas -10");

		$this->assertEquals("0803064",
							$this->obj->getRef("2008-03-01"),
							"getRef pas -100");

		$this->assertEquals("0808272",
							$this->obj->getRef("2008-08-01"),
							"getRef pas +100");
	}


	//@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	//@author Yann GAUTHERON <ygautheron@absystech.fr>
	public function test_updateDate(){
		ATF::db()->rollback_transaction(true);
		$this->initUser();

		$id_affaire=$this->obj->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu"));


		$infosBidon1["id_affaire"]=$id_affaire;
		$infosBidon1["field"]="date_livraison_prevu";
		$this->assertFalse($this->obj->updateDate($infosBidon1),'problème sur updateDate lorsqu il manque la value');

		$infosBidon2["value"]="2010-01-01";
		$infosBidon2["id_affaire"]=false;
		$infosBidon2["field"]="date_livraison_prevu";
		$this->assertFalse($this->obj->updateDate($infosBidon2),'problème sur updateDate lorsqu il manque l id_affaire');

		$infosBidon3["value"]="2010-01-01";
		$infosBidon3["id_affaire"]=$id_affaire;
		try {
			$this->obj->updateDate($infosBidon3);
		} catch (errorATF $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(988,$error,'problème sur updateDate lorsqu il n y a pas de field');

		$infosDate_installation_prevu["value"]="2010-01-01";
		$infosDate_installation_prevu["id_affaire"]=$id_affaire;
		$infosDate_installation_prevu["field"]="date_installation_prevu";

		$this->obj->updateDate($infosDate_installation_prevu);

		$affaire=$this->obj->select($id_affaire);
		/*$this->assertEquals(array(
						array(
							"msg" => "Email envoyé au(x) notifié(s)",
							"title" => NULL,
							"timer" => NULL
							),
						array(
							"msg" => "date_livraison_prevu_modifiee",
							"title" => NULL,
							"timer" => NULL
							),
						array(
							"msg" => "date_installation_prevu_modifiee",
							"title" => NULL,
							"timer" => NULL
							)
					),
					ATF::$msg->getNotices(),
					"Les notices ne sont pas cohérentes pas cohérente updateDate Date_installation_prevu");	*/

		$this->assertEquals("2010-01-01",
							$affaire["date_installation_prevu"],
							"updateDate ne met pas bien à jour date_installation_prevu");

		$this->assertEquals("2010-01-22",
							$affaire["date_livraison_prevu"],
							"updateDate ne met pas bien à jour date_livraison_prevu");


		$infosDate_installation_reel["value"]="2010-01-01";
		$infosDate_installation_reel["id_affaire"]=$id_affaire;
		$infosDate_installation_reel["field"]="date_installation_reel";

		try {
			$this->obj->updateDate($infosDate_installation_reel);
		} catch (errorATF $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(856,$error,'problème sur updateDate lorsqu il n y a pas de devis');

		ATF::devis()->i(array(
								"ref"=>"Ref Tu",
								"id_user"=>$this->id_user,
								"id_societe"=>$this->id_societe,
								"devis"=>"Devis Tu",
								"type_contrat"=>"lld",
								"id_contact"=>$this->id_contact,
								"tva"=>"1.196",
								"validite"=>date("Y-m-d"),
								"id_affaire"=>$id_affaire,
							)
						);

		$this->obj->updateDate($infosDate_installation_reel);

		$affaire=$this->obj->select($id_affaire);
		ATF::$msg->getNotices();
		/*$this->assertEquals(array(
						array(
							"msg" => "Garantie",
							"title" => null,
							"timer" => null,
							"type" => "success"
							),
						array(
							"msg" => "date_installation_reel_modifiee",
							"title" => null,
							"timer" => null,
							"type" => "success"
							)
					),
					ATF::$msg->getNotices(),
					"Les notices ne sont pas cohérentes pas cohérente updateDate Date_installation_reel");	*/

		$this->assertEquals("2010-01-01",
							$affaire["date_installation_prevu"],
							"updateDate ne met pas bien à jour date_installation_prevu");

		$this->assertEquals("2010-01-22",
							$affaire["date_livraison_prevu"],
							"updateDate ne met pas bien à jour date_livraison_prevu");


		$infosDate_garantie["value"]="2012-01-01";
		$infosDate_garantie["id_affaire"]=$id_affaire;
		$infosDate_garantie["field"]="date_garantie";

		$this->obj->updateDate($infosDate_garantie);
		$affaire=$this->obj->select($id_affaire);
		ATF::$msg->getNotices();
		/*
		$this->assertEquals(array(
						array(
							"msg" => "date_garantie_modifiee",
							"title" => null,
							"timer" => null,
							"type" => "success"
							)
					),
					ATF::$msg->getNotices(),
					"Les notices ne sont pas cohérentes pas cohérente updateDate Date_installation_reel");
		*/

		$this->assertEquals("2012-01-01",
							$affaire["date_garantie"],
							"updateDate ne met pas bien à jour date_garantie");

		$infosDate_livraison_prevu["value"]="2010-02-01";
		$infosDate_livraison_prevu["id_affaire"]=$id_affaire;
		$infosDate_livraison_prevu["field"]="date_livraison_prevu";

		$this->obj->updateDate($infosDate_livraison_prevu);
		$affaire=$this->obj->select($id_affaire);
		ATF::$msg->getNotices();
		/*
		$this->assertEquals(array(
						array(
							"msg" => "date_livraison_prevu_modifiee",
							"title" => null,
							"timer" => null,
							"type" => "success"
							)
					),
					ATF::$msg->getNotices(),
					"Les notices ne sont pas cohérentes pas cohérente updateDate Date_installation_reel");
		*/

		$this->assertEquals("2010-02-01",
							$affaire["date_livraison_prevu"],
							"updateDate ne met pas bien à jour date_livraison_prevu");
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
	public function test_updateFacturation(){
		ATF::db()->rollback_transaction(true);
		$this->initUser();

		$id_affaire=$this->obj->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu"));

		$infos["field"]=false;
		$infos["id_affaire"]=false;

		$this->assertFalse($this->obj->updateFacturation($infos),'problème sur updateDate lorsqu il manque la value');

		$infos["id_affaire"]=$id_affaire;
		try {
			$this->obj->updateFacturation($infos);
		} catch (errorATF $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(987,$error,'problème sur updateDate lorsqu il n y a pas de field');

		$infos["field"]="RIB";
		$infos["value"]="RIB TU";

		$this->obj->updateFacturation($infos);
		$affaire=$this->obj->select($id_affaire);

		$this->assertEquals(array(
						array(
							"msg" => "RIB_modifiee",
							"title" => null,
							"timer" => null,
							"type" => "success"
							)
					),
					ATF::$msg->getNotices(),
					"Les notices ne sont pas cohérentes pas cohérente updateFacturation RIB");

		$this->assertEquals("RIB TU",$this->obj->select($id_affaire,"RIB"),'problème sur updateDate sur RIB');


		$infos["field"]="IBAN";
		$infos["value"]="IBAN TU";

		$this->obj->updateFacturation($infos);
		$affaire=$this->obj->select($id_affaire);

		$this->assertEquals(array(
						array(
							"msg" => "IBAN_modifiee",
							"title" => null,
							"timer" => null,
							"type" => "success"
							)
					),
					ATF::$msg->getNotices(),
					"Les notices ne sont pas cohérentes pas cohérente updateFacturation IBAN");

		$this->assertEquals("IBAN TU",$this->obj->select($id_affaire,"IBAN"),'problème sur updateDate sur IBAN');


		$infos["field"]="BIC";
		$infos["value"]="BIC TU";

		$this->obj->updateFacturation($infos);
		$affaire=$this->obj->select($id_affaire);

		$this->assertEquals(array(
						array(
							"msg" => "BIC_modifiee",
							"title" => null,
							"timer" => null,
							"type" => "success"
							)
					),
					ATF::$msg->getNotices(),
					"Les notices ne sont pas cohérentes pas cohérente updateFacturation BIC");

		$this->assertEquals("BIC TU",$this->obj->select($id_affaire,"BIC"),'problème sur updateDate sur BIC');

		$infos["field"]="nom_banque";
		$infos["value"]="banque TU";

		$this->obj->updateFacturation($infos);
		$affaire=$this->obj->select($id_affaire);

		$this->assertEquals(array(
						array(
							"msg" => "nom_banque_modifiee",
							"title" => null,
							"timer" => null,
							"type" => "success"
							)
					),
					ATF::$msg->getNotices(),
					"Les notices ne sont pas cohérentes pas cohérente updateFacturation nom_banque");

		$this->assertEquals("banque TU",$this->obj->select($id_affaire,"nom_banque"),'problème sur updateDate sur nom_banque');

		$infos["field"]="ville_banque";
		$infos["value"]="ville_banque TU";

		$this->obj->updateFacturation($infos);
		$affaire=$this->obj->select($id_affaire);

		$this->assertEquals(array(
						array(
							"msg" => "ville_banque_modifiee",
							"title" => null,
							"timer" => null,
							"type" => "success"
							)
					),
					ATF::$msg->getNotices(),
					"Les notices ne sont pas cohérentes pas cohérente updateFacturation ville_banque");

		$this->assertEquals("ville_banque TU",$this->obj->select($id_affaire,"ville_banque"),'problème sur updateDate sur ville_banque');

		$infos["field"]="reference_refinanceur";
		$infos["value"]="ref TU";

		$this->obj->updateFacturation($infos);
		$affaire=$this->obj->select($id_affaire);

		$this->assertEquals(array(
						array(
							"msg" => "reference_refinanceur_modifiee",
							"title" => null,
							"timer" => null,
							"type" => "success"
							)
					),
					ATF::$msg->getNotices(),
					"Les notices ne sont pas cohérentes pas cohérente updateFacturation reference_refinanceur");

		$this->assertEquals("ref TU",$this->obj->select($id_affaire,"reference_refinanceur"),'problème sur updateDate sur reference_refinanceur');

		$infos["field"]="RUM";
		$infos["value"]="  RUM";

		$this->obj->updateFacturation($infos);
		$affaire=$this->obj->select($id_affaire);
		$this->assertEquals(array(
						array(
							"msg" => "RUM_modifiee",
							"title" => NULL,
							"timer" => NULL,
							"type" => "success"
							)
					),
					ATF::$msg->getNotices(),
					"Les notices ne sont pas cohérentes pas cohérente updateFacturation reference_refinanceur");

		$this->assertEquals("++RUM",$this->obj->select($id_affaire,"RUM"),'problème update sur RUM');

	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
	public function test_getMargeTotaleDepuisDebutAnnee(){
		$this->assertEquals(0,$this->obj->getMargeTotaleDepuisDebutAnnee(),'problème sur getMargeTotaleDepuisDebutAnnee');
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
	public function test_isAR(){
		ATF::db()->rollback_transaction(true);
		$this->initUser();

		$id_affaire=$this->obj->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu"));

		$affaire = new affaire_cleodis($id_affaire);
		$this->assertFalse($affaire->isAR(),'problème sur isAR quand ce nest pas un AR');

		$this->obj->u(array("id_affaire"=>$id_affaire,"nature"=>"AR"));

		$affaire = new affaire_cleodis($id_affaire);
		$this->assertTrue($affaire->isAR(),'problème sur isAR quand c est un AR');
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
	public function test_isAvenant(){
		ATF::db()->rollback_transaction(true);
		$this->initUser();

		$id_affaire=$this->obj->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu"));

		$affaire = new affaire_cleodis($id_affaire);
		$this->assertFalse($affaire->isAvenant(),'problème sur isAvenant quand ce nest pas un avenant');

		$this->obj->u(array("id_affaire"=>$id_affaire,"nature"=>"avenant"));

		$affaire = new affaire_cleodis($id_affaire);
		$this->assertTrue($affaire->isAvenant(),'problème sur isAvenant quand c est un avenant');
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
	public function test_getParentAvenant(){
		ATF::db()->rollback_transaction(true);
		$this->initUser();

		$this->assertFalse($this->obj->getParentAvenant("aa"),'problème sur getParentAvenant quand il n y a pas de id_affaire');

		$id_affaire=$this->obj->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu"));

		$this->assertFalse($this->obj->getParentAvenant($id_affaire),'problème sur getParentAvenant quand il n y a pas de id_parent classe');

		$affaire = new affaire_cleodis($id_affaire);

		$this->assertFalse($affaire->getParentAvenant(),'problème sur getParentAvenant quand il n y a pas de id_parent objet');

		$id_affaireAvenant=$this->obj->i(array("ref"=>"refTuAvenant","id_societe"=>$this->id_societe,"affaire"=>"AffaireTuAvenant","id_parent"=>$id_affaire));

		$this->assertTrue(is_object($this->obj->getParentAvenant($id_affaireAvenant)),'problème sur getParentAvenant quand il n y a pas de id_parent classe');

		$affaire = new affaire_cleodis($id_affaireAvenant);

		$this->assertTrue(is_object($affaire->getParentAvenant()),'problème sur getParentAvenant quand il n y a pas de id_parent objet');
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
	public function test_getParentAR(){
		ATF::db()->rollback_transaction(true);
		$this->initUser();

		$this->assertFalse($this->obj->getParentAR(),'problème sur test_getParentAR quand il n y a pas de id_affaire');

		$this->assertFalse($this->obj->getParentAR("aa"),'problème sur test_getParentAR quand il n y a pas de id_affaire');

		$id_affaire=$this->obj->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu"));

		$this->assertFalse($this->obj->getParentAR($id_affaire),'problème sur test_getParentAR quand il n y a pas de id_parent classe');

		$affaire = new affaire_cleodis($id_affaire);

		$this->assertFalse($affaire->getParentAR(),'problème sur test_getParentAR quand il n y a pas de id_parent objet');

		$id_affaireAR=$this->obj->i(array("ref"=>"refTuAR","id_societe"=>$this->id_societe,"affaire"=>"AffaireTuAR","id_parent"=>$id_affaire,"id_fille"=>$id_affaire));
		$this->assertEquals(array(array("id_affaire"=>$id_affaireAR,"ref"=>"refTuAR")),$this->obj->getParentAR($id_affaire),'problème sur getParentAR quand il n y a pas de id_parent class');

		$affaire = new affaire_cleodis($id_affaire);
		$this->assertEquals(array(array("id_affaire"=>$id_affaireAR,"ref"=>"refTuAR")),$affaire->getParentAR(),'problème sur getParentAR quand il n y a pas de id_parent objet');
	}


	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
	public function test_getCommande(){
		ATF::db()->rollback_transaction(true);
		$this->initUser();

		$this->assertFalse($this->obj->getCommande(),'problème sur getCommande quand il n y a pas de id_affaire');

		$this->assertFalse($this->obj->getCommande("aa"),'problème sur getCommande quand il n y a pas de id_affaire');

		$id_affaire=$this->obj->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu"));

		$this->assertFalse($this->obj->getCommande($id_affaire),'problème sur getCommande quand il n y a pas de id_parent classe');

		$affaire = new affaire_cleodis($id_affaire);

		$this->assertFalse($affaire->getCommande(),'problème sur getCommande quand il n y a pas de id_parent objet');

		ATF::commande()->i(array("ref"=>"Ref tu","id_societe"=>$this->id_societe,"id_user"=>$this->id_user,"tva"=>"1,196","id_affaire"=>$id_affaire));

		$this->assertTrue(is_object($this->obj->getCommande($id_affaire)),'problème sur getCommande quand il n y a pas de id_parent classe');

		$affaire = new affaire_cleodis($id_affaire);

		$this->assertTrue(is_object($affaire->getCommande()),'problème sur getCommande quand il n y a pas de id_parent objet');
	}


	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
	public function test_getDevis(){
		ATF::db()->rollback_transaction(true);
		$this->initUser();

		$this->assertFalse($this->obj->getDevis(),'problème sur getDevis quand il n y a pas de id_affaire');

		$this->assertFalse($this->obj->getDevis("aa"),'problème sur getDevis quand il n y a pas de id_affaire');

		$id_affaire=$this->obj->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu"));

		$this->assertFalse($this->obj->getDevis($id_affaire),'problème sur getDevis quand il n y a pas de id_parent classe');

		$affaire = new affaire_cleodis($id_affaire);

		$this->assertFalse($affaire->getDevis(),'problème sur getDevis quand il n y a pas de id_parent objet');

		ATF::devis()->i(array(
								"ref"=>"Ref Tu",
								"id_user"=>$this->id_user,
								"id_societe"=>$this->id_societe,
								"devis"=>"Devis Tu",
								"type_contrat"=>"lld",
								"id_contact"=>$this->id_contact,
								"tva"=>"1.196",
								"validite"=>date("Y-m-d"),
								"id_affaire"=>$id_affaire,
							)
						);

		$this->assertTrue(is_object($this->obj->getDevis($id_affaire)),'problème sur getDevis quand il n y a pas de id_parent classe');

		$affaire = new affaire_cleodis($id_affaire);

		$this->assertTrue(is_object($affaire->getDevis()),'problème sur getDevis quand il n y a pas de id_parent objet');
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
	public function test_getProlongation(){
		ATF::db()->rollback_transaction(true);
		$this->initUser();

		$this->assertFalse($this->obj->getProlongation(),'problème sur getProlongation quand il n y a pas de id_affaire');

		$this->assertFalse($this->obj->getProlongation("aa"),'problème sur getProlongation quand il n y a pas de id_affaire');

		$id_affaire=$this->obj->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu"));

		$this->assertFalse($this->obj->getProlongation($id_affaire),'problème sur getProlongation quand il n y a pas de id_parent classe');

		$affaire = new affaire_cleodis($id_affaire);

		$this->assertFalse($affaire->getProlongation(),'problème sur getProlongation quand il n y a pas de id_parent objet');

		ATF::prolongation()->i(array(
								"ref"=>"Ref Tu",
								"id_societe"=>$this->id_societe,
								"id_affaire"=>$id_affaire
							)
						);

		$this->assertTrue(is_object($this->obj->getProlongation($id_affaire)),'problème sur getProlongation quand il n y a pas de id_parent classe');

		$affaire = new affaire_cleodis($id_affaire);

		$this->assertTrue(is_object($affaire->getProlongation()),'problème sur getProlongation quand il n y a pas de id_parent objet');
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
	public function test_getDemandeRefiValidee(){
		ATF::db()->rollback_transaction(true);
		$this->initUser();

		$this->assertFalse($this->obj->getDemandeRefiValidee(),'problème sur getDemandeRefiValidee quand il n y a pas de id_affaire');

		$this->assertFalse($this->obj->getDemandeRefiValidee("aa"),'problème sur getDemandeRefiValidee quand il n y a pas de id_affaire');

		$id_affaire=$this->obj->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu"));

		$this->assertFalse($this->obj->getDemandeRefiValidee($id_affaire),'problème sur getDemandeRefiValidee quand il n y a pas de id_parent classe');

		$affaire = new affaire_cleodis($id_affaire);

		$this->assertFalse($affaire->getDemandeRefiValidee(),'problème sur getDemandeRefiValidee quand il n y a pas de id_parent objet');

		ATF::demande_refi()->i(array(
								"date"=>date("Y-m-d"),
								"id_refinanceur"=>1,
								"id_societe"=>$this->id_societe,
								"id_contact"=>$this->id_contact,
								"description"=>"Description Tu",
								"etat"=>"valide",
								"id_affaire"=>$id_affaire
							)
						);

		$this->assertTrue(is_object($this->obj->getDemandeRefiValidee($id_affaire)),'problème sur getDemandeRefiValidee quand il n y a pas de id_parent classe');

		$affaire = new affaire_cleodis($id_affaire);

		$this->assertTrue(is_object($affaire->getDemandeRefiValidee()),'problème sur getDemandeRefiValidee quand il n y a pas de id_parent objet');
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
	public function test_set(){
		ATF::db()->rollback_transaction(true);
		$this->initUser();

		$id_affaire=$this->obj->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu"));
		$affaire = new affaire_cleodis($id_affaire);

		$affaire->set("etat","perdue");
		$this->assertEquals("perdue",$this->obj->select($id_affaire,"etat"),'set ne modifie pas bien l etat');

		$this->assertEquals(array(
						array(
							"msg" => "L'état de l'affaire 'refTu' a changé de 'Devis' à 'Perdue'",
							"title" => null,
							"timer" => null,
							"type" => "success"
							)
					),
					ATF::$msg->getNotices(),
					"Les notices ne sont pas cohérentes pas cohérente set etat");

		$affaire->set("nature","AR");
		$this->assertEquals("AR",$this->obj->select($id_affaire,"nature"),'set ne modifie pas bien la nature');

		$this->assertEquals(array(
						array(
							"msg" => "La nature de l'affaire 'refTu' a changé de 'Affaire' à 'AR'",
							"title" => null,
							"timer" => null,
							"type" => "success"
							)
					),
					ATF::$msg->getNotices(),
					"Les notices ne sont pas cohérentes pas cohérente set etat nature");


		$affaire->set("ref","refTu1");
		$this->assertEquals("refTu1",$this->obj->select($id_affaire,"ref"),'set ne modifie pas bien normale');

		$this->assertEquals(array(),ATF::$msg->getNotices(),"Les notices ne sont pas cohérentes pas cohérente");
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
	public function test_getDateLivraison(){
		$this->assertEquals("2010-01-22",$this->obj->getDateLivraison("2010-01-01"),"getDateLivraison ne fonctionne pas si pas de date");
		$this->assertEquals("2010-01-11",$this->obj->getDateLivraison("2010-01-01",10),"getDateLivraison ne fonctionne pas si pas de date");
	}

//	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
//	public function test_majGarantieParc(){
//
//		ATF::db()->rollback_transaction(true);
//		$this->initUser();
//
//		$id_affaire=$this->obj->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu"));
//		$id_parc1=ATF::parc()->i(array("id_affaire"=>$id_affaire,"libelle"=>"parc tu","serial"=>"parc tu","etat"=>"broke"));
//		$id_parc2=ATF::parc()->i(array("id_affaire"=>$id_affaire,"libelle"=>"parc tu","serial"=>"parc tu","etat"=>"broke","provenance"=>$id_affaire));
//
//		$affaire = new affaire_cleodis($id_affaire);
//		$affaire->majGarantieParc("2010-01-01");
//
//		$this->assertEquals("2010-01-01",ATF::parc()->select($id_parc1,"date_garantie"),"majGarantieParc ne met pas à jour la garantie");
//		$this->assertEquals("",ATF::parc()->select($id_parc2,"date_garantie"),"majGarantieParc ne devrait pas mettre à jour car il y a une provenance");
//	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
	public function test_majForecastProcess(){

		ATF::db()->rollback_transaction(true);
		$this->initUser();

		$id_affaire=$this->obj->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu"));
		$affaire = new affaire_cleodis($id_affaire);

		$this->assertFalse($affaire->majForecastProcess(),"majForecastProcess ne devrait rien renvoyer");

		$this->obj->u(array("id_affaire"=>$id_affaire,"date_installation_prevu"=>date("Y-m-d",strtotime(date("Y-m-d")."-28 day"))));

		ATF::devis()->i(array(
								"ref"=>"Ref Tu",
								"id_user"=>$this->id_user,
								"id_societe"=>$this->id_societe,
								"devis"=>"Devis Tu",
								"type_contrat"=>"lld",
								"id_contact"=>$this->id_contact,
								"tva"=>"1.196",
								"validite"=>date("Y-m-d"),
								"id_affaire"=>$id_affaire,
							)
						);

		ATF::commande()->i(array(
									"ref"=>"Ref tu",
									"id_societe"=>$this->id_societe,
									"id_user"=>$this->id_user,
									"tva"=>"1,196",
									"id_affaire"=>$id_affaire,
									"retour_contrat"=>date("Y-m-d",strtotime(date("Y-m-d")."-1 day")),
									"date_debut"=>date("Y-m-d",strtotime(date("Y-m-d")."-1 day")),
									"date_evolution"=>date("Y-m-d",strtotime(date("Y-m-d")."+1 day"))
								)
							);

		ATF::demande_refi()->i(array(
								"date"=>date("Y-m-d"),
								"id_refinanceur"=>1,
								"id_societe"=>$this->id_societe,
								"id_contact"=>$this->id_contact,
								"description"=>"Description Tu",
								"etat"=>"valide",
								"id_affaire"=>$id_affaire
							)
						);

		$affaire = new affaire_cleodis($id_affaire);
		$this->assertTrue($affaire->majForecastProcess(),"majForecastProcess ne devrait renvoyer true");
		$this->assertEquals(100,$this->obj->select($id_affaire,"forecast"),"ajForecastProcess ne devrait rien renvoyer");
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
	public function test_num_avenant(){

		ATF::db()->rollback_transaction(true);
		$this->initUser();

		$id_affaire=$this->obj->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu"));
		$id_affaire2=$this->obj->i(array("ref"=>"refTuAVT1","id_societe"=>$this->id_societe,"affaire"=>"AffaireTuParent1","id_parent"=>$id_affaire));
		$this->assertEquals(1,$this->obj->num_avenant("refTuAVT1"),"num avenant ne renvoie pas le bon résultat quand 1 parent");

		$id_affaire3=$this->obj->i(array("ref"=>"refTuAVT2","id_societe"=>$this->id_societe,"affaire"=>"AffaireTuParent2","id_parent"=>$id_affaire));
		$this->assertEquals(2,$this->obj->num_avenant("refTuAVT2"),"num avenant ne renvoie pas le bon résultat quand plusieurs parents");

	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
	public function test_refiValid(){

		ATF::db()->rollback_transaction(true);
		$this->initUser();

		$id_affaire=$this->obj->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu"));

		$id_demande_refi=ATF::demande_refi()->i(array(
								"date"=>date("Y-m-d"),
								"id_refinanceur"=>1,
								"id_societe"=>$this->id_societe,
								"id_contact"=>$this->id_contact,
								"description"=>"Description Tu",
								"etat"=>"valide",
								"id_affaire"=>$id_affaire
							)
						);

		$refiValid=$this->obj->refiValid($id_affaire);
		$this->assertEquals($id_demande_refi,$refiValid["id_demande_refi"],"refiValid ne renvoie pas la bonne demande_refi");

	}

	public function test_getFilles(){

		ATF::db()->rollback_transaction(true);
		$this->initUser();

		$this->assertFalse($this->obj->getFilles($id_affaire2),"Probleme getFilles sans id_affaire");

		$id_affaire1=$this->obj->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu"));

		$this->assertFalse($this->obj->getFilles($id_affaire1),"Probleme getFilles sans fille");

		$id_affaire2=$this->obj->i(array("ref"=>"refTu2","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu","id_fille"=>$id_affaire1));

		$id_affaire3=$this->obj->i(array("ref"=>"refTu3","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu","id_parent"=>$id_affaire2));

		$affaire = new affaire_cleodis($id_affaire2);

		$this->assertEquals(array(0=>array("id_affaire"=>$id_affaire1,"ref"=>"refTu"),1=>array("id_affaire"=>$id_affaire3,"ref"=>"refTu3")),$affaire->getFilles(),"Probleme mauvaises affaires filles");
	}


	public function test_getFillesAR(){

		ATF::db()->rollback_transaction(true);
		$this->initUser();

		$this->assertFalse($this->obj->getFillesAR($id_affaire2),"Probleme getFilles sans id_affaire");

		$id_affaire1=$this->obj->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu"));

		$this->assertFalse($this->obj->getFillesAR($id_affaire1),"Probleme getFilles sans fille");

		$id_affaire2=$this->obj->i(array("ref"=>"refTu2","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu","id_fille"=>$id_affaire1));

		$id_affaire3=$this->obj->i(array("ref"=>"refTu3","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu","id_parent"=>$id_affaire2));

		$affaire = new affaire_cleodis($id_affaire2);
		$this->assertEquals($this->obj->select($id_affaire1),$affaire->getFillesAR(),"$id_affaire 2");
	}

	/* @author Nicolas BERTEMONT <nbertemont@absystech.fr> */
	public function testMidas_select_all(){
		$id_soc=ATF::societe()->i(array("societe"=>"lol","code_client"=>"M7777","divers_3"=>"Midas"));

		$id_af=$this->obj->i(array("ref"=>132485,"id_societe"=>$id_soc,"affaire"=>"lol"));

		ATF::loyer()->i(array("id_affaire"=>$id_af,"loyer"=>123,"duree"=>2,"assurance"=>27));
		$c=new affaire_midas();
		$c->q->setLimit(1)->addOrder("affaire.id_affaire","desc");
		$recup=$c->select_all();

		$this->assertEquals("150.00",$recup[0]['dernier_loyer'],"Le loyer calculé est incorrecte");
	}

	/* @author Nicolas BERTEMONT <nbertemont@absystech.fr> */
	public function test_selectAllFranchCours(){
		$c=new affaire_midas();
		$c->selectAllFranchCours();
		$this->assertEquals("155b9cd12cd9cc13300672601ef6d948",md5(serialize($c->q->getWhere())),"Les conditions de filtrage ont changé ?");
	}

	/* @author Nicolas BERTEMONT <nbertemont@absystech.fr> */
	public function test_selectAllFranchCoursInfo(){
		$c=new affaire_midas();
		$c->selectAllFranchCoursInfo();
		$this->assertEquals("b11856db39f9734b1c2499ae240273f5",md5(serialize($c->q->lastSQL)),"Les conditions de filtrage ont changé ?");
	}

	/* @author Nicolas BERTEMONT <nbertemont@absystech.fr> */
	public function test_selectAllSucCours(){
		$c=new affaire_midas();
		$c->selectAllSucCours();
		$this->assertEquals("fa4024742454e43a364f64d5f2dba694",md5(serialize($c->q->getWhere())),"Les conditions de filtrage ont changé ?");
	}

	/* @author Nicolas BERTEMONT <nbertemont@absystech.fr> */
	public function test_selectAllSucCoursInfo(){
		$c=new affaire_midas();
		$c->selectAllSucCoursInfo();
		$this->assertEquals("a14a58fe5ad9970b0a6cfe6d007cafa7",md5(serialize($c->q->lastSQL)),"Les conditions de filtrage ont changé ?");
	}

	/*@author Morgan FLEURQUIN <mfleurquin@absystech.fr>*/
	public function test_getPourcentagesMateriel(){
		$devis = unserialize(self::$devis);
		$id_devis = classes::decryptId(ATF::devis()->insert($devis,$this->s));

		$id_affaire = ATF::devis()->select($id_devis , "id_affaire");

		$res = $this->obj->getPourcentagesMateriel($id_affaire);

		$this->assertEquals(3 , $res["mat"], "Total incorrect");
		$this->assertEquals(100 , $res["pourcentagesMat"], "Total 2 incorrect");

	}

	/*@author Morgan FLEURQUIN <mfleurquin@absystech.fr>*/
	public function test_prixTotal(){
		$devis = unserialize(self::$devis);
		$id_devis = classes::decryptId(ATF::devis()->insert($devis,$this->s));

		$id_affaire = ATF::devis()->select($id_devis , "id_affaire");

		$id_prolongation = ATF::prolongation()->i(array("id_affaire"=> $id_affaire,
													   "ref" => "Prol TU",
													   "id_refinanceur"=>4,
													   "id_societe"=>$devis["devis"]["id_societe"]
													  ));


		ATF::loyer_prolongation()->i(array("id_affaire"=> $id_affaire,
										   "id_prolongation"=>$id_prolongation,
										   "loyer"=>200,
										   "duree"=>25,
										   "assurance"=>NULL,
										   "frais_de_gestion"=>20
										  ));



		$res = ATF::loyer_prolongation()->prixTotal($id_affaire);

		$this->assertEquals(5500 , $res , "PrixTotal incorrect");

	}
	/**
	 * @author Cyril CHARLIER <ccharlier@absystech.fr>
	 */
	public function test_CreateAffairePartenaire(){
		$id_soc=ATF::societe()->i(array("societe"=>"myTest","code_client"=>"M12341"));

		$contact = ATF::contact()->i(
			array(
				"nom"=> "TU affaire",
				"prenom"=> "affaire new",
				"nom"=> "TU affaire",
				'id_societe' => $id_soc
			)
		);
		ATF::user()->q->reset()->Where('login','partenaire');
		$user = ATF::user()->select_row();

		ATF::$usr->set('contact', array(
			"id_contact"=> $contact,
			"id_user"=> $user['id_user'],
			"id_societe"=> $id_soc
		));
	 	$gerant =ATF::contact()->i(array(
	 		"id_societe" => $id_soc,
	 		"nom" => 'mister Test',
	 	));
		$fab = ATF::fabriquant()->i(array('fabriquant' =>'test fabriquant'));
		$cat = ATF::categorie()->i(array('categorie' =>'test categorie'));
		$sousCat = ATF::sous_categorie()->i(array('sous_categorie' =>'test sous categorie','id_categorie'=>$cat));
		$id_produit = ATF::produit()->insert(array(
			"ref"=>"Test produit",
			"produit"=>"Produit",
			"prix_achat"=>500,
			"id_fabriquant"=> $fab,
			"id_sous_categorie"=>$sousCat
		));
		$post = array(
			'id_societe'=> $id_soc,
			'gerant'=> $gerant,
			'loyer'=> 120,
			'duree'=> 36,
			'libelle'=> 'Test Test',
			'id_produit'=>$id_produit,
		);
		$file = __ABSOLUTE_PATH__."test/cleodis/pdf_exemple.pdf";
 		$files = array(
 			"devis_file"=> array(
				"name"=>"pdf_exemple"
				,"type"=>"application/pdf"
				,"tmp_name"=>$file
				,"error"=>0
				,"size"=>filesize($file)
			)
		);
		$ret = ATF::affaire()->_CreateAffairePartenaire(false,$post,$files);
		$this->assertEquals($ret["result"],true,'doit retourner true');

		$post['gerant'] = "0";
		$post["nom_gerant"] = "tutu";
		$post["prenom_gerant"] ="utut";
		$post["email_gerant"] = "tu@absystech.fr";
		$post["fonction_gerant"] = "testeur";

		$ret = ATF::affaire()->_CreateAffairePartenaire(false,$post,$files);
		$this->assertEquals($ret["result"],true,'doit retourner true');




		$id_soc2=ATF::societe()->i(
			array(
				"societe"=>"myTest",
				"code_client"=>"M12341",
				"cs_score"=>75,
				'date_creation'=> date("Y-m-d", strtotime("-10 years")) 
			)
		);
		$post = array(
			'libelle'=> 'Test Test',
		);
		try{
			$ret2 = ATF::affaire()->_CreateAffairePartenaire(false,$post);
		}catch(errorATF $e){
			$erreur = $e->getErrno();
		}
		$this->assertEquals($erreur,600,'doit retourner une erreur 600');


	}
	/**
	 * @author Cyril CHARLIER <ccharlier@absystech.fr>
	 */
	public function test_GetAffairePartenaire(){
		$id_soc=ATF::societe()->i(array("societe"=>"myTest","code_client"=>"M12341"));
		$id_soc2=ATF::societe()->i(array("societe"=>"myTest","code_client"=>"M12341"));
		ATF::user()->q->reset()->Where('login','partenaire');
		$user = ATF::user()->select_row();
		$contact = ATF::contact()->i(
			array(
				"nom"=> "TU affaire",
				"prenom"=> "affaire new",
				"nom"=> "TU affaire",
				"id_societe" => $id_soc

			)
		);
		$contact2 = ATF::contact()->i(
			array(
				"nom"=> "TU affaire 2",
				"prenom"=> "affaire new",
				"nom"=> "TU affaire 2",
				"id_societe" => $id_soc2

			)
		);
	 	$gerant =ATF::contact()->i(array(
	 		"id_societe" => $id_soc,
	 		"nom" => 'mister Test',
	 	));
		$fab = ATF::fabriquant()->i(array('fabriquant' =>'test fabriquant'));
		$cat = ATF::categorie()->i(array('categorie' =>'test categorie'));
		$sousCat = ATF::sous_categorie()->i(array('sous_categorie' =>'test sous categorie','id_categorie'=>$cat));
		$id_produit = ATF::produit()->insert(array(
			"ref"=>"Test produit",
			"produit"=>"Produit",
			"prix_achat"=>500,
			"id_fabriquant"=> $fab,
			"id_sous_categorie"=>$sousCat
		));
		$post = array(
			'id_societe'=> $id_soc,
			'gerant'=> $gerant,
			'loyer'=> 120,
			'duree'=> 36,
			'libelle'=> 'Test Test',
			'id_produit'=>$id_produit,
		);
		$file = __ABSOLUTE_PATH__."test/cleodis/pdf_exemple.pdf";
 		$files = array(
 			"devis_file"=> array(
				"name"=>"pdf_exemple"
				,"type"=>"application/pdf"
				,"tmp_name"=>$file
				,"error"=>0
				,"size"=>filesize($file)
			)
		);
		ATF::$usr->set('contact', array());

 		try{
			ATF::affaire()->_affairePartenaire();
 		}catch(errorATF $e){
 			$msg = $e->getMessage();
 		}
 		$this->assertEquals("Probleme d'apporteur", $msg , "Probleme d'apporteur");
		ATF::$usr->set('contact', array(
			"id_contact"=> $contact,
			"id_user"=> $user['id_user'],
			"id_societe"=> $id_soc
		));
		ATF::affaire()->_CreateAffairePartenaire(false,$post,$files);
		ATF::$usr->set('contact', array(
			"id_contact"=> $contact2,
			"id_user"=> $user['id_user'],
			"id_societe"=> $id_soc2
		));

		ATF::affaire()->_CreateAffairePartenaire(false,$post,$files);
		$post['libelle'] = 'azertyuiop';
		$aff_crypt = ATF::affaire()->_CreateAffairePartenaire(false,$post,$files);
		$ret = ATF::affaire()->_affairePartenaire();
		$this->assertEquals(2, count($ret), "Probleme nombre affaire retournées");
		$this->assertEquals("Test Test", $ret[0]["affaire"], "Probleme affaire retournée");
		$this->assertEquals("36", $ret[0]["duree"], "Probleme duree affaire retournée");


		ATF::affaire()->u(
			array(
				"id_affaire"=> ATF::affaire()->decryptId($aff_crypt['id_crypt']),
				"date"=>date("Y-m-d", strtotime("-5 day"))
			)
		);

		$get= array("filters"=> array(
			'startdate'=> date("Y-m-d", strtotime("-1 day")),
			'enddate'=> date("Y-m-d", strtotime("+1 day"))
		));
		$retFilter = ATF::affaire()->_affairePartenaire($get);
		$this->assertEquals(1, count($retFilter), "Probleme nombre affaire retournées");

		$get= array("id_affaire"=> ATF::affaire()->decryptId($aff_crypt['id_crypt']));
		$ret2 = ATF::affaire()->_affairePartenaire($get);
		$this->assertEquals("azertyuiop", $ret2["affaire"], "Probleme affaire retournée");
		


		// créer une commande sur une affaire		
		ATF::commande()->i(array(
			'id_user' => $user['id_user'],
			'id_affaire'=>ATF::affaire()->decryptId($aff_crypt['id_crypt']),
			'ref'=> "TestRef",
			'tva' => "15.2",
			"id_societe"=> $id_soc2
		));
		$get= array("search"=> 'azertyuiop');
		$ret3 = ATF::affaire()->_affairePartenaire($get);
		$this->assertEquals(1, count($ret3), "Probleme nombre affaire retournées");
		$this->assertEquals("azertyuiop", $ret3[0]["affaire"], "Probleme affaire retournée");
		$this->assertFalse($ret3[0]["contrat_signe"], "Probleme contrat signé retourné");
		$this->assertFalse($ret3[0]["retourPV"], "Probleme retourPV retourné");
	}
	/**
	 * @author Cyril CHARLIER <ccharlier@absystech.fr>
	 */
	public function test_AffaireParc(){
		$id_soc=ATF::societe()->i(array("societe"=>"myTest","code_client"=>"M12341"));
		// gestion des erreurs
		ATF::$usr->set("contact","");
		try{
			ATF::affaire()->_AffaireParc();
		}catch(errorATF $e){
			$erreur = $e->getErrno();
			$message = $e->getMessage();
		}
		$this->assertEquals($erreur,500,'doit retourner une erreur 500');
		$this->assertEquals($erreur,500,'Probleme d\'apporteur');
	
		$id_soc=ATF::societe()->i(array("societe"=>"myTest","code_client"=>"M12341"));
		$contact = ATF::contact()->i(
			array(
				"nom"=> "TU affaire",
				"prenom"=> "affaire new",
				"nom"=> "TU affaire",
				'id_societe' => $id_soc
			)
		);

		// avec  resultat vide
		ATF::user()->q->reset()->Where('login','partenaire');
		$user = ATF::user()->select_row();

		ATF::$usr->set('contact', array(
			"id_contact"=> $contact,
			'id_societe' => $id_soc

		));
		$ret = ATF::affaire()->_AffaireParc();
		$this->assertEquals(count($ret),0, 'Devrait retourner 0');
		// test a finir -> créer des affaires de differentes societes qui sont en cours
		// avoir des bdc passés auprès des fournisseurs 


	}
}