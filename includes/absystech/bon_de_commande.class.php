<?php
/**
* Classe commande fournisseur (bon de commande)
* @author Mouad elhizabri
* @package Optima
* @subpackage Absystech
*/
require_once dirname(__FILE__)."/../bon_de_commande.class.php";
class bon_de_commande_absystech extends bon_de_commande{
	/**
	* Constructeur
	*/
	public function __construct() {
		parent::__construct();
		$this->table = "bon_de_commande";	
		$this->colonnes['fields_column']  = array(
			 'bon_de_commande.ref'
			,'bon_de_commande.resume'
			,'bon_de_commande.date'
			,'bon_de_commande.date_fin'
			,'bon_de_commande.etat'=>array("renderer"=>"etat","width"=>30)
			,'bon_de_commande.prix'=>array("aggregate"=>array("min","avg","max","sum"),"align"=>"right","renderer"=>"money","width"=>100)
			,'bon_de_commande.prix_achat'=>array("aggregate"=>array("min","avg","max","sum"),"align"=>"right","renderer"=>"money","width"=>100)
			,'termine'=>array("custom"=>true,"align"=>"center","nosort"=>true,"renderer"=>"setCompleted","width"=>50)
			,'url'=>array("renderer"=>"viewCommand","custom"=>true,"align"=>"center","nosort"=>true)
		);
		//panel principale "premier plan"									
		$this->colonnes['primary'] = array(
			 "id_commande"	
			 ,"resume"
			 ,"ref"
			 ,"etat"
			 ,"id_societe"
			 ,"date"
			 ,"date_fin"
			 ,"id_fournisseur"=>array("autocomplete"=>array("function"=>"autocompleteFournisseursDeCommande"))	
			 //,"fournisseur_import"=>array("custom"=>true)		
			 ,"id_fournisseurFinal"=>array("custom"=>true,"autocomplete"=>array("function"=>"autocompleteFournisseurs"))
			 ,"id_affaire"
			 ,"id_user"
		 );
		//produits de la commande
		$this->colonnes['panel']['lignes'] = array(
			"produits"=>array("custom"=>true)
		);
		//les totaux
		$this->colonnes['panel']['total'] = array(
			"frais_de_port"=>array("custom"=>true,"formatNumeric"=>true,"xtype"=>"textfield")
			,"prix"=>array("custom"=>true,"readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield")
			,"prix_achat"=>array("custom"=>true,"readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield")
			,"tva"
		);
		
		$this->fieldstructure();	
		$this->foreign_key['id_fournisseur'] =  "societe";	
		$this->foreign_key['id_fournisseurFinal'] =  "societe";
		$this->field_nom = "resume";
		
		// Onglets
		$this->onglets = array('bon_de_commande_ligne', 'facture_fournisseur'=>array('opened'=>true));

		// Propriété des panels
		$this->panels['primary'] = array("visible"=>true,'nbCols'=>3);
		$this->panels['lignes'] = array("visible"=>true, 'nbCols'=>1);
		$this->panels['total'] = array("visible"=>true,'nbCols'=>4);
		
		// Champs masqués
		$this->colonnes['bloquees']['insert'] =  
		$this->colonnes['bloquees']['cloner'] =  
		$this->colonnes['bloquees']['update'] =  array('id_user','id_affaire','tva','id_commande','date','etat','id_societe');	

		//privileges
		$this->addPrivilege("insert","update");
		$this->addPrivilege("select_all","update");
		
		//$this->addPrivilege("autocompleteConditions");
		$this->addPrivilege("setCompleted","update");
		
		$this->no_update = true;
	}
	
