{strip}{
	xtype: 'superboxselect',
	{if $nofieldLabel}hideLabel:true,{else}fieldLabel: '{ATF::$usr->trans(tache_user,$current_class->table)|escape:javascript}',{/if}
	name: 'dest[]',
	width: '{$width|default:250}',
	height: {$height|default:150},
	store: [
			{ATF::user()->q->reset()->addCondition(etat,normal)->addOrder("user.nom")->end()}
			{foreach from=ATF::user()->options(null,null,false) key=k item=i}
				['{$k}', '{$i|escape:javascript}']
				{if !$i@last},{/if}
			{/foreach}
			]
	,listeners: {
		render : function(){
			{if $requests[$current_class->table].dest}
				var liste_dest="";
				{foreach from=$requests[$current_class->table].dest key=cle item=id_user}
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
}{/strip}