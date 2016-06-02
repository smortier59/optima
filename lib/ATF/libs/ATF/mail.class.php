<?php
/**
* La classe mail d'ATF centralise tous les emails envoyés via le projet.
* Ces envois peuvent être formaté par templates Smarty, et il est géré
* la séparation texte plein et HTML. De plus l'ajout de fichier joint
* a été implémenté et l'encodage automatique en MIME Mail des objets.
*
* @date 2009-04-13
* @package ATF
* @version 6
* @author Yann GAUTHERON <ygautheron@absystech.fr>
* @example
		$mail = new mail(array(
				"user"=>$user
				,"template"=>"defi"
				,"from"=>$user->infos["prenom"]." ".$user->infos["nom"]." <".$user->infos["email"].">"
				,"extra_info_1"=>1
				,"extra_info_1"=>"deux"));
		$mail->send("ygautheron@absystech.fr");

Disposition des bloc MIME :
--related
	--alternative
		text/plain
	--alternative
		text/html
	--alternative--
--related
	png embeded
--related
	png embeded
--related
	file attached
--related--

*/
class mail {

	/**
	* Infos passées en paramètre constructeur , ces infos sont passées en tant que variable $infos aux templates de TEXTE PLEIN et HTML utilisés
	* @var array
	*		objet string : Objet de l'email envoyé, si vide l'objet est automatiquement cherché sous la forme "mail_$template_objet" dans le fichier de traduction
	*		from string : Expéditeur
	*		user user : Utilisateur nécessaire pour la localisation
	*		body string : Corps du mail, si aucun template défini
	*		return_path string : Adresse de retour du mail en cas d'erreur de boite pleine etc...
	*		recipient : destinataire défini directement plutôt qu'à l'utilisation de la méthode $this->send
	*		template : body remplacé par template smarty de ce nom (*.tpl.txt)
	*		template_only : Ne pas encapsuler par l'ossature mail.tpl.htm autour du template demandé
	*		html : body HTML en plus du texte plein (*.tpl.htm)
	*/
	private $infos;

	/**
	* Nom de code schema du site, sert aux recherche de templates dans un répertoire prioritaire correspondant à ce nom
	* @var string
	*/
	private $codename;

	/**
	* Contenu du mail en texte
	* @var mixed
	*/
	public $plain;

	/**
	* Contenu du mail en html
	* @var mixed
	*/
	private $html;

	/**
	* Fichiers en base_64
	* @var string
	*/
	private $fichiers;

	/**
	* Headers spécifiques
	* @var array
	*/
	private $customHeaders = array();

	/**
	* Chemin vers les fichiers à copier uniquement lors du send()
	* @var array
	*		filename => filepath
	*		filename => filepath
	*		filename => filepath...
	*/
	private $deferedFiles;

	/**
	* Fichiers images embarqués et utilisés dans l'email HTML en base_64
	* @var string
	*/
	private $images;

	/**
	* Chaîne globale du corps de l'email
	* @var string
	*/
	private $body;

	/**
	* Email qui intercepte tous les email (aucun email n'arrive au destinataire prévu)
	* @var string
	*/
	private $interceptor;

	/**
	* Envoi à une adresse esptionne tous les emails envoyés
	* @var string
	*/
	private $mailcopy;

	/**
	* Tableau enregistrant tous les emails envoyés
	* @var array
	*/
	public $sent;

	/**
	* Construit un nouveau mail.
	* @param array $infos Toutes les infos du mail !
	* @return mail
	*/
	public function __construct($infos) {
		$this->infos = $infos;
		if (!$infos["objet"] && $infos["template"]) { /* Traduction automatique de l'objet du mail par rapport à celui prévu par le nom template dans la localisation */
			if ($infos["user"] instanceof usr) {
				$this->infos["objet"] = $infos["user"]->trans("objet","mail_".$infos["template"]);
			} else {
				$this->infos["objet"] = loc::ation("objet","mail_".$infos["template"]);
			}
		}

		if (!$infos["from"]) { /* Expéditeur par défaut */
			$this->infos["from"] = ATF::$mailfrom;
		}

		/* Schema utilisé, on a donc un codename */
		if (ATF::$codename) {
			$this->codename = ATF::$codename;
		}

		/* Copie automatiquement vers un email espion */
		if ($infos["mailcopy"]) {
			$this->mailcopy = $infos["mailcopy"];
		} elseif (ATF::$mailcopy) {
			$this->mailcopy = ATF::$mailcopy;
		}

		/* Préfixe */
		if(ATF::$mailprefix){
			$this->infos["objet"] = ATF::$mailprefix." ".$this->infos["objet"];
		}

		/* Interception de tous les emails (si DEV ou TEST) */
		if ((ATF::isTestUnitaire() || __DEV__===true) && !ATF::$mailinterceptor) {
			if (ATF::isTestUnitaire()) {
				ATF::define("mailinterceptor","tu@absystech.net");
			} else {
				ATF::define("mailinterceptor",ATF::$debugMailbox);
			}
		}
		if (ATF::$mailinterceptor && !$this->infos["noInterceptor"]) {
			$this->interceptor = ATF::$mailinterceptor;
			$this->interceptor_object = (ATF::isTestUnitaire() ?"TU ": (__DEV__===true ? "DEV " : "INTERCEPTION ")).ATF::$project.($this->codename?"(".$this->codename.")":NULL);
		}

		/* Return path par défaut (doit etre une mailbox existante, pour éviter de passer pour du spam !) */
		if (!$this->infos["return_path"]) {
			$this->infos["return_path"] = "no-reply@absystech.fr";
		}
	}

