<?
/** Classe facture_fournisseur
* @package Optima
* @subpackage LMA
*/
class facture_fournisseur extends classes_optima {
	function __construct($table_or_id=NULL) {
		$this->table = "facture_fournisseur";
		parent::__construct($table_or_id);

		$this->colonnes['fields_column'] = array(
			"facture_fournisseur.ref"
			,"facture_fournisseur.id_fournisseur"
			,"facture_fournisseur.prix"=>array("aggregate"=>array("min","avg","max","sum"),"renderer"=>"money")
			,"facture_fournisseur.prix_ht"=>array("aggregate"=>array("min","avg","max","sum"),"renderer"=>"money")
			,"facture_fournisseur.id_affaire"
			,"facture_fournisseur.id_bon_de_commande"
			,"facture_fournisseur.etat"=>array("renderer"=>"etat","width"=>30)
			,"facture_fournisseur.date_paiement"=>array("renderer"=>"updateDate","width"=>170)
			,'fichier_joint'=>array("custom"=>true,"nosort"=>true,"type"=>"file","align"=>"center","width"=>50)
			,'facture_fournisseur.bap'=>array("align"=>"center","renderer"=>"estBAP","width"=>50)
			,"facture_fournisseur.date_bap"
			,"facture_fournisseur.id_user"

		);

		$this->colonnes['primary'] = array(
			"ref"
			,"id_affaire"=>array("disabled"=>true)
			,"id_bon_de_commande"=>array("disabled"=>true)
			,"id_fournisseur"=>array("disabled"=>true)
			,"type"
			,"periodicite"
		);

		$this->colonnes['panel']['lignes'] = array(
			"produits"=>array("custom"=>true)
		);

		$this->colonnes['panel']['statut'] = array(
			"prix_ht"=>array("custom"=>true,"formatNumeric"=>true,"xtype"=>"textfield","null"=>true)
			,"etat"
			,"prix"=>array("custom"=>true,"formatNumeric"=>true,"xtype"=>"textfield","null"=>true)
			,"tva"=>array("readonly"=>true)
		);

		$this->colonnes['panel']['dates'] = array(
			"date"
			,"date_echeance"
		);


		// Propriété des panels
		$this->panels['lignes'] = array('nbCols'=>1,'visible'=>true);
		$this->panels['dates'] = array('nbCols'=>2,'visible'=>true);
		$this->panels['statut'] = array('nbCols'=>2,'visible'=>true);

		// Champs masqués
		$this->colonnes['bloquees']['insert'] =
		$this->colonnes['bloquees']['cloner'] =
		$this->colonnes['bloquees']['update'] = array("date_paiement","deja_exporte_immo","deja_exporte_achat");
		$this->fieldstructure();


		$this->addPrivilege("export_data");
		$this->addPrivilege("export_cegid");
		$this->addPrivilege("export_ap");
		$this->addPrivilege("export_bap");
		$this->addPrivilege("export_immo_adeo");
		$this->addPrivilege("estBAP");


		$this->field_nom = "ref";
		$this->foreign_key['id_fournisseur'] =  "societe";
		$this->onglets = array('facture_fournisseur_ligne','facture_non_parvenue');
		$this->files["fichier_joint"]  = array("type"=>"pdf","obligatoire"=>true);
		$this->can_insert_from = array("bon_de_commande");
		$this->no_insert = true;
		$this->selectAllExtjs=true;
	}

	/**
    * Retourne la valeur par défaut spécifique aux données des formulaires
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $field
	* @param array &$s La session
	* @param array &$request Paramètres disponibles (clés étrangères)
	* @return string
    */
	public function default_value($field,&$s,&$request){
		if ($id_bon_de_commande = ATF::_r('id_bon_de_commande')) {
			$bon_de_commande=ATF::bon_de_commande()->select($id_bon_de_commande);
		}

		switch ($field) {
			case "id_fournisseur":
				return $bon_de_commande['id_fournisseur'];
			case "id_affaire":
				return $bon_de_commande['id_affaire'];
			case "prix":

				ATF::bon_de_commande()->q->reset()
										 ->addCondition("bon_de_commande.id_bon_de_commande",$bon_de_commande['id_bon_de_commande'])
										 ->setDimension("row");

				$return = ATF::bon_de_commande()->select_all();

				return $return["solde_ht"];
			case "tva":
				return $bon_de_commande['tva'];
		}

		return parent::default_value($field,$s,$request);
	}

	/**
	* Permet de modifier la date sur un select_all
	* @param array $infos id_table, key (nom du champs à modifier),value (nom du champs à modifier)
	* @return boolean
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	*/
	public function updateDate($infos){

		if ($infos['value'] == "undefined") $infos["value"] = "";

		$infos["key"]=str_replace($this->table.".",NULL,$infos["key"]);
		$infosMaj["id_".$this->table]=$infos["id_".$this->table];
		$infosMaj[$infos["key"]]=$infos["value"];

		if($infosMaj[$infos["key"]]){
			$infosMaj["etat"]="payee";
		}else{
			$infosMaj["etat"]="impayee";
		}
		if($this->u($infosMaj)){
			ATF::$msg->addNotice(
				loc::mt(ATF::$usr->trans("notice_update_success_date"),array("record"=>$this->nom($infosMaj["id_".$this->table]),"date"=>$infos["key"]))
				,ATF::$usr->trans("notice_success_title")
			);

//			$id_affaire=$this->select($infosMaj["id_".$this->table],"id_affaire");
//			ATF::affaire()->redirection("select",$id_affaire);
			return true;
		}else{
			return false;
		}
	}


