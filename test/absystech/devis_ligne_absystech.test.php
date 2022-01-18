<?
class devis_ligne_absystech_test extends ATF_PHPUnit_Framework_TestCase {
	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	/*besoin d'un user pour les traduction*/
	function setUp() {
		$this->initUser();

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
		$this->devis["values_devis"]=array("produits"=>'[{"devis_ligne__dot__ref":"TU","devis_ligne__dot__produit":"Tu_devis","devis_ligne__dot__quantite":"15","devis_ligne__dot__poids":"10","devis_ligne__dot__prix":"10","devis_ligne__dot__prix_achat":"1","devis_ligne__dot__id_fournisseur":"1","devis_ligne__dot__id_compte_absystech":"1","devis_ligne__dot__marge":97.14,"devis_ligne__dot__id_fournisseur_fk":"1"}]');

		$this->id_devis = ATF::devis()->insert($this->devis,$this->s);
		$this->id_affaire = ATF::devis()->select($this->id_devis,"id_affaire");
		$this->devis_ligne = $this->obj->select_special("id_devis",classes::decryptId($this->id_devis));
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function tearDown(){
		ATF::db()->rollback_transaction(true);
	}
	
	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function testToCommandeLigne(){
		$fields=array(
			"commande_ligne.ref"
			, "commande_ligne.produit"
			, "commande_ligne.quantite"
			, "commande_ligne.prix"
			, "commande_ligne.prix_achat"
			, "commande_ligne.id_fournisseur"
			, "commande_ligne.id_compte_absystech"
			, "commande_ligne.serial"
		);
		$q=ATF::_s("pager")->getAndPrepare("commandeProduitsUpdate");
		$q->reset()->where("id_devis",classes::decryptId($this->id_devis))->setView(array("order"=>$fields))->setCount();
		$this->obj->setQuerier($q);
		$toCommandeLigne=$this->obj->toCommandeLigne();
		$toCommandeLigne_attendu=array(
										"data"=>array(
														"0"=>array(
																	"commande_ligne.id_produit"=>"",
																	"commande_ligne.produit"=>$this->devis_ligne[0]["produit"],
																	"commande_ligne.quantite"=>$this->devis_ligne[0]["quantite"],
																	"commande_ligne.ref"=>$this->devis_ligne[0]["ref"],
																	"commande_ligne.poids"=>$this->devis_ligne[0]["poids"],
																	"commande_ligne.prix"=>$this->devis_ligne[0]["prix"],
																	"commande_ligne.id_fournisseur"=>"AbsysTech",
																	"commande_ligne.prix_achat"=>$this->devis_ligne[0]["prix_achat"],
																	"commande_ligne.id_compte_absystech"=>"",
																	"commande_ligne.id_produit_fk"=>"",
																	"commande_ligne.id_fournisseur_fk"=>"1",
																	"commande_ligne.id_compte_absystech_fk"=>"",
																	"commande_ligne.id_commande_ligne"=>$this->devis_ligne[0]["id_devis_ligne"],
																	"commande_ligne.marge"=>"90",
																	"commande_ligne.marge_absolue"=>"135",
																	"commande_ligne.periode"=> NULL,
																	'commande_ligne.prix_nb' => null,
																	'commande_ligne.prix_couleur' => null,
																	'commande_ligne.prix_achat_nb' => null,
																	'commande_ligne.prix_achat_couleur' => null,
																	'commande_ligne.index_nb' => null,
																	'commande_ligne.index_couleur' => null)
														),
										"count"=>"1"
										);
		
		$this->assertEquals($toCommandeLigne_attendu["data"][0]["commande_ligne.produit"],$toCommandeLigne["data"][0]["commande_ligne.produit"],"toCommandeLigne ne renvoi pas le bon produit");
		$this->assertEquals($toCommandeLigne_attendu["data"][0]["commande_ligne.quantite"],$toCommandeLigne["data"][0]["commande_ligne.quantite"],"toCommandeLigne ne renvoi pas le bon quantite");
		$this->assertEquals($toCommandeLigne_attendu["data"][0]["commande_ligne.ref"],$toCommandeLigne["data"][0]["commande_ligne.ref"],"toCommandeLigne ne renvoi pas le bon ref");
		$this->assertEquals($toCommandeLigne_attendu["data"][0]["commande_ligne.poids"],$toCommandeLigne["data"][0]["commande_ligne.poids"],"toCommandeLigne ne renvoi pas le bon poids");
		$this->assertEquals($toCommandeLigne_attendu["data"][0]["commande_ligne.prix"],$toCommandeLigne["data"][0]["commande_ligne.prix"],"toCommandeLigne ne renvoi pas le bon prix");
		$this->assertEquals($toCommandeLigne_attendu["data"][0]["commande_ligne.id_fournisseur"],$toCommandeLigne["data"][0]["commande_ligne.id_fournisseur"],"toCommandeLigne ne renvoi pas le bon id_fournisseur");
		$this->assertEquals($toCommandeLigne_attendu["data"][0]["commande_ligne.prix_achat"],$toCommandeLigne["data"][0]["commande_ligne.prix_achat"],"toCommandeLigne ne renvoi pas le bon prix_achat");
		$this->assertEquals($toCommandeLigne_attendu["data"][0]["commande_ligne.marge"],$toCommandeLigne["data"][0]["commande_ligne.marge"],"toCommandeLigne ne renvoi pas le bon marge");
		$this->assertEquals($toCommandeLigne_attendu["data"][0]["commande_ligne.marge_absolue"],$toCommandeLigne["data"][0]["commande_ligne.marge_absolue"],"toCommandeLigne ne renvoi pas le bon marge_absolue");
		//$this->assertEquals($toCommandeLigne_attendu,$toCommandeLigne,"toCommandeLigne ne renvoi pas le bon tableau");
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function testSelect_all(){
		$fields=array($this->obj->table.".ref",$this->obj->table.".produit",$this->obj->table.".quantite",$this->obj->table.".poids",$this->obj->table.".prix",$this->obj->table.".prix_achat",$this->obj->table.".id_fournisseur",$this->obj->table.".id_compte_absystech");
		$this->obj->q->reset()->where("id_devis",classes::decryptId($this->id_devis))->setView(array("order"=>$fields));
		$select_all=$this->obj->select_all(false,false,false,true);

		$this->assertEquals($select_all['count'],1,"1 select_all renvoie le bon count");
		$this->assertEquals($select_all['data'][0]["ref"],"TU","2 select_all renvoie le bon data");

		$this->obj->q->reset()->where("id_devis",0)->setView(array("order"=>$fields));
		$select_all=$this->obj->select_all(false,false,false,true);
		$this->assertEquals($select_all['count'],0,"3 select_all renvoie le bon count");
		$this->assertNull($select_all['data'][0],array(),"4 select_all renvoie le bon data");
	}
};