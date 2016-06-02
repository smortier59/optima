<?
class stats_cleodis_test extends ATF_PHPUnit_Framework_TestCase {
	/* @author Yann GAUTHERON <ygautheron@absystech.fr> */
	public function test_stats_cleodis(){
		$c = new stats_cleodis();
		$this->assertTrue(is_a($c,'stats_cleodis'),"Objet stats_cleodis");
		$this->assertEquals('a:3:{s:5:"suivi";a:2:{s:6:"taille";s:5:"200px";s:7:"couleur";s:4:"vert";}s:5:"devis";a:2:{s:6:"taille";s:5:"200px";s:7:"couleur";s:4:"vert";}s:8:"commande";a:2:{s:6:"taille";s:5:"200px";s:7:"couleur";s:4:"vert";}}'
			,serialize($c->stats),"Stats mauvaises");
	}
};