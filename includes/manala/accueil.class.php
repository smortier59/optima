<?
/** Classe accueil - Gestion de l'accueil
* @package Optima
* @subpackage Cleodis
*/
require_once dirname(__FILE__)."/../accueil.class.php";
class accueil_manala extends accueil {
	public $onglets = array(
		"mission"=>array("opened"=>true)
	);
	protected $targetGlobalSearch = array("societe","personnel","mission");// La recherche globale se fait sur ces modules
}