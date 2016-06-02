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
	maj: function(){
		var records = this.store.getRange();
		var prix = 0;
		var total = 0;

		for (var i = 0; i < this.store.getRange().length; i++) {
			prix=(
				records[i].data.{$current_class->table}__dot__loyer*1+
				records[i].data.{$current_class->table}__dot__assurance*1+
				records[i].data.{$current_class->table}__dot__frais_de_gestion*1
			)*records[i].data.{$current_class->table}__dot__duree; 
			records[i].set('{$current_class->table}__dot__loyer_total',prix);
			total+=prix;
		}
		if(Ext.ComponentMgr.get('{$table}[prix]')){
			Ext.ComponentMgr.get('{$table}[prix]').setValue(ATF.formatNumeric(total));
		}

		if(Ext.ComponentMgr.get('{$table}[marge]')){
			Ext.ComponentMgr.get('{$table}[marge]').setValue(ATF.formatNumeric(parseFloat((total-Ext.ComponentMgr.get('{$table}[prix_achat]').getValue().replace(' ','')*1)/total)*100));
		}

		if(Ext.ComponentMgr.get('{$table}[marge_absolue]')){
			Ext.ComponentMgr.get('{$table}[marge_absolue]').setValue(ATF.formatNumeric(parseFloat(total-Ext.ComponentMgr.get('{$table}[prix_achat]').getValue().replace(' ','')*1)));
		}
	},
	{if !$no_update}
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
				
				var idx = 0; // Numéro de ligne par défaut.

				if(store.getCount() != 0){					
					idx = store.getCount();					
				}

				if(idx == 0){				
					var index = grid.getSelectionModel().getSelectedCell();
					if (index) {
						idx = index[0]+1; // Numéro de ligne sélectionné
					}
				}				
				
				store.insert(idx, p);
				grid.startEditing(idx, 0);
				var records = Ext.ComponentMgr.get('{$id}').store.getRange();
				records[idx].set('{$current_class->table}__dot__frequence_loyer','mois');
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
	{/if}
	columns: {util::getExtJSGridMappingColumns($q->getView(),$current_class)},
	viewConfig: {
		forceFit:true
		,showPreview:true
	},
	listeners: {
		'afteredit': function(field, newVal, oldVal){
			Ext.ComponentMgr.get('{$id}').maj();
		}
	},
	cm:new Ext.grid.ColumnModel({
		/*defaults: {
			sortable: true
		},*/
		columns: [
		    {
				header: 'Durée',
				width:20,
				dataIndex: '{$current_class->table}__dot__duree',
				{if !$no_update}
					editor: new Ext.form.TextField({
						value:0
					})
				{/if}
			},{
				header: 'Loyer',
				width:20,
				dataIndex: '{$current_class->table}__dot__loyer',
				renderer: 'money',
				{if !$no_update}
					editor: new Ext.form.TextField({
						value:0
					})
				{/if}
			},{
				header: 'Frais',
				width:20,
				dataIndex: '{$current_class->table}__dot__frais_de_gestion',
				renderer: 'money'
				{if !$no_update}
					,editor: new Ext.form.TextField({
						value:0
					})
				{/if}
			},{
				header: 'Assurance',
				width:20,
				dataIndex: '{$current_class->table}__dot__assurance',
				renderer: 'money'
				{if !$no_update}
					,editor: new Ext.form.TextField({
						value:0
					})
				{/if}
			},{
				header: 'Fréquence',
				width:20,
				dataIndex: '{$current_class->table}__dot__frequence_loyer'
				{if !$no_update}
					,editor: {include file="generic-gridpanel-combo.tpl.js" key=frequence_loyer function=null}
				{/if}
			},{
				header: 'Loyer Total',
				width:20,
				dataIndex: '{$current_class->table}__dot__loyer_total',
				renderer: 'money',
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
		{if $calcul_prix}
			,listeners:{
				load:function(field, newVal, oldVal){ {if $calcul_loyer} Ext.ComponentMgr.get('{$id}').maj(); {/if} }
			}
		{/if}
	})
})