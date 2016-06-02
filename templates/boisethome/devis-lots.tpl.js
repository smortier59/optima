(function(){

var updateCustomFields = function() {
	var grid = Ext.ComponentMgr.get('{$id}'),
		records = grid.store.getRange(),
		prix_achat = 0,
		p = 0;
	for (var i = 0; i < grid.store.getRange().length; i++) {
		p = parseFloat(records[i].data.prix_achat*records[i].data.{$current_class->table}__dot__quantite);
		records[i].set('{$current_class->table}__dot__total',p);
		prix_achat += p;
	}

	/*Ext.ComponentMgr.get('{$parent_class->table}[prix_achat]').setValue(ATF.formatNumeric(prix_achat));*/
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
	setLot(index[0]+offset);
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
		handler : function(){
			var grid = Ext.ComponentMgr.get('{$id}');
			var store = grid.getStore();
			var theType = store.recordType;
			var p = new theType({
				{foreach from=$fields item=i}
					{util::extJSEscapeDot($i)}:'{if $i=="devis_lot.optionnel"}non{elseif $i=="devis_lot.id_devis_lot"}'+getLotNextId()+'{/if}'{if !$i@last},{/if}
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

			setLot(idx);
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
			setLot(-1);
		}
	}],
	columns: {util::getExtJSGridMappingColumns($q->getView(),$current_class)}.push({ 'dataIndex':'{$current_class->table}__dot__id_article_fk' }),
	viewConfig: {
		forceFit:true
		,showPreview:true
	},
	listeners: {
		'cellclick': function(cell){
			var grid = Ext.ComponentMgr.get('{$id}');
			var index = grid.getSelectionModel().getSelectedCell();
			setLot(index[0]);
		},
		'afteredit': function(field, newVal, oldVal){
			updateCustomFields();
			if (field && field.row)
				setLot(field.row);
		}
	},

	cm:new Ext.grid.ColumnModel({
		/*defaults: {
			sortable: true
		},*/
		columns: [
			{
				header: 'Libellé',
				dataIndex: '{$current_class->table}__dot__libelle',
				editor: new Ext.form.TextField()
			}, {
				header: 'Optionnel',
				width:20,
				dataIndex: '{$current_class->table}__dot__optionnel',
				editor: jQuery.extend({include file="generic-gridpanel-combo.tpl.js" key=optionnel function=null extJSGridComboboxSeparator=true},{ })
			}, {
				header: '% paiement',
				width:50,
				dataIndex: '{$current_class->table}__dot__payer_pourcentage',
				editor: new Ext.form.TextField()
			/*}, {
				header: 'Prix achat',
				width:20,
				dataIndex: 'prix_achat',
				renderer: 'money',
			}, {
				header: 'Prix vendu',
				width:20,
				dataIndex: 'prix',
				renderer: 'money',
			}, {
				header: 'Marge',
				width:20,
				dataIndex: 'marge',
				renderer: 'decimal_pourcent',
			}, {
				header: 'Marge abs.',
				width:30,
				dataIndex: 'marge_absolue',
				renderer: 'money',*/
			}, {
				header: '#lot',
				hidden:true,
				dataIndex: '{$current_class->table}__dot__id_devis_lot'
			}
		]
	}),
	store:new Ext.data.JsonStore({
		root: 'result',
		totalProperty: 'totalCount',
		idProperty: 'id',
		remoteSort: true,
		fields: ATF.extParseFields({util::getExtJSGridMappingFields($q->getView(),["{$current_class->table}.quantite"],"{$current_class->table}.id_article_fk")}),
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
				Ext.ComponentMgr.get('{$id}').refreshHiddenValues();
			}
		}
	})
});
})()