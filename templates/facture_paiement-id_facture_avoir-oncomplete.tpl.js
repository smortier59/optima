var test=record.data.id;
ATF.log(record);
/* Ajustement du montant */

if (record.data.detail) {

	Ext.ComponentMgr.get('facture_paiement[montant]').setValue(Math.abs(record.json.raw_3));

}
