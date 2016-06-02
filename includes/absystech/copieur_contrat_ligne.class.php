<?	
/** Classe copieur_contrat_ligne
* @package Optima
* @subpackage Absystech
*/
class copieur_contrat_ligne extends classes_optima {
	function __construct() {
		parent::__construct(); 
		$this->table = __CLASS__;
		$this->controlled_by = "copieur_contrat";

		$this->colonnes['fields_column'] = array( 
			 'copieur_contrat_ligne.type'
			,'copieur_contrat_ligne.designation'=>array("width"=>100,"align"=>"center")
			,'copieur_contrat_ligne.quantite'=>array("width"=>50,"align"=>"center")
			,'copieur_contrat_ligne.prixNB'=>array("width"=>100,"align"=>"right")
			,'copieur_contrat_ligne.prixC'=>array("width"=>100,"align"=>"right")
			,'copieur_contrat_ligne.prix_achatNB'=>array("width"=>100,"align"=>"right")
			,'copieur_contrat_ligne.prix_achatC'=>array("width"=>100,"align"=>"right")
		);
 
		$this->colonnes['primary'] = array(
			"id_copieur_contrat"
			,"type"
			,"designation"
			,"quantite"
		);
		
		$this->colonnes['bloquees']['insert'] =  array('id_copieur_contrat_ligne','id_copieur_contrat');
		$this->colonnes['ligne'] =  array( 	
			"copieur_contrat_ligne.type"
			,"copieur_contrat_ligne.designation"
			,"copieur_contrat_ligne.quantite"
			,"copieur_contrat_ligne.prixNB"
			,"copieur_contrat_ligne.prixC"
			,"copieur_contrat_ligne.prix_achatNB"
			,"copieur_contrat_ligne.prix_achatC"
		);
		
		$this->fieldstructure();
		$this->field_nom = "type";
		
	}

	public function cleanAll($id_cc) {
		$this->q->reset()->where('id_copieur_contrat',$id_cc);
		foreach ($this->sa() as $k=>$i) {
			$this->delete($i['id_copieur_contrat_ligne']);
		}
		return true;
	}

  	/**
	* Retourne les lignes d'une commande pour le grid des facture ligne
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/	
  	public function toFactureLigne() {
		// Le pager a normalement été préparé dans le template de commande
		$this->q->reset('field,limit,page')->addField(util::keysOrValues($this->colonnes['ligne']));
		if ($res = $this->select_all()) {			
			// Maquillage des devis_ligne en commande_ligne
			foreach ($res["data"] as $kRow => $row) {
				foreach ($row as $kCol => $value) {
					$return[$kRow][str_replace("copieur_contrat_ligne","copieur_facture_ligne",$kCol)]=$value;
				}
			}
			$res["data"] = $return;
		}
		return $res;
	}
};

?>