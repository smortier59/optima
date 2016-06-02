{$pager="{$current_class->table}ProduitsUpdate"}
{$parent_class=$current_class}
{$current_class=ATF::getClass("{$current_class->table}_ligne")}
{$q=ATF::_s(pager)->getAndPrepare($pager)}
{$fields=[
	  "{$current_class->table}.ref"
	, "{$current_class->table}.produit"
	, "{$current_class->table}.quantite"
	, "{$current_class->table}.poids"
	, "{$current_class->table}.prix"
	, "{$current_class->table}.prix_achat"
	, "{$current_class->table}.id_fournisseur"
	, "{$current_class->table}.id_compte_absystech"
	, "{$current_class->table}.periode"
	, "{$current_class->table}.visible"
]}
{if ATF::_r(id_devis)}
	{$q->reset()->where("id_devis",classes::decryptId(ATF::_r(id_devis)))->setView([order=>$fields])->end()}
{else}
	{$q->reset()->where("id_devis",0)->setView([order=>$fields])->end()}
{/if}
{include file="devis-produits-lignes.tpl.js"}