<?
include_once "emailing.test.php";

class emailing_projet_test extends emailing_test {
	
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 20-08-2010
	*/ 
	function setUp() {
		parent::setUp("emailing_contact,emailing_lien,emailing_projet"); 
 		$this->obj = ATF::emailing_projet();
	}
	
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 20-08-2010
	*/ 
	function tearDown() {
		ATF::db()->rollback_transaction(true);
		//Flush des notices
		ATF::$msg->getNotices();
	}
    /**
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 08-04-2011
    */
     
    public function test_select_all() {
        $this->obj->q->reset();
        $r = $this->obj->select_all();
        $this->assertEquals(1,count($r),"Erreur, il y a plus qu'un retour");
        $this->assertEquals(0,$r[0]['nbJobs'],"Il n'a pas de job pour ce projet normallement");
        $this->assertEquals($this->ep['id_emailing_projet'],$r[0]['emailing_projet.id_emailing_projet'],"Il n'a pas de job pour ce projet normallement");
    }
    /**
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 20-01-2011
    */
    
    public function test_insert() {
        $projet = array("emailing_projet"=>array(
            "emailing_projet"=>"Test l'insert de projet"
            ,"subject"=>"Sujet test de l'insert de projet"
            ,"mail_from"=>"mail_from@test_de_l_insert_de_projet.fr"
            ,"nom_expediteur"=>"nom_expediteur test de l'insert de projet"
            ,"couleur_fond"=>"#efefef"
        ));
        $id = $this->obj->insert($projet);
        $this->assertNotNull($id,"Pas d'ID en retour ?");
        $this->assertEquals("efefef",$this->obj->select($id,'couleur_fond'),"Erreur la couleur de fond n'est pas bien traité");
        $cr=ATF::$cr->getCrefresh();
        $this->assertEquals("generic-update_ext.tpl.htm",$cr["main"]["template"],"Erreur de redirection, ca devrait partir sur l'update");
    }
    /**
    * @author Quentin JANON <qjanon@absystech.fr>
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>    
    * @date 18-09-2014
    */
    
    public function test_update() {
        $this->ep['couleur_fond'] = "#656565";
        $projet['emailing_projet'] = $this->ep;
        $r = $this->obj->update($projet);
        $this->assertEquals(1,$r,"Erreur de nombre d'enregistrements modifiés");
        $this->assertEquals("656565",$this->obj->select($this->ep['id_emailing_projet'],'couleur_fond'),"Erreur la couleur de fond n'est pas bien traité");
/*
        $projet["corps_SMS"] = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed auctor semper nisi, fermentum ullamcorper erat. Proin scelerisque libero finibus, mollis nisl at turpis duis.";
        $r2 = $this->obj->update($projet);
        $this->assertFalse($r2, "Ne retourne pas false pour un SMS de plus de 160 caracteres !");

        $notice = ATF::$msg->getNotices();
        $a =  Array ("msg" => "Le message SMS ne doit pas dépasser 160 caractères. (Ici 170 caractères)",
                    "title" => "error",
                    "timer" => 3
        );
        $this->assertEquals($a , $notice, "La notice n'est pas bonne ?");
*/
    }
    /**
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 20-08-2010
    */
     
    public function test_apply_links() {
        $this->obj->apply_links($this->ep['corps']);
        $this->assertFalse(strpos("[LINK",$this->ep['corps']),"Erreur : il reste un Lien qui n'a pas été traité.");
    }
     
