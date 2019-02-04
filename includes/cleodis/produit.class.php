<?
/** Classe devis
* @package Optima
* @subpackage Cleodis
*/
require_once dirname(__FILE__)."/../produit.class.php";
class produit_cleodis extends produit {
	// Mapping prévu pour un autocomplete sur produit
	public static $autocompleteMapping = array(
		array("name"=>'nom', "mapping"=>0),
		array("name"=>'type', "mapping"=>1),
		array("name"=>'ref', "mapping"=>2),
		array("name"=>'fournisseur', "mapping"=>3),
		array("name"=>'prix_achat', "mapping"=>4),
		array("name"=>'produit_dd', "mapping"=>5),
		array("name"=>'fabriquant', "mapping"=>6),
		array("name"=>'produit_garantie_uc', "mapping"=>7),
		array("name"=>'produit_garantie_ecran', "mapping"=>8),
		array("name"=>'produit_garantie_imprimante', "mapping"=>9),
		array("name"=>'produit_lan', "mapping"=>10),
		array("name"=>'produit_lecteur', "mapping"=>11),
		array("name"=>'produit_OS', "mapping"=>12),
		array("name"=>'produit_puissance', "mapping"=>13),
		array("name"=>'produit_ram', "mapping"=>14),
		array("name"=>'produit_technique', "mapping"=>15),
		array("name"=>'produit_type', "mapping"=>16),
		array("name"=>'produit_typeecran', "mapping"=>17),
		array("name"=>'produit_viewable', "mapping"=>18),
		array("name"=>'processeur', "mapping"=>19),
		array("name"=>'produit_format', "mapping"=>20),
		array("name"=>'id_fournisseur', "mapping"=>21),
		array("name"=>'id_produit', "mapping"=>22)
	);

