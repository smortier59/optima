{if $requests.id_audit}
	{$id_societe=ATF::audit()->select($requests.id_audit , "id_societe")}
{else}
	{$id_societe=$requests.id_societe|default:$requests.mandat.id_societe}
{/if}
{include file="generic-field-textfield.tpl.js" condition_field=id_societe condition_value=$id_societe}