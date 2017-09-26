record.data.article = Ext.util.Format.stripTags(record.data.article);
$('#{$id|replace:"[":"\\\\["|replace:"]":"\\\\]"}').val(record.data.article);

var c = Ext.ComponentMgr.get('produit[articles]').getSelectionModel().getSelectedCell();
var r = Ext.ComponentMgr.get('produit[articles]').getStore().getRange(c[0],c[0]);

r[0].set('{$current_class->table}__dot__id_article_fk',record.data.id);
r[0].set('{$current_class->table}__dot__article',record.data.article);