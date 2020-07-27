{strip}
{* Ajout du champ nécessaire pour ce renderer *}
{util::push($fieldsKeys,"commande.etat")}
/* Pour les dates ! */
{util::push($fieldsKeys,"commande.date_debut")}
{util::push($fieldsKeys,"commande.date_evolution")}
{util::push($fieldsKeys,"commande.retour_contrat")}
{util::push($fieldsKeys,"commande.retour_prel")}
{util::push($fieldsKeys,"commande.retour_pv")}

{util::push($fieldsKeys,"commande.date_demande_resiliation")}
{util::push($fieldsKeys,"commande.date_prevision_restitution")}
{util::push($fieldsKeys,"commande.date_restitution_effective")}

/* Pour les prolongations */
{util::push($fieldsKeys,"prolongationAllow")}
/* Pour les BDC */
{util::push($fieldsKeys,"bdcExist")}
/* Pour les Deùmande refi */
{util::push($fieldsKeys,"demandeRefiExist")}
/* Pour les Factures */
{util::push($fieldsKeys,"factureAllow")}
{util::push($fieldsKeys,"id_affaireCrypt")}
/* Pour les Ventes */
{util::push($fieldsKeys,"vente")}
/* Pour les Fichiers */
{util::push($fieldsKeys,"envoiContratEtBilanExists")}
{util::push($fieldsKeys,"envoiContratSsBilanExists")}
{util::push($fieldsKeys,"envoiAvenantExists")}
{util::push($fieldsKeys,"contratTransfertExists")}
{util::push($fieldsKeys,"ctSigneExists")}
{util::push($fieldsKeys,"CourrierRestitutionExists")}
{util::push($fieldsKeys,"allowBDCCreate")}
{util::push($fieldsKeys,"allowAllBDCCreate")}
{if ATF::$codename == "cleodis"} {util::push($fieldsKeys,"ctSGEFExists")} {/if}
{if ATF::$codename == "cleodisbe"}{util::push($fieldsKeys,"ctlettreBelfiusExists")} {/if}

{util::push($fieldsKeys,"envoiCourrierClassiqueExists")}


