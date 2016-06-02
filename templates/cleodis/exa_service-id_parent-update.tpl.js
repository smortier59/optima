{strip}
	{$id_societe = ATF::exa_service()->select(ATF::exa_service()->decryptId(ATF::_r('id_exa_service')), "id_societe")}

	{include file="generic-field-textfield.tpl.js" condition_field="exa_service.id_societe" condition_value=$id_societe function="autocompleteAuditParent" surpasseCount=true}
{/strip}