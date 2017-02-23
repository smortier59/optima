<?
/** Classe devis
 * @package Optima
 * @subpackage Absystech
 */
require_once dirname(__FILE__)."/../devis.class.php";
class devis_absystech extends devis {
	/**
	 * Mail de devis
	 * @author Mathieu Tribouillard <mtribouillard@absystech.fr> Jérémie Gwiazdowski <jgw@absystech.fr>
	 */
	private $devis_mail;

	/**
	 * Mail de copy de devis
	 * @author Mathieu Tribouillard <mtribouillard@absystech.fr> Jérémie Gwiazdowski <jgw@absystech.fr>
	 */
	private $devis_copy_mail;

	/**
	 * Mail actuel
	 * @var mixed
	 */
	private $current_mail=NULL;

	/**
	 * Constructeur
	 */
	public function __construct() {
		parent::__construct();
		$this->table = "devis";
		$this->colonnes['fields_column'] = array(
			 'devis.ref'=>array("width"=>100,"align"=>"center")
			 ,'devis.id_societe'
			 ,'devis.resume'
			 ,'devis.id_user'=>array("width"=>150)
			 ,'devis.revision'=>array("width"=>50,"align"=>"center")
			 ,'devis.etat'=>array("renderer"=>"etat","width"=>30)
			 ,'devis.prix'=>array("aggregate"=>array("min","avg","max","sum"),"align"=>"right","renderer"=>"money","width"=>100)
			 ,'devis.prix_achat'=>array("aggregate"=>array("min","avg","max","sum"),"align"=>"right","renderer"=>"money","width"=>100)
			 ,'fichier_joint'=>array("custom"=>true,"nosort"=>true,"align"=>"center","type"=>"file","width"=>50)
			 ,'actions'=>array("custom"=>true,"nosort"=>true,"align"=>"center","renderer"=>"actionsDevis","width"=>80)
		 );

		 $this->colonnes['primary'] = array(
			"id_societe"=>array("autocomplete"=>array(
				"function"=>"autocompleteAvecTermes"
				,"mapping"=>array(
					array('name'=> 'id_termes', 'mapping'=> 0)
					,array('name'=> 'id_termes_fk', 'mapping'=> 1)
					,array('name'=>'id', 'mapping'=> 2)
					,array('name'=> 'nom', 'mapping'=> 3)
					,array('name'=> 'detail', 'mapping'=> 4, 'type'=>'string' )
					,array('name'=> 'nomBrut', 'mapping'=> 'raw_3')
				)
			))
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
				)
			)
			,"date"=>array("contain_date"=>true)
			,"validite"=>array("contain_date"=>false)
			,"id_user_admin"=>array(
				"autocomplete"=>array(
					"function"=>"autocompleteAssDirection"
				)
			)
			,"id_user_technique"=>array(
				"autocomplete"=>array(
					"function"=>"autocompleteTechnicien"
				)
			)
			,"date_modification"
			,"id_opportunite"
			,"type_devis"=>array("listeners"=>array("change"=>"ATF.changeTypeDevis","select"=>"ATF.selectTypeDevis"))
			,"resume"

		);

		$this->colonnes['panel']['location'] = array(
			"duree_location",
			"prix_location"
		);


		$this->colonnes['panel']['financement'] = array(
			"duree_financement"=> array("null"=> true,"listeners"=>array("change"=>"ATF.changefinancement")),
			"cout_total_financement"=> array("null"=> true,"listeners"=>array("change"=>"ATF.changefinancement")),
			"maintenance_financement"=> array("null"=> true,"listeners"=>array("change"=>"ATF.changefinancement")),
			"financement_cleodis"
		);

		$this->colonnes['panel']['redaction'] = array(
			"id_politesse_pref"
			,"id_politesse_post"
			,"id_termes"=>array(
				"type"=>"int"
				,"null"=>1
				,"default"=>1
				,"autocomplete"=>array(
					"pageSize"=>0
				)
			)
			,"acompte"
			,"id_delai_de_realisation"=>array(
				"type"=>"int"
				,"autocomplete"=>array(
					"pageSize"=>0
				)
			)
		);

		$this->colonnes['panel']['lignes'] = array(
			"produits"=>array("custom"=>true)
		);

		$this->colonnes['panel']['lignes_consommable'] = array(
			"duree_contrat_cout_copie"
			,"consommables"=>array("custom"=>true)
		);

		$this->colonnes['panel']['total'] = array(
			"sous_total"=>array("custom"=>true,"readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield")
			,"frais_de_port"=>array("custom"=>true,"readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield","quickTips"=>array('url'=>'societe_frais_port,getQuickTips.ajax'))
			,"prix"=>array("custom"=>true,"readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield")
			,"prix_achat"=>array("custom"=>true,"readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield")
			,"marge"=>array("custom"=>true,"readonly"=>true)
			,"marge_absolue"=>array("custom"=>true,"readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield")
			,"financement_mois"=>array("custom"=>true,"readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield")
			,"marge_financement"=>array("custom"=>true,"readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield")
		);

		$this->colonnes['panel']['courriel'] = array(
			"email"=>array("custom"=>true,'null'=>true)
			,"emailCopie"=>array("custom"=>true,'null'=>true)
			,"emailTexte"=>array("custom"=>true,'null'=>true,"xtype"=>"htmleditor")
		);

		// Propriété des panels
		$this->panels['redaction'] = array("visible"=>true, 'nbCols'=>1);
		$this->panels['lignes'] = array("visible"=>true, 'nbCols'=>1);
		$this->panels['lignes_consommable'] = array("visible"=>false, 'nbCols'=>1);
		$this->panels['total'] = array("visible"=>true,'nbCols'=>4);
		$this->panels['courriel'] = array('nbCols'=>2);

		// Champs masqués
		$this->colonnes['bloquees']['insert'] =
		$this->colonnes['bloquees']['cloner'] =
		$this->colonnes['bloquees']['update'] =  array('divers_1','ref','id_user','cause_perdu','tva','revision','etat','date_modification','mail','mail_copy','mail_text');
		//		$this->colonnes['bloquees']['filtre'] =  array("donnee"=>array('tache.concernes'));
		$this->colonnes['bloquees']['select'] = array('email','emailCopie','emailTexte');

		$this->quickMail = true;
		$this->fieldstructure();
		$this->onglets = array('devis_ligne');
		$this->stats_types = array("user","users");
		$this->sans_partage = true; /* Evite de se voir jeté à cause d'un droit de partage pour ce module */
		$this->field_nom = "ref";
		$this->foreign_key["id_user_admin"] = "user";
		$this->foreign_key["id_user_technique"] = "user";
		$this->foreign_key["id_politesse_pref"] = "politesse";
		$this->foreign_key["id_politesse_post"] = "politesse";
		$this->foreign_key["id_remplacant"] = "devis";

		$this->files = array(
			"fichier_joint"=>array("type"=>"pdf","preview"=>true,"quickMail"=>true)
			,"documentAnnexes"=>array("custom"=>true,'type'=>"zip","quickMail"=>true,"multiUpload"=>true)
		);
		$this->addPrivilege("unlock","update");
		$this->addPrivilege("sendMailDevis","update");
		$this->addPrivilege("annulation","update");
