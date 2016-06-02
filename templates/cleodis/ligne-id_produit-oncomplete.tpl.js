
{if ATF::$codename == "exactitude"}

record.data.nom = Ext.util.Format.stripTags(record.data.nom);
$('#{$id|replace:"[":"\\\\["|replace:"]":"\\\\]"}').val(record.data.nom);

{if $current_class->controlled_by == "facture"}	
	var c = Ext.ComponentMgr.get('{$current_class->controlled_by}[prods]').getSelectionModel().getSelectedCell();
	var r = Ext.ComponentMgr.get('{$current_class->controlled_by}[prods]').getStore().getRange(c[0],c[0]);
{else}	
	var c = Ext.ComponentMgr.get('{$current_class->controlled_by}[{$produit}]').getSelectionModel().getSelectedCell();
	var r = Ext.ComponentMgr.get('{$current_class->controlled_by}[{$produit}]').getStore().getRange(c[0],c[0]);
{/if}

console.log(record);

r[0].set('{$current_class->table}__dot__ref',Ext.util.Format.stripTags(record.data.ref));
r[0].set('{$current_class->table}__dot__produit',Ext.util.Format.stripTags(record.data.produit));
r[0].set('{$current_class->table}__dot__id_produit_fk',record.data.id);
r[0].set('{$current_class->table}__dot__prix_achat',record.data.prix_achat);
r[0].set('{$current_class->table}__dot__prix',record.data.prix);


{else}
	

{if $pager=="ProduitsUpdateVisible"}
	{$produit="produits"}
{elseif $pager=="ProduitsUpdateNonVisible"}
	{$produit="produits_non_visible"}
{elseif $pager=="ProduitsUpdateRepris"}
	{$produit="produits_repris"}
{/if}


record.data.nom = Ext.util.Format.stripTags(record.data.nom);
$('#{$id|replace:"[":"\\\\["|replace:"]":"\\\\]"}').val(record.data.nom);

var c = Ext.ComponentMgr.get('{$current_class->controlled_by}[{$produit}]').getSelectionModel().getSelectedCell();
var r = Ext.ComponentMgr.get('{$current_class->controlled_by}[{$produit}]').getStore().getRange(c[0],c[0]);
r[0].set('{$current_class->table}__dot__type',Ext.util.Format.stripTags(record.data.type));
r[0].set('{$current_class->table}__dot__ref',Ext.util.Format.stripTags(record.data.ref));
r[0].set('{$current_class->table}__dot__id_fournisseur',Ext.util.Format.stripTags(record.data.fournisseur));
r[0].set('{$current_class->table}__dot__produit',Ext.util.Format.stripTags(record.data.nom));
r[0].set('{$current_class->table}__dot__prix_achat',record.data.prix_achat);
r[0].set('{$current_class->table}__dot__code',record.data.code);
r[0].set('{$current_class->table}__dot__id_produit_fk',record.data.id_produit);
r[0].set('{$current_class->table}__dot__id_fournisseur_fk',record.data.id_fournisseur);

{/if}
