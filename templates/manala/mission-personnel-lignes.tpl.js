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
					{util::extJSEscapeDot($i)}:"{$current_class->default_value($i)}"{if !$i@last},{/if}
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
				header: 'Personnel choisi',
				width:100,
				dataIndex: '{$current_class->table}__dot__id_personnel',
				renderer: function (value, metaData, record, rowIndex, colIndex, store){
					if (value) {
						var a = value.split(ATF.extJSGridComboboxSeparator);
						if (a[1]) {
							record.set('{$current_class->table}__dot__id_personnel_fk',a[1]);
						}
						return a[0];
					}
				},
				editor: jQuery.extend({include file="generic-gridpanel-combo.tpl.js" key=id_personnel function=null extJSGridComboboxSeparator=true},{
					listWidth:400
				})
			}, {
				hidden:true,
				dataIndex: '{$current_class->table}__dot__id_personnel_fk'
			},{
				header: 'Intitulé du poste',
				dataIndex: '{$current_class->table}__dot__poste',
				editor: new Ext.form.TextField()

			}, {
				header: "Nombre d'heure prévues",
				width:30,
				dataIndex: '{$current_class->table}__dot__heure_totale',
				editor: new Ext.form.NumberField({
					allowDecimals: false
				})
			},{
				header: 'Taux horaire',
				width:50,
				dataIndex: '{$current_class->table}__dot__prix',
				renderer: 'money',
				editor: new Ext.form.TextField({
					value:9.8,
					allowDecimals: true,
					decimalPrecision: 2
				})	
			}, {
				header: 'Panier repas ?',
				width:30,
				dataIndex: '{$current_class->table}__dot__panier_repas',
				editor : new Ext.form.ComboBox({
				    typeAhead: true,
				    triggerAction: 'all',
				    lazyRender:true,
				    mode: 'local',
				    store: new Ext.data.ArrayStore({
				        id: 0,
				        fields: [
				            'id',
				            'value'
				        ],
				        data: [["oui", "Oui"], ["non", 'Non']]
				    }),
				    valueField: 'id',
				    displayField: 'value'				
				})

			}, {
				header: 'Nombre de panier repas',
				width:30,
				dataIndex: '{$current_class->table}__dot__nb_panier_repas',
				editor: new Ext.form.TextField({
					value:0,
					allowDecimals: false
				})
			}, {
				header: 'Prix du panier repas',
				width:30,
				dataIndex: '{$current_class->table}__dot__prix_panier_repas',
				renderer: 'money',
				editor: new Ext.form.TextField({
					value:8.8,
					allowDecimals: true,
					decimalPrecision: 2
				})	
			}, {
				header: 'Défraiement (au Km)',
				width:30,
				dataIndex: '{$current_class->table}__dot__defraiement',
				editor : new Ext.form.ComboBox({
				    typeAhead: true,
				    triggerAction: 'all',
				    lazyRender:true,
				    mode: 'local',
				    store: new Ext.data.ArrayStore({
				        id: 0,
				        fields: [
				            'id',
				            'value'
				        ],
				        data: [["oui", "Oui"], ["non", 'Non'], ["forfait", 'Forfait']]
				    }),
				    valueField: 'id',
				    displayField: 'value'
				})
			}, {
				header: 'Indemnité kilométrique',
				width:30,
				dataIndex: '{$current_class->table}__dot__indemnite_defraiement',
				renderer: 'money',
				editor: new Ext.form.TextField({
					value:0,
					allowDecimals: true,
					decimalPrecision: 2
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