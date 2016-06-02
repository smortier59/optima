var fields = ['commande','affaire','contact'];
for (var x=0;x<fields.length;x++) {
	var f = Ext.getCmp('label_{$current_class->table}[id_'+fields[x]+']');
	f.clearValue();
	f.store.proxy=new Ext.data.HttpProxy({
		url: fields[x]+',autocomplete.ajax,condition_field='+fields[x]+'.{$key}&condition_value='+record.data.id
		,method:'POST'
	});
	f.store.reload();
}