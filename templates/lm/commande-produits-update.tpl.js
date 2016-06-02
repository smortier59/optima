{$table="commande"}
{$no_update='true'}
{$pager="ProduitsUpdateVisible"}
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
	, "{$current_class->table}.commentaire"
]}
{if ATF::_r(id_devis)}
	{$calcul_prix='true'}
	{$q->reset()->addCondition("id_devis",classes::decryptId(ATF::_r(id_devis)))->addCondition("id_affaire_provenance",null,null,false,"IS NULL")->addCondition("visible","oui","AND")->setView([order=>$fields])->end()}
	{include file="produits-update.tpl.js" proxyUrl="devis_ligne,extJSgsa.ajax,pager={$pager}" function="toCommandeLigne"}
{elseif ATF::_r(id_commande)}
	{$calcul_prix='true'}
	{$q->reset()->addCondition("id_commande",classes::decryptId(ATF::_r(id_commande)))->addCondition("id_affaire_provenance",null,null,false,"IS NULL")->addCondition("visible","oui","AND")->setView([order=>$fields])->end()}
	{include file="produits-update.tpl.js"}
{else}
	{$q->reset()->addCondition("id_commande",0)->addCondition("visible","oui","AND")->addCondition("id_affaire_provenance",null,null,false,"IS NULL")->setView([order=>$fields])->end()}
	{include file="produits-update.tpl.js"}
{/if}