	/**
	* Ajoute une chaine binaire en fichier joint sous forme d'une string base_64
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param $data Données binaires
	* @param $filename Nom du fichier
	*/
	public function addBinary($data,$filename) {
		$this->fichiers .= "\n\n\n--00001Mixed\nContent-Type: application/octet-stream\nContent-Transfer-Encoding: base64\nContent-Disposition: attachment; filename=\"".urlencode($filename)."\"\n\n".chunk_split(base64_encode($data));
	}

	/**
	* Défini l'email où envoyer les notification de lecture
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param array $headers
	*/
	public function setNotificationsTo($email) {
		$this->setCustomHeaders(array(
			"X-Confirm-Reading-To"=>$email,
			"Disposition-Notification-To"=>$email
		));
	}

	/**
	* Défini les headers spécifiques (notification, etc...)
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param array $headers
	*/
	public function setCustomHeaders(array $headers) {
		$this->customHeaders = $headers;
	}

	/**
	* Ajoute un fichier joint sous forme d'une string base_64
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $filepath Chemin vers le fichier
	* @param string $filename Nom du fichier, par défaut celui du chemin $filepath
	* @param boolean $deferedFiles Copier lefichier au moment du send() uniquement (délayer)
	*/
	public function addFile($filepath,$filename=NULL,$deferedFiles=false) {
		if ($deferedFiles) {
			$this->deferedFiles[$filename] = $filepath;
		} else {
			if (!$filename) {
				$filename = basename($filepath);
			}
			if (!file_exists($filepath)) {
				throw new errorATF("file_not_found ".$filepath,9);
			}
			$this->fichiers .= "\n\n\n--00001Mixed"
			. "\nContent-Type: application/octet-stream"
			. "\nContent-Transfer-Encoding: base64";

			$this->fichiers .= "\nContent-Disposition: attachment; filename=\"".$filename."\""
			. "\n\n".chunk_split(base64_encode(file_get_contents($filepath)));
		}
	}

	/**
	* Ajoute un fichier joint sous forme d'une string base_64
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $filepath Chemin vers le fichier
	* @return Retourne l'adresse Content-ID à emttre en source
	*/
	public function addEmbeddedImage($filepath) {
		$filepath = $this->getCachedFilePath($filepath);
		if (!$filename) {
			$filename = basename($filepath);
		}
		$finfo = new finfo(FILEINFO_MIME_TYPE); // Pour récupérer le type de fichier
		$cid = $filename."@absystech.fr";
		$this->images .= "\n\n\n--00002Related"
		. "\nContent-Type: ".$finfo->file($filepath)
		. "\nContent-Transfer-Encoding: base64"
		. "\nContent-ID: <".$cid.">"
		. "\nContent-Disposition: attachment; filename=\"".urlencode($filename)."\""
		. "\n\n".chunk_split(base64_encode(file_get_contents($filepath)));

		return "cid:".$cid;
	}

