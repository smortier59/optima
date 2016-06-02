<?
/** 
* Classe emailing_projet, gère les projets d'emailing
* @package Optima
* @author Quentin JANON <qjanon@absystech.fr>
*/
class emailing_projet extends emailing {

	function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		
		$this->colonnes['fields_column'] = array(	
			'emailing_projet.emailing_projet'
			,'emailing_projet.nom_expediteur'
			,'emailing_projet.subject'
			,'emailing_projet.mail_from'=>array("renderer"=>"email")
			,'nbJobs'=>array("width"=>80,"custom"=>true,"aggregate"=>array("min","avg","max","sum"),"align"=>"center","type"=>"decimal")
		);		
		
		$this->colonnes['primary'] = array(
				"emailing_projet"
				,"nom_expediteur"
				,"subject"
				,"mail_from"
				,"embed_image"
				,"afficher_infos_societe"
		);								

        $this->colonnes['panel']['couleur'] = array(
                "couleur_fond"=>array("color"=>true)
                ,"couleur_link"=>array("color"=>true)
                ,"couleur_footer"=>array("color"=>true)
        );
        $this->panels['couleur'] = array("visible"=>true,"nbCols"=>3);
        
        $this->colonnes['panel']['corpsSPM'] = array(
            "corps"=>array('plugins'=>true,"height"=>500)
        );
        $this->panels['corpsSPM'] = array("visible"=>true,"nbCols"=>1);
		
		$this->colonnes['panel']['corpsTXT'] = array(
			"corps_txt"=>array("xtype"=>"textarea","height"=>500)
		);
		$this->panels['corpsTXT'] = array("visible"=>false,"nbCols"=>1);

		$this->colonnes['panel']['advanced'] = array(
			"embed"=>array('xtype'=>"textarea")
		);
		$this->panels['advanced'] = array("visible"=>false,"nbCols"=>1);

		$this->colonnes['bloquees']['select'] =  array('corps');	
		$this->colonnes['bloquees']['insert'] =  array('corps','corps_txt','embed');	

		$this->fieldstructure();
		$this->formExt=true; 
		$this->addPrivilege('send');
		$this->addPrivilege('uploadFile');
		$this->addPrivilege('fileList');
		$this->addPrivilege('deleteFile');
		$this->addPrivilege('saveSendAndStay','update');
		
		$this->noUnsetFormIsActive = true;
		