	function __construct() {
		parent::__construct();
		$this->table = "produit";
		$this->colonnes['fields_column'] = array('produit.produit','produit.type','produit.ref','produit.id_fournisseur','produit.prix_achat'=>array("width"=>80,"rowEditor"=>"setInfos"),'produit.etat','produit.id_document_contrat');
		$this->colonnes['primary']=array('ref','produit','etat','commentaire','id_document_contrat');
		$this->colonnes['panel']['caracteristiques']=array('prix_achat','id_fabriquant','type','id_sous_categorie','id_fournisseur','obsolete');
		$this->colonnes['panel']['uc']=array('id_produit_besoins','id_produit_type','id_processeur','id_produit_puissance','id_produit_ram','id_produit_dd','id_produit_lecteur','id_produit_lan','id_produit_OS','id_produit_garantie_uc');
		$this->colonnes['panel']['ecran']=array('id_produit_typeecran','id_produit_viewable','id_produit_garantie_ecran');
		$this->colonnes['panel']['imprimante']=array('id_produit_technique','id_produit_format','id_produit_garantie_imprimante','id_produit_env');
		$this->colonnes['panel']['telephone']=array('id_produit_tel_produit','id_produit_tel_type');
		$this->colonnes['panel']['site_web']=array( 'loyer'=>array("width"=>80,"rowEditor"=>"setInfos"),
													'duree'=>array("width"=>80,"rowEditor"=>"setInfos"),
													'loyer1'=>array("width"=>80,"rowEditor"=>"setInfos"),
													'duree1'=>array("width"=>80,"rowEditor"=>"setInfos"),
													'loyer2'=>array("width"=>80,"rowEditor"=>"setInfos"),
													'duree2'=>array("width"=>80,"rowEditor"=>"setInfos"),
													'description', 'avis_expert', 'type_offre',
													'visible_sur_site'=>array("rowEditor"=>"ouinon","renderer"=>"etat","width"=>80),
													'services');

		$this->fk_ligne =  'ref,prix_achat,id_fournisseur,id_produit,code,produit,type';
		$this->autocomplete = array(
			"field"=>array("produit","ref")
			,"show"=>array("produit","ref")
			,"popup"=>array("produit","ref")
		);
		$this->colonnes["speed_insert"] = array('ref','id_fabriquant');
		$this->colonnes["speed_insert1"] = array(
			'produit'
			,'prix_achat'
			,'id_sous_categorie'
			,'type'
			,'id_fournisseur'
			,'obsolete'
			,'id_produit_type'
			,'id_processeur'
			,'id_produit_puissance'
			,'id_produit_ram'
			,'id_produit_dd'
			,'id_produit_lecteur'
			,'id_produit_lan'
			,'id_produit_OS'
			,'id_produit_garantie_uc'
			,'id_produit_typeecran'
			,'id_produit_viewable'
			,'id_produit_garantie_ecran'
			,'id_produit_technique'
			,'id_produit_format'
			,'id_produit_garantie_imprimante'
			,'id_produit_tel_produit'
			,'id_produit_tel_type'
			,'id_produit_besoins'
			,'id_produit_env'
			,'commentaire'
		);

		$this->colonnes['bloquees']['insert'] =
		$this->colonnes['bloquees']['cloner'] =
		$this->colonnes['bloquees']['update'] =  array('code','id_produit_dotpitch');


		$this->panels['caracteristiques'] = array('nbCols'=>2,'visible'=>true);
		$this->panels['site_web'] = array('nbCols'=>2,'visible'=>true);

		$this->fieldstructure();
		$this->foreign_key['id_fournisseur'] =  "societe";
		$this->foreign_key['id_produit_garantie_uc'] =  "produit_garantie";
		$this->foreign_key['id_produit_garantie_ecran'] =  "produit_garantie";
		$this->foreign_key['id_produit_garantie_imprimante'] =  "produit_garantie";

		$this->files["photo"] = array("type"=>"png","convert_from"=>array("jpg","png","gif"),"select"=>true);
		$this->files["photo1"] = array("type"=>"png","convert_from"=>array("jpg","png","gif"),"select"=>true);
		$this->files["photo2"] = array("type"=>"png","convert_from"=>array("jpg","png","gif"),"select"=>true);

		$this->addPrivilege("getInfosFromICECAT");
		$this->addPrivilege("setInfos","update");
		$this->addPrivilege("EtatUpdate");

		$this->onglets = array("produit_fournisseur_loyer");
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

	public function EtatUpdate($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL){

        $data["id_produit"] = $this->decryptId($infos["id_produit"]);
        $data[$infos["field"]] = $infos[$infos["field"]];

        if ($r=$this->u($data)) {
            ATF::$msg->addNotice(loc::mt(ATF::$usr->trans("notice_update_success")));
        }
        return $r;
    }

	/**
    * Surcharge de la méthode autocomplete pour faire apparaître les produits déjà insérés par l'utilisateur
    * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $infos
    * @return int  id si enregistrement ok
    */
	function autocomplete($infos) {

			// Récupérer les produits
			$this->q->reset()
				->addJointure("produit","id_fournisseur","societe","id_societe")
				->addJointure("produit","id_produit_dd","produit_dd","id_produit_dd")
				->addJointure("produit","id_produit_garantie_uc","produit_garantie","id_produit_garantie","produit_garantie_uc")
				->addJointure("produit","id_produit_garantie_ecran","produit_garantie","id_produit_garantie","produit_garantie_ecran")
				->addJointure("produit","id_produit_garantie_imprimante","produit_garantie","id_produit_garantie","produit_garantie_imprimante")
				->addJointure("produit","id_produit_lan","produit_lan","id_produit_lan")
				->addJointure("produit","id_produit_lecteur","produit_lecteur","id_produit_lecteur")
				->addJointure("produit","id_produit_OS","produit_OS","id_produit_OS")
				->addJointure("produit","id_produit_puissance","produit_puissance","id_produit_puissance")
				->addJointure("produit","id_produit_ram","produit_ram","id_produit_ram")
				->addJointure("produit","id_produit_technique","produit_technique","id_produit_technique")
				->addJointure("produit","id_produit_type","produit_type","id_produit_type")
				->addJointure("produit","id_produit_typeecran","produit_typeecran","id_produit_typeecran")
				->addJointure("produit","id_produit_viewable","produit_viewable","id_produit_viewable")
				->addJointure("produit","id_processeur","processeur","id_processeur")
				->addJointure("produit","id_produit_format","produit_format","id_produit_format")
				->addJointure("produit","id_fabriquant","fabriquant","id_fabriquant")
//				->addCondition("produit.produit","%".ATF::db($this->db)->real_escape_string($infos["query"])."%","OR","cle",'LIKE')
//				->addCondition("produit.ref","%".ATF::db($this->db)->real_escape_string($infos["query"])."%","OR","cle",'LIKE')
				->addField("produit.produit")
				->addField("produit.type")
				->addField("produit.ref","ref")
				->addField("societe.societe","fournisseur")
				->addField("produit.prix_achat","prix_achat")
				->addField("produit_dd.produit_dd","produit_dd")
				->addField("fabriquant.fabriquant","fabriquant")
				->addField("produit_garantie_uc.produit_garantie","produit_garantie_uc")
				->addField("produit_garantie_ecran.produit_garantie","produit_garantie_ecran")
				->addField("produit_garantie_imprimante.produit_garantie","produit_garantie_imprimante")
				->addField("produit_lan.produit_lan","produit_lan")
				->addField("produit_lecteur.produit_lecteur","produit_lecteur")
				->addField("produit_OS.produit_OS","produit_OS")
				->addField("produit_puissance.produit_puissance","produit_puissance")
				->addField("produit_ram.produit_ram","produit_ram")
				->addField("produit_technique.produit_technique","produit_technique")
				->addField("produit_type.produit_type","produit_type")
				->addField("produit_typeecran.produit_typeecran","produit_typeecran")
				->addField("produit_viewable.produit_viewable","produit_viewable")
				->addField("processeur.processeur","processeur")
				->addField("produit_format.produit_format","produit_format")
				->addField("produit.id_produit","id_produit")
				->addField("produit.id_fournisseur","id_fournisseur")
				->addCondition("produit.etat","actif")
				->addGroup("produit.ref");
		return parent::autocomplete($infos,false);
	}

//	/**
//    * Permet de regroupé les lignes de produits par provenance
//    * @author Quentin JANON <qjanon@absystech.fr>
//	* @param array $lignes
//    * @return int  id si enregistrement ok
//    */
//	function groupByProvenance($lignes) {
//		foreach ($lignes as $k=>$i) {
//			$return[$i['id_affaire_provenance']][] = $i;
//		}
//		return $return;
//	}

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
		$this->infoCollapse($infos);
		$this->q->reset()->addCondition("ref",$infos["ref"])->setCount();
		$count=$this->sa();

		$infos["url"] = util::mod_rewrite($infos["produit"]);

		if($count["count"]>0){
			throw new errorATF("Cette Ref existe déjà !",987);
		}
		return parent::insert($infos,$s,$files,$cadre_refreshed,$nolog);
	}

