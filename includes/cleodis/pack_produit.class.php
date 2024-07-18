<?
/** Classe pack_produit
* @package Optima
* @subpackage Cléodis
*/
require __ABSOLUTE_PATH__ . 'libs/ATF/libs/phpseclib/vendor/autoload.php';
use phpseclib\Net\SFTP;
use phpseclib\Crypt\RSA;




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
		$this->addPrivilege("export_middleware_xls");

		$this->addPrivilege("csv_middleware");
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



	public function export_middleware_xls($infos, &$s){
		ob_get_flush();

		if($infos["onglet"] && str_replace("gsa_pack_produit_pack_produit", "", $infos["onglet"]) !== "") {
    		$this->setQuerier($s["pager"]->create($infos['onglet']));
    	}else{
    		ATF::site_associe()->q->reset()->where("export_middleware", "oui");
    		$site_associe_export = ATF::site_associe()->sa();
    		$this->q->reset();
    		foreach ($site_associe_export as $key => $value) {
    			$this->q->where("site_associe", $value["site_associe"], "OR", "site_associe");
    		}
    	}
		//on retiens le where dans le cas d'un onglet pour filtrer les donnéees
		$this->q->addField("id_pack_produit")->setLimit(-1)->unsetCount();
		$data = $this->select_data($s,"saExport");

		$packs = [];
		$lignes = [];
		$produits = [];
		foreach ($data as $pack) {

			// On récupère les packs avec les bonnes colonnes
	        ATF::pack_produit()->q->reset()
				->addField("pack_produit.nom")
				->addField("pack_produit.site_associe")
				->addField("pack_produit.type_offre")
				->addField("pack_produit.loyer")
				->addField("pack_produit.duree")
				->addField("pack_produit.frequence")
				->addField("pack_produit.etat")
				->addField("pack_produit.specifique_partenaire")
				->addField("pack_produit.visible_sur_site")
				->addField("pack_produit.id_pack_produit_besoin")
				->addField("pack_produit.id_pack_produit_produit")
				->addField("pack_produit.url")
				->addField("pack_produit.description")
				->addField("pack_produit.avis_expert")
				->addField("pack_produit.popup")
				->addField("pack_produit.id_document_contrat")
				->addField("pack_produit.prolongation")
				->addField("pack_produit.val_plancher")
				->addField("pack_produit.val_plafond")
				->addField("pack_produit.max_qte")
				->where('id_pack_produit', $pack['id_pack_produit'])
				->addOrder('id_pack_produit', 'DESC');
	        $packs[] = ATF::pack_produit()->select_row();

			// On récupère les packs avec les bonnes colonnes
	        ATF::pack_produit_ligne()->q->reset()
				->addField("pack_produit_ligne.id_pack_produit_ligne")
				->addField("pack_produit_ligne.type")
				->addField("pack_produit_ligne.id_pack_produit")
				->addField("pack_produit_ligne.id_produit")
				->addField("pack_produit_ligne.ref")
				->addField("pack_produit_ligne.produit")
				->addField("pack_produit_ligne.quantite")
				->addField("pack_produit_ligne.min")
				->addField("pack_produit_ligne.max")
				->addField("pack_produit_ligne.option_incluse")
				->addField("pack_produit_ligne.option_incluse_obligatoire")
				->addField("pack_produit_ligne.id_fournisseur")
				->addfield('fournisseur.societe','Fournisseur')
				->addField("pack_produit_ligne.frequence_fournisseur")
				->addField("pack_produit_ligne.id_partenaire")
				->addfield('partenaire.societe','Partenaire')
				->addField("pack_produit_ligne.prix_achat")
				->addField("pack_produit_ligne.code")
				->addField("pack_produit_ligne.serial")
				->addField("pack_produit_ligne.visible")
				->addField("pack_produit_ligne.visible_sur_pdf")
				->addField("pack_produit_ligne.visibilite_prix")
				->addField("pack_produit_ligne.neuf")
				->addField("pack_produit_ligne.date_achat")
				->addField("pack_produit_ligne.ref_simag")
				->addField("pack_produit_ligne.commentaire")
				->addField("pack_produit_ligne.ordre")
				->addField("pack_produit_ligne.principal")
				->addField("pack_produit_ligne.val_modifiable")
				->addField("pack_produit_ligne.valeur")
				->from('pack_produit_ligne','id_fournisseur','societe','id_societe','fournisseur')
				->from('pack_produit_ligne','id_partenaire','societe','id_societe','partenaire')
				->where('id_pack_produit',$pack['pack_produit.id_pack_produit'])
				->setStrict();
	        foreach (ATF::pack_produit_ligne()->select_all() as $l) {
	        	array_push($lignes, $l);
	        }
		}
		// Et maintenant les produits
		foreach ($lignes as $l) {


	        ATF::produit()->q->reset()
				->addField("produit.id_produit")
				->addField("produit.ref")
				->addField("produit.produit")
				->addField("produit.prix_achat")
				->addField("produit.taxe_ecotaxe")
				->addField("produit.taxe_ecomob")
				->addField("produit.url_produit")
				->addField("produit.id_fabriquant")
				->addField("fabriquant.fabriquant", "Fabriquant")
				->addField("sous_categorie.id_categorie", 'id_categorie')
				->addField("categorie.categorie", "Catégorie")
				->addField("produit.id_sous_categorie")
				->addField("sous_categorie.sous_categorie", "Sous categorie")
				->addField("produit.type")
				->addField("produit.id_fournisseur")
				->addField("fournisseur.societe", 'Fournisseur')
				->addField("produit.code")
				->addField("produit.obsolete")
				->addField("produit.id_produit_dd")
				->addField("produit.id_produit_dotpitch")
				->addField("produit.id_produit_format")
				->addField("produit.id_produit_garantie_uc")
				->addField("produit.id_produit_garantie_ecran")
				->addField("produit.id_produit_garantie_imprimante")
				->addField("produit.id_produit_lan")
				->addField("produit.id_produit_lecteur")
				->addField("produit.id_produit_OS")
				->addField("produit.id_produit_puissance")
				->addField("produit.id_produit_ram")
				->addField("produit.id_produit_technique")
				->addField("produit.id_produit_type")
				->addField("produit.id_produit_typeecran")
				->addField("produit.id_produit_viewable")
				->addField("produit.id_processeur")
				->addField("produit.etat")
				->addField("produit.commentaire")
				->addField("produit.type_offre")
				->addField("produit.description")
				->addField("produit.site_associe")
				->addField("produit.loyer")
				->addField("produit.duree")
				->addField("produit.loyer1")
				->addField("produit.duree2")
				->addField("produit.duree1")
				->addField("produit.loyer2")
				->addField("produit.visible_sur_site")
				->addField("produit.avis_expert")
				->addField("produit.services")
				->addField("produit.id_produit_env")
				->addField("produit.id_produit_besoins")
				->addField("produit.id_produit_tel_produit")
				->addField("produit.id_produit_tel_type")
				->addField("produit.url")
				->addField("produit.ean")
				->addField("produit.id_document_contrat")
				->addField("document_contrat.document_contrat", "Document contrat")
				->addField("produit.url_image")
				->addField("produit.livreur")
				->addField("produit.frais_livraison")
				->addField("produit.ref_garantie")
				->addField("produit.id_licence_type")
				->addField("licence_type.licence_type", "Licence type")
				->addField("produit.increment")
				->from('produit','id_fournisseur','societe','id_societe','fournisseur')
				->from('produit','id_fabriquant','fabriquant','id_fabriquant','fabriquant')
				->from('produit','id_sous_categorie','sous_categorie','id_sous_categorie')
				->from('sous_categorie','id_categorie','categorie','id_categorie')
				->from('produit','id_document_contrat','document_contrat','id_document_contrat')
				->from('produit','id_licence_type','licence_type','id_licence_type')
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
		$workbook->setActiveSheetIndex(0);
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
		$workbook->setActiveSheetIndex(1);
	    $sheet->setTitle("Packs");

	    $entetes = array_keys($packs[0]);
	    $entetes = array_map(function ($el) {
	    	return str_replace("pack_produit.","",$el);
	    }, $entetes);
	    $sheet->fromArray($entetes, NULL, 'A1');
		$sheet->fromArray($packs, NULL, 'A2');

        // Troisième onglet
        $sheet = $workbook->createSheet(2);
		$workbook->setActiveSheetIndex(2);
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



    /** Export des produits pour le middleware
     * @author Quentin JANON <qjanon@absystech.fr>
     * @param array $infos : contient éventuellement un pager(onglet) qui permet d'exporter uniquement la sélection
     */
    public function export_middleware($infos,&$s){
    	ob_get_flush();

    	if($infos["onglet"] && str_replace("gsa_pack_produit_pack_produit", "", $infos["onglet"]) !== "") {
    		$this->setQuerier($s["pager"]->create($infos['onglet']));
    	}else{

    		ATF::site_associe()->q->reset()->where("export_middleware", "oui");
    		$site_associe_export = ATF::site_associe()->sa();
    		$this->q->reset();
    		foreach ($site_associe_export as $key => $value) {
    			$this->q->where("site_associe", $value["site_associe"], "OR", "site_associe");
    		}
    	}
		//on retiens le where dans le cas d'un onglet pour filtrer les donnéees
		$this->q->addField("id_pack_produit")->setLimit(-1)->unsetCount();
		$data = $this->select_data($s,"saExport");
		/*if (count($data) > 500) {
			die("Trop de résultat pour générer l'export, cliquez sur le bouton RETOUR de votre navigateur et affiner votre filtrage.");
		}*/




		$packs = [];
		$lignes = [];
		$produits = [];
		foreach ($data as $pack_query) {

			// On récupère les packs avec les bonnes colonnes
	        ATF::pack_produit()->q->reset()
				->addField("pack_produit.nom")
				->addField("pack_produit.site_associe")
				->addField("pack_produit.type_offre")
				->addField("pack_produit.loyer")
				->addField("pack_produit.duree")
				->addField("pack_produit.frequence")
				->addField("pack_produit.etat")
				->addField("pack_produit.specifique_partenaire")
				->addField("pack_produit.visible_sur_site")
				->addField("pack_produit.id_pack_produit_besoin")
				->addField("pack_produit.id_pack_produit_produit")
				->addField("pack_produit.url")
				->addField("pack_produit.description")
				->addField("pack_produit.avis_expert")
				->addField("pack_produit.popup")
				->addField("pack_produit.id_document_contrat")
				->addField("pack_produit.prolongation")
				->addField("pack_produit.val_plancher")
				->addField("pack_produit.val_plafond")
				->addField("pack_produit.max_qte")
				->where('id_pack_produit', $pack_query['id_pack_produit'])
				->addOrder('id_pack_produit', 'DESC');
			$pack = ATF::pack_produit()->select_row();
			$packs[] = $pack;

			// On récupère les packs avec les bonnes colonnes
	        ATF::pack_produit_ligne()->q->reset()
				->addField("pack_produit_ligne.id_pack_produit_ligne")
				->addField("pack_produit_ligne.type")
				->addField("pack_produit_ligne.id_pack_produit")
				->addField("pack_produit_ligne.id_produit")
				->addField("pack_produit_ligne.ref")
				->addField("pack_produit_ligne.produit")
				->addField("pack_produit_ligne.quantite")
				->addField("pack_produit_ligne.min")
				->addField("pack_produit_ligne.max")
				->addField("pack_produit_ligne.option_incluse")
				->addField("pack_produit_ligne.option_incluse_obligatoire")
				->addField("pack_produit_ligne.id_fournisseur")
				->addfield('fournisseur.societe','Fournisseur')
				->addField("pack_produit_ligne.frequence_fournisseur")
				->addField("pack_produit_ligne.id_partenaire")
				->addfield('partenaire.societe','Partenaire')
				->addField("pack_produit_ligne.prix_achat")
				->addField("pack_produit_ligne.code")
				->addField("pack_produit_ligne.serial")
				->addField("pack_produit_ligne.visible")
				->addField("pack_produit_ligne.visible_sur_pdf")
				->addField("pack_produit_ligne.visibilite_prix")
				->addField("pack_produit_ligne.neuf")
				->addField("pack_produit_ligne.date_achat")
				->addField("pack_produit_ligne.ref_simag")
				->addField("pack_produit_ligne.commentaire")
				->addField("pack_produit_ligne.ordre")
				->addField("pack_produit_ligne.principal")
				->addField("pack_produit_ligne.val_modifiable")
				->addField("pack_produit_ligne.valeur")
				->from('pack_produit_ligne','id_fournisseur','societe','id_societe','fournisseur')
				->from('pack_produit_ligne','id_partenaire','societe','id_societe','partenaire')
				->where('id_pack_produit',$pack['pack_produit.id_pack_produit'])
				->setStrict();
	        foreach (ATF::pack_produit_ligne()->select_all() as $l) {
				$l["pack_produit_ligne.site_associe"] = $pack["pack_produit.site_associe"];
	        	array_push($lignes, $l);
	        }
		}
		// Et maintenant les produits
		foreach ($lignes as $l) {
	        ATF::produit()->q->reset()
				->addField("produit.id_produit")
				->addField("produit.ref")
				->addField("produit.produit")
				->addField("produit.prix_achat")
				->addField("produit.taxe_ecotaxe")
				->addField("produit.taxe_ecomob")
				->addField("produit.url_produit")
				->addField("produit.id_fabriquant")
				->addField("fabriquant.fabriquant", "Fabriquant")
				->addField("sous_categorie.id_categorie", 'id_categorie')
				->addField("categorie.categorie", "Catégorie")
				->addField("produit.id_sous_categorie")
				->addField("sous_categorie.sous_categorie", "Sous categorie")
				->addField("produit.type")
				->addField("produit.id_fournisseur")
				->addField("fournisseur.societe", 'Fournisseur')
				->addField("produit.code")
				->addField("produit.obsolete")
				->addField("produit.id_produit_dd")
				->addField("produit.id_produit_dotpitch")
				->addField("produit.id_produit_format")
				->addField("produit.id_produit_garantie_uc")
				->addField("produit.id_produit_garantie_ecran")
				->addField("produit.id_produit_garantie_imprimante")
				->addField("produit.id_produit_lan")
				->addField("produit.id_produit_lecteur")
				->addField("produit.id_produit_OS")
				->addField("produit.id_produit_puissance")
				->addField("produit.id_produit_ram")
				->addField("produit.id_produit_technique")
				->addField("produit.id_produit_type")
				->addField("produit.id_produit_typeecran")
				->addField("produit.id_produit_viewable")
				->addField("produit.id_processeur")
				->addField("produit.etat")
				->addField("produit.commentaire")
				->addField("produit.type_offre")
				->addField("produit.description")
				->addField("produit.loyer")
				->addField("produit.duree")
				->addField("produit.loyer1")
				->addField("produit.duree2")
				->addField("produit.duree1")
				->addField("produit.loyer2")
				->addField("produit.visible_sur_site")
				->addField("produit.avis_expert")
				->addField("produit.services")
				->addField("produit.id_produit_env")
				->addField("produit.id_produit_besoins")
				->addField("produit.id_produit_tel_produit")
				->addField("produit.id_produit_tel_type")
				->addField("produit.url")
				->addField("produit.ean")
				->addField("produit.id_document_contrat")
				->addField("document_contrat.document_contrat", "Document contrat")
				->addField("produit.url_image")
				->addField("produit.livreur")
				->addField("produit.frais_livraison")
				->addField("produit.ref_garantie")
				->addField("produit.id_licence_type")
				->addField("licence_type.licence_type", "Licence type")
				->addField("produit.increment")
				->from('produit','id_fournisseur','societe','id_societe','fournisseur')
				->from('produit','id_fabriquant','fabriquant','id_fabriquant','fabriquant')
				->from('produit','id_sous_categorie','sous_categorie','id_sous_categorie')
				->from('sous_categorie','id_categorie','categorie','id_categorie')
				->from('produit','id_document_contrat','document_contrat','id_document_contrat')
				->from('produit','id_licence_type','licence_type','id_licence_type')
				->where('id_produit',$l['pack_produit_ligne.id_produit'])
				->setStrict();

	        foreach (ATF::produit()->select_all() as $p) {
				$p["produit.site_associe"] = $l["pack_produit_ligne.site_associe"];
	        	array_push($produits, $p);
	        }
		}

		$site_associe = array();

		foreach ($packs as $key => $value) {
			$site_associe[$value["pack_produit.site_associe"]][$value["pack_produit.id_pack_produit"]] = true ;
		}

		$zipname = 'middleware' . date("Ymd-Hi") . '.zip';
		$zip = new ZipArchive;
		$zip->open($zipname, ZipArchive::CREATE);

		foreach ($site_associe as $key => $value) {

			$file = $this->csv_middleware("Packs", $packs, $key, $site_associe);
			$zip->addFile($file);
			$this->csv_middleware_to_ftp($file);

			$file = $this->csv_middleware("Produits", $produits, $key, $site_associe);
			$zip->addFile($file);
			$this->csv_middleware_to_ftp($file);

			$file = $this->csv_middleware("Lignes", $lignes, $key, $site_associe);
			$zip->addFile($file);
			$this->csv_middleware_to_ftp($file);

			file_put_contents($key.".txt", "ok");
			$this->csv_middleware_to_ftp($key.".txt");
		}


		$zip->close();


		if($infos["export_from_batch"]){

			return true;
		}else{

			header('Content-Type: application/zip');
			header('Content-disposition: attachment; filename='.$zipname);
			header('Content-Length: ' . filesize($zipname));
			readfile($zipname);

		}
	}

	/**
	 * Création des fichiers CSV pour le middleware
	 * @author : Morgan FLEURQUIN <mfleurquin@absystech.fr>
	 * @param  String $titre                 Type de fichier (Packs / Produits / Lignes)
	 * @param  array  $data                  Données des packs
	 * @param  String $site_associe          Site associé
	 * @param  Array  $pack_par_site_associe Données des packs, par site associé
	 * @return String File                   Nom du fichier
	 */
	public function csv_middleware($titre, $donnees, $site_associe, $pack_par_site_associe){


		$fn = $titre . "-" . $site_associe . ".csv";
        $fp = fopen($fn, 'w');
		$data = [];

		switch ($titre) {
			case 'Produits':
				$entetes = array_keys($donnees[0]);
				$entetes = array_map(function ($el) {
			    	return str_replace("produit.","",$el);
			    }, $entetes);
			    $produitDedoublonne = [];

				foreach($donnees as $p){
					if($site_associe === $p['produit.site_associe']){
						$produitDedoublonne[$p['produit.id_produit']] = $p;
					}
				}
				$data = $produitDedoublonne;

			break;

			case 'Packs':
				$entetes = array_keys($donnees[0]);
			    $entetes = array_map(function ($el) {
			    	return str_replace("pack_produit.","",$el);
			    }, $entetes);

			    $packs = [];

			    foreach ($donnees as $key => $value) {
			    	if($pack_par_site_associe[$site_associe][$value["pack_produit.id_pack_produit"]]){
			    		$packs[] = $value;
			    	}
			    }

			    $data = $packs;
			break;


			case 'Lignes':
				$entetes = array_keys($donnees[0]);
			    $entetes = array_map(function ($el) {
			    	return str_replace("pack_produit_ligne.","",$el);
			    }, $entetes);

			    $lignes = [];
			    foreach ($donnees as $key => $value) {
			    	if($pack_par_site_associe[$site_associe][$value["pack_produit_ligne.id_pack_produit"]]){
			    		$lignes[] = $value;
			    	}
			    }


			    $data = $lignes;
			break;
		}

		$entete_format = array();
		$entete_format[0] = $entetes;

		$data = array_merge($entete_format , $data);



		foreach ($data as $fields => $value) {

		    fputcsv($fp, $value, ";", '"');
		}

		fclose($fp);

		return $fn;
	}

	/**
	 * Envoi par SFTP des fichiers CSV middleware
	 * @author : Morgan FLEURQUIN <mfleurquin@absystech.fr>
	 * @param  String $file
	 */
	public function csv_middleware_to_ftp($file){
		try{
			$key = new RSA();
			$key->loadKey(file_get_contents(__MIDDLEWARE_FTP_PRIVATE_KEY__));
			$sftp = new SFTP(__MIDDLEWARE_FTP_HOST__, __MIDDLEWARE_FTP_PORT__);

			if (!$sftp->login(__MIDDLEWARE_FTP_LOGIN__, $key)) {
			    log::logger("Login failed" , "upload_middleware");
				log::logger($sftp->getLastSFTPError(), "upload_middleware");
			}else{
				log::logger("Login success" , "upload_middleware");
			}

			$sftp->put(__MIDDLEWARE_FTP_FOLDER__.$file, $file, SFTP::SOURCE_LOCAL_FILE);

			return true;

		}catch(Exception $e){
			log::logger($e->getMessage() , "upload_middleware");
		}
	}

}


class pack_produit_boulanger extends pack_produit{ }