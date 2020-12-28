<?
/** Classe devis_ligne
* @package Optima
* @subpackage Cleodis
*/
require_once dirname(__FILE__)."/../devis_ligne.class.php";
class devis_ligne_cleodis extends devis_ligne {
	function __construct() {
		parent::__construct();
		$this->table = "devis_ligne";
		$this->controlled_by = "devis";
		$this->colonnes['fields_column'] = array(
			 'devis_ligne.produit'
			,'devis_ligne.quantite'
			,'devis_ligne.caracteristique'
			,'devis_ligne.type'
			,'devis_ligne.ref'
			,'devis_ligne.id_fournisseur'
			,'devis_ligne.neuf'
			,"devis_ligne.frequence_fournisseur"
			,'devis_ligne.prix_achat'=>array("renderer"=>"money")
		);

		$this->colonnes['primary'] = array(
			"id_devis"
			,"id_produit"=>array("autocomplete"=>array(
				"mapping"=>ATF::produit()->autocompleteMapping
			))
			,"id_fournisseur"
		);

		$this->colonnes['bloquees']['insert'] =  array('id_devis_ligne','id_devis')	;
		$this->colonnes['ligne'] =  array(
			"devis_ligne.id_produit"=>array("hidden"=>true)
			,"devis_ligne.produit"
			,"devis_ligne.quantite"
			,'devis_ligne.caracteristique'
			,"devis_ligne.type"
			,"devis_ligne.ref"
			,"devis_ligne.id_fournisseur"
			,"devis_ligne.prix_achat"
			,'devis_ligne.neuf'
			,"devis_ligne.frequence_fournisseur"
			,'devis_ligne.commentaire'
		);

		$this->no_insert=true;
		$this->no_update=true;
		$this->no_delete=true;

		$this->fieldstructure();
		$this->addPrivilege("toCommandeLigne");
		$this->foreign_key['id_fournisseur'] =  "societe";
	}

	/**
	* Retourne les fournisseurs d'un devis.
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param id_devis ID d'un devis
	* @param string pour contrôler le retour, soit en string soit en array
	*/
	function getFournisseurs($id_devis) {
		$this->q->reset()
					->addField("id_fournisseur")
					->where('id_devis',$id_devis)
					->setDistinct()
					->setStrict();
		return $this->sa();
	}

  	/**
	* Retourne les lignes d'un devis pour le grid des commande ligne
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos
	*/
  	function toCommandeLigne() {

  		log::logger($this->colonnes['ligne'] , "mfleurquin");

		// Le pager a normalement été préparé dans le template de commande
		$this->q->reset('field')->addField(util::keysOrValues($this->colonnes['ligne']))->setLimit(-1);
		if ($res = $this->select_all()) {
			// Maquillage des devis_ligne en commande_ligne
			foreach ($res["data"] as $kRow => $row) {
				foreach ($row as $kCol => $value) {
					$return[$kRow][str_replace("devis_ligne","commande_ligne",$kCol)]=$value;
				}
			}
			$res["data"] = $return;
		}
		return $res;
	}

	public function extJSgsa(&$post,&$s=NULL){
		$post["limit"] = 300;
		$return =  parent::extJSgsa($post,$s);
		return $return;


	}

};

class devis_ligne_midas extends devis_ligne_cleodis {
	function __construct() {
		parent::__construct();
		$this->table = "devis_ligne";
		$this->colonnes['fields_column'] = array(
			 'devis_ligne.produit'
			,'devis_ligne.quantite'
			,'devis_ligne.caracteristique'
			,'devis_ligne.type'
			,'devis_ligne.ref'
			,'devis_ligne.id_fournisseur'
		);

		$this->fieldstructure();
	}

}

class devis_ligne_cleodisbe extends devis_ligne_cleodis { };
class devis_ligne_cap extends devis_ligne_cleodis { };

class devis_ligne_bdomplus extends devis_ligne_cleodis { };
class devis_ligne_boulanger extends devis_ligne_cleodis { };


class devis_ligne_assets extends devis_ligne_cleodis { };