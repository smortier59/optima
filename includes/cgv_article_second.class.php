<?
/** Classe cgv_article_second
* @package Optima
*/
class cgv_article_second extends classes_optima{
	/*--------------------------------------------------------------*/
	/*                   Attributs                                  */
	/*--------------------------------------------------------------*/
	/*--------------------------------------------------------------*/
	/*                   Constructeurs                              */
	/*--------------------------------------------------------------*/
	public function __construct(){
        parent::__construct();
        $this->table=__CLASS__;
        $this->colonnes['fields_column']=array(
            'cgv_article_second.cgv_article_second'
            ,"cgv_article_second.resume"
            ,'cgv_article_second.id_cgv_article'
        );
        $this->colonnes['primary']=array(
            "id_cgv_article"
            ,"cgv_article_second"
            ,"resume"
        );
        
        $this->fieldstructure();
	}

	/*--------------------------------------------------------------*/
	/*                   Méthodes                                   */
	/*--------------------------------------------------------------*/	
};
?>