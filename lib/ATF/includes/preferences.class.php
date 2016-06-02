<?php
/**
* Classe privilege : Gestion des droits sur ATF5
* @package ATF
*/
class preferences extends classes_optima {
	/**
	* Constructeur
	*/
	public function __construct() {
		parent::__construct();
		$this->table = __CLASS__;

		/* Info utilisateurs */
		$r = $this->getCustom();
		$this->colonnes['primary'] = array(
			'user.date_format' => array("null"=>true,"xtype"=>"combo","data"=>array("d/m/Y","m/d/Y","complet","texte","Y-m-d","d-m-Ymin"),"default"=>"complet")
			,"user.heure" => array("null"=>true,"xtype"=>"radiogroup", "data"=>array("oui","non"),'default'=>'oui')
			,"user.tronquer" => array("null"=>true,"xtype"=>"radiogroup", "data"=>array("oui","non"),'default'=>'non')
			,"user.show_all" => array("null"=>true,"xtype"=>"radiogroup", "data"=>array("oui","non"),'default'=>'non')
			,"user.show_data_day" => array("null"=>true,"xtype"=>"radiogroup", "data"=>array("oui","non"),'default'=>'non')
		);
		if (ATF::$usr->privilege("localisation_langue")) {
			$this->colonnes['primary']["user.id_localisation_langue"] = array("null"=>true,"xtype"=>"combo","data"=>ATF::localisation_langue()->getAll(),"default"=>"fr");
		}
		if (ATF::module()->ss("module","news")) {
			$this->colonnes['primary']["user.newsletter"] = array("null"=>true,"xtype"=>"radiogroup", "data"=>array("oui","non"),'default'=>'oui');
		}
		$this->panels['primary'] = array("nbCols"=>1,"collapsible"=>false);

		$this->colonnes['panel']['password'] = array(
			"user.password" => array("null"=>true,"xtype"=>"textfield","inputType"=>"password","default"=>"")
			,"user.password_again" => array("null"=>true,"xtype"=>"textfield","inputType"=>"password","default"=>"")
		);
		$vis = false;
		$this->panels['password'] = array("visible"=>$vis, 'nbCols'=>1 ,"collapsible"=>false,"checkboxToggle"=>true);

		if (ATF::$usr->privilege("messagerie")) {
			$this->colonnes['panel']['messagerie'] = array(
				"messagerie.host" => array("null"=>true,"xtype"=>"textfield","default"=>"")
				,"messagerie.username" => array("null"=>true,"xtype"=>"textfield","default"=>ATF::$usr->get('email'))
				,"messagerie.password" => array("null"=>true,"xtype"=>"textfield","inputType"=>"password","default"=>"")
				,"messagerie.port" => array("null"=>true,"xtype"=>"textfield","default"=>143)
				,"messagerie.folder" => array("null"=>true,"xtype"=>"textfield","default"=>"INBOX")
				,"messagerie.tls" => array("null"=>true,"xtype"=>"radiogroup", "data"=>array("starttls","tls","notls","ssl"),"default"=>"notls")
			);
				$vis = false;
				if ($r['messagerie']) {
					$vis = true;
				}
			$this->panels['messagerie'] = array("visible"=>$vis, 'nbCols'=>1 ,"collapsible"=>false,"checkboxToggle"=>true);
		}

		/*if (ATF::$usr->privilege("calendrier") && ATF::calendrier()) {
			$this->colonnes['panel']['calendrier'] = array(
				"calendrier.host" => array("null"=>true,"xtype"=>"textfield","default"=>"")
				,"calendrier.username" => array("null"=>true,"xtype"=>"textfield","default"=>"")
				,"calendrier.password" => array("null"=>true,"xtype"=>"textfield","inputType"=>"password","default"=>"")
				,"calendrier.calendar_partage" => array("null"=>true,"xtype"=>"multiselect","data"=>ATF::calendrier()->liste(),"default"=>NULL)
				,"calendrier.calendar_default" => array("null"=>true,"xtype"=>"radiogroup", "data"=>array("oui","non"),'default'=>'oui')
				,"calendrier.calendar_name"=>array("null"=>true,"xtype"=>"textfield","default"=>"")
			);
			$vis = false;
			if ($r['messagerie']) {
				$vis = true;
			}
			$this->panels['calendrier'] = array("visible"=>true, 'nbCols'=>1 ,"collapsible"=>true,"checkboxToggle"=>true);
		}*/
		if (ATF::$usr->privilege("messagerie") && ATF::calendrier()) {
			$this->colonnes['panel']['widgets'] = array(
				"widgets.ca_previsionnel" => array("null"=>true,"xtype"=>"radiogroup", "data"=>array("oui","non"),'default'=>'oui')
				,"widgets.marge" => array("null"=>true,"xtype"=>"radiogroup", "data"=>array("oui","non"),'default'=>'oui')
				,"widgets.resteAPayer" => array("null"=>true,"xtype"=>"radiogroup", "data"=>array("oui","non"),'default'=>'oui')
				,"widgets.facture_top10negatif" => array("null"=>true,"xtype"=>"radiogroup", "data"=>array("oui","non"),'default'=>'oui')
				,"widgets.suvis" => array("null"=>true,"xtype"=>"radiogroup", "data"=>array("oui","non"),'default'=>'oui')
				,"widgets.hotline_interaction" => array("null"=>true,"xtype"=>"radiogroup", "data"=>array("oui","non"),'default'=>'oui')
				,"widgets.hotline" => array("null"=>true,"xtype"=>"radiogroup", "data"=>array("oui","non"),'default'=>'oui')
				,"widgets.tpsPriseCharge" => array("null"=>true,"xtype"=>"radiogroup", "data"=>array("oui","non"),'default'=>'oui')
				,"widgets.hotline_top10negatif" => array("null"=>true,"xtype"=>"radiogroup", "data"=>array("oui","non"),'default'=>'oui')
				,"widgets.tpsCloture" => array("null"=>true,"xtype"=>"radiogroup", "data"=>array("oui","non"),'default'=>'oui')
				,"widgets.nbreCloture" => array("null"=>true,"xtype"=>"radiogroup", "data"=>array("oui","non"),'default'=>'oui')
				,"widgets.partTicket" => array("null"=>true,"xtype"=>"radiogroup", "data"=>array("oui","non"),'default'=>'oui')
				,"widgets.waitmep" => array("null"=>true,"xtype"=>"radiogroup", "data"=>array("oui","non"),'default'=>'oui')
				,"widgets.statCleodis" => array("null"=>true,"xtype"=>"radiogroup", "data"=>array("oui","non"),'default'=>'oui')
				,"widgets.hotline_requetebyUser" => array("null"=>true,"xtype"=>"radiogroup", "data"=>array("oui","non"),'default'=>'oui')
				,"widgets.hotline_requetebyUser7joursGlissants" => array("null"=>true,"xtype"=>"radiogroup", "data"=>array("oui","non"),'default'=>'oui')
				,"widgets.hotline_requetebyUserParMois" => array("null"=>true,"xtype"=>"radiogroup", "data"=>array("oui","non"),'default'=>'oui')
				,"widgets.hotline_graph_tarif_horaire" => array("null"=>true,"xtype"=>"radiogroup", "data"=>array("oui","non"),'default'=>'oui')
			);
			$this->panels['widgets'] = array("visible"=>true, 'nbCols'=>2 ,"collapsible"=>true,"checkboxToggle"=>true);
		}
		if (ATF::$usr->privilege("suivi")) {
			$this->colonnes['panel']['suivi'] = array(
				"suivi.mail"=>array("null"=>true,"xtype"=>"radiogroup", "data"=>array("oui","non"),'default'=>'oui'),
				"suivi.mail_digest"=>array("null"=>true,"xtype"=>"radiogroup", "data"=>array("oui","non"),'default'=>'oui')
			);
			$vis = false;
			if ($r['messagerie']) {
				$vis = true;
			}
			$this->panels['suivi'] = array("visible"=>$vis, 'nbCols'=>1 ,"collapsible"=>false,"checkboxToggle"=>true);
		}

		if (ATF::$usr->privilege("phone") && !ATF::$usr->isGod() && ATF::$usr->getID()) {
			ATF::phone()->q->reset()->addField("id_phone,phone")->where("id_user",ATF::$usr->getID());
			$phones=ATF::phone()->sa();
			$this->preference["phone"]["phone"]["data"]=array();
			foreach($phones as $phone){
				$data[]=$phone["id_phone"];
			}
			$this->colonnes['panel']['phone'] = array(
				"phone.phone"=>array("null"=>true,"xtype"=>"combo","data"=>$data,"default"=>ATF::$usr->get("id_phone"))
			);
			$vis = false;
			if ($r['phone']['phone']) {
				$vis = true;
			}
			$this->panels['phone'] = array("visible"=>$vis, 'nbCols'=>1 ,"collapsible"=>false,"checkboxToggle"=>true);
		}
	}

