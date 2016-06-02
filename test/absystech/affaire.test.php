<?
class affaire_test extends ATF_PHPUnit_Framework_TestCase {
	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function testGetMargeTotaleDepuisDebutAnnee(){
		$getMargeTotaleDepuisDebutAnnee=$this->obj->getMargeTotaleDepuisDebutAnnee(0,(date("Y")-1)."-06-06");
		$this->assertNotNull($getMargeTotaleDepuisDebutAnnee,"getMargeTotaleDepuisDebutAnnee ne renvoi pas de chiffre");

		$getMargeTotaleDepuisDebutAnnee1=$this->obj->getMargeTotaleDepuisDebutAnnee(-1,(date("Y")-1)."-06-06");
		$this->assertNotNull($getMargeTotaleDepuisDebutAnnee1,"getMargeTotaleDepuisDebutAnnee ne renvoi pas de chiffre pour l'année précédente");

		$getMargeTotaleDepuisDebutAnnee1=$this->obj->getMargeTotaleDepuisDebutAnnee(-1,(date("Y")-1)."-06-06");
		$this->assertNotEquals($getMargeTotaleDepuisDebutAnnee,$getMargeTotaleDepuisDebutAnnee1,"getMargeTotaleDepuisDebutAnnee ne doit pas être égale à  getMargeTotaleDepuisDebutAnnee -1");
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function testgetMargeTotaleDepuisDebutAnneeDifferenceAnneePrecedente(){
		$getMargeTotaleDepuisDebutAnneeDifferenceAnneePrecedente=$this->obj->getMargeTotaleDepuisDebutAnneeDifferenceAnneePrecedente();
		$this->assertNotNull($getMargeTotaleDepuisDebutAnneeDifferenceAnneePrecedente,"getMargeTotaleDepuisDebutAnneeDifferenceAnneePrecedente ne renvoi pas de chiffre pour l'année précédente");
	}
};
?>