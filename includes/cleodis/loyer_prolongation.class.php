<?	
/** Classe loyer
* @package Optima
* @subpackage Cleodis
*/
class loyer_prolongation extends classes_optima {
	
	function __construct() {
		parent::__construct(); 
		$this->table = "loyer_prolongation";
		$this->colonnes['fields_column'] = array( 
			'loyer_prolongation.duree'=>array("align"=>"right")
			,'loyer_prolongation.frequence_loyer'=>array("align"=>"right","suffix"=>"x")
			,'loyer_prolongation.loyer'=>array("aggregate"=>array("min","max"),"align"=>"right","renderer"=>"money")
			,'loyer_prolongation.assurance'=>array("aggregate"=>array("min","max"),"align"=>"right","renderer"=>"money")
			,'loyer_prolongation.frais_de_gestion'=>array("aggregate"=>array("min","max"),"align"=>"right","renderer"=>"money")
			,'loyer_prolongation.date_debut'=>array("align"=>"right")
			,'loyer_prolongation.date_fin'=>array("align"=>"right")
		);
				
		$this->no_insert=false;
		$this->fieldstructure();
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