	/**
	* Utilisé par la fenêtre de préférences
	*/
	public function insert($infos,$s,$files) {
		ATF::$cr->block("top");
		ATF::$msg->addNotice(ATF::$usr->trans("insert_preferences_ok",$this->table));
		return $this->update($infos,$s,$files);
	}

	/**
	* Utilisé par la fenêtre de préférences
	*/
	public function update($infos,$s,$files) {
		if ($infos['radiofield_preferences']['calendrier.calendar_name']){
			$infos['preferences']['calendrier.calendar_name'] = $infos['radiofield_preferences']['calendrier.calendar_name'];
		}
		elseif ($infos['preferences']['calendrier.calendar_name'] === 'defaultCalendar') {
			$infos['preferences']['calendrier.calendar_name'] = 'Calendar';
		}
		$this->infoCollapse($infos);
		$pref = $this->getCustom();
		if ($infos['messagerie.password'] && $infos['messagerie.password'] !== $pref['messagerie']['password']) {
			$infos['messagerie.password'] = $this->cryptPasswordMessagerie($infos['messagerie.password']);
		}
		if ($infos['calendrier.password'] && $infos['calendrier.password'] !== $pref['calendrier']['password']) {
			$infos['calendrier.password'] = $this->cryptPasswordIcal($infos['calendrier.password'] , ATF::$usr->getId());
		}

		if ($infos["user.password"] === $infos["user.password_again"]) {
			try {
				ATF::$usr->sanitize_password($infos["user.password"]);
			} catch (Exception $e) {
				if (get_class($e) == "errorATF") {
					throw $e;
				}
			}
		}

		foreach ($infos as $k=>$i) {
			$d = explode(".",$k);
			$params[$d[0]][$d[1]] = $i;
		}

		$params['user']['id_user'] = ATF::$usr->getId(); //$params de base
		return ATF::$usr->setPreferences($params);
	}