	public function update($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		parent::update($infos,$s,$files);
	}

	/**
	* On ne doit avoir que les produits actifs
	* @author mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @return une nouvelle fenêtre
	*/
	public function select_all($order_by="produit.etat",$asc='asc',$page=false,$count=false){
		return parent::select_all($order_by,$asc,$page,$count);
	}

	/**
	* Renvoil es infos du produits si trouvé sur ICECAT
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param array $infos contient la référence et l'id_fabriquant renseigné dans le formulaire
	* @return array Toutes les infos produits formatté pour le pré remplissage du formulaire.
	*/
	public function getInfosFromICECAT($infos){
		if (!$infos['ref'] || !$infos['id_fabriquant']) {
			throw new errorATF("Impossible d'interroger ICECAT sans la référence et le fabriquant.");
		}

		// Vérifier que la référence n'existe pas déjà dans le catalogue produit, si c'est le cas on renvoi un message d'erreur.

		// Récupération marque pour envoi a ICECAT
		$fabriquant = ATF::fabriquant()->nom($infos['id_fabriquant']);

		$d = ATF::produit()->getDataFromIcecat($fabriquant,$infos['ref']);

        $r['produit'] = $d['produit'];
        $r['url'] = $d['url'];

    	if ($d['dd']) {
    		$r['dd'] = $this->dealWith("produit_dd",$d['dd']);
    	}

    	if ($d['type_ecran']) {
    		$r['typeecran'] = $this->dealWith("produit_typeecran",$d['type_ecran']);
    	}

    	if ($d['taille_ecran']) {
    		$r['tailleecran'] = $this->dealWith("produit_viewable",$d['taille_ecran']);
    	}

    	if ($d['proc_modele']) {
    		$r['proc_modele'] = $this->dealWith("processeur",$d['proc_modele']);
    	}

    	if ($d['proc_puissance']) {
    		$r['proc_puissance'] = $this->dealWith("produit_puissance",$d['proc_puissance']);
    	}

    	if ($d['mem']) {
    		$r['mem'] = $this->dealWith("produit_ram",$d['mem']);
    	}

    	if ($d['lecteur'] && strtolower($d['lecteur'])!="non") {
    		$r['lecteur'] = $this->dealWith("produit_lecteur",$d['lecteur']);
    	}

    	if ($d['reseau']) {
    		$r['reseau'] = $this->dealWith("produit_lan",$d['reseau']);
    	}

    	if ($d['os']) {
    		$r['os'] = $this->dealWith("produit_OS",$d['os']);
    	}

    	if ($d['tech_impression']) {
    		$r['tech_impression'] = $this->dealWith("produit_technique",$d['tech_impression']);
    	}

    	if ($d['format_impression']) {
    		$r['format_impression'] = $this->dealWith("produit_format",$d['format_impression']);
    	}

    	if ($d['photo']) {
        	$fp1 = $this->filepath(ATF::$usr->get('id_user'),"photo",true);
        	util::file_put_contents($fp1,file_get_contents($d['photo']));
        	$r['photo'] = $d['photo'];
    	}
    	if ($d['photo1']) {
        	$fp2 = $this->filepath(ATF::$usr->get('id_user'),"photo1",true);
        	util::file_put_contents($fp2,file_get_contents($d['photo1']));
        	$r['photo1'] = $d['photo1'];
    	}
    	if ($d['photo2']) {
        	$fp3 = $this->filepath(ATF::$usr->get('id_user'),"photo2",true);
        	util::file_put_contents($fp3,file_get_contents($d['photo2']));
        	$r['photo2'] = $d['photo2'];
    	}

    	return $r;
	}

