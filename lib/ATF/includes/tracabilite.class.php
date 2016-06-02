<?php
/** Classe privilege : Gestion des droits sur ATF5
* @package ATF
* @todo simplifier la tracabilité
*/
class tracabilite extends classes_optima {
	/** Permet d'ajouter les traces
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @var array $FK L'ensemble des clés étrangères de la base de données courante
	*/
	protected $FK = array();
	
	public function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		$this->table_existe=$this->existe();		
		
		$this->colonnes['fields_column']  = array('tracabilite.date'
													,'tracabilite.id_user'
													,'tracabilite.tracabilite'
													,'tracabilite.nom_element'
													,'tracabilite.id_module'
													,'information'=>array("renderer"=>"traceModif","width"=>80,"custom"=>true));
		//IMPORTANT, complète le tableau de colonnes avec les infos MYSQL des colonnes
		$this->fieldstructure();	
		
		$this->colonnes['bloquees']['select'] =  array('rollback','id_tracabilite_parent');
		$this->colonnes['bloquees']['filtre'] =  array("colonne"=>array('id_tracabilite_parent','avant_modification')
														,"donnee"=>array('id_tracabilite_parent','avant_modification','modification'));
		
		$this->trace=false;	
		$this->rollback_trace=false;
		//liste des modules qui ne doivent pas être concernées par la tracabilite et qui n'ont pas de class créée
		$this->no_trace["tracabilite"]=1;
		$this->no_trace["filtre_defaut"]=1;
		$this->addPrivilege("rollback_trace","update");
		
		// Mise à jour des clé étrangères
		$this->FK = ATF::db($this->db)->fetch_foreign_keys();
		
//		$this->selectAllExtjs=false;
		
