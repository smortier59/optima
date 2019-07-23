<?	
/** Classe loyer
* @package Optima
* @subpackage Cleodis
*/
class loyer extends classes_optima {
	
	function __construct() {
		parent::__construct(); 
		$this->table = __CLASS__;
			$this->colonnes['fields_column'] = array( 
			'loyer.duree'=>array("align"=>"right","suffix"=>"x")
			,'loyer.frequence_loyer'
			,'loyer.loyer'=>array("aggregate"=>array("min","max"),"renderer"=>"money")
			,'loyer.assurance'=>array("aggregate"=>array("min","max"),"renderer"=>"money")
			,'loyer.frais_de_gestion'=>array("aggregate"=>array("min","max"),"renderer"=>"money")
		);

		$this->colonnes['primary'] = array(
			"duree"
			,"loyer"
			,"frequence_loyer"
			,"assurance"
			,"frais_de_gestion"
			,"id_affaire"
		);
		
		$this->colonnes['bloquees']['insert'] =  array('id_devis')	;
		$this->colonnes['ligne'] =  array( 	
			"loyer.loyer"
			,"loyer.duree"
			,"loyer.assurance"
			,"loyer.frais_de_gestion"
			,"loyer.frequence_loyer"
		);
		
		$this->controlled_by = "devis";
		$this->no_insert=true;
		$this->no_update=true;
		$this->no_delete=true;
		$this->fieldstructure();	
		$this->selectAllExtjs=true; 
	}
	
	/**
    * Permet d'avoir les lignes de devis dans l'ordre d'insertion
    * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
    */ 
	function select_all($order_by=false,$asc='asc',$page=false,$count=false,$parent=false){
		return parent::select_all($order_by,$asc,$page,$count);
	}

	/**
    * Renvoi la durée total d'un devis en mois
    * @author Quentin JANON <qjanon@absystech.fr>
    * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @date 13-01-2011
	* @param array $l Tableau contenant tous les loyers du devis
	* @return Durée totale de la location pour un devis
    */ 
	public function dureeTotal($id_affaire) {
		foreach($this->ss("id_affaire",$id_affaire) as $key=>$item){
			if($item["frequence_loyer"]=="an"){
				$duree+=$item["duree"]*12;
			}elseif($item["frequence_loyer"]=="semestre"){
				$duree+=$item["duree"]*6;
			}elseif($item["frequence_loyer"]=="trimestre"){
				$duree+=$item["duree"]*3;
			}elseif($item["frequence_loyer"]=="mois"){
				$duree+=$item["duree"];
			}
		}
		return $duree;
	}

	/**
    * Renvoi la durée total d'un devis
    * @author Quentin JANON <qjanon@absystech.fr>
	* @param array $l Tableau contenant tous les loyers du devis
	* @return Durée totale de la location pour un devis
    */ 
	public function dureeTotalBrut($id_affaire) {
		foreach($this->ss("id_affaire",$id_affaire) as $key=>$item){
			$duree += $item["duree"];
		}
		return $duree;
	}

	/**
    * Renvoi le prix total pour une affaire
    * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @date 13-01-2011
	* @param array $l Tableau contenant tous les loyers du devis
	* @return Durée totale de la location pour un devis
    */ 
	public function prixTotal($id_affaire) {
		$total=0;
		foreach($this->ss("id_affaire",$id_affaire) as $key=>$item){
			$total+=(($item["loyer"]+$item["frais_de_gestion"]+$item["assurance"])*$item["duree"]);		
		}
		return $total;
	}
	
};