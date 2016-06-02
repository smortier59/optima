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
	maj: function(){ },
	majFour: function(field,id){ },	
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
					
					var idx = 0; // Numéro de ligne par défaut
					var index = grid.getSelectionModel().getSelectedCell();
					if (index) {
						idx = index[0]+1; // Numéro de ligne sélectionné
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
		listeners: {
			'afteredit': function(field, newVal, oldVal){
				Ext.ComponentMgr.get('{$id}').maj();
			}
		},
	
	{/if}
	columns: {util::getExtJSGridMappingColumns($q->getView(),$current_class)}.push({ 'dataIndex':'{$current_class->table}__dot__id_pack_produit_fk' },{ 'dataIndex':'{$current_class->table}__dot__id_{$current_class->table}' }),
	viewConfig: {
		forceFit:true
		,showPreview:true
	},	
	cm:new Ext.grid.ColumnModel({		
		columns: [
			{if !$no_update}
				{
				header: 'Catalogue',
				iconCls: 'insert-button',
				width:20,
				dataIndex: 'id',
					renderer: function() { 
						{$pack_produit="pack_produits"}
						{$key_class=ATF::getClass('pack_produit')}
						{capture assign=quickInsert}
							{strip}
								<a href="javascript:;"
									onclick="{strip}
										var c = Ext.ComponentMgr.get('{$id}').getSelectionModel().getSelectedCell();
										var r = Ext.ComponentMgr.get('{$id}').getStore().getRange(c[0],c[0]);
										if(r){
											var id_pack_produit = r[0].get('{$current_class->table}__dot__id_pack_produit_fk');
											
										}else{
											var id_pack_produit = 0;
										}
										var w=new Ext.Window({
										layout: 'fit',
										title: '{ATF::$usr->trans($key_class->name())} | {ATF::$usr->trans('speed_insert')}',
										width:1000,
										x: 100,
										y: 100,
										id: 'speed_insert{$id}',
										monitorResize:true,
										autoLoad:{ url: '{$key_class->name()},speed_insert_template.ajax,id={$id}&id_pack_produit='+id_pack_produit+'&parent_class={$current_class->table}', scripts:true }
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
				header: 'nom',
				height:60,
				dataIndex: '{$current_class->table}__dot__nom',
				renderer: function (value, metaData, record, rowIndex, colIndex, store){					
					if (value) {
						var a = value.split(ATF.extJSGridComboboxSeparator);
						if (a[1]) {
							record.set('{$current_class->table}__dot__id_pack_produit_fk',a[1]);
						}
						return a[0];
					}
				},				
				editor: jQuery.extend({include file="generic-gridpanel-combo.tpl.js" key=id_pack_produit function=null forceId="id_pack_produit{$id}" extJSGridComboboxSeparator=true},{
					listWidth:400
				})
			}, {
				hidden:true,
				dataIndex: '{$current_class->table}__dot__id_pack_produit_fk'
			}			
			,  {
				hidden:true,
				dataIndex: '{$current_class->table}__dot__id_{$current_class->table}'
			}				
				
						
				
			
		]
	}),
	store:new Ext.data.JsonStore({
		root: 'result',
		totalProperty: 'totalCount',
		idProperty: 'id',
		remoteSort: true,	
		fields: ATF.extParseFields({util::getExtJSGridMappingFields($q->getView(),["{$current_class->table}.id_pack_produit_fk","{$current_class->table}.id_{$current_class->table}"])}),
		{if $function}baseParams:{ 'function':'{$function}' },{/if}
		proxy: new Ext.data.HttpProxy({
			url: '{if $proxyUrl}{$proxyUrl}{else}{$current_class->table},extJSgsa.ajax,pager={$pager}{/if}'
			,method:'POST'
		})
		,listeners:{
			load:function(field, newVal, oldVal){ ATF.{$current_class->table}{$pager}__id_pack_produit=Array();}
		}
	})
})