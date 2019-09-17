<?
/** Classe facture_paiement
* @package Optima
* @subpackage Absystech
*/
class facture_paiement extends classes_optima {
	function __construct() { // PHP5
		parent::__construct();
		$this->table = __CLASS__;
		$this->colonnes['fields_column'] = array(
			'facture_paiement.id_facture'
			,'facture_paiement.montant'
			,'facture_paiement.mode_paiement'
			,'facture_paiement.date'
			,'facture_paiement.remarques'
			,'facture_paiement.montant_interet'
			,'fichier_joint'=>array("custom"=>true,"nosort"=>true,"type"=>"file","align"=>"center","width"=>50, "renderer"=>"scanner")
			,'factureInteret'=>array("custom"=>true,"nosort"=>true,"type"=>"file","align"=>"center","width"=>50)
		);

		$this->colonnes['primary'] = array(
 			'id_facture'
			,'montant'
			,'mode_paiement'=>array("listeners"=>array("select"=>"ATF.selectModePaiement"))
			,'date'
			,'remarques'
		);

		$this->colonnes['panel']['reference_paiement'] = array(
			"num_cheque"
			,"num_compte"
			,"num_bordereau"
			,"id_facture_avoir"=>array("readonly"=>true,"autocomplete"=>array("function"=>"autocompleteFactureAvoirDispo"))
		);

		//IMPORTANT, complte le tableau de colonnes avec les infos MYSQL des colonnes
		$this->fieldstructure();
		$this->panels['reference_paiement'] = array("visible"=>true);
		$this->files["fichier_joint"] = array("type"=>"pdf");
		$this->files["factureInteret"] = array("type"=>"pdf","no_upload"=>true);

		$this->foreign_key['id_facture_avoir'] = "facture";



		// Champs masqués
		$this->colonnes['bloquees']['insert'] =
		$this->colonnes['bloquees']['cloner'] =
		$this->colonnes['bloquees']['update'] =  array('montant_interet');
		$this->formExt=true;
	}

	/** Surcharge de l'insert afin de remplir le champ date_effective de la table facture
	* @author mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	*/
	function insert($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		ATF::db($this->db)->begin_transaction();
		$this->infoCollapse($infos);
		$facture=ATF::facture()->select($infos["id_facture"]);
		if ($infos['mode_paiement']=="avoir") {
			if (!$infos['id_facture_avoir']) throw new errorATF("Impossible d'insérer un avoir comme mode de paiement si celui ci n'est pas précisé.");

			$infos['id_facture_avoir'] = $this->decryptId($infos['id_facture_avoir']);

			$avoir = ATF::facture()->select($infos['id_facture_avoir']);
			$prix_avoir = abs($avoir["prix"]*$avoir["tva"]);
			$prix_facture = $facture['prix']*$facture['tva'];

			//if ($prix_facture!=$prix_avoir) throw new errorATF("Impossible d'effectuer ce mode de paiement, car le montant (".$prix_facture.") est différend de celui de l'avoir (".$prix_avoir.")");

			$infos['montant'] = $prix_avoir;

			$last_id=$this->insertAvoir($infos);

		} else {
			$last_id=parent::insert($infos,$s,$files);
		}

		$path = $this->filepath($last_id,"factureInteret");
		$this->q->reset()
				->addJointure("facture_paiement","id_facture","facture","id_facture")
				->addField("(facture.prix*facture.tva)","prix_facture")
				->addField("SUM(facture_paiement.montant)","montant_paiement")
				->addField("ROUND((facture.prix*facture.tva)-SUM(facture_paiement.montant),2)","solde")
				->addCondition("`facture_paiement`.`id_facture`",$facture["id_facture"])
				->addGroup("facture.id_facture")
				->setDimension("row");

		$solde=$this->select_all();
		$this->checkFacture($facture["id_facture"],$solde["solde"],$infos["mode_paiement"],$infos["date"]);

		$this->updateInteret($last_id);
		ATF::db($this->db)->commit_transaction();
		ATF::facture()->redirection("select",$infos["id_facture"]);

		return $this->cryptId($last_id);
	}

