{if $infos.wait_mep=="oui"}
	{
		html:'<div class="label label-wait_mep">{ATF::$usr->trans(wait_mep,$current_class->table)|escape:javascript}</div>',
		cls: "labelCtn"
	}
{else} 
	{
		html:'<div class="label label-{$infos.etat}">{ATF::$usr->trans($infos.etat,$current_class->table)|escape:javascript}</div>',
		cls: "labelCtn"
	}
{/if}