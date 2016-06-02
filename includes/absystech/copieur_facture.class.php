<? 
/**
* Classe Copieur facture
* Cet objet permet de gérer les factures de copieur au sein de la gestion commerciale
* @package Optima
*/
class copieur_facture extends classes_optima {

	/**	* Constructeur par défaut
	*/ 
	public function __construct() { 
		parent::__construct();
		$this->table = "copieur_facture";
		$this->colonnes['fields_column'] = array(	
			'copieur_facture.ref'=>array("width"=>100,"align"=>"center")
			,'copieur_facture.id_societe'
			,'copieur_facture.releve_compteurNB'
			,'copieur_facture.releve_compteurC'
			,'copieur_facture.date'=>array("width"=>100,"align"=>"center")
			,'copieur_facture.date_previsionnelle'=>array("width"=>160,"align"=>"center","renderer"=>"updateDate")
			,'copieur_facture.etat'=>array("width"=>30,"renderer"=>"etat")
			,'copieur_facture.date_effective'=>array("width"=>160,"align"=>"center","renderer"=>"updateDate")
			,'copieur_facture.prix'=>array("aggregate"=>array("min","avg","max","sum"),"align"=>"right","renderer"=>"money","width"=>80)
			,'copieur_facture.prix_ttc'=>array("aggregate"=>array("min","avg","max","sum"),"align"=>"right","renderer"=>"money","width"=>80)
			,'retard'=>array("custom"=>true,"aggregate"=>array("min","avg","max","sum"),"align"=>"right","renderer"=>"duree","width"=>80)
			,'fichier_joint'=>array("custom"=>true,"nosort"=>true,"type"=>"file","align"=>"center","width"=>50)
		);	

		$this->colonnes['primary'] = array(	 
			"id_societe"=>array("autocomplete"=>array(
				"function"=>"autocompleteAvecTVA"
				,"mapping"=>array(
					array('name'=> 'tva', 'mapping'=> 'raw_0')
					,array('name'=>'id', 'mapping'=> 1)
					,array('name'=> 'nom', 'mapping'=> 2)
					,array('name'=> 'detail', 'mapping'=> 3, 'type'=>'string' )
					,array('name'=> 'nomBrut', 'mapping'=> 'raw_2')
				)
			))
			,"date"			
			,'date_previsionnelle'
			,'id_affaire_cout_page'			
			,'id_termes'=>array("updateOnSelect"=>true,"custom"=>true)
		);		

		$this->colonnes['panel']['mode_facturation'] = array(
			"date_debut_periode"=>array("null"=>false)				
			,"date_fin_periode"=>array("null"=>false)
			,"tva"=>array("formatNumeric"=>true,"xtype"=>"textfield","num_decimal_places"=>3)
			,"releve_compteurNB"
			,"releve_compteurC"
			,"finale"=>array("custom"=>true,"xtype"=>"checkbox")		
		);

		$this->colonnes['panel']['lignes'] = array(
			"produits"=>array("custom"=>true)
		);

		$this->colonnes['panel']['courriel'] = array(
			"email"=>array("custom"=>true,'null'=>true)
			,"emailCopie"=>array("custom"=>true,'null'=>true)
			,"emailTexte"=>array("custom"=>true,'null'=>true,"xtype"=>"htmleditor")
		);
		
		// Propriété des panels
		$this->panels['mode_facturation'] = array("visible"=>true,'nbCols'=>3,"collapsible"=>false);
		$this->panels['primary'] = array("visible"=>true,'nbCols'=>3);
		$this->panels['lignes'] = array("visible"=>true, 'nbCols'=>1);
		$this->panels['courriel'] = array('nbCols'=>2);

		// Champs masqués
		$this->colonnes['bloquees']['insert'] =  
		$this->colonnes['bloquees']['cloner'] =  
		$this->colonnes['bloquees']['update'] =  array('ref','id_user','etat','date_effective','prix');	
		$this->colonnes['bloquees']['select'] =  array('finale','email','emailCopie','emailTexte','produits');
		

		//IMPORTANT, complte le tableau de colonnes avec les infos MYSQL des colonnes
		$this->fieldstructure();	

		$this->onglets = array('copieur_facture_ligne');
		$this->files["fichier_joint"] = array("type"=>"pdf","preview"=>true,"quickMail"=>true);
		$this->field_nom = "ref";		
		
		$this->autocomplete = array(
			"field"=>array("copieur_facture.ref","copieur_facture.prix")
			,"show"=>array("copieur_facture.ref","copieur_facture.prix")
			,"popup"=>array("copieur_facture.ref","copieur_facture.prix")
			,"view"=>array("copieur_facture.ref","copieur_facture.prix")
		);

		$this->no_update = $this->no_insert = true;
	}

