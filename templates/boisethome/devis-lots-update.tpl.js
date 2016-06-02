{$pager="{$current_class->table}DevisLotsUpdate"}
{$parent_class=$current_class}
{$current_class=ATF::getClass("{$current_class->table}_lot")}
{$q=ATF::_s(pager)->getAndPrepare($pager)}
{$fields=[
	"{$current_class->table}.libelle"
	, "{$current_class->table}.optionnel"
	, "{$current_class->table}.payer_pourcentage"
	, "{$current_class->table}.id_devis"
	, "{$current_class->table}.id_devis_lot"
]}
{$q->reset()->setView([order=>$fields])->end()}
{include file="devis-lots.tpl.js" proxyUrl="devis_lot,selectFromDevis.ajax" baseParams=[id_devis=>ATF::_r(id_devis)]}