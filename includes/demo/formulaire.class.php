<?
/**
* La classe formulaire contient toutes les fonctions qui permette de construire le formulaire
* Entre autre bipassement des insert d'element, d'attribut, de pe, pour faire les bonnes redirections
*
*
* @date 2009-10-31
* @package inventaire
* @version 1.0.0
* @author QJ <qjanon@absystech.fr>
*
*/ 
class formulaire extends classes_optima {

	/**
	* Liste des noeuds ouverts
	* @var array  
	*/
	private $nodeOpened = array();

	/**
	* Constructeur
	*/
	public function __construct() {
		parent::__construct();

		$this->controlled_by = "attr";
		$this->addPrivilege("delAttr","delete");
		$this->addPrivilege("addNewAttr","insert");
		$this->addPrivilege("addAttr","insert");
		$this->addPrivilege("flushOpened");
		$this->addPrivilege("removeOpened");
	}
	
	/**
    * Sauvegarde les noeuds ouverts
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    * @param string $id
    */   
	public function keepOpened($id) {
		$this->nodeOpened[$id] = true;
	}
	
	/**
    * Ferme un noeud
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    * @param string $id
    */   
	public function removeOpened($id) {
		if (is_array($id) && isset($id["id"])) {
			$id=$id["id"];
		}
		if (isset($this->nodeOpened[$id])) {
			unset($this->nodeOpened[$id]);
		}
	}
	
	/**
    * Sauvegarde les noeuds ouverts
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    * @param string $id
    */   
	public function isOpened($id) {
		return $this->nodeOpened[$id];
	}
	
	/**
    * Vide les noeuds ouverts
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    */   
	public function flushOpened() {
		$this->nodeOpened = array();
	}
	
	/**
    * Ajoute un attribut existant a un element d'un formulaire
    * @author QJ <qjanon@absystech.fr>
    * @return array $infos le POST
    * @return string résultat HTML de l'ajout a transmettre directement au template dans l'état
    */   
	public function addAttr($infos,&$s,$files=NULL,&$cadre_refreshed) {
		if (!$infos || !is_array($infos)) {
			throw new errorATF("Fonction ".ATF::$usr->trans($this->table,'module')."::addAttr() - ".ATF::$usr->trans("aucunes_donnees_transmise"));
		}
		
		ATF::db($this->db)->autocommit(false);
		// Verifions que l'attribut a modifier ne comporte pas de PA, si c'est le cas on le duplique afin de pouvoir le modifier sans effet papillon sur les autres formulaires.
		if ($infos['id_parent'] && !ATF::pa()->ss('id_parent',$infos['id_parent'])) {
			// On récupère les infos de l'attrbut parent
			$attr_parent = ATF::attr()->select(ATF::pa()->select($infos['id_parent'],'id_attr'));
			
			// On récupère ses enfants dans le catalogue et on les insère dans PA pour les rendre propre au projet et ainsi pouvoir les modifier sans effet papillon
			foreach (ATF::attr()->selectChilds($attr_parent['id_attr']) as $k=>$i) {
				$insert_pa = array(
					"id_gep_projet"=>$infos['id_gep_projet']
					,"id_attr"=>$i['id_attr']
					,"id_parent"=>$infos['id_parent']
					,"offset"=>$i['offset']
					,"multi"=>$i['multi']
					,"id_style"=>$i['id_style']
				);
				$id_pa = ATF::pa()->insert($insert_pa);
			}
		}
		
		// ensuite / ou s''il y a déjà des enfant sous forme de PA
		$attr = ATF::attr()->select($infos['id_attr']);
		$id_pa = ATF::pa()->insert(array(
			"id_gep_projet"=>$infos['id_gep_projet']
			,"id_attr"=>$infos['id_attr']
			,"id_parent"=>$infos['id_parent']
			,"offset"=>$attr['offset']
			,"multi"=>$attr['multi']
			,"id_style"=>$attr['id_style']
		));
		
		// On initialise le retour
		ATF::db($this->db)->autocommit(true);
		
		if (!$infos['id_parent']) {
			$var = array(
				"current_class"=>$this
				,"id_gp"=>$infos['id_gep_projet']
			);
			ATF::$cr->add("formulaire","formulaire",$var);
		} else {
			$var = array(
				"current_class"=>$this
				,"infos"=>ATF::pa()->select($infos['id_parent'])
				,"projet"=>ATF::gep_projet()->select($infos['id_gep_projet'])
			);
			ATF::$cr->add('pa_'.$infos['id_parent'],"gep_projet-formulaire_attr",$var);
		}
		ATF::$cr->rm("top,main");
	}	

