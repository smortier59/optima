<?
/** Classe pack_produit 
* @package Optima
* @subpackage Cléodis
*/
class pack_produit extends classes_optima {
	
	function __construct($table_or_id=NULL) {
		parent::__construct($table_or_id);
		$this->table ="pack_produit"; 
		
		$this->colonnes['fields_column'] = array('pack_produit.libelle',												
											 	 'pack_produit.popup',
											 	 'pack_produit.fin_formulaire',
												 'pack_produit.type_pack',
												 'pack_produit.ref_lm_principale',
												 'pack_produit.etat'=>array("rowEditor"=>"ouinon","renderer"=>"etat","width"=>80),
												 'pack_produit.id_produit');


		$this->colonnes['primary']=array('libelle',
										 'libelle_ecran_magasin',
										'popup',
										'etat'=>array("targetCols"=>1),							
										'type_pack'=>array("targetCols"=>1),
										'id_produit'=>array("targetCols"=>1),
										'ref_lm_principale'=>array("targetCols"=>1),
										'fin_formulaire',
										'description',
										'service_inclus'
										);					

		
		$this->field_nom = "%id_pack_produit% - %libelle%";
		
		$this->foreign_key['id_produit'] =  "produit";	

		$this->fieldstructure();			
			
		$this->files["photo"] = array("type"=>"png","convert_from"=>array("jpg","png","gif"),"select"=>true);
		$this->files["photo1"] = array("type"=>"png","convert_from"=>array("jpg","png","gif"),"select"=>true);
		$this->files["photo2"] = array("type"=>"png","convert_from"=>array("jpg","png","gif"),"select"=>true);

	
		$this->panels['primary'] = array('nbCols'=>1,'visible'=>true);		
		$this->formExt=true;		
		//$this->no_delete = true;
		$this->selectAllExtjs=true; 

		$this->onglets = array('produit');

		$this->addPrivilege("setInfos","update");
		$this->addPrivilege("EtatUpdate");
	}


	/**
	 * Permet de modifier un champs en AJAX
	 * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	 * @return bool
	 */
	public function setInfos($infos){
		$res = $this->u(array("id_pack_produit"=> $this->decryptId($infos["id_pack_produit"]),
						  $infos["field"] => $infos[$infos["field"]])
					);
		if($res){
			ATF::$msg->addNotice(
				loc::mt(ATF::$usr->trans("notice_update_success"))
				,ATF::$usr->trans("notice_success_title")
			);
		}		
	}
	public function EtatUpdate($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL){
        
        $data["id_pack_produit"] = $this->decryptId($infos["id_pack_produit"]);
        $data[$infos["field"]] = $infos[$infos["field"]];
               
        if ($r=$this->u($data)) {
            ATF::$msg->addNotice(loc::mt(ATF::$usr->trans("notice_update_success")));
        }
        return $r;
    }

}
?>