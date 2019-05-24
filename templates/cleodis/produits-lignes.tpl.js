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
		var prix_achat = 0;
		var marge = 0;
		var marge_absolue = 0;
		var prix = 0;

		var records = Ext.ComponentMgr.get('{$parent_class->table}[produits]').store.getRange();
		if (records) {
			for (var i = 0; i < Ext.ComponentMgr.get('{$parent_class->table}[produits]').store.getRange().length; i++) {
				prix_achat+=records[i].data.{$current_class->table}__dot__prix_achat*records[i].data.{$current_class->table}__dot__quantite;
			}
		}

		var pnv = Ext.ComponentMgr.get('{$parent_class->table}[produits_non_visible]');
		if (pnv) {
			var records_parent = pnv.store.getRange();
			for (var i = 0; i < pnv.store.getRange().length; i++) {
				prix_achat+=records_parent[i].data.{$current_class->table}__dot__prix_achat*records_parent[i].data.{$current_class->table}__dot__quantite;
			}
		}

		var pr = Ext.ComponentMgr.get('{$parent_class->table}[produits_repris]');
		if (pr) {
			var records_parent = pr.store.getRange();
			for (var i = 0; i < pr.store.getRange().length; i++) {
				prix_achat+=records_parent[i].data.{$current_class->table}__dot__prix_achat*records_parent[i].data.{$current_class->table}__dot__quantite;
			}
		}

		if(Ext.ComponentMgr.get('{$parent_class->table}[prix_achat]')){
			Ext.ComponentMgr.get('{$parent_class->table}[prix_achat]').setValue(ATF.formatNumeric(prix_achat));
		}


		if(Ext.ComponentMgr.get('{$parent_class->table}[marge]')){
			Ext.ComponentMgr.get('{$parent_class->table}[marge]').setValue(ATF.formatNumeric(parseFloat((Ext.ComponentMgr.get('{$parent_class->table}[prix]').getValue().replace(' ','')*1-prix_achat)/Ext.ComponentMgr.get('{$parent_class->table}[prix]').getValue().replace(' ','')*1)*100));
		}

		if(Ext.ComponentMgr.get('{$parent_class->table}[marge_absolue]')){
			Ext.ComponentMgr.get('{$parent_class->table}[marge_absolue]').setValue(ATF.formatNumeric(parseFloat(Ext.ComponentMgr.get('{$parent_class->table}[prix]').getValue().replace(' ','')*1-prix_achat)));
		}
	},
	majFour: function(field,id){
		var prix = 0;

		var records = Ext.ComponentMgr.get('{$parent_class->table}[produits]').store.getRange();
		if (records) {
			for (var i = 0; i < Ext.ComponentMgr.get('{$parent_class->table}[produits]').store.getRange().length; i++) {
				prix+=records[i].data.{$current_class->table}__dot__prix*records[i].data.{$current_class->table}__dot__quantite;
			}
		}

		if(Ext.ComponentMgr.get('{$parent_class->table}[prix]')){
			Ext.ComponentMgr.get('{$parent_class->table}[prix]').setValue(ATF.formatNumeric(prix));
		}

	},
	fourniRepris: function(field,id){
		var pr = Ext.ComponentMgr.get('{$parent_class->table}[produits_repris]');
		if (pr) {
			var records_parent = pr.store.getRange();
			for (var i = 1; i < pr.store.getRange().length; i++) {
				records_parent[i].set('{$current_class->table}__dot__id_fournisseur',field);
				records_parent[i].set('{$current_class->table}__dot__id_fournisseur_fk',id);
			}
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
		{if !$repris}
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
					records[idx].set('{$current_class->table}__dot__visibilite_prix','invisible');
					records[idx].set('{$current_class->table}__dot__neuf','oui');
				}
		{else}
			}, '-', {
				text: '{ATF::$usr->trans(import)|escape:javascript}',
				iconCls: 'import-button',
				handler: function(btn, ev) {
					var grid = Ext.ComponentMgr.get('{$id}');
					var store = grid.getStore();

					/*On vide le grid*/
					store.removeAll();

					if(Ext.ComponentMgr.get('panel_avenant_lignes').collapsed==false){
						var tree = Ext.ComponentMgr.get('avenant_tree');
					}else if(Ext.ComponentMgr.get('panel_AR').collapsed==false){
						var tree = Ext.ComponentMgr.get('AR_tree');
					}else if(Ext.ComponentMgr.get('panel_vente').collapsed==false){
						var tree = Ext.ComponentMgr.get('vente_tree');
					}

					var checked=tree.getChecked();
					for (var i = 0; i < checked.length; i++) {
						if(checked[i].attributes.id_produit_fk){

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
							records[idx].set('{$current_class->table}__dot__visibilite_prix','invisible');
							records[idx].set('{$current_class->table}__dot__neuf','non');
							records[idx].set('{$current_class->table}__dot__type',checked[i].attributes.type);
							records[idx].set('{$current_class->table}__dot__id_produit_fk',checked[i].attributes.id_produit_fk);
							records[idx].set('{$current_class->table}__dot__serial',checked[i].attributes.serial);
							records[idx].set('{$current_class->table}__dot__produit',checked[i].attributes.produit);
							records[idx].set('{$current_class->table}__dot__quantite',checked[i].attributes.quantite);
							records[idx].set('{$current_class->table}__dot__ref',checked[i].attributes.ref);
							records[idx].set('{$current_class->table}__dot__id_parc',checked[i].attributes.id_parc);
							records[idx].set('{$current_class->table}__dot__id_affaire_provenance',checked[i].attributes.id_affaire_provenance);
						}
					}
					grid.refreshHiddenValues();
					Ext.ComponentMgr.get('{$id}').maj();
				}
		{/if}
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
		listeners: {
			'afteredit': function(field, newVal, oldVal){
				Ext.ComponentMgr.get('{$id}').maj();
			}
		},
	{else if $current_class->table==facture_fournisseur_ligne  ||  $current_class->table==parc}
		tbar: [{
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
				Ext.ComponentMgr.get('{$id}').majFour();
			}
		}],
	{/if}
	columns: {util::getExtJSGridMappingColumns($q->getView(),$current_class)}.push({ 'dataIndex':'{$current_class->table}__dot__id_produit_fk' },{ 'dataIndex':'{$current_class->table}__dot__id_fournisseur_fk' },{ 'dataIndex':'{$current_class->table}__dot__id_{$current_class->table}' }),
	viewConfig: {
		forceFit:true
		,showPreview:true
	},
	listeners: {
		'afteredit': function(field, newVal, oldVal){
			{if $current_class->table!=facture_fournisseur_ligne}
				Ext.ComponentMgr.get('{$id}').maj();
			{/if}
		}
	},
	cm:new Ext.grid.ColumnModel({
		/*defaults: {
			sortable: true
		},*/
		columns: [
			{if !$no_update && !$repris}
				{
				header: 'Catalogue',
				iconCls: 'insert-button',
				width:20,
				dataIndex: 'id',
					renderer: function() {
						{$produit="produits"}
						{$key_class=ATF::getClass('produit')}
						{capture assign=quickInsert}
							{strip}
								<a href="javascript:;"
									onclick="{strip}
										var c = Ext.ComponentMgr.get('{$id}').getSelectionModel().getSelectedCell();
										var r = Ext.ComponentMgr.get('{$id}').getStore().getRange(c[0],c[0]);
										if(r){
											var id_produit = r[0].get('{$current_class->table}__dot__id_produit_fk');

										}else{
											var id_produit = 0;
										}
										var w=new Ext.Window({
										layout: 'fit',
										title: '{ATF::$usr->trans($key_class->name())} | {ATF::$usr->trans('speed_insert')}',
										width:1000,
										x: 100,
										y: 100,
										id: 'speed_insert{$id}',
										monitorResize:true,
										autoLoad:{ url: '{$key_class->name()},speed_insert_template.ajax,id={$id}&id_produit='+id_produit+'&parent_class={$current_class->table}', scripts:true }
									}).show();return false;{/strip}" >
									<img class="png" height="16" width="16" alt="" src="{ATF::$staticserver}images/icones/insert.png"/>
								</a>
							{/strip}
						{/capture}
						return '{$quickInsert|escape:javascript}';
					}
				},
			{/if}
			{
				header: 'Produit',
				height:60,
				dataIndex: '{$current_class->table}__dot__produit',
				renderer: function (value, metaData, record, rowIndex, colIndex, store){
					if (value) {
						var a = value.split(ATF.extJSGridComboboxSeparator);
						if (a[1]) {
							record.set('{$current_class->table}__dot__id_produit_fk',a[1]);
						}
						return a[0];
					}
				},
				{if !$no_update && !$repris}
					editor: jQuery.extend({include file="generic-gridpanel-combo.tpl.js" key=id_produit function=null forceId="id_produit{$id}" extJSGridComboboxSeparator=true},{
						listWidth:400
					})
				{/if}
			}, {
				hidden:true,
				dataIndex: '{$current_class->table}__dot__serial'
			}, {
				hidden:true,
				dataIndex: '{$current_class->table}__dot__id_produit_fk'
			}, {
				header: 'Quantité',
				width:20,
				dataIndex: '{$current_class->table}__dot__quantite',
				renderer: 'money',
				{if !$no_update && !$repris}
					editor: new Ext.form.TextField({
						value:0
					})
				{/if}
			}
			{if $current_class->table=="pack_produit_ligne"}
				, {
					header: 'Produit principal ?',
					width:20,
					dataIndex: '{$current_class->table}__dot__principal',
					{if !$no_update}
						editor: {include file="generic-gridpanel-combo.tpl.js" key=principal value="non" function=null}
					{/if}
				}
			{/if}
			{if $current_class->table=="pack_produit_ligne"}
				, {
					header: 'Min',
					width:20,
					dataIndex: '{$current_class->table}__dot__min',
					editor: new Ext.form.TextField({
						value:0
					})
				}
			{/if}
			{if $current_class->table=="pack_produit_ligne"}
				, {
					header: 'Max',
					width:20,
					dataIndex: '{$current_class->table}__dot__max',
					editor: new Ext.form.TextField({
						value:0
					})
				}
			{/if}
			{if $current_class->table=="pack_produit_ligne"}
				, {
					header: 'Option Incluse',
					width:20,
					dataIndex: '{$current_class->table}__dot__option_incluse',
					{if !$no_update}
						editor: {include file="generic-gridpanel-combo.tpl.js" key=option_incluse value="non" function=null}
					{/if}
				}

				, {
					header: 'Option Incluse Obligatoire',
					width:20,
					dataIndex: '{$current_class->table}__dot__option_incluse_obligatoire',
					{if !$no_update}
						editor: {include file="generic-gridpanel-combo.tpl.js" key=option_incluse_obligatoire value="oui" function=null}
					{/if}
				}

				,{
					header: 'Afficher sur PDF',
					width:20,
					dataIndex: '{$current_class->table}__dot__visible_sur_pdf',
					{if !$no_update}
						editor: {include file="generic-gridpanel-combo.tpl.js" key=visible_sur_pdf value="oui" function=null}
					{/if}
				}

			{/if}



			{if $current_class->table=="devis_ligne" && ATF::$codename != "exactitude"}
				, {
					header: 'Type',
					width:20,
					dataIndex: '{$current_class->table}__dot__type',
				}
			{/if}
			{if $current_class->table=="pack_produit_ligne"}
				, {
					header: 'Ordre',
					width:20,
					dataIndex: '{$current_class->table}__dot__ordre',
					editor: new Ext.form.TextField({
						value:0
					})
				}
			{/if}
			, {
				header: 'Référence',
				dataIndex: '{$current_class->table}__dot__ref',
				width:30,
			{if ATF::$codename == "exactitude"}
				}, {
					header: 'Prix',
					width:20,
					dataIndex: '{$current_class->table}__dot__prix',
					renderer: 'money',
					editor: new Ext.form.TextField({ })
				}, {
					header: 'Commentaire produit',
					width:50,
					dataIndex: '{$current_class->table}__dot__commentaire',
					editor: new Ext.form.TextField({
						value:""
					})
				}
			{else}
				{if $current_class->table==facture_fournisseur_ligne ||  $current_class->table==parc}
					}, {
						header: 'Prix',
						width:20,
						dataIndex: '{$current_class->table}__dot__prix',
						renderer: 'money',
						}, {
						header: 'Serial',
						width:20,
						dataIndex: '{$current_class->table}__dot__serial',
						{if $current_class->table==parc}
							editor: new Ext.form.TextField({
								value:0
							})
						{/if}
					}
				{else}
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
								{if $id=='devis[produits_repris]'}
									if(rowIndex===0){
										Ext.ComponentMgr.get('{$id}').fourniRepris(a[0],a[1]);
									}
								{/if}
								return a[0];
							}
						},
						{if !$no_update}
							editor: jQuery.extend({include file="generic-gridpanel-combo.tpl.js" key=id_fournisseur function=autocompleteFournisseurs  forceId="id_fournisseur{$id}" extJSGridComboboxSeparator=true},{
								listWidth:200
							})
						{/if}
					}, {
						hidden:true,
						dataIndex: '{$current_class->table}__dot__id_fournisseur_fk'
					}, {
						hidden:true,
						dataIndex: '{$current_class->table}__dot__id_{$current_class->table}'
					}, {
						header: 'Px Achat',
						width:20,
						dataIndex: '{$current_class->table}__dot__prix_achat',
						renderer: 'money',
						{if !$no_update  || $id=='commande[produits_repris]'}
							editor: new Ext.form.TextField({
								value:0
							})
						{/if}
					{if $current_class->table =='facture_ligne' && ($id == "facture[produits_repris]" || $id== "facture[produits]")}
						}, {
							header: 'Afficher sur le pdf',
							width:20,
							dataIndex: '{$current_class->table}__dot__afficher',
							editor: {include file="generic-gridpanel-combo.tpl.js" key=afficher function=null}

					{/if}
					{if $current_class->table=='devis_ligne' || $current_class->table=='pack_produit_ligne'}
						}, {
							header: 'Visibilité Prix',
							width:20,
							dataIndex: '{$current_class->table}__dot__visibilite_prix',
							{if !$no_update}
								editor: {include file="generic-gridpanel-combo.tpl.js" key=visibilite_prix value="oui" function=null}
							{/if}
					{/if}

					{if $current_class->table=='devis_ligne' && !$repris}
						}, {
							header: 'Neuf',
							width:20,
							dataIndex: '{$current_class->table}__dot__neuf',
							{if !$no_update}
								editor: {include file="generic-gridpanel-combo.tpl.js" key=neuf function=null}
							{/if}
						}, {
							header: 'Ref SIMAG',
							width:20,
							dataIndex: '{$current_class->table}__dot__ref_simag',
							editor: new Ext.form.TextField({
								value:""
							})
					{/if}

					{if $repris}
						}, {
							hidden:true,
							dataIndex: '{$current_class->table}__dot__id_parc'
						}, {
							hidden:true,
							dataIndex: '{$current_class->table}__dot__id_affaire_provenance'
					{/if}
					}
					{if $current_class->table=="devis_ligne" || $current_class->table=="commande_ligne"}
						, {
							header: 'Commentaire produit',
							width:50,
							dataIndex: '{$current_class->table}__dot__commentaire',
							editor: new Ext.form.TextField({
								value:""
							})
						}
					{/if}

					{if $pager=="ProduitsUpdateOptionPartenaire"}
						, {
							header: 'Partenaire',
							dataIndex: '{$current_class->table}__dot__id_partenaire',
							renderer: function (value, metaData, record, rowIndex, colIndex, store){
								if (value) {
									var a = value.split(ATF.extJSGridComboboxSeparator);
									if (a[1]) {
										record.set('{$current_class->table}__dot__id_partenaire_fk',a[1]);
									}
									return a[0];
								}
							},
							editor: jQuery.extend({include file="generic-gridpanel-combo.tpl.js" key=id_partenaire function=null  forceId="id_partenaire{$id}" extJSGridComboboxSeparator=true},{
									listWidth:200
							})
						}
						,{
							hidden:true,
							dataIndex: '{$current_class->table}__dot__id_partenaire_fk'
						}
					{/if}
				{/if}
			{/if}
		]
	}),
	store:new Ext.data.JsonStore({
		root: 'result',
		totalProperty: 'totalCount',
		idProperty: 'id',
		remoteSort: true,
		fields: ATF.extParseFields({util::getExtJSGridMappingFields($q->getView(),["{$current_class->table}.id_fournisseur_fk","{$current_class->table}.id_produit_fk","{$current_class->table}.id_{$current_class->table}"])}),
		{if $function}baseParams:{ 'function':'{$function}' },{/if}
		proxy: new Ext.data.HttpProxy({
			url: '{if $proxyUrl}{$proxyUrl}{else}{$current_class->table},extJSgsa.ajax,pager={$pager}{/if}'
			,method:'POST'
		})
		,listeners:{
			load:function(field, newVal, oldVal){ {if $calcul_prix} Ext.ComponentMgr.get('{$id}').maj(); {/if} ATF.{$current_class->table}{$pager}__id_produit=Array();}
		}
	})
})