	/**
	* Traite les différents type de données renvoyées par Icecat
    * @author Quentin JANON <qjanon@absystech.fr>
    * @param string $nom libellé
	*/
	public function dealWith($type,$nom) {
		ATF::_s("preselected_".$type,NULL);
		ATF::$type()->q->reset()->where($type,"%".str_replace("'"," ",$nom)."%","OR",false,"LIKE");
		$res = ATF::$type()->select_all();

		if (!$res) {
			$id = ATF::$type()->i(array($type=>$nom));
			$r[] = array("id_".$type=>$id,"libelle"=>$nom);
		} else {
			ATF::_s("preselected_".$type,$res);
			foreach ($res as $k=>$i) {
				$r[] = array("id_".$type=>$i['id_'.$type],"libelle"=>$nom);
			}
		}
		return $r;
	}

	/**
	* Méthode permettant de recuperer les infos icecat de compléter avant l'insert
    * Infos récupéré : description, shortDescription, poids et les images
    * @author Antoine MAITRE <amaitre@absystech.fr>
    * @author Quentin JANON <qjanon@absystech.fr>
    * @param array $infos tableau représentant l'insert
	* @return bool retourne true si tout se passe bien et false en cas d echec
	*/

	public function getDataFromIcecat($marque,$ref) {

        // URL ICE CAT VERS LA FICHE TECHNIQUE DU PRODUIT
		$urls = "http://prf.icecat.biz/?shopname=toto;smi=product;vendor=".urlencode($marque).';prod_id='.urlencode($ref).";lang=fr";
		$r['url'] = $urls;

		$ch = curl_init($urls);
		curl_setopt($ch, CURLOPT_COOKIEFILE, realpath('cookie.txt'));
//		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$html = curl_exec($ch);
		$html = mb_convert_encoding($html, "UTF-8");
		curl_close($ch);



        // Pour éviter les caractère pourries qui empêche la bonne résolution des Regex
        $html = str_replace("`","'",$html);
        // La fiche technique est inaccessible : Produit obsolète ou alors la REF n'est pas bonn, ou bien la marque :/
		if (preg_match('#Sorry, for this product no additional product information is found#', $html) || !$html) {
            throw new errorATF(ATF::$usr->trans("Produit introuvable sur ICECAT !"),910);
        }

		$html = mb_convert_encoding($html, 'HTML-ENTITIES', "UTF-8");



        // On récupère l'id icecat pour pouvoir appeler le XML qui est plus facile a parser que le HTML
        preg_match('#<input type="hidden" name="productId"\s+value="(.*?)" >#si', $html, $pregResult);


        if ($idIceCat = $pregResult[1]) {
            $url = "http://openIcecat-xml:freeaccess@data.icecat.biz/export/freexml.int/FR/".$idIceCat.".xml";

			header('Content-Type: text/xml');
            $xml = file_get_contents($url);



            $dom = new DOMDocument();
            $dom->loadXML($xml);
            //$dom->load("http://openIcecat-xml:freeaccess@data.icecat.biz/export/freexml.int/FR/435466.xml"); // Fiche avec tout ce qui faut comme infos
            //$dom->load("http://openIcecat-xml:freeaccess@data.icecat.biz/export/freexml.int/FR/".$idIceCat.".xml");
            $xpath = new Domxpath($dom);


            // libellé
            $q = $xpath->query("//Product");
            if ($q->item(0)) $r['produit'] = $q->item(0)->getAttribute("Title");

            // Sous catégorie == Type de produit
            //$q = $xpath->query("//Feature/Name[@Value='Type de produit']");
            //if ($q->item(0)) $r['sous_categorie'] = $q->item(0)->parentNode->parentNode->getAttribute("Presentation_Value");

            // processeur
            $q = $xpath->query("//Feature/Name[@Value='Modèle de processeur']");
            if ($q->item(0)) $r['proc_modele'] = $q->item(0)->parentNode->parentNode->getAttribute("Presentation_Value");

            $q = $xpath->query("//Feature/Name[@Value='Vitesse du processeur']");
            $q2 = $xpath->query("//Feature/Name[@Value='Nombre de coeurs de processeurs']");
            if ($q->item(0) && $q2->item(0)) $r['proc_puissance'] = $q2->item(0)->parentNode->parentNode->getAttribute("Presentation_Value")." x ".$q->item(0)->parentNode->parentNode->getAttribute("Presentation_Value");

            // Mémoire
            $q = $xpath->query("//Feature/Name[@Value='Mémoire interne']");
            if ($q->item(0)) $r['mem'] = $q->item(0)->parentNode->parentNode->getAttribute("Presentation_Value");

            // DD
            $q = $xpath->query("//Feature/Name[@Value='Quantité de disques durs installés']");
            $q2 = $xpath->query("//Feature/Name[@Value='Capacité disque dur']");
            if ($q->item(0) && $q2->item(0))$r['dd'] = $q->item(0)->parentNode->parentNode->getAttribute("Presentation_Value")." x ".$q2->item(0)->parentNode->parentNode->getAttribute("Presentation_Value");

            // Lecteur
            $q = $xpath->query("//Feature/Name[@Value='Type de lecteur optique']");
            if ($q->item(0)) $r['lecteur'] = $q->item(0)->parentNode->parentNode->getAttribute("Presentation_Value");

            // Reseau
            $q = $xpath->query("//Feature/Name[@Value='LAN Ethernet : taux de transfert des données']");
            if ($q->item(0)) $r['reseau'] = $q->item(0)->parentNode->parentNode->getAttribute("Presentation_Value");

            // OS
            $q = $xpath->query("//Name[@Value=\"Système d'exploitation installé\"]");
            if ($q->item(0)) $r['os'] = $q->item(0)->parentNode->parentNode->getAttribute("Presentation_Value");

            // Type de l'écran
            $q = $xpath->query("//Feature/Name[@Value='Écran']");
            if ($q->item(0)) $r['type_ecran'] = $q->item(0)->parentNode->parentNode->getAttribute("Presentation_Value");

            // Taille de l'écran
            $q = $xpath->query("//Feature/Name[@Value=\"Taille de l'écran\"]");
            if ($q->item(0)) $r['taille_ecran'] = $q->item(0)->parentNode->parentNode->getAttribute("Presentation_Value");

            // Technologie d'impression
            $q = $xpath->query("//Feature/Name[@Value=\"Technologie d'impression\"]");
            if ($q->item(0)) $r['tech_impression'] = $q->item(0)->parentNode->parentNode->getAttribute("Presentation_Value");

            // ISO, Format Series A (A0...A9)
            $q = $xpath->query("//Feature/Name[@Value=\"ISO, Format Series A (A0...A9)\"]");
            if ($q->item(0)) $r['format_impression'] = $q->item(0)->parentNode->parentNode->getAttribute("Presentation_Value");

            // Image 1, 2 et 3
            $q = $xpath->query("//ProductGallery/ProductPicture");



            if ($q->item(0)) {
            	$r['photo'] = $q->item(0)->getAttribute("Original");
            }
            if ($q->item(1)) {
            	$r['photo1'] = $q->item(1)->getAttribute("Original");
            }
            if ($q->item(2)) {
            	$r['photo2'] = $q->item(2)->getAttribute("Original");
            }
        }

		return $r;
	}

