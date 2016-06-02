{strip}
	{
		layout:"fit"
		,id:"id_champs_sup"
		,items:[
			{if $champs_complementaire}
				{foreach from=$champs_complementaire key=champs item=valeur}	
					{if $champs!="filestoattach" && $champs!="__redirect"}
						{$sous=$champs|php:substr:0:3}
						{$nom=$champs|php:substr:3}
						{if $nom=="owner" || $nom=="superieur"}
							{$valeur_bis=ATF::user()->nom($valeur)}
						{elseif $nom=="filiale"}
							{$valeur_bis=ATF::societe()->nom($valeur)}
						{elseif $sous=="id_"}
							{$valeur_bis=ATF::$nom()->nom($valeur)}
						{else}
							{$valeur_bis=$valeur}
						{/if}
						{
							xtype: 'compositefield',
							hideLabel:true,
							msgTarget: 'under',
							autoWidth:true,
							autoHeight:true,
							id:'{$champs}',
							items: [ 
									{ xtype: 'displayfield', value: '{ATF::$usr->trans($champs,$provenance)|escape:javascript}', width:150 }
									,{ xtype: 'displayfield', value: '{$valeur_bis|escape:javascript}', width:200 }
									,{ xtype: 'button', text: 'Supprimer cette donnée'
										,handler:function(b,e){
											Ext.getCmp('id_champs_sup').remove('{$champs}');
										}
									}
									,{ xtype:'hidden', name:"champs_sup[{$provenance}.{$champs}]", value:'{$valeur}' }
							 ]
						}	
						{if !$valeur@last},{/if}
					{/if}
				{/foreach}
			{else}
				{ xtype: 'displayfield', value: 'Aucun champs supplémentaire à attribuer' }
			{/if}
		]
	}
{/strip}