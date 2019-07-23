<?
/** Classe site_offre
* @package Optima
* @subpackage Cleodis
*/
class site_offre extends classes_optima {
    
    function __construct() {
        parent::__construct(); 
        $this->table = "site_offre";
        $this->colonnes['fields_column'] = array(              
            'site_offre.site_offre'
            ,'site_offre.texte_offre'            
        );

        $this->colonnes['primary'] = array(            
             "site_offre"
            ,"texte_offre"
        );

        $this->fieldstructure();  
        
    }
	
	


};
