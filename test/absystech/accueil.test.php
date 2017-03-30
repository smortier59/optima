<?
class accueil_test extends ATF_PHPUnit_Framework_TestCase {
	// @author Yann GAUTHERON <ygautheron@absystech.fr>
	public function test_table(){
		$this->assertEquals("accueil",$this->obj->name(),"Probleme de nom de classe accueil");
	}


	// @author Yann GAUTHERON <ygautheron@absystech.fr>
	public function test_espaceDisque(){
		$this->assertEquals("double",gettype($this->obj->espace_disque()),"Probleme de retour d'espace disque utilisé");
	}

	// @author Yann GAUTHERON <ygautheron@absystech.fr>
	public function test_global_search(){
		$this->initUser(true);
		$this->obj->setTargetGlobalSearch(array("profil","contact","societe"));
		$infos["query"] = "soci";
		$infos["limit"] = "10";
		$search = $this->obj->global_search($infos);
		$this->assertArrayHasKey(0,$search,"Première dimension du tableau introuvable");
		$this->assertArrayHasKey(0,$search[0],"Seconde dimension du tableau introuvable");
		$this->assertEquals("profil",$search[0][0],"nom du module brut");
		$this->assertEquals("Profils utilisateurs",$search[0][1],"Nom du module");
		$this->assertEquals(32,strlen($search[0][2]),"ID en md5");
		$this->assertEquals('As<span class="searchSelectionFound">soci</span>é',$search[0][3],"Libellé avec highlight HTML span");

		$this->rollback_transaction();
	}

	// @author Quentin JANON <qjanon@absystech.fr>
	public function test_getAllFilters(){
		$this->initUser(true);
		// On commence par vider les tables utiles
		ATF::filtre_optima()->truncate();
		ATF::filtre_user()->truncate();

		// On créer quelques enregistrements
		$fo[1] = array(
			"filtre_optima"=>array(
				"filtre_optima"=>"FO1",
				"id_module"=>ATF::module()->from_nom("societe"),
				"id_user"=>ATF::$usr->getId(),
				"options"=>"Aucunes"
			)
		);
		$fo[2] = array("filtre_optima"=>array("filtre_optima"=>"FO2","id_module"=>ATF::module()->from_nom("societe"),"id_user"=>ATF::$usr->getId(),"options"=>"Aucunes"));
		$fo[3] = array("filtre_optima"=> array("filtre_optima"=>"FO3","id_module"=>ATF::module()->from_nom("devis"),"id_user"=>ATF::$usr->getId(),"options"=>"Aucunes"));
		$fo[4] = array("filtre_optima"=>array("filtre_optima"=>"FO4","id_module"=>ATF::module()->from_nom("devis"),"id_user"=>ATF::$usr->getId(),"options"=>"Aucunes"));
		$fo[5] = array("filtre_optima"=>array("filtre_optima"=>"FO5","id_module"=>ATF::module()->from_nom("affaire"),"id_user"=>ATF::$usr->getId(),"options"=>"Aucunes"));

		for ($i=1;$i<6;$i++) {
			$fo[$i]['filtre_optima']['id_filtre_optima'] = ATF::filtre_optima()->i($fo[$i]);
		}


		$fu[1] = array("id_filtre_optima"=>$fo[1]['filtre_optima']['id_filtre_optima'],"id_module"=>ATF::module()->from_nom("accueil"),"id_user"=>ATF::$usr->getId());
		$fu[2] = array("id_filtre_optima"=>$fo[2]['filtre_optima']['id_filtre_optima'],"id_module"=>ATF::module()->from_nom("accueil"),"id_user"=>ATF::$usr->getId());
		$fu[3] = array("id_filtre_optima"=>$fo[3]['filtre_optima']['id_filtre_optima'],"id_module"=>ATF::module()->from_nom("accueil"),"id_user"=>ATF::$usr->getId());

		for ($i=1;$i<4;$i++) {
			$fu[$i]['id_filtre_user'] = ATF::filtre_user()->i($fu[$i]);
		}

		$r = $this->obj->getAllFilters();

		// Check la présence des filtres
		$this->assertArrayHasKey("Affaires",$r,"Il n'y a pas les filtres d'affaires en retour");
		$this->assertArrayHasKey("Devis",$r,"Il n'y a pas les filtres d'affaires en retour");
		$this->assertArrayHasKey("Entités",$r,"Il n'y a pas les filtres d'affaires en retour");

		// Check des class CSS
		$this->assertEquals("icon-module-affaire",$r['Affaires'][0]['cls'],"Erreur qsur la class CSS retourné pour le filtre affaire");
		$this->assertEquals("icon-module-societe",$r['Entités'][0]['cls'],"Erreur qsur la class CSS retourné pour le filtre entité");
		$this->assertEquals("icon-module-devis",$r['Devis'][0]['cls'],"Erreur qsur la class CSS retourné pour le filtre devis");

		// Check du nombre de filtre
		$this->assertEquals(1,count($r['Affaires']),"Erreur sur le nombre de filtre pour affaire");
		$this->assertEquals(2,count($r['Entités']),"Erreur sur le nombre de filtre pour entité");
		$this->assertEquals(2,count($r['Devis']),"Erreur sur le nombre de filtre pour devis");

		// Check des filtres checked
		$this->assertTrue($r['Entités'][0]['checked'],"Le filtre 1 d'entite devrait être checked");
		$this->assertTrue($r['Entités'][1]['checked'],"Le filtre 2 d'entite devrait être checked");
		$this->assertTrue($r['Devis'][0]['checked'],"Le filtre 1 de devis devrait être checked");
		$this->assertFalse($r['Devis'][1]['checked'],"Le filtre 2 d'entite ne devrait pas être checked");
		$this->assertFalse($r['Affaires'][0]['checked'],"Le filtre 1 d'affaire ne devrait pas être checked");

		$this->rollback_transaction();
	}

	// @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	public function test_global_search_TELESCOPE(){
		$this->initUser(true);
		$this->obj->setTargetGlobalSearch(array("profil","contact","societe"));
		$infos["query"] = "soci";
		$infos["limit"] = "10";
		$search = $this->obj->_global_search($infos);
		log::logger($search , "mfleurquin");


		$this->assertArrayHasKey(0,$search,"Première dimension du tableau introuvable");
		$this->assertArrayHasKey("mod",$search[0],"Seconde dimension du tableau introuvable");
		$this->assertEquals("profil",$search[0]["mod"],"nom du module brut");
		$this->assertEquals("Profils utilisateurs",$search[0]["modb"],"Nom du module");
		$this->assertEquals(1,$search[0]["id"],"ID non crypté");
		$this->assertEquals('Associé',$search[0]["nom"],"Libellé avec highlight HTML span");

		$this->rollback_transaction();
	}
};
?>