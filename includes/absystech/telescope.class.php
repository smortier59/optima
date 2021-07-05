<?
/**
* Classe telescope
* @package Optima
* @subpackage AbsysTech
*/
class telescope extends classes_optima {
	/**
	* Constructeur !
	*/
	public function __construct() {
		parent::__construct();

		$this->table = "telescope";
		$this->colonnes['fields_column'] = array(
			'telescope.telescope',
			'telescope.codename',
			'telescope.url',
			'telescope.theme',
			'telescope.actif'
		);

		$this->fieldstructure();

	}

	/**
	 * [_getTelescopeInfos description]
	 * @author : Morgan FLEURQUIN <mfleurquin@absystech.fr>
	 * @param  Array $get
	 *         			url -> URL du telescope
	 * @return array    infos telescope
	 */
	public function _getTelescopeInfos() {
		ATF::telescope()->q->reset()->where("codename", ATF::$codename);
		return ATF::telescope()->select_row();
	}

	public function _getTelescopesActif() {
		ATF::telescope()->q->reset()->where("actif", "oui");
		return ATF::telescope()->select_all();
	}
}