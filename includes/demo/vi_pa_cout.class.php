<?
/**
* Classe VI PA COUT
*
*
* @date 2009-10-31
* @package inventaire
* @version 1.0.0
* @author Yann GAUTHERON <ygautheron@absystech.fr>
*
*/ 
class vi_pa_cout extends classes_optima {
	function __construct() { // PHP5
		parent::__construct();
		$this->table = __CLASS__;
		
		$this->no_insert = true;
		$this->no_update = true;
		
		$this->fieldstructure();
		
		$this->controlled_by = "visite";
		
		$this->addPrivilege("getCosts");
		$this->addPrivilege("addCost","update");
		$this->addPrivilege("updCost","update");
		$this->addPrivilege("delCost","update");
	}	
	
	/**
    * Récupère les vi_pa_cout
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $id_vi_pa 
    * @return array
    */   
	function getCosts($id_vi_pa,$dateMax=NULL){
		// Infos de la réponse
		$vi_pa = ATF::vi_pa()->select($id_vi_pa);
		
		// Tous les coûts demandés sur cette réponse
		$this->q->reset()->addCondition("id_vi_pa",$id_vi_pa);
		$r = $this->select_all();
		foreach ($r as $k => $i) {
			if ($dateMax && strtotime($dateMax)<=$i['date']) {
				continue;
			}
			$r[$k]["vi_pa"]=$vi_pa;
			$r[$k]["cout_catalogue"]=ATF::cout_catalogue()->select($i["id_cout_catalogue"]);
					
			// Coût catalogue spécifique de projet
			if ($i["id_cout_catalogue"]) {
				$id_gep_projet = ATF::pa()->select($vi_pa["id_pa"],"id_gep_projet");
				
				/* Projet Reunion GE et ACC, il faut appliquer un coeff 1.3 a tous les coûts */
				if ($id_gep_projet==35 || $id_gep_projet==42) {
					$r[$k]["cout_catalogue"]["cout_unitaire"] = 1.3*$r[$k]["cout_catalogue"]["cout_unitaire"];
				}
				
				if ($cc_gep=ATF::cout_catalogue_gep()->coutSpecifique($i["id_cout_catalogue"],$id_gep_projet)) {
					$r[$k]["coutStandard"]=$r[$k]["cout_catalogue"]["cout_unitaire"];
					$r[$k]["cout_catalogue"]["cout_unitaire"] = $cc_gep;
					$r[$k]["estUnCoutProjet"]=true;
				}
			}
					
			$r[$k]["cout_unitaire_reel"]=$r[$k]["cout_unitaire"];
			if (strlen($r[$k]["cout_unitaire_reel"])===0 && $vi_pa["id_pa"]) {
				$r[$k]["cout_unitaire_reel"]=ATF::cout_unitaire()->selectFromPA($i["id_cout_catalogue"],$vi_pa["id_pa"]);
			}
			if (strlen($r[$k]["cout_unitaire_reel"])===0) {
				$r[$k]["cout_unitaire_reel"]=$r[$k]["cout_catalogue"]["cout_unitaire"];
			}
			$r[$k]["regle"]=ATF::vi_pa()->getRegle($id_vi_pa);
			$r[$k]["cout_unitaire_calcule"]=ATF::vi_pa()->computeCost($id_vi_pa,$r[$k]["regle"],$r[$k]["cout_unitaire_reel"]);
		}
		return $r;
	}
	
	/**
    * Calcul la somme des coûts unitaires produits par vi_pa_cout::getCosts()
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $costs 
    * @return array
    */   
	function getCostsTotal($costs=NULL) {
		foreach ($costs as $c) {
			$r+=$c["cout_unitaire_calcule"];
		}
		return $r;
	}
	
	/**
    * Calcul la somme des coûts totaux produits par vi_pa_cout::getCosts()
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $costs 
    * @return array
    */   
	function getCostsTotalUnitaire($costs=NULL) {
		foreach ($costs as $c) {
			$r+=$c["cout_unitaire_reel"];
		}
		return $r;
	}
	
	/**
    * Ajouter un coût à cette réponse
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos 
	* 		int id_vi_pa
	* 		int id_cout_catalogue
	* 		int id_cout_unitaire (facultatif)
	* @param array $s : contenu de la session
	* @param array $files
	* @param array $cadre_refreshed
    * @return boolean true | array $infos si pas de cadre_refreshed demandé
    */   
	function addCost(&$infos,&$s,$files=NULL,&$cadre_refreshed=NULL){
		$cout = array(
			"id_vi_pa"=>$infos["id_vi_pa"]
			,"id_cout_catalogue"=>$infos["id_cout_catalogue"]
		);
		if ($infos["id_cout_unitaire"]) {
			$cout["id_cout_unitaire"]=$infos["id_cout_unitaire"];
		}
		try {
			ATF::vi_pa_cout()->insert($cout);
		} catch (error $e) {
			// Duplicate entry ne doit pas virer le texte de la modalbox
		}
		
		// Mise à jour des templates
		ATF::vi_pa()->viewCosts($infos,$s,$files,$cadre_refreshed);		
	}
	
	/**
    * Retire un coût à cette réponse
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos 
	* 		int id_vi_pa_cout
	* @param array $s : contenu de la session
	* @param array $files
	* @param array $cadre_refreshed
    * @return boolean true | array $infos si pas de cadre_refreshed demandé
    */   
	function delCost(&$infos,&$s,$files=NULL,&$cadre_refreshed=NULL){		
		$infos['id_vi_pa']=ATF::vi_pa_cout()->select($infos['id_vi_pa_cout'],'id_vi_pa');
		$infos['id_cout_catalogue']=ATF::vi_pa_cout()->select($infos['id_vi_pa_cout'],'id_cout_catalogue');
		ATF::vi_pa_cout()->delete($infos['id_vi_pa_cout']);
		
		// Mise à jour des templates
		ATF::vi_pa()->viewCosts($infos,$s,$files,$cadre_refreshed);
	}
	
	/**
    * Met à jour un cout d'expert
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos 
	* 		int id_vi_pa_cout
	* @param array $s : contenu de la session
	* @param array $files
	* @param array $cadre_refreshed
    * @return boolean true | array $infos si pas de cadre_refreshed demandé
    */   
	function updCost(&$infos,&$s,$files=NULL,&$cadre_refreshed=NULL){		
		$infos['id_vi_pa']=ATF::vi_pa_cout()->select($infos['id_vi_pa_cout'],'id_vi_pa');
		$infos['id_cout_catalogue']=ATF::vi_pa_cout()->select($infos['id_vi_pa_cout'],'id_cout_catalogue');
		ATF::vi_pa_cout()->update($infos);
		
		// Mise à jour des templates
		ATF::vi_pa()->viewCosts($infos,$s,$files,$cadre_refreshed);
	}
};
?>