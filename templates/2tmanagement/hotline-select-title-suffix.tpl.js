{$id_crypted = ATF::hotline()->cryptId($infos.id_hotline)}
{$path = $current_class->filepath($infos.id_hotline,"fichier_joint")}

{$societe = ATF::societe()->select($infos.id_societe)}

{
	html:'<div style="border: 2px solid #a60000; color: #a60000; font-weight: bold; line-height: 13px; padding-left: 5px; padding-right: 5px; border-radius: 5px;">Presta "facturable" : {ATF::hotline()->getTotalTime($infos.id_hotline, "prestaTicket")} | Dép. : {ATF::hotline()->getTotalTime($infos.id_hotline, "dep")}<br>Crédits facturés : {ATF::hotline()->getCreditUtilises($infos.id_hotline)}</div>',
	cls: "labelCtn"
}

/* START : Prise en charge de la requête */
{if !$infos.id_user && ($infos.etat=="wait" || $infos.etat=="free" || $infos.etat=="fixing")}
	,{
		xtype:'button',
		id:'btnHotlinePriseEnCharge',
		scale: 'medium',
		split: true,
		tooltip:'{ATF::$usr->trans("hotline_prendre")}',
		iconCls: 'iconPriseEnCharge',
		handler: function () {
			Ext.Msg.confirm(
				"{ATF::$usr->trans(Etes_vous_sur)}",
				"{ATF::$usr->trans(hotline_prendre)}",
				function (btn) {
					if (btn=="yes") {
						ATF.loadMask.show();
						ATF.tpl2div('{$current_class->table},takeRequest.ajax','id_hotline={$id_crypted}&send_mail='+Ext.getCmp('btnHotlineSendMail').pressed,{ onComplete: function() { ATF.loadMask.hide(); }});
					}
				}
			);
		},
		style: {
			marginLeft: "5px",
			float: 'left'
		},
	}
{/if}
/* END : Prise en charge de la requête */
{if file_exists($path)}

	,{
		xtype:'button',
		id:'btnHotlinePJ',
		scale: 'medium',
		split: true,
		tooltip:'{ATF::$usr->trans("hotline_pj")}',
		iconCls: 'iconPJ',
		handler: function () {
			window.open("hotline-select-fichier_joint-{$id_crypted}.dl");
		},
		style: {
			marginLeft: "5px",
			float: 'left'
		},
	}
{/if}

/* START : CHOIX DE LA FACTURATION */
{if $infos.etat != "annulee"}
	,{
		xtype: 'splitbutton',
		id:'btnHotlineFacturation',
		scale: 'medium',
		split: true,
		tooltip:'{ATF::$usr->trans("hotline_choix_mode_facturation")}',
		iconCls: 'iconEuro',
		{if $infos.etat=='fixing' || $infos.etat=='wait' || $infos.etat=='free'} disabled: false, {else} disabled: true, {/if}
		style: {
			marginLeft: "5px",
			float: 'left'
		},


		menu: {
			cls: 'no-icon-space',
			showSeparator: false,
			items: [{
				cls:' no-icon_space',
				text:"{ATF::hotline()->getBillingMode($infos.id_hotline,true)}"
			},"-",{
				text:"Inter",
				menu: {
					cls: 'no-icon-space',
					showSeparator: false,
					items: [{
						text:"A la charge de 2T Management",
						handler: function (el,ev) {
							ATF.loadMask.show();
							ATF.tpl2div("hotline,setbillingModeNew.ajax","charge=intervention&type_requete=charge_absystech&id_hotline={$id_crypted}&send_mail="+Ext.getCmp('btnHotlineSendMail').pressed,{ onComplete: function() { ATF.loadMask.hide(); }});

						}
					},{
						text:"A la charge du client",
						handler: function (el,ev) {
							ATF.loadMask.show();
							ATF.tpl2div("hotline,setbillingModeNew.ajax","charge=intervention&type_requete=charge_client&id_hotline={$id_crypted}&send_mail="+Ext.getCmp('btnHotlineSendMail').pressed,{ onComplete: function() { ATF.loadMask.hide(); }});
						}
					}]
				}
			},{
				text:"R&D",
				handler: function (el,ev) {
					ATF.loadMask.show();
					ATF.tpl2div("hotline,setbillingModeNew.ajax","charge=rd&type_requete=charge_absystech&id_hotline={$id_crypted}&send_mail="+Ext.getCmp('btnHotlineSendMail').pressed,{ onComplete: function() { ATF.loadMask.hide(); }});
				}
			}]
		}
	}
{/if}
/* END : CHOIX DE LA FACTURATION */

