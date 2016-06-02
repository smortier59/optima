var ATF = new Object();
//Paramètres ATF
ATF.timer_loading=0;// Timer pour la page de chargement quand on appelle une page en AJAX
ATF.debug=false;//Niveau de debg (equivaut ) ATF::$debug)
ATF.networkReady=true;//False si on perd la connexion réseaux
ATF.errors = new Array();//Liste des erreurs
ATF.formErrors = new Array();//Liste des erreurs formulaires
ATF.notices = new Array();//Listes des notices
ATF.warnings = new Array();//Lise des warning
ATF.godirect=false;//Je sais plus non plus !
ATF.previousPage=false;//Ancre précédente
ATF.currentPage=false;//Ancre suivante
ATF.changeUrl=false;//Utilisé pour la redirection via le code métier (méthode redirection)
ATF.staticserver='';//Statique url
ATF.ongletThreads= new Array();//La liste des objets Ajax (onglets) en cours de raffraichissement
ATF.errorSystemTitle="Système d'erreur AbsysTech";
ATF.errorSystemMsg="<b>L'application vient de provoquer une erreur !</b><br/>Nous allons étudier la cause du problème et le corriger aussi vite que possible. Si vous êtes bloqué merci de contacter le <a href=\http://hotline.absystech.net/\" target=\"_blank\">support AbsysTech</a> en précisant les circonstances exactes.";
ATF.timeoutMsg="<b>Problème de connexion Internet</b><br/>Veuillez vérifier la connexion réseau de votre ordinateur.";
ATF.newVersionAlreadyDisplay=false;//Si la modalBox de nouvelle version a déjà été demandée.
ATF.menuLength=null;//Taille du menu
ATF.formIsActive=false; // Si on est sur un formulaire de saisie, permet de prévenir la perte de donnée par changement de page involontaire
ATF.currentpage=Array(); // Pour sauvegarder le numéro de page sur lequel on se situe (scrollbar des select_all extjs)

if (typeof(Ext) != "undefined") {
	Ext.onReady(function(){
		Ext.QuickTips.init();
		ATF.loadMask=new Ext.LoadMask(Ext.getBody(), {msg:"Please Wait..."});//Message de chargement
		ATF.deleteLoadMask=new Ext.LoadMask(Ext.getBody(), {msg:"Delete Request.Please Wait..."});//Message de chargement
	});

}

ATF.createLoadMask = function (el) {
	return new Ext.LoadMask(
		el,
		{
			msg:"Chargement...",
			removeMask: true,
			store: el.store
		}
	);
}


//	ATF.log - Affiche des logs dans la console ou dans une fenêtre popup Extjs
ATF.log = function (message) {
	try{
		console.log(message);
	}catch(e){
		if(ATF.debug==true){
			Ext.Msg.alert("Console.log ",message);
		}else{
			//Ext.Msg.alert(ATF.errorSystemTitle,ATF.errorSystemMsg+"Console.log("+message+")</div>");
		}
	}
}


//	ATF.version - Renvoi la version actuelle de ATF
ATF.version = function () {
	Ext.Msg.alert(ATF.errorSystemTitle,"5 !");
}

//	ATF.setJsError - Gestion d'erreur en ajax
//	@param object error
//	@author Jérémie Gwiazdowski <jgw@absystech.fr>
ATF.setJsError = function(error){
	//Fermeture des masques de chargement
	ATF.loadMask.hide();
	ATF.deleteLoadMask.hide();
	//Envoi de l'erreur au serveur
	Ext.Ajax.request({
		url:"error.ajax",
		method:"POST",
		params:{
			"fileName":((error.fileName)?error.fileName:"NameNotFound")
			,"lineNumber":((error.lineNumber)?error.lineNumber:"LineNotFound")
			,"message":((error.message)?error.message:"ErrorNotFound")
			,"name":((error.name)?error.name:"NameNotFound")
			,"url":((ATF.currentPage)?ATF.currentPage:"UrlNotFound")
			,"jsStack":((error.stack)?error.stack:"JsStackNotFound")
		}
	});

	//Affichage de l'erreur au client
	/*if(error.message){
		Ext.Msg.alert(ATF.errorSystemTitle
					  ,((ATF.debug)?"Erreur : "+error.message:ATF.errorSystemMsg)
					  ,function(btn,text){
						  if(btn == 'ok'){
							  Ext.History.back();
							}
					  },{
						  minWidth:300
					  });
	}*/

	//Gestion de l'url
	/*if(ATF.previousPage!=ATF.currentPage){
		ATF.changeUrl=true;//Indiquation au moteur d'url qu'on ne désire pas raffraichir
		window.location.hash=ATF.previousPage;//Changement de l'url
	}*/

	//Affichage de l'erreur en Dev
	if(ATF.debug){
		ATF.log("Erreur Javascript : "+error.message);
		ATF.log(error.stack);
	}
}

//	ATF.calendar - Mémorise la date choisie dans un calendrier
//	@param calendar
//	@param url
ATF.calendar = function (calendar,url) {
	var y = calendar.date.getFullYear();
	var m = calendar.date.getMonth()+1;
	var j = calendar.date.getDate();

	if ( m == 1 || m == 2 || m == 3 || m == 4 || m == 5 || m == 6 || m == 7 || m == 8 || m == 9) {
		m = "0"+m;
	}
	if ( j == 1 || j == 2 || j == 3 || j == 4 || j == 5 || j == 6 || j == 7 || j == 8 || j == 9) {
		j = "0"+j;
	}
	var date = y+"-"+m+"-"+j;
	ATF.tpl2div(url,'date='+date);
}

//	ATF.concatAdresse - Concaténation de l'adresse
//	@param string adresse
//	@param string adresse_2
//	@param string adresse_3
//	@param string ville
//	@param string cp
//	@param string id_pays
ATF.concatAdresse = function (adresse,ville,pays,cp,adresse2,adresse3){
	var adresse = adresse.value+" "+adresse2.value+" "+adresse3.value+" "+cp.value+" "+ville.value+" "+pays.value;
	ATF.geocode(adresse,'societe');
}

//	ATF.geocode - Recherche les coordonnées d'une adresse avec le service Google Maps */
//	@param string adresse
ATF.geocode = function (adresse,c) {
	ATF.ajax(c+',geocode.ajax' //url
		,'url='+adresse // data
		,{ onComplete:function (obj) { //infos
				document.getElementById('societe[latitude]').value = obj.result.Placemark[0].Point.coordinates[0];
				document.getElementById('societe[longitude]').value = obj.result.Placemark[0].Point.coordinates[1];
			}
		}
	);
}

//	ATF.tpl2div - Alias de ATF.ajax
//	@param url
//	@param post Données à poster
//	@param infos Paramètres supplémentaires (onComplete,...)
ATF.tpl2div = function (url,post,infos) {
	return ATF.ajax(url,post,infos);
}

//	Fonction qui gère toute requête AJAX
//	@param url
//	@param post Données à poster
//	@param infos Paramètres supplémentaires
//	@exemple ATF.ajax('/url','data=1&toto=3',{ onComplete: function () { }, onSuccess: function () { } });
ATF.ajax = function (url,post,infos) {
	if (!infos) {
		var infos = new Object();
	}

	infos.postBody = post;
//	infos.onLoading = function () { ATF.wait_on(); }
//	infos.onInteractive  = function () { ATF.wait_off(); }

	//Surcharge de l'onComplete
	if (infos.onComplete) {
		infos.onCompleteFinal=infos.onComplete;
	}

	//Surcharge du onSuccess
	if (infos.onSuccess) {
		infos.onSuccessFinal=infos.onSuccess;
	}

	// En cas de succès d'une requête C'est cette fonction qui est utilisée
	infos.onSuccess = function (request,opts) {
		try{
			//On a reçu le résultat de la requête : Le réseau est op
			ATF.networkReady=true;

			// Suppression de toutes les infobulles, puis initialisation de tous les tips sur attributs clés
			ATF.bindTips(true);
			//ATF.log(request);
			var obj = $.parseJSON(request.responseText);
			//obj = obj.request.evalResponse();

			//Exécution du onComplete spécifique
			if (infos.onCompleteFinal) {
				var retour = infos.onCompleteFinal(obj, request);
			}

			//Exécution du onComplete spécifique
			if (infos.onSuccessFinal) {
				var retour = infos.onSuccessFinal(obj, request);
			}

			//Suite onSuccess !
			ATF.ajax_refresh(jQuery.extend(obj,infos));

			if (retour) {
				return retour;
			}
		}catch(e){
			ATF.setJsError(e);
		}
	}//fin infos.onSuccess

	infos.onFailure = function(request,url){
		if(request.status==-1 || request.status==0){
			//Timeout => perte du réseau
			ATF.loadMask.hide();
			ATF.deleteLoadMask.hide();
			if (request.status==-1) {
				ATF.networkReady=false;
				Ext.Msg.alert(ATF.errorSystemTitle,ATF.timeoutMsg);
			}
		}else{
			//Erreur 404
			if(!ATF.networkReady){
				Ext.Msg.alert(ATF.errorSystemTitle,ATF.timeoutMsg);
			}else{
				var error=new Object();
				error.message="Maybe Url \""+url.url+"\" not exists or request error : "+request.toString();
				error.name=request.status;
				ATF.setJsError(error);
			}
		}
	}

	infos.onException = function(request,ex){
		//ATF.log("Exception");
	}

	//return new Ajax.Request(url, infos);
	return Ext.Ajax.request({
							url:url,
							method:"POST",
							success:infos.onSuccess,
							failure:infos.onFailure,
							onException:infos.onException,
							params:post,
							timeout:120000
	});
}

//	TogglePanel
//	@author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
//	@param element id du div cible
//	@param effet
//	@param params
ATF.TogglePanel = function (element,effet,params) {
	if (element.slice(0,1)!='#')
		element='#'+element;
	var el=$(element+'_img');
	ATF.Toggle(element,effet,{
		afterFinish:function(){
			if(el.src.match(/arrow_expand_up/)){
				el.src=ATF.staticserver+'images/icones/arrow_expand_down.png';
			}else{
				el.src=ATF.staticserver+'images/icones/arrow_expand_up.png';
			}
		}
	});
}

//	ToggleToolBar Pour la barre de raccourci sur la gauche !
//	@author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
ATF.ToggleToolBar = function () {

	$('#toolbar').toggle();
}


//	Toggle avec effet par défaut
//	@author Yann GAUTHERON <ygautheron@absystech.fr>
//	@param element id du div cible
//	@param effet
//	@param params
ATF.Toggle = function (element,params) {
	if (!params) {
		params={ };
	}
	params = jQuery.extend({
		duration:.25
		, afterFinish:function() {
			ATF.adjustContainer();
		}
	},params);
	return $(element).toggle(params);
}

//	ShowShorcutContainer Affiche ou ferme le conteneur à coté des shortcuts !
//	@author Jérémie Gwiazdowski <jgw@absystech.fr>
//	@param string template Le nom du template
//	@param string shorcutname le nom de la shortcut
//	@param string shortcutnameTr le nom de la shortcut traduite !
var oldShortcutImg;
ATF.showContainer = function (template,shorcutname,shortcutnameTr){
	var cls="selected";
	shorcutname = "#"+shorcutname;

	if($(shorcutname+'_img').hasClass(cls) && $('#panelContainer') && $('#panelContainer').is(':visible')){
		$(shorcutname+'_img').removeClass();
		$('#panelContainer').fadeOut({ duration:.3 });
	}else{
		if($(oldShortcutImg) && $(oldShortcutImg).hasClass(cls)){
			$(oldShortcutImg).removeClass();
		}
		oldShortcutImg=shorcutname+'_img';
		$(shorcutname+'_img').addClass(cls);
		ATF.tpl2div("user,createShorcutContainer.ajax","shortcut=1&tpl="+template, {
		  onComplete: function (response,json) {
			$('#panelContainerTitle').html(shortcutnameTr);
			$('#panelContainer').show({
				scaleX:true,
				scaleY:false,
				duration:.3,
				complete:  function () {
					$('.leftPanelContentContainer').each(function (idx,e) {
						$(e).height($('#contentContainer').height());
					});
				}
			});
		  }
		});
	}
}

//	Execute un BlindUp de la gauche vers la droite sur un div
//	@param element
ATF.BlindLeftToRight = function (element) {
	if ($(element).style.display=='none') {
		Effect.BlindDown(element, { scaleX: true, duration:1.5 });
	} else {
		Effect.BlindUp(element, { scaleX: true, duration:1.5 });
	}
}

// Redirige vers la page de login en cas de perte de session
// @author Jérémie Gwiazdowski <jgw@absystech.fr>
ATF.goto_login = function () {
	try{
		Modalbox.hide();
	}catch(e){
		//ModalBox non initialisée : Normal si on ferme la modal avant
	}
	window.location.replace("");
	return;
}

// Redirection si plus de session
// @author Jérémie Gwiazdowski <jgw@absystech.fr>
// @param boolean nosession true si redirection
ATF.no_session = function(nosession) {
	//ATF.log("check_no_session");
	if (nosession){
		//ATF.log("no_session !!!!");
		try{
			Modalbox.show('<div><div id="__loadingBoxTimer" style="display:none">_</div>'+ATF.__redirectBox.content+'</div>', ATF.__redirectBox.params);
		}catch(e){
			ATF.setJsError(e);
			return false;
		}
		setTimeout("ATF.goto_login()",5000);
		return true;
	}
	return false;
}