	public function getCustom($index=false,$id_user=false) {
		if($id_user){
			$custom=unserialize(ATF::user()->select($id_user,"custom"));
		}else{
			$custom=ATF::$usr->get('custom');
		}
//		foreach ($custom as $k=>$i) {
//			if ($index && $index != $k) continue;
//			foreach ($i as $k_=>$i_) {
//				$return['preferences'][$k.".".$k_] = $i_;
//			}
//		}
		$custom['user']['newsletter'] = ATF::$usr->get('newsletter');
		$custom['user']['id_localisation_langue'] = ATF::$usr->get('id_localisation_langue');
		$custom['phone']['phone'] = ATF::$usr->get('id_phone');

		return $custom;
	}

	public function getRequestFormulaire() {
		$custom=unserialize(ATF::user()->select(ATF::$usr->getId(),"custom"));
		foreach ($custom as $k=>$i) {
			foreach ($i as $k_=>$i_) {
				$return['preferences'][$k.".".$k_] = $i_;
			}
		}
		$return['preferences']['user.newsletter'] = ATF::$usr->get('newsletter');
		$return['preferences']['user.id_localisation_langue'] = ATF::$usr->get('id_localisation_langue');
		$return['preferences']['phone.phone'] = ATF::$usr->get('id_phone');

		return $return;
	}

	public function getNomFromTable($v,$f) {
		if ($f=="phone.phone") {
			return ATF::phone()->nom($v);
		}
		return NULL;
	}

	public function changePreference(&$infos) {
		$infos['display'] = true;
		ATF::$html->array_assign(array(
			"current_class"=>$this,
			"event"=>"update",
			"requests"=>self::getRequestFormulaire()
		));
		return ATF::$html->display("preference.tpl.js");
	}

	public function initAesFixePasswordMessagerie() {
		$seed = ATF::user()->cryptId(ATF::$usr->get("id_user"));
		$this->aesFixe = new aes();
		$this->aesFixe->setSeed($seed);
	}

	public function cryptPasswordMessagerie($mdp) {
		$this->initAesFixePasswordMessagerie();
		return $this->aesFixe->crypt($mdp);
	}

	public function decryptPasswordMessagerie($mdp) {
		$this->initAesFixePasswordMessagerie();
		return $this->aesFixe->decrypt($mdp);
	}
/**********************************************************
 * Preference pour Ical
 *
 ***********************************************************/


	public function initAesFixePasswordIcal($id_user) {
		$seed = ATF::user()->cryptId($id_user);
		$this->aesFixe = new aes();
		$this->aesFixe->setSeed($seed);
	}

	public function cryptPasswordIcal($mdp, $id_user) {
		$this->initAesFixePasswordIcal($id_user);
		return $this->aesFixe->crypt($mdp);
	}

	public function decryptPasswordIcal($mdp, $id_user) {
		$this->initAesFixePasswordIcal($id_user);
		return $this->aesFixe->decrypt($mdp);
	}


};
?>