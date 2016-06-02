<?
/** 
* @deprecated Classe livraison absystech test
* @since mardi 25 mai 2011
* @version 1.0
* @author MOUAD EL HIZABRI
* @package test
*/
class livraison_absystech_test extends ATF_PHPUnit_Framework_TestCase {
	/**
	   Méthodes livraison:
		    -delivery_Complete  -insert  
			-delete             -update
			-select_all         -can_update
            _constructeur
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
		ATF::db()->truncate("stock_etat");	
		ATF::db()->truncate("stock");
		ATF::db()->truncate("livraison");
		//devis
		$contact["nom"]="Tu_devis";
		$this->id_contact=ATF::contact()->insert($contact);
		$this->devis["devis"]["id_contact"]=$this->id_contact;
		$this->devis["devis"]['resume']='Tu_devis_livraison';
		$this->devis["devis"]["date"]=date("Y-m-d");
		$this->devis["devis"]['id_societe']=1;
		$this->devis["devis"]['validite']=date('Y-m-d');
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
		$commande["date"]=date("Y-m-d");
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
		$this->_bon_de_commande_ligne = ATF::bon_de_commande_ligne()->ss("id_bon_de_commande",$this->_bon_de_commande);
		$this->requete_valide("bon_de_commande_ligne");

		//--------------------[instance STOCK]----------------------//
		ATF::stock()->q->reset();
		$this->_stock = ATF::stock()->sa();
		$this->requete_valide("stock");
		$this->_stock_etat = ATF::stock_etat()->sa();
		$this->requete_valide("stock_etat");	

		$this->livraison["livraison"]["id_commande"] = $this->_commande;
		$this->livraison["livraison"]["id_societe"] = $this->id_societe;
		$this->livraison["livraison"]["id_affaire"]=$this->_affaire;
		$this->livraison["livraison"]["date"]=date('Y-m-d');
		$this->livraison["values_livraison"]=array("produits"=>'[{"stock__dot__id_stock_fk":'.$this->_stock[0]["id_stock"].',"stock__dot__ref":"1201","stock__dot__libelle":"manette","stock__dot__serial":"kokfghghhghhhgys"}'.
		                                                       ',{"stock__dot__id_stock_fk":'.$this->_stock[1]["id_stock"].',"stock__dot__ref":"1201","stock__dot__libelle":"manette","stock__dot__serial":"klmlkmlkh"}]');	
		//--------------------[instance LIVRAISON]---------------------//
		$id_livraison = $this->obj->insert($this->livraison);
		$this->requete_valide("livraison");
		$this->_livraison = $this->obj->decryptID($id_livraison);
		
		//--------------------[instance LIVRAISON LIGNE]----------------//	
		$this->_livraison_ligne= ATF::livraison_ligne()->sa();
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
		//ATF::initialize();
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


	/** @test Test du constructeur livraison.  */
	public function test_constructeur(){
		$this->_livraison = new livraison();	
	}


	/** @test Test de insert livraison, avec redirection vers l'affaire. */
	public function test_insert_affaire(){
		//nouveau bon de livraison
		$cadre_refreshed=array();
		$livraison_2["preview"] = true;
		$livraison_2["livraison"]["id_commande"] = $this->_commande;
		$livraison_2["livraison"]["id_affaire"]=$this->_affaire;
		$livraison_2["livraison"]["id_societe"] = $this->id_societe;
		$livraison_2["livraison"]["date"]=date('Y-m-d');
		$livraison_2["values_livraison"]=array("produits"=>'[{"stock__dot__id_stock_fk":'.$this->_stock[3]["id_stock"].
															',"stock__dot__ref":"EF410GHT"'.
															',"stock__dot__libelle":"PC"'.
															',"stock__dot__serial":"KIKIL12ER"}]'
											   );	
		//insertion du nouveau bon
		$this->obj->insert($livraison_2,$this->s,NULL,$cadre_refreshed);
		//la requete se crée bien
		$this->requete_valide("livraison");
		//mise à jours de l'instance $_livraison
		$this->_livraison= $this->obj->sa();
		$this->requete_valide("livraison");
		//2 enregistrement dans la table livraison
		$this->assertEquals(2,count($this->_livraison),"livraison insert assert 1");
		//1 enregistrement de cette livraison dans livraison_ligne
		$this->assertEquals(0,count($etat),"livraison insert assert 2");
	}
	
