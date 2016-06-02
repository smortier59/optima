<?
/**
* @package Optima
*/
class gep_projet extends classes_optima {
	function __construct() { // PHP5
		parent::__construct();
		$this->table = __CLASS__;
		
		$this->colonnes['fields_column'] = array(
			 'gep_projet.gep_projet'=>array("width"=>150)
			,'gep_projet.id_owner'=>array("width"=>150)
			,'gep_projet.id_societe'=>array("width"=>150)
			,'gep_projet.id_affaire'=>array("width"=>180)
			,'gep_projet.id_contact_facturation'=>array("width"=>180)
			,'gep_projet.description'
			,'gep_projet.date_debut'=>array("width"=>100,"align"=>"center")
			,'gep_projet.date_fin'=>array("width"=>100,"align"=>"center")
		);

		$this->colonnes['primary'] = array(
			 "id_societe"
			,"id_affaire"
			,"id_contact_facturation"
			,"gep_projet"
			,"nature"
			,"description"
			,"date_debut"
			,"date_fin"
		);

		$this->colonnes["speed_insert"] = array(
			'gep_projet'	
			,'id_societe'
		);
		 
		$this->fieldstructure();
		
		$this->onglets = array('ged','hotline');
		//pour les stats
		$this->pas="mois";
		$this->addPrivilege("modifStatProjet");
		
		$this->files["documents"] = array("multiUpload"=>true);

		$this->foreign_key["id_contact_facturation"] = "contact";
	}
		
	/** Change les secondes en heures et minutes (pas de méthode php pour récupérer des heures supérieures à 24)
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @return XXhXX
	*/
	function sec_to_time($temps){
		$heure=floor($temps/3600);
		$temps=$temps%3600;
		$minute=floor($temps/60);
		//pour une question d'esthetique (afficher 00 au lieu de 0)
		if(strlen($minute)==1){
			$minute="0".$minute;
		}
		$time=$heure."h".$minute;
		return $time;
	}
	
	/** Retourne la fiche de détail du projet (tableau représentant la liste des utilisateurs par leurs temps de travail sur le projet)
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr> 
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* date 15/01/2008
	* modified 3/12/2009
	* @param id_gep_projet int l'id du projet 
	*/
	function detail_projet($id_gep_projet){
		ATF::pointage()->q->reset()->addField("pointage.id_user")
									->addField("SUM(TIME_TO_SEC(pointage.temps))","temps_total")
									->addField("SUM(TIME_TO_SEC( p2.temps ))","temps_hot")
									->addCondition("p2.id_hotline",NULL,"OR",false,"IS NOT NULL")
									->addJointure("pointage","id_pointage","pointage","id_pointage","p2",NULL,NULL,"p2.id_hotline")
									->addCondition("pointage.id_gep_projet",$this->decryptId($id_gep_projet))
									->addGroup("pointage.id_user")
									->addOrder("pointage.id_user")
									->setStrict();
		$retour=ATF::pointage()->sa();	
		
		foreach($retour as $key=>$item){
			$retour[$key]['temps_hot']=$this->sec_to_time($item['temps_hot']);
			if($retour[$key]['temps_hot']!="0h00"){
				$retour[$key]['temps_total']=$this->sec_to_time($item['temps_total'])."<br />(".$retour[$key]['temps_hot'].")";
			}else{
				$retour[$key]['temps_total']=$this->sec_to_time($item['temps_total']);
			}
		}		
		
		return $retour;
				
	}
	
	/** Retourne le temps total travaillé pour le projet donné en argument
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr> 
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* date 15/01/2008
	* modified 3/12/2009
	* @param id_gep_projet int l'id du projet 
	*/
	function tps_total_projet($id_gep_projet){
		ATF::pointage()->q->reset()->addField("SUM(TIME_TO_SEC(pointage.temps))","temps_total")
									->addField("SUM(TIME_TO_SEC( p2.temps ))","temps_hot")
									->addCondition("p2.id_hotline",NULL,"OR",false,"IS NOT NULL")
									->addJointure("pointage","id_pointage","pointage","id_pointage","p2",NULL,NULL,"p2.id_hotline")
									->addCondition("pointage.id_gep_projet",$this->decryptId($id_gep_projet))
									->setDimension("row_arro")
									->setStrict();
		$tab=ATF::pointage()->sa();	
		
		$tab['temps_total']=$this->sec_to_time($tab['temps_total']);
		$tab['temps_hot']=$this->sec_to_time($tab['temps_hot']);
		return $tab;
	}
	
