<?
class commission_test extends ATF_PHPUnit_Framework_TestCase {
	public function setUp() {
		ATF::db()->begin_transaction(true);
	}
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}
	public function test_commission_constructeur(){
		new commission();
	}	
	
	// @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	public function test_select_all(){
		$this->obj->i(array(
			"id_commercial"=>1,
			"id_responsable"=>3,
			"commission_responsable"=>66,
			"commission_commercial"=>67,
			"id_affaire"=>300
		));
		$this->obj->q->reset();
		$resultat = $this->obj->select_all();
		var_dump($resultat);
		$this->assertEquals("hop",$resultat[0]["sdfsdf"],"commission mauvaise");
	}
	
	// @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	public function test_setInfos(){
		
	}
}