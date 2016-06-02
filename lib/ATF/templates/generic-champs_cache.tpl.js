{if ATF::_g("id_{$current_class->name()}")}
	,{
		xtype:'hidden'
		,name: '{$current_class->table}[id_{$current_class->table}]'
		,value:'{ATF::_g("id_{$current_class->name()}")}'
	}
{elseif $requests["{$current_class->name()}"]["id_{$current_class->name()}"]}
	,{
		xtype:'hidden'
		,name: '{$current_class->table}[id_{$current_class->table}]'
		,value:'{$requests["{$current_class->name()}"]["id_{$current_class->name()}"]}'
	}
{/if}

{* si l on provient d un module étranger (système d onglet) *}
{$champs=$current_class->recupChampsCache(ATF::_r(),$current_class->table,$event)}
{foreach from=$champs key=field_name item=valeur}
	,{
		xtype:'hidden'
		,name: '{$current_class->table}[{$field_name}]'
		,value:'{$valeur}'
	}
{/foreach}