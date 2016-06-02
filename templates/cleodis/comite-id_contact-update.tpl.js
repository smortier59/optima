{strip} {* Condition : ne parcourir que les contacts de la société et mettre le contact signataire par défaut *}
	{if ATF::_r('id_devis')}
		{$id_affaire=ATF::devis()->select(ATF::_r('id_devis'), "id_affaire")}
	{else}
		{$id_affaire=$item.devis.id_affaire|default:$requests.devis.id_affaire|default:$requests.comite.id_affaire}
	{/if}
	
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