<?
class cgv_test extends ATF_PHPUnit_Framework_TestCase {
	/** M�thode pr�-test, ex�cut�e avant chaque test unitaire
	* besoin d'un user pour les traduction
	*/
	public function setUp() {
		//print '[SU]';
		ATF::db()->autocommit(false,true);
		$this->s["user"]= new usr(1);
		//log::logger($this->s["user"],'jgwiazdowski');
	}
	
	/** M�thode post-test, ex�cut�e apr�s chaque test unitaire
	*/
	public function tearDown(){
		//print '[TD]';
		ATF::db()->rollback(true);
	}

	/*--------------------------------------------------------------*/
	/*                   Tests unitaires                            */
	/*--------------------------------------------------------------*/
	
	/** Test du constructeur
	* @author J�r�mie Gwiazdowski <jgw@absystech.fr>
	*/
	public function test_cgv_constructeur(){
		new cgv();
	}	
};
?>