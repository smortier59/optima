<?
/**
 * Classe affaire
 * @package Optima
 */
require_once dirname(__FILE__)."/../affaire.class.php";
class affaire_absystech extends affaire {
	/* ID du user de Seb */
	private $idSmortier = 3;
	/**
	 * Mapping prévu pour un autocomplete sur produit
	 * @var array
	 */
	public static $autocompleteMapping = array(
		array("name"=>'id', "mapping"=>0),
		array("name"=>'nom', "mapping"=>1),
		array("name"=>'date', "mapping"=>2),
		array("name"=>'etat', "mapping"=>3)
	);

	/**
	 * Constructeur
	 */
	public function __construct() {
		parent::__construct();

		$this->actions_by = array("insert"=>"devis","update"=>"devis");
		$this->stats_types = array("CA","marge","marge_detail");
		$this->forecast = array('20'=>'20%','40'=>'40%','60'=>'60%','80'=>'80%');

		$this->table = "affaire";
		$this->colonnes['fields_column'] = array(
			'affaire.id_societe'
			,'affaire.affaire'
			,'affaire.etat'=>array("renderer"=>"etatAffaire","width"=>30)
			,'affaire.date'
			,'affaire.forecast'=>array("renderer"=>"progress","rowEditor"=>"forecastUpdate","width"=>100)
			,'marge'=>array("custom"=>true,"aggregate"=>array("avg","min","max","sum"/*,"stddev","variance"*/),"align"=>"right","renderer"=>"margeBrute","type"=>"decimal","width"=>100)
			,'margenette'=>array("custom"=>true,"aggregate"=>array("avg","min","max","sum"/*,"stddev","variance"*/),"align"=>"right","renderer"=>"margeBrute","type"=>"decimal","width"=>100)
			,'marge_commandee'=>array("custom"=>true,"aggregate"=>array("avg","min","max","sum"/*,"stddev","variance"*/),"align"=>"right","renderer"=>"money","type"=>"decimal","width"=>100)
			,'pourcent'=>array("renderer"=>"percent","custom"=>true,"aggregate"=>array("avg","min","max"),"width"=>80)
		);

		$this->colonnes['primary'] = array(
			"etat"
			,"date"
			,"id_societe"=>array("updateOnSelect"=>true,"custom"=>true)
			,"affaire"
			,"forecast"
			,"id_termes"=>array("updateOnSelect"=>true,"custom"=>true)
			,"code_commande_client"=>array("updateOnSelect"=>true,"custom"=>true)
			,"contrat_maintenance"=>array("updateOnSelect"=>true,"xtype"=>"textarea","width"=>400,"custom"=>true)


		);

		$this->colonnes['panel']['maintenance'] = array(
			"date_fin_maintenance",
			"rappel_annee",
			"jours_inclus"
		);

		$this->fieldstructure();

		$this->onglets = array(
			'devis'=>array('opened'=>true,'function'=>'toutesRevisions')
			,'commande'=>array('opened'=>true)
			,'bon_de_commande'=>array('opened'=>true)
			//,'planification'
			//,'intervention'
			,'stock'=>array('opened'=>true)
			,'livraison'
			,'facture'=>array('opened'=>true)
			,'suivi'
			,'hotline'
		);

		$this->autocomplete = array(
			"view"=>array("affaire.id_affaire","affaire.date","affaire.etat")
		);

		$this->colonnes['bloquees']['update'] = array("date","etat","id_commercial","date_fin_maintenance");


		$this->addPrivilege("getAllForMenu");
		$this->addPrivilege("u","update");
		$this->addPrivilege("update_termes","update");
		$this->addPrivilege("update_forecast","update");
		$this->addPrivilege("autocompleteHotlineForm");
		$this->addPrivilege("setForecast","update");
		// GED
		$this->quick_action['select']["affiche_ged"]=array('privilege'=>'select');

		//$this->selectExtjs=true;
		$this->foreign_key['id_commercial'] = "user";
	}

