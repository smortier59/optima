<?
require_once dirname(__FILE__)."/../suivi.class.php";
/** Suivi pour Alerteo
* @package Optima
* @subpackage Alerteo
*/
class suivi_alerteo extends suivi {
	function __construct() { // PHP5
		parent::__construct();
		$this->table = "suivi";
		$this->colonnes["fields_column"] = array(	
			'suivi.id_user'
			,'suivi.id_societe'
			,'suivi.date'
			,'suivi.texte'=>array("truncate"=>128/*,"editor"=>"simpleEditor"*/)
			,'fichier_joint'=>array("custom"=>true,"nosort"=>true,"type"=>"file")
		);
		$this->colonnes['primary'] = array(
			"id_societe"
			,"id_opportunite"
			,"date"
			,"texte"=>array("xtype"=>"textarea","height"=>300)
		);
		
		$this->fieldstructure();
		
		$this->colonnes['bloquees']['select'] =  array('id_user','type');	
		$this->colonnes['bloquees']['insert'] =  array('id_user');	
		$this->colonnes['bloquees']['update'] =  array('id_user');	
		
		$this->files["fichier_joint"] = true;
		$this->onglets = array('tache');
	
		//modification du field_nom, car le foreign_key_translator prends ces champs lors de l'envoi de suivi.id_suivi (du $class->q->field)
		//et de ce fait, fait des jointures sur ces tables en prenant pour foreign_key un champs de suivi (suivi.id_contact=contact.id_contact)
		//or suivi.id_contact n'existe pas dans la table suivi de la base alerteo
		//$this->field_nom = "%id_societe% - %contact.id_contact%";
		$this->field_nom = "%id_societe% - %date%";
	}
		
};

?>