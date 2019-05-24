<?
/** Classe facture_non_parvenue
* @package Optima
* @subpackage Cléodis
*/
class facture_magasin extends classes_optima {
	function __construct() {
		parent::__construct();
		$this->table = "facture_magasin";

		$this->colonnes['fields_column'] = array(
			"facture_non_parvenue.ref_facture"
			,"facture_non_parvenue.id_affaire"
			,"facture_non_parvenue.etat"
		);

		$this->fieldstructure();

		$this->no_insert = true;
		$this->no_update = true;
		$this->no_delete = true;
		$this->field_nom = "ref_facture";
		$this->selectAllExtjs=true;

		$this->foreign_key["id_affaire"] = "affaire";
	}

	public function check_statut_facture(){

       ATF::facture_magasin_recu()->q->reset()->where("statut", "en_attente_traitement");

       $factures_recu = ATF::facture_magasin_recu()->select_all();

       foreach ($factures_recu as $key => $value) {
			$ref = $value["deb_ref_facture"].$value["fin_ref_facture"];

			$this->q->reset()->where("ref_facture", $ref);
			$facture_magasin = $this->select_row();
			if($facture_magasin){
				$this->u(array("id_facture_magasin" => $facture_magasin["id_facture_magasin"], "etat"=> "paye"));
				ATF::facture_magasin_recu()->u(array("id_facture_magasin_recu"=> $value["id_facture_magasin_recu"], "statut"=> "traitee"));
			}
       }
    }
};