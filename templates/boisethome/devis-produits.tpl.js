(function(){
var moveRow = function (offset) {
	var grid = Ext.ComponentMgr.get('{$id}');
	var index = grid.getSelectionModel().getSelectedCell();
	var rec = grid.store.getAt(index[0]);

	var store = grid.getStore();
	var records = store.getRange();

	for (var i = 0; i < records.length; i++) {
		if(rec.id==records[i].id){
			if(records[i+offset]){
				for(champ in rec.data){
					var var_temp=records[i+offset].data[champ];
					records[i+offset].set(champ,rec.data[champ]);
					rec.set(champ,var_temp);
				}
			}
		}
	}
	grid.refreshHiddenValues();
	grid.getSelectionModel().selectCell([index[0]+offset,index[1]]);
	grid.startEditing(index[0]+offset, index[1]);
	setProduit(index[0]+offset);
};

return ATF.buildGridEditor({
	title:'{$title}',
	id:'{$id}',
	height: 300,
	autoExpandColumn: 'common',
	frame: true,
	clicksToEdit: 1,
	tbar: [{
		text: '',
		iconCls: 'arrow_up-button',
		handler: function(field){ moveRow(-1); }
	}, '-', {
		text: '',
		iconCls: 'arrow_down-button',
		handler: function(field){ moveRow(1); }
	}, '-', {
		text: '{ATF::$usr->trans(insert)|escape:javascript}',
		iconCls: 'insert-button',
		id: 'insertBtnProduit',
		disabled:true,
		handler : function(){
			var pk_lot = getLot();
			var grid = Ext.ComponentMgr.get('{$id}');
			var store = grid.getStore();
			var theType = store.recordType;
			var p = new theType({
				{foreach from=$fields item=i}
					{util::extJSEscapeDot($i)}:'{if $i=="devis_lot_produit.id_devis_lot"}'+pk_lot+'{elseif $i=="devis_lot_produit.id_devis_lot_produit"}'+getProduitNextId()+'{/if}'{if !$i@last},{/if}
				{/foreach}
			});
			grid.stopEditing();

			var idx = 0; // Numéro de ligne par défaut
			var index = grid.getSelectionModel().getSelectedCell();
			if (index) {
				idx = index[0]+1; // Numéro de ligne sélectionné
			}

			store.insert(idx, p);
			grid.getSelectionModel().selectCell([idx,0]);
			grid.startEditing(idx, 0);

			$(grid.getView().getRow(idx)).data("lot",pk_lot);
			setProduit(idx);
		}
	}, '-', {
		text: '{ATF::$usr->trans(delete)|escape:javascript}',
		iconCls: 'delete-button',
		handler: function(btn, ev) {
			var grid = Ext.ComponentMgr.get('{$id}');
			var index = grid.getSelectionModel().getSelectedCell();
			if (index) {
				var rec = grid.store.getAt(index[0]);
				grid.store.remove(rec);
				grid.refreshHiddenValues();
			}
			updateCustomFields();
			setProduit(-1);
		}
	}],
	columns: {util::getExtJSGridMappingColumns($q->getView(),$current_class)}.push({ 'dataIndex':'{$current_class->table}__dot__id_fournisseur_fk' }),
	viewConfig: {
		forceFit:true
		,showPreview:true
	},
	listeners: {
		'cellclick': function(cell){
			var grid = Ext.ComponentMgr.get('{$id}');
			var index = grid.getSelectionModel().getSelectedCell();
			setProduit(index[0]);
		},
		'afteredit': function(field, newVal, oldVal){
			if (field && field.row)
				setProduit(field.row);
		}
	},

	cm:new Ext.grid.ColumnModel({
		/*defaults: {
			sortable: true
		},*/
		columns: [
			{
				header: 'Produit',
				height:60,
				dataIndex: '{$current_class->table}__dot__produit',
				editor: jQuery.extend({include file="generic-gridpanel-combo.tpl.js" key=id_produit function=null extJSGridComboboxSeparator=true},{
					listWidth:400
				})
			}, {
				header: 'id_produit',
				hidden:true,
				dataIndex: '{$current_class->table}__dot__id_produit_fk'
			}, {
				header: 'Description',
				width:60,
				dataIndex: '{$current_class->table}__dot__description',
				editor: new Ext.form.TextField({
					value:0
				})
			},{
				header: 'Quantité',
				width:20,
				dataIndex: '{$current_class->table}__dot__quantite',
				editor: new Ext.form.TextField({
					value:0
				})
			},{
				header: 'Unité',
				width:20,
				dataIndex: '{$current_class->table}__dot__unite',
				editor: jQuery.extend({include file="generic-gridpanel-combo.tpl.js" key=unite function=null extJSGridComboboxSeparator=true},{ })
			}, {
				header: 'Lambda',
				width:20,
				dataIndex: '{$current_class->table}__dot__lambda',
				editor: new Ext.form.TextField({
					value:0
				})
			/*}, {
				header: 'Prix achat',
				width:20,
				dataIndex: '{$current_class->table}__dot__prix_achat'
			}, {
				header: 'Prix vendu',
				width:20,
				dataIndex: '{$current_class->table}__dot__prix'*/
			}, {
				header: '#lot',
				width:20,
				hidden:true,
				dataIndex: '{$current_class->table}__dot__id_devis_lot'
			}, {
				header: '#produit',
				width:20,
				hidden:true,
				dataIndex: '{$current_class->table}__dot__id_devis_lot_produit'
			}
		]
	}),
	store:new Ext.data.JsonStore({
		root: 'result',
		totalProperty: 'totalCount',
		idProperty: 'id',
		remoteSort: true,
		fields: ATF.extParseFields({util::getExtJSGridMappingFields($q->getView(),["{$current_class->table}.id_produit_fk","{$current_class->table}.id_devis_lot_produit","{$current_class->table}.id_devis_lot"])}),
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
				Ext.ComponentMgr.get('{$id}').refreshHiddenValues();
			}
		}
	})
});
})()