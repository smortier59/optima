<?php
/** 
* Classe ged : Gestion électronique de documents
* @package ATF
*/
class ged extends classes_optima {
	/**
	* Constructeur
	*/
	public function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		$this->colonnes['fields_column'] = array(
			'ged.ged'
			,'ged.date'
			,'ged.format'
			,'ged.version'
			,'ged.weight'=>array("aggregate"=>array("min","avg","max","sum"),"align"=>"right","suffix"=>"Mo","type"=>"decimal")
			,'fichier'=>array("width"=>50,"custom"=>true,"nosort"=>true,"type"=>"file")
		);
		$this->colonnes['primary'] = array('date','ged','format','version','weight','commentaires','id_societe');
		$this->colonnes['bloquees']['insert'] = array("id_owner","ged","date","format","version","weight","id_parent","dossier");
		$this->quick_action['select'] = array('delete');
		$this->files["fichier"] = array("obligatoire"=>true);
		$this->no_select=true;
		
		$this->fieldstructure();
		
		$this->addPrivilege("branch");
		$this->addPrivilege("download");
		$this->addPrivilege("sup_ged","delete");
		$this->addPrivilege("insert_dir","insert");
		$this->formExt=true;
	}
		
	/**
	* Retourne le chemin du répertoire de GED où est stocké ce fichier
	* @param int $id Clé primaire d'enregistrement associé NE GERE PAS LES ID en MD5
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/
	public function folderpath($id) {
		if ($id_parent = $this->select($id,'id_parent')) {
			return $this->folderpath($id_parent)."/".$id;
		} else {
			return $id;
		}
	}
	
	/** 
	* Branche d'un arbre de GED
	* @author : Yann GAUTHERON <ygautehron@absystech.fr>
	* @author : Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @author : Quentin JANON <qjanon@absystech.fr>
	* @todo A refactorisé, surtout le coup du die a la fin
	*/
	public function branch($infos,&$s,$files=NULL,&$cadre_refreshed){
		$this->q->reset()->addOrder("ged","asc");
		$this->q->addCondition("id_".$infos["element"],ATF::getClass($infos["element"])->decryptId($infos["valeur"]));

		// Commenté le 13 08 pendantl es TU, j'ai recherché sur Optima et GEMP BATIMENT et je ne trouve nul part ou ces attributs sont utilisé.
		if ($infos["node"]=="source") {
			if ($infos['source']) {
				$this->q->addCondition("id_parent",$infos["source"]);
			} else {
				$this->q->addCondition("id_parent",NULL,"OR",false,"IS NULL");
			}
		} elseif ($infos['node']) {
			$this->q->addCondition("id_parent",$infos["node"]);
		}
		
		if ($infos['filters']) {
			switch ($infos['filters']) {
				case 'img':
					$this->q->addCondition("format","jpg")
								->addCondition("format","png")
								->addCondition("format","gif");
				break;
			}
		}
		
		if ($items = parent::select_all()) {
			foreach ($items as $item) {
				$return[]=$this->item($item);
			}
		}
		$infos['display'] = true;
		return json_encode($return);
	}
	
	/** 
	* Format un élément de GED
	* @author : Yann GAUTHERON <ygautehron@absystech.fr>
	*/
	public function item($infos){		
		return array(
			"adapter"=>$infos["id_parent"]
			,"text"=>$infos["ged"]
			,"id"=>$infos["id_ged"]
			,"leaf"=>$infos["dossier"]==0
			,"cls"=>$infos["dossier"]?"folder":"file"
			,"checked"=>false
		);
	}
	
	/** 
	* Permet à partir des infos envoyés, de retourner l'id_société de la ged
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param integer/array $donnees : contient l'id de la ged ou les infos de la ged en question
	*/
	public function recup_soc($donnees){
		if(!is_array($donnees)){
			$donnees=$this->select($donnees);
		}

		if($donnees['id_gep_projet']){
			return ATF::gep_projet()->select($donnees['id_gep_projet'],'id_societe');
		}else{
			return $donnees['id_societe'];
		}
	}
	
	/** 
	* Retourne le chemin vers le fichier binaire à partirde son ID
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @array $id
	*/
	public function getFilepath($id){
		if (strlen($id)===32) $id = $this->decryptId($id);
		return $this->filepath().$this->recup_soc($id)."/".$id;
	}
	
	/** 
	* On récupère les éléments cochés qui seront sous cette forme : Array([node] => [Node 50],[Node 51])
	* si on coche un dossier, on télécharge tous ses enfants (/!\ si un enfant est lui aussi coché, on ne retélécharge pas le contenu)
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @array $infos : contient la liste des éléments cochés
	*/
	public function download($infos){	
	
		//on créé la liste de nodes
		$liste_node=explode(",",$infos['node']);
		//on récupère les id qui nous interesse
		foreach($liste_node as $key=>$item){
			preg_match("/\[Node (.[^']*)\]$/",$item,$node);
			$tab[]=$node[1];
		}
		//on prends le dernier node comme référence pour connaître l'id société des geds
		$id_societe=$this->recup_soc($node[1]);
		// on génére l'arborescence des fichiers (dans temp)
		$data_path_origine=$this->filepath($infos["id_user"],NULL,true);
		if(!file_exists($data_path_origine)){
			util::mkdir($data_path_origine);
		}
		//création de l'archive
		$zipfile = new ZipArchive();
		$fichier=$this->filepath(NULL,NULL,true)."/".$infos["id_user"].".fichier";
		shell_exec("rm -f ".$fichier);
		touch($fichier);


		if ($zipfile->open($fichier) === TRUE) {
			//structure un tableau contenant les données pour créer le zip
			foreach($tab as $id=>$valeur){
				//si l'on a déjà stocké les éléments à mettre dans le zip, on check d'abord que ce qui a été coché n'en fait pas parti
				//si c'est le cas on ne les remet pas dans le zip
				if(!$tab_node_utilise || !isset($tab_node_utilise[$valeur])){
					$donnees_ged=$this->select($valeur);
					$donnees_ged['ged']=iconv('UTF-8', 'IBM850', $donnees_ged['ged']);
					if($donnees_ged['dossier']==0){
						util::copy($this->filepath().$id_societe."/".$valeur,$data_path_origine."/".$donnees_ged['ged']);
						$zipfile->addFile($data_path_origine."/".$donnees_ged['ged'],$donnees_ged['ged']);
					}else{
						$new_chem=$data_path_origine."/".$donnees_ged['ged'];
						mkdir($new_chem);
						$tab_node_utilise=$this->recup_doc_down($zipfile,array('id_ged'=>$valeur,'id_societe'=>$id_societe,'data_path_complete'=>$new_chem),$tab_node_utilise,$donnees_ged['ged']);
					}
					
				}
			}
		}
		
	
		$zipfile->close();
		//suppression des données créées sauf le zip
		shell_exec("rm -rf ".$data_path_origine);
	}
	
	/** 
	* Permet de récupérer tous les ged enfants de celui coché
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param object $zipfile : zip à remplir
	* @param array data => integer id_ged : identifiant du ged courant
	* integer id_societe : identifiant de la societe courante
	* string data_path_complete : chemin que l'on rempli au fur et a mesure, pour copier les fichiers au bon endroit dans le temp (structuré comme dans le zip, pour verif)
	* @param array $tab_node_utilise : contient les nodes déjà présent dans le zip, donc à ne plus prendre en compte si on les a coché sur ext
	* @param string $chemin_zip : chemin que l'on rempli au fur et a mesure, qui sert à structurer le contenu du zip
	* @return array $tab_node_utilise : contient les nodes utilisés
	*/
	public function recup_doc_down(&$zipfile,$data,$tab_node_utilise=NULL,$chemin_zip=NULL){
		//on va chercher les enfants de ce noeud, si il n'y en a pas, il s'agit d'un fichier ou dossier vide
		$this->q->reset()->addCondition("id_parent",$data['id_ged']);
		if($donnees=$this->sa()){
		
			foreach($donnees as $cle=>$infos){
				$chemin="";
				$tab_node_utilise[$infos['id_ged']]=1;
				//si c'est un fichier on le met dans le zip
				$infos['ged']=iconv('UTF-8', 'IBM850', $infos['ged']);
				if($infos['dossier']==0){
					copy($this->filepath().$data['id_societe']."/".$infos['id_ged'],$data['data_path_complete']."/".$infos['ged']);
					$zipfile->addFile($data['data_path_complete']."/".$infos['ged'],$chemin_zip."/".$infos['ged']);
				}else{
					mkdir($data['data_path_complete']."/".$infos['ged']);
					$chemin=$chemin_zip."/".$infos['ged'];
					//sinon on relance la méthode en complétant le chemin avec le nom du dossier
					$data['id_ged']=$infos['id_ged'];
					$data['data_path_complete']=$data['data_path_complete']."/".$infos['ged'];
					$tab_node_utilise=$this->recup_doc_down($zipfile,$data,$tab_node_utilise,$chemin);
				}
			}
		}else{
			$infos=$this->select($data['id_ged']);
			//si c'est un fichier on le met dans le zip
			//sinon on stop, on insère pas de dossier vide ...
			if($infos['dossier']==0){
				$infos['ged']=iconv('UTF-8', 'IBM850', $infos['ged']);
				copy($this->filepath().$data['id_societe']."/".$data['id_ged'],$data['data_path_complete']."/".$infos['ged']);
				$zipfile->addFile($data['data_path_complete']."/".$infos['ged'],$chemin_zip."/".$infos['ged']);
			}
			$tab_node_utilise[$data['id_ged']]=1;
		}
		
		return $tab_node_utilise;
	}
	
	/** 
	* Surcharge de la méthode insert pour ajouter les éléments manquants
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/
	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed){
		$this->infoCollapse($infos);
		//$infos = $infos[$this->table];
		//si on possède déjà toutes les informations (cas d'un rollback de trace)
		if($infos["weight"] && $infos["format"]){
			$infos['id_'.$this->table]=parent::insert($infos,$s);
		}else{
		
			if(!$infos['id_owner'] && !$infos['id_user']){
				$infos['id_owner']=ATF::$usr->getID();
			}
			$infos["date"] = date("Y-m-d H:i:s");
			if($infos['id_societe']){
				$infos['id_societe']=$this->decryptId($infos['id_societe']);
				$id_societe=$infos['id_societe'];
			}
			$temporaryFilepath = $this->filepath(ATF::$usr->getID(),"fichier",true);
// 	YG : J'ai ajouté cette erreur mais ca fou le bordel... donc on verra plus tard mais pour l'instant c'est pas top !			
//			if (!file_exists($temporaryFilepath)) {
//				throw new errorATF(ATF::$usr->trans("fichier_non_trouve"));
//			}

			$pathinfo = pathinfo($infos["filestoattach"]["fichier"]);
			$infos["format"] = $pathinfo['extension'];

			//on unset pour éviter qu'il soit précisé dans l'enregistrement
			if($infos['id_gep_projet']){
				unset($infos['id_societe']);
			}

			//si il s'agit d'un format zip, on dezippe, et on insère tous les éléments qui s'y trouve
			if ($infos["format"]=="zip") {
				$zipfile = new ZipArchive();
				if($zipfile->open($temporaryFilepath)){
					$chemin=$this->filepath(ATF::$usr->getID(),NULL,true);
					if(!is_dir($chemin)){
						util::mkdir($chemin);
					}
					$zipfile->extractTo($chemin);
					$zipfile->close();
					//on fait un unset plutot que de créer un nouveau tableau, car il y a des infos qui n'apparaissent pas toujours mais qu'il faudra inséré (ex: id_gep_projet)
					unset($infos['commentaires'],$infos['__redirect'],$infos['format'],$infos['weight']);	
					if($id_societe){
						$data_path=$this->filepath($id_societe);
						if(!file_exists($data_path)) {
							util::mkdir($data_path);
						}
						$this->insertZip($data_path,$chemin."/",$infos,$infos['id_parent']);
					}else{
						$data_path="";
						$this->insertZip($data_path,$chemin."/",$infos,$infos['id_ged_dossier'],$list_file);
					}
					
					shell_exec("rm -Rf ".$chemin);
				}
			}else{
				$infos["weight"] = round(filesize($temporaryFilepath)/1048576,2);
		
				if($infos[$this->table]=$infos["filestoattach"]["fichier"]){
					if($infos['id_'.$this->table]=parent::insert($infos,$s,$files)){

						$target=$this->filepath($infos['id_'.$this->table],"fichier");
						if(!file_exists(dirname($target))){
							util::mkdir(dirname($target));
						}
						rename($temporaryFilepath,$target);
					}
				}
			}
			if(is_array($cadre_refreshed)){
				if($infos['id_gep_projet']){
					ATF::gep_projet()->redirection("select",$infos['id_gep_projet']);
				}elseif($infos['id_societe']){
					ATF::societe()->redirection("select",$infos['id_societe'],"societe-select-".$this->cryptId($infos['id_societe']).".html:ged");
				}else{
					ATF::societe()->redirection("select_all");
				}
			}
		}
		return ($list_file?$list_file:$infos['id_'.$this->table]);
	}
	
	/**
	* Permet de récupérer et insérer de manière récursive, les documents qui ont été dézippés
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $chemin : chemin temporaire dans lequel on a dézippé le fichier
	* @param array $infos : informations générales nécessaires pour chaque insertion de fichier
	* @parem integer $id_parent : identifiant de l'élément parent à celui en cours (ex: id du dossier dans lequel contient le fichier que je regarde)
	*/
	public function insertZip($data_path,$chemin,$infos,$id_parent=NULL){
		$liste_originale=scandir($chemin);
		//on enlève les fichiers "." et ".."
		$liste_originale=array_flip($liste_originale);
		unset($liste_originale["."],$liste_originale[".."]);
		$liste_originale=array_flip($liste_originale);
		//on va ajouter le chemin complet au titre de l'image contenu dans le tableau
		foreach($liste_originale as $key=>$item){
			if($item!=$infos['id_owner'].".fichier"){
				unset($infos2);
				$infos2=$infos;
				$infos2['ged']=preg_replace("`[^a-zA-z0-9ÄËÜÏÖäëüïöÂÊÛÎÔâêûîôÀÈàèÉéùíÇç()-. ]`","_",iconv('IBM850', 'UTF-8',$item));
				if(is_file($chemin."/".$item)){
					$pathinfo = pathinfo($item);
					$infos2["format"] = $pathinfo['extension'];
					$infos2["weight"] = round(filesize($chemin."/".$item)/1048576,2);
					$infos2['id_parent']=$id_parent;
					$id_fichier=parent::insert($infos2);
					copy($chemin."/".$item,$data_path."/".$id_fichier);
				}else{
					$infos2['dossier']=1;
					$infos2['id_parent']=$id_parent;
					$id_parent2=parent::insert($infos2);
					$this->insertZip($data_path,$chemin."/".$item,$infos,$id_parent2);
				}
			}
		}
		return true;
	}
		
	/**
	* Suppression des éléments via extjs (si on coche un dossier, on supprime tout son contenu)
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function sup_ged($infos,&$s,$files=NULL,&$cadre_refreshed){
		//on créé la liste de nodes
		$liste_node=explode(",",$infos['node']);
		//on récupère les id qui nous interesse
		foreach($liste_node as $key=>$item){
			preg_match("/\[Node (.[^']*)\]$/",$item,$node);
			$tab[]=$node[1];
		}
		//on prends le dernier node comme référence pour connaître l'id société des geds
		$id_societe=$this->recup_soc($node[1]);
		
		//structure un tableau contenant les données à supprimer
		foreach($tab as $id=>$valeur){
			//si l'on a déjà supprimé les éléments, on check d'abord que ce qui a été coché n'en fait pas parti
			//si c'est le cas on ne refait pas le test de suppression
			if(!$tab_node_utilise || !isset($tab_node_utilise[$valeur])){
				//si il s'agit d'un fichier
				$donnees_ged=$this->select($valeur);
				if($donnees_ged['dossier']==0){
					$tab_node_utilise[$valeur]=1;
					$tab_node_sup[][$valeur]=0;
				}else{
					$tab_node_utilise[$valeur]=1;
					$tab_node_sup[][$valeur]=1;
					$tab_node_sup=$this->recup_doc_sup(array("id_ged"=>$valeur,"id_societe"=>$id_societe),$tab_node_utilise,$tab_node_sup);
				}
			}
		}
		//le tableau tab_node_sup est normalement mis dans l'ordre du coche, il nous faut inverser l'ordre, pour pouvoir supprimer les enfants avant les parents
		krsort($tab_node_sup);
		
		//création de la corbeille (rep temporaire qui se vide si toutes les requêtes sont bien exécutées)
		$bin_path=$this->filepath($infos["id_user"],'corbeille',true);
		mkdir($bin_path);

		//on supprime tous les dossiers une fois qu'on est sur que tous les fichiers ont été delete
		//donc obligé de faire un second foreach plutot que de tout mettre en un seul
		ATF::db($this->db)->begin_transaction();
		
		try{
			foreach($tab_node_sup as $cle=>$ged){
				foreach($ged as $id_ged=>$dossier){
					//si une erreur dans le delete, elle est recupérée dans le catch parent qui permet de remettre les fichiers éventuellement supprimés auparavant
					parent::delete($id_ged);
					
					//une fois qu'on est sur que la donnée a été supprimée, on peut supprimer le fichier ($dossier=1 => dossier, $dossier=0 => fichier)
					if($dossier==0){
						//envoi des fichiers dans la corbeille 
						copy($this->filepath($id_societe)."/".$id_ged,$bin_path."/".$id_ged);
						unlink($this->filepath($id_societe)."/".$id_ged);
					}
				}	
			}
		}catch(errorATF $e){
			//remet tous les fichiers supprimés et envoi l'erreur
			$liste_fichier=scandir($bin_path);

			//on enlève les fichiers "." et ".."
			$liste_fichier=array_flip($liste_fichier);
			unset($liste_fichier["."],$liste_fichier[".."]);
			$liste_fichier=array_flip($liste_fichier);
		
			//on va ajouter le chemin complet au titre de l'image contenu dans le tableau
			foreach($liste_fichier as $k=>$i){
				$path_parts = pathinfo($i);
				copy($bin_path."/".$path_parts['filename'],$this->filepath($id_societe)."/".$path_parts['filename']);
			}
			ATF::db($this->db)->rollback_transaction(true);
			throw $e;
		}
		
		ATF::db($this->db)->commit_transaction(true);
		
		//suppression de la corbeille et de son contenu
		shell_exec("rm -rf ".$bin_path);
		
		if (is_array($cadre_refreshed)) { //Redirection vers le module d'où on provenait si il s'agissait d'un onglet
			$this->redirection("select_all_optimized",$infos["pager"]);
			//$cadre_refreshed[$infos['div']] = ATF::$html->fetch($infos['template'].".tpl.htm");
		}
	}
	
	/**
	* Permet de récupérer tous les ged enfants de celui coché
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array data => integer id_ged : identifiant du ged courant
							integer id_societe : identifiant de la societe courante
	* @param array $tab_node_utilise : contient les nodes présent dans le dossier
	* @param array $tab_node_sup : contient les nodes à supprimer
	* @return array $tab_node_sup : contient les nodes à supprimer
	*/
	public function recup_doc_sup($data,&$tab_node_utilise,$tab_node_sup){
		if (!$data['id_ged']) return false;
		//on va chercher les enfants de ce noeud, si il n'y en a pas, il s'agit d'un fichier ou dossier vide
		$this->q->reset()->addCondition("id_parent",$data['id_ged']);
		if($donnees=$this->sa()){
			foreach($donnees as $cle=>$infos){
				//si c'est un dossier on relance la méthode pour supprimer son contenu
				if($infos['dossier']==1){
					$tab_node_utilise[$infos['id_ged']]=1;
					$tab_node_sup[][$infos['id_ged']]=1;
					$data['id_ged']=$infos['id_ged'];
					$tab_node_sup=$this->recup_doc_sup($data,$tab_node_utilise,$tab_node_sup);
				}else{
					$tab_node_utilise[$infos['id_ged']]=1;
					$tab_node_sup[][$infos['id_ged']]=0;
				}
			}
		}else{
			if($this->select($data['id_ged'],'dossier')==1){
				$tab_node_utilise[$data['id_ged']]=1;
				$tab_node_sup[][$data['id_ged']]=1;
			}else{
				$tab_node_utilise[$data['id_ged']]=1;
				$tab_node_sup[][$data['id_ged']]=0;
			}
		}
		return $tab_node_sup;
	}
	
	/**
	* Créer un dossier à la racine du tree
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
	public function insert_dir($infos){
		$i = array(
			'date'=>date("Y-m-d H:i:s")
			,'ged'=>str_replace("/","-",$infos['nom_rep'])
			,'dossier'=>1
			,'id_'.$infos['element']=>$infos['valeur']
		);
		if (isset($infos['id_parent']) && $infos['id_parent']!="undefined" && $infos['id_parent']!="null") {
			$i['id_parent'] = $infos['id_parent'];
		}
		if (isset($infos['id_user']) && $infos['id_user']!="undefined" && $infos['id_user']!="null") {
			$i['id_owner'] = $infos['id_user'];
		} else {
			$i['id_owner'] = ATF::$usr->getID();
		}
		return parent::insert($i);
	}
	
	/**
	* Surcharge pour prendre en compte le chemin des dossiers parents
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/
	public function update($infos,&$s,$files=NULL,&$cadre_refreshed){
		if ($infos['noRedirection']) {
			unset($infos['noRedirection']);
			return parent::update($infos,$s,$files);
		}
		return parent::update($infos,$s,$files,$cadre_refreshed);
	}
	
	/**
	* ATTENTION : ne pas effacer pour le bon fonctionnement de l'insert !
	* Surcharge de check_files pour justement éviter de se voir retourner une erreur a chaque création.
	* Le check est fait en interne dans la classe
	*/	
//	public function check_files(){	}	
	public function move_files(){	}	
	
};
?>