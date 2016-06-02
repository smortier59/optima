<?  
/** Classe mandat_contact
* @package Optima
* @subpackage Cleodis
*/
class mandat_contact extends classes_optima {
    
    function __construct() {
        parent::__construct(); 
        $this->table = "mandat_contact";      

        $this->fieldstructure();           
    }
}