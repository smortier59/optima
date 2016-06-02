<?
/**
* Classe de test sur le module questionnaire_bk
*/
class questionnaire_bk_test extends ATF_PHPUnit_Framework_TestCase {
	/** Méthode pré-test, exécutée avant chaque test unitaire
	* besoin d'un user pour les traduction
	*/
	public function setUp() {		
		ATF::db()->begin_transaction(true);

		$pack_produit = array("nom"=>"test_pack_produit",
					  		  "site_associe"=>"cleodis",
							  "loyer"=>200,
							  "frequence"=>"mois",
							  "duree"=>"10");
		$pack_produit_ligne["produits_non_visible"] = '[{"pack_produit_ligne__dot__produit":"SonicWALL Global VPN Client","pack_produit_ligne__dot__quantite":"2","pack_produit_ligne__dot__type":"sans_objet","pack_produit_ligne__dot__ref":"01-SSC-5316","pack_produit_ligne__dot__prix_achat":"0.00","pack_produit_ligne__dot__id_produit":"","pack_produit_ligne__dot__id_fournisseur":"ABSYSTECH","pack_produit_ligne__dot__visibilite_prix":"visible","pack_produit_ligne__dot__date_achat":"","pack_produit_ligne__dot__commentaire":"","pack_produit_ligne__dot__neuf":"oui","pack_produit_ligne__dot__id_produit_fk":"9546","pack_produit_ligne__dot__id_fournisseur_fk":"1606"}]'; 
		
		$this->pack_produit_id = ATF::pack_produit()->insert(array("pack_produit"=>$pack_produit, "values_pack_produit"=>$pack_produit_ligne));

	}
	
	/** Méthode post-test, exécutée après chaque test unitaire
	*/
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
		
	}

	/*--------------------------------------------------------------*/
	/*                   Tests unitaires                            */
	/*--------------------------------------------------------------*/
	
	/** Test du constructeur
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/	
	public function test__construct(){
		$c = new questionnaire_bk();	
		$this->assertTrue($c instanceOf questionnaire_bk, "L'objet questionnaire_bk n'est pas de bon type");
	}


	public function test_insert(){

		$questionnaire_bk = array("questionnaire_bk"=>array("question" => "test", "index_question" => 1,"type_reponse" => "oui_non"), 
    							  "values_questionnaire_bk" => array(
            							"pack_produits" => '[{"questionnaire_bk_ligne__dot__nom":"test_pack_produit","questionnaire_bk_ligne__dot__id_pack_produit":'.$this->pack_produit_id.',"questionnaire_bk_ligne__dot__id_pack_produit_fk":'.$this->pack_produit_id.'}]'
            						)
    							);

		$id = $this->obj->insert($questionnaire_bk);

		$this->assertNotNull($id , "Erreur insert");
	}


	public function test_update(){

		$questionnaire_bk = array("questionnaire_bk"=>array("question" => "test", "index_question" => 1,"type_reponse" => "oui_non"), 
    							  "values_questionnaire_bk" => array(
            							"pack_produits" => '[{"questionnaire_bk_ligne__dot__nom":"test_pack_produit","questionnaire_bk_ligne__dot__id_pack_produit":'.$this->pack_produit_id.',"questionnaire_bk_ligne__dot__id_pack_produit_fk":'.$this->pack_produit_id.'}]'
            						)
    							);

		$id = $this->obj->insert($questionnaire_bk);
		$this->assertNotNull($id , "Erreur insert");

		$questionnaire_bk["questionnaire_bk"]["id_questionnaire_bk"] = $id;
		$questionnaire_bk["questionnaire_bk"]["question"] = "Test update";
		$id = $this->obj->update($questionnaire_bk);
		$this->assertEquals("Test update", $this->obj->select($id, "question"), "Erreur update ?");


	}



}