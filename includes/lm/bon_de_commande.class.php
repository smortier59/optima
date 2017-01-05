<?
/** Classe bon de commande
* @package Optima
* @subpackage Leroy Merlin
*/
require_once dirname(__FILE__)."/../bon_de_commande.class.php";
class bon_de_commande_lm extends bon_de_commande {
	function __construct() {
		parent::__construct();
		$this->table = "bon_de_commande";

		$this->colonnes['fields_column'] = array(
			"bon_de_commande.ref"
			,"bon_de_commande.num_bdc"=>array("rowEditor"=>"setInfos")
			,"bon_de_commande.id_fournisseur"
			,"bon_de_commande.bon_de_commande"
			,"bon_de_commande.etat"=>array("renderer"=>"etat","width"=>30)
			,"bon_de_commande.prix"=>array("aggregate"=>array("min","avg","max","sum"),"type"=>"decimal","renderer"=>"money")
			,'solde_ht'=>array("custom"=>true,"aggregate"=>array("min","avg","max","sum"),"align"=>"right","suffix"=>"€","type"=>"decimal","renderer"=>"money")
			,'fichier_joint'=>array("custom"=>true,"nosort"=>true,"type"=>"file","align"=>"center","width"=>50)
			,'pdf'=>array("custom"=>true,"nosort"=>true,"type"=>"file","align"=>"center","width"=>70,"renderer"=>"uploadFile")
			,'factureFournisseur'=>array("custom"=>true,"nosort"=>true,"align"=>"center","width"=>50,"renderer"=>"expandToFactureFournisseur")
			,'parcInsertion'=>array("custom"=>true,"nosort"=>true,"align"=>"center","width"=>50,"renderer"=>"uploadFile","renderer"=>"parcInsertion")
			,"bon_de_commande.date_livraison_estime"=>array("renderer"=>"updateDate","width"=>170)
			,"bon_de_commande.date_livraison_prevue"=>array("renderer"=>"updateDate","width"=>170)
			,"bon_de_commande.date_livraison_reelle"=>array("renderer"=>"updateDate","width"=>170)
			,"bon_de_commande.date_installation_prevue"=>array("renderer"=>"updateDate","width"=>170)
			,"bon_de_commande.date_installation_reele"=>array("renderer"=>"updateDate","width"=>170)
			,"bon_de_commande.date_limite_rav"=>array("renderer"=>"updateDate","width"=>170)
		);
		$this->colonnes["bloquees"]["insert"] =
		$this->colonnes["bloquees"]["update"] = array("ref","id_user","date_livraison_prevision","date_installation_prevision","date_reception_prevision","livraison_partielle","date_reception_fournisseur","date_livraison","date_installation","date_pv_install","factureFournisseur",'date_livraison_estime','date_livraison_prevue','date_livraison_reelle','date_installation_prevue','date_installation_reele','date_limite_rav');
		$this->colonnes["bloquees"]["update"][] = "date";

		$this->colonnes['primary'] = array(
			"ref"
			,"id_societe"=>array("disabled"=>true)
			,"id_affaire"=>array("disabled"=>true)
			,"id_commande"=>array("disabled"=>true)
			,"bon_de_commande"
			,"id_fournisseur"=>array(
				"autocomplete"=>array(
					"function"=>"autocompleteFournisseursDeCommande"
					,"mapping"=>array(
						array('name'=> 'id_contact_signataire', 'mapping'=> 0)
						,array('name'=> 'id_contact_signataire_fk', 'mapping'=> 1)
						,array('name'=>'id', 'mapping'=> 2)
						,array('name'=> 'nom', 'mapping'=> 3)
						,array('name'=> 'detail', 'mapping'=> 4, 'type'=>'string' )
						,array('name'=> 'nomBrut', 'mapping'=> 'raw_3')
					)
				)
			)
			,"id_contact"=>array(
				"autocomplete"=>array(
					"function"=>"autocompleteAvecMail"
					,"mapping"=>array(
						array('name'=> 'email', 'mapping'=> 0)
						,array('name'=>'id', 'mapping'=> 1)
						,array('name'=> 'nom', 'mapping'=> 2)
						,array('name'=> 'detail', 'mapping'=> 3, 'type'=>'string' )
						,array('name'=> 'nomBrut', 'mapping'=> 'raw_2')
					)
				)
			)
			,'factureFournisseur'=>array("custom"=>true,"nosort"=>true,"align"=>"center")
			,"num_bdc"
			,"id_magasin"
			,"commentaire"=>array("xtype"=>"textarea")
		);
		$this->colonnes['panel']['commande_lignes'] = array(
			"commandes"=>array("custom"=>true)
		);

		$this->colonnes['panel']['total'] = array(
			"prix"=>array("custom"=>true,"formatNumeric"=>true,"xtype"=>"textfield","null"=>true)
		);

		$this->colonnes['panel']['total_cleodis'] = array(
			"prix_cleodis"=>array("custom"=>true,"formatNumeric"=>true,"xtype"=>"textfield","null"=>true)
		);

		// Blocs montant/état/dates
		$this->colonnes['panel']['statut'] = array(
			"montant"=>array("custom"=>true,'xtype'=>'fieldset','panel_key'=>'montant_fs')
			,"dates"=>array("custom"=>true,'xtype'=>'fieldset','panel_key'=>'dates_fs')
		);

		// Bloc montant/état
		$this->colonnes['panel']['montant_fs'] = array(
			"tva"
			,"etat"
			,"payee"
		);

		// Bloc dates importantes
		$this->colonnes['panel']['dates_fs'] = array(
			 "date"
			,"date_livraison_demande"
			,"date_installation_demande"
		);

		// Blocs Adresses
		$this->colonnes['panel']['adresses'] = array(
			"adresse_complete"=>array("custom"=>true,'xtype'=>'fieldset','panel_key'=>'livraison_adresse_finale_fs')
			,"livraison_adresse_complete"=>array("custom"=>true,'xtype'=>'fieldset','panel_key'=>'livraison_adresse_intermediaire_fs')
		);

		// Bloc adresse postale
		$this->colonnes['panel']['livraison_adresse_finale_fs'] = array(
			"destinataire"
			,"adresse"
			,"adresse_2"
			,"adresse_3"
			,"cp_ville"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
				"cp"
				,"ville"
			))
			,"id_pays"
		);

		// Bloc adresse de livraison
		$this->colonnes['panel']['livraison_adresse_intermediaire_fs'] = array(
				"id_fournisseur_intermediaire"=>array("custom"=>true,"null"=>true,"autocomplete"=>array(
				"function"=>"autocompleteAvecAdresse"
					,"mapping"=>array(
						array('name'=> 'adresse', 'mapping'=> 0)
						,array('name'=>'ville', 'mapping'=> 1)
						,array('name'=>'cp', 'mapping'=> 2)
						,array('name'=>'id', 'mapping'=> 3)
						,array('name'=> 'nom', 'mapping'=> 4)
						,array('name'=> 'detail', 'mapping'=> 5, 'type'=>'string' )
						,array('name'=> 'nomBrut', 'mapping'=> 'raw_2')
					)
			))
			,"livraison_destinataire"
			,"livraison_adresse"
			,"livraison_cp_ville"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
				"livraison_cp"
				,"livraison_ville"
			))
		);

		$this->colonnes['panel']['courriel'] = array(
			"email"=>array("custom"=>true,'null'=>true)
			,"emailCopie"=>array("custom"=>true,'null'=>true)
			,"emailTexte"=>array("custom"=>true,'null'=>true,"xtype"=>"htmleditor")
		);

		// Propriété des panels
		$this->panels['commande_lignes'] = array('nbCols'=>1,'visible'=>true);
		$this->panels['total'] = array("visible"=>true,'nbCols'=>1);
		$this->panels['total_cleodis'] = array("visible"=>true, 'nbCols'=>1);
		$this->panels['statut'] = array('nbCols'=>2,'visible'=>true);
		$this->panels['dates_fs'] = array('nbCols'=>1,'visible'=>true,'isSubPanel'=>true);
		$this->panels['montant_fs'] = array('nbCols'=>1,'visible'=>true,'isSubPanel'=>true);
		$this->panels['adresses'] = array('nbCols'=>2,'visible'=>true);
		$this->panels['livraison_adresse_finale_fs'] = array('nbCols'=>1,'visible'=>true,'isSubPanel'=>true);
		$this->panels['livraison_adresse_intermediaire_fs'] = array('nbCols'=>1,'visible'=>true,'isSubPanel'=>true);
		$this->panels['courriel'] = array('nbCols'=>2,"checkboxToggle"=>true);

		// Ne pas afficher sur le select les panels spécifiques aux insert/update
		$this->colonnes['bloquees']['select'] =  array_merge(
			array_keys($this->colonnes['panel']['commande_lignes']),
			array_keys($this->colonnes['panel']['courriel'])
		);

		$this->fieldstructure();

		$this->addPrivilege("updateDate");
		$this->addPrivilege("setInfos","update");

		$this->noTruncateSA = true;
		$this->no_insert = true;
		$this->no_update = true;
		$this->field_nom = "ref";
		$this->onglets = array('bon_de_commande_ligne','facture_fournisseur','facture_non_parvenue');
		$this->foreign_key['id_fournisseur'] =  "societe";
		$this->foreign_key['id_fournisseur_intermediaire'] =  "societe";
		$this->files["fichier_joint"] = array("type"=>"pdf","preview"=>true);
		$this->files["pdf"] = array("type"=>"pdf","no_upload"=>true,"no_generate"=>true);
		$this->can_insert_from = array("commande");
		$this->selectAllExtjs=true;


	}


	/**
	 * Permet de modifier un champs en AJAX
	 * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	 * @return bool
	 */
	public function setInfos($infos){
		$res = $this->u(array("id_bon_de_commande"=> $this->decryptId($infos["id_bon_de_commande"]),
						  $infos["field"] => $infos[$infos["field"]])
					);
		if($res){
			ATF::$msg->addNotice(
				loc::mt(ATF::$usr->trans("notice_update_success"))
				,ATF::$usr->trans("notice_success_title")
			);
		}
	}

	public function uploadFileFromSA(&$infos,&$s,$files=NULL,&$cadre_refreshed=NULL){
		$infos['display'] = true;
		$class = ATF::getClass($infos['extAction']);
		if (!$class) return false;
		if (!$infos['id']) return false;
		if (!$files) return false;

		$id = $class->decryptID($infos['id']);

		$id_affaire = $class->select($id, "id_affaire");

		foreach ($files as $k=>$i) {
			if (!$i['size']) return false;
			$id_pdf_affaire = ATF::pdf_affaire()->insert(array("id_affaire"=>$id_affaire, "provenance"=>ATF::$usr->trans($class->name(), "module")." ".$k." ref : ".$infos['extAction']." ".$class->select($id, "ref")));
			$this->store($s,$id,$k,$i);

			copy($class->filepath($id,$k), ATF::pdf_affaire()->filepath($id_pdf_affaire,"fichier_joint"));
		}
		ATF::$cr->block('generationTime');
		ATF::$cr->block('top');



		$o = array ('success' => true );
		return json_encode($o);
	}

	/**
    * Permet de mettre a jour une date en ajax
    * @author Quentin JANON <qjanon@absystech.fr>
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos
	* @param array &$s La session
	* @param array &$request Paramètres disponibles (clés étrangères)
	* @return bool
    */
	public function updateDate($infos,&$s,&$request){

		if (!$infos['id_bon_de_commande']) return false;

		$infos["id_bon_de_commande"] = $this->decryptId($infos["id_bon_de_commande"]);

		if ($infos['value'] == "undefined") $infos["value"] = "";
		switch ($infos['key']) {
			default:
				$d = array("id_bon_de_commande"=>$infos['id_bon_de_commande']
					   ,$infos['key']=>($infos['value']?date("Y-m-d",strtotime($infos['value'])):NULL)
					);
				$this->u($d);
			break;
		}



		return true;
	}



	/**
	* Surcharge du select-All
	*/
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){
		$this->q
			->addJointure("bon_de_commande","id_bon_de_commande","facture_fournisseur","id_bon_de_commande")
			->addJointure("bon_de_commande","id_commande","commande","id_commande")
			->addField("(`bon_de_commande`.`prix`*`bon_de_commande`.`tva`) - (SUM(
																					IF(
																						(`facture_fournisseur`.`prix`)
																						,(`facture_fournisseur`.`prix`*`facture_fournisseur`.`tva`)
																						,0)
																					)
													)"
													,"solde")
			->addField("`bon_de_commande`.`prix` - (SUM(
														IF(
															(`facture_fournisseur`.`prix`)
															,`facture_fournisseur`.`prix`
															,0)
														)
													)"
													,"solde_ht")
			->addGroup("bon_de_commande.id_bon_de_commande");

		$return = parent::select_all($order_by,$asc,$page,$count);
		foreach ($return['data'] as $k=>$i) {
			//if ($i["solde_ht"]>0 || !$i["solde_ht"]) {
				$return['data'][$k]['factureFournisseurAllow'] = true;
			//}

			if (ATF::parc()->parcByBdc($i['bon_de_commande.id_bon_de_commande'])) {
				$return['data'][$k]['parcInsertionAllow'] = true;
			}
		}
		return $return;
	}

	/**
	* Impossible de supprimer un bon de commande qui a une facture fournisseur
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param int $id
	* @return boolean
	*/
	public function can_delete($id){
		$bdc=$this->select($id);
		$affaire = new affaire_lm($bdc['id_affaire']);

		//On ne doit pas pouvoir modifier une affaire Annulée et remplacée
		ATF::commande()->checkUpdateAR($affaire);

		ATF::affaire()->q->reset()->addCondition("id_parent",$affaire->get("id_affaire"))->setDimension("row");
		$affaireEnfant=ATF::affaire()->sa();
		//On ne peux pas modifier une affaire qui est parente d'une autre commande
		if($affaireEnfant){
			ATF::commande()->checkUpdateAVT($affaireEnfant);
		}

		ATF::facture_fournisseur()->q->reset()->addCondition("id_bon_de_commande",$id)->setCount();
		$count=ATF::facture_fournisseur()->sa();
		if($count["count"]>0){
			throw new errorATF("Impossible de modifier/supprimer ce ".ATF::$usr->trans($this->table)." car il y a une ".ATF::$usr->trans("facture_fournisseur")." liée.",884);
		}else{
			return true;
		}
	}

	public function can_update($id,$infos=false){
		return $this->can_delete($id);
	}

	/**
    * Retourne la valeur par défaut spécifique aux données des formulaires
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $field
	* @param array &$s La session
	* @param array &$request Paramètres disponibles (clés étrangères)
	* @return string
    */
	public function default_value($field,&$s,&$request){
		if ($id_commande = ATF::_r('id_commande')) {
			$commande=ATF::commande()->select($id_commande);
		}

		switch ($field) {
			case "id_societe":
				return $commande[$field];
				break;
			case "id_affaire":
				return $commande[$field];
				break;
			case "bon_de_commande":
				return $commande["commande"]." - ".ATF::societe()->select($commande["id_societe"], "code_client");
				break;
			case "emailCopie":
				return ATF::$usr->get("email");
				break;
			case "emailTexte":
				return $this->majMail($commande['id_societe']);
				break;
			case "tva":
				return $commande[$field];
				break;
			case "date":
				return date("Y-m-d");
				break;
			case "destinataire":
				return ATF::societe()->nom($commande['id_societe']);
				break;
			case "adresse":
				return ATF::societe()->select($commande['id_societe'],$field);
				break;
			case "adresse_2":
				return ATF::societe()->select($commande['id_societe'],$field);
				break;
			case "adresse_3":
				return ATF::societe()->select($commande['id_societe'],$field);
				break;
			case "ville":
				return ATF::societe()->select($commande['id_societe'],$field);
				break;
			case "cp":
				return ATF::societe()->select($commande['id_societe'],$field);
				break;
			case "id_pays":
				return ATF::societe()->select($commande['id_societe'],$field);
				break;
			}

		return parent::default_value($field,$s,$request);
	}

	/**
    * Retourne la valeur du texte d'email, appelé en Ajax
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param int $id_societe
	* @return string texte du mail
    */
	public function majMail($id_societe){
		return nl2br("Bonjour,\n\nCi-joint le bon de commande pour la société ".ATF::societe()->nom($id_societe).".\nBon de commande effectué le ".date("d/m/Y").".\n");
	}


	/**
    * Retourne la référence du bon de commande à partir de son id_fournisseur et id_affaire
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $id_affaire
	* @param int $id_fournisseur
	* @return string
    */
	function getRef($id_affaire,$id_fournisseur){
		$code_four=ATF::societe()->select($id_fournisseur,"code_fournisseur");

		if(!$code_four){
			throw new errorATF("Il doit y avoir un code fournisseur pour ".ATF::societe()->nom($id_fournisseur),880);
			return false;
		}

		$prefix=$code_four."-".ATF::affaire()->select($id_affaire,"ref")."-";

		$this->q->reset()
			->addField("ROUND(SUBSTRING(`ref`,".(strlen($prefix)+1)."))","ref_reel")
			->addCondition("ref",$prefix."%","AND",false,"LIKE")
			->addOrder('ref_reel',"DESC")
			->setDimension("row")
			->setLimit(1);

		$nb=$this->sa();

		if($nb["ref_reel"]){
			$suffix=$nb["ref_reel"]+1;
		}else{
			$suffix="1";
		}

		return $prefix.$suffix;
	}

	/**
	* Ajoute
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
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
					$conditions["condition_value"][] = ATF::commande()->decryptId($infos["id_commande"]);
				}
				break;
		}
		return array_merge_recursive((array)($conditions),parent::autocompleteConditions($class,$infos,$condition_field,$condition_value));
	}


//	private function getArrayCommandeLigne($infos){
//		$infos_explode = explode(",",$infos);
//		foreach($infos_explode as $key => $item){
//			if(strpos($item,"parc_")===0){
//				$parc=str_replace("parc_","",$item);
//				$return["parc"][]=$parc;
//			}elseif(strpos($item,"affaire_")===0){
//				$affaire=str_replace("affaire_","",$item);
//				$return["affaire"][]=$affaire;
//			}
//		}
//
//		//Si aucune affaire sélectionné
//		if(!$affaire){
//			throw new errorATF(ATF::$usr->trans("parc_sans_".$type),879);
//		//Si c'est un avenant il ne peut y avoir qu'une affaire parente
//		}elseif(count($return["affaire"])>1 && $type=="avenant"){
//			throw new errorATF(ATF::$usr->trans("une_affaire_par_avenant"),878);
//		}else{
//			return $return;
//		}
//	}
//

	/**
	* Surcharge de l'insert afin d'insérer les lignes du bon de commande et d'nvoyer un mail
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	*/
	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		if(isset($infos["preview"])){
			$preview=$infos["preview"];
		}else{
			$preview=false;
		}

		//Gestion ligne de bon de commande
		if($infos["commandes"]){
			$infos_bon_de_commande_ligne=explode(",",$infos["commandes"]);
			//On supprime l'élément correspondant à l'id_commande (car on ne garde que les id_bon_de_commande)
			unset($infos_bon_de_commande_ligne[0]);
		}else{
			throw new errorATF("Il faut sélectionner des lignes de bon de commande.",875);
		}

		$envoyerEmail = $infos["panel_courriel-checkbox"];
		$this->infoCollapse($infos);

		//Si le fournisseur est LM, il faut un magasin
		if($infos["id_fournisseur"] == 2 && !$infos["id_magasin"]){
			throw new errorATF("Il faut le magasin sur lequel la commande Leroy Merlin a été passée",500);
		}

		//Gestion mail
		if($envoyerEmail){
			$email["email"]=$infos["email"];
			$email["emailCopie"]=$infos["emailCopie"];
			$email["texte"]=$infos["emailTexte"];
		}else{
			$email=false;
		}

		unset($infos["email"],$infos["emailCopie"],$infos["emailTexte"],$infos["AR_societe"],$infos["id_fournisseur_intermediaire"]);
		$infos["id_user"] = ATF::$usr->getID();
		$societe=ATF::societe()->select($infos["id_societe"]);
		$infos["id_societe"] = $societe["id_societe"];
		$infos["id_fournisseur"] = ATF::societe()->decryptId($infos["id_fournisseur"]);

		//Cleodis & Cleofi ont la possibilité de choisir le prix
		$societe=ATF::societe()->nom($infos["id_fournisseur"]);
		if($societe=="CLEODIS" || $societe=="CLEOFI") $infos["prix"]=$infos["prix_cleodis"];
		unset($infos["prix_cleodis"]);
		$infos["ref"] = $this->getRef($infos["id_affaire"],$infos["id_fournisseur"]);
		//$infos["date"] = date("Y-m-d");


		$infos['etat'] = 'envoyee';


		//Vérification du bon de commande
		$this->check_field($infos);

		ATF::db($this->db)->begin_transaction();