	/**
	 * Sert à trier la colonne marge et pourcentage
	 * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	 * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	 */
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){
		if(!$page) $page=0;
		$this->q
			->addJointure("affaire","id_affaire","commande","id_affaire")
			->addJointure("affaire","id_affaire","facture","id_affaire")
			->addJointure("affaire","id_affaire","hotline","id_affaire")
			->addJointure("hotline","id_hotline","hotline_interaction","id_hotline")

			->addGroup("affaire.id_affaire")
			->addField("(SUM(facture.prix)
							 -IF(`commande`.`prix_achat` IS NULL OR `commande`.`etat` = 'annulee', 0, `commande`.`prix_achat`))","marge")
			->addField("(SUM(facture.prix)
			 			-IF(`commande`.`prix_achat` IS NULL OR `commande`.`etat` = 'annulee', 0, `commande`.`prix_achat`)
						-IF(COUNT(`hotline`.`id_hotline`)=0,
							0,
						    (SUM(`hotline_interaction`.`credit_presta`)+SUM(`hotline_interaction`.`credit_dep`))*".__COUT_HORAIRE_TECH__.")
						 	)","margenette")
			->addField("IF(`commande`.`prix_achat` IS NULL OR `commande`.`etat` = 'annulee', 0, commande.prix-`commande`.`prix_achat`)","marge_commandee")
			->addField("(SUM(facture.prix)-IF(`commande`.`prix_achat` IS NULL OR `commande`.`etat` = 'annulee', 0, `commande`.`prix_achat`)) / SUM(facture.prix)","pourcent")
			->setView(array("align"=>array("marge"=>"right","pourcent"=>"center")));
		$this->saFilter();
		$return = parent::select_all($order_by,$asc,$page,$count);
		return $return;
	}

	/**
	 * Renvoi les affaires
	 * Pour un appel en AJAX
	 * @author Quentin JANON <qjanon@absystech.fr>
	 */
	public function getAllForMenu(&$infos,$s,$f) {
		$tmp = json_decode(ATF::devis()->getAllForMenu($infos,$s,$f),true);
		/*$r[] = array(
			"xtype"=>"textfield",
			"anchor"=>"98%",
			"id"=>"searchIntoAffaire",
			"emptyText"=>ATF::$usr->trans("search")
		);
		$r[] = "-";*/
		foreach ($tmp as $k=>$i) {
			$i["iconCls"] = "smallIcon".ucfirst($this->select($i['id'],'etat'));
			$r[] = $i;
		}
		return json_encode($r);
	}

	/**
	 * Filtrage d'information selon le profil
	 * @author Yann GAUTHERON <ygautheron@absystech.fr>
	 */
	protected function saFilter(){
		if (ATF::$usr->get("id_profil")==11) {
			// Profil apporteur d'affaire
			$this->q
				->orWhere("affaire.id_commercial",ATF::$usr->getID(),"filtreGeneral","=",true);
		}
	}

	/**
	 * Utilisée pour les statistiques
	 * @author DEV <dev@absystech.fr>
	 * @todo ==> querier !!!
	 */
	public function forecast() {
		$this->q->reset()
		->addJointure("affaire","id_affaire","devis","id_affaire")
		->addField("SUM(`devis`.`prix`*(`affaire`.`forecast`/100))","ca")
		->addField("SUM((`devis`.`prix`-`devis`.`prix_achat`)*(`affaire`.`forecast`/100))","marge")
		->addCondition("affaire.etat",'devis')
		->setDimension("row");
		return parent::select_all();
	}

	/**
	 * Retourne un tableau pour les graphes d'affaires, dans statistique
	 * @author DEV <dev@absystech.fr>
	 * @author Fanny DECLERCK  <fdeclerck@absystech.fr>
	 * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	 * @param array $session
	 * @param array $stats
	 * @param string $type : type de graphe, par CA /marge ou nbre de création
	 * @return array
	 */
	public function stats($stats=false,$type=false,$widget=false,$annee=NULL) {
		//on récupère la liste des années que l'on souhaite voir afficher sur les graphes
		$this->q->reset();
		/*foreach(ATF::stats()->liste_annees[$this->table] as $key_list=>$item_list){
			if($item_list){
				ATF::stats()->conditionYear($this->q,"affaire.date",$key_list);
			}
		}*/
		ATF::stats()->conditionYear(ATF::stats()->liste_annees[$this->table],$this->q,"affaire.date");

		switch ($type) {
			case "CA":
				$stats['DATA'] = ATF::facture()->stats_CA(ATF::stats()->liste_annees[$this->table]);

				$this->q->addField("DISTINCT YEAR(`date`)","years");
				$stats['YEARS'] =parent::select_all();

				return parent::stats($stats,$type);

			case "marge_detail":

				return $this->statMargeDetail($annee);

			case "marge":
				ATF::commande()->q->reset()
								->addField("commande.date","date")
								->addField("-prix_achat","prix")
								->setStrict()
								->setToString();
				ATF::facture()->q->reset()
								->addField("facture.date","date")
								->addField("prix")
								->setStrict()
								->setToString();
				$this->q->reset()
						->addUnion(ATF::commande()->sa("no_order"))
						->addUnion(ATF::facture()->sa("no_order"));
				$subQuery=$this->q->getUnion();

				//requête récupérant la marge (en utilisant la subquery)
				$this->q->reset()
						->addField("YEAR(uni.date)","year")
						->addField("MONTH(uni.date)","month")
						->addField("SUM(uni.prix)","nb")
						->setStrict()
						->setSubQuery($subQuery,'uni')
						->addGroup("year")
						->addGroup("month");

				// Nombre d'année suivant sélection ou widget
				if ($widget) {
					//$this->q->addCondition("YEAR(uni.date)",(date("Y",time())-1),"OR",false,">=");
					ATF::stats()->conditionYear(array(($annee?$annee:date("Y"))=>1,(($annee?$annee:date("Y"))-1)=>1),$this->q,"uni.date");
				} else {
					/*foreach(ATF::stats()->liste_annees[$this->table] as $key_list=>$item_list){
						if($item_list){
							//$this->q->addCondition("YEAR(uni.date)",$key_list,"OR");
							ATF::stats()->conditionYear($this->q,"uni.date",$key_list);
						}
					}*/
					ATF::stats()->conditionYear(ATF::stats()->liste_annees[$this->table],$this->q,"uni.date");
				}

				$stats['DATA'] = $this->sa("no_order",false,false,false,true);

				$this->q->reset("field,group,table,strict")
						->addField("DISTINCT YEAR(uni.date)","year");
				$stats['YEARS'] =parent::select_all(false,false,false,false,true);

				return parent::stats($stats,$type,$widget);

			default:
				return parent::stats($stats,$type,$widget);
		}
	}

	/**
	 * Retourne le tableau permettant la conception du graphe de marge avec le détail (prix de vente et d'achat)
	 * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	 * @return array
	 */
	public function statMargeDetail($annee=NULL){

		//on va récupérer les données à afficher sur le graphe
		$this->getDonneesGraphe($donnees,$annee);

		/*foreach (util::month() as $k=>$i) {
			$graph['categories']["category"][$k] = array("label"=>substr($i,0,4));
		}*/
		foreach (ATF::stats()->recupMois() as $k=>$i) {
			$graph['categories']["category"][] = array("label"=>substr($i,0,4));
		}

		$graph['params']['caption'] = "Marge en detail de l annee courante";

		/*parametres graphe*/
		$this->paramGraphe($dataset_params,$graph);

		$type=array("commande"=>"CC0000","facture"=>"006600","marge"=>"CC00CC");

		foreach ($donnees['marge'] as $key_=>$val_) {

			foreach($type as $nom=>$couleur){
				//initialisation des graphes
				if (!$graph['dataset'][$nom]) {
					$specificite=array(
						"seriesname"=>ATF::$usr->trans($nom,'stats')
					,"color"=>$couleur
					);
					if($nom=="marge")$specificite["parentYAxis"]='S';
					$graph['dataset'][$nom]["params"] = array_merge($dataset_params,$specificite);

					/*foreach ($donnees[$nom] as $val_2) {
						$graph['dataset'][$nom]['set'][$val_2['month']] = array("value"=>0,"alpha"=>100,"titre"=>"0");
					}*/
					ATF::stats()->initGraphe($graph,$nom,$type);
				}
			}
			$val_['month']=strlen($val_['month'])<2?"0".$val_['month']:$val_['month'];
			$graph['dataset']['commande']['set'][$val_['month']] = array("value"=>$donnees['commande'][$key_]['prix'],"alpha"=>100,"titre"=>ATF::$usr->trans('commande','stats')." : ".$donnees['commande'][$key_]['prix']." €");
			$graph['dataset']['facture']['set'][$val_['month']] = array("value"=>$donnees['facture'][$key_]['prix'],"alpha"=>100,"titre"=>ATF::$usr->trans('facture','stats')." : ".$donnees['facture'][$key_]['prix']." €");
			$graph['dataset']['marge']['set'][$val_['month']] = array("value"=>$val_['prix'],"alpha"=>100,"titre"=>ATF::$usr->trans('marge','stats')." : ".$val_['prix']." €");
		}
		return $graph;
	}

	/**
	 * Récupère les calculs d'achat et vente pour déterminer les marges pour chaque mois
	 * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	 */
	public function getDonneesGraphe(&$donnees,$annee=NULL){
		//récupération des éléments permettant le calcul de la marge
		ATF::commande()->q->reset()
		->addField("YEAR(date)","year")
		->addField("MONTH(date)","month")
		->addField("date")
		->addField("-SUM(prix_achat)","prix")
		->setStrict()
//		->addCondition("YEAR(date)",date('Y'))
		->addGroup("year")
		->addGroup("month");
		ATF::stats()->conditionYearSimple(ATF::commande()->q,"date",($annee?$annee:date('Y')));
		$donnees['commande']=ATF::commande()->sa("no_order");

		ATF::facture()->q->reset()
		->addField("YEAR(date)","year")
		->addField("MONTH(date)","month")
		->addField("date")
		->addField("SUM(prix)","prix")
		->setStrict()
//		->addCondition("YEAR(date)",date('Y'))
		->addGroup("year")
		->addGroup("month");
		ATF::stats()->conditionYearSimple(ATF::facture()->q,"date",($annee?$annee:date('Y')));
		$donnees['facture']=ATF::facture()->sa("no_order");

		// Création de la subQuery
		//obligé de répéter les group by et sum sinon résultat faussé
		ATF::commande()->q->setToString();
		ATF::facture()->q->setToString();
		$this->q->reset()
		->addUnion(ATF::commande()->sa("no_order"))
		->addUnion(ATF::facture()->sa("no_order"));
		$subQuery=$this->q->getUnion();

		//requête récupérant la marge (en utilisant la subquery)
		$this->q->reset()
		->addField("YEAR(uni.date)","year")
		->addField("MONTH(uni.date)","month")
		->addField("SUM(uni.prix)","prix")
		->setStrict()
		->setSubQuery($subQuery,'uni')
//		->addCondition("YEAR(uni.date)",date('Y'))
		->addGroup("year")
		->addGroup("month");
		ATF::stats()->conditionYearSimple($this->q,"uni.date",($annee?$annee:date('Y')));
		$donnees['marge']=$this->sa("no_order");
	}

	/**
	 * Mise à jour des termes de paiement
	 * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	 * @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	 * @param array &$s La session
	 * @param array $files $_FILES
	 * @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	 * @param array $nolog True si on ne désire par voir de logs générés par la méthode
	 * @return boolean
	 */
	public function update_termes($infos,&$s,$files=NULL,&$cadre_refreshed=NULL){
		if($this->u($infos)){
			ATF::$msg->addNotice(
			loc::mt(ATF::$usr->trans("notice_update_success"),array("record"=>$this->nom($infos["id_affaire"])))
			,ATF::$usr->trans("notice_success_title")
			);
			$this->redirection("select",$infos["id_affaire"]);
			return true;
		}else{
			return false;
		}
	}

	/**
	 * Mise à jour des forecast
	 * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	 * @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	 * @param array &$s La session
	 * @param array $files $_FILES
	 * @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	 * @param array $nolog True si on ne désire par voir de logs générés par la méthode
	 * @return boolean
	 */
	public function update_forecast($infos,&$s,$files=NULL,&$cadre_refreshed=NULL){
		$this->infoCollapse($infos);
		if($this->u(array(
					"id_affaire"=>$infos["id_affaire"]
			,"forecast"=>$infos["forecast"]
			))){
			ATF::$msg->addNotice(
			loc::mt(ATF::$usr->trans("notice_update_success"),array("record"=>$this->nom($infos["id_affaire"])))
			,ATF::$usr->trans("notice_success_title")
			);
			return true;
		}else{
			return false;
		}
	}

	/**
	 * Retourne false si la société est en etat douteux
	 * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	 * @return boolean
	 */
	public function can_insert($id,$infos=false){
		if (!$infos) $infos = $this->select($id);
		if($infos["societe"]["etat"] == "douteux") return false;
		return true;
	}

	/**
	 * Possibilité de supprimer seulement si l'affaire n'a ni devis ni commande ni facture
	 * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	 * @return boolean
	 */
	public function can_delete($id){
		$this->q->reset()->addJointure("affaire","id_affaire","commande","id_affaire")
		->addJointure("affaire","id_affaire","devis","id_affaire")
		->addJointure("affaire","id_affaire","facture","id_affaire")
		->addCondition("affaire.id_affaire",$id)
		->setDimension("row");

		$affaire=parent::select_all();

		if($affaire["id_facture"] || $affaire["id_commande"] || $affaire["id_devis"]){
			throw new errorATF("Il est impossible de supprimer cette affaire car il y a soit un devis soit une commande soit une facture",892);
		}else{
			return true;
		}
	}

	/**
	 * Retourne false car impossibilité, àa part si on modifie le terme XOR le code commande client
	 * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	 * @return boolean
	 */
	public function can_update($id,$infos=false){
		if(($infos["id_societe"] || $infos["id_termes"] || $infos["code_commande_client"] || $infos["date_fin_maintenance"]) || $infos["contrat_maintenance"] && count($infos)==2){
			return true;
		}else{
			throw new errorATF("Il est impossible de modifier une affaire",892);
		}
	}

	/**
	 * Autocomplete spécifique pour la hotline
	 * @author Jérémie Gwiazdowski <jgw@absystech.fr>
	 */
	public function autocompleteHotlineForm($infos,$reset=true){
		$this->q->reset()->addCondition("affaire.etat","perdue","OR",false,"<>");
		return parent::autocomplete($infos,false);
	}

	/**
	* Met à jour le forecast
	* @author Quezntin JANON <qjanon@absystech.fr>
	* @param array $infos "id_affaire" int l'id_affaire et "forecast" int le forecast
	*/
	public function setForecast($infos){
		if($infos["forecast"]>100||$infos["forecast"]<0){
			throw new errorATF(ATF::$usr->trans("invalid_range"),6512);
		}

		$this->u(array("id_affaire"=>$infos["id_affaire"],"forecast"=>$infos["forecast"]));

		$notice=ATF::$usr->trans("update_forecast",$this->table);
		ATF::$msg->addNotice($notice);
	}

	/**
    * Retourne la ref d'une affaire autre qu'avenant
    * @author Mathieu Tribouillard <mtribouillard@absystech.fr>
	* @param int $id_parent
	* @return string ref
    */
	function getRef($date,$class){
		if (!$date) {
			throw new errorATF(ATF::$usr->trans("impossible_de_generer_la_ref_sans_date"),321);
		}
		if($class=="devis"){
			$prefix="D";
		}elseif($class=="commande"){
			$prefix="C";
		}elseif($class=="facture"){
			$prefix="F";
		}
		$prefix.=strtoupper(substr(ATF::agence()->nom(ATF::$usr->get('id_agence')),0,2)).date("ym",strtotime($date));
		ATF::$class()->q->reset()
					   ->addCondition("ref",$prefix."%","AND",false,"LIKE")
					   ->addField('SUBSTRING(`ref`,8)+1',"max_ref")
					   ->addOrder('ref',"DESC")
					   ->setDimension("row")
					   ->setLimit(1);

		$nb=ATF::$class()->sa();

		// On regarde aussi les références des contrat de maintenance des copieur car ils ont le même numéro séquentiel
		/*if ($class=="devis") {
			ATF::copieur_contrat()->q->reset()
						   ->addCondition("ref",$prefix."%","AND",false,"LIKE")
						   ->addField('SUBSTRING(`ref`,8)+1',"max_ref")
						   ->addOrder('ref',"DESC")
						   ->setDimension("row")
						   ->setLimit(1);
			$nb=max(ATF::copieur_contrat()->sa(),$nb);
		}
		// On regarde aussi les références des contrat de maintenance des copieur car ils ont le même numéro séquentiel
		if ($class=="facture") {
			ATF::copieur_facture()->q->reset()
						   ->addCondition("ref",$prefix."%","AND",false,"LIKE")
						   ->addField('SUBSTRING(`ref`,8)+1',"max_ref")
						   ->addOrder('ref',"DESC")
						   ->setDimension("row")
						   ->setLimit(1);
			$nb=max(ATF::copieur_facture()->sa(),$nb);
		}*/

		if($nb["max_ref"]){
			if($nb["max_ref"]<10){
				$suffix="000".$nb["max_ref"];
			}elseif($nb["max_ref"]<100){
				$suffix="00".$nb["max_ref"];
			}elseif($nb["max_ref"]<1000){
				$suffix="0".$nb["max_ref"];
			}else{
				$suffix=$nb["max_ref"];
			}
		}else{
			$suffix="0001";
		}
		return $prefix.$suffix;
	}


	/* surcharge de la fonction pour gérer le cas d'un changement de société
	*	@author Quentin JANON <qjanon@absytech.fr>
	*
	*/
	public function updateOnSelect($infos,$force=false){
		ATF::db($this->db)->begin_transaction();
		try {
			$oldSociete = ATF::societe()->nom(ATF::affaire()->select($infos['id'],"id_societe"));
			$r = parent::updateOnSelect($infos,$force);
			if ($infos['key']=="id_societe") {
				$idNewSociete = ATF::societe()->decryptId($infos['id_value']);
				// On récupère les contraintes pour avoir les éléments rattachés a l'affaire
				foreach (ATF::db($this->db)->showConstraint($this->table) as $k=>$i) {
					// On regarde si les table ont un id_société
					if (in_array("id_societe",ATF::db()->fields($k))) {
						$c = ATF::getClass($k);
						// Si oui, alors on modifie cet id_societe pour le nouvel enregistrement
						$c->q->reset()->where("id_affaire",$infos['id']);
						$el = $c->sa();
						foreach ($el as $enr) {
							$enr['id_societe'] = $idNewSociete;
							$c->u($enr);
						}
					}
				}
				// Création de suivi automatique
				$suivi = array(
					"id_user"=>ATF::$usr->getId()
					,"id_societe"=>$idNewSociete
					,"id_affaire"=>$infos['id']
					,"texte"=>"Changement de société de l'affaire.\nAncienne société : '".$oldSociete."'.\nNouvelle société : '".ATF::societe()->nom($idNewSociete)."'"
					,"suivi_notifie"=>$this->idSmortier.(ATF::$usr->getId()!=$this->idSmortier?",".ATF::$usr->getId():"")
					,"__redirect"=>"affaire"
				);
				$id_suivi = ATF::suivi()->insert($suivi);
			}
		} catch (errorATF $e) {
			ATF::db($this->db)->rollback_transaction();
			throw $e;
		}
		ATF::db($this->db)->commit_transaction();
		return $id_suivi;
	}


	/** Recupere les devis des 30 derniers jours pour l'afficher sur le graph en page d'accueil
	* @author Morgan Fleurquin <mfleurquin@absystech.fr>
	*/
	public function widget_marge_nette(){
		$this->q->reset()
				->setStrict()
				->addField("affaire.id_commercial")
				->addField("affaire.id_affaire")
				->addCondition("affaire.date","'".date("Y-m-d 00:00:00", strtotime(date("Y-m-d")." -1 month"))."'",NULL,false,">=",false,false,true)
				->addCondition("affaire.etat","facture");
		$result= $this->select_all();


		foreach ($result as $i) {
			$nom=ATF::user()->select($i["id_user"]);
			$graph['categories']["category"][$i['user']] = array("label"=>substr($nom['prenom'],0,1).substr($nom['nom'],0,1));
		}
		$graph['params']['showLegend'] = "0";
		$graph['params']['bgAlpha'] = "0";
		$graph['categories']['params']["fontSize"] = "12";


		/*parametres graphe*/
		$this->paramGraphe($dataset_params,$graph);



		foreach ($result as $val_){
			if (!$graph['dataset'][$etat]) {
				$graph['dataset'][$etat]["params"] = array_merge($dataset_params,array(
					"seriesname"=>ATF::$usr->trans("etat_".$etat,'devis')
					,"color"=>$couleur
				));

				foreach ($result as $val_2) {
					$graph['dataset'][$etat]['set'][$val_2["id_user"]] = array("value"=>0,"alpha"=>100,"titre"=>ATF::$usr->trans("etat_".$etat,'devis')." : 0");
				}
			}
			$graph['dataset'][$etat]['set'][$val_["id_user"]] = array("value"=>$val_['nb_'.$etat],"alpha"=>100,"titre"=>ATF::$usr->trans("etat_".$etat,'devis')." : ".$val_['nb_'.$etat]);
		}
		return $graph;
	}

	/**
	* Renvoi les informations pour afficher le rapport de facturation périodique dans telescope
	* @package Telescope
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param $get array
	* @param $post array
	* @return array result
	*/
	public function _rapportFacturePeriodique($get,$post) {

		// Gestion du tri
		if (!$get['tri']) $get['tri'] = "id_hotline";
		if (!$get['trid']) $get['trid'] = "desc";

		// Gestion du limit
		if (!$get['limit']) $get['limit'] = 30;

		// Gestion de la page
		if (!$get['page']) $get['page'] = 0;

		if ($get['filters']['field-date_debut_periode']) {
 			$field = "date_debut_periode";
 		} else {
 			$field = "date";

 		}

		ATF::affaire()->q->reset()
			->addField("affaire.id_societe")
			->addField("affaire.id_affaire")
			->addField("commande.date","date_cmd")
			->addField("devis_ligne.periode","periode")
			->from("affaire","id_affaire","devis","id_affaire")
			->from("affaire","id_affaire","commande","id_affaire")
			->from("affaire","id_societe","societe","id_societe")
			->from("devis","id_devis","devis_ligne","id_devis")
			->from("affaire","id_affaire","facture","id_affaire")
			->where("devis.etat","gagne")
			->where("commande.etat","annulee","OR",false,"!=")
 			->where("DATE_FORMAT(facture."+$field+",'%Y')",$get['year'],"OR",false,"<=")
			->whereIsNotNull("devis_ligne.periode")
			->addGroup("affaire.id_affaire")
			->addGroup("affaire.id_societe")
		;

		if($get["search"]){
			header("ts-search-term: ".$get['search']);
			ATF::affaire()->q->setSearch($get["search"]);
		}

		// TRI
		switch ($get['tri']) {
			case 'id_societe':
			case 'id_affaire':
				$get['tri'] = "affaire.".$get['tri'];
			break;
		}

		if(!$get['noLimit']) $this->q->setLimit($get['limit']);

		$affaires = ATF::affaire()->select_all($get['tri'],$get['trid'],$get['page'],true);

		foreach ($affaires['data'] as $k=>$lines) {
			foreach ($lines as $k_=>$val) {
				if (strpos($k_,".")) {
					$tmp = explode(".",$k_);
					$affaires['data'][$k][$tmp[1]] = $val;
					unset($affaires['data'][$k][$k_]);
				}
			}
		}

		foreach ($affaires['data'] as $k=>$line) {
			ATF::facture()->q->reset()->where('id_affaire',$line['id_affaire_fk'])->where("DATE_FORMAT(facture.".$field.",'%Y')",$get['year']);
			foreach (ATF::facture()->sa() as $key=>$i) {
				$affaires['data'][$k][strftime("%b",strtotime($i[$field]))] += $i['prix'];
			}
		}
		// Envoi des headers
		header("ts-total-row: ".$affaires['count']);
		header("ts-max-page: ".ceil($affaires['count']/$get['limit']));
		header("ts-active-page: ".$get['page']);

		return $affaires;
	}

	public function _export_rapport_facturation_periodique(&$get,$post) {

        include_once __ATF_PATH__."libs/PHPExcel/Classes/PHPExcel.php";
        $o = new PHPExcel();
        $o->getProperties()
           ->setCreator('Quentin JANON <qjanon@absystech.fr>')
           ->setTitle('Export listing de la facturation périodique')
           ->setDescription("Document reprenant le listing de la facturation périodique")
           ->setCategory('export')
           ;

        $s = $o->getSheet(0);
        $s->setTitle("Facturation périodique");

        $get['noLimit'] = true;
		$data = self::_rapportFacturePeriodique($get,$post);
		$data = $data['data'];
        // HEader
        $h = array("Société","Affaire","Date de commande","Jan","Fév","Mar","Avr","Mai","Juin","Jui","Aou","Sept","Oct","Nov","Déc.");

        $row = 1;
        $s->fromArray($h," ","A".$row);

        $header = 'a1:z1';
        // $ews->getStyle($header)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('00ffff00');
        $style = array(
            'font' => array('bold' => true),
            'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,),
        );
        $s->getStyle($header)->applyFromArray($style);

        $row = 3;
        foreach ($data as $k=>$i) {

            $d = array(
                $i['id_societe'],
                $i['id_affaire']." (".$i['periode'].")",
                $i['date_cmd'],
                number_format($i['Jan'],2),
                number_format($i['Feb'],2),
                number_format($i['Mar'],2),
                number_format($i['Apr'],2),
                number_format($i['May'],2),
                number_format($i['Jun'],2),
                number_format($i['Jul'],2),
                number_format($i['Aug'],2),
                number_format($i['Sept'],2),
                number_format($i['Oct'],2),
                number_format($i['Nov'],2),
                number_format($i['Dec'],2),
            );

            $s->fromArray($d," ","A".$row);
            $row++;
        }

        for ($col = ord('a'); $col <= ord('z'); $col++) {
            $s->getColumnDimension(chr($col))->setAutoSize(true);
        }

        $writer = \PHPExcel_IOFactory::createWriter($o, 'Excel5');

        $fn = $this->filepath(ATF::$usr->getId(),"rapport_facturation_periodique",true);
        util::file_put_contents($fn,"");
        $writer->save($fn);
        $return['URL'] = __ABSOLUTE_WEB_PATH__."/affaire-".ATF::user()->cryptId(ATF::$usr->getId())."-rapport_facturation_periodique.temp";

        return $return;
	}

