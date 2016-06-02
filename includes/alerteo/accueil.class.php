<?
require_once dirname(__FILE__)."/../accueil.class.php";
/** Classe accueil - Gestion de l'accueil
* @package Optima
* @subpackage Alerteo
*/
class accueil_alerteo extends accueil {
	public $onglets = array(
		"tache"=>array("opened"=>true)
		,"suivi"=>array("opened"=>true)
	);
	public $shortcut = NULL;	
	protected $targetGlobalSearch = array("societe","contact","suivi","tache");// La recherche globale se fait sur ces modules
};
?>