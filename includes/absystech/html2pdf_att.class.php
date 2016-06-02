<?php
/**
* html2pdf pour absystech
* @package Optima
* @subpackage Absystech
* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
*/
class html2pdf_att extends html2pdf_optima {
	/**
	* Constructeur par défaut
	*/
	public function __construct(){
		parent::__construct();
	}
	
	/**
	* Echéancier des tickets hotline par mois
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	*/
	public function hotline_echeancier($id){
		if(ATF::_r("date_debut")){
			$date_debut=ATF::_r("date_debut");
		}else{
			$date_debut="01-".date("m-Y");
		}
		
		if(ATF::_r("date_fin")){
			$date_fin=ATF::_r("date_fin");
		}else{
			$date_fin=date("t-m-Y");
		}
				
		ATF::gestion_ticket()->q->reset()->addField("hotline.id_hotline","id_hotline")
								  ->addField("gestion_ticket.id_facture","id_facture")
								  ->addField("gestion_ticket.solde","solde")
								  ->addField("gestion_ticket.libelle","libelle")
								  ->addField("gestion_ticket.nbre_tickets","nbre_tickets")
								  ->addField("gestion_ticket.date","date")
								  ->addField("hotline.id_contact","id_contact")
								  ->addField("hotline.id_user","id_user")
								  ->addField("hotline.hotline","hotline")
								  ->addField("hotline.facturation_ticket","facturation_ticket")
								  ->addCondition("gestion_ticket.id_societe",ATF::societe()->decryptId($id))
								  //->whereIsNotNull("id_hotline")
								  ->addCondition("gestion_ticket.date",util::formatDate($date_fin),"AND",false,"<=")
								  ->addCondition("gestion_ticket.date",util::formatDate($date_debut),"AND",false,">=")
								  ->addJointure("gestion_ticket","id_hotline","hotline","id_hotline")
								  ->addOrder("gestion_ticket.date");
		
		ATF::$html->assign("tickets",ATF::gestion_ticket()->sa());
		ATF::$html->assign("id_societe",$id);
		ATF::$html->assign("date_debut",$date_debut);
		ATF::$html->assign("date_fin",$date_fin);
		return " --encoding utf-8";	
	}
};

?>