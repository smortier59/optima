<?
/** 
* @deprecated Classe bon_de_commande absystech test
* @since mardi 25 mai 2011
* @version 1.0
* @author MOUAD EL HIZABRI
* @package test
*/
class bon_de_commande_absystech_test extends ATF_PHPUnit_Framework_TestCase {
	/**
	   Méthodes bon_de_commande:
			_constructeur   _select_all   -autocompleteConditions     -delete
			_insert         _update       _default_value              _getProviderUrl          _setCompleted 
	*/  
	
	
	/**
	* instance de la classe bon-de_commande
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
		ATF::db()->truncate("bon_de_commande");
		//devis
		$contact["nom"]="Tu_devis";
		$this->id_contact=ATF::contact()->insert($contact);
		$this->devis["devis"]["id_contact"]=$this->id_contact;
		$this->devis["devis"]['resume']='Tu_devis_bon_de_commande';
		$this->devis["devis"]['id_societe']=1;
		$this->devis["devis"]["date"]=date('Y-m-d');
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
		$id_commande = ATF::commande()->insert($commande,$this->s);
		$this->requete_valide("commande");
		$this->commande=$commande;
		$this->id_commande = $id_commande;
	 	//bon_de_commande
		$bon_de_commande["bon_de_commande"]=$this->commande["commande"];
		$bon_de_commande["bon_de_commande"]["id_commande"] = $id_commande; 
		$bon_de_commande["bon_de_commande"]["ref"]="REF_bon_de_commande";
		$bon_de_commande["bon_de_commande"]["id_fournisseurFinal"]= 1589;
		$bon_de_commande["bon_de_commande"]["id_fournisseur"]= 1589;
		$bon_de_commande["values_bon_de_commande"]=array("produits"=>'[{"bon_de_commande_ligne__dot__ref":"REF_Produit",'.
																	   '"bon_de_commande_ligne__dot__produit":"Tu_bon_de_commande"'.
																	  ',"bon_de_commande_ligne__dot__quantite":"15"'.
																	  ',"bon_de_commande_ligne__dot__prix":"10","bon_de_commande_ligne__dot__prix_achat":"10"'.
																	  ',"bon_de_commande_ligne__dot__id_fournisseur":"1"'.
																	  ',"bon_de_commande_ligne__dot__serial":"GFGFGFG"'.
																	  ',"bon_de_commande_ligne__dot__id_compte_absystech":"1"'.
																	  ',"bon_de_commande_ligne__dot__marge":97.14,'.
																	  '"bon_de_commande_ligne__dot__id_fournisseur_fk":"1"}]'
														 );
		unset($bon_de_commande["bon_de_commande"]["prix_achat"]);
		unset($bon_de_commande["bon_de_commande"]["id_devis"]);
		unset($bon_de_commande["bon_de_commande"]["affaire"]);
		$this->bon_de_commande=$bon_de_commande;
		
		//----------------[instance BON DE COMMANDE]------------------//
		$this->_bon_de_commande = $this->obj->insert($this->bon_de_commande,$this->s);
		$this->requete_valide("bon_de_commande");
		//print_r($this->_bon_de_commande );
		
		//---------------[instance BON DE COMMANDE LIGNE]------------//
		$this->_bon_de_commande_ligne = ATF::bon_de_commande_ligne()->ss("id_bon_de_commande",$this->_bon_de_commande);
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
		$this->_livraison=  ATF::livraison()->sa();
		
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


	/** @test Test du constructeur bon_de_commande.  */
	public function test_constructeur(){
		$this->_bon_de_commande = new bon_de_commande();	
	}