/* PARTIE DES FONCTIONS POUR TELESCOPE*/

	/**
  *
  * Fonctions _GET pour telescope
  * @package Telescope
  * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
  * @param $get array contient le tri, page limit et potentiellement un id.
  * @param $post array Argument obligatoire mais inutilisé ici.
  * @return array un tableau avec les données
  */
  public function _GET($get,$post) {

    // Gestion du tri
    if (!$get['tri'] || $get['tri'] == 'action') $get['tri'] = "affaire.date";
    if (!$get['trid']) $get['trid'] = "desc";

    // Gestion du limit
    if (!$get['limit']) $get['limit'] = 30;

    // Gestion de la page
    if (!$get['page']) $get['page'] = 0;

    $colsData = array("affaire.*","societe.societe");

    $this->q->reset();

    if ($get['id']) $colsData = array("affaire.*");

    $this->q->addField($colsData);
    $this->q->from("affaire","id_societe","societe","id_societe");

    if($get["search"]){
      header("ts-search-term: ".$get['search']);
      $this->q->setSearch($get['search']);
    }

	// Filtre sur l'etat de l'affaire
	if ($get['filters']['devis'] == "on") {
		$this->q->where("affaire.etat","devis","OR","etatAffaire");
	}
	if ($get['filters']['commande'] == "on") {
		$this->q->where("affaire.etat","commande","OR","etatAffaire");
	}
	if ($get['filters']['facture'] == "on") {
		$this->q->where("affaire.etat","facture","OR","etatAffaire");
	}
	if ($get['filters']['terminee'] == "on") {
		$this->q->where("affaire.etat","terminee","OR","etatAffaire");
	}
	if ($get['filters']['perdue'] == "on") {
		$this->q->where("affaire.etat","perdue","OR","etatAffaire");
	}



    if ($get['id']) {

		$this->q->where("affaire.id_affaire",$get['id'])->setCount(false)->setDimension('row');
		$data = $this->sa();

		ATF::devis()->q->reset()->addField("CONCAT(SUBSTR(user.prenom, 1,1),'. ',user.nom)","user")
								->addField("devis.*")
								->from("devis","id_user","user","id_user")
								->where("devis.id_affaire",$get['id'])->addOrder('id_devis', 'desc');
		$data["devis"] = ATF::devis()->sa();

		foreach ($data as $key => $value) {
			if($key == "id_societe") $data["societe"] = ATF::societe()->select($value);
			//if($key == "id_contact") $data["contact"] = ATF::contact()->select($value);
			if($key == "id_commercial") $data["user"] = ATF::user()->select($value);
			//if($key == "id_user_technique") $data["user_technique"] = ATF::user()->select($value);
			//if($key == "id_user_admin") $data["user_admin"] = ATF::user()->select($value);

			//$data["fichier_joint"] = $data["documentAnnexes"] = false;

			//if (file_exists($this->filepath($get['id'],"fichier_joint"))) $data["fichier_joint"] = true;
			//if (file_exists($this->filepath($get['id'],"documentAnnexes"))) $data["documentAnnexes"] = true;


			unset($data["id_societe"],  $data["id_commercial"]);
		}

		foreach ($data["devis"] as $key => $value) {
			$data['devis'][$key]["fichier_joint"] = $data['devis'][$key]["documentAnnexes"] = false;

			if (file_exists(ATF::devis()->filepath($value['id_devis'],"fichier_joint"))) $data['devis'][$key]["fichier_joint"] = true;
			if (file_exists(ATF::devis()->filepath($value['id_devis'],"documentAnnexes"))) $data['devis'][$key]["documentAnnexes"] = true;
		}

		/*$this->q->reset()->where("affaire.id_affaire", $data["id_affaire"]);
		$data["affaireAffaire"] = $this->sa();
		*/
		$data["idcrypted"] = $this->cryptId($get["id_affaire"]);

    } else {
      $this->q->setLimit($get['limit'])->setCount();
      $data = $this->select_all($get['tri'],$get['trid'],$get['page'],true);
    }



    if($get['id']){
      $return = $data;
    }else{
      header("ts-total-row: ".$data['count']);
      header("ts-max-page: ".ceil($data['count']/$get['limit']));
      header("ts-active-page: ".$get['page']);
      $return = $data['data'];
    }
    return $return;
  }


	/** Fonction qui génère les résultat pour les champs d'auto complétion affaire
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
	public function _ac($get,$post) {
		//$length = 25;
		//$start = 0;

		$this->q->reset();

		// On ajoute les champs utiles pour l'autocomplete
		$this->q->addField("affaire.id_affaire","id_affaire")
				->addField("affaire.affaire","affaire")
				->addField("affaire.etat","etat");

		if ($get['q']) {
			$this->q->setSearch($get["q"]);
		}

		if ($get['id_societe']) {
			$this->q->where("affaire.id_societe",$get["id_societe"]);
		}

		//$this->q->setLimit($length,$start)->setPage($start/$length);

		return $this->select_all();
	}

	/** Fonction qui génère les résultat pour les champs d'auto complétion affaire seulement différent de perdu pour l'echeancier
	* @author Cyril Charlier <ccharlier@absystech.fr>
	*/
	public function _acSpecial($get,$post) {
		//$length = 25;
		//$start = 0;

		$this->q->reset();

		// On ajoute les champs utiles pour l'autocomplete
		$this->q->addField("affaire.id_affaire","id_affaire")->addField("affaire.affaire","affaire")->addField("affaire.etat","etat");

		if ($get['q']) {
			$this->q->setSearch($get["q"]);
		}

		if ($get['id_societe']) {
			$this->q->where("affaire.id_societe",$get["id_societe"]);
		}
		$this->q->AndWhere('affaire.etat','perdue',false,'<>');
		//$this->q->setLimit($length,$start)->setPage($start/$length);

		return $this->select_all();
	}




};

