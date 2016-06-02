{capture assign=speedInsert}{strip}
	<a href="javascript:;" onclick="var w=new Ext.Window({
			layout: 'fit',
			title: '{ATF::$usr->trans(contact)|escape:javascript} | Création',
			width:1000,
			x: 100,
			y: 100,
			id: 'speed_insert{$id}',
			monitorResize:true,
			autoLoad:{ url: '{ATF::contact()->name()},speed_insert_template.ajax,id={$id}&parent_class=suivi_contact{foreach from=ATF::contact()->colonnes.speed_insert key=k_speed item=i_speed}{if ATF::_r($k_speed)}&{$k_speed}={ATF::_r($k_speed)}{/if}{/foreach}', scripts:true }
		}).show();return false;">
		<img src="{ATF::$staticserver}images/icones/insert.png" height="16" width="16" alt="" />
	</a>
{/strip}{/capture}
{
	xtype: 'superboxselect',
	fieldLabel: '{ATF::$usr->trans($key,$current_class->table)|escape:javascript} {$speedInsert|escape:javascript}',
	name: '{$alternateName|default:$name}[]',
	id: '{$alternateId|default:$id}',
	width: 250,
	height: 200,
	mode: 'local',
	store: new Ext.data.Store({
		{$id_societe=$requests[$current_class->name()].id_societe|default:ATF::_r(id_societe)|default:$smarty.session.requests[$current_class->table].id_societe}
		{if !$id_societe && $id_affaire}
			{$id_societe=ATF::affaire()->select($id_affaire,'id_societe')}
		{else if !$id_societe && ATF::_r(id_contact)}
			{$id_societe=ATF::contact()->select(ATF::_r(id_contact),'id_societe')}
		{/if}
		proxy: new Ext.data.HttpProxy({
			url:'contact,autocomplete.ajax,{http_build_query(ATF::suivi()->autocompleteConditions(ATF::contact(),ATF::_r(),"contact.id_societe",$id_societe))}'
			,method:'POST'
		}),
		reader: new Ext.data.JsonReader({
			root: 'result'
			,totalProperty: 'totalCount'
		}, [
			{ name: 'id', mapping: 0 },
			{ name: 'nom', mapping: 1 }
		]),
		autoLoad:true,
		listeners: {
			load : function(){
				{if $requests[$current_class->table][$key]}
					var liste_dest="";
					{foreach from=$requests[$current_class->table][$key] key=cle item=id_contact}
						if(liste_dest)liste_dest+=",";
						liste_dest+="{$id_contact|cryptId}";
					{/foreach}
					Ext.getCmp('suivi[suivi_contact]').setValue(liste_dest);
				{else}
					/* par défaut, on selectionne le user courant */
					{$id_contact=ATF::_r(suivi_contact_id_contact)|default:ATF::_r(id_contact)}
					Ext.getCmp('suivi[suivi_contact]').setValue("{$id_contact|cryptId}");
				{/if}
			} 
		}
	}),
	displayField: 'nom',
	valueField: 'id'
}