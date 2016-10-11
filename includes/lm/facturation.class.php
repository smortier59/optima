<?	
/** 
* Classe facturation
* @package Optima
* @subpackage Cléodis
*/
class facturation extends classes_optima {	
	function __construct() {
		$this->table="facturation";
		parent::__construct(); 
		$this->colonnes['fields_column'] = array( 
			  'facturation.id_societe'
			 ,'facturation.id_affaire'
			 ,'facturation.id_facture'
			 ,'facturation.date_periode_debut'
			 ,'facturation.date_periode_fin'
			 ,'facturation.montant'
			 ,'facturation.frais_de_gestion'
			 ,'facturation.assurance'
			 ,'facturation.type'
			 ,'facturation.envoye'
		);
		
		$this->fieldstructure();
		$this->addPrivilege("periode_facturation");
		$this->no_insert = true;
		$this->no_update = true;
		$this->no_delete = true;

		$this->files["global_facture"] = array("type"=>"pdf","no_upload"=>true);
		$this->files["global_factureSociete"] = array("type"=>"pdf","no_upload"=>true);
		$this->files["global_factureCode"] = array("type"=>"pdf","no_upload"=>true);
		$this->files["global_factureDate"] = array("type"=>"pdf","no_upload"=>true);

		$this->files["global_prolongation"] = array("type"=>"pdf","no_upload"=>true);
		$this->files["global_prolongationSociete"] = array("type"=>"pdf","no_upload"=>true);
		$this->files["global_prolongationCode"] = array("type"=>"pdf","no_upload"=>true);
		$this->files["global_prolongationDate"] = array("type"=>"pdf","no_upload"=>true);

		$this->files["grille_prolongationclientSociete"] = array("type"=>"pdf","no_upload"=>true);
		$this->files["grille_prolongationclientCode"] = array("type"=>"pdf","no_upload"=>true);
		$this->files["grille_prolongationclientDate"] = array("type"=>"pdf","no_upload"=>true);

		$this->files["grille_prolongationclient_non_envoyeSociete"] = array("type"=>"pdf","no_upload"=>true);
		$this->files["grille_prolongationclient_non_envoyeCode"] = array("type"=>"pdf","no_upload"=>true);
		$this->files["grille_prolongationclient_non_envoyeDate"] = array("type"=>"pdf","no_upload"=>true);

		$this->files["grille_contratclientSociete"] = array("type"=>"pdf","no_upload"=>true);
		$this->files["grille_contratclientCode"] = array("type"=>"pdf","no_upload"=>true);
		$this->files["grille_contratclientDate"] = array("type"=>"pdf","no_upload"=>true);

		$this->files["grille_contratclient_non_envoyeSociete"] = array("type"=>"pdf","no_upload"=>true);
		$this->files["grille_contratclient_non_envoyeCode"] = array("type"=>"pdf","no_upload"=>true);
		$this->files["grille_contratclient_non_envoyeDate"] = array("type"=>"pdf","no_upload"=>true);

	}
	
	
	/** 
	* Modification de la facturation de l'affaire parente lors de ma modification de la date de la commande fille
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param object $affaire_parente 
	* @param object $affaire 
	* @return boolean
	*/
	function update_facturations($affaire_parente,$affaire){
		ATF::loyer()->q->reset()->Where("id_affaire",$affaire_parente->get("id_affaire"));
		$loyer_parent = ATF::loyer()->sa();
		$commande = $affaire->getCommande();
		$commande_parent = $affaire_parente->getCommande();
		$devis_parent = $affaire_parente->getDevis();
		$date_debut=$commande_parent->get("date_debut");


		//La facturation est possible à partir du moment où le contrat est démarré
		if($date_debut){
			//Si c'est un loyer unique
			if($devis_parent->get("loyer_unique")=='oui'){
				if($loyer_parent[0]["frequence_loyer"]=="an"){
					$frequence=12;
				}elseif($loyer_parent[0]["frequence_loyer"]=="semestre"){	$frequence=6;
				}elseif($loyer_parent[0]["frequence_loyer"]=="trimestre"){
					$frequence=3;				
				}else{
					$frequence=1;
				}
				$date_fin=date("Y-m-d H:i:s",strtotime($date_debut."+".$frequence." month"));
				$date_fin=date("Y-m-d",strtotime($date_fin."-1 day"));

				$this->i(array(
								"id_societe"=>$affaire_parente->get("id_societe"),
								"id_affaire"=>$affaire_parente->get("id_affaire"),
								"montant"=>$loyer_parent[0]['loyer'],
								"assurance"=>$loyer_parent[0]['assurance'],
								"frais_de_gestion"=>$loyer_parent[0]['frais_de_gestion'],
								"date_periode_fin"=>$date_fin,
								"date_periode_debut"=>$date_debut,
								"type"=>"contrat"));
			}else{
				
				
				// Vérification d'une facture de n'importe quel type sur l'affaire parente ayant une date de début et de fin qui chevauche la date de début
				$date_debut_affaire_courante = $commande->get("date_debut");
				ATF::facture()->q->reset()
					->addCondition("id_affaire",$affaire_parente->get("id_affaire"))
					->addCondition("date_periode_debut",$date_debut_affaire_courante,"AND",false,"<=")
					->addCondition("date_periode_fin",$date_debut_affaire_courante,"AND",false,">=")
					->setDimension("row");
				if ($facture_parente_qui_chevauche_le_debut_du_contrat_enfant = ATF::facture()->sa()) {
					// Est-ce que l'avoir est déjà créé ?
					ATF::facture()->q->reset()
						->addCondition("date_periode_debut",$facture_parente_qui_chevauche_le_debut_du_contrat_enfant["date_periode_debut"],"AND",false,"<=")
						->addCondition("date_periode_fin",$facture_parente_qui_chevauche_le_debut_du_contrat_enfant["date_periode_fin"],"AND",false,">=")
						->addCondition("id_affaire",$facture_parente_qui_chevauche_le_debut_du_contrat_enfant["id_affaire"],"AND")
						->addCondition("type_facture","libre","AND")
						->addCondition("prix",0,"AND",false,"<")
						->setDimension("row");

						//log::logger( ATF::facture()->sa() , "mfleurquin");

						if (!($avoir_correspondant = ATF::facture()->sa())) {
							// Avoir non trouvé, alors on raise l'erreur
							$this->raiseErrorAvoirNonTrouve(
								$affaire->get("id_affaire"),
								$date_debut,
								$facture_parente_qui_chevauche_le_debut_du_contrat_enfant["id_affaire"],
								$facture_parente_qui_chevauche_le_debut_du_contrat_enfant["date_periode_debut"],
								$facture_parente_qui_chevauche_le_debut_du_contrat_enfant["date_periode_fin"],
								878
							); }
				}
				
				//Pour chacune des périodes
				foreach ($loyer_parent as $key=>$item){
					if($item["frequence_loyer"]=="an"){
						$frequence=12;
					}elseif($item["frequence_loyer"]=="semestre"){	$frequence=6;
					}elseif($item["frequence_loyer"]=="trimestre"){
						$frequence=3;
					
					}else{
						$frequence=1;
					}
					//Pour chaque échéance d'une période
					for($j=1;$j<=$item['duree'];$j++){
						$date_fin=date("Y-m-d H:i:s",strtotime($date_debut."+".$frequence." month"));
						$date_fin=date("Y-m-d",strtotime($date_fin."-1 day"));						
						$this->q->reset()->addCondition("id_affaire",$affaire_parente->get("id_affaire"),"AND")
										 ->addCondition("date_periode_debut",$date_debut,"AND",false,">=")
										 ->addCondition("date_periode_fin",$date_fin,"AND",false,"<=");
						$facturations_parent = $this->select_row();			
						if($commande->get("date_debut") <= $date_fin){
							if($facturations_parent){
								if($facturations_parent["id_facture"]){
									//Il est possible qu'il y est une double facturation (parent/fille) sur la même période à condition que l'affaire parente est un avoir sur cette période
									$facture=ATF::facture()->select($facturations_parent["id_facture"]);
									ATF::facture()->q->reset()->addCondition("date_periode_debut",$facture["date_periode_debut"],"AND",false,"<=")
															  ->addCondition("date_periode_fin",$facture["date_periode_fin"],"AND",false,">=")
															  ->addCondition("id_affaire",$facture["id_affaire"],"AND")
															  ->addCondition("type_facture","libre","AND")
															  ->addCondition("prix",0,"AND",false,"<");
	
									if(!($factureAvoir=ATF::facture()->sa())){
										// Si aucune facture d'avoir trouvées, alors on prévient l'utilisateur de le créer lui même pour éviter un double retrait bancaire !
										$this->raiseErrorAvoirNonTrouve(
											$affaire->get("id_affaire"),
											$commande->get("date_debut"),
											$affaire_parente->get("id_affaire"),
											$facturations_parent["date_periode_debut"],
											$facturations_parent["date_periode_fin"],
											879
										); }
									
								}else{
									$this->d($facturations_parent["id_facturation"]);
								}
							}
						}else{
							if(!$facturations_parent){
								$this->i(array(
									"id_societe"=>$affaire_parente->get("id_societe"),
									"id_affaire"=>$affaire_parente->get("id_affaire"),
									"montant"=>$item['loyer'],
									"assurance"=>$item['assurance'],
									"frais_de_gestion"=>$item['frais_de_gestion'],
									"date_periode_fin"=>$date_fin,
									"date_periode_debut"=>$date_debut,
									"type"=>"contrat")
								);
							}
						}
						$date_debut=date("Y-m-d H:i:s",strtotime($date_debut."+".$frequence." month"));
					}
				}
			}
			
			//Suppression du fichier de facturation
			ATF::affaire()->delete_file($affaire_parente->get("id_affaire"));

			//Génération du fichier pdf  facturation
			ATF::affaire()->move_files($affaire_parente->get("id_affaire")); 
			return true;
		}else{
			return false;
		}
	}

