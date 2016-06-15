<?
/** Classe accueil - Gestion de l'accueil
* @package Optima
* @subpackage Cleodis
*/
require_once dirname(__FILE__)."/../accueil.class.php";
class accueil_exquado extends accueil {
	public $onglets = array(
		"affaire"=>array("opened"=>true)
	);
	protected $targetGlobalSearch = array("societe","contact","affaire");// La recherche globale se fait sur ces modules
};
?>