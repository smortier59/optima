{
	xtype: 'superboxselect',
	fieldLabel: '{ATF::$usr->trans($key,$current_class->table)|escape:javascript}',
	name: '{$alternateName|default:$name}[]',
	id: '{$alternateId|default:$id}',
	width: 400,
	height: 200,
	store: [		
			['16', 'Jérome Loison'],
			['17', 'Christophe Loison'],
			['18', 'Pierre Caminel'],
			['93', 'Térence Delattre'],
			['35', 'Frédérique Randoux']		
	]
	,listeners: {
		render : function(){
			{if $requests[$current_class->table][$key]}
				var liste_dest="";
				{foreach from=$requests[$current_class->table][$key] key=cle item=id_user}
					if(liste_dest)liste_dest+=",";
					liste_dest+="{$id_user}";
				{/foreach}
				this.setValue(liste_dest);
			{/if}
		} 
	}
}