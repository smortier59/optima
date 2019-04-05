<?
/**
* Classe commande
* @package Optima
* @subpackage Absystech
*/
require_once dirname(__FILE__)."/../commande_ligne.class.php";
class commande_ligne_absystech extends commande_ligne {
	function __construct() {
		parent::__construct();
		$this->colonnes['fields_column'] = array(
			 'commande_ligne.produit'
			,'commande_ligne.quantite'=>array("width"=>50,"align"=>"center")
			,'commande_ligne.ref'=>array("width"=>100)
			,'commande_ligne.prix'=>array("renderer"=>"money","width"=>100)
			,'commande_ligne.prix_achat'=>array("renderer"=>"money","width"=>100)
		);

		$this->colonnes['primary'] = array(
			"id_commande"
			,"id_produit"=>array("autocomplete"=>array(
				"mapping"=>ATF::produit()->autocompleteMapping
			))
			,"id_fournisseur"=>array("autocomplete"=>array(
				"function"=>"autocompleteFournisseurs"
			))
			,"id_compte_absystech"
		);

		$this->colonnes['bloquees']['insert'] = array('id_commande_ligne','id_commande');
		$this->colonnes['ligne'] =  array(
			"commande_ligne.id_produit"=>array("disable"=>true)
			,"commande_ligne.produit"
			,"commande_ligne.quantite"
			,"commande_ligne.ref"
			,"commande_ligne.prix"
			,"commande_ligne.id_fournisseur"
			,"commande_ligne.prix_achat"
			,"commande_ligne.id_compte_absystech"
			,"commande_ligne.serial"
			,"commande_ligne.prix_nb"
			,"commande_ligne.prix_couleur"
			,"commande_ligne.prix_achat_nb"
			,"commande_ligne.prix_achat_couleur"
			,"commande_ligne.index_nb"
			,"commande_ligne.index_couleur"
			,"commande_ligne.serial"
			,"commande_ligne.visible"
		);

		$this->fieldstructure();

		$this->foreign_key['id_fournisseur'] =  "societe";

		$this->addPrivilege("selectOnlyNotYetOrderedQuantities");
	}

