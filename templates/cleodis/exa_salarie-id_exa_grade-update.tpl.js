{strip}

{if ATF::_r("id_societe")}
	{$id_societe = ATF::societe()->decryptId(ATF::_r("id_societe"))}
{/if}
{if ATF::_r("id_exa_salarie")}
	{$id_societe =  ATF::exa_salarie()->select(ATF::exa_salarie()->decryptId(ATF::_r("id_exa_salarie")) , "id_societe")}
{/if}

{include file="generic-field-textfield.tpl.js" condition_field="exa_grade.id_societe" condition_value=$id_societe function="autocompleteListe" surpasseCount=true}

{/strip}