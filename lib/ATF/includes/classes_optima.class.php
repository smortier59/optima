<?php
/**
* Classe mère spécifique à Optima
* @package ATF
*/
class classes_optima extends classes {

	/* Fichiers joints et leurs attributes particuliers
	* @example
	*	$this->files = array(
	*		"logo"=>array(
	*			"type"=>"png"
	*			,"convert_from"=>array("jpg","png","gif")
	*		)
	*		"rapport"=>array(
	*			"type"=>"pdf"
	*		)
	*	);
	*	public $files = array(
	*		"fichier_joint" => array("type"=>"pdf")
	*		"fichier_joint2" => true
	*	);
	* @param string type Type stocké (et seul aurotisé si aucun convert_from)
	* @param array convert_from Types autorisés
	* @param boolean obligatoire
	*/
	public $files;

//	// Droits hérités de ce module
//	public $controlled_by = "devis";
//
//	// Le comportements sont régis par le module spécifié selon l'action
//	public $actions_by = array("insert"=>"devis","update"=>"devis");
//
//	// Cette classe est toujours visible, tout user qui se connecte a tous les droits dessus
//	public $public = true;
//
//	// Un privilège est sous la responsabilité d'un autre
	public $privilege_egal = array("select_all"=>"select","cloner"=>"insert");

	/*Les Elements du desc de la table*/
	public $desc = NULL;

	/*  Nom du module + icone     */
	public $tabModule=true;

	/* Pagination */
	public $pagerPager=true;

	/* Recherche */
	public $pagerSearch=true;

	/* Filtrage des données */
	public $pagerFilter=true;

	/* Vue (sélection des colonnes) */
	public $pagerColumn=true;

	/* Export des données */
	public $pagerExport=true;

	/* Géolocalisation */
	public $gmap=false;

	/* Tous cocher/décocher */
	public $check_all=false;

	/* Table Standard */
	public $table_standard=false;

	/* Permet d'afficher la possibilité de cloner dans un listing (select_all) */
	public $clone_listing=false;

	/* Le formulaire doit être en extJS */
	public $formExt=true;

	/**
	* Tableau de modules
	* Le is_acti
	* @var array
	*/
	protected $can_insert_from=NULL;

	// Mapping prévu pour un autocomplete
	public static $autocompleteMapping = array();
//		array("name"=>'nom', "mapping"=>0),
//		array("name"=>'detail', "mapping"=>1),
//		array("name"=>'prix', "mapping"=>2),
//		array("name"=>'prixAchat', "mapping"=>3),
//		array("name"=>'id_devis_ligne', "mapping"=>4),
//		array("name"=>'id_produit', "mapping"=>5)
//	);

	/**
	* Contructeur
	*/
	public function __construct($table=NULL) {

		// Relations générique entre les tables
		$this->foreign_key["id_owner"] = "user";

		$this->quick_action['select_all'] = array('insert','refresh'/*,'print'*/);
		$this->quick_action['select'] = array('insert','cloner','update','delete','affichage_onglet'=>array('privilege'=>'select'),'print'=>array('privilege'=>'select'));
		$this->quick_action['update'] = array('select');
		$this->quick_action['insert'] = array('select');
		$this->quick_action['cloner'] = array('select');


		parent::__construct($table);

		// Constructeur automatique
		//n'a normalement plus d'utilité
		/*if ($table && $table!=='module') {
			if ($id_module = ATF::module()->from_nom($table)) {
				if ($default_constructor = ATF::module()->select($id_module,"construct")) {
					try {
						eval($default_constructor);
					} catch(Exception $e) {
						throw new errorATF('erreur_constructeur_par_defaut ('.$table.')');
					}
				}
			}
		}*/

		$this->fieldstructure();

		$this->addPrivilege("generatePDF","update");
		$this->addPrivilege("createPermalink");
		$this->addPrivilege("widget");
		$this->addPrivilege("onglet");
		$this->addPrivilege("refresh");
		$this->addPrivilege("uploadFileFromSA","update");
		$this->addPrivilege("getTruncated");
		$this->addPrivilege("updateDate");
		$this->addPrivilege("updateOnSelect","update");
		$this->addPrivilege("getUpdateForm","insert");
		$this->addPrivilege("saveFilterTab");
		$this->addPrivilege("changeActiveTab");
		$this->addPrivilege("loadFilters");
		$this->addPrivilege("genereMenuVue");
		$this->addPrivilege("recupAggregate");
		$this->addPrivilege("uploadXHR");
		$this->addPrivilege("saveAggregat");
		$this->addPrivilege("deleteCascade");
		$this->addPrivilege("isAggregateActive");
		$this->addPrivilege("tronque");
		$this->addPrivilege("getQuickTips");
		$this->addPrivilege("getFilterCountSimulations");
		$this->addPrivilege("champs1");
		$this->addPrivilege("operator");
		$this->addPrivilege("trad");
		$this->addPrivilege("listeModuleAssocie");
		$this->addPrivilege("resultForFiltre");
		$this->addPrivilege("operand");
		$this->addPrivilege("getNameOperand");
		$this->addPrivilege("sendMailEXT");
	}

	/**
	* Déclenche une erreur si la colonne spécifiée dans un panel n'existe pas dans la base
	* Vérifie aussi les compositefields
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $colonnes
	* @param string $dimension : principales/secondaires
	*/
	public function checkColonnes(&$colonnes,$dimension){
		foreach ($colonnes as $key=>$item) {
			if (is_numeric($key) && is_string($item)) {
				ATF::$msg->addWarning(loc::mt(
					ATF::$usr->trans("colonne_inconnu_et_non_custom")
					,array(
						"dimension"=>$dimension
						,"table"=>$this->table
						,"field"=>$item
					)
				));
				unset($colonnes[$key]);
			}

			// Compositefields
			if($item["fields"]) {
				foreach($item["fields"] as $k=>$i){
					if (is_numeric($k) && is_string($i)) {
						ATF::$msg->addWarning(loc::mt(
							ATF::$usr->trans("colonne_inconnu_et_non_custom")
							,array(
								"dimension"=>$dimension
								,"table"=>$this->table
								,"field"=>$key.".".$i
							)
						));
						unset($colonnes[$key]["fields"][$k]);
					}
				}
			}

			$this->checkColonnePrivilege($colonnes,$key);
		}
	}

	/**
	* Vérifie les privilèges de l'utilisateur courant
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $colonnes
	* @param string $key, champ dans le tableau qu'il faut vérifier
	* @param string $privilege Privilège à tester
	* @return boolean TRUE si le privilege est accepté
	*/
	public function checkColonnePrivilege(&$colonnes,$key,$privilege="select"){
		$field = $key;
		if (strpos($key,".")!==false) {
			$field = substr($key,strpos($key,".")+1);
		}
		if (!ATF::$usr->privilege($this->table,$privilege,$field)) {
			unset($colonnes[$key]);
			return false;
		}
		return true;
	}

	/**
	* Retire les champs qui n'existent pas dans la base de données pour une dimension
	* Vérifie aussi les compositefields
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $dimension : Nom du panel, ne fonctionne pas pour PRIMARY !
	*/
	public function checkAndRemoveBadFields($dimension){
		foreach ($this->colonnes['panel'][$dimension] as $k => $i) {
			if (!is_array($i)) {
				unset($this->colonnes['panel'][$dimension][$k]);
			}
			if ($i["fields"]) {
				foreach ($i["fields"] as $k_ => $i_) {
					if (!is_array($i_)) {
						unset($this->colonnes['panel'][$dimension][$k]["fields"][$k_]);
					}
				}
			}
		}
	}

	/**
	* Renvoi les colonnes secondaires appropriées
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @author refactoring Quentin JANON <qjanon@absystech.fr>
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $dimension : principales/secondaires
	* @param string $event : insert/update
	* @param boolean $panel TRUE s'il s'agit d'un panel et donc dans $this->colonnes['panel']
	*/
	public function colonnes($dimension,$event=NULL,$panel=false){
		$colonnes=$this->colonnes[$dimension];
		switch ($dimension) {
			case "primary":
				// Vérification colonne existe
				$this->checkColonnes($this->colonnes['primary']);

				if ($event==="select") {
					foreach($colonnes as $key=>$item){
						if($item["fields"]) {
							// Compositefields
							foreach($item["fields"] as $k=>$i){
								if($item["fields"]) {
									$colonnes[$k]=$i;
								}
							}
							unset($colonnes[$key]);
						}
					}
				}

				foreach($this->colonnes['bloquees'][$event] as $k=>$i){
					if($colonnes[$i]) {
						unset($colonnes[$i]);
					}
				}
			break;
			default:
				if ($panel) {
					// Vérification colonne existe
					$this->checkColonnes($this->colonnes['panel'][$dimension],$dimension);
					if($colonnes = $this->colonnes['panel'][$dimension]) {
						if ($event==="select") {
							// Ne pas voir les panels secondaires dans le select
							foreach($colonnes as $key=>$item){
								if($item["panel_key"]) {
									unset($colonnes[$key]);
								}
								if($item["fields"]) {
									// Compositefields
									foreach($item["fields"] as $k=>$i){
										if($item["fields"]) {
											$colonnes[$k]=$i;
										}
									}
									unset($colonnes[$key]);
								}
							}
						}

						foreach($this->colonnes['bloquees'][$event] as $key=>$item){
							if($colonnes[$item]) {
								unset($colonnes[$item]);
							}
						}
					}
				} else {
					// Panel secondaire, colonnes restantes
					$colonnes['primary']=array_merge((array)$this->colonnes['primary'],array_flip((array)$this->colonnes['bloquees'][$event]));
					$colonnes['panel'] = $this->colonnes['panel'];
					$return = $this->colonnes_restantes($colonnes,true);

					return $return;
				}
			break;
		}
		return $colonnes;

	}

	/**
    * Renvoi les détails de structure d'un tableau de champs MySQL
	* @param array $fields
    * @author Quentin JANON <qjanon@absystech.fr>
    * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
    */
	public function fieldstructure() {
		try {
			// Je n'ai pas compris pourquoi maintenant il y a plein d'erreurs 1146 table introuvable, fax, calendrier, user_absystech etc... donc je met ce try catch

			//log::logger('[fieldstructure '.$this->table.' num='.$i.']','jgwiazdowski');
			if (!$this->desc) {
				// Structure des champs bruts récupérés par la base
				$this->desc = ATF::db($this->db)->desc($this->table);
			}
			foreach ($this->desc as $item) {
				$structure[$item["Field"]] = $item;
				if ($item["Field"]!="id_".$this->table) {
					$structure_simple[] = $item["Field"];
				}
			}

			// Remplir les définitions de colonnes si rien n'est défini dans le constructeur
			if (!isset($this->colonnes["fields_column"]) || !isset($this->colonnes["primary"])) {
				unset($structure_simple["id_".$this->table]);

				// Par défaut, toutes les colonnes pour les listing
				if (!isset($this->colonnes["fields_column"])) {
					$this->colonnes["fields_column"] = $structure_simple;
				}

				// Par défaut, toutes les colonnes dans le primary
				if (!isset($this->colonnes["primary"])) {
					$this->colonnes["primary"] = $structure_simple;
				}
			}
			//Sauvegarde des colonnes bloquées
			$col_save = $this->colonnes['bloquees'];
			unset($this->colonnes['bloquees']);

			$this->colonnes = ATF::db($this->db)->fieldstructure($this,$this->colonnes,$structure);
			$this->colonnes["restante"] = $this->colonnes_restantes($this->colonnes,true);
			//Restauration des collones bloquees
			$this->colonnes['bloquees'] = $col_save;

		} catch (errorSQL $e) {
			if ($e->getErrno() == 1146) {
				// pas grave
			} else {
				throw $e;
			}
		}

	}

	/**
    * Renvoi les colonnes déjà utilisées sous forme d'une string pour effectuer le filtrage
	* @param array $cols tableau de colonnes initialisées dans le constructeur de la classe
	* @param bool $html_structure Pilote l'appel de la fonction correspondante en retour
    * @author Quentin JANON <qjanon@absystech.fr>
    * @return string Filtre sous forme de string avec les champs separé par virgule, ou alors renvoi le résultat de l'html_structure avec le filtre obtenu appliqué
    */
	public function colonnes_restantes($cols,$html_structure=false) {
		// Ici il n'y a que le colonne primary et le colonne panel qui doit passer sinon toutes les colonnes seront achappés :o/
		// trouvé quelque chose ed générique pour ca dans l'avenir !
		if (isset($cols["panel"])) {
			foreach($cols["panel"] as $key=>$item){
				foreach($item as $k=>$i){
					if (is_numeric($k)) $k=$i;
					$panel[$k]=$i;
				}
			}
		}

		$colonnes = array();
		if (isset($cols["primary"])) {
			$colonnes = array_merge($colonnes,(array)$cols["primary"]);
		}
		if (isset($panel)) {
			$colonnes = array_merge($colonnes,(array)$panel);
		}
		if (isset($cols["fields"])) {
			$colonnes = array_merge($colonnes,(array)$cols["fields"]);
		}

		$ignore = array("maxlength","null","type","default","field","key","extra","custom");
		foreach ($colonnes as $k=>$i) {
			if (in_array($k,$ignore) && !is_array($i)) {
				if ($k!="custom" || is_bool($i)) { // Le custom peut etre un champ de base de données, ou le flag particulier de champ spécifique. Lorsqu'il est booléen on est sur qu'il s'agit du flag !
					continue;
				}
			}
			$filtre_array = explode(",",$filtre);
			$filtre_array[]=$k;
			$filtre = implode(",",array_flip(array_flip($filtre_array))).",";
			if (is_array($i)) {
				$filtre .= self::colonnes_restantes($i);
			}
		}
		if ($html_structure) {
			$fk = "id_".$this->table;
			if (isset($filtre) && $filtre) {
				$filtre .= ",".$fk;
			} else {
				$filtre = $fk;
			}
			return $this->html_structure($filtre);
		} else {
			return $filtre;
		}
	}

	/**
	* Retourne le résultat d'une recherche pour un affichage en mode autocomplétion (Seuil déterminé par constante __SEUIL_AUTOCOMPLETION__)
	*  Ajout du 28/10/2010 :
	*       Possibilité de trier l'autocomplete avec order_field et order_sens
	*       Exemple : $infos["order_field"]="affaire.date" et $infos["order_sens"]="desc"
	*       Note : order_sens est obligatoire, donc par défaut il faut mettre $infos["order_sens"]="asc"
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr> Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @date 2009-02-22
	* @param array $infos ($_POST habituellement attendu)
	*	string $infos[recherche]
	* @param boolean $reset VRAI si on reset lme querier, FAUX si on a initialisé qqch de précis avant...
	* @return string HTML de retour
	*/
	public function autocomplete($infos,$reset=true) {
		if ($infos["limit"]>25) return; // Protection nombre d'enregistrements par page

		if (!$infos["query"]) {
			$infos["query"] = "%";
		}
		//if (strlen($infos["query"])>0) {
			if ($reset) {
				$this->q->reset();
			}

			// On ne doit pas écraser une limite particulière demandée...
			if (!$this->q->getLimit()) {
				if(!$infos["limit"]) $infos["limit"] = 30;
				if(!$infos["start"]) $infos["start"] = 0;


				$this->q
					->setLimit($infos["limit"])
					->setPage($infos["start"]/$infos["limit"]);
			}

			$this->q
				->setCount()
				->setStrict(1)
				->setDimension('row_arro')
				->setSearch(stripslashes(urldecode($infos["query"])))
				->addField(array("CONCAT(".$this->table.".id_".$this->table.")"=>array("alias"=>"id","nosearch"=>true))); // Clé primaire brute

			/* On défini les champs sur lesquels effectuer la recherche */
			if ($this->autocomplete["view"]) {
				$this->q->addField($this->autocomplete["view"]);
			} else {
				$this->q->addField($this->table.".id_".$this->table);
			}
			if (count($this->autocomplete["view"])<2) {
				$this->q->addField('""',"detail");
			}

			// Clée étrangère
			if($infos["condition_field"] && $infos["condition_value"]){

				/* Lorsqu'on a une string pour condition_field */
				if (!is_array($infos["condition_field"])) {
					$infos["condition_field"] = array($infos["condition_field"]);
					$infos["condition_value"] = array($infos["condition_value"]);
				}

				foreach ($infos["condition_value"] as $k => $v) {
					$this->q->addCondition($infos["condition_field"][$k],$this->decryptId($infos["condition_value"][$k]));
				}
			}

			// Ordre particulier
			if($infos["order_field"] && $infos["order_sens"]){
				$this->q->addOrder($infos["order_field"],$infos["order_sens"]);
			}
			if ($result = $this->select_data()) {
				// On met en valeur la chaîne recherchée dans les réponses
				$replacement = ATF::$html->fetch("search_replacement.tpl.htm","sr");
				foreach ($result["data"] as $k => $i) {
					foreach ($result["data"][$k] as $k_ => $i_) { // Mettre en valeur
						$result["data"][$k]["raw_".$k_] = $i_;
						if ($k_>0 || !is_numeric($i_)) {
							$result["data"][$k][$k_] = util::searchHighlight($i_, $infos["query"], $replacement);
						} else {
							$result["data"][$k][$k_] = classes::cryptId($i_);
						}
					}
				}
			}
			ATF::$json->add("totalCount",$result["count"]);
		//}
		ATF::$cr->rm("top");
		return $result["data"];
	}

