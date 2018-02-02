(function(){

var updateCustomFields = function() {
	/* Faire la somme totale des articles */
	var prix = 0, prix_achat = 0;

	var grid = Ext.ComponentMgr.get('{$id}');
	var store = grid.getStore();
	var records = store.getRange();

	for (var i = 0; i < records.length; i++) {
		prix_achat += 1*records[i].data.devis_lot_produit_article__dot__prix_achat * records[i].data.devis_lot_produit_article__dot__quantite;
		prix += 1*records[i].data.devis_lot_produit_article__dot__prix_achat * records[i].data.devis_lot_produit_article__dot__id_marge * records[i].data.devis_lot_produit_article__dot__quantite;
	}

	Ext.ComponentMgr.get('{$parent_class->table}[prix]').setValue(Math.round(100*prix)/100);
	Ext.ComponentMgr.get('{$parent_class->table}[prix_achat]').setValue(Math.round(100*prix_achat)/100);
	Ext.ComponentMgr.get('{$parent_class->table}[marge]').setValue(Math.round(100*100*(prix-prix_achat)/prix)/100);
	Ext.ComponentMgr.get('{$parent_class->table}[marge_absolue]').setValue(Math.round(100*(prix - prix_achat)/100));

	Ext.ComponentMgr.get('{$id}').refreshHiddenValues();
};

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
		id: 'insertBtnArticle',
		disabled:true,
		handler : function(){
			var pk_produit = getProduit();
			var grid = Ext.ComponentMgr.get('{$id}');
			var store = grid.getStore();
			var theType = store.recordType;
			var o = {
				{foreach from=$fields item=i}
					{util::extJSEscapeDot($i)}:'{if $i=="devis_lot_produit_article.id_devis_lot_produit"}'+pk_produit+'{/if}'{if !$i@last},{/if}
				{/foreach}
			};
			var p = new theType(o);
			grid.stopEditing();

			var idx = 0; // Numéro de ligne par défaut
			var index = grid.getSelectionModel().getSelectedCell();
			if (index) {
				idx = index[0]+1; // Numéro de ligne sélectionné
			}

			store.insert(idx, p);
			grid.getSelectionModel().selectCell([idx,0]);
			grid.startEditing(idx, 0);
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
		}
	}],
	columns: {util::getExtJSGridMappingColumns($q->getView(),$current_class)}.push({ 'dataIndex':'{$current_class->table}__dot__id_fournisseur_fk' },{ 'dataIndex':'{$current_class->table}__dot__id_compte_absystech_fk' },{ 'dataIndex':'{$current_class->table}__dot__marge' },{ 'dataIndex':'{$current_class->table}__dot__marge_absolue' }),
	viewConfig: {
		forceFit:true
		,showPreview:true
	},
	listeners: {
		'cellclick': function(cell){
		},
		'afteredit': function(field, newVal, oldVal){
			updateCustomFields();
		}
	},
	cm:new Ext.grid.ColumnModel({
		columns: [
			{
				header: 'Quantité',
				width:20,
				dataIndex: '{$current_class->table}__dot__quantite',
				editor: new Ext.form.TextField({
					value:0
				})
			}, {
				header: 'Article',
				height:60,
				dataIndex: '{$current_class->table}__dot__id_article',
				editor: jQuery.extend({include file="generic-gridpanel-combo.tpl.js" key=id_article function=null extJSGridComboboxSeparator=true},{
					listWidth:400
				})
			}, {
				hidden:true,
				dataIndex: '{$current_class->table}__dot__id_article_fk'
			}, {
				header: 'Unité',
				width:20,
				dataIndex: '{$current_class->table}__dot__unite',
				editor: jQuery.extend({include file="generic-gridpanel-combo.tpl.js" key=unite function=null extJSGridComboboxSeparator=true},{ })
			}, {
				header: 'Fournisseur',
				width:50,
				dataIndex: '{$current_class->table}__dot__id_fournisseur',
				renderer: function (value, metaData, record, rowIndex, colIndex, store){
					if (value) {
						var a = value.split(ATF.extJSGridComboboxSeparator);
						if (a[1]) {
							record.set('{$current_class->table}__dot__id_fournisseur_fk',a[1]);
						}
						return a[0];
					}
				},
				editor: jQuery.extend({include file="generic-gridpanel-combo.tpl.js" key=id_fournisseur function=null extJSGridComboboxSeparator=true},{ listWidth:200 })
			}, {
				hidden:true,
				dataIndex: '{$current_class->table}__dot__id_fournisseur_fk'
			}, {
				header: 'Conditionnement',
				width:50,
				dataIndex: '{$current_class->table}__dot__conditionnement',
				editor: new Ext.form.TextField({
					value:0
				})
			}, {
				header: 'Visible',
				width:20,
				dataIndex: '{$current_class->table}__dot__visible',
				editor: jQuery.extend({include file="generic-gridpanel-combo.tpl.js" key=visible function=null extJSGridComboboxSeparator=true},{ })
			}, {
				header: 'Px Achat',
				width:20,
				dataIndex: '{$current_class->table}__dot__prix_achat',
				renderer: 'money',
				editor: new Ext.form.TextField({
					value:0
				})
			}, {
				header: 'Taux de marge',
				width:50,
				dataIndex: '{$current_class->table}__dot__id_marge',
				editor: jQuery.extend({include file="generic-gridpanel-combo.tpl.js" key=id_marge function=null extJSGridComboboxSeparator=true},{ listWidth:200 })
			}, {
				hidden:true,
				dataIndex: '{$current_class->table}__dot__id_marge_fk'
			/*}, {
				header: 'Prix vendu',
				width:20,
				dataIndex: '{$current_class->table}__dot__prix'*/
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
		fields: ATF.extParseFields({util::getExtJSGridMappingFields($q->getView(),["{$current_class->table}.id_article_fk","{$current_class->table}.id_fournisseur_fk","{$current_class->table}.id_marge_fk","{$current_class->table}.id_devis_lot_produit_fk"])}),
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