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
	columns: {util::getExtJSGridMappingColumns($q->getView(),$current_class)},
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
				header: 'Date',
				width:20,
				dataIndex: '{$current_class->table}__dot__date',
			},{
				header: 'Detail',
				width:120,
				dataIndex: '{$current_class->table}__dot__detail',
			},{
				header: 'Utilisateur',
				width:20,
				dataIndex: '{$current_class->table}__dot__id_user',
			}
		]
	}),
	store:new Ext.data.JsonStore({
		root: 'result',
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