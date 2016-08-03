{$id_societe=$requests.id_societe|default:$requests.audit.id_societe}
{include file="generic-field-textfield.tpl.js" condition_field=id_societe condition_value=$id_societe}