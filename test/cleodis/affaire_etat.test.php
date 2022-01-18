<?
/**
 * @testdox Etat des affaires
 */
class affaire_etat_test extends ATF_PHPUnit_Framework_TestCase {
	/** Méthode pré-test, exécute avant chaque test unitaire
	* besoin d'un user pour les traduction
	*/
	public function setUp() {
		$this->initUser();
	}

	/* Méthode post-test, exécute après chaque test unitaire */
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}

	public function initAffaire(){
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
		$this->id_affaire = ATF::devis()->select($this->id_devis, "id_affaire");
	}

	/** Test du constructeur
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @testdox Constructeur
	*/
	public function test__construct(){
		$c = new affaire_etat();
		$this->assertTrue($c instanceOf affaire_etat, "L'objet affaire_etat n'est pas de bon type");
	}

	/**
	* @testdox Méthode _GET
	*/
	public function test__GET(){
		$this->initAffaire();
		$this->obj = ATF::affaire_etat();

		$res = ATF::affaire_etat()->_GET(array("id_affaire"=>$this->id_affaire ));
		log::logger($res , "mfleurquin");
		$this->assertEquals($res, false, "Un affaire etat sur une affaire créée ?");

		/*
		$this->assertEquals("reception_demande", $res[0]["etat"], "Retour GET incorrect 1");
		$this->assertEquals("2017-10-18 17:15:15", $res[0]["date"], "Retour GET incorrect 2");
		$this->assertEquals($this->id_affaire, $res[0]["id_affaire"], "Retour GET incorrect 3");*/

	}

	/**
	* @testdox Méthode _POST
	*/
	public function test__POST(){
		$this->initAffaire();
		$this->obj = ATF::affaire_etat();

		$res = ATF::affaire_etat()->_POST(array(),array("id_affaire"=>$this->id_affaire ,"etat"=>"reception_pj"));
		$this->assertEquals(true,$res, "POST incorrect");

		$res = ATF::affaire_etat()->_GET(array("id_affaire"=>$this->id_affaire));

		$this->assertEquals("reception_pj", $res[0]["etat"], "Retour GET incorrect 1");
		$this->assertEquals($this->id_affaire, $res[0]["id_affaire"], "Retour GET incorrect 3");


	}

};