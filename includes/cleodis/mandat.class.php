<?  
/** Classe mandat
* @package Optima
* @subpackage CAP
*/
class mandat extends classes_optima { 
};

class mandat_cap extends mandat {


    function __construct() {
        parent::__construct(); 
        $this->table = "mandat";
        $this->colonnes['fields_column'] = array( 
             'mandat.ref'
            ,'mandat.id_societe'
            ,'mandat.etat'
            ,'fichier_joint'=>array("custom"=>true,"nosort"=>true,"type"=>"file","align"=>"center","width"=>70)
            ,'retourBPA'=>array("custom"=>true,"nosort"=>true,"type"=>"file","align"=>"center","renderer"=>"uploadFile","width"=>70)
            ,"mandat.date_envoi"=>array("renderer"=>"updateDate","width"=>170)
            ,"mandat.date_retour"=>array("renderer"=>"updateDate","width"=>170)
            ,"mandat.date_debut"=>array("renderer"=>"updateDate","width"=>170)
            ,"mandat.date_fin"=>array("renderer"=>"updateDate","width"=>170)
        );

        $this->colonnes['primary'] = array(
            "ref"            
            ,"id_affaire"
            ,"id_societe"
            ,"date"  
            ,"date_envoi"   
            ,"date_retour" 
            ,"id_representant" 
            ,"nature"=>array("listeners"=>array("change"=>"ATF.natureMandatChange"))     
        );

        $this->colonnes['panel']['participants'] = array(
            "contact"=>array("custom"=>true)  
        );

        $this->colonnes['panel']['flux_entrant'] = array(
               "type_creance"
              ,"enregistrement_creance"
        );        

        $this->colonnes['panel']['production'] = array(            
             "phase_judiciaire_auto"
            ,"autorisation_huissier"
            ,"visite_domiciliaire"
            ,"relance_interne"       
        );       

        $this->colonnes['panel']['reporting'] = array(
             "acces_web"
            ,"certif_irrecouvrabilite_auto"
        );

        $this->panels['flux_entrant'] = array('nbCols'=>2,'isSubPanel'=>true,'collapsible'=>true,'visible'=>true);
        $this->panels['production'] = array('nbCols'=>1,'isSubPanel'=>true,'collapsible'=>true,'visible'=>true);
        $this->panels['reporting'] = array('nbCols'=>2,'isSubPanel'=>true,'collapsible'=>true,'visible'=>true);

        $this->colonnes['panel']['condition'] = array(
             "flux_entrant"=>array("custom"=>true,'xtype'=>'fieldset','panel_key'=>'flux_entrant')
            ,"production"=>array("custom"=>true,'xtype'=>'fieldset','panel_key'=>'production')  
            ,"reporting"=>array("custom"=>true,'xtype'=>'fieldset','panel_key'=>'reporting')               
            ,"cahier_charge"                
            ,"commentaire"
            ,"indemnite_retard"
            ,"clause_penale"
            ,"clause_penale_percentage"
        );

               
        $this->colonnes['panel']['ligne_btob'] = array(
            "btob"=>array("custom"=>true)            
            ,"precision_btob"
        );
       

        $this->colonnes['panel']['ligne_btoc'] = array(
            "btoc"=>array("custom"=>true)            
            ,"precision_btoc"
        );

        $this->colonnes['panel']['ligne_cedre'] = array(
            "cedre"=>array("custom"=>true)
        );

        $this->panels['ligne_btob'] = array('nbCols'=>1,'visible'=>false);
        $this->panels['ligne_btoc'] = array('nbCols'=>1,'visible'=>false);
        $this->panels['ligne_cedre'] = array('nbCols'=>1,'visible'=>false);
        $this->panels['condition'] = array('nbCols'=>1,'visible'=>true);

        $this->colonnes['bloquees']['insert'] =  
        $this->colonnes['bloquees']['clone'] =  
        $this->colonnes['bloquees']['update'] =  array_merge(array("date_retour","date_envoi","reversement_cheque","enquete_adresse","date_debut","date_fin","etat"));

        $this->fieldstructure();    

        $this->onglets = array('mandat_ligne');
        $this->field_nom = "ref";
        $this->foreign_key["id_representant"] = "contact";
        $this->files["fichier_joint"] = array("type"=>"pdf","preview"=>true,"no_upload"=>true);
        $this->files["retourBPA"] = array("type"=>"pdf","preview"=>false,"no_upload"=>true,"no_generate"=>true);
    }


