<?
/**
* @deprecated Classe bon_de_commande_ligne absystech test
* @since mardi 25 mai 2011
* @version 1.0
* @author MOUAD EL HIZABRI
* @package test
*/
class bon_de_commande_ligne_absystech_test extends ATF_PHPUnit_Framework_TestCase {
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
		$this->devis["devis"]['resume']='Tu_devis_bon_de_commande_ligne';
		$this->devis["devis"]['id_societe']=1;
		$this->devis["devis"]['validite']=date('Y-m-d');
		$this->devis["devis"]["date"]=date('Y-m-d');
		$this->devis["devis"]['prix']="200";
		$this->devis["devis"]['frais_de_port']="50";
		$this->devis["devis"]['prix_achat']="50";
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
	 	$this->id_commande = $this->_commande;
	 	//bon_de_commande
		$bon_de_commande["bon_de_commande"]=$this->commande["commande"];
		$bon_de_commande["bon_de_commande"]["id_commande"] = $this->id_commande;
		$bon_de_commande["bon_de_commande"]["ref"]="REF_bon_de_commande";
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
		$this->_bon_de_commande_ligne = $this->obj->ss("id_bon_de_commande",$this->_bon_de_commande);
		$this->requete_valide("bon_de_commande_ligne");

		//--------------------[instance STOCK]----------------------//
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
		$this->_livraison= $this->obj->sa();

		//--------------------[instance LIVRAISON LIGNE]----------------//
		$this->_livraison_ligne= ATF::livraison_ligne()->select_all();
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


	/** @test Test du constructeur bon_de_commande_ligne. */
	public function test_constructeur(){
		$this->_bon_de_commande_ligne = ATF::bon_de_commande_ligne();
	}


	/** @test Test de insert bon_de_commande_ligne. */
	public function test_insert(){
		$cadre_refreshed=array();
		$bon_de_commande_ligne=array(
								"id_bon_de_commande" => $this->_bon_de_commande
								,"etat" => "en_cours"
								,"date" => date('Y-m-d')
								,"produit"=>"ERDFER"
								,"tva" => 1

							);
		//test quantite
		try{
			$this->obj->insert($bon_de_commande_ligne,$this->s);
			$this->requete_valide("bon_de_commande_ligne");
		}catch(errorATF $e){
			$erreur = $e->getCode();
		}
		$this->assertEquals(600,$erreur,"ERREUR NON ATTRAPPEE 1");
		$bon_de_commande_ligne["quantite"] = 4;
		//test ref
		try{
			$this->obj->insert($bon_de_commande_ligne,$this->s);
			$this->requete_valide("bon_de_commande_ligne");
		}catch(errorATF $e){
			$erreur = $e->getCode();
		}
		$this->assertEquals(600,$erreur,"ERREUR NON ATTRAPPEE 2");
		$bon_de_commande_ligne["ref"] = "REF_bon_de_commande";
		try{
			$this->obj->insert($bon_de_commande_ligne,$this->s);
			$this->requete_valide("bon_de_commande_ligne");
		}catch(errorATF $e){
			$erreur = $e->getCode();
		}
		$this->assertEquals(600,$erreur,"ERREUR NON ATTRAPPEE 3");
		///renseignier le prix
		$bon_de_commande_ligne["prix"] = 200;
		//insertion apres les tests
		$this->obj->insert($bon_de_commande_ligne,$this->s,NULL,$cadre_refreshed);
	}


	/** @test Test de update bon_de_commande_ligne. */
	public function test_update(){
		//etat d'avant
		$this->assertEquals("en_cours",$this->_bon_de_commande_ligne[0]["etat"],"bon_de_commande_ligne update assert 1");
		//mise à jours d'etat de la bon_de_commande_ligne
		$this->obj->update(array("etat"=>"recu","id_bon_de_commande_ligne"=>$this->_bon_de_commande_ligne[0]["id_bon_de_commande_ligne"]),$this->s);
		$this->requete_valide("bon_de_commande_ligne");
		//test etat apres l'update
		$this->assertEquals("recu",$this->obj->select($this->_bon_de_commande_ligne[0]["id_bon_de_commande_ligne"],"etat"),"bon_de_commande_ligne update assert 2");
	}