/* START : CLICK 2 CALL */

,{
	xtype: 'splitbutton',
	iconCls: 'iconHotline',
	id:'btnClick2call',
	border:false,
	split: true,
	scale: 'medium',
	tooltip:'{ATF::$usr->trans("click2call")}',
	// handler: function () {

	// } ,
	style: {

		marginLeft: "5px",
		float: 'left'
	},
	menu: {
		items: [
		{if ATF::contact()->select($infos.id_contact,'tel')}
			{
				text: "{ATF::contact()->nom($infos.id_contact)} - {ATF::contact()->select($infos.id_contact,'tel')}"
				,iconCls:"iconHotline"
				,handler: function (el,v) {
					ATF.tpl2div(
						'asterisk,createCall.ajax',
						'id={classes::cryptId($infos.id_contact)}&table=contact&field=tel'
					);
				}
			},
		{/if}
		{if ATF::contact()->select($infos.id_contact,'gsm')}
			{
				text: "{ATF::contact()->nom($infos.id_contact)} - {ATF::contact()->select($infos.id_contact,'gsm')}"
				,iconCls:"iconHotline"
				,handler: function (el,v) {
					ATF.tpl2div(
						'asterisk,createCall.ajax',
						'id={classes::cryptId($infos.id_contact)}&table=contact&field=gsm'
					);
				}
			},
		{/if}
		{if ATF::societe()->select($infos.id_societe,'tel')}
			{
				text: "{ATF::societe()->nom($infos.id_societe)} - {ATF::societe()->select($infos.id_societe,'tel')}"
				,iconCls:"iconHotline"
				,handler: function (el,v) {
					ATF.tpl2div(
						'asterisk,createCall.ajax',
						'id={classes::cryptId($infos.id_societe)}&table=societe&field=tel'
					);
				}
			},
		{/if}
		]
	}
}
/* END : CLICK 2 CALL */
/* START : GEOLOC */

,{
	xtype: 'button',
	iconCls: 'iconGMap',
	id:'btnGeoloc',
	border:false,
	scale: 'medium',
	tooltip:'{ATF::$usr->trans("Situer la société")}',
	handler: function () {
		ATF.currentWindow = new Ext.Window({
            title: '{ATF::$usr->trans(geolocalisation)|escape:javascript} - {ATF::societe()->nom($infos.id_societe)|escape:javascript}',
            cls: "gMapWindow",
            monitorResize:false,
            resizable: false,
            autoLoad:{ url: 'societe,geolocalisation.ajax,id={$infos.id_societe}', scripts:true }

		});

		ATF.currentWindow.show();
	},
	style: {

		marginLeft: "5px",
		float: 'left'
	}

}
/* END : GEOLOC */

{if ATF::contact()->select($infos.id_contact,'email')}
	,{
		xtype: 'button',
		iconCls: 'iconTeamviewer',
		id:'btnTeamviewer',
		border:false,
		scale: 'medium',
		tooltip:'{ATF::$usr->trans("sendMailTeamviewer")}',
		handler: function () {
			Ext.Msg.confirm(
				"{ATF::$usr->trans(Etes_vous_sur)}",
				"{ATF::$usr->trans(envoi_teamviewer)}",
				function (btn) {
					if (btn=="yes") {
						ATF.loadMask.show();
						ATF.tpl2div('contact,sendMailTeamViewer.ajax','id_contact={$infos.id_contact}',{ onComplete: function() { ATF.loadMask.hide(); }});
					}
				}
			);
		},
		style: {

			marginLeft: "5px",
			float: 'left'
		}

	}
{/if}




