<?php
/**
* Le pagineur permet d'externaliser principalement la couche de pagination sous la forme
* d'un trousseau de requêteurs. Ainsi, le pagineur en variable de session est capable de
* garder une trace de chaque position de tous les templates, et de faciliter le
* codage des mises à jour dynamique en AJAX des div correspondants.
*
* @date 2008-10-30
* @package ATF
* @version 5
* @author Yann GAUTHERON <ygautheron@absystech.fr>
*/ 
class pager {
	/**
	* Trousseau de requêteur
	* @var array
	*/
	public $q = array(); 
	
	/**
    * Crée le querier du template s'il n'existe pas encore
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $template
	* @param int $limit Initialiser la pagination et définir cette limite
	* @param boolean $count Définir le flag du count
	* @param mixed $object Objet sur lequel attacher du même coup ce requêteur
	* @return querier
    */ 
	public function &create($template,$limit=NULL,$count=NULL,$object=NULL,$selectAllExtJs=false) {	
		if (!$template) {
			throw new errorATF('Pager::select_all called no template !',45);
		}
		
//log::logger("[".md5(ATF::$id_thread)."] "."querier::create = ".$template.($this->q[$template]? " | fk=".count($this->q[$template]->getFk())." where=".count($this->q[$template]->getWhere()) : NULL),ygautheron,true);
		if (!$this->q[$template]) {
//log::logger("[".md5(ATF::$id_thread)."] pager create '".$template."' not exists, creating now","ygautheron");
			$this->q[$template] = new querier();
			
			//dans le cas du select_all extjs, filtre_defaut sert uniquement a determiner sur quel tabpanel on se situe
			//il ne faut donc surtout pas appliqué le filtre par défaut sur le select_all normal, sinon il y aura deux tab avec le même contenu
			if(!$selectAllExtJs){
				// Ce querier vient d'être initialisé, on récupère son filtre par défaut
				$this->q[$template]
					->setFilterKey(ATF::$usr->getDefaultFilter($template))
					->setOrder(ATF::$usr->getDefaultFilter($template,"order"))
//					->setPage(ATF::$usr->getDefaultFilter($template,"page"))
					->setLimit(ATF::$usr->getDefaultFilter($template,"limit"));
			}
		}
//log::logger("[".md5(ATF::$id_thread)."] "."create => ".$this->q[$template]->page,ygautheron);				
		
		/* Définir le nombre d'enregistrements par page */
		//if ($limit!==NULL && $limit!==$this->q[$template]->limit["limit"]) {
			//$this->q[$template]->setPage(0);
			$this->q[$template]->setLimit($limit);
		//}
		
		/* Définir le comptage */
		if ($count) {
			$this->q[$template]->setCount($count);
		}
		
		/* Définir ce requêteur pour cet objet */
		if ($object && method_exists($object,"setQuerier")) {
//log::logger("[".md5(ATF::$id_thread)."] ".$this->q[$template],0,ygautheron);
			$object->setQuerier($this->q[$template]);
		}
		
		return $this->q[$template];
	}
	
//	/**
//    * Retourn VRAI si cer indentifiant querier a été initialisé
//    * @author Yann GAUTHERON <ygautheron@absystech.fr>
//	* @param string $template
//	* @return boolean
//    */ 
//	public function isInitialized($template) {
//		return isset($this->q[$template]);
//	}
	
	/**
    * Prépare le querier, et le retourne
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $id Identifiant du pager
	* @param string $fk CLés étrangères
	* @return querier
    */ 
	public function getAndPrepare($id,$fk=NULL,$selectAllExtJs=false) {
		$q = $this->create($id,NULL,true,NULL,$selectAllExtJs);
//log::logger("[".md5(ATF::$id_thread)."] ".$this->table." getAndPrepare debut fk=".count($fk)." where=".count($q->getWhere()),ygautheron);
		$q->setFk($fk);
//log::logger("[".md5(ATF::$id_thread)."] ".$this->table." getAndPrepare fin fk=".count($fk)." where=".count($q->getWhere()),ygautheron);
		return $q;
	}
	
	/**
    * Retourne le listing de la table à l'aide du requêteur correspondant du template
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $obj Table à lister, soit l'objet soir le nom de la table
	* @param string $template
	* @param string $count VRAI si on ne veut que le compte
	* @return void
    */ 
	public function select_all($obj,$template,$count=false) {
		if (!$template) {
			throw new errorATF('Pager::select_all called no template !',46);
		}
		
		if (is_string($obj)) {
			$obj = ATF::getClass($obj);
		}

		if (!$obj) {
			throw new errorATF('Pager::select_all called no object !',47);
		}
		
		$this->create($template);
		$this->q[$template]->setCount($count);
		$obj->setQuerier($this->q[$template]);
		return $obj->select_all();
	}
	
	/**
    * Supprime un querier du "trousseau"
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $id Identifiant du pager
	* @return void
    */ 
	public function unsetQuerier($id) {
		unset($this->q[$id]);
	}

	/**
    * Retourne le listing de la table à l'aide du requêteur correspondant du template
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param mixed $obj Table à lister, soit l'objet soir le nom de la table
	* @param string $template
	* @param string $method Méthode personalisée à executer, la méthode doit exister dans la classe $table
	* @param string $count VRAI si on ne veut que le compte
	* @return void
    */ 
//	public function custom($obj,$template,$method,$count=false) {
//		if (is_string($table)) {
//			$obj = ATF::getClass($obj);
//		}
//		$this->create($template);
//		$this->q[$template]->setCount($count);
//		if ($obj && method_exists($obj,"setQuerier")) {
//			$obj->setQuerier($this->q[$template]);
//			if (method_exists($obj,$method)) {
//				return $obj->$method();
//			}
//		}
//	}
};
?>