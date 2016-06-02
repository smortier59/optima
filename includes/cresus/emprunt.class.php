<?
class emprunt extends classes_optima {
	function __construct() {
		parent::__construct();
		$this->colonnes["fields_column"] = array(	
			  "emprunt.id_adherent"
			  ,"organisme"	
			  ,"mensualite"=>array("aggregate"=>array("sum","min","avg","max"),"align"=>"right","renderer"=>"money","width"=>80)
			  ,"montant"   =>array("aggregate"=>array("sum","min","avg","max"),"align"=>"right","renderer"=>"money","width"=>80)
			  ,"impaye"    =>array("aggregate"=>array("sum","min","avg","max"),"align"=>"right","renderer"=>"money","width"=>80)		  
			  ,"date_debut"
			  ,"date_fin"
		);		
		
		$this->fieldstructure();
		$this->field_nom = "id_adherent";
	}

	/**
	* Surcharge du select-All
	*/
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){
		$return = parent::select_all($order_by,$asc,$page,$count);

		//Ajout du Num de dossier pour les exports
		if($return[0]){
			foreach ($return as $key => $value) {
				if($value["emprunt.id_adherent_fk"]){ $id_adherent = $value["emprunt.id_adherent_fk"]; }else{ $id_adherent = $value["id_adherent"];}			
				$return[$key]["num_dossier"] = ATF::adherent()->select($id_adherent, "num_dossier");
			}	
		}

		return $return;
	}
}	