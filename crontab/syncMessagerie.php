<?
define("__BYPASS__",true);
include(dirname(__FILE__)."/../global.inc.php");

ATF::$codename = "absystech";

foreach (ATF::user()->sa() as $usr) {
	ATF::$usr->set('id_user',$usr['id_user']);
	$custom = ATF::user()->select($usr['id_user'],"custom");

	$pref = unserialize($custom);

	$conf = $pref["messagerie"];

	if (!$conf) {
		echo "Pas de configuration pour l'utilisateur '".$usr['login']."'...\n\n";
		continue;
	}
	echo "Connexion pour l'utilisateur '".$usr['login']."'...";
	$mbox = new imap($conf['host'],$conf['port'],$conf['username'],$conf['password'],$conf['folder']);
	echo " OK \n";
	echo "Overview lancé...";
	$overview = $mbox->imap_fetch_overview("1:300",0);
	echo " OK \n";
	$c = count($overview);

	foreach ($overview as $k=>$i) {
		echo "Synchronisation du mail $k/$c...";

		if (ATF::messagerie()->isSync($usr['id_user'],$i->msgno)) {
			echo "Deja faite !\n";
		} elseif (!strtotime($i->date)) {
			echo "Impossible la date est vraquee : '".$i->date."'\n";
		} elseif (!$i->from) {
			echo "Impossible il n'y a pas d'expediteur\n";
		} else {
			$h = $mbox->imap_headerinfo($i->msgno);
			$attachments = $mbox->get_attachments($i->uid);
			$insert = array(
				'subject'=>$h->subject,
				'from'=>$h->from,
				'to'=>$h->to,
				'date'=>$h->date,
				'message_id'=>$h->message_id,
				'size'=>$h->Size,
				'uid'=>$i->uid,
				'msgno'=>$h->Msgno,
				'recent'=>$i->recent,
				'flagged'=>$i->flagged,
				'answered'=>$i->answered,
				'deleted'=>$i->deleted,
				'seen'=>$i->seen,
				'draft'=>$i->draft,
				'udate'=>$i->udate,
				'id_user'=>$usr['id_user']
			);
			foreach ($attachments as $kpj=>$ipj) {
				if ($kpj) $insert['attachments'] .= ",";
				if ($kpj) $insert['attachmentsRealName'] .= ",";
				$insert['attachments'] .= $mbox->decodeMimeString($ipj['filename']);
				$insert['attachmentsRealName'] .= $ipj['filename'];
			}
			if ($id = ATF::messagerie()->insert($insert)) {

				if ($h->from && $contact = ATF::contact()->ss('email',addslashes($h->from))) {

					$ct = $mbox->get_body($mbox->stream, $i->uid);
					$suivi = array(
						"id_user"=>$usr['id_user']
						,"id_societe"=>$contact[0]['id_societe']
						,"type"=>'email'
						,"date"=>date("Y-m-d H:i:s",strtotime($h->date))
						,"suivi_contact"=>ATF::contact()->cryptId($contact[0]['id_contact'])
						,"suivi_notifie"=>ATF::user()->cryptId($usr['id_user'])
						,"suivi_societe"=>ATF::user()->cryptId($usr['id_user'])
						,"texte"=>"Texte original :\n".$mbox->decodeMimeString($ct['content'])
					);
					if ($attachments) {
						$suivi['filestoattach']['fichier_joint'] = true;
						foreach ($attachments as $pj) {
							$pj['content'] = $mbox->get_attachments($i->uid,$pj['filename']);
							$pj['filename'] = $mbox->decodeMimeString($pj['filename']);
							$fp = ATF::suivi()->filepath($usr['id_user'],"fichier_joint.".$pj['filename'],true);
							util::file_put_contents($fp,$pj['content']);
						}
					}
					
					if ($id = ATF::suivi()->insert($suivi)) {
						echo "Suivi creer...";	
					}
				}
				echo "REUSSIE\n";	
				
			} else {
				echo "ECHOUEE\n";	
			}
		}
	}
}

?>