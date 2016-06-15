<?
/**
* Classe VI PA
*
*
* @date 2009-10-31
* @package inventaire
* @version 1.0.0
* @author QJ <qjanon@absystech.fr>
* @author Yann GAUTHERON <ygautheron@absystech.fr>
*
*/ 
class vi_pa extends classes_optima {
	
	/**
    * Stocke toutes les réponses effectuées par un vi_pa::store(), évite d'insérer deux fois les mêmes réponses
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $alreadyStored
    */ 
	public $alreadyStored = array();
	
	/**
	* Constructeur
	*/
	public function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		
		//Colonnes SELECT ALL
		$this->colonnes['fields_column'] = array(	
			'vi_pa.id_visite'
			,'vi_pa.date'
			,'vi_pa.id_attr'
			//,'vi_pa.id_pa'
			,'vi_pa.id_user'
			,'vi_pa.reponse'
			//,'vi_pa.id_parent'
		);
		
		//Colonnes SELECT
		$this->colonnes['primary'] = array(
			'id_visite'
			,'date'
			,'id_user'
			,'id_attr'
			,'reponse'
		);
		$this->controlled_by = "visite";
		
		$this->no_insert = true;
		$this->no_update = true;
		
		$this->fieldstructure();
		
		$this->foreign_key["id_parent"] = $this->table;
		$this->field_nom = "%id_visite% > %id_attr%";
		
		$this->files = array("photo" => array("type"=>"jpg"));
		
		$this->addPrivilege("uploadPhoto","update");
		$this->addPrivilege("uploadPhotoI","update");
		$this->addPrivilege("newMulti","update");
		$this->addPrivilege("store","update");
		$this->addPrivilege("viewCosts","update");
		$this->addPrivilege("autocompleteReponse");
		$this->addPrivilege("autocompleteHistorique");
		$this->addPrivilege("updateVPMOffset","update");
		
