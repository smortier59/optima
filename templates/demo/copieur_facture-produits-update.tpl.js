{$pager="{$current_class->table}ProduitsUpdate"}
{$parent_class=$current_class}
{$current_class=ATF::getClass("{$current_class->table}_ligne")}
{$q=ATF::_s(pager)->getAndPrepare($pager)}
{$fields=[
	"{$current_class->table}.type"
	, "{$current_class->table}.designation"
	, "{$current_class->table}.quantite"
	, "{$current_class->table}.prix_achatNB"
	, "{$current_class->table}.prix_achatC"
	, "{$current_class->table}.prixNB"
	, "{$current_class->table}.prixC"
]}
{if ATF::_r(id_copieur_contrat)}
	{$q->reset()->where("id_copieur_contrat",classes::decryptId(ATF::_r(id_copieur_contrat)))->setView([order=>$fields])->end()}
	{include file="copieur_contrat-produits-lignes.tpl.js" proxyUrl="copieur_contrat_ligne,extJSgsa.ajax" function="toFactureLigne"}
{elseif ATF::_r(id_copieur_facture)}
	{$q->reset()->where("id_copieur_facture",classes::decryptId(ATF::_r(id_copieur_facture)))->setView([order=>$fields])->end()}
	{include file="copieur_contrat-produits-lignes.tpl.js"}
{else}
	{$q->reset()->where("id_copieur_facture",0)->setView([order=>$fields])->end()}
	{include file="copieur_contrat-produits-lignes.tpl.js"}
{/if}	