/* Rafraichissement des extJS */
ATF.extRefresh = function (form, action) { 
	var i = $.parseJSON(action.response.responseText); 
	delete i.cadre_refreshed; 
	ATF.ajax_refresh(i); 
	if ((!i.error || !i.error.length) && i.extTpl) {
		for (var t in i.extTpl) {
			Ext.ComponentMgr.get(t).removeAll();
			Ext.ComponentMgr.get(t).add($.parseJSON(i.extTpl[t]));
			Ext.ComponentMgr.get(t).doLayout(true);
		}
	}
};


ATF.createPhotoView = function(obj,essai) {
	var tableau=Array();
	var element23=essai.split('&');

	for(var i=0;i<element23.length;i++){
		var test=element23[i].split('=');
		tableau[test[0]]=test[1];
	}
	var viWindow = new Ext.Window({
		width: 400,
		title: "Choix d'une image",
		items:[
			ATF.basicInfo = new Ext.FormPanel({
				labelWidth: 75
				,height: 300
				,bodyStyle:'padding:5px 5px 0'
				,layout: 'form'
				,items: [{include file="generic-field-textfield.tpl.js"
								fieldLabel='Choisissez'
								name="id_ged"
								id="id_ged"
								key="id_ged"
								condition_field="id_visite"
								condition_value=$smarty.request.id_visite
								current_class=ATF::ged()
								function=autocompleteGedPhoto}]
				,buttons: [{
					text: 'Sauver les modifications',
					handler: function(){
						ATF.basicInfo.getForm().submit({
							method  : 'post',
							waitMsg : 'Insertion...',
							url     : 'extjs.ajax',
							params: {
								'extAction':'vi_pa'
								,'extMethod':'uploadPhoto'
								,'id_vi_pa':tableau['id_vi_pa']
								,'a':tableau['a']
								,'ppa':tableau['ppa']
								,'v':tableau['v']
								,'m':tableau['m']
								,'pa':tableau['pa']
							}
							,success:function(form, action) {
								if(action.result.result){
									eval('__x'+obj.up().up().up().id.replace('_childs','')+'();');
								}else{
									ATF.extRefresh(form,action);
								}
								viWindow.destroy();
							}
						});
					}
				}]
			})
		]
	}).show();
}

ATF.historique = function(params) {
	var rand = Math.floor(Math.random()*10000);
	new Ext.Window({
		width: 400,
		title: "Historique",
		items:[
			new Ext.Panel({
				labelWidth: 75
				,height: 300
				,bodyStyle:'padding:5px 5px 0'
				,layout: 'form'
				,items: [[jQuery.extend(ATF.autocompleteConfig({
					url:'vi_pa,autocompleteHistorique.ajax,'+params
					,mapping:[
						{ name: 'id', mapping: 0 },
						{ name: 'reponse', mapping: 1 },
						{ name: 'nom', mapping: 2, type:'string' },
						{ name: 'date', mapping: 3, type:'string' },
						{ name: 'reponseBrut', mapping: 'raw_1' },
						{ name: 'nomBrut', mapping: 'raw_2' }
					]
					,autoWidth:true
					,loadingText:'Recherche...'
					,template:'<tpl for="."><div class="search-item historique">{literal}<h3>{reponse}<div>{nom}, {date}</div></h3>{/literal}</div></tpl>'
				},true),{ 
					xtype:'combo',
					anchor:'100%'
					,id:'historique'+rand
				})]]
			})
		]
	}).show();
	Ext.ComponentMgr.get('historique'+rand).onTriggerClick();
}
