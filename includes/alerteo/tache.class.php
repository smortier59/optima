<?
/** Classe affaire
* @package Optima
* @subpackage Cléodis
*/
require_once dirname(__FILE__)."/../tache.class.php";
class tache_alerteo extends tache {
	function __construct() {
		$this->table = "tache";   
		parent::__construct();
	}
			
	
	/**
    * Valide une tache
    * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array $infos pour la validation
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
    */
	public function valid($infos,&$s,$files=NULL,&$cadre_refreshed){
		// on met a jour les infos de la base avec entre autre la date de validation et le nom du user qui a validé
		//envoi d'un mail à tous les concernés si mise à jour effectuée
		if(parent::u(array("etat"=>"fini",
								"id_aboutisseur"=>ATF::$usr->getID(),
								"date_validation"=>date("Y-m-d H:i"),
								"complete"=>100,
								"id_tache"=>$infos["id_tache"])
						)
			){	
			if ($email_envoye=$this->envoyer_mail($infos["id_tache"],"tache_valid")) {
				ATF::$msg->addNotice(ATF::$usr->trans("email_envoye"));
			}
		}
		
		return $email_envoye;
	}

	
	
	/*
	 * Annule une tâche
 	 * @author Quentin JANON <qjanon@absystech.fr>
 	 * @param id
	 * @return TRUE si vrai, sinon FALSE
	 */	
	function cancel($infos) {
		if (!$infos['id']) return false;
		$d = array("id_tache"=>$this->decryptId($infos['id']),"etat"=>"annule");
		return parent::u($d);
	}
	
	
	/** Selectionne les taches correspondant à une date et au user connecté 
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param date $date
	*/
	public function tache_imminente($date,$nbJour=5){
		if (!$date) $date = date("Y-m-d h:i:s");
		$this->q->reset()
				->addField("tache.tache","tache")
				->addField("societe.id_societe","id_societe")
				->addField("societe.societe","societe")
				->addField("DATE_FORMAT(tache.horaire_fin,'%Y-%m-%d')","date")
				->addField("CONCAT_WS(' ',user.civilite,user.prenom,user.nom)","createur")
				->addJointure('tache','id_tache','tache_user','id_tache')
				->addJointure('tache','id_user','user','id_user')
				->addJointure("tache","id_societe","societe","id_societe")
				->addCondition('tache.horaire_fin',$date,NULL,"sup",'>=')
				->addCondition('tache.horaire_fin',date("Y-m-d h:i:s",strtotime('+'.$nbJour.' days',strtotime($date))),NULL,"inf",'<')
				->addCondition('tache.etat','en_cours')
				->addCondition('tache_user.id_user',ATF::$usr->getID())
				->addOrder("date","asc");
				
		foreach(parent::select_all() as $key=>$item){
			$lignes[$item['date']][]=$item;
		}		
		return $lignes;	
	}
		
};
