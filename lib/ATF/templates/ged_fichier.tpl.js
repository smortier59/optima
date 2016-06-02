{strip}
ATF.refreshGridGed = function(id,id_element) {
	Ext.Ajax.request({
	 url: 'ged_fichier,updateListing.ajax',
	 method:"POST",
	   params:{
		   'table':'ged_fichier',
		   'pager':'gsa_ged_fichier_ged_fichier',
		   'id':'gridged',
		   'id_ged_dossier':id,
		   'id_element':id_element,
		   'table_join':'{$current_class->table}'
	   },
	   success: function (response, opts) {
		   	Ext.getCmp("rep_select").setValue(id);
		   	ATF.currentpage["gsa_ged_fichier_ged_fichier"]=0;
			var searchB=Ext.getCmp("gridged").getStore().searchBox;
			eval(response.responseText);
			Ext.getCmp("gridged").colModel.setConfig(cols["gsa_ged_fichier_ged_fichier"]);
			store["gsa_ged_fichier_ged_fichier"].searchBox = searchB;
			store["gsa_ged_fichier_ged_fichier"].searchBox.store= store["gsa_ged_fichier_ged_fichier"];
			Ext.getCmp("gridged").reconfigure(store["gsa_ged_fichier_ged_fichier"],Ext.getCmp("gridged").colModel);
			Ext.getCmp("gridged").getStore().load({ params:{ start:0, limit:30 }});
	   }
	 });
};

ATF.deleteHandlerFile = function(version,id_element) {
	if(Ext.getCmp('gridged')){
		var grid = Ext.getCmp('gridged');
		var records = grid.getSelectionModel().getSelections();
	
		Ext.Msg.show({ 
			title:ATF.usr.trans('Etes_vous_sur'),
			msg:ATF.usr.trans('supprimer_les_enregistrements_selectionnes'),
			buttons:Ext.Msg.YESNO,
			fn:function (buttonId,text,opt) {
				switch (buttonId) {
					case "no":
						break;
						
					case "yes":	
						var ids = Array();	
						for (var r=0;r<records.length;r++) {
							if(records[r].json){
								ids.push(records[r].data["ged_fichier__dot__id_ged_fichier"]);
							}
						}
						if(ids[0]){
							Ext.Ajax.request({
								url: (version?'ged_fichier,delete.ajax':'ged_fichier,deleteAllVersion.ajax'),
								success:function(action) {									
									ATF.extRefresh({ response:action });
									if ($.parseJSON(action.responseText).result) {
										grid.store.remove(records);
									}
									grid.getStore().load({ params:{ start:0, limit:30 }});
								},
								params: { 
									"id[]": ids
									,'table_join':'{$current_class->table}'
									,'id_element':id_element
								}
							});
						}
						break;
				}
			},
			animEl:grid,
			closable:false,
			icon:Ext.MessageBox.QUESTION
		});	
	}
};

