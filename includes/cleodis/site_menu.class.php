<?
/** Classe site_menu
* @package Optima
* @subpackage Cléodis
*/
require_once dirname(__FILE__)."/../site_menu.class.php";
class site_menu_cleodis extends site_menu {



	/**
	 * Permet d'afficher le contenu d'un menu de site passé en parametre
	 * @author : Morgan FLEURQUIN <mfleurquin@absystech.fr>
	 * @param  array $get  	site_associe -> le site web associé au site menu
	 *                      rubrique -> La rubrique à lister
	 * @return array
	 */
	public function _GET($get,$post){

		$return = array();

		$this->q->reset()->where("site_web", $get["site_associe"])
						 ->where("titre_menu", $get["rubrique"])
						 ->where("visible", "oui");

		if($menu = $this->select_row()){
			ATF::site_article()->q->reset()->where("id_site_menu", $menu["id_site_menu"])
										   ->where("visible", "oui")
										   ->addOrder("site_article.position","ASC");
			$articles = ATF::site_article()->select_all();

			if($articles){
				foreach ($articles as $key => $value) {
					$data = array();

					ATF::site_article_contenu()->q->reset()->where("id_site_article", $value["id_site_article"]);
					$contenu = ATF::site_article_contenu()->select_all();

					$data["titre"] = $value["titre"];

					foreach ($contenu as $kc => $vc) {
						$data["contenu"][] = $vc["texte"];
					}

					$return[] = $data;
				}
			}
		}

		return $return;

	}

}