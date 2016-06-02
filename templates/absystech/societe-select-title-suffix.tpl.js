{$icone=$current_class->meteo_icone($infos.meteo)}
{ATF::$html->assign('id',$infos.id_societe)}
{$contactSociete = ATF::contact()->options_special('id_societe',$infos.id_societe)}
{$dataGraphEcheancier = ATF::gestion_ticket()->stats(false,lol,true,$infos.id_societe)} 
{$solde=ATF::societe()->getSolde($infos.id_societe)}


{
	html:'<div class="label label-{if $solde>0}positive{else}negative{/if}">Crédit restants : {$solde|escape:javascript}</div>',
	cls: "labelCtn"
}


{$tpl = ATF::$html->fetch('tooltip/societe-meteo.tpl.htm')}  
,{ 
	html:'<img class="png" height="32" ext:qtip="{$tpl|addslashes}" width="32" alt="" src="{ATF::$staticserver}images/icones/meteo/{$icone.icone}.png"/>',
	border: false,
	style: {
		float: 'left'
	}
}

{if $infos.tel}
	,{
		xtype: 'button',
		iconCls: 'iconHotline',
		id:'btnClick2call',
		border:false,
		scale: 'medium',
		tooltip:'{ATF::$usr->trans("click2call")}',
		handler: function () {
			ATF.tpl2div(
				'asterisk,createCall.ajax',
				'id={classes::cryptId($id|default:$item["`$current_class->table`.id_`$current_class->table`"])}&table={$current_class->name()}&field=tel'
			);
		} ,	
		style: {
			
			marginLeft: "5px",
			float: 'left'
		}

	}
{/if}
	,{
		xtype: 'button',
		iconCls: 'iconInfo',
		id:'btnHotline',
		scale: 'medium',
		tooltip:'{ATF::$usr->trans("hotline")}',
		handler: function () {


			ATF.currentWindow = new Ext.Window({
				title: "{ATF::$usr->trans('societe_hotline',$current_class->table)}",
				width:800,
				id:'windowInfoHotline',
				height:600,
				items: [{
					layout:'accordion',
					xtype:'panel',
					margins:'5 0 5 5',
					animate: true,
        			activeOnTop: true,
        			defaults: {
						autoHeight:true,
						style:{
							textAlign:'center'
						},
						xtype: 'panel',
						padding: 10
        			},
					anchor:"95%",
                	items:[{
							title:'{ATF::$usr->trans("boutons_rapide",$current_class->table)}',
							id:'panelButtonHotlineInsert',
							
							
						},{
							title:'{ATF::$usr->trans("mail_lost_password",$current_class->table)}',
							id:'panelIdentifiantHotline',
							layout:'form',
							items:[{
								xtype:'combo',
								typeAhead:true,
								editable:false,
								triggerAction:'all',
								lazyRender:true,
								mode:'local',
								preventMark:true,
								hiddenName:'{$name}',
								store: new Ext.data.ArrayStore({
									fields: [
										'myId',
										'displayText'
									],			
									data: [
										{foreach from=$contactSociete key=k item=i}
											['{$k}', '{$i|escape:javascript}']
											{if !$i@last},{/if}
										{/foreach}
									]
								}),
								valueField: 'myId',
								displayField: 'displayText',
								fieldLabel: '{ATF::$usr->trans("societe_select_contact",$current_class->table)|escape:javascript} {$img_ob|escape:javascript}',
								name: 'select_contact',
								id: 'select_contact',
								anchor:'95%'
							}]
						},{
							title:'{ATF::$usr->trans('add_credits_hotline',$current_class->table)}',
							id:'panelCreditPlusHotline',
							items:[{
								xtype: 'form',
								labelWidth: 50,
								id:'addCreditForm',
								border: false,
								layout:'column',
								defaults:{
									layout:'form',
									border:false,
									xtype:'panel'
								},
								
								items:[{
									columnWidth:0.2,
									items: [{
										name:'credits',
										xtype:'textfield',
										anchor: '95%',
										id:'credits',
										fieldLabel:'{ATF::$usr->trans(ajout_credit,$current_class->table)|escape:javascript}'
									}]
								},{
									columnWidth:0.4,
									items: [{
										name:'libelle',
										xtype:'textfield',
										anchor: '95%',
										id:'libelle',
										fieldLabel:'{ATF::$usr->trans(libelle,$current_class->table)|escape:javascript}'
									}]
								},{
									columnWidth:0.4,
									items: [
										{include file="generic-field-textfield.tpl.js" 
													id="id_facture"
													key="id_facture" 
													condition_field="id_societe" 
													condition_value="{$infos.id_societe}" 
													est_nul=true 
													fieldLabel="{ATF::$usr->trans(id_facture,$current_class->table)|escape:javascript}"}
									]
								}]
							}]
						},{
							title:'{ATF::$usr->trans('hotline_echeancier',$current_class->table)}'
							,id:'formEcheancier'
							,layout:'column'
							,defaults: {
								xtype:'datefield',
								format: 'd-m-Y',
								columnWidth:'0.4'
							}
							,items: [{
								columnWidth:'1'
								,xtype:'container'
								,id:'graphe'
								,style:{
									textAlign:'center'
								}
								,listeners:{
									afterlayout: function (el) {
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
									                {foreach $dataGraphEcheancier["categories"]["category"] as $cat}                    
									                    '{$cat["label"]}' {if !$cat@last},{/if}
									                {/foreach}
									                ]
									        },
									        legend: {
									            itemStyle: {
									                fontSize:'9px',
									                
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
									           {foreach $dataGraphEcheancier["dataset"] as $data2}          
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
									}
								}
							},{ 
								fieldLabel: '{ATF::$usr->trans(date_debut_rapport,societe)|escape:javascript}'
								,id: 'date_debut_rapport'
								,name: 'date_debut_rapport'
								,value: '01-{$smarty.now|date_format:'%m-%Y'|escape:javascript}'

							},{
								fieldLabel: '{ATF::$usr->trans(date_fin_rapport,societe)|escape:javascript}'
								,id: 'date_fin_rapport'
								,name: 'date_fin_rapport'
								,value: '{date(t)|escape:javascript}-{$smarty.now|date_format:'%m-%Y'|escape:javascript}'
							},{ 
								xtype:'button'
								,columnWidth:'0.2'	
							    ,text: '{ATF::$usr->trans(generer_rapport,societe)|escape:javascript}'
							    ,handler:function(){
									window.open('hotline_echeancier-{ATF::_r(id_societe)}.html2pdf,dl=1&date_debut='+Ext.getCmp('date_debut_rapport').getValue().dateFormat('Y-m-d')+'&date_fin='+Ext.getCmp('date_fin_rapport').getValue().dateFormat('Y-m-d'));
									if(ATF.currentWindow){
										ATF.currentWindow.close();
									}
							    }
							}]
						}

                	]
				}],
				listeners: {
					afterrender: function (el,v) {
						var p = Ext.getCmp('panelButtonHotlineInsert');
						var data = {
							id: "ButtonHotlineInsert",
							icone: "insert",
							text: "{ATF::$usr->trans(hotline_insert,$current_class->table)|strtoupper}",
							style: 'style="width:150px"',
							onclick: "ATF.currentWindow.close();ATF.goTo('hotline-insert.html,id_societe={$infos.id_societe|cryptid}');"
						};
						buttonTpl.overwrite(p.body, data);

						var data2 = {
							id: "ButtonHotlinePortal",
							icone: "expand",
							text: "{ATF::$usr->trans(portail_hotline,$current_class->table)|strtoupper}",
							style: 'style="width:150px"',
							onclick: "window.open('{$current_class->redirect_hotline($infos.ref,$infos.divers_5)}')"
						};
						buttonTpl.append(p.body, data2);

						var p2 = Ext.getCmp('panelIdentifiantHotline');
						var data3 = {
							id: "ButtonSendIdentifiant",
							icone: "email",
							text: "{ATF::$usr->trans(societe_send_mail,$current_class->table)|strtoupper}",
							style: 'style="width:150px"',
							onclick: "ATF.__send_identifiants_hotline('{ATF::$usr->trans('mail_lost_password_confirm',$current_class->table)}','{$infos.id_societe}',Ext.getCmp('select_contact').getValue());"
						};
						buttonTpl.append(p2.body, data3);
                		
						var p3 = Ext.getCmp('panelCreditPlusHotline');
						var data4 = {
							id: "ButtonAddCredits",
							icone: "add",
							text: "{ATF::$usr->trans(add_credits_hotline,$current_class->table)|strtoupper}",
							style: 'style="width:150px"',
							onclick: "ATF.__update_tickets();"
						};
						buttonTpl.append(p3.body, data4);
                		
					}

				}
			});

			ATF.currentWindow.show();

		} ,	
		style: {
			
			marginLeft: "5px",
			float: 'left'
		}

	},{
		xtype: 'button',
		iconCls: 'iconGMap',
		id:'btnGMap',
		scale: 'medium',
		tooltip:'{ATF::$usr->trans("geolocalisation")}',
		style: {
			 
			marginLeft: "5px",
			float: 'left'
		},
		handler: function () {


			ATF.currentWindow = new Ext.Window({
	            title: '{ATF::$usr->trans(geolocalisation)|escape:javascript} - {ATF::societe()->nom($infos.id_societe)|escape:javascript}',
	            cls: "gMapWindow",
	            monitorResize:false,
	            resizable: false,
		        autoLoad:{ url: '{$current_class->name()},geolocalisation.ajax,id={$infos.id_societe}', scripts:true }
    		});
    		
			ATF.currentWindow.show();

			
		}
	},{
		xtype: 'button',
		iconCls: 'iconATCard',
		id:'btnATCard',
		scale: 'medium',
		tooltip:'{ATF::$usr->trans("atcard")}',
		style: {
			
			marginLeft: "5px",
			float: 'left'
		},
		handler: function () {
			window.open("societe,atcard.ajax,id_societe={$id}");
		}
	}