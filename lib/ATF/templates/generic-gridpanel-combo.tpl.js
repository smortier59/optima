{strip}
{*
@param string $key Clé du champ
*}
{include file="generic-field-textfield.tpl.js"
	fieldLabel=ATF::$usr->trans($key,$current_class->table)
	name="`$current_class->table`[`$key`]"
	id=$forceId|default:"`$current_class->table`[`$key`]"
	est_nul=$item["null"]
	noHiddenField=true
	renderTo=null
	item=$current_class->getColonne($key)
	always_display=true}
{/strip}