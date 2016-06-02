{strip} 
{*
@param string $name Nom de objet javascript
@param string $current_class Classe courante
@param string $pager Identifiant du pager
@param querier $q
@param string $renderTo Div où placer le grid
@param string $title Titre du panel du grid
@param string $function Fonction impliquée
@param array $cols Colonnes à afficher
@param boolean $search Afficher la recherche ou non
@param int $height Hauteur du grid
@param boolean $closable Provenant d un tab, et peut etre fermable
*} 
{if $id}
	var id_insert_filtre_user="{$id}";
{/if}
{if !$pager}
	{$pager=$current_class->genericSelectAllDivName($div,$parent_class)}
{/if}
{if !$id}
	{$id=$pager}
{*elseif $id_filtre}
	{$id="`$id`Filtre`$pager`_`$id_filtre`"*}
{/if}

{if !$q}
	{$q=ATF::_s(pager)->getAndPrepare($pager,null,true)}
	{$vue=ATF::vue()->recupOrdre($current_class->table)}
	{if $vue}
		{$q->setView($vue,true)->end()}
		{if $vue.tri}
			{$q->addOrder($vue.tri.champs,$vue.tri.ordre)->end()}
		{/if}
	{else}
		{$q->reset('field,view,order')->end()}
	{/if}
	{$current_class->genericSelectAllView($q,true)}
	{*capture assign=logger}[{md5(ATF::$id_thread)}] {$current_class->table} generic-gridpanel.tpl.js debut  = {$pager} | fk={count($q->getFk())} where{count($q->getWhere())}{/capture}
	{log::logger($logger,ygautheron,true)*}
	{$url_extra=$current_class->genericSelectAllFK($q,$fk)}
	{*capture assign=logger}[{md5(ATF::$id_thread)}] {$current_class->table} generic-gridpanel.tpl.js fin = {$pager} | fk={count($q->getFk())} where{count($q->getWhere())}{/capture}
	{log::logger($logger,ygautheron,true)*}
{/if}

{if ATF::$html->template_exists("{$current_class->table}-gridpanel_vars.tpl.js")}
	{include file="{$current_class->table}-gridpanel_vars.tpl.js"}
{else}
	{include file="generic-gridpanel_vars.tpl.js"}
{/if}

/* Recherche */
{if $search}
	if (!searchBox) {
		var searchBox=Array();
	}
	searchBox["{$pager}"] = new Object();
	searchBox['{$pager}']=new Ext.ux.form.SearchField({
		store: store['{$pager}'],
		value:'{$q->getSearch()|escape:javascript}',
		width:200
	});	
{/if}

{if !$noFilter}

{/if}
/* Bouton de suppression */
var deleteHandler = function() {
	var grid = Ext.getCmp('{$id}');
	var records = grid.getSelectionModel().getSelections();

	{* dans le cas où on sélectionne un aggregat, il ne faut pas supprimer *}
	if((records.length==1 && records[0].json) || records.length>1){
		Ext.Msg.show({ 
			title:ATF.usr.trans('Etes_vous_sur'),
			msg:ATF.usr.trans('supprimer_les_enregistrements_selectionnes'),
			buttons:Ext.Msg.YESNO,
			fn:function (buttonId,text,opt) {
				switch (buttonId) {
					case "no":
						break;
						
					case "yes":	
						var ids = Array();	
						for (var r=0;r<records.length;r++) {	
							if(records[r].json){
								ids.push(records[r].data["{$current_class->table}__dot__id_{$current_class->table}"]);
							}
						}
						if(ids[0]){
							Ext.Ajax.request({
								url: '{$current_class->table},deleteCascade.ajax',
								success:function(action) {
										if($.parseJSON(action.responseText).error.length<1 && $.parseJSON(action.responseText).result!==false){
											Ext.Msg.show({ 
												title:ATF.usr.trans('suppression_cascade'),
												msg:$.parseJSON(action.responseText).result,
												buttons:Ext.Msg.YESNO,
												fn:function (buttonId,text,opt) {
													switch (buttonId) {
														case "no":
															break;
															
														case "yes":	
															var ids = Array();	
															for (var r=0;r<records.length;r++) {	
																if(records[r].json){
																	ids.push(records[r].data["{$current_class->table}__dot__id_{$current_class->table}"]);
																}
															}
															if(ids[0]){
																Ext.Ajax.request({
																	url: '{$current_class->table},delete.ajax',
																	success:function(action) {
																		ATF.extRefresh({ response:action });
																		if ($.parseJSON(action.responseText).result) {
																			grid.store.remove(records);
																		}
																		grid.getStore().load({ params:{ start:0, limit:30 }});
																	},
																	params: { 
																		"id[]": ids
																	}
																});
															}
															break;
													}
												},
												animEl:grid,
												closable:false,
												icon:Ext.MessageBox.WARNING
											});					
										}
									
									ATF.extRefresh({ response:action });
									if ($.parseJSON(action.responseText).result) {
										grid.store.remove(records);
									}
									grid.getStore().load({ params:{ start:0, limit:30 }});
								},
								params: { 
									"id[]": ids
								}
							});
						}
						break;
				}
			},
			animEl:grid,
			closable:false,
			icon:Ext.MessageBox.QUESTION
		});					
	}
};