	/** @test Test de select_all bon_de_commande. */
	public function test_select_all(){
		//inserer 20 enregistrement de bon de commande
		for($i=1;$i<=20;$i++){
			$bon_de_commande["bon_de_commande"]=array(
										"id_affaire" => $this->_affaire
										,"etat" => "en_cours"
										,"date" => date('Y-m-d') 
										,"id_societe" => 1
										,"ref" => "REF_bon_de_commande"										
										,"resume" => "Tu_devis"
										,"prix_achat" => 0
										,"prix" => 200
										,"id_user" => 131
										,"id_commande" =>$this->_commande
										,"tva" => 1
										,"frais_de_port" => 60
										,"id_fournisseurFinal" => 1589
										,"id_fournisseur" => 1589
									);
			$this->obj->insert($bon_de_commande,$this->s);
			$this->requete_valide("bon_de_commande");
		}
		$this->_bon_de_commande = $this->obj->select_all();
		//print_r($this->_bon_de_commande);
		//tester le nombre de ligne dans le select all:  21
		$this->assertEquals(21,count($this->_bon_de_commande),"bon_de_commande select_all assert 1");
	}

	// @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	public function test_insertError(){
			$bon_de_commande["bon_de_commande"]=$this->commande["commande"];
			$bon_de_commande["bon_de_commande"]["id_commande"] = $this->id_commande; 
			$bon_de_commande["bon_de_commande"]["ref"]="REF_bon_de_commande";
			$bon_de_commande["bon_de_commande"]["id_fournisseurFinal"]= 1589;
			$bon_de_commande["bon_de_commande"]["id_fournisseur"]= 1589;
			$bon_de_commande["values_bon_de_commande"]=array("produits"=>'[{"bon_de_commande_ligne__dot__ref":"REF_Produit",'.
																		   '"bon_de_commande_ligne__dot__produit":"Tu_bon_de_commande"'.
																		  ',"bon_de_commande_ligne__dot__quantite":"30"'.
																		  ',"bon_de_commande_ligne__dot__prix":"10","bon_de_commande_ligne__dot__prix_achat":"10"'.
																		  ',"bon_de_commande_ligne__dot__id_fournisseur":"1"'.
																		  ',"bon_de_commande_ligne__dot__serial":"GFGFGFG"'.
																		  ',"bon_de_commande_ligne__dot__id_compte_absystech":"1"'.
																		  ',"bon_de_commande_ligne__dot__marge":97.14,'.
																		  '"bon_de_commande_ligne__dot__id_fournisseur_fk":"1"}]'
															 );
			unset($bon_de_commande["bon_de_commande"]["prix_achat"]);
			unset($bon_de_commande["bon_de_commande"]["id_devis"]);
			unset($bon_de_commande["bon_de_commande"]["affaire"]);			
			ATF::bon_de_commande()->d($this->_bon_de_commande_ligne);
			ATF::bon_de_commande()->d($this->_bon_de_commande);
			
