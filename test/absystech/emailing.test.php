<?

class emailing_test extends ATF_PHPUnit_Framework_TestCase {
	
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 17-11-2010
	*/ 
	function setUp($init=false) {
		$this->begin_transaction();
 		
		$this->obj = ATF::emailing();

		if ($init) {
			foreach (explode(",",$init) as $k=>$i) {
				$this->$i();
			}
		}
	}
	
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 17-11-2010
	*/ 
	function tearDown() {
		ATF::db()->rollback_transaction(true);
	}

	/**
	* Créer 4 lien d'emailing pour les TU
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 17-11-2010
	*/ 
	function emailing_lien() {
		ATF::emailing_lien()->truncate();
		$this->elien = array(
			array("emailing_lien"=>"Lien TU 1","url"=>"http://www.lienTU1.fr/")
			,array("emailing_lien"=>"Lien TU 2","url"=>"http://www.lienTU2.fr/")
			,array("emailing_lien"=>"Lien TU 3","url"=>"http://www.lienTU3.fr/")
			,array("emailing_lien"=>"Lien TU 4","url"=>"http://www.lienTU4.fr/")
		);
		foreach ($this->elien as $k=>$i) {
			$this->elien[$k]['id_emailing_lien'] = ATF::emailing_lien()->i($i);
		}
	}

	/**
	* Créer un projet d'emailing pour les TU
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 17-11-2010
	*/ 
	function emailing_projet() { 
		ATF::emailing_projet()->truncate();
		$this->ep = array(
			"emailing_projet"=>"Projet TU 1"
			,"subject"=>"Subject TU 1"
			,"mail_from"=>"tu@absystech.net"
			,"nom_expediteur"=>"TU Expediteur"
			,"couleur_fond"=>"EFEFEF"
			,"corps"=>"Corps du [LINK=".$this->elien[1]['id_emailing_lien']."] mail de projet TU parsemé [LINK=".$this->elien[2]['id_emailing_lien']."] avec des liens\n\n"
		);
		if ($this->ec[0]) {
			$this->ep['corps'] .= "\n\n [INFO=nom] [INFO=prenom] <[INFO=email]>";
		}
		$this->ep['id_emailing_projet'] = ATF::emailing_projet()->i($this->ep);
	}

	/**
	* Créer 4 contacts d'emailing pour les TU
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 17-11-2010
	*/ 
	function emailing_contact() {
		if (!$this->es['id_emailing_source']) return false;
		ATF::emailing_contact()->truncate();
		$this->ec[] = array("nom"=>"Contact TU 1","prenom"=>"Contact TU 1","email"=>"tu1@absystech-speedmail.com","id_emailing_source"=>$this->es['id_emailing_source']);
		$this->ec[] = array("nom"=>"Contact TU 2","prenom"=>"Contact TU 2","email"=>"tu2@absystech-speedmail.com","id_emailing_source"=>$this->es['id_emailing_source']);
		$this->ec[] = array("nom"=>"Contact TU 3","prenom"=>"Contact TU 3","email"=>"tu3@absystech-speedmail.com","id_emailing_source"=>$this->es['id_emailing_source']);
		$this->ec[] = array("nom"=>"Contact TU 4","prenom"=>"Contact TU 4","email"=>"tu4@absystech-speedmail.com","id_emailing_source"=>$this->es['id_emailing_source']);
		$this->ec[] = array("nom"=>"Contact TU 5","prenom"=>"Contact TU 5","email"=>"tu5@absystech-speedmail.com","id_emailing_source"=>$this->es['id_emailing_source']);
		$this->ec[] = array("nom"=>"Contact TU 6","prenom"=>"Contact TU 6","email"=>"tu6@absystech-speedmail.com","id_emailing_source"=>$this->es['id_emailing_source']);
		foreach ($this->ec as $k=>$i) {
			$this->ec[$k]['id_emailing_contact'] = ATF::emailing_contact()->i($i);
		}
	}

	/**
	* Créer 1 liste de contact d'emailing pour les TU
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 17-11-2010
	*/ 
	function emailing_liste() {
		ATF::emailing_liste()->truncate();
		$this->el = array("emailing_liste"=>"Liste TU 1","description"=>"Usage unique pour les TU");
		$this->el['id_emailing_liste'] = ATF::emailing_liste()->i($this->el);
		if ($this->ec) {
			$this->emailing_liste_contact();
		}
	}

	/**
	* Créer les liaisons entre liste et contact d'emailing pour les TU
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 17-11-2010
	*/ 
	function emailing_liste_contact() {
		ATF::emailing_liste_contact()->truncate();
		if ($this->ec && $this->el) {
			foreach ($this->ec as $k=>$i) {
				$this->ecl[$k] = array("id_emailing_contact"=>$i['id_emailing_contact'],"id_emailing_liste"=>$this->el['id_emailing_liste']);
				$this->ecl[$k]["id_emailing_liste_contact"] = ATF::emailing_liste_contact()->i($this->ecl[$k]);
			}
		}
	}