	/** 
	* Déclenche une erreur sur avoir non trouvé
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $id_affaire 
	* @param string $date_debut_contrat 
	* @param int $id_affaire_parente 
	* @param string $date_periode_debut 
	* @param string $date_periode_fin 
	*/
	function raiseErrorAvoirNonTrouve($id_affaire,$debut_contrat,$id_parente,$periode_debut,$periode_fin,$errno=878) {
		throw new errorATF("Impossible de commencer l'affaire ".ATF::affaire()->nom($id_affaire)." au ".$debut_contrat
			." car l'affaire parente ".ATF::affaire()->nom($id_parente)
			." est facturée pour la période du ".$periode_debut." au ".$periode_fin
			.". Il faut créer un avoir pour cette période.",$errno);
	}

	/** 
	* Insertion des facturations
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param object $commande 
	* @param object $affaire 
	* @param object $affaire_parente 
	* @param object $devis 
	* @param string $type 
	* @return boolean
	*/
	function insert_facturations($commande,$affaire,$affaires_parentes=false,$devis,$type) {
		ATF::loyer()->q->reset()->Where("id_affaire",$affaire->get("id_affaire"))->where("loyer.nature","prolongation","AND",false,"!=")->where("loyer.nature","prolongation_probable","AND",false,"!=");
		$loyer = ATF::loyer()->sa();
		if($commande->get("etat")!="arreter" && $commande->get("etat")!="vente" && $commande->get("etat")!="restitution" && $commande->get("etat")!="restitution_contentieux" && !$commande->isAR() && $loyer){
			
			//***************FACTURATION************************
			$this->delete_special($commande->get("id_affaire"));
		
			$date_debut=$commande->get("date_debut");

			//Si cette affaire annule et remplace une autre affaire il faut également refaire la facturation de l'ancienne affaire
			if($affaires_parentes){
				foreach ($affaires_parentes as $affaire_parente) {
					$this->update_facturations($affaire_parente,$affaire);
				}
			}
			
			//La facturation est possible à partir du moment où le contrat est démarré
			if($date_debut){
				//Si c'est un loyer unique
				if($devis->get("loyer_unique")=='oui'){
					$date_fin=$commande->get("date_evolution");
					$this->i(array(
									"id_societe"=>$affaire->get("id_societe"),
									"id_affaire"=>$affaire->get("id_affaire"),
									"montant"=>$loyer[0]['loyer'],
									"assurance"=>$loyer[0]['assurance'],
									"frais_de_gestion"=>$loyer[0]['frais_de_gestion'],
									"date_periode_fin"=>$date_fin,
									"date_periode_debut"=>$date_debut,
									"type"=>"contrat"));
				}else{
					//Pour chacune des périodes
					foreach ($loyer as $key=>$item){
						if($item["frequence_loyer"]=="an"){
							$frequence=12;
						}elseif($item["frequence_loyer"]=="semestre"){  $frequence=6;
						}elseif($item["frequence_loyer"]=="trimestre"){
							$frequence=3;										
						}else{
							$frequence=1;
						}
						if($item["frequence_loyer"] == "jour"){
							$date_fin=date("Y-m-d H:i:s",strtotime($date_debut."+".$item['duree']." day"));
							$this->i(array(
												"id_societe"=>$affaire->get("id_societe"),
												"id_affaire"=>$affaire->get("id_affaire"),
												"montant"=>$item['loyer'],
												"assurance"=>$item['assurance'],
												"frais_de_gestion"=>$item['frais_de_gestion'],
												"date_periode_fin"=>$date_fin,
												"date_periode_debut"=>$date_debut,
												"type"=>"contrat",
												"nature"=>$item["nature"])
											);							
						}else{
							//Pour chaque échéance d'une période
							for($j=1;$j<=$item['duree'];$j++){
								$date_fin=date("Y-m-d H:i:s",strtotime($date_debut."+".$frequence." month"));
								$date_fin=date("Y-m-d",strtotime($date_fin."-1 day"));
								$echeance = array(
									"id_societe"=>$affaire->get("id_societe"),
									"id_affaire"=>$affaire->get("id_affaire"),
									"montant"=>$item['loyer'],
									"assurance"=>$item['assurance'],
									"frais_de_gestion"=>$item['frais_de_gestion'],
									"date_periode_fin"=>$date_fin,
									"date_periode_debut"=>$date_debut,
									"type"=>"contrat",
									"nature"=>$item["nature"]
								);
								$this->i($echeance);

								// Excheancier de facturation fournisseur
								//ATF::facturation_fournisseur()->createEcheance($affaire, $echeance);

								$date_debut=date("Y-m-d H:i:s",strtotime($date_debut."+".$frequence." month"));


							}
						}
						
					}
				}
				/*******************************************************/
				
				//***************PROLONGATION************************
				//Si la date de commande est modifié, il faut modifier la prolongation
				$this->insert_facturation_prolongation($commande);
				/*******************************************************/

				//Génération du fichier pdf  facturation
				ATF::affaire()->move_files($affaire->get("id_affaire")); 
			}
		}

		return true;
	}

