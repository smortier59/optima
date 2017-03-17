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


		$this->colonnes['primary']=array('etat',
										 'type_pack',
										 'id_produit',
										 'type_contrat',
										 'id_document_contrat',
										 'id_rayon',
										 'url_redirection',
										 'message_redirection'
										);

		$this->colonnes["panel"]["affichage_lm"] = array('ref_lm_principale',
														 'libelle',
														 'popup'
														);

		$this->colonnes["panel"]["affichage_magasin"] = array('libelle_ecran_magasin',
															  'type_pack_magasin');

		$this->colonnes["panel"]["affichage_site_souscription"] = array('description',
																		'service_inclus',
																		'fin_formulaire'
																);



		$this->field_nom = "%id_pack_produit% - %libelle%";

		$this->foreign_key['id_produit'] =  "produit";

		$this->fieldstructure();

		$this->files["photo"] = array("type"=>"png","convert_from"=>array("jpg","png","gif"),"select"=>true);
		$this->files["photo1"] = array("type"=>"png","convert_from"=>array("jpg","png","gif"),"select"=>true);
		$this->files["photo2"] = array("type"=>"png","convert_from"=>array("jpg","png","gif"),"select"=>true);


		$this->panels['primary'] = array('nbCols'=>1,'visible'=>true);
		$this->panels['affichage_lm'] = array('nbCols'=>1);
		$this->panels['affichage_magasin'] = array('nbCols'=>1);
		$this->panels['affichage_site_souscription'] = array('nbCols'=>1);


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