	/** Retourne la fiche de détail de projet par mois (temps utilisateur par mois)
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr> 
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* date 15/01/2008
	* modified 3/12/2009
	* @param id_gep_projet int l'id du projet 
	*/
	function detail_projet_par_mois($id_gep_projet){
		ATF::pointage()->q->reset()->addField("pointage.id_user")
									->addField("SUM(TIME_TO_SEC(pointage.temps))","temps_total")
									->addField("SUM(TIME_TO_SEC( p2.temps ))","temps_hot")
									->addField("DATE_FORMAT( pointage.date , '%Y-%m' )","date_total")
									->addCondition("p2.id_hotline",NULL,"OR",false,"IS NOT NULL")
									->addJointure("pointage","id_pointage","pointage","id_pointage","p2",NULL,NULL,"p2.id_hotline")
									->addCondition("pointage.id_gep_projet",$this->decryptId($id_gep_projet))
									->addGroup("date_total,pointage.id_user")
									->addOrder("date_total")
									->addOrder("pointage.id_user")
									->setStrict();
		$tab=ATF::pointage()->sa();

		foreach($tab as $key=>$item){
			$tab[$key]['temps_total']=$this->sec_to_time($item['temps_total']);
			$tab[$key]['temps_hot']=$this->sec_to_time($item['temps_hot']);
			if($tab[$key]['temps_hot']!="0h00"){
				$temp[$item['date_total']][$item['pointage.id_user']]=$tab[$key]['temps_total']."<br />(".$tab[$key]['temps_hot'].")";
			}else{
				$temp[$item['date_total']][$item['pointage.id_user']]=$tab[$key]['temps_total'];
			}
		}
		
		//Recherche de la liste des utilisateurs sur le projet
		$liste_utilisateur=$this->liste_user($this->decryptId($id_gep_projet));
		
		//Ajout de tout les utilisateurs pour chaque mois
		foreach($temp as $cle => $valeur){
			foreach($liste_utilisateur as $valeur2){
				if(!isset($temp[$cle][$valeur2])){
					$temp[$cle][$valeur2]=0;
				}
			}
			ksort($temp[$cle]);
		}

		return $temp;
	}
	
	/** Recherche de la liste des utilisateurs ayant travaillé sur un projet
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr> 
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* date 15/01/2008
	* modified 3/12/2009
	* @param id_gep_projet int l'id du projet 
	*/
	function liste_user($id_gep_projet){
		if(strlen($id_gep_projet)==32)$id_gep_projet=$this->decryptId($id_gep_projet);
		ATF::pointage()->q->reset()->addField("id_user")
									->addCondition("id_gep_projet",$id_gep_projet)
									->addGroup("id_user")
									->addOrder("id_user")
									->setStrict();
		$tab=ATF::pointage()->sa();
		
		//Création du tableau final
		$temp='';
		$indice=0;

		foreach($tab as $valeur){
			$temp[$indice]=$valeur["id_user"];
			$indice++;
		}
		
		return $temp;
	}
	
	/** Retourne le temps passé sur un projet pour chaque mois
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr> 
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* date 15/01/2008
	* modified 3/12/2009
	* @param id_gep_projet int l'id du projet 
	*/
	function tps_projet_mois($id_gep_projet) {
		ATF::pointage()->q->reset()->addField("SUM(TIME_TO_SEC(pointage.temps))","temps_total")
									->addField("SUM(TIME_TO_SEC( p2.temps ))","temps_hot")
									->addField("DATE_FORMAT( pointage.date , '%Y-%m' )","date_total")
									->addCondition("p2.id_hotline",NULL,"OR",false,"IS NOT NULL")
									->addJointure("pointage","id_pointage","pointage","id_pointage","p2",NULL,NULL,"p2.id_hotline")
									->addCondition("pointage.id_gep_projet",$this->decryptId($id_gep_projet))
									->addGroup("date_total")
									->setStrict();
		$tab=ATF::pointage()->sa();
		
		//Construction du tableau final
		//Exemple
		//[2006-12] => 4.0
    	//[2007-01] => 102.0
		foreach($tab as $valeur){
			$temps_hot=$this->sec_to_time($valeur['temps_hot']);
			if($temps_hot!="0h00"){
				$temp[$valeur['date_total']]=$this->sec_to_time($valeur['temps_total'])."<br />(".$temps_hot.")";
			}else{
				$temp[$valeur['date_total']]=$this->sec_to_time($valeur['temps_total']);
			}
			
		}
		
		return $temp;
	}
	
