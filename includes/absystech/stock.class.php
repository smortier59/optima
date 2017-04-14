<?
/** 
* Classe stock
* @author MOUAD EL HIZABRI
* @package Optima
* @subpackage Absystech
*/
require_once dirname(__FILE__)."/../stock.class.php";
class stock_absystech extends stock {

	// Mapping prévu pour un autocomplete sur les stock
	public static $autocompleteMapping = array(
		array("name"=>'nom', "mapping"=>"stock"),
		array("name"=>'detail', "mapping"=>"serial"),
		array("name"=>'serial', "mapping"=>"serial"),
		array("name"=>'serialAT', "mapping"=>"serialAT"),
		array("name"=>'ref', "mapping"=>"ref"),
		array("name"=>'id_stock', "mapping"=>"id_stock")
	);

	/**
	* Constructeur
	*/
	public function __construct() {
		parent::__construct();
		$this->table = "stock";
		$this->colonnes['fields_column']  = array(
			'stock.ref'=>array("width"=>100,"align"=>"center","rowEditor"=>"setInfos")
			,'stock.libelle'=>array("width"=>100,"align"=>"left","rowEditor"=>"setInfos")
			,'stock.serial'=>array("width"=>150,"rowEditor"=>"setInfos")
			,'stock.serialAT'=>array("width"=>150,"rowEditor"=>"setInfos")
			,'stock.adresse_mac'=>array("width"=>150,"rowEditor"=>"setInfos")
			,'etat'=>array("custom"=>true,"width"=>30,"align"=>"center","renderer"=>"etat")
			,'affaire.id_societe'=>array()
			,'stock.prix'=>array("rowEditor"=>"setInfos")
			,'stock.prix_achat'=>array("rowEditor"=>"setInfos")
			,'stock.id_affaire'
			,'stock.date_achat'
			,'actions'=>array("custom"=>true,"align"=>"center","nosort"=>true,"renderer"=>"actionsStock","width"=>200)
			,'stock.categories_magento'
			,'stock.to_magento'
		);

		$this->colonnes['primary'] = array(
			"libelle"
			,"serial"
			,"ref"
			,"serialAT"
			,"etat"=>array("custom"=>true,"xtype"=>"combo","data"=>array("stock","immo"),"default"=>"stock")
			,"adresse_mac"
			,"prix"
			,"prix_achat"
			,"id_affaire"
			,"date_achat"
			,"date_fin_immo"
			//,"date"=>array("custom"=>true,"xtype"=>"datefield")
			// Commenté car pour moi ça ne veut rien dire... déjà hidden n'est pas un xtype, et le default a true... est ce paske c'est une checkbox solo ? Je n'ai pas le temps de regarder là donc je commente.
//			,"formulaire"=>array("custom"=>true,"xtype"=>"hidden","default"=>true)
			,"date_achat"
			,"date_fin_immo"
			,'commentaire'=>array("custom"=> true, "null"=> true)
		);

		//Panel group
		$this->colonnes['panel']['group'] = array(
			"grouper"=>array("custom"=>true,"xtype"=>"combo","data"=>array("oui","non"),"default"=>"non")
			,"quantite"=>array("custom"=>true,"xtype"=>"numberfield","default"=>"1")
		);

		/* Clées étrangères*/
		$this->foreign_key["id_affaire"] = "affaire";

		$this->fieldstructure();
		$this->field_nom = "libelle";
		//visibilité des panels
		$this->panels['group'] = array("visible"=>true);
		//les priviléges
		$this->addPrivilege("setReceived","insert");
		$this->addPrivilege("annule","insert");
		$this->addPrivilege("setInfos","insert");
		$this->addPrivilege("setDelivered","insert");
		$this->addPrivilege("updateForMagento","update");
		$this->addPrivilege("switchStock");
		$this->addPrivilege("checkInventaire2013");


		//bloquage insertion et modification
		$this->colonnes['bloquees']['insert']  = array("id_group","id_affaire","affectation","id_bon_de_commande_ligne");
		$this->colonnes['bloquees']['update'] = array("grouper","quantite","id_group","affectation","id_bon_de_commande_ligne","etat");
		//etiquette et code barre
		$this->quick_action['select_all']['etiquetteLogo'] = array('privilege'=>'select');
		$this->quick_action['select_all']['codeBarrePDF'] = array('privilege'=>'select');
		$this->quick_action['select_all']['codeBarreATT'] = array('privilege'=>'select');
		$this->no_update_all = false;
		//les onglets
		$this->onglets = array('stock_etat');

		// Fichier
		$this->files = array(
			"photo"=>array(
				"type"=>"png",
				"convert_from"=>array("png","jpg","gif")
			)
		);
	}

	/**
	* Creation d'une notice
	* @author MOUAD EL HIZABRI
	* @param string $text le message de la nitice en question
	* @param string $titre le titre de la notice
	* @param string $stock l'id stock
	* @return notice
	*/
	public function notice_content($text,$title,$stock){
		return ATF::$msg->addNotice(
			loc::mt(ATF::$usr->trans($text),array("record"=>$stock))
			,ATF::$usr->trans($title)
	   );
	}

	/**
	* Mise à jour d'etat du stock après réception de la commande fournisseur
	* @author MOUAD EL HIZABRI
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @throws error
	* @param array $infos
	* @return etat "stocké"
	*/
	public function setReceived($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL){
		$id_stock = $this->decryptId($infos["id_stock"]);

		//Protection id_stock
		if(!$id_stock || !is_numeric($id_stock)){
			//erreur absence d'identifiant stock 
			throw new errorStock(ATF::$usr->trans("erreur_identifiant_stock"),900);
		}
		
		// Etat identique
		if (ATF::stock_etat()->getEtat($id_stock)=="stock") {
			throw new errorStock(ATF::$usr->trans("etat_identique"),901);
		}
		
		//si le serial est renseigné a ce moment on peut
		//modifire l'etat du stock en "stocké"
		$data=array("id_stock"=>$id_stock,"etat"=>"stock");
		if($infos["date_reception"]) $data["date"]=$infos["date_reception"];
		$id_last=ATF::stock_etat()->insert($data);			
		$this->notice_content("notice_etat_stock_recu","notice_success_title",$id_stock);

		return $id_last;
	}
			
