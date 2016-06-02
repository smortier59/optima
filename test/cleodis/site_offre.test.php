<?
class site_offre_test extends ATF_PHPUnit_Framework_TestCase {

	// @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	public function test_construct(){
		$c = new site_offre();			

		$this->assertTrue($c instanceOf site_offre, "L'objet site_offre n'est pas de bon type");
	}
};