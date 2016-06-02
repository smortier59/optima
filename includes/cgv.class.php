<?
/** Classe cgv
* @package Optima
*/
class cgv extends classes_optima{
	/*--------------------------------------------------------------*/
	/*                   Attributs                                  */
	/*--------------------------------------------------------------*/
	/*--------------------------------------------------------------*/
	/*                   Constructeurs                              */
	/*--------------------------------------------------------------*/
	public function __construct(){
		parent::__construct();
		$this->table=__CLASS__;
		$this->colonnes['fields_column'] = array('cgv.cgv');
		$this->colonnes['primary']=array("cgv","id_societe");
		
		$this->fieldstructure();
        
        $this->onglets = array(
            "cgv_article"
        );
	}
	/*--------------------------------------------------------------*/
	/*                   M�thodes                                   */
	/*--------------------------------------------------------------*/	
};
?>