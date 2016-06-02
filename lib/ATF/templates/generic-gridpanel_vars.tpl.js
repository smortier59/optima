{strip}
{$fieldsKeys=[]}
{if ATF::$html->template_exists("`$current_class->name()`-gridpanel_init.tpl.js")}
	{include file="`$current_class->name()`-gridpanel_init.tpl.js"}
{/if}
{$current_class->prepareView($q,$function)}
{$fieldsKeys[]="`$current_class->table`.id_`$current_class->table`"}

if (!cols) {
	var cols=Array();
} 

cols["{$pager}"] = new Object();

cols["{$pager}"] = [{if $noUpdate!==false}{ 
	dataIndex:'updateBtn', 
	renderer:ATF.renderer.actions('{$current_class->table}','{$current_class->no_select}','{$current_class->no_update}','{$current_class->selectExtjs}'), 
	width:45, 
	resizable:false, 
	hideable:false, 
	menuDisabled:true, 
	sortable:false, 
	fixed:true 
}{/if}];

cols["{$pager}"] = cols["{$pager}"].concat({util::getExtJSGridMappingColumns($q->getView(),$current_class)});
{foreach from=$fieldsKeys item=i}
	cols["{$pager}"].push({ dataIndex:"{$i}", hidden:true, hideable:false });
{/foreach}

ATF.currentpage["{$pager}"] = 1;

