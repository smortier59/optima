$('#{$id|replace:"[":"\\\\["|replace:"]":"\\\\]"}').val(record.data.taux);

var c = Ext.ComponentMgr.get('devis[articles]').getSelectionModel().getSelectedCell();
var r = Ext.ComponentMgr.get('devis[articles]').getStore().getRange(c[0],c[0]);

r[0].set('{$current_class->table}__dot__id_marge_fk',record.data.id);
r[0].set('{$current_class->table}__dot__id_marge',record.data.taux);

console.log('{$current_class->table}__dot__id_marge',record.data);