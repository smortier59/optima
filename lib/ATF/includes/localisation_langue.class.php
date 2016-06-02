<?php
/** Classe localisation_langue
* @package ATF
*/
class localisation_langue extends classes_optima {
	/*---------------------------*/
	/*      Attributs            */
	/*---------------------------*/
	
	/*---------------------------*/
	/*      Constructeurs        */
	/*---------------------------*/	
	public function __construct() { 
		parent::__construct();
		$this->table = __CLASS__;
		$this->controlled_by = "localisation_traduction";
		
		$this->colonnes['fields_column'] = array('localisation_langue.localisation_langue',
												 'localisation_langue.libelle');
		$this->colonnes['primary'] = array('localisation_langue','libelle');
		$this->fieldstructure();
		
		$this->field_nom = "libelle";
	}
	
	/*---------------------------*/
	/*      Méthodes             */
	/*---------------------------*/	
	/* Retourne toutes les langues disponibles
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
	public function getAll(){
		
		$this->q->reset()->addField("localisation_langue");
		
		return parent::sa();
		
	}
		/*---------------------------*/
	/*      Méthodes             */
	/*---------------------------*/	
	/* Retourne toutes les langues disponibles
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
	public function getID($acro="fr"){
		$this->q->reset()->addField("id_localisation_langue")->where("localisation_langue",$acro)->setDimension('cell');
		
		return parent::sa();
		
	}	
};
?>