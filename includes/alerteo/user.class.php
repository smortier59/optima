<?
/**
* @package Optima
* @subpackage Alerteo
*/
require_once __ATF_PATH__."includes/user.class.php";
class user_alerteo extends user {
	public function __construct($id_user=NULL) {
		//Appel du constructeur de classes
		parent::__construct($id_user);
		
		unset($this->colonnes['panel']['parametres']);
		unset($this->colonnes['panel']['telephonie']);
		
		try {
			$this->fieldstructure();	/*		log::logger($this->colonnes['fields_column'],'qjanon');	*///		$this->tables_de_jointure = array("domaine");
		} catch (error $e) {
			// Si pas de table user, impossible de se logger, mais on va créer un "user::$logged=false"
		}
	}
}
?>