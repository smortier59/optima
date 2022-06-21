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

ATF.rowEditor.setType_energie=function(table,field) {
    return new Ext.form.TextField({
        value: 0,
        id:table+'_'+field+'_'+Ext.id(),
        fieldLabel: '',
        listeners:{
            change:function(f) {
                ATF.ajax(table+',setType_energie.ajax','id_'+table+'='+this.gridEditor.record.data[table+'__dot__id_'+table]+'&type_energie='+this.getValue());
            }
            ,specialkey: function(tf, e){
                if (e.getKey() == e.ENTER) {
                    ATF.ajax(table+',setType_energie.ajax','id_'+table+'='+this.gridEditor.record.data[table+'__dot__id_'+table]+'&type_energie='+this.getValue());
                }
            }
        }
    });
};