ATF.renderer.dateCleCommande=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		var idDiv = Ext.id();
		var id = record.data[table+'__dot__id_'+table];
		if (!record.data.vente) {
			(function(){
				var params = {
					renderTo: idDiv,
					bodyStyle:'background-color:transparent; border:0px',
					defaults: {
						xtype: 'datefield',
						format: 'd-m-Y',
						width:150,
						labelStyle: 'width:125px;'
						{if ATF::$codename=="midas"}
							,disabled:true
						{/if}
					},
					items:[{
						id:"date_debut"+id,
						name:"date_debut",
						fieldLabel: '{ATF::$usr->trans(date_debut,commande)}',
						value:record.data.commande__dot__date_debut,
						listeners: {
							'select': function(fb, v){
								ATF.ajax("commande,updateDate.ajax","id_commande="+id+"&table={ATF::_r('parent_name')|default:ATF::_r('table')}&key=date_debut&value="+fb.value);
								store.reload();
							}
						}
					},{
						id:"date_evolution"+id,
						name:"date_evolution",
						fieldLabel:"{ATF::$usr->trans('date_evolution','commande')}",
						value:record.data.commande__dot__date_evolution,
						listeners: {
							'select': function(fb, v){
								ATF.ajax("commande,updateDate.ajax","id_commande="+id+"&table={ATF::_r('parent_name')|default:ATF::_r('table')}&key=date_evolution&value="+fb.value);
								store.reload();
							}
						}
					},{
						id:"retour_contrat"+id,
						name:"retour_contrat",
						fieldLabel:"{ATF::$usr->trans('retour_contrat','commande')}",
						value:record.data.commande__dot__retour_contrat,
						listeners: {
							'select': function(fb, v){
								ATF.ajax("commande,updateDate.ajax","id_commande="+id+"&table={ATF::_r('parent_name')|default:ATF::_r('table')}&key=retour_contrat&value="+fb.value);
								store.reload();
							}
						}
					},{
						id:"retour_prel"+id,
						name:"retour_prel",
						fieldLabel:"{ATF::$usr->trans('retour_prel','commande')}",
						value:record.data.commande__dot__retour_prel,
						listeners: {
							'select': function(fb, v){
								ATF.ajax("commande,updateDate.ajax","id_commande="+id+"&table={ATF::_r('parent_name')|default:ATF::_r('table')}&key=retour_prel&value="+fb.value);
								store.reload();
							}
						}
					},{
						id:"retour_pv"+id,
						name:"retour_pv",
						fieldLabel:"{ATF::$usr->trans('retour_pv','commande')}",
						value:record.data.commande__dot__retour_pv,
						listeners: {
							'select': function(fb, v){
								ATF.ajax("commande,updateDate.ajax","id_commande="+id+"&table={ATF::_r('parent_name')|default:ATF::_r('table')}&key=retour_pv&value="+fb.value);
								store.reload();
							}
						}
					},{
						id:"date_demande_resiliation"+id,
						name:"date_demande_resiliation",
						fieldLabel:"{ATF::$usr->trans('date_demande_resiliation','commande')}",
						value:record.data.commande__dot__date_demande_resiliation,
						listeners: {
							'select': function(fb, v){
								ATF.ajax("commande,updateDate.ajax","id_commande="+id+"&table={ATF::_r('parent_name')|default:ATF::_r('table')}&key=date_demande_resiliation&value="+fb.value);
								store.reload();
							}
						}
					},{
						id:"date_prevision_restitution"+id,
						name:"date_prevision_restitution",
						fieldLabel:"{ATF::$usr->trans('date_prevision_restitution','commande')}",
						value:record.data.commande__dot__date_prevision_restitution,
						listeners: {
							'select': function(fb, v){
								ATF.ajax("commande,updateDate.ajax","id_commande="+id+"&table={ATF::_r('parent_name')|default:ATF::_r('table')}&key=date_prevision_restitution&value="+fb.value);
								store.reload();
							}
						}
					},{
						id:"date_restitution_effective"+id,
						name:"date_restitution_effective",
						fieldLabel:"{ATF::$usr->trans('date_restitution_effective','commande')}",
						value:record.data.commande__dot__date_restitution_effective,
						listeners: {
							'select': function(fb, v){
								ATF.ajax("commande,updateDate.ajax","id_commande="+id+"&table={ATF::_r('parent_name')|default:ATF::_r('table')}&key=date_restitution_effective&value="+fb.value);
								store.reload();
							}
						}
					}],
				};
				var p = new Ext.FormPanel(params);
			}).defer(25);
		}


		html = '<div id="'+idDiv+'"></div>';

		return html;
	}
};


ATF.renderer.pdfCommande=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		var idDiv = Ext.id();
		var id = record.data[table+'__dot__id_'+table];
		var html = "";



		html += '<a href="contratA3-'+id+'.pdf" target="_blank">';
		html += '<img src="{ATF::$staticserver}images/icones/pdf.png" />'+ATF.usr.trans('contratA3','commande');
		html += '</a><br /><hr>';

		html += '<a href="contratA4-'+id+'.pdf" target="_blank">';
		html += '<img src="{ATF::$staticserver}images/icones/pdf.png" />'+ATF.usr.trans('contratA4','commande');
		html += '</a><br /><hr>';

		html += '<a href="mandatSepa-'+id+'.pdf" target="_blank">';
		html += '<img src="{ATF::$staticserver}images/icones/pdf.png" />'+ATF.usr.trans('mandatSepa','commande');
		html += '</a><br /><hr>';




		{if ATF::$codename!="midas"}
			html += '<a href="contratAP-'+id+'.pdf" target="_blank">';
			html += '<img src="{ATF::$staticserver}images/icones/pdf.png" />'+ATF.usr.trans('contratAP','commande');
			html += '</a><br /><hr>';

			html += '<a href="contratPV-'+id+'.pdf" target="_blank">';
			html += '<img src="{ATF::$staticserver}images/icones/pdf.png" />'+ATF.usr.trans('contratPV','commande');
			html += '</a><br /><hr>';
		{/if}


		return '<div id="'+idDiv+'">'+html+'</div>';
	}
};

