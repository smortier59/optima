<?
/**
* Classe Livraison Absystech
* @author MOUAD EL HIZABRI
* @package Optima
* @subpackage Absystech
**/
require_once dirname(__FILE__)."/../livraison.class.php";
class livraison_absystech extends livraison {
	/**
	* Constructeur Livraison
	* @author MOUAD EL HIZABRI
	*/
	public function __construct() {
		parent::__construct();
		$this->table = "livraison"; 
		$this->colonnes['fields_column'] = array(
			'livraison.ref'=>array("width"=>100,"align"=>"center")
			,'livraison.livraison'
			,'livraison.date'=>array("width"=>100,"align"=>"center")
			,'livraison.etat'=>array("width"=>30,"align"=>"center","renderer"=>"etat")
			,'termine'=>array("width"=>50,"custom"=>true,"align"=>"center","nosort"=>true,"renderer"=>"livraisonTermine")
			,'bon_de_livraison'=>array("custom"=>true,"nosort"=>true,"align"=>"center","type"=>"file","width"=>60)
			,'bon_de_livraison_signe'=>array("custom"=>true,"nosort"=>true,"align"=>"center","type"=>"file","width"=>60,"renderer"=>"scanner")
		);
											  
		$this->colonnes['primary'] = array(
			'id_expediteur'
			,'date'
			,'id_transporteur'
			,'code_de_tracabilite'
			,'id_societe'
			,'regenerate'=>array("custom"=>true)
		);
	    $this->colonnes['panel']['lignes'] = array(
			"produits"=>array("custom"=>true)
		);
		$this->panels['lignes'] = array("visible"=>true, 'nbCols'=>1 ,"collapsible"=>false);
		$this->panels['primary'] = array("visible"=>true, 'nbCols'=>4 ,"collapsible"=>false);
		
		$this->fieldstructure();	
		$this->files["bon_de_livraison"] =  array(
												"type"=>"pdf"
												,"preview"=>true
												,"collapsible"=>false
												,"no_upload"=>true
											  );
											
		$this->files["bon_de_livraison_signe"] =  array(
												"type"=>"pdf"
												//,"collapsible"=>false
												,"no_store"=>true
											);
											
	   $this->colonnes['bloquees']['insert'] = array(
													 'ref'
													 ,'id_affaire'
													 ,'id_commande'
													 ,'id_devis'
													 //,'id_societe'
													 ,'etat'
													 ,'livraison'
													 ,'regenerate'
												  );
												  
	   $this->colonnes['bloquees']['update'] = array(
													 'ref'
													 ,'id_affaire'
													 ,'id_commande'
													 ,'id_devis'
													 ,'id_expediteur'
													 ,'id_societe'
			//										 ,'bon_de_livraison'
													 ,'etat'
													 ,'livraison'
													 ,'date'
													 ,'id_transporteur'
													 ,'code_de_tracabilite'
													 ,'produits'
													 ,'regenerate'
												  );
		//privileges										
		$this->foreign_key["id_affaire"] = "affaire";
		$this->foreign_key["id_expediteur"] = "user";           
		$this->foreign_key["id_transporteur"] = "transporteur";
		$this->addPrivilege("delivery_Complete","update");
		$this->addPrivilege("generatePDF","insert");
		//$this->field_nom = "ref";
		$this->no_insert = true;
		$this->no_update = false;
		$this->no_delete = false;
		$this->onglets = array('livraison_ligne');
		
	}
	
