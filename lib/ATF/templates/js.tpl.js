Ext.onReady(function(){ 
	{*--ModeDebug--*}
	{if ATF::$debug}
		ATF.debug=true;
	{else}
		ATF.debug=false;
	{/if}
	
	{*--Serveur statique--*}
	ATF.staticserver="{ATF::$staticserver}";
	
	{*--MessageBox de chargement--*}
	//Chargement d'une page
	ATF.loadMask=new Ext.LoadMask(Ext.getBody(),{ msg:'<img src="'+ATF.staticserver+'images/loading/loading2.gif" alt="loading-img" style="margin-right:5px;"/>Chargement de la page en cours...<br /><a href="javascript:;" onclick="if(ATF.loadMask.lastXHR && ATF.loadMask.lastXHR.conn){ ATF.loadMask.lastXHR.conn.abort(); }ATF.loadMask.hide();">Annuler</a>', msgCls:'atf-mask-loading' });
	//Suppression d'un ou plusieurs éléments
	ATF.deleteLoadMask=new Ext.LoadMask(Ext.getBody(),{ msg:'<img src="'+ATF.staticserver+'images/loading/loading2.gif" alt="loading-img" style="margin-right:5px;"/>Suppression de(s) (l\')élément(s) en cours...', msgCls:'atf-mask-loading' });
	//Raffraichissement d'un listing
	ATF.updWaitMsg = '<div id="noItemNotFound"><img src="'+ATF.staticserver+'images/loading/loading3.gif" alt="loading-img" style="margin-right:5px;"/>';	
	
	{if !ATF::$usr->invited} {* Ne fonctionne pas en invité, ne sait pas pourquoi... *}
		{*--Historique ExtJs--*}
		Ext.History.init();
		{* Listener sur l history *}
		{* Cest le bout de code qui est appellé à chaque changement d URL automatiquement *}
		Ext.History.on('change', function(token){
			if (ATF.formIsActive) {
				if (!confirm('{ATF::$usr->trans(sur_de_quitter_la_page)|escape:javascript}')) {
					return;
				} else {
					ATF.unsetFormIsActive();					
				}
			}
			if(!ATF.changeUrl){
				if(token){
					{* Fonctionnement classique => voir loadUrl dans atf.js *}
					ATF.loadUrl(token);
				}else{
					{* Cas particulier : Retour sur la première page. Cest lorquon revient en arriere jusque la premiere page chargée *}
					ATF.loadUrl(window.location.pathname.substr(1,window.location.pathname.length-1));
				}
			}else{
				ATF.changeUrl=false;
			}
		});
	{/if}
		
	{* Affichage du module actuel *}
	if(window.location.hash){
		ATF.loadUrl(window.location.hash.substr(1,window.location.hash.length-1));
	}else{
		var reg1=new RegExp(".","g");
		if(window.location.pathname.length!=41 || !window.location.pathname.match(reg1)){
			ATF.loadUrl(window.location.pathname.substr(1,window.location.pathname.length-1));
		}
	}

	{*--KeepOnline--*}
	{if ATF::$usr->logged}
		setTimeout('ATF.keepOnline();',300000);
	{/if}
	
	{*--Js spécifique--*}
	{if ATF::$usr->logged}
		{* Message d'erreur modalbox (Par exemple, explication d'une redirection par rapport à de mauvais droits) *}
		{foreach from=ATF::$msg->getErrors() item=item}
			ATF.errors.push({json_encode($item)});
			{if $item@last}
				ATF.showError();
			{/if}
		{/foreach}
		
		{* /* Message d'information (notice) */ *}
		{foreach from=ATF::$msg->getNotices() item=item}
			{if $item@first}
			{/if}
			ATF.notices.push({json_encode($item)});
			{if $item@last}
				ATF.showNotice();
			{/if}
		{/foreach}
	{/if}
	
	{*--Selecteurs de listings, pour cocher la case lorsqu on clique sur la ligne--
	ATF.bindListingSelectors();*}
		
	{* Infobulles sur [rel] *}
	ATF.bindTips();
	
	{* Containers *}
	ATF.adjustContainer();
	
	{* /* Localisation javascript 
		@todo Trouver un endroit plus approprier */
	*}	
	ATF.__redirectBox = {
		content: '{ATF::$usr->trans(redirection_login)|escape:quotes}'
		, params: {
			title: '{ATF::$usr->trans(session_out)|escape:quotes}'
			,afterHide: function(){
				ATF.goto_login();
			}
		}
	}
		
	{*--Editeurs--*}				 
	ATF.initEditors();
	
	{*--ProgressBar--*}
	ATF.renderProgressBars();
	
	{* AJAX preference *}
	ATF.createWindowPreference = function () {
		Ext.Ajax.request({
			url: 'preferences,changePreference.ajax',
			success: function(obj){
			   eval(obj.responseText);
			},
			failure: function(obj){
			}
		});

	};
	
	
	{*--Trateiemtnde l a position fixed des ancres--*}
	{if ATF::$usr->logged}
		window.onscroll = function () {
			if ($('#anchorsTabs')) {
				if (window.pageYOffset>=42 && !$('#anchorsTabs').hasClass('scrolled')) {
					$('#anchorsTabs').addClass("scrolled");

					{*--
						Dans cette version, on a un effet de clignotement sous Chrome...
						Cependant, il semble qu'il n'y a qu'avec cette version qu'on peut gérer le z-index pour éviter le passage en dessous du primary
						Mais lors de mes tests pas moyen de gérer ce z-index comme il faut.
						$('#anchorsTabs').style.top = window.pageYOffset+"px";
					--*}
				} else if (window.pageYOffset<42) {
					$('#anchorsTabs').removeClass("scrolled");
				}
			}
			
		}
			
	{/if}


	ATF.importScript = function(){
		/*
		 * Import des script JST
		 */
		var template = new Array();
		template[0] = "list";
		template[1] = "input";
		template[2] = "date";
		template[3] = "filterElement";
		template[4] = "updateFilter";
		
		var length = template.length;	
		for (var i = 0, len = length; i < len; i++) {
			var scriptTag = document.createElement("link");
			    scriptTag.setAttribute("type", "application/x-handlebars-template");
			    scriptTag.setAttribute("charset", "utf-8");
			    scriptTag.setAttribute("src", "/templates/"+template[i]+".jst?v={ATF::$version}");
			    scriptTag.setAttribute("id", template[i]);
			    scriptTag.setAttribute("rel", "prefetch");
			    scriptTag.setAttribute("data-template", template[i]);
			 var head = document.getElementsByTagName("head").item(0);
			 head.appendChild(scriptTag); 						
		}
		
		/*
		 * Import des script JS
		 */	
		var template = new Array();
		template[0] = "libs/handlebars";
		template[1] = "libs/jquery.selectbox-0.2.min";
		template[2] = "libs/underscore-min";
		template[3] = "libs/glDatePicker.min";
		template[4] = "dataBridge";	
		template[5] = "filterElement";	
		template[6] = "filterCollection";	
		template[7] = "filterBuilder";	
		template[8] = "page";	
		
		var length = template.length;	
		for (var i = 0, len = length; i < len; i++) {
			var scriptTag = document.createElement("script");
			    scriptTag.setAttribute("type", "text/javascript");
			    scriptTag.setAttribute("charset", "utf-8");
			    scriptTag.setAttribute("src", "{ATF::$staticserver}common/js/"+template[i]+".js?v={ATF::$version}");
			    scriptTag.setAttribute("id", template[i]);
			 var head = document.getElementsByTagName("head").item(0);
			 head.appendChild(scriptTag); 						
		}
		
	}
	
	/* premet le resize des panel lors d'un resize de window ! */
	Ext.EventManager.onWindowResize(function(w, h){
	    (function(){
	    	Ext.select('.x-tab-panel').each(function(el) {
	    		Ext.getCmp(el.id).doLayout()
	    	});
	    	//Ext.getCmp('OngletsTabPanel').doLayout()
	    }).defer(10);
	    

	});

	ATF.createSelfTache = function () {

		var myForm = new Ext.FormPanel({
			width: 500
			,items: [
				{include 
					file="generic-field-textfield.tpl.js" 
					current_class=ATF::getClass("tache") 
					key="id_societe" 
					function="autocomplete" 
					id="tache[id_societe]" 
					name="tache[id_societe]" 
					fieldLabel="Société" 
				}
			,{
				fieldLabel: '{ATF::$usr->trans(tache,tache)|escape:javascript}'
				,xtype:'textarea'
				,id: 'tache[tache]'
				,name: 'tache[tache]'
				,width: 400
			}]
			,buttons: [{
				text: '{ATF::$usr->trans(ok)|escape:javascript}',
				handler: function(){
					myForm.getForm().submit({
						submitEmptyText:false,
						method  : 'post',
						waitMsg : '{ATF::$usr->trans(creating_new_element)|escape:javascript}',
						waitTitle : '{ATF::$usr->trans(loading)|escape:javascript}',
						url     : 'extjs.ajax',
						params: {
							'extAction':'tache'
							,'extMethod':'insert'
							,'tache[id_user]':'{ATF::$usr->getId()}'
							,'tache[horaire_fin]':"{$smarty.now|date_format:'%Y-%m-%d %H:%I'}"
							,'dest[]':"{ATF::$usr->getId()}"
						}
						,success:function(form, action) {		

							ATF.unsetFormIsActive();
							//ATF.extRefresh(action); 
							ATF.currentWindow.destroy();
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
			}]
		});	
	
		ATF.currentWindow=new Ext.Window({
			title: '{ATF::$usr->trans(insert,tache)|escape:javascript}',
			buttonAlign:'center',
			closable:true,
			items: myForm
		});
		ATF.currentWindow.show();
	}

});
