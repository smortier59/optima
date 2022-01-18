<?
/**
* @package Optima
*/
class compte_absystech extends classes_optima{

	function __construct(){ //PHP5
		parent::__construct();
		$this->table=__CLASS__;
		$this->colonnes['fields_column']=array('compte_absystech.compte_absystech',
												'compte_absystech.type','compte_absystech.code', "compte_absystech.etat");
		$this->colonnes['primary']=array("compte_absystech",
											"type","code", "etat");

		$this->fieldstructure();
		$this->addPrivilege("getCompteActif");
	}

	public function getCompteActif(){
		$this->q->reset()->addCondition("compte_absystech.etat", "actif");
		return parent::autocomplete($infos,false);
	}

	/**
	 * fonction _GET afin
	 * @param  $get obligatoire mais inutilisé
	 * @param  $post obligatoire mais inutilisé
	 * @author  Cyril Charlier <ccharlier@absystech.fr>
	 * @return array retourne tous les elements
	 */
	function _GET($get,$post){
 		$colsData = array(
	      "id_compte_absystech"=>array(),
	      "compte_absystech"=>array()
	    );
	    $this->q->reset()->where("compte_absystech.etat", "actif");
	    $this->q->addField($colsData);
	    $data = $this->select_all();

		return $data;

	}
};
?>