/* START : SEND MAIL */
,{
	xtype:'button',
	id:'btnHotlineSendMail',
	scale: 'medium',
	enableToggle: true,
	{if $infos.visible=='non'}
		disabled:true,
		iconCls: 'iconMailNOK',
		pressed: false,
	{else}
		iconCls: 'iconMailOK',
		pressed: true,
	{/if}
	tooltip:'{ATF::$usr->trans("send_mail",hotline)}',
	style: {
		marginLeft: "5px",
		float: 'left'
	},
	toggleHandler: function (btn,v) {
		if (v) {
			btn.setIconClass("iconMailOK");
		} else {
			btn.setIconClass("iconMailNOK");
		}
	}
}
/* END : SEND MAIL */



/* START : Mise en attente de la requete */
{if $infos.etat != "payee" && $infos.etat != "annulee" && $infos.etat != "done" && $infos.etat != "free" && $infos.wait_mep != "oui" && $infos.id_user}
	,{
		xtype:'button',
		id:'btnHotlineWait',
		scale: 'medium',
		enableToggle: true,
		{if $infos.etat=='wait'}
			pressed: true,
			tooltip:'{ATF::$usr->trans("hotline_mea_desactivation")}',
		{else}
			pressed: false,
			tooltip:'{ATF::$usr->trans("hotline_mea_activation")}',
		{/if}
		iconCls: 'iconClock',
		toggleHandler: function (btn,v) {
			Ext.Msg.confirm(
				"{ATF::$usr->trans(hotline_mea)}",
				"{ATF::$usr->trans(Etes_vous_sur)}",
				function (btn) {
					if (btn=="yes") {
						ATF.loadMask.show();
						if (v) {
							ATF.tpl2div('{$current_class->table},setWait.ajax','id_hotline={$id_crypted}&send_mail='+Ext.getCmp('btnHotlineSendMail').pressed,{ onComplete: function() { ATF.loadMask.hide(); }});
						} else {
							ATF.tpl2div('{$current_class->table},fixingRequest.ajax','id_hotline={$id_crypted}',{ onComplete: function() { ATF.loadMask.hide(); }});
						}
					}
				}
			);
		},
		style: {
			marginLeft: "5px",
			float: 'left'
		},
	}
{/if}
/* END : Mise en attente de la requete */


/* START : Modifier la priorité */
,{
	xtype:'button',
	id:'btnHotlineSetPriority',
	scale: 'medium',
	split: true,
	tooltip:'{ATF::$usr->trans("hotline_priority")}',
	iconCls: 'iconPriority',
	style: {
		marginLeft: "5px",
		float: 'left'
	},
	menu: {
		items: [{
			text: "{ATF::$usr->trans(urgence_bloquant,hotline)} (15)"
			,iconCls:"iconRedFlag"
			,handler: function (el,v) {
				ATF.loadMask.show();
				ATF.ajax('hotline,setPriorite.ajax','id_hotline={$id_crypted}&hotline_select=1&&priorite=15', { onComplete: function() { ATF.loadMask.hide(); } })

			}
		},{
			text: "{ATF::$usr->trans(urgence_genant,hotline)} (10)"
			,iconCls:"iconOrangeFlag"
			,handler: function (el,v) {
				ATF.loadMask.show();
				ATF.ajax('hotline,setPriorite.ajax','id_hotline={$id_crypted}&hotline_select=1&&priorite=10', { onComplete: function() { ATF.loadMask.hide(); } })
			}
		},{
			text: "{ATF::$usr->trans(urgence_detail,hotline)} (5)"
			,iconCls:"iconGreenFlag"
			,handler: function (el,v) {
				ATF.loadMask.show();
				ATF.ajax('hotline,setPriorite.ajax','id_hotline={$id_crypted}&hotline_select=1&&priorite=5', { onComplete: function() { ATF.loadMask.hide(); } })
			}
		}]
	}
}
/* END : Modifier la priorité */

