{strip}
{
	text: 'Interroger ICECAT',
	handler: function(el){
		if (!Ext.getCmp('produit[ref]').getValue()) {
			Ext.Msg.show({
			   title:'Champ manquant',
			   msg: 'Vous devez renseigner une référence pour interroger ICECAT',
			   buttons: Ext.Msg.OK,
			   animEl: 'elId',
			   icon: Ext.MessageBox.WARNING
			});
			return false;
		}

		if (!Ext.getCmp('produit[id_fabriquant]').getValue()) {
			Ext.Msg.show({
			   title:'Champ manquant',
			   msg: 'Vous devez renseigner un fabriquant pour interroger ICECAT',
			   buttons: Ext.Msg.OK,
			   animEl: 'elId2',
			   icon: Ext.MessageBox.WARNING
			});
			return false;
		}

		ATF.loadMask = ATF.createLoadMask(el.findParentByType('form').el);
		ATF.loadMask.show();

		ATF.ajax(
			"produit,getInfosFromICECAT,ajax"
			,"ref="+Ext.getCmp('produit[ref]').getValue()+"&id_fabriquant="+Ext.getCmp('produit[id_fabriquant]').getValue()
			, {
				onComplete: function (r) {
					if (r.result) {

						if (r.result.produit) {
							Ext.getCmp('produit[produit]').setValue(r.result.produit);
						} else {
							Ext.getCmp('produit[produit]').reset();
						}

						if (r.result.typeecran) {
							Ext.getCmp('produit[id_produit_typeecran]').setValue(r.result.typeecran.id_produit_typeecran);
							Ext.getCmp('label_produit[id_produit_typeecran]').setValue(r.result.typeecran.libelle);
						} else {
							Ext.getCmp('produit[id_produit_typeecran]').reset();
							Ext.getCmp('label_produit[id_produit_typeecran]').reset();
						}

						Ext.getCmp('label_produit[id_produit_typeecran]').removeClass('orange_border_textfield'); 
						if (r.result.typeecran) {
							if (r.result.typeecran.length>1) {
								Ext.getCmp('label_produit[id_produit_typeecran]').addClass('orange_border_textfield'); 
							} else {
								Ext.getCmp('produit[id_produit_typeecran]').setValue(r.result.typeecran[0].id_produit_typeecran);
								Ext.getCmp('label_produit[id_produit_typeecran]').setValue(r.result.typeecran[0].libelle);
							}
						} else {
							Ext.getCmp('produit[id_produit_typeecran]').reset();
							Ext.getCmp('label_produit[id_produit_typeecran]').reset();
						}

						Ext.getCmp('label_produit[id_produit_technique]').removeClass('orange_border_textfield'); 
						if (r.result.tailleecran) {
							if (r.result.tailleecran.length>1) {
								Ext.getCmp('label_produit[id_produit_viewable]').addClass('orange_border_textfield'); 
							} else {
								Ext.getCmp('produit[id_produit_viewable]').setValue(r.result.tailleecran[0].id_produit_tailleecran);
								Ext.getCmp('label_produit[id_produit_viewable]').setValue(r.result.tailleecran[0].libelle);
							}
						} else {
							Ext.getCmp('produit[id_produit_viewable]').reset();
							Ext.getCmp('label_produit[id_produit_viewable]').reset();
						}

						Ext.getCmp('label_produit[id_produit_technique]').removeClass('orange_border_textfield'); 
						if (r.result.tech_impression) {
							if (r.result.tech_impression.length>1) {
								Ext.getCmp('label_produit[id_produit_technique]').addClass('orange_border_textfield'); 
							} else {
								Ext.getCmp('produit[id_produit_technique]').setValue(r.result.tech_impression[0].id_produit_technique);
								Ext.getCmp('label_produit[id_produit_technique]').setValue(r.result.tech_impression[0].libelle);
							}
						} else {
							Ext.getCmp('produit[id_produit_technique]').reset();
							Ext.getCmp('label_produit[id_produit_technique]').reset();
						}

						Ext.getCmp('label_produit[id_produit_format]').removeClass('orange_border_textfield'); 
						if (r.result.format_impression) {
							if (r.result.format_impression.length>1) {
								Ext.getCmp('label_produit[id_produit_format]').addClass('orange_border_textfield'); 
							} else {
								Ext.getCmp('produit[id_produit_format]').setValue(r.result.format_impression[0].id_produit_format);
								Ext.getCmp('label_produit[id_produit_format]').setValue(r.result.format_impression[0].libelle);
							}
						} else {
							Ext.getCmp('produit[id_produit_format]').reset();
							Ext.getCmp('label_produit[id_produit_format]').reset();
						}

						Ext.getCmp('label_produit[id_produit_ram]').removeClass('orange_border_textfield'); 
						if (r.result.mem) {
							if (r.result.mem.length>1) {
								Ext.getCmp('label_produit[id_produit_ram]').addClass('orange_border_textfield'); 
							} else {
								Ext.getCmp('produit[id_produit_ram]').setValue(r.result.mem[0].id_produit_ram);
								Ext.getCmp('label_produit[id_produit_ram]').setValue(r.result.mem[0].libelle);
							}
						} else {
							Ext.getCmp('produit[id_produit_ram]').reset();
							Ext.getCmp('label_produit[id_produit_ram]').reset();
						}

						Ext.getCmp('label_produit[id_produit_lecteur]').removeClass('orange_border_textfield'); 
						if (r.result.lecteur) {
							if (r.result.lecteur.length>1) {
								Ext.getCmp('label_produit[id_produit_lecteur]').addClass('orange_border_textfield'); 
							} else {
								Ext.getCmp('produit[id_produit_lecteur]').setValue(r.result.lecteur[0].id_produit_lecteur);
								Ext.getCmp('label_produit[id_produit_lecteur]').setValue(r.result.lecteur[0].libelle);
							}
						} else {
							Ext.getCmp('produit[id_produit_lecteur]').reset();
							Ext.getCmp('label_produit[id_produit_lecteur]').reset();
						}

						Ext.getCmp('label_produit[id_produit_OS]').removeClass('orange_border_textfield'); 
						if (r.result.os) {
							if (r.result.os.length>1) {
								Ext.getCmp('label_produit[id_produit_OS]').addClass('orange_border_textfield'); 
							} else {
								Ext.getCmp('produit[id_produit_OS]').setValue(r.result.os[0].id_produit_OS);
								Ext.getCmp('label_produit[id_produit_OS]').setValue(r.result.os[0].libelle);
							}
						} else {
							Ext.getCmp('produit[id_produit_OS]').reset();
							Ext.getCmp('label_produit[id_produit_OS]').reset();
						}

						Ext.getCmp('label_produit[id_produit_puissance]').removeClass('orange_border_textfield'); 
						if (r.result.proc_puissance) {
							if (r.result.proc_puissance.length>1) {
								Ext.getCmp('label_produit[id_produit_puissance]').addClass('orange_border_textfield'); 
							} else {
								Ext.getCmp('produit[id_produit_puissance]').setValue(r.result.proc_puissance[0].id_produit_puissance);
								Ext.getCmp('label_produit[id_produit_puissance]').setValue(r.result.proc_puissance[0].libelle);
							}
						} else {
							Ext.getCmp('produit[id_produit_puissance]').reset();
							Ext.getCmp('label_produit[id_produit_puissance]').reset();
						}

						Ext.getCmp('label_produit[id_produit_lan]').removeClass('orange_border_textfield'); 
						if (r.result.reseau) {
							if (r.result.reseau.length>1) {
								Ext.getCmp('label_produit[id_produit_lan]').addClass('orange_border_textfield'); 
							} else {
								Ext.getCmp('produit[id_produit_lan]').setValue(r.result.reseau[0].id_produit_lan);
								Ext.getCmp('label_produit[id_produit_lan]').setValue(r.result.reseau[0].libelle);
							}
						} else {
							Ext.getCmp('produit[id_produit_lan]').reset();
							Ext.getCmp('label_produit[id_produit_lan]').reset();
						}

						Ext.getCmp('label_produit[id_processeur]').removeClass('orange_border_textfield'); 
						if (r.result.proc_modele) {

							if (r.result.proc_modele.length>1) {
								Ext.getCmp('label_produit[id_processeur]').addClass('orange_border_textfield'); 
							} else {
								Ext.getCmp('produit[id_processeur]').setValue(r.result.proc_modele[0].id_processeur);
								Ext.getCmp('label_produit[id_processeur]').setValue(r.result.proc_modele[0].libelle);
							}
						} else {
							Ext.getCmp('produit[id_processeur]').reset();
							Ext.getCmp('label_produit[id_processeur]').reset();
						}

						Ext.getCmp('label_produit[id_produit_dd]').removeClass('orange_border_textfield'); 
						if (r.result.dd) {
							if (r.result.dd.length>1) {
								Ext.getCmp('label_produit[id_produit_dd]').addClass('orange_border_textfield'); 
							} else {
								Ext.getCmp('produit[id_produit_dd]').setValue(r.result.dd[0].id_produit_dd);
								Ext.getCmp('label_produit[id_produit_dd]').setValue(r.result.dd[0].libelle);
							}
						} else {
							Ext.getCmp('produit[id_produit_dd]').reset();
							Ext.getCmp('label_produit[id_produit_dd]').reset();
						}

						if (r.result.photo) {
							Ext.getCmp('produit[photo]').setValue("1");
						}

						if (r.result.photo1) {
							Ext.getCmp('produit[photo1]').setValue("1");
						}

						if (r.result.photo2) {
							Ext.getCmp('produit[photo2]').setValue("1");
						}

					} else if (r.error) {
						ATF.errors = r.error;
						ATF.showError();
					}
					ATF.loadMask.hide();
				}
			}
		);
	}
},
{/strip}