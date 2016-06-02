<?
class reglement_cleodis_test extends ATF_PHPUnit_Framework_TestCase {
	
	public function setUp() {
		ATF::db()->begin_transaction(true);
	}
	
	public function tearDown() {
		ATF::db()->rollback_transaction(true);
	}

	/*--------------------------------------------------------------*/
	/*                   Tests unitaires                            */
	/*--------------------------------------------------------------*/
	
	/** Test du constructeur
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test_reglement_constructeur(){
		new reglement_cleodis();
	}	

}
?>