	/**
	* Méthode permettant de recuperer l'image en base64 du produit
    * @author Morgan FLEURUQUIN <mfleurquin@absystech.fr>
    * @param array $get  tableau des parametre get envoyé par le site avec l'id du produit
    * @param array $post
	* @return string le base64 du fichier
	*/
	public function _getBase($get, $post){
		$path = ATF::produit()->filepath($get['id'],"photo");
		$data = file_get_contents($path);
	  	return base64_encode($data);
	}


/** Fonction qui génère les résultat pour les champs d'auto complétion
	* @author Cyril CHARLIER <ccharlier@absystech.fr>
	*/
	public function _ac($get,$post) {
		$length = 25;
		$start = 0;

		$this->q->reset();

		// On ajoute les champs utiles pour l'autocomplete
		$this->q->addField("id_produit")->addField("ref")->addField("produit")->where("id_sous_categorie",$get['id']);

		if ($get['q']) {
			$this->q->setSearch($get["q"]);
		}
		$this->q->setLimit($length,$start)->setPage($start/$length);

		return $this->select_all();
	}

	/**
	* Retour tous les packs où l'on trouve le produit
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
	public function getPacks($id_produit) {
		ATF::pack_produit_ligne()->q->reset()
			->addField('id_pack_produit')
			->where('id_produit', $id_produit);
		return ATF::pack_produit_ligne()->sa();
	}



};

class produit_cleodisbe extends produit_cleodis { };

class produit_cap extends produit {
	// Mapping prévu pour un autocomplete sur produit
	public static $autocompleteMapping = array(
		array("name"=>'produit', "mapping"=>0),
		array("name"=>'ref', "mapping"=>1),
		array("name"=>'prix_achat', "mapping"=>2),
		array("name"=>'id', "mapping"=>3)
	);

	function __construct() {
		parent::__construct();
		$this->table = "produit";
		$this->colonnes['fields_column'] = array('produit.produit','produit.ref','produit.prix_achat','produit.etat');
		$this->colonnes['primary']=array('ref','produit','commentaire');
		$this->colonnes['panel']['caracteristiques']=array('prix_achat');
		$this->colonnes['panel']['site_web']=array('description', 'avis_expert', 'type_offre','visible_sur_site',"prix");


		$this->autocomplete = array(
			"field"=>array("produit","ref")
			,"show"=>array("produit","ref")
			,"popup"=>array("produit","ref")
		);
		$this->colonnes["speed_insert"] = array('ref');
		$this->colonnes["speed_insert1"] = array(
			 'produit'
			,'prix_achat'
			,'commentaire'
			,"prix"
		);

		$this->panels['caracteristiques'] = array('nbCols'=>2,'visible'=>true);
		$this->panels['site_web'] = array('nbCols'=>2,'visible'=>true);

		$this->fieldstructure();
		$this->files["photo"] = array("type"=>"png","convert_from"=>array("jpg","png","gif"),"select"=>true);
	}


	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		$this->infoCollapse($infos);
		$this->q->reset()->addCondition("ref",$infos["ref"])->setCount();
		$count=$this->sa();

		$infos["url"] = util::mod_rewrite($infos["produit"]);

		if($count["count"]>0) throw new errorATF("Cette Ref existe déjà !",987);

		return parent::insert($infos,$s,$files,$cadre_refreshed,$nolog);
	}

	/**
    * Surcharge de la méthode autocomplete pour faire apparaître les produits déjà insérés par l'utilisateur
    * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $infos
    * @return int  id si enregistrement ok
    */
	function autocomplete($infos) {
		// Récupérer les produits
		$this->q->reset()
			->addField("produit.produit")
			->addField("produit.ref","ref")
			->addField("produit.prix_achat","prix_achat")
			->addField("produit.id_produit","id")
			->addField("produit.id_produit","id_produit_fk");
		return parent::autocomplete($infos,false);
	}

