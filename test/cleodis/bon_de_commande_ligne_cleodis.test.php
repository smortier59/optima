<?
class bon_de_commande_ligne_cleodis_test extends ATF_PHPUnit_Framework_TestCase {
	
	/** Méthode pré-test, exécute avant chaque test unitaire
	* besoin d'un user pour les traduction
	*/
	public function setUp() {
		$this->initUser();
//		echo "(".ATF::db()->numberTransaction()."|";
	}
	
	/** Méthode post-test, exécute après chaque test unitaire*/
	public function tearDown(){
//		echo ATF::db()->numberTransaction().")";
		ATF::db()->rollback_transaction(true);
	}

	public function insertBdc(){

		$this->id_societe=1397;
		
		$this->devis["devis"]=array(
								 "id_societe" => $this->id_societe
								,"id_filiale" => 246
								,"date" => date("Y-m-d")
								,"id_contact" => 1401
								,"devis" => "Tu Bdc"
								,"type_contrat" => "lld"
								,"validite" => date("Y-m-d",strtotime(date("Y-m-d")."- 15 day"))
								,"id_opportunite" =>NULL
								,"tva" => "1.196"
								,"prix" => "14 000.00"
								,"prix_achat" => "4 641.00"
								,"marge" => "66.85"
								,"marge_absolue" => "9 359.00"
        );
		
		$this->devis["values_devis"] = array(
             "loyer" => '[{"loyer__dot__loyer":"1000","loyer__dot__duree":"14","loyer__dot__assurance":"","loyer__dot__frais_de_gestion":"","loyer__dot__frequence_loyer":"mois","loyer__dot__loyer_total":14000}]'
            ,"produits" => '[{"devis_ligne__dot__produit":"Optiplex GX520 TFT 19","devis_ligne__dot__quantite":"1","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"DEL-WRK-OPTGX520-19","devis_ligne__dot__prix_achat":"10","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"DELL|#ref=164a1c62808dc1a3af6f7d99051db73b","devis_ligne__dot__visibilite_prix":"visible","devis_ligne__dot__id_produit_fk":"9","devis_ligne__dot__id_fournisseur_fk":"1351"},{"devis_ligne__dot__produit":"XSERIES 226","devis_ligne__dot__quantite":"1","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"O2-SRV-226-001","devis_ligne__dot__prix_achat":"3113.00","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"AUDIOPTIC TRADE SERVICES|#ref=c0529cb381c6dcf43fc554b910ce02e9","devis_ligne__dot__visibilite_prix":"visible","devis_ligne__dot__id_produit_fk":"5","devis_ligne__dot__id_fournisseur_fk":"1358"},{"devis_ligne__dot__produit":"Optiplex GX520 TFT 17","devis_ligne__dot__quantite":"2","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"DEL-WRK-OPTGX520-17","devis_ligne__dot__prix_achat":"759.00","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"DELL|#ref=164a1c62808dc1a3af6f7d99051db73b","devis_ligne__dot__visibilite_prix":"visible","devis_ligne__dot__id_produit_fk":"8","devis_ligne__dot__id_fournisseur_fk":"1351"}]'
        );
		
		$this->id_devis=classes::decryptId(ATF::devis()->insert($this->devis));
		
		$this->devis_select=ATF::devis()->select($this->id_devis);
		ATF::devis_ligne()->q->reset()->addCondition("id_devis",$this->id_devis);
		$this->devis_ligne=ATF::devis_ligne()->sa();
		
		$this->commande["commande"]=array(
								 "commande" => $this->devis_select["devis"]
								,"type" => "prelevement"
								,"id_societe" => $this->id_societe
								,"date" => date("Y-m-d")
								,"id_affaire" => $this->devis_select["id_affaire"]
								,"clause_logicielle" => "non"
								,"prix" => "14 000.00"
								,"prix_achat" =>"4 641.00"
								,"marge" => "66.85"
								,"marge_absolue" => "9 359.00"
								,"id_devis" => $this->devis_select["id_devis"]
        );
		
		$this->commande["values_commande"] = array(
			 "loyer" => '[{"loyer__dot__loyer":"1000.00","loyer__dot__duree":"14","loyer__dot__assurance":null,"loyer__dot__frais_de_gestion":null,"loyer__dot__frequence_loyer":"mois","loyer__dot__loyer_total":14000}]'
			,"produits" => '[{"commande_ligne__dot__produit":"Optiplex GX520 TFT 19","commande_ligne__dot__quantite":"1","commande_ligne__dot__ref":"DEL-WRK-OPTGX520-19","commande_ligne__dot__id_fournisseur":"DELL","commande_ligne__dot__id_fournisseur_fk":"1351","commande_ligne__dot__prix_achat":"10.00","commande_ligne__dot__id_produit":"Optiplex GX520 TFT 19","commande_ligne__dot__id_produit_fk":"9","commande_ligne__dot__id_commande_ligne":"'.$devis_ligne[0]["id_devis_ligne"].'"},{"commande_ligne__dot__produit":"XSERIES 226","commande_ligne__dot__quantite":"1","commande_ligne__dot__ref":"O2-SRV-226-001","commande_ligne__dot__id_fournisseur":"AUDIOPTIC TRADE SERVICES","commande_ligne__dot__id_fournisseur_fk":"1358","commande_ligne__dot__prix_achat":"3113.00","commande_ligne__dot__id_produit":"XSERIES 226","commande_ligne__dot__id_produit_fk":"5","commande_ligne__dot__id_commande_ligne":"'.$devis_ligne[1]["id_devis_ligne"].'"},{"commande_ligne__dot__produit":"Optiplex GX520 TFT 17","commande_ligne__dot__quantite":"2","commande_ligne__dot__ref":"DEL-WRK-OPTGX520-17","commande_ligne__dot__id_fournisseur":"DELL","commande_ligne__dot__id_fournisseur_fk":"1351","commande_ligne__dot__prix_achat":"759.00","commande_ligne__dot__id_produit":"Optiplex GX520 TFT 17","commande_ligne__dot__id_produit_fk":"8","commande_ligne__dot__id_commande_ligne":"'.$devis_ligne[2]["id_devis_ligne"].'"}]'
		);
		
		$this->id_commande=classes::decryptId(ATF::commande()->insert($this->commande));
		ATF::$msg->getNotices();
		$this->commande_select=ATF::commande()->select($this->id_commande);
		ATF::commande_ligne()->q->reset()->addCondition("id_commande",$this->id_commande);
		$this->commande_ligne=ATF::commande_ligne()->sa();
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
	public function test_autocompleteConditions(){
		$this->insertBdc();
		
		$bon_de_commande["bon_de_commande"]=array(
								 "id_societe" => $this->id_societe
								,"id_commande" => $this->id_commande
								,"id_fournisseur" => 1351
								,"id_affaire" => $this->devis_select["id_affaire"]
								,"bon_de_commande" => $this->devis_select["devis"]
								,"id_contact" => 5333
								,"prix" => "10.00"
								,"tva" =>"1.196"
								,"etat" => "envoyee"
								,"payee" => "non"
								,"date" => date("Y-m-d")
								,"destinataire" => "AXXES"
								,"adresse" => "26 rue de La Vilette - Part Dieu"
								,"adresse_2" => $this->devis_select["id_devis"]
								,"adresse_3" => $this->devis_select["id_devis"]
								,"cp" => "69003"
								,"ville" => "LYON"
								,"id_pays" => "FR"
								,"id_fournisseur_intermediaire" => NULL
								,"livraison_destinataire" => NULL
								,"livraison_adresse" => NULL
								,"livraison_cp" => NULL
								,"livraison_ville" => NULL
								,"email" => "debug@absystech.fr"
								,"emailTexte" => "TU<br>"
								,"emailCopie" => "debug@absystech.fr"
								,"filestoattach" =>  array(
										"fichier_joint" =>NULL
									)
        );
		
		$bon_de_commande["commandes"]="xnode-".$this->id_commande.",".$this->commande_ligne[0]["id_commande_ligne"]."";

		$refresh = array();
		$id_bon_de_commande=classes::decryptId(ATF::bon_de_commande()->insert($bon_de_commande,$this->s,NULL,$refresh));
		ATF::bon_de_commande_ligne()->q->reset()->addCondition("id_bon_de_commande",$id_bon_de_commande);
		$bon_de_commande_ligne=ATF::bon_de_commande_ligne()->sa();

		ATF::bon_de_commande_ligne()->q->reset()->addCondition("id_bon_de_commande",0);
		$this->assertFalse(ATF::bon_de_commande_ligne()->toFacture_fournisseurLigne(),"toFacture_fournisseurLigne ne devrait rien renvoyé");
		
		ATF::bon_de_commande_ligne()->q->reset()->addCondition("id_bon_de_commande",$id_bon_de_commande);
		$toFacture_fournisseurLigne=ATF::bon_de_commande_ligne()->toFacture_fournisseurLigne();
		$this->assertEquals(array(
									"data"=>array(
												0=>array(
															"facture_fournisseur_ligne.produit"=>"Optiplex GX520 TFT 19",
															"facture_fournisseur_ligne.quantite"=>"1",
															"facture_fournisseur_ligne.ref"=>"DEL-WRK-OPTGX520-19",
															"facture_fournisseur_ligne.prix"=>"10.00",
															"facture_fournisseur_ligne.id_facture_fournisseur_ligne"=>$bon_de_commande_ligne[0]["id_bon_de_commande_ligne"],
															"facture_fournisseur_ligne.serial"=>NULL
														)
											),
									"count"=>1
								)
							,$toFacture_fournisseurLigne,'toFacture_fournisseurLigne ne renvoie pas les bonnes infos sur le 1er BDC');

		//2er BDC
		$bon_de_commande["bon_de_commande"]=array(
								 "id_societe" => $this->id_societe
								,"id_commande" => $this->id_commande
								,"id_fournisseur" => 1358
								,"id_affaire" => $this->devis_select["id_affaire"]
								,"bon_de_commande" => $this->devis_select["devis"]
								,"id_contact" => 4365
								,"prix" => " 3 113.00"
								,"tva" =>"1.196"
								,"etat" => "envoyee"
								,"payee" => "non"
								,"date" => date("Y-m-d")
								,"destinataire" => "AXXES"
								,"adresse" => "26 rue de La Vilette - Part Dieu"
								,"adresse_2" => $this->devis_select["id_devis"]
								,"adresse_3" => $this->devis_select["id_devis"]
								,"cp" => "69003"
								,"ville" => "LYON"
								,"id_pays" => "FR"
								,"id_fournisseur_intermediaire" => NULL
								,"livraison_destinataire" => NULL
								,"livraison_adresse" => NULL
								,"livraison_cp" => NULL
								,"livraison_ville" => NULL
								,"email" => "debug@absystech.fr"
								,"emailTexte" => "TU<br>"
								,"emailCopie" => "debug@absystech.fr"
								,"filestoattach" =>  array(
										"fichier_joint" =>NULL
									)
        );
		
		$bon_de_commande["commandes"]="xnode-".$this->id_commande.",".$this->commande_ligne[2]["id_commande_ligne"]."";

		$refresh = array();
		$id_bon_de_commande2=classes::decryptId(ATF::bon_de_commande()->insert($bon_de_commande,$this->s,NULL,$refresh));
		ATF::bon_de_commande_ligne()->q->reset()->addCondition("id_bon_de_commande",$id_bon_de_commande2);
		$bon_de_commande_ligne2=ATF::bon_de_commande_ligne()->sa();
		ATF::bon_de_commande_ligne()->q->reset()->addCondition("id_bon_de_commande",$id_bon_de_commande2);
		$toFacture_fournisseurLigne2=ATF::bon_de_commande_ligne()->toFacture_fournisseurLigne();
		$this->assertEquals(
							array(
								"data"=>array(
										0=>array(
											"facture_fournisseur_ligne.produit"=>"Optiplex GX520 TFT 17","facture_fournisseur_ligne.quantite"=>"1","facture_fournisseur_ligne.ref"=>"DEL-WRK-OPTGX520-17","facture_fournisseur_ligne.prix"=>"759.00","facture_fournisseur_ligne.id_facture_fournisseur_ligne"=>$bon_de_commande_ligne2[0]["id_bon_de_commande_ligne"],"facture_fournisseur_ligne.serial"=>NULL
											),
										1=>array(
											"facture_fournisseur_ligne.produit"=>"Optiplex GX520 TFT 17","facture_fournisseur_ligne.quantite"=>"1","facture_fournisseur_ligne.ref"=>"DEL-WRK-OPTGX520-17","facture_fournisseur_ligne.prix"=>"759.00","facture_fournisseur_ligne.id_facture_fournisseur_ligne"=>$bon_de_commande_ligne2[0]["id_bon_de_commande_ligne"],"facture_fournisseur_ligne.serial"=>NULL
											)
										)
								,"count"=>2
							),
							$toFacture_fournisseurLigne2,
							'toFacture_fournisseurLigne ne renvoie pas les bonnes infos sur le 2ème BDC'
							);
							
	
		ATF::commande_ligne()->u(array("id_commande_ligne"=>$bon_de_commande_ligne2[0]["id_commande_ligne"],"serial"=>"33 44"));

		ATF::bon_de_commande_ligne()->q->reset()->addCondition("id_bon_de_commande",$id_bon_de_commande2);
		$toFacture_fournisseurLigne2=ATF::bon_de_commande_ligne()->toFacture_fournisseurLigne();

		$this->assertEquals(
							array(
								"data"=>array(
										0=>array(
											"facture_fournisseur_ligne.produit"=>"Optiplex GX520 TFT 17","facture_fournisseur_ligne.quantite"=>"1","facture_fournisseur_ligne.ref"=>"DEL-WRK-OPTGX520-17","facture_fournisseur_ligne.prix"=>"759.00","facture_fournisseur_ligne.id_facture_fournisseur_ligne"=>$bon_de_commande_ligne2[0]["id_bon_de_commande_ligne"],"facture_fournisseur_ligne.serial"=>33
											),
										1=>array(
											"facture_fournisseur_ligne.produit"=>"Optiplex GX520 TFT 17","facture_fournisseur_ligne.quantite"=>"1","facture_fournisseur_ligne.ref"=>"DEL-WRK-OPTGX520-17","facture_fournisseur_ligne.prix"=>"759.00","facture_fournisseur_ligne.id_facture_fournisseur_ligne"=>$bon_de_commande_ligne2[0]["id_bon_de_commande_ligne"],"facture_fournisseur_ligne.serial"=>44
											)
										)
								,"count"=>2
							),
							$toFacture_fournisseurLigne2,
							'Can update ne doit pas se faire si etat gagne'
							);
							
		
	}


	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
	public function test_toParcInsert(){
		$this->insertBdc();
		
		$bon_de_commande["bon_de_commande"]=array(
								 "id_societe" => $this->id_societe
								,"id_commande" => $this->id_commande
								,"id_fournisseur" => 1351
								,"id_affaire" => $this->devis_select["id_affaire"]
								,"bon_de_commande" => $this->devis_select["devis"]
								,"id_contact" => 5333
								,"prix" => "10.00"
								,"tva" =>"1.196"
								,"etat" => "envoyee"
								,"payee" => "non"
								,"date" => date("Y-m-d")
								,"destinataire" => "AXXES"
								,"adresse" => "26 rue de La Vilette - Part Dieu"
								,"adresse_2" => $this->devis_select["id_devis"]
								,"adresse_3" => $this->devis_select["id_devis"]
								,"cp" => "69003"
								,"ville" => "LYON"
								,"id_pays" => "FR"
								,"id_fournisseur_intermediaire" => NULL
								,"livraison_destinataire" => NULL
								,"livraison_adresse" => NULL
								,"livraison_cp" => NULL
								,"livraison_ville" => NULL
								,"email" => "debug@absystech.fr"
								,"emailTexte" => "TU<br>"
								,"emailCopie" => "debug@absystech.fr"
								,"filestoattach" =>  array(
										"fichier_joint" =>NULL
									)
        );
		
		$bon_de_commande["commandes"]="xnode-".$this->id_commande.",".$this->commande_ligne[0]["id_commande_ligne"]."";

		$refresh = array();
		$id_bon_de_commande=classes::decryptId(ATF::bon_de_commande()->insert($bon_de_commande,$this->s,NULL,$refresh));
		ATF::bon_de_commande_ligne()->q->reset()->addCondition("id_bon_de_commande",$id_bon_de_commande);
		$bon_de_commande_ligne=ATF::bon_de_commande_ligne()->sa();
		
		ATF::bon_de_commande_ligne()->q->reset()->addCondition("id_bon_de_commande",$id_bon_de_commande);
		$toParcInsert=ATF::bon_de_commande_ligne()->toParcInsert();

		$this->assertEquals(
							array(
								"data"=>array(
										0=>array(
											"parc.produit"=>"Optiplex GX520 TFT 19","parc.quantite"=>"1","parc.ref"=>"DEL-WRK-OPTGX520-19","parc.prix"=>"10.00","parc.id_parc"=>$bon_de_commande_ligne[0]["id_bon_de_commande_ligne"],"parc.serial"=>NULL 
											)
										)
								,"count"=>1
							),
							$toParcInsert,
							'toParcInsert ne renvoi pas le bon parc'
							);

		//2er BDC
		$bon_de_commande["bon_de_commande"]=array(
								 "id_societe" => $this->id_societe
								,"id_commande" => $this->id_commande
								,"id_fournisseur" => 1358
								,"id_affaire" => $this->devis_select["id_affaire"]
								,"bon_de_commande" => $this->devis_select["devis"]
								,"id_contact" => 4365
								,"prix" => " 3 113.00"
								,"tva" =>"1.196"
								,"etat" => "envoyee"
								,"payee" => "non"
								,"date" => date("Y-m-d")
								,"destinataire" => "AXXES"
								,"adresse" => "26 rue de La Vilette - Part Dieu"
								,"adresse_2" => $this->devis_select["id_devis"]
								,"adresse_3" => $this->devis_select["id_devis"]
								,"cp" => "69003"
								,"ville" => "LYON"
								,"id_pays" => "FR"
								,"id_fournisseur_intermediaire" => NULL
								,"livraison_destinataire" => NULL
								,"livraison_adresse" => NULL
								,"livraison_cp" => NULL
								,"livraison_ville" => NULL
								,"email" => "debug@absystech.fr"
								,"emailTexte" => "TU<br>"
								,"emailCopie" => "debug@absystech.fr"
								,"filestoattach" =>  array(
										"fichier_joint" =>NULL
									)
        );
		
		$bon_de_commande["commandes"]="xnode-".$this->id_commande.",".$this->commande_ligne[2]["id_commande_ligne"]."";

		$refresh = array();
		$id_bon_de_commande2=classes::decryptId(ATF::bon_de_commande()->insert($bon_de_commande,$this->s,NULL,$refresh));
		ATF::bon_de_commande_ligne()->q->reset()->addCondition("id_bon_de_commande",$id_bon_de_commande2);
		$bon_de_commande_ligne2=ATF::bon_de_commande_ligne()->sa();
		ATF::bon_de_commande_ligne()->q->reset()->addCondition("id_bon_de_commande",$id_bon_de_commande2);
		$toParcInsert=ATF::bon_de_commande_ligne()->toParcInsert();

		$this->assertEquals(
							array(
								"data"=>array(
										0=>array(
											"parc.produit"=>"Optiplex GX520 TFT 17","parc.quantite"=>"1","parc.ref"=>"DEL-WRK-OPTGX520-17","parc.prix"=>"759.00","parc.id_parc"=>$bon_de_commande_ligne2[0]["id_bon_de_commande_ligne"],"parc.serial"=>NULL
											),
										1=>array(
											"parc.produit"=>"Optiplex GX520 TFT 17","parc.quantite"=>"1","parc.ref"=>"DEL-WRK-OPTGX520-17","parc.prix"=>"759.00","parc.id_parc"=>$bon_de_commande_ligne2[0]["id_bon_de_commande_ligne"],"parc.serial"=>NULL
											)
										)								
								,"count"=>2
							),
							$toParcInsert,
							'toParcInsert ne renvoi pas le bon parc quantite 2'
							);
							
	
		ATF::commande_ligne()->u(array("id_commande_ligne"=>$bon_de_commande_ligne2[0]["id_commande_ligne"],"serial"=>"33"));

		ATF::bon_de_commande_ligne()->q->reset()->addCondition("id_bon_de_commande",$id_bon_de_commande2);
		$toParcInsert=ATF::bon_de_commande_ligne()->toParcInsert();
		ATF::bon_de_commande_ligne()->q->reset()->addCondition("id_bon_de_commande",$id_bon_de_commande2);
		$bon_de_commande_ligne2=ATF::bon_de_commande_ligne()->sa();

		$this->assertEquals(
							array(
								"data"=>array(
										0=>array(
											"parc.produit"=>"Optiplex GX520 TFT 17","parc.quantite"=>"1","parc.ref"=>"DEL-WRK-OPTGX520-17","parc.prix"=>"759.00","parc.id_parc"=>$bon_de_commande_ligne2[0]["id_bon_de_commande_ligne"],"parc.serial"=>NULL
											)
										)								
								,"count"=>1
							),
							$toParcInsert,
							'toParcInsert ne renvoi pas le bon parc quantite avec un serial'
							);

		ATF::commande_ligne()->u(array("id_commande_ligne"=>$bon_de_commande_ligne2[0]["id_commande_ligne"],"serial"=>"33 44"));

		ATF::bon_de_commande_ligne()->q->reset()->addCondition("id_bon_de_commande",$id_bon_de_commande2);
		$toParcInsert=ATF::bon_de_commande_ligne()->toParcInsert();
		$this->assertEquals(
							array(
								"count"=>0
							),
							$toParcInsert,
							'toParcInsert ne renvoi pas le bon parc quantite avec tous les serial'
							);
	}

	
	/* @author Nicolas BERTEMONT <nbertemont@absystech.fr> */
	public function test_bdclMidas(){
		$cm=new bon_de_commande_ligne_midas();
		$this->assertEquals('a:5:{s:40:"bon_de_commande_ligne.id_bon_de_commande";a:4:{s:4:"type";s:3:"int";s:5:"xtype";s:11:"numberfield";s:9:"maxlength";s:1:"8";s:7:"default";N;}s:25:"bon_de_commande_ligne.ref";a:5:{s:4:"type";s:4:"text";s:5:"xtype";s:9:"textfield";s:9:"maxlength";s:2:"32";s:7:"default";N;s:4:"null";b:1;}s:29:"bon_de_commande_ligne.produit";a:4:{s:4:"type";s:4:"text";s:5:"xtype";s:9:"textfield";s:9:"maxlength";s:3:"500";s:7:"default";N;}s:30:"bon_de_commande_ligne.quantite";a:4:{s:4:"type";s:3:"int";s:5:"xtype";s:11:"numberfield";s:9:"maxlength";s:2:"10";s:7:"default";N;}s:26:"bon_de_commande_ligne.prix";a:5:{s:4:"type";s:7:"decimal";s:5:"xtype";s:11:"numberfield";s:7:"default";N;s:4:"null";b:1;s:8:"renderer";s:5:"money";}}',serialize($cm->colonnes['fields_column']),"Le constructeur de la classe midas a changé");
	}
};
?>