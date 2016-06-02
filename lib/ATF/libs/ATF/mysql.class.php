<?php
/**
* La classe db hérite de mysqli, elle permet l'accès
* aux données MySQL.
*
* @date 2008-11-03
* @package ATF
* @version 5
* @abstract db (Base de données)
* @author Yann GAUTHERON <ygautheron@absystech.fr>
*/
include_once dirname(__FILE__)."/queue.class.php";
class mysql extends mysqli implements db {
	private $nb_query=0;//Nombre de requête exécutées
	private $nb_error_query=0;//Nombre de requêtes non exécutées
	private $all_tables=NULL;//L'ensemble des tables de la base en cours (utilisé pour éviter les show tables successif)
	private $lock_transaction=0;//Bloque les transactions
	private $defaultCharset="";//Charset par défaut installé sur le serveur
	public $logSQL=true;//Log ou non la requête

	// Paramètres de connexion
	private $host;
	private $login;
	private $password;
	private $database;
	private $port;


	/* Constructeur, sauve les paramètres
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $host
	* @param string $login
	* @param string $password
	* @param string $database
	* @param string $port
	*/
	public function __construct($host,$login,$password,$database,$port) {
		$this->host = $host;
		$this->login = $login;
		$this->password = $password;
		$this->database = $database;
		$this->port = $port;

		$mysql=parent::__construct($host,$login,$password,$database,$port);

		if (mysqli_connect_error()) {
			log::logger("Erreur : (".mysqli_connect_errno().")".mysqli_connect_error(),__CLASS__);
			switch(mysqli_connect_errno()){
				case "1045":
					log::logger(" Problème de droit sur login/password",__CLASS__);
					break;
				case "1049":
					log::logger(" Problème de base de données : base inexistante ('".$database."')",__CLASS__);
					break;
				case "2002":
					log::logger(" Problème sur le host ('".$host."')",__CLASS__);
					break;
			}
		}
		return $mysql;
	}

	public function __destruct() {
		log::logger("DESTRUCTION OBJET MYSQL - Thread : ".ATF::$id_thread,"allSql.log");
	}

	/**
	* Initialisation des constantes stockées dans la base en constantes PHP
	* @package ATF
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	*/
	public function setCharset($charset="utf8") {
		if ($charset!=$this->defaultCharset) {
			/* Connection UTF8 certifiée */
			$query = "SET NAMES ".$charset.";";
			$this->query($query);
		}

		/* Problème des soustractions inférieures à 0 sur UNSIGNED */
//		$query = "SET sql_mode = 'NO_UNSIGNED_SUBTRACTION';";
//		$this->query($query);
	}

//	/**
//	* Désactive les clées étrangères
//	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
//	*/
//	public function disableForeignKey(){
//		$query = "SET foreign_key_checks = 0";
//		$this->query($query);
//	}
//
//	/**
//	* Active les clées étrangères. Par défaut c'est ce mode qui est utilisé par mysql. Inutile d'utiliser cette méthode toute seule !
//	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
//	*/
//	public function enableForeignKey(){
//		$query = "SET foreign_key_checks = 1";
//		$this->query($query);
//	}

	/* Informations sur la dernière requête executée
	* @author Mathieu Tribouillard <mtribouillard@absystech.fr>, Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @return string
	*/
	public function report() {
		//chr(45) correspond au caractère "-", c'est le code ASCII décimal de la lettre
		$report = $this->query;
		if ($this->error) {
			$report = "[SQL Error] : ".$this->errno." : ".$this->error." [Query] : ".$this->query;
			switch($this->errno){
				case "1062":
					preg_match_all("/`.[^`]*`/",$this->query,$tab1);
					preg_match_all("/'.[^']*'/",$this->error,$tab2);
					$multi=preg_match("`\) VALUES \(`",$this->query);
					$table=str_replace("`","",$tab1[0][0]);
					$key=$tab2[0][1];
					$fields=$this->desc($table);
					$index=$this->showIndex($table);
					foreach($index as $item){
						if($item["Key_name"]==str_replace("'","",$key)){
							$keyContraints[]=$item["Column_name"];
						}
					}
					$nb=0;

					foreach($index as $item){
						if($item["Key_name"]==str_replace("'","",$key)){
							$nb++;
							if($nb>1){
								$valeurs.=" , ";
								$champs.=" , ";
							}

							unset($preg_champ);
							preg_match_all('/`'.$item["Column_name"].'` = ".[^"]*"/',$report,$preg_champ);


							//S'il n'y a pas de valeur pour ce champs dans l'insert et qu'il y a une valeur par défaut alors il faut récupérer la valeur par défaut
							if(!(str_replace('`'.$item["Column_name"].'` = ',"",$preg_champ[0][0])) && $fields[$item["Column_name"]]["Default"]){
								//Si c'est un CURRENT_TIMESTAMP il faut récupérer la date de l'error
								if($fields[$item["Column_name"]]["Default"]=="CURRENT_TIMESTAMP"){
									foreach($keyContraints as $k => $keyContraint){
										if($keyContraint==$item["Column_name"]){
											$keyContraintNb=$k;
										}
									}
									preg_match_all("/[0-9]*-[0-9]*-[0-9]* [0-9]*:[0-9]*:[0-9]*/",$tab2[0][0],$tabcontraint);
									$valeurs.=$tabcontraint[0][$keyContraintNb];
								//Sinon on récupère la date par défaut de la base
								}else{
									$valeurs.=$fields[$item["Column_name"]]["Default"];
								}
							}else{
								if($itemTable=ATF::getClass($table)->fk_from($item["Column_name"])){
									$valeurs.='"'.ATF::getClass($itemTable)->nom(str_replace('`'.$item["Column_name"].'` = "',"",$preg_champ[0][0])).'"';
								}else{
									$valeurs.=str_replace('`'.$item["Column_name"].'` = ',"",$preg_champ[0][0]);
								}
							}
							$champs.=ATF::$usr->trans($item["Column_name"]);
						}
					}

					if($multi){
						$report=loc::mt(
							ATF::$usr->trans("mysql_duplicate_entry"),
							array(
								"valeur"=>str_replace("'","",$tab2[0][0]),
								"champs"=>$champs,
								"table"=>ATF::$usr->trans($table)
							)
						);
					}elseif($nb==1){
						$report=loc::mt(
							ATF::$usr->trans("mysql_duplicate_entry"),
							array(
								"valeur"=>str_replace("'","",$tab2[0][0]),
								"champs"=>ATF::$usr->trans(str_replace("'","",$key),$table),
								"table"=>ATF::$usr->trans($table)
							)
						);
					}else{
						$report=loc::mt(
							ATF::$usr->trans("mysql_duplicate_entry_multiple"),
							array(
								"valeurs"=>$valeurs,
								"champs"=>$champs,
								"table"=>ATF::$usr->trans($table)
							)
						);
					}
				break;
			}
		} else {
			$report .=chr(45).$this->affected_rows;
			if ($this->info) {
				$report .= " affected (".$this->info.")";
			}
			if ($this->insert_id) {
				$report .= chr(45)."insert_id = ".$this->insert_id;
			}
		}
		//$report .= "\n".$this->get_warnings();
		return $report;
	}

	/** Permet de connaître les index d'une table
	* @param string $table
	* @return array index
	*/
	public function showIndex($table) {
		$query="SHOW INDEX FROM ".$table.";";
		return $this->sql2array($query);
	}

	/** Retourne les contraintes d'une table
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param string $table
	* @return array return_constraints
	*/
	public function showConstraint($table) {

		$id_table="id_".$table;

		$constraints = ATF::db($this->db)->fetch_foreign_keys_constraints();
		//Pour toutes les tables
		foreach($constraints as $table => $table_constraints){
			//Pour toutes les contraintes de chaque table
			foreach($table_constraints  as $k => $constraint){
				//Si une contrainte se fait sur la clé primaire
				if($constraint["foreign_key"]==$id_table){
					$return_constraints[$table][]=$constraint;
				}
			}
		}
		if($return_constraints){
			return $return_constraints;
		}else{
			return false;
		}
	}

	/** Retourne les enregistrements liés à la contrainte d'une table
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $constraints fournit par showConstraint()
	* @return array index
	*/
	public function showDeleteConstraint($constraints,$id,$type="cascade") {
		if($type=="cascade"){
			$type=array(0=>"onDeleteCascade",1=>"onDeleteSetNull");
		}else{
			$type=array(0=>"onDeleteRestrict",1=>"onDeleteNoAction");
		}
		$nb=0;

		//Pour toutes les tables
		foreach($constraints as $table => $table_constraints){
			//Pour toutes les contraintes de chaque table
			foreach($table_constraints  as $k => $constraint){
				//S'il y a une suppression en cascade
				if($constraint[$type[0]] || $constraint[$type[1]]){
					$class = ATF::getClass($table);
					$class->q->reset()->addCondition($table.'.'.$constraint["key"],$id);
					if($enregistrement_contraint=$class->sa()){
						foreach($enregistrement_contraint as $enregistrement){
							if($constraint[$type[0]]){
								$enregistrement_supprime[$type[0]][]=array("nom"=>$class->nom($enregistrement["id_".$table]),"table"=>$table);
							}else{
								$enregistrement_supprime[$type[1]][]=array("nom"=>$class->nom($enregistrement["id_".$table]),"table"=>$table);
							}
							$nb++;
							if($nb>50){
								foreach($constraints as $tableReturn => $table_constraintsReturn){
									//Pour toutes les contraintes de chaque table
									foreach($table_constraintsReturn  as $kReturn => $constraintReturn){
										//S'il y a une suppression en cascade
										if($constraintReturn[$type[0]] || $constraintReturn[$type[1]]){
											$enregistrement_supprime["enregistrementSuperieurTable"][$tableReturn]= " '".ATF::$usr->trans($tableReturn,"module")."'";
										}
									}
								}

								$enregistrement_supprime["enregistrementSuperieur"]=true;
								return $enregistrement_supprime;
							}
						}
					}
				}
			}
		}
		if($enregistrement_supprime){
			return $enregistrement_supprime;
		}else{
			return false;
		}
	}

