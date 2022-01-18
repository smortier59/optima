<?
/** Classe devis
* @package Optima
* @subpackage Absystech
*/
require_once dirname(__FILE__)."/../devis_ligne.class.php";
class devis_ligne_absystech extends devis_ligne {
	function __construct() {
		parent::__construct();
		$this->table = "devis_ligne";
		$this->colonnes['fields_column'] = array(
			 'devis_ligne.produit'
			,'devis_ligne.quantite'=>array("width"=>50,"align"=>"center")
			,'devis_ligne.ref'=>array("width"=>100,"align"=>"center")
			,'devis_ligne.poids'=>array("width"=>100,"align"=>"center")
			,'devis_ligne.prix'=>array("width"=>100,"align"=>"right","renderer"=>"money")
			,'devis_ligne.prix_achat'=>array("width"=>100,"align"=>"right","renderer"=>"money")
		);

		$this->colonnes['primary'] = array(
			"id_devis"
			,"id_produit"=>array("autocomplete"=>array(
				"mapping"=>ATF::produit()->autocompleteMapping
			))
			,"id_fournisseur"
			,"id_compte_absystech"
			,"periode"
		);

		$this->colonnes['bloquees']['insert'] =  array('id_devis_ligne','id_devis');
		$this->colonnes['ligne'] =  array(
			"devis_ligne.id_produit"=>array("hidden"=>true)
			,"devis_ligne.produit"
			,"devis_ligne.quantite"
			,"devis_ligne.ref"
			,"devis_ligne.poids"
			,"devis_ligne.prix"
			,"devis_ligne.id_fournisseur"=>array("obligatoire"=>true)
			,"devis_ligne.prix_achat"
			,"devis_ligne.id_compte_absystech"=>array("obligatoire"=>true)
			,"devis_ligne.periode"
			,"devis_ligne.prix_nb"
			,"devis_ligne.prix_couleur"
			,"devis_ligne.prix_achat_nb"
			,"devis_ligne.prix_achat_couleur"
			,"devis_ligne.index_nb"
			,"devis_ligne.index_couleur"
			,"devis_ligne.visible"
		);

		$this->fieldstructure();
		$this->foreign_key['id_fournisseur'] =  "societe";
		$this->field_nom = "produit";
		$this->colonnes['bloquees']['export'] = array("id_produit");

	}

	/**
    * Permet d'avoir les lignes de devis dans l'ordre d'insertion
    * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
    */
	function select_all($order_by=false,$asc='desc',$page=false,$count=false,$parent=false){
		$asc="asc";
		$select_all=parent::select_all($order_by,$asc,$page,$count);
		if($select_all){
			foreach($select_all["data"] as $key=>$item){
				$item["devis_ligne.prix"] = (float)$item["devis_ligne.prix"];
				$item["devis_ligne.prix_achat"] = (float)$item["devis_ligne.prix_achat"];
				if (!$item["devis_ligne.prix"] || !$item["devis_ligne.prix_achat"]) {
					$select_all["data"][$key]["devis_ligne.marge"] = 0;
					$select_all["data"][$key]["devis_ligne.marge_absolue"] = 0;
				} else {
					$marge = (($item["devis_ligne.prix"]-$item["devis_ligne.prix_achat"])/$item["devis_ligne.prix"])*100;
					$select_all["data"][$key]["devis_ligne.marge"] = max(0,$marge);

					$marge_absolue = ($item["devis_ligne.prix"]*$item["devis_ligne.quantite"])-($item["devis_ligne.prix_achat"]*$item["devis_ligne.quantite"]);
					$select_all["data"][$key]["devis_ligne.marge_absolue"]=max(0,$marge_absolue);
				}
			}
		}

		return $select_all;
	}

	public function extJSgsa(&$post,&$s=NULL){
		$post["limit"] = 300;
		$return =  parent::extJSgsa($post,$s);
		return $return;
	}

  	/**
	* Retourne les lignes d'un devis pour le grid des commande ligne
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos
	*/
  	function toCommandeLigne() {
		// Le pager a normalement été préparé dans le template de commande
		$this->q->reset('field')->addField(util::keysOrValues($this->colonnes['ligne']))->reset('limit,page');
		if ($res = $this->select_all()) {

			// Maquillage des devis_ligne en commande_ligne
			foreach ($res["data"] as $kRow => $row) {
				foreach ($row as $kCol => $value) {
					$return[$kRow][str_replace("devis_ligne","commande_ligne",$kCol)]=$value;
				}

				$return[$kRow]["commande_ligne.prix"] = (float)$return[$kRow]["commande_ligne.prix"];
				$return[$kRow]["commande_ligne.prix_achat"] = (float)$return[$kRow]["commande_ligne.prix_achat"];

				if ($return[$kRow]["commande_ligne.prix"] && $return[$kRow]["commande_ligne.prix"]) {
					$marge = (($return[$kRow]["commande_ligne.prix"]-$return[$kRow]["commande_ligne.prix_achat"])/$return[$kRow]["commande_ligne.prix"])*100;
					$return[$kRow]["commande_ligne.marge"] = round($marge,2);
				} else {
					$return[$kRow]["commande_ligne.prix"] = 0;
					$return[$kRow]["commande_ligne.marge"] = 0;
				}
			}
			$res["data"] = $return;
		}
		return $res;
	}
};

class devis_ligne_att extends devis_ligne_absystech { };
class devis_ligne_wapp6 extends devis_ligne_absystech { };
class devis_ligne_demo extends devis_ligne_absystech { };
?>