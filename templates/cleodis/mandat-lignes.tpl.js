{*
$current_class
$proxyUrl
*}
ATF.buildGridEditor({
	title:'{$title}',
	id:'{$id}',
	height: 300,
	autoExpandColumn: 'common',
	frame: true,
	clicksToEdit: 1,
	maj: function(){},
	majFour: function(field,id){	
	},	
	
	tbar: [
		{
			text: '',
			iconCls: 'arrow_up-button',
			handler : function(field){

				var grid = Ext.ComponentMgr.get('{$id}');
				var index = grid.getSelectionModel().getSelectedCell();
				var rec = grid.store.getAt(index[0]);

				var store = grid.getStore();
				var records = store.getRange();

				for (var i = 0; i < records.length; i++) {
					if(rec.id==records[i].id){
						if(records[i-1]){
							for(champ in rec.data){
								var var_temp=records[i-1].data[champ];
								records[i-1].set(champ,rec.data[champ]);
								rec.set(champ,var_temp);
							}
						}
					}
				}
				grid.startEditing(index[0] - 1, index[1]);
				grid.refreshHiddenValues();
			}
		}, '-', {
			text: '',
			iconCls: 'arrow_down-button',
			handler : function(field){
				var grid = Ext.ComponentMgr.get('{$id}');
				var index = grid.getSelectionModel().getSelectedCell();
				var rec = grid.store.getAt(index[0]);

				var store = grid.getStore();
				var records = store.getRange();

				for (var i = 0; i < records.length; i++) {
					if(rec.id==records[i].id){
						if(records[i+1]){
							for(champ in rec.data){
								var var_temp=records[i+1].data[champ];
								records[i+1].set(champ,rec.data[champ]);
								rec.set(champ,var_temp);
							}
						}
					}
				}
				grid.startEditing(index[0] + 1, index[1]);
				grid.refreshHiddenValues();
			}
		
		}, '-', {
			text: '{ATF::$usr->trans(insert)|escape:javascript}',
			iconCls: 'insert-button',
			handler : function(){
				var grid = Ext.ComponentMgr.get('{$id}');
				var store = grid.getStore();
				var theType = store.recordType;
				var p = new theType({
					{foreach from=$fields item=i}
						{util::extJSEscapeDot($i)}:''{if !$i@last},{/if}
					{/foreach}
				});
				grid.stopEditing();
				
				var idx = 0; // Numéro de ligne par défaut
				var index = grid.getSelectionModel().getSelectedCell();
				if (index) {
					idx = index[0]+1; // Numéro de ligne sélectionné
				}
				
				store.insert(idx, p);
				grid.startEditing(idx, 0);					
			}	
		}, '-', {
			text: '{ATF::$usr->trans(delete)|escape:javascript}',
			iconCls: 'delete-button',
			handler: function(btn, ev) {
				var grid = Ext.ComponentMgr.get('{$id}');
				var index = grid.getSelectionModel().getSelectedCell();
				if (!index) {
					return false;
				}
				var rec = grid.store.getAt(index[0]);
				grid.store.remove(rec);
				grid.refreshHiddenValues();
				
				Ext.ComponentMgr.get('{$id}').maj();
			}
	}],
	listeners: {
		'afteredit': function(field, newVal, oldVal){
			Ext.ComponentMgr.get('{$id}').maj();
		}
	},
	
	columns: {util::getExtJSGridMappingColumns($q->getView(),$current_class)}.push({ 'dataIndex':'{$current_class->table}__dot__id_{$current_class->table}' }),
	viewConfig: {
		forceFit:true
		,showPreview:true
	},	
	cm:new Ext.grid.ColumnModel({
		/*defaults: {
			sortable: true
		},*/
		columns: [			
			{
				header: 'texte',
				height:60,
				dataIndex: '{$current_class->table}__dot__texte',
				editor: new Ext.form.TextField({
					value:""
				})	
			}, {
				header: 'Valeur',
				width:20,
				dataIndex: '{$current_class->table}__dot__valeur',								
				editor: new Ext.form.TextField({
					value:""
				})				
			}			
			, {
				header: 'Type',
				width:20,
				dataIndex: '{$current_class->table}__dot__type',
				editor: {include file="generic-gridpanel-combo.tpl.js" key=type function=null}
			}			
			, {
				header: 'Catégorie',
				dataIndex: '{$current_class->table}__dot__ligne_titre',
				editor: {include file="generic-gridpanel-combo.tpl.js" key=ligne_titre function=null},
				width:30
			}			
		]
	}),
	store:new Ext.data.JsonStore({
		root: 'result',
		totalProperty: 'totalCount',
		idProperty: 'id',
		remoteSort: true,	
		fields: ATF.extParseFields({util::getExtJSGridMappingFields($q->getView(),["{$current_class->table}.id_{$current_class->table}"])}),
		{if $function}baseParams:{ 'function':'{$function}' },{/if}
		proxy: new Ext.data.HttpProxy({
			url: '{if $proxyUrl}{$proxyUrl}{else}{$current_class->table},extJSgsa.ajax,pager={$pager}{/if}'
			,method:'POST'
		})
		,listeners:{
			load:function(field, newVal, oldVal){ {if $calcul_prix} Ext.ComponentMgr.get('{$id}').maj(); {/if} ATF.{$current_class->table}{$pager}__id_produit=Array();}
		}
	})
})