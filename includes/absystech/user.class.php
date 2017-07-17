<?
/**
* Classe user_ATF
* @package ATF
* Cet objet permet La manipulation de l'utilisateur connecté, le login de l'application
* C'est un objet central de l'application
*/
require_once dirname(__FILE__)."/../../libs/ATF/includes/user.class.php";
class user_absystech extends user {
	public function __construct() { 
		parent::__construct();
		$this->addPrivilege("autocompleteAssDirection");
		$this->addPrivilege("autocompleteTechnicien");
		$this->onglets = array(
			'societe'=>array('field'=>'societe.id_owner')
			,'contact'=>array('field'=>'contact.id_owner')
			,'suivi'=>array('field'=>'suivi_societe.id_user')
			,'tache'=>array('field'=>'tache_user.id_user')
			,'conge'
			,'devis'
		);
	}
	
	/**
	* Autocomplete pour les assistantes de direction
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos ($_POST habituellement attendu)
	*	string $infos[recherche]
	* @param boolean $reset VRAI si on reset lme querier, FAUX si on a initialisé qqch de précis avant...
	* @return string HTML de retour
	*/
	public function autocompleteAssDirection($infos,$reset=true) {
		if ($reset) {
			$this->q->reset();
		}
		$this->q
			 ->addJointure('user','id_profil','profil','id_profil')
			 ->addCondition("profil.profil","Assistant de direction");
		return $this->autocomplete($infos,false);
	}

	/**
	* Autocomplete pour les techniciens
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos ($_POST habituellement attendu)
	*	string $infos[recherche]
	* @param boolean $reset VRAI si on reset lme querier, FAUX si on a initialisé qqch de précis avant...
	* @return string HTML de retour
	*/
	public function autocompleteTechnicien($infos,$reset=true) {
		if ($reset) {
			$this->q->reset();
		}
		$this->q
			 ->addJointure('user','id_profil','profil','id_profil')
			 ->addCondition("profil.profil","Développeur")
			 ->addCondition("profil.profil","Associé")
			 ->addCondition("profil.profil","Commercial")
			 ->addCondition("profil.profil","Technicien");
		return $this->autocomplete($infos,false);
	}

	public function _updateMailUser($get, $post){
		if(!$post['password_mail']) throw new errorATF("Il manque le mot de passe", 500);
		if(!$post['id_user']) throw new errorATF("Il manque l'id user", 500);

		$this->q->reset();

		return !!$this->update($post);

	}
	
};
class user_att extends user_absystech {
	public function __construct() { 
		parent::__construct();
		$this->colonnes["restante"]["pole"]["default"] = "telecom";
		$this->colonnes["restante"]["pole"]["xtype"] = "hidden";
		$this->colonnes["primary"]["pole"]= $this->colonnes["restante"]["pole"];	
	}
};
class user_demo extends user_absystech { };
?>