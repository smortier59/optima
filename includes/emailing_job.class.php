<?
/** 
* Classe emailing_job, gère les jobs d'envoi de mail
* @author Quentin JANON <qjanon@absystech.fr>
* @package Optima
* @todo Refactoring ATF5
*/
class emailing_job extends emailing {

	protected $idConstanteLastSpeedmail = 12;
	protected $tarif = 0.02;

	function __construct() { // PHP5
		
		parent::__construct();
		$this->table = __CLASS__;
		
		$this->colonnes['fields_column'] = array(	
			'emailing_job.emailing_job'
			,'emailing_job.depart'=>array("width"=>100,"align"=>"center")
			,'emailing_job.fin'=>array("width"=>100,"align"=>"center")
			,'emailing_job.id_emailing_projet'
			,'emailing_job.id_emailing_liste'
			,'emailing_job.etat'=>array("width"=>30,"align"=>"center","renderer"=>"etat")
			,'nbSent'=>array("width"=>120,'custom'=>true,"aggregate"=>array("min","avg","max","sum"),"align"=>"center")
			,'nbToSend'=>array("width"=>120,'custom'=>true,"nosort"=>true,"align"=>"center")
			,'nbClic'=>array("width"=>120,'custom'=>true,"align"=>"center")
			,'tauxClic'=>array("width"=>120,'custom'=>true,"align"=>"center","renderer"=>"percent")
			,'tauxPenetration'=>array("width"=>120,'custom'=>true,"align"=>"center","renderer"=>"percent")
			,'nbRetour'=>array("width"=>120,'custom'=>true,"align"=>"right")
			,'tauxRetour'=>array("width"=>120,'custom'=>true,"align"=>"center","renderer"=>"percent")
		);
		
		$this->colonnes['primary'] = array(
			"emailing_job"
			,"depart"
			,"id_emailing_projet"
			,"id_emailing_liste"
			
		);
								
		// Adresse
		$this->colonnes['panel']['statistique'] = array(
			"nbSent"=>array("custom"=>true)
			,"nbToSend"=>array("custom"=>true)
			,"nbClic"=>array("custom"=>true)
			,"tauxClic"=>array("custom"=>true)
			,'tauxPenetration'=>array('custom'=>true)
			,"nbRetour"=>array("custom"=>true)
			,"tauxRetour"=>array("custom"=>true)
			,"nb_visu_online"
		);
		$this->panels['statistique'] = array("visible"=>true,'nbCols'=>1,'isSubPanel'=>true);
								
		$this->colonnes['panel']['prix'] = array(
			"prix"=>array('xtype'=>'box',"custom"=>true,"html"=>"Choisissez une liste de diffusion")
		);
		$this->panels['prix'] = array("visible"=>true,"nbCols"=>1);
		
		$this->colonnes['bloquees']['insert'] =  
		$this->colonnes['bloquees']['update'] = 
		$this->colonnes['bloquees']['cloner'] = 
		$this->colonnes['bloquees']['select'] =  array('fin','etat');	

		$this->fieldstructure();
		$this->onglets = array('emailing_job_email'=>array("opened"=>true),								
								'emailing_tracking'=>array("opened"=>true));			
		$this->helpMeURL = "http://wiki.optima.absystech.net/index.php/Jobs";
		$this->addPrivilege("estimationPrix");
	}
	
