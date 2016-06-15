<?
class affaire_absystech_test extends ATF_PHPUnit_Framework_TestCase {

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function setUp() {
		$this->initUser();

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
		$this->commande["commande"]["id_affaire"]=$this->id_affaire;
		$this->commande["commande"]["id_devis"]=$this->id_devis;
		
		//Commande_ligne
		$this->commande["values_commande"]=array("produits"=>'[{"commande_ligne__dot__ref":"TU","commande_ligne__dot__produit":"Tu_commande","commande_ligne__dot__quantite":"15","commande_ligne__dot__prix":"10","commande_ligne__dot__prix_achat":"10","commande_ligne__dot__id_fournisseur":"1","commande_ligne__dot__id_compte_absystech":"1","commande_ligne__dot__marge":97.14,"commande_ligne__dot__id_fournisseur_fk":"1"}]');

		//Insertion
		unset($this->commande["commande"]["id_contact"],$this->commande["commande"]["validite"]);
		$this->id_commande = ATF::commande()->insert($this->commande,$this->s);

		//Facture
		$this->facture["facture"]=$this->commande["commande"];
		$this->facture["facture"]["date"]="2010-01-01";
		$this->facture["facture"]["id_affaire"]=$this->id_affaire;
		$this->facture["facture"]["mode"]="facture";
		$this->facture["facture"]["id_termes"]=2;
		$this->facture["facture"]["tva"]=1.2;
		
		//Facture_ligne
		$this->facture["values_facture"]=array("produits"=>'[{"facture_ligne__dot__ref":"TU","facture_ligne__dot__produit":"Tu_facture","facture_ligne__dot__quantite":"15","facture_ligne__dot__prix":"10","facture_ligne__dot__prix_achat":"10","facture_ligne__dot__id_fournisseur":"1","facture_ligne__dot__serial":"777","facture_ligne__dot__id_compte_absystech":"1","facture_ligne__dot__marge":97.14,"facture_ligne__dot__id_fournisseur_fk":"1"}]');

		//Insertion
		unset($this->facture["facture"]["resume"],$this->facture["facture"]["prix_achat"],$this->facture["facture"]["id_devis"]);
		$this->id_facture = ATF::facture()->insert($this->facture,$this->s);
	}
	
	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function tearDown(){
		ATF::db()->rollback_transaction(true);
		//Flush des notices
		ATF::$msg->getNotices();
	}

	/*@author Nicolas BERTEMONT <nbertemont@absystech.fr> */ 
	public function recupDonneesVerif(){
		//récupération des éléments permettant le calcul de la marge
		ATF::commande()->q->reset()
							->addField("YEAR(date)","year")
							->addField("MONTH(date)","month")
							->addField("-prix_achat","prix")
							->setStrict()
							->setToString();
		ATF::facture()->q->reset()
						->addField("YEAR(date)","year")
						->addField("MONTH(date)","month")
						->addField("prix")
						->setStrict()
						->setToString();	
		
		$this->obj->q->reset()
					->addUnion(ATF::commande()->sa("no_order"))
					->addUnion(ATF::facture()->sa("no_order"));
		$subQuery=$this->obj->q->getUnion();
		
		//requête récupérant la marge (en utilisant la subquery)
		$this->obj->q->reset()
					->addField("uni.year","year")
					->addField("uni.month","month")
					->addField("SUM(uni.prix)","prix")
					->setStrict()
					->setSubQuery($subQuery,'uni')
					->addCondition("year",date('Y')-1,"OR",false,">=")
					->addGroup("year")
					->addGroup("month");
		$donnees=$this->obj->sa("no_order");
		
		return $donnees;
	}
	
