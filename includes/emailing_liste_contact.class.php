<?
/** 
* Classe emailing_liste, gère les contacts d'une liste de diffusion
* @package Optima
* @author Quentin JANON <qjanon@absystech.fr>
* @todo Refactoring ATF5
*/
class emailing_liste_contact extends emailing {
	function __construct() { // PHP5
		parent::__construct();
		$this->table = __CLASS__;
		$this->colonnes['fields_column'] = array(
			'emailing_liste_contact.id_emailing_liste'
			,'emailing_liste_contact.id_emailing_contact'
			,'emailing_liste_contact.sollicitation'
			,'emailing_liste_contact.tracking'
			,'emailing_liste_contact.last_tracking'
			,'emailing_liste_contact.erreur'
		);
		$this->colonnes_bloquees_insert = "etat,sollicitation,tracking,last_tracking,erreur";
		$this->colonnes_bloquees_update = "sollicitation,tracking,last_tracking,erreur";
		
		$this->fieldstructure();
		
	}
	
	/** 
	* Renvoi le nombre de mails a envoyer sur une liste
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 08-12-2010
	*/
	public function nbMail($id) {
		$this->q->reset()
					->addJointure("emailing_liste_contact","id_emailing_contact","emailing_contact","id_emailing_contact")
					->Where("id_emailing_liste",$id)
					->Where("opt_in","oui")
					->WhereIsNotNull("email")
					->setCountOnly();
		return $this->sa();
	}
	
};
?>