	/** @author Jérémie GWIAZDOWSKI <jgw@absystech.fr> test avec stock__dot__id_stock au lieu de stock__dot__id_stock_fk */
	public function test_insert_bug_fk(){
		//nouveau bon de livraison
		$cadre_refreshed=array();
		$livraison_2["preview"] = true;
		$livraison_2["livraison"]["id_commande"] = $this->_commande;
		$livraison_2["livraison"]["id_affaire"]=$this->_affaire;
		$livraison_2["livraison"]["id_societe"] = $this->id_societe;
		$livraison_2["livraison"]["date"]=date('Y-m-d');
		$livraison_2["values_livraison"]=array("produits"=>'[{"stock__dot__id_stock":'.$this->_stock[3]["id_stock"].
															',"stock__dot__ref":"EF410GHT"'.
															',"stock__dot__libelle":"PC"'.
															',"stock__dot__serial":"KIKIL12ER"}]'
											   );	
		//insertion du nouveau bon
		$this->obj->insert($livraison_2,$this->s,NULL,$cadre_refreshed);
		//la requete se crée bien
		$this->requete_valide("livraison");
	}

	/** @test Test du update livraison,redirection vers l'affaire */
	public function test_update_affaire(){
		//1 enregistrement dans la table livraison
		$this->assertEquals(1,count($this->_livraison),"livraison update assert 1");
		$cadre_refreshed =array();
		$inf = array(
					//bon signé
					"filestoattach"=>array("bon_de_livraison_signe"=>"08042011091341309.pdf")
					,"id_livraison"=>$this->_livraison
					,"id_affaire"=>$this->_affaire
				);
		$livraison_1_up = $this->obj->update($inf,$this->s,NULL,$cadre_refreshed);
		$this->requete_valide("livraison");	
	}
	
	
	/** @test Test du update livraison,redirection vers le panele livraison */
	public function test_update_select_all(){
		//1 enregistrement dans la table livraison
		$this->assertEquals(1,count($this->_livraison),"livraison update assert 1");
		$inf = array(
					//pas de bon signé
					"filestoattach"=>array()
					,"id_livraison"=>$this->_livraison
				);
		$livraison_1_up = $this->obj->update($inf,$this->s,NULL);
		$this->requete_valide("livraison");
	}


	/** @test Test du delete livraison,redirection vers l'affaire */
	public function test_delete_affaire_simple(){
		//1 enregistrement dans la table livraison
		$this->assertEquals(1,count($this->_livraison),"livraison delete assert 1");
		$cadre_refreshed =array();
		$livraison_1_up = $this->obj->delete(array("id"=>array("0"=>$this->_livraison)),$this->s,NULL,$cadre_refreshed);
		$this->requete_valide("livraison");	
		//0 enregistrement dans la table livraison
		$this->_livraison = $this->obj->sa();
		$this->assertEquals(0,$this->_livraison["count"],"livraison delete assert 2");
	}


