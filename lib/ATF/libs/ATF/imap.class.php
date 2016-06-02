<?php
/**
 * Provides IMAP functionality.
*/
class imap {
	public $stream;
	protected $mailbox;
	public $getAttachments = true;
	/**
	 * Initiate connection.
	*/
	public function __construct( $hostname, $port, $username, $password, $folder = "", $validateCertificate = false , $confPrefix =false) {
		if (isset($hostname) && isset($username) && isset($port) && isset($password)) {
			$this->init( $hostname, $port, $username, $password, $folder, $validateCertificate, $confPrefix);
		}
	}
	
	public function init( $hostname, $port, $username, $password, $folder = "", $validateCertificate = false ,$confPrefix =false) {
			
		$this->mailbox = "{".$hostname.":".$port.( ($confPrefix == false ? "" : $confPrefix).($validateCertificate == false ? "/novalidate-cert" : "/validate-cert" ))."}".$folder;
		/* An error occured, set the stream to null */
		$this->stream = @imap_open(
			$this->mailbox
			,$username
			,$password
		);	
		$error = imap_last_error();
		/* An error occured, set the stream to null */
		if ( $error != "" ) {
			$this->stream = null;
			$this->error = $error;
		}
	}

	public function cleanFolders( $folders ) {
		foreach ( $folders as $folder => $name ) {
			$folders[$folder] = str_replace( $this->mailbox, "", $name );
		}
		return $folders;
	}

	public function imap_sort($critere,$reverse=0,$options=SE_UID,$searchCritere=NULL,$charset="ISO-8859-1") {
		return imap_sort($this->stream,$critere,$reverse,SE_UID|SE_NOPREFETCH,$searchCritere,$charset);
	}

	public function imap_list() {
		return $this->cleanFolders( imap_list( $this->stream, $this->mailbox, "*" ) );
	}

	public function imap_num_msg() {
		return imap_num_msg ($this->stream);
	}

	public function imap_check() {
		return imap_check( $this->stream );
	}

	public function imap_fetch_overview($filter,$flag=FT_UID) {
		return imap_fetch_overview( $this->stream, $filter, $flag);
	}

	public function imap_headerinfo( $msgno ) {
		$return = imap_headerinfo( $this->stream, $msgno );

		if ($d = imap_mime_header_decode($return->subject)) {
			$return->subject = $d[0]->text;
		} else {
			$return->subject = $return->subject;
		}
		
		if ($d = imap_mime_header_decode($return->toaddress)) {
			$return->to = $d[0]->text;
		} else {
			$return->to = $return->toaddress;
		}
		
		if ($d = imap_mime_header_decode($return->from[0]->mailbox.'@'.$return->from[0]->host)) {
			$return->from = $d[0]->text;
		} else {
			$return->from = $return->from[0]->mailbox.'@'.$return->from[0]->host;
		}
		
		return $return;
	}

	public function view_message( $msgno ) {
		echo $this->get_body( $this->stream, $msgno );
	}

	public function close() {
		if ( $this->stream != null ) {
			imap_close( $this->stream );
		}
	}

	public function imap_createmailbox( $folder ) {
		imap_createmailbox( $this->stream, $this->mailbox.$folder );
	}

	public function imap_expunge() {
		imap_expunge( $this->stream );
	}

	public function imap_delete( $uid ) {
		imap_delete( $this->stream, $uid, FT_UID );
	}

	public function imap_mail_move( $msglist, $folder ) {
		imap_mail_move( $this->stream, $msglist, $folder, FT_UID );
		$this->imap_expunge();
	}

	public function imap_append( $content, $flags = null ) {
		return imap_append( $this->stream, $this->mailbox, $content, $flags );
	}

	/* PHP.net */
	private function get_mime_type( &$structure ) {
		$primary_mime_type = array("TEXT", "MULTIPART","MESSAGE", "APPLICATION", "AUDIO","IMAGE", "VIDEO", "OTHER");
		if($structure->subtype) {
			return $primary_mime_type[(int) $structure->type] . '/' .$structure->subtype;
		}
		return "TEXT/PLAIN";
	}
	
