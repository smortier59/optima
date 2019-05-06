<?
class licence extends classes_optima {

	public function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		$this->colonnes["fields_column"] = array(
			'licence.licence' =>array("custom"=>true,"nosort"=>true,"align"=>"left"),
			/*'licence.part_1',
			'licence.part_2'*/
			'licence.id_licence_type'
		);
		$this->fieldstructure();

		$this->foreign_key['id_licence_type'] =  "licence_type";

		$this->colonnes['bloquees']['select'] = array("part_1", "part_2");
	}


	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){


		$this->q->addField("UPPER(CONCAT('****************************',`licence`.`part_2`))","licence.licence")
						 ->addField("UPPER(`licence`.`part_1`)","licence.part_1")
						 ->addField("UPPER(`licence`.`part_2`)","licence.part_2");

		$return = parent::select_all($order_by,$asc,$page,$count);


		return $return;
	}


};
class licence_midas extends licence { };
class licence_cleodisbe extends licence { };
class licence_cap extends licence { };
class licence_bdomplus extends licence { };
class licence_bdom extends licence { };
class licence_boulanger extends licence { };