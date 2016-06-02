<?php

class zimbra {
	
	private $ServerAddress = "zimbra.absystech.net";
	private $authToken;
	public $Header;
	
	/**
    * Permet d'envoyer des messages SOAP au serveur
    * @author Antoine MAITRE <amaitre@absystech.fr>
	* @param string $MessageSOAP composé de son header et de son body
	* @return bool false si erreur et true si tout se passe correctement
    */   	
	
	public function Send_Mess_SOAP($MessageSOAP, $username, $password) {
		$this->connect($username, $password, $admin);
		$this->Header_SOAP();
		$MessageSOAP = $this->Header.$MessageSOAP.'
</soap:Envelope>';
		$PostAdress = "https://".$this->ServerAddress."/service/soap/";
		  $CurlHandle = ATF::curl()->curlInit($PostAdress);		
        ATF::curl()->curlSetopt(CURLOPT_POST,           TRUE);
        ATF::curl()->curlSetopt(CURLOPT_RETURNTRANSFER, TRUE);
        ATF::curl()->curlSetopt(CURLOPT_SSL_VERIFYPEER, FALSE);
        ATF::curl()->curlSetopt(CURLOPT_SSL_VERIFYHOST, FALSE);
		ATF::curl()->curlSetopt(CURLOPT_POSTFIELDS, $MessageSOAP);
        $ZimbraSOAPResponse = ATF::curl()->curlExec();
		ATF::curl()->curlClose();
		return $ZimbraSOAPResponse;
	}

	/**
    * Permet de se connecter au serveur SOAP et de recup le authToken
    * @author Antoine MAITRE <amaitre@absystech.fr>
	* @param string $username 
	* @param string $password
	* @param bool $admin définit si le compte est un utilisateur admin ou non
	* @return bool false si erreur et true si tout se passe correctement
    */   	

	public function connect($username, $password){
		$PostAdress = "https://".$this->ServerAddress."/service/soap/";
		ATF::curl()->curlInit($PostAdress);
        ATF::curl()->curlSetopt(CURLOPT_POST,           TRUE);
        ATF::curl()->curlSetopt(CURLOPT_RETURNTRANSFER, TRUE);
        ATF::curl()->curlSetopt(CURLOPT_SSL_VERIFYPEER, FALSE);
        ATF::curl()->curlSetopt(CURLOPT_SSL_VERIFYHOST, FALSE);

        $SOAPMessage  = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
                                <soap:Header>
                                        <context xmlns="urn:zimbra"/>
                                </soap:Header>
                                <soap:Body>
                                        <AuthRequest xmlns="urn:zimbraAccount">
                                                <account by="name">'.$username.'</account>
                                                <password>'.$password.'</password>
                                        </AuthRequest>
                                </soap:Body>
                        </soap:Envelope>';
		ATF::curl()->curlSetopt(CURLOPT_POSTFIELDS, $SOAPMessage);
        $ZimbraSOAPResponse = ATF::curl()->curlExec();
		
        $authToken = strstr($ZimbraSOAPResponse, "<authToken");
        $authToken = strstr($authToken, ">");
        $authToken = substr($authToken, 1, strpos($authToken, "<") - 1);
		if ($authToken === false) {
			$authToken = NULL;
		}
		$this->authToken = $authToken;
		ATF::curl()->curlClose();
 		return $authToken;
	}

	/**
    * Prepare le header pour la requete SOAP
    * @author Antoine MAITRE <amaitre@absystech.fr>
    */   	

	public function Header_SOAP() {
		$SOAPHeader = '<soap:Envelope	
	xmlns:soap="http://www.w3.org/2003/05/soap-envelope"
	xmlns:zimbra="urn:zimbra"
	xmlns:zaccount="urn:zimbraAccount"
	xmlns:zmail="urn:zimbraMail"
	xmlns:zadmin="urn:zimbraAdmin">
	<soap:Header>
		<zimbra:context xmlns="urn:zimbraSoap">
			<zaccount:authToken>'.$this->authToken.'</zaccount:authToken>
			<nonotify/>
			<noqualify/>
		</zimbra:context>
	</soap:Header>';
		$this->Header = $SOAPHeader;
		if ($this->authToken) {
			return $SOAPHeader;
		} else {
			return NULL;
		}
	}

