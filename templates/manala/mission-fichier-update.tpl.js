{$pager="{$current_class->table}PersonnelUpdate"}
{$parent_class=$current_class}
{$current_class=ATF::getClass("{$current_class->table}_ged")}
{$q=ATF::_s(pager)->getAndPrepare($pager)}
{$fields=[
	"{$current_class->table}.id_mission_ged"
]}
{if ATF::_r(id_mission)}
	{$params = ["id_mission"=>classes::decryptId(ATF::_r(id_mission))]}
{/if}


{	
	xtype: 'panel',
	id: 'panelForChosenDocument',
	items: [{
		{if $selected=ATF::societe()->getGed($params)}
			items: [{
				xtype: "checkboxgroup",
				fieldLabel : "Documents disponibles : ",
				id: 'chosenDocument',
				items: [
					{foreach $selected as $k=>$i}
						{
							boxLabel: "{$i.boxLabel}",
							inputValue: "{$i.inputValue}",
							name: "{$i.name}",
							checked: "{$i.checked}"
						}
						{if !$i@last},{/if}
					{/foreach}
				]
			}]
		{else}
			html: "Choisissez une société pour voir la liste des documents disponibles"
		{/if}

	}]
}
