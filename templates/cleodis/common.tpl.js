ATF.formation_facture_type = function(el,val,lastVal) {

	var id_devis =  Ext.ComponentMgr.get('formation_facture[id_formation_devis]').value;

	ATF.ajax('formation_devis,getMontantForFacture.ajax','id_formation_devis='+id_devis+'&type='+val,{
		onComplete:function(obj){
			Ext.ComponentMgr.get('formation_facture[prix]').setValue(obj.result);
		}
	});
}

ATF.natureMandatChange = function(el,val,lastVal) {
	if(val == "cedre"){
		Ext.ComponentMgr.get('combomandat[type_creance]').setValue("btob_btoc");
		Ext.ComponentMgr.get('combomandat[autorisation_huissier]').setValue("oui");
		Ext.ComponentMgr.get('combomandat[relance_interne]').setValue("oui");
		Ext.ComponentMgr.get('combomandat[acces_web]').setValue("oui");
		Ext.ComponentMgr.get('combomandat[cahier_charge]').setValue("oui");
		Ext.ComponentMgr.get('mandat[indemnite_retard]').setValue("75");
	}


	if(val == "orange_bleue"){
		Ext.ComponentMgr.get('combomandat[type_creance]').setValue("btoc");
		Ext.ComponentMgr.get('combomandat[enregistrement_creance]').setValue("edi");
		Ext.ComponentMgr.get('combomandat[acces_web]').setValue("oui");
		Ext.ComponentMgr.get('combomandat[relance_interne]').setValue("oui");
		Ext.ComponentMgr.get('combomandat[acces_web]').setValue("oui");
		Ext.ComponentMgr.get('combomandat[visite_domiciliaire]').setValue("oui");
		Ext.ComponentMgr.get('combomandat[autorisation_huissier]').setValue("oui");



		Ext.ComponentMgr.get('mandat[indemnite_retard]').setValue("50");



	}


}



ATF.formation_devis_type = function(el,val,lastVal) {

	if(Ext.getCmp('comboformation_devis[type]').value == "light"){
		Ext.ComponentMgr.get('panel_fournisseurs').hide();
		Ext.ComponentMgr.get('panel_participants').hide();
		Ext.ComponentMgr.get('panel_light').show();

	}else{
		Ext.ComponentMgr.get('panel_fournisseurs').show();
		Ext.ComponentMgr.get('panel_participants').show();
		Ext.ComponentMgr.get('panel_light').hide();
	}
}




ATF.changeServiceType = function(el,val,lastVal) {
	if(Ext.getCmp('comboexa_directeur[restriction_services_type]').value == "sans"){
		Ext.ComponentMgr.get('panel_restriction_services_lignes').hide();
	}else{
		Ext.ComponentMgr.get('panel_restriction_services_lignes').show();
	}
}


ATF.changeGeoTyoe = function(el,val,lastVal) {
	if(Ext.getCmp('comboexa_directeur[restriction_geo_type]').value == "sans"){
		Ext.ComponentMgr.get('panel_restriction_geo_lignes').hide();
	}else{
		Ext.ComponentMgr.get('panel_restriction_geo_lignes').show();
	}
}


ATF.changeType_suivi = function(el,val,lastVal) {
	if(Ext.getCmp('combosuivi[type_suivi]').value == "Formation"){
		Ext.ComponentMgr.get('panel_formation').show();
	}else{
		Ext.ComponentMgr.get('panel_formation').hide();
	}
}