	/** @test Test de bonCommandeLigne bon_de_commande_ligne. */
	public function test_bonCommandeLigne(){
		$fields=array(
			"bon_de_commande_ligne.ref"
			, "bon_de_commande_ligne.produit"
			, "bon_de_commande_ligne.quantite"
			, "bon_de_commande_ligne.prix"
			, "bon_de_commande_ligne.id_compte_absystech"
			, "bon_de_commande_ligne.serial"
		);
		$q=ATF::_s("pager")->getAndPrepare("bon_de_commande_ligneProduitsUpdate");
		$q->reset()->where("id_bon_de_commande",classes::decryptId($this->_bon_de_commande))->setView(array("order"=>$fields))->setCount();
		$this->obj->setQuerier($q);
		$bonCommandeLigne=$this->obj->BonCommandeLigne();
		$bonCommandeLigne_attendu=array(
										"data"=>array(
														"0"=>array(
																	"bon_de_commande_ligne.id_produit"=>"",
																	"bon_de_commande_ligne.produit"=>$this->devis_ligne[0]["produit"],
																	"bon_de_commande_ligne.quantite"=>$this->devis_ligne[0]["quantite"],
																	"bon_de_commande_ligne.ref"=>$this->devis_ligne[0]["ref"],
																	"bon_de_commande_ligne.poids"=>$this->devis_ligne[0]["poids"],
																	"bon_de_commande_ligne.prix"=>$this->devis_ligne[0]["prix"],
																	"bon_de_commande_ligne.prix_achat"=>$this->devis_ligne[0]["prix_achat"],
																	"bon_de_commande_ligne.id_compte_absystech"=>"",
																	"bon_de_commande_ligne.id_produit_fk"=>"",
																	"bon_de_commande_ligne.id_fournisseur_fk"=>"1",
																	"bon_de_commande_ligne.id_compte_absystech_fk"=>"",
																	"bon_de_commande_ligne.id_bon_de_commande_ligne"=>$this->devis_ligne[0]["id_devis_ligne"],
																	)
														),
										"count"=>"1"
										);

		$this->assertEquals($bonCommandeLigne_attendu["data"][0]["bon_de_commande_ligne.produit"],$bonCommandeLigne["data"][0]["bon_de_commande_ligne.produit"],"bonCommandeLigne ne renvoi pas le bon produit");
		$this->assertEquals($bonCommandeLigne_attendu["data"][0]["bon_de_commande_ligne.quantite"],$bonCommandeLigne["data"][0]["bon_de_commande_ligne.quantite"],"bonCommandeLigne ne renvoi pas le bon quantite");
		$this->assertEquals($bonCommandeLigne_attendu["data"][0]["bon_de_commande_ligne.ref"],$bonCommandeLigne["data"][0]["bon_de_commande_ligne.ref"],"bonCommandeLigne ne renvoi pas le bon ref");
		$this->assertEquals($bonCommandeLigne_attendu["data"][0]["bon_de_commande_ligne.poids"],$bonCommandeLigne["data"][0]["bon_de_commande_ligne.poids"],"bonCommandeLigne ne renvoi pas le bon poids");
		$this->assertEquals($bonCommandeLigne_attendu["data"][0]["bon_de_commande_ligne.prix"],$bonCommandeLigne["data"][0]["bon_de_commande_ligne.prix"],"bonCommandeLigne ne renvoi pas le bon prix");
		$this->assertEquals($bonCommandeLigne_attendu["data"][0]["bon_de_commande_ligne.prix_achat"],$bonCommandeLigne["data"][0]["bon_de_commande_ligne.prix_achat"],"bonCommandeLigne ne renvoi pas le bon prix_achat");
	}
}
?>