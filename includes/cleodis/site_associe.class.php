<?php

class site_associe extends classes_optima{

    public function __construct() { 
        parent::__construct();
        $this->table = "site_associe";

        $this->colonnes['fields_column'] = array(
           
            'site_associe.site_associe'
            ,'site_associe.code'
            ,'site_associe.steps_tunnel'
            ,'site_associe.id_client'
            ,'site_associe.url_front'
            ,'site_associe.cs_score_minimal'
            ,'site_associe.age_minimal'
            ,'site_associe.export_middleware'
            ,'site_associe.id_societe'
            ,'site_associe.color_dominant'
            ,'site_associe.color_footer'
            ,'site_associe.color_links'
            ,'site_associe.color_titles'
           
        );

        $this->colonnes['primary'] = array(
             'site_associe'
            ,'code'
            ,'steps_tunnel'
            ,'id_client'
            ,'url_front'
            ,'cs_score_minimal'
            ,'age_minimal'
            ,'export_middleware'
            ,'id_societe'
            ,'color_dominant'
            ,'color_footer'
            ,'color_links'
            ,'color_titles'
		);

        $this->fieldstructure();

    }


   
  

	
}