<?
require_once dirname(__FILE__)."/../societe.class.php";
/**  
* @package Optima
* @subpackage nco
*/
class societe_nco extends societe {
	/**
	* Constructeur par défaut
	*/
	public function __construct() {
		parent::__construct();
		$this->table = "societe";
		
		unset($this->colonnes['panel']['facturation_fs']);

		// Structure et secteur
		$this->colonnes['panel']['structure_secteur_fs'] = array(
			"structure_societe"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
				"structure"
				,"activite"
				  ,"naf"
			))
			,"information_financiere"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
				"capital"
				,"ca"
			))
			,"taille"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
				"effectif"
				,"nb_employe"
			))
			,"id_secteur_geographique"
		);
		$this->panels['structure_secteur_fs'] = array("visible"=>true,'nbCols'=>1,'isSubPanel'=>true);

		$this->fieldstructure();

	}


};