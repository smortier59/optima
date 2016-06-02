{$pager="{$current_class->table}ProduitsArticleUpdate"}
{$parent_class=$current_class}
{$current_class=ATF::getClass("{$current_class->table}_lot_produit_article")}
{$q=ATF::_s(pager)->getAndPrepare($pager)}
{$fields=[
	"{$current_class->table}.quantite"
	, "{$current_class->table}.article"
	, "{$current_class->table}.unite"
	, "{$current_class->table}.id_fournisseur"
	, "{$current_class->table}.prix_achat"
	, "{$current_class->table}.conditionnement"
	, "{$current_class->table}.id_marge"
	, "{$current_class->table}.visible"
	, "{$current_class->table}.id_article"
	, "{$current_class->table}.id_devis_lot_produit"
]}
{$q->reset()->setView([order=>$fields])->end()}
{include file="devis-articles.tpl.js" proxyUrl="devis_lot_produit_article,selectFromDevis.ajax" baseParams=[id_devis=>ATF::_r(id_devis)]}