	/** 
	* Insertion d'une facturation
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param object $commande 
	* @param object $affaire 
	* @return int $id
	*/
	function insert_facturation($commande,$affaire) {
		ATF::loyer()->q->reset()->Where("id_affaire",$affaire->get("id_affaire"));
		$loyer = ATF::loyer()->sa();
		if($commande->get("etat")!="arreter" && $commande->get("etat")!="vente" && ($commande->get("etat")!="restitution" || $commande->get("date_prevision_restitution") > date("Y-m-d")) && ($commande->get("etat")!="restitution_contentieux" || $commande->get("date_prevision_restitution") > date("Y-m-d")) && !$commande->isAR() && $loyer){
			//***************PROLONGATION************************

			$date_debut_periode = date("Y-m-d",mktime(0,0,0,date("m"),01,date("Y")));
			$date_debut_periode=date("Y-m-d",strtotime($date_debut_periode."+1 month"));
			$date_fin_periode=date("Y-m-d",strtotime($date_debut_periode."+1 month"));
			/*Je vérifie que je n'ai aucune facturation pour cette période*/
			$this->q->reset()
					->addCondition("id_affaire",$affaire->get("id_affaire"),"AND")
					->addCondition("`facturation`.`date_periode_debut`",$date_debut_periode,"AND",false,">=")
					->addCondition("`facturation`.`date_periode_debut`",$date_fin_periode,"AND",false,"<");
		
			$facturation=$this->sa();
			if(!$facturation){
				ATF::prolongation()->q->reset()->addCondition("id_affaire",$affaire->get("id_affaire"))->setDimension("row");
				$prolongation=ATF::prolongation()->sa();
				if($prolongation && ($prolongation["date_debut"]<=$date_debut_periode)){
					//S'il y a une date arrêt, on ne doit pas facturer
					if($prolongation["date_arret"] && $prolongation["date_arret"]<=$date_debut_periode){
						return false;
					}
					ATF::loyer_prolongation()->q->reset()
											->addCondition("id_affaire",$affaire->get("id_affaire"))
											->addOrder("id_loyer_prolongation","DESC")
											->setDimension("row");
					$dernierLoyer=ATF::loyer_prolongation()->sa();
				
				}else{
					//S'il y a une prolongation pour cette affaire on récupère le dernier loyer
					ATF::loyer()->q->reset()
												->addCondition("id_affaire",$affaire->get("id_affaire"))
												->addOrder("id_loyer","DESC")
												->setDimension("row");
					$dernierLoyer=ATF::loyer()->sa();
				}


				//Je recalcule la fréquence à partir de la dernière prolongation
				if($dernierLoyer['frequence_loyer']=="an"){
					$frequence=12;
				}elseif($dernierLoyer["frequence_loyer"]=="semestre"){	$frequence=6;
				}elseif($dernierLoyer['frequence_loyer']=="trimestre"){
					$frequence=3;
				}elseif($dernierLoyer['frequence_loyer']=="mois"){
					$frequence=1;
				}
				$montant=$dernierLoyer['loyer'];
				$assurance=$dernierLoyer['assurance'];
				$frais_de_gestion=$dernierLoyer['frais_de_gestion'];
				$date_debut=date(date("Y-m",strtotime($date_debut_periode))."-".date("d",strtotime($commande->get("date_debut"))));
				$date_fin=date("Y-m-d",strtotime($date_debut."+".$frequence." month -1 day"));
				$id=$this->i(array(
									"id_societe"=>$affaire->get("id_societe"),
									"id_affaire"=>$affaire->get("id_affaire"),
									"montant"=>$montant,
									"assurance"=>$assurance,
									"frais_de_gestion"=>$frais_de_gestion,
									"date_periode_debut"=>$date_debut,
									"date_periode_fin"=>$date_fin,
									"type"=>"prolongation")
								);
				return $id;
			}
		}elseif(($commande->get("etat")=="restitution" || $commande->get("etat")=="restitution_contentieux") && ($commande->get("date_prevision_restitution") <= date("Y-m-d")) && !$commande->isAR() && $loyer){
			/****************************************  RESTITUTION ****************************************************/		
//log::logger("************************************************************" , "mfleurquin");
//log::logger("Création facture Restitution !!" , "mfleurquin");

			
			$date_debut_periode = date("Y-m-d",mktime(0,0,0,date("m"),01,date("Y")));
			$date_fin_periode = $date_debut_periode;
			$date_debut_periode=date("Y-m-d",strtotime($date_debut_periode."-1 month"));

//log::logger("Date debut periode : ".$date_debut_periode , "mfleurquin");
//log::logger("Date fin periode : ".$date_fin_periode , "mfleurquin");

//log::logger("Date prevision resti : ".$commande->get("date_prevision_restitution") , "mfleurquin");
//log::logger($commande->get("ref") , "mfleurquin");


			if(strtotime($commande->get("date_prevision_restitution")) <= strtotime($date_debut_periode)){
//log::logger("Date commande prevision <= date debut periode" , "mfleurquin");				
			
				//Je vérifie que je n'ai aucune facturation pour cette période
				$this->q->reset()
						->addCondition("id_affaire",$affaire->get("id_affaire"),"AND")
						->addCondition("`facturation`.`date_periode_debut`",$date_debut_periode,"AND",false,">=")
						->addCondition("`facturation`.`date_periode_debut`",$date_fin_periode,"AND",false,"<")
						->addCondition("`facturation`.`montant`",0,"AND",false,">=");
				
				$facturation=$this->sa();		
				if(!$facturation){					
//log::logger("Création de la facture //////////////////////////////" , "mfleurquin");
					ATF::prolongation()->q->reset()->addCondition("id_affaire",$affaire->get("id_affaire"))->setDimension("row");
					$prolongation=ATF::prolongation()->sa();
					if($prolongation && ($prolongation["date_debut"]<=$date_debut_periode)){										
						ATF::loyer_prolongation()->q->reset()
											->addCondition("id_affaire",$affaire->get("id_affaire"))
											->addOrder("id_loyer_prolongation","DESC")
											->setDimension("row");
						$dernierLoyer=ATF::loyer_prolongation()->sa();
						if(!$dernierLoyer){
							ATF::loyer()->q->reset()->addCondition("id_affaire",$affaire->get("id_affaire"))
												->addOrder("id_loyer","DESC")
												->setDimension("row");
							$dernierLoyer=ATF::loyer()->sa();
						}
					}else{
						ATF::loyer()->q->reset()->addCondition("id_affaire",$affaire->get("id_affaire"))
												->addOrder("id_loyer","DESC")
												->setDimension("row");
						$dernierLoyer=ATF::loyer()->sa();
					}
					//Je recalcule la fréquence à partir de la dernière prolongation
					if($dernierLoyer['frequence_loyer']=="an"){
						$frequence=12;
					}elseif($dernierLoyer['frequence_loyer']=="semestre"){	$frequence=6;
					}elseif($dernierLoyer['frequence_loyer']=="trimestre"){
						$frequence=3;
					}elseif($dernierLoyer['frequence_loyer']=="mois"){
						$frequence=1;
					}
					$montant=$dernierLoyer['loyer'];
					$assurance=$dernierLoyer['assurance'];
					$frais_de_gestion=$dernierLoyer['frais_de_gestion'];
					$date_debut=date(date("Y-m",strtotime($date_debut_periode))."-".date("d",strtotime($commande->get("date_debut"))));
					$date_fin=date("Y-m-d",strtotime($date_debut." +".$frequence." month -1 day"));
					$id=$this->i(array(
										"id_societe"=>$affaire->get("id_societe"),
										"id_affaire"=>$affaire->get("id_affaire"),
										"montant"=>$montant,
										"assurance"=>$assurance,
										"frais_de_gestion"=>$frais_de_gestion,
										"date_periode_debut"=>$date_debut,
										"date_periode_fin"=>$date_fin,
										"type"=>"prolongation")
									);								
					return $id;	
				}			
			}}
		return false;
	}

	
	/** 
	* Créé les facturations de prolongation
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param object $commande 
	* @return boolean
	*/
	function insert_facturation_prolongation($commande) {

		ATF::prolongation()->q->reset()->addCondition("id_affaire",$commande->get("id_affaire"))
						   ->setDimension("row");
		$prolongation=ATF::prolongation()->sa();

		if($prolongation){
			//Il faut vérifier que la date début ne soit pas inférieur à la date de fin du contrat

//			$date_debut=date("Y-m-d",strtotime($commande->get("date_evolution")."+ 1 day"));

//			$this->delete_special($commande->get("id_affaire"),"prolongation");

			//On vérifie qu'il y ait au moins une fréquence et une durée
			ATF::loyer_prolongation()->q->reset()->addCondition("id_prolongation",$prolongation['id_prolongation']);
			$loyer_prolongation=ATF::loyer_prolongation()->sa();
//			$prolongation['date_debut']=$date_debut;
			$date_debut=$prolongation['date_debut'];
			foreach($loyer_prolongation as $key=>$item){
				$item["date_debut"]=$date_debut;
				if($item["duree"] && (!$prolongation["date_arret"] || $prolongation["date_arret"]>$date_debut) ){

					if($item['frequence_loyer']=="an"){
						$frequence=12;
					}elseif($item['frequence_loyer']=="semestre"){	$frequence=6;
					}elseif($item["frequence_loyer"]=="trimestre"){
						$frequence=3;
					}elseif($item["frequence_loyer"]=="mois"){
						$frequence=1;
					}
					$item["date_fin"]=$date_debut;
					//Pour chaque échéance d'une période
					for($j=1;$j<=$item['duree'];$j++){
						if(!$date_limite || $date_limite >= $date_debut){
							$date_fin=date("Y-m-d H:i:s",strtotime($date_debut."+".$frequence." month"));
							$date_fin=date("Y-m-d",strtotime($date_fin."-1 day"));

							//La facturatio ne doit pas exister
							$this->q->reset()->addCondition("id_affaire",$commande->get("id_affaire"))
											 ->addCondition("date_periode_debut",$date_debut)
											 ->addCondition("date_periode_fin",$date_fin)
											 ->WhereIsNotNull("id_facture")
											 ->setDimension("row");

							$facturationExist=$this->sa();
							if(!$facturationExist){
								$facturation["id_societe"]=$prolongation["id_societe"];
								$facturation["id_affaire"]=$commande->get("id_affaire");
								$facturation["montant"]=$item["loyer"];
								$facturation["assurance"]=$item["assurance"];
								$facturation["frais_de_gestion"]=$item["frais_de_gestion"];
								$facturation["date_periode_debut"]=$date_debut;
								$facturation["date_periode_fin"]=$date_fin;
								$facturation["type"]="prolongation";
								
								$this->i($facturation);
							}

							$date_debut=date("Y-m-d H:i:s",strtotime($date_debut."+".$frequence." month"));
						}
					}
					$item["date_fin"]=$date_fin;
				}
				ATF::loyer_prolongation()->u($item);
			}
			$prolongation['date_fin']=$date_fin;
			ATF::prolongation()->u($prolongation);
			//Génération du fichier pdf  facturation
			ATF::prolongation()->move_files($prolongation['id_prolongation']); 
		}
		return true;
	}


