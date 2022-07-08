{strip}
{* Ajout des champs pas forcément dans la vue, nécessaires pour ce renderer *}
{util::push($fieldsKeys,"parc.existence")}
{util::push($fieldsKeys,"parc.date_achat")}
{/strip}

ATF.rowEditor.setImmatriculation=function(table,field) {
    return new Ext.form.TextField({
        value: 0,
        id:table+'_'+field+'_'+Ext.id(),
        fieldLabel: '',
        listeners:{
            change:function(f) {
                ATF.ajax(table+',setImmatriculation.ajax','id_'+table+'='+this.gridEditor.record.data[table+'__dot__id_'+table]+'&immatriculation='+this.getValue());
            }
            ,specialkey: function(tf, e){
                if (e.getKey() == e.ENTER) {
                    ATF.ajax(table+',setImmatriculation.ajax','id_'+table+'='+this.gridEditor.record.data[table+'__dot__id_'+table]+'&immatriculation='+this.getValue());
                }
            }
        }
    });
};


ATF.rowEditor.setkilometrage=function(table,field) {
    return new Ext.form.TextField({
        value: 0,
        id:table+'_'+field+'_'+Ext.id(),
        fieldLabel: '',
        listeners:{
            change:function(f) {
                ATF.ajax(table+',setkilometrage.ajax','id_'+table+'='+this.gridEditor.record.data[table+'__dot__id_'+table]+'&kilometrage='+this.getValue());
            }
            ,specialkey: function(tf, e){
                if (e.getKey() == e.ENTER) {
                    ATF.ajax(table+',setkilometrage.ajax','id_'+table+'='+this.gridEditor.record.data[table+'__dot__id_'+table]+'&kilometrage='+this.getValue());
                }
            }
        }
    });
};


ATF.rowEditor.setdate_premiere_mise_en_circulation=function(table,field) {
    return new Ext.form.TextField({
        value: 0,
        id:table+'_'+field+'_'+Ext.id(),
        fieldLabel: '',
        listeners:{
            change:function(f) {
                ATF.ajax(table+',setdate_premiere_mise_en_circulation.ajax','id_'+table+'='+this.gridEditor.record.data[table+'__dot__id_'+table]+'&date_premiere_mise_en_circulation='+this.getValue());
            }
            ,specialkey: function(tf, e){
                if (e.getKey() == e.ENTER) {
                    ATF.ajax(table+',setdate_premiere_mise_en_circulation.ajax','id_'+table+'='+this.gridEditor.record.data[table+'__dot__id_'+table]+'&date_premiere_mise_en_circulation='+this.getValue());
                }
            }
        }
    });
};


ATF.rowEditor.setPuissance=function(table,field) {
    return new Ext.form.TextField({
        value: 0,
        id:table+'_'+field+'_'+Ext.id(),
        fieldLabel: '',
        listeners:{
            change:function(f) {
                ATF.ajax(table+',setPuissance.ajax','id_'+table+'='+this.gridEditor.record.data[table+'__dot__id_'+table]+'&puissance='+this.getValue());
            }
            ,specialkey: function(tf, e){
                if (e.getKey() == e.ENTER) {
                    ATF.ajax(table+',setPuissance.ajax','id_'+table+'='+this.gridEditor.record.data[table+'__dot__id_'+table]+'&puissance='+this.getValue());
                }
            }
        }
    });
};


ATF.renderer.setType_energie=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		if(record.json){ 
			var idDivUD = Ext.id();
			var id = record.data[table+'__dot__id_'+table];
			(function(){
				var params = {
					renderTo: idDivUD,
					bodyStyle:'background-color:transparent; border:0px',
					frame:false,
					border:false,
					defaults: {
						xtype:'combo'
						,width:180
						,hideLabel:true
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
								['diesel','Diesel'] , 								
								['essence','Essence'],
								['electrique','Electrique'],
								['hybride','Hybride'],

							]
						})
						,value: record.data[table+'__dot__'+field]
						,valueField: 'myId'
						,displayField: 'displayText'
					},
					items:[{
						id:table+"setType_energie"+id,
						name:"setType_energie",
						value:record.data[table+'__dot__'+field],
						listeners :{
							select:function(f,n,o){
								ATF.ajax(table+',setType_energie.ajax','id_'+table+'='+id+'&key='+field+'&value='+f.value);
								store.load();
							}
						}
					}]
				};
				var p = new Ext.FormPanel(params);
			}).defer(25);
					
			return '<div  id="'+idDivUD+'"></span>';
		}
	}
};
