if(record.data.civilite == "Mlle"){
	Ext.ComponentMgr.get('label_devis[id_politesse_post]').setValue("Veuillez agréer, Mademoiselle, l'expression de nos sentiments les meilleurs.");	
}else if(record.data.civilite == "Mme"){
	Ext.ComponentMgr.get('label_devis[id_politesse_post]').setValue("Veuillez agréer, Madame, l'expression de nos sentiments les meilleurs.");	
}else{
	Ext.ComponentMgr.get('label_devis[id_politesse_post]').setValue("Veuillez agréer, Monsieur, l'expression de nos sentiments les meilleurs.");
}
Ext.ComponentMgr.get('devis[email]').setValue(Ext.util.Format.stripTags(record.data.email));

