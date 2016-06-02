<?php
/** 
* Classe pointage_horaire
* @author Morgan FLEURQUIN <mfleurquin@absystech.net>
* @package optima
*/

class pointage_horaire extends classes_optima {
	/**
	* Constructeur
	*/
	function __construct($table_or_id=NULL) {
		parent::__construct($table_or_id);
		$this->table = __CLASS__;


		$this->colonnes['fields_column'] = array(
			  'pointage_horaire.id_user'			 
			 ,'pointage_horaire.Jour'
			 ,'pointage_horaire.debut'
			 ,'pointage_horaire.fin'			 
		 );

		$this->fieldstructure();

		$this->no_update = true;
		$this->no_insert = true;
		$this->no_delete = true;
	}



}
