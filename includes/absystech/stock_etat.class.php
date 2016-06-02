<?php
/** 
* Classe stock_etat
* @author Mouad elhizabri
* @package Optima
* @subpackage Absystech
*/
require_once dirname(__FILE__)."/../stock_etat.class.php";

class stock_etat_absystech extends stock_etat {
	/** 
	* constructeur de stock_etat 
	*/
	public function __construct() {
		parent::__construct();
		$this->table = "stock_etat";
		$this->colonnes['fields_column']  = array(
			'stock_etat.id_stock'
			,'stock_etat.etat'=>array("width"=>30,"align"=>"center","renderer"=>"etat")
			,'stock_etat.date'=>array("width"=>100,"align"=>"center")
			,'stock_etat.commentaire'
		);
	
		$this->colonnes['primary'] = array(
			"date"
			,"etat"
			,"id_stock"
			,"commentaire"
			,"desassocier"=>array("custom"=>true,"data"=>array("oui","non"),"xtype"=>"combo")
		);
		$this->fieldstructure();
		$this->field_nom = "etat";
		$this->fieldProcess = array("commentaire"=>"aes_encrypte");
		$this->addPrivilege("checkEtatInventaire");
	}
	
	/**
	* etat actuel du stock
	* @author MOUAD EL HIZABRI
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $id_stock identifiant du stock 
	* @return string $etat dernier etat du stock
	**/
	public function getEtat($id_stock){
		$this->q
			 ->reset()
			 ->addField("etat")
			 ->addOrder("date","desc")
			 ->addOrder("id_stock_etat","desc")
			 ->where("id_stock",$id_stock)
			 ->setStrict()
			 ->setDimension("cell")
			 ->setLimit(1);
		return $this->select_all();
	}
	
	/**
	* Empêcher d'ajouter un stock_etat plus ancien que le plus récent
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	**/
	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed){
		$this->infoCollapse($infos);
		$this->q
			 ->reset()
			 ->addField("date")
			 ->addOrder("date","desc")
			 ->addOrder("id_stock_etat","desc")
			 ->where("id_stock",$infos["id_stock"])
			 ->setStrict()
			 ->setLimit(1);
		$dateLastEtat = $this->select_cell();
		if ($infos["date"] && $dateLastEtat && strtotime($infos["date"]) < strtotime($dateLastEtat)) {
			throw new error("Impossible d'ajouter un état de stock antérieur au dernier état connu !",20973);
		}
		if ($infos["desassocier"]=="oui") {
			$desassocier = true;
			$stockAvant = ATF::stock()->select($infos["id_stock"]);
			if ($stockAvant["id_affaire"]) {
				$infos["commentaire"] .= " (id_affaire precedente : ".$stockAvant["id_affaire"].", id_bon_de_commande_ligne precedent : ".$stockAvant["id_bon_de_commande_ligne"].")";
			}
		}
		unset($infos["desassocier"]);
		if ($result = parent::insert($infos,$s,$files,$cadre_refreshed)) {
			if ($desassocier=="oui") {
				ATF::stock()->update(array(
					"id_stock"=>$infos["id_stock"],
					"id_affaire"=>NULL,
					"id_bon_de_commande_ligne"=>NULL
				));
			}
		}
		return $result;
	}
	
	/**
	* Empêcher de modifier la date d'un stock_etat plus ancien que le plus récent
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	**/
	public function update($infos,&$s,$files=NULL,&$cadre_refreshed){
		$this->infoCollapse($infos);
		$id_stock = $this->select($infos["id_stock_etat"],"id_stock");
		$this->q
			 ->reset()
			 ->addField("date")
			 ->addOrder("date","desc")
			 ->addOrder("id_stock_etat","desc")
			 ->where("id_stock",$id_stock)
			 ->setStrict()
			 ->setLimit(1);
		$dateLastEtat = $this->select_cell();
		if ($infos["date"] && $dateLastEtat && strtotime($infos["date"]) < strtotime($dateLastEtat)) {
			throw new error("Impossible de modifier un état de stock antérieur au dernier état connu !",20974);
		}
		return parent::update($infos,$s,$files,$cadre_refreshed);	
	}
	
	/**
	* nombre d'etat d'un stock
	* @author MOUAD EL HIZABRI
	* @param le stock en question
	* @return le nombre d'etat
	*/
	public function nb_etat_stock($stock){
		$this->q
			 ->reset()
			 ->addField("count(etat)","nb_ligne")
			 ->where("stock_etat.etat","immo")
			 ->orWhere("stock_etat.etat","stock")
			 ->where("stock_etat.id_stock",$stock)
			 ->setStrict();
			 
		$nb_etat = $this->select_all();
		return $nb_etat[0]['nb_ligne'];
	}

	
	public function checkEtatInventaire($infos){
		$data["stock_etat"] = array("date" => date("Y-m-d H:i:s"),
									"id_stock" => ATF::stock()->decryptId($infos["id_stock"]),
									"etat" => "stock",
									"commentaire" => "Check Inventaire du ".date("d/m/Y")						
								   );
		$this->insert($data);
		ATF::$msg->addNotice(ATF::$usr->trans("check_inventaire",$this->table));				
	}
		
};

class stock_etat_att extends stock_etat_absystech {};
class stock_etat_demo extends stock_etat_absystech {};
?>