	/**
	* Retourne le chemin vers le fichier sur le disque dur s'il a déjà été mis en cache, utile pour les images distantes
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $filepath Chemin vers le fichier
	* @return Retourne le meilleur chemin
	*/
	public function getCachedFilePath($filepath) {
		if(strpos($filepath,"://")!==false) {
			$headers = get_headers($filepath,1);

			$md5 = md5($filepath.$headers["Last-Modified"]);
			$path = __TEMP_PATH__."cacheEmail/";
			if (!is_dir($path)) {
				mkdir($path);
				chmod($path,0777);
			}
			if (file_exists($path.$md5)) {
				return $path.$md5;
			} else {
//				util::transverseFile($path.$md5,$filepath);
                $ch = curl_init($filepath);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);
				file_put_contents($path.$md5,$result);
                curl_close($ch);
				chmod($path.$md5,0777);
				return $path.$md5;
			}
		} else {
			// Cache uniquement si fichier distant
			return $filepath;
		}
	}

	/**
	* Création de l'objet Smarty
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	*/
	private function smarty() {
		$smarty = new Smarty_ATF($this->infos["user"]);
		$smarty->assign("email",true);
		return $smarty;
	}

	/**
	* Echappe les caractères spéciaux UTF8 en caractères encodés de la manière déclarée en RFC
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param $subject string (Doit être UTF8)
	* @return string
	*/
	public function mail_escape_header($subject) {
		$subject = preg_replace_callback('/([^a-z ])/i', 'sprintf("=%02x",ord(stripslashes("\\1")))', $subject);
		$subject = str_replace(' ', '_', $subject);
		return "=?utf-8?Q?".$subject."?=";
	}

	/**
	* Echappe les caractères spéciaux UTF8 en caractères encodés de la manière déclarée en RFC , sur la partie avant l'email <>
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param $subject string (Doit être UTF8)
	* @return string
	*/
	private function mail_escape_address($address) {
		if (($pos = strpos($address,"<"))>-1) {
			$address = $this->mail_escape_header(substr($address,0,$pos-1)).substr($address,$pos);
		}
		return $address;
	}

	/**
	* Crée l'entête MIME
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	*/
	private function mime() {
		if ($this->infos["from"]) {
			$from = $this->mail_escape_address($this->infos["from"]);
			$replyTo = $from;
		}
		$this->mime = ($this->infos["from"]?"From: ".$from."\n":NULL)
			.($replyTo?"Reply-to: ".$replyTo."\n":NULL)
			.($this->infos["return_path"]?"Return-Path: ".$this->infos["return_path"]."\n":NULL)
			."Message-Id: <".md5(uniqid(microtime()))."@".str_replace("http:","",str_replace("https:","",str_replace("/","",__MANUAL_WEB_PATH__))).">\n"
			."Content-Encoding: UTF-8\n"
			."Content-Transfer-Encoding: 8bit\n"
			."MIME-Version: 1.0:\n"
			."Content-Type: multipart/mixed; charset=UTF-8; boundary=00001Mixed\n";
		foreach($this->customHeaders as $h => $v) {
			$this->mime .= $h.": ".$v."\n";
		}
	}

	/**
	* Crée le body en multipart/alternative
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	*/
	private function body() {
		$this->body .= "--00001Mixed\nContent-Type: multipart/related; charset=UTF-8; boundary=00002Related\n\n"
			."--00002Related\nContent-Type: multipart/alternative; charset=UTF-8; boundary=00003Alternative\n\n";

		/* Template TEXTE PLEIN */
		$this->plain = $this->smarty();
		$this->plain->assign("infos",$this->infos);
		if ($this->infos["template"] && $this->plain->template_exists("mail/".$this->infos["template"].".tpl.txt")) {
			if ($this->infos["template_only"]) {
				$plain = $this->plain->fetch("mail/".$this->infos["template"].".tpl.txt");
			} else {
				$plain = $this->plain->fetch("mail/mail.tpl.txt");
			}
		} else {
			$plain = $this->infos["body"];
		}
		$this->body .= "--00003Alternative\nContent-Type: text/plain; charset=UTF-8;\n\n".$plain."\n\n";

		/* Template HTML */
		$this->html = $this->smarty();
		if ($this->infos["template"] && $this->html->template_exists("mail/".$this->infos["template"].".tpl.htm")) {
			$this->html->assign("infos",$this->infos);
			$this->html->assignByRef("emailObj",$this); // Sert aux ajouts d'images depuis les templates
			if ($this->infos["template_only"]) {
				$htm = $this->html->fetch("mail/".$this->infos["template"].".tpl.htm");
			} elseif($this->html->template_exists(ATF::$codename."/mail/mail.tpl.htm")){
				$htm = $this->html->fetch(ATF::$codename."/mail/mail.tpl.htm");
			} else {
				// Cette ligne est tres consommatrice en ressource, on passe de 4 a 1 seconde lors de l'envoi d'un mail... Il faudrait voir si c'eest le fetch Smarty qui est lourd ou autre chose et surtout, le régler...
				$htm = $this->html->fetch("mail/mail.tpl.htm");
			}

			$this->body .= "--00003Alternative\nContent-Type: text/html; charset=UTF-8;\n\n".$htm."\n\n";
		}

		$this->body .= "--00003Alternative--";

		if($this->images){
			// Avec des images embarquées
			$this->body .= "\n\n".$this->images;
		}

		$this->body .= "\n\n--00002Related--";

		if($this->fichiers){
			// Avec des fichier attachés
			$this->body .=  "\n\n".$this->fichiers;
		}

		$this->body .= "\n\n--00001Mixed--";
	}

	/**
	* Envoi un e-mail multipart/alternative
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param $email string Destinataire (adresse email)
	* @param $objet string
	* @return boolean TRUE si correctement envoyé, FAUX sinon
	*/
	private function sendmail($email=NULL,$objet=NULL) {

		if (!$objet) { // Si aucun objet en paramètre on prend l'objet des infos de cet objet
			$objet = $this->infos["objet"];
		}

		// Objet
		$objet = $this->mail_escape_header($objet);

		// Trajet de retour en cas d'erreur
		if ($this->infos["return_path"]) {
			$return_path = " -f ".$this->infos["return_path"];
		}

		// Stockage des emails envoyés
		$this->sent[] = array(
			"email"=>$email
			,"objet"=>$objet
			,"body"=>$this->body
			,"mime"=>$mime
			,"return_path"=>$return_path
		);
		return mail($email,$objet,$this->body,$this->mime,$return_path);
	}

	/**
	* Vérification d'une adresse mail
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param $email string Destinataire (adresse email)
	* @return boolean TRUE si emails corrects
	*/
	public static function check_mail($mails){
		//Pour tous ceux qui utilisent les ; pour séparateur
		$mails=str_replace(';',',',$mails);
		$lmail=explode(',', $mails);
		foreach($lmail as $key=>$mail){
			//S'il existe
			if (!$mail) {
				throw new errorATF(ATF::$usr->trans("email_non_specifie"),1001);
			}
			//S'il a une bonne structure syntaxique
			if ( !util::isEmail($mail) ) {
				throw new errorATF(ATF::$usr->trans("email_non_valide")." ".$mail,1002);
			}
			//S'il le domaine existe
			list( $Username, $Domain ) = explode( '@', $mail );

			if (!checkdnsrr($Domain) ) {
				throw new errorATF(ATF::$usr->trans("domain_invalid")." ".$Domain,1003);
			}
		}

		return true;
	}


	/**
	* Envoyer un email
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $email Destinataire (adresse email)
	* @param boolean $no_interceptor ATTENTION : si TRUE, le mail sera forcément envoyé au destinataire même si le projet est environnement DEV ou TEST
	* @param boolean $force Force l'envoi de mail : on peut alors désactiver la transaction ou permettre l'envoi de mail dans des transactions intermédiares (utilisée par la queue) NE PAS ACTIVER EN TEMPS NORMAL.
	* @return boolean TRUE si correctement envoyé ou mis en queue, FAUX sinon
	* @todo Problème du $this->db pour les transactions => L'utilisation de force peut outrepasser ce problème.
	*/
	public function send($email=NULL,$no_interceptor=false,$force=false){
		if (!$email) {
			$email = $this->infos["recipient"];
		}
		$email = trim($email);
		$this->check_mail($email);
		if(!$force && ATF::db($this->db)->isTransaction()){
			ATF::db($this->db)->getQueue()->sendEmail($this,$email);
			return true;
		} else {
			/* Des fichiers sont à embarquer seulement lors du send() */
			if ($this->deferedFiles) {
				foreach ($this->deferedFiles as $filename => $filepath) {
					$this->addFile($filepath,$filename);
				}
			}
			$this->body();
			$this->mime();

			/* Interception de tous les emails (utilisé en DEV et en TU) */
			if ($this->interceptor && $no_interceptor!==true) {
				$this->infos["objet"] ="[".$this->interceptor_object." Destinataire : ".$email.($this->mailcopy ? " + mailcopy(".$this->mailcopy.")" : NULL)."] ".$this->infos["objet"];
				$email = $this->interceptor;
			}

			/* Si pas d'intercepteur, copie automatique supplémentaire (configuré via la constante ATF mailcopy ou passé en paramètre du constructeur du mail ) */
			elseif ($this->mailcopy) {
				 $this->sendmail($this->mailcopy,"[MAILCOPY Destinataire : ".$email."] ".$this->infos["objet"]);
			}

			return $this->sendmail($email);
		}
	}

	/**
	* Renvoie les infos du mail
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	*/
	public function getInfos($field){
		if($field){
			return $this->infos[$field];
		}else{
			return $this->infos;
		}
	}

	/**
	* Intialise les infos du mail
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
	public function setInfos($data,$field=NULL){
		if($field){
			return $this->infos[$field] = $data;
		}else{
			return $this->infos = $data;
		}
	}
};
?>
