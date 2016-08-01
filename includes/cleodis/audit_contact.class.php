<?  
/** Classe audit_contact
* @package Optima
* @subpackage Cleodis
*/
class audit_contact extends classes_optima {
    
    function __construct() {
        parent::__construct(); 
        $this->table = "audit_contact";      

        $this->fieldstructure();           
    }
}