/* 
	Renderer pour la modification d'un texte.
	@author Morgan FLEURQUIBN <mfleurquin@absystech.fr> 
*/
ATF.rowEditor.setInfos=function(table,field) {
	if(   (field=="achat")
		||(field=="fourniture")
		||(field=="frais_generaux")
	){	
		return new Ext.form.ComboBox({				
			id:table+'_'+field+'_'+Ext.id(),
			listeners:{
				change:function(f) {
					ATF.ajax(table+',setInfos.ajax','id_'+table+'='+this.gridEditor.record.data[table+'__dot__id_'+table]+'&field='+field+'&value='+this.getValue());
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
					['oui','Oui'] ,								
					['non','Non']
				]
			})
			,value: ""
			,valueField: 'myId'
			,displayField: 'displayText'
			,fieldLabel: ''
		});
	}else if(field=="statut"){	
		return new Ext.form.ComboBox({				
			id:table+'_'+field+'_'+Ext.id(),
			listeners:{
				change:function(f) {
					ATF.ajax(table+',setInfos.ajax','id_'+table+'='+this.gridEditor.record.data[table+'__dot__id_'+table]+'&field='+field+'&value='+this.getValue());
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
					['attente','En attente'] ,								
					['paye','Payé'] ,
					['litige','Litige'] , 
					['ok_paye_pour','Payé pour']
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
					ATF.ajax(table+',setInfos.ajax','id_'+table+'='+this.gridEditor.record.data[table+'__dot__id_'+table]+'&field='+field+'&value='+this.getValue());
				}
				,specialkey: function(tf, e){
					if (e.getKey() == e.ENTER) {
						ATF.ajax(table+',setInfos.ajax','id_'+table+'='+this.gridEditor.record.data[table+'__dot__id_'+table]+'&field='+field+'&value='+this.getValue());
					}
				}
			}
		});
	}

};