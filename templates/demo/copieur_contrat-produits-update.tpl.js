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
{else}
	{$q->reset()->where("id_copieur_contrat",0)->setView([order=>$fields])->end()}
{/if}
{include file="copieur_contrat-produits-lignes.tpl.js"}