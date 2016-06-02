<?
require_once dirname(__FILE__)."/../devis.class.php";
class devis_boisethome extends devis {
	public function __construct() {
		parent::__construct();
		$this->table = "devis";
		$this->colonnes['fields_column'] = array(
			 'devis.ref'=>array("width"=>100,"align"=>"center")
			 ,'devis.id_societe'
			 ,'devis.resume'
			 ,'devis.nature'
			 ,'devis.id_user'=>array("width"=>150)
			 ,'devis.revision'=>array("width"=>50,"align"=>"center")
			 ,'devis.etat'=>array("renderer"=>"etat","width"=>30)
			 ,'devis.prix'=>array("aggregate"=>array("min","avg","max","sum"),"align"=>"right","renderer"=>"money","width"=>100)
			 ,'devis.marge'=>array("aggregate"=>array("min","avg","max","sum"),"align"=>"right","width"=>100)
			 ,'devis.marge_absolue'=>array("aggregate"=>array("min","avg","max"),"align"=>"right","renderer"=>"money","width"=>100)
			 ,'devis_detaille'=>array("custom"=>true,"nosort"=>true,"align"=>"center","type"=>"file","width"=>50)
			 ,'devis_reduit'=>array("custom"=>true,"nosort"=>true,"align"=>"center","type"=>"file","width"=>50)
			 ,'actions'=>array("custom"=>true,"nosort"=>true,"align"=>"center","renderer"=>"actionsDevis","width"=>80)
		 );

		 $this->colonnes['primary'] = array(
		 	"devis"
			,"note"=>array("xtype"=>"textarea")
			,"id_societe"=>array("targetCols"=>1)
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
						,array('name'=>'civilite', 'mapping'=> "civilite")
					)
				),
				"targetCols"=>1
			)
			,"date"=>array("contain_date"=>true,"targetCols"=>1)
			,"validite"=>array("contain_date"=>false,"targetCols"=>1)
			,"remise"=>array("targetCols"=>1)
			,"tva"=>array("targetCols"=>1)
			,"nature"=>array("targetCols"=>2)
		);

		$this->colonnes['panel']['lots'] = array(
			"lots"=>array("custom"=>true)
		);

		$this->colonnes['panel']['produits'] = array(
			"produits"=>array("custom"=>true)
		);

		$this->colonnes['panel']['articles'] = array(
			"articles"=>array("custom"=>true)
		);

		$this->colonnes['panel']['total'] = array(
			"prix"=>array("readonly"=>true)
			,"prix_achat"=>array("readonly"=>true)
			,"marge"=>array("readonly"=>true)
			,"marge_absolue"=>array("readonly"=>true)
		);

		$this->colonnes['panel']['courriel'] = array(
			"email"=>array("custom"=>true,'null'=>true)
			,"emailCopie"=>array("custom"=>true,'null'=>true)
			,"emailTexte"=>array("custom"=>true,'null'=>true,"xtype"=>"htmleditor")
		);

		// Propriété des panels
		$this->panels['lots'] =
		$this->panels['produits'] =
		$this->panels['articles'] = array("visible"=>true, 'nbCols'=>1);
		$this->panels['total'] = array("visible"=>true,'nbCols'=>4);
		$this->panels['courriel'] = array('nbCols'=>2);

		// Champs masqués
		$this->colonnes['bloquees']['insert'] =
		$this->colonnes['bloquees']['cloner'] =
		$this->colonnes['bloquees']['update'] =  array('ref','id_user','cause_perdu','id_affaire','tva','revision','etat','mail','mail_copy','mail_text');
		$this->colonnes['bloquees']['select'] = array('email','emailCopie','emailTexte');

		$this->quickMail = true;
		$this->fieldstructure();
		$this->onglets = array('devis_lot');
		$this->field_nom = "ref";

		$this->files = array(
			"devis_detaille"=>array("type"=>"pdf","preview"=>true,"quickMail"=>true),
			"devis_reduit"=>array("type"=>"pdf","preview"=>true,"quickMail"=>true)
			//,"documentAnnexes"=>array("custom"=>true,'type'=>"zip","quickMail"=>true,"multiUpload"=>true)
		);
		$this->addPrivilege("sendMailDevis","update");
		$this->addPrivilege("setInfos","update");
		$this->addPrivilege("annulation","update");
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
			$this->u(array("id_devis"=>$devis["id_devis"],"etat"=>"perdu","date_modification"=>date("Y-m-d H:i:s")),$s);

			ATF::$msg->addNotice(
				loc::mt(ATF::$usr->trans("notice_devis_perdu"),array("record"=>$this->nom($infos["id_devis"])))
				,ATF::$usr->trans("notice_success_title")
			);

			$this->redirection("select_all",NULL,"devis.html");
			return true;
		}else{
			return false;
		}
	}

	/**
	 * Méthode quif ait le portail pour l'annulation d'un devis, redirige vers perdu, annule ou remplacement.
	 * @author Quentin JANON <qjanon@absystech.fr>
	 * @author Yann GAUTHERON <ygautheron@absystech.fr>
	 * @param array $infos
	 * @param array &$s La session
	 * @param array $files $_FILES
	 * @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	 */
	public function annulation($infos,&$s,$files=NULL,&$cadre_refreshed){
		if (!$infos['action'] || !$infos['id']) return false;
		ATF::db($this->db)->begin_transaction();
			switch ($infos['action']) {
				case "perdu":
				case "annule":
					$params = array('id_devis'=>$infos['id']);
					$result = $this->$infos['action']($params);
					if ($infos["raison"]) {
						// Enregistre la raison
						$params["cause_perdu"] = $infos["raison"];
						$this->u($params);
					}
					break;
				default:
					ATF::db($this->db)->rollback_transaction();
					return false;
			}
		ATF::db($this->db)->commit_transaction();
		return $result;
	}

	/**
	 * Permet d'afficher la dernière revision
	 * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	 */
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){
		if (!$this->q->alias) {
			$getFk=$this->q->getFk();
			if(!$getFk["devis.id_affaire"]){
				$d = new devis();
				$d->q->setAlias("d2")
				->addField('d2.id_devis')
				->addOrder('d2.revision','desc')
				->addCondition('d2.ref','devis.ref','OR',false,"=",false,true)
				->setLimit(1)
				->setStrict()
				->setToString();
				$subQuery = $d->select_all();

				$this->q
				->addCondition("devis.id_devis",$subQuery,'AND',false,"=",false,true)
				->addOrder('devis.date','desc');
			}
		}
		$return = parent::select_all($order_by,$asc,$page,$count);
		foreach ($return['data'] as $k=>$i) {
			$return['data'][$k]['allowCmd'] = $i['devis.etat'] == "attente";
			$return['data'][$k]['allowCancel'] = ($i['devis.etat'] == "bloque" || $i['devis.etat'] == "attente") && ATF::$usr->privilege('devis','update');
		}

		return $return;
	}

	/**
	 * Permet d'afficher la dernière revision
	 * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	 */
	public function toutesRevisions($order_by=false,$asc='desc',$page=false,$count=false){
		return parent::select_all($order_by,$asc,$page,$count);
	}

	/**
	 * Retourne true c'est à dire que la modification est possible
	 * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	 * @return boolean
	 */
	public function can_update($id,$infos=false){
		return true;
	}

	/**
	 * Surcharge de l'insert afin d'insérer les lignes de devis de créer l'affaire si elle n'existe pas
	 * @author mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	 * @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	 * @param array &$s La session
	 * @param array $files $_FILES
	 * @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	 * @param array $nolog True si on ne désire par voir de logs générés par la méthode
	 */
	public function update($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){

		if(isset($infos["preview"])){
			$preview=$infos["preview"];
		}else{
			$preview=false;
		}

		$devis=$this->select($infos[$this->table]["id_devis"]);
		if ($devis["etat"]!="attente") {
			$infos[$this->table]["nature"]='avenant';
		}
		$infos[$this->table]["ref"]=$devis["ref"];
		$infos[$this->table]["id_affaire"]=$devis["id_affaire"];
		$infos[$this->table]["revision"]=chr(ord($devis["revision"])+1);
		unset($infos[$this->table]["id_devis"]);

		ATF::db($this->db)->begin_transaction();
		//*****************************Transaction********************************

		//Le devis d'origine prend pour état la valeur NULL
		if ($infos[$this->table]["nature"]=="devis") {
			$this->u(array("id_devis"=>$devis["id_devis"],"etat"=>NULL),$s);
		}

		$last_id=$this->insert($infos,$s,$files);
		//*****************************************************************************

		if($preview){
			ATF::db($this->db)->rollback_transaction();
		}else{
			ATF::db($this->db)->commit_transaction();
			ATF::affaire()->redirection("select",$devis["id_affaire"]);
		}
		return $last_id;
	}

	/**
	 * Surcharge du cloner qui permet d'unseter les champs inutiles
	 * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	 * @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	 * @param array &$s La session
	 * @param array $files $_FILES
	 * @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	 * @param array $nolog True si on ne désire par voir de logs générés par la méthode
	 */
	public function cloner($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL,$nolog=false) {
		unset($infos[$this->table]["ref"],$infos[$this->table]["revision"],$infos[$this->table]["etat"],$infos[$this->table]["cause_perdu"],$infos[$this->table]["id_affaire"],$infos[$this->table]["id_devis"]);
		return $this->insert($infos,$s,$files,$cadre_refreshed,$nolog);
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
	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		if(isset($infos["preview"])){
			$preview=$infos["preview"];
		}else{
			$preview=false;
		}

		$lots		= json_decode($infos["values_".$this->table]["lots"],true);
		$produits 	= json_decode($infos["values_".$this->table]["produits"],true);
		$articles 	= json_decode($infos["values_".$this->table]["articles"],true);

		unset(
			$infos["devis"]["prix"],
			$infos["devis"]["prix_achat"],
			$infos["devis"]["marge"],
			$infos["devis"]["marge_absolue"]
		);

		$this->infoCollapse($infos);

		// Pour regénérer le fichier à chaque fois ?
		foreach($this->files as $key=>$item){
			if($infos["filestoattach"][$key]==="true"){
				$infos["filestoattach"][$key]="";
			}
		}

		if($infos["emailTexte"]){
			// Enregistrement des infos du mail
			$infos["mail"]=$infos["email"];
			ATF::mail()->check_mail($infos["email"]);
			$infos["mail_copy"]=$infos["emailCopie"];
			$infos["mail_text"]=$infos["emailTexte"];
		}
		unset($infos["email"],$infos["emailCopie"],$infos["emailTexte"]);
		//Si c'est une insertion et non pas un update
		if(!$infos["ref"]){
			if ($infos["nature"]=="avenant") {
				throw new error("Un avenant ne peut être la première révision d'un devis !");
			}
			$infos["ref"] = ATF::affaire()->getRef($infos["date"],"devis");
		}
		$infos["id_user"] = ATF::$usr->getID();
		$societe=ATF::societe()->select($infos["id_societe"]);
		$infos["id_societe"] = $societe["id_societe"];
		if($societe["id_pays"]!="FR") $infos["tva"] =  1;
		else $infos["tva"] =  __TVA__;

		//Vérification du devis
		$this->check_field($infos);

		if(!$articles){
			throw new error(ATF::$usr->trans("Pas d'articles ?"));
		}

		ATF::db($this->db)->begin_transaction();
		//*****************************Transaction********************************

		//Affaire
		if(!$infos["id_affaire"]){
			$affaire["etat"]='devis';
			$affaire["id_societe"]=$infos["id_societe"];
			$affaire["affaire"]=$infos["devis"];
			$affaire["date"]=$infos["date"];
			$affaire["forecast"]=20;
			$affaire["id_commercial"]=$infos["id_user"];
			$infos["id_affaire"]=ATF::affaire()->insert($affaire,$s);
		}else{
			ATF::affaire()->u(array("id_affaire"=>$infos["id_affaire"],"etat"=>"devis","forecast"=>20),$s);
		}

		//Devis
		unset($infos["id_termes"],$infos["sous_total"],$infos["marge"],$infos["marge_absolue"]);
		$id_devis=parent::insert($infos,$s,NULL,$var=NULL,NULL,true);


		//Devis Ligne
		foreach($lots as $lot_k => $lot){
			foreach($lot as $k => $i){
				$k_unescape=util::extJSUnescapeDot($k);
				$lots[$lot_k][str_replace("devis_lot.","",$k_unescape)]=$i;
				unset($lots[$lot_k][$k]);
			}
		}
		foreach($produits as $produit_k => $produit){
			foreach($produit as $k=>$i){
				$k_unescape=util::extJSUnescapeDot($k);
				$produits[$produit_k][str_replace("devis_lot_produit.","",$k_unescape)]=$i;
				unset($produits[$produit_k][$k]);
			}
		}
		foreach($articles as $article_k => $article){
			foreach($article as $k=>$i){
				$k_unescape=util::extJSUnescapeDot($k);
				$articles[$article_k][str_replace("devis_lot_produit_article.","",$k_unescape)]=$i;
				unset($articles[$article_k][$k]);
			}
		}
		try {
			foreach($lots as $lot){
				$data_lot = array(
					"id_devis"	=> $id_devis,
					"libelle" 	=> $lot["libelle"],
					"optionnel" => $lot["optionnel"],
					"payer_pourcentage" => $lot["payer_pourcentage"]
				);
				$total_echeancier += $lot["payer_pourcentage"];
				$id_devis_lot = ATF::devis_lot()->insert($data_lot);
				foreach($produits as $produit){
					if ($lot["id_devis_lot"]==$produit["id_devis_lot"]) {
						$data_produit = array(
							"id_devis_lot"	=> $id_devis_lot,
							"id_produit" => $produit["id_produit_fk"],
							"produit" => $produit["produit"],
							"description" => $produit["description"],
							"unite" => $produit["unite"],
							"quantite" => $produit["quantite"],
							"lambda" => $produit["lambda"]
						);
						$id_devis_lot_produit = ATF::devis_lot_produit()->insert($data_produit);
						$prix_article = $prix_achat_article = 0;
						foreach($articles as $article){
							if ($produit["id_devis_lot_produit"]==$article["id_devis_lot_produit"]) {
								$data_article = array(
									"id_devis_lot_produit"	=> $id_devis_lot_produit,
									"article"=>$article["id_article"],
									"id_article"=>$article["id_article_fk"],
									"quantite"=>$article["quantite"],
									"unite"=>$article["unite"],
									"conditionnement"=>$article["conditionnement"],
									"id_fournisseur"=>$article["id_fournisseur_fk"],
									"prix_achat"=>$article["prix_achat"],
									"id_marge"=>$article["id_marge_fk"],
									"visible"=>$article["visible"]
								);
								$id_devis_lot_produit_article = ATF::devis_lot_produit_article()->insert($data_article);

								$prix_article += $article["quantite"] * $article["prix_achat"] * ATF::marge()->select($article["id_marge_fk"],"taux");
								$prix_achat_article += $article["quantite"] * $article["prix_achat"];
							}
						}
						$devis["prix"] += $produit["quantite"] * $prix_article;
						$devis["prix_achat"] += $produit["quantite"] * $prix_achat_article;
					}
				}
			}
		} catch (Exception $e) {
			throw new error($e->getMessage() . " (".print_r($lot,true)." / ".print_r($produit,true)." / ".print_r($article,true).")");
		}

		if ($total_echeancier!=100) {
			throw new error("La somme des pourcentage à payer en fin des lots doit faire au total 100% !");
		}

		$this->u(array(
			"id_devis"=>$id_devis,
			"prix"=>$devis["prix"],
			"prix_achat"=>$devis["prix_achat"],
			"marge"=>100 * ($devis["prix"]-$devis["prix_achat"]) / $devis["prix"],
			"marge_absolue"=>$devis["prix"]-$devis["prix_achat"]
		));

		//*****************************************************************************
		if($preview){
			$this->move_files($id_devis,$s,true,$infos["filestoattach"]); // Génération du PDF de preview
			ATF::db($this->db)->rollback_transaction();
			return $this->cryptId($id_devis);
		}else{
			$this->move_files($id_devis,$s,false,$infos["filestoattach"]); // Génération du PDF avec les lignes dans la base
			ATF::db($this->db)->commit_transaction();
		}

		if(is_array($cadre_refreshed)){
			ATF::affaire()->redirection("select",$infos["id_affaire"]);
		}
		return $this->cryptId($id_devis);
	}

	/**
	 * Retourne true c'est à dire que la suppression est possible
	 * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	 * @return boolean
	 */
	public function can_delete($id){
		return $this->can_update($id);
	}

	/**
	 * Fonction de suppression d'un élément de la table
	 * Utilisation d'un querier de suppression
	 * @param int $infos le ou les identificateurs de l'élément que l'on désire inséré
	 * @author mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	 * @param array &$s La session
	 * @param array $files $_FILES
	 * @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	 * @return boolean TRUE si cela s'est correctement passé
	 */
	public function delete($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL) {
		if (is_numeric($infos) || is_string($infos)) {
			$devis=$this->select($this->decryptId($infos));

			ATF::db($this->db)->begin_transaction();
			//*****************************Transaction********************************
			$devis_ref=$this->select_special("ref",$devis["ref"]);
			foreach($devis_ref as $key=>$item){
				parent::delete($item["id_devis"],$s);
			}

			//Affaire
			$this->q->reset()->addCondition("id_affaire",$devis["id_affaire"])->end();
			$tab_devis = $this->select_all();
			ATF::facture()->q->reset()->addCondition("id_affaire",$devis["id_affaire"])->end();
			$tab_facture = ATF::facture()->sa();

			if(!$tab_facture && !$tab_devis) {
				ATF::affaire()->delete($devis["id_affaire"],$s);
				unset($devis["id_affaire"]);
			}

			//Dans le cas d'une révision, le devis précédent passe en attente
			if($devis["nature"]=="devis" && $devis["revision"]!="A"){
				$revision=chr(ord($devis["revision"])-1);
				$this->q->reset()
				->addCondition("revision",$revision)
				->addCondition("id_affaire",$devis["id_affaire"])
				->setDimension("row");

				$anc_devis=$this->sa();
				$this->u(array(
					"id_devis"=>$anc_devis["id_devis"],
					"etat"=>"attente"
				));
			}

			ATF::db($this->db)->commit_transaction();
			//*****************************************************************************

			if($devis["id_affaire"]){
				ATF::affaire()->redirection("select",$devis["id_affaire"]);
			}else{
				$this->redirection("select_all",NULL,"devis.html");
			}

			return true;

		} elseif (is_array($infos) && $infos) {

			foreach($infos["id"] as $key=>$item){
				$this->delete($item,$s,$files,$cadre_refreshed);
			}
		}
	}

	/**
    * Retourne la valeur par défaut spécifique aux données passées en paramètres
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $field
	* @return string
    */
	public function default_value($field){
		switch ($field) {
			case "validite":
				return date("Y-m-d",strtotime("+3 month"));

			default:
				break;
		}
	}

	public function setInfos($infos){
		if ($infos["field"]=="etat") {
			return $this->u(array(
				"id_devis"=> $this->decryptId($infos["id_devis"]),
				$infos["field"] => $infos["value"]
			));
		}
	}
}