	private function get_part($stream, $uid, $mime_type, $structure = false, $part_number = false) {
		if(!$structure) {
			$structure = imap_fetchstructure($stream, $uid, FT_UID);
		}
		if($structure) {
			if($mime_type == $this->get_mime_type($structure)) {
				if(!$part_number) {
					$part_number = "1";
				}
				$text = imap_fetchbody($stream, $uid, $part_number, FT_UID);
				
				if($structure->encoding == 3) {
					return imap_base64($text);
				} else if($structure->encoding == 4) {
					return imap_qprint($text);
				} else {
				return $text;
			}
		}

		if($structure->type == 1) /* multipart */ {
			while(list($index, $sub_structure) = each($structure->parts)) {
				if($part_number) {
					$prefix = $part_number . '.';
				} else {
					$prefix = "";
				}
				$data = $this->get_part($stream, $uid, $mime_type, $sub_structure,$prefix . ($index + 1));
				if($data) {
					return $data;
				}
			} // END OF WHILE
			} // END OF MULTIPART
		} // END OF STRUTURE
		return false;
	} // END OF FUNCTION

	public function get_body( $stream, $uid ) {
		$this->fetch($stream,$uid);
		
		if ($this->bodyHTML != "") {
//			$h = imap_fetchheader($this->connection, $this->messageNumber, FT_UID);
//			$this->bodyHTML = str_replace($h,"",$this->bodyHTML);
			$params = $this->getParams($stream, $uid);
			if ($params["charset"]!="UTF-8" && array_key_exists("charset",$params)) {
				$msgBody = utf8_encode($this->bodyHTML);
			} else {
				$msgBody = $this->bodyHTML;
			}
			$mailformat = "html";
		} else {
			$msgBody = $this->bodyPlain;
			$mailformat = "text";
		}
		
		$r = array( 
			"mime_type" => $mailformat, 
			"content" => $msgBody, 
			"attachments"=> $this->attachments
		);
		
		return $r;
	}
	
	public function fetch($stream,$uid) {
		$this->connection = $stream;
		$this->messageNumber = $uid;
		$structure = @imap_fetchstructure($this->connection, $this->messageNumber, FT_UID);
		if(!$structure) {
			return false;
		} elseif ($structure->subtype=="PLAIN") {
			$h = imap_fetchheader($this->connection, $this->messageNumber, FT_UID);
			$b = imap_fetchbody($this->connection, $this->messageNumber, false, FT_UID);
			$this->bodyPlain = substr_replace($b,"",0,strlen($h));
		} else {
			$this->recurse($structure->parts);
		}
		return true;
		
	}

	public function recurse($messageParts, $prefix = '', $index = 1, $fullPrefix = true) {

		foreach($messageParts as $part) {
			
			$partNumber = $prefix . $index;
			
			if($part->type == 0) {
				if($part->subtype == 'PLAIN') {
					$this->bodyPlain .= $this->getPart($partNumber, $part->encoding);
				}
				else {
					$this->bodyHTML .= $this->getPart($partNumber, $part->encoding);
				}
			}elseif($part->type == 2) {
				//$msg = new EmailMessage($this->connection, $this->messageNumber);
				$msg->getAttachments = $this->getAttachments;
				$msg->recurse($part->parts, $partNumber.'.', 0, false);
				$this->attachments[] = array(
					'type' => $part->type,
					'subtype' => $part->subtype,
					'filename' => '',
					'data' => $msg,
					'inline' => false,
				);
			}elseif(isset($part->parts)) {
				if($fullPrefix) {
					$this->recurse($part->parts, $prefix.$index.'.');
				}
				else {
					$this->recurse($part->parts, $prefix);
				}
			}elseif($part->type > 2) {
				if(isset($part->id)) {
					$id = str_replace(array('<', '>'), '', $part->id);
					$this->attachments[$id] = array(
						'type' => $part->type,
						'subtype' => $part->subtype,
						'filename' => $this->getFilenameFromPart($part),
						'data' => $this->getAttachments ? $this->getPart($partNumber, $part->encoding) : '',
						'inline' => true,
					);
				}
				else {
					$this->attachments[] = array(
						'type' => $part->type,
						'subtype' => $part->subtype,
						'filename' => $this->getFilenameFromPart($part),
						'data' => $this->getAttachments ? $this->getPart($partNumber, $part->encoding) : '',
						'inline' => false,
					);
				}
			}
			
			$index++;
			
		}
		
	}
	
