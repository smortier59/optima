<?
/** Cette classe gère la table snap_stat
* Cette notion permet le stockage de valeurs de statistiques chaque jour afin d'observer leurs évolutions sous la forme de graphiques
* @author Yann GAUTHERON <ygautheron@absystech.fr>
* @class stat_snap
* @package Optima
* @subpackage AbsysTech
*/
class stat_snap extends classes_optima {
	public function __construct(){
		parent::__construct();
		$this->table=__CLASS__;
		$this->colonnes['fields_column'] = array(
			'stat_snap.code'
			,'stat_snap.date'
			,'stat_snap.valeur'
		);
		$this->colonnes['primary']=array("code","valeur");
		
		$this->fieldstructure();
		
		ATF::tracabilite()->no_trace[$this->table]=1; //désactivation de tracabilite car inutile vu qu'utiliser en crontab
	}
	
	/** Ajoute la valeur retournée par la méthode de l'objet passé en paramètre
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $singleton
	* @param string $method
	* @todo traductions des erreurs
	* @return void
	*/
	function storeValue($singleton,$method) {
		$class = ATF::getClass($singleton);
		if (is_object($class)) {
			if (method_exists($class,$method)) {
				$infos["valeur"] = $class->$method();
				$infos["id_module"] = ATF::module()->from_nom($singleton);
				$infos["code"] = $method;
				$this->insert($infos);
				return;
			}
			throw new error("La méthode '".$method."' n'existe pas dans le singleton '".$singleton."'");
		}
		throw new error("Le singleton '".$singleton."' n'existe pas");
	}
	
	/** Retourne la liste de tous les snapshots à faire
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @todo Actuellement écrit en dur dans le code PHP !
	* @return array
	*/
	function getSnaps() {
		return array(
			array("facture","getTotalImpayees")
			,array("devis","getTotalPipe")
			,array("devis","getTotalPipePondere")
			,array("affaire","getMargeTotaleDepuisDebutAnnee")
			,array("affaire","getMargeTotaleDepuisDebutAnneeDifferenceAnneePrecedente")
			,array("hotline","getTempsTotalNonResolu")
		);
	}
	
	/** Exécute tous les snapshots
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return void
	*/
	function storeAllValues() {
		ATF::db()->begin_transaction();
		foreach ($this->getSnaps() as $snap) {
			$this->storeValue($snap[0],$snap[1]);
		}
		ATF::db()->commit_transaction();
	}
	
	/**
	* Statistiques
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param integer $pas
	* return enregistrements
	*/
	public function stats($pas){	
		if(!$pas)$pas=30;
		$this->q->reset()
				->addField('code')	
				->addField("DATE_FORMAT(`date`,'%Y-%m-%d')",'jour')
				->addField("valeur")
				->setStrict()
				->addCondition("TO_DAYS(NOW())-TO_DAYS(`".$this->table."`.`date`)",$pas,NULL,"sub_date","<",false,false,true)
				->addOrder('jour','asc');				
		$result=parent::select_all();

		foreach ($result as $i) {
			$graph['categories']["category"][$i["jour"]] = array("label"=>ATF::_s('user')->date_trans($i["jour"]));
		}
		
		$graph['params']['caption'] = "Stats sur les ".$pas." derniers jours";
		$graph['params']['yaxisname'] = "Valeur";
		$graph['params']['labelDisplay']="STAGGER";
		if($pas<=90){
			$graph['params']['labelStep']=substr($pas,0,1);
		}elseif($pas==180){
			$graph['params']['labelStep']=15;
		}elseif($pas==360){
			$graph['params']['labelStep']=35;
		}
		
		/*parametres graphe*/		
		$this->paramGraphe($dataset_params,$graph);	
						
		foreach ($result as $val_) {
			if (!$graph['dataset'][$val_['code']]) {
				$graph['dataset'][$val_['code']]["params"] = array_merge($dataset_params,array(
					"seriesname"=>ATF::_s('user')->trans($val_['code'],'stat_snap')
					,"color"=>dechex(rand(0,16777216))
				));
				
				foreach ($result as $val_2) { 
					$graph['dataset'][$val_['code']]['set'][$val_2["jour"]] = array("value"=>0,"alpha"=>100,"titre"=>ATF::_s('user')->trans($val_['code'],'stat_snap')." (".ATF::_s('user')->date_trans($val_2["jour"]).") : 0");
				}
			}
			$graph['dataset'][$val_['code']]['set'][$val_["jour"]] = array("value"=>$val_['valeur'],"alpha"=>100,"titre"=>ATF::_s('user')->trans($val_['code'],'stat_snap')." (".ATF::_s('user')->date_trans($val_["jour"]).") : ".$val_['valeur']);
		}
		return $graph;
	}

	
	public function stats_spline($module){		
		ATF::getClass($module)->q->reset()->addField("COUNT(*)", "nb")	
										  ->addField("MONTH($module.date)", "mois")
										  ->addField("YEAR($module.date)", "annee")	
										  ->where("$module.date","2013-01-01","AND",false,">=")
										  ->addGroup("mois")
										  ->addGroup("annee");
		$result = ATF::getClass($module)->sa(); 
		
		$an = 2013;
		$mois = 1;
		$data = array();
		
		$month = array( "1"=> "Janvier",
						"2"=> "Février",
						"3"=> "Mars",
						"4"=> "Avril",
						"5"=> "Mai",
						"6"=> "Juin",
						"7"=> "Juillet",
						"8"=> "Aout",
						"9"=> "Septembre",
						"10"=> "Octobre",
						"11"=> "Novembre",
						"12"=> "Décembre",
					  );
		
			
		foreach($result as $k=>$v){			
			if($v["annee"] !== $an || !$data[$an]){
				$an = $v["annee"];				
				for($i=1; $i<=12; $i++){
					$data[$an][$i] = 0;
				}
			}			
			$data[$an][$v["mois"]] = $v["nb"];
		}
		
		foreach ($month as $k=>$v) {
			$graph['categories']["category"][$k] = array("label"=>$v);
		}
		$graph['dataset'] = array();
		
		foreach($data as $k=> $v){
			
			$graph['dataset'][$k] = array();
			foreach ($v as $key => $value) {
				$graph['dataset'][$k][$month[$key]] = $value;
			} 				
			
		}
		return $graph;
	}

};
?>