			try{
				$this->obj->insert($bon_de_commande,$this->s);
			}catch(error $e){			
				$erreur = $e->getMessage();
			}
			$this->assertEquals("Quantité saisie 30 alors que la quantité max pour le produit ref : REF_Produit est de 15.00",$erreur,"ERREUR NON ATTRAPPEE 1");
			
			
			$bon_de_commande["values_bon_de_commande"]=array("produits"=>'[{"bon_de_commande_ligne__dot__ref":"REF_Produit",'.
																		   '"bon_de_commande_ligne__dot__produit":"Tu_bon_de_commande"'.
																		  ',"bon_de_commande_ligne__dot__quantite":"10"'.
																		  ',"bon_de_commande_ligne__dot__prix":"10","bon_de_commande_ligne__dot__prix_achat":"10"'.
																		  ',"bon_de_commande_ligne__dot__id_fournisseur":"1"'.
																		  ',"bon_de_commande_ligne__dot__serial":"GFGFGFG"'.
																		  ',"bon_de_commande_ligne__dot__id_compte_absystech":"1"'.
																		  ',"bon_de_commande_ligne__dot__marge":97.14,'.
																		  '"bon_de_commande_ligne__dot__id_fournisseur_fk":"1"}]'
															 );
			$this->obj->insert($bon_de_commande,$this->s);
			$bon_de_commande["values_bon_de_commande"]=array("produits"=>'[{"bon_de_commande_ligne__dot__ref":"REF_Produit",'.
																		   '"bon_de_commande_ligne__dot__produit":"Tu_bon_de_commande"'.
																		  ',"bon_de_commande_ligne__dot__quantite":"20"'.
																		  ',"bon_de_commande_ligne__dot__prix":"10","bon_de_commande_ligne__dot__prix_achat":"10"'.
																		  ',"bon_de_commande_ligne__dot__id_fournisseur":"1"'.
																		  ',"bon_de_commande_ligne__dot__serial":"GFGFGFG"'.
																		  ',"bon_de_commande_ligne__dot__id_compte_absystech":"1"'.
																		  ',"bon_de_commande_ligne__dot__marge":97.14,'.
																		  '"bon_de_commande_ligne__dot__id_fournisseur_fk":"1"}]'
															 );
			try{
				$this->obj->insert($bon_de_commande,$this->s);
			}catch(error $e){			
				$erreur = $e->getMessage();
			}
			$this->assertEquals("Quantité saisie 20 + quantite déja commandée 10 = 30 alors que la quantité max pour le produit ref : REF_Produit est de 15.00",$erreur,"ERREUR NON ATTRAPPEE 2");
							
	}

	/** @test Test de insert bon_de_commande,redirection vers panel bon_de_commande */
	public function test_insert_select_all(){
		$cadre_refreshed=array();
		$bon_de_commande=array(
								"id_affaire" => $this->_affaire
								,"etat" => "en_cours"
								,"date" => date('Y-m-d') 
								,"id_societe" => 3
								,"id_fournisseur" =>"1"
								,"prix_achat" => 1000
								,"id_user" => 130
								,"tva" => 1
								,"frais_de_port" => 30
								,"id_fournisseurFinal" => 1589
							);
		//test ref	
		try{
			$this->obj->insert($bon_de_commande,$this->s);
			$this->requete_valide("bon_de_commande");
		}catch(error $e){			
			$erreur = $e->getCode();
		}
		$this->assertEquals(12,$erreur,"ERREUR NON ATTRAPPEE 1");
		$bon_de_commande["ref"] = "REF_bon_de_commande";
		//test resume
		try{
			$this->obj->insert($bon_de_commande,$this->s);
			$this->requete_valide("bon_de_commande");
		}catch(error $e){			
			$erreur = $e->getCode();
		}
		$this->assertEquals(12,$erreur,"ERREUR NON ATTRAPPEE 2");
		
	}
	
	// Insertion de BdC avec stock existant
	// @author Yann GAUTHERON <ygautheron@absystech.fr>
	public function test_insert_stock_existant(){		
		// Check erreur pas de stock
		$erreur = NULL;
		try {
			$bon_de_commande["bon_de_commande"]=$this->commande["commande"];
			$bon_de_commande["bon_de_commande"]["id_commande"] = $this->id_commande;
			$bon_de_commande["bon_de_commande"]["ref"]="REF_bon_de_commande";
			$bon_de_commande["bon_de_commande"]["id_fournisseurFinal"]="1589";
			$bon_de_commande["bon_de_commande"]["id_fournisseur"]="1589";
			$bon_de_commande["values_bon_de_commande"]=array("produits"=>
				'[{"bon_de_commande_ligne__dot__ref":"REF_Prod",'.
				'"bon_de_commande_ligne__dot__produit":"Tu_bon_de_commande_affaire"'.
				',"bon_de_commande_ligne__dot__quantite":"1"'.
				',"bon_de_commande_ligne__dot__prix":"210"'.
				',"bon_de_commande_ligne__dot__prix_achat":"100"'.
				',"bon_de_commande_ligne__dot__id_fournisseur":"1"'.
				',"bon_de_commande_ligne__dot__serial":"777"'.
				',"bon_de_commande_ligne__dot__etat":"recu"'.
				',"bon_de_commande_ligne__dot__id_compte_absystech":"1"'.
				',"bon_de_commande_ligne__dot__marge":97.14,'.
				'"bon_de_commande_ligne__dot__id_fournisseur_fk":"1"}]'
			);
			unset($bon_de_commande["bon_de_commande"]["prix_achat"]);
			unset($bon_de_commande["bon_de_commande"]["id_devis"]);
			unset($bon_de_commande["bon_de_commande"]["affaire"]);
			$this->bon_de_commande=$bon_de_commande;
			
			$this->_bon_de_commande = $this->obj->insert($this->bon_de_commande,$this->s,NULL);
		} catch (errorStock $e) {
			$erreur = $e->getCode();
		}
		$this->assertEquals(9,$erreur,"Le stock ne devrait pas exister");

		// Insertion de stock
		$cadre_refreshed=array();
		$redirection_custom=true;
		$new_stock = array(
				"ref" => "REF_Prod"
				,"libelle" => "Imprimante TMS"
				,"prix" => "230"
				,"etat" => "stock"
				,"quantite" => "1"
				,"adresse_mac" => "FGHRGHFDFDG3"
				,"date_fin_immo" => "2012-12-12"
				,"date_achat" => date("Y-m-d")
		 );
		$id_stock = ATF::stock()->insert_stock($new_stock,$this->s);
		
		// bon_de_commande
		$bon_de_commande["bon_de_commande"]=$this->commande["commande"];
		$bon_de_commande["bon_de_commande"]["ref"]="REF_bon_de_commande";
		$bon_de_commande["bon_de_commande"]["id_fournisseurFinal"]="1589";
		$bon_de_commande["bon_de_commande"]["id_fournisseur"]="1589";
		$bon_de_commande["bon_de_commande"]["id_commande"]=$this->id_commande;
		$bon_de_commande["values_bon_de_commande"]=array("produits"=>
			'[{"bon_de_commande_ligne__dot__ref":"REF_Prod",'.
			'"bon_de_commande_ligne__dot__produit":"Tu_bon_de_commande_affaire"'.
			',"bon_de_commande_ligne__dot__quantite":"1"'.
			',"bon_de_commande_ligne__dot__prix":"210"'.
			',"bon_de_commande_ligne__dot__prix_achat":"100"'.
			',"bon_de_commande_ligne__dot__id_fournisseur":"1"'.
			',"bon_de_commande_ligne__dot__serial":"777"'.
			',"bon_de_commande_ligne__dot__etat":"recu"'.
			',"bon_de_commande_ligne__dot__id_compte_absystech":"1"'.
			',"bon_de_commande_ligne__dot__marge":97.14,'.
			'"bon_de_commande_ligne__dot__id_fournisseur_fk":"1"}]'
		);
		unset($bon_de_commande["bon_de_commande"]["prix_achat"]);
		unset($bon_de_commande["bon_de_commande"]["id_devis"]);
		unset($bon_de_commande["bon_de_commande"]["affaire"]);
		$this->bon_de_commande=$bon_de_commande;
	//----------------[instance BON DE COMMANDE]------------------//
		$this->_bon_de_commande = $this->obj->insert($this->bon_de_commande,$this->s,NULL);
		$this->requete_valide("bon_de_commande");
			
		// Check que c'est bien le bon id_stock qui est atttribué en tant que stock reçu
		$id_bdcl = ATF::stock()->select($id_stock,'id_bon_de_commande_ligne');
		$id_bdc = ATF::bon_de_commande_ligne()->select($id_bdcl,'id_bon_de_commande');
		$this->assertEquals($this->_bon_de_commande,$id_bdc,"Le stock n'a pas été associé au bon bon de commande");
	}

	//@test Test de insert bon_de_commande,redirection vers l'affaire, avec une qantite stock 0 
	public function test_insert_affaire_stock_quantite_zero(){
		$cadre_refreshed=array();
		//bon_de_commande
		$bon_de_commande["bon_de_commande"]=$this->commande["commande"];
		$bon_de_commande["bon_de_commande"]["ref"]="REF_bon_de_commande";
		$bon_de_commande["bon_de_commande"]["id_fournisseurFinal"]="1589";
		$bon_de_commande["bon_de_commande"]["id_fournisseur"]="1589";
		$bon_de_commande["bon_de_commande"]["id_commande"]= $this->id_commande;
		$bon_de_commande["values_bon_de_commande"]=array("produits"=>'[{"bon_de_commande_ligne__dot__ref":"REF_Produit",'.
																	   '"bon_de_commande_ligne__dot__produit":"Tu_bon_de_commande_affaire"'.
																	  ',"bon_de_commande_ligne__dot__quantite":"0"'.
																	  ',"bon_de_commande_ligne__dot__prix":"210"'.
																	  ',"bon_de_commande_ligne__dot__prix_achat":"100"'.
																	  ',"bon_de_commande_ligne__dot__id_fournisseur":"1"'.
																	  ',"bon_de_commande_ligne__dot__serial":"777"'.
																	  ',"bon_de_commande_ligne__dot__id_compte_absystech":"1"'.
																	  ',"bon_de_commande_ligne__dot__marge":97.14,'.
																	  '"bon_de_commande_ligne__dot__id_fournisseur_fk":"1"}]'
														 );
		unset($bon_de_commande["bon_de_commande"]["prix_achat"]);
		unset($bon_de_commande["bon_de_commande"]["id_devis"]);
		unset($bon_de_commande["bon_de_commande"]["affaire"]);
		$this->bon_de_commande=$bon_de_commande;
		
		//----------------[instance BON DE COMMANDE]------------------//
		$this->_bon_de_commande = $this->obj->insert($this->bon_de_commande,$this->s,NULL,$cadre_refreshed);
		$this->requete_valide("bon_de_commande");	
	}
	
	
	/** @test Test de update bon_de_commande. */
	public function test_update(){
		//etat d'avant
		$this->assertEquals("en_cours",$this->obj->select($this->_bon_de_commande,"etat"),"bon_de_commande update assert 1");
		//mise à jours d'etat de la bon_de_commande
		$this->obj->update(array("etat"=>"recu","id_bon_de_commande"=>$this->_bon_de_commande),$this->s);
		$this->requete_valide("bon_de_commande");
		$this->assertEquals("recu",$this->obj->select($this->_bon_de_commande,"etat"),"bon_de_commande update assert 2");
	}
	
	
	/** @test Test de getProviderUrl bon_de_commande. */
	public function test_getProviderUrl(){
		$fournisseur = $this->obj->select($this->_bon_de_commande,"id_fournisseur");
		$this->assertEquals("1589",$fournisseur,"bon_de_commande getProviderUrl assert 1");
		//test de la methode getProviderUrl : cas par default
		$url = $this->obj->getProviderUrl($this->_bon_de_commande);
		//mise à jours du fournisseur
		$this->obj->update(array("id_fournisseur"=>"636","id_bon_de_commande"=>$this->_bon_de_commande),$this->s);
		$this->requete_valide("bon_de_commande");
		$this->assertEquals("636",$this->obj->select($this->_bon_de_commande,"id_fournisseur"),"bon_de_commande getProviderUrl assert 2"); 
		//test de la methode getProviderUrl: cas techdata
		$url = $this->obj->getProviderUrl($this->_bon_de_commande);
		//verification de l'URL
		$this->assertEquals("http://www.techdata.fr",$url,"bon_de_commande getProviderUrl assert 3"); 
		
		
		$this->obj->update(array("id_fournisseur"=>"1367","id_bon_de_commande"=>$this->_bon_de_commande),$this->s);
		$this->assertEquals("1367",$this->obj->select($this->_bon_de_commande,"id_fournisseur"),"bon_de_commande getProviderUrl assert 2"); 
		$this->assertEquals("http://www.etc-dist.fr",$this->obj->getProviderUrl($this->_bon_de_commande),"bon_de_commande getProviderUrl ETC"); 
	}


	/* @test Test de setCompleted bon_de_commande,redirection vers affaire */
	public function test_setCompleted_affaire_update(){
		$cadre_refreshed=array();
		$bdc= $this->obj->select($this->_bon_de_commande);
		//réinitialisation du querier
		ATF::stock()->q->reset();
		$this->_stock=ATF::stock()->select_all();
		//id_bon_de_commande erroné
		try {
			$result = $this->obj->setCompleted(NULL,$this->s,NULL,$cadre_refreshed);
		} catch (error $e) {
			//ATF::db()->rollback_transaction();
			$erreur_capter = $e->getCode();
		}
		$this->assertEquals(12,$erreur_capter,"ERREUR NON ATTRAPPEE (id_stock erroné)");
	}
	
	
	public function test_setCompleted_affaire_update_2(){
		$cadre_refreshed=array();
		$bdc= $this->obj->select($this->_bon_de_commande);
		//réinitialisation du querier
		ATF::stock()->q->reset();
		$this->_stock=ATF::stock()->select_all();
		//renseigner les serial avec la methode UPDATE 
		ATF::stock()->update(array("id_stock"=>$this->_stock[0]["stock.id_stock"],"serial"=>"BAZAZ1"),$this->s,NULL,$cadre_refreshed);
		$this->requete_valide("stock"); 
		ATF::stock()->update(array("id_stock"=>$this->_stock[1]["stock.id_stock"],"serial"=>"BAZAZ2"),$this->s,NULL,$cadre_refreshed);
		$this->requete_valide("stock"); 
		ATF::stock()->update(array("id_stock"=>$this->_stock[2]["stock.id_stock"],"serial"=>"BAZAZ3"),$this->s,NULL,$cadre_refreshed);
		$this->requete_valide("stock"); 
		ATF::stock()->update(array("id_stock"=>$this->_stock[3]["stock.id_stock"],"serial"=>"BAZAZ4"),$this->s,NULL,$cadre_refreshed);
		$this->requete_valide("stock"); 
		ATF::stock()->update(array("id_stock"=>$this->_stock[4]["stock.id_stock"],"serial"=>"BAZAZ5"),$this->s,NULL,$cadre_refreshed);
		$this->requete_valide("stock"); 
		
		//test methode
		$this->obj->setCompleted($bdc,$this->s,NULL,$cadre_refreshed);
		$this->requete_valide("bon_de_commande");
	
	
	}
	
	

	/* @test Test de default_value bon_de_commande. */
	public function test_Default_value(){
		// BON DE COMMANDE
				//ATF::_r('id_bon_de_commande',$this->_bon_de_commande);
		//ATF::_r('id_bon_de_commande',$this->_bon_de_commande);
		//print_r($this->_bon_de_commande);
		//test default_value societe
		$id_societe=$this->obj->default_value("id_societe");
		$this->assertEquals($this->_bon_de_commande["id_societe"],$id_societe,"default_value ne renvoie pas le bon 'id_societe'");
		//test default_value resume
		$resume=$this->obj->default_value("resume");
		$this->assertEquals($this->_bon_de_commande["resume"],$resume,"default_value ne renvoie pas le bon 'resume'");
		//test default_value prix 
		$prix=$this->obj->default_value("prix");
		$this->assertEquals($this->_bon_de_commande["prix"],$prix,"default_value ne renvoie pas le bon 'prix'");
		//test default_value frais_de_port
		$frais_de_port=$this->obj->default_value("frais_de_port");
		$this->assertEquals($this->_bon_de_commande["frais_de_port"],$frais_de_port,"default_value ne renvoie pas le bon 'frais_de_port'");
		//test default_value prix achat
		$prix_achat=$this->obj->default_value("prix_achat");
		$this->assertEquals($this->_bon_de_commande["prix_achat"],$prix_achat,"default_value ne renvoie pas le bon 'prix_achat'");
		//test default_value 
		$sous_total=$this->obj->default_value("sous_total");
		$this->assertEquals(($this->_bon_de_commande["prix"]-$this->_bon_de_commande["frais_de_port"]),$sous_total,"default_value ne renvoie pas le bon 'sous_total'");
		//test default_value marge
		$marge=$this->obj->default_value("marge");
		$this->assertEquals((round((($this->_bon_de_commande["prix"]-$this->_bon_de_commande["prix_achat"])/$this->_bon_de_commande["prix"])*100,2)."%"),$marge,"default_value ne renvoie pas le bon 'sous_total'");
	
		$marge_absolue=$this->obj->default_value("marge_absolue");
		$this->assertEquals(($this->_bon_de_commande["prix"]-$this->_bon_de_commande["frais_de_port"]-$this->_bon_de_commande["prix_achat"]),$marge_absolue,"default_value ne renvoie pas le bon 'marge_absolue'");

		$default_value=$this->obj->default_value("default_value");
		$this->assertNull($default_value,"default_value ne renvoie pas une bonne valeur pour le field default_value");		
	}
	
	/* @test Test de default_value devis. */
	public function test_Default_value_commande(){
		//     COMMANDE
		$com=$this->obj->decryptID($this->id_commande);
		$la_commande = ATF::commande()->select($com);
		ATF::_r('id_commande',$com);
		//test frais_de_port
		$frais_de_port=$this->obj->default_value("frais_de_port");
		$this->assertEquals($la_commande["frais_de_port"],$frais_de_port,"default_value ne renvoie pas le bon 'frais_de_port'");	
	}
	
	/* @test Test de default_value devis. */
	public function test_Default_value_devis(){
		//    DEVIS
		$devis=$this->obj->decryptID($this->_devis);
		$le_devis = ATF::devis()->select($devis);
		ATF::_r('id_devis',$devis);
		//test default_value societe
		$id_societe=$this->obj->default_value("id_societe");
		$this->assertEquals($le_devis["id_societe"],$id_societe,"default_value ne renvoie pas le bon 'id_societe'");	
	}	

	/** @test Test de autocompleteConditions bon_de_commande. */
	public function test_autocompleteConditions(){
		$com=$this->obj->decryptID($this->id_commande);
		$this->assertEquals(
			array("condition_field"=>array("commande_ligne.id_commande","societe.tika")
			      ,"condition_value"=>array($com,"TRUCT TRAKHT MIKHER")),
			$this->obj->autocompleteConditions(ATF::societe(),array("bon_de_commande"=>array("id_commande"=>$com)),"tika","TRUCT TRAKHT MIKHER","id_fournisseur"),
			"Le couple condition_field/value est incorrect"
		);
	}
	
	
	/** @test Test de autocompleteConditions bon_de_commande, sans id_commande */
	public function test_autocompleteConditions_sans_id_commande(){
		$com=$this->obj->decryptID($this->_commande);
		$this->assertEquals(
			array("condition_field"=>array("societe.tika")
			      ,"condition_value"=>array("TRUCT TRAKHT MIKHER")),
			$this->obj->autocompleteConditions(ATF::societe(),array("bon_de_commande"=>array("id_commande"=>"")),"tika","TRUCT TRAKHT MIKHER","id_fournisseur"),
			"Le couple condition_field/value est incorrect"
		);
	}


	/** @test Test de delete bon_de_commande. */
	public function test_delete(){
		$cadre_refreshed=array();
		//nombre de ligne dans stock
		$this->assertEquals(1,count($this->_bon_de_commande),"bon_de_commande delete assert 1");
		//Nombre de stocks avant suppression de commande
		$this->assertEquals(15,ATF::stock()->count(),"bon_de_commande delete assert 2");		
		//Sauvegarde du querier
		$q_save=clone $this->obj->q;
		//Suppression
		//@todo Rajouter le cadre_refresh après avoir corriger le bug du select_all_optimized
		ATF::$cr->reset();		
		$last_id = $this->obj->delete(array("id"=>array($this->_bon_de_commande)),$this->s,NULL,$cr=array());
		$this->requete_valide("bon_de_commande");
		//ligne est supprimé	
		$this->assertTrue($last_id,"bon_de_commande delete assert 2");
		//utiliser le quriere Sauvegarder
		$this->obj->q=$q_save;
		//mise à jours de l'instance _stock
		$this->_bon_de_commande = $this->obj->select_all();
		ATF::stock()->q->reset();
		$this->_stock = ATF::stock()->select_all();
		//nombrede lignes dans stock apres le delete de 2 stock
		$this->assertEquals(0,count($this->_bon_de_commande),"bon_de_commande delete assert 3");
		//nb ligne stock de la commande
		$this->assertEquals(0,count($this->_stock),"bon_de_commande delete assert 4");
	}
}
?>