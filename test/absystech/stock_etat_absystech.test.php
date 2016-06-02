<?
/** 
* @deprecated Classe stock_etat absystech test
* @since mardi 25 mai 2011
* @version 1.0
* @author MOUAD EL HIZABRI
* @package test
*/
class stock_etat_absystech_test extends ATF_PHPUnit_Framework_TestCase {
	/**
	* instance de la classe stock
	* pour l'isolement des tests
	* @access private
	*/
	private $_stock;
	private $_bon_de_commande;
	private $_bon_de_commande_ligne;
	private $_affaire;
	private $_stock_etat;
	private $_commande;

	// test de validité d'une requet 
	private function requet_test($table){
		return $this->assertNotNull($this->_.$table["id_".$table],"La requête de ".$table." ne se crée pas... - assert ");
	}
	
	// Initialisation d'un jeu de test d'affaire
	private function init_affaire(){
		$this->_affaire=array(
					 "date" => date("Y-m-d")	
					,"id_societe" => $this->id_societe
					,"affaire" =>"Affaire 123"
					,"etat" => "devis"
					,"id_termes" =>"1"
					,"forecast" => "0"
					,"date"=>date("Y-m-d")
					,"code_commande_client" => "N425HJKG"
				);
		$this->_affaire['id_affaire']=ATF::affaire()->insert($this->_affaire);
		$this->requet_test('affaire');
	}
	
	// Initialisation d'un jeu de test de la commande
	private function init_commande(){
		$this->_commande= array(
					 "ref" =>"452278"
					,"id_societe" => $this->id_societe
					,"resume" =>"Commande AB"
					,"prix_achat" =>"1254"
					,"prix" =>"1320"
					,"etat" =>"en_cours"
					,"id_user" =>$this->id_user
					,"date" =>"2010-12-12"
					,"id_affaire"=>"123"
					,"tva"=>"1"
					,"frais_de_port"=>"20"
				);
		$this->_commande['id_commande'] = ATF::commande()->i($this->_commande);
		$this->requet_test('commande');
	}
	
	// Initialisation d'un jeu de test du bon_de_commande
	private function init_bon_de_commande(){
		$this->_bon_de_commande=array(
					 "id_commande" => $this->_commande['id_commande']
					,"id_affaire"=> $this->_affaire['id_affaire']
					,"ref" =>"45788"
					,"id_societe" => $this->id_societe
					,"resume" =>"Commande fournisseur AB"
					,"prix_achat" =>"1254"
					,"prix" =>"1320"
					,"etat" =>"recu"
					,"id_user" =>$this->id_user
					,"date" =>"2010-12-08"
					,"tva"=>"1"
					,"frais_de_port"=>"20"
					,"id_fournisseur"=> 1589
				);
	  $this->commande=$commande;	 	
	  $this->_bon_de_commande['id_bon_de_commande']=ATF::bon_de_commande()->i(array("bon_de_commande"=>$this->_bon_de_commande));
	  $this->requet_test('bon_de_commande');					
	}

	// Initialisation d'un jeu de test du bon_de_commande_ligne
	private function init_bon_de_commande_ligne(){
		$this->_bon_de_commande_ligne= array(
				 "id_bon_de_commande" =>$this->_bon_de_commande['id_bon_de_commande']
				,"ref" =>"45708"
				,"produit" => "Imprimante TMS"
				,"quantite" =>"1"
				,"prix" =>"1300"
				,"etat" =>"en_cours"
				,"date" =>"2010-12-08"
				,"tva"=>"1"
		  );
	   $this->_bon_de_commande_ligne['id_bon_de_commande_ligne']=ATF::bon_de_commande_ligne()->i($this->_bon_de_commande_ligne);
	   $this->requet_test('bon_de_commande_ligne');
	}
	
