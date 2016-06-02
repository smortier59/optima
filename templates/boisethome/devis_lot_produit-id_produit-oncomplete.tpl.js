record.data.produit = Ext.util.Format.stripTags(record.data.produit);
$('#{$id|replace:"[":"\\\\["|replace:"]":"\\\\]"}').val(record.data.produit);

var c = Ext.ComponentMgr.get('devis[produits]').getSelectionModel().getSelectedCell();
var r = Ext.ComponentMgr.get('devis[produits]').getStore().getRange(c[0],c[0]);

r[0].set('{$current_class->table}__dot__id_produit_fk',record.data.id_produit);
r[0].set('{$current_class->table}__dot__quantite',1);
r[0].set('{$current_class->table}__dot__unite',record.data.unite);
r[0].set('{$current_class->table}__dot__description',record.data.description);
r[0].set('{$current_class->table}__dot__lambda',record.data.lambda);
r[0].set('{$current_class->table}__dot__prix',record.data.prix);
r[0].set('{$current_class->table}__dot__prix_achat',record.data.prix_achat);

loadArticles(record.data.id_produit);