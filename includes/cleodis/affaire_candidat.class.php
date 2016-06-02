<?  
/** Classe produit_puissance
* @package Optima
* @subpackage Cleodis
*/
class affaire_candidat extends classes_optima {
    
    function __construct() {
        parent::__construct(); 
        $this->table = "affaire_candidat";
        $this->colonnes['fields_column'] = array( 
             'affaire_candidat.id_affaire'
            ,'affaire_candidat.id_societe'
            ,'affaire_candidat.id_candidat'
            ,'affaire_candidat.evalue'=>array("width"=>150,"rowEditor"=>"ouinon","renderer"=>"etat","width"=>80)
            ,'affaire_candidat.envoye'=>array("width"=>150,"rowEditor"=>"ouinon","renderer"=>"etat","width"=>80)
            ,'affaire_candidat.recu_par_client'=>array("width"=>150,"rowEditor"=>"ouinon","renderer"=>"etat","width"=>80)
            ,'affaire_candidat.recrute'=>array("width"=>150,"rowEditor"=>"ouinon","renderer"=>"etat","width"=>80)
            ,'affaire_candidat.commentaire'
        );

        $this->colonnes['primary'] = array(
             "id_affaire"
            ,"id_societe"
            ,"id_candidat"
            ,"commentaire"
        );

        $this->fieldstructure();   

        $this->addPrivilege("EtatUpdate");       
        
    }


    public function EtatUpdate($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL){
        
        $data["id_affaire_candidat"] = $this->decryptId($infos["id_affaire_candidat"]);
        $data[$infos["field"]] = $infos[$infos["field"]];
               
        if ($r=$this->u($data)) {
            ATF::$msg->addNotice(loc::mt(ATF::$usr->trans("notice_update_success")));
        }
        return $r;
    }


   /**
    * Retourne la valeur par défaut spécifique aux données des formulaires
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    * @param string $field
    * @param array &$s La session
    * @param array &$request Paramètres disponibles (clés étrangères)
    * @return string
    */      
    public function default_value($field,&$s,&$request){

        switch ($field) {
            case "id_societe": 
                if(ATF::_r('id_affaire'))  return ATF::affaire()->select(ATF::_r('id_affaire'),"id_societe");
            break;
        }
        return parent::default_value($field,$s,$request);
        
        
    }   
};

?>