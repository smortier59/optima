<?
/** Classe document_complementaire_a_signer
* @package Optima
* @subpackage Cléodis
*/
class document_complementaire_a_signer extends classes_optima
{
    public function __construct() {
        parent::__construct();
        $this->table = __CLASS__;
        $this->colonnes["fields_column"] = array(
            "document_complementaire_a_signer.id_affaire"
            ,"document_complementaire_a_signer.id_document_contrat"
        );
        $this->colonnes["primary"] = array(
            "id_affaire"
            ,"id_document_contrat"
        );
        $this->fieldstructure();

        $this->noTruncateSA = true;
		$this->selectAllExtjs=true;

    }


	/**
    * Retourne les infos de societe
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    */
    public function select_all($order_by=false,$asc='desc',$page=false,$count=false) {
		$this->q->addJointure("document_complementaire_a_signer","id_document_contrat","document_contrat","id_document_contrat");
		return parent::select_all($order_by,$asc,$page,$count);
	}

}