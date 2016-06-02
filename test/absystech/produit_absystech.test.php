<?
class produit_absystech_test extends ATF_PHPUnit_Framework_TestCase {
	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function setUp() {
		$this->initUser();
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function tearDown(){
		$this->rollback_transaction();
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	public function test_autocomplete(){
		ATF::db()->query("UPDATE devis SET id_remplacant=null");
		ATF::db()->truncate("devis");
		$contact["nom"]="Tu_devis";
		$this->id_contact=ATF::contact()->insert($contact);
		$this->devis["devis"]["id_contact"]=$this->id_contact;
		$this->devis["devis"]['resume']='Tu_devis';
		$this->devis["devis"]['id_societe']=1;
		$this->devis["devis"]['validite']=date('Y-m-d');
		$this->devis["devis"]['prix']="200";
		$this->devis["devis"]['frais_de_port']="50";
		$this->devis["devis"]['prix_achat']="50";
		$this->devis["values_devis"]=array("produits"=>'[{"devis_ligne__dot__ref":"TU","devis_ligne__dot__produit":"Tu_devis","devis_ligne__dot__quantite":"15","devis_ligne__dot__poids":"10","devis_ligne__dot__prix":"10","devis_ligne__dot__prix_achat":"10","devis_ligne__dot__id_fournisseur":"1","devis_ligne__dot__id_compte_absystech":"1","devis_ligne__dot__marge":97.14,"devis_ligne__dot__id_fournisseur_fk":"1"}]');
		$this->devis["devis"]["date"]=date('Y-m-d');
		ATF::devis()->insert($this->devis,$this->s);
		
		$produit=$this->obj->autocomplete(array("query"=>"Tu","limit"=>10,"start"=>0));
		$this->assertEquals("10.00",$produit[0][2],"autocomplete ne renvoie pas le bon prix avec query");
		$this->assertEquals("TU",$produit[0][5],"autocomplete ne renvoie pas le bon produit avec query");

		$produit=$this->obj->autocomplete(array("query"=>"SUPPORT10","limit"=>10,"start"=>0));
		$this->assertEquals("680.00",$produit[0][2],"autocomplete ne renvoie pas le bon prix sans query");
		$this->assertEquals("SUPPORT10",$produit[0][5],"autocomplete ne renvoie pas le bon produit sans query");
	}

};
?>