	/**
	* Pour les autocomplete, retourne une conditions au format URL   arg1=2&arg2=3...
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $class Classe des enregistrements affichés dans l'autocomplète
	* @param array $infos ($requests habituellement attendu)
	*	int $infos[id_affaire]
	*	int $infos[id_societe]
	* @param string $condition_field
	* @param string $condition_value
	* @return array Conditions de filtrage
	*/
	public function autocompleteConditions(classes_optima $class,$infos,$condition_field=NULL,$condition_value=NULL) {
		if ($condition_value && $condition_field) {
			if (strpos($condition_field,".")===false) {
				$condition_field = $class->table.".".$condition_field;
			}
			$conditions["condition_field"][] = $condition_field;
			$conditions["condition_value"][] = $condition_value;
		}
		return (array)($conditions);
	}

	/**
	* fonction qui va permettre de n'exporter que ce qui est filtré
	* @author : Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param int $infos l'identificateur de l'élément que l'on désire exporter
	* @param array &$s La session
	*/
	public function export_vue($infos,&$s){
		$this->q->reset();
		$this->setQuerier($s["pager"]->create($infos['onglet'])); // Recuperer le querier actuel
		$this->q->setLimit(-1);
		$infos = $this->select_data($s,"saExport");
		$this->export($infos['data'],$s);
	}

	/**
	* fonction qui va permettre d'exporter tous les champs
	* @author : Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param int $infos l'identificateur de l'élément que l'on désire exporter
	* @param array &$s La session
	*/
	public function export_total($infos,&$s){
		$this->setQuerier($s["pager"]->create($infos['onglet']));
		//on retiens le where dans le cas d'un onglet pour filtrer les donnéees
		$this->q->addAllFields($this->table)->setLimit(-1)->unsetCount();
		$infos = $this->select_data($s,"saExport");
		$this->export($infos,$s);
	}

	/**
	* fonction qui va permettre de télécharger des fichiers
	* @author : Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param int $infos l'identificateur de l'élément que l'on désire exporter
	* @param array &$s La session
	*/
	public function export(&$infos,&$s){
		if ($this->table) {
			//pour éviter les problèmes d'export d'une grosse quantité de données (page blanche)
			session_write_close();

			require_once __ABSOLUTE_PATH__."libs/ATF/libs/php_writeexcel/class.writeexcel_workbook.inc.php";
			require_once __ABSOLUTE_PATH__."libs/ATF/libs/php_writeexcel/class.writeexcel_worksheet.inc.php";

			$fname = tempnam(__TEMPORARY_PATH__, __TEMPLATE__.ATF::$usr->getID());
			$workbook = new writeexcel_workbook($fname);
			$worksheet =& $workbook->addworksheet('Export Optima');

			if ($infos) {
				// mise en place des noms de champs
				$row_data=array();

				foreach ($infos[0] as $k_ => $i_) {
					$k_=explode(".",$k_);
					//si il fait parti des colonnes bloquées, on l'affiche pas
					if(!in_array(($k_[1]?$k_[1]:$k_[0]),$this->colonnes['bloquees']['export'])){
						//si il ne s'agit pas de l'identifiant de la table en question ou d'une clé étrangère
						if(!$k_[1]){
							$row_data[] =ATF::$usr->trans($k_[0],$this->table);
						}elseif(util::class_from_id($k_[1])->table!=$this->table && substr($k_[1],-3)!="_fk" || (util::class_from_id($k_[1])->table==$this->table && !$infos[0][$this->table.".".$this->table])){
							// On regarde le field nom pour mettre le bon nom de colonne
							// Exemple : emailing_contact, le field_nom c'est l'email, du coup quand on exporte on a une colonne 'ID Contact' avec l'email dedans,
							// C'est assez embêtant pour le mapping lors de l'import, du coup on remplace le libellé de colonne par la traduction du field_nom
							if ($k_[1]=="id_".$this->table && $this->field_nom && !preg_match("/%/",$this->field_nom)) {
								$row_data[] =ATF::$usr->trans($this->field_nom,$this->table);
							} else {
								// Si il y a plusieurs champs dans le field nom ou qu'il n'y en a pas, alors on fais le traitement normal.
								$row_data[] =ATF::$usr->trans($k_[1],$this->table);
							}
						}
					}
				}
				$worksheet->write_row("A1", array_map("utf8_decode",$row_data));
				$row++;

				// et ensuite les données
				foreach ($infos as $k => $i) {
					$row++;
					$row_data=array();
					foreach ($i as $k_ => $i_) {
						$k_=explode(".",$k_);
						//si il fait parti des colonnes bloquées, on l'affiche pas
						if(!in_array($k_[1],$this->colonnes['bloquees']['export'])){
							$class = util::class_from_id($k_[1]);
							//si il ne s'agit pas de l'identifiant de la table en question ou d'une clé étrangère
							if($class->table!=$this->table && substr($k_[1],-3)!="_fk" || ($class->table==$this->table && !$infos[$k][$this->table.".".$this->table])) {
								$row_data[]=$i_;
							}
						}
					}
					$worksheet->write_row("A".$row, array_map("utf8_decode",$row_data));
				}
			}

			$workbook->close();
			$fh=fopen($fname, "rb");
			header("Content-Type: application/x-msexcel; name=".str_replace(" ","_",ATF::$usr->trans($this->table,"module")).".xls");
			header("Content-Disposition: attachment; filename=".str_replace(" ","_",ATF::$usr->trans($this->table,"module")).".xls");
			header("Cache-Control: private");
			ob_end_clean();
			fpassthru($fh);
			unlink($fname);
		}
	}

	/**
	* fonction qui va permettre de télécharger des fichiers
	* @author : Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @author : Quentin JANON <qjanon@absystech.fr>
	* @param int $infos l'identificateur de l'élément que l'on désire exporter
	* @param array &$s La session
	*/
	public function export_brut(&$infos,&$s){
		if ($this->table) {
			$this->setQuerier($s["pager"]->create($infos['onglet']));
			//on retiens le where dans le cas d'un onglet pour filtrer les donnéees
			$this->q->addAllFields($this->table)->setStrict()->setLimit(-1)->unsetCount();
			$data = $this->select_all();

			//pour éviter les problèmes d'export d'une grosse quantité de données (page blanche)
			session_write_close();

			require_once __ABSOLUTE_PATH__."libs/ATF/libs/php_writeexcel/class.writeexcel_workbook.inc.php";
			require_once __ABSOLUTE_PATH__."libs/ATF/libs/php_writeexcel/class.writeexcel_worksheet.inc.php";

			$fname = tempnam(__TEMPORARY_PATH__, __TEMPLATE__.ATF::$usr->getID());

			$workbook = new writeexcel_workbook($fname);
			$worksheet =& $workbook->addworksheet('Export Optima');

			if ($data) {
				// mise en place des noms de champs
				$row_data=array();
				foreach ($data[0] as $k_ => $i_) {
					$k_=explode(".",$k_);
					//si il fait parti des colonnes bloquées, on l'affiche pas
					if(!in_array(($k_[1]?$k_[1]:$k_[0]),$this->colonnes['bloquees']['export'])){
						if(!$k_[1]){
							$row_data[] =$k_[0];
						} else {
							$row_data[] =$k_[1];
						}
					}
				}
				$worksheet->write_row("A1", array_map("utf8_decode",$row_data));
				$row++;

				//et ensuite les données
				foreach ($data as $k => $i) {
					$row++;
					$row_data=array();
					foreach ($i as $k_ => $i_) {
						$k_=explode(".",$k_);
						//si il fait parti des colonnes bloquées, on l'affiche pas
						if(!in_array($k_[1],$this->colonnes['bloquees']['export'])){
							$class = util::class_from_id($k_[1]);
							$row_data[]=$i_;
						}
					}
					$worksheet->write_row("A".$row, array_map("utf8_decode",$row_data));
				}
			}

			$workbook->close();

			header("Content-Type: application/x-msexcel; name=".str_replace(" ","_",ATF::$usr->trans($this->table,"module")).".xls");
			header("Content-Disposition: attachment; filename=".str_replace(" ","_",ATF::$usr->trans($this->table,"module")).".xls");
			header("Cache-Control: private");
			$fh=fopen($fname, "rb");
			fpassthru($fh);
			unlink($fname);
		}
	}

	/**
	* Permet d'exporter les données concernant les statistiques
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function export_stats($infos,&$s){
		require_once __ABSOLUTE_PATH__."libs/ATF/libs/php_writeexcel/class.writeexcel_workbook.inc.php";
		require_once __ABSOLUTE_PATH__."libs/ATF/libs/php_writeexcel/class.writeexcel_worksheet.inc.php";

		$fname = tempnam(__TEMPORARY_PATH__, __TEMPLATE__.ATF::$usr->getID());
		$workbook = new writeexcel_workbook($fname);
		$worksheet =& $workbook->addworksheet('Export Optima');

		//si on a envoyé les données on les utilise, sinon on les prends par la méthode dynamique de stats
		if($infos['donnees']){
			$donnees=$infos['donnees'];
		}else{
			$donnees=$this->stats(false,$infos['type']);
		}

		if ($donnees) {
			// mise en place des noms de champs
			$row_data=array();
			//1e case doit être vide
			$row_data[] = "";
			foreach ($donnees['categories']['category'] as $k_ => $i_) {
				$row_data[] =$i_['label'];
			}
			$worksheet->write_row("A1", array_map("utf8_decode",$row_data));
			$row++;

			//et ensuite les données
			foreach ($donnees['dataset'] as $k => $i) {
				$row++;
				$row_data=array();
				$row_data[]=$k;
				foreach ($i['set'] as $k_ => $i_) {
					$row_data[]=$i_['value'];
				}
				$worksheet->write_row("A".$row, array_map("utf8_decode",$row_data));
			}
		}

		$workbook->close();
		header("Content-Type: application/x-msexcel; name=".str_replace(" ","_",ATF::$usr->trans($this->table,"module")).".xls");
		header("Content-Disposition: attachment; filename=".str_replace(" ","_",ATF::$usr->trans($this->table,"module")).".xls");
		$fh=fopen($fname, "rb");
		fpassthru($fh);
		unlink($fname);
	}

	/**
	 * Retourne les colonnes inversées
	 * @author Yann GAUTHERON <ygautheron@absystech.fr>, Nicolas BERTEMONT <nbertemont@absystech.fr>
	 * @param $s session
	 * @param $alias_for_key VRAI mettra les alias comme key
	 * @param $vue si nous avons une vue, nous mettons les colonnes qui y sont contenues
	 * @param $id Identifiant
	 * @return array
	 */
	public function colonnes_simples(&$s,$alias_for_key=false,$vue=NULL/*,$id=NULL*/) {
		if (!isset($this->colonnes["fields_column"])) {
			// Ne s'executera qu'une fois pour les singletons de type classes (sans classe spécifique)
			$this->q->setLimit(1)->setDimension('row');
			$this->colonnes["fields_column"]=array_keys($this->select_all());
//			array_walk($this->colonnes["fields_column"],create_function('$a,$b="'.$this->table.'"', 'return "'.$b.'.'.$a.'";'));
	//		array_walk($this->colonnes["fields_column"], create_function('&$v, $t', 'if ($k{0}!="_"){$arr_rtn["_".$v[\'ID\']]=$v;unset($arr_rtn[$k]);}'), &$this->colonnes["fields_column"],$this->table);
			$this->colonnes["fields_column"] = array_flip($this->colonnes["fields_column"]);
			unset($this->colonnes["fields_column"]["id_".$this->table]);
			$this->colonnes["fields_column"] = array_flip($this->colonnes["fields_column"]);
			array_walk($this->colonnes["fields_column"], create_function('&$v,$k,$table', '$v=$table.".".$v;'), $this->table);
			$this->fieldstructure();
		}
		if(is_array($vue) && isset($vue['order'])){
			foreach ($vue['order'] as $i) {
				$return[]=$i;
			}
		} else {
			foreach ($this->colonnes["fields_column"] as $k => $i) {
				//no_list : visible dans le menu 'vue', mais non affiché par défaut dans le listing, mais dans fields_column pour conserver les caractéristiques
				if(!$i['no_list'])$return[]=$k;
			}
		}

		if ($s) { // Si on passe la session, on souhaite un retour avec la forme de fields_column
			foreach ($return as $k => $i) {
				unset($return[$k]);
				$alias = $i;
				if ($alias_for_key && is_array($this->colonnes["fields_column"][$i]) && isset($this->colonnes["fields_column"][$i]["alias"])) {
					$alias = $this->colonnes["fields_column"][$i]["alias"];
				}

				//si le nom du champs n'est pas dans le field_colum on va chercher ses informations dans dans le $this->desc
				if(isset($this->colonnes["fields_column"][$i])){
					//if (!$this->colonnes["fields_column"][$i]["custom"] || $this->colonnes["fields_column"][$i]["type"]=="file") { // Attention, si c'est un custom ça foire les filtres, donc on échappe
						$return[$alias]=$this->colonnes["fields_column"][$i];
					//}
				}else{
					$infos=explode('.',$i);
					$return[$i]=ATF::db($this->db)->table2htmltable(ATF::getClass($infos[0])->desc,$infos[1]);
				}
				$this->checkColonnePrivilege($return,$alias);
			}
		}

		return $return;
	}

