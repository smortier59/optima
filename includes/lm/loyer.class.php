<?
require_once dirname(__FILE__)."/../cleodis/loyer.class.php"; 
class loyer_lm extends loyer { 

	/**
    * Permet d'avoir les lignes de devis dans l'ordre d'insertion
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    */ 
	function select_all($order_by=false,$asc='asc',$page=false,$count=false,$parent=false){
		return parent::select_all($order_by,$asc,$page,$count);
	}

	/**
    * Renvoi la durée total d'un devis en mois
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @date 02-06-2016
	* @param array $l Tableau contenant tous les loyers du devis
	* @return Durée totale de la location pour un devis
    */ 
	public function dureeTotal($id_affaire) {
		foreach($this->ss("id_affaire",$id_affaire) as $key=>$item){
			if($item["nature"] !== "prolongation"){
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
		}
		return $duree;
	}

	/**
    * Renvoi le prix total pour une affaire
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @date 02-06-2016
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
