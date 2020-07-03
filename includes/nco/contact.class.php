<?
/**
 * Classe contact
 * @package Optima
 * @subpackage AbsysTech
 */
require_once dirname(__FILE__)."/../contact.class.php";
class contact_2tmanagement extends contact {
	private $teamviewerMail=NULL;
	/**
	 * Constructeur
	 */

	public function __construct() {
		parent::__construct();


		$this->fieldstructure();

	}
};

?>
