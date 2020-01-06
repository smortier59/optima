<?
/** Classe accueil - Gestion de l'accueil
* @package Optima
* @subpackage Cleodis
*/
require_once dirname(__FILE__)."/../accueil.class.php";
class accueil_2tmanagement extends accueil {
	public $onglets = array(
		"hotline"=>array("opened"=>true)
	);
	protected $targetGlobalSearch = array("societe","contact","hotline");// La recherche globale se fait sur ces modules

	public function getAgence(){
		if( ATF::user()->select(ATF::$usr->get('id_user'), "id_profil") == 1 ){	 return array(1 , 3);
		}else{ return array(ATF::user()->select(ATF::$usr->get('id_user'), "id_agence"));	}
	}
};