	/** Métohde query
	* @param string $query
	* @param string $resultmode
	* @return resource
	*/
	public function query($query,$resultmode=MYSQLI_STORE_RESULT) {
		return $this->q($query,$resultmode);
	}

	/**
	* Execute une muti requête
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param string $query
	* @return resource
	* @todo : debloquer le throw $e et trouver la faille lorsque l'on fait un speed_insert de contact sur ginger dans suivi
	*/
	public function multi_query($query){
		return $this->q($query,MYSQLI_STORE_RESULT,true);
	}

	/**
	* Envoie les headers de temps passé en SQL
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	*/
	public function makeHeaders() {
		if (ATF::$debug && $this->total_time) {
			header('ATF-SQL-total-time: '.round($this->total_time)."ms");
		}
	}

	/**
	* Execute une requête/multi-requête, et peut journaliser les erreurs
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param string $query
	* @param string $resultmode
	* @param boolean $multi_query true si on veut exécuter une multi-query
	* @return resource
	*/
	private function q($query,$resultmode=MYSQLI_STORE_RESULT,$multi_query=false) {
		if (is_string($query)) {
			$this->query = $query;
//			ATF::$analyzer->flag("SQL");
			//Exécution de la requête
			$microtime = microtime(true);
			if(!$multi_query){
//log::logger('[BEFORE] - Thread : '.ATF::$id_thread.' - query :'.$this->query,'allSql.log',false,true);
//log::logger($this->query,'allSql.'.(ATF::$usr ? ATF::$usr->getLogin() : ""),false,true,NULL,false);
				$return = parent::query($query,$resultmode);
			}else{
				$return = parent::multi_query($query);
			}
			$this->nb_query++;
//			ATF::$analyzer->end("SQL");
			$this->last_time = (microtime(true)-$microtime)*1000;
			$this->total_time += $this->last_time;

			if ($this->logSQL) {
				$s = '['.$this->host.'] '.'['.$this->nb_query.'/err:'.$this->nb_error_query.'] '.number_format($this->last_time,0,"."," ").'ms nb:'.$this->affected_rows.' '.$this->query;
				if (ATF::$debug && $this->last_time>300) {
					header('ATF-SQL-slow-'.$this->nb_query.': '.$s);
				}
				log::logger(ATF::$id_thread.' '.$s,'allSql.log',false,true);
			}

			// Gestion d'erreur
			$this->errorProcess();

			return $return;
		} else {
			throw new errorSQL(loc::ation("bad_query_type"));
		}
	}

	/**
	* Procédure à effectuer en cas d'erreur détectée (appelé après une query, un commit ou un autocommit(true))
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @return void
	*/
	public function errorProcess() {
		if($this->error){
			//if($this->lock_transaction>0){
				//$this->rollback();
				//$this->lock_transaction=0;
			//}
			$this->nb_error_query++;
			//log::logger('[Requete '.$this->nb_query.'/err:'.$this->nb_error_query.'] - Thread : '.ATF::$id_thread.' - ERREUR : '.$this->error.' - query :'.$this->query,'errorSql.log',false,true);
			log::logger('[Requete '.$this->nb_query.'/err:'.$this->nb_error_query.'] - Thread : '.ATF::$id_thread.' - ERREUR : '.$this->errno." - ".$this->error,'allSql.log',false,true);
			if (ATF::getDebug() || $this->errno==1062) {
				// Sauvegarde de l'errno, sinon il saute dans le report parce que la fonction execute du SQL
				$errno = $this->errno;
				// On peut très bien sauvegarder d'autre infos a l'occasion
				throw new errorSQL($this->report(),$errno);
			} else {
				if (ATF::$usr) {
					$message = ATF::$usr->trans("erreur_sql_".$this->errno);
				} else {
					$message = loc::ation("erreur_sql_".$this->errno);
				}
				throw new errorSQL($message,$this->errno);
			}
		}
	}

	/**
	* Retourne toutes les tables de la base de données
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @return array
	*/
	public function all_tables() {
		if(!is_array($this->all_tables)){
			$query = "SHOW TABLES";
			$all_tables = $this->arr($query);
			$this->all_tables=array_map("array_shift",$all_tables);
//log::logger('[Thread: '.ATF::$id_thread.' St req:'.$this->nb_query.' empty:'.empty($this->all_tables).']','jgwiazdowski');
//log::logger($this->all_tables,'jgwiazdowski');
		}
		return $this->all_tables;
	}

	/**
	* Retourne un tableau PHP des possibilité d'un champ de types énumérés
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @modified by Nicolas BERTEMONT <nbertemont@absystech.fr> and Jérémy Gwiazdowski <jgw@absystech.fr>
	* @param string $table Table
	* @param string $field Champ
	* @return array
	*/
	public function enum2array(&$class,$field) {
		//On récupère dans le singleton la struture de la table (fameux desc)
		if(!$class->desc){
			$class->desc=$this->desc($class->table);
		}

		if ($class->desc) {
			foreach($class->desc as $resultat) {
				if ($resultat["Field"]==$field) {
					if (substr($resultat["Type"],0,3)=="set") {
						$resultat["Type"]=substr($resultat["Type"],5,strlen($resultat["Type"])-5-2);
					} else {
						$resultat["Type"]=substr($resultat["Type"],6,strlen($resultat["Type"])-6-2);
					}
					return explode("','",$resultat["Type"]);
				}
			}
		}
	}

	/**
	* Retourne le premier champ du premier enregistrement retourné par MySQL, utilisé habituellement lorsqu'on est certain de ne trouver qu'une seule ligne, et un seul champ
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $query requête
	* @return string Résultat de la requête
	*/
	public function fetch_first_cell($query) {
		$data = $this->fetch_array_once($query);
		return array_shift($data);
	}

	/**
	* Alias de fetch_first_cell
	*/
	public function ffc($query) {
		return $this->fetch_first_cell($query);
	}

	/**
	* Retourne le premier enregistrement retourné par MySQL, utilisé habituellement lorsqu'on est certain de ne trouver qu'une seule ligne.
	* Le résultat est retourné sous la forme d'un tableau associatif ET numérique
	* Exemple : array(0=>"12","id_user"=>"12",1=>"abalam","login"=>"abalam"...)
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $query requête
	* @return array Résultat de la requête
	*/
	public function fetch_array_once($query) {
		$result=$this->query($query);
		return $result->fetch_array();
	}

	/**
	* Alias de fetch_array_once
	*/
	public function farro($query) {
		return $this->fetch_array_once($query);
	}

	/**
	* Retourne le premier enregistrement retourné par MySQL, utilisé habituellement lorsqu'on est certain de ne trouver qu'une seule ligne.
	* Le résultat est retourné sous la forme d'un tableau associatif seulement
	* Exemple : array("id_user"=>"12","login"=>"abalam"...)
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $query requête
	* @return array Résultat de la requête
	*/
	public function fetch_assoc_once($query) {
		$result=$this->query($query);
		return $result->fetch_assoc();
	}

	/**
	* Alias de fetch_assoc_once
	*/
	public function fasso($query) {
		return $this->fetch_assoc_once($query);
	}

	/**
	* Retourne une ressource mysql en tant que tableau PHP indicé ou non selon le champ demandé
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param resource $result Ressource résultat d'une requête MySQL
	* @param string $id Si défini indice le tableau par la valeur du champ défini par cette chaîne de caractère $id
	* @param string $infos Si défini ne retourne qu'un tableau à 1 dimension avec pour données la valeur du champ défini par cette chaîne de caractère $infos
	* @param boolean $assoc VRAI retourne un tableau associatif, FAUX retourne un tableau indicé par des entiers croissants
	* @return array Résultat de la requête
	*/
	public function res2array(&$result,$id=NULL,$infos=NULL,$assoc=true) {
		if ($assoc)
			$fetch=MYSQLI_ASSOC;//"fetch_assoc";
		else
			$fetch=MYSQLI_NUM;//"fetch_array";
		$res=array();
		if ($id) { // Indice a partir de la valeur d'un champ $id
			if ($infos) { // Ne retourner que la valeur du champ $infos pour chaque enregistrement
				while($resultat=$result->fetch_array($fetch)) {
					$res[$resultat[$id]]=$resultat[$infos];
				}
			} else {
				while($resultat=$result->fetch_array($fetch)) {
					$res[$resultat[$id]]=$resultat;
				}
			}
		} else {
			while($resultat=$result->fetch_array($fetch)) {
				$res[]=$resultat;
			}
		}
		return $res;
	}

	/**
	* Retourne une résultat mysql en tant que tableau PHP indicé ou non selon le champ demandé à partir d'une requête SQL
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $query Requête SQL
	* @param string $id Si défini indice le tableau par la valeur du champ défini par cette chaîne de caractère $id
	* @param string $infos Si défini ne retourne qu'un tableau à 1 dimension avec pour données la valeur du champ défini par cette chaîne de caractère $infos
	* @param boolean $assoc VRAI retourne un tableau associatif, FAUX retourne un tableau indicé par des entiers croissants
	* @return array Résultat de la requête
	*/
	public function sql2array($query,$id=NULL,$infos=NULL,$assoc=true) {
		$result = $this->query($query);
		if (!$result->num_rows) return;
		return $this->res2array($result,$id,$infos,$assoc);
	}

	/**
	* Alias de sql2array
	*/
	public function arr($query,$id=NULL,$infos=NULL,$assoc=true) {
		return $this->sql2array($query,$id,$infos,$assoc);
	}

