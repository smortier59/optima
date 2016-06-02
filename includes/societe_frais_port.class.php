<?
/**
* @package Optima
*/
class societe_frais_port extends classes_optima {
	function __construct() {
		parent::__construct();
		//$this->quick_insert = array('devis' => 'devis');
		$this->colonnes['fields_column'] = array(
			'borne1'=>array("align"=>"right","renderer"=>"money")
			,'borne2'=>array("align"=>"right","renderer"=>"money")
			,'prix'=>array("align"=>"right","renderer"=>"money")
		);
		$this->table = __CLASS__;
		$this->fieldstructure();
		$this->addPrivilege("frais_port");
		$this->addPrivilege("getQuickTips");
	}
	
	/**
    * Retourne les frais de ports en fonction du poids et du fournisseur
    * @author Fanny Declerck <fdeclerc@absystech.fr>
    * @param decimal poids
    * @return decimal prix 
    */    
	function frais_port($infos) {
		if($infos["poids"]){
			$infos["poids"]=str_replace(" ","",$infos["poids"]);
			$this->q->reset()
					->addCondition("borne1",$infos["poids"],"AND",false,"<")
					->addCondition("borne2",$infos["poids"],"AND",false,">=")
					->addField("prix")
					->setStrict()
					->setDimension("cell");
			
			if ($frais_port=$this->select_all()) {
				return $frais_port;
			}
		}
		return 0.00;
	}
	
	/**
    * Renvoi le texte de la quickTips pour les frais de ports
    * @author Quentin JANON <qjanon@absystech.fr>
    */    
	public function getQuickTips(&$infos) {
		$infos['display'] = true;
		$this->q->reset()->addOrder("borne1");
		$r = "<ul>";
		foreach ($this->sa() as $k=>$i) {
			$r .= "<li>De ".$i['borne1']."Kg a ".$i['borne2']."Kg : <b>".$i['prix']."€</b></li>";
		}
		$r .= "</ul>";
		return $r;	
	}

};
?>