Ext.ComponentMgr.get('bon_de_commande[livraison_destinataire]').setValue(Ext.util.Format.stripTags(record.json[4]));
Ext.ComponentMgr.get('bon_de_commande[livraison_adresse]').setValue(Ext.util.Format.stripTags(record.json[0]));
Ext.ComponentMgr.get('bon_de_commande[livraison_cp]').setValue(Ext.util.Format.stripTags(record.json[2]));
Ext.ComponentMgr.get('bon_de_commande[livraison_ville]').setValue(Ext.util.Format.stripTags(record.json[1]));