	/**
	* Retourne un listing en tableau PHP et de son total d'enregistrements sollicités en utilisant une multi-query
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $query String qui contient une première requête SQL avec SQL_CALC_FOUND_ROWS et une seconde étant SELECT FOUND_ROWS();
	* @param string $id Si défini indice le tableau par la valeur du champ défini par cette chaîne de caractère $id
	* @param string $infos Si défini ne retourne qu'un tableau à 1 dimension avec pour données la valeur du champ défini par cette chaîne de caractère $infos
	* @param boolean $assoc VRAI retourne un tableau associatif, FAUX retourne un tableau indicé par des entiers croissants
	* @return array Résultat de la requête
	* 		array(
	*			"data" => données résultantes
	*			,"count" => nombre de données sollicitées au total par cette requête
	*		)
	*/
	public function multi2array(&$query,$id=NULL,$infos=NULL,$assoc=true) {
		$result = $this->multi_query($query);
		if (!$result) return;
		$return["data"] = $this->store_result();
		$return["data"] = $this->res2array($return["data"],$id,$infos,$assoc);
		$this->next_result();
		list($return["count"]) = $this->store_result()->fetch_row();
		return $return;
	}

	/**
	* Vide une table de ses enregistrements
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $table
	* @return mixed Résultat de la requête d'effacement
	*/
	public function truncate($table) {
		return $this->query("DELETE FROM `".$table."`");
	}

	/**
	* Optimize un ou toutes les tables de la base de données.
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $table Si NULL alors toutes les tables seront optimisées
	* @return mixed Résultat de la requête d'optimisation
	*/
	public function optimize($table=NULL) {
		$tables = $this->all_tables();
		if ($table) {
			$tables = array($table);
		}
		$query = "OPTIMIZE  TABLE  `".implode("`,`",$tables)."`";
		return $this->arr($query);
	}

	/**
	* Retourne une correspondance rapide entre les types MySQL et les types de champs HTML, afin de générer un formulaire adapté
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @modified by Nicolas BERTEMONT <nbertemont@absystech.fr> and Jérémy Gwiazdowski <jgw@absystech.fr>
	* @param string $table table courante
	* @param string|array $filtre
	* @return array
	*/
	public function fields($table,$filtre=false) {
		//On récupère dans le singleton la struture de la table (fameux desc)
		if(!ATF::$table()->desc){
			ATF::$table()->desc=$this->desc($table);
		}

		if (ATF::$table()->desc) {
			if (is_string($filtre)) {
				$filtre = explode(",",$filtre);
			}

			foreach(ATF::$table()->desc as $res) {
				if (in_array($res["Field"],$filtre) || strlen($filtre_[0])<5 && strpos($res["Field"],$filtre_[0])!==false) {

				} else {
					$infos[$res["Field"]]=$res["Field"];
				}
			}
		}
		return $infos;
	}

	/**
    * Retourne le type des champs $fields (récursivement) dans la table $table
    * @author Quentin JANON <qjanon@absystech.fr>
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $class Classe de provenance
	* @param array $fields Tableau de la dimension courante traitée dans $this->colonnes
	* @param array $structure Structure complète de la table
    * @return array resultat
    */
	function fieldstructure(&$class,$fields,&$structure) {
		if (is_array($fields)) {
			foreach ($fields as $k=>$field) {
				if (preg_match("/".$class->table."./",(string)$field) && $structure[str_replace($class->table.".","",$field)]) {
					$return[$field] = $this->table2htmltable($class->desc,str_replace($class->table.".","",$field));

				// Si le $k est une string qui contient un '.' (exemple societe.societe) && si le champs appartient a la table
				} elseif (preg_match("/".$class->table."./",(string)$k) && isset($structure[str_replace($class->table.".","",(string)$k)]) && $structure[str_replace($class->table.".","",(string)$k)]) {
					// Il y a des options sur le champ
					if (is_array($field)) {
						$return[$k] = array_merge($this->table2htmltable($class->desc,str_replace($class->table.".","",$k)),(array)$field);
					} else {
						$return[$k] = $this->table2htmltable($class->desc,str_replace($class->table.".","",$k));
					}

				// Si le champ designé par $k est dans la table
				} elseif (isset($structure[$k]) && $structure[$k] && $k!="type") {
					$return[$k] = array_merge($this->table2htmltable($class->desc,$k),(array)$field);

				// Si le champ designé par $field est dans la table
				} elseif (is_string($field) && isset($structure[$field]) && $structure[$field]) {
					$return[$field] = $this->table2htmltable($class->desc,$field);

				// S'il s'agit de la définition des colonnes filtrées (insert/update/recherche...)
				/*} elseif ($k=="bloquees") {
					foreach($field as $key=>$item){
						$return[$key]=$structure;
						foreach(explode(",",$item[0]) as $key_explode=>$item_explode){
							if(array_key_exists($item_explode,$structure)) {
								unset($return[$key][$item_explode]);
							}
						}
					}*/
				} else {
					$return[$k] = $this->fieldstructure($class,$field,$structure);
				}
			}
			return $return;
		} else {
			return $fields;
		}
	}

	/**
	* Retourne une correspondance rapide entre les types MySQL et les types de champs HTML, afin de générer un formulaire adapté
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $type de la colonne à convertir en format "HTML", et le "xtype" utile pour extJS
	* @return array
	*/
	public function field2htmltable(&$type) {
		if (strpos($type,"enum")!==false) {
			return array("type"=>"enum","xtype"=>"combo","data"=>explode("','",substr($type,6,strlen($type)-6-2)));
		} elseif (strpos($type,"set")!==false) {
			return array("type"=>"set","xtype"=>"multiselect","data"=>explode("','",substr($type,5,strlen($type)-5-2)));
		} elseif (strpos($type,"varchar")!==false) {
			return array("type"=>"text","xtype"=>"textfield","maxlength"=>substr($type,8,strlen($type)-8-1));
		} elseif (strpos($type,"char")!==false) {
			return array("type"=>"text","xtype"=>"textfield","maxlength"=>substr($type,5,strlen($type)-5-1));
		} elseif (strpos($type,"text")!==false) {
			return array("type"=>"textarea","xtype"=>"htmleditor");
		} elseif (strpos($type,"bigint")!==false) {
			return array("type"=>"int","xtype"=>"numberfield","maxlength"=>substr($type,strlen("bigint("),strpos($type,')')-strlen("bigint(")));
		} elseif (strpos($type,"mediumint")!==false) {
			return array("type"=>"int","xtype"=>"numberfield","maxlength"=>substr($type,strlen("mediumint("),strpos($type,')')-strlen("mediumint(")));
		} elseif (strpos($type,"smallint")!==false) {
			return array("type"=>"int","xtype"=>"numberfield","maxlength"=>substr($type,strlen("smallint("),strpos($type,')')-strlen("smallint(")));
		} elseif (strpos($type,"tinyint")!==false) {
			return array("type"=>"int","xtype"=>"numberfield","maxlength"=>substr($type,strlen("tinyint("),strpos($type,')')-strlen("tinyint(")));
		} elseif (strpos($type,"int")!==false) {
			return array("type"=>"int","xtype"=>"numberfield","maxlength"=>substr($type,strlen("int("),strpos($type,')')-strlen("int(")));
		} elseif (strpos($type,"decimal")!==false) {
			return array("type"=>"decimal","xtype"=>"numberfield");
		} elseif (strpos($type,"float")!==false) {
			return array("type"=>"decimal","xtype"=>"numberfield");
//					} elseif (strpos($type,"blob")!==false) {
//						return array("type"=>"blob","maxlength"=>65536);
		} elseif (strpos($type,"datetime")!==false || strpos($type,"timestamp")!==false) {
			return array("type"=>"datetime","xtype"=>"datefield");
		} elseif (strpos($type,"date")!==false) {
			return array("type"=>"date","xtype"=>"datefield");
		} elseif (strpos($type,"time")!==false) {
			return array("type"=>"time","xtype"=>"datefield");
		}
	}

	/**
	* Retourne une correspondance rapide entre les types MySQL et les types de champs HTML, afin de générer un formulaire adapté
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @author Jérémie <jgw@absystech.fr>
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array $colonnes Liste des colonnes à convertir en format "HTML"
	* @param string $filtre
	* @param string $filtre_
	* @modif de nico le 19/01 en ajoutant condition du current_timestamp && null==no
	* @return array
	*/
	public function table2htmltable(&$colonnes,$filtre=false,$filtre_=NULL) {
		foreach ($colonnes as $res) {
			if (is_array($filtre) && in_array($res["Field"],$filtre) || strlen($filtre_[0])<5 && strpos($res["Field"],$filtre_[0])!==false) {

			} else {
				$infos[$res["Field"]] = $this->field2htmltable($res["Type"]);
				if (!$infos[$res["Field"]]) { // Au cas où aucun type trouvé lors de la conversion
					$infos[$res["Field"]] = $res;
				}
			}

			if (isset($infos[$res["Field"]]) && $infos[$res["Field"]]) {
				//dans le cas d'un timestamp dont le défaut est current_timestamp et non null, cela signifie que l'on est pas obligé de renseigné
				//ce champs nous-même puisque la base le fera à notre place
				if ($res["Default"]=='CURRENT_TIMESTAMP' && $res["Null"]=="NO") {
					//on précise null pour passer outre le check_field (ex:congé), car le champs est en colonne bloquée, et on ne précise rien à son sujet
					$infos[$res["Field"]]["null"] = true;
					//contain_date pour préremplir le champs si non bloqué (ex:hotline_interaction)
					$infos[$res["Field"]]["contain_date"] = true;
				}
				if($res["Default"]!='CURRENT_TIMESTAMP'){
					$infos[$res["Field"]]["default"] = $res["Default"];
				}
				if ($res["Null"]=="YES") {
					$infos[$res["Field"]]["null"] = true;
				}
			}
			if (!is_array($filtre) && $filtre==$res["Field"]) {
				return $infos[$res["Field"]];
			}
		}
		return $infos;
	}

	/**
	* Retourne VRAI si une table ou une vue de ce nom existe dans la base de données
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $table
	* @return boolean
	*/
	public function table_or_view_exists($table) {
		//Construction du show tables pour éviter les multiples appels SQL
		if(!is_array($this->all_tables)){
			$this->all_tables();
		}
		return in_array($table,$this->all_tables);
	}

	/**
	* Retourne le nombre d'enregistrements dans une table
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $table
	* @return int
	*/
	public function count($table) {
		$query = "SELECT COUNT(*) FROM `".$table."`";
		return (int)($this->ffc($query));
	}

