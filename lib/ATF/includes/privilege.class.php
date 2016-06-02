<?php
/** Classe privilege : Gestion des droits sur ATF5
* @package ATF
*/
class privilege extends classes_optima {
	/*---------------------------*/
	/*      Attributs            */
	/*---------------------------*/
	var $memory_optimisation_select = true; // selection optimisée, utile pour les petites tables très souvent sollicitées !
	
	/*---------------------------*/
	/*      Constructeurs        */
	/*---------------------------*/	
	public function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		$this->colonnes['fields_column'] = array('privilege.privilege');
		$this->fieldstructure();
		$this->privilege = $this->select_all();
	}
	
	/*---------------------------*/
	/*      Méthodes             */
	/*---------------------------*/	
};
?>