{if $requests.id_affaire || $requests.devis.id_affaire}
	{$id_affaire=$requests.id_affaire|default:$requests.devis.id_affaire}
{/if}
Ext.getCmp('vente_tree').loader.dataUrl = 'societe,getParcVente.ajax,id_societe='+record.data.id{if $id_affaire}+'&id_affaire={$id_affaire}'{/if};
Ext.getCmp('vente_tree').root.reload();