	/**
	* Vérification des jointures éventuellement absentes alors qu'on a demandé la table dans les fields, uniquement si le mode strict est désactivé
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param classes $class Singleton utilisé
	* @param classes $jointure Jointures déjà existentes
	*/
	private function autoJoins(&$class,&$jointure) {
		if ($class->q->strict!==true) {
			if (is_array($class->q->field)) {
				foreach ($class->q->field as $k_fields => $f_fields) {
					if ($result = $class->foreign_key_translator($f_fields["alias"],$jointure)){
						if ($result["field"] != $k_fields) {
							$replacement = array($result["field"]=>$f_fields["alias"]);
							util::array_insert($class->q->field,util::getoffset($class->q->field,$k_fields),$replacement);
							unset($class->q->field[$k_fields]);
						}
						if (strpos($k_fields,"*")===false && !$class->q->strict && $result["pk_field"] && !$class->q->field[$result["pk_field"]]) { // N'ajoute le champ que si le mode strict est totalement désactivé
							$class->q->field[$result["pk_field"]] = $k_fields."_fk";
						}
//						if (isset($result["jointure"])) {
//							$a = $result["jointure"]["alias"];
//							$jointure[$a] = $result["jointure"];
//						}
					}
				}
				// Ajout de la clé primaire de la table
				if($class->q->dimension!=="cell"){
					$f_temp=array_flip($class->q->field); // N'existe pas non plus en tant qu'alias
					if (!$f_temp[$class->q->getAlias().'.id_'.$class->q->table] && !$class->q->field[$class->q->getAlias().'.id_'.$class->q->table]) {
						if (!$class->q->strict) { // N'ajoute le champ que si le mode strict est totalement désactivé
							$class->q->field[$class->q->getAlias().'.id_'.$class->q->table] = $class->q->getAlias().'.id_'.$class->q->table;
						}
					}
				}
			}
		}
	}

	/**
	* Ordonner les jointures pour être sûr qu'elles s'essaient pas de se joindre sur une table pas encore jointe
	* @todo Ne fonctionne que sur les jointrues simple pour l'instant, et pas encore sur les jointure sur conditons du querier !
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param classes $class Singleton utilisé
	* @param array $jointure Jointures prévues
	* @return void
	*/
	private function orderJoin(&$class,&$jointure) {
//log::logger($jointure,"qjanon");
		$alias = array_keys($jointure);
//log::logger($alias,"qjanon");
		$orderJoin = array($class->q->getAlias());
		$compteur=0;
		while (count($jointure)>$compteur) {
			foreach ($jointure as $k => $i) {
				// Pour chaque jointure on mémorise les tables
				if ($i["cle_condition"]) {
					array_push($orderJoin,$k);
					//continue; // @todo On laisse tomber les jointures complexes pour l'instant !
				} elseif(in_array($k,$orderJoin)) {
					continue; // Déjà dedans
				} elseif(in_array($i["table_left"],$orderJoin) && !in_array($i["table_right"],$alias) || $k==$i["table_right"]) { // Besoin que du left
					array_push($orderJoin,$k);
				} elseif(in_array($i["table_left"],$orderJoin) && in_array($i["table_right"],$orderJoin)) { // Besoin des deux côté
					//Afin de détecter s'il est possible de passer dans cette partie du code
					array_push($orderJoin,$k);
				} else {
//log::logger("else : ".$k,"ygautheron");
				}
			}
			$compteur++;
		}
//log::logger($orderJoin,"ygautheron");

		// $orderJoin contient les clés ordonnées
		unset($orderJoin[0]); // Table de la classes de référence, n'est pas une jointure
		foreach ($orderJoin as $i) {
			$jointuresOrdonnees[$i] = $jointure[$i];

			// @todo Peut être qu'il faudrait vérifier aussi si la jointure ne tente pas de créer un alias d'alias (car impossible en SQL, exemple: 1. JOIN user AS userConcernes ON [...] et puis JOIN userConcernes AS userConcernes2 ON )

		}
		$jointure=$jointuresOrdonnees;
	}

	/**
	* Génération automatique du FROM, et des fields à passer en SELECT
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param classes $class Singleton utilisé
	* @param array $jointure Jointures prévues
	* @param array $fields Variable des fields à éventuellement modifier
	* @return string clauses de jointures en MySQL
	*/
	private function autoFrom(&$class,&$jointure,&$fields) {
		$this->orderJoin($class,$jointure); // Vérification que l'ordre des jointures est correct (utilisation des tables précédemment jointes uniquement)

		foreach ($jointure as $k => $i) {
			$join = " ";
			if ($i["externe"]==="left") {
				$join .= "LEFT";
			} elseif ($i["externe"]==="right") {
				$join .= "RIGHT";
			} else {
				$join .= "INNER";
			}
			$join .= " JOIN ";
			if ($i["change_base"]) {
				$join .= "`".$i["change_base"]."`.";
			}
			if($i["subquery"]){
				$join .= "(".$i["subquery"].")";
			}else{
				$join .= $i["table_right"];
			}
			if ($i["alias"]) {
				$join .= " AS `".$i["alias"]."`";
			}

			$join .= " ON (";
			if ($i["field_left"] && $i["field_right"]) {
				$join .= "`".$i["table_left"]."`.`".$i["field_left"]."`=`";
				if ($i["alias"]) {
					$join .= $i["alias"];
				} else {
					$join .= $i["table_right"];
				}
				$join .= "`.`".$i["field_right"]."`";
			}
			$recup_where=$class->q->getWhere();
			if ($recup_where[$i["cle_condition"]]) {
				if ($i["field_left"] && $i["field_right"]) {
					$join .= " AND ";
				}
				$join .= " (".$recup_where[$i["cle_condition"]].")";
			}
			$join .= ") ";
			if (!$joints[$join]) {	 // Anti-doublons
				$from .= $joints[$join] = $join;

				 // Champ particulier, ou bien tous les champs de cette table
				 if (!is_array($class->q->field)) {
					 if ($i["fields_only"]) {
						$i["fields_only"] = explode(",",$i["fields_only"]);
						$i["fields_alias"] = explode(",",$i["fields_alias"]);
						foreach ($i["fields_only"] as $key_field_only => $field_only) {
							$fields[] = "`".$i["alias"]."`.`".$field_only."`".($i["fields_alias"][$key_field_only]?" AS `".$i["fields_alias"][$key_field_only]."`":NULL);
						}
					 } else {
						 $fields[] = "`".$i["table_right"]."`.*";
					 }
				 }
			}
		}
		if (!is_array($class->q->field)) {
			$fields[] = "`".$class->q->getAlias()."`.*"; // Table en dernier pour être sûr de ne pas voir ses valeurs écrasées par des homonymes de tables jointes
		}

		return $from;
	}

	/**
	* Génération automatique des filtres de recherches en conditions WHERE MySQL
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param classes $class Singleton utilisé
	* @param array $fields Variable des fields à parcourir
	* @return string clauses de jointures en MySQL
	*/
	private function autoSearch(&$class,$fields) {
		/* Mode SEARCH : la recherche ajoute un filtrage des mots clés demandés sur les champs de fields qui ne contiennent pas d'étoile*/
		if ($class->q->getSearch()) {
			if (is_array($fields)) {

				if (is_array($class->q->getSearch())) {
					$keywords = $class->q->getSearch();
					$sup_having=true;
				}else{
					// Si ce n'est pas encore un array, on converti en tableau de mots clés à rechercher
					$class->q->search = $keywords = util::searchEngineParser($class->q->getSearch());
				}

				foreach ($keywords as $offset => $keyword) {
					if(is_numeric($offset)){
						$cle=$offset;
					}else{
						$cle++;
					}
//log::logger($fields,'ygautheron');
					foreach ($fields as $k_fields => $f_fields) {
						if ($f_fields["custom"]===true || $f_fields["nosearch"]===true || $fields[$f_fields["alias"]]["nosearch"]===true /* Dans le cas où un champ ajouté avec addField est en fait un autre  (exemple: conge.duree) */) {
							// Ne doit pas être présent dans la requête
//log::logger("continue !",'ygautheron');
							continue;
						}

						if (is_array($f_fields) && isset($f_fields["alias"])) {
							$having_field = $f_alias = $f_fields["alias"];
	//						$attributs = $class->field_column($f_alias);
						} else {
							$f_alias = $k_fields;
							$having_field = $f_fields;
	//						$attributs = $class->field_column($f_fields);
						}
//log::logger($search,'ygautheron');
//log::logger($f_fields,ygautheron,true);
//log::logger(preg_match("/\(/",$f_alias)." || ".is_array($f_fields)." || ".$f_fields["type"]." (".$k_fields.") // ".is_array($f_fields["data"]),ygautheron);

						if (preg_match("/\.\*/",$f_alias)>0 /* Si le champ cmoporte un * on ne peut pas rechercher dessus ! */) {
							// Ne doit pas être présent dans la requête
							continue;
						}

						if(is_numeric($offset) || $having_field==$offset){
							if (preg_match("/\(/",$f_alias)>0 || !is_array($f_fields) || !$f_fields["type"]) {
//log::logger("passe !",ygautheron);
								$search["having"][$cle][$f_alias] = "`".$having_field."` LIKE '%".ATF::db($class->db)->escape_string($keyword)."%'";
							}
							$search["where"][$cle][$f_alias] = $k_fields." LIKE '%".ATF::db($class->db)->escape_string($keyword)."%'";
						}
					}

//log::logger($search,ygautheron);
					if (count($search["having"][$cle])>0) {
						$search["having"][$cle] = "(".implode(" OR ",array_merge($search["where"][$cle],$search["having"][$cle])).")";
					}
					if (count($search["where"][$cle])>0) {
						$search["where"][$cle] = "(".implode(" OR ",$search["where"][$cle]).")";
					}
				}
				if($sup_having==true)unset($search["having"]);
			}

			// Fusion finale avec clause AND : lorsqu'il y a plusierus mots demandés
			if (count($search["having"])>0) {
				$search["having"] = "(".implode(" AND ",$search["having"]).")";
			}
			if (count($search["where"])>0) {
				$search["where"] = "(".implode(" AND ",$search["where"]).")";
			}

			return $search;
		}
	}

