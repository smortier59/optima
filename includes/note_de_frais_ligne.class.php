<?
/**
* @package Optima
*/
class note_de_frais_ligne extends classes_optima {

	private $idSmortier = 3;

	function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		$this->controlled_by = "note_de_frais";

		$this->colonnes['fields_column'] = array(
			'note_de_frais_ligne.id_note_de_frais'=>array("width"=>80)
			,'note_de_frais_ligne.date'=>array("width"=>100)
			,'note_de_frais_ligne.objet'
			,'note_de_frais_ligne.id_societe'
			,'note_de_frais_ligne.id_frais_kilometrique'=>array("width"=>100)
			,'note_de_frais_ligne.montant'=>array("width"=>80,"aggregate"=>array("min","avg","max","sum"),"align"=>"right","renderer"=>"money","nosearch"=>true)
			,'note_de_frais_ligne.etat'=>array("width"=>30,"renderer"=>"etat")
			,'validation'=>array("custom"=>true,"nosort"=>true,"width"=>60,"renderer"=>"ndflValidation")
		);
		$this->colonnes['primary'] = array(
			'id_note_de_frais'
			,'date'
			,'objet'
			,'id_societe'
			,'montant'
			,'etat'
		);
		$this->no_insert = true;
		$this->no_update = true;
		$this->fieldstructure();
		$this->addPrivilege("valid","update");
		$this->addPrivilege("refus","update");
	}
	
	/**
    * N'affiche que les notes de frais de l'utilisateur trié par date + l'aggrégat
    * @author Quentin JANON <qjanon@absystech.fr>
    */
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){
		$return = parent::select_all($order_by?$order_by:$this->table.".date",$asc,$page,$count);
		foreach ($return['data'] as $k=>$i) {
			$return['data'][$k]['canValid'] = $this->canValid($i['note_de_frais_ligne.id_user_fk']);
		}
		
		return $return;
	}
	
	/**
    * Retourne VRAI si l'utilisateur peut accéder aux icônes de validation de la note de frais, sinon FALSE
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 09-03-2011
	* @param string $field
	* @param array &$s La session
	* @param array &$request Paramètres disponibles (clés étrangères)
	* @return string
    */   	
	public function canValid($idUser){
		if (ATF::$usr->getID()==$this->idSmortier) {
			return true;
		}
		if (ATF::$usr->getID()==ATF::user()->select($idUser,'id_superieur')) {
			return true;
		}
		return false;
	}

	/** 
	* Valide une ligne de note de frais
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 22-03-2011
	* @return boolean 
	*/
	public function valid($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		$u = array(
			"id_note_de_frais_ligne"=>$infos['id_note_de_frais_ligne']
			,"etat"=>"ok"
			,"raison"=>"Validée par '".ATF::user()->nom(ATF::$usr->getID())."' le '".date("d-m-Y")."'"
		);
		$this->update($u);
		
		return true;
	}
	
	/** 
	* Refuse une ligne de note de frais
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 22-03-2011
	* @return boolean 
	*/
	public function refus($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		$u = array(
			"id_note_de_frais_ligne"=>$infos['id_note_de_frais_ligne']
			,"etat"=>"nok"
			,"raison"=>$infos['raison']
		);
		$this->update($u);
		
		return true;
	}
	
};
?>