    /** 
    * Surcharge de l'insert afin d'insérer les lignes de devis de créer l'affaire si elle n'existe pas
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>    
    * @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
    * @param array &$s La session
    * @param array $files $_FILES
    * @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
    * @param array $nolog True si on ne désire par voir de logs générés par la méthode
    */
    public function insert($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
        if(isset($infos["preview"])){
            $preview=$infos["preview"];
        }else{
            $preview=false;
        }     

        $btoc = json_decode($infos["values_".$this->table]["btoc"],true);
        $btob = json_decode($infos["values_".$this->table]["btob"],true);
        $cedre = json_decode($infos["values_".$this->table]["cedre"],true);

        if(isset($infos["tu"])){ $tu = true; }else{ $tu = false; }
        
        $infos = $infos["mandat"];

        ATF::db($this->db)->begin_transaction();

        $mandat_contact = $infos["contact"];
        $audit = $infos["id_audit"];

        unset($infos["contact"], $infos["id_audit"]);

        ATF::audit()->u(array("id_audit"=>$audit, "etat"=>"signe"));

        $last_id=parent::insert($infos,$s,NULL,$var=NULL,NULL,true);

        //iNSERTION DES CONTACTS MANDAT
        foreach ($mandat_contact as $key => $value) {
            ATF::mandat_contact()->insert(array("id_mandat"=>$last_id, "id_contact"=>ATF::contact()->decryptId($value)));
        }

        if($infos["nature"] == "normal"){
            if($infos["type_creance"]){
                if($infos["type_creance"] == "btob"){
                    if($btob){
                        foreach ($btob as $key => $value) {
                            ATF::mandat_ligne()->insert(array( "id_mandat"=>$last_id,
                                                               "texte" => $value["mandat_ligne__dot__texte"] ,
                                                               "valeur"=> $value["mandat_ligne__dot__valeur"],
                                                               "type"=> $value["mandat_ligne__dot__type"],
                                                               "ligne_titre"=> $value["mandat_ligne__dot__ligne_titre"],
                                                               "mandat_type"=> "btob"
                                                        ));
                        }
                    }
                }elseif($infos["type_creance"] == "btoc"){
                    if($btoc){
                        foreach ($btoc as $key => $value) {
                            ATF::mandat_ligne()->insert(array( "id_mandat"=>$last_id,
                                                               "texte" => $value["mandat_ligne__dot__texte"] ,
                                                               "valeur"=> $value["mandat_ligne__dot__valeur"],
                                                               "type"=> $value["mandat_ligne__dot__type"],
                                                               "ligne_titre"=> $value["mandat_ligne__dot__ligne_titre"],
                                                               "mandat_type"=> "btoc"
                                                        ));
                        }
                    }
                }else{
                    if($btob){
                        foreach ($btob as $key => $value) {
                            ATF::mandat_ligne()->insert(array( "id_mandat"=>$last_id,
                                                               "texte" => $value["mandat_ligne__dot__texte"] ,
                                                               "valeur"=> $value["mandat_ligne__dot__valeur"],
                                                               "type"=> $value["mandat_ligne__dot__type"],
                                                               "ligne_titre"=> $value["mandat_ligne__dot__ligne_titre"],
                                                               "mandat_type"=> "btob"
                                                        ));
                        }
                    }
                    if($btoc){
                        foreach ($btoc as $key => $value) {
                            ATF::mandat_ligne()->insert(array( "id_mandat"=>$last_id,
                                                               "texte" => $value["mandat_ligne__dot__texte"] ,
                                                               "valeur"=> $value["mandat_ligne__dot__valeur"],
                                                               "type"=> $value["mandat_ligne__dot__type"],
                                                               "ligne_titre"=> $value["mandat_ligne__dot__ligne_titre"],
                                                               "mandat_type"=> "btoc"
                                                        ));
                        }
                    }
                }
            }
        }elseif($infos["nature"] == "cedre"){
            if($cedre){
                foreach ($cedre as $key => $value) {
                    ATF::mandat_ligne()->insert(array( "id_mandat"=>$last_id,
                                                       "texte" => $value["mandat_ligne__dot__texte"] ,
                                                       "valeur"=> $value["mandat_ligne__dot__valeur"],
                                                       "type"=> $value["mandat_ligne__dot__type"],
                                                       "ligne_titre"=> $value["mandat_ligne__dot__ligne_titre"],
                                                       "mandat_type"=> "cedre"
                                                ));
                }
            }
        }
        


        if($preview){
            if(!$tu) $this->move_files($last_id,$s,true,$infos["filestoattach"]); // Génération du PDF de preview
            ATF::db($this->db)->rollback_transaction();
            return $this->cryptId($last_id);
        }else{
            if(!$tu) $this->move_files($last_id,$s,false,$infos["filestoattach"]); // Génération du PDF avec les lignes dans la base   
            ATF::db($this->db)->commit_transaction();
        }
        
        if(is_array($cadre_refreshed)){  ATF::affaire()->redirection("select",$infos["id_affaire"]);  }
        return $last_id;
    }