ATF.afficheGed = function(id_element,table_join) {
	Ext.Ajax.request({
	 url: 'ged_fichier,fenGed.ajax',
	 method:"POST",
	   params:{
		   'table':'ged_fichier',
		   'pager':'gsa_ged_fichier_ged_fichier',
		   'id':'gridged',
		   'id_element':id_element,
		   'table_join':'{$current_class->table}'
	   },
	   success: function (response, opts) {
		   	eval(response.responseText);
			var searchBox=Array();
			searchBox["gsa_ged_fichier_ged_fichier"] = new Object();
			searchBox["gsa_ged_fichier_ged_fichier"]=new Ext.ux.form.SearchField({
				store: store["gsa_ged_fichier_ged_fichier"],
				width:200
			});	
			store["gsa_ged_fichier_ged_fichier"].searchBox = searchBox["gsa_ged_fichier_ged_fichier"];
			
			new Ext.Window({
				title: ATF.usr.trans('ged_dossier'),
				id:'mywindowGed'+id_element,
				layout:'border',
				width: 750,
				height:500,
				items:[{
						xtype:'hidden'
						,id: 'rep_select'
						,value: 0
					},{
						region:'west',
						xtype:'panel',
						height:500,
						id:'pangauche',
						activeTab:0,
						items:[{
							region: 'north',
							itemId: 'arbre',
							height:415,
							id:'pannordouest',
							xtype: 'treepanel',
							width: 200,
							autoScroll: true,
							split: true,
							useArrows: true,
							animate: false,
							border: false,
							bodyBorder : false,
							frame: true,
							loader:new Ext.tree.TreeLoader({ dataUrl: 'ged_dossier,branch.ajax,table_join='+table_join+'&valeur='+id_element+'&display=true' }),
							root: new Ext.tree.AsyncTreeNode({
								nodeType: 'async',
								text: ATF.usr.trans('genealogie'),
								draggable: false,
								expanded: true,
								id: 'source',
								href:"javascript:ATF.refreshGridGed(0,'"+id_element+"');"
							})
						},{
							region: 'south',
							xtype:'panel',
							id:'pansudouest',
							height:85,
							items:[{
								xtype:'button',
								text: 'ajout_rep',
								listeners: {
									'click': function(fb, v){
										if(Ext.getCmp("rep_select").getValue()==0){
											var nom_rep="Racine";	
										}else{
											var nom_rep=Ext.getCmp("pannordouest").getNodeById(Ext.getCmp("rep_select").getValue()).text;
										}
										new Ext.Window({
											title: ATF.usr.trans('ged_dossier'),
											width: 375,
											height:100,
											id:'fen_ajout_rep',
											items:[{
												   xtype:'displayfield'
												   ,value:'Ajouter un repertoire dans le dossier : '+nom_rep
											   },{
													xtype:'textfield'
													,id:'new_rep'
											   },{
												   xtype:'button',
												   text:'{ATF::$usr->trans("valider")}',
												   listeners: {
													   	'click':function(){
															if(Ext.getCmp("new_rep").getValue()){
																ATF.ajax('ged_dossier,ajout_rep.ajax','id_dossier_parent='+Ext.getCmp("rep_select").getValue()+'&nom='+Ext.getCmp("new_rep").getValue()+'&table_join={$current_class->table}&val_table_join='+id_element,{ onComplete:function(obj){ Ext.getCmp("pannordouest").getRootNode().reload();Ext.getCmp("fen_ajout_rep").destroy(); }});
															}else{
																alert("Veuillez preciser le nom du dossier à creer");	
															}
														}
												   }
											}]
										}).show();
									}
								}
							},{
							   xtype:'button',
							   text:'{ATF::$usr->trans("delete")}',
							   listeners: {
									'click':function(){
										if(Ext.getCmp("rep_select").getValue()>0){
											ATF.ajax('ged_dossier,sup_rep.ajax','id_dossier='+Ext.getCmp("rep_select").getValue()+'&table_join={$current_class->table}',{ 
												onComplete:function(obj){ 
													if(obj.result){
														Ext.getCmp("pannordouest").getRootNode().reload();
													}else{
														alert("Il y a encore des fichiers dans le répertoire");	
													}
												}
											});
										}else{
											alert("Aucun répertoire sélectionné ou il s'agit du dossier racine");	
										}
									}
							   }
							},{
							   xtype:'button',
							   text:'{ATF::$usr->trans("delete_version_file")}',
							   listeners: {
									'click':function(){
										ATF.deleteHandlerFile(1,id_element);
									}
							   }
							},{
							   xtype:'button',
							   text:'{ATF::$usr->trans("delete_file")}',
							   listeners: {
									'click':function(){
										ATF.deleteHandlerFile(0,id_element)
									}
							   }
							}]
						}]
						,listeners:{
							resize:function(win,wi,h){
								if(h!=500){
									Ext.getCmp('pannordouest').setHeight(415+(h-500));
								}
							}
						}
					},{
						
						region: 'center',
						xtype:'panel',
						height:500,
						id:'pancentre',
						activeTab:0,
						items:[{
									region: 'north',
									xtype:'tabpanel',
									id:'pannord',
									height:384,
									activeTab:0,
									items:[{
										xtype:'editorgrid',
										columns:cols["gsa_ged_fichier_ged_fichier"],
										store:store["gsa_ged_fichier_ged_fichier"],
										id:'gridged',			
										title:'Liste des fichiers',
										selModel: new Ext.grid.RowSelectionModel(),
										loadMask: true,
										listeners:{
											render: function(grid){
												grid.getView().scroller.dom.scrollTop = 0;
												grid.getStore().load({ params:{ start:0, limit:30 } }); 
											}
										},
										viewConfig: function (o){
											o.onLoad=Ext.emptyFn;
											if (typeof(o.listeners)=='undefined') {
												o.listeners = {};
											}
											o.listeners.beforerefresh=function(v) {
												v.scrollTop = v.scroller.dom.scrollTop;
											};
											o.listeners.refresh=function(v) {
												v.scroller.dom.scrollTop = v.scrollTop;
											};
											return o;
										}({
											forceFit:true
											,enableRowBody:true
											,emptyText:"Aucun enregistrement"
												,showPreview:false
												,getRowClass: function(record, rowIndex, p, store){
												}
										})
									}]
								},{
									{$key="fichier"}
									{$table="ged_fichier"}
								 	region: 'south',
									xtype:'panel',
									id:'pansud',
									height:85,
									items:[{
										xtype:'button'
										,text:"{ATF::$usr->trans('upload_fichier')}"
										,listeners:{
										   'click': function(){
												var win=new Ext.Window({
													layout: 'fit',
													title: "{ATF::$usr->trans('upload_fichier')}",
													width:400,
													height:150,
													items:[
														new Ext.FormPanel({
															fileUpload: true,
															id:'{$key}_form',
															width: 500,
															autoHeight: true,
															labelWidth: 50,
															bodyStyle: 'padding: 10px 10px 10px 10px;',
															items: [{
																xtype: 'fileuploadfield',
																emptyText: "{ATF::$usr->trans('upload_fichier')}",
																fieldLabel: 'Fichier',
																name: '{$key}',
																buttonText: 'Parcourir...',
																width: 300
															},{
																xtype:'textfield',
																name:'field',
																value:'{$key}',
																inputType:'hidden'
															}],
															buttons: [{
																text: 'Valider',
																handler: function(){
																	if(Ext.getCmp('{$key}_form').getForm().isValid()){
																		Ext.getCmp('{$key}_form').getForm().submit({
																			method: 'post',									   	
																			url: 'extjs.ajax',
																			params: {
																				'key':'{$key}'
																				,'table':'{$table}'
																				,'field':'{$key}'
																				,'table_join':'{$current_class->table}'
																				,'val_table_join':id_element
																				,'id_ged_dossier':Ext.getCmp("rep_select").getValue()
																				, 'extAction':'{$table}'
																				, 'extMethod':'uploadXHR'
																			},
																			waitTitle:'Veuillez patienter',
																			waitMsg: 'Chargement ...'
																			, success:function(form, action) {
																				ATF.refreshGridGed(Ext.getCmp("rep_select").getValue(),id_element);
																				win.hide();
																			}
																		});
																	}
																}
															},{
																text: 'Annuler',
																handler: function(){
																	Ext.getCmp('{$key}_form').getForm().reset();
																	win.hide();
																}
															}]
														})
									
													]
												}).show();
										   }
										}
									   },{
										autoHeight:true,
										id:'xhrUpload-{$table}-{$key}',
										html:'
											<style type="text/css">
											.progress-bar-container { color:white; background-color:#CCC; border:1px solid black; overflow:hidden; position:relative; height:15px;   }
											.progress-bar-container > div.progress-bar { position:absolute; color:white; background-color:#00C; height:15px; overflow:hidden; }
											#drop-area{$key} { background-color:#EEE; border:1px dashed black; cursor:crosshair; height:30px;  padding:1em; font-size:13px; }
											#drop-area{$key}.over { background-color:#DDD; }
											#file-list{$key} > li { float:left; width:400px; margin:.5em; }
											</style>
											<div id="ajaxUpload{$key}">
												<p id="drop-area{$key}">
													'+ATF.usr.trans('ou_glissez_et_deplacez_un_fichier_dans_cette_zone')+' (Max. {ATF::$maxFileSize}Mo)
													<br />'+ATF.usr.trans('extensions_acceptees')+' [toutes] 
												</p>
												<ul id="file-list{$key}" style="display:none">
													<li class="no-items"></li>
												</ul>
											</div>'
										,listeners:{
											'afterRender':function (e) {
												var dropArea = document.getElementById("drop-area{$key}"),
													fileList = document.getElementById("file-list{$key}");
									
												function uploadFile (file) {
													var li = document.createElement("li"),
														div = document.createElement("div"),
														img,
														progressBarContainer = document.createElement("div"),
														progressBar = document.createElement("div"),
														reader,
														xhr,
														fileInfo;
													
													li.appendChild(div);
													progressBarContainer.className = "progress-bar-container";
													progressBar.className = "progress-bar";
													progressBarContainer.appendChild(progressBar);
													li.appendChild(progressBarContainer);
											
													xhr = new XMLHttpRequest();
											
													function handleHttpResponse() {
														if(xhr.readyState == 4 && xhr.status == 200) {
														}
													};
													
													if(xhr.upload.addEventListener){
														xhr.upload.addEventListener("progress", function (evt) {
															if (evt.lengthComputable) {
																var pct = Math.round((evt.loaded / evt.total) * 100);
																progressBar.style.width = pct+"%";
																progressBar.innerHTML = pct+"% ("+evt.loaded+"/"+evt.total+")";
																Ext.getCmp('pansud').setHeight(135);
																Ext.getCmp('pannord').setHeight(334);
																fileList.appendChild(li);
																fileList.style.display="block";
															}
														}, false);
												
														xhr.addEventListener("load", function (a,z,e,r) {
															progressBarContainer.className += " uploaded";
															progressBar.style.width = '100%';
															progressBar.innerHTML = ATF.usr.trans('recu_par_le_serveur_web');
															var o = $.parseJSON(this.responseText);
															ATF.ajax_refresh(o);		
															if (Ext.getCmp("{$table}[{$key}]")) {
																Ext.getCmp("{$table}[{$key}]").setValue(o.{$key});
															} else if (Ext.getCmp("{$key}")) {
																Ext.getCmp("{$key}").setValue(o.{$key});
															}
															Ext.getCmp('pansud').setHeight(85);
															Ext.getCmp('pannord').setHeight(384);
															if(isNaN(Ext.getCmp("rep_select").getValue())){
																Ext.getCmp("pannordouest").getRootNode().reload();
															}
															fileList.style.display="none";
														}, false);
													}
													xhr.onreadystatechange = handleHttpResponse;													
													
													xhr.open("post", "{$table},uploadXHR.ajax,field={$key}&table_join={$current_class->table}&val_table_join="+id_element+"&id_ged_dossier="+Ext.getCmp("rep_select").getValue()+"&extTpl[{$key}]=generic-upload_fichier", true);
											
													xhr.setRequestHeader("Content-Type", "application/octet-stream");
													xhr.setRequestHeader("X-File-Name",file.name || file.fileName);
													xhr.setRequestHeader("X-File-Size", file.size);
													xhr.setRequestHeader("X-File-Type", file.type);
													xhr.overrideMimeType('text/plain; charset=x-user-defined-binary');

													if (file.size>{ATF::$maxFileSize}*1024*1024) {
														alert("Fichier trop gros : limite {ATF::$maxFileSize}Mo !");
														return;
													}
													
													xhr.send(file);
													
													fileInfo = "<div><strong>"+ATF.usr.trans('filename')+"</strong> " + file.name + "</div>";
													div.innerHTML = fileInfo;
														
													if (true) { /* UN SEUL FICHIER POUR L'INSTANT ! */
														fileList.innerHTML = "";
													}
												}
											
												function traverseFiles (files) {
													if (typeof files !== "undefined") {
														for (var i=0, l=files.length; i<l; i++) {
															{if $current_class->files[$key].type}
																var ext = [{foreach from=$current_class->files[$key].convert_from item=i}{if !$i@first},{/if}"{$i}"{/foreach}];
																if (ext.length==0 || !ext) var ext = [{foreach from=$current_class->files[$key].type item=i}{if !$i@first},{/if}"{$i}"{/foreach}];
																if (ext.indexOf(files[i].name.substr(files[i].name.indexOf('.')+1).toLowerCase())>-1) {
																	uploadFile(files[i]);
																} else {
																	alert("Mauvais "+ATF.usr.trans('filetype')+" ("+files[i].name+")");
																	return;
																}
															{else}
																uploadFile(files[i]);
															{/if}
														}
													} else {
														fileList.innerHTML = "No support for the File API in this web browser";
													}
												}
												
												if (dropArea && dropArea.addEventListener) {												
													dropArea.addEventListener("dragleave", function (evt) {
														var target = evt.target;
														if (target && target === dropArea) {
															this.className = "";
														}
														evt.preventDefault();
														evt.stopPropagation();
													}, false);
												
													dropArea.addEventListener("dragenter", function (evt) {
														this.className = "over";
														evt.preventDefault();
														evt.stopPropagation();
													}, false);
												
													dropArea.addEventListener("dragover", function (evt) {
														evt.preventDefault();
														evt.stopPropagation();
													}, false);
												
													dropArea.addEventListener("drop", function (evt) {
														traverseFiles(evt.dataTransfer.files);
														ATF.refreshGridGed(Ext.getCmp("rep_select").getValue(),id_element);
														this.className = "";
														evt.preventDefault();
														evt.stopPropagation();
													}, false);
												} else if (Ext.getCmp("xhrUpload-{$table}-{$key}")) {
													Ext.getCmp("xhrUpload-{$table}-{$key}").destroy();
												}
											}
										}
									}]
							  }]
					}]
					,listeners:{
						resize:function(win,wi,h){
							if(h!=500){
								Ext.getCmp('pannord').setHeight(384+(h-500));
							}
						}
					}
			}).show();
		}
	 });
};
{/strip}