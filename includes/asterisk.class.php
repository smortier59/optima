<?
/** 
* Classe asterisk - Permet l'utilisation de services téléphoniques Asterisk (click2call, fax,...)
* Remarque : le système de fax n'est pas encore implémenté
* @author Jérémie Gwiazdowski <jgw@absystech.fr>
* @package Optima
*/
class asterisk extends classes_optima {
	public function __construct($obj="webService"){
		parent::__construct();
		$this->addPrivilege("createCall");
	}

	/**
	* Crée un appel (click2call) via des templates href, contact-contact.tel et generic-select_all-content
	* Utilisation des webServices
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param mixed $infos
	*/
	public function createCall($infos){
		$this->infoCollapse($infos);
		//Bloquage du Top et du generationTime
		ATF::$cr->block("top");
		ATF::$cr->block("generationTime");
		
		//Recherche de la source et de la destination
		$module = $infos["table"];
		$infos["field"] = str_replace(".","",substr($infos["field"],strpos($infos["field"],".")));
		$tel = str_replace(" ","",ATF::getClass($module)->select($infos["id"],$infos["field"]));
		if(!$tel && $module=='contact'){ // Prendre le contact de société sinon...
			$tel = ATF::societe()->select(ATF::getClass($module)->select($infos["id"],"id_societe"),"tel");
		}
				
		//Sip non paramétré	
		if (!ATF::$usr->get("id_phone")) throw new errorATF(ATF::$usr->trans("sip_failed",$this->table));
		
		//Tel non trouvé
		if (!$tel) throw new errorATF(ATF::$usr->trans("tel_empty",$this->table));
		
		//Gestion de l'indicatif international
		$tel=str_replace("+","00",$tel);
		
		//Lancement de l'appel
		$this->originate(ATF::$usr->get("id_phone"),$tel);
		
		//Notice
		ATF::$msg->addNotice(ATF::$usr->trans("call_to",$this->table)." ".$tel." ".ATF::$usr->trans("call_from",$this->table)." ".ATF::phone()->select(ATF::$usr->get("id_phone"),"sip"));
		return true;
	}

