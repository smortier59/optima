{  
	xtype: 'panel',
	anchor: "100%",
	id:'panelCaro',
	border: true,
	style: {
		backgroundColor: '#fff',
		padding: '0px 25px 10px 10px',
	},
	items: [
		{foreach $current_class->dynamicPicture(ATF::_r('id_hotline')) as $k=>$i}
			
			{
				xtype: 'panel',
				html:'<img src="{$i.URL}" alt="{$i.name|escape:javascript}" title="{$i.name|escape:javascript}" style="height:50px">',
				border: false,
				style : {
					float:'left',
					margin:'10px 0px 10px 10px',
					cursor:'pointer',
				},
			    listeners: {
				   'render': function(panel) {
				       	panel.body.on('click', function() {
				       		{if $i.URLHD}
								if (ATF.currentWindow) ATF.currentWindow.destroy();
						           	ATF.currentWindow = new Ext.Window( {
						           		html:'<img src="{$i.URLHD}">',
						           		id:'tot',
						           		draggable: false,
						           		layout:'fit',
						           		style : {
											width:'700px',
										},
						           	});
					        	ATF.currentWindow.show();
						        ATF.currentWindow.center();

				           	{else if $i.URLDL}
				           		window.open("{$i.URLDL}");

				       		{/if}
				       	});
				    },
				},
			},
		{/foreach}
	],
},
