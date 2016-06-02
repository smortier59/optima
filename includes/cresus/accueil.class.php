<?
require_once dirname(__FILE__)."/../accueil.class.php";
/** Classe accueil - Gestion de l'accueil
* @package Optima
* @subpackage Cresus
*/
class accueil_cresus extends accueil {
	public $onglets = array(
		 "adherent"=>array("opened"=>true)
		,"zonegeo"=>array("opened"=>false)
	);
	protected $targetGlobalSearch = array("adherent");// La recherche globale se fait sur ces modules
	
 
	
	public function getWidgets(){
		$w = array(
			array('module'=>'rdv')
		);
		return $w;
	}
	
};
