{$pager="{$current_class->table}ProduitsUpdateCnnsommable"}
{$parent_class=$current_class}
{$current_class=ATF::getClass("{$current_class->table}_ligne")}
{$q=ATF::_s(pager)->getAndPrepare($pager)}
{$fields=[
	  "{$current_class->table}.ref"
	, "{$current_class->table}.produit"
	, "{$current_class->table}.quantite"
	, "{$current_class->table}.prix_nb"
	, "{$current_class->table}.prix_couleur"
	, "{$current_class->table}.prix_achat_nb"
	, "{$current_class->table}.prix_achat_couleur"
	, "{$current_class->table}.index_nb"
	, "{$current_class->table}.index_couleur"	
	, "{$current_class->table}.id_fournisseur"
	, "{$current_class->table}.id_compte_absystech"
]}
{if ATF::_r(id_devis)}
	{$q->reset()->where("id_devis",classes::decryptId(ATF::_r(id_devis)))->setView([order=>$fields])->end()}
{else}
	{$q->reset()->where("id_devis",0)->setView([order=>$fields])->end()}
{/if}
{include file="devis-consommables-lignes_consommable.tpl.js"}