{$table="facture"}
{$no_update='true'}
{$pager="ProduitsUpdateRepris"}
{$parent_class=$current_class}
{$current_class=ATF::getClass("{$current_class->table}_ligne")}
{$q=ATF::_s(pager)->getAndPrepare($pager)}
{$fields=[
	"{$current_class->table}.produit"
	, "{$current_class->table}.quantite"
	, "{$current_class->table}.ref"
	, "{$current_class->table}.id_fournisseur"
	, "{$current_class->table}.prix_achat"
	, "{$current_class->table}.code"
	, "{$current_class->table}.id_produit"
	, "{$current_class->table}.serial"
	, "{$current_class->table}.afficher"
]}
{if ATF::_r(id_commande)}
	{$q->reset()->addCondition("id_commande",classes::decryptId(ATF::_r(id_commande)))->addCondition("id_affaire_provenance",null,null,false,"IS NOT NULL")->setView([order=>$fields])->end()}
	{include file="produits-update.tpl.js" proxyUrl="commande_ligne,extJSgsa.ajax,pager={$pager}" function="toFactureLigne"}
{elseif ATF::_r(id_facture)}
	{$q->reset()->addCondition("id_facture_commande",classes::decryptId(ATF::_r(id_facture)))->addCondition("id_affaire_provenance",null,null,false,"IS NOT NULL")->setView([order=>$fields])->end()}
	{include file="produits-update.tpl.js"}
{else}
	{$q->reset()->addCondition("id_facture",0)->addCondition("id_affaire_provenance",null,null,false,"IS NOT NULL")->setView([order=>$fields])->end()}
	{include file="produits-update.tpl.js"}
{/if}	