<?
require_once dirname(__FILE__)."/../accueil.class.php";
/** Classe accueil - Gestion de l'accueil
 * @package Optima
 * @subpackage AbsysTech
 */
class accueil_absystech extends accueil { 
	protected $targetGlobalSearch = array("societe","contact","affaire","hotline","devis","commande","facture");// La recherche globale se fait sur ces modules

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

		//Nouvelle hotline
		$this->shortcut[1]=array("name"=>"shortcut_insert_hotline"
		,"rel"=>"Nouvelle requête hotline"
		,"icone"=>"hotline_insert.png"
		,"href"=>"hotline-insert.html"
		);
		//Nouvelle société
		$this->shortcut[2]=array("name"=>"shortcut_insert_societe"
		,"rel"=>"Nouvelle Société"
		,"icone"=>"societe_insert.png"
		,"href"=>"societe-insert.html"
		);
		//Pointage par jour
		$this->shortcut[3]=array("name"=>"shortcut_pointage_by_day"
		,"rel"=>"Pointage par jour"
		,"icone"=>"pointage_by_day.png"
		,"href"=>"javascript:;"
		,"onclick"=>"ATF.showContainer('pointage_by_day','shortcut_pointage_by_day','".ATF::$usr->trans('shortcut_pointage_by_day',$this->table)."');"
		);
		//Pointage par mois
		$this->shortcut[4]=array("name"=>"shortcut_pointage_by_month"
		,"rel"=>"Pointage par Mois"
		,"icone"=>"pointage_by_month.png"
		,"href"=>"javascript:;"
		,"onclick"=>"ATF.showContainer('pointage_by_month','shortcut_pointage_by_month','".ATF::$usr->trans('shortcut_pointage_by_month',$this->table)."');"
		);
		//Solde négatif
		$this->shortcut[5]=array("name"=>"shortcut_solde_negatif"
		,"rel"=>"Sociétés débitrices"
		,"icone"=>"societe_debitrices.png"
		,"href"=>"javascript:;"
		,"onclick"=>"ATF.showContainer('accueil-left-solde_negatif','shortcut_solde_negatif','".ATF::$usr->trans('shortcut_solde_negatif',$this->table)."');"
		);
		//Nouvelle Tâche
		$this->shortcut[6]=array("name"=>"shortcut_insert_tache"
			,"rel"=>"Nouvelle tâche"
			,"icone"=>"tache.png"
			,"href"=>"javascript:;"
			,"onclick"=>"ATF.createSelfTache();"
		);
	}
};
class accueil_demo extends accueil_absystech {
	protected $targetGlobalSearch = array("societe","contact","affaire","devis","commande","facture");// La recherche globale se fait sur ces modules
}
class accueil_att extends accueil_absystech { }
class accueil_wapp6 extends accueil_absystech { }
?>