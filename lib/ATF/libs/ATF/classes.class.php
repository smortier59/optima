<?php
/**
 * Classe classes
 * Cet objet permet les traitements en base de données
 * C'est un singleton, un objet est utilisé par table et permet tout les traitements de celle-ci
 *
 * @date 2009-11-01
 * @package ATF
 * @version 5
 * @author Yann GAUTHERON <ygautheron@absystech.fr>
 */
class classes {
	/*--------------------------------------------------------------*/
	/*                   Deprecated ATF4                            */
	/*--------------------------------------------------------------*/
	/*
	 Liste de tous les attributs possibles [ATF 4]:

	 $this->color["etat"]["sent"] = "#AAAAAA";											Les lignes contenant la valeur 'sent' pour la colonne 'etat' seront écrites en "#AAAAAA"


	 $this->stats_types = array("CA","marge");											Types de stats proposées dans les statistiques pour ce module
	 $this->upload = array("fichier");														Ajouter un ou plusieurs champs supplémentaires d'upload de fichier,
	 prévu pour ne pas être stockés dans la base de données et traités dans le .php
	 $this->view=array("suffix"=>array("horaire_fin"=>"j."));							attribuer des modifications (order,align,suffix,prefix) à la vue (ex: rajouter un 'j.' après la donnée dans la colonne 'horaire_fin')
	 $this->import = false; 																	Proposer l'importation possible

	 $this->no_insert = true;																	Non affichage des icones insert
	 $this->no_update = true;																Non affichage des icones update
	 $this->no_select = true;																Non affichage des icones select
	 $this->fieldProcess = array("commentaire"=>"aes_encrypt");			Process spécial pour un champs 	de la base de données

	 Jointures :
	 $this->jointure[] = array($this->table,"id_emailing_liste_contact","emailing_liste_contact","id_emailing_liste_contact","seulement_ce_champ","qui_aura_ce_nom");

	 $this->listeJointure['domaine']="societe_domaine";  		on montre que pour joindre la table domaine, on passe par une table de jointure qui est societe_domaine, si on créé un filtre dans le module société

	 Conditions obligatoires pour chaque requêtes de cette table :
	 $this->order		=	array("field"=>"asc","field2"=>"desc");
	 $this->group		=	array("field");
	 $this->where		=	array("condition1"=>"table.field='string'");

	 FCK Editor :
	 $this->FCKEditor["ToolbarSets"] = "Speedmail";
	 $this->FCKEditor["EditorAreaCSS"] = "plugins/speedmail/fck_editorarea.css.php";		// Forcer un autre fichier CSS que celui par défaut
	 $this->FCKEditor["enabled"]["corps"]=true;

	 CHAMP NOM : Par défaut, le champ nom est le nom de la table, mais il est possible de personaliser
	 $this->field_nom = "civilite,nom,prenom"

	 @todo : voire pour rendre cela automatique via les infos fournies par InnoDB
	 DEFINIR LES CLE ETRANGERES :
	 $this->foreign_key["id_contact_facturation"] = "contact";
	 $this->foreign_key["id_contact_commercial"] = "contact";
	 $this->foreign_key["id_filiale"] = "societe";

	 BANDEAU SUR LEFT.TPL
	 $this->shortcut = array('shortcut'=>'accueil_shortcut','help','release'); 						//item pour le nom du tpl à appeller


	 TRACABILITE
	 ATF::tracabilite()->no_trace[$this->table]=1;       //si vous ne souhaitez pas que le module soit tracé

	 // Icones a afficher en illustration : key, values ou all
	 public $icons = array(
		"id_centre" => "key"
		);

		*/

	/**
	 * Table
	 * @var string
	 */
	public $table = NULL;

	/**
	 * Namespace
	 * @var string
	 */
	public $namespace = NULL;

	/*
	 * Requêteur permettant le filtrage, la pagination etc...
	 * @var querier
	 */
	public $q;

	/**
	 * Base de donnée à utiliser pour les requêtes
	 * Si la velur est egale a NULL, prend comme base de données par défaut la base "optima_".ATF::$codename
	 * @var mysql
	 */
	public $db = NULL;

	/**
	 * Cache permettant de stocker en cache des résultats appelée une myriade de fois, évite les connections à mysql inutiles
	 * @var mixte
	 */
	protected $cache;

	/**
	 * Description des champs existants dans la base de données
	 * @var mixte
	 */
	public $desc;

	/**
	 * Cache jamais vidée
	 * @var mixte
	 */
	protected $persistent_cache;

	/**
	 * Flag définissant si oui ou non il faut mettre en cache les select sur ce singleton
	 * @var bool
	 */
	protected $memory_optimisation_select = false;

	/**
	 * Relations entre les tables
	 * @var mixte
	 */
	public $foreign_key;

	/**
	 * Les droits d'un module gèrent les droit de ce module
	 * @var mixte
	 */
	public $controlled_by;

	/**
	 * Tableau des valeurs des attributs de l'objet (utilisation en mode objet et non singleton)
	 * @var array
	 */
	public $infos = array();

	/**
	 * Témoin d'état de singleton ou non
	 * un objet héritant de 'classes' peut tout aussi bien servir de simple singleton que d'un objet réel à part entière :
	 * - LE SINGLETON ($this->singleton === true) est une interface simplifiée entre une table de SGBD et sa gestion métier applicative
	 * - UN OBJET ($this->singleton === false) est une interface simplifiée entre UN ENREGISTREMENT SEULEMENT d'une table d'un SGBD et sa gestion métier applicative
	 * @var bool
	 */
	public $singleton = true;

	/**
	 * Onglets satellites dans page de sélection
	 * @example $this->onglets = array('affaire','contact');
	 * @param string table Table à utiliser pour cet onglet
	 * 	@example $this->onglets = array('suivi'=>array('table'=>'suivi_contact'));
	 * @param string field Champ à utiliser pour la jointure à cet onglet
	 * 	@example $this->onglets = array('suivi'=>array('field'=>'autre_table.id_autre_champ'));
	 * @param boolean opened TRUE pour que l'onglet soit ouvert par défaut
	 * 	@example $this->onglets = array('suivi'=>array('opened'=>true));
	 * @var mixte
	 */
	public $onglet = NULL;

	/**
	 * Paramètres d'affichage des colonnes du module, dans le select all, le select et les insert/update.
	 * @example Select All : $this->colonnes['fields_column']
	 * $this->colonnes['fields_column'] = array('societe.id_societe'=>array(
	 *	'alias'=>'societe_mere'   // Alias forcé
	 *	,'custom'=>true   // Ne doit pas être ajouté comme les autres aux champs demandés à la base de données, mais sera utilisé dans l'affichage des colonnes côté présentation
	 *	,'nosort'=>true   // La colonne ne doit pas être triable
	 *	,'nosearch'=>true   // Ne pas utiliser cette recherche dans la colonne
	 *	,'nolink'=>true   // Les enregistrements ne doivent pas avoir de lien sur cette colonne
	 *	,'truncate'=>false   // Ne rien tronquer du tout
	 *	,'truncate'=>50   // Nombre de caractères limite, tronquer ce qui dépasse
	 *	, "aggregate"=>array("avg","min","max","sum","stddev","variance") // Aggrégats demandés (Group by)
	 *	, "prefix"=>"n°"
	 *	, "suffix"=>"€"
	 *	, "align"=>"right" // Alignement ds la colonnes (right,center,left,justify)
	 *	, "type"=>"decimal"
	 *	,'EnumTranslate'=>true   // Traduire automatiquement en SQL les types énumérés
	 * ));
	 * @example Créer un cadre specifique dans la fiche intitulé '__LIBELLE__' et ne contenant que les champs indiqués
	 *	$this->colonnes['panel'][__LIBELLE__] = array('id_societe')
	 *	$this->panels['coordonnees'] = array( // Attributs spéciaux des panels
	 *		"visible"=>true // Si on veut que le panel soit visible par défaut
	 *	);
	 * @example PRIMARY : Permet de déterminer les champs les plus importants qui vont se mettre dans le cadre principale de la fiche
	 * 	$this->colonnes['primary'] = array('societe.field');
	 * @example Ne pas prendre en compte ces champs pour la __TARGET__ voulue. __TARGET__ peut prendre les valeurs 'insert','update','recherche','select' et 'filtre'
	 *	$this->colonnes['bloquees'][__TARGET__] =  array('id_societe')
	 * @example Tableau des colonnes pour afficher le listing dans les panels de la page d'accueil
	 *	$this->colonnes['accueil'] =  array('id_societe')
	 * @example insert - Colonnes bloquées insert :
	 *	$this->colonnes['bloquees']['insert']
	 * @example update - Colonnes bloquées update :
	 *	$this->colonnes['bloquees']['update']
	 * @example select - Colonnes bloquées select :
	 *	$this->colonnes['bloquees']['select']
	 * @example recherche - Colonnes bloquées recherche :
	 *	$this->colonnes['bloquees']['recherche']
	 * @example export - Colonnes bloquées export :
	 *	$this->colonnes['bloquees']['export']
	 * @example filtre - Colonnes bloquées filtre :
	 *	$this->colonnes['bloquees']['filtre']
	 * @var mixte
	 */
	public $colonnes;

	/**
	 * Permet l'envoi de mail depuis le select d'un module avec possibilité d'y joindre des fichiers en ajoutant "quickMail"=>true dans le $this->files du constructeur
	 * Ex : $this->files["fichier_joint"] = array("type"=>"pdf","preview"=>true,"quickMail"=>true);
	 */
	public $quickMail = false;

	/**
	 * Redirection par défaut lors d'un insert/update/delete/clone.
	 * Il existe 4 types de redirection :
	 *      - select : retourne sur la fiche select (impossible pour le delete)
	 *      - parent : retourne sur la fiche parente (parcequ'on est dans un onglet) si pas de parent c'est la fiche select (impossible pour le delete)
	 *      - select_all : retourne sur le listing de l'élément
	 *      - Méthode setDefaultRedirection()
	 * @var array
	 */
	protected $defaultRedirect=array("insert"=>"parent"
	,"update"=>"parent"
	,"cloner"=>"parent"
	,"delete"=>"select_all");

	/**
	 * Mapping des droits spécifiques, les appels ajax doivent obligatoirement avoir un mapping associé à un privilege existant, sinon ils seront DENIED
	 * @var array
	 */
	private $eventPrivilegeMap = array(
		"tpl2div" => "select"
		,"export_total" => "export"
		,"export_brut" => "export"
		,"export_vue" => "export"
		,"upload"=>"insert" //upload de fichier
		,"updateSelectAll" => "select" // Mise à jour du div de listing (pas update sql...)
		,"extJSgsa"=>"select" // Listing par extJS grid
		,"geocode"=>"geolocalisation"
		,"upload_fichier"=>"insert"
		,"speed_insert"=>"update"
		,"speed_insert_modalbox"=>"update"
		,"speed_insert_template"=>"update"
		,"quick_mail_template"=>"update"
		,"quick_mail"=>"update"
		,"autocomplete"=>"select"
		,"insert_ligne"=>"insert"
		,"update_ligne"=>"update"
		,"delete_ligne"=>"delete"
		,"gmap"=>"geolocalisation"
		,"maj"=>"update"
		,"Facture"=>"select"
		,"global_search"=>"select"
		,"modification"=>"update"
		,"refresh_column"=>"filter_insert"
		,"save_onglet"=>"select"
		,"saveOuvertureOnglet"=>"select"
		,"saveOuverturePanel"=>"select"
		,"export_stats"=>"export"
		,"sendPermalink"=>"select"
		,"genererPdf"=>"select"
		,"uploadExt"=>"insert"
		,"updateViewFilter"=>"select"
		,"delete_uploadExt"=>"update"
	);

	/**
	 * Drapeaux de non affichage forcé d'évênements
	 * Non affichage des icones insert
	 * @var bool
	 */
	public $no_insert = false;

	/**
	 * Drapeaux de non affichage forcé d'évênements
	 * Non affichage des icones update
	 * @var bool
	 */
	public $no_update = false;

	/**
	 * Drapeaux de non affichage forcé d'évênements
	 * Non affichage des icones update massif
	 * @var bool
	 */
	public $no_update_all = true;

	/**
	 * Drapeaux de non affichage forcé d'évênements
	 * Non affichage des icones d'impression
	 * @var bool
	 */
	//public $no_print = true;