	/** Surcharge de l'insert afin de remplir le champ date_effective de la table facture
	* @author mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	*/
	function insertAvoir($infos){

		$last_id=parent::insert($infos,$s,$files);

		// Passer l'avoir en etat payé après avoir créer un paiement LETTRAGE pour celle ci
		$paiement = array("id_facture"=>$infos['id_facture_avoir'],"montant"=>$infos['montant'],"mode_paiement"=>"lettrage","date"=>$infos["date"]);
		$id_p_avoir=parent::insert($paiement,$s,$files);

		$avoirToUpdate = array("id_facture"=>$infos['id_facture_avoir'],"etat"=>"payee", "date_effective"=>date("Y-m-d"));
		ATF::facture()->u($avoirToUpdate);

		return $last_id;
	}


	/**
	* Permet de mettre à jour les montants d'intérêt
	* @author mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param int $id id de la facture_paiement
	* @return true
	*/
	public function updateInteret($id){
		$facture_paiement=$this->select($id);
		$facture=ATF::facture()->select($facture_paiement["id_facture"]);
		if($facture["date_previsionnelle"]){
			$nbjours = round((strtotime($facture_paiement["date"]) - strtotime($facture["date_previsionnelle"]))/(60*60*24)-1);

			//Calcul du restant à payer
			ATF::facture_paiement()->q->reset()->where("facture_paiement.id_facture", $facture_paiement["id_facture"]);
			$PartiePaye = ATF::facture_paiement()->select_all();

			$Restant  = round($facture["prix"]*$facture["tva"] , 2);
			foreach ($PartiePaye as $key => $value){
				if($value["facture_paiement.id_facture_paiement"] != $id){
					$Restant = $Restant - $value["facture_paiement.montant"];
				}
			}
			// (Nbre jours de retard x 4,8%) / 365= taux exact à appliquer
			//$montant_interet=$facture_paiement["montant"]*$nbjours*0.01/30;
			$taux=(($nbjours*0.048)/365);
			//$Restant = $Restant/100;
			$montant_interet = 40 + $Restant * $taux;


			if($montant_interet>=1){//65
				$this->u(array("id_facture_paiement"=>$id,"montant_interet"=>$montant_interet));
				$this->move_files($id,$s,false,$infos["filestoattach"]); // Génération du PDF avec les lignes dans la base
			} else {
				$this->delete_file($id,"factureInteret");
			}
		}

		return true;
	}

	/**
	* Surcharge du delete afin de remplir le champ date_effective de la table facture
	* @author mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	*/
	public function delete($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		if (is_numeric($infos) || is_string($infos)) {
			$paiement=$this->select($infos);
			ATF::db($this->db)->begin_transaction();
			parent::delete($infos);
			$this->q
					->reset()
					->addJointure("facture_paiement","id_facture","facture","id_facture")
					->addField("ROUND((facture.prix*facture.tva)-SUM(facture_paiement.montant),2)","solde")
					->addCondition("`facture_paiement`.`id_facture`",$paiement["id_facture"])
					->addGroup("facture.id_facture")
					->setDimension("row")
					->end();
			$solde=$this->select_all();

			$this->checkFacture($paiement["id_facture"],$solde["solde"]);

			ATF::db($this->db)->commit_transaction();
			return $paiement["id_facture"];
		} elseif (is_array($infos) && $infos) {
			foreach($infos["id"] as $key=>$item){
				$id_facture=$this->delete($item);
			}
			ATF::facture()->redirection("select",$id_facture);
			return true;
		}else{
			return false;
		}
	}

	/** Surcharge de l'update pour que la modif d'un paiement mette à jour la facture
	* @author mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	*/
	function update($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		$this->infoCollapse($infos);
		ATF::db($this->db)->begin_transaction();
		parent::update($infos,$s,$files,$cadre_refreshed);
		$facture=ATF::facture()->select($infos["id_facture"]);

		$this->q
				->reset()
				->addJointure("facture_paiement","id_facture","facture","id_facture")
				->addField("ROUND((facture.prix*facture.tva)-SUM(facture_paiement.montant),2)","solde")
				->addCondition("`facture_paiement`.`id_facture`",$facture["id_facture"])
				->addGroup("facture.id_facture")
				->setDimension("row")
				->end();

		$solde=$this->select_all();

		$this->checkFacture($facture["id_facture"],$solde["solde"],$infos["mode_paiement"],$infos["date"]);

		$this->updateInteret($infos["id_facture_paiement"]);

		ATF::db($this->db)->commit_transaction();
	}

