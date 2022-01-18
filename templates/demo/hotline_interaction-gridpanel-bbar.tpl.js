{if $requests.id_hotline}
	,bbar:['->',{
		html:'Total temps facturé : {util::hoursToString(ATF::hotline()->getBillingTime($requests.id_hotline))} / Total temps passé : {util::hoursToString(ATF::hotline()->getTotalTime($requests.id_hotline))}'
	}]
{/if}