ATF.renderer.pdfCommandeLangue=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		var idDiv = Ext.id();
		var id = record.data[table+'__dot__id_'+table];
		var html = "";

		var langue = record.json["langue"];
		if (record.json["langue"] === "FR") langue = "NL";

		/*html += '<a href="contratA3-'+id+'.pdf" target="_blank">';
		html += '<img src="{ATF::$staticserver}images/icones/pdf.png" />'+ATF.usr.trans('contratA3','commande');
		html += '</a><br /><hr>';*/

		html += '<a href="contratA4'+langue+'-'+id+'.pdf" target="_blank">';
		html += '<img src="{ATF::$staticserver}images/icones/pdf.png" />'+ATF.usr.trans('contratA4','commande')+' ('+langue+')';
		html += '</a><br /><hr>';

		html += '<a href="mandatSepa'+langue+'-'+id+'.pdf" target="_blank">';
		html += '<img src="{ATF::$staticserver}images/icones/pdf.png" />'+ATF.usr.trans('mandatSepa','commande')+' ('+langue+')';
		html += '</a><br /><hr>';




		{if ATF::$codename!="midas"}
			/*html += '<a href="contratAP'+record.json["langue"]+'-'+id+'.pdf" target="_blank">';
			html += '<img src="{ATF::$staticserver}images/icones/pdf.png" />'+ATF.usr.trans('contratAP','commande')+' ('+record.json["langue"]+')';
			html += '</a><br /><hr>';*/

			html += '<a href="contratPV'+langue+'-'+id+'.pdf" target="_blank">';
			html += '<img src="{ATF::$staticserver}images/icones/pdf.png" />'+ATF.usr.trans('contratPV','commande')+' ('+langue+')';
			html += '</a><br /><hr>';
		{/if}


		return '<div id="'+idDiv+'">'+html+'</div>';


	}
};


ATF.renderer.pdfCourriers=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		var idDiv = Ext.id();
		var id = record.data[table+'__dot__id_'+table];

		var html = "";

		if (record.data.envoiCourrierClassiqueExists==true) {
			html += '<a href="commande-select-envoiCourrierClassique-'+id+'.dl" target="_blank">';
			html += '<img src="{ATF::$staticserver}images/icones/pdf.png" />'+ATF.usr.trans('Courrier classique','commande');
			html += '</a><br /><hr>';
		}

		if (record.data.envoiContratEtBilanExists==true) {
			html += '<a href="commande-select-envoiContratEtBilan-'+id+'.dl" target="_blank">';
			html += '<img src="{ATF::$staticserver}images/icones/pdf.png" />'+ATF.usr.trans('Contrat avec demande de bilan','commande');
			html += '</a><br /><hr>';
		}
		if (record.data.envoiContratSsBilanExists==true) {
			html += '<a href="commande-select-envoiContratSsBilan-'+id+'.dl" target="_blank">';
			html += '<img src="{ATF::$staticserver}images/icones/pdf.png" />'+ATF.usr.trans('Contrat sans demande de bilan','commande');
			html += '</a><br /><hr>';
		}

		if (record.data.envoiAvenantExists==true) {
			html += '<a href="commande-select-envoiAvenant-'+id+'.dl" target="_blank">';
			html += '<img src="{ATF::$staticserver}images/icones/pdf.png" />'+ATF.usr.trans('Avenant','commande');
			html += '</a><br /><hr>';
		}

		if (record.data.contratTransfertExists==true) {
			html += '<a href="commande-select-contratTransfert-'+id+'.dl" target="_blank">';
			html += '<img src="{ATF::$staticserver}images/icones/pdf.png" />'+ATF.usr.trans('Contrat de transfert','commande');
			html += '</a><br /><hr>';
		}

		if (record.data.ctSigneExists==true) {
			html += '<a href="commande-select-ctSigne-'+id+'.dl" target="_blank">';
			html += '<img src="{ATF::$staticserver}images/icones/pdf.png" />'+ATF.usr.trans('Contrat signé','commande');
			html += '</a><br /><hr>';
		}

		if (record.data.CourrierRestitutionExists==true) {
			html += '<a href="commande-select-CourrierRestitution-'+id+'.dl" target="_blank">';
			html += '<img src="{ATF::$staticserver}images/icones/pdf.png" />'+ATF.usr.trans('Courrier de restitution','commande');
			html += '</a><br /><hr>';
		}

		if (record.data.contratTransfertExists==true) {
			html += '<a href="commande-select-contratTransfert-'+id+'.dl" target="_blank">';
			html += '<img src="{ATF::$staticserver}images/icones/pdf.png" />'+ATF.usr.trans('Contrat de transfert','commande');
			html += '</a><br /><hr>';
		}


		{if ATF::$codename == "cleodis"}
			if (record.data.ctSGEFExists==true) {
				html += '<a href="commande-select-lettreSGEF-'+id+'.dl" target="_blank">';
				html += '<img src="{ATF::$staticserver}images/icones/pdf.png" />'+ATF.usr.trans('Contrat vente SGEF','commande');
				html += '</a><br /><hr>';
			}
			{util::push($fieldsKeys,"")}
		{/if}
		{if ATF::$codename == "cleodisbe"}
			if (record.data.ctlettreBelfiusExists==true) {
				html += '<a href="commande-select-lettreBelfius-'+id+'.dl" target="_blank">';
				html += '<img src="{ATF::$staticserver}images/icones/pdf.png" />'+ATF.usr.trans('Contrat vente Belfius','commande');
				html += '</a><br /><hr>';
			}
		{/if}


		return '<div id="'+idDiv+'">'+html+'</div>';
	}
};




