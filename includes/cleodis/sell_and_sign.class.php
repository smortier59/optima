
<?
/**
* Sell and Sign
* @package Optima
*/
class sell_and_sign extends classes_optima {
	/**
	* Constructeur
	*/
	public function __construct() {
		parent::__construct();
		$this->controlled_by = 'affaire';
		$this->table = "sell_and_sign";
	}	
};

class sell_and_sign_cleodisbe extends sell_and_sign { };
class sell_and_sign_cap extends sell_and_sign { };

class sell_and_sign_bdomplus extends sell_and_sign { };
class sell_and_sign_bdom extends sell_and_sign { };
class sell_and_sign_boulanger extends sell_and_sign { };