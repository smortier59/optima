<?
class fabricant_test extends ATF_PHPUnit_Framework_TestCase {
	/* @author Yann GAUTHERON <ygautheron@absystech.fr> */
	public function test_fabricant(){
		$c = new fabricant();
		$this->assertTrue(is_a($c,'fabricant'),"Objet fabricant");
		$this->assertEquals('a:5:{s:13:"fields_column";a:1:{i:0;s:19:"fabricant.fabricant";}s:7:"primary";a:1:{i:0;s:9:"fabricant";}s:8:"restante";N;s:12:"speed_insert";a:1:{i:0;s:9:"fabricant";}s:8:"bloquees";N;}'
			,serialize($c->colonnes),"Colonnes mauvaises");
	}
};