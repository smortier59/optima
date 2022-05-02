{$pager="{$current_class->table}LoyerKilometrageUpdate"}
{$parent_class=$current_class}
{$current_class=ATF::getClass("restitution_anticipee")}
{$q=ATF::_s(pager)->getAndPrepare($pager)}
{$fields=[
	"{$current_class->table}.loyer"
	, "{$current_class->table}.kilometrage"
	, "{$current_class->table}.echeance"
	, "{$current_class->table}.montant_ht"
]}

{if ATF::_r(id_devis)}
	{$id_affaire=ATF::devis()->select(ATF::_r(id_devis),"id_affaire")}
{elseif ATF::_r(id_commande)}
	{$id_affaire=ATF::commande()->select(ATF::_r(id_commande),"id_affaire")}
{/if}

{if $id_affaire}
	{$q->reset()->addCondition("id_affaire",classes::decryptId($id_affaire))->setView([order=>$fields])->end()}
{else}
	{$q->reset()->addCondition("id_affaire",0)->setView([order=>$fields])->end()}
{/if}
{include file="restitution_anticipee-lignes.tpl.js"}