{$cols=ATF::$usr->get('custom','dashBoard','stats','nbCol')|default:3}
{$widgets=ATF::$usr->getWidgets()}

new Ext.Panel({
	id:'gridStats',
	baseCls:'x-plain',
	layout:'table',
	layoutConfig: { columns:{$cols} },
	// applied to child components
	defaults: { frame:true, width:200, height: 280 },
	items:[
		{foreach from=$widgets key=key item=item}
				{
					xtype:'fieldset',
					title: '{ATF::$usr->trans($item.module, intro)|escape:javascript}',
					border: false,
					autoWidth:true,
					items: [
						{
							xtype: 'panel',
							title: '{ATF::$usr->trans(widget,$item.module, $item.type)|escape:javascript}',
							frame: true,
							border: false,
							autoWidth:true,
							autoLoad:{ url: '{$item.module},widget.ajax{if $item.type},type={$item.type}{/if}', scripts:true }
						}
					]
				},
		{/foreach}
   ]
});