	function getPart($partNumber, $encoding) {
		$data = imap_fetchbody($this->connection, $this->messageNumber, $partNumber, FT_UID);
		switch($encoding) {
			case 0: return $data; // 7BIT
			case 1: return $data; // 8BIT
			case 2: return $data; // BINARY
			case 3: return base64_decode($data); // BASE64
			case 4: return quoted_printable_decode($data); // QUOTED_PRINTABLE
			case 5: return $data; // OTHER
		}


	}
	
	function getFilenameFromPart($part) {

		$filename = '';

		if($part->ifdparameters) {
			foreach($part->dparameters as $object) {
				if(strtolower($object->attribute) == 'filename') {
					$filename = $object->value;
				}
			}
		}

		if(!$filename && $part->ifparameters) {
			foreach($part->parameters as $object) {
				if(strtolower($object->attribute) == 'name') {
					$filename = $object->value;
				}
			}
		}

		return $filename;

	}
	

	/** See http://www.electrictoolbox.com/extract-attachments-email-php-imap/ for reference */
	public function get_attachments( $uid, $filename = "", $structure=false) {
		if (!$structure) {
			$structure = imap_fetchstructure( $this->stream, $uid, FT_UID );
		}
		$attachments = array();
		$j = 0;
//		log::logger($structure->parts,"qjanon");
//		log::logger("Count = ".count( $structure->parts ),"qjanon");
		if ( isset( $structure->parts ) && count( $structure->parts ) ) { // If the message body has any 'parts'
			for( $i = 0; $i < count( $structure->parts ); $i++ ) { // For every part
				$found = false;
//				log::logger("i = ".$i,"qjanon");
//				log::logger($structure->parts[$i],"qjanon");
				
				if ($structure->parts[$i]->parts) {
					$attachments[$j] = self::get_attachments($uid,$filename, $structure->parts[$i]);
				}
				
				if ( $structure->parts[$i]->ifdparameters || $structure->parts[$i]->subtype == "RFC822" ) { // Search for filename, in the message parameters
					if ( $structure->parts[$i]->subtype == "RFC822"  ) {
						// Search PLAIN text attachements
						for ( $p = 0; $p < count( $structure->parts[$i]->parts ); $p++ ) {
							if ( $structure->parts[$i]->parts[$p]->subtype == "PLAIN" ) {
								$attachments[$j]['filename'] = $structure->parts[$i]->description;
								$attachments[$j]['case'] = 1;
								$found = true;
								$j++;
							}
						}
					} else {
						foreach( $structure->parts[$i]->dparameters as $object ) {
							// Gestiond es image avec des lien du genre CID:, image embarquées
							if ( $structure->parts[$i]->encoding == 3 ) { // 3 = BASE64
								$attachments[$j]['id'] = $structure->parts[$i]->id;
							}
							
							if ( strtolower( $object->attribute ) == 'filename') { // If found, part is an attachment
								$attachments[$j]['filename'] = $object->value;
								$found = true;
								$j++;
							}
						}
					}
				}

				if ( $structure->parts[$i]->ifparameters && $found == false ) { // Same as above
					foreach ( $structure->parts[$i]->parameters as $object ) {
						if ( strtolower( $object->attribute ) == 'name' ) {
							$attachments[$j]['filename'] = $object->value;
								$attachments[$j]['case'] = 3;
							$found = true;
							$j++;
						}
					}
				}

				if ( $found == true && $filename != "" && $filename == $attachments[$j - 1]['filename'] ) {

					$attachments[$j - 1]['attachment'] = imap_fetchbody( $this->stream, $uid, $j + 1, FT_UID );
					if ( $structure->parts[$i]->encoding == 3 ) { // 3 = BASE64
						$attachments[$j - 1]['attachment'] = base64_decode( $attachments[$j - 1]['attachment'] );
					} elseif ( $structure->parts[$i]->encoding == 4 ) { // 4 = QUOTED-PRINTABLE
						$attachments[$j - 1]['attachment'] = quoted_printable_decode( $attachments[$j - 1]['attachment'] );
					} else {
						$attachments[$j - 1]['attachment'] = $attachments[$j - 1]['attachment'];
					}
					return $attachments[$j - 1]['attachment']; // Return content
				}
				$found = true;
			}
		}
		if ( $filename == "" ) {
			return $attachments;
		} else {
			return false;
		}
	}
	
	//return supported encodings in lowercase.
	function mb_list_lowerencodings() { $r=mb_list_encodings();
		for ($n=sizeOf($r); $n--; ) { $r[$n]=strtolower($r[$n]); } return $r;
	}	
	
