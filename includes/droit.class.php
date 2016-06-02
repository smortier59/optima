<?
/**
* @package Optima
*/
class droit extends classes_optima {
	function __construct() { // PHP5
		parent::__construct();
		$this->table = __CLASS__;
		$this->colonnes = array('droit.id_user','droit.id_module','droit.droit');
	}
	
	/**
    * Retourne les infos sur un droit à partir de son ID
    * @author DEV ABSYSTECH <devl@absystech.fr>
	* @param int id 
    * @return array $droit 
    */   
	function select($id,$field=NULL) {
		$query = "SELECT 
							`".$this->table."`.*
							,`module`.`module`
							,`user`.`login`
								FROM `".$this->table."`
									LEFT JOIN `module` ON `droit`.`id_module` = `module`.`id_module`
									LEFT JOIN `user` ON `droit`.`id_user` = `user`.`id_user`
								WHERE `id_".$this->table."`=".$id;
		return ATF::db()->fetch_array_once($query);
	}

	function html_structure($filtre=false,$force=false) {
		$filtre = explode(",",$filtre);
		if (!$force) {
			if ($fe = $this->select_all()) {
				foreach ($fe as $key => $item) {
					foreach ($item as $key_ => $item_) {
						if (!in_array($key_,$filtre)) {
							$return[$key_] = $item_;
						}
					}
					return $return;
				}
			}
		}
		return ATF::db()->table2htmltable($this->table,$filtre);
	}
	
	/**
    * Retourne les droits d'un user sr un ou plusieur module
    * @author DEV ABSYSTECH <devl@absystech.fr>
	* @param int id_user 
	* @param string id_module 
    * @return string $droit 
    */   
	function from_user($id_user,$id_module=NULL) {
		if ($id_module) {
			$query = "SELECT `droit` FROM `droit` WHERE `id_user`=".$id_user." AND `id_module`=".$id_module;
//			$GLOBALS["log"]->logger("Avec id_module = ".$id_module." et query = ".$query,0,"qjanon",true);
			return ATF::db()->fetch_first_cell($query);
		} else {
			$query = "SELECT `module`,`".$this->table."` FROM `".$this->table."` INNER JOIN `module` ON `module`.`id_module` = `".$this->table."`.`id_module` WHERE `id_user`=".$id_user;
//			$GLOBALS["log"]->logger("Sans id_module".$query,0,"qjanon",true);
			if ($return = ATF::db()->sql2array($query,"module","droit")) {
				foreach ($return as $key => $item) {
					$id_module = ATF::module()->from_nom($key);
					/*if ($enfants = ATF::module()->enfants($id_module)) {
						$this->recursive($return,$id_module,$item);
					}*/
					$this->recursive_parent($return,$id_module,$item);
				}
				return $return;
			}
		}
	}
	
	function recursive(&$modules,$id_parent,$droit) {
		if ($enfants = ATF::module()->enfants($id_parent)) {
			foreach ($enfants as $key => $item) {
				$modules[$item["module"]] = $droit;
				$this->recursive($modules,$key,$droit);				
			}
		}
	}
	
	function recursive_parent(&$modules,$id_parent,$droit) {
		if ($parent = ATF::module()->select($id_parent)) {
			if (!$modules[$parent["module"]]) {
				$modules[$parent["module"]] = "read";//$droit;
			}
			$this->recursive_parent($modules,$parent["id_parent"],$droit);
		}
	}
	
	/**
    * Retourne le nom du user concerné par l'id droit en param
    * @author DEV ABSYSTECH <devl@absystech.fr>
	* @param int id du droit 
    * @return string $nom 
    */   
	function nom($id) {
		$query = "SELECT `id_user` FROM `".$this->table."` WHERE `id_".$this->table."` = '".$id."'";
		return ATF::user()->nom(ATF::db()->fetch_first_cell($query));
	}
	
	function purger($id_user) {
		$query = "DELETE FROM `".$this->table."` WHERE `id_user`=".$id_user; 
		ATF::db()->query($query);
	}
	
	/**
    * Permet d'inserer plusieurs droit sur plusieur modules pour un mm user
    * @author DEV ABSYSTECH <devl@absystech.fr>
	* @param int id_user
	* @param array droits
    */   
	function stocker($id_user,$droits) {
		if (!count($droits)) return;
		foreach ($droits as $key => $item) {
			$this->insert(array("id_user"=>$id_user,"id_module"=>$key,"droit"=>$item));
		}
	}
	
	/**
    * Supprimer les droit d'un user et les affecte à un autre
    * @author DEV ABSYSTECH <devl@absystech.fr>
	* @param int target
	* @param int source
    */   
	function transfer($source,$target) {
		$this->purger($target);
		$query = "INSERT INTO `".$this->table."` 
							SELECT
								'' AS `id_".$this->table."`
								,".$target." AS `id_user`
								,`".$this->table."`.`id_module`
								,`".$this->table."`.`droit`
								FROM `".$this->table."`
								WHERE `id_user`=".$source;
		ATF::db()->query($query);
	}
	
	/**
    * Supprimer les droit d'un user et lui affecte ds la table profil
    * @author DEV ABSYSTECH <devl@absystech.fr>
	* @param int id_profil
	* @param int id_user
    */   
	function transfer_from_profil($id_profil,$id_user) {
		$this->purger($id_user);
		$query = "INSERT INTO `".$this->table."` 
							SELECT
								'' AS `id_".$this->table."`
								,".$id_user." AS `id_user`
								,`profil_".$this->table."`.`id_module`
								,`profil_".$this->table."`.`droit`
								FROM `profil_".$this->table."`
								WHERE `id_profil`=".$id_profil;
		ATF::db()->query($query);
	}
};
?>