{$id_user = ATF::$usr->getID()}
{util::push($fieldsKeys,"reseau")}



{if ( (ATF::$codename == "cleodis" || ATF::$codename == "cleodisbe" || ATF::$codename == "assets") && $id_user == 16)
 || ( (ATF::$codename == "cleodis" || ATF::$codename == "cleodisbe" || ATF::$codename == "assets") && $id_user == 17)
 || ( (ATF::$codename == "cleodis" || ATF::$codename == "cleodisbe" || ATF::$codename == "assets") && $id_user == 18)
 || ( (ATF::$codename == "cleodis" || ATF::$codename == "cleodisbe" || ATF::$codename == "assets") && $id_user == 93)
 || ( (ATF::$codename == "cleodis" || ATF::$codename == "cleodisbe" ) && $id_user == 35)
 || ( (ATF::$codename == "cleodis" || ATF::$codename == "cleodisbe" || ATF::$codename == "assets") && $id_user == 21)
 || (ATF::$codename == "cleodisbe" && $id_user == 104) || ((ATF::$codename == "cleodis" || ATF::$codename == "assets") && $id_user == 103)
 || (ATF::$codename == "cleodisbe" && $id_user == 113) || ((ATF::$codename == "cleodis" || ATF::$codename == "assets") && $id_user == 116)
 || (ATF::$codename == "cleodisbe" && $id_user == 116) || ((ATF::$codename == "cleodis" || ATF::$codename == "assets") && $id_user == 124)

 || (ATF::$codename == "bdomplus" && ($id_user ==  16 || $id_user == 116 ))


  }
	ATF.renderer.comiteDecision=function(table,field) {
		return function(filetype, meta, record, rowIndex, colIndex, store) {
			var idDiv = Ext.id();
			var id = record.data[table+'__dot__id_comite'];

			var id_affaire = record.data[table+'__dot__id_affaire_fk'];

			var ok = true;



			if (record.data.comite__dot__etat=="en_attente" && ok==true) {
				var btndecision = {
					xtype:'button',
					id:"decision"+id,
					buttonText: '',
					buttonOnly: true,
					iconCls: 'btnValid',
					cls:'floatLeft',
					tooltip: '{ATF::$usr->trans("Décision")}',
					tooltipType:'title',
					listeners: {
						'click': function(fb, v){
								var decision = new Ext.data.ArrayStore({
									fields: ["id", "text"],
									data: [  ["refus_comite", "Refus comité"]
											,["accord_portage", "Accord de portage"]
											,["accord_portage_recherche_cession", "Accord de portage avec recherche et cession"]
											,["accord_portage_recherche_cession_groupee", "Accord de portage avec recherche et cession GROUPEE"]
											,["accord_reserve_cession", "Accord sous réserve de cession"]
											,["attente_retour", "Attente de retour"]

										  ]
								});
								var hiddenField = new Ext.form.Hidden({
								    name: 'comboDisplay'
								});

								var form = new Ext.FormPanel({
										frame:true,
										autoHeight:true,
										id:'myForm'+id,
										name:'myFormName'+id,
										title: '',
										bodyStyle:'padding:5px 5px 0',
										items: [
											 {
											 	 xtype: "combo"
											 	,fieldLabel: "Décision du comité"
											 	,name:"decision"
											 	,store: decision
											 	,displayField: "text"
											 	,mode: "local"
											 	,listeners: {
											        select: function(combo, record) {
											            hiddenField.setValue(record.data['id']);
											        }
											    }
											 },
											 {
											 	 xtype : 'textfield'
												,fieldLabel: 'Commentaire'
												,name: 'commentaire'
											 },
											 {
											 	 xtype: 'datefield',
											     fieldLabel: 'Date de validité',
											     name: 'date',
											     value:new Date().add(Date.MONTH , +3)
											 }
											,
												hiddenField
										],
										buttons: [{
											 text: 'Ok'
											,handler: function(a,b,c,d){
												Ext.getCmp('myForm'+id).getForm().submit({
													submitEmptyText:false,
													method  : 'post',
													waitMsg : '{ATF::$usr->trans(loading_new_page)|escape:javascript}',
													waitTitle :'{ATF::$usr->trans(loading)|escape:javascript}',
													url     : 'extjs.ajax',
													params: {
														'extAction':'comite'
														,'extMethod':'decision'
														,'id':id
														,'id_affaire':id_affaire
													}
													,success:function(form, action) {
														ATF.ajax_refresh(action.result,true);
														Ext.getCmp('myForm'+id).destroy();
														Ext.getCmp('mywindow'+id).destroy();
														store.reload();
													}
													,timeout:3600
												});
											}
										},
										{
											text: 'Annuler',
											handler: function(){
												Ext.getCmp('myForm'+id).destroy();
												Ext.getCmp('mywindow'+id).destroy();
											}
										}]
								});


								if (!Ext.getCmp('mywindow'+id)) {
									var height = 500;
									var width = 500;
									new Ext.Window({
										title: '{ATF::$usr->trans("Décision du comite : ")}',
										id:'mywindow'+id,
										plain:true,
										bodyStyle:'padding:5px;',
										buttonAlign:'center'
									});
									if (form) {
										Ext.getCmp('mywindow'+id).add(form);
										height += 400;
										width = 800;
									}
								}
								Ext.getCmp('mywindow'+id).setHeight(height);
								Ext.getCmp('mywindow'+id).setWidth(width);
								Ext.getCmp('mywindow'+id).show();
						}
					}
				};

				(function(){
					var params = {
						renderTo: idDiv,
						items:[btndecision]

					};
					var p = new Ext.Container(params);
				}).defer(25);
				return '<div class="left" id="'+idDiv+'"></div>';

			} else {
				return '<img src="{ATF::$staticserver}images/icones/no_2.png" />';
			}
		}
	};
{else}
	ATF.renderer.comiteDecision=function(table,field) {
		return function(filetype, meta, record, rowIndex, colIndex, store) {
			return '<img src="{ATF::$staticserver}images/icones/no_2.png" />';
		}
	};
{/if}


