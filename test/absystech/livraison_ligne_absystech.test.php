<?
/** 
* @deprecated Classe livraison_ligne absystech test
* @since mardi 25 mai 2011
* @version 1.0
* @author MOUAD EL HIZABRI
* @package test
*/
class livraison_ligne_absystech_test extends ATF_PHPUnit_Framework_TestCase {
	/**
	   Méthodes livraison_ligne:
			_delivery_status_verification  _constructeur
	*/
	
	
	/**
	* instance de la classe livraison_ligne
	* pour l'isolement des tests
	* @access private
	*/
	private $_stock;
	private $_stock_etat;	
	private $_devis;
	private $_affaire;
	private $_commande;
	private $_bon_de_commande;
	private $_bon_de_commande_ligne;
	private $_livraison;
	private $_livraison_ligne;


	/** 
	* test apres chaque requete SQL d'un jeu de test 
	*/
	private function requete_valide($table){
		return $this->assertNotNull($this->_.$table["id_".$table],"La requête de ".$table." ne se crée pas... - assert ");
	}
	
	
	// Initialisation d'un jeu de test 
	private function environnement_test(){		
		ATF::db()->truncate("stock");
		ATF::db()->truncate("livraison");
		//devis
		$contact["nom"]="Tu_devis";
		$this->id_contact=ATF::contact()->insert($contact);
		$this->devis["devis"]["id_contact"]=$this->id_contact;
		$this->devis["devis"]['resume']='Tu_devis_livraison_ligne';
		$this->devis["devis"]['id_societe']=1;
		$this->devis["devis"]['validite']=date('Y-m-d');
		$this->devis["devis"]['prix']="200";
		$this->devis["devis"]['frais_de_port']="50";
		$this->devis["devis"]['prix_achat']="50";
		$this->devis["devis"]["date"]=date('Y-m-d');
		$this->devis["values_devis"]=array("produits"=>'[{"devis_ligne__dot__ref":"REF_DEVIS","devis_ligne__dot__produit":"Tu_devis","devis_ligne__dot__quantite":"15","devis_ligne__dot__poids":"10","devis_ligne__dot__prix":"10","devis_ligne__dot__prix_achat":"10","devis_ligne__dot__id_fournisseur":"1","devis_ligne__dot__id_compte_absystech":"1","devis_ligne__dot__marge":97.14,"devis_ligne__dot__id_fournisseur_fk":"1"}]');
		
		//--------------------[instance DEVIS]-------------------//
		$this->_devis = ATF::devis()->insert($this->devis,$this->s);
		$this->requete_valide("devis");
		//ici c'est le devis qui crée l'affaire
		
		//--------------------[instance AFFAIRE]------------------//
		$this->_affaire = ATF::devis()->select($this->_devis,"id_affaire");
		$this->requete_valide("affaire");

		//Commande
		$commande["commande"]=$this->devis["devis"];
		$commande["commande"]["id_affaire"]=$this->_affaire;
		$commande["commande"]["id_devis"]=$this->_devis;
		$commande["values_commande"]=array("produits"=>'[{"commande_ligne__dot__ref":"REF_Produit"'.
														',"commande_ligne__dot__produit":"Tu_commande"'.
														',"commande_ligne__dot__quantite":"15"'.
														',"commande_ligne__dot__prix":"10"'.
														',"commande_ligne__dot__prix_achat":"10"'.
														',"commande_ligne__dot__id_fournisseur":"1"'.
														',"commande_ligne__dot__id_compte_absystech":"1"'.
														',"commande_ligne__dot__marge":97.14'.
														',"commande_ligne__dot__id_fournisseur_fk":"1"}]'
									       );
		unset($commande["commande"]["id_contact"]);
		unset($commande["commande"]["validite"]);
		
		//-------------------[instance COMMANDE]------------------//
		$this->_commande = ATF::commande()->insert($commande,$this->s);
		$this->requete_valide("commande");
		$this->commande=$commande;
	 	//bon_de_commande
		$bon_de_commande["bon_de_commande"]=$this->commande["commande"];
		$bon_de_commande["bon_de_commande"]["ref"]="REF_bon_de_commande";
		$bon_de_commande["bon_de_commande"]["id_commande"] = $this->_commande; 
		$bon_de_commande["bon_de_commande"]["id_fournisseurFinal"]= 1589;
		$bon_de_commande["bon_de_commande"]["id_fournisseur"]= 1589;	 
		$bon_de_commande["values_bon_de_commande"]=array("produits"=>'[{"bon_de_commande_ligne__dot__ref":"REF_Produit",'.
																	   '"bon_de_commande_ligne__dot__produit":"Tu_bon_de_commande"'.
																	  ',"bon_de_commande_ligne__dot__quantite":"15"'.
																	  ',"bon_de_commande_ligne__dot__prix":"10","bon_de_commande_ligne__dot__prix_achat":"10"'.
																	  ',"bon_de_commande_ligne__dot__id_fournisseur":"1"'.
																	  ',"bon_de_commande_ligne__dot__serial":"777"'.
																	  ',"bon_de_commande_ligne__dot__id_compte_absystech":"1"'.
																	  ',"bon_de_commande_ligne__dot__marge":97.14,'.
																	  '"bon_de_commande_ligne__dot__id_fournisseur_fk":"1"}]'
														);
		unset($bon_de_commande["bon_de_commande"]["prix_achat"]);
		unset($bon_de_commande["bon_de_commande"]["id_devis"]);
		$this->bon_de_commande=$bon_de_commande;
		
		//----------------[instance BON DE COMMANDE]------------------//
		$this->_bon_de_commande = ATF::bon_de_commande()->insert($this->bon_de_commande,$this->s);
		$this->requete_valide("bon_de_commande");
		
		//---------------[instance BON DE COMMANDE LIGNE]------------//
		$this->_bon_de_commande_ligne = ATF::bon_de_commande_ligne()->ss("id_bon_de_commande",$this->_bon_de_commande);
		$this->requete_valide("bon_de_commande_ligne");

		//--------------------[instance STOCK]----------------------//
		ATF::stock()->q->reset();
		$this->_stock = ATF::stock()->sa();
		$this->requete_valide("stock");
		$this->_stock_etat = ATF::stock_etat()->sa();
		$this->requete_valide("stock_etat");	

		$this->livraison["livraison"]["id_commande"] = $this->_commande;
		$this->livraison["livraison"]["id_affaire"]=$this->_affaire;
		$this->livraison["livraison"]["id_societe"]=$this->id_societe;
		$this->livraison["livraison"]["date"]=date('Y-m-d');
		$this->livraison["values_livraison"]=array("produits"=>'[{"stock__dot__id_stock_fk":'.$this->_stock[0]["id_stock"].',"stock__dot__ref":"1201","stock__dot__libelle":"manette","stock__dot__serial":"kokfghghhghhhgys"}'.
		                                                       ',{"stock__dot__id_stock_fk":'.$this->_stock[1]["id_stock"].',"stock__dot__ref":"1201","stock__dot__libelle":"manette","stock__dot__serial":"klmlkmlkh"}]');	
		//--------------------[instance LIVRAISON]---------------------//
		$id_livraison = ATF::livraison()->insert($this->livraison);
		$this->requete_valide("livraison");
		$this->_livraison=  ATF::livraison()->sa();
		
		//--------------------[instance LIVRAISON LIGNE]----------------//	
		$this->_livraison_ligne= $this->obj->sa();
		$this->requete_valide("livraison_ligne");
		
		//--------------------[instance STOCK ETAT]--------------------//
		$this->_stock_etat = ATF::stock_etat()->sa(); 
		$this->requete_valide("stock_etat");
		//------------------------------------------------------------//	
	}
	
	
	/**
	* cette méthode est appelée avant le test.
	* @access protected
	*/
	protected function setUp(){
		ATF::initialize();
		$this->initUser();
		$this->environnement_test();
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

	
	/** @test Test du constructeur livraison_ligne. */
	public function test_constructeur(){
		$this->_livraison_ligne = ATF::livraison_ligne();
	}


	/** @test Test de delivery_status_verification livraison_ligne. */
	public function test__delivery_status_verification(){
		
		$nb= $this->obj->delivery_status_verification($this->_livraison_ligne[0]['id_livraison']);
		//nombre de livraison en_cours
		$this->assertEquals(2,$nb,"livraison_ligne insert assert 3");
	}
	
	


}
?>