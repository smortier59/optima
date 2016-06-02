{$table="facture"}
{$pager="ProduitsUpdateVisible"}
{$parent_class=$current_class}
{$current_class=ATF::getClass("{$current_class->table}_ligne")}
{$q=ATF::_s(pager)->getAndPrepare($pager)}
{$fields=[
	 "{$current_class->table}.produit"
	, "{$current_class->table}.quantite"
	, "{$current_class->table}.ref"	
	, "{$current_class->table}.prix_achat"	
	, "{$current_class->table}.id_produit_fk"
	
]}

{$q->reset()->setView([order=>$fields])->end()}
{include file="facture-prods-lignes.tpl.js"}
