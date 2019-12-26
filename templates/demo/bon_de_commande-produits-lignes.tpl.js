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
			
			var records = store.getRange();
			records[idx].set('{$current_class->table}__dot__etat','en_cours');
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
			
			var records = Ext.ComponentMgr.get('{$id}').store.getRange();
			var prix_achat = 0;
			var prix = 0;
			for (var i = 0; i < Ext.ComponentMgr.get('{$parent_class->table}[produits]').store.getRange().length; i++) {
				prix+=parseFloat(records[i].data.{$current_class->table}__dot__prix*records[i].data.{$current_class->table}__dot__quantite); 
				prix_achat+=parseFloat(records[i].data.{$current_class->table}__dot__prix_achat*records[i].data.{$current_class->table}__dot__quantite); 
			}
			Ext.ComponentMgr.get('{$parent_class->table}[prix_achat]').setValue(ATF.formatNumeric(prix_achat));
			Ext.ComponentMgr.get('{$parent_class->table}[prix]').setValue(ATF.formatNumeric(prix));
		}
	}, '-', {
		text: '{ATF::$usr->trans(delete_all)|escape:javascript}',
		iconCls: 'delete-button',
		handler: function(btn, ev) {			
			var grid = Ext.ComponentMgr.get('{$id}');	
			
			grid.store.removeAll();
			grid.refreshHiddenValues();	
			Ext.ComponentMgr.get('{$parent_class->table}[prix_achat]').setValue(0);
			Ext.ComponentMgr.get('{$parent_class->table}[prix]').setValue(0);
		}
	}],
	columns: {util::getExtJSGridMappingColumns($q->getView(),$current_class)}.push({ 'dataIndex':'{$current_class->table}__dot__id_fournisseur_fk' },{ 'dataIndex':'{$current_class->table}__dot__id_compte_absystech_fk' },{ 'dataIndex':'{$current_class->table}__dot__marge' },{ 'dataIndex':'{$current_class->table}__dot__marge_absolue' }),
	viewConfig: {
		forceFit:true
		,showPreview:true
	},
	listeners: {
		'afteredit': function(field, newVal, oldVal){
			var records = this.store.getRange();
			var prix_achat = 0;
			var prix = 0;
			for (var i = 0; i < this.store.getRange().length; i++) {
				prix+=parseFloat(records[i].data.{$current_class->table}__dot__prix*records[i].data.{$current_class->table}__dot__quantite); 
				prix_achat+=parseFloat(records[i].data.{$current_class->table}__dot__prix_achat*records[i].data.{$current_class->table}__dot__quantite); 
			}
			Ext.ComponentMgr.get('{$parent_class->table}[prix_achat]').setValue(ATF.formatNumeric(prix_achat));
			Ext.ComponentMgr.get('{$parent_class->table}[prix]').setValue(ATF.formatNumeric(prix));
		}
	},
	
	cm:new Ext.grid.ColumnModel({
		/*defaults: {
			sortable: true
		},*/
		columns: [
			{
				header: 'Référence',
				dataIndex: '{$current_class->table}__dot__ref',
				width:30
			}, {
				header: 'Produit',
				height:60,
				dataIndex: '{$current_class->table}__dot__produit'
				
			}, {
				header: 'Quantité',
				width:30,
				dataIndex: '{$current_class->table}__dot__quantite',
				renderer: 'money',
				editor: new Ext.form.TextField({
					value:0
				})
			}, {
				header: 'Prix',
				hidden:true,
				width:20,
				dataIndex: '{$current_class->table}__dot__prix',
				renderer: 'money'
			}, {
				header: 'Px Achat',
				width:20,
				dataIndex: '{$current_class->table}__dot__prix_achat',
				renderer: 'money'
			},{
				header: 'En stock ?',
				width:30,
				dataIndex: '{$current_class->table}__dot__etat'
				,editor: {include file="generic-gridpanel-combo.tpl.js" key=etat function=null value=en_cours}
			}
		]
	}),
	store:new Ext.data.JsonStore({
		root: 'result',
		totalProperty: 'totalCount',
		idProperty: 'id',
		remoteSort: true,
		fields: ATF.extParseFields({util::getExtJSGridMappingFields($q->getView(),["{$current_class->table}.id_fournisseur_fk","{$current_class->table}.id_compte_absystech_fk"])}),
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