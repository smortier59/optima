<?
class adherent_cotisation extends classes_optima {
	function __construct() {
			parent::__construct();
			
			$this->colonnes['primary'] = array(
				 "id_adherent"
				,"date_adhesion"
				,"detail"
				,"date_reglement"
			);
			
			$this->colonnes['panel']['montantCotisation']= array(
				 "montant"
				,"montant_regle"
				,"solde"
			);
				
			$this->panels['primary'] = array('nbCols'=>2,'visible'=>true);
			$this->panels['montantCotisation'] = array('nbCols'=>3,'visible'=>true);	
			
			
			
			$this->fieldstructure();	
			$this->field_nom = "id_adherent";
	}
}
