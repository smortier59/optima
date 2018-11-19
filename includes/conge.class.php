<?
/** 
* @package Optima
*/

class conge extends classes_optima {
	public function __construct() {
		parent::__construct();
		
		$this->table = __CLASS__;
				
		$this->colonnes['fields_column']  = array(
			'conge.id_user'
			,'conge.date_debut'
			,'conge.date_fin'
			,'conge.etat'=>array("renderer"=>"etat","width"=>30)
			,'conge.type'=>array("width"=>80,"align"=>"center")
			,'nbVendredi'=>array("custom"=>true,"align"=>"right","type"=>"decimal","nosearch"=>true,"width"=>80)
			,'duree'=>array("renderer"=>"duree","custom"=>true,"aggregate"=>array("min","avg","max","sum"),"align"=>"right","suffix"=>"j.","type"=>"decimal","nosearch"=>true,"width"=>80)
			,'dureeCetteAnnee'=>array("renderer"=>"duree","custom"=>true,"aggregate"=>array("min","avg","max","sum"),"align"=>"right","suffix"=>"j.","type"=>"decimal","nosearch"=>true,"width"=>80)
			,'actions'=>array("renderer"=>"congeActions","width"=>80,"custom"=>true)
		);
		$this->colonnes['primary']  = array('date_debut','periode','date_fin');
		
		//IMPORTANT, complète le tableau de colonnes avec les infos MYSQL des colonnes
		$this->fieldstructure();
		
		$this->colonnes['bloquees']['insert'] =  array('date_creation','id_user','etat','raison');	
		$this->colonnes['bloquees']['update'] =  array('date_creation','id_user','etat','raison');	
		$this->addPrivilege("validation","update");
		$this->addPrivilege("storeIcal","update");
		$this->addPrivilege("annulation","update");
		$this->addPrivilege("CongesDispo");
		
		$this->no_update=true;
//		$this->no_delete=true;
//		$this->selectAllExtjs=true;
	}
	
