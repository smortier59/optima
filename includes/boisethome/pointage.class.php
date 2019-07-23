<?
class pointage_boisethome extends classes_optima {
	public function __construct() {
		parent::__construct();
		$this->colonnes['fields_column'] = array(
			'pointage.date_creation'
			,'pointage.temps'=>array("aggregate"=>array("min","avg","max","sum"),"align"=>"right")
			,'pointage.pourcentage'=>array("aggregate"=>array("min","avg","max"),"align"=>"right")
			,'pointage.date'
			,'pointage.id_user'
			,'pointage.id_devis_lot'
		);
		$this->table = "pointage";
		$this->fieldstructure();
	}

	/**
	* Surcharge du select-All
	*/
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){
		if (!count($this->q->field)) {
			foreach($this->colonnes['fields_column'] as $key=>$item){
				if(!$item["custom"]){
					$this->q->addField($key);
				}
			}
		}
		$this->q
			->from("pointage","id_devis_lot","devis_lot","id_devis_lot")
			->from("devis_lot","id_devis","devis","id_devis")
		;
		return parent::select_all($order_by,$asc,$page,$count);
	}
}