if (!store) {
	var store=Array();
}
store["{$pager}"] = new Object();
store["{$pager}"] = new Ext.data.JsonStore({
	root: 'result',
	totalProperty: 'totalCount',
	idProperty: 'id',
	remoteSort: true,	
	fields: ATF.extParseFields({util::getExtJSGridMappingFields($q->getView(),$fieldsKeys)}),
	baseParams:{
		'pager':'{$pager}'
		,'pager_parent':'{$pager_parent}'
		{if $id_filtre}, 'filter_key':'{$id_filtre}'{/if}	
		{if $function}, 'function':'{$function}'{/if}
	},
	proxy: new Ext.data.HttpProxy({
		url: '{$table|default:$current_class->table},extJSgsa.ajax{if $url_extra},{$url_extra}{/if}'
		,method:'POST'
		,listeners:{
			exception:function(obj,typ,act,opt,resp,arg){
				ATF.extRefresh({ response:resp });
			}
		}
	})
	,listeners:{
		load:function(){
			var grid = Ext.getCmp('{$id}');

			{* Gestion de la scrollbar *}
			if(grid.store.totalLength>30){
				grid.autoHeight=false;
				if (!grid.height) {
					grid.setHeight(Ext.getBody().getViewSize().height-200);
				}
				Ext.get(grid.view.scroller.id).setStyle("overflow","auto");
				if($('#ptb_{$pager}'))var activePage=Math.ceil((Ext.getCmp('ptb_{$pager}').cursor + Ext.getCmp('ptb_{$pager}').pageSize) / Ext.getCmp('ptb_{$pager}').pageSize);
				else var activePage=""; 
				if((grid.getHeight()-grid.getView().scroller.dom.scrollHeight)>50 && activePage!=ATF.currentpage["{$pager}"]){
					ATF.currentpage["{$pager}"] = ATF.currentpage["{$pager}"] + 1;
					ATF.ajax("{$current_class->table},extJSgsa.ajax","pager={$pager}&start="+(ATF.currentpage["{$pager}"]*30)+"&limit=30",{ 
					onComplete:function(obj){
						Ext.getCmp('{$id}').getStore().loadData(obj,true);
					}});
				}
			}
			
			if (typeof(Ext.getCmp("{$pager}TabPanel"))!='undefined') {
				grid.setWidth(Ext.getCmp("{$pager}TabPanel").getWidth()-1);
			}
			
			{* Gestion de l affichage si pas de donnee *}
			if(grid.store.totalLength<1 && !grid.store.baseParams.query && !grid.store.searchBox.initialConfig.value && (Ext.getCmp('champs_date{$id}') && !Ext.getCmp('champs_date{$id}').value)){
				if (typeof(grid.getTopToolbar())!='undefined') {
					grid.getTopToolbar().hide();
				}
				grid.colModel.setConfig({});
				var reg=new RegExp("\\\[(.*)\\\]","g");
				grid.setTitle(grid.title.replace(reg,""));
				grid.setTitle(grid.title+" [0]");
				{* obligé de faire un destroy plutot que de faire une condition avec !
					, car dans un cas : création hotline, puis interaction, le bouton apparait pas car il detecte un element, alors qu il n y en a pas *}
				{if ATF::$usr->privilege($current_class->name(),insert) && !$current_class->no_insert && $current_class->is_active(insert)}
					if(Ext.getCmp('{$current_class->name()}Insert')){
						Ext.getCmp('{$current_class->name()}Insert').destroy();
					}
					if (typeof(Ext.getCmp("{$pager}TabPanel"))!='undefined') {
						Ext.getCmp("{$pager}TabPanel").add({
							xtype:'panel'
							,autoHeight:true
							,iconCls:'iconInsert' 
							,tabCls: 'right-tab'
							,id:'{$current_class->name()}Insert'
							,title:'{ATF::$usr->trans(ajouter)}'
							,listeners:{
								added: function(a,c){
									c.strip.setWidth(c.stripWrap.width() - 25);
								},
								beforeshow:function(tabinsert){
									if(!Ext.getCmp("{$pager}insertion")){
										Ext.Ajax.request({
										   url: '{$current_class->name()},getUpdateForm.ajax',
										   params:{
											   event:'insert',
											   table:'{$current_class->table}',
											   formName:'formulaire'
											   {if $url_extra},'{$url_extra|replace:"=":"':'"|replace:"&":"',"}'{/if} {* L"avoir plutôt en POST *}
										   },
										   success: function (response, opts) {
												eval(response.responseText);
												formulaire.closable=true;
												if (!formulaire.listeners) {
													formulaire.listeners={};
												}
												formulaire.listeners.close=function(){
													Ext.getCmp("{$pager}TabPanel").setActiveTab(0);
												};
												formulaire.id = "{$pager}insertion";
												ATF.basicInfo = new Ext.FormPanel(formulaire);
												tabinsert.insert(0,ATF.basicInfo);
												tabinsert.doLayout();
										   }
										});
									}
								}
							}
						});
					}
				{/if}
			}else{
				{* ne marche pas avec parenthese => '\(' est interprete comme '(' d expression et non comme un caractère à rechercher *}
				if(!isNaN(grid.store.totalLength)){
					var reg=new RegExp("\\\[(.*)\\\]","g");
					grid.setTitle(grid.title.replace(reg,""));
					if(grid.getStore().getCount()<=30){
						grid.setTitle(grid.title+" ["+grid.getStore().getTotalCount()+"]");
					}else{
						ATF.ajax("{$current_class->table},isAggregateActive.ajax","id_filtre={$id_filtre}",{ onComplete:function(obj){ 
							if(obj.result){
								if((grid.getStore().getCount()-4)==(grid.getStore().getTotalCount()-4)){
									grid.setTitle(grid.title+" ["+(grid.getStore().getCount()-4)+"/"+(grid.getStore().getTotalCount()-4)+"]");
								}else{
									grid.setTitle(grid.title+" ["+(grid.getStore().getCount()-4)+"/"+grid.getStore().getTotalCount()+"]");
								}
							}else{
								grid.setTitle(grid.title+" ["+grid.getStore().getCount()+"/"+grid.getStore().getTotalCount()+"]");
							}
						}});
					}
				}
			}
			
			{* 
			- grid.getView().scroller.dom.scrollHeight : hauteur du contenu du grid
			- Ext.getCmp('{$id}').height() : taille du grid
			- +50 : décalage entre la valeur trouvée et la valeur declencheuse
			*}
			grid.addListener('bodyscroll',function (scrollLeft, scrollTop){
				
				if (typeof(Ext.getCmp('ptb_{$pager}'))!='undefined') {
					var scrolloffset = (Ext.getCmp('{$id}').getView().scroller.dom.scrollHeight-Ext.getCmp('{$id}').getHeight())+50;
				}else{
					var scrolloffset = (Ext.getCmp('{$id}').getView().scroller.dom.scrollHeight-Ext.getCmp('{$id}').getHeight())+24;
				}
				
				{*  - scrollTop >= scrolloffset : si le scroll est en bas
					- Ext.getCmp('{$id}').getView().scroller.dom.scrollHeight!=Ext.getCmp('{$id}').saveHeight : pour éviter les chargements de page après un premier chargement
					- Ext.getCmp('{$id}').getStore().getTotalCount()/30)!=ATF.currentpage["{$pager}"] : si le numero de page est bien différent du nombre total *}
				if ((scrollTop >= scrolloffset) && (Ext.getCmp('{$id}').getView().scroller.dom.scrollHeight!=Ext.getCmp('{$id}').saveHeight) && (Math.ceil(Ext.getCmp('{$id}').getStore().getTotalCount()/30)!=ATF.currentpage["{$pager}"])){
					var Mask = new Ext.LoadMask(grid.el, { msg:'{ATF::$usr->trans(loading_new_page)}' });
					Mask.show();
					Ext.getCmp('{$id}').saveHeight=Ext.getCmp('{$id}').getView().scroller.dom.scrollHeight;
										
					ATF.ajax("{$current_class->table},extJSgsa.ajax","pager={$pager}&start="+(ATF.currentpage["{$pager}"]*30)+"&limit=30&function={$function}",{ 
						onComplete:function(obj){
							ATF.currentpage["{$pager}"] = ATF.currentpage["{$pager}"] + 1;
							if (typeof(Ext.getCmp('ptb_{$pager}'))!='undefined') {
								Ext.getCmp('ptb_{$pager}').noChange=true;
							}
							Ext.getCmp('{$id}').getStore().loadData(obj,true);
							Mask.hide();
						}
					});
				}
			});

			{* chargement des aggregats si présent dans le custom *}
			ATF.ajax("{$current_class->table},isAggregateActive.ajax","id_filtre={$id_filtre}",{ onComplete:function(obj){ 
				if(obj.result){
					ATF.chargerAggregat("{$pager}",'{$id}',"{$current_class->table}");
				}
			}});
		}
	}
});


{/strip}