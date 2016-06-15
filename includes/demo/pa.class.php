<?
/**
* Classe PA => correspond aux attributs de projet
*
*
* @date 2009-10-31
* @package inventaire
* @version 1.0.0
* @author QJ <qjanon@absystech.fr>
* @author Yann GAUTHERON <ygautheron@absystech.fr>
*
*/ 
class pa extends attributs {
	/**
	* Constructeur
	*/
	public function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		
		//Colonnes SELECT ALL
		$this->colonnes['fields_column'] = array(	
			'pa.id_gep_projet'
			,'pa.id_attr'
			,'pa.id_parent'
		);
		
		//Colonnes SELECT
		$this->colonnes['primary'] = array(
			'id_gep_projet'
			,'id_attr'
			,'id_parent'
		);

		$this->fieldstructure();
		
		$this->foreign_key["id_parent"] = $this->table;
		$this->field_nom = "%id_gep_projet% > [PA]%id_pa%-%pa% > [A]%id_attr%";
		
		// Droits
		$this->addPrivilege("cout");
		$this->addPrivilege("childs2PA","insert");
		$this->addPrivilege("duplicate","insert");
		$this->addPrivilege("updateLibelle","update");
		$this->addPrivilege("updateOffset","update");
		$this->addPrivilege("updateRef","update");
		