	/** 
	* Retourn les totaux de facturation d'une affaire
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param int $id_affaire 
	* @param string $type 
	* @return array
	*/
	function montant_total($id_affaire,$type){
		$this->q->reset()->addCondition("id_affaire",$id_affaire)
						 ->addCondition("type",$type)
						 ->addField("SUM(`montant`)","total_ht")
						 ->addField("SUM(`assurance`)","total_assurance")
						 ->addField("SUM(`frais_de_gestion`)","total_frais_de_gestion")
						 ->setDimension("row");

		$totaux=$this->sa();
	
		$totaux["loyer"]=$totaux["total_ht"];
		
		$infos_commande = ATF::affaire()->getCommande($id_affaire)->infos;
		
		$totaux["tva"]=round(($totaux["total_ht"]+$totaux["total_assurance"]+$totaux["total_frais_de_gestion"])*($infos_commande['tva']-1),2);
		
		$totaux["total"]=round($totaux["tva"]+($totaux["total_ht"]+$totaux["total_assurance"]+$totaux["total_frais_de_gestion"]),2);
	
		return $totaux;
	}
	
//	function relance_client($id_facture){
//		
//		$facture=ATF::facture()->select($id_facture);
//		$affaire = ATF::affaire()->select($facture["id_affaire"]);
//		$facturation = ATF::facturation()->select_special("id_facture",$id_facture);
//		$facturation = $facturation[0];
//	
//		$societe = ATF::societe()->select($facture["id_societe"]);
//		$contact= ATF::contact()->select($societe["id_contact_facturation"]); 
//	
//		if($contact){
//			if($contact["email"]){
//					
//					$mail = new mail(array( "recipient"=>$contact["email"]
//							,"objet"=>"Votre facture pour la période ".$facturation["date_periode_debut"]." - ".$facturation["date_periode_fin"]
//							,"ref"=>$affaire["ref"]
//							,"resume"=>$affaire["affaire"]
//							,"template"=>"cleodis_facturation"
//							,"from"=>__SOCIETE__." <".__MAIL_SOCIETE__.">"
//							,"html"=>false));
//					
//					$GLOBALS["pdf"] = new PDF_cleodis();
//					$GLOBALS["classes"]["main"]->file_put_contents($GLOBALS["classes"]["main"]->filepath("facture",$id_facture),$GLOBALS["pdf"]->generic("facture",$id_facture,true));
//	
//					$data = file_get_contents($GLOBALS["classes"]["main"]->filepath("facture",$id_facture));
//					$mail->add_file($data,$id_facture.".pdf");
//					
//					if($mail->send()){
//						parent::update(array("id_facturation"=>$facturation["id_facturation"],"envoye"=>"oui"));
//						return true;
//					}else{
//						return "ai";
//					}
//					
//			}else{
//				return "an";
//
//			}
//			
//		}else{
//			return "pc";
//		}
//	}	

	/** 
	* Permet de supprimer toutes les facturations (contrat et/ou prolongation) d'une affaire
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param int $id_affaire 
	* @param string $type 
	* @return boolean
	*/
	function delete_special($id_affaire,$type=false) {

		//Aucune facturation ne doit avoir été envoyée
		$this->q->reset()->Where("id_affaire",$id_affaire)->Where("envoye","oui");
		if($type){
			$this->q->Where("type",$type);
		}

		if($this->sa()){
			throw new errorATF("Impossible de modifier les dates de cette commande car des factures ont déjà été envoyées sur la base de cet échéancier de l'affaire ".ATF::affaire()->nom($id_affaire),878);
		}

		//Aucune facturation ne doit avoir été facturée
		$this->q->reset()->Where("id_affaire",$id_affaire)->WhereIsNotNull("id_facture");
		if($type){
			$this->q->Where("type",$type);
		}
		if($this->sa()){
			throw new errorATF("Impossible de modifier les dates de cette commande car des factures ont déjà été édités sur la base de l'échéancier de l'affaire ".ATF::affaire()->nom($id_affaire),879);
		}

		$this->q->reset()->Where("id_affaire",$id_affaire);
		if($type){
			$this->q->Where("type",$type)
					->WhereIsNull("id_facture");
		}
		
		if($facturation=$this->sa()){
			foreach($facturation as $key=>$item){
				$this->d($item["id_facturation"]);
			}
		}
		
		//Suppression du fichier de facturation
		ATF::affaire()->delete_file($id_affaire);
/**********************Fin Transaction**************************/

		return true;
	}

	/** 
	* Permet de renvoyer les periodes début et fin d'une facturation d'une affaire par rapport à la date du jour
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param int $id_affaire 
	* @param boolean $date pour prendre en compte (ou non) la date dans la recherche de facturation 
	* @return array facturation
	*/
	function periode_facturation($id_affaire,$date=false) {
		$this->q->reset()->Where("id_affaire",$id_affaire,"AND",1)
						 ->Where("date_periode_debut",date("Y-m-d"),"AND",1,">=")
						 ->setDimension("row")
						 ->addOrder("date_periode_debut","asc");
						 
		if($date){
			$this->q->Where("id_facture",NULL,"AND",1,"IS NULL");
		}
		
		return $this->sa();				 
						 
	}

	/** 
	* Incrémente le tableau renseignant le nombre d'enregistrement contrat envoyé (fc), prolongation envoyée (fp), contrat non envoyé (bfc), prolongation non envoyée (nfp)
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $tab 
	* @param string $type 
	* @param boolean $envoye 
	* @return array $tab
	*/
	function incrementeFacture($tab,$type,$envoye){
		if($type=='contrat'){
			if($envoye){
				$tab["fc"]++;
			}else{
				$tab["nfc"]++;
			}
		}else{
			if($envoye){
				$tab["fp"]++;
			}else{
				$tab["nfp"]++;
			}
		}
		return $tab;
	}
	
