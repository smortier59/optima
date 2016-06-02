<?
/** 
* Classe emailing_tracking, gère le tracking des liens traçables.
* @package Optima
* @author Quentin JANON <qjanon@absystech.fr>
*/
class emailing_tracking extends emailing {

	function __construct() { // PHP5
		parent::__construct();
		$this->table = __CLASS__;
		
		$this->colonnes['fields_column'] = array(
			'emailing_lien.emailing_lien'=>array('custom'=>true)
			,'emailing_contact'=>array('custom'=>true,"nosort"=>true)
			,'emailing_tracking.date'
			,'emailing_tracking.ip'
			,'emailing_tracking.host'
		);
		$this->fieldstructure();
		$this->helpMeURL = "http://wiki.optima.absystech.net/index.php/Traçabilité_des_emails";
	}
	
	/**
	* Surcharge du select-All
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){
		$this->q->reset("where");
		$this->q->reset("group");
		$this->q->addField("emailing_tracking.id_emailing_tracking");
		foreach ($this->colonnes['fields_column'] as $k=>$i) {
			if ($k=="emailing_contact") continue;
			$this->q->addField($k);
		}
		$this->q->from("emailing_tracking","id_emailing_job_email","emailing_job_email","id_emailing_job_email");
		$this->q->from("emailing_tracking","id_emailing_lien","emailing_lien","id_emailing_lien");
		$this->q->setStrict();
		
		if (ATF::_r('id_emailing_job')) {
			$this->q->where("emailing_job_email.id_emailing_job",ATF::emailing_job()->decryptID(ATF::_r('id_emailing_job')));	
		} elseif (ATF::_r('id') && ATF::_r('parent_name')=="emailing_job") {
			$this->q->where("emailing_job_email.id_emailing_job",ATF::emailing_job()->decryptID(ATF::_r('id')));	
		} elseif (ATF::_r('pager')) {
			$pager = explode("_",ATF::_r('pager'));
			$id = array_pop($pager);
			$this->q->where("emailing_job_email.id_emailing_job",$id);	
		}
		$r = parent::select_all($order_by,$asc,$page,$count);
		foreach ($r['data'] as $k=>$i) {
			$eje = ATF::emailing_job_email()->select($this->select($i['emailing_tracking.id_emailing_tracking'],'id_emailing_job_email'));
			$id_ec = ATF::emailing_liste_contact()->select($eje['id_emailing_liste_contact'],'id_emailing_contact');
			$r['data'][$k]['emailing_contact'] = ATF::emailing_contact()->nom($id_ec);
		}
		return $r;
	}

	public function tauxPenetration($id_emailing_job,$percent=true) {
		$this->q->reset()
				->addField("COUNT(*)","total")
				->addJointure("emailing_tracking","id_emailing_job_email","emailing_job_email","id_emailing_job_email")
				->where("id_emailing_job",$id_emailing_job)
				->addGroup("id_emailing_lien")
				->addGroup("id_emailing_liste_contact")
				->setCountOnly();
		$penetration = $this->sa();


		if ($percent) {
			ATF::emailing_job_email()->q->reset()->where("id_emailing_job",$id_emailing_job)->setCountOnly();
			$total = ATF::emailing_job_email()->sa();

			return number_format(($penetration/$total)*100);
		} else {
			return $penetration;
		}

	}
	
};
?>