	/**
	* méthode permettant de faire les graphes des différents modules, dans statistique
	* @author DEV <dev@absystech.fr>, Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function stats($stats=NULL,$type=NULL,$widget=false,$block=true) {
		//Blocage du cr
		if ($block) {
			ATF::$cr->block("top");
			ATF::$cr->block("generationTime");
		}
		if (!$stats) {
			$this->q->reset();
			//on récupère la liste des années que l'on ne souhaite pas voir afficher sur les graphes
			//on les incorpore ensuite sur les requêtes adéquates
			if(!$widget)ATF::stats()->conditionYear(ATF::stats()->liste_annees[$this->table],$this->q,"`date`",$type);

			switch ($type) {
				case "user":
					$this->q->addField("YEAR(`date`)","year")
							->addField("MONTH(`date`)","month")
							->addField("COUNT(*)","nb")
							->addCondition($this->table.".id_user",ATF::$usr->getID())
							->addGroup("year")->addGroup("month");
					$stats['DATA'] = parent::select_all();

					$this->q->reset("field,group");
					$this->q->addField("DISTINCT YEAR(`date`)","year");
					$stats['YEARS'] =parent::select_all();

					break;

				case "users":
					$this->q->reset();
					$this->q->addField("CONCAT(`user`.`prenom`,' ',`user`.`nom`)","label")
							->addField("user.id_user","year")
							->addField("DATE_FORMAT(`".$this->table."`.`date`,'%Y')","y")
							->addField("DATE_FORMAT(`".$this->table."`.`date`,'%m')","month")
							->addField("COUNT(*)","nb")
							->addJointure($this->table,"id_user","user","id_user")
							->addCondition("TO_DAYS(NOW())-TO_DAYS(`".$this->table."`.`date`)","365",NULL,"sub_date","<",false,false,true)
							->addGroup("year")->addGroup("month");
					$stats['DATA'] = parent::select_all();

					$this->q->reset("field,group,where");
					$this->q->addField("DISTINCT ".$this->table.".`id_user`","years");
					$stats['YEARS'] = parent::select_all();

					break;

				default:
					$this->q->addField("YEAR(`date`)","year")
							->addField("MONTH(`date`)","month")
							->addField("COUNT(*)","nb")
							->addGroup("year")
							->addGroup("month");
					if($widget){
						//$this->q->addCondition("YEAR(`date`)",date("Y",time())-1,"OR",false,">=");
						ATF::stats()->conditionYear(array(date("Y")=>1,(date("Y")-1)=>1),$this->q,"date");
					}
					$stats['DATA'] = parent::select_all();
					$this->q->reset("field,group");
					$this->q->addField("DISTINCT YEAR(`date`)","years");
					$stats['YEARS'] = parent::select_all();
			}
		}

		$graph['params'] = array(
			"bgColor"=>'EFEFEF'
			,"caption"=>ATF::$usr->trans($this->table,module)
			,"subCaption"=>"Nombre de créations par mois"
		);

		if ($stats["categories"]) {
			$graph['categories']= $stats["categories"];
		} else {
			if ($widget) {
				foreach (ATF::stats()->recupMois($type) as $i) {
					$graph['categories']["category"][] = array("label"=>substr($i,0,1),"hoverText"=>$i);
				}
			} else {
				foreach (ATF::stats()->recupMois($type) as $k=>$i) {
					$graph['categories']["category"][] = array("label"=>substr($i,0,4),"hoverText"=>$i);
				}
			}
		}

		switch ($type) {
			case "user":
				$graph['params']["subCaption"]="Nombre de créations par mois pour ".ATF::user()->nom(ATF::$usr->getID());
				break;

			case "CA":
				$graph['params']["subCaption"]="CA par mois";
				break;

			case "marge":
				$graph['params']["subCaption"]="Marge par mois";
				break;


			case "pourcentage":
				$graph['params']["decimalPrecision"] = 2;
				$graph['params']["subCaption"]="Marge en pourcentage par mois";
				break;

			case "users":
				$graph['params']["subCaption"]="Nombre de créations par mois par utilisateur sur un an glissant";
				foreach ($graph['categories']["category"] as $k => $i) {
					if ($k-date("m",time())<0) {
						$y = date("Y",time());
					} else {
						$y = date("Y",time())-1;
					}
					$graph['categories']["category"][$k] = array("label"=>$i["label"].substr($y,2),"hoverText"=>$i["hoverText"]." ".$y);
				}
				break;
		}

		// Si on demande une légende réduite (widget)
		if ($widget) {
			unset($graph['params']["subCaption"],$graph['params']["caption"]);
			$graph['params']['showLegend'] = "0";
			$graph['params']['bgAlpha'] = "0";
		}

		/* parametres graphe */
		$this->paramGraphe($dataset_params,$graph);

