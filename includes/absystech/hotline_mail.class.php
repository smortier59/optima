<?
/**  
* Classe Hotline_mail
* Gère la construction et l'envoi des mails de la hotline.
* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
* @package Optima
* @subpackage AbsysTech
*/
class hotline_mail {
	/**
	* Mail facturation
	* Il y a un attribut par mail pour pouvoir faire les Tu et gérer chaque mail séparément
	* @var mixed
	*/
	private $mail_billing=NULL;

	/**
	* Mail de notification nouveau mail
	* @var mixed
	*/
	private $mail_insert=NULL;

	/**
	* Mail notification de prise en charge envoyé au contact
	* @var mixed
	*/
	private $mail_prise_en_charge_contact=NULL;
	
	/**
	* Mail notification de prise en charge envoyé à la hotline
	* @var mixed
	*/
	private $mail_prise_en_charge_hotline=NULL;

	/**
	* Mail notification de fin de requête
	* @var mixed
	*/
	private $mail_resolve=NULL;

	/**
	* Mail notification d'annulation de requête
	* @var mixed
	*/
	private $mail_cancel=NULL;

	/**
	* Mail notification d'attente de mise en prod
	* @var mixed
	*/
	private $mail_wait_mep=NULL;

	/**
	* Mail notification de validation mep
	* @var mixed
	*/
	private $mail_mep=NULL;

	/**
	* Crée un mail d'interaction
	* @var mixed
	*/
	private $mail_interaction=NULL;
	
	/**
	* Mail notification de transfert à un utilisateur
	* @var mixed
	*/
	private $mail_user_transfert=NULL;

	/**
	* Mail notification de transfert au pole
	* @var mixed
	*/
	private $mail_pole_transfert=NULL;
	
	/**
	* Mail actuel
	* @var mixed
	*/
	private $current_mail=NULL;
			
	/**
	* Crée un nouveau mail Hotline
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param int $id_hotline
	* @param string $obj L'objet du tmail
	* @param string $from L'email de l'expéditeur
	* @param string $to L'email du destinataire
	* @param string $template Le nom du template de mail à utiliser
	* @param int $id_hotline_interaction L'interaction
	* @param boolean $pj La pièce jointe
	* @param int $id_hotline_interaction
	*/
	private function createMail($id_hotline,$obj,$from,$to,$template,$id_hotline_interaction=NULL,$pj=false,$mep=false){
		//Recherche de la hotline
		if(!$hotline=ATF::hotline()->select($id_hotline)) throw new error(ATF::$usr->trans("null_hotline","hotline"));
		//Current mail
		//if(!$this->current_mail) throw new error(ATF::$usr->trans("null_current_mail",$this->table));
		
		//Test template
		if(!$template) throw new error(ATF::$usr->trans("templateNULL","hotline"));
		
		//Recherche du contact et de la société
		$contact = ATF::contact()->nom(ATF::hotline()->decryptId($hotline['id_contact']));
		$societe = ATF::societe()->nom(ATF::hotline()->decryptId($hotline['id_societe']));
		$societe_detail = ATF::societe()->select($hotline["id_societe"]);
				
		//Données envoyées au template
		$infos["optima_url"]= ATF::permalink()->getURL(ATF::hotline()->createPermalink($id_hotline));
		$infos["portail_hotline_url"]=ATF::hotline()->createPortailHotlineURL($societe_detail["ref"],$societe_detail["divers_5"],$hotline["id_hotline"],$hotline["id_contact"],"validation");
		$infos["etat"]=$hotline["urgence"];
		$infos["ip"]=$_SERVER["REMOTE_ADDR"];
		$infos["contact"]=$contact;
		$infos["societe"]=$societe;
		$infos["hotline"]=$hotline["hotline"];
		$infos["hotline_detail"]=$hotline;
		$infos["detail"]=$hotline["detail"];
		$infos["ref"]=ATF::hotline()->decryptId($hotline["id_hotline"]);
		$infos["recipient"]=$to;
		$infos["from"]=$from;
		$infos["objet"]=$obj;
		$infos["template"]= $template;
		$infos["fichier"]=$pj;


		if($id_hotline_interaction){
			$infos["interaction"]=ATF::hotline_interaction()->select($id_hotline_interaction);
		}


		$this->current_mail = new mail($infos);
		//return $this->mail->send();
	}
	