	/**
	 * Constructeur de la classe mère
	 * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	 * @param string|int $table_or_id Table forcée à laqulle doit se rapporter ce singleton OU clé primaire d'enregistrement pour un objet
	 * @return void
	 */
	public function __construct($table_or_id=NULL) {
		if (!$this->table) {
			$this->table = get_class($this);
		}

		/* Initialisation du Requêteur */
		$this->q = new querier();

		if ($table_or_id!==NULL) {
			if (is_numeric($table_or_id) || strlen($table_or_id)===32 /* MD5 */) {
				// Création d'un objet
				$this->singleton = false;
				//log::logger("DESC id ".$this->table,'ygautheron');
				$this->desc = ATF::getClass($this->table)->desc;
				$this->maj_infos($table_or_id);
			} elseif (is_string($table_or_id)) {
				// Définition de la table forcée
				$this->table = $table_or_id;

				if (!$this->desc) {
					//log::logger("DESC ".$this->table,'ygautheron');
					// Structure des champs bruts récupérés par la base
					//					if (ATF::getClass($this->table)->desc) {
					//						$this->desc = ATF::getClass($this->table)->desc;
					//					} else {
					$this->desc = ATF::db($this->db)->desc($this->table);
					//					}
				}
			}
		}

		//		// Beurk beurk la session ! @todo mettre la session en argument (&s)
		//		if (isset($this->table)) {
		//			if (isset(ATF::$usr) && isset(ATF::$usr->custom) && isset(ATF::$usr->custom[$this->table])){
		//				$this->colonnes['fields_column'] = ATF::$usr->custom[$this->table];
		//			} elseif ($this->field_nom) {
		//				$this->colonnes['fields_column'] = explode(",",$this->field_nom);
		//			} else {
		//				$this->colonnes['fields_column'] = array($this->table);
		//			}
		//		}

		// Aspect spécifique
		// Gestion d'un after spécifique sur le constructeur
		// Il suffit de mettre une méthode spécifique public static afterConstruct dans la motherClass
		$motherClassName=ATF::getDefined('motherClassName');
		if(class_exists($motherClassName)){
			$class=new ReflectionClass($motherClassName);
			if($class->hasMethod('afterConstruct')){
				$refl = $class->getMethod('afterConstruct');
				if($refl->isStatic()){
					$motherClassName::afterConstruct($this);
				}
			}
		}
	}

	/**
	 * Synchronise les infos d'un enregistrement par rapport au contenu récent de la base de donnée
	 * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	 * @param int $id Clé primaire de l'enregistrement à utiliser désormais, si NULL on met à jour l'enregistrement courant
	 * @return array Infos à updater
	 */
	public function maj_infos($id=NULL) {
		if ($this->isSingleton()) {
			throw new errorATF("La méthode classes::maj_infos() ne peut être utilisée sur un objet de type singleton !",101);
		} else {
			if (!$id) {
				// Sans ce paramètre, on considère une mise à jour simple et non un remplacement des données
				$id = $this->infos["id_".$this->table];
				unset($this->cache);
			}

			// Mise à jours des infos basiques
			$res = $this->select($id);
			if (is_array($this->infos)) {
				$this->infos = array_merge($this->infos,$res);
			} else {
				$this->infos = $res;
			}
		}
	}

	/**
	 * Retourne l'état de cet objet, s'il est né singleton ou objet
	 * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	 * @return boolean
	 */
	public function isSingleton() {
		return $this->singleton;
	}

	/**
	 * Déclenche une erreur s'il est singleton
	 * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	 * @return void
	 */
	public function notSingleton() {
		if ($this->isSingleton()) {
			throw new errorATF("appel_a_une_methode_ne_devant_pas_etre_un_singleton_mais_un_objet",59);
		}
	}

	/**
	 * Défini la base de données que ce singleton doit attaquer
	 * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	 * @param string $db
	 * @return void
	 */
	public function setDB($db) {
		$this->db = $db;
	}

	/**
	 * Retourne une instance de l'objet
	 * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	 * @param integer $id
	 * @return object
	 */
	public function newObject($id) {
		$o = $this->name();
		return new $o($id);
	}

	/**
	 * Retourne l'id existant provenant d'un cryptage
	 * @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	 * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	 * @param int $id
	 * @return int
	 */
	public function decryptId($id,$seed=NULL) {
		if (strlen($id)===32){
			if ($seed) {
				$aes_tmp=new aes();
				$aes_tmp->setSeed($seed);
				$id = $aes_tmp->decrypt($id);
				$aes_tmp->endCrypt();
			} elseif(is_object(ATF::getUser()) && is_object(ATF::getUser()->getAES()) ){
				$id = ATF::getUser()->getAES()->decrypt($id);
			}else{
				throw new errorATF('aes_not_exist');
			}
			if (!is_numeric($id)) {
				throw new errorATF("id_corrupted ".rawurlencode($id),3972);
			}
		}
		return $id;
	}

	/**
	 * Cryptage d'un id
	 * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	 * @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	 * @param int $id
	 * @return int
	 */
	public static function cryptId($id) {
		if (!is_numeric($id)) {
			return $id;
		} else {
			if(is_object(ATF::getUser()) && is_object(ATF::getUser()->getAES()) ){
				return ATF::getUser()->getAES()->crypt($id);
			}else{
				throw new errorATF('aes_not_exist');
			}
		}
	}

	/**
	 * Retourne l'enregistrement demandé, ou seulement le ou les champs sélectionnés séparés par virgule
	 * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	 * @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	 * @param int $id
	 * @param string $field (exemple : nom,prenom,id_user)
	 * @return int|array
	 */
	public function select($id,$field=NULL,$seed=NULL) {
		if(is_array($id)){
			// Si $id est un array, on s'attend à ces deux cases définies
			// Utile via $_POST ajax par exemple...
			$field=$id['field'];
			$id=$id['id'];
		}

		if (!$id) {
//			$e = new errorATF(ATF::$usr->trans("id_manquant")." : ".$this->table,404);
//			$e->setLog();
			return false;
		}

		// Décryptage avec une seed
		if($seed){
			$aes_tmp=new aes();
			$aes_tmp->setSeed($seed);
			$id = $aes_tmp->decrypt($id);
			$aes_tmp->endCrypt();
		}else{
			$id = $this->decryptId($id);
		}

		if ($this->memory_optimisation_select && array_key_exists($id,$this->cache[__FUNCTION__][$field])) {
			return $this->cache[__FUNCTION__][$field][$id];
		} else {
			$this->q
			->reset()
			->setStrict()
			->addCondition($this->table.".id_".$this->table,$id);

			$this->q->setDimension("row");
			if ($field) {
				$this->q->addField($field);
				if (count(explode(",",$field))===1) {
					$this->q->setDimension("cell");
				}
			}

			$return = self::select_all();
			if ($this->memory_optimisation_select) {
				$this->cache[__FUNCTION__][$field][$id] = $return;
			}
		}
		//ATF::$cr->rm('top');
		return $return;
	}

	/**
	 * Retourne le champ par défaut pour le libelle
	 * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	 * @param int $id
	 * @return string
	 */
	public function nom($id) {
		if ($this->memory_optimisation_select && isset($this->cache[__FUNCTION__][$id])) {
			return $this->cache[__FUNCTION__][$id];
		} else {
			if (!$id) return;
			$id = $this->decryptId($id); // On sait jamais s'il s'agit d'un md5

			// Si dans la liste des champs, il n'existe pas de champ du nom de la table (charte de nommage ATF conventionnelle), on retourne juste l'ID
			if (!$this->field_nom && is_array($this->desc) && !$this->desc[$this->table]) {
				return $id;
			}

			$this->q
			->reset()
			->addCondition($this->table.".id_".$this->table,$id)
			->setDimension("cell")
			->addField($this->table.".id_".$this->table);
			$nom = $this->select_all();

			if ($this->memory_optimisation_select) {
				$this->cache[__FUNCTION__][$id] = $nom;
				return $this->cache[__FUNCTION__][$id];
			} else {
				return $nom;
			}
		}
	}

	/**
	 * Permet de filtrer les données à afficher dans la liste
	 * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	 * @param string fields le champs à filtrer
	 * @param string value la valeur attribuée au champs
	 * @param string $order_by Forcer un ordre de tri
	 * @param string $asc Forcer un sens de tri (asc ou desc), desc par defaut
	 * @return array
	 */
	public function options_special($field,$value,$order_by=false,$asc='desc'){
		$this->q->reset()->addCondition($field,$value);
		return $this->options(NULL,NULL,false,$order_by,$asc);
	}

	/**
	 * Retourne un tableau sépficiquement formaté pour la mise à jour d'un <select> HTML par le framework ATF javascript
	 * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	 * @param string $selectedValue Valeur qui doit être sélectionnée
	 * @param string $fields Champ à afficher uniquement dans le menu déroulant, peut être séparé par virgules si plusieurs champs
	 Si aucun renseigné, il se peut que la variable $field_nom renseigné dans la classe soit prise en compte.
	 Si rien n'est renseigné,a lors le nom du champ du même nom que la table est pris par défaut.
	 * @param string $id_on_key Champ à utiliser pour la clé du tableau retourné
	 * @param string $reset Reset le requeteur par défaut mais peut être désactivé avec FALSE
	 * @param string $order_by Forcer un ordre de tri
	 * @param string $asc Forcer un sens de tri (asc ou desc), desc par defaut
	 * @return array
	 */
	public function htmlOptions($selectedValue=NULL,$fields=NULL,$id_on_key=NULL,$reset=true,$order_by=false,$asc='desc'){
		foreach ($this->options($fields,$id_on_key,$reset,$order_by,$asc) as $k => $i) {
			$option = array(
			"text" => $i
			, "value" => $k
			);
			if ($selectedValue==$k) {
				$option["selected"]=true;
			}
			$options[]=$option;
		}
		return $options;
	}

	/**
	 * Retourne uniquement les informations nécessaires à la création d'un menu déroulant standard
	 * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	 * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	 * @param string $fields Champ à afficher uniquement dans le menu déroulant, peut être séparé par virgules si plusieurs champs
	 Si aucun renseigné, il se peut que la variable $field_nom renseigné dans la classe soit prise en compte.
	 Si rien n'est renseigné,a lors le nom du champ du même nom que la table est pris par défaut.
	 * @param string $id_on_key Champ à utiliser pour la clé du tableau retourné
	 * @param string $reset Reset le requeteur par défaut mais peut être désactivé avec FALSE
	 * @param string $order_by Forcer un ordre de tri
	 * @param string $asc Forcer un sens de tri (asc ou desc), desc par defaut
	 * @return array
	 */
	public function options($fields=NULL,$id_on_key=NULL,$reset=true,$order_by=false,$asc='desc') {
		$field_nom_saved = $this->field_nom; // Sauvegarde du format officiel
		if ($fields) {
			$this->field_nom = $fields;
		}

		if ($reset) {
			$this->q->reset();
		}

		$this->q
		->reset('field')
		->addField($this->table.".id_".$this->table);

		if ($id_on_key) {
			$this->q->addField($id_on_key);
		} else {
			$id_on_key = $this->table.".id_".$this->table."_fk";
		}

		$this->q->setArrayKeyIndex($id_on_key,false);

		if ($data = self::select_all($order_by,$asc)) {
			foreach($data as $key => $item) {
				$data[$key] = $item[$this->table.".id_".$this->table];
			}
		}
		$this->field_nom = $field_nom_saved; // On remet la sauvegarde du format officiel
		return $data;
	}

	/**
	 * Retourne la table à laquelle fait référence ce champ pour cette table courante
	 * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	 * @param string $field
	 * @param boolean $si_different si VRAI alors on ne retourne rien par défaut, sinon on retourne la table courante
	 * @param boolean $strict si VRAI alors on retourne la complète référence au namespace, SINON juste à la table
	 * @return string La table de référence
	 */
	public function fk_from($field,$si_different=true,$strict=false) {
		$arg = $field."|".$si_different."|".$strict;
		if (!isset($this->cache[__FUNCTION__][$arg])) {
			if ($pos = strpos($field,".")) {
				$field = substr($field,$pos+1);
			}
			if (isset($this->foreign_key[$field])) { // Clé externe strictement définie
				$this->cache[__FUNCTION__][$arg]=$this->foreign_key[$field];
			} elseif (substr($field,0,3)=="id_" && ATF::db($this->db)->table_or_view_exists(substr($field,3))) { // Clé externe auto-détectée
				$this->cache[__FUNCTION__][$arg]=substr($field,3);
				//			} elseif (substr($field,0,3)=="id_" && $this->namespace && ATF::getClass($this->namespace."\\".substr($field,3))) { // Clé externe auto-détectée
				//				if ($strict) {
				//					$this->cache[__FUNCTION__][$arg]=$this->namespace."\\".substr($field,3);
				//				} else {
				//					$this->cache[__FUNCTION__][$arg]=substr($field,3);
				//				}
			} elseif ($si_different===false) {
				if ($strict) {
					$this->cache[__FUNCTION__][$arg]=$this->name();
				} else {
					$this->cache[__FUNCTION__][$arg]=$this->table;
				}
			} else {
				$this->cache[__FUNCTION__][$arg]="";
			}
		}
		return $this->cache[__FUNCTION__][$arg];
	}

	/**
	 * Retourne VRAI si le champ n'est pas de la forme "table.id_table",
	 * ou de la forme "table.id_table2" avec table2 passé en paramètre et différent de table
	 * @author Yann GAUTHERON <ygautheron@absystech.fr>
	 * @param string $field : champ de la forme "societe.id_societe" ou "societe__tutu.id_societe_fk", pointeur pour optimisation
	 * @param string $table2 : champ de la forme "affaire.id_societe" sir $table2=societe
	 * @version 1.0
	 * @return boolean
	 */
	public function isNotPrimaryKeyField(&$field)	{
		//log::logger($this,ygautheron);
		if (!isset($this->cache[__FUNCTION__][$field])) {
			if ($this->table!==NULL && preg_match("/\.id_".$this->table."$/",$field)) { // Seconde Forme
				return false;
			}
			if (substr($field,-3)==="_fk") {
				return false;
			}
			$matches = array();
			preg_match("/(.[^.]*).(.*)/i",$field,$matches);
			$this->cache[__FUNCTION__][$field] = ! ( count($matches)==3 && substr($matches[2],0,3)=="id_" && $matches[1]==substr($matches[2],3) );
			//return !$fk;
		}
		return $this->cache[__FUNCTION__][$field];
	}

