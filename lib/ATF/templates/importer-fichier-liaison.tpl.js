[{
 	"xtype":"hidden"
	,"name": "nom_fichier"
	,"value":"{$filename}"
 },{
	"xtype":"compositefield"
	,"msgTarget": "under"
	,"style":{ "marginTop": "30px","marginBottom": "15px" }
	,"items": [{
			"xtype":"fieldset"
			,"width":575
			,"items": [
					{ "xtype": "displayfield","hideLabel":true, "value": "Fichier source", "style":"font-weight:bold;text-align:center;" }
			]
		},{
			"xtype":"fieldset"
			,"width":250
			,"items": [
					{ "xtype": "displayfield","hideLabel":true, "value": "Module", "style":"font-weight:bold;text-align:center;" }
			]
		}]
},{
	"xtype":"compositefield"
	,"hideLabel":true
	,"msgTarget": "under"
	,"style":{ "marginTop": "15px","marginBottom": "15px" }
	,"items": [
		{ "xtype": "displayfield", "value": "Nom de la colonne du fichier source", "width":250, "style":"font-weight:bold" }
		,{ "xtype": "displayfield", "value": "Exemples du contenu de la colonne", "width":325, "style":"font-weight:bold" }
		,{ "xtype": "displayfield", "value": "Colonnes du module pour la liaison", "width":250, "style":"font-weight:bold" }
	]
}
{foreach from=$mapping key=key item=item}
	{$value=""}
	,{
		"xtype":"compositefield"
		,"hideLabel":true
		,"msgTarget": "under"
		,"items": [
			{ "xtype": "displayfield", "value": "{$item.title|escape:javascript}", "width":250}
			,{ "xtype": "displayfield", "value": "{$item.samples|escape:javascript}", "width":325}
			,{
				"xtype": "combo"
				,"triggerAction":"all"
				,"hideLabel":true
				,"mode":"local"
				,"editable":false
				,"hiddenName":"liaison[{$key}]"
				,"width":250
				,"store": new Ext.data.ArrayStore({
					"fields": [
						"myId",
						"displayText"
					],
					"data": [
						["", "-"],
						{foreach from=$colonnes item=i}
							{if $item.title==ATF::$usr->trans($i,$nom_module)}{$value=$i}{/if}
							["{$i}", "{ATF::$usr->trans($i,$nom_module)|escape:javascript}"]
							{if !$i@last},{/if}
						{/foreach}
					]
				})
				,"value": "{$value}"
				,"emptyText":"-"
				,"valueField": "myId"
				,"displayField": "displayText"
			}
		]
	}
{/foreach}
]