{strip} {* Condition : ne parcourir que les produits du pack *}
{$id_pack_produit=$requests.produit.id_pack_produit|default:$smarty.post.id_pack_produit}
{include file="generic-field-textfield.tpl.js" condition_field=id_pack_produit condition_value=$id_pack_produit}
{/strip}