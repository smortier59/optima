<?php

class zonegeo extends classes_optima {
	
	function __construct() {
		parent::__construct();
		
		$this->colonnes['fields_column'] = array(
			  "zonegeo.cp"
			 ,"zonegeo.id_zonegeo"
			 ,"nombre"=>array("custom"=>true,"align"=>"center","width"=>120)
		);
		
		$this->autocomplete = array(
				"view"=>array("zonegeo.zonegeo","zonegeo.cp","zonegeo.id_zonegeo")
				,"field"=>array("zonegeo.zonegeo","zonegeo.cp","zonegeo.id_zonegeo")
				,"show"=>array("zonegeo.zonegeo","zonegeo.cp","zonegeo.id_zonegeo")
				,"popup"=>array("zonegeo.zonegeo","zonegeo.cp","zonegeo.id_zonegeo")
			);
		
		$this->fieldstructure();
		
		
		$this->onglets = array(
				"adherent"=>array('opened'=>true)
		);
						
		$this->no_insert =
		$this->no_select = 		
		$this->no_update = true;
		
	}
	
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){
		if (!count($this->q->field)) {
			foreach($this->colonnes['fields_column'] as $key=>$item){
				if(!$item["custom"]){
					$this->q->addField($key);
				}
			}
		}
		if(ATF::_r("pager") && ATF::_r("pager") == "gsa_zonegeo"){
			$this->q->from("zonegeo","id_zonegeo","adherent","id_zonegeo")
			     ->addField("COUNT(adherent.id_adherent)","nombre")				 
			     ->addGroup("adherent.id_zonegeo")
				 ->addOrder("nombre", "desc");
		}
		$return =  parent::select_all($order_by,$asc,$page,$count);			
		return $return;		
	}
}