	//  Receive a string with a mail header and returns it
	// decoded to a specified charset.
	// If the charset specified into a piece of text from header
	// isn't supported by "mb", the "fallbackCharset" will be
	// used to try to decode it.
	function decodeMimeString($mimeStr, $inputCharset='utf-8', $targetCharset='utf-8', $fallbackCharset='iso-8859-1') {
		$encodings=$this->mb_list_lowerencodings();
		$inputCharset=strtolower($inputCharset);
		$targetCharset=strtolower($targetCharset);
		$fallbackCharset=strtolower($fallbackCharset);
		
		$decodedStr='';
		$mimeStrs=imap_mime_header_decode($mimeStr);
		for ($n=sizeOf($mimeStrs), $i=0; $i<$n; $i++) {
			$mimeStr=$mimeStrs[$i];
			$mimeStr->charset=strtolower($mimeStr->charset);
			if (($mimeStr == 'default' && $inputCharset == $targetCharset) || $mimStr->charset == $targetCharset) {
				$decodedStr.=$mimStr->text;
			} else {
				$decodedStr.=mb_convert_encoding($mimeStr->text, $targetCharset,(in_array($mimeStr->charset, $encodings)?$mimeStr->charset :$fallbackCharset));
			}
		} 
		return $decodedStr;
	}
	
	public function getParams($stream, $uid) {
		$p = imap_fetchstructure($stream, $uid, FT_UID);
		$params = array();
		
		$this->rgetParams($p,$params);
		
		return $params;
	}
	
	public function rgetParams($struc,&$params) {
		if ($struc->parameters) {
			foreach ($struc->parameters as $x) {
				$params[ strtolower( $x->attribute ) ] = $x->value;
			}
		}
		if ($struc->dparameters) {
			foreach ($struc->dparameters as $x) {
				$params[ strtolower( $x->attribute ) ] = $x->value;
			}
		}
		if ($struc->parts) {
			foreach ($struc->parts as $x) {
				self::rgetParams($x,$params);
			}
		}
		return $params;
	}
	