	/** Applique le système de fourchette de date
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function applyBetweenDate($class){
		if ($bdate=$class->q->getBetweenDate()) {
			$abd["where"] = "(".$bdate["champs_date"]." BETWEEN '".trim($bdate["debut"])." 00:00:00' AND '".trim($bdate["fin"])." 23:59:59'".")";
			return $abd;
		}
	}

	/**
	* Vérification et ajustement des champs demandés selon leurs alias, ou nom particuliers dans la norme MySQL
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param classes $class Singleton utilisé
	* @param array $fields Variable des fields à éventuellement modifier
	*/
	private function autoFields(&$class,&$fields) {
		if (is_array($fields)) {
			foreach ($fields as $k_fields => $f_fields) {
				if ($f_fields["custom"]===true) {
					// Ne doit pas être présent dans la requête
					continue;
				}

				// Placer les ` sur les champs auto-détectés
				//$pattern = '/([_a-zA-Z0-9\-]+)\.([_a-zA-Z0-9\-]+)/';
				$pattern = '/([_a-zA-Z0-9]+)\.([_a-zA-Z0-9]+)/';
				$replacement = '`$1`.`$2`';
				$temp_fields = " ".preg_replace($pattern,$replacement,$k_fields)." ";

				// Retirer les ` des nombres a virgules
				$pattern = '/`([0-9]+)`\.`([0-9]+)`/';
				$replacement = '$1.$2';
				$temp_fields = preg_replace($pattern,$replacement,$temp_fields);

				if (is_array($f_fields)) {
					if ($f_fields['alias'] && strpos($f_fields['alias'],"*")===false) {
						$temp_fields .= ' AS "'.$f_fields['alias'].'"';
					}
				} elseif (strpos($f_fields,"*")!==false || is_numeric($k_fields)) { // Utile dans le cas des jointures sur clé de condition
					$temp_fields = ' '.$f_fields.' ';
				} elseif(strpos($f_fields,"*")===false) {
					$temp_fields .= ' AS "'.$f_fields.'"';
				}
				$fs_fields[] = $temp_fields;
			}
			$fields = implode(",",$fs_fields);
		}
	}

	/**
	* Retourne la syntaxe de ce SGBD pour la traduction de type énuméré
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param array $field Champ à traduire
	* @param array $options Options du champ
	* @return string SQL
	*/
	public function enumTranslation($field,$options) {
		// Traduction automatisée des types énumérés
		if (is_array($options)) {
			$s = " (CASE ".$field;
			foreach ($options as $option) {
				$s .= " WHEN '".$option."' THEN '".$this->real_escape_string(ATF::$usr->trans($option,substr($field,0,strpos($field,"."))))."'";
			}
			$s .= " ELSE ".$field." END) ";
			return $s;
		} else {
			return $field;
		}
	}

	/**
	* modifie les préfixes des conditions dans le cas où le nom du préfixe n'a pas le nom de l'alias
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param string $table : table courante (from du querier)
	* @param array $jointure : contient toutes les jointures du querier
	* @param array $where : contient toutes les conditions du querier
	*/
	public function changePrefixWhere($table,$jointure,&$where){
		// On vérifie qu'il n'y a qu'une seule jointure sur cette table_right, pour cela il faut compter par table_right
		foreach ($jointure as $k => $i) {
			$nb_table_right[$i["table_right"]]++;
		}

		foreach($jointure as $alias=>$infos){
			//si l'alias est différent du nom de la table jointe (ex : alias societe__id_societe, table societe)
			if($infos['alias'] && $infos['table_right']!=$infos['alias'] && $infos['table_right']!=$table && $nb_table_right[$infos["table_right"]]===1){
				foreach($where as $cle=>$cond){
					//séparation des éléments de la condition (ex: societe.id_societe and tache.horaire_debut=> societe/id_societe/tache/horaire_debut)
					preg_match_all('/([_a-zA-Z0-9]+)\.([_a-zA-Z0-9]+)/',$cond,$recup);

					foreach($recup[0] as $cle2=>$champs){
						//nom du champs à gauche du point
						if($recup[1][$cle2]==$infos['table_right']){
							$remplace=preg_replace('/([^a-zA-Z|_]|^)('.$recup[1][$cle2].')\./','$1'.$infos['alias'].'.',$cond);
							$where[$cle]=$remplace;
						}
					}
				}
			}
		}
	}

	/**
	* Listing paginé complet
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param classes $class Singleton utilisé
	* @return array Résultat de la requête
	*/
	public function select_all(classes &$class) {
		/* Champs par défaut : tous ceux de la table courante */
		$fields = " `".$class->q->getAlias()."`.* ";

		// On protège le nom de la table, si ce n'est pas une subQuery "(xxxxx)"
		$table = $class->q->table;
		if (!preg_match("/^\(/",$table)) {
			$table = "`".$table."`";
		}

		/* Gestion de l'alias sur la table principale sollicitée */
		$from = " FROM ".$table." ";
		if ($class->q->alias){
			$from .=  " AS `".$class->q->alias."` ";
		}

		/* Jointures des tables périphériques */
		$jointure = array_merge((array)($class->jointure),(array)($class->q->jointure));

		/* Jointures automatiques */
		$this->autoJoins($class,$jointure);

		/* Clauses from, tables sollicitées */
		if ($jointure) {
			$fields = array();
			$from .= $this->autoFrom($class,$jointure,$fields);
		}

		/* Définitions des FIELDS */
		if (is_array($class->q->field)) {
			$fields = $class->q->field; // Si on a défini des champs on ne retourne QUE ceux ci
		}

		/* Recherche de mots clés */
		$search = $this->autoSearch($class,$fields);

		/* Fourchette de date à appliquer */
		$between_date=$this->applyBetweenDate($class);

		/* Construction complète des FIELDS */
		$fields_saved = $fields;
		$this->autoFields($class,$fields);

		/* Retourner également le nombre d'enregistremesn sollicités */
		if ($class->q->count===true) { // Données ET comptage
			$count = " SQL_CALC_FOUND_ROWS ";
		} elseif ($class->q->count===1) { // Uniquement le comptage
			$fields_saved_before_count = $fields;
			$fields = " COUNT(*) ";
		}

		/* Clause DISTINCT */
		if ($class->q->distinct) {
			$distinct = " DISTINCT "; // Dédoublonnement
		}

		/* Construction de la requête MySQL finale */
		$query = "SELECT ";
		$query .= $distinct;
		$query .= $count;
		$query .= $fields;
		$query .= $from;
		/* Aggrégats GROUP BY */
		$group = array_merge((array)($class->group),(array)($class->q->group));
		if ($group && $class->q->count===1)	{
			$query = "SELECT ".$distinct.$fields_saved_before_count.$from;
		}
		if ($group) {
			$group = " GROUP BY ".implode(", ",$group);
			if ($search["having"]) {
				unset($search["where"]);
			}
		} else {
			unset($search["having"]);
		}

		/* Conditions WHERE */
		$recup_where=$class->q->getWhere();
		$where = array_merge((array)($between_date["where"]),(array)($search["where"]),(array)($class->where),(array)($recup_where));

		foreach ($jointure as $k => $i) {
			/* Suppression des conditions utilisée pour les jointures externes */
			if ($recup_where[$i["cle_condition"]]) {
				unset($where[$i["cle_condition"]]);
			}
		}



		if ($where) {
			$this->changePrefixWhere($class->table,$jointure,$where);
			$query .= " WHERE (".implode(") AND (",$where).")"; //modifie les préfixes des conditions dans le cas où le nom du préfixe n'a pas le nom de l'alias
		}

		if ($group) {
			$query .= $group;
		}

		/* Conditions HAVING */
		if ($having = array_merge((array)($search["having"]),(array)($class->having),(array)($class->q->having))) {
			$this->changePrefixWhere($class->table,$jointure,$having); //modifie les préfixes des conditions dans le cas où le nom du préfixe n'a pas le nom de l'alias
			$query .= " HAVING (".implode(") AND (",$having).")";
		}

		/* Tris ORDER BY */
		if ($order = array_merge((array)($class->order),(array)($class->q->getOrder()))) {
			$fields_saved_reversed = array_flip($fields_saved);
			foreach ($order as $field => $sens) {
				if (is_array($fields_saved[$field]) && $fields_saved[$field]["alias"]) {
					$field = $fields_saved[$field]["alias"];
				} elseif (is_array($fields_saved) && $fields_saved[$field]) {
					$field = $fields_saved[$field];
				} elseif ($fields_saved_reversed[$field]) {
					$field = $fields_saved_reversed[$field];
				} else {
					$field = explode(".",$field);
					$field = str_replace("``","`","`".($class->q->alias?$class->q->alias:$field[0])."`".($field[1]?".`".$field[1]."`":NULL));
					if (strpos($field,"RAND()")>-1 || strpos($field,'-') === 1) { // Patch rapide pour gérer les order by RAND()
						$field = str_replace("`","",$field);
					}
				}
				/*si le order by se fait sur un alias  de cle */
				if(substr($field,strlen($field)-3) == '_fk') $field ='"'. $field.'"';
				$o[] = $field." ".$sens;
			}
			$query .= " ORDER BY ".implode(", ",$o);
		}

		if ($group && $class->q->count===1)	{
			$query = "SELECT COUNT(*) FROM (".$query.") AS counterTable";
		}

		/* Backup du SQL de référence (utile pour les aggregats) */
		$sql = $query;
		if ($count) {
			$sql = str_replace($count," ",$sql);
		}
		$class->q->setLastSQL($sql);

		/* Pagination et limite par page */
		if ($class->q->limit["limit"]!=-1) { // Si pas de requête illimitée
			if (!$class->q->limit) {
				$class->q->limit["offset"] = 0;
				$class->q->limit["limit"] = defined("__RECORD_BY_PAGE__") ? __RECORD_BY_PAGE__ : 500;
			}
			if ($class->q->page!==NULL) {
				$class->q->limit["offset"] = $class->q->page * $class->q->limit["limit"];
				$limit = " LIMIT ".$class->q->limit["offset"].",".$class->q->limit["limit"];
			}
			$query .= $limit;
		}

		/* Gestion du verrou de lecture */
		if ($class->q->forUpdate) {
			$query .= " FOR UPDATE";
		}

//log::logger($query,"ygautheron",true);
		if($class->q->toString){
//if (ATF::getDebug()) {
//log::logger($query." | toString");
//}
			return $query;
		}else{
			/* Exécution de la requête SQL */
			if ($class->q->count===true) {
				// Retourne en second résultat le nombre de lignes sollicitées
				$query.= "; SELECT FOUND_ROWS();";
				$method = "multi2array";
			} elseif($class->q->count===1) {
				// Ne retourne que le résultat du COUNT(*)
				$method = "ffc";
			} elseif($class->q->dimension==="row"){
				// Retourne le listing uniquement sous la forme d'un tableau à 1 dimension associatif
				$method = "fasso";
			} elseif($class->q->dimension==="row_arro"){
				// Retourne le listing uniquement sous la forme d'un tableau à 1 dimension
				$method = "farro";
			}elseif($class->q->dimension==="cell"){
				// Retourne le listing uniquement sous la forme d'un champ
				$method = "ffc";
			}else{
				// Retourne le listing uniquement sous la forme d'un tableau à 2 dimensions
				$method = "sql2array";
			}
//if (ATF::getDebug()) {
//log::logger($query." | ".$method);
//}
			return $this->$method($query,$class->q->getArrayKeyIndex(),NULL,$class->q->dimension!=="row_arro" && $class->q->dimension!=="arro");
		}
	}

