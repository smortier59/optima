{strip}
{util::push($fieldsKeys,"societe.etat")}
ATF.meteo = {};
ATF.meteo.meteo_moyenne='{$smarty.const.__METEO_MOYENNE__}';
ATF.meteo.small='{$smarty.const.__METEO_SMALL__}';
ATF.meteo.big='{$smarty.const.__METEO_BIG__}';

{* Renderer de l état des congés avec bouton pour valider *}
ATF.renderer.tacheActions = function(table,field) {
	return function(val, meta, record, rowIndex, colIndex, store) {
		
		var idDivTacheAction = Ext.id();
		var id = record.data[table+'__dot__id_'+table];

		var btnInsert = {
			xtype:'button',
			id:"refus",
			buttonText: '',
			buttonOnly: true,
			iconCls: 'btnInsert',
			disabled: true,
			cls:'floatLeft',
			tooltip: '{ATF::$usr->trans(ATF::tache()->table,module)|escape:javascript} | Création : '+record.data[table+'__dot__'+table],
			tooltipType:'title',
			listeners: {
				'click': function(fb, v){
					ATF.basicInfo = new Ext.FormPanel(
						 {
							frame:true,
							autoHeight:true,
							bodyStyle:'padding:5px 5px 0',
							items: [
								{ 
									xtype:"compositefield"
									,hideLabel:true
									,layout: 'column'
									,cols: 1
									,items:[
										{
											xtype:"htmleditor"
											,hideLabel:true
											,name: 'tache[tache]'
											,height:200
											,width:485
										}
										,{include file="tache-concernes-insert.tpl.js" width=150 height=200 nofieldLabel=true}
									]
								}
								,{
									xtype:'compositefield'
									,fieldLabel:"{ATF::$usr->trans('horaire_fin','tache')}"
									,items:[{
											xtype:'datefield'
											,listeners :{
												change:function(){
													$('#horaire_fin'+id).val($('#horaire_fin_date'+id).val()+' '+$('#horaire_fin_time'+id).val());
												}
											}
											,format:'d-m-Y'
											,width:110
											,id: 'horaire_fin_date'+id
											,value:"{$value|default:$smarty.now|date_format:'%d-%m-%Y'}"
										},{ 
											xtype:'timefield'
											,id: 'horaire_fin_time'+id
											,format:'H:i'
											,minValue: '9:00'
											,maxValue: '18:00'
											,increment: 30
											,width:65
											,value:"{$value|default:$smarty.now|date_format:'%H:%M'}"
											,listeners :{
												change:function(){
													$('#horaire_fin'+id).val($('#horaire_fin_date'+id).val()+' '+$('#horaire_fin_time'+id).val());
												}
											}
										},{
											xtype:'textfield'
											,hidden:true
											,id:'horaire_fin'+id
											,name:'tache[horaire_fin]'
											,value:"{$value|default:$item.default|default:$smarty.now|date_format:'%d-%m-%Y %H:%M:00'}"
										}]
								}
								,{ xtype:'checkboxgroup',items: [{ boxLabel: 'Cacher la fenêtre après insertion',id:'checkHide_'+id,value:'cacher',checked: true }]}
							]
							,buttons: [
								{
									text: '{ATF::$usr->trans(insert_new_record)|escape:javascript}',
									id:'validFormButton_tache',
									handler: function(){
										ATF.basicInfo.getForm().submit({
											submitEmptyText:false,
											method  : 'post',
											waitMsg : '{ATF::$usr->trans(creating_new_element)|escape:javascript}',
											waitTitle : '{ATF::$usr->trans(loading)|escape:javascript}',
											url     : 'extjs.ajax',
											params: {
												'extAction':'tache'
												,'extMethod':'insert'
												,'tache[id_user]':'{ATF::$usr->getId()}'
												,'tache[horaire_fin]':"{$smarty.now|date_format:'%Y-%m-%d'}"
												,'tache[id_societe]':id
											}
											,success:function(form, action) {		
												if(Ext.getCmp('checkHide_'+id).checked==true){
													Ext.getCmp('mywindow'+id).hide();		
												}
												ATF.unsetFormIsActive();
												ATF.extRefresh(action); 
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
								}
							]
						}
					);

					if (!Ext.getCmp('mywindow'+id)) {
						new Ext.Window({
							title: '{ATF::$usr->trans(tache,module)|escape:javascript} | Création',
							id:'mywindow'+id,
							width: 700,
							autoHeight:true,
							plain:true,
							items: ATF.basicInfo
						});
					}
					Ext.getCmp('mywindow'+id).show();		
				}
			}
		};
		
		(function(){
			var params = {
				renderTo: idDivTacheAction,
				items:[btnInsert]
			};
			var p = new Ext.Container(params);
		}).defer(25);

		return '<div id="'+idDivTacheAction+'"></div>';
	}
};


{/strip}