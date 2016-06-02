{strip}
{if $value!==false}
	{$value=$requests[$current_class->name()][$key]|default:ATF::_r($key)|default:$smarty.session.requests[$current_class->table][$key]|default:$value|default:$current_class->default_value($key,$smarty.session,$smarty.request)|default:$item.default}
{/if}	
{$xtype=$xtype|default:$item.xtype|default:'textfield'}
{if !$requests && $requests!==false}
	{$requests=ATF::_r()}
{/if}
{* gestion des champs obligatoires *}
{if !$est_nul}
	{include file="champ_obligatoire.tpl.htm" assign="img_ob"}
{else}
	{$img_ob=null}
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
					['{$i}', '{ATF::$usr->trans($i,"`$current_class->table`_`$key`",false,true)|default:ATF::preferences()->getNomFromTable($i,$key)|default:ATF::$usr->trans($i,$current_class->table)|escape:javascript}']
					{if !$i@last},{/if}
				{/foreach}
			]
		})
		,value: "{$requests[$current_class->table][$key]|default:$item.default|escape:javascript}"
		,valueField: 'myId'
		,displayField: 'displayText'
		,fieldLabel: '{$fieldLabel|escape:javascript} {$img_ob|escape:javascript}'
		,name: '{$alternateName|default:$name}'
		,id: '{$alternateId|default:$id}'
		,listeners:{
			select:function(obj,record){
				if(record.data.myId=="autre"){
					Ext.getCmp('{$current_class->table}[date_fin]').show();				
				}else{
					Ext.getCmp('{$current_class->table}[date_fin]').hide();				
				}
			}
		}
	}
{/strip}