	/** 
	* Facturation automatique lancé par une crontab tout les 15 du mois
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	*/
	function facturationMensuelle($tu=false){
		ATF::db($this->db)->begin_transaction();
		$_SESSION["user"] = $this->s["user"] = new usr($this->id_user,"740102508660757876764523051621870449619421205063F");
		$s=$_SESSION["user"];
		log::logger("facturationMensuelle id_user = ".ATF::$usr->getID(),__CLASS__);		
		
		$tab["fc"]= $tab["fp"]= $tab["nfp"]= $tab["nfc"]=0;
		$date_debut=date("Y-m-d",strtotime(date("Y-m-01")."+1 month"));
		$date_fin=date("Y-m-d",strtotime($date_debut."+1 month"));
		$date_fin=date("Y-m-d",strtotime($date_fin."-1 day"));

		$facture_contrat = array();
		$facture_prolongation = array();
		$facturer = array();
		$non_envoye = array();
		
		$cleodis=ATF::societe()->select(246);

		$this->q->reset()
				->addField("facturation.*")
				->addField("LTRIM(`societe`.`societe`)","ltrimsociete")
				->addField("LTRIM(`societe`.`code_client`)","ltrimcode_client")
				->setStrict()
				->addJointure("facturation","id_affaire","affaire","id_affaire",false,false,false,false,"INNER")
				->addJointure("facturation","id_affaire","commande","id_affaire",false,false,false,false,"INNER")
				->addJointure("facturation","id_societe","societe","id_societe",false,false,false,false,"INNER")
				->addCondition("`facturation`.`date_periode_debut`",$date_debut,"AND",false,">=")
				->addCondition("`facturation`.`date_periode_debut`",$date_fin,"AND",false,"<=")
				->addCondition("`facturation`.`envoye`","non","AND")
				->addCondition("`facturation`.`id_facture`",NULL,"AND",false,"IS NULL")
				->addCondition("`affaire`.`etat`","perdue","AND",false,"<>")
				->addCondition("`commande`.`etat`","arreter","AND",false,"<>")						
				->addCondition("commande.date_prevision_restitution",NULL,"AND","date_prevision","IS NULL")
					->addCondition("commande.date_prevision_restitution", date("Y-m-d",strtotime(date("Y-m-01")."+1 month")), "OR", "date_prevision", ">=")
				->addCondition("`commande`.`etat`","restitution","AND","restidate","<>")
					->addCondition("commande.date_prevision_restitution", date("Y-m-d",strtotime(date("Y-m-01")."+1 month")), "OR", "restidate", ">=")
				->addCondition("`commande`.`etat`","restitution_contentieux","AND","restidatecontentieux","<>")				
					->addCondition("commande.date_prevision_restitution", date("Y-m-d",strtotime(date("Y-m-01")."+1 month")), "OR", "restidatecontentieux", ">=")
				->addCondition("`commande`.`etat`","arreter","AND",false,"<>")
				->addCondition("`commande`.`etat`","AR","AND",false,"<>")				
				->addCondition("`affaire`.`nature`","vente","AND",false,"<>");
		
		if($tu){
			$this->q->addCondition("`societe`.`code_client`","TU");
		}
//$this->q->addCondition("`societe`.`id_societe`",1499);
		
		$facturation=$this->sa();
		//Pour chacune des facturations on envoi un mail au client concerné
		foreach ($facturation as $key=>$item) {
			ATF::facture()->q->reset()->addCondition("type_facture","refi")
									  ->addCondition("id_affaire",$item["id_affaire"]);
			$facture_refi = ATF::facture()->sa();
			if($facture_refi){
				$code_refi=ATF::refinanceur()->select($facture_refi[0]["id_refinanceur"],"code_refi");
			}
			//Il faut aussi vérifier que l'affaire ne va pas être céder
			$demande_refi=ATF::demande_refi()->existDemandeRefi($item["id_affaire"]);
			$contact = NULL;		
			
			if(!$facture_refi || ($demande_refi[0]["date_cession"] && ($demande_refi[0]["date_cession"]>$date_debut)) || $code_refi=="REFACTURATION" || $item["type"]=="prolongation"){
				
				$affaire = ATF::affaire()->select($item["id_affaire"]);
				$societe = ATF::societe()->select($affaire["id_societe"]);
				if($societe["id_contact_facturation"]){					
					$contact= ATF::contact()->select($societe["id_contact_facturation"]);				
					$contactSoc = ATF::contact()->select($contact["id_contact"] , "id_societe");					
					if($contactSoc !== $affaire["id_societe"]){
						$contact = NULL;
					}					 
				}else{
					$contact = NULL;
				}
				if($id_facture=$this->insert_facture($affaire,$item)){
					$item["id_facture"]=$id_facture;
					if($id_facture!="montant_zero"){
						if($item["type"]=="prolongation"){
							$facture_prolongation=$this->formateTabfacturer($facture_prolongation,$item,"prolongation",$id_facture);
						}else{
							$facture_contrat=$this->formateTabfacturer($facture_contrat,$item,"facture",$id_facture);
						}
						if($contact && $item["type"] !=="prolongation"){							
							if($contact["email"]){
								
								$path=array("facture"=>"fichier_joint");
								
								$email["email"]=$contact["email"];
								$email["texte"]="Votre facture pour la période ".$item["date_periode_debut"]." - ".$item["date_periode_fin"];	
								
								//ATF::affaire()->mailContact($email,$id_facture,"facture",$path);
								$data_fact_attente = array("mail"=> json_encode($email),
														   "id_facture"=> $id_facture,
														   "nom_table"=>"facture",
														   "path"=> json_encode($path),
														   "id_facturation"=> $item["id_facturation"]
														  );
								ATF::facturation_attente()->insert($data_fact_attente);


								$item["email"]=$contact["email"];
								$item["envoye"]='non';
								$facturer=$this->formateTabfacturer($facturer,$item,"client",false,$item["type"]);
								$tab=$this->incrementeFacture($tab,$item["type"],true);
								$this->u(array("id_facturation"=>$item["id_facturation"],"envoye"=>"non"));
								
							}else{
								$item["cause"]="an";
								$non_envoye=$this->formateTabfacturer($non_envoye,$item,"client_non_envoye",false,$item["type"]);
								$tab=$this->incrementeFacture($tab,$item["type"],false);
							}							
						}else{
							$item["cause"]="pc";
							$non_envoye=$this->formateTabfacturer($non_envoye,$item,"client_non_envoye",false,$item["type"]);
							$tab=$this->incrementeFacture($tab,$item["type"],false);
						}
						
						log::logger("Création facture contrat de l'affaire ".$affaire["ref"],__CLASS__);
					}else{
						log::logger("------------------------------------Facture à zéro ".$affaire["ref"],__CLASS__);
					}
				}else{
					$item["cause"]="pi";
					$non_envoye=$this->formateTabfacturer($non_envoye,$item,"client_non_envoye",false,$item["type"]);
					$tab=$this->incrementeFacture($tab,$item["type"],false);
				}
			}
		}

		/************************************PROLONGATIONS QUI NE SONT PAS DANS LA TABLE PROLONGATION***********************************************************/

//		$this->q->reset()->addField('id_affaire')
//					     ->setStrict()
//						 ->addCondition("`facturation`.`date_periode_debut`",$date_debut,"AND",false,"<=")
//						 ->addCondition("`facturation`.`date_periode_fin`",date("Y-m-d",strtotime($date_fin."-1 day")),"AND",false,">=")
//						 ->addCondition("`facturation`.`id_affaire`","`commande`.`id_affaire`","AND")
//						 ->setToString();
//		$subQuery = $this->sa();

//		ATF::commande()->q->reset()
//						  ->addField("commande.*")
//						  ->setStrict()
//						  ->addJointure("affaire","id_affaire","affaire","id_affaire",false,false,false,false,"INNER")
//						  ->addCondition("`commande`.`date_evolution`",$date_fin,"AND",false,"<")
//						  ->addCondition("`affaire`.`etat`","perdue","AND",false,"<>")
//						  ->addCondition("`commande`.`etat`","arreter","AND",false,"<>")
//						  ->addCondition("`commande`.`etat`","AR","AND",false,"<>")
//						  ->addCondition("`commande`.`etat`","vente","AND",false,"<>")
//						  ->setSubQuery($subQuery)
//						  ->addOrder("`commande`.`id_affaire`")
//						  ->setToString();
//
//
//		$prolongation=ATF::commande()->sa();

		$query="SELECT `commande`.* , LTRIM(`societe`.`societe`) as ltrimsociete, LTRIM(`societe`.`code_client`) as ltrimcode_client
				FROM `commande`
				INNER JOIN `affaire` ON `affaire`.`id_affaire` = `commande`.`id_affaire`
				INNER JOIN `societe` ON `societe`.`id_societe` = `commande`.`id_societe`
				LEFT JOIN `prolongation` ON `prolongation`.`id_affaire` = `commande`.`id_affaire`
				WHERE `commande`.`date_evolution` < '".$date_fin."'
				AND (
					`prolongation`.`date_arret` IS NULL 
					OR  `prolongation`.`date_arret` <  '".$date_fin."'
				)
				AND `commande`.`etat` != 'arreter'
				AND `commande`.`etat` != 'AR'
				AND ( 
				        commande.date_prevision_restitution IS NULL
					 OR commande.date_prevision_restitution >= '".date("Y-m-d",strtotime(date("Y-m-01")."+1 month"))."'					
				)
				AND (`commande`.`etat` != 'restitution' OR commande.date_prevision_restitution >= '".date("Y-m-d",strtotime(date("Y-m-01")."+1 month"))."' )
				AND (`commande`.`etat` != 'restitution_contentieux'  OR commande.date_prevision_restitution >= '".date("Y-m-d",strtotime(date("Y-m-01")."+1 month"))."' )
				AND `affaire`.`nature` != 'vente'
				AND `affaire`.`etat` != 'perdue'";
			   
		if($tu){
			$query.="
				AND `societe`.`code_client`='TU'
			";
		}
//$query.=" AND `societe`.`id_societe`=1499 ";
		
		$query.="
			    AND `affaire`.`id_affaire` NOT
				IN (
					SELECT `id_affaire` 
					FROM  `facture` 
					WHERE 
					(
						(
							 `facture`.`date_periode_debut` >=  '".$date_debut."'
							 OR 
							 `facture`.`date_periode_fin` >=  '".$date_fin."'
						)
					)
					AND 
					 `facture`.`type_facture` =  'facture'
					AND 
					 `facture`.`id_affaire` =  `commande`.`id_affaire`
				)";
				
		$prolongation=ATF::db()->sql2array($query);	
		foreach ($prolongation as $key=>$item) {
			$objAffaire = new affaire_lm($item['id_affaire']);
			$objCommande = $objAffaire->getCommande();

			try {
				$id_facturation=$this->insert_facturation($objCommande,$objAffaire);
			} catch (errorATF $e) { log::logger("!!!!!!!! Erreur d'insertion de facturation : ".$e->getMessage(),__CLASS__); }
				
			if($id_facturation){				
				$facturation=$this->select($id_facturation);
				$facturation["ltrimsociete"]=$item["ltrimsociete"];
				$facturation["ltrimcode_client"]=$item["ltrimcode_client"];
				$affaire = ATF::affaire()->select($item["id_affaire"]);
				if($id_facture=$this->insert_facture($affaire,$facturation)){
					$facturation["id_facture"]=$id_facture;
					if($id_facture!="montant_zero"){
						$facture_prolongation=$this->formateTabfacturer($facture_prolongation,$facturation,"prolongation",$id_facture);
						$societe = ATF::societe()->select($affaire["id_societe"]);
						if($societe["id_contact_facturation"]){
							$contact= ATF::contact()->select($societe["id_contact_facturation"]); 
						}
						/*if($contact){
							if($contact["email"]){
								$path=array("facture"=>"fichier_joint");
								$email["email"]=$contact["email"];
								$email["texte"]="Votre facture pour la période ".$facturation["date_periode_debut"]." - ".$facturation["date_periode_fin"];
	
								//ATF::affaire()->mailContact($email,$id_facture,"facture",$path);
								$data_fact_attente = array("mail"=> json_encode($email),
														   "id_facture"=> $id_facture,
														   "nom_table"=>"facture",
														   "path"=> json_encode($path),
														   "id_facturation"=> $id_facturation
														  );
								ATF::facturation_attente()->insert($data_fact_attente);

								$facturation["email"]=$contact["email"];
								$facturation["envoye"]="non";
								//log::logger($facturer , "mfleurquin");
								$facturer=$this->formateTabfacturer($facturer,$facturation,"client",false,"prolongation");
								$tab=$this->incrementeFacture($tab,"prolongation",true);

								$this->u(array("id_facturation"=>$id_facturation,"envoye"=>"non"));
							}else{
								$facturation["cause"]="an";
								$non_envoye=$this->formateTabfacturer($non_envoye,$facturation,"client_non_envoye",false,"prolongation");
								$tab=$this->incrementeFacture($tab,"prolongation",false);
							}
						}else{ */
							$facturation["cause"]="pc";
							$non_envoye=$this->formateTabfacturer($non_envoye,$facturation,"client_non_envoye",false,"prolongation");
							$tab=$this->incrementeFacture($tab,"prolongation",false);
						/*}*/
						log::logger("Création prolongation contrat de l'affaire ".$affaire["ref"],__CLASS__);
					}else{
						log::logger("------------------------------------Facture à zéro prol ".$affaire["ref"],__CLASS__);
					}
				// }else{
					// $this->d($facturation["id_facturation"]);
					// $facturation["cause"]="pi";
					// $non_envoye=$this->formateTabfacturer($non_envoye,$facturation,"client_non_envoye",false,"prolongation");
					// $tab=$this->incrementeFacture($tab,"prolongation",false);
				}
			} else {
				log::logger("Facturation non trouvee, alors on ignore l'affaire ".$objAffaire->get('ref'),__CLASS__);	
			}
		}
		
		
		

		log::logger("Commit de la transaction",__CLASS__);
		ATF::db($this->db)->commit_transaction();
		
		//Envoi du mail à Cléodis des facturations envoyées
		if($facturer){
			log::logger("Envoi du mail à Cléodis des facturations envoyées en fin de mois...",__CLASS__);	
			$this->sendGrille($facturer,$tab["fc"],$tab["fp"],$date_debut,$date_fin,"grille_","Grille de facturation des factures qui seront envoyées en fin de mois",$s);
		}
		
		//Envoi du mail à Cléodis des facturations non envoyées
		if($non_envoye){
			log::logger("Envoi du mail à Cléodis des facturations non envoyées...",__CLASS__);	
			$this->sendGrille($non_envoye,$tab["nfc"],$tab["nfp"],$date_debut,$date_fin,"grille_","grille_","Grille de facturation des factures non envoyées",$s);
		}	

		//Envoi d'un pdf contenant toutes les factures contrat
		log::logger("Envoi d'un pdf contenant toutes les factures contrat...",__CLASS__);
		$this->sendFactures($date_debut,$date_fin,$facture_contrat,"global_","Factures contrat",$s);

		//Envoi d'un pdf contenant toutes les factures prolongation
		log::logger("Envoi d'un pdf contenant toutes les factures prolongation...",__CLASS__);
		$this->sendFactures($date_debut,$date_fin,$facture_prolongation,"global_","Factures prolongation",$s);
		
		$return["facturer"]=$facturer;
		$return["non_envoye"]=$non_envoye;
		$return["facture_contrat"]=$facture_contrat;
		$return["facture_prolongation"]=$facture_prolongation;
		$return["date_debut"]=$date_debut;
		$return["date_fin"]=$date_fin;
					
		log::logger("Batch terminé.",__CLASS__);	
		return $return;
	}


