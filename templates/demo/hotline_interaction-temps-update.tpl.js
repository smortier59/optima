{if $value!==false}
	{$value=$requests[$current_class->name()][$key]|default:ATF::_r($key)|default:$smarty.session.requests[$current_class->table][$key]|default:$value|default:$current_class->default_value($key,$smarty.session,$smarty.request)|default:$item.default}
{/if}	
{$xtype=$xtype|default:$item.xtype|default:'textfield'}
{$anchor=$anchor|default:98}
{$etat = ATF::hotline()->select({$requests['id_hotline']|default:$requests['hotline_interaction']['id_hotline']},'etat')}
{$facturation_ticket = ATF::hotline()->select({$requests['id_hotline']|default:$requests['hotline_interaction']['id_hotline']},'facturation_ticket')}
{* gestion des champs obligatoires *}
{if !$est_nul}
	{include file="champ_obligatoire.tpl.htm" assign="img_ob"}
{/if}
{$alternateName="date`$name`"}{$alternateId="date`$id`"}
{
	xtype: 'compositefield'
	,fieldLabel: '{$fieldLabel|escape:javascript} {$img_ob|escape:javascript}'
	,items: [{	
			xtype:'timefield'
			,name: '{$alternateName}_time'
			,id: '{$alternateId}_time'
			,value:'{$value|default:$smarty.now|date_format:"%H:%M"}'
			,format:'H:i'
			,minValue: '0:00'
			,maxValue: '23:59'
			,increment: 15
			,width:65
			,listeners :{
				change:function(){
					$('#{$id|replace:"[":"\\\\["|replace:"]":"\\\\]"}').val($('#{$alternateId|replace:"[":"\\\\["|replace:"]":"\\\\]"}_time').val());
				}
			}
			,{if $etat=="done" || $etat=="payee" || $etat=="annulee" || $facturation_ticket=="non"}disabled:true{/if}
	}]
},{
	xtype:'hidden'
	,name: '{$name}'
	,id: '{$id}'
	,value:'{$value|default:$smarty.now|date_format:"%H:%M"}'
}