		$this->seed = "bLKyhyFG3rb4MszDMs4sMTZX4TNj9PBKVT75tFWezS3Txep8";
		$this->aesFixe = new aes();
		$this->aesFixe->setSeed($this->seed);
		$this->helpMeURL = "http://wiki.optima.absystech.net/index.php/Projet_d%27emailing";
	}
	
	/**
	* Surcharge du select-All
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){
		$this->q
			->addJointure("emailing_projet","id_emailing_projet","emailing_job","id_emailing_projet")
			->addField("COUNT(emailing_job.id_emailing_job)","nbJobs")
			->addGroup("emailing_projet.id_emailing_projet");
		return parent::select_all($order_by,$asc,$page,$count);
	}
		
	/** 
	* Insert surchargé pour la redirection sur l'update.
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 11-01-2011
	*/
	function insert(&$infos=NULL,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL) {
        $infos['emailing_projet']['couleur_fond'] = preg_replace("/#/","",$infos['emailing_projet']['couleur_fond']);
        $infos['emailing_projet']['couleur_footer'] = preg_replace("/#/","",$infos['emailing_projet']['couleur_footer']);
        $infos['emailing_projet']['couleur_link'] = preg_replace("/#/","",$infos['emailing_projet']['couleur_link']);

		$id = parent::insert($infos,$s,$files);
		ATF::_r("id_emailing_projet",$this->cryptId($id));
		$this->redirection("update",$id);
		return $id;
	}
		
	/** 
	* Insert surchargé pour la redirection sur l'update.
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 11-01-2011
	*/
	function update(&$infos=NULL,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL) {
		$this->infoCollapse($infos);
		
        $infos['couleur_fond'] = preg_replace("/#/","",$infos['couleur_fond']);
        $infos['couleur_footer'] = preg_replace("/#/","",$infos['couleur_footer']);
        $infos['couleur_link'] = preg_replace("/#/","",$infos['couleur_link']);
		
		$this->replaceNormalLinks($infos['corps']);
		
		return parent::update($infos,$s,$files,$cadre_refreshed);
	}
		
	/** 
	* Applique les liens traçable a un projet lors de l'envoi de celui ci, remplace LIEN[8] par le lien id=8
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 04-11-2010
	* @param void
	* @return void
	*/
	function apply_links(&$corps,$id_emailing_job_email=NULL) {
		// Remplacement des liens traçables par les URL
		$corps = preg_replace_callback(
			'|\[LINK\=([0-9]+)\]|'
			 ,create_function(
			   '$matches'
			   ,'return __ABSOLUTE_WEB_PATH__."speedmail.php?idel=".md5($matches[1])."&ideje='.($id_emailing_job_email?md5($id_emailing_job_email):NULL).'&societe=".base64_encode('.ATF::$codename.');'
			 )
			 ,$corps
		 );
	}
	
	/** 
	* Applique les liens traçable a un projet lors de l'envoi de celui ci, remplace LIEN[8] par le lien id=8
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 04-11-2010
	* @param void
	* @return void
	*/
	function replaceNormalLinks(&$corps,$id_emailing_job_email=NULL) {
		// Remplacement des liens normaux pas des liens traçables
		$m = preg_match_all('/<a href=\"http:\/\/[a-z0-9A-Z.]+(?(?=[\/])(.*))\">/', $corps, $match,PREG_SET_ORDER); 

		foreach ($match as $k=>$i) {
			// Lien original avec le http et les guillemets
			$originalLink[$k]["original"] = $i[0];
			// Lien pur
			$link = substr($i[0],9);
			$link = substr($link,0,-2);
			
			// Recherche si le lien existe
			$r = ATF::emailing_lien()->ss("emailing_lien",$link);
			
			if ($r) {
				$replaceLink = '<a href="[LINK='.$r[0]['id_emailing_lien'].']">';
			} else {
				$linkInsert = array("emailing_lien"=>$link,"url"=>$link);
				$idLinkInsert = ATF::emailing_lien()->insert($linkInsert);
				$replaceLink = '<a href="[LINK='.$idLinkInsert.']">';
			}
			// Lien qui va remplacer l'original
			$originalLink[$k]["replacement"] = $replaceLink;
			
		}
		//Remplacement des liens.
		foreach ($originalLink as $k=>$i) {
			$corps = str_replace($i["original"],$i["replacement"],$corps);
		}
		
	}
	
	/** 
	* Applique les informations personnalisé a un projet lors de l'envoi de celui ci, remplace [INFO=...] par l'info correspondante
	* @author Quentin JANON <qjanon@absystech.fr>
	* @todo Refactoring
	* @param void
	* @return void
	*/
	function filters(&$corps,$id_emailing_contact=NULL) {
		if (!$id_emailing_contact) {
			$q = "SELECT id_emailing_contact FROM emailing_contact ORDER BY Rand();";
			$id_emailing_contact = ATF::db($this->db)->ffc($q);
		}
		if ($infos = ATF::emailing_contact()->select($id_emailing_contact)){
			foreach ($infos as $key => $item) {
				$corps=str_replace("[INFO=".$key.",md5]",md5($item),$corps);
				$corps=str_replace("[INFO=".$key."]",$item,$corps);
			}
		}
	}
	
	/** 
	* Envoi le projet par mail a l'adresse indiqué
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param array params
	*		- email
	*		- id_emaling_projet
	* @return void
	*/
	public function send($params,&$s,$forceEmail=false) {
		if (!$params['id_emailing_projet']) {
			return false;
		}
		$d = $this->select($params['id_emailing_projet']);
		//URL de désinscription
		$d["unregister"]= "#";
		//URL de visualisation du mail dans une page HTML 
		$d["viewer"]= "#";
		//Application des liens tracables du projet
		ATF::emailing_projet()->apply_links($d["corps"]);
		//Application des infos personnalisés du projet
		ATF::emailing_projet()->filters($d["corps"]);
		
		// Si pas de mail en Texte brut, alors on le fait nous même
		if (!$d["corps_txt"]) {
			$d['corps_txt'] = utf8_decode(util::toPlainText($d["corps"]));
		}
		
		// Infos du mail 
		$infosM = array(
			"objet"=>"TEST ".$d["subject"]
			,"from"=>$d["nom_expediteur"] ? $d["nom_expediteur"]." <".$d["mail_from"].">" : $d["mail_from"]
			,"recipient"=>$params["email"]
			,"projet"=>$d
			,"template"=>"speedmail"
			,"template_only"=>true
			,"noInterceptor"=>true
		);
		try{
			$m = new mail($infosM);
			ATF::emailing_projet()->parseImages($m);
			
			//Envoi du mail
			$r = $m->send(NULL,false,$forceEmail);
			//Notice mail envoyé
			ATF::$msg->addNotice(ATF::$usr->trans("mail_envoye",$this->table),ATF::$usr->trans("success"),3);
			ATF::$cr->block('top');
			ATF::$cr->block('generationTime');
		}catch(error $e){
			throw new error(ATF::$usr->trans("envoi_mail_NOK",$this->table));
		}
	}
	
	/** 
	* Upload un fichier avec le plugin FileManager de l'HTMLEditor d'EXTJS
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 13-01-2011
	* @param 
	* @return void
	*/
	function uploadFile(&$infos=NULL,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL) {
		$infos['display'] = true;
		$files = $files['files'];
		if (!$files) return false;
		$dirTarget = $this->filepath($this->decryptID($infos['id']),"image");
		util::mkdir(dirname($dirTarget));
		foreach ($files['name'] as $k=>$i) {
			if(!$files['size'][$k]) continue;
			// traitement sur le nom du fichier
			$name = $i;
			
			$name = util::removeAccents($name);

			$name = str_replace("-","",$name);
			$name = str_replace("'","",$name);
			$name = strtolower($name);
			
			$fp = $this->filepath($this->decryptID($infos['id']),$name);
			copy($files['tmp_name'][$k],$fp);
		}
		ATF::$cr->block('generationTime');
		ATF::$cr->block('top');
		$o = array ('success' => true );
		return json_encode($o);
	}
	
	/** 
	* Renvoi le type d'un fichier par rapport a son nom
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 13-01-2011
	* @param string $name nom de fichier
	* @return void
	*/
	function getFileType($name) {
		$allowImageExtension = array("jpg","jpeg","gif","png");
		$allowObjectExtension= array("swf"); 
		$allowDataExtension= array('zip','rar','xml','exe','mp3','pdf','doc','docx' );
		$ar = explode(".", $name);
		if (count($ar)>1) {
			$extension = array_pop($ar);
			if (in_array($extension, $allowImageExtension)) {
				$result = 'image';
			} elseif (in_array($extension, $allowObjectExtension)) {
				$result = 'object';
			} elseif (in_array($extension, $allowDataExtension)) {
				$result = 'data';
			}
		}
		return $result?$result:NULL;
	}

	/** 
	* Renvoi la liste des fichiers présents sur le serveur pour un projet
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 13-01-2011
	* @param 
	* @return void
	*/
	function fileList(&$infos=NULL,&$s=NULL,$files=NULL,&$cadre_refreshed) {
		$infos['display'] = true; 
		$f = array();
		$id = $this->decryptID($infos['id']);
		$dir = dirname($this->filepath($id,"image"));
		foreach (scandir($dir) as $k=>$name) {
			if ($name == "." || $name == "..") continue;
			if (filetype($dir."/".$name)=="dir") continue;
			if (!preg_match("/^".$id.".*/",$name)) continue;
			$type = $this->getFileType($name);
			if ($type === null) continue;
			$size = filesize($dir."/".$name);
			$lastmod = date ( "Y/m/d H:i", filemtime($dir."/".$name));
			list ($w,$h,$fileType,$attr) = @getimagesize($dir."/".$name);
			
			$realName = str_replace($id.".","",$name);
			$extension = substr($realName,-3);
			$fp = $this->filepath($id,$realName);			
			$f[] = array (
				'name'=>$realName,
				'type'=>$type,
				'size'=>$size,
				'lastmod'=>$lastmod,
				'w' =>intval($w), 
				'h' =>intval($h),
				"url"=> "http://".str_replace("optima","speedmail",ATF::_srv('SERVER_NAME'))."/emailing_projet-".$this->specialCrypt($id)."-".$realName."-".intval($w)."-".base64_encode(ATF::$codename).".".$extension
			);
		}
		$o = array ('files' => $f );
		return json_encode($o);
	}
	
	/** 
	* Supprime un fichier uploadé avec le plugin FileManager de l'HTMLEditor d'EXTJS
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 13-01-2011
	* @param 
	* @return void
	*/
	function deleteFile(&$infos=NULL,&$s=NULL,$files=NULL,&$cadre_refreshed) {
		$infos['display'] = true; 

		$dir = dirname($this->filepath($this->decryptID($infos['id']),"image"));
		$filename = $dir."/".$this->decryptID($infos['id']).".".$infos['file'];
		util::rm($filename);

		$o = array ('success'=>true);
		return json_encode($o);
	}
	
	/**
	* Cryptage special d'un ID avec SEED Fixe
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param int $id
	* @return int
	*/
	public function specialCrypt($id) {
		return $this->aesFixe->crypt($id);
	}	
	
	/**
	* Retourne l'id crypté avec la SEED fixe
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param int $id
	* @return int
	*/
	public function specialDecrypt($id) {
		return $this->aesFixe->decrypt($id);
	}	
	
	/**
	*  
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param int $id
	* @return int
	*/
	public function img($request) {
		$request['id'] = $this->specialDecrypt($request['id']);
		return parent::img($request);
	}	
	
	/** 
	* Envoi le projet par mail a l'adresse indiqué
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param array params
	*		- email
	*		- id_emaling_projet
	* @return void
	*/
	public function parseImages(&$mail) {
		if (!$mail) return false;
		$projet = $mail->getInfos("projet");
		if ($projet['embed_image']=='non') return true;
               
        if (preg_match_all("/src=\"http\:\/\/[a-zA-Z].[a-zA-Z0-9=.\-\_\/]*\"/",$projet['corps'],$resultSRC)) {
            foreach ($resultSRC[0] as $k=>$i) {
                $url = str_replace("\"","",substr($i,4));
                $projet['corps'] = str_replace($url,$mail->addEmbeddedImage($url),$projet['corps']);
            }
    		$mail->setInfos($projet,"projet");
        }
                
	}
	
	/**
	* Fait un update, puis envoi le mail du projet pour voir le rendu, ne fais pas de redirection
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param int $id
	* @return int
	*/
	public function saveSendAndStay(&$infos=NULL,&$s=NULL,$files=NULL,&$cadre_refreshed) {
		$this->update($infos);
		$infos["email"] = ATF::$usr->get('email');
		$this->send($infos);
		return true;
	}
};
?>