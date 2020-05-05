<?
/** Classe produit_puissance
* @package Optima
* @subpackage Cleodis
*/
require_once dirname(__FILE__)."/../categorie.class.php";
class categorie_cleodis extends categorie {

    function __construct() {
        parent::__construct();
        $this->table = "categorie";
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
    /**
     * fonction qui retourne la liste des categories avec la gestion de l'autocomplete
     * @author Cyril CHARLIER <ccharlier@absystech.fr>
     * @param  $get  paramètres get
     * @param  $post parametre envoyées en post inutile ici
     * @return [array] categories [description]
     */
    public function _ac($get,$post){
        $length = 25;
        $start = 0;

        $this->q->reset();

        // On ajoute les champs utiles pour l'autocomplete
        $this->q->addField("id_categorie")->addField("categorie")->addOrder('categorie','ASC');

        if ($get['q']) {
            $this->q->setSearch($get["q"]);
        }
        $this->q->setLimit($length,$start)->setPage($start/$length);

        return $this->select_all();
    }
};

class categorie_cleodisbe extends categorie_cleodis {}
class categorie_bdomplus extends categorie_cleodis {}
class categorie_boulanger extends categorie_cleodis {}
class categorie_assets extends categorie_cleodis {}

?>