	function facturationMensuelleRestitution($tu=false){
		ATF::db($this->db)->begin_transaction();
		$_SESSION["user"] = $this->s["user"] = new usr($this->id_user,"740102508660757876764523051621870449619421205063F");
		$s=$_SESSION["user"];
		log::logger("facturationMensuelleRestitution id_user = ".ATF::$usr->getID(),__CLASS__);		
		
		$tab["fc"]= $tab["fp"]= $tab["nfp"]= $tab["nfc"]=0;
		$date_debut=date("Y-m-d",strtotime(date("Y-m-01")."+1 month"));
		$date_fin=date("Y-m-d",strtotime($date_debut."+1 month"));
		$date_fin=date("Y-m-d",strtotime($date_fin."-1 day"));

		$facture_contrat = array();
		$facture_prolongation = array();
		$facturer = array();
		$non_envoye = array();
		
		$cleodis=ATF::societe()->select(246);
		
		/*
		 * RESTITUTION  
		 * Si la date de fin de contrat est atteinte, on génère une facturation à m+1 comme une prolongation par rapport à la date de prévision de restitution
		 * On arrete de générer une facturation une fois que la date de restitution effective est renseignée
		 */
		 $date_deb=date("Y-m-d",strtotime(date("Y-m-01")."-1 month"));
		 //INNER JOIN facturation ON facturation.id_affaire = commande.id_affaire
		 $query="SELECT `commande`.* , LTRIM(`societe`.`societe`) as ltrimsociete, LTRIM(`societe`.`code_client`) as ltrimcode_client
				FROM `commande`
				INNER JOIN `affaire` ON `affaire`.`id_affaire` = `commande`.`id_affaire`
				INNER JOIN `societe` ON `societe`.`id_societe` = `commande`.`id_societe`
				WHERE `commande`.`etat` != 'non_loyer'
				AND `commande`.`etat` != 'mis_loyer'
				AND `commande`.`etat` != 'prolongation'
				AND `commande`.`etat` != 'AR'
				AND `commande`.`etat` != 'arreter'				
				AND `commande`.`etat` != 'vente'
				AND `commande`.`etat` != 'mis_loyer_contentieux'
				AND `commande`.`etat` != 'prolongation_contentieux'
				AND  commande.date_prevision_restitution <= NOW()
				AND `affaire`.`nature` != 'vente'
				AND `affaire`.`etat` != 'perdue'
				AND `date_restitution_effective` IS NULL ";
		
		if($tu){
			$query .= "AND societe.code_client = 'TU'";
		}
		
		$restitution=ATF::db()->sql2array($query);	
		//log::logger($restitution , "mfleurquin");	
		foreach($restitution as $key => $item){
								
			/*if(strtotime($item["date_evolution"]) > strtotime($date_deb)){
				//En restitution mais dans la periode du contrat
				//Facturation normale				
				
				$this->q->reset()
						->addField("facturation.*")
						->addField("LTRIM(`societe`.`societe`)","ltrimsociete")
						->addField("LTRIM(`societe`.`code_client`)","ltrimcode_client")
						->setStrict()
						->addCondition("`facturation`.`date_periode_debut`",$date_debut,"AND",false,">=")
						->addCondition("`facturation`.`date_periode_debut`",$date_fin,"AND",false,"<=")
						->addJointure("facturation","id_affaire","affaire","id_affaire",false,false,false,false,"INNER")
						->addJointure("facturation","id_affaire","commande","id_affaire",false,false,false,false,"INNER")
						->addJointure("facturation","id_societe","societe","id_societe",false,false,false,false,"INNER")						
						->where("commande.id_commande",$item["id_commande"]);

				if($tu){
					$this->q->addCondition("`societe`.`code_client`","TU");
				}				
				$facturation=$this->sa();
								
				//Pour chacune des facturations on envoi un mail au client concerné
				foreach ($facturation as $key=>$item) {
					ATF::facture()->q->reset()->addCondition("type_facture","refi")
											  ->addCondition("id_affaire",$item["id_affaire"]);
					$facture_refi = ATF::facture()->sa();
					if($facture_refi){
						$code_refi=ATF::refinanceur()->select($facture_refi[0]["id_refinanceur"],"code_refi");
					}
					//Il faut aussi vérifier que l'affaire ne va pas être céder
					$demande_refi=ATF::demande_refi()->existDemandeRefi($item["id_affaire"]);
					$contact = NULL;		
					
					if(!$facture_refi || ($demande_refi[0]["date_cession"] && ($demande_refi[0]["date_cession"]>$date_debut)) || $code_refi=="REFACTURATION" || $item["type"]=="prolongation"){
						
						$affaire = ATF::affaire()->select($item["id_affaire"]);
						$societe = ATF::societe()->select($affaire["id_societe"]);
						if($societe["id_contact_facturation"]){					
							$contact= ATF::contact()->select($societe["id_contact_facturation"]);				
							$contactSoc = ATF::contact()->select($contact["id_contact"] , "id_societe");					
							if($contactSoc !== $affaire["id_societe"]){
								$contact = NULL;
							}					 
						}else{
							$contact = NULL;
						}
						if($id_facture=$this->insert_facture($affaire,$item)){
							$item["id_facture"]=$id_facture;
							if($id_facture!="montant_zero"){
								if($item["type"]=="prolongation"){
									$facture_prolongation=$this->formateTabfacturer($facture_prolongation,$item,"prolongation",$id_facture);
								}else{
									$facture_contrat=$this->formateTabfacturer($facture_contrat,$item,"facture",$id_facture);
								}
								if($contact){							
									if($contact["email"]){
										
										$path=array("facture"=>"fichier_joint");
										
										$email["email"]=$contact["email"];
										$email["texte"]="Votre facture pour la période ".$item["date_periode_debut"]." - ".$item["date_periode_fin"];
			
										ATF::affaire()->mailContact($email,$id_facture,"facture",$path);
										$item["email"]=$contact["email"];
										$item["envoye"]='oui';
										$facturer=$this->formateTabfacturer($facturer,$item,"client",false,$item["type"]);
										$tab=$this->incrementeFacture($tab,$item["type"],true);
										$this->u(array("id_facturation"=>$item["id_facturation"],"envoye"=>"oui"));
										
									}else{
										$item["cause"]="an";
										$non_envoye=$this->formateTabfacturer($non_envoye,$item,"client_non_envoye",false,$item["type"]);
										$tab=$this->incrementeFacture($tab,$item["type"],false);
									}
									
								}else{
									$item["cause"]="pc";
									$non_envoye=$this->formateTabfacturer($non_envoye,$item,"client_non_envoye",false,$item["type"]);
									$tab=$this->incrementeFacture($tab,$item["type"],false);
								}
								
								log::logger("Création facture contrat de l'affaire ".$affaire["ref"],__CLASS__);
							}else{
								log::logger("------------------------------------Facture à zéro ".$affaire["ref"],__CLASS__);
							}
						}else{
							$item["cause"]="pi";
							$non_envoye=$this->formateTabfacturer($non_envoye,$item,"client_non_envoye",false,$item["type"]);
							$tab=$this->incrementeFacture($tab,$item["type"],false);
						}
					}
				}			 			
			}else{	*/			
				//On verifie qu'il n'y ait pas de facture pour la periode $date_debut
				$query =	"SELECT `id_affaire` 
					FROM  `facture` 
					WHERE 
					(   '".$date_deb."' BETWEEN `facture`.`date_periode_debut` AND `facture`.`date_periode_fin`
					)
					AND `facture`.`type_facture` =  'facture'
					AND `facture`.`id_affaire` =  ".$item["id_affaire"];
				$facturePresente=ATF::db()->sql2array($query);
				//Si il n'y a pas de facture 
				if(!$facturePresente){					
					//En restitution et fin de contrat dépassée
					$objAffaire = new affaire_lm($item['id_affaire']);
					$objCommande = $objAffaire->getCommande();
		
					try {
						$id_facturation=$this->insert_facturation($objCommande,$objAffaire);
					} catch (errorATF $e) { log::logger("!!!!!!!! Erreur d'insertion de facturation : ".$e->getMessage(),__CLASS__); }
						
					if($id_facturation){				
						$facturation=$this->select($id_facturation);
						$facturation["ltrimsociete"]=$item["ltrimsociete"];
						$facturation["ltrimcode_client"]=$item["ltrimcode_client"];
						$affaire = ATF::affaire()->select($item["id_affaire"]);
						if($id_facture=$this->insert_facture($affaire,$facturation, date("Y-m-d"))){
							$facturation["id_facture"]=$id_facture;
							if($id_facture!="montant_zero"){
								$societe = ATF::societe()->select($affaire["id_societe"]);
								if($societe["id_contact_facturation"]){
									$contact= ATF::contact()->select($societe["id_contact_facturation"]); 
								}							
								$facture_prolongation=$this->formateTabfacturer($facture_prolongation,$item,"prolongation",$id_facture);										
								
								if($contact){
									if($contact["email"]){
										$path=array("facture"=>"fichier_joint");
										$email["email"]=$contact["email"];
										$email["texte"]="Votre facture pour la période ".$facturation["date_periode_debut"]." - ".$facturation["date_periode_fin"];
			
										//ATF::affaire()->mailContact($email,$id_facture,"facture",$path);
										$data_fact_attente = array("mail"=> json_encode($email),
														   "id_facture"=> $id_facture,
														   "nom_table"=>"facture",
														   "path"=> json_encode($path),
														   "id_facturation"=> $id_facturation
														  );
										ATF::facturation_attente()->insert($data_fact_attente);

										$facturation["email"]=$contact["email"];
										$facturation["envoye"]="non";
										$facturer=$this->formateTabfacturer($facturer,$facturation,"client",false,"prolongation");
										$tab=$this->incrementeFacture($tab,"prolongation",true);
		
										$this->u(array("id_facturation"=>$id_facturation,"envoye"=>"non"));
									}else{
										$facturation["cause"]="an";
										$non_envoye=$this->formateTabfacturer($non_envoye,$facturation,"client_non_envoye",false,"prolongation");
										$tab=$this->incrementeFacture($tab,"prolongation",false);
									}
								}else{ 
									$facturation["cause"]="pc";
									$non_envoye=$this->formateTabfacturer($non_envoye,$facturation,"client_non_envoye",false,"prolongation");
									$tab=$this->incrementeFacture($tab,"prolongation",false);
								}
				 
								log::logger("Création facture contrat de l'affaire ".$affaire["ref"],__CLASS__);
							}else{
								log::logger("------------------------------------Facture à zéro ".$affaire["ref"],__CLASS__);
							}
						}
					}
				}
			//}
		}		
		
		log::logger("Commit de la transaction",__CLASS__);
		ATF::db($this->db)->commit_transaction();
			
		//Envoi du mail à Cléodis des facturations envoyées
		if($facturer){
			log::logger("Envoi du mail à Cléodis des facturations envoyées en fin de mois ...",__CLASS__);	
			$this->sendGrille($facturer,$tab["fc"],$tab["fp"],$date_debut,$date_fin,"grille_","Grille de facturation RESTITUTIONS des factures qui seront envoyées en fin de mois",$s);
		}
		
		//Envoi du mail à Cléodis des facturations non envoyées
		if($non_envoye){
			log::logger("Envoi du mail à Cléodis des facturations non envoyées...",__CLASS__);	
			$this->sendGrille($non_envoye,$tab["nfc"],$tab["nfp"],$date_debut,$date_fin,"grille_","Grille de facturation RESTITUTIONS des factures non envoyées",$s);
		}	

		//Envoi d'un pdf contenant toutes les factures contrat
		log::logger("Envoi d'un pdf contenant toutes les factures contrat...",__CLASS__);
		$this->sendFactures($date_debut,$date_fin,$facture_contrat,"global_","Factures RESTITUTIONS contrat",$s);

		//Envoi d'un pdf contenant toutes les factures prolongation
		log::logger("Envoi d'un pdf contenant toutes les factures prolongation...",__CLASS__);
		$this->sendFactures($date_debut,$date_fin,$facture_prolongation,"global_","Factures RESTITUTIONS prolongation",$s);
		
		$return["facturer"]=$facturer;
		$return["non_envoye"]=$non_envoye;
		$return["facture_contrat"]=$facture_contrat;
		$return["facture_prolongation"]=$facture_prolongation;
		$return["date_debut"]=$date_debut;
		$return["date_fin"]=$date_fin;
					
		log::logger("Batch terminé.",__CLASS__);	
		return $return;	
	
	}

