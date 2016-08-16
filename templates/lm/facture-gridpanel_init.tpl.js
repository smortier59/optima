{strip}

ATF.renderer.typeFacture=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		var idDiv = Ext.id();
		var type = record.data[table+'__dot__type_facture'];
		/* Prolongation Expand */
		html = '<img class="smallIcon '+type+'" title="'+ATF.usr.trans(type,table)+'"  alt="'+ATF.usr.trans(type,table)+'" src="'+ATF.blank_png+'" />';
		
		if(type == "libre"){
			return '<div class="center" id="'+idDiv+'">'+html+'<br /><a href="javascript:;" onclick="ATF.ajax(\'facture,libreToNormale.ajax\',\'id_facture='+record.data[table+'__dot__id_facture']+'\');">
																     <img class="smallIcon '+type+'" title="'+ATF.usr.trans(type,table)+'"  alt="'+ATF.usr.trans(type,table)+'" src="'+ATF.blank_png+'" /> 
																     =>
																     <img class="smallIcon facture" title="'+ATF.usr.trans("facture",table)+'"  alt="'+ATF.usr.trans("facture",table)+'" src="'+ATF.blank_png+'" />
																    </a></div>';
		}else{
			return '<div class="center" id="'+idDiv+'">'+html+'</div>';
		}
		
	}
};

/* Pour les dates ! */
{util::push($fieldsKeys,"id_relance_premiere")}
{util::push($fieldsKeys,"id_relance_seconde")}
{util::push($fieldsKeys,"id_relance_mise_en_demeure")}
{util::push($fieldsKeys,"numRelance")}
{util::push($fieldsKeys,"id_autreFacture")}
{util::push($fieldsKeys,"ref_autreFacture")}
{util::push($fieldsKeys,"allowRelance")}

var trad = new Object();
trad["non_rejet"] = "Acceptée";
trad["non_preleve"] = "Pas prélevée";
trad["non_preleve_mandat"] = "Pas prélevée cause mandat";
trad["contestation_debiteur"] = "Contestation débiteur";
trad["provision_insuffisante"] = "Provision insuffisante";
trad["opposition_compte"] = "Opposition Compte";
trad["decision_judiciaire"] = "Décision judiciaire";
trad["compte_cloture"] = "Compte clôture";
trad["solde"] = "Solde";
trad["coor_banc_inexploitable"] = "Coordonnées bancaires inexploitables";
trad["pas_dordre_de_payer"] = "Pas d'ordre de payer";


