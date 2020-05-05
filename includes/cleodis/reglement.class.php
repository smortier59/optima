<?
/** Classe reglement
* @package Optima
* @subpackage Cléodis
*/
require_once dirname(__FILE__)."/../reglement.class.php";
class reglement_cleodis extends reglement {
	public function __construct() {

		parent::__construct();
		$this->colonnes['fields_column']['fichier_joint'] = array("custom"=>true,"nosort"=>true,"type"=>"file","align"=>"center","width"=>70);

		$this->table = "reglement";

		$this->files["fichier_joint"] = array("type"=>"pdf","preview"=>true);

	}
 };

class reglement_cleodisbe extends reglement_cleodis { };
class reglement_cap extends reglement_cleodis { };

class reglement_bdomplus extends reglement_cleodis { };

class reglement_boulanger extends reglement_cleodis { };

class reglement_assets extends reglement_cleodis { };