	/** Si aucun projet sélectionné, on prends les 5 plus conséquents
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function setProjet(){
		//si aucun projet n'est sélectionné, on prends les 5 projets qui ont pris le plus de temps dans le laps de temps indiqué
		ATF::hotline_interaction()->q->reset()
										->addField('hotline.id_gep_projet','id_projet')
										->addField("SUM(TIME_TO_SEC(hotline_interaction.temps_passe))","tot")
										->setStrict()
										->addJointure("hotline_interaction","id_hotline","hotline","id_hotline")
										->addConditionNotNull('hotline.id_gep_projet')
										->addOrder("tot","desc")
										->addGroup("hotline.id_gep_projet")
										->setLimit(5);
										
		if($this->pas=="semaine"){
			ATF::hotline_interaction()->q->addCondition("DATE_ADD(hotline_interaction.date, INTERVAL 12 WEEK)","'".date('Y-m-d')."'",NULL,false,">=",false,false,true);
		}else{
			ATF::hotline_interaction()->q->addCondition("DATE_ADD(hotline_interaction.date, INTERVAL 12 MONTH)","'".date('Y-m-d')."'",NULL,false,">=",false,false,true);
		}
		$temps_graphe=ATF::hotline_interaction()->sa();
		
		foreach($temps_graphe as $cle=>$don){
			ATF::hotline_interaction()->q->reset("where")->addCondition("hotline.id_gep_projet",$don['id_projet']);
			$temps_total=ATF::hotline_interaction()->sa();
		
			$return[$cle]=array("id_projet"=>$don['id_projet'],"tot"=>$this->sec_to_day($don['tot'])." / ".$this->sec_to_day($temps_total[0]['tot']));
		}
			
		return $return;
	}
	
	/* Paramètres les titres de categories en fonction du numéro de semaine
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $week Numéro de semaine
	* @param &$graph Paramétrage du graphe
	*/
	public function fetchLabelsByWeek($week,&$graph){
		if(($week-12)<1){
			for($s=53-(abs($week-12));$s<=52;$s++){
				$graph['categories']["category"][$s]=array("label"=>$s);
			}
		}
		for($s=$week-11;$s<=$week;$s++){
			if($s>=1){
				$graph['categories']["category"][$s]=array("label"=>$s);
			}
		}
	}
	
	/* Paramètres les titres de categories en fonction du numéro de mois
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $time Timestamp
	* @param &$graph Paramétrage du graphe
	*/
	public function fetchLabelsByMonth($time,&$graph){
		$mois=util::month();
		for($m=date('m',$time)+1;$m<=12;$m++){
			$graph['categories']["category"][$m]=array("label"=>substr($mois[(strlen($m)<2?"0".$m:$m)],0,4)." (".substr(date("Y",$time)-1,-2).")");
		}
		for($m=1;$m<=date('m',$time);$m++){
			$graph['categories']["category"][$m]=array("label"=>substr($mois[(strlen($m)<2?"0".$m:$m)],0,4)." (".substr(date("Y",$time),-2).")");
		}
	}
	
	/* Paramètres les valeurs du graphe en fonction du numéro de semaine
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $week Numéro de semaine
	* @param &$graph Paramétrage du graphe
	* @param $val_ Valeurs possibles
	*/
	public function fetchValuesByWeek($week,&$graph,$val_){
		if(($week-12)<1){
			for($s=53-(abs($week-12));$s<=52;$s++){
				$graph['dataset'][$val_["gep_projet"]]['set'][(strlen($s)<2?"0".$s:$s)] = array("value"=>0,"alpha"=>100,"titre"=>$val_["gep_projet"]." : 0");
			}
		}
		for($s=$week-11;$s<=$week;$s++){
			if($s>=1){
				$graph['dataset'][$val_["gep_projet"]]['set'][(strlen($s)<2?"0".$s:$s)] = array("value"=>0,"alpha"=>100,"titre"=>$val_["gep_projet"]." : 0");
			}
		}
	}
	
