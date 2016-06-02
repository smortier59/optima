<?
/**  
* @package Optima
* @subpackage AbsysTech
*/
class jungledisk extends classes_optima {
	public function __construct(){
		parent::__construct();
		$this->table=__CLASS__;
		$this->colonnes['fields_column'] = array('jungledisk.jungledisk','jungledisk.email');
		$this->colonnes['primary']=array("jungledisk","email");
		
		$this->fieldstructure();
	}
	
	/** Converti l'email en bannière AbsysTech
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $source HTML converti
	* @return void
	*/
	function convertirEnAbsysTech(&$source) {
		$source = str_replace("<div>Manage your e-mail subscriptions at: <a href='http://www.jungledisk.com/secure/account/backupreporting.aspx'>http://www.jungledisk.com/secure/account/backupreporting.aspx</a>.</div>","",$source);
		$source = str_ireplace("jungledisk","AbsysTech",$source);
		$source = str_ireplace("jungle disk","AbsysTech",$source);
	}
	
	/** Retourne l'id_jungledisk correspondant au mot clé après "Computer:"
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $jungledisk Mot clé
	* @return int
	*/
	function getIDFromKeyword($jungledisk) {
		$this->q->reset()->addCondition('jungledisk',$jungledisk)->addField('id_jungledisk')->setStrict()->setDimension('cell');
		return $this->sa();
	}
	
	/** Retourne le mot clé après "Computer:"
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param source HTML converti
	* @return string mot clé
	*/
	function getKeywordFromHTML($source) {
		$match = preg_match_all('/font-size: 10px;">Computer<\/th><td style="padding:5px; margin:0; background:#fff;">(.[^<]*)<\/td>/',$source,$matches);
		return $matches[1][0];
	}
	
	/** Récupère les données jungledisk sur la mailbox
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return boolean
	*/
	function parseMailbox() {
//error_reporting(E_ALL);		
		$mbox = imap_open("{zimbra.absystech.net:143/imap/notls}INBOX",'atbackup@absystech.fr','az78qs45') or die("Erreur imap_open : ".imap_last_error());
//log::logger("logged sur imap",'ygautheron');		
		$NB = imap_num_msg($mbox);
//log::logger("nb messages ".$NB,'ygautheron');		
		$overview = imap_fetch_overview($mbox, "1:$NB",0);
		if (is_array($overview)) {
//log::logger("nb = ".count($overview),'ygautheron');		
			foreach ($overview as $val) {
				$set = NULL;
				
				// Seulement si on a pas encore lu le message
				if (!$val->seen) {
//log::logger($val,'ygautheron');		
					$body = imap_fetchbody($mbox,$val->msgno,2);
					if (!$body) {
						$body = imap_fetchbody($mbox,$val->msgno,1);
					}
//log::logger($body,'ygautheron');		
					$body = base64_decode($body);
					
					// Récupère le mot clé
					if ($keyword = $this->getKeywordFromHTML($body)) {
//log::logger($keyword,'ygautheron');	
						
						// Récupère l'identifiant de base
						$id_jungledisk = $this->getIDFromKeyword($keyword);
//log::logger($id_jungledisk,'ygautheron');	

						
						// Envoi de l'email transformé
						$jungledisk = $this->select($id_jungledisk);
//log::logger($jungledisk,'ygautheron');	
//print_r($jungledisk);
						
						// On vire les annotations à Jungledisk, remplace par AbsysTech
						$this->convertirEnAbsysTech($body);
//print_r($keyword);
						
						// Envoi du mail
						mail($jungledisk["email"]/*.",smortier@absystech.fr"*/,$val->subject,$body,"From: Backup AbsysTech <atbackup@absystech.fr>\nContent-Type: text/html\n\n");				
						
					} else {
//log::logger("Cet email n'est pas un rapport Jungledisk",'ygautheron');	
					}
					
					// On mémorise le message comme  lu
					imap_setflag_full($mbox,$val->msgno,"\\Seen");				
				}
			}
		}
		imap_expunge($mbox);
		imap_close($mbox);
	}
};
?>
