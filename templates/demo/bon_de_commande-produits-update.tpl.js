{$pager="{$current_class->table}ProduitsUpdate"}
{$parent_class=$current_class}
{$current_class=ATF::getClass("{$current_class->table}_ligne")}
{$q=ATF::_s(pager)->getAndPrepare($pager)}
{$fields=util::keysOrValues($current_class->colonnes.ligne)}
{$q->reset()->setView([order=>$fields])->end()}
{if ATF::_r(id_commande)}
	{include file="bon_de_commande-produits-lignes.tpl.js" proxyUrl="commande_ligne,selectOnlyNotYetOrderedQuantities.ajax" baseParams=[id_commande=>ATF::_r(id_commande)]}
{elseif ATF::_r(id_bon_de_commande)}
	{include file="bon_de_commande-produits-lignes.tpl.js" proxyUrl="{$current_class->table},extJSgsa.ajax,pager={$pager}}" function="BonCommandeLigne"}
{else}
	{*$q->reset()->where("id_commande",0)->setView([order=>$fields])}
	{include file="bon_de_commande-produits-lignes.tpl.js"*}
{/if}	