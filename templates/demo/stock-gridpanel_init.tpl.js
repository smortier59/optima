{strip}
{* Ajout du champ nécessaire pour ce renderer *}
{util::push($fieldsKeys,"allowRecu")}
{util::push($fieldsKeys,"allowLivre")}
{util::push($fieldsKeys,"allowAnnule")}
{util::push($fieldsKeys,"allowVente")}
{util::push($fieldsKeys,"allowMagento")}
{util::push($fieldsKeys,"quantiteInMagento")}
{util::push($fieldsKeys,"quantite")}
{util::push($fieldsKeys,"serial")}
{util::push($fieldsKeys,"ref")}
{util::push($fieldsKeys,"to_magento")}
{util::push($fieldsKeys,"categories_magento")}
{util::push($fieldsKeys,"inventaire2013")}

ATF.renderer.actionsStock=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		if(record.json){
			var idDivActionsStock = Ext.id();
			var id = record.data[table+'__dot__id_'+table];
			var allowRecu = record.data['allowRecu'];
			var allowLivre = record.data['allowLivre'];
			var allowAnnule = record.data['allowAnnule'];
			var allowVente = record.data['allowVente'];
			var allowMagento = record.data['allowMagento'];
			ATF.log(record.data);
			if (record.data["inventaire2013"]=="non") {
				var allowInventaire = false;
			} else {
				var allowInventaire = true;
			}

			var btnRecu = {
				xtype:'button',
				id:"recu",
				buttonText: '',
				buttonOnly: true,
				iconCls: 'btnRecu',
				cls:'floatLeft',
				disabled:!allowRecu,
				tooltip: '{ATF::$usr->trans("recu","livraison")}',
				tooltipType:'title',
				listeners: {
					'click': function(fb, v){
						if(ATF.currentWindow){
							ATF.currentWindow.close();
						}
						ATF.basicInfo = new Ext.FormPanel({
							items:[
								 {
									 xtype: 'compositefield',
									 fieldLabel: '{ATF::$usr->trans("date_reception","stock")|escape:javascript}',
									 items:[
											{
												xtype:'datefield',
												id: 'date_reception',
												width: 120,
												format:'d-m-Y',
												value: '{$smarty.now|date_format:"%d-%m-%Y"}'
											}
											,
											{
												xtype:'timefield',
												id: 'time_reception',
												width: 65,
												format:'H:i',
												value: '{$smarty.now|date_format:"%H:%M"}'
											}
											,
											{
												xtype:'hidden',
												id: 'time_second_reception',
												value:'{$smarty.now|date_format:"%S"}'
											}
										]
								 }
							]
							,buttons: [
								 {
									 text: '{ATF::$usr->trans(ok)|escape:javascript}',
									 handler: function(el,ev){
										if (!record.data[table+'__dot__serial']) {
											Ext.Msg.show({
											   title:'{ATF::$usr->trans("manque_de_serial_title","stock")|escape:javascript}',
											   msg: '{ATF::$usr->trans("manque_de_serial","stock")|escape:javascript}',
											   buttons: Ext.Msg.OKCANCEL,
											   fn: function (v) {
													if (v=="ok") {
														ATF.ajax('stock,setReceived.ajax','id_stock='+id+'&date_reception='+Ext.getCmp('date_reception').value+"{urlencode(" ")}"+Ext.getCmp('time_reception').value+"{urlencode(":")}"+Ext.getCmp('time_second_reception').value);
														ATF.currentWindow.close();
														store.reload();
													} else {
														ATF.currentWindow.close();
													}
											   },
											   animEl: 'elId',
											   icon: Ext.MessageBox.QUESTION
											});
										} else {
											ATF.ajax('stock,setReceived.ajax','id_stock='+id+'&date_reception='+Ext.getCmp('date_reception').value+"{urlencode(" ")}"+Ext.getCmp('time_reception').value+"{urlencode(":")}"+Ext.getCmp('time_second_reception').value);
											ATF.currentWindow.close();
											store.reload();
										}
									 }
								 }
							 ]
						});
						/*ATF.ajax('stock,setReceived.ajax','id_stock='+id);*/
						ATF.currentWindow = new Ext.Window({
							title: '{ATF::$usr->trans("reception_stock","stock")|escape:javascript}',
							id:'mywindow',
							width: 400,
							buttonAlign:'center',
							closable:true,
							items: ATF.basicInfo
						}).show();
						return false;
					}
				}
			};

		
			var btnMagento = {
				xtype:'button',
				id:"btnToMagento",
				buttonText: '',
				disabled:!allowMagento,
				buttonOnly: true,
				iconCls: 'btnVente',
				cls:'floatLeft',
				tooltip: '{ATF::$usr->trans("en_vente", "stock")}',
				tooltipType:'title',
				listeners: {
					'click': function(fb, v){
						if(ATF.currentWindow){
							ATF.currentWindow.close();
						}
						ATF.basicInfo = new Ext.FormPanel({
							anchor:"95% 95%"
							,bodyStyle:'padding:5px'
							,labelWidth: 350
							,defaults:{
								width:120								
							}
							,buttonAlign:'center'
							,items:[{
								fieldLabel:'Catégorie Magento'
								,xtype:'combo'
								,editable:false
								,lazyRender:true
								,allowBlank:false
								,store: new Ext.data.ArrayStore({
									fields:['myId','displayText']
									,data: [
										['imprimantes','Imprimantes'], 
										['uc','UC'],
										['ecrans','Ecrans'],
										['portables','Portables'], 
										['serveurs','Serveurs'], 
										['reseau','Réseau'], 
										['photocopieurs','Photocopieurs'], 
										['video_projecteurs','Vidéos projecteurs'], 
										['divers','Divers']
									]
								})
								,hiddenName:'categories_magento'
								,name:"combo_categories_magento"
								,valueField:'myId'
								,displayField:'displayText'
								,triggerAction:'all'
								,typeAhead:true
								,preventMark:true
								,mode:'local'
								,value:record.data["categories_magento"]
							}, {
								fieldLabel:'Prix de vente'
								,xtype:'textfield'
								,id:"prix"
								,value:record.data[table+'__dot__prix']
							}, {
								fieldLabel:'Mise a jour automatique des infos via ICECAT (description, description courte, poids et image)"'
								,xtype:'checkbox'
								,id:"icecatParsing"
								,name:"icecatParsing"
								,boxLabel: "Oui"
								,inputValue: 1
							}, {
								xtype: 'label',
								html: '<br/><br/>'+record.data[table+'__dot__libelle']+ 
										'<br/><br/>REF:	'+record.data[table+'__dot__ref']+
										'<br/>Quantité déjà dans Magento : '+record.data['quantiteInMagento']+
										'<br/>Quantité a envoyer a Magento : '+record.data['quantite']
										
							}, {
								xtype: 'textfield',
								hidden:true,
								value:id,
								id:'id_stock',
								name:'id_stock'
							}]
							,buttons: [
								{
									text:"Valider"
									,handler: function (a,b) {
										ATF.basicInfo.getForm().submit({
											submitEmptyText:false,
											method : 'post',
											waitMsg : '{ATF::$usr->trans(loading)|escape:javascript}',
											url     : 'extjs.ajax',
											params: {
												'extAction':'stock'
												,'extMethod':'updateForMagento'
												,'action':record.data[table+'__dot__to_magento']=="oui"?"retrieve":"send"
											},success:function(form, action) {
												ATF.extRefresh(action);
												ATF.currentWindow.close();
												store.reload();
											}
										});
									
									}
								}
							]
						});
						
						if (record.data[table+'__dot__to_magento'] == "non") {
							ATF.currentWindow = new Ext.Window({
								title: '{ATF::$usr->trans("reception_stock","absystech")|escape:javascript}',
								id:'mywindow',
								width: 500,
								buttonAlign:'center',
								closable:true,
								items: ATF.basicInfo
							}).show();
						} else {
							Ext.MessageBox.buttonText.yes = "Tout retirer";
							Ext.MessageBox.buttonText.no = "Retirer un seul produit";
							Ext.MessageBox.buttonText.cancel = "Annuler";
							Ext.Msg.show({
								title:'{ATF::$usr->trans("question_stock_title", "stock")|addslashes}',
								msg: '{ATF::$usr->trans("question_stock","stock")|addslashes}<br />'+record.data[table+'__dot__libelle'],
								buttons: Ext.Msg.YESNOCANCEL,
								fn: function (v) {
									if (v=="yes" || v=="no") {
										ATF.ajax('stock,updateForMagento.ajax','id_stock='+id+'&to_magento=non&nb='+v);
										store.reload();
									}
								},
								animEl: 'elId',
								icon: Ext.MessageBox.QUESTION
							});	
						}
						return false;
					}
				}
		
			};

			var btnLivre = {
				xtype:'button',
				id:"livre",
				buttonText: '',
				buttonOnly: true,
				iconCls: 'btnLivre',
				cls:'floatLeft',
				disabled:!allowLivre,
				tooltip: '{ATF::$usr->trans("livre","livraison")}',
				tooltipType:'title',
				listeners: {
					'click': function(fb, v){
						if (confirm('{ATF::$usr->trans(Etes_vous_sur)}')) {
							ATF.ajax('stock,setDelivered.ajax','id_stock='+id);
							store.reload();
							return false;
						}
					}
				}
			};
	
			var btnAnnule = {
				xtype:'button',
				id:"annule",
				buttonText: '',
				buttonOnly: true,
				iconCls: 'btnCancel',
				cls:'floatLeft',
				disabled:!allowAnnule,
				tooltip: '{ATF::$usr->trans("annule","livraison")}',
				tooltipType:'title',
				listeners: {
					'click': function(fb, v) {
						if (confirm('{ATF::$usr->trans(Etes_vous_sur)}')) {
							ATF.ajax('stock,annule.ajax','id_stock='+id);
							store.reload();
							return false;
						}
					}
				}
			};
	
	
			var btnBarcode = {
				xtype:'button',
				id:"btnBarcode",
				buttonText: '',
				buttonOnly: true,
				iconCls: 'btnBarcode',
				cls:'floatLeft',
				tooltip: '{ATF::$usr->trans("btnBarcode","stock")}',
				tooltipType:'title',
				listeners: {
					'click': function(fb, v) {
						window.open('/?function=code_barre&method=pdf&id[serial][0]='+record.data[table+'__dot__serial']);
					}
				}
			};
	
			var btnIcecat = {
				xtype:'button',
				id:"btnIcecat",
				buttonText: '',
				buttonOnly: true,
				iconCls: 'btnIcecat',
				cls:'floatLeft',
				tooltip: '{ATF::$usr->trans("btnIcecat","stock")}',
				tooltipType:'title',
				listeners: {
					'click': function(fb, v) {
						window.open('http://www.icecat.biz/index.cgi?language=fr&new_search=1&lookup_text='+record.data[table+'__dot__ref']);
					}
				}
			};
	
			var btnGarantieHP = {
				xtype:'button',
				id:"btnGarantieHP",
				buttonText: '',
				buttonOnly: true,
				iconCls: 'btnGarantieHP',
				cls:'floatLeft',
				tooltip: '{ATF::$usr->trans("btnGarantieHP","stock")}',
				tooltipType:'title',
				listeners: {
					'click': function(fb, v) {
						/*window.open('http://h10025.www1.hp.com/ewfrf/wc/weNoResults?tmp_weCountry=fr&tmp_weSerial='+record.data[table+'__dot__serial']+'&tmp_weProduct='+record.data[table+'__dot__ref']+'&cc=fr&dlc=fr&lc=fr');*/
						window.open('http://h10025.www1.hp.com/ewfrf/wc/weResults?tmp_weCountry=fr&tmp_weSerial='+record.data[table+'__dot__serial']+'&tmp_weProduct='+record.data[table+'__dot__ref']+'&lc=fr&dlc=fr&cc=fr&tmp_weDest=&tmp_track_link=ot_we%2Fsubmit%2Ffr_fr%2Fentitlement%2Floc%3A0');
					}
				}
			};
			
			var btnCheckInventaire = {
				xtype:'button',
				id:"btnCheck",
				buttonText: '',
				buttonOnly: true,
				iconCls: 'valid',
				disabled:allowInventaire,
				cls:'floatRight',
				tooltip: '{ATF::$usr->trans("checkInventaire")}',
				tooltipType:'title',
				listeners: {
					'click': function(fb, v) {
						ATF.ajax('stock,checkInventaire2013.ajax','id_stock='+id);
						store.reload();
						return false;
						
					}
				}
			};

			var btnSwitchStock = {
				xtype:'button',
				id:"btnSwitchStock",
				buttonText: '',
				buttonOnly: true,
				iconCls: 'switchStock',
				cls:'floatRight',
				tooltip: '{ATF::$usr->trans("SwitchStock")}',
				tooltipType:'title',
				listeners: {
					'click': function(fb, v) {
							var fp = new Ext.FormPanel({
						    width: 500,
						    frame: true,
						    id : "formulaire",
						    title: 'SwitchStock',
						    autoHeight: true,
						    bodyStyle: 'padding: 10px 10px 0 10px;',
						    labelWidth: 200,
						    items: [
						    		{
							            xtype: 'textfield',
							            fieldLabel: 'Serial',
							            name: 'serial',
							            id: 'serial',   
							            autoHeight: true,
							            width: 117,
							            allowBlank: false,
							            maxLength: 11
							        }
						    
						    ]
						});
						
						var win = new Ext.Window({
			                width:500,
			                height:150,
			                closeAction:'destroy',
			                id:'windowForm',
			                items: [fp],
			                buttons : [
			                	{
			                		text : 'Changer le serial',
			                		handler : function(btn){
			                			var serial = Ext.getCmp("formulaire").form.items.items[0].getValue();
										ATF.ajax("stock,switchStock.ajax",{	id : id,
									     									serial : serial,
									     									affaire : record.data["stock__dot__id_affaire_fk"]
									     								  }, 
									     	{
									     	  	onSuccess:function (obj) {				
													if(obj.result == "error"){
														Ext.Msg.alert("Serial Incorrect","Le serial ne correspond à aucun stock");														
													}else{
														Ext.Msg.alert("Changement effectué","Le stock a été changé");
														Ext.getCmp("windowForm").close();
														store.reload();											
													}
												}
											}

	     								);
			                		}
			                	}
			                ]
			            });
			            win.show(this);
					}
				}
			};


			
			(function(){					
					var params = {
						renderTo: idDivActionsStock,
						items:[btnMagento,btnRecu,btnLivre,btnAnnule,btnBarcode,btnGarantieHP,btnIcecat,btnCheckInventaire{if ATF::_r('id_affaire')},btnSwitchStock{/if}]
					};
					
				var p = new Ext.Container(params);
			}).defer(25);
	
			
			return '<div class="left" id="'+idDivActionsStock+'"></div>';
		}
	}
};

ATF.rowEditor.setInfos=function(table,field) {
	return new Ext.form.TextField({
		value: 0,
		id:table+'_'+field+'_'+Ext.id(),
		fieldLabel: '',
		listeners:{
			change:function(f) {
				ATF.ajax(table+',setInfos.ajax','id_'+table+'='+this.gridEditor.record.data[table+'__dot__id_'+table]+'&'+field+'='+this.getValue());
			}
			,specialkey: function(tf, e){
				if (e.getKey() == e.ENTER) {
					ATF.ajax(table+',setInfos.ajax','id_'+table+'='+this.gridEditor.record.data[table+'__dot__id_'+table]+'&'+field+'='+this.getValue());
				}
			}
		}
	});
};


{/strip}