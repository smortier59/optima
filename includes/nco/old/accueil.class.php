<?
require_once dirname(__FILE__)."/../accueil.class.php";
/** Classe accueil - Gestion de l'accueil
 * @package Optima
 * @subpackage nco
 */
class accueil_nco extends accueil { 
	protected $targetGlobalSearch = array("societe","contact");// La recherche globale se fait sur ces modules

	/**
	 * Constructeur 
	 */
	public function __construct(){
		//Nouveau suivi
		$this->shortcut[0]=array("name"=>"shortcut_insert_suivi"
			,"rel"=>"Nouveau suivi"
			,"icone"=>"suivi_insert.png"
			,"href"=>"suivi-insert.html"
		);
		//Nouvelle Tâche
		$this->shortcut[1]=array("name"=>"shortcut_insert_tache"
			,"rel"=>"Nouvelle tâche"
			,"icone"=>"tache.png"
			,"href"=>"javascript:;"
			,"onclick"=>"ATF.createSelfTache();"
		);

	}
};
