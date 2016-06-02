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

			var records = this.store.getRange(),
			 prix_achat = 0,
			 p = 0;
			for (var i = 0; i < this.store.getRange().length; i++) {
				p = parseFloat(records[i].data.prix_achat*records[i].data.{$current_class->table}__dot__quantite);
				records[i].set('{$current_class->table}__dot__total',p);
				prix_achat += p;
			}

			Ext.ComponentMgr.get('{$parent_class->table}[prix_achat]').setValue(ATF.formatNumeric(prix_achat));
		}
	}],
	columns: {util::getExtJSGridMappingColumns($q->getView(),$current_class)}.push({ 'dataIndex':'{$current_class->table}__dot__id_article_fk' }),
	viewConfig: {
		forceFit:true
		,showPreview:true
	},
	listeners: {
		'afteredit': function(field, newVal, oldVal){
			var records = this.store.getRange(),
			 prix_achat = 0,
			 p = 0;
			for (var i = 0; i < this.store.getRange().length; i++) {
				p = parseFloat(records[i].data.prix_achat*records[i].data.{$current_class->table}__dot__quantite);
				records[i].set('{$current_class->table}__dot__total',p);
				prix_achat += p;
			}

			Ext.ComponentMgr.get('{$parent_class->table}[prix_achat]').setValue(ATF.formatNumeric(prix_achat));
		}
	},

	cm:new Ext.grid.ColumnModel({
		/*defaults: {
			sortable: true
		},*/
		columns: [
			{
				header: 'Quantité',
				width:20,
				dataIndex: '{$current_class->table}__dot__quantite',
				renderer: 'money',
				editor: new Ext.form.TextField({
					value:0
				})
			}, {
				header: 'Article',
				height:60,
				dataIndex: '{$current_class->table}__dot__article',
				editor: jQuery.extend({include file="generic-gridpanel-combo.tpl.js" key=id_article function=null extJSGridComboboxSeparator=true},{
					listWidth:400
				})
			}, {
				header: 'Prix d\'achat catalogue',
				width:20,
				dataIndex: 'prix_achat',
				renderer: 'money'
			}, {
				header: 'Total',
				width:20,
				dataIndex: '{$current_class->table}__dot__total',
				renderer: 'money'
			}, {
				hidden:true,
				dataIndex: '{$current_class->table}__dot__id_article_fk'
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
			}
		}
	})
})