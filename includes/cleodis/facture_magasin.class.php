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
		try {
			ATF::facture_magasin_recu()->q->reset()->where("statut", "en_attente_traitement");
			$factures_recu = ATF::facture_magasin_recu()->select_all();

			foreach ($factures_recu as $key => $value) {
				$ref = $value["deb_ref_facture"].$value["fin_ref_facture"];


				log::logger("Traitement de la facture ".strtoupper($ref), "controle_affaire_magasin_facture");

				$this->q->reset()->where("ref_facture", strtoupper($ref));
				$facture_magasin = $this->select_row();
				if($facture_magasin){
					log::logger("-- Reference de facture magasin trouvée chez nous facture : ".$facture_magasin["ref"], "controle_affaire_magasin_facture");

					$this->u(array("id_facture_magasin" => $facture_magasin["id_facture_magasin"], "etat"=> "paye"));
					log::logger("-- Mise à jour de l'état de la facture en payée", "controle_affaire_magasin_facture");
					ATF::facture_magasin_recu()->u(array("id_facture_magasin_recu"=> $value["id_facture_magasin_recu"], "statut"=> "traitee"));
					log::logger("-- Passage du statut de la facture magasin recu en traitée", "controle_affaire_magasin_facture");


					ATF::facture()->q->reset()->where("facture.id_affaire", $facture_magasin["id_affaire"])->addOrder("facture.id_facture", "ASC");
					$facture = ATF::facture()->select_row();
					if($facture){
						ATF::facture()->u(array("id_facture"=> $facture["facture.id_facture"], "ref_magasin"=> strtoupper($facture_magasin["ref_facture"])));
						log::logger("-- Mise à jour de la ref magasin ".strtoupper($facture_magasin["ref_facture"])."de la facture sur la facture ref ".$facture["ref"], "controle_affaire_magasin_facture");
					}
				}
			}
		} catch (errorATF $e) {
			throw new errorATF("probleme dans check_statut_facture",856);

		}




    }
};