<script type="text/javascript">/*<![CDATA[*/
{if ATF::_r('id_societe')}
	ATF.__update_tickets = function () {
		Ext.getCmp('addCreditForm').getForm().submit({
			submitEmptyText:false,
			method  : 'post',
			waitMsg : '{ATF::$usr->trans(updating_element)|escape:javascript}',
			waitTitle : '{ATF::$usr->trans(loading)|escape:javascript}',
			url     : 'extjs.ajax',
			params: {
				'id_{$current_class->table}':'{ATF::_r("id_societe")}'
				,'extAction':'{$current_class->table}'
				,'extMethod':"add_ticket"
			}
			,success:function(form, action) {
				ATF.extRefresh(action); 
				if(ATF.currentWindow){
					ATF.currentWindow.close();
				} 
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
				if(ATF.currentWindow){
					ATF.currentWindow.close();
				} 
			}
			,timeout:3600
		});						





	}
{/if}

ATF.__send_identifiants_hotline = function (message,id_societe,id_contact){
	if(confirm(message)){
		ATF.tpl2div('societe,send_identifiants_hotline.ajax','id_societe='+id_societe+'&id_contact='+id_contact);
		if(ATF.currentWindow){
			ATF.currentWindow.close();
		} 
	}
}

ATF.openHotlineEcheancierWindow = function(){
	if(ATF.currentWindow){
		ATF.currentWindow.close();
	} 
	var hotline_echeancier_form=new Ext.FormPanel({
				frame:true
				,width: 800
				,defaultType: 'textfield'
				,layout:'column'
				,items: [{
							columnWidth:'1',
							xtype: 'box'
							,id:'graphe'
						 },{
							columnWidth:'0.4'	
							,fieldLabel: '{ATF::$usr->trans(date_debut_rapport,societe)|escape:javascript}'
							,xtype:'datefield'
							,id: 'date_debut_rapport'
							,name: 'date_debut_rapport'
							,format: 'd-m-Y'
							,value: '01-{$smarty.now|date_format:'%m-%Y'|escape:javascript}'
							,width:130
						},{
							columnWidth:'0.4'	
							,fieldLabel: '{ATF::$usr->trans(date_fin_rapport,societe)|escape:javascript}'
							,xtype:'datefield'
							,id: 'date_fin_rapport'
							,name: 'date_fin_rapport'
							,format: 'd-m-Y'
							,value: '{date(t)|escape:javascript}-{$smarty.now|date_format:'%m-%Y'|escape:javascript}'
							,width:130
						},{ 
							xtype:'button'
							,columnWidth:'0.2'	
						    ,text: '{ATF::$usr->trans(generer_rapport,societe)|escape:javascript}'
						    ,handler:function(){
								window.open('hotline_echeancier-{ATF::_r(id_societe)}.html2pdf,dl=1&date_debut='+Ext.ComponentMgr.get('date_debut_rapport').value+'&date_fin='+Ext.ComponentMgr.get('date_fin_rapport').value);
								if(ATF.currentWindow){
									ATF.currentWindow.close();
								}
						    }
						}]
			});		
	
	ATF.currentWindow=new Ext.Window({
		title: '{ATF::$usr->trans(rapport_tickets_hotline,societe)|escape:javascript}',
		buttonAlign:'center',
		closable:true,
		items: hotline_echeancier_form
	});
	ATF.currentWindow.show();
	

	{$data = ATF::gestion_ticket()->stats(false,lol,true,ATF::_r(id_societe))} 
	Ext.onReady(function(){
	    var chart = new Highcharts.Chart({
	        chart: {
	            renderTo: 'graphe',
	            type: 'spline'
	        },
	        title: {
	            text: '{ATF::$usr->trans(widget,$module, $type)|escape:javascript}'
	        },	       
	        xAxis: {
	            categories: [
	                {foreach $data["categories"]["category"] as $cat}                    
	                    '{$cat["label"]}' {if !$cat@last},{/if}
	                {/foreach}
	                ]
	        },
	         legend: {
	            itemStyle: {
	                fontSize:'9px'
	            }
	        },
	        yAxis: {	            
	            title: {
	                text: null
	            }
	        },
	        plotOptions: {
	                spline: {
	                    lineWidth: 2,
	                    states: {
	                        hover: {
	                            lineWidth: 3
	                        }
	                    },
	                    marker: {
	                        enabled: true
	                    }                    
	                }
	            },
	        series: [
	           {foreach $data["dataset"] as $data2}          
	               {
	                    name: '{$data2["params"]["seriesname"]|escape:javascript}'                    
	                   ,data: [	                   			
	                            {foreach $data2["set"] as $k=>$v}	                            	
	                                {$v["value"]|number_format:2:'.':''}                                
	                                {if !$v@last},{/if}
	                            {/foreach}    
	                          ]
	               }
	                {if !$data2@last},{/if}
	            {/foreach}            
	           ]        
	    });
	});    
	
}
/*]]>*/</script>