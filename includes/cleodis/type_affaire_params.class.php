<?
/** Classe type_affaire
* @package Optima
* @subpackage Cléodis
*/
class type_affaire_params extends classes_optima {
	public function __construct() {
        parent::__construct();

		$this->table = "type_affaire_params";
		$this->colonnes["fields_column"] = array(
            "id_type_affaire"
			,"id_societe"
		);

		$this->colonnes["fields_column"] = array(
            "id_type_affaire"
            ,"id_societe"
		);

    	$this->fieldstructure();
	}



	public function get_type_affaire_by_societe($id_societe){

		$this->q->reset()->where("id_societe", $id_societe);
		$type_affaire = $this->select_row();

		if($type_affaire){
			return $type_affaire["id_type_affaire"];
		}else{

			ATF::type_affaire()->q->reset()->where("type_affaire", "normal");
			$type_affaire = ATF::type_affaire()->select_row();
			return $type_affaire["id_type_affaire"];

		}

	}
}