	/**
	* Envoi le mail courant (current_mail)
	*/
	public function sendMail(){
		if(!$this->current_mail) throw new error("null_current_mail");
		return $this->current_mail->send();
	}
	
	/**
	* Donne le mail actuel
	* @return mixed
	*/
	public function getCurrentMail(){
		//Current mail
		if(!$this->current_mail) throw new error(ATF::$usr->trans("null_current_mail",$this->table));
		return $this->current_mail;
	}
	
	/**
	* Initialise le mail courant
	* @param string $mail le nom du mail courant
	*/
	public function setCurrentMail($mail){
		$this->current_mail=&$this->$mail;
	}
	
//	/**
//	* Indique l'objet du mail
//	* @param int $id_requete Le numéro de la requête
//	* @param string $societe Le nom de la société
//	* @param string $etat L'état de la requête (new, update, mep)
//	* @param string $sujet Le sujet de la requête
//	*/
//	public function setObj($id_requete,$societe,$etat,$sujet){
//		return "[".$id_requete."-".$societe."-".$etat."] ".$sujet;
//	}
	
	/**
	* Création de mail pour AbsysTech
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param int $id_hotline
	* @param string $obj L'objet du tmail
	* @param string $to L'email du destinataire
	* @param string $template Le nom du template de mail à utiliser
	* @param int $id_hotline_interaction L'interaction
	* @param boolean $pj La pièce jointe
	* @param int $id_hotline_interaction
	*/
	private function createMailForAT($id_hotline,$obj,$to,$template,$id_hotline_interaction=NULL,$pj=false){
		ATF::hotline()->q->reset()->where("id_hotline", $id_hotline);
		$id_contact = ATF::hotline()->select_row();
		$from="Hotline AbsysTech <optima-hotline-".ATF::$codename."-".ATF::hotline()->cryptId($id_hotline)."-".ATF::user()->cryptId($id_contact["id_contact"])."@absystech-speedmail.com>";
		$this->createMail($id_hotline,$obj,$from,$to,$template,$id_hotline_interaction,$pj);
	}
	
	/**
	* Création de mail pour les clients
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param int $id_hotline
	* @param string $obj L'objet du tmail
	* @param string $to L'email du destinataire
	* @param string $template Le nom du template de mail à utiliser
	* @param int $id_hotline_interaction L'interaction
	* @param boolean $pj La pièce jointe
	* @param int $id_hotline_interaction
	*/
	public function createMailForCustomers($id_hotline,$obj,$to,$template,$id_hotline_interaction=NULL,$pj=false,$mep=false){
		//On regarde si le contact n'est pas le même que l'utilisateur optima.
		if($to==ATF::user()->select(ATF::hotline()->select($id_hotline,"id_user"),"email")){
			//throw new error(ATF::$usr->trans("same_mail"));
			ATF::$msg->addWarning(ATF::$usr->trans("same_mail"));
		}else{
			ATF::hotline()->q->reset()->where("id_hotline", $id_hotline);
			$id_contact = ATF::hotline()->select_row();
			$from="Hotline AbsysTech <optima-hotline-".ATF::$codename."-".ATF::hotline()->cryptId($id_hotline)."-".ATF::user()->cryptId($id_contact["id_contact"])."@absystech-speedmail.com>";

			$this->createMail($id_hotline,$obj,$from,$to,$template,$id_hotline_interaction,$pj,$mep);
		}
	}
	