	/**
	* Mise à jour d'etat du stock après une livraison
	* @author MOUAD EL HIZABRI
	* @return etat "livré"
	*/
	public function setDelivered($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL){	
		$id_stock = $this->decryptId($infos['id_stock']);
		
		// Etat identique
		if (ATF::stock_etat()->getEtat($id_stock)=="livr") {
			throw new errorStock(ATF::$usr->trans("etat_identique"),901);
		}
		
		//Nouvelle transaction
		ATF::db($this->db)->begin_transaction();
		
		$id_affaire=$this->select($id_stock,"id_affaire");
		$infos['etat']="livr";

		//nouveau etat stock "livré"
		$id_last=ATF::stock_etat()->insert($infos,$s);
		
		//--------mise à jour de livraison ligne---------//
		$ligne=ATF::livraison_ligne()->ss('id_stock',$id_stock);
		$livraison_ligne["id_livraison_ligne"]=$ligne[0]["id_livraison_ligne"];
		$livraison_ligne['etat']="termine";
		ATF::livraison_ligne()->update($livraison_ligne,$s);
		
		//--------mise à jour de la  livraison---------//
		$livraison["id_livraison"]=$ligne[0]["id_livraison"];
		//verification des etat de la livraison
		//si elle est completement terminée
		$nb_ligne=ATF::livraison_ligne()
			 ->delivery_status_verification($livraison["id_livraison"]);
		if($nb_ligne!=0){
			//si il y'a encore du stock non livré
			$livraison['etat'] = "termine_partiel";
			ATF::livraison()->u($livraison,$s);
		}else{
			//si tout le stock de la livraison est livré
			$livraison['etat'] = "termine";
			ATF::livraison()->u($livraison,$s);
		}
		//notice: livraison du stock
		$this->notice_content("notice_etat_stock_livre","notice_success_title",$id_stock);
		//--------rafraichisement des onglets---------//

		//Fin transaction
		ATF::db($this->db)->commit_transaction();
				
		return $id_last;
	}
	
	/**
	* Mise à jour du numéro de série
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
	public function setInfos($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL){
		if(!$infos['id_stock']) {
			throw new errorStock(ATF::$usr->trans("erreur_identifiant_stock"),900);
		} 
		$stock["id_stock"] = $infos["id_stock"];
		
		if (isset($infos["serial"]))
			$stock["serial"] = $infos["serial"];
			
		if (isset($infos["serialAT"]))
			$stock["serialAT"] = $infos["serialAT"];
			
		if (isset($infos["adresse_mac"]))
			$stock["adresse_mac"] = $infos["adresse_mac"];
			
		if (isset($infos["libelle"]))
			$stock["libelle"] = $infos["libelle"];
			
		if (isset($infos["ref"]))
			$stock["ref"] = $infos["ref"];
		
		if (isset($infos["prix"])) {
			if (ATF::$usr->get('id_profil')!=1) {
				throw new errorStock(loc::mt(ATF::$usr->trans("vous_avez_pas_le_droit_de_modifier_cette_information"),array("info"=>"prix","table"=>$this->table)),904);
			}
			$stock["prix"] = $infos["prix"];			
		}
		if (isset($infos["prix_achat"])) {
			if (ATF::$usr->get('id_profil')!=1) {
				throw new errorStock(loc::mt(ATF::$usr->trans("vous_avez_pas_le_droit_de_modifier_cette_information"),array("info"=>"prix_achat","table"=>$this->table)),905);
			}
			$stock["prix_achat"] = $infos["prix_achat"];			
		}
		if ($r=$this->u($stock)) {
			ATF::$msg->addNotice(loc::mt(ATF::$usr->trans("notice_update_success"),array("record"=>$this->nom($stock["id_stock"]))),ATF::$usr->trans("notice_success_title"));
		}
		return $r;
	}
		
	/**
	* Insertion d'un stock via le formulaire
	* @author EL HIZABRI MOUAD
	* @param array $infos les données a inseré
	* @return resultat insert
	*/
	public function insert_stock($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL){		
		$this->infoCollapse($infos);
		//Nouvelle transaction 
		ATF::db($this->db)->begin_transaction();
		//----------Préparation des données-----------------//
		//--------------Gestion de l'état ------------------//
		//à partir du formulaire d'insertion stock
		if($infos["etat"]){
			$stock_etat["etat"]=$infos["etat"];
			$stock_etat["commentaire"]=$infos["commentaire"];
			unset($infos["etat"] , $infos["commentaire"]);
		}
		unset($infos['formulaire']);
		unset($infos["commentaire"]);
		$redirection=$infos["redirection_custom"];
		unset($infos["redirection_custom"]);
		//--------------------------------------------------//
		//--------------ID_GROUP-----------------//
//		$id_group=$this->get_last_id_group();
//		$infos["id_group"]=$id_group+1;
		//----------------------------------------//		
		//--------------QUANTITE-----------------//
		$qte= $infos["quantite"];
		unset($infos["quantite"]);
		//--------------GROUPER-----------------//
		if($infos["grouper"]=="non"){
			//test serial
			if(empty($infos["serial"])){ 
				//erreur serial vide 
				ATF::db()->rollback_transaction();
				throw new errorStock(ATF::$usr->trans("erreur_serial_stock_vide"),906);
			}
			//test quantite
			if($qte<1){
				//erreur quantité inferieur à 1 
				ATF::db()->rollback_transaction();
				throw new errorStock(ATF::$usr->trans("erreur_quantité_stock_inferieure_a_1"),907);
			}
			unset($infos["grouper"]);
			//----------Insertion Stock--------------//
			$id=parent::insert($infos,$s);
			//---------Insertion Stock-Etat---------//
			$stock_etat["id_stock"]=$id;
			ATF::stock_etat()->insert($stock_etat,$s);
		}else{
			//test serial
			if(!empty($infos["serial"])){ 
				//erreur serial renseigner 
				ATF::db()->rollback_transaction();
				throw new errorStock(ATF::$usr->trans("erreur_serial_stock_renseigner"),908);
			}
			//test quantite
			if($qte<1){
				//erreur quantité superieur à 1 
				ATF::db()->rollback_transaction();
				throw new errorStock(ATF::$usr->trans("erreur_quantité_stock_inferieure_a_1"),907);
			}
			unset($infos["grouper"]);
			//----------Insertion Stock--------------//
			//$infos["libelle"]=$infos["libelle"]." X ".$qte;
			for($i=0;$i<$qte;$i++){
				$id=parent::insert($infos,$s);
				//---------Insertion Stock-Etat---------//
				$stock_etat["id_stock"]=$id;
				ATF::stock_etat()->insert($stock_etat,$s);
			}	
		}
		// Redirection
		if(is_array($cadre_refreshed)){
			$this->redirection("select_all");
		}
		//Fin transaction
		ATF::db($this->db)->commit_transaction();
		return $id;
	}

