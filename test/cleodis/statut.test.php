<?
class statut_test extends ATF_PHPUnit_Framework_TestCase {
	/* @author Morgan FLEURQUIN <mfleurquin@absystech.fr> */
	public function test_statut(){
		$c = new statut();
		$this->assertTrue(is_a($c,'statut'),"Objet statut");
	}
};