<?php
/** Classe Activité
* @package ATF
*/ 
class activity extends classes_optima{
	/*---------------------------*/
	/*      Constructeurs        */
	/*---------------------------*/	
	public function __construct(){
		parent::__construct();
		$this->table=__CLASS__;
		$this->db='main';
	}		
	
	/*---------------------------*/
	/*      Méthodes             */
	/*---------------------------*/	
	/** 
	* Met à jour l'activité d'un utilisateur au sein de la table "activity"
	* @param $s la session utilisée 
	*/
	public function setActivity() {
		if (!in_array(ATF::_r('method'),array("js","css"))) { // Ne pas tenir compte de l'activité de certaines méthodes

			$infos = array(
				"nb"=>array("value"=>"`nb`+1")
				,"template"=>ATF::_r('event')
				,"activity"=>array("value"=>"NOW()")
			);
			if (!$infos["template"]) { // Si pas d'event on prend la table
				$infos["template"]=ATF::_r('table');
			}
			if (ATF::_g('event')==="usr,keepOnline") { // Le keep online n'est pas vraiment un click...
				unset($infos["nb"],$infos["template"]);
			}
	
			$d = date("Y-m-d H:i:s");
			if ($this->lastDate!==$d) { // Une seule mise à jour par seconde
				$this->lastDate = $d;
				$this->q->reset()->addField("id_activity")
					->addCondition("login",ATF::$usr->getLogin())
					->addCondition("website_codename",ATF::$codename)
					->setDimension('cell')
					->setStrict();
					
				if ($id_activity=$this->select_all()) { // Mise à jour de l'id_activity
					$this->q->reset()
						->addValues($infos)
						->addCondition("id_activity",$id_activity)
						->addCondition("website_codename",ATF::$codename);
					ATF::db($this->db)->update($this);
				} else { // Créer l'activité, l'utilisateur n'est pas encore dans cette table
					$infos["login"]=ATF::$usr->getLogin();
					$infos["website_codename"]=ATF::$codename;
					$this->q->reset()->addValues($infos);
					ATF::db($this->db)->insert($this);
				}
			}
		}
	}
};
?>