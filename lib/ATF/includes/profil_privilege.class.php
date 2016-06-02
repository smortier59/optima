<?php
/** Classe privilege : Gestion des droits sur ATF5
* @package ATF
*/
class profil_privilege extends classes_optima {
	/*---------------------------*/
	/*      Attributs            */
	/*---------------------------*/
	
	/*---------------------------*/
	/*      Constructeurs        */
	/*---------------------------*/	
	public function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		$this->controlled_by = "profil";
		$this->addPrivilege("modification","update");
	}
		
	/*---------------------------*/
	/*      Méthodes             */
	/*---------------------------*/	
	
	/** Créer la liste des modules avec les privilèges associés pour ce profil
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param int profil : le profil du select sur lequel on est
	*/
	public function a_privilege($id_profil){
		$id_profil=ATF::profil()->decryptId($id_profil);
		$this->q->reset();
        $this->q->addCondition('id_profil',$id_profil);
		foreach($this->select_all() as $key=>$item){
			$listing[$item['id_module']][$item['id_privilege']]=$item['id_profil_privilege'];
		}
		
		return $listing;
	}
	
	/** Permet d'ajouter ou de supprimer un privilège sur un module
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function modification($infos){
		//stockage des informations pour récupération dans template
		$infos['id_profil']=ATF::profil()->decryptId($infos['id_profil']);
		$infos['id_module']=ATF::module()->decryptId($infos['id_module']);
		$infos['id_privilege']=ATF::privilege()->decryptId($infos['id_privilege']);
		if($infos['coche']=='true'){
			unset($infos['coche']);
			parent::insert($infos);
			$infos['coche']=true;
		}else{
			$this->q->reset("where");
			$this->q->addCondition('id_profil',$infos['id_profil'])
					->addCondition('id_module',$infos['id_module'])
					->addCondition('id_privilege',$infos['id_privilege']);
			parent::delete();
		}
		ATF::$cr->rm("top");
		ATF::$cr->add($infos['id_module']."_".$infos['id_privilege'],"profil_check.tpl.htm",array('infos'=>$infos));
	}
	
	/** Permet de supprimer tous les privilèges sur le profil concerné
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array $infos : informations servant à la suppression
	*/
	public function delete($infos,&$s,$files=NULL,&$cadre_refreshed){
		if(is_array($infos)){
			$id_profil_crypte=$infos['id_profil'];
			$infos['id_profil']=ATF::profil()->decryptId($infos['id_profil']);
			$this->q->reset();
			
			if($infos['id_module']){
				$infos['id_module']=ATF::module()->decryptId($infos['id_module']);
				//si on a un module, mais qu'on ne souhaite pas réinitialiser les enfants avec
				if(isset($infos['sup_mod'])){
					unset($infos['sup_mod']);
				}else{	
					//suppression des privilèges pour les modules enfants
					//si il s'agit d'un parent 
					if($enfants=ATF::$usr->structureModule($infos['id_module'],NULL)){
						$this->multi_delete($enfants,$infos['id_profil'],$id_profil_crypte);
						parent::delete();
					}
				}
				
				$this->q->reset()
						->addCondition("id_module",$infos['id_module'])
						->addCondition("id_profil",$infos['id_profil']);
				
				$modul=ATF::module()->select($infos['id_module'],'module');
				
				foreach(ATF::privilege()->privilege as $cle_priv=>$info_priv){
					//on enlève le privilège uniquement si on en a le droit
					if (ATF::$usr->privilege($modul,ATF::privilege()->nom($info_priv['id_privilege']))){
						$this->q->addCondition("id_privilege",$info_priv['id_privilege'],"OR","id_privilege");
						$infos['id_module']=$infos['id_module'];
						$infos['id_privilege']=$info_priv['id_privilege'];
						$infos['id_profil']=$id_profil_crypte;
						$infos['coche']=false;
						ATF::$cr->rm("top");
						ATF::$cr->add($infos['id_module']."_".$info_priv['id_privilege'],"profil_check.tpl.htm",array('infos'=>$infos),"fetchWithAnalyzer",false,true);
					}	
				}
				parent::delete();
			}else{
				$this->multi_delete(NULL,$infos['id_profil'],$id_profil_crypte);
				parent::delete();
			}
		}else{
			//dans le cas d'un rollback de trace
			parent::delete($infos,$s,$files=NULL,$cadre_refreshed);
		}
	}
	
	/** Méthode récursive permettant de créer la requête de suppression massive des privilèges sur les modules
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array $enfants : liste des modules qu'il faut supprimer de la table (enfants du module sélectionné)
	* @param int $id_profil : profil sur lequel on se situe
	* @param int $id_profil_crypte : profil crypte sur lequel on se situe
	*/
	public function multi_delete($enfants,$id_profil,$id_profil_crypte){
		foreach(($enfants?$enfants:ATF::$usr->structureModule(NULL,NULL)) as $cle_mod=>$info_mod){
			if(is_numeric($cle_mod)){
				$this->q->addCondition("id_module",$cle_mod);
				$this->q->addCondition("id_profil",$id_profil);
				$this->q->addSuperCondition("id_module,id_profil","AND","A",false);
				
				foreach(ATF::privilege()->privilege as $cle_priv=>$info_priv){
					if (ATF::$usr->privilege($info_mod['module'],$info_priv['privilege'])){
						$this->q->addCondition("id_privilege",$info_priv['id_privilege'],"OR","id_privilege");
						$infos['id_profil']=$id_profil_crypte;
						$infos['coche']=false;
						$infos['id_module']=$cle_mod;
						$infos['id_privilege']=$info_priv['id_privilege'];
						ATF::$cr->rm("top");
						ATF::$cr->add($infos['id_module']."_".$info_priv['id_privilege'],"profil_check.tpl.htm",array('infos'=>$infos),"fetchWithAnalyzer",false,true);
					}
				}
				
				$this->q->addSuperCondition("A,id_privilege","AND","AB");
				$this->q->addSuperCondition("AB,fini","OR","fini",false);
				
				if($info_mod['enfants']){
					$liste_id=$this->multi_delete($info_mod['enfants'],$id_profil,$id_profil_crypte);
				}
			}
		}
		return $liste_id;
	}
	
	/** Permet d'insérer tous les privilèges pour tous les modules sur le profil concerné
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed){
		
		ATF::db($this->db)->begin_transaction();
		
		/*parent::multi_insert(array(0=>array('id_profil'=>1,'id_privilege'=>1,'id_module'=>2)));*/
		$infos['id_profil']=ATF::profil()->decryptId($infos['id_profil']);
		//si l'on précise un module
		if($infos['id_module']){
			$infos['id_module']=ATF::module()->decryptId($infos['id_module']);
			//suppression des privilèges de ce module au cas où ils sont déjà présent, mais que le user clique sur le bouton d'ajout
			$infos['sup_mod']=true;
			$this->delete($infos,$s);
			
			$profil_crypte=ATF::profil()->decryptId($infos['id_profil']);
			$id_profil=$infos['id_profil'];
			
			$modul=ATF::module()->select($infos['id_module']);
			
			foreach(ATF::privilege()->privilege as $cle_priv=>$info_priv){
				if (ATF::$usr->privilege($modul['module'],ATF::privilege()->nom($info_priv['id_privilege'])) && (!$modul['privilege'] || $modul['privilege'][$info_priv['id_privilege']])){
					$tab[]=array('id_profil'=>$id_profil,'id_privilege'=>$info_priv['id_privilege'],'id_module'=>$infos['id_module']);
					$infos['id_profil']=$profil_crypte;
					$infos['id_privilege']=$info_priv['id_privilege'];
					$infos['coche']=true;
					ATF::$cr->rm("top");
					ATF::$cr->add($infos['id_module']."_".$info_priv['id_privilege'],"profil_check.tpl.htm",array('infos'=>$infos),"fetchWithAnalyzer",false,true);
				}
			}

			parent::multi_insert($tab);
		}else{
			//suppression des informations avant insertion (permet d'eviter les dupplicate et de garder une trace des anciens droits)
			$this->delete($infos,$s);
			$id_profil_crypte=ATF::profil()->decryptId($infos['id_profil']);
			
			parent::multi_insert($this->tab_mod_priv($infos['id_profil'],$id_profil_crypte,NULL,NULL));
		}
		
		ATF::db($this->db)->commit_transaction();
	}
	
	/** Méthode récursive permettant la structure du tableau pour l'insertion massive des privilèges sur les profils
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array $enfants : liste des modules qu'il faut ajouter (enfants du module sélectionné)
	* @param int $id_profil : profil sur lequel on se situe
	* @param int $id_profil_crypte : profil crypte sur lequel on se situe
	*/
	public function tab_mod_priv($id_profil,$id_profil_crypte,$tab=NULL,$enfants=NULL){
		foreach(($enfants?$enfants:ATF::$usr->structureModule(NULL,NULL)) as $cle_mod=>$info_mod){
			if(is_numeric($cle_mod)){
				$modul=ATF::module()->select($cle_mod);
				foreach(ATF::privilege()->privilege as $cle_priv=>$info_priv){
					if (ATF::$usr->privilege($info_mod['module'],$info_priv['privilege']) && (!$modul['privilege'] || $modul['privilege'][$info_priv['id_privilege']])){
						$infos['id_profil']=$id_profil_crypte;
						$infos['id_module']=$cle_mod;
						$infos['id_privilege']=$info_priv['id_privilege'];
						$infos['coche']=true;
						ATF::$cr->rm("top");
						ATF::$cr->add($infos['id_module']."_".$info_priv['id_privilege'],"profil_check.tpl.htm",array('infos'=>$infos),"fetchWithAnalyzer",false,true);
						$tab[]=array('id_profil'=>$id_profil,'id_privilege'=>$info_priv['id_privilege'],'id_module'=>$cle_mod);
					}
				}
				if($info_mod['enfants']){
					$tab=$this->tab_mod_priv($id_profil,$id_profil_crypte,$tab,$info_mod['enfants']);
				}
			}
		}
		return $tab;
	}
};
?>