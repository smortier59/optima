record.data.nom = Ext.util.Format.stripTags(record.data.nom);
$('#{$id|replace:"[":"\\\\["|replace:"]":"\\\\]"}').val(record.data.nom);

var grid = Ext.ComponentMgr.get('bon_de_pret[produits]');
var idx = grid.getSelectionModel().getSelectedCell();
var range = Ext.ComponentMgr.get('bon_de_pret[produits]').getStore().getRange(idx[0],idx[0]);
range[0].set('{$current_class->table}__dot__ref',record.data.ref);
range[0].set('{$current_class->table}__dot__serial',record.data.serial);
range[0].set('{$current_class->table}__dot__serialAT',record.data.serialAT);
range[0].set('{$current_class->table}__dot__id_stock',record.data.id_stock);
