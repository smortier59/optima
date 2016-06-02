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
	majFour: function(field,id){
		var prix = 0;

		var records = Ext.ComponentMgr.get('facture[prods]').store.getRange();		
		if (records) {
			for (var i = 0; i < Ext.ComponentMgr.get('facture[prods]').store.getRange().length; i++) {
				if(records[i].data.{$current_class->table}__dot__prix){
					prix+=records[i].data.{$current_class->table}__dot__prix*records[i].data.{$current_class->table}__dot__quantite;
				}						
			}
		}


		if(Ext.ComponentMgr.get('facture[prix]')){
			Ext.ComponentMgr.get('facture[prix]').setValue(prix);
		}				

	},
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
			Ext.ComponentMgr.get('{$id}').majFour();
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
			Ext.ComponentMgr.get('{$id}').majFour();
	

		}
	}],
	columns: {util::getExtJSGridMappingColumns($q->getView(),$current_class)}.push(),
	viewConfig: {
		forceFit:true
		,showPreview:true
	},	
	listeners: {
		'afteredit': function(field, newVal, oldVal){	
			var grid = Ext.ComponentMgr.get('{$id}');
			var store = grid.getStore();		
			grid.refreshHiddenValues();
			Ext.ComponentMgr.get('{$id}').majFour();
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
										autoLoad:{ url: 'produit,speed_insert_template.ajax,id={$id}&id_produit='+id_produit+'&parent_class=facture', scripts:true }
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
				hidden:true,
				dataIndex: '{$current_class->table}__dot__id_produit_fk'
			}, {
				header: 'Quantité',
				width:20,
				dataIndex: '{$current_class->table}__dot__quantite',
				renderer: 'money',
				editor: new Ext.form.TextField({
					value:0
				})			
			}, {
				header: 'Prix',
				width:20,
				dataIndex: '{$current_class->table}__dot__prix',
				renderer: 'money',
				editor: new Ext.form.TextField({
					value:0
				})			
			}, {
				header: 'Px Achat',
				width:20,
				dataIndex: '{$current_class->table}__dot__prix_achat',
				renderer: 'money',
				editor: new Ext.form.TextField({
					value:0
				})
			}
		]
	}),
	store:new Ext.data.JsonStore({
		root: 'result',
		totalProperty: 'totalCount',
		idProperty: 'id',
		remoteSort: true,	
		fields: ATF.extParseFields({util::getExtJSGridMappingFields($q->getView(),[])}),
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