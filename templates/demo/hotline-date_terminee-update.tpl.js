{strip}
	{*
	@string $xtype Type extJS
	*}
	{if $value!==false}
		{$value=$requests[$current_class->name()][$key]|default:$smarty.request[$key]|default:$smarty.session.requests[$current_class->table][$key]|default:$value|default:$current_class->default_value($key,$smarty.session,$smarty.request)|default:$item.default}
	{/if}	
	{$xtype=$xtype|default:$item.xtype|default:'textfield'}

	{if $smarty.get.event=='insert' || $smarty.get.event=="speedInsert"}
		{$readonly = "false"} 
	{else}
		{$readonly = "true"}
	{/if}

	{* gestion des champs obligatoires *}
	{if !$est_nul}
		{include file="champ_obligatoire.tpl.htm" assign="img_ob"}
	{/if}
	{
		fieldLabel: '{ATF::$usr->trans("add_deadLine","hotline")|escape:javascript}',
		xtype:'checkboxgroup',
		id:'choix_deadline',
		readOnly: {$readonly},
		listeners:{
			change:function(radiogroup,item){
				if(Ext.getCmp('hotline[date_terminee]').hidden==true){
					Ext.getCmp('hotline[date_terminee]').enable();
					Ext.getCmp('hotline[date_terminee]').show();
				}else{
					Ext.getCmp('hotline[date_terminee]').hide();
					Ext.getCmp('hotline[date_terminee]').disable();
				}
			}
		},items: [
			{
				id:'check_choix_deadLine',
				checked:{if $value}true{else}false{/if},
				name:'check_choix_deadLine'
			}
		],
		{if $value}checked: true{/if}
	},
	{
		xtype:'datefield',
		fieldLabel: '{ATF::$usr->trans("date_terminee","hotline")|escape:javascript}',
		id: 'hotline[date_terminee]',
		readOnly: {$readonly},
		format:'d-m-Y',
		value:'{if $value}{$value|date_format:"%d-%m-%Y"}{/if}',
		width:125,
		listeners:{
			afterrender:function(){
				{if !$value}this.hide();{/if}
			}
		}
	}
{/strip}