    /**
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 20-08-2010
    */ 
    public function test_replaceNormalLinks() {
        $lien1 = "http://lienTU1.fr";
        $lien2 = "http://lienTU2.fr";
        $lien3 = "http://lienTU3.fr";
        $linkInsert = array("emailing_lien"=>$lien3,"url"=>$lien3);
        $idLinkInsert = ATF::emailing_lien()->insert($linkInsert);
        $corps = '<img title="coyote.jpg" src="http://dev.speedmail.absystech.net/emailing_projet-f500ccdd9725c1aa5bc2b3ef3dfb7989-coyote.jpg-309-YWJzeXN0ZWNo.jpg" align="default">&nbsp;<a href="'.$lien1.'">pgyuhl </a>kbhkljh lkhbklj <a href="'.$lien2.'">lijyhgb </a>jhbkjlok <a href="'.$lien3.'">njkhbjgvkj </a>hbkjnbkljn&nbsp;<br>';
        
        $this->obj->replaceNormalLinks($corps);
        
        $this->assertNotNull(ATF::emailing_lien()->ss("emailing_lien",$lien1),"Le lien 1 n'est pas retrouvé dans la BDD... erreur lors de la création ?");
        $this->assertNotNull(ATF::emailing_lien()->ss("emailing_lien",$lien2),"Le lien 2 n'est pas retrouvé dans la BDD... erreur lors de la création ?");
        $this->assertNotNull(ATF::emailing_lien()->ss("emailing_lien",$lien3),"Le lien 3 n'est pas retrouvé dans la BDD... erreur lors de la création ?");
        
        $this->assertEquals(3,substr_count($corps, '[LINK='),"Erreur, problème lors du traitement des liens.");
        $this->assertEquals(1,substr_count($corps, '[LINK='.$idLinkInsert.']'),"Erreur, problème lors du traitement du lien déjà existant.");
        
    }
    
    /**
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 20-08-2010
    */ 
    public function test_filters() {
        $this->obj->filters($this->ep['corps'],$this->ec[0]['id_emailing_contact']);
        
        $this->assertFalse(strpos("[INFO",$this->ep['corps']),"Erreur : il reste une info personnalisé qui n'a pas été traitée.");
        $this->assertNotNull(strpos($this->ec[0]['nom'],$this->ep['corps']),"Erreur : Le nom n'as pas été bien traité.");
        $this->assertNotNull(strpos($this->ec[0]['prenom'],$this->ep['corps']),"Erreur : Le nom n'as pas été bien traité.");
        $this->assertNotNull(strpos($this->ec[0]['email'],$this->ep['corps']),"Erreur : Le nom n'as pas été bien traité.");
        
        $this->assertNotNull(strpos($this->ec[0]['nom']." ".$this->ec[0]['nom']." <".$this->ec[0]['email'].">",$this->ep['corps']),"Erreur : Le nom+prenom+email n'as pas été bien traité.");
    }
    


	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 20-08-2010
	*/
	 
	public function test_send() {
		//Upload d'un fichier image 
		$infos = array("id"=>$this->ep['id_emailing_projet']);
		$files = array("files"=>array(
			"name"=>array(
				"Fichier1.jpg"
				,"Fichier2.jpg"
			),
			"size"=>array(666,777),
			"tmp_name"=>array(
				__PDF_PATH__."absystech/entete.jpg"
				,__PDF_PATH__."absystech/suite.jpg"
			)
		));
		$this->obj->uploadFile($infos);
		$this->assertFalse($this->obj->send(),"Erreur : Ca passe sans rien en entrée ?");
		
		$params = array(
			"id_emailing_projet"=>$this->ep['id_emailing_projet']
		);
		try {
			$this->obj->send($params);
		} catch (errorATF $e) {
			$this->assertNotNull($e->getMessage(),"Le throw ne s'est pas déclenché, couille dans le paté !");
		}
		
		$params = array(
			"id_emailing_projet"=>$this->ep['id_emailing_projet']
			,"email"=>"tu@absystech.net"
		);
		//Simulation d'URL généré par le fileManager
		$src = "http://speedmail.absystech.net/emailing_projet-".$this->obj->specialCrypt($this->ep['id_emailing_projet'])."-Fichier1.jpg-200-".base64_encode(ATF::$codename).".jpg";
		$this->ep['corps'] .= '<br><img src="'.$src.'">';
		$this->obj->update($this->ep);
		$this->obj->send($params,$params,true);
		
		try {
			$mbox = imap_open("{zimbra.absystech.net:143/imap/notls}INBOX",'tu@absystech.net','!1337!');
			$NB = imap_num_msg($mbox);
	
			$overview = imap_fetch_overview($mbox, "1:$NB",0);
			$counter=0;
			if (is_array($overview)) {
				foreach ($overview as $val) {
					if ($val->subject!="TEST Subject TU 1") continue;
					$this->assertNotNull(preg_match("/<img src=\"cid:/",imap_body($mbox,$val->msgno)),"Erreur : Pas d'image en mode CID");
					$this->assertNotNull(preg_match("/--00002ImagesEmbarquees/",imap_body($mbox,$val->msgno)),"Erreur : Pas d'image embarqué");
					$this->assertEquals("tu@absystech.net",$val->to,"Erreur : Le to du mail n'est pas le bon");
					$this->assertEquals("TU Expediteur <tu@absystech.net>",$val->from,"Erreur : Le from du mail n'est pas le bon");
					$counter++;
					imap_delete($mbox,$val->msgno);
				}
			}
			imap_expunge($mbox);
			imap_close($mbox);
			$this->assertNotEquals(0,$counter,"Erreur : Il n'a  trouvé aucun mail ");
		} catch (errorATF $e) {
			throw new errorATF("Erreur imap_open : ".imap_last_error(),801);
		}

	}
	