/* Echelle de date */
{$between_date=$q->getBetweenDate()}
{$recup_date=$current_class->recupColDate()}
/*store.setDefaultSort('lastpost', 'desc');*/
var {$name} = new Ext.grid.EditorGridPanel({
	{if $autoHeight}
		autoHeight: true,
	{elseif $height}
		height:{$height},
	{else}
		autoHeight: true,
	{/if}
	title:'{$title|escape:javascript}',
	stripeRows: true,
	store: store['{$pager}'],
	{if $vue['tronque']}
		cls:'gridPanelNoWrapRow',
	{/if}
	selModel: new Ext.grid.RowSelectionModel(),
	{if $id}id:'{$id}',{/if}
	trackMouseOver:true,
	disableSelection:true,
	loadMask: true,
	{if $closable}closable:true,{/if}
	{if $region}region:'{$region}',{/if}
	{if $split}split:true,{/if}
	enableHdMenu: false,
	listeners:{
		close:function() {
			{*Ext.Ajax.request({
				url:"{$current_class->table},saveFilterTab.ajax",
				method:"POST",
				params:{
					't':'{$id_filtre}',
					'v':'',
					'div':'{$pager}'
				}
			});	*}
			Ext.Ajax.request({
				url:"filtre_user,saveFilterTab.ajax",
				method:"POST",
				params:{
					't':'{$id}',
					'v':'',
					'div':'{$pager}'
				}
			});
			
		}
		,render: function(grid){
			ATF.modifColsEvent("{$current_class->table}",grid,cols["{$pager}"]);	
		}
	},
	columns: cols["{$pager}"],
	viewConfig: function (o){
			{* utile pour la partie scroll des grids (retenir la position du scroll après reload) *}
			o.onLoad=Ext.emptyFn;
			if (typeof(o.listeners)=='undefined') {
				o.listeners = {};
			}
			o.listeners.beforerefresh=function(v) {
				v.scrollTop = v.scroller.dom.scrollTop;
			};
			o.listeners.refresh=function(v) {
				v.scroller.dom.scrollTop = v.scrollTop;
			};
			return o;
		} ({if ATF::$html->template_exists("{$current_class->table}-gridpanel-viewConfig-{$pager}.tpl.js")}
				{include file="{$current_class->table}-gridpanel-viewConfig-{$pager}.tpl.js" bodyCols=util::getExtJSGridRowBody($q->getView())}
			{elseif ATF::$html->template_exists("{$current_class->table}-gridpanel-viewConfig.tpl.js")}
				{include file="{$current_class->table}-gridpanel-viewConfig.tpl.js" bodyCols=util::getExtJSGridRowBody($q->getView())}
			{else}
				{include file="generic-gridpanel-viewConfig.tpl.js" bodyCols=util::getExtJSGridRowBody($q->getView())}
			{/if})
	,
	tbar: [ 
		insertNow ? new Ext.Button({
			text: "{ATF::$usr->trans(ajouter)}",
			icon:'{ATF::$staticserver}images/icones/insert.png',
			handler: insertNow
			{if !ATF::$usr->privilege($current_class->name(),insert) || $current_class->no_insert || !$current_class->is_active(insert)}
				,disabled : true
			{/if}	
		}) : { }
		,'-', 
		new Ext.PagingToolbar({
			id:'ptb_{$pager}',
			pageSize: 30,
			store: store['{$pager}'],
			displayInfo: true,
			emptyMsg: "{ATF::$usr->trans(aucun)}",
			listeners:{
				change:function(a,b){
					if(Ext.getCmp('ptb_{$pager}').noChange==true){
						Ext.getCmp('ptb_{$pager}').noChange=false;
					}else{
						ATF.currentpage["{$pager}"]=b.activePage;
					}
				}
			}
		})
		{if $recup_date}
			,'-',
			{
				text: '{ATF::$usr->trans(afficher_date)}',
				icon:'{ATF::$staticserver}images/icones/calendar.png',
				id:'bouton_between{$id}',
				handler: function(button){
					Ext.getCmp("tb_between_date{$id}").show();
					Ext.getCmp("tb_between_date{$id}").setHeight(35);
					button.setDisabled(true);
				}
				{if $between_date || ATF::$usr->custom["user"]["show_data_day"]=='oui'}
					,disabled:true
				{/if}
			}
		{/if}
		,'-',
		{if $search}
			'Recherche: ', ' ',
			searchBox['{$pager}'],
		{/if}
		{if util::getExtJSGridRowBody($q->getView())}
			'-', 
			{
				pressed: true,
				enableToggle:true,
				text: "{ATF::$usr->trans(recherche)}",
				cls: 'x-btn-text-icon details',
				toggleHandler: function(btn, pressed){
					var view = Ext.getCmp('{$id}').getView();
					view.showPreview = pressed;
					view.refresh();
				}
			},
		{/if}
		{if $name}
			{if ATF::$usr->privilege($current_class->name(),'view')}
			'-',{
				text: '{ATF::$usr->trans(vue,top)}',
				cls: 'x-btn-text-icon details',
				icon: '{ATF::$staticserver}images/icones/columns.png',
				menu: new Ext.menu.Menu({ 
					id:'menu_vue_{$pager}'
					,listeners:{
						render:function(){
							{* génération du menu de vue *}
							Ext.Ajax.request({
								url: '{$current_class->name()},genereMenuVue.ajax',
								method:"POST",
								params:{
								   'pager':'{$pager}',
								   'table':'{$current_class->table}',
								   'id_filtre':'{$id_filtre}',
								   'pager_parent':'{$pager_parent}',
								   'id':'{$id}',
								   'function':'{$function}',
								   'name':'{$name}'
								},
								success: function (response, opts) {
								   eval(response.responseText);
								}
							});
						}
					}
				})
			},
			{/if}
			{if ATF::$usr->privilege($current_class->name(),'export')}
			'-', {
				text: '{ATF::$usr->trans(exporter)}',
				cls: 'x-btn-text-icon details',
				icon: '{ATF::$staticserver}images/module/16/exporter.png',
				menu: new Ext.menu.Menu({ items:[
					{ 
						text: 'Ces colonnes'
						, handler: function(b,e){
							window.location='{$current_class->name()},export_vue.ajax,onglet={$pager}';
						}
					},{ 
						text: 'Toutes les colonnes'
						, handler: function(b,e){
							window.location='{$current_class->name()},export_total.ajax,onglet={$pager}';
						}
					}
					{if ATF::$html->template_exists("{$current_class->table}-export_header.tpl.js")}
						,{include file="{$current_class->table}-export_header.tpl.js"}
					{/if}
				]})
			},
			{/if}
			{if ATF::$usr->privilege($current_class->name(),'filter_select')}
			'-', {
				text: '{ATF::$usr->trans(filter_select,privilege)}',
				id:"menuFiltre_{$id}",
				iconCls: 'iconFiltre',
				xtype:'button',
				menu: new Ext.ux.menu.StoreMenu({
					id:'filterMenu',
					url:'filtre_optima,NEWgetFilters.ajax',
					baseParams: {
						table: "{$current_class->table}",
						gridId: "{$id}"
					},
					listeners: {
						beforeshow: function() {
							/*if (Ext.getCmp("forcer").pressed) {
								this.loaded=false;
							}*/
						}
					}
				})/*
				,listeners : {
				    beforerender: function(button,event) {
				    	ATF.log(button);
				        button.setText('Hide');
				    }
				}*/
			},
			{/if}
			{if ATF::$html->template_exists("{$current_class->table}-select_all_header.tpl.js")}
				'-',
				{include file="{$current_class->table}-select_all_header.tpl.js"},
			{/if}
		{/if}
		'->'
		,new Ext.Button({
			text: '{ATF::$usr->trans(supprimer)}',
			icon:'{ATF::$staticserver}images/icones/delete.png',
			handler: deleteHandler
			{if !ATF::$usr->privilege($current_class->name(),delete) || $current_class->no_delete || !$current_class->is_active(delete)}
				,disabled : true
			{/if}
		})
	]
	{if ATF::$html->template_exists("{$current_class->table}-gridpanel-bbar.tpl.js")}
		{include file="{$current_class->table}-gridpanel-bbar.tpl.js"}	
	{/if}
});
store['{$pager}'].searchBox = searchBox['{$pager}'];

