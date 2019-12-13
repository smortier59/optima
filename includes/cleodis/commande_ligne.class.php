<?
/**
* Classe commande
* @package Optima
* @subpackage Cléodis
*/
require_once dirname(__FILE__)."/../commande_ligne.class.php";
class commande_ligne_cleodis extends commande_ligne {
	function __construct() {
		parent::__construct();
		$this->controlled_by = "commande";
		$this->colonnes['fields_column'] = array(
			 'commande_ligne.produit'
			,'commande_ligne.quantite'
			,'commande_ligne.ref'
			,'commande_ligne.neuf'
			,"commande_ligne.frequence_fournisseur"
			,'commande_ligne.prix_achat'=>array("renderer"=>"money")
		);

		$this->colonnes['primary'] = array(
			"id_commande"
			,"id_produit"=>array("autocomplete"=>array(
				"mapping"=>ATF::produit()->autocompleteMapping
			))
			,"id_fournisseur"
		);

		$this->colonnes['bloquees']['insert'] = array('id_commande_ligne','id_commande');
		$this->colonnes['ligne'] =  array(
			"commande_ligne.id_produit"=>array("hidden"=>true)
			,"commande_ligne.produit"
			,"commande_ligne.quantite"
			,"commande_ligne.ref"
			,"commande_ligne.id_fournisseur"
			,"commande_ligne.prix_achat"
			,"commande_ligne.serial"
			,"commande_ligne.neuf"
			,"commande_ligne.frequence_fournisseur"
		);

		$this->fieldstructure();

		$this->addPrivilege("toFactureLigne");
		$this->foreign_key['id_fournisseur'] =  "societe";
		$this->no_insert=true;
		$this->no_update=true;
		$this->no_delete=true;
	}

	/**
	* Retourne les lignes d'un devis pour le grid des commande ligne
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos
	*/
  	function toFactureLigne() {
		// Le pager a normalement été préparé dans le template de commande
		$this->q->reset('field')->addField(util::keysOrValues($this->colonnes['ligne']));
		if ($res = $this->select_all()) {
			// Maquillage des devis_ligne en commande_ligne
			foreach ($res["data"] as $kRow => $row) {
				foreach ($row as $kCol => $value) {
					$return[$kRow][str_replace("commande_ligne","facture_ligne",$kCol)]=$value;
				}
				$return[$kRow]["facture_ligne.afficher"]="oui";
			}
			$res["data"] = $return;
		}
		return $res;
	}

};

class commande_ligne_midas extends commande_ligne_cleodis {
	function __construct() {
		parent::__construct();
		$this->table = "commande_ligne";
		$this->colonnes['fields_column'] = array(
			 'commande_ligne.produit'
			,'commande_ligne.quantite'
			,'commande_ligne.ref'
		);

		$this->fieldstructure();
	}

}

class commande_ligne_cleodisbe extends commande_ligne_cleodis { };
class commande_ligne_cap extends commande_ligne_cleodis { };


class commande_ligne_bdomplus extends commande_ligne_cleodis { };
class commande_ligne_bdom extends commande_ligne_cleodis { };
class commande_ligne_boulanger extends commande_ligne_cleodis { };