	/**
	* Retourne un id_stock dispo ayant la ref passée en paramètre
	* @author Yann GAUTHERON <ygautheron@absytech.fr>
	* @param string $ref 
	* @return int $id_stock
	*/	
	public function getRefEnStock($ref){
		$this->q
			 ->reset()
			 ->select("stock.id_stock","id")
			 ->where("stock.ref",$ref)
			 ->where("stock_etat.etat","stock")
			 ->whereIsNull("id_bon_de_commande_ligne")
			 ->setDimension("cell");
		return $this->select_all();
	}
	
	/**
	* Surcharge de l'insert
	* @author EL HIZABRI MOUAD Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param array $infos les données a inseré
	* @return resultat insert
	*/
	public function insert($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL){
		$this->infoCollapse($infos);
		if($infos["id_affaire"]){
			//Nouvelle transaction 
			ATF::db($this->db)->begin_transaction();
			
			//----------Préparation des données-----------------//
			
			//--------------Gestion de l'état ------------------//
			//à partir du formulaire d'insertion stock
			if($infos["etat"]){
				$stock_etat["etat"]=$infos["etat"];
				unset($infos["etat"]);
			}
			unset($infos['formulaire']);
			unset($infos["commentaire"]);
			$redirection=$infos["redirection_custom"];
			unset($infos["redirection_custom"]);
			unset($infos["grouper"]);
			//----------Insertion Stock--------------//
			$id=parent::insert($infos,$s);
			//---------Insertion Stock-Etat---------//
			$stock_etat["id_stock"]=$id;
			if ($infos["date_achat"]) $stock_etat["date"]=$infos["date_achat"];
			ATF::stock_etat()->insert($stock_etat,$s);
			
			//redirection
			if(is_array($cadre_refreshed)){
				if($redirection){
					ATF::affaire()->redirection("select",$this->cryptId($infos["id_affaire"]));
				}else{
					$this->redirection("select_all");
				}
			}
			//-----------------------------------//
			//Fin transaction
			ATF::db($this->db)->commit_transaction();
			return $id;
		}else{
			$id = $this->insert_stock($infos,$s,$files,$cadre_refreshed);
		}
		return $id;
	}
	
	/**
	* Update à partir du formulaire
	* @author EL HIZABRI MOUAD
	* @param array $info les données a mettre à jours
	* @return resultat update 
	*/
	public function update_from($infos,&$s,$files=NULL,&$cadre_refreshed=NULL){
		$this->infoCollapse($infos);
		
		//Nouvelle transaction 
		ATF::db($this->db)->begin_transaction();
		
		$id_stock = $this->decryptId($infos["id_stock"]);
		unset($infos["formulaire"]);
		$etat=$infos["etat"];
		unset($infos["etat"]);
		
		//--------Mise à jours Stock----------//
		$id=parent::update($infos,$s);
		
		//-----Mise à jours stock_etat--------//
		if($etat){
			$ligne_stock_etat = ATF::stock_etat()->ss("id_stock",$id_stock);
			$stock_etat["id_stock_etat"] = $ligne_stock_etat[0]["id_stock_etat"];
			$stock_etat["date"] = date("Y-m-d H:i:s");
			$stock_etat["id_stock"] = $id_stock;
			$stock_etat["etat"] = $etat;
			ATF::stock_etat()->update($stock_etat,$s);
		}
		
		//Fin transaction 
		ATF::db($this->db)->commit_transaction();
		
		//Redirection 
		if(is_array($cadre_refreshed)){
//			if($redirection){
//				ATF::affaire()->redirection("select",$this->cryptId($infos["id_affaire"]));
//			}else{
				$this->redirection("select_all");
//			}
		}
		
		//Return id
		return $id;
	}
	
	/**
	* Surcharge de l'update
	* @author EL HIZABRI MOUAD Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param array $info les données a mettre à jours
	* @return resultat update 
	*/
	public function update($infos,&$s,$files=NULL,&$cadre_refreshed=NULL){
		$this->infoCollapse($infos);
		//etat stock
		if (isset($infos["etat"])) {
			unset($infos["etat"]);
		}

		//Nouvelle transaction 
		ATF::db($this->db)->begin_transaction();

		if($infos['formulaire']==false){  
			if (isset($infos['formulaire'])) unset($infos['formulaire']);
			//--------Mise à jours Stock----------//
			$id=parent::update($infos,$s);
		}else{
			$id= $this->update_from($infos,$s,$files,$cadre_refreshed);
		}
		
		//Fin transaction 
		ATF::db($this->db)->commit_transaction();
		return $id;		
	}
	
