<?	
/** Classe devis
* @package Optima
* @subpackage Cleodis
*/
require_once dirname(__FILE__)."/../produit.class.php";
class produit_lm extends produit {	

	function __construct() {
		parent::__construct();
		$this->table = "produit";
		$this->colonnes['fields_column'] = array(
			'produit.produit',											
			//'produit.id_fournisseur',												 
			//'prix_achat_ht'=>array("custom"=>true,"width"=>80,"rowEditor"=>"setInfos","align"=>"right","renderer"=>"money"),
			'somme_loyers_engages'=>array("custom"=>true,"nosort"=>true,"align"=>"right","renderer"=>"money"),
			'detail_loyers'=>array("custom"=>true),
			'produit.etat'=>array("rowEditor"=>"actifUpdate","renderer"=>"etat","width"=>80),
			'produit.id_pack_produit',
			'produit.id_produit_principal',
			'produit.ordre'=>array("width"=>80,"rowEditor"=>"setInfos"),
			'produit.min'=>array("width"=>80,"rowEditor"=>"setInfos"),
			'produit.max'=>array("width"=>80,"rowEditor"=>"setInfos"),
			'produit.defaut'=>array("width"=>80,"rowEditor"=>"setInfos"),
		);
		
		$this->colonnes['primary']=array('produit',
										'url_produit',
										'etat'=>array("targetCols"=>1),
										'nature'=>array("targetCols"=>1),
										"quantite"=>array("custom"=>true, 
														  "targetCols"=>1,
														  'null'=>true,
														  'xtype'=>'compositefield',
														  'fields'=>array(
																"min"
																,"max"
																,"defaut"
															)),
										'id_pack_produit'=>array("targetCols"=>1),
										'id_produit_principal',
										'ref_lm'=>array("targetCols"=>1),
										'libelle_a_revoyer_lm'=>array("targetCols"=>1),
										"description",
										"ordre"=>array("targetCols"=>1),
										"afficher"=>array("targetCols"=>1),
										'id_fabriquant'
										);
		
		/*$this->colonnes['panel']['caracteristiques']=array('prix_achat_ht',
															'tva_prix_achat',															
												   			'tva_loyer',
															'id_fabriquant',
															'id_fournisseur',
															"mode_paiement"
															);*/
		$this->colonnes["panel"]["fournisseur_lignes"] = array(
			"loyer_fournisseur"=>array("custom"=>true),
			"produit_fournisseur"=>array("custom"=>true),
			'controle_fournisseur',
			'declencheur_mep',
			'tva_prix_achat'
		);


		$this->colonnes["panel"]["loyer_lignes"] = array(
			"loyer"=>array("custom"=>true),
			'tva_loyer'
		);		
		  
		$this->autocomplete = array(
			"field"=>array("produit" , "id")
			,"show"=>array("produit" , "id")
			,"popup"=>array("produit" , "id")
		);
		$this->colonnes['bloquees']['select'] =  array('loyer')	;
		
		$this->panels['primary'] = array('nbCols'=>1,'visible'=>true);		
		$this->panels['loyer_lignes'] = array("visible"=>true, 'nbCols'=>1);
		$this->panels['fournisseur_lignes'] = array("visible"=>true, 'nbCols'=>1);

		$this->files["photo_pop_up"] = array("type"=>"png","convert_from"=>array("jpg","png","gif"),"select"=>true);

		$this->fieldstructure();

		$this->field_nom = "%produit% (Pack %id_pack_produit% )";
		//$this->foreign_key["id_produit_principal"] = "produit"; 

		$this->onglets = array('produit_loyer','produit_fournisseur','produit_fournisseur_loyer');
		//$this->field_nom = "%produit% (%id_pack_produit%)";
				
		$this->addPrivilege("setInfos","update");		
		$this->addPrivilege("actifUpdate");
	}
	
	/**
	 * Permet de modifier un champs en AJAX
	 * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	 * @return bool
	 */
	public function setInfos($infos){
		$res = $this->u(array("id_produit"=> $this->decryptId($infos["id_produit"]),
						  $infos["field"] => $infos[$infos["field"]])
					);
		if($res){
			ATF::$msg->addNotice(
				loc::mt(ATF::$usr->trans("notice_update_success"))
				,ATF::$usr->trans("notice_success_title")
			);
		}		
	}

	public function actifUpdate($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL){
        
		$data["id_produit"] = $this->decryptId($infos["id_produit"]);
        $data["etat"] = $infos["etat"];
               
        if ($r=$this->u($data)) {
            ATF::$msg->addNotice(loc::mt(ATF::$usr->trans("notice_update_success")));
        }
        return $r;
    }

	
	
