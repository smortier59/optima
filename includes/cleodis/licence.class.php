<?
class licence extends classes_optima {

	public function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		$this->colonnes["fields_column"] = array(
			'licence.licence' =>array("custom"=>true,"nosort"=>true,"align"=>"left"),
			//'licence.part_1',
			//'licence.part_2',
			'licence.id_licence_type',
			'licence.id_commande_ligne',
			'licence.deja_pris' => array("custom"=> true, "renderer"=>"licence_prise")
		);
		$this->fieldstructure();

		$this->foreign_key['id_licence_type'] =  "licence_type";
		$this->foreign_key['id_commande_ligne'] =  "commande_ligne";

		$this->colonnes['bloquees']['select'] = array("part_1", "part_2");
	}


	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){

		$this->q->addField("UPPER(CONCAT('****************************',`licence`.`part_2`))","licence.licence");
				//->addField("UPPER(`licence`.`part_1`)","licence.part_1")
				//->addField("UPPER(`licence`.`part_2`)","licence.part_2")
				//->from("licence","id_commande_ligne", "commande_ligne", "id_commande_ligne")
				//->from("commande_ligne","id_commande", "commande", "id_commande");

		$return = parent::select_all($order_by,$asc,$page,$count);
		foreach ($return["data"] as $key => $value) {
			if($value["licence.id_commande_ligne"]){
				$return["data"][$key]["deja_pris"] = true;
			}else{
				$return["data"][$key]["deja_pris"] = false;
			}
		}
		return $return;
	}


};
class licence_midas extends licence { };
class licence_cleodisbe extends licence { };
class licence_cap extends licence { };
class licence_bdomplus extends licence { };
class licence_bdom extends licence { };
class licence_boulanger extends licence { };