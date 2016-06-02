<?
require_once dirname(__FILE__)."/../emailing_projet.class.php";
/** 
* Classe emailing_projet, gère les projets d'emailing
* @package Optima
* @author Quentin JANON <qjanon@absystech.fr>
*/
class emailing_projet_pvr extends emailing_projet {

	function __construct() {
		parent::__construct();

	}
	 
	/** 
	* Se connecte a Chrome et récupère les vidéos depuis la dernière sollicitation
	* @package Optima
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
	public function getVideos($mail, $last_sollicitation=false,$id_emailing_job_email=false) {		

		ATF::define_db("pvrDB","pvr_".(__DEV__===true?"dev":"prod"),(__DEV__===true?"speedmail":"speedmail_prod"),"zEL5X7s9ZYquXMzW","chrome.absystech.net",3306);

		ATF::emailing_contact()->q->reset()->where('email',$mail);
		// On ne sélectionne qu'une ligne avec le select_row
		$ec = ATF::emailing_contact()->select_row();

		// On résupère sous forme d'array les tags lié au contact
		$tags = ATF::emailing_contact()->getAbonnement($ec['id_emailing_contact'],false);
		$total = 0;
		//log::logger($tags,"speedmailDEBUGPVR");
		foreach($tags as $k=>$i) {
			$q = "SELECT * FROM video
				LEFT JOIN video_tag ON video_tag.id_video=video.id_video
				LEFT JOIN tag ON tag.id_tag=video_tag.id_tag
				WHERE tag.tag='".ATF::db('pvrDB')->real_escape_string($i['abonnement'])."'";

			// Soit on force la date pour le viewer HTML par exemple, soir on prend celle de l'EC.
			$ls = $last_sollicitation?$last_sollicitation:$ec['last_sollicitation'];
			if ($ls) {
				$q .= " AND date_creation>'".$ls."'";
			}

			$q .= " LIMIT 3";			

			$q2 = "SELECT COUNT(*) as total FROM video
				LEFT JOIN video_tag ON video_tag.id_video=video.id_video
				LEFT JOIN tag ON tag.id_tag=video_tag.id_tag
				WHERE tag.tag='".ATF::db('pvrDB')->real_escape_string($i['abonnement'])."'";

			if ($ls) {
				$q2 .= " AND date_creation>'".$ls."'";
			}
			//log::logger($i,"speedmailDEBUGPVR");
			log::logger(ATF::db('pvrDB')->real_escape_string($i['abonnement']),"speedmailDEBUGPVR");
			$v = ATF::db('pvrDB')->sql2array($q);
			log::logger($v,"speedmailDEBUGPVR");
			foreach ($v as $k_=>$i_) {
				if ($i_['url']) {
					$q = "SELECT url_front FROM chaine WHERE id_chaine=".$i_['id_chaine_owner'];

					if ($vhost = ATF::db('pvrDB')->fetch_first_cell($q)) {

						$url = $vhost."/".$i_['url'].".html";
						if ($lien = ATF::emailing_lien()->select_special("url",$url)) {
							$id_emailing_lien = $lien[0]['id_emailing_lien'];
						} else {
							$lien = array("url"=>$url,"emailing_lien"=>$i_['name']);
							$id_emailing_lien = ATF::emailing_lien()->i($lien);							
						}
						$v[$k_]['speedmailLink'] = __ABSOLUTE_WEB_PATH__."speedmail.php?idel=".md5($id_emailing_lien)."&ideje=".($id_emailing_job_email?md5($id_emailing_job_email):NULL)."&societe=".base64_encode(ATF::$codename);
					}
				}

				if ($i_['id_chaine_owner']) {
					$q = "SELECT chaine FROM chaine WHERE id_chaine=".$i_['id_chaine_owner'];

					$v[$k_]['chaine'] = ATF::db('pvrDB')->fetch_first_cell($q);
					$v[$k_]['chaineLogo'] = (__DEV__===true?"http://dev.wall.ra.novastream.fr/":"http://www.rhonealpes.tv/")."common/media/logo-chaine/".$v[$k_]['chaine'].".png";
				}

				$id_tag = $i_['id_tag'];
			}

			$lsFormated = preg_replace("#[-| |:]#","",$ls);
			$lienAll = (__DEV__===true?"http://dev.wall.ra.novastream.fr/":"http://www.rhonealpes.tv/")."search/tag-".$id_tag."/".$lsFormated."-".addslashes($i['abonnement']);
			ATF::emailing_lien()->q->reset()->where("url",$lienAll);
			if ($lien2 = ATF::emailing_lien()->sa()) {
				$id_emailing_lien2 = $lien2[0]['id_emailing_lien'];
			} else {
				$lien2 = array("url"=>$lienAll,"emailing_lien"=>"Toutes les vidéos : ".$i['abonnement']);
				$id_emailing_lien2 = ATF::emailing_lien()->i($lien2);
			}
			$lienAllURL = __ABSOLUTE_WEB_PATH__."speedmail.php?idel=".md5($id_emailing_lien2)."&ideje=".($id_emailing_job_email?md5($id_emailing_job_email):NULL)."&societe=".base64_encode(ATF::$codename);
			//die("URL = ".$lienAll." - ".$lienAllURL);
			$t = ATF::db('pvrDB')->ffc($q2);
			$total += $t;
			if ($t) {
				$videos[$i['abonnement']] = array("total"=>$t,"videos"=>$v,"lienAll"=>$lienAllURL);
			}

		}		
		$videos['total'] = $total;
		return $videos;
	}
};
?>