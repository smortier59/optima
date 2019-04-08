<?
/**
* Classe de test sur le module societe_absystech
*/
class societe_absystech_test extends ATF_PHPUnit_Framework_TestCase {
	/** Méthode pré-test, exécute avant chaque test unitaire
	* besoin d'un user pour les traduction
	*/
	public function setUp() {
		$this->initUser();
	}
	
	/** Méthode post-test, exécute après chaque test unitaire*/
	
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}


//	* @author Mathieu Tribouillard <mtribouillard@absystech.fr>
	public function testMeteo_societe(){
		ob_start();
		$this->obj->meteo_societe($this->id_societe);
		ob_end_clean();
		$societe=$this->obj->select($this->id_societe);
		$this->assertNull($societe["meteo"],'1 La Meteo_societe ne doit pas renvoyer de meteo');

		$meteo=$this->obj->meteo($this->id_societe);
		$this->assertEquals("Fog",$meteo["icone"],'La météo doit renvoyer un fog quand il n y a pas de données');
		$this->assertEquals("Pas de données",$meteo["echelle"],'La météo ne doit pas renvoyer d echelles quand il n y a pas de données');
		$this->assertNull($meteo["solde_total"],'La météo ne doit pas renvoyer de solde_total');

		//Contact
		$contact["nom"]="Tu_devis";
		$this->id_contact=ATF::contact()->insert($contact);

		//Devis
		$this->devis["devis"]["id_contact"]=$this->id_contact;
		$this->devis["devis"]['resume']='Tu_devis';
		$this->devis["devis"]['id_societe']=$this->id_societe;
		$this->devis["devis"]['validite']=date('Y-m-d');
		$this->devis["devis"]['prix']="200";
		$this->devis["devis"]['frais_de_port']="50";
		$this->devis["devis"]['prix_achat']="50";
		$this->devis["devis"]["date"]=date('Y-m-d');
		
		//Devis_ligne
		$this->devis["values_devis"]=array("produits"=>'[{"devis_ligne__dot__ref":"TU","devis_ligne__dot__produit":"Tu_devis","devis_ligne__dot__quantite":"15","devis_ligne__dot__poids":"10","devis_ligne__dot__prix":"10","devis_ligne__dot__prix_achat":"10","devis_ligne__dot__id_fournisseur":"1","devis_ligne__dot__id_compte_absystech":"1","devis_ligne__dot__marge":97.14,"devis_ligne__dot__id_fournisseur_fk":"1"}]');

		//Insertion
		$this->id_devis = ATF::devis()->insert($this->devis,$this->s);
		$this->id_affaire = ATF::devis()->select($this->id_devis,"id_affaire");
	
		//Commande
		$this->commande["commande"]=$this->devis["devis"];
		$this->commande["commande"]["id_affaire"]=$this->id_affaire;
		$this->commande["commande"]["id_devis"]=$this->id_devis;
		$this->commande["commande"]["date"]="2010-01-01";
		
		//Commande_ligne
		$this->commande["values_commande"]=array("produits"=>'[{"commande_ligne__dot__ref":"TU","commande_ligne__dot__produit":"Tu_commande","commande_ligne__dot__quantite":"15","commande_ligne__dot__prix":"10","commande_ligne__dot__prix_achat":"10","commande_ligne__dot__id_fournisseur":"1","commande_ligne__dot__id_compte_absystech":"1","commande_ligne__dot__marge":97.14,"commande_ligne__dot__id_fournisseur_fk":"1"}]');

		//Insertion
		unset($this->commande["commande"]["id_contact"],$this->commande["commande"]["validite"]);
		$this->id_commande = ATF::commande()->insert($this->commande,$this->s);

		//Facture
		$this->facture["facture"]=$this->commande["commande"];
		$this->facture["facture"]["date"]=date('Y-m-d');
		$this->facture["facture"]["id_affaire"]=$this->id_affaire;
		$this->facture["facture"]["mode"]="facture";
		$this->facture["facture"]["date"]="2010-01-01";
		$this->facture["facture"]["date_effective"]="2011-01-01";
		$this->facture["facture"]["id_termes"]=2;
		$this->facture["facture"]["tva"]=1.2;
		
		//Facture_ligne
		$this->facture["values_facture"]=array("produits"=>'[{"facture_ligne__dot__ref":"TU","facture_ligne__dot__produit":"Tu_facture","facture_ligne__dot__quantite":"15","facture_ligne__dot__prix":"10","facture_ligne__dot__prix_achat":"10","facture_ligne__dot__id_fournisseur":"1","facture_ligne__dot__serial":"777","facture_ligne__dot__id_compte_absystech":"1","facture_ligne__dot__marge":97.14,"facture_ligne__dot__id_fournisseur_fk":"1"}]');

		//Devis
		$this->devisPerdu["devis"]["id_contact"]=$this->id_contact;
		$this->devisPerdu["devis"]['resume']='Tu_devis';
		$this->devisPerdu["devis"]['id_societe']=$this->id_societe;
		$this->devisPerdu["devis"]['validite']=date('Y-m-d');
		$this->devisPerdu["devis"]['prix']="200";
		$this->devisPerdu["devis"]['frais_de_port']="50";
		$this->devisPerdu["devis"]['prix_achat']="50";
		$this->devisPerdu["devis"]["date"]=date('Y-m-d');
		
		//Devis_ligne
		$this->devisPerdu["values_devis"]=array("produits"=>'[{"devis_ligne__dot__ref":"TU","devis_ligne__dot__produit":"Tu_devis","devis_ligne__dot__quantite":"15","devis_ligne__dot__poids":"10","devis_ligne__dot__prix":"10","devis_ligne__dot__prix_achat":"10","devis_ligne__dot__id_fournisseur":"1","devis_ligne__dot__id_compte_absystech":"1","devis_ligne__dot__marge":97.14,"devis_ligne__dot__id_fournisseur_fk":"1"}]');

		//Insertion
		$this->id_devisPerdu = ATF::devis()->insert($this->devisPerdu,$this->s);
		$this->id_affairePerdu = ATF::devis()->select($this->id_devisPerdu,"id_affaire");

		ATF::affaire()->u(array("id_affaire"=>$this->id_affairePerdu,"etat"=>"perdue"));

		//Insertion
		unset($this->facture["facture"]["resume"],$this->facture["facture"]["prix_achat"],$this->facture["facture"]["id_devis"]);
		$this->id_facture = ATF::facture()->insert($this->facture,$this->s);

		ob_start();
		$this->obj->meteo_societe($this->id_societe);
		ob_end_clean();
		$societe=$this->obj->select($this->id_societe);
		$this->assertEquals("365.0000",$societe["meteo"],'2 La Meteo_societe ne doit pas renvoyer de meteo');
	}