//*****************************Transaction********************************

		$last_id = parent::insert($infos,$s,NULL,$var=NULL,NULL,true);

		$prix_total = 0;
		foreach($infos_bon_de_commande_ligne as $key=>$item){
			$commande_ligne=ATF::commande_ligne()->select($item);
			$bon_de_commande_ligne["id_commande_ligne"]=$item;
			$bon_de_commande_ligne["id_bon_de_commande"]=$last_id;
			$bon_de_commande_ligne["ref"]=$commande_ligne["ref"];
			$bon_de_commande_ligne["produit"]=$commande_ligne["produit"];
			$bon_de_commande_ligne["quantite"]=$commande_ligne["quantite"];
			$bon_de_commande_ligne["prix"]=$commande_ligne["prix_achat"];
			$prix_total += $bon_de_commande_ligne["prix"]*$bon_de_commande_ligne["quantite"];
			ATF::bon_de_commande_ligne()->i($bon_de_commande_ligne);
		}

		if($societe=="CLEODIS" || $societe=="CLEOFI"){
			if($prix_total != $infos["prix"]){
				$prix_total=$infos["prix"];
			}
		}

		// Ajout de la facture non parvenue globale du bon de commande
		ATF::facture_non_parvenue()->i(array(
			'ref'=>$infos['ref']."-FNP"
			,'prix'=>$prix_total // Valeur positive
			,'id_affaire'=>$infos["id_affaire"]
			,'tva'=>$infos["tva"]
			,'id_bon_de_commande'=>$last_id
		));

