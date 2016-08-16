{$pack_produit="pack_produits"}

record.data.nom = Ext.util.Format.stripTags(record.data.nom);
$('#{$id|replace:"[":"\\\\["|replace:"]":"\\\\]"}').val(record.data.nom);

var c = Ext.ComponentMgr.get('{$current_class->controlled_by}[{$pack_produit}]').getSelectionModel().getSelectedCell();
var r = Ext.ComponentMgr.get('{$current_class->controlled_by}[{$pack_produit}]').getStore().getRange(c[0],c[0]);
r[0].set('{$current_class->table}__dot__nom',Ext.util.Format.stripTags(record.data.nom));
r[0].set('{$current_class->table}__dot__id_pack_produit_fk',record.data.id_pack_produit);

