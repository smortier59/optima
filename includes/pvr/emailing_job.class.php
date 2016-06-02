<?
require_once dirname(__FILE__)."/../emailing_job.class.php";
/** 
* Classe emailing_job, gère les jobs d'envoi de mail
* @author Quentin JANON <qjanon@absystech.fr>
* @package Optima
* @todo Refactoring ATF5
*/
class emailing_job_pvr extends emailing_job {

	protected $idConstanteLastSpeedmail = 12;
	protected $tarif = 0.02;

	function __construct() { // PHP5
		parent::__construct();
	}

	/** 
	* Gère l'envoi des mails, avec un pas pour éviter la surcharge
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 03-11-2010
	* @return void
	*/
	function send($step=150,$forceEmail=false) {
		//log::logger("=========================== BEGINNING ===========================","speedmailDEBUGPVR");
		$this->q->reset()
					->addField("emailing_job.id_emailing_job","id_emailing_job")
					->addField("emailing_projet.id_emailing_projet","id_emailing_projet")
					->addField("emailing_contact.email","email")
					->addField("emailing_contact.id_emailing_contact","id_emailing_contact")
					->addField("emailing_contact.frequence","frequence")
					->addField("emailing_contact.last_sollicitation","last_sollicitation")
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
		$this->q->setToString();
		//log::logger($this->sa(),"speedmailDEBUGPVR");
		$this->q->unsetToString();
		if ($r = $this->sa()) {
			foreach ($r as $k=>$i) {
				
				// Enregistrement dans la table emailing_job_email
				log::logger("=========================== OCCURENCE ".$k."===========================","speedmailDEBUGPVR");
				log::logger($i,"speedmailDEBUGPVR");
				//log::logger("INSERT EJE","speedmailDEBUGPVR");
				$id_ej = ATF::emailing_job_email()->insert(array(
					"id_emailing_job"=>$i["id_emailing_job"]
					,"id_emailing_liste_contact"=>$i["id_emailing_liste_contact"]
				));

				$projet = ATF::emailing_projet()->select($i["id_emailing_projet"]);
				//log::logger("PROJET","speedmailDEBUGPVR");
				//log::logger($projet,"speedmailDEBUGPVR");
				//Application des liens tracables du projet
				//ATF::emailing_projet()->apply_links($projet["corps"],$id_ej);
				//Application des infos personnalisés du projet
				ATF::emailing_projet()->filters($projet["corps"],$i["id_emailing_contact"]);
				//log::logger("CORPS APRES PARSING","speedmailDEBUGPVR");
				//log::logger($projet["corps"],"speedmailDEBUGPVR");
				//URL de désinscription
				$projet["unregister"]= (__DEV__===true?"http://dev.wall.ra.novastream.fr/":"http://www.rhonealpes.tv/")."alerte?email=".$i["email"];
				//URL de visualisation du mail dans une page HTML 
				$projet["viewer"]= __ABSOLUTE_WEB_PATH__."speedmail.php?idec=".md5($i["id_emailing_contact"])."&ideje=".md5($id_ej)."&id=".md5($i["id_emailing_projet"])."&societe=".base64_encode(ATF::$codename)."&last_sollicitation=".strtotime($i['last_sollicitation']);
		
				// Si pas de mail en Texte brut, alors on le fait nous même
				if (!$d["corps_txt"]) {
					$d['corps_txt'] = utf8_decode(util::toPlainText($d["corps"]));
				}

				// Récupération des vidéos
				$ls = (ATF::_g('last_sollicitation')?ATF::_g('last_sollicitation'):$i['last_sollicitation']);
				log::logger("LAST SOLLICITATION","speedmailDEBUGPVR");
				log::logger($ls." - ".ATF::_g('last_sollicitation')." - ".$i['last_sollicitation'],"speedmailDEBUGPVR");
				
				try {

					$v = ATF::emailing_projet()->getVideos($i["email"],$ls,$id_ej);

					//log::logger("VIDEO","speedmailDEBUGPVR");
					//log::logger($v,"speedmailDEBUGPVR");

					if (!$v['total']) {
						ATF::emailing_job_email()->update(array(
							"id_emailing_job_email"=>$id_ej
							,"retour"=>"oui"
							,"permanent_failure"=>"6.6.6"
							,"erreur_brute"=>"Aucune nouvelles vidéos : email non envoyé !"
						));
					} else {

						//Préparation du mail
						$infosM = array(
							"objet"=>$projet["subject"]
							,"from"=>$projet["nom_expediteur"] ? $projet["nom_expediteur"]." <".$projet["mail_from"].">" : $projet["mail_from"]
							,"recipient"=>$i["email"]
							,"return_path"=>md5($i["id_emailing_job"])."-".md5($i["id_emailing_liste_contact"])."-".base64_encode(ATF::$codename)."-speedmail@absystech-speedmail.com"
							,"projet"=>$projet
							,"videos"=>$v
							,"template"=>"speedmail"
							,"template_only"=>true
							,"noInterceptor"=>true
							,"frequence"=>$i["frequence"]
						);

						log::logger("MAIL TO SEND","speedmailDEBUGPVR");
						log::logger($infosM,"speedmailDEBUGPVR");
						$m = new mail($infosM);

						ATF::emailing_projet()->parseImages($m);
						//Envoi du mail
						//log::logger("BEFORE SEND","speedmailDEBUGPVR");
						try {
							$r = $m->send(NULL,false,$forceEmail);
							log::logger("SEND SUCCEED","speedmailDEBUGPVR");
							//log::logger($r,"speedmailDEBUGPVR");
						} catch (error $e) {
							ATF::emailing_job_email()->update(array(
								"id_emailing_job_email"=>$id_ej
								,"retour"=>"oui"
								,"erreur_brute"=>"ERROR (#".$e->getCode().") : ".$e->getMessage()
							));
							log::logger("SEND FAILED","speedmailDEBUGPVR");
							//log::logger("ERROR (#".$e->getCode().") : ".$e->getMessage(),"speedmailDEBUGPVR");
						}
						//Energistrement dans la base Optima
						//log::logger("ENREGISTREMENT BASE OPTIMA","speedmailDEBUGPVR");
						ATF::speedmail()->send($i["id_emailing_job"]);
					}
					//Incrémentation des sollicitations
					//log::logger("INCREASE SOLLICITATION EC","speedmailDEBUGPVR");
					ATF::emailing_contact()->increase($i["id_emailing_contact"],"sollicitation");
					//log::logger("INCREASE SOLLICITATION ELC","speedmailDEBUGPVR");
					ATF::emailing_liste_contact()->increase($i["id_emailing_liste_contact"],"sollicitation");
					//log::logger("INCREASE SOLLICITATION EL","speedmailDEBUGPVR");
					ATF::emailing_liste()->increase($i["id_emailing_liste"],"sollicitation");
					
					// Mise a jour de la dernière sollicitation
					//log::logger("UPDATE LAST SOLLICITATION CONTACT","speedmailDEBUGPVR");
					$u = array("id_emailing_contact"=>$i["id_emailing_contact"],"last_sollicitation"=>date("Y-m-d H:i:s"));
					//log::logger($u,"speedmailDEBUGPVR");
					ATF::emailing_contact()->u($u);

					// Mis a jour de l'état
					//log::logger("UPDATE ETAT JOB !","speedmailDEBUGPVR");
					$this->majEtatSent();
				} catch (error $e) {
					log::logger("/!\ ERREUR /!\ ","speedmailPVR");
					log::logger($e->getCode(),"speedmailPVR");
					log::logger($e->getMessage(),"speedmailPVR");				
				}

			}
		}
		log::logger("=========================== END ===========================","speedmailDEBUGPVR");
	}

	
};
?>