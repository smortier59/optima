<?
/** Classe questionnaire_fl 
* @package Optima
* @subpackage Cléodis
*/
class questionnaire_fl extends classes_optima {
	function __construct($table_or_id=NULL) {
		$this->table ="questionnaire_fl"; 
		parent::__construct($table_or_id);
		
		$this->colonnes['fields_column'] = array(
			 'questionnaire_fl.question'
			,'questionnaire_fl.type_reponse'							
		);
		
		$this->colonnes['primary'] = array(		 
			 'question'
			,'type_reponse'
			,"index_question"
		);
	
		$this->colonnes['panel']['lignes'] = array(
			"pack_produits"=>array("custom"=>true)
		);
		
		// Propriété des panels		
		$this->panels['lignes'] = array("visible"=>true, 'nbCols'=>1);		

		
		$this->fieldstructure();			

		$this->onglets = array('questionnaire_fl_ligne');
		
		
		$this->formExt=true;		
		//$this->no_delete = true;
		$this->selectAllExtjs=true; 
	}


	/** 
	* Surcharge de l'insert afin d'insérer les lignes de questionnaire_fl de créer l'affaire si elle n'existe pas
	* @author Morgan FLEURQUIN <mfleruquin@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session	
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	*/
	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){	
		$infos_ligne = json_decode($infos["values_".$this->table]["pack_produits"],true);


		$this->infoCollapse($infos);
			
		ATF::db($this->db)->begin_transaction();
			
		$last_id=parent::insert($infos,$s,$files,$cadre_refreshed,$nolog);
				
		if($infos_ligne){
			$infos_ligne=$this->extJSUnescapeDot($infos_ligne,"questionnaire_fl_ligne");
			foreach($infos_ligne as $key=>$item){
				$item["id_questionnaire_fl"]=$last_id;							
				ATF::questionnaire_fl_ligne()->i($item);
			}
		}				
		ATF::db($this->db)->commit_transaction();


		if(is_array($cadre_refreshed)){	ATF::questionnaire_fl()->redirection("select",$last_id); }
		return $last_id;
	}
	
	/** 
	* corrige les lignes de questionnaire_fl
	* @author Morgan FLEURQUIN <mfleruquin@absystech.fr>
	* @param array $infos_ligne ligne de questionnaire_fl
	* @return array $infos_ligne_escapeDot ligne de questionnaire_fl corrigé
	*/
	public function extJSUnescapeDot($infos_ligne,$escape){
		foreach($infos_ligne as $key=>$item){
			foreach($item as $k=>$i){
				$k_unescape=util::extJSUnescapeDot($k);
				$item[str_replace($escape.".","",$k_unescape)]=$i;
				unset($item[$k]);
			}			
			$item["id_pack_produit"]=ATF::pack_produit()->decryptId($item["id_pack_produit_fk"]);
			unset($item["id_pack_produit_fk"]);
			$item["index"]=util::extJSEscapeDot($key);			
			$infos_ligne_escapeDot[]=$item;
		}
		return $infos_ligne_escapeDot;
	}

	/** 
	* Surcharge de l'insert afin d'insérer les lignes de questionnaire_fl de créer l'affaire si elle n'existe pas
	* @author Morgan FLEURQUIN <mfleruquin@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	*/
	public function update($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		$questionnaire_fl=$this->select($infos["questionnaire_fl"]["id_questionnaire_fl"]);	
		ATF::questionnaire_fl()->d($questionnaire_fl["id_questionnaire_fl"]);
		unset($infos["questionnaire_fl"]["id_questionnaire_fl"]);

		$last_id =  $this->insert($infos,$s,$files);
		if(is_array($cadre_refreshed)){	ATF::questionnaire_fl()->redirection("select",$last_id); }
		return $last_id;

	}

}
?>