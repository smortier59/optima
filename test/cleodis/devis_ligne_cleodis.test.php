<?
class devis_ligne_cleodis_test extends ATF_PHPUnit_Framework_TestCase {
	protected static $devis = 'a:9:{s:9:"extAction";s:5:"devis";s:9:"extMethod";s:6:"insert";s:7:"preview";s:4:"true";s:11:"label_devis";a:5:{s:10:"id_filiale";s:7:"CLEODIS";s:14:"id_opportunite";s:8:"Aucun(e)";s:10:"id_societe";s:7:"FINORPA";s:10:"id_contact";s:16:"M Philippe MOONS";s:10:"AR_societe";s:7:"FINORPA";}s:5:"devis";a:21:{s:10:"id_filiale";s:3:"246";s:5:"devis";s:2:"TU";s:3:"tva";s:5:"1.196";s:11:"date_accord";s:10:"08-02-2011";s:14:"id_opportunite";s:0:"";s:10:"id_societe";i:5391;s:12:"type_contrat";s:3:"lld";s:8:"validite";s:10:"23-02-2011";s:10:"id_contact";s:4:"5753";s:6:"loyers";s:4:"0.00";s:23:"frais_de_gestion_unique";s:4:"0.00";s:16:"assurance_unique";s:4:"0.00";s:10:"AR_societe";s:0:"";s:5:"marge";s:5:"99.96";s:13:"marge_absolue";s:8:"8 021.00";s:4:"prix";s:8:"8 024.00";s:10:"prix_achat";s:4:"3.00";s:5:"email";s:17:"pmoons@finorpa.fr";s:10:"emailTexte";s:4:"<br>";s:10:"emailCopie";s:24:"jerome.loison@cleodis.fr";s:13:"filestoattach";a:1:{s:13:"fichier_joint";s:0:"";}}s:7:"avenant";s:0:"";s:2:"AR";s:0:"";s:5:"loyer";a:1:{s:15:"frequence_loyer";s:1:"m";}s:12:"values_devis";a:2:{s:5:"loyer";s:185:"[{"loyer__dot__loyer":"233","loyer__dot__duree":"34","loyer__dot__assurance":"2","loyer__dot__frais_de_gestion":"1","loyer__dot__frequence_loyer":"mois","loyer__dot__loyer_total":8024}]";s:8:"produits";s:415:"[{"devis_ligne__dot__produit":"Zywall 5 - dispositif de sécurité","devis_ligne__dot__quantite":"3","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"ZYX-FW","devis_ligne__dot__prix_achat":"1","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"<span class=\"searchSelectionFound\">D</span>JP SERVICE","devis_ligne__dot__id_produit_fk":"1175","devis_ligne__dot__id_fournisseur_fk":"1583"}]";}}';
	
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

	public function testGetFournisseurs(){
		$devis = unserialize(self::$devis);
		$id_devis = classes::decryptId(ATF::devis()->insert($devis,$this->s));
		$lignes=$this->obj->getFournisseurs($id_devis);
		$this->assertEquals(array(array("id_fournisseur"=>1583)),$lignes,'getFournisseurs ne renvoi pas les bonnes lignes');
	}

	public function testToCommandeLigne(){
		$devis = unserialize(self::$devis);
		$id_devis = classes::decryptId(ATF::devis()->insert($devis,$this->s));
		
		$this->obj->q->reset()->addCondition("id_devis",$id_devis)->setCount()->setView(array("order"=>array("devis_ligne.produit","devis_ligne.ref","devis_ligne.id_fournisseur","devis_ligne.prix_achat","devis_ligne.code","devis_ligne.id_produit","devis_ligne.serial")));
		$lignes=$this->obj->toCommandeLigne($id_devis);
		$this->obj->q->reset()->addCondition("id_devis",$id_devis);
		$lignesq=$this->obj->sa();
		$this->assertEquals(array(
			"data"=>array(array("commande_ligne.id_produit"=>"Zywall 5 - dispositif de sécurité",
						  "commande_ligne.produit"=>"Zywall 5 - dispositif de sécurité",
						  "commande_ligne.quantite"=>3,
						  "commande_ligne.type"=>"fixe",
						  "commande_ligne.ref"=>"ZYX-FW",
						  "commande_ligne.id_fournisseur"=>"DJP SERVICE INFORMATIQUE",
						  "commande_ligne.prix_achat"=>"1.00",
						  "commande_ligne.id_produit_fk"=>1175,
						  "commande_ligne.id_fournisseur_fk"=>1583,
						  "commande_ligne.id_commande_ligne"=>$lignesq[0]["id_devis_ligne"],
						  "commande_ligne.neuf" => 'oui',
						  "commande_ligne.commentaire" => null,
						  )
					  ),
			"count"=>1
			),$lignes,'Update ne se fait pas bien en pre');
	}
	
	/* @author Nicolas BERTEMONT <nbertemont@absystech.fr> */
	public function test_devis_ligneMidas(){
		$cm=new devis_ligne_midas();
		$this->assertEquals('a:5:{s:19:"devis_ligne.produit";a:4:{s:4:"type";s:4:"text";s:5:"xtype";s:9:"textfield";s:9:"maxlength";s:3:"512";s:7:"default";N;}s:20:"devis_ligne.quantite";a:4:{s:4:"type";s:3:"int";s:5:"xtype";s:11:"numberfield";s:9:"maxlength";s:2:"10";s:7:"default";N;}s:16:"devis_ligne.type";a:4:{s:4:"type";s:4:"enum";s:5:"xtype";s:5:"combo";s:4:"data";a:4:{i:0;s:4:"fixe";i:1;s:8:"portable";i:2;s:10:"sans_objet";i:3;s:10:"immateriel";}s:7:"default";s:10:"sans_objet";}s:15:"devis_ligne.ref";a:5:{s:4:"type";s:4:"text";s:5:"xtype";s:9:"textfield";s:9:"maxlength";s:2:"32";s:7:"default";N;s:4:"null";b:1;}s:26:"devis_ligne.id_fournisseur";a:5:{s:4:"type";s:3:"int";s:5:"xtype";s:11:"numberfield";s:9:"maxlength";s:1:"8";s:7:"default";N;s:4:"null";b:1;}}',serialize($cm->colonnes['fields_column']),"Le constructeur de la classe midas a changé");
	}


	public function testextJSgsa(){
        $div='TU_extGSA';
        ATF::_p('query','unit');
        ATF::_p('pager',$div); 
        ATF::_p('parent_class','devis');
        $q = ATF::_s("pager")->create($div);
        $q->where("devis_ligne.id_devis",32);
        $r = ATF::devis_ligne()->extJSgsa(ATF::_p(),ATF::_s());
        $this->assertTrue(is_array($r),"DEVIS LIGNE NON TROUVE");
    }
};
?>