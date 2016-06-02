<?

class calendrier extends classes_optima {

	/**
    * Permet de récupérer tous les logins
    * @author Antoine MAITRE <amaitre@absystech.fr>
	* @return array retourne l'array simple contenant tous les logins
    */   	
	
	public function liste() {
		ATF::user()->q->reset()->addField('email')->where('etat', 'normal')->whereIsNotNull('email');
		$sa = ATF::user()->select_all();
		$return = array();
		foreach ($sa as $k => $i) {
			$return[$i['email']] = ATF::user()->nom($i["user.id_user"]);
		}
		return $return;
	}
}

?>