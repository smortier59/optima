record.data.article = Ext.util.Format.stripTags(record.data.article);
$('#{$id|replace:"[":"\\\\["|replace:"]":"\\\\]"}').val(record.data.article);

var c = Ext.ComponentMgr.get('devis[articles]').getSelectionModel().getSelectedCell();
var r = Ext.ComponentMgr.get('devis[articles]').getStore().getRange(c[0],c[0]);

r[0].set('{$current_class->table}__dot__id_article_fk',record.data.id);
r[0].set('{$current_class->table}__dot__quantite',1);
r[0].set('{$current_class->table}__dot__unite',record.data.unite);
r[0].set('{$current_class->table}__dot__id_fournisseur_fk',record.data.id_fournisseur);
r[0].set('{$current_class->table}__dot__id_fournisseur',record.data.fournisseur);
r[0].set('{$current_class->table}__dot__conditionnement',record.data.conditionnement);
r[0].set('{$current_class->table}__dot__id_marge_fk',record.data.id_marge);
r[0].set('{$current_class->table}__dot__id_marge',record.data.marge);
r[0].set('{$current_class->table}__dot__prix_achat',record.data.prix_achat);
r[0].set('{$current_class->table}__dot__visible',record.data.visible);