	/**
	* Surcharge insertion Livraison
	* @author Mouad EL HIZABRI
	*/
	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed=NULL){
		//Nouvelle transaction
		ATF::db($this->db)->begin_transaction();
		//---------------------------------------------------------//
		$preview= $infos["preview"];
		unset($infos["preview"]);
		$livraison_lignes=json_decode($infos["values_livraison"]["produits"],true);	
		$this->infoCollapse($infos);

		//$infos['id_expediteur']=ATF::$usr->getID();
		$commande = ATF::commande()->select($infos['id_commande']); 
		//Création de la ref bon de livraison
		if(!$infos['ref']){
			$infos['ref'] = strtoupper(substr($this->table,0,3)
						   .substr(ATF::agence()->nom(ATF::$usr->get('id_agence')),0,2).rand(11,90))
						   .($infos["date"]?date("ym",strtotime($infos["date"])):NULL)
						   .rand(1,99);
		}
		$infos['livraison'] = 'Livraison '.$commande['resume'];
		$infos['id_devis']=$commande['id_devis'];	
		unset($infos["filestoattach"]);
		
		//Insertion de la livraison
		$last_id=parent::insert($infos,$s,NULL,$var=NULL,NULL,true);
		
		//Insertion des lignes de livraison
		foreach($livraison_lignes as $valeur){
			$l_ligne["id_livraison"]=$last_id;
			if(isset($valeur["stock__dot__id_stock_fk"])){
				$l_ligne["id_stock"]=$this->decryptId($valeur["stock__dot__id_stock_fk"]);
			}else{
				$l_ligne["id_stock"]=$this->decryptId($valeur["stock__dot__id_stock"]);
			}
			ATF::livraison_ligne()->insert($l_ligne,$s);
			//Mise à jour du stock_etat
			$stock_livrer["id_stock"]=$l_ligne["id_stock"];
			$stock_livrer["etat"] = "livraison";			
			ATF::stock_etat()->insert($stock_livrer,$s);
		}
		//Redirection
		if(is_array($cadre_refreshed)){
			ATF::affaire()->redirection("select",$infos["id_affaire"]);
		}
		//Pdf
		if($preview){
			$this->move_files($last_id,$s,true,$infos["filestoattach"]);
			//Preview => on ne commit pas
			ATF::db($this->db)->rollback_transaction();
			return $this->cryptId($last_id);
		}else{
			$this->move_files($last_id,$s,false,$infos["filestoattach"]); 
			//Fin transaction
			ATF::db($this->db)->commit_transaction();
			return $this->cryptId($last_id);
		}
	}
	
	/**
    * Retourne la valeur par défaut spécifique aux données passées en paramètres
    * @author Jérémie GWIAZDOWSKI <jgwiazdowski@absystech.fr>
	* @param string $field
	* @param array &$s La session
	* @param array &$request Paramètres disponibles (clés étrangères)
	* @return string
    */   	
	public function default_value($field){
		switch ($field) {
			case "id_societe":
				$commande=ATF::_r("id_commande");
				if(isset($commande) && !empty($commande)){
					return ATF::commande()->select($this->decryptId($commande),"id_societe");
				}
			default:
				return parent::default_value($field);
		}
	}	

	/**
	* Surcharge du delete 
	* @author MOUAD EL HIZABRI
    */	
	public function delete($infos,&$s,$files=NULL,&$cadre_refreshed){
		$last_id= array();
		//Nouvelle transaction
		ATF::db($this->db)->begin_transaction();
		//---------------------------------------------------------//
		foreach($infos["id"] as $cle=>$valeur){
			$info_id =$valeur;
			if (is_numeric($info_id) || is_string($info_id)){			
				$id=$this->decryptId($info_id);
				$livraison =$this->select($id); 
				$livraison_ligne = ATF::livraison_ligne()->ss("id_livraison",$livraison["id_livraison"]);
 				//livraison_ligne
				foreach($livraison_ligne as $c=>$v){
					ATF::livraison_ligne()->delete($v["id_livraison_ligne"],$s);
				}
				//livraison
				$last_id = parent::delete($id,$s);
				
			}
		}
		//-----------------------------------------------------------//
		//Fin transaction
		ATF::db($this->db)->commit_transaction();
		//redirection vers l'affaire
		if(is_array($cadre_refreshed)){
			$id_affaire= $livraison["id_affaire"];
			$this->redirection('select_all_optimized',"gsa_affaire_livraison_".$id_affaire);
		}
		return $last_id;
	}
	
	
	/**
	* Livraison Terminée
	* Passe le stock d'une commande en etat livré
	* @author MOUAD EL HIZABRI
	*/
	public function delivery_Complete($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL){
		//Nouvelle transaction
		ATF::db($this->db)->begin_transaction();
		
		$id_livraison = $this->decryptId($infos['id_livraison']);
		unset($infos["id_livraison"]);
		//-----------livraison_ligne---------------//
		$les_stock = ATF::livraison_ligne()->ss("id_livraison",$id_livraison);
		$id_affaire=$this->select($id_livraison,"id_affaire");	
		$livraison['id_livraison'] = $id_livraison;
		unset($id_livraison);
		foreach($les_stock as $qle=>$valeur){
			$infos['id_stock'] = $valeur['id_stock'];
			//modifier l'etat du stock en "livré"
			ATF::stock()->setDelivered($infos);
			
			//----------mise à jour de livraison ligne----------//
			$livraison_ligne['id_livraison_ligne'] 
			= ATF::livraison_ligne()->ss('id_stock',$infos['id_stock']);			
			$livraison_ligne['etat']="termine";
			ATF::livraison_ligne()->u($livraison_ligne,$s);
		}
			
		//-----------mise à jour de la  livraison-----------// 
		$livraison['etat'] = "termine";
		parent::u($livraison,$s);
		
		if($id_affaire){
			//-----actualisation d'onlget livraison----//
			$pager = "gsa_affaire_livraison_".$id_affaire;
			$this->redirection('select_all_optimized',$pager);
			
			//-----actualisation d'onlget stock-----//
			$pager_1 = "gsa_affaire_stock_".$id_affaire;
			ATF::stock()->redirection('select_all_optimized',$pager_1);
		}else{
			$this->redirection("select_all");
		}
		
		//Fin transaction
		ATF::db($this->db)->commit_transaction();
	}
	
	/**
	* Surcharge update Livraison
	* @author Jérémie GWIAZDOWSKI
	*/
	public function update($infos,&$s,$files=NULL,&$cadre_refreshed=NULL){
		$this->infoCollapse($infos);
		if(isset($infos['filestoattach']) && is_array($infos['filestoattach'])){
			$insert_files=$infos['filestoattach'];
			unset($infos['filestoattach']);
		}
		
		$id = $infos["id_".$this->table];
		
		if ($this->files && ($insert_files || $this->files["preview"])){
			if(is_array($insert_files)){
				foreach($insert_files as $key=>$item){
					//Si le fichier a été supprimé
					if($item=="undefined"){
						//$this->delete_file($id,$key);
					//Si le fichier a été modifié
					}elseif($item && $item!="true"){
						$this->move_files($id,$s,false,$insert_files,$key);
					}
				}
			}
		}
		
		if($infos["id_affaire"]){
			ATF::affaire()->redirection("select",$infos["id_affaire"]);
		}else{
			$this->redirection("select_all");
		}
	}

	/** 
	* can_update si le bon signé n'est pas encore rentré
	* @author Jérémie GWIAZDOWSKI
	* @return boolean 
	*/
	public function can_update($id,$infos=false){
		//Il y a toujours un BL signé donc on a toujours l'erreur.
		return true;
		/*if($this->file_exists($this->decryptId($id),"bon_de_livraison_signe")){
			return false;
		}else{
			return true;
		}*/
	}

	/**
	* Surcharge du select-All
	* @author Mouad EL HIZABRI
	*/
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){
		$this->q->addField('livraison.id_affaire','id_affaire');
		$return = parent::select_all($order_by,$asc,$page,$count);
		
		foreach ($return['data'] as $k=>$i) {
			if ($i['livraison.etat']=="en_cours" || $i['livraison.etat']=="termine_partiel") {
				$return['data'][$k]['allowTermine'] = true;	
			} else {
				$return['data'][$k]['allowTermine'] = false;	
			}
		}
		return $return;
	}

	/** Permet d'envoyer un mail au regénérateur pour qu'il garde une trace de la facture supprimée
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return void
	*/ 
	function generatePDF($infos,&$s,$preview=false){
		
		$livraison=$this->select($infos["id"]);
		$path = $this->filepath($infos["id"],"bon_de_livraison",$preview);
		if (file_exists($path)) {
			//Envoi d'un mail
			$info_mail["objet"] = "Livraison avant régénération : ".$livraison["ref"];
			$info_mail["from"] = ATF::user()->nom(ATF::$usr->getID())." <".ATF::$usr->get('email').">";
			$info_mail["html"] = false;
			$info_mail["template"] = 'devis';
			$info_mail["texte"] = "Livraison de sauvegardre avant régénération : ".$livraison["ref"]." (".$infos["id"].")";
			$info_mail["recipient"] = ATF::$usr->get('email');
		
			//Ajout du fichier
			$mail = new mail($info_mail);
			$mail->addFile($path,$infos["ref"].".pdf",true);						
			$mail->send();
		
			ATF::$msg->addNotice("Ancien BL envoyé par email...");
		} else {
			ATF::$msg->addWarning("L'ancien fichier pdf du BL n'existait pas !");
		}
		
		$this->move_files($infos["id"],$s,false);
		
		$this->update(array("id_livraison"=>$infos["id"],"date"=>date("Y-m-d H:i:s")));
		
		ATF::$msg->addNotice("BL regénéré avec succès !");
		$this->redirection("select",$infos["id"]);
		return true;
	}
}
class livraison_att extends livraison_absystech { };
class livraison_wapp6 extends livraison_absystech { };
class livraison_atoutcoms extends livraison_absystech { };
class livraison_demo extends livraison_absystech { };
?>