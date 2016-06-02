<?
/**
* Classe ATTR => correspond aux attributs du catalogue
*
*
* @date 2009-10-31
* @package inventaire
* @version 1.0.0
* @author QJ <qjanon@absystech.fr>
* @author Yann GAUTHERON <ygautheron@absystech.fr>
*
*/ 
class attr extends attributs {
	function __construct() { // PHP5
		parent::__construct();
		$this->table = __CLASS__;
		
		//Colonnes SELECT ALL
		$this->colonnes['fields_column'] = array(	
			"id_attr"=>array("custom"=>true,"width"=>50,"fixedWidth"=>true) // Obligé de faire un alias différent de attr.id_attr parce que sinon problème des id_attr trouvé sur "la loupe" des listings
			,'attr.attr'
			,'attr.id_parent'
			,'attr.offset'
		);
		
		//Colonnes SELECT
		$this->colonnes['primary'] = array(
			"attr"
			,"id_parent"
			,"type"
		);

		$this->colonnes['quick_insert'] = array(
			"attr"
			,"type"
			,"offset"
			,"notes"
			,"maxlength"
			,"option"
			,"id_style"
		);

		$this->fieldstructure();
		
		$this->foreign_key["id_parent"] = "attr";
		/* Définition statique des clés étrangère de la table */
		$this->onglets = array('attr'=>array('field'=>'attr.id_parent'),'pa');				

		$this->addPrivilege("updateAC","update");
		$this->addPrivilege("autocompleteOLD");
		$this->addPrivilege("updateStyle","update");
		$this->addPrivilege("updateMulti","update");
		
		//Liste des attributs qui ne peuvent pas être modifié par un utilisateur
		$this->NotAbleToUpdate = array(
			//Désordres DIR SO et ses enfants
			3831,3832,3833,3834,3835,3836,3837,3838,3839,3840,3841,3831,3842,3843,3844,3845,3846,3847,3848,3849,3850,3851,3852
		);
		
		
	}	
	
	/**
	* Méthode spéciale par défaut "saCustom"
	* Appel la méthode de classe particulière à utiliser,  si $method =flase on utilise select_all
	* Utilisation dans generic_select_all
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $s : la session
	* @param string $method : methode de classe particulière à utiliser
	* @return array Résultat de la requête
	*/
	public function select_data(&$s,$method=false){
		if (!$method) {
			$method="saCustom";
		}
		return parent::select_data($s,$method);
	}

	public function saCustom() {
		$this->q->addField("ROUND(attr.id_attr)","id_attr");

		return $this->select_all();

	}
	
	public function insert($infos,$s,$files,&$cadre_refreshed) {
		$return = parent::insert($infos,$s,$files);
		if (!$infos['redirectToProjet']) {
//			ATF::gep_projet()->redirection("formulaire",ATF::gep_projet()->cryptId($infos['redirectToProjet']));
//		} else {
			$this->redirection("select",$return);
		}
		return $return;

	}

	/* Retourne le résultat d'une recherche pour un affichage en mode autocomplétion (Seuil a 10)
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 2009-02-22
	* @param array $infos ($_POST habituellement attendu)
	*	string $infos[recherche] contenu de la recherche
	* @return string HTML de retour
	*/
	function autocompleteOLD(&$infos,$s,$files,$cadre_refreshed) {
		if (strlen($infos["recherche"])<1 || !isset($infos['recherche'])) {
			return false;
		}
		$infos["display"]=true;// Le résultat appelé en AJAX sera affiché directement, ca ne sera pas retourné en JSON
		$this->q->reset()->setPage(0)->setLimit(10);

		$this->q->setSearch(stripslashes(urldecode($infos["recherche"])));
		$this->q->addField("CONVERT(CONCAT_WS(', ',CONCAT('A',`attr`.`id_attr`),`attr`.`attr`,`attr`.`notes`,`attr`.`type`) USING utf8)","attr");
		$this->q->addField("LENGTH(`attr`)","attrL");
		$this->q->addOrder("attrL");
		$var = array(
			"current_class"=>$this
			,"data"=>$this->select_all()
			,"new"=>true
		);
		return $this->fetchHTML($var,false,$cadre_refreshed,"autocomplete");
	}
	
