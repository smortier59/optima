{$pager="ProduitsUpdateNonVisible"}
{$parent_class=$current_class}
{$current_class=ATF::getClass("{$current_class->table}_ligne")}
{$q=ATF::_s(pager)->getAndPrepare($pager)}
{$fields=[
	  "{$current_class->table}.produit"
	, "{$current_class->table}.quantite"
	, "{$current_class->table}.type"
	, "{$current_class->table}.ref"
	, "{$current_class->table}.prix_achat"
	, "{$current_class->table}.code"
	, "{$current_class->table}.id_produit"
	, "{$current_class->table}.id_fournisseur"
	, "{$current_class->table}.visibilite_prix"
	, "{$current_class->table}.date_achat"
	, "{$current_class->table}.commentaire"
	, "{$current_class->table}.ordre"
]}
{if ATF::_r(id_pack_produit)}
	{$q->reset()->addCondition("pack_produit_ligne.id_pack_produit",classes::decryptId(ATF::_r(id_pack_produit)))->addCondition("visible","non","AND")->setView([order=>$fields],true)->end()}
{else}
	{$q->reset()->addCondition("pack_produit_ligne.id_pack_produit",0)->setView([order=>$fields],true)->end()}
{/if}
{include file="produits-update.tpl.js"}