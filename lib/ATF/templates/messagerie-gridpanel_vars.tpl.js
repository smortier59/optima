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
	renderer:ATF.renderer.actions('{$current_class->table}','{$current_class->no_select}','{$current_class->no_update}'), 
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
		url: '{$current_class->table},extJSgsa.ajax{if $url_extra},{$url_extra}{/if}'
		,method:'POST'
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
				grid.view.scroller.css("overflow","auto");
				if($('#ptb_{$pager}'))var activePage=Math.ceil((Ext.getCmp('ptb_{$pager}').cursor + Ext.getCmp('ptb_{$pager}').pageSize) / Ext.getCmp('ptb_{$pager}').pageSize);
				else var activePage=""; 
				if((grid.height()-grid.getView().scroller.dom.scrollHeight)>50 && activePage!=ATF.currentpage["{$pager}"]){
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
						
			{* 
			- grid.getView().scroller.dom.scrollHeight : hauteur du contenu du grid
			- Ext.getCmp('{$id}').height() : taille du grid
			- +50 : décalage entre la valeur trouvée et la valeur declencheuse
			*}
			grid.addListener('bodyscroll',function (scrollLeft, scrollTop){
													
				var scrolloffset = (Ext.getCmp('{$id}').getView().scroller.dom.scrollHeight-Ext.getCmp('{$id}').height())+50;
				
				{*  - scrollTop >= scrolloffset : si le scroll est en bas
					- Ext.getCmp('{$id}').getView().scroller.dom.scrollHeight!=Ext.getCmp('{$id}').saveHeight : pour éviter les chargements de page après un premier chargement
					- Ext.getCmp('{$id}').getStore().getTotalCount()/30)!=ATF.currentpage["{$pager}"] : si le numero de page est bien différent du nombre total *}
				if ((scrollTop >= scrolloffset) && (Ext.getCmp('{$id}').getView().scroller.dom.scrollHeight!=Ext.getCmp('{$id}').saveHeight) && (Math.round(Ext.getCmp('{$id}').getStore().getTotalCount()/30)!=ATF.currentpage["{$pager}"])){
					var Mask = new Ext.LoadMask(grid.el, { msg:'{ATF::$usr->trans(loading_new_page)}' });
					Mask.show();
					Ext.getCmp('{$id}').saveHeight=Ext.getCmp('{$id}').getView().scroller.dom.scrollHeight;
										

					ATF.ajax("{$current_class->table},extJSgsa.ajax","pager={$pager}&start="+(ATF.currentpage["{$pager}"]*30)+"&limit=30",{ 
						onComplete:function(obj){
							ATF.currentpage["{$pager}"] = ATF.currentpage["{$pager}"] + 1;
							Ext.getCmp('ptb_{$pager}').noChange=true;
							Ext.getCmp('{$id}').getStore().loadData(obj,true);
							Mask.hide();
							ATF.ajax("messagerie,sync.ajax","",{ 
								onComplete:function(obj){
									syncBtn.setIcon('{ATF::$staticserver}images/icones/messagerie/sync.png');
									syncBtn.setText('Synchroniser');
								}
							});
						}
					});
				}
			});
			
			{* chargement des aggregats si présent dans le custom *}
			{if $id_filtre}{$filtr=$id_filtre}{else}{$filtr=0}{/if}
			{if ATF::$usr->custom[$current_class->table]["aggregats"][$filtr]}
				ATF.chargerAggregat("{$pager}",'{$id}',"{$current_class->table}");
			{/if}
		}
	}
});


{/strip}