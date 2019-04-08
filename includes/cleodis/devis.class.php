<?
/** Classe devis
* @package Optima
* @subpackage Cléodis
*/
require_once dirname(__FILE__)."/../devis.class.php";
class devis_cleodis extends devis {
	function __construct($table_or_id=NULL) {
		$this->table ="devis";
		parent::__construct($table_or_id);

		$this->colonnes['fields_column'] = array(
			 'devis.ref'
			,'devis.id_societe'
			,'devis.id_affaire'
			,'devis.devis'
			,'devis.etat'=>array("renderer"=>"etat","width"=>30)
			,'devis.type_contrat'
            ,'devis.date'
            ,'devis.first_date_accord'
			,'fichier_joint'=>array("custom"=>true,"nosort"=>true,"type"=>"file","align"=>"center","width"=>70)
			,'retourBPA'=>array("custom"=>true,"nosort"=>true,"type"=>"file","align"=>"center","renderer"=>"uploadFile","width"=>70)
 			,'devis_etendre'=>array("custom"=>true,"nosort"=>true,"align"=>"center","renderer"=>"devisExpand","width"=>50)
			,'perdu'=>array("custom"=>true,"nosort"=>true,"align"=>"center","renderer"=>"devisPerdu","width"=>50)
			,'comite'=>array("custom"=>true,"nosort"=>true,"align"=>"center","renderer"=>"comiteExpand","width"=>50)
		);

		$this->colonnes['primary'] = array(
			"ref"
			,"id_societe"=>array("disabled"=>true)
			,"id_affaire"
			,"devis"
			,"etat"
			,"id_filiale"=>array("autocomplete"=>array(
				"function"=>"autocompleteAvecFiliale"
				,"mapping"=>array(
					array('name'=>'id', 'mapping'=> 1)
					,array('name'=> 'nom', 'mapping'=> 2)
					,array('name'=> 'detail', 'mapping'=> 3, 'type'=>'string' )
					,array('name'=> 'tva', 'mapping'=> 'raw_0')
					,array('name'=> 'nomBrut', 'mapping'=> 'raw_3')
				)
			))
			,"date"
			,"type_contrat"=>array("custom"=>true,"data"=>array("lld","lrp","presta","vente","cout_copie"),"xtype"=>"combo")
			,"type_devis"
			,"validite"
			,"id_contact"=>array(
				"obligatoire"=>true,
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
			,"id_opportunite"
			,"loyer_unique"=>array("enable"=>false)
			,"tva"=>array("readonly"=>true)
			,"id_user"
			,'devis_etendre'=>array("custom"=>true,"nosort"=>true,"align"=>"center")
			,'perdu'=>array("custom"=>true,"nosort"=>true,"align"=>"center")
			,"type_affaire"=>array("custom"=>true,"data"=>array("normal","2SI"),"xtype"=>"combo")
			,"langue"=>array("custom"=>true,"data"=>array("FR","NL"),"xtype"=>"combo")

		);

		$this->colonnes['panel']['partenaire'] = array(
			"commentaire_offre_partenaire",
			"offre_partenaire"
		);


		$this->colonnes['panel']['facturation'] = array(
			"RIB"=>array("custom"=>true,"null"=>true)
			,"IBAN"=>array("custom"=>true,"null"=>true)
			,"BIC"=>array("custom"=>true,"null"=>true)
			,"nom_banque"=>array("custom"=>true,"null"=>true)
			,"ville_banque"=>array("custom"=>true,"null"=>true)
		);

		$this->colonnes['panel']['loyer_lignes'] = array(
			"loyer"=>array("custom"=>true)
		);

		$this->colonnes['panel']['loyer_uniques'] = array(
			"loyers"=>array("custom"=>true,"formatNumeric"=>true,"xtype"=>"textfield","default"=>"0.00")
			,"frais_de_gestion_unique"=>array("custom"=>true,"formatNumeric"=>true,"xtype"=>"textfield","default"=>"0.00")
			,"assurance_unique"=>array("custom"=>true,"formatNumeric"=>true,"xtype"=>"textfield","default"=>"0.00")
		);

		$this->colonnes['panel']['vente'] = array(
			"prix_vente"=>array("custom"=>true,"formatNumeric"=>true,"xtype"=>"textfield")
			,"vente_societe"=>array("custom"=>true)
			,"vente"=>array("custom"=>true)
		);

		$this->colonnes['panel']['avenant_lignes'] = array(
			"avenant"=>array("custom"=>true)
		);

		$this->colonnes['panel']['AR'] = array(
			"AR_societe"=>array("custom"=>true)
			,"AR"=>array("custom"=>true)
		);

		$this->colonnes['panel']['lignes_repris'] = array(
			"produits_repris"=>array("custom"=>true)
		);

		$this->colonnes['panel']['lignes'] = array(
			"produits"=>array("custom"=>true)
		);

		$this->colonnes['panel']['lignes_non_visible'] = array(
			"produits_non_visible"=>array("custom"=>true)
		);

		$this->colonnes['panel']['total'] = array(
			"prix"=>array("custom"=>true,"readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield","null"=>true)
			,"prix_achat"=>array("custom"=>true,"readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield","null"=>true)
			,"marge"=>array("custom"=>true,"readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield","null"=>true)
			,"marge_absolue"=>array("custom"=>true,"readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield","null"=>true)
		);

		$this->colonnes['panel']['commentaire'] = array(
			"commentaire_facture"=>array("custom"=>true,"xtype"=>"textfield"),
			"commentaire_facture2"=>array("custom"=>true,"xtype"=>"textfield"),
			"commentaire_facture3"=>array("custom"=>true,"xtype"=>"textfield")

		);

		$this->colonnes['panel']['courriel'] = array(
			"email"=>array("custom"=>true,'null'=>true)
			,"emailCopie"=>array("custom"=>true,'null'=>true)
			,"emailTexte"=>array("custom"=>true,'null'=>true,"xtype"=>"htmleditor")
		);
//
//		$this->colonnes['panel']['dates'] = array(
//			"date_accord"
//			,"validite"
//		);

		// Propriété des panels
		$this->panels['partenaire'] = array('nbCols'=>2,'visible'=>false);
		$this->panels['facturation'] = array('nbCols'=>3,'visible'=>true);
		$this->panels['loyer_uniques'] = array("visible"=>true, 'nbCols'=>3,"hidden"=>true);
		$this->panels['loyer_lignes'] = array("visible"=>true, 'nbCols'=>1);
		$this->panels['AR'] = array('nbCols'=>1,"checkboxToggle"=>true);
		$this->panels['avenant_lignes'] = array('nbCols'=>1,"checkboxToggle"=>true);
		$this->panels['vente'] = array('nbCols'=>1,"hidden"=>true,"checkboxToggle"=>true);
		$this->panels['lignes_repris'] = array("visible"=>true, 'nbCols'=>1);
		$this->panels['lignes'] = array("visible"=>true, 'nbCols'=>1);
		$this->panels['lignes_non_visible'] = array("visible"=>false, 'nbCols'=>1);
		$this->panels['total'] = array("visible"=>true,'nbCols'=>4);
		$this->panels['courriel'] = array('nbCols'=>2,"checkboxToggle"=>true);
		$this->panels['commentaire'] = array('nbCols'=>1);

		$this->colonnes['bloquees']['insert'] =
		$this->colonnes['bloquees']['clone'] =
		$this->colonnes['bloquees']['update'] =  array_merge(array('first_date_accord','date_accord','ref','etat','id_user','id_affaire','devis_etendre','perdu'));


		// Ne pas afficher sur le select les panels spécifiques aux insert/update
		$this->colonnes['bloquees']['select'] =  array_merge(
			array_keys($this->colonnes['panel']['loyer_uniques'])
			,array_keys($this->colonnes['panel']['avenant_lignes'])
			,array_keys($this->colonnes['panel']['AR'])
			,array_keys($this->colonnes['panel']['loyer_lignes'])
			,array_keys($this->colonnes['panel']['lignes_repris'])
			,array_keys($this->colonnes['panel']['lignes'])
			,array_keys($this->colonnes['panel']['lignes_non_visible'])
			,array_keys($this->colonnes['panel']['total'])
			,array_keys($this->colonnes['panel']['courriel'])
		);

		$this->fieldstructure();

		$this->foreign_key["id_apporteur"] = "societe";
		$this->foreign_key["id_fournisseur"] = "societe";
		$this->foreign_key["id_filiale"] = "societe";
		$this->foreign_key["AR_societe"] = "societe";
		$this->foreign_key["vente_societe"] = "societe";

		$this->onglets = array('devis_ligne');
		$this->sans_partage = true; /* Evite de se voir jeté à cause d'un droit de partage pour ce module */
		$this->field_nom = "ref";

		$this->noTruncateSA = true;

		$this->files["fichier_joint"] = array("type"=>"pdf","preview"=>true);
		$this->files["retourBPA"] = array("type"=>"pdf","preview"=>false,"no_upload"=>true,"no_generate"=>true);

		$this->addPrivilege("perdu","update");
		$this->addPrivilege("majMail","update");
		$this->addPrivilege("extjs","update");
		$this->addPrivilege("uploadFile","update");
		$this->addPrivilege("export_devis_loyer");

		$this->formExt=true;
		$this->no_insert = true;
		$this->no_delete = true;
		$this->selectAllExtjs=true;
	}

	/**
	* Méthode qui retourne un tableau de parc avec pour key l'id_affaire dans le cadre des treePanel dans l'insertion de devis
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $infos tableau de parc
	* @param array $type permet de distinguer les avenants
	*/
	private function getArrayAvenantARVente($infos,$type){
		$infos_explode = explode(",",$infos);
		foreach($infos_explode as $key => $item){
//			if(strpos($item,"parc_")===0){
//				$parc=str_replace("parc_","",$item);
//				$return["parc"][]=$parc;
//			}else
			if(strpos($item,"affaire_")===0){
				$affaire=str_replace("affaire_","",$item);
				$return["affaire"][]=$affaire;
			}
		}

		//Si aucune affaire sélectionné (seul une vente peut être fait sans faire référence à une affaire)
		if(!$affaire && $type!="vente"){
			throw new errorATF(ATF::$usr->trans("parc_sans_".$type),879);
		//Si c'est un avenant il ne peut y avoir qu'une affaire parente (sauf pour les AR)
		}elseif(count($return["affaire"])>1 && ($type=="avenant" /*|| $type=="vente"*/)){
			throw new errorATF("Il ne peut y avoir qu'une affaire reprise par ".$type,878);
		}else{
			return $return;
		}
	}

	/**
	* Surcharge de l'insert afin d'insérer les lignes de devis de créer l'affaire si elle n'existe pas
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

		$infos_ligne_repris = json_decode($infos["values_".$this->table]["produits_repris"],true);
		$infos_ligne_non_visible = json_decode($infos["values_".$this->table]["produits_non_visible"],true);
		$infos_ligne = json_decode($infos["values_".$this->table]["produits"],true);
		$infos_loyer = json_decode($infos["values_".$this->table]["loyer"],true);

		//Gestion AR/Avenant : soit l'un soit l'autre
		if($infos["panel_AR-checkbox"]){
			$infos_AR=$this->getArrayAvenantARVente($infos["AR"],"AR");
		}elseif($infos["panel_avenant_lignes-checkbox"]){
			$infos_avenant=$this->getArrayAvenantARVente($infos["avenant"],"avenant");
		}elseif($infos["panel_vente-checkbox"]){
			$infos_vente=$this->getArrayAvenantARVente($infos["vente"],"vente");
		}

		$envoyerEmail = $infos["panel_courriel-checkbox"];
		$this->infoCollapse($infos);

		//Gestion mail
		if($envoyerEmail){
			$email["email"]=$infos["email"];
			$email["emailCopie"]=$infos["emailCopie"];
			$email["texte"]=$infos["emailTexte"];
		}else{
			$email=false;
		}

		unset($infos["email"],$infos["emailCopie"],$infos["emailTexte"],$infos["AR_societe"]);
		if(!$infos["id_user"]){ $infos["id_user"] = ATF::$usr->getID(); }
		$societe=ATF::societe()->select($infos["id_societe"]);
		$infos["id_societe"] = $societe["id_societe"];

		//Vérification du devis
		if ($infos["ref"]) { // Dans le cas d'une modification, il faut conerver la ref d'affaire !
			$this->check_field($infos);
		} else {
			$infos["ref"]="prob";
			$this->check_field($infos);
			$infos["ref"]=NULL;
		}

		if($infos["loyer_unique"]=="oui"){
			if(!$infos_avenant){
				throw new errorATF("Un loyer unique doit être un avenant.",881);
			}else{
				$loyer_unique["loyer"]=$infos_loyer[0]["loyer__dot__loyer"];
				$loyer_unique["frais_de_gestion"]=$infos_loyer[0]["loyer__dot__frais_de_gestion"];
				$loyer_unique["assurance"]=$infos_loyer[0]["loyer__dot__assurance"];
				$loyer_unique["frequence_loyer"]=$infos_loyer[0]["loyer__dot__frequence_loyer"];
				unset($infos["loyers"],$infos["frais_de_gestion_unique"],$infos["assurance_unique"]);
			}
		}elseif($infos["type_contrat"]=="vente"){
			//Un contrat de vente doit forcément avoir un prix
			if($infos["prix_vente"]){
				$loyer_vente["loyer"]=$infos["prix_vente"];
				$loyer_vente["duree"]=1;
				$loyer_vente["frequence_loyer"]="mois";
				unset($infos["prix_vente"],$infos_loyer);
			}else{
				throw new errorATF("Il faut un prix pour ce contrat de vente.",880);
			}
		}elseif(!$infos_loyer){
			throw new errorATF("Il n'y a pas de loyer pour ce devis.",875);
		}

		////////////////Affaire
		if($infos_avenant){
			//Si avenant alors id_parent, comme on ne peut faire un avenant que sur une affaire on peut récupérer $infos_avenant["affaire"][0]
			$infos["id_parent"]=$infos_avenant["affaire"][0];
			//Dans le cadre d'un avenant on récupère la date garantie de l'affaire parente
			$infos["nature"]="avenant";
		}elseif($infos_AR){
			//Si AR
			$infos["nature"]="AR";
		}elseif($infos["type_contrat"]=="vente"){
			//Si Vente
			$infos["id_parent"]=$infos_vente["affaire"][0];
			$infos["nature"]="vente";
		}else{
			$infos["nature"]="affaire";
		}
		$affaire=ATF::affaire()->formateInsertUpdate($infos);


		ATF::db($this->db)->begin_transaction();
//*****************************Transaction********************************

		$RUM = "";
		if($infos["type_affaire"]) $affaire["type_affaire"] = $infos["type_affaire"];
		if($affaire["RIB"]){
			if($infos_AR){

				foreach ($infos_AR["affaire"] as $key => $value) {
					$RIB = ATF::affaire()->select($value, "RIB");
					if(ATF::affaire()->select($value, "RUM")){
						$affaire["RIB"] = str_replace(" ", "", $affaire["RIB"]);
						$RIB  = str_replace(" ", "", $RIB );

						if($RIB  == $affaire["RIB"]) $RUM =  ATF::affaire()->select($value, "RUM");
					}
				}
			}elseif ($infos_avenant){
				foreach ($infos_avenant["affaire"] as $key => $value) {
					$RIB = ATF::affaire()->select($value, "RIB");
					if(ATF::affaire()->select($value, "RUM")){
						$affaire["RIB"] = str_replace(" ", "", $affaire["RIB"]);
						$RIB  = str_replace(" ", "", $RIB );

						if($RIB == $affaire["RIB"])	$RUM =  ATF::affaire()->select($value, "RUM");
					}

				}
			}else{
				ATF::affaire()->q->reset()->where("affaire.id_societe" , $infos['id_societe']);
				$lesAffaires = ATF::affaire()->select_all();

				foreach ($lesAffaires as $key => $value) {
					$RIB = ATF::affaire()->select($value, "RIB");
					if(ATF::affaire()->select($value["affaire.id_affaire"], "RUM")){
						$affaire["RIB"] = str_replace(" ", "", $affaire["RIB"]);
						$RIB  = str_replace(" ", "", $infos["RIB"] );

						if($RIB  == $affaire["RIB"]) $RUM =  ATF::affaire()->select($value["affaire.id_affaire"], "RUM");
					}
				}
			}
		}
		if(!$RUM){
			if(ATF::societe()->select($infos['id_societe'], 'RUM')){
				$RUM = ATF::societe()->select($infos['id_societe'], 'RUM');
			}else{
				//Si il n'y a pas de RUM, on en ajoute un pour cette société
			    $RUM = ATF::societe()->create_rum();

			    $societe = ATF::societe()->select($infos['id_societe']);

				if($societe['code_client']){

					if(strlen($societe['code_client']) === 6){
						$RUM .= $societe['code_client'];
					}elseif(strlen($societe['code_client']) > 6){
						$RUM .= substr($societe['code_client'], -6);
					}else{
						for ($i=0; $i < 6 - strlen($societe['code_client']); $i++) {
							$RUM .= '0';
						}
						$RUM .= $societe['code_client'];
					}
				}else{
					$RUM .= '000000';
				}


			    ATF::societe()->u(array("id_societe"=>$infos['id_societe'] , "RUM"=>$RUM));
			}
		}
		$affaire["RUM"] = $RUM;

		$infos["id_affaire"]=ATF::affaire()->i($affaire,$s);
		$affaire=ATF::affaire()->select($infos["id_affaire"]);
		$infos["ref"]=$affaire["ref"];

		////////////////Opportunité
		if ($infos["id_opportunite"])	ATF::opportunite()->u(array('id_opportunite'=>$infos['id_opportunite'],'etat'=>'fini','id_affaire'=>$infos["id_affaire"]));

		////////////////Devis
		unset($infos["marge"],$infos["marge_absolue"],$infos["id_parent"],$infos["nature"],$infos["loyers"],$infos["frais_de_gestion_unique"],$infos["assurance_unique"],$infos["prix_vente"],$infos["date_garantie"],$infos["vente_societe"],$infos["BIC"],$infos["RIB"],$infos["IBAN"],$infos["nom_banque"],$infos["ville_banque"],$infos["type_affaire"],$infos["id_partenaire"],$infos["commentaire_facture"], $infos["commentaire_facture2"], $infos["commentaire_facture3"],$infos["langue"]);
		$last_id=parent::insert($infos,$s,NULL,$var=NULL,NULL,true);

		// Mise à jour du forecast
		$affaire = new affaire_cleodis($infos['id_affaire']);
		$affaire->majForecastProcess();

		////////////////Devis Ligne
		//Lignes reprise
		if($infos_ligne_repris){
			foreach($infos_ligne_repris as $key=>$item){
				$infos_ligne[]=$infos_ligne_repris[$key];
			}
		}

		if(ATF::$codename === 'cleodis' && $societe['id_famille'] != 9 && $infos["type_contrat"]!=="vente" && !$infos_avenant){
			//17492 - Frais de dossiers et frais de cession SGEF et BNP - ajouter en produits non visibles
			$produitSGEFBNP = array('REFI-CESSION-SGEF' ,'REFI-CESSION-BNP', 'REFI-ETUDE-SGEF', 'REFI-ETUDE-BNP');
		}else{
			$produitSGEFBNP = array();
		}


		//Lignes non visibles
		if($infos_ligne_non_visible){
			foreach($infos_ligne_non_visible as $key=>$item){
				$infos_ligne_non_visible[$key]["devis_ligne__dot__visible"]="non";
				$infos_ligne[]=$infos_ligne_non_visible[$key];
				foreach ($produitSGEFBNP as $kpsb => $vpsb) {
					if($infos_ligne_non_visible[$key]["devis_ligne__dot__ref"] === $vpsb){
						unset($produitSGEFBNP[$kpsb]);
					}
				}
			}
		}

		foreach ($produitSGEFBNP as $kpsb => $vpsb) {
			ATF::produit()->q->reset()->where("ref",$vpsb);
			$p = ATF::produit()->select_row();

            $infos_ligne[] = array( 'devis_ligne__dot__produit' => $p['produit'],
						            'devis_ligne__dot__quantite' => '1',
						            'devis_ligne__dot__type' => $p['type'],
						            'devis_ligne__dot__ref' => $p['ref'],
						            'devis_ligne__dot__prix_achat' => $p['prix_achat'],
						            'devis_ligne__dot__id_produit' => $p['id_produit'],
						            'devis_ligne__dot__id_fournisseur' => ATF::societe()->select($p['id_fournisseur'], 'societe'),
						            'devis_ligne__dot__visibilite_prix' => 'invisible',
						            'devis_ligne__dot__neuf' => 'oui',
						            'devis_ligne__dot__id_produit_fk' => $p['id_produit'],
						            'devis_ligne__dot__id_fournisseur_fk' => $p['id_fournisseur'],
						         	'devis_ligne__dot__visible'=> 'non');
		}

		//Lignes
		if($infos_ligne){
			$infos_ligne=$this->extJSUnescapeDot($infos_ligne,"devis_ligne");
			foreach($infos_ligne as $key=>$item){
				$item["id_devis"]=$last_id;
				if(!$item["id_fournisseur"]){
					ATF::db($this->db)->rollback_transaction();
					throw new errorATF("Ligne de devis sans fournisseur",882);
				}
				unset($item["id_parc"]);
				ATF::devis_ligne()->i($item);
			}
		}else{
			ATF::db($this->db)->rollback_transaction();
			throw new errorATF("Devis sans produits",877);
		}

		////////////////Parcs
		try {
			////////////////AR
			if($infos_AR){
				$this->AR($infos_ligne_repris,$infos_AR["affaire"],$infos['id_affaire']);
			}


			////////////////Avenant
			if($infos_avenant){
				$this->avenant($infos_ligne_repris,$infos_avenant["affaire"][0],$infos['id_affaire']);
			}


			////////////////Vente
			if($infos["type_contrat"]=="vente"){
				$this->vente($infos_ligne_repris,$infos_vente["affaire"],$infos['id_affaire']);
			}
		} catch (errorATF $e) {
			ATF::db($this->db)->rollback_transaction();
			throw $e;
		}

		////////////////Loyers
		if($infos["loyer_unique"]=="oui"){
			$this->loyer_unique($infos['id_affaire'],$infos_avenant["affaire"][0],$loyer_unique);
		}elseif($infos_loyer){
			foreach($infos_loyer as $key=>$item){
				foreach($item as $k=>$i){
					$k_unescape=util::extJSUnescapeDot($k);
					$item[str_replace("loyer.","",$k_unescape)]=$i;
					unset($item[$k]);
				}

				$item["id_affaire"]=$infos["id_affaire"];
				$item["index"]=util::extJSEscapeDot($key);
				unset($item["loyer_total"]);
				if($item["frequence_loyer"]){
					ATF::loyer()->i($item);
				}else{
					ATF::db($this->db)->rollback_transaction();
					throw new errorATF("Il n'y a pas de fréquence pour un loyer",876);
				}
			}
		}elseif($infos["type_contrat"]=="vente"){
			$loyer_vente["id_affaire"]=$infos["id_affaire"];
			ATF::loyer()->i($loyer_vente);
		}

//*****************************************************************************
		if($preview){
			$this->move_files($last_id,$s,true,$infos["filestoattach"]); // Génération du PDF de preview
			ATF::db($this->db)->rollback_transaction();
			return $this->cryptId($last_id);
		}else{
			$this->move_files($last_id,$s,false,$infos["filestoattach"]); // Génération du PDF avec les lignes dans la base

			if(ATF::societe()->select($societe["id_societe"] , "relation") == "suspect"){
				ATF::societe()->u(array("id_societe"=> $societe["id_societe"] , "relation"=>"prospect"));
			}

			/* MAIL */
			//Seulement si le profil le permet
			if($email){
				$path=array("devis"=>"fichier_joint");
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
	* corrige les lignes de devis
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $infos_ligne ligne de devis
	* @return array $infos_ligne_escapeDot ligne de devis corrigé
	*/
	public function extJSUnescapeDot($infos_ligne,$escape){
		foreach($infos_ligne as $key=>$item){
			foreach($item as $k=>$i){
				$k_unescape=util::extJSUnescapeDot($k);
				$item[str_replace($escape.".","",$k_unescape)]=$i;
				unset($item[$k]);
			}
			$item["id_fournisseur"]=ATF::societe()->decryptId($item["id_fournisseur_fk"]);
			$item["id_produit"]=ATF::produit()->decryptId($item["id_produit_fk"]);
			unset($item["id_fournisseur_fk"],$item["id_produit_fk"]);
			$item["index"]=util::extJSEscapeDot($key);
			if(!$item["quantite"]){
				$item["quantite"]=0;
			}
			$infos_ligne_escapeDot[]=$item;
		}
		return $infos_ligne_escapeDot;
	}

	/**
	* Surcharge de l'insert afin d'insérer les lignes de devis de créer l'affaire si elle n'existe pas
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	*/
	public function update($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		//Puisqu'une modification de devis ne peut se faire si ce devis n'est pas encore commandé on peut le supprimer et recommencer
		$devis=$this->select($infos["devis"]["id_devis"]);
		$affaire=ATF::affaire()->select($devis["id_affaire"]);

		foreach($this->files as $key=>$item){
			if($infos["devis"]["filestoattach"][$key]==="true"){
				$infos["devis"]["filestoattach"][$key]="";
			}
		}
		$infos["devis"]["first_date_accord"] = $devis["first_date_accord"];
		$infos["devis"]["date_accord"] = $devis["date_accord"];
		$infos["devis"]["id_user"] = $devis["id_user"];


		//Sauvegarder suivis et taches pour pouvoir les assignier à la nouvelle affaire
		ATF::suivi()->q->reset()
					   ->addCondition("id_affaire",$devis["id_affaire"]);
		$suivis=ATF::suivi()->sa();

		ATF::tache()->q->reset()
					   ->addCondition("id_affaire",$devis["id_affaire"]);
		$taches=ATF::tache()->sa();

		ATF::parc()->q->reset()
					   ->addCondition("id_affaire",$devis["id_affaire"]);
		$parcs=ATF::parc()->sa();

		ATF::comite()->q->reset()->addCondition("id_affaire",$devis["id_affaire"]);
		$comites=ATF::comite()->sa();

		ATF::demande_refi()->q->reset()
					   ->addCondition("id_affaire",$devis["id_affaire"]);
		$demande_refis=ATF::demande_refi()->sa();

		ATF::db($this->db)->begin_transaction();
//*****************************Transaction********************************

		ATF::affaire()->q->reset()->addCondition("id_fille",$affaire["id_affaire"]);
		$affaires=ATF::affaire()->sa();
		foreach($affaires as $item){
			$affaire_parent[]=$item;
		}

		//si des affaires AR une autre il faut supprimer le parc a broker de l'affaire parente
		foreach($affaire_parent as $item){
			ATF::parc()->q->reset()->addCondition("etat","broke","AND")
								   ->addCondition("id_affaire",$item["id_affaire"],"AND")
								   ->addCondition("existence","inactif","AND");
			if($parc=ATF::parc()->sa()){
				foreach($parc as $it){
					ATF::parc()->d($it["id_parc"]);
				}
			}
		}

		////////////////Parcs affaires parentes (éventuelle)
		if($parcs){
			if($affaire["id_parent"]){
				$affaire_parent[]=ATF::affaire()->select($affaire["id_parent"]);
			}
			//Supprimer les parcs de l'affaire parentes passées en inactif
			foreach($affaire_parent as $item){
				foreach($parcs as $i){
					ATF::parc()->q->reset()->addCondition("serial",$i["serial"],"AND")
										   ->addCondition("id_affaire",$item["id_affaire"],"AND")
										   ->addCondition("existence","inactif","AND");

					if($parc=ATF::parc()->sa()){
						foreach($parc as $it){
							ATF::parc()->d($it["id_parc"]);
						}
					}
				}
			}
			//Supprimer les parcs de l'affaire
			foreach($parcs as $i){
				ATF::parc()->d($i["id_parc"]);
			}
		}

		////////////////Opportunité
		if ($devis["id_opportunite"]) {
			ATF::opportunite()->u(array('id_opportunite'=>$devis['id_opportunite'],'etat'=>'en_cours','id_affaire'=>NULL));
		}

		$infos["devis"]["ref"]=ATF::affaire()->select($devis["id_affaire"],'ref');
		$affaire = array();

		$data_affaire = ATF::affaire()->select($devis["id_affaire"]);

		$affaire["date_installation_prevu"] = $data_affaire["date_installation_prevu"];
		$affaire["date_installation_reel"]  = $data_affaire["date_installation_reel" ];
		$affaire["date_livraison_prevu"] 	= $data_affaire["date_livraison_prevu"];
		$affaire["date_garantie"] 			= $data_affaire["date_garantie"];
		$affaire["date_ouverture"] 			= $data_affaire["date_ouverture"];
		$affaire["date_recettage_cablage"]  = $data_affaire["date_recettage_cablage"];

		$affaire["site_associe"]  = $data_affaire["site_associe"];
		$affaire["etat_comite"]  = $data_affaire["etat_comite"];
		$affaire["provenance"]  = $data_affaire["provenance"];
		$affaire["pieces"]  = $data_affaire["pieces"];
		$affaire["date_verification"]  = $data_affaire["date_verification"];
		$affaire["id_partenaire"]  = $data_affaire["id_partenaire"];
		//$affaire["commentaire_facture"]  = $data_affaire["commentaire_facture"];

		// Déplacer toutes les pièces jointes anciennes vers le nouveau
		$datapath = dirname(ATF::affaire()->filepath($devis["id_affaire"],"temp"));
		$id_temp = md5(mt_rand(0,time()));
		if ($handle = opendir($datapath)) {
		    while (false !== ($fileName = readdir($handle))) if (strpos($fileName,$devis["id_affaire"].".")===0) rename($datapath."/".$fileName, $datapath."/".str_replace($devis["id_affaire"].".",$id_temp.".",$fileName));
		    closedir($handle);
		}

		ATF::affaire()->d($devis["id_affaire"],$s,$files);

		$last_id = $this->insert($infos,$s,$files);

		$id_affaire = $this->select($last_id,"id_affaire");

		// Déplacer toutes les pièces jointes anciennes vers le nouveau
		if ($handle = opendir($datapath)) {
		    while (false !== ($fileName = readdir($handle))) if (strpos($fileName,$id_temp.".")===0) rename($datapath."/".$fileName, $datapath."/".str_replace($id_temp.".",$id_affaire.".",$fileName));
		    closedir($handle);
		}

		if($id_affaire){
			$affaire["id_affaire"] = $id_affaire;
			if($affaire["affaire"] != $this->select($last_id , "devis")) $affaire["affaire"] = $this->select($last_id , "devis");

			ATF::affaire()->u($affaire);
		}

		//On récupère les suivis de l'affaire modifier (supprimer en fait)
		foreach($suivis as $key=>$item){
			ATF::suivi()->u(array("id_suivi"=>$item["id_suivi"],"id_affaire"=>$id_affaire));
		}

		foreach($taches as $key=>$item){
			ATF::tache()->u(array("id_tache"=>$item["id_tache"],"id_affaire"=>$id_affaire));
		}

		foreach($comites as $key=>$item){
			$com = array("id_comite"=>$item["id_comite"],"id_affaire"=>$id_affaire);
			if(ATF::comite()->select($item["id_comite"] ,"etat") == "accepte") { $com["etat"] = "accord_non utilise"; }
			ATF::comite()->u($com);
		}

		foreach($demande_refis as $key=>$item){
			$item["id_affaire"] =$id_affaire;
			ATF::demande_refi()->i($item);
		}

		if($infos["preview"]){
			ATF::db($this->db)->rollback_transaction();
			return $this->cryptId($last_id);
		}else{

			ATF::db($this->db)->commit_transaction();
			if(is_array($cadre_refreshed)){
				ATF::affaire()->redirection("select",$id_affaire);
			}
			return $last_id;
		}
//*****************************************************************************

	}

	/**
	* Permet d'insérer les parcs en inactif et les lignes de devis lorsqu'on fait un avenant
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $infos_ligne_repris lignes concernées
	* @param array $affaire_avenant id_affaire parente
	* @param int $id_affaire id_affaire fille
	* @return boolean
	*/
	function avenant($infos_ligne_repris,$affaire_avenant,$id_affaire){
		$infos_ligne_repris=$this->extJSUnescapeDot($infos_ligne_repris,"devis_ligne");
		$affaire=ATF::affaire()->select($id_affaire);

		//On vérifie que les parcs sont de la même affaire
		foreach($infos_ligne_repris as $k=>$i){
			//Gestion du parc
			$parc=ATF::parc()->select($i["id_parc"]);
			//Si le parc fait bien partie de l'affaire sélectionnée
			if($i["id_affaire_provenance"]==$affaire_avenant){
				unset($parc["id_parc"]);
				$parc["provenance"]=$parc["id_affaire"];
				$parc["id_affaire"]=$id_affaire;
				$parc["etat"]="broke";
				$parc["existence"]="inactif";
				$parc["date_inactif"]=date("Y-m-d");
				$parc["id_societe"]=$affaire["id_societe"];
				unset($parc["date"]);
				ATF::parc()->i($parc);
			}else{
				throw new errorATF("parc_checked_sans_affaire",891);
			}
		}

		return true;
	}

	/**
	* Permet d'insérer les parcs en inactif et les lignes de devis lorsqu'on fait un AR et de mettre à jour l'id_fille
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $infos_ligne_repris lignes concernées
	* @param array $affaires_AR affaires à partir desquelles on fait l'AR
	* @param int $id_affaire id_affaire fille
	* @return boolean
	*/
	function AR($infos_ligne_repris,$affaires_AR,$id_affaire){
		$infos_ligne_repris=$this->extJSUnescapeDot($infos_ligne_repris,"devis_ligne");
		$affaire=ATF::affaire()->select($id_affaire);
		$affaires=array();
		foreach($infos_ligne_repris as $k_parc=>$i_parc){
			//Gestion du parc
			$parc_reloue=ATF::parc()->select($i_parc["id_parc"]);
			//Si le parc fait bien partie des affaires sélectionnées
			if(in_array($parc_reloue["id_affaire"],$affaires_AR)){
				unset($parc_reloue["id_parc"]);
				$parc_reloue["provenance"]=$parc_reloue["id_affaire"];
				$parc_reloue["id_affaire"]=$id_affaire;
				$parc_reloue["etat"]="reloue";
				$parc_reloue["existence"]="inactif";
				$parc_reloue["date_inactif"]=date("Y-m-d");
				$parc_reloue["id_societe"]=$affaire["id_societe"];
				unset($parc_reloue["date"]);
				ATF::parc()->i($parc_reloue);

			}else{
				throw new errorATF("parc_checked_sans_affaire",891);
			}
		}

		$affaires=array_unique($affaires);
		foreach($affaires_AR as $key=>$item){
			//Mise à jour de l'id_fille de l'affaire parente
			ATF::affaire()->u(array("id_affaire"=>$item,"id_fille"=>$id_affaire));
			ATF::parc()->q->reset()->addCondition("id_affaire",$item)
								   ->addCondition("existence","actif","AND");
			$parc_broke=ATF::parc()->sa();
			//Pour tous les parcs de l'ancienne affaire
			foreach($parc_broke as $k=>$i){
				ATF::parc()->q->reset()
							  ->addCondition("id_affaire",$id_affaire)
							  ->addCondition("serial",$i["serial"],"AND");
				//Sauf si ce park a été affecté à l'affaire fille
				if(!ATF::parc()->sa()){
					//Gestion du parc
					unset($i["id_parc"],$i["date"]);
					$i["etat"]="broke";
					$i["existence"]="inactif";
					$i["date_inactif"]=date("Y-m-d");
					ATF::parc()->i($i);
				}

			}
		}
		return true;
	}

	/**
	* Permet de faire une vente sur tou ou une partie des parcs d'une affaire
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $infos_ligne_repris lignes concernées
	* @param array $affaires_vente affaires à partir desquelles on fait la vente
	* @param int $id_affaire id_affaire fille
	* @return boolean
	*/
	function vente($infos_ligne_repris,$affaires_vente,$id_affaire){
		$infos_ligne_repris=$this->extJSUnescapeDot($infos_ligne_repris,"devis_ligne");
		$affaire=ATF::affaire()->select($id_affaire);
		foreach($infos_ligne_repris as $k_parc=>$i_parc){
			//Gestion du parc
			$parc_vendu=ATF::parc()->select($i_parc["id_parc"]);
			//Si le parc fait bien partie des affaires sélectionnées
			if(in_array($parc_vendu["id_affaire"],$affaires_vente)){
				unset($parc_vendu["id_parc"]);
				$parc_vendu["provenance"]=$parc_vendu["id_affaire"];
				$parc_vendu["id_affaire"]=$id_affaire;
				$parc_vendu["etat"]="vendu";
				$parc_vendu["existence"]="inactif";
				$parc_vendu["id_societe"]=$affaire["id_societe"];
				$parc_vendu["date_inactif"]=date("Y-m-d");
				unset($parc_vendu["date"]);
				ATF::parc()->i($parc_vendu);

				$affaire[]=$parc_vendu["provenance"];
			}else{
				throw new errorATF("parc_checked_sans_affaire",891);
			}
		}
		return true;
	}

	/**
	* Permet de créer un loyer unique dans le cas d'un avenant à loyer unique
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param int $id_affaire
	* @param int $id_parent
	* @param array $loyer montans du loyer pré-rempli dans l'insert
	* @return boolean
	*/
	function loyer_unique($id_affaire,$id_parent,$loyer){
		$loyer["duree"]=1;
		$loyer["id_affaire"]=$id_affaire;
		return ATF::loyer()->i($loyer);
	}


//	function valeur_residuelle($id_affaire,$taux=0) {
//		/* Finance */
//		require_once __ABSOLUTE_PATH__."libs/ATF/libs/php_finance/finance.class.php";
//
//		$f = new Financial;
//		ATF::loyer()->q->reset()->addCondition("id_affaire",$id_affaire);
//		ATF::loyer()->q->reset()->addOrder("id_affaire","DESC");
//		$loyer=ATF::loyer()->select_all();
//
//		foreach($loyer as $key=>$item) {
//			if (!$item["duree"] || !$affaire["loyer"]){
//				if($item["frequence_loyer"]=='mois'){
//					$frequence=12;
//				}elseif($devis["frequence_loyer"]=='trimestre'){
//					$frequence=4;
//				}else{
//					$frequence=1;
//				}
//			}
//			$affaire["pv_".$i] = -$f->PV($affaire["taux_refi".$taux]/($frequence)/100, $affaire["duree_".$i], $affaire["loyer_".$i], $affaire["pv_".($i+1)] ? $affaire["pv_".($i+1)] : $valeur_residuelle_demande_refi, 1);
//			$affaire["pv"] = $affaire["pv_".$i];
//
//
//			$affaire["pv_".$key] = -$f->PV($taux/($frequence)/100, $item["duree"], $affaire["loyer"], $affaire["pv_".($key+1)], 1);
//			$affaire["pv"] = $affaire["pv_".$key];
//		}
//	}

	/**
	* Retourne la durée totale en mois, méthode d'objet et non de singleton
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return int
	*/
	function getDuree(){
		$this->notSingleton();
		return ATF::loyer()->dureeTotal($this->get("id_affaire"));
	}

	/**
	* Retourne la date de fin prévue en utilisant la date de début passée en paramètre, méthode d'objet et non de singleton
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string date (Y-m-d)
	* @return string date (Y-m-d)
	*/
	function getDateFinPrevue($date_debut){
		$this->notSingleton();
		$nb_mois = $this->getDuree();
		if($nb_mois){
			return date("Y-m-d",strtotime($date_debut." + ".$nb_mois." month - 1 day"));
		}else{
			return $date_debut;
		}
	}

	/**
	* Impossible de modifier un devis qui n'est pas en attente
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param int $id
	* @return boolean
	*/
	public function can_update($id,$infos=false){
		if($this->select($id,"etat")=="attente"){
			return true;
		}else{
			throw new errorATF("Impossible de modifier/supprimer ce ".ATF::$usr->trans($this->table)." car il n'est plus en '".ATF::$usr->trans("attente")."'",892);
			return false;
		}
	}

	/**
	* Impossible de supprimer un devis
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param int $id
	* @return boolean
	*/
	public function can_delete($id,$infos=false){
		return false;
	}


	/**
    * Retourne la valeur par défaut spécifique aux données passées en paramètres
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param string $field
	* @param array &$s La session
	* @param array &$request Paramètres disponibles (clés étrangères)
	* @return string
    */
	public function default_value($field){
		if(ATF::_r('id_devis')){
			$devis=ATF::devis()->select(ATF::_r('id_devis'));
			$affaire = ATF::affaire()->select(ATF::devis()->select(ATF::_r('id_devis'), "id_affaire"));
		}

		if($devis){
			switch ($field) {
				case "email":
					return ATF::contact()->select($devis["id_contact"],"email");
				case "emailCopie":
					return ATF::$usr->get("email");
				case "prix":
					$prix=ATF::loyer()->prixTotal($devis["id_affaire"]);
					return $prix;
				case "prix_achat":
					$prix_achat=$devis["prix_achat"];
					return $prix_achat;
				case "marge":
					$prix=ATF::loyer()->prixTotal($devis["id_affaire"]);
					$marge=round((($devis["prix"]-$devis["prix_achat"])/$devis["prix"])*100,2)."%";
					return $marge;
				case "marge_absolue":
					$prix=ATF::loyer()->prixTotal($devis["id_affaire"]);
					$marge_absolue=($devis["prix"]-$devis["frais_de_port"])-$devis["prix_achat"];
					return $marge_absolue;
				case "emailTexte":
					return $this->majMail($devis["id_societe"]);
				case "langue":
					return ATF::affaire()->select($devis["id_affaire"], "langue");
				case "RIB":
				case "BIC":
				case "IBAN":
				case "nom_banque":
				case "ville_banque":
					$return=ATF::affaire()->select($devis["id_affaire"],$field);
					return $return;
				case "prix_vente":
					if($devis["type_contrat"]=='vente'){
						return $devis["prix"];
					}
					break;
				case "commentaire_facture":
					return $affaire["commentaire_facture"];
					break;
				case "commentaire_facture2":
					return $affaire["commentaire_facture2"];
					break;
				case "commentaire_facture3":
					return $affaire["commentaire_facture3"];
					break;
			}
		}else{
			switch ($field) {
				case "id_filiale":
					if(ATF::$codename == "cleodis")		return 246;
					if(ATF::$codename == "cleodisbe")	return 4225;
				case "emailCopie":
					return ATF::$usr->get("email");
				case "tva":
					$s = ATF::societe()->select(ATF::_r('id_societe'),"tva");
					return $s?$s:__TVA__;
				case "type_contrat":
					return "lld";
				case "validite" :
					return date("Y-m-d",strtotime("+15 day"));
				case "date":
					return date("Y-m-d");
				case "prix":
					$prix=0;
					return $prix;
				case "prix_achat":
					$prix_achat=0;
					return $prix_achat;
				case "marge":
					$marge=0;
					return $marge;
				case "marge_absolue":
					$marge_absolue=0;
					return $marge_absolue;
				case "langue":
					if(ATF::_r('id_societe')) return ATF::societe()->select(ATF::_r('id_societe'),"langue");
					return "FR";
				case "RIB":
				case "BIC":
				case "IBAN":
				case "nom_banque":
				case "ville_banque":
					if(ATF::_r('id_societe')){
						$return=ATF::societe()->select(ATF::_r('id_societe'),$field);
					}else{
						$return="";
					}
					return $return;
			}
		}

		return parent::default_value($field);
	}

	/**
    * Retourne la valeur du texte d'email, appelé en Ajax
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param int $id_societe
	* @return string texte du mail
    */
	public function majMail($id_societe){
		return nl2br("Bonjour,\n\nCi-joint le devis pour la société ".ATF::societe()->nom($id_societe).".\nDevis effectué le ".date("d/m/Y").".\n");
	}

	/**
	* Retourne les fournisseurs d'un devis.
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param id_devis ID d'un devis
	* @param string pour contrôler le retour, soit en string soit en array
	*/
	function getFournisseurs($id_devis,$string=false) {
		$f = ATF::devis_ligne()->getFournisseurs($id_devis);
		if ($string) {
			foreach ($f as $k=>$i) {
				$d = ATF::societe()->select($i['id_fournisseur']);
				$return .= $d['societe'].($i['siren']?" (".$i['siren'].")":"");
			}
			return $return;
		} else {
			return $f;
		}

	}

	/**
	* Méthode permettant de passer l'état d'un devis et d'une affaire à perdu
	* @author mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	*/
	public function perdu($infos,&$s,$files=NULL,&$cadre_refreshed){
		$devis=$this->select($infos["id_devis"]);

		if($devis["etat"]!="gagne"){
			ATF::db($this->db)->begin_transaction();
//***************************Transaction************************************************

			$this->u(array("id_devis"=>$devis["id_devis"],"etat"=>"perdu","raison_refus"=>$infos["raison_refus"]),$s);
			ATF::affaire()->u(array("id_affaire"=>$devis["id_affaire"],"etat"=>"perdue","forecast"=>"0"),$s);

			//Cette affaire ne doit plus être la fille de personne
			ATF::affaire()->q->reset()->addCondition("id_fille",$devis["id_affaire"]);
			if($affaires_parentes=ATF::affaire()->sa()){
				foreach($affaires_parentes as $item){
					ATF::affaire()->u(array("id_affaire"=>$item["id_affaire"],"id_fille"=>NULL),$s);
				}
			}

			ATF::comite()->q->reset()->addCondition("id_affaire",$devis["id_affaire"]);
			if($comites=ATF::comite()->sa()){
				foreach($comites as $item){
					ATF::comite()->u(array("id_comite"=>$item["id_comite"],"etat"=>"accord_non utilise"),$s);
				}
			}

			ATF::db($this->db)->commit_transaction();
////*****************************************************************************

			ATF::$msg->addNotice(
				loc::mt(ATF::$usr->trans("notice_devis_perdu"),array("record"=>$this->nom($infos["id_devis"])))
				,ATF::$usr->trans("notice_success_title")
			);


			$suivi_societe = array(0=>ATF::$usr->getID());
			$suivi = array(
				 "id_user"=>ATF::$usr->get('id_user')
				,"id_societe"=>$devis['id_societe']
				,"id_affaire"=>$devis['id_affaire']
				,"type_suivi"=>'Devis'
				,"texte"=>"Le devis vient de passer en perdu\n par ".ATF::$usr->getNom()."\nRaison : ".$infos["raison_refus"]
				,'public'=>'oui'
				,'id_contact'=>NULL
				,'suivi_societe'=>$suivi_societe
				,'suivi_notifie'=>$suivi_societe
				,'no_redirect'=>true
			);
			$id_suivi = ATF::suivi()->insert($suivi);

			if(ATF::societe()->select($devis['id_societe'] , "relation") == "prospect")	ATF::societe()->u(array("id_societe"=> $devis['id_societe'] , "relation"=>"prospect"));



			$this->redirection("select_all",NULL,"devis.html");
			return true;
		}else{
			throw new errorATF("Impossible de passer une affaire gagnée en 'perdu'",899);
		}
	}

	/**
	* Méthode retournant le contact d'un devis
	* @author mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param int $id_affaire
	* @return int contact du devis
	*/
	public function contactByDevis($id_affaire){
		if ($id_affaire) {
			$this->q->reset()->addCondition("id_affaire",$id_affaire)
						 ->setDimension("row");
			$devis=$this->sa();
			return $devis["id_contact"];
		}else{
			return false;
		}
	}

	/**
    * Retourne les infos de societe
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    */
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false) {
		$this->q->addJointure("devis","id_societe","societe","id_societe");
		return parent::select_all($order_by,$asc,$page,$count);
	}

	/**
	* Retourne les lignes d'un type, méthode d'objet et non de singleton
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $type visible|invisible|reprise
	* @return array
	*/
	function getLignes($type){
		$this->notSingleton();
		ATF::devis_ligne()->q->reset()
			->addField("devis_ligne.ref")->addField("devis_ligne.id_fournisseur")->addField("devis_ligne.quantite")->addField("devis_ligne.prix_achat")
			->where("id_devis",$this->get("id_devis"));
		switch ($type) {
			case "visible":
				ATF::devis_ligne()->q->where("visible","oui")->whereIsNull("id_affaire_provenance");
			break;
			case "invisible":
				ATF::devis_ligne()->q->where("visible","non")->whereIsNull("id_affaire_provenance");
			break;
			case "reprise":
				ATF::devis_ligne()->q->whereIsNotNull("id_affaire_provenance");
			break;
		}
		ATF::devis_ligne()->q->addOrder("id_devis_ligne",'asc');
		return util::removeTableInKeys(ATF::devis_ligne()->select_all()); // On préfixe pour avoir la jointure auto des clés étrangères, mais les clés font chier ExtJS en retour
	}





	public function initStyle(){

		$style_titre1 = new excel_style();
		$style_titre1->setWrap()->alignement('center')->setSize(13)->setBorder("thin")->setBold();
		$this->setStyle("titre1",$style_titre1->getStyle());
		/*-------------------------------------------*/
		$style_titre1_right = new excel_style();
		$style_titre1_right->setWrap()->alignement("center","right")->setSize(13)->setBorder("thin")->setBold();
		$this->setStyle("titre1_right",$style_titre1_right->getStyle());
		/*-------------------------------------------*/
		$style_titre1_left = new excel_style();
		$style_titre1_left->setWrap()->alignement("center", "left")->setSize(13)->setBorder("thin")->setBold();
		$this->setStyle("titre1_left",$style_titre1_left->getStyle());
		/*-------------------------------------------*/
		$style_titre2 = new excel_style();
		$style_titre2->setWrap()->alignement('center')->setSize(11)->setBorder("thin");
		$this->setStyle("titre2",$style_titre2->getStyle());
		/*-------------------------------------------*/
		$style_titre2_right = new excel_style();
		$style_titre2_right->setWrap()->alignement("center","right")->setSize(11)->setBorder("thin");
		$this->setStyle("titre2_right",$style_titre2_right->getStyle());
		/*-------------------------------------------*/
		$style_centre = new excel_style();
		$style_centre->alignement();
		$this->setStyle("centre",$style_centre->getStyle());
		/*-------------------------------------------*/
		$style_cel_c = new excel_style();
		$style_cel_c->setWrap()->alignement('center')->setSize(11)->setBorder("thin");
		$this->setStyle("border_cel",$style_cel_c->getStyle());
		/*-------------------------------------------*/
		$style_border_cel_right = new excel_style();
		$style_border_cel_right->setWrap()->alignement("center","right")->setSize(11)->setBorder("thin");
		$this->setStyle("border_cel_right",$style_border_cel_right->getStyle());
		/*-------------------------------------------*/
		$style_border_cel_left = new excel_style();
		$style_border_cel_left->setWrap()->alignement("center","left")->setSize(11)->setBorder("thin");
		$this->setStyle("border_cel_left",$style_border_cel_left->getStyle());
		/*-------------------------------------------*/
		$style_cel_right = new excel_style();
		$style_cel_right->setWrap()->alignement("center","right")->setSize(11);
		$this->setStyle("cel_right",$style_cel_right->getStyle());
	}

	public function setStyle($nom,$objet){
		$this->style[$nom]=$objet;
	}

	public function getStyle($nom){
		return $this->style[$nom];
	}


	/** Surcharge de l'export filtrÃ© pour avoir tous les champs nÃ©cessaire Ã  l'export spÃ©cifique
     * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
     * @param array $infos : contient le nom de l'onglet
     */
	 public function export_devis_loyer($infos,$testUnitaire="false", $reset="true"){

	 	if($testUnitaire == "true"){
	 		$donnees = $infos;
		}else{
			if($reset == "true") $this->q->reset();
			$this->setQuerier(ATF::_s("pager")->create($infos['onglet']));
			$this->q->addAllFields($this->table)->setLimit(-1)->unsetCount();
			$donnees = $this->select_all();
		}
        require_once __ABSOLUTE_PATH__."libs/ATF/libs/PHPExcel/Classes/PHPExcel.php";
		require_once __ABSOLUTE_PATH__."libs/ATF/libs/PHPExcel/Classes/PHPExcel/Writer/Excel5.php";
		$fname = tempnam(__TEMPORARY_PATH__, __TEMPLATE__.ATF::$usr->getID());
		$workbook = new PHPExcel;

		//premier onglet
		$worksheet_auto = new PHPEXCEL_ATF($workbook,0);
		$worksheet_auto->sheet->setTitle('Devis loyer');
		$sheets=array("auto"=>$worksheet_auto);
		$this->initStyle();
		//mise en place des titres
		$this->ajoutTitreExport($sheets);
		//ajout des donnÃ©es
		if($donnees){
			$this->ajoutDonneesExport($sheets,$donnees);
		}

		$writer = new PHPExcel_Writer_Excel5($workbook);

		$writer->save($fname);
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition:inline;filename=export_devis_loyer.xls');
		header("Cache-Control: private");
		$fh=fopen($fname, "rb");
		fpassthru($fh);
		unlink($fname);
		PHPExcel_Calculation::getInstance()->__destruct();
    }


    /** Mise en place des titres
     * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
     */
    public function ajoutTitreExport(&$sheets){
        $row_data = array(
        	 "A"=>array('ENTITE',10)
        	,"B"=>array("RESPONSABLE", 10)
			,"C"=>array("REDACTEUR",10)
			,"D"=>array('DEVIS',10)
			,"E"=>array('DATE CREATION DU DEVIS',10)
			,"F"=>array('CODE CLIENT',10)
			,"G"=>array('ETAT',10)
			,"H"=>array('PREMIERE DATE D\'ACCORD',10)
			,"I"=>array('DERNIERE DATE D\'ACCORD',10)
			,"J"=>array('TYPE DE CONTRAT',10)
			,"K"=>array('DATE INSTALLATION PREVUE',10)
			,"L"=>array('LOYER 1',10)
			,"M"=>array('DUREE 1',10)
			,"N"=>array('FREQUENCE 1',10)
			,"O"=>array('LOYER 2',10)
			,"P"=>array('DUREE 2',10)
			,"Q"=>array('FREQUENCE 2',10)
			,"R"=>array('LOYER 3',10)
			,"S"=>array('DUREE 3',10)
			,"T"=>array('FREQUENCE 3',10)
			,"U"=>array('LOYER 4',10)
			,"V"=>array('DUREE 4',10)
			,"W"=>array('FREQUENCE 4',10)
			,"X"=>array('LOYER + DUREE',10)
			,"Y"=>array('RAISON REFUS',30)
			,"Z"=>array('COMITE',30)
			,"AA"=>array('OBSERVATIONS',30)
			,"AB"=>array('VALIDITE ACCORD',30)
			,"AC"=>array('DECISION COMITE',30)
			,"AD"=>array('COMMENTAIRE',90)

		);

        foreach($sheets as $nom=>$onglet){
             foreach($row_data as $col=>$titre){
				  $sheets[$nom]->write($col.'1',$titre[0],$this->getStyle("titre1"));
				  $sheets[$nom]->sheet->getColumnDimension($col)->setWidth($titre[1]);
            }
        }
    }



	/** Mise en place du contenu
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    * @param array $sheets : contient les 30 onglets
    * @param array $infos : contient tous les enregistrements
    */
    public function ajoutDonneesExport(&$sheets,$infos){
        $row_auto=1;
		foreach ($infos as $key => $value) {
			if(!$value["devis.id_affaire_fk"]) $value["devis.id_affaire_fk"] = $value["devis.id_affaire"];

			$row_data = array();
			$row_data["A"]=array($value["devis.id_societe"],"border_cel_left");
			$row_data["B"]=array(ATF::user()->nom( ATF::societe()->select($value["devis.id_societe_fk"], "id_owner")),"border_cel_left");
			$row_data["C"]=array($value["devis.id_user"],"border_cel_left");
			$row_data["D"]=array($value["devis.id_devis"],"border_cel_left");
			$row_data["E"]=array($value["devis.date"],"border_cel_left");
			$row_data["F"]=array(ATF::societe()->select($value["devis.id_societe_fk"] ,"code_client"),"border_cel_left");
			$row_data["G"]=array($value["devis.etat"],"border_cel_left");
			$row_data["H"]=array($value["devis.first_date_accord"],"border_cel_left");
			$row_data["I"]=array($value["devis.date_accord"],"border_cel_left");
			$row_data["J"]=array($value["devis.type_contrat"],"border_cel_left");
			$row_data["K"]=array(ATF::affaire()->select($value["devis.id_affaire_fk"] , "date_installation_prevu"),"border_cel_left");
			$row_data["Y"]=array($value["devis.raison_refus"],"border_cel_left");

			ATF::comite()->q->reset()->where("id_affaire", $value["devis.id_affaire_fk"]);
			$comites = ATF::comite()->select_all();

			if($comites){
				$decisiondate = $commentaire = $decision = $observation = "";
				foreach ($comites as $k => $v) {
					if($k != 0){
						$decisiondate = $decisiondate."\n".$v["date"];
						$commentaire  = $commentaire."\n". $v["commentaire"];
						$decision	  = $decision."\n".$v["decisionComite"];
						$date_accord  = $date_accord."\n".$v["validite_accord"];
						$observation  = $observation."\n".$v["observations"]; ;
					}else{
						$decisiondate =  $v["date"];
						$commentaire  =  $v["commentaire"];
						$decision 	  =  $v["decisionComite"];
						$date_accord  =  $v["validite_accord"];
						$observation  =  $v["observations"];
					}

				}

				$row_data["Z"]=array($decisiondate,"border_cel_left");
				$row_data["AA"]=array($observation,"border_cel_left");
				$row_data["AB"]=array($date_accord,"border_cel_left");
				$row_data["AC"]=array($decision,"border_cel_left");
				$row_data["AD"]=array($commentaire,"border_cel_left");
			}

			$loyers = NULL;
			ATF::loyer()->q->reset()->where('loyer.id_affaire',$value["devis.id_affaire_fk"]);
			$loyers = ATF::loyer()->select_all();

			$total = 0;

			//A =65 Z=90
		 	$col = 76;
			foreach ($loyers as $k => $v) {
				$row_data[chr($col)]=array($v["loyer"],"border_cel_left");
				$col++;
				$row_data[chr($col)]=array($v["duree"],"border_cel_left");
				$col++;
				$row_data[chr($col)]=array($v["frequence_loyer"],"border_cel_left");
				$col++;
				$total = $total + (($v["loyer"]+$v["assurance"]+$v["frais_de_gestion"])*$v["duree"]);
			}
			$row_data["X"]=array($total,"border_cel_left");

			if($row_data){
				$row_auto++;
				foreach($row_data as $col=>$valeur){
					$sheets['auto']->write($col.$row_auto, $valeur[0], $this->getStyle($valeur[1]));
				}
			}
		}
	}

	/**
	 * méthode permettant de faire les graphes des différents modules, dans statistique
	 * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	 */
	public function devis_gagne_stats($stats=false,$type=false,$widget=false,$date=false,$id_agence) {

		//on récupère la liste des années que l'on ne souhaite pas voir afficher sur les graphes
		//on les incorpore ensuite sur les requêtes adéquates
		$this->q->reset();
		foreach(ATF::stats()->liste_annees[$this->table] as $key_list=>$item_list){
			if($item_list)$this->q->addCondition("YEAR(`date`)",$key_list);
		}
		ATF::stats()->conditionYear(ATF::stats()->liste_annees[$this->table],$this->q,"date");

		$graph = array();

		switch ($type) {
			case "o2m":
			case "autre":
			case "les_S":
			case 'reseau' :

				if($widget){
					$this->q->reset()
							->addField("COUNT(*)","nb")
							->setStrict()
							->addJointure("devis","id_societe","societe","id_societe")
							->addJointure("devis","id_affaire","affaire","id_affaire")
							->addJointure("societe","id_owner","user","id_user")
							->where("user.id_agence",$id_agence);


					if($type == "reseau"){
						$this->q->addCondition("societe.code_client",'%S%',"AND","nonFinie","NOT LIKE")
								->addCondition("societe.code_client",NULL,"AND","nonFinie","IS NOT NULL");
					}else{
						$this->q->addCondition("societe.code_client",'%S%',"AND","nonFinie","LIKE")
								->addCondition("societe.code_client",NULL,"OR","nonFinie","IS NULL");
					}

					$this->q->addCondition("devis.devis","%lcd%" ,"AND", "conditiondevis", "NOT LIKE")
							->addCondition("devis.devis","%avenant%","AND", "conditiondevis", "NOT LIKE")
							->addCondition("devis.devis","%vente%","AND", "conditiondevis", "NOT LIKE")
							->addCondition("devis.devis","%AVT%","AND", "conditiondevis", "NOT LIKE")
							->addCondition("devis.devis","%MIPOS%","AND", "conditiondevis", "NOT LIKE")

							->addCondition("devis.type_contrat","vente","AND", "conditiondevis", "!=")
							->addCondition("devis.ref","%avt%","AND", "conditiondevis", "NOT LIKE")

							->addCondition("devis.etat",'gagne',"AND","conditiondevis","=")
							->addCondition("affaire.etat","terminee","AND","conditiondevis","!=")
							->addCondition("affaire.etat","perdue","AND","conditiondevis","!=")

							->addField("DATE_FORMAT(`".$this->table."`.`first_date_accord`,'%Y')","year")
							->addField("DATE_FORMAT(`".$this->table."`.`first_date_accord`,'%m')","month")

							->addGroup("year")->addGroup("month")
							->addOrder("year")->addOrder("month");

							$this->q->addCondition("`".$this->table."`.`first_date_accord`",$date."-01-01","AND",false,">=");

					$result= parent::select_all();


					$annee = $date-3;
					ATF::stat_snap()->q->reset()->addField("stat_snap.nb","nb")
												->addField("DATE_FORMAT(`stat_snap`.`date`,'%Y')","year")
												->addField("DATE_FORMAT(`stat_snap`.`date`,'%m')","month")
												->addCondition("`stat_snap`.`date`",$annee."-01-01","AND",false,">=")
												->addCondition("`stat_snap`.`date`",$date."-01-01","AND",false,"<")
												->addCondition("`stat_snap`.`id_agence`",$id_agence)
												->addGroup("year")->addGroup("month")
												->addOrder("year")->addOrder("month")
												->where("stat_concerne", "devis-".$type);
					$res = ATF::stat_snap()->select_all();

					/*for($a=$annee; $a<$date;$a++){
						for($m=1;$m<=12;$m++){
							//Resultat precedent
							ATF::devis()->q->reset()
								->addField("COUNT(*)","nb")
								->setStrict()
								->addJointure("devis","id_societe","societe","id_societe")
								->addJointure("devis","id_affaire","affaire","id_affaire")
								->addJointure("societe","id_owner","user","id_user")
								->where("user.id_agence",$id_agence)
								->addCondition("devis.devis","%lcd%" ,"AND", "conditiondevis", "NOT LIKE")
								->addCondition("devis.devis","%avenant%","AND", "conditiondevis", "NOT LIKE")
								->addCondition("devis.devis","%vente%","AND", "conditiondevis", "NOT LIKE")
								->addCondition("devis.devis","%AVT%","AND", "conditiondevis", "NOT LIKE")
								->addCondition("devis.devis","%MIPOS%","AND", "conditiondevis", "NOT LIKE")

								->addCondition("devis.type_contrat","vente","AND", "conditiondevis", "!=")
								->addCondition("devis.ref","%avt%","AND", "conditiondevis", "NOT LIKE")

								->addCondition("devis.etat",'gagne',"AND","conditiondevis","=")
								->addCondition("affaire.etat","terminee","AND","conditiondevis","!=")
								->addCondition("affaire.etat","perdue","AND","conditiondevis","!=")

								->addField("DATE_FORMAT(`devis`.`first_date_accord`,'%Y')","year")
								->addField("DATE_FORMAT(`devis`.`first_date_accord`,'%m')","month")

								->addGroup("year")->addGroup("month")
								->addOrder("year")->addOrder("month")

								->addCondition("`devis`.`first_date_accord`",$a."-".$m."-01","AND",false,">=")
								->addCondition("`devis`.`first_date_accord`",$a."-".$m."-31","AND",false,"<");

							if($type == "reseau"){
								ATF::devis()->q->addCondition("societe.code_client",'%S%',"AND","nonFinie","NOT LIKE")
										    ->addCondition("societe.code_client",NULL,"AND","nonFinie","IS NOT NULL");
							}else{
								ATF::devis()->q->addCondition("societe.code_client",'%S%',"AND","nonFinie","LIKE")
										    ->addCondition("societe.code_client",NULL,"OR","nonFinie","IS NULL");
							}
							$r[$a][$m] = ATF::devis()->select_row();
						}
					}

					foreach ($r as $ky => $value) {
						foreach ($value as $k => $v) {
							$res[] = array(
										"nb"=> $v["nb"],
										"year"=> $v["year"],
										"month"=> $v["month"]
									);
						}
					}*/
				}




				if($widget){

					$agence = ATF::agence()->select($id_agence);

					foreach (ATF::stats()->recupMois($type) as $i) {
						$graph['categories']["category"][] = array("label"=>substr($i,0,1),"hoverText"=>$i);
					}

					$avg = $reel = $obj =array();

					if(count($result) < 12){
						for ($i=0; $i < 12; $i++) {
							if($i <9) $month = "0".$i+1;
							else $month = $i+1;
							$temp[$i] = array("nb"=>0, "year"=>date("Y"), "month"=>$month);
							foreach ($result as $key => $value) {
								if($month == $value["month"]){
									$temp[$i]["nb"] = $value["nb"];
									$temp[$i]["year"] = $value["year"];
									$temp[$i]["month"] = $value["month"];
								}
							}

						}
						$result = $temp;
					}



					foreach ($res as $i) {
						if($avg[$i["month"]]["value"]){		$avg[$i["month"]]["value"] = $avg[$i["month"]]["value"]+$i["nb"];
						}else{	$avg[$i["month"]]["value"] = $i["nb"];	}
						$avg[$i["month"]]["titre"] = "Objectif MENSUEL : ".$avg[$i["month"]]["value"];

						if($graph['year'][$i['year']]["count"]){ $graph['year'][$i['year']]["count"] = $graph['year'][$i['year']]["count"] + $i["nb"]; }
						else{ $graph['year'][$i['year']]["count"] = $i["nb"]; }
						$graph['year'][$i['year']]["annee"] = $i["year"];

					}

					foreach ($result as $i) {
						$reel[$i["month"]]["value"]= $i["nb"];
						$reel[$i["month"]]["titre"] = "Objectif MENSUEL : ".$reel[$i["month"]]["value"];

						if($graph['year'][$i['year']]["count"]){ $graph['year'][$i['year']]["count"] = $graph['year'][$i['year']]["count"] + $i["nb"]; }
						else{ $graph['year'][$i['year']]["count"] = $i["nb"]; }
						$graph['year'][$i['year']]["annee"] = $i["year"];
					}

					$totalPrec = 0;
					if($type == "o2m" ||$type == 'reseau'){	$objectif = $agence["objectif_devis_reseaux"]; }
					else{ 	$objectif = $agence["objectif_devis_autre"]; }

					foreach ($avg as $key => $value) {
						$avg[$key]["value"] = round($value["value"]/3);
						$totalPrec += $avg[$key]["value"];
					}

					foreach ($avg as $key => $value) {
						$pourcentage = ($avg[$key]["value"]/$totalPrec)*100;
						$obj[$key]["value"] = round(($objectif/100)*$pourcentage);
					}

					$graph['dataset']["objectif"] = $obj;
					$graph['dataset']["moyenne"] = $avg;
					$graph['dataset']["reel"] = $reel;


				} else {
					foreach (ATF::stats()->recupMois($type) as $k=>$i) {
						$graph['categories']["category"][] = array("label"=>substr($i,0,4),"hoverText"=>$i);
					}
				}


		}
		return $graph;
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
};

class devis_midas extends devis_cleodis {
	function __construct($table_or_id=NULL) {
		$this->table = "devis";
		parent::__construct($table_or_id);

		$this->colonnes['fields_column'] = array(
			 'devis.ref'
			,'devis.id_societe'
			,'devis.id_affaire'
			,'devis.devis'
			,'devis.etat'=>array("renderer"=>"etat","width"=>30)
			,'devis.date'
		);

		$this->fieldstructure();
	}

	public function select_all($order_by=false,$asc='desc',$page=false,$count=false) {
		$this->q->addJointure("devis","id_societe","societe","id_societe")
				->addCondition("societe.code_client","M%","OR",false,"LIKE")
				->addCondition("societe.divers_3","Midas");
		return parent::select_all($order_by,$asc,$page,$count);
	}
};

class devis_cleodisbe extends devis_cleodis { };


?>
