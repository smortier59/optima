var form;

Ext.regModel('User', {
    fields: [
        { name: 'schema', type: 'string'},
        { name: 'login', type: 'string'},
        { name: 'password', type: 'password'}
    ]
});
var formBase = {
    scroll: 'vertical',
    standardSubmit : false,
	url : 'extjs.ajax',
    items: [
		{
			html: '<div style="text-align:right; width:100%;"><img src="{ATF::$staticserver}images/logos/optima.png" /></div>'
		},{
            xtype: 'fieldset',
            title: 'Identifiation à Optima',
            instructions: 'Merci de remplir les informations ci-dessus.',
            defaults: {
                required: true,
                labelAlign: 'left',
                labelWidth: '40%'
            },
            items: [{
                xtype: 'textfield',
                name : 'schema',
                label: 'Société',
                useClearIcon: true,
                autoCapitalize : false
            }, {
                xtype: 'textfield',
                name : 'login',
                label: 'Utilisateur',
                useClearIcon: true,
                autoCapitalize : false
            }, {
                xtype: 'passwordfield',
                name : 'password',
                id : 'password',
                label: 'Code ',
                useClearIcon: false
            }]
        }
    ],
    listeners : {
        submit : function(form, result){
            window.location='accueil.html';
        },
        exception : function(form, result){
            //ATF.log('failure', Ext.toArray(arguments));
        }
    },

    dockedItems: [
        {
            xtype: 'toolbar',
            dock: 'bottom',
            items: [
                {
                    text: 'Reset',
                    handler: function() {
                        form.reset();
                    }
                },
                { xtype: 'spacer' },
                {
                    text: 'Valider',
                    ui: 'confirm',
                    handler: function() {
                        var seed=hex_md5('{$smarty.now}');
                        var p = Ext.getCmp('password').getValue();
                        Ext.getCmp('password').setValue(hex_md5(hex_md5(Ext.getCmp('password').getValue())+seed));
                        form.submit({
                            waitMsg : { message:'Envoi en cours', cls : 'demos-loading' }
							,method:'POST'
                            ,params:{ 
								seed:seed 
								,'extAction':'usr'
								,'extMethod':'login'
							}
                        });
                        Ext.getCmp('password').setValue(p);
                    }
                }
            ]
        }
    ]
};

if (Ext.is.Phone) {
    formBase.fullscreen = true;
} else {
    Ext.apply(formBase, {
        autoRender: true,
        floating: true,
        modal: true,
        centered: true,
        hideOnMaskTap: false,
        height: 385,
        width: 480
    });
}

form = new Ext.form.FormPanel(formBase);
form.show();