// Messagerie instantanée
ATF.im = {
	// Liste des users
	getUserList: function() {
		if (panel = Ext.getCmp('panel-IMUserList')) {
			return panel;
		}
		var store = new Ext.data.JsonStore({
			url: 'im,getList.ajax',
			root: 'result.im.online',
			fields: [
				'id_user',
				'login',
				'user'
			]
		});
		store.load();

		var userListView = new Ext.list.ListView({
			store: store,
			hideHeaders:true,
			multiSelect: false,
			emptyText: "Aucun utilisateur connecté",
			columns: [{
				width: 1,
				dataIndex: 'user',
				cls: 'im-userListView-user'
			}],
			listeners:{
				'dblclick': function(el,idx,node,ev) {
					var s = el.store.getAt(idx);
				}
			}
		});

		// put it in a Panel so it looks pretty
		var panel = new Ext.Panel({
			id:'panel-IMUserList',
			renderTo:'userListIM',
			title:"Liste des utilisateurs",
			hidden:true,
			width:200,
			height:300,
			items: userListView
		});

		return panel;
	},


	// Ajouter un message dans la boite
	msg:function (result) {
		var bigContiner = $('im');
		var container = $('im_data');
		// On reçoit un message
		if (result.msg) {
			// Analyse de syntaxe spéciale pour messages privés
			var re  = /\/to=(.[^\/]*)\/(.*)/g;
			var match = re.exec(result.msg);
			if (match) {
				result.id_user_recipient_login = match[1];
				result.msg = match[2];
			}

			var m = document.createElement("div");
			if (result.current_id_user==result.id_user) {
				m.className = 'selfMessage';
			} else {
				m.className = 'newMessage';
			}

			if (result.id_user_recipient_login) {
				m.className += ' privateMessage';
			}

			m.innerHTML = "";

			// Représentation d'un message privé
			if (result.id_user_recipient_login) {
				m.innerHTML += "&gt;&gt;&gt; "+result.id_user_recipient_login+" | ";
			}

			// Représentation d'une action
			if (result.me) {
				m.innerHTML += "&gt;&gt;&gt; ";
			}

			// Auteur
			m.innerHTML += "<span class='im_name'>"+result.user+"</span> ";

			// Les deux points seulement si ce n'est pas une action, mais un message
			if (!result.me) {
				m.innerHTML += ": ";
			}

			// Message
			m.innerHTML += result.msg;

			// Position sur Optima
			if (result.location) {
				m.innerHTML += " ("+result.location+")";
			}

			if (el = Ext.getCmp('panel-IMchat-dialog')) {
				//Trouver le moyen d'ajouter l'HTML contenu dans m a la suite, l'update ecrase les anciennes donnée ici
				el.add(
					{
						xtype:'label',
						html:"<div class='panel-chat-item'>"+m.innerHTML+"</div>",
						id:result.id
					}
				);
				el.addClass(m.className);
				el.doLayout();
			}
//			container.appendChild(m);
//			container.scrollTop = container.scrollHeight;
		}


		// On reçoit la liste des users
		if (result.online) {
//			var listPanel = Ext.getCmp('panel-IMUserList');
//			listPanel.setTitle("Liste des utilisateurs ("+result.online.length+")");
//			for (var o=0; o<result.online.length; o++) {
//				if (!Ext.getCmp(result.online[o].id_user)) {
//					listPanel.add(
//						{
//							xtype:'label',
//							html:"<div class='panel-listUser-item'>"+result.online[o].user+"</div>",
//							id:result.online[o].id_user,
//							listeners:{
//								'click': function (el,ev) {
//									alert('phoque');
//									Ext.getCmp('imToolbar').addButton({
//										id: 'im-chatPrivate-'+result.online[o].id_user,
//										width:200,
//										text:result.online[o].user,
//										handler: function() {
//										}
//									});
//								}
//							}
//						}
//					);
//				}
//			}
//			listPanel.doLayout();
//			var onlineList = $('im_list_data');
//			$('im_list_nb').update(result.online.length);
//			onlineList.update('');
//			for (var o=0; o<result.online.length; o++) {
//				var m = document.createElement("div");
//				m.innerHTML = result.online[o].user+' <a href="javascript:;" onclick="$(\'im_input\').value=\'/to='+result.online[o].login+'/\'; $(\'im_input\').focus();">'+ATF.usr.trans('private_message','im')+'</a>';
//				onlineList.appendChild(m);
//			}
		}
	},

	// Ajouter un message dans la boite
	scrollMax:function () {
		if (e = Ext.getCmp('panel-IMchat-dialog')) {
//			var container = $('im_data');
//			container.scrollTop = container.scrollHeight;
			var d = e.el.dom;
			var bottom = e.height();


			e.el.scrollTo('bottom',bottom,true);

		}
	}
};

// ATF.ajax_refresh - onSucess suite de l'ajaj. Cette fonction est appellée à la fin de chaque requête ajaj
// Elle rafraichit le html, affiche des modalBox, affiche les erreurs, met à jour l'url.
// @param obj objets contenant les infos relative au rafraichissement. Les différentes options sont :
//		nosession : Renvoyé si l'utilisateur est déconnecté
//		alert : permet d'afficher un alert
//		cadre_refreshed : collection de cadre à rafraichir
//		result : Affiche une modalBox
//		url : Nouvelle url
//		error : Erreurs
//		notice : notices
//		warning : warnings ! (sisi c'est vrai !)
ATF.ajax_refresh = function(obj){
	//Test de validité de la session
	if(ATF.no_session(obj.nosession)){
		return;
	}

	//Message d'alerte personnalisé
	if (obj.alert) {
		/* Si un message d'alerte est passé en retour */
		Ext.Msg.alert(ATF.errorSystemTitle,obj.alert);
	}

	if (obj.result) {
		//Affichage d'une modalBox
		if (obj.result.modalbox) {
			Modalbox.show('<div>'+obj.result.modalbox.text+'</div>',obj.result.modalbox.params);
			var modalbox_already_raised = true;
		}

		//Messagerie instantanée
		if (obj.result.im) {
			ATF.im.msg(obj.result.im);
		}
	}

	//Retour en haut de la page !
	if(obj.scrollTop){
		scroll(0,0);
	}

	//Placement de l'ancre
	if(obj.ongletAnchor){
		if($('#anchorsTabs')){
			$('#anchorsTabs').show();
		}
		if($(obj.ongletAnchor)){
			$(obj.ongletAnchor).show();
		}
	}

	//Raffraichissement de la page
	if (obj.cadre_refreshed) {
		try{
			for (var it in obj.cadre_refreshed) {
				if ((typeof obj.cadre_refreshed[it]) != "function") {
					if(it=="title"){
						document.title=obj.cadre_refreshed[it];
					}else{
						if ($("#"+it+"_data").length && it+"_data"!="main_data") {
							$("#"+it+"_data").css({ 'min-height':'0px' });
							$("#"+it+"_data").html(obj.cadre_refreshed[it]);
						} else if ($("#"+it).length) {
							$("#"+it).css({ 'min-height':'0px' });
							$("#"+it).html(obj.cadre_refreshed[it]);
						}
					}
				}
			}
		}catch(e){
			//Traitement d'erreur dans les Js renvoyés
			ATF.setJsError(e);
		}
		// Au cas où il y a un listing, éditeurs
		ATF.bindListingSelectors();
		ATF.initEditors();
	}

	//Gestion de l'url
	if(obj.url && (!obj.error || obj.error.length==0) && obj.url!=ATF.previousPage){
		//On indique qu'on change d'url via le code métier
		ATF.changeUrl=true;
		//On met à jour l'url de currentPage (système d'onglet)
		//Cas ou obj.url contient un onglet (ancre)
		var reg1=new RegExp(":","g");
		if(obj.url.match(reg1)){
			ATF.currentPage=obj.url.substr(0,obj.url.indexOf(':'));
		}else{
			ATF.currentPage=obj.url;
		}
		//On met à jour le hash => grâce à changreURL à true l'url changera sans changement de page car celle-ci est déjà modifiée par le cadre_rereshed (voir js.tpl.js dans le onChange history)
		window.location.hash=obj.url;
	}

	//Gestion du skin
	if(obj.skin){
		ATF.loadObjs(obj.skin);
	}

	//Gestion des formsErrors (formulaire extjs)
	if (obj.formErrors && obj.formErrors.length>0) {
		ATF.formErrors = obj.formErrors;
		var highlight=false;
		var smartip=false;
		if(obj.formErrorConfig && obj.formErrorConfig.highlight) highlight=true;
		if(obj.formErrorConfig && obj.formErrorConfig.smartip) smartip=true;
		ATF.showFormErrors(highlight,smartip);
	}

	// Gestion d'erreurs. Affichage consécutif de modalbox
	if (obj.error && obj.error.length>0) {
		ATF.errors = obj.error;
		obj.notice = null;
		obj.warning = null;
		//Si il y a des erreurs => On revient sur l'url précédente
		if(ATF.previousPage && ATF.previousPage!=ATF.currentPage){
			ATF.changeUrl=true;//Indiquation au moteur d'url qu'on ne désire pas raffraichir
			window.location.hash=ATF.previousPage;//Changement de l'url
		}
		//Affichage des erreurs
		ATF.showError();
	} else {
		//Mise à jour de la page précédente (car la page est chargée à présent)
		ATF.previousPage=ATF.currentPage;

		//Affichage des notices
		if(obj.notice) {
			ATF.showNotice(obj.notice);
		}
		//Affichage des waning (notices rouges)
		if(obj.warning) {
			ATF.showWarning(obj.warning);
		}
	}

	//Présence d'une nouvelle version d'ATF
	if(!ATF.newVersionAlreadyDisplay && obj.newVersion){
		ATF.newVersionAlreadyDisplay=true;
		setTimeout("Modalbox.show('new_version.dialog', { scrollTo:0, width: 700, title: 'Nouvelle Version' })",2000);
	}

	// Ajustement graphique du div primary (patch design optima)
	setTimeout("ATF.adjustContainer();",1000);

	// Rendu des progressBar (patch extjs)
	setTimeout("ATF.renderProgressBars();",1000);

}

//	Complete un div avec un resultat HTML
//	@param obj objets contenant les infos relative au rafraichissement. Les différentes options sont :
//		alert : permet d'afficher un message a l'utilisateur avant le traitement des cadres
//		cadre_refreshed : collection de cadre à rafraichir
ATF.ajaxCompleter = function(url, data, container) {
	ATF.ajax(url, data, {
		 onSuccess: function (e) {
			$(container).update($(container).innerHTML+e.responseText);
			ATF.adjustContainer();
		}
	});
}

/* Applique aux listing génériques l'abilité des TR à être sélectionnés
@author Yann GAUTHERON <ygautheron@absystech.fr>
*/
ATF.listing_mapping = new Object({ listing_0:'listing_0_selected',listing_1:'listing_1_selected',listing_0_selected:'listing_0',listing_1_selected:'listing_1' });
ATF.bindListingSelectors = function () {
	$('tr.listing_0','tr.listing_1').each(function (s) {
		s.onclick = function () {
			this.className = ATF.trim11(this.className);
			$('input.check_select_row[value='+this.id+']').each(function (s) { s.checked = !s.checked; ATF.updSelTR(s); });
		}
	});
	$('input.check_select_row[value]').each(function (s) { s.onclick = function () { this.checked=!this.checked; } });
}

/* Modifier la sélection d'un listing
@author Yann GAUTHERON <ygautheron@absystech.fr>
@param string t Nom de la table
@param string flag (all|no|invert)
*/
ATF.updSel = function (t,flag,checkclass) {
	$('table.listing_table_'+t+' input.'+(checkclass ? checkclass : 'check_select_row')).each(function(e) {
		switch(flag) {
			case "all": e.checked = true; break;
			case "no": e.checked = false; break;
			default: e.checked = !e.checked;
		}
		if(!checkclass){
			ATF.updSelTR(e);
		}
	});
}
ATF.updSelTR = function (e) {
	if ($w(e.up().up().className).indexOf('listing_1')===-1 && $w(e.up().up().className).indexOf('listing_1_selected')===-1) {
		e.up().up().removeClassName('listing_0');
		e.up().up().removeClassName('listing_0_selected');
		if (e.checked) {
			// Listing1 à cocher
			e.up().up().addClassName('listing_1_selected');
		} else {
			// Listing1 à pas cocher
			e.up().up().addClassName('listing_1');
		}
	} else {
		e.up().up().removeClassName('listing_1');
		e.up().up().removeClassName('listing_1_selected');
		if (e.checked) {
			// Listing0 à cocher
			e.up().up().addClassName('listing_0_selected');
		} else {
			// Listing0 à pas cocher
			e.up().up().addClassName('listing_0');
		}
	}
}

/* Fonction TRIM
@author http://blog.stevenlevithan.com/archives/faster-trim-javascript
*/
ATF.trim11 = function (str) {
	str = str.replace(/^\s+/, '');
	for (var i = str.length - 1; i >= 0; i--) {
		if (/\S/.test(str.charAt(i))) {
			str = str.substring(0, i + 1);
			break;
		}
	}
	return str;
}

/* Récupère les id selectionnés d'un listing
@author Yann GAUTHERON <ygautheron@absystech.fr>
@param table Id du table qui contient le listing
@return string input trouvés en format URL
*/
ATF.getListingSelected = function (table,format) {
	var result = [];
	var resultNonCoche = [];

	$('table.listing_table_'+table+' > tbody > tr > td > input.check_select_row').each(function (s) {
		if (s.checked) {
			result.push(s.value);
		}else{
			resultNonCoche.push(s.value);
		}
	});

	if (result.length>0 && !format) {
		return "id[]="+result.join("&id[]=");
	}else if(result.length>0){
		return "id["+result.join("]=1&id[")+"]=1";
	}else if(format){
		//si aucune sélection et qu on est en update_all
		return "id["+resultNonCoche.join("]=1&id[")+"]=1";
	}
}

/* Applique un tooltip aux balises qui ont un <a rel="" et title=""
@author Yann GAUTHERON <ygautheron@absystech.fr>
*/
ATF.bindTips = function (resetAll) {
return;
	if (resetAll) {
		Stip.delAll();
	}
	$('a[rel]').each(function(element) {
		var opt = {
			position: 'rightTop'
			//, attach: true
			//, arrow:true
			//, arrowBase:20
			, borderSize:2
			, radius:2
			, css:'ex3'
			, delay:.5
//			, fadeDuration:.25
		};
		if (element.title) {
			opt.title = element.title;
			element.title = "";
		}
		Stip.add(element,element.rel,opt);
	});
}



/* Affiche les notices présentes dans ATF.errors sous forme d'une EXTJS notice
@author Yann GAUTHERON <ygautheron@absystech.fr>
@todo Faire quelquechose pour le HTML dans le javascript... :-/
*/
ATF.noticebox = function(){
    var msgCt;

    function createBox(t, s){
        return ['<div class="msg">',
                '<div class="x-box-tl"><div class="x-box-tr"><div class="x-box-tc"></div></div></div>',
                '<div class="x-box-ml"><div class="x-box-mr"><div class="x-box-mc"><h3>', t, '</h3>', s, '</div></div></div>',
                '<div class="x-box-bl"><div class="x-box-br"><div class="x-box-bc"></div></div></div>',
                '</div>'].join('');
    }

    return {
        msg : function(title, format, timer) {
            if(!msgCt){
                msgCt = Ext.DomHelper.insertFirst(document.body, { id:'msg-div' }, true);
            }
            msgCt.alignTo(document, 't-t');
            var s = String.format.apply(String, Array.prototype.slice.call(arguments, 1));
            var m = Ext.DomHelper.append(msgCt, { html:createBox(title, s) }, true);
            m.slideIn('t').pause((Number)(timer)).ghost("t", { remove:true });

            m.dom.onclick = function() { m.remove(); };
        },

        init : function(){
            var lb = Ext.get('lib-bar');
            if(lb){
                lb.show();
            }
        }
    };
}();
ATF.showNotice = function (n) {
	if (n) {
		ATF.notices = n;
	}

	if (ATF.notices && ATF.notices.length>0) {
		var e = ATF.notices.shift();
		var args = new Array();

//		if (!e.title) {
//			e.title = 'Pour votre information';
//		}

		if (!e.timer) {
			e.timer = 3.5;
		}

		ATF.noticebox.msg(e.title, e.msg, e.timer);

		// S'il reste des notices on les lances aussi immédiatement à la suite
		if (ATF.notices.length>0) {
			ATF.showNotice();
		}
	}
}
ATF.showWarning = function (n) {
	if (n) {
		ATF.warnings = n;
	}

	if (ATF.warnings && ATF.warnings.length>0) {
		var e = ATF.warnings.shift();
		var args = new Array();

		if (!e.title) {
			e.title = 'Attention';
		}

		if (!e.timer) {
			e.timer = 5;
		}

		ATF.noticebox.msg('<span style="color:red">'+e.title+'</span>', '<span style="color:red">'+e.msg+'</span>', e.timer);

		// S'il reste des warnings on les lances aussi immédiatement à la suite
		if (ATF.warnings.length>0) {
			ATF.showWarning();
		}
	}
}

/* Affiche les erreurs présentes dans ATF.errors sous forme de modalbox consécutives
@author Yann GAUTHERON <ygautheron@absystech.fr>
*/
ATF.showError = function () {
	if (ATF.errors && ATF.errors.length>0) {
		var e = ATF.errors.shift();
		var args = new Array();

		if (!e.msg.text) {
			e.msg.text = e.msg;
		}
		// Modalbox avec contenu avancé ou texte simple
		if (e.msg) {
			// Erreur basique
			args[0] = '<div style="color:red;font-weight:bold;">'+e.msg.text+'</div>';
			if (!args[1]) {
				args[1] = new Object();
			}
			args[1] = e.msg.params;
		}

		// S'il reste des erreurs, on cree un callBack de fermeture, qui envoi vers l'erreur suivante
		if (ATF.errors.length>0) {
			args[1].beforeHide = function() {
				ATF.showError();
				return false;
			}
		} else {
			args[1].beforeHide = function() {
				ATF.showNotice();
				ATF.showWarning();
				return false;
			}
		}

		ATF.log(ATF.errors);
		ATF.log(args);
		Modalbox.show(args[0], args[1]);
	}
}