ATF.renderer.scanner = function(table , field){
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		if(record.data[table+'__dot__id_'+table]){
			var id = record.data[table+'__dot__id_'+table];
		}else if(record.data['id_'+table]){
			var id = record.data['id_'+table];
		}else{
			var id = null;
		}
		if(id){
			if(table == "pdf_affaire" || table == "pdf_societe"){
					 /*"'+table+'-select-'+field+'-'+id+'.dl"*/
					 if(record.json.url != null){
					 	return '<a href="'+record.json.urlBig+'_0-2480-3500.png" target="_blank" >'+
								'<img  src="'+record.json.url+'_0-200-500.png" />'+
							'</a>';
						}else{
							return "";
						}

			}else{
				if (filetype) {
						return '<a href="'+table+'-select-'+field+'-'+id+'.dl" alt="'+ATF.usr.trans("popup_download",table)+'">'+
									'<img class="smallIcon '+filetype+'" src="'+ATF.blank_png+'" class="icone" />'+
								'</a>';
				} else {
					var idDiv = Ext.id();

					var btnTransfert = {
						xtype:'button',
						id:"transfert-"+field+"-"+id,
						buttonText: '',
						buttonOnly: true,
						iconCls: 'btnScanner',
						cls:'center',
						tooltip: '{ATF::$usr->trans("Fichier de scanner")}',
						tooltipType:'title',
						listeners: {
							'click': function(fb, v){
									ATF.ajax('scanner,getNoTransfered.ajax'
											,null
											,{ onComplete: function (result) {
												var retour = result.result;
												var donnee = [];

												for (var i = 0; i < retour.length; i++) {
													text = retour[i].provenance+" ("+retour[i].nbpages+ "pages) "+retour[i].date;
													donnee[i] = [
														 retour[i].id_scanner,
														 table,
														 id,
														 text
													];
												}

												var transfert = new Ext.data.ArrayStore({
													fields: ["id_scanner","tableto" , "id_to", "text"],
													data: donnee
												});


												var id_scanner = new Ext.form.Hidden({
												    name: 'id_scanner'
												});

												var module = new Ext.form.Hidden({
												    name: 'module'
												});

												var champs = new Ext.form.Hidden({
												    name: 'champs'
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
														 	,fieldLabel: "Type de document"
														 	,name:"transfert"
														 	,store: transfert
														 	,displayField: "text"
														 	,mode: "local"
														 	,listeners: {
														        select: function(combo, record) {
														            id_scanner.setValue(record.data['id_scanner']);
														            module.setValue(record.data['tableto']);
														            champs.setValue(field);
														        }
														    }
														 }
														,{
															 xtype: 'textfield'
															,name: 'id_to'
															,id: 'id_to'
															,value: id
															,hidden:true
														},
															id_scanner,
															module,
															champs
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
																	 'extAction':'scanner'
																	,'extMethod':'transfertTo'
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
														title: '{ATF::$usr->trans("Faire un transfert vers : ")}',
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

											}}
									);
							 }
						}
					};

					(function(){
						var params = {
							renderTo: idDiv,
							items:[btnTransfert]

						};
						var p = new Ext.Container(params);
					}).defer(25);


					return '<div id="'+idDiv+'"></div>';
				}
			}
		}
	}
};


ATF.changeFamille = function(el,val,lastVal) {
	console.log("changeFamille");
	console.log(val);

	if(val == "Foyer"){
		Ext.getCmp("panel_societe_fs").hide();
		Ext.getCmp("panel_codes_fs").hide();
		Ext.getCmp("panel_caracteristiques").hide();
		if (Ext.getCmp("panel_deploiement")) Ext.getCmp("panel_deploiement").hide();

		Ext.getCmp("panel_coordonnees_supplementaires_fs").hide();

		Ext.getCmp('facturation_rib').hide();
		Ext.getCmp('logo').hide();

		Ext.getCmp("panel_particulier_fs").show();
		Ext.getCmp("panel_fidelite").show();
		Ext.getCmp("panel_optin").show();





	}else{

		Ext.getCmp("panel_societe_fs").show();
		Ext.getCmp("panel_codes_fs").show();
		Ext.getCmp("panel_caracteristiques").show();
		if (Ext.getCmp("panel_deploiement")) Ext.getCmp("panel_deploiement").show();

		Ext.getCmp("panel_coordonnees_supplementaires_fs").show();

		Ext.getCmp('facturation_rib').show();
		Ext.getCmp('logo').show();

		Ext.getCmp("panel_particulier_fs").hide();
		Ext.getCmp("panel_fidelite").hide();
		Ext.getCmp("panel_optin").hide();
	}

}