	public function checkFacture($id_facture,$solde=false,$mode_paiement=false,$date=false){
		if($mode_paiement=="perte"){
			$this->facturePerte($id_facture,$date);
		}elseif(($solde <= 0) && isset($solde)){
			$this->factureTerminee($id_facture,$date);
		}elseif(($solde > 0) || !$solde){
			$this->factureNonTerminee($id_facture);
		}
	}

	public function facturePerte($id_facture,$date){
		ATF::facture()->u(array("id_facture"=>ATF::facture()->decryptId($id_facture),"date_effective"=>$date,"etat"=>"perte","date_modification"=>date("Y-m-d H:i:s")));
		return true;
	}


	public function factureTerminee($id_facture,$date){
		//Si la totalité de la facture est payée alors son état passe en 'payé'
		ATF::facture()->u(array("id_facture"=>$id_facture,"date_effective"=>$date,"etat"=>"payee","date_modification"=>date("Y-m-d H:i:s")));
		ATF::$msg->addNotice(
			loc::mt(ATF::$usr->trans("notice_update_facture_payee"),array("record"=>ATF::facture()->nom($id_facture)))
			,ATF::$usr->trans("notice_success_title")
		);

		ATF::commande_facture()->q->reset()->addCondition('id_facture',ATF::facture()
										   ->decryptId($id_facture))
										   ->setDimension("row");

		if($commande_facture=ATF::commande_facture()->select_all()){
			$commande=ATF::commande()->select($commande_facture["id_commande"]);
			$sum_facture=ATF::facture()->facture_by_commande($commande["id_commande"]);
			//Si la totalité des factures sont payées alors l'état de l'affaire passe en terminée
			if($commande["prix"]==$sum_facture["prix"]){
				ATF::affaire()->u(array("id_affaire"=>$commande["id_affaire"],"etat"=>"terminee"));
				ATF::$msg->addNotice(
					loc::mt(ATF::$usr->trans("notice_update_affaire_terminee"),array("record"=>ATF::affaire()->nom($commande["id_affaire"])))
					,ATF::$usr->trans("notice_success_title")
				);
			}
		}
	}

	public function factureNonTerminee($id_facture){
		ATF::facture()->u(array("id_facture"=>$id_facture,"date_effective"=>NULL,"etat"=>"impayee","date_modification"=>date("Y-m-d H:i:s")));
		//L'état de la facture passe en facturée
		$facture=ATF::facture()->select($id_facture);
		if($facture["id_affaire"]) ATF::affaire()->u(array("id_affaire"=>$facture["id_affaire"],"etat"=>"facture"));
		return true;
	}

	/**
	* Surcharge du select-All
	*/
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){
		if (!count($this->q->field)) {
			foreach($this->colonnes['fields_column'] as $key=>$item){
				if(!$item["custom"]){
					$this->q->addField($key);
				}
			}
		}
		$return = parent::select_all($order_by,$asc,$page,$count);

		foreach ($return['data'] as $k=>$i) {
			if ($i['facture_paiement.montant_interet']) {
				$return['data'][$k]['allowFactureInteret'] = true;
			} else {
				$return['data'][$k]['allowFactureInteret'] = false;
			}
		}
		return $return;
	}


	/**
	* Retourne la ref pour le scanner
	* @author Morgan Fleurquin <mfleurquin@absystech.fr>
	* @param int $id ID de l'enregistrement
	* @return int $id_user
	*/
	public function getRefForScanner($id,$champs) {
		$paiement = $this->select($id);
		return "Transfert vers paiement d'un montant de ".$paiement["montant"]." de la facture ".ATF::facture()->select($paiement["id_facture"], "ref");
	}


};


class facture_paiement_att extends facture_paiement { };
class facture_paiement_wapp6 extends facture_paiement { };
class facture_paiement_demo extends facture_paiement { };
?>
