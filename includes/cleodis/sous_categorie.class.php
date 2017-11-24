<?
/** Classe devis
* @package Optima
* @subpackage Cleodis
*/
require_once dirname(__FILE__)."/../sous_categorie.class.php";
class sous_categorie_cleodis extends sous_categorie {
	// Mapping prévu pour un autocomplete sur produit
	public static $autocompleteMapping = array(
		array("name"=>'id', "mapping"=>0),
		array("name"=>'nom', "mapping"=>1),
		array("name"=>'categorie', "mapping"=>2)
	);

	function __construct() {
		parent::__construct();
		$this->table = "sous_categorie";
		$this->colonnes['fields_column'] = array(
			 'sous_categorie.sous_categorie'
		);

		$this->colonnes['primary'] = array(
			"sous_categorie"
		);

		$this->fieldstructure();
		$this->onglets = array(
			'produit'=>array('opened'=>true)
		);
		$this->controlled_by = "categorie";
	}

	/**
    * Surcharge de la méthode autocomplete pour faire apparaître sous_catégorie et catégorie
    * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $infos
    * @return int  id si enregistrement ok
    */
	function autocomplete($infos) {

			// Récupérer les produits
			$this->q->reset()
				->addJointure("sous_categorie","id_categorie","categorie","id_categorie")
				->addField("id_sous_categorie")
				->addField("sous_categorie.sous_categorie")
				->addField("categorie.categorie");
		return parent::autocomplete($infos,false);
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
        $this->q->addField("id_sous_categorie")->addField("sous_categorie")->where("id_categorie",$get["id"])->addOrder('sous_categorie','ASC');

        if ($get['q']) {
            $this->q->setSearch($get["q"]);
        }
        $this->q->setLimit($length,$start)->setPage($start/$length);

        return $this->select_all();
    }
};

class sous_categorie_cleodisbe extends sous_categorie { };
class sous_categorie_cap extends sous_categorie { };
?>