class affaire_att extends affaire_absystech {
	/**
	* Retourne la marge effectuée entre le début de l'année passée en paramètre et NOW()
    * @author Yann GAUTHERON <ygautheron@absystech.fr>, Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param int $offset Décalage de l'année demandé
	* @return int
	*/
	public function getMargeTotaleDepuisDebutAnnee($offset=0,$mois=NULL){
		if(!$mois)$mois=date("m");
		$annee = date("Y",time()) + $offset;

		ATF::commande()->q->reset()
							->addField("commande.date","date")
							->addField("-prix_achat","prix")
							->setStrict()
							->setToString();
		ATF::facture()->q->reset()
						->addField("facture.date","date")
						->addField("prix")
						->setStrict()
						->setToString();
		$this->q->reset()
				->addUnion(ATF::commande()->sa("no_order"))
				->addUnion(ATF::facture()->sa("no_order"));
		$subQuery=$this->q->getUnion();


		//requête récupérant la marge (en utilisant la subquery)
		$this->q->reset()
				->addField("SUM(uni.prix)")
				->setStrict()
				->setSubQuery($subQuery,'uni')
				->setDimension("cell");

		if($mois<7){
			$this->q->setBetweenDate(array("champs_date"=>"uni.date"
												,"debut"=>($annee-1)."-07-01"
												,"fin"=>$annee.date("-m-d")));
		}else{
			$this->q->setBetweenDate(array("champs_date"=>"uni.date"
												,"debut"=>$annee."-07-01"
												,"fin"=>$annee.date("-$mois-d")));
		}

		return $this->sa(false,false,false,false,true);
	}

