{strip}

ATF.rowEditor.setInfos=function(table,field) {
	if(field=="codename"){						
		return new Ext.form.ComboBox({
			id:table+'_'+field+'_'+Ext.id(),
			listeners:{
				change:function(f) {
					ATF.ajax(table+',setInfos.ajax','id_'+table+'='+this.gridEditor.record.data[table+'__dot__id_'+table]+'&'+field+'='+this.getValue());
				}
			}
			,typeAhead:true
			,triggerAction:'all'
			,editable:false
			,lazyRender:true
			,mode:'local'
			,preventMark:true
			,store: new Ext.data.ArrayStore({
				fields: [
					'myId',
					'displayText'
				],			
				data: [
					['', '{$item.libNull|default:"-"}'],
					['hotline', 'Hotline V2'],
					{foreach from=ATF::db()->recup_codename() item=i}
						['{$i}', '{$i|escape:javascript}']
						{if !$i@last},{/if}
					{/foreach}
					
				]
			})
			,value: ""
			,valueField: 'myId'
			,displayField: 'displayText'
			,fieldLabel: ''
		});
	}else{
		return new Ext.form.TextField({
			value: 0,
			id:table+'_'+field+'_'+Ext.id(),
			fieldLabel: '',
			listeners:{
				change:function(f) {
					ATF.ajax(table+',setInfos.ajax','id_'+table+'='+this.gridEditor.record.data[table+'__dot__id_'+table]+'&'+field+'='+this.getValue());
				}
				,specialkey: function(tf, e){
					if (e.getKey() == e.ENTER) {
						ATF.ajax(table+',setInfos.ajax','id_'+table+'='+this.gridEditor.record.data[table+'__dot__id_'+table]+'&'+field+'='+this.getValue());
					}
				}
			}
		});
	}
};

{/strip}