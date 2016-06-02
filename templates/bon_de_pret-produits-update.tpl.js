{$pager="{$current_class->table}ProduitsUpdate"}
{$parent_class=$current_class}
{$current_class=ATF::getClass("bon_de_pret_ligne")}
{$q=ATF::_s(pager)->getAndPrepare($pager)}
{$fields=[
		"{$current_class->table}.id_stock"
	    ,"{$current_class->table}.ref"
		, "{$current_class->table}.id_stock_fk"
		, "{$current_class->table}.serial"
		, "{$current_class->table}.serialAT"
]}
{if ATF::_r(id_affaire)}
	{$q->reset()->where("stock.id_affaire",classes::decryptId(ATF::_r(id_affaire)))->setView([order=>$fields])->end()}
	{include file="bon_de_pret-produits-lignes.tpl.js" proxyUrl="stock,extJSgsa.ajax,pager={$pager}" function="toStock"}
{else}
	{$q->reset()->where("stock.id_affaire",0)->setView([order=>$fields])->end()}
	{include file="bon_de_pret-produits-lignes.tpl.js"}
{/if}	