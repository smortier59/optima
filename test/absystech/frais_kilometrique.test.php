<?
/**
* @package TU Optima
*/
class frais_kilometrique_test extends ATF_PHPUnit_Framework_TestCase {

	function setUp() {
		$this->initUser();
		$this->obj = ATF::frais_kilometrique();
		$this->obj->truncate();
		
		$frais = array(
			array("annee"=>"2009","cv"=>"5","coeff"=>"0.258","type"=>"auto")
			,array("annee"=>"2009","cv"=>"6","coeff"=>"0.369","type"=>"auto")
			,array("annee"=>"2009","cv"=>"7","coeff"=>"0.147","type"=>"moto")
			,array("annee"=>"2010","cv"=>"5","coeff"=>"0.741","type"=>"auto")
			,array("annee"=>"2010","cv"=>"6","coeff"=>"0.852","type"=>"auto")
			,array("annee"=>"2011","cv"=>"5","coeff"=>"0.123","type"=>"auto")
			,array("annee"=>"2011","cv"=>"6","coeff"=>"0.456","type"=>"auto")
			,array("annee"=>"2011","cv"=>"7","coeff"=>"0.789","type"=>"auto")
			,array("annee"=>"2011","cv"=>"5","coeff"=>"0.789","type"=>"moto")
		);
		
		foreach ($frais as $k=>$i) {
			$frais[$k]["id"] = $this->obj->insert($i);
		}
	}


	function tearDown(){
		ATF::db()->rollback_transaction(true);
	}
	
	/** 
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 18-05-2011
	*/
	public function test_default_value() {
		$this->assertEquals(ATF::$usr->getID(),$this->obj->default_value("id_user"),"Erreur de default value pour l'id_user");
		$this->assertNull($this->obj->default_value("cv"),"Erreur de default value pour l'cv");
	}

	/** 
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 18-05-2011
	*/
	public function test_getCoeffs() {
		$r = json_decode($this->obj->getCoeffs("2011"));
		$count=0;
		foreach ($r as $el) {
			$count += 1;	
		}
		$this->assertEquals(4,$count,"Erreur dans le nombre de retour pour 2011");
		$r = json_decode($this->obj->getCoeffs("2010"));
		$count=0;
		foreach ($r as $el) {
			$count += 1;	
		}
		$this->assertEquals(2,$count,"Erreur dans le nombre de retour pour 2010");
		$r = json_decode($this->obj->getCoeffs("2009"));
		$count=0;
		foreach ($r as $el) {
			$count += 1;	
		}
		$this->assertEquals(3,$count,"Erreur dans le nombre de retour pour 2009");
	}

	/** 
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 18-05-2011
	*/
	public function test_autocomplete() {
		$r = $this->obj->autocomplete($infos,$s,$request,2011);
		$this->assertEquals(4,count($r),"Pas le bon nombre de retour ici");
	}

};
?>