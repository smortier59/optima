<?
/** Classe accueil - Gestion de l'accueil
* @package Optima
* @subpackage Cleodis
*/
require_once dirname(__FILE__)."/../accueil.class.php";
class accueil_cleodis extends accueil {
	public $onglets = array(
		"affaire"=>array("opened"=>true)
	);
	protected $targetGlobalSearch = array("societe","contact","affaire");// La recherche globale se fait sur ces modules

	public function getAgence(){
		if( ATF::user()->select(ATF::$usr->get('id_user'), "id_profil") == 1 ){	 return array(1 , 3);
		}else{ return array(ATF::user()->select(ATF::$usr->get('id_user'), "id_agence"));	}
	}

	/**
	* Retourne les widgets de l'utilisateur
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/
	public function getWidgets($id_agence){

		$w = array();

		if(ATF::user()->select(ATF::$usr->get('id_user'), "graphe_reseau") == "oui"){
			if(ATF::agence()->select($id_agence , "objectif_devis_reseaux")>0) $w[] =  array('module'=>'devis','type'=>'reseau','id_agence'=>$id_agence);
			if(ATF::agence()->select($id_agence , "objectif_mep_reseaux")>0) $w[] =  array('module'=>'commande','type'=>'reseau','id_agence'=>$id_agence);
			/*if(ATF::agence()->select($id_agence , "objectif_devis_S")>0) $w[] =  array('module'=>'devis','type'=>'les_S','id_agence'=>$id_agence);
			if(ATF::agence()->select($id_agence , "objectif_mep_S")>0) $w[] =  array('module'=>'commande','type'=>'les_S','id_agence'=>$id_agence);*/
		}

		if(ATF::user()->select(ATF::$usr->get('id_user'), "graphe_autre") == "oui"){
			if(ATF::agence()->select($id_agence , "objectif_devis_autre")>0) $w[] =  array('module'=>'commande','type'=>'autre','id_agence'=>$id_agence);
			if(ATF::agence()->select($id_agence , "objectif_mep_autre")>0)   $w[] =  array('module'=>'devis','type'=>'autre','id_agence'=>$id_agence);
		}

		$paysCS = ATF::creditsafe()->getSolde(false);
		log::logger($paysCS, "mfleurquin");
		foreach ($paysCS as $key => $v) {
			$w[] =  array('module'=>'creditsafe','type'=>'getSoldeCS_'.$key, 'index' => $key, 'id_agence'=>$id_agence);
		}

		return $w;
	}

	public function type_agence($infos){
		$donnees = explode(",", $infos);
		$donnees[1]= str_replace("id_agence=", "", $donnees[1]);
		$res = array("type"=> $donnees[0], "id_agence"=>$donnees[1]);

		if ($donnees[2]) {
			$donnees[2]= str_replace("index=", "", $donnees[2]);
			$res["index"] = $donnees[2];
		}


		return $res;
	}
};
class accueil_midas extends accueil_cleodis { };
class accueil_cleodisbe extends accueil_cleodis {
	public function getWidgets(){
		// @todo YG : Gérercela dans les préférences (par exemple)
		return array();
	}
};

class accueil_itrenting extends accueil_cleodis {
	public function getWidgets(){
		// @todo YG : Gérercela dans les préférences (par exemple)
		return array();
	}
};

class accueil_cap extends accueil_cleodis {
	public $onglets = array(
		"societe"=>array("opened"=>true)
	);
};

class accueil_solo extends accueil_cleodis {
	public function getWidgets(){
		return array();
	}
};

class accueil_arrow extends accueil_cleodis {
	public function getWidgets(){
		return array();
	}
};


class accueil_bdomplus extends accueil_cleodis { };
class accueil_boulanger extends accueil_cleodis { };


class accueil_assets extends accueil_cleodis { };
class accueil_go_abonnement extends accueil_cleodis { };
