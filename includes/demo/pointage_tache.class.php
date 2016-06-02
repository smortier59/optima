<?
/**
* Gestion du pointage
* @package Optima
*/
class pointage_tache extends classes_optima {
	/**
	* Contructeur
	*/
	public function __construct() {
		parent::__construct();
	}	
	
	public function getByLibelle($lib,$field=false) {
		$this->q->reset()->where('pointage_tache',$lib)->setDimension('row');
		if ($field) {
			$this->q->addField($field)->setDimension('cell');
		}
		return $this->sa();
	}

	public function saTaches($order_by=false,$asc=false,$page=false,$count=false) {
		$this->q->reset();
		return parent::select_all($order_by,$asc,$page,$count);	
	}

	public function getAllLib() {
		$this->q->reset()->addField('pointage_tache')->setStrict();
		
		foreach (parent::sa() as $k=>$i) {
			$return[] = $i['pointage_tache'];
		}
		return $return;
	}
};
?>