	/**
    * Retourne la valeur par défaut spécifique aux données passées en paramètres
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $field
	* @return string
    */   	
	public function default_value($field){
		if(ATF::_r('id_copieur_facture')){
			$el=ATF::copieur_facture()->select(ATF::_r('id_copieur_facture'));
		}elseif(ATF::_r('id_copieur_contrat')){
			$el=ATF::copieur_contrat()->select(ATF::_r('id_copieur_contrat'));
		}elseif(ATF::_r('id_affaire_cout_page')){
			$el=ATF::affaire_cout_page()->select(ATF::_r('id_affaire_cout_page'));
		}


		switch ($field) {
			case "date":
				return date("Y-m-d");
			case "emailCopie":
				return ATF::$usr->get("email");
			case "email":
				if($id_contact_facturation=ATF::societe()->select($el['id_societe'],"id_contact_facturation")){
					return ATF::contact()->select($id_contact_facturation,"email");
				}else{
					return false;
				}

			case "id_termes":
				$affaire=ATF::affaire_cout_page()->select($el['id_affaire_cout_page']);
				$id_termes=$affaire["id_termes"];					
				return $id_termes;
			case "date_previsionnelle":
				return date('Y-m-d',strtotime(date("Y-m-d")." + 30 day"));

			case "tva":
				if ($el[$field]) {
					return $el[$field];
				} else {
					return "1.200";
				}
			default:
				return $el[$field];
			break;
				
		}
	}

