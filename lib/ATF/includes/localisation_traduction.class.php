<?php
/** Classe traduction : gère les diffrentes langues de l'application et notamment le module qui permet de traduire
* "localisation"=>"localisation_traduction"
* @package ATF
*/

class localisation_traduction extends classes_optima {
	/*---------------------------*/
	/*      Constructeurs        */
	/*---------------------------*/
	public function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		$this->colonnes['fields_column'] = array(
			'localisation_traduction.id_localisation'
			,'localisation_traduction.expression_traduite'=>array("rowEditor"=>"setInfos")
			,'localisation_traduction.codename'=>array("rowEditor"=>"setInfos")
		);

		$this->colonnes["bloquees"]["select"]=array("id_localisation_langue");
		$this->genererColonnes($this->colonnes['fields_column']);
		$this->fieldstructure();

		$this->no_update_all = false; // Pouvoir modifier massivement
		$this->no_update = true;
		$this->no_select = true;

		$this->addPrivilege("export_base_vers_fichier","export");
		$this->addPrivilege("selectAllNonTraduit");
		ATF::tracabilite()->no_trace[$this->table]=1;
		$this->formExt=false; //il est en extjs, mais specifique donc blocage du formulaire basique
		$this->addPrivilege("changeLangue");
		$this->addPrivilege("setInfos","update");

