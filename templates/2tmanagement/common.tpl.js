ATF.changeModeFacture = function(el,val,lastVal) {
	Ext.getCmp('facture[date_debut_periode]').hide();
	if (val=="avoir") {
		Ext.ComponentMgr.get('label_facture[id_facture_parente]').setDisabled(false);
	} else {
		Ext.ComponentMgr.get('label_facture[id_facture_parente]').setDisabled(true);
	}

	if (val=="acompte") {		
		Ext.ComponentMgr.get('facture[finale]').hide();
	}

	if (val=="facture") {
		Ext.ComponentMgr.get('combofacture[periodicite]').show();		
		Ext.ComponentMgr.get('facture[date_fin_periode]').hide();
		Ext.ComponentMgr.get('facture[finale]').show();		
	} else {
		Ext.ComponentMgr.get('combofacture[periodicite]').hide();		
		Ext.ComponentMgr.get('facture[date_fin_periode]').hide();
	}
}

ATF.changeAcompte = function(el,val,lastVal){
	Ext.ComponentMgr.get('facture[finale]').hide();
}

ATF.changePeriode = function(el,val,lastVal) {
	Ext.getCmp('facture[date_debut_periode]').show();
	if(Ext.getCmp('combofacture[periodicite]').value !== ""){
		Ext.ComponentMgr.get('facture[date_fin_periode]').show();
	}else{
		Ext.ComponentMgr.get('facture[date_fin_periode]').hide();
	}
}

ATF.changeFraisDePort = function(el,val,lastVal) {
	Ext.getCmp('facture[frais_de_port]').setValue(val);
	var sumHT = parseFloat(Ext.getCmp('facture[sous_total]').value.replace(" ", "")) + parseFloat(val.replace(" ", ""));
	Ext.getCmp('facture[prix]').setValue(sumHT);
}

ATF.changeTypeDevis = function(el,val,lastVal) {

	Ext.getCmp("panel_financement").show();
	//Ext.getCmp("panel_redaction").show();
	Ext.getCmp("panel_total").show();
	Ext.getCmp("panel_courriel").show();
	Ext.getCmp("panel_redaction").show();
	Ext.getCmp("panel_lignes_consommable").hide();

	if(val == "normal"){
		Ext.getCmp("panel_location").collapse();
		Ext.getCmp("panel_location").hide();		

	}else if(val == "location"){
		Ext.getCmp("panel_location").expand();
		Ext.getCmp("panel_location").show();
	}else if(val == "consommable"){
		Ext.getCmp("panel_location").collapse();
		Ext.getCmp("panel_location").hide();
		Ext.getCmp("panel_lignes").hide();

		Ext.getCmp("panel_financement").hide();
		//Ext.getCmp("panel_redaction").hide();
		Ext.getCmp("panel_total").hide();
		Ext.getCmp("panel_courriel").hide();
		Ext.getCmp("panel_redaction").hide();

		Ext.getCmp("panel_lignes_consommable").show();
		Ext.getCmp("panel_lignes_consommable").expand();
	}

}

ATF.selectTypeDevis = function(el,val,lastVal) {
	var val = val.json[0];

	Ext.getCmp("panel_financement").show();
	//Ext.getCmp("panel_redaction").show();
	Ext.getCmp("panel_total").show();
	Ext.getCmp("panel_courriel").show();
	Ext.getCmp("panel_redaction").show();
	Ext.getCmp("panel_lignes_consommable").hide();

	if(val == "normal"){
		Ext.getCmp("panel_location").collapse();
		Ext.getCmp("panel_location").hide();		

	}else if(val == "location"){
		Ext.getCmp("panel_location").expand();
		Ext.getCmp("panel_location").show();
	}else if(val == "consommable"){
		Ext.getCmp("panel_location").collapse();
		Ext.getCmp("panel_location").hide();
		Ext.getCmp("panel_lignes").hide();

		Ext.getCmp("panel_financement").hide();
		//Ext.getCmp("panel_redaction").hide();
		Ext.getCmp("panel_total").hide();
		Ext.getCmp("panel_courriel").hide();
		Ext.getCmp("panel_redaction").hide();

		Ext.getCmp("panel_lignes_consommable").show();
		Ext.getCmp("panel_lignes_consommable").expand();
	}
	
}