	/**
	* Listing paginé complet
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param array $queries Toutes les requêtes
	* @param querier $q Querier à utiliser
	* @param boolean $assoc VRAI pour avoir un tableau associatif
	* @return array Résultat de la requête
	*/
	public function union(array &$queries,querier &$q,$assoc=false) {
		$query = "(".implode(   ") UNION (",$queries).")";
		if ($order =$q->getOrder()) {
			foreach ($order as $field => $sens) {
				$o[] = $field." ".$sens;
			}
			$query .= " ORDER BY ".implode(", ",$o);
		}
		if ($q->page!==NULL) {
			$q->limit["offset"] = $q->page * $q->limit["limit"];
			$query .= " LIMIT ".$q->limit["offset"].",".$q->limit["limit"];
		}
		$query .= "; SELECT FOUND_ROWS();";
		return $this->multi2array($query,NULL,NULL,$assoc);
	}

	/**
	* Executer le select_all et en retirer des résultats d'aggregats
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param classes $class Singleton utilisé
	* @param string $field Calculs basés sur ce champ
	* @param array $aggregats
	* @return array
	*/
	public function select_aggregate(&$class,$field,$aggregats) {
		if($class->q->lastSQLForAggregate){
			$query = "SELECT ";
			foreach ($aggregats as $a) {
				$aggr[] = $a."(aggregate_selection.`".$field."`) AS ".$a;
			}
			if(!$aggr)return false;
			$query .= implode(",",$aggr);
			$query .= " FROM (".$class->q->lastSQLForAggregate.") AS aggregate_selection";

//log::logger("Aggregats > ".$query);

			return $this->fasso($query);
		}
	}

	/**
	* Procédure de traitement des valeurs d'un enregistrement
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param classes $class Singleton utilisé
	* @param array $data Champs et leurs valeurs d'un seul enregistrement à traiter avec syntaxes d'échappement du SGBD et procéssus spéciaux (AES etc...)
	*/
	public function fieldsProcess(&$class,$data) {
		if(!$data) {
			throw new errorATF("Il n'y a pas de valeurs à traiter pour cet enregistrement !");
		}

		foreach($data as $key => $item) {
			if (is_array($item) && !isset($item[0])) {
				// Process spécifique, sans filtrage
				$infos[$key] = $item["value"];
				log::logger($item,__CLASS__."_".__FUNCTION__);

//			} elseif(($item_spe = unserialize(stripslashes($item)))/*Posté par un formulaire*/ || ($item_spe = unserialize($item))/*Transmis autrement, sans échappement*/){
//				// Champs nécessitant un traitement particulier en SQL
//				switch ($key) {
//					// Fonction d'encryptage
//					case "aes_encrypte":
//						foreach ($item_spe as $key => $data_and_password) {
//							$data = key($data_and_password); // $infos_encrypte ne contient qu'une ligne, $key= valeur du champs et $item= le mdp pour crypter
//							$password = array_shift($data_and_password); // $infos_encrypte ne contient qu'une ligne, $key= valeur du champs et $item= le mdp pour crypter
//							if (strlen($password)>0) {
//								$infos[$key] = "AES_ENCRYPT('".$data."','".$password."')"; //AES_ENCRYPT (valeur du champ,mdp)
//							} else {
//								throw new errorATF("Il manque le mot de passe pour le champ crypté : ".$field." (".$class->name().")");
//							}
//						}
//						break;
//
//					default :
//die('hop');
//						$infos[$key] = "'".$item."'";
//					break;
//				}
//
			} else {
				// Traitement des types de champs conventionnels
				if ($item!=="" && $item!==NULL) {
					// Si la valeur n'est pas vide
					if (is_array($item)) {
						// Un tableau on l'implode par virgule
						$item=implode(",",$item);
					} elseif (is_numeric(str_replace(",",".",$item))) {
						// Un nombre on change la virgule en un point
						$item = str_replace(",",".",$item);
					}
					//$item = strip_tags($item,"<script>"); // On vire les tags de script
					$item = stripslashes($item); // On vire les slash habituellement postés
					$item = $this->real_escape_string($item); // On échappe avec la bonne méthode du SGBD MySQL
					$infos[$key] = '"'.$item.'"';
				} else {
					$infos[$key] = 'null';
				}
			}

		}
		return (array)($infos);
	}

	/**
	* Mode étendu ou standard du passage des élements en SQL
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $data Données des champs
	* @param array $etendu VRAI pour le mode étendu du style ('donnee1','donnee2'), FAUX pour le mode `field1`='donnee1', `field2`='donnee2'
	* @return string SQL résultant selon le mode demandé
	*/
	public function fields2sql(array $data,$etendu=false) {
		if ($etendu) {
			ksort($data); // Pour que tous les enregistrements aient le même ordre
			return "(".implode(",",$data).")";
		} else {
			foreach ($data as $key => $item) {
				$infos[] = '`'.$key.'` = '.$item;
			}
			return implode(",",$infos);
		}
	}
//
//	/**
//	* L'autocommit est-il activé ?
//	* @author Yann GAUTHERON <ygautheron@absystech.fr>
//	*/
//	public function isAutocommit() {
//		$query = "SELECT @@autocommit";
//		return $this->ffc($query)=="1";
//	}

	/**
	* Test si la $class est bien classes
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param classes $class Singleton utilisé
	*/
	private function checkClass(&$class) {
		if(!is_a($class,'classes') || !$class->table) {
			throw new errorATF("Le paramètre 'class' n'est pas un objet classes)");
		}
	}

	/**
	* Met le log de chaque query et gestion de la tracabilité
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param classes $class Singleton utilisé
	* @param string $query Requête SQL
	* @param string $event insert|update|delete
	* @param array $nolog True si on ne désire pas voir de logs générés par la méthode
	* @todo comment faire pour les triggers ? doit-on tout gérer les créations des ref en PHP afin de pouvoir maitriser la totalité des déclenchements surtout en trigger UPDATE ?
	*/
	private function execQuery(&$class,&$query,$event,$nolog=false) {
		//si il ne s'agit pas d'un module l'on ne doit pas tracer
		if(ATF::$tracabilite && !ATF::tracabilite()->getNoTrace($class->table)){
			$donnees=ATF::tracabilite()->anciennes_donnees($class,$event);
		}

		// Exécution de la requête
		try{
			$retour=$this->query($query);
		}catch(errorATF $e){
			throw $e;
		}

		// Dans le cas d'un insert on retourne le dernier ID créé en autoincrement
		if ($event==='insert') {
			$retour = $this->insert_id;
		// Dans le cas d'un update on retourne le nombre d'enregistrements affectés
        } elseif ($event==='update') {
            $retour = $this->affected_rows;
        } elseif ($event==="multi_insert") {
            $r = $this->info;

            $tmp = explode(" ",$r);
            $retour = $r?array(
                str_replace(":","",$tmp[0]) => $tmp[1],
                str_replace(":","",$tmp[3]) => $tmp[4],
                str_replace(":","",$tmp[6]) => $tmp[7],
            ):array("Records"=>1);

            if ($retour['Warnings']) {
                $e = $this->get_warnings();
                do {
                    $retour['Warnings_details'][] = $e->errno.": ".$e->message;
                } while ($e->next());
            }

        }

		// Méthode en texte brut
		if(!$nolog){
			log::logger(chr(124).ATF::$usr->getLogin().chr(124).ATF::$codename.chr(124).$event.chr(124).$class->table.chr(124).$retour.chr(124).$query,$event.'.log',false,true);
		}

		//la requête étant bien executée, on stocke sa trace
		if(ATF::$tracabilite && !ATF::tracabilite()->getNoTrace($class->table)){
			//si il y a plus qu'une donnée impactée, on ne prends pas en compte l'id retourné
			ATF::tracabilite()->insertion_trace($class,$event,$donnees,(($this->affected_rows==1 && $event==="insert")?$retour:NULL));
		}

		//Retour de la méthode
		return $retour;
	}

	/**
	* Insertion SQL
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @author Fanny DECLERCK <fdeclerck@absystech.fr>
	* @param classes $class Singleton utilisé
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	* @return int l'identifiant inséré
	*/
	public function insert(&$class,$nolog=false) {
		$this->checkClass($class);

		// Traitement de chaque champs
		$infos = $this->fieldsProcess($class,$class->q->getValues());

		// Assemblage du SQL
		$query = "INSERT INTO `".$class->table."` SET ".$this->fields2sql($infos);

		return $this->execQuery($class,$query,__FUNCTION__,$nolog);
	}