        /**
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 10-05-2011
    */
    public function test_uploadFile() {
        $infos = array("id"=>$this->ep['id_emailing_projet']);
        $files = array("files"=>array(
            "name"=>array(
                "Fichier1.jpg"
                ,"Fichier2.jpg"
            ),
            "size"=>array(666,777),
            "tmp_name"=>array(
                __PDF_PATH__."absystech/entete.jpg"
                ,__PDF_PATH__."absystech/suite.jpg" 
            )
        ));
        $s = array();
        $r = $this->obj->uploadFile($infos,$s,$files);
        
        $path1 = $this->obj->filepath($this->ep['id_emailing_projet'],"Fichier1.jpg");
        $path2 = $this->obj->filepath($this->ep['id_emailing_projet'],"Fichier2.jpg");

        $this->assertTrue(file_exists(strtolower($path1)),"Erreur de placement du fichier 1");
        $this->assertTrue(file_exists(strtolower($path2)),"Erreur de placement du fichier 2");
        $this->assertEquals('{"success":true}',$r,"Erreur dans le retour");

    }
    
    /**
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 20-01-2011
    */ 
    function test_getFileType() {
        
        $this->assertNull($this->obj->getFileType("phoque"),"Erreur de retour, Ca devrait être un objet.");
        
        $this->assertEquals("image",$this->obj->getFileType("phoque.jpg"),"Erreur de retour, Ca devrait être une image.");
        $this->assertEquals("image",$this->obj->getFileType("phoque.jpeg"),"Erreur de retour, Ca devrait être une image.");
        $this->assertEquals("image",$this->obj->getFileType("phoque.gif"),"Erreur de retour, Ca devrait être une image.");
        $this->assertEquals("image",$this->obj->getFileType("phoque.png"),"Erreur de retour, Ca devrait être une image.");
        
        $this->assertEquals("object",$this->obj->getFileType("phoque.swf"),"Erreur de retour, Ca devrait être un objet.");
        
        $this->assertEquals("data",$this->obj->getFileType("phoque.zip"),"Erreur de retour, Ca devrait être une data.");
        $this->assertEquals("data",$this->obj->getFileType("phoque.rar"),"Erreur de retour, Ca devrait être une data.");
        $this->assertEquals("data",$this->obj->getFileType("phoque.xml"),"Erreur de retour, Ca devrait être une data.");
        $this->assertEquals("data",$this->obj->getFileType("phoque.exe"),"Erreur de retour, Ca devrait être une data.");
        $this->assertEquals("data",$this->obj->getFileType("phoque.mp3"),"Erreur de retour, Ca devrait être une data.");
        $this->assertEquals("data",$this->obj->getFileType("phoque.pdf"),"Erreur de retour, Ca devrait être une data.");
        $this->assertEquals("data",$this->obj->getFileType("phoque.doc"),"Erreur de retour, Ca devrait être une data.");
        $this->assertEquals("data",$this->obj->getFileType("phoque.docx"),"Erreur de retour, Ca devrait être une data.");
    }
    
    /**
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 20-01-2011
    */
     
