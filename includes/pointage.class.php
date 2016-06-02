<?
/**
* Gestion du pointage
* @package Optima
*/
class pointage extends classes_optima {
	/**
	* Contructeur
	*/
	public function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		$this->fieldstructure();
	}	

	/** 
	* Retourne un tableau avec le nombre de jours du mois
	* @param : date date : date contenant le mois et l'annee
	* @return array le nombre de jour du mois
	*/ 
	public function nbJour($date) {
		for ($i=1; $i<=31; $i++) {
			if ($i<10) $i = "0".$i;
			$temp =  "$date-$i";
			
			if (date("Y-m-d", strtotime($temp)) == $temp){		
				$return[$i] = $i;
			} 
			else{	
				continue;	
			}
		}
		return $return;
	}
	
	/** 
	* Prédicat qui indique si on se trouve sur sa feuille de pointage
	* Il y a un effet de bord : une notice est lancée
	* @param int $id_user l'identifiant de l'utilisateur
	* @author : Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	*/
	private function isMySheet($id_user){
		$id_user=$this->decryptId($id_user);
		if(ATF::$usr->getID()!=$id_user){
			ATF::$msg->addNotice(ATF::$usr->trans("pointage_not_self",$this->table));
			return false;
		}else{
			return true;
		}
	}
		

};
?>