	/**
	* Lance le webService originate sur le serveur définit dans le user (id_asterisk)
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $id_source_phone L'identifiant du téléphone source
	* @param string $destination Le numéro appelé
	*/
	public function originate($id_source_phone,$destination){
		// Recherche des informations sur le téléphone
		$phone=ATF::phone()->select($id_source_phone);
		$asterisk=$this->select($phone["id_asterisk"]);

		$params = array(
			'token'=>$asterisk["token"],
			'from'=>$phone["sip"],
			'to'=>$destination,
			'callerId'=>str_replace(array("é","è"),array("e","e"),ATF::user()->nom(ATF::usr()->getID())),
			'context'=>'atta'
		);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_URL, $asterisk["url_webservice"].__FUNCTION__);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
		//curl_setopt($ch, CURLOPT_VERBOSE, 1);
		$data = curl_exec($ch);
		curl_close($ch);
		return !!json_decode($data,true);
	}
	
	/**
	* Retourne le nom du CallerId s'il est trouvé
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param int $callerId numéro de téléphone
	* @param boolean $idOnly TRUE ne renverra que l'id_contact
	* @return int|array
	*/
	public function getContactFromCallerId($callerId,$idOnly=false){
		ATF::contact()->q->reset()
			->addField("contact.id_contact","id_contact")
			->addField("societe.id_societe","id_societe")
			->addField("nom")
			->addField("prenom")
			->addField("societe.societe","societe")
			->from("contact","id_societe","societe","id_societe")
			->orWhere("REPLACE(contact.tel,' ','')",$callerId,"phone")
			->orWhere("REPLACE(contact.gsm,' ','')",$callerId,"phone")
			->orWhere("REPLACE(contact.tel_autres,' ','')",$callerId,"phone");
		$result = ATF::contact()->select_row();
		if ($idOnly) {
			return $result["id_contact"];
		}
		return $result;
	}
	
	/**
	* Retourne le nom du CallerId s'il est trouvé
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param int $callerId numéro de téléphone
	* @param boolean $idOnly TRUE ne renverra que l'id_societe
	* @return int|array
	*/
	public function getSocieteFromCallerId($callerId,$idOnly=false){
		ATF::societe()->q->reset()
			->addField("id_societe")
			->addField("societe")
			->orWhere("REPLACE(tel,' ','')",$callerId,"phone");
		$result = ATF::societe()->select_row();
		if ($idOnly) {
			return $result["id_societe"];
		}
		return $result;
	}
	
	/**
	* Retourne le nom du CallerId s'il est trouvé
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param int $callerId numéro de téléphone
	* @return string
	*/
	public function getCallerName($callerId){
		if ($contact = $this->getContactFromCallerId($callerId)) {
			$result = $contact["nom"]." ".$contact["prenom"];
			if ($contact["societe"]) {
				$result = $contact["societe"]." (".$result.")";
			}
		} elseif ($societe = $this->getSocieteFromCallerId($callerId)) {
			$result = $societe["societe"];
		}		
		$result = mb_convert_encoding($result, "UTF-8");
		return $result;
	}
	
	/**
	* Retourne le nombre de crédits de la société
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param string $refSociete Référence de la société
	* @return string
	*/
	public function getCreditFromRef($refSociete){
		$solde="nok";
		if (($id = $this->getSocieteId($refSociete))!=="nok") {						
			$solde = ATF::societe()->getSolde($id);					
			$ref = ATF::societe()->select($id , "ref");						 		
			if(strpos($ref , "A") === 0){											
				$notifier = "24,23,33";
			}else{													
				$notifier = "1,3,12,30,42,52,55";
			}		
			$infos = array(
				"id_societe"=>$id
				,"id_user"=>13
				,"type"=>"note"
				,"texte"=>"Appel par téléphone détecté sur le serveur vocal."
				,"date"=>date("Y-m-d H:i:s")
				,"suivi_contact"=>NULL
				,"suivi_societe"=>$notifier
				,"suivi_notifie"=>$notifier							
			);			  
			ATF::suivi()->insert($infos);
		}		
		return $solde;
	}
	
	/**
	* Stocke un suivi d'appel interrompu
	* @param string $refSociete Référence de la société
	* @return string
	*/
	public function callCancelled($refSociete){
		if (($id = $this->getSocieteId($refSociete))!=="nok") {		
			$ref = ATF::societe()->select($id , "ref");						 		
			if(strpos($ref , "A") === 0){											
				$notifier = "24,23,33";
			}else{													
				$notifier = "1,3,12,30,42,52,55";
			}		
			$infos = array(
				"id_societe"=>$id
				,"id_user"=>13
				,"type"=>"note"
				,"texte"=>"Appel interrompu avant enregistrement d'un message."
				,"date"=>date("d-m-Y H:i")
				,"suivi_contact"=>NULL
				,"suivi_societe"=>$notifier
				,"suivi_notifie"=>$notifier							
			);			  
			ATF::suivi()->insert($infos);
		}		
	}
	
	/**
	* Retourne l'id_societe
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $refSociete Référence de la société
	* @return string
	*/
	public function getSocieteId($refSociete){
		ATF::societe()->q->reset()->orWhere("ref",'%'.$refSociete,"reference","LIKE")->addField("id_societe")->setStrict();
		$id = ATF::societe()->select_cell();
		return $id ? $id : "nok";
	}
	
	/**
	* Retourne le nom de la hotline à partir de son ID
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param int $id_hotline
	* @return string
	*/
	public function checkHotlineFromId($id_hotline){
		$hotline = ATF::hotline()->select($id_hotline);
		if (!is_array($hotline)) {
			return "nok";
		}
		if ($hotline["etat"]=="payee" || $hotline["etat"]=="annulee" || $hotline["etat"]=="done") {
			return "ferme";
		}
		return $hotline["hotline"];
	}
	
	/**
	* Retourne le codename de la hotline
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param int $id_hotline
	* @return string
	*/
	public function getCodenameFromHotline($id_hotline){
		$absystech = $this->checkHotlineFromId($id_hotline);
		if ($absystech!="ferme" && $absystech!="nok") {
			return 'absystech';
		} else {
			
			// Ajouter check reel ATT
			return 'att';
		}
	}
	
	/**
	* Retourne le SIP de l'utilisateur en charge de cette hotline
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param int $id_hotline
	* @return string
	*/
	public function getSIPFromHotline($id_hotline){
		$hotline = ATF::hotline()->select($id_hotline);
		if (is_array($hotline)) {
			if ($hotline["id_user"]) {
				if ($id_phone = ATF::user()->select($hotline["id_user"],"id_phone")) {
					if ($sip = ATF::phone()->select($id_phone,"sip")) {
						return $sip;
					} else {
						return "nosip";
					}	
				} else {
					return "nophone";
				}
			} else {
				return "nouser";
			}
		} else {
			return "nok";
		}
	}

	/**
	* Création d'une requête hotline vocalement depuis le SVI
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $refSociete Référence de la société
	* @param array $files
	* @return string
	*/
	public function insertHotline($refSociete,$files,$pole){
		$files["fichier_joint"]=array_shift($files);
		$hotline = array(
			"hotline"=>"Requête SVI du ".date("d/m/Y")
			,"id_societe"=>$this->getSocieteId($refSociete)
			,"detail"=>"Requête créée par le serveur vocal interactif téléphonique."
			,"date_debut"=>date("Y-m-d")
			,"visible"=>"oui"
			,"id_contact"=>false // On force pas de contact
			,"pole_concerne"=>$pole
		);
        
		try {
			$id_hotline = ATF::hotline()->insert($hotline,$session,$files);
		} catch (Exception $e) { log::logger($e->getMessage(),"asterisk.log"); log::logger($e->getTraceAsString(),"asterisk.log"); }
		
		// Insertion du fichier		
		$fichier = array_shift($files);	
		$newpath = ATF::hotline()->filepath($id_hotline,"fichier_joint");
		mkdir(dirname($newpath),0777,true);
		log::logger(ATF::$codename.",".$method." : deplacement et zip du fichier ".$fichier["tmp_name"]." (".number_format(filesize($fichier["tmp_name"]),0,"."," ")." octets) vers ".$newpath,"asterisk.log");
		$zip = new ZipArchive();
		util::file_put_contents($newpath,"");
		$zip->open($newpath);
		$zip->addFile($fichier["tmp_name"],"audio.wav");	
		$zip->close();
		return $id_hotline;
	}

	/**
	* Création d'une interaction hotline vocale depuis le SVI
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $id_hotline
	* @param array $files
	* @return string
	*/
	public function insertInteraction($id_hotline,$files){
		$files["fichier_joint"]=array_shift($files);
		$hotline_interaction = array(
			"id_hotline"=>$id_hotline
			,"date"=>date('Y-m-d H:i:s')
			,"detail"=>"Interaction vocale via répondeur téléphonique AbsysTech."
			,"id_contact"=>false // On force pas de contact
			,"duree_presta"=>"00:01"
			,"heure_debut_presta"=>date('H:i')
			,"heure_fin_presta"=>date('H:i',strtotime(date("Y-m-d H:i:s")."+1 minutes"))
			,"credit_presta"=>0.00
			,"no_test_credit"=>true
		);



		try {
			$id_hotline_interaction = ATF::hotline_interaction()->insert($hotline_interaction,$session,$files);
		} catch (Exception $e) { log::logger($e->getMessage(),"asterisk.log"); log::logger($e->getTraceAsString(),"asterisk.log"); }
		
		// Insertion du fichier		
		$fichier = array_shift($files);	
		$newpath = ATF::hotline_interaction()->filepath($id_hotline_interaction,"fichier_joint");
		mkdir(dirname($newpath),0777,true);
		log::logger(ATF::$codename.",".$method." : deplacement et zip du fichier ".$fichier["tmp_name"]." (".number_format(filesize($fichier["tmp_name"]),0,"."," ")." octets) vers ".$newpath,"asterisk.log");
		$zip = new ZipArchive();
		util::file_put_contents($newpath,"");
		$zip->open($newpath);
		$zip->addFile($fichier["tmp_name"],"audio.wav");	
		$zip->close();
		
		return "ok";
	}
	
	/**
	* Manager des webservices
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param array $post du script AGI
	*							array	$post[infos][get]
	*							array	$post[infos][post]
	*							array	$post[infos][server]
	*							array	$post[infos][server][argv]
	*/
	public function webservice($post,$session,$files){
log::logger($post,"asterisk.log");
//log::logger($files,"asterisk.log");
		if ($script = array_shift($post["server"])) {
			$firstArg = array_shift($post["server"]);
			if (strpos($firstArg,"/")!==false) {
				// Alors on reçoit un fichier
				log::logger("reception du fichier ".$firstArg,"asterisk.log");
				if ($files) {
					$method = array_shift($post["server"]);
					$secondArg = array_shift($post["server"]);
					$thirdArg = array_shift($post["server"]);
					if (method_exists($this,$method)) {
						log::logger(ATF::$codename.",".$method." : la methode existe","asterisk.log");
						$retour = $this->$method($secondArg,$files,$thirdArg);
						log::logger(ATF::$codename.",".$method."(".$secondArg.",".$files.",".$thirdArg.") => ".$retour,"asterisk.log");
						$s .= 'SET VARIABLE '.$method.' "'.$retour.'"'."\n";			

					} else log::logger(ATF::$codename.",".$method." : la methode n'existe pas","asterisk.log");
				} else log::logger(ATF::$codename.",".$method.' : aucun $_FILES !',"asterisk.log");
				
			} else {
				foreach ($post["server"] as $method) {
					log::logger($method."(".$firstArg.")","asterisk.log");
					if (method_exists($this,$method)) {						
						log::logger(ATF::$codename.",".$method." : la methode existe","asterisk.log");
						$retour = $this->$method($firstArg);
						log::logger(ATF::$codename.",".$method."(".$firstArg.") => ".$retour,"asterisk.log");
						$s .= 'SET VARIABLE '.$method.' "'.$retour.'"'."\n";
					} else log::logger(ATF::$codename.",".$method." : la methode n'existe pas","asterisk.log");
				}
			}
		}
		log::logger(ATF::$codename.",".$_SERVER["REMOTE_ADDR"].",".$script.",".$firstArg." => ".str_replace("\n","|",$s),"asterisk.log");
	
		return $s;
	}
};

