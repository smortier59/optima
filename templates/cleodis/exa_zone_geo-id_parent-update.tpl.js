{strip}
	{$id_societe = ATF::exa_zone_geo()->select(ATF::exa_zone_geo()->decryptId(ATF::_r('id_exa_zone_geo')), "id_societe")}

	{include file="generic-field-textfield.tpl.js" condition_field="exa_zone_geo.id_societe" condition_value=$id_societe function="autocompleteAuditParent" surpasseCount=true}
{/strip}