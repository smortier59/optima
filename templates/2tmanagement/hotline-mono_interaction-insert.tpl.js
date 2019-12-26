{strip}
{*
@string $xtype Type extJS
*}
{if $value!==false}
	{$value=$requests[$current_class->name()][$key]|default:$smarty.request[$key]|default:$smarty.session.requests[$current_class->table][$key]|default:$value|default:$current_class->default_value($key,$smarty.session,$smarty.request)|default:$item.default}
{/if}	
{$xtype=$xtype|default:$item.xtype|default:'textfield'}

{* gestion des champs obligatoires *}
{if !$est_nul}
	{include file="champ_obligatoire.tpl.htm" assign="img_ob"}
{/if}
	{
		fieldLabel: '{$fieldLabel|escape:javascript} {$img_ob|escape:javascript}',
		xtype:'checkboxgroup',
		listeners:{
			change:function(radiogroup,item){
				if(Ext.getCmp('hotline[temps_mono_interaction]').hidden==true){
					Ext.getCmp('hotline[temps_mono_interaction]').enable();
					Ext.getCmp('hotline[temps_mono_interaction]').show();
				}else{
					Ext.getCmp('hotline[temps_mono_interaction]').hide();
					Ext.getCmp('hotline[temps_mono_interaction]').disable();
				}
			}
		},items: [
			{
				id:'hotline[mono_interaction]',
				checked:false,
				name:'hotline[mono_interaction]'
			}
		]
	},
	{
		xtype:'timefield',
		fieldLabel: '{ATF::$usr->trans("mono_interaction_temps_passe","hotline")|escape:javascript} {$img_ob|escape:javascript}',
		id: 'hotline[temps_mono_interaction]',
		minValue: '0:00',
		maxValue: '23:59',
		increment: 15,
		width:65,
		format:'H:i',
		value:'00:00',
		listeners:{
			afterrender:function(){
					this.hide();
			}
		}
	}
{/strip}