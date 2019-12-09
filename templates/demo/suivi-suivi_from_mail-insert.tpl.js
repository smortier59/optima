{strip}
{	
	xtype:'combo',
	fieldLabel: '{ATF::$usr->trans($key,$current_class->table)|escape:javascript}',
	name: '{$alternateName|default:$name}[]',
	id: '{$alternateId|default:$id}',
	anchor:"98%",
	mode:'local',
	store : new Ext.data.ArrayStore({
				fields: [
					'myId',
					'displayText'
				],			
				data: [
					{foreach from=ATF::suivi_from_mail()->recup_mail_suivi() key=k item=i}
						['{$i[0]}', '{$i[1]|escape:javascript}']
						{if !$i@last},{/if}
					{/foreach}
				]
			})
	,valueField: 'myId'
	,displayField: 'displayText'
}
{/strip}