	/**
	* Créer un job d'emailing pour les TU
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 17-11-2010
	*/ 
	function emailing_job() {
		ATF::emailing_job()->truncate();
		$this->ej = array("emailing_job"=>"Job TU 1","id_emailing_projet"=>$this->ep['id_emailing_projet'],"id_emailing_liste"=>$this->el['id_emailing_liste'],"depart"=>date("Y-m-d",strtotime("-1 day")));
		$this->ej['id_emailing_job'] = ATF::emailing_job()->i($this->ej);		
	}
	
	/**
	* Créer un job d'emailing pour les TU
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 17-11-2010
	*/ 
	function emailing_job_email() {
		if (!$this->ej || !$this->ecl) return false;
		ATF::emailing_job_email()->truncate(); 
		$this->eje = array(
			array("id_emailing_job"=>$this->ej['id_emailing_job'],"id_emailing_liste_contact"=>$this->ecl[0]['id_emailing_liste_contact'])
			,array("id_emailing_job"=>$this->ej['id_emailing_job'],"id_emailing_liste_contact"=>$this->ecl[1]['id_emailing_liste_contact'])
			,array("id_emailing_job"=>$this->ej['id_emailing_job'],"id_emailing_liste_contact"=>$this->ecl[2]['id_emailing_liste_contact'])
			,array("id_emailing_job"=>$this->ej['id_emailing_job'],"id_emailing_liste_contact"=>$this->ecl[3]['id_emailing_liste_contact'])
		);
		foreach ($this->eje as $k=>$i) {
			$this->eje[$k]['id_emailing_job_email'] = ATF::emailing_job_email()->i($i);
		}
	}
	
	/**
	* Créer 4 contacts d'emailing pour les TU
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 17-11-2010
	*/ 
	function emailing_source() {
		//ATF::emailing_source()->truncate();
		
		$this->es = array("id_user"=>ATF::$usr->getID(),"date"=>"2050-08-01 09:00:00"	,"emailing_source"=>"Source TU pour emailing liste");
		$this->es['id_emailing_source'] = ATF::emailing_source()->i($this->es);

		$this->ec[] = array("nom"=>"Contact TU 99","prenom"=>"Contact TU 99","email"=>"tu99@absystech-speedmail.com","id_emailing_source"=>$this->es['id_emailing_source']);
		$this->ec[] = array("nom"=>"Contact TU 98","prenom"=>"Contact TU 98","email"=>"tu98@absystech-speedmail.com","id_emailing_source"=>$this->es['id_emailing_source']);

		
		foreach ($this->ec as $k=>$i) {
			$this->ec[$k]['id_emailing_contact'] = ATF::emailing_contact()->i($i);
		}
	}
	
	/**
	* Créer 4 contacts d'emailing pour les TU
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 17-11-2010
	*/ 
	function emailing_tracking() {
		if (!$this->eje || !$this->elien) return false;
		ATF::emailing_tracking()->truncate();
		
		$this->et = array(
			array("id_emailing_job_email"=>$this->eje[0]['id_emailing_job_email'],"id_emailing_lien"=>$this->elien[0]['id_emailing_lien'],"date"=>date('Y-m-d'),"ip"=>"1.1.1.1","host"=>"roubaix.absystech.net")
			,array("id_emailing_job_email"=>$this->eje[1]['id_emailing_job_email'],"id_emailing_lien"=>$this->elien[1]['id_emailing_lien'],"date"=>date('Y-m-d'),"ip"=>"2.2.2.2","host"=>"wattrelos.absystech.net")
			,array("id_emailing_job_email"=>$this->eje[1]['id_emailing_job_email'],"id_emailing_lien"=>$this->elien[1]['id_emailing_lien'],"date"=>date('Y-m-d'),"ip"=>"2.2.2.4","host"=>"seclin.absystech.net")
			,array("id_emailing_job_email"=>$this->eje[2]['id_emailing_job_email'],"id_emailing_lien"=>$this->elien[2]['id_emailing_lien'],"date"=>date('Y-m-d'),"ip"=>"3.3.3.3","host"=>"lyslezlannoy.absystech.net")
			,array("id_emailing_job_email"=>$this->eje[3]['id_emailing_job_email'],"id_emailing_lien"=>$this->elien[3]['id_emailing_lien'],"date"=>date('Y-m-d'),"ip"=>"4.4.4.4","host"=>"tourcoing.absystech.net")
		);
				
		foreach ($this->et as $k=>$i) {
			$this->et[$k]['id_emailing_tracking'] = ATF::emailing_tracking()->i($i);
		}
	}

	/** 
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 09-06-2011
	*/
	public function test_default_value() {
		$this->assertEquals(ATF::$usr->getID(),$this->obj->default_value("id_user"),"Erreur de default value pour l'id_user");
		$this->assertNull($this->obj->default_value("cv"),"Erreur de default value pour l'cv");
	}


};
?>