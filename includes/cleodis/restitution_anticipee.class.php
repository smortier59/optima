<?php


class restitution_anticipee extends classes_optima{

    public function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		$this->controlled_by = 'affaire';
		$this->colonnes["fields_column"] = array(
			 'restitution_anticipee.loyer'
            ,"restitution_anticipee.kilometrage"
			,"restitution_anticipee.echeance"
			,"restitution_anticipee.montant_ht"

		);

		$this->colonnes['primary'] = array(
			'id_affaire' => array("disabled"=>true),
			'loyer',
			'kilometrage',
			'echeance',
			'montant_ht'
		);

		$this->fieldstructure();
	}


	/**
    * Retourne la valeur par défaut spécifique aux données passées en paramètres
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param string $field
	* @param array &$s La session
	* @param array &$request Paramètres disponibles (clés étrangères)
	* @return string
    */
	public function default_value($field){
		if(ATF::_r('id_affaire')){

			$id_societe = ATF::affaire()->select(ATF::_r('id_affaire'), "id_societe");
		}

		return parent::default_value($field);
	}
}