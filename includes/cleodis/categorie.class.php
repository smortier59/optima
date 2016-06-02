<?  
/** Classe produit_puissance
* @package Optima
* @subpackage Cleodis
*/
class categorie extends classes_optima {
    
    function __construct() {
        parent::__construct(); 
        $this->table = __CLASS__;
        $this->colonnes['fields_column'] = array( 
             'categorie.categorie'
        );

        $this->colonnes['primary'] = array(
            "categorie"
        );

        $this->fieldstructure();    
        $this->onglets = array(
            'sous_categorie'=>array('opened'=>true)
        );
        $this->controlled_by = "produit";
    }
};

?>