<?
/**
* @package Optima
*/
class messagerie extends classes_optima {
	
	private $mailbox;
	private $limitNbMsgSync = 30;
	
	function __construct() { //hé
		parent::__construct();
		$this->table = __CLASS__;
		$this->colonnes["fields_column"] = array(	
			'messagerie.seen'=>array("width"=>30,"renderer"=>"seenUnseenMail","hideLabel"=>true,"body"=>true)
			,'messagerie.subject'=>array("width"=>550)
			,'messagerie.from'
			,'messagerie.to'
			,'messagerie.date'=>array("width"=>100)
			,'messagerie.attachments'
		);
		$this->fieldstructure();
		
		$this->noTruncateSA = false;

		$this->addPrivilege("getAll");
		$this->addPrivilege("getBody");
		$this->addPrivilege("fetchAttachment");
		$this->addPrivilege("sync");
		
		ATF::tracabilite()->no_trace[$this->table]=1;
	}
		
	public function select_all($order_by=false,$asc='asc',$page=false,$count=false){
		$this->q->where("id_user",ATF::$usr->getId());
		$this->q->addField("msgno");
		$this->q->addField("messagerie.uid");
		$this->q->addField("messagerie.attachmentsRealName");
		$this->q->addOrder('date','desc');
		$this->q->addOrder('seen','asc');
		$return = parent::select_all($order_by,$asc,$page,$count);
		return $return;
	}
	
	private function getConf($idUsr) {
		$custom = ATF::user()->select($idUsr,"custom");
		$pref = unserialize($custom);
		
		if ($pref['messagerie']['password']) {
			$pref['messagerie']['password'] = ATF::preferences()->decryptPasswordMessagerie($pref['messagerie']['password']);
		}
		return $pref["messagerie"];
	}

	public function getBody(&$infos) {
		$conf = $this->getConf(ATF::$usr->getId());
		$this->imap = new imap($conf['host'],$conf['port'],$conf['username'],$conf['password'],$conf['folder']);
		$content = $this->imap->get_body( $this->imap->stream, $infos['uid'] );
		if ($infos['mime']) { 
			return $content;
		}
		switch ( $content['mime_type'] ) {
			case "text":
				header('Content-Type: text/html; charset=UTF-8');
				$return = nl2br($content['content']);
			break;
			case "html":
				//Traitement des images inlines
				if ($content['attachments']) {
					$content = $this->parseImageCID($content,$infos['id']);
				}
				header('Content-Type: text/html; charset=UTF-8');
				header('Content-Transfer-Encoding: 8bit');
				$return = $content['content'];
			break;
		}
		if ($infos['id']) {
			$msg = array("id_messagerie"=>$infos['id'],"seen"=>1);
			$this->update($msg);
		}
		
		return $return;
	}
	public function parseImageCID($content,$idMsg) {
		// On recherche les src="cid:
		preg_match_all('/src="cid:(.*)"/Uims', $content['content'], $matches);
		if(count($matches)) {
			$search = array();
			$replace = array();
			foreach($matches[1] as $match) {
				// Nom du fichier
				$uniqueFilename = $content['attachments'][$match]['filename'];
				// Le chemin où on le sauvegarde
				$pathToSave = $this->filepath($this->decryptId($idMsg),$uniqueFilename);
				// Création du fichier si nécessaire
				if (!file_exists($pathToSave)) {
					util::file_put_contents($pathToSave, $content['attachments'][$match]['data']);
				}
				// Récupération dimensions image originale
				$dim = ATF::gd()->getDimension($pathToSave);
				$search[] = "src=\"cid:$match\"";
				$replace[] = "src=\"".__ABSOLUTE_WEB_PATH__."messagerie-".$idMsg."-".$content['attachments'][$match]['filename']."-".$dim['w']."-".$dim['h'].".png\"";
			}
			// Remplacement des cid par les chemins d'images stockés sur optima.
			$content['content'] = str_replace($search, $replace, $content['content']);
		}
		return $content;
	}
	
	public function isSync($id_user,$uid) {
		$this->q->reset()
					->where("id_user",$id_user)
					->where("uid",$uid)
					->setDimension('row');
		$r = $this->sa();		
		return $r?true:false;
	}
	
