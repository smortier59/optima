{$table="facture_fournisseur"}
{$no_update='true'}
{$no_maj='true'}
{$pager="ProduitsUpdate"}
{$parent_class=$current_class}
{$current_class=ATF::getClass("facture_fournisseur_ligne")}
{$q=ATF::_s(pager)->getAndPrepare($pager)}
{$fields=[
	"{$current_class->table}.produit"
	, "{$current_class->table}.quantite"
	, "{$current_class->table}.ref"
	, "{$current_class->table}.prix"
	, "{$current_class->table}.serial"
]}

{if ATF::_r(id_bon_de_commande)}
	{$q->reset()->addCondition("id_bon_de_commande",classes::decryptId(ATF::_r(id_bon_de_commande)))->setView([order=>$fields])->end()}
	{include file="produits-update.tpl.js" proxyUrl="bon_de_commande_ligne,extJSgsa.ajax,pager={$pager}" function="toFacture_fournisseurLigne"}
{else}
	{$q->reset()->addCondition("id_bon_de_commande",0)->setView([order=>$fields])->end()}
	{include file="produits-update.tpl.js"}
{/if}