	/**
	* returnBodyStr
	* @see http://www.php.net/manual/en/function.imap-fetchbody.php
	* @param $messageNumber(int),part(int)
	* @return string
	*/
	public function returnBodyStr($uid,$section=1){
		return utf8_encode(imap_utf8(imap_fetchbody($this->stream,$uid,$section, FT_UID)));
	}
	
	
	/**
	* returnBody
	* @see http://www.php.net/manual/en/function.imap-fetchbody.php
	* @param $messageNumber(int),part(int)
	* @return string
	*/
	public function returnBody($uid,$section=1){
	  $struct = imap_fetchstructure($this->stream, $uid, FT_UID);
	  
	  //Si on a qu'un partie
	  if($struct->parameters[0]->attribute == "CHARSET"){
		  if(($struct->parameters[0]->value === "ISO-8859-1") || ($struct->parameters[0]->value === "iso-8859-1")){
		  		if($struct->encoding == 3){				
		  			$content = utf8_encode(base64_decode(imap_fetchbody($this->stream,$uid,1, FT_UID)));
				}elseif($struct->encoding == 4){
		  			$cont = quoted_printable_decode(imap_fetchbody($this->stream,$uid,"1", FT_UID));
					$pos = strpos($cont, "quoted-printable");
					$pos = $pos + strlen("quoted-printable");
					$content = utf8_encode(substr($cont, $pos));	
		  		}else{
		  			$content = utf8_encode(imap_fetchbody($this->stream,$uid,1, FT_UID));
		  		}	   		 
		   }elseif(($struct->parameters[0]->value === "UTF-8") || ($struct->parameters[0]->value === "utf-8")){
		   		if($struct->encoding == 3){	 
		  			$content =  base64_decode(imap_fetchbody($this->stream,$uid,1, FT_UID));
				}elseif($struct->encoding == 4){
		  			$cont = quoted_printable_decode(imap_fetchbody($this->stream,$uid,"1", FT_UID));
					$pos = strpos($cont, "quoted-printable");
					$pos = $pos + strlen("quoted-printable");
					$content = substr($cont, $pos);	
		  		}else{		  			 	  			
		  			 $content =	imap_fetchbody($this->stream,$uid,1, FT_UID);
		  		}	   		
		   }
		  else{		  		
		  	 	$content =	imap_fetchbody($this->stream,$uid,1, FT_UID);
		  }
	  }else{  	
	  	 if(($struct->parts[0]->parameters[0]->value === "ISO-8859-1") || ($struct->parts[0]->parameters[0]->value === "iso-8859-1")){
		  		if($struct->parts[0]->encoding == 3){				
		  			$content = utf8_encode(base64_decode(imap_fetchbody($this->stream,$uid,"1", FT_UID)));
		  		}elseif($struct->parts[0]->encoding == 4){
		  			$cont = quoted_printable_decode(imap_fetchbody($this->stream,$uid,"1", FT_UID));
					$pos = strpos($cont, "quoted-printable");
					$pos = $pos + strlen("quoted-printable");
					$content = substr($cont, $pos);
				}else{		  			
		  			$content = utf8_encode(imap_fetchbody($this->stream,$uid,"1", FT_UID));
		  		}	   		 
		   }elseif(($struct->parts[0]->parameters[0]->value === "UTF-8") || ($struct->parts[0]->parameters[0]->value === "utf-8")){
		   		if($struct->parts[0]->encoding == 3){	 
		  			$content =  base64_decode(imap_fetchbody($this->stream,$uid,"1", FT_UID));
		  		}elseif($struct->parts[0]->encoding == 4){
		  			$cont = quoted_printable_decode(imap_fetchbody($this->stream,$uid,"1", FT_UID));
					$pos = strpos($cont, "quoted-printable");
					$pos = $pos + strlen("quoted-printable");
					$content = substr($cont, $pos);	
				}else{		  			 			
		  			 $content =	imap_fetchbody($this->stream,$uid,"1", FT_UID);
				}
		  //Si c'est BOUNDARY, la Structure est differente !!		
		  }elseif($struct->parts[0]->parameters[0]->attribute == "BOUNDARY"){		  		
		  		 if(($struct->parts[0]->parts[0]->parameters[0]->value=== "ISO-8859-1") || ($struct->parts[0]->parts[0]->parameters[0]->value === "iso-8859-1")){
				  		if($struct->parts[0]->parts[0]->encoding == 3){				
				  			$content = utf8_encode(base64_decode(imap_fetchbody($this->stream,$uid,"1", FT_UID)));
				  		}elseif($struct->parts[0]->parts[0]->encoding == 4){
				  			$cont = quoted_printable_decode(imap_fetchbody($this->stream,$uid,"1", FT_UID));
							$pos = strpos($cont, "quoted-printable");
							$pos = $pos + strlen("quoted-printable");
							$content = substr($cont, $pos);
						}else{		  			
				  			$content = utf8_encode(imap_fetchbody($this->stream,$uid,"1", FT_UID));
				  		}	   		 
				   }elseif(($struct->parts[0]->parts[0]->parameters[0]->value === "UTF-8") || ($struct->parts[0]->parts[0]->parameters[0]->value === "utf-8")){
				   		if($struct->parts[0]->parts[0]->encoding == 3){
				   			$mess = imap_fetchbody($this->stream,$uid,"1", FT_UID);	
				   			if(strpos ($mess ,"base64") +6 < 200){
				   				$content =  base64_decode(substr($mess, strpos ($mess ,"base64") +6));
				   			}else{
				   				$content =  base64_decode($mess);
				   			}			   					 
				  			
				  		}elseif($struct->parts[0]->parts[0]->encoding == 4){				  			
				  			$cont = quoted_printable_decode(imap_fetchbody($this->stream,$uid,"1", FT_UID));
							$pos = strpos($cont, "quoted-printable");
							$pos = $pos + strlen("quoted-printable");
							$content = substr($cont, $pos);	
						}else{		  			 			
				  			 $content =	imap_fetchbody($this->stream,$uid,"1", FT_UID);
						}
				  }else{
				  		$content = imap_fetchbody($this->stream,$uid,"1", FT_UID);
				  }  		
		  }else{			
		  	 	$content = imap_fetchbody($this->stream,$uid,"1", FT_UID);
		  }
	  }	   
	  return $content;
	}
	
	/**
	* returnmail
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param $messageNumber(int)
	* @return String MAIL entier
	*/
	public function returnmail($uid){		
		$mail = imap_fetchheader ($this->stream,$uid, FT_UID);
		$mail = $mail.imap_fetchbody($this->stream,$uid,NULL, FT_UID);
				
		return $mail;
	}		

}
?>