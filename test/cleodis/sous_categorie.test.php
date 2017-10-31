<?
class sous_categorie_test extends ATF_PHPUnit_Framework_TestCase {
	
	/** Méthode pré-test, exécute avant chaque test unitaire
	* besoin d'un user pour les traduction
	*/
	public function setUp() {
		ATF::db()->begin_transaction(true);
	}
	
	/** Méthode post-test, exécute après chaque test unitaire*/
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function testAutocompleteAvecAdresse(){
		$infos["query"]="WORKSTATION";
		$autocomplete=$this->obj->autocomplete($infos);
		$this->assertEquals($this->obj->cryptId(25),$autocomplete[0]["0"],"autocompleteAvecAdresse ne renvoie pas le bon id crypté");
		$this->assertEquals('<span class="searchSelectionFound">WORKSTATION</span>',$autocomplete[0]["1"],"autocomplete ne renvoie pas le bon sous_categorie");
		$this->assertEquals("INFORMATIQUE",$autocomplete[0]["2"],"autocomplete ne renvoie pas le bon categorie");
		$this->assertEquals(25,$autocomplete[0]["3"],"autocomplete ne renvoie pas le bon id");

		$this->assertEquals(25,$autocomplete[0]["raw_0"],"autocomplete ne renvoie pas le bon id");
		$this->assertEquals("WORKSTATION",$autocomplete[0]["raw_1"],"autocomplete ne renvoie pas le bon sous_categorie");
		$this->assertEquals("INFORMATIQUE",$autocomplete[0]["raw_2"],"autocomplete ne renvoie pas le bon categorie");
		$this->assertEquals(25,$autocomplete[0]["raw_3"],"autocomplete ne renvoie pas le bon id");
	}
		/**
	 * Test méthode autocomplete
	 * @author Cyril CHARLIER <ccharlier@absystech.fr>
	 */
	public function test_ac(){
		ATF::db()->truncate("sous_categorie");
		$cat = ATF::categorie()->i(array('categorie' =>'test categorie'));
		$cat2 = ATF::categorie()->i(array('categorie' =>'Ma 2e cat'));

		$sousCat = ATF::sous_categorie()->i(array('sous_categorie' =>'sous categorie','id_categorie'=>$cat));
		$sousCat2 = ATF::sous_categorie()->i(array('sous_categorie' =>'My souscat','id_categorie'=>$cat));
		$sousCat3 = ATF::sous_categorie()->i(array('sous_categorie' =>'troisieme sous cat','id_categorie'=>$cat2));

		$get= array('id' => $cat);
		$res=ATF::sous_categorie()->_ac($get,false);

		$this->assertEquals(sizeof($res),2,'le nombre de retour n\'est pas bon');
		$this->assertEquals($res[0]['sous_categorie'],'My souscat', 'pas la bonne sous_categorie retournée');
		$this->assertEquals($res[1]['sous_categorie'], 'sous categorie', 'pas la bonne sous_categorie retournée');
		$get['q']="my";
		$reswithParams=ATF::sous_categorie()->_ac($get,false);
		$this->assertEquals(sizeof($reswithParams),1,'le nombre de retour n\'est pas bon');
		$this->assertEquals($reswithParams[0]['sous_categorie'],'My souscat', 'pas la bonne sous_categorie retournée');

		$res2=ATF::sous_categorie()->_ac(array('id' => $cat2),false);

		$this->assertEquals(sizeof($res2),1,'le nombre de retour n\'est pas bon');
		$this->assertEquals($res2[0]['sous_categorie'],'troisieme sous cat', 'pas la bonne sous_categorie retournée');

	}
};
?>