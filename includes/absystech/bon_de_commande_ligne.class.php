<?php
/**
* Classe bon_de_commande_ligne
* @author Mouad elhizabri
* @package Optima
* @subpackage Absystech
*/
//require_once dirname(__FILE__)."/../bon_de_commande_ligne.class.php";

class bon_de_commande_ligne_absystech extends classes_optima{
	/**
	* Constructeur
	*/
	public function __construct() {
		parent::__construct();
		$this->table = "bon_de_commande_ligne";
		$this->colonnes['fields_column']  = array(
			 'bon_de_commande_ligne.ref'
			,'bon_de_commande_ligne.produit'
			,'bon_de_commande_ligne.etat'=>array("renderer"=>"etat","width"=>30)
		);

		//panel principale "premier plan"
		$this->colonnes['primary'] = array(
			  "ref"
			 ,"produit"
			 ,"etat"
		 );
		$this->fieldstructure();

		// Propriété des panels
		$this->panels['primary'] = array("visible"=>true,"collapsible"=>false);


		//$this->foreign_key['id_fournisseur'] =  "societe";

		$this->field_nom = "produit";

		// Lignes de bon de commande
		$this->colonnes['ligne'] =  array(
			"bon_de_commande_ligne.ref"
			,"bon_de_commande_ligne.produit"
			,"bon_de_commande_ligne.quantite"
			,"bon_de_commande_ligne.prix"
			,"bon_de_commande_ligne.prix_achat"
			,"bon_de_commande_ligne.etat"
		);
	}

	/**
	* Surcharge du insert
	* @author Mouad EL HIZABRI
	**/
	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed){
		$this->infoCollapse($infos);
		//unset($infos["prix_achat"]);
		return parent::insert($infos,$s,$files,$cadre_refreshed);
	}

	/**
	* Surcharge du update
	* @author Mouad EL HIZABRI
	**/
	public function update($infos,&$s,$files=NULL,&$cadre_refreshed){
		$this->infoCollapse($infos);
		return parent::update($infos,$s,$files,$cadre_refreshed);
	}

	/**
	* Retourne les lignes d'une commande pour le grid des commande ligne
	* @author Mouad EL HIZABRI
	*/
	 public function BonCommandeLigne() {


	 	// Le pager a normalement été préparé dans le template de commande
		$this->q->reset('field,limit,page')->addField(util::keysOrValues($this->colonnes['ligne']));
		if ($res = $this->select_all()) {
			//remplacer la chaine commande_ligne par bon_de_commande_ligne dans les colonnes de commande
			foreach ($res["data"] as $kRow => $row) {
				foreach ($row as $kCol => $value) {

						$return[$kRow][str_replace("commande_ligne","bon_de_commande_ligne",$kCol)]=$value;

					//$return[$kRow][$kCol]=$value;

				}
			}
			$res["data"] = $return;
		}

		return $res;
	}
};

class bon_de_commande_ligne_att extends bon_de_commande_ligne_absystech { };
class bon_de_commande_ligne_wapp6 extends bon_de_commande_ligne_absystech { };
class bon_de_commande_ligne_atoutcoms extends bon_de_commande_ligne_absystech { };
class bon_de_commande_ligne_demo extends bon_de_commande_ligne_absystech { };
class bon_de_commande_ligne_nco extends bon_de_commande_ligne_absystech { };

?>