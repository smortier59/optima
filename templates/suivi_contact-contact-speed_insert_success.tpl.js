,success:function(form, action) {
	if(action.result.result){
		Ext.ComponentMgr.get('{$id}').store.reload();
		Ext.ComponentMgr.get('speed_insert{$id}').close();
	}else{
		ATF.extRefresh(action); 
	}
}