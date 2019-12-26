<?
/** Classe affaire
* @package Optima
* @subpackage Cléodis
*/
require_once dirname(__FILE__)."/../tache.class.php";
class tache_absystech extends tache {
	function __construct() {
		$this->table = "tache"; 
		parent::__construct();		
		$this->colonnes['primary']["periodique"] = "";
		$this->colonnes['fields_column']["tache.periodique"];	
		$this->fieldstructure();
	}
	
	/**
    * Valide une tache
    * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param array $infos pour la validation
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
    */
	public function valid($infos,&$s,$files=NULL,&$cadre_refreshed){
		// on met a jour les infos de la base avec entre autre la date de validation et le nom du user qui a validé
		//envoi d'un mail à tous les concernés si mise à jour effectuée
		$tache = $this->select($infos["id_tache"]);
		$termine = true;

		if($tache["periodique"]){
			$date_deb =  strtotime($tache["horaire_debut"]);
			$date_fin =  strtotime($tache["horaire_fin"]);
			

			switch ($tache["periodique"]) {
				case 'hebdomadaire':	
						$date_deb = date('Y-m-d H:i', strtotime("+7 day",  $date_deb));
						$date_fin = date('Y-m-d H:i', strtotime("+7 day",  $date_fin));				
				break;

				case 'mensuel':	
						$date_deb = date('Y-m-d H:i', strtotime("+1 month",  $date_deb));
						$date_fin = date('Y-m-d H:i', strtotime("+1 month",  $date_fin));				
				break;

				case 'trimestriel':	
						$date_deb = date('Y-m-d H:i', strtotime("+3 month",  $date_deb));
						$date_fin = date('Y-m-d H:i', strtotime("+3 month",  $date_fin));				
				break;

				case 'annuel':	
						$date_deb = date('Y-m-d H:i', strtotime("+1 year",  $date_deb));
						$date_fin = date('Y-m-d H:i', strtotime("+1 year",  $date_fin));				
				break;
			}

			$tache["horaire_debut"] = $date_deb;
			$tache["horaire_fin"] = $date_fin;
			unset($tache["date"], $tache["id_tache"]);
			$dest = $tache["dest"];
			unset($tache["dest"]);
			parent::insert(array("tache"=>$tache, "dest"=>$dest));
		}		
		return parent::valid($infos,$s,$files,$cadre_refreshed);
		
	}

	
	
	/*
	 * Annule une tâche
 	 * @author Quentin JANON <qjanon@absystech.fr>
 	 * @param id
	 * @return TRUE si vrai, sinon FALSE
	 */	
	function cancel($infos) {
		if (!$infos['id']) return false;
		$d = array("id_tache"=>$this->decryptId($infos['id']),"etat"=>"annule");
		return parent::u($d);
	}		
		
};