/**
* Affiche les erreurs formulaire
* @author Jérémie Gwiazdowski <jgw@absystech.fr>
* @param bool highlight
* @param bool smartip
*/
ATF.showFormErrors = function(highlight,smartip) {
return;
	if (ATF.formErrors && ATF.formErrors.length>0) {
		var e = ATF.formErrors;
		// Modification de feuilles de styles pilotées par les erreurs
		if (e) {
			e.each(function(name,index){
				if ($(name) && $(name).type!='hidden') {
					if(highlight){
						$(name).addClassName("formError");
						//eval('$('+name+').onfocus = function () { this.removeClassName(\'formError\'); this.onfocus=\'\'; }');
					}
					if(smartip){
						var opt = {
							position:'rightTop'
							, attach:true
							, arrow:true
							, borderSize:4
							, radius:4
							, css:'ex3'
							, fadeDuration:.25
							, closeButton:name
							, hideAll:false
							, width:110
						//	, once:true
						};
						Stip.addNow(name,"Champ obligatoire !", opt);
					}
					if(highlight||smartip){
						$(name).onfocus = function () {
							if(highlight) this.removeClassName("formError");
							this.onfocus="";
							if($(name) && $(name).stip && $(name).stip.tip){
								$(name).stip.tip.destroy();
							}
						};
					}
				}
			});
		}
	}
}

ATF.panneau_lateral = function (element) {
	if ($(element).style.display=='none') {
	// Gestion du déroulement du panneau latéral
		new ATF.tpl2div("tpl2div.ajax", "div="+$(element).id+"&template=left", {
		  onSuccess: function (response,json) {
			  Effect.toggle(element,'appear',  { scaleX:true, scaleY:false, duration:.3 });
		  }
		});
	} else {
//		new Effect.Opacity(element, { duration:2, fps:25, from:1.0, to:0.0 });//
		Effect.toggle(element,'appear',  { scaleX:true, scaleY:false, duration:.3 });
	}
}

/*
	Fait une redirection
	@url
*/
ATF.__toLocation = function(url) {
	window.location = url;
}

/*
	Simule une activité pour éviter la déconnexion de l'utilisateur
*/
ATF.keepOnline = function () {
	if (typeof(Ext) != "undefined") {
		Ext.Ajax.request({
		   url: 'usr,keepOnline.ajax'
		});
	} else {
		$.ajax({ url: "usr,keepOnline.ajax" });
	}
	setTimeout('ATF.keepOnline();',300000);
}

/*
	Permet l'inclusion d'un fichier
	@filename
*/
ATF.include = function (fileName) {
	if (document.getElementsByTagName) {
		Script = document.createElement("script");
		Script.type = "text/javascript";
		Script.src = fileName;
		Body = document.getElementsByTagName("BODY");
		if (Body) {
			Body[0].appendChild(Script);
		}
	}
}

ATF.sendmail = function (table,to,subject,msg) {
	var panel = new Ext.FormPanel({
		frame: true,
		width: 690,
		height: 470,
		items: [
			{
				xtype: "textfield",
				fieldLabel: "To",
				name: "to",
				id: "to",
				value : to
			},{
				xtype: "textfield",
				fieldLabel: "Sujet",
				id: "sujet",
				name: "sujet",
				value: subject
			},{
				xtype: "textarea",
				fieldLabel: "Message",
				id: "message",
				name: "message",
				anchor: '100%',
				height:350,
				value: msg
			}
		],
		buttons:[{
			text : "Envoyer",
			handler : function(){

				ATF.ajax(table+",sendMailEXT.ajax"
				,{
					msg:Ext.getCmp('message').getValue()
					,to: Ext.getCmp('to').getValue()
					,subject : Ext.getCmp('sujet').getValue()
				}, {
					onComplete: function (r) {
						ATF.currentWindow.close();
						Ext.Msg.alert("", "Le(s) mail(s) a(ont) bien(s) été(s) envoyé(s)");

					}
				});
			}
		}]
	});

	ATF.currentWindow = new Ext.Window({
		title: '{ATF::$usr->trans("Envoi de mails")}',
		id:'mywindow',
		width: 700,
		height: 500,
		buttonAlign:'center',
		autoScroll:false,
		closable:true,
		items: [panel]
	}).show();
}


/*
	Gère la validation d'un formulaire de modification d'un element en AJAX
	http://dev.optima.absystech.net/accueil.html#societe-update-c4ca4238a0b923820dcc509a6f75849b.div
*/
ATF.update = function (element,params,forceURL) {
	// Désactive les éditeurs
	ATF.hideAllEditors();

	var myForm = $(element);
	if(ATF.speedmail){
		for (var i in ATF.speedmail.instances) {
			ATF.speedmail.instances[i]();
		}
	}

	if (!params) {
		params = new Object();
	}
	if (!params.onComplete) {
		params.onComplete = function(response,json) { ATF.speedmail = undefined; ATF.ajax_refresh(response); };
	}

	if (!forceURL) {
		forceURL = myForm.name+".ajax";
	}
	//requête AJAX
	new ATF.ajax(forceURL, myForm.serialize(), params);
}

/*
	Gère l'insertion d'un element en AJAX
	@param params paramètres supplémentaires
*/
ATF.cloner = ATF.insert = function (element,params,forceURL,speed_insert) {
	// Désactive les éditeurs
	ATF.hideAllEditors();

	var myForm = $(element);
	if (!params) {
		params = new Object();
	}
	if (!params.onComplete) {
		params.onComplete = function(response,json) { ATF.ajax_refresh(response); };
	}

	if (!forceURL) {
		forceURL = myForm.name+".ajax";
	}

	//requête AJAX
	if(speed_insert){
		new ATF.ajax(forceURL, "nocr=true&speed_insert=true&"+myForm.serialize(), params);
	}else{
		new ATF.ajax(forceURL, myForm.serialize(), params);
	}
}

/*
	Gère la suppression d'un element en AJAX
*/
ATF.deletor = function (table,id,post_in) {
	var post = "id_"+table+"="+id;

	if(post_in){
		post+='&'+post_in;
	}

	//requête AJAX
	new ATF.ajax(table+",delete.ajax", post, {
		onComplete: function(response,json) {
			ATF.ajax_refresh(response);
		}
	});
}
/*
	Compte à rebour avant execution commande
	@author Yann GAUTHERON <ygautheron@absystech.fr>
*/
ATF.countdownTimer = function(ms) {
    this.ms = ms;
    this.tp = 0;
}
/*
	Démarre un compte à rebour
	@param cmd Commande à executer à la fin
*/
ATF.countdownTimer.prototype.start = function(cmd,id_div) {
    if (this.tp > 0) this.reset();
    this.tp = window.setTimeout(cmd, this.ms);
}
ATF.countdownTimer.prototype.reset = function() {
    if (this.tp > 0) window.clearTimeout(this.tp);
    this.tp = 0;
}


/*
	Animation de la pagination au survol de la souris
	@author Yann GAUTHERON <ygautheron@absystech.fr>
*/
ATF.showPaging = function(elmt) {
	ATF.timer(elmt.id,1,function () {
		var i = 0;
		$('#'+elmt.id+' > div.retractable').each(function(element) {
			if (!element.id) {
				$(element).css({ width:(element.width())+"px", height:(element.height())+"px" });
				element.id = this.id+"_zut"+(++i);
			}
			if (!element.visible()) {
				Effect.Grow(element.id, { duration: .3 });
			}
		}.bind(elmt));
	}.bind(elmt));
}

ATF.hidePaging = function(elmt) {
	ATF.timer(elmt.id,1,function () {
		$('#'+elmt.id+' > div.retractable').each(
			function(element) {
				if ($(element.id)) {
					Effect.Shrink(element.id, { duration: .3 });
				}
			}.bind(elmt)
		)
	}.bind(elmt));
}

/*
	Fonction de déclenchement avec délai de fonctions, et annulation si appel multiple du même timer
	@author Yann GAUTHERON <ygautheron@absystech.fr>
	@param string id un id DOM
*/
ATF.timer = function (id,sec,callbackFunction) {
	id = $(id);
	if (id) {
		if (!ATF.timers) {
			ATF.timers = new Object();
		}
		if (ATF.timers[id.id]) {
			ATF.timers[id.id].cancel();
		}
		ATF.timers[id.id] = new Effect.Opacity(id.id, {  // Bricolage ! Utiliser scriptaculous comme timer........ :-/
			duration: sec,
			from: $(id.id).getStyle('opacity'),
			to: $(id.id).getStyle('opacity'),
			afterFinish: callbackFunction
		});
	}
}

/*
	Permet d'afficher un message d'attente en attendant par exemple un rafraichissement d'un listing ou d'une page de listing
	ATTENTION : Uniquement prévu pour le tabContent
	@author Yann GAUTHERON <ygautheron@absystech.fr>
	@param string id un id DOM
	@param string message Message à afficher
	@param callbackFunction la fonction de callback à appeler
*/
ATF.updWaitMsg = "";
ATF.updWait = function(id,message,callbackFunction) {
	message = ATF.updWaitMsg+message+'</div>';

	var element=$(id);
	if(element){
		element.css("min-height:"+element.height()+"px");

		if (callbackFunction) {
			//Petit effet de transition
			new Effect.Opacity(
				id,{
				from:1.0
				,to:0.0
				,duration:.5
				,afterFinish:function(){
					element.update(message);
					element.setOpacity(1.0);
					if (callbackFunction) {
						callbackFunction();
					}
				}
			});
		} else{
			element.update(message);
		}
	}
}

/*UPLOAD DES FICHIER EN AJAX*/
  function updateBytes(evt) {
	if (evt.lengthComputable) {
	  evt.target.curLoad = evt.loaded;
	}
  }

  function updateSpeed(target) {
	if (!target.curLoad) return;
	target.prevLoad = target.curLoad;
  }

  function updateProgress(evt) {
	 updateBytes(evt);
	if (evt.lengthComputable) {
	  var loaded = (evt.loaded / evt.total);
	  if (loaded < 1) {
		var newW = loaded * 150;
		if (newW < 10) newW = 10;
		evt.target.log.style.width = newW + "px";
	  }
	}
  }

  function loadError(evt) {
	evt.target.log.setAttribute("status", "error");
	evt.target.log.parentNode.parentNode.previousSibling.previousSibling.textContent = "error";
	clearTarget(evt.target);
  }

  function loaded(evt) {
	updateBytes(evt);
	evt.target.log.style.width = 150 + "px";
	evt.target.log.setAttribute("status", "loaded");
	evt.target.log.parentNode.parentNode.previousSibling.previousSibling.textContent = "";
	clearTarget(evt.target);
  }


  function initXHREventTarget(target, container) {
	var progressContainer = document.createElement("div");
	progressContainer.className = "progressBarContainer";
	container.appendChild(progressContainer);

	var progress = document.createElement("div");
	progressContainer.appendChild(progress);
	progress.className = "progressBar";

	target.log = progress;
	target.interval = setInterval(updateSpeed, 1000, target);
	target.curLoad = 0;
	target.prevLoad = 0;
	target.onprogress = updateProgress;
	target.onload = loaded;
	target.onerror = loadError;
  }

/*lancement du dl en ajax*/
ATF.start = function start(file,c) {
	try {
		// Essayer Internet Explorer
	   xhr = new ActiveXObject("Microsoft.XMLHTTP");
	}catch(e) {
		// Echec
		// Autres navigateurs
	  xhr = new XMLHttpRequest();
	}

	var container = $("#filename_upload");
	container.textContent = file.fileName;
	container.className = "filename";

	initXHREventTarget(xhr.upload, container);

	var tbody = $('#tbody');
	tbody.appendChild(container);
	tbody.style.display = "";

	if ($('#secondary')) {
		$('#secondary').css({ 'padding': (parseFloat($('#slipContainer').height())+parseFloat(55))+"px 0px 0px 0px" });
	}
	$('.slipContentContainer').each(function (e) {
		$(e).css({ height: 10+$('#slipContainer').height()+"px" });
	});

	xhr.open("POST", c+",upload_fichier.ajax");
	xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	var data = "table="+c+"&filename="+ file.fileName+"&content="+file.getAsBinary();
	xhr.send(data);
}

/*Upload de fichier en ajax*/
ATF.startXHR = function (c) {
	var files = $("#file").files;
        if (files) {
          var file = files.item(0);
          if (file && file.fileSize) {
            if (file.fileSize > (25 * 1024*1024)) {
              alert("Le fichier est trop gros: " + file.fileName + ", " + Number(file.fileSize/m).toFixed(1) + "Mo");
              return;
            }
            ATF.start(file,c);
            return;
          }
        }
        alert("Rien à uploader !");
}

/** Lors d'une saisie manuelle de l'horaire, vérification de la saisie
 *  Auteur : Quentin JANON <qjanon@absystech.fr>
 *  date : 16/10/2009
 *  param : name le nom de l'élément
 *  param : type le type du projet
 *  param : weekend true si le jour fait parti du week-end
 */
ATF.verif_horaire = function (name,type,weekend){
	tab = $(name).value.split(':');
	var time = new Date(2000,01,01,tab[0],tab[1],00,00);
	var hours = time.getHours();

	if (!$(name).value.match("^([0-9]|[0-1][0-9]|2[0-4]):[0-5][0-9]$")) {
		alert("Le temps inscrit n'est pas correctement formaté");
		$(name).value="0:00";
		return false;
	} else if(hours<0 || hours>24) {
		alert('La valeur doit être comprise entre 0:00 et 24:00 heures');
		$(name).value="0:00";
		return false;
	}

//Changement de la couleurSi on est dans la feuille de pointage
	if (type) {
		ATF.pointage.change_color_case_horaire(name,type,weekend);
	}
	return true;
}

/** Mise à jour du container spécial fait pas QJ
 *  Auteur : Quentin JANON <qjanon@absystech.fr>
 *  Auteur : Yann GAUTHERON <ygautheron@absystech.fr>
 *  @todo YG : C'est une méthode assez peu géniale, il faudrait voir pour remplacer tout ça par du relatif plutôt que de l'absolu en CSS des primary etc...
 */
ATF.adjustContainer = function () {
	$('.slipContentContainer').each(function (idx,e) {
		$(e).css({ height: (10+$('#slipContainer').height())+"px" });
	});
}

/** Number format en Javascript
 *  Auteur : Quentin JANON <qjanon@absystech.fr>
 *  Auteur : Yann GAUTHERPON <ygautheron@absystech.fr>
 *  @todo YG : C'est une méthode assez peu géniale, il faudrait voir pour remplacer tout ça par du relatif plutôt que de l'absolu en CSS des primary etc...
 */
ATF.number_format = function (num,dec,thou,pnt,curr1,curr2,n1,n2) {
	if (!curr1) curr1="";
	if (!curr2) curr2="";
	if (!n1) n1="";
	if (!n2) n2="";

	var x = Math.round(num * Math.pow(10,dec));
	if (x >= 0) n1=n2='';

	var y = (''+Math.abs(x)).split('');
	var z = y.length - dec;

	if (z<0) z--;

	for(var i = z; i < 0; i++)
		y.unshift('0');

	y.splice(z, 0, pnt);
	if(y[0] == pnt) y.unshift('0');

	while (z > 3) {

		z-=3;
		y.splice(z,0,thou);
	}

	var r = curr1+n1+y.join('')+n2+curr2;
	return r;
}