	/**
    * Ajoute un nouvel attribut a un élément du formulaire
    * @author QJ <qjanon@absystech.fr>
    * @return array $infos le POST
    * @return array ModalBox avec formulaire pour saisir les infos
	* @return bool TRUE si l'insertion est aware, sinon FALSE
    */   
	public function addNewAttr($infos,&$s,$files=NULL,&$cadre_refreshed) {
		if ($infos['display_form']) {
			$params = array(
				"id_gep_projet"=>$infos['id_gep_projet']
				,'id_attr_parent' => $infos['id_attr_parent']
				,'id_parent' => $infos['id_parent']
				,'id_attr_parent' => $infos['id_attr_parent']
			);
			return array("modalbox"=>util::mbox("attr-quick_insert",ATF::$usr->trans("quick_insert",'formulaire'),array("params"=>$params, "method"=>"post")));
		} else {
			if (!$infos['id_gep_projet'] || !$infos['id_attr']) {
				throw new errorATF("Fonction ".ATF::$usr->trans($this->table,'module')."::addNewAttr() - ".ATF::$usr->trans("aucunes_donnees_transmise"));
			}
			ATF::db($this->db)->autocommit(false);
			// Verifions que l'attribut a modifier ne comporte pas de PA, si c'est le cas on le duplique afin de pouvoir le modifier sans effet papillon sur les autres formulaires.
			// Ou si l'attribut qu'on ajoute  n'a pas de parent
			if ($infos['id_parent'] && !ATF::pa()->ss('id_parent',$infos['id_parent'])) {
				// On récupère les infos de l'attrbut parent
				$attr_parent = ATF::attr()->select(ATF::pa()->select($infos['id_parent'],'id_attr'));
				// On récupère ses enfants dans le catalogue et on les insère dans PA pour les rendre propre au projet et ainsi pouvoir les modifier sans effet papillon
				foreach (ATF::attr()->selectChilds($attr_parent['id_attr']) as $k=>$i) {
					$insert_pa = array(
						"id_gep_projet"=>$infos['id_gep_projet']
						,"id_attr"=>$i['id_attr']
						,"id_parent"=>$infos['id_parent']
						,"offset"=>$i['offset']
						,"multi"=>$i['multi']
						,"id_style"=>$i['id_style']
					);
					$id_pa = ATF::pa()->insert($insert_pa);
				}
			} else {
				$attr = ATF::attr()->select($infos['id_attr']);
				// ensuite / ou s''il y a déjà des enfant sous forme de PA
				$id_pa = ATF::pa()->insert(array(
					"id_gep_projet"=>$infos['id_gep_projet']
					,"id_attr"=>$infos['id_attr']
					,"id_parent"=>$infos['id_parent']
					,"offset"=>$attr['offset']
					,"multi"=>$attr['multi']
					,"id_style"=>$attr['id_style']
				));
			}

			ATF::db($this->db)->autocommit(true);
			if (!$infos['id_parent']) {
				$var = array(
					"current_class"=>$this
					,"id_gp"=>$infos['id_gep_projet']
				);
				$this->fetchHTML($var,'formulaire',$cadre_refreshed,'gep_projet-formulaire_data');
			} else {
				$var = array(
					"current_class"=>$this
					,"infos"=>ATF::pa()->select($infos['id_parent'])
					,"projet"=>ATF::gep_projet()->select($infos['id_gep_projet'])
				);
				
				$this->fetchHTML($var,'pa_'.$infos['id_parent'],$cadre_refreshed,'gep_projet-formulaire_attr');
			}

			return true;
		}
	}
	
	/**
    * Supprime un attribut du formulaire
    * @author QJ <qjanon@absystech.fr>
    * @return array $infos le POST
	* @return bool TRUE si tout s'est bien passé, sinon FALSE
    */   
	public function delAttr($infos,&$s,$files=NULL,&$cadre_refreshed) {
		// Suppression du pe et de tous ses enfants
		if (!$infos) {
			throw new errorATF("Fonction ".ATF::$usr->trans($this->table,'module')."::delAttr() - ".ATF::$usr->trans("aucunes_donnees_transmise"));
		}
		// Suppression de la liaison
		ATF::pa()->delete($infos);
		return true;
		
	}	
	
};
?>