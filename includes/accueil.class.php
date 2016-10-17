<?
/** 
* Classe accueil - Gestion de l'accueil
* @package Optima
*/
class accueil {
	/**
	* Onglets de l'accueil
	* @var array
	*/
	public $onglets = array(
		'hotline'=>array('opened'=>true)
	);
	
	/**
	* Raccourcis sur la gauche
	* @var array
	*/
	public $shortcut = array();
		
	protected $targetGlobalSearch = array("societe","contact","affaire");// La recherche globale se fait sur ces modules
	public $table="accueil";
		
	/**
	* Déféinir les table à parcourir pour le global_search
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @date 2009-02-22
	* @param array $a array("societe","contact","affaire")
	*/
	public function setTargetGlobalSearch($a) {
		$this->targetGlobalSearch = $a;
	}
	
	/**
	* Retourne le résultat d'une recherche pour un affichage en mode autocomplétion GLOBALE
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @date 2009-02-22
	* @param array|string $infos|$infos[query] ($_POST habituellement attendu)
	* @return string HTML de retour
	*/
	public function global_search($infos) {
		if ($infos["limit"]>25) return; // Protection nombre d'enregistrements par page
		
		if (strlen($infos["query"])>0) {
			$data = array();
			$searchKeywords = stripslashes(urldecode($infos["query"]));
			
			// Calcul du nombre de colonne maximal (important pour le UNION SQL)
			foreach ($this->targetGlobalSearch as $module) {
				if (!ATF::$usr->privilege($module)) continue;
				$class = ATF::getClass($module);
				$nb_cols = max($nb_cols,count($class->autocomplete["view"]));
			}
			
			// Pour chaque module dans lequel il est défini de chercher
			foreach ($this->targetGlobalSearch as $priorite => $module) {
				if (!ATF::$usr->privilege($module)) continue;
				$class = ATF::getClass($module);
				$class->q->reset()
					->setSearch($searchKeywords)
					->setStrict(1)
					->setToString();
				
				// On défini les champs sur lesquels effectuer la recherche
				$class->q->addField(array("'".$class->name()."'"=>array("alias"=>"moduleBrut","nosearch"=>true))) // Pour savoir de quel podule provient cet enregistrement
				->addField(array("'".ATF::$usr->trans($class->table,"module")."'"=>array("alias"=>"module","nosearch"=>true))) // Pour savoir de quel podule provient cet enregistrement
				->addField(array("CONCAT(".$class->table.".id_".$class->table.")"=>array("alias"=>"id","nosearch"=>true)));
				if ($class->autocomplete["view"]) {
					$class->q->addField($class->autocomplete["view"]);
					$nb_cols_this = count($class->autocomplete["view"]);
				} else {
					$class->q->addField($class->table.".id_".$class->table);
					$nb_cols_this = 1;
				}
				for ($i=$nb_cols_this;$i<=$nb_cols;$i++) {
					$class->q->addField('SUBSTRING(" '.$i.'",0,1)',"detail".$i); // Entourloupe pour avoir le même nombre de champs pour le UNION ^^
				}
				
				// Calcul de pertinence : pour afficher en premier les plus pertinents
				$class->q->addField(array('('.($priorite*100000000).'+1/'.$class->table.'.id_'.$class->table.')'=>array("alias"=>"pertinence","nosearch"=>true)));
				
				// Récupérer le SQL seulement
				$queries[] = $class->sa();
			}
			
			$q = new querier();
			$q->setLimit($infos["limit"])
			->addOrder('pertinence','asc')
			->setPage($infos["start"]/$infos["limit"]);
			if ($result = ATF::db($this->db)->union($queries,$q)) {
				// On met en valeur la chaîne recherchée dans les réponses
				$replacement = ATF::$html->fetch("search_replacement.tpl.htm","sr");
				foreach ($result["data"] as $k => $i) {
					foreach ($result["data"][$k] as $k_ => $i_) { // Mettre en valeur
						if ($k_===2 || $k_===0) {
							$result["data"][$k][$k_] = classes::cryptId($i_);
						} else {
							$result["data"][$k][$k_] = util::searchHighlight($i_, $infos["query"], $replacement);
						}
					}
				}
			}
			ATF::$json->add("totalCount",$result["count"]);
			ATF::$cr->block("top");
			ATF::$cr->block("generationTime");
		}
		
		return $result["data"];
	}
	
