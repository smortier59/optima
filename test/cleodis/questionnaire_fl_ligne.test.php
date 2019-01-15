<?
/**
* Classe de test sur le module questionnaire_fl
*/
class questionnaire_fl_ligne_test extends ATF_PHPUnit_Framework_TestCase {

	/*--------------------------------------------------------------*/
	/*                   Tests unitaires                            */
	/*--------------------------------------------------------------*/

	/** Test du constructeur
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test__construct(){
		$c = new questionnaire_fl_ligne ();
		$this->assertTrue($c instanceOf questionnaire_fl_ligne , "L'objet questionnaire_fl_ligne  n'est pas de bon type");
	}

}