<?
class qualification_test extends ATF_PHPUnit_Framework_TestCase {
	/* @author Morgan FLEURQUIN <mfleurquin@absystech.fr> */
	public function test_qualification(){
		$c = new qualification();
		$this->assertTrue(is_a($c,'qualification'),"Objet qualification");
	}
};