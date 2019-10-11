{ 
	html:'	{strip}<div class="ficheTitle" title="{$current_class->nom($id)|escape:javascript}">
				{$current_class->nom($id)|truncate:50|escape:javascript|php:"strip_tags"|html_entity_decode:$smarty.const.ENT_QUOTES:"UTF-8"} ({$id})
                {$credits = ATF::societe()->getSolde($infos.id_societe)}
                <span class="label {if $credits>=0}label-default{else}label-danger{/if} labelCtn" style="float: right; font-size: 11px">Solde: {$credits}</span>
			</div>
			<div>
				<small class="pull-right muted" style="font-size:90%">
					{ATF::$usr->trans("creer")}
					{if $infos["date"]}
						&nbsp;
                        {ATF::$usr->trans("le")}
                        &nbsp;
                        {ATF::$usr->date_trans($infos["date"],true,true,true)|escape:javascript}
                    {/if}
                    {if $infos["id_contact"]}
                        &nbsp;
                        {ATF::$usr->trans("par")}
                        &nbsp;
                        {ATF::contact()->nom($infos["id_contact"])|escape:javascript}
                	{/if} 
                	{if $infos["id_user"] && $infos["date_debut"]}
						<br>
                        {ATF::$usr->trans("prise_charge","hotline")|strtolower|ucfirst}
                        &nbsp;
                        {ATF::user()->nom($infos["id_user"])|escape:javascript}
                        &nbsp;
                        {ATF::$usr->trans("le")}
                        &nbsp;
                        {ATF::$usr->date_trans($infos["date_debut"],true,true,true)|escape:javascript}
                	{/if}

                </small>
            </div>
            {/strip}',
	style: {
		float: 'left'
	} 
}