	// Initialisation d'un jeu de test du stock
	private function init_stock($ref){
		//$this->initUser(false);
		$this->_stock =	 array(
			"id_bon_de_commande_ligne" =>$this->_bon_de_commande_ligne['id_bon_de_commande_ligne']
			,"id_affaire" => $this->_affaire['id_affaire']
			,"ref" => $ref
			,"libelle" => "Imprimante TMS"
			,"date_achat" => date("Y-m-d")
			,"prix" => "230"
			,"serial" => "FRT2GHHJ45HJ"
			,"adresse_mac" => "MAC".$ref
			,"date_fin_immo" => "2010-12-11"
			,"affectation" => "affectation"
			,"serialAT" => $ref."-00-AT"
		 );
		$this->_stock['id_stock'] = ATF::stock()->insert($this->_stock);
		$this->requet_test('stock');
	}

	/**
	* cette méthode est appelée avant le test.
	* @access protected
	*/
	protected function setUp(){
		ATF::db()->begin_transaction(true);
		$this->initUser(false);
		$this->init_affaire();
		$this->init_commande();
		$this->init_bon_de_commande();
		$this->init_bon_de_commande_ligne();
		$this->init_stock("12452132");
	}
		
	/**
	* cette méthode est appelée apres le test.
	* @access protected
	*/
	protected function tearDown(){
		ATF::db()->rollback_transaction(true);
		//Flush des notices
		ATF::$msg->getNotices();
	}

	/** @test Test de getEtat stock_etat. */
	public function test_getEtat(){
		sleep(1);
		$_stock_etat = $this->obj->insert(array("id_stock" => $this->_stock['id_stock']));

		$etat=$this->obj->select($_stock_etat);
		$etat_get=$this->obj->getEtat($this->_stock['id_stock']);
		$this->assertNotNull($_stock_etat,"La requête ne se crée pas...");
		$this->assertNull($etat["commentaire"],"assert stock_etat getEtat 1");
		//etat par defaut :immo
		$this->assertEquals("stock",$etat["etat"],"assert stock_etat getEtat 2");
		$this->assertEquals("stock",$etat_get,"assert stock_etat getEtat 3");
		
		// Impossible d'anter dater update
		try {
			$this->obj->update(array(
				"etat" => "livr"
				,"id_stock_etat" => $etat['id_stock_etat']
				,"date" => "2000-01-01 00:00:00"
			));
		} catch (error $e) {
			$errno = $e->getCode();
		}
		$this->assertEquals(20974,$errno,"erreur modif non trouvee");
		
		// Impossible d'anter dater insert
		try {
			$this->obj->insert(array(
				"etat" => "stock"
				,"id_stock" => $etat['id_stock']
				,"date" => "2000-01-01 00:00:00"
			));
		} catch (error $e) {
			$errno = $e->getCode();
		}
		$this->assertEquals(20973,$errno,"erreur modif non trouvee");
		
		//nouveau etat: livré
		$_stock_etat_2 = $this->obj->update(array(
			"etat" => "livr"
			,"id_stock_etat" => $etat['id_stock_etat']
		));
		$etat_get=$this->obj->getEtat($this->_stock['id_stock']);
		//test etat
		$this->assertEquals("livr",$etat_get,"assert stock_etat getEtat 4");
	}
	
	/** test methode nombre de s etat d'un stock*/
	public function test_nb_etat_stock(){
		$nb=$this->obj->nb_etat_stock($this->_stock['id_stock']);
		$this->assertEquals(1,$nb,"assert stock_etat nb_etat_stock 1");
		$this->requet_test('stock');
	}

	public function test_checkEtatInventaire(){
		$this->obj->checkEtatInventaire(array("id_stock"=>$this->_stock['id_stock']));
		ATF::stock_etat()->q->reset()->where("id_stock" , $this->_stock['id_stock'])
											 ->setLimit(1)
											 ->addOrder("date", "desc");
		$etat = ATF::stock_etat()->select_row();
		
		$this->assertEquals($etat["commentaire"] , "Check Inventaire du ".date("d/m/Y") , "Check Invetaire bug");
	}
}
?>