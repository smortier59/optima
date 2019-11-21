<?
require_once dirname(__FILE__)."/../ordre_de_mission.class.php";


class ordre_de_mission_atoutcoms extends ordre_de_mission {


	/**
	* Envoi le mail de planification de mission
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	*/
	public function sendMail(){
		return false;
	}
}