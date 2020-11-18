<?php

class panier extends classes_optima{

    function __construct($table_or_id) { 
        
        $this->table = "panier";

        parent::__construct($table_or_id);

        $this->colonnes['fields_column'] = array(
            'duplicate'=>array(
                "custom"=>true,
                "nosort"=>true,
                "align"=>"center",
                "renderer"=>"duplicate",
                "width"=>50
            )
            ,'url_direct_souscription'=>array("custom"=>true)
            ,'panier.panier'
            ,'panier.date'
            ,'panier.id_client'
            ,'panier.num_client'
            ,'panier.content'
            ,'panier.url_retour_success'
            ,'panier.url_retour_error'
            ,'panier.livraison'
            ,'panier.facturation'
            ,'panier.statut'
            ,'panier.commentaire'
            ,'panier.permalien'
            ,'panier.expire_permalien'
            ,'panier.id_affaire'
            ,'panier.siret'
            ,'panier.email'
            ,'panier.meelo_record_id'
            ,'panier.meelo_record_url'


        );

        $this->fieldstructure();

    }

	public function select_all($order_by=false,$asc='desc',$page=false,$count=false) { 
        
        $this->q->addField("CONCAT(site_associe.url_front,'/',panier.panier)","url_direct_souscription")

                ->addJointure("panier","id_client","site_associe","id_client");

        return parent::select_all($order_by,$asc,$page,$count);
	}


}