		/* ajout des donnees */
		$this->ajoutDonnees($graph,$stats,$dataset_params,$type);
		return $graph;
	}

	/**
	* Contient les paramètres nécessaire à l'affichage des graphes
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array dataset_params : va contenir les caratéristiques concernant le graphe
	* @param array graph : contient toutes les données utiles à l'affichage du graphe
	*/
	public function paramGraphe(&$dataset_params,&$graph){
		$dataset_params = array(
			"showValues"=>'0'
			,"numberPrefix"=>''
			,"numberSuffix"=>''
			,"alpha"=>'90'
			,"anchorAlpha"=>0
		);

		$graph['params'] = array_merge($graph['params'],array(
			"showValues"=>'0'
			,"formatNumber"=>"1"
			,"formatNumberScale"=>"0"
			,"decimalSeparator"=>"."
			,"thousandSeparator"=>" "
			,"decimalPrecision"=>'2'
			,"anchorRadius"=>'4'
			,"anchorBgAlpha"=>'0'
			,"lineThickness"=>'2'
			,"limitsDecimalPrecision"=>'2'
			,"divLineDecimalPrecision"=>'2'
		));
	}

	/**
	* Mise en place des données structurées de manière à être interprété par le graphe
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array graph : contient toutes les données utiles à l'affichage du graphe
	* @param array stats : contient les datas et autres
	* @param array dataset_params : contient les caratéristiques concernant le graphe
	*/
	public function ajoutDonnees(&$graph,$stats,$dataset_params,$type){
		foreach ($stats['DATA'] as $val_) {
			$infos=ATF::stats()->intitule($val_["month"],$val_["label"],$val_["year"]);
			$val_["month"] = strlen($val_["month"])<2?"0".$val_["month"]:$val_["month"];
			if (!$graph['dataset'][$infos['int']]) {
				$graph['dataset'][$infos['int']]["params"] = array_merge($dataset_params,array(
					"seriesname"=>$infos['int']
					,"color"=>dechex(rand(0,16777216))
				));

				/* Initalisation de tous les set à 0 */
				if(method_exists($this,initGraphe)){
					$this->initGraphe($graph,$infos['int']);
				}else{
					ATF::stats()->initGraphe($graph,$infos['int'],$type);
				}
			}

			$graph['dataset'][$infos['int']]['set'][$val_["month"]] = array("value"=>$val_['nb'],"alpha"=>100,"titre"=>$infos['lib']." : ".$val_["nb"]);

			/* ajout de l'url */
			$graph['dataset'][$infos['int']]['set'][$val_["month"]]["link"]=urlencode($this->table.".html,stats=1&annee=".($val_["label"]?$val_["y"]:$val_["year"])."&mois=".$val_["month"]."&label=".$val_["label"]."&type=".$type);
		}
		asort($graph['dataset']);
	}

	/**
	* avoir les années pour lesquelles le module a été utilisé
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @return array
	*/
	public function get_years(){
		$champs=($this->table=="tache"?"horaire_fin":"date");
		$this->q->reset()
				->setStrict()
				->addField("YEAR( ".$this->table.".".$champs." )","year")
				->addCondition($champs,NULL,NULL,"non_null","IS NOT NULL")
				->addCondition($champs,"0",NULL,NULL,"!=",false,false,true)
				->addGroup("year")->addOrder("year");
		return parent::select_all();
	}

	/**
	* Permet de réinitialiser la current_class de la session (voir update_session)  regroupant ses valeurs
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @todo YGG : Trouver un moyen de rendre plus propre cette réinitialisation des lignes
	*/
	public function initialise_ligne(&$s){
		if (is_array($s[$this->table])) {
			unset($s[$this->table]);
		}
	}

	/**
	* Execute une moyenne, somme, min ou max d'un champ de select_all
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $field
	* @param array $aggregats array(avg|sum|min|max)
	* @return mixed
	*/
	public function select_aggregate($field,$aggregats) {
		return ATF::db($this->db)->select_aggregate($this,$field,$aggregats);
	}

	/**
	* Méthode qui permet de rassembler les colonnes de la table sur laquelle on se situe avec celle des tables jointes
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param type de filtrage des champs : colonne => pour filtrer les colonnes du select_all / donnee => poru filtrer les données affichées dans le select_all
	*/
	public function recup_colonnes($filtre="colonne"){
		//on va chercher toutes les colonnes de la table (bdd)
		//on affiche l'id que dans le cas où l'on créé un filtre, dans le cas d'une vue, on le cache, car si on le sélectionne, cela provoque des problèmes (car non géré)
		if($filtre=="colonne")$tableau=ATF::getClass($this->table)->table_structure("id_".$this->table);
		else $tableau=ATF::getClass($this->table)->table_structure();

		//concernant les colonnes du module en cours, on prends le field_column pour avoir aussi les colonnes remplies manuellement
		foreach($this->colonnes['fields_column'] as $cle_column=>$item_column){
			//condition permettant d'éviter l'affichage multiple d'un champs à filtrer
			//ex : si tracabilite.modification existe, vu que dans fields_column j'ai du l'ajouter sous le nom de modification, il ne faut pas l'afficher deux fois
			if(!$tableau[$cle_column] && !$tableau[$this->table.".".$cle_column]){
				$tableau[$cle_column]=$cle_column;
			}
		}

		//on retire les colonnes qui sont bloquées dans le select
		foreach($this->colonnes['bloquees']['filtre'][$filtre] as $cle=>$col_bloq){
			foreach($tableau as $cham=>$champs){
				//si le champs bloquées équivaut à un des champs à afficher, structuré par ex : id_user, tout comme tache.id_user
				if($cham==$col_bloq || $cham==$this->table.".".$col_bloq || ($filtre=="donnee" && $this->colonnes['fields_column'][$cham]["custom"]==true)){
					unset($tableau[$cham]);
				}
			}
		}

		$options[ATF::$usr->trans($this->table,'module')]=ATF::$usr->trans($tableau,$this->table);

		if ($this->jointure || $this->q->jointure || ATF::_s("pager")->q['gsa_'.$this->table.'_'.$this->table]) {
			if ($this->jointure) {
				$joint=$this->jointure;
			} elseif($this->q->jointure) {
				$joint=$this->q->jointure;
			} elseif(ATF::_s("pager")->q['gsa_'.$this->table.'_'.$this->table]) {
				//si on sélectionne un filtre, on peut récupérer les jointures qui s'y rapporte
				$joint=ATF::_s("pager")->q['gsa_'.$this->table.'_'.$this->table]->jointure;
			}

			foreach($joint as $key=>$item){
				if (ATF::getClass($item['table_right']) && !isset($this->colonnes['bloquees']['filtre']["table"][$item['table_right']])) {

					$tableau_joint=ATF::getClass($item['table_right'])->table_structure();
					//on retire les colonnes qui sont bloquées dans le select
					foreach(ATF::getClass($item['table_right'])->colonnes['bloquees']['filtre'][$filtre] as $cle=>$col_bloq){
						foreach($tableau_joint as $cham=>$champs){
							//si le champs bloquées équivaut à un des champs à afficher, structuré par ex : id_user, tout comme tache.id_user
							if($cham==$col_bloq || $cham==$item['table_right'].".".$col_bloq){
								unset($tableau_joint[$cham]);
								break;
							}
						}
					}
					$options[ATF::$usr->trans($item['table_right'],'module')]=ATF::$usr->trans($tableau_joint,$item['table_right']);
				}
			}
		}

		// Check des privilège sur colonne
		foreach($options as $kTable => $iTable) {
			foreach($iTable as $field => $fieldTrans) {
				$this->checkColonnePrivilege($options[$kTable],$field);
			}
		}

		return $options;
	}

	/**
	* Téléchargement de fichiers
	* @param int $infos l'identificateur de l'élément que l'on désire inséré
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/
	public function dl($infos=NULL,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL) {

		if ($infos["temp"] && !$infos["id_".$this->table]) { // Prévisu
			$id = ATF::$usr->getID();
			if (!$infos['field']) $infos["field"] = "rapport_de_simulation";
		} elseif (!$infos["id_".$this->table] && isset($infos['id'])) { // L'id est une string
			$id = $infos["id"];
		} else { // Fichier réel
			$item = $this->select($infos["id_".$this->table]);
			$id = $this->decryptId($infos["id_".$this->table]);
		}
		$target = $this->filepath($id,$infos["field"],$infos["temp"]);

		// Si demande du vrai fichier, et qu'on a pas de fichier en stock, et qu'il est demandé de le générer
		if (!$infos["temp"] && !filesize($target)) {
			if (is_array($this->files[$infos["field"]]) && $this->files[$infos["field"]]["type"]==="pdf" && ATF::pdf() instanceof pdf && method_exists(ATF::pdf(),$this->table)){
				if($this->files[$infos["field"]]["no_store"]===true){ // Seulement si demande expresse de ne pas stocker, génération dans temp
					$infos["temp"]=true;
					$id = ATF::$usr->getID();
					$target = $this->filepath($id,$infos["field"],$infos["temp"]);
					$this->store($s,$id,$infos["field"],ATF::pdf()->generic($this->table,$infos["id_".$this->table],true,$s,true),true);
				} else { // S'il n'existe pas, on regénère le fichier et on le store
					$this->store($s,$id,$infos["field"],ATF::pdf()->generic($this->table,$infos["id_".$this->table],true,$s));
				}
			}
		}
		if ($size = filesize($target)) {
			ob_clean();
			header("Pragma: public");
			header("Expires: 0");
			//header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: no-cache");
			header("Content-Type: application/force-download");
			header("Content-Type: application/octet-stream");
			header("Content-Type: application/download");
			$filename = $this->downloadedFilename($id,$infos["field"]);

			if ($this->files[$infos["field"]]["type"]) { // Type statique défini dans le constructeur de la classe
				$types = explode(",",$this->files[$infos["field"]]["type"]);
				$filename .= ".".array_pop($types);
			} elseif($item[$infos["field"]]) { // Type dynamique défini dans la valeur du champ de la base
				$filename = $item[$infos["field"]];
			} elseif($item["format"]) { // Type dynamique défini dans la valeur du champ de la base
				if (!preg_match("^".".".$item["format"]."^",$filename)) $filename .= ".".$item["format"];
			} elseif($infos["type"]) { // Sil e type est précisé en entrée
				$filename .= ".".$infos["type"];
			} else { //cas d'upload de fichier sans précision du type
				$filename .= ".zip";
			}

			if ($this->colonnes[$infos["field"]]["type"]) {
				header("Content-Type: application/".$this->colonnes[$infos["field"]]["type"]);
			} else {
				$finfo = finfo_open(FILEINFO_MIME_TYPE); // Retourne le type mime à la extension mimetype
				$ct = finfo_file($finfo, $target);
				header("Content-Type: ".$ct);
				if ($ext = util::getExtensionByContentType($ct)) {
					$filename = str_replace(".zip",$ext,$filename);
				}

				finfo_close($finfo);
			}
			header("Content-Disposition: attachment; filename=\"".addslashes($filename).'"');
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".$size);
			readfile($target);
		} else {
			echo ATF::$usr->trans('pas_fichier');
		}
	}

	/**
	* Téléchargement de fichiers pour TELESCOPE
	* @param int $infos l'identificateur de l'élément que l'on désire inséré
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @author Morgan Fleurquin <mfleurquin@absystech.fr>
	*/
	public function _dl($get, $post) {
		$item = $this->select($post["id_".$this->table]);
		$id = $this->decryptId($post["id_".$this->table]);

		$target = $this->filepath($id,$post["field"],$post["temp"]);

		// Si demande du vrai fichier, et qu'on a pas de fichier en stock, et qu'il est demandé de le générer
		if (!$post["temp"] && !filesize($target)) {
			if (is_array($this->files[$post["field"]]) && $this->files[$post["field"]]["type"]==="pdf" && ATF::pdf() instanceof pdf && method_exists(ATF::pdf(),$this->table)){
				if($this->files[$post["field"]]["no_store"]===true){ // Seulement si demande expresse de ne pas stocker, génération dans temp
					$post["temp"]=true;
					$id = ATF::$usr->getID();
					$target = $this->filepath($id,$post["field"],$post["temp"]);
					$this->store($s,$id,$post["field"],ATF::pdf()->generic($this->table,$post["id_".$this->table],true,$s,true),true);
				} else { // S'il n'existe pas, on regénère le fichier et on le store
					$this->store($s,$id,$post["field"],ATF::pdf()->generic($this->table,$post["id_".$this->table],true,$s));
				}
			}
		}
		if ($size = filesize($target)) {

			$filename = $this->downloadedFilename($id,$post["field"]);

			if ($this->files[$post["field"]]["type"]) { // Type statique défini dans le constructeur de la classe
				$types = explode(",",$this->files[$post["field"]]["type"]);
				$filename .= ".".array_pop($types);
			} elseif($item[$post["field"]]) { // Type dynamique défini dans la valeur du champ de la base
				$filename = $item[$post["field"]];
			} elseif($item["format"]) { // Type dynamique défini dans la valeur du champ de la base
				if (!preg_match("^".".".$item["format"]."^",$filename)) $filename .= ".".$item["format"];
			} elseif($post["type"]) { // Sil e type est précisé en entrée
				$filename .= ".".$post["type"];
			} else { //cas d'upload de fichier sans précision du type
				$filename .= ".zip";
			}

			if ($this->colonnes[$post["field"]]["type"]) {
				$return["strMimeType"] = "Content-Type: application/".$this->colonnes[$post["field"]]["type"];
			} else {
				$finfo = finfo_open(FILEINFO_MIME_TYPE); // Retourne le type mime à la extension mimetype
				$ct = finfo_file($finfo, $target);
				$return["strMimeType"] = $ct;
				header("Content-Type: ".$ct);
				if ($ext = util::getExtensionByContentType($ct)) {
					$filename = str_replace(".zip",$ext,$filename);
				}

				finfo_close($finfo);
			}



			$return["strFileName"] = $filename;
			$return["data"] = base64_encode(file_get_contents($target));
			return $return;
		} else {
			echo ATF::$usr->trans('pas_fichier');
		}
	}


	/**
	* Nom du fichier téléchargé
	* @param int $id Numéro de l'enregistrement
	* @param string $field Nom du champs (du fichier $this->files)
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/
	function downloadedFilename($id,$field) {

		switch ($field) {
			case "fichier_joint":
			case "fichier":
				if ($nom = $this->nom($id)) {
					return urlencode(substr($nom,0,42));
				}
			default:
				return $this->table."_".$field."_".date("YmdHis");
		}
	}

	/**
	* Stockage de fichiers
	* @param int $id Clé primaire d'enregistrement associé
	* @param string $field Nom du champ associé
	* @param string|array $data Données binaires, ou tableau de référence à un fichier uploadé
				string binaire
				tableau de type $_FILES[field]
					name
					tmp_name
	* @param boolean $temp Si VRAI il s'agit d'un fichier temporaire
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function store(&$s,$id,$field,$data,$temp=false) {
		$id = $this->decryptId($id);
		$field = basename($field); // protection si chemin relatif envoyé en paramètre
		$target = $this->filepath($id,$field,$temp);
		if ($temp && ATF::db($this->db)->isTransaction()) {
			// Si c'est une transaction, on mémorise qu'il faudra déplacer dans /data/ lors du commit
			// On continue le script ensuite pour que le fichier temporaire stocké avec l'ID du user soit rename avec l'ID de l'enregistrement créer lors de la transaction
			// Comme ça au moment du commit on peut migrer le fichier dans data.
			// Par contre si il y a rollback, le fichier créer plus bas reste dans le temp, ce qui doit être gérer a l'occasion.
			ATF::db($this->db)->getQueue()->moveFile($target,$this->filepath($id,$field));
		}
		if (!util::mkdir(dirname($target))) {
			throw new errorATF("Le dossier '".dirname($target)."' ne s'est pas créé ");
		}

		if (file_exists($target)) {
			if(unlink($target)) {
				// Suppression du fichier
				if (is_array($this->files[$field]['convert_from'])) {
					// Suppression des miniatures générées
					$cmd = "rm -f ".$target.".*-*";
					`$cmd`;
				}
			} else {
				throw new errorATF('Problème lors de la suppression du fichier '.$field);
			}
		}
		// Gestion des preview et STOCKAGE DANS TEMP
		if (is_array($data)) {
			// On vérifie que le fichier fournit ne dépasse pas la taille max
			if (isset($this->files[$field]['max_size'])) {
				if($data["size"]>$this->files[$field]['max_size']){
					throw new errorATF("Le fichier uploadé dépasse la taille autorisée (".$this->files[$field]['max_size'].") pour un fichier ");
				}
			}
			if ($this->table=="ged" || $this->table=="ged_fichier") {
				return rename($data["tmp_name"],$target);
			} elseif (isset($this->files[$field]['type'])) {
				if (is_array($this->files[$field]['convert_from'])) {
					// Plusieurs formats attendus
					if (!gd::conversion($data["tmp_name"],$target,NULL,NULL,$this->files[$field]['type'])) {
						throw new errorATF("Le fichier uploadé (".$data["name"].") ne correspond pas à l'un des formats attendus (".implode(",",$this->files[$field]['convert_from']).")");
					}
					return true;
				} else {
					//si plusieurs extensions sont prévues, on check chacune d'elle pour voir si celui du fichier est correct
					$types = $this->files[$field]['type'];
					if (is_array($types)) {
						$types = implode(",",$types);
					}
					if(preg_match("/\,/",$types)){
						foreach(explode(",",$types) as $num=>$type){
							if(preg_replace("/^(.*)\.(.*)$/", "\\2", strtolower($data["name"]))==$type) {
								//on stocke le type de fichier
								$s[$this->table]["upload"]['type']=$type;
								// Si le type est défini, alors on stocke directement la chaîne
								return rename($data["tmp_name"],$target);
							}
						}
						//vu qu'il y a un return dans le cas ou le type est bon, pas besoin de refaire une condition dans le cas ou aucun type ne corresponds
						throw new errorATF("Le fichier uploadé (".$data["name"].") ne correspond pas au format attendu (".$types.")");


					}else{
						// On vérifie que le fichier fournit correspond au format attendu
						if(preg_replace("/^(.*)\.(.*)$/", "\\2", strtolower($data["name"]))==$types) {
							// Si le type est défini, alors on stocke directement la chaîne
							$result = rename($data["tmp_name"],$target);
						} else {
							throw new errorATF("Le fichier uploadé (".$data["name"].") ne correspond pas au format attendu (".$types.")");
						}
					}
				}
			} elseif(class_exists("ZipArchive")) {
				// Si aucun type défini pour ce $this->files, on s'attend à aucun type de fichier particulier
				// Et on a besoin du nom de fichier pour le zipper avec son filename original
				$zipfile = new ZipArchive();

				$fichier_temp=$this->filepath($id,$data['name'],true);
				if(util::copy($data['tmp_name'],$fichier_temp)===FALSE){
					throw new errorATF('Problème lors de la création du fichier '.$field,39756);
				}
				touch($target); // Nécessaire pour créer le fichier avant de l'open
				if (!$zipfile->open($target) || !$zipfile->addFromString($data['name'], file_get_contents($fichier_temp)) || !$zipfile->close()) {
				   throw new errorATF("Problème avec la création ou l'ajout de fichier dans le zip");
				}
				$result = unlink($fichier_temp);
			}
		// STOCKAGE DU FICHIER DANS DATA
		} else {
			if($data){
				// Placement du fichier
				if(file_put_contents($target,$data)){
					return true;
				}else{
					throw new errorATF('Problème création fichier (certainement problème droit) - Target : '.$target,1843);
				}
			}else{
				throw new errorATF('Pas de donnée à enregistrer',1842);
			}
		}

		return $result;
	}

	/**
	* Retourne vrai si le fichier existe
	* @param int $id Clé primaire d'enregistrement associé
	* @param string $field Nom du champ associé
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/
	public function file_exists($id,$field=NULL,$temp=false) {
		$id = $this->decryptId($id);
		$target = $this->filepath($id,$field,$temp);
		return file_exists($target);
	}

	/**
	* Permet d'uploader un fichier dans un formulaire extjs
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function uploadExt(&$infos,&$s,$files=NULL,&$cadre_refreshed=NULL){
		$infos["display"]=true;
		$return=$infos;
		try {
			$return["result"]=$this->store($s,ATF::$usr->getID(),$infos["field"],$files[$infos["field"]],true);

			if (!$return[$infos["field"]]) $return[$infos["field"]]=$files[$infos["field"]]['name'];
			$return["success"]=true;
		} catch (errorATF $e) {
			$return[$infos["field"]]=ATF::$usr->trans("problem_upload");
			$return["success"]=false;
			ATF::$msg->addWarning($e->getMessage());
		}
		foreach($return['extTpl'] as $id=>$tpl){
			$return['extTpl'][$id] = "";
			if (is_array($tpl)) {
				foreach ($tpl as $k=>$i) {
					if ($k) $return['extTpl'][$id] .= ",";
					ATF::$html->assign("filename",$return[$infos["field"]]);
					$return['extTpl'][$id].=ATF::$html->fetch($i.".tpl.js");
				}
			} else {
				ATF::$html->assign("filename",$return[$infos["field"]]);
				$return['extTpl'][$id]=ATF::$html->fetch($tpl.".tpl.js");
			}
		}

		/* Affichage en warning du pourquoi cela a échoué */
		$return["warning"]=ATF::$msg->getWarnings();

		return json_encode($return);
	}

	/**
	* Permet d'uploader un fichier en XHR
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos[field] Nom du champ cible
	*/
	public function uploadXHR(&$infos,&$s,$files=NULL,&$cadre_refreshed=NULL){
		$infos["key"]=$infos["field"];

		$path = "/tmp/uploadXHR-".ATF::$codename."-".date("YmdHis")."-".ATF::$usr->getID();

		util::transverseFile($path);
//		file_put_contents($path,file_get_contents('php://input'));
		$files[$infos["field"]]=array(
			"name"=>utf8_encode(ATF::_srv("HTTP_X_FILE_NAME"))
			,"type"=>ATF::_srv("HTTP_X_FILE_TYPE")
			,"tmp_name"=>$path
			,"error"=>0
			,"size"=>ATF::_srv("HTTP_X_FILE_SIZE")
		);

		return $this->uploadExt($infos,$s,$files,$cadre_refreshed);
	}

	/**
	* Permet de supprimer un fichier uploadé
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	*/
	public function delete_uploadExt(&$infos,&$s,$files=NULL,&$cadre_refreshed=NULL){
		$infos["display"]=true;
		$return=$infos;
		if ($infos['identifiant'] && $infos['table']) {
			$t = ATF::getClass($infos['table']);
			$id = $t->decryptId($infos['identifiant']);
			// Fichier stocké ET miniatures
			$path = $this->filepath($id,$infos["key"]);
			if (file_exists($path)) {
				unlink($path);
				$return["result"] = true;
				$return["success"] = true;
				// Suppression du fichier
				if (is_array($t->files[$infos["key"]]['convert_from'])) {
					// Suppression des miniatures générées
					`rm -f $path.*`;
				}
			}
		}
		// Fichier temporaire aussi au cas où c'est une insertion
		$path = $this->filepath(ATF::$usr->getID(),$infos["key"],true);
		if (file_exists($path)) {
			unlink($path);
		}

		foreach($return['extTpl'] as $id=>$tpl){
			if (is_array($tpl)) {
				$return['extTpl'][$id] = "[";
				foreach ($tpl as $k=>$i) {
					if ($k) $return['extTpl'][$id] .= ",";
					$return['extTpl'][$id] .= ATF::$html->fetch($i.".tpl.js");
				}
				$return['extTpl'][$id] .= "]";
			} else {
				$return['extTpl'][$id]=ATF::$html->fetch($tpl.".tpl.js");
			}
		}
		/* Affichage en warning du pourquoi cela a échoué */
		$return["warning"]=ATF::$msg->getWarnings();

		return json_encode($return);
	}
	/**
	* Méthode qui déplace les fichiers de temp vers data ou qui génère le pdf
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @author Quentin JANON <qjanon@absystech.fr>
	* @todo effacé tous les fichiers associés lors de l'upload d'un nouveau fichier.
	* @param int $id Clé primaire d'enregistrement associé
	* @param array $s de la session
	* @param boolean $preview s'il s'agit d'une prévisualisation
	*/
	public function moveMultipleFiles($id,$field){
		// Récupération des fichier a mettre dans le zip
		$dir = dirname($this->filepath(ATF::$usr->getID(),"*",true));
		if ($dir) {
			foreach (scandir($dir) as $k_=>$i_) {
				$f = explode(".",$i_);
				if ($f[0]!=ATF::$usr->getID()) continue;

				$filename = str_replace(ATF::$usr->getID().".".$field.".","",$i_);
				$fileToZip[$filename] = $dir."/".$i_;
				$filesToRm[] = $dir."/".$i_;
			}
		}

		if ($fileToZip && class_exists("ZipArchive")) {
			$zip = new ZipArchive();
			$zipFileName = $this->filepath($id,$field);
			if (!file_exists($zipFileName)) {
				util::file_put_contents($zipFileName,""); // Nécessaire pour créer le fichier avant de l'open
			}

			$zip->open($zipFileName);

			foreach ($fileToZip as $k_=>$i_) {
				$zip->addFile($i_,$k_);
			}

			$zip->close();
			foreach ($fileToZip as $k_=>$i_) {
				util::rm($i_);
			}
		}
	}

	/**
	* Méthode qui déplace les fichiers de temp vers data ou qui génère le pdf
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @author Quentin JANON <qjanon@absystech.fr>
	* @todo effacé tous les fichiers associés lors de l'upload d'un nouveau fichier.
	* @param int $id Clé primaire d'enregistrement associé
	* @param array $s de la session
	* @param boolean $preview s'il s'agit d'une prévisualisationCléodis
	*/
	public function move_files($id,&$s,$preview=false,$filestoattach=NULL,$field=false,$paramSpecifique=false){
		foreach($this->files as $key=>$item){
			if ($item['multiUpload'] && !$preview) {
				self::moveMultipleFiles($id,$key);
			} else {
				if(!$field || $field==$key){
					// Reset de data
					$data = NULL;
					if($paramSpecifique){
						$param=$paramSpecifique;
					}else{
						$param=$id;
					}
					if(isset($filestoattach[$key]) && $filestoattach[$key] && !$preview){
						if($filestoattach[$key]!=="undefined" && $filestoattach[$key]!=="true"){
							// Fichier téléchargé par l'utilisateur
							$data = file_get_contents($this->filepath(ATF::$usr->getID(),$key,true));
						}
					} elseif (is_array($item) && $item["type"]==="pdf" && !$item["no_store"] && !$item["no_generate"] && ATF::pdf() instanceof pdf && method_exists(ATF::pdf(),$this->table)){
						// Fichier PDF généré
						$data = ATF::pdf()->generic($this->table,$param,true,$s,$preview);
					} elseif (is_array($item) && $item["type"]==="pdf" && !$item["no_store"] && !$item["no_generate"] && ATF::pdf() instanceof pdf && method_exists(ATF::pdf(),$key)){
						// Fichier PDF généré
						$data = ATF::pdf()->generic($key,$param,true,$s,$preview);
					} elseif ($item["obligatoire"]) {
						if(!$field){
							$this->delete($id);
						}

						$probleme[] = "'".ATF::$usr->trans($key)." (".$this->table.")'";
						throw new errorATF(util::mbox(
													loc::mt(ATF::$usr->trans("file_manquant"),array("data"=>implode(", ",$probleme)))
													,ATF::$usr->trans("attention_file_obligatoire_manquant")
													),103
										);
					}

					// On stocke dans le folder /temp/ lorsqu'on est en transaction, ou en preview. Dans le cas d'une transaction, c'est le $queue->generate() qui déplacera le fichier dans /data/
					if ($data) {
						// Suprression des fichiers présents avant
						gd::cleanThumbs($this->table,$id,$key);
						self::store($s,$id,$key,$data,$preview || ATF::db($this->db)->isTransaction());
					}
				}
			}
		}
	}

	/**
	* Génère les PDF de manière arbitraire
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @return void
	*/
	public function generatePDF($infos,&$s){
		$this->move_files($infos["id"],$s,false);
	}

	/**
	* Permet de faire une insertion rapide via la modalbox
	* modifier par Nico, pour raffraîchir le champs autocomplété, affichant ainsi la donnée nouvellement insérée
	* @author mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	* @return array de la modalbox qui permet d'insérer les champs
	* @todo refactoriser
	*/
	public function speed_insert_modalbox($infos=NULL,&$s=NULL,$files=NULL,&$cadre_refreshed){
		//Blocage du cr
		ATF::$cr->block("top");
		ATF::$cr->block("generationTime");

		//Construction de la modalBox
		$data['donnees']=array('name'=>$infos['name']
            				,'key'=>$infos['key']
                            ,'count'=>$infos['count']
                            ,'extra'=>$infos['extra']
                            ,'noname'=>$infos['noname']
                            ,'item'=>$infos['item']
                            ,'condition_field'=>$infos['condition_field']
                            ,'condition_value'=>$infos['condition_value']
							,'table_request'=>$infos['table_request']);
		unset($infos['name'],$infos['key'],$infos['count'],$infos['extra'],$infos['noname'],$infos['item'],$infos['condition_field'],$infos['condition_value'],$infos['table_request']);
		$data["field_sup"]=$infos;
		$data["table_insert"]=$this->table;
		return array("modalbox"=>util::mbox(
			"speed_insert"
			,ATF::$usr->trans($data['table_insert'])." | ".ATF::$usr->trans("speed_insert")
			,array("method"=>"post")
			,$data
		));
	}

	/**
	* Permet de faire une insertion rapide en extjs
	* @author mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @return une nouvelle fenêtre
	*/
	public function speed_insert_template(&$infos){
		ATF::$html->array_assign($infos);
		ATF::$html->assign("table",$this->table);
		$infos["display"]=true;
		return ATF::$html->fetch("generic-speed_insert.tpl.htm");
	}

	/**
	* Permet de faire un envoi rapide de mail en extjs
	* @author mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @return une nouvelle fenêtre
	*/
	public function quick_mail_template(&$infos){
		ATF::$html->array_assign($infos);
		ATF::$html->assign("table",$this->table);
		$infos["display"]=true;
		return ATF::$html->fetch("generic-quick_mail.tpl.htm");
	}

	/**
	* Tableau des champs du quickmail
	* @author mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @return array : tableau formaté
	*/
	public function quick_mail_template_field(){
		$tab = array(
			'email'=>array("xtype"=>'textfield')
			,'emailCopie'=>array("xtype"=>'textfield','default'=>ATF::$usr->get("email"))
			,'objet'=>array("xtype"=>'textfield')
			,'texte'=>array("xtype"=>'htmleditor')
		);

		return $tab;
	}

	/**
	* Retourne le chemin vers l'icone du module la classe courante
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param $size Taille en pixels
	* @return string
	*/
	public function icone($size=16){
		/*if (isset($this->namespace) && $this->namespace) {
			$prefix = $this->namespace."___"; // C'est tout ce que j'ai trouvé de rapide pour différencier les modules de namespace rapidement !
		}
		return module::iconePath($prefix.$this->table,$size);*/
		return module::iconePath($this->table,$size);
	}

	/**
	* Retourne true c'est à dire que la modification est possible
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $event événement concerné
	* @param int $id de l'enregistrement concerné
	* @param array $infos Informations supplémentaires permettant d'évaluer
	* @return boolean à true si ok
	*/
	public function is_active($event,$id=false,$infos=NULL){
		if($id){
			$id = $this->decryptId($id);
		}
		if (!$infos) {
			$infos = ATF::_r();
		}
		//log::logger("gsa_".implode("_affaire_|gsa_",$this->can_insert_from)."_affaire_  =>  ".$infos["pager"]." ".preg_match("/gsa_".implode("_".$this->name()."_|gsa_",$this->can_insert_from)."_".$this->name()."_/",$infos["pager"]),ygautheron);
		if($event!=="insert" || // Dans le cas de l'insert
			!$this->can_insert_from // qui ne peut s'effectuer que depuis certains modules doit avoir une clé étrangère nécessaire
			|| $infos["parent_name"] && in_array($infos["parent_name"],$this->can_insert_from) && $infos["id"] // En appel ajax de l'onglet => Il faut le parent_name et un id
			|| $infos["pager"] && preg_match("/gsa_".implode("_".$this->name()."_|gsa_",$this->can_insert_from)."_".$this->name()."_/",$infos["pager"]) // En appel ajax de l'onglet rafraichi => on check le nom conforme du pager
			|| $this->is_activeFKFound($infos,$this->can_insert_from) // Dans le $infos[$table]
		){
			$method="can_".$event;
			if(method_exists($this,$method)){
				//dans le cas du delete, si on n'a pas d'id précisé, le bouton doit être visible
				if($event=="delete" && !$id){
					return true;
				}else{
					try {
						return $this->$method($id,$infos);
					} catch (errorATF $e) {
						return false;
					}
				}
			}else{
				return true;
			}
		} else {
			return false;
		}
	}

	/**
	* Retourne true si au moins un ID des tables passées en paramètres est trouvé
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos Informations supplémentaires permettant d'évaluer
	* @param array $tables
	* @return boolean à true si ok
	*/
	public function is_activeFKFound($infos,$tables){
		foreach ($tables as $t) {
			if ($infos["id_".$t] || $infos[$this->table]["id_".$t]) {
				return true;
			}
		}
		return false;
	}

	/**
	* Méthode retournant l'initisalisation d'une google map avec des points positionnés
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	*	string table Nom du module
	*	string pager Nom du pager
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	*/
	public function gmap($infos,&$s=NULL,$files=NULL,&$cadre_refreshed) {
		ATF::$html->array_assign($infos);
		return ATF::$html->fetch("gmap.tpl.js");
	}

	/**
	* Génére la latitude et longitude d'une donnée d'un module
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param string $table : nom de la table concernée
	* @param integer $id_table : id de la table dont on recherche les coordonnées
	*/
	public function genLatLong($id_table){
		$new_c=new classes($this->table);
		$new_c->q->addCondition('id_'.$this->table,$id_table);
		$donnees=$new_c->select_all();
		if ($donnees[0]['latitude']!="-1.00") {
			if ($donnees[0]['adresse'] && $donnees[0]['ville']){
			    ATF::curl()->curlInit("http://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($donnees[0]['adresse']).",".urlencode($donnees[0]['ville']).",".$donnees[0]['id_pays']."&sensor=false");
                ATF::curl()->curlSetopt(CURLOPT_RETURNTRANSFER, true);
                $result = ATF::curl()->curlExec();
                ATF::curl()->curlClose();
				//$result=file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($donnees[0]['adresse']).",".urlencode($donnees[0]['ville']).",".$donnees[0]['id_pays']."&sensor=false");
				$coor=json_decode($result);
				if($coor->status == "OK"){
					$this->update(array("id_".$this->table=>$id_table,"latitude"=>$coor->results[0]->geometry->location->lat,"longitude"=>$coor->results[0]->geometry->location->lng));
					return array('lat'=>$coor->results[0]->geometry->location->lat,'long'=>$coor->results[0]->geometry->location->lng);
				}
			}
			$this->update(array("id_".$this->table=>$id_table,"latitude"=>"-1.00","longitude"=>"-1.00"));
		}
		return false;
	}

	/**
	* Retourne la liste des enregistrements à proximité d'une autre
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $id
	* @param int $seuil
	* @return array, FALSE si pas possible
	*/
	public function getProximite($id,$seuil=0.01){
		$item = $this->select($id,"latitude,longitude");
		if ($item["latitude"]=="-1") {
			return false;
		}
		if (!$item["latitude"]) {
			$this->genLatLong($id);
			$item = $this->select($id,"latitude,longitude");
		}
		if ($item["latitude"]=="-1") {
			return false;
		}
		return $this->getProximiteFromXY($item["latitude"],$item["longitude"],$seuil);
	}

	/**
	* Retourne la liste des enregistrements à proximité de coordonnées x et y
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $latitude
	* @param int $longitude
	* @param int $seuil
	* @return array, FALSE si pas possible
	*/
	public function getProximiteFromXY($latitude,$longitude,$seuil=0.1){
		$distance = "SQRT(POW(ABS(latitude-".$latitude."),2)+POW(ABS(longitude-".$longitude."),2))";
		$this->q->reset()
			->addField("societe.id_societe,latitude,longitude,societe,adresse,adresse_2,adresse_3,cp,ville,id_pays")
			//->addField($distance,"distance")
			//->addOrder("distance","asc")
			->where('latitude',"-1","AND",false,"!=")
			->where($distance,$seuil,"AND","proximite","<")
		;
		return $this->select_all();
	}

	/**
	* Méthode retournant uniquement les panels qui ont au moin un contenu
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos De type $infos[$this->table]
	*/
	public function panels($infos) {
		foreach ($this->colonnes["panel"] as $k => $i) {
			foreach ($i as $k_ => $i_) {
				if ($infos[$k_] || $i_["custom"]) {
					$panels[$k][$k_]=$i_;
				}
			}
		}
		return $panels;
	}

	/**
	* Méthode ajoutant le contenu de $requests dans la session
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	*/
	public function session_requests($infos,&$s){
		$s["requests"]=$infos;
	}

	/**
	* Insertion spécifique à optima
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @param array $noMoveFiles Désactive le move_files quand il doit être fait manuellement. Exemple : Insert de devis, pas de move files car on doit le faire après l'insertion des lignes de devis.
	* @return int L'id inséré
	*/
	public function insert($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL,$nolog=false,$noMoveFiles=false) {
		$this->infoCollapse($infos);

		if ($infos["quick_insert"]) {
			// Si c'est un quickinsert, on ne met pas à jour le top
			ATF::$cr->rm("top");
		}

		if (method_exists($this,"can_".__FUNCTION__) && !$this->can_insert()) {
			throw new errorATF(loc::mt(ATF::$usr->trans("probleme_is_active",$this->table),array("table"=>ATF::$usr->trans($this->table,module),"function"=>ATF::$usr->trans(__FUNCTION__))));
		}

		if(isset($infos['filestoattach']) && is_array($infos['filestoattach'])){
			$insert_files=$infos['filestoattach'];
			unset($infos['filestoattach']);
		}
		$id = parent::insert($infos,$s,$files,$cadre_refreshed,$nolog);

		//UPLOAD D'UN FICHIER
		if ($this->files && ($insert_files || $this->files["preview"]) && !$noMoveFiles){
			$this->move_files($id,$s,false,$insert_files);
		}

		// Ajout d'une notice dans le cas d'un appel AJAX (car cadre_refreshed sollicité)
		if (is_array($cadre_refreshed) && is_array($s) && ATF::$usr instanceof usr && $id) {
			ATF::$msg->addNotice(
				loc::mt(ATF::$usr->trans("notice_insert_success"),array("record"=>$this->nom($id)))
				,ATF::$usr->trans("notice_success_title")
			);
		}
		return $id;
	}

	/**
	* Modification spécifique à optima
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @return boolean TRUE si cela s'est correctement passé
	*/
	public function update($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false) {
		$this->infoCollapse($infos);
		if(isset($infos['filestoattach']) && is_array($infos['filestoattach'])){
			$insert_files=$infos['filestoattach'];
			unset($infos['filestoattach']);
		}

		if (method_exists($this,"can_".__FUNCTION__) && !$this->can_update($infos["id_".$this->table],$infos)) {
			throw new errorATF(loc::mt(ATF::$usr->trans("probleme_is_active",$this->table),array("table"=>ATF::$usr->trans($this->table,module),"function"=>ATF::$usr->trans(__FUNCTION__))));
		}

		$result = parent::update($infos,$s,$files,$cadre_refreshed,$nolog);
		$id = $infos["id_".$this->table];

		if ($this->files && ($insert_files || $this->files["preview"])){
			if(is_array($insert_files)){
				foreach($insert_files as $key=>$item){
					//Si le fichier a été supprimé
					if($item==="undefined"){
						//Il ne faut pas pouvoir supprimé un fichier obligatoire !
						if($this->files[$key]["obligatoire"]===true){
							$probleme[] = "'".ATF::$usr->trans($key)." (".$this->table.")'";
							throw new errorATF(util::mbox(
														loc::mt(ATF::$usr->trans("file_manquant"),array("data"=>implode(", ",$probleme)))
														,ATF::$usr->trans("attention_file_obligatoire_manquant")
														),103
											);
						}else{
							$this->delete_file($id,$key);
						}
					//Si le fichier a été modifié
					}elseif($key && $item!=="true"){
						$this->move_files($id,$s,false,$insert_files,$key);
					}
				}
			}
		}

		// Ajout d'une notice dans le cas d'un appel AJAX (car cadre_refreshed sollicité)
		if (is_array($cadre_refreshed) && is_array($s) && ATF::$usr instanceof usr) {
			ATF::$msg->addNotice(
				loc::mt(ATF::$usr->trans("notice_update_success"),array("record"=>$this->nom($id)))
				,ATF::$usr->trans("notice_success_title")
			);
		}

		return $result;
	}

	/**
	* Suppression spécifique à optima
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @return boolean TRUE si cela s'est correctement passé
	*/
	public function delete($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL) {

		if (!is_array($infos)) {
			$old_nom = $this->nom($infos);
		} elseif(!is_array($infos["id"])) {
			$old_nom = $this->nom($infos["id"]);
		}

		$result = parent::delete($infos,$s,$files,$cadre_refreshed,$nolog);

		// Ajout d'une notice dans le cas d'un appel AJAX (car cadre_refreshed sollicité)
		if (ATF::$usr instanceof usr) {
			if (is_array($infos) && is_array($infos["id"])) { // Plusieurs suppressions en simultané
				$m = loc::mt(ATF::$usr->trans("notice_delete_all_success"),array("nb"=>count($infos["id"]),"module"=>ATF::$usr->trans($this->table)));
			} elseif ($old_nom && !is_numeric($old_nom)) { // Une seule suppression
				$m = loc::mt(ATF::$usr->trans("notice_delete_success"),array("record"=>$old_nom));
			} else { // Une seule suppression, sans nom ($old_nom créé plus haut vide)
				$m = loc::mt(ATF::$usr->trans("notice_delete_all_success"),array("nb"=>1,"module"=>ATF::$usr->trans($this->table)));
			}
			ATF::$msg->addNotice($m,ATF::$usr->trans("notice_success_title"));
		}

		return $result;
	}

	/**
	* @author Mathieu Tribouillard <mtribouillard@absystech.fr>
	*/
	public function deleteCascade($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL){

		if (!is_array($infos)) {
			$ids[] = $this->decryptId($infos);
		} elseif(!is_array($infos["id"])) {
			$ids[] = $this->decryptId($infos["id"]);
		} elseif(!is_array($infos["id"][0])) {
			foreach($infos["id"] as $key=>$item){
				$ids[] = $this->decryptId($item);
			}
		}

		//Il faut faire un can_delete sinon l'utilisateur pourrait avoir un message sur les suppressions en cascade et ensuite un message can_delete...
		foreach($ids as $id){
			if (method_exists($this,"can_delete") && !$this->can_delete($this->decryptId($id))) {
				throw new errorATF(loc::mt(ATF::$usr->trans("probleme_is_active",$this->table),array("table"=>ATF::$usr->trans($this->table,module),"function"=>ATF::$usr->trans("delete"))));
			}
		}
		$constraintes=ATF::db($this->db)->showConstraint($this->table);

		if($constraintes){
			foreach($ids as $key=>$item){
				$enregistrements_supprimes_restrict=ATF::db($this->db)->showDeleteConstraint($constraintes,$item,"restrict");
				if($enregistrements_supprimes_restrict){
					foreach($enregistrements_supprimes_restrict["onDeleteRestrict"] as $enregistrementOnDeleteRestrict){
						if($enregistrementOnDeleteRestrict["nom"]){
							$msg.=loc::mt(ATF::$usr->trans("affichage_champs_delete_cascade"),array("table"=>ATF::$usr->trans($enregistrementOnDeleteRestrict["table"],"module"),"nom"=>$enregistrementOnDeleteRestrict["nom"]));
						}
					}
					if($enregistrements_supprimes_restrict["enregistrementSuperieur"]){
						$msg.=ATF::$usr->trans("plus_50_enregistrements_concernes");
						$msg.=ATF::$usr->trans("modules_peuvent_etre_concerne");
						foreach($enregistrements_supprimes_restrict["enregistrementSuperieurTable"] as $enregistrementSuperieurTable){
							if($msgItem){
								$msgItem.=", ";
							}
							$msgItem.=$enregistrementSuperieurTable;
						}
						$msg.=$msgItem.ATF::$usr->trans("saut_2_lignes");
					}

					throw new errorATF(loc::mt(ATF::$usr->trans("suppressions_champs_delete_cascade_impossible"),array("item"=>$this->nom($item),"module"=>ATF::$usr->trans($this->table,"module"))).$msg);
				}

				$enregistrements_supprimes=ATF::db($this->db)->showDeleteConstraint($constraintes,$item);
				if($enregistrements_supprimes){
					if($enregistrements_supprimes["onDeleteCascade"]){
						$msg.=loc::mt(ATF::$usr->trans("suppressions_champs_delete_cascade"),array("item"=>$this->nom($item),"module"=>ATF::$usr->trans($this->table,"module")));
						foreach($enregistrements_supprimes["onDeleteCascade"] as $enregistrementOnDeleteCascade){
							if($enregistrementOnDeleteCascade["nom"]){
								$msg.=loc::mt(ATF::$usr->trans("affichage_champs_delete_cascade"),array("table"=>ATF::$usr->trans($enregistrementOnDeleteCascade["table"],"module"),"nom"=>$enregistrementOnDeleteCascade["nom"]));
							}
						}
					}elseif($enregistrements_supprimes["onDeleteSetNull"]){
						$msg.=loc::mt(ATF::$usr->trans("suppressions_champs_delete_cascade_relation"),array("item"=>$this->nom($item),"module"=>ATF::$usr->trans($this->table,"module")));
						foreach($enregistrements_supprimes["onDeleteSetNull"] as $enregistrementOnDeleteSetNull){
							if($enregistrementOnDeleteSetNull["nom"]){
								$msg.=loc::mt(ATF::$usr->trans("affichage_champs_delete_cascade"),array("table"=>ATF::$usr->trans($enregistrementOnDeleteSetNull["table"],"module"),"nom"=>$enregistrementOnDeleteSetNull["nom"]));
							}
						}
					}
					$msg.=ATF::$usr->trans("saut_2_lignes");
				}
			}
		}

		if($enregistrements_supprimes){
			if($enregistrements_supprimes["enregistrementSuperieur"]){
				$msg.=ATF::$usr->trans("plus_50_enregistrements_supprimes");
				$msg.=ATF::$usr->trans("modules_peuvent_etre_concerne");
				foreach($enregistrements_supprimes["enregistrementSuperieurTable"] as $item){
					if($msgItem){
						$msgItem.=", ";
					}
					$msgItem.=$item;
				}
				$msg.=$msgItem.ATF::$usr->trans("saut_2_lignes");
			}
			$msg.=ATF::$usr->trans("Etes_vous_sur");
			return $msg;
		}else{
			$this->delete($infos,$s,$files,$cadre_refreshed);
			return false;
		}
	}


	/**
	* Retourne le contenu d'une page en dehors du domaine
	* dans le but, notamment, de récupérer les coordonnées gmap depuis une adresse postale
	* @author Fanny DECLERCK <fdeclerck@absystech.fr>
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param string $post
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @version 1.0
	* @return string $content
	*/
	/*
	public  function geocode($post,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL) {
		$content = file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($post['url'])."&sensor=false");
		$content = json_decode(str_replace("\t","",str_replace("\n","",$content)));
		$cadre_refreshed = false;
		return $content;
	}*/

	/**
    * Retourne la valeur par défaut spécifique aux données passées en paramètres
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $field
	* @param array &$s La session
	* @param array &$request Paramètres disponibles (clés étrangères)
	* @return string
    */
	public function default_value($field){
		return NULL;
	}

	/**
	* N'applique aucun style particulier à la ligne du select_all (mais peut etre surchargé dans la classe du module (ex: contact))
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array donnees : les donnees de la ligne du select_all (donc qui se base sur les colonnes)
	* @return string class css à appliquer
	*/
	public function applique_css(&$donnees){
		return NULL;
	}

	/**
    * Retourne le tableau de mapping nécessaire pour un autocomplete spécifique (exemple : produit)
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param boolean $json Retourner en format JSON
	* @return array
    */
	public function getAutocompleteMapping($json=false){
		if ($json) {
			return json_encode($this::$autocompleteMapping);
		} else {
			return $this::$autocompleteMapping;
		}
	}

	/**
	* Retourne la liste des onglets à afficher/cacher
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function select_onglet(){
		foreach($this->onglets as $key=>$item){
			if(is_array($item)){
				$item=$key;
			}
			if(ATF::$usr->privilege($item,'select')){
				//check dans le custom si l'onglet doit être affiché ou non
				if(isset(ATF::$usr->custom[$this->table]['onglets_caches'][$item])){
					$onglets[$item]="";
				}else{
					$onglets[$item]="checked='checked'";
				}
			}

		}
		return $onglets;
	}

	/**
	* Sauvegarder les onglets à afficher
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function save_onglet($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL){
		//si on passe par le quick_action
		if(isset($infos['liste'])){
			$liste=array_flip(explode(',',$infos['liste']));

			foreach($this->onglets as $key=>$item){
				if(is_array($item)){
					$item=$key;
				}
				if(ATF::$usr->privilege($item,'select')){
					if(!isset($liste[$item]))$onglets[$item]=1;
				}
			}

			if(ATF::$usr->custom[$this->table]['onglets_caches']){
				unset(ATF::$usr->custom[$this->table]['onglets_caches']);
			}

			if($onglets){
				ATF::$usr->custom[$this->table]['onglets_caches']=$onglets;
			}

		}elseif(isset($infos['ongletDelete'])){
			//si on clique sur la croix d'un onglet
			ATF::$usr->custom[$this->table]['onglets_caches'][$infos['ongletDelete']]=1;
		}elseif(isset($infos['ongletInsert'])){
			//si on choisit un onglet dans la liste déroulante en bas d'un select
			unset(ATF::$usr->custom[$this->table]['onglets_caches'][$infos['ongletInsert']]);
			ATF::$usr->custom['onglets_open_close']["gsa_".$infos['table']."_".$infos['ongletInsert']]="ouvert";

		}

		//mise à jour dans la base
		ATF::$usr->updateCustom();
		$this->redirection("select",$infos["id_".$this->table]);
		ATF::$cr->block('generationTime');
	}

	/**
	* Retourne la liste des onglets cachés qui peuvent être ajouté sur une fiche
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function optionsListeOnglet(){
		foreach($this->onglets as $key=>$item){
			if(is_array($item)){
				$item=$key;
			}
			if(ATF::$usr->privilege($item,'select')){
				//check dans le custom si l'onglet doit être affiché ou non
				if(isset(ATF::$usr->custom[$this->table]['onglets_caches'][$item])){
					$onglets[$item]=ATF::$usr->trans($item,'module');
				}
			}

		}
		return $onglets;
	}

	/**
	* Sauvegarder l'ouverture/fermeture des onglets
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function saveOuvertureOnglet($infos){
		ATF::$cr->block("generationTime");
		ATF::$cr->block("top");
		if($infos['onglet']){
			ATF::$usr->custom['onglets_open_close'][$infos['onglet']]=($infos['showorhide']=="false"?'ouvert':'ferme');
		}
		//mise à jour dans la base
		ATF::$usr->updateCustom();
	}

	/**
	* Sauvegarder l'ouverture/fermeture des onglets
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function saveOuverturePanel($infos){
		ATF::$cr->block("generationTime");
		ATF::$cr->block("top");
		if($infos['panel']){
			ATF::$usr->custom[$this->table]['panel_open_close'][$infos['panel']]=($infos['ouvert']=='none'?'ouvert':'ferme');
		}
		//mise à jour dans la base
		ATF::$usr->updateCustom();
	}

	/**
	* Sauvegarder l'ouverture/fermeture de la toolbar
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	*/
	public function saveOuvertureToolbar($infos){
		ATF::$cr->block("generationTime");
		ATF::$cr->block("top");
		ATF::$usr->custom['toolbar']=($infos['visible']=="false"?'ouvert':'ferme');

		//mise à jour dans la base
		ATF::$usr->updateCustom();
	}

	/**
	* Détermine si le champs passé en paramètre est un champs bloqué ou non
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function est_bloquee($champs){
		foreach($this->colonnes['bloquees']['update'] as $valeur){
			if($valeur==$champs)return true;
		}
		return false;
	}

	/**
	* Lorsque l'on clique sur un graphe, on redirige vers le resultat qu'on souhaite sur le select_all filtré par la barre sur laquelle on a cliqué
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $donnees Peut préremplir les données
	*/
	public function statsFiltrage($donnees=NULL){
		if ($donnees===NULL) {
			/* nom du filtre */
			$donnees['name']='Filtre de stats';
			/* Pour que chaque condition soit reliée en AND */
			$donnees['mode']='AND';

			if(ATF::_r('type')=="users" || ATF::_r('type')=="user"){
				$this->setConditionFiltre($donnees,$this->table.".id_user",'LIKE',(ATF::_r('label')?ATF::_r('label'):ATF::user()->nom(ATF::$usr->getID())),1);
			}
			if($this->table=='tache'){
				$donnees['conditions'][0]['field']=$this->table.".horaire_fin";
			}else{
				$donnees['conditions'][0]['field']=$this->table.".date";
			}

			$donnees['conditions'][0]['operand']='LIKE%';
			$donnees['conditions'][0]['value']=ATF::_r('annee')."-".ATF::_r('mois');
		}

		$insertion=array(
			"filtre_optima"=>$donnees['name']
			,"id_module"=>ATF::module()->from_nom($this->table)
			,"id_user"=>ATF::$usr->getID()
			,"options"=>serialize($donnees)
			,"type"=>"prive");

		//si le filtre existe déjà on le met à jour
		ATF::filtre_optima()->q->reset()
								->addCondition('id_module',ATF::module()->from_nom($this->table))
								->addCondition('id_user',ATF::$usr->getID())
								->addCondition('filtre_optima',$donnees['name'])
								->addCondition('type',"prive")
								->setDimension('farro');
		$ancien_filtre=ATF::filtre_optima()->select_all();

		if($ancien_filtre[0]){
			$insertion['id_filtre_optima']=$ancien_filtre[0]['id_filtre_optima'];
			ATF::filtre_optima()->update($insertion);
			$id_filtre=$ancien_filtre[0]['id_filtre_optima'];
		}else{
			//sinon on le créé
			$id_filtre=ATF::filtre_optima()->insert($insertion);
		}

		return $id_filtre;
	}

	/**
	* Permet de structuré la condition de manière à être interprété au niveau des custom et du filtre
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array infos : tableau qui va contenir toutes les conditions
	* @param string field : champs sur lequel filtré
	* @param string operand : opérateur (=, LIKE, !=, ...)
	* @param string value : valeur à appliquer au champs
	* @param integer cle : ordre dans lequel les conditions sont affichées
	*/
	public function setConditionFiltre(&$donnees,$field,$operand,$value,$cle){
		$donnees['conditions'][$cle]['field']=$field;
		$donnees['conditions'][$cle]['operand']=$operand;
		$donnees['conditions'][$cle]['value']=$value;
	}

	/**
	* si on était sur une recherche par colonne et qu'on veut revenir à la recherche normale
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array $s
	* @param string $div
	*/
	public function reinitSearch(&$s,$div){
		$s["pager"]->q[$div]->reset('search');
	}

	/**
	* Retourne le HTML d'un widget
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array infos : tableau qui va contenir toutes les conditions
	* @return string
	*/
	public function widget(&$infos){
		// Après cette ligne, on débloque la session par nous n'y écrirons plus
		ATF::getEnv()->commitSession();

		$infos["display"]=true;
		ATF::$html->assign('module',$this->table);
		if (ATF::$html->template_exists($this->table."_widget_".$infos["type"].".tpl.htm")) {
			return ATF::$html->fetch($this->table."_widget_".$infos["type"].".tpl.htm");
		} elseif (ATF::$html->template_exists($this->table."_widget.tpl.htm")) {
			return ATF::$html->fetch($this->table."_widget.tpl.htm");
		} else  {
			return ATF::$html->fetch("generic-widget.tpl.htm");
		}
	}

	/**
	* Crée un permalink pour la fiche select de cet enregistrement
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $id
	* @param mixed $droits
	*			FALSE (Valeur par défaut) pour le mode permalink qui demande une identification avant d'accéder à la page si on est pas déjà identifié
	*			Modules accessibles séparés par virgule OU tableau spécifique qui prendra la place des privileges de ATF::$usr
	*			, ou id_user si autologin
	*			, ou NULL si on veut un mode invité avec uniquement cette table accessible
	* @param string $extra PAramètres supplémentaires en get. Exemple : permapdf=true
	* @todo Droit sur un seul enregistrement ?
	* @return id
	*/
	public function createPermalink($id,$droits=false,$extra=NULL){
//		if (is_string($droits) || is_array($droits)) {
//			// Si les droits sont définis alors on sera en mode invité
//			$invited=true;
//		}
		$id=$this->decryptId($id);
		if ($droits===NULL) {
			$droits = $this->table;
		}
//		if(ATF::$html->template_exists($this->table."-select.tpl.htm")){
//			$template=$this->table."-select";
//		}else{
//			$template="generic-select";
//		}
		return ATF::permalink()->create($this->table,"select",$id,$extra,$droits,$this->table);
	}

	/**
	* Envoi du mail avec le permalien
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array infos : contient l'id module et les emails
	*/
	public function sendPermalink($infos){
		$infos['id']=$this->decryptId($infos['id']);
		$infos['lien']=ATF::permalink()->getURL($this->createPermalink($infos['id'],$infos["droits"]));

		// envoie du mail
		$mail = new mail(array(
			"recipient"=>$infos['email'],
			"objet"=>loc::mt(ATF::$usr->trans("permalien"),array("user"=>ATF::user()->nom(ATF::$usr->getID()))),
			"template"=>"permalink",
			"elements"=>$infos,
			"from"=>"Optima <no-reply@absystech.fr>"
		));
		if($mail->send()){
			ATF::$msg->addNotice(ATF::$usr->trans("email_envoye"));
		}
	}

	/**
	* renvoi le module que l'on veut joindre, et si ce dernier est déterminé dans le constructeur de la classe
	* comme étant lié à la table courante par une table de jointure, on ajoute la table de jointure dans la liste
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* array $infos : ce qui est envoyé par la requête ajax
	*/
	public function refresh_column($infos){
		$nom_module=ATF::module()->select($infos['mod'],'module');
		if($module=ATF::getClass($infos['table'])->listeJointure[$nom_module]){
			$liste_mod[]=array('module'=>$nom_module,'jointure'=>$module);
		}else{
			$liste_mod[]=$nom_module;
		}
		return $liste_mod;
	}

	/**
	* Génére le fichier contenant toutes les données pour imprimer la fiche sur laquelle on se situe
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param $infos : contient l'identifiant de la fiche à imprimer
	*/
	public function genererPdf($infos){
		$listepriv=implode(",",array_keys(ATF::$usr->getPrivileges()));
		$infos['id']=$this->decryptId($infos['id']);
		$link=ATF::permalink()->getURL(ATF::getClass($this->table)->createPermalink($infos['id'],$listepriv,"permapdf=true"));
		$chem="/tmp/impression".rand(1,100).".pdf";

		$cmd = "wkhtmltopdf --dpi 100 -q '".$link."' ".$chem." 2>&1";
		exec($cmd,$r);
		header('Content-Type: application/octet-stream');
		header('Content-disposition: attachment; filename=impression.pdf');
		header('Content-Length: '.filesize($chem));
		header("Cache-Control: private");
		$fh=fopen($chem, "rb");
		fpassthru($fh);
		unlink($chem);
	}

	/**
	* Retourne le HTML d'une google map
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array infos : tableau qui va contenir toutes les conditions
	* @return string
	*/
	public function geolocalisation(&$infos){
		ATF::$html->array_assign($infos);
		ATF::$html->assign("table",$this->table);
		if (ATF::_r('id')) {
			ATF::$html->assign("condition_key","id_".$this->table);
			ATF::$html->assign("condition_value",ATF::_r('id'));
		}
		$infos["display"]=true;
		if (ATF::$html->template_exists($this->table."-geolocalisation.tpl.htm")) {
			return ATF::$html->fetch($this->table."-geolocalisation.tpl.htm");
		} else {
			return ATF::$html->fetch("geolocalisation.tpl.htm");
		}
	}

	/**
	* Gestion de l'onglet
	* Cette méthode appelée en ajax permet la construction d'un onglet
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param array $infos Les infos passés par le contrôleur
	*/
	public function onglet($infos){
		//Chargement de l'onglet
		if ($infos["template"] && $infos["div"]) {
			$name=substr($infos["div"],7,strlen($infos["div"])-15);
			ATF::$cr->add($infos["div"],$infos["template"].".tpl.htm",
				array(
					"current_class"=>ATF::getClass($name)
					,"parent_name"=>$infos["parent_name"]
					,"id_current"=>$this->decryptId($infos["id"])
					,"name"=>$name
					,"opened"=>$infos["opened"]
					,"field"=>$infos["field"]
					,"table"=>$infos["table"]
					,"function"=>$infos["function"]
					,"select"=>$infos["select"]
				)
			);
			ATF::$cr->block("generationTime");
			ATF::$cr->rm("top");
		}
	}

	/**
	* Permet de séléctionner les enregistrements qui ont été créer depuis la dernière connection de l'utilisateur
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param bool c retourne le compte si TRUE sinon retourne les data
	*/
	function getSinceLastConnection($c=false) {
		$this->q->reset();
		$this->q->addCondition("date",ATF::$usr->get('date_connection'),"OR",NULL,">=");
		if ($c) $this->q->setCountOnly();
		return $this->sa();
	}

//	/**
//	* Permet de calculer le pourcentage de champs rempli dans la table courante pour la donnée envoyée
//	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
//	* @param integer $id : contient l'identifiant de la donnée à vérifier
//	* @return la couleur, le texte et la valeur de la barre de progression
//	*/
//	public function champsComplete($id){
//		$this->q->reset()->addCondition('id_'.$this->table,$id)->setDimension('row');
//		$nbre=$rempli=0;
//		foreach($this->select_all() as $champs=>$valeur){
//			$nbre++;
//			if($valeur)$rempli++;
//		}
//
//		return array("couleur"=>"progressGreen","valeur"=>(round(($rempli*100)/$nbre)/100),"pourcentage"=>round(($rempli*100)/$nbre)." %");
//	}

	/**
	* Retourne la totalité du texte
	* @param int $infos[id] Numéro de l'enregistrement
	* @param int $infos[field] Champ requis
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/
	public function getTruncated(&$infos) {
		$infos["display"]=true;
		return $this->select($infos["id"],$infos["field"]);
	}

	/**
	* Récupère les champs cachés filtrés par rapport aux colonnes affichées dans le formulaire (ne tient pas compte des autres panels, car aléatoire selon module)
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array $request : $_REQUEST
	* @param string $table : table courante
	* @param string $event : insert/update
	* @return array $tab
	*/
	public function recupChampsCache($request,$table,$event){
		$colonnes=array_merge(ATF::getClass($table)->colonnes('primary',$event),ATF::getClass($table)->colonnes(NULL,$event));
		foreach($request as $field_name=>$valeur){
			//if ($this->getColonne($field_name)) {
				$key_class=ATF::getClass(ATF::getClass($table)->fk_from($field_name));
				if(is_a($key_class,classes) &&  $key_class->table!=$table && !$colonnes[$field_name]){
					$tab[$field_name]=$key_class->decryptId($valeur);
					$redirect=$key_class->name();
				}
			//}
		}
		if($redirect){
			$tab["__redirect"]=$redirect;
		}

		return $tab;
	}

	/**
	* Permet d'uploader un fichier depuis un select all
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 23-03-2011
	*/
	public function uploadFileFromSA(&$infos,&$s,$files=NULL,&$cadre_refreshed=NULL){
		$infos['display'] = true;
		$class = ATF::getClass($infos['extAction']);
		if (!$class) return false;
		if (!$infos['id']) return false;
		if (!$files) return false;

		$id = $class->decryptID($infos['id']);

		foreach ($files as $k=>$i) {
			if (!$i['size']) return false;
			$this->store($s,$id,$k,$i);

		}
		ATF::$cr->block('generationTime');
		ATF::$cr->block('top');



		$o = array ('success' => true );
		return json_encode($o);
	}

	/**
	* Setter d'attributs
	* @param string $attr Numéro de l'enregistrement
	* @param mixed $value Champ requis
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/
	public function setAttribute($attr,$value) {
		if (property_exists($this,$attr)) {
			$this->$attr = $value;
		}
	}

	/**
	* Setter d'attributs
	* @param string $attr Numéro de l'enregistrement
	* @param mixed $value Champ requis
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/
	public function getAttribute($attr) {
		if (property_exists($this,$attr)) {
			return $this->$attr;
		}
		return false;
	}

	/**
	* Permet de modifier la date sur un select_all
	* @param array $infos id_table, key (nom du champs à modifier),value (nom du champs à modifier)
	* @return boolean
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	*/
	public function updateDate($infos) {

		if ($infos['value'] == "undefined") $infos["value"] = "";

		$infos["key"]=str_replace($this->table.".",NULL,$infos["key"]);
		$infosMaj["id_".$this->table]=$infos["id_".$this->table];
		$infosMaj[$infos["key"]]=$infos["value"];
		if($this->u($infosMaj)){
			ATF::$msg->addNotice(
				loc::mt(ATF::$usr->trans("notice_update_success_date"),array("record"=>$this->nom($infosMaj["id_".$this->table]),"date"=>$infos["key"]))
				,ATF::$usr->trans("notice_success_title")
			);
			return true;
		}else{
			return false;
		}
	}

	/**
	* Permet de modifier un champ sur un select
	* @param array $infos id_table, key (nom du champs à modifier),value (nom du champs à modifier)
	* @return boolean
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	*/
	public function updateOnSelect($infos,$force=false){
//		if(!$force){
//			if(!ATF::$usr->privilege($this->table,"update")) {
//				throw new errorATF($this->table." update access denied");
//				return false;
//			}
//		}

		$tab["id_".$this->table]=$this->decryptId($infos["id"]);

		if($infos["id_value"] && $infos["id_value"]!=="undefined"){
			$tab[$infos["key"]]=$this->decryptId($infos["id_value"]);
		}elseif($infos["value"]){
			$tab[$infos["key"]]=$infos["value"];
		}

		if($this->update($tab)){
			ATF::$msg->addNotice(
				loc::mt(ATF::$usr->trans("notice_update_success"),array("record"=>$this->nom($tab["id_".$this->table]),"field"=>$infos["key"]))
				,ATF::$usr->trans("notice_success_title")
			);
			return true;
		}else{
			return false;
		}
	}

	/**
	* Retourne le javascript du formulaire d'insertion
	* @param array $infos
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/
	public function getUpdateForm(&$infos) {
		if (ATF::$usr->privilege($infos['table'],$infos['event'],"",$infos['table'])) {
			$infos['current_class'] = ATF::gate()->getClass($infos['table']);
			ATF::$html->array_assign($infos);

			if ($this->selectExtjs) {
				ATF::$html->assign("event",$infos['event']);
				ATF::$html->assign("renderTo",false);
				ATF::$html->assign("noStyle",true);
				if(ATF::$html->template_exists($this->table."-select.tpl.js")){
					$js = ATF::$html->fetch($this->table."-select.tpl.js");
				}else{
					$js = ATF::$html->fetch("generic-select.tpl.js");
				}
			} else {
				if(ATF::$html->template_exists($this->table."-update.tpl.js")){
					$js = ATF::$html->fetch($this->table."-update.tpl.js");
				}else{
					$js = ATF::$html->fetch("generic-update.tpl.js");
				}
			}
			$infos["display"]=true;
			return $js;
		}
	}
		/**
	* Retourne le javascript du formulaire d'insertion
	* @param array $infos
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/
	public function getInsertForm(&$infos) {
		if (ATF::$usr->privilege($infos['table'],$infos['event'],"",$infos['table'])) {
			$infos['current_class'] = ATF::gate()->getClass($infos['table']);
			ATF::$html->array_assign($infos);
			if(ATF::$html->template_exists($this->table."-select.tpl.js")){
				$js = ATF::$html->fetch($this->table."-select.tpl.js");
			}else{
				$js = ATF::$html->fetch("generic-select.tpl.js");
			}
			$infos["display"]=true;
			return $js;
		}
	}

	/**
	* Sauvegarder la présence ou l'abscence d'un onglet dans le TabGridPanel
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/
	public function saveFilterTab(&$infos){
		if (!$infos['t']){
			return false;
		}
		if ($infos['v']) {
			ATF::$usr->custom[$this->table]['filtre'][$infos['t']]=true;
		} elseif (isset(ATF::$usr->custom[$this->table]['filtre'][$infos['t']])) {
			unset(ATF::$usr->custom[$this->table]['filtre'][$infos['t']]);
			//suppression du filtre_defaut et de son querier
			ATF::_s("pager")->unsetQuerier($infos['div']);
			ATF::filtre_defaut()->q->reset()
				->where("id_user",ATF::$usr->getID())
				->where("`div`",$infos['div'])
				->setDimension("row_arro");
			$donnees=ATF::filtre_defaut()->select_all();
			if($donnees['id_filtre_defaut']) ATF::filtre_defaut()->delete($donnees['id_filtre_defaut']);
		}

		//mise à jour dans la base
		ATF::$usr->updateCustom();

		$infos["display"]=true;

		if ($infos['v']) {
			// Retourner la création du nouvel onglet en Javascript
			ATF::$html->array_assign($infos);
			return ATF::$html->fetch("generic-gridpanel.tpl.js");
		}
	}

	/** Permet de sauvegarder le tab sur lequel on a cliqué en dernier (hormi filtre et insert)
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array $infos : contient le nom du tab cliqué
	* @todo n'est plus utilisé, je la laisse en commentaire au cas où.

	public function changeActiveTab($infos){
		ATF::$cr->block("top");
		ATF::$cr->block("generationTime");
		preg_match_all("`(.*)Filtre(.*)_([0-9]*)`",$infos['tab'],$divs);
		if($divs[1][0] && $divs[2][0]){
			ATF::$usr->setDefaultFilter($divs[2][0],$divs[1][0],$this);
		}elseif($infos['tab']=="gsa_".$this->table."_".$this->table){
			//ATF::_s("pager")->unsetQuerier($infos['tab']);
			ATF::filtre_defaut()->q->reset()
				->where("id_user",ATF::$usr->getID())
				->where("`div`",$infos['tab'])
				->setDimension("row_arro");
			$donnees=ATF::filtre_defaut()->select_all();
			if($donnees['id_filtre_defaut']) ATF::filtre_defaut()->delete($donnees['id_filtre_defaut']);
		}
	}
	*/
	/** Permet de rafraîchir le tabpanel des filtres
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array $infos : contient les informations nécessaires (table, filtre, ...)
	*/
	public function loadFilters(&$infos){
		$infos["display"]=true;
		ATF::$html->array_assign($infos);
		return ATF::$html->fetch("generic-tabpanel-filter.tpl.js");
	}

	/** Permet de générer le menu de vue de manière différée
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array $infos : contient les informations nécessaires (table, filtre, ...)
	*/
	public function genereMenuVue(&$infos){
		$infos["display"]=true;
		$infos['current_class']=$this;
		ATF::$html->array_assign($infos);
		return ATF::$html->fetch("generic-gridpanel-view.tpl.js");
	}

	/** Permet de récupérer les valeurs des aggregats
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array $infos : contient le champs
	*/
	public function recupAggregate($infos){
		ATF::$cr->block("generationTime");
		ATF::$cr->block("top");

		// Fermer la session, car seulement du SELECT
		ATF::getEnv()->commitSession();

		$champs=str_replace("__dot__",".",$infos['champs']);
		$aggregat=$this->colonnes['fields_column'][$champs]["aggregate"];
		if($aggregat){
			$v_agre=$this->select_aggregate($champs,$aggregat);
			$tab['sum']=array("champs"=>$infos['champs'],"valeur"=>$v_agre['sum']>0?round($v_agre['sum'],2):$v_agre['sum']);
			$tab['min']=array("champs"=>$infos['champs'],"valeur"=>$v_agre['min']>0?round($v_agre['min'],2):$v_agre['min']);
			$tab['max']=array("champs"=>$infos['champs'],"valeur"=>$v_agre['max']>0?round($v_agre['max'],2):$v_agre['max']);
			$tab['avg']=array("champs"=>$infos['champs'],"valeur"=>$v_agre['avg']>0?round($v_agre['avg'],2):$v_agre['avg']);
			return $tab;
		}
		return;
	}

	/** Permet de savoir si il faut ou non afficher les aggregats sur le select_all
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param string $pager : nom du pager courant
	*/
	public function afficheAggregate($pager){
		//si le pager courant a des colonnes custom, et qu'elles n'ont pas besoin d'aggregats, alors on affiche pas
		ATF::vue()->q->reset()->addCondition("vue",$pager)->setDimension("cell")->addField("ordre_colonne")->setStrict();

		if($liste=ATF::vue()->select_all()){
			$liste_champs=explode(',',$liste);
			foreach($liste_champs as $key=>$champs){
				if(isset($this->colonnes['fields_column'][util::extJSUnescapeDot($champs)]['aggregate']))return true;
			}
		}else{
			//sinon si c'est la vue standard, on peut se référer au fields_column
			foreach($this->colonnes['fields_column'] as $champs=>$donnees){
				if(isset($donnees['aggregate']))return true;
			}
		}
		return false;
	}

	/**
	* Sauvegarder l'affichage des aggregats
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function saveAggregat($infos){
		ATF::$cr->block("generationTime");
		ATF::$cr->block("top");
		if(ATF::$usr->custom[$this->table]["aggregats"][($infos['filtre']?$infos['filtre']:0)]===true){
			unset(ATF::$usr->custom[$this->table]["aggregats"][($infos['filtre']?$infos['filtre']:0)]);
			$return=false;
		}else{
			ATF::$usr->custom[$this->table]["aggregats"][($infos['filtre']?$infos['filtre']:0)]=true;
			$return=true;
		}
		//mise à jour dans la base
		ATF::$usr->updateCustom();

		return $return;
	}

	/** Permet de déterminer si il faut activer ou non les aggregats (fait en ajax pour envoyer une demande si recherche)
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array $infos : contient l'id du filtre
	*/
	public function isAggregateActive($infos){
		// Fermer la session, car seulement du SELECT
		ATF::getEnv()->commitSession();

		ATF::$cr->block("generationTime");
		ATF::$cr->block("top");

		if($infos['id_filtre']){
			$id_filtre=$infos['id_filtre'];
		}else{
			$id_filtre=0;
		}

		if(ATF::$usr->custom[$this->table]["aggregats"][$id_filtre]){
			return true;
		}

		return false;
	}

	/** Récupère la liste des colonnes de type date
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function recupColDate(){
		foreach($this->desc as $k=>$i){
			$colonne = $this->getColonne($k);
			if ($colonne["xtype"]=="datefield" && !in_array($k,$this->colonnes['bloquees']['select'])) {
				$liste_champs[]=array('header' => ATF::$usr->trans($k,$this->table)
									,'index' => util::extJSEscapeDot($this->table.".".$k));
			}
		}

		return $liste_champs;
	}

	/**
    * Renvoi le texte de la quickTips
    * @author Quentin JANON <qjanon@absystech.fr>
    */
	public function getQuickTips(&$infos) {
		$infos['display'] = true;
		$r = ATF::$usr->trans($infos['field']."_info",$this->table);
		return $r;
	}

	/**
	* Stockes les fichiers piece jointes envoyé par de tous les mail au bon endroit puis les supprime
	* @author Antoine MAITRE <amaitre@absystech.fr>
	* @param string mail correspond au nom de la boite mail
	* @param string className correspond au nom de la classe dans laquelle le fichier sera stocké
	* @param string suffixName correspond au suffixe du nom du fichier
	* @param string codename correspond au nom du serveur de la société
	* @return bool false si une erreur ou true si tout se passe bien
	*/
	public function mailToPieceJointe($className, $suffixName, $codename) {
		if (!$codename || !$suffixName || !$className){
			return false;
		}
		$mail = "optima.".$codename.'.'.$className.'.'.$suffixName.'@absystech.fr';
		ATF::imap()->init("zimbra.absystech.net", 143, $mail, "az78qs45");
		if (ATF::imap()->error) {
			throw new errorATF(ATF::imap()->error);
		}
		$mail = ATF::imap()->imap_fetch_overview('1:*');
		$class = ATF::getClass($className);
		if (!is_object($class)) return false;
		foreach ($mail as $k) {
			preg_match("/([a-zA-Z_\-%+\.]*)@([a-zA-Z_\-%+\.]*)/",$k->from,$m);
			$email_sender = $m[1]."@".$m[2];
			$id_user_sender = ATF::user()->getIDFromEmail($email_sender);
			$piece_jointe = ATF::imap()->get_attachments($k->uid);
			$pdf = ATF::imap()->get_attachments($k->uid, $piece_jointe[0]['filename']);
			$class->q->reset()->where('ref', $k->subject)->setDimension('row');
			$tmp = $class->sa();

			// Si on trouve un reponsable à cet objet
			if ($id_user_responsable=$class->getUserResponsable($tmp['id_'.$className])) {
				// Sauvegarde de la pièce jointe qui est écrasée si elle existe
				$path = $class->filepath($tmp['id_'.$className],$suffixName);
				if (file_exists($path)) {
					//Envoi d'un mail
					$info_mail["objet"] = "Fichier avant remplacement : ".$className." > ".$tmp['ref']." > ".$suffixName;
					$info_mail["from"] = "Optima <optima.".$server."@absystech.fr>";
					$info_mail["html"] = false;
					$info_mail["template"] = 'empty';
					$info_mail["texte"] = "Remplacement d'un fichier : ".$className." > ".$tmp['ref']." > ".$suffixName.". L'ancien fichier est en piece jointe de ce mail, pour archive et vérification.";
					$info_mail["recipient"] = ATF::user()->select($id_user_responsable,'email').",".$email_sender;

					//Ajout du fichier
					$mail = new mail($info_mail);
					$mail->addFile($path,$infos["ref"].".pdf",true);
				}
			}

			$class->store(ATF::_s(), $tmp['id_'.$className], $suffixName, $pdf);

			$id_affaire = $class->select($tmp['id_'.$className],'id_affaire');
			$id_societe = ATF::affaire()->select($id_affaire,'id_societe');

			if ($id_societe && $id_affaire && $id_user_sender) {
				$suivi = array(
					"id_user"=>$id_user_sender
					,"id_societe"=>$id_societe
					,"id_affaire"=>$id_affaire
					,"texte"=>"Création d'un nouveau fichier ".$suffixName." pour la référence ".$tmp['ref']." a été créé"
				);
				$id_suivi = ATF::suivi()->insert($suivi);
			}

//				ATF::imap()->imap_delete($k->uid);
		}
		ATF::imap()->imap_expunge();
		return true;
	}

	/**
	* Retourne l'id_user responsable de cet enregistrement
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $id ID de l'enregistrement
	* @return int $id_user
	*/
	public function getUserResponsable($id) {
		switch ($this->table) {
			case "devis":
			case "commande":
			case "bon_de_commande":
				return $this->select($id,"id_user");
		}
		return false;
	}

	/**
	* Retourne la ref pour le scanner
	* @author Morgan Fleurquin <mfleurquin@absystech.fr>
	* @param int $id ID de l'enregistrement
	* @return int $id_user
	*/
	public function getRefForScanner($id, $champs) {
		$return = $this->select($id);
		return "Transfert vers ".ATF::$usr->trans($this->table)." - ".ATF::$usr->trans($champs)." (ref : ".$return["ref"]." )";
	}


	/* ****************************************
	 *		FONCTIONS POUR LES FILTRES        *
	 ******************************************/
	/*public function getFields(){
		$res = ATF::$usr->tri_ident(ATF::$usr->trans(ATF::getClass($table)->table_structure(),$table),true);
		header('Content-Type: application/json');
		return json_encode($res);
	}*/

	/**
	* Execute les requetes et récupère le nombre de lignes correspondant à chaque conditions
	* @author Morgan Fleurquin <mfleurquin@absystech.fr>
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos
	* @return array $return
	*/
	public function getFilterCountSimulations($infos){

		if($infos["id_filtre"] && $infos["id_filtre"] !== "null"){
			$id_filtre = $infos["id_filtre"];
			if(strpos($infos["id_filtre"],'_')){
				$id_filtre = explode("_", $infos["id_filtre"]);
				$id_filtre = $id_filtre[1];
			}
			$filtre = ATF::filtre_optima()->select($id_filtre);
			$sharable = false;
			if($filtre["type"] === "public"){
				 $sharable = true;
			}
			$data = unserialize($filtre["options"]);
		} else {
			$data = $infos;
		}
		$conditions = array();

		foreach($data["conditions"] as $k=>$v){

			// On travaille sur la table du champ uniquement
			$module = explode(".", $v["field"]);
			$class = ATF::getClass($module[0]);
			$resultForFiltre = $class->resultForFiltre(array("conditions"=>array($v)));
			$pattern = '#etat#';
			if (preg_match($pattern,$v["field"])) {
				$v['valueTrad'] = ATF::$usr->trans($v["value"], $module[0]);
			}
			$pattern = '#(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})#';
			if (preg_match($pattern,$v["value"])) {
				$v['valueTrad'] = ATF::$usr->date_trans($v["value"]);
				if ($v['value_sup']) $v['value_sup_trad'] = ATF::$usr->date_trans($v["value_sup"]);
			}
			$conditions[] = array(
				"field"=>$v["field"],
				"module" => $module[0],
				"operator" => array(
					"name" => $this->getNameOperand($v["operand"]),
					"value" => $v["operand"]
				),
				"value" => $v["value"],
				"valueTrad" => $v["valueTrad"],
				"value_sup" => $v["value_sup"],
				"value_sup_trad" => $v["value_sup_trad"],
				"traduction" => ATF::$usr->trans($module[1],$module[0]),
				"result" => $resultForFiltre["reste"].'/'.$resultForFiltre["total"],
				"deprecated"=>($v["operand"]=="BETWEEN"?true:false)
			);
		}
		//log::logger($conditions,qjanon);

		// Simulation du filtre avec tous les critères :
		$resultForFiltre = $this->resultForFiltre(array("conditions"=>$data["conditions"]));


		return array(
			"name"=> $data["name"],
			"sharable"=> $sharable,
			"elements" => $conditions,
			"total" => $resultForFiltre["reste"]
		);
	}

	public function champs1($infos){
		$donnees = ATF::getClass($infos["module"])->table_structure();

		$data[] = array("name"=>"Choisissez...","trad"=>"Choisissez...");
		foreach ($donnees as $key => $value) {
			if(strpos($key , $infos["module"]) !== false){
				if(strpos($key , "id_".$infos["module"]) === false){
					$val = str_replace($infos["module"].".", "", $key);
					$data[] = array("module"=> $infos["module"],
						"name"=>$val,
						"trad"=>$this->trad(array("champs"=>$val,"module"=>$infos["module"]))
					);
				}
			}
		}

		$data = array_reverse($data);

		header('Content-Type: application/json');
		return json_encode($data);
	}

	public function operator($infos){

		$donnees = ATF::getClass($infos["module"])->html_structure();
		$donneesType = '';

		$d = explode(".", $infos["champs"]);
		$infos["champs"] = $d[1];
		foreach ($donnees as $key => $value) {
			if($key == $infos["champs"]){
				$type = $value["type"];
				if($value["data"]){
					foreach ($value["data"] as $k => $val) {
						if(ATF::$usr->trans($val , $infos["module"]) != $val){
							$trad = ATF::$usr->trans($val , $infos["module"]);
						}else{
							$trad = ATF::$usr->trans($val);
						}
						//$trad = $val;
						$value["data"][$k] = array($val, $trad);
					}
					$donneesType = $value["data"];
				}
			}
		}
		$data = $this->operand($type, $donneesType);

		header('Content-Type: application/json');
		return json_encode($data);
	}

	public function operand($type, $donnees){
		$o = querier::$operateur;
		$data = array();
		switch ($type) {
			case 'datetime':
			case 'date':
				$rtype = "date";
			break;
			case 'list':
			case 'enum':
			case 'set':
				$rtype = "list";
			break;
			case 'text':
			case 'textarea':
			default :
				$rtype = "input";
			break;
		}
		$op = array();
		foreach ($o as $k=>$i) {
			if ((is_array($i['type']) && in_array($type,$i['type'])) || (is_array($i['not_this_type']) && !in_array($type,$i['not_this_type']))) {
				$op[] = array("name"=>ATF::$usr->trans($k,'operateur_filtre'),"value"=>$k,"type"=>$rtype);
			} elseif (!is_array($i)) {
				$op[] = array("name"=>ATF::$usr->trans($i,'operateur_filtre'),"value"=>$i,"type"=>$rtype);
			}
		}
		$op[] = array("name"=>"Choisissez...","value"=>"", "type"=>"");
		if($donnees){
			$data = array($op);
			$data["donnees"] = $donnees;
		} else {
			$data = $op;
		}
		return $data;

	}

	function trad($infos){
		return ATF::$usr->trans($infos["champs"], $infos["module"]);
		/*if(ATF::$usr->trans($infos["champs"], $infos["module"]) != $infos["champs"]){
			return ATF::$usr->trans($infos["champs"], $infos["module"]);
		}elseif(ATF::$usr->trans($infos["champs"]) != $infos["champs"]){
			return ATF::$usr->trans($infos["champs"]);
		}else{
			return $infos["champs"];
		}*/
	}

	function getNameOperand($operand){
		$retour = null;
		switch($operand){
			case "LIKE" : $retour = "contient";
			break;
			case "NOT LIKE" : $retour = "ne contient pas";
			break;
			case "<" : $retour = "est inferieur strictement à";
			break;
			case ">" : $retour = "est superieur strictement à";
			break;
			case "<=" : $retour = "est inferieur ou égal à";
			break;
			case ">=" : $retour = "est superieur ou égal à";
			break;
			case "<>" :
			case "!=" : $retour = "est différent de";
			break;
			case "=" : $retour = "est égal à";
			break;
			case "BETWEEN" : $retour = "est compris entre";
			break;
			case "IS NOT NULL" : $retour = "n'est pas nul(le)";
			break;
			case "IS NULL" : $retour = "est nul(le)";
			break;

		}
		return $retour;
	}

	function listeModuleAssocie($module){
		$champs = ATF::getClass($module)->table_structure();

		if(ATF::getClass($module."_ligne")){
			$table[] = $module."_ligne";
		}

		foreach($champs as $k=>$v){
			if(strpos($k , "id_")){
				if($k !== $module.".id_".$module){
					$table[] = ATF::getClass($module)->fk_from(str_replace($module.".", "", $k));
				}
			}
		}

		//On recupere les tables module_ligne
		foreach ($table as $key => $value) {
			if(ATF::getClass($value."_ligne")){
				$table[] = $value."_ligne";
			}
		}

		//On retire les doublons de module
		$table = array_unique($table);

		//On retire le module si il est présent dans les enfants
		foreach($table as $k=>$v){
			if($v === $module){
				unset($table[$k]);
			}
		}

		//Reorganise les clés sinon JQuery prendra ça pour un object
		$table = array_values($table);


		header('Content-Type: application/json');
		return json_encode($table);
	}

	function resultForFiltre($infos){

		$this->q->reset();
		$return["total"] = $this->count(); // Nombre de lignes dans cette table au total
		ATF::db()->begin_transaction();

		ATF::filtre_optima()->saveFilter(array(
			"nommodule" => $this->name(),
			"table" => "filtre_optima",
			"filtre_optima" => array(
				"filtre_optima" => "filtre_optima" ,
				"options" => array(
					"name" => "filtre_temporaire",
					"mode" => "AND",
					"conditions" => $infos["conditions"],
					"choix_join" => "left"
				),
				"type" => "prive"
			),
			"event" => "insert"
		));
		ATF::filtre_optima()->q->reset()->addOrder("id_filtre_optima", "desc")->setLimit(1);
		$id = ATF::filtre_optima()->select_row();

		$array = array(
			"pager"=> "gsa_".$this->name()."_".$this->name()."_".$id["id_filtre_optima"]
			,"pager_parent" =>  "gsa_".$this->name()."_".$this->name()
			,"filter_key" => $id["id_filtre_optima"]
			,"function"=>"select_all_count_only"
		);
		ATF::_p("filter_key",$id["id_filtre_optima"]);
		ATF::_p("pager",$array["pager"]);
		ATF::_p("pager_parent",$array["pager_parent"]);

		$q=ATF::_s("pager")->create($array["pager"],NULL,true);
		$q->setView(array("order"=>array($this->table.".id_".$this->table=>$this->table.".id_".$this->table)));

		$this->updateSelectAll($array);
		$return["reste"] = $array["data"]; // Nombre de lignes restantes

		ATF::db()->rollback_transaction();

		return $return;
	}

	/* ****************************************
	 *	FIN DES FONCTIONS POUR LES FILTRES    *
	 ******************************************/

	/**
	* Module d'envoi de mails depuis société
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
	public function sendMailEXT($infos){
		if (!$infos['to']) {
			throw new error ("Impossible d'envoyer un mail sans destinataire");
		}
		if (!$infos['subject']) {
			throw new error ("Impossible d'envoyer un mail sans sujet");
		}
		if (!$infos['msg']) {
			throw new error ("Impossible d'envoyer un mail sans corps de mail");
		}

		$toSend = explode(",",$infos['to']);

		foreach ($toSend as $k=>$i) {
			$m = array('recipient'=> $i,'objet'=> $infos["subject"],'body' => $infos["msg"]);

			$mail=new mail($m);
			$mail->send();
		}
	}

	/**
	* Retourne le binaire d'une image
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/
	public function _img($get, $post){
		$path = $this->filepath($get['id_'.$this->table],$get['field']);
		return base64_encode(file_get_contents($path));
	}

	/**
	* Retourne l'id decrypté
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/
	public function _decryptId($get, $post){
		return $this->decryptId($get['id']);
	}
}
