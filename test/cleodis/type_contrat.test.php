<?
class type_contrat_test extends ATF_PHPUnit_Framework_TestCase {
	/* @author Morgan FLEURQUIN <mfleurquin@absystech.fr> */
	public function test_type_contrat(){
		$c = new type_contrat();
		$this->assertTrue(is_a($c,'type_contrat'),"Objet type_contrat");
	}
};