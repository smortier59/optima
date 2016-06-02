{*
$current_class
$proxyUrl
*}
{if ATF::_r(id_devis)}
	{if $current_class->table == "commande_ligne"}

	{/if}	
{/if}



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
			
			var records = Ext.ComponentMgr.get('{$id}').store.getRange();
			var sous_total = 0;
			var poids = 0;
			var prix_achat = 0;
			var frais_de_port = 0;
			var prix = 0;
			var marge = 0;
			var marge_absolue = 0;
			var maintenance = 0;		

			
			
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
			{else if $current_class->table == "commande_ligne"}
				{if ATF::_r("id_devis")}
					{if ATF::devis()->select(ATF::_r("id_devis"), "type_devis") == "consommable"}
						Ext.getCmp("panel_maintenance").hide();
						Ext.getCmp("panel_total").hide();
						Ext.getCmp("fichier_joint").expand();
					{/if}
				{/if}

				{if ATF::_r("id_commande")}		
					{if ATF::affaire()->select( ATF::commande()->select(ATF::_r("id_commande"), "id_affaire")  , "nature") == "consommable"}
						Ext.getCmp("panel_total").hide();
						Ext.getCmp("fichier_joint").expand();
					{/if}
				{/if}
			{else if $current_class->table == "facture_ligne"}
				{if ATF::_r("id_commande")}
						Ext.getCmp("facture[date_previsionnelle]").hide();							
						Ext.getCmp("facture[affaire_sans_devis]").hide();
						Ext.getCmp("facture[date_relance]").hide();
						Ext.getCmp("facture[affaire_sans_devis_libelle]").hide();
						Ext.getCmp("facture[infosSup]").hide();
						Ext.getCmp("facture[dematerialisation]").hide();
						Ext.getCmp("combofacture[mode]").hide();
						Ext.getCmp("label_facture[id_facture_parente]").hide();
						Ext.getCmp("combofacture[periodicite]").hide();
						Ext.getCmp("facture[acompte_pourcent]").hide();
						Ext.getCmp("panel_total").hide();
				{/if}

				{if ATF::_r("id_facture")}
					Ext.getCmp("facture[date_previsionnelle]").hide();		
						Ext.getCmp("facture[date_relance]").hide();
						Ext.getCmp("facture[infosSup]").hide();
						Ext.getCmp("facture[dematerialisation]").hide();
						Ext.getCmp("combofacture[mode]").hide();
						Ext.getCmp("label_facture[id_facture_parente]").hide();
						Ext.getCmp("combofacture[periodicite]").hide();
						Ext.getCmp("panel_total").hide();
				{/if}
				

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
				editor: jQuery.extend({include file="generic-gridpanel-combo.tpl.js" key=id_produit function=autocompleteConsommable extJSGridComboboxSeparator=true},{
					listWidth:400
				})
			}, {
				header: 'Prix N&B',
				width:20,
				dataIndex: '{$current_class->table}__dot__prix_nb',
				editor: new Ext.form.TextField({
					value:0
				})				
			},{
				header: 'Prix Couleur',
				width:20,
				dataIndex: '{$current_class->table}__dot__prix_couleur',
				editor: new Ext.form.TextField({
					value:0
				})				
			},{
				header: 'Prix achat N&B',
				width:20,
				dataIndex: '{$current_class->table}__dot__prix_achat_nb',
				editor: new Ext.form.TextField({
					value:0
				})				
			},{
				header: 'Prix achat Couleur',
				width:20,
				dataIndex: '{$current_class->table}__dot__prix_achat_couleur',
				editor: new Ext.form.TextField({
					value:0
				})				
			}			
			, {
				header: 'Index N&B',
				width:20,
				dataIndex: '{$current_class->table}__dot__index_nb',
				editor: new Ext.form.TextField({
					value:0
				})			
			},{
				header: 'Index Couleur',
				width:20,
				dataIndex: '{$current_class->table}__dot__index_couleur',
				editor: new Ext.form.TextField({
					value:0
				})			
			}
			{if $current_class->table == "commande_ligne"}
			,{
				header: 'Serial',
				width:20,
				dataIndex: '{$current_class->table}__dot__serial',
				editor: new Ext.form.TextField({
					value:0
				})			
			}
			{/if}

			{if $current_class->table !== "facture_ligne"}
			,{
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
			}
			{/if}
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
		})
	})
})