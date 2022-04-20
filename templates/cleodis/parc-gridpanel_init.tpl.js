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