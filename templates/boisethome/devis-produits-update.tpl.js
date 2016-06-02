{$pager="{$current_class->table}ProduitsUpdate"}
{$parent_class=$current_class}
{$current_class=ATF::getClass("devis_lot_produit")}
{$q=ATF::_s(pager)->getAndPrepare($pager)}
{$fields=[
	"{$current_class->table}.produit"
	, "{$current_class->table}.quantite"
	, "{$current_class->table}.unite"
	, "{$current_class->table}.description"
	, "{$current_class->table}.lambda"
	, "{$current_class->table}.id_produit"
	, "{$current_class->table}.id_devis_lot"
	, "{$current_class->table}.id_devis_lot_produit"
]}
{$q->reset()->setView([order=>$fields])->end()}
{include file="devis-produits.tpl.js" proxyUrl="devis_lot_produit,selectFromDevis.ajax" baseParams=[id_devis=>ATF::_r(id_devis)]}