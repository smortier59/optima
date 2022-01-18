/**
 * @author mfleurquin
 */

{
	text: '{ATF::$usr->trans(valider_mep_button_header,hotline)|escape:javascript}',
	disabled: !{ATF::hotline()->isThereMEPTicket()},
	handler: function(btn, ev) {			
		
		ATF.basicInfo = new Ext.FormPanel({
			frame: true,
			width: 690,
			items: [{
				html: "<strong>Choisissez les tickets qui ont été passé en production : </strong>"
			}],
			listeners: {
				render: function (el) {
					ATF.loadMask.show();
					ATF.ajax(
						"hotline,getMEPTicket.ajax"
						,""		
						,{
							onComplete: function (r) {
								Ext.iterate(r.result, function(key, value) {
									var cb = {
								  		xtype: "checkbox"
								  		,boxLabel: "["+key.id_hotline+"] "+key.hotline
								  		,value: key.id_hotline
								  		,name: "th["+key.id_hotline+"]"
								  	};
								  	el.add(cb);
								});
								el.doLayout();
								ATF.loadMask.hide();
							}
						}
					);	
				}
			},
			buttons:[{
				text : "{ATF::$usr->trans(valider_mep_button_window,hotline)|escape:javascript}",
				handler : function(){
					ATF.basicInfo.getForm().submit({
						submitEmptyText:false,
						method  : 'post',
						waitMsg : '{ATF::$usr->trans(submit)|escape:javascript}',
						waitTitle : '{ATF::$usr->trans(loading)|escape:javascript}',
						url     : 'extjs.ajax',
						params: {
							'extAction':'hotline'
							,'extMethod':'massValidMEP'
						}
						,success:function(form, action) {		
							ATF.unsetFormIsActive();
							ATF.currentWindow.close();
							ATF.extRefresh(action); 
							store.reload();
						}
						,failure:function(form, action) {
							var title='Problème';
							if (action.failureType === Ext.form.Action.CONNECT_FAILURE){
								Ext.Msg.alert(title, 'Server reported:'+action.response.status+' '+action.response.statusText);
							} else if (action.failureType === Ext.form.Action.SERVER_INVALID){
								Ext.Msg.alert(title, action.result.errormsg);
							} else if (action.failureType === Ext.form.Action.CLIENT_INVALID){
								Ext.Msg.alert(title, "Un champs est mal renseigné");
							} else if (action.failureType === Ext.form.Action.LOAD_FAILURE){
								Ext.Msg.alert(title, "Un champs est mal renseigné");
							}
						}
						,timeout:3600
					});
				}
			}]
		});
		ATF.unsetFormIsActive();
		
		ATF.currentWindow = new Ext.Window({
			title: '{ATF::$usr->trans(valider_mep_title,hotline)|escape:javascript}',
			id:'mywindow',
			width: 700,
			buttonAlign:'center',
			autoScroll:false,
			closable:true,
			items: ATF.basicInfo
		}).show();
			
	}
	
}
