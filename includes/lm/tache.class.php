<?
/** Classe tache
* @package Optima
* @subpackage LMA
*/
require_once dirname(__FILE__)."/../tache.class.php";
class tache_lm extends tache {
	function __construct() {
		$this->table = "tache"; 
		parent::__construct();
	}
			
};