	/**
    * Retourne vrai si le ATTR a des enfants ATTR
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    * @param array $array Tableau de ATTR
    * @return bool TRUE s'il y a des enfants, sinon FALSE
    */   
	function hasChild($id_attr) {
		$this->q->reset()
			->setCountOnly()		
			->addCondition("id_parent",$id_attr);
		return $this->select_all()>0;
	}

	/**
    * Mise a jour du champ multi, et retour du fetch du template gep_projet-formulaire_attr_multi.tpl.htm
    * @author QJ <qjanon@absystech.fr>
    * @param array $infos correspond au informations posté par l'AJAX
    * @return bool TRUE si ok, sinon FALSE
    */   
	function updateMulti($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL,$nolog=false) {
		if (!$infos) return false;
		$d["multi"]=$infos["multi"];
		$d["id_attr"]=$infos["id_attr"];
		parent::update($d,$s);
		$var = array(
			"current_class"=>$this
			,"infos"=>ATF::attr()->select($infos['id_attr'])
		);
		ATF::$msg->addNotice("Mode multiple modifié pour l'attribut A".$infos["id_attr"].".");
		$this->fetchHTML($var,'multi_'.$infos['id_attr'].'_'.$infos['ppa'],$cadre_refreshed,'gep_projet-formulaire_attr_multi');
		return true;
	}
	
	/**
    * Renvoi les pa enfants d'un autre attr ordonnée par offset
    * @author QJ <qjanon@absystech.fr>
    * @param int $id_attr L'id du attr
    * @param string $orderBy Colonne de la table sur laquelle effectuer le tri.
    * @return array listing des attr
    */   
	function selectChilds($id_attr,$orderBy="offset",$count=false,$id=false) {
		if (!$id_attr) {
			return false;
		}
		$this->q->reset()
					->addCondition("id_parent",$id_attr);
		if ($orderBy) {			
			$this->q->addOrder($orderBy);
		}
		if ($count) {			
			$this->q->setCountOnly();
		}
		if ($id) {			
			$this->q->where("id_attr",$id);
			$this->q->setDimension("row");
		}
		return $this->select_all();
	}

	/**
    * Renvoi le nombre de lien de parenté d'un attr
    * @author QJ <qjanon@absystech.fr>
    * @param int $id_attr l'ID ATTR
    * @param int $nb Le nombre de lien
    * @return int $nb le nombre de lien final
    */   
	function lvlParent($id_attr,$nb=1) {
		if (!$id_attr) {
			return false;
		}
		$id_parent = $this->select($id_attr,'id_parent');
		if ($parent = $this->select($id_parent)) {
			$nb++;
			$nb = self::lvlParent($parent['id_attr'],$nb);
		}
		return $nb;
	}

	/**
    * Renvoi le type d'attribut du parent
    * @author QJ <qjanon@absystech.fr>
    * @param int $id_parent l'ID PARENT
    * @return string Type du parent
    */   
	function typeParent($id_attr) {
		if (!$id_attr) {
			return false;
		}
		$id_parent = $this->select($id_attr,'id_parent');
		if (!$id_parent) {
			return false;
		}
		$parent = $this->select($id_parent);
		return ATF::attr()->select($parent['id_attr'],'type');
	}	
	
	/**
    * Fonction qui teste si une collection de ATTR a des enfants ou pas.
	* Utile dans le cas ou on doit vérifier qu'aucun enfants d'un attr n'a d'enfant lui même, ou inversement
    * @author QJ <qjanon@absystech.fr>
    * @param array $array Tableau de ATTR
    * @return bool TRUE s'il y a des enfants, sinon FALSE
    */   
	function getChild($array) {
		if (is_array($array)) {
			foreach ($array as $k=>$i) {
				if ($i['id_attr'] && $this->selectChilds($i['id_attr'],"offset",true)) {
					return true;
				}
			}
		}
		return false;
	}
	
