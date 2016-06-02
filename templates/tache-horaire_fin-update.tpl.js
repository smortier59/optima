{strip}{
	xtype:'compositefield'
	,fieldLabel:"{ATF::$usr->trans('horaire_fin','tache')}"
	,items:[{
			xtype:'datefield'
			,listeners :{
				change:function(){
					$('#horaire_fin').val($('#horaire_fin_date').val()+' '+$('#horaire_fin_time').val());
				}
			}
			,format:'d-m-Y'
			,width:110
			,name: 'horaire_fin_date'
			,id: 'horaire_fin_date'
			,value:"{$value|default:$smarty.now|date_format:'%d-%m-%Y'}"
		},{ 
			xtype:'timefield'
			,name: 'horaire_fin_time'
			,id: 'horaire_fin_time'
			,format:'H:i'
			,minValue: '9:00'
			,maxValue: '18:00'
			,increment: 30
			,width:65
			,value:"{$value|default:$smarty.now|date_format:'%H:%M'}"
			,listeners :{
				change:function(){
					$('#horaire_fin').val($('#horaire_fin_date').val()+' '+$('#horaire_fin_time').val());
				}
			}
		},{
			xtype:'textfield'
			,hidden:true
			,id:'horaire_fin'
			,name:'tache[horaire_fin]'
			,value:"{$value|default:$item.default|default:$smarty.now|date_format:'%d-%m-%Y %H:%M:00'}"
		}]
},{
	xtype:'button'
	,text:"{ATF::$usr->trans('charge','tache')|escape:javascript}"
	,listeners:{
		'click':function(){
			ATF.ajax('tache,liste_tache.ajax','date='+$('#horaire_fin_date').val(),{ onComplete : function(obj){ 
				if(!Ext.ComponentMgr.get('liste_tache').isVisible()){
					Ext.ComponentMgr.get('liste_tache').show();
				}

				var date_horaire_fin=$('#horaire_fin_date').val();
				var jour = date_horaire_fin.substring(0,2);
				var mois = date_horaire_fin.substring(3,5);
				var annee = date_horaire_fin.substring(6,10);
				var new_format=annee+"-"+mois+"-"+jour;
				date_horaire_fin=new Date(new_format);
				Ext.ComponentMgr.get('datepicker2').setValue(date_horaire_fin);
				
				Ext.ComponentMgr.get('liste_tache2').removeAll();
				if (obj.result) {
					for(var horaire_fin in obj.result[0]){
						Ext.ComponentMgr.get('liste_tache2').add(
							{
								xtype: 'compositefield',
								hideLabel:true,
								msgTarget: 'under',
								items: [
									{ xtype: 'displayfield', value: horaire_fin+" : ", width:80 }
									,{ xtype: 'displayfield', value: obj.result[0][horaire_fin], width:30 }
								]
							}
						);
					}
				}
				Ext.ComponentMgr.get('liste_tache2').doLayout(true);
			} });
		}
	}	
},{
	xtype:'fieldset',
	id:'liste_tache',
	hidden:true,
	title: "{ATF::$usr->trans('nbre_prevue','tache')|escape:javascript}",
	autoHeight:true,
	defaults: { labelWidth: 150 },
	items:[{
		   xtype: 'panel',
			anchor: '95%',
			layout:'column',
			items:[{
						id:'liste_tache2'
						,width:200
					},{
						xtype:'datepicker'
						,id:'datepicker2'
						,startDay : 1
						,autoWidth:true
						,listeners: {
							'select': function(node, dd){ 
								ATF.ajax('tache,liste_tache.ajax','date='+dd.dateFormat("d-m-Y"),{ onComplete : function(obj){ 
									$('#horaire_fin_date').val(dd.dateFormat("d-m-Y"));	
									$('#horaire_fin').val($('#horaire_fin_date').val()+' '+$('#horaire_fin_time').val());
									Ext.ComponentMgr.get('liste_tache2').removeAll();
									if (obj.result) {
										for(var horaire_fin in obj.result[0]){
											Ext.ComponentMgr.get('liste_tache2').add(
												{
													xtype: 'compositefield',
													hideLabel:true,
													msgTarget: 'under',
													items: [
														{ xtype: 'displayfield', value: horaire_fin+" : ", width:80 }
														,{ xtype: 'displayfield', value: obj.result[0][horaire_fin], width:30 }
													]
												}
											);
										}
									}
									Ext.ComponentMgr.get('liste_tache2').doLayout(true);
								} }); 
							}
						} 
					}]
			}]
}{/strip}