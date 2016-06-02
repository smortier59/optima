<?
/*
* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
* @package Optima
* @subpackage Cléodis
* @date 21-01-11
*/ 
class pack_produit_ligne_test extends ATF_PHPUnit_Framework_TestCase {

	/** Méthode pré-test, exécute avant chaque test unitaire
	* besoin d'un user pour les traduction
	*/
	public function setUp() {
		$this->begin_transaction(true);
	}
	
	/** Méthode post-test, exécute après chaque test unitaire*/
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}

	/** @test Test du constructeur produit.  */
	public function test_constructeur(){
		$this->pack_produit_ligne = new pack_produit_ligne();	
	}
}
?>