	/**
	* CLIENT
	* Crée un mail de facturation. 
	* Cela concerne une demande de validation ou notifie que le travail est à la charge d'AbsysTech ou pris en charge tout simplement (affaire)
	* @param int $id_hotline
	*/
	public function createMailBilling($id_hotline){
		if(!$id_hotline) throw new error(ATF::$usr->trans("null_id_hotline"));
		//Paramètres du mail
		$id_hotline=ATF::hotline()->decryptId($id_hotline);
		$obj=ATF::$usr->trans("mail_hotline_objet3").$id_hotline;
		$to=ATF::contact()->select(ATF::hotline()->select($id_hotline,"id_contact"),"email");
		if(!$to) throw new error(ATF::$usr->trans("null_mail_contact"));
		$template="hotline_facturation";
		$this->setCurrentMail("mail_billing");
		$this->createMailForCustomers($id_hotline,$obj,$to,$template);
	}
	
	/**
	* INTERNE
	* Crée un mail en interne lors de la création d'une requête
	* Cela concerne une demande de validation ou notifie que le travail est à la charge d'AbsysTech ou pris en charge tout simplement (affaire)
	* @param int $id_hotline
	*/
	public function createMailInsert($id_hotline,$pj=false,$id_user=NULL){
		if(!$id_hotline) throw new error(ATF::$usr->trans("null_id_hotline"));
		//Paramètres du mail
		$id_hotline=ATF::hotline()->decryptId($id_hotline);
		$obj='[#'.$id_hotline.' - Priorite : '.ATF::hotline()->select($id_hotline,"priorite").' ] NOUVELLE REQUETE '.($id_user?'pour '.ATF::user()->nom($id_user).' ':'').'de '.ATF::contact()->nom(ATF::hotline()->decryptId(ATF::hotline()->select($id_hotline,"id_contact"))).' de la societe '.ATF::societe()->nom(ATF::hotline()->decryptId(ATF::hotline()->select($id_hotline,"id_societe")));
		$to="hotline.".ATF::hotline()->select($id_hotline,"pole_concerne")."@absystech.fr";
		if(!$to) throw new error(ATF::$usr->trans("null_mail_pole"));
		$template="hotline_insert";
		$this->setCurrentMail("mail_insert");
		$this->createMailForAT($id_hotline,$obj,$to,$template,NULL,$pj);
	}
	
	/**
	* INTERNE
	* Crée un mail de prise en charge au contact
	* @param int $id_hotline
	*/
	public function createMailTakeAT($id_hotline){
		if(!$id_hotline) throw new error(ATF::$usr->trans("null_id_hotline"));
		//Paramètres du mail
		$id_hotline=ATF::hotline()->decryptId($id_hotline);
		$obj='[#'.$id_hotline.'] '.ATF::$usr->trans("hotline_prise_charge").' '.ATF::user()->nom(ATF::hotline()->select($id_hotline,"id_user"));
		$to="hotline.".ATF::hotline()->select($id_hotline,"pole_concerne")."@absystech.fr";
		if(!$to) throw new error(ATF::$usr->trans("null_mail_pole"));
		$template="hotline_prise_en_charge_hotline";
		$this->setCurrentMail("mail_prise_en_charge_hotline");
		$this->createMailForAT($id_hotline,$obj,$to,$template);
	}
	
	/**
	* CLIENT
	* Crée un mail de prise en charge au client
	* @param int $id_hotline
	*/
	public function createMailTakeCustomer($id_hotline){
		if(!$id_hotline) throw new error(ATF::$usr->trans("null_id_hotline"));
		//Paramètres du mail
		$id_hotline=ATF::hotline()->decryptId($id_hotline);
		$obj=ATF::$usr->trans("mail_hotline_objet1")." ".$id_hotline;
		$to=ATF::contact()->select(ATF::hotline()->select($id_hotline,"id_contact"),"email");
		if(!$to) throw new error(ATF::$usr->trans("null_mail_contact"));
		$template="hotline_prise_en_charge_contact";
		$this->setCurrentMail("mail_prise_en_charge_contact");
		$this->createMailForCustomers($id_hotline,$obj,$to,$template);
	}
	