    function test_fileList() {
        $dir = dirname($this->obj->filepath($this->ep['id_emailing_projet'],"image"));
        util::rm($dir);
        $infos = array("id"=>$this->ep['id_emailing_projet']);
        $files = array("files"=>array(
            "name"=>array(
                "Fichier1.jpg"
                ,"Fichier2.jpg"
            ),
            "size"=>array(666,777),
            "tmp_name"=>array(
                __PDF_PATH__."absystech/entete.jpg"
                ,__PDF_PATH__."absystech/suite.jpg"
            )
        ));
        $s = array();
        $this->obj->uploadFile($infos,$s,$files);
        $r = $this->obj->fileList($infos);
        $r = json_decode($r,true);
        $r = $r['files'];
        $this->assertEquals(2,count($r),"Erreur de retour, il devrait y avoir deux éléments uniquement.");
        $r = $r[0];
        $this->assertNotNull($r['name'],"Erreur de retour, il devrait y avoir un 'name' pour le fichier");
        $this->assertNotNull($r['type'],"Erreur de retour, il devrait y avoir un 'type' pour le fichier");
        $this->assertNotNull($r['size'],"Erreur de retour, il devrait y avoir un 'size' pour le fichier");
        $this->assertNotNull($r['lastmod'],"Erreur de retour, il devrait y avoir un 'lastmod' pour le fichier");
        $this->assertNotNull($r['w'],"Erreur de retour, il devrait y avoir un 'w' pour le fichier");
        $this->assertNotNull($r['h'],"Erreur de retour, il devrait y avoir un 'h' pour le fichier");
        $this->assertNotNull($r['url'],"Erreur de retour, il devrait y avoir un 'url' pour le fichier");
        $url = "http://speedmail.absystech.net/emailing_projet-".$this->obj->aesFixe->crypt($this->ep['id_emailing_projet'])."-Fichier1.jpg-".$r['w']."-".base64_encode(ATF::$codename).".jpg";
        $this->assertNotNull($url,$r['url'],"Erreur de retour, l'url de l'image 1 est mauvaise");
    }
    /**
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 20-01-2011
    */
     
    function test_deleteFile() {
        
        $infos = array("id"=>$this->ep['id_emailing_projet']);
        $infos1 = array("id"=>$this->ep['id_emailing_projet'],"file"=>"Fichier1.jpg");
        $filename1 = $this->obj->filepath($this->ep['id_emailing_projet'],"Fichier1.jpg"); 
        $infos2 = array("id"=>$this->ep['id_emailing_projet'],"file"=>"Fichier2.jpg");
        $filename2 = $this->obj->filepath($this->ep['id_emailing_projet'],"Fichier2.jpg"); 
        $files = array("files"=>array(
            "name"=>array(
                "Fichier1.jpg"
                ,"Fichier2.jpg"
            ),
            "size"=>array(666,777),
            "tmp_name"=>array(
                __PDF_PATH__."absystech/entete.jpg"
                ,__PDF_PATH__."absystech/suite.jpg"
            )
        ));
        $s = array();
        $this->obj->uploadFile($infos,$s,$files);
        $r1 = $this->obj->deleteFile($infos1);
        $this->assertFalse(file_exists($filename1),"Erreur 1 : le fichier est toujours présent");
        $this->assertEquals('{"success":true}',$r1,"Erreur1 dans le retour");
        $r2 = $this->obj->deleteFile($infos2);
        $this->assertFalse(file_exists($filename2),"Erreur 2 : le fichier est toujours présent");
        $this->assertEquals('{"success":true}',$r2,"Erreur2 dans le retour");
    }
    /**
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 21-01-2011
    */
    function test_specialDecryptANDspecialCryptANDimg() {
        $r = $this->obj->specialCrypt(1);
        $this->obj->img(array("id"=>$r));
        $this->assertEquals("9a1ba10300b8ac9328a301322ee31b13",$r,"Erreur : l'ID crypté avec la SEED fixe n'est pas bon");
        $this->assertEquals(1,$this->obj->specialDecrypt($r),"Erreur : l'ID decrypté avec la SEED fixe n'est pas bon");
    }
     
    /**
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 21-01-2011
    */ 
    function test_parseImages() {
        $this->assertFalse($this->obj->parseImages(),"PAs d'entre, devrait retourner FALSE.");
    }
    
    /**
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 12-07-2012
    */ 
    function test_saveSendAndStay() {
        $this->ep['couleur_fond'] = "#656565";
        $projet['emailing_projet'] = $this->ep;
        $r = $this->obj->saveSendAndStay($projet);
        $this->assertTrue($r,"Erreur de retour");
        $this->assertEquals("656565",$this->obj->select($this->ep['id_emailing_projet'],'couleur_fond'),"Erreur la couleur de fond n'est pas bien traité");
    }
		
};
?>