	/* Paramètres les valeurs du graphe en fonction du numéro de mois
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $month Numéro de mois
	* @param &$graph Paramétrage du graphe
	* @param $val_ Valeurs possibles
	*/
	public function fetchValuesByMonth($month,&$graph,$val_){
		for($m=$month+1;$m<=12;$m++){
			$graph['dataset'][$val_["gep_projet"]]['set'][$m] = array("value"=>0,"alpha"=>100,"titre"=>$val_["gep_projet"]." : 0");
		}
		for($m=1;$m<=$month;$m++){
			$graph['dataset'][$val_["gep_projet"]]['set'][$m] = array("value"=>0,"alpha"=>100,"titre"=>$val_["gep_projet"]." : 0");
		}
	}
	
	/* Méthode de génération de graphe de stat
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array $liste_proj : liste des projets dans le cas où l'on n'a sélectionné aucun projet
	*/
	public function stats($liste_proj=NULL){
		ATF::hotline_interaction()->q->reset()
									->addField('gep_projet')	
									->addField("SUM(TIME_TO_SEC(hotline_interaction.temps_passe))",'tps')
									->setStrict()
									->addJointure("hotline_interaction","id_hotline","hotline","id_hotline")
									->addJointure("hotline","id_gep_projet","gep_projet","id_gep_projet")
									->addCondition("hotline_interaction.id_user",NULL,NULL,false,"IS NOT NULL")
									->addGroup("gep_projet");

		if($this->pas=="semaine"){
			ATF::hotline_interaction()->q->addField("DATE_FORMAT(hotline_interaction.date,'%u')",'semaine')
											->addField("DATE_FORMAT(MIN(hotline_interaction.date),'%d/%m/%Y')",'date_debut')
											->addField("DATE_FORMAT(MAX(hotline_interaction.date),'%d/%m/%Y')",'date_fin')
											->addCondition("DATE_ADD(hotline_interaction.date, INTERVAL 12 WEEK)","'".date('Y-m-d')."'",NULL,false,">=",false,false,true)
											->addGroup("DATE_FORMAT(hotline_interaction.date,'%x %u')");
		}else{
			ATF::hotline_interaction()->q->addField("MONTH(hotline_interaction.date)","mois")
											->addField("YEAR(hotline_interaction.date)","annee")
											->addCondition("DATE_ADD(hotline_interaction.date, INTERVAL 12 MONTH)","'".date('Y-m-d')."'",NULL,false,">=",false,false,true)
											->addGroup("mois");
		}							
		
		foreach($this->projet as $id_projet=>$value){
			ATF::hotline_interaction()->q->addCondition("hotline.id_gep_projet",$id_projet,"OR","hotline.id_gep_projet");
		}
		foreach($liste_proj as $cle=>$don){
			ATF::hotline_interaction()->q->addCondition("hotline.id_gep_projet",$don['id_projet'],"OR","hotline.id_gep_projet");
		}
		$result=ATF::hotline_interaction()->sa();
		
		if($this->pas=="semaine"){
			//pour avoir une donnée par semaine obligatoire
			//si il y a moins de 12 semaines d'écart, on va chercher celle de l'année d'avant
			$this->fetchLabelsByWeek(date('W'),$graph);
		}else{
			//pour avoir une donnée par mois obligatoire
			$this->fetchLabelsByMonth(time(),$graph);
		}
		$graph['params']['caption'] = "Charge par projet";
		$graph['params']['yaxisname'] = "Temps passe (pas ".$this->pas.")";

		/*parametres graphe*/		
		$this->paramGraphe($dataset_params,$graph);

		foreach ($result as $val_) {
		
			if (!$graph['dataset'][$val_["gep_projet"]]) {
				$graph['dataset'][$val_["gep_projet"]]["params"] = array_merge($dataset_params,array(
					"seriesname"=>$val_["gep_projet"]
					,"color"=>dechex(rand(0,16777216))
				));
				if($this->pas=="semaine"){
					$this->fetchValuesByWeek(date('W'),$graph,$val_);
				}else{
					$this->fetchValuesByMonth(date('m'),$graph,$val_);
				}
			}
			
			if($this->pas=="semaine"){
				$graph['dataset'][$val_["gep_projet"]]['set'][$val_['semaine']] = array("value"=>$this->sec_to_day($val_['tps']),"alpha"=>100,"titre"=>$val_["gep_projet"]." (".$val_['date_debut']." au ".$val_['date_fin'].") : ".$this->sec_to_day($val_['tps'])." jour(s)");
			}else{
				$graph['dataset'][$val_["gep_projet"]]['set'][$val_['mois']] = array("value"=>$this->sec_to_day($val_['tps']),"alpha"=>100,"titre"=>$val_["gep_projet"]." : ".$this->sec_to_day($val_['tps'])." jour(s)");
			}
		}
		
		return $graph;
	}
	
