<?
/** Classe cgv_article
* @package Optima
*/
class cgv_article extends classes_optima{
	/*--------------------------------------------------------------*/
	/*                   Attributs                                  */
	/*--------------------------------------------------------------*/
	/*--------------------------------------------------------------*/
	/*                   Constructeurs                              */
	/*--------------------------------------------------------------*/
	public function __construct(){
		parent::__construct();
		$this->table=__CLASS__;
		$this->colonnes['fields_column']=array('cgv_article.cgv_article');
		$this->colonnes['primary']=array("id_cgv","cgv_article");
		
		$this->fieldstructure();

        $this->onglets = array(
            "cgv_article_second"
        );
	}

	/*--------------------------------------------------------------*/
	/*                   M�thodes                                   */
	/*--------------------------------------------------------------*/	
};
?>