		$this->autocomplete = array("view" => array("reponse","nom","vi_pa.date"));
	}	
	
	/**
    * Stocke une réponse
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param 	int $infos[r] reponse
	* @param 	int $infos[a] id_attr
	* @param 	int|string $infos[v] id_visite
	* @param 	int $infos[ppa] id_ppa
	* @param 	int $infos[pa] id_pa (facultatif
	* @param 	int $infos[m] id_vi_pa_multi (facultatif)
	* @param 	boolean $infos[force] (facultatif) Force la création d'une nouvelle réposne, même si le champ "réponse" est identique à la précédente (utile pour les photos par exemple...)
	* @param array &$s La session
    */ 
	public function store($infos,&$s,&$files,&$cadre_refreshed=NULL) {
		if (!$infos["v"]) {
			throw new errorATF("vi_pa::store() Il manque un id_visite",44);	
		}
		if (ATF::db()->begin_transaction()) {
			// On flush le tableau antidoublons
			$this->alreadyStored = array();
		}
		$insert = array(
			"id_user"=>ATF::$usr->getID()
			, "reponse"=>html_entity_decode($infos["r"],ENT_COMPAT,"UTF-8")
			, "id_attr"=>$infos["a"]
			, "id_visite"=>ATF::visite()->decryptid($infos["v"])
			, "id_ppa"=>$infos["ppa"] // Sans ça on est embété plus bas pour l'annulation des réponses soeurs d'enum.
		);
		if ($infos["pa"]) {
			$insert["id_pa"]=$infos["pa"];
		}
		if ($infos["m"]) {
			$insert["id_vi_pa_multi"]=$infos["m"];
		}
		// Dernière réponse
		$lastAnswer=$this->isAnswered($insert["id_visite"],$insert["id_attr"],$insert["id_ppa"],NULL,$insert["id_vi_pa_multi"]);
		// Si la réponse est différente de celle qu'on souhaite
		//if (true) {
		if (!strlen($lastAnswer["reponse"]) || $lastAnswer["reponse"]!==$insert["reponse"]) {
			
			// Insertion de la nouvelle réponse
			try {
				$new_answer_id = $this->insert($insert);
				
				if ($insert['id_attr']==3730) { // Correspond a l'attribut 'OUI' qui déclenche un désordre en accessibilité
					foreach (ATF::pa()->selectChilds($insert['id_pa']) as $k=>$i) {
						if ($i['id_attr']==3708) {
							$sc = $i;
						}
					}
					
					// Ici on doit créer automatiquement le multi scenario en enfant.
					$infosMultiSpecial = array(
						"ppa"=>$sc['id_pa']?$sc['id_pa']:$insert['id_pa']
						,"a"=>$sc['id_attr'] 
						,"pa"=>$sc['id_pa']
						,"m"=>$insert['id_vi_pa_multi']
						,"v"=>$insert['id_visite']
					);
					$retourMulti = $this->newMulti($infosMultiSpecial,$s);
					ATF::formulaire()->keepOpened(($sc['id_pa']?$sc['id_pa']:$insert['id_pa'])."_".$sc['id_attr']."_".$retourMulti['id_vi_pa_multi']);
					foreach (ATF::pa()->selectChilds($sc['id_pa']) as $c=>$o) {
						ATF::formulaire()->keepOpened($o['id_pa']."_".$o['id_attr']."_".$retourMulti['id_vi_pa_multi']);
					}
				} elseif ($insert['id_attr']==3505) {
					ATF::formulaire()->keepOpened($insert['id_pa']."_".$insert['id_attr']."_".$insert['id_vi_pa_multi']);
					foreach (ATF::attr()->selectChilds($insert['id_attr']) as $c=>$o) {
						ATF::formulaire()->keepOpened($insert['id_pa']."_".$o['id_attr']."_".$insert['id_vi_pa_multi']);
					}
				}
				
			} catch (errorSQL $e) {
				ATF::db()->rollback();
				if ($e->getErrno()==1062) {
					throw new errorATF("vi_pa::store() l'insertion a échouée, date identique",1);
				}
			}
			
			// On met à jour la date de la visite
			ATF::visite()->majDate($insert["id_visite"]);
				
			// Si c'est une réponse à un type enum on doit supprimer les autres réponses soeurs et leurs enfants
			if ($insert["id_pa"]) {
				// S'il y a un id_pa, on check ke type de l'attribut du parent PA direct
				$id_ppa = ATF::pa()->select($insert["id_pa"],"id_parent");
				$id_pattr = ATF::pa()->select($id_ppa,"id_attr");
			} else {
				// Sinon le type du parent Attr direct
				$id_pattr = ATF::attr()->select($insert["id_attr"],"id_parent");
			}
//////log::logger("store id_pattr=".$id_pattr,"ygautheron");				
			
//			if (ATF::attr()->select($insert["id_attr"],"type")==="unary") {
//				log::logger();
				// Si on a un type unaire, on doit vérifie si l'activation ou la désactivation n'engage pas la suppression d'autres réponses
				if (ATF::attr()->select($id_pattr,"type")==='enum') {
					// Seulement si le type est enum il faut annuler les autres réponses soeurs et leurs enfants
					$insert["id_vi_pa"] = $new_answer_id;
					$this->storeEmptySisters($insert,$s);
					
				} 
				if(!strlen($insert["reponse"])) {
					// Si pas de réponse, et pas de parent ENUM, on est une checkbox ou un autre type, et on doit supprimer toutes les réponses enfants
					$this->storeEmptyChilds($insert,$s,true);
				}
//			}
			
			// Pas d'erreur, on commit seulement si pas de transaction plus large en cours
			if (ATF::db()->end_transaction()) {
				
				// On flush le tableau antidoublons
				$this->alreadyStored = array();
				
				// Mise à jour du cout affiché à coté de l'attribut sur le formulaire si cadre_refreshed demandé
				if (is_array($cadre_refreshed)) {
					$infos=array(
						"id_vi_pa"=>$new_answer_id
						,"id_pa"=>$insert["id_pa"]
						,"div"=>$infos["d"]."Cost"
						,"id_vi_pa_multi"=>$insert["id_vi_pa_multi"]
						,"id_attr"=>$insert["id_attr"]
					);
					$this->showCost($infos,$s,$files,$cadre_refreshed);
				}
			}
			
			return $new_answer_id;
		} else {
			ATF::db()->rollback();
			throw new errorATF("vi_pa::store() réponse déjà la même...",2);
		}
		
	}
		
	/**
    * Applique des réponses vides à toutes les réponses enfants existantes de cet identifiant de réponse
	* ATTENTION : on part du principe qu'un UNAIRE de enum a forcément un parent !
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array &$protectedAnswer Infos de la réponse protégée, en cas de suppression de soeurs, ne pas remplacer la réponse choisie en dernier.
	* @param array &$s La session
    */ 
	public function storeEmptySisters(&$protectedAnswer,&$s) {		
		if ($protectedAnswer["id_pa"]) { // Si id_pa, on cherche les enfant PA		
			$id_parent = ATF::pa()->select($protectedAnswer["id_pa"],"id_parent");
			$childs = ATF::pa()->selectChilds($id_parent,false,false,false);
//log::logger("storeEmptySisters nb_child_pa=".count($childs)." du parent=".$id_parent,"ygautheron");				
			
		} elseif($protectedAnswer["id_attr"]) { // Sinon les enfants de catalogue, on trouve les freres de même parent 				
			$id_parent = ATF::attr()->select($protectedAnswer["id_attr"],"id_parent");
			$childs = ATF::attr()->selectChilds($id_parent,false);
//log::logger("storeEmptySisters nb_childs_attr=".count($childs)." du parent=".$id_parent,"ygautheron");				
			
		} else {
			throw new errorATF("vi_pa::storeEmptySisters needs a parent !");
		}
		
		// Les enfants sont trouvés, alors on execute le remplacement par des réponses vides recursivement, sauf sur la réponse protégée.
		if (is_array($childs)) {
//log::logger($childs,"ygautheron");
			foreach ($childs as $q) {
//log::logger("storeEmptySisters Check answer id_visite=".$protectedAnswer["id_visite"]." id_attr=".$q["id_attr"]." id_ppa=".($q["id_pa"]?$q["id_pa"]:$protectedAnswer["id_ppa"]),"ygautheron");
				if ($lastAnswer = $this->isAnswered($protectedAnswer["id_visite"],$q["id_attr"],$q["id_pa"]?$q["id_pa"]:$protectedAnswer["id_ppa"],$q["id_pa"],$protectedAnswer["id_vi_pa_multi"])) {
					
//log::logger($lastAnswer,"ygautheron");
					// On vérifie que la réponse n'est pas celle qui est protégée
					if ($lastAnswer["id_vi_pa"]!=$protectedAnswer["id_vi_pa"]) {
//log::logger("storeEmptySisters effacer enfants","ygautheron");
						
						// Si une réponse existe, on l'annule avec une nouvelle remplacante vide
						$this->storeEmptyChilds($lastAnswer,$s);
					} else {
//log::logger("storeEmptySisters reponse protegee","ygautheron");
					}
				} else {
//log::logger("storeEmptySisters Pas de réponses, mais on continue dans les enfants","ygautheron");
					$fakeAnswer = array(
						"id_user"=>ATF::$usr->getID()
						,"id_attr"=>$q["id_attr"]
						,"id_visite"=>$protectedAnswer["id_visite"]
						,"id_pa"=>$q["id_pa"]
						,"id_ppa"=>($q["id_pa"]?$q["id_pa"]:$protectedAnswer["id_ppa"])
						,"id_vi_pa_multi"=>$protectedAnswer["id_vi_pa_multi"]
					);
					$this->storeEmptyChilds($fakeAnswer,$s,true);
				}
			}
		}
	}
	
	/**
    * Applique des réponses vides à toutes les réponses enfants existantes de cet identifiant de réponse
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int|array &$reponse Identifiant à rendre vide, ou infos de la réponse complète sous forme d'un array
	* @param array &$s La session 
	* @param boolean $childsOnly Mettre à vrai pour ne vider que les réponses enfants (utile si le papa est déjà fait, ou aucune réponse sur le papa)
	* @return void
    */ 
	public function storeEmptyChilds(&$reponse,&$s,$childsOnly=false) {		
		// Infos de la réponse d'origine
		if (is_numeric($id_vi_pa)) {
			$reponse = $this->select($id_vi_pa);
		}
		
//log::logger("storeEmptyChilds","qjanon");				
//log::logger("storeEmptyChilds [flag1] id_visite=".$reponse["id_visite"]." id_attr=".$reponse["id_attr"]." id_ppa=".$reponse["id_ppa"],"qjanon");				
		if (is_array($reponse)) {
			$insert = array(
				"id_user"=>ATF::$usr->getID()
				,"reponse"=>""
				,"id_attr"=>$reponse["id_attr"]
				,"id_visite"=>$reponse["id_visite"]
				,"id_ppa"=>$reponse["id_ppa"]
				,"id_vi_pa_multi"=>$reponse["id_vi_pa_multi"]
			);
			
			// Pour tous les enfants également on doit supprimer les réponses
			if ($reponse["id_pa"]) { // Si id_pa, on cherche les enfant PA
				$insert["id_pa"]=$reponse["id_pa"];
				$childs = ATF::pa()->selectChilds($reponse["id_pa"],false,false,false);
//log::logger("storeEmptyChilds [flag1] nb_child_pa=".count($childs)." du parent=".$reponse["id_pa"],"qjanon");				
			} else { // Sinon les enfants de catalogue
				$childs = ATF::attr()->selectChilds($reponse["id_attr"],false);
//log::logger("storeEmptyChilds [flag1] nb_childs_attr=".count($childs)." du parent=".$reponse["id_attr"],"qjanon");				
			}
			if ($childs) {
				foreach ($childs as $q) {


//log::logger("storeEmptyChilds Check answer id_visite=".$reponse["id_visite"]." id_attr=".$q["id_attr"]." id_ppa=".($q["id_pa"]?$q["id_pa"]:$reponse["id_ppa"]),"qjanon");				
//log::logger("storeEmptyChilds 60314 Check answer id_visite=".$reponse["id_visite"]." id_attr=".$q["id_attr"]." id_ppa=".($q["id_pa"]?$q["id_pa"]:$reponse["id_ppa"]),"qjanon");				
					if (($lastAnswer = $this->isAnswered($reponse["id_visite"],$q["id_attr"],$q["id_pa"]?$q["id_pa"]:$reponse["id_ppa"],$q["id_pa"],$insert["id_vi_pa_multi"])) && strlen($lastAnswer["reponse"])) {
						// Si une réponse existe, on l'annule avec une nouvelle remplacante vide
//log::logger("storeEmptyChilds [flag1] Une reponse existe pour ".$reponse["id_visite"].",".$q["id_attr"].",".($q["id_pa"]?$q["id_pa"]:$reponse["id_ppa"]),"qjanon");				
						$this->storeEmptyChilds($lastAnswer,$s);
					} else {
//log::logger("storeEmptyChilds [flag1] pas de réponse (".serialize($lastAnswer)."), mais on doit aussi vérifier les enfants de ".$reponse["id_visite"].",".$q["id_attr"].",".($q["id_pa"]?$q["id_pa"]:$reponse["id_ppa"]),"qjanon");				
						// Sinon on regarde pour vider ses enfants aussi
						$fakeAnswer = array(
							"id_user"=>ATF::$usr->getID()
							,"id_attr"=>$q["id_attr"]
							,"id_visite"=>$insert["id_visite"]
							,"id_pa"=>$q["id_pa"]
							,"id_ppa"=>($q["id_pa"]?$q["id_pa"]:$insert["id_ppa"])
							,"id_vi_pa_multi"=>$insert["id_vi_pa_multi"]
						);
//log::logger($fakeAnswer,"qjanon");				
						$this->storeEmptyChilds($fakeAnswer,$s,true);
					}
					
					// Gestion des MULTI
					if ($multis = $this->getDistinct($reponse["id_visite"],$q["id_attr"],$q["id_pa"]?$q["id_pa"]:$reponse["id_ppa"],$insert["id_vi_pa_multi"])) {
						// Des réponses multiples sont détectées, il faut aussi les vider avec leurs enfants
						foreach ($multis as $multi) {
//log::logger("Multi ".$multi['id_vi_pa'].",".$multi['id_vi_pa_multi'].",".$multi['reponse'],"qjanon");			
							$fakeAnswer = array(
								"id_user"=>ATF::$usr->getID()
								,"id_attr"=>$q["id_attr"]
								,"id_visite"=>$insert["id_visite"]
								,"id_pa"=>$q["id_pa"]
								,"id_ppa"=>($q["id_pa"]?$q["id_pa"]:$insert["id_ppa"])
								,"id_vi_pa_multi"=>$multi['id_vi_pa_multi']
							);
							$this->storeEmptyChilds($fakeAnswer,$s);
						}
					}
				}
			}
			
			// Et en plus, est-ce qu'il y a des réponses avec ce pa comme "ppa" de référence
//log::logger("storeEmptyChilds appel des réponses ","qjanon");		
			try {
				if ($childsAnswers = $this->getAnswers($reponse["id_visite"],$reponse["id_pa"],$reponse["id_vi_pa_multi"])) {
					foreach ($childsAnswers as $a) {
						if (($lastAnswer = $this->isAnswered($a["id_visite"],$a["id_attr"],$a["id_ppa"])) && strlen($lastAnswer["reponse"])) {
							// Si une réponse existe, on l'annule avec une nouvelle remplacante vide
//log::logger("storeEmptyChilds Une reponse non nulle existe pour ".$a["id_visite"].",".$a["id_attr"].",".$a["id_ppa"],"qjanon");				
							$this->storeEmptyChilds($a,$s);
						}
					}
				}
			} catch (errorATF $e) {
				$e->setError($e);
				throw new errorATF(__CLASS__."::".__FUNCTION__." pb attribut dans PA :".log::arrayToString($reponse));
			}
			
//log::logger("storeEmptyChilds essai Insertion reponse sur id_attr=".$insert["id_attr"]." id_pa=".$insert["id_pa"]." id_ppa=".$insert["id_ppa"]." reponse=".$insert["reponse"]."IF[".(!$childsOnly)." && ".(!$this->alreadyStored[$insert["id_ppa"]][$insert["id_attr"]])."]","qjanon");				
			if (!$childsOnly && !$this->alreadyStored[$insert["id_ppa"]][$insert["id_attr"]]) {
				// Finalement on insère le parent
//log::logger("storeEmptyChilds Insertion reponse sur id_attr=".$insert["id_attr"]." id_pa=".$insert["id_pa"]." id_ppa=".$insert["id_ppa"]." reponse=".$insert["reponse"],"qjanon");				
				$this->alreadyStored[$insert["id_ppa"]][$insert["id_attr"]] = true;
				$this->insert($insert);
			}
		}
	}
	
	/**
    * Retourne une réponse
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $id_visite
	* @param int $id_attr
	* @param int $id_ppa Parent le plus direct
	* @param int $id_pa PA a titre unformatif dans la base de données
	* @param int $id_vi_pa_multi
	* @param datetime $dateMax Date de filtrage, utile pour récupérer des réponses à une date données et ainsi crée un rapport tel qu'il était à l'époque
	* @param boolean $answerOnly
    */
	public function isAnswered($id_visite,$id_attr,$id_ppa=NULL,$id_pa=NULL,$id_vi_pa_multi=NULL,$dateMax=NULL,$answerOnly=false) {
		if ($id_visite && $id_attr && ($id_ppa || $id_vi_pa_multi)) {
			$id_visite = ATF::visite()->decryptid($id_visite);
			$this->q->reset()
				->addCondition("id_visite",$id_visite)
				->addCondition("id_attr",$id_attr)
				->addOrder("date","desc")
				->setLimit(1)
				->setDimension('row')
			;
			if ($answerOnly) { // Ne retourner que la réponse
				$this->q->addField("reponse")->setDimension('cell');
			}
			if ($id_pa) {
				$this->q->addCondition("id_pa",$id_pa);
			}
			if ($id_ppa) {
				$this->q->addCondition("id_ppa",$id_ppa);
			}
			if ($id_vi_pa_multi) {
				$this->q->addCondition("id_vi_pa_multi",$id_vi_pa_multi);
			}
			if ($dateMax) {
				$this->q->addCondition("date",$dateMax,"AND",false,"<=");
			}
			
//			$this->q->setToString();
//			log::logger($this->select_all(),qjanon,true);
//			$this->q->unsetToString();
			
			return $this->select_all();
		} else {
			throw new errorATF(__CLASS__."::".__FUNCTION__."($id_visite,$id_attr,$id_ppa,$id_pa,$id_vi_pa_multi)");	
		}
	}
	
	/**
    * Retourne toutes les réponses associées à un PPA, une visite et un id_vi_pa_multi, qui ont un id_pa à NULL
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $id_visite
	* @param int $id_ppa Parent le plus direct
	* @param int $id_vi_pa_multi
    */
	public function getAnswers($id_visite,$id_ppa,$id_vi_pa_multi=NULL,$string=NULL,$id_attr=NULL,$dateMax=NULL) {
		if (!$id_ppa) {
			return false;
		}
		if ($id_visite) {
			$id_visite = ATF::visite()->decryptid($id_visite);
				$this->q->reset()
					->addCondition("id_visite",$id_visite)
					->addCondition("id_ppa",$id_ppa)
					->addCondition("id_pa",NULL,"AND",false,"IS NULL") // Ne retourne que les réponses avec un id_pa à NULL
					;
			if ($id_attr) {
				$this->q->addCondition("id_attr",$id_attr);
			} else {
				$this->q->addGroup("id_visite")->addGroup("id_ppa")->addGroup("id_attr");
			}
			if ($id_vi_pa_multi) {
				$this->q->addCondition("id_vi_pa_multi",$id_vi_pa_multi);
			}
			if ($dateMax) {
				$this->q->addCondition("date",$dateMax,"AND",false,"<=");
			}
			if ($string) {
				$this->q->settoString();
			}
			return $this->select_all();
		} else {
			throw new errorATF(__CLASS__."::".__FUNCTION__."($id_visite,$id_ppa,$id_vi_pa_multi)");	
		}
	}
	
	/**
    * Retourne toutes les clés id_via_pa_multi pour les bloc multiples
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $id_visite
	* @param int $id_attr
	* @param int $id_pa PA
	* @param int $id_vi_pa_multi Parent id_vi_pa_multi facultatif
	* @todo Faire que le querier sache ajouter des requete de requete dans addjointure etpas que dans addcondition
    */
	public function getDistinct($id_visite,$id_attr,$id_pa,$id_vi_pa_multi=NULL,$dateMax=NULL) {
		if ($id_visite && $id_attr/* && $id_pa*/) {
			$id_visite = ATF::visite()->decryptid($id_visite);
			$query = "SELECT vNotNull.* 
				FROM (
					SELECT v.id_vi_pa, v.reponse, v.id_vi_pa_multi
					FROM (
						SELECT v2.*".($id_vi_pa_multi?",`vm2`.`offset`":NULL)."
						FROM `vi_pa` v2 
							".($id_vi_pa_multi?"INNER JOIN `vi_pa_multi` vm2 ON v2.id_vi_pa_multi=vm2.id_vi_pa_multi AND vm2.id_parent=".$id_vi_pa_multi:NULL)."
						WHERE 
							v2.id_pa".($id_pa ? "=".$id_pa : " IS NULL")." 
							AND v2.id_visite=".$id_visite." 
							AND v2.id_attr=".$id_attr;
							
			if ($dateMax) {
				$query.=" AND v2.date<='".$dateMax."'";
			}
							
			$query.=" ORDER BY v2.`date` DESC
					) AS v
					GROUP BY v.id_visite, v.id_pa, v.id_attr, v.id_vi_pa_multi
					".(($id_vi_pa_multi && $id_attr==3754)?" ORDER BY `v`.`offset`,`v`.`id_vi_pa_multi` ASC":NULL)."
				) AS vNotNull
				WHERE vNotNull.reponse IS NOT NULL";
//			log::logger(ATF::db($this->db)->sql2array($query),'qjanon');
			return ATF::db($this->db)->sql2array($query);
		} else {
			throw new errorATF(__CLASS__."::".__FUNCTION__."($id_visite,$id_attr,$id_pa)");	
		}
	}
	
	/**
    * Retourne le nombre de questions répondues
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $id id_visite
	* @param string $type Ne chercher que ce type de réponse (type d'attribut)
	* @return int Nombre de réponses non nulles
    */ 
	public function getNbReponses($id_visite,$type=NULL) {
		// Création de la subQuery
		$this->q->reset()
			->addField('vi_pa.*')
			->addJointure("vi_pa","id_vi_pa_multi","vi_pa_multi","id_vi_pa_multi") // Left join
			->addCondition("vi_pa.id_visite",$id_visite)
			->addOrder("vi_pa.date","desc")
			->setStrict()
			->setToString();
		if ($type) {	
			$this->q
				->addJointure("vi_pa","id_attr","attr","id_attr") // Left join
				->addCondition("attr.type",$type);
		}
		$subQuery = $this->select_all();
		
		// Utilisation de la subQuery pour créer la seconde subQuery
		$this->q->reset()
			->setSubQuery($subQuery,'v')           
			->addField('id_vi_pa')
			->addField('reponse')
			->addField('id_vi_pa_multi')
			->addGroup('v.id_visite')
			->addGroup('v.id_pa')
			->addGroup('v.id_attr')
			->addGroup('v.id_vi_pa_multi')
			->setToString();
		$subQuery = ATF::db($this->db)->select_all($this);
		
		// Utilisation de la seconde subQuery pour créer la requête finale
		$this->q->reset()
			->setSubQuery($subQuery,'vNotNull')           
			->addField('COUNT(*)','total')
			->addConditionNotNull("vNotNull.reponse")
			->setDimension('cell');
		return ATF::db($this->db)->select_all($this);
	}
	
	/**
    * Crée un nouveau bloc multi
	*
	* - Crée un enregistrement dans vi_pa_multi qui permet de conserver une arborescence entre 
	* Multi imbriqués (batiments multiples, puis des pieces multiples, puis des portes multiples par exemple...)
	* - Crée aussi une réponse (de valeur "1") vi_pa sur la racine du bloc multiple pour "matérialiser" sa présence dans la table
	* des réponses. Ca sera cette même réponse qu'il faudra en utilisant storeEmptyChilds() si on supprime ce bloc (cette porte, piece ou batiment par exemple)
	*
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos
	* @param 	int $infos[a] id_attr
	* @param 	int $infos[p] id_pa Le drapeau "Multi" n'existe que sur les PA
	* @param 	int $infos[ppa] id_ppa
	* @param 	int $infos[m] id_vi_pa_multi (facultatif) Branche de multiple dans laquelle créer ce nouveau sous-bloc mutiple
	* @param array &$s Session
	* @return array vi_pa_multi créé
    */
	public function newMulti($infos,&$s) {
		// Création du vi_pa_multi
		$multi = array("id_vi_pa_multi"=>NULL);
		if ($infos["m"]) {
			$multi["id_parent"]=$infos["m"];
		}
		if ($multi["id_vi_pa_multi"] = $infos["m"] = ATF::vi_pa_multi()->insert($multi)) {
			
			// Création de la réponse racine
			$infos["r"]=1;
			if ($multi["id_vi_pa"] = $this->store($infos,$s)) {
			
				// On enregistre la réponse qui a créé le vi_pa_multi, pour une éventuelle suppression en cascade utile
				ATF::vi_pa_multi()->update($multi);
				
				return $multi;
			}
		}
	}
	
	/** 
	* Rattacher une photo sur visite-formulaire
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function uploadPhoto(&$infos){
		if ($infos["a"] && $infos["ppa"] && $infos["v"]) {
			if ($infos["id_ged"]=ATF::ged()->decryptId($infos["id_ged"])) {
				$filepath = ATF::ged()->getFilepath($infos["id_ged"]);
				$filename =  ATF::ged()->nom($infos["id_ged"]);
			}

			if (file_exists($filepath)) {
				if ($lastAnswer=$this->isAnswered($infos["v"],$infos["a"],$infos["ppa"],NULL,$infos["m"])) {
					$md5_old = md5(file_get_contents($this->filepath($lastAnswer["vi_pa"],"photo")));				
				}
				
				$data = file_get_contents($filepath);
				$md5_new = md5($data);
				
				if ($md5_old !== $md5_new) {
					// Les binaires ne sont pas identique à la réponse précédente, on peut procéder
					$response = array(
						"a"=>$infos["a"]
						,"pa"=>$infos["pa"]
						,"ppa"=>$infos["ppa"]
						,"v"=>$infos["v"]
						,"m"=>$infos["m"]
						,"r"=>$filename
					);

					if ($id_vi_pa = $this->store($response,$s)) { // Si la réponse se stocke bien, on stocke la photo
						ATF::$html->assign("id_vi_pa",$id_vi_pa);
						parent::store($s,$id_vi_pa,"photo",$data);
						return $id_vi_pa;
					}
				} else {
					// Photo exactement identique à la précédente, on retourne un message d'erreur
					ATF::$html->assign("same_photo",true);
				}
			}
		}
		return false;
	}
	
	/**
    * Retourne le calcul d'un coût d'une réponse
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $id_vi_pa 
    * @return int
    */   
	public function getCost($id_vi_pa,$unitaire=false,$dateMax=NULL){
		if ($id_vi_pa) {
			$costs = ATF::vi_pa_cout()->getCosts($id_vi_pa,$dateMax);
			
			if ($unitaire) {
				return ATF::vi_pa_cout()->getCostsTotalUnitaire($costs);
			} else {
				return ATF::vi_pa_cout()->getCostsTotal($costs);
			}
		}
	}
	
	/**
    * Calcule le coût d'une réponse par rapport à son id et la règle à utiliser
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $id_vi_pa 
    * @return string $regle
    * @return int $cout_unitaire
	* @todo trouver une solution autre que eval() car très risqué !
    */   
	public function computeCost($id_vi_pa,$regle,$cout_unitaire){
		$arithmetique = $this->costAnswers($id_vi_pa,$regle);
		
		// Analyse des valeurs par défaut
		$arithmetique = preg_replace("/\(0\|sinon:([0-9]*)/",'(\1',$arithmetique);
		$arithmetique = preg_replace("/([0-9]*)\|sinon:[0-9]*/",'\1',$arithmetique);

		// Evaluation par PHP (pas zoli...)
		// log::logger('$total = '.$cout_unitaire."*".$arithmetique.';',"qjanon");
		$total = 0;
		if ($cout_unitaire && $arithmetique) {
			$cout_unitaire = str_replace(",",".",$cout_unitaire);
			$arithmetique = str_replace(",",".",$arithmetique);
			eval('$total = '.$cout_unitaire."*".$arithmetique.';');
		}
		
		return $total;
	}
	
	/**
    * Met à jour le div passé en paramètre, avec le cout de la réponse
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos 
	* @param array $s : contenu de la session
	* @param array $files
	* @param array $cadre_refreshed
    * @return boolean true
    */   
	public function showCost($infos,&$s,$files=NULL,&$cadre_refreshed=NULL){
		if ($infos["div"] && $infos["id_vi_pa"] && $infos["id_pa"]) {
			ATF::$html->assign("vi_pa",$this->select($infos["id_vi_pa"]));
			ATF::$html->assign("id_vi_pa_multi",$infos["id_vi_pa_multi"]);
			ATF::$html->assign("attr",ATF::attr()->select($infos["id_attr"]));
			ATF::$html->assign("infos",$infos);
			$cadre_refreshed[$infos["div"]] = ATF::$html->fetch("visite-formulaire_attr_showCost.tpl.htm");
			return true;
		} else {
			return false;
		}
	}
	
	/**
    * Consultation des coûts depuis le formulaire de visite
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos 
	* @param array $s : contenu de la session
	* @param array $files
	* @param array $cadre_refreshed
    * @return boolean true | array $infos si pas de cadre_refreshed demandé
    */   
	public function viewCosts(&$infos,&$s,$files=NULL,&$cadre_refreshed=NULL){
		if ($infos["id_vi_pa"]) {
			if ($infos["id_cout_catalogue"]==="0") { // On annule le cout sur cette réponse
				$this->update(array(
					"id_vi_pa"=>$infos["id_vi_pa"]
					,"id_cout_unitaire"=>""
					,"id_cout_catalogue"=>""
					,"cout_unitaire"=>""
				));
			} elseif (!$infos["id_cout_catalogue"]) { // Si indéfini, on récupère ce cout catalogue
				$infos["id_cout_catalogue"]=$this->select($infos["id_vi_pa"],"id_cout_catalogue");
				if (!$infos["id_cout_catalogue"]) {
					$infos["id_cout_catalogue"]=ATF::cout_unitaire()->select($this->select($infos["id_vi_pa"],"id_cout_unitaire"),"id_cout_catalogue");
				}
			}
			$infos["vi_pa"] = $this->select($infos["id_vi_pa"]);
			if ($infos["id_cout_catalogue"]) {
				if ($infos["vi_pa"]) {
					//if ($infos["cout_unitaire"] = ATF::cout_unitaire()->selectFromPA($infos["id_cout_catalogue"],$infos["vi_pa"]["id_pa"])) {
						if ($infos["cout_unitaire"] = ATF::cout_unitaire()->selectFromPA($infos["id_cout_catalogue"],$infos["vi_pa"]["id_pa"])) {
							// On enregistre le cout_unitaire sélectionné s'il a changé
							if ($infos["vi_pa"]["id_cout_unitaire"]!=$infos["cout_unitaire"]["id_cout_unitaire"] || $infos["vi_pa"]["cout_unitaire"]!=$infos["cout_unitaire_expert"] && isset($infos["cout_unitaire_expert"])) {
								$infos["vi_pa"]["id_cout_unitaire"]=$infos["cout_unitaire"]["id_cout_unitaire"];
								$infos["vi_pa"]["cout_unitaire"]=$infos["cout_unitaire_expert"];
								$this->update($infos["vi_pa"]);
							}
						} else {
							// On enregistre le cout catalogue s'il a changé, et qu'on a pas de cout_unitaire imposé sur ce PA
							if ($infos["vi_pa"]["id_cout_catalogue"]!=$infos["id_cout_catalogue"] || $infos["vi_pa"]["cout_unitaire"]!=$infos["cout_unitaire_expert"] && isset($infos["cout_unitaire_expert"])) {
								$infos["vi_pa"]["id_cout_unitaire"]="";
								$infos["vi_pa"]["id_cout_catalogue"]=$infos["id_cout_catalogue"];
								$infos["vi_pa"]["cout_unitaire"]=$infos["cout_unitaire_expert"];
								$this->update($infos["vi_pa"]);
							}
							$infos["cout_unitaire"] = array("regle" => ATF::pa()->getRegle($infos["vi_pa"]["id_pa"]));
						}
						$infos["cout_catalogue"] =  ATF::cout_catalogue()->select($infos["id_cout_catalogue"]);
						
						// Coût catalogue spécifique de projet
						if ($infos["id_cout_catalogue"]) {
							$id_gep_projet = ATF::pa()->select($infos["vi_pa"]["id_pa"],"id_gep_projet");
							if ($cc_gep=ATF::cout_catalogue_gep()->coutSpecifique($infos["id_cout_catalogue"],$id_gep_projet)) {
								$infos["cout_catalogue"]["cout_unitaire"] = $cc_gep;
							}
						}
						
						// Définition du prix unitaire de référence
						if ($infos["vi_pa"]["cout_unitaire"]) { // Soit cout unitaire d'expert
							$infos["cout_unitaire_reel"] =  $infos["vi_pa"]["cout_unitaire"]; 
						} elseif ($infos["cout_unitaire"]["cout_unitaire"]) { // Soit cout unitaire de prix unitaire défini spécifiquement pour ce PA
							$infos["cout_unitaire_reel"] =  $infos["cout_unitaire"]["cout_unitaire"];
						} else { // Soit prix unitaire du catalogue
							$infos["cout_unitaire_reel"] =  $infos["cout_catalogue"]["cout_unitaire"];
						}
//					} else {
//						throw new errorATF($this->table."::".__FUNCTION__." > error 3 : aucun coût associé");
//					}
				} else {
					throw new errorATF($this->table."::".__FUNCTION__." > error 2 : aucun PA associé au vi_pa");
				}
			}
		} else {
			throw new errorATF($this->table."::".__FUNCTION__." > error 1 : aucun vi_pa");
		}
		if (is_array($cadre_refreshed)) {
			return $this->refreshCostModalbox($infos,$s,$files,$cadre_refreshed);
		} else {
			return $infos;
		}
	}
	
	/**
    * Rafraichi la modalbox de choix des coûts lors d'une visite
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos 
	* @param array $s : contenu de la session
	* @param array $files
	* @param array $cadre_refreshed
    */
	private function refreshCostModalbox($infos,&$s,$files=NULL,&$cadre_refreshed=NULL){
		ATF::$html->assign("infos",$infos);
		$cadre_refreshed["__view_cout_unitaire"] = ATF::$html->fetch("visite-formulaire_attr_cout.tpl.htm");
		// Mise à jour du cout affiché à coté de l'attribut sur le formulaire si cadre_refreshed demandé
		$nfo=$infos["vi_pa"];
		$nfo["div"]=$infos["vi_pa"]["id_ppa"]."_".$infos["vi_pa"]["id_attr"]."_".$infos["vi_pa"]["id_vi_pa_multi"]."Cost";
		$this->showCost($nfo,$s,$files,$cadre_refreshed);
		return true;
	}
	
	/**
    * Retourne les réponses trouvées impliquées par la règle de coût d'une vi_pa
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $id_vi_pa 
	* @param string $regle 
    * @return string
    */
	public function costAnswers($id_vi_pa,$regle){
		return $this->analyzeAndProcess($id_vi_pa,$regle);
	}
	
	/**
    * Analyse syntaxique d'une règle, et retour d'une liste des ATTR ou PA impliqués
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $regle 
    * @return array
    */
	public function analyze($regle){
		$matches = array();
		preg_match_all("/(PA[0-9]*|A[0-9]*)/",$regle,$matches);
//log::logger($matches,ygautheron);
		foreach ($matches[1] as $ref) {
			if (substr($ref,0,2)==="PA") { // PA
				$attributs[$ref]=ATF::pa()->nom(substr($ref,2));
			} elseif (substr($ref,0,1)==="A") { // Attr
				$attributs[$ref]=ATF::attr()->nom(substr($ref,1));
			}
		}
		return $attributs;
	}
	
	/**
    * Analyse syntaxique d'une règle, et retour de la règle arithmétique FINALE évaluable
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $regle 
    * @return string
    */
	public function analyzeAndProcess($id_vi_pa,$regle){
		if (!$regle) { // Pas de règles, on retourne un coeff 1
			return 1;
		}
//log::logger("Regle avant => ".$id_vi_pa." / ".$regle,qjanon);
		$regle = preg_replace_callback(
			'/(PA[0-9]*|A[0-9]*)/'
			, create_function(
				'$matches,$id_vi_pa='.$id_vi_pa,
				'return ATF::vi_pa()->findAnswerFromReference($id_vi_pa,$matches[1]);'
			)
			, $regle
		);
//log::logger("Regle apres => ".$id_vi_pa." / ".$regle,ygautheron);
		return $regle;
	}
	
	/**
    * Analyse le nom du A ou PA et retourne la réponse associée à un vi_pa de référence
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $attribut 
    * @return numeric
    */
