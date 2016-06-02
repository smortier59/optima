<?
class commande_ligne_cleodis_test extends ATF_PHPUnit_Framework_TestCase {
	
	/** Méthode pré-test, exécute avant chaque test unitaire
	* besoin d'un user pour les traduction
	*/
	public function setUp() {
		$this->initUser();
	}
	
	/** Méthode post-test, exécute après chaque test unitaire*/
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
		ATF::$msg->getNotices();
	}

	public function testToFactureLigne(){
		$devisSerialize = 'a:9:{s:9:"extAction";s:5:"devis";s:9:"extMethod";s:6:"insert";s:7:"preview";s:4:"true";s:11:"label_devis";a:5:{s:10:"id_filiale";s:7:"CLEODIS";s:14:"id_opportunite";s:8:"Aucun(e)";s:10:"id_societe";s:7:"FINORPA";s:10:"id_contact";s:16:"M Philippe MOONS";s:10:"AR_societe";s:7:"FINORPA";}s:5:"devis";a:21:{s:10:"id_filiale";s:3:"246";s:5:"devis";s:2:"TU";s:3:"tva";s:5:"1.196";s:11:"date_accord";s:10:"08-02-2011";s:14:"id_opportunite";s:0:"";s:10:"id_societe";i:5391;s:12:"type_contrat";s:3:"lld";s:8:"validite";s:10:"23-02-2011";s:10:"id_contact";s:4:"5753";s:6:"loyers";s:4:"0.00";s:23:"frais_de_gestion_unique";s:4:"0.00";s:16:"assurance_unique";s:4:"0.00";s:10:"AR_societe";s:0:"";s:5:"marge";s:5:"99.96";s:13:"marge_absolue";s:8:"8 021.00";s:4:"prix";s:8:"8 024.00";s:10:"prix_achat";s:4:"3.00";s:5:"email";s:17:"pmoons@finorpa.fr";s:10:"emailTexte";s:4:"<br>";s:10:"emailCopie";s:24:"jerome.loison@cleodis.fr";s:13:"filestoattach";a:1:{s:13:"fichier_joint";s:0:"";}}s:7:"avenant";s:0:"";s:2:"AR";s:0:"";s:5:"loyer";a:1:{s:15:"frequence_loyer";s:1:"m";}s:12:"values_devis";a:2:{s:5:"loyer";s:185:"[{"loyer__dot__loyer":"233","loyer__dot__duree":"34","loyer__dot__assurance":"2","loyer__dot__frais_de_gestion":"1","loyer__dot__frequence_loyer":"mois","loyer__dot__loyer_total":8024}]";s:8:"produits";s:415:"[{"devis_ligne__dot__produit":"Zywall 5 - dispositif de sécurité","devis_ligne__dot__quantite":"3","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"ZYX-FW","devis_ligne__dot__prix_achat":"1","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"<span class=\"searchSelectionFound\">D</span>JP SERVICE","devis_ligne__dot__id_produit_fk":"1175","devis_ligne__dot__id_fournisseur_fk":"1583"}]";}}';
		$devis = unserialize($devisSerialize);
		$id_devis = classes::decryptId(ATF::devis()->insert($devis,$this->s));
		ATF::devis_ligne()->q->reset()->addCondition("id_devis",$id_devis);
		$devis_ligne=ATF::devis_ligne()->sa();
		$id_affaire = ATF::devis()->select($id_devis,'id_affaire');
	
		$commandeSerialize = 'a:5:{s:9:"extAction";s:8:"commande";s:9:"extMethod";s:6:"insert";s:7:"preview";s:4:"true";s:8:"commande";a:15:{s:8:"commande";s:2:"TU";s:4:"type";s:11:"prelevement";s:10:"id_societe";s:4:"5391";s:4:"date";s:0:"";s:10:"id_affaire";s:4:"6429";s:17:"clause_logicielle";s:3:"non";s:4:"prix";s:9:"39 780.00";s:10:"prix_achat";s:5:"53.00";s:5:"marge";s:5:"99.87";s:13:"marge_absolue";s:9:"39 727.00";s:5:"email";s:17:"pmoons@finorpa.fr";s:10:"emailTexte";s:0:"";s:10:"emailCopie";s:24:"jerome.loison@cleodis.fr";s:8:"id_devis";s:4:"6356";s:10:"__redirect";s:5:"devis";}s:15:"values_commande";a:4:{s:5:"loyer";s:198:"[{"loyer__dot__loyer":"1000.00","loyer__dot__duree":"39","loyer__dot__assurance":"10.00","loyer__dot__frais_de_gestion":"10.00","loyer__dot__frequence_loyer":"mois","loyer__dot__loyer_total":39780}]";s:15:"produits_repris";s:527:"[{"commande_ligne__dot__produit":"LATITUDE D520","commande_ligne__dot__quantite":"1","commande_ligne__dot__ref":"DELLLAT-1","commande_ligne__dot__id_fournisseur":"CBASE","commande_ligne__dot__id_fournisseur_fk":"faf6cbeded2dea2b761ebe7905d9a1ea","commande_ligne__dot__prix_achat":"50.00","commande_ligne__dot__id_produit":"LATITUDE D520","commande_ligne__dot__id_produit_fk":"bf606b897aa6a431fd1bdadda788129d","commande_ligne__dot__serial":"BBPZB2J","commande_ligne__dot__id_commande_ligne":"5f9fb4146f2a2f610b23e492f89aa47a"}]";s:8:"produits";s:533:"[{"commande_ligne__dot__produit":"Zywall 5 - dispositif de sécurité","commande_ligne__dot__quantite":"3","commande_ligne__dot__ref":"ZYX-FW","commande_ligne__dot__id_fournisseur":"DJP SERVICE","commande_ligne__dot__id_fournisseur_fk":"c3fa67e42bb24869e490543b88af9df2","commande_ligne__dot__prix_achat":"1.00","commande_ligne__dot__id_produit":"Zywall 5 - dispositif de sécurité","commande_ligne__dot__id_produit_fk":"8892f086954296d8ec3a1fd4810bc9d2","commande_ligne__dot__id_commande_ligne":"87316a8c4ab84c44e2ef2b3170902366"}]";s:20:"produits_non_visible";s:0:"";}}';
		$commande = unserialize($commandeSerialize);
		$commande["commande"]["id_devis"] = $id_devis;
		$commande["values_commande"]["produits"]='[{"commande_ligne__dot__produit":"Zywall 5 - dispositif de sécurité","commande_ligne__dot__quantite":"3","commande_ligne__dot__ref":"ZYX-FW","commande_ligne__dot__id_fournisseur":"DJP SERVICE","commande_ligne__dot__id_fournisseur_fk":"1583","commande_ligne__dot__prix_achat":"1.00","commande_ligne__dot__id_produit":"Zywall 5 - dispositif de sécurité","commande_ligne__dot__id_produit_fk":"1175","commande_ligne__dot__id_commande_ligne":"'.$$devis_ligne[0]["id_devis_ligne"].'"}]';
		unset($commande["values_commande"]["produits_repris"]);
		unset($commande["values_commande"]["produits_non_visible"]);
		$id_commande = classes::decryptId(ATF::commande()->insert($commande,$this->s));
		
		$this->obj->q->reset()->addCondition("id_commande",$id_commande)->setCount()->setView(array("order"=>array("commande_ligne.produit","commande_ligne.ref","commande_ligne.id_fournisseur","commande_ligne.prix_achat","commande_ligne.code","commande_ligne.id_produit","commande_ligne.serial")));
		$lignes=$this->obj->toFactureLigne($id_commande);
		$this->obj->q->reset()->addCondition("id_commande",$id_commande);
		$lignesq=$this->obj->sa();
		$this->assertEquals(array(
			"data"=>array(array("facture_ligne.id_produit"=>"Zywall 5 - dispositif de sécurité",
						  "facture_ligne.produit"=>"Zywall 5 - dispositif de sécurité",
						  "facture_ligne.quantite"=>3,
						  "facture_ligne.ref"=>"ZYX-FW",
						  "facture_ligne.id_fournisseur"=>"DJP SERVICE INFORMATIQUE",
						  "facture_ligne.prix_achat"=>"1.00",
						  "facture_ligne.serial"=>"",
						  "facture_ligne.id_produit_fk"=>1175,
						  "facture_ligne.id_fournisseur_fk"=>1583,
						  "facture_ligne.id_facture_ligne"=>$lignesq[0]["id_commande_ligne"],
						  'facture_ligne.neuf' => 'oui',
						  'facture_ligne.afficher' => 'oui')),
			"count"=>1
			),$lignes,'Update ne se fait pas bien en pre');
		
	}
	
	/* @author Nicolas BERTEMONT <nbertemont@absystech.fr> */
	public function test_commande_ligneMidas(){
		$cm=new commande_ligne_midas();
		$this->assertEquals('a:3:{s:22:"commande_ligne.produit";a:4:{s:4:"type";s:4:"text";s:5:"xtype";s:9:"textfield";s:9:"maxlength";s:3:"500";s:7:"default";N;}s:23:"commande_ligne.quantite";a:4:{s:4:"type";s:3:"int";s:5:"xtype";s:11:"numberfield";s:9:"maxlength";s:2:"10";s:7:"default";N;}s:18:"commande_ligne.ref";a:5:{s:4:"type";s:4:"text";s:5:"xtype";s:9:"textfield";s:9:"maxlength";s:2:"32";s:7:"default";N;s:4:"null";b:1;}}',serialize($cm->colonnes['fields_column']),"Le constructeur de la classe midas a changé");
	}
};
?>