	/**
	* Multi Insertion SQL
	* Permet l'insertion massive en une seule requête
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param classes $class Singleton utilisé
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	* @return int l'identifiant inséré
	*/
	public function multi_insert(&$class,$nolog=false,$options=false) {
		$this->checkClass($class);

		// Valeurs définies ?
		$vals = $class->q->getValues();

		// Ajout des champs et trie du tableau, on tri pour garantir l'odre des champs insérés
		if (is_array($array = current($vals))) {
			ksort($array);
			$fields = "`".implode('`,`',array_keys($array))."`";
		} else {
			// Une seule dimension, alors il n'y a qu'un seul enregisrement à insérer, on appelle la méthode standard
			return $this->insert($class,$nolog);
		}

		foreach($vals as $item){
			// Traitement des champs de chaque enregistrement
			$infos = $this->fieldsProcess($class,$item);
			$values[] = $this->fields2sql($infos,true);
		}

        switch ($options) {
            case "ignore": // Ignore les duplicates entry
                $query = "INSERT IGNORE INTO `".$class->table."` (".$fields.") VALUES ".implode(",",$values);
            break;
            /*case "update": // Ignore les duplicates entry
                $query = "INSERT INTO `".$class->table."` (".$fields.") VALUES ".implode(",",$values);
                $query .= " ON DUPLICATE KEY UPDATE ";
            break;*/
            default:
                $query = "INSERT INTO `".$class->table."` (".$fields.") VALUES ".implode(",",$values);
            break;
        }

		return $this->execQuery($class,$query,"multi_insert",$nolog);
	}

	/**
	* Mise à jour
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @author Fanny DECLERCK <fdeclerck@absystech.fr>
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param classes $class Singleton utilisé
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	* @param boolean $force Force la requête même si aucun where, modifie donc tous les enregistrements de la table (FAUX par défaut)
	* @return boolean vrai si la requête s'est bien effectuée
	*/
	public function update(&$class,$nolog=false,$force=false) {
		$this->checkClass($class);

		// Traitement de chaque champs
		$infos = $this->fieldsProcess($class,$class->q->getValues());

		// Assemblage du SQL
		$query = "UPDATE `".$class->table."` SET ".$this->fields2sql($infos);

		// Ajout des conditions
		$where = array_merge((array)($class->where),(array)($class->q->getWhere()));
		if (count($where)>0) {
			$query .= " WHERE (".implode(") AND (",$where).")";
		} elseif (!$force) {
			throw new errorATF("Aucune condition pour le where !");
		}

		$retour=$this->execQuery($class,$query,__FUNCTION__,$nolog);

		/* MySQL vérifie que les données ont changé avant de faire l'update, donc on ne peut pas considérer ca comme une erreur
		if (!$this->affected_rows) {
			throw new errorATF("Aucun enregistrement affecté",$this->errno);
		}
		*/
		return $retour;
	}

	/**
	* Suppression
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param classes $class Singleton utilisé
	* @param array ou string $ids le ou les identifiants que l'on désire supprimer
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	* @return boolean vrai si la requête s'est bien effectuée
	*/
	public function delete(&$class,$nolog=false) {
		$this->checkClass($class);
		// Assemblage du SQL
		$query = "DELETE FROM `".$class->table."` ";

		//Ajout des conditions
		$where = array_merge((array)($class->where),(array)($class->q->getWhere()));
		if (count($where)>0) {
			$query .= " WHERE (".implode(") AND (",$where).")";
		} else {
			throw new errorATF("Aucune condition pour le where !",550);
		}
		return $this->execQuery($class,$query,__FUNCTION__,$nolog);
	}

	/**
	* Retourne la syntaxe de ce SGBD pour la concaténation de plusieurs chaines ou champs de la base de données
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param array $fields Champs à concaténer en SQL
	* @param array $separateur Séparateur de la commande CONCAT_WS
	* @return string SQL
	*/
	public function concat($fields,$separateur="") {
		array_unshift($fields,"'".$separateur."'"); // Séparateur de la commande CONCAt_WS
		return "CONCAT_WS(".implode(",",$fields).")";
	}

	/**
	* Retourne la syntaxe de ce SGBD pour la concaténation par aggrégat
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param array $field Champ à concaténer en SQL
	* @return string SQL
	*/
	public function group_concat($field) {
		return "GROUP_CONCAT(".$field.")";
	}

	/**
	* Méthode spécifique de l'autocommit de mysql avec la prise en charge du système de lock, de la tracabilité et des test unitaires
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param boolean $valeur true pour le commit, false pour démarrer la transation
	* @param boolean $tu true si on se trouve dans les test unitaires !
	* @return boolean
	*/
	public function autocommit($valeur=false,$tu=false) {
		//Lock de la transaction
//echo "autocommit:".$this->lock_transaction."\n";
		if($this->lock_transaction>0) return false;

//echo "autocommit ok\n";
		if(ATF::isTestUnitaire()){
			//on ne prends en compte l'autocommit que du TU et pas ceux qui sont dans les méthodes testées
			if($tu===true){
				$return = parent::autocommit($valeur);
			} else {
				// Simulation qu'on a pris en compte
				$return = true;
			}
		}else{
			if (ATF::$tracabilite && method_exists(ATF::$tracabilite, "init_trace")) {

				if($valeur===true && ATF::tracabilite()->rollback_trace===false){
					// on réinitialise la valeur de la trace une fois tous les traitements effectués
					ATF::tracabilite()->init_trace(false);
				}elseif($valeur===false){
					// on initialise la valeur de la trace
					ATF::tracabilite()->init_trace(true);
				}
			}
			log::logger("AUTOCOMMIT v=".$valeur." - Thread : ".ATF::$id_thread,"allSql.log");
			$return = parent::autocommit($valeur);
		}

		//Si c'est une fin de transaction je génére les actions de la queue et je supprime l'objet
		if ($valeur) {
//			while ($queue = array_pop(ATF::$queues)) {
				ATF::$queue->generate();
				$this->unsetQueue();
//			}
			// Gestion d'erreur
			$this->errorProcess();
		} else {
			//Si c'est un début de transaction je créé l'objet
			ATF::$queue = new \ATF\queue();
		}

		return $return;
	}

	/**
	* Commit avec gestion d'erreur
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @return boolean
	*/
	public function commit() {
		log::logger("COMMIT TRANSACTION - Thread : ".ATF::$id_thread,"allSql.log");
		$return = parent::commit();

		// Gestion d'erreur
		$this->errorProcess();

		return $return;
	}

	/**
	* Méthode spécifique du rollback de mysql avec la prise en charge du système de lock et de la tracabilité
	* @author mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param boolean $force Force le rollback dans les test unitaires (ATTENTION cela ne passe pas outre le lockage)
	* @return boolean
	*/
	public function rollback($force=false) {
		//Lock de la transaction
		//if($this->lock_transaction>0) return false;
		if(!$force && ATF::isTestUnitaire()){
			return false;
		}else{
			if (ATF::$tracabilite && method_exists(ATF::$tracabilite, "init_trace")) {
				ATF::tracabilite()->init_trace(false);
			}
			log::logger("ROLLBACK TRANSACTION - Thread : ".ATF::$id_thread,"allSql.log");
			$return = parent::rollback();

			//Voir un jour pour supprimer tous les fichiers temp..
			if(ATF::isTestUnitaire() && ATF::$queue){
				ATF::$queue->deGenerate();
			}

			return $return;
		}
	}

	/**
    * Retourne le champ crypté en décrypté
    * @author Fanny DECLERCK <fdeclerck@absystech.fr>
	* @param array infos
	* @param string table
    * @return string value champs
    */
//	function aes_decrypte($infos,$table) {
//		$query="SELECT AES_DECRYPT(`".$infos['champs']."`,'".$infos['mdp']."') as `".$infos['champs']."` FROM `".$table."` WHERE `id_".$table."`=".$infos['id_'.$table];
//		return $this->fetch_first_cell($query);
//	}

	/**
	* On commit lorsque l'objet est détruit
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>

	function __destruct() {
		$this->commit();
	}*/

	/**
	* Permet de récupérer tous les codenames pour lesquelles on peut créer un fichier de traduction, structuré pour être utilisé dans un select
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/
	public function recup_codename(){
		try {
			foreach($this->sql2array("SHOW DATABASES") as $key=>$item){
				if(substr($item['Database'],0,6)=="optima"){
					$nom=explode("optima_",$item['Database']);
					$options[$nom[1]]=$nom[1];
				}
			}
		} catch (errorSQL $e) {};
		$options[ATF::$codename]=ATF::$codename;
        $options['hotline']="hotline";
		return $options;
	}

	/**
	* Retourne le nom de la base de données actuelle
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/
	public function getDatabase(){
		return $this->ffc("SELECT DATABASE();");
	}

	/** Permet de générer la liste des liaisons entre les tables, par leur contrainte d'intégrité
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $codename
	public function recup_ci($codename){
		$fichier = __INCLUDE_PATH__.ATF::$codename."/ci.inc.php";

		if(!file_exists(__INCLUDE_PATH__.ATF::$codename))util::mkdir(__INCLUDE_PATH__.ATF::$codename);

		$query="SELECT k.REFERENCED_TABLE_SCHEMA, k.TABLE_NAME, k.COLUMN_NAME, k.REFERENCED_TABLE_NAME, k.REFERENCED_COLUMN_NAME
				FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE AS k
				INNER JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS AS c ON k.CONSTRAINT_SCHEMA = c.CONSTRAINT_SCHEMA
				AND k.CONSTRAINT_NAME = c.CONSTRAINT_NAME
				WHERE c.CONSTRAINT_TYPE = 'FOREIGN KEY'
				AND k.REFERENCED_TABLE_SCHEMA = ( SELECT DATABASE() )";
		foreach($this->sql2array($query) as $key=>$item){
			$ci[$item['REFERENCED_TABLE_NAME']][$item['TABLE_NAME']]=$item['COLUMN_NAME'];
		}

		$sourceCode = '<?php $GLOBALS["ci"] = array(';
		foreach($ci as $table=>$liaison){
			if($l) {
				$l.=',';
			}
			$l.='"'.$table.'"=>array(';
			$i=0;
			foreach($liaison as $nom=>$valeur){
				if($i!=0)$l.=",";
				$l.='"'.$nom.'"=>"'.$valeur.'"';
				$i++;
			}
			$l.=')';
		}
		$sourceCode .= $l.');?>';
		return file_put_contents($fichier,$sourceCode);
	}
	*/

