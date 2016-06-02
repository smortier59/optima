<?
/** Classe candidat
* @package Optima
*/
class candidat extends classes_optima {
	/*--------------------------------------------------------------*/
	/*                   Attributs                                  */
	/*--------------------------------------------------------------*/
	/*--------------------------------------------------------------*/
	/*                   Constructeurs                              */
	/*--------------------------------------------------------------*/
	function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		
		$this->colonnes['fields_column'] = array(
			 'candidat.nom'
			,'candidat.prenom'
			,'candidat.ville'
			,'candidat.email'=>array("renderer"=>"email")
			,'candidat.etat'=>array("width"=>130,"renderer"=>"etatCandidat")
			,'fichier_joint'=>array("width"=>50,"custom"=>true,"nosort"=>true,"type"=>"file")
		);

		$this->fieldstructure();
		$this->field_nom = "civilite,nom,prenom";
		$this->files["fichier_joint"] = array("multiUpload"=>true);
		$this->addPrivilege("validation");
		$this->colonnes['bloquees']['insert'] =  	
		$this->colonnes['bloquees']['update'] = array('id_user','etat','raison','date');
	}
	/*--------------------------------------------------------------*/
	/*                   Méthodes                                   */
	/*--------------------------------------------------------------*/
	/** Valider ou refuser la candidature 
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array $infos : infos à modifier sur le candidat (notamment l'état et la raison)
	*/
	public function validation($infos){
		$this->update($infos);
		
		$donnees=$this->select($infos['id_candidat']);
		//envoi d'un mail pour prévenir de l'ajout d'un candidat
		$mail = new mail(array( "recipient"=>$donnees['email'], 
								"objet"=>"Réponse à la candidature".($donnees['id_jobs']?" pour l'offre : ".ATF::jobs()->select($donnees['id_jobs'],'intitule'):" spontanée"),
								"template"=>"candidat_etat",
								"donnees"=>$_POST,
								"from"=>ATF::$usr->get('email')));
		$mail->send();
			
		ATF::$msg->addNotice(ATF::$usr->trans("mail_envoye",$this->table));	
		ATF::$cr->block('top');
		ATF::$cr->add("main","generic-select_all.tpl.htm");
	}	
};
?>