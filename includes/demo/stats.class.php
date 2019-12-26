<?
require_once dirname(__FILE__)."/../stats.class.php";
/**  
* @package Optima
* @subpackage AbsysTech
*/
class stats_absystech extends stats {
	function __construct() { // PHP5
		parent::__construct();
		$this->stats["hotline_interaction"]=array(
			"multigraphe"=>"true"
			,'couleur'=>"bleu"
			,'taille'=>"100px"
			,"graphes"=>array("graph1"=>array("numero"=>1,"taille"=>"100px","couleur"=>"bleu")
							,"graph2"=>array("numero"=>2,"taille"=>"150px","couleur"=>"bleu")
							,"graph3"=>array("numero"=>3,"taille"=>"125px","couleur"=>"bleu")
							,"graph4"=>array("numero"=>4,"taille"=>"90px","couleur"=>"bleu")
							,"graph5"=>array("numero"=>5,"taille"=>"50px","couleur"=>"bleu")
		));
		//$this->stats["hotline"]=array("taille"=>"600px","couleur"=>"bleu");
		$this->stats["hotline"]=array(
			"multigraphe"=>"true"
			,'couleur'=>"bleu"
			,'taille'=>"200px"
			,"graphes"=>array("graph1"=>array("numero"=>1,"taille"=>"150px","couleur"=>"bleu")
							,"graph2"=>array("numero"=>2,"taille"=>"100px","couleur"=>"bleu")
							,"graph3"=>array("numero"=>3,"taille"=>"50px","couleur"=>"bleu")
		));
		$this->stats["stats"]=array("taille"=>"250px","couleur"=>"violet");
		$this->liste_annees = $this->initialisation();
	}	
};

class stats_att extends stats_absystech { 
	/** Transforme la condition sur l'année, en condition sur un intervalle de temps (entre juillet et juin)
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param object $q : querier courant
	* @param string $nom : nom du champs de la date concernée
	* @param integer $valeur : valeur de l'année
	*/
	public function conditionYearSimple(&$q,$nom,$valeur){
		$q->addCondition($nom,$valeur."-07-01","AND","annee_".$valeur,">=");
		$q->addCondition($nom,($valeur+1)."-06-30","AND","annee_".$valeur,"<=");
	}
	
	/** Transforme la condition sur l'année, en condition sur un intervalle de temps (entre juillet et juin) mais avec une concaténation des conditions en OR et non AND (comme c'est de base)
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array $liste_annees : liste des années cochées
	* @param object $q : querier courant
	* @param string $nom : nom du champs de la date concernée
	* @param $type : type du graphe (users, user, ca, marge, ...)
	*/
	public function conditionYear($liste_annees,&$q,$nom,$type){
		//pour le graphe concernant tous les users, on ne prends pas cet intervalle en compte
		if($type!="users"){
			foreach($liste_annees as $key_list=>$item_list){
				if($item_list){
					$q->addCondition($nom,$key_list."-07-01","AND","annee_".$key_list,">=");
					$q->addCondition($nom,($key_list+1)."-06-30","AND","annee_".$key_list,"<=");
					$liste_annee[]="annee_".$key_list;
				}
			}
			if($liste_annee)$q->addSuperCondition(implode(",",$liste_annee),"OR",true);
		}else{
			parent::conditionYear($liste_annees,$q,$nom,$type);
		}
	}

	/** L'année, n'étant pas une année civile mais fiscale, elle est comprise entre deux années, je ne peux donc pas en afficher qu'une (ex: pour 2011, l'année fiscale est 2011/2012)
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param string $month : mois en question
	* @param $label : l'éventuel label (ex : nom utilisateur)
	* @param $year : année en question
	*/
	public function intitule($month,$label=NULL,$year=NULL){
		$lab=($label ? $label : $year);
		if(!$label && $year){
			if($month>=7 && $month<=12){
				$intitule=$year."/".($year+1);
			}elseif($month>=1 && $month<=6){
				$intitule=($year-1)."/".$year;
			}	
			$libelle=$year."-".$month;
			return array("int"=>$intitule,"lib"=>$libelle,"lab"=>$lab);
		}
		return array("int"=>$lab,"lib"=>$lab,"lab"=>$lab);
	}
	
	/** Contrairement à l'année civile d'AT, l'année fiscale d'ATT va de Juillet de l'année en cours à Juin de l'année suivante
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param string $type : type du graphe
	*/
	public function recupMois($type){
		//pour le graphe de tous les users, on garde l'année civile
		if($type!="users"){
			foreach(util::month() as $key=>$mois){
				$cle=$key;
				$cle=$key-6;
				if($cle<=0){
					$cle+=12;
				}
				$liste[$cle]=$mois;
			}
			ksort($liste);
			
			$mois_inverse=array_flip(util::month());
			foreach($liste as $item){
				$liste2[$mois_inverse[$item]]=$item;
			}
			return $liste2;
		}else{
			return parent::recupMois($type);
		}
	}
	
	/** Initalisation de tous les set à 0, mais en commençant par le mois de juillet
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array $graph : contient toutes les données nécessaires à l'affichage du graphe
	* @param string $intitule : nom à afficher en label des données
	* @param string $type : type du graphe
	*/
	public function initGraphe(&$graph,$intitule,$type){		
		if($type!="users"){
			// /!\ laissez les deux for de cette manière pour avoir le mois de juillet en premier dans le tableau			
			for ($m=7;$m<13;$m++) { 
				$m2=strlen($m)<2?"0".$m:$m;
				$graph['dataset'][$intitule]['set'][$m2] = array("value"=>0,"alpha"=>100,"titre"=>$intitule." : 0");
			}
			for ($m=1;$m<7;$m++) {
				$m2=strlen($m)<2?"0".$m:$m;
				$graph['dataset'][$intitule]['set'][$m2] = array("value"=>0,"alpha"=>100,"titre"=>$intitule." : 0");
			}
		}else{
			parent::initGraphe($graph,$intitule,$type);
		}
	}
	
	/** On change les catégories du graphe dans le cas où la donnée est 0, il ne faut pas affiché l'année en question, mais celle correspondant à la partie de l'année fiscale
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function returnCat(){
		foreach ($this->recupMois() as $k=>$i) {
			if ($k<=date("m") && $year!="fin") {
				$y = date("Y");
				if($k==date("m"))$year="fin";
			} else {
				$y = date("Y")-1;
			}
			$categories[$k] = array("label"=>substr($i,0,4).substr($y,2),"hoverText"=>$i." ".$y);
		}
		return $categories;
	}

}
class stats_demo extends stats_absystech { }
class stats_wapp6 extends stats_absystech { }
?>