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

	/**
	* Permet de récupérer la liste des utilisateurs
	* @package Telescope
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param $get array Paramètre de filtrage, de tri, de pagination, etc...
	* @param $post array Argument obligatoire mais inutilisé ici.
	* @return array un tableau avec les données
	*/
	public function _GET($get,$post) {

		// Gestion du tri
		if (!$get['tri']) $get['tri'] = "id_user";
		if (!$get['trid']) $get['trid'] = "desc";

		// Gestion du limit
		if (!$get['limit'] && !$get['no-limit']) $get['limit'] = 30;

		// Gestion de la page
		if (!$get['page']) $get['page'] = 0;

		$this->q->reset()->addJointure('user','id_profil', 'profil','id_profil')
						 ->select('user.id_user', 'id_user')
						 ->select('user.login', 'login')
						 ->select('user.email', 'email')
						 ->select('user.etat', 'etat')
						 ->select('user.id_profil', 'id_profil')
						 ->select('profil.profil', 'id_profil');


		if ($get['id']) {
			$this->q->where("user.id_user",$get['id'])->setLimit(1);
		} else {
			if ($get['filters']['etat']) $this->q->where("user.etat",$get['filters']['etat'],"OR","sta");

			if (!$get['no-limit']) $this->q->setLimit($get['limit']);

			$data = $this->sa($get['tri'],$get['trid'],$get['page'],true);
			if ($get['id']) {
				$return = $data['data'][0];
			} else {
				header("ts-total-row: ".$data['count']);
				if ($get['limit']) header("ts-max-page: ".ceil($data['count']/$get['limit']));
				if ($get['page']) header("ts-active-page: ".$get['page']);
				if ($get['no-limit']) header("ts-no-limit: 1");
				$return = $data['data'];
			}
			return $return;
		}
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
class user_atoutcoms extends user_absystech { };
class user_nco extends user_absystech { };
class user_i2m extends user_absystech { };
?>