	/**
    * @author Yann GAUTHERON <ygautheron@absystech.fr>,Nicolas BERTEMONT <nbertemont@absystech.fr>
	* Surcharge du select-All
	*/
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){
		$m = date("m");
		$Y = date("Y");
		$premiere_annee = ($m>5); // Vrai si on est pas encore dans la seconde année de la tranche
		$annee_debut = ($premiere_annee ? $Y : $Y-1);	
		
		$this->q
			->addField("@duree:=ROUND(
				IF(
					conge.periode='pm' OR conge.periode='am'
					,0.5
					,(@d:=DATEDIFF(conge.date_fin,conge.date_debut)+IF(WEEKDAY(conge.date_fin)<5,1,0))
					"./* Soustraire les dimanche (pas les samedis car on compte les jours ouvrables et pas ouvrées) */"
					-
					IF(
						@d<7
						,IF(
							WEEKDAY(conge.date_debut)>WEEKDAY(conge.date_fin)
							,1"./* Congé de - d'une semaine mais se terminant un jour de la semaine qui suit */"
							,0
						)
						,IF(
							@d<14
							,IF(
								WEEKDAY(conge.date_debut)>WEEKDAY(conge.date_fin)
								,2"./* Congé de - de 2 semaines mais se terminant un jour de la semaine qui suit */"
								,1
							)
							,IF(
								@d<21
								,IF(
									WEEKDAY(conge.date_debut)>WEEKDAY(conge.date_fin)
									,3"./* Congé de - de 3 semaines mais se terminant un jour de la semaine qui suit */"
									,2
								)
								,IF(
									@d<28
									,IF(
										WEEKDAY(conge.date_debut)>WEEKDAY(conge.date_fin)
										,4"./* Congé de - de 4 semaines mais se terminant un jour de la semaine qui suit */"
										,3
									)
									,0"./* En supposant que personne prendra + */"
								)
							)
						)
					)
				)
				"./* Ajouter les vendredi qui comptent double (car on compte les jours ouvrables et pas ouvrées) */"
				+IF(
					WEEKDAY(conge.date_fin)=4 AND conge.periode!='am'
					,1
					,0
				)
			,1)","duree");
		
		$this->q->addField("ROUND(
				IF(
					conge.date_debut>'".$annee_debut."-06-01' AND conge.date_fin<='".($annee_debut+1)."-06-01' AND conge.etat='ok' AND conge.type='paye'
					,@duree
					,0"./* Ces congés ne sont pas dans l'annuité courante, car avant juin de l'année précédente */"
				)
			,1)","dureeCetteAnnee");
		
		// Sert pour le rendu de la colonne état
		$this->q
			->from("conge","id_user","user","id_user")
			->addField("user.id_superieur")
			->addField("user.id_user","id_user")
			->addField("conge.raison")
			->addField("conge.etat");
		$return = parent::select_all($order_by,$asc,$page,$count);
		foreach ($return['data'] as $k=>$i) {
			// Calcul du nombre de vendredis
			$debut = strtotime($i['conge.date_debut']);		
			$fin = strtotime($i['conge.date_fin']);
			$return['data'][$k]['nbVendredi']=0;	
			if ($debut<$fin) {
				for ($d=$debut;$d<=$fin;$d+=86400) {
					if (date("w",$d)==5) {
						$return['data'][$k]['nbVendredi']++;
					}
				}
			} else if (date("w",$debut)==5) {
				$return['data'][$k]['nbVendredi']++;
			}
			
			// Activation des actions
			if ($i['conge.etat']=="en_cours" && ATF::$usr->getID()==$i["user.id_superieur"]) {
				$return['data'][$k]['allowValid'] = true;
				$return['data'][$k]['allowRefus'] = true;
			} else {
				$return['data'][$k]['allowValid'] = false;	
				$return['data'][$k]['allowRefus'] = false;
			}
			
			//tout le monde peut envoyer une demande d'annulation de son congé, quelque soit la date et l'état de ce dernier
			//strpos, pour éviter de recliquer sur le bouton, si un premier envoie a deja été réalisé
			if(ATF::$usr->getID()==$i["id_user"] && (!$i["conge.raison"] || !is_integer(strpos($i["conge.raison"],"->"))) && $i["conge.etat"]!="annule"){
				$return['data'][$k]['allowCancel'] = true;
			}
			
		}
		
		return $return;
	}
	
	/**
    * On check si on a ou non le droit de supprimer les enregistrements
    * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function can_delete($id){
		$infos=$this->select($id);
		$id_superieur=ATF::user()->select($infos['id_user'],"id_superieur");
		//tout le monde peut supprimer son congé si la date de début est supérieur à la date actuelle (congé pas encore passé)
		//le supérieur peut supprimer n'importe quel congé de ses subordonnés
		if((ATF::$usr->getID()==$infos["id_user"] && $infos['date_debut']>date("Y-m-d H:i:s")) || ATF::$usr->getID()==$id_superieur){
			return true;
		}else{
			throw new errorATF("Il est impossible de supprimer un congé qui n'est pas le votre ou dont la date a déjà expiré",893);
		}
	}


	public function CongesDispo($infos){
		$id_conge = $this->decryptId($infos["id_conge"]);
		$id_user = $this->select($id_conge , "id_user");

		if(date("m") <6){
			$dateDebut = date("Y-06-01", strtotime('-1 year'));
			$dateFin = date("Y-05-31"); 
		}else{
			$dateDebut = date("Y-06-01");
			$dateFin = date("Y-05-31", strtotime('+1 year'));
		}

		$this->q->reset()->addField("conge.type")
						->where("conge.id_user", $id_user, "AND")
						->where("conge.type", "paye", "AND")
						->where("conge.etat", "ok", "AND")
						->where("date_debut", $dateDebut ,  "AND", false, ">=")
						->where("date_fin", $dateFin , "AND", false, "<=");

		$nb = 0;

		foreach ($this->select_all() as $key => $value) {
			$nb += $value["duree"];
		}

		return $nb;
	}

	/**
    * Méthode d'insertion
    * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @return int id_conge
    */ 	
	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed){
		$infos = $infos[$this->table];
		if($infos['periode']!=="autre")$infos['date_fin']=$infos['date_debut'];
		if(strtotime($infos['date_fin'])<strtotime($infos['date_debut'])){
			throw new errorATF(ATF::$usr->trans("fin_inf_deb",$this->table));
		}
		if(!$infos["id_user"])$infos["id_user"]=ATF::$usr->getID();		
		
		$id_conge=parent::insert($infos,$s,NULL,$cadre_refreshed);
		
		//on ne peut pas se fier sur la session car dans le cas d'un rollback, ce n'est pas le user de la session
		//donc on récupère les informations de l'utilisateur
		$infos_user = ATF::user()->select($infos["id_user"]);
		
		//si il a un supérieur on lui envoie un mail
		if($infos_user['id_superieur']){
			$infos['id_superieur']=$infos_user['id_superieur'];
			$infos['nom']=ATF::user()->nom($infos["id_user"]);
			$mail = new mail(array(
					"recipient"=>ATF::user()->select($infos_user['id_superieur'],'email')
					,"objet"=>ATF::$usr->trans('adresse','conge')." ".$infos['nom']
					,"template"=>"conge"
					,"optima_url"=>ATF::permalink()->getURL($this->createPermalink($id_conge))
					,"conge"=>$infos
					,"from"=>$infos['nom']." <".$infos_user["email"].">"));
			$mail->send();
		}
		
		return $id_conge;
	}
	
	/**
    * Méthode permettant de valider ou refusé un congé, et envoi d'un mail d'information
    * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array $infos : le paramètre ok ou nok et l'id du congé à modifier
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
    */
	public function validation($infos,&$s,$files=NULL,&$cadre_refreshed){
		$infos_conge=$this->select($infos['id_conge']);	
		parent::update($infos);
		$infos_conge=$this->select($infos['id_conge']);
		$this->conge_zimbra($infos_conge);
		$infos_conge['etat']=$infos['etat'];
		$id_superieur=ATF::user()->select($infos_conge["id_user"],'id_superieur');
		$mail = new mail(array(
			"recipient"=>ATF::user()->select($infos_conge["id_user"],'email')
			,"objet"=>($infos['etat']=="ok"?ATF::$usr->trans("accepte",'conge'):ATF::$usr->trans("refuse",'conge'))
			,"template"=>"conge_validation"
			,"conge"=>$infos_conge
			,"from"=>ATF::user()->nom($id_superieur)." <".ATF::user()->select($id_superieur,'email').">"));
		$envoye=$mail->send();
		
		return $envoye;
	}
	
	/**
    * Méthode de mise à jour
    * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @return int id_conge
    */ 	
	public function update($infos,&$s,$files=NULL,&$cadre_refreshed){
		$infos = $infos[$this->table];
		if($infos['periode']!=="autre")$infos['date_fin']=$infos['date_debut'];
		if(strtotime($infos['date_fin'])<strtotime($infos['date_debut'])){
			throw new errorATF(ATF::$usr->trans("fin_inf_deb",$this->table));
		}
		
		return parent::update($infos,$s,NULL,$cadre_refreshed);
	}

	
	/**
    * Stockage sur l'agenda
    * @author Yann GAUTHERON <ygautheron@absystech.fr>, Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @return int id_conge
    */ 	
	public function storeIcal($infos,&$s,$files=NULL,&$cadre_refreshed){
		if($infos['mdp']){
			//récupération des informations concernant le congé
			$conge=$this->select($infos['id_conge']);
			
			switch($conge['periode']){
				case 'am':
					$date_debut="TZID=Europe/Paris:".str_replace('-','',$conge['date_debut']).'T090000';
					$date_fin="TZID=Europe/Paris:".str_replace('-','',$conge['date_fin']).'T123000';
				break;
				case 'pm':
					$date_debut="TZID=Europe/Paris:".str_replace('-','',$conge['date_debut']).'T140000';
					$date_fin="TZID=Europe/Paris:".str_replace('-','',$conge['date_fin']).'T173000';
				break;
				case 'jour':
					$date_debut="VALUE=DATE:".str_replace('-','',$conge['date_debut']);
					$date_fin="VALUE=DATE:".str_replace('-','',$conge['date_fin']);
				break;
				case 'autre':
					$date_debut="VALUE=DATE:".str_replace('-','',$conge['date_debut']);
					$date_fin="VALUE=DATE:".str_replace('-','',$conge['date_fin']);
				break;
			}
			
			$date = date("Ymd\THis\Z");
			$uid=time();
		
$putString = "BEGIN:VCALENDAR
PRODID:-//Mozilla.org/NONSGML Mozilla Calendar V1.1//EN
VERSION:2.0
BEGIN:VTIMEZONE
TZID:Europe/Paris
X-LIC-LOCATION:Europe/Paris
BEGIN:DAYLIGHT
TZOFFSETFROM:+0100
TZOFFSETTO:+0200
TZNAME:CEST
DTSTART:19700329T020000
RRULE:FREQ=YEARLY;BYDAY=-1SU;BYMONTH=3
END:DAYLIGHT
BEGIN:STANDARD
TZOFFSETFROM:+0200
TZOFFSETTO:+0100
TZNAME:CET
DTSTART:19701025T030000
RRULE:FREQ=YEARLY;BYDAY=-1SU;BYMONTH=10
END:STANDARD
END:VTIMEZONE
BEGIN:VEVENT
CREATED:".$date."
LAST-MODIFIED:".$date."
DTSTAMP:".$date."
UID:".$uid."
SUMMARY:".($conge['conge']?$conge['conge']:"conge")."
DTSTART;".$date_debut."
DTEND;".$date_fin."
X-MOZ-GENERATION:1
END:VEVENT
END:VCALENDAR";
$putData = tmpfile();
fwrite($putData, $putString);
fseek($putData, 0); 

$data = "http://ical.absystech.net/caldav.php/".ATF::user()->select($conge['id_user'],'login')."/absystech/".$uid.".ics";

$ch = ATF::curl()->curlInit($data);

ATF::curl()->curlSetopt(CURLOPT_HTTPAUTH, CURLAUTH_ANY);
ATF::curl()->curlSetopt(CURLOPT_USERPWD, ATF::$usr->getLogin().':'.base64_decode($infos['mdp']));
ATF::curl()->curlSetopt(CURLOPT_RETURNTRANSFER, true);
ATF::curl()->curlSetopt(CURLOPT_PUT, true);
ATF::curl()->curlSetopt(CURLOPT_INFILE, $putData);
ATF::curl()->curlSetopt(CURLOPT_INFILESIZE, strlen($putString));

if(ATF::curl()->curlExec()){
	ATF::$msg->addNotice(ATF::$usr->trans("mdp_incorrect","conge"));
}else{
	ATF::$msg->addNotice(ATF::$usr->trans("ajout_agenda","conge"));
}

ATF::curl()->curlClose();

			ATF::$cr->block('top');
			ATF::$cr->add("main","generic-select_all.tpl.htm");
		}
	}
	
	/** Permet d'envoyer une demande d'annulation / d'annuler un congé
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function annulation($infos,&$s,$files=NULL,&$cadre_refreshed){
		$infos_conge=$this->select($infos['id_conge']);
		
		if($infos['raison']){
			$infos['raison']=$infos_conge['raison']." -> Raison de l'annulation : ".$infos['raison'];
		}
		$infos['etat']="annule";
		parent::update($infos);
		$this->delete_zimbra_conge($infos_conge);
		$infos_conge['etat']=$infos['etat'];
		$infos_conge['raison']=$infos['raison'];
		$id_superieur=ATF::user()->select($infos_conge["id_user"],'id_superieur');
		$mail = new mail(array(
			//"recipient"=>($infos['etat']=="annule"?ATF::user()->select($infos_conge["id_user"],'email'):ATF::user()->select($id_superieur,'email'))
			"recipient"=>ATF::user()->select($infos_conge["id_user"],'email').",".ATF::user()->select($id_superieur,'email')
			,"objet"=>($infos['etat']=="annule"?ATF::$usr->trans("annule",'conge'):ATF::$usr->trans("demande_annule",'conge'))
			,"template"=>"conge_annulation"
			,"conge"=>$infos_conge
			,"optima_url"=>ATF::permalink()->getURL($this->createPermalink($infos_conge['id_conge']))
			,"from"=>ATF::user()->nom($infos_conge["id_user"])." <".ATF::user()->select($infos_conge["id_user"],'email').">"));
		if($envoye=$mail->send()){
			ATF::$msg->addNotice(ATF::$usr->trans("email_envoye",$this->table));
		}
		
		return $envoye;
	}
	
	/** Permets d'actualiser le congé sur zimbra
	*	@author Antoine MAITRE <amaitre@absystech.fr>
	*	@param array $infos contient les infos de la table conge
	*/
	
	public function conge_zimbra($infos) {
		$type = array('paye' => 'CP', 'sans_solde' => 'CSS', 'maladie' => 'CM', 'autre' => 'A');
		$SOAPArray = array();
		$SOAPArray['date_debut'] = str_replace("-", "", $infos['date_debut']);
		$SOAPArray['date_fin'] = str_replace("-", "", $infos['date_fin']);
		$SOAPArray['description'] = $infos['commentaire']?$infos['commentaire']:"Pas de description";
		if ($infos['periode'] == "autre" || $infos['periode'] == "jour") {
			$SOAPArray['allDay'] = 1;
			$SOAPArray['date_debut'] = $SOAPArray['date_debut'].'T000000Z';
			$SOAPArray['date_fin'] = $SOAPArray['date_fin'].'T000000Z';
		} else {
			$SOAPArray['allDay'] = 0;
			if ($infos['periode'] == 'am') {
				$SOAPArray['date_debut'] = $SOAPArray['date_debut'].'T090000';
				$SOAPArray['date_fin'] = $SOAPArray['date_fin'].'T123000';
			}
			else if ($infos['periode'] == 'pm') {
				$SOAPArray['date_debut'] = $SOAPArray['date_debut'].'T140000';
				$SOAPArray['date_fin'] = $SOAPArray['date_fin'].'T173000';
			}
		}
		$SOAPArray['subject'] = 'Conge';
		$SOAPArray['name'] = $type[$infos['type']].' '.$infos['conge'];
		$SOAPArray['zid'] = $infos['zid'];
		$custom=unserialize(ATF::user()->select($infos['id_user'],"custom"));
		$custom['calendrier']['password'] = ATF::preferences()->decryptPasswordMessagerie($custom['calendrier']['password']);
		$mess = ATF::zimbra()->Create_Appointment($SOAPArray);
		if ($custom['calendrier']['username'] && $custom['calendrier']['password']) {
			$return = ATF::zimbra()->Send_Mess_SOAP($mess, $custom['calendrier']['username'], $custom['calendrier']['password']);
		} else {
			ATF::$msg->addWarning("Attention, la personne n'a pas remplis toutes les préférences de son calendrier. Le calendrier zimbra ne se mettra donc pas à jour.", "Attention");
		}
		preg_match('#invId="([0-9a-z\-]+)"#', $return, $matches);
		$infos['zid'] = $matches[1];
		if ($infos['zid'] == NULL) {
			ATF::$msg->addWarning(ATF::$usr->trans("conge_fail_validation_zimbra",$this->table));
			return false;
		}
		parent::update($infos);
		ATF::$msg->addNotice(ATF::$usr->trans("conge_validation_zimbra",$this->table), ATF::$usr->trans("notice_success_title"));
		return true;
	}

	/** Permets de supprimer le congé sur zimbra
	*	@author Antoine MAITRE <amaitre@absystech.fr>
	*	@param array $infos contient les infos de la table conge
	*/

	public function delete_zimbra_conge($infos) {
		if (!$infos['zid']) {
			ATF::$msg->addWarning(ATF::$usr->trans("conge_fail_annulation_zimbra",$this->table));
			return false;
		}
		$zm = new zimbra();
		$mess = $zm->Cancel_Appointment($infos['zid']);
		$custom=unserialize(ATF::user()->select($infos['id_user'],"custom"));
		$custom['calendrier']['password'] = ATF::preferences()->decryptPasswordMessagerie($custom['calendrier']['password']);
		if ($custom['calendrier']['username'] && $custom['calendrier']['password']) {
			$return = ATF::zimbra()->Send_Mess_SOAP($mess, $custom['calendrier']['username'], $custom['calendrier']['password']);
		} else {
			ATF::$msg->addWarning("Attention, la personne n'a pas remplis toutes les préférences de son calendrier. Le calendrier zimbra ne se mettra donc pas à jour.", "Attention");
		}
		ATF::$msg->addNotice(ATF::$usr->trans("conge_annulation_zimbra",$this->table), ATF::$usr->trans("notice_success_title"));
		return true;
	}
};
?>