	/**
    * Retourne la ref d'une affaire autre qu'avenant
    * @author Mathieu Tribouillard <mtribouillard@absystech.fr>
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $id_parent
	* @return string ref
    */
	function getRef($date,$class){
		if (!$date) {
			throw new errorATF(ATF::$usr->trans("impossible_de_generer_la_ref_sans_date"),321);
		}
		if($class=="devis"){
			$prefix="AD";
		}elseif($class=="commande"){
			$prefix="AC";
		}elseif($class=="facture"){
			$prefix="AF";
		}
		$prefix.=strtoupper(substr(ATF::agence()->nom(ATF::$usr->get('id_agence')),0,2)).date("ym",strtotime($date));
		ATF::$class()->q->reset()
					   ->addCondition("ref",$prefix."%","AND",false,"LIKE")
					   ->addField('SUBSTRING(`ref`,9)+1',"max_ref")
					   ->addOrder('ref',"DESC")
					   ->setDimension("row")
					   ->setLimit(1);

		$nb=ATF::$class()->sa();

		if($nb["max_ref"]){
			if($nb["max_ref"]<10){
				$suffix="000".$nb["max_ref"];
			}elseif($nb["max_ref"]<100){
				$suffix="00".$nb["max_ref"];
			}elseif($nb["max_ref"]<1000){
				$suffix="0".$nb["max_ref"];
			}else{
				$suffix=$nb["max_ref"];
			}
		}else{
			$suffix="0001";
		}
		return $prefix.$suffix;
	}





};
class affaire_wapp6 extends affaire_absystech { }
class affaire_demo extends affaire_absystech { }

?>