		$this->field_nom = "expression_traduite";

	}

	/*---------------------------*/
	/*      Mthodes             */
	/*---------------------------*/
	/** Insérer la traduction d'une expression
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed){
        ATF::db($this->db)->begin_transaction();

        foreach ($infos as $k=>$i) {

            if (!is_numeric($k)) continue;
            if (!$i["expression"]) continue;
            // We check the translation, if it exists, we decrypt the ID, otherwise we create it.
            if($i['localisation']){
                $i['id_localisation']=ATF::localisation()->decryptId($i['localisation']);
            }else{
            	$insert = array('localisation'=>$infos['label_'.$k]['localisation']);
                $i['id_localisation']=ATF::localisation()->insert($insert);
                unset($i['label_'.$k]);
            }

            unset($i['localisation']);

            $i['expression_traduite'] = $i['expression'];
            unset($i['expression']);
            // We check the translate exression out to know if we must create it or just retrieve it for update
            $this->q->reset()->addField('id_localisation_traduction')->where("id_localisation", $i["id_localisation"]);
            // DOn't forget to specify the codename
            if(!$i["codename"]){
                $this->q->whereIsNull("codename");
            }else{
                $this->q->where("codename", $i["codename"]);
            }
            if ($id_existing = $this->select_cell()) {
                // We do an update
                $i['id_localisation_traduction'] = $id_existing;
                parent::update($i);
            } else {
                // We do an insert

                parent::insert($i);
            }

        }
        ATF::db($this->db)->commit_transaction();

        $this->redirection("select_all");
        return;
	}

	/** Supprime une traduction avec une expression si n'est plus rattache à aucune traduction
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @return boolean
	*/
	public function delete($infos,&$s,$files=NULL,&$cadre_refreshed){
		foreach($infos['id'] as $key=>$id){
			$traduc=$this->select($id);

			//si on supprime une traduction, on supprime aussi celle dans une autre langue
			$this->q->reset()->addField("id_localisation_traduction")
							->setStrict()
							->addCondition("id_localisation",$traduc["id_localisation"]);
			if($traduc['codename']){
				$this->q->addCondition("codename",$traduc['codename']);
			}else{
				$this->q->addConditionNull("codename");
			}
			foreach($this->sa() as $cle_lang=>$item_lang){
				if($traduc['id_localisation']!=$item_lang['id_localisation'])$this->d($item_lang["id_localisation_traduction"]);
			}

			//on regarde si la localisation est encore attaché  une traduction
			$this->q->reset()->addCondition('localisation_traduction.id_localisation',$traduc["id_localisation"])->setCount(1);
			//si ce n'est pas le cas on la supprime
			//==1 car toujours rattaché a la traduction que l'on va supprimer
			if($this->sa()==0){
				ATF::localisation()->delete($traduc["id_localisation"]);
			}
		}

		return parent::delete($infos,$s,NULL,$cadre_refreshed);
	}

	/** Creer ou ecrase les fichiers de traduction a partir de la base pour chaque codename et chaque langage
	* @author Maïté Glinkowski
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function export_base_vers_fichier($infos,&$s){
		$path = __INCLUDE_PATH__.ATF::$project."/language/";
		if (!file_exists($path)) {
			$path = __INCLUDE_PATH__."language/";
		}
		$path = str_replace("/core/","/core-i18n/",$path);

		$path_JS = __ABSOLUTE_PATH__."www/js/atf/lang/";
		$path_JS = str_replace("/core/","/core-i18n/",$path_JS);

		$this->q->reset()->addField("localisation_langue.id_localisation_langue","id_langue")
						->addField("localisation_langue.localisation_langue","langue")
						->addField("localisation_traduction.codename","codename")
						->setStrict()
						->addJointure("localisation_traduction","id_localisation_langue","localisation_langue","id_localisation_langue")
						->addGroup("localisation_langue.localisation_langue, localisation_traduction.codename");
		$liste_langue_avec_codename = $this->sa();

		//Pour chaque langage en PHP
		foreach($liste_langue_avec_codename as $key => $item){
			if($item['codename']!=NULL){ //Ce n'est pas une traduction globale
				$fichier = $path.$item['codename'].'/'.$item['langue'].".inc.php";
				$debut = '<?php $GLOBALS["language"]["'.$item['langue'].'"] = array_merge($GLOBALS["language"]["'.$item['langue'].'"],array(';

				$JSfichier = $path_JS.$item['langue'].".".$item['codename'].".js";
			}else{
				$fichier = $path.$item['langue'].".inc.php";
				$debut = '<?php $GLOBALS["language"]["'.$item['langue'].'"] = array(';

				$JSfichier =$path_JS.$item['langue'].".js";
			}

			//si le fichier n'existe pas on le cr
			if(!file_exists($fichier)){
				mkdir(dirname($fichier));
				touch($fichier);
			}

			if($ressource = fopen($fichier,'w+')){

				fwrite($ressource,$debut);

				//On récupère toutes les expressions pour lesquelles il existe une tradution dans la langue courante
				ATF::localisation()->q->reset()
										->addField("localisation.localisation","localisation")
										->addField("localisation_traduction.expression_traduite","expression_traduite")
										->setStrict()
										->addCondition("localisation_traduction.id_localisation_langue",$item["id_langue"],"AND",'cond_join')
										->addJointure("localisation","id_localisation",'localisation_traduction','id_localisation',NULL,NULL,NULL,'cond_join','inner');
				if($item['codename']){
					ATF::localisation()->q->addCondition("localisation_traduction.codename",$item['codename'],"AND",'cond_join');
				}else{
					ATF::localisation()->q->addConditionNull("localisation_traduction.codename",NULL,"AND",'cond_join');
				}
				$localisation = ATF::localisation()->select_all();

				$trans=array();
				$JStrans=array();
				foreach($localisation as $cle => $ligne){
					$trans[]='"'.$ligne['localisation'].'"=>"'.addslashes($ligne["expression_traduite"]).'"';
					$JStrans[]='"'.$ligne['localisation'].'":"'.addslashes($ligne["expression_traduite"]).'"';
				}

				// Ecriture javascript
				util::mkdir(dirname($JSfichier));
				file_put_contents($JSfichier,"jQuery.extend(ATF.loc,{".implode(",",$JStrans)."});");
				if(!fwrite ($ressource,implode(",",$trans))) throw new errorATF("file_write_error__".$fichier);
				if($item['codename']!=NULL) {
					fwrite($ressource,")");
				}

				fwrite($ressource,");?>");

				fclose($ressource);

			}else{
				throw new errorATF("file_open_error__".$fichier);
			}
		}
		ATF::$msg->addNotice(ATF::$usr->trans("generation",$this->table));

		// Pull
		$cmd_git = 'export HOME=/var/www && cd /home/optima/core-i18n && git pull';
		log::logger($cmd_git,"git");
		$retour = `$cmd_git`;
		log::logger($retour,"git");
		ATF::$msg->addNotice($retour,'Commit GIT '.$path,10);

		// Commit PHP
		$cmd_git = 'export HOME=/var/www && cd /home/optima/core-i18n && git add -f '.$path.'* && git commit '.$path.' --message "Commit language"';
		log::logger($cmd_git,"git");
		$retour = `$cmd_git`;
		log::logger($retour,"git");
		ATF::$msg->addNotice($retour,'Commit GIT '.$path,10);

		// Commit JS
		$path_JS_Physical = trim(`cd $path_JS && pwd -P`);
		ATF::$msg->addNotice($path_JS_Physical,'Commit GIT '.$path_JS,10);
		$cmd_git = 'export HOME=/var/www && cd /home/optima/core-i18n && git add -f '.$path_JS_Physical.'* && git commit '.$path_JS_Physical.' --message "Commit language"';
		log::logger($cmd_git,"git");
		$retour = `$cmd_git`;
		log::logger($retour,"git");
		ATF::$msg->addNotice($retour,'Commit GIT '.$path_JS,10);

		// Push
		$cmd_git = 'export HOME=/var/www && cd /home/optima/core-i18n && git push';
		log::logger($cmd_git,"git");
		$retour = `$cmd_git`;
		log::logger($retour,"git");
		ATF::$msg->addNotice($retour,'Commit GIT '.$path,10);
	}

	/* Permet d'ajouter automatiquement les trans dans la base si il n'y a pas de traduction retourne
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/
	public function insertDefaultTrans($mot_traduit=NULL,$word,$prefix=NULL,$suffix=NULL,$strict=false,$id_language="fr",$suffixInPrefix=NULL){
			//si les mots sont identiques, c'est qu'il n'y a pas eu de traductions de faite
			//rajout du is_array($_SESSION) pour ne le faire que si le user est loggué (on ne peut utiliser islogged dans le cas où l'on est sur controlle _optima et que l'on appele logout())
			if($mot_traduit==$word && !is_numeric($word) && ((is_string($word) && !$strict) || ($strict && $suffixInPrefix)) && is_array($_SESSION)){

//log::logger(ATF::$codename." debut ".$mot_traduit,ygautheron);
				//dans le cas d'un strict, si aucune traduction trouve, on ne retourne rien, donc insert d'une trad
				ATF::db($this->db)->begin_transaction();

				//si il n'y a pas de language on met
				if(!$id_language)$id_language="fr";

				if($suffixInPrefix){
					//code repris de loc.class, je suppose donc que si on donne un suffixInPrefix, on a un prefix obligatoirement remplie
					$prefix=$prefix."_".$suffixInPrefix;
				}
				if($prefix){
					$word=$prefix.'_'.$word;
				}
				if($suffix){
					$word=$word.'_'.$suffix;
				}
				$word=ATF::db()->real_escape_string($word);
				ATF::localisation()->q->reset()->addCondition('localisation',$word)->setCount();
				$donnee=ATF::localisation()->select_all();

				//si la localisation n'existe pas on la créé
				if($donnee['count']==0){
					$id_loca=ATF::localisation()->insert(array('localisation'=>$word));
				}else{
					$id_loca=$donnee['data'][0]['id_localisation'];
					//on regarde si une traduction est deja applique dans le cas o la localisation existe deja
					$this->q->reset()->addCondition('localisation_traduction.id_localisation',$id_loca)->setCount(1);
					$donnee['count']=$this->sa();
				}

				if($donnee['count']==0){
					//id_localisation_langue
					ATF::localisation_langue()->q->reset()
												->addField('id_localisation_langue')
												->setStrict()
												->addCondition("localisation_langue",$id_language)
												->setDimension('cell');
					$this->i(array('id_localisation_langue'=>1,'id_localisation'=>$id_loca,'codename'=>(ATF::$project=="optima"?NULL:ATF::$project)));
				}
				ATF::db($this->db)->commit_transaction();
			}
	}

	/** On ajoute une colonne par langue dans la vue, dans fields_column pour conserver les caractéristiques
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @author array $fields_column : le contenu des colonnes du fields_column
	*/
	public function genererColonnes(&$fields_column){
		if(ATF::db($this->db)->table_or_view_exists("localisation_langue")){
			ATF::localisation_langue()->q->reset();
			foreach(ATF::localisation_langue()->select_all() as $key=>$item){
				if($item["id_localisation_langue"]!=1)$fields_column["expression_traduite_".$item["localisation_langue"]]=array("no_list"=>"true","custom"=>true,"rowEditor"=>"setInfos");
			}
		}
	}

	/**
	* Mise à jour des infos du grid
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function setInfos($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL){
		if(!$infos['id_localisation_traduction']) {
			throw new errorATF(ATF::$usr->trans("erreur_identifiant_localisation_traduction"),89);
		}
		$cles=array_keys($infos);
		$traduc=$this->select($infos['id_localisation_traduction']);

		if($langue=ATF::localisation_langue()->select_special("localisation_langue",substr($cles[1],-2))){
			//si l'expression existe déjà, on la modifie
			$this->q->reset()->addField("id_localisation_traduction")
							->setStrict()
							->addCondition("id_localisation_langue",$langue[0]["id_localisation_langue"])
							->addCondition("id_localisation",$traduc["id_localisation"])
							->setDimension("cell");
			if($traduc['codename']){
				$this->q->addCondition("codename",$traduc['codename']);
			}else{
				$this->q->addConditionNull("codename");
			}

			if($id_loca=$this->sa()){
				$tab_update=array("id_localisation_traduction"=>$id_loca,"expression_traduite"=>$infos[$cles[1]]);
				if ($r=$this->u($tab_update)) {
					ATF::$msg->addNotice(loc::mt(ATF::$usr->trans("notice_update_success")));
				}
			}else{
				//sinon on la créé
				$tab_insert=array("id_localisation_langue"=>$langue[0]["id_localisation_langue"],"id_localisation"=>$traduc["id_localisation"],"expression_traduite"=>$infos[$cles[1]],"codename"=>$traduc["codename"]);
				if ($r=$this->i($tab_insert)) {
					ATF::$msg->addNotice(loc::mt(ATF::$usr->trans("notice_insert_success")));
				}
			}
		}else{
			//recup le codename avant modif
			if ($r=$this->u($infos)) {
				//si changement du codename, cela doit impacter toutes les langues
				if(isset($infos['codename'])){
					$this->q->reset()->addField("id_localisation_traduction")
							->setStrict()
							->addCondition("id_localisation",$traduc["id_localisation"]);
					if($traduc['codename']){
						$this->q->addCondition("codename",$traduc['codename']);
					}else{
						$this->q->addConditionNull("codename");
					}
					foreach($this->sa() as $cle_lang=>$item_lang){
						$this->u(array("id_localisation_traduction"=>$item_lang["id_localisation_traduction"],"codename"=>$infos['codename']));
					}
				}
				ATF::$msg->addNotice(loc::mt(ATF::$usr->trans("notice_update_success")));
			}
		}
		return $r;
	}

	/** Surcharge du select_all pour n'afficher qu'une ligne dans le listing pour toutes les langues d'une meme traduction
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function select_all(){
		$this->q->addCondition("localisation_traduction.id_localisation_langue",1)
				->addField("localisation_traduction.codename");
		$listing=parent::select_all();
		foreach($listing["data"] as $key=>$item){
			$this->q->reset()->addField("localisation_langue","langue")
							->addField("expression_traduite")
							->addJointure("localisation_traduction","id_localisation_langue","localisation_langue","id_localisation_langue")
							->addCondition("id_localisation",$item["localisation_traduction.id_localisation_fk"])
							->addCondition("localisation_traduction.id_localisation_langue",1,"OR",false,"!=");
							if($item["localisation_traduction.codename"]){
								$this->q->addCondition("codename",$item["localisation_traduction.codename"]);
							}else{
								$this->q->addConditionNull("codename");
							}
			if($liste_lang=parent::select_all()){
				foreach($liste_lang as $cle=>$donnees){
					$listing["data"][$key]["expression_traduite_".$donnees["langue"]]=$donnees["expression_traduite"];
				}
			}
		}

		return $listing;
	}

};
?>