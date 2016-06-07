{strip} {* Condition : ne parcourir que les contacts de la société et mettre le contact signataire par défaut *}
{if ATF::_r(id_commande)}
	{$id_affaire=ATF::commande()->select(ATF::_r(id_commande),"id_affaire")}
{else if ATF::_r(id_affaire)}
	{$id_affaire=ATF::_r(id_affaire)}
{/if}
{include file="generic-field-textfield.tpl.js"}

{/strip}