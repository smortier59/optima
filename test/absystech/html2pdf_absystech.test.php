<?
/**
* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
*/ 
class html2pdf_absystech_test extends ATF_PHPUnit_Framework_TestCase {
	
	public function setUp() {
		ATF::db()->begin_transaction(true);
	}
	
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}
	
	// Test du constructeur
	public function test_html2pdf_absystech_test_constructeur(){
		new html2pdf_absystech();
	}	
	
	public function testHotline_echeancier(){
		$this->initUserOnly(false);
		$id_societe=ATF::societe()->i(array("societe"=>"soc lol"));
		//$id_hotline=ATF::hotline()->i(array("id_societe"=>$id_societe,"hotline"=>"lol"));
		$retour=$this->obj->hotline_echeancier($id_societe);
		
		$this->assertEquals(" --encoding utf-8",$retour,"Le retour de la mthode est incorrecte");
		$date=ATF::$html->getVariable('date_debut');
		$this->assertEquals(date("01-m-Y"),$date->value,"La date assigne est incorrecte");
		$date=ATF::$html->getVariable('date_fin');
		$this->assertEquals(date("t-m-Y"),$date->value,"La date assigne est incorrecte");
		
		$date_debut=date("15-m-Y");
		$date_fin=date("20-m-Y");
		ATF::_r("date_debut",$date_debut);
		ATF::_r("date_fin",$date_fin);
		//$id_hotline=ATF::hotline()->i(array("id_societe"=>$id_societe,"hotline"=>"lol"));
		$retour=$this->obj->hotline_echeancier($id_societe);		

		$this->assertEquals(" --encoding utf-8",$retour,"Le retour de la mthode est incorrecte");
		$date=ATF::$html->getVariable('date_debut');
		$this->assertEquals($date_debut,$date->value,"La date assigne est incorrecte");
		$date=ATF::$html->getVariable('date_fin');
		$this->assertEquals($date_fin,$date->value,"La date assigne est incorrecte");
		
		//$hotlines=ATF::$html->getVariable('tickets');
		//$this->assertTrue(count($hotlines->value)==1,"Le nombre de hotline est incorrecte");
		//$this->assertEquals($id_hotline,$hotlines->value[0]['id_hotline'],"La hotline retourne est incorrecte");
	}
	
	
}	
?>