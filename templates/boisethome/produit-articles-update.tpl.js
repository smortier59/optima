{$pager="{$current_class->table}ArticlesUpdate"}
{$parent_class=$current_class}
{$current_class=ATF::getClass("{$current_class->table}_article")}
{$q=ATF::_s(pager)->getAndPrepare($pager)}
{$fields=[
	"{$current_class->table}.article",
	"{$current_class->table}.quantite",
	"prix_achat",
	"{$current_class->table}.id_article"
]}
{if ATF::_r(id_produit)}
	{$q->reset()->where("id_produit",classes::decryptId(ATF::_r(id_produit)))->setView([order=>$fields])->end()}
{else}
	{$q->reset()->where("id_produit",0)->setView([order=>$fields])->end()}
{/if}
{include file="produit-articles-lignes.tpl.js"}