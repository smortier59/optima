<?
/** Classe suivi_from_mail
* @package Optima
* @subpackage Absystech
*/
class suivi_from_mail extends classes_optima {

	/**
	 * Permet de checker la boite mail zimbra, recuperer les mails et recuperer les fichiers scanné
	 * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	 *
	*/
	public function recup_mail_suivi(){
		$mail = 'optima.'.ATF::$codename.'.suivi@absystech.net';
		$host = "zimbra.absystech.net";
		$port = 143;
		$password = ATF::$codename=="att"?"P272nqX4tG":"43b52RaNNz";

		ATF::imap()->init($host, $port, $mail, $password);
		if (ATF::imap()->error) {	throw new errorATF(ATF::imap()->error); }
		$mails = ATF::imap()->imap_fetch_overview('1:*');

		if (is_array($mails)){
			foreach ($mails as $val) {
				if($val->seen != 1){
					$options[] = array($val->uid, $val->subject." | ".$val->from);
				}
			}
		}
		ATF::imap()->imap_expunge();
		return $options;
	}
}
?>