/** Construit une progress bar à l'aide de la magnifique librairie extjs
* @author Jérémie Gwiazdowski <jgw@absystech.fr>
* @param string element le nom du div ou l'on désire placer la toolbar
* @param string cls : le nom de la classe spécifique de la progressBar (progressRed, progressOrange, progressGrey, progressGreen, x-progress
* @param string texte : un texte alternatif
* @param int poucentage : un pourcentage ([0-100])
* @param boolean boutton true si on désire avoir deux boutons pour modifier la barre (+: l'id doit être "plus-element" et de façon analogue pour -) Attention pour ce dernier paramètre il faut que text=false
* @param int width La taille de la progressBar
* @param string callback fonction appellée lors de l'utilisation des boutons, elle permet l'exécution de fonctionnalités particulière et possède comme argument value qui permet d'utiliser la valeur de la progressBar
*/
ATF.progressBar = function(element,cls,text,pourcentage,boutton,width,callback){
	if(!ATF.bar){
			ATF.bar=Array();
	}
	ATF.bar[element]=new Ext.ProgressBar(
								{
									text:(text)?text:'0%',
									id:'bar-'+element,
									//renderTo:element,
									height: 20,
									width:(width)?width:'120',
									ctCls:'ctProgress',
									baseCls:(cls)?cls:'x-progress',
									per:(pourcentage)?pourcentage:0
								}
							);
	//Positionnement du pourcentage adéquat
	if(pourcentage){
		ATF.bar[element].updateProgress(pourcentage/100,(text)?text:pourcentage+'%');
	}

	//Construction des boutons dynamiques
	if(boutton && !text){
		var element2=element;
		//Mise à jour de la barre
		barUpdate = function(element,nombre,up){
			if(up){
				  if(element.per<100){
					  element.per=parseInt(nombre)+parseInt(element.per);
					  element.updateProgress(element.per/100,element.per+'%');
				  }
			}else{
				if(element.per>0){
				  element.per=parseInt(element.per)-parseInt(nombre);
				  element.updateProgress(element.per/100,element.per+'%');
				}
			}
			if(callback){
				callback(element.per);
			}
		 }
		 //Evènements
		 $('#plus-'+element).observe('click',function(){ barUpdate(ATF.bar[element],10,true); });
		 $('#moins-'+element).observe('click',function(){ barUpdate(ATF.bar[element],10,false); });
	}
}

/** Permet d'effectuer le rendu des progressBar
* @author Jérémie Gwiazdowski <jgw@absystech.fr>
*/
ATF.renderProgressBars = function(){
	if(ATF.bar){
		for(i in ATF.bar){
			if($(i)){
				try{
					if(!ATF.bar[i].rendered){
						ATF.bar[i].render(i);
					}
				}catch(error){
						//ATF.log(error);
				}
			}
		}
	}
}

//	Attachement des editeurs TinyMCE
//	@author Yann GAUTHERON <ygautheron@absystech.fr>
ATF.editorSettings = {
	simpleEditor:{
		mode : "none",
		language : "fr",
		theme : "advanced",
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,undo,redo,|,cleanup,|,bullist,numlist",
		theme_advanced_buttons2 : "",
		theme_advanced_buttons3 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true
//		editor_selector : "simpleEditor"
	},
	inventaireEditor:{
		mode : "none",
		language : "fr",
		theme : "advanced",
		theme_advanced_buttons1 : "bold,italic,underline,|,undo,redo,|,cleanup,|,bullist,numlist",
		theme_advanced_buttons2 : "",
		theme_advanced_buttons3 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true
//		editor_selector : "simpleEditor"
	},
	fullEditor:{
		// General options
		mode : "none",
		language : "fr",
		theme : "advanced",
//		editor_selector : "fullEditor",
		plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave",

		// Theme options
		theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
		theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

		// Example content CSS (should be your site CSS)
		content_css : "css/content.css",

		// Drop lists for link/image/media/template dialogs
		template_external_list_url : "lists/template_list.js",
		external_link_list_url : "lists/link_list.js",
		external_image_list_url : "lists/image_list.js",
		media_external_list_url : "lists/media_list.js",

		// Style formats
		style_formats : [
			{ title : 'Bold text', inline : 'b' },
			{ title : 'Red text', inline : 'span', styles : { color : '#ff0000' } },
			{ title : 'Red header', block : 'h1', styles : { color : '#ff0000' } },
			{ title : 'Example 1', inline : 'span', classes : 'example1' },
			{ title : 'Example 2', inline : 'span', classes : 'example2' },
			{ title : 'Table styles' },
			{ title : 'Table row 1', selector : 'tr', classes : 'tablerow1' }
		],

		// Replace values for the template plugin
		template_replace_values : {
			username : "Some User",
			staffid : "991234"
		}
	}
};
ATF.editors = new Object();
ATF.initEditors = function() {
	return; // Annulation tinyMCE car on passe par les htmleditor extJS
	$('textarea.simpleEditor').each(function(e) {
		if (!ATF.editors[e.id]) {
			ATF.editors[e.id] = true; // Protection, ne pas appliquer tinyMCE deux fois sur le même ID !
//			tinyMCE.execCommand('mceRemoveControl', false, e.id);
			tinyMCE.settings = ATF.editorSettings['simpleEditor'];
			tinyMCE.execCommand('mceAddControl', true, e.id);
		}
	});
	$('textarea.inventaireEditor').each(function(e) {
		if (!ATF.editors[e.id]) {
			ATF.editors[e.id] = true; // Protection, ne pas appliquer tinyMCE deux fois sur le même ID !
//			tinyMCE.execCommand('mceRemoveControl', false, e.id);
			tinyMCE.settings = ATF.editorSettings['inventaireEditor'];
			tinyMCE.execCommand('mceAddControl', true, e.id);
		}
	});
	$('textarea.fullEditor').each(function(e) {
		if (!ATF.editors[e.id]) {
			ATF.editors[e.id] = true; // Protection, ne pas appliquer tinyMCE deux fois sur le même ID !
//			tinyMCE.execCommand('mceRemoveControl', false, e.id);
			tinyMCE.settings = ATF.editorSettings['fullEditor'];
			tinyMCE.execCommand('mceAddControl', true, e.id);
		}
	});
}

//	Désactive les éditeurs avant de les poster
//	@author Yann GAUTHERON <ygautheron@absystech.fr>
ATF.hideAllEditors = function() {
	for (var i in ATF.editors) {
		tinyMCE.execCommand('mceRemoveControl', false, i);
		delete ATF.editors[i];
	}
}

//	Attachement d'un autocomplete ExtJS
//	@author Yann GAUTHERON <ygautheron@absystech.fr>
ATF.autocompleteConfig = function(infos) {
	var i = {
		store: new Ext.data.Store({
			proxy: new Ext.data.HttpProxy({
				url: infos.url
				,method:'POST'
				,listeners:{
					exception:function(obj,typ,act,opt,resp,arg){
						ATF.extRefresh({ response:resp });
					}
				}
			}),
			reader: new Ext.data.JsonReader({
				root: 'result'
				,totalProperty: 'totalCount'
			}, infos.mapping)
		}),
		loadingText: infos.loadingText,
		width: ((infos.width)?infos.width:300),
		noClearOnTrigger: infos.noClearOnTrigger,
		onTriggerClick: function(){
			if (!this.noClearOnTrigger) {
				this.clearValue(); // On reset le combo si on clique sur le trigger !
				//on reset le champs hidden (si il y a) qui contient la valeur de l'id
				if($(this.id.replace("label_",""))){
					$(this.id.replace("label_","")).value=null;
				}
			}

			if(this.readOnly || this.disabled){
				return;
			}
			if(this.isExpanded()){
				this.collapse();
				this.el.focus();
			}else {
				this.onFocus({});
				if(this.triggerAction == 'all') {
					this.doQuery(this.allQuery, true);
				} else {
					this.doQuery(this.getRawValue());
				}
				this.el.focus();
			}
		},
		pageSize:10,
		minChars:0,
		tpl: new Ext.XTemplate(infos.template),
		itemSelector: 'div.search-item'
	};

	if (infos.onSelect) {
		i.onSelect=infos.onSelect;
	}

	if (infos.applyTo) {
		i.applyTo=infos.applyTo;
	}

	return i;
}

//	Attachement d'un autocomplete ExtJS
//	@author Yann GAUTHERON <ygautheron@absystech.fr>
ATF.autocomplete = function(infos,infosSup) {
	var i = this.autocompleteConfig(infos);
	if (infosSup) {
		i = jQuery.extend(i,infosSup);
	}
	return new Ext.form.ComboBox(i);
}

/** On utilise la date d'extjs
* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
* @author Quentin JANON <qjanon@absystech.fr>
* @param nom : nom du champs texte sur lequel appliquer le type date
* @param formatage : formatage de la date ex : Y-m-d
* @modified quentin pour l'onchange
*/
ATF.datefield = function(nom,formatage,onchange){
	if(onchange){
		return new Ext.form.DateField({
			width:120
			,applyTo: nom
			,format: formatage
			,listeners: {
				change:onchange
			}
		});
	}else{
		return new Ext.form.DateField({
			width:120
			,applyTo: nom
			,format: formatage
		});
	}
}

ATF.dateTimeField = function(nom,valeurDate,valeurHeure){
	new Ext.form.CompositeField({
		renderTo: nom+'_bis'
		,items:[{
			xtype:'datefield'
			,listeners :{
				change:function(){
					$(nom).value=$(nom+'_date').value+' '+$(nom+'_time').value;
				}
			}
			,format:'d-m-Y'
			,width:100
			,name: nom+'_date'
			,id: nom+'_date'
			,value:valeurDate
		},{
			xtype:'timefield'
			,name: nom+'_time'
			,id: nom+'_time'
			,format:'H:i'
			,minValue: '9:00'
			,maxValue: '18:00'
			,increment: 30
			,width:65
			,value:valeurHeure
			,listeners :{
				change:function(){
					$(nom).value=$(nom+'_date').value+' '+$(nom+'_time').value;
				}
			}
		}]
	});
}

/** Ajouter un bouton clear sur tous les champs date
* @author Yann GAUTHERON <ygautheron@absystech.fr>
*/
if (typeof(Ext) != "undefined") {
	Ext.onReady(function(){
		if (Ext.form.DateField) {
			Ext.apply(Ext.form.DateField.prototype, {
				initComponent: function() {
						this.triggerConfig = {
								tag:'span', cls:'x-form-twin-triggers', cn:[
								{ tag: "img", src: Ext.BLANK_IMAGE_URL, cls: "x-form-trigger x-form-clear-trigger"},
								{ tag: "img", src: Ext.BLANK_IMAGE_URL, cls: "x-form-trigger x-form-date-trigger"}
						]};
						Ext.form.DateField.superclass.initComponent.apply(this, arguments);
				},
				onTrigger1Click : function() {
					this.setValue('');
					this.fireEvent('select',[this,null]);
				},
				getTrigger: Ext.form.TwinTriggerField.prototype.getTrigger,
				initTrigger: Ext.form.TwinTriggerField.prototype.initTrigger,
				onTrigger2Click: Ext.form.DateField.prototype.onTriggerClick,
				trigger1Class: Ext.form.DateField.prototype.triggerClass,
				trigger2Class: Ext.form.DateField.prototype.triggerClass
			});
		}
	});
}
/** Vérifie un email
 *  Auteur : Quentin JANON <qjanon@absystech.fr>
 */
ATF.verifMail = function (id) {
	valide1 = false;
	for(var j=1;j<(id.value.length);j++){
		if(id.value.charAt(j)=='@'){
			if(j<(id.value.length-4)){
				for(var k=j;k<(id.value.length-2);k++){
					if(id.value.charAt(k)=='.') valide1=true;
				}
			}
		}
	}
	return valide1;
}

//	Pour la modification des valeurs des champs sur le select_all
//	@author Nicolas BERTEMONT <nbertemont@absystech.fr>
var reinit_temps=new ATF.countdownTimer(1500);

ATF.__submitSearchChampsSelectAll = function(table,id,champs) {
	reinit_temps.start("$('__res_"+champs+"_"+id+"_loading').show(); ATF.__callSearchChampsSelectAll('"+table+"','"+id+"','"+champs+"');");
}
ATF.__callSearchChampsSelectAll = function(table,id,champs) {
	ATF.ajax(table+',update.ajax','nocr=true&id_'+table+'='+id+'&'+champs+'='+$(table+'['+champs+'_'+id+']').value,{ onComplete:function(obj) {  $('__res_'+champs+'_'+id+'_loading').hide(); } });
}

//	Permet d'éxecuter une ouverture/fermeture d'onglet et la sauvegarde de son état directement
//	@author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
//	@param string name Le nom de l'onglet (du module)
//	@param string parent_name Le nom du parent de l'ongleet
//	@param string div le nom du div, autrement dit l'attribut id
//	@param string fk_name Le nom de la clée étrangère
//	@param string fk_value La valeur de la clée étrangère
ATF.clickOnglet = function(name,parent_name,div,fk_name,fk_value){
	if ($(div+'_content')){
		var visible=$(div+'_content').visible();

		//Sauvegarde de l'onglet
		ATF.ajax(name+',saveOuvertureOnglet.ajax','onglet=gsa_'+parent_name+'_'+name+'&showorhide='+visible);

		if(visible){
			if(!ATF.godirect){
				ATF.closeOnglet(name,div);
			}
		}else{
			ATF.openOnglet(name,div,parent_name,fk_name,fk_value);
		}

		ATF.godirect=false;
	}
}

//	Permet d'ouvrir un onglet dans une fiche select
//	@author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
//	@param string name L'attribut name du div
//	@param string div le nom du div, autrement dit l'attribut id
ATF.openOnglet = function(name,div,parent_name,fk_name,fk_value){
	if($(name+'_switch2')){
		 $(name+'_switch2').hide();
	}
	if($(div+'_content')){
		if($(div+'_no_content')){
			Effect.toggle(div+'_content','blind', { duration: .5 });
			ATF.updWait(div+'_content',"Merci de patienter, le contenu de l'onget va s'afficher dans un instant...");
			ATF.tpl2div(name+',updateSelectAll.div','pager='+div+'&parent_class='+parent_name+'&function=&fk_name='+fk_name+'&fk_value='+fk_value);
		}else{
			Effect.toggle(div+'_content','blind', { duration: .5 });
		}
	}
	var divs=Array(div+'_pager',div+'_pagerSearch',div+'_pagerFilter',div+'_pagerColumn',div+'_pagerExport',div+'_pagerGeoloc',div+'_checkedSelectionDown',div+'_pagerSort');
	divs.each(function test(obj){
			if($(obj) && !$(obj).visible()){
				Effect.Appear(obj, { duration: .5 });
			}
		}
	 );
}

//	Permet de fermer un onglet dans une fiche select
//	@author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
//	@param string name L'attribut name du div
//	@param string div le nom du div, autrement dit l'attribut id
ATF.closeOnglet = function(name,div){
	if($(div+'_content')){
		Effect.toggle(div+'_content','blind', { duration: .5 });
	}
	if($(name+'_switch2')){
		$(name+'_switch2').show();
	}
	var divs=Array(div+'_pager',div+'_pagerSearch',div+'_pagerFilter',div+'_pagerColumn',div+'_pagerExport',div+'_pagerGeoloc',div+'_checkedSelectionDown',div+'_pagerSort');
	divs.each(function test(obj){
				if($(obj) && $(obj).visible()){
					Effect.Fade(obj, { duration: .5 });
				}
			}
	  );
}

