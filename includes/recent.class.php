<?
/**
* @package Optima
*/
class recent extends classes_optima {
	function __construct() {
		parent::__construct();
		$this->colonnes = array('event','module', 'id','id_user', 'date');
		$this->table = __CLASS__;
	}
	
	function insert($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL,$nolog=false) {
		$infos["date"] = date("Y-m-d H:i:s",time());
		return parent::insert($infos);
	 }
	
	function last_update($id,$module) {
		$query = "SELECT `id_user`,`date` FROM `".$this->table."` WHERE `id`=".$id." AND `module`='".$module."' AND `event`='update' ORDER BY `date` DESC LIMIT 0,1";
		if ($return = ATF::db()->fetch_assoc_once($query)) {
			return ATF::$usr->date_trans($return["date"],true,true)." (".ATF::user()->nom($return["id_user"]).")";
		} 
	}
	
	function creation($id,$module) {
		$query = "SELECT `id_user`,`date` FROM `".$this->table."` WHERE `id`=".$id." AND `module`='".$module."' AND `event`='insert' ORDER BY `date` DESC LIMIT 0,1";
		if ($return = ATF::db()->fetch_assoc_once($query)) {
			return ATF::$usr->date_trans($return["date"],true,true)." (".ATF::user()->nom($return["id_user"]).")";
		}
	}
	
	/*  
	* Retourne les X modules les plus visités
	* 	pour un id_user donné, X étant la limit
	* @author Fanny DECLERCK <fdeclerck@absystech.fr>
	* @date 2009-04-07
	* @param array $s ,session
	* @param int $limit : nb d'enregistrement à retourner
	* @return array $module
	*/
	function module_recent($s,$limit=5){
		$this->q->reset();			
		$this->q->addField('count(*)','nb')			
			->addField('recent.module','module')
			->addCondition('id_user',ATF::$usr->getID()) //`id_user` =28   
			->addCondition('module','filtre',"AND",'filtre',"!=") 
			->addCondition('module','societe_frais_port',"AND",'societe_frais_port',"!=") 
			->addCondition('module','pointage',"AND",'pointage',"!=") 
			->addCondition('module','user',"AND",'user',"!=")
			->addOrder('nb','desc') 
			->addGroup('module'); 
		$this->q->setLimit($limit);//5
		return $this->select_all();
	}
	
	/*  
	* Retourne les X dernier enregistrement d'un module ayant subi une action
	* 	X étant la limit
	* @author Fanny DECLERCK <fdeclerck@absystech.fr>
	* @date 2009-04-07
	* @param string $module
	* @param int $limit : nb d'enregistrement à retourner
	* @return array $id
	*/
	function id_by_module($module,$limit=5){
		$this->q->reset();
		$this->q->addField('date, event, id_user, id') 
			->addCondition('module',$module)   
			->setLimit($limit);//5
		return $this->select_all('`recent`.`id_recent`','desc');
	}
};
?>