<?php
require_once dirname(__FILE__)."/../libs/ATF/libs/ATF/usr.class.php";
class usr_midas extends usr {

	/**
	* Login au portail
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @date 2009-01-15
	* @param array $infos ($_POST habituellement attendu)
	*	string $infos[schema]
	*	string $infos[login]
	*	string $infos[password]
	* @return boolean
	*/
	public function login($infos) {
		if($infos["schema"]!="midas"){
			throw new errorATF("Vous devez vous connecter avec la Société midas",883);
		}
		return parent::login($infos);
	}


};
?>