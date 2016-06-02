{strip}
{foreach from=ATF::privilege()->privilege key=cle_priv_titre item=info_priv_titre}	
	{
		xtype: 'compositefield',
		hideLabel:true,
		msgTarget: 'under',
		items: [ 
				{ xtype: 'checkbox', name:'privilege[{$info_priv_titre.id_privilege}]', hideLabel: true, width:40 {if $requests.module.privilege["{$info_priv_titre.id_privilege}"] || !$requests.module.privilege}, checked:true {/if} }
				,{ xtype: 'displayfield', value: '{ATF::$usr->trans($info_priv_titre.privilege,privilege)}', width:200 }
		 ]
	}	
	{if !$info_priv_titre@last},{/if}
{/foreach}
{/strip}