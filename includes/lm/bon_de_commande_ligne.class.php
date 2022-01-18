<?
/**
* Classe bon de commande
* @package Optima
* @subpackage Leroy Merlin
*/
require_once dirname(__FILE__)."/../bon_de_commande_ligne.class.php";
class bon_de_commande_ligne_lm extends bon_de_commande_ligne {
	function __construct() {
		parent::__construct();
		$this->table = "bon_de_commande_ligne";

		$this->colonnes['fields_column'] = array(
			 'bon_de_commande_ligne.id_bon_de_commande'
			,'bon_de_commande_ligne.ref'
			,'bon_de_commande_ligne.produit'
			,'bon_de_commande_ligne.quantite'
			,'bon_de_commande_ligne.prix'
			,'bon_de_commande_ligne.prix_ttc'=>array("renderer"=>"money")
		);

		$this->colonnes['primary'] = array(
			"id_bon_de_commande"
			,'ref'
			,'produit'
			,'quantite'
			,'prix'
			,"prix_ttc"
		);

		$this->colonnes['ligne'] =  array(
			"bon_de_commande_ligne.produit"
			,"bon_de_commande_ligne.quantite"
			,"bon_de_commande_ligne.ref"
			,"bon_de_commande_ligne.prix"
			,"bon_de_commande_ligne.prix_ttc"
		);

		$this->controlled_by = "bon_de_commande";

//		$this->colonnes['bloquees']['insert'] = array('id_commande');

		$this->fieldstructure();
		$this->no_insert=true;
		$this->no_update=true;
		$this->no_delete=true;
	}

  	/**
	* Retourne les lignes d'un bon de commande pour les factures fournisseurs
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @return array
	*/
  	function toFacture_fournisseurLigne() {
		// Le pager a normalement été préparé dans le template de commande
		$this->q->reset('field')->addField(array_keys($this->colonnes['ligne']))->setCount()->setLimit(100000);

		if($sa = $this->select_all()){

			$data = array();
			foreach ($sa["data"] as $key => $value) {
				$esp = false;
				$i=0;
				while($i<count($data["data"]) && $esp == false){
					if(  ($data["data"][$i]["bon_de_commande_ligne.produit"] == $value["bon_de_commande_ligne.produit"])
					  && ($data["data"][$i]["bon_de_commande_ligne.ref"] == $value["bon_de_commande_ligne.ref"])
					  && ($data["data"][$i]["bon_de_commande_ligne.prix"] == $value["bon_de_commande_ligne.prix"])){

					  	$data["data"][$i]["bon_de_commande_ligne.prix"] += $value["bon_de_commande_ligne.prix"];
					  	$data["data"][$i]["bon_de_commande_ligne.prix_ttc"] += $value["bon_de_commande_ligne.prix_ttc"];
					  	$data["data"][$i]["bon_de_commande_ligne.quantite"] += $value["bon_de_commande_ligne.quantite"];
					  	$esp = true;
					  }
					 $i++;
				}

				if($esp == false){
					$data["data"][] = $value;
				}
			}

			$data["count"] = count($data["data"]);
			$sa = $data;
		}

		if ($sa) {
			// Maquillage des devis_ligne en commande_ligne
			$k=0;
			foreach ($sa["data"] as $kRow => $row) {
				$id_commande_ligne=$this->select($row["bon_de_commande_ligne.id_bon_de_commande_ligne"],"id_commande_ligne");
				$commande_ligne=ATF::commande_ligne()->select($id_commande_ligne);

				$row["bon_de_commande_ligne.serial"]=ATF::commande_ligne()->select($id_commande_ligne,"serial");

				$row["bon_de_commande_ligne.prix_ttc"] = $row["bon_de_commande_ligne.prix_ttc"];
				$row["bon_de_commande_ligne.prix_ht"] = round($row["bon_de_commande_ligne.prix_ttc"] / __TVA__ ,2);

				foreach ($row as $kCol => $value) {
					$return[$k][str_replace("bon_de_commande_ligne","facture_fournisseur_ligne",$kCol)]=$value;
				}

				ATF::facture_fournisseur_ligne()->q->reset()->addCondition("id_bon_de_commande_ligne",$return[$k]["facture_fournisseur_ligne.id_facture_fournisseur_ligne"])->setCount();
				$facture_fournisseur=ATF::facture_fournisseur_ligne()->sa();
/* 				if($return[$k]["facture_fournisseur_ligne.quantite"]>1){
					if($commande_ligne["serial"]){
						$tabSerial=explode(" ",$commande_ligne["serial"]);
						$qte=$return[$k]["facture_fournisseur_ligne.quantite"];
					}else{
						$qte=$return[$k]["facture_fournisseur_ligne.quantite"]-$facture_fournisseur["count"];
						$tabSerial=false;
					}
					for($i=0;$i<$qte;$i++){
						$return[$k+$i]=$return[$k];
						$return[$k+$i]["facture_fournisseur_ligne.quantite"]=1;
						if($tabSerial[$i]){
							ATF::facture_fournisseur_ligne()->q->reset()
															   ->addCondition("id_bon_de_commande_ligne",$return[$k]["facture_fournisseur_ligne.id_facture_fournisseur_ligne"])
															   ->addCondition("serial",$tabSerial[$i])
															   ->setCount();
							$facture_fournisseur_serial=ATF::facture_fournisseur_ligne()->sa();
//							if($facture_fournisseur_serial["count"]<1){
								$return[$k+$i]["facture_fournisseur_ligne.serial"]=$tabSerial[$i];
								$res_temp[$k+$i]=$return[$k+$i];
								$k+=$i;
//							}
						}else{
							$res_temp[$k+$i]=$return[$k+$i];
							$k+=$i;
						}
					}
					$k++;
				}else{
*///					if($facture_fournisseur["count"]<1){
						$res_temp[$k]=$return[$k];
						$k++;
//					}
//				}
			}
			$res["data"] = array_values($res_temp);
		}
		if($res["data"]){
			$res["count"]=count($res["data"]);
			return $res;
		}else{
			return false;
		}
	}

