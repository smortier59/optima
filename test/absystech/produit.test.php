<?
/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
class produit_test extends ATF_PHPUnit_Framework_TestCase {
	public function setUp() {
		$this->initUser();
	}
	
	public function tearDown() {
		ATF::db()->rollback_transaction(true);
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	public function test_select(){
		$contact["nom"]="Tu_devis";
		$this->id_contact=ATF::contact()->insert($contact);
		$this->devis["devis"]["id_contact"]=$this->id_contact;
		$this->devis["devis"]['resume']='Tu_devis';
		$this->devis["devis"]['id_societe']=1;
		$this->devis["devis"]['validite']=date('Y-m-d');
		$this->devis["devis"]['prix']="200";
		$this->devis["devis"]['frais_de_port']="50";
		$this->devis["devis"]['prix_achat']="50";
		$this->devis["devis"]["date"]=date('Y-m-d');
		$this->devis["values_devis"]=array("produits"=>'[{"devis_ligne__dot__ref":"TU","devis_ligne__dot__produit":"Tu_devis","devis_ligne__dot__quantite":"15","devis_ligne__dot__poids":"10","devis_ligne__dot__prix":"10","devis_ligne__dot__prix_achat":"10","devis_ligne__dot__id_fournisseur":"1","devis_ligne__dot__id_compte_absystech":"1","devis_ligne__dot__marge":97.14,"devis_ligne__dot__id_fournisseur_fk":"1"}]');
		$id_devis=ATF::devis()->insert($this->devis,$this->s);
		ATF::devis_ligne()->q->reset()->addCondition("id_devis",ATF::devis()->decryptId($id_devis));

		$devis_ligne=ATF::devis_ligne()->sa();
		$produit=$this->obj->select(array("lang"=>$devis_ligne[0]["id_devis_ligne"]));
		$this->assertEquals($devis_ligne[0]["id_devis_ligne"],$produit["id_devis_ligne"],"select ne renvoie pas la bonne ligne");
		$this->assertNull($produit["quantite"],"select ne retire pas quantite");

		$id_produit=ATF::produit()->insert(array("ref"=>"TU","produit"=>"produit_tu","prix"=>100,"id_fabriquant"=>20,"id_sous_categorie"=>5));
		$produit=$this->obj->select($id_produit);
		$this->assertEquals($id_produit,$produit["id_produit"],"select ne renvoie pas la bon produit");
	}
};
?>