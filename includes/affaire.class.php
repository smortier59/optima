<?
/** Classe affaire
* @package Optima
* @subpackage Cléodis
*/
class affaire extends classes_optima {
	/**
	* Retourne la marge effectuée entre le début de l'année passée en paramètre et NOW()
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $offset Décalage de l'année demandé
	* @return int
	*/
	public function getMargeTotaleDepuisDebutAnnee($offset=0,$date=NULL){
		$date=($date?strtotime($date):time());
		$annee = date("Y",$date) + $offset;
		$query="SELECT
						SUM(a.prix)
					FROM
					(
						SELECT date, -prix_achat as prix FROM commande
						UNION SELECT date, prix FROM facture
					) AS a
		 			WHERE
						a.date BETWEEN '".$annee."-01-01' AND '".$annee.date("-m-d",$date)."'";
		return ATF::db($this->db)->ffc($query);
	}

	/**
	* Retourne la différence entre la marge effectuée entre le début de l'année passée en paramètre et NOW() avec l'année précédente
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return int
	*/
	public function getMargeTotaleDepuisDebutAnneeDifferenceAnneePrecedente(){
		return $this->getMargeTotaleDepuisDebutAnnee()-$this->getMargeTotaleDepuisDebutAnnee(-1);
	}



};
?>