	/**
	 * Retourne un tableau résumé des jointures déjà effectuées afin de pouvoir accéder facilement aux alias correspondants à utiliser
	 * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	 * @param array $jointure Les jointures modifiées au besoin
	 * @return array Tableau résumé des tables déjà jointes
	 */
	public function joined($jointure) {
		$table_jointe = array();
		if ($jointure) {
			// Scan les tables demandées dans les jointures déjà faites
			foreach ($jointure as $k => $i) {
				if (is_array($i) && $i["alias"]) {
					$table_jointe[$i["alias"]] = $i["table_right"];
				} else {
					$table_jointe[$i["table_right"]] = $i["table_right"];
				}
			}
		}
		return $table_jointe;
	}

	/**
	 * Tranforme le champ d'une table par le champ de référence par jointure supplémentaire nécessaire éventuelle
	 * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	 * @param array $field Champ à traiter
	 * @param array $jointure Jointure déjà programmées
	 * @return array Résultat de la transformation, ajoute dans $jointure les jointures supplémentaires nécessaires
	 *		array(
	 *			"field_fk" => string
	 *			,"field" => string
	 *		)
	 */
	public function foreign_key_translator($field,&$jointure) {
		if (strpos($field,"AS")===false) { // Seulement si aucun alias est défini, auquel cas il s'afgit d'un champ défini manuellement, et les jointure ont dû l'être aussi manuellement
			$table_jointe = $this->joined($jointure);
			$f_table = explode(".",$field);
			if (count($f_table)===2) {
				/* S'il s'agit d'une foreign key, on la transforme */
				if (($fk_table = $this->fk_from($f_table[1],false)) && ($fk_table !== $this->table || $f_table[1]==="id_".$this->table || $this->foreign_key[$f_table[1]])) {
					if (isset($table_jointe[$fk_table]) && !$this->foreign_key[$f_table[1]]) { // La jointure existe déjà sur la table retournée par fk_from
						$alias = $fk_table;
					} else { // Création automatique de la jointure
						if (($jointure[$fk_table] || $fk_table === $this->table) && !$this->foreign_key[$f_table[1]]) {
							$alias = $fk_table;
						} else {
							$alias = $fk_table."__".$f_table[1];
							if(!$jointure[$alias]) {
								$jointure[$alias] = array(
								"table_left" => isset($table_jointe[$f_table[0]]) ? $f_table[0] : $this->table
								,"field_left" => $f_table[1]
								,"table_right" => $fk_table
								,"field_right" => "id_".$fk_table
								,"alias" => $alias
								,"externe" => left
								);
							}
						}
					}

					if (ATF::getClass($fk_table)->field_nom) {
					 // Nom particulier à prendre par défaut
						if (strpos(ATF::getClass($fk_table)->field_nom,'%')!==false) {
							// Syntaxe avancée
							$f_nom = preg_replace_callback(
							'/%(.[a-zA-Z\_\.^\ ]*)%/'
							, create_function(
								'$matches'
								, 'return "||[".$matches[1]."]||";'
								)
								, ATF::getClass($fk_table)->field_nom
								);
								$f_nom = explode("||[",$f_nom);
								foreach ($f_nom as $k => $i) {
									$i = explode("]||",$i);
									$suffix = $i[1];
									$f_nom[$k] = $i = $i[0];
									if ($i!==$field) { // Doit être différent du champ actuellement traité, sinon boucle infinie !
										if (preg_match("/^([a-zA-Z\_]+\.{0,1}[a-zA-Z\_]+)$/",$i)) {
											if (strpos($i,".")===false) {
												// On essai avec un préfixe de la table courante si aucun séparateur TABLE/CHAMP détecté
												$f_nom[$k] = $alias.".".$i;
											}
											// RECURSIVITE !
											// SU chaque champ de la syntaxe, on essai de le traiter de la même manière.
											if ($f_nom[$k]!==$field && ($concatField = $this->foreign_key_translator($f_nom[$k],$jointure))) {
												$f_nom[$k] = $concatField["field"];
											}
										} else {
											$f_nom[$k] = "'".ATF::db($this->db)->real_escape_string($i)."'";
										}
									}

									// Ne pas conserver une chaine vide
									if (!strlen($i)) {
										unset($f_nom[$k]);
									}

									// Insérer le suffixe
									if (strlen($suffix)) {
										$f_nom[$k] .= ",'".ATF::db($this->db)->real_escape_string($suffix)."'";
									}
								}
								$return["field"] = ATF::db($this->db)->concat($f_nom);
						} elseif (count($f_nom = explode(',',ATF::getClass($fk_table)->field_nom))>1) {
							// Mode standard, séparé par virgule
							$return["field"] = ATF::db($this->db)->concat(array($alias.".".implode(','.$alias.".",$f_nom))," "); //'CONCAT_WS(" ",'.$alias.".".implode(',\' \', '.$alias.".",$f_nom).')';
						} else {
							$return["field"] = $alias.".".$f_nom[0];
						}
					} else {
						$return["field"] = $alias.".".$fk_table;
					}
					$return["pk_field"] = $alias.".id_".$fk_table;
				}

				/* Si on a un point dans le nom du champ, on vérifie qu'on a bien fait une jointure sur la table */
				elseif ($f_table[0]!==$this->table) {
					//$f_field = $f_table[1];
					$f_field_original = $f_table[1];
					$f_field = "id_".$f_table[0];
					$f_table_original = $f_table = $f_table[0];
					if (!isset($table_jointe[$f_table])) {
						/* Si on a pas défini de jointure pour cette table, alors on s'en charge maintenant */
						if ($jointure[$f_table] || $f_table === $this->table) {
							$alias = $f_table;
						} else {
							$alias = $f_table."__".$f_field;
							$jointure[$alias] = array(
							"table_left" => $this->table
							,"field_left" => $f_field
							,"table_right" => $f_table
							,"field_right" => $f_field
							,"alias" => $alias
							,"externe" => left
							);
							$f_table = $alias; // L'alias est créé selon la forme générique
						}
					}
					$return["field"] = $f_table.".".$f_field_original;

					// CLé primaire réelle à prendre
					$pk_field = "id_".$f_table_original;
					if (isset($table_jointe[$f_table_original])) {
						$pk_field = "id_".$table_jointe[$f_table_original];
					}

					$return["pk_field"] = $f_table.".".$pk_field;

				}
			}

			// Gestion du group_concat
			if ($attributs = $this->field_column($field)) {
				// Traductions des types énumérés
				if (isset($attributs["EnumTranslate"]) && $attributs["EnumTranslate"] && isset($attributs["data"]) && $attributs["data"]) {
					$return["field"] = ATF::db($this->db)->enumTranslation($field,$attributs["data"]);
				}

				// Gestion du group_concat
				if (isset($attributs["group_concat"]) && $attributs["group_concat"]) {
					$return["field"] = ATF::db($this->db)->group_concat($return["field"]);
				}
			}

			return $return;
		}
	}

	/**
	 * Impose de ne retourner que le total des lignes
	 * @author Yann GAUTHERON <ygautheron@absystech.fr>
	 * @param array $s : la session
	 * @return int Résultat de la requête
	 */
	public function select_all_count_only(&$s){
		$this->q->setCountOnly();
		return $this->select_all();
	}

	/**
	 * Appel la méthode de classe particulière à utiliser,
	 * si $method =flase on utilise select_all
	 * Utilisation dans generic_select_all
	 * @author Yann GAUTHERON <ygautheron@absystech.fr>
	 * @param array $s : la session
	 * @param string $method : methode de classe particulière à utiliser
	 * @return array Résultat de la requête
	 */
	public function select_data(&$s,$method=false){
		if ($method && method_exists($this,$method)) {
			return $this->$method($s);
		} else {
			return $this->select_all();
		}
	}

	/**
	 * Requête simplifiée pour appel de listing à filtrage uniconditionnel
	 * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	 * @param string $order_by Forcer un ordre de tri
	 * @param string $asc Forcer un sens de tri (asc ou desc), desc par defaut
	 * @param string $page Forcer une page particulière
	 * @param boolean $count Retourner aussi le nombre d'enregistrements sollicités
	 * @return array Résultat de la requête
	 * 	$count===true :
	 *		array(
	 *			"data" => array( données résultantes )
	 *			,"count" => nombre de données sollicitées au total par cette requête
	 *		)
	 * 	$count===1 :
	 *		int
	 * 	$count===false :
	 *		array( données résultantes )
	 */
	public function query($order_by=false,$asc='desc',$page=false,$count=false) { return $this->select_all($order_by,$asc,$page,$count); }
	/** Alias pour éviter d'appeler les méthodes setDimension, et plus parlant que d'appeler la méthode "select_all" si on ne veut qu'un champs
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function select_cell($order_by=false,$asc='desc',$page=false,$count=false) {
		$this->q->setDimension('cell');
		return $this->select_all($order_by,$asc,$page,$count);
	}
	public function select_row($order_by=false,$asc='desc',$page=false,$count=false) {
		$this->q->setDimension('row');
		return $this->select_all($order_by,$asc,$page,$count);
	}
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false,$noapplyfilter=false) {

/*
// Voir d'où a été appelée ce select_all
if ($this->table=="facture") {
	$callers=debug_backtrace();
	log::logger("select_all "
		.$callers[1]['function']."<"
		.$callers[2]['function']."<"
		.$callers[3]['function']."<"
		.$callers[4]['function']."<"
		.$callers[5]['function']."<"
		.$callers[6]['function']."<"
		.$callers[7]['function']."<"
		.$callers[8]['function']."<"
		.$callers[9]['function']
	,ygautheron);
}*/
		// Filtrage général
		if($noapplyfilter===false){
			$this->saFilter();
		}

		// Si pas de table de référence, on prend la table de la classe
		if (!$this->q->table) {
			$this->q->setRefTable($this->table,false);
		}

		/* Les arguments sont prioritaires sur le requêteur */
		if($order_by=="no_order"||$this->q->dimension==="cell"){
			//si on ne veut pas de tri
		}elseif ($order_by !==false){
			$this->q->addOrder($order_by,$asc);
		}elseif($this->q->table===$this->q->getAlias()){
			$this->q->addOrder($this->q->table.".id_".$this->table,$asc); // Trie par défaut en ordre décroissant des clés primaire
		}

		if ($page!==false) 	$this->q->setPage($page);
		if ($count) 		$this->q->setCount($count);

