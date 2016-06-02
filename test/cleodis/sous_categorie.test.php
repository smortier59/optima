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

};
?>