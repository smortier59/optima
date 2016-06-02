{strip} {* Condition : ne parcourir que les contacts de la société et mettre le contact signataire par défaut *}
	{$id_affaire=ATF::_r('id_affaire')|default:$item.affaire.id_affaire|default:$requests.affaire.id_affaire|default:$requests.demande_refi.id_affaire}
	{$id_contact=ATF::devis()->contactByDevis($id_affaire)}
	{$id_societe=ATF::affaire()->select($id_affaire,"id_societe")}
	{if $id_contact}
		{include file="generic-field-textfield.tpl.js" condition_field=id_societe condition_value=$id_societe value=$id_contact}
	{else}
		{$id_contact_signataire=ATF::societe()->select($id_societe,"id_contact_signataire")}
		{if $id_contact_signataire}
			{include file="generic-field-textfield.tpl.js" condition_field=id_societe condition_value=$id_societe value=$id_contact_signataire}
		{else}
			{include file="generic-field-textfield.tpl.js" condition_field=id_societe condition_value=$id_societe}
		{/if}
	{/if}
{/strip}