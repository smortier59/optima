<?
/** 
* Classe ged : Gestion électronique de documents
* @package includes
*/
require_once dirname(__FILE__)."/ged.class.php";
class ged_dossier extends ged {
	/**
	* Constructeur
	*/
	public function __construct() {
		parent::__construct();
		$this->controlled_by="ged";
		$this->table = __CLASS__;
		$this->colonnes['fields_column'] = array(
			'ged_dossier.ged_dossier'
		);
		$this->fieldstructure();

		$this->no_insert = true;
		$this->no_update = true;
		$this->no_delete = true;
		
		$this->addPrivilege("branch");
		$this->addPrivilege("ajout_rep");
		$this->addPrivilege("sup_rep");
	}
	
	/** Récupère le parent 'racine'
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function recupParentRoot($id_parent){
		//si le parent a lui meme un parent, on va le chercher
		$this->q->reset()->addCondition("id_ged_dossier",$id_parent);
		$parent=parent::select_row();
		if($parent["id_parent"])$parent=self::recupParentRoot($parent["id_parent"]);
		return $parent;
	}
	
	/** Surchage de branche car cas particulier pour source
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function branch($infos,&$s,$files=NULL,&$cadre_refreshed){
		//récupération de tous les repertoires liés au module courant
		$this->q->reset()->addJointure($this->table,"id_".$this->table,$infos["table_join"]."_".$this->table,"id_".$this->table)
						->addCondition($infos["table_join"]."_ged_dossier.id_".$infos["table_join"],ATF::getClass($infos["table_join"])->decryptId($infos["valeur"]))
						->setArrayKeyIndex("ged_dossier",false);
		
		if ($infos["node"]=="source") {
			if ($infos['source']) {
				$this->q->reset("where")->addCondition("id_ged_dossier",$infos["source"]);
			} else {
				$this->q->addCondition("id_parent",NULL,"OR",false,"IS NULL");
			}
		} elseif ($infos['node']) {
			$this->q->addCondition("id_parent",$infos["node"]);
		}
		
		$liste_rep=parent::select_all();				
		
		if ($infos["node"]=="source") {
			if ($infos['source']) {
				foreach($liste_rep as $key=>$item){
					if($item["id_ged_dossier"]==$infos["source"])$items[$key]=$item;
				}
			} else {
				foreach($liste_rep as $key=>$item){
					if(!$item["id_parent"])$items[$key]=$item;
					else{
						$parent=$this->recupParentRoot($item["id_parent"]);
						$items[$parent["ged_dossier"]]=$parent;
					}
				}
			}
		} elseif ($infos['node']) {
			foreach($liste_rep as $key=>$item){
				if($item["id_parent"]==$infos["node"])$items[$key]=$item;
			}
		}
		
		
		//Partie ossature
		if ($infos["node"]=="source") {	
			self::transOssature(ATF::getClass($infos["table_join"])->ged_ossature,$items);
		} elseif ($infos['node']) {
			$liste_parent=explode("/",$infos["node"]);
			$ossa=ATF::getClass($infos["table_join"])->ged_ossature;
			foreach($liste_parent as $key=>$item){
				$ossa=$ossa[$item];
			}
			self::transOssature($ossa,$items,$infos["node"]);
		}
		
		if($items){	
			ksort($items);
			foreach ($items as $item) {
				$return[]=$this->item($item,$infos["valeur"]);
			}
		}
		$infos['display'] = true;
		return json_encode($return);
	}

	/** Paramètre des éléments du tree
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function item($infos,$id_element){
		 return array("adapter"=>$infos["id_parent"]
						,"text"=>$infos["ged_dossier"]
						,"id"=>$infos["id_ged_dossier"]
						,"leaf"=>0
						,"cls"=>"folder"
						,"href"=>"javascript:ATF.refreshGridGed('".$infos["id_ged_dossier"]."','".$id_element."')");
	}
	
	/** Affichage de l'ossature
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function transOssature($oss,&$ossature,$id_parent=NULL){
		foreach($oss as $key=>$item){
			if(is_numeric($key))$key=$item;
			if(!$ossature["$key"]){
				$id_ged_dossier=($id_parent?"$id_parent/":"")."$key";
				$ossature["$key"]=array("id_ged_dossier"=>"$id_ged_dossier","ged_dossier"=>"$key","id_parent"=>$id_parent);
			}
		}
	}
	
	/** Ajout d'un répertoire
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function ajout_rep($infos){
		$insert=array("ged_dossier"=>$infos['nom']);
		if($infos['id_dossier_parent'] && is_numeric($infos['id_dossier_parent']))$insert['id_parent']=$infos['id_dossier_parent'];
		elseif($infos['id_dossier_parent'])$insert['id_parent']=self::materialise($infos['id_dossier_parent'],$infos['table_join'],ATF::getClass($infos["table_join"])->decryptId($infos['val_table_join']));
		$id_gd=$this->i($insert);
		
		ATF::getClass($infos['table_join']."_ged_dossier")->insert(array("id_".$infos['table_join']=>$infos['val_table_join'],"id_ged_dossier"=>$id_gd));
		return $id_gd;
	}
	
	/** Suppression d'un répertoire
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function sup_rep($infos){
		if($infos['id_dossier']){
			$this->q->reset()->addJointure("ged_dossier","id_ged_dossier","ged_fichier","id_ged_dossier")
							->addCondition("ged_dossier.id_parent",$infos['id_dossier'],"OR","cond")
							->addCondition("ged_fichier.id_ged_dossier",$infos['id_dossier'],"OR","cond");
			if($this->sa()){
				return false;
			}else{
				//si ce n'est pas le cas, on peut supprimer le répertoire
				return $this->d($infos['id_dossier']);
			}
		}
		return false;	
	}
	
	/** matérialise les dossiers d'ossatures (qui sont virtuels par défaut)
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function materialise($nom_dossier,$table_jointe,$id_element){
		$liste=explode("/",$nom_dossier);
		foreach($liste as $key=>$item){
			if($key==0)$id_ged_dossier=$this->i(array("ged_dossier"=>$item));
			else $id_ged_dossier=$this->i(array("ged_dossier"=>$item,"id_parent"=>$id_ged_dossier));
			ATF::getClass($table_jointe."_ged_dossier")->insert(array("id_$table_jointe"=>$id_element,"id_ged_dossier"=>$id_ged_dossier));
		}
		return $id_ged_dossier;
	}
};
?>