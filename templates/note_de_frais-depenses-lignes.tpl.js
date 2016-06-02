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
		}
	}],
	columns: {util::getExtJSGridMappingColumns($q->getView(),$current_class)},
	viewConfig: {
		forceFit:true
		,showPreview:true
	},
	coeffs: {ATF::frais_kilometrique()->getCoeffs()},
	listeners: {
		'afteredit': function(field, newVal, oldVal){
			var grid = Ext.ComponentMgr.get('{$id}');
	
			var records = grid.store.getRange();
			for (var i = 0; i < records.length; i++) {
				var a = grid.getColumnModel().getIndexById('montant');
				var editor = grid.getColumnModel().getCellEditor(a,i);
				if (records[i].data.note_de_frais_ligne__dot__id_frais_kilometrique || records[i].data.note_de_frais_ligne__dot__km) {
					editor.disable();
				} else {
					editor.enable();
				}

			}
			
		}
	},
	cm:new Ext.grid.ColumnModel({
		columns: [
			{
				hidden:true,
				dataIndex: 'note_de_frais_ligne__dot__id_note_de_frais_ligne'
			}, {
				header: 'Libellé',
				dataIndex: '{$current_class->table}__dot__objet',
				width:150,
				editor: new Ext.form.TextField({
				})
			}, {
				header: 'Date',
				height:50,
				dataIndex: '{$current_class->table}__dot__date',
				id:"dateLigne",
				renderer: function(v,params,record){
					return Ext.util.Format.date(v,'d-m-Y');
				},
				editor: new Ext.form.DateField({
					format: 'd-m-Y'
					,width:120
				})
			}, {
				header: 'Société',
				width:70,
				dataIndex: '{$current_class->table}__dot__id_societe',
				renderer: function (value, metaData, record, rowIndex, colIndex, store){
					if (value) {
						var a = value.split(ATF.extJSGridComboboxSeparator);
						if (a[1]) {
							record.set('{$current_class->table}__dot__id_societe_fk',a[1]);
						}
						return a[0];
					}
				},
				editor: jQuery.extend({include file="generic-gridpanel-combo.tpl.js" key=id_societe function=autocomplete extJSGridComboboxSeparator=true},{
					listWidth:200
				})
			}, {
				hidden:true,
				dataIndex: '{$current_class->table}__dot__id_societe_fk'
			}, {
				header: 'Km Parcouru',
				width:70,
				dataIndex: '{$current_class->table}__dot__km',
				editor: new Ext.form.NumberField({
					allowBlank: true
				})
			}, {
				header: 'Frais Kilométrique',
				width:70,
				dataIndex: '{$current_class->table}__dot__id_frais_kilometrique',
				renderer: function (value, metaData, record, rowIndex, colIndex, store){
					if (value) {
						var valueKM = record.get("{$current_class->table}__dot__km");
						var a = value.split(ATF.extJSGridComboboxSeparator);
						ATF.log(a);
						if (a[1]) {
							record.set('{$current_class->table}__dot__id_frais_kilometrique_fk',a[1]);
						}
						if (valueKM && a[1]) {
							record.set('{$current_class->table}__dot__montant',valueKM*Ext.getCmp('{$id}').coeffs[a[1]].coeff);
						}
						// Evite de marquer la merde retournée par le CONCAT WS SQL a cause du field nom.
						if (a[0] != " - CV " && a[0]!="  - CV") {
							return a[0];
						}
						return "";
					}
				}
				,editor: jQuery.extend({include file="generic-gridpanel-combo.tpl.js" key=id_frais_kilometrique function=autocomplete extJSGridComboboxSeparator=true},{
					listWidth:200,
					allowBlank: true
				})
			}, {
				hidden:true,
				dataIndex: '{$current_class->table}__dot__id_frais_kilometrique_fk'
			}, {
				header: 'Montant',
				width:50,
				dataIndex: '{$current_class->table}__dot__montant',
				id: 'montant',
				renderer: 'money',
				editor: new Ext.form.TextField({
					allowBlank: true
				})
			}
		]
	}),
	store:new Ext.data.JsonStore({
		root: 'result',
		totalProperty: 'totalCount',
		idProperty: 'id',
		remoteSort: true,	
		fields: ATF.extParseFields({util::getExtJSGridMappingFields($q->getView(),["{$current_class->table}.id_{$current_class->table}"])}),
		{if $function}baseParams:{ 'function':'{$function}' },{/if}
		proxy: new Ext.data.HttpProxy({
			url: '{if $proxyUrl}{$proxyUrl}{else}{$current_class->table},extJSgsa.ajax,pager={$pager}{/if}'
			,method:'POST'
		})
	})
})