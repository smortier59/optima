<?
class commande_test extends ATF_PHPUnit_Framework_TestCase {
	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	/*besoin d'un user pour les traduction*/
	function setUp() {
		$this->initUser();
		$commande = new commande();
	}
	
	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function tearDown(){
		ATF::db()->rollback_transaction(true);
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function testStats(){
		unset(ATF::stats()->liste_annees['commande']);
		ATF::stats()->liste_annees["commande"][2011]=1;
		/* nombre de taches */
		$graph=$this->obj->stats();
		$this->assertTrue(is_array($graph),"Méthode stats non fonctionnel");
		$this->assertTrue(isset($graph['params']),"Méthode stats non fonctionnel (niveau params)");
		$this->assertTrue(isset($graph['categories']['category']),"Méthode stats non fonctionnel (niveau categories)");
		$this->assertTrue(isset($graph['dataset']),"Méthode stats non fonctionnel (niveau dataset)");
		
		/* nombre de taches créé pour un user */
		$graph=$this->obj->stats(false,'CA');
		$this->assertTrue(is_array($graph),"CA Méthode stats non fonctionnel");
		$this->assertTrue(isset($graph['params']),"Méthode stats non fonctionnel (CA niveau params)");
		$this->assertTrue(isset($graph['categories']['category']),"Méthode stats non fonctionnel (CA niveau categories)");
		$this->assertTrue(isset($graph['dataset']),"Méthode stats non fonctionnel (CA niveau dataset)");
		
		/* nombre de taches créé pour tous les users */
		$graph=$this->obj->stats(false,'marge');
		$this->assertTrue(is_array($graph),"marge Méthode stats non fonctionnel");
		$this->assertTrue(isset($graph['params']),"marge Méthode stats non fonctionnel (niveau params)");
		$this->assertTrue(isset($graph['categories']['category']),"marge Méthode stats non fonctionnel (niveau categories)");
		$this->assertTrue(isset($graph['dataset']),"marge Méthode stats non fonctionnel (niveau dataset)");

		/* nombre de taches créé pour tous les pipe */
		$graph=$this->obj->stats(false,'pourcentage');
		$this->assertTrue(is_array($graph),"pourcentage Méthode stats non fonctionnel");
		$this->assertTrue(isset($graph['params']),"pourcentage Méthode stats non fonctionnel (niveau params)");
		$this->assertTrue(isset($graph['categories']['category']),"pourcentage Méthode stats non fonctionnel (niveau categories)");
		$this->assertTrue(isset($graph['dataset']),"pourcentage Méthode stats non fonctionnel (niveau dataset)");
	}
	
};
?>