	/**
	* Méthode permettant de passer l'état d'un stock à annulee
	* @author Mouad EL HIZABRI
	* @param array $infos 
	*/
	public function annule($infos,&$s,$files=NULL,&$cadre_refreshed=NULL){
		$stock=$this->select($infos["id_stock"]);
		//dernier etat du stock
		$stock_etat = ATF::stock_etat()->getEtat($stock["id_stock"]);
		//verifecation de l'etat du stock
		if($stock_etat=="reception"){
			//-----------------------------Transaction--------------------------------------//
			ATF::db($this->db)->begin_transaction();
			//  STOCK ETAT
			$new_id=ATF::stock_etat()->insert(array("id_stock"=>$stock["id_stock"],"etat"=>"annule"),$s);
			ATF::db($this->db)->commit_transaction();
			//----------------------------------------------------------------------------//
			//notice: stock annulé
			$this->notice_content("notice_stock_annule","notice_success_title",$new_id);
			//--------rafraichisement des onglets---------//
			//if($id_affaire){ 
				//actualisation d'onlget stock
				$pager = "gsa_affaire_stock_".$stock["id_affaire"]; 
				$this->redirection('select_all_optimized',$pager);
		}
		//nouveau stock etat
		return $new_id;
	}
	
	/**
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return les lignes de stock en etat stock pour un bon de commande 
	*/
	 public function toStock() {
		$this->q->where("stock_etat.etat","stock")->reset('limit,page');
		return $this->select_all();
	}	
	
