<?
/** Classe facture_non_parvenue
* @package Optima
* @subpackage LMA
*/
class facture_non_parvenue extends classes_optima {
	function __construct() {
		parent::__construct();
		$this->table = "facture_non_parvenue"; 

		$this->colonnes['fields_column'] = array(
			"facture_non_parvenue.ref"
			,"facture_non_parvenue.id_facture_fournisseur"
			,"facture_non_parvenue.prix"=>array("aggregate"=>array("min","avg","max","sum"),"renderer"=>"money")
			,"facture_non_parvenue.id_affaire"
			,"facture_non_parvenue.date"
			,"facture_non_parvenue.facturation_terminee"=>array("rowEditor"=>"ouinon","renderer"=>"etat","width"=>80)
			//,'pdf'
		);
		
		$this->fieldstructure();
		
		$this->no_insert = true;
		$this->no_update = true;
		$this->no_delete = true;
		$this->field_nom = "ref";
		$this->selectAllExtjs=true; 

		$this->addPrivilege("EtatUpdate");
	}

	public function EtatUpdate($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL){
        
        $data["id_facture_non_parvenue"] = $this->decryptId($infos["id_facture_non_parvenue"]);
        $data[$infos["field"]] = $infos[$infos["field"]];
               
        if ($r=$this->u($data)) {
            ATF::$msg->addNotice(loc::mt(ATF::$usr->trans("notice_update_success")));
        }
        return $r;
    }

};

?>