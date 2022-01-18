<?
/**
* Classe de test sur le module questionnaire_bk
*/
class questionnaire_bk_ligne_test extends ATF_PHPUnit_Framework_TestCase {

	/*--------------------------------------------------------------*/
	/*                   Tests unitaires                            */
	/*--------------------------------------------------------------*/

	/** Test du constructeur
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test__construct(){
		$c = new questionnaire_bk_ligne ();
		$this->assertTrue($c instanceOf questionnaire_bk_ligne , "L'objet questionnaire_bk_ligne  n'est pas de bon type");
	}

}