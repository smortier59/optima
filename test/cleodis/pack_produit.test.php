<?
/*
* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
* @package Optima
* @subpackage Cléodis
* @date 21-01-11
*/
class pack_produit_test extends ATF_PHPUnit_Framework_TestCase {

	/** Méthode pré-test, exécute avant chaque test unitaire
	* besoin d'un user pour les traduction
	*/
	public function setUp() {
		$this->begin_transaction(true);
	}

	/** Méthode post-test, exécute après chaque test unitaire*/
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}

	/** @test Test du constructeur produit.  */
	public function test_constructeur(){
		$this->pack_produit = new pack_produit();
	}

	public function test_insert(){

		$pack_produit = array("nom"=>"test_pack_produit",
					  		  "site_associe"=>"cleodis",
							  "loyer"=>200,
							  "frequence"=>"mois",
							  "duree"=>"10");
		$pack_produit_ligne["produits"] = array();

		try{
			$this->obj->insert(array("pack_produit"=>$pack_produit, "values_pack_produit"=>$pack_produit_ligne));
		}catch(errorATF $e){
			$err = $e->getMessage();
		}
		$this->assertEquals("pack_produit sans produits",$err, "Erreur incorrecte");

		$pack_produit_ligne["produits"] = '[{"pack_produit_ligne__dot__produit":"SonicWALL Global VPN Client","pack_produit_ligne__dot__quantite":"2","pack_produit_ligne__dot__type":"sans_objet","pack_produit_ligne__dot__ref":"01-SSC-5316","pack_produit_ligne__dot__prix_achat":"0.00","pack_produit_ligne__dot__id_produit":"","pack_produit_ligne__dot__id_fournisseur":"ABSYSTECH","pack_produit_ligne__dot__visibilite_prix":"visible","pack_produit_ligne__dot__date_achat":"","pack_produit_ligne__dot__commentaire":"","pack_produit_ligne__dot__neuf":"oui","pack_produit_ligne__dot__id_produit_fk":"9546","pack_produit_ligne__dot__id_fournisseur_fk":""}]';

		try{
			$this->obj->insert(array("pack_produit"=>$pack_produit, "values_pack_produit"=>$pack_produit_ligne));
		}catch(errorATF $e){
			$err = $e->getMessage();
		}
		$this->assertEquals("Ligne de pack_produit sans fournisseur",$err, "Erreur incorrecte");




		$pack_produit_ligne["produits"] = '[{"pack_produit_ligne__dot__produit":"SonicWALL Global VPN Client","pack_produit_ligne__dot__quantite":"2","pack_produit_ligne__dot__type":"sans_objet","pack_produit_ligne__dot__ref":"01-SSC-5316","pack_produit_ligne__dot__prix_achat":"0.00","pack_produit_ligne__dot__id_produit":"","pack_produit_ligne__dot__id_fournisseur":"ABSYSTECH","pack_produit_ligne__dot__visibilite_prix":"visible","pack_produit_ligne__dot__date_achat":"","pack_produit_ligne__dot__commentaire":"","pack_produit_ligne__dot__neuf":"oui","pack_produit_ligne__dot__id_produit_fk":"9546","pack_produit_ligne__dot__id_fournisseur_fk":"1606"}]';
		$pack_produit_ligne["produits_non_visible"] = '[{"pack_produit_ligne__dot__produit":"SonicWALL Global VPN Client","pack_produit_ligne__dot__quantite":"2","pack_produit_ligne__dot__type":"sans_objet","pack_produit_ligne__dot__ref":"01-SSC-5316","pack_produit_ligne__dot__prix_achat":"0.00","pack_produit_ligne__dot__id_produit":"","pack_produit_ligne__dot__id_fournisseur":"ABSYSTECH","pack_produit_ligne__dot__visibilite_prix":"visible","pack_produit_ligne__dot__date_achat":"","pack_produit_ligne__dot__commentaire":"","pack_produit_ligne__dot__neuf":"oui","pack_produit_ligne__dot__id_produit_fk":"9546","pack_produit_ligne__dot__id_fournisseur_fk":"1606"}]';
		$pack_produit_ligne["produits_option_partenaire"] = '[{"pack_produit_ligne__dot__produit":"SonicWALL Global VPN Client","pack_produit_ligne__dot__quantite":"2","pack_produit_ligne__dot__type":"sans_objet","pack_produit_ligne__dot__ref":"01-SSC-5316","pack_produit_ligne__dot__prix_achat":"0.00","pack_produit_ligne__dot__id_produit":"","pack_produit_ligne__dot__id_fournisseur":"ABSYSTECH","pack_produit_ligne__dot__visibilite_prix":"visible","pack_produit_ligne__dot__date_achat":"","pack_produit_ligne__dot__commentaire":"","pack_produit_ligne__dot__neuf":"oui","pack_produit_ligne__dot__id_produit_fk":"9546","pack_produit_ligne__dot__id_fournisseur_fk":"1606"}]';
		try{
			$this->obj->insert(array("pack_produit"=>$pack_produit, "values_pack_produit"=>$pack_produit_ligne));
		}catch(errorATF $e){
			$err = $e->getMessage();
		}
		$this->assertEquals("Ligne d'option partenaire sans partenaire",$err, "Erreur incorrecte");


		$pack_produit_ligne["produits_option_partenaire"] = '[{"pack_produit_ligne__dot__produit":"SonicWALL Global VPN Client","pack_produit_ligne__dot__quantite":"2","pack_produit_ligne__dot__type":"sans_objet","pack_produit_ligne__dot__ref":"01-SSC-5316","pack_produit_ligne__dot__prix_achat":"0.00","pack_produit_ligne__dot__id_fournisseur":"ABSYSTECH","pack_produit_ligne__dot__visibilite_prix":"visible","pack_produit_ligne__dot__date_achat":"","pack_produit_ligne__dot__commentaire":"","pack_produit_ligne__dot__neuf":"oui","pack_produit_ligne__dot__id_fournisseur_fk":"1606","pack_produit_ligne__dot__id_partenaire_fk":"1606"}]';
		try{
			$this->obj->insert(array("pack_produit"=>$pack_produit, "values_pack_produit"=>$pack_produit_ligne));
		}catch(errorATF $e){
			$err = $e->getMessage();
		}
		$this->assertEquals("SonicWALL Global VPN Client ne fait réference à aucun produit (Manque Id produit)",$err, "Erreur incorrecte");


		$pack_produit_ligne["produits_option_partenaire"] = '[{"pack_produit_ligne__dot__produit":"SonicWALL Global VPN Client","pack_produit_ligne__dot__quantite":"2","pack_produit_ligne__dot__type":"sans_objet","pack_produit_ligne__dot__ref":"01-SSC-5316","pack_produit_ligne__dot__prix_achat":"0.00","pack_produit_ligne__dot__id_produit":"","pack_produit_ligne__dot__id_fournisseur":"ABSYSTECH","pack_produit_ligne__dot__visibilite_prix":"visible","pack_produit_ligne__dot__date_achat":"","pack_produit_ligne__dot__commentaire":"","pack_produit_ligne__dot__neuf":"oui","pack_produit_ligne__dot__id_produit_fk":"9546","pack_produit_ligne__dot__id_fournisseur_fk":"1606","pack_produit_ligne__dot__id_partenaire_fk":"1606"}]';

		$id = $this->obj->insert(array("pack_produit"=>$pack_produit, "values_pack_produit"=>$pack_produit_ligne));
	}


	public function test_update(){
		$pack_produit = array("nom"=>"test_pack_produit",
					  		  "site_associe"=>"cleodis",
							  "loyer"=>200,
							  "frequence"=>"mois",
							  "duree"=>"10");
		$pack_produit_ligne["produits"] = '[{"pack_produit_ligne__dot__produit":"SonicWALL Global VPN Client","pack_produit_ligne__dot__quantite":"2","pack_produit_ligne__dot__type":"sans_objet","pack_produit_ligne__dot__ref":"01-SSC-5316","pack_produit_ligne__dot__prix_achat":"0.00","pack_produit_ligne__dot__id_produit":"","pack_produit_ligne__dot__id_fournisseur":"ABSYSTECH","pack_produit_ligne__dot__visibilite_prix":"visible","pack_produit_ligne__dot__date_achat":"","pack_produit_ligne__dot__commentaire":"","pack_produit_ligne__dot__neuf":"oui","pack_produit_ligne__dot__id_produit_fk":"9546","pack_produit_ligne__dot__id_fournisseur_fk":"1606"}]';
		$pack_produit_ligne["produits_non_visible"] = '[{"pack_produit_ligne__dot__produit":"SonicWALL Global VPN Client","pack_produit_ligne__dot__quantite":"2","pack_produit_ligne__dot__type":"sans_objet","pack_produit_ligne__dot__ref":"01-SSC-5316","pack_produit_ligne__dot__prix_achat":"0.00","pack_produit_ligne__dot__id_produit":"","pack_produit_ligne__dot__id_fournisseur":"ABSYSTECH","pack_produit_ligne__dot__visibilite_prix":"visible","pack_produit_ligne__dot__date_achat":"","pack_produit_ligne__dot__commentaire":"","pack_produit_ligne__dot__neuf":"oui","pack_produit_ligne__dot__id_produit_fk":"9546","pack_produit_ligne__dot__id_fournisseur_fk":"1606"}]';

		$id = $this->obj->insert(array("pack_produit"=>$pack_produit, "values_pack_produit"=>$pack_produit_ligne));

		$pack_produit["id_pack_produit"] = $id;
		$pack_produit["loyer"] = 300;

		$id_update = $this->obj->update(array("pack_produit"=>$pack_produit, "values_pack_produit"=>$pack_produit_ligne));
		$this->assertEquals(300,$this->obj->select($id_update, "loyer"), "Erreur update");



		$pack_produit_ligne["produits_non_visible"] = '[{"pack_produit_ligne__dot__produit":"SonicWALL Global VPN Client","pack_produit_ligne__dot__quantite":"2","pack_produit_ligne__dot__type":"sans_objet","pack_produit_ligne__dot__ref":"01-SSC-5316","pack_produit_ligne__dot__prix_achat":"0.00","pack_produit_ligne__dot__id_produit":"","pack_produit_ligne__dot__id_fournisseur":"ABSYSTECH","pack_produit_ligne__dot__visibilite_prix":"visible","pack_produit_ligne__dot__date_achat":"","pack_produit_ligne__dot__commentaire":"","pack_produit_ligne__dot__neuf":"oui","pack_produit_ligne__dot__id_produit_fk":"9546"}]';
		try{
			$this->obj->update(array("pack_produit"=>$pack_produit, "values_pack_produit"=>$pack_produit_ligne));
		}catch(errorATF $e){
			$err = $e->getMessage();
		}
		$this->assertEquals("Ligne de pack_produit sans fournisseur",$err, "Erreur incorrecte");


		try{
			$this->obj->update(array("pack_produit"=>$pack_produit, "values_pack_produit"=>array()));
		}catch(errorATF $e){
			$err = $e->getMessage();
		}
		$this->assertEquals("pack_produit sans produits",$err, "Erreur incorrecte 2");


		$pack_produit_ligne["produits"] = '[{"pack_produit_ligne__dot__produit":"SonicWALL Global VPN Client","pack_produit_ligne__dot__quantite":"2","pack_produit_ligne__dot__type":"sans_objet","pack_produit_ligne__dot__ref":"01-SSC-5316","pack_produit_ligne__dot__prix_achat":"0.00","pack_produit_ligne__dot__id_produit":"","pack_produit_ligne__dot__id_fournisseur":"ABSYSTECH","pack_produit_ligne__dot__visibilite_prix":"visible","pack_produit_ligne__dot__date_achat":"","pack_produit_ligne__dot__commentaire":"","pack_produit_ligne__dot__neuf":"oui","pack_produit_ligne__dot__id_produit_fk":"9546","pack_produit_ligne__dot__id_fournisseur_fk":"1606"}]';
		$pack_produit_ligne["produits_non_visible"] = '[{"pack_produit_ligne__dot__produit":"SonicWALL Global VPN Client","pack_produit_ligne__dot__quantite":"2","pack_produit_ligne__dot__type":"sans_objet","pack_produit_ligne__dot__ref":"01-SSC-5316","pack_produit_ligne__dot__prix_achat":"0.00","pack_produit_ligne__dot__id_produit":"","pack_produit_ligne__dot__id_fournisseur":"ABSYSTECH","pack_produit_ligne__dot__visibilite_prix":"visible","pack_produit_ligne__dot__date_achat":"","pack_produit_ligne__dot__commentaire":"","pack_produit_ligne__dot__neuf":"oui","pack_produit_ligne__dot__id_produit_fk":"9546","pack_produit_ligne__dot__id_fournisseur_fk":"1606"}]';
		$pack_produit_ligne["produits_option_partenaire"] = '[{"pack_produit_ligne__dot__produit":"SonicWALL Global VPN Client","pack_produit_ligne__dot__quantite":"2","pack_produit_ligne__dot__type":"sans_objet","pack_produit_ligne__dot__ref":"01-SSC-5316","pack_produit_ligne__dot__prix_achat":"0.00","pack_produit_ligne__dot__id_produit":"","pack_produit_ligne__dot__id_fournisseur":"ABSYSTECH","pack_produit_ligne__dot__visibilite_prix":"visible","pack_produit_ligne__dot__date_achat":"","pack_produit_ligne__dot__commentaire":"","pack_produit_ligne__dot__neuf":"oui","pack_produit_ligne__dot__id_produit_fk":"9546","pack_produit_ligne__dot__id_fournisseur_fk":"1606"}]';
		try{
			$this->obj->update(array("pack_produit"=>$pack_produit, "values_pack_produit"=>$pack_produit_ligne));
		}catch(errorATF $e){
			$err = $e->getMessage();
		}
		$this->assertEquals("Ligne d'option partenaire sans partenaire",$err, "Erreur incorrecte");


		$pack_produit_ligne["produits_option_partenaire"] = '[{"pack_produit_ligne__dot__produit":"SonicWALL Global VPN Client","pack_produit_ligne__dot__quantite":"2","pack_produit_ligne__dot__type":"sans_objet","pack_produit_ligne__dot__ref":"01-SSC-5316","pack_produit_ligne__dot__prix_achat":"0.00","pack_produit_ligne__dot__id_fournisseur":"ABSYSTECH","pack_produit_ligne__dot__visibilite_prix":"visible","pack_produit_ligne__dot__date_achat":"","pack_produit_ligne__dot__commentaire":"","pack_produit_ligne__dot__neuf":"oui","pack_produit_ligne__dot__id_fournisseur_fk":"1606","pack_produit_ligne__dot__id_partenaire_fk":"1606"}]';
		try{
			$this->obj->update(array("pack_produit"=>$pack_produit, "values_pack_produit"=>$pack_produit_ligne));
		}catch(errorATF $e){
			$err = $e->getMessage();
		}
		$this->assertEquals("SonicWALL Global VPN Client ne fait réference à aucun produit (Manque Id produit)",$err, "Erreur incorrecte");


		$pack_produit_ligne["produits_option_partenaire"] = '[{"pack_produit_ligne__dot__produit":"SonicWALL Global VPN Client","pack_produit_ligne__dot__quantite":"2","pack_produit_ligne__dot__type":"sans_objet","pack_produit_ligne__dot__ref":"01-SSC-5316","pack_produit_ligne__dot__prix_achat":"0.00","pack_produit_ligne__dot__id_produit":"","pack_produit_ligne__dot__id_fournisseur":"ABSYSTECH","pack_produit_ligne__dot__visibilite_prix":"visible","pack_produit_ligne__dot__date_achat":"","pack_produit_ligne__dot__commentaire":"","pack_produit_ligne__dot__neuf":"oui","pack_produit_ligne__dot__id_produit_fk":"9546","pack_produit_ligne__dot__id_fournisseur_fk":"1606","pack_produit_ligne__dot__id_partenaire_fk":"1606"}]';

		$id = $this->obj->update(array("pack_produit"=>$pack_produit, "values_pack_produit"=>$pack_produit_ligne));


	}


	public function test_setInfos(){
		$pack_produit = array("nom"=>"test_pack_produit",
					  		  "site_associe"=>"cleodis",
							  "loyer"=>200,
							  "frequence"=>"mois",
							  "duree"=>"10");
		$pack_produit_ligne["produits"] = '[{"pack_produit_ligne__dot__produit":"SonicWALL Global VPN Client","pack_produit_ligne__dot__quantite":"2","pack_produit_ligne__dot__type":"sans_objet","pack_produit_ligne__dot__ref":"01-SSC-5316","pack_produit_ligne__dot__prix_achat":"0.00","pack_produit_ligne__dot__id_produit":"","pack_produit_ligne__dot__id_fournisseur":"ABSYSTECH","pack_produit_ligne__dot__visibilite_prix":"visible","pack_produit_ligne__dot__date_achat":"","pack_produit_ligne__dot__commentaire":"","pack_produit_ligne__dot__neuf":"oui","pack_produit_ligne__dot__id_produit_fk":"9546","pack_produit_ligne__dot__id_fournisseur_fk":"1606"}]';

		$id = $this->obj->insert(array("pack_produit"=>$pack_produit, "values_pack_produit"=>$pack_produit_ligne));

		$data = array("id_pack_produit"=>$id,
					  "field"=>"loyer",
					  "loyer"=>"100"
					 );
		$this->obj->setInfos($data);
		$this->assertEquals(100,$this->obj->select($id, "loyer"), "SetInfos n'a pas fonctionné !");

		$notice = ATF::$msg->getNotices();

		$this->assertEquals(1,count($notice), "Nbre de notice incorrect");
	}

	public function test_EtatUpdate(){
		$pack_produit = array("nom"=>"test_pack_produit",
					  		  "site_associe"=>"cleodis",
							  "loyer"=>200,
							  "frequence"=>"mois",
							  "duree"=>"10");
		$pack_produit_ligne["produits"] = '[{"pack_produit_ligne__dot__produit":"SonicWALL Global VPN Client","pack_produit_ligne__dot__quantite":"2","pack_produit_ligne__dot__type":"sans_objet","pack_produit_ligne__dot__ref":"01-SSC-5316","pack_produit_ligne__dot__prix_achat":"0.00","pack_produit_ligne__dot__id_produit":"","pack_produit_ligne__dot__id_fournisseur":"ABSYSTECH","pack_produit_ligne__dot__visibilite_prix":"visible","pack_produit_ligne__dot__date_achat":"","pack_produit_ligne__dot__commentaire":"","pack_produit_ligne__dot__neuf":"oui","pack_produit_ligne__dot__id_produit_fk":"9546","pack_produit_ligne__dot__id_fournisseur_fk":"1606"}]';

		$id = $this->obj->insert(array("pack_produit"=>$pack_produit, "values_pack_produit"=>$pack_produit_ligne));

		$data = array("id_pack_produit"=>$id,
					  "field"=>"visible_sur_site",
					  "visible_sur_site"=>"oui"
					 );
		$this->obj->EtatUpdate($data);
		$this->assertEquals("oui",$this->obj->select($id, "visible_sur_site"), "EtatUpdate n'a pas fonctionné !");

		$notice = ATF::$msg->getNotices();

		$this->assertEquals(1,count($notice), "Nbre de notice incorrect");
	}

	public function test_autocomplete(){
		$pack=array(
						"start"=>0,
					   "limit"=>0,
					   "query"=>"PACK ATOL PRO"
						);

		$autocomplete=$this->obj->autocomplete($pack);
		$this->assertEquals(52,$autocomplete[0]["raw_0"],"autocomplete ne renvoi pas le bon pack produit");
	}


	public function test_getDureePack(){
		$pack_produit = array("nom"=>"test_pack_produit",
					  		  "site_associe"=>"cleodis",
							  "loyer"=>200,
							  "frequence"=>"mois",
							  "duree"=>"10");
		$pack_produit_ligne["produits"] = '[{"pack_produit_ligne__dot__produit":"SonicWALL Global VPN Client","pack_produit_ligne__dot__quantite":"2","pack_produit_ligne__dot__type":"sans_objet","pack_produit_ligne__dot__ref":"01-SSC-5316","pack_produit_ligne__dot__prix_achat":"0.00","pack_produit_ligne__dot__id_produit":"","pack_produit_ligne__dot__id_fournisseur":"ABSYSTECH","pack_produit_ligne__dot__visibilite_prix":"visible","pack_produit_ligne__dot__date_achat":"","pack_produit_ligne__dot__commentaire":"","pack_produit_ligne__dot__neuf":"oui","pack_produit_ligne__dot__id_produit_fk":"9546","pack_produit_ligne__dot__id_fournisseur_fk":"1606"}]';

		$id = $this->obj->insert(array("pack_produit"=>$pack_produit, "values_pack_produit"=>$pack_produit_ligne));

		ATF::produit()->u(array("id_produit"=> 9546, "duree"=> 25));

		$duree = $this->obj->getDureePack($id);
		$this->assertEquals($duree,25,"Get durée incorrect");

	}


	public function test_getProduitPrincipal(){
		$pack_produit = array("nom"=>"test_pack_produit",
					  		  "site_associe"=>"cleodis",
							  "loyer"=>200,
							  "frequence"=>"mois",
							  "duree"=>"10");
		$pack_produit_ligne["produits"] = '[{"pack_produit_ligne__dot__produit":"SonicWALL Global VPN Client","pack_produit_ligne__dot__quantite":"2","pack_produit_ligne__dot__type":"sans_objet","pack_produit_ligne__dot__ref":"01-SSC-5316","pack_produit_ligne__dot__prix_achat":"0.00","pack_produit_ligne__dot__id_produit":"","pack_produit_ligne__dot__id_fournisseur":"ABSYSTECH","pack_produit_ligne__dot__visibilite_prix":"visible","pack_produit_ligne__dot__date_achat":"","pack_produit_ligne__dot__commentaire":"","pack_produit_ligne__dot__neuf":"oui","pack_produit_ligne__dot__id_produit_fk":"9546","pack_produit_ligne__dot__id_fournisseur_fk":"1606"}]';

		$id = $this->obj->insert(array("pack_produit"=>$pack_produit, "values_pack_produit"=>$pack_produit_ligne));

		$produit_principal = $this->obj->getProduitPrincipal($id);
		$this->assertEquals($produit_principal,9546,"getProduitPrincipal incorrect");

	}
}
?>