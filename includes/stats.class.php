<?
/**
* Module statistique
* @package Optima
*/
class stats extends classes_optima {
	/**
	* Constructeur
	*/
	public function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		$this->stats = array("affaire"=>array("taille"=>"500px","couleur"=>"rouge")
							,"suivi"=>array("taille"=>"200px","couleur"=>"vert")
							,"devis"=>array("taille"=>"150px","couleur"=>"rouge")
							,"commande"=>array("taille"=>"200px","couleur"=>"rouge")
							,"facture"=>array("taille"=>"125px","couleur"=>"rouge")
							,"societe"=>array("taille"=>"400px","couleur"=>"vert")
							,"tache"=>array("taille"=>"225px","couleur"=>"jaune")
							,"contact"=>array("taille"=>"650px","couleur"=>"vert")
							,"emailing_tracking"=>array("taille"=>"100px","couleur"=>"violet")
							,"stats"=>array("taille"=>"250px","couleur"=>"violet")
							,"gep_projet"=>array("taille"=>"300px","couleur"=>"vert"));
		$this->liste_annees = $this->initialisation();
	}	
	
	/** 
	* Initilisation des années à afficher (par défaut juste l'année courante doit l'être)
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function initialisation(){
		foreach($this->stats as $module=>$infos){
			$tab[$module][date('Y')]=1;	
		}
		return $tab;
	}
	
	/**
	* Méthode qui va permettre d'enregistrer les années cochées/décochées, et d'attribuer l'état correspondant pour qu'elle soit prise ou non en compte sur les graphes
	* @author Nicolas BERTEMONT	<nbertemont@absystech.fr>
	*/
	public function modif_liste_annee($type,$module,$annee){
		if($type=="ajout")
			$this->liste_annees[$module][$annee]=1;
		else
			$this->liste_annees[$module][$annee]=0;
	}
	
	/**
	* Crée un menu pour les statistiques. Par défaut ce menu est utilisé par le menu de l'utilisation (usr->createMenu)
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	*/
	public function createMenu(){
		$menu_stats=array();
		foreach($this->stats as $nom=>$infos){
			if(ATF::$usr->privilege($nom,"select")){
				if($infos["multigraphe"]=="true"){
					$ss_tab=array();
					foreach($infos["graphes"] as $graph=>$donnees){
						array_push($ss_tab,array("module"=>$graph
									,"icone"=>ATF::$staticserver."images/module/16/stats.png"
									,"traduction"=>$graph
									,"graphe"=>$nom
									,"graphe_num"=>$donnees["numero"])
							);
					}
				
					array_push($menu_stats,array("module"=>$nom
									,"icone"=>ATF::$staticserver."images/module/16/stats.png"
									,"traduction"=>ATF::$usr->trans($nom,"module")
									,"graphe"=>$nom
									,"enfants"=>$ss_tab
									,"nb_enfants"=>count($ss_tab))
							);
				}else{
					array_push($menu_stats,array("module"=>$nom
									,"icone"=>ATF::$staticserver."images/module/16/stats.png"
									,"traduction"=>ATF::$usr->trans($nom,"module")
									,"graphe"=>$nom)
							);
				}
			}
		}
		return $menu_stats;
	}
	
	/** Transforme la condition sur l'année en condition
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param object $q : querier courant
	* @param string $nom : nom du champs de la date concernée
	* @param integer $valeur : valeur de l'année
	*/
	public function conditionYearSimple(&$q,$nom,$valeur){
		$q->addCondition("YEAR(".$nom.")",$valeur);
	}
	
	/** On met dans le querier toutes les années cochées
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array $liste_annees : liste des années cochées
	* @param object $q : querier courant
	* @param string $nom : nom du champs de la date concernée
	* @param $type : type du graphe (users, user, ca, marge, ...)
	*/
	public function conditionYear($liste_annees,&$q,$nom,$type){
		foreach($liste_annees as $key_list=>$item_list){
			if($item_list)$q->addCondition("YEAR(".$nom.")",$key_list);
		}
	}
	
	/** Définition des labels du graphe
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param string $month : mois en question
	* @param $label : l'éventuel label (ex : nom utilisateur)
	* @param $year : année en question
	*/
	public function intitule($month,$label=NULL,$year=NULL){
		$lib=($label ? $label : $year);
		return array("int"=>$lib,"lib"=>$lib,"lab"=>$lib);
	}
	
	/** Récupération des mois de l'année
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param string $type : type du graphe
	*/
	public function recupMois($type){
		return util::month();
	}
	
	/** Initalisation de tous les set à 0
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array $graph : contient toutes les données nécessaires à l'affichage du graphe
	* @param string $intitule : nom à afficher en label des données
	* @param string $type : type du graphe
	*/
	public function initGraphe(&$graph,$intitule,$type){				
		for ($m=1;$m<13;$m++) {
			$graph['dataset'][$intitule]['set'][strlen($m)<2?"0".$m:$m] = array("value"=>0,"alpha"=>100,"titre"=>$intitule." : 0");
		}
	}
	
	/** On change les catégories du graphe dans le cas où la donnée est 0
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function returnCat($month=NULL,$year=NULL){
		if(!$month)$month=date('m');
		if(!$year)$year=date('Y');
		foreach (util::month() as $k=>$i) {
			if ($k<=$month) {
				$y = $year;
			} else {
				$y = $year-1;
			}
			$categories[$k] = array("label"=>substr($i,0,4).substr($y,2),"hoverText"=>$i." ".$y);
		}
		return $categories;
	}

};
?>