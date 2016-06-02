<?
class copieur_contrat_ligne_test extends ATF_PHPUnit_Framework_TestCase {
	
	/*@author Quentin JANON <qjanon@absystech.fr> */ 
	function setUp() {
		$this->initUser();
	}
	
	/*@author Quentin JANON <qjanon@absystech.fr> */ 
	function tearDown(){
		ATF::db()->rollback_transaction(true);
		//Flush des notices
		ATF::$msg->getNotices();
	}
	
	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function testToFactureLigne(){

		$copieur_contrat["copieur_contrat"]["id_societe"]=$this->id_societe;
		$copieur_contrat["copieur_contrat"]["date"]=date("Y-m-d");
		$copieur_contrat["copieur_contrat"]['duree']=36;
		$copieur_contrat["values_copieur_contrat"]=array("produits"=>'[{"copieur_contrat_ligne__dot__type":"fhdfghdf","copieur_contrat_ligne__dot__designation":"sdfghndhyfnsn sfnbsg","copieur_contrat_ligne__dot__quantite":"","copieur_contrat_ligne__dot__prix_achatNB":"0.00500","copieur_contrat_ligne__dot__prix_achatC":"0.50000","copieur_contrat_ligne__dot__prixNB":"0.00700","copieur_contrat_ligne__dot__prixC":"0.70000"},{"copieur_contrat_ligne__dot__type":"jgvjg","copieur_contrat_ligne__dot__designation":"jgvjgv","copieur_contrat_ligne__dot__quantite":"1","copieur_contrat_ligne__dot__prix_achatNB":"0.20000","copieur_contrat_ligne__dot__prix_achatC":"0.30000","copieur_contrat_ligne__dot__prixNB":"0.40000","copieur_contrat_ligne__dot__prixC":"0.50000"}]');

		$idC = ATF::copieur_contrat()->insert($copieur_contrat,$this->s);
		$id = ATF::copieur_contrat()->decryptId($idC);


		$this->obj->q->where("id_copieur_contrat",classes::decryptId($id))->setCount();
		$toFactureLigne=$this->obj->toFactureLigne();


		$this->assertEquals(2,$toFactureLigne['count'],"Erreur dans le nombre de lignes récupéré du contrat pour les factures.");
	}

	public function test_cleanAll() {
		$copieur_contrat["copieur_contrat"]["id_societe"]=$this->id_societe;
		$copieur_contrat["copieur_contrat"]["date"]=date("Y-m-d");
		$copieur_contrat["copieur_contrat"]['duree']=36;
		$copieur_contrat["values_copieur_contrat"]=array("produits"=>'[{"copieur_contrat_ligne__dot__type":"fhdfghdf","copieur_contrat_ligne__dot__designation":"sdfghndhyfnsn sfnbsg","copieur_contrat_ligne__dot__quantite":"","copieur_contrat_ligne__dot__prix_achatNB":"0.00500","copieur_contrat_ligne__dot__prix_achatC":"0.50000","copieur_contrat_ligne__dot__prixNB":"0.00700","copieur_contrat_ligne__dot__prixC":"0.70000"},{"copieur_contrat_ligne__dot__type":"jgvjg","copieur_contrat_ligne__dot__designation":"jgvjgv","copieur_contrat_ligne__dot__quantite":"1","copieur_contrat_ligne__dot__prix_achatNB":"0.20000","copieur_contrat_ligne__dot__prix_achatC":"0.30000","copieur_contrat_ligne__dot__prixNB":"0.40000","copieur_contrat_ligne__dot__prixC":"0.50000"}]');

		$idC = ATF::copieur_contrat()->insert($copieur_contrat,$this->s);
		$id = ATF::copieur_contrat()->decryptId($idC);		

		$this->obj->cleanAll($id);

		$this->obj->q->reset()->where('id_copieur_contrat',$id)->setCountOnly();
		$this->assertEquals(0,$this->obj->sa(),"Il ne devrait plus y avoir de lignes.");
	}
};
?>