		return ATF::db($this->db)->select_all($this);
	}

	/**
	 * Alias de select_all, permet d'accéder au select_all() générique, et bypasser éventuellement les select_all() surchargés.
	 */
	public function sa($order_by=false,$asc='asc',$page=false,$count=false,$noapplyfilter=false) {
		return self::select_all($order_by,$asc,$page,$count,$noapplyfilter);
	}

	/**
	 * Requête simplifiée pour appel de listing à filtrage uniconditionnel
	 * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	 * @param string $field Champ de condition
	 * @param string $value Valeur de condition
	 * @param string $order_by Forcer un ordre de tri
	 * @param string $asc Forcer un sens de tri (asc ou desc)
	 * @param string $page Forcer une page particulière
	 * @param boolean $count Retourner aussi le nombre d'enregistrements sollicités
	 * @return array Résultat de la requête
	 * 	$count==true :
	 *		array(
	 *			"data" => array( données résultantes )
	 *			,"count" => nombre de données sollicitées au total par cette requête
	 *		)
	 * 	$count==false :
	 *		array( données résultantes )
	 *
	 */
	public function select_special($field,$value,$order_by=false,$asc='asc',$page=false,$count=false) {
		$this->q->reset();
		$this->q->addCondition($field,$value);
		return $this->select_all($order_by,$asc,$page,$count);
	}

	/**
	 * Alias de select_special
	 */
	public function ss($field,$value,$order_by=false,$asc='asc',$page=false,$count=false) {
		return $this->select_special($field,$value,$order_by,$asc,$page,$count);
	}

	/**
	 * Retourne le nombre d'enregistrements dans cette table
	 * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	 * @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	 * @param array $fk Un ensemble de clée étrangère
	 * @param bool $reset true si on désire faire un reset sur le querier
	 * @return int
	 */
	public function count($fk=NULL,$reset=false,$function=NULL,$conditionField=NULL,$condition_value=NULL) {
		if($reset){
			$this->q->reset();
		}
		if($fk && is_array($fk)){
			foreach($fk as $key=>$value){
				$this->q->addCondition($key,$value);
			}
		}
		$this->q->setCountOnly();
		if($function){
			if(method_exists($this, $function)){
				$count = true;
				$return = $this->$function(array("condition_field"=>$conditionField, "condition_value"=>$condition_value) ,$reset,$count);
			}
		}else{
			$return = $this->select_all();
		}

		unset ($this->q->count);
		return $return;
	}

	/**
	 * Retourne la structure de la table avec les types HTML de formulaire correspondants
	 * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	 * @param $filtre Champs à ne pas traiter
	 * @return array
	 */
	public function html_structure($filtre=false) {
		return ATF::db($this->db)->table2htmltable($this->desc,explode(",",$filtre));
	}

	/**
	 * Retourne la structure de la table courante
	 * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	 * @param string $filtre Sauf ces fields là
	 * @param boolean $autodetect_fk VRAI pour remplacer à la volée les champs de clé par les libellés par défaut des tables satellites
	 * @return array
	 */
	public function table_structure($filtre="",$autodetect_fk=false,$simplify=false) {
		if ($fields = ATF::db($this->db)->fields($this->table,explode(",",$filtre))) {
			if ($autodetect_fk===false) {
				foreach ($fields as $key => $item) {
					if (!$simplify) {
						$item = $this->table.".".$item;
					}
					$t_fields[$item] = $item;
				}
			} else {
				foreach ($fields as $key => $item) {
					if ($fk = $this->fk_from($item,false)) {
						if (!$simplify) {
							$item = $fk.".".$fk;
						}
						$f = $fk;
					} else {
						//Afin de détecter s'il est possible de passer dans cette partie du code
						if (!$simplify) {
							$item = $this->table.".".$item;
						}
						$f = $item;
					}
					$t_fields[$f] = $f;
				}
			}
		}
		return $t_fields;
	}

	/**
	 * Vérifie que tous les champs obligatoires sont renseignés
	 * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	 * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	 * @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	 * @author Yann GAUTHERON <ygautheron@absystech.fr>
	 * @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	 * @param boolean $update True si on utilise un check_field dans un update
	 */
	public function check_field(&$infos,$update=false){
		//Construction des colonnes pour le check
		$panels = $this->colonnes["panel"];
		$panels[] = $this->colonnes["restante"];
		$panels[] = $this->colonnes["primary"];
		$colonnes = array();
		foreach($panels as $key=>$item){
			foreach($item as $k=>$i){
				if ($i["fields"]) {
					// Compositefields
					foreach ($i["fields"] as $k_ => $i_) {
						$colonnes[$k_]=$i_;
					}
				} else {
					$colonnes[$k]=$i;
				}
			}
		}

		if($update){
			$colonnes_update=array();
			foreach($infos as $index=>$col){
				if($colonnes[$index]){
					$colonnes_update[$index]=$colonnes[$index];
				}
			}
			$colonnes=$colonnes_update;
		}
		//Fin construction des colonnes
		$probleme = array();
		foreach($colonnes as $key => $item){
			//Vérification de la taille et du type de la donnée ; $infos["index"] n'existe que s'il y a plusieurs ligne dans une insertion genre (devis, commande, facture)
			if (isset($infos[$key])) {
				if (!ATF::getClass($this->table)->noTrim) {
					$infos[$key]=trim($infos[$key]);
				}

				//				//Permet d'enlever le html inséré lorsque le champs est un autocomplete
				//				$explode=explode('<span class="searchSelectionFound">', $infos[$key]);
				//				$infos[$key]=$explode[0].$explode[1];
				//				$explode=explode('</span>', $infos[$key]);
				//				$infos[$key]=$explode[0].$explode[1];

				$this->check_type($item,$infos[$key],$key,$infos["index"]);
			}
			//Patch pour les default non renseignés
			if(isset($item["default"]) && array_key_exists($key,$infos) && !strlen($infos[$key]) && (!array_key_exists("null",$item) || ( isset($item["obligatoire"]) && $item["obligatoire"] ))){
				unset($infos[$key]);
			}
			if((!isset($item["null"]) || (isset($item["obligatoire"]) && $item["obligatoire"])) && strlen($item["default"])===0 && $key!="id_".$this->table && !$item["custom"]){
				if(!$infos[$key] && $infos[$key]!=="0"){
					if (isset(ATF::$usr)) {
						$probleme[]= "'".ATF::$usr->trans($key,$this->table)." (".ATF::$usr->trans($this->table).")'";
						//Dans le cas où il y a plusieurs lignes
						if(isset($infos["index"])){
							$changeElementsClass[$this->table."[".$key."][".$infos["index"]."]"]="formError";
							if (ATF::getClass($this->fk_from($key))) {
								$changeElementsClass[$key."_label[".$infos["index"]."]"]="formError";
							}
						}else{
							$changeElementsClass[$this->table."[".$key."]"]="formError";
							if (ATF::getClass($this->fk_from($key))) {
								$changeElementsClass[$key."_label"]="formError";
							}
						}
					}
				}
			}

			if ($key=="rib" && $infos['rib']) {
				$infos['rib'] = str_replace(" ","",$infos['rib']);
				$cbanque = substr($infos['rib'],0,5);
				$cguichet = substr($infos['rib'],5,5);
				$nocompte = substr($infos['rib'],10,11);
				$clerib = substr($infos['rib'],-2);
				if (!util::check_rib($cbanque,$cguichet,$nocompte,$clerib)) {
					$probleme[]= "'".ATF::$usr->trans("errorRib",$this->table)."'";
					$changeElementsClass[$key]="formError";
				}
			}
			if ($key=="email" && $infos['email']) {
				if (!util::isEmail($infos['email'])) {
					$probleme[]= "'".ATF::$usr->trans("errorEmail")."'";
					$changeElementsClass[$key]="formError";
				}
			}
		}

		if ($probleme) {
			//Ajout des champs erronés dans le json
			ATF::$json->add("formErrors",array_keys($changeElementsClass));
			if($infos["speed_insert"]){
				//Définition de l'affichage des erreurs formulaires :
				//   highlight : Affichage des champs en rouge
				//   smartip : Affichage de la smartip sur le champ
				ATF::$json->add("formErrorConfig",array("highlight"=>true,"smartip"=>false));
				ATF::$json->add("formErrorMsg",implode(", ",$probleme));
				ATF::$cr->block("top");
				ATF::$cr->block("generationTime");
				return false;
			}
			if(ATF::_r("extAction")){
				ATF::$json->add("formErrorConfig",array("highlight"=>false,"smartip"=>true));
			}else{
				ATF::$json->add("formErrorConfig",array("highlight"=>true,"smartip"=>true));
			}
			throw new errorATF(util::mbox(
			implode(", ",$probleme)
			,ATF::$usr->trans("data_manquante")
			));
		}

		if(isset($infos["index"])){
			unset($infos["index"]);
		}
		return true;
	}

	/**
	 * Check les numéro de téléphones, fax et gsm, et les formatte correctement s'il le faut.
	 * @author Quentin JANON <qjanon@absystech.fr>
	 * @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	 */
	public function check_tel(&$infos){
		foreach ($infos as $k=>$i) {
			if ($k=="tel" || $k=="fax" || $k=="gsm") {
				$infos[$k] = str_replace(".","",$infos[$k]);
				$infos[$k] = str_replace(" ","",$infos[$k]);
				$infos[$k] = str_replace("-","",$infos[$k]);
				$infos[$k] = str_replace("/","",$infos[$k]);
				$infos[$k] = str_replace("(0)","",$infos[$k]);
			}
		}
	}

	/**
	 * Vérifie que tous les champs sont du bon type
	 * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	 * @param array $item tableau définie dans la htmlstructure
	 * @param string $value valeur de la donnée à insérer
	 * @param string $key nom du champ
	 * @param int $index Utilisé pour sélectionner la ligne de l'élément(par exemple pour les devis)
	 */
	public function check_type($item,&$value,$key,$index=false){
		$check_value=$value;
		if($check_value){
			if($item["type"]=="int"){
				$check_value = $this->decryptId($check_value);
			}

			if($item["type"]=="int"){
				$conv_value=util::stringToNumber($check_value);
				if($check_value!="0" && $conv_value==0){
					$probleme=true;
					$type="type";
				}else{
					$value=$conv_value;
				}
			}elseif($item["type"]=="decimal"){
				$value=str_replace(" ","",$check_value);
				if(!is_numeric($value)){
					if(strrpos($value,",")){
						$value=str_replace(",",".",$value);
					}else{
						$probleme=true;
						$type="type";
					}
				}
			}elseif($item["type"]=="enum"){
				if(!in_array(strtolower($check_value),array_map("strtolower",$item["data"]))){
					$probleme=true;
					$type="type";
				}
			}elseif($item["type"]=="date" || $item["type"]=="datetime"){
				$check_value=util::formatDate($check_value);
				if(!strtotime($check_value) || $check_value=="1970-01-01 01:00:00" || $check_value=="1970-01-01"){
					$probleme=true;
					$type="type";
				}else{
					$value=$check_value;
				}
			}

			if($item["maxlength"]){
				if($item["maxlength"]<strlen($check_value)){
					$probleme=true;
					$type="taille";
				}
			}

			if ($probleme) {
				return $this->checkTypeRaiseError($key,$item,$index,$type,$check_value);
			}
			return true;
		}
	}

	/**
	 * Déclenche une erreur de type de champ
	 * @author Yann GAUTHERON <ygautheron@absystech.fr>
	 * @param string $key nom du champ
	 * @param array $item
	 * @param int $index Utilisé pour sélectionner la ligne de l'élément(par exemple pour les devis)
	 * @param string $type type|taille Type d'erreur
	 * @param string $value
	 */
	public function checkTypeRaiseError($key,$item,$index,$type=NULL,$value) {
		$probleme[]= ATF::$usr->trans($this->table)." : '".ATF::$usr->trans($key)."' ('".$value."') ".ATF::$usr->trans($item["type"]);

		//Dans le cas où il y a plusieurs lignes
		if(isset($index)){
			$changeElementsClass[$this->table."[".$key."][".$index."]"]="formError";
			if (ATF::getClass($this->fk_from($key))) {
				$changeElementsClass[$key."_label[".$index."]"]="formError";
			}
		}else{
			$changeElementsClass[$this->table."[".$key."]"]="formError";
			if (ATF::getClass($this->fk_from($key))) {
				$changeElementsClass[$key."_label"]="formError";
			}
		}
		// Champs erronés envoyés dans le json
		ATF::$json->add("formErrors",array_keys($changeElementsClass));

		//Définition de l'affichage des erreurs formulaires :
		//   highlight : Affichage des champs en rouge
		//   smartip : Affichage de la smartip sur le champ
		ATF::$json->add("formErrorConfig",array("highlight"=>true,"smartip"=>false));

		if($type=="type"){
			throw new errorATF(util::mbox(
			implode(", ",$probleme)
			,ATF::$usr->trans("data_mauvais_type")
			),102);
		}elseif($type=="taille"){
			throw new errorATF(util::mbox(
			implode(", ",$probleme)
			,ATF::$usr->trans("data_mauvaise_taille")." ".strlen($value).ATF::$usr->trans("caractere_mauvaise_taille").$item["maxlength"]
			),103);
		}

		// Type d'erreur mauvais
		return false;
	}

	/**
	 * Vérifie que tous les fichiers obligatoires sont uploadés
	 * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	 * @param array &$s La session
	 */
	public function check_files($id=false){
		$probleme = array();
		$s=ATF::_s($this->table);
		//S'il y a des fichiers pour cette classe
		if (!empty($this->files)) {
			foreach($this->files as $key=>$item){
				//Si le fichier n'a pas été uploadé, s'il est obligatoire et si il n'y a pas de pdf généré automatiquement
				if(!$s["upload"][$key] && $item["obligatoire"] && (!method_exists(ATF::pdf(),$this->table))){
					//Si le fichier n'est pas sur le serveur
					if(!$this->file_exists($id,$key)){
						$probleme[] = "'".ATF::$usr->trans($key)." (".$this->table.")'";
					}
				}
			}
		}

		if($probleme){
			throw new errorATF(util::mbox(
			loc::mt(ATF::$usr->trans("file_manquant"),array("data"=>implode(", ",$probleme)))
			,ATF::$usr->trans("attention_file_obligatoire_manquant")
			),103);
		}else{
			return true;
		}
	}

	/**
	 * Traduit la valeur de chaque id en int
	 * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	 * @param array $infos
	 * @return array $infos avec les id  parser en int
	 */
	public function check_decryptId(&$infos){
		foreach($infos as $key => $item) {
			if($foreign_class=ATF::getClass($this->fk_from($key))){
				$infos[$key]=$foreign_class->decryptId($item);
			}
		}
		return $infos;
	}

	/**
	 * Gestion du speed insert afin de retourner l'enregistrement et non pas juste l'id
	 * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	 * @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	 * @param array &$s La session
	 * @param array $files $_FILES
	 * @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	 * @param array $nolog True si on ne désire par voir de logs générés par la méthode
	 * @return boolean TRUE si cela s'est correctement passé
	 */
	public function speed_insert($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL,$nolog=false) {
		$last_id = $this->insert($infos,$s,$files,$cadre_refreshed,$nolog);
		$result["nom"]=$this->nom($last_id);
		$result["id"]=$last_id;
		return $result;
	}

	/**
	 * Gestion de l'envoi rapide d'un mail
	 * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	 * @param array $infos champs concernat le mail (destinataire,objet,copie,texte,files)
	 * @return boolean TRUE si cela s'est correctement passé sinon false
	 */
	public function quick_mail($infos) {
		if($infos[$this->table]["email"]){

			$from = ATF::$usr->get('email');

			$info_mail["objet"] = $infos[$this->table]["objet"];
			$info_mail["from"] = ATF::user()->nom(ATF::$usr->getID())." <".$from.">";
			$info_mail["html"] = false;
			$info_mail["template"] = $this->table;
			$info_mail["texte"] = $infos[$this->table]["texte"];
			$info_mail["recipient"] = $infos[$this->table]["email"];

			$mail = new mail($info_mail);

			//Mail copie
			if($infos[$this->table]["emailCopie"]){
				$info_mail["recipient"] = $infos[$this->table]["emailCopie"];
				$copy_mail = new mail($info_mail);
			}

			//Ajout de fichier(s)
			//S'il y a un fichier joint dans le constructeur
			if($infos[$this->table]["filestoattach"]){
				$fichier_joints="Fichiers joints :";
				foreach($infos[$this->table]["filestoattach"] as $key=>$item){
					//S'il y a un fichier joint effectif
					if($item && $item!=="undefined"){
						//Si c'est le Fichier de l'enregistrement du module
						if($item==="true"){
							$nameFile=$key."-".$this->nom($infos["id"]);
							if($this->files[$key]["type"]){
								$nameFile.=".".$this->files[$key]["type"];
							}
							$path = $this->filepath($infos["id"],$key);
						}else{
							$nameFile=$item;
							// Fichier téléchargé par l'utilisateur
							$path = $this->filepath(ATF::$usr->getID(),$key,true);
						}
						$fichier_joints.=" ".$nameFile;
						$mail->addFile($path,$nameFile,true);
						if($copy_mail){
							$copy_mail->addFile($path,$nameFile,true);
						}
					}
				}
			}

			//Envoi mail
			if($mail->send()){
				ATF::$msg->addNotice(
				loc::mt(ATF::$usr->trans("notice_mail_envoye")." ".$infos[$this->table]["email"]." ".$fichier_joints)
				,ATF::$usr->trans("notice_success_title")
				);
			}
			if($copy_mail){
				if($copy_mail->send()){
					ATF::$msg->addNotice(
					loc::mt(ATF::$usr->trans("notice_mail_envoye")." ".$infos[$this->table]["emailCopie"]." ".$fichier_joints)
					,ATF::$usr->trans("notice_success_title")
					);
				}
			}

			return true;

		}else{
			throw new errorATF("Veuillez renseigner le mail",999);
			return false;
		}
	}


	/**
	 * Insertion dans la table courante
	 * Utilisation d'un querier d'insertion
	 * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	 * @author Quentin JANON <qjanon@absystech.fr>
	 * @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	 * @author Fanny DECLERCK <fdeclerck@absystech.fr>
	 * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	 * @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	 * @param array &$s La session
	 * @param array $files $_FILES
	 * @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	 * @param array $nolog True si on ne désire par voir de logs générés par la méthode
	 * @version 3
	 * @return boolean TRUE si cela s'est correctement passé
	 */
	public function insert($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL,$nolog=false) {
		$this->infoCollapse($infos);

		if (isset($infos["ok"])) {
			unset($infos["ok"]);
		}

		//on retire la redirection si on insere un élément d'un module qui est l'onglet d'un autre
		if (isset($infos["__redirect"])) {
			$redirection=$infos['__redirect'];
			unset($infos['__redirect']);
		}

		// Dans le cas d'un clone il faut retirer la clé primaire
		// sauf si il s'agit d'une insertion d'un module tracé dont on rollback
		// Je l'ai enlevé car on a maintenant besoin de faire des insertion avec l'id (ex la synchro de calinauto)
		// En plus pour le clonage, l'id doit être unsetté normallement dans la fonction d'insert surchargé créer pour l'occasion.

		// le cas d'un cloner, on peut le détecter grâce a l'extMethod cloner dans le ATF::_r()
		if (isset($infos["id_".$this->table]) && ATF::_r("extMethod")=="cloner") {
			$id = $infos["id_".$this->table];
			unset($infos["id_".$this->table]);
		}
		if(isset($infos["id_".$this->table]) && !$this->no_clone && !$this->insertWithId){
			$id = $infos["id_".$this->table];
			unset($infos["id_".$this->table]);
		}

		// YG: On a commenté tout le système de rollback
		if(ATF::$tracabilite && ATF::tracabilite()->rollback_trace===true){
			$infos["id_".$this->table] = $id;
		}

		//Vérifie que tout les champs sont en int
		$infos = $this->check_decryptId($infos);

		//Vérification des champs obligatoire à renseigner
		if(!$this->check_field($infos)) return false;

		//Formattage des numéros de téléphone
		$this->check_tel($infos);

		//Encrypte le mdp les mot de passe
		if (isset($infos["password"])) {
			$infos["password"] = hash('sha256',$infos["password"]);
		}
		if (isset($infos["pwd"])) {
			$infos["pwd"] = hash('sha256',$infos["pwd"]);
		}

		// Ajout des infos dans le querier
		$this->q->reset("values")->addValues($infos,false);

		// Utilisation de l'API de base de données
		if($last_id=ATF::db($this->db)->insert($this,$nolog)){
			// Redirection
			if($this->table!='tracabilite' && is_array($cadre_refreshed)){
				if($redirection && $redirection!=$this->table){
					if($obj=ATF::getClass($redirection)){
						$obj->redirection($this->getDefaultRedirection(__FUNCTION__),$infos["id_".$redirection],$obj->getDefaultUrl(__FUNCTION__,$infos["id_".$redirection]).":".$this->table);
					}else{
						throw new errorATF('wrong_redirect_module',3847);
					}
				}else{
					$this->redirection($this->getDefaultRedirection(__FUNCTION__),$last_id,self::getDefaultUrl(__FUNCTION__,$last_id));
				}
			}
		}
		return $last_id;
	}

	/**
	 * Alias de insert, permet d'accéder au insert() générique, et bypasser éventuellement les insert() surchargés.
	 */
	public function i($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL,$nolog=false) {
		return self::insert($infos,$s,$files,$cadre_refreshed,$nolog);
	}

	/**
	 * Par défaut le clone est un alias de insert
	 */
	public function cloner($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL,$nolog=false) {
		return $this->insert($infos,$s,$files,$cadre_refreshed,$nolog);
	}

	/**
	 * Alias de update, permet d'accéder au update() générique, et bypasser éventuellement les update() surchargés.
	 */
	public function u($infos,&$s,$files=NULL,&$cadre_refreshed,$nolog=false) {
		return self::update($infos,$s,$files,$cadre_refreshed,$nolog);
	}

	/**
	 * Alias de delete, permet d'accéder au delete() générique, et bypasser éventuellement les delete() surchargés.
	 */
	public function d($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL, $no_file_delete=false) {
		return self::delete($infos,$s,$files,$cadre_refreshed, $no_file_delete);
	}

	/**
	 * Insertion multiple dans la table courante à l'aide du nombre minimum de requêtes (optimisation)
	 * Utilisation d'un querier d'insertion
	 * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr> Jérémie Gwiazdowski <jgw@absystech.fr>
	 * @param array $infos Tableau à deux dimensions, permettant d'insérer plusieurs enregistrements ayant les même champs
	 * @param array &$s La session
	 * @param array $files $_FILES
	 * @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	 * @param array $nolog True si on ne désire par voir de logs générés par la méthode
	 * @return boolean TRUE si cela s'est correctement passé
	 */
	public function multi_insert($infos,$nolog=false,$options=false,&$cadre_refreshed=NULL) {
		if(!is_array($infos)) {
			throw new errorATF("[muti_insert] $infos n'est pas un tableau !",104);
		}

		// Ajout des infos dans le querier
		$this->q->reset("values")->addMultiValues($infos);

		// Utilisation de l'API de base de données
		$retour=ATF::db($this->db)->multi_insert($this,$nolog,$options);

		//-----------------Cadre refresh (Redirection) --------------------------
		if (is_array($cadre_refreshed) && $this->table!='tracabilite') {
			$this->redirection(array("id_".$this->table=>$last_id),$s,$files,$cadre_refreshed);
		}

		return $retour;
	}

	/**
	 * Mise à jour dans la table courante
	 * Utilisation d'un querier de mise à jour
	 * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	 * @author Quentin JANON <qjanon@absystech.fr>
	 * @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	 * @author Fanny DECLERCK <fdeclerck@absystech.fr>
	 * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	 * @param array $infos Simple dimension des champs à mettre à jour, multiple dimension avec au moins un $infos[$this->table]
	 * @param array &$s La session
	 * @param array $files $_FILES
	 * @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	 * @param array $nolog True si on ne désire par voir de logs générés par la méthode
	 * @version 3
	 * @return boolean TRUE si cela s'est correctement passé
	 */
	public function update($infos,&$s,$files=NULL,&$cadre_refreshed,$nolog=false) {
		$this->infoCollapse($infos);

		if (isset($infos["ok"])) {
			unset($infos["ok"]);
		}

		//on retire la redirection si on insere un élément d'un module qui est l'onglet d'un autre
		if (isset($infos["__redirect"])) {
			$redirection=$infos['__redirect'];
			unset($infos['__redirect']);
		}

		/* SI on a pas de clé, on cherche dans un éventuel ->infos */
		if (!isset($infos["id_".$this->table]) && isset($this->infos["id_".$this->table])) {
			// Si l'objet contient des infos, on update par défaut l'id correspondant à cette instance
			$infos["id_".$this->table] = $this->infos["id_".$this->table];
		}
		// On sait jamais s'il s'agit d'un md5
		$infos["id_".$this->table] = $this->decryptId($infos["id_".$this->table]);

		//Vérifie que tout les champs sont en int
		$infos = $this->check_decryptId($infos);

		//Vérification des champs obligatoire à renseigner
		//$record_infos=$this->select($infos["id_".$this->table]);
		//$nv_infos=array_merge($record_infos,$infos);
		$this->check_field($infos,true);

		//Formattage des numéros de téléphone
		$this->check_tel($infos);

		/* On vire les post des champ d'upload */
		if (isset($this->upload)) {
			foreach ($this->upload as $i) {
				unset($infos[$i]);
			}
		}

		//On ne met pas à jout les mot de passe
		if (isset($infos["password"])) {
			if (strlen($infos["password"])==64) unset($infos["password"]);
			else $infos["password"] = hash('sha256',$infos["password"]);
		}

		// Réinitialisation du querier
		$this->q->reset();

		// Ajout des conditions
		$this->q->addCondition('id_'.$this->table,$infos['id_'.$this->table]);
		$id[$this->table] = $infos['id_'.$this->table];
		if (isset($infos['id_'.$this->table])) {
			unset($infos['id_'.$this->table]);
		}

		// Ajout des infos dans le querier
		$this->q->addValues($infos);
		// Utilisation de l'API de base de données
		$retour=ATF::db($this->db)->update($this,$nolog);

		//-----------------Cadre refresh (Redirection) --------------------------
		if (is_array($cadre_refreshed) && $this->table!='tracabilite') {
			//Redirection forcée par un $infos['__redirect'] (nom du module)
			if($redirection && $redirection!=$this->table){
				if($obj=ATF::getClass($redirection)){
					$obj->redirection($this->getDefaultRedirection(__FUNCTION__),$infos["id_".$redirection],$obj->getDefaultUrl(__FUNCTION__,$infos["id_".$redirection]).":".$this->table);
				}else{
					throw new errorATF('wrong_redirect_module');
				}
			}else{
				$this->redirection($this->getDefaultRedirection(__FUNCTION__),$id[$this->table],self::getDefaultUrl(__FUNCTION__,$id[$this->table]));
			}
		}

		return $retour;
	}

	/* Retourne le type équivalent HTML du champ */
	public function fieldTypeHTML($field) {
		$field = explode(".",$field);
		return ATF::db($this->db)->table2htmltable($this->desc,$field[count($field)-1]);
	}

	/**
	 * Fonction de suppression d'un élément de la table
	 * Utilisation d'un querier de suppression
	 * @author Dév <dev@absystech.fr>
	 * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	 * @param int $infos le ou les identificateurs de l'élément que l'on désire inséré
	 * @param array &$s La session
	 * @param array $files $_FILES
	 * @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	 * @version 2
	 * @return boolean TRUE si cela s'est correctement passé
	 */
	public function delete($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL, $no_file_delete=false) {
		if (is_array($infos) && isset($infos["id"])) {

			//Appel via AJAX
			if (is_array($infos["id"])) {
				$ids = $infos["id"];
			} else {
				$ids = array($infos["id"]);
			}

			// Mode strict, on supprime toujours un à un de multiples enregistrements en utilisant leur méthodes ->delete() éventuelle
			if ($infos["strict"]) {

				ATF::db($this->db)->begin_transaction();
				foreach ($ids as $id) {
					$this->delete($id,$s);
				}
				ATF::db($this->db)->commit_transaction();

				//-----------------Cadre refresh (Redirection) --------------------------
				if ($this->table!='tracabilite' && is_array($cadre_refreshed)) {
					$this->redirection("select_all_optimized",$infos["pager"]);
				}
				return true;
			}

		} elseif (is_array($infos) && isset($infos[0])) {
			// Suppression de multiples enregistrements dans un tableau à 1 dimension
			$ids = $infos;

		} elseif ((is_numeric($infos) || is_string($infos)) && $infos!="") {
			//Passage simple de l'ID
			$ids = array($infos);

		} else {
			// Dans ce cas précis, on aura normalement défini des conditions sur le querier avant l'appel à $class->delete()
		}
		if (is_array($ids)) {
			//Obligation de faire le tes dans un foreach en amont pour ne pas reseter le addCondition ci-dessous
			foreach($ids as $id){
				if (method_exists($this,"can_".__FUNCTION__) && !$this->can_delete($this->decryptId($id))) {
					throw new errorATF(loc::mt(ATF::$usr->trans("probleme_is_active",$this->table),array("table"=>ATF::$usr->trans($this->table,module),"function"=>ATF::$usr->trans(__FUNCTION__))));
				}
			}
			$this->q->reset();
			foreach($ids as $id){
				$this->q->addCondition('id_'.$this->table,$this->decryptId($id));
			}
		}

		//Utilisation de l'API de base de données
		$return = ATF::db($this->db)->delete($this);

		//si la suppression s'est bien passée, on regarde si il n'y a pas de fichier à supprimer
		if (!$no_file_delete) {
			if (is_array($ids)) {
				foreach($ids as $id){
					$this->delete_files($id);
				}
			}
		}


		//-----------------Cadre refresh (Redirection) --------------------------
		if ($this->table!='tracabilite' && is_array($cadre_refreshed)) {
			if($infos['quick_action']){
				//Lors d'un delete d'une quick_action on redirige sur le selectAll du module
				$this->redirection("select_all",NULL,self::getDefaultUrl(__FUNCTION__));
			}else{
				$this->redirection("select_all_optimized",$infos["pager"]);
			}
		}
		return true;
	}
	/**
	 * todo : A refactoriser !!! Moche !!
	 */
	public function increase($id,$field,$value=1) {
		if (!$id || !$field || !$value) return;
		$infos = array($field=>array("value"=>"`".$field."`+".$value));
		$this->q
		->reset()
		->addCondition($this->table.".id_".$this->table,$id)
		->addValues($infos);

		return ATF::db($this->db)->update($this);
	}

	/* Retourne les possibilités d'un champ enuméré */
	public function enum2array($field) {
		return ATF::db($this->db)->enum2array($this,$field);
	}

	/**
	 * Défini le requêteur à utiliser
	 * @param querier $querier
	 * @author Yann GAUTHERON <ygautheron@absystech.fr>
	 */
	public function setQuerier($querier) {
		if (is_object($querier))
		$this->q = clone $querier;
	}

	/**
	 * Retourne le nom du module complet, utile dans l'utilisation des namespace pour éviter les modules homonymes
	 * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	 * @return string
	 */
	public function name() {
		if ($this->table) {
			$r = $this->table;
		}
		return $r;
	}

	/**
	 * Retourne le nom de la table du module
	 * @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	 * @return string le nom de la table
	 */
	//	public function table(){
	//		return $this->table;
	//	}

	/**
	 * Transforme l'$infos en uniquement les infos du formulaire de données (retire une dimension), si besoin
	 * @author Yann GAUTHERON <ygautheron@absystech.fr>
	 * @param array Infos peut être en multiple dimension
	 * @return void
	 */
	public function infoCollapse(&$infos) {
		if (is_array($infos)) {
			if (is_array($infos[$this->table])) {
				$infos = $infos[$this->table];
			}
			if($infos["date"]){
				$infos["date"]=util::formatDate($infos["date"]);
			}
		} else {
			throw new errorATF($this->table."::".__FUNCTION__." > Array non valide",105);
		}
	}

	/**
	 * affiche une image
	 * @author QJ <qjanon@absystech.fr>
	 * @param array $infos
	 */
	public function img($infos) {
		$id = $this->decryptId($infos["id"]);
		if (!$id || !$infos["field"]) return false;
		if ($infos['codename'] && !ATF::$codename) {
			$codename = base64_decode($infos['codename']);
		} else {
			$codename = ATF::$codename;
		}
		if ($infos['width'] || $infos['height']) {
			$filepath = gd::createThumb($infos["table"],$id,$infos["field"],$infos['width'],$infos['height'],$infos["method"],$codename);
		} else {
			$filepath = ATF::{$infos["table"]}()->filepath($id,$infos["field"],false,$codename);
		}
		return file_get_contents($filepath);
	}

	/**
	 * Teste si l'utilisateur a le droit de faire l'évênement, et retourne un booleen
	 * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	 * @param string $event
	 * @return boolean
	 */
	public function eventPrivilege(&$event) {
		if (isset($this->eventPrivilegeMap[$event])) {
			$event = $this->eventPrivilegeMap[$event]; // Mapping de droits spécifique au module
		}
		return NULL; // Par défaut
	}

	/**
	 * DEPRECATED Exécute un template grace a un fetch afin d'en recevoir le resultat en HTML
	 * @author Quentin JANON <qjanon@absystech.fr>
	 * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	 * @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	 * @date 2009-04-09
	 * @param array $var tableau de variable a envoyer a smarty avant d'executer le template
	 * @param string $target Le div Cible ou l'on désire placer le template
	 * @param array $cadre_refreshed Pointeur vers le tableau cadre_refreshed
	 * @return string Résultat du fetch sur le template
	 */
	public function fetchHTML($var,$target,$cadre_refreshed,$tpl="generic") {
		//Fetch du template passé en paramète
		if (is_array($cadre_refreshed) && $target) {
			if(!$cadre_refreshed[$target]){ //Optimisation en cas d'appel multiple du fetch
				ATF::$cr->add($target,$tpl.".tpl.htm",$var);
			}
		} else {
			//Passage de variables à Smarty (super !!!)
			if (is_array($var)) {
				ATF::$html->array_assign($var);
			}
			return ATF::$html->fetch($tpl.".tpl.htm");
		}

	}

	/**
	 * Redirige vers le selectAll du module
	 * @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	 */
	private function redirectToSelectAll(){
		//$this->q->reset();
		$tmp=NULL;
		//Variables passées au template
		$var = array(
		"current_class"=>$this
		);

		ATF::$cr->addVar("top",$var);
		$var['define_div']=true;
		ATF::$cr->add("main","generic-select_all.tpl.htm",$var);

		//Gestion du changement de l'url
		ATF::$cr->setUrl($this->table.".html");
	}

	/**
	 * Redirige vers une fiche select
	 * @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	 * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	 * @param int $id L'identifiant de l'élément sur la fiche select
	 * @param string $parent Le nom du module parent (pour le retour sur une fiche parente)
	 */
	private function redirectToSelect($id,$parent=NULL){
		$obj=$this;
		$request=array($obj->table => $obj->select($obj->decryptId($id))
		,"event"=>"select");

		//réinitialisation du where pour éviter les problèmes de tracabilité
		$obj->q->reset('where');
		//Variables passées au template
		$var = array(
		"current_class"=>$obj
		,"requests"=>$request
		);

		ATF::$cr->addVar('top',$var);
		//gestion des templates spécifiques
		if(ATF::$html->template_exists($this->table."-select.tpl.htm")){
			ATF::$cr->add('main',$this->table."-select.tpl.htm",$var);
		}else{
			ATF::$cr->add('main',"generic-select.tpl.htm",$var);
		}

		//Gestion du changement de l'url
		ATF::$cr->setUrl($this->table."-select-".$this->cryptId($id).".html");
	}
	/**
	 * Redirige vers une fiche update
	 * @author Quentin JANON <qjanon@absystech.fr>
	 * @date 15-01-2011
	 * @param int $id L'identifiant de l'élément sur la fiche update
	 */
	private function redirectToUpdate($id){
		$obj=$this;
		$request=array($obj->table => $obj->select($obj->decryptId($id))
		,"event"=>"update");

		//réinitialisation du where pour éviter les problèmes de tracabilité
		$obj->q->reset('where');
		//Variables passées au template
		$var = array(
		"current_class"=>$obj
		,"requests"=>$request
		);

		ATF::$cr->addVar('top',$var);
		//gestion des templates spécifiques
		if(ATF::$html->template_exists($this->table."-update_ext.tpl.htm")){
			ATF::$cr->add('main',$this->table."-update_ext.tpl.htm",$var);
		}else{
			ATF::$cr->add('main',"generic-update_ext.tpl.htm",$var);
		}

		//Gestion du changement de l'url
		ATF::$cr->setUrl($this->table."-update-".$this->cryptId($id).".html");
	}

	/**
	 * Refresh optimisé du selectAll, utilisé pour le delete par exemple
	 * @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	 * @param string $pager Le nom du pager
	 */
	private function redirectToSelectAllOptimized($pager){
		ATF::$cr->block('top');
		//Blocage du rafraichissement de l'onglet complet
		ATF::$cr->block(substr($pager,0,strrpos($pager,"_")));
		//récupération du module en onglet
		preg_match("`gsa_(.*)_".$this->table."`",$pager,$recup_table);

		$tmp=substr($pager,strpos($pager,"_")+1);
		$post=array(
		"pager"=>$pager
		,"table"=>$this->table
		,"fk_name"=>$this->table.".id_".$recup_table[1]
		,"fk_value"=>substr($pager,strrpos($pager,"_")+1)
		);

		$tmp=array();
		$this->updateSelectAll($post,ATF::_s(),$tmp,$tmp);
	}

	/**
	 * Permet de faire la redirection ajax via le cadre_refresh. On peut sélectionner l'évènement dorénavent (select, insert, update, select_all)
	 * @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	 * @param string $type select,insert,parent,update,select_all
	 * @param int|string $id_or_pager id dans le cas d'un redirect sur une fiche ou alors le pager ! (Maudit
	 * @param string $url L'url ajax (ancre) que l'on désire afficher !
	 */
	public function redirection($type,$id_or_pager=NULL,$url=NULL){
		//Gestion de l'event si présent
		switch($type){
			case 'select_all':
				$this->redirectToSelectAll();
				break;
			case 'select_all_optimized':
				//Utilisé par exemple pour le delete
				$this->redirectToSelectAllOptimized($id_or_pager);
				break;
			case 'parent':
			case 'select':
				$this->redirectToSelect($id_or_pager);
				break;
			case 'update':
				$this->redirectToUpdate($id_or_pager);
				break;
			default:
		}
		//ByPass le redirectSelect s'il on spécifie le paramètre
		if($url){
			ATF::$cr->setUrl($url);
		}
	}

	/**
	 * Permet d'obtenir la méthode de redirection utilisée après les évènements classiques (delete, insert, update, clone)
	 * @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	 * @param string event l'évènement désiré
	 * @return string l'évènement
	 */
	public function getDefaultRedirection($event){
		return $this->defaultRedirect[$event];
	}

	/**
	 * Permet d'obtenir l'url par défaut en fonction de l'id et du defaultRedirection
	 * @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	 * @param string $method Le nom de la méthode (insert, update, delete)
	 * @param int $id Si c'est un select par exemple
	 * @retun string L'url de la page
	 */
	protected function getDefaultUrl($method,$id=NULL){
		switch($this->getDefaultRedirection($method)){
			case "parent":
			case "select":
				if($id){
					return $this->table."-select-".$this->cryptId($id).".html";
				}
			default :
				return $this->table.".html";
		}
	}

	/** Retourne les prochaines actions pour l'opportunité demandée en paramètre
	 * @author Yann GAUTHERON <ygautheron@absystech.fr>
	 * @param $post
	 */
	public function extJSgsa(&$post,&$s=NULL){
		// Appel Base de données
		$this->updateSelectAll($post,$s);

		// Après cette ligne, on débloque la session par nous n'y écrirons plus
		ATF::getEnv()->commitSession();

		ATF::$json->add('totalCount',$post["q"]->nb_rows);

	 // Pas de refresh du div, c'est un grid, il lui faut seulement du javascript dans le json[result]
		unset($post["div"]);

		// Fichiers présents sur le filesystem
		$v = $this->q->getView();
		foreach ($v["order"] as $k => $i) {
			$col = $this->getColonne($k);
			if ($col["type"]==="file" || $col["renderer"]==="file") {
				$chkFiExist[$k]=true;
			}
		}

		// On met en valeur la chaîne recherchée dans les réponses
		//		$replacement = ATF::$html->fetch("search_replacement.tpl.htm","sr");
		foreach ($post["data"] as $k => $i) {
			foreach ($post["data"][$k] as $k_ => $i_) { // Mettre en valeur
				$from = $this->fk_from($k_);
				$classFrom = ATF::getClass($from);
				if (substr($k_,-3)==="_fk" || (strpos($k_,".id_")>-1 && !array_key_exists($k_."_fk",$post["data"][$k]))) {
					$post["data"][$k][$k_] = classes::cryptId($i_);
				} else {
					if ($post["query"] && is_string($post["data"][$k][$k_])) {
						//	$post["data"][$k][$k_] = util::searchHighlight($i_, $post["query"], $replacement);
						// Essai de le faire coté javascript
					}
					//$post["data"][$k]["raw_".$k_] = $i_;
				}
			}

			// Check si les fichier existents sur le filesystem, seulement si demandé dans cette vue
			foreach ($chkFiExist as $field => $val) {
				if ($this->file_exists($post["data"][$k][$this->table.".id_".$this->table],$field) || $this->files[$field]["no_store"]===true) {
					$t = $this->files[$field]["type"];
					if (!$t) {
						$t = "upload";
					}
					$post["data"][$k][$field]=$t;
				} elseif ($this->files[$field]['force_generate']===true && $this->files[$field]['type']=="pdf") {
					$post["data"][$k][$field] = 	array(
						"force_generate"=>true,
						"fonction"=>$this->files[$field]['fonction']?$this->files[$field]['fonction']:$this->table

					);
				}
			}
		}

		ATF::$cr->block("top");
		ATF::$cr->block("generationTime");
		return $post["data"];
	}

	/**
	 * Recherche et/ou met à jour un listing "select_all", doit mettre à jour seulement le {$div}_content et {$div}_pager
	 * Utilisation d'un querier de suppression
	 * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	 * @param array $post Normalement le $_POST
	 * @param array &$s La session
	 * @param array $files $_FILES
	 * @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	 * @return boolean TRUE si cela s'est correctement passé
	 */
	public function updateSelectAll(&$post,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL) {
		$post["url_extra"]=NULL;
		$post["extra"]=NULL;
		$post["view"]=($post["view"]?$post["view"]:NULL);
		$post["div"]=$post["pager"];
		$post["div_parent"]=$post["pager_parent"];
		$post["fk"]=NULL;
		if($post["parent_class"]){
			$post["parent_class"]=ATF::getClass($post["parent_class"]);
		}
		if($post["fk_name"] && $post["fk_value"]){
			$post["fk"]=array($post["fk_name"]=>$this->decryptId($post["fk_value"]));
		}

		$post["data"]=$this->genericSelectAll($post["div"],$post["parent_class"],$post["q"],$post["view"],$post["url_extra"],$post["extra"],$post["fk"],$post["function"],$s,false,false,$post["div_parent"]);


		$this->q->saveSQLForAggregate();

		//Raffraichissement
		if (is_array($cadre_refreshed)) {
			if(isset($post["table"]) && ATF::$html->template_exists($post["table"].".tpl.htm")){
				ATF::$cr->add($post["div"],$post["table"].".tpl.htm",$post);
			}elseif (isset($post["filter_key"])) {
				ATF::$cr->add($post["div"],"generic-select_all.tpl.htm",$post);
			}else{
				//ATF::$cr->add($post["div"]."_content","generic-select_all-content.tpl.htm",$post);
				//ATF::$cr->add($post["div"]."_pager",array("div"=>$post["div"]),"pager_pager.tpl.htm");
				//ATF::$cr->add($post["div"]."_pager","pager_pager.tpl.htm",$post);
			}
			ATF::$cr->block("generationTime");
			ATF::$cr->rm("top");
		}
	}

	/**
	 * Applique la pagination demandée dans le querier du pager
	 * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	 * @param array $post Normalement le $_POST
	 * @param array $div L'identifiant du requêteur
	 */
	private function genericSelectAllPager($div) {
		$post =& ATF::_p();

		// ExtJS
		if ($post["sort"]) {
			$post["sort"]=util::extJSUnescapeDot($post["sort"]);
			$post["order"] = $post["sort"];
			//unset($post["sort"]);
		}
		if ($post["dir"]) {
			$post["sens"] = $post["dir"];
			unset($post["dir"]);
		}
		if (isset($post["start"]) && isset($post["limit"])) {
			$post["page"]=ceil($post["start"]/$post["limit"]);
			unset($post["limit"],$post["start"]);
		}
		if (isset($post["page"]) || isset($post["order"]) && $post["order"] || isset($post["limit"])) {
			$q = ATF::_s("pager")->create($div);
			$q->setLimit($post["limit"]);
			if (isset($post["page"])) {
				$q->setPage($post["page"],$post["increase"]);
			}
			if ($post["order"]) {
				if (!isset($post["sort"]) && $post["order"]==$q->getOrderBrut()) {
					$q->switchSens();
				} else {
					if (!$post["sens"]) {
						$post["sens"] = "desc"; // Par défaut à l'envers, pour éviter de voir du vide en premier...
					}
					$q->reset('order')->addOrder($post["order"],$post["sens"]);
				}
			}
			//log::logger("genericSelectAllPager => ".$q->page,jgwiazdowski);
		}
	}

	/**
	 * Applique le filtre demandé dans le querier du pager
	 * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	 * @param array $post Normalement le $_POST
	 * @param string $div L'identifiant du requêteur
	 */
	private function genericSelectAllFilter($div) {
		$post =& ATF::_p();

		//dans le cas de grid extjs, il ne faut pas réinitialiser la pagination dans le cas où l'on envoie un filtre en post
		if(isset($post["filter_key"]) && $this->selectAllExtjs!==false){
			$this->genericSelectAllFilterApply($div,$post["filter_key"],false);
		}elseif (isset($post["filter_key"])) {
			$this->genericSelectAllFilterApply($div,$post["filter_key"]);
		} else {
			$q = ATF::_s("pager")->create($div);
			if(isset($q)) {
				if($filter_key = $q->getFilterKey()) {
					$this->genericSelectAllFilterApply($div,$filter_key,false);
				}
			}
		}
	}

	/**
	 * Applique le filtre demandé dans le querier du pager
	 * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	 * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	 * @param string $filter_key Clé
	 * @param string $div L'identifiant du requêteur
	 */
	private function genericSelectAllFilterApply($div,$filter_key,$resetPage=true) {

		if($filter_key){
			$filter = ATF::filtre_optima()->select(str_replace("public_","",$filter_key),"options");
		}
		ATF::_s("pager")->create($div)->setFilter($filter,$filter_key,$this);
		if ($resetPage) {
			ATF::_s("pager")->create($div)->setPage(0);
		}
	}

	/**
	* Applique la recherche demandée dans le querier du pager
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param array $post Normalement le $_POST
	* @param array $div L'identifiant du requêteur
	*/
	private function genericSelectAllSearch($div) {
		$post =& ATF::_p();

		// extJS
		if (isset($post["query"])) {
			if (!isset($post["limit"])) { // Le paramètre limit est envoyé par extJS seulement si on demande une page suivante
				$post["recherche"] = $post["query"];
			}
			unset($post["query"]);
		}
		if (isset($post["recherche"])) {
			if (strlen($post["recherche"])>0) {
				if(isset($post['champs'])){
					ATF::_s("pager")->q[$div]->setPage(0);
					ATF::_s("pager")->q[$div]->addSearch($post['champs'],stripslashes(urldecode($post["recherche"])));
				}else{
					ATF::_s("pager")->q[$div]->reset('search')->setPage(0);
					ATF::_s("pager")->q[$div]->setSearch(stripslashes(urldecode($post["recherche"])));
				}

				/* On défini les champs sur lesquels effectuer la recherche */
				if ($post["fields"]) {
					ATF::_s("pager")->q[$div]->addField($post["fields"]);
				}
			}else{
				if(isset($post['champs'])){
					ATF::_s("pager")->q[$div]->delSearch($post['champs'])->setPage(0);
				}else{
					ATF::_s("pager")->q[$div]->reset('search')->setPage(0);
				}
			}
		}
	}

	/**
	* Applique la fourchette de date demandée dans le querier du pager
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array $div L'identifiant du requêteur
	*/
	private function genericSelectAllBetweenDate($div) {
		$post =& ATF::_p();

		// extJS
		if (isset($post["between_begin"]) && isset($post["between_end"]) && isset($post["champs_date"])) {
			if (!isset($post["limit"])) { // Le paramètre limit est envoyé par extJS seulement si on demande une page suivante
				$post["fourchette"] = array("deb"=>$post["between_begin"],"fin"=>$post["between_end"]);
			}
			unset($post["between_begin"],$post["between_end"]);
		}

		if (isset($post["fourchette"])){
			ATF::_s("pager")->q[$div]->setPage(0);
			ATF::_s("pager")->q[$div]->setBetweenDate(array("champs_date"=>util::extJSUnescapeDot($post['champs_date'])
															,"debut"=>date("Y-m-d",strtotime($post["fourchette"]["deb"]))
															,"fin"=>date("Y-m-d",strtotime($post["fourchette"]["fin"]))));
		}

		if($post["sup_between_date"]){
			ATF::_s("pager")->q[$div]->reset('between_date')->setPage(0);
		}
	}

	/**
	 * Applique la vue sur le querier
	 * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	 * @param querier $q
	 * @param boolean $protectedView Protéger la vue d'un écrasement
	 */
	public function genericSelectAllView($q,$protectedView=false) {
		if(isset(ATF::$usr->custom["columns"][$this->table]['vue_custom'])){
			if (ATF::$usr->custom["columns"][$this->table]) {
				$view = ATF::$usr->custom["columns"][$this->table]; // Vue du filtre, sinon vue perso
			}
		}elseif ($q->hasView()) {
			$view = $q->getView();
		} else {
			$view = $this->view;
		}

		// Si toujours pas de vue, on prépare quand même la vue avec les colonnes par défaut
		if (!$view["order"]){
			$colonnes = $this->colonnes_simples(ATF::_s(),false,$view);
			//log::logger($colonnes,ygautheron);
			foreach ($colonnes as $k => $i) {
				//if (!$i["custom"]) {
					$view["order"][$i["alias"] ? $i["alias"] : $k]=$k;
				//}
			}
		}
		//log::logger($view,ygautheron);
		$q->setView($view,$protectedView);
	}

	/**
	 * Applique les foreignkeys au querier
	 * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	 * @param querier $q
	 * @param array $fk
	 * @return string $url_extra
	 */
	public function genericSelectAllFK($q,&$fk) {
//log::logger("[".md5(ATF::$id_thread)."] ".$this->table." genericSelectAllFK debut fk=".count($fk)." where=".count($q->getWhere()),ygautheron);
		if (!$fk) { // Si aucune FK, on stocke
			$fk = $q->getFk();
		}
		if ($fk) {
			$q->setFk($fk);
			foreach ($fk as $field => $value) {
				// on ajoute la clé de l'élément courant si il existe pour l'ajouter aux URL d'update et d'insert
				if ($this->fk_from($field)) {
					if ($url_extra) {
						$url_extra .= "&";
					}
					$url_extra .= str_ireplace($this->table.".","",$field)."=".self::cryptId($value);
				}

				// on ajoute les conditions lié aux FK défini au requeteur
				// avec le système d'overwrite
				$q->orWhere($field,$value,false,"=",true);
			}
		}
//log::logger("[".md5(ATF::$id_thread)."] ".$this->table." genericSelectAllFK fin fk=".count($fk)." where=".count($q->getWhere()),ygautheron);
		return $url_extra;
	}

	/**
	 * Interprète le nom du div s'il n'existe pas, ce nom est la base des querier dans le pager
	 * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	 * @param string $div
	 * @param mixed $parent_class
	 * @return string $div
	 */
	public function genericSelectAllDivName($div,$parent_class) {
		if (!$div) {
			// Balise div de référence pour cette pagination
			if ($parent_class) {
				$div="gsa_".$parent_class->table."_".$this->table;
			} else {
				$div="gsa_".$this->table."_".$this->table;
			}
		}

		return $div;
	}

	/**
	 * Recherche dans un listing "select_all", doit mettre à jour seulement le {$div}_content et {$div}_pager
	 * Utilisation d'un querier de suppression
	 * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	 * @param [IN/OUT] array &$div	Nom de référence de l'élement div du DOM, ce nom sera utilisé comme préfixe dans la plupart des div contenus à l'intérieur
	 * @param [IN/OUT] classes &$parent_class Classe parente d'un point de vue encapsulation de DIV côté DOM (par exempole une fiche select SOCIETE appelle des onglets enfants CONTACT, AFFAIRE... pour CONTACT le parent_class sera un objet SOCIETE)
	 * @param [OUT] querier &$q Requêteur qui doit être récupéré dans le PAGER
	 * @param [OUT] array &$view Vue récupérée de la session dans le champ custom du user, sinon la vue par défaut définie dans l'objet de la classe courante
	 * @param [OUT] array &$url_extra Ces arguments supplémentaire sseront ajoutés aux URL d'ajout et d'insert...
	 * @param [OUT] array &$extra Ces arguments seront ajoutés aux mises à jour de listings
	 * @param [IN] array $fk Les clés étrangères à utiliser pour filtrer les enregistrements
	 * @param [IN] array $function Fonction à exécuter (en général par defaut select_all)
	 * @param [IN/OUT] array &$s La session
	 * @param [IN] boolean $reinit permet de savoir si cet appel provient d'une réinitialisation (auquel cas, sécurité mis en place contre le bouclage)
	 * @return array $data les données trouvées
	 */
	public function genericSelectAll(&$div,&$parent_class,&$q,&$view,&$url_extra,&$extra,$fk,$function,&$s,$no_limit=false,$reinit=false,$div_parent=NULL) {
		/* Nomination du div */
		$div = $this->genericSelectAllDivName($div,$parent_class);

		/* Gestion de la pagination */
		$this->genericSelectAllPager($div);

		/* Filtrage */
		$this->genericSelectAllFilter($div);

		/* Recherche */
		$this->genericSelectAllSearch($div);

		/* Fourchette date */
		$this->genericSelectAllBetweenDate($div);

		// Sauvegarde dans le custom
		ATF::$usr->setDefaultFilter($div,($div_parent?$div_parent:$div),$this);

		// Requêteur fourni par le pager (le pager conserve toujours l'état courant par rapport à la clé "nom du div de référence")
		$q = ATF::_s("pager")->create($div,NULL,true);

		//log::logger("genericSelectAll => ".$q->page,ygautheron);

		// On prépare aussi la vue, c'est à dire les colonnes vues, alignement, etc...
		$this->genericSelectAllView($q);

		// On get à nouveau, car si la vue est protégée, elle n'est pas modifiée, sinon le $view serait erroné
		$view = $q->getView();
		// Iitialisation du requêteur pour l'appel aux données via SQL
		$q->reset('field')->addField($this->colonnes_simples($s,false,$view,$div));

		if(!$no_limit){
			if ($limit=ATF::_p('limit')) {
				$q->setLimit($limit);
			} elseif (!$q->limit["limit"]) {  // Si aucune limite, on met la limite par défaut
				$q->setLimit(defined('__RECORD_BY_PAGE__')?__RECORD_BY_PAGE__:30);
			}
		}

		// Clés étrangères de filtrage éventuel
//log::logger("[".md5(ATF::$id_thread)."] ".$this->table." genericSelectAll = ".$div." | fk=".count($q->getFk())." where=".count($q->getWhere()),ygautheron,true);
		$url_extra = $this->genericSelectAllFK($q,$fk);
		$this->setQuerier($q); // On applique ce requêteur à la classe courante

		// Paramètres aux URL de mise à jour de listing
		$extra="table=".urlencode($this->name())."&function=".$function;

		if($url_extra){
			preg_match("`(.*)=(.*)`",$url_extra,$condition);
			$extra.="&fk_name=".$this->table.".".$condition[1]."&fk_value=".$condition[2];
		}

		try{
			return $q->query($this->select_data($s,$function));
		}catch(errorATF $e){
			//si la requête renvoie une erreur, on regarde si il y a un filtre concerné
			//à la demande de Yann, n'est géré que pour le 1054
			if($e->getErrno()=="1054" && !$reinit){
				$post=ATF::_p();
				//si c'est le cas on réinitialise les données
				if (isset($post["filter_key"])) {
					return ATF::$usr->reinitFiltre($this->table,$post["filter_key"],$div,$parent_class,$q,$view,$url_extra,$extra,$fk,$function,$s,$no_limit);
				} else {
//					if (!$q) $q = ATF::_s("pager")->create($div);
//					print_r($q);
//					if(isset($q)) {
//						if($filter_key = $q->getFilterKey()) {
//							return ATF::$usr->reinitFiltre($this->table,$filter_key,$div,$parent_class,$q,$view,$url_extra,$extra,$fk,$function,$s,$no_limit);
//						}
//					}
				}
			}
		}
	}

	/**
	 * Prépare la vue avec la méthode spécifique si elle existe
	 * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	 * @return querier $q
	 * @return string $function
	 */
	public function prepareView($q,$function) {
		if ($function && method_exists($this,$function)) {
			$this->$function($s,$q);
		}
	}

	/**
	 * Vide la table de ses enregistrements
	 * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	 * @return mixed Résultat de la requête d'effacement
	 */
	public function truncate() {
		return ATF::db($this->db)->truncate($this->table);
	}

	/**
	 * Donne la valeur de l'attribut passé en paramètre
	 * @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	 * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	 * @param string $attribute
	 * @return mixed Résultat de l'attribut
	 */
	public function get($attribute) {
		if($this->isSingleton()) {
			if (property_exists($this, $attribute)){
				return $this->$attribute;
			}else{
				throw new errorATF("attribute_dont_exists");
			}
		} else {
			return $this->infos[$attribute];
		}
	}

	/**
	 * Met à jour une valeur d'attribut
	 * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	 * @param string $attribute
	 * @param string $value
	 * @return mixed Résultat de la requête d'effacement
	 */
	public function set($attribute,$value) {
		if($this->isSingleton()) {
			if (property_exists($this, $attribute)){
				$this->$attribute = $value;
				return true;
			}else{
				throw new errorATF("attribute_dont_exists");
			}
		} else {
			$this->infos[$attribute]=$value;
			return $this->u(array($attribute=>$value));
		}
	}

	/**
	 * Retourne les infos d'une seule colonnes, ca la chercher dans n'importe quel panel
	 * @author Yann GAUTHERON <ygautheron@absystech.fr>
	 * @param string field : Colonne recherchée
	 * @return array
	 */
	public function getColonne($field,$colonnes=NULL) {
		if ($colonnes===NULL) {
			$colonnes = $this->colonnes;
			$main=true; // Recherche la plus haute (niveau récursif initial)
		}

		if (!array_key_exists($field,$this->cache[__FUNCTION__])) {
			// A la première demande, on crée en cache la correspondance
			foreach ($colonnes as $k => $item) {
				if ($k==="bloquees") continue; // Pas de structure dans les colonnes bloquées
				if ($k===$field && is_array($item) && (is_string($item["type"]) || $item["alias"]===$field || $item["custom"])) {
					$this->cache[__FUNCTION__][$field] = $item;
					return $item;
				} elseif (is_array($item)) {
					if ($resultat = $this->getColonne($field,$item)) {
						$this->cache[__FUNCTION__][$field] = $resultat;
						return $resultat;
					}
				}
			}
			if ($main) {
				$this->cache[__FUNCTION__][$field] = NULL;
			}
		}
		return $this->cache[__FUNCTION__][$field];
	}

	/**
	 * Retourne les attributs de ce champ, en cherchant aussi dans les alias
	 * @param string $field
	 * @author Yann GAUTHERON <ygautheron@absystech.fr>
	 * @return array Attributs
	 */
	public function field_column($field) {
		if ($return = $this->getColonne($field)) {
			return $return;
		}
		$pos = strpos($field,".");
		if ($pos>-1) {
			return $this->getColonne(substr($field,$pos+1));
		}
		//		if(is_array($this->colonnes["fields_column"])){
		//			foreach ($this->colonnes["fields_column"] as $k => $i) {
		//				if ($k===$field || $k===substr($field,strpos($field,".")+1) || isset($i["alias"]) && $i["alias"]===$field) {
		//					return $i;
		//				}
		//			}
		//		}
	}

	/**
	 * Crée le conteneur des shorcuts (lors d'un clic sur le menu shortcut)
	 * @param array $infos Les infos passées par le contrôleur
	 */
	public function createShorcutContainer($infos){
		$this->infoCollapse($infos);
		ATF::$cr->block("top");
		ATF::$cr->block("generationTime");
		ATF::$cr->block("main");
		ATF::$cr->add("contentContainer",$infos["tpl"]);
	}

	//	/**
	//	* Création de l'URL Optima avec encryption en AES
	//	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	//	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	//	* @param int $id_hotline L'identifiant hotline
	//	* @return string l'url
	//	*/
	//	public function createOptimaURL($id){
	//		$id = $this->decryptId($id);
	//		$aes_tmp=new aes();
	//		$id = $aes_tmp->crypt($id);
	//		$url = __MANUAL_WEB_PATH__.'?url='.base64_encode('table='.$this->table.'&event=select&id_'.$this->table.'='.$id.'&seed='.$aes_tmp->getIV().$aes_tmp->getKey());
	//		$aes_tmp->endCrypt();
	//		return $url;
	//	}

	/**
	 * Retourne le chemin vers le fichier
	 * @param int $id Clé primaire d'enregistrement associé NE GERE PAS LES ID en MD5
	 * @param string $field Nom du champ associé
	 * @author Yann GAUTHERON <ygautheron@absystech.fr>
	 */
	public function filepath($id,$field=NULL,$temp=false,$codename=false) {
		$field = basename($field);
		if ($id) {
			$target_filename = $this->decryptId($id);
		}
		if ($field) {
			$target_filename .= ".".$field;
		}

		if ($temp===true) {
			$path = __TEMP_PATH__;
		}elseif($temp=="trash"){
			$path = __TRASH_PATH__;
		} else {
			$path = __DATA_PATH__;
		}
		if (ATF::isTestUnitaire()){
			return $path."testsuite/".$this->table."/".$target_filename;
		} elseif ($codename) {
			return $path.$codename."/".$this->table."/".$target_filename;
		} else {
			return $path.ATF::$codename."/".$this->table."/".$target_filename;
		}
	}

	/**
	 * Retourne le fichier
	 * @author Yann GAUTHERON <ygautheron@absystech.fr>
	 * @param array $request
	 */
	public function readfile($request) {
		return file_get_contents($this->filepath($this->decryptId($request["event"]),$request["id"]));
	}

	/**
	 * Prédicat qui retourne VRAI dans le cas où la clé primaire de la classe passée en paramètre est dans les fields_columns
	 * @author Yann GAUTHERON <ygautheron@absystech.fr>
	 * @param classes class : Classe de laquelle on a déjà la clé
	 * @return boolean Retourne VRAI si on a deja la clé dans les fields_colums
	 */
	public function alreadyHasItsForeignKey(classes $class) {
		$event = ATF::$controller->getEvent(ATF::_r());
		$fk = "id_".$class->table;

		// Primaire
		foreach ($this->colonnes("primary",$event) as $field => $i_) {
			if ($field===$fk) {
				return true;
			}
		}

		// Panels
		foreach ($this->colonnes["panel"] as $k => $i) {
			foreach ($this->colonnes($k,$event,'true') as $field => $i_) {
				if ($field===$fk) {
					return true;
				}
			}
		}

		// Champs secondaires
		foreach ($this->colonnes("secondary",$event) as $field => $i_) {
			if ($field===$fk) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Permet de passer un num de type 12345.87 => 12 345.87
	 * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	 * @param number float : Number à formater
	 * @param num_decimal_places int   : Nombre de chiffre après la virgule
	 * @param dec_separator  string  : Séparateur décimal
	 * @param thousands_separator string  : Séparateur millier
	 * @return string valeur formaté
	 */
	public function formatNumeric($number,$num_decimal_places=2,$dec_separator=".",$thousands_separator=" "){
		return number_format($number,$num_decimal_places,$dec_separator,$thousands_separator);
	}

	/**
	 * Suppression des fichiers liés à cette table
	 * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	 * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	 * @author Yann GAUTHERON <ygautheron@absystech.fr>
	 * @param int $id de l'élément supprimé
	 * @return boolean
	 */
	public function delete_files($id){
		if(is_array($this->files)){
			$nb_delete_file=0;
			foreach($this->files as $key=>$item){
				if($this->delete_file($id,$key)){
					$nb_delete_file++;
					if($name){
						$name.=" - ";
					}
					$name= $name.\ATF::$usr->trans($key)." (".\ATF::$usr->trans($this->table).")";
				}
			}
			if(!ATF::db($this->db)->isTransaction()){
				if($nb_delete_file>0){
					ATF::$msg->addNotice(loc::mt(ATF::$usr->trans("fichiers_supprimes"),array("nb"=>$nb_delete_file,"name"=>$name)));
				}
			}
			return true;
		}else{
			return false;
		}
	}

	/**
	 * Suppression d'un fichier lié à une table
	 * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	 * @author Quentin JANON <qjanon@absystech.fr>
	 * @author Yann GAUTHERON <ygautheron@absystech.fr>
	 * @param int $id de l'élément supprimé
	 * @param string $key de l'élément supprimé
	 * @return boolean
	 */
	public function delete_file($id,$key){
		$path = $this->filepath($this->decryptId($id),$key);
		if(file_exists($path)){
			if(!ATF::$tracabilite){
				if (@unlink($path)) {
				// Si je ne suis pas dans la traçabilité donc je supprime
					ATF::$msg->addNotice(loc::mt(ATF::$usr->trans("fichiers_supprimes"),array("nb"=>1,"name"=>ATF::$usr->trans($key)." (".ATF::$usr->trans($this->table).")")));
					return true;
				} else {
					ATF::$msg->addWarning(loc::mt(ATF::$usr->trans("attention_fichier_non_supprime"),array("table"=>$this->table,"field"=>$key,"id"=>$id)));
					return false;
				}
			} elseif (ATF::db($this->db)->isTransaction()){
				// En transaction on délaye la suppression au moment futur du commit
				ATF::db($this->db)->getQueue()->deleteFile($id,$this->table,$key);
				return true;
			}else{
				// Sinon la traçabilité s'en charge
				return ATF::tracabilite()->filesToAttach(array("id_".$this->table=>$id),$this->table,true,$key);
			}
		} elseif (ATF::db($this->db)->isTransaction()){
			// En transaction on délaye la suppression au moment futur du commit
			ATF::db($this->db)->getQueue()->deleteFile($id,$this->table,$key);
			return true;
		} else {
			return false;
		}
	}

	/**
	 * RAZ du cache ATF
	 * @author Quentin JANON <qjanon@absystech.fr>
	 * @param string $function nom de la fonction
	 * @param string $key de l'élément supprimé
	 * @return boolean
	 */
	function resetCache($function,$field=false,$id=false) {
		if ($id) {
			unset($this->cache[$function][$field][$id]);
		} elseif ($field) {
			unset($this->cache[$function][$field]);
		} else {
			unset($this->cache[$function]);
		}
	}

	/**
	 * Setter du cache ATF
	 * @author Quentin JANON <qjanon@absystech.fr>
	 * @param string $value valeur
	 * @param string $function nom de la fonction
	 * @param string $key de l'élément supprimé
	 * @param string $id id
	 * @return boolean
	 */
	function setCache($value,$function,$field=false,$id=false) {
		if ($id) {
			$this->cache[$function][$field][$id] = $value;
		} elseif ($field) {
			$this->cache[$function][$field] = $value;
		} else {
			$this->cache[$function] = $value;
		}
	}

	/**
	 * Getter du cache ATF
	 * @author Quentin JANON <qjanon@absystech.fr>
	 * @param string $value valeur
	 * @param string $function nom de la fonction
	 * @param string $key de l'élément supprimé
	 * @param string $id id
	 * @return boolean
	 */
	function getCache($function,$field=false,$id=false) {
		if ($id) {
			return $this->cache[$function][$field][$id];
		} elseif ($field) {
			return $this->cache[$function][$field];
		} else {
			return $this->cache[$function];
		}
	}

	/**
	 * Ajoute un privilege sur une méthode, utilisé pour les appels Ajax à des méthodes PHP
	 * @param string $methode
	 * @param string $privilege
	 * @author Yann GAUTHERON <ygautheron@absystech.fr>
	 */
	protected function addPrivilege($method,$privilege="select") {
		$this->eventPrivilegeMap[$method]=$privilege;
	}

	/**
	 * Filtrage général d'information des listings (par exemple selon le profil)
	 * Est appelée par le globalSearch et select_all
	 * @author Yann GAUTHERON <ygautheron@absystech.fr>
	 */
	protected function saFilter(){

	}
}
