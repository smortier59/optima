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

			var records = Ext.ComponentMgr.get('{$id}').store.getRange();
			records[idx].set('{$current_class->table}__dot__visible','oui');
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
			var sous_total = 0;
			var poids = 0;
			var prix_achat = 0;
			var frais_de_port = 0;
			var prix = 0;
			var marge = 0;
			var marge_absolue = 0;
			var maintenance = 0;		

			for (var i = 0; i < Ext.ComponentMgr.get('{$parent_class->table}[produits]').store.getRange().length; i++) {
				sous_total+=parseFloat(records[i].data.{$current_class->table}__dot__prix*records[i].data.{$current_class->table}__dot__quantite); 
				prix_achat+=parseFloat(records[i].data.{$current_class->table}__dot__prix_achat*records[i].data.{$current_class->table}__dot__quantite); 
				{if $parent_class->table==devis}
					poids+=parseFloat(records[i].data.{$current_class->table}__dot__poids*records[i].data.{$current_class->table}__dot__quantite); 
				{/if}
			}

			Ext.ComponentMgr.get('{$parent_class->table}[prix_achat]').setValue(ATF.formatNumeric(prix_achat));
			
			
			{if $parent_class->table==devis}
				ATF.ajax('societe_frais_port,frais_port.ajax','poids='+poids,{ 
					onComplete:function(obj){
						frais_de_port+=parseFloat(obj.result);
						prix+=sous_total+frais_de_port;
						Ext.ComponentMgr.get('{$parent_class->table}[frais_de_port]').setValue(ATF.formatNumeric(frais_de_port)); 
						Ext.ComponentMgr.get('{$parent_class->table}[prix]').setValue(ATF.formatNumeric(prix));
					
						if(Ext.getCmp('devis[cout_total_financement]').value){
							var prix_vente = parseFloat(Ext.getCmp('devis[cout_total_financement]').value.replace(" ", "")); 
							var pa = parseFloat(Ext.getCmp('devis[prix_achat]').value.replace(" ", ""));
														
							var mb = (1 - (pa/prix_vente)) * 100;	
							Ext.getCmp('devis[marge_financement]').setValue(ATF.formatNumeric(parseFloat(mb)));
						}
											
						{*
							var total = 0;
							
							if(Ext.getCmp('devis[duree_financement]').value 
							&& Ext.getCmp('devis[marge_financement]').value 
							&& Ext.getCmp('devis[prix]').value){
								var duree = Ext.getCmp('devis[duree_financement]').value;
								var margeFin = Ext.getCmp('devis[marge_financement]').value;
								var prixFin = prix;	
								if(Ext.getCmp('devis[maintenance_financement]').value){
									maintenance = Ext.getCmp('devis[maintenance_financement]').value  + parseFloat(maintenance) ;
								}
								
								total = parseFloat(((prix * (margeFin/100)  ) + parseFloat(prixFin))/ duree) ;
							}
						
							Ext.getCmp('devis[financement_mois]').setValue(ATF.formatNumeric(parseFloat(total)));
						*}
					} 
				});
			{else}
				frais_de_port=parseFloat(Ext.ComponentMgr.get('{$parent_class->table}[frais_de_port]').getValue());
				prix+=sous_total+frais_de_port;
				Ext.ComponentMgr.get('{$parent_class->table}[frais_de_port]').setValue(ATF.formatNumeric(frais_de_port)); 
				Ext.ComponentMgr.get('{$parent_class->table}[prix]').setValue(ATF.formatNumeric(prix));

			{/if}
			Ext.ComponentMgr.get('{$parent_class->table}[sous_total]').setValue(ATF.formatNumeric(sous_total));
			Ext.ComponentMgr.get('{$parent_class->table}[marge]').setValue(ATF.formatNumeric(parseFloat((sous_total-prix_achat)/sous_total)*100));
			Ext.ComponentMgr.get('{$parent_class->table}[marge_absolue]').setValue(ATF.formatNumeric(parseFloat(sous_total-prix_achat)));
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
			var sous_total = 0;
			var poids = 0;
			var prix_achat = 0;
			var frais_de_port = 0;
			var prix = 0;
			var marge = 0;
			var marge_absolue = 0;
			var maintenance = 0;		

			for (var i = 0; i < this.store.getRange().length; i++) {
				if(records[i].data.{$current_class->table}__dot__visible == "oui"){
					sous_total+=parseFloat(records[i].data.{$current_class->table}__dot__prix*records[i].data.{$current_class->table}__dot__quantite); 
					prix_achat+=parseFloat(records[i].data.{$current_class->table}__dot__prix_achat*records[i].data.{$current_class->table}__dot__quantite); 
					{if $parent_class->table==devis}
						poids+=parseFloat(records[i].data.{$current_class->table}__dot__poids*records[i].data.{$current_class->table}__dot__quantite); 
					{/if}
				} else {
					records[i].data.{$current_class->table}__dot__quantite = 1;
					records[i].data.{$current_class->table}__dot__prix = 0;
					records[i].data.{$current_class->table}__dot__marge = 100;
				}		
			}

			var r = Ext.ComponentMgr.get('{$id}').getStore().getRange(field.row,field.row);
			marge = parseFloat((r[0].data.{$current_class->table}__dot__prix-r[0].data.{$current_class->table}__dot__prix_achat)/r[0].data.{$current_class->table}__dot__prix)*100;
			
			
			r[0].set('{$current_class->table}__dot__marge',marge);
			marge_absolue = parseFloat((r[0].data.{$current_class->table}__dot__prix*r[0].data.{$current_class->table}__dot__quantite)-(r[0].data.{$current_class->table}__dot__prix_achat*r[0].data.{$current_class->table}__dot__quantite));
			r[0].set('{$current_class->table}__dot__marge_absolue',marge_absolue);

			Ext.ComponentMgr.get('{$parent_class->table}[prix_achat]').setValue(ATF.formatNumeric(prix_achat));				
			
			{if $parent_class->table==devis}
				ATF.ajax('societe_frais_port,frais_port.ajax','poids='+poids,{ 
					onComplete:function(obj){
						frais_de_port+=parseFloat(obj.result);
						prix+=sous_total+frais_de_port;
						Ext.ComponentMgr.get('{$parent_class->table}[frais_de_port]').setValue(ATF.formatNumeric(frais_de_port)); 
						Ext.ComponentMgr.get('{$parent_class->table}[prix]').setValue(ATF.formatNumeric(prix));
						
						if(Ext.getCmp('devis[cout_total_financement]').value){
							var prix_vente = parseFloat(Ext.getCmp('devis[cout_total_financement]').value.replace(" ", "")); 
							var pa = parseFloat(Ext.getCmp('devis[prix_achat]').value.replace(" ", ""));
							
							var mb = (1 - (pa/prix_vente)) * 100;	
							Ext.getCmp('devis[marge_financement]').setValue(ATF.formatNumeric(parseFloat(mb)));
						}
						
						{*
							var total = 0;
							
							if(Ext.getCmp('devis[duree_financement]').value 
							&& Ext.getCmp('devis[marge_financement]').value 
							&& Ext.getCmp('devis[prix]').value){
								var duree = Ext.getCmp('devis[duree_financement]').value;
								var margeFin = Ext.getCmp('devis[marge_financement]').value;
								var prixFin = prix;	
								if(Ext.getCmp('devis[maintenance_financement]').value){
									maintenance = Ext.getCmp('devis[maintenance_financement]').value  + parseFloat(maintenance) ;
								}
								
								total = parseFloat(((prix * (margeFin/100)  ) + parseFloat(prixFin))/ duree) ;
							}
						
							Ext.getCmp('devis[financement_mois]').setValue(ATF.formatNumeric(parseFloat(total)));
						*}
						
						
					} 
				});
			{else}
				frais_de_port=parseFloat(Ext.ComponentMgr.get('{$parent_class->table}[frais_de_port]').getValue());
				prix+=sous_total+frais_de_port;
				Ext.ComponentMgr.get('{$parent_class->table}[frais_de_port]').setValue(ATF.formatNumeric(frais_de_port)); 
				Ext.ComponentMgr.get('{$parent_class->table}[prix]').setValue(ATF.formatNumeric(prix));
			{/if}
			Ext.ComponentMgr.get('{$parent_class->table}[sous_total]').setValue(ATF.formatNumeric(sous_total));
			Ext.ComponentMgr.get('{$parent_class->table}[marge]').setValue(ATF.formatNumeric(parseFloat((sous_total-prix_achat)/sous_total)*100));
			Ext.ComponentMgr.get('{$parent_class->table}[marge_absolue]').setValue(ATF.formatNumeric(parseFloat(sous_total-prix_achat)));
		},
		'render': function(component) {
			{if $current_class->table == "devis_ligne"}
				val = Ext.getCmp("combodevis[type_devis]").value;

				Ext.getCmp("panel_financement").show();			
				Ext.getCmp("panel_total").show();
				Ext.getCmp("panel_courriel").show();
				Ext.getCmp("panel_redaction").show();
				Ext.getCmp("panel_lignes_consommable").hide();

				if(val == "normal"){
					Ext.getCmp("panel_location").collapse();
					Ext.getCmp("panel_location").hide();
				}else if(val == "location"){
					Ext.getCmp("panel_location").expand();
					Ext.getCmp("panel_location").show();
				}else if(val == "consommable"){
					Ext.getCmp("panel_location").collapse();
					Ext.getCmp("panel_location").hide();
					Ext.getCmp("panel_lignes").hide();

					Ext.getCmp("panel_financement").hide();				
					Ext.getCmp("panel_total").hide();
					Ext.getCmp("panel_courriel").hide();
					Ext.getCmp("panel_redaction").hide();
					Ext.getCmp("panel_lignes_consommable").show();
					Ext.getCmp("panel_lignes_consommable").expand();
				}	
			{/if}
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
				width:30,
				editor: new Ext.form.TextField({
				})
			}, {
				header: 'Produit',
				height:60,
				dataIndex: '{$current_class->table}__dot__produit',
				editor: jQuery.extend({include file="generic-gridpanel-combo.tpl.js" key=id_produit function=null extJSGridComboboxSeparator=true},{
					listWidth:400
				})
			}, {
				header: 'Quantité',
				width:20,
				dataIndex: '{$current_class->table}__dot__quantite',
				renderer: 'money',
				editor: new Ext.form.TextField({
					value:0
				})
			{if $parent_class->table==devis}
				}, {
					header: 'Poids (Kg)',
					width:20,
					dataIndex: '{$current_class->table}__dot__poids',
					renderer: 'money',
					editor: new Ext.form.TextField({
						value:0
					})
			{/if}
			}, {
				header: 'Prix',
				width:20,
				dataIndex: '{$current_class->table}__dot__prix',
				renderer: 'money',
				editor: new Ext.form.TextField({
					value:0
				})
			{if $parent_class->table==devis}
			},{
				header: 'Periode',
				width:40,
				dataIndex: 'devis_ligne__dot__periode',	
				renderer: function (value, metaData, record, rowIndex, colIndex, store){
					if (value) {						
						var a = value.split(ATF.extJSGridComboboxSeparator);
						if (a[1]) {
							record.set('{$current_class->table}__dot__periode',a[1]);
						}
						return a[0];
					}
				},					
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
					        data: [["", "Aucun"], ["mois", 'Mois'],["trimestre", 'Trimestre'],["an", 'Année']]
					    }),
					    valueField: 'id',
					    displayField: 'value'
					})

			{/if}	
			}, {
				header: 'Px Achat',
				width:20,
				dataIndex: '{$current_class->table}__dot__prix_achat',
				renderer: 'money',
				editor: new Ext.form.TextField({
					value:0
				})
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
				editor: jQuery.extend({include file="generic-gridpanel-combo.tpl.js" key=id_fournisseur function=autocompleteFournisseurs extJSGridComboboxSeparator=true},{
					listWidth:200
				})
			}, {
				hidden:true,
				dataIndex: '{$current_class->table}__dot__id_fournisseur_fk'
			}, {
				header: 'Compte',
				width:50,
				dataIndex: '{$current_class->table}__dot__id_compte_absystech',
				renderer: function (value, metaData, record, rowIndex, colIndex, store){
					if (value) {
						var a = value.split(ATF.extJSGridComboboxSeparator);
						if (a[1]) {
							record.set('{$current_class->table}__dot__id_compte_absystech_fk',a[1]);
						}
						return a[0];
					}
				},
				editor: jQuery.extend({include file="generic-gridpanel-combo.tpl.js" key=id_compte_absystech function=null extJSGridComboboxSeparator=true},{
					listWidth:200
				})
			}, {
				hidden:true,
				dataIndex: '{$current_class->table}__dot__id_compte_absystech_fk'
			}, {
				header: 'Marge',
				width:20,
				dataIndex: '{$current_class->table}__dot__marge',
				renderer: 'decimal_pourcent',
			}, {
				header: 'Marge Absolue',
				width:30,
				dataIndex: '{$current_class->table}__dot__marge_absolue',
				renderer: 'money',
			}, {
				header: 'Visible',
				width:20,
				dataIndex: '{$current_class->table}__dot__visible',
				editor: {include file="generic-gridpanel-combo.tpl.js" key=visible value="oui" function=null}				
			}
		]
	}),
	store:new Ext.data.JsonStore({
		root: 'result',
		totalProperty: 'totalCount',
		idProperty: 'id',
		remoteSort: true,	
		fields: ATF.extParseFields({util::getExtJSGridMappingFields($q->getView(),["{$current_class->table}.id_fournisseur_fk","{$current_class->table}.id_compte_absystech_fk","{$current_class->table}.marge","{$current_class->table}.marge_absolue"])}),
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
		})
	})
})