//	Permet de Générer un onglet à la volée
//	@author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
//	@param string parent_name Le nom du parent de l'onglet (module conteneur)
//	@param string name Le nom de l'onglet
//	@param bool opened True si on désire ouvrir l'onglet
//	@param string field Le nom du champ ??
//	@param string table Le nom de la table ??
//	@param string function_name Le nom de d'un fonction ??
//	@param bool select_onglet True si on désire créer un onglet select
ATF.createOnglet = function(parent_name,id,name,opened,field,table,function_name,select_onglet,permapdf){
	if(!permapdf){
		permapdf=0;
	}
	ATF.ongletThreads.push(ATF.tpl2div(name+",onglet.ajax","template=generic-onglet&div=onglet_"+name+"_content&parent_name="+parent_name+"&id="+id+"&opened="+opened+"&field="+field+"&table="+table+"&function="+function_name+"&select="+select_onglet+"&permapdf="+permapdf,{ ongletAnchor:'anchor_'+table }));
}

//	Permet d'ajouter un onglet à la volée dans une fiche select
//	@author Nicolas BERTEMONT <nbertemont@absystech.fr>
//	@author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
//	@param string parent_name Le nom du parent de l'onglet (module conteneur)
//	@param string name Le nom de l'onglet
//	@param int id L'identifiant
ATF.addOnglet = function(parent_name,name,id,value){
	ATF.loadMask.show();
	ATF.tpl2div(parent_name+',save_onglet.div'
			 ,'table='+parent_name+'&id_'+parent_name+'='+id+'&ongletInsert='+value
			 ,{ onComplete:function(response){
				 	ATF.loadMask.hide();
				 	}
				}
	);
}

//	Permet de supprimer un onglet à la volée, la version est optimisée pour éviter le rafraichissement ajax
//	@author Nicolas BERTEMONT <nbertemont@absystech.fr>
//	@author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
//	@param string parent_name Le nom du parent de l'onglet (module conteneur)
//	@param string name Le nom de l'onglet
//	@param int id L'identifiant
ATF.rmOnglet = function(parent_name,name,id){
	ATF.loadMask.show();
	ATF.tpl2div(parent_name+',save_onglet.div'
			 ,'table='+parent_name+'&id_'+parent_name+'='+id+'&ongletDelete='+name
			 ,{ onComplete:function(response){
				 	ATF.loadMask.hide();
				 	}
				}
	);
}

// Permet de se rendre vers l'onglet à partir des ancres dans le top
// @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
// @param string item le nom de l'onglet (contact, societe,...)
ATF.goToOnglet = function(item){
	if($(item+'_switch2').length && $(item+'_switch2').getStyle('display')!="none"){
		if($('#onglet_'+item)){
			ATF.godirect=true;
			window.scroll(0,$('#onglet_'+item)[0].offsetTop);
			$(item+'_switch').onclick();
		}
	}else{
		if($('#onglet_'+item)[0]){
			window.scroll(0,$('#onglet_'+item)[0].offsetTop);
		}
	}
}

//	Permet de mettre à jour un menu déroulant sans mettre à jour de div
//	@author Yann GAUTHERON <ygautheron@absystech.fr>
//	@param string url URL Ajax
//	@param string data Paramètres a passer en ajax
//	@param string optionId ID DOM de l'élement <select>
//	@param string nullOptionText Texte à mettre sur une option de valeur nulle
ATF.updateHtmlOptions = function(url, data, optionId, nullOptionText) {
	if (!optionId) {
		throw ('Paramètre optionId manquant !');
	} else if(!$(optionId)) {
		throw ("'infos.optionId'"+" N'est pas un élément DOM !");
	}

	ATF.ajax(url,data,{
		onComplete: function (obj) {
			var sel=$(optionId);

			// On vide le menu déroulant
			while (sel.length > 0) {
				sel.remove(sel.length - 1);
			}

			// Si une valeur nulle
			if (nullOptionText) {
				var el = document.createElement('option');
				el.text = nullOptionText;
				el.value = "";
				try {
					sel.add(el, null); // standards compliant; doesn't work in IE
				} catch(ex) {
					sel.add(el); // IE only
				}
			}

			// On met à jour avec les nouveaux élements
			if (obj.result) {
				for (var i=0;i<obj.result.length;i++) {
					var el = document.createElement('option');
					el.text = obj.result[i].text;
					el.value = obj.result[i].value;
					if (obj.result[i].selected) {
						sel.selectedIndex = i; // On pré-sélectionne une option
					}
					try {
						sel.add(el, null); // standards compliant; doesn't work in IE
					} catch(ex) {
						sel.add(el); // IE only
					}
				}
			}
		}
	});
}

//	ExtJS : Mapping ne marche pas avec des champs contenant des points, il faut donc utiliser cette fonction avec "convert"
//	@author Yann GAUTHERON <ygautheron@absystech.fr>
//	@param string champ avec un '.'
ATF.conv = function (s) {
	return function(v, n){ return n[s]; }
}
ATF.extParseFields = function (a) {
	for (var i in a) {
		if (a[i].convert) { // Si 'convert' existe, c'est qu'il y a un '.'
			a[i].convert = ATF.conv(a[i].convert);
		}
	}
	return a;
}

//	Envoyer un permalink
//	@author Yann GAUTHERON <ygautheron@absystech.fr>
//	@param string table
//	@param string id
ATF.permalink = function (table,id) {
	Ext.Msg.prompt('Envoyer cette fiche en lien direct', 'Merci de noter une adresse email', function(btn, text) {
		if (btn == 'ok') {
			ATF.ajax(table+',sendPermalink.ajax','id='+id+'&email='+text);
		}
	});
}

//	Prévenir si on sort de la page !
//	@author Yann GAUTHERON <ygautheron@absystech.fr>
ATF.preventUnload = function () {
	window.onbeforeunload = function(){
//	window.onhashchange = function(){
		return '';//'Attention, vous allez perdre toutes les informations non validée ! Etes-vous sûr ?';
	};
}

//	Ne plus prévenir si on sort de la page !
//	@author Yann GAUTHERON <ygautheron@absystech.fr>
ATF.unPreventUnload = function () {
	window.onbeforeunload = null;
}

//	Prévenir si on sort de la page !
//	@author Yann GAUTHERON <ygautheron@absystech.fr>
ATF.setFormIsActive = function () {
	ATF.preventUnload();
	ATF.formIsActive = true;
}

//	Ne plus prévenir si on sort de la page !
//	@author Yann GAUTHERON <ygautheron@absystech.fr>
ATF.unsetFormIsActive = function () {
	ATF.unPreventUnload();
	ATF.formIsActive = false;
}

/* Rafraichissement des extJS */
ATF.extRefresh = function (action,orAction) {
	if (orAction) {
		action = orAction;
	}
	var i = Ext.util.JSON.decode(action.response.responseText);
	//delete i.cadre_refreshed;
	ATF.ajax_refresh(i);
	if ((!i.error || !i.error.length) && i.extTpl) {
		for (var t in i.extTpl) {
			Ext.ComponentMgr.get(t).removeAll();
			Ext.ComponentMgr.get(t).add(Ext.util.JSON.decode(i.extTpl[t]));
			Ext.ComponentMgr.get(t).doLayout(true);
		}
	}
}

/* Rafraichissement des extJS */
if (typeof(Ext) != "undefined" && Ext.grid && Ext.grid.EditorGridPanel) {
	Ext.ux.FormGridPanel = Ext.extend(Ext.grid.EditorGridPanel, {
		hiddenValue: null,
		onRender: function() {
			Ext.ux.FormGridPanel.superclass.onRender.apply(this, arguments);
			this.on('afteredit', this.refreshHiddenValues);
		},
		refreshHiddenValues: function() {
			var store = this.getStore();
			if (this.hiddenValue && store) {
				var datar = new Array();
				var records = store.getRange();
				for (var i = 0; i < records.length; i++) {
					datar.push(records[i].data);
				}
				this.hiddenValue.setValue(Ext.encode(datar));
			}
		}
	});
}
ATF.buildGridEditor = function (params) {
	var g = new Ext.ux.FormGridPanel(jQuery.extend({
		autoHeight:true,
		autoWidth:true,
		layout:'fit',
		trackMouseOver:true,
		disableSelection:true,
		loadMask: true
	},params));

	if (params.renderTo) {
		g.render(params.renderTo);
	}

	if (g.store) {
        g.store.on('load', g.refreshHiddenValues, g );
		g.store.load({ params:{ start:0, limit:30 } });
	}
	g.hiddenValue = new Ext.form.Hidden({
		xtype:'hidden',
		name: 'values_'+g.id,
		id: 'values_'+g.id,
		value: ''/*,
		getValue: function () {
			ATF.log("test");
			return this.value;
		}*/
	});
	return [g,g.hiddenValue];
}
ATF.formatNumeric = function (v) {
	v = (Math.round((v-0)*100))/100;
	v = (v == Math.floor(v)) ? v + ".00" : ((v*10 == Math.floor(v*10)) ? v + "0" : v);
	v = String(v);
	var ps = v.split('.'),

	whole = ps[0],
		sub = ps[1] ? '.'+ ps[1] : '.00',
		r = /(\d+)(\d{3})/;
	while (r.test(whole)) {
		whole = whole.replace(r, '$1' + ' ' + '$2');
	}
	v = whole + sub;
	return v;
}
//	ExtJS : Pour séparer la référence du libellé dans les grid avec combobox. Lors d'une sélection on a besoin de retourner les deux valeurs + libellé.
//  Ce séparateur permet de sécuriser les deux parties lors du onSelect du combo, et ainsi que les décapsuler dans le grid lors du renderer de la case.
//	@author Yann GAUTHERON <ygautheron@absystech.fr>
//	@param string extJSGridComboboxSeparator
ATF.extJSGridComboboxSeparator = "|#ref=";

// ATF.goto
// c'est la fonction à utiliser pour accèder aux pages suivantes en javascript (plus de tpl2div hein !)
// New : Il existe trois méthodes pour accèder à un module
// On peut le faire via le HTML :
//             <a href="#mon-module.html">Se rendre à mon module</a>
//  - via le javascript :
//             ATT.goTo('mon-module.html');
//  - via le php
//             ATF::mon-module()->redirection("select_all");
// REMARQUE IMPORTANTE : On appelle un onglet avec ATF.goTo(":hotline");
// @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
ATF.goTo = function(url){
	// Gestion des onglets (ancre)
	var reg1=new RegExp(":","g");
	if(url.match(reg1)){
		if(window.location.hash.match(reg1)){
			url=window.location.hash.substr(0,window.location.hash.indexOf(':'))+url;
		}else if (!url) {
		}else{
			// Ce cas est rencontré lorsqu'on a pas le accueil.html# dans l'URL, par exemple : http://dev.optima.absystech.net/societe-select-d42427bc3dc54064a667df843fa36214.html
			// Dans ce cas là il faut prendre le pathname pour intialisé correctement l'URL et pour éviter les erreur avec les ancres
			if (!window.location.hash) {
				url="#"+window.location.pathname.substr(1)+url;
			} else {
				url=window.location.hash+url;
			}
		}
		url=url.substr(1);
	}

	//--Présence d'un hash identique à l'url
	if(window.location.hash && window.location.hash.substr(1)==url){
		ATF.loadUrl(window.location.hash.substr(1));
		//--Fonctionnement normal--
	}else{
		//Ajout dans l'historique => Utilisation de l'écouteur onChange voir donc js.tpl.js
		Ext.History.add(url);
	}
}

// ATF.loadUrl
// charge une url statique en ajax
// @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
ATF.loadUrl = function(fullUrl){
	// Variable contenant les informations post (anciennement le 2ème paramètre de tpl2div)
	var post="";
	// Variable contenant l'url finale
	var url="";

	//-- Partie gestion de l'ancre --
	var reg1=new RegExp(":","g");
	if(fullUrl.match(reg1)){
		// On recherche l'onglet sur lequel on va se rendre
		var onglet=fullUrl.substr(fullUrl.indexOf(':')+1);
		url=fullUrl.substr(0,fullUrl.indexOf(':'));

		// Test : Aujourd'hui on ne peut pas avoir d'ancre sur des fiches autres que select
		reg1=new RegExp("select","g");
		if(url.match(reg1)){
			// Test : Si la previous page est la fiche select on ne recharge pas la page !
			if(ATF.previousPage==url){
				ATF.goToOnglet(onglet);
			// Chargement de la page avec un focus sur l'onglet
			}else{
				ATF.currentPage=url;
				//Affichage de la page de l'overlay de chargement
				ATF.loadMask.show();
				//Requête Ajax
				ATF.tpl2div(url.substr(0,url.length-5)+".div",null,{ onComplete:function(){ ATF.loadMask.hide(); setTimeout("ATF.goToOnglet('"+onglet+"')",3000); } });
			}
		}else{
			Ext.Msg.alert(ATF.errorSystemTitle
						  ,ATF.errorSystemMsg+"L'url n'est pas valide.</div>"
						  ,function(btn,text){
							  if(btn == 'ok'){
								  Ext.History.back();
						  		}
						  },{
							  minWidth:300
						  });
		}
	//-- Cas classique --
	}else{
		// Pour éviter la concurrence des requêtes On stoppe tous les threads précédents
		// Fermeture de la requête précédente
		if(ATF.loadMask.lastXHR && ATF.loadMask.lastXHR.conn){
			ATF.loadMask.lastXHR.conn.abort();
		}

		// Fermeture des threads précédents
		if(ATF.ongletThreads){
			for(key in ATF.ongletThreads) {
			  if(ATF.ongletThreads[key] && ATF.ongletThreads[key].conn) ATF.ongletThreads[key].conn.abort();
			}
			ATF.ongletThreads=new Array();
		}

		ATF.currentPage=fullUrl;
		// Test : On recherche les varaibles post
		reg1=new RegExp(",","g");
		if(fullUrl.match(reg1)){
			post=fullUrl.substr(fullUrl.indexOf(',')+1);
			url=fullUrl.substr(0,fullUrl.indexOf(',')-5);
		}else{
			url=fullUrl.substr(0,fullUrl.length-5);
		}
		// Test : Si la previous page est la fiche select on ne recharge pas la page !
		if(ATF.previousPage==url+".html"){
			ATF.goToOnglet(onglet);
		}else{
			//Affichage de la page de l'overlay de chargement
			ATF.loadMask.show();
			//Requête Ajax
			ATF.loadMask.lastXHR=ATF.tpl2div(url+".div",post,{
											 onComplete:function(){
													ATF.loadMask.hide();
													scroll(0,0);
													//Sélection de la rubrique
													if(ATF.highlightMenuItem){
														var reg2=new RegExp("-","g");
														if(url.match(reg2)){
															ATF.highlightMenuItem(url.substr(0,url.indexOf('-')));
														}else{
															ATF.highlightMenuItem(url);
														}
													}
												}
											 });
		}
	}
}

// ATF.loadObjs
// Charge un js ou un css dynamiquement
// Placé par jgwiazdowski sur ATF. Issue de AdSolaris
// @author Yann GAUTHERON <ygautheron@absystech.fr>
ATF.loadObjs = function (){
	if (!document.getElementById) return;
	for (i=0; i<arguments.length; i++){
		var file=arguments[i]
		var fileref=""
		if (file.indexOf(".js")!=-1){ //If object is a js file
			fileref=document.createElement('script')
			fileref.setAttribute("type","text/javascript");
			fileref.setAttribute("src", file);
		}
		else if (file.indexOf(".css")!=-1){ //If object is a css file
			fileref=document.createElement("link")
			fileref.setAttribute("rel", "stylesheet");
			fileref.setAttribute("type", "text/css");
			fileref.setAttribute("href", file);
		}
		if (fileref!=""){
			document.getElementsByTagName("head").item(0).appendChild(fileref)
		}
	}
}