ATF.changefinancement = function(el,val,lastVal) {
	var total = 0;
	if(Ext.getCmp('devis[duree_financement]').value 
	&& Ext.getCmp('devis[cout_total_financement]').value 
	&& Ext.getCmp('devis[prix]').value){
		var duree = Ext.getCmp('devis[duree_financement]').value;
		var cout_total = Ext.getCmp('devis[cout_total_financement]').value;	
		total = parseFloat(cout_total / duree) ;
	
		var prix = parseFloat(Ext.getCmp('devis[cout_total_financement]').value.replace(" ", "")); 
		var pa = parseFloat(Ext.getCmp('devis[prix_achat]').value.replace(" ", ""));
		
		var mb = (1 - (pa/prix)) * 100;	
		Ext.getCmp('devis[marge_financement]').setValue(ATF.formatNumeric(parseFloat(mb)));	
	}	
	
	Ext.getCmp('devis[financement_mois]').setValue(ATF.formatNumeric(parseFloat(total)));	
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
			if(table == "facture_fournisseur"){					
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


/** Permet de gérer l'affichage de nouveau champs lorsque l'on choisi le mode de paiement a l'insertion d'un mpaiement de facture
* @author Quentin JANON <qjanon@absystech.fr>
*/
ATF.selectModePaiement = function (a,b,c) {
	if (b.data.myId=="avoir") {
		if (Ext.ComponentMgr.get('label_facture_paiement[id_facture_avoir]')) {
			Ext.ComponentMgr.get('label_facture_paiement[id_facture_avoir]').setReadOnly(false);
			var id_facture = Ext.ComponentMgr.get('facture_paiement[id_facture]').getValue();
			if (id_facture) {
				Ext.ComponentMgr.get('label_facture_paiement[id_facture_avoir]').clearValue();
				Ext.ComponentMgr.get('label_facture_paiement[id_facture_avoir]').store.proxy=new Ext.data.HttpProxy({
					url: 'facture,autocompleteFactureAvoirDispo.ajax,id_facture='+id_facture
					,method:'POST'
				});
				Ext.ComponentMgr.get('label_facture_paiement[id_facture_avoir]').store.reload();
			}
		}
	} else {
		if (Ext.ComponentMgr.get('label_facture_paiement[id_facture_avoir]')) {
			Ext.ComponentMgr.get('label_facture_paiement[id_facture_avoir]').setReadOnly(true);
		}
	}


}


ATF.selectNature = function (el,val, lastVal){
	Ext.getCmp("hotline_interaction[champ_alerte]").hide();


	Ext.getCmp("hotline_interaction[send_mail]").setValue("oui");
	arr = Ext.query("ul.send_mail_switch li");
	for (var i = 0, len = arr.length; i < len; i++) { 
		var el = Ext.get(arr[i]);
		if(i == 0){
			el.addClass("on");
		}else{			
			el.removeClass("on");
		}
	}


	Ext.getCmp("hotline_interaction[visible]").setValue("oui");
	arr = Ext.query("ul.visible_switch li");
	for (var i = 0, len = arr.length; i < len; i++) { 
		var el = Ext.get(arr[i]);
		if(i == 0){
			el.addClass("on");
		}else{			
			el.removeClass("on");
		}
	}


	if(val.data.myId == "interaction"){
		Ext.getCmp("datehotline_interaction[heure_depart_dep]_time").enable(true);
		Ext.getCmp("datehotline_interaction[heure_arrive_dep]_time").enable(true);
		Ext.getCmp("hotline_interaction[credit_dep]").enable(true);

		
		Ext.getCmp("hotline_interaction[credit_presta]").setDisabled(false);
		
		if(Ext.getCmp("datehotline_interaction[duree_presta]_time").value != "00:00"){
				calculPresta(function (ticket) {
				Ext.getCmp("hotline_interaction[credit_presta]").setValue(ticket);
			});
		}
		
		if(Ext.getCmp("datehotline_interaction[duree_dep]_time").value != "00:00"){
			calculDep(function (ticket) {
				Ext.getCmp("hotline_interaction[credit_dep]").setValue(ticket);
			});
		}		

	}else{	

		Ext.getCmp("datehotline_interaction[heure_depart_dep]_time").setDisabled(true);

		
		Ext.getCmp('datehotline_interaction[heure_depart_dep]_time').setValue(Ext.getCmp('datehotline_interaction[heure_debut_presta]_time').value);
		Ext.getCmp('hotline_interaction[heure_depart_dep]').setValue(Ext.getCmp('datehotline_interaction[heure_debut_presta]_time').value);

		Ext.getCmp('datehotline_interaction[heure_arrive_dep]_time').setValue(Ext.getCmp('datehotline_interaction[heure_fin_presta]_time').value);
		Ext.getCmp('hotline_interaction[heure_arrive_dep]').setValue(Ext.getCmp('datehotline_interaction[heure_fin_presta]_time').value);



		Ext.getCmp("datehotline_interaction[heure_arrive_dep]_time").setDisabled(true);
		Ext.getCmp("hotline_interaction[credit_dep]").setDisabled(true);
		Ext.getCmp("hotline_interaction[credit_dep]").setValue(0);
		Ext.getCmp("datehotline_interaction[duree_dep]_time").setValue("00:00");
		Ext.getCmp("hotline_interaction[duree_dep]").setValue("00:00");
		
		if(val.data.myId == "internal"){
			Ext.getCmp("hotline_interaction[credit_presta]").setDisabled(true);
			Ext.getCmp("hotline_interaction[credit_presta]").setValue(0);


			Ext.getCmp("hotline_interaction[send_mail]").setValue("non");
			Ext.getCmp("hotline_interaction[visible]").setValue("non");

			arr = Ext.query("ul.send_mail_switch li");
			for (var i = 0, len = arr.length; i < len; i++) { 
				var el = Ext.get(arr[i]);
				if(i == 0){
					el.removeClass("on");
				}else{
					el.addClass("on");
				}
			}

			arr = Ext.query("ul.visible_switch li");
			for (var i = 0, len = arr.length; i < len; i++) { 
				var el = Ext.get(arr[i]);
				if(i == 0){
					el.removeClass("on");
				}else{
					el.addClass("on");
				}
			}
		}else{
			Ext.getCmp("hotline_interaction[credit_presta]").setDisabled(false);
			Ext.getCmp("hotline_interaction[credit_presta]").setValue(0);			
		}
	}

}



/** Permet de calculer la durée de prestation et le nombre de ticket de presta
* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
*/
ATF.selectHeurePresta = function (el,val,lastVal) {

	if(el.id  == "datehotline_interaction[heure_debut_presta]_time"){
		Ext.getCmp('hotline_interaction[heure_debut_presta]').setValue(val.data.field1);
		if(Ext.getCmp("combohotline_interaction[nature]").value !== "interaction"){			
			Ext.getCmp('hotline_interaction[heure_depart_dep]').setValue(val.data.field1);
			Ext.getCmp('datehotline_interaction[heure_depart_dep]_time').setValue(val.data.field1);
		}

	}else{
		Ext.getCmp('hotline_interaction[heure_fin_presta]').setValue(val.data.field1);
		Ext.getCmp('hotline_interaction[heure_arrive_dep]').setValue(val.data.field1);
		Ext.getCmp('datehotline_interaction[heure_arrive_dep]_time').setValue(val.data.field1);
	}	
	calculPresta(function (ticket) {
		Ext.getCmp("hotline_interaction[credit_presta]").setValue(ticket);
	});

	calculDep(function (ticket) {
		Ext.getCmp("hotline_interaction[credit_dep]").setValue(ticket);
	});
}

ATF.changeHeurePresta = function (el,val,lastVal) {

	if(el.id  == "datehotline_interaction[heure_debut_presta]_time"){
		Ext.getCmp('hotline_interaction[heure_debut_presta]').setValue(val);
		Ext.getCmp('datehotline_interaction[heure_debut_presta]_time').setValue(val);

		if(Ext.getCmp("combohotline_interaction[nature]").value !== "interaction"){			
			Ext.getCmp('hotline_interaction[heure_depart_dep]').setValue(val);
			Ext.getCmp('datehotline_interaction[heure_depart_dep]_time').setValue(val);
		}

	}else{
		Ext.getCmp('hotline_interaction[heure_fin_presta]').setValue(val);
		Ext.getCmp('datehotline_interaction[heure_fin_presta]_time').setValue(val);
		Ext.getCmp('hotline_interaction[heure_arrive_dep]').setValue(val);
		Ext.getCmp('datehotline_interaction[heure_arrive_dep]_time').setValue(val);
	}	
	calculPresta(function (ticket) {
		Ext.getCmp("hotline_interaction[credit_presta]").setValue(ticket);
	});	

	calculDep(function (ticket) {
		Ext.getCmp("hotline_interaction[credit_dep]").setValue(ticket);
	});
}

ATF.changeDureePause = function (el,val, lastVal) { 
	Ext.getCmp('hotline_interaction[duree_pause]').setValue(val);
	Ext.getCmp('datehotline_interaction[duree_pause]_time').setValue(val);

	calculPresta(function (ticket) {
		Ext.getCmp("hotline_interaction[credit_presta]").setValue(ticket);
	});
}

ATF.selectDureePause = function (el,val, lastVal) { 

	Ext.getCmp('hotline_interaction[duree_pause]').setValue(val.data.field1);
	Ext.getCmp('datehotline_interaction[duree_pause]_time').setValue(val.data.field1);

	calculPresta(function (ticket) {
		Ext.getCmp("hotline_interaction[credit_presta]").setValue(ticket);
	});
}

ATF.selectDureePresta = function (el,val, lastVal) {	
		
	Ext.getCmp('hotline_interaction[duree_presta]').setValue(val.data.field1);
	Ext.getCmp('datehotline_interaction[duree_presta]_time').setValue(val.data.field1);

	var debut  = Ext.getCmp('hotline_interaction[heure_fin_presta]').value;
	var fin = Ext.getCmp('hotline_interaction[duree_presta]').value;

	spdebut = debut.split(":");
	spfin = fin.split(":");

	debut = parseInt(spdebut[0])*60 + parseInt(spdebut[1]);
	fin = parseInt(spfin[0])*60 + parseInt(spfin[1]);

	heureFin = debut - fin;	
	
	hour = parseInt(heureFin/60);
	min = heureFin- (parseInt(heureFin/60)*60);

	if(min<10) min = "0"+min;

	heureFin = hour+":"+min;

	Ext.getCmp('hotline_interaction[heure_debut_presta]').setValue(heureFin);
	Ext.getCmp('datehotline_interaction[heure_debut_presta]_time').setValue(heureFin);

	if(Ext.getCmp("combohotline_interaction[nature]").value !== "interaction"){			
		Ext.getCmp('hotline_interaction[heure_depart_dep]').setValue(heureFin);
		Ext.getCmp('datehotline_interaction[heure_depart_dep]_time').setValue(heureFin);
	}

	
	res = ReturnDebutFinSplit( 'hotline_interaction[heure_depart_dep]', 'hotline_interaction[heure_debut_presta]');

	debut = res[0];
	fin = res[1];	

	duree = fin - debut;

	if(duree < 0){
		Ext.getCmp('hotline_interaction[heure_depart_dep]').setValue(heureFin);
		Ext.getCmp('datehotline_interaction[heure_depart_dep]_time').setValue(heureFin);
	}

	calculPresta(function (ticket) {
		Ext.getCmp("hotline_interaction[credit_presta]").setValue(ticket);
	});

	calculDep(function (ticket) {
		Ext.getCmp("hotline_interaction[credit_dep]").setValue(ticket);
	});
}

ATF.changeDureePresta = function (el,val, lastVal) {	
	Ext.getCmp('hotline_interaction[duree_presta]').setValue(val);
	Ext.getCmp('datehotline_interaction[duree_presta]_time').setValue(val);

	res = ReturnDebutFinSplit('hotline_interaction[heure_fin_presta]' , 'hotline_interaction[duree_presta]');

	debut = res[0];
	fin = res[1];	

	heureFin = debut - fin;	
		
	hour = parseInt(heureFin/60);
	min = heureFin- (parseInt(heureFin/60)*60);

	if(min<10) min = "0"+min;

	heureFin = hour+":"+min;

	Ext.getCmp('hotline_interaction[heure_debut_presta]').setValue(heureFin);
	Ext.getCmp('datehotline_interaction[heure_debut_presta]_time').setValue(heureFin);
	
	if(Ext.getCmp("combohotline_interaction[nature]").value !== "interaction"){			
		Ext.getCmp('hotline_interaction[heure_depart_dep]').setValue(heureFin);
		Ext.getCmp('datehotline_interaction[heure_depart_dep]_time').setValue(heureFin);
	}

	res = ReturnDebutFinSplit( 'hotline_interaction[heure_depart_dep]', 'hotline_interaction[heure_debut_presta]');

	debut = res[0];
	fin = res[1];	

	duree = fin - debut;

	if(duree < 0){
		Ext.getCmp('hotline_interaction[heure_depart_dep]').setValue(heureFin);
		Ext.getCmp('datehotline_interaction[heure_depart_dep]_time').setValue(heureFin);
	}


	calculPresta(function (ticket) {
		Ext.getCmp("hotline_interaction[credit_presta]").setValue(ticket);
	});

	calculDep(function (ticket) {
		Ext.getCmp("hotline_interaction[credit_dep]").setValue(ticket);
	});
}



/** Permet de calculer la durée de deplacement et le nombre de ticket de presta
* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
*/
ATF.selectHeureDep = function (el,val,lastVal) {

	if(el.id  == "datehotline_interaction[heure_depart_dep]_time"){
		Ext.getCmp('hotline_interaction[heure_depart_dep]').setValue(val.data.field1);
		Ext.getCmp('datehotline_interaction[heure_depart_dep]_time').setValue(val.data.field1);
		Ext.getCmp('hotline_interaction[heure_debut_presta]').setValue(val.data.field1);
		Ext.getCmp('datehotline_interaction[heure_debut_presta]_time').setValue(val.data.field1);

	}else{
		Ext.getCmp('hotline_interaction[heure_arrive_dep]').setValue(val.data.field1);
		Ext.getCmp('datehotline_interaction[heure_arrive_dep]_time').setValue(val.data.field1);
	}

	calculDep(function (ticket) {
		Ext.getCmp("hotline_interaction[credit_dep]").setValue(ticket);
	});

	calculPresta(function (ticket) {
		Ext.getCmp("hotline_interaction[credit_presta]").setValue(ticket);
	});
}

ATF.changeHeureDep = function (el,val,lastVal) {

	if(el.id  == "datehotline_interaction[heure_depart_dep]_time"){
		Ext.getCmp('hotline_interaction[heure_depart_dep]').setValue(val);
		Ext.getCmp('datehotline_interaction[heure_depart_dep]_time').setValue(val);
		Ext.getCmp('hotline_interaction[heure_debut_presta]').setValue(val);
		Ext.getCmp('datehotline_interaction[heure_debut_presta]_time').setValue(val);
	}else{
		Ext.getCmp('hotline_interaction[heure_arrive_dep]').setValue(val);
		Ext.getCmp('datehotline_interaction[heure_arrive_dep]_time').setValue(val);
	}

	calculDep(function (ticket) {
		Ext.getCmp("hotline_interaction[credit_dep]").setValue(ticket);
	});

	calculPresta(function (ticket) {
		Ext.getCmp("hotline_interaction[credit_presta]").setValue(ticket);
	});
}


function ReturnDebutFinSplit(CmpDebut , CmpFin){
	var debut  = Ext.getCmp(CmpDebut).value;
	var fin = Ext.getCmp(CmpFin).value;

	spdebut = debut.split(":");
	spfin = fin.split(":");

	debut = parseInt(spdebut[0])*60 + parseInt(spdebut[1]);
	fin = parseInt(spfin[0])*60 + parseInt(spfin[1]);

	return [debut, fin];
}



ATF.changeCreditPresta = function (el,val, lastVal) {
	calculPresta(function (ticket) {
		if(Ext.getCmp("combohotline_interaction[nature]").value == "interaction"){
			if(val < ticket){
				Ext.getCmp("hotline_interaction[champ_alerte]").setValue("");
				Ext.getCmp("hotline_interaction[champ_alerte]").emptyText = "Vous souhaitez offrir tout ou partie de la prestation ou du déplacement : merci d'indiquer ici pourquoi vous dérogez à la règle de calcul prévue !";
				Ext.getCmp("hotline_interaction[champ_alerte]").applyEmptyText();
				Ext.getCmp("hotline_interaction[champ_alerte]").show();
			}
		}
		
	});


		
}	

ATF.changeCreditDep = function (el,val, lastVal) {
	calculDep(function (ticket) {
		if(Ext.getCmp("combohotline_interaction[nature]").value == "interaction"){
			if(val < ticket){
				Ext.getCmp("hotline_interaction[champ_alerte]").setValue("");
				Ext.getCmp("hotline_interaction[champ_alerte]").emptyText = "Vous souhaitez offrir tout ou partie de la prestation ou du déplacement : merci d'indiquer ici pourquoi vous dérogez à la règle de calcul prévue !";
				Ext.getCmp("hotline_interaction[champ_alerte]").applyEmptyText();
				Ext.getCmp("hotline_interaction[champ_alerte]").show();
			}
		}
	});
}	



function calculDep(cb){
	hour = min = 0;
	
		res  = ReturnDebutFinSplit('hotline_interaction[heure_depart_dep]' , 'hotline_interaction[heure_arrive_dep]');
		res2 = ReturnDebutFinSplit('hotline_interaction[heure_debut_presta]' , 'hotline_interaction[heure_fin_presta]');

		debutDep = res[0];
		debutPresta = res2[0]
		finDep = res[1];
		finPresta = res2[1];

		if(finDep - finPresta > 0){
			duree = (debutPresta - debutDep) + (finDep - finPresta);
		}else{
			duree = (debutPresta - debutDep);
		}
		

		if(duree < 0) duree = 0;

		hour = parseInt(duree/60);
		min = duree - (parseInt(duree/60)*60);
		
		if(min<10) min = "0"+min;

		duree = hour+":"+min;

		Ext.getCmp("hotline_interaction[duree_dep]").setValue(duree);
		Ext.getCmp("datehotline_interaction[duree_dep]_time").setValue(duree);

		min = parseInt(min);

	if(Ext.getCmp("combohotline_interaction[nature]").value == "interaction"){
		var ticket = ((100/60)*min)/100;
		ticket = ticket.toFixed(2);
		ticket =  parseFloat(ticket) + hour;
		ticket = ticket.toFixed(2);
	}else{
		ticket = 0;
	}

	if(hour+min > 0){
		var a_facturer = estAFacturer(function (a_facturer){
			if(a_facturer == true){
				var au_forfait = estAuForfait(function (au_forfait){							
					if(au_forfait !== "0.00"){
						cb(au_forfait);
					}else{
						cb(ticket);
					}
				});			
			}else{
				Ext.getCmp('hotline_interaction[credit_dep]').setDisabled(true);
				Ext.getCmp('hotline_interaction[credit_presta]').setDisabled(true);
				cb(0);
			}	
		});
	}else{
		cb(ticket);
	}
		
}

function calculPresta(cb){	
	
		res = ReturnDebutFinSplit('hotline_interaction[heure_debut_presta]' , 'hotline_interaction[heure_fin_presta]');

		debut = res[0];
		fin = res[1];

		if(Ext.getCmp("datehotline_interaction[duree_pause]_time").value != "00:00"){
			pause = ReturnDebutFinSplit('datehotline_interaction[duree_pause]_time' , 'hotline_interaction[heure_fin_presta]');
			pause = pause[0];
			
			duree_ss_pause = fin - debut - pause;	
			hour_ss_pause = parseInt(duree_ss_pause/60);
			min_ss_pause = duree_ss_pause - (parseInt(duree_ss_pause/60)*60);			
		}
		
		duree = fin - debut;

		hour = parseInt(duree/60);
		min = duree - (parseInt(duree/60)*60);
				

		if(min<10) min = "0"+min;

		duree = hour+":"+min;

		Ext.getCmp("hotline_interaction[duree_presta]").setValue(duree);
		Ext.getCmp("datehotline_interaction[duree_presta]_time").setValue(duree);
		
		if(Ext.getCmp("datehotline_interaction[duree_pause]_time").value != "00:00"){
			hour = hour_ss_pause;
			min = min_ss_pause;
		}

		min = parseInt(min);

	if(Ext.getCmp("combohotline_interaction[nature]").value == "interaction"){
		var ticket = ((100/60)*min)/100;
		ticket = ticket.toFixed(2);
		ticket =  parseFloat(ticket) + hour;
		ticket = ticket.toFixed(2);

	}else{
		ticket = 0;
	}

	var a_facturer = estAFacturer(function (a_facturer){
		if(a_facturer == true){
			cb(ticket);
		}else{
			Ext.getCmp('hotline_interaction[credit_dep]').setDisabled(true);
			Ext.getCmp('hotline_interaction[credit_presta]').setDisabled(true);
			cb(0);
		}	
	});
}

function estAuForfait(cb){
	var id_hotline = null;
	var id_hotline_interaction = null;
	if(document.getElementsByName("hotline_interaction[id_hotline]")[0] !== undefined){
		var id_hotline = document.getElementsByName("hotline_interaction[id_hotline]")[0]["value"];
	}else{
		var id_hotline_interaction = document.getElementsByName("hotline_interaction[id_hotline_interaction]")[0]["value"];
	}

	Ext.Ajax.request({
		params:{ "id_hotline" : id_hotline, "id_hotline_interaction" :  id_hotline_interaction }
		,url: 'hotline,estAuForfait.ajax'
		,method:'POST'	
		,success: function (r,e) {
			var res = JSON.parse(r.responseText);
			cb(res.result);
		}
	});
}

function estAFacturer(cb){
	cb(true);
	/*var id_hotline = null;
	var id_hotline_interaction = null;
	if(document.getElementsByName("hotline_interaction[id_hotline]")[0] !== undefined){
		var id_hotline = document.getElementsByName("hotline_interaction[id_hotline]")[0]["value"];
	}else{
		var id_hotline_interaction = document.getElementsByName("hotline_interaction[id_hotline_interaction]")[0]["value"];
	}
	

	Ext.Ajax.request({
		params:{ "id_hotline" : id_hotline, "id_hotline_interaction" :  id_hotline_interaction }
		,url: 'hotline,getModeFacturation.ajax'
		,method:'POST'	
		,success: function (r,e) {
			var res = JSON.parse(r.responseText);
			cb(res.result);
		}
	});*/
}


