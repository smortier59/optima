<?
/**
 * Classe suivi
 * @package Optima
 */
require_once dirname(__FILE__)."/../suivi.class.php";
class suivi_absystech extends suivi {
	public function __construct() {
		parent::__construct($table_or_id);

		$this->colonnes['fields_column']["audio"] = array("width"=>50,"custom"=>true,"nosort"=>true,"type"=>"file");

		$this->colonnes['primary']["suivi_from_mail"] = array("custom"=>true);
		$this->fieldstructure();

		$this->files["audio"] = array("type"=>"wav","preview"=>false,"no_upload"=>true,"no_generate"=>true);
	}


	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){

		if($infos["suivi"]["suivi_from_mail"][0]){
			$mail = 'optima.'.ATF::$codename.'.suivi@absystech.net';
			$host = "zimbra.absystech.net";
			$port = 143;
			$password = ATF::$codename=="att"?"P272nqX4tG":"43b52RaNNz";

			ATF::imap()->init($host, $port, $mail, $password);
			if (ATF::imap()->error) { throw new errorATF(ATF::imap()->error);	}
			$mails = ATF::imap()->imap_fetch_overview('1:*');

			if (is_array($mails)){
				$i=0;
				while ($mails[$i]->subject." | ".$mails[$i]->from != $infos["suivi"]["suivi_from_mail"][0]) {
					$i++;
				}
				$mail = $mails[$i];


				$text = ATF::imap()->returnBody($mail->uid);
				$piece_jointe = ATF::imap()->get_attachments($mail->uid);

				$infos["suivi"]["texte"] .= "\n\nMail :\n\n".$text;

			}
		}
		unset($infos["suivi"]["suivi_from_mail"]);

		$last_id = parent::insert($infos,$s,NULL,$var=NULL,NULL,true);


		if($mail && $piece_jointe){

			foreach ($piece_jointe as $key => $value) {
				$pj = ATF::imap()->get_attachments($mail->uid, $value["filename"]);

				$extension = explode(".", $value["filename"]);

				$extension = $extension[count($extension) - 1];

				if($extension == "WAV"){
					ATF::suivi()->store(ATF::_s(), $last_id, "audio", $pj);
				}else{
					ATF::suivi()->store(ATF::_s(), $last_id, "fichier_joint", $pj);
				}
			}
		}
		return $last_id;
	}

	public function update($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		if($infos["suivi"]["suivi_from_mail"][0]){
			$mail = 'optima.'.ATF::$codename.'.suivi@absystech.net';
			$host = "zimbra.absystech.net";
			$port = 143;
			$password = ATF::$codename=="att"?"P272nqX4tG":"43b52RaNNz";

			ATF::imap()->init($host, $port, $mail, $password);
			if (ATF::imap()->error) { throw new errorATF(ATF::imap()->error);	}
			$mails = ATF::imap()->imap_fetch_overview('1:*');


			if (is_array($mails)){
				$i=0;
				while ($mails[$i]->subject." | ".$mails[$i]->from != $infos["suivi"]["suivi_from_mail"][0]) {
					$i++;
				}
				$mail = $mails[$i];


				$text = ATF::imap()->returnBody($mail->uid);
				$piece_jointe = ATF::imap()->get_attachments($mail->uid);

				$infos["suivi"]["texte"] .= "\n\nMail :\n\n".$text;


			}
		}
		unset($infos["suivi"]["suivi_from_mail"]);

		parent::update($infos,$s,NULL,$var=NULL,NULL,true);

		$last_id = $infos["id_suivi"];

		if($mail && $piece_jointe){

			foreach ($piece_jointe as $key => $value) {
				$pj = ATF::imap()->get_attachments($mail->uid, $value["filename"]);

				$extension = explode(".", $value["filename"]);

				$extension = $extension[count($extension) - 1];

				if($extension == "WAV"){
					ATF::suivi()->store(ATF::_s(), $last_id, "audio", $pj);
				}else{
					ATF::suivi()->store(ATF::_s(), $last_id, "fichier_joint", $pj);
				}
			}
		}
		return $last_id;


	}

};

class suivi_att extends suivi_absystech { };
class suivi_atoutcoms extends suivi_absystech { };
class suivi_nco extends suivi_absystech { };
?>
