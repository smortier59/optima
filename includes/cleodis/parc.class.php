<?
/** Classe parc
* @package Optima
* @subpackage Cleodis
*/
class parc_cleodis extends classes_optima {
	function __construct() {
		parent::__construct();
		$this->table = "parc";
		$this->colonnes['fields_column']  = array('parc.ref','parc.libelle','parc.id_societe','parc.date_garantie'=> array("renderer"=>"updateDate"),'parc.serial','parc.etat','parc.existence','parc.provenance', "parc.date_achat" => array("renderer"=>"updateDate"));

		$this->colonnes['bloquees']['insert'] =
		$this->colonnes['bloquees']['update'] =array("serial","id_societe","id_produit","id_affaire","ref","libelle","divers","etat","code","date","date_inactif","date_garantie","provenance","existence");
		$this->colonnes['panel']['lignes'] = array(
			"produits"=>array("custom"=>true)
		);
		$this->panels['lignes'] = array('nbCols'=>1,'visible'=>true);
		$this->fieldstructure();
		$this->field_nom = "libelle";
		$this->foreign_key["provenance"] = "affaire";
		$this->foreign_key["id_fournisseur"] = "societe";
		$this->addPrivilege("updateDate_garantie");
		$this->no_delete = true;
		$this->no_insert = true;
		$this->no_update = true;
	}

