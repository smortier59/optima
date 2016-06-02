{$pager="formation_devis_ligne"}
{$parent_class="formation_devis"}
{$current_class=ATF::getClass("formation_devis_ligne")}
{$q=ATF::_s(pager)->getAndPrepare($pager)}

{$fields=["{$current_class->table}.date",
		"{$current_class->table}.date_deb_matin",
		"{$current_class->table}.date_fin_matin",
		"{$current_class->table}.date_deb_am",
		"{$current_class->table}.date_fin_am"
		]}

{if ATF::_r(id_formation_devis)}
	{$q->reset()->where("id_formation_devis",ATF::formation_devis()->decryptId(ATF::_r(id_formation_devis)))->setView([order=>$fields])->end()}
{else}
	{$q->reset()->where("id_formation_devis",0)->setView([order=>$fields])->end()}
{/if}

{include file="formation_devis-formation_devis_ligne.tpl.js"}