	/**
	* Insertion de la facture non parvenue négative dès l'insertion d'une facture fournisseur
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	*/
	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){

		$infos_ligne = json_decode($infos["values_".$this->table]["produits"],true);
		$this->infoCollapse($infos);
		$affaire=ATF::affaire()->select($infos["id_affaire"]);

//*****************************Transaction********************************
		ATF::db($this->db)->begin_transaction();

			$infos["tva"]=ATF::bon_de_commande()->select($infos["id_bon_de_commande"],"tva");
			$last_id=parent::insert($infos,$s,$files,$cadre_refreshed,$nolog);

			ATF::facture_non_parvenue()->q->reset()->where("id_affaire",$infos["id_affaire"])
												   ->where("id_bon_de_commande",$infos["id_bon_de_commande"]);
			$fnps = ATF::facture_non_parvenue()->select_all();

			$fnp = array(
				'ref'=>$infos['ref']."-FNP"
				,'id_facture_fournisseur'=>$last_id
				,'prix'=>-(str_replace(" ","",$infos['prix'])) // Valeur négative
				,'prix_ht'=>-(str_replace(" ","",$infos['prix_ht']))
				,'id_affaire'=>$infos["id_affaire"]
				,'tva'=>$infos["tva"]
				,'date'=>$infos["date"]
				,'id_bon_de_commande'=>$infos["id_bon_de_commande"]
			);

			if($fnps){
				$prix = $prix_ht = 0;
				foreach ($fnps as $key => $value) {
					$prix += $value["prix"];
					$prix_ht += $value["prix_ht"];
				}
				$prix = $prix - str_replace(" ","",$infos['prix']);
				$prix_ht = $prix_ht - str_replace(" ","",$infos['prix_ht']);

				if($prix == 0){
					foreach ($fnps as $key => $value) {
						ATF::facture_non_parvenue()->u(array("id_facture_non_parvenue"=> $value["id_facture_non_parvenue"],
														 	 "facturation_terminee"=>"oui"
														)
												  );
					}
					$fnp["facturation_terminee"] = "oui";
				}
			}
			ATF::facture_non_parvenue()->i($fnp);

			//Lignes
			if($infos_ligne){
				foreach($infos_ligne as $key=>$item){
					foreach($item as $k=>$i){
						$k_unescape=util::extJSUnescapeDot($k);
						$item[str_replace("facture_fournisseur_ligne.","",$k_unescape)]=$i;
						unset($item[$k]);
					}

					//Facture fournisseur
					$item["id_facture_fournisseur"]=$last_id;
					$item["id_bon_de_commande_ligne"]=$item["id_facture_fournisseur_ligne"];
					$item["index"]=util::extJSEscapeDot($key);
					$bon_de_commande_ligne=ATF::bon_de_commande_ligne()->select($item["id_bon_de_commande_ligne"]);
					$item["id_produit"]=ATF::commande_ligne()->select($bon_de_commande_ligne["id_commande_ligne"],"id_produit");
					unset($item["id_facture_fournisseur_ligne"]);
					$lastIdLigne=ATF::facture_fournisseur_ligne()->i($item,$s);
				}
			}else{
				ATF::db($this->db)->rollback_transaction();
				throw new errorATF("Facture fournisseur sans produits",877);
			}

		ATF::db($this->db)->commit_transaction();
//*****************************************************************************

		$class = ATF::getClass("facture_fournisseur");

		$id_pdf_affaire = ATF::pdf_affaire()->insert(array("id_affaire"=>$infos["id_affaire"], "provenance"=>ATF::$usr->trans($class->name(), "module")." ref : ".$infos['ref']));

		copy($this->filepath($last_id,"fichier_joint"), ATF::pdf_affaire()->filepath($id_pdf_affaire,"fichier_joint"));

		ATF::affaire()->redirection("select",$infos["id_affaire"]);

		return $last_id;
	}

	/**
	* Surcharge de delete afin de supprimer les parcs insérés
	* @author mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param int $infos le ou les identificateurs de l'élément que l'on désire inséré
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	*/
	public function delete($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL) {

		if (!is_array($infos)) {
			$id_affaire = $this->select($infos,"id_affaire");
		} elseif(!is_array($infos["id"])) {
			$id_affaire = $this->select($infos["id"],"id_affaire");
		}

		$return = parent::delete($infos,$s,$files,$cadre_refreshed);
		ATF::affaire()->redirection("select",$id_affaire);

		return $return;
	}


	/**
	* Impossible de supprimer une facture fournisseur payee
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param int $id
	* @return boolean
	*/
	public function can_delete($id){
		if($this->select($id,"etat")=="impayee"){
			return true;
		}else{
			throw new errorATF("Impossible de modifier/supprimer ce ".ATF::$usr->trans($this->table)." car elle est en '".ATF::$usr->trans("payee")."'",883);
			return false;
		}
	}

	/**
	* Impossible de modifier une facture fournisseur payee
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param int $id
	* @return boolean
	*/
	public function can_update($id,$infos=false){
		return $this->can_delete($id);
	}


	/**
	 * Permet de passer une facture fournisseur en BAP
	 * @param  array $infos donnée envoyée par bouton BAP du select_all
	 * @return boolean
	 */
	public function estBAP($infos){
		if($infos["id_facture_fournisseur"]){
			$id_affaire = $this->select($infos["id_facture_fournisseur"], "id_affaire");

			$this->u(array("id_facture_fournisseur"=>$this->decryptId($infos["id_facture_fournisseur"]),
						   "bap"=>"1",
						   "date_bap"=>date("Y-m-d"),
						   "id_user"=>ATF::$usr->get('id_user'),
						   "num_bap"=>ATF::affaire()->select($id_affaire , "ref")
						   )
					 ,$s);

			ATF::$msg->addNotice(
							loc::mt("Facture fournisseur passée en BAP",array("record"=>$this->nom($infos["id_facture_fournisseur"])))
							,ATF::$usr->trans("notice_success_title")
						);
			$this->redirection("select_all",NULL,"facture_fournisseur.html");
			return true;
		}
	}

