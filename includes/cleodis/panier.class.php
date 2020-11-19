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
        $this->addPrivilege("duplicatePanier");

    }

	public function select_all($order_by=false,$asc='desc',$page=false,$count=false) { 
        
        $this->q->addField("CONCAT(site_associe.url_front,'/',panier.panier)","url_direct_souscription")

                ->addJointure("panier","id_client","site_associe","id_client");

        return parent::select_all($order_by,$asc,$page,$count);
    }
    

    public function duplicatePanier($infos){
		$panier = ATF::panier()->select($this->decryptId($infos['id_panier']));
		$duplicatePanier = array(
		'panier'=>$panier['panier']
		,'date'=>date('Y-m-d h:i:s')
		,'id_client'=>$panier['id_client']
        ,'num_client'=>$panier['num_client']
		,'content'=>$panier['content']
		,'url_retour_success'=>$panier['url_retour_success']
		,'url_retour_error'=>$panier['url_retour_error']
		,'livraison'=>$panier['livraison']
		,'facturation'=>$panier['facturation']
		,'statut'=>'en_cours'
		,'commentaire'=>$panier['commentaire']
		,'permalien'=>$panier['permalien']
		,'expire_permalien'=>$panier['expire_permalien']
		,'id_affaire'=>NULL
		,'siret'=>$panier['siret']
		,'email'=>$panier['email']
		,'meelo_record_id'=>$panier['meelo_record_id']
		,'meelo_record_url'=>$panier['meelo_record_url']

		);

		ATF::panier()->i($duplicatePanier);

		return true;

	}



}