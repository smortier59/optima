{strip} {* Mettre le mail à jour si contact signataire *}
{$id_societe=$requests.id_societe|default:$requests.devis.id_societe}
{$id_contact_signataire=ATF::societe()->select($id_societe,"id_contact_signataire")}
{if $id_contact_signataire}
	{$mail=ATF::contact()->select($id_contact_signataire,"email")}
	{include file="generic-field-textfield.tpl.js" value=$mail}
{else}
	{include file="generic-field-textfield.tpl.js"}
{/if}
{/strip}