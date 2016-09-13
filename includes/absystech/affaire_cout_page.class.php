<?
/**
 * Classe affaire_cout_page
 * @package Optima
 */
class affaire_cout_page extends classes_optima {
	/* ID du user de Seb */
	private $idSmortier = 3;
	/**
	 * Mapping prévu pour un autocomplete sur produit
	 * @var array
	 */
	public static $autocompleteMapping = array(
		array("name"=>'id', "mapping"=>0),
		array("name"=>'nom', "mapping"=>1),
		array("name"=>'date', "mapping"=>2),
		array("name"=>'etat', "mapping"=>3) 
	);

	/**
	 * Constructeur
	 */
	public function __construct() {
		parent::__construct();

		$this->actions_by = array("insert"=>"copieur_contrat","update"=>"copieur_contrat");
		
		//$this->stats_types = array("CA","marge","marge_detail");
		//$this->forecast = array('20'=>'20%','40'=>'40%','60'=>'60%','80'=>'80%');

		$this->table = __CLASS__;
		$this->colonnes['fields_column'] = array(
			'affaire_cout_page.id_societe'
			,'affaire_cout_page.affaire_cout_page'
			,'affaire_cout_page.etat'=>array("renderer"=>"etat","width"=>30)
			,'affaire_cout_page.date'
			,'affaire_cout_page.forecast'=>array("renderer"=>"progress","rowEditor"=>"forecastUpdate","width"=>100)
			,'marge'=>array("custom"=>true,"aggregate"=>array("avg","min","max","sum"/*,"stddev","variance"*/),"align"=>"right","renderer"=>"money","type"=>"decimal","width"=>100)
			,'pourcent'=>array("renderer"=>"percent","custom"=>true,"aggregate"=>array("avg","min","max"),"width"=>80)
		);

		$this->colonnes['primary'] = array(
			"etat"
			,"date"
			,"id_societe"=>array("updateOnSelect"=>true,"custom"=>true)
			,"affaire_cout_page"
			,"forecast"
			,"id_termes"=>array("updateOnSelect"=>true,"custom"=>true)
		);
		
		$this->fieldstructure();

		$this->onglets = array(
			'copieur_facture'=>array('opened'=>true)
			,'copieur_contrat'=>array('opened'=>true)
		);

		$this->autocomplete = array(
			"view"=>array("affaire_cout_page.id_affaire_cout_page","affaire_cout_page.date","affaire_cout_page.etat")
		);

		$this->colonnes['bloquees']['update'] = array("date","etat","id_commercial");


		$this->addPrivilege("u","update");
		$this->addPrivilege("update_termes","update");
		$this->addPrivilege("update_forecast","update");
		$this->addPrivilege("setForecast","update");

		$this->selectExtjs=true;
		$this->foreign_key['id_commercial'] = "user";
	}

	/**
	 * Filtrage d'information selon le profil
	 * @author Yann GAUTHERON <ygautheron@absystech.fr>
	 */
	protected function saFilter(){
		if (ATF::$usr->get("id_profil")==11) {
			// Profil apporteur d'affaire
			$this->q->orWhere("affaire_cout_page.id_commercial",ATF::$usr->getID(),"filtreGeneral","=",true);
		}
	}

	/**
	 * Mise à jour des termes de paiement
	 * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	 * @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	 * @param array &$s La session
	 * @param array $files $_FILES
	 * @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	 * @param array $nolog True si on ne désire par voir de logs générés par la méthode
	 * @return boolean
	 */
	public function update_termes($infos,&$s,$files=NULL,&$cadre_refreshed=NULL){
		if($this->u($infos)){
			ATF::$msg->addNotice(
				loc::mt(ATF::$usr->trans("notice_update_success"),array("record"=>$this->nom($infos["id_affaire_cout_page"])))
				,ATF::$usr->trans("notice_success_title")
			);
			$this->redirection("select",$infos["id_affaire"]);
			return true;
		}else{
			return false;
		}
	}


	/**
	 * Possibilité de supprimer seulement si l'affaire n'a ni devis ni commande ni facture
	 * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	 * @return boolean
	 */
	public function can_delete($id){
		$this->q->reset()
		->addJointure("affaire_cout_page","id_affaire_cout_page","copieur_contrat","id_affaire_cout_page")
		->addJointure("affaire_cout_page","id_affaire_cout_page","copieur_facture","id_affaire_cout_page")
		->addCondition("affaire_cout_page.id_affaire_cout_page",$id)
		->setDimension("row");

		$affaire=parent::select_all();

		if($affaire["id_copieur_facture"] || $affaire["id_copieur_contrat"]){
			throw new errorATF("Il est impossible de supprimer cette affaire car il y a soit un contrat soit une facture",892);
		}else{
			return true;
		}
	}

	/**
	 * Retourne false car impossibilité, àa part si on modifie le terme XOR le code commande client
	 * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	 * @return boolean
	 */
	public function can_update($id,$infos=false){
		if(($infos["id_societe"] || $infos["id_termes"]) && count($infos)==2){
			return true;
		}else{
			throw new errorATF("Il est impossible de modifier une affaire",892);
		}
	}

	/**
	* Met à jour le forecast
	* @author Quezntin JANON <qjanon@absystech.fr>
	* @param array $infos "id_affaire" int l'id_affaire et "forecast" int le forecast
	*/
	public function setForecast($infos){
		if($infos["forecast"]>100||$infos["forecast"]<0){
			throw new errorATF(ATF::$usr->trans("invalid_range"),6512);
		}
		
		$this->u(array("id_affaire_cout_page"=>$infos["id_affaire_cout_page"],"forecast"=>$infos["forecast"]));
		
		$notice=ATF::$usr->trans("update_forecast",$this->table);
		ATF::$msg->addNotice($notice);
	}


};

?>