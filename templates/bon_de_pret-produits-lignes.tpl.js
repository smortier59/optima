{*
$current_class
$proxyUrl
*}
ATF.buildGridEditor({
	title:'Stock',
	id:'{$id}',
	height: 300,
	autoExpandColumn: 'stock',
	frame: true,
	clicksToEdit: 1,
	tbar: [
		{
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
			var grid = Ext.ComponentMgr.get('{$id|escape:javascript}');
			var index = grid.getSelectionModel().getSelectedCell();
				if (!index) {
					return false;
				}
			var rec = grid.store.getAt(index[0]);
			grid.store.remove(rec);
			grid.refreshHiddenValues();
		 }
	}],

	viewConfig: {
		forceFit:true,
		showPreview:true
	},

	cm:new Ext.grid.ColumnModel({
		columns: [
			{
				header: 'Référence',
				dataIndex: '{$current_class->table}__dot__ref',
				width:20
			},{
				header: 'Stock',
				dataIndex: '{$current_class->table}__dot__stock',
				editor: jQuery.extend({include file="generic-gridpanel-combo.tpl.js" key=id_stock function=null extJSGridComboboxSeparator=true},{
					listWidth:400
				})
			},{
				dataIndex: '{$current_class->table}__dot__id_stock',
				hidden:true
			}, {
				header: 'Serial',
				width:30,
				dataIndex: '{$current_class->table}__dot__serial',
				renderer: 'string'
			}, {
				header: 'SerialAT',
				width:50,
				dataIndex: '{$current_class->table}__dot__serialAT',
				renderer: 'string'
			}
		]
	}),
	store:new Ext.data.JsonStore({
		root: 'result',
		totalProperty: 'totalCount',
		idProperty: 'id',
		remoteSort: true,	
		fields: ATF.extParseFields({util::getExtJSGridMappingFields($q->getView())}),
		{if $function}baseParams:{ 'function':'{$function}' },{/if}
		proxy: new Ext.data.HttpProxy({
			url: '{if $proxyUrl}{$proxyUrl}{else}{$current_class->table},extJSgsa.ajax,pager={$pager}{/if}'
			,method:'POST'
		})
	})
})
					