	/** @test Test du delete livraison,redirection vers l'affaire */
	public function test_delete_affaire(){
		//nouveau bon de livraison
		$cadre_refreshed=array();
		$livraison_2["preview"] = true;
		$livraison_2["livraison"]["id_commande"] = $this->_commande;
		$livraison_2["livraison"]["id_societe"] = $this->id_societe;
		$livraison_2["livraison"]["id_affaire"]=$this->_affaire;
		$livraison_2["livraison"]["date"]=date('Y-m-d');
		$livraison_2["values_livraison"]=array("produits"=>'[{"stock__dot__id_stock_fk":'.$this->_stock[3]["id_stock"].
															',"stock__dot__ref":"EF410GHT"'.
															',"stock__dot__libelle":"PC"'.
															',"stock__dot__serial":"KIKIL812ER"}]'
											   );	
		//insertion du nouveau bon
		$this->obj->insert($livraison_2,$this->s,NULL,$cadre_refreshed);
		//la requete se crée bien
		$this->requete_valide("livraison");
		//nouveau bon de livraison
		$livraison_3["preview"] = true;
		$livraison_3["livraison"]["id_commande"] = $this->_commande;
		$livraison_3["livraison"]["id_societe"] = $this->id_societe;
		$livraison_3["livraison"]["id_affaire"]=$this->_affaire;
		$livraison_3["livraison"]["date"]=date('Y-m-d');
		$livraison_3["values_livraison"]=array("produits"=>'[{"stock__dot__id_stock_fk":'.$this->_stock[2]["id_stock"].
															',"stock__dot__ref":"EF410GHT"'.
															',"stock__dot__libelle":"PCGHG"'.
															',"stock__dot__serial":"KIKI88L12ER"}]'
											   );	
		//insertion du nouveau bon
		$this->obj->insert($livraison_3,$this->s,NULL,$cadre_refreshed);
		//la requete se crée bien
		$this->requete_valide("livraison");
		$this->obj->q->setCount();
		$r = $this->obj->select_all();
		$this->_livraison = $r['data'];
		//3 enregistrement dans la table livraison
		$this->assertEquals(3,count($this->_livraison),"livraison delete assert 1");
		$this->assertArrayHasKey("allowTermine",$this->_livraison[0],"Il manque le allowTermine en retour de select_all");
		$livraison_1_up = $this->obj->delete(array("id"=>array("0"=>$this->_livraison[0]["livraison.id_livraison"])),$this->s,NULL,$cadre_refreshed);
		$this->requete_valide("livraison");	
		//2 enregistrement dans la table livraison
		$this->_livraison = $this->obj->sa();
		$this->assertEquals(2,$this->_livraison["count"],"livraison delete assert 2");
	}

	
	
	
	/** @test Test de can_update livraison. */
	public function test_can_update_true(){
		$this->assertTrue($this->obj->can_update($this->_livraison));
		//$cadre_refreshed =array();
		
		//$target = $this->obj->filepath($this->_livraison,"bon_de_livraison_signe");
		//util::file_put_contents($target,"EREZERZR");
		//$this->assertFileExists($target);
		//$this->assertFalse($this->obj->can_update($this->_livraison));
		//util::rm($target);
		
	}


