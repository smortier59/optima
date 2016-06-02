ATF.selectType_client = function(el,val,lastVal) {
	var val = val.json[0];

	if(val == "client"){
		Ext.getCmp("panel_infos_soc").collapse();
		Ext.getCmp("panel_infos_soc").hide();

		Ext.getCmp("panel_client").show();
		Ext.getCmp("panel_client").expand();	
	}else{
		Ext.getCmp("panel_client").collapse();
		Ext.getCmp("panel_client").hide();

		Ext.getCmp("panel_infos_soc").show();
		Ext.getCmp("panel_infos_soc").expand();	
	}	
}


ATF.loadClient = function(){
	/*
    Ext.getCmp("panel_infos_soc").collapse();
	Ext.getCmp("panel_infos_soc").hide();
	Ext.getCmp("panel_client").show();
	Ext.getCmp("panel_client").expand();
	*/

}