//
//	/**
//	* Permet de savoir si toutes les lignes d'un bon de commande sont passées en facture fournisseur
//	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
//	* @param int $id
//	* @return boolean
//	*/
//	public function factureFournisseurByBdC($id_bon_de_commande){
//		ATF::bon_de_commande_ligne()->q->reset()->addCondition("id_bon_de_commande",$id_bon_de_commande)->setCount();
//		$bon_de_commande_ligne=ATF::bon_de_commande_ligne()->sa();
//		$nb_bon_de_commande_ligne=$bon_de_commande_ligne["count"];
//		$nb_facture_fournisseur_ligne=0;
//		foreach($bon_de_commande_ligne["data"] as $key=>$item){
//			if($item["quantite"]>1){
//				$nb_bon_de_commande_ligne+=($item["quantite"]-1);
//			}
//			ATF::facture_fournisseur_ligne()->q->reset()->addCondition("id_bon_de_commande_ligne",$item["id_bon_de_commande_ligne"]);
//			if($facture_fournisseur_ligne=ATF::facture_fournisseur_ligne()->sa()){
//				foreach($facture_fournisseur_ligne as $k=>$i){
//					$nb_facture_fournisseur_ligne++;
//				}
//			}
//		}
//
//		if($nb_facture_fournisseur_ligne==$nb_bon_de_commande_ligne){
//			return true;
//		}else{
//			return false;
//		}
//	}
//



	/** Permet l'export des facture_fournisseur
     * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
     * @param array $infos : contient le nom de l'onglet
     */
	 public function export_data($infos){
        if(!$infos["tu"]){ $this->q->reset(); }

        $this->setQuerier(ATF::_s("pager")->create($infos['onglet'])); // Recuperer le querier actuel

        $this->q->addAllFields($this->table)->setLimit(-1)->unsetCount();
        $this->q->where("facture_fournisseur.deja_exporte_achat","non");
        $infos = $this->sa();

		require_once __ABSOLUTE_PATH__."libs/ATF/libs/PHPExcel/Classes/PHPExcel.php";
		require_once __ABSOLUTE_PATH__."libs/ATF/libs/PHPExcel/Classes/PHPExcel/Writer/Excel5.php";
		$fname = tempnam(__TEMPORARY_PATH__, __TEMPLATE__.ATF::$usr->getID());
		$workbook = new PHPExcel;

		//premier onglet
		$worksheet_auto = new PHPEXCEL_ATF($workbook,0);
		$worksheet_auto->sheet->setTitle('facture_fournisseurs');
		$sheets=array("auto"=>$worksheet_auto);


		//mise en place des titres
		$this->ajoutTitre($sheets);

		//ajout des donnÃ©es
		$this->ajoutDonnees($sheets,$infos);


		$writer = new PHPExcel_Writer_Excel5($workbook);

		$writer->save($fname);
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition:inline;filename=export_facture_fournisseur.xls');
		header("Cache-Control: private");
		$fh=fopen($fname, "rb");
		fpassthru($fh);
		unlink($fname);
		PHPExcel_Calculation::getInstance()->__destruct();
    }



     /** Mise en place des titres
     * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	 * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
     * @param array $sheets : contient les 5 onglets
     */
    public function ajoutTitre(&$sheets){
        $row_data = array(
        	"A"=>'Type'
        	,"B"=>'Date'
			,"C"=>'Journal'
			,"D"=>'Général'
			,"E"=>'Auxiliaire'
			,"F"=>'Sens'
			,"G"=>'Montant'
			,"H"=>'Libellé'
			,"I"=>'Référence'
			,"J"=>'Section A1'
			,"K"=>'Date echeance'
			,'L'=>'Date paiement'
		);

         foreach($sheets as $nom=>$onglet){
             foreach($row_data as $col=>$titre){
				  $sheets[$nom]->write($col.'1',$titre);
				  $sheets[$nom]->sheet->getColumnDimension($col)->setAutoSize(true);
             }
         }
     }

     /** Mise en place du contenu
     * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	 * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
     * @param array $sheets : contient les 5 onglets
     * @param array $infos : contient tous les enregistrements
     */
    public function ajoutDonnees(&$sheets,$infos){
		$row_auto=1;
		$increment=0;
		foreach ($infos as $key => $item) {

			$this->u(array("id_facture_fournisseur"=> $item["facture_fournisseur.id_facture_fournisseur_fk"], "deja_exporte_achat"=>"oui"));

			$increment++;
			if($item){
				//initialisation des données
				$societe = ATF::societe()->select(ATF::affaire()->select($item["facture_fournisseur.id_affaire_fk"] , "id_societe"));

	 			$date=date("dmY",strtotime($item['facture_fournisseur.date']));
				$affaire=ATF::affaire()->select($item['facture_fournisseur.id_affaire_fk']);

				if($item["facture_fournisseur.type"] !== "maintenance"){
					$libelle = $item["facture_fournisseur.id_facture_fournisseur"]."-".$societe["code_client"] ."-".ATF::societe()->nom($societe["id_societe"]);
				}else{
					$libelle = $item["facture_fournisseur.id_facture_fournisseur"]."-".$societe["code_client"] ."-".ATF::societe()->nom($societe["id_societe"])."-".$item["periodicite"];
				}

				$date_echeance = "".date("d/m/y",strtotime($item['facture_fournisseur.date_echeance']));
				if(!$item['facture_fournisseur.date_paiement']){
					$date_paiement = "";
				}else{ $date_paiement = " ".date("d/m/y",strtotime($item['facture_fournisseur.date_paiement'])); }

				$cle = $key+1;
				if($cle <10){ $inc = "00".$cle; }
				elseif($cle <100){ $inc = "0".$cle;  } else{ $inc = $cle; }

				ATF::demande_refi()->q->reset()->where("demande_refi.id_affaire",$item['facture_fournisseur.id_affaire_fk'],false,"AND")
											   ->where("demande_refi.etat","valide");
				$refi = ATF::demande_refi()->select_row();

				ATF::commande()->q->reset()->where("commande.id_affaire" , $item['facture_fournisseur.id_affaire_fk']);
				$commande = ATF::commande()->select_row();

				$reference = "A".date("ym",strtotime($item['facture_fournisseur.date'])).$inc;



				//insertion des donnÃ©es
				for ($i = 1; $i <= 4; $i++) {
					$row_data=array();
					if($i==1){
						$row_data["A"]='G';
						$row_data["B"]="‘".$date;
						$row_data["C"]='AC';
						$row_data["D"]="401000";
						$row_data["E"]=ATF::societe()->select($item["facture_fournisseur.id_fournisseur_fk"], "code_fournisseur");
						$row_data["F"]='C';

						$row_data["G"]=round(abs($item['facture_fournisseur.prix']*$item["facture_fournisseur.tva"]),2);
						$row_data["H"]=$libelle;
						$row_data["I"]=$reference;
						$row_data["J"]="";
						$row_data["K"]=$date_echeance;
						$row_data["L"]="‘".$date_paiement;
					}elseif($i==2){
						$row_data["A"]='G';
						$row_data["B"]="‘".$date;
						$row_data["C"]='AC';
						$row_data["F"]='D';

						if($refi && $refi["id_refinanceur"] == 4){
							if($commande["etat"] == "non_loyer"){ $row_data["D"]="231800"; }
							else{ $row_data["D"]="218310"; }
						}else{
							if($commande["etat"] == "non_loyer"){ $row_data["D"]="231800"; }
							else{
								if($item["facture_fournisseur.type"] == "cout_copie"){ $row_data["D"]="611100";  }
								elseif($item["facture_fournisseur.type"] == "maintenance") { $row_data["D"]="611200"; }
								else{ $row_data["D"]="607110"; }
							}
						}

						$row_data["G"]=round(abs($item['facture_fournisseur.prix']),2);

						$row_data["H"]=$libelle;
						$row_data["I"]=$reference;
						$row_data["J"]="";
						$row_data["K"]="";
						$row_data["L"]="‘".$date_paiement;
					}elseif($i==3 && ($refi["id_refinanceur"] != 4)){
						$row_data["A"]='A1';
						$row_data["B"]="‘".$date;
						$row_data["C"]='AC';
						$row_data["F"]='D';

						if($refi && $refi["id_refinanceur"] == 4){
							if($commande["etat"] == "non_loyer"){ $row_data["D"]="231800"; }
							else{ $row_data["D"]="218310"; }
						}else{
							if($commande["etat"] == "non_loyer"){ $row_data["D"]="231800"; }
							else{
								if($item["facture_fournisseur.type"] == "cout_copie"){ $row_data["D"]="611100";  }
								elseif($item["facture_fournisseur.type"] == "maintenance") { $row_data["D"]="611200"; }
								else{ $row_data["D"]="607110"; }
							}
						}
						$row_data["G"]=round(abs($item['facture_fournisseur.prix']),2);

						$row_data["H"]=$libelle;
						$row_data["I"]=$reference;
						if($affaire["nature"]=="avenant"){
							//Faire en sorte que l1296 = 2008 et non pas 208
							$row_data["J"]="20".substr($affaire["ref"],0,7).$societe["code_client"]."AV";
						}else{
							$row_data["J"]="20".substr($affaire["ref"],0,7).$societe["code_client"]."00";
						}
						$row_data["K"]="";
						$row_data["L"]="‘".$date_paiement;

					}elseif($i==4){

						$row_data["A"]='G';
						$row_data["B"]="‘".$date;
						$row_data["C"]='AC';
						if($refi && $refi["id_refinanceur"] == 4){	$row_data["D"]="445620"; }
						else{	$row_data["D"]="445660"; }
						$row_data["E"]='';
						$row_data["F"]='D';
						$row_data["G"]=round(abs(($item['facture_fournisseur.prix']*$item['facture_fournisseur.tva'])-$item['facture_fournisseur.prix']),2);
						$row_data["H"]=$libelle;
						$row_data["I"]=$reference;
						$row_data["J"]="";
						$row_data["K"]="";
						$row_data["L"]="‘".$date_paiement;
					}


					if($row_data){
						$row_auto++;
						foreach($row_data as $col=>$valeur){
							$sheets['auto']->write($col.$row_auto, $valeur);
						}
					}
				}
			}
		}
	}

	public function initStyle(){

		$style_titre1 = new excel_style();
		$style_titre1->setWrap()->alignement('center')->setSize(13)->setBorder("thin")->setBold();
		$this->setStyle("titre1",$style_titre1->getStyle());
		/*-------------------------------------------*/
		$style_titre1_right = new excel_style();
		$style_titre1_right->setWrap()->alignement("center","right")->setSize(13)->setBorder("thin")->setBold();
		$this->setStyle("titre1_right",$style_titre1_right->getStyle());
		/*-------------------------------------------*/
		$style_titre1_left = new excel_style();
		$style_titre1_left->setWrap()->alignement("center", "left")->setSize(13)->setBorder("thin")->setBold();
		$this->setStyle("titre1_left",$style_titre1_left->getStyle());
		/*-------------------------------------------*/
		$style_titre2 = new excel_style();
		$style_titre2->setWrap()->alignement('center')->setSize(11)->setBorder("thin");
		$this->setStyle("titre2",$style_titre2->getStyle());
		/*-------------------------------------------*/
		$style_titre2_right = new excel_style();
		$style_titre2_right->setWrap()->alignement("center","right")->setSize(11)->setBorder("thin");
		$this->setStyle("titre2_right",$style_titre2_right->getStyle());
		/*-------------------------------------------*/
		$style_centre = new excel_style();
		$style_centre->alignement();
		$this->setStyle("centre",$style_centre->getStyle());
		/*-------------------------------------------*/
		$style_cel_c = new excel_style();
		$style_cel_c->setWrap()->alignement('center')->setSize(11)->setBorder("thin");
		$this->setStyle("border_cel",$style_cel_c->getStyle());
		/*-------------------------------------------*/
		$style_border_cel_right = new excel_style();
		$style_border_cel_right->setWrap()->alignement("center","right")->setSize(11)->setBorder("thin");
		$this->setStyle("border_cel_right",$style_border_cel_right->getStyle());
		/*-------------------------------------------*/
		$style_border_cel_left = new excel_style();
		$style_border_cel_left->setWrap()->alignement("center","left")->setSize(11)->setBorder("thin");
		$this->setStyle("border_cel_left",$style_border_cel_left->getStyle());
		/*-------------------------------------------*/
		$style_cel_right = new excel_style();
		$style_cel_right->setWrap()->alignement("center","right")->setSize(11);
		$this->setStyle("cel_right",$style_cel_right->getStyle());
	}

	public function setStyle($nom,$objet){
		$this->style[$nom]=$objet;
	}

	public function getStyle($nom){
		return $this->style[$nom];
	}


	/** Export CEGID
	 * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
     * @param array $infos : contient le nom de l'onglet
     */
	public function export_cegid($infos){
		if(!$infos["tu"]){ $this->q->reset(); }

        $this->setQuerier(ATF::_s("pager")->create($infos['onglet'])); // Recuperer le querier actuel

        $force = false;
        $this->q->addAllFields($this->table)->setLimit(-1)->unsetCount();
        if(!$infos["force"]){ $this->q->where("facture_fournisseur.deja_exporte_immo","non"); $force=true; }
        $infos = $this->sa();

		$this->export_xls_cegid($infos,$force);
    }


    /** Surcharge pour avoir un export
     * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
     * @param array $infos : contient tous les enregistrements
     */
    public function export_xls_cegid(&$infos,$force){
		require_once __ABSOLUTE_PATH__."libs/ATF/libs/PHPExcel/Classes/PHPExcel.php";
		require_once __ABSOLUTE_PATH__."libs/ATF/libs/PHPExcel/Classes/PHPExcel/Writer/Excel5.php";
		$fname = tempnam(__TEMPORARY_PATH__, __TEMPLATE__.ATF::$usr->getID());
		$workbook = new PHPExcel;

		//premier onglet
		$worksheet_auto = new PHPEXCEL_ATF($workbook,0);
		$worksheet_auto->sheet->setTitle('EXPORT CEGID');
		$sheets=array("auto"=>$worksheet_auto);
		$this->initStyle();

		//mise en place des titres
		$row_data = array(
        	 "A"=>array('Compte',40)
        	,"B"=>array('Date entrée',40)
			,"C"=>array('Date de mise en service',40)
			,"D"=>array('Date début amortissement comptable',40)
			,"E"=>array('Date début amortissement fiscal',40)
			,"F"=>array('Réference',40)
			,"G"=>array('Libellé',40)
			,"H"=>array('Prix unitaire',40)
			,"I"=>array('Montant HT',40)
			,"J"=>array('Quantité',40)
			,"K"=>array('Montant TVA',40)
			,"L"=>array('Taux TVA',40)
			,"M"=>array('Prorata TVA',40)
			,"N"=>array('Montant TTC',40)
			,"O"=>array('Type sortie',40)
			,"P"=>array('Date sortie',40)
			,"Q"=>array('Base comptable',40)
			,"R"=>array('Méthode comptable',40)
			,"S"=>array('Durée comptable',40)
			,"T"=>array('Base fiscale',40)
			,"U"=>array('Méthode fiscale',40)
			,"V"=>array('Durée fiscale',40)
			,"W"=>array('Nature bien',40)
			,"X"=>array('Type entrée',40)
			,"Y"=>array('Niveau réalité',40)
			,"Z"=>array('Total cumulantérieur comptable',40)
			,"AA"=>array('Total cumulantérieur fiscal',40)
			,"AB"=>array('Critère 1',40)
			,"AC"=>array('Réference 2',40)
			,"AD"=>array('Compte fournisseur',40)
		);



        foreach($sheets as $nom=>$onglet){
            foreach($row_data as $col=>$titre){
				$sheets[$nom]->write($col.'1',$titre[0],$this->getStyle("titre1"));
				$sheets[$nom]->sheet->getColumnDimension($col)->setWidth($titre[1]);
            }
        }


		//ajout des donnÃ©es
		if($infos){
			$row_auto=1;

			foreach ($infos as $key => $value) {
				if($force){ $this->u(array("id_facture_fournisseur"=> $value["facture_fournisseur.id_facture_fournisseur_fk"], "deja_exporte_immo"=>"oui")); }

				ATF::facture()->q->reset()->addCondition("type_facture","refi")
									  ->addCondition("id_affaire",$value["facture_fournisseur.id_affaire_fk"]);
				$facture_refi = ATF::facture()->sa();

				//Il faut aussi vérifier que l'affaire ne va pas être céder
				$demande_refi=ATF::demande_refi()->existDemandeRefi($value["facture_fournisseur.id_affaire_fk"]);

				if(!$facture_refi || ATF::refinanceur()->select($facture_refi[0]["id_refinanceur"],"code_refi")=="REFACTURATION"){

					$facture  = ATF::facture_fournisseur()->select($value["facture_fournisseur.id_facture_fournisseur_fk"]);
					$affaire  = ATF::affaire()->select($value["facture_fournisseur.id_affaire_fk"]);
					ATF::commande()->q->reset()->where("commande.id_affaire", $value["facture_fournisseur.id_affaire_fk"]);
					$commande = ATF::commande()->select_row();

					$societe  = ATF::societe()->select($affaire["id_societe"]);
					$fournisseur = ATF::societe()->select($value["facture_fournisseur.id_fournisseur_fk"], "societe");

					$date_mise_service = "";
					if($commande["commande.date_debut"]){ $date_mise_service = date("d/m/Y", strtotime($commande["commande.date_debut"])); }

					$duree = 0;

					ATF::loyer()->q->reset()->where("loyer.id_affaire", $value["facture_fournisseur.id_affaire_fk"]);
					$loyers = ATF::loyer()->select_all();

					foreach ($loyers as $k => $v) {
						if($v["frequence_loyer"] == "an"){ $duree = $duree + $v["duree"];
						}elseif($v["frequence_loyer"] == "trimestre"){ $duree = $duree + ($v["duree"]/4);
						}elseif($v["frequence_loyer"] == "semestre"){ $duree = $duree + ($v["duree"]/3);
						}else{ $duree = $duree + ($v["duree"]/12);		}
					}

					if($commande["commande.date_debut"] && $commande["commande.date_evolution"]){
						$datetime1 = new DateTime($commande["commande.date_debut"]);
						$datetime2 = new DateTime($commande["commande.date_evolution"]);
						$duree = $datetime1->diff($datetime2);
						$duree = number_format( (intval($duree->format('%a')) / 365) , 3);
					}

					$critere1 = '20'.substr($affaire["ref"],0,7).$societe["code_client"];
					if(strpos($affaire["ref"], "AVT")){ $critere1 .= "AV"; }
					else{ $critere1 .= "00"; }

		        	$row_data = array(
						        	 "A"=>array('218310')
						        	,"B"=>array(date("d/m/Y", strtotime($facture["date"])))
									,"C"=>array($date_mise_service)
									,"D"=>array($date_mise_service)
									,"E"=>array($date_mise_service)
									,"F"=>array('')
									,"G"=>array($societe["societe"]." ".$affaire["ref"]."-".$societe["code_client"])
									,"H"=>array('')
									,"I"=>array($facture["prix_ht"])
									,"J"=>array('1')
									,"K"=>array($facture["prix"]-$facture["prix_ht"])
									,"L"=>array(abs(($facture['tva']-1)*100),2,'.',' ')
									,"M"=>array('100')
									,"N"=>array($facture["prix"])
									,"O"=>array('00')
									,"P"=>array('30/12/2099')
									,"Q"=>array($facture["prix_ht"])
									,"R"=>array('01')
									,"S"=>array(number_format($duree, 2))
									,"T"=>array($facture["prix_ht"])
									,"U"=>array('01')
									,"V"=>array(number_format($duree, 2))
									,"W"=>array('01')
									,"X"=>array('01')
									,"Y"=>array('09')
									,"Z"=>array('09')
									,"AA"=>array('0')
									,"AB"=>array($critere1)
									,"AC"=>array($facture["ref"])
									,"AD"=>array($fournisseur)
								);
					$row_auto++;
					foreach($row_data as $col=>$valeur){
						$sheets['auto']->write($col.$row_auto, $valeur[0],$this->getStyle("centre"));
					}
				}
			}
	    }

		$writer = new PHPExcel_Writer_Excel5($workbook);

		$writer->save($fname);
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition:inline;filename=export_CEGID.xls');


		header("Cache-Control: private");
		$fh=fopen($fname, "rb");
		fpassthru($fh);
		unlink($fname);
		PHPExcel_Calculation::getInstance()->__destruct();
	}

	 /** Export des factures fournisseurs pour ADEO Services
     * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
     * @date 28/06/2016
     * @param array $infos : contient tous les enregistrements
     */
	public function export_ap(&$infos,$invoice=null){

		if($infos['onglet']){
			$infos["display"] = true;
			$this->setQuerier(ATF::_s("pager")->create($infos['onglet'])); // Recuperer le querier actuel

	        $this->q->addAllFields($this->table)->setLimit(-1)->unsetCount();
	        $data = $this->sa();
		}else{
			$data = $invoice;
			foreach ($data as $key => $value) {
				$data[$key]["facture_fournisseur.id_facture_fournisseur"] = $value["facture_fournisseur.ref"];
			}
		}


        $string = "";
        $total_debit = 0;
        $total_credit = 0;
        $lignes = 0;

        $donnees = array();

        foreach ($data as $key => $value) {
        	$code_magasin = "380"; //Par defaut web

        	$compteTVA = $compteTTC = $compteHT  = $magasin;

        	$ref_cmd_lm = ATF::affaire()->select($value["facture_fournisseur.id_affaire_fk"] , "ref_commande_lm");

        	if($value["facture_fournisseur.id_fournisseur_fk"] == 2){
        		$code_magasin = ATF::magasin()->select(ATF::bon_de_commande()->select($value["facture_fournisseur.id_bon_de_commande_fk"] , "id_magasin"), "num_magasin_lm");
        	}

        	ATF::devis()->q->reset()->where("id_affaire",$value["facture_fournisseur.id_affaire_fk"])->setLimit(1);
        	$devis = ATF::devis()->select_row();

        	//On recupere le 1er produit de la facture pour connaitre le pack et donc le rayon
        	ATF::devis_ligne()->q->reset()->where("id_devis",$devis["id_devis"])
        									->setLimit(1);
        	$ligne = ATF::devis_ligne()->select_row();

        	$pack = ATF::pack_produit()->select(ATF::produit()->select($ligne["id_produit"] , "id_pack_produit"));


        	if($value["facture_fournisseur.type"] == "achat"){
        		$compteComptable = "2156LET";
        	}elseif($value["facture_fournisseur.type"] == "maintenance"){
				$compteComptable = "615610";
        	}elseif($value["facture_fournisseur.type"] == "diagnostique"){
				$compteComptable = "604112";
        	}elseif($value["facture_fournisseur.type"] == "installation"){
				$compteComptable = "604100";
        	}

        	ATF::facture_fournisseur_ligne()->q->reset()->where("id_facture_fournisseur",$value["facture_fournisseur.id_facture_fournisseur_fk"]);
			$lignes_ff = ATF::facture_fournisseur_ligne()->select_all();

			$this->u(
					array("id_facture_fournisseur"=>$value["facture_fournisseur.id_facture_fournisseur_fk"] ,
						  "DATE_EXPORT_FACT"=>date("Y-m-d")
					)
				);

        	$rayon = ATF::rayon()->select($pack["id_rayon"] , "centre_cout_profit");


        	$recap_produit = array();
        	$TTC_lignes = 0;

			foreach ($lignes_ff as $k => $v) {
					//Ajouter le check sur le compte comptabke ICI !!
				if(!$recap_produit[$v["id_produit"]]) $recap_produit[$v["id_produit"]] = $v;
				else{
					$recap_produit[$v["id_produit"]]["prix"] += $v["prix"];
					$recap_produit[$v["id_produit"]]["prix_ttc"] += $v["prix_ttc"];
					$recap_produit[$v["id_produit"]]["quantite"] += $v["quantite"];
				}
			}

			foreach ($recap_produit as $kl => $vl) {
				if($vl["prix"] > 0){
					$TTC_lignes += $vl["prix_ttc"];
				}
			}

        	//TTC
    		$donnees[$key][1][1] = "1";
        	$donnees[$key][1][2] = "e"; //Type d'evenement e/a
        	$donnees[$key][1][3] = "120"; //Code BU
        	$donnees[$key][1][4] = "54";
        	$donnees[$key][1][5] = "1"; //Code pays (centrale)
        	$donnees[$key][1][6] = $code_magasin;
        	$donnees[$key][1][7] = ($value["facture_fournisseur.prix"] >= 0)?"F":"A"; //Type de facture F/A
        	$donnees[$key][1][8] =  $value["facture_fournisseur.id_facture_fournisseur"];
        	$donnees[$key][1][9] =  date("Ymd", strtotime($value["facture_fournisseur.date_echeance"]));
        	$donnees[$key][1][10] = date("Ymd", strtotime($value["facture_fournisseur.date"]));
        	$donnees[$key][1][11] = date("Ymd", strtotime($value["facture_fournisseur.date"]));
        	$donnees[$key][1][12] = number_format($TTC_lignes,2,".",""); //Montant TTC separateur numeric .
        	$donnees[$key][1][13] = "EUR";
    		$donnees[$key][1][14] = date("Ymd", strtotime($value["facture_fournisseur.date_echeance"]));
    		$donnees[$key][1][15] = "";
        	$donnees[$key][1][16] = ""; //$value["facture_fournisseur.bap"]; //Numéro de BAP Dans le fichier FACTURES : NULL Dans le fichier BAP : un numéro de BAP
        	$donnees[$key][1][17] =  ""; //date("Ymd", strtotime($value["facture_fournisseur.date"])); //Date de BAP de la pièce Dans le fichier FACTURES : NULL Dans le fichier BAP : La date de passage BAP
        	$donnees[$key][1][18] = ATF::societe()->select($value["facture_fournisseur.id_fournisseur_fk"] ,"numero_site"); //Numéro du site fournisseur (FGX .. Ou IMO…)
        	$donnees[$key][1][19] = $code_magasin;
			$donnees[$key][1][20] = "1";
			$donnees[$key][1][21] = "001";
        	$donnees[$key][1][22] = "1";
			$donnees[$key][1][23] = "";
			$donnees[$key][1][24] = "";
			$donnees[$key][1][25] = $compteComptable;
			$donnees[$key][1][26] = "";
			$donnees[$key][1][27] = $ref_cmd_lm;  //N° Commande site LM
			$donnees[$key][1][28] = ATF::affaire()->select($value["facture_fournisseur.id_affaire_fk"] , "ref")." / ".ATF::affaire()->select($value["facture_fournisseur.id_affaire_fk"] , "affaire"); //Numéro du contrat / affaire
			$description = ATF::affaire()->select($value["facture_fournisseur.id_affaire_fk"] , "affaire");
			if (strlen($description) > 200){
				$donnees[$key][1][29] = substr($description, 0, strrpos(substr($description, 0, 200), ''));
			}else{
				$donnees[$key][1][29] = $description;
			}

			$donnees[$key][1][30] = "";
			$donnees[$key][1][31] = "";

			$total_debit += number_format(($TTC_lignes),2,".","");



			$recap_produit = array();

			foreach ($lignes_ff as $k => $v) {
				if(!$recap_produit[$v["id_produit"]]) $recap_produit[$v["id_produit"]] = $v;
				else {
					$recap_produit[$v["id_produit"]]["prix"] += $v["prix"];
					$recap_produit[$v["id_produit"]]["prix_ttc"] += $v["prix_ttc"];
					$recap_produit[$v["id_produit"]]["quantite"] += $v["quantite"];
				}
			}

			$i = 2;
			foreach ($recap_produit as $kl => $vl) {
				if($vl["prix"] > 0){
					$TTC_ligne = $vl["prix_ttc"];
					$HT_ligne = round($vl["prix"] ,2);
					$total_credit = $TTC_ligne;

					//HT
	        		$donnees[$key][$i][1] = "2";
		        	$donnees[$key][$i][2] = "1"; //TVA =0 / HT=1
		        	$donnees[$key][$i][3] = $rayon;
		        	$donnees[$key][$i][4] =  $HT_ligne; // Montant
		        	if($value["facture_fournisseur.type"] == "achat"){
		        		$donnees[$key][$i][5] = "D20 Immo"; //Code TVA
		       	 	}else{
		       	 		$donnees[$key][$i][5] = ""; //Code TVA
		       	 	}
		        	$donnees[$key][$i][6] = "0";
		        	$donnees[$key][$i][7] = "";
		        	$donnees[$key][$i][8] = "";
		        	$donnees[$key][$i][9] = ""; //Type de facture
		        	$donnees[$key][$i][10] = "";

					//$total_credit += $vl["prix"];

					$i++;

					//TVA
	        		$donnees[$key][$i][1] = "2";
		        	$donnees[$key][$i][2] = "0"; //TVA =0 / HT=1
		        	$donnees[$key][$i][3] = "0";
		        	$donnees[$key][$i][4] = number_format(($TTC_ligne - $HT_ligne) ,2,".",""); // Montant
		        	if($value["facture_fournisseur.type"] == "achat"){
		        		$donnees[$key][$i][5] = "D20 Immo"; //Code TVA
		       	 	}else{
		       	 		$donnees[$key][$i][5] = ""; //Code TVA
		       	 	}
		        	$donnees[$key][$i][6] = ($value["facture_fournisseur.tva"]-1)*100;
		        	$donnees[$key][$i][7] = "";
		        	$donnees[$key][$i][8] = "" ;
		        	$donnees[$key][$i][9] = ""; //Type de facture
		        	$donnees[$key][$i][10] = "";

					//$total_credit += round(($vl["prix"]*$value["facture_fournisseur.tva"])-$vl["prix"] ,2);

					$i++;
				}
			}
        }

       	$sequence = ATF::constante()->getSequence("__SEQUENCE_AP__");

        $filename = 'CLEODIS_AP'.$sequence.'.fic';

        header('Content-Type: application/fic');
		header('Content-Disposition: attachment; filename="'.$filename.'"');


		foreach ($donnees as $key => $value) {
			foreach ($value as $k => $v) {
				if($v[1] == "1"){
					for($i=1;$i<=31;$i++){
						if(isset($v[$i])){
							$string .= $v[$i];
							if($i!=31) $string .= ";";
						}else{
							$string .= "";
							if($i!=31) $string .= ";";
						}
					}
				}elseif($v[1] == "2"){
					for($i=1;$i<=10;$i++){
						if(isset($v[$i])){
							$string .= $v[$i];
							if($i!=10) $string .= ";";
						}else{
							$string .= "";
							if($i!=10) $string .= ";";
						}
					}
				}


				$string .= "\n";
				$lignes ++;
			}
		}

        $string .=  "98;".count($donnees).";CLEODIS\n";
        $lignes ++;
        $string .= "99;".number_format($total_debit,2,".","").";EUR\n";
       	$lignes ++;
        $string .= "0;".$lignes.";".date("Ymd");




		if($infos['onglet']){
        	header('Content-Type: application/fic');
			header('Content-Disposition: attachment; filename="'.$filename.'"');

	        echo $string;
        }else{
        	return array("filename"=>$filename, "content"=>$string);
        }
    }



    /** Export des factures fournisseurs pour ADEO Services
     * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
     * @date 28/06/2016
     * @param array $infos : contient tous les enregistrements
     */
	public function export_bap(&$infos,$invoice=null){

		if($infos['onglet']){
			$infos["display"] = true;
			$this->setQuerier(ATF::_s("pager")->create($infos['onglet'])); // Recuperer le querier actuel

	        $this->q->addAllFields($this->table)->setLimit(-1)->unsetCount();
	        $data = $this->sa();

		}else{
			$data = $invoice;
			foreach ($data as $key => $value) {
				$data[$key]["facture_fournisseur.id_facture_fournisseur"] = $value["facture_fournisseur.ref"];
			}

		}

        $string = "";
        $total_debit = 0;
        $total_credit = 0;
        $lignes = 0;

        $donnees = array();

        foreach ($data as $key => $value) {
        	$code_magasin = "380"; //Par defaut web

        	$compteTVA = $compteTTC = $compteHT  = $magasin;

        	$ref_cmd_lm = ATF::affaire()->select($value["facture_fournisseur.id_affaire_fk"] , "ref_commande_lm");

        	if($value["facture_fournisseur.id_fournisseur_fk"] == 2){
        		$code_magasin = ATF::magasin()->select(ATF::bon_de_commande()->select($value["facture_fournisseur.id_bon_de_commande_fk"] , "id_magasin"), "num_magasin_lm");
        	}

        	ATF::devis()->q->reset()->where("id_affaire",$value["facture_fournisseur.id_affaire_fk"])->setLimit(1);
        	$devis = ATF::devis()->select_row();

        	//On recupere le 1er produit de la facture pour connaitre le pack et donc le rayon
        	ATF::devis_ligne()->q->reset()->where("id_devis",$devis["id_devis"])
        									->setLimit(1);
        	$ligne = ATF::devis_ligne()->select_row();

        	$pack = ATF::pack_produit()->select(ATF::produit()->select($ligne["id_produit"] , "id_pack_produit"));


        	if($value["facture_fournisseur.type"] == "achat"){
        		$compteComptable = "2156LET";
        	}elseif($value["facture_fournisseur.type"] == "maintenance"){
				$compteComptable = "615610";
        	}elseif($value["facture_fournisseur.type"] == "diagnostique"){
				$compteComptable = "604112";
        	}elseif($value["facture_fournisseur.type"] == "installation"){
				$compteComptable = "604100";
        	}

        	ATF::facture_fournisseur_ligne()->q->reset()->where("id_facture_fournisseur",$value["facture_fournisseur.id_facture_fournisseur_fk"]);
			$lignes_ff = ATF::facture_fournisseur_ligne()->select_all();

        	$rayon = ATF::rayon()->select($pack["id_rayon"] , "centre_cout_profit");


        	$this->u(
        			array("id_facture_fournisseur"=>$value["facture_fournisseur.id_facture_fournisseur_fk"] ,
        				"DATE_EXPORT_BAP"=>date("Y-m-d")
        			)
        	);

        	$recap_produit = array();
        	$TTC_lignes = 0;

			foreach ($lignes_ff as $k => $v) {
				if(!$recap_produit[$v["id_produit"]]) $recap_produit[$v["id_produit"]] = $v;
				else {
					$recap_produit[$v["id_produit"]]["prix"] += $v["prix"];
					$recap_produit[$v["id_produit"]]["prix_ttc"] += $v["prix_ttc"];
					$recap_produit[$v["id_produit"]]["quantite"] += $v["quantite"];
				}
			}

			foreach ($recap_produit as $kl => $vl) {
				if($vl["prix"] > 0){
					$TTC_lignes += $vl["prix_ttc"];
				}
			}

        	//TTC
    		$donnees[$key][1][1] = "1";
        	$donnees[$key][1][2] = "e"; //Type d'evenement e/a
        	$donnees[$key][1][3] = "120"; //Code BU
        	$donnees[$key][1][4] = "54";
        	$donnees[$key][1][5] = "1"; //Code pays (centrale)
        	$donnees[$key][1][6] = $code_magasin;
        	$donnees[$key][1][7] = ($value["facture_fournisseur.prix"] >= 0)?"F":"A"; //Type de facture F/A
        	$donnees[$key][1][8] =  $value["facture_fournisseur.id_facture_fournisseur"];
        	$donnees[$key][1][9] =  date("Ymd", strtotime($value["facture_fournisseur.date_echeance"]));
        	$donnees[$key][1][10] = date("Ymd", strtotime($value["facture_fournisseur.date"]));
        	$donnees[$key][1][11] = date("Ymd", strtotime($value["facture_fournisseur.date"]));
        	$donnees[$key][1][12] = number_format($TTC_lignes,2,".",""); //Montant TTC separateur numeric .
        	$donnees[$key][1][13] = "EUR";
    		$donnees[$key][1][14] = date("Ymd", strtotime($value["facture_fournisseur.date_echeance"]));
    		$donnees[$key][1][15] = "";
        	$donnees[$key][1][16] = $value["facture_fournisseur.num_bap"]; //Numéro de BAP Dans le fichier FACTURES : NULL Dans le fichier BAP : un numéro de BAP
        	$donnees[$key][1][17] = date("Ymd", strtotime($value["facture_fournisseur.date_bap"])); //Date de BAP de la pièce Dans le fichier FACTURES : NULL Dans le fichier BAP : La date de passage BAP
        	$donnees[$key][1][18] = ATF::societe()->select($value["facture_fournisseur.id_fournisseur_fk"] ,"numero_site"); //Numéro du site fournisseur (FGX .. Ou IMO…)
        	$donnees[$key][1][19] = $code_magasin;
			$donnees[$key][1][20] = "1";
			$donnees[$key][1][21] = "001";
        	$donnees[$key][1][22] = "1";
			$donnees[$key][1][23] = "";
			$donnees[$key][1][24] = "";
			$donnees[$key][1][25] = $compteComptable;
			$donnees[$key][1][26] = "";
			$donnees[$key][1][27] = $ref_cmd_lm;  //N° Commande site LM
			$donnees[$key][1][28] = ATF::affaire()->select($value["facture_fournisseur.id_affaire_fk"] , "ref")." / ".ATF::affaire()->select($value["facture_fournisseur.id_affaire_fk"] , "affaire"); //Numéro du contrat / affaire
			$description = ATF::affaire()->select($value["facture_fournisseur.id_affaire_fk"] , "affaire");
			if (strlen($description) > 200){
				$donnees[$key][1][29] = substr($description, 0, strrpos(substr($description, 0, 200), ''));
			}else{
				$donnees[$key][1][29] = $description;
			}

			$donnees[$key][1][30] = "";
			$donnees[$key][1][31] = "";

			$total_debit += number_format(($TTC_lignes),2,".","");



			$recap_produit = array();

			foreach ($lignes_ff as $k => $v) {
				if(!$recap_produit[$v["id_produit"]]) $recap_produit[$v["id_produit"]] = $v;
				else {
					$recap_produit[$v["id_produit"]]["prix"] += $v["prix"];
					$recap_produit[$v["id_produit"]]["prix_ttc"] += $v["prix_ttc"];
					$recap_produit[$v["id_produit"]]["quantite"] += $v["quantite"];
				}
			}
        }

       	$sequence = ATF::constante()->getSequence("__SEQUENCE_BAP__");

        $filename = 'CLEODIS_APBAP'.$sequence.'.fic';

        header('Content-Type: application/fic');
		header('Content-Disposition: attachment; filename="'.$filename.'"');


		foreach ($donnees as $key => $value) {
			foreach ($value as $k => $v) {
				if($v[1] == "1"){
					for($i=1;$i<=31;$i++){
						if(isset($v[$i])){
							$string .= $v[$i];
							if($i!=31) $string .= ";";
						}else{
							$string .= "";
							if($i!=31) $string .= ";";
						}
					}
				}elseif($v[1] == "2"){
					for($i=1;$i<=10;$i++){
						if(isset($v[$i])){
							$string .= $v[$i];
							if($i!=10) $string .= ";";
						}else{
							$string .= "";
							if($i!=10) $string .= ";";
						}
					}
				}


				$string .= "\n";
				$lignes ++;
			}
		}

        $string .=  "98;".count($donnees).";CLEODIS\n";
        $lignes ++;
        $string .= "99;".number_format($total_debit,2,".","").";EUR\n";
       	$lignes ++;
        $string .= "0;".$lignes.";".date("Ymd");


        if($infos['onglet']){
        	header('Content-Type: application/fic');
			header('Content-Disposition: attachment; filename="'.$filename.'"');

	        echo $string;
        }else{
        	return array("filename"=>$filename, "content"=>$string);
        }
    }




     /** Permet de savoir si une affaire était en periode d'engagement à une date donnée
     * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
     * @date 04/05/2017
     */
    public function estEnPeriodeEngagement($date, $id_affaire){
    	ATF::commande()->q->reset()->where("commande.id_affaire", $id_affaire);
    	$commande = ATF::commande()->select_row();

    	if($commande["commande.date_debut"]){
    		$dateDeb = strtotime($commande["commande.date_debut"]);
	    	$dateFin = strtotime($commande["commande.date_evolution"]);

	    	if($dateDeb <= strtotime($date) && strtotime($date) <= $dateFin){
	    		return true;
	    	}
    	}
    	return false;

    }


     /** Export des immobilisation pour ADEO Services
     * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
     * @date 22/12/2016
     * @param array $infos : contient tous les enregistrements
     */
    public function export_immo_adeo(&$infos,$force){

    	$date = date("Y-m-d");
    	if($infos["date"]) $date = $infos["date"];

    	$infos["display"] = true;

		$this->setQuerier(ATF::_s("pager")->create($infos['onglet'])); // Recuperer le querier actuel

        $this->q->addAllFields($this->table)->setLimit(-1)->unsetCount();
        $data = $this->sa();

        $donnees = array();

        foreach ($data as $key => $value) {

        	if($value["facture_fournisseur.type"] == "achat"){

        		if($this->estEnPeriodeEngagement($date,$value["facture_fournisseur.id_affaire_fk"])){

        			$code_magasin = "M0380"; //Par defaut web

		        	if(ATF::affaire()->select($value["facture_fournisseur.id_affaire_fk"] , "type_souscription") == "magasin" && ATF::affaire()->select($value["facture_fournisseur.id_affaire_fk"] , "id_magasin")){
		        		$code_magasin = ATF::magasin()->select(ATF::affaire()->select($value["facture_fournisseur.id_affaire_fk"] , "id_magasin"), "entite_lm");
		        	}

		        	ATF::loyer()->q->reset()->where("id_affaire",$value["facture_fournisseur.id_affaire_fk"])
		        							->where("nature","prolongation","AND",false,"!=")
		        							->where("nature","prolongation_probable","AND",false,"!=");
		        	$loyers_engagement = ATF::loyer()->sa();

		        	$engagement = 0;
		        	foreach ($loyers_engagement as $kv => $vv) {
		        		$engagement += $vv["duree"];
		        	}
		        	$montant = number_format($value["facture_fournisseur.prix_ht"]/$engagement ,2 ,".","");
		        	$total += $montant;

		        	ATF::devis()->q->reset()->where("id_affaire",$value["facture_fournisseur.id_affaire_fk"])->setLimit(1);
	        		$devis = ATF::devis()->select_row();

		        	ATF::devis_ligne()->q->reset()->where("id_devis",$devis["id_devis"])
	        									->setLimit(1);
		        	$ligne = ATF::devis_ligne()->select_row();
					$pack = ATF::pack_produit()->select(ATF::produit()->select($ligne["id_produit"] , "id_pack_produit"));
					$rayon = ATF::rayon()->select($pack["id_rayon"] , "centre_cout_profit");

					$d1 = date("Y-m-01", strtotime(date("Y-m-d")." +1 month"));
					$d1 = date("Ymd", strtotime($d1. " -1 day"));

					$date_comptable = $date_app_taux = $d1;

		        	$donnees[$key][1][1] = "1";
		        	$donnees[$key][1][2] = "1";
		        	$donnees[$key][1][3] = "54";
		        	$donnees[$key][1][4] = $code_magasin;
		        	$donnees[$key][1][5] = "120";
		        	$donnees[$key][1][6] = "CLEODIS";
		        	$donnees[$key][1][7] = "20";
		        	$donnees[$key][1][8] =  $date_comptable; //date comptable = Denier jour de la periode
		        	$donnees[$key][1][9] =  "1";
		        	$donnees[$key][1][10] = "EUR";
		        	$donnees[$key][1][11] = $date_app_taux; //date application du taux
		        	$donnees[$key][1][12] = $rayon; //Centre cout/profit R03(Alarme)- R04(Chaudiere) - 00000(Immo)
		        	$donnees[$key][1][13] = "1";
		    		$donnees[$key][1][14] = "681120";
		    		$donnees[$key][1][15] = "000000000";
		        	$donnees[$key][1][16] = "0";
		        	$donnees[$key][1][17] = "0";
		        	$donnees[$key][1][18] = "0";
		        	$donnees[$key][1][19] = "0";
					$donnees[$key][1][20] = $montant; //Montant debit
					$donnees[$key][1][21] = "0"; //Montant credit
		        	$donnees[$key][1][22] = "0";
					$donnees[$key][1][23] = "Dotation aux amortissements au ".date("d/m/Y", strtotime($date))." / ".ATF::affaire()->select($value["facture_fournisseur.id_affaire_fk"], "ref");
					$donnees[$key][1][24] = $date_comptable; //date comptable
					$donnees[$key][1][25] = "";
					$donnees[$key][1][26] = "";
					$donnees[$key][1][27] = "";
					$donnees[$key][1][28] = "";
					$donnees[$key][1][29] = "";
					$donnees[$key][1][30] = "";
					$donnees[$key][1][31] = "";
					$donnees[$key][1][32] = "";
					$donnees[$key][1][33] = "";
					$donnees[$key][1][34] = "";
					$donnees[$key][1][35] = "";
					$donnees[$key][1][36] = "";

					$donnees[$key][2][1] = "1";
		        	$donnees[$key][2][2] = "1";
		        	$donnees[$key][2][3] = "54";
		        	$donnees[$key][2][4] = $code_magasin;
		        	$donnees[$key][2][5] = "120";
		        	$donnees[$key][2][6] = "CLEODIS";
		        	$donnees[$key][2][7] = "20";
		        	$donnees[$key][2][8] =  $date_comptable; //date comptable = Denier jour de la periode
		        	$donnees[$key][2][9] =  "1";
		        	$donnees[$key][2][10] = "EUR";
		        	$donnees[$key][2][11] = $date_app_taux; //date application du taux
		        	$donnees[$key][2][12] = "00000";
		        	$donnees[$key][2][13] = "1";
		    		$donnees[$key][2][14] = "281560";
		    		$donnees[$key][2][15] = "000000000";
		        	$donnees[$key][2][16] = "0";
		        	$donnees[$key][2][17] = "0";
		        	$donnees[$key][2][18] = "0";
		        	$donnees[$key][2][19] = "0";
					$donnees[$key][2][20] = "0"; //Montant debit
					$donnees[$key][2][21] = $montant; //Montant credit
		        	$donnees[$key][2][22] = "0";
					$donnees[$key][2][23] = "Dotation aux amortissements au ".date("d/m/Y", strtotime($date))." / ".ATF::affaire()->select($value["facture_fournisseur.id_affaire_fk"], "ref");
					$donnees[$key][2][24] = $date_comptable; //date comptable
					$donnees[$key][2][25] = "";
					$donnees[$key][2][26] = "";
					$donnees[$key][2][27] = "";
					$donnees[$key][2][28] = "";
					$donnees[$key][2][29] = "";
					$donnees[$key][2][30] = "";
					$donnees[$key][2][31] = "";
					$donnees[$key][2][32] = "";
					$donnees[$key][2][33] = "";
					$donnees[$key][2][34] = "";
					$donnees[$key][2][35] = "";
        		}
        	}

        }

        $sequence = ATF::constante()->getSequence("__SEQUENCE_IMMO__");
        $filename = 'CLEODIS_IMMO'.$sequence.'.fic';

        header('Content-Type: application/fic');
		header('Content-Disposition: attachment; filename="'.$filename.'"');


		foreach ($donnees as $key => $value) {
			foreach ($value as $k => $v) {
				if($v[1] == "1"){
					for($i=1;$i<=36;$i++){
						if(isset($v[$i])){
							$string .= $v[$i];
							if($i!=36) $string .= ";";
						}else{
							$string .= "";
							if($i!=36) $string .= ";";
						}
					}
				}elseif($v[1] == "2"){
					for($i=1;$i<=10;$i++){
						if(isset($v[$i])){
							$string .= $v[$i];
							if($i!=10) $string .= ";";
						}else{
							$string .= "";
							if($i!=10) $string .= ";";
						}
					}
				}


				$string .= "\n";
				$lignes ++;
			}
		}

        //$string .=  "98;".count($donnees).";CLEODIS\n";
        //$lignes ++;
        $string .= "99;".number_format($total ,2 ,".","").";".number_format($total ,2 ,".","").";EUR\n";
       	$lignes ++;
        $string .= "0;".$lignes.";".date("Ymd");

        echo $string;


    }

};
?>