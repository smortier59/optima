{
	xtype: 'superboxselect',
	fieldLabel: '{ATF::$usr->trans($key,$current_class->table)|escape:javascript}',
	name: '{$alternateName|default:$name}[]',
	id: '{$alternateId|default:$id}',
	width: 250,
	height: 200,
	store: [
		{ATF::user()->q->reset()->addCondition(etat,normal)->addCondition("login","absystech","OR",false,"!=")->addOrder("user.nom")->end()}
		{foreach from=ATF::user()->options(null,null,false) key=k item=i}
			['{$k}', '{$i|escape:javascript}']
			{if !$i@last},{/if}
		{/foreach}
	]
	,listeners: {
		render : function(){
			{if $requests.id_tache}
				var liste_dest="";
				{$list=ATF::tache()->select($requests.id_tache)}
				{foreach from=$list['dest'] key=cle item=id_user}
					if(liste_dest)liste_dest+=",";
					liste_dest+="{$id_user}";
				{/foreach}
				this.setValue(liste_dest);
			{elseif $requests[$current_class->table][$key]}
				var liste_dest="";
				{foreach from=$requests[$current_class->table][$key] key=cle item=id_user}
					if(liste_dest)liste_dest+=",";
					liste_dest+="{$id_user}";
				{/foreach}
				this.setValue(liste_dest);
			{else}
				/* par défaut, on selectionne le user courant */
				this.setValue("{ATF::$usr->getID()}");
			{/if}
		} 
	}
}