	/**
	* Surcharge du speed_insert pour pouvoir renvoyer les champs voulus
	* Utilisation d'un querier d'insertion
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	* @version 3
	* @return boolean TRUE si cela s'est correctement passé
	*/	
	public function speed_insert($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL,$nolog=false) {	
		$last_id = $this->insert($infos,$s,$files,$cadre_refreshed,$nolog);
		$result["nom"]=$this->nom($last_id);
		$result["id"]=$last_id;
		$this->q->reset()
				->addCondition("id_".$this->table,$last_id)
				->setDimension("row");

		$result["data"]=$this->sa();
		if($result["data"]["id_fournisseur"]){
			$result["data"]["fournisseur"]=ATF::societe()->nom($result["data"]["id_fournisseur"]);
		}
		return $result;
	}


	/**
	* Surcharge du speed_insert pour permettre de pré-remplir les champs
	* @author mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @return une nouvelle fenêtre
	*/
	public function speed_insert_template(&$infos){
		if($infos["id_produit"] && $infos["id_produit"]!="undefined"){
			$produit=$this->select($infos["id_produit"]);
			foreach($produit as $key=>$item){
				ATF::_r($key,$item);
			}
		}
		
		return parent::speed_insert_template($infos);
	}
	
	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		
		$infos_loyer = json_decode($infos["values_".$this->table]["loyer"],true);
		$infos_fournisseur = json_decode($infos["values_".$this->table]["produit_fournisseur"] , true);
		$infos_loyer_fournisseur = json_decode($infos["values_".$this->table]["loyer_fournisseur"] , true);

		$this->infoCollapse($infos);


		$last_id = parent::insert($infos,$s,$files,$cadre_refreshed,$nolog);

		foreach ($infos_loyer as $key => $value) {
			ATF::produit_loyer()->i(array( "id_produit"=> $this->decryptId($last_id),
										   "loyer"=> $value["produit_loyer__dot__loyer"],
										   "duree"=> $value["produit_loyer__dot__duree"],
										   "nature"=> $value["produit_loyer__dot__nature"],
										   "periodicite"=> $value["produit_loyer__dot__periodicite"],
										   "ordre"=> $key+1
									));
		}

		foreach ($infos_fournisseur as $key => $value) {			
			$id_pf = ATF::produit_fournisseur()->i(array( "id_produit"=> $this->decryptId($last_id),
													      "id_fournisseur"=> $value["produit_fournisseur__dot__id_fournisseur_fk"],
													      "prix_prestation"=> $value["produit_fournisseur__dot__prix_prestation"],
													      "recurrence"=> $value["produit_fournisseur__dot__recurrence"],
													      "departement"=> $value["produit_fournisseur__dot__departement"]
												   ));

		}

		foreach ($infos_loyer_fournisseur as $key => $value) {			
			$id_pf = ATF::produit_fournisseur_loyer()->i(array( "id_produit"=> $this->decryptId($last_id),
														        "id_fournisseur"=> $value["produit_fournisseur__dot__id_fournisseur_fk"],
														        "loyer"=> $value["produit_fournisseur__dot__loyer"],
														        "ordre"=> $value["produit_fournisseur__dot__ordre"],
														        "periodicite"=> $value["produit_fournisseur__dot__periodicite"],
														        "nature"=> $value["produit_fournisseur__dot__rnature"],
														        "nb_loyer"=> $value["produit_fournisseur__dot__nb_loyer"],
														        "departement"=> $value["produit_fournisseur__dot__departement"]
													   ));

		}