	/**
	* Surcharge select_all
	* recuperer les lignes du stock de la commande
	* avec leur etat actuel
	* @author Mouad EL HIZABRI Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param boolean $order_by
	* @param string $asc
	* @param string $page
	* @param string $count
	* @param string  $parent
	*/
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false,$parent=false){
		//sous requete: dernier insertion de stock_etat pour un stock
		$s_etat = new stock_etat();
		$s_etat->q
			  ->setAlias("seSub")
//			  ->addField("MAX(seSub.id_stock_etat)","id")
//			  ->orWhere("seSub.id_stock","stock.id_stock",false,"=",false,true)
			  ->addField("seSub.id_stock_etat","id")
			  ->orWhere("seSub.id_stock","stock.id_stock",false,"=",false,true)
			  ->setLimit(1)
			  ->addOrder("seSub.date","desc")
			  ->addOrder("seSub.id_stock_etat","desc")
			  ->setStrict()
			  ->setToString();
		$sous_requete = $s_etat->select_all();
		
		//requete principale: le stock avec son dernier etat
		$this->q
			 ->orWhere("se.id_stock_etat",$sous_requete,"seSub","=",true,true)
			 ->addField("se.etat","etat")
			 ->addField("stock.inventaire2013",inventaire2013)
			 //->addField("affaire.id_societe","affaire__id_affaire.id_societe")
			 ->addJointure("stock","id_affaire","affaire","id_affaire")
			 //->addJointure("affaire","id_societe","societe","id_societe")
			 ->from("stock","id_stock","stock_etat","id_stock","se",NULL,NULL,"seSub");
		$return = parent::select_all($order_by,$asc,$page,$count);

		foreach ($return['data'] as $k=>$i) {
			if ($i['etat'] == "reception") {
				$return['data'][$k]['allowRecu'] = true;	
				$return['data'][$k]['allowAnnule'] = true;	
			} else {
				$return['data'][$k]['allowRecu'] = false;	
				$return['data'][$k]['allowAnnule'] = false;	
			}
			if ($i['etat'] == "livraison") {
				$return['data'][$k]['allowLivre'] = true;	
			} else {
				$return['data'][$k]['allowLivre'] = false;	
			}
			$return['data'][$k]['quantiteInMagento'] = $this->getQuantity($i['stock.ref']);
			$return['data'][$k]['quantite'] = $this->getQuantity($i['stock.ref'],'non');
			if ($i['etat']=="stock") {
				$return['data'][$k]['allowMagento'] = true;
			} else {
				$return['data'][$k]['allowMagento'] = false;
			}
			if ($i['stock.ref']) {
				$return['data'][$k]['allowVente'] = true;
			} else {
				$return['data'][$k]['allowVente'] = false;
			}

		}
		return $return;
	}	
	
	/**
	* Surcharge de la méthode autocomplete pour faire apparaître les infos suplémentaire
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param array $infos 
	*/   	
	function autocomplete($infos) {
		if ($infos["limit"]>25) return; // Protection nombre d'enregistrements par page
		
		if (strlen($infos["query"])>0) {
			$data = array();
			$searchKeywords = stripslashes(urldecode($infos["query"]));
			
			// Récupérer les lignes devis
			$this->q->reset()
						->addField("stock.ref","ref")
						->addField("stock.serial","serial")
						->addField("stock.serialAT","serialAT")
						->addField("stock.libelle","stock")
						->addField('stock.id_stock',"id_stock")
						->addCondition("stock.libelle","%".ATF::db($this->db)->real_escape_string($infos["query"])."%","OR","cle",'LIKE')
						->addCondition("stock.serial","%".ATF::db($this->db)->real_escape_string($infos["query"])."%","OR","cle",'LIKE')
						->addCondition("stock.serialAT","%".ATF::db($this->db)->real_escape_string($infos["query"])."%","OR","cle",'LIKE')
						->addCondition("stock.ref","%".ATF::db($this->db)->real_escape_string($infos["query"])."%","OR","cle",'LIKE');
			$this->q->setLimit($infos["limit"])->setPage($infos["start"]/$infos["limit"]);
			
		}
		ATF::$cr->rm("top");
		return $this->sa();
	}
	
	/**
	* Prépare les produits a envoyer a Magento
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param array $infos tableau associatif en vue d'un update
	* @return booleen false si il y a eu un problème et true si tout c'est bien passé
	*/
	public function updateForMagento($infos) {
		if (!$infos['prix']) unset($infos['prix']);
						
		if (!$infos["id_stock"]) return false;
		else $id_stock = $this->decryptId($infos["id_stock"]);
		// Récupération du Stock et de ses infos
		$this->q->reset()
				->addField('ref')
				->addField('prix')
				->addField('description')
				->addField('short_description')
				->addField('categories_magento')
				->addField('libelle')
				->addField('marque')
				->where('stock.id_stock',$this->decryptId($infos["id_stock"]));
		$r = $this->select_row();
		// Si pas de ref alors on renvoi une erreur claire !
		if (!$r['ref']) {
			throw new errorStock("il_manque_la_ref",902);
		}
		
		// Est ce que le produit a une marque ? Sinon on essaye de la retrouver
		if (!$r['marque']) {
			$r['marque'] = $this->getMarque($r['libelle']);
			if (!$r['marque']) {
				throw new errorStock("impossible_de_definir_la_marque_du_stock",904);
			}
		}
		
		// On prépare notre tableau pour Magento
		$magento = array(
			"ref"=>$r['ref'],
			"libelle"=>$r['libelle'],
			"marque"=>$r['marque'],
			"id_stock"=>$id_stock,
			"prix"=>$infos['prix']?$infos['prix']:$r['prix'],
			"categories_magento"=>$infos["categories_magento"]?$infos["categories_magento"]:$r['categories_magento'],
			"description"=>$r['description'],
			"short_description"=>$r["short_description"]
		);
		
		// Throw si pas de prix !
		
		ATF::db($this->db)->begin_transaction();
		
		try {
			// Envoi vers Magento
			if ($infos['action'] == 'send') {
				$magento['to_magento'] = "oui";
				if ($infos['icecatParsing']) $this->ParserIcecat($magento);
				// Mise a jour du stock (et de tous ses confrères)
				$this->update($magento);
				$this->update_all_ref($magento);
				$behavior = "append";
			} else {
				// Supression de Magento
				$magento['to_magento'] = "non";
				
				if ($infos['nb'] == "yes") { // On enlève tous les produits avec la même ref de magento
					$behavior = "delete";
					$this->update($magento);
					$this->update_all_ref($magento);
					
				} else { // On ne supprime qu'un produit (dans le cas d'une vente par exemple)
					$behavior = "replace";
					$this->u($magento);
				}
			}
			$this->sendToMagento($magento,$behavior);
		} catch (errorStock $e) {
			ATF::db()->rollback_transaction();
			throw $e; 
		}
		
		ATF::$msg->addNotice(loc::mt(ATF::$usr->trans("notice_update_success"),array("record"=>$r['ref'])),ATF::$usr->trans("notice_success_title"));
		ATF::db($this->db)->commit_transaction();
				
		return true;
	}
	
	/**
	* Méthode permettant la récupération d'une quantité en fonction de la ref en paramètre
	* @author Antoine MAITRE <amaitre@absystech.fr>
	* @param string $ref chaine representant la réf du produit
	* @return int $quantity représente la quantité de produit trouvée
	*/
	
	public function getQuantity($ref,$toMagento="oui"){
		if (!$ref) return false;	
		
		$this->q->reset()->addField('id_stock')->where("ref",$ref)->where("to_magento", $toMagento);
		$tab = $this->sa();
		$count = 0;
		foreach($tab as $k => $i) {
			if (ATF::stock_etat()->getEtat($i['id_stock']) == "stock") {
				$count++;	
			}
		}
		return $count;
	}
	
	/**
	* Méthode permettant la récupération d'une marque en fonction de la chaine donnée en paramètre
	* @author Antoine MAITRE <amaitre@absystech.fr>
	* @param string $str chaine representant la chaine où la marque doit être retrouvé
	* @return string $marq_str ou NULL représente la quantité de produit trouvée
	*/
	
	public function getMarque($str) {
		
		$tmp_tab = NULL;
		
		if ($str == NULL) {
			return NULL;	
		}
	/*	Tableau contenant toutes les marques que l'on cherche	*/
	
		$marq_tab = array("Netgear","Kingston Technology","Kingston","Lenovo","DELL","Microsoft","TPM","Seagate","WD","Fujisu","SWEEX","Samsung","LSI", "MSI", "Cisco","Sony","D-Link","Linksys","IBM","Mitel","Acer","LG","Philips","Hyundai","Sampo","Asus","Belinea","3com","SonicWALL","Toshiba","NEC","HP","PNY","Compaq");
		
		foreach ($marq_tab as $i) {
			if (preg_match("/\b".$i."\b/i", $str)) {
				if ($tmp_tab == NULL) {
					$tmp_tab = $i;
				} else {
					$tmp_tab = $tmp_tab.",".$i;
				}
			}
		}
		return $tmp_tab;
	}

	/**
	* Méthode permettant de recuperer les infos icecat de compléter avant l'insert
	* Infos récupéré : description, shortDescription, poids et les images
	* @author Antoine MAITRE <amaitre@absystech.fr>
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param array $infos tableau représentant l'insert
	* @return bool retourne true si tout se passe bien et false en cas d echec
	*/
	
	public function getDataFromIcecat($marque,$ref,$id_stock) {
				
		// URL ICE CAT VERS LA FICHE TECHNIQUE DU PRODUIT
		$urls = "http://prf.icecat.biz/?shopname=openIcecat-url;smi=product;vendor=".urlencode($marque).';prod_id='.urlencode($ref).";lang=fr";

		$ch = curl_init($urls); 
		curl_setopt($ch, CURLOPT_COOKIEFILE, realpath('cookie.txt'));
//		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$html = curl_exec($ch);
		$html = mb_convert_encoding($html, "UTF-8"); 
		curl_close($ch);
		
		// Pour éviter les caractère pourries qui empêche la bonne résolution des Regex
		$html = str_replace("`","'",$html);
		
		// La fiche technique est inaccessible : Produit obsolète ou alors la REF n'est pas bonn, ou bien la marque :/
		if (preg_match('#Désolé, pour ce produit, nous n\'avons pas trouvé d\'autres informations produit.<br>Si vous n\'êtes pas redirigés automatiquement, veuillez cliquer#', $html) || !$html) {
			throw new errorStock(ATF::$usr->trans("fiche_technique_inaccessible_for_url : ").$urls,910);
		}
				
		$html = mb_convert_encoding($html, 'HTML-ENTITIES', "UTF-8");
		
		// On récupère l'id icecat pour pouvoir appeler le XML qui est plus facile a parser que le HTML
		preg_match('#<input type="hidden" name="icecat_id"\s+value="(.*?)" />#si', $html, $pregResult);

		if ($idIceCat = $pregResult[1]) {
			$dom = new DOMDocument();
			//$dom->load("http://openIcecat-xml:freeaccess@data.icecat.biz/export/freexml.int/FR/435466.xml"); // Fiche avec tout ce qui faut comme infos
			$dom->load("http://openIcecat-xml:freeaccess@data.icecat.biz/export/freexml.int/FR/".$idIceCat.".xml"); 
			$xpath = new Domxpath($dom); 
			
			$shortDesc = $xpath->query("//ShortSummaryDescription");
			if ($shortDesc->length) $return['short_description'] = $shortDesc->item(0)->nodeValue; 
			$desc = $dom->getElementsByTagName('ProductDescription'); 
			$return['description'] = ($desc->length && $desc->item(0)->getAttribute("LongDesc"))?$desc->item(0)->getAttribute("LongDesc"):$xpath->query("//LongSummaryDescription")->item(0)->nodeValue; 
			$nodePoids = $xpath->query("//ProductFeature[@No='100021']");
			if ($nodePoids->length) $return['poids'] = $nodePoids->item(0)->getAttribute("Presentation_Value");
			
			if (!file_exists($this->filepath($id_stock, "photo"))) {
				$this->getImageFromIcecat($dom, $id_stock);
			}   
		}
		
		$return["description"] .= "<br /><br /><a href=".$urls." target=_blank>Pour plus d'informations, cliquez sur ce lien.<a>";
		return $return; 
	}

	/**
	* Récupère les URL des images du produits dans le DOM et les stock dans optima !
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param array $dom Obket DOM contenant toutes les infos du produits issue de sa fiche XML
	* @param mixed $id définit l'id stock de l'objet pour la création de la photo
	*/
	
	public function getImageFromIcecat($dom,$id) {
		if (!$dom) return false;
		if ($product = $dom->getElementsByTagName('Product')) {
			$urlHighPic = $product->item(0)->getAttribute("HighPic");
			$data = file_get_contents($urlHighPic);
			if (strlen($data)) {
				ATF::util()->file_put_contents($this->filepath($id, "photo"), $data);
				ATF::$msg->addNotice(ATF::$usr->trans("stock_image_stock_success"), ATF::$usr->trans("stock_image_stock_success_title"));  
			} else {
				ATF::$msg->addWarning(ATF::$usr->trans("stock_image_stock_failure"), ATF::$usr->trans("stock_image_stock_failure_title"));  
			}
		}
	}
	
	/**
	* Méthode permettant de finir de compléter avant l'insert
	* Récupère la description, le poids et les images du produit
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param array $infos tableau représentant l'insert
	* @param mixed $id définit l'id stock de l'objet pour la création de la photo
	* @return bool false en cas d'erreur et true si tout se passe bien
	*/
		
	public function ParserIcecat(&$data) {
		if (!$data['marque']) {
			throw new errorStock("pas_de_marque_pour_ce_stock_impossible_d_interroger_icecat",909);
		} else {
			foreach (explode(',',$data['marque']) as $k=>$marque) {
				$r = $this->getDataFromIcecat($marque,$data['ref'],$data['id_stock']);
				$data['description'] = $r['description'];
				$data['short_description'] = $r['short_description'];
				$data['poids'] = $r['poids'];                
			}           
		}
		return true;
	}

	/**
	* Méthode permettant l'envoie d'un produit sur magento
	* @author Antoine MAITRE <amaitre@absystech.fr>
	* @author Quentin JANON <qjanon@absystech.fr>
	 *     * @param array $infos correspond aux valeurs necessaires à l'import du produit
	* @return bool return false quand une erreur survient et true si tout se passe normalement
	*/

	public function sendToMagento($infos,$behavior="append"){ 
		$qte = $this->getQuantity($infos['ref'],$infos['to_magento']);
		if ($behavior=="replace") {
			$qte = $this->getQuantity($infos['ref'],"oui");
		}
		if ($qte<1) $behavior = "delete";  
		
			
		$line = array(
			"sku"=>$infos['ref'],
			"_attribute_set"=>"Default",                    //Attribut à mettre par défaut, non optionnel
			"_type"=>"simple",                              //Attribut à mettre par défaut, non optionnel
			"qty"=> number_format($qte,4),
			"description"=>$infos["description"],
			"image" => NULL,
			"is_in_stock"=>1,
			"media_gallery" => NULL,
			"name"=>substr($infos["libelle"],0,254),    //Le name ne peut pas dépasser 255 caractères, sinon il n'est pas pris par magento
			"news_from_date"=>date("Y-m-d")." 00:00:00",
			"news_to_date"=>date("Y-m-d",strtotime("+1 week"))." 00:00:00",
			"price"=>$infos["prix"],
			"short_description"=>$infos["short_description"],
			"small_image" => NULL,
			"status"=>1,
			"tax_class_id"=>2,
			"thumbnail" => NULL,
			"visibility"=>4,
			"weight"=>$infos['poids']?number_format($infos['poids'], 4):0,
			"_category"=>ATF::$usr->trans($infos['categories_magento'], "stock"),          //Chemin de l'emplacement de l'objet dans le magasin
			"_media_attribute_id" => 88,
			"_media_image" => NULL,
			"_media_is_disabled" => 0,
			"_product_websites"=>"base",                    //Permets le stock des produits dans le frontend et pas seulement le backend
			"_root_category"=>"Default Category"        //Permets de sélectionner le magasin
		);

		$photoPath = $this->filepath($infos['id_stock'], "photo");
		if (file_exists($photoPath)) {
			if (!copy($photoPath, __STORE_PATH__.'media/import/'.$infos['id_stock'].".photo.png")) {
				ATF::$msg->addWarning(ATF::$usr->trans("stock_image_stock_failure").__STORE_PATH__.'media/import/', ATF::$usr->trans("stock_image_stock_failure_title"));
				$line['image'] = NULL;
				$line['media_gallery'] = NULL;
				$line['small_image'] = NULL;
				$line['_media_image'] = NULL;
			} else {
				$line['image'] = $infos['id_stock'].".photo.png";
				$line['media_gallery'] = $infos['id_stock'].".photo.png";
				$line['small_image'] = $infos['id_stock'].".photo.png";
				$line['_media_image'] = $infos['id_stock'].".photo.png";
			}
		}
		
		// Création du fichier CSV pour l'import
		$filenameCSV = "/tmp/Produit_mag.csv";
		$tmpCSV = fopen($filenameCSV, 'w+');
		// Entête
		fputcsv($tmpCSV, array_keys($line));
		fputcsv($tmpCSV, $line);
		fclose($tmpCSV);
		
		$string = "login[username]=".urlencode('Optima')."&login[password]=".urlencode('qGS4Fb7d7BOKZTj');
		
		$formulaire = array();
		$formulaire["entity"]=urlencode('catalog_product');
		// Comportement de l'import : create (append), update (replace) ou delete 
		$formulaire["behavior"]=$behavior;
		$formulaire["import_file"] = '@'.realpath($filenameCSV);
		
		$cookPath = '/tmp/cookieForMagento.txt';
		$cook = realpath($cookPath);
		if ($cook !== false) {
			unlink($cook);
			touch($cook);
			chmod($cook, 0666);
			fopen($cook, 'w+');
		} else {
			touch($cookPath);
			chmod($cookPath, 0666);
			fopen($cookPath, 'w+');
			$cook = realpath($cookPath);
		}
		
		// Connexion a l'admin du STORE
		$ch = curl_init('http://dev.store.absystech.fr/index.php/admin/admin/');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_COOKIE, session_name().'='.session_id());
		curl_setopt($ch, CURLOPT_COOKIESESSION, true);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cook);
		$output = curl_exec($ch);
		
		if ($err = curl_error($ch)){
			throw new errorStock("Erreur cURL 0: ".curl_errno($ch)." : ".curl_error($ch));  
		}
		unset($err);        
		curl_close($ch);
		
		$ch = curl_init(__STORE_URL__.'index.php/admin/admin/import/');
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cook);
		//curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($ch);
		if ($err = curl_error($ch)){
			throw new errorStock("Erreur cURL 1 : ".curl_errno($ch)." : ".curl_error($ch));  
		}
		unset($err);
		curl_close($ch);

		preg_match("#FORM_KEY = '([a-zA-Z0-9]+)';#si", $output, $matches);
		$formulaire['form_key'] = $matches[1];
		if (!$formulaire['form_key']){
			throw new errorStock("Aucune cle trouvee... : ".$output);
		}

		$ch = curl_init(__STORE_URL__.'index.php/admin/admin/import/validate/?form_key='.$formulaire['form_key']);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cook);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $formulaire);
		$output = curl_exec($ch);

		if ($err = curl_error($ch)){
			throw new errorStock("Erreur cURL 2 : ".curl_errno($ch)." : ".curl_error($ch));  
		}
		unset($err);        
		curl_close($ch);
		
		// Si le fichier est invalide, alors on déclenche une erreur en affichant le problème
		if (!preg_match('#File is valid#', $output)) {
			preg_match('#"innerHTML":\{"import_validation_messages":(.*?)"\}#i',$output,$m);
			// check des erreurs connues 
			if (preg_match("#Required attribute '([a-zA-Z_]*)' has an empty value in rows#i",$m[1],$field)) {
				$msg = ATF::$usr->trans("champ_obligatoire")." Il manque les infos pour le champ : ".ATF::$usr->trans($field[1],"stock");
			} else {
				$msg = ATF::$usr->trans("csv_format")." ".$m[1]." (".$output.")";
			}
			throw new errorStock($msg);
		}

		$formulaire['import_file'] = "";
		
		$ch = curl_init(__STORE_URL__.'index.php/admin/admin/import/start/');
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cook);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $formulaire);
		$output = curl_exec($ch);
		if ($err = curl_error($ch)){
			throw new errorStock("Erreur cURL 3 : ".curl_errno($ch)." : ".curl_error($ch));  
		}
		unset($err);        
		
		$index = array();
		
		$index['form_key'] = $formulaire['form_key'];
		$index['process'] = '1,2,3,4,5,6,7,8,9';
		$index['massaction_prepare_key'] = 'process';
		
		$ch = curl_init(__STORE_URL__.'index.php/admin/admin/process/massReindex/');
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cook);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $index);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);//       curl_setopt($ch, CURLOPT_VERBOSE, true);
		$output = curl_exec($ch);
		if ($err = curl_error($ch)){
			throw new errorStock("Erreur cURL 4 : ".curl_errno($ch)." : ".curl_error($ch));  
		}
		unset($err);        
				
		curl_close($ch);

		unlink($cook);
		unlink($filenameCSV);
		ATF::$msg->addNotice(ATF::$usr->trans("import_mag_success"),ATF::$usr->trans("mag_success_title"));
		return true;

	}

	/**
	* Méthode permettant l'update de tous les produits ayant la même ref et étant en stock
	* @author Antoine MAITRE <amaitre@absystech.fr>
	* @param array $product correspond aux valeurs necessaires à l'update du produit
	* @return bool return false quand une erreur survient et true si tout se passe normalement
	*/

	public function update_all_ref($infos){
	return true;	
	if (!$infos['id_stock']) return false;
		//$infos['id_stock'] = $this->decryptId($infos['id_stock']);        
		$r = $this->select($infos['id_stock']);

		$product = array(
			"ref"=>$r["ref"],
			"libelle"=>$r["libelle"],
			"prix"=>$r['prix'],
			"marque"=>$r["marque"],
			"description"=>$r["description"],
			"short_description"=>$r["short_description"],
			"poids"=>$r["poids"],
			"categories_magento"=>$r["categories_magento"],
			"to_magento"=>$r["to_magento"],
		);
		
		$this->q->reset()->addField('id_stock')->where('ref', $product['ref']);
		$r = $this->sa();
		foreach($r as $k=>$i) {
			$product['id_stock'] = $i['id_stock'];
			$this->update($product);
		}
		
		return true;
	}

	public function switchStock($infos){
		ATF::stock()->q->reset()->where("serial" , $infos["serial"])
								->whereIsNull("stock.id_affaire");
								/*->whereIsNull("stock.id_bon_de_commande_ligne"); Car lors d'une duplication, on stocke quand meme l'id_bon_de_commande_ligne */
		$stock = ATF::stock()->select_row();

		if($stock && $stock["etat"] == "stock"){
			$return = "success";
			$id_affaire = ATF::affaire()->decryptId($infos["affaire"]);
			ATF::stock_etat()->q->reset()->where("id_stock" , ATF::stock()->decryptId($infos["id"]))
										->addOrder("stock_etat.date", "desc")
										->setLimit(1);

			$etat = ATF::stock_etat()->select_row();
			$id_bdcl = $this->select(ATF::stock()->decryptId($infos["id"]) , "id_bon_de_commande_ligne");
			ATF::stock()->u(array('id_stock' => ATF::stock()->decryptId($infos["id"]) , "id_affaire" => NULL , "id_bon_de_commande_ligne" => NULL));
			ATF::stock()->u(array('id_stock' => $stock["stock.id_stock"] , "id_affaire" => $id_affaire , "id_bon_de_commande_ligne" => $id_bdcl));
			if($etat["etat"] !== "stock"){
				ATF::stock_etat()->insert(array("id_stock" => ATF::stock()->decryptId($infos["id"]) , "etat" => "stock" , "commentaire" => "Switch de stock affaire ".ATF::affaire()->select($id_affaire , "affaire")));
				ATF::stock_etat()->insert(array("id_stock" => $stock["stock.id_stock"] , "etat" => $etat["etat"] , "commentaire" => "Switch de stock avec stock Serial : ".ATF::stock()->select(ATF::stock()->decryptId($infos["id"]) , "serial")));
			}
			
		}else{
			$return = "error";			
		}		
		return $return;
	}

	public function checkInventaire2013($infos) {			
		$infos['inventaire2013'] = "oui";
		if(ATF::$codename == "att"){
			ATF::stock_etat()->i(array( "date" => date("Y-m-d H:i:s"),
										"etat" => "stock",
										"commentaire" => "Inventaire ATT a Juillet ".date("Y"),
										"id_stock" => $this->decryptId($infos["id_stock"])
								));
		}else{
			ATF::stock_etat()->i(array( "date" => date("Y-m-d H:i:s"),
										"etat" => "stock",
										"commentaire" => "Inventaire AT ".date("Y"),
										"id_stock" => $this->decryptId($infos["id_stock"])
								));
		}
		return parent::update($infos);
	}
	/**
	* Fonction _GET pour telescope
	* @package Telescope - Hyperviseur CC
	* @author Charlier Cyril <ccharlier@absystech.fr>
	* @param $get array.
	* @param $post array Argument obligatoire.
	* @return boolean | integer
	*/
	public function _GET($get,$post) {
		// Gestion du tri
		if (!$get['tri']) $get['tri'] = "ref";
		if (!$get['trid']) $get['trid'] = "desc";
		// Gestion du limit
		if (!$get['limit']) $get['limit'] = 30;
		log::logger($get,'ccharlier');
		// Gestionde la page
		if (!$get['page']) $get['page'] = 0;

		$colsData = array(
			"stock.id_stock",
			"serial",
			'stock.id_affaire'
		);
		$this->q->reset();
		if ($get['serial']) {
			$this->q->where("serial",$get['serial'])->setLimit(1);

		} else {
			$this->q->setLimit($get['limit']);
		}
		$this->q->addField($colsData)
				->setCount();
		$data = $this->select_all($get['tri'],$get['trid'],$get['page'],true);
		foreach ($data["data"] as $k=>$lines) {
			foreach ($lines as $k_=>$val) {
				if (strpos($k_,".")) {
					$tmp = explode(".",$k_);
					$data['data'][$k][$tmp[1]] = $val;
					unset($data['data'][$k][$k_]);
				}				
			}
		}
		// si l'on recupère un seul user, on renvoie directement la premiere ligne du tableau
		if($get['serial']){
			$return = $data['data'][0];	
		}else{	
			header("ts-total-row: ".$data['count']);
			header("ts-max-page: ".ceil($data['count']/$get['limit']));
			header("ts-active-page: ".$get['page']);
			$return = $data['data'];
		}
		return $return;
	}
}

class errorStock extends errorATF {};

class stock_att extends stock_absystech { };
class stock_wapp6 extends stock_absystech { };
class stock_demo extends stock_absystech { };
?>
