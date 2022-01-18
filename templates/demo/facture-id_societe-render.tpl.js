{$societe = ATF::societe()->select($requests.id_societe)}
{if $societe.id_filiale}
	{$id_filiale = $societe.id_filiale}
	Ext.getCmp('facture[id_societe]').setValue("{ATF::societe()->nom($societe.id_filiale)}");
	Ext.each(Ext.getCmp('facture[id_societe]'),function (item,idx) {
		item.setValue("{$societe.id_filiale}");
	});
	var supFields = Ext.query("[name=facture\\[id_societe\\]]");
	if (supFields) {
		Ext.each(supFields,function (item,idx) {
			item.value = "{$societe.id_filiale}";
		});
	}
{/if}