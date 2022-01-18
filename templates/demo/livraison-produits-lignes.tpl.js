{*
$current_class
$proxyUrl
*}
//var Checkbox = new Ext.grid.CheckboxSelectionModel();
ATF.buildGridEditor({
	title:'Stock',
	id:'{$id}',
	height: 300,
	autoExpandColumn: 'stock',
	frame: true,
	clicksToEdit: 1,
	tbar: [
		{
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
				header: 'Stock',
				dataIndex: '{$current_class->table}__dot__id_stock',
				width:20,
				hidden:true
			}, {
				header: 'Référence',
				css : "background:#C4CEF7;",
				dataIndex: '{$current_class->table}__dot__ref',
				width:6
			}, {
				header: 'Produit',
				height:70,
				css : "background:#C6E2FF;",
				dataIndex: '{$current_class->table}__dot__libelle'
			}, {
				header: 'Serial',
				width:10,
				css : "background:#C4CEF7;",
				dataIndex: '{$current_class->table}__dot__serial',
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