	/** 
	* Surcharge de l'insert afin d'insérer les lignes de factures et modifier l'état de l'affaire sur l'insert d'une facture
	* @author mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	*/
	public function insert($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		$lignes = json_decode($infos["values_".$this->table]["produits"],true);

 		$preview=$infos["preview"];
		$finale=$infos[$this->table]["finale"];
		$id_copieur_contrat=$infos[$this->table]["id_copieur_contrat"];

		$this->infoCollapse($infos);
		
		if(!$infos["id_societe"]){
			throw new error("Vous devez spécifier la société (Entité)",167);
		}else{
			if(ATF::societe()->estFermee($infos["id_societe"])){
				throw new error("Impossible d'ajouter une facture sur une entité fermée");
			}
		}
		
		if(!isset($infos["releve_compteurC"]) || !isset($infos["releve_compteurNB"])){
			throw new error("Vous devez saisir les relevés de compteur couleur ET noir & blanc.",167);
		}
		if(!$infos["id_affaire_cout_page"]){
			$infos["id_affaire_cout_page"]=ATF::copieur_contrat()->select($id_copieur_contrat,"id_affaire_cout_page");
		}
		if(!$infos["id_affaire_cout_page"]){
			throw new error("Vous devez spécifier une affaire coût/pages",167);
		}
		
		/*Formatage des numériques*/
		$infos["prix"]=util::stringToNumber($infos["prix"]);

		if($infos["emailTexte"]){
			$email["email"]=$infos["email"];
			$email["emailCopie"]=$infos["emailCopie"];
			$email["texte"]=$infos["emailTexte"];
		}else{
			$email=false;
		}
		unset($infos["email"],$infos["emailCopie"],$infos["emailTexte"],$infos["id_copieur_contrat"],$infos["finale"],$infos["preview"]);

		$infos["id_user"] = ATF::$usr->getID();
		$infos["ref"] = ATF::affaire()->getRef($infos["date"],"facture");
		
		$societe=ATF::societe()->select($infos["id_societe"]);
		
		//Seuls les associés peuvent modifier la tva
		$tva=ATF::facture()->getTVA($societe["id_societe"]);
		if($tva!=$infos["tva"] && ATF::$usr->get("id_profil")!=1){
			$profil=ATF::profil()->select(1);
			ATF::$msg->addNotice(
				loc::mt(ATF::$usr->trans("error_403_facture_tva"),array("profil"=>$profil["profil"], "tva"=>$tva))
				,ATF::$usr->trans("Droits_d_acces_requis_pour_cette_operation")
			);
			$infos["tva"] = $tva;
		}

		$prix = 0;		
		ATF::db($this->db)->begin_transaction();
			
		if($infos["id_termes"] === NULL){
			ATF::db($this->db)->rollback_transaction();
			throw new error("Vous devez spécifier les termes",167);
		}
		
		
		if ($precedentFacture = $this->getLastFacture($infos['id_affaire_cout_page'])) {
			if ($precedentFacture['releve_compteurNB']>$infos['releve_compteurNB']) {
				ATF::db($this->db)->rollback_transaction();
				throw new error("Le relevé de compteur N&B de cette facture ne peut être inférieur au précedent (qui est ".$precedentFacture['releve_compteurNB']." - Facture ".$precedentFacture['ref'].")");
			}
			if ($precedentFacture['releve_compteurC']>$infos['releve_compteurC']) {
				ATF::db($this->db)->rollback_transaction();
				throw new error("Le relevé de compteur couleur de cette facture ne peut être inférieur au précedent (qui est ".$precedentFacture['releve_compteurC']." - Facture ".$precedentFacture['ref'].")");
			}


			$real_releve_compteurNB = $infos['releve_compteurNB']-$precedentFacture['releve_compteurNB'];
			$real_releve_compteurC = $infos['releve_compteurC']-$precedentFacture['releve_compteurC'];
		} else {
			$real_releve_compteurNB = $infos['releve_compteurNB'];
			$real_releve_compteurC = $infos['releve_compteurC'];

			// si valeur initial dans l'affaire il faut les décrementer.		
			if ($riC = ATF::affaire_cout_page()->select($infos['id_affaire_cout_page'],"releve_initial_C")) {
				if ($riC>$real_releve_compteurC) {
					ATF::db($this->db)->rollback_transaction();
					throw new error("Le relevé de compteur N&B de cette facture ne peut être inférieur au relevé initial de l'affaire (qui est ".$riC.")");
				}

				$real_releve_compteurC -= $riC;

			}
			if ($riNB = ATF::affaire_cout_page()->select($infos['id_affaire_cout_page'],"releve_initial_NB")) {
				if ($riNB>$real_releve_compteurNB) {
					ATF::db($this->db)->rollback_transaction();
					throw new error("Le relevé de compteur N&B de cette facture ne peut être inférieur au relevé initial de l'affaire (qui est ".$riNB.")");
				}
				$real_releve_compteurNB -= $riNB;
			}
		}
		//Facture
		$last_id=parent::insert($infos,$s,NULL,$var=NULL,NULL,true);
			

		//Facture Ligne
		foreach($lignes as $key=>$item){

			foreach($item as $k=>$i){
				$k_unescape=util::extJSUnescapeDot($k);
				$item[str_replace("copieur_facture_ligne.","",$k_unescape)]=$i;
				unset($item[$k]);
			}

			$item["id_copieur_facture"]=$last_id;
			if(!$item["quantite"]){
				$item["quantite"]=0;
			}

			$prix += $real_releve_compteurNB*$item['prixNB'];
			$prix += $real_releve_compteurC*$item['prixC'];

			ATF::copieur_facture_ligne()->i($item,$s);
		}
		
		// MAJ DU PRIX
		$this->u(array('id_copieur_facture'=>$last_id,"prix"=>round($prix,2),"prix_ttc"=>round($prix*$infos['tva'],2)));
		
		//Contrat
		if($id_copieur_contrat){
			$cc["id_copieur_contrat"]=$id_copieur_contrat;
			$cc["etat"]= $finale?"fini":"accepte";
			ATF::copieur_contrat()->u($cc,$s);

			$affaire_cout_page["id_affaire_cout_page"]=$infos["id_affaire_cout_page"];
			$affaire_cout_page["etat"]=$finale?"terminee":"facture";
			ATF::affaire_cout_page()->u($affaire_cout_page,$s);
		}


		//***************************************************************************************
		
		if($preview){
			$this->move_files($last_id,$s,true,$infos["filestoattach"]); // Génération du PDF de preview
			ATF::db($this->db)->rollback_transaction();
			return $this->cryptId($last_id);
		}else{
			$this->move_files($last_id,$s,false,$infos["filestoattach"]); // Génération du PDF avec les lignes dans la base	
			/* MAIL */
			if($email){
				if(!$email["email"]){
					$id_contact_facturation=ATF::societe()->select($infos["id_societe"],"id_contact_facturation");
					if($id_contact_facturation){
						if(!$recipient=ATF::contact()->select($id_contact_facturation,"email")){
							ATF::db($this->db)->rollback_transaction();
							throw new error("Il n'y a pas d'email pour ce contact",166);
						}
					}else{
						ATF::db($this->db)->rollback_transaction();
						throw new error("Il n'y a pas d'email pour ce contact",166);
					}
				}else{
					$recipient = $email["email"];
				}
				$f = ATF::copieur_facture()->select($last_id);
				$from = ATF::user()->select(ATF::$usr->getID(),"email");

				$info_mail["objet"] = "Votre Facture référence : ".$f["ref"];
				$info_mail["from"] = ATF::user()->nom(ATF::$usr->getID())." <".$from.">";
				$info_mail["html"] = false;
				$info_mail["template"] = 'devis';
				$info_mail["texte"] = $email["texte"];
				$info_mail["recipient"] = $recipient;
				//Ajout du fichier
				$path = $this->filepath($last_id,"fichier_joint");		
				
				$this->facture_mail = new mail($info_mail);
				$this->facture_mail->addFile($path,$f["ref"].".pdf",true);						
				$this->facture_mail->send();
				
				if($email["emailCopie"]){
					$info_mail["recipient"] = $email["emailCopie"];
					$this->facture_copy_mail = new mail($info_mail);
					$this->facture_copy_mail->addFile($path,$f["ref"].".pdf",true);						
					$this->facture_copy_mail->send();
				}
			}
			//ATF::db($this->db)->rollback_transaction();
			ATF::db($this->db)->commit_transaction();
		}

		ATF::affaire_cout_page()->redirection("select",$infos["id_affaire_cout_page"]);
		
		return $this->cryptId($last_id);
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
		if (!$infos['id_copieur_facture']) return false;
		$cf = $this->select($infos['id_copieur_facture']);
		if ($infos['value'] == "undefined") $infos["value"] = "";		
		switch ($infos['key']) {
			// Sécurité, n'exécuter une action que pour ces champs
			case "date_effective":	
				
				if(strtotime($cf['date']) > strtotime($infos['value'])){
					throw new error("Une facture ne peut pas avoir une ".ATF::$usr->trans($infos['key'],$this->table)." antérieure à la date d'edition (ici ".date("d-m-Y",strtotime($cf['date'])).")",880);
				}

				$toU = array("id_copieur_facture"=>$infos['id_copieur_facture'],"etat"=>"payee",$infos['key']=>$infos['value']);
				$this->u($toU);

				ATF::$msg->addNotice(loc::mt(
					ATF::$usr->trans("dates_modifiee",$this->table)
					,array("date"=>ATF::$usr->trans($infos['key'],$this->table))
				));
			break;

			default:
				parent::updateDate($infos,$s,$request);
			break;
		}

		ATF::affaire_cout_page()->redirection("select",$cf["id_affaire_cout_page"]);
		return true;
	}	

	public function select_all($order_by=false,$asc='desc',$page=false,$count=false) {
		$this->q->addField("TO_DAYS(IF(copieur_facture.date_effective IS NOT NULL,copieur_facture.date_effective,NOW())) - TO_DAYS(copieur_facture.date_previsionnelle)","retard");

		return parent::select_all($order_by,$asc,$page,$count);

	}

	public function getLastFacture($id_affaire,$idRef=false) {
		$this->q->reset()
				->addField("id_copieur_facture")
				->addField("releve_compteurNB")
				->addField("releve_compteurC")
				->addField("date")
				->addField("ref")
				->where('id_affaire_cout_page',$id_affaire)
				->setLimit(1);

		if ($idRef){
			$this->q->where('id_copieur_facture',$idRef,"OR","","<")->addOrder('id_copieur_facture','desc');
		} else {
			$this->q->addOrder('date','desc');
		}
		return $this->select_row();
	}

};
?>