    public function update($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
        $id_mandat = $this->decryptId($infos["mandat"]["id_mandat"]);

        ATF::db($this->db)->begin_transaction();

        ATF::mandat_contact()->q->reset()->where("id_mandat",$id_mandat);
        foreach (ATF::mandat_contact()->select_all() as $key => $value) {
            ATF::mandat_contact()->delete($value["id_mandat_contact"]);
        }

        ATF::mandat_ligne()->q->reset()->where("id_mandat",$id_mandat);
        foreach (ATF::mandat_ligne()->select_all() as $key => $value) {
            ATF::mandat_ligne()->delete($value["id_mandat_ligne"]);
        }

        $infos["mandat"]["id_audit"] = $this->select($infos["mandat"]["id_mandat"] , "id_affaire");

        $this->delete($infos["mandat"]["id_mandat"]);

        unset($infos["mandat"]["id_mandat"]);

        $last_id = $this->insert($infos,$s,$files);



        if($infos["preview"]){
            ATF::db($this->db)->rollback_transaction();
            return $this->cryptId($last_id);
        }else{
            
            ATF::db($this->db)->commit_transaction();
            if(is_array($cadre_refreshed)){  ATF::affaire()->redirection("select",$id_affaire);   }
            return $last_id;
        }
    }


   public function default_value($field){
        if(ATF::_r('id_audit')){
            $audit=ATF::audit()->select(ATF::_r('id_audit'));
        }

        if($audit){
            switch ($field) {
                case "ref": return $audit["ref"];
                case "id_societe": return $audit["id_societe"];
                case "id_affaire": return $audit["id_affaire"];
                case "id_representant": return $audit["id_representant"];
                case "date": return date("Y-m-d");
            }
        }        
        return parent::default_value($field);


    }

    /**
    * Permet de modifier la date sur un select_all
    * @param array $infos id_table, key (nom du champs à modifier),value (nom du champs à modifier)
    * @return boolean
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    */
    public function updateDate($infos){

        if ($infos['value'] == "undefined") $infos["value"] = "";

        $infos["key"]=str_replace($this->table.".",NULL,$infos["key"]);
        $infosMaj["id_".$this->table]=$infos["id_".$this->table];
        $infosMaj[$infos["key"]]=$infos["value"];

        if($infos["key"] == "date_retour" && $infos["value"] && ATF::societe()->select($this->select($infos["id_".$this->table], "id_societe"), "relation") == "prospect"){
           ATF::societe()->u(array("id_societe"=>$this->select($infos["id_".$this->table], "id_societe"), "relation"=>"client"));
        }
        

        if(($infos["key"] == "date_debut" || $infos["key"] == "date_fin") && $infos["value"]){
            if($infos["key"] == "date_debut" && $infos["value"] <= date("Y-m-d")){
                if($this->select($this->decryptId($infos["id_".$this->table]), "date_fin") && $this->select($this->decryptId($infos["id_".$this->table]), "date_fin")>=date("Y-m-d")){
                    $infosMaj["etat"] = "actif";                    
                }
            }

            if($infos["key"] == "date_fin" && $infos["value"] >= date("Y-m-d")){
                $infosMaj["etat"] = "actif";                    
            }else{
                $infosMaj["etat"] = "inactif";
            }
            
        }

        if($this->u($infosMaj)){
            ATF::$msg->addNotice(
                loc::mt(ATF::$usr->trans("notice_update_success_date"),array("record"=>$this->nom($infosMaj["id_".$this->table]),"date"=>$infos["key"]))
                ,ATF::$usr->trans("notice_success_title")
            );           

            
        }else{ return false; }

        return true;
    }
}

?>