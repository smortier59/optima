<?
/** 
* Classe emailing_job_sms, gère les SMS envoyé par un job
* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
* @package Optima
* @todo Refactoring ATF5
*/
class emailing_job_sms extends emailing {
	function __construct() { // PHP5
		parent::__construct();
		
		$this->table = __CLASS__;
		
		$this->colonnes['fields_column'] = array(
			'emailing_job_sms.id_emailing_job'
			,'emailing_contact'=>array('custom'=>true)
			,'emailing_job_sms.date'=>array("width"=>100,"align"=>"center")
			,'emailing_job_sms.tracking'=>array("width"=>100,"align"=>"center")
			,'emailing_job_sms.last_tracking'=>array("width"=>100,"align"=>"center")
			,'emailing_job_sms.retour'=>array("width"=>100,"align"=>"center")
		);		
		$this->fieldstructure();
	}

	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){
		$this->q->addField("emailing_contact.gsm","emailing_contact")
				->from("emailing_job_sms","id_emailing_liste_contact","emailing_liste_contact","id_emailing_liste_contact")
				->from("emailing_liste_contact","id_emailing_contact","emailing_contact","id_emailing_contact");
					
		return parent::select_all($order_by,$asc,$page,$count);
	}
	
	
	/**
	* Met a jour un enregistrement et surtout les erreurs qui lui sont attachés
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 17-11-2010
	* @return bool Cf Update
	*/
	public function updateError($d) {
		$idj = $d["id_emailing_job"];
		$idlc = $d["id_emailing_liste_contact"];
		if (!$idj || !$idlc) return false;
		unset($d["id_emailing_job"],$d["id_emailing_liste_contact"]);

		$this->q->reset()
					->Where("MD5(id_emailing_job)",$idj)
					->Where("MD5(id_emailing_liste_contact)",$idlc)
					->addValues($d);
		return ATF::db()->update($this);
	}

};
?>