//	* @author Mathieu Tribouillard <mtribouillard@absystech.fr>
	public function testMeteo_icone(){
		$return=$this->obj->meteo_icone($societe["meteo"]);
		$this->assertEquals("1",$return["echelle"],'Meteo ne renvoi pas la bonne échelle');
		$this->assertEquals("Sunny",$return["icone"],'Meteo ne renvoi pas la bonne icone');

		$this->obj->u(array("id_societe"=>$this->id_societe,"meteo"=>"0.0000"));
		$societe=$this->obj->select($this->id_societe);
		$return=$this->obj->meteo_icone("0.0000");
		$this->assertEquals("Pas de données pour la météo",$return["echelle"],"Meteo ne doit pas renvoyer d'échelles");
		$this->assertEquals("Fog",$return["icone"],"Meteo ne doit pas renvoyer d'icone");
	}

//	* @author Mathieu Tribouillard <mtribouillard@absystech.fr>
	public function testCreate_ref_prefix(){
		$objATT = new societe_att();
		$return=$objATT->create_ref_prefix();
		$this->assertEquals("AS",$return,"create_ref_prefix ne renvoi pas AS");
	}
	
    /** @authot mouad EL HIZABRI*/
    public function test_autocompleteFournisseursDeCommande(){
        $autocompleteFournisseursDeCommande=$this->obj->autocompleteFournisseursDeCommande(array("condition_value"=>640,"condition_field"=>"societe.id_societe"));
        $this->assertEquals("OVH",$autocompleteFournisseursDeCommande[0][1],"autocompleteFournisseursDeCommande ne renvoie pas la bonne valeur");
    
    
    }

    /*@author Quentin JANON <qjanon@absystech.fr> */ 
    public function test_autocompleteFournisseurs(){
        $r = $this->obj->autocompleteFournisseurs();

        $this->assertEquals("oui",ATF::societe()->select($r[0]['raw_0'],'fournisseur'),"autocompleteFournisseurs ne renvoie pas un fournisseur");
    
    
    }

    /*@author Quentin JANON <qjanon@absystech.fr> */ 
    public function test_estFermee(){
        $this->assertFalse($this->obj->estFermee(1),"La société est active :/");
        $soc = array("id_societe"=>1,"etat"=>"inactif");
        ATF::societe()->u($soc);
        $this->assertTrue($this->obj->estFermee(1),"La société est inactive :/");
   
    }
    
    /*@author Quentin JANON <qjanon@absystech.fr> */ 
    public function test_update(){
        $mdp = $this->obj->select(1,'divers_5');
        $soc = array("id_societe"=>1,"etat"=>"inactif");
        $this->obj->update($soc);
        $this->assertNotEquals($mdp, $this->obj->select(1,'divers_5'),"La société n'as pas changé de mot de passe hotline :/");
   
    }

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function testAutocompleteAvecTVA(){
		$infos=array("condition_field"=>"societe.id_societe","condition_value"=>ATF::user()->cryptId($this->id_societe));
		$retour=$this->obj->autocompleteAvecTVA($infos);
		$this->assertEquals($retour[0][1],$this->id_societe,"autocompleteAvecTVA ne renvoie pas le bon id");
		$this->assertEquals($retour[0][2],"TestTU","autocompleteAvecTVA ne renvoie pas le bon nom");
		$this->assertEquals($retour[0]["raw_0"],"1.2","autocompleteAvecTVA ne renvoie pas le bon tva");
		$this->assertEquals($retour[0]["raw_1"],$this->id_societe,"autocompleteAvecTVA ne renvoie pas le bon id");
		$this->assertEquals($retour[0]["raw_2"],"TestTU","autocompleteAvecTVA ne renvoie pas le bon nom");
	}


	//@author Yann Gaël GAUTHERON <ygautheron@absystech.fr>, Nicolas BERTEMONT <nbertemont@absystech.fr>
	public function test_rpcGetForMobile(){
		// On ne met que quelques infos
		$this->insertSociete();
		$this->insertContact();
		
		// Ajout d'un suivi
		$suivi=array('suivi'=>array(
			'texte'=>'TU suivi'
			,'id_societe'=>$this->id_societe
			,"suivi_contact"=>$this->id_contact
			,"suivi_societe"=>$this->id_user
		));
		$this->id_suivi=ATF::suivi()->insert($suivi);
		$suivi2=array('suivi'=>array(
			'texte'=>'TU suivi2'
			,'id_societe'=>$this->id_societe
			,"suivi_contact"=>$this->id_contact
			,"suivi_societe"=>$this->id_user
		));
		$this->id_suivi2=ATF::suivi()->insert($suivi2);
		$suivi3=array('suivi'=>array(
			'texte'=>'TU suivi3'
			,'id_societe'=>$this->id_societe
			,"suivi_contact"=>$this->id_contact
			,"suivi_societe"=>$this->id_user
		));
		$this->id_suivi3=ATF::suivi()->insert($suivi3);
			
		// Contact en plus avec une adresse
		$contact=array(
			"civilite"=>"M"
			,"prenom"=>"contact test2"
			,"nom"=>"unitaire2"
			,"email"=>"debug@absystech.fr"
			,"etat"=>"actif"
			,"tel"=>"123"
			,"id_societe"=>$this->id_societe
			,"adresse"=>"Adresse bidon"
		);
		$id_contact2 = ATF::contact()->i($contact);
		
		// Contacts de la société*/
		$donnees=$this->obj->rpcGetContactsForMobile(array("id"=>$this->id_societe));
		$donnees[5]["detail"] = "Mar. 23 aoû. 09:51 c unitaire c unitaire";
		$donnees[6]["detail"] = "Mar. 23 aoû. 09:51 c unitaire c unitaire";
		$donnees[7]["detail"] = "Mar. 23 aoû. 09:51 c unitaire c unitaire";
		$this->assertEquals(
array (
  0 => 
  array (
    'detail' => 'Adresse',
    'text' => '139 rue des arts   59100 Roubaix France',
    'group' => '1societe',
    'groupTitle' => 'TestTU',
    'type' => 'address',
  ),
  1 => 
  array (
    'detail' => 'Téléphone',
    'text' => '123',
    'group' => '1societe',
    'groupTitle' => 'TestTU',
    'type' => 'phone',
    'id_societe' => $this->id_societe,
  ),
  2 => 
  array (
    'group' => '2contact test2 unitaire2',
    'groupTitle' => 'contact test2 unitaire2',
    'etat' => 'actif',
    'detail' => 'Adresse postale',
    'text' => 'Adresse bidon     France',
    'type' => 'address',
  ),
  3 => 
  array (
    'group' => '2contact test2 unitaire2',
    'groupTitle' => 'contact test2 unitaire2',
    'etat' => 'actif',
    'detail' => 'Téléphone',
    'text' => '123',
    'type' => 'phone',
    'id_contact' => $id_contact2,
  ),
  4 => 
  array (
    'group' => '2contact test unitaire',
    'groupTitle' => 'contact test unitaire',
    'etat' => 'actif',
    'detail' => 'Téléphone',
    'text' => '123',
    'type' => 'phone',
    'id_contact' => $this->id_contact,
  ),
  5 => 
  array (
    'group' => '0suivis',
    'groupTitle' => 'Suivis',
    'detail' => 'Mar. 23 aoû. 09:51 c unitaire c unitaire',
    'text' => 'TU suivi3',
    'id_suivi' => '',
    'type' => 'suivi',
    'intervenant_societe' => 'c unitaire',
    'contact' => 'c unitaire',
    'notifie' => ' ',
    'texte' => 'TU suivi3',
    'societe' => 'TestTU',
  ),
  6 => 
  array (
    'group' => '0suivis',
    'groupTitle' => 'Suivis',
    'detail' => 'Mar. 23 aoû. 09:51 c unitaire c unitaire',
    'text' => 'TU suivi2',
    'id_suivi' => '',
    'type' => 'suivi',
    'intervenant_societe' => 'c unitaire',
    'contact' => 'c unitaire',
    'notifie' => ' ',
    'texte' => 'TU suivi2',
    'societe' => 'TestTU',
  ),
  7 => 
  array (
    'group' => '0suivis',
    'groupTitle' => 'Suivis',
    'detail' => 'Mar. 23 aoû. 09:51 c unitaire c unitaire',
    'text' => 'TU suivi',
    'id_suivi' => '',
    'type' => 'suivi',
    'intervenant_societe' => 'c unitaire',
    'contact' => 'c unitaire',
    'notifie' => ' ',
    'texte' => 'TU suivi',
    'societe' => 'TestTU',
  ),
)			
			,$donnees,"Les contacts sont mauvais");
		
		// Société en plus client pour tester la prospection
		$societe = array(
			"societe"=>"- TestTUclient"
			,"latitude"=>50.6895
			,"longitude"=>3.16284
			,"adresse"=>"139 rue des arts"
			,"cp"=>"59100"
			,"ville"=>"Roubaix"
			,"tel"=>"123"
			,"relation"=>"client"
			,'id_owner'=>$this->id_user
		);
		$id_societe2 = ATF::societe()->i($societe);
		
		// Sociétés de la base
		$c_soc=new socTu();
		$c_soc->id_societe=$this->id_societe;
		$c_soc->id_societe2=$id_societe2;
		$donnees=$c_soc->rpcGetForMobile();
		$this->assertEquals('[{"societe":"- TestTUclient","id_societe":"'.$id_societe2.'","societe.id_societe":"'.$id_societe2.'","indexAlpha":"-"},{"societe":"TestTU","id_societe":"'.$this->id_societe.'","societe.id_societe":"'.$this->id_societe.'","indexAlpha":"T"}]'
			,json_encode($donnees),"Les sociétés retournées sont mauvaises");

		// Prospection, doit retourner les deux société prospect et client
		$donnees=$c_soc->rpcGetRecentToRecallForMobile(array("id_societe"=>$this->id_societe));
		$this->assertEquals('[{"text":"TestTU","id_societe":"'.$this->id_societe.'","group":"prospect","groupTitle":"Prospect"},{"text":"- TestTUclient","id_societe":"'.$id_societe2.'","group":"client","groupTitle":"Client"}]'
			,json_encode($donnees),"1/ Les sociétés de prospections ne sont pas bonnes");
			
		// Pour épargner le truncate de 30s dans societe.test.php
		$donnees=$c_soc->rpcGetRecentToRecallForMobile(array("id_societe"=>$this->id_societe));
		$this->assertEquals('[{"text":"TestTU","id_societe":"'.$this->id_societe.'","group":"prospect","groupTitle":"Prospect"},{"text":"- TestTUclient","id_societe":"'.$id_societe2.'","group":"client","groupTitle":"Client"}]'
			,json_encode($donnees),"2/ Les sociétés de prospections ne sont pas bonnes");
		
		// Prospection, incrémente le compteur lorsqu'on appelle le client avec son mobile
		$this->assertTrue($this->obj->rpcSetRecalledForMobile(array("id_contact"=>$this->id_contact)),'La procédure doit retourner TRUE');
		$this->assertEquals(1,$this->obj->select($this->id_societe,'recallCounter'),'Le compteur de recall est mauvais');
		
		// Retourne les sociétés à proximité des coordonnées
		$donnees=$c_soc->rpcGetProximityForMobile(array("nw"=>"50.683326,3.155093","se"=>"50.695044,3.168826"));
		$this->assertEquals('[{"societe.id_societe":"- TestTUclient","latitude":"50.6895","longitude":"3.16284","societe":"- TestTUclient","adresse":"139 rue des arts","adresse_2":"","adresse_3":"","cp":"59100","ville":"Roubaix","id_pays":"FR","societe.id_societe_fk":"'.$id_societe2.'"}]'
			,json_encode($donnees),"Le nombre de suivi retourné n'est pas correcte");
		
		// Tester la classe société sans la surcharge societe_absystech
		$testor = new societe();
		$donnees=$testor->rpcGetRecentToRecallForMobile(array("id_societe"=>$this->id_societe));
		$this->assertGreaterThan(0,count($donnees),"0/ Les sociétés de prospections ne sont pas bonnes");
	}
	
	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function testRapprocher(){
		$infos["id_societe"]=$this->id_societe;
		$infos["montant"]="NaN";
		$rapprocher=$this->obj->rapprocher($infos);
		$this->assertNotNull("ATF.RapprocherFacturesForm = new Ext.FormPanel({autoWidth: true,frame:true,items: [[]],buttons: [{text: 'Valider',handler: function(){ATF.RapprocherFacturesForm.getForm().submit({method  : 'post',waitMsg : 'Rapprocher factures...',waitTitle : 'Chargement',url     : 'extjs.ajax',params: {'extAction':'','extMethod':'updateRapprocher'},success:function(form, action) {if(action.result.result){var montant=Ext.getCmp('societe[montant]').getValue();Ext.getCmp('rapprocher_".$this->id_societe."').destroy();ATF.getRapprocherWindow(montant);}else{ATF.extRefresh(action);}},failure:function(form, action) {ATF.extRefresh(action);},timeout:3600});}}]});",$rapprocher,"rapprocher ne renvoie pas True");
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function testUpdateRapprocher(){
		$this->assertTrue($this->obj->updateRapprocher(),"updateRapprocher ne renvoie pas True");
	}
	
	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function testGetGroup(){
		$societe1 = array(
			"societe"=>"societe TEST TU 1",
			"id_filiale"=>$this->id_societe
		);
		$id_societe1 = ATF::societe()->decryptId(ATF::societe()->i($societe1));

		$societe2 = array(
			"societe"=>"societe TEST TU 2",
			"id_filiale"=>$id_societe1
		);
		$id_societe2 = ATF::societe()->i($societe2);
		
		$getGroup1=$this->obj->getGroup($this->id_societe);
		$this->assertEquals($this->id_societe,$getGroup1[0]["id_societe"],"getGroup 1 ne renvoi pas le bon résultat 1");
		$this->assertEquals($id_societe1,$getGroup1[1]["id_societe"],"getGroup 2 ne renvoi pas le bon résultat 1");
		$this->assertEquals($id_societe2,$getGroup1[2]["id_societe"],"getGroup 3 ne renvoi pas le bon résultat 1");
		
		$getGroup2=$this->obj->getGroup($this->id_societe);
		$this->assertEquals($getGroup1,$getGroup2,"getGroup ne renvoi pas le bon résultat 2");

		$getGroup3=$this->obj->getGroup($this->id_societe);
		$this->assertEquals($getGroup1,$getGroup3,"getGroup ne renvoi pas le bon résultat 3");
	}
	
	
	/** 
	* @author Morgan Fleurquin <mfleruquin@absystech.fr>
	*/
	public function test_autocompleteOnlyActive(){
		$autocompleteOnlyActive=$this->obj->autocompleteOnlyActive();
		$this->assertNotNull($autocompleteOnlyActive,"autocompleteOnlyActive ne renvoi aucune société active ???");
		
		$id = $autocompleteOnlyActive[0]["raw_0"];		
		$this->obj->update(array("id_societe"=>$id , "etat"=> "inactif"));
		$autocompleteOnlyActive=$this->obj->autocompleteOnlyActive();
		
		$this->assertNotEquals($id,$autocompleteOnlyActive[0]["raw_0"],"autocompleteOnlyActive renvoi une societe alors qu'elle est inactive??");
	}
//	* @author Mathieu Tribouillard <mtribouillard@absystech.fr>
	public function testMeteo(){
		$meteo=$this->obj->meteo($this->id_societe);
		$this->assertEquals("Fog",$meteo["icone"],'La météo doit renvoyer un fog quand il n y a pas de données');
		$this->assertEquals("Pas de données",$meteo["echelle"],'La météo ne doit pas renvoyer d echelles quand il n y a pas de données');
		$this->assertNull($meteo["solde_total"],'La météo ne doit pas renvoyer de solde_total');

		//Contact
		$contact["nom"]="Tu_devis";
		$this->id_contact=ATF::contact()->insert($contact);

		//Devis
		$this->devis["devis"]["id_contact"]=$this->id_contact;
		$this->devis["devis"]['resume']='Tu_devis';
		$this->devis["devis"]["date"]=date('Y-m-d');
		$this->devis["devis"]['id_societe']=$this->id_societe;
		$this->devis["devis"]['validite']=date('Y-m-d');
		$this->devis["devis"]['prix']="200";
		$this->devis["devis"]['frais_de_port']="50";
		$this->devis["devis"]['prix_achat']="50";
		
		//Devis_ligne
		$this->devis["values_devis"]=array("produits"=>'[{"devis_ligne__dot__ref":"TU","devis_ligne__dot__produit":"Tu_devis","devis_ligne__dot__quantite":"15","devis_ligne__dot__poids":"10","devis_ligne__dot__prix":"10","devis_ligne__dot__prix_achat":"10","devis_ligne__dot__id_fournisseur":"1","devis_ligne__dot__id_compte_absystech":"1","devis_ligne__dot__marge":97.14,"devis_ligne__dot__id_fournisseur_fk":"1"}]');

		//Insertion
		$this->id_devis = ATF::devis()->insert($this->devis,$this->s);
		$this->id_affaire = ATF::devis()->select($this->id_devis,"id_affaire");

		//Commande
		$this->commande["commande"]=$this->devis["devis"];
		$this->commande["commande"]["date"]=date('Y-m-d');
		$this->commande["commande"]["id_affaire"]=$this->id_affaire;
		$this->commande["commande"]["id_devis"]=$this->id_devis;
		
		//Commande_ligne
		$this->commande["values_commande"]=array("produits"=>'[{"commande_ligne__dot__ref":"TU","commande_ligne__dot__produit":"Tu_commande","commande_ligne__dot__quantite":"15","commande_ligne__dot__prix":"10","commande_ligne__dot__prix_achat":"10","commande_ligne__dot__id_fournisseur":"1","commande_ligne__dot__id_compte_absystech":"1","commande_ligne__dot__marge":97.14,"commande_ligne__dot__id_fournisseur_fk":"1"}]');

		//Insertion
		unset($this->commande["commande"]["id_contact"],$this->commande["commande"]["validite"]);
		$this->id_commande = ATF::commande()->insert($this->commande,$this->s);

		//Facture
		$this->facture["facture"]=$this->commande["commande"];
		$this->facture["facture"]["date"]=date('Y-m-d');
		$this->facture["facture"]["id_affaire"]=$this->id_affaire;
		$this->facture["facture"]["mode"]="facture";
		$this->facture["facture"]["id_termes"]=2;
		$this->facture["facture"]["tva"]=1.2;
		
		//Facture_ligne
		$this->facture["values_facture"]=array("produits"=>'[{"facture_ligne__dot__ref":"TU","facture_ligne__dot__produit":"Tu_facture","facture_ligne__dot__quantite":"15","facture_ligne__dot__prix":"10","facture_ligne__dot__prix_achat":"10","facture_ligne__dot__id_fournisseur":"1","facture_ligne__dot__serial":"777","facture_ligne__dot__id_compte_absystech":"1","facture_ligne__dot__marge":97.14,"facture_ligne__dot__id_fournisseur_fk":"1"}]');

		//Insertion
		unset($this->facture["facture"]["resume"],$this->facture["facture"]["prix_achat"],$this->facture["facture"]["id_devis"]);
		$this->id_facture = ATF::facture()->insert($this->facture,$this->s);

		$meteo=$this->obj->meteo($this->id_societe);
		$this->assertEquals("3.45 = (30*(((/30)-1)+1))-(50*0)+(15*(((240.00/1000)-1)+1))-(5*(((150/5000)-1)+1))+(1*(((0/10)-1)+1))",$meteo["meteo_calcul"],'1 La météo ne renvoit pas la meteo_calcul');
		$this->assertEquals("3,45",str_replace(".",",",$meteo["meteo"]),'La météo ne renvoit pas la meteo');
		$this->assertEquals("240.00",$meteo["solde_total"],'La météo ne renvoit pas le solde_total');

		ATF::commande()->d($this->id_commande);
		$meteo=$this->obj->meteo($this->id_societe);
		$this->assertEquals("3.4 = (30*(((/30)-1)+1))-(50*0)+(15*(((240.00/1000)-1)+1))-(5*(((200/5000)-1)+1))+(1*(((0/10)-1)+1))",$meteo["meteo_calcul"],'2 La météo ne renvoit pas la meteo_calcul');
		$this->assertEquals("3,4",str_replace(".",",",$meteo["meteo"]),'La météo ne renvoit pas la meteo');
		$this->assertEquals("240",$meteo["solde_total"],'La météo ne renvoit pas le solde_total');

	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>*/  
	public function test_insert(){
		$id=$this->obj->insert(array(
					'societe'=>array(
						'societe'=>'Testouille2'
						,'relation'=>'suspect')
		),$this->s);
		$societe=$this->obj->select($id);
		$this->assertNotNull($societe['divers_5'],'L\'insert ne gère pas le divers_5');

		$societe=array(
								"societe"=>"Nom TU",
								"id_famille"=>2,
								"etat"=>"inactif",
								"id_owner"=>$this->id_user,
								"adresse"=>"adresse TU",
								"adresse_2"=>"adresse TU2",
								"adresse_3"=>"adresse TU3",
								"cp"=>"59000",
								"ville"=>"Lille de m...",
								"id_pays"=>"FR",
								"tel"=>"0606060606",
								"fax"=>"0606060607",
								"email"=>"mail@tu.absystech.fr",
								"cle_externe"=>"TU"
		);
		
		$id=$this->obj->insert($societe,$this->s);
		
		ATF::contact()->q->reset()->addCondition("id_societe",$id)->setDimension("row");
		$contact=ATF::contact()->sa();
		$this->assertEquals(array(
								"id_contact"=>$contact["id_contact"],
								"date"=>$contact["date"],
								"civilite"=>NULL,
								"nom"=>"NOM TU",
								"prenom"=>NULL,
								"etat"=>$societe["etat"],
								"id_societe"=>$id,
								"id_owner"=>$this->id_user,
								"private"=>"non",
								"adresse"=>$societe["adresse"],
								"adresse_2"=>$societe["adresse_2"],
								"adresse_3"=>$societe["adresse_3"],
								"cp"=>$societe["cp"],
								"ville"=>$societe["ville"],
								"id_pays"=>$societe["id_pays"],
								"tel"=>$societe["tel"],
								"gsm"=>NULL,
								"fax"=>$societe["fax"],
								"email"=>$societe["email"],
								"fonction"=>NULL,
								"departement"=>NULL,
								"anniversaire"=>NULL,
								"loisir"=>NULL,
								"langue"=>NULL,
								"assistant"=>NULL,
								"assistant_tel"=>NULL,
								"tel_autres"=>NULL,
								"adresse_autres"=>NULL,
								"forecast"=>"0",
								"description"=>NULL,
								"cle_externe"=>$societe["cle_externe"],
								"disponibilite"=>NULL,
								"login"=>NULL,
								"pwd"=>NULL
							)
							,$contact
							,'contact mal insérée');
	}
	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>*/ 
	public function test_societes_debitrices(){
		//On insère une société débitrice
		ATF::gestion_ticket()->i(array("operation"=>1,"id_societe"=>$this->id_societe,"type"=>"ajout","solde"=>"-100000"));
		$societe=$this->obj->societes_debitrices(10);
		$this->assertEquals($this->id_societe,$societe[0]['id_societe'],'societes_debitrices ne renvoie pas les sociétés les plus débitrices');
	} 
	
	/*@author Yann-Gaël GAUTHERON <mtribouillard@absystech.fr>*/ 
	public function test_solde_total_global(){
		$soldes=$this->obj->solde_total_global();
		$this->assertGreaterThan($soldes[0]['soldeTotal'],0,'Solde comptable incorrect 1');
		$this->assertGreaterThanOrEqual($soldes[0]['soldeTotal'],$soldes[1]['soldeTotal'],'Solde comptable incorrect');
	} 

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	public function test_getSoldeSNegatives(){
		//On insère une société débitrice
		ATF::gestion_ticket()->i(array("operation"=>1,"id_societe"=>$this->id_societe,"type"=>"ajout","solde"=>"-100000"));
		$solde=$this->obj->getSoldeSNegatives();
		$this->assertLessThan("-100000",$solde,'getSoldeSNegatives ne renvoie pas le bon solde');
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>, Nicolas BERTEMONT <nbertemont@absystech.fr> */ 
	public function test_getSoldeS(){
		//On insère une société débitrice
		$solde_av=$this->obj->getSoldeS();
		ATF::gestion_ticket()->i(array("operation"=>1,"id_societe"=>$this->id_societe,"type"=>"ajout","solde"=>"-100000"));
		$solde_ap=$this->obj->getSoldeS();
		$this->assertEquals("-100000",$solde_ap-$solde_av,'getSoldeS ne renvoie pas le bon solde');
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	public function test_add_ticket() {		
		ATF::$msg->getNotices();
		//On insère une société débitrice
		ATF::gestion_ticket()->i(array("operation"=>1,"id_societe"=>$this->id_societe,"type"=>"ajout","solde"=>"0"));
		//Test de la méthode
		$infos=array('id_societe'=>$this->id_societe,'credits'=>10,'libelle'=>'test_libelle');
		$this->obj->add_ticket($infos,$this->s);
		$this->assertEquals(array(0=>array("msg"=>"Ajout des tickets effectué !","title"=>"","timer"=>"","type"=>"success")),ATF::$msg->getNotices(),"3 La notice d'annulation fonctionne bien");
	
		//Lecture du nombre de tickets
		$nb_credits=$this->obj->getSolde($this->id_societe);
		$this->assertEquals("10",$nb_credits,'add_ticket n\'insère pas le ticket');
		ATF::$msg->getNotices();
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>*/ 
	public function test_redirect_hotline() {
		$hotline=$this->obj->redirect_hotline("reference","password_hotline");
		$this->assertEquals(__HOTLINE_URL__."login.php?login=cmVmZXJlbmNl&password=cGFzc3dvcmRfaG90bGluZQ==&contact=Y2hvaXhfY29udGFjdA==&url=".base64_encode(__HOTLINE_URL__."choix_contact.php")."&schema=".base64_encode("absystech"),$hotline,'redirect_hotline ne renvoie pas la bonne url');
	} 

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>*/  
	public function test_send_identifiants_hotline() {		
							
		// Erreur 1
		try {
			$send_identifiants_hotline=$this->obj->send_identifiants_hotline(array("id_societe"=>$this->id_societe));
		} catch (errorATF $e) {
			$erreur_trouvee1 = $e->getMessage();
		}
		$this->assertEquals("Le contact n'est pas spécifié ! Vous devez choisir un contact afin d'envoyer le mail.",$erreur_trouvee1,"send_identifiants_hotline : Erreur non attrapé : il manque un contact");

		// Erreur 2
		try {
			$send_identifiants_hotline=$this->obj->send_identifiants_hotline(array("id_contact"=>$this->id_contact));
		} catch (errorATF $e) {
			$erreur_trouvee2 = $e->getMessage();
		}
		$this->assertEquals("La société n'est pas spécifié !",$erreur_trouvee2,"send_identifiants_hotline : Erreur non attrapé : il manque une société");


		// Erreur 3
		ATF::contact()->u(array("id_contact"=>$this->id_contact,"email"=>""));
		try {
			$send_identifiants_hotline=$this->obj->send_identifiants_hotline(array("id_contact"=>$this->id_contact,"id_societe"=>$this->id_societe));
		} catch (errorATF $e) {
			$erreur_trouvee3 = $e->getMessage();
		}
		$this->assertEquals("Le contact que vous avez sélectionné n'a pas d'adresse mail ! Impossible d'envoyer les identifiants.",$erreur_trouvee3,"send_identifiants_hotline : Erreur non attrapé : il n'y a pas de mail pour le contact");

		ATF::contact()->u(array("id_contact"=>$this->id_contact,"email"=>"mtribouillard@absystech.fr"));
		ATF::$msg->getNotices();
		$send_identifiants_hotline=$this->obj->send_identifiants_hotline(array("id_contact"=>$this->id_contact,"id_societe"=>$this->id_societe));
		$notice=ATF::$msg->getNotices();
		$this->assertTrue($send_identifiants_hotline,"send_identifiants_hotline n\'a pas fonctionné");
		$this->assertEquals("Les identifiants ont été envoyés par mail !",$notice[0]["msg"],"send_identifiants_hotline ne renvoie pas la bonne notice");
	}

	/*@author Mathieu Tribouillard <mtribouillard@absystech.fr>*/
	public function test_getSolde() {		
		$nb_credits=$this->obj->getSolde($this->id_societe);
		$this->assertEquals("0",$nb_credits,'getSolde ne renvoie pas 0 lorsqu\il n\'y a pas de ticket');

		$infos=array('id_societe'=>$this->id_societe,'credits'=>10,'libelle'=>'test_libelle');
		$this->obj->add_ticket($infos,$this->s);
		$notice=ATF::$msg->getNotices();
		$this->assertEquals(ATF::$usr->trans("gestion_ticket_add","gestion_ticket"),$notice[0]["msg"],"add_ticket ne renvoie pas la bonne notice");
		
		$nb_credits=$this->obj->getSolde($this->id_societe);
		$this->assertEquals("10",$nb_credits,'getSolde ne renvoie pas le bon montant sans date');

		$nb_credits=$this->obj->getSolde($this->id_societe,"2010-01-01 00:00:00");
		$this->assertEquals("0",$nb_credits,'getSolde ne renvoie pas le bon montant avec date');

		$nb_credits=$this->obj->getSolde($this->id_societe,date("Y-m-d H:i:s",strtotime($data["date_data"]."+1 hour")));
		$this->assertEquals("10",$nb_credits,'getSolde ne renvoie pas le bon montant avec date');
	}

//	* @author Quentin JANON <qjanon@absystech.fr>
//	* @date 24-12-2010 
	public function test_atcardImport() {
		$this->assertFalse($this->obj->atcardImport(),"Erreur : Rien en entrée et pas de False :/");
		
		$infos = array();
		$s = array();
		$files = array("atcardImport"=>array("tmp_name"=>"/home/optima/core/test/absystech/societe_ATCARD-TU.atcf"));
		$cr = array();
		
		try {
			$this->obj->atcardImport($infos,$s,$files,$cr);
		} catch (errorATF $e) {
			$erreur_trouvee = $e->getMessage();
		}
		$this->assertNotNull($erreur_trouvee,"Erreur non attrapé : pas de taille de fichier");
		unset($erreur_trouvee);
		
		try {
			$files['atcardImport']['size'] = "666";
			$r = $this->obj->atcardImport($infos,$s,$files,$cr);
		} catch (errorATF $e) {
			$erreur_trouvee = $e->getMessage();
		}

		$this->assertNull($erreur_trouvee,"Erreur attrapé : problème :/ =>".$erreur_trouvee);
		$this->assertNotNull($r,"Erreur : Doit retourner un JSON");
		
		try {
			$this->obj->atcardImport($infos,$s,$files,$cr);
		} catch (errorATF $e) {
			$erreur_trouvee = $e->getMessage();
		}
		$this->assertNotNull($erreur_trouvee,"Erreur non attrapé : problemeLorsDeLimport");
		
	}

//	* @author Quentin JANON <qjanon@absystech.fr>
//	* @date 29-12-2010
	public function test_atcard() {
		$fp = $this->obj->filepath($id,"ATCARD-".$this->obj->nom(ATF::$usr->get('id_societe')));
		touch($fp);
		$i = array("id_societe"=>ATF::$usr->get('id_societe'));
		ob_start();
		$this->obj->atcard($i);
		$r = ob_get_clean();
		$this->assertNotNull(strpos("/===BEGIN:SOCIETE===/",$r),"Erreur : il manque le ===BEGIN:SOCIETE===");
		$this->assertNotNull(strpos("/===BEGIN:CONTACT===/",$r),"Erreur : il manque le ===BEGIN:CONTACT===");
		unlink($fp);
	}
	
//	* @author Mathieu Tribouillard <mtribouillard@absystech.fr>
	public function test_autocompleteAvecTermes(){
		$this->obj->update(array("id_societe"=>$this->id_societe,"id_termes"=>1));
		$autocompleteAvecTermes=$this->obj->autocompleteAvecTermes(array("condition_value"=>$this->id_societe,"condition_field"=>"societe.id_societe"));
		$this->assertEquals("A réception de facture",$autocompleteAvecTermes[0][0],"autocompleteAvecTermes ne renvoie pas la bonne valeur avec 1 paramètre");
		$autocompleteAvecTermes=$this->obj->autocompleteAvecTermes(array("condition_value"=>$this->id_societe,"condition_field"=>"societe.id_societe"),false);
		$this->assertEquals("A réception de facture",$autocompleteAvecTermes[0][0],"autocompleteAvecTermes ne renvoie pas la bonne valeur avec 2 paramètres");
	}

//	* @author Quentin JANON <qjanon@absystech.fr>
//	* @date 11-01-2011
	public function test_getCSSSocieteDebitrice() {
		$this->assertFalse($this->obj->getCSSSocieteDebitrice(),"Erreur, doit retourner FALSE sans rien en entrée");
		
		ATF::gestion_ticket()->i(array("operation"=>1,"id_societe"=>$this->id_societe,"date"=>"2005-12-12","type"=>"ajout","solde"=>"-100000"));
		$this->assertEquals("societeDebitriceToCall",$this->obj->getCSSSocieteDebitrice(array("id_societe"=>$this->id_societe)),"Erreur : La classe CSS devrait être societeDebitriceToCall");
		
		ATF::gestion_ticket()->i(array("operation"=>1,"id_societe"=>$this->id_societe,"date"=>date("Y-m-d"),"type"=>"ajout","solde"=>"-100000"));
		$this->assertEquals("societeDebitrice",$this->obj->getCSSSocieteDebitrice(array("id_societe"=>$this->id_societe)),"Erreur : La classe CSS devrait être societeDebitriceToCall");
	}
	
};

class socTu extends societe_absystech {
	public function select_all() {
		$this->q->addCondition("id_societe",$this->id_societe)->addCondition("id_societe",$this->id_societe2);
		return parent::select_all();
	}
}
?>