	/**
    * Prepare le body pour la requete SOAP (crée rdv)
    * @author Antoine MAITRE <amaitre@absystech.fr>
	* @param Array $SOAPArray contient les élèment necessaires au remplissage de la fonction
    */   	

	public function Create_Appointment($SOAPArray) {
		$SOAPBody = '
	<soap:Body>
		<zmail:CreateAppointmentRequest >';
		if ($SOAPArray['zid']) {
			$SOAPBody = $SOAPBody.'
			<m l="'.$SOAPArray['zid'].':10">';
		} else {
		 	$SOAPBody = $SOAPBody.'
			<m l="10" >';
		}
			$SOAPBody = $SOAPBody.'
				<su>'.$SOAPArray['subject'].'</su>
					<inv>
						<comp
							name="'.$SOAPArray['name'].'"
							status="CONF"
							fb="B"
							fba="B"
							allDay="'.$SOAPArray['allDay'].'" >
								<s tz="Europe/Brussels" d="'.$SOAPArray['date_debut'].'"/>
								<e tz="Europe/Brussels" d="'.$SOAPArray['date_fin'].'"/>
								<desc>'.$SOAPArray['description'].'</desc>
						</comp>
					</inv>
			</m>
		</zmail:CreateAppointmentRequest >
	</soap:Body>';
		return $SOAPBody;
	}

	/**
    * Prepare le body pour la requete SOAP (supprime rdv)
    * @author Antoine MAITRE <amaitre@absystech.fr>
	* @param string $zid contient l'id zimbra du RDV
    */   	

	public function Cancel_Appointment($zid) {
		$SOAPMessage = '	
	<soap:Body>
		<zmail:CancelAppointmentRequest id="'.$zid.'" comp="0">
			<m l="10">
				<su>Canceling Appointment</su>
					<mp ct="text/plain">
						<content>Canceling This Appointment</content>
					</mp>
			</m>
		</zmail:CancelAppointmentRequest>
	</soap:Body>';
		return $SOAPMessage;
	}
	
	/** Méthode permettant la récupération d'un zid (zimbra ID) à partir d'un calendrier ics généré pour chaque utilisateur
	* @author Antoine MAITRE <amaitre@absystech.fr>
	* @param string login dont on veux récupérer le zid
	* @return bool en fonction de si l'exécution se passe bien ou pas
	*/
	
	public function user_zid($login) {
		$tmp = fopen('/tmp/tmp.ics', 'w+');
		if ($tmp == false) {
			return false;
		}
		ATF::curl()->curlInit("https://".$this->ServerAddress."/service/home/".$login['username']."/".rawurlencode($login['calendar_name']));
		ATF::curl()->curlSetopt(CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		ATF::curl()->curlSetopt(CURLOPT_USERPWD, $login['username'].':'.$login['password']);
		ATF::curl()->curlSetopt(CURLOPT_RETURNTRANSFER, true);
		ATF::curl()->curlSetopt(CURLOPT_FILE, $tmp);
        ATF::curl()->curlSetopt(CURLOPT_SSL_VERIFYPEER, FALSE);
        ATF::curl()->curlSetopt(CURLOPT_SSL_VERIFYHOST, FALSE);
		
		ATF::curl()->curlExec();
		ATF::curl()->curlClose();
		fclose($tmp);
		$ics = file_get_contents('/tmp/tmp.ics');

		preg_match('#X-WR-CALID:([0-9a-z\-]+):10#', $ics, $matches);
		$user = strstr($login['username'], '@', true);
		ATF::user()->q->reset()->addField('id_user')->where('login', $user);
		$id_user = ATF::user()->select_cell();
		$infos['id_user'] = $id_user; 
		$infos['zid'] = $matches[1];
		if (!$infos['zid']) {
			return false;
		}
		$zid = $infos['zid'];
		ATF::user()->update($infos);
		unlink('/tmp/tmp.ics');
		return true;
	}

}