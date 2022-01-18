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
	tbar: [{
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

		}
	}],
	columns: {util::getExtJSGridMappingColumns($q->getView(),$current_class)},
	viewConfig: {
		forceFit:true
		,showPreview:true
	},
	listeners: {
		'afteredit': function(field, newVal, oldVal){

		}
	},
	
	cm:new Ext.grid.ColumnModel({
		/*defaults: {
			sortable: true
		},*/
		columns: [
			{
				header: 'Type',
				dataIndex: '{$current_class->table}__dot__type',
				width:30,
				editor: new Ext.form.TextField({
					value:0,

				})	
			}, {
				header: 'Désignation',
				height:60,
				dataIndex: '{$current_class->table}__dot__designation',
				editor: new Ext.form.TextField({
					value:0,

				})				
			}, {
				header: 'Quantité',
				width:30,
				dataIndex: '{$current_class->table}__dot__quantite',
				renderer: 'money',
				editor: new Ext.form.TextField({
					value:0,

				})

			}, {
				header: 'Prix page N&B',
				width:30,
				dataIndex: '{$current_class->table}__dot__prixNB',
				editor: new Ext.form.NumberField({
					allowDecimals: true,
					decimalPrecision: 5,
					value:0,
				})
			}, {
				header: 'Prix page couleur',
				width:30,
				dataIndex: '{$current_class->table}__dot__prixC',
				editor: new Ext.form.NumberField({
					value:0,
					allowDecimals: true,
					decimalPrecision: 5
				})
			}, {
				header: 'Prix achat page N&B',
				width:30,
				dataIndex: '{$current_class->table}__dot__prix_achatNB',
				editor: new Ext.form.NumberField({
					value:0,
					allowDecimals: true,
					decimalPrecision: 5
				})
			}, {
				header: 'Prix achat page couleur',
				width:30,
				dataIndex: '{$current_class->table}__dot__prix_achatC',
				editor: new Ext.form.NumberField({
					value:0,
					allowDecimals: true,
					decimalPrecision: 5
				})			
			}
		]
	}),
	store:new Ext.data.JsonStore({
		root: 'result',
		totalProperty: 'totalCount',
		idProperty: 'id',
		remoteSort: true,
		fields: ATF.extParseFields({util::getExtJSGridMappingFields($q->getView())}),
		baseParams:{ 
			'pager':'{$pager}'
			{foreach from=$baseParams key=kParam item=iParam}
				,'{$kParam}':'{$iParam}'
			{/foreach}
			{if $function}
				,'function':'{$function}'
			{/if}
		},
		proxy: new Ext.data.HttpProxy({
			url: '{if $proxyUrl}{$proxyUrl}{else}{$current_class->table},extJSgsa.ajax{/if}'
			,method:'POST'
		}),
		listeners: {
			'load':function () {			
				Ext.ComponentMgr.get('{$id}').fireEvent('afteredit');
			}
		}
	})
})