	/* Modifie la liste des projets à afficher sur le graphe
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array $infos : informations de la requête ajax
	*/
	public function modifStatProjet($infos){
		if($infos['projet']){
			if($infos['ajout']){
				$this->projet[$this->decryptId($infos['projet'])]=1;
				//mise a jour des temps
				$this->majTpsProjet($pas);
			}else{
				unset($this->projet[$infos['projet']]);
			}
		}
		if($infos['pas'])$this->pas=$infos['pas'];
		$infos['current_class']=ATF::stats();
		ATF::$cr->add('main','stats_menu.tpl.htm',$infos);
	}
	
	/** Met à jour le temps de chaque projet en fonction du pas
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param string $pas : si on précise un pas, on l'utilise, sinon par défaut c'est mois
	*/
	public function majTpsProjet($pas=NULL){
		foreach($this->projet as $id_projet=>$temps){
			//calcul du temps passé sur le projet
			ATF::hotline_interaction()->q->reset()
										->addField("SUM(TIME_TO_SEC(hotline_interaction.temps_passe))",'tps')
										->setStrict()
										->addJointure("hotline_interaction","id_hotline","hotline","id_hotline")
										->addCondition("hotline_interaction.id_user",NULL,NULL,false,"IS NOT NULL")
										->addCondition("hotline.id_gep_projet",$id_projet)
										->setDimension('cell');
	
			$temps_total=ATF::hotline_interaction()->sa();
			
			//dans le temps fixé
			if($pas=="semaine" || $this->pas=="semaine"){
				ATF::hotline_interaction()->q->addCondition("DATE_ADD(hotline_interaction.date, INTERVAL 12 WEEK)","'".date('Y-m-d')."'",NULL,false,">=",false,false,true);
			}else{
				ATF::hotline_interaction()->q->addCondition("DATE_ADD(hotline_interaction.date, INTERVAL 12 MONTH)","'".date('Y-m-d')."'",NULL,false,">=",false,false,true);
			}
			$temps_graphe=ATF::hotline_interaction()->sa();
			
			$this->projet[$id_projet]=$this->sec_to_day($temps_graphe)." / ".$this->sec_to_day($temps_total);
		}
	}
	
	/** Transforme le temps en jours (demandé en jour, mais du coup moins précis)
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function sec_to_day($temps){
		$heure=floor($temps/3600);
		return round($heure/7,1);
	}



/* PARTIE DES FONCTIONS POUR TELESCOPE*/


	/** Fonction qui génère les résultat pour les champs d'auto complétion société
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
	public function _ac($get,$post) {
		$length = 25;
		$start = 0;

		$this->q->reset();

		// On ajoute les champs utiles pour l'autocomplete
		$this->q->addField("id_gep_projet")->addField("gep_projet");

		if ($get['q']) {
			$this->q->setSearch($get["q"]);
		}

		if ($get['id_societe']) {
			$this->q->where("id_societe",$get["id_societe"]);
		}

		$this->q->setLimit($length,$start)->setPage($start/$length);

		return $this->select_all();
	}


	
};
?>