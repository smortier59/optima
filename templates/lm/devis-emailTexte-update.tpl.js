{strip} {* Mettre le texte de mail à jour si société *}
{$id_societe=$requests.id_societe|default:$requests.devis.id_societe}
{if $id_societe}
	{$emailTexte=ATF::devis()->majMail($id_societe)}
	{include file="generic-field-textfield.tpl.js" value=$emailTexte}
{else}
	{include file="generic-field-textfield.tpl.js"}
{/if}
{/strip}