{if ATF::$usr->privilege('prolongation','insert')}
	ATF.renderer.cmdAction=function(table,field) {
		return function(filetype, meta, record, rowIndex, colIndex, store) {
			var idDiv = Ext.id();
			var id = record.data[table+'__dot__id_'+table];
			var id_affaire = record.data['id_affaireCrypt'];
			var etat = record.data[table+'__dot__etat'];
			var html = "";
			/* Prolongation Expand */
			if (record.data.prolongationAllow) {
				html += '<p>';
				html += '<a href="#prolongation-insert.html,id_commande='+id+'">';
				html += '<img src="{ATF::$staticserver}images/module/16/prolongation.png" />';
				html += ATF.usr.trans('creerProlongation','commande');
				html += '</a>&nbsp;&nbsp;&nbsp;';
				html += '</p>';
			}
			/* BDC Expand */
			/* if !ATF::bon_de_commande()->bdcByAffaire($id_commande) */
			if(record.data.allowBDCCreate){
				if (!record.data.bdcExist) {
					html += '<p>';
					html += '<a href="#bon_de_commande-insert.html,id_commande='+id+'">';
					html += '<img src="{ATF::$staticserver}images/module/16/bon_de_commande.png" />';
					html += ATF.usr.trans('creerBDC','commande');
					html += '</a>&nbsp;&nbsp;&nbsp;';
					html += '</p>';
				}
			}
			if(record.data.allowAllBDCCreate){
					html += '<p>';
					html += '<a href="javascript:;" onclick="if (confirm(\''+ATF.usr.trans('Etes_vous_sur')+'\')) ATF.ajax(\'bon_de_commande,createAllBDC.ajax\',\'id_commande='+id+'\');">';
					html += '<img src="{ATF::$staticserver}images/module/16/bon_de_commande.png" />';
					html += ATF.usr.trans('creerAllBDC','commande');
					html += '</a>&nbsp;&nbsp;&nbsp;';
					html += '</p>';
			}

			/* Demande refi Expand */
			if (!record.data.demandeRefiExist) {
				html += '<p>';
				html += '<a href="#demande_refi-insert.html,id_affaire='+id_affaire+'">';
				html += '<img src="{ATF::$staticserver}images/module/16/demande_refi.png" />';
				html += ATF.usr.trans('creerDemandeRefi','commande');
				html += '</a>&nbsp;&nbsp;&nbsp;';
				html += '</p>';
			}

			/* Facture Expand */
			if (record.data.factureAllow) {
				html += '<p>';
				html += '<a href="#facture-insert.html,id_commande='+id+'">';
				html += '<img src="{ATF::$staticserver}images/module/16/facture.png" />';
				html += ATF.usr.trans('creerFacture','commande');
				html += '</a>&nbsp;&nbsp;&nbsp;';
				html += '</p>';
			}

			if (etat=='mis_loyer' || etat=='prolongation' || etat=='restitution' || etat=='mis_loyer_contentieux' || etat=='prolongation_contentieux' || etat=='restitution_contentieux') {
				/* Stop CMD */
				html += '<p>';
				html += '<a href="javascript:;" onclick="if (confirm(\''+ATF.usr.trans('Etes_vous_sur')+'\')) ATF.ajax(\'commande,stopCommande.ajax\',\'id_commande='+id+'\');">';
				html += '<img src="{ATF::$staticserver}images/icones/stop.png" />';
				html += ATF.usr.trans('stop','commande');
				html += '</a>';
				html += '</p>';
			}

			if (etat=='arreter' || etat=='arreter_contentieux' ) {
				/* Reactive CMD */
				html += '<p>';
				html += '<a href="javascript:;" onclick="if (confirm(\''+ATF.usr.trans('Etes_vous_sur')+'\')) ATF.ajax(\'commande,reactiveCommande.ajax\',\'id_commande='+id+'\');">';
				html += '<img src="{ATF::$staticserver}images/icones/valid.png" />';
				html += ATF.usr.trans('reactive','commande');
				html += '</a>';
				html += '</p>';
			}


			if (!Ext.getCmp('myForm'+id)) {
				 var formPanel = new Ext.FormPanel({
					frame:true,
					autoHeight:true,
					id:'myForm'+id,
					name:'myFormName'+id,
					title: '',
					bodyStyle:'padding:5px 5px 0',
					items: [	{
							html: '<span class="bold">{ATF::$usr->trans("choixCourrier","commande")|escape:javascript}</span><hr>',
							xtype:'container'
						}
						,
						{
							xtype: 'combo'
							,name: 'combo_pdf'
							,hiddenName:'pdf'
							,id: 'choixCourrierPdf'
							,fieldLabel:'{ATF::$usr->trans("documentCR","commande")|escape:javascript}'
						    ,store: new Ext.data.ArrayStore({
						        fields: ['myId','displayText'],
						        data: [
						        	['envoiCourrierClassique', 'Courrier classique'],
						        	['envoiContratEtBilan', 'Contrat avec demande de bilan'],
						        	['envoiContratSsBilan', 'Contrat sans demande de bilan'],
						        	['envoiAvenant', 'Avenant'],
						        	['contratTransfert', 'Contrat de transfert'],
						        	['ctSigne', 'Contrat signé'],
						        	['CourrierRestitution', 'Courrier de restitution'],
						        	{if ATF::$codename == "cleodis"}
						        		['lettreSGEF', 'Contrat vente SGEF']
									{/if}
									{if ATF::$codename == "cleodisbe"}
										['lettreBelfius', 'Contrat vente Belfius']
									{/if}

						        ]
						    }),
						    valueField: 'myId',
						    displayField: 'displayText',
							typeAhead: true,
						    triggerAction: 'all',
						    lazyRender:true,
						    mode: 'local',
						    editable:false,
						    listeners: {
						    	select: function(field) {
						    		if(field.value == "lettreSGEF"){
						    			Ext.getCmp("bdc"+id).setVisible(false);
                        				Ext.getCmp("reprise_magasin"+id).setVisible(false);
                        				Ext.getCmp("docSupAretourner"+id).setVisible(false);
                        				Ext.getCmp("type_devis"+id).setVisible(false);
                        				Ext.getCmp("date_echeance"+id).setVisible(false);
                        				Ext.getCmp("rar"+id).setVisible(false);
                        				Ext.getCmp("num_contrat"+id).setVisible(false);
                        				Ext.getCmp("date_signature"+id).setVisible(true);
                        				Ext.getCmp("equipement"+id).setVisible(false);
                        			}else if(field.value =="lettreBelfius"){
                        				Ext.getCmp("bdc"+id).setVisible(false);
                        				Ext.getCmp("reprise_magasin"+id).setVisible(false);
                        				Ext.getCmp("docSupAretourner"+id).setVisible(false);
                        				Ext.getCmp("type_devis"+id).setVisible(false);
                        				Ext.getCmp("date_echeance"+id).setVisible(false);
                        				Ext.getCmp("rar"+id).setVisible(false);
                        				Ext.getCmp("num_contrat"+id).setVisible(true);
                        				Ext.getCmp("date_signature"+id).setVisible(true);
                        				Ext.getCmp("equipement"+id).setVisible(true);
						    		} else if(field.value == "envoiAvenant"){
                        				Ext.getCmp("bdc"+id).setVisible(true);
                        				Ext.getCmp("reprise_magasin"+id).setVisible(false);
                        				Ext.getCmp("docSupAretourner"+id).setVisible(false);
                        				Ext.getCmp("type_devis"+id).setVisible(false);
                        				Ext.getCmp("date_echeance"+id).setVisible(false);
                        				Ext.getCmp("rar"+id).setVisible(false);
                        				Ext.getCmp("num_contrat"+id).setVisible(false);
                        				Ext.getCmp("date_signature"+id).setVisible(false);
                        				Ext.getCmp("equipement"+id).setVisible(false);
                        			} else if (field.value == "contratTransfert") {
                        				Ext.getCmp("bdc"+id).setVisible(false);
                        				Ext.getCmp("reprise_magasin"+id).setVisible(true);
                        				Ext.getCmp("docSupAretourner"+id).setVisible(true);
                        				Ext.getCmp("type_devis"+id).setVisible(false);
                        				Ext.getCmp("date_echeance"+id).setVisible(false);
                        				Ext.getCmp("rar"+id).setVisible(false);
                        				Ext.getCmp("num_contrat"+id).setVisible(false);
                        				Ext.getCmp("date_signature"+id).setVisible(false);
                        				Ext.getCmp("equipement"+id).setVisible(false);
                        			} else if (field.value == "envoiContratEtBilan" || field.value == "envoiContratSsBilan" || field.value == "envoiCourrierClassique") {
                        				Ext.getCmp("bdc"+id).setVisible(false);
                        				Ext.getCmp("reprise_magasin"+id).setVisible(false);
                        				Ext.getCmp("docSupAretourner"+id).setVisible(false);
                        				Ext.getCmp("type_devis"+id).setVisible(true);
                        				Ext.getCmp("date_echeance"+id).setVisible(false);
                        				Ext.getCmp("rar"+id).setVisible(false);
                        				Ext.getCmp("num_contrat"+id).setVisible(false);
                        				Ext.getCmp("date_signature"+id).setVisible(false);
                        				Ext.getCmp("equipement"+id).setVisible(false);
                        			} else if (field.value == "CourrierRestitution") {
                        				Ext.getCmp("bdc"+id).setVisible(false);
                        				Ext.getCmp("reprise_magasin"+id).setVisible(false);
                        				Ext.getCmp("docSupAretourner"+id).setVisible(false);
                        				Ext.getCmp("type_devis"+id).setVisible(false);
                        				Ext.getCmp("date_echeance"+id).setVisible(true);
                        				Ext.getCmp("rar"+id).setVisible(true);
                        				Ext.getCmp("num_contrat"+id).setVisible(false);
                        				Ext.getCmp("date_signature"+id).setVisible(false);
                        				Ext.getCmp("equipement"+id).setVisible(false);
                        				ATF.ajax("commande,getDateResti.ajax"
                        						,"id_commande="+id
                        						,{ onComplete: function (result) {
                        								if(result.result){
                        									Ext.getCmp("date_echeance"+id).setValue(result.result);
                        								}
                        							}
                        						}
                        				);

                        			}else {
                        				Ext.getCmp("bdc"+id).setVisible(false);
                        				Ext.getCmp("reprise_magasin"+id).setVisible(false);
                        				Ext.getCmp("docSupAretourner"+id).setVisible(false);
                        				Ext.getCmp("type_devis"+id).setVisible(false);
                        				Ext.getCmp("date_echeance"+id).setVisible(false);
                        				Ext.getCmp("rar"+id).setVisible(false);
                        				Ext.getCmp("num_contrat"+id).setVisible(false);
                        				Ext.getCmp("date_signature"+id).setVisible(false);
                        				Ext.getCmp("equipement"+id).setVisible(false);
                        			}

                        		}
						    }
						},{
		                    xtype: 'textfield',
		                    fieldLabel: 'Type du devis',
		                    name : 'type_devis',
		                    id : 'type_devis'+id,
		                    hidden: true,
						},{
		                    xtype: 'textfield',
		                    fieldLabel: 'Bon de commande',
		                    name : 'bdc',
		                    id : 'bdc'+id,
		                    hidden: true,
						},{
							xtype: 'textfield',
		                    fieldLabel: 'Reprise Magasin',
		                    name : 'reprise_magasin',
		                    id : 'reprise_magasin'+id,
		                    hidden: true,
						},{
							xtype: 'textfield'
							,name: 'id_commande'
							,id: 'id_commande'
							,value: id
							,hidden:true
						},{
							 xtype: 'textfield',
		                    fieldLabel: 'Document supplémentaire a retourner',
		                    name:'docSupAretourner',
		                    id : 'docSupAretourner'+id,
		                    hidden: true,
						},{
							xtype: 'datefield',
		                    fieldLabel: 'Date d\'echeance',
		                    name : 'date_echeance',
		                    id : 'date_echeance'+id,
		                    hidden: true,
						},{
							xtype: 'textfield',
		                    fieldLabel: 'Numéro de recommandé',
		                    name : 'rar',
		                    id : 'rar'+id,
		                    hidden: true,
						},{
							xtype: 'textfield',
		                    fieldLabel: 'N° du Contrat de Financement',
		                    name:'num_contrat',
		                    id : 'num_contrat'+id,
		                    hidden: true,
						},{
							xtype: 'textfield',
		                    fieldLabel: 'Date de la signature',
		                    name:'date_signature',
		                    id : 'date_signature'+id,
		                    hidden: true,
						},{
							xtype: 'textfield',
		                    fieldLabel: 'Désignation de l\'Equipement',
		                    name:'equipement',
		                    id : 'equipement'+id,
		                    hidden: true,
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
									'extAction':'commande'
									,'extMethod':'generateCourrierType'
								}
								,success:function(form, action) {
									ATF.ajax_refresh(action.result,true);
									Ext.getCmp('myForm'+id).destroy();
									Ext.getCmp('mywindow'+id).destroy();
									Ext.getCmp(store.baseParams.pager).store.reload();
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
									'extAction':'commande'
									,'extMethod':'generateCourrierType'
									,'preview':'true'
								}
								,success:function(form, action) {

									if(action.result.result && action.result.fileToPrevisu){
										window.location='commande-select-'+action.result.fileToPrevisu+'-'+action.result.result+'.temp';
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
			}
			if (!Ext.getCmp('mywindow'+id)) {
				new Ext.Window({
					title: '{ATF::$usr->trans("creerCourrierType","commande")}',
					id:'mywindow'+id,
					plain:true,
					bodyStyle:'padding:5px;',
					width:600,
					buttonAlign:'center',
					items: [Ext.getCmp('myForm'+id)]
				});
			}


			html += '<p>';
			html += '<a href="javascript:;"  onclick="Ext.getCmp(\'mywindow'+id+'\').show();">';
			html += '<img src="{ATF::$staticserver}images/icones/pdf.png" />';
			html += ATF.usr.trans('creerCourrierType','commande');
			html += '</a>';
			html += '</p>';

			return '<div id="'+idDiv+'">'+html+'</div>';
		}
	};
{/if}


{if ATF::$codename=="midas"}
	ATF.renderer.uploadFileMidas=function(table,field) {
		return function(filetype, meta, record, rowIndex, colIndex, store) {
			if(record.json){
				var id = record.data[table+'__dot__id_'+table];

				html = '<div class="floatLeft" style="width:50%; text-align:center">';

				if (filetype) {
					html += '<a href="'+table+'-select-'+field+'-'+id+'.dl" alt="'+ATF.usr.trans("popup_download",table)+'">'+
						'<img class="smallIcon '+filetype+'" src="'+ATF.blank_png+'" class="icone" />'+
						'</a>';
				} else {
					html += '<img class="smallIcon warning" src="'+ATF.blank_png+'" />';
				}
				html += '</div>';

				return html;
			}
		}
	};
{/if}

{/strip}