/* START : Clôture */
{if
	(
		($infos.etat == "fixing" && $infos.facturation_ticket=="non" && $infos.id_user) ||
		($infos.etat == "fixing" && $infos.facturation_ticket=="oui" && $infos.ok_facturation == "oui")
	)
&& $infos.wait_mep=="non"
&& $infos.id_user}
	,{
		xtype:'button',
		id:'btnHotlineResolu',
		scale: 'medium',
		split: true,
		tooltip:'{ATF::$usr->trans("requete_resolu")}',
		iconCls: 'iconResoudre',
		handler: function () {
			Ext.Msg.confirm(
				"{ATF::$usr->trans(requete_resolu)}",
				"{ATF::$usr->trans(Etes_vous_sur)}",
				function (btn) {
					if (btn=="yes") {
						ATF.loadMask.show();
						ATF.ajax('hotline,resolveRequest.ajax','id_hotline={$id_crypted}&send_mail='+Ext.getCmp('btnHotlineSendMail').pressed, { onComplete: function() { ATF.loadMask.hide(); } })
						/*ATF.tpl2div('{$current_class->table},resolveRequest.ajax','id_hotline={$infos.id_hotline}&send_mail='+Ext.getCmp('btnHotlineSendMail').pressed);*/
					}
				}
			);
		},
		style: {
			marginLeft: "5px",
			float: 'left'
		},
	}{/if}
/* END : Clôture */

/* START : Annulation de la requête */
{if $infos.etat != "payee" && $infos.etat != "annulee" && $infos.etat != "done"}
	,{
		xtype:'button',
		id:'btnHotlineCancel',
		scale: 'medium',
		split: true,
		tooltip:'{ATF::$usr->trans("hotline_annule")}',
		iconCls: 'iconCancel',
		handler: function () {
			Ext.Msg.confirm(
				"{ATF::$usr->trans(hotline_annule)}",
				"{ATF::$usr->trans(Etes_vous_sur)}",
				function (btn) {
					if (btn=="yes") {
						Ext.Msg.confirm(
							"{ATF::$usr->trans(hotline_annule)}",
							"ATTENTION CETTE ACTION EST IRREVERSIBLE ! Continuer ?",
							function (btn) {
								if (btn=="yes") {
									ATF.loadMask.show();
									ATF.ajax('{$current_class->table},cancelRequest.ajax','id_hotline={$id_crypted}&send_mail='+Ext.getCmp('btnHotlineSendMail').pressed, { onComplete: function() { ATF.loadMask.hide(); } })
								}
							}
						);
					}
				}
			);
		},
		style: {
			marginLeft: "5px",
			float: 'left'
		},
	}
{/if}
/* END : Annulation de la requête */

{if $societe["est_sous_contrat_maintenance"]}
	{if $societe["est_sous_contrat_maintenance"] == "sous_contrat"}
		{$alerte = "btn-success"}
		{$texte = "Client sous contrat de maintenance"}
	{else if $societe["est_sous_contrat_maintenance"] == "sous_contrat_partiel"}
		{$alerte = "btn-warning"}
		{$texte = "Contrat de maintenance Partiel"}
	{else}
		{$alerte = "btn-danger"}
		{$texte = "Le client ne possède pas de contrat de maintenance"}
	{/if}

	{$commentaire = ""}
	{if $societe["commentaire_contrat_maintenance"]}
		{$commentaire = $societe["commentaire_contrat_maintenance"]}
	{/if}

	{$image =''}
	{if $societe["option_contrat_maintenance"] != "aucune"}
		{if $societe["option_contrat_maintenance"] == "a_caliner"}
			,{
				html: '<button type="button" class="btn btn-lg {$alerte}"><div style="float:left; margin-right:10px;"><img src="{ATF::$staticserver}images/heart.png" style="height:40px;"></div>{$texte}{if $commentaire}<br><span class="infos_sup">{$commentaire|escape:javascript}</span>{/if}</button>'
				,border: false
				,style: {
					marginLeft: "10px",
					float: 'left'
				}
			}
		{else}
			,{
				html: '<button type="button" class="btn btn-lg {$alerte}"><div style="float:left; margin-right:10px;"><img src="{ATF::$staticserver}images/new_install.png" style="height:40px;"></div>{$texte}{if $commentaire}<br><span class="infos_sup">{$commentaire|escape:javascript}</span>{/if}</button>'
				,border: false
				,style: {
					marginLeft: "10px",
					float: 'left'
				}
			}
		{/if}
	{else}
		,{
			html: '<button type="button" class="btn btn-lg {$alerte}">{$image}{$texte}{if $commentaire}<br><span class="infos_sup">{$commentaire|escape:javascript}</span>{/if}</button>'
			,border: false
			,style: {
				marginLeft: "10px",
				float: 'left'
			}
		}
	{/if}
{/if}