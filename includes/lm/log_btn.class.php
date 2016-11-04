<?	
/** Classe Magasin
* @package Optima
* @subpackage Leroy Merlin
*/

class log_btn extends classes_optima {	

	function __construct($table_or_id=NULL) {
		$this->table = "log_btn";
		parent::__construct();
		$this->controlled_by = "societe";

		$this->addPrivilege("getForWidget");
	}

	/**
	 * Permet de recupérer les infos de la table log_btn pour construire le graph 
	 * @author Anthony LAHLAH <alahlah@absystech.fr>
	 * @return array
	*/
	public function getForWidget(){
		$date = date('Y-m-d',strtotime('- 7 day'));

		$q = "SELECT date, SUM(IF(big_offre=1,1,0)) as ok, SUM(IF(big_offre IS NULL,1,0)) as ko FROM log_btn WHERE date >= '".$date."%' GROUP BY YEAR(date), MONTH(date), DAY(date)";
		
		
		$r=ATF::db()->sql2array($q);
		log::logger($r, 'alahlah');
        return $r;

    } 
	
} 
?>