	/**
    * Fonction qui renvoi tous les Attributs racines
	* Utile pour le catalogue complet
    * @author QJ <qjanon@absystech.fr>
    * @return array Collection d'attribut
    */   
	function selectAllRoot() {
		$this->q->reset()
					->addCondition("attr.id_parent",NULL,false,false,"IS NULL");
		return $this->select_all();
	}
	
	/**
    * Mise a jour du champ multi, et retour du fetch du template gep_projet-formulaire_attr_multi.tpl.htm
    * @author QJ <qjanon@absystech.fr>
    * @param array $infos correspond au informations posté par l'AJAX
    * @return bool TRUE si ok, sinon FALSE
    */   
	function updateAC($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL,$nolog=false) {
		if (!$infos) return false;
		$d["ac"]=$infos["ac"];
		$d["id_attr"]=$infos["id_attr"];
		parent::update($d,$s);
		$var = array(
			"current_class"=>$this
			,"infos"=>ATF::attr()->select($infos['id_attr'])
		);
		ATF::$msg->addNotice("Mode d'autocompletion ".($infos["ac"]=="on"?"activé":"desactivé")." pour l'attribut A".$infos["id_attr"].".");
		ATF::$cr->add('ac_'.$infos['id_attr'],"gep_projet-formulaire_attr_ac",$var);
		ATF::$cr->rm("top,main");
		return true;
		
	}
	
	/**
    * Mise a jour du champ multi, et retour du fetch du template gep_projet-formulaire_attr_multi.tpl.htm
    * @author QJ <qjanon@absystech.fr>
    * @param array $infos correspond au informations posté par l'AJAX
    * @return bool TRUE si ok, sinon FALSE
    */   
	function canUpdate($id_attr) {
		if (in_array($id_attr,$this->NotAbleToUpdate)) {
			return false;
		} 
		return true;
	}

	/**
    * Retourne la règle de cout
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $id_attr 
    * @return string
    */
	function getRegle($id_attr) {
		return $this->select($id_attr,"regle");
	}
	
	
	/**
    * Renvoi les options attribué au PA ou s'il n'y en a pas de l'ATTR
	* @author QJ <qjanon@absystech.fr>
    * @param array $infos correspond au informations de l'attribut
    * @return string/bool Le nom de la fonction sinon FALSE
    */   
	function getOption($infos) {
		return $this->select($infos['id_attr'],"`option`");
	}

	/**
    * Renvoi le style attribué au PA ou s'il n'y en a pas de l'ATTR
	* @author QJ <qjanon@absystech.fr>
    * @param array $infos correspond au informations de l'attribut
    * @return string/bool Le nom de la fonction sinon FALSE
    */   
	function getStyle($infos) {
		$return = ATF::style()->select($this->select($infos['id_attr'],"id_style"));
		if (!$return) {
			$return = ATF::style()->select(1);
		}
		return $return;
	}

	/**
    * Renvoi le libellé attribué au PA ou s'il n'y en a pas de l'ATTR
	* @author QJ <qjanon@absystech.fr>
    * @param array $infos correspond au informations de l'attribut
    * @return string/bool Le nom de la fonction sinon FALSE
    */   
	function getLibelle($infos) {
		return $this->select($infos['id_attr'],"attr");
	}


	/**
    * Mise a jour du style
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
    * @param array $infos correspond au informations posté par l'AJAX
    * @return bool TRUE si ok, sinon FALSE
    */   
	function updateStyle($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL,$nolog=false) {
		if (parent::update(array("id_".$this->table=>$infos["id_".$this->table],"id_style"=>$infos["id_style"]),$s)) {
			$var = array(
				"current_class"=>$this
				,"infos"=>$this->select($infos['id_'.$this->table])
			);
			
			$this->fetchHTML($var,'attrStyle_'.$infos['id_attr'].'_'.$infos['id_pa'],$cadre_refreshed,'gep_projet-formulaire_attr_attrStyle');
			return true;
		}
		return false;
	}
};
?>