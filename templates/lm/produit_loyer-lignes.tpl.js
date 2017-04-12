{*
$current_class
$proxyUrl
*}
ATF.buildGridEditor({
	title:'Lignes de loyer',
	id:'{$id}',
	height: 300,
	autoExpandColumn: 'common',
	frame: true,
	clicksToEdit: 1,
	maj: function(){
		var records = this.store.getRange();



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
				editor: new Ext.form.TextField({
					value:0
				})
			},{
				header: 'Nature',
				width:20,
				dataIndex: '{$current_class->table}__dot__nature',
				editor: {include file="generic-gridpanel-combo.tpl.js" key=nature function=null}
			},{
				header: 'Periodicite',
				width:20,
				dataIndex: '{$current_class->table}__dot__periodicite',
				editor: {include file="generic-gridpanel-combo.tpl.js" key=periodicite function=null}
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