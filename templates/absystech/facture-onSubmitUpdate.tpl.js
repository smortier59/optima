if(Ext.ComponentMgr.get('{$current_class->table}[finale]')){
	if(Ext.ComponentMgr.get('{$current_class->table}[finale]').getValue()==true){
		if (!confirm('{ATF::$usr->trans(sur_declarer_commande_facture)|escape:javascript}')) {
			return;
		}
	}else{
		if (!confirm('{ATF::$usr->trans(sur_declarer_commande_non_facture)|escape:javascript}')) {
			return;
		}
	}
}