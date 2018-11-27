<?php
/** 
* Classe module : permet de gérer les news de l'application
* @package ATF
*/
require_once __ATF_PATH__."includes/news.class.php";
class news_absystech extends news {
	/**
	* Constructeur
	*/
	public function __construct() {
		parent::__construct();
		
		/*-----------Colonnes insert/update/select - informations principales-----------------------*/
		$this->colonnes['primary'] = array(
			"news"
			,"date"
			,"redacteur"
			,"codename"=>array(
				"custom"=>true
				,"xtype"=>"combo"
				,"data"=>ATF::db()->recup_codename()
				,"libNull"=>"Tous"
			)
		);

		$this->fieldstructure();
	}


	public function getConseils(){
		$q = "SELECT details FROM news WHERE type = 'tips' ORDER BY RAND() LIMIT 1";
		ATF::define_db("db","optima");
		$tips =  ATF::db()->ffc($q);
		ATF::define_db("db","optima_absystech");
		return $tips;
	}
	
	
};
?>