  	/**
	* Retourne les parcs dont le serial doit être inséré
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @return array
	*/
  	function toParcInsert() {
		// Le pager a normalement été préparé dans le template de commande
		$this->q->reset('field')->addField(array_keys($this->colonnes['ligne']))->setLimit(99999)->setCount();
		if ($sa = $this->select_all()) {
			// Maquillage des devis_ligne en commande_ligne
			$k=0;
			foreach ($sa["data"] as $kRow => $row) {
				$id_commande_ligne=$this->select($row["bon_de_commande_ligne.id_bon_de_commande_ligne"],"id_commande_ligne");
				$commande_ligne=ATF::commande_ligne()->select($id_commande_ligne);

				if($commande_ligne){
					if(!$row["bon_de_commande_ligne.serial"]=ATF::commande_ligne()->select($id_commande_ligne,"serial")){
						$row["bon_de_commande_ligne.serial"]="";
					}

					foreach ($row as $kCol => $value) {
						$return[$k][str_replace("bon_de_commande_ligne","parc",$kCol)]=$value;
					}

					$return[$k]["parc.quantite"]=$commande_ligne["quantite"];

					if($return[$k]["parc.quantite"]>1){
						//S'il n'y a pas de serial alors c'est qu'aucun des produits n'a un serial
						if(!$commande_ligne["serial"]){
							$qte=$return[$k]["parc.quantite"];
						//Sinon c'est que certains ont un serial, il faut donc les soustraire à la quantité initiale
						}else{
							$qte=($return[$k]["parc.quantite"]-(count(explode(" ",$commande_ligne["serial"]))));
						}
						/*for($i=0;$i<$qte;$i++){
							$return[$k+$i]=$return[$k];
							$return[$k+$i]["parc.quantite"]=1;
							$return[$k+$i]["parc.serial"]="";
						}*/
						if($qte){
							$return[$k]=$return[$k];
							$return[$k]["parc.quantite"]=1;
							$return[$k]["parc.serial"]="";
						}


					}
					$k++;
				}
			}
			//On retire les éléments ayant déjà un serial
			foreach($return as $key=>$item){
				if($item["parc.serial"]){
					unset($return[$key]);
				}else{
					$res["data"][]=$item;
				}
			}
		}
		$res["count"]=count($res["data"]);
		return $res;
	}

};
?>