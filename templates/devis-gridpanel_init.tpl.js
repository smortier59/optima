{strip}
{* Ajout du champ nécessaire pour ce renderer *}
{util::push($fieldsKeys,"devis.etat")}

{* Pdf Devis *}
ATF.renderer.devisPdf=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		if(record.data.devis__dot__etat!='bloque' || '{ATF::$usr->get(id_profil)}'=='1') {
			if (filetype) {
				var id = record.data[table+'__dot__id_'+table];
				return '<a href="'+table+'-select-'+field+'-'+id+'.dl" alt="'+ATF.usr.trans("popup_download",table)+'">'+
					'<img src="http://static.absystech.net/images/icones/'+filetype+'.png" class="icone" />'+
					'</a>';
			} else {
				return '<img src="http://static.absystech.net/images/icones/warning.png" />';
			}
		}
	}
};

ATF.renderer.comiteExpand=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		if(record.data.devis__dot__etat!='perdu') {
			var id = record.data[table+'__dot__id_'+table];
			return '<a href="comite-insert.html,id_devis='+id+'" onclick="ATF.goTo(\'comite-insert.html,id_devis='+id+'\'); return false;"><img src="{ATF::$staticserver}images/icones/expand.png" height="16" width="16" alt="" /></a>';
		} else {
			return '<img src="{ATF::$staticserver}images/icones/no_2.png" />';
		}
	}
};

{* Bouton devis-vers-commande *}
{if ATF::$usr->privilege('commande','insert')}
	ATF.renderer.devisExpand=function(table,field) {
		return function(filetype, meta, record, rowIndex, colIndex, store) {
			var id = record.data[table+'__dot__id_'+table];
			if (record.data.devis__dot__etat=="attente") {
				return '<a href="commande-insert.html,id_devis='+id+'" onclick="ATF.goTo(\'commande-insert.html,id_devis='+id+'\'); return false;"><img src="{ATF::$staticserver}images/icones/expand.png" height="16" width="16" alt="" /></a>';
				{if ATF::$usr->get("id_profil")==1}
					} else if (record.data.devis__dot__etat=="bloque") {
						return '<a href="javascript:;" onclick="ATF.tpl2div(\'devis,valider.ajax\',\'id_devis='+id+'\');"><img src="{ATF::$staticserver}images/icones/valid.png" height="16" width="16" alt="" /></a>';
				{/if}
			} else {
				return '<img src="{ATF::$staticserver}images/icones/no_2.png" />';
			}
		}
	};
{/if}


ATF.renderer.CreerFacture=function(table,field) {	
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		var id = record.data[table+'__dot__id_'+table];
		return '<a href="facture-insert.html,id_devis='+id+'" onclick="ATF.goTo(\'facture-insert.html,id_devis='+id+'\'); return false;"><img src="{ATF::$staticserver}images/icones/expand.png" height="16" width="16" alt="" /></a>';
	}
};



{if ATF::$usr->privilege('devis','update')}
	

	{if ATF::$codename == "cleodis"}	
		ATF.renderer.devisPerdu=function(table,field) {
			return function(filetype, meta, record, rowIndex, colIndex, store) {
				var idDiv = Ext.id();
				var id = record.data[table+'__dot__id_devis'];
				

				if (record.data.devis__dot__etat=="attente") {					
					var btnRefus = {
						xtype:'button',
						id:"refus"+id,
						buttonText: '',
						buttonOnly: true,
						iconCls: 'btnRefus',
						cls:'floatLeft',
						tooltip: '{ATF::$usr->trans("Refus")}',
						tooltipType:'title',
						listeners: {
							'click': function(fb, v){	
									var form = new Ext.FormPanel({
											frame:true,
											autoHeight:true,
											id:'myForm'+id,
											name:'myFormName'+id,
											title: '',
											bodyStyle:'padding:5px 5px 0',
											items: [											 
												 {
												 	 xtype : 'textfield'
													,fieldLabel: 'Raison du refus'
													,name: 'raison_refus'
												 }																		
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
															'extAction':'devis'
															,'extMethod':'perdu'
															,'id_devis':id											
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
											title: '{ATF::$usr->trans("Raison du refus : ")}',
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
							}
						}
					};
					
					(function(){
						var params = {
							renderTo: idDiv,
							items:[btnRefus]
							
						};
						var p = new Ext.Container(params);
					}).defer(25);
					return '<div class="left" id="'+idDiv+'"></div>';

				} else {
					return '<img src="{ATF::$staticserver}images/icones/no_2.png" />';
				}
			}
		};
	{else}
		{* Bouton devis-perdu *}
		ATF.renderer.devisPerdu=function(table,field) {
			return function(filetype, meta, record, rowIndex, colIndex, store) {
				var id = record.data[table+'__dot__id_'+table];
				if (record.data.devis__dot__etat=="attente"  || record.data.devis__dot__etat=="bloque") {
					return '<a href="javascript:;" onclick="if (confirm(\''+ATF.usr.trans('Etes_vous_sur')+'\')) ATF.tpl2div(\''+table+',perdu.ajax\',\'id_devis='+id+'\');"><img src="{ATF::$staticserver}images/icones/no.png" height="16" width="16" alt="" /></a>';
				} else {
					return '<img src="{ATF::$staticserver}images/icones/no_2.png" />';
				}
			}
		};

	{/if}

	{* Bouton devis-perdu *}
		ATF.renderer.devisAnnule=function(table,field) {
			return function(filetype, meta, record, rowIndex, colIndex, store) {
				var id = record.data[table+'__dot__id_'+table];
				if (record.data.devis__dot__etat=="attente"  || record.data.devis__dot__etat=="bloque") {
					return '<a href="javascript:;" onclick="if (confirm(\''+ATF.usr.trans('Etes_vous_sur')+'\')) ATF.tpl2div(\''+table+',annule.ajax\',\'id_devis='+id+'\');"><img src="{ATF::$staticserver}images/icones/no.png" height="16" width="16" alt="" /></a>';
				} else {
					return '<img src="{ATF::$staticserver}images/icones/no_2.png" />';
				}
			}
		};
	
{/if}
{/strip}