	/**
	* Surcharge du select-All
	*/
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){
		$this->q
			->addJointure("emailing_job","id_emailing_job","emailing_job_email","id_emailing_job")
			->addField("nbMailToSend")
			->addField("COUNT(emailing_job_email.id_emailing_job_email)","nbSent")
			->addField("SUM(emailing_job_email.tracking)","nbClic")
			->addField("ROUND((SUM(emailing_job_email.tracking)/COUNT(emailing_job_email.id_emailing_job_email))*100)","tauxClic")
			->addField("(SELECT COUNT(*) FROM emailing_job_email WHERE retour='oui' AND emailing_job_email.id_emailing_job=emailing_job.id_emailing_job)","nbRetour")
			->addField("ROUND(((SELECT COUNT(*) FROM emailing_job_email WHERE retour='oui' AND emailing_job_email.id_emailing_job=emailing_job.id_emailing_job)/COUNT(emailing_job_email.id_emailing_job_email))*100)","tauxRetour")
			->addGroup("emailing_job.id_emailing_job");
		$data = parent::select_all($order_by,$asc,$page,$count);
		foreach ($data['data'] as $k=>$i) {
			if (!$i['nbMailToSend']) {
				$j = array("id_emailing_job"=>$i['emailing_job.id_emailing_job'],"nbMailToSend"=>ATF::emailing_liste_contact()->nbMail($i['emailing_job.id_emailing_liste_fk']));
				$this->u($j);
				$data['data'][$k]['nbToSend'] = $j["nbMailToSend"];
			} else {
				$data['data'][$k]['nbToSend'] = $i['nbMailToSend'];
			}
			$data['data'][$k]['nbToSend'] .= " (".number_format($data['data'][$k]['nbToSend']*$this->tarif,2)."€)";

			if ($i['emailing_job.id_emailing_job']) {
				$data['data'][$k]['tauxPenetration'] = ATF::emailing_tracking()->tauxPenetration($i['emailing_job.id_emailing_job']);
			}
		}
		return $data;
	}
	
	/**
	* Surcharge du select
	*/
	public function select($id,$field=NULL){
		$sel = parent::select($id,$field);
		if ($field) return $sel;
		$this->q->reset()
					->addField("COUNT(emailing_job_email.id_emailing_job_email)","nbSent")
					->addField("SUM(emailing_job_email.tracking)","nbClic")
					->addField("ROUND((SUM(emailing_job_email.tracking)/COUNT(emailing_job_email.id_emailing_job_email))*100)","tauxClic")
					->addField("(SELECT COUNT(*) FROM emailing_job_email WHERE retour='oui' AND emailing_job_email.id_emailing_job=emailing_job.id_emailing_job)","nbRetour")
					->addField("ROUND(((SELECT COUNT(*) FROM emailing_job_email WHERE retour='oui' AND emailing_job_email.id_emailing_job=emailing_job.id_emailing_job)/COUNT(emailing_job_email.id_emailing_job_email))*100)","tauxRetour")
					->addJointure("emailing_job","id_emailing_job","emailing_job_email","id_emailing_job")
					->setStrict()
					->setDimension('row')
					->addCondition($this->table.".id_".$this->table,$this->decryptID($id));
		$this->q->setToString();
		$this->q->unsetToString();
		$r = parent::select_all();
		$r['nbToSend'] = ATF::emailing_liste_contact()->nbMail($sel['id_emailing_liste']);
		$r['tauxPenetration'] = ATF::emailing_tracking()->tauxPenetration($sel['id_emailing_job'])."%";
		$r['tauxClic'] .= "%";
		$r['tauxRetour'] .= "%";
		return array_merge($sel,$r);
	}
	
	/** 
	* Insert un job en vérifiant que la date d'envoi est valide
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 03-11-2010
	* @param $infos données du formulaire
	* @param $s Session courante
	* @param $files Fichier joint transmis
	* @param $cadre_refreshed
	* @return void
	*/
	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed=NULL) {
		$this->infoCollapse($infos);	

		$date=strtotime($infos['depart']); 
		$now=strtotime(date("Y-m-d H:i:s",time()));

		if(($date+3600)<$now) {
			throw new errorATF(ATF::$usr->trans('dateInvalide',$this->table));
			return false;
		}

		$return = parent::insert($infos,$s,$files,$cadre_refreshed);
		
		$message = "Un job d'emailing viens d'être créer sur Optima ".ATF::$codename." par ".ATF::$usr->get("prenom")." ".ATF::$usr->get("nom").", en voici les infos : \n\n";
		$infos_message = $this->select($return);
		
		foreach ($this->select($return) as $k=>$i) {
			$infos_message[$k] = $i;
			if ($k == 'id_emailing_projet') {
				$infos_message[$k] = ATF::emailing_projet()->nom($i);
			} elseif ($k == 'id_emailing_liste') {
				$infos_message[$k] = ATF::emailing_liste()->nom($i);
			}
		}
		
		if (!ATF::isTestUnitaire()) {
			$m["recipient"]="qjanon@absystech.fr, ygautheron@absystech.fr";
			$m["from"]="Robot Optima AT";
			$m["objet"]="Creation Job #".$return;
			$m["template"]="emailing_job";
			$m['message'] = $message;
			$m['infos_message'] = $infos_message;
		
			$mail = new mail($m);
			$mail->send();
		}
		return $return;
	}
	
	/** 
	*  Renvoi la veleur de la prorpriete privé idConstanteLastSpeedmail
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 03-11-2010
	* @return int idConstanteLastSpeedmail
	*/
	public function getCLS() {
		return $this->idConstanteLastSpeedmail;
	}
	
	/** 
	* Renvoi le nombre de mails restant à envoyer tous jobs confondus pour l'extranet courant
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 03-11-2010
	* @return int Nombre de mails
	*/
	public function toSent($strict=false,$flag=false) {
		$this->q->reset()
					->addField("SUM(IF(emailing_job_email.id_emailing_job_email IS NULL,1,0))","nb_to_sent")
					->fromInner("emailing_job","id_emailing_liste","emailing_liste_contact","id_emailing_liste")
					->fromInner("emailing_liste_contact","id_emailing_contact","emailing_contact","id_emailing_contact")
					->Where("emailing_job_email.id_emailing_job","emailing_job.id_emailing_job","AND","J1","=",false,false,true)
					->from("emailing_liste_contact","id_emailing_liste_contact","emailing_job_email","id_emailing_liste_contact",NULL,NULL,NULL,"J1")					
					->Where("emailing_contact.opt_in","oui")
					->WhereIsNotNull("emailing_contact.email")
					->Where("emailing_job.etat","sending")
					->Where("emailing_job.depart",date("Y-m-d H:i:s"),"OR",false,"<")
					;
		if ($strict) {
			$this->q->setStrict()->setDimension("cell");
		} else {
			$this->q->addField("emailing_job.id_emailing_job","id_emailing_job");
		}
		return $this->sa();		
	}

	/** 
	*  Passe tous les jobs de l'état wait a l'état sending
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 03-11-2010
	* @return int Nombre d'enregistrements modifiés
	*/
	public function majEtatSending() {
		$this->q->reset()
			->Where("etat","wait")
			->where("depart",date("Y-m-d H:i:s"),"AND",false,"<")
			->addValues(array('etat'=>"sending"));
		return ATF::db()->update($this);
	}
	
	/** 
	*  Passe tous les jobs de l'état sending a l'état sent
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 03-11-2010
	* @return void
	*/
	public function majEtatSent() {
		$ct = 0;
		if ($d = $this->toSent(false,'hop')) {
			foreach ($d as $k=>$i) {
				if (!$i['nb_to_sent'] && $i["id_emailing_job"]) {
					ATF::speedmail()->done($i["id_emailing_job"]);
					$ct += $this->update(array(
						"id_emailing_job"=>$i['id_emailing_job']
						,"etat"=>"sent"
						,'fin'=>date("Y-m-d H:i:s",time())
					));
					$el = array(
						"id_emailing_liste"=>$this->select($i['id_emailing_job'],'id_emailing_liste')
						,"etat"=>"close"
					);
					ATF::emailing_liste()->u($el);
				}
			}
		}
		
		return $ct?$ct:false;
	}

	/** 
	* Gère l'envoi des mails, avec un pas pour éviter la surcharge
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 03-11-2010
	* @return void
	*/
	function send($step=150,$forceEmail=false) {
		$this->q->reset()
					->addField("emailing_job.id_emailing_job","id_emailing_job")
					->addField("emailing_projet.id_emailing_projet","id_emailing_projet")
					->addField("emailing_contact.email","email")								
					->addField("emailing_contact.id_emailing_contact","id_emailing_contact")
					->addField("emailing_liste_contact.id_emailing_liste_contact	","id_emailing_liste_contact")
					->addField("emailing_liste_contact.id_emailing_liste","id_emailing_liste")
					->fromInner("emailing_job","id_emailing_projet","emailing_projet","id_emailing_projet")
					->fromInner("emailing_job","id_emailing_liste","emailing_liste_contact","id_emailing_liste")
					->fromInner("emailing_liste_contact","id_emailing_contact","emailing_contact","id_emailing_contact")
					->Where("emailing_job_email.id_emailing_liste_contact","emailing_liste_contact.id_emailing_liste_contact","AND","J2","=",false,false,true)
					->from("emailing_job","id_emailing_job","emailing_job_email","id_emailing_job",NULL,NULL,NULL,"J2")
					->where("emailing_contact.opt_in","oui")
					->where("emailing_job.etat","sending")
					->whereIsNotNull("emailing_contact.email")
					->whereIsNull("emailing_job_email.id_emailing_job_email")	
					->setLimit($step)
					->setStrict();
		if ($r = $this->sa()) {
			foreach ($r as $k=>$i) {
				
							

				
					// Enregistrement dans la table emailing_job_email
					$id_ej = ATF::emailing_job_email()->insert(array(
						"id_emailing_job"=>$i["id_emailing_job"]
						,"id_emailing_liste_contact"=>$i["id_emailing_liste_contact"]
					));

					$projet = ATF::emailing_projet()->select($i["id_emailing_projet"]);
					//Application des liens tracables du projet
					ATF::emailing_projet()->apply_links($projet["corps"],$id_ej);
					//Application des infos personnalisés du projet
					ATF::emailing_projet()->filters($projet["corps"],$i["id_emailing_contact"]);
					//URL de désinscription
					$projet["unregister"]= __ABSOLUTE_WEB_PATH__."speedmail.php?confirm=1&unregister=".md5($i["id_emailing_contact"])."&societe=".base64_encode(ATF::$codename);
					//URL de visualisation du mail dans une page HTML 
					$projet["viewer"]= __ABSOLUTE_WEB_PATH__."speedmail.php?idec=".md5($i["id_emailing_contact"])."&ideje=".md5($id_ej)."&id=".md5($i["id_emailing_projet"])."&societe=".base64_encode(ATF::$codename);
			
					// Si pas de mail en Texte brut, alors on le fait nous même
					if (!$d["corps_txt"]) {
						$d['corps_txt'] = utf8_decode(util::toPlainText($d["corps"]));
					}

					//Préparation du mail

					$infosM = array(
						"objet"=>$projet["subject"]
						,"from"=>$projet["nom_expediteur"] ? $projet["nom_expediteur"]." <".$projet["mail_from"].">" : $projet["mail_from"]
						,"recipient"=>$i["email"]
						,"return_path"=>md5($i["id_emailing_job"])."-".md5($i["id_emailing_liste_contact"])."-".base64_encode(ATF::$codename)."-speedmail@absystech-speedmail.com"
						,"projet"=>$projet
						,"template"=>"speedmail"
						,"template_only"=>true
						,"noInterceptor"=>true
					);

					$m = new mail($infosM);
					ATF::emailing_projet()->parseImages($m);
					//Envoi du mail
					try {
						$r = $m->send(NULL,false,$forceEmail);
					} catch (error $e) {
						ATF::emailing_job_email()->update(array(
							"id_emailing_job_email"=>$id_ej
							,"retour"=>"oui"
							,"erreur_brute"=>"ERROR (#".$e->getCode().") : ".$e->getMessage()
						));
					}
					
					//Energistrement dans la base Optima
					ATF::speedmail()->send($i["id_emailing_job"]);
					//Incrémentation des sollicitations
					ATF::emailing_contact()->increase($i["id_emailing_contact"],"sollicitation");
					ATF::emailing_liste_contact()->increase($i["id_emailing_liste_contact"],"sollicitation");
					ATF::emailing_liste()->increase($i["id_emailing_liste"],"sollicitation");
				
				// Mis a jour de l'état
				$this->majEtatSent();
			
			}
		}
	}
	
	public function bouncesAnalyse($temp,&$set){
		while($bounce = array_pop($temp)) {
			if (strlen($bounce)) {
				$num="(25[0-5]|2[0-4]\d|[01]?\d\d|\d)";
				$bounce = preg_replace("/$num\\.$num\\.$num\\.$num/","",$bounce);
				$bounces = array(2=>array(),4=>array(),5=>array());
				preg_match_all("/2\\.[0-7]\\.[0-8]/",$bounce,$bounces[2]);
				preg_match_all("/4\\.[0-7]\\.[0-8]/",$bounce,$bounces[4]);
				preg_match_all("/5\\.[0-7]\\.[0-8]/",$bounce,$bounces[5]);
				$bounces[2] = array_shift($bounces[2]);
				$bounces[4] = array_shift($bounces[4]);
				$bounces[5] = array_shift($bounces[5]);
				if (count($bounces[2])>0) {
					$x=NULL;
					foreach ($bounces[2] as $k => $i) {
						$x[$i]=$i;
					}
					$bounces[2] = $x;
					$set["success"] = implode(",",$bounces[2]);								
				}
				if (count($bounces[4])>0) {
					$x=NULL;
					foreach ($bounces[4] as $k => $i) {
						$x[$i]=$i;
					}
					$bounces[4] = $x;
					$set["persistent_failure"] = implode(",",$bounces[4]);								
				}
				if (count($bounces[5])>0) {
					$x=NULL;
					foreach ($bounces[5] as $k => $i) {
						$x[$i]=$i;
					}
					$bounces[5] = $x;
					$set["permanent_failure"] = implode(",",$bounces[5]);								
				}
				if (count($set)) break;
			}
		}	
		return $bounces;
	}
	
	/** 
	* Parse la boite mail <postmaster@absystech-speedmail.com> (Adresse de retour et de réponse des mailings) pour gérer les différents retours
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 03-11-2010
	* @return void
	*/
	function check_retour() {
		if (!ATF::getSingleton("imap")) ATF::setSingleton("imap", new mockObjectForIMAP("mail.absystech.net",143,"postmaster@absystech-speedmail.com","lkjoiu987"));
//		$mbox = new imap("mail.absystech.net",143,"postmaster@absystech-speedmail.com","lkjoiu987");
//		echo "Logged sur imap\n";
		$overview = ATF::imap()->imap_fetch_overview("1:*");

		if (is_array($overview)) {
			foreach ($overview as $val) {
				$set = NULL;
//				echo "\n========================================================\n";
//				echo "Num ".($numero_message++)."\n";
				$msgno[] = $val->msgno;
				if (strpos(str_replace(" ","",$val->to),"-speedmail@absystech-speedmail.com")!==false  // Vérification du domaine
					&& (
						strpos(str_replace(" ","",$val->to),"<")===false // Le to ne contient
						|| 
						strpos(str_replace(" ","",$val->to),"No-reply")!==false
					)
				) {
					if ("-speedmail@absystech-speedmail.com"==$val->to) {
						 /*Pas bon, on pourra jamais retrouver le projet !*/
//						echo "Corrupted & deleted... ".$val->to." \n";
						ATF::imap()->imap_delete($val->uid);
//						imap_delete($mbox,$val->msgno);
						continue;
					}
					//$body = imap_fetchbody($mbox,$val->msgno,1);
					$body = ATF::imap()->returnBodyStr($val->uid);
					$temp = explode("--- Below",$body);

					$erreur_brute = $temp;
					$bounces = $this->bouncesAnalyse($temp,$set);

					$tab = explode("-",$val->to);
//					echo "Val->to : ".$val->to." \n";

					$md5_job = $tab[0];
					$md5_liste_contact = $tab[1];
					$societe = base64_decode($tab[2]);

//					echo "md5_job : ".$md5_job." \n";
//					echo "md5_contact : ".$md5_liste_contact." \n";
//					echo "societe : ".$societe." \n";

					if (!$md5_job || !$md5_liste_contact || !$societe) ATF::imap()->imap_delete($val->uid);
					
//					echo "Société : ".$societe." \n";
					if ($societe!=ATF::$codename) continue;

					$retour = 'oui';
					if (count($set)==1 && count($bounces[2])>0) {
						 /*Seulement un retour succès */
						$retour = 'non';
					}
					if (count($set)) {
						$erreur_brute = ATF::db()->escape_string(serialize($erreur_brute));
					} else {
						$erreur_brute = ATF::db()->escape_string(serialize($body));
					}
					
					$d = array(
						"retour"=>$retour
						,"erreur_brute"=>$erreur_brute
						,"id_emailing_job"=>$md5_job
						,"id_emailing_liste_contact"=>$md5_liste_contact
					);

					if (count($set)) {
						foreach ($set as $cle=>$obj) {
							$d[$cle] = $obj;
						}
					}
					ATF::emailing_job_email()->updateError($d);
					//Si erreur 5.X.X (sauf boite mail pleine), desinscription du contact
					if ((substr($d['permanent_failure'],0,1)=="5" && $d['permanent_failure']!="5.2.2") || preg_match("ERROR",$erreur_brute)) {
						ATF::emailing_liste_contact()->q
						->reset()
						->addField('id_emailing_contact')
						->Where("MD5(id_emailing_liste_contact)",$md5_liste_contact)
						->setDimension('cell');
						
						if ($id = ATF::emailing_liste_contact()->select_all()) {
							$ec = array("emailing_contact"=>array(
								"id_emailing_contact"=>$id
								,"opt_in"=>"non"
							));
							ATF::emailing_contact()->update($ec);
						}
					}
					
					
//					echo "Erreur detectee : ".(count($set) ? implode(",",$set) : "Erreur Inconnue")."\n";
					ATF::imap()->imap_delete($val->uid);
					
					if (ATF::db()->affected_rows) {
						 /* Comptage des erreurs */ 
						$liste_contact = ATF::emailing_liste_contact()->fromMD5($md5_liste_contact);
						ATF::emailing_liste_contact()->increase($liste_contact["id_emailing_liste_contact"],"erreur");
						ATF::emailing_liste()->increase($liste_contact["id_emailing_liste"],"erreur");
						ATF::emailing_contact()->increase($liste_contact["id_emailing_contact"],"erreur");
					}
				} else {
//					echo "2] SPAM deleted... (".$val->to.") \n";
					ATF::imap()->imap_delete($val->uid);
				}
			}
		}
//		imap_expunge($mbox);
//		imap_close($mbox);
		ATF::imap()->imap_expunge();
		ATF::imap()->close();
	}
	
	/** 
	* Renvoi le prix du mailing
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
	public function estimationPrix($infos) {
		$idEL = ATF::emailing_liste_contact()->decryptId($infos['idEL']);
		return number_format(ATF::emailing_liste_contact()->nbMail($idEL)*$this->tarif,2);
	}
	
	
};
?>