/* Gestion des formulaires, champ de type searchField */
if (typeof(Ext) != "undefined") {
	Ext.onReady(function(){
		if (Ext.form.TwinTriggerField) {
			Ext.ns('Ext.ux.form');
			Ext.ux.form.SearchField = Ext.extend(Ext.form.TwinTriggerField, {
				initComponent : function(){
					Ext.ux.form.SearchField.superclass.initComponent.call(this);
					this.on('specialkey', function(f, e){
						if(e.getKey() == e.ENTER){
							this.onTrigger2Click();
						}
					}, this);
					this.on('afterrender', function(f, e){
						if(this.value){
							this.triggers[0].show();
							this.hasSearch = true;
						}
					}, this);
				},

				validationEvent:false,
				validateOnBlur:false,
				trigger1Class:'x-form-clear-trigger',
				trigger2Class:'x-form-search-trigger',
				hideTrigger1:true,
				width:180,
				hasSearch : false,
				paramName : 'query',

				onTrigger1Click : function(){
					if(this.hasSearch){
						this.el.dom.value = '';
						var o = { start: 0 };
						this.store.baseParams = this.store.baseParams || { };
						this.store.baseParams[this.paramName] = '';
						this.store.reload({ params:o });
						this.triggers[0].hide();
						this.hasSearch = false;
					}
				},

				onTrigger2Click : function(){
					var v = this.getRawValue();
					if(v.length < 1){
						this.onTrigger1Click();
						return;
					}
					var o = { start: 0 };
					this.store.baseParams = this.store.baseParams || { };
					this.store.baseParams[this.paramName] = v;
					this.store.reload({ params:o });
					this.hasSearch = true;
					this.triggers[0].show();
				}
			});
		}
	});
}
// ATF.blank_png est le format base64 d'un pixel PNG
// @author Yann GAUTHERON <ygautheron@absystech.fr>
ATF.blank_png = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyBpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBXaW5kb3dzIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOkJENDZGRjg0N0ZBNDExRTA5Qjk3OTI5RTBDOEI2ODg1IiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOkJENDZGRjg1N0ZBNDExRTA5Qjk3OTI5RTBDOEI2ODg1Ij4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6QkQ0NkZGODI3RkE0MTFFMDlCOTc5MjlFMEM4QjY4ODUiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6QkQ0NkZGODM3RkE0MTFFMDlCOTc5MjlFMEM4QjY4ODUiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz6Rfc00AAAAEElEQVR42mL4//8/A0CAAQAI/AL+26JNFgAAAABJRU5ErkJggg==';

// ATF.render
// Fonctions de rendus des cellules dans les listings, si le renderer existe
// @param Objet de paramètres
// 		string		.renderer
// 		string		.table
// 		string		.field
// @author Yann GAUTHERON <ygautheron@absystech.fr>
ATF.render=function(renderer, table, field, targetTable){
	if (ATF.renderer[renderer]) {
		return ATF.renderer[renderer](table,field,targetTable);
	}
	return ATF.renderer.searchHighlight;
};

ATF.renderer = {};
/* Met un span autour de ce qui a été recherché */
ATF.renderer.searchHighlight=function(val, meta, record, rowIndex, colIndex, store){
	if (val) {
		val = (String)(val);
		val = val.replace(new RegExp("\\n",'g'),'<br />').replace(new RegExp("\\r",'g'),'');
		val = val.replace(new RegExp('&lt;!--.*--&gt;','g'),'');
		val = val.replace(new RegExp('<xml>.*</xml>','g'),'');
		val = val.replace(new RegExp('<style>.*</style>','g'),'');
		val = val.replace(new RegExp('<br \/>','g'),"\n").replace(new RegExp('<br>','g'),"\n");
		val = val.replace(new RegExp("\\n\\n",'g'),"\n");
		val = Ext.util.Format.stripTags(Ext.util.Format.stripScripts(val));
		val = val.replace(new RegExp("\\n",'g'),"<br />");
		var keywords=store.searchBox.getValue();
		if (keywords) {
			return val.replace(new RegExp('('+keywords+')','i'), '<span class="searchSelectionFound">\$1</span>');
		}
		return val;
	}
};
ATF.renderer.actions=function(table,noSelect,noUpdate,selectExtjs) {
	return function(val, meta, record, rowIndex, colIndex, store) {
		if(!val){
			var id = record.data[table+'__dot__id_'+table];
			if (!id) var id = record.data[table+'__dot__id_'+table];
			html = "";
			if (!noSelect) {
				html += '<a href="#'+table+'-select-'+id+'.html"><img class="smallIcon select" src="'+ATF.blank_png+'" /></a>';
			}
			if (!noUpdate) {
				if (selectExtjs) {
					html += ' <a href="#'+table+'-select-'+id+'.html,edit=1"><img class="smallIcon update" src="'+ATF.blank_png+'" /></a>';
				} else {
					html += ' <a href="#'+table+'-update-'+id+'.html"><img class="smallIcon update" src="'+ATF.blank_png+'" /></a>';
				}
			}
			return html;
		}else{
			return val;
		}
	}
};
ATF.renderer.foreignKey=function(table,field,targetTable) {
	return function(val, meta, record, rowIndex, colIndex, store) {
		if (val) {
			id = record.data[table+'__dot__'+field+'_fk'];
			if (!id) {
				id = record.data[table+'__dot__'+field];
			}
			return '<a href="#'+targetTable+'-select-'+id+'.html">'+val+'</a>';
		}
	}
};
ATF.renderer.file=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		if(record.json){
			id = record.data[table+'__dot__id_'+table];
			if (ATF.is_object(filetype) && filetype.force_generate==true && filetype.fonction) {
				return '<a href="'+filetype.fonction+'-'+id+'.pdf" alt="'+ATF.usr.trans("popup_download",table)+'" target="_blank">'+
					'<img class="smallIcon pdf" src="'+ATF.blank_png+'" class="icone" />'+
					'</a>';
			} else if (filetype) {
				return '<a href="'+table+'-select-'+field+'-'+id+'.dl" alt="'+ATF.usr.trans("popup_download",table)+'">'+
					'<img class="smallIcon '+filetype+'" src="'+ATF.blank_png+'" class="icone" />'+
					'</a>';
			} else {
				return '<img class="smallIcon noAction" src="'+ATF.blank_png+'" />';
			}
		}
	}
};
ATF.renderer._datefield=function(val, meta, record, rowIndex, colIndex, store) {
	if (val) {
		if(val=="0000-00-00" || val.length<1){
			return "";
		} else if (val.length>10) { // Date avec temps
			val = Date.parseDate(val,"Y-m-d H:i:s");
			return Ext.util.Format.dateRenderer("d/m/Y H\\hi")(val, meta, record, rowIndex, colIndex, store);
		} else if(val.match(new RegExp("[:]","g"))){ // Temps seulement
			val = Date.parseDate(val,"H:i:s");
			return Ext.util.Format.dateRenderer("H\\hi\'s\'\'")(val, meta, record, rowIndex, colIndex, store);
		} else { // Date seulement
			val = Date.parseDate(val,"Y-m-d");
			return Ext.util.Format.dateRenderer("d/m/Y")(val, meta, record, rowIndex, colIndex, store);
		}
	}
};
ATF.renderer.datefield=function(table,field) {
	return ATF.renderer._datefield;
};
ATF.renderer.temps=function(table,field) {
	return function(h, meta, record, rowIndex, colIndex, store) {
		if (h) {
			var m = Math.round(60 * ( (h*100) % 100 ) / 100);
			if (m == 0) {
				m = "00";
			} else if (m < 10) {
				m = "0"+m;
			}
			h = Math.floor(h);

	//		if (workDays) {
				var r;
				if (h>=7) { // Calcul des jours
					j = Math.floor(h/7);
					h = h % 7;
					r = j+"j "+h+"h"+m;
				}
	//		}
			return r?r:h+"h"+m;
		}
	}
};
ATF.renderer.tel=function(table,field) {
	return function(val, meta, record, rowIndex, colIndex, store) {
		if (val) {
			var f = record.fields.items[colIndex-1].name.split("__dot__");
			var id = record.data[f[0]+'__dot__id_'+f[0]];
			if (val.length==10) {
				val = val.substr(0,2)+" "+val.substr(2,2)+" "+val.substr(4,2)+" "+val.substr(6,2)+" "+val.substr(8,2);
			}
			return '<a onclick="ATF.tpl2div(\'asterisk,createCall.ajax\',\'id='+id+'&amp;table='+f[0]+'&amp;field='+f[0]+'.'+f[1]+'\');" href="javascript:;"><img class="smallIcon call" src="'+ATF.blank_png+'" /></a>&nbsp;'+val;
		}
	}
};
ATF.renderer.url=function(table,field) {
	return function(val, meta, record, rowIndex, colIndex, store) {
		if (val) {
			var f = record.fields.items[colIndex-1].name.split("__dot__");
			var id = record.data[f[0]+'__dot__id_'+f[0]];
			return '<a href="'+val+'" target="_blank"><img class="smallIcon url" src="'+ATF.blank_png+'" /></a>&nbsp;'+val;
		}
	}
};
ATF.renderer.email= function(table,field) {
	return function(val, meta, record, rowIndex, colIndex, store) {
		if (val) {
			return '<a href="mailto:'+val+'" class="email"><img class="smallIcon email" src="'+ATF.blank_png+'" /></a>&nbsp;<a href="mailto:'+val+'" class="email">'+val+'</a>';
		}
	}
};
ATF.renderer.priorite=function(table,field) {
	return function(val, meta, record, rowIndex, colIndex, store) {
		if(val>=0){
			// Libellé étape
			function getText(etat,mep){
				if (mep=="oui") {
					etat = "MEP_prevue";
				}
				return ATF.usr.trans(etat,'hotline');
			}

			// Couleur
			function getColor(etat,priorite,mep) {
				if (mep=="oui") {
					return "green";
				}
				switch(etat){
					case "free":
						return "red";
					case "annulee":
						return "grey";
					default:
						if(priorite<10){
							return "green";
						}else if(priorite<15){
							return "orange";
						}else{
							return "red";
						}
				}
				return "grey";
			}

			// Pourcentage
			function getPercent(etat,avancement){
				switch(etat){
					case "fixing":
					case "wait":
						return 20+0.6*avancement;
					case "done":
						return 90;
					case "payee":
						return 100;
					case "annulee":
						return 100;
				}
				return 0;
			}
			var etat = record.data['hotline__dot__etat'];
			var avancement = record.data['hotline__dot__avancement'];
			var mep = record.data['hotline__dot__wait_mep'];
			var txt = val+'/'+getText(etat,mep);
			var pct = getPercent(etat,avancement);
			var color = getColor(etat,val,mep);
			var pct = parseFloat(getPercent(etat,avancement)/100);
			var idProgress = Ext.id();
			(function(){
				// Barre de progression
				var params = {
					renderTo: idProgress,
					value: pct,
					text:txt,
					ctCls:color+'Bar'
				};
				if($(idProgress)){
					var p = new Ext.ProgressBar(params);
				}
		//		record.addListener('click', function () { alert('test'); }, record, { target:p });
			}).defer(25);
		//	var js = "Ext.getCmp('"+record.id+"').beginEdit();";
			return '<span id="' + idProgress + '"></span>';
		}
	}
};

ATF.renderer.ponderation=function(table,field) {
	return function(val, meta, record, rowIndex, colIndex, store) {
		if(val>=0){

			// Couleur
			function getColor(priorite) {
				if(priorite<4){
					return "green";
				}else if(priorite<7){
					return "orange";
				}else{
					return "red";
				}
				return "grey";
			}

			// Pourcentage
			function getPercent(priorite){
				return priorite*10;
			}

			var txt = val;
			var pct = getPercent(val);
			var color = getColor(val);
			var pct = parseFloat(getPercent(val)/100);
			var idProgress = Ext.id();
			(function(){
				// Barre de progression
				var params = {
					renderTo: idProgress,
					value: pct,
					text:txt,
					ctCls:color+'Bar'
				};
				if($(idProgress)){
					var p = new Ext.ProgressBar(params);
				}
		//		record.addListener('click', function () { alert('test'); }, record, { target:p });
			}).defer(25);
		//	var js = "Ext.getCmp('"+record.id+"').beginEdit();";
			return '<span id="' + idProgress + '"></span>';
		}
	}
};
ATF.renderer.progress=function(table,field) {
	return function(val, meta, record, rowIndex, colIndex, store) {
		if(val>=0){
			var idProgress = Ext.id();
			val = parseFloat(val/100);
			(function(){
				// Barre de progression
				var params = {
					renderTo: idProgress,
					value: val,
					text:Math.round(val*100)+" %"
				};
				if (val>.66) {
					params.ctCls='greenBar';
				} else if (val>.33) {
					params.ctCls='orangeBar';
				} else {
					params.ctCls='redBar';
				}
				if($(idProgress)){
					var p = new Ext.ProgressBar(params);
				}
			}).defer(25);
			return '<span id="' + idProgress + '"></span>';
		}
	}
};
ATF.renderer.meteo=function(table,field) {
	return function(val, meta, record, rowIndex, colIndex, store) {
		if (val) {
			val = parseFloat(val);
			var echelle = {};
			var r = {};
			if(val!=0){
				//Définition des icones
				var icones = [];
				icones[1]="Sunny";
				icones[2]="Mostly_Sunny";
				icones[3]="Mostly_Cloudy";
				icones[4]="Cloudy";
				icones[5]="Rain";
				icones[6]="Cloudy_With_Dizzle";
				icones[7]="Rain_Or_Snow";
				icones[8]="Flurries";
				icones[9]="Snow";
				icones[10]="Thunderstorms";

				if (!ATF.meteo) {
					throw 'ATF.meteo not found !';
				}
				meteo_moyenne = ATF.meteo.meteo_moyenne;
				small = ATF.meteo.small;
				big = ATF.meteo.big;
				j=0;
				var echelle = [];
				for(i=2;i<=10;i+=2){
					j++;
					echelle[j]=((meteo_moyenne)-small)*(i/10)+small;
				}

				for(i=2;i<=10;i+=2){
					j++;
					echelle[j]=(big-(meteo_moyenne))*(i/10)+(meteo_moyenne);
				}

				//Pour chaque societe meteo on définit son classement
				for(i=1;i<=10;i++){
					if(val<=echelle[i]){
						r["icone"]=icones[i];
						r["echelle"]=i;
						//On sort de la boucle
						i=11;
					}
				}
			} else {
				r["icone"]="Fog";
				r["echelle"]="Pas de données pour la météo";
			}

			val = '<a rel="'+(r["echelle"]?r["echelle"]+'/10':'NC')+'">';
			val += '<img class="meteo '+r["icone"]+'" src="'+ATF.blank_png+'" />';
			val += '</a>';
			return val;
		}
	}
};
ATF.renderer.atcard=function(table,field) {
	return function(val, meta, record, rowIndex, colIndex, store) {
		if(record.json){
			var id = record.data['societe__dot__id_societe'];
			return '<a href="societe,atcard.ajax,id_societe='+id+'"><img class="smallIcon atcard" src="'+ATF.blank_png+'" /></a>';
		}
	}
};
ATF.renderer.derniereFacture=function(table) {
	return function(val, meta, record, rowIndex, colIndex, store) {
		if(record.json){
			var v = ATF.renderer._datefield(val, meta, record, rowIndex, colIndex, store);
			if (val) {
				var d = new Date(val);
				var now = new Date();
				var diff = now-d;
				var days = Math.round(diff/(1000*60*60*24));
				if (days<365) {
					return '<img class="smallIcon ledgreen" src="'+ATF.blank_png+'" title="'+v+'" alt="'+v+'" />';
				}
				return '<img class="smallIcon ledorange" src="'+ATF.blank_png+'" title="'+v+'" alt="'+v+'" />';
			}
			return '<img class="smallIcon ledred" src="'+ATF.blank_png+'" title="'+v+'" alt="'+v+'" />';
		}
	}
};