//	function isAnswered($id_visite,$id_attr,$id_ppa,$id_pa=NULL,$id_vi_pa_multi=NULL,$dateMax=NULL,$answerOnly=false) {
	public function findAnswerFromReference($id_vi_pa,$ref) {
//log::logger("findAnswerFromReference id_vi_pa => ".$id_vi_pa." / ".$ref,qjanon);
		$vi_pa = $this->select($id_vi_pa);
		if (substr($ref,0,2)==="PA") { // PA
			$id_pa = substr($ref,2);
//log::logger("PA => ".$id_pa,ygautheron);
			$pa = ATF::pa()->select($id_pa);
			
			// Soit avec le PPA = PA
			$answer = $this->isAnswered($vi_pa["id_visite"],$pa["id_attr"],$pa["id_pa"],$id_pa,$vi_pa["id_vi_pa_multi"],NULL,true);
			if (!$answer) { // Sans multi
				$answer = $this->isAnswered($vi_pa["id_visite"],$pa["id_attr"],$pa["id_pa"],$id_pa,NULL,NULL,true);
			}
			
			if (!$answer) {
				// Soit avec le PPA = $vi_pa["id_pa"] donné comme référence pour le calcul
				$answer = $this->isAnswered($vi_pa["id_visite"],$pa["id_attr"],$vi_pa["id_pa"],$id_pa,$vi_pa["id_vi_pa_multi"],NULL,true);
				if (!$answer) { // Sans multi
					$answer = $this->isAnswered($vi_pa["id_visite"],$pa["id_attr"],$vi_pa["id_pa"],$id_pa,NULL,NULL,true);
				}
			}
			
			if (!$answer) {
				// Soit avec le PPA = $vi_pa["id_ppa"] donné comme référence pour le calcul
				$answer = $this->isAnswered($vi_pa["id_visite"],$pa["id_attr"],$vi_pa["id_ppa"],$id_pa,$vi_pa["id_vi_pa_multi"],NULL,true);
				if (!$answer) { // Sans multi
					$answer = $this->isAnswered($vi_pa["id_visite"],$pa["id_attr"],$vi_pa["id_ppa"],$id_pa,NULL,NULL,true);
				}
			}
		} elseif (substr($ref,0,1)==="A") { // Attr
			$id_attr = substr($ref,1);
//log::logger("A => ".$id_attr,qjanon);
//log::logger($vi_pa,qjanon);

			// Avec le PPA = $id_vi_pa donné comme référence pour le calcul
//log::logger("isAnswered => ".$vi_pa["id_visite"]."/".$id_attr."/".$vi_pa["id_pa"]."/".$vi_pa["id_vi_pa_multi"]."/".$vi_pa["id_ppa"],qjanon);
			
			// Soit avec le PPA = $vi_pa["id_pa"] donné comme référence pour le calcul
			$answer = $this->isAnswered($vi_pa["id_visite"],$id_attr,$vi_pa["id_pa"],NULL,$vi_pa["id_vi_pa_multi"],NULL,true);
			if (!$answer) { // Sans multi
				$answer = $this->isAnswered($vi_pa["id_visite"],$id_attr,$vi_pa["id_pa"],NULL,NULL,NULL,true);
			}
			
			if (!$answer) {
				// Soit avec le PPA = $vi_pa["id_ppa"] donné comme référence pour le calcul
				$answer = $this->isAnswered($vi_pa["id_visite"],$id_attr,$vi_pa["id_ppa"],NULL,$vi_pa["id_vi_pa_multi"],NULL,true);
				if (!$answer) { // Sans multi
					$answer = $this->isAnswered($vi_pa["id_visite"],$id_attr,$vi_pa["id_ppa"],NULL,NULL,NULL,true);
				}
			}
			
			if (!$answer && $vi_pa["id_vi_pa_multi"]) {
				// Soit avecuniquement le MULTI et le ID_ATTR pour information... (expérimental)
				$answer = $this->isAnswered($vi_pa["id_visite"],$id_attr,NULL,NULL,$vi_pa["id_vi_pa_multi"],NULL,true);
			}
			if (!$answer && !$vi_pa["id_vi_pa_multi"]) {
				// Si pas de vi_pa_multi et toujours pas de réponse, il faut retrouver le PA de l'attr et sonder pour la réponse
				$id_gep = ATF::visite()->select($vi_pa["id_visite"],'id_gep_projet');
				if ($pa = ATF::pa()->isPA($id_attr,$id_gep,$vi_pa['id_pa'])) {
					$answer = $this->isAnswered($vi_pa["id_visite"],$id_attr,$pa['id_pa'],$pa['id_pa'],NULL,NULL,true);
				}
			}
			
		}
		
//log::logger($answer,ygautheron);5984
		if (is_numeric($answer)) {
			return $answer;
		}
		return 0;
	}
	
	/**
    * Retourne toutes les réponses associées à un PPA, une visite et un id_vi_pa_multi, qui ont un id_pa à NULL et les range dans un tableau qui a pour index les id_attr
    * @author QJ <qjanon@absystech.fr>
	* @param int $id_visite
	* @param int $id_ppa Parent le plus direct
	* @param int $id_vi_pa_multi
    */
	public function getAnswersByAttr($id_visite,$id_ppa,$id_vi_pa_multi) {
		$query = $this->getAnswers($id_visite,$id_ppa,$id_vi_pa_multi,true);
		return ATF::db()->sql2array($query,"id_attr");
	}
	
	
	/**
    * Retourne toutes les réponses saisies dans un projet et pour un ATTR
    * @author QJ <qjanon@absystech.fr>
	* @param array $infos Tableau qui doit contenir l'id ATTR, l'id VISITE et la recherche !
	* @return array Listing des réponses déjà saisies
    */
	public function autocompleteReponse(&$infos,&$s,&$files=NULL,&$cadre_refreshed=NULL) {
		$infos['display'] = true;
		if (!$infos['id_attr'] || !$infos['id_gep_projet'] || !strlen($infos['recherche'])) {
			return false;
		}
		$this->q->reset()
			->setPage(0)->setLimit(10)
			->addField("vi_pa.reponse","vi_pa")
			->addJointure("vi_pa","id_visite","visite","id_visite")
			->addCondition("id_attr",$infos['id_attr'])
			->addCondition("id_gep_projet",$infos['id_gep_projet'])
			->addCondition("reponse",ATF::db()->real_escape_string($infos['recherche'])."%","AND",false,"LIKE")
			->addCondition("LENGTH(reponse)",1,"AND",false,">")
			->addGroup("reponse");
		$var = array(
			"current_class"=>$this
			,"data"=>$this->select_all()
		);
		return $this->fetchHTML($var,NULL,$cadre_refreshed,"autocomplete-vi_pa");
//		ATF::$cr->add("autocomplete","autocomplete",$var);
//		ATF::$cr->rm("top,main");
//
//		return $cadre_refreshed["autocomplete"];
		
	}
	
	/**
    * Accessibilité : retourne les différents bâtiments créé sur une visite
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param int $id_visite
	* @return array Les numéros id_vi_pa_multi correspondants
    */ 
	public function selectAccessibiliteBatiments($id_visite,$dateMax=false){		
		// Bâtiment = id_attr 48
		// Quel est le PA de cet attribut dans le projet de cette visite ?
		$id_gep_projet = ATF::visite()->select($id_visite,'id_gep_projet');
		ATF::pa()->q->reset()->setStrict()
			->addField('id_pa')
			->addCondition("id_attr",48)
			->addCondition("id_gep_projet",$id_gep_projet)
			->setDimension("cell");
		if ($id_pa = ATF::pa()->select_all()) {
			
			// Quels sont les multiples qui ont été créé pour ce PA "bâtiment" dans cette visite ?
			$this->q->reset()->setStrict()->setToString()
				->addField('id_vi_pa','id_vi_pa')
				->addField('id_vi_pa_multi','id_vi_pa_multi')
				->addField('reponse','reponse')
				->addCondition("id_visite",$id_visite)
				->addCondition("id_pa",$id_pa);
			if ($dateMax) {
				$this->q->addCondition("date",$dateMax,"AND",false,"<=");
			}
			$subQuery = $this->select_all();
			
			// @todo gérer les sous requêtes dans le querier
			$query = 'SELECT * FROM ('.$subQuery.') AS a GROUP BY a.id_vi_pa_multi';
			if ($result = ATF::db($this->db)->sql2array($query)) {
				foreach ($result as $k => $i) {
					if (!$i["reponse"]) {
						unset($result[$k]); // Ne pas conserver les id_vi_pa avec pour dernière réponse vide
					}
				}
				return $result;
			}
		}
	}
	
	/**
    * Accessibilité : retourne les différentes occurences d'un même élement créés sur une visite, et dont le parent id_vi_pa_multi est celui du batiment
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param int $id_visite
	* @param int $id_pa
	* @param int $id_vi_pa_multi
	* @return array Les numéros id_vi_pa_multi correspondants
    */ 
	public function selectAccessibiliteElements($id_visite,$id_pa,$id_vi_pa_multi,$dateMax=false){	
		// Quels sont les multiples qui ont été créé pour ce PA "bâtiment" dans cette visite ?
		$this->q->reset()->setStrict()->setToString()
			->addField('vi_pa.id_vi_pa','id_vi_pa')
			->addField('vi_pa.id_pa','id_pa')
			->addField('vi_pa.id_vi_pa_multi','id_vi_pa_multi')
			->addField('vi_pa.reponse','reponse')
			->addField("vpm.offset","offset")
			->addJointure("vi_pa","id_vi_pa_multi","vi_pa_multi","id_vi_pa_multi","vpm",NULL,NULL,NULL,"inner")
			->addCondition("vi_pa.id_visite",$id_visite)
			->addCondition("vi_pa.id_pa",$id_pa)
			->addCondition("vpm.id_parent",$id_vi_pa_multi);
		if ($dateMax) {
			$this->q->addCondition("date",$dateMax,"AND",false,"<=");
		}
		$subQuery = $this->select_all();
		
		// @todo gérer les sous requêtes dans le querier
		$query = 'SELECT * FROM ('.$subQuery.') AS a GROUP BY a.id_vi_pa_multi ORDER BY a.offset,a.id_vi_pa_multi ASC';

		if ($result = ATF::db($this->db)->sql2array($query)) {

			foreach ($result as $k => $i) {
				if (!$i["reponse"]) {
					unset($result[$k]); // Ne pas conserver les id_vi_pa avec pour dernière réponse vide
				} else {
					$result[$k]["id_vi_pa_localisation"] = $this->getLocalisationReponse($id_visite,$i["id_pa"],$i["id_vi_pa_multi"]);
					$result[$k]["id_vi_pa_plan"] = $this->getPlanReponse($id_visite,$i["id_pa"],$i["id_vi_pa_multi"]);
					$result[$k]["id_vi_pa_photos"] = $this->getPhotosReponse($id_visite,$i["id_pa"],$i["id_vi_pa_multi"]);
				}
			}
			return $result;
		}
	}
	
	/**
    * Accessibilité : retourne l'id_vi_pa du plan de l'élement parent passé en paramètre
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param int $id_visite
	* @param int $id_pa id_pa de l'élément parent
	* @param int $id_vi_pa_multi
	* @return int $id_vi_pa de la réponse plan
    */ 
	public function getPlanReponse($id_visite,$id_pa,$id_vi_pa_multi){		
		$this->q->reset()->setStrict()->setDimension("cell")
			->addField("vi_pa.id_vi_pa","id_vi_pa")
			->addJointure("vi_pa","id_pa","pa","id_pa","p1",NULL,NULL,NULL,"inner")
			->addCondition("vi_pa.id_visite",$id_visite)
			->addCondition("p1.id_parent",$id_pa)
			->addCondition("vi_pa.id_attr",3783) // Le plan
			->addCondition("vi_pa.id_vi_pa_multi",$id_vi_pa_multi)
			->addOrder("vi_pa.date","desc");
		return $this->select_all();
	}
	
	/**
    * Accessibilité : retourne l'id_vi_pa de la localisation de l'élément
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param int $id_visite
	* @param int $id_pa id_pa de l'élément parent
	* @param int $id_vi_pa_multi
	* @return int $id_vi_pa de la réponse plan
    */ 
	public function getLocalisationReponse($id_visite,$id_pa,$id_vi_pa_multi){		
		$this->q->reset()->setStrict()->setDimension("cell")
			->addField("vi_pa.reponse","reponse")
			->addJointure("vi_pa","id_pa","pa","id_pa","p1",NULL,NULL,NULL,"inner")
			->addCondition("vi_pa.id_visite",$id_visite)
			->addCondition("p1.id_parent",$id_pa)
			->addCondition("vi_pa.id_attr",3782) // La localisation
			->addCondition("vi_pa.id_vi_pa_multi",$id_vi_pa_multi)
			->addOrder("vi_pa.date","desc");
		return $this->select_all();
	}
	
	/**
    * Accessibilité : retourne l'id_vi_pa des photos de l'élément
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param int $id_visite
	* @param int $id_pa id_pa de l'élément parent
	* @param int $id_vi_pa_multi
	* @return array 
    */ 
	public function getPhotosReponse($id_visite,$id_pa,$id_vi_pa_multi){		
		$this->q->reset()->setStrict()->setToString()
			->addField("vi_pa.id_vi_pa","id_vi_pa")
			->addField("vi_pa.reponse","reponse")
			->addField("vi_pa.id_vi_pa_multi","id_vi_pa_multi")
			->addJointure("vi_pa","id_vi_pa_multi","vi_pa_multi","id_vi_pa_multi","vpm",NULL,NULL,NULL,"inner")
			->addCondition("vi_pa.id_visite",$id_visite)
			->addCondition("vi_pa.id_attr",3784) // Les photos d'accessibilité
			->addCondition("vpm.id_parent",$id_vi_pa_multi);
		$subQuery = $this->select_all();
		
		// @todo gérer les sous requêtes dans le querier
		$query = 'SELECT * FROM ('.$subQuery.') AS a GROUP BY a.id_vi_pa_multi';
		if ($result = ATF::db($this->db)->sql2array($query)) {
			foreach ($result as $k => $i) {
				if ($i["reponse"]) {
					$return[] = $i["id_vi_pa"];
				}
			}
			return $return;
		}
	}
	
	/**
    * Accessibilité : retourne tous les constats à OUI enfants d'un PA
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param int $id_visite
	* @param int $id_pa de l'élément
	* @param int $id_vi_pa_multi de l'élément
	* @return array $constats Les constats
    */ 
	public function getConstatsAccessibilite($id_visite,$id_pa,$id_vi_pa_multi,$dateMax=NULL){		
		$this->q->reset()->setStrict()->setToString()
			->addField("vi_pa.id_vi_pa","id_vi_pa")
			->addField("vi_pa.id_pa","id_pa")
			->addField("vi_pa.id_attr","id_attr")
			->addField("vi_pa.reponse","reponse")
			->addField("vi_pa.id_vi_pa_multi","id_vi_pa_multi")
//			->addField("vi_pa.id_cout_catalogue","id_cout_catalogue")
			->addJointure("vi_pa","id_pa","pa","id_pa","p1",NULL,NULL,NULL,"inner")
			->addJointure("p1","id_parent","pa","id_pa","p2",NULL,NULL,NULL,"inner")
			->addField("p2.id_pa","id_pa_oui")
			->addJointure("p2","id_parent","pa","id_pa","p3",NULL,NULL,NULL,"inner")
			->addJointure("vi_pa","id_vi_pa_multi","vi_pa_multi","id_vi_pa_multi","vpm",NULL,NULL,NULL,"inner") // Multi du scénario
			->addCondition("vi_pa.id_attr",3708) // Scénario
			->addCondition("p3.id_attr",3707) // Constat
			->addCondition("p3.id_parent",$id_pa) // Element d'accessibilité
			->addCondition("vpm.id_parent",$id_vi_pa_multi) // Le parent est le multi de l'élément
			->addOrder("p3.offset","asc");
		if ($dateMax) {
			$this->q->addCondition("date",$dateMax,"AND",false,"<=");
		}
		$subQuery = $this->select_all();
		
		// @todo gérer les sous requêtes dans le querier
		$query = 'SELECT * FROM ('.$subQuery.') AS a GROUP BY a.id_pa, a.id_vi_pa_multi';
//log::logger($query,qjanon);
		if ($result = ATF::db($this->db)->sql2array($query)) {
			foreach ($result as $k => $i) {
				if ($i["reponse"]) {
					$costs = ATF::vi_pa_cout()->getCosts($result[$k]["id_vi_pa"]);
					$result[$k]["cout_catalogue"] = $costs[0]['cout_catalogue'];
					//Ancien code qui faisait un méchant mélange dans les données...
//					$result[$k]["cout_catalogue"] = ATF::cout_catalogue()->select($result[$k]["id_cout_catalogue"]);
					// A gérer plus tard pour les coûts multiples
//					foreach ($costs as $k_=>$i_) {
//						$result[$k]["cout_catalogue"][$k_] = $costs[$k_]['cout_catalogue'];
//					}
					// Coût catalogue spécifique de projet
					if ($result[$k]["id_cout_catalogue"]) {
						$id_gep_projet = ATF::pa()->select($i["id_pa"],"id_gep_projet");
						if ($cc_gep=ATF::cout_catalogue_gep()->coutSpecifique($result[$k]["id_cout_catalogue"],$id_gep_projet)) {
							$result[$k]["cout_catalogue"]["cout_unitaire"] = $cc_gep;
						}
					}
						
					$result[$k]["numero_scenario"] = $this->getNumeroScenario($id_visite,$i["id_pa"],$i["id_vi_pa_multi"]);
					$result[$k]["commentaire"] = $this->getCommentaire($id_visite,$i["id_pa"],$i["id_vi_pa_multi"]);
					$result[$k]["mesure"] = $this->getMesure($id_visite,$i["id_pa_oui"],$id_vi_pa_multi);
					$result[$k]["cout_catalogue_accessibilite"] = ATF::cout_catalogue_accessibilite()->select($result[$k]["cout_catalogue"]["id_cout_catalogue_accessibilite"]);
//$result[$k]["plan"] = $this->isAnswered($id_visite,3783,$id_pa,$id_pa,$id_vi_pa_multi);
					$result[$k]["cout"] = ATF::vi_pa()->getCost($result[$k]["id_vi_pa"]);
					$result[$k]["priorite"] = ATF::vi_pa()->getPriorite($result[$k],$id_visite,$i["id_vi_pa_multi"]);
				} else {
					unset($result[$k]);
				}
			}
			
			// Trier par le prix
			foreach ($result as $k => $row) {
				$couts[$k]  = $row['cout'];
			}
			array_multisort($couts, SORT_DESC, $result);
			
//log::logger($result,ygautheron);
//log::logger(count($result),ygautheron);
			return $result;
		}
	}
	
	/**
    * Accessibilité : retourne le champ texte "mesure" correspondant à la visite et à la branche parente du scénario id_pa_scenario
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param int $id_visite
	* @param int $id_pa_oui Le id_pa de la case a cocher "oui" du desordre d'accessibilité
	* @param int $id_vi_pa_multi
	* @param int $id_pa
    */ 
	public function getMesure($id_visite,$id_pa_oui,$id_vi_pa_multi){		
		// Récupération de l'id_pa du numéro
		ATF::pa()->q->reset()->setStrict()
			->setDimension('cell')
			->addField("pa.id_pa","id_pa")
			->addCondition("id_attr",3944)
			->addCondition("id_parent",$id_pa_oui);
		if ($id_pa = ATF::pa()->select_all()) {
			$r = $this->isAnswered($id_visite,3944,$id_pa,$id_pa,$id_vi_pa_multi);
			return $r['reponse'];
		}
	}
	
	/**
    * Accessibilité : retourne le numéro du scénario correspondant à la visite et au scénario id_pa_scenario
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param int $id_visite
	* @param int $id_pa_scenario
	* @param int $id_vi_pa_multi
	* @param int $id_pa
    */ 
	public function getNumeroScenario($id_visite,$id_pa_scenario,$id_vi_pa_multi){		
		// Récupération de l'id_pa du numéro
		ATF::pa()->q->reset()->setStrict()
			->setDimension('cell')
			->addField("pa.id_pa","id_pa")
			->addCondition("id_attr",3792)
			->addCondition("id_parent",$id_pa_scenario);
		$id_pa = ATF::pa()->select_all();
//log::logger($id_pa."-".$id_vi_pa_multi,ygautheron);		
		$r = $this->isAnswered($id_visite,3792,$id_pa,$id_pa,$id_vi_pa_multi);
		return $r['reponse'];
	}
	
	/**
    * Accessibilité : retourne le commentaire du scénario correspondant à la visite et au scénario id_pa_scenario
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param int $id_visite
	* @param int $id_pa_scenario
	* @param int $id_vi_pa_multi
	* @param int $id_pa
    */ 
	public function getCommentaire($id_visite,$id_pa_scenario,$id_vi_pa_multi){		
		// Récupération de l'id_pa du numéro
		ATF::pa()->q->reset()->setStrict()->setDimension('cell')
			->addField("pa.id_pa","id_pa")
			->addCondition("id_attr",3714)
			->addCondition("id_parent",$id_pa_scenario);
		$id_pa = ATF::pa()->select_all();
//log::logger($id_pa."-".$id_vi_pa_multi,ygautheron);		
		$r = $this->isAnswered($id_visite,3714,$id_pa,$id_pa,$id_vi_pa_multi);
		return $r['reponse'];
	}
	
	/**
    * Accessibilité : retourne le constat le pire
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param array $constats Les constats
	* @param string $handicap L'un des 6 handicaps
	* @param int $scenario Numéro du scenario
	* @param bool $toTerme Retourne le terme et non la note
    */ 
	public function getPireConstat(&$constats,$handicap,$scenario,$toTerme=true){		
		$pireNote = 1; // Note par défaut
		foreach ($constats as $i) {
			if (!$scenario || !$i["numero_scenario"] || $i["numero_scenario"]==$scenario) { // On ne calcul que pour ce scenario + là où le scenario n'est pas précisé
				$pireNote = max($pireNote,$i["cout_catalogue_accessibilite"][$handicap]);
			}
			
			// Mise à jour ticket 3797
			if (!$id_gep_projet) {
				$id_gep_projet = ATF::visite()->select(ATF::vi_pa()->select($i["id_vi_pa"],"id_visite"),"id_gep_projet");
			}
		}
		
		switch ($id_gep_projet) {
			// Mise à jour ticket 3797
			case 50: // Projet DDE 59
				$pireNote--;
				if ($pireNote<1) {
					$pireNote = 1;
				}
				break;
			default:
		}
		
		if ($toTerme) {
			$pireNote = $this->noteToTerme($pireNote,ATF::pa()->select($constats[0]["id_pa"],"id_gep_projet"));
		}
		return $pireNote;
	}
	
	/**
    * Accessibilité : transorme la note en terme administratif
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param int $note
	* @param int $id_gep_projet
	* @return string $terme
    */ 
	public function noteToTerme($note,$id_gep_projet){
//log::logger($id_gep_projet,ygautheron);		
		switch ($id_gep_projet) {
			case 42: // 09.130 - La Réunion - Accessibilité
				$termes = array(NULL,"<span style='font-size:smaller; color:green'>R.F</span>","<span style='font-size:smaller; color:yellow'>R.NF</span>","<span style='font-size:smaller; color:orange'>NR.F</span>","<span style='font-size:smaller; color:red'>NA</span>");
				return $termes[$note];
				break;
				
			default:
				return $note;
		}
	}
	
	/**
    * Accessibilité : retourne le constat avec les gains
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param array $constats Les constats
	* @param string $handicap L'un des 6 handicaps
	* @param int $scenario Numéro du scenario
	* @param bool $toTerme Retourne le terme et non la note
    */ 
	public function getBetterConstat(&$constats,$handicap,$scenario,$toTerme=true){
		$noteInitiale = $this->getPireConstat($constats,$handicap,$scenario,false); // Note par défaut
		$pireNote=1;
		foreach ($constats as $i) {
			if (!$scenario || !$i["numero_scenario"] || $i["numero_scenario"]==$scenario) { // On ne calcul que pour ce scenario + là où le scenario n'est pas précisé
				$pireNote = max($pireNote,$i["cout_catalogue_accessibilite"]["action_".$handicap]);
			}
			
			// Mise à jour ticket 3797
			if (!$id_gep_projet) {
				$id_gep_projet = ATF::visite()->select(ATF::vi_pa()->select($i["id_vi_pa"],"id_visite"),"id_gep_projet");
			}
		}
		$betterNote = min($pireNote,$noteInitiale);
		switch ($id_gep_projet) {
			// Mise à jour ticket 3797
			case 50: // Projet DDE 59
				$betterNote--;
				if ($betterNote<1) {
					$betterNote = 1;
				}
				break;
			default:
		}
		
		if ($toTerme) {
			$betterNote = $this->noteToTerme($betterNote,ATF::pa()->select($constats[0]["id_pa"],"id_gep_projet"));
		}
		return $betterNote;
	}
	
	/**
    * Accessibilité : retourne VRAI si un scenario 2 existe
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param array $constats Les constats
    */ 
	public function existScenario2(&$constats){
		foreach ($constats as $i) {
			if ($i["numero_scenario"]==2) {
				return true;
			}
		}
		return false;
	}
	
	/**
    * Accessibilité : retourne la somme de tous les coûts des constats
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
    */ 
	public function getConstatsAccessibiliteTotal(&$constats,$type=false,$scenario=NULL){		
		$total = 0;
		foreach ($constats as $i) {
//log::logger($i,ygautheron);				
//log::logger($i["cout"]." / ".$i["cout_catalogue_accessibilite"]["reg_ct"]."/".$i["cout_catalogue_accessibilite"]["reg_erp"]." => ".($type===false)
//	." || ".$i["cout_catalogue_accessibilite"][$type]." && (".($type=="reg_ct")." && ".(!$i["cout_catalogue_accessibilite"]["reg_erp"])." || ".($type=="reg_erp").")"
//	." || ".($type===NULL)." && ".(!$i["cout_catalogue_accessibilite"]["reg_ct"])." && ".(!$i["cout_catalogue_accessibilite"]["reg_erp"]),ygautheron);				
			if ($type===false 
				|| $i["cout_catalogue_accessibilite"][$type] && ($type=="reg_ct" && !$i["cout_catalogue_accessibilite"]["reg_erp"] || $type=="reg_erp")
				|| $type===NULL/*  && !$i["cout_catalogue_accessibilite"]["reg_ct"]*/ && !$i["cout_catalogue_accessibilite"]["reg_erp"]) {  
				if (!$scenario || !$i["numero_scenario"] || $i["numero_scenario"]==$scenario) { // On ne calcul que les coûts pour ce scenario + là où le scenario n'est pas précisé
					$total += $i["cout"];
				}
			}
		}
		return $total;
	}
	
	/**
    * Accessibilité : retourne les constats cotés regroupés par constat (dédoublonne)
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
    */ 
	public function groupConstats($constats){	
		foreach ($constats as $k=>$i) {
			if ($d[$i["cout_catalogue_accessibilite"]["code"]] || !$i["cout_catalogue"]) {
				unset($constats[$k]);
				continue;
			}
			$d[$i["cout_catalogue_accessibilite"]["code"]]=true;
		}
		return $constats;
	}
	
	/**
    * Accessibilité : retourne les constats de ce numéro de scénario
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
    */ 
	public function getConstatScenario(&$constats,$numero_scenario){	
		foreach ($constats as $k=>$i) {
			if ($i["numero_scenario"]==$numero_scenario) {
				$c[$k]=$i;
			}
		}
		return $c;
	}
	
	/**
    * Accessibilité : retourne les commentaires de ce numéro de scénario
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
    */ 
	public function getConstatCommentaires(&$constats,$numero_scenario){	
		foreach ($constats as $k=>$i) {
			if ($i["numero_scenario"]==$numero_scenario && $i["commentaire"]) {
				$c[$k]=$i;
			}
		}
		return $c;
	}
	
	/**
    * Retourne le nombre de désordre pour la visite
	* @param int $id_visite
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
    */
	public function getNbDesordres($id_visite){	
		$id_gep_projet = ATF::visite()->select($id_visite,'id_gep_projet');
		return $this->getDesordres($id_gep_projet,$id_visite,true);
	}
		
	/**
    * Listing désordre : Renvoi un tableau contenant tous les désrodres pour un projet.
	* @param int $id_gep_projet
	* @param int $id_visite
	* @param boolean $countOnly Ne retourne que le nombre de désordres
	* @author QJ <qjanon@absystech.fr>
    */
	function getDesordres($id_gep_projet,$id_visite,$countOnly=false,$dateMax=NULL){	
	    $this->q->reset();
		switch ($id_gep_projet) {
			case 26: 
			case 100: 
				$id_desordre = 3831; 
			break; // 09.141 - DIR SO
			case 34: $id_desordre = 3862; break; // 09.114 - SNCF Région Est
			default: $id_desordre = 3505; break;
		}

	    $this->q
			->addField("vi_pa.id_vi_pa","id_vi_pa")
			->addField("vi_pa.id_pa","id_pa")
			->addField("vi_pa.id_attr","id_attr")
			->addField("vi_pa.id_ppa","id_ppa")
			->addField("vi_pa.reponse","reponse")
			->addField("vi_pa.id_vi_pa_multi","id_vi_pa_multi")
			->addCondition("vi_pa.id_visite",$this->decryptID($id_visite))
			->addCondition("vi_pa.id_attr",$id_desordre)
			->addOrder("vi_pa.date","desc")
			->setStrict()
			->setToString();
		if ($dateMax) {
			$this->q->addCondition("vi_pa.date",$dateMax,"AND",false,"<=");
		}
        $subQuery = $this->select_all();
		$this->q->reset()
			->addAllFields("s")
			->setSubQuery($subQuery,'s')
			->addGroup("id_pa,id_ppa,id_vi_pa_multi")
			->setStrict()
			->setToString();
        $subQuery2 = ATF::db($this->db)->select_all($this);
		$this->q->reset()
			->addAllFields("s2")
			->setSubQuery($subQuery2,'s2')
			->addCondition("reponse",1);
		
		// Ne retourner que le nombre de désordres
		if ($countOnly) {
			$this->q->setCountOnly()
				->addField("visite")
				->setStrict();
		}
		
		$return = ATF::db($this->db)->select_all($this);
		return $return;
		// Pour détecter les désordres fantôme, il faut faire apparaitre la liste des désordres relevés
		// Dans cette liste il faut détécter tous les désordres qui ont le même multi qu'un bâtiment
		// Et oui comme les désordres sont tous des multi (dans le meilleur des mondes), ils doivent avoir leur propre ID MULTI
		// Si ce n'est pas le cas il s'agit alors d'un désordre fantôme, il faut aller mettre NULL dans le champ réponse du vi_pa !
		print_r($return);die();
	}
	
	/**
    * Renvoi un tableau contenant tous les désrodres pour un projet.
	* @author QJ <qjanon@absystech.fr>
    */ 
	public function getPriorite($infos,$id_visite,$id_multi=false) {
		$c = ATF::pa()->selectChilds($infos['id_pa']);
		if (!$c) {
			$c = 	ATF::attr()->selectChilds($infos['id_attr']);
		}
		
		foreach ($c as $k=>$i) {
			if ($i['id_attr']==3805) { //Priorité
				$priorite = $i;
				break;
			}	
		}
		if (!$priorite) return "D";
		if ($id_multi) {
			$priorite['id_vi_pa_multi'] = $id_multi;
		}
		$return = $this->getEnumReponse($priorite,$id_visite);
		
		if (!$return) return "D";
		else return ATF::attr()->select($return['id_attr'],'attr');
	}

	
	/**
    * Renvoi un tableau contenant tous les désrodres pour un projet.
	* @author QJ <qjanon@absystech.fr>
    */ 
	public function getEnumReponse($infos,$id_visite,$dateMax=NULL) {
		//if ($infos['id_attr']!=3851) return;
		$enfants = ATF::pa()->selectChilds($infos['id_pa']);
		if (!$enfants) {
			$enfants = ATF::attr()->selectChilds($infos['id_attr']);
		}
		foreach ($enfants as $k=>$i) {
			try{
				//echo "====V".$id_visite."====A".$i['id_attr']."====PPA".($i['id_pa']?$i['id_pa']:$infos['id_pa'])."====PA".$i['id_pa']."====M".$infos['id_vi_pa_multi']."====D".$dateMax."====\n";
				$vi_pa=ATF::vi_pa()->isAnswered($id_visite,$i['id_attr'],$i['id_pa']?$i['id_pa']:$infos['id_pa'],$i['id_pa'],$infos['id_vi_pa_multi'],$dateMax);
			} catch(errorATF $e) { }
			if ((!$reponse || ($vi_pa && strtotime($reponse['date'])<strtotime($vi_pa['date']))) && $vi_pa['reponse']) {
				$reponse = $vi_pa;
			}
		}
//		print_r($reponse);
//		die();
		return $reponse;
	}


	/**
    * Retourne la règle imposée de coût pour cette réponse
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $id_vi_pa 
    * @return string
    */
	public function getRegle($id_vi_pa) {
		$v = $this->select($id_vi_pa,"id_pa,id_attr");
		if ($v["id_pa"]) {
			// On a un PA, on cherche la règle de son attribut
			return ATF::pa()->getRegle($v["id_pa"]);
		} else {
			// On n'a pas de PA, on prend directement la règle de l'attribut
			return ATF::attr()->getRegle($v["id_attr"]);
		}
	}
	
	/**
    * Accessibilité : Renvoi un tableau contenant tous les désrodres pour un projet d'accessibilite.
	* @author QJ <qjanon@absystech.fr>
    */ 
	public function getCostAccessibilite($id_visite,$numScenario,$loc){	
		switch ($numScenario) {
			case 1:
				$p = array("P1","P2","R");
			break;
			case 2:
				$p = array("P1","P2");
			break;
			case 3:
				$p = array("P1");
			break;
			default:
				$p = array("P1","P2","P3","R");
			break;
		}
	
		$Cost = 0;
		$this->q->reset()
					->addCondition("vi_pa.id_visite",$this->decryptID($id_visite))
					->addCondition("vi_pa.id_attr",3708)
					->addGroup("id_pa,id_ppa,id_vi_pa_multi");
		$return = $this->select_all();
		foreach ($return as $k=>$i) {
			//Récupération du numéro de scénario saisi dans le formulaire
			$enfants = ATF::pa()->selectChilds($i['id_pa']);
			if (!$enfants) {
				$enfants = ATF::attr()->selectChilds($i['id_attr']);
			}
			foreach ($enfants as $k_=>$i_) {
				if ($i_['id_attr']==3805) {
					$priorite = $this->getEnumReponse($i_,$id_visite);
					$prio = ATF::pa()->select($priorite['id_pa'],'pa');
					if (!$prio) {
						$prio = ATF::attr()->select($priorite['id_attr'],'attr');
					}
				} elseif ($i_['id_attr']==3715) {
					$local = $this->getEnumReponse($i_,$id_visite);
				}
			}
			// Si c'est le bon numéro de scénario on résupère le coût.
			if (in_array($prio,$p) && $local['id_attr']==$loc) {
				foreach (ATF::vi_pa_cout()->getCosts($i['id_vi_pa']) as $k_=>$i_) {
					$Cost += $i_['cout_unitaire_calcule'];
				}
			}
		}		
		return $Cost;
		print_r($Cost);die("Cost");
	}
	
	/**
    * Récupère le bâtiment parent de l'attribut ou du PA
	* L'attribut Batiment doit être du type TEXT, sinon return false
    * @author QJ <qjanon@absystech.fr>
    * @param int $infos tableau contenant les infos de l'attr et/ou du pa (au moins un de leur ID respectif)
    * @param int $idBat ID de l'attr BATIMENT : 48 par défaut
    * @return int $id_vi_pa de reference
    */   
	function getBatiment($infos,$idBat=48) {
		if (!$infos['id_vi_pa_multi']) return false;
//		echo "=========".$infos['id_vi_pa_multi']."==================\n";
//		print_r($infos);
		
		$current = ATF::vi_pa_multi()->select($infos['id_vi_pa_multi']);
		if (!$current['id_parent']) {
			if (ATF::vi_pa()->select($current['id_vi_pa'],'id_attr')==$idBat) {
				$hop = $this->select($current['id_vi_pa']);
				$r = $this->isAnswered($hop['id_visite'],$hop['id_attr'],$hop['id_ppa'],$hop['id_pa'],$hop['id_vi_pa_multi']);
				return $r ? $r['id_vi_pa'] : $current['id_vi_pa'];
			} else {
				return false;
			}
		} else {
			// Recherche du parent et vérification s'il s'agit du $idBat
			ATF::vi_pa_multi()->q->reset()
											->addCondition("vi_pa_multi.id_vi_pa_multi",$current['id_parent'])
											->setDimension('row');
			$parent = ATF::vi_pa_multi()->select_all();
			$vi_pa = $this->select($parent['id_vi_pa']);
			$parent = array_merge($parent,$vi_pa);
//			print_r($parent);
			if ($parent['id_attr']==$idBat) {
				$vi_pa = $this->isAnswered($parent['id_visite'],$idBat,$parent['id_ppa'],$parent['id_pa'],$parent['id_vi_pa_multi']);
//				print_r($vi_pa);
				if (ATF::attr()->select($vi_pa['id_attr'],'type')!='text') return false;
				return $vi_pa['id_vi_pa'];
			} else {
//				echo "\nON REPLONGE\n";
				return self::getBatiment($parent);
			}
		}		
	}	
	
	/* Autocomplete sur toutes les réponses d'un attribut
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos ($_POST habituellement attendu)
	*	string $infos[id_vi_pa]
	* @param boolean $reset VRAI si on reset lme querier, FAUX si on a initialisé qqch de précis avant...
	* @return json
	*/
	public function autocompleteHistorique($infos,$reset=true) {
		if ($reset) {
			$this->q->reset();
		}
		$this->q
			->from("vi_pa","id_user","user","id_user")
			->where("id_visite",$this->decryptId(ATF::_g("v")))
			->where("id_ppa",ATF::_g("ppa"))
			->where("id_attr",ATF::_g("a"))
			->addOrder("vi_pa.date","desc");
			
		if (ATF::_g("m")) {
			$this->q->where("id_vi_pa_multi",ATF::_g("m"));
		} else {
			$this->q->whereIsNull("id_vi_pa_multi");
		}
		
		if (ATF::_g("pa")) {
			$this->q->where("id_pa",ATF::_g("pa"));
		} else {
			$this->q->whereIsNull("id_pa");
		}

		return $this->autocomplete($infos,false);
	}
	
	public function formatDateReference($date) {
		return substr($date,0,4)."-".substr($date,4,2)."-".substr($date,6,2)." ".substr($date,8,2).":".substr($date,10,2).":".substr($date,12,2);
	
	}
	
	public function updateVPMOffset($infos,$s,$files,$cr) {
		return ATF::vi_pa_multi()->update($infos);
	
	}
	
	/* Autocomplete sur toutes les réponses d'un attribut
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param array $infos ($_POST habituellement attendu)
	* @return json
	*/
	public function getAllConclusion($req) {
		if (!$req['id_visite']) return false;
		else $id_visite = ATF::visite()->decryptID($req['id_visite']);
		$visite = ATF::visite()->select($id_visite);
		$projet = ATF::gep_projet()->select($visite['id_gep_projet']);
		$desordres = ATF::vi_pa()->getDesordres($visite['id_gep_projet'],$id_visite);
		
		foreach ($desordres as $k=>$i) {
			$parentDuDesordre = ATF::pa()->select(ATF::pa()->select($i['id_pa'],'id_parent'));
			$d[ATF::vi_pa()->getBatiment($i)][$parentDuDesordre['id_pa']] = $parentDuDesordre;
		}
		return $d;
	}
};
?>