<?
require_once dirname(__FILE__)."/../accueil.class.php";
/** Classe accueil - Gestion de l'accueil
* @package Optima
* @subpackage Cresus
*/
class accueil_pvr extends accueil {
	public $onglets = array(
	);
	protected $targetGlobalSearch = array("emailing_contact");// La recherche globale se fait sur ces modules
	
};