	/*@author Nicolas BERTEMONT <nbertemont@absystech.fr> */
	public function testStats(){
		unset(ATF::stats()->liste_annees['affaire']);
	
		//CA
		ATF::stats()->liste_annees['affaire'][2010]=1;
		$CA=$this->obj->stats(false,'CA');
		unset(ATF::stats()->liste_annees['affaire']);

		//check de la méthode ajoutDonnees
		$this->assertTrue(count($CA['dataset'])>0,'CA / Problème de récupération des données à afficher');
		//check de la méthode paramGraphe
		$this->assertTrue(is_array($CA['params']),'CA / Problème de récupération des params');
		//le nombre de valeur dans les set de data doit être égal au nombre de catégorie
		$this->assertTrue(isset($CA['dataset'][2010]),'CA / Problème de récupération des dataset');
		$this->assertEquals(2010,$CA['dataset'][2010]['params']['seriesname'],'CA / Problème de params dans les données');
		$this->assertEquals(count($CA['categories']['category']),count($CA['dataset'][2010]['set']),"CA / Le nombre de données à afficher en abscisse n'est pas correct");

		//test du contenu
		$this->assertEquals("Mars",$CA['categories']['category']['2']['label'],"CA / Le nom des catégories n'est pas correct");
		$this->assertEquals("Affaires",$CA['params']['caption'],"CA / Le nom des légendes n'est pas correct");
		$this->assertEquals("CA par mois",$CA['params']['subCaption'],"CA / Le nom des sous légendes n'est pas correct");
		$this->assertEquals("62736.61",$CA['dataset'][2010]['set']["04"]['value'],'CA / 1/ Les valeurs ne sont pas correctes');
		$this->assertEquals("2010 : 237187.62",$CA['dataset'][2010]['set']["09"]['titre'],'CA / 2/ Les valeurs ne sont pas correctes');
		$this->assertEquals("affaire.html%2Cstats%3D1%26annee%3D2010%26mois%3D12%26label%3D%26type%3DCA",$CA['dataset'][2010]['set']["12"]['link'],'CA / 3/ Les valeurs ne sont pas correctes');

		//marge_detail
		$marge_detail=$this->obj->stats(false,'marge_detail',false,(date('Y')-1));
		
		//check de la méthode ajoutDonnees
		$this->assertTrue(count($marge_detail['dataset'])==3,'marge_detail / Problème de récupération des données à afficher');
		$this->assertTrue(is_array($marge_detail['params']),'marge_detail / Problème de récupération des params');
		$this->assertTrue(isset($marge_detail['dataset']['commande']),'marge_detail / Problème de récupération des dataset');
		$this->assertEquals('Vente',$marge_detail['dataset']['facture']['params']['seriesname'],'marge_detail / Problème de params dans les données');
		$this->obj->getDonneesGraphe($verif,(date('Y')-1));
		//$this->assertEquals(count($marge_detail['dataset']['marge']['set']),count($verif['marge']),"marge_detail / Le nombre de données à afficher n'est pas correct");
		//test du contenu
		$this->assertEquals("Mars",$marge_detail['categories']['category']['2']['label'],"marge_detail / Le nom des catégories n'est pas correct");
		$this->assertEquals("Marge en detail de l annee courante",$marge_detail['params']['caption'],"marge_detail / Le nom des légendes n'est pas correct");
		$this->assertEquals($verif['commande'][0]['prix'],$marge_detail['dataset']['commande']['set']['01']['value'],'marge_detail / 1/ Les valeurs ne sont pas correctes');
		$this->assertEquals("Vente : ".$verif['facture'][0]['prix']." €",$marge_detail['dataset']['facture']['set']['01']['titre'],'marge_detail / 2/ Les valeurs ne sont pas correctes');

		//marge
		ATF::stats()->liste_annees['affaire'][2010]=1;
		$marge=$this->obj->stats(false,'marge');
		unset(ATF::stats()->liste_annees['affaire']);

		//check de la méthode ajoutDonnees	
		$this->assertTrue(count($marge['dataset'])>0,'marge / Problème de récupération des données à afficher');
		$this->assertTrue(is_array($marge['params']),'marge / Problème de récupération des params');
		$this->assertTrue(isset($marge['dataset'][2010]),'marge / Problème de récupération des dataset');
		$this->assertEquals(2010,$marge['dataset'][2010]['params']['seriesname'],'marge / Problème de params dans les données');
		$this->assertEquals(count($marge['categories']['category']),count($marge['dataset'][2010]['set']),"marge / Le nombre de données à afficher en abscisse n'est pas correct");
	
		//test du contenu
		$this->assertEquals("Mars",$marge['categories']['category']['2']['label'],"marge / Le nom des catégories n'est pas correct");
		$this->assertEquals("Affaires",$marge['params']['caption'],"marge / Le nom des légendes n'est pas correct");
		$this->assertEquals("Marge par mois",$marge['params']['subCaption'],"marge / Le nom des sous légendes n'est pas correct");
		$this->assertEquals("57651.80",$marge['dataset'][2010]['set']["04"]['value'],'marge / 1/ Les valeurs ne sont pas correctes');
		$this->assertEquals("2010 : 209300.04",$marge['dataset'][2010]['set']["09"]['titre'],'marge / 2/ Les valeurs ne sont pas correctes');
		$this->assertEquals("affaire.html%2Cstats%3D1%26annee%3D2010%26mois%3D12%26label%3D%26type%3Dmarge",$marge['dataset'][2010]['set']["12"]['link'],'marge / 3/ Les valeurs ne sont pas correctes');

		//marge widget 
		ATF::stats()->liste_annees['affaire'][date('Y')]=1;
		$marge_widget=$this->obj->stats(false,'marge',true,date('Y'));
		unset(ATF::stats()->liste_annees['affaire']);
		//check de la méthode ajoutDonnees	
		$this->assertTrue(count($marge_widget['dataset'])==2,'marge widget / Problème de récupération des données à afficher');
		$this->assertTrue(is_array($marge_widget['params']),'marge widget / Problème de récupération des params');
		$this->assertTrue(isset($marge_widget['dataset'][date('Y')-1]),'marge widget / Problème de récupération des dataset');
		$this->assertEquals(date('Y')-1,$marge_widget['dataset'][date('Y')-1]['params']['seriesname'],'marge widget / Problème de params dans les données');
		$this->assertEquals(count($marge_widget['categories']['category']),count($marge_widget['dataset'][date('Y')-1]['set']),"marge widget / Le nombre de données à afficher en abscisse n'est pas correct");
		
		//récupération de données pour vérifier les valeurs renvoyées par les stats		
		foreach($this->recupDonneesVerif() as $key=>$item){
			$compare[$item['year']][$item['month']]=$item['prix'];
		}
						
		//test du contenu
		$this->assertEquals("M",$marge_widget['categories']['category']['2']['label'],"marge widget / Le nom des catégories n'est pas correct");
		$this->assertEquals($compare[date('Y')-1][10],$marge_widget['dataset'][date('Y')-1]['set']["10"]['value'],'marge widget / 1/ Les valeurs ne sont pas correctes');
		$this->assertEquals((date('Y')-1)." : ".$compare[date('Y')-1][10],$marge_widget['dataset'][date('Y')-1]['set']["10"]['titre'],'marge widget / 2/ Les valeurs ne sont pas correctes');
		$this->assertEquals("affaire.html%2Cstats%3D1%26annee%3D".(date('Y')-1)."%26mois%3D12%26label%3D%26type%3Dmarge",$marge_widget['dataset'][date('Y')-1]['set']["12"]['link'],'marge widget / 3/ Les valeurs ne sont pas correctes');
	
		//default
		ATF::stats()->liste_annees['affaire'][2010]=1;
		$defaut=$this->obj->stats(false,'lol');
		unset(ATF::stats()->liste_annees['affaire']);
		
		//check de la méthode ajoutDonnees	
		$this->assertTrue(count($defaut['dataset'])>0,'defaut / Problème de récupération des données à afficher');
		$this->assertTrue(is_array($defaut['params']),'defaut / Problème de récupération des params');
		$this->assertTrue(isset($defaut['dataset'][2010]),'defaut / Problème de récupération des dataset');
		$this->assertEquals(2010,$defaut['dataset'][2010]['params']['seriesname'],'defaut / Problème de params dans les données');
		$this->assertEquals(count($defaut['categories']['category']),count($defaut['dataset'][2010]['set']),"defaut / Le nombre de données à afficher en abscisse n'est pas correct");
		
		//test du contenu
		$this->assertEquals("Mars",$defaut['categories']['category']['2']['label'],"defaut / Le nom des catégories n'est pas correct");
		$this->assertEquals("Affaires",$defaut['params']['caption'],"defaut / Le nom des légendes n'est pas correct");
		$this->assertEquals("Nombre de créations par mois",$defaut['params']['subCaption'],"defaut / Le nom des sous légendes n'est pas correct");
		$this->assertEquals("38",$defaut['dataset'][2010]['set']["04"]['value'],'defaut / 1/ Les valeurs ne sont pas correctes');
		$this->assertEquals("2010 : 31",$defaut['dataset'][2010]['set']["09"]['titre'],'defaut / 2/ Les valeurs ne sont pas correctes');
		$this->assertEquals("affaire.html%2Cstats%3D1%26annee%3D2010%26mois%3D12%26label%3D%26type%3Dlol",$defaut['dataset'][2010]['set']["12"]['link'],'defaut / 3/ Les valeurs ne sont pas correctes');
	
	}
	
	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function testSelect_all(){
		$this->obj->q->reset()->addCondition("`affaire`.`id_affaire`",$this->id_affaire)->end();
		$affaire=$this->obj->select_all();
		$this->assertEquals("150.00",$affaire[0]["marge"],"select_all ne renvoie pas la bonne marge");
		$this->assertEquals("0.75",$affaire[0]["pourcent"],"select_all ne renvoie pas le bon pourcent");
		$this->assertEquals($this->id_affaire,$affaire[0]["affaire.id_affaire"],"select_all ne renvoie pas le bon id_affaire");
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function testForecast(){
		$forecast=$this->obj->forecast();
		$this->obj->q->reset()
					->addJointure("affaire","id_affaire","devis","id_affaire")
					->addField("SUM(`devis`.`prix`*(`affaire`.`forecast`/100))","ca")
					->addField("SUM((`devis`.`prix`-`devis`.`prix_achat`)*(`affaire`.`forecast`/100))","marge")
					->addCondition("affaire.etat",'devis')
					->setDimension("row");
		$forecast2 = $this->obj->sa();
		$this->assertEquals($forecast2,$forecast,"forecast ne renvoie pas le bon array");
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function testUpdate_termes(){
		$affaire=$this->obj->select($this->id_affaire);
		$this->assertEquals("",$affaire["id_termes"],"Il ne devrait pas y avoir de termes");

		$affaire["id_termes"]=1;
		$update_termes=$this->obj->update_termes($affaire);
		$this->assertEquals(array(0=>array("msg"=>"Modification de l'enregistrement 'Tu_devis' effectuée avec succès.","title"=>"Succès !","timer"=>"")),ATF::$msg->getNotices(),"1 La notice de modification fonctionne bien");
		$affaire=$this->obj->select($this->id_affaire);
		$this->assertEquals("1",$affaire["id_termes"],"update_termes ne fonctionne pas");
		$this->assertTrue($update_termes,"update_termes ne renvoie pas true");

		$update_termes=$this->obj->update_termes($affaire);
		$this->assertFalse($update_termes,"update_termes ne renvoie pas false");
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function testUpdate_forecast(){
		$affaire=$this->obj->select($this->id_affaire);
		$affaire["forecast"]="80";
		$update_forecast=$this->obj->update_forecast($affaire);
		$this->assertEquals(array(0=>array("msg"=>"Modification de l'enregistrement 'Tu_devis' effectuée avec succès.","title"=>"Succès !","timer"=>"")),ATF::$msg->getNotices(),"1 La notice de modification fonctionne bien");
		$affaire=$this->obj->select($this->id_affaire);
		$this->assertEquals("80",$affaire["forecast"],"update_forecast ne fonctionne pas");
		$this->assertTrue($update_forecast,"update_forecast ne renvoie pas true");

		$update_termes=$this->obj->update_forecast($affaire);
		$this->assertFalse($update_termes,"update_forecast ne renvoie pas false");
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function testCan_delete(){
	
		try {
			$can_delete=$this->obj->can_delete($this->id_affaire);
		} catch (errorATF $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(892,$error,"can_delete ne doit pas pouvoir suprimer une affaire s'il y a une facture une commande ou un devis");


		ATF::facture()->d($this->id_facture);
		try {
			$can_delete=$this->obj->can_delete($this->id_affaire);
		} catch (errorATF $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(892,$error,"can_delete ne doit pas pouvoir suprimer une affaire s'il y a une commande ou un devis");

		ATF::commande()->d($this->id_commande);
		try {
			$can_delete=$this->obj->can_delete($this->id_affaire);
		} catch (errorATF $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(892,$error,"can_delete ne doit pas pouvoir suprimer une affaire s'il y a un devis");

		ATF::devis()->d($this->id_devis);
		$can_delete=$this->obj->can_delete($this->id_affaire);
		$this->assertTrue($can_delete,"can_delete doit pouvoir suprimer une affaire s'iln'y a pas de devis ni de commande ni de facture");
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function testAutocompleteHotlineForm(){
		$infos["query"]="Tu_devis_AutocompleteHotlineForm";
		$autocompleteHotlineForm=$this->obj->autocompleteHotlineForm($infos);
		$this->assertNotNull($autocompleteHotlineForm,"autocompleteHotlineForm ne renvoie pas une affaire existante");
		$this->obj->u(array("id_affaire"=>$this->id_affaire,"etat"=>"perdue"));
		$autocompleteHotlineForm=$this->obj->autocompleteHotlineForm($infos);
		$this->assertNull($autocompleteHotlineForm[0],"autocompleteHotlineForm ne devrait pas renvoyer une affaire perdue");
	}
	
	// @uathor Yann GAUTHERON <ygautheron@absystech.fr>
	function test_saFilter(){
		// FIltrage général par profil
		ATF::$usr->set('id_profil',11);
		ATF::affaire()->select_all();
		$this->assertEquals(array("filtreGeneral"=>"affaire.id_commercial = '".ATF::$usr->getID()."'"),ATF::affaire()->q->getWhere(),"Filtrage general sur affaire invailde");
	}
	
	// @uathor Yann GAUTHERON <ygautheron@absystech.fr>
	function test_canUpdate(){

		try {
			ATF::affaire()->can_update();
		} catch (errorATF $e) {
			$error = $e->getCode();
		}

		$this->assertEquals(892,$error,"Can update devrait etre faux");
		$i = array(
			"id_termes"=>1
			,"second"=>1
		);
		$this->assertTrue(ATF::affaire()->can_update(NULL,$i),"Can update invalide");
	}
		
	/*@author Nicolas BERTEMONT <nbertemont@absystech.fr> */ 
	function testGetMargeTotaleDepuisDebutAnnee(){
		$c_affaire_att=new affaire_att();
		
		$getMargeTotaleDepuisDebutAnnee=$c_affaire_att->getMargeTotaleDepuisDebutAnnee();
		$this->assertNotNull($getMargeTotaleDepuisDebutAnnee,"getMargeTotaleDepuisDebutAnnee ne renvoi pas de chiffre");

		$this->assertNotNull($c_affaire_att->getMargeTotaleDepuisDebutAnnee(0,3),"2/ getMargeTotaleDepuisDebutAnnee ne renvoi pas de chiffre");

		$this->assertNotNull($c_affaire_att->getMargeTotaleDepuisDebutAnnee(-1,10),"getMargeTotaleDepuisDebutAnnee ne renvoi pas de chiffre pour l'année précédente");

		$getMargeTotaleDepuisDebutAnnee1=$c_affaire_att->getMargeTotaleDepuisDebutAnnee(-1);
		$this->assertNotEquals($getMargeTotaleDepuisDebutAnnee,$getMargeTotaleDepuisDebutAnnee1,"getMargeTotaleDepuisDebutAnnee ne doit pas être égale à  getMargeTotaleDepuisDebutAnnee -1");
	}
		
	/*@author Quentin JANON <qjanon@absystech.fr> */ 
	function test_setForecast(){
		$u = array("id_affaire"=>$this->id_affaire,"forecast"=>"180");
		$erreur = false;
		try {
			$this->obj->setForecast($u);
		} catch (errorATF $e) {
			$erreur = true;
		}
		$this->assertTrue($erreur,"Erreur non remontée");
		$this->assertEquals(6512,$e->getErrno(),"Mauvais code d'erreur");
				
		$u = array("id_affaire"=>$this->id_affaire,"forecast"=>"80");
		$this->obj->setForecast($u);
		$this->assertEquals("80",$this->obj->select($this->id_affaire,"forecast"),"Erreur le forecast n'est pas a 80...");
		
	}
	
	/*@author Quentin JANON <qjanon@absystech.fr>  */
	public function test_getRefNoDate(){
		$erreur = false;
		try {
			$this->obj->getRef(NULL,"facture");	
		} catch (errorATF $e) {
			$erreur = true;
		}
		$this->assertTrue($erreur,"Erreur non catché !");
		$this->assertEquals(321, $e->getErrno(),"Mauvais code d'erreur !");
	}
	
	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
	public function test_getRef(){
		ATF::$usr->set('id_agence',1);
		
		$this->assertEquals("DLI00010001",
							$this->obj->getRef("2000-01-01","devis"),
							"getRef pas 0");	

		$this->assertEquals("DLI05080002",
							$this->obj->getRef("2005-08-09","devis"),
							"getRef pas 1");	

		$this->assertEquals("DLI08030063",
							$this->obj->getRef("2008-03-29","devis"),
							"getRef pas +10");	
		
		ATF::devis()->u(array("id_devis"=>702,"ref"=>"DLI05080100"));
		$this->assertEquals("DLI05080101",
							$this->obj->getRef("2005-08-09","devis"),
							"getRef pas 100");	
		
		ATF::devis()->u(array("id_devis"=>702,"ref"=>"DLI05081000"));
		$this->assertEquals("DLI05081001",
							$this->obj->getRef("2005-08-09","devis"),
							"getRef pas 1000");	

	}

	/*@author Quentin JANON <qjanon@absystech.fr>  */
	public function test_getRefATTNoDate(){
		$o = new affaire_att();
		$erreur = false;
		try {
			$o->getRef(NULL,"facture");	
		} catch (errorATF $e) {
			$erreur = true;
		}
		$this->assertTrue($erreur,"Erreur non catché !");
		$this->assertEquals(321, $e->getErrno(),"Mauvais code d'erreur !");
	}


	/*@author Yann GAUTHERON <ygautheron@absystech.fr>  */
	public function test_getRefATT(){
		ATF::$usr->set('id_agence',1);
		$o = new affaire_att();
		$this->assertEquals("ADLI00010001",
							$o->getRef("2000-01-01","devis"),
							"getRef pas 0");	

		$this->assertEquals("ADLI05080001",
							$o->getRef("2005-08-09","devis"),
							"getRef pas 1");	

		$this->assertEquals("ADLI08030001",
							$o->getRef("2008-03-29","devis"),
							"getRef pas +10");	
		
		ATF::devis()->u(array("id_devis"=>702,"ref"=>"ADLI05080100"));
		$this->assertEquals("ADLI05080101",
							$o->getRef("2005-08-09","devis"),
							"getRef pas 100");	
		
		ATF::devis()->u(array("id_devis"=>702,"ref"=>"ADLI05011000"));
		$this->assertEquals("ACLI05080001",
							$o->getRef("2005-08-09","commande"),
							"getRef pas 1000");	
		$this->assertEquals("AFLI05080001",
							$o->getRef("2005-08-09","facture"),
							"getRef pas 100");	
							
		$this->assertEquals("LI05080001",$o->getRef("2005-08-09","mockObject1"),"mock pas 1");	
		$this->assertEquals("LI05080011",$o->getRef("2005-08-09","mockObject10"),"mock pas 10");	
		$this->assertEquals("LI05080101",$o->getRef("2005-08-09","mockObject100"),"mock pas 100");	
		$this->assertEquals("LI05081001",$o->getRef("2005-08-09","mockObject1000"),"mock pas 1000");	

	}

	/*@author Quentin JANON <qjanon@absystech.fr>  */
	public function test_updateOnSelectSociete(){
		ATF::societe()->q->reset()->addField("id_societe")->where("societe","Absystech");
		$id_new_societe = ATF::societe()->select_cell();
		$infos = array("key"=>"id_societe","value"=>"Nouvelle société","id_value"=>ATF::societe()->cryptId($id_new_societe),"id"=>$this->id_affaire);
		$id_suivi = $this->obj->updateOnSelect($infos);
		
		$this->assertEquals($id_new_societe,ATF::affaire()->select($this->id_affaire,"id_societe"),"Erreur dans le changement de la société de l'affaire");
		$this->assertEquals($id_new_societe,ATF::devis()->select($this->id_devis,"id_societe"),"Erreur dans le changement de la société de l'devis");
		$this->assertEquals($id_new_societe,ATF::commande()->select($this->id_commande,"id_societe"),"Erreur dans le changement de la société de l'commande");
		$this->assertEquals($id_new_societe,ATF::facture()->select($this->id_facture,"id_societe"),"Erreur dans le changement de la société de l'facture");
		
		$suivi = ATF::suivi()->select($id_suivi);
		$this->assertEquals("Changement de société de l'affaire.\nAncienne société : '".ATF::societe()->nom($this->id_societe)."'.\nNouvelle société : 'AbsysTech'",$suivi['texte'],"Erreur dans le texte du suivi automatique");
		$this->assertEquals($id_new_societe,$suivi['id_societe'],"Erreur dans la société du suivi automatique");
		$this->assertEquals($this->id_affaire,$suivi['id_affaire'],"Erreur dans l'affaire  du suivi automatique");
	}

	/*@author Quentin JANON <qjanon@absystech.fr>  */
	public function test_updateOnSelectERROR(){
		$infos = array("key"=>"id_societe","value"=>"Nouvelle société","id_value"=>ATF::societe()->cryptId($id_new_societe),"id"=>$this->id_affaire);
		$erreur = false;
		try {
			$id_suivi = $this->obj->updateOnSelect($infos);
		} catch (errorATF $e) {
			$erreur = true;
		}
		
		$this->assertTrue($erreur,"Pas d'erreur remonté, donc pas de rollback :/");
	}

	/*@author Quentin JANON <qjanon@absystech.fr>  */
	public function test_getAllForMenu(){

		$infos['id_societe'] = $this->id_societe;
		$r = $this->obj->getAllForMenu($infos);

		$r2 = json_decode($r,true);

		$devis = ATF::devis()->select($this->id_devis);

		$this->assertEquals(1,count($r2),"Problème dans le relevé du nom");
		$this->assertEquals($devis['ref'].' - '.$devis['resume'],$r2[0]['text'],"Problème dans le relevé du nom");
		$this->assertEquals($devis['id_affaire'],ATF::affaire()->decryptId($r2[0]['id']),"Problème dans le relevé du id_affaire");
		$this->assertEquals("smallIconFacture",$r2[0]['iconCls'],"Problème dans le relevé du iconCls");
	}


};
 
class mockObject1 extends classes_optima {
	function sa() {
		return array("max_ref"=>1);
	}
}

class mockObject10 extends classes_optima {
	function sa() {
		return array("max_ref"=>11);
	}
}

class mockObject100 extends classes_optima {
	function sa() {
		return array("max_ref"=>101);
	}
}

class mockObject1000 extends classes_optima {
	function sa() {
		return array("max_ref"=>1001);
	}
}
?>