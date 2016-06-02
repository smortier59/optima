<?
/**
* @date 2010-03-12
* @package inventaire
* @version 1.0.0
* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
*
*/ 
class cout_categorie extends classes_optima {
	function __construct() { // PHP5
		parent::__construct();
		$this->table = __CLASS__;
	}	

	/**
	* CF options classes.class.php
	* Tri par défaut par le libellé
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
	public function options($fields=NULL,$id_on_key=NULL,$reset=true,$order_by='cout_categorie',$asc='asc',$bypassvalue=false) {	
		return parent::options($fields,$id_on_key,$reset,$order_by,$asc,$bypassvalue);
	}

};
?>