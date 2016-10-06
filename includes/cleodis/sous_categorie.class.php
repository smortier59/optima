<?	
/** Classe devis
* @package Optima
* @subpackage Cleodis
*/
class sous_categorie extends classes_optima {
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

};

class sous_categorie_cleodisbe extends sous_categorie { };
class sous_categorie_cap extends sous_categorie { };
?>