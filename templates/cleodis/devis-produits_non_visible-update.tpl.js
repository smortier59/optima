{$pager="ProduitsUpdateNonVisible"}
{$id_parent="produits"}
{$parent_class=$current_class}
{$current_class=ATF::getClass("{$current_class->table}_ligne")}
{$q=ATF::_s(pager)->getAndPrepare($pager)}
{$fields=[
	"{$current_class->table}.produit"
	, "{$current_class->table}.caracteristique"
	, "{$current_class->table}.quantite"
	, "{$current_class->table}.type"
	, "{$current_class->table}.ref"
	, "{$current_class->table}.prix_achat"
	, "{$current_class->table}.code"
	, "{$current_class->table}.id_produit"
	, "{$current_class->table}.id_fournisseur"
	, "{$current_class->table}.visibilite_prix"
]}
{if ATF::_r(id_devis)}
	{$calcul_prix='true'}
	{$q->reset()->addCondition("id_devis",classes::decryptId(ATF::_r(id_devis)))->addCondition("id_affaire_provenance",null,null,false,"IS NULL")->addCondition("visible","non","AND")->setView([order=>$fields],true)->end()}
{else}
	{$q->reset()->addCondition("id_devis",0)->addCondition("visible","non","AND")->addCondition("id_affaire_provenance",null,null,false,"IS NULL")->setView([order=>$fields],true)->end()}
{/if}
{include file="produits-update.tpl.js"}