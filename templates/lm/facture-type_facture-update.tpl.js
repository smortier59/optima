{$affaire=ATF::affaire()->select(ATF::commande()->select($id_commande,"id_affaire"))}


{if $value!==false}
	{$value=$requests[$current_class->name()][$key]|default:ATF::_r($key)|default:$smarty.session.requests[$current_class->table][$key]|default:$value|default:$current_class->default_value($key,$smarty.session,$smarty.request)|default:$item.default}
{/if}
{$xtype=$xtype|default:$item.xtype|default:'textfield'}
{$anchor=$anchor|default:98}
{if $item.autocomplete}
	{$function=$function|default:$item.autocomplete.function|default:autocomplete}
	{$pageSize=$pageSize|default:$item.autocomplete.pageSize}
{else}
	{$function=$function|default:autocomplete}
{/if}


{* gestion des champs obligatoires *}
{if !$est_nul}
	{include file="champ_obligatoire.tpl.htm" assign="img_ob"}
{/if}
{
		xtype:'{$xtype}'
		,typeAhead:true
		,triggerAction:'all'
		,editable:false
		,lazyRender:true
		,mode:'local'
		,preventMark:true
		,hiddenName:'{$name}'{$alternateName="combo`$name`"}{$alternateId="combo`$id`"}
		,store: new Ext.data.ArrayStore({
			fields: [
				'myId',
				'displayText'
			],			
			data: [
				{if $est_nul}
					['', '{$item.libNull|default:"-"}'],
				{/if}
				{foreach from=$item.data item=i}
					['{$i}', '{ATF::$usr->trans($i,"`$current_class->table`_`$key`",false,true)|default:ATF::$usr->trans($i,$current_class->table)|escape:javascript}']
					{if !$i@last},{/if}
				{/foreach}
			]
		})
		,valueField: 'myId'
		,displayField: 'displayText'
		,fieldLabel: '{$fieldLabel|escape:javascript} {$img_ob|escape:javascript}'
		,name: '{$alternateName|default:$name}'
		,id: '{$alternateId|default:$id}'
		,listeners :{
			'select':function(f,n,o){
				if(n.data.myId=='refi'){
					Ext.getCmp('panel_refi').show();
					Ext.getCmp('panel_dates_facture').hide();
					Ext.getCmp('panel_dates_facture_libre').hide();
					Ext.getCmp('combofacture[type_libre]').disable();
					Ext.getCmp('combofacture[redevance]').disable();					
				}else if(n.data.myId=='facture'){										
					Ext.getCmp('panel_refi').hide();
					Ext.getCmp('panel_dates_facture').show();
					Ext.getCmp('panel_dates_facture_libre').hide();
					Ext.getCmp('combofacture[type_libre]').disable();
					Ext.getCmp('combofacture[redevance]').disable();
				}else if(n.data.myId=='libre'){
					Ext.getCmp('panel_refi').hide();
					Ext.getCmp('panel_dates_facture').hide();
					Ext.getCmp('panel_dates_facture_libre').show();
					Ext.getCmp('facture[date_periode_debut_libre]').show();
					Ext.getCmp('facture[date_periode_fin_libre]').show();
					Ext.getCmp('facture[prix_libre]').show();
					Ext.getCmp('combofacture[type_libre]').enable();
					Ext.getCmp('combofacture[redevance]').enable();
				}else if(n.data.myId=='ap'){
					Ext.getCmp('combofacture[type_libre]').disable();					
					Ext.getCmp('combofacture[redevance]').disable();
				}
			}
		}
		{if $emptyText},emptyText:'{$emptyText|escape:javascript}'{/if}
		{if $labelStyle  || $item.labelStyle},labelStyle: '{$labelStyle|default:$item.labelStyle}'{/if}
		{if $originalValue},originalValue:'{$originalValue|escape:javascript}'{/if}
		{if $value || $value==='0' || $alternateValue || $alternateValue==='0'}
			{if $item.formatNumeric}
				{$value_def=$current_class->formatNumeric($value)}
			{else}
				{$value_def=$alternateValue|default:$value|escape:javascript}
			{/if}
			,value:'{$value_def}'
		{/if}
		{if $disabled || $item.disabled},disabled:true{/if}
		{*{if $readonly || $item.readonly || $affaire["nature"]=="vente"},readOnly:true{/if}*}
		{if $readonly || $item.readonly},readOnly:true{/if}
		{if $renderTo  || $item.hidden},renderTo:'{$renderTo|escape:javascript}'{/if}
		{if $anchor || $item.anchor},anchor:'{$anchor|default:$item.anchor}%'{/if}
		{if $style || $item.style},style:'{$style|default:$item.style}'{/if}
		{if $width || $item.width},width:{$width|default:$item.width}{/if}				
		
	}