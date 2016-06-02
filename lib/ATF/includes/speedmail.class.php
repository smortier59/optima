<?php
/** Classe Activité
* @package ATF
*/
class speedmail extends classes_optima {
	public function __construct(){
		parent::__construct();
		$this->table=__CLASS__;
		$this->db='main';
	}		
	
//	/** 
//	* Création d'un speedmail
//	* @author Yann GAUTHERON <ygautheron@absystech.fr>
//	* @param $infos Informations du speedmail envoyé
//	*/
//	public function insert($infos) {
//		$infos["creation"] = date("Y-m-d H:i:s",time());
//		$infos["website_codename"] = ATF::$codename;
//		foreach($infos as $key => $item) {
//			$keys[] = "`".$key."`";
//		}
//		foreach($infos as $key => $item) {
//			if ($item!=="") {
//				if (is_array($item)) {
//					$item=implode(",",$item);
//				}
//				$infos[$key] = "\"".$item."\"";
//			} else {
//				$infos[$key] = "null";
//			}
//		}
//		return parent::insert($infos);
//	}
//	
//	/** 
//	* Suppression d'un job
//	* @author Yann GAUTHERON <ygautheron@absystech.fr>
//	* @param $infos Informations du speedmail envoyé
//	*/
//	public function delete($infos) {
//		$this->q->reset()
//			->where("id_emailing_job",$infos["id_emailing_job"])
//			->where("website_codename",ATF::$codename);
//		return parent::delete(); 
//	}
	
	/** 
	* On incrémente le compteur d'email envoyés
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param $id_emailing_job Informations du job speedmail envoyé
	*/
	public function send($id_emailing_job) {
		$this->q->reset()
			->where("id_emailing_job",$id_emailing_job)
			->where("website_codename",ATF::$codename);
		if (!$this->select_row()) {
			$j = ATF::emailing_job()->select($id_emailing_job,'depart');
			$i = array(
				"website_codename"=>ATF::$codename
				,"id_emailing_job"=>$id_emailing_job
				,"creation"=>date("Y-m-d H:i:s")
				,"depart"=>$j
			);
			$this->insert($i);
		}
		
		$this->q->addValues(array("nb"=>array("value"=>"`nb`+1")));
		return ATF::db($this->db)->update($this);
	}
	
	/** 
	* Enregistrement de la date de fin du job
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param $id_emailing_job Informations du job speedmail envoyé
	*/
	public function done($id_emailing_job) {
		$this->q->reset()
			->where("id_emailing_job",$id_emailing_job)
			->where("website_codename",ATF::$codename)
			->addValues(array("fin"=>array("value"=>"'".date("Y-m-d H:i:s")."'")));
		return ATF::db($this->db)->update($this);
	}
};
?>