	public function updateExistenzOriginale($commande,$affaire,$affaire_parente=NULL,$affaires_parentes=NULL){

		ATF::parc()->q->reset()->addCondition("id_affaire",$affaire->get('id_affaire'));
		$parc=ATF::parc()->sa();
		//Pour tous les parcs de l'affaire
		foreach($parc as $key=>$item){
			if($commande->get('etat')=="arreter"){
				if($item["existence"]=="actif"){
					ATF::parc()->u(array("id_parc"=>$item["id_parc"],"existence"=>"inactif","date_inactif"=>date("Y-m-d")));

					$this->q->reset()->addCondition("etat","broke")
									 ->addCondition("serial",$item["serial"])
									 ->addCondition("id_affaire",$item["id_affaire"])
									 ->setDimension("row");

					if($brokeExiste=$this->sa()){
						ATF::parc()->u(array("id_parc"=>$brokeExiste["id_parc"],"existence"=>"actif","date_inactif"=>NULL));
					}else{
						$new_parc=$item;
						unset($new_parc["id_parc"]);
						$new_parc["date_inactif"]=NULL;
						$new_parc["etat"]="broke";
						ATF::parc()->i($new_parc);
					}
				}
			}else{

				//Le parc de l'affaire doit passer en actif si affaire/fille==mis_loyer || prolongation
				if($commande->get('etat')=="mis_loyer" || $commande->get('etat')=="prolongation" || $commande->get('etat')=="vente"){
					//Si c'est un broke et que ce n'est pas un avenant alors c'est que le broke provient de l'affaire fille qui ne reprend pas le parc mais si la parente est active alors c'est que le parc doit être inactif
					if($item["etat"]=="broke" && $affaire->get('nature')!="avenant"){
						if($item["existence"]!="inactif"){
							ATF::parc()->u(array("id_parc"=>$item["id_parc"],"existence"=>"inactif"));
						}
					}else{
						if($item["existence"]!="actif" || $item["date_inactif"]){
							ATF::parc()->u(array("id_parc"=>$item["id_parc"],"existence"=>"actif","date_inactif"=>NULL));
						}
					}
				}elseif($commande->get('etat')=="non_loyer"){
					if($item["existence"]!="inactif"){
						ATF::parc()->u(array("id_parc"=>$item["id_parc"],"existence"=>"inactif"));
					}
				}

				if($affaire->get('nature')=="avenant" || $affaire->get('nature')=="vente"){
					if($commande->get('etat')=="mis_loyer" || $commande->get('etat')=="prolongation" || $commande->get('etat')=="vente"){
						//Si un parc passe en actif alors les autres parcs du même serial doivent passer en inactif
						ATF::parc()->q->reset()->addCondition("serial",$item["serial"])
											   ->addCondition("id_parc",$item["id_parc"],false,false,"!=");
						$parc_serial=ATF::parc()->sa();
						if($parc_serial){
							foreach($parc_serial as $k=>$i){
								if($i["id_parc"]!=$item["id_parc"]){
									if($i["existence"]!="inactif"  || $i["date_inactif"]!= $commande->get('date_debut')){
										//Tous les parcs dupliqués doivent passer en inactif si affaire/fille==mis_loyer || prolongation sauf si c'est le parc de l'affaire fille !!!
										ATF::parc()->u(array("id_parc"=>$i["id_parc"],"existence"=>"inactif","date_inactif"=>$commande->get('date_debut')));
									}
								}
							}
						}
					}elseif($commande->get('etat')=="non_loyer"){
						//Sinon le parc de l'affaire parente repasse en actif
						if($affaire_parente) {
							ATF::parc()->q->reset()->addCondition("id_affaire",$affaire_parente->get("id_affaire"))
												   ->addCondition("serial",$item["serial"])
												   ->setDimension("row");
							$parc_serial=ATF::parc()->sa();
							if($parc_serial){
								if($parc_serial["existence"]!="actif"  || $parc_serial["date_inactif"]){
									ATF::parc()->u(array("id_parc"=>$parc_serial["id_parc"],"existence"=>"actif","date_inactif"=>NULL));
								}
							}
						}
					}
				}
			}
			//Dans le cas d'un AR tous les parcs passent en inactif sauf ceux abroker qui passent en actif
			if($affaire->get('nature')=="AR"){
				if ($affaires_parentes) {
					foreach ($affaires_parentes as $affaire_parente) {
						$commande_parent = $affaire_parente->getCommande();
						if($commande_parent && $commande_parent->get("etat")!="arreter"){
							ATF::parc()->q->reset()->addCondition("id_affaire",$affaire_parente->get("id_affaire"));
							$parc_parent=ATF::parc()->sa();
							foreach($parc_parent as $key=>$item) {
								//Si l'etat de la commande est mis_loyer/prolongation alors tous les parcs passent en inactif sauf ceux à broker
								if($commande->get('etat')=="mis_loyer" || $commande->get('etat')=="prolongation"){
									if($item["etat"]=="broke"){
										if($item["existence"]!="actif"  || $item["date_inactif"]){
											ATF::parc()->u(array("id_parc"=>$item["id_parc"],"existence"=>"actif","date_inactif"=>NULL));
										}
									}else{
										if($item["existence"]!="inactif"  || $item["date_inactif"]!= $commande->get('date_debut')){
											ATF::parc()->u(array("id_parc"=>$item["id_parc"],"existence"=>"inactif","date_inactif"=>$commande->get('date_debut')));
										}
									}
								}elseif($commande->get('etat')=="non_loyer"){
								//Si l'etat de la commande n'est pas mis_loyer/prolongation alors tous les parcs passent en actif sauf ceux à broker
									if($item["etat"]=="broke"){
										if($item["existence"]!="inactif"  || $item["date_inactif"]!= $commande->get('date_debut')){
											ATF::parc()->u(array("id_parc"=>$item["id_parc"],"existence"=>"inactif"));
										}
									}else{
										if($item["existence"]!="actif"  || $item["date_inactif"]){
											ATF::parc()->u(array("id_parc"=>$item["id_parc"],"existence"=>"actif","date_inactif"=>NULL));
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}

	/* Routine de prévention pour les doublons de parcs actifs => Si on en trouve, on ne garde que le parc le plus récent
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/
	public function preventionDoublonsActifs($id_affaire){
		$this->q->reset()
			->where("id_affaire",$id_affaire)
			->where("existence","actif");
		if ($parcs = $this->sa()) {
			// Pour tous les parcs de l'affaire
			foreach($parcs as $parc){
				// On check si un autre parc avec ce serial est en état actif dans toute la base de données
				$this->q->reset()
					->where("serial",$parc["serial"])
					->where("existence","actif")
					->setStrict()
					->addOrder("id_affaire","desc")
					->addOrder("id_parc","desc");
				if ($autresParcsDeMemeSerial = $this->sa()) {
					foreach($autresParcsDeMemeSerial as $k => $parcDeMemeSerial){
						if (!$k) {
							//log::logger($k."[".$parc["serial"]." | ".$parc["id_affaire"]." | ".$parcDeMemeSerial["id_parc"]."] on ne garde que l'etat actif '".$parcDeMemeSerial["etat"]."'","preventionDoublonsActifs");
						} else {
							$this->update(array("id_parc"=>$parcDeMemeSerial["id_parc"],"existence"=>"inactif","date_inactif"=>date("Y-m-d")));

							// Log
							$s = $k."[".$parc["serial"]." | ".$parc["id_affaire"]." | ".$parcDeMemeSerial["id_parc"]."] on passe en inactif l'état '".$parcDeMemeSerial["etat"]."'";
							//mail("ygautheron@absystech.fr","Cleodis preventionDoublonsActifs ".$parc["serial"],$s);
							log::logger($s,"preventionDoublonsActifs");
						}
					}
				}
			}
		}
	}

	/**
    * Méthode qui met à jour l'activité des parcs
    * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
    */
	public function updateExistenz($commande,$affaire,$affaire_parente=NULL,$affaires_parentes=NULL){
		$this->updateExistenzOriginale($commande,$affaire,$affaire_parente,$affaires_parentes);
		$this->preventionDoublonsActifs($affaire->get('id_affaire'));
	}

	/**
	* select_all qui permet de n'avoir que les parcs existant
    * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	*/
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){
		$this->q
			->addJointure("parc","id_affaire", "affaire", "id_affaire")
			->addJointure("affaire", "id_affaire", "commande","id_affaire")
			->addJointure("parc", "id_produit", "produit","id_produit")
			->addJointure("parc", "id_societe", "societe","id_societe")
			->addOrder("existence","ASC");
		return parent::select_all($order_by,$asc,$page,$count);
	}


	/**
	* Insertion du parc
    * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $item
	* @param array $commande_ligne
	*/
	public function insertParcSerial($item,$commande_ligne){

		$type=ATF::produit()->select($item["id_produit"],"type");

		if(!$item["serial"]){
			ATF::db($this->db)->rollback_transaction();
			throw new errorATF("Il faut un serial pour le produit ".$item["produit"],883);
		}

		//Parcs, insertion des parcs uniquement s'ils ne proviennent pas d'une affaire (car déjà présent)
		$affaire=ATF::affaire()->select(ATF::commande()->select($commande_ligne["id_commande"],"id_affaire"));


		if(!$affaire["date_garantie"]){
			ATF::db($this->db)->rollback_transaction();
			throw new errorATF("Il n'y a pas de date de garantie pour cette affaire",880);
		}

		/*Le serial ne doit pas déjà exister*/
		$this->q->reset()->addCondition("serial",$item["serial"])->setCount();
		$countParc=$this->sa();
		if($countParc["count"]>0){
			ATF::db($this->db)->rollback_transaction();
			throw new errorATF("Le serial ".$item["serial"]." n'est pas valide car il est déjà utilisé par le parc '".$countParc["data"][0]["libelle"]."' de l'affaire '".ATF::affaire()->nom($countParc["data"][0]["id_affaire"])."'",881);
		}



		$serial["id_societe"]=$affaire["id_societe"];
		$serial["id_affaire"]=$affaire["id_affaire"];
		if($affaire["nature"]=="vente"){
			$serial["etat"]="vendu";
		}else{
			$serial["etat"]="loue";
		}


		$serial["date_garantie"]=$affaire["date_garantie"];
		$serial["serial"]=$item["serial"];
		$serial["libelle"]=$item["produit"];
		$serial["ref"]=$item["ref"];
		$serial["caracteristique"]=$item["caracteristique"];

		$serial["id_produit"]=$item["id_produit"];
		$serial["existence"]="actif";



		// On met à jour le serial de contrat ssi il n'est pas encore présent dedans
		if (!(strpos($commande_ligne["serial"], $item["serial"]) !== false)) {
			$commande_ligne["serial"].=" ".$item["serial"];
			ATF::commande_ligne()->u($commande_ligne);
		}
		/*
		if($this->parcSerialIsActif($serial["serial"])){
			throw new errorATF("Impossible d'insérer ce parc car un parc ACTIF existe déjà avec ce même serial. (serial=".$serial["serial"].")",347);
		}
		 * Ne sert pas, car on teste deja plus haut qu'aucun autre parc existe deja avec ce serial ! */

		return $this->i($serial,$s);
	}

	public function getParcPartenaire($id_affaire){
		/*Si contrat pas encore démarré et qu'on a une facture sur la periode actuelle
				Afficher les parcs meme ceux qui sont inactif
		Sinon faire comme actuellement*/
		$parc = NULL;

		ATF::commande()->q->reset()->where("affaire.id_affaire", $id_affaire);
		$commande = ATF::commande()->select_row();

		if($commande["etat"] == "non_loyer"){
			ATF::facture()->q->reset()->addField('loyer.loyer')
									  ->where("facture.date_periode_debut",date("Y-m-d"),"AND","cond_date","<=")
									  ->where("facture.date_periode_fin",date("Y-m-d"),"AND","cond_date",">=")
									  ->where("facture.id_affaire", $id_affaire);
			if(ATF::facture()->select_all()){
				$this->q->reset()->addCondition("parc.id_affaire",$id_affaire)->addOrder("parc.id_parc","asc");
				$parc = $this->sa();
			}
		}else{
			$this->q->reset()->addCondition("parc.id_affaire",$id_affaire)
							   ->addCondition("parc.existence","inactif","AND",NULL,"!=")
							   ->addCondition("parc.etat","broke","AND",1,"!=")
							   ->addOrder("parc.id_parc","asc");
			$parc = $this->sa();
		}
		return $parc;
	}

	/**
	* Retourne un id_parc actif du serial demandé
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $serial
	* @return int $id_parc
	*/
	public function getParcActifFromSerial($serial){
		$this->q->reset()
			->addField("id_parc")
			->where("existence","actif")
			->where("serial",$serial)
			->setDimension("cell");
		return $this->select_all();
	}

	/**
	* Retourne VRAI si un serial de parc  est déjà actif
  * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $serial
	* @return boolean
	*/
	public function parcSerialIsActif($serial){
		return !!$this->getParcActifFromSerial($serial);
	}

	/**
	* Insertion des parcs d'un bon de commande
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	*/
	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){

		$infos_ligne = json_decode($infos["values_".$this->table]["produits"],true);

		$this->infoCollapse($infos);

		$id_affaire=ATF::bon_de_commande()->select($infos["id_bon_de_commande"],"id_affaire");

		//Lignes
		if($infos_ligne){
			ATF::db($this->db)->begin_transaction();
			foreach($infos_ligne as $key=>$item){
				foreach($item as $k=>$i){
					$k_unescape=util::extJSUnescapeDot($k);
					$item[str_replace("parc.","",$k_unescape)]=$i;
					unset($item[$k]);
				}
				$item["id_bon_de_commande_ligne"]=$item["id_parc"];
				$item["index"]=util::extJSEscapeDot($key);
				$bon_de_commande_ligne=ATF::bon_de_commande_ligne()->select($item["id_bon_de_commande_ligne"]);
				$item['caracteristique'] = $bon_de_commande_ligne['caracteristique'];
				$commande_ligne=ATF::commande_ligne()->select($bon_de_commande_ligne["id_commande_ligne"]);
				$item["id_produit"]=ATF::commande_ligne()->select($bon_de_commande_ligne["id_commande_ligne"],"id_produit");
				unset($item["id_facture_fournisseur_ligne"]);
				$this->insertParcSerial($item,$commande_ligne);
			}
			ATF::db($this->db)->commit_transaction();
		}else{
			throw new errorATF("Il n'y a pas de produit !",877);
		}
		ATF::affaire()->redirection("select",$id_affaire);
		return true;
	}

	/**
	* Permet de savoir si tous les produits d'un Bdc sont insérés dans le parc
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param int $id_bon_de_commande
	* @return boolean
	*/
	function parcByBdc($id_bon_de_commande){
		ATF::bon_de_commande_ligne()->q->reset()->addCondition("id_bon_de_commande",$id_bon_de_commande);
		$parc=ATF::bon_de_commande_ligne()->toParcInsert();

		if($parc["count"]>0){
			return true;
		}else{
			return false;
		}
	}

	/**
	* Permet de savoir si tous les produits d'une affaire sont insérés dans le parc
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param int $id_bon_de_commande
	* @return boolean
	*/
	function parcByAffaire($id_affaire){
		ATF::bon_de_commande()->q->reset()->addCondition("id_affaire",$id_affaire);
		$bon_de_commande=ATF::bon_de_commande()->sa();
		foreach($bon_de_commande as $key=>$item){
			if($this->parcByBdc($item["id_bon_de_commande"])){
				return true;
			}
		}
		return false;
	}

	/**
	* Permet de modifier la date sur un select_all
	* @param array $infos id_table, key (nom du champs à modifier),value (nom du champs à modifier)
	* @return boolean
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	function updateDate($infos){

		if($infos["key"] == "date_achat"){
			$id_affaire = $this->select($this->decryptId($infos["id_parc"]) , "id_affaire");
			ATF::loyer()->q->reset()->where("id_affaire" , $id_affaire);
			$loyer = ATF::loyer()->select_row();

			$freq = "";
			if($loyer["frequence_loyer"] == "mois"){
				$freq = $loyer["duree"]." month";
			}elseif ($loyer["frequence_loyer"] == "trimestre") {
				$loyer["duree"] = $loyer["duree"]*3;
				$freq = $loyer["duree"]." month";
			}elseif ($loyer["frequence_loyer"] == "semestre") {
				$loyer["duree"] = $loyer["duree"]*6;
				$freq = $loyer["duree"]." month";
			}else{
				$freq = $loyer["duree"]." year";
			}
			$date = explode("-", $infos["value"]);
			$date = date("Y-m-d" , strtotime($date["2"]."-".$date["1"]."-".$date["0"]));
			$date = strtotime("+".$freq, strtotime($date));
			$dateGarantie = date("Y-m-d" , $date);
			$this->updateDate(array("id_parc" => $infos["id_parc"], "key" => "date_garantie"  , "value" => $dateGarantie));
		}
		if ($infos['value'] == "undefined") $infos["value"] = "";
		$infos["key"]=str_replace($this->table.".",NULL,$infos["key"]);
		$infosMaj["id_".$this->table]=$infos["id_".$this->table];
		$infosMaj[$infos["key"]]=$infos["value"];

		if($this->u($infosMaj)){
			ATF::$msg->addNotice(
				loc::mt(ATF::$usr->trans("notice_update_success_date"),array("record"=>$this->nom($infosMaj["id_".$this->table]),"date"=>$infos["key"]))
				,ATF::$usr->trans("notice_success_title")
			);
			return true;
		}else{
			return false;
		}
	}

};

class parc_cleodisbe extends parc_cleodis { };
class parc_itrenting extends parc_cleodis { };
class parc_bdomplus extends parc_cleodis { };

class parc_boulanger extends parc_cleodis { };
class parc_assets extends parc_cleodis { };
class parc_go_abonnement extends parc_cleodis {
	function __construct() {
		parent::__construct();
		$this->table = "parc";
		$this->colonnes['fields_column']["parc.immatriculation"] = array("width"=>150,"rowEditor"=>"setImmatriculation");
		$this->colonnes['fields_column']["parc.kilometrage"] = array("width"=>150,"rowEditor"=>"setkilometrage");
		$this->colonnes['fields_column']["parc.date_premiere_mise_en_circulation"] = array("renderer"=>"updateDate");
		$this->colonnes['fields_column']["parc.puissance"] = array("width"=>150,"rowEditor"=>"setPuissance");
		$this->colonnes['fields_column']["parc.type_energie"] = array("renderer"=>"setType_energie","width"=>200);




		$this->fieldstructure();

		$this->addPrivilege("setImmatriculation");
		$this->addPrivilege("setkilometrage");
		$this->addPrivilege("setdate_premiere_mise_en_circulation");
		$this->addPrivilege("setPuissance");
		$this->addPrivilege("setType_energie");
	}

	public function setImmatriculation($infos) {
		$res = $this->update(
			array(
				"id_parc"=> $this->decryptId($infos["id_parc"]),
				"immatriculation" => strtoupper($infos["immatriculation"])
			)
		);
		if($res){
			ATF::$msg->addNotice(
				loc::mt(ATF::$usr->trans("notice_update_success"))
				,ATF::$usr->trans("notice_success_title")
			);
		}
	}


	public function setkilometrage($infos) {
		$res = $this->update(
			array(
				"id_parc"=> $this->decryptId($infos["id_parc"]),
				"kilometrage" => $infos["kilometrage"]
			)
		);
		if($res){
			ATF::$msg->addNotice(
				loc::mt(ATF::$usr->trans("notice_update_success"))
				,ATF::$usr->trans("notice_success_title")
			);
		}
	}


	public function setdate_premiere_mise_en_circulation($infos) {
		$res = $this->update(
			array(
				"id_parc"=> $this->decryptId($infos["id_parc"]),
				"date_premiere_mise_en_circulation" => $infos["date_premiere_mise_en_circulation"]
			)
		);
		if($res){
			ATF::$msg->addNotice(
				loc::mt(ATF::$usr->trans("notice_update_success"))
				,ATF::$usr->trans("notice_success_title")
			);
		}
	}


	public function setPuissance($infos) {
		$res = $this->update(
			array(
				"id_parc"=> $this->decryptId($infos["id_parc"]),
				"puissance" => $infos["puissance"]
			)
		);
		if($res){
			ATF::$msg->addNotice(
				loc::mt(ATF::$usr->trans("notice_update_success"))
				,ATF::$usr->trans("notice_success_title")
			);
		}
	}

	public function setType_energie($infos) {
		$res = $this->update(
			array(
				"id_parc"=> $this->decryptId($infos["id_parc"]),
				"type_energie" => $infos["value"]
			)
		);
		if($res){
			ATF::$msg->addNotice(
				loc::mt(ATF::$usr->trans("notice_update_success"))
				,ATF::$usr->trans("notice_success_title")
			);
		}
	}



};


class parc_midas extends parc_cleodis {
	function __construct() {
		parent::__construct();
		$this->table = "parc";
		$this->colonnes['fields_column']  = array('parc.ref'
												,'parc.libelle'
												,'parc.id_societe'
												,'parc.date_garantie'=>array("updateDate"=>true)
												,'parc.serial'
												,'parc.etat');

		$this->filtre_ob['parc_franc']=array("titre"=>"Franchisées","function"=>"selectAllFranch");
		$this->filtre_ob['parc_franc_cours']=array("titre"=>"Franchisées en cours","function"=>"selectAllFranchCours");
		$this->filtre_ob['parc_franc_cours_info']=array("titre"=>"Franchisées en cours informatique","function"=>"selectAllFranchCoursInfo");

		$this->filtre_ob['parc_suc']=array("titre"=>"Succursales","function"=>"selectAllSuc");
		$this->filtre_ob['parc_suc_cours']=array("titre"=>"Succursales en cours","function"=>"selectAllSucCours");
		$this->filtre_ob['parc_suc_cours_info']=array("titre"=>"Succursales en cours informatique","function"=>"selectAllSucCoursInfo");

		$this->fieldstructure();
	}
	/** On affiche que les sociétés midas et que les parcs actifs
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false) {
		$this->q->addJointure("parc","id_societe","societe","id_societe")
				->addCondition("societe.code_client","M%","OR",false,"LIKE")
				->addCondition("societe.divers_3","Midas")
				->addCondition("parc.existence","actif");
		return parent::select_all($order_by,$asc,$page,$count);
	}

	/** Surcharge pour filtrer le select_all si clic sur le filtre
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function selectAllFranch(){
		$this->q->addConditionNull("societe.id_filiale");
		return $this->select_all();
	}

	/** Surcharge pour filtrer le select_all si clic sur le filtre
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function selectAllFranchCours(){
		$this->q->addJointure("parc","id_affaire","affaire","id_affaire")
				->addJointure("affaire","id_affaire","commande","id_affaire")
				->addCondition("commande.etat","prolongation","OR")->addCondition("commande.etat","mis_loyer","OR");
		return $this->selectAllFranch();
	}

	/** Surcharge pour filtrer le select_all si clic sur le filtre
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function selectAllFranchCoursInfo(){
		$this->q->addCondition("libelle","%HP%","OR",false,"LIKE")
				->addCondition("libelle","%NEC%","OR",false,"LIKE")
				->addCondition("libelle","%Brother%","OR",false,"LIKE");
		return $this->selectAllFranchCours();
	}

	/** Surcharge pour filtrer le select_all si clic sur le filtre
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function selectAllSuc(){
		$this->q->addConditionNotNull("societe.id_filiale");
		return $this->select_all();
	}

	/** Surcharge pour filtrer le select_all si clic sur le filtre
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function selectAllSucCours(){
		$this->q->addJointure("parc","id_affaire","affaire","id_affaire")
				->addJointure("affaire","id_affaire","commande","id_affaire")
				->addCondition("commande.etat","prolongation","OR")->addCondition("commande.etat","mis_loyer","OR");
		return $this->selectAllSuc();
	}

	/** Surcharge pour filtrer le select_all si clic sur le filtre
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function selectAllSucCoursInfo(){
		$this->q->addCondition("libelle","%HP%","OR",false,"LIKE")
				->addCondition("libelle","%NEC%","OR",false,"LIKE")
				->addCondition("libelle","%Brother%","OR",false,"LIKE");
		return $this->selectAllSucCours();
	}

};
