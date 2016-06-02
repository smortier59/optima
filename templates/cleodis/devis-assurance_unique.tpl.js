
{*
@param int $anchor 
@param int $pageSize 0 Pour ne pas avoir de pagination dans un combo autocomplete
@param string $function Nom de la fonction à utiliser en AJAX pour le combo autocomplete
@param boolean $noHiddenField Ne pas ajouter de champ hidden automatiquement dans un combo autocomplete
@param boolean $always_display Affiche la combo même s''il n'y a pas d'éléments.
@param string $xtype Type extJS
@param string $requests Informations sur un enregistrement de référence
*}
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
	,decimalPrecision:20
	,fieldLabel: '{$fieldLabel|escape:javascript} {$img_ob|escape:javascript}'
	,name: '{$alternateName|default:$name}'
	,id: '{$alternateId|default:$id}'
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
	{if $readonly || $item.readonly},readOnly:true{/if}
	,listeners :{
		change:function(f,n,o){
			var prix=parseFloat(n)+parseFloat(Ext.ComponentMgr.get('devis[loyers]').getValue())+parseFloat(Ext.ComponentMgr.get('devis[frais_de_gestion_unique]').getValue());
			Ext.ComponentMgr.get('devis[prix]').setValue(ATF.formatNumeric(prix));
			Ext.ComponentMgr.get('devis[marge]').setValue(ATF.formatNumeric(parseFloat((prix-parseFloat(Ext.ComponentMgr.get('devis[prix_achat]').getValue().replace(' ','')))/prix)*100));
			Ext.ComponentMgr.get('devis[marge_absolue]').setValue(ATF.formatNumeric(parseFloat(prix-parseFloat(Ext.ComponentMgr.get('devis[prix_achat]').getValue().replace(' ','')))));
		}
	}
	{if $anchor || $item.anchor},anchor:'{$anchor|default:$item.anchor}%'{/if}
	{if $style || $item.style},style:'{$style|default:$item.style}'{/if}
	{if $width || $item.width},width:{$width|default:$item.width}{/if}
}

