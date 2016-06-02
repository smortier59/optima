<?
/** 
* Classe ged : Gestion électronique de documents
* @package includes
*/
require_once dirname(__FILE__)."/ged.class.php";
class ged_fichier extends ged {
	/**
	* Constructeur
	*/
	public function __construct() {
		parent::__construct();
		$this->controlled_by="ged";
		$this->table = __CLASS__;
		$this->colonnes['fields_column'] = array(
			'ged_fichier.ged_fichier'
			,'ged_fichier.date'
			,'ged_fichier.format'=>array("width"=>50)
			,'ged_fichier.version'=>array("width"=>50)
			,'ged_fichier.weight'=>array("width"=>75)
		);
		$this->colonnes['primary'] = array('commentaires');
		$this->colonnes['bloquees']['insert'] = array("id_user","ged_fichier","date","format","version","weight","id_ged_dossier");

		$this->fieldstructure();
		$this->autocomplete = array(
			"field"=>array("ged_fichier.ged_fichier")
			,"show"=>array("ged_fichier.ged_fichier")
			,"popup"=>array("ged_fichier.ged_fichier")
			,"view"=>array("ged_fichier.ged_fichier")
		);
		$this->no_update = true;
		
		$this->addPrivilege("updateListing");
		$this->addPrivilege("fenGed");
		$this->addPrivilege("deleteAllVersion");
	}
	