	/** 
	* Récupère l'espace disque utilisé par les fichiers sauvegardés
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @return float taille en Mo
	*/
	public function espace_disque(){
		$deb_chemin=__DATA_PATH__.ATF::$codename;
		$taille=explode("/",`du -bs $deb_chemin`);
		return round(trim($taille[0])/1048576,2);
	}
	
	/**
	* Récupère le nom du module (utilisé par les templates =/)
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	*/
	public function table(){
		return 'accueil';
	}
	
	/**
	* Récupère le nom du module (utilisé par les templates =/)
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	*/
	public function name(){
		return $this->table();
	}	
	
	/**
	* Récupère les filtres pour les proposer en page d'accueil
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
	public function getAllFilters(){
		ATF::filtre_optima()->q->reset()->where("id_user",ATF::$usr->getId());
		
		$result = ATF::filtre_optima()->sa();
		
		foreach ($result as $k=>$i) {
			$module = ATF::module()->select($i["id_module"],"module");
			$i['cls'] = "icon-module-".$module;
			$i['checked'] = false;
			if ($id = ATF::filtre_user()->filterExist(ATF::module()->from_nom("accueil"),$i['id_filtre_optima'])) {
				$i['id_filtre_user'] = $id;
				if (ATF::filtre_user()->select($id,'active')) $i['active'] = true;
				$i['checked'] = true;
			}
			$i['module'] = $module;
			$return[ATF::module()->nom($i["id_module"])][] = $i;	
		}
		
		return $return;
	}	




	public function _global_search($get,$post) {
		if ($get["limit"]>25) return; // Protection nombre d'enregistrements par page
		
		if (strlen($get["query"])>0) {
			$data = array();
			$searchKeywords = stripslashes(urldecode($get["query"]));
			
			// Calcul du nombre de colonne maximal (important pour le UNION SQL)
			foreach ($this->targetGlobalSearch as $module) {
				if (!ATF::$usr->privilege($module)) continue;
				$class = ATF::getClass($module);
				$nb_cols = max($nb_cols,count($class->autocomplete["view"]));
			}
			
			// Pour chaque module dans lequel il est défini de chercher
			foreach ($this->targetGlobalSearch as $priorite => $module) {
				if (!ATF::$usr->privilege($module)) continue;
				$class = ATF::getClass($module);
				$class->q->reset()
					->setSearch($searchKeywords)
					->setStrict(1)
					->setToString();
				
				// On défini les champs sur lesquels effectuer la recherche
				$class->q->addField(array("'".$class->name()."'"=>array("alias"=>"moduleBrut","nosearch"=>true))) // Pour savoir de quel podule provient cet enregistrement
				->addField(array("'".ATF::$usr->trans($class->table,"module")."'"=>array("alias"=>"module","nosearch"=>true))) // Pour savoir de quel podule provient cet enregistrement
				->addField(array("CONCAT(".$class->table.".id_".$class->table.")"=>array("alias"=>"id","nosearch"=>true)));
				if ($class->autocomplete["view"]) {
					$class->q->addField($class->autocomplete["view"]);
					$nb_cols_this = count($class->autocomplete["view"]);
				} else {
					$class->q->addField($class->table.".id_".$class->table);
					$nb_cols_this = 1;
				}
				for ($i=$nb_cols_this;$i<=$nb_cols;$i++) {
					$class->q->addField('SUBSTRING(" '.$i.'",0,1)',"detail".$i); // Entourloupe pour avoir le même nombre de champs pour le UNION ^^
				}
				
				// Calcul de pertinence : pour afficher en premier les plus pertinents
				$class->q->addField(array('('.($priorite*100000000).'+1/'.$class->table.'.id_'.$class->table.')'=>array("alias"=>"pertinence","nosearch"=>true)));
				
				$class->q->addOrder("pertinence","ASC");

				// Récupérer le SQL seulement
				$queries[] = $class->sa();
			}
			
			$q = new querier();
			$q->setLimit($get["limit"])->addOrder('pertinence','asc')->setPage($get["start"]/$get["limit"]);

			$result = ATF::db($this->db)->union($queries,$q);
			
			// On force les index du tableau, car les index numérique sont inexploitable côté HBS
			foreach ($result["data"] as $k=>$i) {
					$return[$k] = array(
						"mod"=>$i[0],
						"modb"=>$i[1],
						"id"=>$i[2],
						"nom"=>$i[3],
						"d1"=>$i[4],
						"d2"=>$i[5],
						"d3"=>$i[6],
						"d4"=>$i[7]
					);
			}

		}
		
		return $return;

	}
};
?>