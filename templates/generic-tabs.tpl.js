Ext.namespace("Ext.ux");

Ext.onReady(function(){

    // second tabs built from JS
    var tabs = new Ext.TabPanel({
        renderTo: 'dashBoard',
		id:'dashBoardTabs',
        enableTabScroll:true,
//		height:'100%',
//        width:'99%',
        height:{ATF::$usr->get('custom','dashBoard','dashBoard','height')|default:500},
        defaults:{ autoScroll: true },
		plugins: new Ext.ux.TabCloseMenu()

    });
	
    function addTab(t){
		switch (t) {
			case 'config':
				{include file="generic-tabs-conf.tpl.js"}
				var itemsValue = [{  items:[ panelConf ] }];
				tabs.add({
					title: 'Conf',
					id:'conf',
					items:itemsValue,
					closable:false
				}).show();
			break;
			case 'bienvenue':
				tabs.add({
					title: ' Bienvenue '
					,id:'bienvenue'
					,items: [{
						xtype:'panel'
//						,width:'100%'
//						,height:'100%'
						,bodyStyle:'border:0px; padding:10px'
						,html:'{ATF::$html->fetch("bienvenue.tpl.htm")|addslashes}'
						,layout:'fit'
					}]
					,closable:false
				}).show();
			break;
			case 'tache':
				{if ATF::$usr->privilege('tache')}
					{include file="generic-tabs-taches.tpl.js"}
					var itemsValue = [{  items:[ Ext.ComponentMgr.get('gridTache') ] }];
					tabs.add({
						title: ' Tâches '
						,id:'tache'
						,items:itemsValue
						,closable:false
					}).show();
				{/if}
			break;
			case 'hotline':
				{if ATF::$usr->privilege('hotline')}
					{include file="generic-tabs-hotline.tpl.js"}
					var itemsValue = [{  items:[ Ext.ComponentMgr.get('gridHotline') ] }];
					tabs.add({
						title: ' Tickets Hotline '
						,id:'hotline'
						,items:itemsValue
						,closable:false
					}).show();
				{/if}
			break;
			case 'stats':
				{if ATF::$usr->privilege('stats')}
					{include file="generic-tabs-stats.tpl.js"}
					var itemsValue = [{  items:[ Ext.ComponentMgr.get('gridStats') ] }];
					tabs.add({
						title: ' Statistiques '
						,id:'stats'
						,items:itemsValue
						,closable:false
					}).show();
				{/if}
			break;
		}
		
    }

	// Ajout des onglets selectionnés dans la config
	addTab('bienvenue');
	{foreach ATF::$usr->get('custom','dashBoard') as $k=>$i}
		addTab('{$k}');
	{/foreach}
	addTab('config');
	tabs.setActiveTab("{ATF::$usr->get('custom','dashBoard','dashBoard','defaultTab')|default:'bienvenue'}");
	
});

// Traitement special pour les radio afin qu'il gère les [] dans les names 
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
