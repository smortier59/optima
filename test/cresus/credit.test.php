<?php

class credit_test extends ATF_PHPUnit_Framework_TestCase {
	
	/** Méthode pré-test, exécutée avant chaque test unitaire
	* besoin d'un user pour les traduction
	*/
	public function setUp() {
		ATF::db()->begin_transaction(true);
	}
	
	/** Méthode post-test, exécutée après chaque test unitaire
	*/
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}

	/*--------------------------------------------------------------*/
	/*                   Tests unitaires                            */
	/*--------------------------------------------------------------*/
	
	/** Test du constructeur
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/	
	public function test__construct(){
		$c = new credit();	
		$this->assertTrue($c instanceOf credit, "L'objet credit n'est pas de bon type");
	}	
	
	/** Test du select_all
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test_select_all(){
		$sa = $this->obj->select_all();	

		$this->assertEquals(ATF::adherent()->select($sa[0]["id_adherent"] ,"num_dossier"), $sa[0]["num_dossier"] , "Error" );
	}
	
}
?>