	function formateTabfacturer($tab,$item,$prefix,$id_facture=false,$type=false){
		if($id_facture){
			$i=$id_facture;
		}else{
			$i=$item;
		}
		
		if($type){
			$prefix=$type.$prefix;
		}
		
		//PDF avec les memes facture mais pour des tris differents ensuite dans sendGrille		
		$tab[$prefix."Societe"][$item["ltrimsociete"].$item["id_facturation"]]=$i;
		$tab[$prefix."Code"][$item["ltrimcode_client"].$item["id_facturation"]]=$i;
		$tab[$prefix."Date"][$item["date_periode_debut"].$item["id_facturation"]]=$i;
		return $tab;
	}


	public function sendFactures($date_debut,$date_fin,$facture_contrat,$type,$texte,$s){
		//$emailGlobalFacture["email"]=ATF::societe()->select(246,"email");
		$emailGlobalFacture["email"] = ATF::user()->select(35, "email").",".ATF::user()->select(16, "email").",".ATF::user()->select(91, "email");
		$emailGlobalFacture["texte"]=$texte." pour la période du ".$date_debut."  au ".$date_fin.".";
		$emailGlobalFacture["objet"]=$texte." pour la période du ".$date_debut."  au ".$date_fin.".";
		foreach($facture_contrat as $key => $item){
			//Envoi d'un pdf contenant toutes les factures contrat
			//Tri des factures par rapport au code, Societe ou date
			ksort($item);
			ATF::facturation()->move_files($date_debut."_".$date_fin,$s,false,NULL,$type.$key,$item); // Génération du PDF avec les lignes dans la base
			$path[$key]=$type.$key;
		}
		ATF::affaire()->mailContact($emailGlobalFacture,$date_debut."_".$date_fin,"facturation",$path);
	}
	