	/** Permet de générer la liste des liaisons entre les tables, par leur contrainte d'intégrité
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return array $ci Tableau associatif qui répertorie les contraintes d'intégrité tel que :
	* @example
		[user] => Array
			(
				[commande] => id_user
				[devis] => id_user
				[droit] => id_user
				[exporter] => id_user
				[filtre_optima] => id_user
				[gep_equipe] => id_user
				[hotline] => id_user
				[hotline_interaction] => id_user
				[intervention_societe] => id_user
				[opportunite] => id_owner
			)
	*/
	public function fetch_foreign_keys() {
		$cmd = "mysqldump --no-data --lock-tables=0 -u ".$this->login." -p\"".$this->password."\" -h ".$this->host." -P ".$this->port." \"".$this->database."\" 2>&1";
		$result = shell_exec($cmd);
		preg_match_all("/CREATE TABLE `(.[^`]*)`(.[^\;]*)\;/",$result,$matches);
		foreach ($matches[2] as $k => $match) {
			preg_match_all("/CONSTRAINT `(.[^`]*)` FOREIGN KEY \(`(.[^`]*)`\) REFERENCES `(.[^`]*)` \(`(.[^`]*)`\)/",$match,$matchesConstraints);

			// On enlève les cases inutiles
			array_shift($matchesConstraints); // 1ère
			array_shift($matchesConstraints); // 2nde
			array_pop($matchesConstraints); // Dernière
			foreach ($matchesConstraints[1] as $j => $fk) {
//echo $fk." ".$matches[1][$k]." = ".$matchesConstraints[0][$j];		echo"\n";
				$return[$fk][$matches[1][$k]] = $matchesConstraints[0][$j];
			}
		}
		ksort($return);
		return $return;
	}

	/** Retourne les contraintes des tables de la base
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @return array $constraints Tableau de toutes les contraintes par table
	*/
	public function fetch_foreign_keys_constraints() {
		$cmd = "mysqldump --no-data --lock-tables=0 -u ".$this->login." -p\"".$this->password."\" -h ".$this->host." -P ".$this->port." \"".$this->database."\" 2>&1";
		$result = shell_exec($cmd);
		preg_match_all("/CREATE TABLE `(.[^`]*)`/",$result,$table);
		preg_match_all("/CREATE TABLE `(.[^`]*)`(.[^\;]*)\;/",$result,$matches);
		foreach ($matches[2] as $k => $match) {

			preg_match_all("/CONSTRAINT `(.[^`]*)` FOREIGN KEY \(`(.[^`]*)`\) REFERENCES `(.[^`]*)` \(`(.*)`\)( (.[^,)]*)|)/",$match,$matchesConstraints);

			foreach($matchesConstraints[0] as $ke => $it){
				$constraints[$table[1][$k]][$ke]["key"]=$matchesConstraints[2][$ke];
				$constraints[$table[1][$k]][$ke]["foreign_table"]=$matchesConstraints[3][$ke];
				$constraints[$table[1][$k]][$ke]["foreign_key"]=$matchesConstraints[4][$ke];

				preg_match_all("/ON UPDATE CASCADE/",$matchesConstraints[5][$ke],$onUpdateCascade);
				preg_match_all("/ON DELETE CASCADE/",$matchesConstraints[5][$ke],$onDeleteCascade);


				preg_match_all("/ON UPDATE SET NULL/",$matchesConstraints[5][$ke],$onUpdateSetNull);
				preg_match_all("/ON DELETE SET NULL/",$matchesConstraints[5][$ke],$onDeleteSetNull);

				/* mis en commentaire car non utilisé (en accord avec mathieu)
				preg_match_all("/ON UPDATE NO ACTION/",$matchesConstraints[5][$ke],$onUpdateNoAction);
				preg_match_all("/ON DELETE NO ACTION/",$matchesConstraints[5][$ke],$onDeleteNoAction);*/

				preg_match_all("/ON UPDATE RESTRICT/",$matchesConstraints[5][$ke],$onUpdateRestrict);
				preg_match_all("/ON DELETE RESTRICT/",$matchesConstraints[5][$ke],$onDeleteRestrict);

				if($onUpdateCascade[0][0]){
					$constraints[$table[1][$k]][$ke]["onUpdateCascade"]=true;
				}elseif($onUpdateSetNull[0][0]){
					$constraints[$table[1][$k]][$ke]["onUpdateSetNull"]=true;
				/*}elseif($onUpdateNoAction[0][0]){
					$constraints[$table[1][$k]][$ke]["onUpdateNoAction"]=true;*/
				}else{
					$constraints[$table[1][$k]][$ke]["onUpdateRestrict"]=true;
				}

				if($onDeleteCascade[0][0]){
					$constraints[$table[1][$k]][$ke]["onDeleteCascade"]=true;
				}elseif($onDeleteSetNull[0][0]){
					$constraints[$table[1][$k]][$ke]["onDeleteSetNull"]=true;
				/*}elseif($onDeleteNoAction[0][0]){
					$constraints[$table[1][$k]][$ke]["onDeleteNoAction"]=true;*/
				}else{
					$constraints[$table[1][$k]][$ke]["onDeleteRestrict"]=true;
				}
			}
		}
		return $constraints;
	}


	/**
	* Méthode DESC d'une table générique
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param string $table Le nom de la table concernée
	* @return array un tableau bizarre !
	*/
	public function desc($table){
		try {
			return $this->sql2array('DESC `'.$this->real_escape_string($table).'`','Field');
		} catch (errorATF $e) {
			// Erreur de desc qui me fait chier en prod car les tables de localisation n'y sont pas !
		}
	}

	/**
	* Permet de bloquer les commits et particulièrement les commit dans les sous-transations
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	*/
	public function lock_transaction(){
		$this->lock_transaction++;
		log::logger("TRANSACTION++ (LOCK) => ".$this->lock_transaction." - Thread : ".ATF::$id_thread,"allSql.log");
		//On ne doit pas pouvoir écraser une transaction de la file d'attente (EX : Trans 1 (Lock=1), Trans 2 (Lock=2), Commit (Lock=1) mais toujours Trans 1 & Trans 2 donc Lock++, Trans 3, Lock--)
//		if(!ATF::$queues[$this->lock_transaction]){
//			$this->addQueue();
//		}else{
//			//Permet de faire le addQueue() sans écraser les dimensions de queues existantes
//			$this->lock_transaction();
//			//Il faut unlocker sinon le commit ne peut se faire
//			$this->unlock_transaction();
//		}
	}

	/**
	* Permet de debloquer les transactions, bien entendu tout les autocommit ou les commits avant ne sont pas pris en compte !
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	*/
	public function unlock_transaction(){
		$this->lock_transaction--;
		log::logger("TRANSACTION-- (UNLOCK) =>".$this->lock_transaction." - Thread : ".ATF::$id_thread,"allSql.log");
	}

	/**
	* Permet de savoir si une transaction est en cours
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	*/
	public function isTransaction(){
		if($this->lock_transaction>0){
			return true;
		}else{
			return false;
		}
	}

	/**
	* Permet de savoir si une transaction est en cours
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/
	public function syncAutocommit(){
		if (!$this->isTransaction()) {
			// On passe pas par query() pour pas écraser le $this->query
			$query = "SELECT @@autocommit;";
			$result = parent::query($query,MYSQLI_STORE_RESULT);
			if (!array_shift($result->fetch_array())) {
				parent::rollback(); // On est jamais trop prudent
				parent::autocommit(true);
			}
		}
	}

	/**
	* Permet de savoir si une transaction est en cours
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	*/
	public function numberTransaction(){
		return $this->lock_transaction;
	}

	/**
	* Retourne la queue de la transaction courante
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/
	public function getQueue(){
		return ATF::$queue;
	}

	/**
	* Supprime la queue de la transaction courante
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/
	public function unsetQueue(){
		ATF::$queue=NULL;
	}

	/**
	* Crée la queue de la transaction courante
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/
//	public function addQueue(){
//		ATF::$queues[$this->lock_transaction] = new queue();
//	}

	/**
	* Débute une transaction, c'est un alias de autocommit(false) qui permet une "meilleure compréhension" dans le code !
	* Ajout du verrouillage automatique
	* @param boolean $tu true si on se trouve dans les test unitaires !
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return boolean FAUX si l'action a été refusée/échappée
	*/
	public function begin_transaction($tu=false){
		$result = $this->autocommit(false,$tu);
		$this->lock_transaction();
		return $result;
	}

	/**
	* Commite une transaction, c'est un alias de autocommit(true) qui permet une "meilleure compréhension" dans le code !
	* Ajout du déverrouillage automatique
	* @param boolean $tu true si on se trouve dans les test unitaires !
	* @return boolean true si le commit a eu lieu
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	*/
	public function end_transaction($tu=false) { return $this->commit_transaction($tu); }
	public function commit_transaction($tu=false) {
		$this->unlock_transaction();
		return $this->autocommit(true,$tu);
	}

	/**
	* Rollback une transaction, c'est un alias de rolback() qui permet une "meilleure compréhension" dans le code !
	* Ajout du déverrouillage automatique
	* @param boolean $tu true si on se trouve dans les test unitaires !
	* @return boolean true si le rollback a eu lieu
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	*/
	public function rollback_transaction($tu=false) {
		$return = $this->rollback($tu);
		$this->unlock_transaction();
		$this->syncAutocommit();
		return $return;
	}
}
?>