	/**
	* CLIENT
	* Crée un mail de fin de requête
	* @param int $id_hotline
	*/
	public function createMailResolve($id_hotline){
		if(!$id_hotline) throw new error(ATF::$usr->trans("null_id_hotline"));
		//Paramètres du mail
		$id_hotline=ATF::hotline()->decryptId($id_hotline);
		$obj=ATF::$usr->trans("mail_hotline_objet4").$id_hotline;
		$to=ATF::contact()->select(ATF::hotline()->select($id_hotline,"id_contact"),"email");
		if(!$to) throw new error(ATF::$usr->trans("null_mail_contact"));
		$template="hotline_resolue";
		$this->setCurrentMail("mail_resolve");
		$this->createMailForCustomers($id_hotline,$obj,$to,$template);
	}
	
	/**
	* CLIENT
	* Crée un mail d'annulation de requête
	* @param int $id_hotline
	*/
	public function createMailCancel($id_hotline){
		if(!$id_hotline) throw new error(ATF::$usr->trans("null_id_hotline"));
		//Paramètres du mail
		$id_hotline=ATF::hotline()->decryptId($id_hotline);
		$obj=ATF::$usr->trans("mail_hotline_objet7").$id_hotline;
		$to=ATF::contact()->select(ATF::hotline()->select($id_hotline,"id_contact"),"email");
		if(!$to) throw new error(ATF::$usr->trans("null_mail_contact"));
		$template="hotline_annulee";
		$this->setCurrentMail("mail_cancel");
		$this->createMailForCustomers($id_hotline,$obj,$to,$template);
	}
	
	/**
	* INTERNE
	* Crée un mail d'attente de mise en prod
	* @param int $id_hotline
	*/
	public function createMailWaitMep($id_hotline){
		if(!$id_hotline) throw new error(ATF::$usr->trans("null_id_hotline"));
		//Paramètres du mail
		$id_hotline=ATF::hotline()->decryptId($id_hotline);
		$obj=ATF::$project." - ".ATF::$codename." - Attente de mise en prod !";
		$to="dev@absystech.fr";
		if(!$to) throw new error(ATF::$usr->trans("null_mail_dev"));
		$template="hotline_wait_mep";
		$this->setCurrentMail("mail_wait_mep");
		$this->createMailForAT($id_hotline,$obj,$to,$template);
	}
	
	/**
	* INTERNE
	* Crée un mail de validation de mise en prod
	* @param int $id_hotline
	*/
	public function createMailMep($id_hotline){
		if(!$id_hotline) throw new error(ATF::$usr->trans("null_id_hotline"));
		//Paramètres du mail
		$id_hotline=ATF::hotline()->decryptId($id_hotline);
		$obj='[#'.$id_hotline.' ] Mise en production effectuée';
		$to=ATF::user()->select(ATF::hotline()->select($id_hotline,"id_user"),"email");
		if(!$to) throw new error(ATF::$usr->trans("null_mail_user"));
		$template="hotline_mep";
		$this->setCurrentMail("mail_mep");
		$this->createMailForAT($id_hotline,$obj,$to,$template);
	}
	
