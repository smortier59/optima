<?
/** Classe pack_produit
* @package Optima
* @subpackage Cléodis
*/
class pack_produit extends classes_optima {
	// Mapping prévu pour un autocomplete sur produit
	public static $autocompleteMapping = array(
		array("name"=>'id_pack_produit', "mapping"=>0),
		array("name"=>'nom', "mapping"=>1),
		array("name"=>'site_associe', "mapping"=>2)
	);


	function __construct($table_or_id=NULL) {
		$this->table ="pack_produit";
		parent::__construct($table_or_id);

		$this->colonnes['fields_column'] = array(
			 'pack_produit.nom'
			,'pack_produit.site_associe'
			,'loyer'=>array("width"=>80,"rowEditor"=>"setInfos")
			,'duree'=>array("width"=>80,"rowEditor"=>"setInfos")
			,'id_document_contrat'
			,'visible_sur_site'=>array("rowEditor"=>"ouinon","renderer"=>"etat","width"=>80)

		);

		$this->colonnes['primary'] = array(
			 'nom'
			,'site_associe'
			,'etat'
			,'id_pack_produit_besoin'
			,'id_pack_produit_produit'
			,'specifique_partenaire'
		);



		$this->colonnes['panel']['lignes'] = array(
			"produits"=>array("custom"=>true)
		);

		$this->colonnes['panel']['lignes_option_partenaire'] = array(
			"produits_option_partenaire"=>array("custom"=>true)
		);

		$this->colonnes['panel']['lignes_non_visible'] = array(
			"produits_non_visible"=>array("custom"=>true)
		);


		// Propriété des panels
		$this->panels['lignes'] = array("visible"=>true, 'nbCols'=>1);
		$this->panels['lignes_option_partenaire'] = array("visible"=>true, 'nbCols'=>1);
		$this->panels['lignes_non_visible'] = array("visible"=>true, 'nbCols'=>1);


		$this->files["photo"] = array("type"=>"png","convert_from"=>array("jpg","png","gif"),"select"=>true);

		$this->field_nom = "nom";
		$this->foreign_key["specifique_partenaire"] = "societe";

		$this->fieldstructure();

		$this->onglets = array('pack_produit_ligne');


		$this->formExt=true;
		//$this->no_delete = true;
		$this->selectAllExtjs=true;

		$this->addPrivilege("setInfos","update");
		$this->addPrivilege("EtatUpdate");
		$this->addPrivilege("export_middleware");
	}

	/**
    * Surcharge de la méthode autocomplete pour faire apparaître sous_catégorie et catégorie
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param array $infos
    * @return int  id si enregistrement ok
    */
	function autocomplete($infos) {

			// Récupérer les produits
			$this->q->reset()
				->addField("id_pack_produit")
				->addField("nom")
				->addField("site_associe");
		return parent::autocomplete($infos,false);
	}


	/**
	 * Permet de modifier un champs en AJAX
	 * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	 * @return bool
	 */
	public function setInfos($infos){
		$res = $this->u(array("id_pack_produit"=> $this->decryptId($infos["id_pack_produit"]),
						  $infos["field"] => $infos[$infos["field"]])
					);
		if($res){
			ATF::$msg->addNotice(
				loc::mt(ATF::$usr->trans("notice_update_success"))
				,ATF::$usr->trans("notice_success_title")
			);
		}
	}
	public function EtatUpdate($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL){

        $data["id_pack_produit"] = $this->decryptId($infos["id_pack_produit"]);
        $data[$infos["field"]] = $infos[$infos["field"]];

        if ($r=$this->u($data)) {
            ATF::$msg->addNotice(loc::mt(ATF::$usr->trans("notice_update_success")));
        }
        return $r;
    }



