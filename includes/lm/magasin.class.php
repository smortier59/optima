<?	
/** Classe Magasin
* @package Optima
* @subpackage Leroy Merlin
*/

class magasin extends classes_optima {	

	function __construct($table_or_id=NULL) {
		$this->table = "magasin";
		parent::__construct($table_or_id);		

		$this->colonnes['fields_column'] = array(
			'magasin.magasin'
			,'magasin.langue'
			,'magasin.afficher'=>array("rowEditor"=>"ouinon","renderer"=>"etat","width"=>80)
			,'magasin.id_magasin_lm'
		);

		$this->addPrivilege("EtatUpdate");


	}

	/**
	 * Permet de modifier un champs en AJAX
	 * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	 * @return bool
	 */
	public function EtatUpdate($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL){
        
        $data["id_magasin"] = $this->decryptId($infos["id_magasin"]);
        $data[$infos["field"]] = $infos[$infos["field"]];
               
        if ($r=$this->u($data)) {
            ATF::$msg->addNotice(loc::mt(ATF::$usr->trans("notice_update_success")));
        }
        return $r;
    }

	
} 
?>