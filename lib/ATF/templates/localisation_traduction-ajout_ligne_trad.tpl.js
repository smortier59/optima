{
	xtype: 'compositefield',
	hideLabel:true,
	msgTarget: 'under',
	height:30,
	items: [
			{ xtype: 'panel',
				items: [{include file="generic-field-textfield.tpl.js"
						name="{$ident}[localisation]"
						key='id_localisation'
						id="localisation{$ident}"
						width=300
						current_class=ATF::localisation_traduction()}] 
			}
			,{ xtype: 'textfield', name: '{$ident}[expression]', width:300 }
			,{ xtype: 'panel',
				items: [{include file="generic-field-textfield.tpl.js"
						name="{$ident}[codename]"
						id="codename{$ident}"
						width=100
						est_nul=true
						xtype='combo'
						item=['data'=>ATF::db()->recup_codename()]
						current_class=ATF::localisation()}] 
			}
	]	
}