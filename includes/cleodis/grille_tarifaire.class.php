<?
/**
* Classe facture
* Cet objet permet de gérer les factures au sein de la gestion commerciale
* @package Optima
*/
class grille_tarifaire extends classes_optima {

    function __construct($table_or_id=NULL) {
		$this->table = "grille_tarifaire";
		parent::__construct($table_or_id);

		$this->colonnes['fields_column'] = array(
			 'grille_tarifaire.nom'
			,'grille_tarifaire.id_type_affaire'
            ,'grille_tarifaire.etat'
            ,'grille_tarifaire.date_creation'
		);

		// Panel principal
		$this->colonnes['primary'] = array(
			"nom",
            "id_type_affaire",
            "etat",
            "date_creation"
		);

		// Champs masqués
		$this->colonnes['bloquees']['insert'] =
		$this->colonnes['bloquees']['cloner'] =
		$this->colonnes['bloquees']['update'] = array('date_creation');
		$this->fieldstructure();

		$this->onglets = array('grille_tarifaire_ligne');

		$this->field_nom="nom";
		$this->foreign_key["id_type_affaire"] = "type_affaire";
		$this->selectAllExtjs=true;


	}

}