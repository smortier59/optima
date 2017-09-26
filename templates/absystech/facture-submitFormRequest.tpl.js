{strip}
	if (Ext.getCmp("facture[tva]").getValue()!="1.200" && Ext.getCmp("facture[tva]").getValue()!="1.2" && Ext.getCmp("facture[tva]").getValue()!="1.20") {
		var r = confirm("La valeur de la TVA n'est pas à jour, êtes vous sûr ?");
		if (r) {
			{include file="generic-submitFormRequest.tpl.js"}
		}
	} else {
		{include file="generic-submitFormRequest.tpl.js"}
	}
{/strip}

