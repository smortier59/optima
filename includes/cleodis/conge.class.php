<?
/** Classe conge
* @package Optima
* @subpackage Cléodis
*/
require_once dirname(__FILE__)."/../conge.class.php";
class conge_cleodis extends conge {
	public function __construct() {
		parent::__construct();
		$this->table = "conge"; 	
	}
	
	/**
    * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
    * @author Morgan FLEURQUIN  <mfleurquin@absystech.fr>
	* Surcharge du select-All
	*/
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){

		//Il faut retirer les jours Fériers si cela tombe en pleine semaine
		$jours_ferie = array(date("Y-01-01"), /* Jour de l’an */
							 date("Y-05-01"), /* Fête du travail */
							 date("Y-05-08"), /* Victoire du 8 mai 1945 */
							 date("Y-07-14"), /* Fête nationale */
							 date("Y-08-15"), /* Assomption */
							 date("Y-11-01"), /* Toussaint */
							 date("Y-11-11"), /* Armistice */
							 date("Y-12-25")  /* Noël */
							);

		$m = date("m");
		$Y = date("Y");
		$premiere_annee = ($m>5); // Vrai si on est pas encore dans la seconde année de la tranche
		$annee_debut = ($premiere_annee ? $Y : $Y-1);
		$this->q
			->addField("@duree:=ROUND(
				IF(
					conge.periode='pm' OR conge.periode='am'
					,0.5
					,(@d:=DATEDIFF(conge.date_fin,conge.date_debut)+IF(WEEKDAY(conge.date_fin)<6,1,0))
					"./* Soustraire les dimanche et les samedis */"
					-
					IF(
						@d<7
						,IF(
							WEEKDAY(conge.date_debut)>WEEKDAY(conge.date_fin)
							,2"./* Congé de - d'une semaine mais se terminant un jour de la semaine qui suit */"
							,0
						)
						,IF(
							@d<14
							,IF(
								WEEKDAY(conge.date_debut)>WEEKDAY(conge.date_fin)
								,4"./* Congé de - de 2 semaines mais se terminant un jour de la semaine qui suit */"
								,2
							)
							,IF(
								@d<21
								,IF(
									WEEKDAY(conge.date_debut)>WEEKDAY(conge.date_fin)
									,6"./* Congé de - de 3 semaines mais se terminant un jour de la semaine qui suit */"
									,4
								)
								,IF(
									@d<28
									,IF(
										WEEKDAY(conge.date_debut)>WEEKDAY(conge.date_fin)
										,8"./* Congé de - de 4 semaines mais se terminant un jour de la semaine qui suit */"
										,6
									)
									,0"./* En supposant que personne prendra + */"
								)
							)
						)
					)
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
		$return = parent::sa($order_by,$asc,$page,$count);
		foreach ($return['data'] as $k=>$i) {
			//si il s'agit du supérieur direct ou du supérieur placé plus en amont
			if(($i['conge.etat']=="en_cours" || $i['conge.etat']=="attente_jerome" || $i['conge.etat']=="attente_christophe")&& (ATF::$usr->getID() == 17 || ATF::$usr->getID() == 16)){
				if(($i["id_user"] == 16 && ATF::$usr->getID() == 16)){
					$return['data'][$k]['allowValid'] = false;	
					$return['data'][$k]['allowRefus'] = false;
				}else{
					if((ATF::$usr->getID() == 16 && ($i['conge.etat']=="en_cours" || $i['conge.etat']=="attente_jerome"))
					 || (ATF::$usr->getID() == 17 && ($i['conge.etat']=="en_cours" || $i['conge.etat']=="attente_christophe"))){
						$return['data'][$k]['allowValid'] = true;
						$return['data'][$k]['allowRefus'] = true;
					}else{
						$return['data'][$k]['allowValid'] = false;
						$return['data'][$k]['allowRefus'] = false;
					}
				}				
			} else {
				$return['data'][$k]['allowValid'] = false;	
				$return['data'][$k]['allowRefus'] = false;
			}
			
			//tout le monde peut envoyer une demande d'annulation de son congé, quelque soit la date et l'état de ce dernier
			//strpos, pour éviter de recliquer sur le bouton, si un premier envoie a deja été réalisé
			if(ATF::$usr->getID()==$i["id_user"] && (!$i["conge.raison"] || !is_integer(strpos($i["conge.raison"],"->"))) && $i["conge.etat"]!="annule"){
				$return['data'][$k]['allowCancel'] = true;
			}

			//On check les jours feriés
			foreach ($jours_ferie as $i => $j) {				
				if( $j > $return['data'][$k]['conge.date_debut']  	&&  $j < $return['data'][$k]['conge.date_fin'] 	&& date("N", strtotime($j)) != 6 /*Pas un samedi*/ 	&& date("N", strtotime($j)) != 7 /*Pas un dimanche*/){		$return['data'][$k]['duree'] =  $return['data'][$k]['duree'] -1;		}
			}
		}

		
		
		
		return $return;
	}
	
	/** Détermine si la personne connectée est la supérieure directe ou indirecte de la personne qui a créé le congé
    * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param int $id_user : utilisateur connecté
	* @param int $id_superieur : identifiant des utilisateurs potentiellement supérieur du créateur du congé
	*/
	public function estSuperieur($id_user,$id_superieur){
		if($id_superieur){
			//si le supérieur du user est en congé, on autorise une personne ayant les memes infos à valider le congé
			$this->q->reset()->setCountOnly()
								->addCondition('conge.id_user',$id_superieur)
								->addCondition("date_debut","NOW()","OR",false,"<",false,false,true)
								->addCondition("date_fin","NOW()","OR",false,">",false,false,true)
								->addCondition("periode","jour")
								->addCondition("periode","autre")
								->addCondition("conge.etat","ok")
								->addCondition("conge.etat","en_cours");

			if($id_user==$id_superieur){
				return true;
			}elseif($this->select_cell()){
				//si ça l'est, alors on cherche après un user possédant le même profil et id_supérieur
				//et qui ne soit pas en congé
				$sup=ATF::user()->select($id_superieur);
				ATF::user()->q->reset()->addField("user.id_user")
										->setStrict()
										->addJointure("user","id_user","conge","id_user")
										->addCondition("id_profil",$sup["id_profil"])
										->addCondition("user.login","tutul","AND",false,"!=")
										->addCondition("user.login","absystech","AND",false,"!=")
										->addCondition("date_debut","NOW()","OR",false,">",false,false,true)
										->addCondition("date_fin","NOW()","OR",false,"<",false,false,true)
										->addCondition("user.etat","normal")
										->addSuperCondition("date_debut,date_fin","OR","AB",false)
										->addConditionNull("conge.id_conge","OR","C")
										->addSuperCondition("AB,C","OR")
										->setLimit(1);
				if($sup["id_superieur"])ATF::user()->q->addCondition("id_superieur",$sup["id_superieur"]);
				else ATF::user()->q->addConditionNull("id_superieur");					
				if($id_new_sup=ATF::user()->select_cell()){
					return true;			
				}
			}elseif($id_sup=ATF::user()->select($id_superieur,'id_superieur')){
				//sinon on autorise le supérieur du supérieur à valider
				return $this->estSuperieur($id_user,$id_sup);
			}
		}
		return false;
	}
	
	/**
    * Méthode permettant de valider ou refusé un congé, et envoi d'un mail d'information
    * @author Morgan FLEURQUIN  <mfleurquin@absystech.fr>
	* @param array $infos : le paramètre ok ou nok et l'id du congé à modifier
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
    */
	public function validation($infos,&$s,$files=NULL,&$cadre_refreshed){
		$recipient = "";

		$id = $this->decryptId($infos["id_conge"]);
		$recipient .= ATF::user()->select($this->select($id, "id_user") , "email").",".ATF::user()->select(17 , "email").",".ATF::user()->select(16 , "email");

		if($infos["etat"] == "ok"){
			if( ATF::user()->select($this->select($id, "id_user") , "id_profil") != 1){
				if(($this->select($infos["id_conge"] , "etat") == "en_cours" ||  $this->select($infos["id_conge"] , "etat") == 'attente_jerome' || $this->select($infos["id_conge"] , "etat") == 'attente_christophe')  && $this->select($id, "id_user") != 16){
					if(ATF::$usr->getID() == 17){
						if($this->select($infos["id_conge"] , "etat") == "en_cours")	$infos["etat"] = "attente_jerome";
					}else{
						if($this->select($infos["id_conge"] , "etat") == "en_cours")	$infos["etat"] = "attente_christophe";
					}
				}
			}
			
		}

		parent::u($infos);	
		$infos_conge=$this->select($infos['id_conge']);

		$objet = ATF::$usr->trans("accepte",'conge');
		if($infos["etat"] == "nok"){ $objet = ATF::$usr->trans("refuse",'conge'); }
		else{ $objet = ATF::$usr->trans($infos["etat"] ,'conge'); }
		
		$mail = new mail(array(
			"recipient"=>$recipient
			,"objet"=>$objet
			,"template"=>"conge_validation"
			,"conge"=>$infos_conge
			,"from"=>ATF::user()->nom(ATF::$usr->getID())." <".ATF::user()->select(ATF::$usr->getID(),'email').">"));
		
		
		ATF::$msg->addNotice(ATF::$usr->trans("email_envoye"));	
		return $mail->send();			

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

		$id_conge=parent::i($infos,$s,NULL,$cadre_refreshed);
		
		//on ne peut pas se fier sur la session car dans le cas d'un rollback, ce n'est pas le user de la session
		//donc on récupère les informations de l'utilisateur
		$infos_user = ATF::user()->select($infos["id_user"]);
		
		//si il a un supérieur on lui envoie un mail
		if($infos_user['id_superieur']){
			//sauf dans le cas où ce dernier est en congé
			$this->q->reset()->setCountOnly()
								->addCondition('conge.id_user',$infos_user['id_superieur'])
								->addCondition("date_debut","NOW()","OR",false,"<",false,false,true)
								->addCondition("date_fin","NOW()","OR",false,">",false,false,true)
								->addCondition("periode","jour")
								->addCondition("periode","autre")
								->addCondition("conge.etat","ok")
								->addCondition("conge.etat","en_cours");
			//si ça l'est, alors on cherche après un user possédant le même profil et id_supérieur
			//et qui ne soit pas en congé
			if($this->select_cell()){
				$sup=ATF::user()->select($infos_user['id_superieur']);
				ATF::user()->q->reset()->addField("user.id_user")
										->setStrict()
										->addJointure("user","id_user","conge","id_user")
										->addCondition("id_profil",$sup["id_profil"])
										->addCondition("user.login","tutul","AND",false,"!=")
										->addCondition("user.login","absystech","AND",false,"!=")
										->addCondition("date_debut","NOW()","OR",false,">",false,false,true)
										->addCondition("date_fin","NOW()","OR",false,"<",false,false,true)
										->addCondition("user.etat","normal")
										->addSuperCondition("date_debut,date_fin","OR","AB",false)
										->addConditionNull("conge.id_conge","OR","C")
										->addSuperCondition("AB,C","OR")
										->setLimit(1);
				if($sup["id_superieur"])ATF::user()->q->addCondition("id_superieur",$sup["id_superieur"]);
				else ATF::user()->q->addConditionNull("id_superieur");	
				if($id_new_sup=ATF::user()->select_cell()){
					$infos_user['id_superieur']=$id_new_sup;					
				}elseif($id_sup=$sup["id_superieur"]){
					//sinon on autorise le supérieur du supérieur à valider
					$infos_user['id_superieur']=$id_sup;
				}
			}	
			$infos['id_superieur']=$infos_user['id_superieur'];
			$infos['nom']=ATF::user()->nom($infos["id_user"]);
			$mail = new mail(array(
					"recipient"=>ATF::user()->select($infos_user['id_superieur'],'email')
					,"objet"=>ATF::$usr->trans('adresse','conge')." ".$infos['nom']
					,"template"=>"conge"
					,"conge"=>$infos
					,"optima_url"=>ATF::permalink()->getURL($this->createPermalink($id_conge))
					,"from"=>$infos['nom']." <".$infos_user["email"].">"));
			$mail->send();
		}
		
		return $id_conge;
	}

};

class conge_cleodisbe extends conge_cleodis { };
class conge_cap extends conge_cleodis { };
?>