/* 
	Renderer pour la relance
	@author Quentin JANON <qjanon@absystech.fr> 
*/
ATF.renderer.relanceFacture=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		var idDiv = Ext.id();
		var id = record.data[table+'__dot__id_facture'];
		var ref = record.data[table+'__dot__ref'];
		var allowRelance = true;

		var numRelance = record.data['numRelance'];
		var allowRelance = record.data['allowRelance'];
		if (record.data['id_relance_premiere']) var id_relance_premiere = record.data['id_relance_premiere'];
		if (record.data['id_relance_seconde']) var id_relance_seconde = record.data['id_relance_seconde'];
		if (record.data['id_relance_mise_en_demeure']) var id_relance_med = record.data['id_relance_mise_en_demeure'];
		if (record.data['id_autreFacture']) var id_autreFacture = record.data['id_autreFacture'];
		if (record.data['ref_autreFacture']) var ref_autreFacture = record.data['ref_autreFacture'];
		if (record.data['facture__dot__rejet']) var rejet = trad[record.data['facture__dot__rejet']];

		var btnRelance = {
			xtype:'button',
			id:"relance"+id,
			buttonText: '',
			buttonOnly: true,
			iconCls: 'btnRelance',
			cls:'floatLeft',
			disabled:!allowRelance,
			tooltip: '{ATF::$usr->trans("relance")}',
			tooltipType:'title',
			listeners: {
				'click': function(fb, v){
					if (!Ext.getCmp('myForm'+id)) {
						var storeRelance = new Ext.data.JsonStore({
							url : 'facture,getAllForRelance.ajax,id_facture='+id,
							root : 'result',
			                storeId: 'autreFactureStore',
			                idProperty: 'id',					                
			                autoLoad: true,
			                fields: [ 
								{ name:'value', mapping: 'id' },
								{ name:'text', mapping: 'reference' }
							]
							
						});
						
						
						var itemForFactureSelection = {
							xtype: 'multiselect'
							,width: 'auto'
							,allowBlank:true
							,fieldLabel:"Choisir d'autres factures de la même affaire"
							,minSelections:1
							,name : "autreFacture"
							,id : "autreFacture"
							,height: 70
							,store: storeRelance
							,valueField: 'value'
							,displayField: 'text'
							,hidden:dis															
						};
						var extraItem = null;
						var dlRelance = null;
						var fileToPrevisu = "relance1";
						var dis = false;
						if (id_relance_premiere) {
							var html = "";
							html += '<a href="relance-select-relance1-'+id_relance_premiere+'.dl" target="_blank">';
							html += '<img src="{ATF::$staticserver}images/icones/pdf.png" style="margin-right: 10px" />'+ATF.usr.trans('dl_relance_premiere','relance');
							html += '</a><hr>';
							dlRelance = new Ext.Panel({
								title:'<span class="bold">{ATF::$usr->trans("dl_relance","relance")}</span>'
								,items:[
									{
										xtype:'container'
										,style: {
											paddingLeft: '10px',
											paddingTop: '3px'
										}
										,html:html
									}
								]
							});
							fileToPrevisu = "relance2";
							dis = true;
							if (ref_autreFacture!=undefined) {
								itemForFactureSelection = {
									xtype:'container'
									,html: "<b>Factures concernées : </b>"+ref_autreFacture
									,height: 50
								};
							}
							extraItem = {
								xtype: 'textfield'
								,name: 'autreFacture' 
								,id: 'autreFacture'
								,value: id_autreFacture
								,hidden:true							
							};
						}
						
						if (id_relance_seconde) {
							var html = "";
							html += '<a href="relance-select-relance2-'+id_relance_seconde+'.dl" target="_blank">';
							html += '<img src="{ATF::$staticserver}images/icones/pdf.png" style="margin-right: 10px" />'+ATF.usr.trans('dl_relance_seconde','relance');
							html += '</a><hr>';
							dlRelance.add({
								xtype:'container'
								,style: {
									paddingLeft: '10px',
									paddingTop: '3px'
								}
								,html:html
							});
							fileToPrevisu = "relance3";
							dis = true;
						}
						
						var form = null;
						if (id_relance_med) {
							var html = "";
							html += '<a href="relance-select-relance3-'+id_relance_med+'.dl" target="_blank">';
							html += '<img src="{ATF::$staticserver}images/icones/pdf.png" style="margin-right: 10px" />'+ATF.usr.trans('dl_relance_med','relance');
							html += '</a><hr>';
							dlRelance.add({
								xtype:'container'
								,style: {
									paddingLeft: '10px',
									paddingTop: '3px'
								}
								,html:html
							});
							
						} else {
							var form = new Ext.FormPanel({
								frame:true,
								autoHeight:true,
								id:'myForm'+id,
								name:'myFormName'+id,
								title: '',
								bodyStyle:'padding:5px 5px 0',
								items: [
									{
										html: '<span class="bold">{ATF::$usr->trans("create_relance","relance")}</span><hr>',
										xtype:'container'
									},
										itemForFactureSelection
									,{
										xtype: 'textarea'
										,name: 'texte' 
										,id: 'texte'
										,fieldLabel:'{ATF::$usr->trans("texte","relance")|addslashes}'
										,style: {
											width: '99%',
											height:'120px'
										}
										,value: rejet
									},
									{
										xtype: 'textfield'
										,name: 'id_facture' 
										,id: 'id_facture'
										,value: id
										,hidden:true
									}
								],
							
								buttons: [{
									text: 'Ok',
									handler: function(a,b,c,d){
										Ext.getCmp('myForm'+id).getForm().submit({
											submitEmptyText:false,
											method  : 'post',
											waitMsg : '{ATF::$usr->trans(loading_new_page)|escape:javascript}',
											waitTitle :'{ATF::$usr->trans(loading)|escape:javascript}',
											url     : 'extjs.ajax',
											params: {
												'extAction':'relance'
												,'extMethod':'insert'
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
								},{
									text: '{ATF::$usr->trans(preview)|escape:javascript}',
									handler: function(){
										Ext.getCmp('myForm'+id).getForm().submit({
											submitEmptyText:false,
											method  : 'post',
											waitMsg : '{ATF::$usr->trans(generating_pdf)|escape:javascript}',
											waitTitle : '{ATF::$usr->trans(loading)|escape:javascript}',
											url     : 'extjs.ajax',
											params: {
												'extAction':'relance'
												,'extMethod':'insert'
												,'preview':'true'
											}
											,success:function(form, action) {
												if(action.result.result){
													window.location='relance-select-'+fileToPrevisu+'-'+action.result.result+'.temp';
												}else if(action.result.cadre_refreshed){
													ATF.ajax_refresh(action.result,true);
												}else {
													ATF.extRefresh(action); 
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
											}
											,timeout:3600
										});									
									}
								},{
									text: 'Annuler',
									handler: function(){
										Ext.getCmp('myForm'+id).destroy();
										Ext.getCmp('mywindow'+id).hide();
									}
								}]
							});
							
							if (extraItem) {
								Ext.getCmp('myForm'+id).add(extraItem);
							}
						}
					}
					
					if (!Ext.getCmp('mywindow'+id)) {
						var height = 0;
						var width = 0;
						new Ext.Window({
							title: '{ATF::$usr->trans("Faire une relance pour la facture : ")}'+ ref,
							id:'mywindow'+id,
							plain:true,
							bodyStyle:'padding:5px;',
							buttonAlign:'center'
						});
						if (dlRelance) {
							Ext.getCmp('mywindow'+id).add(dlRelance);
							height += 200;
							width = 350;
						}
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
				items:[btnRelance]
				
			};
			var p = new Ext.Container(params);
		}).defer(25);

		
		return '<div class="left" id="'+idDiv+'"></div>';
	}
};


/* 
	Renderer pour la modification d'un texte.
	@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> 
*/
ATF.renderer.updateEnumFactureRejetCledodis=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		if(record.json){ 
			var idDivUD = Ext.id();
			var id = record.data[table+'__dot__id_'+table];
			(function(){
				var params = {
					renderTo: idDivUD,
					bodyStyle:'background-color:transparent; border:0px',
					frame:false,
					border:false,
					defaults: {
						xtype:'combo'
						,width:180
						,hideLabel:true
						,typeAhead:true
						,triggerAction:'all'
						,editable:false
						,lazyRender:true
						,mode:'local'
						,preventMark:true
						,store: new Ext.data.ArrayStore({
							fields: [
								'myId',
								'displayText'
							],
							data: [
								['non_rejet','Acceptée'] , 								
								['non_preleve','Pas prélevée'],
								['non_preleve_mandat','Pas prélevée cause mandat'],
								['contestation_debiteur','Contestation débiteur'],
								['provision_insuffisante','Provision insuffisante'],
								['opposition_compte','Opposition Compte'],
								['decision_judiciaire','Décision judiciaire'],
								['compte_cloture','Compte clôture'],
								['solde','Solde'],
								['coor_banc_inexploitable','Coordonnées bancaires inexploitables'],
								['pas_dordre_de_payer','Pas d\'ordre de payer'],
								

							]
						})
						,value: record.data[table+'__dot__'+field]
						,valueField: 'myId'
						,displayField: 'displayText'
					},
					items:[{
						id:table+"updateEnumRejet"+id,
						name:"updateEnumRejet",
						value:record.data[table+'__dot__'+field],
						listeners :{
							select:function(f,n,o){
								ATF.ajax(table+',updateEnumRejet.ajax','id_'+table+'='+id+'&key='+field+'&value='+f.value);
								store.load();
							}
						}
					}]
				};
				var p = new Ext.FormPanel(params);
			}).defer(25);
					
			return '<div  id="'+idDivUD+'"></span>';
		}
	}
};

{/strip}