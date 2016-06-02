{strip}{
	xtype: 'superboxselect',
	{if $nofieldLabel}hideLabel:true,{else}fieldLabel: '{ATF::$usr->trans(missions,$current_class->table)|escape:javascript}',{/if}
	name: 'missions[]',
	width: '{$width|default:250}',
	store: [
		{ATF::mission()->q->reset()->addCondition('etat','validee')->addOrder("mission.date")->end()}
		{foreach from=ATF::mission()->options(null,null,false) key=k item=i}
			['{$k}', '{$i|escape:javascript}']
			{if !$i@last},{/if}
		{/foreach}
	]

}{/strip}