<?
class magasin_cleodis extends classes_optima {
	public function __construct() {
		parent::__construct();
		$this->table = "magasin";
		$this->colonnes["fields_column"] = array(
			 'magasin.code'
			,"magasin.magasin"
			,"magasin.site_associe"
			,"magasin.id_societe"
			,"magasin.statut"
		);

		$this->fieldstructure();

		$this->foreign_key["id_societe"] = "societe";
	}
}

class magasin_cleodisbe extends magasin_cleodis { };
class magasin_bdomplus extends magasin_cleodis { };
class magasin_bdom extends magasin_cleodis { };
class magasin_boulanger extends magasin_cleodis { };

?>