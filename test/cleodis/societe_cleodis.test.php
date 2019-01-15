<?
/**
* Classe de test sur le module societe_cleodis
*/
class societe_cleodis_test extends ATF_PHPUnit_Framework_TestCase {
	/** Méthode pré-test, exécute avant chaque test unitaire
	* besoin d'un user pour les traduction
	*/
	public function setUp() {
		ATF::db()->begin_transaction(true);
	}

	/** Méthode post-test, exécute après chaque test unitaire*/

	public function tearDown(){
		ATF::db()->rollback_transaction(true);
		ATF::$msg->getNotices();
	}

	public function test_getOpca(){
		$res = $this->obj->getOpca();
		$this->assertNotNull($res, "Get Opca vide ??");
	}

	/*@author Yann GAUTHERON <ygautheron@absystech.fr>
	  @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test_insert(){
		$this->initUser();
		$this->obj = ATF::societe();
		$this->assertEquals(get_class($this->obj),'societe_cleodis','Classe dans $this->obj incorrecte');

		$societe = array(
			'societe'=>'Test cleodis TU',
			"siret" => "123456789"
		);
		$id=$this->obj->insert(array('societe'=>$societe));
		$this->assertEquals($this->obj->select($id,'societe'),$societe['societe'],'Stockage de la societe incorrect');

		$societe["siret"] = "45307981600048";
		try {
			$this->obj->insert(array('societe'=>$societe));
		} catch (errorATF $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(878,$error,"Une société existe déja avec le SIRET ");
	}

	/*
	  @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test_insert_RUM_soc(){
		$this->initUser();
		$this->obj = ATF::societe();
		$this->assertEquals(get_class($this->obj),'societe_cleodis','Classe dans $this->obj incorrecte');

		$prefixRum = $this->obj->create_rum();

		$societe = array(
			'societe'=>'Test cleodis TU',
			"siret" => "123456789",
			"code_client" => "123456"
		);
		$id=$this->obj->insert(array('societe'=>$societe));
		$this->assertSame($prefixRum."123456",$this->obj->select($id,'RUM'),'Stockage du RUM incorrect 1');

		$this->obj->d(array("id_societe"=>$id));
		$societe = array(
			'societe'=>'Test cleodis TU',
			"siret" => "123456789",
			"code_client" => "123456111"
		);
		$id=$this->obj->insert(array('societe'=>$societe));
		$this->assertSame($prefixRum."456111",$this->obj->select($id,'RUM'),'Stockage du RUM incorrect 2');

		$this->obj->d(array("id_societe"=>$id));
		$societe = array(
			'societe'=>'Test cleodis TU',
			"siret" => "123456789",
			"code_client" => "123"
		);
		$id=$this->obj->insert(array('societe'=>$societe));
		$this->assertSame($prefixRum."000123",$this->obj->select($id,'RUM'),'Stockage du RUM incorrect 3');
	}

	/*
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test_update(){
		$this->initUser();
		$this->obj = ATF::societe();
		$this->assertEquals(get_class($this->obj),'societe_cleodis','Classe dans $this->obj incorrecte');

		$societe = array('societe'=>'Test cleodis TU',"siret" => "123456789");
		$id=$this->obj->insert(array('societe'=>$societe));

		$societe2 = array('societe'=>'Test cleodis TU 2',"siret" => "1123456789");
		$id2=$this->obj->insert(array('societe'=>$societe2));

		try{
			$this->obj->update(array("societe"=>array("id_societe"=>$id2, "siret"=>"123456789")));
		}catch(errorATF $e){
			$error = $e->getCode();
			$errorM = $e->getMessage();
		}
		$this->assertSame("Une société existe déja avec le SIRET 123456789", $errorM, "Message erreur incorrect ?");
		$this->assertSame(878, $error, "Erreur incorrecte ?");

	}


	/*
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test_updateFoyer(){
		$this->initUser();
		$this->obj = ATF::societe();
		$this->assertEquals(get_class($this->obj),'societe_cleodis','Classe dans $this->obj incorrecte');

		$societe = array('societe'=>'Test cleodis TU',"siret" => "123456789");
		$id=$this->obj->insert(array('societe'=>$societe));

		try{
			$this->obj->update(array('label_societe'=>array('famille'=>'Foyer'),
									 "societe"=>array("id_societe"=>$id, "particulier_nom"=>"toto")));
		}catch(errorATF $e){
			$error = $e->getCode();
			$errorM = $e->getMessage();
		}
		$this->assertSame("Le champs civilite est obligatoire pour un particulier!", $errorM, "Message erreur incorrect ?");
		$this->assertSame(878, $error, "Erreur incorrecte ?");


		try{
			$this->obj->update(array('label_societe'=>array('famille'=>'Foyer'),"societe"=>array("id_societe"=>$id, "particulier_civilite"=>"M")));
		}catch(errorATF $e){
			$error = $e->getCode();
			$errorM = $e->getMessage();
		}
		$this->assertSame("Le champs nom est obligatoire pour un particulier!", $errorM, "Message erreur incorrect ?");
		$this->assertSame(878, $error, "Erreur incorrecte ?");


		try{
			$this->obj->update(array('label_societe'=>array('famille'=>'Foyer'),"societe"=>array("id_societe"=>$id, "particulier_civilite"=>"M", "particulier_nom"=>"toto")));
		}catch(errorATF $e){
			$error = $e->getCode();
			$errorM = $e->getMessage();
		}
		$this->assertSame("Le champs prenom est obligatoire pour un particulier!", $errorM, "Message erreur incorrect ?");
		$this->assertSame(878, $error, "Erreur incorrecte ?");

		$this->obj->update(array('label_societe'=>array('famille'=>'Foyer'),"societe"=>array("id_societe"=>$id, "particulier_civilite"=>"M", "particulier_nom"=>"toto", "particulier_prenom"=>"toto")));

		$this->assertSame("M toto toto", $this->obj->select($id, "societe"), "Societe particulier non mis à jour ??");
	}

	/*
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test_insertFoyer(){
		$this->initUser();
		$this->obj = ATF::societe();
		$this->assertEquals(get_class($this->obj),'societe_cleodis','Classe dans $this->obj incorrecte');

		try{
			$this->obj->insert(array('label_societe'=>array('id_famille'=>'Foyer'),
									 "societe"=>array('societe'=>'Test cleodis TU', "particulier_nom"=>"toto")));
		}catch(errorATF $e){
			$error = $e->getCode();
			$errorM = $e->getMessage();
		}
		$this->assertSame("Le champs civilite est obligatoire pour un particulier!", $errorM, "Message erreur incorrect ?");
		$this->assertSame(878, $error, "Erreur incorrecte ?");


		try{
			$this->obj->insert(array('label_societe'=>array('id_famille'=>'Foyer'),"societe"=>array('societe'=>'Test cleodis TU', "particulier_civilite"=>"M")));
		}catch(errorATF $e){
			$error = $e->getCode();
			$errorM = $e->getMessage();
		}
		$this->assertSame("Le champs nom est obligatoire pour un particulier!", $errorM, "Message erreur incorrect ?");
		$this->assertSame(878, $error, "Erreur incorrecte ?");


		try{
			$this->obj->insert(array('label_societe'=>array('id_famille'=>'Foyer'),"societe"=>array('societe'=>'Test cleodis TU', "particulier_civilite"=>"M", "particulier_nom"=>"toto")));
		}catch(errorATF $e){
			$error = $e->getCode();
			$errorM = $e->getMessage();
		}
		$this->assertSame("Le champs prenom est obligatoire pour un particulier!", $errorM, "Message erreur incorrect ?");
		$this->assertSame(878, $error, "Erreur incorrecte ?");

		$id = $this->obj->insert(array('label_societe'=>array('id_famille'=>'Foyer'),"societe"=>array('societe'=>'Test cleodis TU', "particulier_civilite"=>"M", "particulier_nom"=>"toto", "particulier_prenom"=>"toto")));

		$this->assertSame("M toto toto", $this->obj->select($id, "societe"), "Societe particulier non mis à jour ??");
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */
	function testAutocompleteFournisseursDeCommande(){
		$infos["query"]="PARA OPTIC SARL";
		$autocompleteFournisseursDeCommande=$this->obj->autocompleteFournisseursDeCommande($infos);

		$this->assertEquals(547,$autocompleteFournisseursDeCommande[0]["2"],"autocompleteFournisseursDeCommande ne renvoie pas le bon ID");
		$this->assertEquals('<span class="searchSelectionFound">PARA</span> <span class="searchSelectionFound">OPTIC</span> <span class="searchSelectionFound">SARL</span>',$autocompleteFournisseursDeCommande[0]["3"],"autocompleteFournisseursDeCommande ne renvoie pas le bon span");
		$this->assertEquals(547,$autocompleteFournisseursDeCommande[0]["raw_2"],"autocompleteFournisseursDeCommande ne renvoie pas le bon ID");
		$this->assertEquals("PARA OPTIC SARL",$autocompleteFournisseursDeCommande[0]["raw_3"],"autocompleteFournisseursDeCommande ne renvoie pas le bon fournisseur");
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */
	function testAutocompleteAvecAdresse(){
		$infos["query"]="PARA OPTIC SARL";
		$autocompleteAvecAdresse=$this->obj->autocompleteAvecAdresse($infos);
		$this->assertEquals("Centre Commercial GEANT",$autocompleteAvecAdresse[0]["0"],"autocompleteAvecAdresse ne renvoie pas le bon adresse");
		$this->assertEquals("ALBI",$autocompleteAvecAdresse[0]["1"],"autocompleteAvecAdresse ne renvoie pas le bon ville");
		$this->assertEquals("81000",$autocompleteAvecAdresse[0]["2"],"autocompleteAvecAdresse ne renvoie pas le bon cp");
		$this->assertEquals(547,$autocompleteAvecAdresse[0]["3"],"autocompleteAvecAdresse ne renvoie pas le bon id");
		$this->assertEquals('<span class="searchSelectionFound">PARA</span> <span class="searchSelectionFound">OPTIC</span> <span class="searchSelectionFound">SARL</span>',$autocompleteAvecAdresse[0]["4"],"autocompleteAvecAdresse ne renvoie pas le bon span");

		$this->assertEquals("Centre Commercial GEANT",$autocompleteAvecAdresse[0]["raw_0"],"autocompleteAvecAdresse ne renvoie pas le bon adresse");
		$this->assertEquals("ALBI",$autocompleteAvecAdresse[0]["raw_1"],"autocompleteAvecAdresse ne renvoie pas le bon ville");
		$this->assertEquals("81000",$autocompleteAvecAdresse[0]["raw_2"],"autocompleteAvecAdresse ne renvoie pas le bon cp");
		$this->assertEquals(547,$autocompleteAvecAdresse[0]["raw_3"],"autocompleteAvecAdresse ne renvoie pas le bon id");
		$this->assertEquals('PARA OPTIC SARL',$autocompleteAvecAdresse[0]["raw_4"],"autocompleteAvecAdresse ne renvoie pas le bon span");
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */
	function testAutocompleteAvecFiliale(){
		$infos["query"]="EQUIPEMENT";
		$autocompleteAvecFiliale=$this->obj->autocompleteAvecFiliale($infos);

		$this->assertEquals("4161",$autocompleteAvecFiliale[0]["1"],"autocompleteAvecFiliale ne renvoie pas le bon id");
		$this->assertEquals('CLEODIS <span class="searchSelectionFound">EQUIPEMENT</span>',$autocompleteAvecFiliale[0]["2"],"autocompleteAvecFiliale ne renvoie pas le bon societe");

		$this->assertEquals("1.200",$autocompleteAvecFiliale[0]["raw_0"],"autocompleteAvecFiliale ne renvoie pas le bon tva");
		$this->assertEquals("4161",$autocompleteAvecFiliale[0]["raw_1"],"autocompleteAvecFiliale ne renvoie pas le bon id");
		$this->assertEquals("CLEODIS EQUIPEMENT",$autocompleteAvecFiliale[0]["raw_2"],"autocompleteAvecFiliale ne renvoie pas le bon societe");
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */
	function test_formateGetParc(){
		ATF::db()->rollback_transaction(true);
		$this->initUser();

		$id_affaire1=ATF::affaire()->i(array("ref"=>"refTu1","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu1"));
		$id_affaire2=ATF::affaire()->i(array("ref"=>"refTu2","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu2","etat"=>"perdue"));
		$id_affaire3=ATF::affaire()->i(array("ref"=>"refTu3","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu3"));
		$id_affaire4=ATF::affaire()->i(array("ref"=>"refTu4","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu4"));

		//Seul id_parc1 doit être dans getParc1 car : id_parc2 est inactif, id_parc4 est inactif, id_parc3 est actif mais en attente
		$id_parc1=ATF::parc()->i(array("id_affaire"=>$id_affaire1,"libelle"=>"parc tu1","serial"=>"parc tu1","etat"=>"loue","existence"=>"actif"));
		$id_parc2=ATF::parc()->i(array("id_affaire"=>$id_affaire2,"libelle"=>"parc tu1","serial"=>"parc tu1","etat"=>"loue","existence"=>"actif","provenance"=>$id_affaire1));
		$id_parc3=ATF::parc()->i(array("id_affaire"=>$id_affaire1,"libelle"=>"parc tu2","serial"=>"parc tu2","etat"=>"loue","existence"=>"actif"));
		$id_parc4=ATF::parc()->i(array("id_affaire"=>$id_affaire3,"libelle"=>"parc tu2","serial"=>"parc tu2","etat"=>"loue","existence"=>"actif","provenance"=>$id_affaire1));

		$id_affaire_courante=$id_affaire4;

		$this->assertFalse($this->obj->formateGetParc(false,$item,$id_affaire_courante),"formateGetParc ne doit rien retourner s'il n'y a pas de parc");

		unset($parc);
		$parc[]=ATF::parc()->select($id_parc1);
		$item["affaire.id_affaire_fk"]=$id_affaire1;
		$item["affaire.affaire"]="AffaireTu1";
		$item["affaire.ref"]="refTu1";
		$formateGetParc1=$this->obj->formateGetParc($parc,$item,$id_affaire_courante);
		$this->assertEquals("refTu1 AffaireTu1",
							$formateGetParc1["text"],
							"formateGetParc1 ne renvoie pas le bon text");

		$this->assertEquals("affaire_".$id_affaire1,
							$formateGetParc1["id"],
							"formateGetParc1 ne renvoie pas le bon id");

		$this->assertEquals(NULL,
							$formateGetParc1["leaf"],
							"formateGetParc1 ne renvoie pas le bon leaf");

		$this->assertEquals("javascript:window.open('affaire-select-".ATF::affaire()->cryptId($id_affaire1).".html');",
							$formateGetParc1["href"],
							"formateGetParc1 ne renvoie pas le bon href");

		$this->assertEquals("folder",
							$formateGetParc1["cls"],
							"formateGetParc1 ne renvoie pas le bon cls");

		$this->assertEquals(NULL,
							$formateGetParc1["expanded"],
							"formateGetParc1 ne renvoie pas le bon expanded");

		$this->assertEquals(NULL,
							$formateGetParc1[0]["adapter"],
							"formateGetParc1 ne renvoie pas le bon adapter");

		$this->assertEquals("parc tu1  (parc tu1) - 'loue'",
							$formateGetParc1["children"][0]["text"],
							"formateGetParc1 ne renvoie pas le bon children text");

		$this->assertEquals("parc_".$id_parc1,
							$formateGetParc1["children"][0]["id"],
							"formateGetParc1 ne renvoie pas le bon children id");

		$this->assertEquals(true,
							$formateGetParc1["children"][0]["leaf"],
							"formateGetParc1 ne renvoie pas le bon children leaf");

		$this->assertEquals("http://dev.static.absystech.net/images/blank.gif",
							$formateGetParc1["children"][0]["icon"],
							"formateGetParc1 ne renvoie pas le bon children icon");

		$this->assertEquals(NULL,
							$formateGetParc1["children"][0]["checked"],
							"formateGetParc1 ne renvoie pas le bon children checked");

		$this->assertEquals(NULL,
							$formateGetParc1[0]["children"][0]["id_produit_fk"],
							"formateGetParc1 ne renvoie pas le bon children id_produit_fk");

		$this->assertEquals("parc tu1",
							$formateGetParc1["children"][0]["produit"],
							"formateGetParc1 ne renvoie pas le bon children produit");

		$this->assertEquals(NULL,
							$formateGetParc1["children"][0]["ref"],
							"formateGetParc1 ne renvoie pas le bon children ref");

		$this->assertEquals(NULL,
							$formateGetParc1["children"][0]["type"],
							"formateGetParc1 ne renvoie pas le bon children type");

		$this->assertEquals("parc tu1",
							$formateGetParc1["children"][0]["serial"],
							"formateGetParc1 ne renvoie pas le bon children serial");

		$this->assertEquals("1",
							$formateGetParc1["children"][0]["quantite"],
							"formateGetParc1 ne renvoie pas le bon children quantite");

		$this->assertEquals("visible",
							$formateGetParc1["children"][0]["visibilite_prix"],
							"formateGetParc1 ne renvoie pas le bon children visibilite_prix");

		$this->assertEquals($id_affaire1,
							$formateGetParc1["children"][0]["id_affaire_provenance"],
							"formateGetParc1 ne renvoie pas le bon children id_affaire_provenance");

		$this->assertEquals($id_parc1,
							$formateGetParc1["children"][0]["id_parc"],
							"formateGetParc1 ne renvoie pas le bon children id_parc");

		$this->assertEquals(1,
							count($formateGetParc1["children"]),
							"formateGetParc1 ne devrait renvoyer qu'un parc");



		unset($parc);
		$parc[]=ATF::parc()->select($id_parc2);
		$item["affaire.id_affaire_fk"]=$id_affaire2;
		$item["affaire.affaire"]="AffaireTu2";
		$item["affaire.ref"]="refTu2";
		$formateGetParc2=$this->obj->formateGetParc($parc,$item,$id_affaire_courante);
		$this->assertEquals("refTu2 AffaireTu2",
							$formateGetParc2["text"],
							"formateGetParc2 ne renvoie pas le bon text");

		$this->assertEquals("affaire_".$id_affaire2,
							$formateGetParc2["id"],
							"formateGetParc2 ne renvoie pas le bon id");

		$this->assertEquals("parc tu1  (parc tu1) - 'loue' - Parc provenant de l'affaire AffaireTu2 (refTu2)",
							$formateGetParc2["children"][0]["text"],
							"formateGetParc2 ne renvoie pas le bon children text");

		$this->assertEquals("parc_".$id_parc2,
							$formateGetParc2["children"][0]["id"],
							"formateGetParc2 ne renvoie pas le bon children id");

		$this->assertEquals("parc tu1",
							$formateGetParc2["children"][0]["produit"],
							"formateGetParc2 ne renvoie pas le bon children produit");

		$this->assertEquals("parc tu1",
							$formateGetParc2["children"][0]["serial"],
							"formateGetParc2 ne renvoie pas le bon children serial");

		$this->assertEquals($id_affaire2,
							$formateGetParc2["children"][0]["id_affaire_provenance"],
							"formateGetParc2 ne renvoie pas le bon children id_affaire_provenance");

		$this->assertEquals($id_parc2,
							$formateGetParc2["children"][0]["id_parc"],
							"formateGetParc2 ne renvoie pas le bon children id_parc");

		$this->assertEquals(1,
							count($formateGetParc2["children"]),
							"formateGetParc2 ne devrait renvoyer qu'un parc");


		unset($parc);
		$parc[]=ATF::parc()->select($id_parc3);
		$item["affaire.id_affaire_fk"]=$id_affaire1;
		$item["affaire.affaire"]="AffaireTu1";
		$item["affaire.ref"]="refTu1";
		$formateGetParc3=$this->obj->formateGetParc($parc,$item,$id_affaire_courante);
		$this->assertEquals(NULL,
							$formateGetParc3,
							"formateGetParc3 ne doit rien retourner car le parc 3 est repris par le parc 4");

		unset($parc);
		$parc[]=ATF::parc()->select($id_parc4);
		$item["affaire.id_affaire_fk"]=$id_affaire3;
		$item["affaire.affaire"]="AffaireTu3";
		$item["affaire.ref"]="refTu3";
		$formateGetParc4=$this->obj->formateGetParc($parc,$item,$id_affaire_courante);
		$this->assertEquals("refTu3 AffaireTu3",
							$formateGetParc4["text"],
							"formateGetParc4 ne renvoie pas le bon text");

		$this->assertEquals("affaire_".$id_affaire3,
							$formateGetParc4["id"],
							"formateGetParc4 ne renvoie pas le bon id");

		$this->assertEquals("parc tu2  (parc tu2) - 'loue' - Parc provenant de l'affaire AffaireTu3 (refTu3)",
							$formateGetParc4["children"][0]["text"],
							"formateGetParc4 ne renvoie pas le bon children text");

		$this->assertEquals("parc_".$id_parc4,
							$formateGetParc4["children"][0]["id"],
							"formateGetParc4 ne renvoie pas le bon children id");

		$this->assertEquals("parc tu2",
							$formateGetParc4["children"][0]["produit"],
							"formateGetParc4 ne renvoie pas le bon children produit");

		$this->assertEquals("parc tu2",
							$formateGetParc4["children"][0]["serial"],
							"formateGetParc4 ne renvoie pas le bon children serial");

		$this->assertEquals($id_affaire3,
							$formateGetParc4["children"][0]["id_affaire_provenance"],
							"formateGetParc4 ne renvoie pas le bon children id_affaire_provenance");

		$this->assertEquals($id_parc4,
							$formateGetParc4["children"][0]["id_parc"],
							"formateGetParc4 ne renvoie pas le bon children id_parc");

		$this->assertEquals(1,
							count($formateGetParc4["children"]),
							"formateGetParc4 ne devrait renvoyer qu'un parc");

	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */
	function test_getParc(){
		ATF::db()->rollback_transaction(true);
		$this->initUser();

		$id_affaire1=ATF::affaire()->i(array("ref"=>"refTu1","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu1"));
		$id_commande1=ATF::commande()->i(array("ref"=>"refTu1","id_societe"=>$this->id_societe,"id_user"=>$this->id_user,"tva"=>"1,196","id_affaire"=>$id_affaire1,"etat"=>"non_loyer"));

		//Seul id_parc1 doit être dans getParc1 car : id_parc2 est inactif, id_parc4 est inactif, id_parc3 est actif mais en attente
		$id_parc1=ATF::parc()->i(array("id_affaire"=>$id_affaire1,"libelle"=>"parc tu1","serial"=>"parc tu1","etat"=>"loue","existence"=>"actif"));
		$id_parc2=ATF::parc()->i(array("id_affaire"=>$id_affaire1,"libelle"=>"parc tu2","serial"=>"parc tu2","etat"=>"reloue","existence"=>"actif"));
		$id_parc3=ATF::parc()->i(array("id_affaire"=>$id_affaire1,"libelle"=>"parc tu3","serial"=>"parc tu3","etat"=>"broke","existence"=>"actif"));
		$id_parc4=ATF::parc()->i(array("id_affaire"=>$id_affaire1,"libelle"=>"parc tu4","serial"=>"parc tu4","etat"=>"loue","existence"=>"inactif"));
		$id_parc5=ATF::parc()->i(array("id_affaire"=>$id_affaire1,"libelle"=>"parc tu5","serial"=>"parc tu5","etat"=>"reloue","existence"=>"inactif"));

		$infos["id_societe"]=$this->id_societe;
		$getParc1=$this->obj->getParc($infos);
		$getParc1=json_decode($getParc1);

		$this->assertEquals("parc_".$id_parc1,
							$getParc1[0]->children[0]->id,
							"mauvais id_parc 1 1");

		$this->assertEquals("parc_".$id_parc2,
							$getParc1[0]->children[1]->id,
							"mauvais id_parc 1 2");

		$this->assertEquals(2,
							count($getParc1[0]->children),
							"getParc devrait renvoyer 2 parc");

		$id_affaire2=ATF::affaire()->i(array("ref"=>"refTu2","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu2"));
		$id_commande2=ATF::commande()->i(array("ref"=>"refTu2","id_societe"=>$this->id_societe,"id_user"=>$this->id_user,"tva"=>"1,196","id_affaire"=>$id_affaire2,"etat"=>"mis_loyer"));
		$id_parc6=ATF::parc()->i(array("id_affaire"=>$id_affaire2,"libelle"=>"parc tu6","serial"=>"parc tu6","etat"=>"loue","existence"=>"actif"));

		$getParc2=$this->obj->getParc($infos);
		$getParc2=json_decode($getParc2);

		$this->assertEquals("parc_".$id_parc6,
							$getParc2[1]->children[0]->id,
							"mauvais id_parc 2 1");

		$this->assertEquals(1,
							count($getParc2[1]->children),
							"getParc2 devrait renvoyer 1 parc");

		$this->assertEquals(2,
							count($getParc2[0]->children),
							"getParc2 devrait renvoyer 2 parc");

		$id_affaire3=ATF::affaire()->i(array("ref"=>"refTu3","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu3"));
		$id_commande3=ATF::commande()->i(array("ref"=>"refTu3","id_societe"=>$this->id_societe,"id_user"=>$this->id_user,"tva"=>"1,196","id_affaire"=>$id_affaire3,"etat"=>"prolongation"));
		$id_parc7=ATF::parc()->i(array("id_affaire"=>$id_affaire3,"libelle"=>"parc tu7","serial"=>"parc tu7","etat"=>"loue","existence"=>"actif"));

		$getParc3=$this->obj->getParc($infos);
		$getParc3=json_decode($getParc3);
		$this->assertEquals("parc_".$id_parc7,
							$getParc3[2]->children[0]->id,
							"mauvais id_parc 3 1");

		$this->assertEquals(2,
							count($getParc3[0]->children),
							"getParc3 devrait renvoyer 2 parc");

		$this->assertEquals(1,
							count($getParc3[1]->children),
							"getParc3 devrait renvoyer 1 parc");

		$this->assertEquals(1,
							count($getParc3[2]->children),
							"getParc3 devrait renvoyer 1 parc");


		$id_affaire4=ATF::affaire()->i(array("ref"=>"refTu4","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu4","nature"=>"avenant"));
		$id_commande4=ATF::commande()->i(array("ref"=>"refTu4","id_societe"=>$this->id_societe,"id_user"=>$this->id_user,"tva"=>"1,196","id_affaire"=>$id_affaire4,"etat"=>"prolongation"));
		$id_parc8=ATF::parc()->i(array("id_affaire"=>$id_affaire4,"libelle"=>"parc tu8","serial"=>"parc tu8","etat"=>"loue","existence"=>"actif"));

		$getParc4=$this->obj->getParc($infos);
		$getParc4=json_decode($getParc4);

		$this->assertEquals("parc_".$id_parc8,
							$getParc4[3]->children[0]->id,
							"mauvais id_parc 4 1");

		$this->assertEquals(2,
							count($getParc4[0]->children),
							"getParc4 devrait renvoyer 2 parc");

		$this->assertEquals(1,
							count($getParc4[1]->children),
							"getParc4 devrait renvoyer 1 parc");

		$this->assertEquals(1,
							count($getParc4[2]->children),
							"getParc4 devrait renvoyer 1 parc");

		$this->assertEquals(1,
							count($getParc4[3]->children),
							"getParc4 devrait renvoyer 1 parc");

		$infos["type"]="avenant";
		$getParc5=$this->obj->getParc($infos);
		$getParc5=json_decode($getParc5);

		$this->assertEquals(2,
							count($getParc5[0]->children),
							"getParc5 devrait renvoyer 2 parc");

		$this->assertEquals(1,
							count($getParc5[1]->children),
							"getParc5 devrait renvoyer 1 parc");

		$this->assertEquals(1,
							count($getParc5[2]->children),
							"getParc5 devrait renvoyer 1 parc");

	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */
	function test_getParcVente(){
		ATF::db()->rollback_transaction(true);
		$this->initUser();

		$id_affaire1=ATF::affaire()->i(array("ref"=>"refTu1","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu1","nature"=>"AR"));
		$id_commande1=ATF::commande()->i(array("ref"=>"refTu1","id_societe"=>$this->id_societe,"id_user"=>$this->id_user,"tva"=>"1,196","id_affaire"=>$id_affaire1,"etat"=>"non_loyer"));

		$id_parc1=ATF::parc()->i(array("id_affaire"=>$id_affaire1,"libelle"=>"parc tu1","serial"=>"parc tu1","etat"=>"loue","existence"=>"actif"));
		$id_parc2=ATF::parc()->i(array("id_affaire"=>$id_affaire1,"libelle"=>"parc tu2","serial"=>"parc tu2","etat"=>"broke","existence"=>"actif"));
		$id_parc3=ATF::parc()->i(array("id_affaire"=>$id_affaire1,"libelle"=>"parc tu3","serial"=>"parc tu3","etat"=>"broke","existence"=>"inactif"));

		$infos["id_societe"]=$this->id_societe;
		$getParcVente1=$this->obj->getParcVente($infos);
		$getParcVente1=json_decode($getParcVente1);

		$this->assertEquals("parc_".$id_parc2,
							$getParcVente1[0]->children[0]->id,
							"mauvais id_parc 1 1");

		$this->assertEquals(1,
							count($getParcVente1[0]->children),
							"getParc devrait renvoyer 2 parc");


		$id_affaire2=ATF::affaire()->i(array("ref"=>"refTu2","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu2"));
		$id_commande2=ATF::commande()->i(array("ref"=>"refTu2","id_societe"=>$this->id_societe,"id_user"=>$this->id_user,"tva"=>"1,196","id_affaire"=>$id_affaire2,"etat"=>"arreter"));

		$id_parc4=ATF::parc()->i(array("id_affaire"=>$id_affaire2,"libelle"=>"parc tu4","serial"=>"parc tu4","etat"=>"loue","existence"=>"actif"));
		$id_parc5=ATF::parc()->i(array("id_affaire"=>$id_affaire2,"libelle"=>"parc tu5","serial"=>"parc tu5","etat"=>"broke","existence"=>"actif"));
		$id_parc6=ATF::parc()->i(array("id_affaire"=>$id_affaire2,"libelle"=>"parc tu6","serial"=>"parc tu6","etat"=>"broke","existence"=>"inactif"));

		$getParcVente2=$this->obj->getParcVente($infos);
		$getParcVente2=json_decode($getParcVente2);

		$this->assertEquals("parc_".$id_parc4,
							$getParcVente2[1]->children[0]->id,
							"mauvais id_parc 2 1");

		$this->assertEquals("parc_".$id_parc5,
							$getParcVente2[1]->children[1]->id,
							"mauvais id_parc 2 2");

		$this->assertEquals(2,
							count($getParcVente2[1]->children),
							"getParc devrait renvoyer 2 parc");
	}

	public function test_updateScore() {
		// Le initUser est pas dans le set up, du coup on rollback et on appelle le initUser
		ATF::db()->rollback_transaction(true);
		$this->initUser();

		$societe = ATF::societe()->select($this->id_societe);

		$societe['score'] = "7";
		$r = $this->obj->update($societe);

		$this->assertEquals(1,$r,"Nombre de ligne modifié mauvais");

		ATF::suivi()->q->reset()->where("suivi.id_societe",$this->id_societe);
		$suivi = ATF::suivi()->sa();

		$this->assertEquals(1,count($suivi),"Il ne doit y avoir qu'un suivi a ce niveau là");
		$this->assertEquals("La société passe du score 'NC' à '7'",$suivi[0]['texte'],"le message du suivi n'est pas correct");
	}

	public function test_updateAvis() {
		// Le initUser est pas dans le set up, du coup on rollback et on appelle le initUser
		ATF::db()->rollback_transaction(true);
		$this->initUser();

		$societe = ATF::societe()->select($this->id_societe);

		$societe['avis_credit'] = "NR";
		$r = $this->obj->update($societe);

		$this->assertEquals(1,$r,"Nombre de ligne modifié mauvais");

		ATF::suivi()->q->reset()->where("suivi.id_societe",$this->id_societe);
		$suivi = ATF::suivi()->sa();

		$this->assertEquals(1,count($suivi),"Il ne doit y avoir qu'un suivi a ce niveau là");
		$this->assertEquals("La société passe de l'avis crédit 'NC' à '7Ke'",$suivi[0]['texte'],"le message du suivi n'est pas correct");
	}

	/* @author Nicolas BERTEMONT <nbertemont@absystech.fr> */
	public function test_conMidas(){
		$sm=new societe_midas();
		$this->assertEquals('a:5:{s:19:"societe.code_client";a:5:{s:4:"type";s:4:"text";s:5:"xtype";s:9:"textfield";s:9:"maxlength";s:3:"255";s:7:"default";N;s:4:"null";b:1;}s:15:"societe.societe";a:4:{s:4:"type";s:4:"text";s:5:"xtype";s:9:"textfield";s:9:"maxlength";s:3:"128";s:7:"default";N;}s:22:"societe.nom_commercial";a:5:{s:4:"type";s:4:"text";s:5:"xtype";s:9:"textfield";s:9:"maxlength";s:2:"64";s:7:"default";N;s:4:"null";b:1;}s:16:"societe.id_owner";a:5:{s:4:"type";s:3:"int";s:5:"xtype";s:11:"numberfield";s:9:"maxlength";s:1:"8";s:7:"default";N;s:4:"null";b:1;}s:5:"actif";a:2:{s:6:"custom";b:1;s:8:"renderer";s:5:"actif";}}',serialize($sm->colonnes['fields_column']),"Le constructeur de la classe midas a changé");
	}

	/* @author Nicolas BERTEMONT <nbertemont@absystech.fr> */
	public function test_select_allMidas(){
		$sm=new societe_midas();
		$sm->select_all();
		$this->assertEquals("458ff5933c92c0432ba21636aba63591",md5($sm->q->lastSQL),"La requête n'est pas bonne");
	}


	public function test_autocomleteFOurnisseurFormationDevis(){
		$this->environnementFormation();
		$infos = array(  "numero_dossier" => "123456789"
						,"thematique" => "La thématique de la formation"
						,"date" => date("Y-m-d")
						,"nb_heure" => "20"
						,"prix" => "50"
						,"id_owner"=>97
						,"id_contact"=>81
						,"id_societe" => $this->id_societe
						,"contact" => array($this->id_contact1, $this->id_contact2, $this->id_contact3)
						,"type"=>"normal"
				 );
		$dates = array("formation_devis_ligne" => json_encode(array(array("formation_devis_ligne__dot__date"=>"2015-01-31T00:00:00",
																				"formation_devis_ligne__dot__date_deb_matin"=> "8h",
																				"formation_devis_ligne__dot__date_fin_matin"=> "12h30",
																				"formation_devis_ligne__dot__date_deb_am"=> "14h",
																				"formation_devis_ligne__dot__date_fin_am"=> "18h"),
																		  array("formation_devis_ligne__dot__date"=>"2015-01-29T00:00:00",
																		  		"formation_devis_ligne__dot__date_deb_matin"=> "8h",
																				"formation_devis_ligne__dot__date_fin_matin"=> "12h30")
																		)
																),
						"formation_devis_fournisseur" => json_encode(array(array("formation_devis_fournisseur__dot__id_societe_fk"=>"1606",
																				 "formation_devis_fournisseur__dot__type"=>"lieu_formation"),
																				array("formation_devis_fournisseur__dot__id_societe_fk"=>"246",
																					  "formation_devis_fournisseur__dot__type"=>"apporteur_affaire")
																				)
																		)
					);
		$this->id_devis_formation = ATF::formation_devis()->insert(array("formation_devis" => $infos , "values_formation_devis" => $dates));

		$res = $this->obj->autocompleteFournisseurFormationDevis(array("condition_field"=> "formation_devis.id_formation_devis",
																	   "condition_value"=> $this->id_devis_formation,
																	  ),true,true);
		$this->assertEquals(2 , $res["count"], "error");

		$this->obj->q->reset()->setLimit(30);
		$res = $this->obj->autocompleteFournisseurFormationDevis(array("condition_field"=> "formation_devis.id_formation_devis",
																	   "condition_value"=> $this->id_devis_formation,
																	  ),false,false);
		$this->assertEquals("ABSYSTECH", $res[0][1], "error 1");


	}


	public function test_getInfosInvestissement(){
		$investissements = $this->obj->getInfosInvestissement(6207);
		$this->assertNotNull($investissements , "Probleme de retour investissements ??");
	}


	/* @author Morgan FLEURQUIN <mfleurquin@absystech.fr> */
	public function test_conCleodisBE(){
		$sm=new societe_cleodisbe();
		$this->assertEquals('a:8:{s:15:"societe.societe";a:4:{s:4:"type";s:4:"text";s:5:"xtype";s:9:"textfield";s:9:"maxlength";s:3:"128";s:7:"default";N;}s:11:"societe.tel";a:6:{s:4:"type";s:4:"text";s:5:"xtype";s:9:"textfield";s:9:"maxlength";s:2:"20";s:7:"default";N;s:4:"null";b:1;s:3:"tel";b:1;}s:11:"societe.fax";a:6:{s:4:"type";s:4:"text";s:5:"xtype";s:9:"textfield";s:9:"maxlength";s:2:"20";s:7:"default";N;s:4:"null";b:1;s:3:"tel";b:1;}s:13:"societe.email";a:5:{s:4:"type";s:4:"text";s:5:"xtype";s:9:"textfield";s:9:"maxlength";s:3:"255";s:7:"default";N;s:4:"null";b:1;}s:13:"societe.ville";a:5:{s:4:"type";s:4:"text";s:5:"xtype";s:9:"textfield";s:9:"maxlength";s:2:"32";s:7:"default";N;s:4:"null";b:1;}s:22:"societe.nom_commercial";a:5:{s:4:"type";s:4:"text";s:5:"xtype";s:9:"textfield";s:9:"maxlength";s:2:"64";s:7:"default";N;s:4:"null";b:1;}s:19:"societe.code_client";a:5:{s:4:"type";s:4:"text";s:5:"xtype";s:9:"textfield";s:9:"maxlength";s:3:"255";s:7:"default";N;s:4:"null";b:1;}s:4:"logo";a:6:{s:6:"custom";b:1;s:6:"nosort";b:1;s:4:"type";s:4:"file";s:5:"align";s:6:"center";s:5:"width";i:70;s:8:"renderer";s:10:"uploadFile";}}',serialize($sm->colonnes['fields_column']),"Le constructeur de la classe cleodisbe a changé");
	}

	/* @author Morgan FLEURQUIN <mfleurquin@absystech.fr> */
	public function test_conCAP(){
		$sm=new societe_cap();
		$this->assertEquals('a:8:{s:15:"societe.societe";a:4:{s:4:"type";s:4:"text";s:5:"xtype";s:9:"textfield";s:9:"maxlength";s:3:"128";s:7:"default";N;}s:11:"societe.tel";a:6:{s:4:"type";s:4:"text";s:5:"xtype";s:9:"textfield";s:9:"maxlength";s:2:"20";s:7:"default";N;s:4:"null";b:1;s:3:"tel";b:1;}s:11:"societe.fax";a:6:{s:4:"type";s:4:"text";s:5:"xtype";s:9:"textfield";s:9:"maxlength";s:2:"20";s:7:"default";N;s:4:"null";b:1;s:3:"tel";b:1;}s:13:"societe.email";a:5:{s:4:"type";s:4:"text";s:5:"xtype";s:9:"textfield";s:9:"maxlength";s:3:"255";s:7:"default";N;s:4:"null";b:1;}s:13:"societe.ville";a:5:{s:4:"type";s:4:"text";s:5:"xtype";s:9:"textfield";s:9:"maxlength";s:2:"32";s:7:"default";N;s:4:"null";b:1;}s:22:"societe.nom_commercial";a:5:{s:4:"type";s:4:"text";s:5:"xtype";s:9:"textfield";s:9:"maxlength";s:2:"64";s:7:"default";N;s:4:"null";b:1;}s:19:"societe.code_client";a:5:{s:4:"type";s:4:"text";s:5:"xtype";s:9:"textfield";s:9:"maxlength";s:3:"255";s:7:"default";N;s:4:"null";b:1;}s:4:"logo";a:6:{s:6:"custom";b:1;s:6:"nosort";b:1;s:4:"type";s:4:"file";s:5:"align";s:6:"center";s:5:"width";i:70;s:8:"renderer";s:10:"uploadFile";}}',serialize($sm->colonnes['fields_column']),"Le constructeur de la classe cap a changé");
	}


	public function test_envoi_courrier(){
		$sm=new societe_cap();
		$retour = $sm->envoiCourrier(array("table" => "societe",
							"extAction" => "societe",
							"extMethod" => "envoiCourrier",
							"ids" => "c22f051d7470a4ef925baed6a10acf2c|4888feabc0f6c2c7eb0fc97aa480f5de|",
							"societe_source" => "Abjuris"));
		$this->assertTrue($retour , "evoi courrier error");
	}

	public function test_export(){

		$data = array(array(
        	"societe.societe" => "CLEODIS",
        	"societe.adresse" => "144 rue Nationale",
        	"societe.cp" => "59800",
        	"societe.ville" => "LILLE",
        	"societe.siren" => "453079816",
        	"societe.date_creation" => "2004-04-01",
        	"societe.code_client" => "",
        	"societe.siret" => "45307981600048",
        	"societe.tel" => "0328140200",
        	"societe.id_owner" => "Jérôme LOISON",
        	"societe.id_famille" => "Société",
        	"societe.nom_commercial" => "",
        	"societe.receivables" => "2 324 406",
        	"societe.financialcharges" => "59 035",
        	"societe.financialincome" => "4 741",
        	"societe.operationgprofitless" => "125 707",
        	"societe.netturnover" => "2 448 354",
        	"societe.operatingincome" => "3 497 660",
        	"societe.id_owner_fk" => 16,
        	"societe.id_famille_fk" => 2,
        	"societe.id_societe" => 246
        ));


		ob_start();
		$infos=array($data);
		$this->obj->export($infos,ATF::_s());

		$fichier=ob_get_contents();

		ob_end_clean();
	}
};
?>