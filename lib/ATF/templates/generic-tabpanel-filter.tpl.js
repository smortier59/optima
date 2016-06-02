{strip}
{if ATF::$usr->privilege($table,'filter_insert')}
Ext.getCmp('{$id_tab}').add(
		{ 
			xtype: 'compositefield'
			,hideLabel:true
			,msgTarget: 'under'
			,style:'padding:10px 10px 10px 16px'
			,items: [
			{ 
				xtype:'button'
				, text: '<img class="smallIcon insert" src="'+ATF.blank_png+'" />'
				, handler: function(b,e){
					/* b --> boutton
					 * e --> evenement 
					 */
										
					Nova.module = "{$table}";
					var table_assoc = {ATF::getClass($table)->listeModuleAssocie($table)};										
					$.ajax({
					  url:  "pager_filter.dialog",					 
					  data: 'nom_tab_parent={$id_tab_panel}&nom_tab={$id_tab}&onglet={$onglet}{if $parent_table}&parent_class={$parent_table}{/if}&table={$table}&table_assoc='+table_assoc,
					  type: "POST"
					}).done(function(data) {						
					 	$('#filtreCtn').html(data);
					 	ATF.importScript();				
					});
				}
			},{ xtype: 'displayfield', value: 'Créer un filtre', style: 'margin:5px' }]
		});
{/if}		
{$filters=ATF::filtre_optima()->getFilters($table)}
{if $filters}
	Ext.getCmp('{$id_tab}').add(
		{foreach from=$filters key=type item=filtres}			
			{if !$filtres@first},{/if}
			new Ext.form.FieldSet({
				title: '{ATF::$usr->trans($type)|escape:javascript}'
				,style: 'margin:5px'
				,items:[
					{foreach from=$filtres key=id_filtre item=filtre}						
						{if !$filtre@first},{/if}
						{
							xtype: 'compositefield'
							,hideLabel:true
							,msgTarget: 'under'
							,items: [
							{if ATF::$usr->privilege($table,'filter_insert')}		 
							{ 
								xtype:'button'
								, text: '<img class="smallIcon update" src="'+ATF.blank_png+'" />'
								, handler: function(b,e){
									Nova.module = "{$table}";
									Nova.id_filtre = "{$id_filtre}";
									var table_assoc = {ATF::getClass($table)->listeModuleAssocie($table)};									
									
									$.ajax({
									  url:  "pager_filter.dialog",					 
									  data: 'nom_tab_parent={$id_tab_panel}&nom_tab={$id_tab}&onglet={$onglet}{if $parent_table}&parent_class={$parent_table}{/if}&table={$table}&table_assoc='+table_assoc,
									  type: "POST"
									}).done(function(data) {						
									 	$('#filtreCtn').html(data);
									 	ATF.importScript();				
									});

								}
							}
							{/if}
							{if $type!="filtre_utilise"}
							{if ATF::$usr->privilege($table,'filter_insert')}
							,
							{/if}
							{ 
								xtype:'button'
								, text: '<img class="smallIcon valid" src="'+ATF.blank_png+'" />'
								, handler: function(b,e){
									ATF.loadMask.show();
									{$id_filtre=$id_filtre|replace:"public_":""}
									Ext.Ajax.request({
										url:"filtre_user,addFilterToPanel.ajax",
										method:"POST",
										params:{
											'filtre_user[id_filtre_optima]':'{$id_filtre}',
											'filtre_user[id_module]':'{ATF::module()->from_nom($table)}',
											'filtre_user[id_user]':'{ATF::$usr->getId()}',
											'table':'{$table}',

											't':'{$id_filtre}'
											,'v':1
											,'id':'{$pager}'
											,'pager':'{$pager}_{$id_filtre}'
											,'pager_parent':'{$pager}'
											,'id_filtre':'{$id_filtre}'
											,'name':'dynamicNewTab'
											,'search':'{$search}'
											{foreach from=$fk key=key item=item}
												,'fk[{$key}]':'{$item}'
										    {/foreach}
										},
									   success: function (response, opts) {
										   ATF.loadMask.hide();
										   var preselection=false;
										   {* si le panel existe déjà, pas besoin de le recréer, il suffit juste de le preselectionner 
											for(ess in tab.items.items){
												if(tab.items.items[ess].id=="{$pager}Filtre{$pager}_{$id_filtre}_{$id_filtre}"){
													tab.setActiveTab(parseInt(ess));
													preselection=true;
												}
											}*}
											{* création dans le cas contraire *}
											if(!preselection){
												eval(response.responseText);												
												dynamicNewTab.closable=true;
												dynamicNewTab.id=id_insert_filtre_user;
												dynamicNewTab.title='{ATF::filtre_optima()->select($id_filtre,filtre_optima)|escape:javascript}';
												var i =tab.add(dynamicNewTab);
												tab.setActiveTab(i);
											}
									   },
									   failure: function() {
											ATF.loadMask.hide();
											alert('erreur_inconnue_ajouter_tab'); 
									   }
									});	
								}
							}
							{/if}
							,{ xtype: 'displayfield', value: '{$filtre|escape:javascript}',style: 'margin:5px' }]
						}
					{/foreach}
				]
			})
		{/foreach}
	);
{/if}
Ext.getCmp('{$id_tab}').doLayout();

{/strip}