<?
class categorie_test extends ATF_PHPUnit_Framework_TestCase {
	
	public function setUp() {
		$this->begin_transaction(true);
		
		ATF::db()->truncate("commande_ligne");
		ATF::db()->truncate("facture_ligne");		
		
		ATF::db()->truncate("produit");		
		ATF::db()->truncate("sous_categorie");
		ATF::db()->truncate("categorie");		
	}
	
	/** Méthode post-test, exécute après chaque test unitaire*/
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}


	/* @author Yann GAUTHERON <ygautheron@absystech.fr> */
	public function test_categorie(){
		$c = new categorie();
		$this->assertTrue(is_a($c,'categorie'),"Objet categorie");
		$this->assertEquals('a:4:{s:13:"fields_column";a:1:{s:19:"categorie.categorie";a:4:{s:4:"type";s:4:"text";s:5:"xtype";s:9:"textfield";s:9:"maxlength";s:2:"64";s:7:"default";N;}}s:7:"primary";a:1:{s:9:"categorie";a:4:{s:4:"type";s:4:"text";s:5:"xtype";s:9:"textfield";s:9:"maxlength";s:2:"64";s:7:"default";N;}}s:8:"restante";N;s:8:"bloquees";N;}'
			,serialize($c->colonnes),"Colonnes mauvaises");
	}
	/**
	 * test autocomplete
	 * @author Cyril CHARLIER <ccharlier@absystech.fr>
	 */
	public function test_ac(){
		$cat = ATF::categorie()->i(array('categorie' =>'test categorie'));
		$cat2 = ATF::categorie()->i(array('categorie' =>'deuxieme categorie'));
		$cat3 = ATF::categorie()->i(array('categorie' =>'troisieme categorie'));

		$res=ATF::categorie()->_ac();
		log::logger($res,'ccharlier');
		$this->assertEquals(sizeof($res),3,'le nombre de retour n\'est pas bon');
		$this->assertEquals($res[0]['categorie'], 'troisieme categorie', 'pas la bonne categorie retournée');
		$this->assertEquals($res[1]['categorie'], 'deuxieme categorie', 'pas la bonne categorie retournée');
		$this->assertEquals($res[2]['categorie'],'test categorie', 'pas la bonne categorie retournée');

		$get['q']="troisieme";
		$reswithParams=ATF::categorie()->_ac($get,false);
		$this->assertEquals(sizeof($reswithParams),1,'le nombre de retour n\'est pas bon');
		$this->assertEquals($reswithParams[0]['categorie'],'troisieme categorie', 'pas la bonne categorie retournée');



	}
};