	//* @test Test de _delivery_Complete livraison. 
	public function test_delivery_Complete(){
		//nouveau stock
		$new_stock = array(
				 "id_affaire" => $this->_affaire
				,"ref" => "efrgte"
				,"libelle" => "Imprimante TMS"
				,"date_achat" => date("Y-m-d")
				,"prix" => "230"
				,"serial" => "FRT2GHHJ45HJ"
				,"etat" => "livraison"
				,"adresse_mac" => "FGHRGHDFDG41"
				,"date_fin_immo" => "2010-12-11"
				,"affectation" => "affectation"
				,"serialAT" => "084530-151010-00-AT"
		 );
		$this->_stock = ATF::stock()->insert($new_stock);
		$this->requete_valide("stock");
		//mise à jours des etats stock	
		ATF::stock_etat()->q->reset();
		$this->_stock_etat=ATF::stock_etat()->select_all();
		
		$this->assertEquals("livraison",$this->_stock_etat[0]["etat"],"livraison delivery_Complete assert 2");
		$this->assertEquals("livraison",$this->_stock_etat[1]["etat"],"livraison delivery_Complete assert 3");
		
		$this->obj->delivery_Complete(array("id_livraison"=>$this->_livraison));
		$this->requete_valide("livraison");
		ATF::stock_etat()->q->reset();
		$this->_stock_etat = ATF::stock_etat()->select_all();
		$this->requete_valide("stock_etat");
		//verification etat stock
		$this->assertEquals("livr",$this->_stock_etat[0]["etat"],"livraison delivery_Complete assert 4");
		$this->assertEquals("livr",$this->_stock_etat[1]["etat"],"livraison delivery_Complete assert 5");
		//virefication etat livraison
		$this->assertEquals("termine",$this->obj->select($this->_livraison,"etat"),"livraison delivery_Complete assert 6");
		
	}
	
	
	/** @test Test de _delivery_Complete livraison, redirection module livraison */
	public function test_delivery_Complete_select_all(){
		sleep(1);
		//livraison directe (sans lien avec affaire)
		$this->livraison["livraison"]["id_commande"] = $this->_commande;
		unset($this->livraison["livraison"]["id_affaire"]);
		$this->livraison["livraison"]["date"]=date('Y-m-d');
		$this->livraison["values_livraison"]=array("produits"=>'[{"stock__dot__id_stock_fk":'.$this->_stock[0]["id_stock"].',"stock__dot__ref":"1201","stock__dot__libelle":"manette","stock__dot__serial":"kokfghghhghhhgys"}'.
		                                                       ',{"stock__dot__id_stock_fk":'.$this->_stock[1]["id_stock"].',"stock__dot__ref":"1201","stock__dot__libelle":"manette","stock__dot__serial":"klmlkmlkh"}]');	
		//--------------------[instance LIVRAISON]---------------------//
		$id_livraison = $this->obj->insert($this->livraison);
		$this->requete_valide("livraison");
		$this->_livraison= $this->obj->decryptID($id_livraison);
		//mise à jours de l'instance stock_etat
		ATF::stock_etat()->q->reset();
		$this->_stock_etat = ATF::stock_etat()->select_all();
		
		//enciens etat du stock
		$this->assertEquals("livraison",$this->_stock_etat[0]["etat"],"livraison delivery_Complete assert 1");
		$this->assertEquals("livraison",$this->_stock_etat[1]["etat"],"livraison delivery_Complete assert 2");
		//appliquer la methode
		$this->obj->delivery_Complete(array("id_livraison"=>$this->_livraison));
		$this->requete_valide("livraison");
		ATF::stock_etat()->q->reset();
		$this->_stock_etat = ATF::stock_etat()->select_all();
		$this->requete_valide("stock_etat");
		//verification etat stock
		$this->assertEquals("livr",$this->_stock_etat[0]["etat"],"livraison delivery_Complete assert 4");
		$this->assertEquals("livr",$this->_stock_etat[1]["etat"],"livraison delivery_Complete assert 5");
		//virefication etat livraison
		$this->assertEquals("termine",$this->obj->select($this->_livraison,"etat"),"livraison delivery_Complete assert 6");		
	}

	/* @author Jérémie Gwiazdowski <jgw@absystech.fr> */
	public function test_default_value(){
		$this->environnement_test();		
		//Default value parent
		$value=$this->obj->default_value("id_societe");
		$this->assertNull($value);
		//Default value empty
		$value=$this->obj->default_value();
		$this->assertNull($value);	
		
		ATF::_r("id_commande",$this->_commande);
		$value=$this->obj->default_value("id_societe");
		$this->assertEquals($value,1,"La société n'est pas la bonne !!");	
	}	

	//@author Yann GAUTHERON <ygautheron@absystech.fr> 
	function testGeneratePDF(){
		$livraison=$this->obj->select($this->_livraison);
		$livraison['id'] = $livraison['id_livraison'];
		$pathAttendu = $this->obj->filepath($livraison["id_livraison"],"bon_de_livraison",false);
		touch($pathAttendu);

		$pathAttendu = $this->obj->filepath($livraison["id_livraison"],"bon_de_livraison",true);
		$this->obj->generatePDF($livraison,$this->s);
		$this->assertFileExists($pathAttendu,"Le fichier ne s'est pas généré");

		$notice = array(
			array(
				"msg"=>"Ancien BL envoyé par email...",
				"title"=>"",
				"timer"=>""
			),
			array(
				"msg"=>"BL regénéré avec succès !",
				"title"=>"",
				"timer"=>""
			)
		);

		$this->assertEquals($notice,ATF::$msg->getNotices(),"La notice ne se fait pas bien");

		util::rm($pathAttendu);

		$this->obj->generatePDF($livraison,$this->s);

		$warning = array(
			array(
				"msg"=>"L'ancien fichier pdf du BL n'existait pas !",
				"title"=>"",
				"timer"=>""
			)
		);
		$this->assertEquals($warning,ATF::$msg->getWarnings(),"La warning ne se fait pas");
	}

}
?>