{* Chargement différé, uniquement qd on clique sur l onglet *}
{$name}.on('render', function(el){
	Ext.getCmp('{$id}').store.reload({ params:{ start:0, limit:30 } }); 

	{if $recup_date}
		var tb2 = new Ext.Toolbar({
			renderTo: Ext.getCmp('{$id}').tbar,
			autoWidth: true,
			id:'tb_between_date{$id}',
			layout: 'fit',
			hideMode:'visibility',
			{if !$between_date}
				{if ATF::$usr->custom["user"]["show_data_day"]=='oui'}	
					listeners:{
						afterrender:function(tb){
							Ext.getCmp('{$id}').store.baseParams['between_begin'] = $('#between_begin{$id}').val();
							Ext.getCmp('{$id}').store.baseParams['between_end'] = $('#between_end{$id}').val();
							Ext.getCmp('{$id}').store.baseParams['champs_date'] = Ext.getCmp('champs_date{$id}').val();
							delete Ext.getCmp('{$id}').store.baseParams['sup_between_date'];
							Ext.getCmp('{$id}').store.reload({ params:{ start: 0 } });
						}
					},
				{else}
					listeners:{
						render:function(tb){
							tb.hide();	
							tb.setHeight(0);
							Ext.getCmp("bouton_between{$id}").setDisabled(false);
						}
					},
				{/if}	
			{/if}
			items: [
				new Ext.FormPanel({
					autoHeight:true,
					bodyStyle:'padding:5px 5px 0',
					items: [{ 
						xtype:"compositefield"
						,hideLabel:true
						,items:[{
							xtype:'displayfield'	
							,value:'Filtrer les données sur le champ : '
						},{
							xtype:'combo'
							,triggerAction:'all'
							,editable:false
							,allowBlank:false
							,mode:'local'
							,width:150
							,id:'champs_date{$id}'
							,store: new Ext.data.ArrayStore({
								fields: [
									'myId',
									'displayText'
								],			
								data: [
									{foreach from=$recup_date item=i}
										['{$i.index}', '{$i.header|escape:javascript}']
										{if !$i@last},{/if}
									{/foreach}
								]
							})
							{if $between_date.champs_date}
								,value: "{util::extJSEscapeDot($between_date.champs_date)}"
							{else}
								,listeners:{
									render:function(obj){
										obj.setValue(obj.store.data.items[0].data.myId);
									}
								}
							{/if}
							,valueField: 'myId'
							,displayField: 'displayText'
						},{
							xtype:'displayfield'	
							,value:' entre le '
						},{
							xtype:"datefield"
							,id:'between_begin{$id}'
							,value:'{($between_date.debut|default:$smarty.now)|date_format:"%d-%m-%Y"}'
							,format:'d-m-Y'
							,width:110
						},{
							xtype:'displayfield'	
							,value:' et le '
						},{
							xtype:"datefield"
							,id:'between_end{$id}'
							,value:'{($between_date.fin|default:$smarty.now)|date_format:"%d-%m-%Y"}'
							,format:'d-m-Y'
							,width:110
						},{
							xtype:"button",
							text: '{ATF::$usr->trans(appliquer)}',
							cls: 'x-btn-text-icon details',
							icon: '{ATF::$staticserver}images/icones/valid.png',
							handler: function(){
								Ext.getCmp('{$id}').store.baseParams['between_begin'] = Ext.getCmp('between_begin{$id}').value;
								Ext.getCmp('{$id}').store.baseParams['between_end'] = Ext.getCmp('between_end{$id}').value;
								Ext.getCmp('{$id}').store.baseParams['champs_date'] = Ext.getCmp('champs_date{$id}').value;
								delete Ext.getCmp('{$id}').store.baseParams['sup_between_date'];
								Ext.getCmp('{$id}').store.reload({ params:{ start: 0 } });
							}
						},{
							xtype:"button",
							text: '{ATF::$usr->trans(cancel)}',
							cls: 'x-btn-text-icon details',
							icon: '{ATF::$staticserver}images/icones/delete.png',
							handler: function(){
								Ext.getCmp('{$id}').store.baseParams['sup_between_date'] = true;
								Ext.getCmp('{$id}').store.reload({ params:{ start: 0 } });
								Ext.getCmp("tb_between_date{$id}").hide();	
								Ext.getCmp("tb_between_date{$id}").setHeight(0);
								Ext.getCmp("bouton_between{$id}").setDisabled(false);
							}
						}]
					}]
				})
			]
		});
	{/if}
});

{if $renderTo}
	{$name}.render('{$renderTo}');
{/if}

{if $fromTab}
	{$fromTab}.add({$name});
{/if}
{/strip}