	public function speed_insert_template(&$infos){
		if($infos["id_produit"] && $infos["id_produit"]!="undefined"){
			$produit=$this->select($infos["id_produit"]);
			foreach($produit as $key=>$item){
				ATF::_r($key,$item);
			}
		}
		return parent::speed_insert_template($infos);
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
		return $result;
	}


};

class produit_exactitude extends produit {

	// Mapping prévu pour un autocomplete sur produit
	public static $autocompleteMapping = array(
		array("name"=>'produit', "mapping"=>0),
		array("name"=>'ref', "mapping"=>1),
		array("name"=>'prix_achat', "mapping"=>2),
		array("name"=>'prix', "mapping"=>3),
		array("name"=>'id', "mapping"=>4)
	);

	function __construct() {
		parent::__construct();
		$this->table = "produit";
		$this->colonnes['fields_column'] = array('produit.produit','produit.ref','produit.prix_achat','produit.etat');
		$this->colonnes['primary']=array('ref','produit','commentaire');
		$this->colonnes['panel']['caracteristiques']=array('prix_achat');
		$this->colonnes['panel']['site_web']=array('description', 'avis_expert', 'type_offre','visible_sur_site',"prix");


		$this->autocomplete = array(
			"field"=>array("produit","ref")
			,"show"=>array("produit","ref")
			,"popup"=>array("produit","ref")
		);
		$this->colonnes["speed_insert"] = array('ref');
		$this->colonnes["speed_insert1"] = array(
			 'produit'
			,'prix_achat'
			,'commentaire'
			,"prix"
		);

		$this->panels['caracteristiques'] = array('nbCols'=>2,'visible'=>true);
		$this->panels['site_web'] = array('nbCols'=>2,'visible'=>true);

		$this->fieldstructure();
		$this->files["photo"] = array("type"=>"png","convert_from"=>array("jpg","png","gif"),"select"=>true);
	}

	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		$this->infoCollapse($infos);
		$this->q->reset()->addCondition("ref",$infos["ref"])->setCount();
		$count=$this->sa();

		$infos["url"] = util::mod_rewrite($infos["produit"]);

		if($count["count"]>0) throw new errorATF("Cette Ref existe déjà !",987);
		return parent::insert($infos,$s,$files,$cadre_refreshed,$nolog);
	}

	/**
    * Surcharge de la méthode autocomplete pour faire apparaître les produits déjà insérés par l'utilisateur
    * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $infos
    * @return int  id si enregistrement ok
    */
	function autocomplete($infos) {
		// Récupérer les produits
		$this->q->reset()
			->addField("produit.produit")
			->addField("produit.ref","ref")
			->addField("produit.prix_achat","prix_achat")
			->addField("produit.prix","prix")
			->addField("produit.id_produit","id")
			->addField("produit.id_produit","id_produit_fk");
		return parent::autocomplete($infos,false);
	}

	public function speed_insert_template(&$infos){
		if($infos["id_produit"] && $infos["id_produit"]!="undefined"){
			$produit=$this->select($infos["id_produit"]);
			foreach($produit as $key=>$item){
				ATF::_r($key,$item);
			}
		}
		return parent::speed_insert_template($infos);
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
		return $result;
	}




};
?>