		if(is_array($cadre_refreshed)){	ATF::produit()->redirection("select",$last_id);	}
		return $last_id;

	}

	public function update($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){		
		

		$infos_loyer = json_decode($infos["values_".$this->table]["loyer"],true);
		$infos_fournisseur = json_decode($infos["values_".$this->table]["produit_fournisseur"] , true);
		$infos_loyer_fournisseur = json_decode($infos["values_".$this->table]["loyer_fournisseur"] , true);
		unset($infos["values_".$this->table]);
		
		//On supprime les loyers pour les reinserer avant l'update
		ATF::produit_loyer()->q->reset()->where("id_produit",$this->decryptId($infos["produit"]["id_produit"]));
		$loyers = ATF::produit_loyer()->select_all();
		
		foreach ($loyers as $key => $value) {
			ATF::produit_loyer()->d($value["id_produit_loyer"]);
		}


		foreach ($infos_loyer as $key => $value) {
			ATF::produit_loyer()->i(array( "id_produit"=> $this->decryptId($infos["produit"]["id_produit"]),
										   "loyer"=> $value["produit_loyer__dot__loyer"],
										   "duree"=> $value["produit_loyer__dot__duree"],
										   "nature"=> $value["produit_loyer__dot__nature"],
										   "periodicite"=> $value["produit_loyer__dot__periodicite"],
										   "ordre"=> $key+1
									));
		}	

		//On supprime les produits fournisseurs pour les reinserer avant l'update
		ATF::produit_fournisseur()->q->reset()->where("id_produit",$this->decryptId($infos["produit"]["id_produit"]));
		$produit_fournisseurs = ATF::produit_fournisseur()->select_all();
		
		foreach ($produit_fournisseurs as $key => $value) {
			ATF::produit_fournisseur()->d($value["id_produit_fournisseur"]);			
		}

		foreach ($infos_fournisseur as $key => $value) {			
			$id_pf = ATF::produit_fournisseur()->i(array( "id_produit"=> $this->decryptId($infos["produit"]["id_produit"]),
													      "id_fournisseur"=> $value["produit_fournisseur__dot__id_fournisseur_fk"],
													      "prix_prestation"=> $value["produit_fournisseur__dot__prix_prestation"],
													      "recurrence"=> $value["produit_fournisseur__dot__recurrence"],
													      "departement"=> $value["produit_fournisseur__dot__departement"]
												   ));
		}

		//On supprime les produits fournisseurs loyer pour les reinserer avant l'update
		ATF::produit_fournisseur_loyer()->q->reset()->where("id_produit",$this->decryptId($infos["produit"]["id_produit"]));
		$produit_fournisseurs_loyer = ATF::produit_fournisseur_loyer()->select_all();
		
		foreach ($produit_fournisseurs_loyer as $key => $value) {
			ATF::produit_fournisseur_loyer()->d($value["id_produit_fournisseur_loyer"]);			
		}

		foreach ($infos_loyer_fournisseur as $key => $value) {			
			$id_pf = ATF::produit_fournisseur_loyer()->i(array( "id_produit"=> $this->decryptId($infos["produit"]["id_produit"]),
														        "id_fournisseur"=> $value["produit_fournisseur_loyer__dot__id_fournisseur_fk"],
														        "loyer"=> $value["produit_fournisseur_loyer__dot__loyer"],
														        "ordre"=> $value["produit_fournisseur_loyer__dot__ordre"],
														        "periodicite"=> $value["produit_fournisseur_loyer__dot__periodicite"],
														        "nature"=> $value["produit_fournisseur_loyer__dot__nature"],
														        "nb_loyer"=> $value["produit_fournisseur_loyer__dot__nb_loyer"],
														        "departement"=> $value["produit_fournisseur_loyer__dot__departement"]
													   ));

		}


		parent::update($infos,$s,$files);

		if(is_array($cadre_refreshed)){	ATF::produit()->redirection("select",$infos["produit"]["id_produit"]);	}
		return $infos["produit"]["id_produit"];
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
		$this->q
			->addField("SUM(IF(produit_loyer.nature='prolongation',0,produit_loyer.duree * produit_loyer.loyer))","somme_loyers_engages")
			->addField("GROUP_CONCAT(CONCAT(produit_loyer.duree, 'x', produit_loyer.loyer) ORDER BY produit_loyer.ordre ASC SEPARATOR ' + ')","detail_loyers")
			->from("produit","id_produit","produit_loyer","id_produit")

			//->addField("SUM(IF(produit_fournisseur.recurrence='achat',produit_fournisseur.prix_prestation,0))","prix_achat_ht")
			//->from("produit","id_produit","produit_fournisseur","id_produit")

			->addGroup("produit.id_produit")
		;

		$return = parent::select_all($order_by,$asc,$page,$count);
		/*$p = new produit_lm(); // Ne pas écraser le querier courant
		foreach ($return['data'] as $k=>$i) {
			$return['data'][$k]['prix_achat_ht'] = $p->prix_achat_prestation($i['produit.id_produit']);
		}*/
		return $return;
	}

	/**
	* Retourne le prix d'achat total, donc la somme des prix_prestation de ses produit_fournsiseur
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param int $id_produit
	* @return float $prix_total
	
	public function prix_achat_prestation($id_produit){
		ATF::produit_fournisseur()->q->reset()
			->addField("SUM(IF(produit_fournisseur.recurrence='achat',produit_fournisseur.prix_prestation,0))","prix_achat_ht")
			->where("produit_fournisseur.recurrence","achat")
			->where("produit_fournisseur.id_produit",$id_produit)
			->addGroup("produit_fournisseur.id_produit")
			->setDimension('cell')
		;
		return ATF::produit_fournisseur()->sa();
	}*/
}