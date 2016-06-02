<?
class categorie_test extends ATF_PHPUnit_Framework_TestCase {
	/* @author Yann GAUTHERON <ygautheron@absystech.fr> */
	public function test_categorie(){
		$c = new categorie();
		$this->assertTrue(is_a($c,'categorie'),"Objet categorie");
		$this->assertEquals('a:4:{s:13:"fields_column";a:1:{s:19:"categorie.categorie";a:4:{s:4:"type";s:4:"text";s:5:"xtype";s:9:"textfield";s:9:"maxlength";s:2:"64";s:7:"default";N;}}s:7:"primary";a:1:{s:9:"categorie";a:4:{s:4:"type";s:4:"text";s:5:"xtype";s:9:"textfield";s:9:"maxlength";s:2:"64";s:7:"default";N;}}s:8:"restante";N;s:8:"bloquees";N;}'
			,serialize($c->colonnes),"Colonnes mauvaises");
	}
};