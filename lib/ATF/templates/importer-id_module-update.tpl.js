{if !$est_nul}
	{include file="champ_obligatoire.tpl.htm" assign="img_ob"}
{/if}
{ 
	xtype: 'combo'
	,triggerAction:'all'
	,mode:'local'
	,editable:false
	,hiddenName:'{$name}'
	,hiddenId:'{$name}'
	,store: new Ext.data.ArrayStore({
		fields: [
			'myId',
			'displayText'
		],			
		data: [
			{foreach from=ATF::module()->options_special('import',1) item=i}
				['{$i}', '{ATF::$usr->trans($i,module)|escape:javascript}']
				{if !$i@last},{/if}
			{/foreach}
		]
	})
	,emptyText:'{ATF::$usr->trans(aucun)|escape:javascript}'
	,valueField: 'myId'
	,displayField: 'displayText'
	,fieldLabel: '{$fieldLabel|escape:javascript} {$img_ob|escape:javascript}' 
	,value: "{$provenance|escape:javascript}"

}