/* Renderer pour l'upload de fichier sur un select all EXTJS */
/* pour l'EXTJS 4.0 voir la docs ici : http://docs.sencha.com/ext-js/4-0/#/api/Ext.form.field.File */
/* @author Quentin JANON <qjanon@absystech.fr> */
ATF.renderer.uploadFile=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		if(record.json){
			var idDivFU = Ext.id();
			var id = record.data[table+'__dot__id_'+table];
			(function(){
				var params = {
					renderTo: idDivFU,
					fileUpload: true,
					width:24,
					layout:'fit',
					items:[{
						xtype:'fileuploadfield',
						id:field,
						buttonText: '',
						buttonOnly: true,
						buttonCfg: {
							iconCls: 'fileUploadIcon'
						},
						listeners: {
							'fileselected': function(fb, v){
								p.getForm().submit({
									submitEmptyText:false,
									method  : 'post',
									url     : 'extjs.ajax',
									params: {
										'id':id
										,'extAction':table
										,'extMethod':"uploadFileFromSA"
									}
									,success:function(form, action) {
										ATF.extRefresh(action);
										store.load();
									}
									,failure:function(form, action) {
										ATF.extRefresh(action);
									}
									,timeout:3600
								});
							}
						}
					}]

				};
				var p = new Ext.FormPanel(params);
			}).defer(25);

			html = '<div class="floatLeft" id="'+idDivFU+'" style="width:50%; text-align:center"></div>';

			html += '<div class="floatLeft" style="width:50%; text-align:center">';

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
/*
	Renderer pour la modification de date.
	@author Quentin JANON <qjanon@absystech.fr>
*/
ATF.renderer.updateDate=function(table,field) {
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
					format: 'd-m-Y',
					defaults: {
						xtype: 'datefield',
						format: 'd-m-Y',
						width:150,
						hideLabel:true
					},
					items:[{
						id:table+"updateDate"+id+field+Ext.id(),
						name:"updateDate",
						value:record.data[table+'__dot__'+field],
						listeners :{
							select:function(f,n,o){
								ATF.ajax(table+',updateDate.ajax','id_'+table+'='+id+'&key='+field+'&value='+f.value);
								store.load();
							}
							,change:function(f,n,o){
								ATF.ajax(table+',updateDate.ajax','id_'+table+'='+id+'&key='+field+'&value='+f.value);
								store.load();
							}
						}
					}]
				};
				var p = new Ext.FormPanel(params);
			}).defer(25);

			return '<div  id="'+idDivUD+'"></div>';
		}
	}
};
/*
	Renderer pour le rating.
	@author Quentin JANON <qjanon@absystech.fr>
*/
ATF.renderer.rating=function(table,field) {
	return function(val, meta, record, rowIndex, colIndex, store) {
		if(record.json){
			var idDivUD = Ext.id();
			var id = record.data[table+'__dot__id_'+table];
			var note = record.data[table+'__dot__note'];

			html = '<span class="ratingSpriteGrey" title="'+note+'">';
			html += '<span class="ratingSpriteYellow" style="width: '+(note*10)+'%;"></span>';
			html += '</span>';

			return '<div  id="'+idDivUD+'">'+html+'</div>';
		}
	}
};
/*
	Renderer pour la modification de date.
	@author Quentin JANON <qjanon@absystech.fr>
*/
ATF.renderer.etat=function(table,field) {
	return function(val, meta, record, rowIndex, colIndex, store) {
		var idDivE = Ext.id();
		var id = record.data[table+'__dot__id_'+table];
		if (record.data[table+'__dot__'+field]) {
			var etat = record.data[table+'__dot__'+field];
		} else {
			var etat = record.data[field];
		}

		if (etat) {
			var defaut = false;
			html = '<img class="smallIcon ';
			switch (etat) {
				case 'accepte':
				case 'accepter':
					html += 'accepte';
				break;
				case 'nok':
				case 'refus':
				case 'refuse':
				case 'refuser':
				case 'perdu':
					html += 'refus';
				break;
				case 'termine':
				case 'terminee':
				case 'done':
				case 'fini':
					html += 'done';
				break;
				case 'immo':
				case 'immobilise':
					html += 'immo';
				break;
				case 'livr':
					html += 'done';
				break;
				case 'livraison':
					html += 'livraison';
				break;
				case 'lock':
				case 'bloque':
				case 'close':
					html += 'lock';
				break;
				case 'unlock':
				case 'open':
					html += 'unlock';
				break;
				case 'arreter':
				case 'annulee':
				case 'annule':
				case 'cancel':
				case 'mort':
				case 'erreur':
				case 'probleme':
					html += 'perdu';
				break;
				case 'ok':
				case 'gagne':
				case 'valid':
				case 'valide':
				case 'validee':
				case 'valider':
					html += 'valid';
				break;
				case 'attente':
				case 'en_attente':
				case 'non_loyer':
				case 'sending':
				case 'wait':
					html += 'wait';
				break;
				case 'envoyee':
				case 'sent':
					html += "sent"
				break;
				case 'a_regler':
				case 'attente_paiement':
				case 'impayee':
					html += "impayee"
				break;
				case 'payee':
				case 'facturee':
				case 'vente':
					html += "payee"
				break;
				case 'reception':
				case 'recu':
					html += "recu";
				break;
				case 'sinistr':
				case 'sinistre':
					html += "sinistre";
				break;
				case 'defectueux':
					html += "broken";
				break;
				case 'attente_christophe':
				case 'attente_frederique':
				case 'pret':
				case 'en_cours':
				case 'en_attente_etls':
				case 'etude':
				case 'fnp':
				case 'fae':
				case 'stock':
				case 'recu':
				case 'demande':
				case 'remplace':
				case 'mis_loyer':
				case 'prolongation':
				case 'actif':
				case 'indisponible':
				case 'inactif':
				case 'oui' :
				case 'non' :
					html += etat;
				break;
				default:
					defaut = true;
					html = '<span style="font-weight:bold">'+ATF.usr.trans(etat,table)+'</span>';
				break;
			}
			if (!defaut) {
				html += '" title="'+ATF.usr.trans(etat,table)+'"  alt="'+ATF.usr.trans(etat,table)+'" src="'+ATF.blank_png+'" />';
			}
			return '<div class="center" id="'+idDivE+'">'+html+'</div>';
		}
	}
};
/*
	Renderer pour la durée
	@author Quentin JANON <qjanon@absystech.fr>
*/
ATF.renderer.duree=function(table,field) {
	return function(val, meta, record, rowIndex, colIndex, store) {
		var idDivDuree = Ext.id();
		var id = record.data[table+'__dot__id_'+table];
		if (record.data[table+'__dot__id_'+field]) {
			var duree = String(record.data[table+'__dot__id_'+field]);
		} else {
			var duree = String(record.data[field]);
		}

		if (duree) {
			if (duree==0.0) {
				html = ATF.usr.trans("aucun");
			} else if (duree==0.5) {
				html = "1/2 "+ATF.usr.trans("journee");
			} else if (duree==1.0) {
				html = "1 "+ATF.usr.trans("jour");
			} else if (duree.indexOf(".")) {
				a = duree.split(".");
				html = a[0];
				if (a[1] && a[1]==5) {
					html += ATF.usr.trans("journee_et_demi");
				} else {
					html += " "+ATF.usr.trans("jours");
				}
			} else {
				html = a+" "+ATF.usr.trans("jours");
			}
		} else {
			html = '<img class="smallIcon noAction" src="'+ATF.blank_png+'" />';
		}
		return '<div id="'+idDivDuree+'">'+html+'</div>';
	}
};
/**
* @author Quentin JANON
* <p> Renderer Money </p>
*/
ATF.renderer.money=function(table,field) {
	return function(val, meta, record, rowIndex, colIndex, store) {
		if(!isNaN(val)){
			var idDiv = Ext.id();
			var id = record.data[table+'__dot__id_'+table];
			if (record.data[table+'__dot__'+field]) {
				var v = record.data[table+'__dot__'+field];
			} else if (record.data[field]) {
				var v = record.data[field];
			}
			// Init a 0 si pas de valeur pour éviter le Nan
			if (!v) v=0;
			//Remplacement virgule par un point pour éviter l'affichage de Nan
			else if (strpos(v,",")) v=v.replace(",",".");

			v = (Math.round((v-0)*100))/100;
			v = (v == Math.floor(v)) ? v + ".00" : ((v*10 == Math.floor(v*10)) ? v + "0" : v);
			v = String(v);

			var ps = v.split('.'),	whole = ps[0], sub = ps[1] ? '.'+ ps[1] : '.00', r = /(\d+)(\d{3})/;
			while (r.test(whole)) {
				whole = whole.replace(r, '$1' + ',' + '$2');
			}
			v = whole + sub;

			if(v.charAt(0) == '-'){
				v = '-' + v.substr(1);
			}

			return '<div class="right" id="'+idDiv+'">'+v+' &euro;</div>';
		}
	}
};

/*
	Renderer pour les pourcentage
	@author Quentin JANON <qjanon@absystech.fr>
*/
ATF.renderer.percent=function(table,field) {
	return function(val, meta, record, rowIndex, colIndex, store) {
		var idDivPercent = Ext.id();
		var id = record.data[table+'__dot__id_'+table];
		if (record.data[table+'__dot__'+field]) {
			var value = record.data[table+'__dot__'+field];
		}else if (record.data[field]) {
			var value = record.data[field];
		}
		if (value) {
			html = Math.round(value*100)+"%";
		} else {
			html = '0%';
		}
		return '<div id="'+idDivPercent+'">'+html+'</div>';
	}
};


// Renderer pour les enum
// @author Yann GAUTHERON <ygautheron@absystech.fr>
ATF.renderer.combo=function(table,field) {
	return function(val, meta, record, rowIndex, colIndex, store) {
		var val2 = "";
		if (val) val2 = ATF.usr.trans(field,table,val,true);
		if (val && (!val2 || val==val2)) {

			return ATF.usr.trans(val,table);
		}
		return val2;
	}
};

// Renderer pour les combobox
// @author Quentin JANON <qjanon@absystech.fr>
ATF.renderer.comboBoxRenderer = function(combo) {
	return function(value) {
		var idx = combo.store.find(combo.valueField, value);
		var rec = combo.store.getAt(idx);
		if (rec) {
			return rec.get(combo.displayField);
		} else {
			return value;
		}
	};
};

/* rowEditor : moteurs de rendus sur les champs qui sont paramétrés pour être édités ! */
ATF.rowEditor=function(editor, table, field){
//ATF.log(editor+" "+field+" "+id);
	if (ATF.rowEditor[editor]) {
		return ATF.rowEditor[editor](table,field);
	}
	return ATF.rowEditor.std(table,field);
};
ATF.rowEditor.std=function(table,field) {
	return new Ext.form.TextField({
		value: 0,
		id:table+'_'+field+'_'+Ext.id(),
		fieldLabel: '',
		listeners:{
			change:function(f) {
				ATF.ajax(table+',update.ajax','id_'+table+'='+this.gridEditor.record.data[table+'__dot__id_'+table]+'&'+field+'='+this.getValue());
			}
			,specialkey: function(tf, e){
				// e.HOME, e.END, e.PAGE_UP, e.PAGE_DOWN,
				// e.TAB, e.ESC, arrow keys: e.LEFT, e.RIGHT, e.UP, e.DOWN
				if (e.getKey() == e.ENTER) {
					ATF.ajax(table+',update.ajax','id_'+table+'='+this.gridEditor.record.data[table+'__dot__id_'+table]+'&'+field+'='+this.getValue());
				}
			}
		}
	});
}

ATF.rowEditor.ouinon=function(table,field) {
	var d = [];
	d.push(["oui", "Oui"]);
	d.push(["non", "Non"]);
	return new Ext.form.ComboBox({
		typeAhead: true,
		triggerAction: 'all',
		lazyRender:true,
		mode: 'local',
		store: new Ext.data.ArrayStore({
			id: 0,
			fields: [
				'myId',
				'displayText'
			],
			data: d
		}),
		valueField: 'myId',
		displayField: 'displayText',
		listeners:{
			change:function(f) {
				var grid = this.gridEditor;
				ATF.ajax(table+',EtatUpdate.ajax','id_'+table+'='+grid.record.data[table+'__dot__id_'+table]+'&field='+field+'&'+field+'='+this.getValue(),{
				 onComplete:function(){ grid.record.store.reload();	 }} );
			}
		}
	});
}


ATF.rowEditor.prioriteUpdate=function(table,field) {
	var d = [];
	for (var i=0;i<21;i++) d.push([i,i]);
	return new Ext.form.ComboBox({
		typeAhead: true,
		triggerAction: 'all',
		lazyRender:true,
		mode: 'local',
		store: new Ext.data.ArrayStore({
			id: 0,
			fields: [
				'myId',
				'displayText'
			],
			data: d
		}),
		valueField: 'myId',
		displayField: 'displayText',
		listeners:{
			change:function(f) {
				ATF.ajax('hotline,setPriorite.ajax','id_hotline='+this.gridEditor.record.data[table+'__dot__id_'+table]+'&priorite='+this.getValue());
			}
		}
	});
}
ATF.rowEditor.forecastUpdate=function(table,field) {
	var d = [];
	for (var i=1;i<6;i++) d.push([20*i,20*i]);
	return new Ext.form.ComboBox({
		typeAhead: true,
		triggerAction: 'all',
		lazyRender:true,
		mode: 'local',
		store: new Ext.data.ArrayStore({
			id: 0,
			fields: [
				'myId',
				'displayText'
			],
			data: d
		}),
		valueField: 'myId',
		displayField: 'displayText',
		listeners:{
			change:function(f) {
				ATF.ajax('affaire,setForecast.ajax','id_affaire='+this.gridEditor.record.data[table+'__dot__id_'+table]+'&forecast='+this.getValue());
			}
		}
	});
}

ATF.rowEditor.actifUpdate=function(table,field) {
	var d = [];
	d.push(["actif", "Actif"]);
	d.push(["inactif", "Inactif"]);
	return new Ext.form.ComboBox({
		typeAhead: true,
		triggerAction: 'all',
		lazyRender:true,
		mode: 'local',
		store: new Ext.data.ArrayStore({
			id: 0,
			fields: [
				'myId',
				'displayText'
			],
			data: d
		}),
		valueField: 'myId',
		displayField: 'displayText',
		listeners:{
			change:function(f) {
				var grid = this.gridEditor;
				ATF.ajax(table+',actifUpdate.ajax','id_'+table+'='+grid.record.data[table+'__dot__id_'+table]+'&'+field+'='+this.getValue(),{
				 onComplete:function(){ grid.record.store.reload();	 }} );
			}
		}
	});
}

