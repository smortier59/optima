<?
require_once dirname(__FILE__)."/../emailing_contact.class.php";
/** Classe accueil - Gestion de l'accueil
* @package Optima
* @subpackage Cresus
*/
class emailing_contact_pvr extends emailing_contact {
	public function __construct() { 
		parent::__construct(); 
		$this->table = "emailing_contact";

		$this->colonnes['fields_column'] = array(	
			'emailing_contact.email'=>array("renderer"=>"email")
			,'emailing_contact.sollicitation'=>array("width"=>100,"align"=>"center")
			,'emailing_contact.tracking'=>array("width"=>150,"align"=>"center")
			,'emailing_contact.last_tracking'=>array("width"=>150,"align"=>"center")
			,'emailing_contact.last_sollicitation'=>array("width"=>150,"align"=>"center")
			,'abonnement'=>array("custom"=>true)
		);		
	}



	/** Select_all surchargé pour ajouter les abonnements dans les datas
	* @author Quentin JANON <qjanon@absystech.fr>
	* 
	*/
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false) {
		$r = parent::select_all($order_by,$asc,$page,$count);

		foreach ($r['data'] as $k=>$i) {
			$r['data'][$k]['abonnement'] = $this->getAbonnement($i['emailing_contact.id_emailing_contact']?$i['emailing_contact.id_emailing_contact']:$i['id_emailing_contact']);
		}

		return $r;
	}

	/** Renvoi les abonnements en sstring ou en array
	* @author Quentin JANON <qjanon@absystech.fr>
	* 
	*/
	public function getAbonnement($id,$group_concat=true) {
		if ($group_concat) {
			ATF::emailing_contact_abonnement()->q->reset()->addField("GROUP_CONCAT(abonnement)")->where('id_emailing_contact',$id)->addGroup('id_emailing_contact');
			$r = ATF::emailing_contact_abonnement()->select_cell();
		} else {
			ATF::emailing_contact_abonnement()->q->reset()->addField("abonnement")->setStrict()->where('id_emailing_contact',$id);
			$r = ATF::emailing_contact_abonnement()->select_all();
		}

		return $r;
	}

	/** Renvoi tous les emailing_contact trié par abonnements
	* @author Quentin JANON <qjanon@absystech.fr>
	* 
	*/
	public function getAllByAbonnement() {
		$this->q->setCount();
		$all = $this->select_all();
			
		foreach ($all['data'] as $k=>$i) {
			$abs = explode(",",$i['abonnement']);
			foreach ($abs as $ab) {
				$r[$ab][] = $i;
			}
		}

		return $r;
	}

	public function getTradFrequence($frequence,$type="m") {
		switch ($frequence) {
			case "mois":
				if ($type=="m") $lib = "mensuel";
				else $lib = "mensuelle";
			break;
			case "jour":
				if ($type=="m") $lib = "journalier";
				else $lib = "journalière";
			break;
			case "semaine":
				$lib = "hebdomadaire";
			break;

		}	
		return $lib;
	}

};