	/**
    * Permet d'avoir les lignes de commande dans l'ordre d'insertion
    * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
    */
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false,$parent=false){
		$select_all=parent::select_all($order_by,$asc,$page,$count);
		if($select_all["count"]>0){
			foreach($select_all["data"] as $key=>$item){
				if (!$item["commande_ligne.prix"] || !$item["commande_ligne.prix_achat"]) {
					$select_all["data"][$key]["commande_ligne.marge"] = 0;
					$select_all["data"][$key]["commande_ligne.marge_absolue"] = 0;
				} else {
					$marge = (($item["commande_ligne.prix"]-$item["commande_ligne.prix_achat"])/$item["commande_ligne.prix"])*100;
					$select_all["data"][$key]["commande_ligne.marge"] = max(0,$marge);

					$marge_absolue = ($item["commande_ligne.prix"]*$item["commande_ligne.quantite"])-($item["commande_ligne.prix_achat"]*$item["commande_ligne.quantite"]);
					$select_all["data"][$key]["commande_ligne.marge_absolue"]=max(0,$marge_absolue);
				}
			}
		}
		return $select_all;
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
					$return[$kRow][str_replace("commande_ligne","facture_ligne",$kCol)]=$value;
				}
			}
			$res["data"] = $return;

			$id_commande = ATF::commande_ligne()->select($res["data"][0]["facture_ligne.id_facture_ligne"] , "id_commande");

			if(ATF::affaire()->select(ATF::commande()->select($id_commande, "id_affaire") , "nature") == "consommable"){
				ATF::facture()->q->reset()->where("facture.id_affaire",ATF::commande()->select($id_commande, "id_affaire"))
								  ->addOrder("facture.date", "desc")
								  ->setLimit(1);

				if($facture = ATF::facture()->select_row()){
					ATF::facture_ligne()->q->reset()->where("id_facture",$facture["facture.id_facture"]);
					$prec = ATF::facture_ligne()->select_all();

					foreach ($prec as $key => $value) {
						$k = 0;
						while ($k < count($res["data"])-1 || ($value["ref"] == $res["data"][$k]["facture_ligne.ref"] && $value["produit"] == $res["data"][$k]["facture_ligne.produit"])) {
							if($value["ref"] == $res["data"][$k]["facture_ligne.ref"] && $value["produit"] == $res["data"][$k]["facture_ligne.produit"]){
								$res["data"][$k]["facture_ligne.index_nb"] = $value["index_nb"];
								$res["data"][$k]["facture_ligne.index_couleur"] = $value["index_couleur"];
							}
							$k++;
						}
					}
				}
			}

		}
		return $res;
	}

	/**
	* Retourne les lignes d'une commande pour le grid des commande ligne, aucune ligne si aucun fournisseur sélectionné
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param Objet querier
	*/
	 public function selectOnlyNotYetOrderedQuantities($post,$s) {

	 	//log::logger($post , "mfleurquin");

		$q = ATF::_s("pager")->create($post["pager"],NULL,true);
		$q->reset('where')
			->where("commande_ligne.id_compte_absystech",1) // Vente de marchandise uniquement !
			->where("commande_ligne.id_commande",ATF::commande()->decryptId($post["id_commande"]))
		/*if($post["id_fournisseur"]){
			$fournisseurs = explode(",", $post["id_fournisseur"]);
			$fournisseurs = array_unique($fournisseurs);
			$post["id_fournisseur"]	= $fournisseurs;
			foreach($fournisseurs as $k=>$v){
				$q->where("commande_ligne.id_fournisseur",$this->decryptId($v), "OR");
			}
		}*/
		->where("commande_ligne.id_fournisseur",ATF::societe()->decryptId($post["id_fournisseur"]));
		$post["function"]="selectOnlyNotYetOrderedQuantitiesAfter";



		$res = $this->extJSgsa($post,$s);


		foreach ($res as $key => $value) {
			$quantite = 0;
			ATF::bon_de_commande_ligne()->q->reset()->where("bon_de_commande_ligne.ref", $value["bon_de_commande_ligne.ref"])
													->from("bon_de_commande_ligne", "id_bon_de_commande", "bon_de_commande" , "id_bon_de_commande")
													->from("bon_de_commande_ligne", "id_bon_de_commande_ligne", "stock" , "id_bon_de_commande_ligne")
													->where("bon_de_commande.id_commande", ATF::commande()->decryptId($post["id_commande"]));

			$commandePrecedente = ATF::bon_de_commande_ligne()->select_all();


			if($commandePrecedente){

				foreach($commandePrecedente as $k=>$v){

					if($v["id_stock"]){
						ATF::stock_etat()->q->reset()->where("stock_etat.id_stock",$v["id_stock"])
													->setLimit(1)
												  	->addOrder("stock_etat.id_stock_etat","desc");
						$stock = ATF::stock_etat()->select_row();


						if($stock["etat"] != "sinistr") $quantite += 1;

					}else{	$quantite += 1;  }


				}

				if($res[$key]["bon_de_commande_ligne.quantite"] - $quantite <= 0){	unset($res[$key]);
				}else{	$res[$key]["bon_de_commande_ligne.quantite"] = $res[$key]["bon_de_commande_ligne.quantite"] - $quantite; }

			}
		}

		$i = 0;
		foreach($res as $k=>$v){
			$data[$i] = $v;
			$i++;
		}

		return $data;

	}

  	/**
	* Retourne les lignes d'une commande pour le grid des facture ligne
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/
  	public function selectOnlyNotYetOrderedQuantitiesAfter() {
		$this->q->reset('field,limit,page')->addField(util::keysOrValues($this->colonnes['ligne']));
		if ($res = $this->select_all()) {
			foreach ($res["data"] as $kRow => $row) {
				$res["data"][$kRow]["commande_ligne.etat"]="en_cours";
			}
			util::finalAliasesTranslator($res,"commande_ligne","bon_de_commande_ligne");
		}
		return $res;
	}

	/** @author Morgan FLEURQUIN	<mfleurquin@absystech.fr> */
	public function can_update(){
		throw new errorATF("Pour modifier une ligne de commande, il faut modifier dans la commande !!!");
		return false;
	}

	/**
	* @author Morgan Fleurquin <mfleurquin@absystech.fr>
	*/
	public function can_insert(){
		throw new errorATF("Pour inserer une ligne de commande, il faut modifier dans la commande !!!");
		return false;
	}

	/**
	* @author Morgan Fleurquin <mfleurquin@absystech.fr>
	*/
	/*public function can_delete(){
		throw new errorATF("Pour supprimer une ligne de commande, il faut modifier dans la commande !!!");
		return false;
	}*/

	/**
	* Surcharge de l'insert pour recalcul du total
	* @author mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	*/
	/*public function update($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){

		$this->infoCollapse($infos);

		ATF::db($this->db)->begin_transaction();

		$retour=parent::update($infos,$s,$files,$cadre_refreshed,$nolog);
		$id_commande=$infos["id_commande"];
		$this->majPrixCommande($id_commande);

		ATF::db($this->db)->commit_transaction();

		ATF::commande()->redirection("select",$id_commande);

		return $retour;
	}*/


	/**
	* Surcharge de l'insert pour recalcul du total
	* @author mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	*/
	/*public function insert($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){

		ATF::db($this->db)->begin_transaction();

		$lastId=parent::insert($infos,$s,$files,$cadre_refreshed,$nolog);
		$id_commande=$this->select($lastId,"id_commande");
		$this->majPrixCommande($id_commande);

		ATF::db($this->db)->commit_transaction();

		ATF::commande()->redirection("select",$id_commande);

		return $lastId;
	}*/

	/**
	* Surcharge de l'insert pour recalcul du total
	* @author mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	*/
	/*public function delete($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		ATF::db($this->db)->begin_transaction();

		foreach($infos["id"] as $id){
			$id_decrypt=$this->decryptId($id);
			$id_commande=$this->select($id_decrypt,"id_commande");
			$result=parent::delete($id_decrypt);
			$this->majPrixCommande($id_commande);
		}

		ATF::db($this->db)->commit_transaction();

		ATF::commande()->redirection("select",$id_commande);

		return $result;
	}*/


	/**
	* Recalcul du prix et du prix achat
	* @author mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	*/
	/*public function majPrixCommande($id_commande){
		$this->q->reset()->addCondition("id_commande",$id_commande);
		$prix=$prix_achat=0;
		foreach($this->sa() as $item){
			$prix+=($item["prix"]*$item["quantite"]);
			$prix_achat+=($item["prix_achat"]*$item["quantite"]);
		}
		$prix = $prix + ATF::commande()->select($id_commande , "frais_de_port");
		ATF::commande()->u(array("id_commande"=>$id_commande,"prix_achat"=>$prix_achat,"prix"=>$prix));

		$infos["id"]=$id_commande;

		return true;
	}*/





};
class commande_ligne_att extends commande_ligne_absystech { };
class commande_ligne_wapp6 extends commande_ligne_absystech { };
class commande_ligne_demo extends commande_ligne_absystech { };
?>