ATF.rowEditor.interesseUpdate=function(table,field) {
	var d = [];
	d.push(["oui", "Oui"]);
	d.push(["non", "Non"]);
	d.push(["attente", "En attente"]);
	return new Ext.form.ComboBox({
		typeAhead: true,
		triggerAction: 'all',
		lazyRender:true,
		mode: 'local',
		store: new Ext.data.ArrayStore({
			id: 0,
			fields: [
				'myId',
				'displayText'
			],
			data: d
		}),
		valueField: 'myId',
		displayField: 'displayText',
		listeners:{
			change:function(f) {
				var grid = this.gridEditor;
				ATF.ajax(table+',interesseUpdate.ajax','id_'+table+'='+grid.record.data[table+'__dot__id_'+table]+'&'+field+'='+this.getValue(),{
				 onComplete:function(){ grid.record.store.reload();	 }} );
			}
		}
	});
}

ATF.rowEditor.setInfos=function(table,field) {
	return new Ext.form.TextField({
		value: 0,
		id:table+'_'+field+'_'+Ext.id(),
		fieldLabel: '',
		listeners:{
			change:function(f) {
				ATF.ajax(table+',setInfos.ajax','id_'+table+'='+this.gridEditor.record.data[table+'__dot__id_'+table]+'&'+field+'='+this.getValue()+'&field='+field);
			}
			,specialkey: function(tf, e){
				if (e.getKey() == e.ENTER) {
					ATF.ajax(table+',setInfos.ajax','id_'+table+'='+this.gridEditor.record.data[table+'__dot__id_'+table]+'&'+field+'='+this.getValue()+'&field='+field);
				}
			}
		}
	});
}

/* Language */
ATF.usr = {
	lang:'fr'
	,trans:function (word,prefix,suffix,strict,suffixInPrefix) {
		/* Champ prefixé par la table (ex: societe.id_famille)
		if (word.indexOf(".")>-1 && !preg_match("/^[0-9]*\.[0-9]*$/",word)) {
			explode_word = word.split(".");
			if (explode_word.length==2) {
				prefix = explode_word.shift();
				word = explode_word.shift();
			}
		} */

		if (typeof word == 'Array') {
			var w;
			for (var key in  word)  {
				var i = word[key];
				w[key] = ATF.usr.trans(i,prefix,suffix,strict,id_language);
			}

			return w;
		}

		if (prefix && suffix && ATF.loc[prefix+"_"+word+"_"+suffix]) {
			return ATF.loc[prefix+"_"+word+"_"+suffix];
		}
		if (prefix && ATF.loc[prefix+"_"+word] && (!suffix || !strict)) {
			return ATF.loc[prefix+"_"+word];
		}
		if(ATF.loc[word] && (!suffix && !prefix || !strict)) {
			return ATF.loc[word];
		}

		/* Si c'est strict on a rien trouvé, on ne retourne RIEN */
		if (strict) {
			// Avec un suffixe dans le préfixe, on cible les valeurs des ENUM ou SET qui ont deux préfixe : la table + le nom du champ
			if (suffixInPrefix) {
				return ATF.usr.trans(word,prefix+"_"+suffixInPrefix,suffix,strict);
			} else {
				return;
			}
		}

		return word;
	}
}
ATF.loc={

}

// @author Yann GAUTHERON <ygautheron@absystech.fr>
// Ajout de f.el sinon crash qd on valide un formulaire contenu dans un tabpanel
if (typeof(Ext) != "undefined") {
	Ext.onReady(function(){
		if (Ext.form.Action) {
			Ext.extend(Ext.form.Action.Submit, Ext.form.Action, {
				type : 'submit',
				run: function(){
					var o = this.options,
						method = this.getMethod(),
						isGet = method == 'GET';
					if(o.clientValidation === false || this.form.isValid()){
						if (o.submitEmptyText === false) {
							var fields = this.form.items,
								emptyFields = [],
								setupEmptyFields = function(f){
			/*Ajout f.el */		if (f.el && f.el.getValue() == f.emptyText) {
										emptyFields.push(f);
										f.el.dom.value = "";
									}
									if(f.isComposite && f.rendered){
										f.items.each(setupEmptyFields);
									}
								};

							fields.each(setupEmptyFields);
						}
						Ext.Ajax.request(Ext.apply(this.createCallback(o), {
							form:this.form.el.dom,
							url:this.getUrl(isGet),
							method: method,
							headers: o.headers,
							params:!isGet ? this.getParams() : null,
							isUpload: this.form.fileUpload
						}));
						if (o.submitEmptyText === false) {
							Ext.each(emptyFields, function(f) {
								if (f.applyEmptyText) {
									f.applyEmptyText();
								}
							});
						}
					}else if (o.clientValidation !== false){
						this.failureType = Ext.form.Action.CLIENT_INVALID;
						this.form.afterAction(this, false);
					}
				},
				success : function(response){
					var result = this.processResponse(response);
					if(result === true || result.success){
						this.form.afterAction(this, true);
						return;
					}
					if(result.errors){
						this.form.markInvalid(result.errors);
					}
					this.failureType = Ext.form.Action.SERVER_INVALID;
					this.form.afterAction(this, false);
				},
				handleResponse : function(response){
					if(this.form.errorReader){
						var rs = this.form.errorReader.read(response);
						var errors = [];
						if(rs.records){
							for(var i = 0, len = rs.records.length; i < len; i++) {
								var r = rs.records[i];
								errors[i] = r.data;
							}
						}
						if(errors.length < 1){
							errors = null;
						}
						return {
							success : rs.success,
							errors : errors
						};
					}
					return Ext.decode(response.responseText);
				}
			});
		}
	});
}
// @author Yann GAUTHERON <ygautheron@absystech.fr>
// Patch IE9 pour menu
if ((typeof Range !== "undefined") && !Range.prototype.createContextualFragment) {
    Range.prototype.createContextualFragment = function(html)
    {
        var frag = document.createDocumentFragment(),
        div = document.createElement("div");
        frag.appendChild(div);
        div.outerHTML = html;
        return frag;
    };
}

/* Gestion des crochets [] dans les noms pour les radios */
if (typeof(Ext) != "undefined") {
	Ext.onReady(function(){
		if (Ext.DomQuery.matchers) {
			Ext.DomQuery.matchers[2] = {
				re: /^(?:([\[\{])(?:@)?([\w-]+)\s?(?:(=|.=)\s?(["']?)(.*?)\4)?[\]\}])/,
				select: 'n = byAttribute(n, "{2}", "{5}", "{3}", "{1}");'
			};
			Ext.override(Ext.form.Radio, {
				getGroupValue : function(){
					var p = this.el.up('form') || Ext.getBody();
					var c = p.child('input[name="'+this.el.dom.name+'"]:checked', true);
					return c ? c.value : null;
				},
				onClick : function(){
					if(this.el.dom.checked != this.checked){
						var els = this.getCheckEl().select('input[name="' + this.el.dom.name + '"]');
						els.each(function(el){
							if(el.dom.id == this.id){
								this.setValue(true);
							}else{
								Ext.getCmp(el.dom.id).setValue(false);
							}
						}, this);
					}
				},
				setValue : function(v){
					if (typeof v == 'boolean') {
						Ext.form.Radio.superclass.setValue.call(this, v);
					} else {
						var r = this.getCheckEl().child('input[name="' + this.el.dom.name + '"][value="' + v + '"]', true);
						if(r){
							Ext.getCmp(r.id).setValue(true);
						}
					}
					return this;
				}
			});
		}
	});
}
/* Méthodes utiles à tous les tabpanel/gridpanel */
ATF.gridGetCols = function(cols,chaine){
	if(chaine){
		var cols2="";
		for(ind in cols){
			if(cols[ind]['dataIndex']!="updateBtn" && cols[ind]['hideable']==true && !cols[ind]['hidden']){
				if(cols2){
					cols2+=",";
				}
				cols2+=cols[ind]['dataIndex'];
			}
		}
	}else{
		var cols2=Array();
		for(ind in cols){
			if(cols[ind]['dataIndex']!="updateBtn" && cols[ind]['hideable']==true && !cols[ind]['hidden']){
				cols2[ind]=cols[ind]['dataIndex'];
			}
		}
	}
	return cols2;
}

ATF.gridRaffraichissement = function(nom_grid,id_grid,reponse,nom_pager,searchbox,nom_table){
	//  pour cacher la zone de chargement
	ATF.loadMask.hide();

	//  pour exécuter le js du template retourné
	eval(reponse);

	//  pour reconfigurer les colonnes
	nom_grid.colModel.setConfig(cols[nom_pager]);

	//  pour mettre en surbrillance la recherche éventuelle et raffraichir les éléments de la toolbar
	store[nom_pager].searchBox = searchbox;
	store[nom_pager].searchBox.store= store[nom_pager];
	nom_grid.reconfigure(store[nom_pager],nom_grid.colModel);

	// pour recharger le tableau
	Ext.getCmp(id_grid).getStore().load({ params:{ start:0, limit:30 }});

	// pour appliquer les événements sur les nouvelles colonnes
	ATF.modifColsEvent(nom_table,nom_grid,cols[nom_pager]);
}

ATF.gridCheckCols = function(cols,module,nom){
	// au cas où un champs serait deja préfixé
	var reg2=new RegExp("("+module+"\.)","g");
	var nom2=nom.replace(reg2,"");
	var nom_mod=module+"__dot__"+nom2;

	for(ind in cols){
		if(cols[ind]['dataIndex']!="updateBtn" && cols[ind]['hideable']==true && !cols[ind]['hidden']){
			if(cols[ind]['dataIndex']==nom || cols[ind]['dataIndex']==nom_mod){
				return true;
			}
		}
	}
	return false;
}

ATF.modifColsEvent = function(ident,grid,cols){
	grid.colModel.on('columnmoved', function(a, b, c){
		ATF.ajax("vue,update.ajax","vue="+ident+"&ordre_colonne="+ATF.gridGetCols(cols));
	});
	grid.on('columnresize', function(num_col, taille, c){
		var cols2="";
		for(ind in cols){
			if(cols[ind]['dataIndex']!="updateBtn" && cols[ind]['hideable']==true && !cols[ind]['hidden']){
				if(cols2){
					cols2+=",";
				}
				cols2+=cols[ind]['dataIndex']+"-"+cols[ind].width;
			}
		}
		ATF.ajax("vue,update.ajax","vue="+ident+"&ordre_colonne="+ATF.gridGetCols(cols)+"&tailles="+cols2);
	});
	grid.on('sortchange',function(obj,tri){
		ATF.ajax("vue,update.ajax","vue="+ident+"&ordre_colonne="+ATF.gridGetCols(cols)+"&champs="+tri['field']+"&ordre="+tri['direction'],{ onComplete:function(){ grid.saveHeight='undefined'; }});
	});
}

// met les lignes du menu en gras si on sélectionne un élément du menu enfant (recursivement)
ATF.toolbarAddBold = function (el){
	var parent=el.parentMenu.ownerCt;
	if(parent && parent.text!="Vue"){
		parent.addClass('bold');
		ATF.toolbarAddBold(parent);
	}
}

// si aucun enfant de coché, je peux retirer le gras du parent
ATF.toolbarRemoveBold = function (el){
	var parent=el.parentMenu.ownerCt;
	var enfants=parent.menu.items.items;
	for(cle_enfant in enfants){
		if(enfants[cle_enfant].checked==true){
			return;
		}
	}
	parent.removeClass('bold');
	ATF.toolbarRemoveGrandParentBold(parent);
}

// si aucun panel en gras, je peux retirer le gras du grand parent
ATF.toolbarRemoveGrandParentBold = function (parent){
	var gdparent=parent.parentMenu.ownerCt;
	var freres=gdparent.menu.items.items;
	for(cle_freres in freres){
		if(freres[cle_freres].menu){
			var enfants=freres[cle_freres].menu.items.items;
			for(cle_enfant in enfants){
				if(enfants[cle_enfant].checked==true){
					return;
				}
			}
		}
	}
	gdparent.removeClass('bold');
}

//chargement des lignes d aggregat
ATF.chargerAggregat = function(pager,ident,table){
	/*
	1/ récupérer les colonnes actives
	2/ récupérer les eventuels aggregats de ces colonnes
	3/ récupérer les valeurs de chaque aggregats
	4/ afficher les valeurs
	*/

	var grid = Ext.getCmp(ident);
	var cols=grid.getColumnModel().config;

	var theType = grid.store.recordType;

	var pos=30;
	if(grid.store.totalLength<30){
		pos=grid.store.totalLength;
	}

	/* seulement si il y a plus d une donnee */
	if(pos>1 && grid.store.data.length<31){
		var ajout_sum = new theType();
		var ajout_min = new theType();
		var ajout_max = new theType();
		var ajout_avg = new theType();

		grid.store.insert(0,ajout_min);
		grid.store.insert(1,ajout_max);
		grid.store.insert(2,ajout_avg);
		grid.store.insert(3,ajout_sum);

		var record_min = grid.store.getAt(0);
		var record_max = grid.store.getAt(1);
		var record_avg = grid.store.getAt(2);
		var record_sum = grid.store.getAt(3);

		for(ind in cols){
			if(!cols[ind]["hidden"] && cols[ind]["dataIndex"]){
				ATF.ajax(table+",recupAggregate.ajax","champs="+cols[ind]["dataIndex"],{ onComplete:function(obj){
					if(obj.result){
						if(obj.result['sum']){
							record_sum.set(obj.result['sum']['champs'], obj.result['sum']['valeur']);
							record_sum.set("updateBtn",ATF.usr.trans("sum","aggregat"));
						}
						if(obj.result['min']){
							record_min.set(obj.result['min']['champs'], obj.result['min']['valeur']);
							record_min.set("updateBtn",ATF.usr.trans("min","aggregat"));
						}
						if(obj.result['max']){
							record_max.set(obj.result['max']['champs'], obj.result['max']['valeur']);
							record_max.set("updateBtn",ATF.usr.trans("max","aggregat"));
						}
						if(obj.result['avg']){
							record_avg.set(obj.result['avg']['champs'], obj.result['avg']['valeur']);
							record_avg.set("updateBtn",ATF.usr.trans("avg","aggregat"));
						}
					}
					}});
			}
		}
	}
}
//effacer les lignes d'aggregats
ATF.cacherAggregat = function(pager,ident,table){
	var grid = Ext.getCmp(ident);

	for(var i=0;i<=3;i++){
		grid.store.removeAt(0);
	}
}
//force l affichage des aggregagts si present dans le custom
ATF.actionAggregat = function(pager,ident,table,id_filtre){
	ATF.ajax(table+',saveAggregat.ajax','filtre='+id_filtre,{
		onComplete:function(obj){
			if(obj.result==true){
				ATF.chargerAggregat(pager,ident,table);
			}else{
				ATF.cacherAggregat(pager,ident,table);
			}
		}
	});
}

ATF.is_array = function(input){
	return typeof(input)=='object'&&(input instanceof Array);
}

ATF.is_object = function(mixed_var) {
    if (Object.prototype.toString.call(mixed_var) === '[object Array]') {
        return false;
    }
    return mixed_var !== null && typeof mixed_var == 'object';
}

Modalbox = {
	show: function modalboxShow(content,params){
		params = params || {};
		if (!$( "#modalBox" ).length) {
			var $newdiv1 = $( "<div id='modalBox'></div>" );
			$( "body" ).prepend( $newdiv1 );
		}
		if ($( "#modalBox" ).length>0) {
			var d = $( "#modalBox" );
			d.html(content);
			if (content.slice(-7)==".dialog") {
				d.load(content, params);
			}
			d.dialog({
		      autoOpen: true,
		      title:params.title,
		      show: {
		        effect: "blind",
		        duration: 1000
		      },
		      hide: {
		        effect: "explode",
		        duration: 1000
		      }
		    });
		}
	},
	hide: function modalboxHide(){
		if ($( "#modalBox" )) {
			$( "#modalBox" ).remove();
		}
	}
};

