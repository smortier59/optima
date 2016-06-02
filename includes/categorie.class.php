<?
/** Classe categorie
* @package Optima
*/
class categorie extends classes_optima{
	/*--------------------------------------------------------------*/
	/*                   Attributs                                  */
	/*--------------------------------------------------------------*/
	/*--------------------------------------------------------------*/
	/*                   Constructeurs                              */
	/*--------------------------------------------------------------*/
	function __construct(){ 
		parent::__construct();
		$this->table=__CLASS__;
		$this->colonnes['fields_column']=array('categorie.categorie');
		$this->colonnes['primary']=array('categorie');
		
		$this->fieldstructure();
		
	}
	/*--------------------------------------------------------------*/
	/*                   Méthodes                                   */
	/*--------------------------------------------------------------*/	
};
?>