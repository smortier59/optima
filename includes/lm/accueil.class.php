<?
/** Classe accueil - Gestion de l'accueil
* @package Optima
* @subpackage Cleodis
*/
require_once dirname(__FILE__)."/../accueil.class.php";
class accueil_lm extends accueil {
	public $onglets = array(
		"affaire"=>array("opened"=>true)
	);

	/**
	* Retourne les widgets de l'utilisateur
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/
	public function getWidgets(){
		$w = array(
			//array('module'=>'log_btn')
		);
		return $w;
	}



};
?>