		$this->no_update=true;
		$this->no_delete=true;
		$this->no_insert=true;
	}
	
	/** Pour éviter l'envoi multiple de mail si table n'existe pas
	* @author Nicolas BERTEMONT <nbertmemont@absystech.fr>
	*/
	public function existe(){
		if(ATF::db($this->db)->table_or_view_exists($this->table)){
			return true;
		}else{
			$mail = new mail(array( "recipient"=>"nbertemont@absystech.fr", 
									"objet"=>"Pas de table tracabilité dans ".ATF::$codename,
									"from"=>"Optima <no-reply@absystech.fr>"));
			$mail->send();
			return false;
		}
	}

	/** on n'affiche que les traces parentes dans le select_all
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function select_all($order_by=false,$asc='desc'){
		if(!$this->q->getWhere()){
			$this->q->addCondition("id_tracabilite_parent",NULL,NULL,false,"IS NULL");
		}
		//nécessaire pour le renderer
		$this->q->addField("tracabilite.modification")->addField("tracabilite.avant_modification")->addField("tracabilite.id_tracabilite_parent");
		$return = parent::select_all($order_by,$asc);
		foreach ($return['data'] as $k=>$i) {
			if ($i['tracabilite.modification']) {
				$return['data'][$k]['showModif'] = true;
			} else {
				$return['data'][$k]['showModif'] = false;
			}
			if ($i['tracabilite.avant_modification']) {
				$return['data'][$k]['showAvModif'] = true;
			} else {
				$return['data'][$k]['showAvModif'] = false;
			}
		}
		
		return $return;

	}

	/*---------------------------*/
	/*      Méthodes             */
	/*---------------------------*/	
	
	/** Permet d'ajouter les traces
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param string $event : type d'événement (insert/update/delete)
	* @param object $class : objet du module sur lequel on a effectué un changement
	* @param int $id_element : id de l'élément modifié (si table étrangère pas d'id, ex: table de jointure pas de clé primaire)
	* @param array $donnees : stockage des informations avant la modification
	*/
	public function ajout($event,$class,$id_element=NULL,$donnees=false){	
		//initialisation des premières informations qui vont être insérées dans la trace	
		$infos=array('id_user'=>ATF::$usr->getID()
						,'tracabilite'=>$event
						,'id_module'=>ATF::module()->from_nom($class->table)
						,'nom_element'=>$class->table);
		
		//si il s'agit d'une table jointe (donc pas de module) ou de table utilisée dans une transaction
		if(is_numeric($this->trace)){
			//on stocke l'id de la trace principales pour faire un regroupement de trace si on insère plusieurs éléments dans des tables différentes 
			$infos['id_tracabilite_parent']=$this->trace;

			$this->ajoutTraceEnfant($infos,$class,$event,$donnees);

			try{
				if($infos['modification'] || $infos['avant_modification']){
					parent::insert($infos);
				}
			}catch(errorATF $e) {
				$infos['erreur']=log::array2string(ATF::db()->report())."<br />".ATF::db()->getDatabase()."<br />".$e->getTraceAsString();
				$infos['errno']=$e->getErrno();
				$this->envoyer_mail('erreur_tracabilite_tj',$infos);
			}
		}else{	
			//initialisation des différentes informations qui vont être insérées dans la trace
			if(isset($donnees['nom_element'])){
				$infos['nom_element']=strlen($donnees['nom_element'])>250?substr($donnees['nom_element'],0,250)."...":$donnees['nom_element'];
				unset($donnees['nom_element']);
			}

			if(is_numeric($id_element)){
				$infos['id_element']=$id_element;			
			}
			
			$this->ajoutTrace($infos,$class,$event,$id_element,$donnees);
			



			//stockage de la trace
			try{
				if($infos['modification'] || $infos['avant_modification'] || ($infos['modification'] && $infos['avant_modification'])){
					$id_trace=parent::insert($infos);

					// Test UDP
					self::sendUDP($infos);
				}
			}catch(errorATF $e) {
				$infos['erreur']=log::array2string(ATF::db()->report())."<br />".ATF::db()->getDatabase()."<br />".$e->getTraceAsString();
				$infos['errno']=$e->getErrno();
				$this->envoyer_mail('erreur_tracabilite_stock_trace',$infos);
			}
			
			//on stocke l'id de la trace pour déterminer que les requêtes qui se situeront après, seront stockées comme trace enfants jusqu'au autocommit(true)
			if($this->trace===true){
				$this->init_trace($id_trace);			
			}
		}
	}
	
	/**
	* Remonte les informations concernant les modifications appliquées
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array $infos : informations concernant la trace
	* @param object $class : objet du module sur lequel on a effectué un changement
	* @param string $event : type d'événement (insert/delete)
	* @param array $donnees : stockage des informations avant la modification
	*/
	public function ajoutTraceEnfant(&$infos,$class,$event,$donnees){
		if($event==="insert"){
			$infos['modification']=ATF::db($this->db)->real_escape_string(serialize($class->q->values));
		}elseif($event==="delete"){
			$donnees=$this->filtrage_null($donnees);

			//la structure du tableau de données pouvant être différent selon la façon de récupérer les données, on applique aucun traitement pour le moment

			//on regarde si il y a des fichiers attachés aux enfants
			$this->filesToAttach($donnees,$class->table,true);

			//on sauvegarde les informations qu'il y avait concernant les éléments avant l'action
			if(is_array($donnees) && $donnees){
				$infos['avant_modification']=ATF::db($this->db)->real_escape_string(serialize($donnees));
			}
		}
	}
	
	/**
	* Ajout la trace (parente)
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array $infos : informations concernant la trace
	* @param object $class : objet du module sur lequel on a effectué un changement
	* @param string $event : type d'événement (insert/delete)
	* @param int $id_element : id de l'élément modifié
	* @param array $donnees : stockage des informations avant la modification
	*/
	public function ajoutTrace(&$infos,$class,$event,$id_element,$donnees){
		//insertion multiple non géré au niveau parent
		if($event==='insert'){
			$infos['modification']=ATF::db($this->db)->real_escape_string(serialize($class->q->values));

		//modification massive non géré au niveau parent	
		}elseif($event==='update' && is_numeric($id_element)){
			$this->ajoutTraceUpdate($infos,$class,$id_element,$donnees);
		}elseif($event=='delete'){
			$donnees=$this->filtrage_null($donnees);

			foreach($donnees as $key=>$item){
				$champs=$this->recup_champs($key);

				//on ne stocke que les valeurs présentes dans la bdd, plutôt que celles qu'on pourrait ajoutées dans la méthode select_all
				// exemple : horaire_fin de tache de la bdd => datetime (date de fin), du select_all => int (nbre_jour)
				if(!isset($modif[$champs])){
					$modif[$champs]=$item;
				}
			}

			//on regarde si il y a des fichiers attachés aux enfants
			$this->filesToAttach($modif,$class->table,true);
	
			if(is_array($modif)){
				$infos['avant_modification']=ATF::db($this->db)->real_escape_string(serialize($modif));
			}
		}
	}
	
	/**
	* Permet d'ajouter une trace d'event update
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array $infos : informations concernant la trace
	* @param object $class : objet du module sur lequel on a effectué un changement
	* @param int $id_element : id de l'élément modifié (si table étrangère pas d'id, ex: table de jointure pas de clé primaire)
	* @param array $donnees : stockage des informations avant la modification
	*/
	public function ajoutTraceUpdate(&$infos,$class,$id_element,$donnees){
		//on va comparer le tableau des anciennes données avec celui des nouvelles, pour déterminer ce qui a été modifié
		$c=new classes($class->table);
		$c->q->addAllFields($c->table)->setStrict()->addCondition('id_'.$c->table,$id_element);
		$nouvelles_donnees=$c->select_all();

		foreach($nouvelles_donnees[0] as $key=>$item){
			$champs=$this->recup_champs($key);
			
			if($item!=$donnees[$key]){
				//on ne stocke que les valeurs présentes dans la bdd, plutôt que celles qu'on pourrait ajoutées dans la méthode select_all
				// exemple : horaire_fin de tache de la bdd => datetime (date de fin), du select_all => int (nbre_jour)
				if(!isset($modif[$champs])){
					if($serial=unserialize($item)){
						$modif[$champs]=$this->modif_serialize(unserialize($donnees[$key]),$serial);
					}else{
						$modif[$champs]=array('avant'=>$donnees[$key],'apres'=>$item);
					}
				}
			}
			//on remplace les données du tableau, en remplacant les clés par les noms des champs (ex : passé de array(tache.horaire_fin=>??) à array(horaire_fin=>??))
			//pour éviter d'afficher 2 fois la même information, même si structuré différemment
			$donnees[$champs]=$donnees[$key];
			
			//si le nom 'original' (ex : tache.id_user) est différent du nom trouvé par la méthode (ex: id_user), alors on supprime son info
			if($key!=$champs){
				unset($donnees[$key]);
			}
		}

		$donnees=$this->filtrage_null($donnees);
		
		if($donnees){
			$infos['avant_modification']=ATF::db($this->db)->real_escape_string(serialize($donnees));
		}
		
		if($modif){
			$infos['modification']=ATF::db($this->db)->real_escape_string(serialize($modif));
		}
	}
	
	/** Permet de voir si lors de la suppression d'une donnée, un fichier doit être déplacé dans la corbeille (en complément du delete_files de classes, 
	* car il s'agit de donnéee supprimée en bdd que la tracabilité récupère, donc ne passe pas par delete_files)
	* Sert également à restaurer les fichiers dans le cas d'un rollback
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $donnees : toutes les informations sur les éléments (dont l'id servant à la correspondance avec un éventuel fichier)
	* @param string $table : table des éléments
	* @param boolean $corbeille : précise si le fichier doit être mis dans la corbeille ou si il doit en être restaurer
	* @param string $field : nom du fichier particulier à traiter (cas général, et non pas cas de la GED)
	*/
	public function filesToAttach($donnees,$table,$corbeille=false,$field=NULL){
		//si la table n'a pas d'attribut files, il ne sert a rien de continuer
		//si il s'agit de la ged, il y a une structure de fichier particulière
		if($table=="ged" && !ATF::db($this->db)->isTransaction()){
			if($corbeille){				
				$infos=$this->recupIdTrash($donnees,$table);

				//pour chaque id on verifie si il y a un fichier correspondant
				foreach($infos as $num=>$infos){
					//sécurité supplémentaire, car si il y a un problème, et qu'aucun id n'est présent, on déplacera tout le répertoire conteneur
					if($infos['id_'.$table]){
						//sécurité, si il s'agit d'un md5
						$infos['id_'.$table]=ATF::getClass($table)->decryptId($infos['id_'.$table]);

						//si c'est le cas on le rattache à l'enregistrement
						if(!$this->deplacerFichier(__DATA_PATH__.ATF::$codename."/".$table."/".$infos['id_societe']."/".$infos['id_'.$table],__TRASH_PATH__.ATF::$codename."/".$table."/".$infos['id_societe']."/".$infos['id_'.$table])){
							//si il y a un souci dans le transfert des documents on stoppe les transactions
							return false;
						}
					}
				}
			}else{
				//pour chaque élément inséré
				foreach($donnees as $cle_file=>$donnees_file){
					//sécurité supplémentaire, car si il y a un problème, et qu'aucun id n'est présent, on déplacera tout le répertoire conteneur
					if($donnees_file['id_'.$table]){
						//sécurité, si il s'agit d'un md5
						$donnees_file['id_'.$table]=ATF::getClass($table)->decryptId($donnees_file['id_'.$table]);
						
						//si c'est le cas on le rattache à l'enregistrement						
						if(!$this->deplacerFichier(__TRASH_PATH__.ATF::$codename."/".$table."/".$donnees_file['id_societe']."/".$donnees_file['id_'.$table],__DATA_PATH__.ATF::$codename."/".$table."/".$donnees_file['id_societe']."/".$donnees_file['id_'.$table])){
							return false;
						}	
					}
				}
			}
		}elseif(is_array(ATF::getClass($table)->files) && $table!="ged" && !ATF::db($this->db)->isTransaction()){
			//si il y a des fichiers à mettre dans la corbeille
			if($corbeille){
				$infos=$this->recupIdTrash($donnees,$table);

				//pour chaque id on verifie si il y a un fichier correspondant
				foreach($infos as $num=>$id){
					//pour chaque type de fichier
					foreach(ATF::getClass($table)->files as $key_file=>$item_file){
						if(!$field || $field==$key_file){ // Si $field spécifié, traiter seulement celui là, sinon on traite tous les fichiers
							if($id){ // sécurité supplémentaire, car si il y a un problème, et qu'aucun id n'est présent, on déplacera tout le répertoire conteneur
								$id=ATF::getClass($table)->decryptId($id); // sécurité, si il s'agit d'un md5
	
								// si c'est le cas on le rattache à l'enregistrement
								if(!$this->deplacerFichier(ATF::getClass($table)->filepath($id,$key_file),ATF::getClass($table)->filepath($id,$key_file,'trash'))){
									//si il y a un souci dans le transfert des documents on stoppe les transactions
									return false;
								}
							}
						}
					}
				}
			}else{
				//si il faut restaurer certains fichiers de la corbeille
				//pour chaque type de fichier
				foreach(ATF::getClass($table)->files as $key_file=>$item_file){
					//pour chaque élément inséré
					foreach($donnees as $cle_file=>$donnees_file){
						//sécurité supplémentaire, car si il y a un problème, et qu'aucun id n'est présent, on déplacera tout le répertoire conteneur
						if(!$field || $field==$cle_file){ // Si $field spécifié, traiter seulement celui là, sinon on traite tous les fichiers
							if($donnees_file['id_'.$table]){
								//sécurité, si il s'agit d'un md5
								$donnees_file['id_'.$table]=ATF::getClass($table)->decryptId($donnees_file['id_'.$table]);
	
								//si c'est le cas on le rattache à l'enregistrement
								if(!$this->deplacerFichier(ATF::getClass($table)->filepath($donnees_file['id_'.$table],$key_file,'trash'),ATF::getClass($table)->filepath($donnees_file['id_'.$table],$key_file))){
									return false;
								}	
							}
						}
					}		
				}
			}
		}
		return true;
	}
	
	/** Permet de déplacer un fichier d'un point A à un point B, si le répertoire du point B n'est pas créer alors on le fait
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param string origine : chemin du fichier d'origine
	* @param string destination : chemin du fichier de destination
	*/
	public function deplacerFichier($origine,$destination){
		//Création du dossier où sera stocké le fichier si non existant
		if(!file_exists(dirname($destination))){
			if(!util::mkdir(dirname($destination))){
				return false;
			}
		}
		
		if(file_exists($origine)){
			if(!rename($origine,$destination)){
				return false;
			}
		}
		
		return true;
	}
	
	/** Permet de récupérer les informations nécessaire au transfert du fichier attaché à la donnée (si existant)
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array donnees : données conernant l'information supprimée/à rollbacker
	* @param string table : table concerné par la donnée
	*/
	public function recupIdTrash($donnees,$table){
		//selon la structure des données
		if(isset($donnees[0][0])){
			foreach($donnees as $cle=>$elements_joints){
				//pour éviter l'unset de module et id si il y a
				if(is_int($cle)){
					foreach($elements_joints as $num=>$liste){
						if(is_int($num)){
							//on structure le tableau contenant les champs et leurs valeurs respectives
							$don=$this->champs_valeurs($liste);
							if($table=="ged"){
								$infos[]=array('id_'.$table=>$don['id_'.$table],'id_societe'=>$don['id_societe']);
							}else{
								$infos[]=$don['id_'.$table];
							}
						}
					}
				}
			}
		}elseif(isset($donnees[0])){
			//si l'on passe par exemple dans le delete de tache_user qui se situe dans tache
			// dans ce cas le tableau ressemble a tab=array(0=>array(id_user=>23,id_tache=>10),1=>array(id_user=>12,id_tache=>10)
			foreach($donnees as $num=>$liste){
				//on structure le tableau contenant les champs et leurs valeurs respectives
				$don=$this->champs_valeurs($liste);
				if($table=="ged"){
					$infos[]=array('id_'.$table=>$don['id_'.$table],'id_societe'=>$don['id_societe']);
				}else{
					$infos[]=$don['id_'.$table];
				}
			}
		}else{
			if($table=="ged"){
				//si on a pas précisé l'id_societe, alors on va la chercher
				if(!$donnees['id_societe']){
					$donnees['id_societe']=ATF::ged()->select($donnees['id_'.$table],"id_societe");
				}
				$infos[]=array('id_'.$table=>$donnees['id_'.$table],'id_societe'=>$donnees['id_societe']);
			}else{
				$infos[]=$donnees['id_'.$table];
			}
		}
	
		return $infos;
	}
	
	/** Permet de trouver de manière récursive les tables qui ont pu voir leur données modifiées par l'intermédiaire
		d'une modification sur la table courante (Contraintes d'intégrités)
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param object $class : objet correspondants à la table donc on check si sa clé primaire est une clé étrangère d'une autre table
	* @param integer $id : valeur de cette clé primaire
	* @param array $tableau_etranger : tableau qui va contenir, au fur et à mesure, les données nécessaires pour créer la tracabilité correspondante
	* @return array $tableau_etranger : tableau qui va contenir les données nécessaires pour créer la tracabilité correspondante
	*/
	public function recup_table_etrangere($class,$id,$tableau_etranger=NULL){
		try{
			if($id){
				foreach($this->FK[$class->table] as $nom_table=>$nom_champs){
					$anciennes_donnees_etrangeres="";
					//plutot que d'utiliser le ATF::getClass($nom_table) on créé un nouvel objet pour ne pas interférer avec le querier actuel
					$classe_table_etrangere=new classes($nom_table);
					//on créé une trace pour chaque
					//obligé de faire un addjointure sur la table de la clé étrangère sinon l'id utilisé dans le where n'est pas reconnu
					// ex ==>
					// pour societe obligé de mettre dans le where societe.id_societe, car bcp de table jointe, et certaines ont un champs id_societe, donc il serait ambigue
					// tache_user n'a pas de jointure, or vu que le where est tache.id_tache, probleme car tache non joint
					// donc solution temporaire : rajouté un left join de la table
					// mais pour societe, il y a deux left join de cette table dans la requête
					$classe_table_etrangere->q->addAllFields($nom_table)
												->setStrict()
												->addJointure($classe_table_etrangere->table,$nom_champs,$class->table,'id_'.$class->table,str_replace('id_','',$nom_champs))
												->addCondition(str_replace('id_','',$nom_champs).'.id_'.$class->table,$id);

					try{
						$anciennes_donnees_etrangeres=$classe_table_etrangere->select_all($classe_table_etrangere->table.".".$nom_champs,'asc');
					}catch(errorATF $e) {
						$infos['erreur']=log::array2string(ATF::db()->report())."<br />".ATF::db()->getDatabase()."<br />".$e->getTraceAsString();
						$infos['errno']=$e->getErrno();
						$this->envoyer_mail('erreur_recup_table_etrangere',$infos);
						throw $e;
					}
	
					//on stocke dans le tableau de retour uniquement si une donnée peut être modifié
					if($anciennes_donnees_etrangeres){
						//si le tableau contient déjà le nom de la table, on ajoute la modification à celle déjà présente
						if(isset($tableau_etranger[$classe_table_etrangere->table])){
							$tableau_etranger[$classe_table_etrangere->table]["donnees"][]=$anciennes_donnees_etrangeres;
						}else{
							$tableau_etranger[$classe_table_etrangere->table]=array("class"=>$classe_table_etrangere,"donnees"=>array(0=>$anciennes_donnees_etrangeres));
						}
						//$tableau_etranger[$classe_table_etrangere->table]["donnees"][count($tableau_etranger[$classe_table_etrangere->table]["donnees"])-1]['module']=$class->table;
						//$tableau_etranger[$classe_table_etrangere->table]["donnees"][count($tableau_etranger[$classe_table_etrangere->table]["donnees"])-1]["id"]=$id;
					}

					// si la table enfant à elle aussi sa clé primaire mis en tant que clé étrangère dans une autre table, alors on doit l'ajouter
					if(isset($this->FK[$classe_table_etrangere->table]) && !isset($this->histo[$nom_table][str_replace('id_','',$nom_champs).'.id_'.$class->table][$id])){
						$this->histo[$nom_table][str_replace('id_','',$nom_champs).'.id_'.$class->table][$id]=1;
						foreach($anciennes_donnees_etrangeres as $cle=>$item){
							$tableau_etranger=$this->recup_table_etrangere($classe_table_etrangere,$item[$classe_table_etrangere->table.'.id_'.$classe_table_etrangere->table],$tableau_etranger);
						}
					}
				}
				return $tableau_etranger;
			}
		}catch(errorATF $e){
			return false;
		}
	}

	/** Ne renvoi que les données qui n'ont pas leur valeur null
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array $donnees : données à filtrer
	* @return array $donnees : données filtrées
	*/
	public function filtrage_null($donnees){
		foreach($donnees as $champs=>$valeur){
			if(!$valeur){
				unset($donnees[$champs]);
			}
		}
		return $donnees;
	}
	
	/** Permet de remettre les données tel qu'elles étaient avant l'action effectuée
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
/*	en commentaire le temps de l'optimiser et le rendre complétement fonctionnel
	public function rollback_trace($infos,&$s,$files=NULL,&$cadre_refreshed){
		//on récupère les informations concernant cette trace
		$id_trace=$this->decryptId($infos['id_trace']);
		
		$c=new classes($this->table);
		$c->q->reset()->addCondition("id_tracabilite",$id_trace)
						->addCondition("id_tracabilite_parent",$id_trace,"OR","id_tracabilite");
						
		//on regroupe toutes les traces créées par le rollback				
		$this->init_trace(true);
		//on précise qu'il s'agit d'une insertion de trace pour passer outre la suppression de l'id (dans $infos) dans classes et éviter les autocommit
		$this->rollback_trace=true;

		foreach($c->select_all("id_tracabilite","asc") as $key=>$item){	
			//récupération du module sur lequel on a efectué une action
			if($item['id_element'] && $item['id_module']){
				$module=ATF::module()->select($item['id_module'],'module');
			}else{
				$module=$item['nom_element'];
			}

			//dans le cas d'une insertion, si il y a des enfants, ils devraient être automatiquement supprimés avec les CI
			if($item['tracabilite']==="insert" && !isset($item['id_tracabilite_parent'])){
				if(isset($item['id_module'])){
					//pas de gestion d'erreur pour ce delete, car si id_element existe pas, la requête est néamoins bien exécuter, donc renverra vrai
					//si un autre élément est erroné (ex: pas d'id_element, pas d'id_module) une fatal erreur est déclanchée)
					try{
						ATF::getClass($module)->delete($item['id_element']);
					}catch(errorATF $e){
						$infos['erreur']=ATF::db()->error."[Query] : ".ATF::db()->query;                                                                                                               
					}
				}else{
					//cas d'un multi insert (ex: profil_privilege, cocher_tout pour un module)
					$c_element=new classes(ATF::getClass($item['nom_element'])->table);
					$item['modification']=unserialize($item['modification']);
					
					if(isset($item['modification'][0])){				
						foreach($item['modification'] as $num=>$liste){
							foreach($liste as $champs=>$valeur){ 
								$champ=$this->recup_champs($champs);
								$c_element->q->addCondition($champ,$valeur);
								$c_element->q->addSuperCondition($champ.",A","AND","A",false);
							}
							$c_element->q->addSuperCondition("A,fini","OR","fini",false);
						}
					}else{
						//si on fait une insertion d'un élément mais sans avoir l'id_module (ex: profil_privilege)
						$c_element->q->addCondition("id_".$item['nom_element'],$item['id_element']);
					}
	
					try{
						$c_element->delete();
					}catch(errorATF $e){
						$infos['erreur']=ATF::db()->error."[Query] : ".ATF::db()->query;                                                                                                               
					}			
				}
			}elseif($item['tracabilite']==="insert" && isset($item['modification'])){
				//dans le cas d'une mise à jour, un rollback remettra l'ancienne version des informations (ex: remettra les infos de tache)
				//mais les éléments éventuellement ajoutés (ex: des tache_user) ne sont pas influencés par les CI, donc on doit les supprimer nous même
				$c_element=new classes(ATF::getClass($item['nom_element'])->table);

				foreach(unserialize($item['modification']) as $num=>$liste){
					
					foreach($liste as $champs=>$valeur){ 
						$champ=$this->recup_champs($champs);
						$c_element->q->addCondition($champ,$valeur);
						$c_element->q->addSuperCondition($champ.",A","AND","A",false);
					}
					$c_element->q->addSuperCondition("A,fini","OR","fini",false);
				}

				try{
					$c_element->delete();
				}catch(errorATF $e){
					$infos['erreur']=ATF::db()->error."[Query] : ".ATF::db()->query;                                                                                                               
				}
			}elseif($item['tracabilite']==="delete" && isset($item['id_tracabilite_parent'])){
				//réinjection des données
				//réinitialisation des différentes valeurs
				//si il s'agit d'une table jointe (ex: plusieurs personnes selectionnées à réinsérées pour une tâche)
				$insert2=array();
				$item['avant_modification']=unserialize($item['avant_modification']);

				//dans le cas où l'on passe par recup_table_etrangere, il y a une dimension en plus dans le cas de plusieurs appel à la même table
				//ex appel deux fois tache_user du coup on a tab=array(0=>array(0=>array(id_user=>23,id_tache=>10),1=>array(id_user=>12,id_tache=>10))
				//														,1=>array(0=>array(id_user=>15,id_tache=>12),1=>array(id_user=>19,id_tache=>12))
				//si c'est le cas la première dimension est toujours pleine
				if(isset($item['avant_modification'][0][0])){
					if(isset($item['avant_modification'][0]['module']))unset($item['avant_modification'][0]['module'],$item['avant_modification'][0]['id']);
					foreach($item['avant_modification'] as $cle=>$elements_joints){
						if(isset($elements_joints['module']))unset($elements_joints['module'],$elements_joints['id']);
						foreach($elements_joints as $num=>$liste){
							//on structure le tableau contenant les champs et leurs valeurs respectives
							$insert2[]=$this->champs_valeurs($liste);
						}
					}
				}else{
					//si l'on passe par exemple dans le delete de tache_user qui se situe dans tache
					// dans ce cas le tableau ressemble a tab=array(0=>array(id_user=>23,id_tache=>10),1=>array(id_user=>12,id_tache=>10)
					foreach($item['avant_modification'] as $num=>$liste){
						//on structure le tableau contenant les champs et leurs valeurs respectives
						$insert2[]=$this->champs_valeurs($liste);
					}
				}

				try{
					ATF::getClass($item['nom_element'])->multi_insert($insert2);
					
					//on check si il n'y a pas des fichiers à rattacher
					$this->filesToAttach($insert2,$item['nom_element']);
							
				}catch(errorATF $e){
					$infos['erreur']=ATF::db()->error."[Query] : ".ATF::db()->query;                                                                                                               
				}

			}elseif($item['tracabilite']==="delete"){
				//réinjection des données
				//réinitialisation des différentes valeurs
				$insert2=array();
				$item['avant_modification']=unserialize($item['avant_modification']);
			
				//on précise qu'il s'agit d'une insertion de trace pour passer outre la suppression de l'id (dans $infos) dans classes
				$this->rollback_trace=true;
				
				//si il y a plusieurs enregistrements concernés (ex : profil_privilege, cocher_tout sur un module)
				if(isset($item['avant_modification'][0])){
					foreach($item['avant_modification'] as $num=>$liste){
						//on structure le tableau contenant les champs et leurs valeurs respectives
						$insert2[]=$this->champs_valeurs($liste);
					}
					
					try{
						ATF::getClass($module)->multi_insert($insert2,$s);
					}catch(errorATF $e){
						$infos['erreur']=ATF::db()->error."[Query] : ".ATF::db()->query;                                                                                                               
					}
				}else{
					//on structure le tableau contenant les champs et leurs valeurs respectives
					$insert2[$module]=$this->champs_valeurs($item['avant_modification']);

					try{
						$id=ATF::getClass($module)->insert($insert2,$s);
						//on regarde si il y a un fichier attaché à remettre
						$this->filesToAttach($insert2,$module);
					}catch(errorATF $e){
						$infos['erreur']=ATF::db()->error."[Query] : ".ATF::db()->query;                                                                                                               
					}
				}
				
				$this->rollback_trace=false;
			}elseif($item['tracabilite']==="update"){
				//on structure le tableau contenant les champs et leurs valeurs respectives
				$modif=$this->champs_valeurs(unserialize($item['avant_modification']));
				
				//dans le cas de champs qui était à null avant modification, ils ne sont pas enregistré dans 'avant_modification'
				//donc on check si parmi les modif, si un champs n'est pas pris en compte dans le rollback, c'est qu'il était surement à null
				//vu que l'on explode deja tous les champs lors de l'ajout, il n'y a plus interet de le refaire
				foreach(unserialize($item['modification']) as $donnee=>$valeurs){
					$modif[$donnee]=$valeurs['avant'];
				}
				
				$modif2[$module]=$modif;	

				try{
					ATF::getClass($module)->update($modif2);
				}catch(errorATF $e){
					$infos['erreur']=ATF::db()->error."[Query] : ".ATF::db()->query;                                                                                                               
				}
			}
		}
		
		//on réinitialise le rollback une fois tout effectué
		$this->rollback_trace=false;
		//réinitialisation de la trace insérée par le premier insert 
		$this->init_trace(false);

		//mise à jour de la trace pour préciser que l'on a effectué le rollback dessus si toutes les requêtes se sont bien exécutées
		if(isset($infos['erreur'])){
			$this->envoyer_mail('erreur_tracabilite_rollback',$infos);
			throw new errorATF(ATF::$usr->trans("rollback_echoue",'tracabilite'));
		}else{
			$cadre_refreshed["rollback_tracabilite"] =ATF::$usr->trans("rollback_effectue",'tracabilite');
			$this->update(array('id_tracabilite'=>$id_trace,'rollback'=>1));
		}
		
	}
*/	
	/** Formate le retour pour ne pas avoir à visualiser le contenu du champs serialize
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array avant : contenu avant modification
	* @param array apres : contenu après modification
	* @return array tableau formaté
	*/
	public function modif_serialize($avant,$apres){
		foreach($apres as $key=>$item){
			if($avant[$key]){
				if($avant[$key]!=$apres[$key]){
					if($modif){
						$concat=", ";
					}else{
						$concat="";
					}
					$modif.=$concat.ATF::$usr->trans("modification_de",$this->table).$key;
				}
			}else{
				if($modif){
					$concat=", ";
				}else{
					$concat="";
				}
				$modif.=$concat.ATF::$usr->trans("ajout_de",$this->table).$key;
			}
		}
		return array('avant'=>"serialize",'apres'=>$modif);
	}
	
	/** Permet l'initialisation du système de tracabilité ou son arrêt
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param bool $valeur : indique si on initialise ou on arrête le système
	*/
	public function init_trace($valeur){
		$this->trace=$valeur;
	}
	
	/** Permet de récupérer les informations concernant les données avant modif, un identifiant éventuel et la liste des tableaux étrangers
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param classes $class Singleton utilisé
	* @param string $event insert|update|delete
	* @return array
	*/
	public function anciennes_donnees(&$class,$event){
		//seulement si il y a une trace (un id_trace de sauvegarde)
		if(is_numeric($this->trace) && $event==='delete'){
			if($class->q->getWhere()){
				$c=new classes($class->table);
				$c->q->addAllFields($c->table)
						->setStrict()
						->whereMerged($class->q->getWhere());	
						
				try{	
					$anciennes_donnees=$c->select_all();
				}catch(errorATF $e){
					$infos['erreur']=log::array2string(ATF::db()->report())."<br />".ATF::db()->getDatabase()."<br />".$e->getTraceAsString();
					$infos['errno']=$e->getErrno();
					$this->envoyer_mail('erreur_tracabilite_anciennes_donnees_delete_enfant',$infos);
					return false;
				}
			}
		}elseif($event==='update' || $event==='delete'){
			/* on récupère la valeur de l'identifiant de la table modifiée, permettant ainsi de sauvegarder les données avant la modification
				permet de récupérer la valeur entre ' : ex : id_tache='123'
				on récupère le contenu du where (retournera par exemple : array(0=>"id_tache='123'"))
			 	Tous les cas de figure de la structure que peut contenir la dimension du where, gérés :
			$possibilite="id_user = '1' AND  id_tache = '957' AND  id_tache_user = '867'";
			$possibilite2="id_tache = '957'";
			$possibilite3="tache_user.id_tache = '957'";
			$possibilite4="`tache_user`.`id_user` = '1' AND  `tache_user`.`id_tache` = '957' AND  `tache_user`.`id_tache_user` = '867'";
			preg_match retournera dans le 1e cas : Array ( [0] => id_tache = '957' [1] => = [2] => 957 )*/

			$where=$class->q->getWhere();
			$contenu=array_values($where);

			preg_match("`id_".$class->table."(.[^']*)'([0-9]{1,32})'`",$contenu[0],$tableau_avec_id);

			$c=new classes($class->table);
			$c->q->addAllFields($c->table)->setStrict();

			//si il s'agit d'une suppression ou modif dont on précise l'id de l'élément
			if($where['id_'.$class->table] && $tableau_avec_id[2]){
				$c->q->addCondition('id_'.$c->table,$tableau_avec_id[2])->setDimension('row');
				try{
					$anciennes_donnees=$c->select_all();
					//vu qu'on ne gère pas l'update massif, je peux stocker l'identifiant
					if($event=="update"){
						$return['ident']=$tableau_avec_id[2];
					}
				}catch(errorATF $e){
					$infos['erreur']=log::array2string(ATF::db()->report())."<br />".ATF::db()->getDatabase()."<br />".$e->getTraceAsString();
					$infos['errno']=$e->getErrno();
					$this->envoyer_mail('erreur_tracabilite_anciennes_donnees',$infos);
					return false;
				}
				if($anciennes_donnees[$c->table.".".$c->table]){
					$anciennes_donnees['nom_element']=$anciennes_donnees[$c->table.".".$c->table];
				}
			}elseif($event==="delete"){
				//si il s'agit de plusieurs enregistrements, il peut y avoir plusieurs conditions
				$c->q->whereMerged($class->q->getWhere());
				//on réinitialise la valeur à 0 pour ne pas fausser les autres informations
				try{
					$anciennes_donnees=$c->select_all();
				}catch(errorATF $e){
					$infos['erreur']=log::array2string(ATF::db()->report())."<br />".ATF::db()->getDatabase()."<br />".$e->getTraceAsString();
					$infos['errno']=$e->getErrno();
					$this->envoyer_mail('erreur_tracabilite_anciennes_donnees_delete',$infos);
					return false;
				}
			}

			//pas besoin de gérer les tables jointes pour les delete multiple executer sur les select_all car il s'agit de plusieurs delete
			// et pour les multi_delete, il n'y a pas de table jointe
			if($event==="delete"){
				//on récupère également les données des tables étrangères si il y a
				if($tableau_etranger=$this->recup_table_etrangere($c,$tableau_avec_id[2])){
					$return["tab_etranger"]=$tableau_etranger;
					$this->init_trace(true);
				}
				$this->histo=NULL;
			}
		}
		$return["anciennes_donnees"]=$anciennes_donnees;
		return $return;
	}
	
	/** Permet d'ajouter la trace de l'événement et les éventuelles traces des tables jointes
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param classes $class Singleton utilisé
	* @param string $event insert|update|delete
	* @param array $donnees : contient les informations concernant les données avant modif, un identifiant éventuel et la liste des tableaux étrangers
	* @param int $id_element : contient l'id de l'élément inséré
	*/
	public function insertion_trace(&$class,$event,$donnees,$id_element=NULL){
		if(!$id_element && is_numeric($donnees['ident'])){
			$id_element=$donnees['ident'];
		}
	
		$this->ajout($event,$class,$id_element,$donnees['anciennes_donnees']);
		
		//dans le cas d'une suppression on stocke les traces des suppressions éventuelles en cascade dû aux CI
		if($event==="delete" && is_numeric($this->trace) && $donnees['tab_etranger']){
			//on créé une trace pour chaque table dont un élément peut avoir été supprimé
			foreach($donnees['tab_etranger'] as $key=>$infos_etranger){
				$this->ajout($event,$infos_etranger['class'],NULL,$infos_etranger['donnees']);
			}
			$this->init_trace(false);
		}
	}
	
	/** renvoi la liste des champs avec leurs valeurs
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array $donnees : tableau contenant la liste des champs et valeurs
	* @return array
	*/
	public function champs_valeurs($donnees){
		foreach($donnees as $champs=>$valeur){
			//explode pour ex: tache.id_user, ne conserver que les noms de champs
			$champs=$this->recup_champs($champs);
			$tableau[$champs]=$valeur;
		}
		
		return $tableau; 
	}
	
	/** renvoi le nom du champs en fonction de la string envoyée (ex: tache.id_tache ou id_tache directement)
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param string $nom_champs : contient le nom du champs
	* @return string
	*/
	public function recup_champs($nom_champs){
		//explode pour ex: tache.id_user, ne conserver que les noms de champs, mais laisser explode, car il peut y avoir un nom comme id_user sans préfixe
		$division=explode(".",$nom_champs);
		if(isset($division[1])){
			$champs=$division[1];
		}else{
			$champs=$division[0];
		}
		return $champs;
	}

	/** Envoi un mail d'erreur avec le détail de cette dernière
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param string $titre : objet de l'email
	* @param array $infos : liste des infos
	*/
	public function envoyer_mail($titre,$infos){
		//envoi de mail uniquement si TU ou Dév
		if((ATF::isTestUnitaire() || __DEV__===true) && $this->table_existe && $this->histo_erreurs[$infos['errno']]!=date("Y-m-d H")){
			//pour éviter les problèmes d'envoi de mail massif, gestion d'historique généralisé toutes les heures
			$this->histo_erreurs[$infos['errno']]=date("Y-m-d H");
			unset($infos['errno']);
			$mail = new mail(array( "recipient"=>"nbertemont@absystech.fr", 
									"objet"=>$titre,
									"template"=>"tracabilite_erreur",
									"renseignements"=>$infos,
									"from"=>"Optima <no-reply@absystech.fr>"));
			$mail->send();
		}
	}
	
	/** Permet de temporairement éviter les trace d'un segment de code
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $module
	* @return void
	*/
	public function maskTrace($module) {
		$this->save_no_trace[$module] = $this->no_trace[$module];
		$this->no_trace[$module] = 1;
	}
	
	/** Tracer ce module
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $module
	* @return void
	*/
	public function unmaskTrace($module) {
		$this->no_trace[$module] = $this->save_no_trace[$module];
	}
	
	/** Récupère la valeur actuel du no_trace du module
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $module
	* @return int
	*/
	public function getNoTrace($module) {	
		//on ne trace pas les tables qui ne font pas parti de la base actuelle
		if(ATF::getClass($module)->db=='main')return true;	
		return $this->no_trace[$module]; 
	}

	/** supprimer les tracabilités d'une date > 4mois
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function deleteTracePerime(){
		$this->q->reset()->addCondition("ADDDATE(date, INTERVAL 4 MONTH)","NOW()","OR",false,"<",false,false,true);
		$this->delete();		
	}
	
	/** Supprimer les fichiers attachés au traces supérieures à 4 mois
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function viderCorbeille($chemin=NULL){
		if(!$chemin){
			$chemin=__TRASH_PATH__.ATF::$codename;
		}
		$liste_originale=scandir($chemin);

		//on enlève les fichiers sources "." et ".."
		unset($liste_originale[0],$liste_originale[1]);

		foreach($liste_originale as $key=>$item){
			if(is_dir($chemin."/".$item)){
				self::viderCorbeille($chemin."/".$item);
			}elseif(file_exists($chemin."/".$item)){
				//on regarde la date de dernière modification
				$timestamp_derniere_modif=filemtime($chemin."/".$item);
				$date_interval=strtotime("+4 month",$timestamp_derniere_modif);
				//si date_interval est inférieure à la date d'aujourd'hui, c'est que le fichier est obsolète
				if($date_interval < strtotime("now")){
					if(unlink($chemin."/".$item)){
						echo "Suppression du fichier : ".$chemin."/".$item." reussie\n";
					}else{
						echo "Suppression du fichier : ".$chemin."/".$item." echoue\n";
					}
				}
			}
		}
	}
		
	/** Méthode GET REST pour API
	* Retourne les dernières activités
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/
	public static function _GET($get,$post) {
		ATF::tracabilite()->q->reset()
			->select('tracabilite')
			->select('id_user')
			->select('date')
			->select('id_module')
			->select('nom_element')
			->select('id_element')

			->orWhere('nom_element','hotline','module')
			->orWhere('nom_element','hotline_interaction','module')

			->orWhere('nom_element','societe','module')
			->orWhere('nom_element','affaire','module')
			->orWhere('nom_element','suivi','module')
			->orWhere('nom_element','devis','module')
			->orWhere('nom_element','commande','module')
			->orWhere('nom_element','facture','module')

			->whereIsNotNull('id_module')
			->addOrder('date','desc')
			->setLimit(100);
		if ($data = ATF::tracabilite()->sa()) {
			foreach ($data as $trace) {
				if ($trace = self::formatTraceForTelescope($trace)) {
					$return[] = $trace;
				}
			}
		}
		return $return;
	}
		
	/** Formate une trace pour envoyer a telescope
	* Retourne les dernières activités
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/
	public static function formatTraceForTelescope($trace) {
		$module = $trace["nom_element"];
		if ($class = ATF::getClass($module)) {
			if ($module=="hotline_interaction") {
				$id_societe = ATF::hotline()->select($class->select($trace["id_element"],"id_hotline"),"id_societe");
			} else {
				$id_societe = $class->select($trace["id_element"],"id_societe");
			}

			if ($module=="commande" || $module=="devis") {
				$libelle = $class->select($trace["id_element"],'resume');
			}
			if (!$libelle) $libelle = $class->nom($trace["id_element"]);

			return array(
				"date" => $trace["date"],
				"action" => $trace["tracabilite"],
				"user" => ATF::user()->nom($trace["id_user"]),
				"id_user" => $trace["id_user"],
				"societe" => ATF::societe()->nom($id_societe),
				"id_societe" => $id_societe,
				"module" => $module,
				"item" => $libelle,
				"id_item" => $trace["id_element"]
			);
		}
	}
		
	/** Envoie en UDP une tracabilité
	* Retourne les dernières activités
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/
	public static function sendUDP($infos) {
		if ($infos['nom_element'] == "suivi") {
			if ($infos["modification"]) $infos["modification"] = unserialize(stripslashes($infos["modification"]));
			if ($infos["avant_modification"]) $infos["avant_modification"] = unserialize(stripslashes($infos["avant_modification"]));
			api::sendUDP(array(
				'event'=>__CLASS__,
				'data'=>self::formatTraceForTelescope($infos)
			));
		}
	}
}