	/**
	* CLIENT
	* Crée un mail d'interaction
	* @param int $id_hotline
	* @param int $id_hotline_interaction
	* @param mixed $pj La pièce jointe
	* @param string $toPlus Une chaine de caractère contenant la liste des id contacts destinataires. Exemple "12,3,56"
	*/
	public function createMailInteraction($id_hotline,$id_hotline_interaction,$pj=false,$toPlus=false,$mep=false){
		if(!$id_hotline) throw new error(ATF::$usr->trans("null_id_hotline"));
		//Paramètres du mail
		$id_hotline=ATF::hotline()->decryptId($id_hotline);
		$obj=ATF::$usr->trans("mail_hotline_objet2").$id_hotline;
		$to=ATF::contact()->select(ATF::hotline()->select($id_hotline,"id_contact"),"email");
		if ($toPlus) {
			$plusMail = is_array($toPlus)?$toPlus:explode(",",$toPlus);
			foreach ($plusMail as $m) {
				$to .= ", ".ATF::contact()->select($m,'email');
			}
		}
		if(!$to) throw new error(ATF::$usr->trans("null_mail_user"));
		$template="hotline_interaction_contact";
		$this->setCurrentMail("mail_interaction");
		$this->createMailForCustomers($id_hotline,$obj,$to,$template,$id_hotline_interaction,$pj,$mep);
	}
	
	/**
	* INTERNE
	* Crée un mail d'interaction interne envoyé à tous les intervenants
	* @param string $to une chaine de caractère contenant les emails destinataires
	* @param int $id_hotline
	* @param int $id_hotline_interaction
	* @param mixed $pj La pièce jointe
	*/
	public function createMailInteractionInternal($to,$id_hotline,$id_hotline_interaction,$pj=false){
		if(!$id_hotline) throw new error(ATF::$usr->trans("null_id_hotline"));
		//Paramètres du mail
		$id_hotline=ATF::hotline()->decryptId($id_hotline);
		if(ATF::$usr->getID()){
			$obj=ATF::$usr->trans("mail_hotline_objet8")." ".ATF::user()->nom(ATF::$usr->getID());
		}else{
			ATF::hotline_interaction()->q->reset()->where("id_hotline_interaction", $id_hotline_interaction);
			$interaction = ATF::hotline_interaction()->select_row();
			$obj=ATF::$usr->trans("mail_hotline_objet8")." ".ATF::contact()->nom($interaction["id_contact"]);
		}		
		
		if(!$to) throw new error(ATF::$usr->trans("null_mail_user"));
		$template="hotline_interaction_hotline";
		$this->setCurrentMail("mail_interaction");
		$this->createMailForAT($id_hotline,$obj,$to,$template,$id_hotline_interaction,$pj);
	}
	
	/**
	* INTERNE
	* Crée un mail de transfert à un utilisateur
	* @param int $id_hotline
	*/
	public function createMailUserTransfert($id_hotline,$id_hotline_interaction,$email){
		if(!$id_hotline) throw new error(ATF::$usr->trans("null_id_hotline"));
		//Paramètres du mail
		$id_hotline=ATF::hotline()->decryptId($id_hotline);
		$obj=ATF::user()->nom(ATF::$usr->getID()).' '.ATF::$usr->trans("mail_hotline_objet6").' '.$id_hotline;
		$to=$email;
		if(!$to) throw new error(ATF::$usr->trans("null_mail_user"));
		$template="hotline_transfert";
		$this->setCurrentMail("mail_user_transfert");
		$this->createMailForAT($id_hotline,$obj,$to,$template,$id_hotline_interaction);
	}
	
	/**
	* INTERNE
	* Crée un mail de transfert à un pole
	* @param int $id_hotline
	*/
	public function createMailPoleTransfert($id_hotline,$id_hotline_interaction,$email){
		if(!$id_hotline) throw new error(ATF::$usr->trans("null_id_hotline"));
		//Paramètres du mail
		$id_hotline=ATF::hotline()->decryptId($id_hotline);
		$obj=ATF::user()->nom(ATF::$usr->getID()).' '.ATF::$usr->trans("mail_hotline_objet6").' '.$id_hotline;
		$to=$email;
		if(!$to) throw new error(ATF::$usr->trans("null_mail_user"));
		$template="hotline_transfert";
		$this->setCurrentMail("mail_pole_transfert");
		$this->createMailForAT($id_hotline,$obj,$to,$template,$id_hotline_interaction);
	}
};
?>