	/** Raffraîchit le gridpanel avec les nouvelles infos
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/	
	public function updateListing(&$infos){
		$liste_parent=explode("/",$infos['id_ged_dossier']);
		$infos['id_ged_dossier']=$liste_parent[count($liste_parent)-1];
		$q=ATF::_s("pager")->create($infos['pager'])->reset("where");
		if($infos['id_ged_dossier'])$q->addCondition("id_ged_dossier",$infos['id_ged_dossier'])
										->addJointure($this->table,"id_".$this->table,$infos["table_join"]."_ged_fichier","id_".$this->table,NULL,NULL,NULL,NULL,"inner")
										->addCondition($infos["table_join"]."_ged_fichier.id_".$infos["table_join"],ATF::getClass($infos["table_join"])->decryptId($infos['id_element']));
		else $q->addConditionNull("id_ged_dossier");
		
		$infos['q']=$q;
		$infos['current_class']=ATF::ged_fichier();
		$infos['table']="ged_fichier";
		$infos["pager"]=$infos['pager'];
		$infos["noUpdate"]=false;

		ATF::$html->array_assign($infos);
		
		$js = ATF::$html->fetch("generic-gridpanel_vars.tpl.js");
		
		$infos["display"]=true;
		return $js;
	}
	
	/** Permet d'afficher le système de ged
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function fenGed(&$infos){
		$q=ATF::_s("pager")->create($infos['pager'])->reset()->addConditionNull("id_ged_dossier")
															->addJointure($this->table,"id_".$this->table,$infos["table_join"]."_ged_fichier","id_".$this->table,NULL,NULL,NULL,NULL,"inner")
															->addCondition($infos["table_join"]."_ged_fichier.id_".$infos["table_join"],ATF::getClass($infos["table_join"])->decryptId($infos['id_element']));
		$this->genericSelectAllView($q,true);
					
		$infos['q']=$q;
		$infos['current_class']=ATF::ged_fichier();
		$infos["noUpdate"]=false;
	
		ATF::$html->array_assign($infos);
		$js = ATF::$html->fetch("generic-gridpanel_vars.tpl.js");
		
		$infos["display"]=true;
		return $js;	
	}
	
	/** Insert un fichier
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed){
		$doc=$infos["ged_fichier"]["id_ged_dossier"];
		if($doc && !is_numeric($doc)){
			$infos["ged_fichier"]["id_ged_dossier"]=ATF::ged_dossier()->i(array("ged_dossier"=>"$doc"));	
		}
		$infos["ged_fichier"]['id_user']=ATF::$usr->getID();
		return parent::insert($infos,$s,$files);
	}
	
	/** Gestion de l'insertion du fichier zip
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function insertZip($data_path,$chemin,$infos,$id_parent=NULL,&$list_file){
		$liste_originale=scandir($chemin);
		//on enlève les fichiers "." et ".."
		$liste_originale=array_flip($liste_originale);
		unset($liste_originale["."],$liste_originale[".."]);
		$liste_originale=array_flip($liste_originale);

		//on va ajouter le chemin complet au titre de l'image contenu dans le tableau
		foreach($liste_originale as $key=>$item){
			if($item!=$infos['id_user'].".fichier"){
				unset($infos2);
				$infos2=$infos;
				if(is_file($chemin."/".$item)){
					$infos2[$this->table]=mb_convert_encoding(preg_replace("`[^a-zA-z0-9ÇÏÀÂÈÉÊÔéèàùëíïüôîêâûç()-.]`","_",$item),'UTF-8');
					$pathinfo = pathinfo($item);
					$infos2["format"] = $pathinfo['extension'];
					$infos2["weight"] = round(filesize($chemin."/".$item)/1048576,2);
					$infos2['id_ged_dossier']=$id_parent;
					$id_fichier=parent::insert($infos2);
					$list_file['f'][$id_fichier]=$infos2[$this->table];
					copy($chemin."/".$item,$data_path."/".$id_fichier);
				}else{
					unset($infos2);
					$infos2["ged_dossier"]=$item;
					$infos2['id_parent']=$id_parent;
					$id_parent2=ATF::ged_dossier()->i($infos2);
					$list_file['r'][$id_parent2]=$item;
					$this->insertZip($data_path,$chemin."/".$item,$infos,$id_parent2,$list_file);
				}
			}
		}
		return true;
	}
	
	/** Système de Drag and Drop
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function uploadXHR(&$infos,&$s,$files=NULL,&$cadre_refreshed=NULL){
		$id_el=ATF::getClass($infos["table_join"])->decryptId($infos['val_table_join']);
		
		//si le dossier sélectionné est virtuel (nom string), on le matérialise avec ses parents
		if($infos['id_ged_dossier'] && !is_numeric($infos['id_ged_dossier'])){
			$infos['id_ged_dossier']=ATF::ged_dossier()->materialise($infos['id_ged_dossier'],$infos['table_join'],$id_el);
		}elseif(!$infos['id_ged_dossier']){
			$infos['id_ged_dossier']=NULL;
		}
		$infos["key"]=$infos["field"];
		if(isset($files[$infos["field"]])){
			$path = "/tmp/uploadXHR-".ATF::$codename."-".date("YmdHis")."-".ATF::$usr->getID();
			file_put_contents($path,file_get_contents($files[$infos["field"]]['tmp_name']));
			$files[$infos["field"]]['tmp_name']=$path;
		}else{
			$path = "/tmp/uploadXHR-".ATF::$codename."-".date("YmdHis")."-".ATF::$usr->getID();
			file_put_contents($path,file_get_contents('php://input'));
			$files[$infos["field"]]=array(
				"name"=>ATF::_srv("HTTP_X_FILE_NAME")
				,"type"=>ATF::_srv("HTTP_X_FILE_TYPE")
				,"tmp_name"=>$path
				,"error"=>0
				,"size"=>ATF::_srv("HTTP_X_FILE_SIZE")
			);
		}
		$retour=$this->uploadExt($infos,$s,$files,$cadre_refreshed);
		
		$nom_fichier=mb_convert_encoding(preg_replace("`[^a-zA-z0-9ÇÏÀÂÈÉÊÔéèàùëíïüôîêâûç()-.]`","_",$files[$infos["field"]]['name']),'UTF-8');
		$list_file=$this->insert(array("extAction"=>"ged_fichier"
										,"extMethod"=>"insert"
										,"ged_fichier"=>array("id_ged_dossier"=>$infos['id_ged_dossier'],"filestoattach"=>array("fichier"=>$nom_fichier))));

		//pour chaque fichiers insérés, leur attribué l'élément courant (table jointe)
		if(is_numeric($list_file)){
			//on vérifie si le fichier existe déjà, si c'est le cas on crée une nouvelle version
			$version=$this->checkVersion($infos,$nom_fichier,$id_el);
			if($version>1){
				$this->update(array("id_ged_fichier"=>$list_file,"version"=>$version));
			}
			ATF::getClass($infos["table_join"]."_ged_fichier")->insert(array("id_".$this->table=>$list_file,"id_".$infos["table_join"]=>$id_el));
		}elseif(is_array($list_file)){
			foreach($list_file['f'] as $id_fichier=>$nom_fichier){
				$version=$this->checkVersion($infos,$nom_fichier,$id_el);
				if($version>1){
					$this->update(array("id_ged_fichier"=>$id_fichier,"version"=>$version));
				}
				$array_insert[]=array("id_".$this->table=>$id_fichier,"id_".$infos["table_join"]=>$id_el);
			}
			ATF::getClass($infos["table_join"]."_ged_fichier")->multi_insert($array_insert);
			
			foreach($list_file['r'] as $id_rep=>$nom_rep){
				$array_insert_rep[]=array("id_ged_dossier"=>$id_rep,"id_".$infos["table_join"]=>$id_el);
			}
			if($array_insert_rep)ATF::getClass($infos["table_join"]."_ged_dossier")->multi_insert($array_insert_rep);
		}
		
		return $infos['id_ged_dossier'];
	}
	
	/** Vérifie si il y a une version précédente du même fichier et insère en conséquence
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function checkVersion($infos,$filename,$id_el){
		//on vérifie si le fichier existe déjà, si c'est le cas on crée une nouvelle version
		$this->q->reset()->addField("version")->setStrict()
						->addOrder("version","desc")
						->addCondition("ged_fichier",$filename)
						->addJointure($this->table,"id_".$this->table,$infos["table_join"]."_ged_fichier","id_".$this->table,NULL,NULL,NULL,NULL,"inner")
						->addCondition($infos["table_join"]."_ged_fichier.id_".$infos["table_join"],$id_el);
		if($infos['id_ged_dossier'])$this->q->addCondition("id_ged_dossier",$infos['id_ged_dossier']);
		else $this->q->addConditionNull("id_ged_dossier");
		
		if($version=$this->select_cell()){
			return $version+=1;
		}else{
			return 1;
		}
	}
	
	/** Surcharge pour gestion de l'affichage en fonction du versionning
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){
		$c_gf = new ged_fichier();
		$c_gf->q->setAlias("gf")
				->addField('id_ged_fichier')
				->addOrder('gf.version','desc')
				->setStrict()
				->setToString();
		$subQuery = $c_gf->sa();
			
		$this->q->addJointure($this->table,"id_".$this->table,"gf","id_".$this->table,"gf",NULL,NULL,NULL,"inner",NULL,$subQuery)
				->addGroup($this->table.".".$this->table);	
		return parent::select_all($order_by,$asc,$page,$count);
	}
	
	/** Supprime toutes les versions d'un même fichier
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function deleteAllVersion($infos){
		foreach($infos['id'] as $key=>$id){
			//on récupère les informations de la ged a delete pour retrouver les éventuelles autres versions
			$donnees=$this->select($this->decryptId($id));
			
			$this->q->reset()->addField("ged_fichier.id_ged_fichier","id_gf")->setStrict()
							->addCondition("ged_fichier",$donnees["ged_fichier"])
							->addJointure($this->table,"id_".$this->table,$infos["table_join"]."_ged_fichier","id_".$this->table,NULL,NULL,NULL,NULL,"inner")
							->addCondition($infos["table_join"]."_ged_fichier.id_".$infos["table_join"],ATF::getClass($infos["table_join"])->decryptId($infos['id_element']));
			if($donnees['id_ged_dossier'])$this->q->addCondition("id_ged_dossier",$donnees['id_ged_dossier']);
			else $this->q->addConditionNull("id_ged_dossier");
			
			foreach($this->sa() as $cle=>$id_sup){
				$supp[]=$id_sup['id_gf'];
			}		
		}
		$this->delete($supp);
	}
};
?>