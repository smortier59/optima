<?php
/** 
* Classe localisation_langue
* localisation : expression
* @package ATF
*/
class localisation extends classes_optima {
	/** 
	* Constructeur
	*/	
	public function __construct() { 
		parent::__construct();
		$this->table = __CLASS__;
		$this->controlled_by = "localisation_traduction";
		
		$this->fieldstructure();
		
		$this->field_nom = "localisation";
		
		ATF::tracabilite()->no_trace[$this->table]=1;
	}
			
	/** 
	* Retourne les localisations d'un codename particulier, ou codename=NULL
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param string $codename
//	* @param string $db
	* @return array
	*/
	public function getLoc($codename=NULL/*,$db=""*/){
		$loc=0;
		// Sauver les traductions (du projet courant) de $codename dans un tableau de référence
		$this->q->reset()->setStrict();
		
//		//Setting du db de localisation_traduction
//		if($db){
//			$old_db=ATF::localisation_traduction()->db;
//			ATF::localisation_traduction()->db=$db;
//		}
		
		if ($loc = $this->select_all()) {
			foreach ($loc as $kloc => $localisation) {
				ATF::localisation_traduction()->q->reset()->setStrict()
					->addCondition("id_localisation",$localisation["id_localisation"]);
				if ($codename) {
					ATF::localisation_traduction()->q->addCondition("codename",$codename);
				} else {
					ATF::localisation_traduction()->q->addConditionNull("codename");
				}
				if ($trad = ATF::localisation_traduction()->sa()) {
					foreach ($trad as $traduction) {
						unset($traduction["id_localisation_traduction"],$traduction["id_localisation"]);
						$loc[$kloc]["traductions"][] = $traduction;
					}
					unset($loc[$kloc]["id_localisation"]);
					$loc[$codename."_".$loc[$kloc]["localisation"]] = $loc[$kloc];
				}
				unset($loc[$kloc]);
			}
		}
		
//		//reSetting du bon db pour localisation_traduction
//		if($db){
//			ATF::localisation_traduction()->db=$old_db;
//		}
		return $loc;
	}

	/** 
	* Synchroniser les traduction de codename NULL depuis Optima
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @author Jérémie GWIAZDOWSKI <ygautheron@absystech.fr>	
	* @param string $codename le nom du projet de detination
//	* @param string le lien vers l'objet db spcifique
	*/
	public function syncTransFromOptima($codename/*,$db=""*/){
		// Sauver les traductions (du projet courant) de $codename dans un tableau de référence
		$loc = $this->getLoc($codename);
		echo "\n".count($loc)." traductions codename=".$codename;
		
		// Connexion  la base Optima de référence
//		if($db) $this->db=$db;
		
		// Récupérer toutes les traductions de codename NULL de Optima et les ajouter au tableau de référence
		$dbold = ATF::db($this->db)->getDatabase();
		$db_optima = "optima_absystech";
		ATF::db($this->db)->select_db($db_optima);
		if (ATF::db($this->db)->getDatabase()!=$db_optima) throw new errorATF("La base ".$db_optima." n'est pas accessible !");		

		$locOptima = $this->getLoc(NULL,$db);
		echo "\n".count($locOptima)." traductions codename=NULL dans optima";
		
		$loc = $loc + $locOptima;
		echo "\n".count($loc)." traductions a inserer en tout";
		
		// On se repositionne sur la base du projet courant
//		if($db) $this->db=NULL;
		ATF::db($this->db)->select_db($dbold);
		
		// Vider les traductions
		ATF::db($this->db)->begin_transaction();
		ATF::db($this->db)->truncate("localisation");
		
		// Injecter toutes les traductions dans la base du projet courant
		foreach ($loc as $l) {
			$id = ATF::localisation()->i(array(
				"localisation"=>$l["localisation"]
			));
			$nbL++;
			foreach ($l["traductions"] as $t) {
				$t["id_localisation"]=$id;
				ATF::localisation_traduction()->i($t);
				$nbT++;
				echo ".";
			}
		}
		
		echo "\n".$nbL." localisation et ".$nbT." traductions finalement inserees.";
		ATF::db($this->db)->end_transaction();
	}

    /** 
    * Surcharge pour ne renvoyer que ce qui n'est pas rempli.
    * @author Quentin JANON <qjanon@absystech.fr> 
    */    
    public function autocomplete($infos,$reset=true) {
        $this->q->reset()->from("localisation","id_localisation","localisation_traduction","id_localisation")->whereIsNull("localisation_traduction.expression_traduite");
        return parent::autocomplete($infos,false);
    }

};
?>