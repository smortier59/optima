/*  Aewd Stip - 2009-12-27
Copyright (c) 2000-2009 Yann-GaÃ«l GAUTHERON (http://www.aewd.net/)

Smartip is a very customisable tooltip built in CSS/Javascript based on the free prototype library ( http://ajax.googleapis.com/ajax/libs/prototype/1.6.0.3/prototype.js ).
You can use it easily with the PHP template engine Smarty ( http://www.smarty.net/ ), there is the plugin for Smarty 3 in the package.

You can find all informations about this project here :
http://www.aewd.net/projects/smartip/ 

2010-07-10 1.2.1 - Ajout de mouseenter et mouseleave pour eviter le bubbling
*/

var Stip = {
	v: '1.2.2' // Stip version
	
	// Feel free to setup those parameters to your convenience
	,config:{
		zIndex: 42000
	}
};

Object.extend(Stip, {
	debug:false
	,required: [
		{lib:"Prototype", v:"1.6"} // required prototype version
	]
	,log:function (t) {
		if (!Stip.debug) return;
		if (Prototype.Browser.IE) {
			return;
		}
		if ((typeof console != "undefined") && (typeof console.log != "undefined")) {
			console.log(t);
		}
	}
	,defaultOptions:{}
	,init:function () {
		this.checkRequired();
		if (!(this.canvasSupport = !!document.createElement("canvas").getContext)) {
            if (document.documentMode >= 8 && !document.namespaces.ie_vml) {
                document.namespaces.add("ie_vml", "urn:schemas-microsoft-com:vml", "#default#VML")
            } else {
                document.observe("dom:loaded", function () {
                    document.createStyleSheet().addRule("ie_vml\\:*", "behavior: url(#default#VML);")
                })
            }
		}
		//Element.observe(window, "unload", this.unload);
	}
	
	// DÃ©finir les options par dÃ©faut si aucune options passÃ©e lors d'une crÃ©ation de smartip
	,setDefaultOptions:function (o) {		
		this.defaultOptions = o;
	}
	
	// Retourne les options par dÃ©faut
	,getDefaultOptions:function () {		
		return this.defaultOptions;
	}
	
	// Retourne VRAI si toutes les librairies nÃ©cessaires sont prÃ©sentes
	,checkRequired:function () {		
		for (var i=0;i<this.required.length;i++) {
			if ((typeof window[this.required[i].lib] == "undefined") || !window[this.required[i].lib].Version || this.required[i].v > window[this.required[i].lib].Version.substr(0,this.required[i].v.length)) {
				throw ("[Stip] Please make sure that all required libraries are installed, thank you ("+this.required[i].lib+" must be greater or equal than version "+this.required[i].v+").");
				return false;
			}
		}
		return true;
	}
	
	,camelCase:function (s){
	  for(var exp=/-([a-z])/; exp.test(s); s=s.replace(exp,RegExp.$1.toUpperCase()));
	  return s;
	}
	,getStyle:function (e,a){
		// convert s to camel case

	  var v=null;
	  if(document.defaultView && document.defaultView.getComputedStyle){
		var cs=document.defaultView.getComputedStyle(e,null);
		if(cs && cs.getPropertyValue) v=cs.getPropertyValue(a);
	  }
	  if(!v && e.currentStyle) v=e.currentStyle[this.camelCase(a)];
	  return v;
	}
	
	// Toutes les tips
	,overlayOwner:null // Lorsqu'un overlay est crÃ©Ã©, on mÃ©morise la derniÃ¨re tip qui l'a appelÃ©
	,visible:[]
	,tips:[]
	,add: function (btn, content, options) {
		if (Object.isString(btn)) {
			elmt = $(btn);
		} else if (Object.isElement(btn)) {
			elmt = btn;
		} else if (Object.isElement(btn.srcElement)) {
			elmt = btn.srcElement;
		}
		
		if (elmt) {
			elmt.onmouseover = ''; // Eviter les conflits
			
			// RÃ©fÃ©rence unique de l'infobulle sur cet Ã©lÃ©ment
			var id = 'tip';
			if (options && options.id) {
				id = options.id;
			}
			
			// Array des tips de l'Ã©lements
			if (!elmt.stip) {
				elmt.stip = new Object();
			}
			
			if (!elmt.stip[id]) { // Already never been created ?
				this.tips.push(elmt.stip[id] = new this.tip(elmt, content, options));
			}
			
			return elmt.stip[id];
		} else {
			throw ("Stip: DOM Element '"+btn+"' not found !");
			return false;
		}
	}
	,addNow: function (e, content, options) {
		if (e.currentTarget && $(e.currentTarget)) {
			return this.add(e.currentTarget, content, options).display(e);
		} else {
			return this.add(e, content, options).display();
		}
	}
	,del: function (stip) {
		if (stip && stip.destroy) {
			stip.destroy();
			this.tips = this.tips.without(stip);
			delete stip;
		}
	}
	,delAll: function (group) {
		this.hideAll(null,group);
		this.tips.each(function (stip) {
			if (!group || stip.options.group==group) {
				this.del(stip);
			}
		}.bind(this))
	}
	,addVisible: function (st) {
		st.tipVisible = true;
		this.visible.push(st);
	}
	,delVisible: function (st) {
		if (st.tipVisible) {
			delete st.tipVisible;
		}
		this.visible = this.visible.without(st);
	}
	,hideAll: function (force,group) {
		this.visible.invoke("hideAllUndisplay",force,group);
	}
	
	// Tableaux de conversions
	,__mirror: {
		left: "right",
		right: "left",
		top: "bottom",
		bottom: "top",
		middle: "middle",
		vertical: "horizontal",
		horizontal: "vertical"
	}	
	// Retourne la position mirroir de la flÃ¨che
	,arrowMirror: function (s) {
		return this.__mirror[s];
	}
	,attach: function (st, target) {
		return ;
	}
	
	,setHighest:function(o) {
		if (o.style.zIndex<this.config.zIndex) {
			o.setStyle({
				zIndex: this.config.zIndex++
			});
		}
	}
	
	,setLowest:function(o) {
		if (o.style.zIndex>-100) {
			o.setStyle({
				zIndex: -100
			});
		}
	}
	
	,showOverlay: function(endCallback,options) {
		if (!this.overlay) {
			this.overlay = new Element("div", { id: "smartipOverlay", style: "opacity: 0" });
			$(document.body).insert(this.overlay);
		}
		this.options = Object.extend({
			overlayOpacity: .65
			,overlayDuration: .3
		},options);
		new Effect.Fade(this.overlay, {
			from: this.overlay.getStyle('opacity'), 
			to: this.options.overlayOpacity, 
			duration: this.options.overlayDuration, 
			afterFinish: function() {				
				endCallback();
			}.bind(this)
		});
	}
	
	,hideOverlay: function() {
		new Effect.Fade(this.overlay, {
			from: this.options.overlayOpacity, 
			to: 0, 
			duration: this.options.overlayDuration, 
			afterFinish: function() {
Stip.log("Stip.hideOverlay() - overlayRemove");
				this.overlay.remove();
				delete this.overlay;
			}.bind(this)
		});		
	}

// Objet tip
	,tip:Class.create({
		initialize: function (source, content, options) {
Stip.log("Stip.tip.initialize()");
			this.source = $(source);
			Stip.del(this.source);
			if (Object.isString(content)) {
				this.content = new Element('div').update(content);
			} else if (Object.isElement(content)) {
				this.content = content;
			} else if (content && !options) {
				// Ajax ?
				options = content;
				this.content = '<div class="smartipLoader"></div>';
			} else {
				throw ("Stip: Content not defined !");
				return false;
			}
			
			this.options = {};
			
			// Options utilisateur par dÃ©faut
			if (options && !options.noDefault) {
				this.options = Object.extend(this.options,Stip.defaultOptions);
			}
			
			this.options = Object.extend(this.options,options);
			
			// Options par dÃ©faut sinon
			this.options = Object.extend({
				borderSize: 1
				,id: 'tip' // Nom de l'infobulle si plusieurs sur le mÃªme Ã©lÃ©ment
				,radius: 0
				,arrow: false // Longueur de la flÃ¨che, true pour la valeur par dÃ©faut
				,arrowBase: 10 // Taille du pied de la flÃ¨che
				,title: false
				,titleHeight: false
				,overflow: false
				,width: false
				,maxWidth: false // La tip prendra une largeur automatique, mais ne pourra pas dÃ©passer cette largeur
				,target: this.source
				,fadeDuration: false
				,offset: {x:16, y:16}
				,position: 'rightBottom'
				,attach: false
				,closeButton: false
				,css: false
				,hideAll: true // Effacer toutes les autres infobulles
				,hideAllImmune: false // ImmunisÃ© contre l'effacement massif
				,overlay: false // true = Overlay grisant le fond, et forÃ§ant le focus, s'effacera si on clique dessus | 'static' = Overlay grisant le fond, et forÃ§ant le focus, mais ne s'enlÃ¨vera si on clique dessus
				,overlayImmune: false
				,delay: 0 // DÃ©lai d'appartition en seconde
				,url: false // Appel ajax de cette URL et remplacement du contenu de l'infobulle par le rÃ©sultat ajax
				,urlPost: '' // Arguments Ã  poster
				,advancedText: false // Si on reste longtemps sur l'infobulle, on affiche la totalitÃ© de ce texte
				,urlAdvancedText: false // Si on reste longtemps sur l'infobulle, on affiche la totalitÃ© du texte Ã  cette URL
				,listeners: true // Ecoute du survol, ou du click pour l'affichage. Si FALSE, alors seulement dÃ©clenchÃ© "manuellement" par un .display()
				,callback:{
					offset:function () { return {top:0,left:0}; } // Exemple : function(){return $('main').cumulativeScrollOffset();}
					,beforeDisplay: false
					,afterHide: false
				}
			},this.options);
			
			// Accroche des callback
			for (var i in this.options.callback) {
				if (this.options.callback[i]) {	
					this.options.callback[i] = this.options.callback[i].bind(this);
				}
			}
			
			this.target = $(this.options.target);
			
			this.options.offset = {left:this.options.offset.x, top:this.options.offset.y};
			
			if (this.options.overlay) {
				this.options.attach = true;	
			
				if (!this.options.overlayImmune) {
					this.options.overlayImmune = [this.target];
				}
			}
			
			if (this.options.attach) {
				// Si attachÃ©, le dÃ©passement de viewport est autorisÃ© !
				this.options.overflow = true;
				
				// AttachÃ©, l'offset est de 0
//				this.options.offset = {x:0, y:0};
			}
			
			// Taille de la flÃ¨che par dÃ©faut
			if (this.options.arrow) {
				if (this.options.arrow===true || this.options.arrow<=0) {
					this.options.arrow = 10;
				}
			}
			
			// 'auto' si non renseignÃ©
			$w('height width titleHeight').each((function (p) {
				if (this.options[p] && this.options[p]!="auto") {
					this.options[p] = this.options[p]+"px";	
				} else {
					this.options[p] = "auto";
				}
			}).bind(this));
			
			// Position en lower
			if (this.options.position) {
				this.options.position = this.options.position.toLowerCase();
			}
			
			if (this.options.listeners) {
//Stip.log('bind');				
				this.bindListerners();
			}
		}
		
		// Detruire les divs
		,destroy: function() {
Stip.log("Stip.destroy()");
			if (this.div) {
				this.target.stopObserving();
				//Attention ! Impossible de fare un remove sur le div lui-même s'il n'appartient pas au document
				if(this.div.parentNode){
						this.div.remove();
				}
			}
		}
		
		,bindListerners: function () {
//Stip.log("Stip.tip.bindListerners()");
//Stip.log(this.source);
            this.target.observe("mouseenter", this.mouseover.bind(this));
			this.target.observe("mouseleave", this.mouseout.bind(this));
			this.target.observe("click", this.mouseclick.bind(this));
			if (!this.options.attach) {
            	this.target.observe("mousemove", this.positionne.bind(this));
			}
		}
		
		// Positionne l'infobulle en fonction de la config et position de la souris
		,positionne: function (e) {
Stip.log("Stip.tip.positionne()");
			if (this.div) {
				if (this.options.attach) {
					
					this.options.offset = {left:this.target.getWidth(), top:this.target.getHeight()};
//alert("offset = "+this.options.offset.left+" "+this.options.offset.top);					
					// Conversion de l'offset en fonction de la position demandÃ©e
					switch (this.options.position) {
						case "rightbottom": 
						case "bottomright":
							break;
							
						case "right":
						case "rightmiddle":
							this.options.offset.top /= 2;
							this.options.offset.top -= this.div.getHeight()/2;
							break;
							
						case "lefttop":
						case "topleft":
							this.options.offset.left = 0;
							this.options.offset.left -= this.div.getWidth();
						case "righttop":
						case "topright":
							this.options.offset.top = 0;
							this.options.offset.top -= this.div.getHeight();
							break;
							
						case "left":
						case "leftmiddle":
							this.options.offset.top /= 2;
							this.options.offset.top -= this.div.getHeight()/2;
						case "leftbottom":
						case "bottomleft":
							this.options.offset.left = 0;
							this.options.offset.left -= this.div.getWidth();
							break;
							
						case "top":
						case "topmiddle":
							this.options.offset.top = 0;
							this.options.offset.top -= this.div.getHeight();
						case "bottom":
						case "bottommiddle":
							this.options.offset.left /= 2;
							this.options.offset.left -= this.div.getWidth()/2;
							break;
					}

//alert("divDim = "+this.div.getWidth()+" "+this.div.getHeight());
//alert("offset2 = "+this.options.offset.left+" "+this.options.offset.top);
//alert("target = "+this.target.viewportOffset().left+" "+this.target.viewportOffset().top);
					var o = this.options.callback.offset();
//Stip.log(o);
					var coord = {
						x: this.options.offset.left + this.target.cumulativeOffset().left - o.left
						,y: this.options.offset.top + this.target.cumulativeOffset().top - o.top
					};
//alert("coord = "+coord.x+" "+coord.y);
				} else {
//Stip.log(e);
					var coord = {
						x:  this.options.offset.left
						,y: this.options.offset.top
					};
					if (e) {
						coord.x += Event.pointerX(e);
						coord.y += Event.pointerY(e);
					}
				}
//Stip.log(coord);
				this.div.setStyle({
					top: coord.y+"px"
					,left: coord.x+"px"
				});
			}

			// GÃ©rer les dÃ©passement de viewport
			if (!this.options.overflow && this.div) {
				var pos = this.getPositionWithinViewport(this.div.cumulativeOffset());
//Stip.log(pos);
//				pos.position.left += pos.mirrorArrow.vertical ? 2 * Prototip.toggleInt(g.x - this.options.offset.left) : 0;
//				pos.position.top += pos.mirrorArrow.vertical ? 2 * Prototip.toggleInt(g.y - this.options.offset.top) : 0;
//Stip.log(pos.newPosition);
				if (this.options.arrow && this.options.position!=pos.newPosition) {
					this.positionneFleche(pos.newPosition);
				}
				this.div.setStyle({top:pos.position.top+"px",left:pos.position.left+"px"});
			}
		}
		,positionneFleche:function (position) {
//Stip.log("positionne fleche " + position);
			this.flecheUpdated = false;
			this.options.position=position;
			this.fleche();
		}
	    ,getPositionWithinViewport: function (coord) {
//Stip.log("getPositionWithinViewport");
			var newPosition = this.options.position,
			tipDimension = this.div.getDimensions(),
			scroll = document.viewport.getScrollOffsets(),
			windownDimension = document.viewport.getDimensions(),
			possibleOverflow = {
				left: "width",
				top: "height"
			};
//Stip.log(tipDimension);
//Stip.log(scroll);
//Stip.log(windownDimension);
			for (var cote in possibleOverflow) {
//Stip.log((coord[cote]+" + "+tipDimension[possibleOverflow[cote]]+" - "+scroll[cote=="left" ? 0 : 1])+" > "+windownDimension[possibleOverflow[cote]]);
//Stip.log((coord[cote] + tipDimension[possibleOverflow[cote]] - scroll[cote=="left" ? 0 : 1]) +" > "+windownDimension[possibleOverflow[cote]]);
				if ((coord[cote] + tipDimension[possibleOverflow[cote]] - scroll[cote=="left" ? 0 : 1] + this.options.offset[cote]) > windownDimension[possibleOverflow[cote]]) {
//Stip.log("ouille !");
					coord[cote] = coord[cote] - (tipDimension[possibleOverflow[cote]] + ( this.options.offset[cote] * 2 ));
					if (this.options.arrow) {
						if (cote=="left") {
							newPosition = newPosition.replace("left","LEFT").replace("right","left").replace("LEFT","right");
						} else {
							newPosition = newPosition.replace("top","TOP").replace("bottom","top").replace("TOP","bottom");
						}
					}
				}
			}
			
//			// VÃ©rification que de l'autre cÃ´tÃ© cela ne dÃ©passe pas non plus en largeur, 
//			// Car dÃ©placer de l'autre cÃ´tÃ© pour que ca dÃ©passe aussi ne sert Ã  rien,
//			var curPos_ = this.options.position.replace("top","").replace("bottom","");
//			var newPos_ = newPosition.replace("top","").replace("bottom","");
//			if (curPos_ != newPos_) {
//				console.log(windownDimension);
//				console.log(tipDimension.width);
//				console.log(coord);
//				//this.div.setStyle({height:'auto',width:'300px'});
//				//this.syncHeights();
//				console.log('FUCK !');
//			}
			
			return {
				position: coord,
				newPosition: newPosition
			}
		}
		,overlayOn: function() {
			this.overlayed = true;		
			
			if (Stip.overlayOwner) {
				Stip.overlayOwner.overlayOff(true);
			}
			
			Stip.overlayOwner = this;
//Stip.log(this.target);
			
			for (var i=0; i<this.options.overlayImmune.length; i++) {
				var tgt = $(this.options.overlayImmune[i]);
				if (tgt) {
					tgt.saveOldZIndex = tgt.getStyle('z-index') || 1;
					tgt.setStyle('position:relative');
					if (tgt.up() && !tgt.up().getStyle('position')) {
						tgt.up().setStyle('position:relative');
					}
//Stip.log(tgt.getStyle('position'));					
//Stip.log(tgt.up().getStyle('position'));					
					tgt.setStyle('z-index:'+(parseInt(Stip.overlay.getStyle('z-index'))+1));
Stip.log("overlayOn ("+tgt.id+") saved="+tgt.saveOldZIndex+" now="+tgt.getStyle('z-index'));
				}
			}
		
			this.display();
			
			if (this.options.overlay!=='static') {
				Stip.overlay.observe("click", this.undisplay.bind(this));
			}
		}
		,overlayOff: function(forced) {
Stip.log("forced = "+forced);
			this.overlayed = false;
			if (!forced) { // Si on force l'overlay a off, on n'appelle pas un effacement de l'overlay car l'overlay est certainement sollicitÃ© d'autre part
Stip.log("effacement overlay pas forcÃ©");
				Stip.overlayOwner = null;
				Stip.hideOverlay();
			}
			
			for (var i=0; i<this.options.overlayImmune.length; i++) {
				var tgt = $(this.options.overlayImmune[i]);
				if (tgt) {
					tgt.setStyle('z-index:'+tgt.saveOldZIndex);
Stip.log("overlayOff saved="+tgt.saveOldZIndex+" now="+tgt.getStyle('z-index'));
				}
			}
		}
		,mouseclick: function(e) {
//Stip.log("Stip.tip.mouseclick()");
			if (this.tipVisible) {
				this.display(e);
			} else {
				this.undisplay();
			}
//			if (!this.options.closeButton) {
//            	this.undisplay();
//			}
		}
		,mouseover: function(e) {
//Stip.log("Stip.tip.mouseover()");
			this.display(e);
		}
		/* Declenche le timer pour le message avancÃ© (infobulle plus complÃ¨te si on insiste) */
		,delayAdvancedTextStart: function() {
Stip.log("Stip.tip.delayAdvancedTextStart()");
			if (!this.ajaxContentAdvancedTexte && (this.options.urlAdvancedText || this.options.advancedText)) {
			// DÃ©lai d'affichage pour le texte avancÃ©
			if (this.delayAdvancedText && this.delayAdvancedText.cancel) this.delayAdvancedText.cancel(); // SÃ»r de ne pas en lancer plusieurs
Stip.log("Stip.tip.delayAdvancedTextStart() 2");
				this.delayAdvancedText = new Effect.Opacity(this.source, {  // Bricolage ! Utiliser scriptaculous comme timer........ :-/
					duration: 2,
					from:this.source.getStyle('opacity'), 
					to: this.source.getStyle('opacity'),
					afterFinish: function () {
Stip.log("Stip.tip.delayAdvancedTextStart() 3");
						delete this.delayAdvancedText;
						
						if (this.options.urlAdvancedText) {
							// Appel ajax pour le texte avancÃ©
							new Ajax.Request(this.options.urlAdvancedText, {
								method: 'post',
								onSuccess: function (t) {
									// On rÃ©cupÃ¨re le texte avancÃ©
									this.options.advancedText = t.responseText;
									this.fadeInAdvancedText();
								}.bind(this)
								//, postBody: ''//this.options.urlPost
								//, insertion: Insertion.Bottom
								//, evalScripts: true // IdÃ©e pour la transition d'ajout
							});
						} else {
							this.fadeInAdvancedText();
						}
					}.bind(this)
				});
			}
		}
		/* Affichage du texte avancÃ© */
		,fadeInAdvancedText: function() {
Stip.log("Stip.tip.fadeInAdvancedText() "+this.options.advancedText);
			this.ajaxContentAdvancedTexte=true; 
			
			this.div.setStyle({
width:'300px'
			});
			this.div.contentContainer.content.update(this.options.advancedText);
			
			this.syncHeights(); 
			
			// On met Ã  jour le placement de la flÃ¨che et de l'infobulle en fonction du nouveau contenu
			this.positionneFleche(this.options.position);
			this.positionne();
		}
		/*
			@param Event e Evenement
			@param Boolean immediate TRUE force a ne pas utiliser de dÃ©lai pour cet appel
		*/
		,display: function (e,immediate) {
Stip.log("Stip.tip.display()");
			// DÃ©lai d'apparition
			if (this.options.delay>0 && !immediate) {
				// DÃ©lai d'affichage
				if (this.delayed && this.delayed.cancel) this.delayed.cancel(); // SÃ»r de ne pas en lancer plusieurs
				this.delayed = new Effect.Opacity(this.source, {  // Bricolage ! Utiliser scriptaculous comme timer........ :-/
					duration: this.options.delay,
					from:this.source.getStyle('opacity'), 
					to: this.source.getStyle('opacity'),
					afterFinish: function () {
Stip.log("Stip.tip.display() timer finished");
						this.delayed = true;
						this.display(e,true);
					}.bind(this)
				});
				return;
			}
			
Stip.log("Stip.tip.display() 2");
			if (this.delayed) {
				delete this.delayed;
			}
			
			// Texte supplÃ©mentaire
			this.delayAdvancedTextStart();
			
			// Overlay si demandÃ© et pas encore fait !
			if (this.options.overlay && !this.overlayed) {
				Stip.showOverlay(this.overlayOn.bind(this));
				return;
			}						
			
			// Callback avant affichage
			if (this.options.callback.beforeDisplay) {
				this.options.callback.beforeDisplay();
			}
			
//Stip.log(this.tipVisible);			
			if (!this.tipVisible) {
				if (!this.div) {
					this.build();
				}
				
				
				
				if (this.options.hideAll) {
					Stip.hideAll();
				}
				
				if (this.div) {
					// Affichage ON
					if (this.options.fadeDuration) {
						this.div.setStyle({opacity:0});
						this.div.show();
						if (this.effect) {
	//Stip.log(this.effect.inspect())
							this.effect.cancel();
						}
						this.effect = new Effect.Opacity(this.div, { 
							duration: this.options.fadeDuration,
							transition: Effect.Transitions.sinoidal,
							from: 0, 
							to: 1
							,afterFinish: (function () { Stip.setHighest(this.div); }).bind(this)
						});
					} else {
						this.div.show();
					}
				}
//Stip.log("Stip.tip.display() 3");
								
				Stip.addVisible(this);
			}
			
//Stip.log("Stip.tip.display() 4");
			this.positionne(e);
			
			// Appel ajax
			if (!this.ajaxContent && this.options.url) {
				new Ajax.Updater(this.div.contentContainer.content, this.options.url, {
					postBody: this.options.urlPost
					, onComplete: function (o) { 
						this.ajaxContent=true; 
						this.syncHeights(); 
						
						// On met Ã  jour le placement de la flÃ¨che et de l'infobulle en fonction du nouveau contenu
						this.positionneFleche(this.options.position);
						this.positionne();
					}.bind(this)
				});
			}

			// Positionnement en zMax
			Stip.setHighest(this.div);
		}
		,cancelDelay: function() {
			if (this.delayed && this.delayed.cancel) {
				this.delayed.cancel();
			}
			if (this.delayAdvancedText && this.delayAdvancedText.cancel) {
				this.delayAdvancedText.cancel();
			}
		}
		,mouseout: function(e) {
Stip.log("Stip.tip.mouseout()");
//Stip.log(this.delayed);
			this.cancelDelay();
			if (!this.options.closeButton && !this.options.overlay) {
				this.undisplay();
			}
		}
		,hideAllUndisplay: function (force,group) {
			// Effacement appelÃ© par un hideAll
			if (!group || this.options.group==group) {
				if (this.options.hideAllImmune===false || force) {
					this.undisplay();
				}
			}
		}
		,undisplay: function () {
Stip.log("Stip.tip.undisplay()");						
			// On annule le timer
			this.cancelDelay();

			// Affichage OFF
			if (this.tipVisible) {
				if (this.options.fadeDuration) {
					if (this.effect) {
//Stip.log(this.effect.inspect())
						this.effect.cancel();
					}
					this.effect = new Effect.Opacity(this.div, { 
						duration: this.options.fadeDuration,
						transition: Effect.Transitions.sinoidal,
						from: 1, 
						to: 0
						,afterFinish: (function () { 
							Stip.setLowest(this.div); 							
							Stip.delVisible(this);	
						
							// Callback aprÃ¨s effacement
							if (this.options.callback.afterHide) {
								this.options.callback.afterHide();
							}
						}).bind(this)
				   });
				} else {
					this.div.hide();
					Stip.delVisible(this);
					
					// Callback aprÃ¨s effacement
					if (this.options.callback.afterHide) {
						this.options.callback.afterHide();
					}
				}
				
				if (this.overlayed) {
					this.overlayOff();						
				}
			}
		}
		
		,build: function () {
//Stip.log("Stip.tip.build()");
			if (document.loaded) {
				this.buildHTML();				
			} else {
				document.observe("dom:loaded", this.buildHTML.bind(this));
			}
		}
		
		,buildHTML: function () {
//Stip.log("Stip.tip.buildHTML()");

			if (!this.isBuildingHTML && !this.div) {
				this.isBuildingHTML = true;
				this.div = new Element("div");
				if (this.options.css) {
					this.div.addClassName(this.options.css);
				}
				this.div.addClassName('smartip');
				this.div.setStyle({
					height:this.options.height
					,width:this.options.width
				});

				if (this.options.fadeDuration) {
					this.div.setStyle({
						display:"none"
					});
					this.div.setOpacity(0);
				} else {
					this.div.setStyle({
						display:"none"
					});
				}
				
				if (this.options.arrow) {
					// Fleche
					this.div.insert(this.div.fleche = new Element("div",{
						className:'fleche'
					}));
				}
				
				this.div.insert(this.div.borders = new Element("div",{
					className:'borders'
				}));
				
				// Taille de la bordure

				this.options.borderSize = this.options.radius > this.options.borderSize ? this.options.radius : this.options.borderSize;
				
				this.div.insert(this.div.contentContainer = new Element("div",{
					className:'contentContainer'
				}).setStyle({
					padding:this.options.borderSize+'px'
				}));
				
				// 4 coins
				this.div.borders
				.insert( // Row Top
					(new Element("div", {
						className: "bdrRow"
					}).setStyle({
						height:this.options.borderSize+'px'
					})
					).insert( // Top left
						(new Element("div", {
							className: "absoluteContainer"
						}).setStyle({
							height:this.options.borderSize+'px'
						})).insert( // Top left content
							 this.div.borders.tl = new Element("div", {
								className: "bdrRowLeftContent"
							}).setStyle({
								width:this.options.borderSize+'px'
								,height:this.options.borderSize+'px'
							}))
					).insert( // Between left & right
						(new Element("div", {
							className: "absoluteContainer"
						}).setStyle({
							height:this.options.borderSize+'px'
						})).insert( // Between left & right content
							new Element("div", {
								className: "between"
							}).setStyle({
								height:this.options.borderSize+'px'
								,margin:"0 "+this.options.borderSize+'px'
							}).addClassName('borderColor'))
					).insert( // Top right
						(new Element("div", {
							className: "absoluteContainer"
						}).setStyle({
							height:this.options.borderSize+'px'
						})).insert( // Top right content
							 this.div.borders.tr = new Element("div", {
								className: "bdrRowRightContent"
							}).setStyle({
								width:this.options.borderSize+'px'
								,height:this.options.borderSize+'px'
							}))
					)
				).insert( // Middle
					(this.div.borders.middle = new Element("div", {
						className: "bdrRow"
					}).setStyle({
						height:this.options.borderSize+'px'
					}).addClassName('borderColor'))
				).insert( // Row Bottom
					(new Element("div", {
						className: "bdrRow"
					}).setStyle({
						height:this.options.borderSize+'px'
					})
					).insert( // Bottom left
						(new Element("div", {
							className: "absoluteContainer"
						}).setStyle({
							height:this.options.borderSize+'px'
						})).insert( // Top left content
							 this.div.borders.bl = new Element("div", {
								className: "bdrRowLeftContent"
							}).setStyle({
								width:this.options.borderSize+'px'
								,height:this.options.borderSize+'px'
							}))
					).insert( // Between left & right
						(new Element("div", {
							className: "absoluteContainer"
						}).setStyle({
							height:this.options.borderSize+'px'
						})).insert( // Between left & right content
							new Element("div").setStyle({
								height:this.options.borderSize+'px'
								,margin:"0 "+this.options.borderSize+'px'									
							}).addClassName('borderColor'))
					).insert( // Bottom right
						(new Element("div", {
							className: "absoluteContainer"
						}).setStyle({
							height:this.options.borderSize+'px'
						})).insert( // Top right content
							 this.div.borders.br = new Element("div", {
								className: "bdrRowRightContent"
							}).setStyle({
								width:this.options.borderSize+'px'
								,height:this.options.borderSize+'px'
							}))
					)
				);
				
				if (this.options.title) {
					this.div.contentContainer.insert(this.div.contentContainer.bar = new Element("div", {
						className: "bar"
					}).setStyle({
						height:this.options.titleHeight
					}));

					// Titre
					this.div.contentContainer.bar.insert(new Element("div", {
						className: "title"
					}).update(this.options.title));
					
					if (this.options.closeButton===true) {
						// Bouton close
						this.div.contentContainer.bar.insert(this.div.contentContainer.bar.closeButton = new Element("div", {
							className: "close"
//						}).setStyle({
//							float:'right'
						}));
						this.div.contentContainer.bar.closeButton.observe("click", this.undisplay.bind(this));
					} else if (cb = $(this.options.closeButton)) {
						cb.observe("click", this.undisplay.bind(this));
					}
					
					this.div.contentContainer.bar.insert(new Element("div").setStyle({
						clear:"both"
					}));
				}
				
				this.div.contentContainer/*.setStyle(this.options.offset)*/.insert(this.div.contentContainer.content = new Element("div", {
					className: "content"
				}).update(this.content));
				
				$(document.body).insert(this.div);
//alert(this.div.getWidth());

				if (this.options.borderSize) {
					this.options.borderColor = Stip.getStyle(this.div.borders.middle,"background-color");
					$w('tl tr bl br').each(function (pos) {						
						if (this.options.radius) {
							Stip.coinArrondi(this.div.borders[pos], pos, {
								backgroundColor: this.options.borderColor,
								border: this.options.borderSize,
								radius: this.options.radius
							});
						} else {
							this.div.borders[pos].addClassName('borderColor');
						}
					}.bind(this));
					
					// On met Ã  jour la hauteur du fond middle des bordures
					this.syncHeights();
				}
						
				this.fleche();
				
				// Scrollbar
				var scrollOffset = this.target.cumulativeScrollOffset();
//Stip.log(scrollOffset);
				scrollOffset = {
					left:scrollOffset.left + this.div.cumulativeOffset().left
					,top:scrollOffset.top + this.div.cumulativeOffset().top	
				};
				this.div.setStyle(scrollOffset);
				
				/* FUCK IE */
				// On met Ã  jour la largeur
				this.div.setStyle({width:(this.div.getWidth())+"px"});
				// Ce putain de float marche que en l'appliquant aprÃ¨s sous IE8 !
				if (this.div.contentContainer && this.div.contentContainer.bar && this.div.contentContainer.bar.closeButton) {
					this.div.contentContainer.bar.closeButton.setStyle({cssFloat:'right'});
				}
				
				delete this.isBuildingHTML;
			}
		}
		,syncHeights: function (bSize) {
			if (!this.options.width || this.options.url) { // @todo pas moyen de forcer le width avec url dans ce cas !
				this.div.setStyle({width:"auto"});
			}
			if (this.options.maxWidth && (this.div.getWidth()-this.options.borderSize*2)>this.options.maxWidth) {
				this.div.setStyle({width:this.options.maxWidth+"px"});
			} else {
				this.div.setStyle({width:(this.div.getWidth()-this.options.borderSize*2)+"px"});
			}
			this.div.borders.middle.setStyle({height:(this.div.getHeight()-this.options.borderSize*2)+"px"});
		}
		,fleche: function () {
//Stip.log("fleche");
			if (this.options.arrow) {
				//this.options.offset = {left:this.div.contentContainer.viewportOffset().left, top:this.div.contentContainer.viewportOffset().top};
				this.options.offset = {left:0, top:0};
//alert("offset = "+this.options.offset.left+" "+this.options.offset.top);				
				var divOrigin = {left: 0, top: 0} // Offset du div la flÃ¨che
				, coordFleche = [{left: 0, top: 0}, {left: 0, top: 0}, {left: 0, top: 0}]; // CoordonnÃ©es du pied de la flÃ¨che
				
				var r = this.options.arrow*Math.sqrt(2)/2;
				
				// Conversion de l'offset en fonction de la position demandÃ©e
				switch (this.options.position) {
					case "rightbottom": 
					case "bottomright":
						// On prend comme origine
						divOrigin.left = this.options.offset.left;
						divOrigin.top = this.options.offset.top;
						
						
						// Ce qui donne pour coordonnÃ©es pour la flÃ¨che
						//base[0].top = this.options.offset.top;
						//base[0].left = this.options.offset.left;
						coordFleche[1].left = r + this.options.arrowBase / 2;
						coordFleche[1].top = r;
						coordFleche[2].left = r;
						coordFleche[2].top = r + this.options.arrowBase / 2;
						
						// On dÃ©place l'info bulle
						this.options.offset.left += r;
						this.options.offset.top += r;
						break;
						
					case "right":
					case "rightmiddle":
						divOrigin.left = this.options.offset.left;
						divOrigin.top = this.options.offset.top + this.div.getHeight() / 2 - this.options.arrowBase / 2;
						//coordFleche[0].left = 0;
						coordFleche[0].top = this.options.arrowBase / 2;
						coordFleche[1].left = this.options.arrow;
						//coordFleche[1].top = 0;
						coordFleche[2].left = this.options.arrow;
						coordFleche[2].top = this.options.arrowBase;
						this.options.offset.left += this.options.arrow;
						//this.options.offset.top += r;
						break;
						
					case "righttop":
					case "topright":
						divOrigin.left = this.options.offset.left;
						divOrigin.top = this.options.offset.top + this.div.getHeight() - r - this.options.arrowBase / 2;
						//coordFleche[0].left = 0;
						coordFleche[0].top = r + this.options.arrowBase / 2;
						coordFleche[1].left = r;
						//coordFleche[1].top = 0;
						coordFleche[2].left = r + this.options.arrowBase / 2;
						coordFleche[2].top = this.options.arrowBase / 2;
						this.options.offset.left += r;
						this.options.offset.top -= r;
						break;
						
					case "bottom":
					case "bottommiddle":
						divOrigin.left = this.options.offset.left + this.div.getWidth() / 2 - this.options.arrowBase / 2;
						divOrigin.top = this.options.offset.top;
						coordFleche[0].left = this.options.arrowBase / 2;
						//coordFleche[0].top = this.options.arrow;
						coordFleche[1].left = this.options.arrowBase;
						coordFleche[1].top = this.options.arrow;
						//coordFleche[2].left = 0;
						coordFleche[2].top = this.options.arrow;
						//this.options.offset.left += 0;
						this.options.offset.top += this.options.arrow;
						break;
						
					case "leftbottom":
					case "bottomleft":
						divOrigin.left = this.options.offset.left + this.div.getWidth() - r - this.options.arrowBase / 2;
						divOrigin.top = this.options.offset.top;
						coordFleche[0].left = r + this.options.arrowBase / 2;
						//coordFleche[0].top = r;
						//coordFleche[1].left = r;
						coordFleche[1].top = r;
						coordFleche[2].left = this.options.arrowBase / 2;
						coordFleche[2].top = this.options.arrowBase;
						this.options.offset.left -= r;
						this.options.offset.top += r;
						break;
						
					case "top":
					case "topmiddle":
						divOrigin.left = this.options.offset.left + this.div.getWidth() / 2 - this.options.arrowBase / 2;
						divOrigin.top = this.options.offset.top + this.div.getHeight() - this.options.arrow;
						coordFleche[0].left = this.options.arrowBase / 2;
						coordFleche[0].top = this.options.arrow;
						coordFleche[1].left = this.options.arrowBase;
						//coordFleche[1].top = 0;
						//coordFleche[2].left = 0;
						//coordFleche[2].top = 0;
						//this.options.offset.left += 0;
						this.options.offset.top -= this.options.arrow;
						break;
						
					case "left":
					case "leftmiddle":
						divOrigin.left = this.options.offset.left + this.div.getWidth() - this.options.arrow;
						divOrigin.top = this.options.offset.top + this.div.getHeight() / 2 - this.options.arrowBase / 2;
						coordFleche[0].left = this.options.arrow;
						coordFleche[0].top = this.options.arrowBase / 2;
						//coordFleche[1].left = this.options.arrow;
						//coordFleche[1].top = this.options.arrowBase / 2;
						//coordFleche[2].left = this.options.arrow;
						coordFleche[2].top = this.options.arrowBase;
						this.options.offset.left -= this.options.arrow;
						//this.options.offset.top += r;
						break;
						
					case "lefttop":
					case "topleft":
						divOrigin.left = this.options.offset.left + this.div.getWidth() - r - this.options.arrowBase / 2;
						divOrigin.top = this.options.offset.top + this.div.getHeight() - r - this.options.arrowBase / 2;
						coordFleche[0].left = r + this.options.arrowBase / 2;
						coordFleche[0].top = r + this.options.arrowBase / 2;
						//coordFleche[1].left = r;
						coordFleche[1].top = this.options.arrowBase / 2;
						coordFleche[2].left = this.options.arrowBase / 2;
						//coordFleche[2].top = this.options.arrowBase;
						this.options.offset.left -= r;
						this.options.offset.top -= r;
						break;
				}
				
//Stip.log(this.options.offset);
//Stip.log(divOrigin);
//Stip.log(coordFleche);
				
				// DÃ©terminer les dimensions de la fleche
				var dimFleche = { left:0, top:0 };
				for (var i=0;i<3;i++) {
					dimFleche.top = Math.max(coordFleche[i].top,dimFleche.top);
					dimFleche.left = Math.max(coordFleche[i].left,dimFleche.left);

				}
//Stip.log(dimFleche);
				
				// Positionnement des divs
				this.div.borders.setStyle(Stip.addPx(this.options.offset));
				this.div.contentContainer.setStyle(Stip.addPx(this.options.offset));
				this.div.fleche.setStyle(Stip.addPx(divOrigin));
				
				if (!(!document.createElement("canvas").getContext)) {
					if (!this.flecheUpdated && this.div.fleche.canvas) {
						this.div.fleche.canvas.remove();
						delete this.div.fleche.canvas;
					}
				   if (!this.div.fleche.canvas) {
					   this.div.fleche.canvas = new Element("canvas", {
							width: dimFleche.left + "px",
							height: dimFleche.top + "px"
						});
				   }
				   
				   fleche = this.div.fleche.canvas;
				   this.div.fleche.insert(fleche);
				
				   var shape = fleche.getContext("2d");
				   //shape.clearRect(0, 0, 300, 300);
				   shape.fillStyle = this.options.borderColor;
//Stip.log(this.options.borderColor);
				   shape.beginPath();
				   shape.moveTo(coordFleche[0].left,coordFleche[0].top);
				   shape.lineTo(coordFleche[1].left,coordFleche[1].top);
				   shape.lineTo(coordFleche[2].left,coordFleche[2].top);
				   shape.lineTo(coordFleche[0].left,coordFleche[0].top);
				   shape.fill();
//Stip.log(this.div.fleche.canvas.getDimensions());
				
				} else {
				   /* Sans canvas */                  
				   if (!this.div.fleche.canvas) {
//				   alert("M "+Math.round(coordFleche[0].left)+","+Math.round(coordFleche[0].top)+" L "+Math.round(coordFleche[1].left)+","+Math.round(coordFleche[1].top)+", "+Math.round(coordFleche[2].left)+","+Math.round(coordFleche[2].top)+" X E");
//				   alert(Math.round(dimFleche.left)+" "+Math.round(dimFleche.top));
					   this.div.fleche.canvas = new Element("ie_vml:shape", {
						   path: "M "+Math.round(coordFleche[0].left)+","+Math.round(coordFleche[0].top)+" L "+Math.round(coordFleche[1].left)+","+Math.round(coordFleche[1].top)+", "+Math.round(coordFleche[2].left)+","+Math.round(coordFleche[2].top)+" X E"
						   ,fillcolor: this.options.borderColor
						   ,strokeWeight: "1px"
						   ,strokeColor: this.options.borderColor
						   ,coordorigin: "0 0"
						   ,coordsize: Math.round(dimFleche.left)+" "+Math.round(dimFleche.top)
					   });
				   }
				   fleche = this.div.fleche.canvas;
				   fleche.setStyle({
					   width: Math.round(dimFleche.left)+"px"
					   ,height: Math.round(dimFleche.top)+"px"
					   ,position: "absolute"
				   });
				   this.div.fleche.insert(fleche);
				   fleche.outerHTML = fleche.outerHTML;
				} 
				
				this.flecheUpdated = true;
			}
		}
	})
	,addPx: function (o) {
	   var o = Object.clone(o);
	   if (Object.isString(o)) {
		   return o+"px";
	   }
	   for (i in o) {
		   o[i]=o[i]+"px";
	   }
	   return o;
	} 
    ,coinArrondi: function (div, position, options) {
        options = options || this.options,
        pos = {
            left: (position == "tl" || position == "bl")
            ,top: (position == "tl" || position == "tr")
        };
        if (!(!document.createElement("canvas").getContext)) {
            var canvas = new Element("canvas", {
                width: options.border + "px",
                height: options.border + "px"
            });
            div.insert(canvas);
            var shape = canvas.getContext("2d");
            shape.fillStyle = options.backgroundColor;
            shape.arc((pos.left ? options.radius : options.border - options.radius), (pos.top ? options.radius : options.border - options.radius), options.radius, 0, Math.PI * 2, true);
            shape.fill();
            shape.fillRect((pos.left ? options.radius : 0), 0, options.border - options.radius, options.border);
            shape.fillRect(0, (pos.top ? options.radius : 0), options.border, options.border - options.radius);
        } else {
            div.insert(coin = new Element("div").setStyle({
                margin: 0,
                padding: 0,
                position: "relative",
                display: "block",
                width: options.border + "px",
                height: options.border + "px"
                ,overflow: "hidden"
            }));
            var arrondi = new Element("ie_vml:roundrect", {
                fillcolor: options.backgroundColor,
                strokeWeight: "1px",
                strokeColor: options.backgroundColor,
                arcSize: (options.radius / options.border * 0.5).toFixed(2)
            }).setStyle({
                width: 2 * options.border - 1 + "px",
                height: 2 * options.border - 1 + "px",
                position: "absolute",
                left: (pos.left ? 0 : (-1 * options.border)) + "px",
                top: (pos.top ? 0 : (-1 * options.border)) + "px"
            });
            coin.insert(arrondi);
            arrondi.outerHTML = arrondi.outerHTML;
        }
    }
}).init();