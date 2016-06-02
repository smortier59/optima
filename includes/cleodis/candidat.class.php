<?
/** Classe candidat
* @package Optima
*/
require_once dirname(__FILE__)."/../candidat.class.php";
class candidat_cleodis extends candidat { };


class candidat_exactitude extends candidat_cleodis{
	/*--------------------------------------------------------------*/
	/*                   Attributs                                  */
	/*--------------------------------------------------------------*/
	/*--------------------------------------------------------------*/
	/*                   Constructeurs                              */
	/*--------------------------------------------------------------*/
	function __construct() {
		parent::__construct();
		$this->table = "candidat";
		
		$this->colonnes['fields_column'] = array(
			 'candidat.nom'
			,'candidat.prenom'
			,'candidat.ville'
			,'candidat.email'=>array("renderer"=>"email")
			,"action"=>array("custom"=>true,"nosort"=>true,"renderer"=>"AttacherMission","width"=>80)
			//,'fichier_joint'=>array("width"=>50,"custom"=>true,"nosort"=>true,"type"=>"file")
			,'cv'=>array("width"=>50,"custom"=>true,"nosort"=>true,"type"=>"file")
		);

		unset($this->colonnes['fields_column']["fichier_joint"], $this->files["fichier_joint"]);
		$this->fieldstructure();
		$this->field_nom = "civilite,nom,prenom";
		//$this->files["fichier_joint"] = array("multiUpload"=>true);
		$this->files["cv"] = array("type"=>"pdf","preview"=>false,"no_generate"=>true);
		
		$this->colonnes['bloquees']['insert'] =  	
		$this->colonnes['bloquees']['update'] = array('id_user','etat','raison','date');

		$this->onglets = array("affaire_candidat");		
	}


	public function getAffaires(){
		ATF::affaire()->q->reset()->where("affaire.etat","terminee","AND",false,"!=")
								  ->where("affaire.etat","perdue","AND",false,"!=");
		return ATF::affaire()->select_all();
	}


	/** 
	* @author yann GAUTHERON <ygautheron@absystech.fr>
	*/
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false,$parent=false) {

		if (!count($this->q->field)) {
			foreach($this->colonnes['fields_column'] as $key=>$item){
				if(!$item["custom"]){
					$this->q->select($key);
				}
			}
		}
		$this->q->addField("candidat.ocrcv","ocrcv");

	
		if ($search = $this->q->getSearch()) {	
			if(strstr($search , "(") || strstr($search , '"')){
				preg_match_all("/\(([^)]+)\)/", $search, $searchs);
				$jointures = preg_split("/\(([^)]+)\)/", $search);

				log::logger($jointures , "mfleurquin");

				$this->prepareQuerierSearch($searchs,$jointures);
			}

			if(strstr($search , "ET") || strstr($search , 'OU')){
				
			}else{
				$search = explode(" ",$search);

				foreach ($search as $kw) {
					$searchResult[] = $kw;			
				}
				$this->q->setSearch(implode(" ",$searchResult));
			}			
		}

		
		
		return parent::select_all($order_by,$asc='desc',$page,$count,$parent);
	}

	public function prepareQuerierSearch($search, $jointures=NULL){
		

		if(is_array($search) && is_array($search[0])){		
			$this->prepareQuerierSearch($search[1]);			
		}else{	
			if(is_array($search)){
				foreach ($search as $key => $value) {
					$this->prepareQuerierSearch($value);	
				}
			}else{
				log::logger($search , "mfleurquin");
			}	
		}
	}

};
?>