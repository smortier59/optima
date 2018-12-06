<?
/** Classe Magasin
* @package Optima
* @subpackage Leroy Merlin
*/

class magasin extends classes_optima {

    function __construct($table_or_id=NULL) {
            $this->table = "magasin";
            parent::__construct();
            $this->colonnes['fields_column'] = array(
                     'magasin.magasin',
                     'magasin.code',
                     'magasin.site_associe',
                     'magasin.id_societe'

            );
            $this->fieldstructure();
    }

}
?>