		$this->noTrim = true;
	}	
	
	/**
    * Fonction qui ertourne les PE root d'un formulaire
    * @author QJ <qjanon@absystech.fr>
    * @return int $id_gp L'id du projet
    * @return array Listing
    */   
	public function selectRootFromProjet($id_gp,$orderBy="pa.offset") {
		if (!$id_gp) {
			return false;
		}
		if (strlen($id_gp)==32) {
			$id_gp = ATF::gep_projet()->decryptid($id_gp);
		}
		$this->q->reset()
					->addCondition("id_gep_projet",$id_gp)
					->addCondition("pa.id_parent",NULL,false,false,"IS NULL")
					->addJointure("pa","id_attr","attr","id_attr")
					->addOrder($orderBy);
		return $this->select_all();
	}

	/**
    * Retourne vrai si le PA a des enfants PA
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    * @param array $array Tableau de PA
    * @return bool TRUE s'il y a des enfants, sinon FALSE
    */   
	public function hasChild($id_pa) {
		if (!$id_pa) {
			return false;
		}
		$this->q->reset()
			->setCountOnly()		
			->addCondition("pa.id_parent",$id_pa);
		return $this->select_all()>0;
	}
	
	/**
    * Renvoi les pe enfants d'un autre pe ordonnée par offset
    * @author QJ <qjanon@absystech.fr>
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    * @param int $id_pe L'id du PE
    * @param string $orderBy Colonne de la table sur laquelle effectuer le tri.
    * @param boolean $count
    * @param boolean $jointure
    * @return array listing des pe
    */   
	public function selectChilds($id_pa,$orderBy="pa.offset",$count=false,$jointure=true,$id_attr=false) {
		if (!$id_pa) {
			return false;
		}
		$this->q->reset()
					->addCondition("pa.id_parent",$id_pa);
		if ($jointure) {
			$this->q->addJointure("pa","id_attr","attr","id_attr");
		}
		if ($orderBy) {
			$this->q->addOrder($orderBy);
		}
		if ($count) {			
			$this->q->setCountOnly();
		}
		if ($id_attr) {			
			$this->q->where("attr.id_attr",$id_attr);
			$this->q->setDimension("row");
		}
		return $this->select_all();
	}
	
	/**
    * Renvoi le nombre de lien de parenté d'un pa
    * @author QJ <qjanon@absystech.fr>
    * @param int $id_pa l'ID PA
    * @param int $nb Le nombre de lien
    * @return int $nb le nombre de lien final
    */   
	public function lvlParent($id_pa,$nb=1) {
		if (!$id_pa) {
			return false;
		}
		if ($parent = $this->select($this->select($id_pa,'id_parent'))) {
			$nb++;
			$nb = self::lvlParent($parent['id_pa'],$nb);
		}
		return $nb;
	}

	/**
    * Renvoi le type d'attribut du parent
    * @author QJ <qjanon@absystech.fr>
    * @param int $id_parent l'ID PARENT
    * @return string Type du parent
    */   
	public function typeParent($id_pa) {
		if (!$id_pa) {
			return false;
		}
		$parent = $this->select($this->select($id_pa,'id_parent'));
		return ATF::attr()->select($parent['id_attr'],'type');
	}	

	/**
    * Fonction qui teste si un ensemble de PA a des enfants
    * @author QJ <qjanon@absystech.fr>
    * @param array $array Tableau de PA
    * @return bool TRUE s'il y a des enfants, sinon FALSE
    */   
	public function getChild($array) {
		if (is_array($array)) {
			foreach ($array as $k=>$i) {
				if ($this->selectChilds($i['id_pa'],"pa.offset",true)) {
					return true;
				}
			}
		}
		return false;
	}

	/**
    * Mise a jour du champ multi, et retour du fetch du template gep_projet-formulaire_attr_multi.tpl.htm
    * @author QJ <qjanon@absystech.fr>
    * @param array $infos correspond au informations posté par l'AJAX
    * @return bool TRUE si ok, sinon FALSE
    */   
	public function updateMulti($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL,$nolog=false) {
		$d["multi"]=$infos["multi"];
		$d["id_pa"]=$infos["id_pa"];
		if (parent::update($d,$s)) {
			$var = array(
				"current_class"=>$this
				,"infos"=>ATF::pa()->select($infos['id_pa'])
			);
			ATF::$msg->addNotice("Mode multiple modifié pour le PA".$infos["id_pa"].".");
			$this->fetchHTML($var,'multi_'.$infos['id_attr'].'_'.$infos['id_pa'],$cadre_refreshed,'gep_projet-formulaire_attr_multi');
			return true;
		}
		return false;
	}
	
	/**
    * Duplique un élément et créer les liaisons vers ses attributs puis vers ses éléments enfants
    * @author QJ <qjanon@absystech.fr>
    * @param array $infos $id_elmt
    * @return array Listing
      
	function duplicate($infos,&$s,$files=NULL,&$cadre_refreshed) {
		if (!$infos['id_attr']) {
			throw new errorATF(ATF::$usr->trans($this->table,'module')." - ".ATF::$usr->trans("Pas_d_id_attr_transmis",$this->table));
		}
		if (!$infos['id_pa']) {
			throw new errorATF("Fonction ".ATF::$usr->trans($this->table,'module')."::Duplicate() - ".ATF::$usr->trans("Pas_d_id_pa_transmis",$this->table));
		}
		ATF::db($this->db)->autocommit(false);
		$attr_old = ATF::attr()->select($infos['id_attr']);
		
		// On configure le nouvel attribut
		$attr_new = $attr_old;
		if (isset($attr_new['id_attr'])) unset($attr_new['id_attr']);
		$attr_new['attr'] = "[CLONE] - ".$attr_new['attr'];
		// On l'insère ainsi que ces enfants
		$id_attr = ATF::attr()->insert($attr_new,$s,$files,$cadre_refreshed);
		
		//On récupère l'id projet grace au pa
		$id_gp = $this->select($infos['id_pa'],"id_gep_projet");
		//Selection des attribut de l'ancien attr pour les associer aussi au clone
		foreach (ATF::attr()->selectChilds($infos['id_attr']) as $k=>$i) {
			if (!$this->isPA($i['id_attr'],$id_gp,$infos['id_pa'])) {
				$insertPA = array(
					"id_parent"=>$infos['id_pa']
					,"id_attr"=>$i['id_attr']
					,"offset"=>$i['offset']
					,"id_fct_attr"=>$i['id_fct_attr']
					,"id_fct_cout"=>$i['id_fct_cout']
					,"id_gep_projet"=>$id_gp
				);
	
				$this->insert($insertPA,$s,$files,$cadre_refreshed);
				if ($childPA = ATF::pa()->selectChilds($i['id_pa'])) {
					foreach ($childPA as $k_=>$i_) {
						self::duplicate($i_,$s,$files,$cadre_refreshed);
					}
				}
			}
		}
		
		// Liaisons au projet
		// modification de l'id_attr de l'ancienne liaison
		$infosPA = $attr_new;
		$infos['id_pa'] = $infos['id_pa']
		$affectedRows = $this->update(array("id_pa"=>$infos['id_pa'],"id_gp"=>$id_gp);

		if(!$affectedRows) {
			ATF::db($this->db)->rollback();
			throw new errorATF("Fonction ".ATF::$usr->trans($this->table,'module')."::Duplicate() - ".ATF::$usr->trans("liaison_nouvel_attr_NOK",$this->table));
		}
		
		ATF::db($this->db)->autocommit(true);
		
		//Retour
		$var = array(
			"current_class"=>$this
			,"infos"=>$this->select($infos['id_pa'])
			,"id_gp"=>$id_gp
		);
		$this->fetchHTML($var,'main',$cadre_refreshed,'gep_projet-formulaire');
	}	
	*/
	
	
	/**
    * Convertion des Attributs enfants en PA
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    * @param array $infos $id_elmt
    * @param array &$s Session
    * @return array Pour JSON
    */   
	public function childs2PA($infos,&$s,$files=NULL,&$cadre_refreshed) {
		if (!$infos['id_attr']) {
			throw new errorATF(ATF::$usr->trans($this->table,'module')." - ".ATF::$usr->trans("Pas_d_id_attr_transmis",$this->table));
		}
		if (!$infos['id_pa']) {
			throw new errorATF("Fonction ".ATF::$usr->trans($this->table,'module')."::Duplicate() - ".ATF::$usr->trans("Pas_d_id_pa_transmis",$this->table));
		}
		ATF::db($this->db)->autocommit(false);
		$attr_old = ATF::attr()->select($infos['id_attr']);
		
		//On récupère l'id projet grace au pa
		$id_gp = $this->select($infos['id_pa'],"id_gep_projet");
		//Selection des attribut de l'ancien attr pour les associer aussi au clone
		foreach (ATF::attr()->selectChilds($infos['id_attr']) as $k=>$i) {
			$insertPA = array(
				"id_parent"=>$infos['id_pa']
				,"id_attr"=>$i['id_attr']
				,"offset"=>$i['offset']
				,"multi"=>$i['multi']
				,"id_gep_projet"=>$id_gp
			);

			$this->insert($insertPA,$s,$files,$cadre_refreshed);
		}
	
		ATF::db($this->db)->autocommit(true);
		
		//Retour
		$var = array(
			"current_class"=>$this
			,"infos"=>$this->select($infos['id_pa'])
			,"id_gp"=>$id_gp
		);
		$return = $this->fetchHTML($var,'main',$cadre_refreshed,'gep_projet-formulaire');
		
		return $return;
	}

	/**
	* Prédicat PA
	*/
	public function isPA($id_attr,$id_gp,$id_parent=false) {
		if (!$id_attr || !$id_gp) {
			return false;
		}
		$this->q->reset()
					->addcondition("id_attr",$id_attr)
					->addCondition("id_gep_projet",$id_gp)
					->setLimit(1)
					->setDimension('row');
		if ($id_parent) {
			$this->q->addcondition("id_parent",$id_parent);
		}
		return $this->select_all();
	}

	/**
    * Infobulle de gestion des coûts
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos 
	* @param array $s : contenu de la session
	* @param array $files
	* @param array $cadre_refreshed
    * @return boolean true
    */   
	public function cout(&$infos,&$s,$files=NULL,&$cadre_refreshed=NULL){
		if ($infos["delete"] && $infos["id"]) {
			ATF::cout_unitaire()->delete($infos["id"]);
			unset($infos["delete"]);
		}
		if ($infos["id_pa"] && $infos["id_cout_catalogue"]) {
			if ($data = ATF::cout_unitaire()->selectFromPA($infos["id_cout_catalogue"],$infos["id_pa"])) {
				$infos = $data;
			}
			ATF::$html->assign("infos",$infos);
		}
		$cadre_refreshed["__edit_cout_unitaire"] = ATF::$html->fetch("gep_projet-formulaire_attr_cout.tpl.htm");
		return true;
	}	

	/**
    * Retourne le nombre de couts existant sur ce PA
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $id_pa 
    * @return int
    */
	public function hasCout($id_pa) {
		if (!$id_pa) {
			return false;
		}
		ATF::cout_unitaire()->q->reset()
			->addCondition("id_pa",$id_pa)
			->setCountOnly();
		return ATF::cout_unitaire()->select_all();
	}
	
	/**
    * Mise a jour du champ libelle, et retour du fetch du template gep_projet-formulaire_attr_label.tpl.htm
    * @author QJ <qjanon@absystech.fr>
    * @param array $infos correspond au informations posté par l'AJAX
    * @return bool TRUE si ok, sinon FALSE
    */   
	public function updateLibelle($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL,$nolog=false) {
		if (!$infos) return false;
		$d["pa"]=$infos["libelle"];
		$d["id_pa"]=$infos["id_pa"];
		parent::update($d,$s);
		$var = array(
			"current_class"=>$this
			,"infos"=>$this->select($infos['id_pa'])
			,"projet"=>ATF::gep_projet()->select($this->select($infos['id_pa'],'id_gep_projet'))
		);
		ATF::$msg->addNotice("Libellé modifié pour le PA".$infos["id_pa"].".");
		ATF::$cr->add('pa_libelle_'.$infos['id_pa'],"gep_projet-formulaire_attr_label",$var);
		ATF::$cr->rm("top,main");
		return true;
	}

	/**
    * Mise a jour du champ libelle, et retour du fetch du template gep_projet-formulaire_attr_label.tpl.htm
    * @author QJ <qjanon@absystech.fr>
    * @param array $infos correspond au informations posté par l'AJAX
    * @return bool TRUE si ok, sinon FALSE
    */   
	public function updateOffset($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL,$nolog=false) {
		if (!$infos) return false;
		$d["offset"]=$infos["offset"];
		$d["id_pa"]=$infos["id_pa"];
		parent::update($d,$s);
		$var = array(
			"current_class"=>$this
			,"data"=>array($this->select($infos['id_pa']))
		);
		ATF::$msg->addNotice("Offset modifié pour le PA".$infos["id_pa"].".");
		ATF::$cr->add('pa_'.$infos['id_pa'],"gep_projet-formulaire_attr",$var);
		ATF::$cr->rm("top,main");
		return true;
	}

	/**
	* Duplication récursive
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    * @param array $infos correspond au informations posté par l'AJAX
    * @return bool TRUE si ok, sinon FALSE
	*/	
	public function cloner($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL,$nolog=false) {
		if (is_array($infos)) { // Duplication avec ces infos
			if (count($infos)>1) {
				$this->infoCollapse($infos);
			} elseif (isset($infos["id_".$this->table])) {
				$infos = $infos["id_".$this->table];
			}
		}
		if(is_numeric($infos)) { // Clé, on veut dupliquer sans modification
			$infos = $this->select($infos);
		}
		$id_pa_source = $infos["id_pa"]; // Sauvegarde de la clé primaire de provenance
		
		// Début de la transaction
		ATF::db($this->db)->begin_transaction();
		
		 // Insertion d'une copie de ce PA
		$tmp=false;//Pas de refresh par le insert
		$id_pa = $this->insert($infos,$s,$files,$tmp,$nolog);
		
		// Duplication des enfants du PA dupliqué
		$this->q->reset()->addCondition("id_parent",$infos["id_pa"])->addOrder("id_pa","asc")->setStrict();
		if ($childs =$this->select_all()) {
			foreach ($childs as $pa) {
				$pa["id_parent"]=$id_pa;
				$pa["id_gep_projet"]=$infos["id_gep_projet"]; // On s'assure que le projet des enfants sera le même que le parent créé. Ainsi, si lors de la copie on change le projet, les enfants seront correctement associés.
				$this->cloner($pa);
			}
		}
		
		// Commit
		ATF::db($this->db)->commit_transaction();
		
		//Redirection
		ATF::$cr->add("main","gep_projet-formulaire",array("id_gp"=>$infos["id_gep_projet"]));
	}

	/**
    * Retourne la règle de cout imposéen sur cet PA, sinon de l'attribut
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $id_pa 
    * @return string
    */
	public function getRegle($id_pa) {
		$id_attr = $this->select($id_pa,"id_attr");
		return ATF::attr()->getRegle($id_attr);
	}
	
	/**
    * Accessibilité : retourne tous les PA des A3615 d'un projet (Elément d'accessibilité)
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
    */ 
	public function selectAccessibiliteElementsReferences($id_gep_projet){
		$this->q->reset()
			->addCondition("id_gep_projet",$id_gep_projet)
			->addCondition("id_attr",3754)
			->addOrder("offset","asc");
		return parent::select_all();	
	}
	
	/**
    * Renvoi les options attribué au PA ou s'il n'y en a pas de l'ATTR
	* @author QJ <qjanon@absystech.fr>
    * @param array $infos correspond au informations de l'attribut
    * @return string/bool Le nom de la fonction sinon FALSE
    */   
	public function getOption($infos) {
		$return = $this->select($infos['id_pa'],"`option`");
		if (!$return) {
			$return = ATF::attr()->getOption($infos);
		}
		return $return?$return:false;
	}
	
	/**
    * Renvoi la fonction attribué au PA ou s'il n'y en a pas de l'ATTR
	* @author QJ <qjanon@absystech.fr>
    * @param array $infos correspond au informations de l'attribut
    * @return string/bool Le nom de la fonction sinon FALSE
    */   
	public function getFonction($infos,$log=false) {
		$option = $this->getOption($infos);
		if (preg_match("/function/",$option)){
			return str_replace("'","",substr($option,10));
		}
		return false;
	}

	/**
    * Renvoi la fonction attribué au PA ou s'il n'y en a pas de l'ATTR
	* @author QJ <qjanon@absystech.fr>
    * @param array $infos correspond au informations de l'attribut
    * @return string/bool Le nom de la fonction sinon FALSE
    */   
	public function getClass($infos) {
		$option = $this->getOption($infos);
		if (preg_match("/class/",$option)){
			return str_replace("'","",substr($option,6));
		}
		return false;
	}

	/**
    * Renvoi la largeur attribué au PA ou s'il n'y en a pas de l'ATTR
	* @author QJ <qjanon@absystech.fr>
    * @param array $infos correspond au informations de l'attribut
    * @return string/bool Le nom de la fonction sinon FALSE
    */   
	public function getWidth($infos) {
		$option = $this->getOption($infos);
		if (preg_match("/width/",$option)){
			return explode(",",str_replace("'","",substr($option,6)));
		}
		return false;
	}

	/**
    * Renvoi le style attribué au PA ou s'il n'y en a pas de l'ATTR
	* @author QJ <qjanon@absystech.fr>
    * @param array $infos correspond au informations de l'attribut
    * @return string/bool Le nom de la fonction sinon FALSE
    */   
	public function getStyle($infos) {
		if ($infos['id_pa']) {
			return ATF::style()->select($this->select($infos['id_pa'],"id_style"));
		} else {
			return ATF::attr()->getStyle($infos);
		}
	}

	/**
    * Renvoi le libellé attribué au PA ou s'il n'y en a pas de l'ATTR
	* @author QJ <qjanon@absystech.fr>
    * @param array $infos correspond au informations de l'attribut
    * @return string/bool Le nom de la fonction sinon FALSE
    */   
	public function getLibelle($infos) {
		if (!is_array($infos)) {
			$infos =array('id_pa'=>$infos);
		}
		if ($infos['id_pa']) {
			$return = $this->select($infos['id_pa'],"pa");
		}
		if (!$return) {
			if (!$infos['id_attr']) $infos['id_attr'] = $this->select($infos['id_pa'],'id_attr');
			$return = ATF::attr()->getLibelle($infos);
		}
		return $return;
	}

	/**
    * Mise a jour du champ libelle, et retour du fetch du template gep_projet-formulaire_attr_label.tpl.htm
    * @author QJ <qjanon@absystech.fr>
    * @param array $infos correspond au informations posté par l'AJAX
    * @return bool TRUE si ok, sinon FALSE
    */   
	public function updateRef($infos) {
		if (!$infos) return false;
		$update = array("id_pa"=>$infos['id_pa'],"reference"=>($infos['ref']=="true"?1:0));
		$this->update($update);

		return true;
	}


	/**
    * Récupère l'id pa de l'élément de référence pour un PA donné
    * @author QJ <qjanon@absystech.fr>
    * @param int $id_pa parent
    * @return int $id_pa de reference
    */   
	public function getReferenceElement($id) {
		if (!$id) return false;
		$id_parent = $this->select($id,"id_parent");
		if (!$id_parent) return false;
		$this->q->reset()
					->addCondition("id_pa",$id_parent)
					->setDimension("row");
		$parent = $this->select_all();
		if ($parent['reference']) {
			return $parent;
		} else {
			return self::getReferenceElement($parent['id_pa']);
		}
	}

	/**
    * Renvoi l'offset réél de l'attribut 'Urgence' d'un bloc désordre
    * @author QJ <qjanon@absystech.fr>
    * @param int $id_projet id projet
    * @return int index offset
    */   
	public function getIndexUrgence($id_projet) {
		if (!$id_projet) return false;
		switch ($id_projet) {
			case 34:
				$idDesordre = 3862;
				$idUrgence = 3864;
			break;
			default:
				$idDesordre = 3505;
				$idUrgence = 3507;
			break;
		}
		if (!$PAs = $this->isPA($idDesordre,$id_projet)) {
			return false;
		}

		foreach (ATF::attr()->selectChilds($idDesordre) as $k=>$i) {
			if ($i['id_attr']==$idUrgence) {
				return $k;
			}
		}
	}

	/**
    * Retourne la forme contractée demandée par Marion
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    * @return string $s
    */   
	public function acronyme($s) {
		$words = explode(" ",$s);
		if (count($words)>1) {
			array_walk($words,create_function('&$s', '$s = ucfirst(substr($s,0,1));'));
			return implode($separator,$words);
		} else {
			return utf8_encode(substr(utf8_decode($s),0,2));
		}
	}
	
	/**
    * Retourne le nom de l'élément de synthèse par rapport à un libellé de PA de référence et une clé d'attribut
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    * @param int $id_attr
    * @param string $pa
    * @return string $element
    */   
	public function syntheseElement($id_attr,$pa) {
		$this->q->reset()
			->addField("synthese_element")
			->where("id_attr",$id_attr)
			->where("pa",$pa)
			->where("id_gep_projet",55) //Projet generique
			->whereIsNotNull("synthese_element")
			->setDimension("cell");
		return $this->select_all();
	}
	
	/**
    * Retourne le nom de l'élément de synthèse par rapport à une clé d'attribut
    * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
    * @param int $id_attr
    * @return string $element
    */
	public function syntheseElementBis($id_attr,$attr) {
		ATF::attr()->q->reset()
					->addField("synthese_element")
					->addJointure("attr","id_attr","pa","id_attr")
					->where("attr.id_attr",$id_attr)
					->whereIsNotNull("synthese_element")
					->setDimension("cell");
		return ATF::attr()->select_all();
	}
	
	/** Retourne le tableau structuré du carnet de synthèse
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function syntheseInitTableau(){
		/**************** CLOS COUVERT *****************/
		$mapping["Clos-couvert"]=array("Structures"=>array("donnees"=>array('pond_client'=>20,'pond_certu'=>23)
															,"detail"=>array("Structure générale"=>"P","Planchers"=>"P","Poteaux, voiles, poutres"=>"P","Isolations des structures"=>"P"))
										,"Couvertures"=>array("donnees"=>array('pond_client'=>10,'pond_certu'=>12)
															,"detail"=>array("Charpentes, couvertures"=>"P","Zinguerie, fumisterie"=>"P","Toiture terrasse"=>"P"))
										,"Façades"=>array("donnees"=>array('pond_client'=>15,'pond_certu'=>15)
															,"detail"=>array("Façades"=>"P","Isolation Façades"=>"P"))
										,"Menuiseries extérieures"=>array("donnees"=>array('pond_client'=>10,'pond_certu'=>10)
															,"detail"=>array("Menuiseries extérieures"=>"P","Portes - fenêtre et Chassis"=>"P","Occultation extérieure"=>"P ou L")));
		
		/******** Synthèse équipements techniques ********/
		$mapping["Équipements techniques"]=array("Chauffage, ventillation, climatisation"=>array("donnees"=>array('pond_client'=>8,'pond_certu'=>8)
																								,"detail"=>array("Chauffage"=>"P","Ventilation"=>"P","Climatisation"=>"P","Équipement autonome"=>"L"))
												,"Plomberie, sanitaire"=>array("donnees"=>array('pond_client'=>2,'pond_certu'=>2)
																				,"detail"=>array("Appareils sanitaires"=>"P","Alimentation en eau"=>"P","Production Eau Chaude Sanitaire"=>"P"))
												,"Appareils élévateurs"=>array("donnees"=>array('pond_client'=>2,'pond_certu'=>2)
																				,"detail"=>array("Ascenseurs"=>"P","Escaliers mécaniques"=>"P","Portes automatique"=>"P"))
												,"Courant fort"=>array("donnees"=>array('pond_client'=>4,'pond_certu'=>4)
																		,"detail"=>array("Tableaux, distributions"=>"P","Prises, appareillages"=>"L","Groupe électrogène"=>"L"))
												,"Courant faible"=>array("donnees"=>array('pond_client'=>2,'pond_certu'=>2)
																		,"detail"=>array("Onduleur"=>"L","Autocommutateur"=>"L"))
												,"Équipements de sécurité"=>array("donnees"=>array('pond_client'=>3,'pond_certu'=>2)
																				,"detail"=>array("Installation de protection contre l'incendie"=>"L","Surveillance, protection du site"=>"L")));
		
		/******** Synthèse aménagements extérieurs ********/
		$mapping["Aménagements extérieurs"]=array("Voirie"=>array("donnees"=>array('pond_client'=>2,'pond_certu'=>0)
																	,"detail"=>array("Voies d'accès"=>"P","Éclairage extérieur"=>"P","Espaces verts"=>"P","Parkings"=>"P"))
												,"Réseaux divers"=>array("donnees"=>array('pond_client'=>1,'pond_certu'=>0)
																	,"detail"=>array("Réseau extérieur"=>"P","Branchements EDF, Télécom"=>"P","Branchements Gaz"=>""))
												,"Serrurerie"=>array("donnees"=>array('pond_client'=>1,'pond_certu'=>0)
																	,"detail"=>array("Clôtures"=>"P","Portails"=>"P","Autres (barrières, plots...)"=>"P"))
												,"Autres"=>array("donnees"=>array('pond_client'=>2,'pond_certu'=>0)
																	,"detail"=>array("Gardiennage"=>"L","Réservoirs ou cuves"=>"L")));
															
		/******** Second oeuvre et autres **********/
		$mapping["Second oeuvre et autres"]=array("Menuiseries intérieures"=>array("donnees"=>array('pond_client'=>6,'pond_certu'=>5)
																	,"detail"=>array("Menuiseries intérieures"=>"L","Escaliers"=>"L","Occultation intérieure"=>"L"))
												,"Équipement intérieur"=>array("donnees"=>array('pond_client'=>6,'pond_certu'=>7)
																	,"detail"=>array("Vestiaire"=>"L","Éclairage intérieur"=>"L","Cuisine"=>"L"))
												,"Revêtement"=>array("donnees"=>array('pond_client'=>6,'pond_certu'=>8)
																	,"detail"=>array("Revêtement des plafonds"=>"L","Revêtement des sols"=>"L","Revêtement des murs"=>"L"))
												);
	
		/******** Second oeuvre et autres **********/
		$mapping["Données sanitaires et réglementaires"]=array(
			"Données sanitaires et réglementaires"=>array(
				"donnees"=>array(
					'pond_client'=>""
					,'pond_certu'=>0
				)
				,"detail"=>array(
					"Règlementations locales"=>"P"
					,"Données sanitaires et réglementaires"=>"L"
					,"Données manquantes"=>"L"
				)
			)
		);
	
		return $mapping;
	}
		
	/** Retourne le tableau structuré d'export actions préconisées
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function syntheseInitTableauSap(){
	
		$mapping["Travaux sur structures/clos/couvert"]=array("Structures"=>array("Structure générale"=>"P","Planchers"=>"P","Poteaux, voiles, poutres"=>"P")
															,"Charpentes/ Couvertures / toitures"=>array("Isolations des structures"=>"P","Charpentes, couvertures"=>"P","Zinguerie, fumisterie"=>"P","Toiture terrasse"=>"P","Isolation Toiture"=>"P")
															,"Façades"=>array("Façades"=>"P","Isolation Façades"=>"P")
															,"Menuiseries extérieures"=>array("Menuiseries extérieures"=>"P","Portes - fenêtre et Chassis"=>"P","Occultation extérieure"=>"P ou L"));

		$mapping["Travaux Chauffage/ventilation/climatisation/Plomberie"]=array("CVC"=>array("Chauffage"=>"P","Ventilation"=>"P","Climatisation"=>"P","Équipement autonome"=>"L")
																			,"Plomberie sanitaire"=>array("Appareils sanitaires"=>"P","Alimentation EF-EC"=>"P","Production ECS"=>"P")
																			,"Mise aux normes accessibilité sanitaires"=>array("Sanitaires"=>"P"));
																			
		$mapping["Travaux ascenseurs"]=array(""=>array("Ascenseurs"=>"P","Escaliers mécaniques"=>"P","Portes automatique"=>"P"));
		
		$mapping["Travaux électricité courant fort"]=array(""=>array("Tableaux, distributions"=>"P","Prises, appareillages"=>"L","Groupe électrogène"=>"L","Onduleur"=>"L","Éclairage intérieur"=>"L"));																	
												
		$mapping["Travaux électricité courant faible"]=array(""=>array("Autocommutateur"=>"L"));										

		$mapping["Travaux liés à la sécurité incendie"]=array(""=>array("Installation de protection contre l'incendie"=>"L"));						
												
		$mapping["Travaux VRD et aménagements extérieurs"]=array(""=>array("Voies d'accès"=>"P","Éclairage extérieur"=>"P","Espaces verts"=>"P","Parkings"=>"P","Parking"=>"P","Circulations extérieures"=>"P"
																			,"Réseaux d'eau (EP, EU, EV)"=>"P","Branchements EDF, Télécom, Eau"=>"P","Branchements Gaz"=>""
																			,"Clôtures"=>"P","Portails"=>"P","Autres"=>"P","Autres (barrières, plots...)"=>"P"
																			,"Gardiennage"=>"L","Réservoirs ou cuves"=>"L"));
								
		$mapping["Installation ENR"]=array(""=>1);
		
		$mapping["Optimisation des contrats et abonnements"]=array(""=>1);
		
		$mapping["Actions sur l'usage"]=array(""=>array("Surveillance, protection du site"=>"L","Vestiaire"=>"L","Cuisine"=>"L","Données manquantes"=>"L"));			
															
		/* On ne les prends plus en compte du fait du nouveau mapping basé sur le fichier excel
		$mapping["Menuiseries intérieures"]=array(""=>array("Menuiseries intérieures"=>"L","Escaliers"=>"L","Occultation intérieure"=>"L","Cloisonnement"=>"L"));*/
		
		$lignes["noir"]=$mapping;
		
		
		$mapping_bleu["Accessibilité espaces dédiés"]=array(""=>array("Espaces dédiés"=>"P","Sécurité"=>"P","Circulations horizontales"=>"P","Circulations verticales"=>"P","Commandes/Signalétique"=>"P"));
		
		$lignes["bleu"]=$mapping_bleu;
		
		$mapping_violet["Second oeuvre"]=array("Menuiseries intérieures"=>array("Portes automatique intérieures"=>"L","Portes"=>"L")
										,"Revêtements"=>array("Revêtement des plafonds"=>"L","Revêtement des sols"=>"L","Revêtement des murs"=>"L"));	
		
		$lignes["violet"]=$mapping_violet;								
		
		$mapping_orange["Autres"]=array("Données sanitaires et réglementaires - diagnostics complémentaires"=>array("Données sanitaires et réglementaires"=>"L")
								,"Réalisation d'un équipement recommandé par un PPR"=>array("Règlementations locales"=>"P"));
		
		$lignes["orange"]=$mapping_orange;							
	
		return $lignes;
	}
	
	/**
    * Renvoi les frères de l'attribut
	* @author QJ <qjanon@absystech.fr>
    */ 
	public function getBro($id_parent,$id_attr=false,$row=false) {
		$this->q->reset()->Where("id_parent",$id_parent);
		if ($id_attr) {
			$this->q->Where("id_attr",$id_attr);
		}
		if ($row) {
			$this->q->setDimension('row');
		}
		return $this->sa();
	}
	
	/**
	* Prédicat PA
	*/
	public function getPAByLib($lib,$id_gp) {
		if (!$lib || !$id_gp) {
			return false;
		}
		$this->q->reset()
					->addCondition("pa",addslashes($lib))
					->addCondition("id_gep_projet",$id_gp)
					->setLimit(1)
					->setDimension('row');
		return $this->sa();
	}
	
	public function getEtatGeneral($pa,$id_visite,$eg=3933) {
		$c1 = $this->selectChilds($pa['id_pa']);
		foreach ($c1 as $k_=>$i_) {
			if ($i_['id_attr']!=3545) continue;
			$childs = $this->selectChilds($i_['id_pa']);
			if (!$childs) $childs = ATF::attr()->selectChilds($i_['id_attr']);
			foreach ($childs as $k=>$i) {
				if ($i['id_attr']!=$eg) continue;
				//On met dans id_pa le ppa sinon la réponse ne se trouve pas. ATTENTION ca peut foutre la merde.
				if (!$i['id_pa']) $i['id_pa'] = $i_['id_pa']?$i_['id_pa']:$pa['id_pa'];
				$i['id_vi_pa_multi'] = $pa['id_vi_pa_multi'];
				$R = ATF::vi_pa()->getEnumReponse($i,$id_visite);
				$Rlib = ATF::pa()->getLibelle($R);
				return $Rlib;
			}
		}
	}
	
	public function getDesordreFromPA($pa,$id_visite) {
	    $this->q->reset();
		switch ($id_gep_projet) {
			case 26: $id_desordre = 3831; break; // 09.141 - DIR SO
			case 34: $id_desordre = 3862; break; // 09.114 - SNCF Région Est
			default: $id_desordre = 3505; break;
		}
		$c1 = $this->selectChilds($pa['id_pa']);
		foreach ($c1 as $k_=>$i_) {
			if ($i_['id_attr']!=3545) continue;
			$childs = $this->selectChilds($i_['id_pa']);
			if (!$childs) $childs = ATF::attr()->selectChilds($i_['id_attr']);
			foreach ($childs as $k=>$i) {
				if ($i['id_attr']!=$id_desordre) continue;
				//On met dans id_pa le ppa sinon la réponse ne se trouve pas. ATTENTION ca peut foutre la merde.
				if (!$i['id_pa']) $i['id_pa'] = $i_['id_pa']?$i_['id_pa']:$pa['id_pa'];
				$i['id_vi_pa_multi'] = $pa['id_vi_pa_multi'];
				if ($i['multi']) {
					$R = ATF::vi_pa()->getDistinct($id_visite,$i['id_attr'],$i['id_pa'],$i['id_vi_pa_multi']);
				} else {
					$des = ATF::vi_pa()->isAnswered($id_visite,$i['id_attr'],$i['id_pa'],$i['id_pa'],$i['id_vi_pa_multi']);
					if ($des['reponse']) $R[0]=$des;
				}
				return $R;
			}
		}
	}
	
	public function optionCS($id) {
		$this->q->reset()->Where('id_gep_projet',$id);
		$return[] = "-";
		foreach ($this->sa() as $k=>$i) {
			unset($flag);
			$c = $this->selectChilds($i['id_pa']);
			foreach ($c as $cs) {
				if ($cs['id_attr']==3545) $flag = true;
			}
			if (!$flag) {
				continue;
			}
			$return[$i['id_pa']] = $this->getLibelle($i);
		}
		return $return;
	}
	
	public function batForParent($id_pa,$idBat=48) {
		$parent = $this->select($id_pa);
		if ($parent['id_attr']==$idBat) {
			return true;
		} elseif ($parent['id_parent']) {
			return self::batForParent($parent['id_parent'],$idBat);
		}
		return false;
	}
		
};
?>