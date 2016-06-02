<?php
/** Classe importer
* @package ATF
*/
class filtre_optima extends classes_optima {
	/*---------------------------*/
	/*      Constructeurs        */
	/*---------------------------*/	
	public function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		$this->controlled_by = "my";
		$this->colonnes['fields_column'] = array(
			'filtre_optima.id_user'
			,'filtre_optima.id_module'
			,'filtre_optima.filtre_optima'
			,'filtre_optima.type'
		);
		
		$this->fieldstructure();
		$this->addPrivilege("NEWgetFilters");
		$this->addPrivilege("saveFilter","insert");
		$this->addPrivilege("removeFilter","delete");
		$this->addPrivilege("getFilterSubMenu","update");
	}
	
	/**
	* Retourne la liste des filtres de l'utilisateur pour cette table
	* @author Quentin JANON <qjanon@absystech.fr>
	* @return array
	*/
	public function getFilters($table) {
		$id_module = ATF::module()->from_nom($table);		
		if (!$id_module) return false;	
		// Filtres privés
		ATF::filtre_optima()->q->reset()->addCondition("id_module",$id_module)->addCondition("id_user",ATF::$usr->getID())->addCondition('type','prive');
		if($filtres_prives=ATF::filtre_optima()->select_all()){
			foreach ($filtres_prives as $key => $item) {
				if (ATF::filtre_user()->filterExist($item["id_module"],$item["id_filtre_optima"])) {
					$filters["filtre_utilise"][$item["id_filtre_optima"]]=$item["filtre_optima"];
				} else {
					$filters["filtre_perso"][$item["id_filtre_optima"]]=$item["filtre_optima"];
				}
			}
		}
		
		// Filtres publics
		ATF::filtre_optima()->q->reset()->addCondition("id_module",$id_module)->addCondition('type','public');
		if($filtres_publics=ATF::filtre_optima()->select_all()){
			foreach ($filtres_publics as $key => $item) {
				if (ATF::filtre_user()->filterExist($item["id_module"],$item["id_filtre_optima"])) {
					$filters["filtre_utilise"][$item["id_filtre_optima"]]=$item["filtre_optima"];
				} else {
					$filters["filtre_public"]["public_".$item["id_filtre_optima"]]=$item["filtre_optima"];
				}
			}
		}
		
		return $filters;
	}
	
	/**
	* Retourne la liste des filtres de l'utilisateur pour cette table
	* @author Quentin JANON <qjanon@absystech.fr>
	* @return JSON
	*/
	public function NEWgetFilters(&$infos) {
		$id_module = ATF::module()->from_nom($infos['table']);		
		if (!$id_module) return false;	

		$filtres_prives = $filtres_publics = array("");
		// Filtres privés
		ATF::filtre_optima()->q->reset()->addCondition("id_module",$id_module)->addCondition("id_user",ATF::$usr->getID())->addCondition('type','prive');
		foreach (ATF::filtre_optima()->select_all() as $k=>$i) {
			$return[] = $i;
		}
		
		// Filtres publics
		ATF::filtre_optima()->q->reset()->addCondition("id_module",$id_module)->addCondition('type','public');
		foreach (ATF::filtre_optima()->select_all() as $k=>$i) {
			$return[] = $i;
		}
		$r[] = array(
			"text"=>ATF::$usr->trans('reinit_filter'),
			"id"=>"menuFiltreItem_".$this->table."_reset",
			"iconCls"=>"iconFiltreReset",
			"handler"=>"function (el,v) {
				Ext.getCmp('menuFiltre_".$infos['gridId']."').setText('".addslashes(ATF::$usr->trans("filter_select","privilege"))."'); 
				Ext.getCmp('".$infos['gridId']."').store.reload({ params:{ filter_key: ''} }); 
			}",
			'iconCls'=>"iconFiltreReset"
		);
		$r[] = "-";
		foreach ($return as $k=>$i) {
			$r[] = array(
				'text'=>addslashes($i['filtre_optima']),
				'id'=>"menuFiltreItem_".$i['id_filtre_optima'],
				'handler'=>"function (el,v) { 
					Ext.getCmp('menuFiltre_".$infos['gridId']."').setText('".addslashes($i['filtre_optima'])."'); 
					Ext.getCmp('".$infos['gridId']."').store.reload({ params:{ filter_key: '".$i['id_filtre_optima']."'} });	
				}",
				'menu'=>"new Ext.ux.menu.StoreMenu({
					url:'filtre_optima,getFilterSubMenu.ajax',
					baseParams: {
						id_filtre: ".$i['id_filtre_optima'].",
						'table':'".$infos['table']."',
						'gridId':'".$infos['gridId']."'
					}
				})",
				'iconCls'=>"iconFiltre".ucfirst($i['type'])

			);
		}
		$r[] = "-";
		$r[] = array(
			"text"=>addslashes(ATF::$usr->trans('add_filter')),
			"id"=>"menuFiltreItem_".$this->table."_add",
			"iconCls"=>"iconFiltreAdd",
			"handler"=>"function (el,v) {
				Nova.module = '".$infos['table']."';
				Nova.codename = '".ATF::$codename."';
				var table_assoc = ".ATF::getClass($infos['table'])->listeModuleAssocie($infos['table'])."								
				Nova.table_assoc = table_assoc;
				Ext.Ajax.request({
					url:'pager_filter.dialog',
					method:'POST',
					params:{
						'table':'".$infos['table']."'
						,'table_assoc':table_assoc
						,'new':true
					},
				   	success: function (response, opts) {
				   		$('#filtreCtn').html(response.responseText);
				 		ATF.importScript();
				    },
				    failure: function() {
						ATF.loadMask.hide();
						alert('erreur_inconnue_ajouter_tab'); 
				    }
				});	

			}"
		);


		$infos['display'] = true;
		return json_encode($r);
	}

	/**
	* Renvoi le submenu pourl es filtres, celui ci contient les lien pour l'update et le delete
	* @author Quentin JANON <qjanon@absystech.fr>
	* @return JSON
	*/
	public function getFilterSubMenu(&$infos) {
		$r[] = array(
			"text"=> ATF::$usr->trans('update_filter'), 
			"iconCls"=> 'iconFiltreUpdate',
			"handler"=>"function (el,v) {
				
				Nova.module = '".$infos['table']."';
				Nova.id_filtre = '".$infos['id_filtre']."';
				var table_assoc = ".ATF::getClass($infos['table'])->listeModuleAssocie($infos['table'])."								
				Nova.table_assoc = table_assoc;
				
				Ext.Ajax.request({
					url:'pager_filter.dialog',
					method:'POST',
					params:{
						'table':'".$infos['table']."'
						,'table_assoc':table_assoc
					},
				   	success: function (response, opts) {
				   		$('#filtreCtn').html(response.responseText);
				 		ATF.importScript();
				    },
				    failure: function() {
						ATF.loadMask.hide();
						alert('erreur_inconnue_ajouter_tab'); 
				    }
				});	
			}"
		);

		$r[] = array(
			"text"=> ATF::$usr->trans('delete_filter'), 
			"iconCls"=> 'iconFiltreDelete',
			"handler"=>"function (el,v) {	

				Ext.Msg.confirm(
					'Confirmation',
					'".ATF::$usr->trans("Etes_vous_sur")."',
					function (value) {
						if (value=='yes') {
							ATF.deleteLoadMask.show(); 
							Ext.Ajax.request({
								url:'filtre_optima,removeFilter.ajax',
								method:'POST',
								params:{
									'table':'".$infos['table']."'
									,'id_filtre_optima':'".$infos['id_filtre']."'
								},
							   	success: function (response, opts) {
							   		Ext.getCmp('menuFiltre_".$infos['gridId']."').menu.store.reload();
							   		ATF.deleteLoadMask.hide(); 
							    },
							    failure: function() {
									ATF.deleteLoadMask.hide(); 
									alert('erreur_inconnue_ajouter_tab'); 
							    }
							});	
						}

					}
				);

			}"
		);

		$infos['display'] = true;
		return json_encode($r);
	}

	/**
	* Sauvegarde le contenu des informations du filtre
	* @author Quentin JANON <qjanon@absystech.fr>
	* @return array
	*/
	public function saveFilter($infos) {
		if (array_key_exists("conditions",$infos["filtre_optima"]["options"]) && is_string($infos["filtre_optima"]["options"]["conditions"])) {
			$infos["filtre_optima"]["options"]["conditions"] = json_decode($infos["filtre_optima"]["options"]["conditions"] , true);
		}
		if (!$infos['event']) return false;
		$event = $infos['event'];
		$module = strtolower($infos['nommodule']);
		$this->infoCollapse($infos);
		if($infos['id_filtre_optima'])$infos['id_filtre_optima'] = str_replace("public_","",$infos['id_filtre_optima']);
		
		$infos['id_user'] = ATF::$usr->getId();
		$infos['id_module'] = ATF::module()->from_nom($module);
		$infos['options'] = serialize($infos['options']);
		parent::$event($infos);

		ATF::$cr->block('top');
		
		return $infos;
		
	}
	
	/** 
	* Supprime un filtre
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function removeFilter($infos){
		ATF::$cr->block('top');
		return $this->delete(str_replace("public_","",$infos['id_filtre_optima']));
	}
};
?>