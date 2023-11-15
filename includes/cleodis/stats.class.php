<?
/**
* Module statistique
* @package Optima
*/
require_once dirname(__FILE__)."/../stats.class.php";
class stats_cleodis extends stats {
	/**
	* Constructeur
	*/
	public function __construct() {
		parent::__construct();
		$this->table = "stats";
		$this->stats = array("suivi"=>array("taille"=>"200px","couleur"=>"vert"));
		$this->stats["devis"]=array("taille"=>"200px","couleur"=>"vert");
		$this->stats["commande"]=array("taille"=>"200px","couleur"=>"vert");
		$this->liste_annees = $this->initialisation();
	}
};

class stats_cleodisbe extends stats_cleodis { };
class stats_itrenting extends stats_cleodis { };
class stats_cap extends stats_cleodis { };
class stats_solo extends stats_cleodis { };

class stats_bdomplus extends stats_cleodis { };

class stats_boulanger extends stats_cleodis { };

class stats_assets extends stats_cleodis { };
class stats_go_abonnement extends stats_cleodis { };