//				$this->addPrivilege("perdu","update");
//				$this->addPrivilege("annule","update");

	}

	/** Recupere les devis des 30 derniers jours pour l'afficher sur le graph en page d'accueil
	* @author Morgan Fleurquin <mfleurquin@absystech.fr>
	*/
	public function devis_signe(){
		$this->q->reset()
				->setStrict()
				->addField('count(*)','dif')
				->addField('devis.etat','etat')
				->addField("sum(case devis.etat when 'gagne' then 1  else 0 end)",'nb_gagne')
				->addField("sum(case devis.etat when 'attente' then 1 when 'bloque' then 1 else 0 end)",'nb_attente')
				->addField("sum(case devis.etat when 'perdu' then 1  when 'remplace' then 1 when 'annule' then 1 else 0 end)",'nb_perdu')



				->addField("CONCAT(`user`.`prenom`,' ',`user`.`nom`)","user")
				->addField("user.id_user","id_user")

				->addJointure("devis","id_user","user","id_user")

				->addCondition("devis.date","'".date("Y-m-d 00:00:00", strtotime(date("Y-m-d")." -1 month"))."'",NULL,false,">=",false,false,true)

				->addGroup("devis.id_user")
				->addOrder("dif",'desc');
		$result=parent::sa();


		foreach ($result as $i) {
			$nom=ATF::user()->select($i["id_user"]);
			$graph['categories']["category"][$i['user']] = array("label"=>substr($nom['prenom'],0,1).substr($nom['nom'],0,1));
		}
		$graph['params']['showLegend'] = "0";
		$graph['params']['bgAlpha'] = "0";
		$graph['categories']['params']["fontSize"] = "12";


		/*parametres graphe*/
		$this->paramGraphe($dataset_params,$graph);

		$liste_etat=array('perdu'=>"FF0033", 'attente'=>"2f7ed8",'gagne'=>"0000FF");

		foreach ($result as $val_){
			foreach($liste_etat as $etat=>$couleur){
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
		}
		return $graph;
	}

	public function _devis_prix($get, $post){
		$at = $this->devis_prix(true);
		ATF::define_db("db","extranet_v3_att");
		ATF::$codename = "att";
		$att = $this->devis_prix(true);
		ATF::define_db("db","extranet_v3_absystech");
		ATF::$codename = "absystech";

		return array("at"=>$at, "att"=>$att, "infos"=>array("graph"=>"marge"));
	}
	/** Recupere les devis des 30 derniers jours pour l'afficher sur le graph en page d'accueil
	* @author Morgan Fleurquin <mfleurquin@absystech.fr>
	*/
	public function devis_prix($marge = false){
		$this->q->reset()
				->setStrict()
				->addField('devis.etat','etat');

		if($marge){
			$this->q->addField("sum(case devis.etat when 'gagne' then devis.prix-devis.prix_achat  else 0 end)",'nb_gagne')
					->addField("sum(case devis.etat when 'attente' then devis.prix-devis.prix_achat when 'bloque' then devis.prix-devis.prix_achat else 0 end)",'nb_attente')
					->addField("sum(case devis.etat when 'perdu' then devis.prix-devis.prix_achat  when 'remplace' then devis.prix-devis.prix_achat when 'annule' then devis.prix-devis.prix_achat else 0 end)",'nb_perdu');
		}else{
			$this->q->addField("sum(case devis.etat when 'gagne' then devis.prix  else 0 end)",'nb_gagne')
					->addField("sum(case devis.etat when 'attente' then devis.prix when 'bloque' then devis.prix else 0 end)",'nb_attente')
					->addField("sum(case devis.etat when 'perdu' then devis.prix  when 'remplace' then devis.prix when 'annule' then devis.prix else 0 end)",'nb_perdu');
		}

		$this->q->addField("CONCAT(`user`.`prenom`,' ',`user`.`nom`)","user")
				->addField("user.id_user","id_user")

				->addJointure("devis","id_user","user","id_user")
				->addJointure("devis","id_devis","commande","id_devis")

				->addCondition("devis.date","'".date("Y-m-d 00:00:00", strtotime(date("Y-m-d")." -1 month"))."'","OR","date",">=",false,false,true)
				->addCondition("commande.date","'".date("Y-m-d", strtotime(date("Y-m-d")." -1 month"))."'","OR","date",">=",false,false,true)

				->addGroup("devis.id_user")
				->addOrder("nb_gagne",'desc');
		$result=parent::sa();


		foreach ($result as $i) {
			$nom=ATF::user()->select($i["id_user"]);
			$graph['categories']["category"][$i['id_user']] = array("label"=>substr($nom['prenom'],0,1).substr($nom['nom'],0,1));
		}
		$graph['params']['showLegend'] = "0";
		$graph['params']['bgAlpha'] = "0";
		$graph['categories']['params']["fontSize"] = "12";


		/*parametres graphe*/
		$this->paramGraphe($dataset_params,$graph);

		$liste_etat=array('perdu'=>"FF0033", 'attente'=>"2f7ed8",'gagne'=>"0000FF");

		foreach ($result as $val_) {
			foreach($liste_etat as $etat=>$couleur){
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
		}
		return $graph;
	}



	public function prix_moyen($avg=NULL){
		$this->q->reset()
				->setStrict()
				->addField("COUNT(*)", "nb")
				->addJointure("devis","id_user","user","id_user")
				->addCondition("user.etat","normal")
			    ->addCondition("DATE_FORMAT(devis.date,'%Y')","2010","OR",false,">=")
				->addGroup("DATE_FORMAT(devis.date,'%Y %M')")
				->addOrder("devis.date",'asc')
				->addField("DATE_FORMAT(devis.date,'%Y %M')","hotdate")
				->addField("CONCAT(`user`.`prenom`,' ',`user`.`nom`)","user")
				->addField("user.id_user","id_user")
				->addGroup("devis.id_user");
		if($avg){
			$this->q->addCondition("devis.etat","gagne")
					->addField("SUM(devis.prix)", "sum");
		}

		$result=parent::sa();



		foreach ($result as $i) {
			$graph['categories']["category"][$i['hotdate']] = array("label"=>$i['hotdate']);
		}

		/*parametres graphe*/
		$this->paramGraphe($dataset_params,$graph);


		foreach ($result as $val_) {
			if($avg){
				if($val_["nb"]>0){
					$val_['dif'] = round($val_["sum"]/$val_["nb"] , 2);
				}else{
					$val_['dif'] = 0;
				}
			}else{

			}


			if (!$graph['dataset'][$val_['user']]) {
				$graph['dataset'][$val_['user']]["params"] = array_merge($dataset_params,array(
					"seriesname"=>$val_['user']
					,"color"=>dechex(rand(0,16777216))
				));

				foreach ($result as $val_2) {
					$graph['dataset'][$val_['user']]['set'][$val_2['hotdate']] = array("value"=>0,"alpha"=>100,"titre"=>$val_['user']." : 0");
				}
			}
			$graph['dataset'][$val_['user']]['set'][$val_['hotdate']] = array("value"=>$val_['dif'],"alpha"=>100,"titre"=>$val_['user']." : ".$val_['dif']);
		}

		//log::logger($graph , "mfleurquin");

		return $graph;
	}


	/**
	 * méthode permettant de faire les graphes des différents modules, dans statistique
	 * @author DEV <dev@absystech.fr>, Nicolas BERTEMONT <nbertemont@absystech.fr>
	 */
	public function stats($stats=false,$type=false,$widget=false) {
		//on récupère la liste des années que l'on ne souhaite pas voir afficher sur les graphes
		//on les incorpore ensuite sur les requêtes adéquates
		$this->q->reset();
		/*foreach(ATF::stats()->liste_annees[$this->table] as $key_list=>$item_list){
			if($item_list)$this->q->addCondition("YEAR(`date`)",$key_list);
		}*/
		ATF::stats()->conditionYear(ATF::stats()->liste_annees[$this->table],$this->q,"date");

		switch ($type) {
			case "pipe":
				$this->q->reset();
				$y = date("Y");
				$y1 = $y-1;
				ATF::stats()->conditionYear(array($y=> 1 , $y1=>1),$this->q,"date");

				$this->q->addField("YEAR(`date`)","year")
				->addField("MONTH(`date`)","month")
				->addField("SUM(`prix`)","nb")
				->addCondition("etat","attente")
				->addCondition("etat","bloque")
				->addGroup("year")
				->addGroup("month");
				$stats['DATA'] = parent::select_all();

				$this->q->reset("field,group");
				$this->q->addField("DISTINCT YEAR(`date`)","year");
				$stats['YEARS'] =parent::select_all();
				return parent::stats($stats,$type,$widget);

			case "user":
				$this->q->addField("YEAR(`date`)","year")
				->addField("MONTH(`date`)","month")
				->addField("COUNT(*)","nb")
				->addCondition($this->table.".id_user",ATF::$usr->getID())
				->addGroup("year")->addGroup("month");
				$stats['DATA'] = parent::select_all();

				$this->q->reset("field,group");
				$this->q->addField("DISTINCT YEAR(`date`)","years");
				$stats['YEARS'] =parent::select_all();

				return parent::stats($stats,$type,$widget);

			case "users":
				$this->q->reset();
				$this->q->addField("CONCAT(`user`.`civilite`,' ',`user`.`prenom`,' ',`user`.`nom`)","label")
				->addField("user.id_user","year")
				->addField("DATE_FORMAT(`".$this->table."`.`date`,'%Y')","y")
				->addField("DATE_FORMAT(`".$this->table."`.`date`,'%m')","month")
				->addField("COUNT(*)","nb")
				->addJointure($this->table,"id_user","user","id_user")
				->addCondition("TO_DAYS(NOW())-TO_DAYS(`".$this->table."`.`date`)","365",NULL,"sub_date","<",false,false,true)
				->addGroup("year")->addGroup("month");
				$stats['DATA'] = parent::select_all();

				$this->q->reset();
				$this->q->addField("DISTINCT ".$this->table.".`id_user`","years")
				->addJointure($this->table,"id_user","user","id_user");
				$stats['YEARS'] = parent::select_all();

				return parent::stats($stats,$type,$widget);

			default:
				return parent::stats($stats,$type,$widget);
		}
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
				->addOrder('devis.date_modification','desc');
			}
		}
		$return = parent::select_all($order_by,$asc,$page,$count);
		foreach ($return['data'] as$k=>$i) {
			if ($i['devis.etat'] == "attente" && ATF::$usr->privilege('commande','insert')) {
				$return['data'][$k]['allowCmd'] = true;
			} else {
				$return['data'][$k]['allowCmd'] = false;
			}
			if ($i['devis.etat'] == "bloque" && ATF::$usr->get("id_profil")==1) {
				$return['data'][$k]['allowUnlockDevis'] = true;
			} else {
				$return['data'][$k]['allowUnlockDevis'] = false;
			}
			if (($i['devis.etat'] == "bloque" || $i['devis.etat'] == "attente") && ATF::$usr->privilege('devis','update')) {
				$return['data'][$k]['allowCancel'] = true;
			} else {
				$return['data'][$k]['allowCancel'] = false;
			}
		}


		return $return;
	}

	/**
	 * Filtrage d'information selon le profil
	 * @author Yann GAUTHERON <ygautheron@absystech.fr>
	 */
	protected function saFilter(){
		if (ATF::$usr->get("id_profil")==11) {
			// Profil apporteur d'affaire
			$this->q
				->where("devis.id_user",ATF::$usr->getID());
		}
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
		if($devis=$this->select($id)){
			if(ATF::societe()->estFermee($devis["id_societe"])){
				throw new errorATF(ATF::$usr->trans("Impossible de modifier un devis car la société est inactive"));
			}

			ATF::commande()->q->reset()
			->addCondition("commande.id_affaire",$devis["id_affaire"])
			->setDimension("row")
			->end();
			$commande=ATF::commande()->select_all();

			//Si l'état est gagné on ne peut le modifier uniquement s'il n'y a plus d'affaire et qu'elle n'est pas annulée
			if(!$devis["etat"] || ($devis["etat"]=="gagne" && $commande && $commande["etat"]!="annulee")){
				throw new errorATF("Il est impossible de modifier un devis gagné ou un devis qui a une commande",892);
			}else{
				return true;
			}
		}else{
			return false;
		}
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
		$infos[$this->table]["ref"]=$devis["ref"];
		$infos[$this->table]["id_affaire"]=$devis["id_affaire"];
		$infos[$this->table]["revision"]=chr(ord($devis["revision"])+1);
		unset($infos[$this->table]["id_devis"]);

		ATF::db($this->db)->begin_transaction();
		//*****************************Transaction********************************

		if($infos["devis"]["type_devis"] == "consommable"){
			$infos["values_devis"]["produits"] = NULL;
		}else{
			$infos["values_devis"]["consommables"] = NULL;
		}


		//Le devis d'origine prend pour état la valeur NULL
		$this->u(array("id_devis"=>$devis["id_devis"],"etat"=>NULL,"date_modification"=>date("Y-m-d H:i:s")),$s);

		$last_id=$this->insert($infos,$s,$files);
		//*****************************************************************************

		if($preview){
			ATF::db($this->db)->rollback_transaction();
		}else{
			ATF::db($this->db)->commit_transaction();
			ATF::affaire()->redirection("select",$devis["id_affaire"]);
		}

		api::sendUDP(array("data"=>array("type"=>"devis")));

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
		unset($infos["devis"]["financement_mois"] , $infos["devis"]["marge_financement"]);
		if(ATF::societe()->estFermee($infos["devis"]["id_societe"])){
			throw new errorATF(ATF::$usr->trans("Impossible d'ajouter un devis car la société est inactive"));
		}

		if($infos["label_devis"]["id_politesse_post"] && !$infos["devis"]["id_politesse_post"]){
			if($infos["label_devis"]["id_politesse_post"] == "Veuillez agréer, Mademoiselle, l'expression de nos sentiments les meilleurs."){
				$politesse = ATF::politesse()->cryptId(12);
			}elseif($infos["label_devis"]["id_politesse_post"] == "Veuillez agréer, Madame, l'expression de nos sentiments les meilleurs."){
				$politesse = ATF::politesse()->cryptId(7);
			}else{
				$politesse = ATF::politesse()->cryptId(6);
			}
			$infos["devis"]["id_politesse_post"] = $politesse;
		}

		if($infos["label_devis"]["id_termes"] && !$infos["devis"]["id_termes"]){
				ATF::termes()->q->reset()->where("termes", $infos["label_devis"]["id_termes"]);
				$termes = ATF::termes()->select_row();
				$infos["devis"]["id_termes"] = $termes["id_termes"];
		}
		if(isset($infos["telescope"])) $telescope = true;
		if(isset($infos["preview"])){
			$preview=$infos["preview"];
		}else{
			$preview=false;
		}
		$infos_ligne = json_decode($infos["values_".$this->table]["produits"],true);
		$consommables = json_decode($infos["values_".$this->table]["consommables"],true);


		$this->infoCollapse($infos);

		if($infos["type_devis"] != "consommable"){
			// On calcul le prix par rapport aux lignes
			foreach($infos_ligne as $key=>$item){
				foreach($item as $k=>$i){
					$k_unescape=util::extJSUnescapeDot($k);
					$item[str_replace("devis_ligne.","",$k_unescape)]=$i;
					unset($item[$k]);
				}

				if(!$item["quantite"]) $item["quantite"]=0;

				$prixFinal += $item["prix"]*$item["quantite"];
			}
			$prixFinal += $infos["frais_de_port"];

			/*Formatage des numériques*/
			$infos["prix"]=util::stringToNumber($prixFinal);
			$infos["frais_de_port"]=util::stringToNumber($infos["frais_de_port"]);



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
		}
		unset($infos["email"],$infos["emailCopie"],$infos["emailTexte"]);

		//Si c'est une insertion et non pas un update
		if(!$infos["ref"]){
			$infos["ref"] = ATF::affaire()->getRef($infos["date"],"devis");
		}
		$infos["id_user"] = ATF::$usr->getID();
		$societe=ATF::societe()->select($infos["id_societe"]);
		$infos["id_societe"] = $societe["id_societe"];
		if($societe["id_pays"]!="FR") $infos["tva"] =  1;
		else $infos["tva"] =  __TVA__;

		//Vérification du devis
		$this->check_field($infos);

		if(!$infos_ligne && $infos["type_devis"] != "consommable"){
			throw new errorATF(ATF::$usr->trans("devis_ligne_inexistant"),600);
		}

		if(!$consommables && $infos["type_devis"] == "consommable"){
			throw new errorATF(ATF::$usr->trans("devis_ligne_consommable_inexistant"),600);
		}

		//Limite sur les montants selon les profils
		$profil=ATF::profil()->select(ATF::$usr->get("id_profil"));
		if(is_numeric($profil["seuil"]) && $profil["seuil"]<=$infos["prix"] && $profil["seuil"]>0){
			ATF::$msg->addNotice(
				loc::mt(ATF::$usr->trans("error_403_devis_seuil"),array("profil"=>$profil["profil"], "seuil"=>$profil["seuil"]))
				,ATF::$usr->trans("Droits_d_acces_requis_pour_cette_operation")
			);
			$infos["etat"]="bloque";
		}

		ATF::db($this->db)->begin_transaction();
		//*****************************Transaction********************************

		//Affaire

		$affaire["etat"]='devis';
		$affaire["id_societe"]=$infos["id_societe"];
		$affaire["affaire"]=$infos["resume"];
		$affaire["date"]=$infos["date"];
		$affaire["id_termes"]=$infos["id_termes"];
		$affaire["forecast"]=20;
		$affaire["id_commercial"]=$infos["id_user"];
		if ($infos["type_devis"] == "consommable") $affaire["nature"]="consommable";

		if(!$infos["id_affaire"]){
			$infos["id_affaire"]=ATF::affaire()->insert($affaire,$s);
		}else{
			if ($infos["type_devis"] == "consommable"){
				ATF::affaire()->u(array(
					"id_affaire"=>$infos["id_affaire"],
					"id_termes"=>$infos["id_termes"],
					"etat"=>"devis",
					"nature"=>"consommable",
					"forecast"=>20)
				,$s);
			}else{
				ATF::affaire()->u(array(
					"id_affaire"=>$infos["id_affaire"],
					"id_termes"=>$infos["id_termes"],
					"etat"=>"devis",
					"forecast"=>20),
				$s);
			}

		}


		if($infos_ligne){
			$totalCom = 0;
			foreach($infos_ligne as $key=>$item){
				if(!$item["devis_ligne__dot__quantite"]){
					$item["devis_ligne__dot__quantite"]=0;
				}
				$totalCom += ($item["devis_ligne__dot__quantite"]* $item["devis_ligne__dot__prix"]);
			}
			$totalCom += $infos["frais_de_port"];
			$prix = str_replace(" ","", $infos["prix"]);
		}



		//Devis
		unset($infos["id_termes"],$infos["sous_total"],$infos["marge"],$infos["marge_absolue"]);
		$last_id=parent::insert($infos,$s,NULL,$var=NULL,NULL,true);

		//Devis Ligne
		if($infos_ligne){
			foreach($infos_ligne as $key=>$item){
				foreach($item as $k=>$i){
					$k_unescape=util::extJSUnescapeDot($k);
					$item[str_replace("devis_ligne.","",$k_unescape)]=$i;
					unset($item[$k]);
				}

				$item["id_fournisseur"]=$item["id_fournisseur_fk"];
				$item["id_compte_absystech"]=$item["id_compte_absystech_fk"];
				unset($item["id_fournisseur_fk"],$item["id_compte_absystech_fk"],$item["marge"],$item["marge_absolue"]);
				$item["id_devis"]=$last_id;
				$item["index"]=util::extJSEscapeDot($key);
				if(!$item["quantite"]){
					$item["quantite"]=0;
				}
				$item["visible"] = ($item["visible"] == "on")? 'oui':'non';
				ATF::devis_ligne()->insert($item,$s);
			}
		}

		if($consommables && $infos["type_devis"] == "consommable"){
			foreach($consommables as $key=>$item){
				foreach($item as $k=>$i){
					$k_unescape=util::extJSUnescapeDot($k);
					$item[str_replace("devis_ligne.","",$k_unescape)]=$i;
					unset($item[$k]);
				}

				$item["id_fournisseur"]=$item["id_fournisseur_fk"];
				$item["id_compte_absystech"]=$item["id_compte_absystech_fk"];
				unset($item["id_fournisseur_fk"],$item["id_compte_absystech_fk"],$item["marge"],$item["marge_absolue"]);
				$item["id_devis"]=$last_id;
				$item["index"]=util::extJSEscapeDot($key);
				$item["quantite"]=1;

				if(!isset($item["index_nb"])){
					ATF::db($this->db)->rollback_transaction();
					throw new errorATF(ATF::$usr->trans("index_nb_inexistant"));
				}
				if(!isset($item["index_couleur"])){
					ATF::db($this->db)->rollback_transaction();
					throw new errorATF(ATF::$usr->trans("index_couleur_inexistant"));
				}

				ATF::devis_ligne()->insert($item,$s);
			}
		}

		//*****************************************************************************

		if($preview){
			if($telescope){
				return base64_encode(ATF::pdf()->generic("devis",$last_id,true,$s,true));
			}
			$this->move_files($last_id,$s,true,$infos["filestoattach"]); // Génération du PDF de preview
			ATF::db($this->db)->rollback_transaction();
			return $this->cryptId($last_id);
		}else{
			if($telescope){
				return base64_encode(ATF::pdf()->generic("devis",$last_id,true,$s,false));
			}

			$this->move_files($last_id,$s,false,$infos["filestoattach"]); // Génération du PDF avec les lignes dans la base

			/* MAIL */
			//Seulement si le profil le permet
			if($infos["etat"]!="bloque"){
				$this->sendMailDevis($last_id);
			}else{
				if(ATF::$usr->get("id_superieur")){
					if($email=ATF::user()->select(ATF::$usr->get("id_superieur"),"email")){
						$recipient = $email;
					}else{
						ATF::$msg->addNotice(
						loc::mt("Veuillez prévenir votre supérieur pour débloquer le devis.")
						,ATF::$usr->trans("Droits_d_acces_requis_pour_cette_operation")
						);
					}
				}else{
					ATF::$msg->addNotice(
					loc::mt("Veuillez prévenir votre supérieur pour débloquer le devis.")
					,ATF::$usr->trans("Droits_d_acces_requis_pour_cette_operation")
					);
				}

				if($recipient){
					$devis = ATF::devis()->select($last_id);
					$ref_devis = $devis["ref"]."-".$devis["revision"];
					$from = ATF::user()->select(ATF::$usr->getID(),"email");

					$info_mail["objet"] = "Devis bloqué référence : ".$ref_devis;
					$info_mail["from"] = ATF::user()->nom(ATF::$usr->getID())." <".$from.">";
					$info_mail["html"] = false;
					$info_mail["template"] = 'devis';
					$info_mail["texte"] = "Un devis bloqué a été créé par ".ATF::user()->nom(ATF::$usr->getID());
					$info_mail["recipient"] = $recipient;
					//Ajout du fichier
					$path = $this->filepath($last_id,"fichier_joint");

					$this->devis_mail = new mail($info_mail);
					$this->devis_mail->addFile($path,$devis["ref"]."-".$devis["revision"].".pdf",true);
					$this->devis_mail->send();
				}
			}
			ATF::db($this->db)->commit_transaction();
		}

		if(is_array($cadre_refreshed)){
			ATF::affaire()->redirection("select",$infos["id_affaire"]);
		}

		api::sendUDP(array("data"=>array("type"=>"devis")));

		return $this->cryptId($last_id);


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
			ATF::commande()->q->reset()->addCondition("commande.id_affaire",$devis["id_affaire"])->end();
			$tab_commande = ATF::commande()->select_all();
			$this->q->reset()->addCondition("devis.id_affaire",$devis["id_affaire"])->end();
			$tab_devis = $this->select_all();
			ATF::facture()->q->reset()->addCondition("facture.id_affaire",$devis["id_affaire"])->end();
			$tab_facture = ATF::facture()->sa();

			if(!$tab_facture && !$tab_devis && !$tab_commande) {
				ATF::affaire()->delete($devis["id_affaire"],$s);
				unset($devis["id_affaire"]);
			}

			//Dans le cas d'une révision, le devis précédent passe en attente
			if($devis["revision"]!="A"){
				$revision=chr(ord($devis["revision"])-1);
				$this->q->reset()
				->addCondition("revision",$revision)
				->addCondition("devis.id_affaire",$devis["id_affaire"])
				->setDimension("row");

				$anc_devis=$this->sa();
				$this->u(array(
					"id_devis"=>$anc_devis["id_devis"],
					"etat"=>"attente",
					"date_modification"=>date("Y-m-d H:i:s")
				));
			}

			ATF::db($this->db)->commit_transaction();
			//*****************************************************************************

			if($devis["id_affaire"]){
				ATF::affaire()->redirection("select",$devis["id_affaire"]);
			}else{
				$this->redirection("select_all",NULL,"devis.html");
			}

			api::sendUDP(array("data"=>array("type"=>"devis")));

			return true;

		} elseif (is_array($infos) && $infos) {

			foreach($infos["id"] as $key=>$item){
				$this->delete($item,$s,$files,$cadre_refreshed);
			}

		}

	}

	/**
	 * Méthode permet de valider un devis "bloque" c'est à dire fait par un profil qui a dépassé le seuil fixé dans son profil, seul les associées (profil 1) peuvent valider ce devis
	 * @author mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	 * @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	 * @param array &$s La session
	 * @param array $files $_FILES
	 * @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	 * @param array $nolog True si on ne désire par voir de logs générés par la méthode
	 */
	public function unlock($infos,&$s,$files=NULL,&$cadre_refreshed){
		ATF::db($this->db)->begin_transaction();
		$this->u(array("id_devis"=>$infos["id_devis"],"etat"=>"attente","date_modification"=>date("Y-m-d H:i:s")),$s);
		$devis=$this->select($infos["id_devis"]);

		if(!ATF::user()->select($devis["id_user"],"email")){
			ATF::db($this->db)->rollback_transaction();
			throw new errorATF("Il n'y a pas d'email pour ce contact");
		}else{
			$recipient = ATF::user()->select($devis["id_user"],"email");
		}

		$ref_devis = $devis["ref"]."-".$devis["revision"];
		$from = ATF::user()->select(ATF::$usr->getID(),"email");

		$info_mail["objet"] = "Devis validé : '".$ref_devis."'";
		$info_mail["from"] = ATF::user()->nom(ATF::$usr->getID())." <".$from.">";
		$info_mail["html"] = false;
		$info_mail["template"] = 'devis_valider';
		$info_mail["texte"] = "Votre devis a été validé.";
		$info_mail["recipient"] = $recipient;

		// Regénération du fichier PDF
		$this->move_files($devis["id_devis"],$s,false); // Génération du PDF avec les lignes dans la base

		//Ajout du fichier
		$path = $this->filepath($devis["id_devis"],"fichier_joint");

		$this->devis_mail = new mail($info_mail);
		$this->devis_mail->addFile($path,$devis["ref"]."-".$devis["revision"].".pdf",true);
		$this->devis_mail->send();

		if ($devis['mail'] && $devis['mail_text']) {
			$this->sendMailDevis($infos["id_devis"]);
		}


		ATF::db($this->db)->commit_transaction();

		ATF::$msg->addNotice(
			loc::mt(ATF::$usr->trans("notice_devis_valider"),array("record"=>$this->nom($devis["id_devis"])))
			,ATF::$usr->trans("notice_success_title")
		);

		return true;
	}

	/**
	 * Méthode permettant de passer l'état d'une commande à annulee
	 * @author mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	 * @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	 * @param array &$s La session
	 * @param array $files $_FILES
	 * @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	 * @param array $nolog True si on ne désire par voir de logs générés par la méthode
	 */
	public function annule($infos,&$s,$files=NULL,&$cadre_refreshed=NULL){
		$devis=$this->select($infos["id_devis"]);

		if($devis["etat"]!="gagne"){
			ATF::db($this->db)->begin_transaction();
			//Dans le cas d'une révision, le devis précédent passe en attente
			if($devis["revision"]!="A"){
//				$revision=chr(ord($devis["revision"])-1);
//				$this->q->reset()
//				->addCondition("revision",$revision)
//				->addCondition("commande.id_affaire",$devis["id_affaire"])
//				->setDimension("row");
//
//				$anc_devis=$this->sa();
//				$this->u(array(
//					"id_devis"=>$anc_devis["id_devis"],
//					"etat"=>"attente",
//					"date_modification"=>date("Y-m-d H:i:s")
//				));

				$this->q->reset()
				->addCondition("devis.id_devis",$devis["id_devis"],false,false,'!=')
				->from("devis","id_affaire","commande","id_affaire")
				->addCondition("commande.id_affaire",$devis["id_affaire"]);

				$anc_devis=$this->sa();
				foreach($anc_devis as $item){
					$this->u(array(
									"id_devis"=>$item["id_devis"],
									"etat"=>NULL,
									"date_modification"=>date("Y-m-d H:i:s")
									)
					);
				}
			}

			$this->u(array(
							"id_devis"=>$infos["id_devis"],
							"etat"=>"annule",
							"date_modification"=>date("Y-m-d H:i:s")
							)
			);
			ATF::db($this->db)->commit_transaction();

			ATF::$msg->addNotice(
			loc::mt(ATF::$usr->trans("notice_devis_annule"),array("record"=>$this->nom($infos["id_devis"])))
			,ATF::$usr->trans("notice_success_title")
			);

			$this->redirection("select_all",NULL,"devis.html");
			return true;
		}else{
			return false;
		}
	}

	/**
	 * Retourne la valeur par défaut spécifique aux données passées en paramètres
	 * @author Yann GAUTHERON <ygautheron@absystech.fr>
	 * @param string $field
	 * @param array &$s La session
	 * @param array &$request Paramètres disponibles (clés étrangères)
	 * @return string
	 */
	public function default_value($field,$quickMail=false){


		if($field!="id_user_technique" && $field!="id_user_admin" && $field!="emailCopie"){
			if(ATF::_r('id')){
				$devis=ATF::devis()->select(ATF::_r('id'));
			}elseif(ATF::_r('id_devis')){
				$devis=ATF::devis()->select(ATF::_r('id_devis'));
			}
		}

		switch ($field) {
			case "id_termes":
				if($devis){
					$id_termes=ATF::affaire()->select($devis["id_affaire"],"id_termes");
				}elseif(ATF::_r('id_societe')){
					$id_termes=ATF::societe()->select(ATF::_r('id_societe'),"id_termes");
				}else{
					$id_termes="";
				}
				return $id_termes;
			case "email":
				if($devis){
					$email=ATF::contact()->select($devis["id_contact"],"email");
				}else{
					$email="";
				}
				return $email;
			case "emailCopie":
				return ATF::$usr->get("email");
			case "sous_total":
				if($devis){
					$sous_total=$devis["prix"]-$devis["frais_de_port"];
				}else{
					$sous_total=0;
				}
				return $sous_total;
			case "marge":
				if($devis){
					$marge=round((($devis["prix"]-$devis["prix_achat"])/$devis["prix"])*100,2)."%";
				}else{
					$marge=0;
				}
				return $marge;
			case "marge_absolue":
				if($devis){
					$marge_absolue=($devis["prix"]-$devis["frais_de_port"])-$devis["prix_achat"];
				}else{
					$marge_absolue=0;
				}
				return $marge_absolue;
			case "objet":
				return "Devis ref : ".$devis["ref"];
			case "texte":
				return nl2br("Bonjour,\n\nCi-joint le devis ".$devis["resume"].".\nDevis effectué le ".$devis["date"].".\n");
			case "fichier_joint":
				/*C'est à dire que je ne veux pas proposer de fichier sur l'update/insert*/
				return !$quickMail;
			default:
				return parent::default_value($field);
		}
	}

	//	/**
	//	* Donne le mail actuel
	//	* @return mixed
	//	*/
	//	public function getCurrentMail(){
	//		//Current mail
	//		if(!$this->current_mail) throw new errorATF(ATF::$usr->trans("null_current_mail",$this->table));
	//		return $this->current_mail;
	//	}
	//
	//	/**
	//	* Initialise le mail courant
	//	* @param string $mail le nom du mail courant
	//	*/
	//	public function setCurrentMail($mail){
	//		$this->current_mail=&$this->$mail;
	//	}

	/**
	 * Select classique qui ne prend pas en compte certaines données lors du cloner
	 * @author Quentin JANON <qjanon@absystech.fr>
	 * @param int id
	 * @param string field
	 * @return array
	 */
	public function select($id,$field=NULL) {
		$devis=parent::select($id,$field);
		if(ATF::_r("event")=="cloner"){
			$devis["date"]="";
			$devis["id_opportunite"]="";
			$devis["validite"]="";
		}
		return $devis;
	}

	/**
	 * Renvoi l'id d'un devis gagné d'une affaire
	 * @author Quentin JANON <qjanon@absystech.fr>
	 * @param int id_affaire
	 * @return mediumint
	 */
	public function getIdFromAffaire($id_affaire) {
		$id_affaire = ATF::affaire()->decryptId($id_affaire);
		$this->q->reset()
				->addField('id_devis')
				->where('id_affaire',$id_affaire)
				->where('etat','gagne');

		$id = $this->select_cell();

		return $this->cryptId($id);
	}

	/**
	 * Pour les autocomplete, retourne une conditions au format URL   arg1=2&arg2=3...
	 * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	 * @param string $class Classe des enregistrements affichés dans l'autocomplète
	 * @param array $infos ($requests habituellement attendu)
	 *	int $infos[id_affaire]
	 *	int $infos[id_societe]
	 * @param string $condition_field
	 * @param string $condition_value
	 * @return array Conditions de filtrage
	 */
	public function autocompleteConditions(classes_optima $class,$infos,$condition_field=NULL,$condition_value=NULL) {
		$this->infoCollapse($infos);
		switch ($class->table) {
			case "contact":
			case "opportunite":
				if ($infos["id_societe"]) {
					$conditions["condition_field"][] = $class->table.".id_societe";
					$conditions["condition_value"][] = $infos["id_societe"];
				}
				break;
		}
		return array_merge_recursive((array)($conditions),parent::autocompleteConditions($class,$infos,$condition_field,$condition_value));
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

			$this->u(array("id_devis"=>$devis["id_devis"],"etat"=>"perdu","date_modification"=>date("Y-m-d H:i:s")),$s);
			ATF::affaire()->u(array("id_affaire"=>$devis["id_affaire"],"etat"=>"perdue","forecast"=>"0"),$s);

			ATF::db($this->db)->commit_transaction();
			////*****************************************************************************

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
					$result = $this->{$infos['action']}($params);
					if ($infos["raison"]) {
						// Enregistre la raison
						$params["cause_perdu"] = $infos["raison"];
						$this->u($params);
					}
					break;

				case "replace":
					$params = array(
						'id_devis'=>$this->decryptId($infos['id'])
						,'etat'=>'remplace'
						,'id_remplacant'=>$this->decryptId($infos['id_devis'])
						,'cause_perdu'=>$infos['raison']
					);
					$result = $this->u($params);
					break;
				default:
					ATF::db($this->db)->rollback_transaction();
					return false;
			}
		ATF::affaire()->u(array("id_affaire"=>$this->select($params['id_devis'],"id_affaire"),"etat"=>"perdue","forecast"=>"0"),$s);
		ATF::db($this->db)->commit_transaction();
		return $result;
	}

	/**
	 * Méthode qui gère l'envoi immédiat du mail du devis
	 * @author Quentin JANON <qjanon@absystech.fr>
	 * @param array $id_devis
	 * @param array $devis
	 */
	public function getRefMail($id_devis,$devis=false){
		if (!$devis) {
			$devis = $this->select($id_devis);
		}
		return $devis["ref"]."-".$devis["revision"];
	}


	/**
	 * Méthode qui gère l'envoi immédiat du mail du devis
	 * @author Quentin JANON <qjanon@absystech.fr>
	 * @param array $devis
	 */
	public function sendMailDevis($id_devis){
		$id_devis = $this->decryptId($id_devis);
		$devis = ATF::devis()->select($id_devis);

		if (!$devis['mail_text']) return false;

		$from = ATF::user()->select($devis['id_user'],"email");
		$recipient = $devis['mail']?$devis['mail']:ATF::contact()->select($devis["id_contact"],"email");

		if (!$recipient) {
			if (ATF::db($this->db)->isTransaction()) ATF::db($this->db)->rollback_transaction();
			throw new errorATF("Il n'y a pas d'email pour ce contact",1054);
		}

		$mail = array(
			"objet"=>"Votre devis référence : ".$this->getRefMail($id_devis,$devis)
			,"from"=>ATF::user()->nom($devis['id_user'])." <".$from.">"
			,"html"=>false
			,"template"=>'devis'
			,"texte"=>$devis["mail_text"]
			,"recipient"=>$recipient
		);
		// Création du mail
		$this->devis_mail = new mail($mail);
		//Ajout du fichier joint
		$path = $this->filepath($id_devis,"fichier_joint");
		$this->devis_mail->addFile($path,$this->getRefMail($id_devis,$devis).".pdf",true);

		// Ouverture du ZIP contenant les annexes pour les mettre toutes en pièce jointe
		$pathAnnexe = $this->filepath($id_devis,"documentAnnexes");
		if (file_exists($pathAnnexe)) {
			$zip = new ZipArchive();
			$res = $zip->open($pathAnnexe);
			if ($res !== TRUE) {
				if (ATF::db($this->db)->isTransaction()) ATF::db($this->db)->rollback_transaction();
				throw new errorATF("Ouverture du ZIP (".$pathAnnexe.") Impossible, res = ".$res,501);
			}

			$dossierTempToExtract = "/tmp/".ATF::$codename."_".$this->table."_tempZip".$id_devis."/";
			util::mkdir($dossierTempToExtract);
			$zip->extractTo($dossierTempToExtract);
			foreach (scandir($dossierTempToExtract) as $fileTmp) {
				if ($fileTmp=="." || $fileTmp=="..") continue;
				$this->devis_mail->addFile($dossierTempToExtract.$fileTmp,$fileTmp,true);
			}
		}

		if ($this->devis_mail->send()) {
			ATF::$msg->addNotice(ATF::$usr->trans("a_cette_adresse")." : ".$recipient,ATF::$usr->trans("notice_mail_devis_envoye"));
		}

		if($devis["mail_copy"]){
			$mail["recipient"] = $devis["mail_copy"];
			$this->devis_copy_mail = new mail($mail);
			$this->devis_copy_mail->addFile($path,$devis["ref"]."-".$devis["revision"].".pdf",true);
			// Ouverture du ZIP contenant les annexes
			foreach (scandir($dossierTempToExtract) as $fileTmp) {
				if ($fileTmp=="." || $fileTmp=="..") continue;
				$this->devis_copy_mail->addFile($dossierTempToExtract.$fileTmp,$fileTmp,true);
			}
			if ($this->devis_copy_mail->send()) {
				ATF::$msg->addNotice(ATF::$usr->trans("a_cette_adresse")." : ".$devis["mail_copy"],ATF::$usr->trans("notice_mail_copy_devis_envoye"));
			}
		}
		return true;
	}

	/**
	 * Renvoi les devis
	 * Pour un appel en AJAX
	 * @author Quentin JANON <qjanon@absystech.fr>
	 */
	public function getAllForMenu(&$infos,$s,$f) {
		$this->q->reset();
		if ($infos["id_societe"]) {
			$this->q->where("id_societe",$infos['id_societe']);
		}
		$this->q->addCondition("devis.etat","perdu","AND",false,"<>");
		$this->q->addCondition("devis.etat","annule","AND",false,"<>");
		$this->q->addOrder("date","desc");
		$return = parent::sa();
		foreach ($return as $k=>$i) {
			$r[] = array(
				"text"=>$i['ref']." - ".$i['resume'],
				"id"=>ATF::affaire()->cryptId($i['id_affaire'])
			);
		}
		$infos['display'] = true;
		return json_encode($r);
	}



	/** Fonction qui génère les résultat pour les champs d'auto complétion affaire
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function _ac($get,$post) {

		$this->q->reset();

		// On ajoute les champs utiles pour l'autocomplete
		$this->q->addField("devis.id_devis","id_devis")
				->addField("devis.resume","devis")
				->addField("devis.ref","ref");

		if ($get['q']) {
			$this->q->setSearch($get["q"]);
		}

		if ($get['id_societe']) {
			$this->q->where("devis.id_societe",$get["id_societe"]);
		}

		return $this->select_all();
	}


	/**
	 * Fonction telescope qui permet d'annuler, mettre en perdu ou remplace un devis
	 * @package Telescope
	 * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
  	 * @param $get array contient le tri, page limit et potentiellement un id.
  	 * @param $post array Argument obligatoire mais inutilisé ici.
	 * @return id_devis si tout c'est bien passé false sinon
	 */
	public function _annulation ($get, $post) {
		$return = $this->annulation($post);
		if($return){
			$return = array("id"=>$post["id"], "etat"=>$post['action']);
		}
		return $return;
	}

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
    if (!$get['tri'] || $get['tri'] == 'action') $get['tri'] = "devis.id_devis";
    if (!$get['trid']) $get['trid'] = "asc";

    // Gestion du limit
    if (!$get['limit']) $get['limit'] = 30;

    // Gestion de la page
    if (!$get['page']) $get['page'] = 0;

    $colsData = array("devis.id_devis","devis.ref","resume","affaire","societe.id_societe","revision","devis.id_affaire","devis.etat");

    $this->q->reset();

    if ($get['id_devis']) $colsData = array("devis.*");

    $this->q->addField($colsData);

    if($get["search"]){
      header("ts-search-term: ".$get['search']);
      $this->q->setSearch($get['search']);
    }

    if ($get['id_devis']) {

		$this->q->where("devis.id_devis",$get['id_devis'])->setCount(false)->setDimension('row');
		$data = $this->sa();

		foreach ($data as $key => $value) {
			if($key == "id_societe") $data["societe"] = ATF::societe()->select($value);
			if($key == "id_contact") $data["contact"] = ATF::contact()->select($value);
			if($key == "id_user") $data["user"] = ATF::user()->select($value);
			if($key == "id_user_technique") $data["user_technique"] = ATF::user()->select($value);
			if($key == "id_user_admin") $data["user_admin"] = ATF::user()->select($value);
			if($key == "id_opportunite") $data["id_opportunite_fk"] = ATF::opportunite()->select($value, "opportunite");

			if($key == "id_termes") $data["id_termes_fk"] = ATF::termes()->select($value, "termes");
			if($key == "id_politesse_post") $data["id_politesse_post_fk"] = ATF::politesse()->select($value, "politesse");
			if($key == "id_politesse_pref") $data["id_politesse_pref_fk"] = ATF::politesse()->select($value, "politesse");
			if($key == "id_delai_de_realisation") $data["id_delai_de_realisation_fk"] = ATF::delai_de_realisation()->select($value, "delai_de_realisation");

			$data["fichier_joint"] = $data["documentAnnexes"] = false;

			if (file_exists($this->filepath($get['id_devis'],"fichier_joint"))) $data["fichier_joint"] = true;
			if (file_exists($this->filepath($get['id_devis'],"documentAnnexes"))) $data["documentAnnexes"] = true;

			$data["id_societe_fk"] = $data["societe"]["societe"];
			$data["id_contact_fk"] = $data["contact"]["nom"]." ".$data["contact"]["prenom"];
			$data["id_user_fk"] = $data["user"]["nom"]." ".$data["user"]["prenom"];
			$data["id_user_technique_fk"] = $data["user_technique"]["nom"]." ".$data["user_technique"]["prenom"];
			$data["id_user_admin_fk"] = $data["user_admin"]["nom"]." ".$data["user_admin"]["prenom"];

		}



		$this->q->reset()->where("devis.id_affaire", $data["id_affaire"]);
		$data["devisAffaire"] = $this->sa();

		$data["idcrypted"] = $this->cryptId($get["id_devis"]);

    } else {
      $this->q->setLimit($get['limit'])->setCount();
      $data = $this->select_all($get['tri'],$get['trid'],$get['page'],true);
    }



    if($get['id_devis']){
    	// GET d'un élément, on ajoute ses lignes récurrentes et ponctuelles
    	$data['ligne'] = ATF::devis_ligne()->select_special('id_devis', $get['id_devis']);

		foreach($data["ligne"] as $k => $v){
			$data["ligne"][$k]["id_fournisseur_fk"] = $data["ligne"][$k]["id_compte_absystech_fk"] = " - ";
			if($v["id_fournisseur"])$data["ligne"][$k]["id_fournisseur_fk"] = ATF::societe()->select($v["id_fournisseur"], "societe");
			if($v["id_compte_absystech"])$data["ligne"][$k]["id_compte_absystech_fk"] = ATF::compte_absystech()->select($v["id_compte_absystech"], "compte_absystech");
		}

    	$return = $data;
    }else{
      header("ts-total-row: ".$data['count']);
      header("ts-max-page: ".ceil($data['count']/$get['limit']));
      header("ts-active-page: ".$get['page']);
      $return = $data['data'];
    }
    return $return;
  }

  /**
  *
  * Fonctions _POST pour telescope
  * @package Telescope
  * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
  * @param $get inutile ici.
  * @param $post array Argument obligatoire contient les data à inserer/mettre à jour.
  * @return array un tableau avec les données
  */
  public function _POST($get,$post) {

  	$infos = array();

  	$post["financement_cleodis"] = ($post["financement_cleodis"] == "on")? 'oui':'non';
  	$infos["values_devis"]["produits"] = json_encode($post["values_devis"]["produits"]);
  	$infos["values_devis"]["consommables"] = json_encode($post["values_devis"]["consommables"]);
  	if($post["preview"]){
  		$infos["preview"] = true;
  		unset($post["preview"]);
  	}

  	unset($post["values_devis"]);
  	$infos["devis"] = $post;


  	$return= $this->insert($infos);

  	if (is_numeric($return) || is_string($return)) {
		$return = $this->decryptId($return);
	}

  	$res['notices'] = ATF::$msg->getNotices();
	$res['result'] = $return;

	return $res;

  }

  public function _PUT($get,$post) {
  	$input = file_get_contents('php://input');
  	if (!empty($input)) parse_str($input,$post);

  	$infos = array();

  	$post["financement_cleodis"] = ($post["financement_cleodis"] == "on")? 'oui':'non';
  	$infos["values_devis"]["produits"] = json_encode($post["values_devis"]["produits"]);
  	$infos["values_devis"]["consommables"] = json_encode($post["values_devis"]["consommables"]);
  	if($post["preview"]){
  		$infos["preview"] = true;
  		$infos["telescope"] = true;
  		unset($post["preview"]);
  	}

  	unset($post["values_devis"]);
  	$infos["devis"] = $post;


	$return= $this->update($infos);

	if (is_numeric($return) || is_string($return)) {
		$return = $this->decryptId($return);
	}

  	$res['notices'] = ATF::$msg->getNotices();
	$res['result'] = $return;
	return $res;
  }



  public function _getPDF($get,$post){
  	$data = file_get_contents($this->filepath($post["id_devis"], $post["file"]));
  	return base64_encode($data);
  }

  public function _getHotline_devis($get,$post){
  	$limit = 10;

	ATF::hotline()->q->reset()->where("hotline.id_affaire", $this->select($get["id_devis"], "id_affaire"))
							  ->setLimit(100)->setCount()
							  ->addOrder("hotline.id_hotline","desc");
	$hotlines = ATF::hotline()->select_all();

	$i = 0;

	foreach ($hotlines["data"] as $kh => $vh) {
		$temps_passe = ATF::hotline()->getTotalTime($vh['id_hotline'],"prestaTicket");

		if($i < $get["page"]*$limit && $i > ($get["page"]-1)*$limit){
			$data["hotlines"][$vh["id_hotline"]]["tps"] = $temps_passe;
			$data["hotlines"][$vh["id_hotline"]]["resume"] = $vh["hotline"];
		}

		$t = explode("H", $temps_passe);

		$total += ($t[0]*60)+$t[1];

		$i++;
	}
	$data["total_hotline"] = intval($total/60)."H".intval((($total/60)-intval($total/60))*60);

	$data["pages"] = ceil($hotlines['count']/$limit);
	$data["page"] = $get["page"];

	return $data;


  }

};

class devis_att extends devis_absystech { };
class devis_wapp6 extends devis_absystech { };
class devis_demo extends devis_absystech { };
?>