//*****************************************************************************
		if($preview){
			$this->move_files($last_id,$s,true,$infos["filestoattach"]); // Génération du PDF de preview
			ATF::db($this->db)->rollback_transaction();
			return $this->cryptId($last_id);
		}else{
			$this->move_files($last_id,$s,false,$infos["filestoattach"]); // Génération du PDF avec les lignes dans la base

			/* MAIL */
			//Seulement si le profil le permet
			if($email){
				$path=array("CommandeFournisseur"=>"fichier_joint");
				ATF::affaire()->mailContact($email,$last_id,"devis",$path);
			}
			ATF::db($this->db)->commit_transaction();
		}

		if(is_array($cadre_refreshed)){
			ATF::affaire()->redirection("select",$infos["id_affaire"]);
		}
		return $last_id;

	}


	/**
	* Surcharge de delete
	* @author mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param int $infos le ou les identificateurs de l'élément que l'on désire inséré
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	*/
	public function delete($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL) {
		if (is_numeric($infos) || is_string($infos)) {
			$id=$this->decryptId($infos);
			$bon_de_commande=$this->select($id);

			//Commande
			if($bon_de_commande){
//*****************************Transaction********************************
				ATF::db($this->db)->begin_transaction();

				ATF::facture_non_parvenue()->q->reset()->addCondition("id_bon_de_commande",$bon_de_commande["id_bon_de_commande"]);
				$facture_non_parvenue=ATF::facture_non_parvenue()->sa();

				foreach($facture_non_parvenue as $key=>$item){
					ATF::facture_non_parvenue()->d($item["id_facture_non_parvenue"]);
				}

				ATF::bon_de_commande_ligne()->q->reset()->addCondition("id_bon_de_commande",$bon_de_commande["id_bon_de_commande"]);
				$bon_de_commande_ligne=ATF::bon_de_commande_ligne()->sa();

				foreach($bon_de_commande_ligne as $key=>$item){
					$commande_ligne=ATF::commande_ligne()->select($item["id_commande_ligne"]);
					if($commande_ligne["serial"] && !$commande_ligne["id_affaire_provenance"]){
						$parcs=explode(" ",$commande_ligne["serial"]);
						ATF::commande_ligne()->u(array("id_commande_ligne"=>$item["id_commande_ligne"],"serial"=>NULL));
						foreach($parcs as $k=>$i){
							ATF::parc()->q->reset()->addCondition("serial",$i)->setDimension("row");
							$parc=ATF::parc()->sa();
							if(!$parc["provenance"]){
								ATF::parc()->d($parc["id_parc"]);
							}
						}
					}
				}

				parent::delete($id,$s);


				ATF::db($this->db)->commit_transaction();
	//*****************************************************************************

				ATF::affaire()->redirection("select",$bon_de_commande["id_affaire"]);

				return true;
			}
		} elseif (is_array($infos) && $infos) {

			foreach($infos["id"] as $key=>$item){
				$this->delete($item,$s,$files,$cadre_refreshed);
			}
		}
	}


	/**
	* Permet de savoir si toutes les lignes d'une commande sont passées en bon de commande
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param int $id
	* @return boolean
	*/
	public function bdcByAffaire($id_commande){
		ATF::commande_ligne()->q->reset()->addCondition("id_commande",$id_commande);
		$commande_ligne=ATF::commande_ligne()->sa();
		$nb_commande_ligne=0;
		foreach($commande_ligne as $key=>$item){
			ATF::bon_de_commande_ligne()->q->reset()->addCondition("id_commande_ligne",$item["id_commande_ligne"]);
			if($bon_de_commande_ligne=ATF::bon_de_commande_ligne()->sa()){
				$nb_commande_ligne++;
			}
		}

		if($nb_commande_ligne==count($commande_ligne)){
			return true;
		}else{
			return false;
		}
	}

};
?>