	public function sendGrille($facturer,$fc,$fp,$date_debut,$date_fin,$type,$texte,$s){
		//$emailGrille["email"]=ATF::societe()->select(246,"email");
		$emailGrille["email"] = ATF::user()->select(35, "email").",".ATF::user()->select(16, "email").",".ATF::user()->select(91, "email");
		$emailGrille["texte"]=$texte." pour la période du ".$date_debut."  au ".$date_fin.".";
		$emailGrille["objet"]=$texte." pour la période du ".$date_debut."  au ".$date_fin.".";

		foreach($facturer as $key=>$item){
			//Tri des factures par rapport au code, Societe ou date
			ksort($item);
			$item["reserve"]['fc']=$fc;
			$item["reserve"]['fp']=$fp;
			$item["reserve"]['date_debut']=$date_debut;
			$item["reserve"]['date_fin']=$date_fin;
			ATF::facturation()->move_files($date_debut."_".$date_fin,$s,false,NULL,$type.$key,$item);			
			$path[$key]=$type.$key;
		}
		ATF::affaire()->mailContact($emailGrille,$date_debut."_".$date_fin,"facturation",$path);
		
	}


	/** 
	* Insertion de facture généré automatiquement lors de la facturation
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $affaire affaire que l'on facture	
	* @param array $facturation facturation que l'on utilise pour créer la facture
	* @param date $date_debut permet de forcer la date
	*/
	function insert_facture($affaire,$facturation,$date_debut=false){
		
		//Vérifier s'il n'y a pas déjà une facture sur cette période
		ATF::facture()->q->reset()
						 ->addCondition("type_facture","facture","AND")
						 ->addCondition("id_affaire",$affaire["id_affaire"],"AND")
						 ->addCondition("date_periode_debut",$facturation["date_periode_debut"],"AND",false,">=")
						 ->addCondition("date_periode_fin",$facturation["date_periode_fin"],"AND",false,"<=")
						 ->setCount();						 
		$count_facture=ATF::facture()->sa();

		if($count_facture["count"]<1){
		
			if(!$date_debut){
				$date_debut=date("Y-m-d",strtotime(date("Y-m-01")."+1 month"));
			}
			
			ATF::commande()->q->reset()
							->addCondition("id_affaire",$affaire["id_affaire"])
							->setDimension("row");
			$commande = ATF::commande()->sa();
						
			if($commande){
				if($facturation["montant"]+$facturation["assurance"]+$facturation["frais_de_gestion"]!="0"){
					$ref=ATF::facture()->getRef($affaire["id_affaire"],"facture");
					if($date_previsionnelle=ATF::affaire()->select($affaire['id_affaire'],"date_previsionnelle")){
						$day=$date_previsionnelle;
					}else{
						$day=0;
					}			
					
					
					//MAJ suite au passage de la TVA a 20% 
					$yearFacture = explode("-" , $date_debut);
					$tva = $commande["tva"];
					if($yearFacture[0] >= 2014 && $tva == "1.196"){
						$tva = 1.2;
					}
					
					//Insertion des factures
					$facture_date_previsionnelle=date('Y-m-d',strtotime($facturation["date_periode_debut"]."+".$day." day"));
					$facture=array(
						"id_societe"=>$commande["id_societe"],
						"ref"=>$ref,
						"prix"=>$facturation["montant"]+$facturation["assurance"]+$facturation["frais_de_gestion"],
						"etat"=>"impayee",
						"date"=>$date_debut,
						"date_periode_debut"=>$facturation["date_periode_debut"],
						"date_periode_fin"=>$facturation["date_periode_fin"],
						"tva"=> $tva,
						"id_user"=>$commande["id_user"],
						"id_commande"=>$commande["id_commande"],
						"mode_paiement"=>$commande["type"],
						"id_affaire"=>$affaire["id_affaire"],
						"date_previsionnelle"=>$facture_date_previsionnelle,
						"date_paiement"=>$facture_date_previsionnelle
					);
	
					$id_facture=ATF::facture()->i($facture);	
					
					//Insertion des lignes de factures
					ATF::commande_ligne()->q->reset()
											->addCondition("id_commande",$commande["id_commande"]);
					$commande_ligne=ATF::commande_ligne()->sa();
					foreach($commande_ligne as $k=>$i){
						$facture_ligne=array(
							"id_facture"=>$id_facture,
							"id_produit"=>$i["id_produit"],
							"ref"=>$i["ref"],
							"produit"=>$i["produit"],
							"quantite"=>$i["quantite"],
							"id_fournisseur"=>$i["id_fournisseur"],
							"prix_achat"=>$i["prix_achat"],
							"serial"=>$i["serial"],
							"code"=>$i["code"],
							"id_affaire_provenance"=>$i["id_affaire_provenance"],
							"visible"=>$i["visible"]
						);
						ATF::facture_ligne()->i($facture_ligne);
					}
					
					ATF::facture()->move_files($id_facture); // Génération du PDF avec les lignes dans la base
					
					$this->u(array("id_facturation"=>$facturation["id_facturation"],"id_facture"=>$id_facture));
					return $id_facture;
				}else{
					return "montant_zero";
				}
			}
		}
		return false;
	}

	/** 
	* Retourne la somme des factures restant à facturer par l'échéancier
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $id_affaire
	*/
	function getResteAPayer($id_affaire){
		$this->q->reset()->addField("SUM(montant)")->where("id_affaire",$id_affaire)->whereIsNull("id_facture");		
		return $this->select_cell();
	}

};
?>