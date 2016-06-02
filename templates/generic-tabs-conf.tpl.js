{foreach ATF::$usr->get('custom','dashBoard') as $k=>$i}
	{if $k!='dashBoard'}
		{if $str}
			{$str = "{$str},"}
		{/if}
		{$str = "{$str}'{$k}'"}
	{/if}
{/foreach}
{if $str}
	var comboDasboard = [{$str}]; 
{/if}
var storeHotlineFilter = new Ext.data.ArrayStore({
	proxy: new Ext.data.HttpProxy({
		url:'user,AJAXgetFilters.ajax,tableTo=hotline'
		,method:'POST'
	}),
	reader: new Ext.data.ArrayReader({
			root:'result'							 
        	,totalProperty: 'totalCount'
		}, [
	   { name: 'id_filtre', mapping: 0 },
	   { name: 'filtre', mapping: 1 }
	])
})
storeHotlineFilter.load();  



var panelConf = new Ext.FormPanel({
		xtype:'compositefield'
        ,labelWidth: 200 // label settings here cascade unless overridden
        ,url:'usr,preferenceAccueil.ajax'
        ,frame:true
//		,height:470
		,anchor:'100%'
        ,bodyStyle:'padding:5px 5px 0; '
//        ,width: '100%'
        ,items: [{
            xtype:'fieldset'
            ,title: 'Tableau de bord'
			,name:'dashBoard'
			,width: 400
            ,autoHeight:true
            ,defaults: { width: 150 }
            ,defaultType: 'textfield'
            ,items :[{
                    xtype: 'hidden'
                    ,name: 'dashBoard[activate]'
                    ,id: 'dashBoard[activate]'
					,value:true
                }
				,{
                    fieldLabel: 'Hauteur (en px)'
                    ,name: 'dashBoard[height]'
                    ,id: 'dashBoard[height]'
					,value:{ATF::$usr->get('custom','dashBoard','dashBoard','height')|default:500}
                    ,allowBlank:false
                },{
					fieldLabel: 'Accès rapides aux modules',
					xtype: 'radiogroup',
					id:'quickAccessModule',
					defaults: {
						anchor: '100%',
						hideEmptyLabel: false
					},
					items: [
						{ 
							boxLabel: "{ATF::$usr->trans('societe','module')}"
							,name: 'dashBoard[quickAccessModule]' 
							,inputValue: 'societe'
							{if ATF::$usr->get('custom','dashBoard','dashBoard','quickAccessModule')}
								,checked:true
							{/if} 
						},
						{ 
							boxLabel: "{ATF::$usr->trans('suivi','module')}"
							,name: 'dashBoard[quickAccessModule]' 
							,inputValue: 'suivi'
							{if ATF::$usr->get('custom','dashBoard','dashBoard','quickAccessModule')}
								,checked:true
							{/if} 
						},
						{ 
							boxLabel: "{ATF::$usr->trans('affaire','module')}"
							,name: 'dashBoard[quickAccessModule]' 
							,inputValue: 'affaire'
							{if ATF::$usr->get('custom','dashBoard','dashBoard','quickAccessModule')}
								,checked:true
							{/if} 
						},
						{ 
							boxLabel: "{ATF::$usr->trans('hotline','module')}"
							,name: 'dashBoard[quickAccessModule]' 
							,inputValue: 'hotline'
							{if ATF::$usr->get('custom','dashBoard','dashBoard','quickAccessModule')}
								,checked:true
							{/if} 
						},
						{ 
							boxLabel: "{ATF::$usr->trans('administration','module')}"
							,name: 'dashBoard[quickAccessModule]' 
							,inputValue: 'administration'
							{if ATF::$usr->get('custom','dashBoard','dashBoard','quickAccessModule')}
								,checked:true
							{/if} 
						}
					]
				}
				// Tester l'existence d'une tab autre que dashboard
				{if $str}
					,{
						fieldLabel: 'Onglet selectionné par défaut'
						,name: 'dashBoard[defaultTab]'
						,id: 'dashBoard[defaultTab]'
						,xtype:'combo'
						,store: comboDasboard
					}
				{/if}
				
            ]
        }
		{if ATF::$usr->privilege('tache')}
			,{
				xtype:'fieldset'
				,checkboxToggle:true
				,title: 'Tâches'
				,name:'tache[activate]'
				,id:'tache[activate]'
				,width: 400
				,autoHeight:true
				,defaults: { width: 150 }
				,defaultType: 'textfield'
				{if ATF::$usr->get('custom','dashBoard','tache','activate')=='on'}
					,collapsed: false
				{else}
					,collapsed: true
				{/if}
				
				,items :[{
						fieldLabel: 'Afficher les tâches en retard',
						xtype: 'radiogroup',
						id:'late',
						items: [
							{ 
								boxLabel: 'Oui' 
								,name: 'tache[late]' 
								,inputValue: 1 
								{if ATF::$usr->get('custom','dashBoard','tache','late')}
									,checked:true
								{/if} 
							},
							{ 
								boxLabel: 'Non'
								,name: 'tache[late]' 
								,inputValue: 0 
								{if !ATF::$usr->get('custom','dashBoard','tache','late')}
									,checked:true
								{/if} 
							}
						]
					},{
						fieldLabel: 'Nombre de jours'
						,name: 'tache[nbJour]'
						,id: 'tache[nbJour]'
						,value:{ATF::$usr->get('custom','dashBoard','tache','nbJour')|default:5}
						,allowBlank:false
					},{
						fieldLabel: 'Afficher le calendrier',
						name: 'calendar',
						xtype: 'radiogroup',
						items: [
							{if ATF::$usr->get('custom','dashBoard','tache','calendar')===0}
								{ boxLabel: 'Oui', name: 'tache[calendar]', inputValue: 1  },
								{ boxLabel: 'Non', name: 'tache[calendar]', inputValue: 0 , checked:true }
							{else if ATF::$usr->get('custom','dashBoard','tache','calendar')===1}
								{ boxLabel: 'Oui', name: 'tache[calendar]', inputValue: 1 , checked:true },
								{ boxLabel: 'Non', name: 'tache[calendar]', inputValue: 0 }
							{else}
								{ boxLabel: 'Oui', name: 'tache[calendar]', inputValue: 1 , checked:true },
								{ boxLabel: 'Non', name: 'tache[calendar]', inputValue: 0 }
							{/if}
						]
					}
				]
			}
		{/if}
		{if ATF::$usr->privilege('hotline')}
			,{
				xtype:'fieldset'
				,checkboxToggle:true
				,title: 'hotline'
				,id:'hotline[activate]'
				,name:'hotline[activate]'
				,width: 400
				,autoHeight:true
				,defaults: { width: 150 }
				,defaultType: 'textfield'
				{if ATF::$usr->get('custom','dashBoard','hotline','activate')=='on'}
					,collapsed: false
				{else}
					,collapsed: true
				{/if}
				,items :[{
						fieldLabel: 'Filtre par défaut'
						,name: 'hotline[defaultFilter]'
						,id: 'hotline[defaultTab]'
						,xtype:'combo'
						,store: storeHotlineFilter
						,displayField:'filtre'
						,valueField:'id_filtre'
				}]
			}
		{/if}
		,{
			xtype:'fieldset'
			,checkboxToggle:true
			,title: 'Statistiques'
			,id:'stats[activate]'
			,name:'stats[activate]'
			,width: 400
			,autoHeight:true
			,defaults: { width: 150 }
			,defaultType: 'textfield'
			{if ATF::$usr->get('custom','dashBoard','stats','activate')=='on'}
				,collapsed: false
			{else}
				,collapsed: true
			{/if}
			,items: [
				{
					fieldLabel: 'Nombre de colonnes'
					,name: 'stats[nbCol]'
					,id: 'stats[nbCol]'
					,value:{ATF::$usr->get('custom','dashBoard','stats','nbCol')|default:3}
					,allowBlank:false
				}		 
			]
		}
		],

		buttonAlign:'left',
        buttons: [{
            text: '{ATF::$usr->trans(save)}',
            handler: function() {
				panelConf.getForm().submit({
					method  : 'post',
					waitMsg : 'Mise à jour...',
					url : 'extjs.ajax',
					params: {
						'extAction':'user'
						,'extMethod':'preferenceAccueil'
					}
					,success:function(form, action) {
						ATF.log(form);
						ATF.log(action);
						//if(action.result.result){
//							ATF.unsetFormIsActive();
//							window.location='{$current_class->table}-select-{$key}-'+action.result.result+'.temp'; 
//							ATF.setFormIsActive();
//						}else if(action.result.cadre_refreshed){
//							ATF.ajax_refresh(action.result,true);
//						}else{
//							ATF.extRefresh(form,action); 
//						}
						//ATF.tpl2div('refresh.ajax','div=main&template=accueil');
					}
					,onFailure:function(error) {
						console.log("YALLAH".error);	
					}
				});
			}
        },{
            text: '{ATF::$usr->trans(cancel)}'
        }]
    });
	
	
/* Gestion des crochets [] dans les noms pour les radios */
{literal}
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
{/literal}

