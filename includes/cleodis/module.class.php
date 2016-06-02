<?php
/** 
* Classe module : permet de gérer les modules de l'application
* @package ATF
*/
require_once dirname(__FILE__)."/../../libs/ATF/includes/module.class.php";
class module_cleodis extends module {}
class module_midas extends module {
	/** 
	* Proteger certains modules midas
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param int $id_parent : id du module pour lequel on veut les enfants
	* @return array
	*/
	public function enfants($id_parent,$visible=1) {
		$resultat = parent::enfants($id_parent,$visible);
		foreach ($resultat as $k => $r) {
			switch ($resultat[$k]["module"]) {
				case "contact":
				case "commande":
				case "devis":
				case "bon_de_commande":
				case "prolongation":
					unset($resultat[$k]);
					break;
			}
		}
		return $resultat;
	}
	
	/** 
	* Retourne toujours jaune pour midas
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param 
	* @return string
	*/
	public function skin_from_nom($nom) {
		return "yellow";
	}
};

class module_cleodisbe extends module_cleodis { };
class module_cap extends module_cleodis { };
class module_exactitude extends module_cleodis { };
?>