ATF.renderer.envoi_mail_demandeRefi=function(table, field){
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		var idDiv = Ext.id();
		var id = record.data[table+'__dot__id_comite'];

		var btnSendMail = {
			xtype:'button',
			id:"decision"+id,
			buttonText: '',
			buttonOnly: true,
			iconCls: 'iconMailOK',
			cls:'floatLeft',
			tooltip: 'Envoyer le mail pour la demande de refinancement',
			tooltipType:'title',
			listeners: {
				'click': function(fb, v){

					var form = new Ext.FormPanel({
						frame:true,
						autoHeight:true,
						id:'myForm'+id,
						name:'myFormName'+id,
						title: '',
						bodyStyle:'padding:5px 5px 0',
						buttons: [{
							 text: 'Envoyer'
							,handler: function(a,b,c,d){
								Ext.getCmp('myForm'+id).getForm().submit({
									submitEmptyText:false,
									method  : 'post',
									waitMsg : '{ATF::$usr->trans(loading_new_page)|escape:javascript}',
									waitTitle :'{ATF::$usr->trans(loading)|escape:javascript}',
									url     : 'extjs.ajax',
									params: {
										'extAction':'comite'
										,'extMethod':'sendMailDemandeRefi'
										,'id':id
									}
									,success:function(form, action) {
										ATF.ajax_refresh(action.result,true);
										Ext.getCmp('myForm'+id).destroy();
										Ext.getCmp('mywindow'+id).destroy();
										store.reload();
									}
									,timeout:3600
								});
							}
						},
						{
							text: 'Annuler',
							handler: function(){
								Ext.getCmp('myForm'+id).destroy();
								Ext.getCmp('mywindow'+id).destroy();
							}
						}]
					});


					if (!Ext.getCmp('mywindow'+id)) {
						var height = 200;
						var width = 100;
						new Ext.Window({
							title: '{ATF::$usr->trans("Envoyer le mail au refinanceur pour la demande de refinancement ?")}',
							id:'mywindow'+id,
							plain:true,
							bodyStyle:'padding:5px;',
							buttonAlign:'center'
						});
						if (form) {
							Ext.getCmp('mywindow'+id).add(form);
							height += 400;
							width = 800;
						}
					}
					Ext.getCmp('mywindow'+id).setHeight(height);
					Ext.getCmp('mywindow'+id).setWidth(width);
					Ext.getCmp('mywindow'+id).show();
				}
			}
		};


		var btnNoSendMail = {
			xtype:'button',
			id:"decision"+id,
			buttonText: '',
			buttonOnly: true,
			iconCls: 'iconMailNOK',
			cls:'floatLeft',
			tooltip: 'Il n\'y a pas de mail pour ce refinanceur',
			tooltipType:'title'
		};

		if(record.json.email !== null){
			(function(){
				var params = {
					renderTo: idDiv,
					items:[btnSendMail]

				};
				var p = new Ext.Container(params);
			}).defer(25);
		}else{
			(function(){
				var params = {
					renderTo: idDiv,
					items:[btnNoSendMail]

				};
				var p = new Ext.Container(params);
			}).defer(25);
		}

		return '<div class="left" id="'+idDiv+'"></div>';

	}
};
