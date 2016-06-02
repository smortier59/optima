Ext.regModel('Devis', {
    fields: [
		{ name: 'date', type: 'string'},
		{ name: 'user', type: 'string'},
		{ name: 'resume', type: 'string'},
		{ name: 'prix', type: 'int'},
		{ name: 'etat', type: 'string'}
	]
});

Ext.regModel('Hotline', {
    fields: [
		{ name: 'id', type: 'string'},
		{ name: 'date', type: 'string'},
		{ name: 'user', type: 'string'},
		{ name: 'societe', type: 'string'},
		{ name: 'contact', type: 'string'}
	]
});

Ext.regModel('Suivi', {
    fields: [
		{ name: 'date', type: 'string'},
		{ name: 'texte', type: 'string'},
		{ name: 'societe', type: 'string'},
		{ name: 'contact', type: 'string'}
	]
});

var tabpanel = new Ext.TabPanel({
	tabBar: {
		dock: 'bottom',
		layout: {
			pack: 'center'
		}
	},
	fullscreen: true,
	ui: 'light',
	cardSwitchAnimation: {
		type: 'slide',
		cover: true
	},
	defaults: {
		scroll: 'vertical'
	},
	items: [{
		title: 'Accueil',
		html: '<div><img src="{ATF::$staticserver}images/login/logo.png" /><h1>Bonjour {ATF::$usr->get(prenom)} {ATF::$usr->get(nom)},</h1><p>Bienvenue sur la version mobile de votre portail collaboratif <strong>Optima</strong>.</p></div>',
		iconCls: 'accueil', // bookmarks download favorites info more time user team
		cls: 'card1'
	}
	{if ATF::hotline_interaction()}, {
		title: 'Tickets',
		iconCls: 'hotline',
		cls: 'card2',
		
		{$x=ATF::hotline_interaction()->getRecentForMobile(true)}
		{if $x}
			badgeText: '',
		{/if}
		
        layout: Ext.is.Phone ? 'fit' : {
            type: 'vbox',
            align: 'center',
            pack: 'center'
        },
        items: [{
            width: Ext.is.Phone ? undefined : 300,
            height: 500,
            xtype: 'list',
            store: new Ext.data.Store({
				model: 'Devis',
				sorters: {
					property : 'date',
					direction: 'DESC'
				},
				getGroupString : function(record) {
					return record.get('date').substr(0,10);
				},
				data: {ATF::hotline_interaction()->getRecentForMobile()|json_encode}				
			}),
            itemTpl: '{literal}<div>{humanDate} <span style="color:red">#{id}</span><br />{detail}<br /><span style="font-size:smaller">{user}{contact}</span></div>{/literal}',
            grouped: true,
            indexBar: true
        }]
	}{/if}, {
		title: 'Chiffres clés',
		id: 'tab3',
		html: '<h1>Chiffres clés</h1><br />{include file="devis_widget.tpl.htm" assign=w}{$w|escape:javascript}<br />{include file="affaire_widget.tpl.htm" assign=w}{$w|escape:javascript}<br />{include file="facture_widget.tpl.htm" assign=w}{$w|escape:javascript}',
		cls: 'card3',
		iconCls: 'stats'
	}, {
		title: 'Suivis',
		cls: 'card4',
		iconCls: 'suivi',
		
		{$x=ATF::suivi()->getRecentForMobile(true)}
		{if $x}
			badgeText: '',
		{/if}
		
        layout: Ext.is.Phone ? 'fit' : {
            type: 'vbox',
            align: 'center',
            pack: 'center'
        },
        items: [{
            width: Ext.is.Phone ? undefined : 300,
            height: 500,
            xtype: 'list',
            store: new Ext.data.Store({
				model: 'Suivi',
				sorters: {
					property : 'date',
					direction: 'DESC'
				},
				getGroupString : function(record) {
					return record.get('date').substr(0,10);
				},
				data: {ATF::suivi()->getRecentForMobile()|json_encode}				
			}),
            itemTpl: '{literal}<div>{humanDate} <span style="color:red">{societe}</span><br />{texte}<br /><span style="font-size:smaller">{contact}</span><br />{texte}</div>{/literal}',
            grouped: true,
            indexBar: true
        }]
	}, {
		title: 'Devis',
		cls: 'card5',
		iconCls: 'devis',
        layout: Ext.is.Phone ? 'fit' : {
            type: 'vbox',
            align: 'center',
            pack: 'center'
        },
        items: [{
            width: Ext.is.Phone ? undefined : 300,
            height: 500,
            xtype: 'list',
            store: new Ext.data.Store({
				model: 'Devis',
				sorters: {
					property : 'date',
					direction: 'DESC'
				},
				getGroupString : function(record) {
					return record.get('date').substr(0,10);
				},
				data: {ATF::devis()->getRecentForMobile()|json_encode}				
			}),
            itemTpl: '{literal}<div>{humanDate}<br /><span style="color:red">{prix} &euro;</span> {etat}<br />{resume}<br /><span style="font-size:smaller">{user}</span></div>{/literal}',
            grouped: true,
            indexBar: true
        }]
	}]
});

