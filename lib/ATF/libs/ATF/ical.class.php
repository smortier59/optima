<?php
class ical extends classes {
	public $id = NULL;

	/**
    * Permet de se connecter au calendrier zimbra pour en recup les infos
    * @author Antoine MAITRE <amaitre@absystech.fr>
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param string $login pour connaitre le calendrier a recup
	* @return array $infos l'array contient les informations du calendrier
    */ 
	public function info_calendar_zimbra($calendar_name, $email, $id, $start, $end) {		
		$pref = unserialize(ATF::user()->select(ATF::$usr->getId(),"custom"));
		$pref = $pref['calendrier'];

		if (!$email && !$pref) {
			return NULL;
		}

		if(!$calendar_name) {
			ATF::$msg->addWarning("Attention, la personne ayant pour email: ".$email.". n'a pas encore enregistré le nom de son calendrier", "Erreur de calendrier");
			return NULL;
		}	
					

		$curl = new curl;
		touch('/tmp/cookie.txt');
		$filename = '/tmp/'.$calendar_name.'.ics';
		$f = fopen($filename, 'w+');
		ATF::curl()->curlInit();
		$adresse = (preg_match("#https#",$pref['host'])?$pref['host']:"https://".$pref['host'])."/home/".$email."/".rawurlencode($calendar_name)."?fmt=json&start=".$start."&end=".date('Y-m-d', strtotime('+1 day', strtotime($end)));
		//echo "\n".$adresse."\n";

		ATF::curl()->curlSetopt(CURLOPT_URL,$adresse);
		

		ATF::curl()->curlSetopt(CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		ATF::curl()->curlSetopt(CURLOPT_USERPWD, $pref['username'].':'.ATF::preferences()->decryptPasswordIcal($pref['password'] , ATF::$usr->getId()));

		//$verbosPath = realpath("/home/optima/core/log/qjanon");
		//ATF::curl()->curlSetopt(CURLOPT_VERBOSE, true);
		//ATF::curl()->curlSetopt(CURLOPT_STDERR, $f);

		ATF::curl()->curlSetopt(CURLOPT_SSL_VERIFYPEER, false);
		ATF::curl()->curlSetopt(CURLOPT_AUTOREFERER, true);
		ATF::curl()->curlSetopt(CURLOPT_RETURNTRANSFER, true);
		ATF::curl()->curlSetopt(CURLOPT_SSL_VERIFYHOST, FALSE);
		ATF::curl()->curlSetopt(CURLOPT_COOKIESESSION, true);
		ATF::curl()->curlSetopt(CURLOPT_COOKIEJAR, realpath('/tmp/cookie.txt'));
		ATF::curl()->curlSetopt(CURLOPT_FILE, $f);

		ATF::curl()->curlExec();
		ATF::curl()->curlClose();
		fclose($f);
		unlink('/tmp/cookie.txt');

		//https://zimbra.absystech.net/home/qjanon@absystech.fr/calendar?fmt=json&start=1day
		//https://zimbra.absystech.net/home/qjanon@absystech.fr/calendar?fmt=json&start=20130101

		$data = json_decode(file_get_contents($filename),true);

		$data = $data['appt'];

		foreach ($data as $k=>$i) {
			$tmp = $i['inv'][0]['comp'][0]['s'][0]['d'];
			$s = "";
			if (substr($tmp, 0, 4)) $s .= substr($tmp, 0, 4).'-';
			if (substr($tmp, 4, 2)) $s .= substr($tmp, 4, 2).'-';
			if (substr($tmp, 6, 2)) $s .= substr($tmp, 6, 2);
			if (substr($tmp, 8, 1)) $s .= substr($tmp, 8, 1);
			if (substr($tmp, 9, 2)) $s .= substr($tmp, 9, 2).':';
			if (substr($tmp, 11, 2)) $s .= substr($tmp, 11, 2).':';
			if (substr($tmp, 13, 2)) $s .= substr($tmp, 13, 2);

			$tmp = $i['inv'][0]['comp'][0]['e'][0]['d'];
			$e = "";
			if (substr($tmp, 0, 4)) $e .= substr($tmp, 0, 4).'-';
			if (substr($tmp, 4, 2)) $e .= substr($tmp, 4, 2).'-';
			if (substr($tmp, 6, 2)) $e .= substr($tmp, 6, 2);
			if (substr($tmp, 8, 1)) $e .= substr($tmp, 8, 1);
			if (substr($tmp, 9, 2)) $e .= substr($tmp, 9, 2).':';
			if (substr($tmp, 11, 2)) $e .= substr($tmp, 11, 2).':';
			if (substr($tmp, 13, 2)) $e .= substr($tmp, 13, 2);

			$r[] = array(
				"id"=> $i['id'] , 
				"cid" => $id, 
				"start" => $s,//$i['inv'][0]['comp'][0]['s'][0]['d'], 
				"end" => $e,//$i['inv'][0]['comp'][0]['s'][0]['d'], 
				"title" => $i['inv'][0]['comp'][0]['name'] , 
				"notes" => $i['inv'][0]['comp'][0]['desc'][0]['_content'] , 
				"loc"=>$i['inv'][0]['comp'][0]['loc'],
				"ad"=>$i['inv'][0]['comp'][0]['allDay']?true:false
			);
		}


		//unlink($filename);
		return $r;

	}
	
	/**
	* Teste si l'utilisateur a le droit de faire l'évênement, et retourne un booleen
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $module Le module cible du test de privilege
	* @param string $event
	* @param array $s ($_SESSION habituellement attendu)
	* @return boolean
	*/	
	public function eventPrivilege(&$event) {
		switch ($event) {
			default:
				return true; // On peut toujours le faire
		}
		return parent::eventPrivilege($event); // Par défaut
	}
	
	/**
    * Renvoi un JSON avec le contenu de l'agenda entre la borne de date passé en entrée
    * @author Quentin JANON <qjanon@absystech.fr>
	* @param array $infos 
	*	start : date de début
	* 	end : date de fin
	* @return json Les data en JSON ou NULL si rien
    */ 
   	function view(&$infos) {		
		$infos["display"]=true;
		$calendars = $this->getCalendars();
		foreach ($calendars as $k=>$i) {
			$r = $this->info_calendar_zimbra($i['realName'],$i['email'],$i['id'],$infos['start'],$infos['end']);
			foreach ($r as $k_=>$i_) {
				$json['data'][] = $i_;
			}
		}


		if (!$json['data']) {
			ATF::$msg->addWarning("Aucun calendrier n'a été récupéré. Vérifiez vos partages sur zimbra avec les autres utilisateurs.", "");
			return NULL;
		} else {
			return json_encode($json);
		}
		
	}
	

	/**
    * Récupère tous les calendrier par rapport a ce qui est coché dans les préférences
    * @author Quentin JANON <qjanon@absystech.fr>
	* @return array tous les calendriers disponibles
    */ 
	public function getCalendars(){
		$color = 1;

		//$info["display"] = true;
		$custom=unserialize(ATF::user()->select(ATF::$usr->getId(),"custom"));
		if ($custom["calendrier"]["calendar_name"]) $calendar_perso = explode(",",$custom["calendrier"]["calendar_name"]);
		if ($custom["calendrier"]["calendar_default"]=="oui") {
			$calendar_perso[] = "Calendar";
		}
		foreach ($calendar_perso as $k => $value) {
			$r[] = array(
				"id"=>ATF::$usr->getId().$k,
				"title"=>ATF::user()->nom(ATF::$usr->getId())." - ".$value,
				"realName"=>$value,
				"color"=>$color,
				"email"=>ATF::$usr->get('email')
			);

			$color++;
		}			

		if ($custom["calendrier"]["calendar_partage"]) {
			$emails = explode(",",$custom["calendrier"]["calendar_partage"]);

			foreach ($emails as $key => $email) {

				ATF::user()->q->reset()->where("email", $email);
				$user = ATF::user()->select_row();					

				$customExtend = unserialize(ATF::user()->select($user['id_user'],"custom"));

				if ($customExtend["calendrier"]["calendar_name"]) $calendar_extend = explode(",",$customExtend["calendrier"]["calendar_name"]);
				if ($customExtend["calendrier"]["calendar_default"]=="oui") {
					$calendar_extend[] = "Calendar";
				}
				if (!$calendar_extend) continue;
				foreach ($calendar_extend as $k=>$calendar) {
					$r[] = array(
						"id"=>$user['id_user'].$k,
						"title"=>ATF::user()->nom($user['id_user'])." - ".$calendar,
						"realName"=>$calendar,
						"color"=>$color,
						"email"=>$user['email']
					);

					$color++;
				}
			}	
		}

		//log::logger($r,"qjanon");
		return $r;
	}
}