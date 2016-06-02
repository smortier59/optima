{$pager="{$current_class->table}LoyerUpdate"}
{$parent_class=$current_class}
{$current_class=ATF::getClass("loyer")}
{$q=ATF::_s(pager)->getAndPrepare($pager)}
{$fields=[
	"{$current_class->table}.loyer"
	, "{$current_class->table}.duree"
	, "{$current_class->table}.assurance"
	, "{$current_class->table}.frais_de_gestion"
	, "{$current_class->table}.frequence_loyer"
]}
{if ATF::_r(id_devis)}
	{$id_affaire=ATF::devis()->select(ATF::_r(id_devis),"id_affaire")}
{elseif ATF::_r(id_commande)}
	{$id_affaire=ATF::commande()->select(ATF::_r(id_commande),"id_affaire")}
{/if}
{if $id_affaire}
	{$calcul_prix='true'}
	{$q->reset()->addCondition("id_affaire",classes::decryptId($id_affaire))->setView([order=>$fields])->end()}
{else}
	{$q->reset()->addCondition("id_affaire",0)->setView([order=>$fields])->end()}
{/if}
{include file="loyer-lignes.tpl.js"}