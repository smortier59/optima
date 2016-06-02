{strip}ATF.speedInsertForm{$table} = new Ext.FormPanel({
	labelWidth: 75,
	renderTo:'divSpeedInsertForm{$table}',
	frame:true,
	bodyStyle:'padding:400 400 0',
	items: [
		{* Panel primaire *} 

		{include file="generic-panel.tpl.js" 
			colonnes=$current_class->colonnes.speed_insert 
			title="Informations principales" 
			requests=$requests 
			forceVisible=true
			event=speedInsert
			colspan=$current_class->panels.speed_insert.nbCols|default:2 
			panel_key=speed_insert}
		,	
		{
			xtype: 'fieldset',
			hidden: true,
			title:"Url de la fiche produit sur ICECAT",
			id: 'fieldSetForURL',
			items: [
				{
					xtype: 'label',
					id: 'urlToIcecat',
					html: ""
				}
			]
		},	
		{include file="generic-panel.tpl.js" 
			colonnes=$current_class->colonnes.speed_insert1 
			title="Informations secondaires" 
			requests=$requests 
			forceVisible=true
			event=speedInsert
			colspan=$current_class->panels.speed_insert.nbCols|default:2 
			panel_key=speed_insert1}
		
		
		{* Pour les fichiers *}
		{foreach from=$current_class->files key=key item=item}
			{if $item.obligatoire==1}
				{include file="champ_obligatoire.tpl.htm" assign="img_ob"}
			{/if}
			{if !$item.no_upload}
				,{
					xtype:'hidden'
					,name: '{$current_class->table}[filestoattach][{$key}]'
					,id: '{$current_class->table}[{$key}]'
					{if $event==update && ATF::getClass($current_class->table)->file_exists($identifiant,$key)}
					,value: 'true'
					{/if}
				},{
					xtype:'fieldset',
					title: '{ATF::$usr->trans($key,$current_class->table)} {$img_ob|escape:javascript}',
					collapsible:{if $item.collapsible===false}false{else}true{/if},
					collapsed: {if $item.collapsed==1}true{else}false{/if},
					animCollapse:false,
					autoHeight:true,
					autoWidth:true,
					id:'{$key}',
					defaults: { anchor: '95%' },
					items: {if ATF::$html->template_exists("`$current_class->table`-`$key`-upload_fichier.tpl.js")}
								{include file="`$current_class->table`-`$key`-upload_fichier.tpl.js" table=$current_class->table}									
							{else}
								{include file="generic-upload_fichier.tpl.js" table=$current_class->table}
							{/if}
				}
				{$img_ob=false}
			{/if}
		{/foreach}
		
	]
	,buttons: [
		{if ATF::$codename != "exactitude"}
		{
		text: 'Interroger ICECAT',
		handler: function(el){
			if (!Ext.getCmp('produit[ref]').getValue()) {
				Ext.Msg.show({
				   title:'Champ manquant',
				   msg: 'Vous devez renseigner une référence pour interroger ICECAT',
				   buttons: Ext.Msg.OK,
				   animEl: 'elId',
				   icon: Ext.MessageBox.WARNING
				});
				return false;
			}

			if (!Ext.getCmp('produit[id_fabriquant]').getValue()) {
				Ext.Msg.show({
				   title:'Champ manquant',
				   msg: 'Vous devez renseigner un fabriquant pour interroger ICECAT',
				   buttons: Ext.Msg.OK,
				   animEl: 'elId2',
				   icon: Ext.MessageBox.WARNING
				});
				return false;
			}

			ATF.loadMask = ATF.createLoadMask(el.findParentByType('form').el);
			ATF.loadMask.show();

			ATF.ajax(
				"produit,getInfosFromICECAT,ajax"
				,"ref="+Ext.getCmp('produit[ref]').getValue()+"&id_fabriquant="+Ext.getCmp('produit[id_fabriquant]').getValue()
				, {
					onComplete: function (r) {
						if (r.result) {

							if (r.result.url) {
								Ext.getCmp('fieldSetForURL').setVisible(true);
								Ext.getCmp('urlToIcecat').setText("URL : <a target='_blank' href='"+r.result.url+"'>"+r.result.url+"</a>",false);
							} else {
								Ext.getCmp('fieldSetForURL').setVisible(false);
								Ext.getCmp('urlToIcecat').setText("");
							}

							if (r.result.produit) {
								Ext.getCmp('produit[produit]').setValue(r.result.produit);
							} else {
								Ext.getCmp('produit[produit]').reset();
							}

							Ext.getCmp('label_produit[id_produit_typeecran]').removeClass('orange_border_textfield'); 
							if (r.result.typeecran) {
								if (r.result.typeecran.length>1) {
									Ext.getCmp('label_produit[id_produit_typeecran]').addClass('orange_border_textfield'); 
								} else {
									Ext.getCmp('produit[id_produit_typeecran]').setValue(r.result.typeecran[0].id_produit_typeecran);
									Ext.getCmp('label_produit[id_produit_typeecran]').setValue(r.result.typeecran[0].libelle);
								}
							} else {
								Ext.getCmp('produit[id_produit_typeecran]').reset();
								Ext.getCmp('label_produit[id_produit_typeecran]').reset();
							}

							Ext.getCmp('label_produit[id_produit_technique]').removeClass('orange_border_textfield'); 
							if (r.result.tailleecran) {
								if (r.result.tailleecran.length>1) {
									Ext.getCmp('label_produit[id_produit_viewable]').addClass('orange_border_textfield'); 
								} else {
									Ext.getCmp('produit[id_produit_viewable]').setValue(r.result.tailleecran[0].id_produit_tailleecran);
									Ext.getCmp('label_produit[id_produit_viewable]').setValue(r.result.tailleecran[0].libelle);
								}
							} else {
								Ext.getCmp('produit[id_produit_viewable]').reset();
								Ext.getCmp('label_produit[id_produit_viewable]').reset();
							}

							Ext.getCmp('label_produit[id_produit_technique]').removeClass('orange_border_textfield'); 
							if (r.result.tech_impression) {
								if (r.result.tech_impression.length>1) {
									Ext.getCmp('label_produit[id_produit_technique]').addClass('orange_border_textfield'); 
								} else {
									Ext.getCmp('produit[id_produit_technique]').setValue(r.result.tech_impression[0].id_produit_technique);
									Ext.getCmp('label_produit[id_produit_technique]').setValue(r.result.tech_impression[0].libelle);
								}
							} else {
								Ext.getCmp('produit[id_produit_technique]').reset();
								Ext.getCmp('label_produit[id_produit_technique]').reset();
							}

							Ext.getCmp('label_produit[id_produit_format]').removeClass('orange_border_textfield'); 
							if (r.result.format_impression) {
								if (r.result.format_impression.length>1) {
									Ext.getCmp('label_produit[id_produit_format]').addClass('orange_border_textfield'); 
								} else {
									Ext.getCmp('produit[id_produit_format]').setValue(r.result.format_impression[0].id_produit_format);
									Ext.getCmp('label_produit[id_produit_format]').setValue(r.result.format_impression[0].libelle);
								}
							} else {
								Ext.getCmp('produit[id_produit_format]').reset();
								Ext.getCmp('label_produit[id_produit_format]').reset();
							}

							Ext.getCmp('label_produit[id_produit_ram]').removeClass('orange_border_textfield'); 
							if (r.result.mem) {
								if (r.result.mem.length>1) {
									Ext.getCmp('label_produit[id_produit_ram]').addClass('orange_border_textfield'); 
								} else {
									Ext.getCmp('produit[id_produit_ram]').setValue(r.result.mem[0].id_produit_ram);
									Ext.getCmp('label_produit[id_produit_ram]').setValue(r.result.mem[0].libelle);
								}
							} else {
								Ext.getCmp('produit[id_produit_ram]').reset();
								Ext.getCmp('label_produit[id_produit_ram]').reset();
							}

							Ext.getCmp('label_produit[id_produit_lecteur]').removeClass('orange_border_textfield'); 
							if (r.result.lecteur) {
								if (r.result.lecteur.length>1) {
									Ext.getCmp('label_produit[id_produit_lecteur]').addClass('orange_border_textfield'); 
								} else {
									Ext.getCmp('produit[id_produit_lecteur]').setValue(r.result.lecteur[0].id_produit_lecteur);
									Ext.getCmp('label_produit[id_produit_lecteur]').setValue(r.result.lecteur[0].libelle);
								}
							} else {
								Ext.getCmp('produit[id_produit_lecteur]').reset();
								Ext.getCmp('label_produit[id_produit_lecteur]').reset();
							}

							Ext.getCmp('label_produit[id_produit_OS]').removeClass('orange_border_textfield'); 
							if (r.result.os) {
								if (r.result.os.length>1) {
									Ext.getCmp('label_produit[id_produit_OS]').addClass('orange_border_textfield'); 
								} else {
									Ext.getCmp('produit[id_produit_OS]').setValue(r.result.os[0].id_produit_OS);
									Ext.getCmp('label_produit[id_produit_OS]').setValue(r.result.os[0].libelle);
								}
							} else {
								Ext.getCmp('produit[id_produit_OS]').reset();
								Ext.getCmp('label_produit[id_produit_OS]').reset();
							}

							Ext.getCmp('label_produit[id_produit_puissance]').removeClass('orange_border_textfield'); 
							if (r.result.proc_puissance) {
								if (r.result.proc_puissance.length>1) {
									Ext.getCmp('label_produit[id_produit_puissance]').addClass('orange_border_textfield'); 
								} else {
									Ext.getCmp('produit[id_produit_puissance]').setValue(r.result.proc_puissance[0].id_produit_puissance);
									Ext.getCmp('label_produit[id_produit_puissance]').setValue(r.result.proc_puissance[0].libelle);
								}
							} else {
								Ext.getCmp('produit[id_produit_puissance]').reset();
								Ext.getCmp('label_produit[id_produit_puissance]').reset();
							}

							Ext.getCmp('label_produit[id_produit_lan]').removeClass('orange_border_textfield'); 
							if (r.result.reseau) {
								if (r.result.reseau.length>1) {
									Ext.getCmp('label_produit[id_produit_lan]').addClass('orange_border_textfield'); 
								} else {
									Ext.getCmp('produit[id_produit_lan]').setValue(r.result.reseau[0].id_produit_lan);
									Ext.getCmp('label_produit[id_produit_lan]').setValue(r.result.reseau[0].libelle);
								}
							} else {
								Ext.getCmp('produit[id_produit_lan]').reset();
								Ext.getCmp('label_produit[id_produit_lan]').reset();
							}

							Ext.getCmp('label_produit[id_processeur]').removeClass('orange_border_textfield'); 
							if (r.result.proc_modele) {

								if (r.result.proc_modele.length>1) {
									Ext.getCmp('label_produit[id_processeur]').addClass('orange_border_textfield'); 
								} else {
									Ext.getCmp('produit[id_processeur]').setValue(r.result.proc_modele[0].id_processeur);
									Ext.getCmp('label_produit[id_processeur]').setValue(r.result.proc_modele[0].libelle);
								}
							} else {
								Ext.getCmp('produit[id_processeur]').reset();
								Ext.getCmp('label_produit[id_processeur]').reset();
							}

							Ext.getCmp('label_produit[id_produit_dd]').removeClass('orange_border_textfield'); 
							if (r.result.dd) {
								if (r.result.dd.length>1) {
									Ext.getCmp('label_produit[id_produit_dd]').addClass('orange_border_textfield'); 
								} else {
									Ext.getCmp('produit[id_produit_dd]').setValue(r.result.dd[0].id_produit_dd);
									Ext.getCmp('label_produit[id_produit_dd]').setValue(r.result.dd[0].libelle);
								}
							} else {
								Ext.getCmp('produit[id_produit_dd]').reset();
								Ext.getCmp('label_produit[id_produit_dd]').reset();
							}
						} else if (r.error) {
							ATF.errors = r.error;
							ATF.showError();
						}
						ATF.loadMask.hide();
					}
				}
			);
		}
	},{/if}
	{
		text: 'Insérer un nouvel enregistrement',
		handler: function(){
			ATF.speedInsertForm{$table}.getForm().submit({
				method  : 'post',
				waitMsg : 'Insertion de l\'élément en cours...',
				waitTitle : 'Chargement',
				url     : 'extjs.ajax',
				params: {
					'extAction':'{$current_class->table}'
					,'extMethod':'speed_insert'
					,
				}
				{if ATF::$codename != "exactitude" }
					{if ATF::$html->template_exists("`$parent_class`-`$current_class->table`-speed_insert_success.tpl.js")}
						{include file="`$parent_class`-`$current_class->table`-speed_insert_success.tpl.js"}
					{else}
						,success:function(form, action) {
							if(action.result.result){
								Ext.ComponentMgr.get('{$id}').setValue(action.result.result.id);
								Ext.ComponentMgr.get('label_{$id}').setValue(action.result.result.nom);
								Ext.ComponentMgr.get('speed_insert{$id}').close();
							}else{
								ATF.extRefresh(action); 
							}
						}
					{/if}
				{else}
					{if $current_class->table == "facture"}
						{include file="facture_ligne-prod-speed_insert_success.tpl.js"}				
					{else}
						{if ATF::$html->template_exists("`$parent_class`-`$current_class->table`-speed_insert_success.tpl.js")}
							{include file="`$parent_class`-`$current_class->table`-speed_insert_success.tpl.js"}
						{else}
							,success:function(form, action) {
								if(action.result.result){
									Ext.ComponentMgr.get('{$id}').setValue(action.result.result.id);
									Ext.ComponentMgr.get('label_{$id}').setValue(action.result.result.nom);
									Ext.ComponentMgr.get('speed_insert{$id}').close();
								}else{
									ATF.extRefresh(action); 
								}
							}
						{/if}
					{/if}
				{/if}
				,failure:function(form, action) {
					ATF.extRefresh(action); 
				}
				,timeout:3600
			});
		}
	}
	{if ATF::$html->template_exists("`$current_class->table`-files.tpl.js")}
			{include file="`$current_class->table`-files.tpl.js"}
	{else}
		{foreach from=$current_class->files key=key item=item}
			{if $item.preview}
				,{
					text: 'Prévisualiser {ATF::$usr->trans($key)}',
					handler: function(){
						{if $event==update && ATF::getClass($current_class->table)->file_exists($identifiant,$key) && !$item.force_generate}
							ATF.speedInsertForm{$table}.getForm().submit({
								method  : 'post',
								waitMsg : 'Génération du PDF...',
								waitTitle : 'Chargement',
								url     : 'extjs.ajax',
								params: {
									'{$current_class->table}[id_{$current_class->table}]':'{$identifiant}'
									,'extAction':'{$current_class->table}'
									,'extMethod':"{if $event=='cloner'}cloner{else}update{/if}"
									,'preview':'true'
								}
								,success:function(form, action) {
									if(action.result.result){
										window.location='{$current_class->table}-select-{$key}-'+action.result.result+'.temp'; 
									}else if(action.result.cadre_refreshed){
										ATF.ajax_refresh(action.result,true);
									}else {
										ATF.extRefresh(action); 
									}
								}
								,failure:function(form, action) {
									ATF.extRefresh(action); 
								}
								,timeout:3600
							});
						{else}
							ATF.speedInsertForm{$table}.getForm().submit({
								method  : 'post',
								waitMsg : 'Génération du PDF...',
								waitTitle : 'Chargement',
								url     : 'extjs.ajax',
								params: {
									'extAction':'{$current_class->table}'
									,'extMethod':'insert'
									,'preview':'true'
								}
								,success:function(form, action) {
									if(action.result.result){
										window.location='{$current_class->table}-select-{$key}-'+action.result.result+'.temp'; 
									}else if(action.result.cadre_refreshed){
										ATF.ajax_refresh(action.result,true);
									}else{
										ATF.extRefresh(action); 
									}
								}
								,failure:function(form, action) {
									ATF.extRefresh(action); 
								}
								,timeout:3600
							});
						{/if}
					}
				}
			{/if}
		{/foreach}
	{/if}
	]
});
{/strip}
