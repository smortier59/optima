{$pager="{$current_class->table}LoyerProlongationUpdate"}
{$parent_class=$current_class}
{$current_class=ATF::getClass("loyer_prolongation")}
{$q=ATF::_s(pager)->getAndPrepare($pager)}
{$fields=[
	"{$current_class->table}.loyer"
	, "{$current_class->table}.duree"
	, "{$current_class->table}.assurance"
	, "{$current_class->table}.frais_de_gestion"
	, "{$current_class->table}.frequence_loyer"
]}
{if ATF::_r(id_prolongation)}
	{$calcul_prix='true'}
	{$q->reset()->addCondition("id_prolongation",classes::decryptId(ATF::_r(id_prolongation)))->setView([order=>$fields])->end()}
{else}
	{$q->reset()->addCondition("id_prolongation",0)->setView([order=>$fields])->end()}
{/if}
{include file="loyer-lignes.tpl.js"}