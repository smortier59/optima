<?	
/** 
* Classe facture
* @package Optima
* @subpackage Absystech
*/
require_once dirname(__FILE__)."/../facture_ligne.class.php";
class facture_ligne_absystech extends facture_ligne {	
	function __construct() {
		parent::__construct(); 
	}
	
	
	/** @author Morgan FLEURQUIN	<mfleurquin@absystech.fr> */
	public function can_update(){
		throw new errorATF("Pour modifier une ligne de facture, il faut modifier dans la facture !!!");					
		return false;
	}
	
	/**
	* @author Morgan Fleurquin <mfleurquin@absystech.fr>
	*/
	public function can_insert(){
		throw new errorATF("Pour inserer une ligne de facture, il faut modifier dans la facture !!!");
		return false;	
	}
	
	/**
	* @author Morgan Fleurquin <mfleurquin@absystech.fr>
	*/
	/*public function can_delete(){
		throw new errorATF("Pour supprimer une ligne de facture, il faut modifier dans la facture !!!");
		return false;	
	}*/
	
	
}