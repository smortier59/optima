<?
/**
* @package Optima
*/
class pdf extends FPDF_ATF_BOOKMARK {
	
	// Affichage ou non du pied de page automatique
	private $noFooter = false;
	// Affichage ou non de l'entête automatique
	private $noHeader = false;
	
	/* Active le Footer
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 05-01-2011
	*/
	public function setFooter() {
		$this->noFooter = false;	
	}
		 
	/* Désactive le Footer
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 05-01-2011
	*/
	public function unsetFooter() {
		$this->noFooter = true;	
	}
		
	/* Renvoi la valeur pour le Footer
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 05-01-2011
	*/
	public function getFooter() {
		return $this->noFooter;	
	}
		
	/* Active le Header
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 05-01-2011
	*/
	public function setHeader() {
		$this->noHeader = false;	
	}
		
	/* Désactive le Header
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 05-01-2011
	*/
	public function unsetHeader() {
		$this->noHeader = true;	
	}
		
	/* Renvoi la valeur pour le Header
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 05-01-2011
	*/
	public function getHeader() {
		return $this->noHeader;	
	}
		
	function pied($infos) {
		$this->setautopagebreak(false,'1');
		$this->sety(287);
		$this->setfont('arial','I',8);
		$this->multicell(0,3,strtoupper($infos["societe"])." - ".$infos['structure'].($infos["capital"] ? " au capital de ".number_format($infos["capital"],2,',','.')." EUR" : NULL).($infos["siret"] ? " SIRET : ".$infos["siret"] : NULL),0,'C');
		$this->multicell(0,3,"Siège social : ".$infos['adresse']." - ".$infos['cp']." ".$infos['ville']." - ".ATF::pays()->select($infos['id_pays'],"pays")." - ".$infos['email']." - Tél : ".$infos['tel'].($infos['fax'] ? " - Fax : ".$infos['fax'] : NULL),0,'C');
	}
	
		
	/* Permet dde faire unt rait très léger au endroit où il faut plier le courrier pour l'enveloppe. Par défaut, il s'agit d'un pliage en 3
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 17-10-2012
	public function repereDePliage($nb=3) {
		switch ($nb) {
			default:
				$this->setDrawColor(125,125,125);
				// Première ligne
				$this->line(0,98,5,98);
				$this->line(205,98,210,98);
				//Seconde ligne
				$this->line(0,202,5,202);
				$this->line(205,202,210,202);
			break;
		}
	}
	*/
}

?>