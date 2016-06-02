<?
class devis_test extends ATF_PHPUnit_Framework_TestCase {
	
	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function setUp() {
		$this->initUser();
		$devis = new devis();
	}
	
	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function tearDown(){
		ATF::db()->rollback_transaction(true);
	}
	
	
	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function testGetTotalPipe(){
		$this->obj->q->reset()
			->addCondition("devis.etat","attente")
			->addCondition("devis.etat","bloque")
			->setDimension('cell')
			->addField("SUM(`devis`.`prix`)","nb");
		$getTotalPipe_sa=$this->obj->sa();
		$getTotalPipe=$this->obj->getTotalPipe();
		$this->assertEquals($getTotalPipe_sa,$getTotalPipe,"1 getTotalPipe ne renvoie pas tous le bon montant sans forcast");

		$this->obj->q->reset()
			->addCondition("devis.etat","attente")
			->addCondition("devis.etat","bloque")
			->setDimension('cell')
			->addJointure("devis","id_affaire","affaire","id_affaire")
			->addField("SUM(`devis`.`prix`*`affaire`.`forecast`/100)","nb");
		$getTotalPipe_sa=$this->obj->sa();
		$getTotalPipe=$this->obj->getTotalPipe(true);
		$this->assertEquals($getTotalPipe_sa,$getTotalPipe_sa,"2 getTotalPipe ne renvoie pas tous le bon montant avec forcast");
	}
	
	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function testGetTotalPipePondere(){
		$this->obj->q->reset()
			->addCondition("devis.etat","attente")
			->addCondition("devis.etat","bloque")
			->setDimension('cell')
			->addJointure("devis","id_affaire","affaire","id_affaire")
			->addField("SUM(`devis`.`prix`*`affaire`.`forecast`/100)","nb");
		$getTotalPipe_sa=$this->obj->sa();
		$getTotalPipePondere=$this->obj->getTotalPipePondere();
		$this->assertEquals($getTotalPipe_sa,$getTotalPipePondere,"2 getTotalPipePondere ne renvoie pas tous le bon montant");
	}

	function test_insert_devis(){
		$contact["nom"]="Tu_devis";
		$this->id_contact=ATF::contact()->insert($contact);
		$this->devis["devis"]["id_contact"]=$this->id_contact;
		$this->devis["devis"]["date"]=date("Y-m-d");
		$this->devis["devis"]['resume']='Tu_devis';
		$this->devis["devis"]['id_societe']=1;
		$this->devis["devis"]['validite']=date('Y-m-d');
		$this->devis["devis"]['prix']="200";
		$this->devis["devis"]['frais_de_port']="50";
		$this->devis["devis"]['prix_achat']="50";
		$this->devis["devis"]['ref']="REF Devis TU";
		$this->devis["devis"]['tva']=19.6;
		$this->devis["devis"]['id_user']= 1;
		$this->devis["values_devis"]=array("produits"=>'[{"devis_ligne__dot__ref":"TU","devis_ligne__dot__produit":"Tu_devis","devis_ligne__dot__quantite":"15","devis_ligne__dot__poids":"10","devis_ligne__dot__prix":"10","devis_ligne__dot__prix_achat":"10","devis_ligne__dot__id_fournisseur":"1","devis_ligne__dot__id_compte_absystech":"1","devis_ligne__dot__marge":97.14,"devis_ligne__dot__id_fournisseur_fk":"1"}]');
		$this->id_devis = $this->obj->insert_devis($this->devis,$this->s);
	}
};
?>