	/**
	* Surcharge du select-All
	* @author Quentin JANON <qjanon@absystech.fr>
	**/
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){
		$c = new bon_de_commande_absystech(); // Protégerer le queryier courant
		$return = parent::select_all($order_by,$asc,$page,$count);
		foreach ($return['data'] as $k=>$i) {
			$return['data'][$k]['url'] = $c->getProviderUrl($i["bon_de_commande.id_bon_de_commande"]);
		}
		return $return;
	}

	/**
	* MultiExplode recursif ! 
	* @author Mouad EL HIZABRI
	* @param String $separateur array des separateur
	* @param String $chaine chaine a explodé
	* @return array tableau des sous-chaîne du paramètre chaine 
	*/
	private function multi_explode($separateur,$chaine) {
    	$ary = explode($separateur[0],$chaine);
		//apres separation de la chaine on supprime le separateur d'avant
    	array_shift($separateur);
    	if($separateur != NULL) {
          $ary = $this->multi_explode($separateur, $ary[1]);
   		}
        return  $ary;
	}

	/**
	* Surcharge de l'insertion
	* @author Mouad EL HIZABRI Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @return Integer id insertion de la commande 
	* dans le bon_de_commande et dans le stock
	**/
	public function insert($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL){
		
		
		if($infos["bon_de_commande"]["id_fournisseurFinal"] == NULL){
			throw new errorATF("Il faut un fournisseur pour le bon de commande");
		}
		$infos["bon_de_commande"]["id_fournisseur"] = $infos["bon_de_commande"]["id_fournisseurFinal"];
		$infos["label_bon_de_commande"]["id_fournisseur"] = $infos["label_bon_de_commande"]["id_fournisseurFinal"];
		unset($infos["bon_de_commande"]["id_fournisseurFinal"], $infos["label_bon_de_commande"]["id_fournisseurFinal"]);
		
		$infos_ligne = json_decode($infos["values_".$this->table]["produits"],true);
		$chaine = $infos['values_bon_de_commande']['produits'];
		$separateur = array(',',':','"');
		//libelle du stock
		$libelle = $this->multi_explode($separateur,$chaine);
		//enlever la premiere dimenssion [dim1][dim2] pour garder que la dim2 de $infos
		$this->infoCollapse($infos);
		$societe=ATF::societe()->select($infos["id_societe"]);
		$commande = ATF::commande()->select($infos['id_commande']);
		//$infos["ref"] = $commande["ref"];
		$infos["id_user"] = ATF::$usr->getID();
		if($societe["id_pays"]!="FR") $infos["tva"] = 1;
		else $infos["tva"] =  __TVA__;
		if($infos["id_commande"]){
			$infos["id_affaire"]=ATF::commande()->select($infos['id_commande'],"id_affaire");
		}
		
		//Nouvelle transaction
		ATF::db($this->db)->begin_transaction();
			
		/*------------ Insertion COMMANDE FOURNISSEUR   ----------------*/
		unset($infos["sous_total"],$infos["marge"],$infos["marge_absolue"],$infos["id_stock"]);
		$last_id=parent::insert($infos,$s); 
		foreach($infos_ligne as $key=>$item){
			foreach($item as $k=>$i){
				$k_unescape=util::extJSUnescapeDot($k);
				$item[str_replace("bon_de_commande_ligne.","",$k_unescape)]=$i;
				unset($item[$k]);
			}

			$ligne_bdc["id_bon_de_commande"]=$last_id;
			//$item["id_affaire"]=$infos["id_affaire"];
			//$ligne_bdc["frais_de_port"]=$infos["frais_de_port"];
			$ligne_bdc["tva"]=$infos["tva"];
			
			// Données ligne
			$ligne_bdc["ref"]=$item["ref"];
			$ligne_bdc["produit"]=$item["produit"];
			$ligne_bdc["quantite"]=$item["quantite"];
			$ligne_bdc["prix"]=$item["prix"];
			$ligne_bdc["prix_achat"]=$item["prix_achat"];
			$ligne_bdc["date"]=$item["date"];
			$ligne_bdc["etat"]=$item["etat"];
			
			//TEST pour savoir si la quantité inserée n'est pas > à la quantité commandé dans la commande
			ATF::commande_ligne()->q->reset()->where("ref" , $item["ref"])
											 ->where("id_commande", $infos['id_commande']);
			$quantiteDepart = ATF::commande_ligne()->select_row();		
			
			ATF::bon_de_commande_ligne()->q->reset()->where("bon_de_commande_ligne.ref" , $item["ref"])
													->from("bon_de_commande_ligne" , "id_bon_de_commande" , "bon_de_commande" , "id_bon_de_commande")
													->from("bon_de_commande_ligne" , "id_bon_de_commande_ligne" , "stock" , "id_bon_de_commande_ligne")
													->where("bon_de_commande.id_commande", $infos['id_commande']);
			$Recu = ATF::bon_de_commande_ligne()->select_all();

			

			$quantiteRecu  = 0;

			foreach ($Recu as $kRecu => $vRecu) {
				if($vRecu["id_stock"]){
					ATF::stock_etat()->q->reset()->where("stock_etat.id_stock",$vRecu["id_stock"])
												->setLimit(1)
											  	->addOrder("stock_etat.id_stock_etat","desc");
					$stock = ATF::stock_etat()->select_row();
					if($stock["etat"] != "sinistr") $quantiteRecu  += 1;
				}
			}			

			
			if(isset($quantiteDepart)){
				if(!$quantiteRecu){
					//Pas encore de commande pour celui la
					if($item["quantite"] > $quantiteDepart["quantite"]){
						ATF::db($this->db)->rollback_transaction();
						throw new errorATF("Quantité saisie ".$item["quantite"]. " alors que la quantité max pour le produit ref : ".$item["ref"]. " est de ".$quantiteDepart["quantite"]);
					}								
				}else{
					//Deja des commandes concernant ce produit pour cette affaire
					$total = $quantiteRecu + $item["quantite"]; 
					if($total > $quantiteDepart["quantite"]){
						ATF::db($this->db)->rollback_transaction();
						throw new errorATF("Quantité saisie ".$item["quantite"]." + quantite déja commandée ".$quantiteRecu." = ".$total." alors que la quantité max pour le produit ref : ".$item["ref"]. " est de ".$quantiteDepart["quantite"]);
					}				
				}
			}			
			$id_bon_de_commande_ligne = ATF::bon_de_commande_ligne()->insert($ligne_bdc,$s);

			// Données stock
			if ($ligne_bdc["etat"]=="recu") {
				// Le stock est déjà là
				for($q=0;$q<$item["quantite"];$q++){
					// On crée la liaison vers le bon de commande pour autant de stocks dispos que de quantité demandée
					if ($id_stock = ATF::stock()->getRefEnStock($ligne_bdc["ref"])) {
						ATF::stock()->update(array(
							'id_stock'=>$id_stock
							,'id_bon_de_commande_ligne'=>$id_bon_de_commande_ligne
							,'id_affaire'=>$infos["id_affaire"]
						));
					} else {
						ATF::db($this->db)->rollback_transaction();
						throw new errorStock(loc::mt(ATF::$usr->trans("stock_indisponible_pour_la_ref"),array("ref"=>$ligne_bdc["ref"])),9);
					}
				}
			} else {
				// Le stock est commandé, mais pas encore reçu
				$stock=array();
				$stock["id_bon_de_commande_ligne"] = $id_bon_de_commande_ligne;
				$stock["id_affaire"]=$infos["id_affaire"];
				$stock["libelle"] = $ligne_bdc["produit"];
				$stock["ref"]=$ligne_bdc["ref"];
				$stock["prix_achat"]=$ligne_bdc["prix_achat"];
				$stock["prix"]=$ligne_bdc["prix"];
				$stock["etat"]="reception";
				$stock["redirection_custom"]=true;
				$stock["date_achat"]=$infos["date_fin"];
				
				//unset($item["id_produit"],$item["id_produit_fk"],$item["id_fournisseur"],$item["id_fournisseur_fk"],$item["id_compte_absystech_fk"],$item["marge"],$item["id_compte_absystech"],$item["marge_absolue"],$item["prix_achat"],$item["serial"]);
				
				/*---------------------- Insertion STOCK   ----------------------*/
				if(!$item["quantite"]){
					$item["quantite"]=0;
				}else{
					for($m=1;$m<=$item["quantite"];$m++){
						ATF::stock()->insert($stock,$s);	
					}
				}
			}
		}
		//Redirection vers l'affaire
		if(is_array($cadre_refreshed)){
			ATF::affaire()->redirection("select",$infos["id_affaire"]);
		}		
		//Fin transaction
        //ATF::db($this->db)->rollback_transaction();       
        ATF::db($this->db)->commit_transaction();       
		//Insertion bon de commande
		return $last_id;
	}
	
	/**
	* Surcharge de l'update
	* @author Mouad EL HIZABRI
	*/
	public function update($infos,&$s,$files=NULL,&$cadre_refreshed){
		$this->infoCollapse($infos);
		unset($infos["sous_total"],$infos["marge"],$infos["marge_absolue"],$infos["prix_achat"]);	
		return parent::update($infos,$s,$files,$cadre_refreshed);		
	}
	
	/**
    * Retourne la valeur par défaut spécifique aux données passées en paramètres
    * @author Mouad EL HIZABRI
	* @param string $field
	* @return string
    */  
	public function default_value($field){
		if(ATF::_r('id_devis')){
			$infos=ATF::devis()->select(ATF::_r('id_devis'));
		}elseif(ATF::_r('id_commande')){
			$infos=ATF::commande()->select(ATF::_r('id_commande'));
		}
		switch ($field) {
			case "id_societe":
				return $infos["id_societe"];
			case "resume":
				return $infos["resume"];
			case "prix":
				return $infos["prix"];
			case "frais_de_port":
				return $infos["frais_de_port"];
			case "sous_total":
				return $infos["prix"]-$infos["frais_de_port"];
			case "prix_achat":
				return $infos["prix_achat"];
			case "marge":
				return round((($infos["prix"]-$infos["prix_achat"])/$infos["prix"])*100,2)."%";
			case "marge_absolue":
				return ($infos["prix"]-$infos["frais_de_port"])-$infos["prix_achat"];
			default:
				return parent::default_value($field);
		}
	}
	
	/**
	* Donne l'url du fournisseur
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param string $ref
	*/
	public function getProviderUrl($id_bon_de_commande){
		$fournisseur=$this->select($id_bon_de_commande);
		switch($fournisseur["id_fournisseur"]){
			case 1367:
				return "http://www.etc-dist.fr";
			case 636:
				return "http://www.techdata.fr";
				//return "http://www.materiel.net/client/commande.html?ref=".$fournisseur["ref"];
			default :
				return false;
		}
	}
	
	/**
	* Place la commande en état terminé
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr> MOUAD EL HIZABRI
	*/
	public function setCompleted($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL){
		$id_bdc=$this->decryptId($infos["id_bon_de_commande"]);
		if ($infos["date_reception"]) $infos["date_reception"] = $infos["date_reception"].":".date("s");
		ATF::db()->begin_transaction();
		if(!$id_bdc || !is_numeric($id_bdc)) {
			ATF::db()->rollback_transaction();
			throw new errorATF(ATF::$usr->trans("error_setCompleted"));
		}
		
		//Traitement des lignes de bon de commande
		$bdc_lignes = ATF::bon_de_commande_ligne()->ss("id_bon_de_commande",$id_bdc);
		//Traitement des lignes de bon_de_commande
		foreach($bdc_lignes as $ligne){			
			ATF::bon_de_commande_ligne()->update(array("id_bon_de_commande_ligne"=>$ligne["id_bon_de_commande_ligne"],"etat"=>"recu"));
			
			$stocks = ATF::stock()->ss("id_bon_de_commande_ligne",$ligne["id_bon_de_commande_ligne"]);
			//Traitement des stocks
			foreach($stocks as $stock){
				//log::logger($stock["stock.id_stock"],"melhizabri");
				if($stock["etat"]=="reception"){
					ATF::stock()->setReceived(array("id_stock"=>$stock["stock.id_stock"],"date_reception"=>$infos["date_reception"]));
				}
			}
		}
		
		$this->update(array("id_bon_de_commande"=>$id_bdc,"etat"=>"recu","date_fin"=>$infos["date_reception"]));
		
		//Redirection
		if(is_array($cadre_refreshed)){
			$id_affaire=$this->select($id_bdc,"id_affaire");
			$this->redirection('select_all_optimized',"gsa_affaire_bon_de_commande_".$id_affaire);
			ATF::stock()->redirection('select_all_optimized',"gsa_affaire_stock_".$id_affaire);
		}
		ATF::db()->commit_transaction();
		return true;
	}
	
    /**
	* Surcharge du delete 
	* @author MOUAD EL HIZABRI
    */	
	public function delete($infos,&$s,$files=NULL,&$cadre_refreshed){
		$last_id= array();
		//Nouvelle transaction
		ATF::db($this->db)->begin_transaction();
		// Multi suppression
		foreach($infos["id"] as $cle=>$valeur){
			$info_id =$valeur;
			if (is_numeric($info_id) || is_string($info_id)) {			
				$id=$this->decryptId($info_id);
				//Recherche des lignes
				$bdc_lignes = ATF::bon_de_commande_ligne()->ss("id_bon_de_commande",$id);
				foreach($bdc_lignes as $ligne){
					//Recherche des stocks
					$stock_obj = new stock();
					$stocks = $stock_obj->ss("id_bon_de_commande_ligne",$ligne["id_bon_de_commande_ligne"]);
					foreach($stocks as $stock){
						$stock_obj->delete(array("id"=>array($stock["id_stock"])));
					}
				}
			}
			//Sauvegarde de l'affaire pour le raffraichissement
			if(!$id_affaire){
				$id_affaire=$this->select($id,"id_affaire");
			}
			//Suppression du bon de commande
			parent::delete($id);
		}
		//Fin transaction
		ATF::db($this->db)->commit_transaction();
		//redirection vers l'affaire
		if(is_array($cadre_refreshed)){
			$this->redirection('select_all_optimized',"gsa_affaire_bon_de_commande_".$id_affaire);
			ATF::stock()->redirection('select_all_optimized',"gsa_affaire_stock_".$id_affaire);
		}
		return true;
	} 
	
	/**
	* Condition du filtrage
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr> MOUAD EL HIZABRI
	* @param string classes_optima $class Classe des enregistrements affichés dans l'autocomplète
	* @param array $infos ($requests habituellement attendu)
	*	int $infos[id_affaire]
	*	int $infos[id_societe]
	* @param string $condition_field
	* @param string $condition_value
	* @param string $field Champ d'origine
	* @return array Conditions de filtrage
	*/
	public function autocompleteConditions(classes_optima $class,$infos,$condition_field=NULL,$condition_value=NULL,$field=NULL) {
		$this->infoCollapse($infos);
		switch ($field) {
			case "id_fournisseur":
				if ($infos["id_commande"]) {
					// On propose seulement les sociétés qui sont dans la commande
					$conditions["condition_field"][] = "commande_ligne.id_commande";
					$conditions["condition_value"][] = $infos["id_commande"];
				}
				break;
		}
		return array_merge_recursive((array)($conditions),parent::autocompleteConditions($class,$infos,$condition_field,$condition_value));
	}

	/* a voir */
	
	/** 
	* possiblilité de mise à jour
	* @author Mouad ELHIZABRI
	* @return true si la modification est possible
	*/
	/*public function can_update($id,$field){
		if($this->select($id,"etat")=="en_cours"){
			return false; 
		}else{
			return true;
		}
	}

	/** 
	* possiblilité de suppression
	* @author Mouad ELHIZABRI
	* @return true si la suppression est possible
	*/
	/*public function can_delete($id){
		return $this->can_update($id);
	}*/	
};

class bon_de_commande_att extends bon_de_commande_absystech { };
class bon_de_commande_wapp6 extends bon_de_commande_absystech { };
class bon_de_commande_demo extends bon_de_commande_absystech { };
?>