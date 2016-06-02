<?php
/** 
* Classe vue : permet de gérer les vues de l'application
* @package ATF
*/
class vue extends classes_optima {
	public function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
	}
	
	/** Met à jour la vue du select_all
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function update(&$infos){
		$id_vue=$this->selectId($infos["vue"]);
		$vue=$infos["vue"];
		unset($infos["vue"]);

		//tronque
		if(isset($infos['tronque'])){
			return parent::update(array("id_vue"=>$id_vue,"tronque"=>$infos['tronque']));
		}

		//pour ajouter une colonne
		if($infos['ajout_champs']){
			return $this->ajoutChamps($id_vue,$infos);
		}elseif($infos['sup_champs']){
			//pour en supprimer une
			return $this->supChamps($id_vue,$infos);
		}
		
		//si on modifie l'ordre d'affichage des colonnes/mais aussi la suppression d'une seule colonne		
		if($infos['ordre_colonne']){
			parent::update(array("id_vue"=>$id_vue,"ordre_colonne"=>$infos['ordre_colonne']));
			unset($infos['ordre_colonne']);
		}
		
		//si on modifie le tri d'une colonne
		if($infos['ordre']){
			$this->changerTri($id_vue,$infos);
		}elseif($infos['tailles']){
			$liste=explode(",",$infos['tailles']);
			foreach($liste as $cle=>$dons){
				$valeurs=explode("-",$dons);
				$id_col=$this->selectCol($id_vue,util::extJSUnescapeDot($valeurs[0]));
				ATF::colonne()->update(array("id_colonne"=>$id_col,"taille"=>$valeurs[1]));
			}	
		}elseif($infos['taille']){
			//si on modifie la taille d'une colonne
			$infos['champs']=str_replace("__dot__",".",$infos['champs']);
			$id_col=$this->selectCol($id_vue,$infos['champs']);
			
			unset($infos["vue"],$infos['champs']);
			
			$infos["id_colonne"]=$id_col;
			ATF::colonne()->update($infos);
		}elseif($infos['sup']==true && $id_vue){
			//si on reset la vue
			parent::delete($id_vue);
			return $this->reconfigure($vue,$infos);
		}
		
		ATF::$cr->block("top");
		ATF::$cr->block("generationTime");
	}
		
	/** Sélectionne la vue courante, ou on en créé une si non existente
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param string $vue : nom du div (correspondant au nom de la vue)
	* @param bool insert : si true, on créé la vue si non exitante, false, on ne la créé pas
	* @return int : id_vue
	*/
	public function selectId($vue,$insert=true){
		$this->q->reset()->addField("id_vue")->setStrict()->addCondition("vue",$vue)->addCondition("id_user",ATF::$usr->getId())->setDimension('cell');
		//si la vue existe déjà
		if($id_vue=$this->select_all()){
			return $id_vue;
		}elseif($insert){
			//sinon on la créé
			return parent::insert(array("vue"=>$vue,"id_user"=>ATF::$usr->getId()));
		}
	}
	
	/** Retourne l'id des infos sur les colonnes (taille) ou créé la ligne en question
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @return int : id_colonne
	*/
	public function selectCol($id_vue,$champs,$insert=true){
		ATF::colonne()->q->reset()->addField("id_colonne")->setStrict()->addCondition("id_vue",$id_vue)->addCondition("champs",$champs)->setDimension('cell');
		//si la vue existe déjà
		if($id_colonne=ATF::colonne()->select_all()){
			return $id_colonne;
		}elseif($insert){
			//sinon on la créé
			return ATF::colonne()->insert(array("id_vue"=>$id_vue,"champs"=>$champs));
		}
	}
	
	/** Formate la vue des colonnes (ordre, taille, ...), pour être prise en compte dans les requêtes
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param string $div : nom du pager
	* @return array view 
	*/
	public function recupOrdre($div){
		if($id_vue=$this->selectId($div,false)){
			if($vue=$this->select($id_vue)){
				$colonnes=$vue["ordre_colonne"];
				$view['tronque']=$vue["tronque"];
				foreach(explode(",",$colonnes) as $key=>$champs_dot){
					$champs=str_replace("__dot__",".",$champs_dot);
					if($champs){
						$view["order"][$champs]=$champs;
					}
				}
				ATF::colonne()->q->reset()->addCondition("id_vue",$id_vue);
				foreach(ATF::colonne()->select_all() as $cle=>$donnees){
					if($donnees['taille']){
						$view["width"][$donnees['champs']]=$donnees['taille'];
					}
					if($donnees['tri']){
						$view['tri']=array('champs'=>$donnees['champs'],"ordre"=>$donnees['tri']);
					}
				}
			}			
		}

		return $view;
	}
	
	/** Reset les modifications appliquées aux colonnes
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param string $vue : nom du pager
	* @param array $infos
	*/
	public function reconfigure($vue,&$infos){
		//reset des informations concernant les colonnes dans le pager
		$q=ATF::_s("pager")->create($infos["pager"],NULL,true);
		$q->reset('field,view,order');
		ATF::getClass($infos['table'])->genericSelectAllView($q,true);		
		//reset des colonnes et contenu du tableau select_all
		return $this->refresh($q,$infos,$infos["pager"]);
	}
	
	/** Ajoute une colonne
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param int $id_vue : id de la vue
	* @param array $infos
	*/
	public function ajoutChamps($id_vue,&$infos){
		$vue=$this->select($id_vue);
		//si les colonnes existent déjà, on ajoute le champs
		if($vue["ordre_colonne"]){
			$infos['ordre_colonne']=$vue["ordre_colonne"].",".util::extJSEscapeDot($infos['ajout_champs']);
		}else{
			//si elles ne sont pas précisées, on les mets
			$infos['ordre_colonne'].=",".util::extJSEscapeDot($infos['ajout_champs']);
		}
		parent::update(array("id_vue"=>$id_vue,"ordre_colonne"=>$infos['ordre_colonne']));
		
		//Ajout du champs dans le pager pour les requêtes
		$q=ATF::_s("pager")->create($infos['pager'],NULL,true)->addField($infos['ajout_champs']);
		$view=ATF::vue()->recupOrdre($vue['vue']);
		$q->setView($view,true);
		if($view['tri']){
			$q->addOrder($view['tri']['champs'],$view['tri']['ordre']);
		}
		$q->setLimit($infos['limit']);

		//reset des colonnes et contenu du tableau select_all
		return $this->refresh($q,$infos,$infos['pager']);
	}
	
	/** Supprime une colonne
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param int $id_vue : id de la vue
	* @param array $infos
	*/
	public function supChamps($id_vue,&$infos){
		$vue=$this->select($id_vue);
		
		//sauvegarde du nom du champs pour supprimer de la table colonne si présent
		$champs=util::extJSUnescapeDot($infos['sup_champs']);
		
		//si les colonnes existent déjà, on ajoute le champs
		$infos['ordre_colonne']=($vue["ordre_colonne"]?$vue["ordre_colonne"]:$infos['ordre_colonne']);
		$infos['sup_champs']=str_replace($infos['table'].".","",$infos['sup_champs']);
		
		//on supprime la colonne
		$tab_champs=explode(",",$infos['ordre_colonne']);
		$tab_champs=array_flip($tab_champs);
		//il se peut que sup_champs contiennent juste le nom du champs mais sans le prefixe, alors que dans ordre_colonne, on a le prefixe
		//soit si il y a un prefix
		unset($tab_champs[util::extJSEscapeDot($infos['table'].".".$infos['sup_champs'])]);
		//soit sans prefix
		unset($tab_champs[$infos['sup_champs']]);
		$tab_champs=array_flip($tab_champs);
		$infos['ordre_colonne']=implode(",",$tab_champs);

		if(parent::update(array("id_vue"=>$id_vue,"ordre_colonne"=>$infos['ordre_colonne']))){
			if($id_col=$this->selectCol($id_vue,$champs,false)){
				ATF::colonne()->delete($id_col);
			}
		}
		
		//Ajout du champs dans le pager pour les requêtes
		$q=ATF::_s("pager")->create($infos['pager'],NULL,true);
		$view=$this->recupOrdre($vue['vue']);
		$q->setView($view,true);
		if($view['tri']){
			$q->addOrder($view['tri']['champs'],$view['tri']['ordre']);
		}
		$q->setLimit($infos['limit']);


		//reset des colonnes et contenu du tableau select_all
		return $this->refresh($q,$infos,$infos['pager']);
	}


	/** Génére le menu spécifique à vue
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param string table : nom de la table courante
	*/
	public function genererVue($table){
		$options[$table]=array("alias"=>$table,"panel"=>$this->recupPanel($table,$table,true));
		
		if (ATF::getClass($table)->jointure || ATF::getClass($table)->q->jointure || ATF::_s("pager")->q['gsa_'.$table.'_'.$table]) {
			if (ATF::getClass($table)->jointure) {
				$joint=ATF::getClass($table)->jointure;
			} elseif(ATF::getClass($table)->q->jointure) {
				$joint=ATF::getClass($table)->q->jointure;
			} elseif(ATF::_s("pager")->q['gsa_'.$table.'_'.$table]) {
				//si on sélectionne un filtre, on peut récupérer les jointures qui s'y rapporte
				$joint=ATF::_s("pager")->q['gsa_'.$table.'_'.$table]->jointure;
			}

			foreach($joint as $key=>$item){
				if (ATF::getClass($item['table_right']) && !isset(ATF::getClass($table)->colonnes['bloquees']['filtre']["table"][$item['table_right']])) {
					$options[$item['table_right']]=array("alias"=>$key,"panel"=>$this->recupPanel($item['table_right'],$key));
				}
			}
		}
		
		return $options;
	}

	/** Récupération des panels de la table passée en paramètre
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param string table : nom de la table courante
	* @param string prefix : lorsqu'il s'agit d'une jointure manuel, l'alias peut etre différent du nom de la table
	*/
	public function recupPanel($table,$prefix,$panel_principal=false){
		// /!\ l'ordre est important
		// /!\ je ne gère pas les customs pour éviter tout problème, hormi ceux du module courant
		// /!\ je renomme le nom des champs en les préfixant par le nom du module/alias lorsqu'ils sont dans des panels
		
		//on prends on compte les champs custom et renderer du fields_column, dans le primary
		foreach(ATF::getClass($table)->colonnes['fields_column'] as $nom_champs=>$donnees){
			$nom_champs=str_replace($table.".",$prefix.".",$nom_champs);
			if($panel_principal || !$donnees['custom']){
				$panels['primary'][$nom_champs]=$donnees;
			}
		}

		//colonnes primary
		foreach(ATF::getClass($table)->colonnes("primary","select") as $nom_champs=>$donnees){
			//pour éviter les doublons de champs (à cause du fields_column)
			$nom_champs=str_replace($table.".",$prefix.".",$nom_champs);
			if(!$panels['primary'][$nom_champs] && !$panels['primary'][$prefix.".".$nom_champs]){
				//on renomme le champs en le préfixant par le nom de la table, sauf dans le cas d'un custom
				if(!$donnees['custom']){
					$panels['primary'][$prefix.".".$nom_champs]=$donnees;
				}
			}
		}

		// récupération des panels, en prenant en compte que certains champs sont des composite_field, qui contiennent plusieurs champs
		foreach(ATF::getClass($table)->colonnes['panel'] as $nom_panel=>$champs){
			foreach(ATF::getClass($table)->colonnes($nom_panel,"select",'true') as $nom_champs=>$donnees){
				//pour éviter les doublons de champs (à cause du fields_column)
				//on renomme le champs en le préfixant par le nom de la table, sauf dans le cas d'un custom
				if(!$donnees['custom']){
					//le custom du field column prends le dessus sur le custom d'un panel
					if($panels['primary'][$nom_champs] && !$panels['primary'][$nom_champs]['custom'])unset($panels['primary'][$nom_champs]);
					elseif($panels['primary'][$prefix.".".$nom_champs]) unset($panels['primary'][$prefix.".".$nom_champs]);
					$panels[$nom_panel][$prefix.".".$nom_champs]=$donnees;
				}
			}
		}

		//information supplémentaire
		foreach(ATF::getClass($table)->colonnes("secondary","select") as $nom_champs=>$donnees){
			//pour éviter les doublons de champs (à cause du fields_column)
			//on renomme le champs en le préfixant par le nom de la table, sauf dans le cas d'un custom
			if(!$donnees['custom']){
				//le custom du field column prends le dessus sur le custom d'un panel
				if($panels['primary'][$nom_champs] && !$panels['primary'][$nom_champs]['custom'])unset($panels['primary'][$nom_champs]);
				elseif($panels['primary'][$prefix.".".$nom_champs]) unset($panels['primary'][$prefix.".".$nom_champs]);
				$panels['secondary'][$prefix.".".$nom_champs]=$donnees;
			}
		}
		
		//si il n'y a pas de panel primary, on supprime l'élément, plutot que d'avoir un menu vide
		if(count($panels['primary'])==0)unset($panels['primary']);
		
		return $panels;
	}
	
	/** reset des colonnes et contenu du tableau select_all
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param object q : querier modifié
	* @param array infos : contient les infos nécessaires au template
	* @param string vue : nom du pager
	*/
	public function refresh($q,&$infos,$pager){
		$infos['q']=$q;
		$infos['current_class']=ATF::getClass($infos['table']);
		$infos["pager"]=$pager;

		ATF::$html->array_assign($infos);
		
		$js = ATF::$html->fetch("generic-gridpanel_vars.tpl.js");
		
		$infos["display"]=true;
		return $js;
	}

	/** Pour sauvegarder le tri des colonnes (et supprimer l'ancien si présent)
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param int $id_vue : id de la vue
	* @param array $infos
	*/
	public function changerTri($id_vue,&$infos){
		//si un tri existe déjà on le supprime
		ATF::colonne()->q->reset()->addField("id_colonne")->addField("taille")->setStrict()->addCondition("id_vue",$id_vue)->addConditionNotNull("tri")->setDimension('row_arro');
		$tri_col=ATF::colonne()->select_all();
		//sauf si il possède une taille, on modifie juste le champs tri
		if($tri_col['taille']){		
			ATF::colonne()->update(array("id_colonne"=>$tri_col['id_colonne'],"tri"=>NULL));
		}elseif($tri_col['id_colonne']){
			//sinon on supprime complétement l'information
			ATF::colonne()->delete($tri_col['id_colonne']);
		}
		
		$infos['champs']=str_replace("__dot__",".",$infos['champs']);
		$infos['tri']=strtolower($infos['ordre']);
		$id_col=$this->selectCol($id_vue,$infos['champs']);
		
		unset($infos["vue"],$infos['champs'],$infos['ordre']);
		
		$infos["id_colonne"]=$id_col;
		ATF::colonne()->update($infos);
	}
};

?>