	/**
	* Surcharge de l'insert afin d'insérer les lignes de pack_produit de créer l'affaire si elle n'existe pas
	* @author Morgan FLEURQUIN <mfleruquin@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	*/
	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){


		$infos_ligne = json_decode($infos["values_".$this->table]["produits"],true);
		$infos_ligne_non_visible = json_decode($infos["values_".$this->table]["produits_non_visible"],true);
		$infos_ligne_option_partenaire = json_decode($infos["values_".$this->table]["produits_option_partenaire"],true);

		$this->infoCollapse($infos);

		$infos["url"] = util::mod_rewrite($infos["nom"],1,true);


		ATF::db($this->db)->begin_transaction();

		$last_id=parent::insert($infos,$s,$files,$cadre_refreshed,$nolog);

		//Lignes non visibles
		if($infos_ligne_non_visible){
			foreach($infos_ligne_non_visible as $key=>$item){
				$infos_ligne_non_visible[$key]["pack_produit_ligne__dot__visible"]="non";
				$infos_ligne[]=$infos_ligne_non_visible[$key];
			}
		}

		if($infos_ligne_option_partenaire){
			foreach($infos_ligne_option_partenaire as $key=>$item){
				if(!$item["pack_produit_ligne__dot__id_partenaire_fk"]){
					ATF::db($this->db)->rollback_transaction();
					throw new errorATF("Ligne d'option partenaire sans partenaire",882);
				}
				$infos_ligne[]=$infos_ligne_option_partenaire[$key];
			}
		}

		//Lignes
		if($infos_ligne){
			$infos_ligne=$this->extJSUnescapeDot($infos_ligne,"pack_produit_ligne");
			foreach($infos_ligne as $key=>$item){
				$item["id_pack_produit"]=$last_id;
				if(!$item["id_fournisseur"]){
					ATF::db($this->db)->rollback_transaction();
					throw new errorATF("Ligne de pack_produit sans fournisseur",882);
				}

				if(!$item["id_produit"]){
					ATF::db($this->db)->rollback_transaction();
					throw new errorATF($item["produit"]. " ne fait réference à aucun produit (Manque Id produit)",882);
				}
				ATF::pack_produit_ligne()->i($item);
			}
		}else{
			ATF::db($this->db)->rollback_transaction();
			throw new errorATF("pack_produit sans produits",877);
		}
		ATF::db($this->db)->commit_transaction();


		if(is_array($cadre_refreshed)){	ATF::pack_produit()->redirection("select",$last_id); }
		return $last_id;
	}

	/**
	* corrige les lignes de pack_produit
	* @author Morgan FLEURQUIN <mfleruquin@absystech.fr>
	* @param array $infos_ligne ligne de pack_produit
	* @return array $infos_ligne_escapeDot ligne de pack_produit corrigé
	*/
	public function extJSUnescapeDot($infos_ligne,$escape){
		foreach($infos_ligne as $key=>$item){
			foreach($item as $k=>$i){
				$k_unescape=util::extJSUnescapeDot($k);
				$item[str_replace($escape.".","",$k_unescape)]=$i;
				unset($item[$k]);
			}
			$item["id_fournisseur"]=ATF::societe()->decryptId($item["id_fournisseur_fk"]);
			$item["id_partenaire"]=ATF::societe()->decryptId($item["id_partenaire_fk"]);
			$item["id_produit"]=ATF::produit()->decryptId($item["id_produit_fk"]);
			unset($item["id_fournisseur_fk"],$item["id_produit_fk"], $item["id_partenaire_fk"]);
			$item["index"]=util::extJSEscapeDot($key);
			if(!$item["quantite"]){	$item["quantite"]=0; }
			$infos_ligne_escapeDot[]=$item;
		}
		return $infos_ligne_escapeDot;
	}

	/**
	* Surcharge de l'insert afin d'insérer les lignes de pack_produit de créer l'affaire si elle n'existe pas
	* @author Morgan FLEURQUIN <mfleruquin@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	*/
	public function update($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		$infos_ligne = json_decode($infos["values_".$this->table]["produits"],true);
		$infos_ligne_non_visible = json_decode($infos["values_".$this->table]["produits_non_visible"],true);
		$infos_ligne_option_partenaire = json_decode($infos["values_".$this->table]["produits_option_partenaire"],true);
		$this->infoCollapse($infos);



		if(!$infos["url"]){
			$infos["url"] = util::mod_rewrite($infos["nom"],1,true);
		}

		$last_id = $this->decryptId($infos["id_pack_produit"]);

		ATF::db($this->db)->begin_transaction();


		ATF::pack_produit_ligne()->q->reset()->where("id_pack_produit", $last_id);
		$lignes = ATF::pack_produit_ligne()->sa();

		foreach ($lignes as $key => $value) {
			ATF::pack_produit_ligne()->d($value["id_pack_produit_ligne"]);
		}



		parent::update($infos,$s,$files,$cadre_refreshed,$nolog);

		//Lignes non visibles
		if($infos_ligne_non_visible){
			foreach($infos_ligne_non_visible as $key=>$item){
				$infos_ligne_non_visible[$key]["pack_produit_ligne__dot__visible"]="non";
				$infos_ligne[]=$infos_ligne_non_visible[$key];
			}
		}

		//Ligne d'option partenaire
		if($infos_ligne_option_partenaire){
			foreach($infos_ligne_option_partenaire as $key=>$item){
				if(!$item["pack_produit_ligne__dot__id_partenaire_fk"]){
					ATF::db($this->db)->rollback_transaction();
					throw new errorATF("Ligne d'option partenaire sans partenaire",882);
				}
				$infos_ligne[]=$infos_ligne_option_partenaire[$key];
			}
		}

		//Lignes
		if($infos_ligne){
			$infos_ligne=$this->extJSUnescapeDot($infos_ligne,"pack_produit_ligne");
			foreach($infos_ligne as $key=>$item){
				$item["id_pack_produit"]=$last_id;
				if(!$item["id_fournisseur"]){
					ATF::db($this->db)->rollback_transaction();
					throw new errorATF("Ligne de pack_produit sans fournisseur",882);
				}
				if(!$item["id_produit"]){
					ATF::db($this->db)->rollback_transaction();
					throw new errorATF($item["produit"]. " ne fait réference à aucun produit (Manque Id produit)",882);
				}
				ATF::pack_produit_ligne()->i($item);
			}
		}else{
			ATF::db($this->db)->rollback_transaction();
			throw new errorATF("pack_produit sans produits",877);
		}
		ATF::db($this->db)->commit_transaction();


		if(is_array($cadre_refreshed)){	ATF::pack_produit()->redirection("select",$last_id); }
		return $last_id;
	}

	/**
	 * Retourne la durée du pack par rapport au produit principal
	 * @author : Morgan FLEURQUIN <mfleurquin@absystech.fr>
	 * @param  [type] $id_pack_produit [description]
	 * @return int  duree
	 */
	public function getDureePack($id_pack_produit){
		ATF::pack_produit_ligne()->q->reset()->where("id_pack_produit", $id_pack_produit)->addOrder("ordre","ASC")->setLimit(1);
		$princ = ATF::pack_produit_ligne()->select_row();

		return ATF::produit()->select($princ["id_produit"], "duree");


	}

	/**
	 * Retourne le produit principal d'un pack
	 * @author : Quentin JANON <qjanon@absystech.fr>
	 * @param  Integer $id_pack_produit
	 * @return int id produit
	 */
	public function getProduitPrincipal($id_pack_produit){
		ATF::pack_produit_ligne()->q->reset()->where("id_pack_produit", $id_pack_produit)->addOrder("ordre","ASC")->setLimit(1);
		$princ = ATF::pack_produit_ligne()->select_row();

		return $princ["id_produit"];


	}

	/**
	 * Retourne tous les ID packs où le produit est présent
	 * @author : Quentin JANON <qjanon@absystech.fr>
	 * @param  Integer $id_pack_produit
	 * @return string ID des packs séparé par virgule
	 */
	public function getIdPackFromProduit($id_produit, $etat = false, $principal = false){
		ATF::pack_produit_ligne()->q->reset()
			->addField('GROUP_CONCAT(pack_produit_ligne.id_pack_produit)','id_pack_produit')
			->where("id_produit", $id_produit);

		if ($etat) {
			ATF::pack_produit_ligne()->q->from("pack_produit_ligne","id_pack_produit",'pack_produit',"id_pack_produit");
			ATF::pack_produit_ligne()->q->where('pack_produit.etat',$etat);
		}
		if ($principal) {
			ATF::pack_produit_ligne()->q->where('pack_produit_ligne.principal',"oui");
		}
		$r = ATF::pack_produit_ligne()->select_row();
		return $r["id_pack_produit"];
	}

	/**
	 * Retourne tous les produits actif d'un pack
	 * @author : Quentin JANON <qjanon@absystech.fr>
	 * @param  Integer $id_pack
	 * @return Array Liste des produits du pack
	 */
	public function getProduitFromPack($id_pack, $etat = false){
		ATF::pack_produit_ligne()->q->reset()
			->addField('produit.id_produit', 'id_produit')
			->addField('produit.produit', 'produit')
			->addField('produit.id_fabriquant', 'id_fabriquant')
			->addField('pack_produit_ligne.id_pack_produit', 'id_pack_produit')
			->addField('pack_produit_ligne.id_pack_produit_ligne', 'id_pack_produit_ligne')
			->from("pack_produit_ligne","id_produit","produit","id_produit")
			->where("pack_produit_ligne.id_pack_produit", $id_pack)
			->where("produit.etat", "actif")
			->addGroup('pack_produit_ligne.id_produit')
			->setStrict();
		if ($etat) {
			ATF::pack_produit_ligne()->q->from("pack_produit_ligne","id_pack_produit",'pack_produit',"id_pack_produit");
			ATF::pack_produit_ligne()->q->where('pack_produit.etat',$etat);
		}
		$r = ATF::pack_produit_ligne()->select_all();
		return $r;
	}

    /** Export des produits pour le middleware
     * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
     * @param array $infos : contient tous les enregistrements
    public function export_middleware($infos,&$s){
		$this->setQuerier($s["pager"]->create($infos['onglet']));
		//on retiens le where dans le cas d'un onglet pour filtrer les donnéees
		$this->q->addField("id_pack_produit")->setLimit(-1)->unsetCount();
		$data = $this->select_data($s,"saExport");
		if (count($data) > 500) {
			die("Trop de résultat pour générer l'export, cliquez sur le bouton RETOUR de votre navigateur et affiner votre filtrage.");
		}

		$packs = [];
		$lignes = [];
		$produits = [];
		foreach ($data as $pack) {
			// On récupère les packs avec les bonnes colonnes
	        ATF::pack_produit()->q->reset()
	        				->addField('id_pack_produit')
	        				->addField('nom')
	        				->addField('etat')
	        				->addField('site_associe')
	        				->addField('visible_sur_site')
	        				->addField('description')
	        				->where('id_pack_produit', $pack['id_pack_produit'])
	        				->setStrict()
	        				->addOrder('id_pack_produit', 'DESC');
	        $packs[] = ATF::pack_produit()->select_row();

			// On récupère les packs avec les bonnes colonnes
	        ATF::pack_produit_ligne()->q->reset()
	        				 ->addField('type')
	        				 ->addField('id_pack_produit')
	        				 ->addField('id_produit')
	        				 ->addField('principal')
	        				 ->addField('quantite')
	        				 ->addField('min')
	        				 ->addField('max')
	        				 ->addField('option_incluse')
	        				 ->addField('option_incluse_obligatoire')
	        				 ->addField('visible')
	        				 ->addField('ordre')
	        				 ->addField('visibilite_prix')
	        				 ->addField('visible_sur_pdf')
	        				 ->addField('prix_achat')
	        				 ->where('id_pack_produit',$pack['id_pack_produit'])
	        				 ->setStrict();
	        foreach (ATF::pack_produit_ligne()->select_all() as $l) {
	        	array_push($lignes, $l);
	        }


		}
		// Et maintenant les produits
		foreach ($lignes as $l) {
	        ATF::produit()->q->reset()
	        				 ->addField('id_produit')
	        				 ->addField('ref')
	        				 ->addField('produit')
	        				 ->addField('etat')
	        				 ->addField('commentaire')
	        				 ->addField('prix_achat')
	        				 ->addField('taxe_ecotaxe')
	        				 ->addField('taxe_ecomob')
	        				 ->addField('type')
	        				 ->addField('id_fournisseur')
	        				 ->addField('id_fabriquant')
	        				 ->addField('url_produit') // FAKE column, remplacer par la catégorie plus bas
	        				 ->addField('id_sous_categorie')
	        				 ->addField('loyer')
	        				 ->addField('duree')
	        				 ->addField('visible_sur_site')
	        				 ->addField('ean')
	        				 ->addField('description')
	        				 ->addField('id_document_contrat')
	        				 ->addField('url')
	        				 ->where('id_produit',$l['id_produit'])
	        				 ->setStrict();

	        foreach (ATF::produit()->select_all() as $p) {
	        	array_push($produits, $p);
	        }
		}


        require_once __ABSOLUTE_PATH__."libs/ATF/libs/PHPExcel/Classes/PHPExcel.php";
		require_once __ABSOLUTE_PATH__."libs/ATF/libs/PHPExcel/Classes/PHPExcel/Writer/Excel5.php";

		$fname = tempnam(__TEMPORARY_PATH__, __TEMPLATE__.ATF::$usr->getID());

		$workbook = new PHPExcel;

		$sheets = array("Packs","Packs-Produits","Produits");		

		$worksheet_auto = new PHPEXCEL_ATF($workbook,0);


        // Premier onglet
        $sheet = $workbook->getActiveSheet();
		$workbook->setActiveSheetIndex($key);
	    $sheet->setTitle("Produits");

		//mise en place des titres
		$entetes = array(
        	 "A"=>array('Type de produit',20)
        	,"B"=>array('Référence du produit',20)
			,"C"=>array('Désignation',20)
			,"D"=>array('Etat',20)
			,"E"=>array('Commentaire',20)
			,"F"=>array('Prix d\'achat dont ecotaxe',20)
			,"G"=>array('EcoTaxe',20)
			,"H"=>array('Eco Mobilier',20)
			,"I"=>array('Type',20)
			,"J"=>array('Fournisseur',20)
			,"K"=>array('Fabriquant',20)
			,"L"=>array('Catégorie',20)
			,"M"=>array('Sous Catégorie',20)
			,"N"=>array('Loyer',20)
			,"O"=>array('Durée',20)
			,"P"=>array('Visible sur le site',20)
			,"Q"=>array('EAN',20)
			,"R"=>array('Description',20)
			,"S"=>array('id_document_contrat',20)
			,"T"=>array('URL Image',20)
		);
    	$i=0;
    	foreach($entetes as $col=>$titre){
			$sheet->setCellValueByColumnAndRow($i , 1, $titre[0]);
			$sheet->getColumnDimension($col)->setWidth($titre[1]);
			$i++;
        }

    	$i=0;
    	$j=2;
		foreach ($produits as $k=>$v) {
			foreach ($v as $col => $value) {
				switch($col) {
					case 'id_produit': $value = ''; break;
					case 'prix_achat': $value = $v['prix_achat']+$v['taxe_ecotaxe']; break;
					case 'id_fournisseur': $value = ATF::societe()->nom($value); break;
					case 'id_fabriquant': $value = ATF::fabriquant()->nom($value); break;
					case 'url_produit': $value = ATF::categorie()->nom(ATF::sous_categorie()->select($v['id_sous_categorie'],'id_categorie')); break;
					case 'id_sous_categorie': $value = ATF::sous_categorie()->nom($value); break;
				}
				$sheet->setCellValueByColumnAndRow($i , $j, $value);
				$i++;
			}
	        $i=0;
	        $j++;
		}

        // Second onglet
        $sheet = $workbook->createSheet(1);
		$workbook->setActiveSheetIndex($key);
	    $sheet->setTitle("Packs");
		//mise en place des titres
		$entetes = array(
        	 "A"=>array('N°',20)
        	,"B"=>array('Nom',20)
			,"C"=>array('Etat',20)
			,"D"=>array('Site associé',20)
			,"E"=>array('Visible sur le site',20)
			,"F"=>array('Description',20)
		);
    	$i=0;
    	foreach($entetes as $col=>$titre){
			$sheet->setCellValueByColumnAndRow($i , 1, $titre[0]);
			$sheet->getColumnDimension($col)->setWidth($titre[1]);
			$i++;
        }		 
    	$i=0;
    	$j=2;
		foreach ($packs as $k=>$v) {
			foreach ($v as $col => $value) {
				$sheet->setCellValueByColumnAndRow($i , $j, $value);
				$i++;
			}
	        $i=0;
	        $j++;
		}

        // Troisième onglet
        $sheet = $workbook->createSheet(2);
		$workbook->setActiveSheetIndex($key);
	    $sheet->setTitle("Packs-Produits");
		//mise en place des titres
		$entetes = array(
        	 "A"=>array('Type',20)
        	,"B"=>array('N°',20)
			,"C"=>array('Ref',20)
			,"D"=>array('Produit principal ?',20)
			,"E"=>array('Quantité',20)
			,"F"=>array('Min',20)
			,"G"=>array('Max',20)
			,"H"=>array('Option incluse',20)
			,"I"=>array('Option incluse obligatoire',20)
			,"J"=>array('Afficher sur le site',20)
			,"K"=>array('Ordre',20)
			,"L"=>array('Ligne de produit visible',20)
			,"M"=>array('Visible sur PDF',20)
			,"N"=>array('Prix d\'achat dont ecotax',20)
		);
    	foreach($entetes as $col=>$titre){
			$sheet->setCellValueByColumnAndRow($i , 1, $titre[0]);
			$sheet->getColumnDimension($col)->setWidth($titre[1]);
			$i++;
        }

    	$i=0;
    	$j=2;
		foreach ($lignes as $k=>$v) {
			foreach ($v as $col => $value) {
				switch($col) {
					case 'type': $value = ''; break;
					case 'id_produit': ATF::produit()->select($value, 'ref'); break;
				}
				$sheet->setCellValueByColumnAndRow($i , $j, $value);
				$i++;
			}
	        $i=0;
	        $j++;
		}		    		

		$writer = new PHPExcel_Writer_Excel5($workbook);

		$writer->save($fname);
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition:inline;filename=export_middleware-'.date("YmdHis").'.xls');
		header("Cache-Control: private");
		$fh=fopen($fname, "rb");
		fpassthru($fh);
		unlink($fname);
		PHPExcel_Calculation::getInstance()->__destruct();

	}
     */

    /** Export des produits pour le middleware
     * @author Quentin JANON <qjanon@absystech.fr>
     * @param array $infos : contient éventuellement un pager(onglet) qui permet d'exporter uniquement la sélection
     */
    public function export_middleware($infos,&$s){
    	ob_get_flush();
		$this->setQuerier($s["pager"]->create($infos['onglet']));
		//on retiens le where dans le cas d'un onglet pour filtrer les donnéees
		$this->q->addField("id_pack_produit")->setLimit(-1)->unsetCount();
		$data = $this->select_data($s,"saExport");
		if (count($data) > 500) {
			die("Trop de résultat pour générer l'export, cliquez sur le bouton RETOUR de votre navigateur et affiner votre filtrage.");
		}

		$packs = [];
		$lignes = [];
		$produits = [];
		foreach ($data as $pack) {
			// On récupère les packs avec les bonnes colonnes
	        ATF::pack_produit()->q->reset()
	        				->addAllFields('pack_produit')
	        				->where('id_pack_produit', $pack['id_pack_produit'])
	        				->setStrict()
	        				->addOrder('id_pack_produit', 'DESC');
	        $packs[] = ATF::pack_produit()->select_row();

			// On récupère les packs avec les bonnes colonnes
	        ATF::pack_produit_ligne()->q->reset()
	        				->addAllFields('pack_produit_ligne')
	        				->where('id_pack_produit',$pack['pack_produit.id_pack_produit'])
	        				->setStrict();
	        foreach (ATF::pack_produit_ligne()->select_all() as $l) {
	        	array_push($lignes, $l);
	        }
		}
		// Et maintenant les produits
		foreach ($lignes as $l) {
	        ATF::produit()->q->reset()
	        				->addAllFields('produit')
	        				->where('id_produit',$l['pack_produit_ligne.id_produit'])
	        				->setStrict();

	        foreach (ATF::produit()->select_all() as $p) {
	        	array_push($produits, $p);
	        }
		}

	    // die('fuck it baby');

        require_once __ABSOLUTE_PATH__."libs/ATF/libs/PHPExcel/Classes/PHPExcel.php";
		require_once __ABSOLUTE_PATH__."libs/ATF/libs/PHPExcel/Classes/PHPExcel/Writer/Excel5.php";

		$fname = tempnam(__TEMPORARY_PATH__, __TEMPLATE__.ATF::$usr->getID());

		$workbook = new PHPExcel;

		$sheets = array("Packs","Packs-Produits","Produits");		

		$worksheet_auto = new PHPEXCEL_ATF($workbook,0);


        // Premier onglet
        $sheet = $workbook->getActiveSheet();
		$workbook->setActiveSheetIndex($key);
	    $sheet->setTitle("Produits");

	    $entetes = array_keys($produits[0]);
	    $entetes = array_map(function ($el) {
	    	return str_replace("produit.","",$el);
	    }, $entetes);

		$produitDedoublonne = [];
		foreach ($produits as $p) { $produitDedoublonne[$p['produit.id_produit']] = $p; }
		$produits = $produitDedoublonne;


	    $sheet->fromArray($entetes, NULL, 'A1');
		$sheet->fromArray($produits, NULL, 'A2');

        // Second onglet
        $sheet = $workbook->createSheet(1);
		$workbook->setActiveSheetIndex($key);
	    $sheet->setTitle("Packs");

	    $entetes = array_keys($packs[0]);
	    $entetes = array_map(function ($el) {
	    	return str_replace("pack_produit.","",$el);
	    }, $entetes);
	    $sheet->fromArray($entetes, NULL, 'A1');
		$sheet->fromArray($packs, NULL, 'A2');

        // Troisième onglet
        $sheet = $workbook->createSheet(2);
		$workbook->setActiveSheetIndex($key);
	    $sheet->setTitle("Lignes");

	    $entetes = array_keys($lignes[0]);
	    $entetes = array_map(function ($el) {
	    	return str_replace("pack_produit_ligne.","",$el);
	    }, $entetes);
	    $sheet->fromArray($entetes, NULL, 'A1');
		$sheet->fromArray($lignes, NULL, 'A2');


		foreach ($workbook->getWorksheetIterator() as $worksheet) {

		    $workbook->setActiveSheetIndex($workbook->getIndex($worksheet));

		    $sheet = $workbook->getActiveSheet();
		    $cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
		    $cellIterator->setIterateOnlyExistingCells(true);
		    /** @var PHPExcel_Cell $cell */
		    foreach ($cellIterator as $cell) {
		        $sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
		    }
		}



		$writer = new PHPExcel_Writer_Excel5($workbook);

		$writer->save($fname);
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition:inline;filename=export_middleware-'.date("YmdHis").'.xls');
		header("Cache-Control: private");
		$fh=fopen($fname, "rb");
		fpassthru($fh);
		unlink($fname);
		PHPExcel_Calculation::getInstance()->__destruct();

	}


}


class pack_produit_boulanger extends pack_produit{ }