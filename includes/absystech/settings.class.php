<?
/**
* Classe de configuration
* @package ATF
*/
require_once dirname(__FILE__)."/../settings.class.php";
class settings_absystech extends settings {
	public function __construct() {
		parent::__construct();

	}


};
class settings_att extends settings_absystech {
	public function __construct() {
		parent::__construct();
	}
};
?>
