<?php
/** Classe profil
* @package ATF
*/
class profil extends classes_optima {
	function __construct() { // PHP5
		parent::__construct();
		$this->table = __CLASS__;
		$this->formExt=false;

		$this->addPrivilege("update","update");
	}

	/**
	* Clonage d'un profil avec tous les droits
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function cloner($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL,$nolog=false) {
		$this->infoCollapse($infos);
		
		// On récupère tous les privileges du profil d'origine
		ATF::profil_privilege()->q->reset()->addCondition("id_profil",classes::decryptId($infos["id_profil"]));
		$privileges = ATF::profil_privilege()->select_all();
		if($privileges){
			foreach ($privileges as $p) {
				$m = ATF::module()->select($p["id_module"],"module");
				$priv = ATF::privilege()->select($p["id_privilege"],"privilege");
				if (!ATF::$usr->privilege($m,$priv,$p["field"])) {
					throw new errorATF($m." ".$priv." ".$p["field"]." access denied");
				}
			}
		}
		
		//pas besoin de condition, car si cela se passe mal, il y a un throw et un rollback donc avant comme après, rien exécuté
		ATF::db($this->db)->begin_transaction();
		unset($infos["id_profil"]);
		$id = $this->insert($infos,$s,$files,$cadre_refreshed,$nolog);
		//besoin d'une condition dans le cas où on clone un profil sans privilege (sinon erreur)
		if($privileges){
			ATF::tracabilite()->maskTrace(ATF::profil_privilege()->table);
			foreach ($privileges as $k => $p) {
				unset($privileges[$k]["id_profil_privilege"]);
				$privileges[$k]["id_profil"]=$id;
			}
			ATF::profil_privilege()->multi_insert($privileges);
			ATF::tracabilite()->unmaskTrace(ATF::profil_privilege()->table);
		}
		ATF::db($this->db)->commit_transaction();
		return $id;
	}
	
	/**
	* Modification spécifique du profil : le profil 'associé' doit toujours avoir un seuil à NULL
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @return boolean TRUE si cela s'est correctement passé
	*/	
	public function update($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false) {
		$this->infoCollapse($infos);

		return parent::update($infos,$s,$files,$cadre_refreshed,$nolog);
	}

	public function autocomplete($infos,$reset=true) {
		if ($reset) {
			$this->q->reset();
		} 
		return parent::autocomplete($infos,false);
	}
/**
	* Permet de récupérer la liste des profils pour telescope
	* @package Telescope
	* @author Charlier Cyril <ccharlier@absystech.fr> 
	* @param $get array Argument obligatoire mais inutilisé ici.
	* @param $post array Argument obligatoire mais inutilisé ici.
	* @return array un tableau avec les données
	*/
	public function _GET($get,$post) {
		$length = 25;
		$start = 0;

		$this->q->reset();

		// On ajoute les champs utiles pour l'autocomplete
		$this->q->addField("id_profil")->addField("profil");


		$this->q->setLimit($length,$start)->setPage($start/$length);


		$return = $this->select_all();
		return $return;
	}	

};
?>