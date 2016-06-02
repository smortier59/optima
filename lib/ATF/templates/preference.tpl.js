if(ATF.currentWindow){
	ATF.currentWindow.close();
}
{include file="generic-update.tpl.js" formName=formulaire colspan=1 labelWidth=250 notitle=1}
ATF.basicInfo = new Ext.FormPanel(formulaire);
ATF.unsetFormIsActive();
ATF.currentWindow = new Ext.Window({
	title: '{ATF::$usr->trans("modifPreference")}',
	id:'mywindow',
	width: 700,
	height: 500,
	buttonAlign:'center',
	autoScroll:true,
	closable:true,
	items: ATF.basicInfo
}).show(); 