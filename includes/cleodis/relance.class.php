<?
/** Classe relance
* @package Optima
* @subpackage Cléodis
*/
require_once dirname(__FILE__)."/../relance.class.php";
class relance_cleodis extends relance {
	function __construct(){ //PHP5
		parent::__construct();
		$this->table="relance";
		$this->colonnes['fields_column'] = array(	
			'relance.id_societe'
			,'relance.id_contact'
			,'relance.type'
			,'relance.texte'
		);		
		$this->fieldstructure();
        $this->controlled_by = "facture";
        $this->files["relance1"] = array("type"=>"pdf","no_upload"=>true);
        $this->files["relance2"] = array("type"=>"pdf","no_upload"=>true);
        $this->files["relance3"] = array("type"=>"pdf","no_upload"=>true);
        $this->addPrivilege("getWindow");
        $this->addPrivilege("getAllForRelance");
		$this->addPrivilege("insert","insert");
	}

    /**
    * Insère la relance
    * @author Quentin JANON <qjanon@absystech.fr>
    * @param array $infos
    * @return string html
    */
    public function insert($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false) {
        if ($infos['autreFacture']) $autresFactures = explode(",",$infos['autreFacture']);
        $facture = ATF::facture()->select($infos['id_facture']);
        $affaire = new affaire_cleodis($facture["id_affaire"]);
        $devis = $affaire->getDevis();
        $type = self::getNumeroDeRelance($facture['id_facture']); 
        $file = "relance".(self::getNumeroDeRelance($facture['id_facture'],true)+1);

        $data = array(
            "id_societe"=>$affaire->get("id_societe")
            ,"id_contact"=>$devis->get("id_contact")
            ,"type"=>$type
            ,"texte"=>$infos['texte']
        );
        
        $infos["filestoattach"][$file] = "";
        
        ATF::db($this->db)->begin_transaction();
        
        if ($id = parent::insert($data)) {
            ATF::relance_facture()->insert(array("id_relance"=>$id,"id_facture"=>$facture["id_facture"]));  
            if (isset($autresFactures)) {
                foreach ($autresFactures as $id_facture) {
                    ATF::relance_facture()->insert(array("id_relance"=>$id,"id_facture"=>$id_facture));    
                }
            }
        }

        if($infos["preview"]){
            $this->move_files($id,$s,true,$infos["filestoattach"],$file); // Génération du PDF de preview
            ATF::db($this->db)->rollback_transaction();
            return $this->cryptId($id);
        }else{
            $this->move_files($id,$s,false,$infos["filestoattach"],$file); // Génération du PDF avec les lignes dans la base
            ATF::db($this->db)->commit_transaction();
        }
             
        return $this->cryptId($id);
    



    }

    /** Renvoi le numéro de relance afin de pouvoir la créer
    * @author Quentin JANON <qjanon@absystech.fr>
    * @param array $id_facture ID d'une facture
    * @return string|boolean FALSE si le cycle des relances est déjà terminé, sinon renvoi le numéro de la prochaine relance
    */
    public function getNumeroDeRelance($id_facture,$returnINT=false) {
        if (!$id_facture) return false;
        ATF::relance_facture()->q->reset()->setCountOnly()->where('id_facture',$id_facture);
        
        $n = ATF::relance_facture()->select_cell();
        if ($returnINT) return $n;
        if ($n==="0") {
            return "premiere";
        } elseif ($n==="1") {
            return "seconde";
        } elseif ($n==="2") {
            return "mise_en_demeure";
        } else {
            return false;
            throw new error(ATF::$usr->trans("il_y_a_deja_une_mise_en_demeure_sur_cette_facture")." : ".ATF::facture()->select($infos['id_facture'],"ref"),1022);  
        }
        
    }

    /** Renvoi l'id_relance afin de pouvoir la créer
    * @author Quentin JANON <qjanon@absystech.fr>
    * @param array $id_facture ID d'une facture
    * @return string|boolean FALSE si le cycle des relances est déjà terminé, sinon renvoi le numéro de la prochaine relance
    */
    public function getIdRelance($id_facture,$type) {
        if (!$id_facture) return false;
        $this->q->reset()
                    ->addField("relance.id_relance")
                    ->from("relance","id_relance","relance_facture","id_relance")
                    ->where("relance.type",$type)
                    ->where('relance_facture.id_facture',$id_facture)
                    ->setStrict();
        
        return $this->select_cell();
    }
	
	/** Renvoi l'id_relance afin de pouvoir la créer
    * @author Quentin JANON <qjanon@absystech.fr>
    * @param array $id_facture ID d'une facture
    * @return string|boolean FALSE si le cycle des relances est déjà terminé, sinon renvoi le numéro de la prochaine relance
    */
    public function getIdFactures($id_relance,$filtered_facture=false) {
        if (!$id_relance) return false;
        $this->q->reset()
                    ->addField("GROUP_CONCAT(relance_facture.id_facture)","id_autreFacture")
                    ->addField("GROUP_CONCAT(facture.ref)","ref_autreFacture")
                    ->from("relance","id_relance","relance_facture","id_relance")
                    ->from("relance_facture","id_facture","facture","id_facture")
                    ->where('relance.id_relance',$id_relance)
                    ->setStrict();
        if ($filtered_facture) {
            $this->q->where("relance_facture.id_facture",$filtered_facture,"OR",false,"!=");  
        }
        $return = $this->select_row();

        return $return;
    }
};

class relance_cleodisbe extends relance_cleodis { };
class relance_cap extends relance_cleodis { };
class relance_exactitude extends relance_cleodis { };
?>