	public function fetchAttachment(&$infos) {
		$folder_id = $infos['folder_id'];
		$message_id = $infos['message_id'];
		$filename = $infos['filename'];
		$realFilename = $infos['realFilename'];
		
		if (!$filename || !$message_id) return false;
		
		$conf = $this->getConf(ATF::$usr->getId());
		$this->imap = new imap($conf['host'],$conf['port'],$conf['username'],$conf['password'],$conf['folder']);

		$filename = urldecode($filename);
//		if ($folder_id) {
//			$folderName = $this->getFolderName( $folder_id );
//			if ( $folderName === false || $this->checkOwner( $message_id ) === false ) {
//				die(); // Folder or message does not belong to current user.
//			}
//			global $synch;
//			$synch->connect( $folderName );
//		}

		$result = $this->imap->get_attachments($message_id,$filename);
		if ($result===false || $filename=="") {
			header("HTTP/1.0 404 Not Found");
			die("File not found.");
		} else {
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename='.urlencode(basename($realFilename)));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: '.strlen($result));
			echo $result;
		}
	}
	
	public function getLasitUID() {
		$this->q->reset()
					->addField('uid')
					->where('id_user',ATF::$usr->getId())
					->addOrder('uid','desc')
					->setLimit(1)
					->setDimension('cell');
		return $this->sa();
	}
	
	public function sync_old($infos) {
		return false;
		//Compteur de mail synchronisé
		$ct = 0;
		
		// Configuration de l'imap
		$conf = $this->getConf(ATF::$usr->getId());
		if (!$conf['host'] || !$conf['port'] || !$conf['username'] || !$conf['password']) return false;
		$this->imap = new imap($conf['host'],$conf['port'],$conf['username'],$conf['password'],$conf['folder']);
		
		if ($this->imap->error) {
			ATF::$msg->addWarning($this->imap->error);
			return false;
		}
		
		// ¨Préparation du filter en récupérant le dernier UID
		$filter = "1:*";
							
		if ($lastUID = $this->getLasitUID()) {
			$filter = $lastUID.":*";
		}
 
		$sort = $this->imap->imap_sort(SORTDATE);

		// Check des mails déjà synchronisé
		foreach ($this->imap->imap_sort(SORTDATE) as $k=>$i) {
			$totalMailToSync++;
			// On dépasse la limite de mails a synchroniser
			if (count($overview)+1>$this->limitNbMsgSync) continue;
			// Le mail est déjà synchronisé
			if (ATF::messagerie()->isSync(ATF::$usr->getId(),$i)) continue;
			$overview[] = $this->imap->imap_fetch_overview($i);
		}
//		log::logger($overview,"qjanon");
//		return true;
		
		foreach ($overview as $k=>$i) {
			$i = $i[0];
			$h = $this->imap->imap_headerinfo($i->msgno);
			$attachments = $this->imap->get_attachments($i->uid);
			$insert = array(
				'subject'=>$h->subject?$h->subject:"Pas de sujet",
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
				'id_user'=>ATF::$usr->getId()
			);

			foreach ($attachments as $kpj=>$ipj) {
				if ($kpj) $insert['attachments'] .= ",";
				if ($kpj) $insert['attachmentsRealName'] .= ",";
				$insert['attachments'] .= $this->imap->decodeMimeString($ipj['filename']);
				$insert['attachmentsRealName'] .= $ipj['filename'];
			}
			if ($id = $this->insert($insert)) {
				$ct++;	
			}
		}
		if ($ct) {
			ATF::$msg->addNotice($ct." " .ATF::$usr->trans("mailsSynchronise",$this->table));
		}
		ATF::$json->add("totalCount",$ct);
		return true;	
	}
	
	public function sync($infos) {
		//Compteur de mail synchronisé
		$ct = 0;
		
		// Configuration de l'imap
		$conf = $this->getConf(ATF::$usr->getId());
		if (!$conf['host'] || !$conf['port'] || !$conf['username'] || !$conf['password']) return false;
		$this->imap = new imap($conf['host'],$conf['port'],$conf['username'],$conf['password'],$conf['folder']);
		
		if ($this->imap->error) {
			ATF::$msg->addWarning($this->imap->error);
			return false;
		}
		
		// ¨Préparation du filter en récupérant le dernier UID
		$filter = "1:*";
							
		if ($lastUID = $this->getLasitUID()) {
			$filter = $lastUID.":*";
		}
 
		$sort = $this->imap->imap_sort(SORTDATE,1);

		// Check des mails déjà synchronisé
		foreach ($sort as $k=>$i) {
			$totalMailToSync++;
			// On dépasse la limite de mails a synchroniser
			if (count($overview)+1>$this->limitNbMsgSync) continue;
			// Le mail est déjà synchronisé
			if (ATF::messagerie()->isSync(ATF::$usr->getId(),$i)) continue;
			$overview[] = $this->imap->imap_fetch_overview($i);
		}
//		log::logger($overview,"qjanon");
//		return true;
		
		foreach ($overview as $k=>$i) {
			$i = $i[0];
			$h = $this->imap->imap_headerinfo($i->msgno);
			$attachments = $this->imap->get_attachments($i->uid);
			$insert = array(
				'subject'=>$h->subject?$h->subject:"Pas de sujet",
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
				'id_user'=>ATF::$usr->getId()
			);

			foreach ($attachments as $kpj=>$ipj) {
				if ($kpj) $insert['attachments'] .= ",";
				if ($kpj) $insert['attachmentsRealName'] .= ",";
				$insert['attachments'] .= $this->imap->decodeMimeString($ipj['filename']);
				$insert['attachmentsRealName'] .= $ipj['filename'];
			}
			if ($id = $this->insert($insert)) {
				$ct++;	
			}
		}
		if ($ct) {
			ATF::$msg->addNotice($ct." " .ATF::$usr->trans("mailsSynchronise",$this->table));
		}
		ATF::$json->add("totalCount",$ct);
		return true;	
	}
	
	public function getIdByUID($uid) {
		$this->q->reset()
					->addField('id_messagerie')
					->where('id_user',ATF::$usr->getId())
					->where('uid',$uid);
//		$this->q->setToString();
//		log::logger($this->select_cell(),"qjanon");
//		$this->q->unsetToString();
		return $this->select_cell();
	}
	
	
};
?>