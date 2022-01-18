record.data.nom = Ext.util.Format.stripTags(record.data.nom);
$('#{$id|replace:"[":"\\\\["|replace:"]":"\\\\]"}').val(record.data.nom);

var c = Ext.ComponentMgr.get('devis[produits]').getSelectionModel().getSelectedCell();
var r = Ext.ComponentMgr.get('devis[produits]').getStore().getRange(c[0],c[0]);

r[0].set('{$current_class->table}__dot__prix',record.data.prix);
r[0].set('{$current_class->table}__dot__ref',record.data.ref);