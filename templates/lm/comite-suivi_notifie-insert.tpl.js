{
	xtype: 'superboxselect',
	fieldLabel: '{ATF::$usr->trans($key,$current_class->table)|escape:javascript}',
	name: '{$alternateName|default:$name}[]',
	id: '{$alternateId|default:$id}',
	width: 400,
	height: 200, 
	store: [		
			['16', 'Jérome Loison'],
			['18', 'Benjamin Tronquit']
	]	
	,listeners: {
		render : function(){
			{if ATF::comite()->default_value("suivi_notifie")}
				var liste_dest="";
				{foreach from=ATF::comite()->default_value("suivi_notifie") key=cle item=id_user}
					if(liste_dest)liste_dest+=",";
					liste_dest+="{$id_user}";
				{/foreach}
				this.setValue(liste_dest);
			{/if}
		}
	}
}